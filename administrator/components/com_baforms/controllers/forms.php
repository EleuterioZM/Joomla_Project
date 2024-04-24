<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
use Joomla\Archive\Archive;

class BaformsControllerForms extends JControllerAdmin
{
    public function getModel($name = 'form', $prefix = 'baformsModel', $config = array()) 
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function pasteDesign()
    {
        $model = $this->getModel();
        $id = $this->input->get('id', 0, 'int');
        $str = $this->input->get('design', '{}', 'raw');
        $design = json_decode($str);
        $model->pasteDesign($id, $design);
        echo JText::_('DESIGN_PASTED_SUCCESSFULLY');
        exit;
    }

    public function getFormDesign()
    {
        $model = $this->getModel();
        $id = $this->input->get('id', 0, 'int');
        $options = $model->getFormOptions();
        $settings = baformsHelper::getFormsSettings($id, $options);
        print_r($settings->design);exit;
    }

    public function delete()
    {
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
        $cid = $this->input->get('cid', [], 'array');
        $model = $this->getModel();
        $model->delete($cid);
        $this->postDeleteHook($model, $cid);
        echo JText::_($this->text_prefix.'_N_ITEMS_DELETED');
        exit;
    }

    public function versionCompare()
    {
        $about = baformsHelper::aboutUs();
        $input = JFactory::getApplication()->input;
        $version = $input->get('version', '', 'string');
        $compare = version_compare($about->version, $version);
        echo $compare;
        exit();
    }

    public function checkFormsState()
    {
        $state = baformsHelper::checkFormsState();
        print_r($state);exit();
    }

    public function getUserLicense()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', '', 'string');
        baformsHelper::setAppLicense($data);
    }

    public function setFilters()
    {
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', '', 'string');
        $model = $this->getModel($view);
        $model->populateState();
        exit;
    }

    public function restore()
    {
        $pks = $this->input->getVar('cid', [], 'post', 'array');
        $model = $this->getModel();
        $model->restore($pks);
        echo JText::_('ITEMS_RESTORED');
        exit;
    }

    public function trash()
    {
        $pks = $this->input->getVar('cid', [], 'post', 'array');
        $model = $this->getModel();
        $model->publish($pks, -2);
        echo JText::_('COM_BAFORMS_N_ITEMS_TRASHED');
        exit;
    }

    public function publish()
    {
        $cid = JFactory::getApplication()->input->get('cid', [], 'array');
        $task = $this->getTask();
        switch ($task) {
            case 'trash':
                $value = -2;
                $text = 'COM_BAFORMS_N_ITEMS_TRASHED';
                break;
            case 'unpublish':
                $value = 0;
                $text = 'COM_BAFORMS_N_ITEMS_UNPUBLISHED';
                break;
            default:
                $value = 1;
                $text = 'COM_BAFORMS_N_ITEMS_PUBLISHED';
                break;
        }
        $model = $this->getModel();
        $model->publish($cid, $value);
        echo JText::_($text);
        exit;
    }

    public function contextDuplicate()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->duplicate([$id]);
        echo JText::_('FORM_DUPLICATED');
        exit;
    }

    public function contextRename()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'string');
        $model = $this->getModel();
        $model->rename($id, $title);
        echo JText::_('ITEM_SUCCESSFULLY_RENAMED');
        exit;
    }

    public function contextTrash()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $pks = [$id];
        $model->publish($pks, -2);
        echo JText::_('COM_BAFORMS_N_ITEMS_TRASHED');
        exit;
    }
    
    public function duplicate()
    {
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->duplicate($pks);
        echo JText::_('FORM_DUPLICATED');
        exit;
    }
    
    public function updateForms()
    {
        $config = JFactory::getConfig();
        $path = $config->get('tmp_path').'/pkg_BaForms.zip';
        $data = file_get_contents('php://input');
        $obj = json_decode($data);
        $method = $obj->method;
        $data = $method($obj->package);
        $file = fopen($path, "w+");
        fputs($file, $data);
        fclose($file);
        $this->extract($path, $config->get('tmp_path').'/pkg_BaForms');
        $installer = JInstaller::getInstance();
        $result = $installer->update($config->get('tmp_path').'/pkg_BaForms');
        JFile::delete($path);
        baformsHelper::deleteFolder($config->get('tmp_path').'/pkg_BaForms');
        exit;
    }

    public function addLanguage()
    {
        $input = JFactory::getApplication()->input;
        $method = $input->get('method', '', 'string');
        $url = $input->get('url', '', 'string');
        $zip = $input->get('zip', '', 'string');
        $name = explode('/', $url);
        $name = end($name);
        $config = JFactory::getConfig();
        $tmp_path = $config->get('tmp_path');
        $path = $tmp_path.'/'.$name;
        $name = explode('.', $name);
        $data = $method($zip);
        $file = fopen($path, "w+");
        fputs($file, $data);
        fclose($file);
        $this->extract($path, $tmp_path.'/'.$name[0]);
        $installer = JInstaller::getInstance();
        $result = $installer->install($tmp_path.'/'.$name[0]);
        JFile::delete($path);
        baformsHelper::deleteFolder($tmp_path.'/'.$name[0]);
        echo JText::_('SUCCESS_INSTALL');
        exit;
    }

    public function addLibrary()
    {
        $input = JFactory::getApplication()->input;
        $method = $input->get('method', '', 'string');
        $folder = $input->get('folder', '', 'string');
        $zip = $input->get('zip', '', 'string');
        $package = $input->get('package', '', 'string');
        $data = $method($package);
        $path = JPATH_ROOT.'/components/com_baforms/libraries/';
        $file = fopen($path.$zip, "w+");
        fputs($file, $data);
        fclose($file);
        $this->extract($path.$zip, $path.$folder);
        JFile::delete($path.$zip);
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

    public function exportForms()
    {
        $model = $this->getModel();
        $model->exportForms();
    }

    public function exportForm()
    {
        $input = JFactory::getApplication()->input;
        $export = $input->get('export_id', '', 'string');
        $cid = explode(';', $export);
        $model = $this->getModel();
        $model->exportForm($cid);
    }

    public function download()
    {
        $file = JPATH_ROOT.'/tmp/forms.xml';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    public function importForms()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $files = $input->files->get('ba-files', '', 'array');
        foreach ($files as $item) {
            $name = JPATH_ROOT.'/tmp/'.$item['name'];
            if (!JFile::upload($item['tmp_name'], $name)) {
                echo JText::_('UPLOAD_ERROR');
                exit;
            }
        }
        $xml = simplexml_load_file($name);
        $model->importForms($xml);
        echo JText::_('SUCCESS_UPLOAD');
        exit;
    }
}