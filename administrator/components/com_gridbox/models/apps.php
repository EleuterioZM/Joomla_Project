<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;

class gridboxModelApps extends JModelList
{
    public $appType;

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'published', 'theme', 'state', 'page_category', 'created', 'hits', 'order_list', 'author'
            );
        }
        $this->appType = $this->getType();
        $this->context = strtolower('com_gridbox.'.$this->getName().'.'.$this->appType);
        parent::__construct($config);
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
        $data->tags = gridboxHelper::getTags();
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

    public function getAppCells($id, $pks)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(p.id) AS count')
            ->from('#__gridbox_store_product_data AS d')
            ->where('d.product_type = '.$db->quote('digital'))
            ->where('a.id = '.$id)
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('p.id IN ('.$str.')');
        }
        $db->setQuery($query);
        $digital = $db->loadResult();
        $query = $db->getQuery(true)
            ->select('COUNT(p.id) AS count')
            ->from('#__gridbox_store_product_data AS d')
            ->where('d.product_type <> '.$db->quote('digital'))
            ->where('a.id = '.$id)
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('p.id IN ('.$str.')');
        }
        $db->setQuery($query);
        $physical = $db->loadResult();
        $cells = $this->getCSVAppCells();
        if ($digital == 0) {
            unset($cells['link']);
            unset($cells['expire']);
            unset($cells['max_downloads']);
        }
        if ($physical == 0) {
            unset($cells['options']);
            unset($cells['variation']);
            unset($cells['extra_options']);
            unset($cells['default_variation']);
            unset($cells['default_extra']);
            unset($cells['stock']);
            unset($cells['weight']);
            unset($cells['min']);
        }
        $obj = new stdClass();
        $obj->cells = $cells;
        $obj->fields = $this->getAppFields($id);
        $str = json_encode($obj);
        print_r($str);exit();
    }

    public function getCSVData($file)
    {
        $handle = fopen($file, "r");
        $data = array();
        while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
        JFile::delete($file);

        return $data;
    }

    public function getCSVResponse()
    {
        $response = new stdClass();
        $response->new = $response->updated = $response->errors = 0;
        $response->log = array();

        return $response;
    }

    public function checkRequiredCSVColumns($cells, $response = null)
    {
        $required = $this->getRequiredAppCells();
        $errors = 0;
        foreach ($required as $key => $cell) {
            if (!isset($cells[$key])) {
                $errors++;
                $this->raiseCsvError(1, $cell, 'REQUIRED_COLUMN_NOT_PRESENT', $response);
            }
        }
        if ($response && $response->errors != 0) {
            $this->submitCSVAnswer($response);
        }

        return $errors;
    }

    public function getMatchedCells($map, $matched)
    {
        $cells = $this->getCSVAppCells();
        foreach ($cells as $key => $cell) {
            if (!isset($matched->{$key})) {
                unset($cells[$key]);
            } else {
                $map->{$key} = $matched->{$key} * 1;
            }
        }

        return $cells;
    }

    public function getMatchedFields($id, $map, $matched)
    {
        $fields = $this->getAppFields($id);
        foreach ($fields as $key => $field) {
            if (!isset($matched->{$field->id})) {
                unset($fields->{$key});
            } else {
                $map->{$field->id} = $matched->{$field->id} * 1;
            }
        }

        return $fields;
    }

    public function getGridboxCells($map, $keys)
    {
        $cells = $this->getCSVAppCells();
        foreach ($cells as $key => $cell) {
            if (!in_array($cell, $keys)) {
                unset($cells[$key]);
            } else {
                $map->{$key} = array_search($cell, $keys);
            }
        }

        return $cells;
    }

    public function getGridboxFields($id, $map, $keys)
    {
        $fields = $this->getAppFields($id);
        foreach ($fields as $key => $field) {
            if (!in_array($field->title, $keys)) {
                unset($fields->{$key});
            } else {
                $map->{$field->id} = array_search($field->title, $keys);
            }
        }

        return $fields;
    }

    public function importCSV($file, $id, $overwrite, $matched, $type, $category)
    {
        $data = $this->getCSVData($file);
        $map = new stdClass();
        if ($type == 'gridbox') {
            $keys = $data[0];
            $cells = $this->getGridboxCells($map, $keys);
            $fields = $this->getGridboxFields($id, $map, $keys);
        } else {
            $cells = $this->getMatchedCells($map, $matched);
            $fields = $this->getMatchedFields($id, $map, $matched);
        }
        $errors = $this->checkRequiredCSVColumns($cells);
        if ($errors == 0) {
            $db = JFactory::getDbo();
            $products = array();
            $n = count($data);
            for ($i = 1; $i < $n; $i++) {
                $j = $map->{'product_type'};
                $product_type = $data[$i][$j];
                switch ($product_type) {
                    case '':
                    case 'Product':
                    case 'Digital Product':
                        $product = new stdClass();
                        $product->item = new stdClass();
                        $product->fields = array();
                        $product->data = $data[$i];
                        $product->product_type = !empty($product_type) ? $product_type : 'Product';
                        $product->options = array();
                        $product->variations = array();
                        $product->extra_options = array();
                        $products[] = $product;
                        break;
                    case 'Option':
                    case 'Variation':
                    case 'Extra Options':
                        if ($product_type == 'Option') {
                            $key = 'options';
                        } else if ($product_type == 'Variation') {
                            $key = 'variations';
                        } else {
                            $key = 'extra_options';
                        }
                        $product = end($products);
                        if ($product) {
                            $obj = new stdClass();
                            $obj->data = $data[$i];
                            $product->{$key}[] = $obj;
                        }
                        break;
                }
            }
            $expires = ['Hours' => 'h', 'Days' => 'd', 'Months' => 'm', 'Year' => 'y'];
            $tags = [];
            $badges = [];
            $categories = [];
            $unpublished = null;
            foreach ($products as $product) {
                foreach ($cells as $key => $cell) {
                    $i = $map->{$key};
                    $value = $product->data[$i];
                    if ($key == 'id' && ($value == '' || (is_numeric($value) && is_int($value * 1)))) {
                        if ($overwrite == 1 && is_numeric($value) && is_int($value * 1) && $value != 0) {
                            $query = $db->getQuery(true)
                                ->select('COUNT(id)')
                                ->from('#__gridbox_pages')
                                ->where('id = '.$value)
                                ->where('app_id = '.$id);
                            $db->setQuery($query);
                            $count = $db->loadResult();
                            $value = $count == 0 ? 0 : $value;
                        } else {
                            $value = 0;
                        }
                    }
                    switch ($key) {
                        case 'id':
                        case 'min':
                        case 'stock':
                            if ($value != '' && (!is_numeric($value) || !is_int($value * 1))) {
                                $value = '';
                            }
                            break;
                        case 'price':
                        case 'sale_price':
                        case 'weight':
                            if ($value != '' && !is_numeric($value)) {
                                $value = '';
                            }
                            break;
                        case 'max_downloads':
                            if ($product->product_type == 'Digital Product' && $value != '' && !is_numeric($value)) {
                                $value = '';
                            }
                            break;
                        case 'expire':
                            if ($product->product_type == 'Digital Product' && $value != '') {
                                $expire = explode(' / ', $value);
                                if (count($expire) != 2 || !is_numeric($expire[0]) || !isset($expires[$expire[1]])) {
                                    $value = '';
                                }
                            }
                            break;
                        case 'published':
                            if ($value == '' || ($value != 'TRUE' && $value != 'FALSE')) {
                                $value = 'TRUE';
                            }
                            break;
                        case 'category':
                            if ($value != '' && !isset($categories[$value])) {
                                $query = $db->getQuery(true)
                                    ->select('id')
                                    ->from('#__gridbox_categories')
                                    ->where('title = '.$db->quote($value))
                                    ->where('app_id = '.$id);
                                $db->setQuery($query);
                                $categories[$value] = $db->loadResult();
                            }
                            if (!$categories[$value] && !$unpublished) {
                                $query = $db->getQuery(true)
                                    ->select('id')
                                    ->from('#__gridbox_categories')
                                    ->where('title = '.$db->quote('Uncategorised'))
                                    ->where('app_id = '.$id);
                                $db->setQuery($query);
                                $cat_id = $db->loadResult();
                                $unpublished = $cat_id ? $cat_id : $category->createCat('Uncategorised', $id);
                            }
                            if (!$categories[$value]) {
                                $value = '';
                            } else {
                                $value = $categories[$value];
                            }
                            break;
                        case 'tags':
                            if ($value != '') {
                                $array = explode(' / ', $value);
                                $result = array();
                                foreach ($array as $tag) {
                                    if (!isset($tags[$tag])) {
                                        $query = $db->getQuery(true)
                                            ->select('*')
                                            ->from('#__gridbox_tags')
                                            ->where('title = '.$db->quote($tag));
                                        $db->setQuery($query);
                                        $tags[$tag] = $db->loadObject();
                                    }
                                    if ($tags[$tag]) {
                                        $result[] = $tag;
                                    }
                                }
                                $value = implode(' / ', $result);
                            }
                            break;
                        case 'badges':
                            if ($value != '') {
                                $array = explode(' / ', $value);
                                $result = array();
                                foreach ($array as $badge) {
                                    if ($badge == '%' && !isset($badges[$badge])) {
                                        $query = $db->getQuery(true)
                                            ->select('*')
                                            ->from('#__gridbox_store_badges')
                                            ->where('type = '.$db->quote('sale'));
                                        $db->setQuery($query);
                                        $badges[$badge] = $db->loadObject();
                                    } else if (!isset($badges[$badge])) {
                                        $query = $db->getQuery(true)
                                            ->select('*')
                                            ->from('#__gridbox_store_badges')
                                            ->where('title = '.$db->quote($badge));
                                        $db->setQuery($query);
                                        $badges[$badge] = $db->loadObject();
                                    }
                                    if ($badges[$badge]) {
                                        $result[] = $badge;
                                    }
                                }
                                $value = implode(' / ', $result);
                            }
                            break;
                    }
                    $product->item->{$key} = $value;
                }
                foreach ($fields as $field) {
                    $i = $map->{$field->id};
                    $value = $product->data[$i];
                    switch ($field->field_type) {
                        case 'price':
                        case 'number':
                        case 'range':
                            if ($value != '' && !is_numeric($value)) {
                                $value = 0;
                            }
                            break;
                        case 'date':
                        case 'event-date':
                            if ($value != '' && !DateTime::createFromFormat('Y-m-d', $value)) {
                                $value = '';
                            }
                            break;
                        case 'field-video':
                            if ($value != '') {
                                $array = explode('; ', $value);
                                $video = array('Source File', 'Youtube', 'Vimeo');
                                if (count($array) != 2 || !in_array($array[0], $video)) {
                                    $value = '';
                                }
                            }
                            break;
                        case 'time':
                            if ($value != '') {
                                $array = explode(' : ', $value);
                                if (count($array) != 2) {
                                    $value = '';
                                }
                            }
                            break;
                        case 'url':
                            if ($value != '') {
                                $array = explode('; ', $value);
                                if (count($array) != 2) {
                                    $value = '';
                                }
                            }
                            break;
                        case 'radio':
                        case 'select':
                            if ($value != '') {
                                $flag = false;
                                foreach ($field->params->items as $item) {
                                    if ($item->title == $value) {
                                        $flag = true;
                                        break;
                                    }
                                }
                                if (!$flag) {
                                    $value = '';
                                }
                            }
                            break;
                        case 'checkbox':
                            if ($value != '') {
                                $values = explode(' / ', $value);
                                $array = array();
                                $result = array();
                                foreach ($field->params->items as $item) {
                                    $array[] = $item->title;
                                }
                                foreach ($values as $title) {
                                    if (in_array($title, $array)) {
                                        $result[] = $title;
                                    }
                                }
                                $value = implode(' / ', $result);
                            }
                            break;
                    }
                    $obj = new stdClass();
                    $obj->value = $value;
                    $obj->field_type = $field->field_type;
                    $obj->params = $field->params;
                    $obj->id = $field->id;
                    $product->fields[] = $obj;
                }
            }
            $page_id = 0;
            $theme_id = 0;
            $now = time();
            foreach ($products as $product) {
                $item = $product->item;
                $page = new stdClass();
                if (empty($item->id) && empty($page_id)) {
                    $query = $db->getQuery(true)
                        ->select('MAX(id)')
                        ->from('#__gridbox_pages');
                    $db->setQuery($query);
                    $page_id = $db->loadResult();
                    $query = $db->getQuery(true)
                        ->select('theme')
                        ->from('#__gridbox_app')
                        ->where('id = '.$id);
                    $db->setQuery($query);
                    $theme_id = $db->loadResult();
                }
                if (empty($item->id)) {
                    $page->id = ++$page_id;
                    $page->style = '{}';
                    $page->app_id = $id;
                    $page->created = JHtml::date($now++, "Y-m-d H:i:s");
                    $page->theme = $theme_id;
                } else {
                    $page->id = $item->id;
                }
                $page->title = !empty($item->title) ? $item->title : 'Product_'.$page->id;
                $alias = !empty($item->page_alias) ? $item->page_alias : $page->title;
                $page->page_alias = gridboxHelper::getAlias($alias, '#__gridbox_pages', $page->id, 'page_alias');
                if (isset($item->category)) {
                    $page->page_category = !empty($item->category) ? $item->category : $unpublished;
                }
                if (isset($item->image)) {
                    $page->intro_image = $item->image;
                }
                if (isset($item->meta_title)) {
                    $page->meta_title = $item->meta_title;
                }
                if (isset($item->meta_description)) {
                    $page->meta_description = $item->meta_description;
                }
                if (isset($item->intro_text)) {
                    $page->intro_text = $item->intro_text;
                }
                if (isset($item->published)) {
                    $page->published = $item->published == 'TRUE' ? 1 : 0;
                } else if (empty($item->id)) {
                    $page->published = 1;
                }
                if (!empty($item->id)) {
                    $db->updateObject('#__gridbox_pages', $page, 'id');
                } else {
                    $db->insertObject('#__gridbox_pages', $page);
                    $page->id = $db->insertid();
                }
                
                if (isset($item->tags)) {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_tags_map')
                        ->where('page_id = '.$page->id);
                    $db->setQuery($query);
                    $tags_map = $db->loadObjectList();
                    $array = !empty($item->tags) ? explode(' / ', $item->tags) : array();
                    $tags_pks = array();
                    $exists = array();
                    foreach ($array as $title) {
                        $tags_pks[] = $tags[$title]->id;
                    }
                    foreach ($tags_map as $obj) {
                        if (!in_array($obj->tag_id, $tags_pks)) {
                            $query = $db->getQuery(true)
                                ->delete('#__gridbox_tags_map')
                                ->where('id = '.$obj->id);
                            $db->setQuery($query)
                                ->execute();
                        } else {
                            $exists[] = $obj->tag_id;
                        }
                    }
                    foreach ($tags_pks as $tag_id) {
                        if (!in_array($tag_id, $exists)) {
                            $obj = new stdClass();
                            $obj->tag_id = $tag_id;
                            $obj->page_id = $page->id;
                            $db->insertObject('#__gridbox_tags_map', $obj);
                        }
                    }
                }
                if (isset($item->badges)) {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_store_badges_map')
                        ->where('product_id = '.$page->id);
                    $db->setQuery($query);
                    $badges_map = $db->loadObjectList();
                    $array = !empty($item->badges) ? explode(' / ', $item->badges) : array();
                    $badges_pks = array();
                    $exists = array();
                    foreach ($array as $title) {
                        $badges_pks[] = $badges[$title]->id;
                    }
                    foreach ($badges_map as $obj) {
                        if (!in_array($obj->badge_id, $badges_pks)) {
                            $query = $db->getQuery(true)
                                ->delete('#__gridbox_store_badges_map')
                                ->where('id = '.$obj->id);
                            $db->setQuery($query)
                                ->execute();
                        } else {
                            $exists[] = $obj->badge_id;
                        }
                    }
                    foreach ($badges_pks as $badge_id) {
                        if (!in_array($badge_id, $exists)) {
                            $obj = new stdClass();
                            $obj->badge_id = $badge_id;
                            $obj->product_id = $page->id;
                            $db->insertObject('#__gridbox_store_badges_map', $obj);
                        }
                    }
                }
                foreach ($product->fields as $field) {
                    $value = '';
                    switch ($field->field_type) {
                        case 'product-gallery':
                        case 'field-simple-gallery':
                            if (!empty($field->value)) {
                                $array = explode('; ', $field->value);
                                $images = array();
                                foreach ($array as $key => $v) {
                                    if ($key % 2 == 0) {
                                        $image = new stdClass();
                                        $image->img = $this->checkDesktopFieldFiles($v, $item->id);
                                        $image->alt = '';
                                        $images[] = $image;
                                    } else {
                                        $image = end($images);
                                        $image->alt = $v;
                                    }
                                }
                                $value = json_encode($images);
                            }
                            break;
                        case 'textarea':
                        case 'event-date':
                        case 'text':
                        case 'price':
                        case 'number':
                        case 'date':
                        case 'range':
                            $value = $field->value;
                            break;
                        case 'field-slideshow':
                        case 'product-slideshow':
                            if (!empty($field->value)) {
                                $array = explode('; ', $field->value);
                                $images = array();
                                foreach ($array as $key => $v) {
                                    $image = new stdClass();
                                    $image->img = $this->checkDesktopFieldFiles($v, $item->id);
                                    $image->alt = '';
                                    $images[] = $image;
                                }
                                $value = json_encode($images);
                            }
                            break;
                        case 'image-field':
                            if (!empty($field->value)) {
                                $array = explode('; ', $field->value);
                                $image = new stdClass();
                                $image->src = $this->checkDesktopFieldFiles($array[0], $item->id);
                                $image->alt = isset($array[1]) ? $array[1] : '';
                                $value = json_encode($image);
                            }
                            break;
                        case 'field-video':
                            if (!empty($field->value)) {
                                $array = explode('; ', $field->value);
                                $video = new stdClass();
                                $video->file = $video->id = '';
                                $video->type = $array[0] == 'Source File' ? 'source' : strtolower($array[0]);
                                if ($video->type == 'source') {
                                    $video->file = $this->checkDesktopFieldFiles($array[1], $item->id);
                                } else {
                                    $video->id = $array[1];
                                }
                                $value = json_encode($video);
                            }
                            break;
                        case 'file':
                            $value = $this->checkDesktopFieldFiles($field->value, $item->id);
                            break;
                        case 'time':
                            if (!empty($field->value)) {
                                $array = explode(' : ', $field->value);
                                $time = new stdClass();
                                $time->hours = $time->minutes = $time->format = '';
                                $time->hours = $array[0];
                                if (strpos($array[1], ' ')) {
                                    $array = explode(' ', $array[1]);
                                    $time->minutes = $array[0];
                                    $time->format = $array[1];
                                } else {
                                    $time->minutes = $array[1];
                                }
                                $value = json_encode($time);
                            }
                            break;
                        case 'url':
                            if (!empty($field->value)) {
                                $array = explode('; ', $field->value);
                                $url = new stdClass();
                                $url->label = $array[0];
                                $url->link = $array[1];
                                $value = json_encode($url);
                            }
                            break;
                        case 'radio':
                        case 'select':
                            foreach ($field->params->items as $obj) {
                                if ($obj->title == $field->value) {
                                    $value = $obj->key;
                                    break;
                                }
                            }
                            break;
                        case 'checkbox':
                            $array = explode(' / ', $field->value);
                            $checkbox = array();
                            foreach ($field->params->items as $obj) {
                                if (in_array($obj->title, $array)) {
                                    $checkbox[] = $obj->key;
                                }
                            }
                            $value = json_encode($checkbox);
                            break;
                    }
                    $data = $this->getFieldsData($page->id, $field->id);
                    if ($data) {
                        $data->value = $value;
                        $db->updateObject('#__gridbox_page_fields', $data, 'id');
                    } else {
                        $data = new stdClass();
                        $data->page_id = $page->id;
                        $data->field_id = $field->id;
                        $data->field_type = $field->field_type;
                        $data->value = $value;
                        $db->insertObject('#__gridbox_page_fields', $data);
                    }
                }                
                $productFields = [];
                $order_group = 0;
                $vars = [];
                if (isset($map->{'options'}) && isset($map->{'variation'})) {
                    foreach ($product->options as $option) {
                        $i = $map->{'options'};
                        $value = $option->data[$i];
                        $array = explode(' / ', $value);
                        if (count($array) == 2) {
                            $title = $array[0];
                            if (!isset($productFields[$title])) {
                                $query = $db->getQuery(true)
                                    ->select('*')
                                    ->from('#__gridbox_store_products_fields')
                                    ->where('title = '.$db->quote($title));
                                $db->setQuery($query);
                                $obj = $db->loadObject();
                                $productFields[$title] = $obj;
                            }
                            if (isset($productFields[$title]->id)) {
                                $obj = $productFields[$title];
                                if (!isset($obj->i)) {
                                    $obj->items = array();
                                    $obj->keys = array();
                                    $obj->i = 0;
                                    $obj->list = json_decode($obj->options);
                                    $obj->order_list = 0;
                                    $obj->order_group = $order_group++;
                                }
                                foreach ($obj->list as $li) {
                                    if ($li->title == $array[1]) {
                                        $productFields[$title]->items[] = $li->title;
                                        $productFields[$title]->keys[] = $li->key;
                                        $query = $db->getQuery(true)
                                            ->select('*')
                                            ->from('#__gridbox_store_product_variations_map')
                                            ->where('product_id = '.$page->id)
                                            ->where('field_id = '.$obj->id)
                                            ->where('option_key = '.$li->key);
                                        $db->setQuery($query);
                                        $var = $db->loadObject();
                                        if (!$var) {
                                            $var = new stdClass();
                                            $var->field_id = $obj->id;
                                            $var->option_key = $li->key;
                                            $var->product_id = $page->id;
                                        }
                                        $var->order_list = $obj->order_list++;
                                        $var->order_group = $obj->order_group;
                                        if (isset($map->{'image'})) {
                                            $i = $map->{'image'};
                                            $value = $option->data[$i];
                                            if (!empty($value)) {
                                                $array = explode('; ', $value);
                                                $var->images = json_encode($array);
                                            }
                                        }
                                        if (isset($var->id)) {
                                            $db->updateObject('#__gridbox_store_product_variations_map', $var, 'id');
                                        } else {
                                            $db->insertObject('#__gridbox_store_product_variations_map', $var);
                                            $var->id = $db->insertid();
                                        }
                                        $vars[] = $var->id;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_store_product_variations_map')
                    ->where('product_id = '.$page->id);
                if (!empty($vars)) {
                    $str = implode(', ', $vars);
                    $query->where('id NOT IN ('.$str.')');
                }
                $variations = new stdClass();
                if (isset($map->{'options'}) && isset($map->{'variation'})) {
                    $rows = [];
                    foreach ($productFields as $productField) {
                        $rows[] = $productField;
                    }
                    foreach ($product->variations as $variation) {
                        $i = $map->{'variation'};
                        $value = $variation->data[$i];
                        $array = explode(' / ', $value);
                        $exists = count($array) != 0;
                        $keys = [];
                        foreach ($array as $i => $variable) {
                            if (($ind = array_search($variable, $rows[$i]->items)) === false) {
                                $exists = false;
                            } else {
                                $keys[] = $rows[$i]->keys[$ind];
                            }
                        }
                        if ($exists) {
                            $key = implode('+', $keys);
                            $obj = new stdClass();
                            $i = isset($map->{'price'}) ? $map->{'price'} : null;
                            $obj->price = is_numeric($i) ? $variation->data[$i] : '';
                            $i = isset($map->{'sale_price'}) ? $map->{'sale_price'} : null;
                            $obj->sale_price = is_numeric($i) ? $variation->data[$i] : '';
                            $i = isset($map->{'sku'}) ? $map->{'sku'} : null;
                            $obj->sku = is_numeric($i) ? $variation->data[$i] : '';
                            $i = isset($map->{'stock'}) ? $map->{'stock'} : null;
                            $obj->stock = is_numeric($i) ? $variation->data[$i] : '';
                            $i = isset($map->{'weight'}) ? $map->{'weight'} : null;
                            $obj->weight = is_numeric($i) ? $variation->data[$i] : '';
                            $i = isset($map->{'default_variation'}) ? $map->{'default_variation'} : null;
                            $obj->default = is_numeric($i) ? ($variation->data[$i] == 'TRUE') : false;
                            $variations->{$key} = $obj;
                        }
                    }
                }
                $extra_options = new stdClass();
                if (isset($map->{'extra_options'})) {
                    $extras = [];
                    $ind = 0;
                    foreach ($product->extra_options as $extra_option) {
                        $i = $map->{'extra_options'};
                        $value = $extra_option->data[$i];
                        $array = explode(' / ', $value);
                        if (count($array) != 2) {
                            continue;
                        }
                        $title = $array[0];
                        if (!isset($extras[$title])) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gridbox_store_products_fields')
                                ->where('title = '.$db->quote($title));
                            $db->setQuery($query);
                            $extras[$title] = $db->loadObject();
                            $extras[$title] ? $extras[$title]->ind = $ind++ : '';
                        }
                        if (!$extras[$title]) {
                            continue;
                        }
                        $object = $extras[$title];
                        if (!isset($extra_options->{$object->ind})) {
                            $obj = (object)[
                                'id' => $object->id,
                                'items' => new stdClass()
                            ];
                            $extra_options->{$object->ind} = $obj;
                        } else {
                            $obj = $extra_options->{$object->ind};
                        }
                        if (in_array($object->field_type, ['textarea', 'textinput', 'file'])) {
                            $extra = new stdClass();
                            $i = $map->price ?? null;
                            $extra->price = is_numeric($i) ? $extra_option->data[$i] : '';
                            $i = $map->weight ?? null;
                            $extra->weight = is_numeric($i) ? $extra_option->data[$i] : '';
                            $i = $map->default_extra ?? null;
                            $extra->default = is_numeric($i) ? ($extra_option->data[$i] == 'TRUE') : '';
                            $obj->items->{'0'} = $extra;
                        } else {
                            $list = json_decode($object->options);
                            foreach ($list as $li) {
                                if ($li->title == $array[1]) {
                                    $extra = new stdClass();
                                    $i = $map->price ?? null;
                                    $extra->price = is_numeric($i) ? $extra_option->data[$i] : '';
                                    $i = $map->weight ?? null;
                                    $extra->weight = is_numeric($i) ? $extra_option->data[$i] : '';
                                    $i = $map->default_extra ?? null;
                                    $extra->default = is_numeric($i) ? ($extra_option->data[$i] == 'TRUE') : '';
                                    $obj->items->{$li->key} = $extra;
                                    break;
                                }
                            }
                        }
                    }
                }
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_product_data')
                    ->where('product_id = '.$page->id);
                $db->setQuery($query);
                $data = $db->loadObject();
                if (!$data) {
                    $data = new stdClass();
                }
                $data->product_id = $page->id;
                if (isset($item->price) || !isset($data->id)) {
                    $data->price = !empty($item->price) ? $item->price : 0;
                }
                if (isset($item->sale_price)) {
                    $data->sale_price = $item->sale_price;
                }
                if (isset($item->sku)) {
                    $data->sku = $item->sku;
                }
                if (isset($item->stock)) {
                    $data->stock = $item->stock;
                }
                if (isset($item->weight)) {
                    $dimensions = new stdClass();
                    $dimensions->weight = $item->weight;
                    $data->dimensions = json_encode($dimensions);
                }
                if (isset($item->min)) {
                    $data->min = $item->min;
                }
                $data->product_type = $product->product_type == 'Digital Product' ? 'digital' : 'physical';
                if ($data->product_type == 'digital') {
                    $digital = new stdClass();
                    $digital->file = new stdClass();
                    $link = !empty($item->link) ? $item->link : '';
                    if (strpos($link, 'components/com_gridbox/assets/uploads/') !== false) {
                        $link = isset($data->id) ? basename($link) : '';
                        $digital->file->type = 'upload';
                        $digital->file->filename = $link;
                    } else {
                        $digital->file->type = 'link';
                        $digital->file->filename = '';
                    }
                    $digital->file->name = $link;
                    $digital->expires = new stdClass();
                    $digital->expires->value = $digital->expires->format = '';
                    if (!empty($item->expire)) {
                        $expire = explode(' / ', $item->expire);
                        $digital->expires->value = $expire[0];
                        $digital->expires->format = $expires[$expire[1]];
                    }
                    $digital->max = !empty($item->max_downloads) ? $item->max_downloads : '';
                    if (isset($item->link) || isset($item->link) || isset($item->link)) {
                        $data->digital_file = json_encode($digital);
                    }
                    $data->variations = $data->extra_options = '{}';
                } else {
                    $data->digital_file = '';
                    $data->extra_options = json_encode($extra_options);
                }
                if ($data->product_type != 'digital' && isset($map->{'options'}) && isset($map->{'variation'})) {
                    $data->variations = json_encode($variations);
                } else if ($data->product_type != 'digital' && !isset($data->id)) {
                    $data->variations = '{}';
                }
                if (isset($data->id)) {
                    $db->updateObject('#__gridbox_store_product_data', $data, 'id');
                } else {
                    $db->insertObject('#__gridbox_store_product_data', $data);
                }
            }
            echo json_encode($products);
        }
        exit();
    }

    public function checkDesktopFieldFiles($path, $id)
    {
        $pos = strpos($path, 'components/com_gridbox/assets/uploads');
        if ($pos !== false && $id != 0) {
            preg_match('/\d+/', $path, $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches)) {
                $path = $matches[0][0];
            }
        } else if ($pos !== false && $id == 0) {
            $path = '';
        }

        return $path;
    }

    public function checkMatchedCsv($file, $id, $overwrite, $matched)
    {
        $data = $this->getCSVData($file);
        $response = $this->getCSVResponse();
        $map = new stdClass();
        $cells = $this->getMatchedCells($map, $matched);
        $this->checkRequiredCSVColumns($cells, $response);
        $fields = $this->getMatchedFields($id, $map, $matched);
        $this->checkCSVImport($id, $data, $cells, $fields, $map, $overwrite, $response);
    }

    public function checkGridboxCsv($file, $id, $overwrite)
    {
        $data = $this->getCSVData($file);
        $response = $this->getCSVResponse();
        $keys = $data[0];
        $map = new stdClass();
        $cells = $this->getGridboxCells($map, $keys);
        $this->checkRequiredCSVColumns($cells, $response);
        $fields = $this->getGridboxFields($id, $map, $keys);
        $this->checkCSVImport($id, $data, $cells, $fields, $map, $overwrite, $response);
    }

    public function checkCSVImport($id, $data, $cells, $fields, $map, $overwrite, $response)
    {
        $db = JFactory::getDbo();
        $keys = $data[0];
        $line = 1;
        $products = array();
        $n = count($data);
        for ($i = 1; $i < $n; $i++) {
            $line++;
            $j = $map->{'product_type'};
            $product_type = $data[$i][$j];
            switch ($product_type) {
                case '':
                case 'Product':
                case 'Digital Product':
                    $product = new stdClass();
                    $product->data = $data[$i];
                    $product->product_type = $product_type;
                    $product->line = $line;
                    $product->options = array();
                    $product->variations = array();
                    $product->extra_options = array();
                    $products[] = $product;
                    break;
                case 'Option':
                case 'Variation':
                case 'Extra Options':
                    if ($product_type == 'Option') {
                        $key = 'options';
                    } else if ($product_type == 'Variation') {
                        $key = 'variations';
                    } else {
                        $key = 'extra_options';
                    }
                    $product = end($products);
                    if ($product) {
                        $obj = new stdClass();
                        $obj->data = $data[$i];
                        $obj->line = $line;
                        $product->{$key}[] = $obj;
                    } else {
                        $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    }
                    break;
                default:
                    $this->raiseCsvError($line, $keys[$j], 'INVALID_DATA_TYPE', $response);
                    break;
            }
        }
        $expires = array('Hours' => 'h', 'Days' => 'd', 'Months' => 'm', 'Year' => 'y');
        $tags = array();
        $badges = array();
        foreach ($products as $product) {
            foreach ($cells as $key => $cell) {
                $i = $map->{$key};
                $value = $product->data[$i];
                if ($key == 'id' && ($value == '' || (is_numeric($value) && is_int($value * 1)))) {
                    if ($overwrite == 1 && is_numeric($value) && is_int($value * 1)) {
                        $query = $db->getQuery(true)
                            ->select('COUNT(id)')
                            ->from('#__gridbox_pages')
                            ->where('id = '.$value)
                            ->where('app_id = '.$id);
                        $db->setQuery($query);
                        $count = $db->loadResult();
                        $response->{$count == 0 ? 'new' : 'updated'}++;
                    } else {
                        $response->new++;
                    }
                }
                switch ($key) {
                    case 'id':
                    case 'min':
                    case 'stock':
                        if ($value != '' && (!is_numeric($value) || !is_int($value * 1))) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'price':
                    case 'sale_price':
                    case 'weight':
                        if ($value != '' && !is_numeric($value)) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'max_downloads':
                        if ($product->product_type == 'Digital Product' && $value != '' && !is_numeric($value)) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'expire':
                        if ($product->product_type == 'Digital Product' && $value != '') {
                            $expire = explode(' / ', $value);
                            if (count($expire) != 2 || !is_numeric($expire[0]) || !isset($expires[$expire[1]])) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                    case 'published':
                        if ($value == '' || ($value != 'TRUE' && $value != 'FALSE')) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'tags':
                        if ($value != '') {
                            $array = explode(' / ', $value);
                            foreach ($array as $tag) {
                                if (!isset($tags[$tag])) {
                                    $query = $db->getQuery(true)
                                        ->select('COUNT(id)')
                                        ->from('#__gridbox_tags')
                                        ->where('title = '.$db->quote($tag));
                                    $db->setQuery($query);
                                    $count = $db->loadResult();
                                    $tags[$tag] = $count != 0;
                                }
                                if (!$tags[$tag]) {
                                    $this->raiseCsvError($product->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                                    break;
                                }
                            }
                        }
                        break;
                    case 'badges':
                        if ($value != '') {
                            $array = explode(' / ', $value);
                            foreach ($array as $badge) {
                                if ($badge == '%') {
                                    continue;
                                }
                                if (!isset($badges[$badge])) {
                                    $query = $db->getQuery(true)
                                        ->select('COUNT(id)')
                                        ->from('#__gridbox_store_badges')
                                        ->where('title = '.$db->quote($badge));
                                    $db->setQuery($query);
                                    $count = $db->loadResult();
                                    $badges[$badge] = $count != 0;
                                }
                                if (!$badges[$badge]) {
                                    $this->raiseCsvError($product->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
            foreach ($fields as $field) {
                $i = $map->{$field->id};
                $value = $product->data[$i];
                switch ($field->field_type) {
                    case 'price':
                    case 'number':
                    case 'range':
                        if ($value != '' && !is_numeric($value)) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'date':
                    case 'event-date':
                        if ($value != '' && !DateTime::createFromFormat('Y-m-d', $value)) {
                            $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                        }
                        break;
                    case 'field-video':
                        if ($value != '') {
                            $array = explode('; ', $value);
                            $video = array('Source File', 'Youtube', 'Vimeo');
                            if (count($array) != 2 || !in_array($array[0], $video)) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                    case 'time':
                        if ($value != '') {
                            $array = explode(' : ', $value);
                            if (count($array) != 2) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                    case 'url':
                        if ($value != '') {
                            $array = explode('; ', $value);
                            if (count($array) != 2) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                    case 'radio':
                    case 'select':
                        if ($value != '') {
                            $flag = false;
                            foreach ($field->params->items as $item) {
                                if ($item->title == $value) {
                                    $flag = true;
                                    break;
                                }
                            }
                            if (!$flag) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                    case 'checkbox':
                        if ($value != '') {
                            $values = explode(' / ', $value);
                            $array = array();
                            $flag = false;
                            foreach ($field->params->items as $item) {
                                $array[] = $item->title;
                            }
                            foreach ($values as $title) {
                                if (!in_array($title, $array)) {
                                    $flag = true;
                                    break;
                                }
                            }
                            if ($flag) {
                                $this->raiseCsvError($product->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                            }
                        }
                        break;
                }
            }
            $productFields = array();
            if (isset($map->{'options'})) {
                foreach ($product->options as $option) {
                    $i = $map->{'options'};
                    $value = $option->data[$i];
                    $array = explode(' / ', $value);
                    if (count($array) != 2) {
                        $this->raiseCsvError($option->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                    } else {
                        $title = $array[0];
                        if (!isset($productFields[$title])) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gridbox_store_products_fields')
                                ->where('title = '.$db->quote($title));
                            $db->setQuery($query);
                            $obj = $db->loadObject();
                            $productFields[$title] = $obj;
                        }
                        if (!isset($productFields[$title]->id)) {
                            $this->raiseCsvError($option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        } else {
                            $item = $productFields[$title];
                            if (!isset($item->i)) {
                                $item->items = array();
                                $item->i = 0;
                            }
                            $list = json_decode($item->options);
                            $flag = true;
                            foreach ($list as $li) {
                                if ($li->title == $array[1]) {
                                    $productFields[$title]->items[] = $array[1];
                                    $flag = false;
                                    break;
                                }
                            }
                            if ($flag) {
                                $this->raiseCsvError($option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            }
                        }
                    }
                }
            }
            $rows = array();
            foreach ($productFields as $productField) {
                $rows[] = $productField;
            }
            if (isset($map->{'variation'})) {
                foreach ($product->variations as $variation) {
                    $i = $map->{'variation'};
                    $value = $variation->data[$i];
                    $array = explode(' / ', $value);
                    if (count($array) == 0) {
                        $this->raiseCsvError($variation->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                    } else {
                        foreach ($array as $j => $variable) {
                            if (!isset($rows[$j]->items) || !in_array($variable, $rows[$j]->items)) {
                                $this->raiseCsvError($variation->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            }
                        }
                    }
                }
            }
            if (isset($map->{'extra_options'})) {
                $extra_options = [];
                foreach ($product->extra_options as $extra_option) {
                    $i = $map->{'extra_options'};
                    $value = $extra_option->data[$i];
                    $array = explode(' / ', $value);
                    if (count($array) != 2) {
                        $this->raiseCsvError($extra_option->line, $keys[$i], 'INVALID_DATA_TYPE', $response);
                    } else {
                        $title = $array[0];
                        if (!isset($extra_options[$title])) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gridbox_store_products_fields')
                                ->where('title = '.$db->quote($title));
                            $db->setQuery($query);
                            $extra_options[$title] = $db->loadObject();
                        }
                        if (!isset($extra_options[$title]->id)) {
                            $this->raiseCsvError($extra_option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                        } else {
                            $item = $extra_options[$title];
                            $list = json_decode($item->options);
                            $flag = !in_array($item->field_type, ['textarea', 'textinput', 'file']);
                            foreach ($list as $li) {
                                if ($li->title == $array[1]) {
                                    $flag = false;
                                    break;
                                }
                            }
                            if ($flag) {
                                $this->raiseCsvError($extra_option->line, $keys[$i], 'NO_ITEMS_FOUND', $response);
                            }
                        }
                    }
                }
            }
        }
        $this->submitCSVAnswer($response);
    }

    public function submitCSVAnswer($obj)
    {
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function raiseCsvError($line, $cell, $code, $response)
    {
        if ($response) {
            $response->errors++;
            $error = new stdClass();
            $error->line = $line;
            $error->column = $cell;
            $error->code = JText::_($code);
            $response->log[] = $error;
        }
    }

    public function getCSVAppCells()
    {
        $cells = array('id' => 'ID', 'category' => 'Category', 'title' => 'Title',
            'product_type' => 'Product Type',            
            'options' => 'Option', 'variation' => 'Variation', 'extra_options' => 'Extra Options',
            'price' => 'Price', 'sale_price' => 'Sale Price',
            'sku' => 'SKU', 'stock' => 'In Stock', 'weight' => 'Weight', 'min' => 'Min. Qty',
            'default_variation' => 'Default Variation', 'default_extra' => 'Default Extra Option',
            'link' => 'Product File Link', 'expire' => 'Link Expiration', 'max_downloads' => 'Max. Downloads',
            'image' => 'Image', 'tags' => 'Tags', 'badges' => 'Product Badges', 'intro_text' => 'Intro Text', 'page_alias' => 'Alias',
            'meta_title' => 'SEO Title', 'meta_description' => 'SEO Description', 'published' => 'Published'
        );

        return $cells;
    }

    public function getRequiredAppCells()
    {
        return array('id' => 'ID', 'category' => 'Category', 'title' => 'Title', 'product_type' => 'Product Type');
    }

    public function exportCSV($id, $pks, $cells, $tmp_path)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('p.id, c.title AS category, p.page_alias, p.intro_text, p.meta_title, p.meta_description, p.published')
            ->from('#__gridbox_pages AS p')
            ->leftJoin('#__gridbox_categories AS c ON c.id = p.page_category')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.app_id = '.$id);
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $query->where('p.id IN ('.$str.')');
        }
        $db->setQuery($query);
        $pages = $db->loadObjectList();
        $fields = $this->getCSVAppCells();
        $appFields = $this->getAppFields($id);
        $expires = array('h' => 'Hours', 'd' => 'Days', 'm' => 'Months', 'y' => 'Year');
        foreach ($fields as $key => $field) {
            if (!in_array($key, $cells)) {
                unset($fields[$key]);
            }
        }
        foreach ($appFields as $key => $field) {
            if (!in_array($field->id, $cells)) {
                unset($appFields->{$key});
            }
        }
        foreach ($pages as $key => $page) {
            $page->product = gridboxHelper::$storeHelper->getPreparedProductData($page->id);
            $query = $db->getQuery(true)
                ->select('t.id, t.title')
                ->from('#__gridbox_tags AS t')
                ->leftJoin('#__gridbox_tags_map AS m ON t.id = m.tag_id')
                ->where('m.page_id = '.$page->id);
            $db->setQuery($query);
            $page->tags = $db->loadObjectList();
        }
        $list = [];
        $row = [];
        foreach ($fields as $field) {
            $row[] = $field;
        }
        foreach ($appFields as $key => $field) {
            $row[] = $field->title;
        }
        $list[] = $row;
        foreach ($pages as $page) {
            $row = [];
            $product_type = $page->product->data->product_type;
            if ($product_type == 'digital') {
                $digital_file = $page->product->data->digital_file;
                $digital = !empty($digital_file) ? json_decode($digital_file) : new stdClass();
                $type = isset($digital->file->type) ? $digital->file->type : '';
                $file = new stdClass();
                $file->link = isset($digital->file) ? $digital->file->name : '';
                if ($type != 'link' && isset($digital->file)) {
                    $folder = hash('md5', 'product-'.$page->id);
                    $dir = 'components/com_gridbox/assets/uploads/digital/'.$folder.'/';
                    $file->link = $dir.$digital->file->filename;
                }
                if (isset($digital->file)) {
                    $file->expire = $digital->expires->value.' / '.$expires[$digital->expires->format];
                } else {
                    $file->expire = '';
                }
                $file->max_downloads = isset($digital->file) ? $digital->max : '';
            }
            foreach ($fields as $key => $field) {
                $value = '';
                switch ($key) {
                    case 'id':
                    case 'category':
                    case 'page_alias':
                    case 'intro_text':
                    case 'meta_title':
                    case 'meta_description':
                        $value = $page->{$key};
                        break;
                    case 'product_type':
                        $value = $product_type == 'digital' ? 'Digital Product' : 'Product';
                        break;
                    case 'price':
                    case 'sale_price':
                    case 'sku':
                    case 'stock':
                    case 'image':
                    case 'title':
                    case 'min':
                        $value = $page->product->data->{$key};
                        break;
                    case 'weight':
                        $dimensions = $page->product->data->dimensions;
                        $value = isset($dimensions->weight) ? $dimensions->weight : '';
                        break;
                    case 'tags':
                        $array = [];
                        foreach ($page->tags as $tag) {
                            $array[] = $tag->title;
                        }
                        $value = implode(' / ', $array);
                        break;
                    case 'badges':
                        $array = [];
                        foreach ($page->product->badges as $badge) {
                            $array[] = $badge->type == 'sale' ? '%' : $badge->title;
                        }
                        $value = implode(' / ', $array);
                        break;
                    case 'published':
                        $value = $page->published == 1 ? 'TRUE' : 'FALSE';
                        break;
                    case 'link':
                    case 'expire':
                    case 'max_downloads':
                        if ($product_type == 'digital') {
                            $value = $file->{$key};
                        }
                        break;
                }
                $row[] = $value;
            }
            $desktopFiles = $this->getDesktopFieldFiles($page->id, $id);
            foreach ($appFields as $field) {
                $value = '';
                $data = $this->getFieldsData($page->id, $field->id);
                if (isset($data->value) && !empty($data->value)) {
                    switch ($data->field_type) {
                        case 'product-gallery':
                        case 'field-simple-gallery':
                            $images = json_decode($data->value);
                            $array = array();
                            foreach ($images as $image) {
                                if (is_numeric($image->img) && isset($desktopFiles->{$image->img})) {
                                    $desktopFile = $desktopFiles->{$image->img};
                                    $array[] = 'components/com_gridbox/assets/uploads/app-'.$id.'/'.$desktopFile->filename;
                                } else if (!is_numeric($image->img)) {
                                    $array[] = $image->img;
                                } else {
                                    $array[] = '';
                                }
                                $array[] = $image->alt;
                            }
                            $value = implode('; ', $array);
                            break;
                        case 'textarea':
                        case 'event-date':
                        case 'text':
                        case 'price':
                        case 'number':
                        case 'date':
                        case 'range':
                            $value = $data->value;
                            break;
                        case 'field-slideshow':
                        case 'product-slideshow':
                            $images = json_decode($data->value);
                            $array = array();
                            foreach ($images as $image) {
                                if (is_numeric($image->img) && isset($desktopFiles->{$image->img})) {
                                    $desktopFile = $desktopFiles->{$image->img};
                                    $array[] = 'components/com_gridbox/assets/uploads/app-'.$id.'/'.$desktopFile->filename;
                                } else if (!is_numeric($image->img)) {
                                    $array[] = $image->img;
                                }
                            }
                            $value = implode('; ', $array);
                            break;
                        case 'image-field':
                            $image = json_decode($data->value);
                            $array = array();
                            if (is_numeric($image->src) && isset($desktopFiles->{$image->src})) {
                                $desktopFile = $desktopFiles->{$image->src};
                                $array[] = 'components/com_gridbox/assets/uploads/app-'.$id.'/'.$desktopFile->filename;
                            } else if (!is_numeric($image->src)) {
                                $array[] = $image->src;
                            } else {
                                $array[] = '';
                            }
                            $array[] = $image->alt;
                            $value = implode('; ', $array);
                            break;
                        case 'field-video':
                            $video = json_decode($data->value);
                            $array = array();
                            $array[] = $video->type != 'source' ? ucfirst($video->type) : 'Source File';
                            if ($video->type == 'source' && is_numeric($video->file)
                                && isset($desktopFiles->{$video->file})) {
                                $desktopFile = $desktopFiles->{$video->file};
                                $array[] = 'components/com_gridbox/assets/uploads/app-'.$id.'/'.$desktopFile->filename;
                            } else if ($video->type == 'source' && !is_numeric($video->file)) {
                                $array[] = $video->file;
                            } else if ($video->type != 'source') {
                                $array[] = $video->id;
                            } else {
                                $array[] = '';
                            }
                            $value = implode('; ', $array);
                            break;
                        case 'file':
                            if (is_numeric($data->value) && isset($desktopFiles->{$data->value})) {
                                $desktopFile = $desktopFiles->{$data->value};
                                $value = 'components/com_gridbox/assets/uploads/app-'.$id.'/'.$desktopFile->filename;
                            } else if (!is_numeric($data->value)) {
                                $value = $data->value;
                            }
                            break;
                        case 'time':
                            $time = json_decode($data->value);
                            $value = $time->hours.' : '.$time->minutes;
                            if (!empty($time->format)) {
                                $value .= ' '.$time->format;
                            }
                            break;
                        case 'url':
                            $url = json_decode($data->value);
                            $array = array();
                            $array[] = $url->label;
                            $array[] = $url->link;
                            $value = implode('; ', $array);
                            break;
                        case 'radio':
                        case 'select':
                            foreach ($field->params->items as $item) {
                                if ($item->key == $data->value) {
                                    $value = $item->title;
                                }
                            }
                            break;
                        case 'checkbox':
                            $array = array();
                            $checkbox = json_decode($data->value);
                            foreach ($field->params->items as $item) {
                                if (in_array($item->key, $checkbox)) {
                                    $array[] = $item->title;
                                }
                            }
                            $value = implode(' / ', $array);
                            break;
                    }
                }
                $row[] = $value;
            }
            $list[] = $row;
            if (isset($fields['options'])) {
                foreach ($page->product->fields as $field) {
                    usort($field->map, function($a, $b){
                        return ($a->order_list < $b->order_list) ? -1 : 1;
                    });
                    foreach ($field->map as $option) {
                        $row = [];
                        $images = json_decode($option->images);
                        $array = array($field->title, $option->value);
                        foreach ($fields as $key => $f) {
                            $value = '';
                            if ($key == 'options') {
                                $value = implode(' / ', $array);
                            } else if ($key == 'image' && is_array($images)) {
                                $value = implode('; ', $images);
                            } else if ($key == 'product_type') {
                                $value = $fields['options'];
                            }
                            $row[] = $value;
                        }
                        $list[] = $row;
                    }
                }
            }
            if (isset($fields['variation'])) {
                foreach ($page->product->data->variations as $ind => $obj) {
                    $row = [];
                    $array = explode('+', $ind);
                    $title = [];
                    $obj->weight = isset($obj->weight) ? $obj->weight : '';
                    foreach ($array as $value) {
                        if (isset($page->product->fields_data->{$value})) {
                            $title[] = $page->product->fields_data->{$value};
                        }
                    }
                    foreach ($fields as $key => $f) {
                        $value = '';
                        switch ($key) {
                            case 'product_type':
                                $value = $fields['variation'];
                                break;
                            case 'variation':
                                $value = implode(' / ', $title);
                                break;
                            case 'price':
                            case 'sale_price':
                            case 'sku':
                            case 'stock':
                            case 'weight':
                                $value = $obj->{$key};
                                break;
                            case 'default_variation':
                                $value = isset($obj->default) && $obj->default ? 'TRUE' : '';
                                break;
                        }
                        $row[] = $value;
                    }
                    $list[] = $row;
                }
            }
            if (isset($fields['extra_options'])) {
                foreach ($page->product->data->extra_options as $id => $obj) {
                    foreach ($obj->items as $item) {
                        $title = [$obj->title, $item->title ?? ''];
                        $item->weight = isset($item->weight) ? $item->weight : '';
                        $row = [];
                        foreach ($fields as $key => $f) {
                            $value = '';
                            switch ($key) {
                                case 'product_type':
                                    $value = $fields['extra_options'];
                                    break;
                                case 'extra_options':
                                    $value = implode(' / ', $title);
                                    break;
                                case 'price':
                                case 'weight':
                                    $value = $item->{$key};
                                    break;
                                case 'default_extra':
                                    $value = isset($item->default) && $item->default ? 'TRUE' : '';
                                    break;
                            }
                            $row[] = $value;
                        }
                        $list[] = $row;
                    }
                }
            }
        }
        $file = $tmp_path.'/gridbox-products-'.time().'.csv';
        $fp = fopen($file, 'w');
        foreach ($list as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        return $file;
    }

    public function getPageFields()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $id = $app->input->get('id', '');
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields')
            ->where('app_id = '.$id)
            ->order('order_list DESC')
            ->where('field_type <> '.$db->quote('field-simple-gallery'))
            ->where('field_type <> '.$db->quote('field-slideshow'))
            ->where('field_type <> '.$db->quote('product-gallery'))
            ->where('field_type <> '.$db->quote('product-slideshow'))
            ->where('field_type <> '.$db->quote('field-google-maps'))
            ->where('field_type <> '.$db->quote('field-video'))
            ->where('field_type <> '.$db->quote('image-field'))
            ->where('field_type <> '.$db->quote('tag'))
            ->where('field_type <> '.$db->quote('field-button'));
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

    public function getAppFields($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields')
            ->where('field_type <> '.$db->quote('field-google-maps'))
            ->where('field_type <> '.$db->quote('tag'))
            ->where('app_id = '.$id)
            ->order('order_list DESC');
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $data = new stdClass();
        foreach ($fields as $field) {
            $params = json_decode($field->options);
            $field->params = $params;
            if (empty($params->label) && empty($field->label)) {
                continue;
            }
            $field->title = !empty($params->label) ? $params->label : $field->label;
            $data->{$field->field_key} = $field;
        }

        return $data;
    }

    public function getDesktopFieldFiles($id, $app_id)
    {
        $db = JFactory::getDbo();
        $items = new stdClass();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields_desktop_files')
            ->where('page_id = '.$id)
            ->where('app_id = '.$app_id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        foreach ($files as $file) {
            $items->{$file->id} = $file;
        }

        return $items;
    }

    public function getFieldsData($id, $field_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_page_fields')
            ->where('field_id = '.$field_id)
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $items = $db->loadObject();
        
        return $items;
    }

    public function getType()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();

        return $type;
    }

    public function setGridboxFilters()
    {
        $app = JFactory::getApplication();
        $ordering = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', null);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', null);
        gridboxHelper::setGridboxFilters($ordering, $direction, $this->context);
    }

    public function getGridboxFilters()
    {
        $array = gridboxHelper::getGridboxFilters($this->context);
        if (!empty($array)) {
            foreach ($array as $obj) {
                $name = str_replace($this->context.'.', '', $obj->name);
                $this->setState($name, $obj->value);
            }
        }
    }

    public function setFilters()
    {
        $this->setGridboxFilters();
        $this::populateState();
    }

    public function restore($id, $category)
    {
        $obj = json_decode($category);
        gridboxHelper::movePageFields($id, $obj->app_id);
        $obj->page_category = $obj->id;
        $obj->id = $id;
        $obj->order_list = 0;
        $obj->root_order_list = 0;
        JFactory::getDbo()->updateObject('#__gridbox_pages', $obj, 'id');
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
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $layout = $app->input->get('layout', '');
        $id = $app->input->get('id', '');
        $input = JFactory::getApplication()->input;
        $category = $input->getVar('category', 0, 'get', 'int');
        if ($layout != 'modal' && !empty($category)) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_pages')
                ->where('`app_id` = '.$id)
                ->where('`order_list` = 0')
                ->where('`page_category` <> '.$db->quote('trashed'))
                ->where('`page_category` = '.$db->quote($category));
            $db->setQuery($query);
            $items = $db->loadObjectList();
            if (!empty($items)) {
                $query = $db->getQuery(true)
                    ->select('MAX(order_list) as max, COUNT(id) as count')
                    ->from('#__gridbox_pages')
                    ->where('`app_id` = '.$id)
                    ->where('`order_list` <> 0')
                    ->where('`page_category` <>'.$db->quote('trashed'))
                    ->where('`page_category` = '.$db->quote($category));
                $db->setQuery($query);
                $obj = $db->loadObject();
                if ($obj->count == 0) {
                    $obj->max = 0;
                }
                foreach ($items as $value) {
                    $value->order_list = ++$obj->max;
                    $db->updateObject('#__gridbox_pages', $value, 'id');
                }
            }
        } else if ($layout != 'modal') {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_pages')
                ->where('`app_id` = '.$id)
                ->where('`root_order_list` = 0')
                ->where('`page_category` <> '.$db->quote('trashed'));
            $db->setQuery($query);
            $items = $db->loadObjectList();
            if (!empty($items)) {
                $query = $db->getQuery(true)
                    ->select('MAX(root_order_list) as max, COUNT(id) as count')
                    ->from('#__gridbox_pages')
                    ->where('`app_id` = '.$id)
                    ->where('`root_order_list` <> 0')
                    ->where('`page_category` <>'.$db->quote('trashed'));
                $db->setQuery($query);
                $obj = $db->loadObject();
                if ($obj->count == 0) {
                    $obj->max = 0;
                }
                foreach ($items as $value) {
                    $value->root_order_list = ++$obj->max;
                    $db->updateObject('#__gridbox_pages', $value, 'id');
                }
            }
        }
        $query = $db->getQuery(true);
        if ($layout == 'modal') {
            $query->select('title, id')
                ->from('#__gridbox_app')
                ->where('type <> '.$db->quote('system_apps'))
                ->where('type <> '.$db->quote('single'))
                ->order($db->escape('id ASC'));
            $search = $this->getState($this->context.'filter.search');
            if (!empty($search)) {
                $search = $db->quote('%' . $db->escape($search, true) . '%', false);
                $query->where('title LIKE ' . $search);
            }

            return $query;
        }
        $query->select('DISTINCT p.id, p.title, p.theme, p.published, p.meta_title, p.meta_description, p.featured, p.schema_markup,
            p.meta_keywords, p.intro_image, p.page_alias, p.page_category, p.end_publishing, p.root_order_list, p.robots,
            p.share_image, p.share_title, p.share_description, p.sitemap_override, p.sitemap_include, p.changefreq, p.priority,
            p.page_access, p.intro_text, p.created, p.language, p.app_id, p.hits, p.order_list, p.class_suffix')
            ->from('`#__gridbox_pages` AS p')
            ->where('p.app_id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->leftJoin('`#__gridbox_authors_map` AS m ON '.$db->quoteName('m.page_id').' = '.$db->quoteName('p.id'));
        if ($this->appType == 'products' || $this->appType == 'booking') {
            $query->leftJoin('#__gridbox_store_product_data AS pd ON pd.product_id = p.id')
                ->select('pd.price, pd.sku, pd.stock');
        }
        if (!empty($category)) {
            $query->where('p.page_category = '.$db->quote($category));
        }
        $search = $this->getState('filter.search');
        $wheres = [];

        if (!empty($search)) {
            $search = $db->quote('%'.$db->escape($search, true).'%', false);
            $wheres[] = 'p.title LIKE '.$search;
        }
        if (!empty($search) && $this->appType == 'products') {
            $wheres[] = 'pd.sku LIKE '.$search;
        }
        if (!empty($wheres)) {
            $query->where(implode(' OR ', $wheres));
        }
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('p.published = '.$published);
        } elseif ($published === '') {
            $query->where('p.published IN (0, 1)');
        }
        $theme = $this->getState('filter.theme');
        if (!empty($theme)) {
            $query->where('p.theme = '.(int)$theme);
        }
        $author = $this->getState('filter.author');
        if (!empty($author)) {
            $query->where('m.author_id = '.(int)$author);
        }
        $access = $this->getState('filter.access');
        if (!empty($access)) {
            $query->where('p.page_access = '.$db->quote($access));
        }
        $language = $this->getState('filter.language');
        if (!empty($language)) {
            $query->where('p.language = '.$db->quote($language));
        }
        $ordering = $this->state->get('list.ordering', 'id');
        $dir = $this->state->get('list.direction', 'desc');
        if ($ordering == 'order_list') {
            $dir = 'ASC';
        }
        if ($ordering == 'order_list' && empty($category)) {
            $ordering = 'root_order_list';
        }
        if ($ordering == 'ordering') {
            $ordering = 'title ' . $dir . ', ordering';
        }
        if ($ordering == 'author') {
            $ordering = 'm.author_id';
        } else if ($ordering == 'price' || $ordering == 'sku' || $ordering == 'stock') {
            $order = $this->getProductsOrder($ordering, $dir, $id, $category);
        } else {
            $order =  'p.'.$ordering.' '.$dir;
        }
        $query->order($order);
        
        return $query;
    }

    public function getProductsOrder($order, $dir, $id, $category)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('d.product_id, d.price, d.stock, d.sku')
            ->from('#__gridbox_store_product_data AS d')
            ->where('a.id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
        if (!empty($category)) {
            $query->where('p.page_category = '.$db->quote($category));
        }
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $dir = strtolower($dir);
        if ($order == 'price' && $dir == 'asc') {
            usort($list, function($a, $b){
                return ($a->price < $b->price) ? -1 : 1;
            });
        } else if ($order == 'price') {
            usort($list, function($a, $b){
                return ($a->price < $b->price) ? 1 : -1;
            });
        } else if ($order == 'sku' && $dir == 'asc') {
            usort($list, function($a, $b){
                return ($a->sku < $b->sku) ? -1 : 1;
            });
        } else if ($order == 'sku') {
            usort($list, function($a, $b){
                return ($a->sku < $b->sku) ? 1 : -1;
            });
        } else if ($order == 'stock' && $dir == 'asc') {
            usort($list, function($a, $b){
                return ($a->stock < $b->stock) ? -1 : 1;
            });
        } else if ($order == 'stock') {
            usort($list, function($a, $b){
                return ($a->stock < $b->stock) ? 1 : -1;
            });
        }
        $pks = array();
        foreach ($list as $obj) {
            $pks[] = $obj->product_id;
        }
        if (!empty($pks)) {
            $order = 'FIELD(p.id, '.implode(',', $pks).')';
        } else {
            $order = 'p.id ASC';
        }

        return $order;
    }

    public function getItems()
    {
        $store = $this->getStoreId();
        $app = JFactory::getApplication();
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }
        $query = $this->_getListQuery();
        try {
            if ($app->input->get('layout') == 'modal') {
                $items = $this->_getList($query, 0, 0);
                $this->cache[$store] = $items;

                return $this->cache[$store];
            } else {
                $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
            }            
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        $db = JFactory::getDbo();
        $templates = new stdClass();
        foreach ($items as $key => $item) {
            if (!isset($templates->{$item->theme})) {
                $query = $db->getQuery(true);
                $query->select('id')
                    ->from('#__template_styles')
                    ->where('`id` = ' .$db->quote($item->theme));
                $db->setQuery($query);
                $templates->{$item->theme} = $db->loadResult();
            }
            if (!$templates->{$item->theme} && !isset($templates->default)) {
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__template_styles')
                    ->where('client_id = 0')
                    ->where('template = ' .$db->quote('gridbox'))
                    ->where('home = 1');
                $db->setQuery($query);
                $templates->default = $db->loadResult();
            }
            if (!$templates->{$item->theme}) {
                $item->theme = $templates->default;
                $query = $db->getQuery(true)
                    ->update('#__gridbox_pages')
                    ->set('theme = '.$item->theme)
                    ->where('id = '.$item->id);
                $db->setQuery($query)
                    ->execute();
            }
            $query = $db->getQuery(true)
                ->select('a.id, a.avatar, a.title')
                ->from('#__gridbox_authors_map AS m')
                ->where('m.page_id = '.$item->id)
                ->leftJoin('#__gridbox_authors AS a ON a.id = m.author_id')
                ->order('m.id ASC');
            $db->setQuery($query);
            $items[$key]->author = $db->loadObjectList();
            $query = $db->getQuery(true)
                ->select('p.*, c.title')
                ->from('#__gridbox_category_page_map AS p')
                ->where('p.page_id = '.$item->id)
                ->leftJoin('#__gridbox_categories AS c ON p.category_id = c.id');
            $db->setQuery($query);
            $items[$key]->categories = $db->loadObjectList();
        }
        $this->cache[$store] = $items;

        return $this->cache[$store];
    }
    
    public function getThemes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__template_styles')
            ->where('`template` = ' .$db->Quote('gridbox'));
        $db->setQuery($query);
        
        return $db->loadObjectList();
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        $theme = $this->getUserStateFromRequest($this->context.'.filter.theme', 'theme_filter', '', 'string');
        $this->setState('filter.theme', $theme);
        $author = $this->getUserStateFromRequest($this->context.'.filter.author', 'author_filter', '', 'string');
        $this->setState('filter.author', $author);
        $access = $this->getUserStateFromRequest($this->context.'.filter.access', 'access_filter', '', 'string');
        $this->setState('filter.access', $access);
        $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'language_filter', '', 'string');
        $this->setState('filter.language', $language);
        parent::populateState('id', 'desc');
    }
}