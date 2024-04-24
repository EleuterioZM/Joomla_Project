<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewTrashed extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $about;
    protected $apps;
    protected $themes;
    protected $languages = array();
    
    public function display($tpl = null) 
    {
        $this->items = $this->get('Items');
        $this->apps = gridboxHelper::getApps();
        $this->items = $this->getThemeName($this->items);
        $this->pagination = $this->get('Pagination');        
        $this->state = $this->get('State');
        $this->about = gridboxHelper::aboutUs();
        $this->themes = $this->get('Themes');
        $this->getLanguages();
        $this->addToolBar();
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
            $query->select('title')
                ->from('#__template_styles')
                ->where('id = '.$db->quote($item->theme));
            $db->setQuery($query);
            $item->themeName = $db->loadResult();
            if ($item->hits == -1) {
                $item->app_name = '';
                $item->app_type = 'system';
            } else if ($item->app_id == 0) {
                $item->app_name = JText::_('PAGES');
                $item->app_type = 'single';
            } else {
                $query = $db->getQuery(true);
                $query->select('title, type')
                    ->from('#__gridbox_app')
                    ->where('id = '.$db->quote($item->app_id));
                $db->setQuery($query);
                $result = $db->loadObject();
                $item->app_type = $result->type;
                $item->app_name = $result->title;
            }
        }
        
        return $items;
    }
    
    protected function addToolBar ()
    {
        if (JFactory::getUser()->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::deleteList('', 'trashed.delete');
        }
    }
    
    protected function getSortFields()
    {
        return array(
            'title' => JText::_('JGLOBAL_TITLE'),
            'theme' => JText::_('THEME'),
            'app_id' => JText::_('APP'),
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