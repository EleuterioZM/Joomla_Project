<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class gridboxControllerEditor extends JControllerForm
{
    public function getModel($name = 'Editor', $prefix = 'gridboxModel', $config = [])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

    public function setModalSettings():void
    {
        $service = $this->input->get('service', '', 'string');
        $key = $this->input->get('key', '{}', 'string');
        gridboxHelper::setModalSettings($service, $key);
        exit;
    }

    public function CKEThemeRules()
    {
        $db = JFactory::getDbo();
        $id = $this->input->get('id', 0, 'int');
        $query = $db->getQuery(true)
            ->select('params')
            ->from('#__template_styles')
            ->where('id = ' .$db->quote($id));
        $db->setQuery($query);
        $str = $db->loadResult();
        $params = json_decode($str);
        $theme = $params->params;
        
        $str = 'html body {';
        foreach ($theme->colorVariables as $key => $value) {
            $str .= str_replace('@', '--', $key).': '.$value->color.';';
        }
        $str .= '}';
        gridboxHelper::prepareParentFonts($theme);
        $str .= gridboxHelper::$css->setMediaRules($theme, false, 'createTypography');
        print_r($str);exit;
        exit;
    }

    public function submitNewItem()
    {
        $post = $this->input->post->getArray([]);
        $model = $this->getModel();
        $model->submitNewItem($post);
        print_r($post);exit;
    }

    public function renameVersionsHistory()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'string');
        $model = $this->getModel();
        $model->renameVersionsHistory($id, $title);
        exit;
    }

    public function deleteVersionsHistory()
    {
        $id = $this->input->get('id', 0, 'int');
        $page_id = $this->input->get('page_id', 0, 'int');
        $model = $this->getModel();
        $model->deleteVersionsHistory($id);
        $versions = $model->getVersionsHistory($page_id);
        $str = json_encode($versions);
        echo $str;exit();
    }

    public function getVersionsHistory()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $versions = $model->getVersionsHistory($id);
        $str = json_encode($versions);
        echo $str;exit();
    }

    public function getCountdownTimes()
    {
        $config = JFactory::getConfig();
        $end = $this->input->get('end', '', 'raw');
        $offset = $config->get('offset');
        $tz = new DateTimeZone($offset);
        $nowDate = new DateTime('now', $tz);
        $endDate = new DateTime($end, $tz);        
        $obj = new stdClass();
        $obj->start = $nowDate->getTimestamp();
        $obj->end = $endDate->getTimestamp();
        $str = json_encode($obj);
        echo $str;exit;
    }

    public function getDefaultsSeo()
    {
        $id = $this->input->get('id', 0, 'int');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel();
        $seo = $model->getDefaultsSeo($id, $type);
        $str = json_encode($seo);
        echo $str;
        exit;
    }

    public function setDefaultsSeo()
    {
        $seo = (object)[
            'id' => $this->input->get('id', 0, 'int'),
            'item_id' => $this->input->get('item_id', 0, 'int'),
            'item_type' => $this->input->get('item_type', '', 'string'),
            'meta_title' => $this->input->get('meta_title', '', 'string'),
            'meta_description' => $this->input->get('meta_description', '', 'string'),
            'share_image' => $this->input->get('share_image', '', 'string'),
            'share_title' => $this->input->get('share_title', '', 'string'),
            'share_description' => $this->input->get('share_description', '', 'string'),
            'sitemap_include' => $this->input->get('sitemap_include', '', 'string'),
            'changefreq' => $this->input->get('changefreq', '', 'string'),
            'priority' => $this->input->get('priority', '', 'string'),
            'schema_markup' => $this->input->get('schema_markup', '', 'raw')
        ];
        $model = $this->getModel();
        $model->setDefaultsSeo($seo);
        exit;
    }

    public function getUserGroups()
    {
        $groups = gridboxHelper::getUserGroups();
        $str = json_encode($groups);
        echo $str;exit;
    }

    public function checkEventField()
    {
        $response = new stdClass();
        $response->flag = gridboxHelper::checkEventField();
        $str = json_encode($response);
        echo $str;exit();
    }

    public function checkAppFields()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $type = $model->checkAppFields($id);
        echo $type;exit();
    }

    public function getProductOptions()
    {
        $model = $this->getModel();
        $array = $model->getProductOptions();
        $str = json_encode($array);
        echo $str;
        exit();
    }

    public function generateNewApp()
    {
        $input = JFactory::getApplication()->input;
        $name = $input->get('name', 'test', 'string');
        $path = JPATH_ROOT.'/tmp/'.$name;
        $xml = simplexml_load_file(JPATH_ROOT.'/tmp/'.$name.'.xml');
        if (JFolder::exists($path)) {
            gridboxHelper::deleteFolder($path);
        }
        JFolder::create($path);
        foreach ($xml->apps->app as $app) {
            $obj = json_decode($app);
            $file = 'app.html';
            JFile::write($path.'/'.$file, $obj->app_layout);
            $file = 'app.json';
            JFile::write($path.'/'.$file, $obj->app_items);
            $file = 'default.html';
            JFile::write($path.'/'.$file, $obj->page_layout);
            $file = 'default.json';
            JFile::write($path.'/'.$file, $obj->page_items);
            $file = 'fields-groups.json';
            JFile::write($path.'/'.$file, $obj->fields_groups);
        }
        $obj = new stdClass();
        $obj->fields = array();
        $obj->fields_data = array();
        foreach ($xml->fields->field as $field) {
            $object = json_decode($field);
            $obj->fields[] = $object;
        }
        foreach ($xml->fields_data->field_data as $field_data) {
            $object = json_decode($field_data);
            $obj->fields_data[] = $object;
        }
        $str = json_encode($obj);
        $file = 'fields.json';
        JFile::write($path.'/'.$file, $str);
        echo 'created';
        exit;
    }

    public function getSubmissionForm()
    {
        $id = $this->input->post->get('id', 0, 'int');
        $order = $this->input->post->get('order', [], 'array');
        $fields = $this->input->post->get('fields', [], 'array');
        foreach ($fields as $key => $field) {
            $fields[$key] = (bool)intval($field);
        }
        $str = gridboxHelper::getSubmissionForm($id, $order, $fields);
        print_r($str);exit;
    }

    public function getAppFields()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->post->get('id', 0, 'int');
        $type = $input->post->get('type', '', 'string');
        $edit_type = $input->post->get('edit_type', '', 'string');
        if (($type == 'post-navigation'  || $type == 'related-posts') && $edit_type != 'post-layout') {
            $id = gridboxHelper::getAppId($id);
        }
        if ($type == 'submission-form') {
            $items = gridboxHelper::getSubmissionFields($id);
        } else {
            $items = gridboxHelper::getAppFilterFields($id);
        }
        $str = json_encode($items);
        echo $str;
        exit;
    }

    public function getItemsFilter()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->post->get('id', 0, 'int');
        $type = $input->post->get('type', '', 'string');
        $edit_type = $input->post->get('edit_type', '', 'string');
        if (($type == 'post-navigation'  || $type == 'related-posts') && $edit_type != 'post-layout') {
            $id = gridboxHelper::getAppId($id);
        }
        $str = gridboxHelper::getItemsFilter($id);
        echo $str;exit;
    }

    public function uploadSubmissionFile()
    {
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('file', [], 'array');
        $app_id = $input->post->get('app_id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->uploadSubmissionFile($file, $app_id);
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function uploadDesktopFieldFile()
    {
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('file', [], 'array');
        $id = $input->post->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->uploadDesktopFieldFile($file, $id);
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function removeDesktopFieldFile()
    {
        $id = $this->input->post->get('id', 0, 'int');
        $model = $this->getModel();
        $model->removeDesktopFieldFile($id);
        exit();
    }

    public function checkGridboxState()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();
        print_r($balbooa);exit();
    }

    public function checkSitemap()
    {
        if (isset(gridboxHelper::$systemApps->sitemap)) {
            gridboxHelper::checkSitemap();
        }
        exit;
    }

    public function generateSitemap()
    {
        $input = JFactory::getApplication()->input;
        gridboxHelper::$website->sitemap_domain = $input->get('sitemap_domain', '', 'string');
        gridboxHelper::$website->sitemap_slash = $input->get('sitemap_slash', 0, 'int');
        gridboxHelper::createSitemap();
        exit;
    }

    public function getMapsPlaces()
    {
        $input = JFactory::getApplication()->input;
        $app = $input->get('app', 0, 'int');
        $menuItem = $input->post->get('menuitem', 0, 'int');
        $pages = $input->post->get('pages', '', 'string');
        $obj = gridboxHelper::getMapsPlaces($app, $menuItem, $pages);
        $str = json_encode($obj);
        header('Content-Type: text/javascript');
        echo $str;
        exit;
    }

    public function renderEventCalendar()
    {
        $input = JFactory::getApplication()->input;
        $year = $input->get('year', '0', 'string');
        $month = $input->get('month', '0', 'string');
        $app = $input->get('app', 0, 'int');
        $category = $input->get('category', '', 'string');
        $tags = $input->get('tags', '', 'string');
        $type = $input->get('type', '', 'string');
        $start = $input->get('start', 0, 'int');
        $menuItem = $input->post->get('menuitem', 0, 'int');
        $time = mktime(0, 0, 0, $month, 1, $year);
        $obj = gridboxHelper::renderEventCalendarData($time, $app, $menuItem, $start, $type, $category, $tags);
        $str = json_encode($obj);
        header('Content-Type: text/javascript');
        echo $str;
        exit;
    }

    public function renderWeather()
    {
        $openWeatherMapKey = gridboxHelper::getIntegrationKey('openweathermap');
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', 'page', 'string');
        $placeholder = '';
        if ($view == 'gridbox') {
            $placeholder = '<div class="empty-list"><i class="zmdi zmdi-alert-polygon"></i><p>';
            $placeholder .= JText::_('ENTER_VALID_API_KEY_LOCATION').'</p></div>';
        }
        if (empty($openWeatherMapKey)) {
            print_r($placeholder);exit;
        }
        $string = $input->get('weather', '{}', 'string');
        $weather = json_decode($string);
        if (empty($weather->location)) {
            print_r($placeholder);exit;
        }
        $item = new stdClass();
        $item->weather = $weather;
        $units = $weather->unit == 'c' ? 'metric' : 'imperial';
        $latLon = explode(',', $weather->location);
        if (!empty($latLon) && count($latLon) == 2 && is_numeric($latLon[0])&& is_numeric($latLon[1])) {
            $url = 'http://api.openweathermap.org/data/2.5/forecast?lat='.trim($latLon[0]).'&lon='.trim($latLon[1]);
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        } else if (is_numeric($weather->location)) {
            $url = 'http://api.openweathermap.org/data/2.5/forecast?id='.$weather->location;
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        } else {
            $location = str_replace(' ', '%20', $weather->location);
            $url = 'http://api.openweathermap.org/data/2.5/forecast?q='.$location;
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        }
        $data = gridboxHelper::fetch($url);
        $weather = json_decode($data);
        if (!is_object($weather) || $weather->cod != 200) {
            print_r($placeholder);exit;
        }
        $forecast = gridboxHelper::renderWetherData($item->weather, $data);
        $str = gridboxHelper::renderWetherHTML($forecast, $item);
        print_r($str);exit;
    }

    public function setAppLicense()
    {
        gridboxHelper::setAppLicense('');
        header('Content-Type: text/javascript');
        echo 'var domainResponse = true;';
        exit();
    }

    public function setAppLicenseForm()
    {
        gridboxHelper::setAppLicense('');
        header('Location: https://www.balbooa.com/user/downloads/licenses');
        exit();
    }

    public function setAppLicenseBalbooa()
    {
        gridboxHelper::setAppLicenseBalbooa('');
        header('Content-Type: text/javascript');
        echo 'success';
        exit();
    }

    public function getAppLicense()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $data = $input->get('data', '', 'string');
        gridboxHelper::setAppLicense($data);
        gridboxHelper::setAppLicenseBalbooa($data);
        exit();
    }

    public function getDefaultElementsBox()
    {
        $defaultElementsBox = gridboxHelper::getDefaultElementsBox();
        header('Content-Type: text/javascript');
        $data = 'var defaultElementsBox = '.$defaultElementsBox.';';
        echo $data;exit;
    }

    public function reloadModules()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $type = $input->get('type', '', 'string');
        $model = $this->getModel();
        $str = $model->reloadModules($id, $type);
        echo $str;
        exit;
    }

    public function contentSliderAdd()
    {
        $model = $this->getModel();
        $str = $model->contentSliderAdd();
        echo $str;
        exit;
    }

    public function deleteMenuItem()
    {
        gridboxHelper::checkUserEditLevel();
        $ids = $this->input->get('id', array(), 'array');
        $parents = $this->input->get('parent_id', array(), 'array');
        $model = $this->getModel();
        $model->deleteMenuItem($ids, $parents);
        exit;
    }

    public function saveMenuItemTitle()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->saveMenuItemTitle();
        exit;
    }

    public function sortMenuItems()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->sortMenuItems();
        exit;
    }

    public function getSiteCssObjeck()
    {
        $obj = gridboxHelper::getSiteCssPaterns();
        $str = json_encode($obj);
        echo $str;exit;
    }

    public function setLibraryImage()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->setLibraryImage();
    }

    public function getPostNavigation()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'gridbox');
        $id = $input->get('id', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        gridboxHelper::$editItem = null;
        $model = $this->getModel();
        $model->setEditorView();
        $str = gridboxHelper::getPostNavigation($maximum, $id);
        echo $str;exit;
    }

    public function getRelatedPosts()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'gridbox');
        $id = $input->get('id', 0, 'int');
        $app = $input->get('app', 0, 'int');
        $related = $input->get('related', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $type = $input->get('type', '', 'string');
        $sorting = $input->get('sorting', 'created', 'string');
        gridboxHelper::$editItem = null;
        if ($type == 'slider') {
            gridboxHelper::$editItem = new stdClass();
            gridboxHelper::$editItem->type = 'related-posts-slider';
        }
        $model = $this->getModel();
        $model->setEditorView();
        $str = gridboxHelper::getRelatedPosts($app, $related, $limit, $maximum, $sorting, $id);
        echo $str;exit;
    }

    public function getRecentlyViewedProducts()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'gridbox');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        gridboxHelper::$editItem = new stdClass();
        gridboxHelper::$editItem->type = 'recently-viewed-products';
        $model = $this->getModel();
        $model->setEditorView();
        $str = gridboxHelper::getRecentlyViewedProducts($limit, $maximum);
        echo $str;exit;
    }

    public function getRecentPosts()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'gridbox');
        $id = $input->get('id', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $category = $input->get('category', '', 'string');
        $tags = $input->get('tags', '', 'string');
        $type = $input->get('type', '', 'string');
        $featured = $input->get('featured', false, 'bool');
        $pagination = $input->get('pagination', '', 'string');
        gridboxHelper::$editItem = null;
        $model = $this->getModel();
        $model->setEditorView();
        $obj = new stdClass();
        $obj->posts = gridboxHelper::getRecentPosts($id, $sorting, $limit, $maximum, $category, $featured, 0, '', $type, $tags);
        $obj->pagination = gridboxHelper::getRecentPostsPagination($id, $limit, $category, $featured, 0, $pagination, $type, $tags);
        $str = json_encode($obj);
        echo $str;exit;
    }

    public function getRecentComments()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $category = $input->get('category', '', 'string');
        gridboxHelper::$editItem = null;
        $str = gridboxHelper::getRecentComments($id, $sorting, $limit, $maximum, $category);
        echo $str;exit;
    }

    public function getRecentReviews()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $category = $input->get('category', '', 'string');
        gridboxHelper::$editItem = null;
        $str = gridboxHelper::getRecentReviews($id, $sorting, $limit, $maximum, $category);
        echo $str;exit;
    }

    public function getRecentPostsSlider()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'gridbox');
        $id = $input->get('id', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $category = $input->get('category', '', 'string');
        $tags = $input->get('tags', '', 'string');
        $type = $input->get('type', '', 'string');
        $featured = $input->get('featured', false, 'bool');
        gridboxHelper::$editItem = new stdClass();
        gridboxHelper::$editItem->type = 'recent-posts-slider';
        $model = $this->getModel();
        $model->setEditorView();
        $str = gridboxHelper::getRecentPosts($id, $sorting, $limit, $maximum, $category, $featured, 0, '', $type, $tags);
        echo $str;exit;
    }

    public function getBlogCategories()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $counter = $input->get('counter', 1, 'int');
        $sub = $input->get('sub', 1, 'int');
        $title = $input->get('title', 1, 'int');
        $img = $input->get('img', 1, 'int');
        $digital = gridboxHelper::getSubscriptionProducts();
        $items = gridboxHelper::getBlogCategories($id, 0, $counter == 1, $sub == 1, $digital);
        $str = gridboxHelper::getBlogCategoriesHtml($items, $maximum, false, $counter == 1, $title == 1, $img == 1);
        echo $str;exit;
    }

    public function getBlogTags()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $category = $input->get('category', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $str = gridboxHelper::getBlogTags($id, $category, $limit, $sorting);
        echo $str;exit;
    }

    public function getProductData()
    {
        $model = $this->getModel();
        $data = $model->getProductData();
        echo json_encode($data);
        exit;
    }

    public function getPageTags()
    {
        $model = $this->getModel();
        $data = $model->getTagsFolders();
        $data->page = $model->getPageTags();
        echo json_encode($data);
        exit;
    }

    public function checkProductTour()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->checkProductTour();
    }

    public function getUserAuthorisedLevels()
    {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        sort($groups);
        $obj = json_encode($groups);
        echo $obj;
        exit;
    }

    public function getLibraryItems()
    {
        $model = $this->getModel();
        $obj = $model->getLibraryItems();
        $obj->global = JText::_('GLOBAL_ITEM');
        $obj->delete = JText::_('DELETE');
        $obj = json_encode($obj);
        echo $obj;
        exit;
    }

    public function getBlogPosts()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $max = $input->get('max', 0, 'int');
        $limit = $input->get('limit', 0, 'int');
        $order = $input->get('order', 'created', 'string');
        $pagination = $input->get('pagination', 'created', 'pagination');
        $model = $this->getModel();
        $model->setEditorView();
        echo gridboxHelper::getBlogPosts($id, $max, $limit, 0, 0, $order, $pagination);
        exit;
    }
    
    public function getBlogPagination()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $max = $input->get('max', 0, 'int');
        $limit = $input->get('limit', 0, 'int');
        $pagination = $input->get('pagination', 'created', 'pagination');
        echo gridboxHelper::getBlogPagination($id, 0, $limit, 0, $pagination);
        exit;
    }

    public function getItems()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $theme = $input->get('theme', 0, 'int');
        $edit_type = $input->get('edit_type', '', 'string');
        $view = $input->get('view', '', 'string');
        $pageParams = gridboxHelper::getGridboxItems($id, $theme, $edit_type, $view);
        header('Content-Type: text/javascript');
        echo 'var gridboxItems = '.$pageParams;
        exit;
    }

    public function setStarRatings()
    {
        $model = $this->getModel();
        $result = $model->setStarRatings();
        echo json_encode($result);
        exit;
    }

    public function getLibrary()
    {
        $model = $this->getModel();
        $model->getLibrary();
    }

    public function requestAddLibrary()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->requestAddLibrary();
    }

    public function addLibrary()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->addLibrary();
    }

    public function removeLibrary()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->removeLibrary();
    }

    public function gridboxSaveVersion()
    {
        $data = file_get_contents('php://input');
        $obj = json_decode($data);
        $this->executeSaveVersion($obj);
    }

    public function gridboxAjaxSaveVersion()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('obj', '', 'raw');
        $obj = json_decode($data);
        $this->executeSaveVersion($obj);
    }

    public function gridboxSave()
    {
        $data = file_get_contents('php://input');
        $obj = json_decode($data);
        $this->executeSave($obj);
    }

    public function gridboxAjaxSave()
    {
        $input = JFactory::getApplication()->input;
        $data = $input->get('obj', '', 'raw');
        $obj = json_decode($data);
        $this->executeSave($obj);
    }

    public function executeSaveVersion($obj)
    {
        $model = $this->getModel();
        $model->saveVersion($obj);
        echo JText::_('GRIDBOX_SAVED');
        exit;
    }

    public function cleanupVersionsHistory()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.delete', 'com_gridbox')) {
            $model = $this->getModel();
            $model->cleanupVersionsHistory();
        }
        exit;
    }

    public function executeSave($obj)
    {
        $user = JFactory::getUser();
        if (!isset($obj->edit_type)) {
            $pageAssets = new gridboxAssetsHelper($obj->page->id, 'page');
            $editPage = $pageAssets->checkPermission('core.edit');
            if (!$editPage && !empty($obj->page->page_category)) {
                $editPage = $pageAssets->checkEditOwn($obj->page->page_category);
            }
            $editFlag = $editPage;
        } else if ($obj->edit_type == 'post-layout' || $obj->edit_type == 'blog') {
            $editFlag = $user->authorise('core.edit.layouts', 'com_gridbox.app.'.$obj->page->id);
        } else {
            $editFlag = $user->authorise('core.edit', 'com_gridbox');
        }
        if ($editFlag) {
            $model = $this->getModel();
            $model->gridboxSave($obj);
        } else {
            echo JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED');
            exit;
        }
    }

    public function duplicate()
    {
        $user = JFactory::getUser();
        $id = $this->input->get('id', 0, 'int');
        $edit_type = $this->input->get('edit_type', '', 'string');
        if ($user->authorise('core.duplicate', 'com_gridbox')) {
            $model = $this->getModel();
            $id = $model->duplicate($id);
        }
        print_r($id);exit;
    }

    public function checkMainMenu()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->checkMainMenu();
    }

    public function fetchPageBlock()
    {
        gridboxHelper::checkUserEditLevel();
        $obj = new stdClass();
        $obj->data = $this->input->get('data', '', 'raw');
        $obj->image = $this->input->get('image', '', 'raw');
        $obj->imageData = $this->input->get('imageData', '', 'raw');
        $obj->title = $this->input->get('title', '', 'raw');
        $obj->type = $this->input->get('type', '', 'raw');
        $obj->method = $this->input->get('method', '', 'raw');
        $model = $this->getModel();
        $model->getBlocksLicense($obj);
    }

    public function getBlocksLicense()
    {
        gridboxHelper::checkUserEditLevel();
        $str = file_get_contents('php://input');
        $data = json_decode($str);
        $model = $this->getModel();
        $model->getBlocksLicense($data);
    }

    public function getPluginLicense()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->getPluginLicense();
    }

    public function setNewMenuItem()
    {
        gridboxHelper::checkUserEditLevel();
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/menu.php';
        $gridboxMenu = new gridboxMenu();
        $parent = $this->input->get('parent', 1, 'int');
        $title = $this->input->get('title', '', 'string');
        $link = $this->input->get('link', '', 'string');
        $id = $this->input->get('id', 0, 'int');
        $menu = $gridboxMenu->getMenu($id);
        $menutype = $menu->menutype;
        $gridboxMenu->setMenuItem($parent, $title, $menutype, $link);
        exit;
    }

    public function setMenuItem()
    {
        gridboxHelper::checkUserEditLevel();
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/menu.php';
        $gridboxMenu = new gridboxMenu();
        $id = $this->input->get('id', 0, 'int');
        $parent = $this->input->get('parent', 1, 'int');
        $title = $this->input->get('title', '', 'string');
        $menutype = $this->input->get('menutype', '', 'string');
        $edit_type = $this->input->get('edit_type', '', 'string');
        if (empty($edit_type)) {
            $link = 'index.php?option=com_gridbox&view=page&id='.$id;
        } else if ($edit_type == 'blog') {
            $link = 'index.php?option=com_gridbox&view=blog&app='.$id.'&id=0';
        }
        $gridboxMenu->setMenuItem($parent, $title, $menutype, $link);
        exit;
    }

    public function getMenu()
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/menu.php';
        $gridboxMenu = new gridboxMenu();
        $id = $this->input->get('id', 0, 'int');
        $menu = $gridboxMenu->getMenu($id, true);
        echo json_encode($menu);
        exit;
    }

    public function getMenuItems()
    {
        gridboxHelper::checkUserEditLevel();
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/menu.php';
        $gridboxMenu = new gridboxMenu();
        $menutype = $this->input->get('menutype', '', 'string');
        $items = $gridboxMenu->getMenuItems($menutype);
        echo json_encode($items);
        exit;
    }

    public function loadModule()
    {
        header('Content-Type: text/javascript');
        echo gridboxHelper::loadModule();
        exit;
    }

    public function loadLayout()
    {
        $model = $this->getModel();
        $model->loadLayout();
    }

    public function loadPlugin()
    {
        $model = $this->getModel();
        $model->loadPlugin();
    }
}