<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewSales extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $access;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $this->apps = gridboxHelper::getApps();
        $this->pagination = $this->get('Pagination');
        $this->access = $this->get('Access');
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
        if ($user->authorise('core.duplicate', 'com_gridbox')) {
            JToolBarHelper::custom('sales.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if ($user->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('sales.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('sales.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('sales.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}