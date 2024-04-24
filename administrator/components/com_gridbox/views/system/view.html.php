<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewSystem extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $themes;
    protected $apps;
    protected $languages = [];
    protected $access = [];
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $app = JFactory::getApplication();
        $this->about = gridboxHelper::aboutUs();
        $this->apps = gridboxHelper::getApps();
        $this->items = $this->getThemeName($this->items);
        $this->pagination = $this->get('Pagination');
        $this->themes = $this->get('Themes');
        $this->getLanguages();
        $this->addToolBar();
        $this->getAccess();
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

    protected function addToolBar()
    {
        
        if (JFactory::getUser()->authorise('core.duplicate', 'com_gridbox')) {
            JToolBarHelper::custom('system.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if (JFactory::getUser()->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('system.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('system.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        /*
        if (JFactory::getUser()->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('system.delete', 'delete.png', 'delete.png', 'DELETE', true);
        }
        */
        if (JFactory::getUser()->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('system.addTrash', 'trash.png', 'trash.png', 'JTOOLBAR_TRASH', true);
        }
        JToolBarHelper::custom('system.settings', 'options.png', 'options.png', 'SETTINGS', true);
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

    protected function getThemeName($items)
    {
        $db = JFactory::getDbo();
        foreach ($items as $item) {
            $query = $db->getQuery(true);
            $query->select('`title`')
                ->from('#__template_styles')
                ->where('`id` = '.$db->quote($item->theme));
            $db->setQuery($query);
            $item->themeName = $db->loadResult();
        }
        
        return $items;
    }
    
    protected function getSortFields()
    {
        return array(
            'title' => JText::_('JGLOBAL_TITLE'),
            'order_list' => JText::_('CUSTOM'),
            'theme' => JText::_('THEME'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}