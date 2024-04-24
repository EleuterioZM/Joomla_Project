<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxSeoHelper
{
    private $item;
    private $type;
    private $tags;
    private $shortCodes;
    private $product;

    public function __construct($item, $type)
    {
        $this->item = $item;
        $this->type = $type;
        $this->getShortCodes($type);
        $this->tags = new stdClass();
    }

    public function prepareText($text)
    {
        if (!empty($text)) {
            foreach ($this->shortCodes as $tag) {
                preg_match_all('/\['.$tag.'\]/', $text, $matches);
                if (!empty($matches)) {
                    foreach ($matches[0] as $match) {
                        $value = $this->checkTag($tag, $match);
                        $text = str_replace($match, $value, $text);
                    }
                }
            }
        }

        return $text;
    }

    public function prepareSchema($text)
    {
        if (!empty($text)) {
            foreach ($this->shortCodes as $tag) {
                preg_match_all('/\['.$tag.'\]/', $text, $matches);
                if (!empty($matches)) {
                    foreach ($matches[0] as $match) {
                        $value = $this->checkTag($tag, $match);
                        if ($tag == 'Product In Stock') {
                            $value = 'https://schema.org/'.($this->product->stock === '0' ? 'OutOfStock' : 'InStock');
                        } else if ($tag == 'Page Image' && !gridboxHelper::isExternal($value)) {
                            $value = JUri::root().$value;
                        }
                        $text = str_replace($match, $value, $text);
                    }
                }
                
            }
        }

        return $text;
    }

    protected function checkTag($tag, $match)
    {
        if (isset($this->tags->{$tag}) || ($tag == 'Field \d+' && isset($this->tags->{$match}))) {
            $value = isset($this->tags->{$tag}) ? $this->tags->{$tag} : $this->tags->{$match};
            return $value;
        }
        $value = '';
        $store = gridboxHelper::$store;
        $item = $this->item;
        $db = JFactory::getDbo();
        switch ($tag) {
            case 'Field \d+':
                preg_match('/\d+/', $match, $matches);
                $id = $matches[0];
                $query = $db->getQuery(true)
                    ->select('f.*')
                    ->from('#__gridbox_fields as f')
                    ->where('f.id = '.$id)
                    ->select('pf.value')
                    ->where('pf.page_id = '.$item->id)
                    ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.field_id = f.id');
                $db->setQuery($query);
                $data = $db->loadObject();
                if ($data && ($data->field_type == 'radio' || $data->field_type == 'select')) {
                    $fieldOptions = json_decode($data->options);
                    $array = [];
                    foreach ($fieldOptions->items as $fieldOption) {
                        if ($fieldOption->key == $data->value) {
                            $array[] = $fieldOption->title;
                        }
                    }
                    $value = implode(', ', $array);
                } else if ($data->field_type == 'checkbox') {
                    $valueOptions = json_decode($data->value);
                    $array = [];
                    foreach ($valueOptions as $valueOption) {
                        foreach ($fieldOptions->items as $fieldOption) {
                            if ($fieldOption->key == $valueOption) {
                                $array[] = $fieldOption->title;
                            }
                        }
                    }
                    $value = implode(', ', $array);
                } else if ($data && $data->field_type == 'time') {
                    $valueOptions = json_decode($data->value);
                    $value = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
                } else if ($data && $data->field_type == 'url') {
                    $valueOptions = json_decode($data->value);
                    $value = gridboxHelper::prepareGridboxLinks($valueOptions->link);
                } else if ($data && ($data->field_type == 'date' || $data->field_type == 'event-date')) {
                    $value = gridboxHelper::formatDate($data->value);
                } else if ($data && $data->field_type == 'price') {
                    $fieldOptions = json_decode($data->options);
                    $thousand = $fieldOptions->thousand;
                    $separator = $fieldOptions->separator;
                    $decimals = $fieldOptions->decimals;
                    $value = gridboxHelper::preparePrice($data->value, $thousand, $separator, $decimals, 1);
                } else if ($data) {
                    $value = $data->value;
                }
                $tag = $match;
                break;
            case 'Product SKU':
                if (empty($this->product)) {
                    $this->product = gridboxHelper::$storeHelper->getProductData($item->id);
                }
                $value = $this->product->sku;
                break;
            case 'Product In Stock':
                if (empty($this->product)) {
                    $this->product = gridboxHelper::$storeHelper->getProductData($item->id);
                }
                $value = $this->product->stock;
                break;
            case 'Product Price':
                if (empty($this->product)) {
                    $this->product = gridboxHelper::$storeHelper->getProductData($item->id);
                }
                $value = $this->product->price;
                break;
            case 'Product Sale Price':
                if (empty($this->product)) {
                    $this->product = gridboxHelper::$storeHelper->getProductData($item->id);
                }
                $value = $this->product->sale_price;
                break;
            case 'Product Currency':
                $value = gridboxHelper::$store->currency->code;
                break;
            case 'Page Title':
            case 'Tag Title':
            case 'Author Name':
                $value = $item->title;
                break;
            case 'Page Image':
                $value = $item->intro_image;
                break;
            case 'Page URL':
            case 'Category Page URL':
            case 'Tag Page URL':
            case 'Author Page URL':
                $value = JUri::current();
                break;
            case 'Site Name':
                $app = JFactory::getApplication();
                $value = $app->get('sitename');
                break;
            case 'Date Modified':
                $date = '';
                if (!empty($item->saved_time)) {
                    $array = explode('-', $item->saved_time);
                    $time = count($array) == 6 ? $array[0].'-'.$array[1].'-'.$array[2].' '.$array[3].':'.$array[4].':'.$array[5] : 'now';
                    $date = JHtml::date($time, gridboxHelper::$dateFormat);
                }
                $value = $date;
                break;
            case 'Author':
                $array = [];
                foreach ($item->authors as $author) {
                    $array[] = $author->title;
                }
                $value = implode(', ', $array);
                break;
            case 'Page Tags':
                $groups = JFactory::getUser()->getAuthorisedViewLevels();
                $groups = implode(',', $groups);
                $query = $db->getQuery(true)
                    ->select('m.tag_id as id')
                    ->from('#__gridbox_tags_map AS m')
                    ->where('m.page_id = '.$item->id)
                    ->select('t.title')
                    ->leftJoin('`#__gridbox_tags` AS t ON m.tag_id = t.id')
                    ->order('t.hits desc')
                    ->where('t.published = 1')
                    ->where('t.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                    ->where('t.access in ('.$groups.')')
                    ->leftJoin('`#__gridbox_pages` AS p ON m.page_id = p.id');
                $db->setQuery($query);
                $tags = $db->loadObjectList();
                $array = [];
                foreach ($tags as $obj) {
                    $array[] = $obj->title;
                }
                $value = implode(', ', $array);
                break;
            case 'Start Publishing':
                $value = JHtml::date($item->created, gridboxHelper::$dateFormat);
                break;
            case 'Intro Text':
                $value = $item->intro_text;
                break;
            case 'Category Title':
                $value = $this->type == 'page' ? $item->category_title : $item->title;
                break;
            case 'Category Image URL':
            case 'Tag Image URL':
            case 'Author Profile Picture URL':
                $value = $item->image;
                break;
            case 'Category Description':
            case 'Tag Description':
            case 'Author Description':
                $value = $item->description;
                break;
            case 'Store Name':
                $value = $store->general->store_name;
                break;
            case 'Store Legal Business Name':
                $value = $store->general->business_name;
                break;
            case 'Store Phone':
                $value = $store->general->phone;
                break;
            case 'Store Email':
                $value = $store->general->email;
                break;
            case 'Store Address':
                $address = [];
                if (!empty($store->general->country)) {
                    $address[] = $store->general->country;
                }
                if (!empty($store->general->region)) {
                    $address[] = $store->general->region;
                }
                if (!empty($store->general->city)) {
                    $address[] = $store->general->city;
                }
                if (!empty($store->general->street)) {
                    $address[] = $store->general->street;
                }
                if (!empty($store->general->zip_code)) {
                    $address[] = $store->general->zip_code;
                }
                $value = implode(', ', $address);
                break;
        }
        $this->tags->{$tag} = strip_tags($value);

        return $this->tags->{$tag};
    }

    protected function getShortCodes($type)
    {
        $array = [];
        if ($type == 'page') {
            $array = ['Field \d+', 'Store Address', 'Store Email', 'Store Phone', 'Store Legal Business Name',
                'Store Name', 'Product In Stock', 'Product Currency', 'Product Sale Price', 'Product Price',
                'Product SKU', 'Intro Text', 'Start Publishing', 'Author', 'Page Tags', 'Category Title',
                'Page URL', 'Page Image', 'Page Title', 'Site Name', 'Date Modified'];
        } else if ($type == 'category') {
            $array = ['Site Name', 'Category Title', 'Category Image URL', 'Category Page URL', 'Category Description'];
        } else if ($type == 'tag') {
            $array = ['Site Name', 'Tag Title', 'Tag Image URL', 'Tag Page URL', 'Tag Description'];
        } else if ($type == 'author') {
            $array = ['Site Name', 'Author Name', 'Author Profile Picture URL', 'Author Page URL', 'Author Description'];
        }
        $this->shortCodes = $array;
    }

    public function getGlobal()
    {
        $db = JFactory::getDbo();
        $id = $this->type == 'page' || $this->type == 'category' ? $this->item->app_id : 0;
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_seo_defaults')
            ->where('item_id = '.$id)
            ->where('item_type = '.$db->quote($this->type));
        $db->setQuery($query);
        $obj = $db->loadObject();
        $file = JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/store-options/product-schema.html';
        if (empty($obj)) {
            $schema = $this->type == 'page' && isset($this->item->app_type) && $this->item->app_type == 'products' ? gridboxHelper::readFile($file) : '';
            $seo = [
                'id' => '0',
                'item_id' => $id,
                'item_type' => $this->type,
                'meta_title' => '',
                'meta_description' => '',
                'share_image' => '',
                'share_title' => '',
                'share_description' => '',
                'sitemap_include' => '1',
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'schema_markup' => $schema
            ];
            $obj = (object)$seo;
        }

        return $obj;
    }
}