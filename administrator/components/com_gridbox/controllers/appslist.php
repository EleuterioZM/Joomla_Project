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

class gridboxControllerAppslist extends JControllerAdmin
{
    public function getModel($name = 'appslist', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function refreshSidebar()
    {
        $model = $this->getModel();
        $str = $model->refreshSidebar();
        echo $str;
        exit;
    }

    public function refreshApps()
    {
        $model = $this->getModel();
        $str = $model->refreshApps();
        echo $str;
        exit;
    }

    public function getGroupApps()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $str = $model->getGroupApps($id);
        echo $str;
        exit;
    }

    public function setAppsGroup()
    {
        $ids = $this->input->get('ids', [], 'array');
        $parent = $this->input->get('parent', 0, 'int');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel();
        $str = $model->setAppsGroup($ids, $parent, $type);
        echo $str;
        exit;
    }

    public function ungroup()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->ungroup($id);
        exit;
    }

    public function orderApps()
    {
        $orders = $this->input->get('orders', [], 'array');
        $ids = $this->input->get('ids', [], 'array');
        $types = $this->input->get('types', [], 'array');
        $parent_id = $this->input->get('parent_id', 0, 'int');
        $model = $this->getModel();
        $model->orderApps($ids, $orders, $types, $parent_id);
        exit;
    }

    public function renameApp()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'string');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel();
        $model->renameApp($id, $title, $type);
        exit();
    }

    public function getSystemApps()
    {
        $model = $this->getModel();
        $systemApps = $model->getSystemApps();
        $str = json_encode($systemApps);
        echo $str;
        exit();
    }

    public function addSystemApp()
    {
        $input = JFactory::getApplication()->input;
        $type = $input->post->get('type', '', 'string');
        if (!empty($type)) {
            $model = $this->getModel();
            $model->addSystemApp($type);
        }
        print_r($type);exit;
        exit();
    }
}