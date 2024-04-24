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

class gridboxModelSubscriptions extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'date', 'expires', 'publish_up', 'publish_down'
            );
        }
        parent::__construct($config);
    }

    public function setRenew($obj)
    {
        $cart = new stdClass();
        $cart->subtotal = $obj->subtotal;
        $cart->tax = $obj->tax ? $obj->tax->amount : 0;
        $cart->total = $obj->total;
        $cart->validPromo = $obj->validPromo;
        $cart->promo = $obj->promo;
        $cart->shipping = null;
        $cart->discount = $obj->discount;
        $product = new stdClass();
        $product->id = $obj->product_id;
        $product->renew_id = $obj->id;
        $product->plan_key = $obj->plan->key;
        $product->quantity = 1;
        $product->variation = '';
        $product->price = $obj->total;
        $product->sale_price = '';
        $product->extra_options = new stdClass();
        $product->product_type = 'subscription';
        $product->tax = $obj->tax;
        $product->sku = $obj->sku;
        $product->image = $obj->image;
        $product->title = $obj->title.' ('.$obj->plan->title.')';
        $product->net_price = $obj->subtotal - $obj->discount;
        $product->variations = new stdClass();
        $cart->products = array($product);
        $cart->info = new stdClass();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('value, customer_id')
            ->from('#__gridbox_store_user_info')
            ->where('user_id = '.$obj->user_id);
        $db->setQuery($query);
        $info = $db->loadObjectList();
        foreach ($info as $customer) {
            $cart->info->{$customer->customer_id} = $customer->value;
        }
        $order = gridboxHelper::$storeHelper->createAdminOrder($cart, $obj->user_id);
        gridboxHelper::$storeHelper->updateStatus($order->id, 'completed');
    }

    public function getPromo()
    {
        $db = JFactory::getDbo();
        $date = JDate::getInstance()->format('Y-m-d H:i:s');
        $date = $db->quote($date);
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('COUNT(p.id)')
            ->from('#__gridbox_store_promo_codes AS p')
            ->where('p.published = 1')
            ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$date.')')
            ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$date.')')
            ->where('(p.limit = 0 OR p.used < pc.limit)')
            ->leftJoin('#__gridbox_store_promo_codes AS pc ON pc.id = p.id');
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count;
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

    public function getSubscription($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('s.*, u.username, u.email, p.title, p.intro_image AS image')
            ->from('#__gridbox_store_subscriptions AS s')
            ->where('s.id = '.$id)
            ->leftJoin('#__users AS u ON u.id = s.user_id')
            ->leftJoin('#__gridbox_pages AS p ON p.id = s.product_id');
        $db->setQuery($query);
        $subscription = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('o.date, o.order_number, o.total, o.currency_symbol, o.currency_position')
            ->from('#__gridbox_store_subscriptions_map AS m')
            ->where('m.subscription_id = '.$id)
            ->leftJoin('#__gridbox_store_order_products AS p ON p.id = m.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = p.order_id');
        $db->setQuery($query);
        $subscription->orders = $db->loadObjectList();
        $subscription->renew = [];
        $subscription->categories = gridboxHelper::getProductCategoryId($subscription->product_id);
        $query = $db->getQuery(true)
            ->select('subscription, sku')
            ->from('#__gridbox_store_product_data')
            ->where('product_id = '.$subscription->product_id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if ($obj) {
            $subscription->sku = $obj->sku;
            $object = json_decode($obj->subscription);
            foreach ($object->renew->plans as $key => $plan) {
                if (empty($subscription->expires) || $plan->price == '') {
                    continue;
                }
                $plan->key = $key;
                $subscription->renew[] = $plan;
            }
        }
        $query = $db->getQuery(true)
            ->select('ui.value, ui.id')
            ->from('#__gridbox_store_customer_info AS ci')
            ->where('ci.type = '.$db->quote('country'))
            ->where('ui.user_id = '.$subscription->user_id)
            ->leftJoin('#__gridbox_store_user_info AS ui ON ui.customer_id = ci.id');
        $db->setQuery($query);
        $info = $db->loadObject();
        if (!empty($info->value)) {
            $object = json_decode($info->value);
            $subscription->country = $object->country;
            $subscription->region = $object->region;
        } else {
            $subscription->country = $subscription->region = '';
        }

        return $subscription;
    }

    public function setExpires($id, $expires)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, expires')
            ->from('#__gridbox_store_subscriptions')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $array = explode(' ', $obj->expires);
        $obj->expires = $expires.' '.$array[1];
        $db->updateObject('#__gridbox_store_subscriptions', $obj, 'id');

        return $obj;
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('s.*, p.title, u.email, u.username')
            ->from('#__gridbox_store_subscriptions AS s')
            ->leftJoin('#__gridbox_pages AS p ON p.id = s.product_id')
            ->leftJoin('#__users AS u ON u.id = s.user_id');
        $status = $this->getState('filter.state');
        $now = date('Y-m-d H:i:s');
        if ($status == 'active') {
            $query->where('s.expires > '.$db->quote($now));
        } else if ($status == 'expired') {
            $query->where('s.expires < '.$db->quote($now));
        }
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%'.$db->escape($search, true) . '%', false);
            $query->where('(p.title LIKE '.$search.' OR u.email LIKE '.$search.' OR u.username LIKE '.$search.')');
        }
        $publish_up = $this->getState('filter.publish_up');
        if (!empty($publish_up)) {
            $publish_up = $publish_up.' 00:00:01';
            $query->where('s.date > '.$db->quote($publish_up));
        }
        $publish_down = $this->getState('filter.publish_down');
        if (!empty($publish_down)) {
            $publish_down = $publish_down.' 23:59:59';
            $query->where('s.expires < '.$db->quote($publish_down));
        }
        $order = $this->state->get('list.ordering', 'date');
        $dir = $this->state->get('list.direction', 'desc');
        if ($order == 'date' || $order == 'expires' || $order == 'id') {
            $order = 's.'.$order;
        } else if ($order == 'title') {
            $order = 'p.'.$order;
        }
        $query->order($order.' '.$dir.', s.id DESC');
        
        return $query;
    }

    public function delete($cid)
    {
        foreach ($cid as $id) {
            gridboxHelper::$storeHelper->removeSubscription($id);
        }
    }

    protected function getStoreId($id = '')
    {
        $id .= ':'.$this->getState('filter.search');
        $id .= ':'.$this->getState('filter.state');
        
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        $publish_up = $this->getUserStateFromRequest($this->context.'.filter.publish_up', 'publish_up', '', 'string');
        $publish_down = $this->getUserStateFromRequest($this->context.'.filter.publish_down', 'publish_down', '', 'string');
        $this->setState('filter.publish_up', $publish_up);
        $this->setState('filter.publish_down', $publish_down);
        parent::populateState('id', 'desc');
    }
}