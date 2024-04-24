<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxMenu
{
    public $root;
    public $db;
    public $cache;

    public function __construct()
    {
        $this->db = JFactory::getDbo();
        $this->cache = array();
    }

    public function getMenu($id, $items = false)
    {
        $query = $this->db->getQuery(true)
            ->select('params')
            ->from('#__modules')
            ->where('`id` = '.$id);
        $this->db->setQuery($query);
        $menu = $this->db->loadResult();
        $menu = json_decode($menu);
        $obj = new stdClass();
        $obj->menutype = $menu->menutype;
        if ($items) {
            $obj->items = $this->getMenuItems($menu->menutype);
        }
        
        return $obj;
    }

    public function getMenuItems($menutype)
    {
        $query = $this->db->getQuery(true)
            ->select('id, title')
            ->from('#__menu')
            ->where('`menutype` = '.$this->db->quote($menutype));
        $this->db->setQuery($query);
        $items = $this->db->loadObjectList();
        
        return $items;
    }

    public function getNewMenuAlias($type, $orig)
    {
        $alias = gridboxHelper::stringURLSafe(trim($type));
        $query = $this->db->getQuery(true);
        $query->select('COUNT(id)')
            ->from('#__menu')
            ->where('`alias` = '.$this->db->quote($alias));
        $this->db->setQuery($query);
        $n = $this->db->loadResult();
        if (!empty($n)) {
            $alias = empty($orig) ? gridboxHelper::increment($alias) : gridboxHelper::increment($orig);
            $orig = $alias;
            $alias = gridboxHelper::stringURLSafe($alias);
            if (empty($alias)) {
                $alias = $orig;
                $alias = gridboxHelper::replace($alias);
                $alias = JFilterOutput::stringURLSafe($alias);
            }
            if (empty($alias)) {
                $alias = date('Y-m-d-H-i-s');
            }
            $alias = $this->getNewMenuAlias($alias, $orig);
        }

        return $alias;
    }

    public function setMenuItem($parent, $title, $menutype, $link)
    {
        $query = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('`#__extensions`')
            ->where('`element` = '.$this->db->quote('com_gridbox'))
            ->where('`type` = '.$this->db->quote('component'));
        $this->db->setQuery($query);
        $component = $this->db->loadResult();
        $query = $this->db->getQuery(true)
            ->select('MAX(rgt)')
            ->from('#__menu');
        $this->db->setQuery($query);
        $rgt = $this->db->loadResult();
        $obj = new stdClass();
        $obj->lft = ++$rgt;
        $obj->rgt = ++$rgt;
        $obj->title = $title;
        $obj->menutype = $menutype;
        $obj->alias = $this->getNewMenuAlias($obj->title, '');
        $obj->path = '';
        $obj->level = 1;
        $obj->link = $link;
        $obj->type = 'component';
        $obj->published = 1;
        $obj->parent_id = $parent;
        $obj->component_id = $component;
        $obj->access = 1;
        $obj->language = '*';
        $str = '{"show_title":"","link_titles":"","show_intro":"","info_block_position":"","info_block_show_title":"",'.
            '"show_category":"","link_category":"","show_parent_category":"","link_parent_category":"",'.
            '"show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"",'.
            '"show_item_navigation":"","show_vote":"","show_icons":"","show_print_icon":"","show_email_icon":"",'.
            '"show_hits":"","show_tags":"","show_noauth":"","urls_position":"","menu-anchor_title":"","menu-anchor_css":"",'.
            '"menu_image":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"1","page_heading":"",'.
            '"pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}';
        $obj->params = $str;
        $obj->img = '';
        $this->db->insertObject('#__menu', $obj);
        $this->rebuild();
    }

    public function getRootId()
    {
        if ((int)$this->root > 0) {
            return $this->root;
        }
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where('parent_id = 0');
        $id = $this->db->setQuery($query)
            ->loadResult();
        $this->root = $id;

        return $this->root;
    }

    public function rebuild($parentId = null, $lft = 0, $level = 0, $path = '')
    {
        if ($parentId === null) {
            $parentId = $this->getRootId();
            if ($parentId === false) {
                return false;
            }
        }
        $query = $this->db->getQuery(true);
        if (!isset($this->cache['rebuild'])) {
            $query->clear()
                ->select('id, alias')
                ->from('#__menu')
                ->where('parent_id = %d')
                ->order('parent_id, lft');
            $this->cache['rebuild'] = (string)$query;
        }
        $this->db->setQuery(sprintf($this->cache['rebuild'], (int)$parentId));
        $children = $this->db->loadObjectList();
        $rgt = $lft + 1;
        foreach ($children as $node) {
            $rgt = $this->rebuild($node->id, $rgt, $level + 1, $path.(empty($path) ? '' : '/').$node->alias);
            if ($rgt === false) {
                return false;
            }
        }
        $query->clear()
            ->update('#__menu')
            ->set('lft = '.(int)$lft)
            ->set('rgt = '.(int)$rgt)
            ->set('level = '.(int)$level)
            ->set('path = '.$this->db->quote($path))
            ->where('id = '.(int)$parentId);
        $this->db->setQuery($query)->execute();

        return $rgt + 1;
    }
}