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

class gridboxControllerTags extends JControllerAdmin
{
    public function getModel($name = 'tags', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function move()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('id', 0, 'int');
        $folder = $this->input->get('folder', 1, 'int');
        $model = $this->getModel();
        $model->move($id, $folder);
        gridboxHelper::ajaxReload('SUCCESS_MOVED');
    }

    public function moveTo()
    {
        gridboxHelper::checkUserEditLevel();
        $folder = $this->input->get('category_id', 1, 'int');
        $cid = $this->input->get('cid', [], 'array');
        $model = $this->getModel();
        foreach ($cid as $id) {
            $model->move($id, $folder);
        }
        gridboxHelper::ajaxReload('SUCCESS_MOVED');
    }

    public function renameFolder()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'string');
        $model = $this->getModel();
        $model->renameFolder($id, $title);
        exit();
    }

    public function deleteTagsFolder()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('context-item', 0, 'int');
        $model = $this->getModel();
        $model->deleteTagsFolder($id);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function orderFolders()
    {
        gridboxHelper::checkUserEditLevel();
        $string = $this->input->get('data', '', 'raw');
        $data = json_decode($string);
        $model = $this->getModel();
        $model->orderFolders($data);
        exit();
    }

    public function createFolder()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'string');
        $model = $this->getModel();
        $model->createFolder($id, $title);
        gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
    }

    public function updateTags()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->updateTags();
        gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
    }

    public function addTag()
    {
        gridboxHelper::checkUserEditLevel();
        $title = $this->input->getVar('tag_name');
        $folder = $this->input->get('folder', 1, 'int');
        $model = $this->getModel();
        $obj = $model->addTag($title);
        if ($folder != 1) {
            $model->move($obj->id, $folder);
        }
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

    public function contextDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = array();
        $array[] = $id;
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
}