<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.http.http');
jimport('joomla.filesystem.folder');

class gridboxModelStore extends JModelItem
{
    public function getTable($type = 'Fonts', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function uploadAttachmentFile($id, $cart, $file, $option_id)
    {
        $obj = new stdClass();
        if (isset($file['error']) && $file['error'] == 0) {
            $ext = strtolower(JFile::getExt($file['name']));
            $dir = JPATH_ROOT.'/'.gridboxHelper::$storeHelper->attachments;
            if (!JFolder::exists($dir)) {
                JFolder::create($dir);
            }
            $name = str_replace('.'.$ext, '', $file['name']);
            $filename = gridboxHelper::replace($name);
            $filename = JFile::makeSafe($filename);
            $name = str_replace('-', '', $filename);
            $name = str_replace('.', '', $name);
            if ($name == '') {
                $filename = date("Y-m-d-H-i-s").'.'.$ext;
            }
            $i = 2;
            $name = $filename;
            while (JFile::exists($dir.'/'.$name.'.'.$ext)) {
                $name = $filename.'-'.($i++);
            }
            $filename = $name.'.'.$ext;
            move_uploaded_file($file['tmp_name'], $dir.'/'.$filename);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_attachments_map')
                ->where('cart_id = '.$cart)
                ->where('page_id = '.$id)
                ->where('option_id = '.$option_id)
                ->where('product_id = 0');
            $db->setQuery($query);
            $map = $db->loadObject();
            if (!$map) {
                $map = new stdClass();
                $map->cart_id = $cart;
                $map->page_id = $id;
                $map->option_id = $option_id;
                $db->insertObject('#__gridbox_store_cart_attachments_map', $map);
                $map->id = $db->insertid();
            }
            $obj = new stdClass();
            $obj->attachment_id = $map->id;
            $obj->date = date("Y-m-d-H-i-s");
            $obj->filename = $filename;
            $obj->name = $file['name'];
            $db->insertObject('#__gridbox_store_product_attachments', $obj);
            $obj->id = $db->insertid();
        }

        return $obj;
    }

    public function upgradePlan($id, $upgrade_id)
    {
        $db = JFactory::getDbo();
        $total = gridboxHelper::$storeHelper->calculateSubscriptionTotal($upgrade_id);
        $query = $db->getQuery(true)
            ->select('p.id, p.title, d.subscription, d.price, d.sale_price')
            ->from('#__gridbox_pages AS p')
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id')
            ->where('p.id ='.$id);
        $db->setQuery($query);
        $plan = $db->loadObject();
        $object = gridboxHelper::$storeHelper->getUpgradeObject($plan, $total);
        if ($object->price > 0) {
            return false;
        }
        gridboxHelper::$storeHelper->subscriptionFreeUpgrade($object, $upgrade_id);

        return true;
    }

    public function pendingPayments()
    {
        $db = JFactory::getDbo();
        $types = [];
        foreach (gridboxHelper::$storeHelper->pending as $type) {
            $types[] = 'p.type = '.$db->quote($type);
        }
        $query = $db->getQuery(true)
            ->select('o.*, p.type')
            ->from('#__gridbox_store_orders AS o')
            ->where('o.published = 0')
            ->where('('.implode(' OR ', $types).')')
            ->where('o.params <> '.$db->quote(''))
            ->where('o.params <> '.$db->quote('{"id":""}'))
            ->leftJoin('#__gridbox_store_orders_payment AS p ON o.id = p.order_id');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $payments = [];
        foreach ($result as $order) {
            $type = $order->type;
            unset($order->type);
            if (!isset($payments[$type])) {
                $payments[$type] = [];
            }
            $payments[$type][] = $order;
        }
        foreach ($payments as $type => $orders) {
            $method = 'check'.ucfirst(str_replace('-kassa', '', $type));
            $this->{$method}($orders);
        }
    }

    public function checkKlarna($orders)
    {
        $klarna = gridboxHelper::$storeHelper->getStorePayment('klarna');
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkKlarna($klarna, $order, false, false, false);
        }
    }

    public function checkStripe($orders)
    {
        $stripe = gridboxHelper::$storeHelper->getStorePayment('stripe');
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkStripe($stripe, $order, false, false, false);
        }
    }

    public function checkBarion($orders)
    {
        $barion = gridboxHelper::$storeHelper->getStorePayment('barion');
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkBarion($barion, $order, false, false, false);
        }
    }

    public function checkPaypal($orders)
    {
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkPaypal($order, false, false, false);
        }
    }

    public function checkYandex($orders = null)
    {
        if (!$orders) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('o.*')
                ->from('#__gridbox_store_orders AS o')
                ->where('o.published = 0')
                ->where('p.type = '.$db->quote('yandex-kassa'))
                ->where('o.params <> '.$db->quote(''))
                ->where('o.params <> '.$db->quote('{"id":""}'))
                ->leftJoin('#__gridbox_store_orders_payment AS p ON o.id = p.order_id');
            $db->setQuery($query);
            $orders = $db->loadObjectList();
        }
        $yandex = gridboxHelper::$storeHelper->getStorePayment('yandex-kassa');
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkYandex($yandex, $order, false, false, false);
        }
    }

    public function checkPayupl($orders = null)
    {
        if (!$orders) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('o.*')
                ->from('#__gridbox_store_orders AS o')
                ->where('o.published = 0')
                ->where('p.type = '.$db->quote('payupl'))
                ->where('o.params <> '.$db->quote(''))
                ->where('o.params <> '.$db->quote('{"id":""}'))
                ->leftJoin('#__gridbox_store_orders_payment AS p ON o.id = p.order_id');
            $db->setQuery($query);
            $orders = $db->loadObjectList();
        }
        $payupl = gridboxHelper::$storeHelper->getStorePayment('payupl');
        $json = gridboxHelper::$storeHelper->authorizePayupl($payupl->params);
        foreach ($orders as $order) {
            gridboxHelper::$storeHelper->checkPayupl($payupl, $order, $json, false, false, false);
        }
    }

    public function getProductsList($id, $type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('p.id, p.title, p.intro_image AS image, d.price')
            ->from('#__gridbox_pages AS p')
            ->where('p.id <> '.$id)
            ->where('a.type = '.$db->quote('products'))
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id')
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id')
            ->order('p.id ASC');
        if (!empty($type)) {
            $query->where('d.product_type = '.$db->quote($type));
        }
        $db->setQuery($query);
        $data = new stdClass();
        $data->currency = gridboxHelper::$store->currency;
        $data->list = $db->loadObjectList();
        $t = $data->currency->thousand;
        $s = $data->currency->separator;
        $d = $data->currency->decimals;
        foreach ($data->list as $value) {
            $value->image = (!empty($value->image) && !gridboxHelper::isExternal($value->image) ? JUri::root() : '')
                .$value->image;
            $value->price = gridboxHelper::preparePrice($value->price, $t, $s, $d);
        }

        return $data;
    }

    public function getAppStoreFields($id, $type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('DISTINCT pf.field_key, pf.title')
            ->from('#__gridbox_store_product_variations_map AS vm')
            ->where('(pf.field_type = '.$db->quote('image').' OR pf.field_type = '.$db->quote('color').')')
            ->leftJoin('#__gridbox_pages AS p ON p.id = vm.product_id')
            ->leftJoin('#__gridbox_store_products_fields AS pf ON pf.id = vm.field_id')
            ->order('vm.order_group ASC, pf.title ASC');
        if ($type != 'store-search-result') {
            $query->where('p.app_id = '.$id);
        }
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $data = array('badge' => JText::_('PRODUCT_BADGE'), 'wishlist' => JText::_('WISHLIST'));
        foreach ($fields as $field) {
            $data[$field->field_key] = $field->title;
        }
        $data['price'] = JText::_('PRICE');
        $data['cart'] = JText::_('ADD_TO_CART');
        
        return $data;
    }

    public function getLiveSearchQuery($search, $type, $app_id, $apps)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        $groups = implode(',', $levels);
        $lang = JFactory::getLanguage()->getTag();
        $wheres = [];
        $searchWords = explode(' ', $search);
        $titles = [];
        $params = [];
        foreach ($searchWords as $word) {
            $title = '(p.title REGEXP '.$db->quote('^'.$word).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $param = '(p.params REGEXP '.$db->quote('^'.$word).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $text = mb_strtoupper($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_strtolower($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_ucfirst($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $titles[] = $title;
            $params[] = $param;
        }
        $wheres[] = '('.implode(' AND ', $titles).')';
        $wheres[] = '('.implode(' AND ', $params).')';
        gridboxHelper::getSearchFields($search, $type);
        $subStr = gridboxHelper::$cacheData->{$type}->fields;
        if (!empty($subStr)) {
            $wheres[] = 'p.id in ('.$subStr.')';
        }
        if ($type == 'store-search') {
            $wheres[] = 'pd.sku LIKE '.$db->quote('%'.$db->escape($search, true).'%', false);
        }
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->from('#__gridbox_pages AS p')
            ->where('('.implode(' OR ', $wheres).')')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote($lang).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('#__gridbox_categories AS c ON p.page_category = c.id')
            ->order('p.created desc');
        if ($type == 'store-search') {
            $query->leftJoin('#__gridbox_app AS a ON p.app_id = a.id')
                ->where('a.type ='.$db->quote('products'));
            $query->leftJoin('#__gridbox_store_product_data AS pd ON pd.product_id = p.id');
        }
        if ($app_id !== '' && $app_id != 'multiple') {
            $query->where('p.app_id = '.($app_id * 1));
        } else if ($app_id == 'multiple' && !empty($apps)) {
            $array = [];
            foreach ($apps as $id) {
                $array[] = $id * 1;
            }
            $str = implode(', ', $array);
            $query->where('p.app_id IN ('.$str.')');
        }
        $digital = gridboxHelper::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }

        return $query;
    }

    public function getLiveSearchData($search, $type, $app_id, $apps)
    {
        $db = JFactory::getDbo();
        $data = new stdClass();
        $query = $this->getLiveSearchQuery($search, $type, $app_id, $apps)
            ->select('p.id, p.title, p.intro_image, p.page_category, p.app_id');
        $db->setQuery($query, 0, 10);
        $data->pages = $db->loadObjectList();
        $query = $this->getLiveSearchQuery($search, $type, $app_id, $apps)
            ->select('COUNT(p.id)');
        $db->setQuery($query);
        $data->count = $db->loadResult();
        foreach ($data->pages as $page) {
            if ($page->app_id != 0 && $page->page_category != '') {
                $query = $db->getQuery(true)
                    ->select('c.title')
                    ->from('#__gridbox_categories AS c')
                    ->leftJoin('#__gridbox_pages AS p ON p.page_category = c.id')
                    ->where('p.id = '.$page->id);
                $db->setQuery($query);
                $page->category = $db->loadResult();
                $page->catLink = gridboxHelper::getGridboxCategoryLinks($page->page_category, $page->app_id);
            }
            if ($type == 'store-search') {
                $product = gridboxHelper::$storeHelper->getProductData($page->id);
                $page->prices = gridboxHelper::prepareProductPrices($page->id, $product->price, $product->sale_price);
            }
            $pageType = $page->app_id == 0 || $page->page_category == '' ? 'single' : 'blog';
            $page->link = gridboxHelper::getGridboxPageLinks($page->id, $pageType, $page->app_id, $page->page_category);
        }
        $currency = gridboxHelper::$store->currency;
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/live-search-results.php';

        return $out;
    }

    public function addPostToWishlist($id, $wishlist_id)
    {
        $variations = gridboxHelper::$storeHelper->getProductVariationsMap($id);
        $data = gridboxHelper::$storeHelper->getProductData($id);
        $variation = '';
        foreach ($data->variations as $key => $value) {
            if (isset($value->default) && $value->default) {
                $variation = $key;
                break;
            }
        }
        $extraFlag = false;
        $options = new stdClass();
        foreach ($data->extra_options as $field_id => $extra) {
            $required = $extra->required * 1 == 1;
            foreach ($extra->items as $item) {
                if ($required && $item->default) {
                    $required = false;
                }
                if ($item->default) {
                    $options->{$item->key} = new stdClass();
                    $options->{$item->key}->price = $item->price;
                    $options->{$item->key}->field_id = $field_id;
                }
            }
            if ($required) {
                $extraFlag = $required;
            }
        }
        $response = new stdClass();
        $response->status = false;
        if ((empty($variations) || !empty($variation)) && !$extraFlag) {
            $extra_options = json_encode($options);
            $response->status = true;
            $response->data = $this->addProductToWishlist($id, $wishlist_id, $variation, $extra_options);
        }

        return $response;
    }

    public function addPostToCart($id, $cart_id)
    {
        $variations = gridboxHelper::$storeHelper->getProductVariationsMap($id);
        $data = gridboxHelper::$storeHelper->getProductData($id);
        $variation = '';
        foreach ($data->variations as $key => $value) {
            if (isset($value->default) && $value->default) {
                $variation = $key;
                break;
            }
        }
        $extraFlag = false;
        $options = new stdClass();
        foreach ($data->extra_options as $field_id => $extra) {
            $required = $extra->required * 1 == 1;
            foreach ($extra->items as $item) {
                if ($required && $item->default) {
                    $required = false;
                }
                if ($item->default) {
                    $options->{$item->key} = new stdClass();
                    $options->{$item->key}->price = $item->price;
                    $options->{$item->key}->field_id = $field_id;
                }
            }
            if ($required) {
                $extraFlag = $required;
            }
        }
        $response = new stdClass();
        $response->status = false;
        if ((empty($variations) || !empty($variation)) && !$extraFlag) {
            $extra_options = json_encode($options);
            $response->status = true;
            $this->addProductToCart($id, $cart_id, 1, $variation, $extra_options);
        }

        return $response;
    }

    public function deleteStoreBadge($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_badges')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function updateStoreBadge($badge)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_store_badges', $badge, 'id');
    }

    public function getStoreBadge()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_badges');
        $db->setQuery($query);
        $badges = $db->loadObjectList();

        return $badges;
    }

    public function addProductBadge()
    {
        $db = JFactory::getDbo();
        $badge = new stdClass();
        $badge->color = '#1da6f4';
        $badge->title = JText::_('PRODUCT_BADGE');
        $db->insertObject('#__gridbox_store_badges', $badge);
        $badge->id = $db->insertid();

        return $badge;
    }

    public function payfastCallback($data)
    {
        $payfast = gridboxHelper::$storeHelper->getStorePayment('payfast');
        if ($data['payment_status'] == 'COMPLETE') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_orders')
                ->where('params = '.$db->quote($data['m_payment_id']));
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id) {
                $json = json_encode($data);
                gridboxHelper::$storeHelper->approveOrder($id, $json, true, false, false);
            }
        }
        exit();
    }

    public function pagseguroCallback($id, $transactionCode)
    {
        if (!empty($id) && !empty($transactionCode)) {
            gridboxHelper::$storeHelper->approveOrder($id, $transactionCode, true, false, false);
        }
    }

    public function robokassaCallback($inv_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$inv_id)
            ->where('published = 0');
        $db->setQuery($query);
        $id = $db->loadResult();
        if ($id) {
            gridboxHelper::$storeHelper->approveOrder($id, null, true, false, false);
        }
        exit();
    }

    public function monoCallback($data)
    {
        if (!isset($data['status'])) {
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_store_orders')
            ->where('params = '.$db->quote($data['invoiceId']));
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!$id) {
            return;
        }
        if ($data['status'] == 'success') {
            gridboxHelper::$storeHelper->approveOrder($id, null, true, false, false);
        } else if ($data['status'] == 'failure' || $data['status'] == 'expired') {
            gridboxHelper::$storeHelper->setCanceled($order);
        }
    }

    public function dotpayCallback($data)
    {
        if (isset($data['operation_status']) && $data['operation_status'] == 'completed') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_orders')
                ->where('params = '.$db->quote($data['control']));
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id) {
                gridboxHelper::$storeHelper->approveOrder($id, null, true, false, false);
            }
        }
    }

    public function liqpayCallback($data, $signature)
    {
        $liqpay = gridboxHelper::$storeHelper->getStorePayment('liqpay');
        $str = $liqpay->params->private_key.$data.$liqpay->params->private_key;
        $sign = base64_encode(sha1($str, 1));
        if ($sign == $signature) {
            $json = base64_decode($data);
            $obj = json_decode($json);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_orders')
                ->where('params = '.$obj->order_id);
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id) {
                gridboxHelper::$storeHelper->approveOrder($id, $json, true, false, false);
            }
        }
        exit();
    }

    public function barionCallback($paymentId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders')
            ->where('params = '.$paymentId);
        $db->setQuery($query);
        $order = $db->loadResult();
        if (isset($order->id)) {
            $barion = gridboxHelper::$storeHelper->getStorePayment('barion');
            gridboxHelper::$storeHelper->checkBarion($barion, $order, false, false);
        }
    }

    public function klarnaCallback($order_id)
    {
        $klarna = gridboxHelper::$storeHelper->getStorePayment('klarna');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders')
            ->where('params = '.$order_id);
        $db->setQuery($query);
        $order = $db->loadResult();
        if (isset($order->id)) {
            $klarna = gridboxHelper::$storeHelper->getStorePayment('klarna');
            gridboxHelper::$storeHelper->checkKlarna($klarna, $order, false, false);
        }
    }

    public function mollieCallback($id)
    {
        $mollie = gridboxHelper::$storeHelper->getStorePayment('mollie');
        $headers = array('Authorization: Bearer '.$mollie->params->api_key, 'Content-Type: application/json');
        $curl = curl_init();
        $options = array();
        $options[CURLOPT_URL] = 'https://api.mollie.com/v2/payments/'.$id;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $response = json_decode($body);
        if (!empty($response->paidAt) && empty($response->_links->refunds) && empty($response->_links->chargebacks)) {
            $orderId = $response->metadata->order_id;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_orders')
                ->where('params = '.$orderId);
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id) {
                gridboxHelper::$storeHelper->approveOrder($id, $body, true, false, false);
            }
        }
        exit();
    }

    public function submitRobokassa($id)
    {
        $order = $this->getOrder($id);
        $robokassa = gridboxHelper::$storeHelper->getStorePayment('robokassa');
        $title = array();
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $inv_id = $id;
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $code = $currency->code;
        $allowedCurrency = ['USD', 'EUR', 'KZT'];
        $OutSumCurrency = in_array($code, $allowedCurrency);
        $cache = $robokassa->params->merchant_id.":".$price.":".$inv_id.":";
        $receipt = '';
        if (isset($robokassa->params->fiscalization) && $robokassa->params->fiscalization) {
            $json = new stdClass();
            $json->sno = $robokassa->params->sno;
            $json->items = [];
            foreach ($order->products as $product) {
                $item = new stdClass();
                $item->name = $product->title;
                $item->quantity = $product->quantity;
                $item->sum = $product->sale_price !== '' ? $product->sale_price : $product->price;
                $item->payment_method = $robokassa->params->payment_method;
                $item->payment_object = $robokassa->params->payment_object;
                $item->tax = $robokassa->params->tax;
                $json->items[] = $item;
            }
            if ($order->shipping) {
                $item = new stdClass();
                $item->name = $order->shipping->title;
                $item->quantity = 1;
                $item->sum = $order->shipping->price;
                $item->payment_method = $robokassa->params->payment_method;
                $item->payment_object = $robokassa->params->payment_object;
                $item->tax = $robokassa->params->tax;
                $json->items[] = $item;
            }
            $str = json_encode($json);
            $receipt = urlencode($str);
            $cache .= $receipt.":";
        }
        if ($OutSumCurrency) {
            $cache .= $code.":";
        }
        $cache .= $robokassa->params->merchant_password;
        $signature = md5($cache);
?>
        <form action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST" id="payment-form">
            <input type=hidden name=MerchantLogin value="<?php echo $robokassa->params->merchant_id; ?>">
            <input type=hidden name=OutSum value="<?php echo $price; ?>">
            <input type=hidden name=InvId value="<?php echo $inv_id; ?>">
            <input type=hidden name=Description value="<?php echo $name; ?>">
            <input type=hidden name=SignatureValue value="<?php echo $signature; ?>">
<?php
        if ($OutSumCurrency) {
?>
            <input type=hidden name=OutSumCurrency value="<?php echo $code; ?>">
<?php
        }
        if (!empty($receipt)) {
?>
            <input type=hidden name=Receipt value="<?php echo $receipt; ?>">
<?php
        }
?>
        </form>
        <script>
            document.getElementById('payment-form').submit();
        </script>
<?php
        exit;
    }

    public function submitMono($id)
    {
        $order = $this->getOrder($id);
        $mono = gridboxHelper::$storeHelper->getStorePayment('mono');
        $amount = number_format(floatval($order->total) - floatval($order->later), 2, '.', '') * 100;
        $redirect = JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time();
        $webHook = JUri::root()."index.php?option=com_gridbox&task=store.monoCallback";
        $data = '{"amount": '.$amount.', "ccy": '.$mono->params->ccy.', "redirectUrl": "'.$redirect.'", "webHookUrl": "'.$webHook.'"}';
        $headers = ['X-Token: '.$mono->params->token, 'Content-Type: application/json'];
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = $data;
        $options[CURLOPT_URL] = 'https://api.monobank.ua/api/merchant/invoice/create';
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $json = json_decode($body);
        if (isset($json->invoiceId)) {
            $this->updateOrder($id, $json->invoiceId);
            header('Location: '.$json->pageUrl);
        } else {
            print_r($json);
        }
        exit;
    }

    public function submitMollie($id)
    {
        $order = $this->getOrder($id);
        $mollie = gridboxHelper::$storeHelper->getStorePayment('mollie');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $orderId = time();
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $array = [
            "amount" => ["currency" => $currency->code, "value" => $price],
            "description" => $name,
            "redirectUrl" => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(),
            "webhookUrl" => JUri::root()."index.php?option=com_gridbox&task=store.mollieCallback",
            "metadata" => ["order_id" => $orderId]
        ];
        $headers = ['Authorization: Bearer '.$mollie->params->api_key, 'Content-Type: application/json'];
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = json_encode($array);
        $options[CURLOPT_URL] = 'https://api.mollie.com/v2/payments';
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $response = json_decode($body);
        $this->updateOrder($id, $orderId);
        if (isset($response->_links) && isset($response->_links->checkout)) {
            header('Location: '.$response->_links->checkout->href, true, 303);
        } else {
?>
            <script>
                localStorage.setItem('gridbox_payment_error', '<?php echo addslashes($response->detail); ?>');
                window.location.href = '<?php echo JUri::root(); ?>';
            </script>
<?php
        }
        exit();
    }

    public function submitPayfast($id)
    {
        $order = $this->getOrder($id);
        $payfast = gridboxHelper::$storeHelper->getStorePayment('payfast');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $url = ($payfast->params->environment == 'sandbox' ? 'https://sandbox.' : 'https://www.').'payfast.co.za/eng/process';
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $m_payment_id = time();
        $this->updateOrder($id, $m_payment_id);
?>
<form id="payment-form" method="POST" action="<?php echo $url; ?>" accept-charset="utf-8">
    <input type="hidden" name="merchant_id" value="<?php echo $payfast->params->merchant_id; ?>">
    <input type="hidden" name="merchant_key" value="<?php echo $payfast->params->merchant_key; ?>">
    <input type="hidden" name="return_url" value="<?php echo JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(); ?>">
    <input type="hidden" name="notify_url" value="<?php echo JUri::root()."index.php?option=com_gridbox&task=store.payfastCallback"; ?>">
    <input type="hidden" name="m_payment_id" value="<?php echo $m_payment_id; ?>">
    <input type="hidden" name="amount" value="<?php echo $price; ?>">
    <input type="hidden" name="item_name" value="<?php echo $name; ?>">
</form>
<script>
    document.getElementById("payment-form").submit();
</script>
<?php
        exit;
    }

    public function submitDotpay($id)
    {
        $order = $this->getOrder($id);
        $dotpay = gridboxHelper::$storeHelper->getStorePayment('dotpay');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $array = [
            "api_version" => "dev",
            "amount" => $price,
            "currency" => $currency->code,
            "description" => $name,
            "url" => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(),
            "type" => "0",
            "buttontext" => JText::_('RETURN_TO_SHOP'),
            "urlc" => JUri::root()."index.php?option=com_gridbox&task=store.dotpayCallback",
            "control" => hash('md5', date("Y-m-d H:i:s")),
            "ignore_last_payment_channel" => 1
        ];
        $chkStr = $dotpay->params->pin.$array['api_version'].$dotpay->params->account_id.$array['amount'].
            $array['currency'].$array['description'].$array['control'].$array['url'].$array['type'].
            $array['buttontext'].$array['urlc'].$array['ignore_last_payment_channel'];
        $chk = hash('sha256', $chkStr);
        $url = 'https://ssl.dotpay.pl/'.($dotpay->params->environment == 'sandbox' ? 'test_payment/' : 't2/');
        $this->updateOrder($id, $array['control']);
?>
<form id="payment-form" method="POST" action="<?php echo $url ?>" accept-charset="utf-8">
    <input type="hidden" name="id" value="<?php echo $dotpay->params->account_id; ?>" />
<?php
foreach ($array as $key => $value) {
?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
<?php
}
?>
    <input type="hidden" name="chk" value="<?php echo $chk; ?>" />
</form>
<script>
    document.getElementById("payment-form").submit();
</script>
<?php
        exit;
    }

    public function submitBarion($id)
    {
        $order = $this->getOrder($id);
        $barion = gridboxHelper::$storeHelper->getStorePayment('barion');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $n = $currency->code == 'HUF' ? 0 : 2;
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', $n);
        $params = $barion->params;
        $url = 'https://api.'.($params->environment == 'sandbox' ? 'test.' : '').'barion.com/v2/Payment/Start';
        $sources = [];
        $sources[] = !$params->sources ? 'All' : 'Balance';
        $order_number = gridboxHelper::$storeHelper->createOrderNumber($id);
        $data = [
            'POSKey' => $params->secret_key,
            'PaymentType' => 'Immediate',
            'GuestCheckOut' => $params->guest,
            'FundingSources' => $sources,
            'PaymentRequestId' => $order_number,
            'RedirectUrl' => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(),
            'CallbackUrl' => JUri::root()."index.php?option=com_gridbox&task=store.barionCallback",
            'Locale' => $params->locale,
            'Currency' => $currency->code,
            'Transactions' => [
                [
                    'POSTransactionId' => $order_number,
                    'Payee' => $params->email,
                    'Total' => $price,
                    'Items' => [
                        [
                            'Name' => $name,
                            'Description' => $name,
                            'Quantity' => 1,
                            'Unit' => 'pcs',
                            'UnitPrice' => $price,
                            'ItemTotal' => $price
                        ]
                    ]
                ]
            ]
        ];
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $str = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($str);
        if (isset($response->PaymentId)) {
            $this->updateOrder($id, $response->PaymentId);
            header('Location: '.$response->GatewayUrl);
        } else {
?>
            <script>
                localStorage.setItem('gridbox_payment_error', '<?php echo addslashes($response->Errors[0]->Title); ?>');
                window.location.href = '<?php echo JUri::root(); ?>';
            </script>
<?php
        }
        exit();
    }

    public function submitSquare($id)
    {
        $order = $this->getOrder($id);
        $square = gridboxHelper::$storeHelper->getStorePayment('square');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $url = 'https://connect.squareup'.($square->params->environment == 'sandbox' ? 'sandbox' : '').'.com/v2/locations/';
        $url .= $square->params->location_id.'/checkouts';
        $headers = [
            "Square-Version: 2020-11-18",
            "Authorization: Bearer ".$square->params->access_token,
            "Content-Type: application/json"
        ];
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $data = [
            "idempotency_key" => uniqid(),
            "order" => [
                "idempotency_key" => uniqid(),
                "location_id" => $square->params->location_id,
                "order" => [
                    "location_id" => $square->params->location_id,
                    "line_items" => [
                        [
                            "quantity" => "1",
                            "name" => $name,
                            "base_price_money" => [
                                "currency" => $currency->code,
                                "amount" => (int)($price * 100)
                            ]
                        ]
                    ]
                ]
            ],
            "redirect_url" => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time()
        ];
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $str = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($str);
        if (isset($response->checkout)) {
            $this->updateOrder($id, $response->checkout->order->id);
            header('Location: '.$response->checkout->checkout_page_url);
        } else {
?>
            <script>
                localStorage.setItem('gridbox_payment_error', '<?php echo addslashes($response->errors[0]->detail); ?>');
                window.location.href = '<?php echo JUri::root(); ?>';
            </script>
<?php
        }
        exit();
    }

    public function submitLiqpay($id)
    {
        $order = $this->getOrder($id);
        $liqpay = gridboxHelper::$storeHelper->getStorePayment('liqpay');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $orderId = time();
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $params = ['action' => 'pay', 'amount' => floatval($order->total) - floatval($order->later), 'currency' => $currency->code,
            'description' => $name, 'server_url' => JUri::root()."index.php?option=com_gridbox&task=store.liqpayCallback",
            'result_url' => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(), 'version' => '3',
            'public_key' => $liqpay->params->public_key, 'order_id' => $orderId];
        $data = base64_encode(json_encode($params));
        $str = $liqpay->params->private_key.$data.$liqpay->params->private_key;
        $signature = base64_encode(sha1($str, 1));
        $this->updateOrder($id, $orderId);
?>
<form id="payment-form" method="POST" action="https://www.liqpay.ua/api/3/checkout" accept-charset="utf-8">
    <input type="hidden" name="data" value="<?php echo $data; ?>" />
    <input type="hidden" name="signature" value="<?php echo $signature; ?>" />
</form>
<script>
    document.getElementById("payment-form").submit();
</script>
<?php
        exit;
    }

    public function submitPagseguro($id)
    {
        $order = $this->getOrder($id);
        $pagseguro = gridboxHelper::$storeHelper->getStorePayment('pagseguro');
        $url = 'https://ws.';
        if ($pagseguro->params->environment == 'sandbox') {
            $url .= 'sandbox.';
        }
        $url .= 'pagseguro.uol.com.br/v2/checkout';
        $array = ['email' => $pagseguro->params->email, 'token' => $pagseguro->params->token];
        $url .= '?'.http_build_query($array);
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $total = floatval($order->total) - floatval($order->later) - ($order->shipping ? $order->shipping->price : 0);
        $price = gridboxHelper::preparePrice($total, '', '.', 2);
        $content = "currency=BRL&itemId1=".$id."&itemDescription1=".$name."&itemAmount1=".$price."&itemQuantity1=1";
        if ($order->shipping) {
            $price = gridboxHelper::preparePrice($order->shipping->price, '', '.', 2);
            $content .= '&itemShippingCost1='.$price;
        }
        $content .= '&shippingAddressRequired=true';
        //$content .= "&reference=".md5(time());
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        curl_close($ch);
        $object = simplexml_load_string($xml);
        $response = new stdClass();
        $response->code = isset($object->code) ? $object->code : null;
        $response->status = $response->code ? true : false;
        $response->error = isset($object->error) ? $object->error->message : null;
        $str = json_encode($response);
        echo $str;exit;
    }

    public function submitKlarna($id)
    {
        $order = $this->getOrder($id);
        $klarna = gridboxHelper::$storeHelper->getStorePayment('klarna');
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
        $tax_rate = 0;
        $order_lines = []; 
        foreach ($order->products as $product) {
            $title[] = $product->title;
            $tax_rate = $product->tax_rate;
            $price = !empty($product->sale_price) ? $product->sale_price : $product->price;
            $price = gridboxHelper::preparePrice($price, '', '.', 2);
            $tax = gridboxHelper::preparePrice($product->tax, '', '.', 2);
            $tax_rate = gridboxHelper::preparePrice($product->tax_rate, '', '.', 2);
            $unit_price = gridboxHelper::preparePrice($price / $product->quantity, '', '.', 2);
            $order_lines[] = [
                "name" => $product->title,
                "quantity" => $product->quantity,
                "unit_price" => strval($unit_price * 100),
                "tax_rate" => strval($tax_rate * 100),
                "total_amount" => strval($price * 100),
                "total_tax_amount" => strval($tax * 100),
                "image_url" => (!empty($product->image) && !gridboxHelper::isExternal($product->image) ? JUri::root() : '').$product->image
            ];
        }
        if ($order->shipping) {
            $order->total -= floatval($order->shipping->price);
        }
        $name = implode(', ', $title);
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $tax = gridboxHelper::preparePrice($order->tax, '', '.', 2);
        $tax_rate = gridboxHelper::preparePrice($tax_rate, '', '.', 2);
        $locale = JFactory::getLanguage()->getTag();
        $confirmation = JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time();
        $checkout = gridboxHelper::getStoreSystemUrl('checkout');
        $terms = !empty($klarna->params->terms) ? $klarna->params->terms : JUri::root();
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $array = [
            "purchase_country" => $klarna->params->country,
            "purchase_currency" => $currency->code,
            "locale" => $locale,
            "order_amount" => strval($price * 100),
            "order_tax_amount" => strval($tax * 100),
            "order_lines" => $order_lines,
            "merchant_urls" => [
                "terms" => $terms,
                "checkout" => $checkout."?order_id={checkout.order.id}",
                "confirmation" => $confirmation."?order_id={checkout.order.id}",
                "push" => JUri::root()."index.php?option=com_gridbox&task=store.klarnaCallback?order_id={checkout.order.id}"
            ]
        ];
        if ($order->shipping) {
            $tax = gridboxHelper::preparePrice($order->shipping->tax, '', '.', 2);
            $tax_rate = gridboxHelper::preparePrice($order->shipping->tax_rate, '', '.', 2);
            $price = gridboxHelper::preparePrice($order->shipping->price, '', '.', 2);
            $array['selected_shipping_option'] = [
                "id" => $order->shipping->id,
                "name" => $order->shipping->title,
                "price" => strval($price * 100),
                "preselected" => true,
                "tax_amount" => strval($tax * 100),
                "tax_rate" => strval($tax_rate * 100)
            ];
            $array['shipping_options'] = [
                $array['selected_shipping_option']
            ];
        }
        /*print_r($array);exit;*/
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($klarna->params->username.':'.$klarna->params->password)
        ];
        $curl = curl_init($url.'checkout/v3/orders');
        //curl_setopt($curl, CURLOPT_USERPWD, $klarna->params->username.':'.$klarna->params->password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        try {
            $json = json_decode($body);
            if (isset($json->order_id)) {
                $this->updateOrder($id, $json->order_id);
            }
        } catch (Exception $e) {}
        print_r($body);
        exit;
    }

    public function submitYandexKassa($id)
    {
        $order = $this->getOrder($id);
        $yandex = gridboxHelper::$storeHelper->getStorePayment('yandex-kassa');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $name = implode(', ', $title);
        $name = mb_substr($name, 0, 128);
        $orderId = uniqid('', true);
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $array = [
            'amount' => [
                'value' => $price,
                'currency' => $currency->code,
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(),
            ],
            'capture' => true,
            'description' => $name,
        ];
        $headers = [
            'Idempotence-Key: '.$orderId,
            'Content-Type: application/json'
        ];
        $curl = curl_init('https://api.yookassa.ru/v3/payments');
        curl_setopt($curl, CURLOPT_USERPWD, $yandex->params->shop_id.':'.$yandex->params->secret_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $response = json_decode($body);
        $this->updateOrder($id, $body);

        if (isset($response->confirmation)) {
            header('Location: '.$response->confirmation->confirmation_url);
        } else {
?>
            <script>
                localStorage.setItem('gridbox_payment_error', '<?php echo addslashes($response->description); ?>');
                window.location.href = '<?php echo JUri::root(); ?>';
            </script>
<?php
        }
        exit();
    }

    public function submitPayupl($id)
    {
        $order = gridboxHelper::$storeHelper->getOrder($id, true);
        $payupl = gridboxHelper::$storeHelper->getStorePayment('payupl');
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $email = '';
        foreach ($order->info as $info) {
            if ($info->type == 'email') {
                $email = $info->value;
                break;
            }
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $url = 'https://secure'.($payupl->params->environment == 'sandbox' ? '.snd' : '').'.payu.com';
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', '2');
        $json = gridboxHelper::$storeHelper->authorizePayupl($payupl->params);
        if (isset($json->error)) {
            print_r($json->error_description);exit;
        }
        $fields = new stdClass();
        $fields->continueUrl = JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time();
        $fields->notifyUrl = JUri::root()."index.php?option=com_gridbox&task=store.checkPayupl";
        $fields->customerIp = $_SERVER['REMOTE_ADDR'];
        $fields->merchantPosId = $payupl->params->pos_id;
        $fields->description = gridboxHelper::$store->general->store_name;
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $fields->currencyCode = $currency->code;
        $fields->totalAmount = $price * 100;
        if (!empty($email)) {
            $fields->buyer = ["email" => $email];
        }
        $product = new stdClass();
        $product->name = $name;
        $product->unitPrice = $price * 100;
        $product->quantity = 1;
        $fields->products = array($product);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."/api/v2_1/orders/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$json->access_token
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response);
        if (isset($json->orderId)) {
            $this->updateOrder($id, '{"id":"'.$json->orderId.'"}');
            header('Location: '.$json->redirectUri);
        } else {
?>
            <script>
                localStorage.setItem('gridbox_payment_error', '<?php echo addslashes($json->status->statusDesc); ?>');
                window.location.href = '<?php echo JUri::root(); ?>';
            </script>
<?php
        }
        exit;
    }

    public function submit2checkout($id)
    {
        $order = $this->getOrder($id);
        $checkout = gridboxHelper::$storeHelper->getStorePayment('twocheckout');
        $url = 'https://www.2checkout.com/checkout/purchase';
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $price = gridboxHelper::preparePrice(floatval($order->total) - floatval($order->later), '', '.', 2);
        $return = JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time();
?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
            <input type="hidden" name="sid" value="<?php echo $checkout->params->account_number; ?>">
            <input type="hidden" name="mode" value="2CO">
            <input type="hidden" name="pay_method" value="PPI">
            <input type="hidden" name="x_receipt_link_url" value="<?php echo $return; ?>">
            <input type='hidden' name='return_url' value="<?php echo $return;?>" >
            <input type="hidden" name="li_1_name" value="<?php echo implode(', ', $title); ?>">
            <input type="hidden" name="li_1_price" value="<?php echo $price; ?>">
            <input type="hidden" name="li_1_type" value="product">
            <input type="hidden" name="li_1_quantity" value="1">
<?php
            if ($checkout->params->environment == 'sandbox') {
?>
            <input type='hidden' name='demo' value='Y' />
<?php
            }
?>
        </form>
        <script>
            document.getElementById('payment-form').submit();
        </script>
<?php 
        exit;
    }

    public function stripeCharges($id, $payment_id)
    {
        $order = $this->getOrder($id);
        $stripe = $this->getPayment($payment_id);
        $stripe->params = json_decode($stripe->settings);
        $array = [
            'line_items' => [],
            'mode' => 'payment',
            'success_url' => JUri::root()."index.php?option=com_gridbox&task=store.setOrder&time=".time(),
            'cancel_url' => JUri::root()
        ];
        $title = [];
        foreach ($order->products as $product) {
            $title[] = $product->title;
        }
        if ($order->shipping) {
            $title[] = $order->shipping->title;
        }
        $price = round(floatval($order->total) - floatval($order->later), 2);
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $line_item = [
            'price_data' => [
                'currency' => $currency->code,
                'product_data' => [
                    'name' => implode(', ', $title),
                ],
                'unit_amount' => $price * 100,
            ],
            'quantity' => 1
        ];
        $array['line_items'][] = $line_item;
        $ua = ['bindings_version' => '7.17.0', 'lang' => 'php',
            'lang_version' => phpversion(), 'publisher' => 'stripe', 'uname' => php_uname()];
        $headers = ['X-Stripe-Client-User-Agent: '.json_encode($ua),
            'User-Agent: Stripe/v1 PhpBindings/7.17.0',
            'Authorization: Bearer '.$stripe->params->secret_key];
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = $this->encode($array);
        $options[CURLOPT_URL] = 'https://api.stripe.com/v1/checkout/sessions';
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        print_r($body);exit;
    }

    public function encode($arr, $prefix = null)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $r = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            if ($prefix && $k && !is_int($k)){
                $k = $prefix."[".$k."]";
            } else if ($prefix) {
                $k = $prefix."[]";
            }
            if (is_array($v)) {
                $r[] = $this->encode($v, $k, true);
            } else {
                $r[] = urlencode($k)."=".urlencode($v);
            }
        }

        return implode("&", $r);
    }

    public function getPaymentOptions($id)
    {
        if (!empty($id)) {
            $obj = $this->getPayment($id);
        } else {
            $obj = new stdClass();
            $obj->type = 'offline';
            $obj->settings = '{}';
        }

        return $obj;
    }

    public function payAuthorize($id, $cardNumber, $expirationDate, $cardCode)
    {
        $order = $this->getOrder($id);
        $authorize = gridboxHelper::$storeHelper->getStorePayment('authorize');
        $obj = new stdClass();
        $obj->createTransactionRequest = new stdClass();
        $obj->createTransactionRequest->merchantAuthentication = new stdClass();
        $obj->createTransactionRequest->merchantAuthentication->name = $authorize->params->login_id;
        $obj->createTransactionRequest->merchantAuthentication->transactionKey = $authorize->params->transaction_key;
        $obj->createTransactionRequest->clientId = 'sdk-php-2.0.0-ALPHA';
        $obj->createTransactionRequest->refId = 'ref'.time();
        $obj->createTransactionRequest->transactionRequest = new stdClass();
        $obj->createTransactionRequest->transactionRequest->transactionType = 'authCaptureTransaction';
        $obj->createTransactionRequest->transactionRequest->amount = floatval($order->total) - floatval($order->later);
        $obj->createTransactionRequest->transactionRequest->payment = new stdClass();
        $obj->createTransactionRequest->transactionRequest->payment->creditCard = new stdClass();
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->cardNumber = $cardNumber;
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->expirationDate = $expirationDate;
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->cardCode = $cardCode;
        $xmlRequest = json_encode($obj);
        $url =  ($authorize->params->environment == 'sandbox' ? 'https://apitest' : 'https://api2').'.authorize.net/xml/v1/request.api';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 45);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: text/json"]);
        $text = curl_exec($curl);
        curl_close($curl);
        $response = json_decode(substr($text, 3), true);
        $str = json_encode($response);
        print_r($str);exit;
    }

    public function updateOrder($id, $params)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_orders')
            ->set('params = '.$db->quote($params))
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function getOrder($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$id);
        $db->setQuery($query);
        $order = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_products')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $order->products = $db->loadObjectList();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_shipping')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $order->shipping = $db->loadObject();

        return $order;
    }

    public function setCartShipping($id, $cart_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_shipping')
            ->where('cart_id = '.$cart_id);
        $db->setQuery($query);
        $shipping = $db->loadObject();
        if (!$shipping) {
            $shipping = new stdClass();
            $shipping->cart_id = $cart_id;
            $shipping->order_id = 0;
            $shipping->shipping_id = $id;
            $shipping->title = $shipping->price = $shipping->tax = '';
            $db->insertObject('#__gridbox_store_orders_shipping', $shipping);
        } else {
            $shipping->shipping_id = $id;
            $db->updateObject('#__gridbox_store_orders_shipping', $shipping, 'id');
        }
    }

    public function setCartPayment($id, $cart_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_payment')
            ->where('cart_id = '.$cart_id);
        $db->setQuery($query);
        $payment = $db->loadObject();
        if (!$payment) {
            $payment = new stdClass();
            $payment->cart_id = $cart_id;
            $payment->order_id = 0;
            $payment->payment_id = $id;
            $payment->title = $payment->type = '';
            $db->insertObject('#__gridbox_store_orders_payment', $payment);
        } else {
            $payment->payment_id = $id;
            $db->updateObject('#__gridbox_store_orders_payment', $payment, 'id');
        }
    }

    public function createOrder($data, $id)
    {
        $db = JFactory::getDbo();
        $cart = gridboxHelper::getStoreCart($id);
        $total = $cart->total + (gridboxHelper::$store->tax->mode == 'excl' ? $cart->tax : 0);
        if (!empty($data['shipping'])) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_shipping')
                ->where('id = '.$data['shipping']);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $tax = gridboxHelper::getStoreShippingTax($cart);
            $obj = gridboxHelper::getStoreShippingItem($obj, $total, $tax, $cart);
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_orders_shipping')
                ->where('cart_id = '.$id);
            $db->setQuery($query);
            $shipping = $db->loadObject();
            $shipping = $shipping ? $shipping : new stdClass();
            $shipping->type = $obj->params->type;
            $shipping->title = $obj->title;
            $shipping->price = $obj->price;
            $shipping->tax = $obj->tax;
            $shipping->shipping_id = $data['shipping'];
            $shipping->tax_title = $tax ? $tax->title : '';
            $shipping->tax_rate = $tax ? $tax->rate : '';
            $shipping->cart_id = $id;
            if (isset($data['carrier'])) {
                $shipping->carrier = $data['carrier'];
            }
            $total = $obj->total;
        } else {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_orders_shipping')
                ->where('cart_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders')
            ->where('cart_id = '.$id);
        $db->setQuery($query);
        $order = $db->loadObject();
        $order = $order ? $order : new stdClass();
        $order->cart_id = $id;
        $order->user_id = JFactory::getUser()->id;
        $order->subtotal = $cart->subtotal;
        $order->tax = $cart->tax;
        $order->tax_mode = gridboxHelper::$store->tax->mode;
        $order->later = $cart->later;
        $order->total = $total;
        $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
        $order->currency_symbol = $currency->symbol;
        $order->currency_position = $currency->position;
        if (empty($order->id)) {
            $db->insertObject('#__gridbox_store_orders', $order);
            $order->id = $db->insertid();
        } else {
            $db->updateObject('#__gridbox_store_orders', $order, 'id');
        }

        if (!empty($cart->discount)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_orders_discount')
                ->where('order_id = '.$order->id);
            $db->setQuery($query);
            $discount = $db->loadObject();
            $discount = $discount ? $discount : new stdClass();
            $discount->order_id = $order->id;
            $discount->promo_id = $cart->promo ? $cart->promo->id : 0;
            $discount->title = $cart->promo ? $cart->promo->title : '';
            $discount->code = $cart->promo ? $cart->promo->code : '';
            $discount->unit = $cart->promo ? $cart->promo->unit : '';
            $discount->discount = $cart->promo ? $cart->promo->discount : '';
            $discount->value = $cart->discount;
            $order->discount = $discount;
            if (empty($discount->id)) {
                $db->insertObject('#__gridbox_store_orders_discount', $discount);
            } else {
                $db->updateObject('#__gridbox_store_orders_discount', $discount, 'id');
            }
        } else {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_orders_discount')
                ->where('order_id = '.$order->id);
            $db->setQuery($query)
                ->execute();
        }

        if (!empty($data['shipping'])) {
            $shipping->order_id = $order->id;
            $order->shipping = $shipping;
            if (!isset($shipping->id)) {
                $db->insertObject('#__gridbox_store_orders_shipping', $shipping);
            } else {
                $db->updateObject('#__gridbox_store_orders_shipping', $shipping, 'id');
            }
        }

        if (!empty($data['payment'])) {
            $obj = $this->getPayment($data['payment'] * 1);
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_orders_payment')
                ->where('cart_id = '.$id);
            $db->setQuery($query);
            $payment = $db->loadObject();
            $payment = $payment ? $payment : new stdClass();
            $payment->order_id = $order->id;
            $payment->title = $obj->title;
            $payment->type = $obj->type;
            $payment->payment_id = $obj->id;
            $payment->cart_id = $id;
            if (!isset($payment->id)) {
                $db->insertObject('#__gridbox_store_orders_payment', $payment);
            } else {
                $db->updateObject('#__gridbox_store_orders_payment', $payment, 'id');
            }
        }
        
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_cart_attachments_map')
            ->set('order_id = '.$order->id)
            ->where('cart_id = '.$id);
        $db->setQuery($query)
            ->execute();
        $order->products = [];
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_order_products')
            ->where('order_id = '.$order->id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_order_license')
            ->where('order_id = '.$order->id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_order_product_variations')
            ->where('order_id = '.$order->id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_bookings')
            ->where('order_id = '.$order->id);
        $db->setQuery($query)
            ->execute();
        foreach ($cart->products as $obj) {
            $isBooking = isset($obj->data->app_type) && $obj->data->app_type == 'booking' ? true : null;
            $product = new stdClass();
            $product->order_id = $order->id;
            $product->title = $obj->title;
            $product->image = !empty($obj->images) ? $obj->images[0] : $obj->intro_image;
            $product->product_id = $obj->product_id;
            $product->variation = $obj->variation;
            $product->quantity = $obj->quantity;
            $product->price = $obj->data->price;
            $product->sale_price = $obj->prices->sale_price;
            $product->sku = $obj->data->sku;
            $product->tax = $obj->tax ? $obj->tax->amount : '';
            $product->tax_title = $obj->tax ? $obj->tax->title : '';
            $product->tax_rate = $obj->tax ? $obj->tax->rate : '';
            $product->net_price = $obj->net_price;
            $product->extra_options = json_encode($obj->extra_options);
            $product->product_type = $isBooking ? 'booking' : ($obj->data->product_type ?? '');
            $product->upgrade_id = $obj->upgrade_id;
            $product->renew_id = $obj->renew_id;
            $product->plan_key = $obj->plan_key;
            if (!empty($product->upgrade_id)) {
                $product->upgrade_price = gridboxHelper::$storeHelper->calculateSubscriptionTotal($product->upgrade_id);
            }
            $order->products[] = $product;
            $db->insertObject('#__gridbox_store_order_products', $product);
            $product->id = $db->insertid();
            if ($product->product_type == 'digital') {
                $product->product_token = hash('md5', date("Y-m-d H:i:s").'-'.$product->id);
                $db->updateObject('#__gridbox_store_order_products', $product, 'id');
                $digital = !empty($obj->data->digital_file) ? json_decode($obj->data->digital_file) : new stdClass();
                $license = new stdClass();
                $license->product_id = $product->id;
                $license->order_id = $order->id;
                $license->limit = isset($digital->max) ? $digital->max : '';
                $license->expires = 'new';
                $db->insertObject('#__gridbox_store_order_license', $license);
            }
            foreach ($obj->variations as $object) {
                $variation = new stdClass();
                $variation->product_id = $product->id;
                $variation->order_id = $order->id;
                $variation->title = $object->title;
                $variation->value = $object->value;
                $variation->color = $object->color;
                $variation->image = $object->image;
                $variation->type = $object->field_type;
                $db->insertObject('#__gridbox_store_order_product_variations', $variation);
            }
            if ($isBooking) {
                $paid = !empty($obj->later) || empty($payment->type) || $payment->type == 'offline' ? 0 : 1;
                $booking = (object)[
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'start_date' => $obj->booking->dates[0],
                    'end_date' => $obj->booking->dates[1] ?? '',
                    'start_time' => $obj->booking->time->start ?? '',
                    'end_time' => $obj->booking->time->end ?? '',
                    'guests' => $obj->booking->guests,
                    'price' => $obj->calc_price,
                    'later' => $obj->later ?? '',
                    'prepaid' => $obj->prepaid ?? '',
                    'paid' => $paid
                ];
                $db->insertObject('#__gridbox_store_bookings', $booking);
            }
        }

        $info = gridboxHelper::getCustomerInfo($data['checkout_id']);
        foreach ($info as $obj) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_customer_info')
                ->where('customer_id = '.$obj->id)
                ->where('cart_id = '.$id);
            $db->setQuery($query);
            $customer = $db->loadObject();
            $customer = $customer ? $customer : new stdClass();
            $customer->order_id = $order->id;
            $customer->customer_id = $obj->id;
            $customer->title = $obj->title;
            $customer->type = $obj->type;
            $customer->value = isset($data[$obj->id]) ? $data[$obj->id] : '';
            $customer->options = $obj->options;
            $customer->invoice = $obj->invoice;
            $customer->order_list = $obj->order_list;
            $customer->cart_id = $id;
            $value = $obj->type == 'country' && !empty($customer->value) ? json_decode($customer->value) : null;
            if ($value && !empty($value->country)) {
                $query = $db->getQuery(true)
                    ->select('title')
                    ->from('#__gridbox_countries')
                    ->where('id = '.$value->country);
                $db->setQuery($query);
                $value->country = $db->loadResult();
                if (!empty($value->region)) {
                    $query = $db->getQuery(true)
                        ->select('title')
                        ->from('#__gridbox_country_states')
                        ->where('id = '.$value->region);
                    $db->setQuery($query);
                    $value->region = $db->loadResult();
                }
                $customer->value = json_encode($value);
            }
            if (!isset($customer->id)) {
                $db->insertObject('#__gridbox_store_order_customer_info', $customer);
            } else {
                $db->updateObject('#__gridbox_store_order_customer_info', $customer, 'id');
            }
        }
        $order->currency_code = $currency->code;

        $time = time() + 604800;
        gridboxHelper::setcookie('gridbox_store_order', $order->id, $time);

        return $order;
    }

    public function setCustomerInfo($id, $value, $cart_id)
    {
        $db = JFactory::getDbo();
        $user_id = JFactory::getUser()->id;
        if (!empty($user_id)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_user_info')
                ->where('customer_id = '.$id)
                ->where('user_id = '.$user_id);
        } else {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_customer_info')
                ->where('customer_id = '.$id)
                ->where('cart_id = '.$cart_id);
        }
        $db->setQuery($query);
        $customer = $db->loadObject();
        if (!empty($user_id) && !$customer) {
            $customer = new stdClass();
            $customer->user_id = $user_id;
            $customer->customer_id = $id;
            $customer->value = $value;
            $db->insertObject('#__gridbox_store_user_info', $customer);
        } else if (!empty($user_id)) {
            $customer->value = $value;
            $db->updateObject('#__gridbox_store_user_info', $customer, 'id');
        }/* else if (!$customer) {
            $customer = new stdClass();
            $customer->order_id = 0;
            $customer->cart_id = $cart_id;
            $customer->customer_id = $id;
            $customer->title = $customer->type = '';
            $customer->value = $value;
            $customer->options = '';
            $db->insertObject('#__gridbox_store_order_customer_info', $customer);
        } else {
            $customer->value = $value;
            $db->updateObject('#__gridbox_store_order_customer_info', $customer, 'id');
        }*/
    }

    protected function getPayment($id)
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

    public function getStoreCartHTML($view, $id)
    {
        $cart = gridboxHelper::getStoreCart($id);
        $currency = gridboxHelper::$store->currency;
        $checkout = gridboxHelper::$store->checkout;
        $promoCodes = gridboxHelper::getPublishedPromoCode();
        if ($view == 'gridbox') {
            gridboxHelper::prepareCartForEditor($cart);
        }
        $cart->empty = count($cart->products) == 0;
        $uploader = gridboxHelper::getUploaderHelper();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/cart.php';

        return $out;
    }

    public function getWishlistHTML($view, $id)
    {
        $wishlist = gridboxHelper::getStoreWishlist($id, true);
        $currency = gridboxHelper::$store->currency;
        if ($view == 'gridbox') {
            $this->prepareWishlistForEditor($wishlist);
        }
        $wishlist->empty = count($wishlist->products) == 0;
        $uploader = gridboxHelper::getUploaderHelper();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/wishlist.php';

        return $out;
    }

    public function prepareWishlistForEditor($wishlist)
    {
        $currency = gridboxHelper::$store->currency;
        $product = new stdClass();
        $product->id = 0;
        $product->title = 'Product';
        $product->intro_image = 'components/com_gridbox/assets/images/thumb-square.png';
        $product->quantity = $product->min = 1;
        $product->images = array();
        $product->data = new stdClass();
        $product->data->price = 36.99;
        $product->data->stock = 1;
        $product->data->sale_price = '';
        $product->prices = new stdClass();
        $product->prices->sale_price = '';
        $product->prices->regular = gridboxHelper::preparePrice(36.99, $currency->thousand, $currency->separator, $currency->decimals);
        $product->variations = array();
        $product->extra_options = new stdClass();
        $product->extra_options->items = new stdClass();
        $product->extra_options->count = 0;
        $product->link = JUri::root();
        $product->attachments = [];
        $wishlist->products = array($product);
    }

    public function applyPromoCode($code, $id)
    {
        $db = JFactory::getDbo();
        if ($code != '') {
            $query = gridboxHelper::getPromoCodeQuery()
                ->select('p.id, p.unit, p.discount, p.applies_to, p.disable_sales, p.access')
                ->where('p.code = '.$db->quote($code));
            $db->setQuery($query);
            $promo = $db->loadObject();
        } else {
            $promo = new stdClass();
            $promo->id = 0;
        }
        $result = $code != '' ? 'invalid' : 'valid';
        if ($code != '' && !empty($promo->id)) {
            $products = gridboxHelper::getStoreCartProducts($id);
            foreach ($products as $product) {
                $valid = gridboxHelper::checkPromoCode($promo, $product);
                if ($valid) {
                    $result = 'valid';
                    break;
                }
            }
        }
        if ($result == 'valid') {
            $cart = new stdClass();
            $cart->id = $id;
            $cart->promo_id = $promo->id;
            $db->updateObject('#__gridbox_store_cart', $cart, 'id');
        }

        return $result;
    }

    public function updateProductQuantity($id, $cart_id, $quantity)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('product_id AS p_id, variation AS var, extra_options AS extra')
            ->from('#__gridbox_store_cart_products')
            ->where('id = '.$id);
        $db->setQuery($query);
        $product = $db->loadObject();
        $data = $this->getProductData($product->p_id, $product->var);
        $cart = gridboxHelper::getStoreCartObject($cart_id);
        $product = $this->getProduct($cart->id, $data, $product->var, $product->extra, 0, '', 0);
        $product->quantity = 0;
        $this->setProductQuantity($product, $data, $quantity);
        gridboxHelper::updateStoreCart($cart);
    }

    public function addProductToCart($id, $cid, $q, $var = '', $extra = '{}', $renew = 0, $plan = '', $up = 0, $attachments = [], $booking = '{}')
    {
        $data = $this->getProductData($id, $var);
        $min = !empty($data->min) ? $data->min * 1 : 1;
        if ($data->stock == '' || $data->stock * 1 >= $min) {
            $cart = gridboxHelper::getStoreCartObject($cid);
            $type = $data->product_type;
            $product = $this->getProduct($cart->id, $data, $var, $extra, $renew, $type, $plan, $up, $booking);
            if ($product->quantity == 0 && $q < $min) {
                $q = $min;
            }
            if (!empty($attachments)) {
                $this->setProductAttachment($attachments, $product->id, 0, $cid);
            }
            $this->setProductQuantity($product, $data, $q);
            gridboxHelper::updateStoreCart($cart);
        }
    }

    public function setCartCountry($id, $country, $region)
    {
        $cart = gridboxHelper::getStoreCartObject($id);
        $cart->country = $country;
        $cart->region = $region;
        gridboxHelper::updateStoreCart($cart);
    }

    public function moveProductFromWishlist($id, $product_id, $cart_id)
    {
        $wishlist = gridboxHelper::getStoreWishlist($id);
        $qty = 1;
        foreach ($wishlist->products as $product) {
            if ($product->id != $product_id) {
                continue;
            }
            if (!$product->hasFileQty) {
                break;
            }
            $qty = 0;
            foreach ($product->extra_options->items as $item) {
                if (isset($item->attachments) && $item->quantity) {
                    $qty += count($item->attachments);
                }
            }
            break;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('wp.product_id, wp.variation, wp.extra_options, wp.wishlist_id, wp.booking')
            ->from('#__gridbox_store_wishlist_products AS wp')
            ->where('wp.id = '.$product_id);
        $db->setQuery($query);
        $product = $db->loadObject();
        $attachments = $this->getAttachments($product->product_id, 0, $product_id, $product->wishlist_id);
        $this->addProductToCart($product->product_id, $cart_id, $qty, $product->variation,
                                $product->extra_options, 0, '', 0, $attachments, $product->booking);
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_wishlist_products')
            ->where('id = '.$product_id);
        $db->setQuery($query)
            ->execute();
    }

    public function getAttachments($id, $cart_id, $product_id = 0, $w_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_cart_attachments_map')
            ->where('page_id = '.$id)
            ->where('cart_id = '.$cart_id)
            ->where('wishlist_id = '.$w_id)
            ->where('product_id = '.$product_id);
        $db->setQuery($query);
        $attachments = $db->loadObjectList();

        return $attachments;
    }

    public function addProductToWishlist($id, $w_id, $variation = '', $extra = '{}', $attachments = [], $booking = '{}'):object
    {
        $data = $this->getProductData($id, $variation);
        $wishlist = gridboxHelper::updateStoreWishlist($w_id);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_wishlist_products')
            ->where('wishlist_id = '.$wishlist->id)
            ->where('product_id = '.$id)
            ->where('variation = '.$db->quote($variation))
            ->where('booking = '.$db->quote($booking))
            ->where('extra_options = '.$db->quote($extra));
        $db->setQuery($query);
        $product = $db->loadObject();
        if ($product) {
            return $data;
        }
        $product = new stdClass();
        $product->wishlist_id = $wishlist->id;
        $product->product_id = $id;
        $product->variation = $variation;
        $product->extra_options = $extra;
        $product->booking = $booking;
        $db->insertObject('#__gridbox_store_wishlist_products', $product);
        $product->id = $db->insertid();
        if (!empty($attachments)) {
            $this->setProductAttachment($attachments, $product->id, $wishlist->id);
        }

        return $data;
    }

    public function setProductAttachment($attachments, $product_id, $wishlist_id = 0, $cart_id = 0)
    {
        $db = JFactory::getDbo();
        foreach ($attachments as $attachment) {
            $query = $db->getQuery(true)
                ->update('#__gridbox_store_cart_attachments_map')
                ->set('wishlist_id = '.$wishlist_id)
                ->set('product_id = '.$product_id)
                ->set('cart_id = '.$cart_id)
                ->where('id = '.$attachment->id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function clearWishlist($wishlist_id, $product_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_wishlist_products')
            ->where('wishlist_id = '.$wishlist_id);
        if (!empty($product_id)) {
            $query->where('id = '.$product_id);
        }
        $db->setQuery($query)
            ->execute();
        gridboxHelper::$storeHelper->removeProductAttachment($product_id, 0, $wishlist_id);
    }

    public function setProductQuantity($product, $data, $quantity)
    {
        $db = JFactory::getDbo();
        $product->quantity += $quantity;
        if (isset($data->product_type) && $data->product_type == 'digital' && $product->quantity > 1) {
            $product->quantity = 1;
        }
        if ($data->stock !== '' && $product->quantity > $data->stock) {
            $product->quantity = $data->stock * 1;
        }
        if ($data->app_type == 'booking') {
            $product->quantity = 1;
        }
        if ($data->stock !== '') {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_products')
                ->where('cart_id = '.$product->cart_id)
                ->where('variation = '.$db->quote($product->variation))
                ->where('product_id = '.$product->product_id)
                ->where('id <> '.$product->id);
            $db->setQuery($query);
            $products = $db->loadObjectList();
            $qty = 0;
            foreach ($products as $obj) {
                $qty += $obj->quantity;
            }
            if ($qty + $product->quantity > $data->stock * 1) {
                $product->quantity = $data->stock - $qty;
            }
        }
        $db->updateObject('#__gridbox_store_cart_products', $product, 'id');
    }

    public function getProductData($id, $variation = '')
    {
        $db = JFactory::getDbo();
        $data = gridboxHelper::$storeHelper->getProductData($id);
        $data->images = [];
        if (!empty($variation)) {
            $map = gridboxHelper::$storeHelper->getProductVariationsMap($data->product_id);
            $images = new stdClass();
            foreach ($map as $value) {
                $images->{$value->option_key} = json_decode($value->images);
            }
            $vars = explode('+', $variation);
            foreach ($vars as $value) {
                if (!empty($images->{$value})) {
                    $data->images = $images->{$value};
                }
            }
            foreach ($data->variations->{$variation} as $key => $value) {
                $data->{$key} = $value;
            }
        }

        return $data;
    }

    public function getProduct($cid, $data, $var, $extra = '{}', $renew = 0, $type = '', $plan = '', $up = 0, $booking = '{}')
    {
        $db = JFactory::getDbo();
        if ($type == 'subscription') {
            $product = null;
        } else {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_products')
                ->where('cart_id = '.$cid)
                ->where('variation = '.$db->quote($var))
                ->where('extra_options = '.$db->quote($extra))
                ->where('renew_id = '.$renew)
                ->where('product_id = '.$data->product_id);
            if ($booking != '{}') {
                $query->where('booking = '.$db->quote($booking));
            }
            $db->setQuery($query);
            $product = $db->loadObject();
        }
        if ($data->app_type == 'booking' && $data->booking->type == 'single' && $data->booking->single->type == 'group'
            && $data->booking->single->time == 'yes' && !$product) {
            $obj = json_decode($booking);
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_products')
                ->where('cart_id = '.$cid)
                ->where('variation = '.$db->quote($var))
                ->where('extra_options = '.$db->quote($extra))
                ->where('renew_id = '.$renew)
                ->where('product_id = '.$data->product_id);
            $db->setQuery($query);
            $products = $db->loadObjectList();
            foreach ($products as $prod) {
                $object = json_decode($prod->booking);
                if ($obj->dates[0] == $object->dates[0] && $obj->time == $object->time) {
                    $product = $prod;
                    $product->booking = $booking;
                    break;
                }
            }
        }
        if ($type == 'subscription' && !empty($up)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_products')
                ->where('cart_id = '.$cid)
                ->where('(renew_id = '.$up.' OR upgrade_id = '.$up.')');
            $db->setQuery($query);
            $objects = $db->loadObjectList();
            foreach ($objects as $object) {
                gridboxHelper::removeProductFromCart($object->id, $cid);
            }
        } else if ($type == 'subscription' && !empty($renew)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_cart_products')
                ->where('cart_id = '.$cid)
                ->where('upgrade_id = '.$renew);
            $db->setQuery($query);
            $objects = $db->loadObjectList();
            foreach ($objects as $object) {
                gridboxHelper::removeProductFromCart($object->id, $cid);
            }
        }
        if (!$product) {
            $product = new stdClass();
            $product->cart_id = $cid;
            $product->product_id = $data->product_id;
            $product->variation = $var;
            $product->extra_options = $extra;
            $product->renew_id = $renew;
            $product->plan_key = $plan;
            $product->upgrade_id = $up;
            $product->quantity = 0;
            $product->booking = $booking;
            $db->insertObject('#__gridbox_store_cart_products', $product);
            $product->id = $db->insertid();
        }

        return $product;
    }

    public function removeExtraOptionCart($cart_id, $product_id, $key, $field_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_cart_products')
            ->where('cart_id = '.$cart_id)
            ->where('id = '.$product_id);
        $db->setQuery($query);
        $product = $db->loadObject();
        $extra = json_decode($product->extra_options);
        if (isset($extra->{$key})) {
            unset($extra->{$key});
        }
        $product->extra_options = json_encode($extra);
        $db->updateObject('#__gridbox_store_cart_products', $product, 'id');
    }

    public function removeExtraOptionWishlist($wishlist_id, $product_id, $key, $field_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_wishlist_products')
            ->where('wishlist_id = '.$wishlist_id)
            ->where('id = '.$product_id);
        $db->setQuery($query);
        $product = $db->loadObject();
        $extra = json_decode($product->extra_options);
        if (isset($extra->{$key})) {
            unset($extra->{$key});
        }
        $product->extra_options = json_encode($extra);
        $db->updateObject('#__gridbox_store_wishlist_products', $product, 'id');
    }

    public function uploadDigitalFile($file, $id)
    {
        $obj = new stdClass();
        if (isset($file['error']) && $file['error'] == 0) {
            $ext = strtolower(JFile::getExt($file['name']));
            $dir = gridboxHelper::$storeHelper->getDigitalFolder($id);
            if (!JFolder::exists($dir)) {
                JFolder::create($dir);
            }
            $name = str_replace('.'.$ext, '', $file['name']);
            $filename = gridboxHelper::replace($name);
            $filename = JFile::makeSafe($filename);
            $name = str_replace('-', '', $filename);
            $name = str_replace('.', '', $name);
            if ($name == '') {
                $filename = date("Y-m-d-H-i-s").'.'.$ext;
            }
            $i = 2;
            $name = $filename;
            while (JFile::exists($dir.$name.'.'.$ext)) {
                $name = $filename.'-'.($i++);
            }
            $filename = $name.'.'.$ext;
            move_uploaded_file($file['tmp_name'], $dir.$filename);
            $obj = new stdClass();
            $obj->name = $file['name'];
            $obj->filename = $filename;
        } else {
            $obj->error = 'ba-alert';
            $obj->msg = JText::_('NOT_ALLOWED_FILE_SIZE');
        }

        return $obj;
    }

    public function downloadDigitalFile($token)
    {
        $db = JFactory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('d.product_id, d.digital_file, op.id, o.status, o.user_id')
            ->from('#__gridbox_store_order_products AS op')
            ->where('op.product_token = '.$db->quote($token))
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = op.product_id')
            ->leftJoin("#__gridbox_store_orders AS o ON op.order_id = o.id")
            ->leftJoin("#__gridbox_pages AS p ON d.product_id = p.id");
        $db->setQuery($query);
        $product = $db->loadObject();
        $user_id = JFactory::getUser()->id;
        if ($product->status == 'completed' && !empty($product->digital_file)/* && $user_id == $product->user_id*/) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_license')
                ->where('product_id = '.$product->id);
            $db->setQuery($query);
            $license = $db->loadObject();
            $digital = json_decode($product->digital_file);
            $type = isset($digital->file->type) ? $digital->file->type : '';
            if ($type == 'link') {
                $file = $digital->file->name;
            } else {
                $folder = gridboxHelper::$storeHelper->getDigitalFolder($product->product_id);
                $file = $folder.$digital->file->filename;
            }
            $expired = false;
            $limit = $license->limit == '' || $license->downloads < $license->limit;
            if (!empty($license->expires)) {
                $expired = $date > $license->expires;
            }
            if ((($type == 'link' && !empty($file)) || ($type != 'link' && JFile::exists($file))) && !$expired && $limit) {
                $query = $db->getQuery(true)
                    ->update('#__gridbox_store_order_license')
                    ->set('downloads = '.($license->downloads * 1 + 1))
                    ->where('id = '.$license->id);
                $db->setQuery($query)
                    ->execute();
                $this->downloadFile($type, $digital->file->name, $file);
            }
        }
        return gridboxHelper::raiseError(404, JText::_('DOWNLOAD_FILE_NOT_AVAILABLE'));
    }

    public function downloadSubscriptionFile($s_id, $p_id)
    {
        $db = JFactory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $user_id = JFactory::getUser()->id;
        $query = $db->getQuery(true)
            ->select('d.subscription')
            ->from('#__gridbox_store_subscriptions AS s')
            ->leftJoin('#__gridbox_pages AS p ON p.id = s.product_id')
            ->leftJoin('#__gridbox_store_product_data AS d ON p.id = d.product_id')
            ->where('s.id = '.$s_id)
            ->where('s.action <> '.$db->quote('groups'))
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('(s.expires = '.$db->quote('').' OR s.expires > '.$date.')')
            ->where('s.user_id = '.$user_id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!$obj) {
            return gridboxHelper::raiseError(404, JText::_('DOWNLOAD_FILE_NOT_AVAILABLE'));
        }
        $subscription = json_decode($obj->subscription);
        if (!in_array($p_id, $subscription->products)) {
            return gridboxHelper::raiseError(404, JText::_('DOWNLOAD_FILE_NOT_AVAILABLE'));
        }
        $query = $db->getQuery(true)
            ->select('d.digital_file, d.product_id')
            ->from('#__gridbox_store_product_data AS d')
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('d.product_id = '.$p_id);
        $db->setQuery($query);
        $product = $db->loadObject();
        if (!$product) {
            return gridboxHelper::raiseError(404, JText::_('DOWNLOAD_FILE_NOT_AVAILABLE'));
        }
        $digital = json_decode($product->digital_file);
        $type = isset($digital->file->type) ? $digital->file->type : '';
        if ($type == 'link') {
            $file = $digital->file->name;
        } else {
            $folder = gridboxHelper::$storeHelper->getDigitalFolder($product->product_id);
            $file = $folder.$digital->file->filename;
        }
        if ((($type == 'link' && !empty($file)) || ($type != 'link' && JFile::exists($file)))) {
            $this->downloadFile($type, $digital->file->name, $file);
        }
    }

    public function downloadFile($type, $name, $file)
    {
        if ($type == 'link') {
            header('Location: '.$file);
        } else {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
        }
        exit;
    }
    
    public function getItem($id = null)
    {
        
    }
}