<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelBagallery extends JModelItem
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
        $id = gridboxHelper::getComBa('com_bagallery');
        if (!$id) {
            return array();
        }
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $search = $input->cookie->get('bagallery_search', '', 'string');
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('`#__bagallery_galleries`')
            ->where('`published` = 1');
        if (!empty($search)) {
            $query->where('title LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        }
        $orderCol = $input->cookie->get('bagallery_ordering', 'id', 'string');
        $orderDirn = $input->cookie->get('bagallery_direction', 'desc', 'string');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }
}