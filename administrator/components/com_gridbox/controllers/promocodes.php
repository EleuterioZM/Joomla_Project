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

class gridboxControllerPromocodes extends JControllerAdmin
{
    public function getModel($name = 'promocodes', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function contextDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('context-item', 0, 'int');
        $array = array($id);
        $model = $this->getModel();
        $model->duplicate($array);
        gridboxHelper::ajaxReload('GRIDBOX_DUPLICATED');
    }

    public function duplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->duplicate($pks);
        gridboxHelper::ajaxReload('GRIDBOX_DUPLICATED');
    }

    public function updatePromoCode()
    {
        gridboxHelper::checkUserEditLevel();
        $post = $this->input->post->getArray(array());
        $data = (object)$post;
        $model = $this->getModel();
        $model->updatePromoCode($data);
        $obj = new stdClass();
        $obj->message = JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function getPromoCodes()
    {
        $model = $this->getModel();
        $obj = $model->getPromoCodes();
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function getProducts()
    {
        $model = $this->getModel();
        $category = $this->input->get('category', 0, 'int');
        $app_type = $this->input->get('app_type', '', 'string');
        $obj = $model->getProducts($category, $app_type);
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function getCategories()
    {
        $model = $this->getModel();
        $obj = $model->getCategories();
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function getOptions()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->getOptions($id);
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function addPromoCode()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->addPromoCode();
        exit;
    }

    public function delete()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->delete($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function contextDelete()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('context-item', 0, 'int');
        $array = array();
        $array[] = $id;
        $model = $this->getModel();
        $model->delete($array);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function publish()
    {
        gridboxHelper::checkUserEditLevel();
        $task = $this->getTask();
        if ($task != 'unpublish') {
            $value = 1;
            $text = $this->text_prefix . '_N_ITEMS_PUBLISHED';
        } else {
            $value = 0;
            $text = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
        }
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $model = $this->getModel();
        $model->publish($cid, $value);
        gridboxHelper::ajaxReload($text);
    }
}