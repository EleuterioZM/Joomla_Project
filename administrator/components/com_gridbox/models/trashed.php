<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class gridboxModelTrashed extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'app_id', 'published', 'theme', 'title', 'hits'
            );
        }
        parent::__construct($config);
    }

    public function setFilters()
    {
        $this::populateState();
    }
    
    protected function getListQuery()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, theme, app_id, hits, language')
            ->from('#__gridbox_pages')
            ->where('`page_category` ='.$db->quote('trashed'));
        $q2 = $db->getQuery(true)
            ->select('id, title, theme, type AS app_id, published AS hits, language')
            ->from('#__gridbox_system_pages')
            ->where('published = -1');
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
            $q2->where('title LIKE ' . $search);
        }
        $language = $this->getState('filter.language');
        if (!empty($language)) {
            $query->where('language = '.$db->quote($language));
            $q2->where('language = '.$db->quote($language));
        }
        $app = $this->getState('filter.app');
        if (!empty($app)) {
            $query->where('app_id = '.$db->quote($app));
        }
        $orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
        $q2->order($db->escape($orderCol.' '.$orderDirn));
        $query->unionAll($q2);
        
        return $query;
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
            if ($app->input->get('layout') == 'modal') {
                $items = $this->_getList($query, 0, 0);
            } else {
                $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
            }
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        $this->cache[$store] = $items;

        return $this->cache[$store];
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
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $language = $this->getUserStateFromRequest($this->context . '.filter.language', 'language_filter', '', 'string');
        $this->setState('filter.language', $language);
        $app = $this->getUserStateFromRequest($this->context . '.filter.app', 'filter_state', '', 'string');
        $this->setState('filter.app', $app);
        parent::populateState('id', 'desc');
    }
}