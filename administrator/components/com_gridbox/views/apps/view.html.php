<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewApps extends JViewLegacy
{
    protected $items;
    protected $blog;
    protected $pagination;
    protected $state;
    protected $about;
    protected $themes;
    protected $catList = [];
    protected $categories;
    protected $category;
    protected $access = [];
    protected $languages = [];
    protected $apps;
    protected $root = 'active';
    protected $authors;
    protected $fields;
    protected $tagsFolders;
    
    public function display($tpl = null) 
    {
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'string');
        $id = $input->get('id', 0, 'int');
        if ($layout == 'modal') {
            $this->setLayout('modal');
            $this->items = $this->get('Items');
            $this->state = $this->get('State');
        } else {
            $this->apps = gridboxHelper::getApps();
            foreach ($this->apps as $key => $app) {
                if ($app->id == $id) {
                    $this->blog = $app;
                    break;
                }
            }
            $this->tagsFolders = $this->get('TagsFolders');
            $this->category = $input->get('category', 0, 'int');
            $this->getCategories();
            $this->categoryList = $this->getCategoryList();
            $this->items = $this->get('Items');
            $this->authors = $this->get('Authors');
            $this->items = $this->getThemeName($this->items);
            $this->getCategoryName();
            $this->pagination = $this->get('Pagination');
            $this->state = $this->get('State');
            $this->themes = $this->get('Themes');
            $this->fields = $this->get('PageFields');
            $this->addToolBar();
            $this->getAccess();
            $this->getLanguages();
            foreach ($this->items as &$item) {
                $item->order_up = true;
                $item->order_dn = true;
            }
        }
        $this->about = gridboxHelper::aboutUs();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        parent::display($tpl);
    }

    public function drawCategoryList($items)
    {
        $str = '<ul>';
        $catUrl = 'index.php?option=com_gridbox&view=apps&id='.$this->blog->id.'&category=';
        $input = JFactory::getApplication()->input;
        foreach ($items as $key => $item) {
            $edit = gridboxHelper::assetsCheckPermission($item->id, 'category', 'core.edit');
            $str .= '<li class="ba-category '.$item->active;
            $cookie = $input->cookie->exists('blog'.$this->blog->id.'id'.$item->id);
            if ($cookie) {
                $str .= ' visible-branch';
            }
            if (!$item->published) {
                $str .= ' ba-unpublish';
            }
            $str .= '" data-id="'.$item->id;
            $str .= '"><a href="'.$catUrl.$item->id.'">';
            $str .= '<label><i class="zmdi zmdi-folder"></i></label><span>'.$item->title.'</span>';
            $str .= '<input type="hidden" value="'.htmlspecialchars(json_encode($item), ENT_QUOTES).'"></a>';
            if (count($item->child) > 0) {
                $str .= '<i class="zmdi zmdi-chevron-right ba-icon-md"></i>';
                $str .= $this->drawCategoryList($item->child);
            }
            $str .= '<span>';
            if ($edit) {
                $str .= '<i class="zmdi zmdi-settings open-category-settings ba-icon-md"></i>';
            }
            $str .= '<i class="zmdi zmdi-apps sorting-handle ba-icon-md"></i>';
            $str .= '</span>';
            $str .= '</li>';
        }
        $str .= '</ul>';

        return $str;
    }

    protected function getCategoryList($id = 0, $level = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title, id')
            ->from('#__gridbox_categories')
            ->where('app_id = '.$this->blog->id)
            ->where('parent = '.$id)
            ->order('order_list ASC');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $data = [];
        foreach ($categories as $category) {
            $category->level = $level;
            $data[] = $category;
            $sub = $this->getCategoryList($category->id, $level + 1);
            $data = array_merge($data, $sub);
        }

        return $data;
    }

    protected function getCategories()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_categories')
            ->where('`app_id` = '.$this->blog->id)
            ->where('`parent` = 0')
            ->order('order_list ASC');
        $db->setQuery($query);
        $this->categories = $db->loadObjectList();
        foreach ($this->categories as $value) {
            if ($value->id == $this->category) {
                $value->active = ' active';
                $this->root = '';
            } else {
                $value->active = '';
            }
            $value->child = $this->getAllChild($value);
        }
    }

    protected function getAllChild($parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_categories')
            ->where('`app_id` = '.$this->blog->id)
            ->where('`parent` = '.$parent->id)
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $visible = '';
        foreach ($items as $key => $value) {
            if ($value->id == $this->category) {
                $value->active = 'active';
                $this->root = '';
            } else {
                $value->active = '';
            }
            $value->child = $this->getAllChild($value);
        }

        return $items;
    }

    protected function getCategoryName()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, app_id')
            ->from('#__gridbox_categories');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        foreach ($array as $value) {
            $this->catList[$value->id] = $value->title;
        }
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
            $query = $db->getQuery(true);
            $query->select('`title`')
                ->from('#__gridbox_categories')
                ->where('`id` = '.$db->quote($item->page_category));
            $db->setQuery($query);
            $item->category = $db->loadResult();
            $item->app_type = $this->blog->type;
        }
        
        return $items;
    }
    
    protected function addToolBar()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.duplicate', 'com_gridbox')) {
            JToolBarHelper::custom('apps.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }
        if ($user->authorise('core.edit.state', 'com_gridbox')) {
            JToolbarHelper::publish('apps.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('apps.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($user->authorise('core.edit', 'com_gridbox.app.'.$this->blog->id)) {
            JToolBarHelper::custom('pages.settings', 'options.png', 'options.png', 'SETTINGS', true);
        }
        JToolBarHelper::custom('apps.export', 'download.png', 'download.png', 'EXPORT', false);
        if ($this->blog->type == 'products' && $user->authorise('core.csv', 'com_gridbox')) {
            JToolBarHelper::custom('apps.exportcsv', 'download.png', 'download.png', 'IMPORT_EXPORT_CSV', false);
        }
        if ($user->authorise('core.edit', 'com_gridbox')) {
            JToolBarHelper::custom('apps.moveTo', 'chevron-right.png', 'chevron-right.png', 'MOVE_TO', true);
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
            JToolBarHelper::custom('apps.addTrash', 'trash.png', 'trash.png', 'JTOOLBAR_TRASH', true);
        }
    }
    
    protected function getSortFields()
    {
        return array(
            'published' => JText::_('JSTATUS'),
            'title' => JText::_('JGLOBAL_TITLE'),
            'order_list' => JText::_('CUSTOM'),
            'page_category' => JText::_('CATEGORY'),
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