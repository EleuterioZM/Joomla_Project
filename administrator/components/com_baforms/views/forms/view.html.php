<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');
 

class baformsViewForms extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $user;

    public function display($tpl = null) 
    {
        $this->about = baformsHelper::aboutUs();
        $app = JFactory::getApplication();
        $layout = $app->input->get('layout', '', 'string');
        if (empty($layout)) {
            $this->items = $this->get('Items');
        } else {
            $this->items = $this->get('ModalItems');
        }
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->user = JFactory::getUser();
        $this->addToolBar();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet(JUri::root().'/components/com_baforms/assets/icons/material/material.css');
        $doc->addStyleSheet('components/com_baforms/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root(true).'/media/vendor/jquery/js/jquery.min.js');
        }
        foreach ($this->items as &$item) {
            $item->order_up = true;
            $item->order_dn = true;
        }
        
        parent::display($tpl);
    }
    
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('FORMS'), 'star');
        if ($this->user->authorise('core.create', 'com_baforms')) {
            JToolBarHelper::addNew('form.add');
        }
        if ($this->user->authorise('core.edit', 'com_baforms')) {
            JToolBarHelper::editList('form.edit');
        }
        if ($this->user->authorise('core.duplicate', 'com_baforms')) {
            JToolBarHelper::custom('forms.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if ($this->user->authorise('core.edit.state', 'com_baforms')) {
            JToolbarHelper::publish('forms.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('forms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        JToolBarHelper::custom('forms.export', 'download.png', 'download.png', 'EXPORT', true);
        if ($this->user->authorise('core.delete', 'com_baforms')) {
            JToolbarHelper::trash('forms.trash');
        }
    }
}