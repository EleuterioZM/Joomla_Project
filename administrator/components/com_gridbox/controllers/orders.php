<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class gridboxControllerOrders extends JControllerAdmin
{
    public function getModel($name = 'orders', $prefix = 'gridboxModel', $config = [])
    {
        $model = parent::getModel($name, $prefix, ['ignore_request' => true]);

        return $model;
    }

    public function testOrder()
    {
        $id = $this->input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('o.*, p.type')
            ->from('#__gridbox_store_orders AS o')
            ->leftJoin('#__gridbox_store_orders_payment AS p ON p.order_id = o.id');
        if (!empty($id)) {
            $query->where('o.id = '.$id);
        } else {
            $query->where('o.published <> 1');
        }
        $db->setQuery($query);
        $orders = $db->loadObjectList();
        if (!empty($id)) {
            foreach ($orders as $order) {
                $query = $db->getQuery(true)
                    ->select('value, title')
                    ->from('#__gridbox_store_order_customer_info')
                    ->where('order_id = '.$id);
                $db->setQuery($query);
                $order->info = $db->loadObjectList();
                //gridboxHelper::$storeHelper->approveOrder($order->id, $order->params, true, false, false, false);
            }

        }

        print_r($orders);

        exit;
    }

    public function checkMatchedCsv()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $matched = $this->input->get('matched', '{}', 'raw');
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel();
        $obj = json_decode($matched);
        $model->checkMatchedCsv($file, $overwrite, $obj);
    }

    public function checkGridboxCsv()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel();
        $model->checkGridboxCsv($file, $overwrite);
    }

    public function importCSV()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $matched = $this->input->get('matched', '{}', 'raw');
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $type = $this->input->get('type', '{}', 'string');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel();
        $obj = json_decode($matched);
        $category = $this->getModel('category');
        $model->importCSV($file, $overwrite, $obj, $type, $category);
        exit;
    }

    public function getMatchFields()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $id = $this->input->get('id', 0, 'int');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $handle = fopen($file, "r");
        $model = $this->getModel();
        $obj = new stdClass();
        $obj->data = fgetcsv($handle, 1000, ",");
        $obj->cells = $model->getCSVAppCells();
        $obj->fields = $model->getAppFields($id);
        fclose($handle);
        JFile::delete($file);
        $str = json_encode($obj);
        print_r($str);exit;
        exit;
    }

    public function download()
    {
        $file = $this->input->get('file', '', 'string');
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=gridbox-orders.csv');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            if (readfile($file)) {
                unlink($file);
            }
        }
        exit;
    }

    public function exportCSV()
    {
        $model = $this->getModel();
        $pks = $this->input->get('pks', [], 'array');
        $cells = $this->input->get('cells', [], 'array');
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $response = new stdClass();
        if (!empty($tmp_path) && JFolder::exists($tmp_path)) {
            $response->file = $model->exportCSV($pks, $cells, $tmp_path);
            $response->status = true;
        } else {
            $response->status = false;
            $response->message = 'Temp Folder is not exists';
        }
        $str = json_encode($response);
        print_r($str);
        exit();
    }

    public function getAppCells()
    {
        $pks = $this->input->get('pks', [], 'array');
        $model = $this->getModel();
        $model->getAppCells($pks);
    }

    public function setTracking()
    {
        $obj = new stdClass();
        $obj->id = $this->input->get('id', 0, 'int');
        $obj->order_id = $this->input->get('order_id', 0, 'int');
        $obj->number = $this->input->get('number', '', 'string');
        $obj->url = $this->input->get('url', '', 'string');
        $obj->title = $this->input->get('title', '', 'string');
        $model = $this->getModel();
        $obj = $model->setTracking($obj);
        $str = json_encode($obj);
        echo $str;exit();
    }

    public function getUserInfo()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $info = $model->getUserInfo($id);
        $str = json_encode($info);
        echo $str;exit();
    }

    public function getStatistic()
    {
        $input = JFactory::getApplication()->input;
        $date = $input->get('date', '', 'string');
        $type = $input->get('type', '', 'string');
        $data = gridboxHelper::getShopStatistic($date, $type);
        $str = json_encode($data);
        echo $str;
        exit;
    }

    public function getOrder()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $order = $model->getOrder($id);
        $str = json_encode($order);
        print_r($str);exit;
    }

    public function updateOrder()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', '', 'string');
        $object = json_decode($data);
        $user_id = 0;
        if (isset($object->info->{'user_id'})) {
            $user_id = $object->info->{'user_id'};
            unset($object->info->{'user_id'});
        }
        $model = $this->getModel();
        $model->updateOrder($object, $user_id);
        print_r('{}');exit;
    }

    public function createOrder()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', '', 'string');
        $object = json_decode($data);
        $user_id = 0;
        if (isset($object->info->user_id)) {
            $user_id = $object->info->{'user_id'};
            unset($object->info->user_id);
        }
        $model = $this->getModel();
        $model->createOrder($object, $user_id);
        print_r('{}');exit;
    }

    public function updateStatus()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $status = $input->get('status', '', 'string');
        $comment = $input->get('comment', '', 'string');
        $model = $this->getModel();
        $model->updateStatus($id, $status, $comment);
        echo '{}';exit;
    }

    public function getStatus()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->getStatus($id);
        $str = json_encode($obj);
        echo $str;exit;
    }

    public function contextDelete()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = array($this->input->get('context-item', 0, 'int'));
        $model = $this->getModel();
        $model->delete($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function delete()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->delete($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }
}