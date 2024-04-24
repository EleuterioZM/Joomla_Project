<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
use Joomla\Archive\Archive;

class gridboxControllerIcons extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function addCustomIcons()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = new stdClass();
        $input = JFactory::getApplication()->input;
        $icon = $name = $input->get('icon_name', '', 'string');
        $name = str_replace(' ', '+', $name);
        $file = gridboxHelper::replace($name);
        $file = JFile::makeSafe($file.'.zip');
        $name = str_replace('-', '', $file);
        $name = str_replace('zip', '', $name);
        $name = str_replace('.', '', $name);
        if ($name == '') {
            $file = date("Y-m-d-H-i-s").'.zip';
        }
        $name = strtolower($file);
        $fileName = str_replace('.zip', '', $name);
        $dir = JPATH_ROOT. '/templates/gridbox/library/icons/custom-icons';
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }
        if (JFolder::exists($dir.'/'.$fileName)) {
            $obj->msg = JText::_('ICONS_ALREADY_INSTALLED');
            $obj->type = 'ba-alert';
        } else {
            $input = JFactory::getApplication()->input;
            $files = $_FILES['custom-files'];
            move_uploaded_file($files['tmp_name'], $dir.'/'.$name);
            if (!JFolder::exists($dir.'/tmp')) {
                JFolder::create($dir.'/tmp');
            }
            JFolder::create($dir.'/'.$fileName);
            if (JVERSION >= '4.0.0') {
                $archive = new Archive();
                $archive->extract($dir.'/'.$name, $dir.'/tmp/'.$fileName);
            } else {
                JArchive::extract($dir.'/'.$name, $dir.'/tmp/'.$fileName);
            }
            JFile::delete($dir.'/'.$name);
            $model->installIcons($fileName, $icon);
            gridboxHelper::deleteFolder($dir.'/tmp');
            $obj->msg = JText::_('ICONS_IS_ADDED');
            $obj->type = '';
        }
        echo json_encode($obj);
        exit;
    }
    
    public function delete()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->delete();
        echo JText::_('COM_GRIDBOX_N_ITEMS_DELETED');
        exit;
    }
}