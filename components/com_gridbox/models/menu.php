<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelMenu extends JModelItem
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
        $query = $db->getQuery(true);
        $query->select('id, title, position, module')
            ->from('`#__modules`')
            ->where('`published` = 1')
            ->where('`client_id` = 0')
            ->where('module = '.$db->quote('mod_menu'));
        $input = JFactory::getApplication()->input;
        $search = $input->cookie->get('menu_search', '', 'string');
        if (!empty($search)) {
            $query->where('title LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        }
        $orderCol = $input->cookie->get('menu_ordering', 'id', 'string');
        $orderDirn = $input->cookie->get('menu_direction', 'desc', 'string');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }
}