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

class gridboxControllerAuthors extends JControllerAdmin
{
    public function getModel($name = 'authors', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function checkUser()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('id', '', 'int');
        $currentUser = $this->input->get('currentUser', '', 'int');
        $model = $this->getModel();
        $result = $model->checkUser($id, $currentUser);
        print_r($result);exit;
    }

    public function updateAuthors()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->updateAuthors();
        gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
    }

    public function addAuthor()
    {
        gridboxHelper::checkUserEditLevel();
        $title = $this->input->get('tag_name', '', 'string');
        $user = $this->input->get('user_id', 0, 'int');
        $model = $this->getModel();
        $model->addAuthor($title, $user);
        gridboxHelper::ajaxReload('ITEM_CREATED');
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
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
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