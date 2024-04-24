<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelSelectLink extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function getGridbox()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, type, id')
            ->where('type <> '.$db->quote('system_apps'))
            ->from('#__gridbox_app');
        $db->setQuery($query);
        $apps = $db->loadObjectList();
        $obj = new stdClass();
        $obj->id = 0;
        $obj->title = JText::_('PAGES');
        $obj->type = '';
        $apps[] = $obj;
        usort($apps, function($a, $b){
            return ($a->id < $b->id) ? -1 : 1;
        });
        foreach ($apps as $app) {
            if (empty($app->type) || $app->type == 'single') {
                $query = $db->getQuery(true)
                    ->select('id, title')
                    ->from('#__gridbox_pages')
                    ->where('page_category <> '.$db->quote('trashed'))
                    ->where('app_id = '.$app->id)
                    ->where('published = 1');
                $db->setQuery($query);
                $pages = $db->loadObjectList();
                $app->pages = $this->setPagesLink($pages);
            } else {
                $app->link = 'index.php?option=com_gridbox&view=blog&app='.$app->id.'&id=0';
                $app->childs = $this->getCategories($app->id, 0);
            }
        }
        
        return $apps;
    }

    public function getCategories($id, $parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title')
            ->from('#__gridbox_categories')
            ->where('published = 1')
            ->where('app_id = '.$id)
            ->where('parent = '.$parent);
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        foreach ($categories as $category) {
            $category->childs = $this->getCategories($id, $category->id);
            $category->link = 'index.php?option=com_gridbox&view=blog&app='.$id.'&id='.$category->id;
            $query = $db->getQuery(true)
                ->select('id, title, page_category, app_id')
                ->from('#__gridbox_pages')
                ->where('page_category <> '.$db->quote('trashed'))
                ->where('page_category = '.$category->id)
                ->where('published = 1');
            $db->setQuery($query);
            $pages = $db->loadObjectList();
            $category->pages = $this->setPagesLink($pages);
        }

        return $categories;
    }

    public function setPagesLink($pages)
    {
        foreach ($pages as $page) {
            if (isset($page->page_category)) {
                $page->link = 'index.php?option=com_gridbox&view=page&blog='.$page->app_id;
                $page->link .= '&category='.$page->page_category.'&id='.$page->id;
            } else {
                $page->link = 'index.php?option=com_gridbox&view=page&id='.$page->id;
            }
        }

        return $pages;
    }

    public function getMenus()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, menutype')
            ->from('#__menu_types')
            ->order('title ASC');
        $db->setQuery($query);
        $menus = $db->loadObjectList();
        foreach ($menus as $key => $menu) {
            $query = $db->getQuery(true)
                ->select('title, link, id')
                ->from('#__menu')
                ->where('published = 1')
                ->where('menutype = '.$db->quote($menu->menutype))
                ->where('parent_id = 1');
            $db->setQuery($query);
            $menu->childs = $db->loadObjectList();
            foreach ($menu->childs as $child) {
                $this->getChilds($child);
            }
        }

        return $menus;
    }

    public function getChilds($obj)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, link, id')
            ->from('#__menu')
            ->where('published = 1')
            ->where('parent_id = '.$obj->id);
        $db->setQuery($query);
        $obj->childs = $db->loadObjectList();
        foreach ($obj->childs as $key => $child) {
            $this->getChilds($child);
        }

        return $obj;
    }
}