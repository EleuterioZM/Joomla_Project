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

class gridboxModelBookingcalendar extends JModelList
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'title', 'published', 'state', 'order_list'
            ];
        }
        parent::__construct($config);
    }

    public function updateAppointment(int $id, object $booking, object $info, object $extra, int $product_id):void
    {

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('o.*')
            ->from('#__gridbox_store_orders AS o')
            ->leftJoin('#__gridbox_store_order_products AS p ON p.order_id = o.id')
            ->where('p.id = ' . $db->quote($product_id));
        $db->setQuery($query);
        $order = $db->loadObject();
        
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_products')
            ->where('id = ' . $db->quote($product_id));
        $db->setQuery($query);
        $product = $db->loadObject();
        

        $price = !empty($product->sale_price) ? $product->sale_price : $product->price;
        $order->subtotal -= $price;
        $order->total -= $price;

        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_order_products')
            ->where('id = ' . $db->quote($product_id));
        $db->setQuery($query)
            ->execute();

        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_bookings')
            ->where('product_id = ' . $db->quote($product_id));
        $db->setQuery($query)
            ->execute();
        

        $product = $this->createAppointmentProduct($id, $booking, $extra);
        gridboxHelper::$storeHelper->insertAdminProduct($product, $order->id);

        $price = !empty($product->sale_price) ? $product->sale_price : $product->price;
        $order->subtotal += $price;
        $order->total += $price;
        $db->updateObject('#__gridbox_store_orders', $order, 'id');

        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_order_customer_info')
            ->where('order_id = ' . $order->id);
        $db->setQuery($query)
            ->execute();

        gridboxHelper::$storeHelper->insertAdminCustomerInfo($order->id, $info);

    }

    public function createAppointmentProduct(int $id, object $booking, object $extra):object
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('d.*, p.title, p.intro_image AS image')
            ->from('#__gridbox_store_product_data AS d')
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->where('d.product_id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $quantity = 1;
        if (!empty($booking->guests)) {
            $quantity = $booking->guests;
        } else if (!empty($booking->dates[1])) {
            $delta = strtotime($booking->dates[1]) - strtotime($booking->dates[0]);
            $quantity = $delta / 60 / 60 / 24;
        }
        $options = json_decode($obj->extra_options);
        $extra_options = (object)[
            'count' => 0,
            'price' => 0,
            'items' => new stdClass()
        ];

        foreach ($options as $option) {
            if (!isset($extra->{$option->id})) {
                continue;
            }
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_products_fields')
                ->where('id = '.$option->id);
            $db->setQuery($query);
            $field = $db->loadObject();

            $object = (object)[
                "title" => $field->title,
                "required" => $field->required == 1,
                "values" => new stdClass()
            ];
            if ($field->field_type == 'file') {
                $file_options = json_decode($field->file_options);
                $files = explode(',', $extra->{$option->id});
                $object->attachments = [];
                $object->charge = $file_options->charge == 1;
                $object->quantity = $file_options->quantity == 1;

                foreach ($files as $file_id) {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_store_product_attachments')
                        ->where('id = '.$file_id);
                    $db->setQuery($query);
                    $object->attachments[] = $db->loadObject();
                }
                $extra_options->count++;
                
            } else if ($field->field_type == 'textinput' || $field->field_type == 'textarea') {
                $object->values->{'0'} = (object)[
                    "price" => $option->items->{0}->price,
                    "weight" => $option->items->{0}->weight,
                    "value" => $extra->{$option->id}
                ];
                $extra_options->count++;
                $extra_options->price += !empty($option->items->{0}->price) ? $option->items->{0}->price : 0;
            } else {
                $items = json_decode($field->options);
                $values = explode(',', $extra->{$option->id});
                foreach ($items as $item) {
                    if (in_array($item->key, $values)) {
                        $price = $option->items->{$item->key}->price;
                        $object->values->{$item->key} = (object)[
                            "price" => $price,
                            "weight" => $option->items->{$item->key}->weight,
                            "value" => $item->title
                        ];
                        $extra_options->count++;
                        $extra_options->price += !empty($price) ? $price : 0;
                    }
                }
            }
            $extra_options->items->{$option->id} = $object;
        }

        $product = (object)[
            'title' => $obj->title,
            'image' => $obj->image,
            'id' => $id,
            'variation' => '',
            'quantity' => $quantity,
            'price' => $obj->price * $quantity + $extra_options->price,
            'sale_price' => !empty($obj->sale_price) ? $obj->sale_price * $quantity + $extra_options->price : '',
            'sku' => $obj->sku,
            'tax' => null,
            'net_price' => !empty($obj->sale_price) ? $obj->sale_price * $quantity : $obj->price * $quantity,
            'extra_options' => $extra_options,
            'product_type' => 'booking',
            'variations' => [],
            'booking' => $booking
        ];

        return $product;
    }

    public function createAppointment(int $id, object $booking, object $info, object $extra):object
    {
        $product = $this->createAppointmentProduct($id, $booking, $extra);
        $cart = (object)[
            'subtotal' => !empty($product->sale_price) ? $product->sale_price : $product->price,
            'tax' => 0,
            'total' => !empty($product->sale_price) ? $product->sale_price : $product->price,
            'shipping' => null,
            'products' => [$product],
            'info' => $info
        ];

        return $cart;
    }

    public function getCustomerInfo()
    {
        $db = $this->getDatabase();
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

    public function setPaid(int $id, int $status):void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_bookings')
            ->set('paid = '.$status)
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function setStatus(int $id):void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update('#__gridbox_store_bookings')
            ->set('unread = 0')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function setBlockTime(int $id, string $start_date, string $start_time, string $end_date, string $end_time):void
    {
        $db = $this->getDatabase();
        $obj = (object)[
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_date' => $end_date,
            'end_time' => $end_time,
            'id' => $id
        ];
        if (!empty($obj->id)) {
            $db->updateObject('#__gridbox_store_bookings_blocks', $obj, 'id');
        } else {
            $db->insertObject('#__gridbox_store_bookings_blocks', $obj);
        }
    }

    public function getTimeBlocks():array
    {
        $db = $this->getDatabase();
        $calendar_date = $this->getState('filter.calendar_date');
        $booking_layout = $this->getState('filter.booking_layout');
        if ($booking_layout == 'w') {
            $time = empty($calendar_date) ? 'this week' : $calendar_date;
            $timestamp = strtotime($time);
            $start_date = JDate::getInstance(strtotime('monday this week', $timestamp))->format('Y-m-d');
            $end_date = JDate::getInstance(strtotime('sunday this week', $timestamp))->format('Y-m-d');
        } else if ($booking_layout == 'd') {
            $time = empty($calendar_date) ? 'now' : $calendar_date;
            $start_date = $end_date = JDate::getInstance($time)->format('Y-m-d');
        } else {
            $time = empty($calendar_date) ? 'now' : $calendar_date;
            $start_date = JDate::getInstance(strtotime('first day of this month', strtotime($time)), true)->format('Y-m-d');
            $end_date = JDate::getInstance(strtotime('last day of this month', strtotime($time)), true)->format('Y-m-d');
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_bookings_blocks')

            ->where('(start_date <= '.$start_date.' AND end_date >= '.$end_date.')', 'OR')

            ->where('(start_date >= '.$db->quote($start_date).' AND end_date <= '.$db->quote($end_date).')', 'OR')
            ->where('(start_date < '.$db->quote($start_date).' AND end_date <= '.$db->quote($end_date).' AND end_date > '.$db->quote($start_date).')', 'OR')
            ->where('(start_date < '.$db->quote($start_date).' AND end_date > '.$db->quote($end_date).')', 'OR')
            ->where('(start_date >= '.$db->quote($start_date).' AND start_date < '.$db->quote($end_date).' AND end_date > '.$db->quote($end_date).')', 'OR');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        
        return $data;
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

    public function setColor($id, $color)
    {
        $colors = gridboxHelper::getGridboxApi('booking_colors');
        $obj = json_decode($colors->key);
        $obj->{$id} = $color;
        $colors->key = json_encode($obj);
        $db = $this->getDatabase();
        $db->updateObject('#__gridbox_api', $colors, 'id');
    }

    public function getColors()
    {
        $colors = gridboxHelper::getGridboxApi('booking_colors');

        return json_decode($colors->key);
    }

    public function getSettings()
    {
        $booking = gridboxHelper::getBooking();
        $settings = $booking->getSettings();

        return $settings;
    }

    public function updateSettings($data)
    {
        $booking = gridboxHelper::getBooking();
        $settings = json_encode($data);
        $booking->setSettings($settings);
    }

    public function getServices():array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('p.title, p.id')
            ->from('#__gridbox_pages AS p')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id')
            ->where('a.type = '.$db->quote('booking'))
            ->where('p.page_category <>'.$db->quote('trashed'));
        $db->setQuery($query);
        $services = $db->loadObjectList();

        return $services;
    }

    public function getNewBookings(int $page = 0):object
    {
        $appointments = $this->getAppointments($page, '', 'id DESC', 'new');

        return $appointments;
    }

    public function getUpcoming(int $page = 0):object
    {
        $db = $this->getDatabase();
        $date = date('Y-m-d');
        $where = 'b.start_date > '.$db->quote($date);
        $appointments = $this->getAppointments($page, $where, 'start_date ASC', 'upcoming');

        return $appointments;
    }

    protected function getAppointments(int $page, string $where, string $order, string $type):object
    {
        $limit = 20;
        $start = $page * $limit;
        $db = $this->getDatabase();
        $query = $this->getBookingQuery()
            ->order($order);
        if (!empty($where)) {
            $query->where($where);
        }
        $db->setQuery($query, $start, $limit);
        $data = (object)[
            'items' => $db->loadObjectList(),
            'paginator' => ''
        ];
        if (count($data->items) < $limit) {
            return $data;
        }
        $query = $this->getBookingQuery('COUNT(b.id)');
        $db->setQuery($query);
        $count = $db->loadResult();
        $pages = ceil($count / $limit);
        if ($pages == 1 || $page == $pages - 1) {
            return $data;
        }
        $next = $page + 1;
        $text = JText::_('LOAD_MORE');
        $data->paginator = <<<TEXT
        <div class="ba-booking-pagination">
            <span data-next="$next" data-type="$type">$text</span>
        </div>
        TEXT;

        return $data;
    }

    protected function getBookingQuery(string $select = '')
    {
        $db = $this->getDatabase();
        if (empty($select)) {
            $select = 'b.*, p.title, p.product_id AS item_id, p.image';
        }

        return $db->getQuery(true)
            ->select($select)
            ->from('#__gridbox_store_bookings AS b')
            ->leftJoin('#__gridbox_store_order_products AS p ON p.id = b.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = b.order_id')
            ->where('o.published = 1');
    }

    public function deleteBlock($id):void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_bookings_blocks')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function deleteBooking($id):void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_bookings')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function getBlockDetails($id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_bookings_blocks')
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        $item->start_formated = gridboxHelper::formatDate($item->start_date);
        $item->end_formated = !empty($item->end_date) ? gridboxHelper::formatDate($item->end_date) : '';

        return $item;
    }

    public function getMonthlyItems(int $id, string $date, string $time):object
    {
        $db = $this->getDatabase();
        $query = $this->getBookingQuery()
            ->where('b.start_date = ' . $db->quote($date))
            ->where('p.product_id = ' . $id)
            ->order('start_time ASC');
        if (!empty($time)) {
            $query->where('start_time = ' . $db->quote($time));
        }
        $db->setQuery($query);
        $dateTime = JDate::getInstance($date);
        $items = $db->loadObjectList();
        $response = (object)[
            'date' => $dateTime->format('d M'),
            'year' => $dateTime->format('Y'),
            'start_date' => $dateTime->format('j F Y'),
            'items' => $items
        ];

        return $response;
    }

    public function getBookingDetails(int $id) :?object
    {
        $db = $this->getDatabase();
        $query = $this->getBookingQuery()
            ->select('o.user_id, o.currency_symbol AS symbol, o.currency_position AS position, d.booking, p.extra_options')
            ->where('b.id = '.$id)
            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.product_id');
        $db->setQuery($query);
        $item = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_orders_payment')
            ->where('order_id = '.$item->order_id);
        $db->setQuery($query);
        $item->payment = $db->loadObject();
        if (!empty($item->user_id)) {
            $query = $db->getQuery(true)
                ->select('id, username')
                ->from('#__users')
                ->where('id = '.$item->user_id);
            $db->setQuery($query);
            $item->user = $db->loadObject();
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_customer_info')
            ->where('order_id = '.$item->order_id)
            ->order('order_list ASC, id ASC');
        $db->setQuery($query);
        $item->info = $db->loadObjectList();
        $item->dates = (object)[
            'start_date' => $item->start_date,
            'end_date' => $item->end_date
        ];
        $item->start_date = gridboxHelper::formatDate($item->start_date);
        $item->end_date = !empty($item->end_date) ? gridboxHelper::formatDate($item->end_date) : '';

        return $item;
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $db = $this->getDatabase();
        $query = $this->getBookingQuery();
        $calendar_date = $this->getState('filter.calendar_date');
        $booking_layout = $this->getState('filter.booking_layout');
        $booking_view = $this->getState('filter.booking_view');
        if ($booking_layout == 'w') {
            $time = empty($calendar_date) ? 'this week' : $calendar_date;
            $timestamp = strtotime($time);
            $start_date = JDate::getInstance(strtotime('monday this week', $timestamp))->format('Y-m-d');
            $end_date = JDate::getInstance(strtotime('sunday this week', $timestamp))->format('Y-m-d');
            $query->where('b.start_date >= '.$db->quote($start_date))
                ->where('b.start_date <= '.$db->quote($end_date));
        } else if ($booking_layout == 'd') {
            $time = empty($calendar_date) ? 'now' : $calendar_date;
            $start_date = JDate::getInstance($time)->format('Y-m-d');
            $query->where('b.start_date = '.$db->quote($start_date));
        } else {
            $time = empty($calendar_date) ? 'now' : $calendar_date;
            $start = JDate::getInstance(strtotime('first day of this month', strtotime($time)), true)->format('Y-m-d');
            $end = JDate::getInstance(strtotime('last day of this month', strtotime($time)), true)->format('Y-m-d');
            $start = $db->quote($start);
            $end = $db->quote($end);
            $where = '((b.start_date <= ' . $end . 'AND b.end_date >= ' . $start
                . ') OR (b.start_date >= ' . $start . ' AND b.start_date <= '
                . $end . ' AND b.end_date = ' . $db->quote('') . '))';
            $query->where($where);
        }
        if (($booking_view == 'calendar') && $booking_layout == 'w' || $booking_layout == 'd') {
            $query->where('b.start_time <> '.$db->quote(''));
        }
        

        $services = $this->getState('filter.services');
        if (!empty($services)) {
            $query->where('p.product_id NOT IN ('.$services.')');
        }

        $paid = $this->getState('filter.paid');
        if ($paid == 0) {
            $query->where('b.paid <> 1');
        }

        $not_paid = $this->getState('filter.not_paid');
        if ($not_paid == 0) {
            $query->where('b.paid <> 0');
        }
        
        return $query;
    }

    public function getItems()
    {
        $store = $this->getStoreId();
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }
        $query = $this->_getListQuery();
        try {
            $items = $this->_getList($query);
            $items = $this->prepareAppointments($items);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        $this->cache[$store] = $items;

        return $this->cache[$store];
    }

    protected function prepareWeeklyAppointments(array $items):object
    {
        $data = new stdClass();
        foreach ($items as $item) {
            $date = $item->start_date;
            $time = $item->start_time;
            if (!isset($data->{$date})) {
                $data->{$date} = new stdClass();
            }
            if (strpos($time, '15') || strpos($time, '45')) {
                $array = explode(':', $time);
                $time = $array[0].':'.($array[1] == '15' ? '00' : '30');
            }
            if (!isset($data->{$date}->{$time})) {
                $data->{$date}->{$time} = [];
            }
            $data->{$date}->{$time}[] = $item;
        }

        return $data;
    }

    protected function prepareDaylyAppointments(array $items):object
    {
        $i = 0;
        $columns = new stdClass();
        $data = new stdClass();
        foreach ($items as $item) {
            if (!isset($columns->{$item->item_id})) {
                $columns->{$item->item_id} = $i++;
            }
            $item->column = $columns->{$item->item_id};
            $time = $item->start_time;
            if (!isset($data->{$time})) {
                $data->{$time} = new stdClass();
            }
            if (!isset($data->{$time}->{$item->item_id})) {
                $data->{$time}->{$item->item_id} = (object)[
                    'item' => $item,
                    'count' => 0
                ];
            }
            $data->{$time}->{$item->item_id}->count++;
        }

        return $data;
    }

    protected function prepareMonthlyAppointments(array $items):object
    {
        $data = new stdClass();
        $calendar_date = $this->getState('filter.calendar_date');
        $time = empty($calendar_date) ? 'now' : $calendar_date;
        $start = JDate::getInstance(strtotime('first day of this month', strtotime($time)), true)->format('Y-m-d');
        foreach ($items as $item) {
            $date = $item->start_date;
            if ($date < $start) {
                $date = $start;
            }
            if (!isset($data->{$date})) {
                $data->{$date} = (object)[
                    'single' => new stdClass(),
                    'multiple' => []
                ];
            }
            if (!empty($item->end_date)) {
                $data->{$date}->multiple[] = $item;
                continue;
            }
            if (!isset($data->{$date}->single->{$item->item_id})) {
                $data->{$date}->single->{$item->item_id} = (object)[
                    'item' => $item,
                    'count' => 0
                ];
            }
            $data->{$date}->single->{$item->item_id}->count++;
        }

        return $data;
    }

    protected function setScheduleItem(array &$data, object $item, bool $isBlock):void
    {
        $date = $item->start_date;
        if (!isset($data[$date])) {
            $data[$date] = [];
        }
        $item->isBlock = $isBlock;
        $data[$date][] = $item;
        if ($isBlock && $item->start_date != $item->end_date && $item->start_date < $item->end_date) {
            $clone = clone $item;
            $dateTime = JDate::getInstance($clone->start_date);
            $dateTime->modify('+1 day');
            $clone->start_date = $dateTime->format('Y-m-d');
            $this->setScheduleItem($data, $clone, $isBlock);
        }
        
    }

    protected function prepareSchedule(array $items):object
    {
        $data = [];
        foreach ($items as $item) {
            $this->setScheduleItem($data, $item, false);
        }
        $blocks = $this->getTimeBlocks();
        foreach ($blocks as $item) {
            $this->setScheduleItem($data, $item, true);
        }
        foreach ($data as &$item) {
            usort($item, fn($a, $b) => $a->start_time <=> $b->start_time);
        }
        ksort($data);

        return (object)$data;
    }

    protected function prepareAppointments(array $items) :?object
    {
        $booking_layout = $this->getState('filter.booking_layout');
        $booking_view = $this->getState('filter.booking_view');
        if ($booking_view != 'calendar') {
            return $this->prepareSchedule($items);
        }
        switch ($booking_layout) {
            case 'w':
                $data = $this->prepareWeeklyAppointments($items);
                break;
            case 'd':
                $data = $this->prepareDaylyAppointments($items);
                break;
            default:
                $data = $this->prepareMonthlyAppointments($items);
                break;
        }
        
        return $data;
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

        $booking_layout = $this->getUserStateFromRequest($this->context . '.filter.booking_layout', 'booking_layout', 'w', 'string');
        $this->setState('filter.booking_layout', $booking_layout);

        $booking_view = $this->getUserStateFromRequest($this->context . '.filter.booking_view', 'booking_view', 'calendar', 'string');
        $this->setState('filter.booking_view', $booking_view);

        $calendar_date = $this->getUserStateFromRequest($this->context . '.filter.calendar_date', 'calendar_date', '', 'string');
        $this->setState('filter.calendar_date', $calendar_date);

        $services = $this->getUserStateFromRequest($this->context . '.filter.services', 'services', '', 'string');
        $this->setState('filter.services', $services);

        $paid = $this->getUserStateFromRequest($this->context . '.filter.paid', 'filter_paid', 1, 'int');
        $this->setState('filter.paid', $paid);

        $not_paid = $this->getUserStateFromRequest($this->context . '.filter.not_paid', 'filter_not_paid', 1, 'int');
        $this->setState('filter.not_paid', $not_paid);

        parent::populateState('id', 'desc');
    }
}