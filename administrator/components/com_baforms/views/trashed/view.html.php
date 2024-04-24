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
 

class baformsViewTrashed extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $user;

    public function display($tpl = null) 
    {
        $this->about = baformsHelper::aboutUs();
        $this->items = $this->get('Items');
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
        JToolBarHelper::title(JText::_('TRASHED_ITEMS'), 'star');
        if ($this->user->authorise('core.edit.state', 'com_baforms')) {
            JToolBarHelper::custom('forms.restore', 'undo-2.png', 'undo-2.png', 'RESTORE', true);
        }
        if ($this->user->authorise('core.delete', 'com_baforms')) {
            JToolBarHelper::deleteList('', 'forms.delete');
        }
    }
}