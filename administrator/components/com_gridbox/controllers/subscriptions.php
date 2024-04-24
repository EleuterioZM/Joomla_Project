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

class gridboxControllerSubscriptions extends JControllerAdmin
{
    public function getModel($name = 'subscriptions', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function setExpires()
    {
        $id = $this->input->get('id', 0, 'int');
        $expires = $this->input->get('expires', '', 'string');
        $model = $this->getModel();
        $obj = $model->setExpires($id, $expires);
        $obj->message = JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        $str = json_encode($obj);
        print_r($str);exit;
    }

    public function getSubscription()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $subscription = $model->getSubscription($id);
        $str = json_encode($subscription);
        print_r($str);exit;
    }

    public function setRenew()
    {
        $data = $this->input->get('data', '{}', 'string');
        $obj = json_decode($data);
        $model = $this->getModel();
        $model->setRenew($obj);
        print_r('{}');exit;
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