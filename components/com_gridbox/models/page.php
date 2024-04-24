<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

class gridboxModelPage extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getHits()
    {
        $input = JFactory::getApplication()->input;
        $db = $this->getDbo();
        $table = $this->getTable();
        $id = $input->get('id', 0, 'int');
        $table->load($id);
        $table->hit($id);
    }

    public function getPageLayout()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('b.id')
            ->from('`#__gridbox_pages` AS b')
            ->where('b.id = ' .$id)
            ->select('a.page_layout, a.type')
            ->leftJoin('`#__gridbox_app` AS a'
                . ' ON '
                . $db->quoteName('b.app_id')
                . ' = ' 
                . $db->quoteName('a.id')
            );
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->page_layout)) {
        $item->page_layout = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.html');
        }
        
        return $item->page_layout;
    }

    public function getPageItems()
    {
        
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('b.id')
            ->from('`#__gridbox_pages` AS b')
            ->where('b.id = ' .$id)
            ->select('a.page_items, a.type')
            ->leftJoin('`#__gridbox_app` AS a'
                . ' ON '
                . $db->quoteName('b.app_id')
                . ' = ' 
                . $db->quoteName('a.id')
            );
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->page_items)) {
            $item->page_items = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.json');
        }
        
        return $item->page_items;
    }
    
    public function getItem($id = null)
    {
        $input = JFactory::getApplication()->input;
        $db = $this->getDbo();
        $table = $this->getTable();
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true);
        $query->select('p.*')
            ->from('`#__gridbox_pages` as p')
            ->where('p.id = ' .$id)
            ->where('p.published = 1')
            ->where('p.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')')
            ->select('a.type as app_type')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->select('c.title AS category_title')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id');
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!empty($item->app_type) && $item->app_type != 'single') {
            $query = $db->getQuery(true)
                ->select('au.title, au.avatar, au.id')
                ->from('`#__gridbox_authors_map` AS au_m')
                ->where('au_m.page_id = '.$id)
                ->leftJoin('`#__gridbox_authors` AS au ON au.id = au_m.author_id')
                ->where('au.published = 1')
                ->order('au_m.id ASC');
            $db->setQuery($query);
            $item->authors = $db->loadObjectList();
        }
        
        return $item;
    }

    public function getGlobalItems()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('item')
            ->from('`#__gridbox_library`')
            ->where('`global_item` <> ' .$db->quote(''));
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }
}
