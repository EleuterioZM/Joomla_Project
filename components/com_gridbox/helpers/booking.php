<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxBooking
{
    protected $dir;
    protected $db;
    protected $settings;
    protected $colors;
    protected $blocked;
    protected $productOptions;
    protected $week;

    public $isGroupSession;


    public function __construct()
    {
        $this->dir = JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/booking/';
        $this->db = JFactory::getDbo();
        $this->week = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $this->clearBlockedAppointments();
        $this->loadSettings();
    }

    public function calculateDaylyTopOffset(array $times, string $time, string $min):float
    {
        $y = 0;
        $delta = array_search($min, $times);
        if (in_array($time, $times)) {
            $y = array_search($time, $times);
        } else {
            foreach ($times as $i => $value) {
                if ($time < $value) {
                    $y = $i - 0.5;
                    break;
                }
            }
        }

        return $y - $delta;
    }

    public function calculateDaylyHeight(array $times, string $start, string $end):float
    {
        if (in_array($start, $times)) {
            $y1 = array_search($start, $times);
        } else {
            foreach ($times as $i => $value) {
                if ($start < $value) {
                    $y1 = $i - 0.5;
                    break;
                }
            }
        }
        if (in_array($end, $times)) {
            $y2 = array_search($end, $times);
        } else {
            foreach ($times as $i => $value) {
                if ($end < $value) {
                    $y2 = $i - 0.5;
                    break;
                }
            }
        }
        
        return $y2 - $y1;
    }

    public function isEnabledDays(?object $options = null):bool
    {
        $working = isset($options->hours) ? $options->hours : $this->settings->default;
        $flag = false;
        foreach ($working as $day) {
            if ($day->enable) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }

    public function clearBlockedAppointments():void
    {
        $this->blocked = (object)[
            'bookings' => new stdClass(),
            'times' => new stdClass()
        ];
    }

    protected function getBookedAppointments(string $date, int $id, bool $nights = false):void
    {
        if (isset($this->blocked->bookings->{$date})) {
            return;
        }
        $query = $this->db->getQuery(true)
            ->select('b.start_time')
            ->from('#__gridbox_store_bookings AS b')
            ->leftJoin('#__gridbox_store_order_products AS p ON p.id = b.product_id')
            ->leftJoin('#__gridbox_store_orders AS o ON o.id = b.order_id')
            ->where('o.published = 1')
            ->where('p.product_id = '.$id);
        if ($nights) {
            $query->where('b.start_date <= '.$this->db->quote($date))
            ->where('b.end_date >= '.$this->db->quote($date));
        } else {
            $query->where('b.start_date = '.$this->db->quote($date));
        }
        if ($this->isGroupSession) {
            $query->select('b.guests');
        }
        $this->db->setQuery($query);
        if ($this->isGroupSession) {
            $this->blocked->bookings->{$date} = $this->db->loadObjectList();
        } else {
            $this->blocked->bookings->{$date} = $this->db->loadColumn();
        }
        
    }

    public function getBlockedTimes(string $start_date):void
    {
        if (isset($this->blocked->times->{$start_date})) {
            return;
        }
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_bookings_blocks')
            ->where('(start_date <= '.$this->db->quote($start_date).' AND end_date >= '.$this->db->quote($start_date).')', 'OR');
        $this->db->setQuery($query);
        $this->blocked->times->{$start_date} = $this->db->loadObjectList();
    }

    public function isBlockedSlot(string $date, string $time, int $id):bool
    {
        $today = JDate::getInstance('now');
        $todayTime = $today->format('H:i', true);
        $todayDay = $today->format('Y-m-d', true);
        if ($date == $todayDay && $time < $todayTime) {
            return true;
        }
        if (!$this->productOptions) {
            $this->setProductOptions($id, false);
        }
        $this->getBookedAppointments($date, $id);
        if ($this->isGroupSession && $this->getGroupSessionGuest($date, $time) <= 0) {
            return true;
        } else if (!$this->isGroupSession && in_array($time, $this->blocked->bookings->{$date})) {
            return true;
        }
        $this->getBlockedTimes($date, $time, $id);
        $blocked = false;
        foreach ($this->blocked->times->{$date} as $block) {
            if (
                ($date > $block->start_date && $date < $block->end_date) ||
                ($date == $block->start_date && $time > $block->start_time && $date < $block->end_date) ||
                ($date > $block->start_date && $date == $block->end_date && $time < $block->end_time) ||
                ($date == $block->start_date && $time >= $block->start_time
                    && $date == $block->end_date && $time < $block->end_time)
            ) {
                $blocked = true;
                break;
            }
        }

        return $blocked;
    }

    public function getSingleSlots(object $options, object $dateObject, int $id):array
    {
        $day = $this->week[$dateObject->format('w', true)];
        $times = [];
        $working = isset($options->single->hours) ? $options->single->hours : $this->settings->default;
        if (!$working->{$day}->enable) {
            return $times;
        }
        $this->setProductOptions($id, false, $options);
        $date = $dateObject->format('Y-m-d', true);
        foreach ($working->{$day}->hours as $hours) {
            $time = $hours->start;
            while ($time < $hours->end) {
                $slot = (object)[
                    'start' => $time,
                    'end' => JHtml::date($date.' '.$time.' +'.$options->single->duration.' minutes', 'H:i', null)
                ];
                $time = $slot->end;
                if ($time > $hours->end) {
                    break;
                }
                if ($this->isBlockedSlot($date, $slot->start, $id)) {
                    continue;
                }
                if ($this->isGroupSession) {
                    $slot->guests = $this->getGroupSessionGuest($date, $slot->start);
                }
                $times[] = $slot;
            }
        }

        return $times;
    }

    public function getGroupSessionGuest(string $date, string $time = ''):int
    {
        $guest = $this->productOptions->single->participants * 1;
        foreach ($this->blocked->bookings->{$date} as $obj) {
            if ($time != $obj->start_time) {
                continue;
            }
            $guest -= $obj->guests * 1;
        }

        return $guest;
    }

    public function isBlockedDay(object $dateObject, int $id, bool $nights = false):bool
    {
        $day = $this->week[$dateObject->format('w', true)];
        $working = $this->settings->default;
        if (!$working->{$day}->enable) {
            return true;
        }
        if (!$this->productOptions) {
            $this->setProductOptions($id, $nights);
        }
        $date = $dateObject->format('Y-m-d', true);
        $this->getBookedAppointments($date, $id, $nights);
        if ($this->isGroupSession && $this->getGroupSessionGuest($date) <= 0) {
            return true;
        } else if (!$this->isGroupSession && !empty($this->blocked->bookings->{$date})) {
            return true;
        }
        $blocked = false;
        $this->getBlockedTimes($date);
        $start_time = $working->{$day}->hours[0]->start;
        $end_time = end($working->{$day}->hours)->end;
        foreach ($this->blocked->times->{$date} as $block) {
            if (
                ($date > $block->start_date && $date < $block->end_date) ||
                ($date == $block->start_date && $block->start_time <= $start_time
                    && $date < $block->end_date) ||
                ($date > $block->start_date && $date == $block->end_date
                    && $block->end_time >= $end_time) ||
                ($date == $block->start_date && $date == $block->end_date
                    && $block->start_time <= $start_time && $block->end_time >= $end_time)
            ) {
                $blocked = true;
                break;
            }
        }

        return $blocked;
    }

    protected function setProductOptions(int $id, bool $nights, ?object $options = null):void
    {
        if (!$options) {
            $options = gridboxHelper::$storeHelper->getProductData($id)->booking;
        }
        $this->productOptions = $options;
        $this->isGroupSession = !$nights && $this->productOptions->single->type == 'group-session';
    }

    public function getSingleDay(object $dateObject, int $id, object $options):object
    {
        $this->setProductOptions($id, false, $options);
        if ($this->isBlockedDay($dateObject, $id)) {
            $dateObject->modify('+1 day');
            $dateObject = $this->getSingleDay($dateObject, $id, $options);
        }
        
        return $dateObject;
    }

    public function getMultipleDate(object $start_date, int $id, int $min):array
    {
        while ($this->isBlockedDay($start_date, $id, true)) {
            $start_date->modify('+1 day');
        }
        $end_date = null;
        for ($i = 1; $i <= $min; $i++) {
            $end_date = JDate::getInstance($start_date->format('Y-m-d').' + '.$i.' day');
            if ($this->isBlockedDay($end_date, $id, true)) {
                $start_date->modify('+'.($i + 1).' day');
                list($start_date, $end_date) = $this->getMultipleDate($start_date, $id, $min);
                break;
            }
        }
        
        return [$start_date, $end_date];
    }

    protected function getDbo():object
    {
        return $this->db;
    }

    protected function loadApi(string $service = 'booking_calendar'):string
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('booking_calendar'));
        $db->setQuery($query);

        return $db->loadResult();
    }

    protected function loadSettings():void
    {
        $key = $this->loadApi();
        $this->settings = json_decode($key);
        if (!isset($this->settings->limitation)) {
            $this->settings = $this->decodeSettingsFile();
            $key = json_encode($this->settings);
            $this->setSettings($key);
        }
    }

    public function decodeSettingsFile(string $file = 'settings.json'):object
    {
        $str = gridboxHelper::readFile($this->dir.$file);
        $settings = json_decode($str);

        return $settings;
    }

    public function setSettings(string $settings):void
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_api')
            ->set('`key` = '.$db->quote($settings))
            ->where('service = '.$db->quote('booking_calendar'));
        $db->setQuery($query)
            ->execute();
    }

    public function getSettings():object
    {
        return $this->settings;
    }
}