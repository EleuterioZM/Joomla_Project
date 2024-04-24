<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;

class gridboxModelSingle extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'published', 'theme', 'state', 'page_category', 'created', 'hits', 'order_list'
            );
        }
        parent::__construct($config);
    }

    public function setGridboxFilters()
    {
        $app = JFactory::getApplication();
        $ordering = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', null);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', null);
        gridboxHelper::setGridboxFilters($ordering, $direction, $this->context);
    }

    public function getGridboxFilters()
    {
        $array = gridboxHelper::getGridboxFilters($this->context);
        if (!empty($array)) {
            foreach ($array as $obj) {
                $name = str_replace($this->context.'.', '', $obj->name);
                $this->setState($name, $obj->value);
            }
        }
    }

    public function setFilters()
    {
        $this->setGridboxFilters();
        $this::populateState();
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('id', 0, 'int');
        $this->checkThemes();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_pages')
            ->where('`app_id` = '.$id)
            ->where('`order_list` = 0')
            ->where('`page_category` <> '.$db->Quote('trashed'))
            ->where('(published IN (0, 1))');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (!empty($items)) {
            $query = $db->getQuery(true)
                ->select('MAX(order_list) as max, COUNT(id) as count')
                ->from('#__gridbox_pages')
                ->where('`app_id` = '.$id)
                ->where('`order_list` <> 0')
                ->where('`page_category` <>'.$db->Quote('trashed'))
                ->where('(published IN (0, 1))');
            $db->setQuery($query);
            $obj = $db->loadObject();
            if ($obj->count == 0) {
                $obj->max = 0;
            }
            foreach ($items as $value) {
                $value->order_list = ++$obj->max;
                $db->updateObject('#__gridbox_pages', $value, 'id');
            }
        }
        $query = $db->getQuery(true);
        $query->select('id, title, theme, published, meta_title, meta_description, robots, schema_markup,
            meta_keywords, intro_image, page_alias, page_category, end_publishing, share_image, share_title, share_description,
            page_access, intro_text, created, language, hits, order_list, class_suffix, sitemap_override, sitemap_include, changefreq, priority')
            ->from('#__gridbox_pages')
            ->where('`app_id` = '.$id)
            ->where('`page_category` <>'.$db->Quote('trashed'));
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
        }
        
        $published = $this->getState('filter.state');
        if ($app->input->get('layout') == 'modal') {
            $published = 1;
        }

		if (is_numeric($published)) {
			$query->where('published = ' . (int) $published);
		} elseif ($published === '') {
			$query->where('(published IN (0, 1))');
		}
        $theme = $this->getState('filter.theme');
        if (!empty($theme)) {
            $query->where('theme = '.(int)$theme);
        }
        $access = $this->getState('filter.access');
        if (!empty($access)) {
            $query->where('page_access = '.$db->quote($access));
        }
        $language = $this->getState('filter.language');
        if (!empty($language)) {
            $query->where('language = '.$db->quote($language));
        }
        $orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol == 'ordering') {
			$orderCol = 'title ' . $orderDirn . ', ordering';
		}
        if ($orderCol == 'order_list') {
            $orderDirn = 'ASC';
        }
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
        
        return $query;
    }

    public function getItems()
    {
        $store = $this->getStoreId();
        $app = JFactory::getApplication();
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }
        $query = $this->_getListQuery();
        try
        {
            if ($app->input->get('layout') == 'modal') {
                $items = $this->_getList($query, 0, 0);
            } else {
                $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
            }            
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
        $this->cache[$store] = $items;

        return $this->cache[$store];
     }
    
    protected function checkThemes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, theme');
        $query->from('#__gridbox_pages');
        $db->setQuery($query);
        $pages = $db->loadObjectList();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__template_styles')
            ->where('`client_id` = 0')
            ->where('`template` = ' .$db->Quote('gridbox'))
            ->where('`home` = 1');
        $db->setQuery($query);
        $default = $db->loadResult();
        foreach ($pages as $page) {
            $query = $db->getQuery(true);
            $query->select('id')
                ->from('#__template_styles')
                ->where('`id` = ' .$db->Quote($page->theme));
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id != $page->theme) {
                $table = JTable::getInstance('pages', 'gridboxTable');
                $table->load($page->id);
                $table->bind(array('theme' => $default));
                $table->store();
            }
        }
    }
    
    public function getThemes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__template_styles')
            ->where('`template` = ' .$db->Quote('gridbox'));
        $db->setQuery($query);
        
        return $db->loadObjectList();
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
        $theme = $this->getUserStateFromRequest($this->context . '.filter.theme', 'theme_filter', '', 'string');
        $this->setState('filter.theme', $theme);
        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'access_filter', '', 'string');
        $this->setState('filter.access', $access);
        $language = $this->getUserStateFromRequest($this->context . '.filter.language', 'language_filter', '', 'string');
        $this->setState('filter.language', $language);
        parent::populateState('id', 'desc');
    }
}