<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelAssociations extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function getLinks($id, $type)
    {
        $db = JFactory::getDbo();
        $table = $this->getTableName($type);
        $query = $db->getQuery(true)
            ->select('hash')
            ->from('#__gridbox_associations')
            ->where('item_id = '.$id)
            ->where('item_type = '.$db->quote($type));
        $db->setQuery($query);
        $hash = $db->loadResult();
        $query = $db->getQuery(true)
            ->select('a.item_id AS id, p.title, p.language')
            ->from('#__gridbox_associations AS a')
            ->where('a.hash = '.$db->quote($hash))
            ->where('a.item_type = '.$db->quote($type))
            ->leftJoin($table.' AS p ON p.id = a.item_id');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $response = [];
        foreach ($data as $item) {
            $response[$item->language] = $item;
        }

        return $response;
    }

    public function saveLinks($id, $type, $items)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('hash')
            ->from('#__gridbox_associations')
            ->where('item_id = '.$id)
            ->where('item_type = '.$db->quote($type));
        $db->setQuery($query);
        $hash = $db->loadResult();
        if ($hash) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_associations')
                ->where('hash = '.$db->quote($hash))
                ->where('item_type = '.$db->quote($type));
            $db->setQuery($query);
            $data = $db->loadObjectList();
        } else {
            $data = [];
        }
        $hash = md5(json_encode($items));
        $pages = [];
        foreach ($data as $item) {
            $query = $db->getQuery(true);
            if (!in_array($item->item_id, $items)) {
                $query->delete('#__gridbox_associations')
                    ->where('id = '.$item->id);
            } else {
                $pages[] = $item->item_id;
                $query->update('#__gridbox_associations')
                    ->set('hash = '.$db->quote($hash))
                    ->where('id = '.$item->id);
            }
            $db->setQuery($query)
                ->execute();
        }
        foreach ($items as $item) {
            if (in_array($item, $pages)) {
                continue;
            }
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_associations')
                ->where('item_id = '.$db->quote($item))
                ->where('item_type = '.$db->quote($type));
            $db->setQuery($query);
            $obj = $db->loadObject();
            if ($obj) {
                $obj->hash = $hash;
                $db->updateObject('#__gridbox_associations', $obj, 'id');
            } else {
                $obj = new stdClass();
                $obj->item_id = $item;
                $obj->item_type = $type;
                $obj->hash = $hash;
                $db->insertObject('#__gridbox_associations', $obj);
            }
        }
    }

    public function getTableName($type)
    {
        if ($type == 'page') {
            $table = '#__gridbox_pages';
        } else if ($type == 'app') {
            $table = '#__gridbox_app';
        } else if ($type == 'category') {
            $table = '#__gridbox_categories';
        } else if ($type == 'tag') {
            $table = '#__gridbox_tags';
        } else if ($type == 'system') {
            $table = '#__gridbox_system_pages';
        }

        return $table;
    }

    public function getPages()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $type = $input->get('type', '', 'string');
        $table = $this->getTableName($type);
        $query = $db->getQuery(true)
            ->select('b.id, b.title, b.published')
            ->from($table.' AS b');
        if ($type != 'category' && $type != 'tag') {
            $query->select('t.title as theme')
                ->leftJoin('#__template_styles AS t  ON ' .$db->quoteName('b.theme'). ' = ' . $db->quoteName('t.id'));
        }
        $query = $this->getCondition($query, $type);
        $orderCol = $input->cookie->get('pages_ordering', 'id', 'string');
        $orderDirn = $input->cookie->get('pages_direction', 'desc', 'string');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $limit = $input->cookie->get('pages_limit', 20, 'int');
        $start = $input->cookie->get('pages_start', 0, 'int');
        if ($start != 0) {
            $start = $start + $limit - 1;
        }
        $db->setQuery($query, $start, $limit);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function getPageCount()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $type = $input->get('type', '', 'string');
        $table = $this->getTableName($type);
        $query = $db->getQuery(true)
            ->select('count(b.id)')
            ->from($table.' AS b');
        $query = $this->getCondition($query, $type);
        $db->setQuery($query);
        $count = $db->loadResult();
        $limit = $input->cookie->get('pages_limit', 20, 'int');
        if ($limit == 0) {
            $count = 0;
        } else {
            $count = ceil($count / $limit) - 1;
        }
        
        return $count;
    }

    public function getCondition($query, $type)
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $app = $input->get('app', 0, 'int');
        $search = $input->cookie->get('pages_search', '', 'string');
        $category = $input->get('category', '', 'string');
        $language = $input->get('language', '', 'string');
        $pages_status = $input->cookie->get('pages_status', '', 'string');
        $type = $input->get('type', '', 'string');
        $query->where('b.language = '.$db->quote($language));
        if ($type != 'app' && $type != 'tag' && $type != 'system') {
            $query->where('b.app_id = '.$db->quote($app));
        } else if ($type == 'system') {
            $system = $input->get('system', '', 'string');
            $query->where('type = '.$db->quote($system));
        }
        if ($type == 'page') {
            $query->where('b.page_category <> '.$db->quote('trashed'));
        }
        if (!empty($search)) {
            $query->where('b.title LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        }
        if (!empty($category)) {
            $query->where('b.page_category = '.$db->quote($db->escape($category, true)));
        }
        if (!empty($pages_status)) {
            $query->where('b.published = '.$db->quote($db->escape($pages_status, true)));
        }

        return $query;
    }

    public function getParams()
    {
        $input = JFactory::getApplication()->input;
        $obj = new stdClass();
        $obj->language = $input->get('language', '', 'string');
        $obj->type = $input->get('type', '', 'string');

        return $obj;
    }

    public function getApps()
    {
        $params = $this->getParams();
        if ($params->type == 'app' || $params->type == 'tag' || $params->type == 'system') {
            return [];
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $key => $item) {
            if ($params->type == 'category') {
                $item->categories = [];
            } else {
                $item->categories = $this->getCategories($item->id);
            }
        }

        return $items;
    }

    protected function getCategories($id, $parent = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__gridbox_categories')
            ->where('app_id = '.$id)
            ->where('parent = '.$parent)
            ->order('order_list ASC');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        foreach ($categories as $category) {
            $category->child = $this->getCategories($id, $category->id);
        }

        return $categories;
    }
}