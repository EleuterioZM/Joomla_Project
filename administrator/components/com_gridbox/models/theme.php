<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die; 

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

class gridboxModelTheme extends JModelAdmin
{
    public function getTable($type = 'Style', $prefix = 'TemplatesTable', $config = [])
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function installGridboxApp($type)
    {
        $codeEditor = gridboxHelper::checkSystemApp($type);
        if (!$codeEditor) {
            $db = JFactory::getDbo();
            $obj = new stdClass();
            $obj->title = $type;
            $obj->type = 'system_apps';
            $db->insertObject('#__gridbox_app', $obj);
        }
    }

    public function prepareFormsData($db, $table, $data)
    {
        $query = 'DESCRIBE '.$db->quoteName($table);
        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $obj) {
            if ($obj->Field == 'id') {
                continue;
            }
            if (!isset($data->{$obj->Field}) && is_null($obj->Default)) {
                $data->{$obj->Field} = '';
            }
        }
    }

    public function addNewTheme($xml, $source = false)
    {
        $themes = [];
        $main_menu = [];
        $modules_menu = [];
        $deeper = [];
        $pages = [];
        $forms = [];
        $appsList = array(0 => 0);
        $catsList = array(0 => 0, 'trashed' => 'trashed');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(extension_id)')
            ->from("#__extensions")
            ->where('element = '.$db->quote('com_baforms'));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result > 0 && isset($xml->com_baforms)) {
            foreach ($xml->com_baforms->baform as $key => $baform) {
                $form = json_decode($baform->forms);
                if (empty($form)) {
                   continue;
                }
                $items = json_decode($baform->items);
                $columns = json_decode($baform->columns);
                $fId = $form->id;
                $form->id = 0;
                $this->prepareFormsData($db, '#__baforms_forms', $form);
                $db->insertObject('#__baforms_forms', $form);
                $forms[$fId] = $db->insertid();
                foreach ($items as $item) {
                    $item->id = 0;
                    $item->form_id = $forms[$fId];
                    $this->prepareFormsData($db, '#__baforms_items', $item);
                    $db->insertObject('#__baforms_items', $item);
                }
                foreach ($columns as $column) {
                    $column->id = 0;
                    $column->form_id = $forms[$fId];
                    $this->prepareFormsData($db, '#__baforms_columns', $column);
                    $db->insertObject('#__baforms_columns', $column);
                }
                if (isset($baform->settings)) {
                    $settings = json_decode($baform->settings);
                    foreach ($settings as $setting) {
                        $setting->id = 0;
                        $setting->form_id = $forms[$fId];
                        $this->prepareFormsData($db, '#__baforms_forms_settings', $setting);
                        $db->insertObject('#__baforms_forms_settings', $setting);
                    }
                    $formsPages = json_decode($baform->pages);
                    foreach ($formsPages as $page) {
                        $page->id = 0;
                        $page->form_id = $forms[$fId];
                        $db->insertObject('#__baforms_pages', $page);
                    }
                }
            }
        }
        foreach ($xml->mainmenu->main_menu as $mainmenu) {
            $module = json_decode($mainmenu->module);
            $module_menu = json_decode($mainmenu->module_menu);
            $asset = json_decode($mainmenu->asset);
            $menu = json_decode($mainmenu->menu);
            $menu->menutype = $this->getNewMenuType($menu->menutype, '');
            $menu->id = 0;
            $menu_items = json_decode($mainmenu->menu_items);
            $modules_menu[$module->id] = '';
            $query = $db->getQuery(true)
                ->select("extension_id")
                ->from("#__extensions");
            $query->where("type=".$db->quote('component'))
                ->where('element='.$db->quote('com_gridbox'));
            $db->setQuery($query);
            $com_id = $db->loadResult();
            $query = $db->getQuery(true);
            $query->select('id')
                ->from('#__assets')
                ->where('`name` = '.$db->quote('com_modules'));
            $db->setQuery($query);
            if (!$asset) {
                $asset = new stdClass();
            }
            $asset->parent_id = $db->loadResult();
            $old_id = $module->id;
            $module->id = 0;
            $module->params = json_decode($module->params);
            $module->params->menutype = $menu->menutype;
            $module->params = json_encode($module->params);
            $table = JTable::getInstance('Module', 'JTable', []);
            $data = [];
            foreach ($module as $key => $value) {
                if ($key != 'asset_id') {
                    $data[$key] = $value;
                }
            }
            JPluginHelper::importPlugin($this->events_map['save']);
            $table->bind($data);
            $table->check();
            $dispatcher = JFactory::getApplication();
            $dispatcher->triggerEvent('onExtensionBeforeSave', array('com_modules.module', &$table, true));
            $table->store();
            $dispatcher->triggerEvent('onExtensionAfterSave', array('com_modules.module', &$table, true));
            $mod_id = $table->id;
            $modules_menu[$old_id] = $mod_id;
            $module_menu->moduleid = $mod_id;
            $query = $db->getQuery(true);
            $query->select('COUNT(moduleid)')
                ->where('`moduleid` = '.$mod_id)
                ->from('`#__modules_menu`');
            $db->setQuery($query);
            $c = $db->loadResult();
            if (empty($c)) {
                $db->insertObject('#__modules_menu', $module_menu);
            }
            $db->insertObject('#__menu_types', $menu);
            foreach ($menu_items as $item) {
                if ($item->published == 1 || $item->published == 0) {
                    $id = $item->id;
                    unset($item->id);
                    $item->menutype = $menu->menutype;
                    $item->component_id = $com_id;
                    $item->template_style_id = 0;
                    $item->home = 0;
                    $item->alias = $this->getNewMenuAlias($item->alias, '');
                    $item->component_id = $com_id;
                    $item->template_style_id = 0;
                    $item->home = 0;
                    $db->insertObject('#__menu', $item);
                    $item->id = $db->insertid();
                    $main_menu[$id] = $item;
                    if ($item->parent_id > 1) {
                        $deeper[$item->id] = $item->parent_id;
                    }
                }                
            }
            foreach ($deeper as $key => $deep) {
                $obj = new stdClass();
                $obj->id = $key;
                $obj->parent_id = $main_menu[$deep]->id;
                $db->updateObject('#__menu', $obj, 'id');
            }
        }
        foreach ($xml->themes->theme as $theme) {
            $table = $this->getTable();
            $params = json_decode((string)$theme->params);
            $obj = $this->checkMainMenu($params->header->html, $params->header->items, $modules_menu, $main_menu);
            $obj = $this->checkBaforms($obj->html, $obj->items, $forms);
            $params->header->html = $obj->html;
            $params->header->items = $obj->items;
            $obj = $this->checkMainMenu($params->footer->html, $params->footer->items, $modules_menu, $main_menu);
            $obj = $this->checkBaforms($obj->html, $obj->items, $forms);
            $params->footer->html = $obj->html;
            $params->footer->items = $obj->items;
            $theme->params = json_encode($params);
            $table->bind(array('title' => (string)$theme->title, 'params' => (string)$theme->params,
                'home' => 0, 'client_id' => 0, 'template' => 'gridbox'));
            $table->store();
            $themes[(string)$theme->id] = $table->id;
            gridboxHelper::saveCodeEditor($theme, $table->id);
        }
        if (isset($xml->apps)) {
            foreach ($xml->apps->app as $app) {
                $obj = json_decode($app);
                $id = $obj->id;
                $obj->theme = $themes[$obj->theme];
                $obj->alias = gridboxHelper::getAlias($obj->alias, '#__gridbox_app');
                unset($obj->id);
                if (isset($obj->fields)) {
                    unset($obj->fields);
                }
                $db->insertObject('#__gridbox_app', $obj);
                $appsList[$id] = $db->insertid();
            }
        }
        if (isset($xml->categories)) {
            $catsChild = [];
            foreach ($xml->categories->category as $category) {
                $obj = json_decode($category);
                $id = $obj->id;
                $obj->app_id = $appsList[$obj->app_id];
                $obj->alias = gridboxHelper::getAlias($obj->alias, '#__gridbox_categories');
                unset($obj->id);
                $db->insertObject('#__gridbox_categories', $obj);
                $catsList[$id] = $db->insertid();
                if ($obj->parent != 0) {
                    $object = new stdClass();
                    $object->id = $catsList[$id];
                    $object->parent = $obj->parent;
                    $catsChild[] = $object;
                }
            }
            foreach ($catsChild as $child) {
                $child->parent = $catsList[$child->parent];
                $db->updateObject('#__gridbox_categories', $child, 'id');
            }
        }

        $productFields = new stdClass();
        if (isset($xml->products_fields)) {
            foreach ($xml->products_fields->field as $field) {
                $obj = json_decode($field);
                $field_id = $obj->id;
                $options = json_decode($obj->options);
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_products_fields')
                    ->where('field_key = '.$db->quote($obj->field_key));
                $db->setQuery($query);
                $object = $db->loadObject();
                $opts = new stdClass();
                if ($object) {
                    $obj->id = $object->id;
                    $array = json_decode($object->options);
                    foreach ($array as $option) {
                        $opts->{$option->key} = $option;
                    }
                } else {
                    $obj->id = 0;
                    $db->insertObject('#__gridbox_store_products_fields', $obj);
                    $obj->id = $db->insertid();
                }
                $productFields->{$field_id} = $obj->id;
                foreach ($options as $option) {
                    if (isset($opts->{$option->key})) {
                        continue;
                    }
                    $fData = new stdClass();
                    $fData->field_id = $obj->id;
                    $fData->option_key = $option->key;
                    $fData->value = $option->title;
                    $fData->color = $option->color;
                    $fData->image = $option->image;
                    $db->insertObject('#__gridbox_store_products_fields_data', $fData);
                }
            }
        }
        foreach ($xml->pages->page as $page) {
            $obj = json_decode($page);
            $obj->style = json_decode($obj->style);            
            $params = $this->checkMainMenu($obj->params, $obj->style, $modules_menu, $main_menu);
            $params = $this->checkBaforms($params->html, $params->items, $forms);
            $params = gridboxHelper::importBlogContent($params, $appsList, $catsList);
            $obj->params = $params->html;
            $obj->style = $params->items;
            $obj->page_alias = gridboxHelper::getNewPageAlias($obj->page_alias, '');
            $id = $obj->id;
            $obj->theme = $themes[$obj->theme];
            $obj->style = json_encode($obj->style);
            $obj->app_id = $appsList[$obj->app_id];
            $obj->order_list = 0;
            $obj->root_order_list = 0;
            if (!empty($obj->page_category)) {
                $obj->page_category = $catsList[$obj->page_category];
            }
            unset($obj->id);
            if (isset($obj->fields_data)) {
                unset($obj->fields_data);
            }
            $category_map = [];
            if (isset($obj->category_map)) {
                $category_map = is_string($obj->category_map) ? json_encode($obj->category_map) : $obj->category_map;
                unset($obj->category_map);
            }
            $db->insertObject('#__gridbox_pages', $obj);
            $pages[$id] = $db->insertid();
            foreach ($category_map as $object) {
                unset($object->id);
                $object->page_id = $pages[$id];
                $object->category_id = $catsList[$object->category_id];
                $db->insertObject('#__gridbox_category_page_map', $object);
            }
        }
        if (isset($xml->products_data)) {
            foreach ($xml->products_data->product_data as $data) {
                $obj = json_decode($data);
                $obj->id = 0;
                $obj->product_id = $pages[$obj->product_id];
                if (!empty($obj->extra_options)) {
                    $extra_options = json_decode($obj->extra_options);
                    foreach ($extra_options as $ind => $extra_option) {
                        if (isset($productFields->{$extra_option->id})) {
                            $extra_option->id = $productFields->{$extra_option->id};
                        } else {
                            unset($extra_options->{$ind});
                        }
                    }
                    $obj->extra_options = json_encode($extra_options);
                }
                if ($obj->product_type == 'subscription') {
                    $subscription = json_decode($obj->subscription);
                    $products = [];
                    foreach ($subscription->products as $product) {
                        $products[] = $pages[intval($product)];
                    }
                    $subscription->products = $products;
                    $products = [];
                    foreach ($subscription->upgrade as $product) {
                        $products[] = $pages[intval($product)];
                    }
                    $subscription->upgrade = $products;
                    $obj->subscription = json_encode($subscription);
                }
                $db->insertObject('#__gridbox_store_product_data', $obj);
            }
        }
        if (isset($xml->product_variations)) {
            foreach ($xml->product_variations->variation as $variation) {
                $obj = json_decode($variation);
                $obj->id = 0;
                $obj->product_id = $pages[$obj->product_id];
                $obj->field_id = $productFields->{$obj->field_id};
                $db->insertObject('#__gridbox_store_product_variations_map', $obj);
            }
        }
        if (isset($xml->tags)) {
            $tagsList = [];
            foreach ($xml->tags->tag as $tag) {
                $obj = json_decode($tag);
                $id = $obj->id;
                $object = new stdClass();
                $object->tag_id = $obj->tag_id;
                $object->page_id = $pages[$obj->page_id];
                unset($obj->tag_id);
                unset($obj->page_id);
                unset($obj->id);
                if (!isset($tagsList[$id])) {
                    $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__gridbox_tags')
                        ->where('title = '.$db->quote($obj->title));
                    $db->setQuery($query);
                    $tagId = $db->loadResult();
                    if (empty($tagId)) {
                        $db->insertObject('#__gridbox_tags', $obj);
                        $tagsList[$id] = $db->insertid();
                    } else {
                        $tagsList[$id] = $tagId;
                    }
                }
                $object->tag_id = $tagsList[$object->tag_id];
                $db->insertObject('#__gridbox_tags_map', $object);
            }
        }
        foreach ($appsList as $value) {
            if (empty($value)) {
                continue;
            }
            $query = $db->getQuery(true)
                ->select('id, page_layout, page_items, app_layout, app_items')
                ->from('#__gridbox_app')
                ->where('id = '.$value);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $object = new stdClass();
            $object->html = $obj->page_layout;
            $object->items = json_decode($obj->page_items);
            $object = $this->checkMainMenu($object->html, $object->items, $modules_menu, $main_menu);
            $object = $this->checkBaforms($object->html, $object->items, $forms);
            $object = gridboxHelper::importBlogContent($object, $appsList, $catsList);
            $object = gridboxHelper::findGridboxLinks($object->html, $object->items, $appsList, $catsList, $pages);
            $obj->page_layout = $object->html;
            $obj->page_items = json_encode($object->items);
            $object = new stdClass();
            $object->html = $obj->app_layout;
            $object->items = json_decode($obj->app_items);
            $object = $this->checkMainMenu($object->html, $object->items, $modules_menu, $main_menu);
            $object = $this->checkBaforms($object->html, $object->items, $forms);
            $object = gridboxHelper::importBlogContent($object, $appsList, $catsList);
            $object = gridboxHelper::findGridboxLinks($object->html, $object->items, $appsList, $catsList, $pages);
            $obj->app_layout = $object->html;
            $obj->app_items = json_encode($object->items);
            $db->updateObject('#__gridbox_app', $obj, 'id');
        }
        foreach ($themes as $value) {
            $query = $db->getQuery(true)
                ->select('id, params')
                ->from('#__template_styles')
                ->where('id = '.$value);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $params = json_decode($obj->params);
            $object = new stdClass();
            $object->html = $params->header->html;
            $object->items = $params->header->items;
            $object = gridboxHelper::importBlogContent($object, $appsList, $catsList);
            $object = gridboxHelper::findGridboxLinks($object->html, $object->items, $appsList, $catsList, $pages);
            $params->header->html = $object->html;
            $params->header->items = $object->items;
            $object->html = $params->footer->html;
            $object->items = $params->footer->items;
            $object = gridboxHelper::importBlogContent($object, $appsList, $catsList);
            $object = gridboxHelper::findGridboxLinks($object->html, $object->items, $appsList, $catsList, $pages);
            $params->footer->html = $object->html;
            $params->footer->items = $object->items;
            $obj->params = json_encode($params);
            $db->updateObject('#__template_styles', $obj, 'id');
        }
        foreach ($pages as $value) {
            if (empty($value)) {
                continue;
            }
            $query = $db->getQuery(true)
                ->select('params, style, id')
                ->from('#__gridbox_pages')
                ->where('id = '.$value);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $items = json_decode($obj->style);
            $object = gridboxHelper::findGridboxLinks($obj->params, $items, $appsList, $catsList, $pages);
            $obj->params = $object->html;
            $obj->style = json_encode($object->items);
            $db->updateObject('#__gridbox_pages', $obj, 'id');
        }
        if (isset($xml->fields)) {
            $fieldsList = [];
            foreach ($xml->fields->field as $field) {
                $obj = json_decode($field);
                $id = $obj->id;
                unset($obj->id);
                $obj->app_id = $appsList[$obj->app_id];
                $db->insertObject('#__gridbox_fields', $obj);
                $fieldsList[$id] = $db->insertid();
            }
            foreach ($xml->fields_data->field_data as $field) {
                $obj = json_decode($field);
                unset($obj->id);
                $obj->field_id = $fieldsList[$obj->field_id];
                $db->insertObject('#__gridbox_fields_data', $obj);
            }
            foreach ($xml->page_fields->page_field as $field) {
                $obj = json_decode($field);
                unset($obj->id);
                if ($obj->field_type == 'url') {
                    $value = json_decode($obj->value);
                    $value->link = gridboxHelper::importGridboxLinks($value->link, $appsList, $catsList, $pages);
                    $obj->value = json_encode($value);
                }
                $obj->field_id = $fieldsList[$obj->field_id];
                $obj->page_id = $pages[$obj->page_id];
                $db->insertObject('#__gridbox_page_fields', $obj);
            }
            foreach ($xml->fields_files->field_files as $field) {
                $obj = json_decode($field);
                unset($obj->id);
                $obj->app_id = $appsList[$obj->app_id];
                $obj->page_id = $pages[$obj->page_id];
                $db->insertObject('#__gridbox_fields_desktop_files', $obj);
            }
        }
        foreach ($xml->libraries->library as $lib) {
            $obj = json_decode($lib);
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_library')
                ->where('`global_item` = '.$db->quote($obj->global_item));
            $db->setQuery($query);
            $result = $db->loadResult();
            if (empty($result)) {
                unset($obj->id);
                $obj->item = json_decode($obj->item);
                $obj->item = $this->checkMainMenu($obj->item->html, $obj->item->items, $modules_menu, $main_menu);
                $obj->item = $this->checkBaforms($obj->item->html, $obj->item->items, $forms);
                $obj->item = json_encode($obj->item);
                $db->insertObject('#__gridbox_library', $obj);
            }
        }
        JFile::delete(JPATH_ROOT.'/templates/gridbox/css/storage/global-library.css');
        foreach ($main_menu as $key => $item) {
            $link = [];
            $url = substr($item->link, strpos($item->link, '?option') + 1);
            parse_str($url, $link);
            if ($link['view'] == 'page') {
                if (isset($pages[$link['id']])) {
                    $link['id'] = $pages[$link['id']];
                } else {
                    $link['id'] = end($pages);
                }
            } else {
                if (isset($catsList[$link['id']]) && isset($appsList[$link['app']])) {
                    $link['id'] = $catsList[$link['id']];
                    $link['app'] = $appsList[$link['app']];
                } else {
                    $link['id'] = 0;
                    $link['app'] = end($appsList);
                }
            }
            $array = [];
            foreach ($link as $key => $value) {
                $array[] = $key.'='.$value;
            }
            $url = implode('&', $array);
            $query = $db->getQuery(true)
                ->update('#__menu')
                ->set('link = '.$db->quote('index.php?'.$url))
                ->where('id = '.$item->id);
            $db->setQuery($query)
                ->execute();
        }
        $this->rebuild();
        if ($source && gridboxHelper::$installComments) {
            $this->installGridboxApp('comments');
        }
        if ($source && gridboxHelper::$installReviews) {
            $this->installGridboxApp('reviews');
        }
    }

    public function rebuild()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $table = $this->getTable($type = 'Menu', $prefix = 'gridboxTable', []);
        try {
            $rebuildResult = $table->rebuild();
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }
        if (!$rebuildResult) {
            $this->setError($table->getError());
            return false;
        }
        $query->select('id, params')
            ->from('#__menu')
            ->where('params NOT LIKE ' . $db->quote('{%'))
            ->where('params <> ' . $db->quote(''));
        $db->setQuery($query);
        try {
            $items = $db->loadObjectList();
        } catch (RuntimeException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
        foreach ($items as &$item) {
            $registry = new Registry;
            $registry->loadString($item->params);
            $params = (string) $registry;
            $query->clear();
            $query->update('#__menu')
                ->set('params = ' . $db->quote($params))
                ->where('id = ' . $item->id);
            try {
                $db->setQuery($query)->execute();
            } catch (RuntimeException $e) {
                throw new \Exception($e->getMessage(), 500);
            }
            unset($registry);
        }
        $this->cleanCache();

        return true;
    }

    public function checkMainMenu($html, $items, $menu, $main_menu)
    {
        $obj = new stdClass();
        $obj->html = $html;
        $obj->items = $items;
        $regex = '/\[main_menu=+(.*?)\]/i';
        preg_match_all($regex, $obj->html, $matches, PREG_SET_ORDER);
        if (!empty($menu)) {
            foreach ($matches as $index => $match) {
                if (isset($menu[$match[1]])) {
                    $obj->html = str_replace("[main_menu=".$match[1]."]", "[main_menu=".$menu[$match[1]]."]", $obj->html);
                }
            }
            foreach ($obj->items as $key => $value) {
                if ($value->type == 'menu') {
                    if (isset($menu[$value->integration])) {
                        $value->integration = $menu[$value->integration];
                        if (isset($value->items)) {
                            $object = new stdClass();
                            foreach ($value->items as $ind => $item) {
                                if (isset($main_menu[$ind])) {
                                    $object->{$main_menu[$ind]->id} = $item;
                                }
                            }
                            $value->items = $object;
                        }
                    }
                }
            }
        }
        $pos = 0;
        while ($pos = strpos($obj->html, 'data-megamenu=', $pos)) {
            preg_match('/"item-\d+/', $obj->html, $match, PREG_OFFSET_CAPTURE, $pos);
            if ($match[0] && $match[0][1] - $pos < 20) {
                $pos2 = strlen($match[0][0]) + ($match[0][1] - $pos);
                $substr = substr($obj->html, $pos, $pos2);
                $replace = preg_replace_callback('/\d+/', function($matchData) use ($main_menu){
                    return isset($main_menu[$matchData[0]]) ? $main_menu[$matchData[0]]->id : $matchData[0];
                }, $substr);
                if ($substr != $replace) {
                    $obj->html = str_replace($substr, $replace, $obj->html);
                }
            }
            $pos += 20;
        }

        return $obj;
    }

    public function checkBaforms($html, $items, $forms)
    {
        $obj = new stdClass();
        $obj->html = $html;
        $obj->items = $items;
        $regex = '/\[forms ID=+(.*?)\]/i';
        preg_match_all($regex, $obj->html, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $index => $match) {
                if (isset($forms[$match[1]])) {
                    $obj->html = str_replace('[forms ID='.$match[1].']', '[forms ID='.$forms[$match[1]].']', $obj->html);
                }
            }
            foreach ($obj->items as $key => $value) {
                if ($value->type == 'forms') {
                    if (isset($forms[$match[1]])) {
                        $value->integration = $forms[$value->integration];
                    }
                }
            }
        }

        return $obj;
    }

    public function getNewMenuType($type, $orig)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(id)')
            ->from('#__menu_types')
            ->where('`menutype` = '.$db->quote($type));
        $db->setQuery($query);
        $n = $db->loadResult();
        if (!empty($n)) {
            if (empty($orig)) {
                $type = gridboxHelper::increment($type);
            } else {
                $type = gridboxHelper::increment($orig);
            }
            $orig = $type;
            $type = gridboxHelper::stringURLSafe($type);
            if (empty($type)) {
                $type = $orig;
                $type = gridboxHelper::replace($type);
                $type = JFilterOutput::stringURLSafe($type);
            }
            if (empty($type)) {
                $type = date('Y-m-d-H-i-s');
            }
            $type = $this->getNewMenuType($type, $orig);
        }

        return $type;
    }

    public function getNewMenuAlias($type, $orig)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(id)')
            ->from('#__menu')
            ->where('`alias` = '.$db->quote($type));
        $db->setQuery($query);
        $n = $db->loadResult();
        if (!empty($n)) {
            if (empty($orig)) {
                $type = gridboxHelper::increment($type);
            } else {
                $type = gridboxHelper::increment($orig);
            }
            $orig = $type;
            $type = gridboxHelper::stringURLSafe($type);
            if (empty($type)) {
                $type = $orig;
                $type = gridboxHelper::replace($type);
                $type = JFilterOutput::stringURLSafe($type);
            }
            if (empty($type)) {
                $type = date('Y-m-d-H-i-s');
            }
            $type = $this->getNewMenuAlias($type, $orig);
        }
        return $type;
    }

    public function checkItems($cid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__template_styles')
            ->where('`home` = 1')
            ->where('`client_id` = 0')
            ->where('`template` = '.$db->quote('gridbox'));
        $db->setQuery($query);
        $default= $db->loadResult();
        foreach ($cid as $id) {
            $query = $db->getQuery(true);
            $query->update('#__gridbox_app')
                ->set('`theme` = '.$default)
                ->where('`theme` = '.$id);
            $db->setQuery($query);
            $db->execute();
            $query = $db->getQuery(true);
            $query->update('#__gridbox_pages')
                ->set('`theme` = '.$default)
                ->where('`theme` = '.$id);
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function updateParams()
    {
        $input = JFactory::getApplication()->input;
        $table = $this->getTable();
        $old = $input->get('old_default', 0, 'int');
        $default = $input->get('default_theme', 0, 'int');
        $id = $input->get('ba_id', 0, 'int');
        $title = $input->get('theme_title', 0, 'string');
        $db = JFactory::getDbo();
        if ($old != $default) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('home') . ' = 0'
            );
            $conditions = array(
                $db->quoteName('home') . ' = 1',
                $db->quoteName('client_id') . ' = 0',
            );
            $query->update($db->quoteName('#__template_styles'))
                ->set($fields)
                ->where($conditions);
            $db->setQuery($query)
                ->execute();
        }
        $query = $db->getQuery(true)
            ->select('params')
            ->from('#__template_styles')
            ->where('id = '.$id);
        $db->setQuery($query);
        $result = $db->loadResult();
        $obj = json_decode($result);
        $obj->image = $input->get('image', '', 'string');
        $result = json_encode($obj);
        $table->load($id);
        $table->bind(array('title' => $title, 'home' => $default, 'params' => $result));
        $table->store();
        echo JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        exit;
    }

    public function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_modules');
        parent::cleanCache('mod_menu');
    }
    
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            $this->option . '.gridbox', 'gridbox', array('control' => 'jform', 'load_data' => $loadData)
        );
        
        if (empty($form))
        {
            return false;
        }
 
        return $form;
    }
    
    protected function getNewTitle($title)
	{
        $table = $this->getTable();
        while ($table->load(array('title' => $title)))
		{
			$title = gridboxHelper::increment($title);
		}

		return $title;
	}
    
    public function duplicate(&$pks)
    {
        $db = $this->getDbo();
        foreach ($pks as $pk) {
            $table = $this->getTable();
            $table->load($pk, true);
            $table->id = 0;
            $table->title = $this->getNewTitle($table->title);
            $table->home = 0;
            $table->store();
            gridboxHelper::copyThemeFiles($pk, $table->id);
        }
    }
}