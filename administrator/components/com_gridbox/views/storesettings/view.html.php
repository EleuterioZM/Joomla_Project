<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewStoresettings extends JViewLegacy
{
    protected $about;
    protected $apps;
    protected $store;
    protected $customerInfo;
    protected $form;
    protected $integrations;
    protected $languages;
    protected $systemStatuses = [];
    protected $statuses = [];
    
    public function display($tpl = null) 
    {
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $items = $this->get('Items');
        $this->store = $items[0];
        $this->form = $this->get('Form');
        $this->integrations = $this->get('Integrations');
        $this->languages = $this->get('Languages');
        $doc = JFactory::getDocument();
        $this->apps = gridboxHelper::getApps();
        $this->customerInfo = $this->get('CustomerInfo');
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        $this->getStatusesList();
        
        parent::display($tpl);
    }

    public function getStatusesList()
    {
        foreach (gridboxHelper::$store->statuses as $status) {
            $this->statuses[$status->key] = $status;
        }
        $array = [
            $this->getStatusObject(JText::_('OUT_OF_STOCK'), '#ff4f49', 'stock'),
            $this->getStatusObject(JText::_('EXPIRATION_RENEWAL_REMINDER'), '#ff4f49', 'reminder'),
            $this->getStatusObject(JText::_('NEW_BOOKING'), '#1da6f4', 'new-booking'),
            $this->getStatusObject(JText::_('APPOINTMENT_REMINDER'), '#ff4f49', 'appointment-reminder')
        ];
        foreach ($array as $obj) {
            $this->systemStatuses[] = $obj->key;
            $this->statuses[$obj->key] = $obj;
        }
    }

    public function getStatusObject(string $title, string $color, string $key):object
    {
        return (object)[
            'title' => $title,
            'color' => $color,
            'key' => $key
        ];
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}