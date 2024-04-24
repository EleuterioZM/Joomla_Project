<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewsingle extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $single;
    protected $state;
    protected $about;
    protected $themes;
    protected $access = array();
    protected $languages = array();
    protected $apps;
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->apps = gridboxHelper::getApps();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        foreach ($this->apps as $key => $app) {
            if ($app->id == $id) {
                $this->single = $app;
                break;
            }
        }
        $this->items = $this->getThemeName($this->items);
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->themes = $this->get('Themes');
        $this->about = gridboxHelper::aboutUs();
        $this->addToolBar();
        $this->getAccess();
        $this->getLanguages();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        foreach ($this->items as &$item) {
            $item->order_up = true;
            $item->order_dn = true;
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
            $item->app_type = $this->single->type;
        }
        
        return $items;
    }
    
    protected function addToolBar ()
    {
        if (JFactory::getUser()->authorise('core.duplicate', 'com_gridbox')) {
            JToolBarHelper::custom('pages.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if (JFactory::getUser()->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('pages.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('pages.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if (JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('pages.settings', 'options.png', 'options.png', 'SETTINGS', true);
        }
        JToolBarHelper::custom('pages.export', 'download.png', 'download.png', 'EXPORT', true);
        if (JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('apps.moveTo', 'chevron-right.png', 'chevron-right.png', 'MOVE_TO', true);
        }
        if (JFactory::getUser()->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('pages.addTrash', 'trash.png', 'trash.png', 'JTOOLBAR_TRASH', true);
            JToolBarHelper::custom('apps.deleteApp', 'delete.png', 'delete.png', 'DELETE_APP', false);
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