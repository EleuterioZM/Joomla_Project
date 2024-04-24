<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class baformsViewUploader extends JViewLegacy
{
    protected $_folders;
    protected $_images;
    protected $_items;
    protected $_parent;
    protected $_list;
    protected $_breadcrumb;
    protected $_move_to = array();
    protected $_page = 0;
    protected $_pages = 1;
    protected $_limit = 25;
    protected $about;
    protected $_imagesExt;
    protected $fileTypes;
    protected $folder;
    protected $visibleBranch;
    
    public function display ($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_baforms')) {
            throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
            return;
        }
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode('<br />', $errors), 500);
            return false;
        }
        $this->fileTypes = $this->get('fileTypes');
        $doc = JFactory::getDocument();
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        } else {
            $doc->addScript(JUri::root().'media/jui/js/jquery.min.js');
        }
        $doc->addScript(JUri::root().'administrator/components/com_baforms/assets/libraries/bootstrap/bootstrap.js');
        $doc->addScriptDeclaration('var $fileTypes = '.json_encode($this->fileTypes).', IMAGE_PATH = "'.IMAGE_PATH.'";');
        $this->_folders = $this->get('Folders');
        $this->_images = $this->get('Images');
        $this->_items = array_merge($this->_folders, $this->_images);
        $this->_imagesExt = array('jpg', 'png', 'gif', 'jpeg', 'svg', 'ico', 'webp');
        $this->_parent = $this->get('parent');
        $this->folder = '';
        $this->about = baformsHelper::aboutUs();
        $this->addToolBar();
        $this->drawPagination();
        if (is_array($this->_folders)) {
            $this->_breadcrumb = $this->get('Breadcrumb');
            $this->visibleBranch = new stdClass();
            foreach ($this->_breadcrumb as $value) {
                $this->visibleBranch->{$value->path} = true;
            }
        }
        if ($doc->getDirection() == 'rtl') {
            $doc->addStyleSheet(JUri::root().'components/com_baforms/assets/css/rtl-ba-style.css?'.$this->about->version);
        }
        if (!JFactory::getUser()->authorise('core.edit', 'com_baforms')) {
            throw new \Exception(JText::_('NOT_FOUND'), 404);
        }
        $this->_list = $this->get('FolderList');
        $this->_list = $this->drawFolderList($this->_list);
        parent::display($tpl);
    }

    protected function drawPagination()
    {
        $input = JFactory::getApplication()->input;
        $page = $input->get('page', '', 'string');
        if (!empty($page)) {
            $this->_page = $page * 1;
        }
        $limit = $input->get('ba_limit', '', 'string');
        if (!$limit) {
            $limit = 25;
        }
        $this->_limit = $limit;
        $count = count($this->_items);
        $this->_pages = ceil($count / $limit);
        if ($limit == 1) {
            $this->_pages = 1;
        }
        if ($this->_pages > 1) {
            $this->_items = array_slice($this->_items, $this->_page * $limit, $limit);
        }
    }

    protected function drawFolderList($list)
    {
        $str = '<ul>';
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'layout');
        foreach ($list as $value) {
            $str .= '<li data-path="'.$value->path.'"';            
            $className = '';
            if ($this->_parent == JPATH_ROOT.'/'.IMAGE_PATH.$value->path) {
                $this->folder = $value->path;
                $className .= ' active';
            }
            if (isset($this->visibleBranch->{$value->path})) {
                $className .= ' visible-branch';
            }
            $str .= ' class="'.$className.'"';
            $str .= '><a data-href="folder='.$value->path;
            if ($layout == 'thubnail') {
                $str .= '&layout=thubnail';
            }
            $str .= '&tmpl=component" ><i class="zmdi zmdi-folder"></i> '.$value->name.'</a>';
            if (count($value->childs) > 0) {
                $str .= '<i class="zmdi zmdi-chevron-right"></i>';
                $str .= $this->drawFolderList($value->childs);
            }
            $str .= '</li>';
        }
        $str .= '</ul>';

        return $str;
    }

    protected function getFileSize($size)
    {
        $size = $size / 1024;
        $size = floor($size);
        if ($size >= 1024) {
            $size = $size / 1024;
            $size = floor($size);
            $size = (string)$size .' MB';
        } else {
            $size = (string)$size .' KB';
        }

        return $size;
    }

    protected function addToolBar()
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);
    }
}