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

class gridboxControllerSystem extends JControllerAdmin
{
    public function getModel($name = 'system', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function applySettings()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->applySettings();
        gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
    }

    public function restore()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $id = $this->input->get('context-item', 0, 'int');
        $pks = [$id];
        $model->publish($pks, 1);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_RESTORED');
    }

    public function publish()
    {
        gridboxHelper::checkUserEditLevel();
        $task = $this->getTask();
        if ($task != 'unpublish') {
            $value = 1;
            $text = $this->text_prefix.'_N_ITEMS_PUBLISHED';
        } else {
            $value = 0;
            $text = $this->text_prefix.'_N_ITEMS_UNPUBLISHED';
        }
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $model = $this->getModel();
        $model->publish($cid, $value);
        gridboxHelper::ajaxReload($text);
    }

    public function contextDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $id = $this->input->get('context-item', 0, 'int');
        $pks = [$id];
        $model = $this->getModel();
        $model->duplicate($pks);
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
        $model = $this->getModel();
        $model->delete([$id]);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function addTrash()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', [], 'post', 'array');
        $model = $this->getModel();
        $model->trash($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_TRASHED');
    }

    public function contextTrash()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $model = $this->getModel();
        $model->trash([$id]);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_TRASHED');
    }
}