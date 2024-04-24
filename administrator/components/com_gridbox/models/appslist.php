<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelAppslist extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'state'
            );
        }
        parent::__construct($config);
    }

    public function renameApp($id, $title, $type)
    {
        $db = JFactory::getDbo();
        $table = $type != 'group' ? '#__gridbox_app' : '#__gridbox_apps_groups';
        $query = $db->getQuery(true)
            ->update($table)
            ->set('title = '.$db->quote($title))
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function addSystemApp($type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_app')
            ->where('type = '.$db->quote('system_apps'))
            ->where('title = '.$db->quote($type));
        $db->setQuery($query);
        $count = $db->loadResult();
        $flag = $count == 0;
        if ($flag) {
            $obj = new stdClass();
            $obj->title = $type;
            $obj->type = 'system_apps';
            $db->insertObject('#__gridbox_app', $obj);
        }
        var_dump($flag);exit;
    }

    public function getSystemApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, type')
            ->from('#__gridbox_app')
            ->where('type = '.$db->quote('system_apps'))
            ->order('id ASC');
        $db->setQuery($query);
        $system = $db->loadObjectList();

        return $system;
    }

    public function refreshApps()
    {
        $items = $this->getItems();
        ob_start();
        include JPATH_COMPONENT.'/views/layouts/gridbox-app-item.php';
        $out = ob_get_contents();
        ob_end_clean();

        return $out;
    }

    public function refreshSidebar()
    {
        $items = $this->getItems();
        ob_start();
        include JPATH_COMPONENT.'/views/layouts/apps-list-context-menu.php';
        $out = ob_get_contents();
        ob_end_clean();

        return $out;
    }

    public function setAppsGroup($ids, $parent, $type)
    {
        $db = JFactory::getDbo();
        if ($type != 'group') {
            $item_id = $parent;
            $obj = new stdClass();
            $obj->title = JText::_('GROUP');
            $db->insertObject('#__gridbox_apps_groups', $obj);
            $parent = $db->insertid();
            $query = $db->getQuery(true)
                ->select('order_ind')
                ->from('#__gridbox_apps_order_map')
                ->where('type = '.$db->quote('app'))
                ->where('item_id = '.$item_id);
            $db->setQuery($query);
            $order_ind = $db->loadResult();
            $obj = new stdClass();
            $obj->item_id = $parent;
            $obj->order_ind = $order_ind;
            $obj->type = 'group';
            $db->insertObject('#__gridbox_apps_order_map', $obj);
        }
        foreach ($ids as $i => $id) {
            $query = $db->getQuery(true)
                ->update('#__gridbox_apps_order_map')
                ->set('order_ind = '.($i + 1))
                ->set('parent_id = '.$parent)
                ->where('type = '.$db->quote('app'))
                ->where('item_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }

        return $this->refreshApps();
    }

    public function ungroup($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('MAX(order_ind)')
            ->from('#__gridbox_apps_order_map')
            ->where('parent_id = 0');
        $db->setQuery($query);
        $max = $db->loadResult();
        $max = $max ? ++$max : 1;
        $query = $db->getQuery(true)
            ->update('#__gridbox_apps_order_map')
            ->set('order_ind = '.$max)
            ->set('parent_id = 0')
            ->where('type = '.$db->quote('app'))
            ->where('item_id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function orderApps($ids, $orders, $types, $parent_id)
    {
        $db = JFactory::getDbo();
        foreach ($ids as $i => $id) {
            $type = $types[$i] == 'group' ? 'group' : 'app';
            $query = $db->getQuery(true)
                ->update('#__gridbox_apps_order_map')
                ->set('order_ind = '.$orders[$i])
                ->where('type ='.$db->quote($type))
                ->where('parent_id = '.$parent_id)
                ->where('item_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function removeGroup($db, $obj)
    {
        $query = $db->getQuery(true)
            ->delete('#__gridbox_apps_groups')
            ->where('id = '.$obj->item_id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_apps_order_map')
            ->where('type = '.$db->quote('group'))
            ->where('item_id = '.$obj->item_id);
        $db->setQuery($query)
            ->execute();
    }

    public function getGroupApps($id)
    {
        $items = gridboxHelper::getGridboxAppsList($id);
        ob_start();
        include JPATH_COMPONENT.'/views/layouts/gridbox-group-app-items.php';
        $out = ob_get_contents();
        ob_end_clean();

        return $out;
    }

    public function getItems()
    {
        $db = JFactory::getDbo();
        $map = gridboxHelper::getAppsMap();
        $items = [];
        foreach ($map as $obj) {
            $item = gridboxHelper::getAppObject($obj);
            if ($obj->type == 'group') {
                $childs = gridboxHelper::getAppsMap($obj->item_id);
                if (empty($childs)) {
                    $this->removeGroup($db, $obj);
                    continue;
                }
                $item->apps = [];
                foreach ($childs as $child) {
                    $item->apps[] = gridboxHelper::getAppObject($child);
                }
            }
            if ($item) {
                $item->order_ind = $obj->order_ind;
                $items[] = $item;
            }
        }

        return $items;
    }

    public function setFilters()
    {
        $this::populateState();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        parent::populateState('id', 'desc');
    }
    
}