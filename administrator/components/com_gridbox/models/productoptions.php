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

class gridboxModelProductoptions extends JModelList
{
    public $variations;

    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'published', 'state', 'order_list'
            );
        }
        parent::__construct($config);
    }

    public function updateProductoptions($data)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_store_products_fields', $data, 'id');
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_products_fields_data')
            ->where('field_id = '.$data->id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $array = [];
        $options = json_decode($data->options);
        foreach ($options as $option) {
            $array[$option->key] = $option;
        }
        $pks = [];
        foreach ($items as $item) {
            if (!isset($array[$item->option_key])) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_store_products_fields_data')
                    ->where('id = '.$item->id);
                $db->setQuery($query)
                    ->execute();
                $query = $db->getQuery(true)
                    ->select('DISTINCT product_id')
                    ->from('#__gridbox_store_product_variations_map')
                    ->where('option_key = '.$db->quote($item->option_key));
                $db->setQuery($query);
                $products = $db->loadObjectList();
                foreach ($products as $product) {
                    if (!in_array($product->product_id, $pks)) {
                        $pks[] = $product->product_id;
                    }
                }
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_store_product_variations_map')
                    ->where('option_key = '.$db->quote($item->option_key));
                $db->setQuery($query)
                    ->execute();
            } else {
                $item->value = $array[$item->option_key]->title;
                $item->color = $array[$item->option_key]->color;
                $item->image = $array[$item->option_key]->image;
                $db->updateObject('#__gridbox_store_products_fields_data', $item, 'id');
                unset($array[$item->option_key]);
            }
        }
        foreach ($array as $key => $option) {
            $obj = new stdClass();
            $obj->field_id = $data->id;
            $obj->option_key = $option->key;
            $obj->value = $option->title;
            $obj->color = $option->color;
            $obj->image = $option->image;
            $db->insertObject('#__gridbox_store_products_fields_data', $obj);
        }
        $this->restoreProductData($pks);
    }

    public function getOptions($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_products_fields')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        $pks = [];
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_products_fields')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_products_fields_data')
                ->where('field_id = '.$id);
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->select('DISTINCT product_id')
                ->from('#__gridbox_store_product_variations_map')
                ->where('field_id = '.$id);
            $db->setQuery($query);
            $products = $db->loadObjectList();
            foreach ($products as $product) {
                if (!in_array($product->product_id, $pks)) {
                    $pks[] = $product->product_id;
                    $query = $db->getQuery(true)
                        ->delete('#__gridbox_store_product_variations_map')
                        ->where('field_id = '.$id);
                    $db->setQuery($query)
                        ->execute();
                }
            }
        }
        $this->restoreProductData($pks);
    }

    public function restoreProductData($pks)
    {
        $db = JFactory::getDbo();
        foreach ($pks as $id) {
            $data = $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_product_data')
                ->where('product_id = '.$id);
            $db->setQuery($query);
            $data = $db->loadObject();
            $this->createVariations($id);
            $variations = new stdClass();
            foreach ($this->variations as $variation) {
                $obj = new stdClass();
                $obj->price = $data->price;
                $obj->sale_price = $data->sale_price;
                $obj->sku = $obj->stock = '';
                $variations->{$variation} = $obj;
            }
            $data->variations = json_encode($variations);
            $db->updateObject('#__gridbox_store_product_data', $data, 'id');
        }
    }

    public function createVariations($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_product_variations_map')
            ->where('product_id = '.$id)
            ->order('order_group ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $groups = [];
        $rows = [];
        foreach ($items as $item) {
            if (!isset($groups[$item->field_id])) {
                $groups[$item->field_id] = new stdClass();
                $groups[$item->field_id]->i = 0;
                $groups[$item->field_id]->items = [];
            }
            $groups[$item->field_id]->items[] = $item;
        }
        foreach ($groups as $group) {
            $rows[] = $group;
        }
        $this->variations = [];
        $this->getProductVariations($rows);
    }

    public function getProductVariations($groups)
    {
        $keys = [];
        foreach ($groups as $group) {
            $keys[] = $group->items[$group->i]->option_key;
        }
        $this->variations[] = implode('+', $keys);
        $n = count($groups) - 1;
        $this->incrementVariationsRowIndex($groups, $n);
    }

    public function incrementVariationsRowIndex($rows, $ind)
    {
        $n = count($rows[$ind]->items) - 1;
        if ($rows[$ind]->i < $n) {
            $rows[$ind]->i++;
            $this->getProductVariations($rows);
        } else if ($rows[$ind]->i == $n && isset($rows[$ind - 1])) {
            $rows[$ind]->i = 0;
            $this->incrementVariationsRowIndex($rows, $ind - 1);
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

    public function addProductOptions()
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = 'Product Options';
        $obj->field_key = 'item-'.time();
        $obj->field_type = 'dropdown';
        $obj->options = '[]';
        $db->insertObject('#__gridbox_store_products_fields', $obj);
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_products_fields');
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('title LIKE ' . $search);
        }
        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDirn = $this->state->get('list.direction', 'desc');
        if ($orderCol == 'order_list') {
            $orderDirn = 'ASC';
        }
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        
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