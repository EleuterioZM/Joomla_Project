<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewBookingcalendar extends JViewLegacy
{
    protected $items;
    protected $about;
    protected $apps;
    protected $settings;
    protected $state;
    protected $services;
    protected $newBookings;
    protected $upcoming;
    protected $colors;
    protected $color;
    protected $blocks;
    protected $info;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->blocks = $this->get('TimeBlocks');
        $this->state = $this->get('State');
        $this->settings = $this->get('Settings');
        $this->services = $this->get('Services');
        $this->newBookings = $this->get('newBookings');
        $this->upcoming = $this->get('upcoming');
        $this->colors = $this->get('Colors');
        $this->color = (object)[
            'colors' => ['#cdb502', '#ff9391', '#53767c', '#28bacf', '#262626'],
            'default' => '#cdb502'
        ];
        $this->info = $this->get('CustomerInfo');
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $this->apps = gridboxHelper::getApps();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        parent::display($tpl);
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}