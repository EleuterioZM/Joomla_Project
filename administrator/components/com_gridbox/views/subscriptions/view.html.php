<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewSubscriptions extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $promo;
    protected $item;
    
    public function display($tpl = null) 
    {
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'string');
        $this->about = gridboxHelper::aboutUs();
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->apps = gridboxHelper::getApps();
        $this->pagination = $this->get('Pagination');
        $this->promo = $this->get('Promo');
        $this->addToolBar();
        foreach ($this->items as &$item) {
            $item->order_up = true;
            $item->order_dn = true;
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
        if (JFactory::getUser()->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('subscriptions.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}