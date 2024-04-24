<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class store
{
    private $store;
    private $files;
    private $pdf;
    private $tags;
    private $email;
    private $config;
    public $pending;
    public $sales;
    public $reminder;
    public $attachments;
    public $stock;

    public function __construct()
    {
        $this->store = $this->checkSettings();
        $this->setCurrency();
        $this->config = JFactory::getConfig();
        $this->sales = $this->getSales();
        $this->pending = ['payupl', 'yandex-kassa', 'barion', 'paypal', 'stripe', 'klarna'];
        $this->attachments = 'components/com_gridbox/assets/uploads/attachments';
        $this->removeTrashedAttachments();
        foreach ($this->store->notifications as $notification) {
            if ($notification->status == 'reminder') {
                $this->reminder = $notification;
            } else if ($notification->status == 'stock') {
                $this->stock = $notification;
            }
        }
    }

    public function setCurrency()
    {
        $currency = $this->getDefaultCurrency();
        if ($this->store->currencies->auto) {
            $this->updateAutoExchangerates($currency);
        }
        $app = JFactory::getApplication();
        $view = $app->input->getCmd('view', '');
        if (JLanguageAssociations::isEnabled() && $app->isClient('site') && $view != 'gridbox' && $view != 'editor') {
            $tag = JFactory::getLanguage()->getTag();
            foreach ($this->store->currencies->list as $obj) {
                if ($obj->language == $tag) {
                    $currency = $obj;
                    break;
                }
            }
        }
        if ($app->isClient('site') && $view != 'gridbox' && $view != 'editor'
            && !empty($code = $app->input->cookie->get('gridbox-currency', '', 'string'))) {
            foreach ($this->store->currencies->list as $obj) {
                if ($obj->code == $code) {
                    $currency = $obj;
                    break;
                }
            }
        }
        $this->store->currency = $currency;
    }

    public function getDefaultCurrency()
    {
        $currency = null;
        foreach ($this->store->currencies->list as $obj) {
            if ($obj->default) {
                $currency = $obj;
                break;
            }
        }

        return $currency;
    }

    public function updateAutoExchangerates($default)
    {
        $exchangerates = $this->getService('exchangerates');
        if (empty($exchangerates->key)) {
            return;
        }
        $obj = $this->getService('exchangerates_data');
        $json = json_decode($obj->key);
        $update = false;
        if (!isset($json->base) || $json->base != $default->code || $json->time + 3600 * 12 < time()) {
            $update = true;
        }
        foreach ($this->store->currencies->list as $currency) {
            if ($currency->default) {
                continue;
            } else if ($update || !isset($json->rates->{$currency->code})) {
                $update = true;
                break;
            }
        }
        if (isset($json->key) && $json->key != $exchangerates->key) {
            $update = true;
        }
        if ($update && count($this->store->currencies->list) > 1) {
            $array = [];
            foreach ($this->store->currencies->list as $currency) {
                if ($currency->default) {
                    continue;
                }
                $array[] = $currency->code;
            }
            $json = $this->getAutoExchangerates($default->code, $array, $json, $exchangerates->key);
            $obj->key = json_encode($json);
            $db = JFactory::getDbo();
            $db->updateObject('#__gridbox_api', $obj, 'id');
        }

        foreach ($this->store->currencies->list as $currency) {
            if ($currency->default) {
                continue;
            }
            $currency->rate = $json->rates->{$currency->code};
        }
    }

    public function getAutoExchangerates($base, $array, $old, $key)
    {
        $curl = curl_init();
        $symbols = implode(',', $array);
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/latest?symbols=".$symbols."&base=".$base,
            CURLOPT_HTTPHEADER => [
                "Content-Type: text/plain",
                "apikey: ".$key
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($response);        
        $obj = new stdClass();
        $obj->time = time();
        $obj->base = $base;
        $obj->rates = new stdClass();
        foreach ($array as $currency) {
            $value = isset($old->rates->{$currency}) ? $old->rates->{$currency} : 1;
            $obj->rates->{$currency} = isset($json->rates->{$currency}) ? $json->rates->{$currency} : $value;
        }
        if (isset($json->message)) {
            $obj->message = $json->message;
            $obj->key = $key;
        }

        return $obj;
    }

    public function removeTrashedAttachments()
    {
        $date = date('Y-m-d-H-i-s', strtotime("-1 day"));
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from('#__gridbox_store_product_attachments AS a')
            ->leftJoin("#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id")
            ->where('a.date < '.$db->quote($date))
            ->where('m.product_id = 0');
        $db->setQuery($query);
        $files = $db->loadObjectList();
        foreach ($files as $file) {
            $this->deleteAttachment($file->id, $file->filename);
        }
    }

    public function removeProductAttachment($product_id = 0, $cart_id = 0, $wishlist_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id, m.cart_id, m.wishlist_id')
            ->from('#__gridbox_store_product_attachments AS a')
            ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id');
        if (!empty($wishlist_id)) {
            $query->where('m.wishlist_id = '.$wishlist_id);
        }
        if (!empty($cart_id)) {
            $query->where('m.cart_id = '.$cart_id);
        }
        if (!empty($product_id)) {
            $query->where('m.product_id = '.$product_id);
        }
        $db->setQuery($query);
        $attachments = $db->loadObjectList();
        foreach ($attachments as $attachment) {
            $this->removeAttachment($attachment->id, $attachment->cart_id, $attachment->wishlist_id);
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_cart_attachments_map');
        if (!empty($wishlist_id)) {
            $query->where('wishlist_id = '.$wishlist_id);
        }
        if (!empty($cart_id)) {
            $query->where('cart_id = '.$cart_id);
        }
        if (!empty($product_id)) {
            $query->where('product_id = '.$product_id);
        }
        $db->setQuery($query)
            ->execute();
    }

    public function removeAttachment($id, $cart_id = 0, $wishlist_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from('#__gridbox_store_product_attachments AS a')
            ->where('a.id = '.$id)
            ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id');
        if (!empty($cart_id)) {
            $query->where('m.cart_id = '.$cart_id);
        } else if (!empty($wishlist_id)) {
            $query->where('m.wishlist_id = '.$wishlist_id);
        }
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!$obj) {
            return;
        }
        $this->deleteAttachment($id, $obj->filename);
    }

    public function deleteAttachment($id, $filename)
    {
        $file = JPATH_ROOT.'/'.$this->attachments.'/'.$filename;
        JFile::delete($file);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_product_attachments')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function checkPendingPayments()
    {
        $db = JFactory::getDbo();
        $types = [];
        foreach ($this->pending as $type) {
            $types[] = 'p.type = '.$db->quote($type);
        }
        $query = $db->getQuery(true)
            ->select('count(o.id)')
            ->from('#__gridbox_store_orders AS o')
            ->where('o.published = 0')
            ->where('('.implode(' OR ', $types).')')
            ->where('o.params <> '.$db->quote(''))
            ->leftJoin('#__gridbox_store_orders_payment AS p ON o.id = p.order_id');
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count != 0;
    }

    public function getSales()
    {
        $db = JFactory::getDbo();
        $offset = $this->config->get('offset');
        $tz = new DateTimeZone($offset);
        $date = new DateTime('now', $tz);        
        $now = $date->format('Y-m-d H:i:s');
        $now = $db->quote($now);
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_sales')
            ->where('(publish_down = '.$nullDate.' OR publish_down >= '.$now.')')
            ->where('(publish_up = '.$nullDate.' OR publish_up <= '.$now.')')
            ->where('published = 1')
            ->order('id asc');
        $db->setQuery($query);
        $sales = $db->loadObjectList();
        foreach ($sales as $sale) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_sales_map')
                ->where('sale_id = '.$sale->id);
            $db->setQuery($query);
            $sale->map = $db->loadObjectList();
        }

        return $sales;
    }

    public function sendReminder()
    {
        if (empty($this->reminder->email)) {
            return;
        }
        $db = JFactory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('p.id, p.title, d.subscription, d.sku')
            ->from('#__gridbox_store_product_data AS d')
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->where('d.product_type = '.$db->quote('subscription'))
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        foreach ($items as $item) {
            if (empty($item->subscription)) {
                continue;
            }
            $subscription = json_decode($item->subscription);
            $renew = $subscription->renew;
            if (!empty($renew->remind->value)) {
                $time = strtotime('+'.$renew->remind->value.' '.$expires[$renew->remind->format]);
                $expired = $db->quote(date("Y-m-d H:i:s", $time));
                $query = $db->getQuery(true)
                    ->select('s.*, u.email')
                    ->from('#__gridbox_store_subscriptions AS s')
                    ->leftJoin('#__users AS u ON u.id = s.user_id')
                    ->where('s.product_id = '.$item->id)
                    ->where('s.expires <> '.$db->quote(''))
                    ->where('s.expires > '.$date)
                    ->where('s.expires < '.$expired)
                    ->where('s.reminded = 0');
                $db->setQuery($query);
                $list = $db->loadObjectList();
                foreach ($list as $obj) {
                    $query = $db->getQuery(true)
                        ->select('o.id')
                        ->from('#__gridbox_store_orders AS o')
                        ->leftJoin('#__gridbox_store_order_products AS p ON p.order_id = o.id')
                        ->leftJoin('#__gridbox_store_subscriptions_map AS m ON m.product_id = p.id')
                        ->where('m.subscription_id = '.$obj->id);
                    $db->setQuery($query);
                    $order_id = $db->loadResult();
                    $this->prepareEmails($order_id);
                    $this->tags->{'[Product Title]'} = $item->title;
                    $this->tags->{'[Product SKU]'} = $item->sku;
                    $this->tags->{'[Product Quantity]'} = 1;
                    $this->tags->{'[Expiration Date]'} = gridboxHelper::formatDate($obj->expires);
                    $this->email = $obj->email;
                    $this->sendStoreEmail('reminder', $order_id);
                    $query = $db->getQuery(true)
                        ->update('#__gridbox_store_subscriptions')
                        ->set('reminded = 1')
                        ->where('id = '.$obj->id);
                    $db->setQuery($query)
                        ->execute();
                }
            }
        }
    }

    public function getSettings()
    {
        return $this->store;
    }

    public function getTracking($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_tracking')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $tracking = $db->loadObject();
        if (!$tracking) {
            $tracking = new stdClass();
            $tracking->id = 0;
            $tracking->order_id = $id;
            $tracking->number = $tracking->url = $tracking->title = '';
        }

        return $tracking;
    }

    public function getStorePayment($type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_payment_methods')
            ->where('type = '.$db->quote($type));
        $db->setQuery($query);
        $payment = $db->loadObject();
        $payment->params = json_decode($payment->settings);

        return $payment;
    }

    public function getService($service)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote($service));
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    protected function checkSettings()
    {
        $db = JFactory::getDbo();
        $obj = $this->getService('store');
        $store = json_decode($obj->key);
        $path = JPATH_ROOT.'/administrator/components/com_gridbox/assets/json/store.json';
        $dir = JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/store-options/';
        $update = false;
        if (!isset($store->general)) {
            $str = gridboxHelper::readFile($path);
            $store = json_decode($str);
            foreach ($store->notifications as $notification) {
                $file = $dir.$notification->status.'-'.$notification->type.'.html';
                if ($notification->title == 'Customer Review') {
                    $file = $dir.'customer-review.html';
                }
                $notification->body = gridboxHelper::readFile($file);
            }
            $update = true;
        }
        if (!isset($store->currencies)) {
            $currencies = new stdClass();
            $currencies->list = [];
            $currencies->auto = false;
            $currency = $store->currency;
            $currency->default = true;
            $currency->rate = 1;
            $currency->title = 'Currency';
            $currency->language = '*';
            $currencies->list[] = $currency;
            $store->currencies = $currencies;
            $update = true;
        }
        if (!isset($store->notifications)) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            $array = ['notification', 'stock', 'confirmation', 'completed', 'reminder'];
            foreach ($object->notifications as $i => $notification) {
                if (isset($store->{$array[$i]})) {
                    $object->notifications[$i] = gridboxHelper::object_extend($notification, $store->{$array[$i]});
                    unset($store->{$array[$i]});
                } else {
                    $file = $dir.$notification->status.'-'.$notification->type.'.html';
                    if ($notification->title == 'Customer Review') {
                        $file = $dir.'customer-review.html';
                    }
                    $notification->body = gridboxHelper::readFile($file);
                }
            }
            $store->notifications = $object->notifications;
            $update = true;
        }
        $hasBooking = false;
        foreach ($store->notifications as $notification) {
            if ($notification->status == 'new-booking') {
                $hasBooking = true;
                break;
            }
        }
        if (!$hasBooking) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            foreach ($object->notifications as $i => $notification) {
                if ($notification->status == 'new-booking' || $notification->status == 'appointment-reminder') {
                    $notification->body = gridboxHelper::readFile($dir.$notification->status.'-'.$notification->type.'.html');
                    $store->notifications[] = $notification;
                }
            }
            $update = true;
        }
        if (!isset($store->tax->mode)) {
            $tax = new stdClass();
            $tax->mode = 'excl';
            $tax->rates = [];
            if (!empty($store->tax->amount)) {
                $rate = new stdClass();
                $rate->title = JText::_('TAX');
                $rate->rate = $store->tax->amount;
                $rate->categories = [];
                $rate->country = '';
                $rate->regions = [];
                $rate->shipping = $store->tax->shipping;
                $tax->rates[] = $rate;
            }
            $store->tax = $tax;
            $update = true;
        }
        if (!isset($store->checkout)) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            $store->checkout = $object->checkout;
            $store->wishlist = $object->wishlist;
            $update = true;
        }
        if (!isset($store->invoice)) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            $store->invoice = $object->invoice;
            $update = true;
        }
        if (!isset($store->checkout->minimum)) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            $store->checkout->minimum = $object->checkout->minimum;
            $update = true;
        }
        if (!isset($store->checkout->facebook)) {
            $store->checkout->facebook = $store->checkout->google = false;
            $store->checkout->password = $store->checkout->username = true;
            $store->checkout->recaptcha = '';
            $update = true;
        }
        if (!isset($store->units)) {
            $str = gridboxHelper::readFile($path);
            $object = json_decode($str);
            $store->units = $object->units;
            $update = true;
        }
        foreach ($store->tax->rates as $rate) {
            if (!isset($rate->country_id) && !empty($rate->country)) {
                $update = true;
                $country = new stdClass();
                $country->title = $rate->country;
                $db->insertObject('#__gridbox_countries', $country);
                $rate->country_id = $db->insertid();
                unset($rate->country);
                foreach ($rate->regions as $region) {
                    $state = new stdClass();
                    $state->title = $region->title;
                    $state->country_id = $rate->country_id;
                    $db->insertObject('#__gridbox_country_states', $state);
                    $region->state_id = $db->insertid();
                    unset($region->title);
                }
            }
        }
        if (isset($store->sales) && !empty($store->sales->amount)) {
            $sales = new stdClass();
            $sales->title = 'Sales';
            $sales->unit = '%';
            $sales->discount = $store->sales->amount;
            $sales->applies_to = $store->sales->applies_to;
            $sales->publish_up = $store->sales->publish_up;
            $sales->publish_down = $store->sales->publish_down;
            $db->insertObject('#__gridbox_store_sales', $sales);
            $sales->id = $db->insertid();
            foreach ($store->sales->map as $value) {
                $map = new stdClass();
                $map->type = $sales->applies_to;
                $map->sale_id = $sales->id;
                $map->item_id = $value;
                $db->insertObject('#__gridbox_store_sales_map', $map);
            }
        }
        if (isset($store->sales)) {
            unset($store->sales);
            $update = true;
        }
        if ($update) {
            $obj->key = json_encode($store);
            $db->updateObject('#__gridbox_api', $obj, 'id');
        }

        return $store;
    }

    public function checkShippingOptions($items)
    {
        $db = JFactory::getDbo();
        $options = null;
        foreach ($items as $item) {
            if (empty($item->options)) {
                if (!$options) {
                    $path = JPATH_ROOT.'/administrator/components/com_gridbox/assets/json/shipping-options.json';
                    $str = gridboxHelper::readFile($path);
                    $options = json_decode($str);
                }
                $options->flat->price = $item->price;
                $options->flat->enabled = $item->free !== '' ? true : false;
                $options->flat->free = $item->free;
                $item->options = json_encode($options);
                $db->updateObject('#__gridbox_store_shipping', $item, 'id');
            }
        }
    }

    public function checkAppType($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();

        return $type == 'products';
    }

    public function getOrder($id, $deep = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$id);
        $db->setQuery($query);
        $order = $db->loadObject();
        if ($deep) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_customer_info')
                ->where('order_id = '.$id);
            $db->setQuery($query);
            $order->info = $db->loadObjectList();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_orders_discount')
                ->where('order_id = '.$id);
            $db->setQuery($query);
            $order->promo = $db->loadObject();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_orders_shipping')
                ->where('order_id = '.$id);
            $db->setQuery($query);
            $order->shipping = $db->loadObject();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_products')
                ->where('order_id = '.$id);
            $db->setQuery($query);
            $order->products = $db->loadObjectList();
            $query = $db->getQuery(true)
                ->select('a.*')
                ->from('#__gridbox_store_product_attachments AS a')
                ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON a.attachment_id = m.id')
                ->where('m.order_id = '.$order->id);
            $db->setQuery($query);
            $order->attachments = $db->loadObjectList();
            foreach ($order->products as $product) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_order_product_variations')
                    ->where('product_id = '.$product->id);
                $db->setQuery($query);
                $product->variations = $db->loadObjectList();
            }
        }

        return $order;
    }

    protected function getPayment($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_payment')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $payment = $db->loadObject();
        if ($payment) {
            $payment = $this->getStorePayment($payment->type);
        }

        return $payment;
    }

    public function authorizePayupl($params)
    {
        $url = 'https://secure'.($params->environment == 'sandbox' ? '.snd' : '').'.payu.com';
        $post = 'grant_type=client_credentials&client_id='.$params->client_id.'&client_secret='.$params->client_secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."/pl/standard/user/oauth/authorize");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response);

        return $json;
    }

    public function checkBarion($barion, $order, $cookie = true, $redirect = true, $exit = true)
    {
        $params = $barion->params;
        $url = 'https://api.'.($params->environment == 'sandbox' ? 'test.' : '').'barion.com/v2/Payment/GetPaymentState?';
        $data = [
            'POSKey' => $params->secret_key,
            'PaymentId' => $order->params
        ];
        $url .= http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($str);
        if (isset($response->Status) && $response->Status == 'Succeeded') {
            $this->approveOrder($order->id, $str, true, $cookie, $redirect, $exit);
        } else if (isset($response->Status) && ($response->Status == 'Canceled' || $response->Status == 'Failed')) {
            $this->setCanceled($order);
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        } else {
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function checkPayupl($payupl, $order, $authorize, $cookie = true, $redirect = true, $exit = true)
    {
        $params = json_decode($order->params);
        $url = 'https://secure'.($payupl->params->environment == 'sandbox' ? '.snd' : '').'.payu.com';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."/api/v2_1/orders/".$params->id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer ".$authorize->access_token
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response);
        if ($json->orders[0]->status == 'COMPLETED') {
            $this->approveOrder($order->id, $response, true, $cookie, $redirect, $exit);
        } else if ($json->orders[0]->status == 'CANCELED') {
            $this->setCanceled($order);
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        } else {
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function checkYandex($yandex, $order, $cookie = true, $redirect = true, $exit = true)
    {
        $params = json_decode($order->params);
        if (isset($params->type) && $params->type == 'error') {
            $this->setCanceled($order);
            return;
        }
        $headers = array('Content-Type: application/json');
        $curl = curl_init('https://api.yookassa.ru/v3/payments/'.$params->id);
        curl_setopt($curl, CURLOPT_USERPWD, $yandex->params->shop_id.':'.$yandex->params->secret_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($body);
        if (isset($response->status) && $response->status == 'succeeded') {
            $this->approveOrder($order->id, $body, true, $cookie, $redirect, $exit);
        } else if (isset($response->status) && $response->status == 'canceled') {
            $this->setCanceled($order);
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        } else {
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function checkStripe($stripe, $order, $cookie = true, $redirect = true, $exit = true)
    {
        $params = json_decode($order->params);
        $ua = array('bindings_version' => '7.17.0', 'lang' => 'php',
            'lang_version' => phpversion(), 'publisher' => 'stripe', 'uname' => php_uname());
        $headers = array('X-Stripe-Client-User-Agent: '.json_encode($ua),
            'User-Agent: Stripe/v1 PhpBindings/7.17.0',
            'Authorization: Bearer '.$stripe->params->secret_key);
        $url = 'https://api.stripe.com/v1/checkout/sessions/'.$params->id;
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_HTTPGET] = 1;
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($body);
        if ($json->status == 'complete') {
            $this->approveOrder($order->id, $body, true, $cookie, $redirect, $exit);
        } else if ($json->status == 'expired') {
            $this->setCanceled($order);
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        } else {
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function checkKlarna($klarna, $order, $cookie = true, $redirect = true, $exit = true)
    {
        $order_id = $order->params;
        $headers = ['Content-Type: application/json'];
        $url = 'https://api.';
        if ($klarna->params->region == 'america') {
            $url .= 'na.';
        } else if ($klarna->params->region == 'oceania') {
            $url .= 'oc.';
        }
        if ($klarna->params->environment == 'sandbox') {
            $url .= 'playground.';
        }
        $url .= 'klarna.com/';
        $curl = curl_init($url.'checkout/v3/orders/'.$order_id);
        curl_setopt($curl, CURLOPT_USERPWD, $klarna->params->username.':'.$klarna->params->password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($body);
        if ($response->status == 'checkout_complete') {
            $curl = curl_init($url.'ordermanagement/v1/orders/'.$order_id.'/acknowledge');
            curl_setopt($curl, CURLOPT_USERPWD, $klarna->params->username.':'.$klarna->params->password);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, CURLOPT_TIMEOUT, 80);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            $response = curl_exec($curl);
            $this->approveOrder($order->id, null, true, $cookie, $redirect, $exit);
        } else {
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function checkPaypal($order, $cookie = true, $redirect = true, $exit = true)
    {
        $params = json_decode($order->params);
        if ($params->status == 'COMPLETED') {
            $this->approveOrder($order->id, $order->params, true, $cookie, $redirect, $exit);
        }
    }

    public function checkMono($mono, $order, $cookie = true, $redirect = true, $exit = true)
    {
        $headers = array('X-Token: '.$mono->params->token);
        $invoiceId = $order->params;
        $curl = curl_init();
        $options = array();
        $options[CURLOPT_URL] = 'https://api.monobank.ua/api/merchant/invoice/status?invoiceId='.$invoiceId;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $json = json_decode($body);
        if (isset($json->status) && $json->status == 'success') {
            $this->approveOrder($order->id, null, true, $cookie, $redirect, $exit);
        } else if (isset($json->status) && ($json->status == 'failure' || $json->status == 'expired')) {
            $this->setCanceled($order);
            $this->approveOrder(0, null, false, $cookie, $redirect, $exit);
        }
    }

    public function setCanceled($order)
    {
        $order->published = -1;
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_store_orders', $order, 'id');
    }

    public function setOrder($id)
    {
        $payment = $this->getPayment($id);
        $input = JFactory::getApplication()->input;
        $empty = ['liqpay', 'mollie', 'payfast', 'dotpay', 'pagseguro'];
        $order = $this->getOrder($id);
        if (!$payment || $payment->type == 'offline') {
            $this->approveOrder($id);
        } else if (in_array($payment->type, $empty)) {
            $this->approveOrder(0, null, false);
        } else if ($payment->type == 'robokassa') {
            $inv_id = $input->get("InvId", 0, 'int');
            $approveId = (!empty($inv_id) && $inv_id == $id) ? $id : 0;
            $update = (!empty($inv_id) && $inv_id == $id) ? true : false;
            $this->approveOrder($approveId, null, $update);
        } else if ($payment->type == 'square') {
            $transactionId = $input->get('transactionId', '', 'string');
            if ($order->params == $transactionId) {
                $this->approveOrder($id);
            } else {
                $this->approveOrder(0, null, false);
            }
        } else if ($payment->type == 'paypal') {
            $params = json_decode($order->params);
            if ($params->status == 'COMPLETED') {
                $this->approveOrder($id, $order->params, true);
            } else {
                $this->approveOrder(0, null, false);
            }
        } else if ($payment->type == 'twocheckout') {
            $params = json_decode($order->params);
            if (!empty($params->products)) {
                $this->approveOrder($id, $order->params, true);
            } else {
                $this->approveOrder(0, null, false);
            }
        } else if ($payment->type == 'authorize') {
            $params = json_decode($order->params);
            if (!empty($params->transactionResponse->transId)) {
                $this->approveOrder($id);
            } else {
                $this->approveOrder(0, null, false);
            }
        } else if ($payment->type == 'cloudpayments') {
            $params = json_decode($order->params);
            if (isset($params->amount)) {
                $this->approveOrder($id);
            } else {
                $this->approveOrder(0, null, false);
            }
        } else if ($payment->type == 'barion') {
            $this->checkBarion($payment, $order);
        } else if ($payment->type == 'payupl') {
            $json = $this->authorizePayupl($payment->params);
            $this->checkPayupl($payment, $order, $json);
        } else if ($payment->type == 'klarna') {
            $this->checkKlarna($payment, $order);
        } else if ($payment->type == 'mono') {
            $this->checkMono($payment, $order);
        } else if ($payment->type == 'yandex-kassa') {
            $this->checkYandex($payment, $order);
        } else if ($payment->type == 'stripe') {
            $stripe = $this->getStorePayment('stripe');
            $this->checkStripe($stripe, $order);
        }/* else if ($payment->type == 'twocheckout') {
            $post = $input->post->getArray([]);
            $price = $this->preparePrice($order->total, '', '.', 2);
            $price2 = $this->preparePrice($post['total'], '', '.', 2);
            if ($post['credit_card_processed'] == 'Y' && $price == $price2) {
                $this->approveOrder($id);
            } else {
                $this->approveOrder(0, null, false);
            }
        }*/
    }

    public function prepareLayer($order)
    {
        /*
        if (!gridboxHelper::$website->google_analytics || empty(gridboxHelper::$website->google_gtm_id)
            || !gridboxHelper::$website->ecommerce_tracking) {
            return;
        }
        */
        $layer = new stdClass();
        $layer->transactionId = $order->order_number;
        $layer->transactionAffiliation = $this->store->general->store_name;
        $layer->transactionTotal = $order->total;
        $layer->transactionTax = $order->tax;
        $layer->transactionShipping = isset($order->shipping) ? $order->shipping->price : 0;
        $layer->currency = $this->getDefaultCurrency();
        $layer->transactionCoupon = $order->promo ? $order->promo->code : '';
        $layer->transactionProducts = [];
        foreach ($order->products as $product) {
            $obj = new stdClass();
            $obj->product_id = $product->product_id;
            $obj->sku = $product->sku;
            $obj->name = $product->title;
            $obj->price = $product->price;
            $obj->quantity = $product->quantity;
            $layer->transactionProducts[] = $obj;
        }
        $json = json_encode($layer);
        $session = JFactory::getSession();
        $session->set('gridbox-store-layer', $json);
    }

    public function approveOrder($id, $params = null, $update = true, $cookie = true, $redirect = true, $exit = true)
    {
        if ($update) {
            $this->updateOrder($id, $params);
        }
        if ($cookie) {
            $time = time() - 604800;
            gridboxHelper::setcookie('gridbox_store_order', 0, $time);
            gridboxHelper::setcookie('gridbox_store_cart', 0, $time);
        }
        if (!empty($id)) {
            $payment = $this->getPayment($id);
            $order = $this->getOrder($id, true);
            $this->prepareLayer($order);
            $isDigital = true;
            $isBooking = false;
            foreach ($order->products as $product) {
                if ($product->product_type != 'digital' && $product->product_type != 'subscription') {
                    $isDigital = false;
                }
                if ($product->product_type == 'booking') {
                    $isBooking = true;
                }
            }
            if ($isBooking) {
                $this->setReadStatus($id);
                $this->prepareEmails($id);
                $this->sendStoreEmail('new-booking', $id);
                $this->clearPdfAttachment();
            }
            if ($isBooking || ($isDigital && $payment && $payment->type != 'offline')) {
                $this->updateStatus($id, 'completed');
            }
        }
        if ($redirect) {
            $url = gridboxHelper::getStoreSystemUrl('thank-you-page');
            header('Location: '.$url);
        }
        if ($exit) {
            exit;
        }
    }

    public function setReadStatus(int $id):void
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_orders')
            ->set('unread = 0')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function createAdminOrder(object $cart, int $user_id):object
    {
        $db = JFactory::getDbo();
        $isBooking = false;
        $order = new stdClass();
        $order->cart_id = 0;
        $order->user_id = $user_id;
        $order->subtotal = $cart->subtotal;
        $order->tax = $cart->tax;
        $order->tax_mode = $this->store->tax->mode;
        $order->total = $cart->total;
        $order->published = 1;
        $order->currency_symbol = $this->store->currency->symbol;
        $order->currency_position = $this->store->currency->position;
        $db->insertObject('#__gridbox_store_orders', $order);
        $order->id = $db->insertid();
        if (!empty($cart->discount)) {
            $discount = new stdClass();
            $discount->order_id = $order->id;
            $discount->promo_id = $cart->promo ? $cart->promo->id : 0;
            $discount->title = $cart->promo ? $cart->promo->title : '';
            $discount->code = $cart->promo ? $cart->promo->code : '';
            $discount->unit = $cart->promo ? $cart->promo->unit : '';
            $discount->discount = $cart->promo ? $cart->promo->discount : '';
            $discount->value = $cart->discount;
            $order->discount = $discount;
            $db->insertObject('#__gridbox_store_orders_discount', $discount);
        }
        if ($cart->shipping) {
            $shipping = new stdClass();
            $params = json_decode($cart->shipping->options);
            $shipping->type = $params->type;
            $shipping->order_id = $order->id;
            $shipping->title = $cart->shipping->title;
            $shipping->price = $cart->shipping->price;
            $shipping->tax = '';
            if ($cart->shipping->tax) {
                $shipping->tax = $cart->shipping->tax->amount;
                $shipping->tax_title = $cart->shipping->tax->title;
                $shipping->tax_rate = $cart->shipping->tax->rate;
            }
            $shipping->shipping_id = $cart->shipping->id;
            if (isset($cart->carrier)) {
                $shipping->carrier = $cart->carrier;
            }
            $db->insertObject('#__gridbox_store_orders_shipping', $shipping);
        }
        $payment = new stdClass();
        $payment->order_id = $order->id;
        $payment->title = '';
        $payment->type = 'admin';
        $payment->payment_id = 0;
        $db->insertObject('#__gridbox_store_orders_payment', $payment);
        foreach ($cart->products as $obj) {
            $product = $this->insertAdminProduct($obj, $order->id);
            if ($product->product_type == 'booking') {
                $isBooking = true;
            }
        }

        $this->insertAdminCustomerInfo($order->id, $cart->info);
        $this->updateOrder($order->id);
        if ($isBooking) {
            $this->setReadStatus($order->id);
            $this->updateStatus($order->id, 'completed');
        }

        return $order;
    }

    public function insertAdminCustomerInfo(int $order_id, object $info):void
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_customer_info')
            ->order('order_list ASC');
        $db->setQuery($query);
        $customer_info = $db->loadObjectList();
        foreach ($customer_info as $obj) {
            $customer = new stdClass();
            $customer->order_id = $order_id;
            $customer->customer_id = $obj->id;
            $customer->title = $obj->title;
            $customer->type = $obj->type;
            $customer->value = isset($info->{$obj->id}) ? $info->{$obj->id} : '';
            $customer->options = $obj->options;
            $customer->invoice = $obj->invoice;
            $customer->order_list = $obj->order_list;
            if ($obj->type == 'country' && !empty($customer->value)) {
                $customer->value = $this->setCountryValue($customer->value);
            }
            $db->insertObject('#__gridbox_store_order_customer_info', $customer);
        }
    }

    public function insertAdminProduct(object $obj, int $order_id):object
    {
        $db = JFactory::getDbo();
        $product = $this->getProductObject($obj, $order_id);
        $product = $this->insertProduct($product, $obj);
        foreach ($obj->variations as $object) {
            $variation = (object)[
                'product_id' => $product->id,
                'order_id' => $order_id,
                'title' => $object->title,
                'value' => $object->value,
                'color' => $object->color,
                'image' => $object->image,
                'type' => $object->type,
            ];
            $db->insertObject('#__gridbox_store_order_product_variations', $variation);
        }
        if ($product->product_type == 'booking') {
            $booking = (object)[
                'order_id' => $order_id,
                'product_id' => $product->id,
                'start_date' => $obj->booking->dates[0],
                'end_date' => $obj->booking->dates[1] ?? '',
                'start_time' => $obj->booking->time->start ?? '',
                'end_time' => $obj->booking->time->end ?? '',
                'guests' => $obj->booking->guests,
                'price' => $product->net_price,
                'unread' => 0,
                'later' => '',
                'prepaid' => '',
                'paid' => 0
            ];
            $db->insertObject('#__gridbox_store_bookings', $booking);
        }

        return $product;
    }

    public function insertProduct(object $product, object $obj):object
    {
        $db = JFactory::getDbo();
        $db->insertObject('#__gridbox_store_order_products', $product);
        $product->id = $db->insertid();
        if ($product->product_type == 'digital') {
            $product->product_token = hash('md5', date("Y-m-d H:i:s").'-'.$product->id);
            $db->updateObject('#__gridbox_store_order_products', $product, 'id');
            $digital = !empty($obj->data->digital_file) ? json_decode($obj->data->digital_file) : new stdClass();
            $license = new stdClass();
            $license->product_id = $product->id;
            $license->order_id = $product->order_id;
            $license->limit = isset($digital->max) ? $digital->max : '';
            $license->expires = 'new';
            $db->insertObject('#__gridbox_store_order_license', $license);
        } else if ($product->product_type == 'subscription') {
            $this->addSubscriptionProduct($product);
        }

        return $product;
    }

    public function getProductObject($obj, $order_id)
    {
        $extraPrice = isset($obj->extra_options->price) ? $obj->extra_options->price * $obj->quantity : 0;
        $product = new stdClass();
        $product->order_id = $order_id;
        $product->title = $obj->title;
        $product->image = $obj->image;
        $product->product_id = $obj->id;
        $product->variation = $obj->variation;
        $product->quantity = $obj->quantity;
        $product->price = $obj->price * $obj->quantity + $extraPrice;
        $product->sale_price = $obj->sale_price != '' ? $obj->sale_price * $obj->quantity + $extraPrice : '';
        $product->sku = $obj->sku;
        $product->tax = $obj->tax ? $obj->tax->amount : '';
        $product->tax_title = $obj->tax ? $obj->tax->title : '';
        $product->tax_rate = $obj->tax ? $obj->tax->rate : '';
        $product->net_price = $obj->net_price;
        $product->extra_options = json_encode($obj->extra_options);
        $product->product_type = $obj->product_type;
        $product->renew_id = isset($obj->renew_id) ? $obj->renew_id : 0;
        $product->plan_key = isset($obj->plan_key) ? $obj->plan_key : '';

        return $product;
    }

    public function setCountryValue($value)
    {
        $db = JFactory::getDbo();
        $obj = json_decode($value);
        if (!empty($obj->country)) {
            $query = $db->getQuery(true)
                ->select('title')
                ->from('#__gridbox_countries')
                ->where('id = '.$obj->country);
            $db->setQuery($query);
            $obj->country = $db->loadResult();
        }
        if (!empty($obj->country) && !empty($obj->region)) {
            $query = $db->getQuery(true)
                ->select('title')
                ->from('#__gridbox_country_states')
                ->where('id = '.$obj->region);
            $db->setQuery($query);
            $obj->region = $db->loadResult();
        }

        return json_encode($obj);
    }

    public function createOrderNumber($id)
    {
        $str = (string)$id;
        $len = strlen($str);
        $number = '#00000000';
        $i = $len >= 8 ? 1 : 9 - $len;

        return substr($number, 0, $i).$str;
    }

    public function updateOrder($id, $params = null)
    {
        $offset = $this->config->get('offset');
        $tz = new DateTimeZone($offset);
        $date = new DateTime('now', $tz);
        $db = JFactory::getDbo();
        $obj = $this->getOrder($id);
        $obj->date = $date->format('Y-m-d H:i:s');
        $obj->order_number = $this->createOrderNumber($id);
        $obj->published = 1;
        if ($params) {
            $obj->params = $params;
        }
        $db->updateObject('#__gridbox_store_orders', $obj, 'id');
        $this->updateOrderUsed($id);
        $this->prepareEmails($id);
        $this->sendStoreEmail('new', $id);
        $this->sendStockEmail();
        $this->clearPdfAttachment();
    }

    public function sendAppointmentReminder()
    {
        $appointment = null;
        $formats = ['h' => 'hour', 'd' => 'day'];
        $db = JFactory::getDbo();
        foreach ($this->store->notifications as $notification) {
            if ($notification->status == 'appointment-reminder') {
                $appointment = $notification;
                break;
            }
        }
        
        $date = JDate::getInstance('now');
        $date->modify('+' . $appointment->reminder->value . ' ' . $formats[$appointment->reminder->format]);
        $query = $db->getQuery(true)
            ->select('b.id, b.order_id')
            ->from('#__gridbox_store_orders AS o')
            ->leftJoin('#__gridbox_store_bookings AS b ON o.id = b.order_id')
            ->where('b.start_date <= '.$db->quote($date->format('Y-m-d')))
            ->where('b.reminded = 0');
        $db->setQuery($query);
        $appointments = $db->loadObjectList();
        foreach ($appointments as $obj) {
            $this->setAppointmentReminded($obj->id, $db);
            if (empty($appointment->email)) {
                continue;
            }
            $this->prepareEmails($obj->order_id);
            $this->sendStoreEmail('appointment-reminder', $obj->id);

        }
    }

    public function setAppointmentReminded($id, $db)
    {
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_bookings')
            ->set('reminded = 1')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function sendDelayEmail()
    {
        $db = JFactory::getDbo();
        $formats = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        foreach ($this->store->notifications as $notification) {
            if (!isset($notification->delay) || !$notification->delay->enabled) {
                continue;
            }
            $time = strtotime('-'.$notification->delay->value.' '.$formats[$notification->delay->format]);
            $date = $db->quote(date("Y-m-d H:i:s", $time));
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_email_delay')
                ->where('notification = '.$db->quote($notification->key))
                ->where('created < '.$date);
            $db->setQuery($query);
            $results = $db->loadObjectList();
            foreach ($results as $obj) {
                $this->prepareEmails($obj->order_id);
                $reply = null;
                if ($notification->type == 'customer') {
                    $sender = [$notification->email, $notification->name];
                    $recipients = [$this->email];
                    $reply = $notification->email;
                } else {
                    $recipients = $notification->admins;
                    $sender = [$this->config->get('mailfrom'), $this->config->get('fromname')];
                }
                $this->sendEmail($sender, $notification->subject, $recipients, $notification->body, $reply);
                $this->clearPdfAttachment();
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_email_delay')
                    ->where('id = '.$obj->id);
                $db->setQuery($query)
                    ->execute();
            }
        }
    }

    public function getBackgroundRequests():string
    {
        $requests = [];
        if (JFactory::getApplication()->isClient('administrator')) {
            $requests[] = 'comments.sendCommentsEmails';
            $requests[] = 'reviews.sendCommentsEmails';
            $requests[] = 'editor.checkSitemap';
        }
        if (!empty($this->reminder->email)) {
            $requests[] = 'store.sendReminder';
        }
        if ($this->checkPendingPayments()) {
            $requests[] = 'store.pendingPayments';
        }
        if ($this->checkDelayEmails()) {
            $requests[] = 'store.sendDelayEmails';
        }
        if ($this->checkAppointmentReminder()) {
            $requests[] = 'store.sendAppointmentReminder';
        }
        if (empty($requests)) {
            return '';
        }
        $data = json_encode($requests);
        $html = <<<HTML
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                $data.forEach((request) => {
                    app.fetch(JUri+'index.php?option=com_gridbox&task='+request);
                })
            });
        </script>
        HTML;

        return $html;
    }

    public function checkAppointmentReminder():bool
    {
        $appointment = null;
        $formats = ['h' => 'hour', 'd' => 'day'];
        foreach ($this->store->notifications as $notification) {
            if ($notification->status == 'appointment-reminder') {
                $appointment = $notification;
                break;
            }
        }
        /*if (empty($appointment->email)) {
            return false;
        }*/
        $date = JDate::getInstance('now');
        $date->modify('+' . $appointment->reminder->value . ' ' . $formats[$appointment->reminder->format]);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(o.id)')
            ->from('#__gridbox_store_orders AS o')
            ->leftJoin('#__gridbox_store_bookings AS b ON o.id = b.order_id')
            ->where('b.start_date <= '.$db->quote($date->format('Y-m-d')))
            ->where('b.reminded = 0');
        $db->setQuery($query);

        return $db->loadResult() > 0;
    }

    public function checkDelayEmails()
    {
        $flag = false;
        $db = JFactory::getDbo();
        $formats = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        foreach ($this->store->notifications as $notification) {
            if ((!isset($notification->delay) || !$notification->delay->enabled)
                || (isset($notification->email) && empty($notification->email))
                || (isset($notification->admins) && empty($notification->admins))) {
                continue;
            }
            $time = strtotime('-'.$notification->delay->value.' '.$formats[$notification->delay->format]);
            $date = $db->quote(date("Y-m-d H:i:s", $time));
            $query = $db->getQuery(true)
                ->select('COUNT(id)')
                ->from('#__gridbox_email_delay')
                ->where('created < '.$date);
            $db->setQuery($query);
            $result = $db->loadResult();
            if ($flag = $result > 0) {
                break;
            }
        }

        return $flag;
    }

    protected function setDelayEmail($notification, $id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->order_id = $id;
        $obj->notification = $notification->key;
        $obj->created = date("Y-m-d H:i:s");
        $db->insertObject('#__gridbox_email_delay', $obj);
    }

    protected function sendStoreEmail($status, $id)
    {
        foreach ($this->store->notifications as $notification) {
            if ($notification->status != $status || (isset($notification->admins) && empty($notification->admins))
                 || (isset($notification->email) && (empty($notification->email) || empty($this->email)))) {
                continue;
            }
            if (isset($notification->delay) && $notification->delay->enabled) {
                $this->setDelayEmail($notification, $id);
                continue;
            }
            $reply = null;
            if ($notification->type == 'customer') {
                $sender = [$notification->email, $notification->name];
                $recipients = [$this->email];
                $reply = $notification->email;
            } else {
                $recipients = $notification->admins;
                $sender = [$this->config->get('mailfrom'), $this->config->get('fromname')];
            }
            $this->sendEmail($sender, $notification->subject, $recipients, $notification->body, $reply);
            $this->clearPdfAttachment();
        }
    }

    protected function sendStockEmail()
    {
        if (empty($this->stock->admins)) {
            return;
        }
        $recipients = $this->stock->admins;
        $sender = [$this->config->get('mailfrom'), $this->config->get('fromname')];
        foreach ($this->outStock as $product) {
            $this->tags->{'[Product Title]'} = $product->title;
            $this->tags->{'[Product SKU]'} = $product->sku;
            $this->tags->{'[Product Quantity]'} = $product->stock;
            $this->sendEmail($sender, $this->stock->subject, $recipients, $this->stock->body);
        }
    }

    protected function prepareEmails($id)
    {
        JFactory::getLanguage()->load('com_gridbox');
        $this->outStock = [];
        $order = $this->getOrder($id, true);
        $db = JFactory::getDbo();
        $products = $order->products;
        $title = [];
        $sku = [];
        $quantity = [];
        $currency = $this->getDefaultCurrency();
        $weight = 0;
        $bookingTime = '';
        foreach ($products as $product) {
            if ($product->product_type == 'digital' && $order->status == 'completed') {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_order_license')
                    ->where('product_id = '.$product->id);
                $db->setQuery($query);
                $license = $db->loadObject();
                if ($license->expires == 'new') {
                    $query = $db->getQuery(true)
                        ->select('d.digital_file')
                        ->from('#__gridbox_store_order_products AS op')
                        ->where('op.id = '.$product->id)
                        ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = op.product_id');
                    $db->setQuery($query);
                    $digital_file = $db->loadResult();
                    $digital = !empty($digital_file) ? json_decode($digital_file) : new stdClass();
                    if (empty($digital->expires->value)) {
                        $license->expires = '';
                    } else {
                        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
                        $time = strtotime('+'.$digital->expires->value.' '.$expires[$digital->expires->format]);
                        $license->expires = date("Y-m-d H:i:s", $time);
                    }
                    $db->updateObject('#__gridbox_store_order_license', $license, 'id');
                }
            } else if ($product->product_type == 'booking') {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_bookings')
                    ->where('product_id = '.$product->id);
                $db->setQuery($query);
                $product->booking = $db->loadObject();
                $bookingTime = gridboxHelper::formatDate($product->booking->start_date) . ' ' .
                (!empty($product->booking->end_date) ? '- ' . gridboxHelper::formatDate($product->booking->end_date)
                    : (!empty($product->booking->start_time) ? $product->booking->start_time : ''));
            }
            $query = $db->getQuery(true)
                ->select('app_id, page_category')
                ->from('#__gridbox_pages')
                ->where('id = '.$product->product_id);
            $db->setQuery($query);
            $page = $db->loadObject();
            if ($page) {
                $product->link = JUri::root().'index.php/productID-'.$product->product_id;
            }
            $query = $db->getQuery(true)
                ->select('variations, stock, sku, dimensions')
                ->from('#__gridbox_store_product_data')
                ->where('product_id = '.$product->product_id);
            $db->setQuery($query);
            $result = $db->loadObject();
            if (!$result) {
                continue;
            }
            if (!empty($product->variation)) {
                $variations = json_decode($result->variations);
                if (isset($variations->{$product->variation})) {
                    $result->stock = $variations->{$product->variation}->stock;
                    $result->sku = $variations->{$product->variation}->sku;
                } else {
                    continue;
                }
            }
            if (!empty($result->dimensions)) {
                $dimensions = json_decode($result->dimensions);
                $weight += floatval($dimensions->weight ?? 0) * $product->quantity;
            } else if (!empty($result->weight)) {
                $weight += $result->weight * $product->quantity;
            }
            if (!empty($product->extra_options)) {
                $extra_options = json_decode($product->extra_options);
            } else {
                $extra_options = new stdClass();
            }
            if (isset($extra_options->items)) {
                foreach ($extra_options->items as $extra_item) {
                    if (!empty($extra_item->weight)) {
                        $weight += $extra_item->weight * $product->quantity;
                    }
                }
            }
            $title[] = $product->title;
            $sku[] = $product->sku;
            $quantity[] = $product->quantity;
            if ($result->stock !== '' && $result->stock * 1 <= $this->stock->quantity * 1) {
                $product->stock = $result->stock;
                $product->sku = $result->sku;
                $this->outStock[] = $product;
            }
        }
        $shipping = $order->shipping;
        $discount = $order->promo;
        $information = $order->info;
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_payment')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $payment = $db->loadObject();
        $tracking = $this->getTracking($id);
        $general = $this->store->general;
        $tags = new stdClass();
        $tags->id = $id;
        $tags->{'[Order Weight]'} = $weight;
        $tags->{'[Product Title]'} = implode(', ', $title);
        $tags->{'[Product SKU]'} = implode(', ', $sku);
        $tags->{'[Product Quantity]'} = implode(', ', $quantity);
        $tags->{'[Store Name]'} = $general->store_name;
        $tags->{'[Store Legal Business Name]'} = $general->business_name;
        $tags->{'[Store Phone]'} = $general->phone;
        $tags->{'[Store Email]'} = $general->email;
        $tags->{'[Tracking Number]'} = $tracking->number;
        $tags->{'[Tracking URL]'} = '<a href="'.$tracking->url.'">'.JText::_('VIEW').'</a>';
        $tags->{'[Tracking Carrier]'} = $tracking->title;
        $tags->{'[Booking Time]'} = $bookingTime;
        $address = [];
        if (!empty($general->country)) {
            $address[] = $general->country;
        }
        if (!empty($general->region)) {
            $address[] = $general->region;
        }
        if (!empty($general->city)) {
            $address[] = $general->city;
        }
        if (!empty($general->street)) {
            $address[] = $general->street;
        }
        if (!empty($general->zip_code)) {
            $address[] = $general->zip_code;
        }
        $tags->{'[Store Address]'} = implode(', ', $address);
        $tags->{'[Order Number]'} = $order->order_number;
        $tags->{'[Order Date]'} = JDate::getInstance($order->date)->format(gridboxHelper::$website->date_format);
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/email-store-order-details.php';
        $tags->{'[Order Details]'} = $out;
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/email-order-review.php';
        $tags->{'[Order Review]'} = $out;
        foreach ($information as $info) {
            $tags->{'[Customer ID='.$info->customer_id.']'} = $info->value;
            if ($info->type == 'email') {
                $this->email = $info->value;
            } else if ($info->type == 'country') {
                $object = json_decode($info->value);
                $values = [];
                if (!empty($object->region)) {
                    $values[] = $object->region;
                }
                $values[] = $object->country;
                $value = implode(', ', $values);
                $tags->{'[Customer ID='.$info->customer_id.']'} = $value;
            }
        }
        $tags->{'[Attached Files]'} = [];
        foreach ($order->attachments as $attachment) {
            $tags->{'[Attached Files]'}[] = JPATH_ROOT.'/'.$this->attachments.'/'.$attachment->filename;
        }
        $this->tags = $tags;
    }

    protected function sendEmail($sender, $subject, $recipients, $body, $reply = null)
    {
        $tags = $this->tags;
        $subject = $this->replaceStoreDataTags($tags, $subject);
        $body = $this->replaceStoreDataTags($tags, $body);
        $files = [];
        if (!empty($this->files)) {
            $files = array_merge($files, $this->files);
        }
        if (!empty($this->pdf)) {
            $files[] = $this->pdf;
        }
        try {
            $mailer = JFactory::getMailer();
            $mailer->sendMail($sender[0], $sender[1], $recipients, $subject, $body, true, null, null, $files, $reply);
        } catch (Exception $e) {}
    }

    protected function createPdf($id)
    {
        if (empty($this->pdf)) {
            $config = JFactory::getConfig();
            $path = $config->get('tmp_path').'/';
            $order = $this->getOrder($id, true);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_customer_info')
                ->order('order_list ASC');
            $db->setQuery($query);
            $info = $db->loadObjectList();
            $obj = new stdClass();
            $empty = new stdClass();
            $empty->title = '';
            $empty->items = [];
            $array = [];
            $object = null;
            foreach ($order->products as $product) {
                $product->extra_options = json_decode(!empty($product->extra_options) ? $product->extra_options : '{}');
                if (isset($product->extra_options->price)) {
                    $product->price -= $product->extra_options->price * $product->quantity;
                }
                if ($product->sale_price && isset($product->extra_options->price)) {
                    $product->sale_price -= $product->extra_options->price * $product->quantity;
                }
            }
            foreach ($order->info as $value) {
                $value->empty = true;
                $obj->{$value->customer_id} = $value;
            }
            foreach ($info as $value) {
                if (!$object || $value->type == 'headline') {
                    $array[] = new stdClass();
                    $object = end($array);
                    $object->title = $value->type == 'headline' && $value->invoice == 1 ? $value->title : '';
                    $object->items = [];
                }
                if ($value->type != 'headline' && $value->type != 'acceptance' && isset($obj->{$value->id})) {
                    $obj->{$value->id}->empty = false;
                    $obj->{$value->id}->invoice = $value->invoice;
                    if ($obj->{$value->id}->value === '' || $obj->{$value->id}->invoice == 0) {
                        continue;
                    }
                    $object->items[] = $obj->{$value->id};
                }
            }
            foreach ($obj as $value) {
                if ($value->empty && $value->type != 'headline' && $value->type != 'acceptance') {
                    if ($value->value === '' || $value->invoice == 0) {
                        continue;
                    }
                    $empty->items[] = $value;
                }
            }
            $array[] = $empty;
            $order->contact_info = $array;
            include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/tfpdf/pdf.php';
            $pdf = new pdf('Portrait', 'mm', 'A4');
            $pdf->store = $this->store;
            $this->pdf = $pdf->create($order, $this->store->general, 'F', $path);
        }
    }

    protected function clearPdfAttachment()
    {
        if (!empty($this->pdf)) {
            unlink($this->pdf);
            $this->pdf = null;
        }
    }

    public function addSubscriptionProduct($product)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('d.subscription, o.status, o.user_id, o.date')
            ->from('#__gridbox_store_order_products AS p')
            ->where('p.product_type = '.$db->quote('subscription'))
            ->where('p.id = '.$product->id)
            ->where('p.order_id = '.$product->order_id)
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = p.order_id');
        $db->setQuery($query);
        $object = $db->loadObject();
        if ($object->status != 'completed') {
            return;
        }
        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        $obj = !empty($object->subscription) ? json_decode($object->subscription) : new stdClass();
        $value = isset($obj->length) ? $obj->length->value : '';
        $format = isset($obj->length) ? $obj->length->format : 'h';
        $action = isset($obj->action) ? $obj->action : '';
        $groups = isset($obj->groups) ? $obj->groups : [];
        $created = strtotime($object->date);
        if (empty($value)) {
            $time = '';
        } else {
            $time = strtotime('+'.$value.' '.$expires[$format], $created);
        }
        $subscription = new stdClass();
        $subscription->user_id = $object->user_id;
        $subscription->product_id = $product->product_id;
        $subscription->date = $object->date;
        $subscription->expires = empty($time) ? '' : date("Y-m-d H:i:s", $time);
        $subscription->action = $action;
        $subscription->user_groups = json_encode($groups);
        $db->insertObject('#__gridbox_store_subscriptions', $subscription);
        $map = new stdClass();
        $map->product_id = $product->id;
        $map->start_date = $object->date;
        $map->expires = $subscription->expires;
        $map->subscription_id = $db->insertid();
        $db->insertObject('#__gridbox_store_subscriptions_map', $map);
    }

    public function removeSubscriptionProducts($pks, $order_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('p.id, p.product_id, d.subscription, p.quantity, o.status')
            ->from('#__gridbox_store_order_products AS p')
            ->where('p.product_type = '.$db->quote('subscription'))
            ->where('p.order_id = '.$order_id)
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = p.order_id');
        if (!empty($pks)) {
            $query->where('p.id NOT IN ('.implode(', ', $pks).')');
        }
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        $now = time();
        foreach ($products as $product) {
            $obj = !empty($product->subscription) ? json_decode($product->subscription) : new stdClass();
            $value = isset($obj->length) ? $obj->length->value : '';
            $format = isset($obj->length) ? $obj->length->format : 'h';
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_subscriptions_map')
                ->where('product_id = '.$product->id);
            $db->setQuery($query);
            $maps = $db->loadObjectList();
            foreach ($maps as $map) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_subscriptions')
                    ->where('id = '.$map->subscription_id);
                $db->setQuery($query);
                $subscription = $db->loadObject();
                $expired = !empty($map->expires) ? strtotime($map->expires) : '';
                if (!empty($expired) && $expired > $now && $map->last_status != 'refunded') {
                    $start = strtotime($map->start_date);
                    $delta = $expired - ($start > $now ? $start : $now);
                    $expired = strtotime($subscription->expires) - $delta;
                    $subscription->expires = date("Y-m-d H:i:s", $expired);
                    $db->updateObject('#__gridbox_store_subscriptions', $subscription, 'id');
                }
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_store_subscriptions_map')
                    ->where('id = '.$map->id);
                $db->setQuery($query)
                    ->execute();
                $query = $db->getQuery(true)
                    ->select('COUNT(id)')
                    ->from('#__gridbox_store_subscriptions_map')
                    ->where('subscription_id = '.$subscription->id);
                $db->setQuery($query);
                $c = $db->loadResult();
                if (empty($c)) {
                    if ($subscription->action != 'products' && $product->status == 'completed') {
                        $groups = json_decode($subscription->user_groups);
                        foreach ($groups as $group) {
                            JUserHelper::removeUserFromGroup($subscription->user_id, $group);
                        }
                    }
                    $query = $db->getQuery(true)
                        ->delete('#__gridbox_store_subscriptions')
                        ->where('id = '.$subscription->id);
                    $db->setQuery($query)
                        ->execute();
                }
            }
        }
    }

    public function checkSubscription($order_id, $status)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('user_id, date')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$order_id);
        $db->setQuery($query);
        $order = $db->loadObject();
        if (empty($order->user_id)) {
            return;
        }
        $query = $db->getQuery(true)
            ->select('p.id, p.product_id, d.subscription, p.renew_id, p.upgrade_id, p.plan_key')
            ->from('#__gridbox_store_order_products AS p')
            ->where('p.order_id = '.$order_id)
            ->where('p.product_type = '.$db->quote('subscription'))
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.product_id');
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        $now = time();
        $created = strtotime($order->date);
        foreach ($products as $product) {
            $obj = !empty($product->subscription) ? json_decode($product->subscription) : new stdClass();
            $value = isset($obj->length) ? $obj->length->value : '';
            $format = isset($obj->length) ? $obj->length->format : 'h';
            $action = isset($obj->action) ? $obj->action : '';
            $groups = isset($obj->groups) ? $obj->groups : [];
            if (!empty($product->renew_id) && isset($obj->renew->plans->{$product->plan_key})) {
                $value = $obj->renew->plans->{$product->plan_key}->length->value;
                $format = $obj->renew->plans->{$product->plan_key}->length->format;
            }
            if (empty($value)) {
                $time = '';
            } else {
                $time = strtotime('+'.$value.' '.$expires[$format], $created);
            }
            $query = $db->getQuery(true)
                ->select('s.*')
                ->from('#__gridbox_store_subscriptions AS s')
                ->leftJoin('#__gridbox_store_subscriptions_map AS m ON m.subscription_id = s.id');
            if (empty($product->renew_id)) {
                $query->where('m.product_id = '.$product->id)
                    ->where('s.product_id = '.$product->product_id);
            } else {
                $query->where('s.id = '.$product->renew_id);
            }
            $db->setQuery($query);
            $subscription = $db->loadObject();
            if ($subscription) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_subscriptions_map')
                    ->where('subscription_id = '.$subscription->id)
                    ->where('product_id = '.$product->id);
                $db->setQuery($query);
                $map = $db->loadObject();
            } else {
                $map = null;
            }
            if (!empty($product->upgrade_id) && $status == 'completed' && !$map) {
                $this->removeSubscription($product->upgrade_id);
            }
            if (!empty($product->renew_id) && $status == 'completed' && !$map) {
                $expired = strtotime($subscription->expires);
                $start_date = $expired > $now ? $expired : $now;
                $expired = strtotime('+'.$value.' '.$expires[$format], $start_date);
                $map = new stdClass();
                $map->product_id = $product->id;
                $map->start_date = date("Y-m-d H:i:s", $start_date);
                $map->expires = date("Y-m-d H:i:s", $expired);
                $map->subscription_id = $subscription->id;
                $db->insertObject('#__gridbox_store_subscriptions_map', $map);
                $map->id = $db->insertid();
                $subscription->expires = date("Y-m-d H:i:s", $expired);
                $subscription->reminded = 0;
                $db->updateObject('#__gridbox_store_subscriptions', $subscription, 'id');
            } else if (!$subscription && $status == 'completed') {
                $subscription = new stdClass();
                $subscription->user_id = $order->user_id;
                $subscription->product_id = $product->product_id;
                $subscription->date = $order->date;
                $subscription->expires = empty($time) ? '' : date("Y-m-d H:i:s", $time);
                $subscription->action = $action;
                $subscription->user_groups = json_encode($groups);
                $db->insertObject('#__gridbox_store_subscriptions', $subscription);
                $map = new stdClass();
                $map->product_id = $product->id;
                $map->start_date = $order->date;
                $map->expires = $subscription->expires;
                $map->subscription_id = $db->insertid();
                $db->insertObject('#__gridbox_store_subscriptions_map', $map);
                $map->id = $db->insertid();
            } else if ($subscription && $status != 'completed' && !empty($map->expires)) {
                $expired = strtotime($map->expires);
                if ($expired > $now && $map->last_status != $status) {
                    $start = strtotime($map->start_date);
                    $delta = $expired - ($start > $now ? $start : $now);
                    $expired = strtotime($subscription->expires) - $delta;
                    $subscription->expires = date("Y-m-d H:i:s", $expired);
                    $db->updateObject('#__gridbox_store_subscriptions', $subscription, 'id');
                    $map->last_status = $status;
                    $db->updateObject('#__gridbox_store_subscriptions_map', $map, 'id');
                }
            } else if ($subscription && $status != 'completed') {
                $this->removeSubscription($subscription->id, $subscription);
            } else if ($subscription && $status == 'completed') {
                $expired = strtotime($map->expires);
                if ($expired > $now && $map->last_status != $status) {
                    $start = strtotime($map->start_date);
                    $delta = $expired - ($start > $now ? $start : $now);
                    $expired = strtotime($subscription->expires) + $delta;
                    $subscription->expires = date("Y-m-d H:i:s", $expired);
                    $db->updateObject('#__gridbox_store_subscriptions', $subscription, 'id');
                    $map->last_status = $status;
                    $db->updateObject('#__gridbox_store_subscriptions_map', $map, 'id');
                }
            }
        }
    }

    public function removeSubscription($id, $subscription = null)
    {
        if (!$subscription) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_subscriptions')
                ->where('id = '.$id);
            $db->setQuery($query);
            $subscription = $db->loadObject();
        }
        if (!$subscription) {
            return;
        }
        if ($subscription->action != 'products') {
            $groups = json_decode($subscription->user_groups);
            foreach ($groups as $group) {
                JUserHelper::removeUserFromGroup($subscription->user_id, $group);
            }
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_subscriptions')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_subscriptions_map')
            ->where('subscription_id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function subscriptionFreeUpgrade($object, $upgrade_id)
    {
        $db = JFactory::getDbo();
        $subscription = new stdClass();
        $subscription->user_id = JFactory::getUser()->id;
        $subscription->product_id = $object->id;
        $subscription->date = $object->date;
        $subscription->expires = $object->expires;
        $subscription->action = $object->action;
        $subscription->user_groups = json_encode($object->groups);
        $db->insertObject('#__gridbox_store_subscriptions', $subscription);
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_subscriptions')
            ->where('id = '.$upgrade_id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_subscriptions_map')
            ->where('subscription_id = '.$upgrade_id);
        $db->setQuery($query)
            ->execute();
    }

    public function updateStatus($id, $status, $comment = null)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('status')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$id);
        $db->setQuery($query);
        $old = $db->loadResult();
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_orders')
            ->set('status = '.$db->quote($status))
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        if ($status == 'completed' && $old != 'completed') {
            $this->checkSubscription($id, $status);
        } else if ($old == 'refunded' && $status != 'refunded') {
            $this->updateOrderUsed($id);
            $this->prepareEmails($id);
            $this->sendStockEmail();
        } else if ($old != 'refunded' && $status == 'refunded') {
            $this->checkSubscription($id, $status);
            $this->updateOrderUsed($id, '-');
        }
        if ($old != $status) {
            $this->prepareEmails($id);
            $this->sendStoreEmail($status, $id);
        }
        if ($comment !== null) {
            $obj = new stdClass();
            $obj->date = JDate::getInstance()->format('Y-m-d H:i:s');
            $obj->status = $status;
            $obj->comment = $comment;
            $obj->order_id = $id;
            $obj->user_id = JFactory::getUser()->id;
            $db->insertObject('#__gridbox_store_orders_status_history', $obj);
        }
    }

    protected function updateOrderUsed($id, $action = '+')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('promo_id')
            ->from('#__gridbox_store_orders_discount')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $promo_id = $db->loadResult();
        if (!empty($promo_id)) {
            $query = $db->getQuery(true)
                ->update('#__gridbox_store_promo_codes')
                ->set('`used` = `used` '.$action.' 1')
                ->where('`id` = '.$promo_id);
            $db->setQuery($query)
                ->execute();
        }
        $query = $db->getQuery(true)
            ->select('d.id, d.stock, o.variation, d.variations, o.quantity')
            ->from('#__gridbox_store_order_products AS o')
            ->where('o.order_id = '.$id)
            ->leftJoin('#__gridbox_store_product_data AS d ON o.product_id = d.product_id');
        $db->setQuery($query);
        $products = $db->loadObjectList();
        foreach ($products as $product) {
            $this->updateProductUsed($product, $action);
        }
    }

    public function updateProductUsed($product, $action = '+')
    {
        $db = JFactory::getDbo();
        $variations = json_decode($product->variations);
        $q = $product->quantity * ($action == '+' ? -1 : 1);
        if (empty($product->variation) && $product->stock !== '') {
            $product->stock = $product->stock * 1 + $q;
        } else if (!empty($product->variation) && isset($variations->{$product->variation})
            && $variations->{$product->variation}->stock !== '') {
            $variation = $variations->{$product->variation};
            $variation->stock = $variation->stock * 1  + $q;
        } else {
            return;
        }
        $obj = new stdClass();
        $obj->id = $product->id;
        $obj->variations = json_encode($variations);
        $obj->stock = $product->stock;
        $db->updateObject('#__gridbox_store_product_data', $obj, 'id');
    }

    protected function replaceStoreDataTags($tags, $text)
    {
        if (strpos($text, '[Invoice: Attached]') !== false) {
            $text = str_replace('[Invoice: Attached]', '', $text);
            $this->createPdf($tags->id);
        }
        if (strpos($text, '[Attached Files]') !== false) {
            $text = str_replace('[Attached Files]', '', $text);
            $this->files = $tags->{'[Attached Files]'};
        }
        foreach ($tags as $tag => $value) {
            if ($tag == 'id' || $tag == '[Attached Files]') {
                continue;
            }
            $text = str_replace($tag, $value, $text);
        }
        $text = preg_replace('/\[Customer ID=\d+\]/', '', $text);

        return $text;
    }

    public function preparePrice($price, $thousand, $separator, $decimals)
    {
        $price = round($price * 1, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);

        return $price;
    }

    public function getProductData($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('d.*, p.intro_image AS image, p.title, p.app_id, p.page_category, a.type AS app_type')
            ->from('#__gridbox_store_product_data AS d')
            ->where('d.product_id = '.$id)
            ->leftJoin('#__gridbox_pages AS p ON d.product_id = p.id')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
        $db->setQuery($query);
        $data = $db->loadObject();
        $data->variations = json_decode($data->variations);
        $data->extra_options = $this->getProductExtraOptions($data->extra_options);
        $data->dimensions = !empty($data->dimensions) ? json_decode($data->dimensions) : new stdClass();
        $data->booking = !empty($data->booking) ? json_decode($data->booking) : new stdClass();

        return $data;
    }

    public function getProductExtraOptions($options)
    {
        $options = !empty($options) ? $options : '{}';
        $options = json_decode($options);
        $db = JFactory::getDbo();
        $extra_options = new stdClass();
        foreach ($options as $id => $option) {
            $query =  $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_products_fields')
                ->where('id = '.$option->id);
            $db->setQuery($query);
            $field = $db->loadObject();
            if (!$field) {
                continue;
            }
            $obj = new stdClass();
            $obj->title = $field->title;
            $obj->type = $field->field_type;
            $obj->required = $field->required;
            $obj->items = new stdClass();
            if ($field->field_type == 'file') {
                $obj->items = $option->items;
                $obj->file_options = json_decode($field->file_options);
            } else if ($field->field_type == 'textarea' || $field->field_type == 'textinput') {
                $obj->items = $option->items;
            } else {
                $items = json_decode($field->options);
                foreach ($items as $key => $item) {
                    if (isset($option->items->{$item->key})) {
                        $object = $option->items->{$item->key};
                        $object->title = $item->title;
                        $item->price = $object->price;
                        $item->weight = isset($object->weight) ? $object->weight : '';
                        $item->default = $object->default;
                        $obj->items->{$item->key} = $item;
                    }
                }
            }
            $extra_options->{$field->id} = $obj;
        }

        return $extra_options;
    }

    public function getProductVariationsMap($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('vm.*, fd.value, fd.color, fd.image, f.title, f.field_type, f.field_key')
            ->from('#__gridbox_store_product_variations_map AS vm')
            ->where('vm.product_id = '.$id)
            ->order('vm.order_group ASC, vm.order_list ASC')
            ->leftJoin('#__gridbox_store_products_fields_data AS fd ON fd.option_key = vm.option_key')
            ->leftJoin('#__gridbox_store_products_fields AS f ON f.id = vm.field_id');
        $db->setQuery($query);
        $variations_map = $db->loadObjectList();
        
        return $variations_map;
    }

    public function getProductBadges($id, $data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('b.*')
            ->from('#__gridbox_store_badges_map AS bm')
            ->where('bm.product_id = '.$id)
            ->order('bm.order_list ASC')
            ->leftJoin('#__gridbox_store_badges AS b ON b.id = bm.badge_id');
        $db->setQuery($query);
        $badges = $db->loadObjectList();
        foreach ($badges as $badge) {
            if ($badge->type == 'sale') {
                $price = $data->price;
                $sale = $data->sale_price;
                $badge->title = '- '.($price == 0 ? 0 : round(100 - (($sale === '' ? $price : $sale) * 100 / $price))).'%';
            }
        }
        
        return $badges;
    }

    public function getPreparedProductData($id)
    {
        $db = JFactory::getDbo();
        $product = new stdClass();
        $product->data = $this->getProductData($id);
        $product->variations_map = $this->getProductVariationsMap($id);
        $product->fields = new stdClass();
        $product->fields_data = new stdClass();
        foreach ($product->variations_map as $variation) {
            if (!isset($product->fields->{$variation->field_id})) {
                $product->fields->{$variation->field_id} = new stdClass();
                $product->fields->{$variation->field_id}->title = $variation->title;
                $product->fields->{$variation->field_id}->map = [];
                $product->fields->{$variation->field_id}->type = $variation->field_type;
            }
            $product->fields->{$variation->field_id}->map[] = $variation;
            $product->fields_data->{$variation->option_key} = $variation->value;
        }
        $product->badges = $this->getProductBadges($id, $product->data);
        $query = $db->getQuery(true)
            ->select('p.id, p.title, p.intro_image AS image')
            ->from('#__gridbox_pages AS p')
            ->where('r.product_id = '.$id)
            ->leftJoin('#__gridbox_store_related_products AS r ON r.related_id = p.id')
            ->order('r.order_list ASC');
        $db->setQuery($query);
        $product->related = $db->loadObjectList();
        foreach ($product->related as $value) {
            $value->image = (!empty($value->image) && !gridboxHelper::isExternal($value->image) ? JUri::root() : '')
                .$value->image;
        }
        $product->relatedFlag = false;
        $query = $db->getQuery(true)
            ->select('a.page_items')
            ->from('#__gridbox_app AS a')
            ->where('p.id = '.$id)
            ->leftJoin('#__gridbox_pages AS p ON p.app_id = a.id');
        $db->setQuery($query);
        $page_items = $db->loadResult();
        if ($page_items) {
            $obj = json_decode($page_items);
            foreach ($obj as $value) {
                if (($value->type == 'related-posts' || $value->type == 'related-posts-slider')
                    && $value->related == 'custom') {
                    $product->relatedFlag = true;
                    break;
                }
            }
        }
        
        return $product;
    }

    public function getDigitalFolder($id)
    {
        $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/digital/';
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }
        $folder = hash('md5', 'product-'.$id);
        $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/digital/'.$folder.'/';

        return $dir;
    }

    public function calculateSubscriptionTotal($id)
    {
        $db = JFactory::getDbo();
        $date = date("Y-m-d H:i:s");
        $query = $db->getQuery(true)
            ->select('m.*, p.price, p.sale_price, p.upgrade_price')
            ->from('#__gridbox_store_subscriptions_map AS m')
            ->where('m.last_status = '.$db->quote('completed'))
            ->where('m.expires > '.$db->quote($date))
            ->where('m.subscription_id = '.$id)
            ->leftJoin('#__gridbox_store_order_products AS p ON m.product_id = p.id');
        $db->setQuery($query);
        $maps = $db->loadObjectList();
        $total = 0;
        $seconds = 60 * 60 * 24;
        foreach ($maps as $map) {
            $price = !empty($map->sale_price) ? $map->sale_price : $map->price;
            $price = $price * 1 + $map->upgrade_price * 1;
            $time1 = strtotime($map->start_date);
            $time2 = strtotime($map->expires);
            $days = ceil(($time2 - $time1) / $seconds);
            $dayPrice = $price / $days;
            if ($date > $map->start_date) {
                $time1 = strtotime($map->start_date);
                $time2 = strtotime($date);
                $remaining = ceil(($time2 - $time1) / $seconds);
                $price = ($days - $remaining) * $dayPrice;
            }
            $total += $price;
        }

        return $total;
    }

    public function getUpgradeObject($plan, $total)
    {
        $expires = ['h' => 'hour', 'd' => 'day', 'm' => 'month', 'y' => 'year'];
        $seconds = 60 * 60 * 24;
        $prices = gridboxHelper::prepareProductPrices($plan->id, $plan->price, $plan->sale_price);
        $price = !empty($prices->sale_price) ? $prices->sale_price : $prices->price;
        $object = new stdClass();
        $object->id = $plan->id;
        $object->title = $plan->title;
        $object->price = $price - $total;
        $object->prices = gridboxHelper::prepareProductPrices($plan->id, $object->price, '');
        if ($object->price < 0) {
            $obj = json_decode($plan->subscription);
            $object->action = $obj->action;
            $object->groups = $obj->groups;
            $object->date = date("Y-m-d H:i:s");
            $object->additional = $object->expires = '';
            $value = $obj->length->value;
            if (!empty($value)) {
                $format = $obj->length->format;
                $time1 = time();
                $time2 = strtotime('+'.$value.' '.$expires[$format]);
                $delta = $time2 - $time1;
                $days = ceil($delta / $seconds);
                $object->expires = date("Y-m-d H:i:s", $time2 + $delta);
                $dayPrice = $price / $days;
                $object->additional = ceil($object->price / $dayPrice * -1);
            }
        }

        return $object;
    }

    public function getSubscriprionsQuery($user_id = 0)
    {
        $db = JFactory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('s.*, d.subscription, p.title')
            ->from('#__gridbox_store_subscriptions AS s')
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = s.product_id')
            ->leftJoin('#__gridbox_pages AS p ON p.id = s.product_id')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')');
        if (!empty($user_id)) {
            $query->where('s.user_id = '.$user_id);
        }

        return $query;
    }

    public function getCartUpgrade($upgrade_id, $id, $data)
    {
        $total = 0;
        $db = JFactory::getDbo();
        $query = $this->getSubscriprionsQuery()
            ->where('s.id = '.$upgrade_id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!$item) {
            return null;
        }
        $subscription = json_decode($item->subscription);
        if (!in_array($id, $subscription->upgrade)) {
            return null;
        }
        $total = $this->calculateSubscriptionTotal($item->id);
        $object = $this->getUpgradeObject($data, $total);

        return $object;
    }
}