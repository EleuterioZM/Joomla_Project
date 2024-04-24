<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;

class gridboxModelshipping extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'published', 'state', 'order_list'
            );
        }
        parent::__construct($config);
    }

    public function updateShipping($data)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_store_shipping', $data, 'id');
    }

    public function getOptions($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_shipping')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $array = array($obj);
        gridboxHelper::$storeHelper->checkShippingOptions($array);

        return $obj;
    }

    public function publish($cid, $value)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $obj = new stdClass();
            $obj->id = $id * 1;
            $obj->published = $value * 1;
            $db->updateObject('#__gridbox_store_shipping', $obj, 'id');
        }
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_shipping')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
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

    public function addShipping()
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = 'Shipping Method';
        $path = JPATH_ROOT.'/administrator/components/com_gridbox/assets/json/shipping-options.json';
        $str = gridboxHelper::readFile($path);
        $options = json_decode($str);
        //$options->type = 'pickup';
        $obj->options = json_encode($options);
        $db->insertObject('#__gridbox_store_shipping', $obj);
    }

    public function getCarriers()
    {
        $db = JFactory::getDbo();
        $array = [$db->quote('inpost'), $db->quote('novaposhta')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service IN ('.implode(',', $array).')');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $carriers = [];
        foreach ($array as $item) {
            if (empty($item->key)) {
                continue;
            }
            $carriers[] = $item;
        }

        return $carriers;
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_store_shipping')
            ->where('`order_list` = 0');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (!empty($items)) {
            $query = $db->getQuery(true)
                ->select('MAX(order_list) as max, COUNT(id) as count')
                ->from('#__gridbox_store_shipping')
                ->where('`order_list` <> 0');
            $db->setQuery($query);
            $obj = $db->loadObject();
            if ($obj->count == 0) {
                $obj->max = 0;
            }
            foreach ($items as $value) {
                $value->order_list = ++$obj->max;
                $db->updateObject('#__gridbox_store_shipping', $value, 'id');
            }
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_shipping');
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
        }
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('published = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(published IN (0, 1))');
        }
        $query->order($db->escape('order_list ASC'));
        
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
}