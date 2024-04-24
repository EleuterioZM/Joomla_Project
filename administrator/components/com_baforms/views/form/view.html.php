<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class baformsViewForm extends JViewLegacy
{
    public $about;
    public $item;
    public $pages;
    public $templates;
    public $googleFont;
    public $formOptions;
    public $formSettings;
    public $integrations;
    public $user;
    public $formTemplates;

    public function display($tpl = null)
    {
        $this->about = baformsHelper::aboutUs();
        $this->item = $this->get('Item');
        $this->user = JFactory::getUser();
        if (JVERSION >= '4.0.0') {
            $doc = JFactory::getDocument();
            $doc->addScript(JUri::root(true).'/media/vendor/jquery/js/jquery.min.js');
        }
        if (empty($this->item) || empty($this->item->id)) {
            if (!JFactory::getUser()->authorise('core.create', 'com_baforms')) {
                throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
            }
            $this->setLayout('create');
        } else {
            if (!JFactory::getUser()->authorise('core.edit', 'com_baforms')) {
                throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
            }
            $this->formOptions = $this->get('FormOptions');
            compatibleCheck::checkForm($this->item->id, $this->formOptions);
            $this->integrations = baformsHelper::getIntegrations($this->item->id);
            $this->pages = $this->get('Pages');
            $this->templates = baformsHelper::getTemplates($this->formOptions);
            $this->formSettings = baformsHelper::getFormsSettings($this->item->id, $this->formOptions);
            $this->googleFont = $this->get('GoogleFonts');
            $this->form = $this->get('Form');
            $this->formTemplates = $this->get('FormTemplates');
        }
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $isNew = ($this->item && $this->item->id == 0);
        JToolBarHelper::title($isNew ? JText::_('FORMS_NEW') : JText::_('FORMS_EDIT'), 'star');

        parent::display($tpl);
    }
}