<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewtags extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $access = [];
    protected $languages = [];
    protected $apps;
    protected $folders;
    protected $folder;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $app = JFactory::getApplication();
        $layout = $app->input->get('layout', '');
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        if ($layout != 'apps' && $layout != 'modal') {
            $this->apps = gridboxHelper::getApps();
            $this->folders = $this->get('Folders');
            $this->folder = $app->input->get('folder', 1, 'int');
            $this->pagination = $this->get('Pagination');
            $this->addToolBar();
            $this->getAccess();
            $this->getLanguages();
            foreach ($this->items as &$item) {
                $item->order_up = true;
                $item->order_dn = true;
            }
        }
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        parent::display($tpl);
    }

    protected function getAccess()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__viewlevels')
            ->order($db->quoteName('ordering') . ' ASC')
            ->order($db->quoteName('title') . ' ASC');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        foreach ($array as $value) {
            $this->access[$value->id] = $value->title;
        }
    }

    protected function getLanguages()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('lang_code, title')
            ->from('#__languages')
            ->where('published >= 0')
            ->order('title');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $this->languages['*'] = JText::_('JALL');
        foreach ($items as $key => $value) {
            $this->languages[$value->lang_code] = $value->title;
        }
    }
    
    protected function addToolBar ()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.duplicate', 'com_gridbox')) {
            JToolBarHelper::custom('tags.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if ($user->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('tags.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('tags.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($user->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('tags.moveTo', 'chevron-right.png', 'chevron-right.png', 'MOVE_TO', true);
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('tags.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
    }
    
    protected function getSortFields()
    {
        return array(
            'published' => JText::_('JSTATUS'),
            'title' => JText::_('JGLOBAL_TITLE'),
            'order_list' => JText::_('CUSTOM'),
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