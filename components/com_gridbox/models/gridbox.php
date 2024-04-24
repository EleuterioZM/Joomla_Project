<?php
/**
* @package   Grifbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

class gridboxModelGridbox extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getAppLayout()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true)
            ->select('app_layout, type')
            ->from('`#__gridbox_app`')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('id = ' .$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->app_layout)) {
            $item->app_layout = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.html');
        }
        
        return $item->app_layout;
    }

    public function getAppItems()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true)
            ->select('app_items, type')
            ->from('`#__gridbox_app`')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('id = ' .$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->app_items)) {
            $item->app_items = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.json');
        }
        
        return $item->app_items;
    }

    public function getPageLayout()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('page_layout, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->page_layout)) {
            $item->page_layout = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.html');
        }
        
        return $item->page_layout;
    }

    public function getPageItems()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('page_items, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->page_items)) {
            $item->page_items = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.json');
        }
        
        return $item->page_items;
    }

    public function createSystemPage()
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $input = JFactory::getApplication()->input;
        $obj->title = $input->get('title', '', 'string');
        $obj->type = $input->get('page_type', '', 'string');
        $obj->theme = $input->get('page_theme', '', 'string');
        $obj->page_options = '{}';
        if ($obj->type != '404' && $obj->type != 'offline' && $obj->type != 'preloader') {
            $obj->alias = gridboxHelper::getAlias($obj->title, '#__gridbox_system_pages', 'alias');
        }
        if ($obj->type == '404') {
            $obj->page_options = '{"enable_header":false}';
        } else if ($obj->type == 'submission-form') {
            $obj->page_options = '{"premoderation":true,"author":true,"access":1,"emails":true,"submited_email":true,"published_email":true}';
        }
        $db->insertObject('#__gridbox_system_pages', $obj);

        return $db->insertid();
    }
    
    public function createPage()
    {
        $type = '';
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $app_id = $input->get('app_id', 0, 'int');
        $title = $input->get('title', '', 'string');
        $category = $input->get('category', '', 'string');
        $user = JFactory::getUser();
        $canCreate = $user->authorise('core.create', 'com_gridbox');
        if ($app_id != 0 && !empty($category)) {
            $categoryAssets = new gridboxAssetsHelper($category, 'category');
            $canCreate = $categoryAssets->checkPermission('core.create');
            $query = $db->getQuery(true)
                ->select('type')
                ->from('#__gridbox_app')
                ->where('type <> '.$db->quote('system_apps'))
                ->where('id = '.$app_id);
            $db->setQuery($query);
            $type = $db->loadResult();
        }
        if (!$canCreate) {
            return '';
        }
        $theme = $input->get('page_theme', 0, 'int');
        $table = $this->getTable();
        $title = strip_tags($title);
        $alias = $title;
        $alias = gridboxHelper::getAlias($alias, '#__gridbox_pages', 'page_alias');
        $nowDate = date("Y-m-d H:i:s");
        $count = '12';
        $span = explode('+', $count);
        $count = count($span);
        $obj = new stdClass();
        $obj->items = new stdClass();
        $now = strtotime(date('Y-m-d G:i:s')) * 10;
        if ($type == 'blog') {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/apps/blog/post.php';
        } else if (empty($type) || $type == 'single') {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/section.php';
        } else {
            $out = '';
        }
        $obj->html = $out;
        $array = ['title' => $title, 'page_alias' => $alias, 'page_category' => $category,
            'params' => $obj->html, 'style' => json_encode($obj->items),
            'app_id' => $app_id, 'theme' => $theme, 'created' => $nowDate];
        $table->bind($array);
        $table->store();
        if (!empty($type) && $type != 'single') {
            $user = JFactory::getUser();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_authors')
                ->where('user_id = '.$user->id);
            $db->setQuery($query);
            $author = $db->loadResult();
            if ($author && !empty($author)) {
                $object = new stdClass();
                $object->page_id = $table->id;
                $object->author_id = $author;
                $db->insertObject('#__gridbox_authors_map', $object);
            }
        }
        $this->createProductData($table->id, $type, $input);
        gridboxHelper::triggerEvent('onGidboxPageAfterSave', [$table->id], 'finder');

        return $table->id;
    }

    public function createProductData($id, $type, $input)
    {
        if ($type != 'products' && $type != 'booking') {
            return;
        }
        $db = $this->getDbo();
        $obj = new stdClass();
        $obj->product_id = $id;
        $obj->price = 0;
        $obj->variations = '{}';
        $obj->product_type = $input->get('product_type', '', 'string');
        if ($type == 'booking') {
            $obj->product_type = 'booking';
            $booking = gridboxHelper::getBooking();
            $settings = $booking->decodeSettingsFile('booking-product.json');
            $obj->booking = json_encode($settings);
        }
        $db->insertObject('#__gridbox_store_product_data', $obj);
    }
    
    public function getItem($id = null)
    {
        $input = JFactory::getApplication()->input;
        $db = $this->getDbo();
        $edit_type = $input->get('edit_type', '', 'string');
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true);
        if ($edit_type == 'blog' || $edit_type == 'post-layout') {
            $query->select('b.id, b.title, b.alias, b.theme, b.type, b.saved_time')
                ->from('`#__gridbox_app` AS b')
                ->where('b.type <> '.$db->quote('system_apps'))
                ->where('b.id = ' .$id)
                ->select('t.title as ThemeTitle')
                ->leftJoin('`#__template_styles` AS t'
                    . ' ON '
                    . $db->quoteName('b.theme')
                    . ' = ' 
                    . $db->quoteName('t.id')
                );
            if ($edit_type == 'post-layout') {
                $query->leftJoin('#__gridbox_pages AS p ON p.app_id = b.id')
                    ->select('p.theme as postTheme');
            }
        } else if (empty($edit_type)) {
            $query->select('b.*')
                ->from('`#__gridbox_pages` AS b')
                ->where('b.id = ' .$id)
                ->select('a.type as app_type, a.post_editor_wrapper')
                ->leftJoin('`#__gridbox_app` AS a'
                    . ' ON '
                    . $db->quoteName('b.app_id')
                    . ' = ' 
                    . $db->quoteName('a.id')
                )
                ->select('c.title AS category_title')
                ->leftJoin('`#__gridbox_categories` AS c'
                    . ' ON '
                    . $db->quoteName('b.page_category')
                    . ' = ' 
                    . $db->quoteName('c.id')
                );
        } else if ($edit_type == 'system') {
            $query->select('*')
                ->from('#__gridbox_system_pages')
                ->where('id = '.$id);
        }
        $db->setQuery($query);
        $item = $db->loadObject();
        if (isset($item->app_type) && $item->app_type != 'single') {
            $query = $db->getQuery(true)
                ->select('au.title, au.avatar, au.id')
                ->from('`#__gridbox_authors_map` AS au_m')
                ->where('au_m.page_id = '.$id)
                ->leftJoin('`#__gridbox_authors` AS au ON au.id = au_m.author_id')
                ->where('au.published = 1')
                ->order('au_m.id ASC');
            $db->setQuery($query);
            $item->authors = $db->loadObjectList();
        }
        
        return $item;
    }

    public function getSystemLayout()
    {
        $input = JFactory::getApplication()->input;
        $db = $this->getDbo();
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_system_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();
        $item = new stdClass();
        $item->html = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$type.'.html');
        $item->items = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$type.'.json');
        
        return $item;
    }
    
    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }
}
