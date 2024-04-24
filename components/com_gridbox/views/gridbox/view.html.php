<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewgridbox extends JViewLegacy
{
    protected $item;
    protected $app;
    protected $category;
    protected $custom;
    protected $layout;
    protected $edit_type;
    
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $this->edit_type = $input->get('edit_type', '', 'string');
        $id = $input->get('id', 0, 'int');
        if (empty($id)) {
            return gridboxHelper::raiseError(404, JText::_('NOT_FOUND'));
        }
        $this->item = $this->get('Item');
        $user = JFactory::getUser();
        if ($this->edit_type == 'post-layout' || $this->edit_type == 'blog') {
            $editFlag = $user->authorise('core.edit.layouts', 'com_gridbox.app.'.$this->item->id);
        } else if ($this->edit_type == '') {
            $pageAssets = new gridboxAssetsHelper($this->item->id, 'page');
            $editPage = $pageAssets->checkPermission('core.edit');
            if (!$editPage && !empty($this->item->page_category)) {
                $editPage = $pageAssets->checkEditOwn($this->item->page_category);
            }
            $editFlag = $editPage;
        } else {
            $editFlag = $user->authorise('core.edit', 'com_gridbox');
        }
        if (!$editFlag) {
            gridboxHelper::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
        }
        $this->layout = $input->get('layout', '', 'string');
        $this->app = $input->get('app_id', 0, 'int');
        $this->category = $input->get('category', '', 'string');
        $doc = JFactory::getDocument();
        $doc->setTitle('Gridbox Editor');
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
        } else {
            $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
        }
        $doc->addStyleSheet(JURI::root() . 'components/com_gridbox/assets/css/ba-style.css');
        $doc->addScriptDeclaration("var IMAGE_PATH = '".IMAGE_PATH."';");
        $time = $this->item->saved_time;
        if (!empty($time)) {
            $time = '?'.$time;
        }
        if ($this->edit_type == '') {
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/style-'.$this->item->id.'.css'.$time);
            if ($this->item->app_type == 'blog') {
                $doc->addStyleSheet(JURI::root().'components/com_gridbox/libraries/ckeditor/css/ckeditor.css');
                $doc->addStyleDeclaration($this->item->post_editor_wrapper ?? '');
            }
        } else if ($this->edit_type == 'blog') {
            $this->item->app_type = '';
            $this->item->params = $this->get('AppLayout');
            $this->item->style = $this->get('AppItems');
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/app-'.$this->item->id.'.css'.$time);
        } else if ($this->edit_type == 'post-layout') {
            if (!empty($this->item->postTheme)) {
                $this->item->theme = $this->item->postTheme;
            }
            $this->item->app_type = '';
            $this->item->params = $this->get('PageLayout');
            $this->item->style = $this->get('pageItems');
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/post-'.$this->item->id.'.css'.$time);
        } else if ($this->edit_type == 'system') {
            $this->item->app_type = '';
            if (empty($this->item->html)) {
                $system = $this->get('SystemLayout');
                $this->item->html = $system->html;
                $this->item->items = $system->items;
            }
            $this->item->options = json_decode($this->item->page_options);
            if (($this->item->type == '404' && $this->item->options->enable_header != 1) || $this->item->type == 'offline'
                || $this->item->type == 'preloader') {
                $doc->addStyleDeclaration('header.header, footer.footer {display:none;}');
            }
            if ($this->item->type == 'preloader') {
                $doc->addStyleSheet(JUri::root().'components/com_gridbox/libraries/preloader/css/animation.css');
                $doc->addStyleDeclaration('#ba-edit-section + .ba-add-section {display:none !important;}');
            }
            $doc->addScriptDeclaration('var systemType = "'.$this->item->type.'";');
            $doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/system-page-'.$this->item->id.'.css'.$time);
            $this->item->params = $this->item->html;
            $this->item->style = $this->item->items;
        }
        $this->item->params = gridboxHelper::checkModules($this->item->params, $this->item->style);
        $doc->addScript(JUri::root().'index.php?option=com_gridbox&task=editor.getDefaultElementsBox');
        $doc->setMetaData('cache-control', 'no-cache', true);
        $doc->setMetaData('expires', '0', true);
        $doc->setMetaData('pragma', 'no-cache', true);
        $this->reeadCssFile();
        if (!empty($this->layout)) {
            $this->setLayout($this->layout);
        }
        
        parent::display($tpl);
    }

    protected function reeadCssFile()
    {
        jimport('joomla.filesystem.file');
        $this->custom = new stdClass();
        $id = $this->item->theme;
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$id.'.css';
        if (JFile::exists($file)) {
            $this->custom->code = gridboxHelper::readFile($file);
        } else {
            $this->custom->code = '';
        }
        $file = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-'.$id.'.js';
        if (JFile::exists($file)) {
            $this->custom->js = gridboxHelper::readFile($file);
        } else {
            $this->custom->js = '';
        }
    }
}