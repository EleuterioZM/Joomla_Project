<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewOrders extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $statuses;
    protected $info;
    protected $shipping;
    protected $promo;
    protected $item;
    protected $sales;
    
    public function display($tpl = null) 
    {
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'string');
        $this->about = gridboxHelper::aboutUs();
        if ($layout == 'print' || $layout == 'pdf') {
            $this->item = $this->get('Item');
        } else {
            $this->items = $this->get('Items');
            $this->statuses = $this->get('Statuses');
            $this->state = $this->get('State');
            $this->apps = gridboxHelper::getApps();
            $this->pagination = $this->get('Pagination');
            $this->addToolBar();
            $this->info = $this->get('CustomerInfo');
            $this->shipping = $this->get('Shipping');
            $this->promo = $this->get('Promo');
            $this->sales = $this->get('Sales');
            foreach ($this->items as &$item) {
                $item->order_up = true;
                $item->order_dn = true;
            }
        }
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }

        parent::display($tpl);
    }
    
    protected function addToolBar()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('orders.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
        if ($user->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('orders.exportcsv', 'download.png', 'download.png', 'IMPORT_EXPORT_CSV', false);
        }
    }
    
    protected function getSortFields()
    {
        return array(
            'published' => JText::_('JSTATUS'),
            'title' => JText::_('JGLOBAL_TITLE'),
            'order_list' => JText::_('CUSTOM'),
            'theme' => JText::_('THEME'),
            'created' => JText::_('DATE'),
            'hits' => JText::_('VIEWS'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}