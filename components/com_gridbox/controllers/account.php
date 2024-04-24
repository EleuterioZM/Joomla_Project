<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gridboxControllerAccount extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function uploadProfileImage()
    {
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('file', [], 'array');
        $model = $this->getModel();
        $obj = $model->uploadProfileImage($file);
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function deleteSubmitted()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $order = $model->deleteSubmitted($id);
        exit;
    }

    public function getOrder()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $order = $model->getOrder($id);
        $order->date = JHtml::date($order->date, gridboxHelper::$dateFormat);
        $str = json_encode($order);
        echo $str;exit();
    }

    public function saveCustomerInfo()
    {
        $data = $this->input->post->getArray([]);
        $model = $this->getModel();
        $order = $model->saveCustomerInfo($data);
        exit();
    }

    public function socialLogin()
    {
        $data = $this->input->post->getArray([]);
        $model = $this->getModel();
        $model->socialLogin($data);
        exit;
    }

    public function remindUsername()
    {
        $email = $this->input->post->get('email', '', 'string');
        $model = $this->getModel();
        $model->remindUsername($email);
        exit;
    }

    public function remindPassword()
    {
        $email = $this->input->post->get('email', '', 'string');
        $model = $this->getModel();
        $model->remindPassword($email);
        exit;
    }

    public function requestPassword()
    {
        $data = $this->input->post->getArray([]);
        $model = $this->getModel();
        $model->requestPassword($data);
        exit;
    }

    public function resetPassword()
    {
        $data = $this->input->post->getArray([]);
        $model = $this->getModel();
        $model->resetPassword($data);
        exit;
    }
}