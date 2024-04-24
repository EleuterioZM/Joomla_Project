<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelBaforms extends JModelItem
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
        $id = gridboxHelper::getComBa('com_baforms');
        if (!$id) {
            return array();
        }
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $baforms_search = $input->cookie->get('baforms_search', '', 'string');
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('`#__baforms_forms`')
            ->where('`published` = 1');
        if (!empty($baforms_search)) {
            $query->where('title LIKE '.$db->quote('%'.$db->escape($baforms_search, true).'%'));
        }
        $orderCol = $input->cookie->get('baforms_ordering', 'id', 'string');
        $orderDirn = $input->cookie->get('baforms_direction', 'desc', 'string');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }
}