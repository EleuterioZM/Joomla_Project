<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gridboxControllerBookingcalendar extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, ['ignore_request' => false]);
    }

    public function getGroupSessionGuest()
    {
        $id = $this->input->get('id', 0, 'int');
        $date = $this->input->get('date', '', 'string');
        $booking = gridboxHelper::getBooking();
        $dateObject = JDate::getInstance($date, true);
        $booking->isBlockedDay($dateObject, $id, false);
        $guests = $booking->getGroupSessionGuest($date);
        echo $guests;
        exit;
    }

    public function getAppointments():void
    {
        $nextPage = $this->input->get('next', 0, 'int');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel();
        if ($type == 'new') {
            $data = $model->getNewBookings($nextPage);
        } else {
            $data = $model->getUpcoming($nextPage);
        }
        $items = $data->items;
        $paginator = $data->paginator;
        $isNew = $type == 'new';
        include JPATH_COMPONENT.'/views/layouts/booking-items.php';
        
        exit;
    }

    public function createAppointment():void
    {
        $service = $this->input->get('service', 0, 'int');
        $user = $this->input->get('user', 0, 'int');
        $old_product = $this->input->get('old_product', 0, 'int');
        $booking = (object)[
            'dates' => [
                $this->input->get('start_date', '', 'string'),
                $this->input->get('end_date', '', 'string')
            ],
            'time' => (object)[
                'start' => $this->input->get('start_time', '', 'string'),
                'end' => $this->input->get('end_time', '', 'string')
            ],
            'guests' => $this->input->get('guests', '', 'string')
        ];
        $info = (object)$this->input->get('info', [], 'array');
        $extra = (object)$this->input->get('extra', [], 'array');
        $model = $this->getModel();
        if (!empty($old_product)) {
            $model->updateAppointment($service, $booking, $info, $extra, $old_product);
        } else {
            $cart = $model->createAppointment($service, $booking, $info, $extra);
            gridboxHelper::$storeHelper->createAdminOrder($cart, $user);
        }
        
        exit;
    }

    public function getProducts():void
    {
        $model = $this->getModel('promocodes');
        $products = $model->getProducts(0, 'booking');
        $str = json_encode($products);
        echo $str;
        exit;
    }

    public function setPaid():void
    {
        $id = $this->input->get('id', 0, 'int');
        $status = $this->input->get('status', 0, 'int');
        $model = $this->getModel();
        $model->setPaid($id, $status);
        exit;
    }

    public function setStatus():void
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->setStatus($id);
        exit;
    }

    public function setBlockTime():void
    {
        $start_date = $this->input->get('start_date', '', 'string');
        $end_date = $this->input->get('end_date', '', 'string');
        $start_time = $this->input->get('start_time', '', 'string');
        $end_time = $this->input->get('end_time', '', 'string');
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->setBlockTime($id, $start_date, $start_time, $end_date, $end_time);
        exit;
    }

    public function deleteBlock():void
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->deleteBlock($id);
        exit;
    }

    public function deleteBooking():void
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->deleteBooking($id);
        exit;
    }

    public function getBlockDetails():void
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $data = $model->getBlockDetails($id);
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getMonthlyItems():void
    {
        $id = $this->input->get('id', 0, 'int');
        $date = $this->input->get('date', '', 'string');
        $time = $this->input->get('time', '', 'string');
        $model = $this->getModel();
        $data = $model->getMonthlyItems($id, $date, $time);
        $str = json_encode($data);
        echo $str;exit;
    }

    public function getBookingDetails():void
    {
        $id = $this->input->get('id', 0, 'int');
        $edit = $this->input->get('edit', 0, 'int');
        $model = $this->getModel();
        $data = $model->getBookingDetails($id);
        if ($edit == 1) {
            $model = $this->getModel('Promocodes');
            $data->item = $model->getBookingEditProduct($data->item_id);
        }
        $str = json_encode($data);
        echo $str;exit;
    }

    public function setColor()
    {
        $id = $this->input->get('id', 0, 'int');
        $color = $this->input->get('color', '', 'string');
        $model = $this->getModel();
        $model->setColor($id, $color);
        exit;
    }

    public function updateSettings()
    {
        $data = new stdClass();
        $limitation = $this->input->get('limitation', '', 'raw');
        $default = $this->input->get('default', '', 'raw');
        $data->limitation = json_decode($limitation);
        $data->default = json_decode($default);
        $model = $this->getModel();
        $model->updateSettings($data);
        echo JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        exit;
    }
}