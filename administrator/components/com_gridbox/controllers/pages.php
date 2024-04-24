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
use Joomla\Archive\Archive;

class gridboxControllerPages extends JControllerAdmin
{
    public function getModel($name = 'gridbox', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function versionCompare()
    {
        $about = gridboxHelper::aboutUs();
        $input = JFactory::getApplication()->input;
        $version = $input->get('version', '', 'string');
        $compare = version_compare($about->version, $version);
        echo $compare;
        exit();
    }

    public function orderPages()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->orderPages();
        exit();
    }

    public function addLanguage()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $method = $input->get('method', '', 'string');
        $url = $input->get('url', '', 'string');
        $zip = $input->get('zip', '', 'string');
        $name = explode('/', $url);
        $name = end($name);
        $config = JFactory::getConfig();
        $path = $config->get('tmp_path') . '/'. $name;
        $name = explode('.', $name);
        $data = $method($zip);
        $file = fopen($path, "w+");
        fputs($file, $data);
        fclose($file);
        $this->extract($path, $config->get('tmp_path').'/'.$name[0]);
        $installer = JInstaller::getInstance();
        $result = $installer->install($config->get('tmp_path').'/'.$name[0]);
        JFile::delete($path);
        gridboxHelper::deleteFolder($config->get('tmp_path').'/'.$name[0]);
        echo JText::_('SUCCESS_INSTALL');
        exit;
    }

    public function extract($from, $to)
    {
        if (JVERSION >= '4.0.0') {
            $archive = new Archive();
            $archive->extract($from, $to);
        } else {
            JArchive::extract($from, $to);
        }
    }

    public function setFilters()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', '', 'string');
        $model = $this->getModel($view);
        $model->setFilters();
        exit;
    }

    public function checkBlogsTour()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->checkBlogsTour();
    }

    public function checkSidebarTour()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->checkSidebarTour();
    }

    public function addTrash()
    {
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $flag = true;
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
            $array = [];
            $array[] = $id;
            $model = $this->getModel();
            $model->trash($array);
            gridboxHelper::ajaxReload($this->text_prefix.'_N_ITEMS_TRASHED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function checkGridboxState()
    {
        $state = gridboxHelper::checkGridboxState();
        print_r($state);exit();
    }

    public function getAppLicense()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', '', 'string');
        gridboxHelper::setAppLicense($data);
        gridboxHelper::setAppLicenseBalbooa($data);
        gridboxHelper::ajaxReload('SUCCESS_INSTALL');
    }

    public function addPlugins()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $plugins = $input->get('plugins', '', 'string');
        $data = json_decode($plugins);
        $db = JFactory::getDbo();
        foreach ($data as $group) {
            foreach ($group as $plugin) {
                $db->insertObject('#__gridbox_plugins', $plugin);
            }
        }
    }

    public function applySingle()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->applySingle();
    }

    public function deleteGridboxAppItem()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->deleteApp();
    }

    public function deleteApp()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('blog', 0, 'int');
        $user = JFactory::getUser();
        if ($user->authorise('core.delete.app.'.$id, 'com_gridbox')) {
            $model = $this->getModel();
            $model->deleteApp();
            $this->setRedirect('index.php?option=com_gridbox');
            gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function duplicateApp()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->duplicateApp();
        gridboxHelper::ajaxReload('GRIDBOX_DUPLICATED');
    }

    public function addApp()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $type = $input->get('type', '', 'string');
        $model = $this->getModel();
        $id = $model->addApp($type);
        if ($type != 'single') {
            $model = $this->getModel('category');
            $model->createCat('Uncategorised', $id);
        }
        if ($type == 'products') {
            $appslist = $this->getModel('appslist');
            $appslist->addSystemApp('reviews');
        }
        exit();
    }

    public function publish()
    {
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $task = $this->getTask();
        if ($task != 'unpublish') {
            $value = 1;
            $text = $this->text_prefix . '_N_ITEMS_PUBLISHED';
        } else {
            $value = 0;
            $text = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
        }
        $flag = true;
        foreach ($cid as $pk) {
            $assets = new gridboxAssetsHelper($pk, 'page');
            $flag = $assets->checkPermission('core.edit.state');
            if (!$flag) {
                break;
            }
        }
        if ($flag) {
            $model = $this->getModel();
            $model->publish($cid, $value);
            $model->sendSubmissionEmail($cid, $value);
            gridboxHelper::triggerEvent('onGidboxPagesAfterPublish', [$cid], 'finder');
        } else {
            $text = 'JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED';
        }
        gridboxHelper::ajaxReload($text);
    }

    public function checkRate()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->checkRate();
    }

    public function contextDuplicate()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = [$id];
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
    
    public function updateGridbox()
    {
        gridboxHelper::checkUserEditLevel();
        $config = JFactory::getConfig();
        $path = $config->get('tmp_path').'/pkg_Gridbox.zip';
        $data = file_get_contents('php://input');
        $obj = json_decode($data);
        $method = $obj->method;
        $data = $method($obj->package);
        $file = fopen($path, "w+");
        fputs($file, $data);
        fclose($file);
        $this->extract($path, $config->get('tmp_path').'/pkg_Gridbox');
        $installer = JInstaller::getInstance();
        $result = $installer->update($config->get('tmp_path').'/pkg_Gridbox');
        JFile::delete($path);
        gridboxHelper::deleteFolder($config->get('tmp_path').'/pkg_Gridbox');
        exit;
    }
    
    public function exportXML()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->exportXML();
    }

    public function download()
    {
        $file = $this->input->get('file', '', 'string');
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=gridbox.xml');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
            exit;
        }
    }
}