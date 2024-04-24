<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die; 

jimport('joomla.application.component.modeladmin');

class gridboxModelCategory extends JModelList
{
    public function getTable($type = 'Categories', $prefix = 'gridboxTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function orderCategories()
    {
        $input = JFactory::getApplication()->input;
        $string = $input->get('data', '', 'raw');
        $data = json_decode($string);
        foreach ($data as $value) {
            $db = JFactory::getDbo();
            $db->updateObject('#__gridbox_categories', $value, 'id');
        }
    }

    public function pageMoveTo($category, $id, $app_id)
    {
        $obj = new stdClass();
        $obj->page_category = $category;
        $obj->app_id = $app_id;
        $obj->order_list = 0;
        $obj->root_order_list = 0;
        $obj->id = $id;
        gridboxHelper::movePageFields($id, $app_id);
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_pages', $obj, 'id');
        $this->updateAssets($id, 'page', $obj->page_category, 'category');
        $query = $db->getQuery(true)
            ->delete('#__gridbox_category_page_map')
            ->where('page_id = '.$id)
            ->where('category_id = '.$category);
        $db->setQuery($query)
            ->execute();
        gridboxHelper::triggerEvent('onGidboxPageAfterSave', [$id], 'finder');
    }

    public function updateAssets($id, $type, $parent, $parentType)
    {
        $assets = new gridboxAssetsHelper($id, $type);
        $parentAssets = new gridboxAssetsHelper($parent, $parentType);
        $permission = $parentAssets->getPermission();
        $assets->storeAssets('com_gridbox.'.$type.'.'.$id, '', $permission->id);
    }

    public function updateCategory()
    {
        $obj = new stdClass();
        $input = JFactory::getApplication()->input;
        $obj->title = $input->get('category_title', '', 'string');
        $obj->alias = $input->get('category_alias', '', 'string');
        $obj->id = $input->get('category-id', '', 'string');
        $db = JFactory::getDbo();
        if (empty($obj->alias)) {
            $obj->alias = $obj->title;
        }
        $obj->published = $input->get('category_publish', 0, 'int');
        if (!empty($obj->published)) {
            $parent = $input->get('category_parent', 0, 'int');
            $query = $db->getQuery(true)
                ->select('published')
                ->from('#__gridbox_categories')
                ->where('id = '.$parent);
            $db->setQuery($query);
            $result = $db->loadResult();
            if ($result == 0 && $parent != 0) {
                $obj->published = 0;
            }
        }
        $obj->alias = gridboxHelper::getAlias($obj->alias, '#__gridbox_categories', $obj->id);
        $obj->access = $input->get('category_access', '', 'string');
        $obj->robots = $input->get('category_robots', '', 'string');
        $obj->language = $input->get('category_language', '', 'string');
        $obj->description = $input->get('category_description', '', 'raw');
        $obj->image = $input->get('category_intro_image', '', 'string');
        $obj->meta_title = $input->get('category_meta_title', '', 'string');
        $obj->meta_description = $input->get('category_meta_description', '', 'raw');
        $obj->meta_keywords = $input->get('category_meta_keywords', '', 'string');
        $obj->share_image = $input->get('category_share_image', '', 'string');
        $obj->share_title = $input->get('category_share_title', '', 'string');
        $obj->share_description = $input->get('category_share_description', '', 'string');
        $obj->sitemap_override = $input->get('category_sitemap_override', 0, 'int');
        $obj->sitemap_include = $input->get('category_sitemap_include', 0, 'int');
        $obj->changefreq = $input->get('category_changefreq', 'monthly', 'string');
        $obj->priority = $input->get('category_priority', '0.5', 'string');
        $obj->schema_markup = $input->get('category_schema_markup', '', 'raw');
        $db->updateObject('#__gridbox_categories', $obj, 'id');
        $this->checkChilds($obj->id, $obj->published);
    }

    public function checkChilds($id, $published = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_categories')
            ->where('`parent` = '.$id);
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach ($list as $obj) {
            $obj->published = $published;
            $db->updateObject('#__gridbox_categories', $obj, 'id');
            $this->checkChilds($obj->id, $published);
        }
    }

    public function removeCategory()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('context-item', '', 'string');
        $obj = json_decode($data);
        $pages = $this->getPages($obj);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_category_page_map')
            ->where('category_id = '.$obj->id);
        $db->setQuery($query)
            ->execute();
        foreach ($pages as $page) {
            $obj = new stdClass();
            $obj->id = $page;
            $obj->published = 0;
            $obj->page_category = 'trashed';
            $db->updateObject('#__gridbox_pages', $obj, 'id');
        }
        gridboxHelper::triggerEvent('onGidboxPagesAfterDelete', [$pages], 'finder');
    }

    public function getPages($item, $array = array())
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_pages')
            ->where('`page_category` = '.$item->id);
        $db->setQuery($query);
        $values = $db->loadColumn();
        foreach ($values as $value) {
            $array[] = $value;
        }
        if (!empty($item->child)) {
            foreach ($item->child as $child) {
                $array = $this->getPages($child, $array);
            }
        }
        $query = $db->getQuery(true);
        $query->delete('#__gridbox_categories')
            ->where('`id` = '. $item->id);
        $db->setQuery($query)
            ->execute();

        return $array;
    }

    public function checkDeletePermissions($item)
    {
        $flag = gridboxHelper::assetsCheckPermission($item->id, 'category', 'core.delete', '');
        foreach ($item->child as $child) {
            if (!$flag) {
                break;
            }
            $flag = $this->checkDeletePermissions($child);
        }

        return $flag;
    }

    protected function getListQuery()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title, id')
            ->from('#__gridbox_categories')
            ->where('published = 1')
            ->where('app_id = '.$id)
            ->order($db->escape('order_list DESC'));
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
        }

        return $query;
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        parent::populateState('id', 'desc');
    }

    public function getItems()
    {
        $store = $this->getStoreId();
        $app = JFactory::getApplication();
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }
        $query = $this->_getListQuery();
        try {
            $items = $this->_getList($query, 0, 0);
            $search = $this->getState('filter.search');
            $title = JText::_('ROOT');
            if (empty($search) || strpos($title, $search) !== false) {
                $obj = new stdClass();
                $obj->title = $title;
                $obj->id = 0;
                $items[] = $obj;
                $items = array_reverse($items);
            }

        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        $this->cache[$store] = $items;

        return $this->cache[$store];
    }

    public function moveTo()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('category_id', '', 'string');
        $obj = json_decode($data);
        $obj->parent = $obj->id;
        $obj->id = $input->get('context-item', 0, 'int');
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_categories', $obj, 'id');
        $parent = $obj->parent == 0 ? $obj->app_id : $obj->parent;
        $parentType = $obj->parent == 0 ? 'app' : 'category';
        $this->updateAssets($obj->id, 'category', $parent, $parentType);
        $this->moveChilds($obj->id, $obj->app_id);
    }

    public function moveChilds($parent, $app)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_pages')
            ->where('page_category = '.$db->quote($parent))
            ->set('app_id = '.$app);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_categories')
            ->where('parent = '.$parent);
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach ($list as $obj) {
            $obj->app_id = $app;
            $db->updateObject('#__gridbox_categories', $obj, 'id');
            $this->moveChilds($obj->id, $app);
        }
    }

    public function createCat($title, $app_id, $parent = 0, $order = 0)
    {
        $alias = gridboxHelper::getAlias($title, '#__gridbox_categories');
        $table = $this->getTable();
        $table->bind(array('title' => $title, 'alias' => $alias, 'access' => 1, 'app_id' => $app_id,
            'parent' => $parent, 'order_list' => $order));
        $table->store();

        return $table->id;
    }

    public function duplicate($id, $gridboxModel)
    {
        $table = $this->getTable();
        $table->load($id);
        $table->id = 0;
        $table->title = $this->getNewTitle($table->title);
        $table->alias = $this->getNewAlias($table->alias);
        $table->alias = gridboxHelper::getAlias($table->alias, '#__gridbox_categories');
        $table->store();
        $categories = $gridboxModel->getCategories($table->app_id, $id);
        $gridboxModel->duplicateCategories($categories, $table->app_id, $table->app_id, $table->id);

        return $table->id;
    }

    protected function getNewTitle($title)
    {
        $table = $this->getTable();
        while ($table->load(array('title' => $title))) {
            $title = gridboxHelper::increment($title);
        }

        return $title;
    }

    protected function getNewAlias($alias)
    {
        $table = $this->getTable();
        while ($table->load(array('alias' => $alias))) {
            $alias = gridboxHelper::increment($alias);
        }

        return $alias;
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option . '.gridbox', 'gridbox', array('control' => 'jform', 'load_data' => $loadData)
        );
        
        if (empty($form)) {
            return false;
        }
 
        return $form;
    }
}