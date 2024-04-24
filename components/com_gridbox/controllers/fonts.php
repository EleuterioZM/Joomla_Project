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

class gridboxControllerFonts extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function getFontString()
    {
        $model = $this->getModel();
        $fonts = $model->getGoogleFonts();
        $str = gridboxHelper::createFontString($fonts);
        echo $str;exit;
    }

    public function getFonts()
    {
        gridboxHelper::checkUserEditLevel();
        $str = gridboxHelper::getFonts();
        echo $str;exit;
    }

    public function addCustomFont()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = new stdClass();
        $input = JFactory::getApplication()->input;
        $font_family = $input->get('font_family', '', 'string');
        $font = str_replace(' ', '-', $font_family);
        $input = JFactory::getApplication()->input;
        $files = $input->files->get('custom-files', array(), 'array');
        $name = $files['name'];
        $ext = strtolower(JFile::getExt($name));
        $name = str_replace('.'.$ext, '', $name);
        $name = str_replace(' ', '+', $name);
        $file = gridboxHelper::replace($name);
        $file = JFile::makeSafe($file.'.'.$ext);
        $name = str_replace('-', '', $file);
        $name = str_replace($ext, '', $name);
        $name = str_replace('.', '', $name);
        if ($name == '') {
            $file = date("Y-m-d-H-i-s").'.'.$ext;
        }
        $name = strtolower($file);
        $file = $font.'/'.$name;
        if ($model->addFont($file)) {
            $dir = JPATH_ROOT. '/templates/gridbox/library/fonts';
            if (!JFolder::exists($dir)) {
                jFolder::create($dir);
            }
            $dir .= '/'.$font;
            if (!JFolder::exists($dir)) {
                jFolder::create($dir);
            }
            JFile::upload($files['tmp_name'], $dir.'/'.$name);
            $obj->msg = JText::_('FONT_IS_ADDED');
            $obj->type = '';
        } else {
            $obj->msg = JText::_('FONT_ALREADY_INSTALLED');
            $obj->type = 'ba-alert';
        }
        echo json_encode($obj);
        exit;
    }
    
    public function addFont()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = new stdClass();
        if ($model->addFont()) {
            $obj->msg = JText::_('FONT_IS_ADDED');
            $obj->type = '';
        } else {
            $obj->msg = JText::_('FONT_ALREADY_INSTALLED');
            $obj->type = 'ba-alert';
        }
        echo json_encode($obj);
        exit;
    }
    
    public function delete()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->get('font_id', [], 'array');
        $model = $this->getModel();
        $model->delete($pks);
        echo JText::_('COM_GRIDBOX_N_ITEMS_DELETED');
        exit;
    }

    public function refreshList()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = new stdClass();
        $obj->msg = JText::_('SUCCESSFULLY_UPDATED');
        $obj->str = $model->refreshList();
        echo json_encode($obj);
        exit;
    }
}