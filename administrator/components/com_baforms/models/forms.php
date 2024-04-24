<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class baformsModelForms extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'ordering', 'published'
            );
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, ordering, published')
            ->from('#__baforms_forms');
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
        } else if ($published === '') {
            $query->where('(published IN (0, 1))');
        }        
        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDirn = $this->state->get('list.direction', 'desc');
        if ($orderCol == 'ordering') {
            $orderCol = 'title ' . $orderDirn . ', ordering';
        }
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        
        return $query;
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        
        return parent::getStoreId($id);
    }

    public function getModalItems()
    {
        $db = JFactory::getDbo();
        $query = $this->getListQuery();
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }
    
    public function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '');
        $this->setState('filter.state', $published);
        
        parent::populateState('id', 'desc');
    }
}