<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelPages extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function getPages()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $query = $db->getQuery(true)
            ->select('b.id, b.title, b.published')
            ->from('`#__gridbox_pages` AS b')
            ->select('t.title as theme')
            ->leftJoin('`#__template_styles` AS t  ON ' .$db->quoteName('b.theme'). ' = ' . $db->quoteName('t.id'));
        $query = $this->getCondition($query);
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
        $type = $input->get('type', '', 'string');
        if ($type == 'system') {
            foreach ($items as $key => $item) {
                $item->published = 1;
            }
        }
        
        return $items;
    }

    public function getPageCount()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $query = $db->getQuery(true)
            ->select('count(b.id)')
            ->from('`#__gridbox_pages` AS b');
        $query = $this->getCondition($query);
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

    public function getCondition($query)
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $app = $input->get('app', 0, 'int');
        $search = $input->cookie->get('pages_search', '', 'string');
        $category = $input->get('category', '', 'string');
        $pages_status = $input->cookie->get('pages_status', '', 'string');
        $type = $input->get('type', '', 'string');
        $query->where('b.app_id = '.$db->quote($app))
            ->where('b.page_category <> '.$db->quote('trashed'));
        if (!empty($search)) {
            $query->where('b.title LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        }
        if (!empty($category)) {
            $query->where('b.page_category = '.$db->quote($db->escape($category, true)));
        }
        if (!empty($pages_status)) {
            $query->where('b.published = '.$db->quote($db->escape($pages_status, true)));
        }
        if ($type == 'system') {
            $query = $db->getQuery(true)
                ->select('b.id, b.title')
                ->from('`#__gridbox_system_pages` AS b')
                ->select('t.title as theme')
                ->leftJoin('`#__template_styles` AS t  ON ' .$db->quoteName('b.theme'). ' = ' . $db->quoteName('t.id'));
        }

        return $query;
    }

    public function getApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $key => $item) {
            $item->categories = $this->getCategories($item->id);
        }

        return $items;
    }

    protected function getCategories($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__gridbox_categories')
            ->where('`app_id` = '.$id)
            ->where('`parent` = 0')
            ->order('order_list ASC');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        foreach ($categories as $value) {
            $value->child = $this->getAllChild($value);
        }

        return $categories;
    }

    protected function getAllChild($parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__gridbox_categories')
            ->where('`parent` = '.$parent->id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $key => $value) {
            $value->child = $this->getAllChild($value);
        }

        return $items;
    }
}