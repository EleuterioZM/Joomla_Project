<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxAssetsHelper
{
    private $db;
    private $name;
    private $user;
    private $permission;
    private $id;
    
    public function __construct($id, $type)
    {
        $this->db = JFactory::getDbo();
        $this->id = $id;
        $this->name = 'com_gridbox.'.$type.'.'.$id;
        $this->user = JFactory::getUser();
        $this->permission = $this->getPermissions($id, $type);
    }

    private function getTable($type)
    {
        if ($type == 'page') {
            $table = '#__gridbox_pages';
        } else if ($type == 'category') {
            $table = '#__gridbox_categories';
        } else {
            $table = '#__gridbox_app';
        }

        return $table;
    }

    private function getSelect($type)
    {
        $select = 'title, id';
        if ($type == 'page') {
            $select .= ', page_category, app_id';
        } else if ($type == 'category') {
            $select .= ', app_id, parent';
        }

        return $select;
    }

    public function storeAssets($name, $title = '', $parent = 0)
    {
        $asset = JTable::getInstance('asset');
        $load = $asset->loadByName($name);
        if (!$load) {
            $asset->name = $name;
            $asset->title = mb_substr($title, 0, 99);
        }
        $asset->parent_id = $parent;
        $asset->setLocation($parent, 'last-child');
        $asset->check();
        $asset->store();
    }

    private function createPermissionsTree($id, $type)
    {
        $table = $this->getTable($type);
        $select = $this->getSelect($type);
        $query = $this->db->getQuery(true)
            ->select($select)
            ->from($this->db->quoteName($table))
            ->where('id = '.$id);
        $this->db->setQuery($query);
        $obj = $this->db->loadObject();
        if ($type == 'page' && !empty($obj->page_category) && $obj->page_category != 'trashed') {
            $parent = $this->getPermissions($obj->page_category, 'category');
        } else if ($type == 'category' && $obj && $obj->parent == 0) {
            $parent = $this->getPermissions($obj->app_id, 'app');
        } else if ($type == 'category' && $obj) {
            $parent = $this->getPermissions($obj->parent, 'category');
        } else {
            $parent = $this->loadByName('com_gridbox');
        }
        $this->storeAssets('com_gridbox.'.$type.'.'.$id, $obj->title, $parent->id);
        $permission = $this->loadByName('com_gridbox.'.$type.'.'.$id);

        return $permission;
    }

    private function loadByName($name)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from('#__assets')
            ->where('name = '.$this->db->quote($name));
        $this->db->setQuery($query);
        $permission = $this->db->loadObject();

        return $permission;
    }

    public function updateRules($rules)
    {
        $obj = new stdClass();
        $obj->id = $this->permission->id;
        $obj->rules = $rules;
        $this->db->updateObject('#__assets', $obj, 'id');
    }

    public function getPermissions($id, $type)
    {
        $permission = $this->loadByName('com_gridbox.'.$type.'.'.$id);
        if (!$permission) {
            $permission = $this->createPermissionsTree($id, $type);
        }

        return $permission;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function checkPermission($action, $name = '')
    {
        if (empty($name)) {
            $name = $this->permission->name;
        }
        $flag = $this->user->authorise($action, $name);

        return $flag;
    }

    public function checkEditOwn($category)
    {
        $flag = $this->checkPermission('core.edit.own', 'com_gridbox.category.'.$category);
        if ($flag) {
            $query = $this->db->getQuery(true)
                ->select('COUNT(a.id)')
                ->from('#__gridbox_authors AS a')
                ->where('am.page_id = '.$this->id)
                ->where('a.user_id = '.$this->user->id)
                ->leftJoin('`#__gridbox_authors_map` AS am ON '.$this->db->quoteName('am.author_id').' = '.$this->db->quoteName('a.id'));
            $this->db->setQuery($query);
            $count = $this->db->loadResult();
            $flag = $count > 0;
        }

        return $flag;
    }

    public function getUserGroups()
    {
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from($this->db->quoteName('#__usergroups'))
            ->order('lft ASC');
        $this->db->setQuery($query);
        $groups = $this->db->loadObjectList();

        return $groups;
    }

    public function getGroupPermissions($group, $actions)
    {
        $obj = new stdClass();
        foreach ($actions as $action) {
            $obj->{$action} = $this->checkGroup($group, $action);
        }

        return $obj;
    }

    public function checkGroup($group, $action)
    {
        $obj = new stdClass();
        $rule = JAccess::checkGroup((int)$group, $action, $this->permission->name);
        $isSuperUserGroup = JAccess::checkGroup((int)$group, 'core.admin');
        $obj->status = $isSuperUserGroup || $rule == true ? 'allowed' : 'not-allowed';
        $obj->text = $isSuperUserGroup || $rule == true ? JText::_('JLIB_RULES_ALLOWED') : JText::_('JLIB_RULES_NOT_ALLOWED');
        $obj->icon = $isSuperUserGroup || $rule == true ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-close-circle';
        
        return $obj;
    }
}