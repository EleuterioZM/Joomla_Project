<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelThemes extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'state'
            );
        }
        parent::__construct($config);
    }

    public function setFilters()
    {
        $this::populateState();
    }

    public function getItems()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, home, params')
            ->from('#__template_styles')
            ->where('`template` = ' .$db->Quote('gridbox'));
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
        }
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function getPlugins()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_plugins')
            ->order('joomla_constant asc');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $plugins = array();
        foreach ($items as $item) {
            $plugins[$item->title] = $item;
        }
        $str = json_encode($plugins);

        return $str;
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        parent::populateState('id', 'desc');
    }
    
}