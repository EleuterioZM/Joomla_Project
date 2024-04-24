<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelModules extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function getItems()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $search = $input->cookie->get('modules_search', '', 'string');
        $modules_type = $input->cookie->get('modules_type', '', 'string');
        $modules_position = $input->cookie->get('modules_position', '', 'string');
        $query = $db->getQuery(true);
        $query->select('id, title, position, module')
            ->from('`#__modules`')
            ->where('`published` = 1')
            ->where('`client_id` = 0');
        if (!empty($search)) {
            $query->where('title LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        }
        if (!empty($modules_type)) {
            $query->where('module = '.$db->quote($db->escape($modules_type, true)));
        }
        if (!empty($modules_position)) {
            $query->where('position = '.$db->quote($db->escape($modules_position, true)));
        }
        $orderCol = $input->cookie->get('modules_ordering', 'id', 'string');
        $orderDirn = $input->cookie->get('modules_direction', 'desc', 'string');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function getFilters()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('position, module')
            ->from('`#__modules`')
            ->where('`published` = 1')
            ->where('`client_id` = 0');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }
}