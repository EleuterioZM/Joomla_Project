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

class gridboxModelPaymentmethods extends JModelList
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

    public function updateMethod($data)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_store_payment_methods', $data, 'id');
    }

    public function getOptions($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_payment_methods')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public function publish($cid, $value)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $obj = new stdClass();
            $obj->id = $id * 1;
            $obj->published = $value * 1;
            $db->updateObject('#__gridbox_store_payment_methods', $obj, 'id');
        }
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_payment_methods')
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

    public function addMethod($type)
    {
        $db = JFactory::getDbo();
        $obj = $this->getMethod($type);
        $db->insertObject('#__gridbox_store_payment_methods', $obj);
    }

    public function getMethod($key)
    {
        $obj = new stdClass();
        $obj->type = $key;
        $obj->settings = '{}';
        switch ($key) {
            case 'offline':
                $obj->title = 'Manual Payment';
                $obj->image = 'offline-payments.png';
                break;
            case 'paypal':
                $obj->title = 'PayPal';
                $obj->image = 'paypal.png';
                break;
            case 'twocheckout':
                $obj->title = '2Checkout';
                $obj->image = '2co.png';
                break;
            case 'stripe':
                $obj->title = 'Stripe';
                $obj->image = 'stripe.png';
                break;
            case 'cloudpayments':
                $obj->title = 'Cloudpayments';
                $obj->image = 'cloudpayments.png';
                break;
            case 'liqpay':
                $obj->title = 'LiqPay';
                $obj->image = 'liqpay.png';
                break;
            case 'payupl':
                $obj->title = 'PayU Polska';
                $obj->image = 'payu.png';
                break;
            case 'mollie':
                $obj->title = 'Mollie';
                $obj->image = 'mollie.png';
                break;
            case 'mono':
                $obj->title = 'Monobank';
                $obj->image = 'mono.png';
                break;
            case 'yandex-kassa':
                $obj->title = 'YooKassa';
                $obj->image = 'yookassa.png';
                break;
            case 'klarna':
                $obj->title = 'Klarna';
                $obj->image = 'klarna.png';
                break;
            case 'authorize':
                $obj->title = 'Authorize.Net';
                $obj->image = 'authorize-net.png';
                break;
            case 'payfast':
                $obj->title = 'PayFast';
                $obj->image = 'payfast.png';
                break;
            case 'robokassa':
                $obj->title = 'Robokassa';
                $obj->image = 'robokassa.png';
                break;
            case 'dotpay':
                $obj->title = 'Dotpay';
                $obj->image = 'dotpay.png';
                break;
            case 'pagseguro':
                $obj->title = 'Pagseguro';
                $obj->image = 'pagseguro.png';
                break;
            case 'square':
                $obj->title = 'Square';
                $obj->image = 'square.png';
                break;
            case 'barion':
                $obj->title = 'Barion';
                $obj->image = 'barion.png';
                break;
        }

        return $obj;
    }

    public function getPaymentsMethods()
    {
        $keys = array('offline', 'twocheckout', 'authorize', 'barion', 'cloudpayments', 'dotpay',
            'klarna', 'liqpay', 'mollie', 'mono', 'pagseguro', 'payfast', 'paypal', 'payupl',
            'robokassa', 'square', 'stripe', 'yandex-kassa');
        $methods = new stdClass();
        foreach ($keys as $key) {
            $methods->{$key} = $this->getMethod($key);
            $methods->{$key}->installed = false;
        }

        return $methods;
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_store_payment_methods')
            ->where('`order_list` = 0');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (!empty($items)) {
            $query = $db->getQuery(true)
                ->select('MAX(order_list) as max, COUNT(id) as count')
                ->from('#__gridbox_store_payment_methods')
                ->where('`order_list` <> 0');
            $db->setQuery($query);
            $obj = $db->loadObject();
            if ($obj->count == 0) {
                $obj->max = 0;
            }
            foreach ($items as $value) {
                $value->order_list = ++$obj->max;
                $db->updateObject('#__gridbox_store_payment_methods', $value, 'id');
            }
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_payment_methods');
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