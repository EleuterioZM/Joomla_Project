<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewShipping extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $carriers;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $this->apps = gridboxHelper::getApps();
        $this->pagination = $this->get('Pagination');
        $this->carriers = $this->get('Carriers');
        $this->addToolBar();
        foreach ($this->items as &$item) {
            $item->order_up = true;
            $item->order_dn = true;
        }    
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        parent::display($tpl);
    }

    protected function addToolBar ()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('shipping.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('shipping.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('shipping.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}