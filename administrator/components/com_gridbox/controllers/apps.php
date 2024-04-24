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

class gridboxControllerApps extends JControllerAdmin
{
    public $variations;

    public function getModel($name = 'gridbox', $prefix = 'gridboxModel', $config = array()) 
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function getDefaultsSeo()
    {
        $id = $this->input->get('id', 0, 'int');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel('apps');
        $seo = $model->getDefaultsSeo($id, $type);
        $str = json_encode($seo);
        echo $str;
        exit;
    }

    public function setDefaultsSeo()
    {
        $seo = (object)[
            'id' => $this->input->get('id', 0, 'int'),
            'item_id' => $this->input->get('item_id', 0, 'int'),
            'item_type' => $this->input->get('item_type', '', 'string'),
            'meta_title' => $this->input->get('meta_title', '', 'string'),
            'meta_description' => $this->input->get('meta_description', '', 'string'),
            'share_image' => $this->input->get('share_image', '', 'string'),
            'share_title' => $this->input->get('share_title', '', 'string'),
            'share_description' => $this->input->get('share_description', '', 'string'),
            'sitemap_include' => $this->input->get('sitemap_include', '', 'string'),
            'changefreq' => $this->input->get('changefreq', '', 'string'),
            'priority' => $this->input->get('priority', '', 'string'),
            'schema_markup' => $this->input->get('schema_markup', '', 'raw')
        ];
        $model = $this->getModel('apps');
        $model->setDefaultsSeo($seo);
        exit;
    }

    public function importCSV()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $matched = $this->input->get('matched', '{}', 'raw');
        $id = $this->input->get('id', 0, 'int');
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $type = $this->input->get('type', '{}', 'string');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel('apps');
        $obj = json_decode($matched);
        $category = $this->getModel('category');
        $model->importCSV($file, $id, $overwrite, $obj, $type, $category);
    }

    public function checkMatchedCsv()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $matched = $this->input->get('matched', '{}', 'raw');
        $id = $this->input->get('id', 0, 'int');
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel('apps');
        $obj = json_decode($matched);
        $model->checkMatchedCsv($file, $id, $overwrite, $obj);
    }

    public function checkGridboxCsv()
    {
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $file = $tmp_path.'/gridbox.csv';
        $id = $this->input->get('id', 0, 'int');
        $overwrite = $this->input->get('overwrite', 0, 'int');
        $item = $this->input->files->get('file');
        JFile::upload($item['tmp_name'], $file);
        $model = $this->getModel('apps');
        $model->checkGridboxCsv($file, $id, $overwrite);
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
        $model = $this->getModel('apps');
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

    public function getAppCells()
    {
        $id = $this->input->get('id', 0, 'int');
        $pks = $this->input->get('pks', [], 'array');
        $model = $this->getModel('apps');
        $model->getAppCells($id, $pks);
    }

    public function exportCSV()
    {
        $model = $this->getModel('apps');
        $id = $this->input->get('id', 0, 'int');
        $pks = $this->input->get('pks', [], 'array');
        $cells = $this->input->get('cells', [], 'array');
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $response = new stdClass();
        if (!empty($tmp_path) && JFolder::exists($tmp_path)) {
            $response->file = $model->exportCSV($id, $pks, $cells, $tmp_path);
            $response->status = true;
        } else {
            $response->status = false;
            $response->message = 'Temp Folder is not exists';
        }
        $str = json_encode($response);
        print_r($str);
        exit();
    }

    public function download()
    {
        $file = $this->input->get('file', '', 'string');
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=gridbox-products.csv');
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

    public function setFeatured()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $featured = $input->get('featured', 0, 'int');
        $model = $this->getModel();
        $model->setFeatured($id, $featured);
        exit();
    }

    public function moveTo()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel('category', 'gridboxModel');
        $input = JFactory::getApplication()->input;
        $data = $input->get('category_id', '', 'string');
        $obj = json_decode($data);
        $cid = $input->get('cid', [], 'array');
        foreach ($cid as $id) {
            $model->pageMoveTo($obj->id, $id, $obj->app_id);
        }
        gridboxHelper::ajaxReload('SUCCESS_MOVED');
    }

    public function pageMoveTo()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel('category', 'gridboxModel');
        $input = JFactory::getApplication()->input;
        $data = $input->get('category_id', '', 'string');
        $obj = json_decode($data);
        $id = $input->get('context-item', 0, 'int');
        $model->pageMoveTo($obj->id, $id, $obj->app_id);
        gridboxHelper::ajaxReload('SUCCESS_MOVED');
    }

    public function orderCategories()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel('category');
        $model->orderCategories();
        exit();
    }

    public function updateCategory()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('category-id', '', 'string');
        if (gridboxHelper::assetsCheckPermission($id, 'category', 'core.edit', '')) {
            $model = $this->getModel('category', 'gridboxModel');
            $model->updateCategory();
            gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
        } else {
            gridboxHelper::ajaxReload('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED');
        }
    }

    public function deleteCategory()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('context-item', '', 'string');
        $obj = json_decode($data);
        $model = $this->getModel('category', 'gridboxModel');
        if ($model->checkDeletePermissions($obj)) {
            $model->removeCategory();
            gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function categoryMoveTo()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel('category', 'gridboxModel');
        $model->moveTo();
        gridboxHelper::ajaxReload('SUCCESS_MOVED');
    }

    public function categoryDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $model = $this->getModel('category', 'gridboxModel');
        $gridboxModel = $this->getModel();
        $model->duplicate($id, $gridboxModel);
        gridboxHelper::ajaxReload('GRIDBOX_DUPLICATED');
    }

    public function applySettings()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('blog', 0, 'int');
        $user = JFactory::getUser();
        if ($user->authorise('core.edit.app.'.$id, 'com_gridbox')) {
            $model = $this->getModel();
            $model->applySettings();
            gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
        } else {
            gridboxHelper::ajaxReload('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED');
        }
    }

    public function addCategory()
    {
        $input = JFactory::getApplication()->input;
        $parent = $input->get('parent_id', 0, 'int');
        $blog = $input->get('blog', 0, 'int');
        $obj = new stdClass();
        if (!empty($parent)) {
            $id = $parent;
            $type = 'category';
        } else {
            $id = $blog;
            $type = 'app';
        }
        if (gridboxHelper::assetsCheckPermission($id, $type, 'core.create', '')) {
            $model = $this->getModel('category', 'gridboxModel');
            $title = $input->get('category_name', '', 'string');
            $order = $input->get('category_order_list', 0, 'int');
            $obj->id = $model->createCat($title, $blog, $parent, $order);
            $obj->msg = JText::_('ITEM_CREATED');
        } else {
            $obj->id = 0;
            $obj->msg = JText::_('JERROR_CORE_CREATE_NOT_PERMITTED');
        }
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function publish()
    {
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $task = $this->getTask();
        if ($task != 'unpublish') {
            $text = $this->text_prefix.'_N_ITEMS_PUBLISHED';
        } else {
            $text = $this->text_prefix.'_N_ITEMS_UNPUBLISHED';
        }
        foreach ($cid as $pk) {
            $assets = new gridboxAssetsHelper($pk, 'page');
            $flag = $assets->checkPermission('core.edit.state');
            if (!$flag) {
                break;
            }
        }
        if ($flag) {
            parent::publish();
        } else {
            $text = 'JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED';
        }
        gridboxHelper::ajaxReload($text);
    }

    public function addTrash()
    {
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        foreach ($pks as $pk) {
            $assets = new gridboxAssetsHelper($pk, 'page');
            $flag = $assets->checkPermission('core.delete');
            if (!$flag) {
                break;
            }
        }
        if ($flag) {
            $model = $this->getModel();
            $model->trash($pks);
            gridboxHelper::ajaxReload($this->text_prefix . '_N_ITEMS_TRASHED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function contextTrash()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $assets = new gridboxAssetsHelper($id, 'page');
        $flag = $assets->checkPermission('core.delete');
        if ($flag) {
            $array = array($id);
            $model = $this->getModel();
            $model->trash($array);
            gridboxHelper::ajaxReload($this->text_prefix . '_N_ITEMS_TRASHED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }
    
    public function contextDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = [];
        $array[] = $id;
        $model = $this->getModel();
        $model->duplicate($array);
        gridboxHelper::ajaxReload('GRIDBOX_DUPLICATED');
    }
    
    public function duplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $blog = $this->input->get('blog', 0, 'int');
        $model = $this->getModel();
        $model->duplicate($pks);
        gridboxHelper::ajaxReload('gridbox_DUPLICATED');
    }

    public function getTags()
    {
        gridboxHelper::checkUserEditLevel();
        $tags = gridboxHelper::getTags();
        $json = json_encode($tags);
        echo $json;
        exit;
    }
}