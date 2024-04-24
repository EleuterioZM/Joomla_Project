<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewPage extends JViewLegacy
{
    protected $item;
    protected $pageLayout;
    protected $canEdit;
    protected $schema;
    
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        if (empty($id)) {
            gridboxHelper::raiseError(404, JText::_('NOT_FOUND'));
        }
        $this->item = $this->get('Item');
        if (!empty($this->item) && $this->item->app_type == 'products') {
            $digital = gridboxHelper::getSubscriptionProducts();
        } else {
            $digital = [];
        }
        if (empty($this->item) || $this->item->page_category == 'trashed' || in_array($this->item->id, $digital)) {
            gridboxHelper::raiseError(404, JText::_('NOT_FOUND'));
        }
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        if (!in_array($this->item->page_access, $groups)) {
            gridboxHelper::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
        }
        $itemId = $input->get('Itemid');
        $menus = JFactory::getApplication()->getMenu('site');
        $attributes = array('link');
        $link = 'index.php?option=com_gridbox&view=page&id='.$id;
        $values = array($link);
        $menuItems = $menus->getItems($attributes, $values);
        $menuFlag = gridboxHelper::checkMenuItems($menuItems, $itemId);
        if (!empty($menuItems) && !empty($itemId) && $menuFlag) {
            $link = JRoute::_('index.php?Itemid='.$menuItems[0]->id);
            header('Location: '.$link);
            exit;
        }
        $this->get('Hits');
        $this->setBreadcrumb();
        $this->item->params = gridboxHelper::checkModules($this->item->params, $this->item->style);
        $this->prepareDocument();
        $pageAssets = new gridboxAssetsHelper($this->item->id, 'page');
        $editPage = $pageAssets->checkPermission('core.edit');
        if (!$editPage && !empty($this->item->page_category)) {
            $editPage = $pageAssets->checkEditOwn($this->item->page_category);
        }
        $this->canEdit = $editPage;
        parent::display($tpl);
    }

    public function setBreadcrumb()
    {
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $id = $this->item->page_category;
        if ($id > 0) {
            $itemId = null;
            $menus = JFactory::getApplication()->getMenu('site');
            $component = JComponentHelper::getComponent('com_gridbox');
            $attributes = array('component_id');
            $values = array($component->id);
            $items = $menus->getItems($attributes, $values);
            foreach ($items as $item) {
                if (isset($item->query) && isset($item->query['id']) && isset($item->query['view'])) {
                    if ($item->query['view'] == 'page' && $item->query['id'] == $this->item->id) {
                        $itemId .= $item->id;
                        break;
                    }
                }
            }
            if (!$itemId) {
                $array = gridboxHelper::getCategoryBreadcrumb($id);
                $path = array_reverse($array);
                $path[] = array('title' => $this->item->title, 'link' => '');
                foreach ($path as $key => $value) {
                    $pathway->addItem($value['title'], $value['link']);
                }
            }
        }
    }

    public function prepareDocument()
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/seo.php';
        $seo = new gridboxSeoHelper($this->item, 'page');
        $global = $seo->getGlobal();
        $doc = JFactory::getDocument();
        $time = $this->item->saved_time;
        if (!empty($time)) {
            $time = '?'.$time;
        }
        $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/style-'.$this->item->id.'.css'.$time);
        gridboxHelper::checkMoreScripts($this->item->params, $time);
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $menu = $menus->getActive();
        $meta_title = empty($this->item->meta_title) && !empty($global->meta_title) ? $global->meta_title : $this->item->meta_title;
        $title = $seo->prepareText($meta_title);
        if (empty($title)) {
            $title = $this->item->title;
        }
        $meta_desc = empty($this->item->meta_description) && !empty($global->meta_description) ? $global->meta_description : $this->item->meta_description;
        $desc = $seo->prepareText($meta_desc);
        $schema = !empty($this->item->schema_markup) ? $this->item->schema_markup : '';
        $schema = empty($schema) && isset($global->schema_markup) ? $global->schema_markup : $schema;
        $this->schema = $seo->prepareSchema($schema);
        $keywords = $this->item->meta_keywords;
        $robots = $this->item->robots;
        if (isset($menu) && $menu->query['view'] == 'page' && $menu->query['id'] == $this->item->id) {
            $params  = $menus->getParams($menu->id);
            $page_title = $params->get('page_title');
            $page_desc = $params->get('menu-meta_description');
            $page_key = $params->get('menu-meta_keywords');
            $page_robots = $params->get('robots');
        } else {
            $page_title = '';
            $page_desc = '';
            $page_key = '';
            $page_robots = '';
        }
        if (!empty($page_title)) {
            $title = $page_title;
        }
        if (!empty($page_desc)) {
            $desc = $page_desc;
        }
        if (!empty($page_key)) {
            $keywords = $page_key;
        }
        if (!empty($page_robots)) {
            $robots = $page_robots;
        }
        if ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } else if ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }
        $doc->setTitle($title);
        $doc->setDescription($desc);
        $doc->setMetaData('keywords', $keywords);
        if (empty($robots)) {
            $config = JFactory::getConfig();
            $robots = $config->get('robots');
        }
        if ($robots) {
            $doc->setMetadata('robots', $robots);
        }
        if (!empty($this->item->app_type) && $this->item->app_type != 'single') {
            $this->pageLayout = $this->get('PageLayout');
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/post-'.$this->item->app_id.'.css'.$time);
            $this->setLayout('blog');
            $pageItems = $this->get('pageItems');
            $this->pageLayout = gridboxHelper::checkModules($this->pageLayout, $pageItems);
            gridboxHelper::checkMoreScripts($this->pageLayout, $time);
        }
    }
}