<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewReviews extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $form;
    protected $users;
    protected $reviewsBanList;
    protected $userGroups;
    protected $integrations;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->users = $this->get('Users');
        $this->integrations = $this->get('Integrations');
        $this->userGroups = $this->get('UserGroups');
        $this->reviewsBanList = $this->get('BannedReviewsLists');
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $this->apps = gridboxHelper::getApps();
        $this->pagination = $this->get('Pagination');
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
            JToolbarHelper::custom('reviews.approve', 'publish.png', 'publish.png', 'APPROVE', true);
            JToolbarHelper::custom('reviews.spam', 'minus.png', 'minus.png', 'SPAM', true);
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('reviews.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
        if ($user->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('reviews.readAll', 'eye.png', 'eye.png', 'MARK_ALL_AS_READ', false);
            JToolBarHelper::custom('reviews.unread', 'eye-slash.png', 'eye-slash.png', 'MARK_AS_UNREAD', true);
        }
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}