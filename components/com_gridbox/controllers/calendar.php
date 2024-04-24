<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gridboxControllerCalendar extends JControllerForm
{
    protected $zone;

    public function getModel($name = '', $prefix = '', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

    public function getSingleSlots()
    {
        $product_id = $this->input->get('id', 0, 'int');
        $date = $this->input->get('date', '', 'string');
        $options = gridboxHelper::$storeHelper->getProductData($product_id)->booking;
        $booking = gridboxHelper::getBooking();
        $dateObject = JDate::getInstance($date, true);
        $times = $booking->getSingleSlots($options, $dateObject, $product_id);
        $str = json_encode($times);
        echo $str;
        exit;
    }

    public function render():void
    {
        $year = $this->input->get('year', '0', 'string');
        $month = $this->input->get('month', '0', 'string');
        $format = $this->input->get('date_format', 'Y-m-d', 'raw');
        $product_id = $this->input->get('product_id', 0, 'int');
        $footer = $this->input->get('footer', 0, 'int');
        $multiple = $this->input->get('multiple', 0, 'int');
        if (empty($format)) {
            $format = 'Y-m-d';
        }
        if (strlen($month) == 1) {
            $month = '0'.$month;
        }
        $offset = JFactory::getConfig()->get('offset');
        $this->zone = new DateTimeZone($offset);
        $date = JDate::getInstance($year.'-'.$month.'-01', $this->zone);
        $str = $this->getCalendar($date, $month, $year, 1, $format, $product_id, $footer, $multiple);
        echo $str;
        exit;
    }

    public function getCalendar(object $dateObject, string $month, string $year, int $start,
                                string $format, int $product_id, int $footer, int $multiple):string
    {
        $end = $start + 6;
        $dateData = new stdClass();
        $dateData->days = [JText::_('SUN'), JText::_('MON'), JText::_('TUE'), JText::_('WED'),
            JText::_('THU'), JText::_('FRI'), JText::_('SAT'), JText::_('SUN')];
        $today = date('j');
        $now = JDate::getInstance('now', $this->zone);
        $nowDate = new stdClass();
        $nowDate->date = $now->format('n Y', true);
        $nowDate->year = $now->format('Y', true);
        $nowDate->month = $now->format('n', true);
        $m = strlen($nowDate->month) == 1 ? '0'.$nowDate->month : $nowDate->month;
        $time = $now->format('H:i:s', true);
        if (!empty($product_id)) {
            $options = gridboxHelper::$storeHelper->getProductData($product_id)->booking;
            $booking = gridboxHelper::getBooking();
        }
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/calendar/calendar.php');

        return $out;
    }
}