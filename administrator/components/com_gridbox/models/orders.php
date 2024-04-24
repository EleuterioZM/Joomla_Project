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

class gridboxModelOrders extends JModelList
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'state', 'publish_up', 'publish_down'
            ];
        }
        parent::__construct($config);
    }

    public function checkMatchedCsv($file, $overwrite, $matched)
    {
        $data = $this->getCSVData($file);
        $response = $this->getCSVResponse();
        $map = new stdClass();
        $cells = $this->getMatchedCells($map, $matched);
        $this->checkRequiredCSVColumns($cells, $response);
        $fields = $this->getMatchedFields($map, $matched);
        $this->checkCSVImport($data, $cells, $fields, $map, $overwrite, $response);
    }

    public function checkGridboxCsv($file, $overwrite)
    {
        $data = $this->getCSVData($file);
        $response = $this->getCSVResponse();
        $keys = $data[0];
        $map = new stdClass();
        $cells = $this->getGridboxCells($map, $keys);
        $this->checkRequiredCSVColumns($cells, $response);
        $fields = $this->getGridboxFields($map, $keys);
        $this->checkCSVImport($data, $cells, $fields, $map, $overwrite, $response);
    }

    public function getGridboxFields($map, $keys)
    {
        $fields = $this->getAppFields();
        foreach ($fields as $key => $field) {
            if (!in_array($field->title, $keys)) {
                unset($fields->{$key});
            } else {
                $map->{$field->id} = array_search($field->title, $keys);
            }
        }

        return $fields;
    }

    public function getGridboxCells($map, $keys)
    {
        $cells = $this->getCSVAppCells();
        foreach ($cells as $key => $cell) {
            if (!in_array($cell, $keys)) {
                unset($cells[$key]);
            } else {
                $map->{$key} = array_search($cell, $keys);
            }
        }

        return $cells;
    }

    public function checkRequiredCSVColumns($cells, $response = null)
    {
        $required = $this->getRequiredAppCells();
        $errors = 0;
        foreach ($required as $key => $cell) {
            if (!isset($cells[$key])) {
                $errors++;
                $this->raiseCsvError(1, $cell, 'REQUIRED_COLUMN_NOT_PRESENT', $response);
            }
        }
        if ($response && $response->errors != 0) {
            $this->submitCSVAnswer($response);
        }

        return $errors;
    }

    public function submitCSVAnswer($obj)
    {
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function getRequiredAppCells()
    {
        return ['title' => 'Title', 'product_type' => 'Product Type'];
    }

    public function getCSVData($file)
    {
        $handle = fopen($file, "r");
        $data = array();
        while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
        JFile::delete($file);

        return $data;
    }

    public function getCSVResponse()
    {
        $response = new stdClass();
        $response->new = $response->updated = $response->errors = 0;
        $response->log = [];

        return $response;
    }

    public function getMatchedCells($map, $matched)
    {
        $cells = $this->getCSVAppCells();
        foreach ($cells as $key => $cell) {
            if (!isset($matched->{$key})) {
                unset($cells[$key]);
            } else {
                $map->{$key} = $matched->{$key} * 1;
            }
        }

        return $cells;
    }

    public function getMatchedFields($map, $matched)
    {
        $fields = $this->getAppFields();
        foreach ($fields as $key => $field) {
            if (!isset($matched->{$field->id})) {
                unset($fields->{$key});
            } else {
                $map->{$field->id} = $matched->{$field->id} * 1;
            }
        }

        return $fields;
    }

    public function checkCSVImport($data, $cells, $fields, $map, $overwrite, $response)
    {
        $db = JFactory::getDbo();
        $keys = $data[0];
        $line = 1;
        $orders = [];
        $n = count($data);
        for ($i = 1; $i < $n; $i++) {
            $line++;
            if (empty($orders)) {
                $order = new stdClass();
                $order->data = $data[$i];
                $order->line = $line;
                $order->products = [];
                $orders[] = $order;
            } else if (isset($map->{'order_number'}) && !empty($data[$i][$map->{'order_number'}])) {
                $order = new stdClass();
                $order->data = $data[$i];
                $order->line = $line;
                $order->products = [];
                $orders[] = $order;
            } else if (!empty($orders)) {
                $order = end($orders);
            }
            $j = $map->{'product_type'};
            $product_type = $data[$i][$j];
            switch ($product_type) {
                case '':
                case 'Product':
                case 'Digital Product':
                case 'Subscription':
                    $product = new stdClass();
                    $product->data = $data[$i];
                    $product->product_type = $product_type;
                    $product->line = $line;
                    $product->options = [];
                    $product->variations = [];
                    $product->extra_options = [];
                    $order->products[] = $product;
                    break;
                case 'Option':
                case 'Variation':
                case 'Extra Options':
                    if ($product_type == 'Option') {
                        $key = 'options';
                    } else if ($product_type == 'Variation') {
                        $key = 'variations';
                    } else {
                        $key = 'extra_options';
                    }
                    $product = end($order->products);
                    if ($product) {
                        $obj = new stdClass();
                        $obj->data = $data[$i];
                        $obj->line = $line;
                        $product->{$key}[] = $obj;
                    } else {
                        $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    }
                    break;
                default:
                    $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    break;
            }
        }
        $statuses = gridboxHelper::getStatuses();
        foreach ($orders as $order) {
            $order_number = '';
            foreach ($cells as $key => $cell) {
                $i = $map->{$key};
                $value = $order->data[$i];
                if ($key == 'order_number') {
                    $order_number = $value;
                }
                switch ($key) {
                    case 'date':
                        if ($value != '' && !DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
                            $this->raiseCsvError($order->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'status':
                        $flag = false;
                        foreach ($statuses as $status) {
                            if ($status->title == $value) {
                                $flag = true;
                                break;
                            }
                        }
                        if (!$flag) {
                            $this->raiseCsvError($order->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                        break;
                    case 'quantity':
                        if ($value != '' && (!is_numeric($value) || !is_int($value * 1))) {
                            $this->raiseCsvError($order->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'price':
                        if ($value != '' && !is_numeric($value)) {
                            $this->raiseCsvError($order->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'payment_method':
                        if (!empty($value)) {
                            $query = $db->getQuery(true)
                                ->select('COUNT(id)')
                                ->from('#__gridbox_store_payment_methods')
                                ->where('title = '.$db->quote($value));
                            $db->setQuery($query);
                            $count = $db->loadResult();
                        }
                        if (!empty($value) && $count == 0) {
                            $this->raiseCsvError($order->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                        break;
                    case 'shipping_method':
                        if (!empty($value)) {
                            $query = $db->getQuery(true)
                                ->select('COUNT(id)')
                                ->from('#__gridbox_store_shipping')
                                ->where('title = '.$db->quote($value));
                            $db->setQuery($query);
                            $count = $db->loadResult();
                        }
                        if (!empty($value) && $count == 0) {
                            $this->raiseCsvError($order->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                        break;
                    case 'coupon_code':
                        if (!empty($value)) {
                            $query = $db->getQuery(true)
                                ->select('COUNT(id)')
                                ->from('#__gridbox_store_promo_codes')
                                ->where('code = '.$db->quote($value));
                            $db->setQuery($query);
                            $count = $db->loadResult();
                        }
                        if (!empty($value) && $count == 0) {
                            $this->raiseCsvError($order->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                        break;
                    case 'user':
                        if (!empty($value)) {
                            $query = $db->getQuery(true)
                                ->select('COUNT(id)')
                                ->from('#__users')
                                ->where('username = '.$db->quote($value));
                            $db->setQuery($query);
                            $count = $db->loadResult();
                        }
                        if (!empty($value) && $count == 0) {
                            $this->raiseCsvError($order->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                        break;
                }
            }
            foreach ($order->products as $product) {
                $obj = null;
                $i = $map->title;
                $value = $product->data[$i];
                if (!empty($value)) {
                    $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__gridbox_pages')
                        ->where('title = '.$db->quote($value));
                    $db->setQuery($query);
                    $obj = $db->loadObject();
                }
                if (!$obj) {
                    $this->raiseCsvError($product->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                }
                if (!$obj) {
                    continue;
                }
                $productFields = [];
                if (isset($map->options)) {
                    foreach ($product->options as $option) {
                        $i = $map->options;
                        $value = $option->data[$i];
                        $array = explode(' / ', $value);
                        if (count($array) != 2) {
                            $this->raiseCsvError($option->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            continue;
                        }
                        $title = $array[0];
                        if (!isset($productFields[$title])) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gridbox_store_products_fields')
                                ->where('title = '.$db->quote($title));
                            $db->setQuery($query);
                            $obj = $db->loadObject();
                            $productFields[$title] = $obj;
                        }
                        if (!isset($productFields[$title]->id)) {
                            $this->raiseCsvError($option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            continue;
                        }
                        $list = json_decode($productFields[$title]->options);
                        $flag = false;
                        foreach ($list as $li) {
                            if ($li->title == $array[1]) {
                                $productFields[$title]->items[] = $array[1];
                                $flag = true;
                                break;
                            }
                        }
                        if (!$flag) {
                            $this->raiseCsvError($option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                    }
                }
                $rows = [];
                foreach ($productFields as $productField) {
                    $rows[] = $productField;
                }
                if (isset($map->variation)) {
                    foreach ($product->variations as $variation) {
                        $i = $map->variation;
                        $value = $variation->data[$i];
                        $array = explode(' / ', $value);
                        if (count($array) == 0) {
                            $this->raiseCsvError($variation->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            continue;
                        }
                        foreach ($array as $j => $variable) {
                            if (!isset($rows[$j]->items) || !in_array($variable, $rows[$j]->items)) {
                                $this->raiseCsvError($variation->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            }
                        }
                    }
                }
                if (isset($map->extra_options)) {
                    $extra_options = [];
                    foreach ($product->extra_options as $extra_option) {
                        $i = $map->extra_options;
                        $value = $extra_option->data[$i];
                        $array = explode(' / ', $value);
                        if (count($array) != 2) {
                            $this->raiseCsvError($extra_option->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            continue;
                        }
                        $title = $array[0];
                        if (!isset($extra_options[$title])) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gridbox_store_products_fields')
                                ->where('title = '.$db->quote($title));
                            $db->setQuery($query);
                            $extra_options[$title] = $db->loadObject();
                        }
                        if (!isset($extra_options[$title]->id)) {
                            $this->raiseCsvError($extra_option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            continue;
                        }
                        $item = $extra_options[$title];
                        $list = json_decode($item->options);
                        $flag = false;
                        foreach ($list as $li) {
                            if ($li->title == $array[1]) {
                                $flag = true;
                                break;
                            }
                        }
                        if (!$flag) {
                            $this->raiseCsvError($extra_option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        }
                    }
                }
            }
            if ($overwrite == 1 && !empty($order_number)) {
                $query = $db->getQuery(true)
                    ->select('COUNT(id)')
                    ->from('#__gridbox_store_orders')
                    ->where('order_number = '.$db->quote($order_number));
                $db->setQuery($query);
                $count = $db->loadResult();
                $response->{$count == 0 ? 'new' : 'updated'}++;
            } else {
                $response->new++;
            }
        }
        $this->submitCSVAnswer($response);
    }

    public function raiseCsvError($line, $cell, $code, $response)
    {
        if ($response) {
            $response->errors++;
            $error = new stdClass();
            $error->line = $line;
            $error->column = $cell;
            $error->code = JText::_($code);
            $response->log[] = $error;
        }
    }

    public function exportCSV($pks, $cells, $tmp_path)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_store_orders')
            ->where('published = 1');
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('id IN ('.$str.')');
        }
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $orders = [];
        foreach ($array as $obj) {
            if ($this->checkBooking($obj)) {
                continue;
            }
            $orders[] = $this->getOrder($obj->id);
        }
        $fields = $this->getCSVAppCells();
        $infos = $this->getAppFields();
        $export = $this->getExportFields();
        foreach ($fields as $key => $field) {
            if (!in_array($key, $cells)) {
                unset($fields[$key]);
            }
        }
        foreach ($infos as $key => $field) {
            if (!in_array($key, $cells)) {
                unset($fields[$key]);
            }
        }
        foreach ($export as $key => $field) {
            if (!in_array($key, $cells)) {
                unset($fields[$key]);
            }
        }
        $list = [];
        $row = [];
        foreach ($fields as $field) {
            $row[] = $field;
        }
        foreach ($infos as $key => $field) {
            $row[] = $field->title;
        }
        foreach ($export as $key => $field) {
            $row[] = $field;
        }
        $list[] = $row;
        $statuses = gridboxHelper::getStatuses();
        foreach ($orders as $order) {
            $row = [];
            $product = $order->products[0];
            $product_type = $product->product_type;
            foreach ($fields as $field => $title) {
                $value = '';
                switch ($field) {
                    case 'order_number':
                    case 'date':
                        $value = $order->{$field};
                        break;
                    case 'status':
                        $value = isset($statuses->{$order->status}) ? $statuses->{$order->status}->title : $statuses->undefined->title;
                        break;
                    case 'payment_method':
                        $value = isset($order->payment->title) ? $order->payment->title : '';
                        break;
                    case 'shipping_method':
                        $value = isset($order->shipping->title) ? $order->shipping->title : '';
                        break;
                    case 'branch':
                        $value = isset($order->shipping->carrier) ? $order->shipping->carrier : '';
                        break;
                    case 'coupon_code':
                        $value = isset($order->promo->code) ? $order->promo->code : '';
                        break;
                    case 'user':
                        $value = isset($order->user->username) ? $order->user->username : '';
                        break;
                    case 'sku':
                    case 'title':
                    case 'quantity':
                    case 'price':
                        $value = $product->{$field};
                        break;
                    case 'product_type':
                        $value = $product_type == 'digital' ? 'Digital Product' : ($product_type == 'subscription' ? 'Subscription' : 'Product');
                        break;
                }
                $row[] = $value;
            }

            foreach ($infos as $info) {
                $value = '';
                foreach ($order->info as $obj) {
                    if ($obj->customer_id == $info->id) {
                        $value = $obj->value;
                    }
                    if ($info->type == 'country' && !empty($value)) {
                        $object = json_decode($value);
                        if (!empty($object->country)) {
                            $value = $object->country;
                        }
                        if (!empty($object->country) && !empty($object->region)) {
                            $value .= ' / '.$object->region;
                        }
                    }
                }
                $row[] = $value;
            }
            foreach ($export as $field => $title) {
                $value = '';
                switch ($field) {
                    case 'subtotal':
                    case 'total':
                    case 'tax':
                        $value = $order->{$field};
                        break;
                    case 'discount':
                        $value = isset($order->promo->discount) ? $order->promo->discount : '';
                        break;
                    case 'shipping_price':
                        $value = isset($order->shipping->price) ? $order->shipping->price : '';
                        break;
                }
                $row[] = $value;
            }
            $list[] = $row;
            $variations = [];
            if (isset($fields['options']) || isset($fields['variation'])) {
                foreach ($product->variations as $variation) {
                    $row = [];
                    foreach ($fields as $field => $title) {
                        $value = '';
                        switch ($field) {
                            case 'options':
                                $value = $variation->title.' / '.$variation->value;
                                $variations[] = $variation->value;
                                break;
                            case 'product_type':
                                $value = 'Option';
                                break;
                        }
                        $row[] = $value;
                    }
                    $list[] = $row;
                }
                
            }
            if (isset($fields['variation']) && !empty($variations)) {
                $row = [];
                foreach ($fields as $field => $title) {
                    $value = '';
                    switch ($field) {
                        case 'variation':
                            $value = implode(' / ', $variations);
                            break;
                        case 'product_type':
                            $value = 'Variation';
                            break;
                    }
                    $row[] = $value;
                }
                $list[] = $row;
            }
            if (isset($fields['extra_options']) && isset($product->extra_options->items)) {
                foreach ($product->extra_options->items as $item) {
                    foreach ($item->values as $obj) {
                        $row = [];
                        foreach ($fields as $field => $title) {
                            $value = '';
                            switch ($field) {
                                case 'extra_options':
                                    $value = $item->title.' / '.$obj->value;
                                    break;
                                case 'price':
                                    $value = $obj->price;
                                    break;
                                case 'product_type':
                                    $value = 'Extra Options';
                                    break;
                            }
                            $row[] = $value;
                        }
                        $list[] = $row;
                    }
                    
                }
            }
            foreach ($order->products as $key => $product) {
                if ($key == 0) {
                    continue;
                }
                $row = [];
                $product_type = $product->product_type;
                foreach ($fields as $field => $title) {
                    $value = '';
                    switch ($field) {
                        case 'sku':
                        case 'title':
                        case 'quantity':
                        case 'price':
                            $value = $product->{$field};
                            break;
                        case 'product_type':
                            $value = $product_type == 'digital' ? 'Digital Product' : ($product_type == 'subscription' ? 'Subscription' : 'Product');
                            break;
                    }
                    $row[] = $value;
                }
                $list[] = $row;
                $variations = [];
                if (isset($fields['options']) || isset($fields['variation'])) {
                    foreach ($product->variations as $variation) {
                        $row = [];
                        foreach ($fields as $field => $title) {
                            $value = '';
                            switch ($field) {
                                case 'options':
                                    $value = $variation->title.' / '.$variation->value;
                                    $variations[] = $variation->value;
                                    break;
                                case 'product_type':
                                    $value = 'Option';
                                    break;
                            }
                            $row[] = $value;
                        }
                        $list[] = $row;
                    }
                }
                if (isset($fields['variation']) && !empty($variations)) {
                    $row = [];
                    foreach ($fields as $field => $title) {
                        $value = '';
                        switch ($field) {
                            case 'variation':
                                $value = implode(' / ', $variations);
                                break;
                            case 'product_type':
                                $value = 'Variation';
                                break;
                        }
                        $row[] = $value;
                    }
                    $list[] = $row;
                }
                if (isset($fields['extra_options']) && isset($product->extra_options->items)) {
                    foreach ($product->extra_options->items as $item) {
                        foreach ($item->values as $obj) {
                            $row = [];
                            foreach ($fields as $field => $title) {
                                $value = '';
                                switch ($field) {
                                    case 'extra_options':
                                        $value = $item->title.' / '.$obj->value;
                                        break;
                                    case 'price':
                                        $value = $obj->price;
                                        break;
                                    case 'product_type':
                                        $value = 'Extra Options';
                                        break;
                                }
                                $row[] = $value;
                            }
                            $list[] = $row;
                        }
                        
                    }
                }
            }
        }
        $file = $tmp_path.'/gridbox-orders-'.time().'.csv';
        $fp = fopen($file, 'w');
        foreach ($list as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        return $file;
    }

    public function getAppCells($pks)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->cells = $this->getCSVAppCells();
        $obj->fields = $this->getAppFields();
        $obj->export = $this->getExportFields();
        $str = json_encode($obj);
        print_r($str);exit();
    }

    public function getExportFields()
    {
        $cells = ['subtotal' => 'Subtotal', 'discount' => 'Discount', 'shipping_price' => 'Shipping price', 'tax' => 'Tax', 'total' => 'Total'];

        return $cells;
    }

    public function getAppFields()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_customer_info');
        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }

    public function getCSVAppCells()
    {
        $cells = ['order_number' => 'ID', 'date' => 'Date', 'status' => 'Status', 'sku' => 'SKU',
            'title' => 'Title', 'product_type' => 'Product Type',
            'options' => 'Option', 'variation' => 'Variation', 'extra_options' => 'Extra Options',
            'quantity' => 'Qty', 'price' => 'Price', 'payment_method' => 'Payment method',
            'shipping_method' => 'Shipping method', 'branch' => 'Branch',
            'coupon_code' => 'Coupon code', 'user' => 'User'
        ];

        return $cells;
    }

    public function importCSV($file, $overwrite, $matched, $type, $category)
    {
        $data = $this->getCSVData($file);
        $map = new stdClass();
        if ($type == 'gridbox') {
            $keys = $data[0];
            $cells = $this->getGridboxCells($map, $keys);
            $fields = $this->getGridboxFields($map, $keys);
        } else {
            $cells = $this->getMatchedCells($map, $matched);
            $fields = $this->getMatchedFields($map, $matched);
        }
        $errors = $this->checkRequiredCSVColumns($cells);
        if ($errors !== 0) {
            return;
        }
        $db = JFactory::getDbo();
        $keys = $data[0];
        $orders = [];
        $n = count($data);
        for ($i = 1; $i < $n; $i++) {
            if (empty($orders)) {
                $order = new stdClass();
                $order->data = $data[$i];
                $order->products = [];
                $orders[] = $order;
            } else if (isset($map->{'order_number'}) && !empty($data[$i][$map->{'order_number'}])) {
                $order = new stdClass();
                $order->data = $data[$i];
                $order->products = [];
                $orders[] = $order;
            } else if (!empty($orders)) {
                $order = end($orders);
            }
            $j = $map->{'product_type'};
            $product_type = $data[$i][$j];
            switch ($product_type) {
                case '':
                case 'Product':
                case 'Digital Product':
                case 'Subscription':
                    $product = new stdClass();
                    $product->data = $data[$i];
                    $product->product_type = $product_type;
                    $product->options = [];
                    $product->variations = [];
                    $product->extra_options = [];
                    $order->products[] = $product;
                    break;
                case 'Option':
                case 'Variation':
                case 'Extra Options':
                    if ($product_type == 'Option') {
                        $key = 'options';
                    } else if ($product_type == 'Variation') {
                        $key = 'variations';
                    } else {
                        $key = 'extra_options';
                    }
                    $product = end($order->products);
                    if ($product) {
                        $obj = new stdClass();
                        $obj->data = $data[$i];
                        $product->{$key}[] = $obj;
                    } else {
                        $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    }
                    break;
                default:
                    $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    break;
            }
        }
        $statuses = gridboxHelper::getStatuses();
        $config = JFactory::getConfig();
        foreach ($orders as $order) {
            if (empty($order->products)) {
                continue;
            }
            $cart = new stdClass();
            $cart->country = $cart->region = '';
            $cart->products = [];
            $cart->total = $cart->subtotal = $cart->discount = $cart->net_amount = $cart->tax = 0;
            if ($overwrite == 1 && isset($map->order_number)) {
                $value = $order->data[$map->order_number];
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_orders')
                    ->where('published = 1')
                    ->where('order_number = '.$db->quote($value));
                $db->setQuery($query);
                $order->item = $db->loadObject();
            }
            if (!isset($order->item)) {
                $order->item = new stdClass();
                $order->published = 1;
            }
            $items = [];
            if (isset($order->item->id)) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_order_products')
                    ->where('order_id = '.$order->item->id);
                $db->setQuery($query);
                $items = $db->loadObjectList();
            }
            $promo = null;
            if (isset($map->coupon_code) && !empty($order->data[$map->coupon_code])) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_promo_codes')
                    ->where('code = '.$db->quote($order->data[$map->coupon_code]));
                $db->setQuery($query);
                $promo = $db->loadObject();
            }
            foreach ($order->products as $obj) {
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__gridbox_pages')
                    ->where('title = '.$db->quote($obj->data[$map->title]))
                    ->where('page_category <> '.$db->quote('trashed'));
                $db->setQuery($query);
                $item = $db->loadObject();
                if (!$item) {
                    continue;
                }
                $item = gridboxHelper::$storeHelper->getProductData($item->id);
                $variations = [];
                foreach ($obj->options as $option) {
                    if (!isset($map->options)) {
                        continue;
                    }
                    $value = $option->data[$map->options];
                    $array = explode(' / ', $value);
                    if (count($array) != 2) {
                        continue;
                    }
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_store_products_fields')
                        ->where('title = '.$db->quote($array[0]));
                    $db->setQuery($query);
                    $field = $db->loadObject();
                    if (!$field) {
                        continue;
                    }
                    $options = json_decode($field->options);
                    foreach ($options as $object) {
                        if ($object->title == $array[1]) {
                            $variations[] = $object->key;
                            break;
                        }
                    }
                }
                if (count($obj->options) != count($variations)) {
                    continue;
                }
                $variation = implode('+', $variations);
                $product = new stdClass();
                foreach ($items as $item) {
                    if ($item->title == $obj->data[$map->title]) {
                        $product->id = $item->id;
                        $product->renew_id = $item->renew_id;
                        $product->plan_key = $item->plan_key;
                        $product->upgrade_id = $item->upgrade_id;
                        $product->upgrade_price = $item->upgrade_price;
                        break;
                    }
                }
                $product->variations = [];
                foreach ($variations as $value) {
                    $query = $db->getQuery(true)
                        ->select('fd.value, fd.color, fd.image, f.title, f.field_type')
                        ->from('#__gridbox_store_products_fields_data AS fd')
                        ->where('fd.option_key = '.$db->quote($value))
                        ->leftJoin('#__gridbox_store_products_fields AS f ON f.id = fd.field_id');
                    $db->setQuery($query);
                    $variationObj = $db->loadObject();
                    $product->variations[] = $variationObj;
                }
                if (!empty($variation) && isset($item->variations->{$variation})) {
                    foreach ($item->variations->{$variation} as $ind => $value) {
                        $item->{$ind} = $value;
                    }
                }
                $validPromo = $promo && gridboxHelper::checkPromoCode($promo, $product);
                $extra_options = new stdClass();
                $extra_options->count = 0;
                $extra_options->price = 0;
                $extra_options->items = new stdClass();
                foreach ($obj->extra_options as $extra_option) {
                    if (!isset($map->extra_options)) {
                        continue;
                    }
                    $value = $extra_option->data[$map->extra_options];
                    $array = explode(' / ', $value);
                    foreach ($item->extra_options as $field_id => $extra) {
                        if ($extra->title != $array[0]) {
                            continue;
                        }
                        foreach ($extra->items as $object) {
                            if ($object->title == $array[1]) {
                                if (!isset($extra_options->items->{$field_id})) {
                                    $extraObject = new stdClass();
                                    $extraObject->title = $extra->title;
                                    $extraObject->required = $extra->required == '1';
                                    $extraObject->values = new stdClass();
                                    $extra_options->items->{$field_id} = $extraObject;
                                } else {
                                    $extraObject = $extra_options->items->{$field_id};
                                }
                                $extra_options->count++;
                                $option = new stdClass();
                                if ($extra->type == 'file') {
                                    $extraObject->attachments = $value->files;
                                    $key = 0;
                                } else if ($extra->type == 'textarea' || $extra->type == 'textinput') {
                                    $key = 0;
                                }
                                $option->price = $object->price;
                                $option->weight = isset($object->weight) ? $object->weight : '';
                                if ($extra->type == 'file') {
                                    $extraObject->charge = $extra->file_options->charge;
                                    $extraObject->quantity = $extra->file_options->quantity;
                                    $option->price = $extra->file_options->charge && !empty($option->price) ? ($option->price * count($value->files)) : $option->price;
                                } else if ($extra->type == 'textarea' || $extra->type == 'textinput') {
                                    $option->value = $value->text;
                                    $extraObject->values->{$key} = $option;
                                } else {
                                    $option->value = $extra->items->{$key}->title;
                                    $extraObject->values->{$key} = $option;
                                }
                                if ($extra->type == 'file' && $extraObject->quantity) {
                                    $product->hasFileQty = true;
                                }
                                if (!empty($option->price)) {
                                    $extra_options->price += $option->price * 1;
                                }
                                break;
                            }
                        }
                    }
                }
                $product->extra_options = $extra_options;
                $product->title = $item->title;
                $product->image = $item->image;
                $product->product_id = $item->product_id;
                $product->quantity = isset($map->quantity) && !empty($obj->data[$map->quantity]) ? $obj->data[$map->quantity] : 1;
                $product->variation = $variation;
                $price = ($item->price + $product->extra_options->price) * $product->quantity;
                $sale_price = '';
                if ($item->sale_price !== '') {
                    $sale_price = ($item->sale_price + $product->extra_options->price) * $product->quantity;
                }
                $price = $sale_price !== '' ? $sale_price : $price;
                if ($validPromo) {
                    $value = $promo->discount;
                    $unit = $promo->unit;
                    $discount = $unit == '%' ? $price * ($value / 100) : $value;
                    $price -= $discount;
                    $cart->discount += $discount;
                }
                $product->price = $price;
                $product->sale_price = $sale_price;
                $product->sku = $item->sku;
                $product->tax = gridboxHelper::calculateProductTax($item->product_id, $price, $cart);
                $product->net_price = $price;
                $cart->subtotal += $price;
                if ($product->tax) {
                    $amount = $product->tax->amount;
                    $rate = $product->tax->rate;
                    if ($validPromo) {
                        $amount = gridboxHelper::$store->tax->mode == 'excl' ? $price * ($rate / 100) : $price - $price / ($rate / 100 + 1);
                    }
                    $cart->tax += $amount;
                    $product->net_price = gridboxHelper::$store->tax->mode == 'excl' ? $price : $price - $amount;
                }
                $cart->net_amount += $product->net_price * 1;
                $cart->total += $price;
                $product->product_type = $item->product_type;
                $cart->products[] = $product;
            }
            $date = isset($map->date) ? $order->data[$map->date] : (isset($order->item->date) ? $order->item->date : '');
            if ($date == '' || !DateTime::createFromFormat('Y-m-d H:i:s', $date)) {
                $offset = $config->get('offset');
                $tz = new DateTimeZone($offset);
                $dateTime = new DateTime('now', $tz);
                $date = $dateTime->format('Y-m-d H:i:s');
            }
            $order->item->date = $date;
            if (isset($map->status)) {
                $value = $order->data[$map->status];
                foreach ($statuses as $ind => $status) {
                    if ($status->title == $value) {
                        $order->item->status = $ind;
                        break;
                    }
                }
            }
            if (isset($map->user) && !empty($order->data[$map->user])) {
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__users')
                    ->where('username = '.$db->quote($order->data[$map->user]));
                $db->setQuery($query);
                $user = $db->loadResult();
                $order->item->user_id = $user ? $user : 0;
            }
            $total = $cart->total + (gridboxHelper::$store->tax->mode == 'excl' ? $cart->tax : 0);
            $shipping = null;
            if (isset($map->shipping_method) && !empty($order->data[$map->shipping_method])) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_shipping')
                    ->where('title = '.$db->quote($order->data[$map->shipping_method]));
                $db->setQuery($query);
                $obj = $db->loadObject();
                if ($obj) {
                    $tax = gridboxHelper::getStoreShippingTax($cart);
                    $obj = gridboxHelper::getStoreShippingItem($obj, $total, $tax, $cart);
                    $shipping = new stdClass();
                    $shipping->type = $obj->params->type;
                    $shipping->title = $obj->title;
                    $shipping->price = $obj->price;
                    $shipping->tax = $obj->tax;
                    $shipping->shipping_id = $obj->id;
                    $shipping->tax_title = $tax ? $tax->title : '';
                    $shipping->tax_rate = $tax ? $tax->rate : '';
                    if (isset($map->branch)) {
                        $shipping->carrier = $order->data[$map->branch];
                    }
                    $total = $obj->total;
                }
            }
            $order->item->subtotal = $cart->subtotal;
            $order->item->tax = $cart->tax;
            $order->item->tax_mode = gridboxHelper::$store->tax->mode;
            $order->item->total = $total;
            $currency = gridboxHelper::$storeHelper->getDefaultCurrency();
            $order->item->currency_symbol = $currency->symbol;
            $order->item->currency_position = $currency->position;
            if (empty($order->item->id)) {
                $order->item->unread = 0;
                $order->item->published = 1;
                $db->insertObject('#__gridbox_store_orders', $order->item);
                $order->item->id = $db->insertid();
                $order->item->order_number = gridboxHelper::$storeHelper->createOrderNumber($order->item->id);
            }
            $db->updateObject('#__gridbox_store_orders', $order->item, 'id');
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_orders_discount')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
            if (!empty($cart->discount)) {
                $discount = new stdClass();
                $discount->order_id = $order->item->id;
                $discount->promo_id = $promo ? $promo->id : 0;
                $discount->title = $promo ? $promo->title : '';
                $discount->code = $promo ? $promo->code : '';
                $discount->unit = $promo ? $promo->unit : '';
                $discount->discount = $$promo ? $promo->discount : '';
                $discount->value = $cart->discount;
                $order->discount = $discount;
                $db->insertObject('#__gridbox_store_orders_discount', $discount);
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_orders_shipping')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
            if ($shipping) {
                $shipping->order_id = $order->item->id;
                $order->shipping = $shipping;
                $db->insertObject('#__gridbox_store_orders_shipping', $shipping);
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_orders_payment')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
            if (isset($map->payment_method) && !empty($order->data[$map->payment_method])) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_payment_methods')
                    ->where('title = '.$db->quote($order->data[$map->payment_method]));
                $db->setQuery($query);
                $obj = $db->loadObject();
                $payment = new stdClass();
                $payment->order_id = $order->item->id;
                $payment->title = $obj->title;
                $payment->type = $obj->type;
                $payment->payment_id = $obj->id;
                $db->insertObject('#__gridbox_store_orders_payment', $payment);
            }
            $pks = [];
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_order_product_variations')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
            foreach ($cart->products as $obj) {
                if (isset($obj->id)) {
                    $pks[] = $obj->id;
                    continue;
                }
                $product = new stdClass();
                $product->order_id = $order->item->id;
                $product->title = $obj->title;
                $product->image = $obj->image;
                $product->product_id = $obj->product_id;
                $product->variation = $obj->variation;
                $product->quantity = $obj->quantity;
                $product->price = $obj->price;
                $product->sale_price = $obj->sale_price;
                $product->sku = $obj->sku;
                $product->tax = $obj->tax ? $obj->tax->amount : '';
                $product->tax_title = $obj->tax ? $obj->tax->title : '';
                $product->tax_rate = $obj->tax ? $obj->tax->rate : '';
                $product->net_price = $obj->net_price;
                $product->extra_options = json_encode($obj->extra_options);
                $product->product_type = isset($obj->product_type) ? $obj->product_type : '';
                $db->insertObject('#__gridbox_store_order_products', $product);
                $product->id = $db->insertid();
                $pks[] = $product->id;
                if ($product->product_type == 'digital') {
                    $product->product_token = hash('md5', date("Y-m-d H:i:s").'-'.$product->id);
                    $db->updateObject('#__gridbox_store_order_products', $product, 'id');
                    $digital = !empty($obj->digital_file) ? json_decode($obj->digital_file) : new stdClass();
                    $license = new stdClass();
                    $license->product_id = $product->id;
                    $license->order_id = $order->item->id;
                    $license->limit = isset($digital->max) ? $digital->max : '';
                    $license->expires = 'new';
                    $db->insertObject('#__gridbox_store_order_license', $license);
                }
                foreach ($obj->variations as $object) {
                    $variation = new stdClass();
                    $variation->product_id = $product->id;
                    $variation->order_id = $order->item->id;
                    $variation->title = $object->title;
                    $variation->value = $object->value;
                    $variation->color = $object->color;
                    $variation->image = $object->image;
                    $variation->type = $object->field_type;
                    $db->insertObject('#__gridbox_store_order_product_variations', $variation);
                }
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_order_products')
                ->where('id NOT IN ('.implode(', ', $pks).')')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
            $pks = [];
            foreach ($fields as $field) {
                if (!isset($map->{$field->id})) {
                    continue;
                }
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_order_customer_info')
                    ->where('order_id = '.$order->item->id)
                    ->where('customer_id = '.$field->id);
                $db->setQuery($query);
                $customer = $db->loadObject();
                $customer = $customer ? $customer : new stdClass();
                $customer->order_id = $order->item->id;
                $customer->customer_id = $field->id;
                $customer->title = $field->title;
                $customer->type = $field->type;
                $customer->value = $order->data[$map->{$field->id}];
                $customer->options = $field->options;
                $customer->invoice = $field->invoice;
                $customer->order_list = $field->order_list;
                if ($field->type == 'country' && !empty($customer->value)) {
                    $array = explode(' / ', $customer->value);
                    $value = new stdClass();
                    $value->country = $array[0];
                    $value->region = isset($array[1]) ? $array[1] : '';
                    $customer->value = json_encode($value);
                }
                $db->insertObject('#__gridbox_store_order_customer_info', $customer);
                $customer->id = $db->insertid();
                $pks[] = $customer->id;
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_order_customer_info')
                ->where('id NOT IN ('.implode(', ', $pks).')')
                ->where('order_id = '.$order->item->id);
            $db->setQuery($query)
                ->execute();
        }
        echo "{}";
    }

    public function getUserInfo($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_user_info')
            ->where('user_id = '.$id);
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $info = new stdClass();
        foreach ($list as $value) {
            $info->{$value->customer_id} = $value;
        }

        return $info;
    }

    public function getItem()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'string');
        $order = $this->getOrder($id);

        return $order;
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
        if (!empty($order->user_id)) {
            $query = $db->getQuery(true)
                ->select('id, username')
                ->from('#__users')
                ->where('id = '.$order->user_id);
            $db->setQuery($query);
            $order->user = $db->loadObject();
        }
        if ($order->unread == 1) {
            gridboxHelper::$storeHelper->setReadStatus($id);
        }
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
            ->from('#__gridbox_store_orders_payment')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $order->payment = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_products')
            ->where('order_id = '.$id);
        $db->setQuery($query);
        $order->products = $db->loadObjectList();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_products_fields');
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $fieldsData = [];
        foreach ($fields as $field) {
            $options = json_decode($field->options);
            foreach ($options as $option) {
                $option->value = $option->title;
                $option->title = $field->title;
                $option->type = $field->field_type;
                $fieldsData[$option->key] = $option;
            }
        }
        foreach ($order->products as $product) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_product_variations')
                ->where('product_id = '.$product->id);
            $db->setQuery($query);
            $product->variations = $db->loadObjectList();
            $info = [];
            foreach ($product->variations as $variation) {
                $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
            }
            $product->info = implode('/', $info);
            if ($product->product_type == 'booking') {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_bookings')
                    ->where('product_id = ' . $product->id);
                $db->setQuery($query);
                $product->booking = $db->loadObject();
                $product->booking->formated = (object)[
                    'start_date' => gridboxHelper::formatDate($product->booking->start_date),
                    'end_date' => !empty($product->booking->end_date) ? gridboxHelper::formatDate($product->booking->end_date) : ''
                ];
            }
            $query = $db->getQuery(true)
                ->select('p.title, p.intro_image AS image, d.*')
                ->from('#__gridbox_pages AS p')
                ->where('d.product_id = '.$product->product_id)
                ->where('p.page_category <> '.$db->quote('trashed'))
                ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id');
            $db->setQuery($query);
            $obj = $db->loadObject();
            if (!$obj) {
                continue;
            }
            $variations = json_decode($obj->variations);
            if (!empty($product->variation) && !isset($variations->{$product->variation})) {
                continue;
            }
            if (!empty($product->variation)) {
                $data = $variations->{$product->variation};
            } else {
                $data = new stdClass();
                $data->price = $obj->price;
                $data->sale_price = $obj->sale_price;
                $data->sku = $obj->sku;
                $data->stock = $obj->stock;
            }
            $min = !empty($obj->min) ? $obj->min * 1 : 1;
            if ($data->stock != '' && $data->stock * 1 < $min) {
                continue;
            }
            $data->min = $obj->min;
            $data->id = $obj->product_id;
            $data->dimensions = !empty($obj->dimensions) ? json_decode($obj->dimensions) : new stdClass();
            $data->title = $obj->title;
            $data->image = $obj->image;
            $data->prices = new stdClass();
            $data->prices->price = gridboxHelper::preparePrice($data->price);
            $data->product_type = $obj->product_type;
            if (!empty($data->sale_price)) {
                $data->prices->sale = gridboxHelper::preparePrice($data->sale_price);
            }
            $data->categories = gridboxHelper::getProductCategoryId($obj->product_id);
            $data->variations = [];
            $product->extra = gridboxHelper::getProductExtraOptions($obj->extra_options);
            $product->extra_options = !empty($product->extra_options) ? json_decode($product->extra_options) : new stdClass();
            if (isset($product->extra_options->price)) {
                $product->price -= $product->extra_options->price * $product->quantity;
            }
            if ($product->sale_price && isset($product->extra_options->price)) {
                $product->sale_price -= $product->extra_options->price * $product->quantity;
            }
            $data->extra_options = new stdClass();
            $data->extra_options->count = $data->extra_options->price = 0;
            $data->extra_options->items = new stdClass();
            if (isset($product->extra_options->items)) {
                foreach ($product->extra_options->items as $ind => $item) {
                    if (!isset($product->extra->{$ind})) {
                        continue;
                    }
                    $count = 0;
                    $object = $product->extra->{$ind};
                    $extra = new stdClass();
                    $extra->title = $object->title;
                    $extra->required = $object->required == '1';
                    $extra->values = new stdClass();
                    if (isset($item->attachments) && $object->type == 'file') {
                        $extra->attachments = $item->attachments;
                        $extra->price = $object->items->{0}->price;
                        $extra->charge = $object->file->charge;
                        $extra->quantity = $object->file->quantity;
                        if ($extra->price != '') {
                            $data->extra_options->price += $extra->price * ($extra->charge ? count($extra->attachments) : 1);
                        }
                        $count++;
                    }
                    foreach ($item->values as $key => $value) {
                        if (!isset($object->items->{$key})) {
                            continue;
                        }
                        $value->value = $value->value;
                        $value->price = $object->items->{$key}->price;
                        $count++;
                        if ($value->price != '') {
                            $data->extra_options->price += $value->price * 1;
                        }
                        $extra->values->{$key} = $value;
                    }
                    if ($count == 0) {
                        continue;
                    }
                    $data->extra_options->count += $count;
                    $data->extra_options->items->{$ind} = $extra;
                }
            }
            $data->extra = $product->extra;
            $data->variation = $product->variation;
            if (!empty($data->variation)) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_product_variations_map')
                    ->where('product_id = '.$obj->product_id);
                $db->setQuery($query);
                $map = $db->loadObjectList();
                $images = new stdClass();
                foreach ($map as $variation) {
                    $images->{$variation->option_key} = json_decode($variation->images);
                }
                $info = [];
                $array = explode('+', $product->variation);
                foreach ($array as $var) {
                    if (isset($fieldsData[$var])) {
                        $info[] = '<span>'.$fieldsData[$var]->title.' '.$fieldsData[$var]->value.'</span>';
                        $data->variations[] = $fieldsData[$var];
                    }
                    if (!empty($images->{$var})) {
                        $data->image = $images->{$var}[0];
                    }
                }
                $data->info = implode('/', $info);
            }
            $data->subscription = !empty($obj->subscription) ? json_decode($obj->subscription) : new stdClass();
            $product->data = $data;
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_customer_info')
            ->where('order_id = '.$id)
            ->order('order_list ASC, id ASC');
        $db->setQuery($query);
        $order->info = $db->loadObjectList();
        $order->tracking = gridboxHelper::$storeHelper->getTracking($id);
        
        return $order;
    }

    public function setTracking($obj)
    {
        $db = JFactory::getDbo();
        if (empty($obj->id)) {
            $db->insertObject('#__gridbox_store_order_tracking', $obj);
            $obj->id = $db->insertid();
        } else {
            $db->updateObject('#__gridbox_store_order_tracking', $obj, 'id');
        }

        return $obj;
    }

    public function removeAttached($where)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from('#__gridbox_store_product_attachments AS a')
            ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id')
            ->where($where);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $ids = [];
        foreach ($files as $obj) {
            $file = JPATH_ROOT.'/'.gridboxHelper::$storeHelper->attachments.'/'.$obj->filename;
            JFile::delete($file);
            $ids[] = $obj->id;
        }
        if (!empty($ids)) {
            $str = implode(', ', $ids);
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_product_attachments')
                ->where('id IN ('.$str.')');
            $db->setQuery($query)
                ->execute();
        }
    }

    public function updateOrder(object $cart, int $user_id):void
    {
        $db = JFactory::getDbo();
        $order = new stdClass();
        $order->id = $cart->order_id;
        $order->subtotal = $cart->subtotal;
        $order->tax = $cart->tax;
        $order->total = $cart->total;
        $order->user_id = $user_id;
        $order->currency_symbol = gridboxHelper::$store->currency->symbol;
        $order->currency_position = gridboxHelper::$store->currency->position;
        $db->updateObject('#__gridbox_store_orders', $order, 'id');
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
            if (isset($cart->promo->db_id)) {
                $discount->id = $cart->promo->db_id;
                $db->updateObject('#__gridbox_store_orders_discount', $discount, 'id');
            } else {
                $this->deleteTable($db, '#__gridbox_store_orders_discount', 'order_id = '.$order->id);
                $db->insertObject('#__gridbox_store_orders_discount', $discount);
            }
        }
        if ($cart->shipping) {
            $shipping = new stdClass();
            $shipping->order_id = $order->id;
            $params = json_decode($cart->shipping->options);
            $shipping->type = $params->type;
            $shipping->title = $cart->shipping->title;
            $shipping->price = $cart->shipping->price;
            $shipping->tax = $cart->shipping->tax;
            $shipping->shipping_id = $cart->shipping->id;
            $shipping->carrier = '';
            if (isset($cart->carrier)) {
                $shipping->carrier = $cart->carrier;
            }
            if (isset($cart->shipping->db_id)) {
                $shipping->id = $cart->shipping->db_id;
                $db->updateObject('#__gridbox_store_orders_shipping', $shipping, 'id');
            } else {
                $this->deleteTable($db, '#__gridbox_store_orders_shipping', 'order_id = '.$order->id);
                $db->insertObject('#__gridbox_store_orders_shipping', $shipping);
            }
        }
        $pids = [];
        $vids = [];
        $files = [];
        foreach ($cart->products as $obj) {
            foreach ($obj->extra_options->items as $extra) {
                if (isset($extra->attachments)) {
                    foreach ($extra->attachments AS $attachment) {
                        $files[] = $attachment->id;
                    }
                }
            }
            $product = gridboxHelper::$storeHelper->getProductObject($obj, $order->id);
            if (isset($obj->db_id)) {
                $product->id = $obj->db_id;
                $db->updateObject('#__gridbox_store_order_products', $product, 'id');
            } else {
                $product = gridboxHelper::$storeHelper->insertProduct($product, $obj);
            }
            $pids[] = $product->id;
            foreach ($obj->variations as $object) {
                $variation = new stdClass();
                $variation->product_id = $product->id;
                $variation->order_id = $order->id;
                $variation->title = $object->title;
                $variation->value = $object->value;
                $variation->color = $object->color;
                $variation->image = $object->image;
                $variation->type = $object->type;
                if (isset($object->id)) {
                    $variation->id = $obj->id;
                    $db->updateObject('#__gridbox_store_order_product_variations', $variation, 'id');
                } else {
                    $db->insertObject('#__gridbox_store_order_product_variations', $variation);
                    $variation->id = $db->insertid();
                }
                $vids[] = $variation->id;
            }
        }
        $str = 'm.order_id = '.$order->id;
        if (!empty($files)) {
            $str .= ' AND a.id NOT IN ('.implode(', ', $files).')';
        }
        $this->removeAttached($str);
        $str = ' NOT IN ('.implode(', ', $pids).') AND order_id = '.$order->id;
        gridboxHelper::$storeHelper->removeSubscriptionProducts($pids, $order->id);
        $this->deleteTable($db, '#__gridbox_store_order_products', 'id'.$str);
        $this->deleteTable($db, '#__gridbox_store_order_license', 'product_id'.$str);
        $this->deleteTable($db, '#__gridbox_store_bookings', 'product_id'.$str);
        $str = !empty($vids) ? 'id NOT IN ('.implode(', ', $vids).') AND ' : '';
        $str .= 'order_id = '.$order->id;
        $this->deleteTable($db, '#__gridbox_store_order_product_variations', $str);
        foreach ($cart->info as $key => $value) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_customer_info')
                ->where('id = '.$key);
            $db->setQuery($query);
            $customer = $db->loadObject();
            $customer->value = $value;
            if ($customer->type == 'country' && !empty($customer->value)) {
                $customer->value = gridboxHelper::$storeHelper->setCountryValue($customer->value);
            }
            $db->updateObject('#__gridbox_store_order_customer_info', $customer, 'id');
        }
    }

    public function createOrder($cart, $user_id)
    {
        gridboxHelper::$storeHelper->createAdminOrder($cart, $user_id);
    }

    public function getStatus($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, status, user_id, date')
            ->from('#__gridbox_store_orders')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('h.*, u.username')
            ->from('#__gridbox_store_orders_status_history AS h')
            ->where('h.order_id = '.$id)
            ->leftJoin('#__users AS u on u.id = h.user_id')
            ->order('h.id DESC');
        $db->setQuery($query);
        $obj->history = $db->loadObjectList();
        foreach ($obj->history as $record) {
            $record->date = JDate::getInstance($record->date)->format('M d, Y, H:i');
        }

        return $obj;
    }

    public function updateStatus($id, $status, $comment)
    {
        gridboxHelper::$storeHelper->updateStatus($id, $status, $comment);
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            gridboxHelper::$storeHelper->removeSubscriptionProducts([], $id);
            $this->deleteTable($db, '#__gridbox_store_order_products', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_orders', 'id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_orders_status_history', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_orders_discount', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_orders_shipping', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_orders_payment', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_order_customer_info', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_order_license', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_order_product_variations', 'order_id = '.$id);
            $this->deleteTable($db, '#__gridbox_store_bookings', 'order_id = '.$id);
            $str = 'm.order_id = '.$id;
            $this->removeAttached($str);
        }
    }

    protected function updateProductUsed($db, $where)
    {
        $query = $db->getQuery(true)
            ->select('d.id, d.stock, p.variation, d.variations, p.quantity')
            ->from('#__gridbox_store_order_products AS p')
            ->where('p.'.$where)
            ->leftJoin('#__gridbox_store_product_data AS d ON p.product_id = d.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = p.order_id')
            ->where('o.status <> '.$db->quote('refunded'));
        $db->setQuery($query);
        $products = $db->loadObjectList();
        foreach ($products as $product) {
            gridboxHelper::$storeHelper->updateProductUsed($product, '-');
        }
    }

    public function deleteTable($db, $table, $where)
    {
        if ($table == '#__gridbox_store_order_products') {
            $this->updateProductUsed($db, $where);
        }
        $query = $db->getQuery(true)
            ->delete($table)
            ->where($where);
        $db->setQuery($query)
            ->execute();
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

    public function getShipping()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_shipping')
            ->where('published = 1')
            ->order('order_list ASC');
        $db->setQuery($query);
        $shipping = $db->loadObjectList();

        return $shipping;
    }

    public function getSales()
    {
        $db = JFactory::getDbo();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_sales')
            ->where('applies_to = '.$db->quote('cart'))
            ->where('cart_discount <> '.$db->quote(''))
            ->where('discount <> '.$db->quote(''));
        $db->setQuery($query);
        $sales = $db->loadObjectList();
        usort($sales, function($a, $b){
            if ($a->cart_discount == $b->cart_discount) {
                return 0;
            }
            return ($a->cart_discount < $b->cart_discount) ? 1 : -1;
        });

        return $sales;
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

    public function getCustomerInfo()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_customer_info')
            ->order('order_list ASC');
        $db->setQuery($query);
        $info = $db->loadObjectList();
        foreach ($info as $value) {
            $value->settings = json_decode($value->options);
        }

        return $info;
    }

    public function getStatuses()
    {
        $data = gridboxHelper::getStatuses();

        return $data;
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('o.*')
            ->from('#__gridbox_store_orders AS o')
            ->where('o.published = 1');

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%'.$db->escape($search, true).'%', false);
            $pks = [];
            $q = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_orders')
                ->where('order_number LIKE '.$search);
            $db->setQuery($q);
            $ids = $db->loadObjectList();
            foreach ($ids as $obj) {
                $pks[] = $obj->id;
            }
            $pks = $this->getSearchCustomer($pks, 'customer_id = 1', $search);
            $pks = $this->getSearchCustomer($pks, 'type = '.$db->quote('email'), $search);
            if (!empty($pks)) {
                $str = implode(',', $pks);
                $query->where('o.id in ('.$str.')');
            }
        }
            
        $status = $this->getState('filter.state');
        if (!empty($status)) {
            $query->where('o.status = '.$db->quote($status));
        }
        $publish_up = $this->getState('filter.publish_up');
        if (!empty($publish_up)) {
            $publish_up = $publish_up.' 00:00:01';
            $query->where('o.date > '.$db->quote($publish_up));
        }
        $publish_down = $this->getState('filter.publish_down');
        if (!empty($publish_down)) {
            $publish_down = $publish_down.' 23:59:59';
            $query->where('o.date < '.$db->quote($publish_down));
        }
        $orderCol = $this->state->get('list.ordering', 'date');
        $orderDirn = $this->state->get('list.direction', 'desc');
        $query->order('o.'.$orderCol.' '.$orderDirn.', o.id DESC');
        
        return $query;
    }

    public function getSearchCustomer($pks, $where, $search)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true)
            ->select('order_id')
            ->from('#__gridbox_store_order_customer_info')
            ->where($where)
            ->where('value LIKE '.$search);
        $db->setQuery($q);
        $ids = $db->loadObjectList();
        foreach ($ids as $obj) {
            if (!in_array($obj->order_id, $pks)) {
                $pks[] = $obj->order_id;
            }
        }

        return $pks;
    }

    public function getCustomerInfoValue($item, $where, $key)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true)
            ->select('value')
            ->from('#__gridbox_store_order_customer_info')
            ->where('order_id = '.$item->id)
            ->where($where);
        $db->setQuery($q);
        $result = $db->loadResult();
        $item->{$key} = $result ? $result : '';
    }

    public function getItems()
    {
        $store = $this->getStoreId();
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }
        $query = $this->_getListQuery();
        try {
            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        foreach ($items as $item) {
            $this->getCustomerInfoValue($item, 'customer_id = 1', 'customer_name');
            $this->getCustomerInfoValue($item, 'type = '.$db->quote('email'), 'email');
            $item->hasBooking = $this->checkBooking($item);
        }

        $this->cache[$store] = $items;

        return $this->cache[$store];
    }

    protected function checkBooking(object $item):bool
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_store_order_products')
            ->where('product_type = ' . $db->quote('booking'))
            ->where('order_id = ' . $item->id);
        $db->setQuery($query);

        return $db->loadResult() > 0;
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