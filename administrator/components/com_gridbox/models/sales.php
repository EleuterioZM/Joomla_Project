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

class gridboxModelSales extends JModelList
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

    public function getAccess()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__viewlevels')
            ->order($db->quoteName('ordering') . ' ASC')
            ->order($db->quoteName('title') . ' ASC');
        $db->setQuery($query);
        $access = $db->loadObjectList();

        return $access;
    }

    public function updateSales($data)
    {
        $data->publish_up = empty($data->publish_up) ? '0000-00-00 00:00:00' : $data->publish_up;
        $data->publish_down = empty($data->publish_down) ? '0000-00-00 00:00:00' : $data->publish_down;
        $map = json_decode($data->map);
        $db = JFactory::getDbo();
        unset($data->map);
        $db->updateObject('#__gridbox_store_sales', $data, 'id');
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_sales_map')
            ->where('sale_id = '.$data->id)
            ->where('type <> '.$db->quote($data->applies_to));
        $db->setQuery($query)
            ->execute();
        $pks = array();
        foreach ($map as $obj) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_sales_map')
                ->where('sale_id = '.$data->id)
                ->where('item_id = '.$obj->id)
                ->where('variation = '.$db->quote($obj->variation));
            $db->setQuery($query);
            $obj->item_id = $db->loadResult();
            if ($obj->item_id) {
                $pks[] = $obj->item_id;
            }
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_sales_map')
            ->where('sale_id = '.$data->id);
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('id NOT IN ('.$str.')');
        }
        $db->setQuery($query)
            ->execute();
        foreach ($map as $obj) {
            if (empty($obj->item_id)) {
                $object = new stdClass();
                $object->sale_id = $data->id;
                $object->item_id = $obj->id;
                $object->variation = $obj->variation;
                $object->type = $data->applies_to;
                $db->insertObject('#__gridbox_store_sales_map', $object);
            }
        }
    }

    public function getOptions($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_sales')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if ($obj->applies_to == 'category') {
            $query = $db->getQuery(true)
                ->select('m.item_id AS id, p.title, p.image')
                ->from('#__gridbox_store_sales_map AS m')
                ->where('m.sale_id = '.$id)
                ->leftJoin('#__gridbox_categories AS p ON p.id = m.item_id');
            $db->setQuery($query);
            $obj->map = $db->loadObjectList();
        } else if ($obj->applies_to == 'product') {
            $query = $db->getQuery(true)
                ->select('m.item_id AS id, p.title, p.intro_image AS image, d.variations, m.variation')
                ->from('#__gridbox_store_sales_map AS m')
                ->where('m.sale_id = '.$id)
                ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = m.item_id')
                ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id');
            $db->setQuery($query);
            $obj->map = $db->loadObjectList();
            foreach ($obj->map as $key => $product) {
                $variations = json_decode($product->variations);
                if (empty($product->variation) || !isset($variations->{$product->variation})) {
                    continue;
                }
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_product_variations_map')
                    ->where('product_id = '.$product->id);
                $db->setQuery($query);
                $variations_map = $db->loadObjectList();
                $images = new stdClass();
                foreach ($variations_map as $variation) {
                    $images->{$variation->option_key} = json_decode($variation->images);
                }
                $vars = explode('+', $product->variation);
                $info = array();
                foreach ($vars as $value) {
                    $query = $db->getQuery(true)
                        ->select('fd.value, f.title')
                        ->from('#__gridbox_store_products_fields_data AS fd')
                        ->where('fd.option_key = '.$db->quote($value))
                        ->leftJoin('#__gridbox_store_products_fields AS f ON f.id = fd.field_id');
                    $db->setQuery($query);
                    $variation = $db->loadObject();
                    $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
                    if (!empty($images->{$value})) {
                        $product->image = $images->{$value}[0];
                    }
                }
                $product->info = implode('/', $info);
            }
        } else {
            $obj->map = array();
        }
        $nullDate = $db->getNullDate();
        if ($obj->publish_up == $nullDate) {
            $obj->publish_up = '';
        }
        if ($obj->publish_down == $nullDate) {
            $obj->publish_down = '';
        }

        return $obj;
    }

    public function publish($cid, $value)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $obj = new stdClass();
            $obj->id = $id * 1;
            $obj->published = $value * 1;
            $db->updateObject('#__gridbox_store_sales', $obj, 'id');
        }
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_sales')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_sales_map')
                ->where('sale_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function duplicate($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_sales')
                ->where('id = '.$id);
            $db->setQuery($query);
            $code = $db->loadObject();
            $code->published = 0;
            $id = $code->id;
            unset($code->id);
            $db->insertObject('#__gridbox_store_sales', $code);
            $pk = $db->insertid();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_sales_map')
                ->where('sale_id = '.$id);
            $db->setQuery($query);
            $map = $db->loadObjectList();
            foreach ($map as $value) {
                $value->sale_id = $pk;
                unset($value->id);
                $db->insertObject('#__gridbox_store_sales_map', $value);
            }
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

    public function addSales()
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = 'Sales';
        $db->insertObject('#__gridbox_store_sales', $obj);
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_sales');
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