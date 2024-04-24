<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class gridboxModelEditor extends JModelItem
{
    public function submitNewItem($data)
    {
        $id = $data['id'];
        $app_id = $data['app_id'];
        $page_id = $data['page_id'];
        $title = $data['title'];
        $category = $data['category'];
        $image = $description = '';
        $user = JFactory::getUser();
        $form = gridboxHelper::getSystemParams($id);
        $options = json_decode($form->page_options);
        $groups = $user->getAuthorisedViewLevels();
        if (!in_array($options->access, $groups)) {
            return;
        }
        unset($data['id']);
        unset($data['app_id']);
        unset($data['page_id']);
        unset($data['title']);
        unset($data['category']);
        if (isset($data['image'])) {
            $image = $data['image'];
        }
        if (isset($data['description'])) {
            $description = $data['description'];
            unset($data['description']);
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('theme')
            ->from('#__gridbox_app')
            ->where('id = '.$app_id);
        $db->setQuery($query);
        $theme = $db->loadResult();
        $title = strip_tags($title['value']);
        $alias = gridboxHelper::getAlias($title, '#__gridbox_pages', 'page_alias');
        $category = intval($category['value']);
        if (!empty($image)) {
            $obj = json_decode($image['value']);
            $image = $obj->src;
        }
        if (!empty($description)) {
            $description = strip_tags($description['value']);
        }
        $nowDate = date("Y-m-d H:i:s");
        $page = new stdClass();
        $page->style = '{}';
        $page->params = '';
        $page->title = $title;
        $page->page_alias = $alias;
        $page->page_category = $category;
        $page->app_id = $app_id;
        $page->theme = $theme;
        $page->created = $nowDate;
        $page->intro_image = $image;
        $page->intro_text = $description;
        $page->user_id = $user->id;
        if (!empty($page_id)) {
            $page->id = $page_id;
            $db->updateObject('#__gridbox_pages', $page, 'id');
        } else {
            $page->published = $options->premoderation ? 0 : 1;
            $db->insertObject('#__gridbox_pages', $page);
            $page->id = $db->insertid();
        }
        $fields = [];
        $tags = [];
        $hasTags = false;
        foreach ($data as $field) {
            if ($field['type'] == 'tag') {
                $hasTags = true;
                $tags = json_decode($field['value']);
                continue;
            }
            if ($field['type'] == 'image-field') {
                $obj = json_decode($field['value']);
                if (is_numeric($obj->src)) {
                    $this->setDesktopFiles($obj->src, $page->id);
                }
            } else if ($field['type'] == 'field-simple-gallery' || $field['type'] == 'field-slideshow') {
                $images = json_decode($field['value']);
                foreach ($images as $object) {
                    if (is_numeric($object->img)) {
                        $this->setDesktopFiles($object->img, $page->id);
                    }
                }
            } else if ($field['type'] == 'field-video') {
                $obj = json_decode($field['value']);
                if (!empty($obj) && is_numeric($obj->file)) {
                    $this->setDesktopFiles($obj->file, $page->id);
                }
            } else if ($field['type'] == 'file' && is_numeric($field['value'])) {
                $this->setDesktopFiles($field['value'], $page->id);
            }
            if ($field['type'] == 'checkbox' || $field['type'] == 'url' || $field['type'] == 'field-button' || $field['type'] == 'image-field') {
                $field['value'] = json_decode($field['value']);
            }
            $fields[] = (object)$field;
        }
        gridboxHelper::savePageFields($fields, $page->id);
        if ($hasTags) {
            gridboxHelper::saveMetaTags($tags, $page->id);
        }
        if ($options->author) {
            $this->setAuthor($user, $page->id);
        }
        if (empty($page_id)) {
            $obj = new stdClass();
            $obj->page_id = $page->id;
            $obj->submission_form = $id;
            $obj->sended_published = $options->premoderation ? 0 : 1;
            $db->insertObject('#__gridbox_submitted_items', $obj);
        }
        if (empty($page_id) && $options->emails && $options->submited_email) {
            $this->sendModeratorEmail($user->username, $page->title);
        }
        print_r($fields);exit;
    }

    public function setAuthor($user, $page_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_authors')
            ->where('user_id = '.$user->id);
        $db->setQuery($query);
        $author = $db->loadObject();
        if (!$author) {
            $author = new stdClass();
            $author->id = 0;
            $author->title = $user->name;
            $author->user_id = $user->id;
            $author->author_social = '{}';
            $author->alias = $user->username;
            $author->alias = gridboxHelper::getAlias($author->alias, '#__gridbox_authors', 'alias');
            $db->insertObject('#__gridbox_authors', $author);
            $author->id = $db->insertid();
        }
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_authors_map')
            ->where('page_id = '.$page_id)
            ->where('author_id = '.$author->id);
        $db->setQuery($query);
        $count = $db->loadResult();
        if ($count == 0) {
            $obj = new stdClass();
            $obj->page_id = $page_id;
            $obj->author_id = $author->id;
            $db->insertObject('#__gridbox_authors_map', $obj);
        }
    }

    public function sendModeratorEmail($username, $title)
    {
        try {
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sitename = $config->get('sitename');
            $subject = JText::_('THE_NEW_ITEM_SUBMITTED_ON').' '.$sitename;
            $sender = [$config->get('mailfrom'), $config->get('fromname')];
            $recipient = $config->get('mailfrom');
            $message = JText::_('THE_NEW_ITEM_SUBMITTED_BY_USER');
            $message = str_replace('{USERNAME}', $username, $message);
            $mailer->isHTML(true);
            $mailer->Encoding = 'base64';
            include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/submission-form/moderator-email-pattern.php');
            $mailer->setSender($sender);
            $mailer->setSubject($subject);
            $mailer->addRecipient($recipient);
            $mailer->setBody($out);
            $mailer->Send();
        } catch (Exception $e) {
            
        }
    }


    public function setDesktopFiles($id, $page_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_fields_desktop_files')
            ->where('id = '.$id)
            ->set('page_id = '.$page_id);
        $db->setQuery($query)
            ->execute();
    }

    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = []) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function renameVersionsHistory($id, $title)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_pages_versions')
            ->set('title = '.$db->quote($title))
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function deleteVersionsHistory($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_pages_versions')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function getVersionsHistory($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_pages_versions')
            ->where('page_id = '.$id)
            ->order('id DESC');
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

    public function cleanupVersionsHistory()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_pages_versions')
            ->where('1');
        $db->setQuery($query)
            ->execute();
    }

    public function getPageFields()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $id = $app->input->get('id', '');
        $query = $db->getQuery(true)
            ->select('f.*')
            ->from('#__gridbox_fields AS f')
            ->leftJoin('#__gridbox_pages AS p ON p.app_id = f.app_id')
            ->where('p.id = '.$id)
            ->order('f.order_list DESC')
            ->where('f.field_type <> '.$db->quote('field-simple-gallery'))
            ->where('f.field_type <> '.$db->quote('field-slideshow'))
            ->where('f.field_type <> '.$db->quote('product-gallery'))
            ->where('f.field_type <> '.$db->quote('product-slideshow'))
            ->where('f.field_type <> '.$db->quote('field-google-maps'))
            ->where('f.field_type <> '.$db->quote('field-video'))
            ->where('f.field_type <> '.$db->quote('image-field'))
            ->where('f.field_type <> '.$db->quote('tag'))
            ->where('f.field_type <> '.$db->quote('field-button'));
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $data = new stdClass();
        foreach ($fields as $field) {
            $params = json_decode($field->options);
            $field->params = $params;
            $field->title = !empty($field->label) ? $field->label : $params->label;
            $data->{$field->field_key} = $field;
        }

        return $data;
    }

    public function getDefaultsSeo($id, $type)
    {
        $db = JFactory::getDbo();
        $item = new stdClass();
        $item->app_id = $id;
        if ($type == 'page') {
            $query = $db->getQuery(true)
                ->select('type')
                ->from('#__gridbox_app')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item->app_type = $db->loadResult();
        }
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/seo.php';
        $seo = new gridboxSeoHelper($item, $type);
        $global = $seo->getGlobal();

        return $global;
    }

    public function setDefaultsSeo($seo)
    {
        $db = JFactory::getDbo();
        if (empty($seo->id)) {
            $db->insertObject('#__gridbox_seo_defaults', $seo);
        } else {
            $db->updateObject('#__gridbox_seo_defaults', $seo, 'id');
        }
    }

    public function getHTMLHelper($dir = '')
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/html.php';
        $html = new gridboxHTMLHelper($dir);

        return $html;
    }

    public function getIntegrations()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('type = '.$db->quote('integration'));
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $integrations = new stdClass();
        foreach ($array as $obj) {
            $integrations->{$obj->service} = $obj;
        }

        return $integrations;
    }

    public function checkAppFields($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();

        return $type ? $type : '';
    }

    public function getProductData()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $product = gridboxHelper::$storeHelper->getPreparedProductData($id);
        
        return $product;
    }

    public function getProductOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_products_fields');
        $db->setQuery($query);
        $array = $db->loadObjectList();

        return $array;
    }

    public function getPageAppId($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $app_id = $db->loadResult();

        return $app_id;
    }

    public function uploadFile($file, $app_id, $id)
    {
        $ext = strtolower(JFile::getExt($file['name']));
        $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/app-'.$app_id.'/';
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }
        $name = str_replace('.'.$ext, '', $file['name']);
        $filename = gridboxHelper::replace($name);
        $filename = JFile::makeSafe($filename);
        $name = str_replace('-', '', $filename);
        $name = str_replace('.', '', $name);
        if ($name == '') {
            $filename = date("Y-m-d-H-i-s").'.'.$ext;
        }
        $i = 2;
        $name = $filename;
        while (JFile::exists($dir.$name.'.'.$ext)) {
            $name = $filename.'-'.($i++);
        }
        $filename = $name.'.'.$ext;
        JFile::upload($file['tmp_name'], $dir.$filename);
        $obj = $this->addDesktopFieldFile($file['name'], $filename, $id, $app_id);
        $obj->path = 'components/com_gridbox/assets/uploads/app-'.$app_id.'/'.$filename;

        return $obj;
    }

    public function uploadSubmissionFile($file, $app_id)
    {
        $obj = new stdClass();
        $id = 0;
        if (isset($file['error']) && $file['error'] == 0) {
            $obj = $this->uploadFile($file, $app_id, $id);
        } else {
            $obj->error = 'ba-alert';
            $obj->msg = JText::_('NOT_ALLOWED_FILE_SIZE');
        }

        return $obj;
    }

    public function uploadDesktopFieldFile($file, $id)
    {
        $obj = new stdClass();
        if (isset($file['error']) && $file['error'] == 0) {
            $app_id = $this->getPageAppId($id);
            $obj = $this->uploadFile($file, $app_id, $id);
        } else {
            $obj->error = 'ba-alert';
            $obj->msg = JText::_('NOT_ALLOWED_FILE_SIZE');
        }

        return $obj;
    }

    public function addDesktopFieldFile($name, $filename, $id, $app_id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->page_id = $id;
        $obj->app_id = $app_id;
        $obj->name = $name;
        $obj->filename = $filename;
        $obj->date = date("Y-m-d-H-i-s");
        $db->insertObject('#__gridbox_fields_desktop_files', $obj);
        $obj->id = $db->insertid();
        if ($id == 0) {
            $obj->alt = JFile::stripExt($name);
        }

        return $obj;
    }

    public function removeDesktopFieldFile($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields_desktop_files')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if ($obj) {
            $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/app-'.$obj->app_id.'/'.$obj->filename;
            JFile::delete($dir);
            $query = $db->getQuery(true)
                ->delete('#__gridbox_fields_desktop_files')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function setYandexMapsKey()
    {
        $input = JFactory::getApplication()->input;
        $key = $input->get('yandex_maps', '', 'string');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('`#__gridbox_api`')
            ->set('`key` = '.$db->quote($key))
            ->where('`service` = '.$db->quote('yandex_maps'));
        $db->setQuery($query)
            ->execute();
    }

    public function setOpenWeatherMapKey()
    {
        $input = JFactory::getApplication()->input;
        $key = $input->get('openweathermap', '', 'string');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('`#__gridbox_api`')
            ->set('`key` = '.$db->quote($key))
            ->where('`service` = '.$db->quote('openweathermap'));
        $db->setQuery($query)
            ->execute();
    }

    public function saveMenuItemTitle()
    {
        $input = JFactory::getApplication()->input;
        $obj = new stdClass();
        $obj->id = $input->get('id', 0, 'int');
        $obj->title = $input->get('title', '', 'string');
        $db = JFactory::getDbo();
        $db->updateObject('#__menu', $obj, 'id');
    }

    public function deleteMenuItem($ids, $parents)
    {
        $db = JFactory::getDbo();
        foreach ($ids as $key => $id) {
            $query = $db->getQuery(true)
                ->delete('#__menu')
                ->where("id = " . $id);
            $db->setQuery($query)
                ->execute();
            $query->clear()
                ->update('#__menu')
                ->where('parent_id = '.$id)
                ->set('parent_id = '.$parents[$key]);
            $db->setQuery($query)
                ->execute();
        }
        $this->rebuild();
    }

    public function rebuild()
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/menu.php';
        $menu = new gridboxMenu();
        $menu->rebuild();
    }

    public function sortMenuItems()
    {
        $input = JFactory::getApplication()->input;
        $idArray = $input->get('idArray', [], 'array');
        $pks = [];
        foreach ($idArray as $value) {
            $pks[] = $value['id'];
        }
        $idStr = implode(',', $pks);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('lft, rgt')
            ->from('#__menu')
            ->where('id in ('.$idStr.')')
            ->order('lft ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        for ($i = 0; $i < count($idArray); $i++) {
            $query->clear()
                ->update('#__menu')
                ->where('id = '.$idArray[$i]['id'])
                ->set('lft = '.$items[$i]->lft)
                ->set('parent_id = '.$idArray[$i]['parent_id'])
                ->set('rgt = '.$items[$i]->rgt);
            $db->setQuery($query)
                ->execute();
        }
        $this->rebuild();
    }

    public function setLibraryImage()
    {
        $input = JFactory::getApplication()->input;
        $str = $input->get('object', '', 'string');
        $obj = json_decode($str);
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_library', $obj, 'id');
    }

    public function setStarRatings()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', '', 'string');
        $rating = $input->get('rating', 0, 'int');
        $str = $input->get('page', '', 'string');
        $page = json_decode($str);
        if ($page->option == 'com_gridbox' && $page->view == 'gridbox') {
            $page->view = 'page';
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_star_ratings_users')
            ->where('`ip` = '.$db->quote($ip))
            ->where('`plugin_id` = '.$db->quote($id))
            ->where('`option` = '.$db->quote($page->option))
            ->where('`view` = '.$db->quote($page->view))
            ->where('`page_id` = '.$db->quote($page->id));
        $db->setQuery($query);
        $flag = $db->loadResult();
        $object = new stdClass();
        if (empty($flag)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_star_ratings')
                ->where('`plugin_id` = '.$db->quote($id))
                ->where('`option` = '.$db->quote($page->option))
                ->where('`view` = '.$db->quote($page->view))
                ->where('`page_id` = '.$db->quote($page->id));
            $db->setQuery($query);
            $obj = $db->loadObject();
            if (!isset($obj->id)) {
                $obj = new stdClass();
                $obj->plugin_id = $id;
                $obj->rating = $rating;
                $obj->count = 1;
                $obj->option = $page->option;
                $obj->view = $page->view;
                $obj->page_id = $page->id;
                $db->insertObject('#__gridbox_star_ratings', $obj);
                $obj->id = $db->insertid();
            } else {
                $total = ($obj->rating * $obj->count + $rating) / ($obj->count + 1);
                $obj->rating = number_format($total, 2);
                $obj->count++;
                $db->updateObject('#__gridbox_star_ratings', $obj, 'id');
            }
            $user = new stdClass();
            $user->plugin_id = $obj->plugin_id;
            $user->option = $page->option;
            $user->view = $page->view;
            $user->page_id = $page->id;
            $user->ip = $ip;
            $db->insertObject('#__gridbox_star_ratings_users', $user);
            $object->result = '<span>'.JText::_('THANK_YOU_FOR_VOTE').'</span>';
        } else {
            $object->result = '<span>'.JText::_('ALREADY_VOTED').'</span>';
        }
        list($object->str, $object->rating) = gridboxHelper::getStarRatings($id, $page);

        return $object;
    }

    public function getPageTags()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $query = $db->getQuery(true);
        $query->select('t.id, t.title')
            ->from('#__gridbox_tags_map AS m')
            ->leftJoin('#__gridbox_tags AS t ON t.id = m.tag_id')
            ->where('m.page_id = '.$id);
        $db->setQuery($query);
        $tags = $db->loadObjectList();
        
        return $tags;
    }

    public function getTagsFolders()
    {
        $data = new stdClass();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_tags_folders')
            ->order('order_list ASC');
        $db->setQuery($query);
        $folders = $db->loadObjectList();
        $data->folders = new stdClass();
        foreach ($folders as $folder) {
            $folder->tags = [];
            $data->folders->{$folder->id} = $folder;
        }
        $data->tags = $this->getTags();
        foreach ($data->tags as $tag) {
            $query = $db->getQuery(true)
                ->select('folder_id')
                ->from('#__gridbox_tags_folders_map')
                ->where('tag_id = '.$tag->id);
            $db->setQuery($query);
            $id = $db->loadResult();
            if (!$id) {
                $id = 1;
            }
            $tag->folder_id = $id;
        }

        return $data;
    }

    public function getTags()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_tags');
        $db->setQuery($query);
        $tags = $db->loadObjectList();

        return $tags;
    }

    public function checkProductTour()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`key`, `id`')
            ->from('`#__gridbox_api`')
            ->where('`service` = '.$db->quote('editor_tour'));
        $db->setQuery($query);
        $result = $db->loadObject();
        if (!isset($result->key)) {
            $result = new stdClass();
            $result->key = 'true';
            $obj = new stdClass();
            $obj->service = 'editor_tour';
            $obj->key = 'false';
            $db->insertObject('#__gridbox_api', $obj);
        }
        echo $result->key;
        exit;
    }

    public function setEditorView()
    {
        $app = JFactory::getApplication();
        $app->input->set('view', 'gridbox');
    }

    public function getLibrary()
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $type = $input->get('type', '', 'string');
        if ($type != 'blocks') {
            $id = $input->get('id', 0, 'int');
            $table = '#__gridbox_library';
            $where = 'id = ';
        } else {
            $id = $input->get('id', '', 'string');
            $table = '#__gridbox_page_blocks';
            $where = 'title = ';
        }
        if ($id != $_POST['id']) {
            $id = $_POST['id'];
        }
        $where .= $db->quote($id);
        $dispatcher = $this->getEventDispatcher();
        $query = $db->getQuery(true)
            ->select('item')
            ->from($table)
            ->where($where);
        $db->setQuery($query);
        $string = $db->loadResult();
        $item = json_decode($string);
        $this->setEditorView();
        $item->html = gridboxHelper::checkModules($item->html, $item->items);
        $file = JPATH_ROOT.'/plugins/system/bagallery/bagallery.php';
        if (JFile::exists($file)) {
            include_once $file;
            $config = ['type' => 'system', 'name' => 'bagallery', 'params' => '{}'];
            $plg = new plgSystemBagallery($dispatcher, $config);
            $item->html = $plg->getContent($item->html);
        }
        $file = JPATH_ROOT.'/plugins/system/baforms/baforms.php';
        if (JFile::exists($file)) {
            include_once $file;
            $config = ['type' => 'system', 'name' => 'baforms', 'params' => '{}'];
            $plg = new plgSystemBaforms($dispatcher, $config);
            $html = $plg->getContent($item->html);
            if ($html) {
                $item->html = $html;
            }
        }
        $item->html = gridboxHelper::checkMainMenu($item->html);
        $item = json_encode($item);

        echo $item;
        exit;
    }

    public function removeLibrary()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_library')
            ->where('`id` = '.$db->quote($id));
        $db->setQuery($query)
            ->execute();
        exit;
    }

    public function insertToLibrary($str)
    {
        $obj = json_decode($str);
        $db = JFactory::getDbo();
        if (!empty($obj->global_item)) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_library')
                ->where('`global_item` = '.$db->quote($obj->global_item));
            $db->setQuery($query);
            $id = $db->loadResult();
            if (!empty($id)) {
                $msg = new stdClass();
                $msg->text = JText::_('ALREADY_GLOBAL');
                $msg->type = 'ba-alert';
                $msg = json_encode($msg);
                echo($msg);
                exit;
            }
        }
        $obj->item = json_encode($obj->item);
        $db->insertObject('#__gridbox_library', $obj);
        $msg = new stdClass();
        $msg->text = JText::_('SAVED_TO_LIBRARY');
        $msg->type = '';
        $msg = json_encode($msg);
        echo $msg;
        exit;
    }

    public function requestAddLibrary()
    {
        $data = file_get_contents('php://input');
        $this->insertToLibrary($data);
    }

    public function addLibrary()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('object', '', 'raw');
        $json = json_decode($data);
        if (empty($data) || !$json) {
            print_r('empty_data');exit;
        }
        $this->insertToLibrary($data);
    }

    public function savePostFieldsGroups($data, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $app_id = $db->loadResult();
        if (!empty($app_id)) {
            $obj = new stdClass();
            $obj->id = $app_id * 1;
            $obj->fields_groups = json_encode($data);
            $db->updateObject('#__gridbox_app', $obj, 'id');
        }
    }

    public function saveProductData($product, $id)
    {
        $db = JFactory::getDbo();
        $product->data->variations = json_encode($product->variations);
        $product->data->extra_options = json_encode($product->extra_options);
        $product->data->dimensions = json_encode($product->dimensions);
        $product->data->booking = json_encode($product->booking);
        if ($product->data->id != 0) {
            $db->updateObject('#__gridbox_store_product_data', $product->data, 'id');
        } else {
            $db->insertObject('#__gridbox_store_product_data', $product->data);
        }
        $digital = !empty($product->data->digital_file) ? json_decode($product->data->digital_file) : new stdClass();
        if (isset($digital->file)) {
            $dir = gridboxHelper::$storeHelper->getDigitalFolder($id);
            if (JFolder::exists($dir)) {
                $files = JFolder::files($dir);
                foreach ($files as $file) {
                    if ($file != $digital->file->filename) {
                        JFile::delete($dir.$file);
                    }
                }
                $files = JFolder::files($dir);
                if (empty($files)) {
                    gridboxHelper::deleteFolder($dir);
                }
            }
        }
        $pks = [];
        foreach ($product->variations_map as $obj) {
            if ($obj->id != 0) {
                $db->updateObject('#__gridbox_store_product_variations_map', $obj, 'id');
            } else {
                $db->insertObject('#__gridbox_store_product_variations_map', $obj);
                $obj->id = $db->insertid();
            }
            $pks[] = $obj->id;
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_product_variations_map')
            ->where('product_id = '.$id);
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('id NOT IN ('.$str.')');
        }
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_badges_map')
            ->where('product_id = '.$id);
        $db->setQuery($query);
        $badges = $db->loadObjectList();
        $pks = [];
        foreach ($badges as $badge) {
            if (!isset($product->badges->{$badge->badge_id})) {
                $pks[] = $badge->id;
            } else {
                $product->badges->{$badge->badge_id}->obj = $badge;
            }
        }
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_badges_map')
                ->where('id IN ('.$str.')');
            $db->setQuery($query)
                ->execute();
        }
        foreach ($product->badges as $badge_id => $badge) {
            if (isset($badge->obj)) {
                $badge->obj->order_list = $badge->i;
                $db->updateObject('#__gridbox_store_badges_map', $badge->obj, 'id');
            } else {
                $badge->obj = new stdClass();
                $badge->obj->order_list = $badge->i;
                $badge->obj->badge_id = $badge_id;
                $badge->obj->product_id = $id;
                $db->insertObject('#__gridbox_store_badges_map', $badge->obj);
            }
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_related_products')
            ->where('product_id = '.$id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $pks = [];
        foreach ($items as $item) {
            if (!isset($product->related->{$item->related_id})) {
                $pks[] = $item->id;
            } else {
                $product->related->{$item->related_id}->id = $item->id;
            }
        }
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_related_products')
                ->where('id IN ('.$str.')');
            $db->setQuery($query)
                ->execute();
        }
        foreach ($product->related as $related) {
            if (isset($related->id)) {
                $db->updateObject('#__gridbox_store_related_products', $related, 'id');
            } else {
                $db->insertObject('#__gridbox_store_related_products', $related);
            }
        }
    }

    public function duplicate($id)
    {
        $db = JFactory::getDbo();
        $now = time();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $page = $db->loadObject();
        unset($page->id);
        $page->hits = 0;
        $page->order_list = 0;
        $page->title = gridboxHelper::increment($page->title);
        $page->title = $this->getNewTitle($page->title);
        $page->page_alias = gridboxHelper::getAlias($page->page_alias, '#__gridbox_pages');
        $page->published = 0;
        $page->order_list = 0;
        $page->root_order_list = 0;
        $page->created = date("Y-m-d H:i:s", $now++);
        $db->insertObject('#__gridbox_pages', $page);
        $pk = $db->insertid();
        $this->duplicatePageFields($id, $pk);
        $this->duplicateProductData($id, $pk);
        $query = $db->getQuery(true)
            ->select('tag_id')
            ->from('`#__gridbox_tags_map`')
            ->where('`page_id` = '.$id);
        $db->setQuery($query);
        $tags = $db->loadObjectList();
        foreach ($tags as $tag) {
            $tag->page_id = $pk;
            $db->insertObject('#__gridbox_tags_map', $tag);
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_authors_map')
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $authors = $db->loadObjectList();
        foreach ($authors as $author) {
            $author->page_id = $pk;
            unset($author->id);
            $db->insertObject('#__gridbox_authors_map', $author);
        }

        return $pk;
    }

    public function duplicateProductData($pk, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_product_variations_map')
            ->where('product_id = '.$pk);
        $db->setQuery($query);
        $variations = $db->loadObjectList();
        foreach ($variations as $variation) {
            unset($variation->id);
            $variation->product_id = $id;
            $db->insertObject('#__gridbox_store_product_variations_map', $variation);
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_product_data')
            ->where('product_id = '.$pk);
        $db->setQuery($query);
        $data = $db->loadObjectList();
        foreach ($data as $value) {
            unset($value->id);
            $value->product_id = $id;
            if (!empty($value->digital_file)) {
                $digital = json_decode($value->digital_file);
                $digital->file->name = $digital->file->filename = '';
                $value->digital_file = json_encode($digital);
            }
            $db->insertObject('#__gridbox_store_product_data', $value);
        }
    }

    public function duplicatePageFields($pk, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_page_fields')
            ->where('page_id = '.$pk);
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        foreach ($fields as $field) {
            $field->id = 0;
            $field->page_id = $id;
            $db->insertObject('#__gridbox_page_fields', $field);
        }
    }

    public function getNewTitle($title, $table = '#__gridbox_pages')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($table)
            ->where('title = '.$db->quote($title));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            $title = gridboxHelper::increment($title);
            $title = $this->getNewTitle($title);
        }

        return $title;
    }

    public function saveVersion($obj)
    {
        $db = JFactory::getDbo();
        $obj->title = date("Y-m-d H:i:s");
        $obj->items = json_encode($obj->items);
        $db->insertObject('#__gridbox_pages_versions', $obj);
        if (gridboxHelper::$website->versions_auto_save == 1 && !empty(gridboxHelper::$website->max_versions)) {
            $this->checkMaxVersions($db, $obj->page_id);
        }
    }

    public function checkMaxVersions($db, $page_id)
    {
        $max = gridboxHelper::$website->max_versions * 1;
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_pages_versions')
            ->where('page_id = '.$page_id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $n = count($items);
        if ($max > $n) {
            return;
        }
        $delta = $n - $max;
        $sliced = array_slice($items, 0, $delta);
        $array = [];
        foreach ($sliced as $obj) {
            $array[] = $obj->id;
        }
        $str = implode(', ', $array);
        $query = $db->getQuery(true)
            ->delete('#__gridbox_pages_versions')
            ->where('id IN ('.$str.')');
        $db->setQuery($query)
            ->execute();
    }

    public function gridboxSave($obj)
    {
        if (gridboxHelper::$website->compress_images != $obj->website->compress_images ||
            gridboxHelper::$website->images_max_size != $obj->website->images_max_size ||
            gridboxHelper::$website->images_quality != $obj->website->images_quality ||
            gridboxHelper::$website->adaptive_images != $obj->website->adaptive_images ||
            gridboxHelper::$website->adaptive_quality != $obj->website->adaptive_quality) {
            gridboxHelper::deleteFolder(JPATH_ROOT.'/'.IMAGE_PATH.'/compressed');
        }
        $favicon = JPATH_ROOT.'/templates/gridbox/favicon.png';
        if (gridboxHelper::$website->favicon != $obj->website->favicon && JFile::exists($favicon)) {
            JFile::delete($favicon);
        }
        gridboxHelper::$website = $obj->website;
        gridboxHelper::siteRules($obj->breakpoints);
        gridboxHelper::saveTheme($obj->theme, $obj->page->theme);
        if (!isset($obj->edit_type)) {
            if (gridboxHelper::$website->versions_auto_save == 1) {
                $object = new stdClass();
                $object->page_id = $obj->page->id;
                $object->items = $obj->page->style;
                $object->html = $obj->page->params;
                $this->saveVersion($object);
            }
            if (is_numeric($obj->page->intro_image)) {
                $field = new stdClass();
                $field->field_id = 'image';
                $field->type = 'image-field';
                $field->value = new stdClass();
                $field->value->src = $obj->page->intro_image;
                $obj->fields->image = $field;
            }
            gridboxHelper::savePage($obj->page, $obj->page->id);
            gridboxHelper::savePageFields($obj->fields, $obj->page->id);
            if (isset($obj->product)) {
                $this->saveProductData($obj->product, $obj->page->id);
            }
            $this->savePostFieldsGroups($obj->fieldsGroups, $obj->page->id);
            gridboxHelper::triggerEvent('onGidboxPageAfterSave', [$obj->page->id], 'finder');
        } else if ($obj->edit_type == 'blog') {
            gridboxHelper::saveAppLayout($obj->page, $obj->page->id);
        } else if ($obj->edit_type == 'system') {
            gridboxHelper::saveSystemPage($obj->page, $obj->page->id);
        } else if ($obj->edit_type == 'post-layout') {
            gridboxHelper::savePostLayout($obj->page, $obj->page->id);
        }
        gridboxHelper::saveCodeEditor($obj->code, $obj->page->theme);
        gridboxHelper::saveWebsite($obj->website);
        gridboxHelper::saveGlobalItems($obj->global);
        $performance = gridboxHelper::getPerformance();
        $options = [
            'defaultgroup' => 'gridbox',
            'browsercache' => $performance->browser_cache,
            'caching'  => false,
        ];
        $cache = JCache::getInstance('page', $options);
        $cacheFolders = JFolder::folders(JPATH_CACHE);
        foreach ($cacheFolders as $group) {
            $cache->clean($group);
        }
        echo JText::_('GRIDBOX_SAVED');
        exit;
    }

    public function checkMainMenu()
    {
        $input = JFactory::getApplication()->input;
        $menu = $input->get('main_menu', 0, 'int');
        $data = $input->get('items', '', 'raw');
        $id = $input->get('id', 0, 'int');
        $items = new stdClass();
        $items->{$id} = json_decode($data);
        $html = '<div class="ba-item-main-menu ba-item" id="'.$id.'">[main_menu='.$menu.']</div>';
        $html = gridboxHelper::checkMainMenu($html);
        $html = gridboxHelper::checkDOM($html, $items);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $dom = phpQuery::newDocument($html);
        $html = pq('.ba-item-main-menu')->html();
        echo $html;
        exit;
    }

    public function setMapsKey()
    {
        $input = JFactory::getApplication()->input;
        $key = $input->get('google_maps_key', '', 'string');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('`#__gridbox_api`')
            ->set('`key` = '.$db->quote($key))
            ->where('`service` = '.$db->quote('google_maps'));
        $db->setQuery($query)
            ->execute();
    }

    public function getBlocksLicense($data)
    {
        $this->installBlocks($data);
        echo JText::_('BLOCKS_INSTALLED');
        exit;
    }

    public function getPluginLicense()
    {
        $input = JFactory::getApplication()->input;
        $str = $input->get('data', '', 'string');
        $data = json_decode($str);
        $this->installPlugin($data);
        echo JText::_('PLUGIN_INSTALLED');
        exit;
    }

    public function loadLayout()
    {
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'raw');
        $count = $input->get('count', '', 'raw');
        $span = explode('+', $count);
        $count = count($span);
        $obj = new stdClass();
        $obj->items = new stdClass();
        $now = $input->get('time', strtotime(date('Y-m-d G:i:s')), 'raw');
        include JPATH_ROOT.'/components/com_gridbox/views/layout/'.$layout.'.php';
        $obj->html = $html = gridboxHelper::replaceBootstrap($out);
        echo json_encode($obj);
        exit;
    }

    protected function getEventDispatcher()
    {
        if (JVERSION >= '4.0.0') {
            $dispatcher = JFactory::getApplication()->getDispatcher();
        } else {
            $dispatcher = JEventDispatcher::getInstance();
        }

        return $dispatcher;
    }

    public function reloadModules($id, $type)
    {
        $this->setEditorView();
        $out = '['.$type.' ID='.$id.']';
        $dispatcher = $this->getEventDispatcher();
        if ($type == 'modules') {
            $out = gridboxHelper::checkModules($out, '{}');
            $str = $this->returnStyle();
            $out = $str.$out;
        } else if ($type == 'gallery') {
            $file = JPATH_ROOT.'/plugins/system/bagallery/bagallery.php';
            if (JFile::exists($file)) {
                include_once $file;
                $config = ['type' => 'system', 'name' => 'bagallery', 'params' => '{}'];
                $plg = new plgSystemBagallery($dispatcher, $config);
                $out = $plg->getContent($out);
            }
        } else if ($type == 'forms') {
            $file = JPATH_ROOT.'/plugins/system/baforms/baforms.php';
            if (JFile::exists($file)) {
                include_once $file;
                $config = ['type' => 'system', 'name' => 'baforms', 'params' => '{}'];
                $plg = new plgSystemBaforms($dispatcher, $config);
                $out = $plg->getContent($out);
            }
        }

        return $out;
    }

    public function contentSliderAdd()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', [], 'array');
        $ind = $input->get('ind', 1, 'int');
        $title = $input->get('title', 1, 'int');
        $now = strtotime(date('Y-m-d G:i:s')) * 10;
        $obj = new stdClass();
        $obj->items = new stdClass();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/content-slider-li.php';
        $obj->slides = $slides;
        $obj->html = $out;
        $str = json_encode($obj);

        return $str;
    }

    public function loadPlugin()
    {
        $input = JFactory::getApplication()->input;
        $layout = $input->get('plugin', '', 'string');
        $id = $input->get('id', 0, 'int');
        $edit_type = $input->get('edit_type', 'edit_type', 'string');
        if (!gridboxHelper::checkPlugin($layout)) {
            echo '';
            exit;
        }
        if ($layout == 'content-slider') {
            $data = $input->get('data', [], 'array');
        } else {
            $data = $input->get('data', '', 'string');
        }
        $obj = new stdClass();
        $obj->items = new stdClass();
        $now = $input->get('time', strtotime(date('Y-m-d G:i:s')), 'raw');
        $this->setEditorView();
        $dispatcher = $this->getEventDispatcher();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/'.$layout.'.php';
        if ($layout == 'modules') {
            $out = gridboxHelper::checkModules($out, $obj->items);
            $str = $this->returnStyle();
            $out = str_replace('<input type="hidden" class="modules-styles">', $str, $out);
        } else if ($layout == 'language-switcher' || $layout == 'login' || $layout == 'add-to-cart') {
            $out = gridboxHelper::checkDOM($out, $obj->items);
        } else if ($layout == 'bagallery') {
            $file = JPATH_ROOT.'/plugins/system/bagallery/bagallery.php';
            if (JFile::exists($file)) {
                include_once $file;
                $config = ['type' => 'system', 'name' => 'bagallery', 'params' => '{}'];
                $plg = new plgSystemBagallery($dispatcher, $config);
                $str = '[gallery ID='.$data.']';
                $str = $plg->getContent($str);
                $out = str_replace('[gallery ID='.$data.']', $str, $out);
            }
        } else if ($layout == 'baforms') {
            $file = JPATH_ROOT.'/plugins/system/baforms/baforms.php';
            if (JFile::exists($file)) {
                include_once $file;
                $config = ['type' => 'system', 'name' => 'baforms', 'params' => '{}'];
                $plg = new plgSystemBaforms($dispatcher, $config);
                $str = '[forms ID='.$data.']';
                $str = $plg->getContent($str);
                $out = str_replace('[forms ID='.$data.']', $str, $out);
            }
        } else if ($layout == 'menu') {
            $out = gridboxHelper::checkMainMenu($out);
            $out = gridboxHelper::checkDOM($out, $obj->items);
        } else if ($layout == 'post-tags') {
            $str = gridboxHelper::getPostTags($id);
            $out = str_replace('[blog_post_tags]', $str, $out);
        } else if ($layout == 'tags') {
            if ($edit_type == 'blog') {
                $obj->items->{'item-'.$now}->app = $id;
            } else if (empty($edit_type)) {
                $obj->items->{'item-'.$now}->app = gridboxHelper::getAppId($id);
            }
            if (empty($obj->items->{'item-'.$now}->app)) {
                $obj->items->{'item-'.$now}->app = 0;
            }
            $str = gridboxHelper::getBlogTags($obj->items->{'item-'.$now}->app, '', $obj->items->{'item-'.$now}->count);
            $out = str_replace('[ba_blog_tags]', $str, $out);
        } else if ($layout == 'categories') {
            if ($edit_type == 'blog') {
                $obj->items->{'item-'.$now}->app = $id;
            } else if (empty($edit_type)) {
                $obj->items->{'item-'.$now}->app = gridboxHelper::getAppId($id);
            }
            if (empty($obj->items->{'item-'.$now}->app)) {
                $obj->items->{'item-'.$now}->app = 0;
            }
            $categories = gridboxHelper::getBlogCategories($obj->items->{'item-'.$now}->app);
            $str = gridboxHelper::getBlogCategoriesHtml($categories, $obj->items->{'item-'.$now}->maximum);
            $out = str_replace('[ba_blog_categories]', $str, $out);
        } else if ($layout == 'recent-comments') {
            $id = $obj->items->{'item-'.$now}->app;
            $sort = $obj->items->{'item-'.$now}->sorting;
            $limit = $obj->items->{'item-'.$now}->limit;
            $maximum = $obj->items->{'item-'.$now}->maximum;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getRecentComments($id, $sort, $limit, $maximum, '');
            $out = str_replace('[ba_recent_comments]', $str, $out);
        } else if ($layout == 'recent-reviews') {
            $id = $obj->items->{'item-'.$now}->app;
            $sort = $obj->items->{'item-'.$now}->sorting;
            $limit = $obj->items->{'item-'.$now}->limit;
            $maximum = $obj->items->{'item-'.$now}->maximum;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getRecentReviews($id, $sort, $limit, $maximum, '');
            $out = str_replace('[ba_recent_reviews]', $str, $out);
        } else if ($layout == 'recent-posts') {
            if ($edit_type == 'blog') {
                $obj->items->{'item-'.$now}->app = $id;
            } else if (empty($edit_type)) {
                $obj->items->{'item-'.$now}->app = gridboxHelper::getAppId($id);
            }
            if (empty($obj->items->{'item-'.$now}->app)) {
                $obj->items->{'item-'.$now}->app = 0;
            }
            $id = $obj->items->{'item-'.$now}->app;
            $sort = $obj->items->{'item-'.$now}->sorting;
            $limit = $obj->items->{'item-'.$now}->limit;
            $maximum = $obj->items->{'item-'.$now}->maximum;
            $featured = $obj->items->{'item-'.$now}->featured;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getRecentPosts($id, $sort, $limit, $maximum, '', $featured);
            $out = str_replace('[ba_recent_posts]', $str, $out);
        } else if ($layout == 'author') {
            $str = gridboxHelper::getPostAuthor($id);
            $out = str_replace('[ba_blog_post_author]', $str, $out);
        } else if ($layout == 'post-intro' || $layout == 'breadcrumbs' || $layout == 'comments-box'
            || $layout == 'reviews' || $layout == 'related-posts-slider' || $layout == 'recently-viewed-products'
            || $layout == 'currency-switcher') {
            $out = gridboxHelper::checkDOM($out, $obj->items);
        } else if ($layout == 'recent-posts-slider') {
            if ($edit_type == 'blog') {
                $obj->items->{'item-'.$now}->app = $id;
            } else if (empty($edit_type)) {
                $obj->items->{'item-'.$now}->app = gridboxHelper::getAppId($id);
            }
            if (empty($obj->items->{'item-'.$now}->app)) {
                $obj->items->{'item-'.$now}->app = 0;
            }
            $id = $obj->items->{'item-'.$now}->app;
            $sort = $obj->items->{'item-'.$now}->sorting;
            $limit = $obj->items->{'item-'.$now}->limit;
            $maximum = $obj->items->{'item-'.$now}->maximum;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getRecentPosts($id, $sort, $limit, $maximum);
            $out = str_replace('[ba_recent_posts_slider]', $str, $out);
        } else if ($layout == 'related-posts') {
            $obj->items->{'item-'.$now}->app = gridboxHelper::getAppId($id);
            if ($edit_type == 'post-layout') {
                $obj->items->{'item-'.$now}->app = $id;
            }
            $id = $obj->items->{'item-'.$now}->app;
            $related = $obj->items->{'item-'.$now}->related;
            $limit = $obj->items->{'item-'.$now}->limit;
            $maximum = $obj->items->{'item-'.$now}->maximum;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getRelatedPosts($id, $related, $limit, $maximum, 'created', $id);
            $out = str_replace('[ba_related_posts]', $str, $out);
        } else if ($layout == 'post-navigation') {
            $maximum = $obj->items->{'item-'.$now}->maximum;
            gridboxHelper::$editItem = $obj->items->{'item-'.$now};
            $str = gridboxHelper::getPostNavigation($maximum, $id);
            $out = str_replace('[ba_post_navigation]', $str, $out);
        }
        $obj->html = gridboxHelper::replaceBootstrap($out);
        echo json_encode($obj);
        exit;
    }

    public function returnStyle()
    {
        $str = '';
        $doc = JFactory::getDocument();
        foreach ($doc->_scripts as $key => $script) {
            $str .= '<script src="'.$key.'"';
            if (isset($script['defer']) && !empty($script['defer'])) {
                $str .= ' defer';
            }
            if (isset($script['async']) && !empty($script['async'])) {
                $str .= ' async';
            }
            $str .= '></script>';
        }
        foreach ($doc->_script as $key => $script) {
            $str .= '<script type="'.$key.'">'.$script.'</script>';
        }
        foreach ($doc->_styleSheets as $key => $link) {
            $str .= '<link href="'.$key.'" type="text/css"';
            if (isset($script['media']) && !empty($link['media'])) {
                $str .= ' media="'.$link['media'].'"';
            }
            $str .= ' rel="stylesheet">';
        }
        foreach ($doc->_style as $key => $style) {
            $str .= '<style>'.$style.'</style>';
        }

        return $str;
    }

    public function getWebsite()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('`#__gridbox_website`')
            ->where('`id` = 1');
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    public function getLibraryItems()
    {
        $obj = new stdClass();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('title, global_item, id, image')
            ->from('`#__gridbox_library`')
            ->where('`type` = ' .$db->quote('section'));
        $db->setQuery($query);
        $obj->sections = $db->loadObjectList();
        $query = $db->getQuery(true);
        $query->select('title, global_item, id, image')
            ->from('`#__gridbox_library`')
            ->where('`type` = ' .$db->quote('plugin'));
        $db->setQuery($query);
        $obj->plugins = $db->loadObjectList();

        return $obj;
    }

    private function getUsername($name, $pwd)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('username')
            ->from('#__users')
            ->where('username = '.$db->quote($name))
            ->where('password = '.$db->quote($pwd));
        $db->setQuery($query);
        $username = $db->loadResult();

        return $username;
    }

    public function getItem($id = null)
    {
        $input = JFactory::getApplication()->input;
        $name = $input->get('name', '', 'raw');
        $pwd = $input->get('pwd', '', 'raw');
        $username = $this->getUsername($name, $pwd);
        if (!JFactory::getUser()->authorise('core.edit', 'com_gridbox') && !empty($username)) {
            gridboxHelper::userLogin($username);
        }
        if (!empty($name) || !empty($pwd)) {
            $get = $input->get->getArray([]);
            $id = $get['id'];
            unset($get['name']);
            unset($get['pwd']);
            unset($get['id']);
            unset($get['Itemid']);
            $get['id'] = $id;
            $url = http_build_query($get);
            header('Location: '.JUri::current().'?'.$url);
            exit;
        }
        $db = $this->getDbo();
        $title = $input->get('ba-title', '', 'string');
        $edit_type = $input->get('edit_type', '', 'string');
        $id = $input->get('id', 0, 'int');
        if ($id != 0) {
            $query = $db->getQuery(true);
            if ($edit_type == 'blog' || $edit_type == 'post-layout') {
                $query->select('b.*')
                    ->from('`#__gridbox_app` AS b')
                    ->where('b.type <> '.$db->quote('system_apps'))
                    ->where('b.id = ' .$id)
                    ->select('t.title as ThemeTitle')
                    ->leftJoin('`#__template_styles` AS t ON b.theme = t.id');
            } else if (empty($edit_type)) {
                $query->select('b.*')
                    ->from('`#__gridbox_pages` AS b')
                    ->where('b.id = ' .$id)
                    ->select('t.title as ThemeTitle')
                    ->leftJoin('`#__template_styles` AS t ON b.theme = t.id')
                    ->select('a.type as app_type')
                    ->leftJoin('`#__gridbox_app` AS a ON b.app_id = a.id');
            } else if ($edit_type == 'system') {
                $query->select('*')
                    ->from('#__gridbox_system_pages')
                    ->where('id = '.$id);
            }
            $db->setQuery($query);
            $item = $db->loadObject();
            if (isset($item->app_type) && $item->app_type != 'single') {
                $query = $db->getQuery(true)
                    ->select('a.id, a.avatar, a.title')
                    ->from('#__gridbox_authors_map AS m')
                    ->where('m.page_id = '.$item->id)
                    ->leftJoin('#__gridbox_authors AS a ON a.id = m.author_id')
                    ->order('m.id ASC');
                $db->setQuery($query);
                $item->authors = $db->loadObjectList();
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_category_page_map')
                    ->where('page_id = '.$item->id);
                $db->setQuery($query);
                $item->categories = $db->loadObjectList();
            } else {
                $item->authors = [];
            }
        } else {
            $item = new stdClass();
        }
        
        return $item;
    }

    public function getPageLayout()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.page_layout, a.type')
            ->from('#__gridbox_app AS a')
            ->leftJoin('#__gridbox_pages AS p ON a.id = p.app_id')
            ->where('p.id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (empty($item->page_layout)) {
            $item->page_layout = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.html');
        }
        
        return $item->page_layout;
    }

    public function getAuthors()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.title, a.id, a.avatar, u.username')
            ->from('`#__gridbox_authors` AS a')
            ->leftJoin('`#__users` AS u ON '.$db->quoteName('u.id').' = '.$db->quoteName('a.user_id'));
        $db->setQuery($query);
        $authors = $db->loadObjectList();

        return $authors;
    }

    public function getThemes()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, home')
            ->from('#__template_styles')
            ->where('`template`=' .$db->quote('gridbox'))
            ->order('home desc');
        $db->setQuery($query);
        $themes = new stdClass();
        $themes->list = $db->loadObjectList();
        $themes->default = $themes->list[0];
        $app_id = JFactory::getApplication()->input->get('app_id', 0, 'int');
        if (!empty($app_id)) {
            $query = $db->getQuery(true)
                ->select('theme')
                ->from('#__gridbox_app')
                ->where('id = '.$app_id);
            $db->setQuery($query);
            $theme = $db->loadResult();
            foreach ($themes->list as $value) {
                if ($value->id == $theme) {
                    $themes->default = $value;
                    break;
                }
            }
        }
        
        return $themes;
    }

    public function installBlocks($item)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->type = $item->type;
        $obj->title = $item->title;
        $obj->image = $item->image;
        $object = json_decode($item->data);
        $object->items = json_decode($object->items);
        $obj->item = json_encode($object);
        $db->insertObject('#__gridbox_page_blocks', $obj);
        $array = explode(',', $item->imageData);
        $method = $item->method;
        $content = $method($array[1]);
        JFile::write(JPATH_COMPONENT.'/assets/images/page-blocks/'.$obj->image, $content);
    }

    public function installPlugin($data)
    {
        $db = JFactory::getDbo();
        foreach ($data as $group) {
            foreach ($group as $plugin) {
                $db->insertObject('#__gridbox_plugins', $plugin);
            }
        }
    }

    public function checkBlocks($block) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_page_blocks')
            ->where('`title` = ' .$db->quote($block));
        $db->setQuery($query);
        $id = $db->loadResult();
        
        return $id;
    }

    public function checkPlugin($plugin) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_plugins')
            ->where('`title` = ' .$db->quote($plugin));
        $db->setQuery($query);
        $id = $db->loadResult();
        
        return $id;
    }

    public function returnObj($id, $plugin)
    {
        $obj = new stdClass();
        $obj->id = $id;
        $obj->title = trim((string)$plugin->title);
        $obj->image = trim((string)$plugin->image);
        $obj->type = trim((string)$plugin->type);
        $obj->joomla_constant = trim((string)$plugin->joomla_constant);

        return $obj;
    }

    public function getBlocks()
    {
        $blocks = [
            'cover' => [], 'about-us' => [], 'services' => [], 
            'description' => [], 'steps' => [], 'schedule' => [], 'features' => [],
            'pricing-table' => [], 'pricing-list' => [], 'testimonials' => [], 'team' => [],
            'counters' => [], 'faq' => [], 'call-to-action' => []
        ];
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('type, title, image, id')
            ->from('#__gridbox_page_blocks')
            ->order('id asc');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            if (isset($blocks[$item->type])) {
                $blocks[$item->type][$item->title] = $item;
            }
        }

        return $blocks;
    }

    public function checkInstalledBlog($type = '')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('type <> '.$db->quote('single'));
        if (!empty($type)) {
            $query->where('type = '.$db->quote($type));
        }
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }

    public function getPlugins()
    {
        $input = JFactory::getApplication()->input;
        $edit_type = $input->get('edit_type', '', 'string');
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_plugins')
            ->order('joomla_constant asc');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $plugins = [
            'content' => [], 'info' => [], 'navigation' => [],
            'social' => [], 'blog' => [], 'store' => [], 'fields' => [], '3rd-party-plugins' => []
        ];
        $blog = $this->checkInstalledBlog();
        if ($blog) {
            $plugins['blog'] = $this->getBlogPlugins();
        } else {
            unset($plugins['blog']);
        }
        if ($this->checkInstalledBlog('products') || $this->checkInstalledBlog('booking')) {
            $plugins['store'] = $this->getStorePlugins();
        } else {
            unset($plugins['store']);
        }
        foreach ($items as $item) {
            if ($item->title == 'ba-instagram') {
                continue;
            }
            $plugins[$item->type][$item->title] = $item;
        }
        if (isset(gridboxHelper::$systemApps->comments) && $edit_type != 'blog') {
            $comments = new stdClass();
            $comments->title = 'ba-comments-box';
            $comments->image = 'plugins-comments-box';
            $comments->type = 'social';
            $comments->joomla_constant = 'COMMENTS_BOX';
            $plugins['social'][] = $comments;
        }
        if (isset(gridboxHelper::$systemApps->comments)) {
            $comments = new stdClass();
            $comments->title = 'ba-recent-comments';
            $comments->image = 'plugins-recent-comments';
            $comments->type = 'social';
            $comments->joomla_constant = 'RECENT_COMMENTS';
            $plugins['social'][] = $comments;
        }
        if (isset(gridboxHelper::$systemApps->reviews) && $edit_type != 'blog') {
            $reviews = new stdClass();
            $reviews->title = 'ba-reviews';
            $reviews->image = 'plugins-reviews';
            $reviews->type = 'social';
            $reviews->joomla_constant = 'REVIEWS';
            $plugins['social'][] = $reviews;
        }
        if (isset(gridboxHelper::$systemApps->reviews)) {
            $reviews = new stdClass();
            $reviews->title = 'ba-recent-reviews';
            $reviews->image = 'plugins-recent-reviews';
            $reviews->type = 'social';
            $reviews->joomla_constant = 'RECENT_REVIEWS';
            $plugins['social'][] = $reviews;
        }
        if (isset(gridboxHelper::$systemApps->comments) || isset(gridboxHelper::$systemApps->reviews)) {
            usort($plugins['social'], function($a, $b){
                if ($a->joomla_constant == $b->joomla_constant) {
                    return 0;
                }
                return ($a->joomla_constant < $b->joomla_constant) ? -1 : 1;
            });
        }

        return $plugins;
    }

    public function getStorePlugins()
    {
        $plugins = [
            'cart' => 'plugins-cart',
            'add-to-cart' => 'plugins-add-to-cart',
            'product-slideshow' => 'plugins-slideshow',
            'product-gallery' => 'flaticon-photo-camera-1',
            'wishlist' => 'flaticon-like-2',
            'store-search' => 'flaticon-search',
            'recently-viewed-products' => 'flaticon-television',
            'currency-switcher' => 'plugins-currency-switcher'
        ];
        $store = [];
        foreach ($plugins as $plugin => $image) {
            $obj = new stdClass();
            $obj->title = 'ba-'.$plugin;
            $obj->image = $image;
            $obj->type = 'store';
            $obj->joomla_constant = strtoupper(str_replace('-', '_', $plugin));
            $store[$obj->title] = $obj;
        }

        return $store;
    }

    public function getBlogPlugins()
    {
        $plugins = ['tags', 'categories', 'recent-posts', 'search', 'recent-posts-slider', 'event-calendar',
            'fields-filter', 'google-maps-places'];
        $icons = ['flaticon-bookmark', 'flaticon-folder-13', 'flaticon-calendar-6', 'flaticon-search',
            'flaticon-tabs', 'flaticon-calendar-1', 'flaticon-checked', 'plugins-google-maps'];
        $blog = [];
        while ($plugin = array_pop($plugins)) {
            $obj = new stdClass();
            $obj->title = 'ba-'.$plugin;
            $obj->image = array_pop($icons);
            $obj->type = 'blog';
            $obj->joomla_constant = strtoupper(str_replace('-', '_', $plugin));
            if ($plugin == 'recent-posts-slider') {
                $obj->joomla_constant = 'POST_SLIDER';
            } else if ($plugin == 'fields-filter') {
                $obj->joomla_constant = 'CONTENT_FILTERS';
            }
            $blog[$obj->title] = $obj;
        }

        return $blog;
    }

    public function getMenus()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('menutype, title')
            ->from('#__menu_types');
        $db->setQuery($query);
        $menus = $db->loadObjectList();
        
        return $menus;
    }

    public function getAllApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, id, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $obj = new stdClass();
        $obj->title = JText::_('PAGES');
        $obj->id = 0;
        $obj->type = 'single';
        $array = [$obj];
        $array = array_merge($array, $items);

        return $array;
    }

    public function getApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, id, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('type <> '.$db->quote('single'));
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

    public function getCategories()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, id, app_id')
            ->from('#__gridbox_categories')
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }
    
    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }

    public function getJce()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('enabled')
            ->from('`#__extensions`')
            ->where('`element` = '.$db->quote('jce'))
            ->where('`folder` = '.$db->quote('editors'));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }
}