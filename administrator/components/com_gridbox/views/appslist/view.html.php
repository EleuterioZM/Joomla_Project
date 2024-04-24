<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewappslist extends JViewLegacy
{
    protected $items;
    protected $state;
    protected $about;
    protected $apps;
    protected $system;

    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->system = $this->get('SystemApps');
        $this->apps = gridboxHelper::getApps();
        $this->state = $this->get('State');
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
                
        parent::display($tpl);
    }

    public function createAppObject($type, $icon, $title, $system = false)
    {
        return (object)['type' => $type, 'icon' => 'zmdi zmdi-'.$icon, 'title' => $title, 'system' => $system];
    }

    public function getAppsList()
    {
        return [
            $this->createAppObject('single', 'file', JText::_('PAGES')),
            $this->createAppObject('blog', 'format-color-text', JText::_('BLOG')),
            $this->createAppObject('products', 'shopping-cart', JText::_('STORE')),
            $this->createAppObject('booking', 'calendar-check', JText::_('BOOKING')),
            $this->createAppObject('comments', 'comment-more', JText::_('COMMENTS'), true),
            $this->createAppObject('reviews', 'ticket-star', JText::_('REVIEWS'), true),
            $this->createAppObject('blank', 'crop-free', 'Zero App'),
            $this->createAppObject('portfolio', 'camera', 'Portfolio'),
            $this->createAppObject('hotel-rooms', 'hotel', 'Hotel Rooms'),
            $this->createAppObject('photo-editor', 'camera-alt', JText::_('PHOTO_EDITOR'), true),
            $this->createAppObject('code-editor', 'code-setting', JText::_('CODE_EDITOR'), true),
            $this->createAppObject('performance', 'time-restore-setting', JText::_('PERFORMANCE'), true),
            $this->createAppObject('preloader', 'spinner', JText::_('PRELOADER'), true),
            $this->createAppObject('canonical', 'link', JText::_('CANONICAL'), true),
            $this->createAppObject('sitemap', 'device-hub', 'XML '.JText::_('SITEMAP'), true)
        ];
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}