<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gridboxControllerGridbox extends JControllerForm
{
    public function getModel($name = 'gridbox', $prefix = 'gridboxModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function fixDB()
    {
        $db = JFactory::getDbo();
        try {
            $query = "ALTER TABLE `#__gridbox_pages` DROP COLUMN `fields_data`";
            $db->setQuery($query)
                ->execute();
            $query = "ALTER TABLE `#__gridbox_app` DROP COLUMN `fields`";
            $db->setQuery($query)
                ->execute();
        } catch (Exception $e) {

        }
        exit;
    }

    public function updatePermissions()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $type = $input->get('type', '', 'string');
        $assets = new gridboxAssetsHelper($id, $type);
        if ($assets->checkPermission('core.admin', 'com_gridbox')) {
            $rules = $input->get('rules', '{}', 'string');
            $assets->updateRules($rules);
        }
    }

    public function testNewPermissions()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $type = $input->get('type', '', 'string');
        $actions = $input->get('actions', '', 'string');
        $rules = $input->get('rules', '{}', 'string');
        $array = explode(', ', $actions);
        $assets = new gridboxAssetsHelper($id, $type);
        $permissions = $assets->getPermission();
        $assets->updateRules($rules);
        $groups = $assets->getUserGroups();
        $obj = new stdClass();
        foreach ($groups as $group) {
            $obj->{$group->id} = $assets->getGroupPermissions($group->id, $array);
        }
        $assets->updateRules($permissions->rules);
        $str = json_encode($obj);
        print_r($str);exit;
    }

    public function getPermissions()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $type = $input->get('type', '', 'string');
        $actions = $input->get('actions', '', 'string');
        $array = explode(', ', $actions);
        $assets = new gridboxAssetsHelper($id, $type);
        $permissions = $assets->getPermission();
        $groups = $assets->getUserGroups();
        $data = new stdClass();
        $data->rules = json_decode($permissions->rules);
        $data->groups = new stdClass();
        foreach ($groups as $group) {
            $data->groups->{$group->id} = $assets->getGroupPermissions($group->id, $array);
        }
        $str = json_encode($data);
        print_r($str);exit;
    }

    public function checkJoomlaContentCount()
    {
        $model = $this->getModel();
        $content = $model->checkJoomlaContentCount();
        $str = json_encode($content);
        echo $str;exit;
    }

    public function importJoomlaTags()
    {
        $model = $this->getModel();
        $id = $model->importJoomlaTags();
        echo $id;exit;
    }

    public function importJoomlaCategories()
    {
        $model = $this->getModel();
        $id = $model->importJoomlaCategories();
        echo $id;exit;
    }

    public function importJoomlaArticles()
    {
        $model = $this->getModel();
        $model->importJoomlaArticles();
        exit;
    }
    
    public function save($key = null, $urlVar = null)
    {
        gridboxHelper::checkUserEditLevel();
        $data = $this->input->post->get('jform', array(), 'array');
        $model = $this->getModel();
        $table = $model->getTable();
        $url = $table->getKeyName();
        parent::save($key = $data['id'], $urlVar = $url);
    }

    public function edit($key = null, $urlVar = null )
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
            $this->setRedirect('index.php?option=com_gridbox', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            return false;
        }
        $cid = $this->input->post->get('cid', array(), 'array');
        if (empty($cid)) {
            $cid[0] = $this->input->get('id');
        }
        $user = JFactory::getUser();
        $url = gridboxHelper::getEditorLink().'&id=' .$cid[0];
        $this->setRedirect($url);
    }

    public function getSession()
    {
        $session = JFactory::getSession();
        echo new JResponseJson($session->getState());
        exit;
    }
    
    public function updateTags()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->updateTags();
    }

    public function getPageTags()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->getPageTags();
    }

    public function updateParams()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('ba_id', 0, 'int');
        $category = $input->get('page_category', 0, 'int');
        $pageAssets = new gridboxAssetsHelper($id, 'page');
        $editPage = $pageAssets->checkPermission('core.edit');
        if (!$editPage && $category) {
            $editPage = $pageAssets->checkEditOwn($category);
        }
        if ($editPage) {
            $model = $this->getModel();
            $model->updateParams();
            $message = 'JLIB_APPLICATION_SAVE_SUCCESS';
        } else {
            $message = 'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED';
        }
        gridboxHelper::ajaxReload($message);
    }
}