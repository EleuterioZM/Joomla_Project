<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filter.output');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Languages\Site\Helper\LanguagesHelper;
include 'functions.php';
include JPATH_ROOT.'/components/com_gridbox/helpers/traits/DateTrait.php';

abstract class gridboxHelper
{
    use DateTrait;

    public static $fonts;
    public static $up;
    public static $cssRulesFlag;
    public static $breakpoints;
    public static $breakpoint;
    public static $menuBreakpoint;
    public static $website;
    public static $customFonts;
    public static $colorVariables;
    public static $presets;
    public static $editItem;
    public static $parentFonts;
    public static $commentUser;
    public static $commentsModerators;
    public static $systemApps;
    public static $reviewsModerators;
    public static $blogPostsInfo;
    public static $blogPostsFields;
    public static $review;
    public static $cacheData;
    public static $store;
    public static $taxRates;
    public static $menuItems;
    public static $storeHelper;
    public static $globalItems;
    public static $css;
    public static $isError;
    public static $booking;

    public static function getGridboxApi(string $service)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote($service));
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public static function getModalSettings(string $service = 'modal-cp-position'):string
    {
        $user = JFactory::getUser();
        $service .= '-'.$user->id;
        $obj = self::getGridboxApi($service);
        $key = $obj->key ?? '{}';

        return $key;
    }

    public static function setModalSettings(string $service, string $key):void
    {
        $user = JFactory::getUser();
        $service .= '-'.$user->id;
        $db = JFactory::getDbo();
        $obj = self::getGridboxApi($service);
        if (!$obj) {
            $obj = (object)[
                'service' => $service,
                'key' => $key
            ];
            $db->insertObject("#__gridbox_api", $obj);
        } else {
            $obj->key = $key;
            $db->updateObject('#__gridbox_api', $obj, 'id');
        }
    }

    public static function getBooking()
    {
        if (self::$booking) {
            return self::$booking;
        }
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/booking.php';
        self::$booking = new gridboxBooking();

        return self::$booking;
    }

    public static function prepareIntroImage($img)
    {
        if (!is_numeric($img)) {
            return $img;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields_desktop_files')
            ->where('id = '.$img);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!isset($obj->filename)) {
            return '';
        }
        
        return 'components/com_gridbox/assets/uploads/app-'.$obj->app_id.'/'.$obj->filename;
    }

    public static function triggerEvent($event, $data = [], $plugin = null)
    {
        if ($plugin) {
            JPluginHelper::importPlugin($plugin);
        }
        $dispatcher = JFactory::getApplication();
        $dispatcher->triggerEvent($event, $data);
    }

    public static function getUserGroups($id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__usergroups')
            ->order('lft ASC');
        if (!empty($id)) {
            $query->where('id = '.$id);
        }
        $db->setQuery($query);
        if (!empty($id)) {
            $groups = $db->loadObject();
        } else {
            $groups = $db->loadObjectList();
            foreach ($groups as $group) {
                $group->level = self::getUserGroupLevel($group->parent_id);
            }
        }

        return $groups;
    }

    public static function getUserGroupLevel($parent, $level = 0)
    {
        if (!empty($parent)) {
            ++$level;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('parent_id')
                ->from($db->quoteName('#__usergroups'))
                ->where('id = '.$parent);
            $db->setQuery($query);
            $id = $db->loadResult();
            $level = self::getUserGroupLevel($id, $level);
        }

        return $level;
    }

    public static function isExternal($link)
    {
        return (strpos($link, 'https://') !== false || strpos($link, 'http://') !== false);
    }

    public static function readFile($path)
    {
        $handle = fopen($path, "r");
        $size = filesize($path);
        $content = '';
        if (!empty($size) && $handle) {
            $content = fread($handle, $size);
            fclose($handle);
        }

        return $content;
    }

    public static function deleteFolder($dir)
    {
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        self::deleteFolder($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function getWishlistId()
    {
        $input = JFactory::getApplication()->input;
        $user_id = JFactory::getUser()->id;
        if (self::$store->wishlist->login && $user_id != 0) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_store_wishlist')
                ->where('user_id = '.$user_id);
            $db->setQuery($query);
            $id = $db->loadResult();
            $id = empty($id) ? 0 : $id;
        } else {
            $id = $input->cookie->get('gridbox_store_wishlist', 0, 'int');
        }

        return $id;
    }

    public static function getStoreWishlistObject($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_wishlist')
            ->where('id = '.$id);
        $db->setQuery($query);
        $wishlist = $db->loadObject();
        if (!$wishlist) {
            $wishlist = new stdClass();
            $wishlist->id = 0;
        }

        return $wishlist;
    }

    public static function updateStoreWishlist($id)
    {
        $db = JFactory::getDbo();
        $user_id = JFactory::getUser()->id;
        $wishlist = self::getStoreWishlistObject($id);
        if (self::$store->wishlist->login && $user_id != 0) {
            $wishlist->user_id = $user_id;
        }
        if (empty($wishlist->id)) {
            $db->insertObject('#__gridbox_store_wishlist', $wishlist);
            $wishlist->id = $db->insertid();
        }
        if (!self::$store->wishlist->login) {
            $time = time() + 604800 * 4 * 12;
            self::setcookie('gridbox_store_wishlist', $wishlist->id, $time);
        }

        return $wishlist;
    }

    public static function updateStoreCart($cart)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $cart->user_id = $user->id;
        $db->updateObject('#__gridbox_store_cart', $cart, 'id');
        $time = time() + 604800;
        self::setcookie('gridbox_store_cart', $cart->id, $time);
    }

    public static function getPromoCodeQuery()
    {
        $db = JFactory::getDBO();
        $date = JDate::getInstance()->format('Y-m-d H:i:s');
        $date = $db->quote($date);
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->from('#__gridbox_store_promo_codes AS p')
            ->where('p.published = 1')
            ->where('(p.publish_down = '.$nullDate.' OR p.publish_down >= '.$date.')')
            ->where('(p.publish_up = '.$nullDate.' OR p.publish_up <= '.$date.')')
            ->where('(p.limit = 0 OR p.used < pc.limit)')
            ->leftJoin('#__gridbox_store_promo_codes AS pc ON pc.id = p.id');
        
        return $query;
    }

    public static function checkPromoCode($promo, $product)
    {
        $valid = false;
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        if (!in_array($promo->access, $groups)) {
            return $valid;
        }
        if ($promo->applies_to != '*') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_promo_codes_map')
                ->where('code_id = '.$promo->id)
                ->where('type = '.$db->quote($promo->applies_to));
            $db->setQuery($query);
            $promo->map = $db->loadObjectList();
        }
        if ($promo->applies_to == '*' && $promo->disable_sales == 0) {
            $valid = true;
        } else if ($promo->applies_to == '*' && $promo->disable_sales == 1) {
            $data = self::$storeHelper->getProductData($product->product_id);
            $prices = self::prepareProductPrices($data->product_id, $data->price, $data->sale_price, $product->variation);
            $valid = $prices->sale_price !== '' ? false : true;
        } else if ($promo->applies_to == 'product') {
            foreach ($promo->map as $value) {
                if ($product->product_id == $value->item_id && $product->variation == $value->variation && $promo->disable_sales == 0) {
                    $valid = true;
                } else if ($product->product_id == $value->item_id && $product->variation == $value->variation
                    && $promo->disable_sales == 1) {
                    $data = self::$storeHelper->getProductData($product->product_id);
                    $prices = self::prepareProductPrices($data->product_id, $data->price, $data->sale_price, $product->variation);
                    $valid = $prices->sale_price !== '' ? false : true;
                }
                if ($valid) {
                    break;
                }
            }
        } else {
            $categories = self::getCategoryId($product->product_id);
            foreach ($promo->map as $value) {
                if (in_array($value->item_id, $categories) && $promo->disable_sales == 0) {
                    $valid = true;
                } else if (in_array($value->item_id, $categories) && $promo->disable_sales == 1) {
                    $data = self::$storeHelper->getProductData($product->product_id);
                    $prices = self::prepareProductPrices($data->product_id, $data->price, $data->sale_price, $product->variation);
                    $valid = $prices->sale_price !== '' ? false : true;
                }
                if ($valid) {
                    break;
                }
            }
        }
        
        return $valid;
    }

    public static function getStoreCartObject($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_cart')
            ->where('id = '.$id);
        $db->setQuery($query);
        $cart = $db->loadObject();
        $user_id = JFactory::getUser()->id;
        if (!$cart) {
            $cart = new stdClass();
            $cart->id = 0;
            $cart->promo_id = 0;
            $cart->country = $cart->region = '';
            $db->insertObject('#__gridbox_store_cart', $cart);
            $cart->id = $db->insertid();
            self::updateStoreCart($cart);
        }
        if (!empty($user_id)) {
            $query = $db->getQuery(true)
                ->select('ui.value, ui.id')
                ->from('#__gridbox_store_customer_info AS ci')
                ->where('ci.type = '.$db->quote('country'))
                ->where('ui.user_id = '.$user_id)
                ->leftJoin('#__gridbox_store_user_info AS ui ON ui.customer_id = ci.id');
            $db->setQuery($query);
            $info = $db->loadObject();
            if (!empty($info->value)) {
                $object = json_decode($info->value);
                if (!empty($object->country) && !is_numeric($object->country)) {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_countries')
                        ->where('title = '.$db->quote($object->country));
                    $db->setQuery($query);
                    $country = $db->loadObject();
                    $object->country = $country ? $country->id : 0;
                    if (!empty($object->region)) {
                        $query = $db->getQuery(true)
                            ->select('*')
                            ->from('#__gridbox_country_states')
                            ->where('country_id = '.$object->country)
                            ->where('title = '.$db->quote($object->region));
                        $db->setQuery($query);
                        $region = $db->loadObject();
                        $object->region = $region ? $region->id : 0;
                    }
                    $info->value = json_encode($object);
                    $db->updateObject('#__gridbox_store_user_info', $info, 'id');
                }
                $cart->country = $object->country;
                $cart->region = $object->region;
            }
        }
        if (!empty($cart->country) && !is_numeric($cart->country)) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_countries')
                ->where('title = '.$db->quote($cart->country));
            $db->setQuery($query);
            $country = $db->loadObject();
            $cart->country = $country ? $country->id : 0;
            if (!empty($cart->region)) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_country_states')
                    ->where('country_id = '.$cart->country)
                    ->where('title = '.$db->quote($cart->region));
                $db->setQuery($query);
                $region = $db->loadObject();
                $cart->region = $region ? $region->id : 0;
            }
            $db->updateObject('#__gridbox_store_cart', $cart, 'id');
        }

        return $cart;
    }

    public static function getStoreWishlistProducts($id)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $tag = $db->quote(JFactory::getLanguage()->getTag());
        $query = $db->getQuery(true)
            ->select('wp.*, p.title, p.intro_image, p.app_id, p.page_category')
            ->from('#__gridbox_store_wishlist_products AS wp')
            ->where('wp.wishlist_id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('p.language in ('.$tag.','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->where('c.published = 1')
            ->where('c.language in ('.$tag.','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')')
            ->leftJoin('#__gridbox_pages as p ON wp.product_id = p.id')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id');
        $db->setQuery($query);
        $products = $db->loadObjectList();
        foreach ($products as $product) {
            $link = self::getGridboxPageLinks($product->product_id, 'product', $product->app_id, $product->page_category);
            $product->link = JRoute::_($link);
            $product->extra_options = !empty($product->extra_options) ? json_decode($product->extra_options) : new stdClass();
            $product->booking = !empty($product->booking) ? json_decode($product->booking) : new stdClass();
            $query = $db->getQuery(true)
                ->select('a.*')
                ->from('#__gridbox_store_product_attachments AS a')
                ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id')
                ->where('m.cart_id = 0')
                ->where('m.wishlist_id = '.$id)
                ->where('m.product_id = '.$product->id);
            $db->setQuery($query);
            $attachments = $db->loadObjectList();
            foreach ($attachments as $attachment) {
                if (!isset($product->extra_options->{$attachment->attachment_id})) {
                    continue;
                }
                if (!isset($product->extra_options->{$attachment->attachment_id}->files)) {
                    $product->extra_options->{$attachment->attachment_id}->files = [];
                }
                $product->extra_options->{$attachment->attachment_id}->files[] = $attachment;
            }
        }

        return $products;
    }

    public static function getStoreCartProducts($id)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $tag = $db->quote(JFactory::getLanguage()->getTag());
        $query = $db->getQuery(true)
            ->select('cp.*, p.title, p.intro_image, p.app_id, p.page_category')
            ->from('#__gridbox_store_cart_products AS cp')
            ->where('cp.cart_id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('p.language in ('.$tag.','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->where('c.published = 1')
            ->where('c.language in ('.$tag.','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')')
            ->leftJoin('#__gridbox_pages as p ON cp.product_id = p.id')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id');
        $db->setQuery($query);
        $products = $db->loadObjectList();
        foreach ($products as $product) {
            $link = self::getGridboxPageLinks($product->product_id, 'product', $product->app_id, $product->page_category);
            $product->link = JRoute::_($link);
            if (!empty($product->extra_options)) {
                $product->extra_options = json_decode($product->extra_options);
            } else {
                $product->extra_options = new stdClass();
            }
            if (!empty($product->booking)) {
                $product->booking = json_decode($product->booking);
            } else {
                $product->booking = new stdClass();
            }
            $query = $db->getQuery(true)
                ->select('a.*')
                ->from('#__gridbox_store_product_attachments AS a')
                ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON m.id = a.attachment_id')
                ->where('m.cart_id = '.$id)
                ->where('m.wishlist_id = 0')
                ->where('m.product_id = '.$product->id);
            $db->setQuery($query);
            $attachments = $db->loadObjectList();
            foreach ($attachments as $attachment) {
                if (!isset($product->extra_options->{$attachment->attachment_id})) {
                    continue;
                }
                if (!isset($product->extra_options->{$attachment->attachment_id}->files)) {
                    $product->extra_options->{$attachment->attachment_id}->files = [];
                }
                $product->extra_options->{$attachment->attachment_id}->files[] = $attachment;
            }
        }

        return $products;
    }

    public static function setOrder()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->cookie->get('gridbox_store_order', 0, 'int');
        if (!empty($id)) {
            self::setBreakpoints();
            self::$storeHelper->setOrder($id);
        }
    }

    public static function getGridboxMenuItemidByPage($id)
    {
        $itemId = '';
        foreach (self::$menuItems as $item) {
            if (isset($item->query) && isset($item->query['id']) && isset($item->query['view']) &&
                $item->query['view'] == 'page' && $item->query['id'] == $id) {
                $itemId = '&Itemid='.$item->id;
                break;
            }
        }

        return $itemId;
    }

    public static function getGridboxMenuItemidByCategory($app_id, $id)
    {
        $itemId = '';
        foreach (self::$menuItems as $value) {
            if (isset($value->query) && isset($value->query['id']) && isset($value->query['app'])
                && $value->query['view'] == 'blog' && $value->query['app'] == $app_id && $value->query['id'] == $id) {
                $itemId = '&Itemid='.$value->id;
                break;
            }
        }

        return $itemId;
    }

    public static function getGridboxMenuItemidByApp($app_id)
    {
        $itemId = '';
        foreach (self::$menuItems as $value) {
            if (isset($value->query) && isset($value->query['id']) && isset($value->query['app'])
                && $value->query['view'] == 'blog' && $value->query['app'] == $app_id && $value->query['id'] == 0) {
                $itemId = '&Itemid='.$value->id;
                break;
            }
        }

        return $itemId;
    }

    public static function getGridboxMenuItemidByTag($id, $app_id)
    {
        $itemId = '';
        foreach (self::$menuItems as $item) {
            if (isset($item->query) && isset($item->query['tag']) && isset($item->query['app'])
                && $item->query['view'] == 'blog' && $item->query['app'] == $app_id && $item->query['tag'] == $id) {
                $itemId = '&Itemid='.$item->id;
                break;
            }
        }

        return $itemId;
    }

    public static function getGridboxMenuItems()
    {
        if (!self::$menuItems) {
            $menus = JFactory::getApplication()->getMenu('site');
            $component = JComponentHelper::getComponent('com_gridbox');
            $attributes = array('component_id');
            $values = array($component->id);
            self::$menuItems = $menus->getItems($attributes, $values);
            $languages  = JLanguageHelper::getLanguages();
            foreach ($languages as $language) {
                $attributes[1] = 'language';
                $values[1] = $language->lang_code;
                $array = $menus->getItems($attributes, $values);
                self::$menuItems = array_merge(self::$menuItems, $array);
            }
        }
    }

    public static function getGridboxSystemLinks($id)
    {
        self::getGridboxMenuItems();
        $itemId = '';
        foreach (self::$menuItems as $item) {
            if (isset($item->query) && isset($item->query['id']) && isset($item->query['view']) &&
                $item->query['view'] == 'system' && $item->query['id'] == $id) {
                $itemId = '&Itemid='.$item->id;
                break;
            }
        }
        $link = 'index.php?option=com_gridbox&view=system&id='.$id;
        if (empty($itemId)) {
            $itemId = '&Itemid='.self::getDefaultMenuItem();
        }
        $link .= $itemId;

        return $link;
    }

    public static function getGridboxPageLinks($id, $type = 'single', $app_id = 0, $category = 0)
    {
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByPage($id);
        if ($type == 'single') {
            $link = 'index.php?option=com_gridbox&view=page&id='.$id;
        } else {
            $link = 'index.php?option=com_gridbox&view=page&blog='.$app_id.'&category='.$category.'&id='.$id;
        }
        if (empty($itemId) && $type && $type != 'single') {
            $itemId = self::getGridboxMenuItemidByCategory($app_id, $category);
            if (empty($itemId)) {
                $catsId = self::getCategoryIdPath($category);
                foreach ($catsId as $catId) {
                    $itemId = self::getGridboxMenuItemidByCategory($app_id, $catId);
                    if (!empty($itemId)) {
                        break;
                    }
                }
            }
            if (empty($itemId)) {
                $itemId = self::getGridboxMenuItemidByApp($app_id);
            }
        }
        if (empty($itemId)) {
            $itemId = '&Itemid='.self::getDefaultMenuItem();
        }
        $link .= $itemId;

        return $link;
    }

    public static function getGridboxCategoryLinks($id, $app_id)
    {
        $link = 'index.php?option=com_gridbox&view=blog&app='.$app_id.'&id='.$id;
        $itemId = '';
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByCategory($app_id, $id);
        if (empty($itemId) && !empty($id)) {
            $catsId = self::getCategoryIdPath($id);
            foreach ($catsId as $catId) {
                $itemId = self::getGridboxMenuItemidByCategory($app_id, $catId);
                if (!empty($itemId)) {
                    break;
                }
            }
        }
        if (empty($itemId)) {
            $itemId = self::getGridboxMenuItemidByApp($app_id);
        }
        if (empty($itemId)) {
            $itemId = '&Itemid='.self::getDefaultMenuItem();
        }
        $link .= $itemId;

        return $link;
    }

    public static function getGridboxTagLinks($id, $app_id)
    {
        $link = 'index.php?option=com_gridbox&view=blog&app='.$app_id.'&id=0&tag='.$id;
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByTag($id, $app_id);
        if (empty($itemId)) {
            $itemId = self::getGridboxMenuItemidByApp($app_id);
        }
        if (empty($itemId)) {
            $itemId = '&Itemid='.self::getDefaultMenuItem();
        }
        $link .= $itemId;

        return $link;
    }

    public static function getGridboxAuthorLinks($id, $app_id)
    {
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByApp($app_id);
        if (empty($itemId)) {
            $itemId = '&Itemid='.self::getDefaultMenuItem();
        }
        $link = 'index.php?option=com_gridbox&view=blog&app='.$app_id.'&id=0&author='.$id.$itemId;

        return $link;
    }

    public static function getStoreWishlist($id)
    {
        $db = JFactory::getDbo();
        $wishlist = self::getStoreWishlistObject($id);
        $wishlist->products = self::getStoreWishlistProducts($id);
        $wishlist->quantity = 0;
        foreach ($wishlist->products as $product) {
            $data = self::$storeHelper->getProductData($product->product_id);
            $product->min = $data->min;
            if (!empty($product->variation) && !isset($data->variations->{$product->variation})) {
                self::removeProductFromWishlist($product->id);
                continue;
            }
            $extra_options = new stdClass();
            $extra_options->count = 0;
            $extra_options->price = 0;
            $extra_options->items = new stdClass();
            $removeFlag = false;
            $product->hasFileQty = false;
            foreach ($product->extra_options as $key => $value) {
                if (!isset($data->extra_options->{$value->field_id}) ||
                    (!isset($data->extra_options->{$value->field_id}->items->{$key}) && !isset($value->attachments) && !isset($value->text))) {
                    $removeFlag = true;
                    break;
                } else {
                    $obj = $data->extra_options->{$value->field_id};
                    if (!isset($extra_options->items->{$value->field_id})) {
                        $object = new stdClass();
                        $object->title = $obj->title;
                        $object->required = $obj->required == '1';
                        $object->values = new stdClass();
                        $extra_options->items->{$value->field_id} = $object;
                    } else {
                        $object = $extra_options->items->{$value->field_id};
                    }
                    $extra_options->count++;
                    $option = new stdClass();
                    if ($obj->type == 'file') {
                        $object->attachments = $value->files;
                        $key = 0;
                    } else if ($obj->type == 'textarea' || $obj->type == 'textinput') {
                        $key = 0;
                    }
                    $option->price = $obj->items->{$key}->price;
                    $option->weight = isset($obj->items->{$key}->weight) ? $obj->items->{$key}->weight : '';
                    if ($obj->type == 'file') {
                        $object->charge = $obj->file_options->charge;
                        $object->quantity = $obj->file_options->quantity;
                        $option->price = $obj->file_options->charge ? ($option->price * count($value->files)) : $option->price;
                    } else if ($obj->type == 'textarea' || $obj->type == 'textinput') {
                        $option->value = $value->text;
                        $object->values->{$key} = $option;
                    } else {
                        $option->value = $obj->items->{$key}->title;
                        $object->values->{$key} = $option;
                    }
                    if ($obj->type == 'file' && $object->quantity) {
                        $product->hasFileQty = true;
                    }
                    if (!empty($option->price)) {
                        $extra_options->price += $option->price * 1;
                    }
                }
            }
            if ($removeFlag) {
                self::removeProductFromWishlist($product->id);
                continue;
            }
            $product->extra_options = $extra_options;
            $product->data = !empty($product->variation) ? $data->variations->{$product->variation} : $data;
            $product->data->price += $extra_options->price;
            if ($product->data->sale_price !== '') {
                $product->data->sale_price += $extra_options->price;
            }
            $wishlist->quantity++;


            $isBooking = isset($data->app_type) && $data->app_type == 'booking';
            if ($isBooking && !empty($product->booking->guests)) {
                $quantity = $product->booking->guests;
            } else if ($isBooking && $data->booking->type == 'multiple') {
                $delta = strtotime($product->booking->dates[1]) - strtotime($product->booking->dates[0]);
                $quantity = $delta / 60 / 60 / 24;
            } else if (!$isBooking) {
                $quantity = 1;
            }


            $product->prices = self::prepareProductPrices($product->product_id, $product->data->price, $product->data->sale_price,
                $product->variation, $quantity);
            $price = $product->prices->sale_price !== '' ? $product->prices->sale_price : $product->prices->price;
            $product->variations = [];
            $product->images = [];
            if (!empty($product->variation)) {
                $vars = explode('+', $product->variation);
                $variationsURL = [];
                $variationsMap = self::$storeHelper->getProductVariationsMap($product->product_id);
                $images = new stdClass();
                foreach ($variationsMap as $value) {
                    $images->{$value->option_key} = json_decode($value->images);
                }
                foreach ($vars as $value) {
                    $query = $db->getQuery(true)
                        ->select('fd.value, fd.color, fd.image, f.title, f.field_type')
                        ->from('#__gridbox_store_products_fields_data AS fd')
                        ->where('fd.option_key = '.$db->quote($value))
                        ->leftJoin('#__gridbox_store_products_fields AS f ON f.id = fd.field_id');
                    $db->setQuery($query);
                    $variationObj = $db->loadObject();
                    $variationsURL[] = $variationObj->title.'='.$variationObj->value;
                    $product->variations[] = $variationObj;
                    if (!empty($images->{$value})) {
                        $product->images = $images->{$value};
                    }
                }
                $product->variationURL = implode('&', $variationsURL);
            }
        }

        return $wishlist;
    }

    public static function checkProductTaxMap($id, $categories)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('page_category')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $category = $db->loadResult();
        $flag = self::checkProductCategory($category, $categories);

        return $flag;
    }

    public static function getTaxRegion($regions, $region)
    {
        $result = null;
        foreach ($regions as $value) {
            if ($value->state_id == $region) {
                $result = $value;
                break;
            }
        }

        return $result;
    }

    public static function calculateProductTax($id, $price, $cart, $country = true, $region = true, $category = true)
    {
        $obj = null;
        $array = $category ? self::$taxRates->categories : self::$taxRates->empty;
        foreach ($array as $tax) {
            $count = $country ? $tax->country_id == $cart->country : true;
            $cat = $category ? self::checkProductTaxMap($id, $tax->categories) : true;
            $reg = $region ? self::getTaxRegion($tax->regions, $cart->region) : true;
            if ($count && $cat && $reg) {
                $rate = !empty($reg->rate) ? $reg->rate : $tax->rate;
                $obj = new stdClass();
                $obj->key = $tax->key;
                $obj->title = $tax->title;
                $obj->rate = $rate;
                $obj->amount = self::$store->tax->mode == 'excl' ? $price * ($rate / 100) : $price - $price / ($rate / 100 + 1);
                break;
            }
        }
        if (!$obj && $country && $region && $category) {
            $obj = self::calculateProductTax($id, $price, $cart, true, false, true);
        } else if (!$obj && $country && !$region && $category) {
            $obj = self::calculateProductTax($id, $price, $cart, true, true, false);
        } else if (!$obj && $country && $region && !$category) {
            $obj = self::calculateProductTax($id, $price, $cart, true, false, false);
        } else if (!$obj && $country && !$region && !$category) {
            $obj = self::calculateProductTax($id, $price, $cart, false, false, true);
        } else if (!$obj && !$country && !$region && $category) {
            $obj = self::calculateProductTax($id, $price, $cart, false, false, false);
        }

        return $obj;
    }

    public static function getStoreCart($id)
    {
        $db = JFactory::getDbo();
        $cart = self::getStoreCartObject($id);
        $cart->products = self::getStoreCartProducts($id);
        $cart->subtotal = 0;
        $cart->tax = 0;
        $cart->total = 0;
        $cart->discount = 0;
        $cart->taxes = new stdClass();
        $cart->taxes->count = 0;
        $cart->later = 0;
        if (!empty($cart->promo_id)) {
            $db = JFactory::getDbo();
            $query = self::getPromoCodeQuery()
                ->select('p.id, p.title, p.unit, p.discount, p.applies_to, p.disable_sales, p.access, pc.code')
                ->where('p.id = '.$db->quote($cart->promo_id));
            $db->setQuery($query);
            $cart->promo = $db->loadObject();
        } else {
            $cart->promo = NULL;
        }
        $cart->validPromo = false;
        $cart->quantity = 0;
        $cart->net_amount = 0;
        $expires = ['h' => JText::_('HOURS'), 'd' => JText::_('DAYS'), 'm' => JText::_('MONTHS'), 'y' => JText::_('YEARS')];
        foreach ($cart->products as $i => $product) {
            $product_id = $product->product_id;
            $data = self::$storeHelper->getProductData($product_id);
            $product->min = $data->min;
            if ((!empty($product->variation) && !isset($data->variations->{$product->variation}))) {
                self::removeProductFromCart($product->id, $id);
                unset($cart->products[$i]);
                continue;
            }
            $extra_options = new stdClass();
            $extra_options->count = 0;
            $extra_options->price = 0;
            $extra_options->items = new stdClass();
            $removeFlag = false;
            $product->hasFileQty = false;
            foreach ($product->extra_options as $key => $value) {
                if (!isset($data->extra_options->{$value->field_id})
                    || (!isset($data->extra_options->{$value->field_id}->items->{$key})
                        && !isset($value->attachments) && !isset($value->text))) {
                    $removeFlag = true;
                    break;
                } else {
                    $obj = $data->extra_options->{$value->field_id};
                    if (!isset($extra_options->items->{$value->field_id})) {
                        $object = new stdClass();
                        $object->title = $obj->title;
                        $object->required = $obj->required == '1';
                        $object->values = new stdClass();
                        $extra_options->items->{$value->field_id} = $object;
                    } else {
                        $object = $extra_options->items->{$value->field_id};
                    }
                    $extra_options->count++;
                    $option = new stdClass();
                    if ($obj->type == 'file') {
                        $object->attachments = $value->files;
                        $key = 0;
                    } else if ($obj->type == 'textarea' || $obj->type == 'textinput') {
                        $key = 0;
                    }
                    $option->price = $obj->items->{$key}->price;
                    $option->weight = isset($obj->items->{$key}->weight) ? $obj->items->{$key}->weight : '';
                    if ($obj->type == 'file') {
                        $object->charge = $obj->file_options->charge;
                        $object->quantity = $obj->file_options->quantity;
                        $option->price = $obj->file_options->charge && !empty($option->price) ? ($option->price * count($value->files)) : $option->price;
                    } else if ($obj->type == 'textarea' || $obj->type == 'textinput') {
                        $option->value = $value->text;
                        $object->values->{$key} = $option;
                    } else {
                        $option->value = $obj->items->{$key}->title;
                        $object->values->{$key} = $option;
                    }
                    if ($obj->type == 'file' && $object->quantity) {
                        $product->hasFileQty = true;
                    }
                    if (!empty($option->price)) {
                        $extra_options->price += $option->price * 1;
                    }
                }
            }
            $isBooking = isset($data->app_type) && $data->app_type == 'booking';
            if ($isBooking) {
                $booking = self::getBooking();
            }
            if ($isBooking && !isset($product->booking->dates)) {
                $removeFlag = true;
            } else if ($isBooking && $data->booking->type == 'single' && $data->booking->single->time == 'yes') {
                $removeFlag = $booking->isBlockedSlot($product->booking->dates[0], $product->booking->time->start, $product->product_id);
            } else if ($isBooking && $data->booking->type == 'single') {
                $date = JDate::getInstance($product->booking->dates[0]);
                $removeFlag = $booking->isBlockedDay($date, $product->product_id);
            } else if ($isBooking) {
                $days = (new DateTime($product->booking->dates[1]))->diff(new DateTime($product->booking->dates[0]))->days;
                $date = JDate::getInstance($product->booking->dates[0]);
                for ($i = 0; $i <= $days; $i++) {
                    $removeFlag = $booking->isBlockedDay($date, $product->product_id, true);
                    if ($removeFlag) {
                        break;
                    }
                    $date->modify('+1 day');
                }
            }
            if ($removeFlag) {
                
                self::removeProductFromCart($product->id, $id);
                unset($cart->products[$i]);
                $removeFlag = false;
                continue;
            }
            $product->dimensions = $data->dimensions;
            $product->extra_options = $extra_options;
            $product->data = !empty($product->variation) ? $data->variations->{$product->variation} : $data;
            if ($product->data->stock !== '' && $product->quantity > $product->data->stock) {
                $product->quantity = $product->data->stock * 1;
            }
            if (!empty($product->renew_id) || !empty($product->upgrade_id)) {
                $subscription = json_decode($product->data->subscription);
            }
            if (!empty($product->upgrade_id)) {
                $upgrade = self::$storeHelper->getCartUpgrade($product->upgrade_id, $product_id, $product->data);
                $product->data->sale_price = '';
                $product->data->price = $upgrade ? $upgrade->price : 0;
            }
            
            if ($product->quantity == 0 || $product->quantity < $product->min ||
                (!empty($product->renew_id) && !isset($subscription->renew->plans->{$product->plan_key})) ||
                (!empty($product->upgrade_id) && (!$upgrade || $product->data->price <= 0))) {
                self::removeProductFromCart($product->id, $id);
                unset($cart->products[$i]);
                continue;
            }
            if (!empty($product->renew_id)) {
                $plan = $subscription->renew->plans->{$product->plan_key};
                $product->title .= ' ('.$plan->length->value.' '.$expires[$plan->length->format].')';
                $product->data->sale_price = '';
                $product->data->price = $plan->price;
            }
            $cart->quantity += $product->quantity * 1;
            if ($isBooking && !empty($product->booking->guests)) {
                $product->quantity = $product->booking->guests;
            } else if ($isBooking && $data->booking->type == 'multiple') {
                $delta = strtotime($product->booking->dates[1]) - strtotime($product->booking->dates[0]);
                $product->quantity = $delta / 60 / 60 / 24;
            }
            $productData = $product->data;
            $productData->price += $extra_options->price;
            if ($productData->sale_price !== '') {
                $productData->sale_price += $extra_options->price;
            }
            $productData->single =  self::prepareProductPrices($product_id, $productData->price, $productData->sale_price, $product->variation);
            $productData->price = $productData->price * $product->quantity;
            if ($productData->sale_price !== '') {
                $productData->sale_price = $productData->sale_price * $product->quantity;
            }
            $price = $productData->price;
            $sale_price = $productData->sale_price;
            $product->prices = self::prepareProductPrices($product_id, $price, $sale_price, $product->variation);
            $price = $product->prices->sale_price !== '' ? $product->prices->sale_price : $product->prices->price;
            $cart->subtotal += $price;
            $product->tax = self::calculateProductTax($product_id, $price, $cart);
            
            $cart->total += $price;
            $product->variations = [];
            $product->images = [];
            if (!empty($product->variation)) {
                $vars = explode('+', $product->variation);
                $variationsURL = [];
                $variationsMap = self::$storeHelper->getProductVariationsMap($product_id);
                $images = new stdClass();
                foreach ($variationsMap as $value) {
                    $images->{$value->option_key} = json_decode($value->images);
                }
                foreach ($vars as $value) {
                    $query = $db->getQuery(true)
                        ->select('fd.value, fd.color, fd.image, f.title, f.field_type')
                        ->from('#__gridbox_store_products_fields_data AS fd')
                        ->where('fd.option_key = '.$db->quote($value))
                        ->leftJoin('#__gridbox_store_products_fields AS f ON f.id = fd.field_id');
                    $db->setQuery($query);
                    $variationObj = $db->loadObject();
                    $variationsURL[] = $variationObj->title.'='.$variationObj->value;
                    $product->variations[] = $variationObj;
                    if (!empty($images->{$value})) {
                        $product->images = $images->{$value};
                    }
                }
                $product->variationURL = implode('&', $variationsURL);
            }
        }
        self::calculateProductsDiscount($cart);
        if (!$cart->validPromo && !empty($cart->promo_id)) {
            $obj = new stdClass();
            $obj->id = $id;
            $obj->promo_id = 0;
            $db->updateObject('#__gridbox_store_cart', $obj, 'id');
        }

        return $cart;
    }

    public static function calculateProductDiscount($price, $cart, $sale, $totalCount)
    {
        $value = $sale->discount;
        $unit = $sale->unit;
        $discount = $unit == '%' ? $price * ($value / 100) : $value / $totalCount;
        $price -= $discount;
        $cart->discount += $discount;

        return $price;
    }

    public static function calculateProductsDiscount($cart)
    {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $discounts = [];
        foreach (self::$storeHelper->sales as $sale) {
            if ($sale->applies_to != 'cart' || empty($sale->discount) || empty($sale->cart_discount) || !in_array($sale->access, $groups)) {
                continue;
            }
            $discounts[] = $sale;
        }
        usort($discounts, function($a, $b){
            if ($a->cart_discount == $b->cart_discount) {
                return 0;
            }
            return ($a->cart_discount < $b->cart_discount) ? 1 : -1;
        });
        $sale = null;
        foreach ($discounts as $discount) {
            if ($cart->total < $discount->cart_discount) {
                continue;
            }
            $sale = $discount;
            break;
        }
        $saleProducts = $sale ? count($cart->products) : 0;
        $promoProducts = 0;
        foreach ($cart->products as $product) {
            $product->promo = $cart->promo && self::checkPromoCode($cart->promo, $product);
            if ($product->promo) {
                $promoProducts++;
            }
        }
        foreach($cart->products as $product) {
            $price = $product->prices->sale_price !== '' ? $product->prices->sale_price : $product->prices->price;
            if ($sale) {
                $price = self::calculateProductDiscount($price, $cart, $sale, $saleProducts);
            }
            if ($product->promo) {
                $cart->validPromo = true;
                $price = self::calculateProductDiscount($price, $cart, $cart->promo, $promoProducts);
            }
            $product->net_price = $price;
            if ($product->tax) {
                $amount = $product->tax->amount;
                $rate = $product->tax->rate;
                if ($product->promo || $sale) {
                    $amount = self::$store->tax->mode == 'excl' ? $price * ($rate / 100) : $price - $price / ($rate / 100 + 1);
                }
                $cart->tax += $amount;
                $product->net_price = self::$store->tax->mode == 'excl' ? $price : $price - $amount;
                if (!isset($cart->taxes->{$product->tax->key})) {
                    $cart->taxes->{$product->tax->key} = new stdClass();
                    $cart->taxes->{$product->tax->key}->title = $product->tax->title;
                    $cart->taxes->{$product->tax->key}->rate = $rate;
                    $cart->taxes->{$product->tax->key}->amount = $cart->taxes->{$product->tax->key}->net = 0;
                    $cart->taxes->count++;
                }
                $cart->taxes->{$product->tax->key}->amount += $amount;
                $cart->taxes->{$product->tax->key}->net += $product->net_price;
            }
            $cart->net_amount += $product->net_price * 1;

            $product->calc_price = $price;
            if (isset($product->data->app_type) && $product->data->app_type == 'booking' && $product->data->booking->payment->type == 'partial') {
                $payment = $product->data->booking->payment;
                $product->prepaid = $payment->unit == '%' ? $price * ($payment->value / 100) : $payment->value;
                $product->later = $price - $product->prepaid;
                $cart->later += $product->later;
            }
        }
        $cart->total -= $cart->discount;
    }

    public static function removeProductFromCart($product_id, $cart_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_cart_products')
            ->where('id = '.$product_id);
        $db->setQuery($query)
            ->execute();
        self::$storeHelper->removeProductAttachment($product_id, $cart_id);
    }

    public static function removeProductFromWishlist($product_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_store_wishlist_products')
            ->where('id = '.$product_id);
        $db->setQuery($query)
            ->execute();
    }

    public static function checkIconsLibrary($body)
    {
        $icons = '';
        $link = "\n\t<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\">";
        $href = JUri::root().'templates/gridbox/library/icons/%s/%s.css';
        $array = array('fa fa-', 'fab fa-', 'fal fa-', 'far fa-', 'fas fa-');
        $option = JFactory::getApplication()->input->getCmd('option', '');
        if ($option != 'com_gridbox' && JVERSION < '4.0.0') {
            $icons .= sprintf($link, JUri::root().'media/jui/css/icomoon.css');
        } else if ($option != 'com_gridbox') {
            $icons .= sprintf($link, JUri::root().'media/system/css/joomla-fontawesome.min.css');
        }
        foreach ($array as $value) {
            if (strpos($body, $value)) {
                $icons .= sprintf($link, sprintf($href, 'fontawesome', 'fontawesome'));
                break;
            }
        }
        if (strpos($body, 'zmdi zmdi-')) {
            $icons .= sprintf($link, sprintf($href, 'material', 'material'));
        }
        if (strpos($body, 'flaticon-')) {
            $icons .= sprintf($link, sprintf($href, 'outline', 'flaticon'));
        }

        return $icons;
    }

    public static function initItems($body)
    {
        $str = self::readFile(JPATH_ROOT.'/components/com_gridbox/assets/js/initItems.json');
        $items = json_decode($str);
        $src = array();
        $keys = new stdClass();
        $str = '';
        $aboutUs = self::aboutUs();
        $v = $aboutUs->version;
        preg_match_all('/ba-item-[\w-]+/', $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($items->{$match[0]}) && !isset($keys->{$match[0]})) {
                $keys->{$match[0]} = true;
                switch ($match[0]) {
                    case 'ba-item-counter':
                        $src[] = JUri::root().'components/com_gridbox/libraries/counter/counter.js?'.$v;
                        break;
                    case 'ba-item-scroll-to':
                        $src[] = JUri::root().'components/com_gridbox/libraries/smoothScroll/smoothScroll.js?'.$v;
                        break;
                    case 'ba-item-scroll-to-top':
                        $src[] = JUri::root().'components/com_gridbox/libraries/scrolltop/scrolltop.js?'.$v;
                        break;
                    case 'ba-item-countdown':
                        $src[] = JUri::root().'components/com_gridbox/libraries/countdown/countdown.js?'.$v;
                        break;
                    case 'ba-item-weather':
                        $src[] = JUri::root().'components/com_gridbox/libraries/weather/js/weather.js?'.$v;
                        break;
                    case 'ba-item-social':
                        $src[] = JUri::root().'components/com_gridbox/libraries/social/social.js?'.$v;
                        break;
                    case 'ba-item-content-slider':
                    case 'ba-item-slideshow':
                    case 'ba-item-field-slideshow':
                    case 'ba-item-product-slideshow':
                        $src[] = JUri::root().'components/com_gridbox/libraries/slideshow/js/slideshow.js?'.$v;
                        break;
                    case 'ba-item-slideset':
                        $src[] = JUri::root().'components/com_gridbox/libraries/slideset/js/slideset.js?'.$v;
                        break;
                    case 'ba-item-carousel':
                    case 'ba-item-recent-posts-slider':
                    case 'ba-item-related-posts-slider':
                    case 'ba-item-recently-viewed-products':
                        $src[] = JUri::root().'components/com_gridbox/libraries/carousel/js/carousel.js?'.$v;
                        break;
                    case 'ba-item-testimonials':
                        $src[] = JUri::root().'components/com_gridbox/libraries/testimonials/js/testimonials.js?'.$v;
                        break;
                }
                $link = JUri::root().'components/com_gridbox/libraries/modules/'.$items->{$match[0]}.'.js?'.$v;
                if (!in_array($link, $src)) {
                    $src[] = $link;
                }
            }
        }
        foreach ($src as $value) {
            $str .= "\n\t<script src=\"".$value."\"></script>";
        }
        if (!empty($str)) {
            $initItems = JUri::root().'components/com_gridbox/libraries/modules/initItems.js?'.$v;
            $str = "\n\t<script src=\"".$initItems."\"></script>".$str;
        }

        return $str;
    }

    public static function getSystemApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title')
            ->from('#__gridbox_app')
            ->where('type = '.$db->quote('system_apps'))
            ->order('id ASC');
        $db->setQuery($query);
        $system = $db->loadObjectList();
        $object = new stdClass;
        foreach ($system as $obj) {
            $object->{$obj->title} = true;
        }
        self::$systemApps = $object;
    }

    public static function getDesktopFieldFiles($id = 0)
    {
        $app = JFactory::getApplication();
        if (empty($id)) {
            $id = $app->input->get('id', 0, 'int');
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $app_id = $db->loadResult();
        $items = new stdClass();
        if ($app_id) {
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
        }

        return $items;
    }

    public static function getDesktopSavedFieldFiles($id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields_desktop_files')
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $items = new stdClass();
        foreach ($files as $file) {
            $items->{$file->id} = $file;
        }

        return $items;
    }

    public static function checkSitemap()
    {
        if (self::$website->enable_sitemap == 1) {
            self::createSitemap();
        }
    }

    public static function createSitemap()
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/sitemap.php';
        $sitemap = new gridboxSitemapHelper();
        $sitemap->create();
    }

    public static function checkUserEditLevel($action = 'core.edit')
    {
        if (!JFactory::getUser()->authorise($action, 'com_gridbox')) {
            exit;
        }
    }

    public static function getCommentsEditList($user)
    {
        $list = array();
        if ($user->type != 'guest') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_comments')
                ->where('user_type = '.$db->quote($user->type))
                ->where('user_id = '.$db->quote($user->id));
            $db->setQuery($query);
            $list = $db->loadObjectList();
        }

        return $list;
    }

    public static function getReviewsEditList($user)
    {
        $list = array();
        if ($user->type != 'guest') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_reviews')
                ->where('user_type = '.$db->quote($user->type))
                ->where('user_id = '.$db->quote($user->id));
            $db->setQuery($query);
            $list = $db->loadObjectList();
        }

        return $list;
    }

    public static function getCommentLikeStatus($id)
    {
        $db = JFactory::getDbo();
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $db->getQuery(true)
            ->select('status')
            ->from('#__gridbox_comments_likes_map')
            ->where('ip = '.$db->quote($ip))
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $status = $db->loadResult();

        return $status;
    }

    public static function getReviewLikeStatus($id)
    {
        $db = JFactory::getDbo();
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $db->getQuery(true)
            ->select('status')
            ->from('#__gridbox_reviews_likes_map')
            ->where('ip = '.$db->quote($ip))
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $status = $db->loadResult();

        return $status;
    }

    public static function getCommentAttachments($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_attachments')
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $dir = JUri::root().'components/com_gridbox/assets/uploads/comments/';
        $obj = new stdClass();
        $obj->files = array();
        $obj->images = array();
        foreach ($files as $key => $file) {
            $file->link = $dir.$file->filename;
            if ($file->type == 'file') {
                $obj->files[] = $file;
            } else {
                $obj->images[] = $file;
            }
        }

        return $obj;
    }

    public static function getReviewAttachments($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_reviews_attachments')
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $dir = JUri::root().'components/com_gridbox/assets/uploads/reviews/';
        $obj = new stdClass();
        $obj->files = array();
        $obj->images = array();
        foreach ($files as $key => $file) {
            $file->link = $dir.$file->filename;
            if ($file->type == 'file') {
                $obj->files[] = $file;
            } else {
                $obj->images[] = $file;
            }
        }

        return $obj;
    }

    public static function getDefaultComment($type)
    {

        $str = '';
        $avatar = JUri::root().'components/com_gridbox/assets/images/default-user.png';
        $message = 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';
        $status = '';
        $moderators = array();
        $comment = new stdClass();
        $comment->id = 0;
        $attachments = new stdClass();
        $attachments->files = $attachments->images = array();
        $comment->date = '12 '.JText::_('HOURS_AGO');
        $comment->rating = 5;
        $comment->name = 'Name';
        $comment->likes = $comment->dislikes = $comment->parent = 0;
        $comment->status = 'approved';
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/'.$type.'/'.$type.'-comment-pattern.php');
        $str .= $out;

        return $str;
    }

    public static function getCommentsCountHTML($id, $view, $sortBy)
    {
        if ($view == 'gridbox') {
            $count = 1;
        } else {
            $count = self::getCommentsCount($id);
        }
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/comments-box/comments-box-total-count-pattern.php');

        return $string;
    }

    public static function getReviewsCountHTML($id, $view, $sortBy)
    {
        if ($view == 'gridbox') {
            $count = 1;
            $rating = 5;
            $type = '';
        } else {
            $obj = self::getReviewsCount($id);
            $count = $obj->count;
            $rating = round($obj->rating ?? 0, 1);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.type')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$id)
                ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
            $db->setQuery($query);
            $type = $db->loadResult();
        }
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-total-count-pattern.php');

        return $string;
    }

    public static function getCommentsCount($id, $parent = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $moderators = self::$commentsModerators;
        $user = self::$commentUser;
        if (empty($user) || !$moderators || $user->type != 'user' || !in_array($user->id, $moderators)) {
            $query->select('id')
                ->from('#__gridbox_comments')
                ->where('page_id = '.$id)
                ->where('status = '.$db->quote('approved'))
                ->where('parent = '.$parent)
                ->order('date desc');
            $db->setQuery($query);
            $items = $db->loadObjectList();
            $count = 0;
            foreach ($items as $item) {
                $count++;
                $count += self::getCommentsCount($id, $item->id);
            }
        } else {
            $query->select('COUNT(id)')
                ->from('#__gridbox_comments')
                ->where('page_id = '.$id)
                ->order('date desc');
            $db->setQuery($query);
            $count = $db->loadResult();
        }

        return $count;
    }

    public static function getReviewsCount($id, $parent = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id) as count, AVG(rating) as rating')
            ->from('#__gridbox_reviews')
            ->where('page_id = '.$id)
            ->where('parent = '.$parent)
            ->order('date desc');
        $moderators = self::$reviewsModerators;
        $user = self::$commentUser;
        if (empty($user) || $user->type != 'user' || !in_array($user->id, $moderators)) {
            $query->where('status = '.$db->quote('approved'));
        }
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public static function getUserAvatar($email, $key, $author = null)
    {
        $avatar = '';
        if (!empty($email)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.avatar')
                ->from('#__gridbox_user_avatars AS a')
                ->where('u.email = '.$db->quote($email))
                ->leftJoin('#__users AS u ON u.id = a.user_id');
            $db->setQuery($query);
            $avatar = $db->loadResult();
        }
        $avatar = $author->avatar ?? $avatar;
        if (!empty($avatar) && !self::isExternal($avatar)) {
            $avatar = JUri::root().$avatar;
        } else if (empty($avatar) && self::$website->{$key} == 1 && !empty($email)) {
            $avatar = JUri::root().'components/com_gridbox/assets/images/default-user.png';
            $hash = md5(strtolower(trim($email)));
            $avatar = "https://www.gravatar.com/avatar/".$hash."?d=".$avatar."&s=50";
        } else if (empty($avatar)) {
            $avatar = JUri::root().'components/com_gridbox/assets/images/default-user.png';
        }

        return $avatar;
    }

    public static function getCommentsLoginedUserHTML($obj, $type)
    {
        if (empty($obj->avatar) || !empty($obj->id)) {
            $author = self::getAuthor($obj->id);
            $avatar = self::getUserAvatar($obj->email, $type != 'reviews' ? 'enable_gravatar' : 'reviews_enable_gravatar', $author);
        } else {
            $avatar = $obj->avatar;
        }
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/'.$type.'/'.$type.'-logined-user-pattern.php');
        $str = $string;

        return $str;
    }

    public static function getCommentsLogoutedUserHTML($type)
    {
        $avatar = JUri::root().'components/com_gridbox/assets/images/default-user.png';
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/'.$type.'/'.$type.'-logouted-user-pattern.php');
        $str = $string;

        return $str;
    }

    public static function setcookie($name, $value, $time = 0)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $cookie_path = $app->get('cookie_path', '/');
        $cookie_domain = $app->get('cookie_domain');
        $ssl = $app->isSSLConnection();
        if (phpversion() >= '7.3.0') {
            $options = ['expires' => $time, 'path' => $cookie_path, 'domain' => $cookie_domain,
                'secure' => $ssl, 'httponly' => true, 'samesite' => 'Lax'];
            setcookie($name, $value, $options);
        } else {
            setcookie($name, $value, $time, $cookie_path, $cookie_domain, $ssl, true);
        }
    }

    public static function setCommentsUser($value)
    {
        self::setcookie('gridbox-comments-user', $value);
    }

    public static function getCommentsUser()
    {
        $input = JFactory::getApplication()->input;
        $user = $input->cookie->get('gridbox-comments-user', '', 'string');

        return $user;
    }

    public static function removeCommentsUser()
    {
        self::setcookie('gridbox-comments-user', '', time() - 3600);
    }

    public static function setCommentUser()
    {
        $JUser = JFactory::getUser();
        $user = self::getCommentsUser();
        if (!empty($user)) {
            $object = json_decode($user);
        } else {
            $object = new stdClass();
            $object->id = 0;
            $object->type = 'empty';
        }
        if (!empty($user) && $object->type == 'user' && empty($JUser->id)) {
            self::removeCommentsUser();
            $object = new stdClass();
            $object->id = 0;
            $object->type = 'empty';
            $user = '';
        }
        if (!empty($JUser->id) && ($object->id != $JUser->id || $object->type != 'user')) {
            $data = new stdClass();
            $data->name = $JUser->name;
            $data->email = $JUser->email;
            $data->avatar = '';
            $data->id = $JUser->id;
            $data->type = 'user';
            $value = json_encode($data);
            $user = $value;
            self::setCommentsUser($value);
        }
        if (!empty($user)) {
            $data = json_decode($user);
            if (!empty($JUser->id)) {
                $data->email = $JUser->email;
                $author = self::getAuthor($JUser->id);
                $data->avatar = $author->avatar ?? $data->avatar;
                $data->name = $author->title ?? $data->name;
            }
            self::$commentUser = $data;
        }
    }

    public static function getAuthor($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_authors')
            ->where('user_id = '.$id);
        $db->setQuery($query);
        $author = $db->loadObject();

        return $author;
    }

    public static function getCommentsUserLoginHTML($type)
    {
        self::setCommentUser();
        $obj = new stdClass();
        $obj->status = '';
        if (!empty(self::$commentUser)) {
            $obj->status = 'login';
            $obj->str = self::getCommentsLoginedUserHTML(self::$commentUser, $type);
        } else {
            $obj->str = self::getCommentsLogoutedUserHTML($type);
        }

        return $obj;
    }

    public static function setCommentsModerators()
    {
        if (self::$website->comments_moderator_admins == 'super_user') {
            $db = JFactory::getDbo();
            $moderators = array();
            $query = $db->getQuery(true)
                ->select('u.id, u.name, g.id as level')
                ->from('`#__users` AS u')
                ->leftJoin('`#__user_usergroup_map` AS m ON u.id = m.user_id')
                ->leftJoin('`#__usergroups` AS g ON g.id = m.group_id');
            $db->setQuery($query);
            $users = $db->loadObjectList();
            foreach ($users as $value) {
                if ($value->level == 8) {
                    $moderators[] = $value->id;
                }
            }
        } else if (!empty(self::$website->comments_moderator_admins)) {
            $moderators = explode(',', self::$website->comments_moderator_admins);
        } else {
            $moderators = [];
        }
        self::$commentsModerators = $moderators;
    }

    public static function setReviewsModerators()
    {
        if (self::$website->reviews_moderator_admins == 'super_user') {
            $db = JFactory::getDbo();
            $moderators = array();
            $query = $db->getQuery(true)
                ->select('u.id, u.name, g.id as level')
                ->from('`#__users` AS u')
                ->leftJoin('`#__user_usergroup_map` AS m ON '.$db->quoteName('u.id').' = '.$db->quoteName('m.user_id'))
                ->leftJoin('`#__usergroups` AS g ON '.$db->quoteName('g.id').' = '.$db->quoteName('m.group_id'));
            $db->setQuery($query);
            $users = $db->loadObjectList();
            foreach ($users as $value) {
                if ($value->level == 8) {
                    $moderators[] = $value->id;
                }
            }
        } else if (!empty(self::$website->reviews_moderator_admins)) {
            $moderators = explode(',', self::$website->reviews_moderator_admins);
        } else {
            $moderators = [];
        }
        self::$reviewsModerators = $moderators;
    }

    public static function getComments($id, $parent = 0, $level = 0, $replyName = '')
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $sort = $input->get('sort-by', 'oldest', 'string');
        $moderators = self::$commentsModerators;
        $user = self::$commentUser;
        $order = 'c.date desc';
        if ($sort == 'oldest') {
            $order = 'c.date asc';
        } else if ($sort == 'popular') {
            $order = 'c.likes desc';
        }
        $query = $db->getQuery(true)
            ->select('c.*, u.email AS user_email')
            ->from('#__gridbox_comments AS c')
            ->where('c.page_id = '.$id)
            ->where('c.parent = '.$parent)
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->order($order);
        if (empty($user) || $user->type != 'user' || !in_array($user->id, $moderators)) {
            $query->where('c.status = '.$db->quote('approved'));
        }
        $db->setQuery($query);
        $comments = $db->loadObjectList();
        $str = '';
        $level++;
        foreach ($comments as $comment) {
            if (empty($comment->avatar)) {
                if ($comment->user_type == 'user' && !empty($comment->user_email)) {
                    $comment->email = $comment->user_email;
                }
                $author = self::getAuthor($comment->user_id);
                $comment->name = $author->title ?? $comment->name;
                $avatar = self::getUserAvatar($comment->email, 'enable_gravatar', $author);
            } else {
                $avatar = $comment->avatar;
            }
            $message = str_replace("\n", '<br>', $comment->message);
            $status = self::getCommentLikeStatus($comment->id);
            $attachments = self::getCommentAttachments($comment->id);
            $time = time() - strtotime($comment->date);
            $hour = 60 * 60;
            if ($time < 60) {
                $comment->date = '1 '.JText::_('MINUTES_AGO');
            } else if ($time < $hour) {
                $comment->date = floor($time / 60).' '.JText::_('MINUTES_AGO');
            } else if ($time < 86400) {
                $comment->date = floor($time / $hour).' '.JText::_('HOURS_AGO');
            } else {
                $comment->date = self::formatDate($comment->date);
            }
            include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/comments-box/comments-box-comment-pattern.php');
            $str .= $out;
            $reply = self::getComments($id, $comment->id, $level, $comment->name);
            if (!empty($reply)) {
                if ($level < 3) {
                    $str .= '<div class="ba-comment-reply-wrapper">';
                }
                $str .= $reply;
                if ($level < 3) {
                    $str .= '</div>';
                }
            }
        }

        return $str;
    }

    public static function getReview($id)
    {
        $moderators = self::$reviewsModerators;
        $user = self::$commentUser;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, u.email AS user_email')
            ->from('#__gridbox_reviews AS c')
            ->where('c.id = '.$id)
            ->leftJoin('#__users AS u ON u.id = c.user_id');
        if (empty($user) || $user->type != 'user' || !in_array($user->id, $moderators)) {
            $query->where('c.status = '.$db->quote('approved'));
        }
        $db->setQuery($query);
        $review = $db->loadObject();

        return $review;
    }

    public static function getReviewById($id, $replyName = '')
    {
        $moderators = self::$reviewsModerators;
        $user = self::$commentUser;
        $comment = self::getReview($id);
        if (empty($comment->avatar)) {
            if ($comment->user_type == 'user' && !empty($comment->user_email)) {
                $comment->email = $comment->user_email;
            }
            $author = self::getAuthor($comment->user_id);
            $comment->name = $author->title ?? $comment->name;
            $avatar = self::getUserAvatar($comment->email, 'reviews_enable_gravatar', $author);
        } else {
            $avatar = $comment->avatar;
        }
        $message = str_replace("\n", '<br>', $comment->message);
        $status = self::getReviewLikeStatus($comment->id);
        $attachments = self::getReviewAttachments($comment->id);
        $time = time() - strtotime($comment->date);
        $hour = 60 * 60;
        if ($time < 60) {
            $comment->date = '1 '.JText::_('MINUTES_AGO');
        } else if ($time < $hour) {
            $comment->date = floor($time / 60).' '.JText::_('MINUTES_AGO');
        } else if ($time < 86400) {
            $comment->date = floor($time / $hour).' '.JText::_('HOURS_AGO');
        } else {
            $comment->date = self::formatDate($comment->date);
        }
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-comment-pattern.php');
        
        return $out;
    }

    public static function getReviews($id, $parent = 0, $level = 0, $replyName = '', $active = 1, $replyLimit = 2, $reviewID = 0)
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $sort = $input->get('sort-by', 'recent', 'string');
        $moderators = self::$reviewsModerators;
        $user = self::$commentUser;
        $order = 'c.date desc';
        $limit = 10 * $active;
        if (!empty($reviewID)) {
            self::$review = self::getReview($reviewID);
            $reviews = self::getReviewsCount($id);
            $limit = $reviews->count * 1;
        }
        if (!empty(self::$review) && self::$review->parent == $parent) {
            $replyLimit = 0;
        }
        if ($sort == 'oldest') {
            $order = 'c.date asc';
        } else if ($sort == 'popular') {
            $order = 'c.likes desc';
        }
        $query = $db->getQuery(true)
            ->select('c.*, u.email AS user_email')
            ->from('#__gridbox_reviews AS c')
            ->where('c.page_id = '.$id)
            ->where('c.parent = '.$parent)
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->order($order);
        if (empty($user) || $user->type != 'user' || !in_array($user->id, $moderators)) {
            $query->where('c.status = '.$db->quote('approved'));
        }
        if ($level == 0) {
            $db->setQuery($query, 0, $limit);
        } else {
            $db->setQuery($query, 0, $replyLimit);
        }
        $comments = $db->loadObjectList();
        $str = '';
        $level++;
        foreach ($comments as $comment) {
            if (empty($comment->avatar)) {
                if ($comment->user_type == 'user' && !empty($comment->user_email)) {
                    $comment->email = $comment->user_email;
                }
                $author = self::getAuthor($comment->user_id);
                $comment->name = $author->title ?? $comment->name;
                $avatar = self::getUserAvatar($comment->email, 'reviews_enable_gravatar', $author);
            } else {
                $avatar = $comment->avatar;
            }
            $message = str_replace("\n", '<br>', $comment->message);
            $status = self::getReviewLikeStatus($comment->id);
            $attachments = self::getReviewAttachments($comment->id);
            $time = time() - strtotime($comment->date);
            $hour = 60 * 60;
            if ($time < 60) {
                $comment->date = '1 '.JText::_('MINUTES_AGO');
            } else if ($time < $hour) {
                $comment->date = floor($time / 60).' '.JText::_('MINUTES_AGO');
            } else if ($time < 86400) {
                $comment->date = floor($time / $hour).' '.JText::_('HOURS_AGO');
            } else {
                $comment->date = self::formatDate($comment->date);
            }
            include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-comment-pattern.php');
            $str .= $out;
            $reply = self::getReviews($id, $comment->id, $level, $comment->name);
            if (!empty($reply)) {
                $str .= '<div class="ba-comment-reply-wrapper">';
                $str .= $reply;
                $str .= '</div>';
            }
        }
        if ($level == 1) {
            $str .= self::getReviewsPagination($id, $limit, $active);
        } else {
            $str .= self::getReviewsReplyPagination($id, $replyLimit, $parent);
        }

        return $str;
    }

    public static function getReviewsPagination($id, $limit, $active)
    {
        $reviews = self::getReviewsCount($id);
        $count = $reviews->count * 1;
        if ($count == 0) {
            return '';
        }
        $pages = ceil($count / $limit);
        if ($pages == 1) {
            return '';
        }
        $html = '<span class="ba-load-more-reviews-btn" data-next="'.($active + 1).'">'.JText::_('LOAD_MORE').'</span>';

        return $html;
    }

    public static function getReviewsReplyPagination($id, $limit, $parent)
    {
        $reviews = self::getReviewsCount($id, $parent);
        $count = $reviews->count * 1;
        if ($count == 0 || $limit == 0 || $count <= $limit) {
            return '';
        }
        $html = '<span class="ba-view-more-replies">'.JText::_('VIEW_MORE_REPLIES').' ('.($count - $limit).')</span>';

        return $html;
    }

    public static function removeTmpAttachment($id, $filename)
    {
        if (!empty($id) && !empty($filename)) {
            $db = JFactory::getDbo();
            $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/comments/';
            if (JFile::exists($dir.$filename)) {
                JFile::delete($dir.$filename);
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_comments_attachments')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function removeTmpReviewsAttachment($id, $filename)
    {
        if (!empty($id) && !empty($filename)) {
            $db = JFactory::getDbo();
            $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/reviews/';
            if (JFile::exists($dir.$filename)) {
                JFile::delete($dir.$filename);
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_reviews_attachments')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function getPerformance()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('email_encryption, compress_html, compress_css, compress_js, page_cache,
                browser_cache, compress_images, images_max_size, images_quality, images_lazy_load,
                adaptive_images, adaptive_quality, enable_canonical, defer_loading')
            ->from('#__gridbox_website');
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public static function checkPreloader()
    {
        if (self::$website->preloader == 1 && isset(self::$systemApps->preloader)) {
            $system = self::getSystemParamsByType('preloader');
            if (!$system) {
                return;
            }
            self::checkSystemCss($system->id);
            $doc = JFactory::getDocument();
            $db = JFactory::getDbo();
            $input = JFactory::getApplication()->input;
            $query = $db->getQuery(true)
                ->select('html, items')
                ->from('#__gridbox_system_pages')
                ->where('id = '.$system->id);
            $db->setQuery($query);
            $obj = $db->loadObject();
            if (empty($obj->html)) {
                $obj->html = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/preloader.html');
                $obj->items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/preloader.json');
            }
            $data = json_decode($obj->items);
            $object = $data->{'item-15289771381'};
            if ($object->session->enable == true && $input->cookie->exists('gridbox-preloader')) {
                return;
            } else if ($object->session->enable == true && !$input->cookie->exists('gridbox-preloader')) {
                setcookie('gridbox-preloader', '1', time()+31104000);
            }
            $style = '<style>';
            $dir = JPATH_ROOT.'/components/com_gridbox/libraries/preloader/css/';
            $style .= self::readFile($dir.'preloader.css');
            if ($object->layout == 'spinner') {
                $style .= "\n";
                $type = str_replace('ba-', '', $object->spinner);
                $style .= self::readFile($dir.$type.'.css');
            }
            $style .= "\n";
            $style .= self::readFile($dir.$object->animation.'.css');
            $style .= "\n".self::$css->getPreloaderRules($object->desktop, 'item-15289771381');
            $style .= '</style>';
            $html = self::clearDOM($obj->html, $data);
            echo $style.$html;
        }
    }

    public static function getPageType($id, $view = 'gridbox', $edit_type = '')
    {
        $type = '';
        if (empty($edit_type) && $view == 'gridbox') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.type')
                ->from('#__gridbox_pages AS p')
                ->leftJoin('#__gridbox_app AS a ON p.app_id = a.id')
                ->where('p.id = '.$id);
            $db->setQuery($query);
            $type = $db->loadResult();
        }

        return $type;
    }

    public static function getPageClass($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('class_suffix')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $suffix = $db->loadResult();
        if (!$suffix) {
            $suffix = '';
        }

        return $suffix;
    }

    public static function setAppLicense($data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        if (empty($data)) {
            unset($balbooa->key->data);
        }
        $balbooa->key = json_encode($balbooa->key);
        $db->updateObject('#__gridbox_api', $balbooa, 'id');
    }

    public static function loadUsedCSS($body, $view = '', $option = '')
    {
        if ($view == 'editor' && $option == 'com_gridbox') {
            return '';
        }
        $files = [];
        $plugins = [
            'ba-item-currency-switcher' => [
                'currency-switcher'
            ],
            'ba-item-language-switcher' => [
                'language-switcher'
            ],
            'ba-item-accordion' => [
                'accordion', 'accordion-tabs'
            ],
            'ba-item-logo' => [
                'logo'
            ],
            'ba-item-submission-form' => [
                'submission-form'
            ],
            'ba-item-overlay-section' => [
                'overlay-section', 'lightbox-backdrop'
            ],
            'ba-item-before-after-slider' => [
                'before-after-slider'
            ],
            'ba-item-progress-bar' => [
                'progress-bar'
            ],
            'ba-item-reading-progress-bar' => [
                'reading-progress-bar'
            ],
            'ba-item-hotspot' => [
                'hotspot'
            ],
            'ba-item-progress-pie' => [
                'progress-pie'
            ],
            'ba-item-scroll-to-top' => [
                'scroll-to-top'
            ],
            'ba-item-search' => [
                'search', 'live-search', 'product-rows'
            ],
            'ba-item-simple-gallery' => [
                'simple-gallery', 'image-caption'
            ],
            'ba-item-product-gallery' => [
                'simple-gallery', 'image-caption'
            ],
            'ba-item-scroll-to' => [
                'smooth-scrolling'
            ],
            'ba-item-tabs' => [
                'tabs', 'accordion-tabs'
            ],
            'ba-item-field-video' => [
                'video'
            ],
            'ba-item-video' => [
                'video'
            ],
            'ba-item-weather' => [
                'weather'
            ],
            'ba-item-testimonials' => [
                'testimonials', 'sliders-plugin'
            ],
            'ba-item-social' => [
                'social-share'
            ],
            'ba-item-social-icons' => [
                'social-icons'
            ],
            'ba-item-main-menu' => [
                'menu'
            ],
            'ba-item-forms' => [
                'forms'
            ],
            'ba-item-gallery' => [
                'gallery'
            ],
            'ba-item-countdown' => [
                'countdown'
            ],
            'ba-item-counter' => [
                'counter'
            ],
            'ba-item-error-message' => [
                'error-message'
            ],
            'ba-item-feature-box' => [
                'feature-box'
            ],
            'ba-cookies' => [
                'cookies', 'lightbox', 'lightbox-backdrop'
            ],
            'ba-item-flipbox' => [
                'flipbox'
            ],
            'edit-page-btn' => [
                'edit-page'
            ],
            'ba-item-icon' => [
                'icon'
            ],
            'ba-item-icon-list' => [
                'icon-list'
            ],
            'ba-item-breadcrumbs' => [
                'breadcrumbs'
            ],
            'ba-item-add-to-cart' => [
                'add-to-cart'
            ],
            'ba-lightbox-backdrop' => [
                'lightbox', 'lightbox-backdrop'
            ],
            'parallax-wrapper' => [
                'parallax'
            ],
            'ba-shape-divider' => [
                'shape-divider'
            ],
            'ba-item-preloader' => [
                'preloader'
            ],
            'ba-item-event-calendar' => [
                'event-calendar', 'event-popup'
            ],
            'ba-item-headline' => [
                'headline'
            ],
            'system-message' => [
                'system-message'
            ],
            'side-navigation-menu' => [
                'side-navigation-menu'
            ],
            'ba-item-one-page-menu' => [
                'menu'
            ],
            'ba-item-store-search' => [
                'search', 'live-search', 'product-rows'
            ],
            'ba-item-map' => [
                'google-maps'
            ],
            'ba-item-field-google-maps' => [
                'google-maps'
            ],
            'ba-item-google-maps-places' => [
                'google-maps', 'event-popup'
            ],
            'ba-sticky-header' => [
                'sticky-header'
            ],
            'lazy-load-image' => [
                'lazy-load'
            ],
            'ba-item-comments-box' => [
                'comments', 'blog-posts-comments'
            ],
            'ba-item-reviews' => [
                'comments', 'blog-posts-comments'
            ],
            'ba-item-content-slider' => [
                'content-slider', 'sliders-plugin'
            ],
            'ba-item-field-simple-gallery' => [
                'simple-gallery', 'image-caption'
            ],
            'ba-item-fields-filter' => [
                'alert-tooltip', 'fields-filter'
            ],
            'ba-item-field' => [
                'alert-tooltip', 'field'
            ],
            'ba-item-field-group' => [
                'field'
            ],
            'sidebar-menu' => [
                'sidebar-header'
            ],
            'ba-item-post-tags' => [
                'tags'
            ],
            'ba-item-tags' => [
                'tags'
            ],
            'ba-overlay-slideshow-button' => [
                'ba-overlay-slideshow-button'
            ],
            'inpost-trigger-modal' => [
                'inpost'
            ],
            'k2' => [
                'k2'
            ],
            'hikashop' => [
                'hikashop'
            ],
            'kunena' => [
                'kunena'
            ],
            'virtuemart' => [
                'virtuemart'
            ],
            'ba-store-app-product' => [
                'store-app-product'
            ],
            'ba-checkout-authentication' => [
                'checkout-authentication', 'authentication-fields'
            ],
            'ba-item-checkout-form' => [
                'checkout', 'account', 'cart', 'alert-tooltip', 'product-rows'
            ],
            'ba-my-account' => [
                'checkout', 'account', 'alert-tooltip'
            ],
            'login-form' => [
                'authentication-fields', 'joomla-login'
            ],
            'ba-item-modules' => [
                'default-joomla'
            ],
            'ba-module-position' => [
                'default-joomla'
            ],
            'ba-item-cart' => [
                'cart', 'lightbox-backdrop', 'product-rows'
            ],
            'ba-item-wishlist' => [
                'cart', 'lightbox-backdrop', 'product-rows'
            ],
            '"tooltip"' => [
                'tooltip', 'alert-tooltip'
            ],
            'tip-wrap' => [
                'tooltip', 'alert-tooltip'
            ],
            'popover' => [
                'tooltip', 'alert-tooltip'
            ],
            'ba-item-text' => [
                'text'
            ],
            'ba-item-category-intro' => [
                'blog', 'post-intro'
            ],
            'ba-item-post-intro' => [
                'blog', 'post-intro', 'blog-posts-comments'
            ],
            'ba-item-categories' => [
                'categories', 'blog', 'blog-posts'
            ],
            'ba-item-post-navigation' => [
                'post-navigation', 'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-blog-posts' => [
                'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-recent-comments' => [
                'blog', 'blog-posts'
            ],
            'ba-item-recent-posts' => [
                'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-recent-posts-slider' => [
                'blog', 'blog-posts', 'sliders-plugin', 'blog-posts-comments'
            ],
            'ba-item-recent-reviews' => [
                'blog', 'blog-posts'
            ],
            'ba-item-recently-viewed-products' => [
                'blog', 'blog-posts', 'sliders-plugin', 'blog-posts-comments'
            ],
            'ba-item-related-posts' => [
                'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-related-posts-slider' => [
                'blog', 'blog-posts', 'sliders-plugin'
            ],
            'ba-item-search-result' => [
                'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-store-search-result' => [
                'blog', 'blog-posts', 'blog-posts-comments'
            ],
            'ba-item-author' => [
                'blog', 'blog-posts'
            ],
            'ba-item-carousel' => [
                'sliders-plugin'
            ],
            'ba-item-field-slideshow' => [
                'sliders-plugin'
            ],
            'ba-item-product-slideshow' => [
                'sliders-plugin'
            ],
            'ba-item-slideset' => [
                'sliders-plugin'
            ],
            'ba-item-slideshow' => [
                'sliders-plugin'
            ],
            'ba-button-wrapper' => [
                'button'
            ],
            'ba-item-login' => [
                'login'
            ],
            'ba-image-wrapper' => [
                'image-caption'
            ]
        ];
        foreach ($plugins as $plugin => $list) {
            if ($view == 'gridbox' || strpos($body, $plugin) !== false) {
                foreach ($list as $file) {
                    if (!in_array($file, $files)) {
                        $files[] = $file;
                    }
                }
            }
        }
        if ($option != 'com_gridbox' || $view == 'gridbox') {
            $files[] = 'default-joomla';
        }
        $dir = JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/css/';
        $css = self::readAnimationFile($files, $dir);
        $animations = ['bounceIn', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'bounceInBottom',
            'fadeIn', 'fadeInLeft', 'fadeInRight', 'fadeInUp', 'fadeInBottom', 'zoomIn',
            'ba-shutter-out-diagonal', 'ba-image-zoom-center', 'ba-image-rotate-right',
            'ba-circle-bottom-right', 'ba-circle-top-left'];
        $files = [];
        foreach ($animations as $animation) {
            $file = str_replace('ba-circle-top-left', 'ba-circle-animation', $animation);
            $file = str_replace('ba-circle-bottom-right', 'ba-circle-animation', $file);
            $file = str_replace('ba-image-zoom-center', 'ba-image-zoom-rotate', $file);
            $file = str_replace('ba-image-rotate-right', 'ba-image-zoom-rotate', $file);
            if ((strpos($body, $animation) !== false || $view == 'gridbox') && !in_array($file, $files)) {
                $files[] = $file;
            }
        }
        $dir = JPATH_ROOT.'/components/com_gridbox/libraries/animation/css/';
        $css .= self::readAnimationFile($files, $dir);

        return $css;
    }

    public static function readAnimationFile($files, $dir)
    {
        $css = '';
        foreach ($files as $file) {
            if (JFile::exists($dir.$file.'.css')) {
                $str = self::readFile($dir.$file.'.css');
                $str = preg_replace('/[\n\t\r]+/', '', $str);
                $css .= preg_replace('/ +/', ' ', $str);
            }
        }

        return $css;
    }

    public static function compressGridbox($body)
    {
        if (strpos($body, 'ba-item-error-message') !== false) {
            return $body;
        }
        $performance = self::getPerformance();
        if ($performance->enable_canonical == 1) {
            $body = self::setCanonical($body);
        }
        if (isset(self::$systemApps->performance)) {
            if ($performance->compress_js == 1) {
                $body = self::minifyJs($body);
            }
            if ($performance->compress_css == 1) {
                $body = self::minifyCss($body);
            }
            if ($performance->compress_images == 1 || $performance->adaptive_images == 1) {
                $content = self::compressImages($body);
                if ($content) {
                    $body = $content;
                }
            }
            if ($performance->images_lazy_load == 1) {
                $body = self::setLazyLoad($body);
            }
            if ($performance->defer_loading == 1) {
                $body = self::setDeferredLoading($body);
            }
            if ($performance->compress_html == 1 && strpos($body, 'ba-item-submission-form') === false) {
                $body = preg_replace('/[\n\t\r]+/', '', $body);
                $body = preg_replace('/ +/', ' ', $body);
            }
        }

        return $body;
    }

    public static function setLazyLoad($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $str = '.ba-section, .ba-row, .ba-grid-column, .slideshow-content, .ba-instagram-image';
        $str .= ', .testimonials-img, .ba-blog-post-image > a, .intro-post-image, .comment-attachment-image-type';
        pq($str)->addClass('lazy-load-image');
        foreach (pq('img:not([itemprop]):not(.ba-gravatar-img)') as $img) {
            if (pq($img)->parent()->hasClass('ba-image')) {
                continue;
            }
            $src = pq($img)->attr('src');
            if (!empty($src)) {
                pq($img)->attr('src', JUri::root().'components/com_gridbox/assets/images/default-lazy-load.webp');
                pq($img)->attr('data-gridbox-lazyload-src', $src);
                pq($img)->addClass('lazy-load-image');
            }
            $srcset = pq($img)->attr('srcset');
            if (!empty($srcset)) {
                pq($img)->removeAttr('srcset');
                pq($img)->attr('data-gridbox-lazyload-srcset', $srcset);
                pq($img)->addClass('lazy-load-image');
            }
        }
        $script = JUri::root().'components/com_gridbox/libraries/lazyload/js/lazyload.js';
        pq('body')->append('<script src="'.$script.'"></script>');
        $body = $doc->htmlOuter();

        return $body;
    }

    public static function compressImages($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $root = JUri::root();
        $array = explode('/', $root);
        $path = $array[count($array) - 2];
        $path = '/'.$path.'/';
        $n = 0;
        foreach (pq('img') as $img) {
            self::$breakpoint = '';
            $src = pq($img)->attr('src');
            $url = self::getCompressedImageURL($src);
            if ($url) {
                if (self::$website->compress_images == 1) {
                    $n++;
                    pq($img)->attr('src', $url);
                } else {
                    $url = !self::isExternal($src) ? JUri::root().$src : $src;
                }
                if (self::$website->adaptive_images == 1 && !pq($img)->parent()->hasClass('ba-image')) {
                    $n++;
                    $srcsets = [];
                    $sizes = [];
                    foreach (self::$breakpoints as $key => $breakpoint) {
                        self::$breakpoint = $key;
                        $breakpointImg = self::getCompressedImageURL($src);
                        if (!$breakpointImg) {
                            continue;
                        }
                        $srcsets[$breakpoint] = $breakpointImg.' '.$breakpoint.'w';
                        $sizes[$breakpoint] = '(max-width: '.$breakpoint.'px) '.$breakpoint.'px';
                    }
                    $srcsets['2500'] = $url.' 2500w';
                    if (!empty($srcsets)) {
                        ksort($srcsets);
                        ksort($sizes);
                        $srcset = implode(', ', $srcsets);
                        $size = implode(', ', $sizes);
                        pq($img)->attr('srcset', $srcset);
                        //pq($img)->attr('sizes', $size);
                    }
                }
            }
        }
        $styleStr = '';
        foreach (pq('[style*="background-image"]') as $ind => $value) {
            self::$breakpoint = '';
            $style = pq($value)->attr('style');
            preg_match_all('/url\(([^\)]*)\)/', $style, $matches);
            if (!empty($matches)) {
                $src = $matches[1][0];
                $url = self::getCompressedImageURL($src);
                if ($url) {
                    if (self::$website->compress_images == 1) {
                        $n++;
                    } else {
                        $url = !self::isExternal($src) ? JUri::root().$src : $src;
                    }
                    if (self::$website->adaptive_images == 1) {
                        $n++;
                        pq($value)->addClass('ba-adaptive-image-'.($ind + 1));
                        pq($value)->removeAttr('style');
                        $styleStr .= '.ba-adaptive-image-'.($ind + 1).' {background-image: url('.$url.');}';
                        foreach (self::$breakpoints as $key => $breakpoint) {
                            self::$breakpoint = $key;
                            $url = self::getCompressedImageURL($src);
                            if (empty($url)) {
                                continue;
                            }
                            $styleStr .= "@media (max-width: ".$breakpoint."px) {";
                            $styleStr .= '.ba-adaptive-image-'.($ind + 1).' {background-image: url('.$url.');}';
                            $styleStr .= "}";
                        }
                    } else if (self::$website->compress_images == 1) {
                        $style = str_replace($matches[0][0], 'url('.$url.')', $style);
                        pq($value)->attr('style', $style);
                    }
                }
            }
        }
        if (!empty($styleStr)) {
            $styleStr = '<style data-id="adaptive-images">'.$styleStr.'</style>';
            pq('head')->append($styleStr);
        }
        if ($n == 0) {

            return false;
        } else {
            $body = $doc->htmlOuter();

            return $body;
        }
    }

    public static function isSafari()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        return stripos($agent, 'Safari') !== false && stripos($agent, 'Chrome') === false;
    }

    public static function getCompressFolder($image, $ind)
    {
        $ext = strtolower(JFile::getExt($image));
        $endExt = $ext;
        $gd_info = gd_info();
        if (gridboxHelper::$website->adaptive_images == 1 && !empty($ind) && $gd_info['WebP Support']
            && gridboxHelper::$website->adaptive_images_webp == 1 && $ext != 'webp' && !self::isSafari()) {
            $name = basename($image);
            $name = JFile::stripExt($name);
            $image = str_replace($name.'.'.$ext, $name.'.webp', $image);
            $endExt = 'webp';
        } else if (gridboxHelper::$website->compress_images == 1 && empty($ind) && $gd_info['WebP Support']
            && gridboxHelper::$website->compress_images_webp == 1 && $ext != 'webp' && !self::isSafari()) {
            $name = basename($image);
            $name = JFile::stripExt($name);
            $image = str_replace($name.'.'.$ext, $name.'.webp', $image);
            $endExt = 'webp';
        }
        $path = JPATH_ROOT.'/'.IMAGE_PATH.'/compressed';
        $url = JUri::root().IMAGE_PATH.'/compressed';
        if (!empty($ind)) {
            $path .= '/'.$ind;
            $url .= '/'.$ind;
        }
        $array = explode('/', $image);
        $name = array_pop($array);
        $n = count($array);
        if (!JFolder::exists($path)) {
            JFolder::create($path);
        }
        for ($i = 2; $i < $n; $i++) {
            $path .= '/'.$array[$i];
            $url .= '/'.$array[$i];
            if (!JFolder::exists($path)) {
                JFolder::create($path);
            }
        }
        $path .= '/'.$name;
        $url .= '/'.$name;

        return (object)[
            'path' => $path,
            'url' => $url,
            'ext' => $ext,
            'endExt' => $endExt
        ];
    }

    public static function getCompressedImageURL($src, $bg = false)
    {
        $root = JUri::root();
        $array = explode('/', $root);
        $path = $array[count($array) - 2];
        $path = '/'.$path.'/';
        if ($pos1 = strpos($src, '?')) {
            $src = substr($src, 0, $pos1);
        }
        if (strpos($src, $path) !== false || is_file(JPATH_ROOT.'/'.$src)) {
            $ext = JFile::getExt($src);
            $pngFlag = true;
            $gd_info = gd_info();
            if ($ext == 'png' && self::$website->adaptive_images == 1 && !empty(self::$breakpoint)
                && self::$breakpoint != 'desktop'
                && self::$website->adaptive_images_webp == 1 && $ext != 'webp' && $gd_info['WebP Support']) {
                $pngFlag = false;
            } else if ($ext == 'png' && self::$website->compress_images == 1
                && (empty(self::$breakpoint) || self::$breakpoint == 'desktop')
                && self::$website->compress_images_webp == 1 && $ext != 'webp' && $gd_info['WebP Support']) {
                $pngFlag = false;
            }
            if ($pngFlag && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'webp') {
                
                return false;
            }
            if (($pos = strpos($src, $path)) !== false && !$bg) {
                $file = '/'.substr($src, $pos+strlen($path));
            } else if (strpos($src, '/') !== 0) {
                $file = '/'.$src;
            } else {
                $file = $src;
            }
            $array = explode('/', $file);
            $n = count($array);
            $dir = IMAGE_PATH.'/compressed';
            $compressImage = 'compressImage';
            if (self::$website->adaptive_images == 1 && !empty(self::$breakpoint) && self::$breakpoint != 'desktop') {
                $dir .= '/'.self::$breakpoint;
                $task = str_replace('tablet', 'tb', self::$breakpoint);
                $task = str_replace('phone', 'sm', $task);
                $task = str_replace('-portrait', 'pt', $task);
                $compressImage .= $task;
            }
            for ($i = 2; $i < $n; $i++) {
                $dir .= '/'.$array[$i];
            }
            
            if (self::$website->adaptive_images == 1 && !empty(self::$breakpoint) && self::$breakpoint != 'desktop'
                && self::$website->adaptive_images_webp == 1 && $ext != 'webp' && $gd_info['WebP Support']) {
                $name = basename($dir);
                $name = JFile::stripExt($name);
                $dir = str_replace($name.'.'.$ext, $name.'.webp', $dir);
            } else if (self::$website->compress_images == 1 && (empty(self::$breakpoint) || self::$breakpoint == 'desktop')
                && self::$website->compress_images_webp == 1 && $ext != 'webp' && $gd_info['WebP Support']) {
                $name = basename($dir);
                $name = JFile::stripExt($name);
                $dir = str_replace($name.'.'.$ext, $name.'.webp', $dir);
            }
            $point = str_replace('desktop', '', self::$breakpoint);
            $compressed = gridboxHelper::getCompressFolder($file, $point);
            if (JFile::exists($compressed->path) && filesize($compressed->path) != 0) {
                $url = $compressed->url;
            } else if (JFile::exists(JPATH_ROOT.$file)) {
                $url = JUri::root().'index.php?option=com_gridbox&task=gridbox.'.$compressImage.'&image='.urlencode($file);
            } else {
                $url = false;
            }

            return $url;
        }
    }

    public static function checkCommentsURL($url, $regex, $table, $hash)
    {
        preg_match_all($regex, $url, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $id = $matches[0][1] * 1;
            self::setCommentURL($id, $table, $hash);
        }
    }

    public static function checkURI()
    {
        $url = $_SERVER['REQUEST_URI'];
        self::checkCommentsURL($url, '/commentID-+(\d+)/', '#__gridbox_comments', '#commentID-');
        self::checkCommentsURL($url, '/reviewID-+(\d+)/', '#__gridbox_reviews', '#reviewID-');
        preg_match_all('/productID-+(\d+)/', $url, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $id = $matches[0][1] * 1;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('app_id, page_category')
                ->from('#__gridbox_pages')
                ->where('id = '.$id);
            $db->setQuery($query);
            $page = $db->loadObject();
            if ($page) {
                $link = gridboxHelper::getGridboxPageLinks($id, 'product', $page->app_id, $page->page_category);
                $link = JRoute::_($link);
                header('Location: '.$link);
                exit;
            }
        }
        preg_match_all('/pageID-+(\d+)/', $url, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $id = $matches[0][1] * 1;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('app_id, page_category')
                ->from('#__gridbox_pages')
                ->where('id = '.$id);
            $db->setQuery($query);
            $page = $db->loadObject();
            if ($page) {
                if (!empty($page->app_id)) {
                    $query = $db->getQuery(true)
                        ->select('type')
                        ->from('#__gridbox_app')
                        ->where('id = '.$page->app_id);
                    $db->setQuery($query);
                    $type = $db->loadResult();
                } else {
                    $type = 'single';
                }
                $link = gridboxHelper::getGridboxPageLinks($id, $type, $page->app_id, $page->page_category);
                $link = JRoute::_($link);
                header('Location: '.$link);
                exit;
            }
        }
    }

    public static function setCommentURL($id, $table, $hash)
    {
        $meta = '';
        if ($id == 0) {
            $url = JUri::root();
        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('c.*, p.title, p.page_category, p.app_id, p.language')
                ->from($table.' AS c')
                ->where('c.id = '.$id)
                ->leftJoin('#__gridbox_pages AS p ON p.id = c.page_id');
            $db->setQuery($query);
            $obj = $db->loadObject();
            if (!empty($obj)) {
                if (!empty($obj->app_id)) {
                    $query = $db->getQuery(true)
                        ->select('type')
                        ->from('#__gridbox_app')
                        ->where('id = '.$obj->app_id);
                    $db->setQuery($query);
                    $type = $db->loadResult();
                } else {
                    $type = 'single';
                }
                $url = self::getGridboxPageLinks($obj->page_id, $type, $obj->app_id, $obj->page_category);
                $lang = new stdClass();
                if (JLanguageMultilang::isEnabled() && $obj->language != '*') {
                    $languages  = JLanguageHelper::getLanguages();
                    foreach ($languages as $language) {
                        $lang->{$language->lang_code} = $language->sef;
                    }
                }
                if (JLanguageMultilang::isEnabled() && $obj->language != '*' && isset($lang->{$obj->language})) {
                    $url .= '&lang='.($lang->{$obj->language});
                }
                $url = JRoute::_($url);
                $url .= $hash.$id;
                $meta .= '<meta property="og:url" content="'.$_SERVER['REQUEST_URI'].'">';
                $meta .= '<meta property="og:type" content="article">';
                $meta .= '<meta property="og:title" content="'.$obj->title.'">';
                $meta .= '<meta property="og:description" content="'.$obj->message.'">';
            } else {
                $url = JUri::root();
            }
        }
        $doc = JFactory::getDocument();
        $str = '<html prefix="og: http://ogp.me/ns#" xmlns="http://www.w3.org/1999/xhtml" lang="'.$doc->language;
        $str .= '" dir="'.$doc->direction.'"><head><meta http-equiv="content-type" content="text/html; charset=utf-8">';
        $str .= $meta.'<script>window.location.href = "'.$url.'";</script>';
        $str .= '</head><body></body></html>';
        print_r($str);exit;
    }

    public static function setMicrodata($body)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, meta_title')
            ->from('#__gridbox_pages')
            ->where('`id` = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        $title  = !empty($item->meta_title) ? $item->meta_title : $item->title;
        $menus = $app->getMenu();
        $menu = $menus->getActive();
        if (isset($menu) && $menu->query['view'] == 'page' && $menu->query['id'] == $id) {
            $params  = $menus->getParams($menu->id);
            $page_title = $params->get('page_title');
        } else {
            $page_title = '';
        }
        if (!empty($page_title)) {
            $title = $page_title;
        }
        $sitename = $app->get('sitename');
        if ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $sitename, $title);
        } else if ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $sitename);
        }
        $title = htmlspecialchars($title, ENT_QUOTES, 'utf-8');
        pq('.ba-item-star-ratings, .ba-item-reviews')->find('meta[itemprop="name"]')->attr('content', $title);
        $body = $doc->htmlOuter();

        return $body;
    }

    public static function setCanonical($html)
    {
        $url = '';
        $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $view = $input->get('view', 'page', 'string');
        $author = $input->get('author', 0, 'int');
        $tag = $input->get('tag', 0, 'int');
        if ($view == 'page') {
            $id = $input->get('id', 0, 'int');
            $query = $db->getQuery(true)
                ->select('a.id, a.type, p.page_category')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$id)
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id');
            $db->setQuery($query);
            $app = $db->loadObject();
            $type = empty($app->type) ? 'single' : $app->type;
            $url = self::getGridboxPageLinks($id, $type, $app->id, $app->page_category);
        } else if (!empty($author)) {
            $app = $input->get('app', 0, 'int');
            $url = self::getGridboxAuthorLinks($author, $app);
        } else if (!empty($tag)) {
            $app = $input->get('app', 0, 'int');
            $url = self::getGridboxTagLinks($tag, $app);
        } else {
            $app = $input->get('app', 0, 'int');
            $id = $input->get('id', 0, 'int');
            $url = self::getGridboxCategoryLinks($id, $app);
        }
        if (!empty($url)) {
            $url = JRoute::_($url);
            $l = strlen($url) - 1;
            if (self::$website->canonical_slash && $url[$l] != '/') {
                $url .= '/';
            } else if (!self::$website->canonical_slash && $url[$l] == '/') {
                $url = substr($url, 0, $l);
            }
            $url = self::$website->canonical_domain.$url;
            $str = "\n\t<link href=\"".$url."\" rel=\"canonical\">";
            $pos = strpos($html, '</head>');
            $head = substr($html, 0, $pos);
            $body = substr($html, $pos);
            $head = str_replace('</title>', '</title>'.$str, $head);
            $html = $head.$body;
        }

        return $html;
    }

    public static function minifyJs($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        $root = JUri::root();
        $array = explode('/', $root);
        $path = $array[count($array) - 2];
        $path = '/'.$path.'/';
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $js = array();
        $content = array();
        $md5 = '';
        $time = '';
        foreach (pq('script[src*=".js"]') as $value) {
            $key = pq($value)->attr('src');
            if ($key == '/plugins/system/gdpr/assets/js/cookieconsent.min.js'
                || $key == '/plugins/system/gdpr/assets/js/init.js') {
                continue;
            }
            $file = $key;
            if ($pos1 = strpos($file, '?')) {
                $time = substr($file, $pos1);
                $file = substr($file, 0, $pos1);
            }
            if (strpos($key, $path) !== false || is_file(JPATH_ROOT.'/'.$file)) {
                $js[] = $file;
                $md5 .= $file;
                pq($value)->remove();
            }
        }
        $id = md5($md5);
        if (!JFile::exists(JPATH_ROOT.'/templates/gridbox/js/min/'.$id.'.min.js')) {
            foreach ($js as $key => $src) {
                if (($pos = strpos($src, $path)) !== false) {
                    $file = JPATH_ROOT.'/'.substr($src, $pos+strlen($path));
                } else if (strpos($src, '/') !== 0) {
                    $file = JPATH_ROOT.'/'.$src;
                } else {
                    $file = JPATH_ROOT.$src;
                }
                $str = self::readFile($file);
                $str = preg_replace('/\t/', "\n", $str);
                $str = preg_replace('/\r/', "\n", $str);
                $str = preg_replace('/[ ]{2,}/', " ", $str);
                $str = preg_replace('/\n /', "\n", $str);
                $str = preg_replace('/ \n/', "\n", $str);
                $str = preg_replace('/[\n]{2,}/', "\n", $str);
                $str = str_replace(' =', "=", $str);
                $str = str_replace('= ', "=", $str);
                $str = str_replace(' &&', "&&", $str);
                $str = str_replace('&& ', "&&", $str);
                $str = str_replace(') {', "){", $str);
                $str = str_replace(")\n{", "){", $str);
                $content[] = "try {".$str."} catch (err) {console.info(err);console.info('Error in file ".$src."');}\n";
            }
            $fp = fopen(JPATH_ROOT.'/templates/gridbox/js/min/'.$id.'.min.js', 'w+');
            foreach ($content as $key => $string) {
                fwrite($fp, $string);
            }
            fclose($fp);
        }
        $src = JUri::root().'templates/gridbox/js/min/'.$id.'.min.js'.$time;
        $str = '<script src="'.$src.'"></script>';
        pq('head link[type="image/vnd.microsoft.icon"]:first, head  link[rel="manifest"][href*="manifest.webmanifest"]:first')->after($str);
        $body = $doc->htmlOuter();

        return $body;
    }

    public static function fixSmartSearch($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        foreach (pq('script[src*="awesomplete.min.js"]') as $script) {
            pq('script[src*="finder.min.js"]')->before($script);
        }
        $body = $doc->htmlOuter();

        return $body;
    }

    public static function setDeferredLoading($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $gbody = pq('body');
        $gbody->append("\n");
        $woff = JUri::root().'templates/gridbox/library/icons/ba-icons/ba-icons.woff';
        $gbody->append('<link rel="preload" href="'.$woff.'" as="font" type="font/woff" crossorigin="anonymous">');
        foreach (pq('link[href*=".css"], link[href*="fonts.googleapis]') as $value) {
            $gbody->append($value);
        }
        foreach (pq('link[rel="stylesheet]') as $value) {
            pq($value)->attr('as', 'style');
            pq($value)->attr('rel', 'preload');
        }
        foreach (pq('script')->not('.exclude-deffer') as $value) {
            $gbody->append($value);
        }
        $gbody->attr('style', 'opacity: 0; overflow: hidden; margin: 0;');
        $body = $doc->htmlOuter();
        $colors = '';
        foreach (self::$colorVariables as $key => $value) {
            $colors .= str_replace('@', '--', $key).': '.$value->color.';';
        }
        $body = str_replace('<html', '<html style="'.$colors.'"', $body);

        return $body;
    }

    public static function minifyCss($body)
    {
        //$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
        $root = JUri::root();
        $array = explode('/', $root);
        $path = $array[count($array) - 2];
        $path = '/'.$path.'/';
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        $css = array();
        $content = array();
        $import = '';
        $md5 = '';
        $time = '';
        foreach (pq('link[href*=".css"]') as $value) {
            $key = pq($value)->attr('href');
            if ($key == '/plugins/system/gdpr/assets/css/cookieconsent.min.css' ||
                $key == '/plugins/system/gdpr/assets/css/jquery.fancybox.min.css') {
                continue;
            }
            $file = $key;
            if ($pos1 = strpos($file, '?')) {
                if (empty($time) && strpos($file, 'com_gridbox')) {
                    $time = substr($file, $pos1);
                }
                $file = substr($file, 0, $pos1);
            }
            if (strpos($key, $path) !== false || is_file(JPATH_ROOT.'/'.$file)) {
                $css[] = $file;
                $md5 .= $file;
                pq($value)->remove();
            }
        }
        $id = md5($md5);
        if (!JFile::exists(JPATH_ROOT.'/templates/gridbox/css/min/'.$id.'.min.css')) {
            foreach ($css as $key => $link) {
                $filePath = '';
                if (($pos = strpos($link, $path)) !== false) {
                    $filePath = '/'.substr($link, $pos+strlen($path));
                    $file = JPATH_ROOT.$filePath;
                } else if (strpos($link, '/') !== 0) {
                    $filePath = '/'.$link;
                    $file = JPATH_ROOT.'/'.$link;
                } else {
                    $filePath = $link;
                    $file = JPATH_ROOT.$link;
                }
                $pos2 = strrpos($filePath, '/');
                $filePath = substr($filePath, 0, $pos2);
                $str = self::readFile($file);
                $str = preg_replace("/[\n\t\r]+/", ' ', $str);
                $str = preg_replace("/\n+/", ' ', $str);
                $str = preg_replace('/ +/', ' ', $str);
                preg_match_all('/url\(([^\)]*)\)/', $str, $matches);
                foreach ($matches[1] as $key => $match) {
                    $image = preg_replace('/["\']/', '', $match);
                    if (strpos($image, 'http') !== 0 && strpos($image, '//') !== 0) {
                        $image = '../../../..'.$filePath.'/'.$image;
                        $str = str_replace($matches[0][$key], 'url('.$image.')', $str);
                    }
                }
                preg_match_all('/@import +url\(([^\)]*)\)[;]*/', $str, $matches);
                foreach ($matches[0] as $key => $match) {
                    $import .= $match.' ';
                }
                $content[] = $str;

            }
            $fp = fopen(JPATH_ROOT.'/templates/gridbox/css/min/'.$id.'.min.css', 'w+');
            fwrite($fp, $import);
            foreach ($content as $key => $string) {
                fwrite($fp, $string);
            }
            fclose($fp);
        }
        $src = JUri::root().'templates/gridbox/css/min/'.$id.'.min.css'.$time;
        $str = '<link href="'.$src.'" rel="stylesheet" type="text/css" />';
        pq('head link[type="image/vnd.microsoft.icon"]:first, head  link[rel="manifest"][href*="manifest.webmanifest"]:first')->after($str);
        $body = $doc->htmlOuter();

        return $body;
    }

    public static function getIntegrationKey($service)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote($service));
        $db->setQuery($query);
        $key = $db->loadResult();

        return $key;
    }

    public static function getDefaultElementsStyle()
    {
        $dir = JPATH_COMPONENT.'/libraries/json/';
        $object = array();
        $files = JFolder::files($dir);
        foreach ($files as $file) {
            $str = self::readFile($dir.$file);
            $obj = json_decode($str);
            if (isset($obj->type)) {
                $object[$obj->type] = $obj;
            } else {
                foreach ($obj as $key => $value) {
                    if (is_object($value) && isset($value->type) && !isset($object[$value->type])) {
                        $object[$value->type] = $value;
                    }
                }
            }
        }
        $dir = JPATH_COMPONENT.'/views/layout/apps/blog/';
        $str = self::readFile($dir.'app.json');
        $obj = json_decode($str);
        foreach ($obj as $item) {
            if (!isset($object[$item->type])) {
                $object[$item->type] = $item;
            }
        }
        $str = self::readFile($dir.'default.json');
        $obj = json_decode($str);
        foreach ($obj as $item) {
            if (!isset($object[$item->type])) {
                $object[$item->type] = $item;
            }
        }
        $dir = JPATH_COMPONENT.'/views/layout/system/';
        $str = self::readFile($dir.'404.json');
        $obj = json_decode($str);
        foreach ($obj as $item) {
            if (!isset($object[$item->type])) {
                $object[$item->type] = $item;
            }
        }
        $str = self::readFile($dir.'offline.json');
        $obj = json_decode($str);
        foreach ($obj as $item) {
            if (!isset($object[$item->type])) {
                $object[$item->type] = $item;
            }
        }
        $str = json_encode($object);

        return $str;
    }

    public static function getDefaultElementsBox()
    {
        $dir = JPATH_COMPONENT.'/views/layout/';
        $array = array();
        $files = JFolder::files($dir);
        $span = array(12);
        $count = $data = 1;
        $obj = new stdClass();
        $obj->items = new stdClass();
        $now = 0;
        $edit_type = '';
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        foreach ($files as $key => $file) {
            $exclude = ['category-intro.php', 'intro-category-wrapper.php', 'cookies.php', 'intro-post-content.php'];
            if (in_array($file, $exclude)) {
                continue;
            }
            $layout = basename($file, '.php');
            include $dir.$file;
            if (isset($out)) {
                $dom = phpQuery::newDocument($out);
                foreach (pq('.ba-item') as $value) {
                    $className = pq($value)->attr('class');
                    preg_match('/[-\w]+/', $className, $type);
                    if (!empty($type) && !in_array($type[0], $array)) {
                        $object = new stdClass();
                        $object->edit = '<div class="ba-edit-item">'.trim(pq($value)->find('> .ba-edit-item')->html()).'</div>';
                        $object->box = '<div class="ba-box-model">'.trim(pq($value)->find('> .ba-box-model')->html()).'</div>';
                        $array[$type[0]] = $object;
                    }
                }
            }
        }
        include $dir.'section.php';
        $dom = phpQuery::newDocument($out);
        $obj = new stdClass();
        $obj->edit = '<div class="ba-edit-item">'.trim(pq('.ba-section')->find('> .ba-edit-item')->html()).'</div>';
        $obj->box = '<div class="ba-box-model">'.trim(pq('.ba-section')->find('> .ba-box-model')->html()).'</div>';
        $array['ba-section'] = $obj;
        $obj = new stdClass();
        $obj->edit = '<div class="ba-edit-item">'.trim(pq('.ba-row')->find('> .ba-edit-item')->html()).'</div>';
        $obj->box = '<div class="ba-box-model">'.trim(pq('.ba-row')->find('> .ba-box-model')->html()).'</div>';
        $array['ba-row'] = $obj;
        $obj = new stdClass();
        $obj->edit = '<div class="ba-edit-item">'.trim(pq('.ba-grid-column')->find('> .ba-edit-item')->html()).'</div>';
        $obj->box = '<div class="ba-box-model">'.trim(pq('.ba-grid-column')->find('> .ba-box-model')->html()).'</div>';
        $array['ba-grid-column'] = $obj;
        include $dir.'patterns/plugins/ba-box-model-margin.php';
        $array['margin'] = $out;
        $str = json_encode($array);

        return $str;
    }

    public static function setAppLicenseBalbooa($data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        if (!$balbooa->key) {
            $balbooa->key = self::checkGridboxState();
        }
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        if (empty($data)) {
            unset($balbooa->key->data);
        }
        $balbooa->key = json_encode($balbooa->key);
        $db->updateObject('#__gridbox_api', $balbooa, 'id');
    }

    public static function getFonts()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('font, styles, custom_src')
            ->from('`#__gridbox_fonts`')
            ->order($db->quoteName('font') . ' ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $fonts = new stdClass();
        foreach ($items as $item) {
            if (empty($item->font)) {
                continue;
            }
            if (!isset($fonts->{$item->font})) {
                $fonts->{$item->font} = array();
            }
            $fonts->{$item->font}[] = $item;
        }
        foreach ($fonts as $key => $value) {
            usort($value, function($a, $b){
                if ($a->styles == $b->styles) {
                    return 0;
                }

                return ($a->styles < $b->styles) ? -1 : 1;
            });
            $fonts->{$key} = $value;
        }
        $str = json_encode($fonts);
        
        return $str;
    }

    public static function checkCreatePage($id)
    {
        $app = (int)$id;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$app);
        $db->setQuery($query);
        $type = $db->loadResult();
        
        return $type;
    }

    public static function getVersion()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('manifest_cache')
            ->from('#__extensions')
            ->where("type = " .$db->quote('component'))
            ->where('element = ' .$db->quote('com_gridbox'));
        $db->setQuery($query);
        $manifest = $db->loadResult();
        $obj = json_decode($manifest);

        return $obj->version;
    }

    public static function getGlobalItems()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('item')
            ->from('`#__gridbox_library`')
            ->where('`global_item` <> ' .$db->quote(''));
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

    public static function setBreakpoints()
    {
        if (self::$breakpoints) {
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_website')
            ->where('1');
        $db->setQuery($query);
        $website = $db->loadObject();
        if ($website->breakpoints != 'null' && !empty($website->breakpoints)) {
            $obj = json_decode($website->breakpoints);
        } else {
            $obj = new stdClass();
            $obj->laptop = 1200;
            $obj->tablet = 768;
            $obj->{'tablet-portrait'} = 768;
            $obj->phone = 480;
            $obj->{'phone-portrait'} = 480;
            $obj->menuBreakpoint = 768;
            self::siteRules($obj);
        }
        $params = JComponentHelper::getParams('com_gridbox');
        $image_path = $params->get('image_path', '');
        if (!empty($image_path)) {
            $website->image_path = $params->get('image_path', '');
            $website->file_types = $params->get('file_types', '');
            $website->email_encryption = $params->get('email_encryption', 0);
            $db->updateObject('#__gridbox_website', $website, 'id');
            $query = $db->getQuery(true)
                ->update('#__extensions')
                ->set('params = '.$db->quote('{}'))
                ->where('element = '.$db->quote('com_gridbox'))
                ->where('type = '.$db->quote('component'));
            $db->setQuery($query)
                ->execute();
        }
        if (empty($website->image_path)) {
            $website->image_path = 'images';
        }
        if (empty($website->file_types)) {
            $website->file_types = 'csv, doc, gif, ico, jpg, jpeg, pdf, png, txt, xls, svg, mp4, webp, json';
        }
        self::$website = $website;
        self::$dateFormat = $website->date_format;
        self::$menuBreakpoint = $obj->menuBreakpoint;
        unset($obj->menuBreakpoint);
        if (!isset($obj->laptop)) {
            $object = new stdClass();
            $object->laptop = 1200;
            $object->tablet = $obj->tablet;
            $object->{'tablet-portrait'} = $obj->{'tablet-portrait'};
            $object->phone = $obj->phone;
            $object->{'phone-portrait'} = $obj->{'phone-portrait'};
            $obj = $object;
        }
        self::$breakpoints = $obj;
        self::getSystemApps();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        if (!$balbooa) {
            $obj = new stdClass();
            $obj->key = self::checkGridboxState();
            $obj->service = 'balbooa_activation';
            $db->insertObject('#__gridbox_api', $obj);
        }
        include JPATH_ROOT.'/components/com_gridbox/helpers/store.php';
        self::$storeHelper = new store();
        self::$store = self::$storeHelper->getSettings();
        $rates = new stdClass();
        $rates->categories = [];
        $rates->empty = [];
        foreach (self::$store->tax->rates as $key => $rate) {
            $rate->key = $key;
            if ($rate->rate === '') {
                $rate->rate = 0;
            }
            if (!empty($rate->categories)) {
                $rates->categories[] = $rate;
            } else {
                $rates->empty[] = $rate;
            }
        }
        self::$taxRates = $rates;
        if (!defined('IMAGE_PATH')) {
            define('IMAGE_PATH', self::$website->image_path);
        }
        include JPATH_ROOT.'/components/com_gridbox/helpers/css.php';
        self::$css = new cssHelper();
    }

    public static function checkResponsive()
    {
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/responsive.css';
        if (!JFile::exists($file)) {
            $empty = new stdClass();
            $obj = self::object_extend($empty, self::$breakpoints);
            $obj->menuBreakpoint = self::$menuBreakpoint;
            self::siteRules($obj);
        }
    }

    public static function stringURLSafe($string, $language = '')
    {
        if (\JFactory::getConfig()->get('unicodeslugs') == 1) {
            $output = \JFilterOutput::stringURLUnicodeSlug($string);
        } else {
            if ($language === '*' || $language === '') {
                $languageParams = JComponentHelper::getParams('com_languages');
                $language = $languageParams->get('site');
            }
            $output = \JFilterOutput::stringURLSafe($string, $language);
        }

        return $output;
    }

    public static function getAlias($alias, $table, $name = 'page_alias', $id = 0)
    {
        $originAlias = $alias;
        $alias = self::stringURLSafe(trim($alias));
        if (empty($alias)) {
            $alias = $originAlias;
            $alias = self::replace($alias);
            $alias = JFilterOutput::stringURLSafe($alias);
        }
        if (empty($alias)) {
            $alias = date('Y-m-d-H-i-s');
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from($table)
            ->where($db->quoteName($name).' = '.$db->quote($alias))
            ->where('`id` <> ' .$db->quote($id));
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!empty($id)) {
            $alias = self::increment($alias);
            $alias = self::getAlias($alias, $table, $name);
        }
        return $alias;
    }

    public static function checkGridboxState()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();

        return $balbooa;
    }

    public static function sectionRules($obj, $up = '../../../../', $prepare = true, $get = true)
    {
        $str = '';
        self::$up = $up;
        if ($prepare) {
            self::$css->prepareMediaRules();
        }
        foreach ($obj as $key => $value) {
            $str .= self::$css->getPageCSS($value, $key);
        }
        if ($get) {
            $str .= self::$css->getMediaRules();
        }

        return $str;
    }

    public static function prepareParentFonts($params)
    {
        self::$parentFonts = $params;
    }

    public static function object_extend($obj1, $obj2) {
        $obj = json_decode(json_encode($obj1));
        $object = json_decode(json_encode($obj2));
        foreach ($object as $key => $value) {
            if (is_object($value)) {
                if (!isset($obj1->{$key})) {
                    $obj->{$key} = $value;
                } else {
                    $obj->{$key} = self::object_extend($obj1->{$key}, $value);
                }
            } else {
                $obj->{$key} = $value;
            }
        }

        return $obj;
    }

    public static function siteRules($obj)
    {
        $delete = false;
        foreach (self::$breakpoints as $key => $value) {
            if ($value != $obj->{$key}) {
                $delete = true;
                break;
            }
        }
        if (self::$menuBreakpoint != $obj->menuBreakpoint) {
            $delete = true;
        }
        if ($delete) {
            $folder = JPATH_ROOT. '/templates/gridbox/css/storage/';
            $files = JFolder::files($folder);
            foreach ($files as $file) {
                if (strpos($file, 'code-editor') === false && strpos($file, 'index.') === false) {
                    JFile::delete($folder.$file);
                }
            }
        }
        $object = new stdClass();
        $object->id = 1;
        $object->breakpoints = json_encode($obj);
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_website', $object, 'id');
        self::$menuBreakpoint = $obj->menuBreakpoint;
        unset($obj->menuBreakpoint);
        self::$breakpoints = $obj;
        $patern = self::getSiteCssPaterns();
        $str = "body:not(.com_gridbox) .body .main-body, .ba-overlay-section-backdrop.horizontal-top";
        $str .= " .ba-overlay-section.ba-container .ba-row-wrapper.ba-container, ";
        $str .= ".ba-overlay-section-backdrop.horizontal-bottom .ba-overlay-section.ba-container ";
        $str .= ".ba-row-wrapper.ba-container, .ba-container:not(.ba-overlay-section), ";
        $str .= ".intro-post-wrapper > *:not(.intro-post-image-wrapper) {";
        $str .= "width: ".self::$website->container."px;";
        $str .= "}";
        $points = self::$breakpoints;
        $str .= "\n@media (min-width: ".($points->tablet + 1)."px) {\n";
        $str .= $patern->desktop;
        $str .= "\n}";
        $str .= "\n@media (min-width: ".($points->laptop + 1)."px) {\n";
        $str .= $patern->laptopDesktop;
        $str .= "\n}";
        if (!(bool)self::$website->disable_responsive) {
            $str .= "@media (min-width: ".(self::$menuBreakpoint + 1)."px) {\n";
            $str .= $patern->desktopMenu;
            $str .= "\n}";
            $str .= "@media (max-width: ".self::$menuBreakpoint."px) {\n";
            $str .= $patern->menu;
            $str .= "\n}";
            $str .= "\n@media (max-width: ".$points->laptop."px) {\n";
            $str .= $patern->laptop;
            $str .= "}";
            $str .= "\n@media (max-width: ".$points->tablet."px) {\n";
            $str .= $patern->tablet;
            $str .= "}";
            $str .= "\n@media (max-width: ".$points->{'tablet-portrait'}."px) {\n";
            $str .= $patern->tabletPortrait;
            $str .= "\n}";
            $str .= "@media (min-width: ".($points->tablet + 1)."px) and (max-width: ".$points->laptop."px){";
            $str .= $patern->tabletLaptop;
            $str .= "}";
            $str .= "@media (min-width: ".($points->{'tablet-portrait'} + 1)."px) and (max-width: ".$points->tablet."px){";
            $str .= $patern->tabletPTLS;
            $str .= "}";
            $str .= "@media (min-width: ".($points->phone + 1)."px) and (max-width: ".$points->{'tablet-portrait'}."px){";
            $str .= $patern->phoneTabletPT;
            $str .= "}";
            $str .= "@media (min-width: ".($points->{'phone-portrait'} + 1)."px) and (max-width: ".$points->phone."px){";
            $str .= $patern->phonePTLS;
            $str .= "}";
            $str .= "\n@media (max-width: ".$points->phone."px) {\n";
            $str .= $patern->phone;
            $str .= "\n}";
            $str .= "\n@media (max-width: ".$points->{'phone-portrait'}."px) {\n";
            $str .= $patern->phonePortrait;
            $str .= "\n}";
        } else {
            $str .= 'body {min-width: '.self::$website->container.'px;}';
            $str .= '.main-menu > .ba-item {display: none !important;}';
        }
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/responsive.css';
        JFile::write($file, $str);
    }

    public static function themeRules($obj, $id)
    {
        $theme = $obj->params;
        foreach ($obj->footer->items as $value) {
            if ($value->type == 'footer') {
                $footer = $value;
            }
        }
        $str = 'html body {';
        foreach (self::$colorVariables as $key => $value) {
            $str .= str_replace('@', '--', $key).': '.$value->color.';';
        }
        $str .= '}';
        self::$parentFonts = $footer;
        $str .= self::sectionRules($obj->footer->items, '../../../../', true, false);
        self::$parentFonts = $theme;
        $str .= self::$css->setMediaRules($theme, 'body', 'createRules');
        $str .= self::sectionRules($obj->header->items, '../../../../', false);
        $str .= self::prepareCustomFonts();
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/style-'.$id.'.css';
        JFile::write($file, $str);
    }

    public static function getSiteCssPaterns()
    {
        $obj = new stdClass();
        $dir = JPATH_ROOT.'/components/com_gridbox/views/layout/css/';
        $obj->menu = self::readFile($dir.'menu.css');
        $obj->desktopMenu = self::readFile($dir.'desktop-menu.css');

        $obj->laptopDesktop = self::readFile($dir.'laptop-desktop.css');

        $obj->tabletLaptop = self::readFile($dir.'tablet-laptop.css');

        $obj->tabletPTLS = self::readFile($dir.'tablet-portrait-landscape.css');

        $obj->phoneTabletPT = self::readFile($dir.'phone-tablet-portrait.css');

        $obj->phonePTLS = self::readFile($dir.'phone-portrait-landscape.css');
        

        $obj->desktop = self::readFile($dir.'desktop.css');
        $obj->laptop = self::readFile($dir.'laptop.css');
        $obj->tablet = self::readFile($dir.'tablet.css');
        $obj->tabletPortrait = self::readFile($dir.'tablet-portrait.css');
        $obj->phone = self::readFile($dir.'phone.css');
        $obj->phonePortrait = self::readFile($dir.'phone-portrait.css');

        return $obj;
    }

    public static function returnSystemStyle($doc)
    {
        $str = '';
        foreach ($doc->_styleSheets as $key => $link) {
            $str .= '<link href="'.$key.'" type="text/css"';
            if (isset($script['media']) && !empty($link['media'])) {
                $str .= ' media="'.$link['media'].'"';
            }
            $str .= " rel='stylesheet'>\n\t";
        }
        foreach ($doc->_style as $key => $style) {
            $str .= '<style>'.$style."</style>\n\t";
        }
        foreach ($doc->_scripts as $key => $script) {
            $str .= '<script src="'.$key.'"';
            if (isset($script['defer']) && !empty($script['defer'])) {
                $str .= ' defer';
            }
            if (isset($script['async']) && !empty($script['async'])) {
                $str .= ' async';
            }
            $str .= "></script>\n\t";
        }
        foreach ($doc->_script as $key => $script) {
            if (is_array($script)) {
                $text = implode("\n\t\t", $script);
            } else {
                $text = $script;
            }
            $str .= "<script>\n\t\t".$text."\n\t</script>\n\t";
        }

        return $str;
    }

    public static function getSystemParams($id, $language = true)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_system_pages')
            ->where('published = 1')
            ->where('id = '.$id);
        if ($language) {
            $query->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
        }
        $db->setQuery($query);
        $obj = $db->loadObject();
        if ($obj && empty($obj->html)) {
            $obj->html = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$obj->type.'.html');
            $obj->items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$obj->type.'.json');
        }

        return $obj;
    }

    public static function getSystemParamsByType($type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_system_pages')
            ->where('published = 1')
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('type = '.$db->quote($type));
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj;
    }

    public static function getSystemPageByType($type)
    {
        $db = JFactory::getDbo();        
        $query = $db->getQuery(true)
            ->select('alias')
            ->from('#__gridbox_system_pages')
            ->where('published = 1')
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('type = '.$db->quote($type));
        $db->setQuery($query);
        $alias = $db->loadResult();

        return $alias;
    }

    public static function getSystemPageByAlias($alias)
    {
        $db = JFactory::getDbo();        
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_system_pages')
            ->where('published = 1')
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('alias = '.$db->quote($alias));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function checkSystemTheme($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, theme')
            ->from('`#__gridbox_system_pages`')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__template_styles')
            ->where('`id` = ' .$db->quote($obj->theme));
        $db->setQuery($query);
        $theme = $db->loadResult();
        if ($theme != $obj->theme) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__template_styles')
                ->where('`client_id` = 0')
                ->where('`template` = ' .$db->quote('gridbox'))
                ->where('`home` = 1');
            $db->setQuery($query);
            $default = $db->loadResult();
            if (!$default) {
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__template_styles')
                    ->where('`client_id` = 0')
                    ->where('`template` = ' .$db->quote('gridbox'));
                $db->setQuery($query);
                $default = $db->loadResult();
            }
            $obj->theme = $default;
            $db->updateObject('#__gridbox_system_pages', $obj, 'id');
        }
    }

    public static function checkAccountCss()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('items')
            ->from('`#__gridbox_system_pages`')
            ->where('type = '.$db->quote('checkout'));
        $db->setQuery($query);
        $object = $db->loadObject();
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/account.css';
        if (!JFile::exists($file)) {
            if (empty($object->items)) {
                $items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/checkout.json');
                $obj = json_decode($items);
            } else {
                $obj = json_decode($object->items);
            }
            $data = new stdClass();
            foreach ($obj as $key => $value) {
                if ($value->type == 'checkout-form') {
                    $data->{$key} = $value;
                    break;
                }
            }
            self::$fonts = array();
            self::$customFonts = array();
            $str = self::sectionRules($data, '../../../../');
            $str .= self::prepareCustomFonts();
            JFile::write($file, $str);
        }
    }

    public static function checkSystemCss($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type, items')
            ->from('`#__gridbox_system_pages`')
            ->where('id = '.$id);
        $db->setQuery($query);
        $object = $db->loadObject();
        $type = $object->type;
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/system-page-'.$id.'.css';
        if (!JFile::exists($file)) {
            if (empty($object->items)) {
                $item = new stdClass();
                $item->html = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$type.'.html');
                $item->items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$type.'.json');
                $item->id = $id;
                $obj = json_decode($item->items);
            } else {
                $obj = json_decode($object->items);
            }
            self::$fonts = array();
            self::$customFonts = array();
            $str = self::sectionRules($obj, '../../../../');
            $str .= self::prepareCustomFonts();
            JFile::write($file, $str);
            if (empty($object->items)) {
                $item->fonts = json_encode(self::$fonts);
                $item->saved_time = date('Y-m-d-H-i-s');
                $db->updateObject('#__gridbox_system_pages', $item, 'id');
            }
        }

        return $type;
    }

    public static function checkPageCss($id)
    {
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/style-'.$id.'.css';
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('p.app_id')
            ->from('`#__gridbox_pages` AS p')
            ->select('a.type')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('p.id = '.$id);
        $db->setQuery($query);
        $app = $db->loadObject();
        if (!JFile::exists($file)) {
            $query = $db->getQuery(true)
                ->select('style')
                ->from('`#__gridbox_pages`')
                ->where('id = '.$id);
            $db->setQuery($query);
            $style = $db->loadResult();
            $obj = json_decode($style);
            self::$fonts = array();
            self::$customFonts = array();
            $str = self::sectionRules($obj, '../../../../../');
            $str .= self::prepareCustomFonts();
            $object = new stdClass();
            $object->id = $id;
            $object->fonts = json_encode(self::$fonts);
            $object->saved_time = date('Y-m-d-H-i-s');
            $db->updateObject('#__gridbox_pages', $object, 'id');
            JFile::write($file, $str);
        }
        if (!empty($app->type) && $app->type != 'single') {
            self::checkPostCss($app->app_id);
        }
    }

    public static function checkAppCss($id)
    {
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/app-'.$id.'.css';
        if (!JFile::exists($file)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('app_items, type')
                ->from('`#__gridbox_app`')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            $str = $item->app_items;
            if (empty($str)) {
                $str = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.json');
            }
            $obj = json_decode($str);
            if (!isset($obj->{'item-15003687281'})) {
                $obj->{'item-15003687281'} = self::getOptions('category-intro');
                $object = new stdClass();
                $object->app_items = json_encode($obj);
                $object->id = $id;
                $db->updateObject('#__gridbox_app', $object, 'id');
            }
            self::$fonts = array();
            self::$customFonts = array();
            $str = self::sectionRules($obj, '../../../../../');
            $str .= self::prepareCustomFonts();
            $object = new stdClass();
            $object->id = $id;
            $object->app_fonts = json_encode(self::$fonts);
            $object->saved_time = date('Y-m-d-H-i-s');
            $db->updateObject('#__gridbox_app', $object, 'id');
            JFile::write($file, $str);
        }
    }

    public static function checkPostCss($id)
    {
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/post-'.$id.'.css';
        if (!JFile::exists($file)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('page_items, type')
                ->from('`#__gridbox_app`')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            $str = $item->page_items;
            if (empty($str)) {
                $str = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.json');
            }
            $obj = json_decode($str);
            self::$fonts = array();
            self::$customFonts = array();
            $str = self::sectionRules($obj, '../../../../../');
            $str .= self::prepareCustomFonts();
            $object = new stdClass();
            $object->id = $id;
            $object->page_fonts = json_encode(self::$fonts);
            $object->saved_time = date('Y-m-d-H-i-s');
            $db->updateObject('#__gridbox_app', $object, 'id');
            JFile::write($file, $str);
        }
    }

    public static function pageRules($obj, $id)
    {
        $str = self::sectionRules($obj, '../../../../../');
        $str .= self::prepareCustomFonts();
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/style-'.$id.'.css';
        JFile::write($file, $str);
    }

    public static function prepareCustomFonts()
    {
        $str = '';
        $fontsStr = self::getFonts();
        $fonts = json_decode($fontsStr);
        if (!is_array(self::$customFonts)) {
            self::$customFonts = array();
        }
        foreach (self::$customFonts as $key => $custom) {
            $url = '';
            if (!isset($fonts->{$key})) {
                continue;
            }
            $font = $fonts->{$key};
            foreach ($font as $obj) {
                if (isset($custom[$obj->styles])) {
                    $str .= "@font-face {font-family: '".str_replace('+', ' ', $key)."'; ";
                    $str .= "font-display: swap;";
                    $str .= "font-weight: ".$obj->styles."; ";
                    $str .= "src: url(".self::$up."templates/gridbox/library/fonts/".$obj->custom_src.");} ";
                }
            }
        }

        return $str;
    }

    public static function saveAppLayout($obj, $id)
    {
        $db = JFactory::getDbo();
        self::$fonts = array();
        self::$customFonts = array();
        $str = self::sectionRules($obj->style, '../../../../../');
        $str .= self::prepareCustomFonts();
        $object = new stdClass();
        $object->id = $id;
        $object->app_layout = $obj->params;
        $object->app_items = json_encode($obj->style);
        $object->app_fonts = json_encode(self::$fonts);
        $object->saved_time = date('Y-m-d-H-i-s');
        $db->updateObject('#__gridbox_app', $object, 'id');
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/app-'.$object->id.'.css';
        JFile::write($file, $str);
    }

    public static function savePageFields($fields, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_page_fields')
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $remove = [];
        $desktopFiles = self::getDesktopSavedFieldFiles($id);
        foreach ($items as $item) {
            if (!isset($fields->{$item->field_id})) {
                $remove[] = $item->id;
                continue;
            }
            if ($item->field_type == 'checkbox' && !isset($fields->{$item->field_id}->value)) {
                $value = [];
            } else {
                $value = $fields->{$item->field_id}->value;
            }
            if ($item->field_type == 'checkbox' || $item->field_type == 'url' || $item->field_type == 'field-button'
                || $item->field_type == 'image-field') {
                $item->value = json_encode($value);
            } else {
                $item->value = $value;
            }
            if ($item->field_type == 'image-field') {
                if (is_numeric($value->src) && isset($desktopFiles->{$value->src})) {
                    unset($desktopFiles->{$value->src});
                }
            } else if ($item->field_type == 'field-simple-gallery' || $item->field_type == 'field-slideshow'
                || $item->field_type == 'product-slideshow' || $item->field_type == 'product-gallery') {
                $data = json_decode($value);
                foreach ($data as $object) {
                    if (is_numeric($object->img) && isset($desktopFiles->{$object->img})) {
                        unset($desktopFiles->{$object->img});
                    }
                }
            } else if ($item->field_type == 'field-video') {
                $data = json_decode($value);
                if (!empty($value) && is_numeric($data->file) && isset($desktopFiles->{$data->file})) {
                    unset($desktopFiles->{$data->file});
                }
            } else if ($item->field_type == 'file') {
                if (is_numeric($value) && isset($desktopFiles->{$value})) {
                    unset($desktopFiles->{$value});
                }
            }
            $db->updateObject('#__gridbox_page_fields', $item, 'id');
            unset($fields->{$item->field_id});
        }
        if (!empty($remove)) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_page_fields')
                ->where('id IN ('.implode(',', $remove).')')
                ->where('page_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
        foreach ($fields as $field) {
            $obj = new stdClass();
            $obj->page_id = $id;
            $obj->field_id = $field->field_id;
            $obj->field_type = $field->type;
            if ($field->type == 'checkbox' || $field->type == 'url' || $field->type == 'field-button' || $field->type == 'image-field') {
                $obj->value = json_encode($field->value);
            } else {
                $obj->value = $field->value;
            }
            if ($field->type == 'image-field' && is_numeric($field->value->src) && isset($desktopFiles->{$field->value->src})) {
                unset($desktopFiles->{$field->value->src});
            } else if ($field->type == 'field-simple-gallery' || $field->type == 'field-slideshow'
                || $field->type == 'product-slideshow' || $field->type == 'product-gallery') {
                $data = json_decode($field->value);
                foreach ($data as $object) {
                    if (is_numeric($object->img) && isset($desktopFiles->{$object->img})) {
                        unset($desktopFiles->{$object->img});
                    }
                }
            } else if ($field->type == 'field-video') {
                $data = json_decode($field->value);
                if (!empty($data) && is_numeric($data->file) && isset($desktopFiles->{$data->file})) {
                    unset($desktopFiles->{$data->file});
                }
            } else if ($field->type == 'file' && is_numeric($field->value) && isset($desktopFiles->{$field->value})) {
                unset($desktopFiles->{$field->value});
            }
            if ($field->field_id == 'image') {
                continue;
            }
            $db->insertObject('#__gridbox_page_fields', $obj);
        }
        $desktopArray = [];
        foreach ($desktopFiles as $file) {
            $desktopArray[] = $file->id;
            $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/app-'.$file->app_id.'/';
            $path = $dir.$file->filename;
            if (JFile::exists($path)) {
                JFile::delete($path);
            }
        }
        if (!empty($desktopArray)) {
            $desktopStr = implode(',', $desktopArray);
            $query = $db->getQuery(true)
                ->delete('#__gridbox_fields_desktop_files')
                ->where('id IN ('.$desktopStr.')');
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function saveAppFields($fields, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields')
            ->where('app_id = '.$id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            if (!isset($fields->{$item->field_key})) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_fields')
                    ->where('id = '.$item->id);
                $db->setQuery($query)
                    ->execute();
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_page_fields')
                    ->where('field_id = '.$item->id);
                $db->setQuery($query)
                    ->execute();
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_fields_data')
                    ->where('field_id = '.$item->id);
                $db->setQuery($query)
                    ->execute();
            } else {
                $obj = new stdClass();
                $obj->id = $item->id;
                $obj->label = $fields->{$item->field_key}->label;
                $obj->required = $fields->{$item->field_key}->required;
                $obj->options = json_encode($fields->{$item->field_key}->options);
                $options = $fields->{$item->field_key}->options;
                $obj->field_type = $options->type;
                $db->updateObject('#__gridbox_fields', $obj, 'id');
                if ($options->type != 'select' && $options->type != 'radio' && $options->type != 'checkbox'
                    || $obj->field_type != $item->field_type) {
                    $query = $db->getQuery(true)
                        ->delete('#__gridbox_fields_data')
                        ->where('field_id = '.$item->id);
                    $db->setQuery($query)
                        ->execute();
                    if ($obj->field_type != $item->field_type) {
                        $query = $db->getQuery(true)
                            ->delete('#__gridbox_page_fields')
                            ->where('field_id = '.$item->id);
                        $db->setQuery($query)
                            ->execute();
                    }
                } else {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__gridbox_fields_data')
                        ->where('field_id = '.$item->id);
                    $db->setQuery($query);
                    $fields_data = $db->loadObjectList();
                    $optionData = new stdClass();
                    foreach ($fields_data as $value) {
                        $optionData->{$value->option_key} = $value;
                    }
                    foreach ($options->items as $option) {
                        if (isset($optionData->{$option->key})) {
                            $object = $optionData->{$option->key};
                            $object->value = $option->title;
                            $db->updateObject('#__gridbox_fields_data', $object, 'id');
                            unset($optionData->{$option->key});
                        } else {
                            self::insertFieldsData($db, $item->id, $options->type, $option);
                        }
                    }
                    foreach ($optionData as $value) {
                        $query = $db->getQuery(true)
                            ->delete('#__gridbox_fields_data')
                            ->where('id = '.$value->id);
                        $db->setQuery($query)
                            ->execute();
                    }
                }
                unset($fields->{$item->field_key});
            }
        }
        foreach ($fields as $key => $field) {
            $obj = new stdClass();
            $obj->label = $field->label;
            $obj->app_id = $id;
            $obj->required = $field->required;
            $obj->options = json_encode($field->options);
            $obj->field_type = $field->options->type;
            $obj->field_key = $key;
            $db->insertObject('#__gridbox_fields', $obj);
            if ($field->options->type == 'select' || $field->options->type == 'radio'
                || $field->options->type == 'checkbox') {
                $fieldId = $db->insertid();
                foreach ($field->options->items as $value) {
                    self::insertFieldsData($db, $fieldId, $field->options->type, $value);
                }
            }
        }
    }

    public static function insertFieldsData($db, $fieldId, $type, $obj)
    {
        $object = new stdClass();
        $object->field_id = $fieldId;
        $object->field_type = $type;
        $object->option_key = $obj->key;
        $object->value = $obj->title;
        $db->insertObject('#__gridbox_fields_data', $object);
    }

    public static function savePostLayout($obj, $id)
    {
        $db = JFactory::getDbo();
        self::$fonts = array();
        self::$customFonts = array();
        $str = self::sectionRules($obj->style, '../../../../../');
        $str .= self::prepareCustomFonts();
        $fields = new stdClass();
        foreach ($obj->style as $key => $value) {
            if ($value->type == 'field' || $value->type == 'image-field' || $value->type == 'field-simple-gallery'
                || $value->type == 'field-slideshow' || $value->type == 'product-slideshow'
                || $value->type == 'product-gallery' || $value->type == 'field-button'
                || $value->type == 'field-google-maps' || $value->type == 'field-video') {
                $fields->{$key} = new stdClass();
                $fields->{$key}->label = $value->label;
                $fields->{$key}->required = (integer)$value->required;
                $fields->{$key}->options = $value->options;
            } else if ($value->type == 'field-group') {
                foreach ($value->items as $item) {
                    $fields->{$item->field_key} = new stdClass();
                    $fields->{$item->field_key}->label = $item->label;
                    $fields->{$item->field_key}->required = (integer)$item->required;
                    $fields->{$item->field_key}->options = $item->options;
                }
            }
        }
        self::saveAppFields($fields, $id);
        $object = new stdClass();
        $object->id = $id;
        $object->page_layout = $obj->params;
        $object->page_items = json_encode($obj->style);
        $object->page_fonts = json_encode(self::$fonts);
        $object->post_editor_wrapper = $obj->post_editor_wrapper;
        $object->saved_time = date('Y-m-d-H-i-s');
        $db->updateObject('#__gridbox_app', $object, 'id');
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/post-'.$id.'.css';
        JFile::write($file, $str);
    }

    public static function saveTheme($obj, $id)
    {
        if (!isset($obj->params->colorVariables)) {
            $obj->params->colorVariables = self::getOptions('color-variables');
        }
        if (!isset($obj->params->presets)) {
            $obj->params->presets = new stdClass();
        }
        if (!isset($obj->params->defaultPresets)) {
            $obj->params->defaultPresets = new stdClass();
        }
        self::$presets = $obj->params->presets;
        self::$colorVariables = $obj->params->colorVariables;
        $folder = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/';
        $files = JFolder::files($folder);
        foreach ($files as $file) {
            if (strpos($file, 'index.') === false) {
                JFile::delete($folder.$file);
            }
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from("#__gridbox_system_pages");
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach ($list as $item) {
            $file = JPATH_ROOT. '/templates/gridbox/css/storage/system-page-'.$item->id.'.css';
            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }
        $folder = JPATH_ROOT. '/templates/gridbox/css/min/';
        $files = JFolder::files($folder);
        foreach ($files as $file) {
            if (strpos($file, 'index.') === false) {
                JFile::delete($folder.$file);
            }
        }
        $folder = JPATH_ROOT. '/templates/gridbox/js/min/';
        $files = JFolder::files($folder);
        foreach ($files as $file) {
            if (strpos($file, 'index.') === false) {
                JFile::delete($folder.$file);
            }
        }
        $db = JFactory::getDbo();
        self::$fonts = array();
        self::$customFonts = array();
        if (!isset($obj->header)) {
            $object = self::getThemeParams($id);
            $obj->footer = $object->get('footer');
            $obj->header = $object->get('header');
            foreach ($obj->header->items as $value) {
                if (isset($value->type) && $value->type == 'header') {
                    $obj->layout = $value->layout;
                    break;
                }
            }
        }
        self::themeRules($obj, $id);
        $obj->fonts = json_encode(self::$fonts);
        if (isset($obj->params->image)) {
            $obj->image = $obj->params->image;
        }
        $obj->time = date('Y-m-d-H-i-s');
        $theme = new stdClass();
        $theme->id = $id;
        $theme->params = json_encode($obj);
        $db->updateObject('#__template_styles', $theme, 'id');
        //self::exportFooter($obj->footer, 'footer');
        //self::exportFooter($obj->header, 'header');
        return $obj->fonts;
    }

    public static function saveSystemPage($obj, $id)
    {
        $db = JFactory::getDbo();
        self::$fonts = array();
        self::$customFonts = array();
        $str = self::sectionRules($obj->style, '../../../../');
        $str .= self::prepareCustomFonts();
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/system-page-'.$obj->id.'.css';
        JFile::write($file, $str);
        $obj->fonts = json_encode(self::$fonts);
        $obj->saved_time = date('Y-m-d-H-i-s');        
        $obj->items = json_encode($obj->style);
        if ($obj->type == 'checkout') {
            $customer = $obj->customer;
            unset($obj->customer);
        }
        if (isset($obj->alias)) {
            $obj->alias = self::getAlias($obj->alias, '#__gridbox_system_pages', 'alias', $obj->id);
        }
        unset($obj->style);
        $obj->html = $obj->params;
        unset($obj->params);
        $db->updateObject('#__gridbox_system_pages', $obj, 'id');
        if ($obj->type == 'checkout') {
            $pks = [];
            $ids = [];
            foreach ($customer as $info) {
                $info->options = json_encode($info->settings);
                unset($info->settings);
                if ($info->id != 0) {
                    $db->updateObject('#__gridbox_store_customer_info', $info, 'id');
                } else {
                    $db->insertObject('#__gridbox_store_customer_info', $info);
                    $info->id = $db->insertid();
                }
                $pks[] = $info->id;
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_customer_info_data')
                    ->where('field_id = '.$info->id)
                    ->where('page_id = '.$obj->id);
                $db->setQuery($query);
                $data = $db->loadObject();
                if (!$data) {
                    $data = new stdClass();
                    $data->field_id = $info->id;
                    $data->page_id = $obj->id;
                    $data->type = $info->type;
                    $data->title = $info->title;
                    $data->options = $info->options;
                    $db->insertObject('#__gridbox_store_customer_info_data', $data);
                    $data->id = $db->insertid();
                } else {
                    $data->title = $info->title;
                    $data->type = $info->type;
                    $data->options = $info->options;
                    $db->updateObject('#__gridbox_store_customer_info_data', $data, 'id');
                }
                $ids[] = $data->id;
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_customer_info');
            if (!empty($pks)) {
                $str = implode(', ', $pks);
                $query->where('id NOT IN ('.$str.')');
            }
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_customer_info_data')
                ->where('page_id = '.$obj->id);
            if (!empty($ids)) {
                $str = implode(', ', $ids);
                $query->where('id NOT IN ('.$str.')');
            }
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function savePage($obj, $id)
    {
        $db = JFactory::getDbo();
        self::$fonts = [];
        self::$customFonts = array();
        //self::pageRules($obj->style, $id);
        $obj->fonts = json_encode(self::$fonts);
        $obj->saved_time = date('Y-m-d-H-i-s');
        if (empty($obj->page_alias)) {
            $obj->page_alias = $obj->title;
        }
        $tags = $obj->meta_tags;
        $author = $obj->author;
        $page_categories = $obj->page_categories;
        unset($obj->page_categories);
        unset($obj->meta_tags);
        unset($obj->author);
        if (empty($obj->created)) {
            unset($obj->created);
        }
        if (empty($obj->end_publishing)) {
            unset($obj->end_publishing);
        }
        $obj->page_alias = self::getAlias($obj->page_alias, '#__gridbox_pages', 'page_alias', $obj->id);
        $obj->style = json_encode($obj->style);
        $object = new stdClass();
        $object->params = $obj->params;
        $object->id = $id;
        unset($obj->params);
        $db->updateObject('#__gridbox_pages', $obj, 'id');
        $db->updateObject('#__gridbox_pages', $object, 'id');
        $query = $db->getQuery(true)
            ->delete('#__gridbox_category_page_map')
            ->where('page_id = '.$id);
        if (!empty($page_categories)) {
            $query->where('category_id NOT IN ('.$page_categories.')');
        }
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_category_page_map')
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $categoriesList = $db->loadObjectList();
        if (!empty($page_categories)) {
            $categories = explode(',', $page_categories);
            foreach ($categoriesList as $category) {
                if (($key = array_search($category->category_id, $categories)) !== false) {
                    unset($categories[$key]);
                }
            }
            $categories = array_values($categories);
            foreach ($categories as $category_id) {
                $obj = (object)[
                    'page_id' => $id,
                    'category_id' => $category_id
                ];
                $db->insertObject("#__gridbox_category_page_map", $obj);
            }
        }
        self::saveMetaTags($tags, $id);
        if (!empty($author)) {
            $authors = explode(',', $author);
        } else {
            $authors = [];
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_authors_map')
            ->where('page_id = '.$id);
        if (!empty($author)) {
            $query->where('author_id NOT IN ('.$author.')');
        }
        $db->setQuery($query)
            ->execute();
        foreach ($authors as $value) {
            $query = $db->getQuery(true)
                ->select('COUNT(id)')
                ->from('#__gridbox_authors_map')
                ->where('page_id = '.$id)
                ->where('author_id = '.$value);
            $db->setQuery($query);
            $count = $db->loadResult();
            if ($count == 0) {
                $obj = new stdClass();
                $obj->page_id = $id;
                $obj->author_id = $value;
                $db->insertObject('#__gridbox_authors_map', $obj);
            }
        }
        //self::exportBlock($id);
    }

    public static function saveMetaTags($tags, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, tag_id')
            ->from('#__gridbox_tags_map')
            ->where('`page_id` = '. $id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            if (!in_array($item->tag_id, $tags)) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_tags_map')
                    ->where('id = '.$item->id);
                $db->setQuery($query);
                $db->execute();
            }
        }
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                if (strpos($tag, 'new$') !== false) {
                    $tag = substr($tag, 4);
                    $object = new stdClass();
                    $object->title = $tag;
                    $object->alias = $object->title;
                    $object->alias = self::getAlias($object->alias, '#__gridbox_tags', 'alias');
                    $db->insertObject('#__gridbox_tags', $object);
                    $obj = new stdClass();
                    $obj->page_id = $id;
                    $obj->tag_id = $db->insertid();
                    $db->insertObject('#__gridbox_tags_map', $obj);
                } else {
                    $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__gridbox_tags_map')
                        ->where('`page_id` = '.$id)
                        ->where('`tag_id` = '.$tag);
                    $db->setQuery($query);
                    $item = $db->loadResult();
                    if (empty($item)) {
                        $obj = new stdClass();
                        $obj->page_id = $id;
                        $obj->tag_id = $tag;
                        $db->insertObject('#__gridbox_tags_map', $obj);
                    }
                }
            }
        }
    }

    public static function exportFooter($obj, $name)
    {
        $config = JFactory::getConfig();
        $file =  $config->get('tmp_path') . '/'.$name.'.json';
        JFile::write($file, json_encode($obj->items));
        $file =  $config->get('tmp_path') . '/'.$name.'.php';
        JFile::write($file, $obj->html);
    }

    public static function exportBlock($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('style, params, title')
            ->from('#__gridbox_pages')
            ->where('`id` = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $object = new stdClass();
        $object->html = $obj->params;
        $object->items = $obj->style;
        $string = json_encode($object);
        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $root = $doc->createElement('gridbox');
        $root = $doc->appendChild($root);
        $page = $doc->createElement('data');
        $page = $root->appendChild($page);
        $data = $doc->createTextNode($string);
        $page->appendChild($data);
        $config = JFactory::getConfig();
        $file = $config->get('tmp_path').'/'.$obj->title.'.xml';
        $doc->save($file);
    }

    public static function createGlobalCss($id = null)
    {
        $db = JFactory::getDbo();
        $str = '';
        $query = $db->getQuery(true)
            ->select('item')
            ->from('`#__gridbox_library`')
            ->where('id = '.$id)
            ->where('`global_item` <> '.$db->quote(''));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        self::$fonts = [];
        self::$customFonts = [];
        foreach ($items as $key => $value) {
            $item = json_decode($value->item);
            $str .= self::sectionRules($item->items, '../../../../');
        }
        $str .= self::prepareCustomFonts();
        $fonts = json_encode(self::$fonts);
        $query = $db->getQuery(true)
            ->update('`#__gridbox_api`')
            ->set('`key` = '.$db->quote($fonts))
            ->where('`service` = '.$db->quote('library_font'));
        $db->setQuery($query)
            ->execute();
        $file = JPATH_ROOT.'/templates/gridbox/css/storage/global-library-'.$id.'.css';
        JFile::write($file, $str);
    }

    public static function saveGlobalItems($obj)
    {
        $db = JFactory::getDbo();
        foreach ($obj as $key => $value) {
            $item = json_encode($value);
            $query = $db->getQuery(true)
                ->update('`#__gridbox_library`')
                ->set('`item` = '.$db->quote($item))
                ->where('`global_item` = '.$db->quote($key));
            $db->setQuery($query)
                ->execute();
        }
        $dir = JPATH_ROOT. '/templates/gridbox/css/storage/';
        $files = JFolder::files($dir);
        foreach ($files as $file) {
            if (strpos($file, 'global-library') !== false) {
                JFile::delete($dir.$file);
            }
        }
    }

    public static function getFontUrl()
    {
        if (empty(self::$fonts)) {
            return '';
        }
        $url = '//fonts.googleapis.com/css?family=';
        foreach (self::$fonts as $key => $family) {
            $url .= $key.':';
            foreach ($family as $ind => $weight) {
                $url .= $weight;
                if ($ind != count($family) - 1) {
                    $url .= ',';
                } else {
                    $url .= '%7C';
                }
            }
        }
        $pos = strripos($url, '%7C');
        $url = substr($url, 0, $pos);
        $url .= '&subset=latin,cyrillic,greek,latin-ext,greek-ext,vietnamese,cyrillic-ext&display=swap';

        return $url;
    }

    public static function saveCodeEditor($obj, $id)
    {
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$id.'.css';
        JFile::write($file, $obj->css);
        $file = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-'.$id.'.js';
        JFile::write($file, $obj->js);
    }

    public static function saveWebsite($obj)
    {
        $obj->id = 1;
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_website', $obj, 'id');
    }

    public static function getFavicon()
    {
        $favicon = '/templates/gridbox/favicon.ico';
        if (JFile::exists(JPATH_ROOT.$favicon)) {
            JFile::delete(JPATH_ROOT.$favicon);
        }
        $favicon = 'components/com_gridbox/assets/images/favicon.png';
        if (!empty(self::$website->favicon)) {
            $favicon = self::$website->favicon;
        }
        $file = JPATH_ROOT.'/templates/gridbox/favicon.png';
        if (!JFile::exists($file) && !empty(self::$website->favicon) && JFile::getExt(self::$website->favicon) == 'png') {
            JFile::copy(JPATH_ROOT.'/'.$favicon, $file);
        }
        if (JFile::exists($file)) {
            $str = "\t".'<link rel="icon" type="image/png" sizes="32x32" href="'.JUri::root().'templates/gridbox/favicon.png">'."\n".
                "\t".'<link rel="apple-touch-icon" sizes="180x180" href="'.JUri::root().'templates/gridbox/favicon.png">'."\n".
                "\t".'<link rel="manifest" href="'.JUri::root().'templates/gridbox/manifest.webmanifest">'."\n";
        } else {
            $str = "\t".'<link href="'.JUri::root().$favicon.'" rel="shortcut icon" type="image/vnd.microsoft.icon"/>'."\n";
        }

        return $str;
    }

    public static function getWebsiteCode()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('header_code, body_code')
            ->from('#__gridbox_website')
            ->where('`id` = 1');
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    public static function getComBa($element)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('`#__extensions`')
            ->where('`element` = '.$db->quote($element));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function getContentsCurl($url)
    {
        $http = JHttpFactory::getHttp();
        $body = '';
        $host = 'balbooa.com';
        if ($socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
            $data = $http->get($url);
            $body = $data->body;
            fclose($socket);
        }
        
        return $body;
    }

    public static function checkFooter()
    {
        $obj = new stdClass();
        $obj->items = self::getOptions('footer');
        include JPATH_ROOT.'/components/com_gridbox/views/layout/footer.php';
        $obj->html = $out;
        
        return $obj;
    }

    public static function checkHeader()
    {
        $obj = new stdClass();
        $obj->items = self::getOptions('header');
        include JPATH_ROOT.'/components/com_gridbox/views/layout/header.php';
        $obj->html = $out;
        
        return $obj;
    }

    public static function checkGridboxLanguage()
    {
        $language = JFactory::getLanguage();
        $paths = $language->getPaths('com_gridbox');
        if (empty($paths)) {
            $language->load('com_gridbox');
        }
    }

    public static function loadGridboxLanguage()
    {
        $path = JPATH_ROOT.'/administrator/components/com_gridbox/language/site/en-GB/en-GB.com_gridbox.ini';
        $result = ['ERROR' => JText::_('ERROR'), 'YEAR' => JText::_('JYEAR'), 'MONTH' => JText::_('JMONTH')];
        if (JFile::exists($path)) {
            $contents = self::readFile($path);
            $contents = str_replace('_QQ_', '"\""', $contents);
            $data = parse_ini_string($contents);
            foreach ($data as $ind => $value) {
                $result[$ind] = JText::_($ind);
            }
        }
        if (phpversion() < '7.2.0') {
            $json = json_encode($result);
        } else {
            $json = json_encode($result, JSON_INVALID_UTF8_IGNORE);
        }
        $data = 'var gridboxLanguage = '.$json.';';

        return $data;
    }

    public static function loadModule()
    {
        $input = JFactory::getApplication()->input;
        $module = $input->get('module', '', 'string');
        if ($module == 'defaultElementsStyle') {
            $defaultElementsStyle = self::getDefaultElementsStyle();
            $data = 'var defaultElementsStyle = '.$defaultElementsStyle.';';
        } else if ($module == 'gridboxLanguage') {
            $data = self::loadGridboxLanguage();
        } else if ($module == 'shapeDividers') {
            $shape = self::getShapeObject();
            $data = 'var shapeDividers = '.json_encode($shape).';';
        } else if ($module == 'presetsPatern') {
            $presetsPatern = self::getOptions('presetsPatern');
            $data = 'var presetsPatern = '.json_encode($presetsPatern).';';
        } else {
            $data = self::readFile(JPATH_ROOT.'/components/com_gridbox/libraries/modules/'.$module.'.js');
        }

        return $data;
    }

    public static function getShapeObject()
    {
        $folder = JPATH_ROOT.'/components/com_gridbox/assets/images/shape-dividers/';
        $files = JFolder::files($folder);
        $shape = [];
        foreach ($files as $file) {
            $ext = JFile::getExt($file);
            if ($ext == 'svg') {
                $key = str_replace('.svg', '', $file);
                $shape[$key] = self::readFile($folder.$file);
            }
        }

        return $shape;
    }

    public static function getOptions($type)
    {
        $path = JPATH_ROOT.'/components/com_gridbox/libraries/json/'.$type.'.json';
        if (is_file($path)) {
            $json = self::readFile($path);
        } else {
            $json = '{}';
        }
        
        return json_decode($json);
    }

    public static function checkBalbooaGridboxState()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__gridbox_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();
        $galleryState = json_decode($balbooa);

        return isset($galleryState->data);
    }

    public static function createFontString($fonts)
    {
        $html = '';
        foreach ($fonts as $key => $font) {
            $str = json_encode($font->variants);
            $str = str_replace('regular', '400', $str);
            $html .= '<li data-style="'.htmlspecialchars($str, ENT_QUOTES).'" data-value="';
            $html .= $font->family.'">'.$font->family.'</li>';
        }
        
        return $html;
    }

    public static function getAccess()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__viewlevels')
            ->order($db->quoteName('ordering') . ' ASC')
            ->order($db->quoteName('title') . ' ASC');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $access = [];
        foreach ($array as $value) {
            $access[$value->id] = $value->title;
        }

        return $access;
    }

    public static function replace($str)
    {
        $str = mb_strtolower($str, 'utf-8');
        $search = [
            '?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '***91;',
            '***93;', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=',
            '|', '"', '\'', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ъ',
            'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я'
        ];
        $replace = [
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'j', 'i', 'e', '-', 'zh', 'ts',
            'ch', 'sh', 'shch', '', 'yu', 'ya'
        ];
        $str = str_replace($search, $replace, $str);
        $str = trim($str);
        $str = preg_replace("/_{2,}/", "-", $str);

        return $str;
    }

    public static function getLanguages()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('lang_code, title')
            ->from('#__languages')
            ->where('published >= 0')
            ->order('title');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $languages = [];
        $languages['*'] = JText::_('JALL');
        foreach ($items as $key => $value) {
            $languages[$value->lang_code] = $value->title;
        }

        return $languages;
    }

    public static function checkGridboxLoginData()
    {
        $input = JFactory::getApplication()->input;
        if ($input->cookie->exists('gridbox_username')) {
            $username = $input->cookie->get('gridbox_username');
            self::userLogin($username);
            setcookie('gridbox_username', '', time() - 3600, '/');
        }
    }

    public static function userLogin($username)
    {
        $user = JUser::getInstance();
        $id = (int) JUserHelper::getUserId($username);
        if ($id) {
            $db = JFactory::getDbo();
            $user->load($id);
            $result = $user->authorise('core.login.site');
            if ($result) {
                $user->guest = 0;
                $session = JFactory::getSession();
                $oldSessionId = $session->getId();
                $session->fork();
                $session->set('user', $user);
                $app = JFactory::getApplication();
                $app->checkSession();
                $query = $db->getQuery(true)
                    ->delete('#__session')
                    ->where($db->quoteName('session_id') . ' = ' . $db->quote($oldSessionId));
                try {
                    $db->setQuery($query)->execute();
                } catch (RuntimeException $e) {
                    
                }
                $user->setLastVisit();
                $app->input->cookie->set(
                    'joomla_user_state',
                    'logged_in',
                    0,
                    $app->get('cookie_path', '/'),
                    $app->get('cookie_domain', ''),
                    $app->isHttpsForced(),
                    true
                );
            }
        }
    }

    public static function checkMeta()
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $option = $app->input->getCmd('option', '');
        $view = $app->input->getCmd('view', '');
        $edit_type = $app->input->getCmd('edit_type', '');
        $tag = $app->input->getCmd('tag', '');
        $author = $app->input->getCmd('author', '');
        $str = '';
        if ($option == 'com_gridbox' && empty($edit_type) && ($view == 'page' || $view == 'gridbox' || $view == 'blog')) {
            $id = $app->input->getCmd('id', 0);
            if ($id == 0 && $view != 'blog') {
                return;
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('p.*');
            $type = '';
            if ($view != 'blog') {
                $type = 'page';
                $query->from('#__gridbox_pages AS p')
                    ->select('c.title AS category_title')
                    ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id');
            } else if (!empty($tag)) {
                $id = $tag;
                $type = 'tag';
                $query->from('#__gridbox_tags AS p');
            } else if (!empty($author)) {
                $type = 'author';
                $id = $author;
                $query->from('#__gridbox_authors AS p');
            } else if ($id != 0) {
                $type = 'category';
                $query->from('#__gridbox_categories AS p');
            } else {
                $id = $app->input->getCmd('app', 0);
                $query->from('#__gridbox_app AS p');
            }
            $query->where('p.id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            if ($type == 'page') {
                $query = $db->getQuery(true)
                    ->select('type')
                    ->from('#__gridbox_app')
                    ->where('id = '.$id);
                $db->setQuery($query);
                $item->app_type = $db->loadResult();
            }
            $intro_image = isset($item->intro_image) ? $item->intro_image : $item->image;
            $image = $item->share_image != 'share_image' ? $item->share_image : '';
            $menus = $app->getMenu();
            $menu = $menus->getActive();
            $meta_title = $item->meta_title;
            $meta_description = $item->meta_description;
            $share_title = $item->share_title;
            $share_description = $item->share_description;
            if (!empty($type)) {
                include_once JPATH_ROOT.'/components/com_gridbox/helpers/seo.php';
                $seo = new gridboxSeoHelper($item, $type);
                $global = $seo->getGlobal();
                $share_title = empty($share_title) && !empty($global->share_title) ? $global->share_title : $share_title;
                $share_description = empty($share_description) && !empty($global->share_description) ? $global->share_description : $share_description;
                $share_title = $seo->prepareText($share_title);
                $share_description = $seo->prepareText($share_description);
                $meta_title = empty($meta_title) && !empty($global->meta_title) ? $global->meta_title : $meta_title;
                $meta_description = empty($meta_description) && !empty($global->meta_description) ? $global->meta_description : $meta_description;
                $meta_title = $seo->prepareText($meta_title);
                $meta_description = $seo->prepareText($meta_description);
                $image = empty($image) && !empty($global->share_image) ? $global->share_image : (empty($image) ? $intro_image : $image);
                $image = $seo->prepareText($image);
            }
            $title  = !empty($share_title) ? $share_title : $meta_title;
            $desc = !empty($share_description) ? $share_description : $meta_description;
            if (empty($title)) {
                $title = $item->title;
            }
            if (isset($menu) && $menu->query['view'] == $view && $menu->query['id'] == $id) {
                $params  = $menus->getParams($menu->id);
                $page_title = $params->get('page_title');
                $page_desc = $params->get('menu-meta_description');
            } else {
                $page_title = '';
                $page_desc = '';
                $page_key = '';
            }
            if (!empty($page_title)) {
                $title = $page_title;
            }
            if (!empty($page_desc)) {
                $desc = $page_desc;
            }
            if ($app->get('sitename_pagetitles', 0) == 1) {
                $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
            } else if ($app->get('sitename_pagetitles', 0) == 2) {
                $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
            }
            $path = JPATH_ROOT . '/components/com_bagallery/helpers/bagallery.php';
            JLoader::register('bagalleryHelper', $path);
            $loaded = JLoader::getClassList();
            if (isset($loaded['bagalleryhelper']) && method_exists('bagalleryhelper', 'checkGalleryUri')
                && bagalleryhelper::checkGalleryUri()) {
                return "\n";
            }
            $str = "\t<meta property=\"og:type\" content=\"article\" />\n\t";
            $str .= "<meta property=\"og:title\" content=\"".htmlspecialchars($title, ENT_QUOTES)."\">\n\t";
            if (!empty($desc)) {
                $str .= "<meta property=\"og:description\" content=\"".htmlspecialchars($desc, ENT_QUOTES)."\">\n\t";
            }
            $uri = JUri::getInstance();
            $url = $uri->toString();
            $str .= "<meta property=\"og:url\" content=\"".$url."\">\n\t";
            if (!empty($image) && file_exists(JPATH_ROOT.'/'.$image)) {
                $str .= "<meta property=\"og:image\" content=\"".JUri::root().$image."\">\n\t";
                $ext = JFile::getExt($image);
                $imageCreate = self::imageCreate($ext);
                if ($img = $imageCreate(JPATH_ROOT.'/'.$image)) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $str .= "<meta property=\"og:image:width\" content=\"".$width."\">\n\t";
                    $str .= "<meta property=\"og:image:height\" content=\"".$height."\">\n";
                }
            } else if (!empty($image) && !self::isExternal($image)) {
                $str .= "<meta property=\"og:image\" content=\"".$image."\">\n\t";
            }
        }

        return $str;
    }

    public static function imageCreate($ext) {
        switch ($ext) {
            case 'png':
                $imageCreate = 'imagecreatefrompng';
                break;
            case 'gif':
                $imageCreate = 'imagecreatefromgif';
                break;
            case 'webp':
                $imageCreate = 'imagecreatefromwebp';
                break;
            default:
                $imageCreate = 'imagecreatefromjpeg';
        }
        return $imageCreate;
    }

    public static function checkExt($ext)
    {
        switch($ext) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
                return true;
            default:
                return false;
        }
    }

    public static function aboutUs()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("manifest_cache");
        $query->from("#__extensions");
        $query->where("type=" .$db->quote('component'))
            ->where('element=' .$db->quote('com_gridbox'));
        $db->setQuery($query);
        $about = $db->loadResult();
        $about = json_decode($about);
        return $about;
    }

    public static function checkPlugin($title)
    {
        $default = ['bagallery' => 1, 'baforms' => 1, 'modules' => 1, 'recent-posts' => 1, 'fields-filter' => 1,
            'blog-content' => 1, 'post-intro' => 1, 'field-google-maps' => 1, 'field-video' => 1, 'field-group' => 1,
            'field' => 1, 'image-field' => 1, 'field-simple-gallery' => 1, 'field-slideshow' => 1, 'field-button' => 1,
            'product-slideshow' => 1, 'product-gallery' => 1, 'event-calendar' => 1, 'store-search' => 1,
            'checkout-order-form' => 1, 'checkout-form' => 1, 'recent-comments' => 1, 'wishlist' => 1,
            'logo' => 1, 'menu' => 1, 'post-tags' => 1, 'tags' => 1, 'categories' => 1, 'author' => 1,
            'recent-reviews' => 1, 'reviews' => 1, 'google-maps-places' => 1, 'add-to-cart' => 1, 'cart' => 1,
            'related-posts' => 1, 'post-navigation' => 1, 'search' => 1, 'recent-posts-slider' => 1, 'comments-box' => 1,
            'related-posts-slider' => 1, 'recently-viewed-products' => 1, 'currency-switcher' => 1, 'submission-form' => 1, 'submit-button' => 1];
        if (isset($default[$title])) {
            return 1;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_plugins')
            ->where('`title` = ' .$db->quote('ba-'.$title));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function checkMoreScripts($html, $time)
    {
        if (!$html) {
            return;
        }
        $doc = JFactory::getDocument();
        $pageTitle = $doc->getTitle();
        if (strpos($pageTitle, 'Gridbox Editor') === false && (strpos($html, 'ba-item-map') || strpos($html, 'ba-item-field-google-maps')
                || strpos($html, 'ba-item-google-maps-places') || strpos($html, 'field-google-map-wrapper'))) {
            $key = self::getIntegrationKey('google_maps');
            $doc->addScript('https://maps.googleapis.com/maps/api/js?libraries=places&key='.$key);
        }
        if (strpos($html, 'ba-item-yandex-maps')) {
            $key = self::getIntegrationKey('yandex_maps');
            $doc->addScript('https://api-maps.yandex.ru/2.1/?apikey='.$key.'&lang=ru_RU');
            $doc->addScriptDeclaration('
                if (window.ymaps) {
                    ymaps.ready(function(){
                        app.ymaps = true;
                        if ("initYandexMaps" in app) {
                            app.initYandexMaps(null, null);
                        }
                    });
                }
            ');
        }
        if (strpos($html, 'ba-item-openstreetmap')) {
            $doc->addStyleSheet('https://unpkg.com/leaflet@1.4.0/dist/leaflet.css');
            $doc->addScript('https://unpkg.com/leaflet@1.4.0/dist/leaflet.js');
            $doc->addScriptDeclaration('document.addEventListener("DOMContentLoaded", function(){
                app.openstreetmap = true;
            });');
        }
        $options = [];
        $options['version'] = str_replace('?', '', $time);
        foreach (self::$globalItems as $id) {
            if (!JFile::exists(JPATH_ROOT.'/templates/gridbox/css/storage/global-library-'.$id.'.css')) {
                self::createGlobalCss($id);
            }
            $doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/global-library-'.$id.'.css', $options);
        }
    }

    public static function getMainMenu()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__modules')
            ->where('client_id = 0')
            ->where('published = 1')
            ->where('module = '.$db->quote('mod_menu'));
        $db->setQuery($query);
        $menu = $db->loadResult();

        return $menu;
    }

    public static function prepareFonts($fonts, $option, $id, $edit_type)
    {
        if (self::$website->google_fonts == 0) {
            return '';
        }
        $app = JFactory::getApplication();
        $view = $app->input->getCmd('view', '');
        $option = $app->input->getCmd('option', '');
        if ($view == 'blog' && $edit_type != 'system') {
            $edit_type = 'blog';
            $id = $app->input->getCmd('app', '');
        }
        $fonts = json_decode($fonts);
        self::updateFonts($fonts);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('`#__gridbox_api`')
            ->where('`service` = '.$db->quote('library_font'));
        $db->setQuery($query);
        $libraryFonts = $db->loadResult();
        if (!empty($libraryFonts)) {
            $libraryFonts = json_decode($libraryFonts);
            self::updateFonts($libraryFonts);
        }
        if ($option == 'com_gridbox' && empty($edit_type)) {
            $query = $db->getQuery(true)
                ->select('p.fonts')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$id)
                ->select('a.page_fonts')
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id');
            $db->setQuery($query);
            $pageFonts = $db->loadObject();
            if (!empty($pageFonts->fonts)) {
                $pageFonts->fonts = json_decode($pageFonts->fonts);
                self::updateFonts($pageFonts->fonts);
            }
            if (!empty($pageFonts->page_fonts)) {
                $pageFonts->page_fonts = json_decode($pageFonts->page_fonts);
                self::updateFonts($pageFonts->page_fonts);
            }
        } else if ($edit_type != 'system') {
            $query = $db->getQuery(true)
                ->select('app_fonts')
                ->from('#__gridbox_app')
                ->where('id = '.$id);
            $db->setQuery($query);
            $font = $db->loadResult();
            if (!empty($font)) {
                $font = json_decode($font);
                self::updateFonts($font);
            }
        } else if ($edit_type == 'system') {
            $query = $db->getQuery(true)
                ->select('fonts')
                ->from('#__gridbox_system_pages')
                ->where('id = '.$id);
            $db->setQuery($query);
            $font = $db->loadResult();
            if (!empty($font)) {
                $font = json_decode($font);
                self::updateFonts($font);
            }
        }
        $url = self::getFontUrl();
        
        return $url;
    }

    public static function updateFonts($fonts)
    {
        foreach ($fonts as $key => $font) {
            if (!isset(self::$fonts[$key])) {
                self::$fonts[$key] = array();
            }
            foreach ($font as $weight) {
                if (!in_array($weight, self::$fonts[$key])) {
                    self::$fonts[$key][] = $weight;
                }
            }
        }
    }

    public static function getValidId()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__template_styles')
            ->where('`client_id` = 0')
            ->where('`home` = 1');
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function getGridboxItems($id, $theme, $edit_type, $view)
    {
        $gridbox = self::getThemeParams($theme);
        $params = $gridbox->get('params');
        $params->image = $gridbox->get('image', '');
        $footer = $gridbox->get('footer');
        $header = $gridbox->get('header');
        $pageParams = self::getPageParams($params, $header->items, $footer->items, $id, $edit_type, $view);

        return $pageParams;
    }

    public static function preparePresets($data)
    {
        foreach ($data as $key => $value) {
            $data->{$key} = self::$css->presetsCompatibility($value);
            self::$css->comparePresets($data->{$key});
        }

        return $data;
    }

    public static function getPageParams($params, $header, $footer, $id, $edit_type, $view)
    {
        if (!isset($params->presets)) {
            $params->presets = new stdClass();
        }
        if (!isset($params->defaultPresets)) {
            $params->defaultPresets = new stdClass();
        }
        self::$presets = $params->presets;
        $header = self::preparePresets($header);
        $footer = self::preparePresets($footer);
        $library = self::getGlobalItems();
        $array = ['theme' => $params, 'header' => $header, 'footer' => $footer, 'library' => new stdClass()];
        foreach ($library as $value) {
            $globItem = json_decode($value->item);
            $globItem->items = self::preparePresets($globItem->items);
            foreach ($globItem->items as $key => $glob) {
                $array['library']->{$key} = $glob;
            }
        }
        $db = JFactory::getDbo();
        if (empty($edit_type) && $view != 'blog' && $id != 0) {
            $query = $db->getQuery(true)
                ->select('p.style')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$id)
                ->select('a.page_items, a.type')
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id');
            $db->setQuery($query);
            $item = $db->loadObject();
            $page = json_decode($item->style);
            $page = self::preparePresets($page);
            if (!empty($item->type) && $item->type != 'single' && $view != 'gridbox') {
                if (empty($item->page_items) || $item->page_items == null || $item->page_items == 'null') {
                $item->page_items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.json');
                }
                $page_items = json_decode($item->page_items);
                $page_items = self::preparePresets($page_items);
                $products = [];
                foreach ($page_items as $key => $value) {
                    $page->{$key} = $value;
                    if ($value->type == 'add-to-cart') {
                        $products[] = $value;
                    }
                }
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select('pf.value, f.field_key')
                    ->from('#__gridbox_page_fields as pf')
                    ->where('pf.page_id = '.$id)
                    ->where('pf.field_type = '.$db->quote('field-google-maps'))
                    ->leftJoin('`#__gridbox_fields` AS f ON pf.field_id = f.id');
                $db->setQuery($query);
                $fieldGoogleMaps = $db->loadObjectList();
                foreach ($fieldGoogleMaps as $fieldMap) {
                    if (isset($page->{$fieldMap->field_key})) {
                        $fieldValue = json_decode($fieldMap->value);
                        $page->{$fieldMap->field_key}->map->center = $fieldValue->center;
                        $page->{$fieldMap->field_key}->map->zoom = $fieldValue->zoom;
                        if (isset($fieldValue->marker) && isset($fieldValue->marker->position)) {
                            $page->{$fieldMap->field_key}->marker->{0}->place = $fieldValue->marker->place;
                            $page->{$fieldMap->field_key}->marker->{0}->position = $fieldValue->marker->position;
                        }
                    }
                }
                if (!empty($products)) {
                    $currency = self::$store->currency;
                    $productData = new stdClass();
                    $productData->data = self::$storeHelper->getProductData($id);
                    
                    $prices =  self::prepareProductPrices($id, $productData->data->price, $productData->data->sale_price);
                    $productData->data->price = $prices->price;
                    $productData->data->sale_price = $prices->sale_price;
                    
                    if ($item->type == 'booking' && !isset($productData->booking->data->single->hours)) {
                        $productData->data->booking->single->hours = self::getBooking()->getSettings()->default;
                    }
                    $productData->data->price = $productData->data->price;
                    $productData->thousand = $currency->thousand;
                    $productData->separator = $currency->separator;
                    $productData->decimals = $currency->decimals;
                    $productData->rate = $currency->rate;
                    $variationsMap = self::$storeHelper->getProductVariationsMap($id);
                    $variations = self::getProductVariations($productData->data->variations, $variationsMap);
                    $productData->variations = new stdClass();
                    $productData->images = new stdClass();
                    foreach ($variationsMap as $variation) {
                        $productData->images->{$variation->option_key} = json_decode($variation->images);
                    }
                    foreach ($variations as $key => $variation) {
                        $prices = self::prepareProductPrices($id, $variation->price, $variation->sale_price, $key);
                        $variation->price = $prices->price;
                        $variation->sale_price = $prices->sale_price;
                        $productData->variations->{$key} = $variation;
                    }
                    foreach ($products as $product) {
                        $product->productData = $productData;
                    }
                }
            } else if (!empty($item->type) && $item->type != 'single' && $view == 'gridbox') {
                $array['header'] = $array['footer'] = new stdClass();
            }
            $array['page'] = $page;
        } else if ($edit_type == 'post-layout') {
            $query = $db->getQuery(true)
                ->select('page_items, type')
                ->from('#__gridbox_app')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            if (empty($item->page_items) || $item->page_items == null || $item->page_items == 'null') {
                $item->page_items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/default.json');
            }
            $page = json_decode($item->page_items);
            $page = self::preparePresets($page);
            $array['page'] = $page;
        } else if ($edit_type == 'blog' || $view == 'blog') {
            $query = $db->getQuery(true)
                ->select('app_items, type')
                ->from('#__gridbox_app')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            if (empty($item->app_items) || $item->app_items == null || $item->app_items == 'null') {
                $item->app_items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.json');
            }
            $page = json_decode($item->app_items);
            $page = self::preparePresets($page);
            $array['page'] = $page;
        } else if ($edit_type == 'system') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('items, type')
                ->from('#__gridbox_system_pages')
                ->where('id = '.$id);
            $db->setQuery($query);
            $item = $db->loadObject();
            if (empty($item->items)) {
                $item->items = self::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/system/'.$item->type.'.json');
            }
            $page = json_decode($item->items);
            if ($item->type == 'checkout') {
                $page->{'item-15289771305'}->items = self::getCustomerInfo($id);
            }
            $page = self::preparePresets($page);
            $array['page'] = $page;
        }
        $array = json_encode($array);

        return $array;
    }
    
    public static function checkCustom($id, $view, $time)
    {
        $str = '';
        $doc = JFactory::getDocument();
        $file = JPATH_ROOT.'/templates/gridbox/css/custom.css';
        if (JFile::exists($file) && filesize($file) != 0) {
            $file = JUri::root().'templates/gridbox/css/custom.css';
            $doc->addStyleSheet($file);
        }
        $db = JFactory::getDbo();
        if ($id == 0) {
            $query = $db->getQuery(true);
            $query->select('id')
                ->from('#__template_styles')
                ->where('`client_id` = 0')
                ->where('`home` = 1');
            $db->setQuery($query);
            $id = $db->loadResult();
        }
        $file = JPATH_ROOT.'/templates/gridbox/css/storage/style-'.$id.'.css';
        if (!JFile::exists($file)) {
            $query = $db->getQuery(true)
                ->select('params')
                ->from('`#__template_styles`')
                ->where('`id` = ' .$db->quote($id));
            $db->setQuery($query);
            $params = $db->loadResult();
            $params = json_decode($params);
            self::themeRules($params, $id);
        }
        $pageTitle = $doc->getTitle();
        if ($view != 'gridbox' || strpos($pageTitle, 'Gridbox Editor') === false) {
            $file = JPATH_ROOT.'/templates/gridbox/css/storage/code-editor-'.$id.'.css';
            if (isset(self::$systemApps->{'code-editor'}) && JFile::exists($file) && filesize($file) != 0) {
                $file = JUri::root().'templates/gridbox/css/storage/code-editor-'.$id.'.css'.$time;
                //$doc->addStyleSheet($file);
                $str .= "\t".'<link href="'.$file.'" rel="stylesheet" type="text/css" />'."\n";
            }
            $file = JPATH_ROOT.'/templates/gridbox/js/storage/code-editor-'.$id.'.js';
            if (isset(self::$systemApps->{'code-editor'}) && JFile::exists($file) && filesize($file) != 0) {
                $file = JUri::root().'templates/gridbox/js/storage/code-editor-'.$id.'.js'.$time;
                $doc->addScript($file);
            }
        }

        return $str;
    }
    
    public static function getThemeParams($id)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('params, id')
            ->from('`#__template_styles`');
        if ($id > 0) {
            $query->where('`id` = ' .$db->quote($id));
        } else {
            $query->where('`client_id` = 0')
                ->where('`template` = '.$db->quote('gridbox'));
        }
        $db->setQuery($query);
        $obj = $db->loadObject();
        $params = json_decode($obj->params);
        if (!isset($params->params->desktop)) {
            self::setBreakpoints();
            $params = new stdClass();
            $params->params = self::getOptions('theme');
            $params->footer = self::checkFooter();
            $params->header = self::checkHeader();
            $params->layout = '';
            $params->fonts = self::saveTheme($params, $obj->id);
        }
        if (!isset($params->params->colorVariables)) {
            $params->params->colorVariables = self::getOptions('color-variables');
        }
        if (!isset($params->params->presets)) {
            $params->params->presets = new stdClass();
        }
        if (!isset($params->params->defaultPresets)) {
            $params->params->defaultPresets = new stdClass();
        }
        self::$presets = $params->params->presets;
        self::$colorVariables = $params->params->colorVariables;
        $params = json_encode($params);
        $obj = new Registry;
        $obj->loadString($params);
        
        return $obj;
    }

    public static function getPostLayoutPage($app)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, page_category')
            ->from('#__gridbox_pages')
            ->where('app_id = '.$app)
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('page_category <> '.$db->quote('trashed'))
            ->order('id ASC');
        $db->setQuery($query);
        $page = $db->loadObject();
        
        return $page;
    }
    
    public static function getTheme($id, $blog = false, $edit_type = '')
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('theme');
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            if ($page) {
                $id = $page->id;
                $blog = false;
            }
        }
        if ($edit_type == 'system') {
            $query->from('#__gridbox_system_pages');
        } else if (!$blog) {
            $query->from('#__gridbox_pages');
        } else {
            $query->from('#__gridbox_app');
        }
        $query->where('`id` = ' .$db->quote($id));
        $db->setQuery($query);
        $theme = $db->loadResult();
        
        return $theme;
    }

    public static function checkMainMenu($body)
    {
        $regex = '/\[main_menu=+(.*?)\]/i';
        $app = JFactory::getApplication();
        $view = $app->input->getCmd('view', '');
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        if ($matches) {
            foreach ($matches as $index => $match) {
                $module = $match[1];
                if (isset($module)) {
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__modules')
                        ->where('client_id = 0')
                        ->where('published = 1')
                        ->where('module = '.$db->quote('mod_menu'))
                        ->where('id = ' . $db->quote($module));
                    $query->order('ordering');
                    $db->setQuery($query);
                    $module = $db->loadObject();
                    $access = self::checkModuleAccess($module);
                    if ($access) {
                        $document = JFactory::getDocument();
                        $document->_type = 'html';
                        $renderer = $document->loadRenderer('module');
                        $html = $renderer->render($module);
                        $html .= '<div class="ba-edit-item"><span class="ba-edit-wrapper edit-settings">';
                        $html .= '<i class="zmdi zmdi-settings"></i><span class="ba-tooltip tooltip-delay">';
                        $html .= JText::_('ITEM').'</span></span><div class="ba-buttons-wrapper">';
                        $html .= '<span class="ba-edit-wrapper"><i class="zmdi zmdi-edit edit-mobile-menu-item"></i>';
                        $html .= '<span class="ba-tooltip tooltip-delay settings-tooltip">'.JText::_('EDIT').'</span></span></div></div>';
                    } else {
                        $html = '';
                    }
                    if (!empty($html) || $view != 'gridbox') {
                        $body = @preg_replace("|\[main_menu=".$match[1]."\]|", $html, $body, 1);
                    }
                }
            }
        }

        return $body;
    }

    public static function checkModuleAccess($module)
    {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        if (!in_array($module->access, $groups)) {
            return false;
        } else {
            return true;
        }
    }

    public static function clearDOM($body, $items = array())
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $doc = phpQuery::newDocument($body);
        foreach ($items as $key => $item) {
            $access = isset($item->access_view) ? $item->access_view * 1 : 1;
            $user = JFactory::getUser();
            $groups = $user->getAuthorisedViewLevels();
            if (!in_array($access, $groups)) {
                if ($item->type == 'lightbox' || $item->type == 'cookies') {
                    $parent = pq('#'.$key, $doc)->parent()->parent()->remove();
                } else {
                    pq('#'.$key, $doc)->remove();
                }
            }
        }
        $search = '.ba-edit-item, .ba-box-model, .empty-item, .column-info, .ba-column-resizer,';
        $search .= ' .ba-edit-wrapper, .empty-list, .ba-hotspot-popover .add-new-item';
        foreach (pq($search, $doc) as $value) {
            pq($value, $doc)->remove();
        }
        foreach (pq('.content-text, .headline-wrapper > *', $doc) as $value) {
            pq($value, $doc)->removeAttr('contenteditable');
        }
        pq('.ba-menu-wrapper > .main-menu > .add-new-item', $doc)->remove();
        $db = JFactory::getDbo();
        $state = self::checkBalbooaGridboxState();
        if ($state) {
            $query = $db->getQuery(true)
                ->select('title')
                ->from('#__gridbox_plugins');
            $db->setQuery($query);
            $result = $db->loadObjectList();
            $array = ['ba-blog-posts', 'ba-post-intro', 'ba-blog-content', 'ba-post-tags', 'ba-search',
                'ba-store-search', 'ba-field-video', 'ba-field-button', 'ba-recently-viewed-products',
                'ba-preloader', 'ba-search-result', 'ba-store-search-result', 'ba-tags', 'ba-categories', 'ba-recent-posts',
                'ba-comments-box', 'ba-search-result-headline', 'ba-wishlist', 'ba-currency-switcher',
                'ba-field-google-maps', 'ba-related-posts', 'ba-author', 'ba-field', 'ba-image-field', 'ba-recent-comments',
                'ba-recent-reviews', 'ba-reviews', 'ba-fields-filter', 'ba-google-maps-places', 'ba-add-to-cart', 'ba-cart',
                'ba-field-simple-gallery', 'ba-product-gallery', 'ba-field-slideshow', 'ba-product-slideshow',
                'ba-event-calendar', 'ba-field-group', 'ba-post-navigation', 'ba-checkout-order-form', 'ba-checkout-form',
                'ba-category-intro', 'ba-error-message', 'ba-recent-posts-slider', 'ba-related-posts-slider',
                'ba-submission-form', 'ba-submit-button'];
            foreach ($result as $object) {
                $array[] = str_replace('ba-menu', 'ba-main-menu', $object->title);
            }
        } else {
            $array = ['ba-image', 'ba-text', 'ba-button', 'ba-logo', 'ba-menu', 'ba-modules', 'ba-forms', 'ba-gallery',
                'ba-error-message', 'ba-main-menu'];
        }
        foreach (pq('.ba-item', $doc) as $key => $value) {
            $class = pq($value, $doc)->attr('class');
            $class = str_replace('-item', '', $class);
            $flag = false;
            $class = explode(' ', $class);
            foreach ($class as $name) {
                if (in_array($name, $array)) {
                    $flag = true;
                }
            }
            if (!$flag) {
                pq($value, $doc)->remove();
            }
        }
        foreach (pq('.ba-item-preloader', $doc) as $value) {
            $id = pq($value, $doc)->attr('id');
            pq($value, $doc)->attr('data-delay', $items->{$id}->delay);
        }
        foreach (pq('.ba-lightbox-backdrop:not(.ba-item-cookies)', $doc) as $key => $value) {
            if (!in_array('ba-lightbox', $array)) {
                pq($value, $doc)->remove();
            }
        }
        foreach (pq('.ba-item-cookies', $doc) as $key => $value) {
            if (!in_array('ba-cookies', $array)) {
                pq($value, $doc)->remove();
            }
        }
        $search = '.ba-slideshow-title, .ba-slideshow-description, .slideshow-button a';
        foreach (pq('.ba-item-carousel, .ba-item-slideset, .ba-item-slideshow', $doc)->find($search) as $value) {
            $text = pq($value, $doc)->text();
            if (empty($text) && !pq($value, $doc)->hasClass('ba-overlay-slideshow-button')) {
                pq($value, $doc)->parent()->remove();
            }
        }
        $search = '.ba-image-item-title, .ba-image-item-description, .ba-simple-gallery-title,
            .ba-simple-gallery-description';
        $item = '.ba-item-image, .ba-item-simple-gallery, .ba-item-overlay-section > .ba-image-wrapper >
            .ba-image-item-caption';
        foreach (pq($item, $doc)->find($search) as $value) {
            $text = pq($value, $doc)->text();
            if (empty($text)) {
                pq($value, $doc)->remove();
            }
        }
        pq('.ba-unpublished-html-item', $doc)->remove();
        foreach (pq('.ba-item-testimonials', $doc) as $value) {
            $id = pq($value, $doc)->attr('id');
            $item = $obj->{$id};
            $i = 1;
            foreach ($item->slides as $slide) {
                if (isset($slide->unpublish) && $slide->unpublish) {
                    continue;
                }
                $li = pq($value, $doc)->find('li:nth-child('.$i.')');
                if (empty($slide->img)) {
                    pq($li, $doc)->find('.ba-testimonials-img')->remove();
                }
                $caption = pq($li, $doc)->find('.ba-testimonials-caption')->text();
                if (empty($caption)) {
                    pq($li, $doc)->find('.testimonials-caption-wrapper')->remove();
                }
                $title = pq($li, $doc)->find('.ba-testimonials-name')->text();
                if (empty($title)) {
                    pq($li, $doc)->find('.testimonials-name-wrapper')->remove();
                }
                if (empty($title) && empty($caption)) {
                    pq($li, $doc)->find('.testimonials-title-wrapper')->remove();
                }
                $i++;
            }
        }
        foreach (pq('.ba-item-tabs .ba-tabs-wrapper > ul > li .tabs-title', $doc) as $value) {
            $text = pq($value, $doc)->text();
            if (empty($text)) {
                pq($value, $doc)->remove();
            }
        }
        foreach (pq('.ba-item-accordion .accordion-title', $doc) as $value) {
            $text = pq($value, $doc)->text();
            if (empty($text)) {
                pq($value, $doc)->remove();
            }
        }
        $str = $doc->htmlOuter();
        
        return $str;
    }

    public static function setCustomIcons()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('DISTINCT path')
            ->from('#__gridbox_custom_user_icons');
        $db->setQuery($query);
        $icons = $db->loadObjectList();
        $doc = JFactory::getDocument();
        foreach ($icons as $key => $icon) {
            $doc->addStyleSheet(JUri::root().'templates/gridbox/library/icons/custom-icons/'.$icon->path.'/font.css');
        }
    }

    public static function getMapsPlaces($app, $menuItem, $pagesList = '')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('pf.value, p.intro_image, p.title, c.title as category, p.app_id, p.id, p.page_category, p.created, p.hits')
            ->from('#__gridbox_page_fields as pf')
            ->where('pf.field_type = '.$db->quote('field-google-maps'))
            ->where('f.app_id = '.$app)
            ->leftJoin('#__gridbox_fields AS f ON pf.field_id = f.id')
            ->leftJoin('#__gridbox_pages AS p ON pf.page_id = p.id');
        if ($pagesList != '') {
            $query->where('p.id IN ('.$pagesList.')');
        }
        $date = date("Y-m-d H:i:s");
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $nullDate = $db->quote($db->getNullDate());
        $query->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        $db->setQuery($query);
        $pages = $db->loadObjectList();
        $events = array();
        foreach ($pages as $page) {
            $map = json_decode($page->value);
            if (empty($page->value) || !isset($map->marker->position)) {
                continue;
            }
            $page->map = $map;
            unset($page->value);
            $page->created = self::formatDate($page->created);
            $url = self::getGridboxPageLinks($page->id, 'blog', $page->app_id, $page->page_category);
            if (strpos($url, '&Itemid=') === false && !empty($menuItem)) {
                $url .= '&Itemid='.$menuItem;
            }
            $page->url = JRoute::_($url);
            $url = self::getGridboxCategoryLinks($page->page_category, $page->app_id);
            if (strpos($url, '&Itemid=') === false && !empty($menuItem)) {
                $url .= '&Itemid='.$menuItem;
            }
            $page->catUrl = JRoute::_($url);
            $comments = self::getCommentsCount($page->id);
            $page->comments = '<span class="event-calendar-event-item-comments"><a href="'.$page->url
                .'#total-count-wrapper">';
            if ($comments == 0) {
                $page->comments .= JText::_('LEAVE_COMMENT');
            } else {
                $page->comments .= $comments.' '.JText::_('COMMENTS');
            }
            $page->comments .= '</a></span>';
            $reviews = self::getReviewsCount($page->id);
            if ($reviews->count == 0) {
                $reviews->rating = 0;
            }
            $page->reviews = '<div class="event-calendar-event-item-reviews"><span class="ba-blog-post-rating-stars">';
            $floorRating = floor($reviews->rating);
            for ($i = 1; $i < 6; $i++) {
                $width = 'auto';
                if ($i == $floorRating + 1) {
                    $width = (($reviews->rating - $floorRating) * 100).'%';
                }
                $page->reviews .= '<i class="ba-icons ba-icon-star'.($i <= $floorRating ? ' active' : '')
                    .'" style="width: '.$width.'"></i>';
            }
            $page->reviews .= '</span><a class="ba-blog-post-rating-count" href="'.$page->url
                .'#total-reviews-count-wrapper">';
            if ($reviews->count == 0) {
                $page->reviews .= JText::_('LEAVE_REVIEW');
            } else {
                $page->reviews .= $reviews->count.' '.JText::_('REVIEWS');
            }
            $page->reviews .= '</a></div>';
            $authors = self::getRecentPostAuthor($page->id);
            $page->authors = self::getAuthorsHtml($authors, 'event-calendar-event-item-author', $page->app_id);
            $fields = self::getCategoryListFields($page->id, $page->app_id);
            if (!empty($fields) && (empty(self::$editItem) ||
                (!empty(self::$editItem) && self::$editItem->type != 'search-result'
                    && self::$editItem->type != 'store-search-result'))) {
                $desktopFiles = self::getDesktopFieldFiles($page->id);
                $page->fields = '<div class="ba-blog-post-fields"><div class="ba-blog-post-field-row-wrapper">';
                foreach ($fields as $field) {
                    if (!isset($field->value)) {
                        $field->value = '';
                    }
                    if (empty($field->value) || $field->value == '[]') {
                        continue;
                    }
                    $options = json_decode($field->options);
                    $label = $field->label;
                    $value = '';
                    if (empty($field->value)) {
                        $value = $field->value;
                    } else if ($field->field_type == 'select' || $field->field_type == 'radio') {
                        foreach ($options->items as $option) {
                            if ($option->key == $field->value) {
                                if (!empty($value)) {
                                    $value .= ', ';
                                }
                                $value .= $option->title;
                            }
                        }
                    } else if ($field->field_type == 'checkbox') {
                        $fieldValue = json_decode($field->value);
                        foreach ($options->items as $option) {
                            if (in_array($option->key, $fieldValue)) {
                                $value .= '<span class="ba-blog-post-field-checkbox-value">'.$option->title.'</span>';
                            }
                        }
                    } else if ($field->field_type == 'url') {
                        $fieldOptions = json_decode($field->options);
                        $valueOptions = json_decode($field->value);
                        $link = self::prepareGridboxLinks($valueOptions->link);
                        if (empty($link)) {
                            continue;
                        }
                        $value = '<a href="'.$link.'" '.$fieldOptions->download.' target="'.$fieldOptions->target;
                        $value .= '">'.$valueOptions->label.'</a>';
                    } else if ($field->field_type == 'tag') {
                        $value = self::getPostTags($page->id);
                    } else if ($field->field_type == 'time') {
                        if (!empty($field->value)) {
                            $valueOptions = json_decode($field->value);
                            $value = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
                        }
                    } else if ($field->field_type == 'date' || $field->field_type == 'event-date') {
                        if (!empty($field->value)) {
                            $value = self::formatDate($field->value);
                        }
                    } else if ($field->field_type == 'price' && !empty($field->value)) {
                        $fieldOptions = json_decode($field->options);
                        $thousand = $fieldOptions->thousand;
                        $separator = $fieldOptions->separator;
                        $decimals = $fieldOptions->decimals;
                        $value = self::preparePrice($field->value, $thousand, $separator, $decimals, 1);
                        if ($fieldOptions->position == '') {
                            $value = $fieldOptions->symbol.$value;
                        } else {
                            $value .= $fieldOptions->symbol;
                        }
                    } else if ($field->field_type == 'file') {
                        if (!empty($field->value)) {
                            $fieldOptions = json_decode($field->options);
                            if (is_numeric($field->value) && isset($desktopFiles->{$field->value})) {
                                $desktopFile = $desktopFiles->{$field->value};
                                $src = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                            } else {
                                $src = $field->value;
                            }
                            $value = '<a href="'.JUri::root().$src.'" download>'.$fieldOptions->title.'</a>';
                        }
                    } else if ($field->field_type == 'text') {
                        $value = htmlspecialchars($field->value);
                    } else {
                        $value = $field->value;
                    }
                    $page->fields .= '<div class="ba-blog-post-field-row" data-id="'.$field->field_key.
                        '"><div class="ba-blog-post-field-title">';
                    $page->fields .= $label.'</div><div class="ba-blog-post-field-value">'.$value.'</div></div>';
                }
                $page->fields .= '</div></div>';
            } else {
                $page->fields = '';
            }

            $events[] = $page;
        }

        return $events;
    }

    public static function getAppEventDates($app, $menuItem, $type, $categories, $tags)
    {
        $db = JFactory::getDbo();
        $date = date("Y-m-d H:i:s");
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('pf.value, p.intro_image, p.title, c.title as category, p.app_id, p.id,
                p.page_category, p.created, p.hits')
            ->from('#__gridbox_page_fields as pf')
            ->where('pf.field_type = '.$db->quote('event-date'))
            ->where('f.app_id = '.$app)
            ->leftJoin('#__gridbox_fields AS f ON pf.field_id = f.id')
            ->leftJoin('#__gridbox_pages AS p ON pf.page_id = p.id')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if (!empty($categories) && empty($type)) {
            $query->where('p.page_category in ('.$categories.')');
        } else if (!empty($type) && !empty($tags)) {
            $query->where('t.tag_id IN ('.$tags.')')
                ->leftJoin('`#__gridbox_tags_map` AS t ON p.id = t.page_id');
        }
        $db->setQuery($query);
        $pages = $db->loadObjectList();
        $events = [];
        foreach ($pages as $page) {
            $page->intro_image = self::prepareIntroImage($page->intro_image);
            if (!isset($events[$page->value])) {
                $events[$page->value] = [];
            }
            $page->created = self::formatDate($page->created);
            $url = self::getGridboxPageLinks($page->id, 'blog', $page->app_id, $page->page_category);
            if (strpos($url, '&Itemid=') === false && !empty($menuItem)) {
                $url .= '&Itemid='.$menuItem;
            }
            $page->url = JRoute::_($url);
            $url = self::getGridboxCategoryLinks($page->page_category, $page->app_id);
            if (strpos($url, '&Itemid=') === false && !empty($menuItem)) {
                $url .= '&Itemid='.$menuItem;
            }
            $page->catUrl = JRoute::_($url);
            $comments = self::getCommentsCount($page->id);
            $page->comments = '<span class="event-calendar-event-item-comments"><a href="';
            $page->comments .= $page->url.'#total-count-wrapper">';
            if ($comments == 0) {
                $page->comments .= JText::_('LEAVE_COMMENT');
            } else {
                $page->comments .= $comments.' '.JText::_('COMMENTS');
            }
            $page->comments .= '</a></span>';
            $reviews = self::getReviewsCount($page->id);
            if ($reviews->count == 0) {
                $reviews->rating = 0;
            }
            $page->reviews = '<div class="event-calendar-event-item-reviews"><span class="ba-blog-post-rating-stars">';
            $floorRating = floor($reviews->rating);
            for ($i = 1; $i < 6; $i++) {
                $width = 'auto';
                if ($i == $floorRating + 1) {
                    $width = (($reviews->rating - $floorRating) * 100).'%';
                }
                $page->reviews .= '<i class="ba-icons ba-icon-star'.($i <= $floorRating ? ' active' : '')
                    .'" style="width: '.$width.'"></i>';
            }
            $page->reviews .= '</span><a class="ba-blog-post-rating-count" href="';
            $page->reviews .= $page->url.'#total-reviews-count-wrapper">';
            if ($reviews->count == 0) {
                $page->reviews .= JText::_('LEAVE_REVIEW');
            } else {
                $page->reviews .= $reviews->count.' '.JText::_('REVIEWS');
            }
            $page->reviews .= '</a></div>';
            $authors = self::getRecentPostAuthor($page->id);
            $page->authors = self::getAuthorsHtml($authors, 'event-calendar-event-item-author', $page->app_id);
            $fields = self::getCategoryListFields($page->id, $page->app_id);
            if (!empty($fields) && (empty(self::$editItem) ||
                (!empty(self::$editItem) && self::$editItem->type != 'search-result'
                    && self::$editItem->type != 'store-search-result'))) {
                $desktopFiles = self::getDesktopFieldFiles($page->id);
                $page->fields = '<div class="ba-blog-post-fields"><div class="ba-blog-post-field-row-wrapper">';
                foreach ($fields as $field) {
                    if (!isset($field->value)) {
                        $field->value = '';
                    }
                    if (empty($field->value) || $field->value == '[]') {
                        continue;
                    }
                    $options = json_decode($field->options);
                    $label = $field->label;
                    $value = '';
                    if (empty($field->value)) {
                        $value = $field->value;
                    } else if ($field->field_type == 'select' || $field->field_type == 'radio') {
                        foreach ($options->items as $option) {
                            if ($option->key == $field->value) {
                                if (!empty($value)) {
                                    $value .= ', ';
                                }
                                $value .= $option->title;
                            }
                        }
                    } else if ($field->field_type == 'checkbox') {
                        $fieldValue = json_decode($field->value);
                        foreach ($options->items as $option) {
                            if (in_array($option->key, $fieldValue)) {
                                $value .= '<span class="ba-blog-post-field-checkbox-value">'.$option->title.'</span>';
                            }
                        }
                    } else if ($field->field_type == 'url') {
                        $fieldOptions = json_decode($field->options);
                        $valueOptions = json_decode($field->value);
                        $link = self::prepareGridboxLinks($valueOptions->link);
                        if (empty($link)) {
                            continue;
                        }
                        $value = '<a href="'.$link.'" '.$fieldOptions->download.' target="'.$fieldOptions->target;
                        $value .= '">'.$valueOptions->label.'</a>';
                    } else if ($field->field_type == 'tag') {
                        $value = self::getPostTags($page->id);
                    } else if ($field->field_type == 'time') {
                        if (!empty($field->value)) {
                            $valueOptions = json_decode($field->value);
                            $value = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
                        }
                    } else if ($field->field_type == 'date' || $field->field_type == 'event-date') {
                        if (!empty($field->value)) {
                            $value = self::formatDate($field->value);
                        }
                    } else if ($field->field_type == 'price' && !empty($field->value)) {
                        $fieldOptions = json_decode($field->options);
                        $thousand = $fieldOptions->thousand;
                        $separator = $fieldOptions->separator;
                        $decimals = $fieldOptions->decimals;
                        $value = self::preparePrice($field->value, $thousand, $separator, $decimals, 1);
                    } else if ($field->field_type == 'file') {
                        if (!empty($field->value)) {
                            $fieldOptions = json_decode($field->options);
                            if (is_numeric($field->value) && isset($desktopFiles->{$field->value})) {
                                $desktopFile = $desktopFiles->{$field->value};
                                $src = 'components/com_gridbox/assets/uploads/app-';
                                $src .= $desktopFile->app_id.'/'.$desktopFile->filename;
                            } else {
                                $src = $field->value;
                            }
                            $value = '<a href="'.JUri::root().$src.'" download>'.$fieldOptions->title.'</a>';
                        }
                    } else if ($field->field_type == 'text') {
                        $value = htmlspecialchars($field->value);
                    } else {
                        $value = $field->value;
                    }
                    $page->fields .= '<div class="ba-blog-post-field-row" data-id="'.$field->field_key.
                        '"><div class="ba-blog-post-field-title">';
                    $page->fields .= $label.'</div><div class="ba-blog-post-field-value">'.$value.'</div></div>';
                }
                $page->fields .= '</div></div>';
            } else {
                $page->fields = '';
            }
            $events[$page->value][] = $page;
        }

        return $events;
    }

    public static function getSubmissionForm($id, $order, $fields, $page_id = 0)
    {
        $html = '';
        $fields = (object)$fields;
        $items = self::getSubmissionFields($id);
        $db = JFactory::getDbo();
        $data = [];
        $user = JFactory::getUser();
        $query = $db->getQuery(true)
            ->select('id, user_id, title, intro_text as description, page_category as category, intro_image')
            ->from('#__gridbox_pages')
            ->where('id = '.$page_id)
            ->where('user_id = '.$user->id);
        $db->setQuery($query);
        $page = $db->loadObject();
        if (!$page && !empty($user->id) && ($author = self::getAuthor($user->id))) {
            $query = $db->getQuery(true)
                ->select('p.id, p.user_id, p.title, p.intro_text as description, p.page_category as category, p.intro_image')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$page_id)
                ->where('t.author_id = '.$author->id)
                ->leftJoin('`#__gridbox_authors_map` AS t ON p.id = t.page_id');
            $db->setQuery($query);
            $page = $db->loadObject();
        }
        $fieldsData = !empty($user->id) && isset($page->id) ? gridboxHelper::getFieldsData($page_id) : new stdClass();
        if (!empty($user->id) && isset($page->id)) {
            $array = ['title', 'description', 'category'];
            foreach ($array as $text) {
                $obj = new stdClass();
                $obj->value = $page->{$text};
                $fieldsData->{$text} = $obj;
            }
            $obj = new stdClass();
            $obj->value = '{"src":"'.$page->intro_image.'","alt": ""}';
            $fieldsData->image = $obj;
        }
        foreach ($order as $item) {
            if (!isset($items->{$item}) || !$fields->{$item}) {
                continue;
            }
            $data[] = $items->{$item};
        }
        if (!empty($data)) {
            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__gridbox_tags');
            $db->setQuery($query);
            $tags = $db->loadObjectList();
            $query = $db->getQuery(true);
            $query->select('t.id, t.title')
                ->from('#__gridbox_tags_map AS m')
                ->leftJoin('#__gridbox_tags AS t ON t.id = m.tag_id')
                ->where('m.page_id = '.$page_id);
            $db->setQuery($query);
            $pageTags = $db->loadObjectList();
            include_once JPATH_COMPONENT.'/helpers/fields.php';
            $generator = new fieldsGenerator(false, null, $pageTags, $tags, 'zero', 'submission-form');
            if (!empty($page->id)) {
                $generator->desktopFiles = gridboxHelper::getDesktopFieldFiles($page->id);
            }
            foreach ($data as $field) {
                if (isset($fieldsData->{$field->id})) {
                    $value = $fieldsData->{$field->id}->value;
                } else {
                    $value = '';
                }
                $html .= $generator->getHTML($field, $value);
            }
            
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function renderEventCalendarData($time, $app = 0, $menuItem = 0, $start = 0, $type = '', $categories = '', $tags = '')
    {
        $end = $start + 6;
        $obj = new stdClass();
        $dateData = new stdClass();
        $dateData->days = [JText::_('SUN'), JText::_('MON'), JText::_('TUE'), JText::_('WED'), JText::_('THU'),
            JText::_('FRI'), JText::_('SAT'), JText::_('SUN')];
        $dateData->month = [JText::_('JANUARY'), JText::_('FEBRUARY'), JText::_('MARCH'), JText::_('APRIL'),
            JText::_('MAY'), JText::_('JUNE'), JText::_('JULY'), JText::_('AUGUST'), JText::_('SEPTEMBER'),
            JText::_('OCTOBER'), JText::_('NOVEMBER'), JText::_('DECEMBER')];
        $year = date('Y', $time);
        $month = date('n', $time);
        $today = date('j');
        $nowDate = date('n Y');
        $todayDate = date('n Y', $time);
        $obj->year = $year;
        $obj->month = $month - 1;
        $obj->title = $dateData->month[$month - 1].' '.$year;
        $obj->header = '';
        for ($i = $start; $i <= $end; $i++) { 
            $obj->header .= '<div class="ba-event-calendar-day-name">'.$dateData->days[$i].'</div>';
        }
        $obj->body = '';
        $firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
        if ($firstDay == 0 && $start == 1) {
            $firstDay = 7;
        }
        $daysInMonth = date('t', $time);
        $pages = self::getAppEventDates($app, $menuItem, $type, $categories, $tags);
        $obj->eventList = $pages;
        $date = 1;
        for ($i = 0; $i < 6; $i++) {
            if ($date > $daysInMonth) {
                break;
            }
            $obj->body .= '<div class="ba-event-calendar-row">';
            for ($j = $start; $j <= $end; $j++) {
                if (($i === 0 && $j < $firstDay) || $date > $daysInMonth) {
                    $obj->body .= '<div class="ba-empty-date-cell"></div>';
                } else {
                    $obj->body .= '<div class="ba-date-cell'.($date == $today && $nowDate == $todayDate ? ' ba-curent-date' : '');
                    $eventDate = date('Y-m-d', mktime(0, 0, 0, $month, $date, $year));
                    if (isset($pages[$eventDate])) {
                        $obj->body .= ' ba-event-date';
                    }
                    $obj->body .= '" data-date="'.$eventDate.'">'.$date.'</div>';
                    $date++;
                }

            }
            $obj->body .= '</div>';
        }

        return $obj;
    }

    public static function replaceBootstrap($html)
    {
        preg_match_all('/span\d+/', $html, $matches);
        if (!empty($matches)) {
            foreach($matches[0] as $match) {
                preg_match('/\d+/', $match, $span);
                $html = str_replace('span'.$span[0], 'ba-col-'.$span[0], $html);
            }
        }
        preg_match_all('/Span \d+/', $html, $matches);
        if (!empty($matches)) {
            foreach($matches[0] as $match) {
                preg_match('/\d+/', $match, $span);
                $html = str_replace('Span '.$span[0], JText::_('COLUMN').' '.$span[0], $html);
            }
        }

        return $html;
    }

    public static function checkDOM($html, $obj)
    {
        $obj = self::preparePresets($obj);
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        self::$editItem = null;
        self::setReviewsModerators();
        $app = JFactory::getApplication();
        $page = new stdClass();
        $input = $app->input;
        $page->option = $input->getCmd('option', 'option', 'string');
        $page->view = $input->getCmd('view', 'view', 'string');
        $edit_type = $input->getCmd('edit_type', '', 'string');
        $view = $page->view;
        if ($page->option == 'com_gridbox' && $page->view == 'gridbox') {
            $page->view = 'page';
        }
        $page->id = $input->get('id', 0, 'int');
        $dom = phpQuery::newDocument($html);
        $doc = JFactory::getDocument();
        pq('.ba-video-background')->remove();
        pq('.ba-add-section')->remove();
        $search = '.ba-item-slideshow, .ba-item-content-slider, .ba-item-field-slideshow, .ba-item-product-slideshow, ';
        $search .= '.ba-item-recent-posts-slider ul.slideshow-type, .ba-item-related-posts-slider ul.slideshow-type, ';
        $search .= '.ba-item-recently-viewed-products ul.slideshow-type';
        $slideshow = pq($search);
        self::setCustomIcons();
        pq('script[data-pagespeed-no-defer]')->remove();
        if ($view == 'gridbox') {
            foreach (pq('.ba-item-field-simple-gallery, .ba-item-product-gallery')->find('.ba-instagram-image') as $key => $value) {
                $img = pq($value)->find('img');
                $image = 'components/com_gridbox/assets/images/default-theme.png';
                pq($img)->attr('src', JUri::root().$image);
                pq($value)->attr('style', 'background-image: url('.JUri::root().$image.');');
            }
        }
        foreach ($slideshow as $key => $value) {
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/libraries/slideshow/css/animation.css');
            break;
        }
        foreach (pq('.instagram-wrapper.simple-gallery-masonry-layout') as $key => $value) {
            $doc->addScript(JUri::root().'components/com_gridbox/libraries/modules/setGalleryMasonryHeight.js');
            break;
        }
        foreach (pq('.ba-item-flipbox') as $value) {
            if ($view == 'gridbox') {
                $doc->addStyleSheet(JUri::root().'components/com_gridbox/libraries/flipbox/css/animation-editor.css');
            } else {
                $doc->addStyleSheet(JUri::root().'components/com_gridbox/libraries/flipbox/css/animation.css');
            }
            break;
        }
        for ($i = 1; $i <= 12; $i++) {
            pq('.ba-grid-column-wrapper.span'.$i)->removeClass('span'.$i)->addClass('ba-col-'.$i)
                ->find('> .ba-grid-column > .column-info')->text(JText::_('COLUMN').' '.$i);
        }
        foreach (pq('.ba-item-simple-gallery') as $value) {
            $id = pq($value)->attr('id');
            if ($view != 'gridbox' && $obj->{$id}->desktop->overlay->type == 'none') {
                pq($value)->find('.ba-caption-overlay')->remove();
            }
        }
        if (!isset(self::$systemApps->comments)) {
            pq('.ba-item-comments-box')->remove();
        }
        foreach (pq('.ba-item-comments-box') as $value) {
            $id = pq($value)->attr('id');
            $desktop = !empty($obj->{$id}) && $view != 'gridbox' ? $obj->{$id}->view : null;
            $sortBy = 'oldest';
            $userStatus = self::getCommentsUserLoginHTML('comments-box');
            self::setCommentsModerators();
            $str = self::getCommentsCountHTML($page->id, $view, $sortBy);
            pq($value)->find('.ba-comments-total-count-wrapper')->html($str);
            if (strpos($str, 'ba-comments-be-first-message')) {
                pq($value)->find('a.total-count-wrapper')->remove();
                pq($value)->find('.ba-comments-box-wrapper')->prepend('<a class="total-count-wrapper"></a>');
            }
            if ($page->option == 'com_gridbox' && $view == 'page') {
                $str = self::getComments($page->id);
                pq($value)->find('.users-comments-wrapper')->html($str);
                pq($value)->find('.ba-comments-login-wrapper')->html($userStatus->str);
                include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/comments-box/comments-box-message-pattern.php');
                pq($value)->find('.ba-comment-message-wrapper')->html($string);
                pq($value)->find('.comment-reply-form-wrapper .ba-submit-comment')->attr('data-type', 'reply');
                if ($userStatus->status == 'login') {
                    pq($value)->find('.ba-submit-comment')->text(JText::_('COMMENT'));
                    $editStr = '<span class="ba-submit-comment-wrapper"><span class="ba-submit-cancel">';
                    $editStr .= JText::_('CANCEL').'</span><span class="ba-submit-comment" data-type="edit">';
                    $editStr .= JText::_('SAVE').'</span></span>';
                    pq($value)->find('.comment-edit-form-wrapper .ba-submit-comment')->replaceWith($editStr);
                } else {
                    pq($value)->find('.ba-submit-comment')->remove();
                    pq($value)->find('textarea.ba-comment-message')->attr('disabled', 'disabled');
                }
                if (empty(self::$commentUser) ||
                    (self::$website->comments_recaptcha_guests == 1 && !empty(self::$commentUser) &&
                        (self::$commentUser->type == 'user' || self::$commentUser->type == 'social'))) {
                    pq('.ba-comments-captcha-wrapper')->remove();
                }
                $captcha = self::setCaptcha(self::$website->comments_recaptcha);
                if (!$captcha) {
                    pq('.ba-comments-captcha-wrapper')->remove();
                }
            } else {
                $str = self::getCommentsLogoutedUserHTML('comments-box');
                pq($value)->find('.ba-comments-login-wrapper')->html($str);
                pq($value)->find('.ba-submit-comment')->remove();
                $str = self::getDefaultComment('comments-box');
                pq($value)->find('.users-comments-wrapper')->html($str);
                include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/comments-box/comments-box-message-pattern.php');
                pq($value)->find('.ba-comment-message-wrapper')->html($string);
            }
            if ($desktop && !$desktop->user) {
                pq($value)->find('.ba-user-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->social) {
                pq($value)->find('.ba-social-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->guest) {
                pq($value)->find('.ba-guest-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->share) {
                pq($value)->find('.comment-share-action')->remove();
            }
            if ($desktop && !$desktop->rating) {
                pq($value)->find('.comment-likes-action-wrapper')->remove();
            }
            if ($desktop && !$desktop->files) {
                pq($value)->find('.ba-comments-attachment-file-wrapper[data-type="file"]')->remove();
            }
            if ($desktop && !$desktop->images) {
                pq($value)->find('.ba-comments-attachment-file-wrapper[data-type="image"]')->remove();
            }
            if ($desktop && !$desktop->report) {
                pq($value)->find('.comment-report-user-comment')->remove();
            }
        }
        foreach (pq('.ba-item-reviews') as $value) {
            $id = pq($value)->attr('id');
            $desktop = !empty($obj->{$id}) && $view != 'gridbox' ? $obj->{$id}->view : null;
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-box-wrapper.php';
            pq($value)->find('.ba-comments-box-wrapper')->html($string);
            $sortBy = 'recent';
            $userStatus = self::getCommentsUserLoginHTML('reviews');
            $str = self::getReviewsCountHTML($page->id, $view, $sortBy);
            pq($value)->find('.ba-comments-total-count-wrapper')->html($str);
            include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-rate-pattern.php');
            pq($value)->find('> .ba-comments-box-wrapper > .ba-review-rate-wrapper')->html($string);
            if ($page->option == 'com_gridbox' && $view == 'page') {
                $str = self::getReviews($page->id);
                pq($value)->find('.users-comments-wrapper')->html($str);
                pq($value)->find('.ba-comments-login-wrapper')->html($userStatus->str);
                include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-message-pattern.php');
                pq($value)->find('.ba-comment-message-wrapper')->html($string);
                pq($value)->find('.comment-reply-form-wrapper .ba-submit-comment')->attr('data-type', 'reply');
                pq($value)->find('.comment-reply-form-wrapper .ba-submit-comment')->text(JText::_('COMMENT'));
                pq($value)->find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper')
                    ->find('.ba-comments-attachments-wrapper')->remove();
                pq($value)->find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper')->find('.ba-comment-message')
                    ->attr('placeholder', JText::_('WRITE_COMMENT_HERE'));
                if ($userStatus->status == 'login') {
                    $editStr = '<span class="ba-submit-cancel">';
                    $editStr .= JText::_('CANCEL').'</span><span class="ba-submit-comment" data-type="edit">';
                    $editStr .= JText::_('SAVE').'</span>';
                    pq($value)->find('.comment-edit-form-wrapper .ba-submit-comment-wrapper')->html($editStr);
                } else {
                    pq($value)->find('.ba-submit-comment')->remove();
                    pq($value)->find('textarea.ba-comment-message')->attr('disabled', 'disabled');
                }
                if (empty(self::$commentUser) ||
                    (self::$website->reviews_recaptcha_guests == 1 && !empty(self::$commentUser) &&
                        (self::$commentUser->type == 'user' || self::$commentUser->type == 'social'))) {
                    pq('.ba-comments-captcha-wrapper')->remove();
                }
                $captcha = self::setCaptcha(self::$website->reviews_recaptcha);
                if (!$captcha) {
                    pq('.ba-comments-captcha-wrapper')->remove();
                }
            } else {
                $str = self::getCommentsLogoutedUserHTML('reviews');
                pq($value)->find('.ba-comments-login-wrapper')->html($str);
                pq($value)->find('.ba-submit-comment')->remove();
                $str = self::getDefaultComment('reviews');
                pq($value)->find('.users-comments-wrapper')->html($str);
                include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-message-pattern.php');
                pq($value)->find('.ba-comment-message-wrapper')->html($string);
            }
            if ($desktop && !$desktop->user) {
                pq($value)->find('.ba-user-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->social) {
                pq($value)->find('.ba-social-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->guest) {
                pq($value)->find('.ba-guest-login-wrapper')->remove();
            }
            if ($desktop && !$desktop->share) {
                pq($value)->find('.comment-share-action')->remove();
            }
            if ($desktop && !$desktop->rating) {
                pq($value)->find('.comment-likes-action-wrapper')->remove();
            }
            if ($desktop && !$desktop->files) {
                pq($value)->find('.ba-comments-attachment-file-wrapper[data-type="file"]')->remove();
            }
            if ($desktop && !$desktop->images) {
                pq($value)->find('.ba-comments-attachment-file-wrapper[data-type="image"]')->remove();
            }
            if ($desktop && !$desktop->report) {
                pq($value)->find('.comment-report-user-comment')->remove();
            }
            if ($desktop && !$desktop->reply) {
                pq($value)->find('.comment-reply-action')->remove();
            }
        }
        if (!isset(self::$systemApps->reviews)) {
            pq('.ba-item-reviews')->remove();
        }
        foreach (pq('.ba-item-headline') as $value) {
            $id = pq($value)->attr('id');
            if (!empty($obj->{$id}->desktop->animation->effect)) {
                $doc->addStyleSheet(JUri::root().'components/com_gridbox/libraries/headline/css/animation.css');
                break;
            }
        }
        foreach (pq('.headline-wrapper > *') as $value) {
            pq($value)->removeAttr('contenteditable');
        }
        $scrollToTop = array();
        foreach (pq('.ba-item-scroll-to-top') as $value) {
            $id = pq($value)->attr('id');
            if (in_array($id, $scrollToTop)) {
                pq($value)->remove();
                continue;
            }
            $scrollToTop[] = $id;
            pq($value)->removeClass('scroll-btn-left');
            pq($value)->removeClass('scroll-btn-right');
            pq($value)->addClass('scroll-btn-'.$obj->{$id}->text->align);
        }
        $itemSocial = array();
        foreach (pq('.ba-item-social') as $value) {
            $id = pq($value)->attr('id');
            if (in_array($id, $itemSocial)) {
                pq($value)->remove();
                continue;
            }
            $itemSocial[] = $id;
        }
        foreach (pq('.ba-item-social') as $value) {
            $id = pq($value)->attr('id');
            if (!isset($obj->{$id})) {
                continue;
            }
            $keys = array('facebook', 'linkedin', 'pinterest', 'twitter', 'vk');
            $count = 0;
            foreach ($keys as $key) {
                if ($obj->{$id}->{$key}) {
                    $count++;
                }
            }
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/plugins/social-share.php';
            pq($value)->find('.ba-social')->empty()->append($out);
            pq($value)->attr('style', '--social-count: '.$count.';');
            pq($value)->attr('data-size', $obj->{$id}->view->size);
            pq($value)->attr('data-style', $obj->{$id}->view->style);
            foreach ($keys as $key) {
                if ($view != 'gridbox' && !$obj->{$id}->{$key}) {
                    pq($value)->find('.ba-social .'.$key)->remove();
                }
            }
            if (!$obj->{$id}->view->counters) {
                pq($value)->find('.social-counter')->remove();
            }
        }
        foreach (pq('.ba-item-progress-pie') as $value) {
            $id = pq($value)->attr('id');
            if (isset($obj->{$id}) && $view != 'gridbox') {
                $desktop = $obj->{$id}->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->display->target) {
                pq($value)->find('.progress-pie-number')->remove();
            }
        }
        foreach (pq('.ba-item-progress-bar') as $value) {
            $id = pq($value)->attr('id');
            if (isset($obj->{$id}) && $view != 'gridbox') {
                $desktop = $obj->{$id}->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->display->target) {
                pq($value)->find('.progress-bar-number')->remove();
            }
            if ($desktop && !$desktop->display->label) {
                pq($value)->find('.progress-bar-title')->remove();
            }
        }
        pq('.ba-item-accordion .accordion-icon')->attr('class', 'ba-icons ba-icon-chevron-right accordion-icon');
        pq('.ba-lightbox-close.zmdi')->attr('class', 'ba-icons ba-icon-close ba-lightbox-close');
        pq('.ba-overlay-section-close.zmdi')->attr('class', 'ba-icons ba-icon-close ba-overlay-section-close');
        pq('.slideset-btn-prev.zmdi, .slideshow-btn-prev.zmdi')->removeClass('zmdi')
            ->removeClass('zmdi-chevron-left')->addClass('ba-icons ba-icon-chevron-left');
        pq('.slideset-btn-next.zmdi, .slideshow-btn-next.zmdi')->removeClass('zmdi')
            ->removeClass('zmdi-chevron-right')->addClass('ba-icons ba-icon-chevron-right');
        pq('div[data-ba-slide-to].zmdi')->removeClass('zmdi')
            ->removeClass('zmdi-circle')->addClass('ba-icons ba-icon-close');
        pq('.testimonials-icon-wrapper i.zmdi')->removeClass('zmdi')
            ->removeClass('zmdi-quote')->addClass('ba-icons ba-icon-quote');
        foreach (pq('.ba-item') as $value) {
            $id = pq($value)->attr('id');
            if (!empty($obj->{$id}->desktop->appearance->effect)) {
                pq($value)->attr('data-effect', $obj->{$id}->desktop->appearance->effect);
            } else {
                pq($value)->removeAttr('data-effect');
            }
            foreach (self::$breakpoints as $key => $point) {
                if (!empty($obj->{$id}->{$key}->appearance->effect)) {
                    pq($value)->attr('data-'.$key.'-effect', $obj->{$id}->{$key}->appearance->effect);
                } else {
                    pq($value)->removeAttr('data-'.$key.'-effect');
                }
            }
        }
        foreach (pq('.ba-section, .ba-row, .ba-grid-column') as $value) {
            $id = pq($value)->attr('id');
            if (isset($obj->{$id})) {
                if (!empty($obj->{$id}->desktop->animation->effect)) {
                    pq($value)->attr('data-effect', $obj->{$id}->desktop->animation->effect);
                } else {
                    pq($value)->removeAttr('data-effect');
                }
                foreach (self::$breakpoints as $key => $point) {
                    if (!empty($obj->{$id}->{$key}->animation->effect)) {
                        pq($value)->attr('data-'.$key.'-effect', $obj->{$id}->{$key}->animation->effect);
                    } else {
                        pq($value)->removeAttr('data-'.$key.'-effect');
                    }
                }
                if (isset($obj->{$id}->preset) && !empty($obj->{$id}->preset) && isset($obj->{$id}->desktop->shape)) {
                    pq($value)->find(' > .ba-shape-divider')->remove();
                    $shape = self::getShapeObject();
                    $topKeys = [];
                    $bottomKeys = [];
                    if (!empty($obj->{$id}->desktop->shape->bottom->effect)) {
                        $bottomKeys[] = $obj->{$id}->desktop->shape->bottom->effect;
                    }
                    if (!empty($obj->{$id}->desktop->shape->top->effect)) {
                        $topKeys[] = $obj->{$id}->desktop->shape->top->effect;
                    }
                    foreach (self::$breakpoints as $key => $point) {
                        if (isset($obj->{$id}->{$key}) && isset($obj->{$id}->{$key}->shape)) {
                            if (isset($obj->{$id}->{$key}->shape->bottom)
                                && isset($obj->{$id}->{$key}->shape->bottom->effect)) {
                                $bottomKeys[] = $obj->{$id}->{$key}->shape->bottom->effect;
                            }
                            if (isset($obj->{$id}->{$key}->shape->top) && isset($obj->{$id}->{$key}->shape->top->effect)) {
                                $topKeys[] = $obj->{$id}->{$key}->shape->top->effect;
                            }
                        }
                    }
                    if ($count = count($bottomKeys) > 0) {
                        $str = '<div class="ba-shape-divider ba-shape-divider-bottom">';
                        for ($i = 0; $i < $count; $i++) {
                            $str .= $shape[$bottomKeys[$i]] ? $shape[$bottomKeys[$i]] : '';
                        }
                        $str .= '</div>';
                        pq($value)->find('> .ba-overlay')->after($str);
                    }
                    if ($count = count($topKeys) > 0) {
                        $str = '<div class="ba-shape-divider ba-shape-divider-top">';
                        for ($i = 0; $i < $count; $i++) {
                            $str .= $shape[$topKeys[$i]] ? $shape[$topKeys[$i]] : '';
                        }
                        $str .= '</div>';
                        pq($value)->find('> .ba-overlay')->after($str);
                    }
                }
                if ($obj->{$id}->type == 'row') {
                    if ($obj->{$id}->desktop->view->gutter) {
                        pq($value)->removeClass('no-gutter-desktop');
                    } else {
                        pq($value)->addClass('no-gutter-desktop');
                    }
                } else if ($obj->{$id}->type == 'column') {
                    $parent = pq($value)->parent();
                    foreach (self::$breakpoints as $ind => $point) {
                        $name = str_replace('tablet-portrait', 'ba-tb-pt-', $ind);
                        $name = str_replace('tablet', 'ba-tb-la-', $name);
                        $name = str_replace('phone-portrait', 'ba-sm-pt-', $name);
                        $name = str_replace('phone', 'ba-sm-la-', $name);
                        for ($i = 1; $i <= 12; $i++) {
                            pq($parent)->removeClass($name.$i);
                        }
                        if (isset($obj->{$id}->{$ind}) && isset($obj->{$id}->{$ind}->span)
                            && isset($obj->{$id}->{$ind}->span->width)) {
                            pq($parent)->addClass($name.$obj->{$id}->{$ind}->span->width);
                        }
                        $name .= 'order-';
                        for ($i = 1; $i <= 12; $i++) {
                            pq($parent)->removeClass($name.$i);
                        }
                        if (isset($obj->{$id}->{$ind}) && isset($obj->{$id}->{$ind}->span)
                            && isset($obj->{$id}->{$ind}->span->order)) {
                            pq($parent)->addClass($name.$obj->{$id}->{$ind}->span->order);
                        }
                    }
                }
            }
        }
        foreach (pq('.ba-item-scroll-to .ba-scroll-to') as $value) {
            $id = pq($value)->parent()->attr('id');
            $icon = $obj->{$id}->icon;
            $str = '<div class="ba-button-wrapper"><a class="ba-btn-transition"><span class="empty-textnode">';
            $str .= '</span><i class="'.$obj->{$id}->icon.'"></i></a></div>';
            pq($value)->replaceWith($str);
        }
        foreach (pq('.ba-item-simple-gallery .ba-instagram-image') as $value) {
            $img = pq($value)->find('img');
            $image = pq($img)->attr('data-src');
            if (!self::isExternal($image)) {
                pq($img)->attr('src', JUri::root().$image);
                pq($value)->attr('style', 'background-image: url('.JUri::root().str_replace(' ', '%20', $image).');');
            }
        }
        foreach (pq('.ba-item-logo') as $key => $value) {
            $id = pq($value)->attr('id');
            $link = $obj->{$id}->link->link;
            if (empty($link)) {
                $link = JUri::root();
            }
            $link = self::prepareGridboxLinks($link);
            pq($value)->find('.ba-logo-wrapper a')->attr('href', $link);
        }
        foreach (pq('.ba-item-image, .ba-item-icon, .ba-item-button') as $key => $value) {
            $id = pq($value)->attr('id');
            $link = $obj->{$id}->link->link;
            if (strpos($link, IMAGE_PATH) === 0) {
                $link = JUri::root().$link;
            }
            $link = self::prepareGridboxLinks($link);
            pq($value)->find('a[onclick="return false;"]')->removeAttr('onclick');
            pq($value)->find('a')->attr('href', $link);
        }
        foreach (pq('.ba-grid-column') as $key => $value) {
            $id = pq($value)->attr('id');
            if (isset($obj->{$id}->link)) {
                $link = $obj->{$id}->link->link;
                if (strpos($link, IMAGE_PATH) === 0) {
                    $link = JUri::root().$link;
                }
                $link = self::prepareGridboxLinks($link);
                pq($value)->find('> a')->attr('href', $link);
            }
        }
        foreach (pq('.ba-item-feature-box') as $value) {
            $id = pq($value)->attr('id');
            $i = 1;
            foreach ($obj->{$id}->items as $item) {
                if ($view != 'gridbox' && isset($item->unpublish) && $item->unpublish) {
                    continue;
                }
                $link = self::prepareGridboxLinks($item->button->href);
                pq($value)->find('.ba-feature-box:nth-child('.$i++.') .ba-feature-button a')->attr('href', $link);
            }
        }
        foreach (pq('.ba-item-icon-list') as $value) {
            $id = pq($value)->attr('id');
            $childs = pq($value)->find('ul li');
            foreach ($obj->{$id}->list as $key => $listValue) {
                if (empty($listValue->link)) {
                    continue;
                }
                $link = self::prepareGridboxLinks($listValue->link);
                foreach (pq($childs) as $ind => $child) {
                    if ($ind == $key - 1) {
                        pq($child)->find('a')->attr('href', $link);
                        break;
                    }
                }
            }
        }
        pq('.ba-slideshow-dots.thumbnails-dots')->empty();
        foreach (pq('.ba-item-slideshow, .ba-item-slideset, .ba-item-carousel') as $value) {
            $id = pq($value)->attr('id');
            if (pq($value)->find('.ba-slideshow-dots')->hasClass('dots-position-outside')) {
                pq($value)->find('.ba-slideshow-dots')->removeClass('dots-position-outside');
                pq($value)->find('.slideshow-wrapper')->addClass('dots-position-outside');
            }
        }
        foreach (pq('.ba-item-slideshow, .ba-item-slideset, .ba-item-carousel') as $value) {
            $id = pq($value)->attr('id');
            pq($value)->find('.slideshow-content')->removeAttr('style');
            pq($value)->find('.slideshow-content > li')->removeAttr('style');
            $i = 1;
            foreach ($obj->{$id}->desktop->slides as $key => $slide) {
                if ($view != 'gridbox' && isset($slide->unpublish) && $slide->unpublish) {
                    continue;
                }
                $btn = pq($value)->find('li:nth-child('.$i++.')')->find('.slideshow-button a');
                if (isset($slide->link) && !empty($slide->link)) {
                    $link = $slide->link;
                    $link = self::prepareGridboxLinks($link);
                    pq($btn)->attr('href', $link);
                } else {
                    $link = pq($btn)->attr('href');
                    $pos = strpos($link, '/'.IMAGE_PATH.'/');
                    if ($pos !== false) {
                        $link = substr($link, $pos + 1);
                        pq($btn)->attr('href', $link);
                    }
                }
            }
        }
        foreach (pq('.ba-item-content-slider') as $value) {
            $id = pq($value)->attr('id');
            $i = 1;
            foreach ($obj->{$id}->slides as $key => $slide) {
                if ($view != 'gridbox' && isset($slide->unpublish) && $slide->unpublish) {
                    continue;
                }
                $link = $slide->link->href;
                if (strpos($link, IMAGE_PATH) === 0) {
                    $link = JUri::root().$link;
                }
                $link = self::prepareGridboxLinks($link);
                pq($value)->find('> .slideshow-wrapper > ul > .slideshow-content > li:nth-child('.$i++.') > a')->attr('href', $link);
            }
        }
        foreach (pq('.ba-item-video') as $value) {
            $id = pq($value)->attr('id');
            if ($view != 'gridbox' && $obj->{$id}->video->type == 'youtube'
                && isset($obj->{$id}->lazyLoad) && $obj->{$id}->lazyLoad) {
                $id = $obj->{$id}->video->id;
                $str = '<div class="video-lazy-load-thumbnail" style="background-image: url(';
                $str .= 'https://img.youtube.com/vi/'.$id;
                $str .= '/maxresdefault.jpg);"><i class="ba-icons ba-icon-play-circle"></i></div>';
                pq($value)->find('.ba-video-wrapper')->html($str);
            } else if ($obj->{$id}->video->type == "source" && !self::isExternal($obj->{$id}->video->source->file)) {
                pq($value)->find('source')->attr('src', JUri::root().$obj->{$id}->video->source->file);
            }
        }
        foreach (pq('.ba-item-submission-form') as $value) {
            $id = pq($value)->attr('id');
            $page_id = $input->get('page_id', 0, 'int');
            $str = self::getSubmissionForm($obj->{$id}->app, $obj->{$id}->fields, $obj->{$id}->desktop->fields, $page_id);
            pq($value)->find('.ba-submission-form-wrapper')->html($str)->attr('data-id', $page->id)->attr('data-page', $page_id);
        }
        foreach (pq('.ba-item-submit-button') as $value) {
            $id = pq($value)->attr('id');
            $link = '#';
            if ($obj->{$id}->onsubmit->action == 'redirect') {
                $link = self::prepareGridboxLinks($obj->{$id}->onsubmit->redirect);
            }
            pq($value)->find('a')->attr('href', $link);
        }
        foreach (pq('.ba-item-event-calendar') as $value) {
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            $eventTime = time();
            if (!self::$editItem->start) {
                self::$editItem->start = 0;
            }
            $posts_type = isset(self::$editItem->posts_type) ? self::$editItem->posts_type : '';
            $tags = isset(self::$editItem->tags) ? self::$editItem->tags : new stdClass();
            $array = [];
            foreach ($tags as $tag) {
                $array[] = $tag->id;
            }
            $tagsStr = implode(', ', $array);
            $categories = isset(self::$editItem->categories) ? self::$editItem->categories : new stdClass();
            $array = [];
            foreach ($categories as $category) {
                $array[] = $category->id;
            }
            $categoriesStr = implode(', ', $array);
            $app_id = self::$editItem->app;
            $start = self::$editItem->start;
            $eventData = self::renderEventCalendarData($eventTime, $app_id, 0, $start * 1, $posts_type, $categoriesStr, $tagsStr);
            $menus = $app->getMenu('site');
            $menu = $menus->getActive();
            pq($value)->find('.ba-event-calendar-title')->html($eventData->title);
            pq($value)->find('.ba-event-calendar-header')->html($eventData->header);
            pq($value)->find('.ba-event-calendar-body')->html($eventData->body);
            pq($value)->find('.event-calendar-wrapper')->attr('data-year', $eventData->year);
            pq($value)->find('.event-calendar-wrapper')->attr('data-month', $eventData->month);
            pq($value)->find('.event-calendar-wrapper')->attr('data-menuitem', $menu->id);
        }
        foreach (pq('.ba-item-one-page-menu') as $value) {
            $itemId = pq($value)->attr('id');
            pq($value)->find('> .ba-menu-backdrop')->remove();
            pq($value)->append('<div class="ba-menu-backdrop"></div>');
            $wrapper = pq($value)->find('.ba-menu-wrapper');
            pq($wrapper)->removeClass('ba-menu-position-left');
            pq($wrapper)->removeClass('ba-hamburger-menu');
            pq($wrapper)->removeClass('ba-menu-position-center');
            if ($obj->{$itemId}->hamburger->enable) {
                pq($wrapper)->addClass('ba-hamburger-menu');
            }
            pq($wrapper)->addClass($obj->{$itemId}->hamburger->position);
        }
        foreach (pq('.ba-item-main-menu') as $value) {
            $menuId = pq($value)->attr('id');
            pq($value)->find('> .ba-menu-backdrop')->remove();
            pq($value)->append('<div class="ba-menu-backdrop"></div>');
            if (!isset($obj->{$menuId}->desktop->dropdown)) {
                $effect = 'fadeInUp';
            } else {
                $effect = $obj->{$menuId}->desktop->dropdown->animation->effect;
            }
            pq($value)->find('li.deeper.parent > ul')->addClass($effect);
            if (isset($obj->{$menuId}->items)) {
                foreach ($obj->{$menuId}->items as $key => $item) {
                    $li = pq($value)->find('li.item-'.$key.':first');
                    if (!empty($item->icon)) {
                        pq($li)->find(' > a, > span')->prepend('<i class="ba-menu-item-icon '.$item->icon.'"></i>');
                    }
                    if ($item->megamenu) {
                        pq($li)->addClass('megamenu-item');
                        pq($li)->addClass('deeper');
                        pq($li)->addClass('parent');
                        pq($li)->prepend(pq('#'.$menuId.' .ba-wrapper[data-megamenu="item-'.$key.'"]'));
                    }
                }
            }
            $i = '<i class="ba-icons ba-icon-caret-right"></i>';
            pq($value)->find('li.deeper.parent')->find('> a, > span')->find('> i.ba-icon-caret-right')->remove();
            pq($value)->find('li.deeper.parent')->find('> a, > span')->append($i);
            $wrapper = pq($value)->find(' > .ba-menu-wrapper');
            pq($wrapper)->removeClass('ba-menu-position-left');
            pq($wrapper)->removeClass('ba-hamburger-menu');
            pq($wrapper)->removeClass('ba-menu-position-center');
            pq($wrapper)->removeClass('ba-collapse-submenu');
            if ($obj->{$menuId}->hamburger->enable) {
                pq($wrapper)->addClass('ba-hamburger-menu');
            }
            if (isset($obj->{$menuId}->hamburger->collapse) && $obj->{$menuId}->hamburger->collapse) {
                pq($wrapper)->addClass('ba-collapse-submenu');
            }
            pq($wrapper)->addClass($obj->{$menuId}->hamburger->position);
        }
        foreach (pq('.ba-item-image, .ba-item-logo, .ba-item-overlay-section') as $value) {
            $itemId = pq($value)->attr('id');
            if ($obj->{$itemId}->type == 'overlay-button') {
                $img = pq($value)->find(' > .ba-image-wrapper img');
            } else {
                $img = pq($value)->find('img');
            }
            $src = $obj->{$itemId}->image;
            if (!self::isExternal($src)) {
                $img->attr('src', JUri::root().$src);
            }
        }
        foreach (pq('.ba-item-image-field') as $value) {
            $img = pq($value)->find('img');
            $src = JUri::root().'components/com_gridbox/assets/images/default-theme.png';
            $img->attr('src', $src);
        }
        $stars = pq('.ba-item-star-ratings');
        foreach ($stars as $value) {
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/plugins/star-ratings.php';
            pq($value)->find('> div[itemscope]')->replaceWith($out);
            list($str, $rating) = self::getStarRatings($id, $page);
            $width = ($rating - floor($rating)) * 100;
            $rating = floor($rating);
            $stars = pq($value)->find('.stars-wrapper i');
            pq($stars)->removeClass('active');
            pq($stars)->removeAttr('style');
            foreach (pq($stars) as $key => $star) {
                if ($key < $rating) {
                    pq($star)->addClass('active');
                    pq($star)->attr('style', '');
                    $last = $star;
                }
            }
            if ($rating == 0) {
                pq($stars)->addClass('active');
            }
            if ($rating != 5 && isset($last)) {
                $next = pq($last)->next();
                $next->attr('style', 'width:'.$width.'%');
            }
            pq($value)->find('.star-ratings-wrapper')->append($str);
            if (!empty(self::$editItem) && $view != 'gridbox') {
                $desktop = self::$editItem->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->view->rating) {
                pq($value)->find('.rating-wrapper')->remove();
            }
            if ($desktop && !$desktop->view->votes) {
                pq($value)->find('.votes-wrapper')->remove();
            }
        }
        if ($page->option == 'com_gridbox' && $view == 'gridbox') {
            foreach (pq('.ba-item-text .content-text a[href]') as $value) {
                $href = pq($value)->attr('href');
                pq($value)->attr('data-link', $href);
            }
        }
        foreach (pq('.ba-item-text .content-text a[href]') as $value) {
            $link = pq($value)->attr('href');
            if (strpos($link, IMAGE_PATH) === 0) {
                $link = JUri::root().$link;
            }
            $link = self::prepareGridboxLinks($link);
            pq($value)->attr('href', $link);
        }
        foreach (pq('.ba-item-custom-html') as $value) {
            pq($value)->find('style')->removeAttr('type');
        }
        foreach (pq('.ba-item-tags') as $value) {
            $id = pq($value)->attr('id');
            $tagsApp = pq($value)->attr('data-app');
            $tagsCat = pq($value)->attr('data-category');
            $tagsLimit = pq($value)->attr('data-limit');
            $sorting = isset($obj->{$id}->sorting) ? $obj->{$id}->sorting : 'hits';
            $str = self::getBlogTags($tagsApp, $tagsCat, $tagsLimit, $sorting);
            pq($value)->find('.ba-button-wrapper')->html($str);
        }
        foreach (pq('.ba-item-categories') as $value) {
            $id = pq($value)->attr('id');
            $catApp = pq($value)->attr('data-app');
            self::$editItem = $obj->{$id};
            $desktop = self::$editItem->desktop;
            $collapsible = isset($obj->{$id}->collapsible) ? $obj->{$id}->collapsible : false;
            $counter = $desktop->view->counter;
            $title = $desktop->view->title;
            $image = $desktop->view->image;
            $digital = self::getSubscriptionProducts();
            $items = self::getBlogCategories($catApp, 0, $counter, $desktop->view->sub, $digital);
            $str = self::getBlogCategoriesHtml($items, $obj->{$id}->maximum, $collapsible, $counter, $title, $image);
            if (!empty(self::$editItem->layout->layout) &&
                !pq($value)->find('.ba-categories-wrapper')->hasClass(self::$editItem->layout->layout)) {
                pq($value)->find('.ba-categories-wrapper')->addClass(self::$editItem->layout->layout);
            }
            pq($value)->find('.ba-categories-wrapper')->html($str);
            if ($desktop && !$desktop->view->intro) {
                pq($value)->find('.ba-blog-post-intro-wrapper')->remove();
            }
            if ($desktop && !$desktop->view->sub) {
                pq($value)->find('.ba-blog-post-info-wrapper')->remove();
            }
            if ($desktop && (!$desktop->view->title && !$desktop->view->sub && !$desktop->view->intro)) {
                pq($value)->find('.ba-blog-post-content')->remove();
            }
        }
        pq('.ba-search-result-modal')->remove();
        foreach (pq('.ba-item-search, .ba-item-store-search') as $value) {
            $id = pq($value)->attr('id');
            $url = '';
            $key = 'query';
            if (isset($obj->{$id}->app) && $obj->{$id}->app != '*'  && $obj->{$id}->app != 'multiple'
                && isset($obj->{$id}->results) && $obj->{$id}->results != '') {
                $url = 'index.php?option=com_gridbox&view=blog&app='.$obj->{$id}->app.'&id=0';
                $key = 'search';
                self::getGridboxMenuItems();
                $itemId = self::getGridboxMenuItemidByApp($obj->{$id}->app);
                if ($itemId) {
                    $url .= $itemId;
                }
            } else {
                $system = self::getSystemParamsByType($obj->{$id}->type);
                $url = $system ? self::getGridboxSystemLinks($system->id) : '';
            }
            if (!empty($url)) {
                $url .= '&'.$key.'=';
                $url = JRoute::_($url);
            }
            pq($value)->find('.ba-search-wrapper > input')->attr('data-search-url', $url);
        }
        foreach (pq('.ba-item-search-result, .ba-item-store-search-result') as $value) {
            $id = pq($value)->attr('id');
            $search = $input->get('query', '', 'string');
            $search = trim($search);
            self::$editItem = $obj->{$id};
            $start = $input->get('page', 1, 'int');
            $type = self::$editItem->type == 'search-result' ? 'search' : 'store-search';
            $limit = $obj->{$id}->limit;
            $max = $obj->{$id}->maximum;
            $pagination = isset(self::$editItem->layout->pagination) ? self::$editItem->layout->pagination : '';
            $str = self::getSearchResult($search, $limit, $start - 1, $max, $type, $pagination);
            phpQuery::selectDocument($dom->getDocumentID());
            if (empty($str)) {
                $str = '<p>'.JText::_('NO_MATCHING_SEARCH_RESULTS').'</p>';
            }
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            $str = self::getSearchResultPaginator($search, $limit, $start - 1, $max, $type, $pagination);
            pq($value)->find('.ba-blog-posts-pagination-wrapper')->remove();
            pq($value)->find('.ba-blog-posts-wrapper')->after($str);
        }
        if ($view != 'gridbox') {
            foreach (pq('.ba-item-search-result-headline .search-result-headline-wrapper > *') as $value) {
                $text = pq($value)->text();
                $search = $input->get('query', '', 'string');
                $search = trim($search);
                pq($value)->text($text.' '.$search);
            }
        }
        if ($view == 'page') {
            $fields = self::getPageFieldData();
            if (!empty($fields)) {
                $desktopFiles = self::getDesktopFieldFiles();
            }
            foreach ($fields as $key => $value) {
                if (empty($value->value) || $value->value == '[]') {
                    pq('#'.$value->field_key)->remove();
                    pq('.ba-field-wrapper[data-id="'.$value->field_key.'"]')->remove();
                    continue;
                }
                if ($value->field_type == 'field-google-maps') {
                    $mapOptions = json_decode($value->value);
                    if (empty($mapOptions->marker->place)) {
                        pq('#'.$value->field_key)->remove();
                        pq('.ba-field-wrapper[data-id="'.$value->field_key.'"]')->remove();
                    }
                    continue;
                } else if ($value->field_type == 'radio' || $value->field_type == 'select') {
                    $str = '';
                    $fieldOptions = json_decode($value->options);
                    foreach ($fieldOptions->items as $fieldOption) {
                        if ($fieldOption->key == $value->value) {
                            $str = $fieldOption->title;
                            break;
                        }
                    }
                } else if ($value->field_type == 'checkbox') {
                    $str = '';
                    $fieldOptions = json_decode($value->options);
                    $valueOptions = json_decode($value->value);
                    foreach ($valueOptions as $valueOption) {
                        foreach ($fieldOptions->items as $fieldOption) {
                            if ($fieldOption->key == $valueOption) {
                                $str .= '<span>'.$fieldOption->title.'</span>';
                            }
                        }
                    }
                } else if ($value->field_type == 'file') {
                    $fieldOptions = json_decode($value->options);
                    if (is_numeric($value->value) && isset($desktopFiles->{$value->value})) {
                        $desktopFile = $desktopFiles->{$value->value};
                        $src = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                    } else {
                        $src = $value->value;
                    }
                    if (!self::isExternal($src)) {
                        $src = JUri::root().$src;
                    }
                    $str = '<a href="'.$src.'" download>'.$fieldOptions->title.'</a>';
                } else if ($value->field_type == 'url') {
                    $fieldOptions = json_decode($value->options);
                    $valueOptions = json_decode($value->value);
                    $link = self::prepareGridboxLinks($valueOptions->link);
                    if (empty($link)) {
                        pq('#'.$value->field_key)->remove();
                        pq('.ba-field-wrapper[data-id="'.$value->field_key.'"]')->remove();
                        continue;
                    }
                    $str = '<a href="'.$link.'" '.$fieldOptions->download.' target="'.$fieldOptions->target;
                    $str .= '">'.$valueOptions->label.'</a>';
                } else if ($value->field_type == 'field-button') {
                    $fieldOptions = json_decode($value->options);
                    $valueOptions = json_decode($value->value);
                    $link = self::prepareGridboxLinks($valueOptions->link);
                    if (empty($link)) {
                        pq('#'.$value->field_key)->remove();
                        pq('.ba-field-wrapper[data-id="'.$value->field_key.'"]')->remove();
                        continue;
                    }
                    if ($fieldOptions->download == 'tel' || $fieldOptions->download == 'mailto') {
                        $link = $fieldOptions->download.':'.$link;
                        $fieldOptions->download = '';
                    }
                    $btnLabel = !empty($fieldOptions->label_type) ? $fieldOptions->constant : $valueOptions->label;
                    $str = '<a class="ba-btn-transition" href="'.$link.'" '.$fieldOptions->download.' target="'.$fieldOptions->target;
                    $str .= '"><span>'.$btnLabel.'</span>';
                    if (!empty($fieldOptions->icon)) {
                        $str .= '<i class="'.$fieldOptions->icon.'"></i>';
                    }
                    $str .= '</a>';
                } else if ($value->field_type == 'image-field') {
                    $valueOptions = json_decode($value->value);
                    $src = $valueOptions->src;
                    if (is_numeric($valueOptions->src) && isset($desktopFiles->{$valueOptions->src})) {
                        $desktopFile = $desktopFiles->{$valueOptions->src};
                        $src = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                    } else if (is_numeric($valueOptions->src)) {
                        $src = '';
                    }
                    if (empty($src)) {
                        pq('#'.$value->field_key)->remove();
                        pq('.ba-field-wrapper[data-id="'.$value->field_key.'"]')->remove();
                        continue;
                    }
                    if (!self::isExternal($src)) {
                        $src = JUri::root().$src;
                    }
                    $str = '<img src="'.$src.'" alt="'.$valueOptions->alt.'">';
                } else if ($value->field_type == 'tag') {
                    $str = self::getPostTags($page->id);
                } else if ($value->field_type == 'field-simple-gallery' || $value->field_type == 'product-gallery') {
                    $valueOptions = json_decode($value->value);
                    $str = '';
                    foreach ($valueOptions as $key => $valueOption) {
                        if (isset($valueOption->unpublish) && $valueOption->unpublish) {
                            continue;
                        }
                        if (is_numeric($valueOption->img) && isset($desktopFiles->{$valueOption->img})) {
                            $desktopFile = $desktopFiles->{$valueOption->img};
                            $img = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                        } else {
                            $img = $valueOption->img;
                        }
                        if (!self::isExternal($img)) {
                            $img = JUri::root().$img;
                        }
                        $str .= '<div class="ba-instagram-image" style="background-image: url('.str_replace(' ', '%20', $img).');">';
                        $str .= '<img src="'.$img.'" data-src="'.$valueOption->img.'" alt="'.$valueOption->alt;
                        $str .= '"><div class="ba-simple-gallery-image"></div></div>';
                    }
                } else if ($value->field_type == 'field-slideshow' || $value->field_type == 'product-slideshow') {
                    $valueOptions = json_decode($value->value);
                    $str = '';
                    $slideshowStyle = '';
                    $key = 0;
                    foreach ($valueOptions as $valueOption) {
                        if (isset($valueOption->unpublish) && $valueOption->unpublish) {
                            continue;
                        }
                        if (is_numeric($valueOption->img) && isset($desktopFiles->{$valueOption->img})) {
                            $desktopFile = $desktopFiles->{$valueOption->img};
                            $img = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                        } else {
                            $img = $valueOption->img;
                        }
                        if (!self::isExternal($img)) {
                            $img = JUri::root().$img;
                        }
                        $slideshowStyle .= '--thumbnails-dots-image-'.$key.': url('.$img.');';
                        $str .= '<li class="item'.($key == 0 ? ' active' : '');
                        $str .= '"><div class="ba-slideshow-img" data-src="'.$img;
                        $str .= '" style="background-image: url('.$img.');"></div></li>';
                        $key++;
                    }
                    pq('#'.$value->field_key.' .ba-slideshow-dots.thumbnails-dots')->attr('style', $slideshowStyle);
                    pq('.ba-field-wrapper[data-id="'.$value->field_key.'"] .ba-slideshow-dots.thumbnails-dots')
                        ->attr('style', $slideshowStyle);
                } else if ($value->field_type == 'field-video') {
                    $valueOptions = json_decode($value->value);
                    if ($valueOptions->type == 'youtube') {
                        $str = '<iframe src="https://www.youtube.com/embed/'.$valueOptions->id.'?showinfo=1&controls=1&autoplay=0"';
                        $str .= ' frameborder="0" allowfullscreen></iframe>';
                    } else if ($valueOptions->type == 'vimeo') {
                        $str = '<iframe src="https://player.vimeo.com/video/'.$valueOptions->id.'?autoplay=0&loop=0"';
                        $str .= ' frameborder="0" allowfullscreen></iframe>';
                    } else {
                        if (is_numeric($valueOptions->file) && isset($desktopFiles->{$valueOptions->file})) {
                            $desktopFile = $desktopFiles->{$valueOptions->file};
                            $img = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                        } else {
                            $img = $valueOptions->file;
                        }
                        if (!self::isExternal($img)) {
                            $img = JUri::root().$img;
                        }
                        $str = '<video controls><source src="'.$img.'" type="video/mp4"></video>';
                    }
                } else if ($value->field_type == 'time') {
                    $valueOptions = json_decode($value->value);
                    $str = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
                } else if ($value->field_type == 'date' || $value->field_type == 'event-date') {
                    $str = self::formatDate($value->value);
                } else if ($value->field_type == 'price') {
                    $fieldOptions = json_decode($value->options);
                    $thousand = $fieldOptions->thousand;
                    $separator = $fieldOptions->separator;
                    $decimals = $fieldOptions->decimals;
                    $price = self::preparePrice($value->value, $thousand, $separator, $decimals, 1);
                    pq('#'.$value->field_key.' .ba-field-content .field-price-value')->text($price);
                    pq('.ba-field-wrapper[data-id="'.$value->field_key.'"] .ba-field-content .field-price-value')->text($price);
                    continue;
                } else if ($value->field_type == 'text') {
                    $str = htmlspecialchars($value->value);
                } else {
                    $fieldOptions = json_decode($value->options);
                    $str = $fieldOptions->texteditor ? $value->value : str_replace("\n", '<br>', $value->value);
                    $str = JHtml::_('content.prepare', $str);
                }
                pq('#'.$value->field_key.' .ba-field-content')->html($str);
                pq('.ba-field-wrapper[data-id="'.$value->field_key.'"] .ba-field-content')->html($str);
            }
            foreach (pq('.ba-item-field-group') as $value) {
                $removeFlag = true;
                foreach (pq($value)->find('.ba-field-wrapper') as $fieldW) {
                    $removeFlag = false;
                    break;
                }
                if ($removeFlag) {
                    pq($value)->remove();
                }
            }
        }
        foreach (pq('.ba-field-label') as $value) {
            $text = pq($value)->text();
            if (empty($text) && $view == 'page') {
                pq($value)->remove();
            } else if (empty($text)) {
                pq($value)->addClass('empty-content');
            }
        }
        foreach (pq('.ba-item-recent-comments') as $value) {
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            $application = self::$editItem->app;
            $sorting = self::$editItem->sorting;
            $limit = self::$editItem->limit;
            $maximum = self::$editItem->maximum;
            $categories = self::$editItem->categories;
            $category = '';
            if (!empty($category)) {
                $cats = array();
                foreach ($category as $cat) {
                    $cats[] = $cat->id;
                }
                $category = implode(', ', $cats);
            }
            $str = self::getRecentComments($application, $sorting, $limit, $maximum, $category);
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            if (!empty(self::$editItem) && $view != 'gridbox') {
                $desktop = self::$editItem->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->view->image) {
                pq($value)->find('.ba-blog-post-image')->remove();
            }
            if ($desktop && !$desktop->view->date) {
                pq($value)->find('.ba-blog-post-date')->remove();
            }
            if ($desktop && !$desktop->view->intro) {
                pq($value)->find('.ba-blog-post-intro-wrapper')->remove();
            }
            if ($desktop && !$desktop->view->title) {
                pq($value)->find('.ba-blog-post-title-wrapper')->remove();
            }
        }
        foreach (pq('.ba-item-recent-reviews') as $value) {
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            $application = self::$editItem->app;
            $sorting = self::$editItem->sorting;
            $limit = self::$editItem->limit;
            $maximum = self::$editItem->maximum;
            $categories = self::$editItem->categories;
            $category = '';
            if (!empty($categories)) {
                $cats = array();
                foreach ($categories as $cat) {
                    $cats[] = $cat->id;
                }
                $category = implode(', ', $cats);
            }
            $str = self::getRecentReviews($application, $sorting, $limit, $maximum, $category);
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            if (!empty(self::$editItem) && $view != 'gridbox') {
                $desktop = self::$editItem->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->view->image) {
                pq($value)->find('.ba-blog-post-image')->remove();
            }
            if ($desktop && !$desktop->view->date) {
                pq($value)->find('.ba-blog-post-date')->remove();
            }
            if ($desktop && !$desktop->view->intro) {
                pq($value)->find('.ba-blog-post-intro-wrapper')->remove();
            }
            if ($desktop && !$desktop->view->title) {
                pq($value)->find('.ba-reviews-name')->remove();
            }
            if ($desktop && !$desktop->view->source) {
                pq($value)->find('.ba-reviews-source')->remove();
            }
            if ($desktop && !$desktop->view->title && !$desktop->view->source) {
                pq($value)->find('.ba-blog-post-title-wrapper')->remove();
            }
        }
        foreach (pq('.ba-item-recent-posts') as $value) {
            $application = pq($value)->attr('data-app');
            $sorting = pq($value)->attr('data-sorting');
            $limit = pq($value)->attr('data-count');
            $maximum = pq($value)->attr('data-maximum');
            $category = pq($value)->attr('data-category');
            $id = pq($value)->attr('id');
            if (isset($obj->{$id}->featured)) {
                $featured = $obj->{$id}->featured;
            } else {
                $featured = false;
            }
            $pagination = $obj->{$id}->layout->pagination;
            self::$editItem = $obj->{$id};
            $posts_type = isset(self::$editItem->posts_type) ? self::$editItem->posts_type : '';
            $tags = isset(self::$editItem->tags) ? self::$editItem->tags : new stdClass();
            $array = [];
            foreach ($tags as $tag) {
                $array[] = $tag->id;
            }
            $tagsStr = implode(', ', $array);
            $paginationStr = self::getRecentPostsPagination($application, $limit, $category, $featured, 0, $pagination, $posts_type, $tagsStr);
            $str = self::getRecentPosts($application, $sorting, $limit, $maximum, $category, $featured, 0, '', $posts_type, $tagsStr);
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            pq($value)->find('.ba-blog-posts-pagination')->remove();
            if ($paginationStr) {
                pq($value)->find('.ba-blog-posts-wrapper')->after($paginationStr);
            }
        }
        foreach (pq('.ba-item-fields-filter') as $value) {
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            $app_id = $input->get('app', 0, 'input');
            $category_id = $input->get('id', 0, 'input');
            $tag_id = $input->get('tag', 0, 'input');
            $author_id = $input->get('author', 0, 'input');
            if (empty($app_id) || self::$editItem->app != $app_id || $view != 'blog') {
                $category_id = $tag_id = $author_id = 0;
                $app_id = self::$editItem->app;
            }
            if (!empty($tag_id)) {
                $url = self::getGridboxTagLinks($tag_id, $app_id);
            } else if (!empty($author_id)) {
                $url = self::getGridboxAuthorLinks($author_id, $app_id);
            } else {
                $url = self::getGridboxCategoryLinks($category_id, $app_id);
            }
            $order = $input->get('sort-by', '', 'string');
            $search = $input->get('search', '', 'string');
            $url = JRoute::_($url);
            $symbol = strpos($url, '?') === false ? '?' : '&';
            if (!empty($search)) {
                $url .= $symbol.'search='.$search;
                $symbol = '&';
            }
            if (!empty($order)) {
                $url .= $symbol.'sort-by='.$order;
                $symbol = '&';
            }
            $url .= $symbol.'query=';
            pq($value)->find('.open-responsive-filters span')->text(JText::_('FILTERS'));
            pq($value)->find('.ba-fields-filter-wrapper')->attr('data-query', $url);
            pq($value)->find('.ba-fields-filter-wrapper')->attr('data-category', $category_id);
            pq($value)->find('.ba-fields-filter-wrapper')->attr('data-tag', $tag_id);
            pq($value)->find('.ba-fields-filter-wrapper')->attr('data-author', $author_id);
            $str = self::getItemsFilter(self::$editItem->app);
            pq($value)->find('.ba-fields-filter-wrapper')->html($str);
            pq($value)->find('.open-responsive-filters i.zmdi')->removeClass('zmdi')
                ->removeClass('zmdi-filter-list')->addClass('ba-icons ba-icon-filter-list');
            if (isset(self::$editItem->collapsible) && self::$editItem->collapsible) {
                $first = pq($value)->find('.ba-field-filter')->get(0);
                if (!empty(self::$editItem->fields)) {
                    foreach (self::$editItem->fields as $field) {
                        if (self::$editItem->desktop->fields->{$field}) {
                            $firstOrder = pq($value)->find('.ba-field-filter[data-id="'.$field.'"]')->get(0);
                            if ($firstOrder) {
                                $first = $firstOrder;
                                break;
                            }
                        }
                    }
                }
                pq($value)->find('.ba-field-filter')->addClass('ba-filter-collapsed ba-filter-icon-rotated');
                pq($first)->removeClass('ba-filter-collapsed')->removeClass('ba-filter-icon-rotated');
            }
            if (isset(self::$editItem->auto) && self::$editItem->auto) {
                pq($value)->find('.ba-items-filter-search-button')->remove();
            }
        }
        foreach (pq('.ba-item-author') as $key => $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $id = $input->get('id', 0, 'int');
            $str = self::getPostAuthor($id);
            pq($value)->find('.ba-posts-author-wrapper')->html($str);
        }
        foreach (pq('.ba-item-related-posts') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $related = pq($value)->attr('data-related');
            $limit = pq($value)->attr('data-count');
            $maximum = pq($value)->attr('data-maximum');
            $str = self::getRelatedPosts(0, $related, $limit, $maximum, self::$editItem->sorting);
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
        }
        foreach (pq('.ba-item-related-posts-slider') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $related = self::$editItem->related;
            $limit = self::$editItem->limit;
            $maximum = self::$editItem->maximum;
            $str = self::getRelatedPosts(0, $related, $limit, $maximum);
            pq($value)->find('.slideshow-content')->html($str);
        }
        foreach (pq('.ba-item-recently-viewed-products') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $limit = self::$editItem->limit;
            $maximum = self::$editItem->maximum;
            $str = self::getRecentlyViewedProducts($limit, $maximum);
            pq($value)->find('.slideshow-content')->html($str);
        }
        foreach (pq('.ba-item-post-navigation') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $maximum = pq($value)->attr('data-maximum');
            $str = self::getPostNavigation($maximum);
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            pq($value)->find('.ba-blog-posts-wrapper > i')->remove();
            $posts = pq($value)->find('.ba-blog-posts-wrapper .ba-blog-post');
            foreach (pq($posts) as $key => $post) {
                if ($key == 0) {
                    $title = JText::_('PREVIOUS');
                } else {
                    $title = JText::_('NEXT');
                }
                pq($post)->find('.ba-post-navigation-info a')->text($title);
            }
        }
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        foreach (pq('.ba-edit-item') as $value) {
            $parent = pq($value)->parent();
            $itemId = pq($parent)->attr('id');
            if (isset($obj->{$itemId}) && isset($obj->{$itemId}->access) && !in_array($obj->{$itemId}->access, $groups)) {
                pq($parent)->addClass('ba-user-level-edit-denied');
            } else {
                pq($parent)->removeClass('ba-user-level-edit-denied');
            }
            pq($value)->attr('style', '');
        }
        foreach (pq('.ba-item-login') as $value) {
            if ($view != 'gridbox' && !empty($user->id)) {
                pq($value)->remove();
                continue;
            }
            $id = pq($value)->attr('id');
            self::$editItem = $obj->{$id};
            $str = self::getLoginHTML($view);
            $link = self::prepareGridboxLinks(self::$editItem->options->redirect);
            $link = JRoute::_($link);
            if (!empty(self::$editItem->options->recaptcha)) {
                self::setCaptcha(self::$editItem->options->recaptcha);
            }
            pq($value)->find('.ba-login-content-wrapper')->attr('data-redirect', $link);
            pq($value)->find('.ba-login-content-wrapper')->html($str);
        }
        $integrations = [];
        foreach (pq('.ba-login-integration-btn') as $value) {
            $key = pq($value)->attr('data-integration');
            if ($view == 'gridbox' || isset($integrations[$key])) {
                continue;
            }
            $integrations[$key] = self::getIntegrationKey($key.'_login');
        }
        foreach (pq('.ba-item-checkout-form') as $value) {
            if (self::$store->checkout->facebook && $view != 'gridbox' && !isset($integrations['facebook'])) {
                $integrations['facebook'] = self::getIntegrationKey('facebook_login');
            }
            if (self::$store->checkout->google && $view != 'gridbox' && !isset($integrations['google'])) {
                $integrations['google'] = self::getIntegrationKey('google_login');
            }
        }
        foreach (pq('.ba-comments-facebook-login') as $value) {
            if ($view == 'gridbox' || isset($integrations['facebook'])) {
                break;
            }
            $integrations['facebook'] = self::getIntegrationKey('facebook_login');
        }
        foreach (pq('.ba-comments-google-login') as $value) {
            if ($view == 'gridbox' || isset($integrations['google'])) {
                break;
            }
            $integrations['google'] = self::getIntegrationKey('google_login');
        }
        foreach (pq('.ba-comments-vk-login') as $value) {
            if ($view == 'gridbox' || isset($integrations['vk'])) {
                break;
            }
            $integrations['vk'] = self::getIntegrationKey('vk_login');
        }
        foreach (pq('.ba-item-facebook-comments') as $value) {
            if ($view == 'gridbox' || isset($integrations['facebook_comments'])) {
                break;
            }
            $integrations['facebook_comments'] = self::getIntegrationKey('facebook_comments');
        }
        foreach (pq('.ba-item-hypercomments') as $value) {
            if ($view == 'gridbox' || isset($integrations['hypercomments'])) {
                break;
            }
            $integrations['hypercomments'] = self::getIntegrationKey('hypercomments');
        }
        foreach (pq('.ba-item-vk-comments') as $value) {
            if ($view == 'gridbox' || isset($integrations['vk_comments'])) {
                break;
            }
            $integrations['vk_comments'] = self::getIntegrationKey('vk_comments');
        }
        foreach (pq('.ba-item-disqus') as $value) {
            if ($view == 'gridbox' || isset($integrations['disqus'])) {
                break;
            }
            $integrations['disqus'] = self::getIntegrationKey('disqus');
        }
        if (!empty($integrations)) {
            $str = json_encode($integrations);
            $doc->addScriptDeclaration('window.integrations = '.$str.';');
        }
        foreach (pq('.ba-item-blog-posts') as $value) {
            $flag = false;
            foreach (pq('.ba-item-category-intro') as $intro) {
                $flag = true;
            }
            if (!$flag){
                include(JPATH_ROOT.'/components/com_gridbox/views/layout/category-intro.php');
                $str = $out;
                pq('.ba-item-blog-posts')->before($str);
                $app = $input->getCmd('id', 0);
                if ($view != 'gridbox') {
                    $app = $input->getCmd('app', 0);
                    pq('.ba-edit-item, .ba-box-model')->remove();
                }
                $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/app-'.$app.'.css';
                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }
        }
        foreach (pq('.ba-item-category-intro') as $key => $intro) {
            $id = pq($intro)->attr('id');
            $item = $obj->{$id};
            if (!empty($item) && $view != 'gridbox') {
                $desktop = $item->desktop;
            } else {
                $desktop = null;
            }
            $tag = $input->get('tag', '');
            $author = $input->get('author', '');
            $postContent = self::getCategoryIntro();
            $postHTML = pq($intro)->find('.intro-post-wrapper')->html();
            $search = $input->get('search', '', 'string');
            if (!empty($search)) {
                $postContent->title = JText::_('SEARCH_RESULTS_FOR').' '.$search;
            }
            if (trim($postHTML) == '') {
                $postHTML = '[intro-post-image]'.
                    '<div class="intro-post-title-wrapper"><h1 class="intro-post-title"></h1></div>'.
                    '<div class="intro-post-info"><div class="intro-category-description"></div></div>';
            }
            $postHTML = str_replace('[intro-post-image]', $postContent->image, $postHTML);
            pq($intro)->find('.intro-post-wrapper')->html($postHTML);
            pq($intro)->find('.intro-post-wrapper .intro-post-image-wrapper')->replaceWith($postContent->image);
            pq($intro)->find('.intro-post-wrapper .intro-category-description')->html($postContent->description);
            if (isset($postContent->social)) {
                pq($intro)->find('.intro-post-wrapper .intro-post-info')->after($postContent->social);
            }
            pq($intro)->find('.intro-post-wrapper .intro-post-title')->html($postContent->title);
            if ($desktop && !$desktop->image->show && $item->layout->layout != 'fullscreen-post') {
                pq($intro)->find('.intro-post-image-wrapper')->remove();
            }
            if ($desktop && !$desktop->title->show) {
                pq($intro)->find('.intro-post-title-wrapper')->remove();
            }
            if ($desktop && !$desktop->info->show) {
                pq($intro)->find('.intro-post-info')->remove();
            }
        }
        foreach (pq('.ba-item-post-intro') as $value) {
            $id = pq($value)->attr('id');
            $item = $obj->{$id};
            self::$editItem = $item;
            if (!empty($item) && $view != 'gridbox') {
                $desktop = $item->desktop;
            } else {
                $desktop = null;
            }
            $postContent = self::getBlogPostIntro();
            pq($value)->find('.intro-post-wrapper .intro-post-info')->html($postContent->info);
            pq($value)->find('.intro-post-wrapper .intro-post-title')->text($postContent->title);
            pq($value)->find('.intro-post-wrapper .intro-post-title')->removeAttr('contenteditable');
            $postHTML = pq($value)->find('.intro-post-wrapper')->html();
            $postHTML = str_replace('[intro-post-image]', $postContent->image, $postHTML);
            pq($value)->find('.intro-post-wrapper')->html($postHTML);
            if ($desktop && !$desktop->image->show && $item->layout->layout != 'fullscreen-post') {
                pq($value)->find('.intro-post-image-wrapper')->remove();
            }
            if ($desktop && !$desktop->title->show) {
                pq($value)->find('.intro-post-title-wrapper')->remove();
            }
        }
        pq('.ba-item-instagram')->remove();
        foreach (pq('.ba-item-weather') as $value) {
            $openWeatherMapKey = self::getIntegrationKey('openweathermap');
            break;
        }
        foreach (pq('.ba-item-weather') as $key => $value) {
            if (empty($openWeatherMapKey)) {
                break;
            }
            $id = pq($value)->attr('id');
            $item = $obj->{$id};
            if (empty($item->weather->location)) {
                continue;
            }
            $weather = self::getWeather($item, $id, $openWeatherMapKey);
            if ($weather) {
                pq($value)->find('.ba-weather')->html($weather);
            }
            if (!empty($item) && $view != 'gridbox') {
                $desktop = $item->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->view->wind) {
                pq($value)->find('.weather-info .wind')->remove();
            }
            if ($desktop && !$desktop->view->humidity) {
                pq($value)->find('.weather-info .humidity')->remove();
            }
            if ($desktop && !$desktop->view->pressure) {
                pq($value)->find('.weather-info .pressure')->remove();
            }
            if ($desktop && (!$desktop->view->pressure && !$desktop->view->humidity && !$desktop->view->wind)) {
                pq($value)->find('.weather-info')->remove();
            }
            if ($desktop) {
                foreach (pq($value)->find('.forecast') as $i => $forecast) {
                    if ($i >= $desktop->view->forecast) {
                        pq($forecast)->remove();
                    }
                }
            }
        }
        foreach (pq('.ba-item-countdown') as $value) {
            $id = pq($value)->attr('id');
            $item = $obj->{$id};
            if ($view != 'gridbox' && empty($item->days)) {
                pq($value)->find('.days .countdown-label')->remove();
            }
            if ($view != 'gridbox' && empty($item->hours)) {
                pq($value)->find('.hours .countdown-label')->remove();
            }
            if ($view != 'gridbox' && empty($item->minutes)) {
                pq($value)->find('.minutes .countdown-label')->remove();
            }
            if ($view != 'gridbox' && empty($item->seconds)) {
                pq($value)->find('.seconds .countdown-label')->remove();
            }
        }
        foreach (pq('.ba-item-error-message') as $value) {
            $code = '{gridbox_error_code}';
            $message = '{gridbox_error_message}';
            if ($view == 'gridbox' && $edit_type == 'system') {
                $code = '404';
                $message = JText::_('NOT_FOUND');
            }
            pq($value)->find('.ba-error-code')->text($code);
            pq($value)->find('.ba-error-message')->text($message);
            $id = pq($value)->attr('id');
            $item = $obj->{$id};
            if (!empty($item) && $view != 'gridbox') {
                $desktop = $item->desktop;
            } else {
                $desktop = null;
            }
            if ($desktop && !$desktop->view->code) {
                pq($value)->find('.ba-error-code')->remove();
            }
            if ($desktop && !$desktop->view->message) {
                pq($value)->find('.ba-error-message')->remove();
            }
        }
        $str = '.ba-item-slideshow, .ba-item-field-slideshow, .ba-item-product-slideshow';
        foreach (pq($str) as $value) {
            $id = pq($value)->attr('id');
            if ($view != 'gridbox' && !$obj->{$id}->desktop->view->arrows) {
                pq($value)->find('.ba-slideset-nav, .ba-slideshow-nav')->remove();
            }
            if ($view != 'gridbox' && isset($obj->{$id}->dots) && $obj->{$id}->dots->layout == 'disabled-dots') {
                pq($value)->find('.ba-slideset-dots, .ba-slideshow-dots')->remove();
            }
        }
        $str = '.ba-item-testimonials, .ba-item-content-slider, .ba-item-slideset, ';
        $str .= '.ba-item-carousel, .ba-item-recent-posts-slider, .ba-item-related-posts-slider, ';
        $str .= '.ba-item-recently-viewed-products';
        foreach (pq($str) as $value) {
            $id = pq($value)->attr('id');
            if ($view != 'gridbox' && !$obj->{$id}->desktop->view->arrows) {
                pq($value)->find('.ba-slideset-nav, .ba-slideshow-nav')->remove();
            }
            if ($view != 'gridbox' && !$obj->{$id}->desktop->view->dots) {
                pq($value)->find('.ba-slideset-dots, .ba-slideshow-dots')->remove();
            }
        }
        foreach (pq('.ba-item-recent-posts-slider') as $value) {
            $id = pq($value)->attr('id');
            $application = $obj->{$id}->app;
            $sorting = $obj->{$id}->sorting;
            $limit = $obj->{$id}->limit;
            $maximum = $obj->{$id}->maximum;
            $categories = $obj->{$id}->categories;
            if (isset($obj->{$id}->featured)) {
                $featured = $obj->{$id}->featured;
            } else {
                $featured = false;
            }
            $array = array();
            foreach ($categories as $catId => $cat) {
                $array[] = $catId;
            }
            $category = implode(',', $array);
            self::$editItem = $obj->{$id};
            $posts_type = isset(self::$editItem->posts_type) ? self::$editItem->posts_type : '';
            $tags = isset(self::$editItem->tags) ? self::$editItem->tags : new stdClass();
            $array = [];
            foreach ($tags as $tag) {
                $array[] = $tag->id;
            }
            $tagsStr = implode(', ', $array);
            $str = self::getRecentPosts($application, $sorting, $limit, $maximum, $category, $featured, 0, '', $posts_type, $tagsStr);
            pq($value)->find('.slideshow-content')->html($str);
            foreach (pq($value)->find('li.item') as $key => $postLi) {
                if ($key == $obj->{$id}->desktop->slideset->count) {
                    break;
                }
                pq($postLi)->addClass('active');
            }
        }
        foreach (pq('.ba-item-blog-posts') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $id = $input->get('id', 0, 'int');
            $category = $input->get('category', 0, 'int');
            $application = $input->get('app', 0, 'int');
            if (!empty($application) && $view != 'gridbox') {
                $category = $id;
                $id = $application;
            }
            $start = $input->get('page', 1, 'int');
            $max = $obj->{$itemId}->maximum;
            $limit = $obj->{$itemId}->limit;
            $order = isset($obj->{$itemId}->order) ? $obj->{$itemId}->order : 'created';
            $isStore = self::$storeHelper->checkAppType($id);
            if ($isStore) {
                $order = $input->get('sort-by', $order, 'string');
            }
            $pagination = isset(self::$editItem->pagination) ? self::$editItem->pagination : '';
            $str = self::getBlogPosts($id, $max, $limit, $start - 1, $category, $order, $pagination);
            if (empty($str)) {
                $str = self::getEmptyList();
            }
            pq($value)->find('.ba-blog-posts-wrapper')->html($str);
            $str = self::getBlogPostsHeader($isStore, $id, $category, $order);
            pq($value)->find('.ba-blog-posts-header')->html($str);
            $str = self::getBlogPagination($id, $start - 1, $limit, $category, $pagination);
            pq($value)->find('.ba-blog-posts-pagination-wrapper')->html($str);
            if ($view != 'gridbox' && isset(self::$editItem->desktop->view->sorting)
                && !self::$editItem->desktop->view->sorting) {
                pq($value)->find('.blog-posts-sorting-wrapper')->remove();
            }
        }
        foreach (pq('.ba-item-google-maps-places') as $value) {
            $menus = $app->getMenu('site');
            $menu = $menus->getActive();
            $itemId = pq($value)->attr('id');
            $pages = self::getMapsPlacesPostsList($obj->{$itemId}->app);
            pq($value)->find('.ba-map-wrapper')->attr('data-menuitem', $menu->id);
            pq($value)->find('.ba-map-wrapper')->attr('data-pages', $pages);
        }
        foreach (pq('.ba-item-add-to-cart') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            if ($view == 'gridbox') {
                $out = self::getEditorAddToCartHTML($page->id);
            } else if (self::getPageType($page->id) == 'products') {
                $out = self::getAddToCartHTML($page->id, $input);
            } else {
                $out = self::getBookingAddToCartHTML($page->id, $input);
            }
            pq($value)->find('.ba-add-to-cart-wrapper')->html($out);
            $url = self::getStoreSystemUrl('checkout');
            pq($value)->find('a')->attr('data-url', $url);
            if ($view != 'gridbox') {
                if (!empty(self::$editItem)) {
                    $desktop = self::$editItem->desktop;
                } else {
                    $desktop = null;
                }
                if ($desktop && !$desktop->view->availability) {
                    pq($value)->find('.ba-add-to-cart-stock')->remove();
                }
                if ($desktop && !$desktop->view->button) {
                    pq($value)->find('.ba-add-to-cart-buttons-wrapper a')->remove();
                }
                if ($desktop && !$desktop->view->quantity) {
                    pq($value)->find('.ba-add-to-cart-quantity')->remove();
                }
                if ($desktop && !$desktop->view->sku) {
                    pq($value)->find('.ba-add-to-cart-sku')->remove();
                }
                if ($desktop && !$desktop->view->wishlist) {
                    pq($value)->find('span.ba-add-to-wishlist')->remove();
                }
            }
        }
        foreach (pq('.ba-item-wishlist') as $value) {
            if ($view != 'gridbox') {
                $itemId = pq($value)->attr('id');
                self::$editItem = $obj->{$itemId};
                $wishId = self::getWishlistId();
                $wishlist = self::getStoreWishlist($wishId);
                pq($value)->find('a i')->attr('data-products-count', $wishlist->quantity);
                if (empty(self::$editItem->title)) {
                    pq($value)->find('.ba-wishlist-title')->remove();
                }
            }
        }
        $cartId = $input->cookie->get('gridbox_store_cart', 0, 'int');
        $cart = null;
        foreach (pq('.ba-item-cart') as $value) {
            pq($value)->find('.store-currency-symbol')->text(self::$store->currency->symbol);
            pq($value)->find('.ba-cart-subtotal')->removeClass('right-currency-position');
            pq($value)->find('.ba-cart-subtotal')->addClass(self::$store->currency->position);
            $url = self::getStoreSystemUrl('checkout');
            pq($valid)->find('a')->attr('data-url', $url);
            if ($view != 'gridbox') {
                $cart = !empty($cart) ? $cart : self::getStoreCart($cartId);
                pq($value)->find('a i')->attr('data-products-count', $cart->quantity);
                $currency = self::$store->currency;
                $total = self::preparePrice($cart->total, $currency->thousand, $currency->separator, $currency->decimals);
                pq($value)->find('.store-currency-price')->text($total);
                $itemId = pq($value)->attr('id');
                self::$editItem = $obj->{$itemId};
                if (!self::$editItem->desktop->view->subtotal) {
                    pq($value)->find('.ba-cart-subtotal')->remove();
                }
                if (isset(self::$editItem->desktop->view->empty) && self::$editItem->desktop->view->empty && count($cart->products) == 0) {
                    pq($value)->attr('style', 'display: none;');
                }
            }
        }
        foreach (pq('.ba-item-checkout-order-form') as $value) {
            $cart = self::getStoreCart($cartId);
            if ($view == 'gridbox') {
                self::prepareCartForEditor($cart);
            }
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/checkout-order.php';
            pq($value)->find('.ba-checkout-order-form-wrapper')->html($out);
        }
        foreach (pq('.ba-item-checkout-form') as $value) {
            $html = self::getCustomerInfoHTML($page->id, $cart);
            pq($value)->find('.ba-checkout-form-wrapper')->html($html);
            if (!empty(gridboxHelper::$store->checkout->recaptcha)) {
                self::setCaptcha(gridboxHelper::$store->checkout->recaptcha);
            }
        }
        foreach (pq('.ba-item-breadcrumbs') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $html = self::getBreadCrumbList($view);
            pq($value)->find('.ba-breadcrumbs-wrapper')->html($html);
        }
        foreach (pq('.ba-item-language-switcher') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $html = self::getLanguageSwithcer($view);
            pq($value)->find('.ba-language-switcher-active')->html($html->active);
            pq($value)->find('.ba-language-switcher-list .ba-language-switcher-item')->remove();
            pq($value)->find('.ba-language-switcher-list')->append($html->list);
        }
        foreach (pq('.ba-item-currency-switcher') as $value) {
            $itemId = pq($value)->attr('id');
            self::$editItem = $obj->{$itemId};
            $html = self::getCurrencySwithcer($view);
            pq($value)->find('.ba-currency-switcher-active')->html($html->active);
            pq($value)->find('.ba-currency-switcher-list .ba-currency-switcher-item')->remove();
            pq($value)->find('.ba-currency-switcher-list')->append($html->list);
        }
        foreach (pq('img') as $img) {
            $width = pq($img)->attr('width');
            if (!$width) {
                pq($img)->attr('width', 100)->attr('height', 100);
            }
        }
        $str = $dom->htmlOuter();
        
        return $str;
    }

    public static function getStoreSystemUrl($type)
    {
        $app = JFactory::getApplication();
        $system = self::getSystemParamsByType($type);
        if (!$system) {
            return '';
        }
        $url = self::getGridboxSystemLinks($system->id);
        $router = $app::getRouter();
        $uri = $router->build($url);
        $uri2 = JUri::getInstance();
        $host_port = [$uri2->getHost(), $uri2->getPort()];
        $scheme = ['path', 'query', 'fragment'];
        $uri->setScheme($app->isSSLConnection() ? 'https' : 'http');
        $uri->setHost($host_port[0]);
        $uri->setPort($host_port[1]);
        $scheme = array_merge($scheme, ['host', 'port', 'scheme']);
        $url = $uri->toString($scheme);
        $url = preg_replace('/\s/u', '%20', $url);
        $url = htmlspecialchars($url, ENT_COMPAT, 'UTF-8');

        return $url;
    }

    public static function getDefaultMenuItem()
    {
        $id = 0;
        $menus = JFactory::getApplication()->getMenu('site');
        $menu = $menus->getDefault();
        if ($menu && $menu->component == 'com_gridbox') {
            $id = $menu->id;
        }

        return $id;
    }

    public static function getPublishedPromoCode()
    {
        $db = JFactory::getDBO();
        $query = self::getPromoCodeQuery()
            ->select('COUNT(p.id)');
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }

    public static function prepareCartForEditor($cart)
    {
        $currency = self::$store->currency;
        $product = new stdClass();
        $product->id = 0;
        $product->title = 'Product';
        $product->intro_image = 'components/com_gridbox/assets/images/thumb-square.png';
        $product->quantity = $product->min = 1;
        $product->images = array();
        $product->data = new stdClass();
        $product->data->price = 36.99;
        $product->data->stock = 1;
        $product->data->sale_price = '';
        $product->data->single =  new stdClass();
        $product->data->single->price = $productData->price;
        $product->data->single->sale_price = $productData->sale_price;
        $product->prices = new stdClass();
        $product->prices->sale_price = '';
        $product->prices->regular = self::preparePrice(36.99, $currency->thousand, $currency->separator, $currency->decimals);
        $product->variations = array();
        $product->link = JUri::root();
        $product->extra_options = new stdClass();
        $product->extra_options->items = new stdClass();
        $product->extra_options->count = 0;
        $product->attachments = [];
        $cart->products = array($product);
        $cart->total = $cart->subtotal = $product->data->price;
        $cart->discount = 0;
        $cart->validPromo = false;
        $cart->quantity = 1;
        if (!empty($cart->tax)) {
            $cart->taxes = new stdClass();
            $cart->taxes->count = 0;
        }
    }

    public static function getStoreCheckoutProductsHTML($cart)
    {
        $html = '';
        $currency = self::$store->currency;
        $uploader = self::getUploaderHelper();
        foreach ($cart->products as $product) {
            $image = !empty($product->images) ? $product->images[0] : $product->intro_image;
            if (!empty($image) && !self::isExternal($image)) {
                $image = JUri::root().$image;
            }
            $price = $product->prices->sale_price !== '' ? $product->prices->sale : $product->prices->regular;
            $info = [];
            foreach ($product->variations as $variation) {
                $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
            }
            $infoStr = implode('/', $info);
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/order-products-row.php';
            $html .= $out;
        }
        $response = (object)[
            'html' => $html
        ];

        return $response;
    }

    public static function getStorePaymentsHTML($cart)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_payment_methods')
            ->where('published = 1')
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $html = '';
        $query = $db->getQuery(true)
            ->select('payment_id')
            ->from('#__gridbox_store_orders_payment')
            ->where('cart_id = '.$cart->id);
        $db->setQuery($query);
        $payment_id = $db->loadResult();
        $count = count($items);
        foreach ($items as $item) {
            $item->default = $item->id == $payment_id;
            $settings = json_decode($item->settings);
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/order-payment-row.php';
            $html .= $out;
        }

        return $html;
    }

    public static function getStoreShippingTax($cart, $country = true, $region = true)
    {
        $obj = null;
        foreach (self::$store->tax->rates as $key => $rate) {
            $count = $country ? $rate->country_id == $cart->country : true;
            $reg = $region ? self::getTaxRegion($rate->regions, $cart->region) : true;
            if ($rate->shipping && $count && $reg) {
                $obj = new stdClass();
                $obj->key = $key;
                $obj->title = $rate->title;
                $obj->rate = $rate->rate;
                $obj->amount = $rate->rate / 100;
                break;
            }
        }
        if (!$obj && $country && $region) {
            $obj = self::getStoreShippingTax($cart, true, false);
        } else if (!$obj && $country && !$region) {
            $obj = self::getStoreShippingTax($cart, false, false);
        }

        return $obj;
    }

    public static function getStoreShippingItem($item, $total, $tax, $cart)
    {
        $mode = self::$store->tax->mode;
        $item->params = json_decode($item->options);
        $type = $item->params->type;
        $object = isset($item->params->{$type}) ? $item->params->{$type} : null;
        if ($type == 'free' || $type == 'pickup') {
            $item->price = 0;
        } else if ($type == 'flat') {
            $item->price = $object->price;
        } else if ($type == 'weight-unit') {
            $weight = 0;
            foreach ($cart->products as $product) {
                if (!empty($product->data->weight)) {
                    $weight += $product->data->weight * $product->quantity;
                } else if (!empty($product->dimensions->weight)) {
                    $weight += $product->dimensions->weight * $product->quantity;
                }
                foreach ($product->extra_options->items as $extra) {
                    foreach ($extra->values as $value) {
                        if (!empty($value->weight)) {
                            $weight += $value->weight * $product->quantity;
                        }
                    }
                }
            }
            $item->price = $weight * $object->price;
        } else if ($type == 'product') {
            $item->price = $cart->quantity * $item->params->product->price;
        } else if ($type == 'prices' || $type == 'weight') {
            $range = [];
            $unlimited = null;
            foreach ($object->range as $value) {
                if ($value->rate === '') {
                    $unlimited = $value;
                } else {
                    $value->rate *= 1;
                    $range[] = $value;
                }
            }
            usort($range, function($a, $b){
                if ($a->rate == $b->rate) {
                    return 0;
                }

                return ($a->rate < $b->rate) ? -1 : 1;
            });
            $price = null;
            if ($type == 'weight') {
                $netValue = 0;
                foreach ($cart->products as $product) {
                    if (!empty($product->data->weight)) {
                        $netValue += $product->data->weight * $product->quantity;
                    } else if (!empty($product->dimensions->weight)) {
                        $netValue += $product->dimensions->weight * $product->quantity;
                    }
                    foreach ($product->extra_options->items as $extra) {
                        foreach ($extra->values as $value) {
                            if (!empty($value->weight)) {
                                $netValue += $value->weight * $product->quantity;
                            }
                        }
                    }
                }
            } else {
                $netValue = $cart->total;
            }
            foreach ($range as $value) {
                if ($netValue <= $value->rate) {
                    $price = $value;
                    break;
                }
            }
            if ($price === null && $unlimited) {
                $price = $unlimited;
            }
            if ($price) {
                $item->price = $price->price;
            } else {
                $item->price = 0;
            }
        } else if ($type == 'category') {
            $item->price = 0;
            foreach ($cart->products as $product) {
                if (empty($product->product_id)) {
                    continue;
                }
                $categories = self::getCategoryId($product->product_id);
                $price = null;
                foreach ($item->params->category->range as $range) {
                    foreach ($range->rate as $id) {
                        if (in_array($id, $categories)) {
                            $price = $range->price;
                            break;
                        }
                    }
                    if ($price !== null) {
                        break;
                    }
                }
                if ($price !== null) {
                    $item->price += $price * $product->quantity;
                    continue;
                }
            }
        }
        if ($object && isset($object->enabled) && $object->enabled && $total > $object->free * 1) {
            $item->price = 0;
        }
        $amount = $tax ? $tax->amount : 0;
        $item->tax = $mode == 'excl' ? $item->price * $amount : $item->price - $item->price / ($amount + 1);
        $item->total = $total + $item->price + ($mode == 'excl' ? $item->tax : 0);

        return $item;
    }

    public static function getStoreShippingItems($cart)
    {
        $digital = true;
        foreach ($cart->products as $product) {
            if (!isset($product->data->product_type) ||
                ($product->data->product_type != 'digital' && $product->data->product_type != 'subscription'
                    && $product->data->product_type != 'booking')) {
                $digital = false;
                break;
            }
        }
        $shipping = [];
        if ($digital) {
            return $shipping;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_shipping')
            ->where('published = 1')
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $query = $db->getQuery(true)
            ->select('shipping_id')
            ->from('#__gridbox_store_orders_shipping')
            ->where('cart_id = '.$cart->id);
        $db->setQuery($query);
        $shipping_id = $db->loadResult();
        $total = $cart->total + (self::$store->tax->mode == 'excl' ? $cart->tax : 0);
        $mode = self::$store->tax->mode;
        $tax = self::getStoreShippingTax($cart);
        self::$storeHelper->checkShippingOptions($items);
        foreach ($items as $item) {
            $item->default = $item->id == $shipping_id;
            $item = self::getStoreShippingItem($item, $total, $tax, $cart);
            $available = true;
            $object = $item->params->regions->available;
            $countries = self::getTaxCountries(true);
            $vars = get_object_vars($object);
            $count = count($vars);
            if ($count != 0 && (empty($cart->country) || !isset($object->{$cart->country}) || !isset($countries->{$cart->country}))) {
                $available = false;
            } else if ($count != 0) {
                $regions = $object->{$cart->country};
                if (count($countries->{$cart->country}->states) != 0 &&
                    (empty($cart->region) || !isset($regions->{$cart->region}) || !$regions->{$cart->region})) {
                    $available = false;
                }
            }
            $object = $item->params->regions->restricted;
            $vars = get_object_vars($object);
            $count = count($vars);
            if ($count != 0 && !empty($cart->country) && isset($object->{$cart->country})
                && count($countries->{$cart->country}->states) == 0) {
                $available = false;
            } else if ($count != 0 && !empty($cart->country) && isset($object->{$cart->country})) {
                $regions = $object->{$cart->country};
                if (!empty($cart->region) && isset($regions->{$cart->region}) && $regions->{$cart->region}) {
                    $available = false;
                }
            }
            if (!$available) {
                continue;
            }
            if ($item->carrier != 0) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_api')
                    ->where('id = '.$item->carrier);
                $db->setQuery($query);
                $item->carrier_item = $db->loadObject();
            }
            if (isset($item->carrier_item) && $item->carrier_item->service == 'inpost') {
                $doc = JFactory::getDocument();
                $doc->addStyleSheet('https://geowidget.inpost.pl/inpost-geowidget.css');
                $attr = ['defer' => true];
                $doc->addScript('https://geowidget.inpost.pl/inpost-geowidget.js', [], $attr);
            }
            $shipping[] = $item;
        }

        return $shipping;
    }

    public static function getStoreShippingHTML($cart, $items)
    {
        $html = '';
        $currency = self::$store->currency;
        $count = count($items);
        $inpost = null;
        foreach ($items as $item) {
            $price = self::preparePrice($item->price, $currency->thousand, $currency->separator, $currency->decimals);
            $taxPrice = self::preparePrice($item->tax, $currency->thousand, $currency->separator, $currency->decimals);
            $total = self::preparePrice($item->total, $currency->thousand, $currency->separator, $currency->decimals);
            $totalTax = self::preparePrice($cart->tax + $item->tax, $currency->thousand, $currency->separator, $currency->decimals);
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/order-shipping-row.php';
            $html .= $out;
        }
        if ($inpost) {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/inpost-modal.php';
            $html .= $out;
        }

        return $html;
    }

    public static function checkProductCategory($id, $array)
    {
        $flag = in_array($id, $array);
        if (!$flag && !empty($id)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('parent')
                ->from('#__gridbox_categories')
                ->where('id = '.$id);
            $db->setQuery($query);
            $category = $db->loadResult();
            $flag = self::checkProductCategory($category, $array);
        }

        return $flag;
    }

    public static function prepareProductPrices($id, $price, $sale_price, $variation = '', $qty = 1)
    {
        $price = floatval($price);
        $currency = self::$store->currency;
        $sales = self::$storeHelper->sales;
        $prices = new stdClass();
        $prices->price = $price * $qty;
        $prices->regular = self::preparePrice($price * $qty, $currency->thousand, $currency->separator, $currency->decimals);
        $prices->sale = '';
        $prices->sale_price = $sale_price;
        $date = date('Y-m-d H:i:s');
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        foreach ($sales as $sale) {
            if ($sale_price !== '' || empty($sale->discount) || !in_array($sale->access, $groups)) {
                continue;
            }
            $applies = false;
            if ($sale->applies_to == '*') {
                $applies = true;
            } else if ($sale->applies_to == 'category') {
                $categories = self::getCategoryId($id);
                foreach ($sale->map as $value) {
                    $applies = in_array($value->item_id, $categories);
                    if ($applies) {
                        break;
                    }
                }
            } else if ($sale->applies_to == 'product') {
                foreach ($sale->map as $value) {
                    $applies = $value->item_id == $id && $value->variation == $variation;
                    if ($applies) {
                        break;
                    }
                }
            }
            if ($applies) {
                $sale_price = $price - ($sale->unit == '%' ? $price * ($sale->discount / 100) : $sale->discount);
            }
        }
        if ($sale_price !== '') {
            $sale_price = floatval($sale_price);
            $prices->sale_price = $sale_price * $qty;
            $prices->sale = self::preparePrice($sale_price * $qty, $currency->thousand, $currency->separator, $currency->decimals);
        }

        return $prices;
    }

    public static function getExtraOptionsHTML($id, $cart_id, $options)
    {
        $extra = new stdClass();
        $extra->required = false;
        $extra->price = 0;
        foreach ($options as $option_id => $option) {
            $required = $option->required * 1 == 1;
            if ($option->type == 'file') {
                $option->attachments = self::getProductAttachments($id, $cart_id, $option_id);
            }
            foreach ($option->items as $item) {
                if ($required && ($item->default || ($option->type == 'file' && !empty($option->attachments)))) {
                    $required = false;
                }
                if ($item->default && !empty($item->price)) {
                    $extra->price += $item->price * 1;
                } else if ($option->type == 'file' && !empty($option->attachments)) {
                    $extra->price += $item->price * ($option->file_options->charge ? count($option->attachments) : 1);
                }
            }
            if ($required) {
                $extra->required = $required;
            }
        }
        
        return $extra;
    }

    public static function getBookingAddToCartHTML($id, $input)
    {
        $btn = self::$editItem->{'button-label'};
        $data = self::$storeHelper->getProductData($id);
        $bookingOptions = $data->booking;
        $now = JDate::getInstance('now');
        $booking = self::getBooking();
        $settings = $booking->getSettings();
        $options = $data->extra_options;
        $cart_id = $input->cookie->get('gridbox_store_cart', 0, 'int');
        $extra = self::getExtraOptionsHTML($id, $cart_id, $options);
        $data->price += $extra->price;
        $guest = $bookingOptions->single->participants;
        if ($data->sale_price != '') {
            $data->sale_price += $extra->price;
        }
        if ($extra->required) {
            $btn = JText::_('SELECT_AN_OPTION');
        }
        $attributes = 'data-format="'.self::$dateFormat.'"';
        if ($settings->limitation->enable) {
            $formats = ['h' => 'hour', 'd' => 'day', 'm' => 'month'];
            $str = '+'.$settings->limitation->late->value.' '.$formats[$settings->limitation->late->format];
            $now = JDate::getInstance($str);
            $str = '+'.$settings->limitation->early->value.' '.$formats[$settings->limitation->early->format];
            $attributes .= ' data-early="'.JDate::getInstance($str)->format('Y-m-d').'"';
        }
        if ($bookingOptions->type == 'single' && $bookingOptions->single->time == 'yes'
            && $booking->isEnabledDays($bookingOptions->single)) {
            $times = $booking->getSingleSlots($bookingOptions, $now, $id);
            while (count($times) == 0) {
                $now = JDate::getInstance($now->format('Y-m-d H:m').' + 1 day');
                $times = $booking->getSingleSlots($bookingOptions, $now, $id);
            }
        } else if ($bookingOptions->type == 'single' && $booking->isEnabledDays()) {
            $now = $booking->getSingleDay($now, $id, $bookingOptions);
        } else if ($bookingOptions->type == 'multiple' && $booking->isEnabledDays()) {
            $min = !empty($bookingOptions->multiple->min) ? (int)$bookingOptions->multiple->min : 1;
            list($now, $end) = $booking->getMultipleDate($now, $id, $min);
            
            $delta = strtotime($end->format('Y-m-d')) - strtotime($now->format('Y-m-d'));
            $quantity = $delta / 60 / 60 / 24;
            $data->price *= $quantity;
            $data->sale_price = $data->sale_price != '' ? $data->sale_price * $quantity : '';
        }
        $prices = self::prepareProductPrices($id, $data->price, $data->sale_price);
        $now_date = $now->format('Y-m-d');
        
        $today = self::formatDate($now_date);
        $attributes2 =  $attributes;
        $attributes .= ' data-now="'.$now_date.'" data-value="'.$now_date.'" value="'.$today.'"';
        $isEditor = false;
        if ($booking->isGroupSession && $bookingOptions->single->time == 'yes') {
            $guests = $times[0]->guests;
        } else if ($booking->isGroupSession) {
            $guests = $booking->getGroupSessionGuest($now_date);
        } else {
            $guests = $bookingOptions->single->participants;
        }

        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/booking/add-to-cart.php';
            
        return $out;
    }

    public static function getEditorAddToCartHTML($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $btn = self::$editItem->{'button-label'};
        $active = [];
        $disabled = '';
        $type = $db->loadResult();
        $prices = self::prepareProductPrices(0, 47.77, 36.99);
        $sku = '00000001';
        $stock = 27;
        $min = 1;
        $variations = [];
        $bookingOptions = new stdClass();
        $options = [];
        $attachments = [];
        $isEditor = true;
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/'.($type == 'products' ? 'store' : 'booking').'/add-to-cart.php';
            
        return $out;
    }

    public static function getAddToCartHTML($id, $input)
    {
        $btn = self::$editItem->{'button-label'};
        $active = [];
        $disabled = '';
        $data = self::$storeHelper->getProductData($id);
        $min = !empty($data->min) ? $data->min : 1;
        $options = $data->extra_options;
        $variationsMap = self::$storeHelper->getProductVariationsMap($id);
        $variations = self::getProductVariations($data->variations, $variationsMap);
        $variationImages = new stdClass();
        foreach ($variationsMap as $variation) {
            $variationImages->{$variation->option_key} = json_decode($variation->images);
        }
        $enabledVariation = null;
        $images = [];
        $get = $input->get->getArray([]);
        if (!empty($get)) {
            foreach ($variations as $ind => $variation) {
                $flag = true;
                foreach ($variation->urls as $key => $url) {
                    $key = urldecode($key);
                    $url = urldecode($url);
                    if (!isset($get[$key]) || $get[$key] != $url) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    $data = $variation;
                    $enabledVariation = $variation;
                    $vars = explode('+', $ind);
                    foreach ($vars as $var) {
                        if (!empty($variationImages->{$var})) {
                            $images = $variationImages->{$var};
                        }
                    }
                }
            }
        }
        $variations = self::getProductVariationsHTML($variationsMap);
        if ($enabledVariation) {
            $active = explode('+', $enabledVariation->variation);
        }
        $cart_id = $input->cookie->get('gridbox_store_cart', 0, 'int');
        $extra = self::getExtraOptionsHTML($id, $cart_id, $options);
        $sku = $data->sku;
        $stock = $data->stock;
        if ($stock !== '' && $stock * 1 < $min) {
            $stock = JText::_('OUT_OF_STOCK');
        }
        $data->price += $extra->price;
        if ($data->sale_price != '') {
            $data->sale_price += $extra->price;
        }
        $variation = isset($data->variation) ? $data->variation : '';
        $prices = self::prepareProductPrices($id, $data->price, $data->sale_price, $variation, $min);
        if ($data->stock !== '' && $data->stock * 1 < $min) {
            $btn = JText::_('OUT_OF_STOCK');
            $disabled = ' disabled';
        } else if ((!empty($variationsMap) && !$enabledVariation) || $extra->required) {
            $btn = JText::_('SELECT_AN_OPTION');
        }
        if ($enabledVariation && !empty($images)) {
            $galleryImages = $slideshowImages = $slideshowDots = '';
            foreach ($images as $i => $image) {
                if (!self::isExternal($image)) {
                    $image = JUri::root().$image;
                }
                $galleryImages .= '<div class="ba-instagram-image" style="background-image: url('.$image.');">';
                $galleryImages .= '<img data-src="'.$image.'" alt="" class="" src="'.$image.'">';
                $galleryImages .= '<div class="ba-simple-gallery-image"></div></div>';
                $slideshowImages .= '<li class="item"><div class="ba-slideshow-img" style="background-image: url(';
                $slideshowImages .= $image.');" data-src="'.$image.'"></div></li>';
                $slideshowDots .= '--thumbnails-dots-image-'.$i.': url('.$image.');';
            }
            foreach (pq('.ba-item-product-gallery') as $gallery) {
                $original = [];
                foreach (pq($gallery)->find('.ba-instagram-image img') as $img) {
                    $image = pq($img)->attr('data-src');
                    if (!self::isExternal($image)) {
                        $image = JUri::root().$image;
                    }
                    $original[] = $image;
                }
                $str = json_encode($original);
                pq($gallery)->find('.instagram-wrapper')->attr('data-original', $str);
                pq($gallery)->find('.instagram-wrapper')->attr('data-variation', $enabledVariation->variation);
                pq($gallery)->find('.instagram-wrapper')->html($galleryImages);
            }
            foreach (pq('.ba-item-product-slideshow') as $slideshow) {
                $original = [];
                foreach (pq($slideshow)->find('li.item .ba-slideshow-img') as $img) {
                    $original[] = 'url('.pq($img)->attr('data-src').')';
                }
                $str = json_encode($original);
                pq($slideshow)->find('ul.ba-slideshow')->attr('data-original', $str);
                pq($slideshow)->find('ul.ba-slideshow')->attr('data-variation', $enabledVariation->variation);
                pq($slideshow)->find('.slideshow-content')->html($slideshowImages);
                pq($slideshow)->find('.ba-slideshow-dots')->attr('style', $slideshowDots);
            }
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/add-to-cart.php';
            
        return $out;
    }

    public static function getProductAttachments($id, $cart_id, $option_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from('#__gridbox_store_product_attachments AS a')
            ->leftJoin('#__gridbox_store_cart_attachments_map AS m ON a.attachment_id = m.id')
            ->where('m.page_id = '.$id)
            ->where('m.product_id = 0')
            ->where('m.option_id = '.$option_id)
            ->where('m.cart_id = '.$cart_id);
        $db->setQuery($query);
        $attachments = $db->loadObjectList();
        if (!empty($attachments)) {
            $uploader = self::getUploaderHelper();
        }
        foreach ($attachments as $attachment) {
            $ext = $uploader->getExt($attachment->filename);
            $attachment->isImage = $uploader->isImage($ext);
        }

        return $attachments;
    }

    public static function getUploaderHelper($dir = '')
    {
        if (!class_exists('uploaderHelper')) {
            include_once JPATH_ROOT.'/components/com_gridbox/helpers/uploader.php';
        }
        $uploader = new uploaderHelper();

        return $uploader;
    }

    public static function getProductVariationsHTML($variations_map)
    {
        $variations = new stdClass();
        foreach ($variations_map as $variation) {
            if (!isset($variations->{$variation->field_id})) {
                $variations->{$variation->field_id} = new stdClass();
                $variations->{$variation->field_id}->title = $variation->title;
                $variations->{$variation->field_id}->type = $variation->field_type;
                $variations->{$variation->field_id}->items = array();
            }
            $variations->{$variation->field_id}->items[] = $variation;
        }
        foreach ($variations as $variation) {
            usort($variation->items, function($a, $b){
                return ($a->order_list < $b->order_list) ? -1 : 1;
            });
        }

        return $variations;
    }

    public static function getProductVariations($variations, $variationsMap = array())
    {
        $variationsURL = new stdClass();
        foreach ($variationsMap as $variation) {
            $variationsURL->{$variation->option_key} = new stdClass();
            $variationsURL->{$variation->option_key}->key = urlencode($variation->title);
            $variationsURL->{$variation->option_key}->value = urlencode($variation->value);
        }
        foreach ($variations as $key => $variation) {
            if (!empty($variationsMap)) {
                $vars = explode('+', $key);
                $urls = array();
                $variation->urls = array();
                $variation->variation = $key;
                foreach ($vars as $var) {
                    $urls[] = $variationsURL->{$var}->key.'='.$variationsURL->{$var}->value;
                    $variation->urls[$variationsURL->{$var}->key] = $variationsURL->{$var}->value;
                }
                $variation->url = implode('&', $urls);
            }
        }

        return $variations;
    }

    public static function getMapsPlacesPostsList($id)
    {
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', '', 'string');
        $category = 0;
        $app = $input->get('app', 0, 'int');
        if ($view == 'blog' && $app == $id) {
            $category = $input->get('id', 0, 'int');
        }
        $db = JFactory::getDbo();
        $query = self::getBlogPostsQuery($id, $category)
            ->select('DISTINCT p.id');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $array = array();
        foreach ($items as $item) {
            $array[] = $item->id;
        }
        $str = implode(', ', $array);

        return $str;
    }

    public static function prepareGridboxLinks($link)
    {
        if (strpos($link, 'option=com_gridbox')) {
            parse_str($link, $array);
            if (!isset($array['app']) && isset($array['blog'])) {
                $array['app'] = $array['blog'];
            }
            if ($array['view'] == 'page') {
                $type = !empty($array['app']) ? 'blog' : 'single';
                $app_id = !empty($array['app']) ? $array['app'] : 0;
                $category = !empty($array['category']) ? $array['category'] : 0;
                $link = self::getGridboxPageLinks($array['id'], $type, $app_id, $category);
            } else if ($array['view'] == 'blog' && !isset($array['layout'])) {
                $link = self::getGridboxCategoryLinks($array['id'], $array['app']);
            } else if ($array['view'] == 'blog' && $array['layout'] == 'tag') {
                $link = self::getGridboxTagLinks($array['tag'], $array['app']);
            }
            if (self::$isError) {
                $link = JRoute::_($link);
            }
        } else if (strpos($link, '[product ID=') !== false) {
            preg_match('/\d+/', $link, $matches);
            $id = $matches[0];
            $data = self::$storeHelper->getProductData($id);
            $default = false;
            $var = '';
            if (isset($data->id)) {
                foreach ($data->variations as $key => $variation) {
                    $var = $key;
                    if (isset($variation->default) && $variation->default) {
                        $default = true;
                        break;
                    }
                }
            }
            if (!empty($var) && !$default) {
                $link = self::getGridboxPageLinks($id, 'products', $data->app_id, $data->page_category);
            } else {
                $link = '#';
            }
        }

        return $link;
    }

    public static function getWeatherLanguage()
    {
        $lang = array('wind' => JText::_('WIND'), 'humidity' => JText::_('HUMIDITY'), 'pressure' => JText::_('PRESSURE'),
            'hpa' => JText::_('HPA'), 'Mon' => JText::_('WEATHER_MONDAY'),
            'Tue' => JText::_('WEATHER_TUESDAY'), 'Wed' => JText::_('WEATHER_WEDNESDAY'),
            'Thu' => JText::_('WEATHER_THURSDAY'),
            'Fri' => JText::_('WEATHER_FRIDAY'), 'Sat' => JText::_('WEATHER_SATURDAY'), 'Sun' => JText::_('WEATHER_SUNDAY'),
            '0' => JText::_('WEATHER_JANUARY'), '1' => JText::_('WEATHER_FEBRUARY'), '2' => JText::_('WEATHER_MARCH'),
            '3' => JText::_('WEATHER_APRIL'), '4' => JText::_('WEATHER_MAY'), '5' => JText::_('WEATHER_JUNE'),
            '6' => JText::_('WEATHER_JULY'), '7' => JText::_('WEATHER_AUGUST'), '8' => JText::_('WEATHER_SEPTEMBER'),
            '9' => JText::_('WEATHER_OCTOBER'), '10' => JText::_('WEATHER_NOVEMBER'), '11' => JText::_('WEATHER_DECEMBER'),
            'mph' => JText::_('MPH'), 'ms' => JText::_('MS'));
        
        return $lang;
    }

    public static function getWeatherIcons()
    {
        $icons = array("01" => "wi wi-day-sunny", "02" => "wi wi-day-cloudy", "03" => "wi wi-cloud",
            "04" => "wi wi-cloudy", "09" => "wi wi-showers", "10" => "wi wi-sprinkle",
            "11" => "wi wi-thunderstorm", "13" => "wi wi-snow", "50" => "wi wi-fog");
        
        return $icons;
    }

    public static function getWeatherData($url, $id, $location)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('`#__gridbox_weather`')
            ->where('plugin_id = '.$db->quote($id));
        $db->setQuery($query);
        $obj = $db->loadObject();
        $now = strtotime('now');
        if (!$obj || $now - $obj->saved_time >= 3600 || $obj->location != $location) {
            $data = self::fetch($url);
            $weather = json_decode($data);
            if ($weather->cod == 200) {
                $forecast = self::renderWetherData($item->weather, $data);
                $data = json_encode($forecast);
                $object = new stdClass();
                $object->plugin_id = $id;
                $object->saved_time = $now;
                $object->data = $data;
                $object->location = $location;
                if ($obj) {
                    $object->id = $obj->id;
                    $db->updateObject('#__gridbox_weather', $object, 'id');
                } else {
                    $db->insertObject('#__gridbox_weather', $object);
                }

                return $data;
            } else {
                return false;
            }
        } else {
            return $obj->data;
        }
    }

    public static function renderWetherData($weather, $data)
    {
        $lang = self::getWeatherLanguage();
        $icons = self::getWeatherIcons();
        $obj = json_decode($data);
        preg_match('/[-\d]+/', $obj->list[0]->dt_txt, $matches);
        $date = explode('-', $matches[0]);
        $now = date('Y-m-d', strtotime($obj->list[0]->dt_txt));
        $icon = preg_replace('/d|n/', '', $obj->list[0]->weather[0]->icon);
        $speed = $weather->unit == 'c' ? $lang['ms'] : $lang['mph'];
        $object = new stdClass();
        $object->weather = new stdClass();
        $object->weather->city = $weather->location;
        $object->weather->date = ($date[2] * 1).' '.$lang[$date[1] * 1 - 1].' '.$date[0];
        $object->weather->temp = round($obj->list[0]->main->temp);
        $object->weather->icon = $icons[$icon];
        $object->weather->wind = $lang['wind'].': '.$obj->list[0]->wind->speed.' '.($speed);
        $object->weather->humidity = $lang['humidity'].': '.$obj->list[0]->main->humidity.'%';
        $object->weather->pressure = $lang['pressure'].': '.$obj->list[0]->main->pressure.' '.$lang['hpa'];
        $object->forecast = array();
        $array = new stdClass();
        foreach ($obj->list as $list) {
            $listDate = date('Y-m-d', strtotime($list->dt_txt));
            if ($now == $listDate) {
                continue;
            }
            if (!isset($array->{$listDate})) {
                $array->{$listDate} = array();
            }
            $time = explode(' ', $list->dt_txt);
            if ($time[1] <= '12:00:00') {
                $array->{$listDate}[] = $list;
            }
        }
        foreach ($array as $key => $value) {
            $i = count($object->forecast);
            $day = date('D', strtotime($key));
            $dayObj = $value[count($value) - 1];
            $icon = preg_replace('/d|n/', '', $dayObj->weather[0]->icon);
            $object->forecast[$i] = new stdClass();
            $object->forecast[$i]->day = $lang[$day];
            $object->forecast[$i]->nightTemp = round($value[0]->main->temp);
            $object->forecast[$i]->dayTemp = round($dayObj->main->temp);
            $object->forecast[$i]->icon = $icons[$icon];
        }

        return $object;
    }

    public static function renderWetherHTML($weather, $item)
    {
        $name = isset($item->weather->name) && !empty($item->weather->name) ? $item->weather->name : $item->weather->location;
        include(JPATH_COMPONENT.'/views/layout/weather-today.php');
        $str = $out.'<div>';
        foreach ($weather->forecast as $forecast) {
            include(JPATH_COMPONENT.'/views/layout/weather-forecast.php');
            $str .= $out;
        }
        $str .= '</div>';

        return $str;
    }

    public static function getWeather($item, $id, $openWeatherMapKey)
    {
        $units = $item->weather->unit == 'c' ? 'metric' : 'imperial';
        $latLon = explode(',', $item->weather->location);
        if (!empty($latLon) && count($latLon) == 2 && is_numeric($latLon[0])&& is_numeric($latLon[1])) {
            $url = 'http://api.openweathermap.org/data/2.5/forecast?lat='.trim($latLon[0]).'&lon='.trim($latLon[1]);
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        } else if (is_numeric($item->weather->location)) {
            $url = 'http://api.openweathermap.org/data/2.5/forecast?id='.$item->weather->location;
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        } else {
            $location = str_replace(' ', '%20', $item->weather->location);
            $url = 'http://api.openweathermap.org/data/2.5/forecast?q='.$location;
            $url .= '&units='.$units.'&appid='.$openWeatherMapKey;
        }
        $data = self::getWeatherData($url, $id, $item->weather->location);
        if (!$data) {
            return $data;
        }
        $weather = json_decode($data);
        $str = self::renderWetherHTML($weather, $item);

        return $str;
    }

    public static function fetch($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function setCaptcha($type)
    {
        if (!empty($type)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('element = '.$db->quote($type))
                ->where('folder = '.$db->quote('captcha'))
                ->where('enabled = 1')
                ->where('type = '.$db->quote('plugin'));
            $db->setQuery($query);
            $captcha = $db->loadResult();
        } else {
            $captcha = null;
        }
        $doc = JFactory::getDocument();
        if ($captcha) {
            $obj = new Registry();
            $obj->loadString($captcha);
            $object = new stdClass();
            $object->data = new stdClass();
            $object->public_key = $obj->get('public_key', '');
            $object->private_key = $obj->get('private_key', '');
            $object->type = $type;
            $object->theme = $obj->get('theme2', '');
            $object->size = $obj->get('size', '');
            $object->badge = $obj->get('badge', '');
            $data = json_encode($object);
            $attr = ['defer' => true, 'async' => true];
            $doc->addScript('https://www.google.com/recaptcha/api.js?onload=recaptchaCommentsOnload&render=explicit', [], $attr);
            $doc->addScriptDeclaration('var recaptchaObject = '.$data.';');
        } else {
            $doc->addScriptDeclaration('var recaptchaObject = null;');
        }

        return $captcha;
    }

    public static function getCategoryIntro()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $db = JFactory::getDbo();
        $id = $input->get('id', 0, 'int');
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        if (!empty($tag)) {
            $id = $tag;
        } else if (!empty($author)) {
            $id = $author;
        }
        if ($input->getCmd('view') == 'gridbox') {
            $obj = new stdClass();
            $obj->title = 'Category Title';
            $obj->description = 'Category Description';
            $obj->image = '';
        } else {
            $query = $db->getQuery(true)
                ->select('title, description, image');
            if (!empty($tag)) {
                $query->from('#__gridbox_tags');
            } else if (!empty($author)) {
                $query->select('avatar, author_social');
                $query->from('#__gridbox_authors');
            } else if ($id != 0) {
                $query->from('#__gridbox_categories');
            } else {
                $id = $input->get('app', 0, 'int');
                $query->from('#__gridbox_app');
            }
            $query->where('id = '.$id);
            $db->setQuery($query);
            $obj = $db->loadObject();
        }
        if (isset($obj->avatar) && empty($obj->avatar)) {
            $obj->avatar = 'components/com_gridbox/assets/images/default-user.png';
        }
        if (empty($obj->image)) {
            $obj->image = 'components/com_gridbox/assets/images/default-theme.png';
        }
        if (!self::isExternal($obj->image)) {
            $obj->image = JUri::root().$obj->image;
        }
        $image = '<div class="intro-post-image-wrapper"><div class="ba-overlay"></div><div class="intro-post-image"';
        $image .= ' style="background-image: url('.str_replace(' ', '%20', $obj->image).');"></div></div>';
        $title = $obj->title;
        if (isset($obj->avatar)) {
            if (!self::isExternal($obj->avatar)) {
                $obj->avatar = JUri::root().$obj->avatar;
            }
            $title = '<span class="ba-author-avatar" style="background-image: url('.$obj->avatar.')"></span>'.$title;
        }
        $object = new stdClass();
        $object->image = $image;
        $object->title = $title;
        $object->description = $obj->description ? $obj->description : '';
        if (isset($obj->author_social)) {
            $socialHTML = '';
            $socials = json_decode($obj->author_social);
            foreach ($socials as $key => $social) {
                if (!empty($social->link)) {
                    $socialHTML .= '<a target="_blank" href="'.$social->link.'" class="'.$social->icon.'"></a>';
                }
            }
            if (!empty($socialHTML)) {
                $socialHTML = '<div class="intro-category-author-social-wrapper">'.$socialHTML.'</div>';
                $object->social = $socialHTML;
            }
        }

        return $object;
    }

    public static function renderModules($body)
    {
        $app = JFactory::getApplication();
        $view = $app->input->getCmd('view', '');
        $plugin = $app->input->get('plugin', '', 'string');
        $regex = '/\[modules ID=+(.*?)\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        if ($matches) {
            $db = JFactory::getDBO();
            $date = JDate::getInstance()->format('Y-m-d H:i:s');
            $date = $db->quote($date);
            $null = $db->quote($db->getNullDate());
            $str = '(%1$s = '.$null.' OR %1$s IS NULL OR %1$s >= '.$date.')';
            $publish_down = sprintf($str, 'publish_down');
            $str = '(%1$s = '.$null.' OR %1$s IS NULL OR %1$s <= '.$date.')';
            $publish_up = sprintf($str, 'publish_up');
            $language = 'language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')';
            foreach ($matches as $index => $match) {
                $id = (int)$match[1];
                if ($id) {
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__modules')
                        ->where('client_id = 0')
                        ->where('published = 1')
                        ->where($publish_down)
                        ->where($publish_up)
                        ->where($language)
                        ->where('id = '.$id);
                    $query->order('ordering');
                    $db->setQuery($query);
                    $module = $db->loadObject();
                    $access = self::checkModuleAccess($module);
                    if ($access) {
                        $document = JFactory::getDocument();
                        $document->_type = 'html';
                        $renderer = $document->loadRenderer('module');
                        $html = $renderer->render($module);
                        if ($module->module == 'mod_custom' && empty($plugin)) {
                            $html = JHtml::_('content.prepare', $html);
                        }
                        if ($module->showtitle) {
                            $moduleParams = new Registry;
                            $moduleParams->loadString($module->params);
                            $headerTag = htmlspecialchars($moduleParams->get('header_tag', 'h3'), ENT_COMPAT, 'UTF-8');
                            $headerClass = htmlspecialchars($moduleParams->get('header_class', 'page-header'), ENT_COMPAT, 'UTF-8');
                            $html = '<'.$headerTag.' class="'.$headerClass.'">'.$module->title.'</'.$headerTag.'>'.$html;
                        }
                    } else {
                        $html = '';
                    }
                    if (!empty($html) || $view != 'gridbox') {
                        $body = str_replace($match[0], $html, $body);
                    }
                }
            }
        }
        
        return $body;
    }

    public static function checkModules($body, $items)
    {
        if (!$body) {
            return $body;
        }
        if (!is_object($items)) {
            $obj = json_decode($items);
        } else {
            $obj = $items;
        }
        $body = self::checkGlobalItem($body, $obj);
        $app = JFactory::getApplication();
        $view = $app->input->getCmd('view', '');
        $option = $app->input->getCmd('option', '');
        $body = self::checkMainMenu($body);
        if ($option != 'com_gridbox' || ($view != 'gridbox' && !empty($view))) {
            $body = self::clearDOM($body, $obj);
        }
        $body = self::checkDOM($body, $obj);
        $body = self::checkPostTags($body, $app->input->getCmd('id', 0));
        $body = self::renderModules($body);
        
        return $body;
    }

    public static function getStarRatings($id, $page)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_star_ratings')
            ->where('`plugin_id` = '.$db->quote($id))
            ->where('`option` = '.$db->quote($page->option))
            ->where('`view` = '.$db->quote($page->view))
            ->where('`page_id` = '.$db->quote($page->id));
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!isset($obj->rating)) {
            $obj = new stdClass();
            $obj->rating = '0.00';
            $obj->count = 0;
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/plugins/star-ratings-schema.php';
        $array = array($out, $obj->rating);

        return $array;
    }

    public static function getEmptyList()
    {
        $input = JFactory::getApplication()->input;
        $task = $input->get->get('task', 'gridbox', 'string');
        if (strpos($task, 'editor.') !== false) {
            $input->set('view', 'gridbox');
        }
        $view = $input->get('view', 'gridbox', 'string');
        $option = $input->getCmd('option', '');
        $html = '<div class="empty-list"><i class="zmdi zmdi-alert-polygon"></i><p>';
        $html .= JText::_('NO_ITEMS_HERE').'</p></div>';
        if ($option != 'com_gridbox' || ($view != 'gridbox' && !empty($view))) {
            $html = '';
        }

        return $html;
    }

    public static function getBlogPostsHeader($isStore, $id, $category, $order)
    {
        $app = JFactory::getApplication();
        $input = JFactory::getApplication()->input;
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        if (!empty($tag)) {
            $url = self::getGridboxTagLinks($tag, $id);
        } else if (!empty($author)) {
            $url = self::getGridboxAuthorLinks($author, $id);
        } else {
            $url = self::getGridboxCategoryLinks($category, $id);
        }
        $query = $input->get('query', '', 'raw');
        $search = $input->get('search', '', 'string');
        if (!empty($search)) {
            $url .= '&search='.$search;
        }
        if (!empty($query)) {
            $url .= '&query='.$query;
        }
        $url .= '&sort-by=';
        $url = JRoute::_($url);
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/blog-posts-header.php';

        return $header;
    }

    public static function getBlogPagination($id, $active, $limit, $category, $type = '')
    {
        $app = JFactory::getApplication();
        $input = JFactory::getApplication()->input;
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        if (!empty($tag)) {
            $url = self::getGridboxTagLinks($tag, $id);
        } else if (!empty($author)) {
            $url = self::getGridboxAuthorLinks($author, $id);
        } else {
            $url = self::getGridboxCategoryLinks($category, $id);
        }
        $queryStr = $input->get('query', '', 'raw');
        $order = $input->get('sort-by', '', 'raw');
        $search = $input->get('search', '', 'string');
        if (!empty($search)) {
            $url .= '&search='.$search;
        }
        if (!empty($queryStr)) {
            $url .= '&query='.$queryStr;
        }
        if (!empty($order)) {
            $url .= '&sort-by='.$order;
        }
        $active = $active * 1;
        $db = JFactory::getDbo();
        $query = self::getBlogPostsQuery($id, $category)
            ->select('COUNT(DISTINCT(p.id))');
        $db->setQuery($query);
        $count = $db->loadResult();
        if ($count == 0) {
            return '';
        }
        if ($limit == 0) {
            $limit = 1;
        }
        $pages = ceil($count / $limit);
        if ($pages == 1) {
            return '';
        }
        $start = 0;
        $max = $pages;
        if ($active > 2 && $pages > 4) {
            $start = $active - 2;
        }
        if ($pages > 4 && ($pages - $active) < 3) {
            $start = $pages - 5;
        }
        if ($pages > $active + 2) {
            $max = $active + 3;
            if ($pages > 3 && $active < 2) {
                $max = 4;
            }
            if ($pages > 4 && $active < 2) {
                $max = 5;
            }
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts-pagination.php';
        
        return $out;
    }

    public static function getBlogPostsChildCategories($id)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_categories')
            ->where('published = 1')
            ->where('parent = '.$db->quote($id))
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('access in ('.$groups.')')
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            $childs = self::getBlogPostsChildCategories($item->id);
            $items = array_merge($items, $childs);
        }

        return $items;
    }

    public static function getItemsFilterWheres($app_id, $object)
    {
        $db = JFactory::getDbo();
        $wheres = [];
        $types = [$db->quote('checkbox'), $db->quote('radio'), $db->quote('select'),
                    $db->quote('price'), $db->quote('date'), $db->quote('event-date')];
        foreach ($object as $key => $array) {
            if (empty($array)) {
                continue;
            }
            $sub = [];
            if ($key != 'rating') {
                $query = $db->getQuery(true)
                    ->select('type')
                    ->from('#__gridbox_app')
                    ->where('id = '.$app_id);
                $db->setQuery($query);
                $type = $db->loadResult();
                $fields = [];
                if ($type == 'products' || $type == 'booking') {
                    $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__gridbox_store_products_fields')
                        ->where('title = '.$db->quote($key));
                    $db->setQuery($query);
                    $field = $db->loadObject();
                } else {
                    $field = null;
                }
                if (($type == 'products' || $type == 'booking') && JText::_('PRICE') == $key) {
                    $field = new stdClass();
                    $field->product = true;
                    $field->field_type = 'price';
                    $fields[] = $field;
                } else if ($type == 'booking' && JText::_('SELECT_DATE') == $key) {
                    $field = new stdClass();
                    $field->product = true;
                    $field->field_type = 'date-picker';
                    $fields[] = $field;
                } else if ($type == 'booking' && JText::_('SELECT_DATES') == $key) {
                    $field = new stdClass();
                    $field->product = true;
                    $field->field_type = 'date-range-picker';
                    $fields[] = $field;
                } else if ($field) {
                    $field->product = true;
                    $field->field_type = 'tag';
                    $fields[] = $field;
                }
                $query = $db->getQuery(true)
                    ->select('id, field_type')
                    ->from('#__gridbox_fields')
                    ->where('app_id = '.$app_id)
                    ->where('field_type IN ('.implode(', ', $types).')')
                    ->where('label = '.$db->quote($key));
                $db->setQuery($query);
                $field = $db->loadObject();
                if ($field) {
                    $fields[] = $field;
                }
                foreach ($fields as $field) {
                    if (isset($field->product) && $field->field_type == 'price') {
                        $query = self::getFilterProductsQuery($app_id, $db);
                        $db->setQuery($query);
                        $list = $db->loadObjectList();
                        foreach ($list as $obj) {
                            $prices = self::prepareProductPrices($obj->product_id, $obj->price, $obj->sale_price);
                            $value = $prices->sale_price != '' ? $prices->sale_price : $prices->price;
                            if ($value * 1 >= $array[0] * 1 && $value * 1 <= $array[1] * 1 && !in_array($obj->product_id, $sub)) {
                                $sub[] = $obj->product_id;
                            }
                        }
                    } else if (isset($field->product) && $field->field_type == 'date-range-picker') {
                        $booking = self::getBooking();
                        $settings = $booking->getSettings();
                        $min = $max = '';
                        if ($settings->limitation->enable) {
                            $formats = ['h' => 'hour', 'd' => 'day', 'm' => 'month'];
                            $str = '+'.$settings->limitation->late->value.' '.$formats[$settings->limitation->late->format];
                            $min = JDate::getInstance($str)->format('Y-m-d');
                            $str = '+'.$settings->limitation->early->value.' '.$formats[$settings->limitation->early->format];
                            $max = JDate::getInstance($str)->format('Y-m-d');
                        }
                        if (!empty($min) && $min > $array[0]) {
                            continue;
                        }
                        if (!empty($max) && ($max < $array[0] || $max < $array[1])) {
                            continue;
                        }
                        $dates = [];
                        $dateObject = JDate::getInstance($array[0]);
                        while ($dateObject->format('Y-m-d') <= $array[1]) {
                            $dates[] = $dateObject->format('Y-m-d');
                            $dateObject->modify('+1 day');
                        }
                        $query = $db->getQuery(true)
                            ->select('p.id, d.booking')
                            ->from('#__gridbox_pages AS p')
                            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id')
                            ->where('p.app_id = ' . $app_id);
                        $db->setQuery($query);
                        $list = $db->loadObjectList();
                        foreach ($list as $product) {
                            $options = json_decode($product->booking);
                            if ($options->type != 'multiple') {
                                continue;
                            }
                            $booking->clearBlockedAppointments();
                            $isBlocked = empty($dates);
                            foreach ($dates as $date) {
                                $dateObject = JDate::getInstance($date);
                                $isBlocked = $booking->isBlockedDay($dateObject, $product->id, true);
                                if ($isBlocked) {
                                    break;
                                }
                            }
                            if ($isBlocked) {
                                continue;
                            }
                            $sub[] = $product->id;
                        }
                    } else if (isset($field->product) && $field->field_type == 'date-picker') {
                        $booking = self::getBooking();
                        $settings = $booking->getSettings();
                        $min = $max = '';
                        if ($settings->limitation->enable) {
                            $formats = ['h' => 'hour', 'd' => 'day', 'm' => 'month'];
                            $str = '+'.$settings->limitation->late->value.' '.$formats[$settings->limitation->late->format];
                            $min = JDate::getInstance($str)->format('Y-m-d');
                            $str = '+'.$settings->limitation->early->value.' '.$formats[$settings->limitation->early->format];
                            $max = JDate::getInstance($str)->format('Y-m-d');
                        }
                        if (!empty($min) && $min > $array[0]) {
                            continue;
                        }
                        if (!empty($max) && $max < $array[0]) {
                            continue;
                        }
                        $dateObject = JDate::getInstance($array[0]);
                        $query = $db->getQuery(true)
                            ->select('p.id, d.booking')
                            ->from('#__gridbox_pages AS p')
                            ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id')
                            ->where('p.app_id = ' . $app_id);
                        $db->setQuery($query);
                        $list = $db->loadObjectList();
                        foreach ($list as $product) {
                            $options = json_decode($product->booking);
                            if ($options->type == 'multiple') {
                                continue;
                            }
                            $booking->clearBlockedAppointments();
                            if ($options->single->time == 'yes') {
                                $times = $booking->getSingleSlots($options, $dateObject, $product->id);
                                $isBlocked = empty($times);
                            } else {
                                $isBlocked = $booking->isBlockedDay($dateObject, $product->id, false);
                            }
                            if ($isBlocked) {
                                continue;
                            }
                            $sub[] = $product->id;
                        }
                    } else if (isset($field->product)) {
                        foreach ($array as $value) {
                            $query = $db->getQuery(true)
                                ->select('DISTINCT vm.product_id')
                                ->from('#__gridbox_store_products_fields_data AS fd')
                                ->where('fd.value = '.$db->quote($value))
                                ->leftJoin('#__gridbox_store_product_variations_map AS vm ON vm.option_key = fd.option_key');
                            $db->setQuery($query);
                            $list = $db->loadObjectList();
                            foreach ($list as $obj) {
                                if (!empty($obj->product_id) && !in_array($obj->product_id, $sub)) {
                                    $sub[] = $obj->product_id;
                                }
                            }
                        }
                    } else if ($field->field_type == 'price') {
                        $query = $db->getQuery(true)
                            ->select('page_id, value')
                            ->from('#__gridbox_page_fields')
                            ->where('field_id = '.$field->id);
                        $db->setQuery($query);
                        $list = $db->loadObjectList();
                        foreach ($list as $obj) {
                            $value = !empty($obj->value) ? $obj->value : 0;
                            if ($value * 1 >= $array[0] * 1 && $value * 1 <= $array[1] * 1 && !in_array($obj->page_id, $sub)) {
                                $sub[] = $obj->page_id;
                            }
                        }
                    } else if ($field->field_type == 'date' || $field->field_type == 'event-date') {
                        $q0 = $db->quote($array[0]);
                        $q1 = $db->quote($array[1]);
                        $query = $db->getQuery(true)
                            ->select('page_id, value')
                            ->from('#__gridbox_page_fields')
                            ->where('field_id = '.$field->id)
                            ->where('value = '.$q0.' OR value = '.$q1.' OR (value > '.$q0.' AND value < '.$q1.')');
                        $db->setQuery($query);
                        $list = $db->loadObjectList();
                        foreach ($list as $obj) {
                            $sub[] = $obj->page_id;
                        }
                    } else {
                        foreach ($array as $value) {
                            if (!$field) {
                                continue;
                            }
                            $query = $db->getQuery(true)
                                ->select('fd.option_key, fd.value')
                                ->from('#__gridbox_fields_data AS fd')
                                ->where('fd.field_id = '.$field->id)
                                ->where('fd.value = '.$db->quote($value));
                            $db->setQuery($query);
                            $results = $db->loadObjectList();
                            $option_key = null;
                            foreach ($results as $result) {
                                $option_key = $result->option_key;
                                if ($result->value == $value) {
                                    break;
                                }
                            }
                            $query = $db->getQuery(true)
                                ->select('page_id')
                                ->from('#__gridbox_page_fields')
                                ->where('field_id = '.$field->id);
                            if ($field->field_type != 'checkbox') {
                                $query->where('value = '.$db->quote($option_key));
                            } else {
                                $query->where('value LIKE '.$db->quote('%'.$option_key.'%'));
                            }
                            $db->setQuery($query);
                            $list = $db->loadObjectList();
                            foreach ($list as $obj) {
                                if (!in_array($obj->page_id, $sub)) {
                                    $sub[] = $obj->page_id;
                                }
                            }
                        }
                    }
                }
            } else {
                $rating = implode(', ', $array);
                self::setCommentUser();
                self::setReviewsModerators();
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__gridbox_pages')
                    ->where('app_id = '.$app_id);
                $db->setQuery($query);
                $pages = $db->loadObjectList();
                foreach ($pages as $page) {
                    $reviews = self::getReviewsCount($page->id);
                    if ($reviews->count > 0) {
                        foreach ($array as $rating) {
                            if ($reviews->rating >= $rating && $reviews->rating < $rating * 1 + 1) {
                                $sub[] = $page->id;
                                break;
                            }
                        }
                    }
                }
            }
            if (!empty($sub)) {
                $str = implode(', ', $sub);
                $wheres[] = 'p.id in ('.$str.')';
            } else {
                $wheres[] = 'p.id in (0)';
            }
        }

        return $wheres;
    }

    public static function getItemsFilterCount($app, $object)
    {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $category = $input->get('id', 0, 'int');
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        $search = $input->get('search', '', 'string');
        $search = trim($search);
        if (!empty($search)) {
            $array = [];
            $searchWords = explode(' ', $search);
            $titles = [];
            $params = [];
            foreach ($searchWords as $word) {
                $title = '(p.title REGEXP '.$db->quote('^'.$word).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
                $param = '(p.params REGEXP '.$db->quote('^'.$word).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
                $text = mb_strtoupper($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $text = mb_strtolower($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $text = mb_ucfirst($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
                $titles[] = $title;
                $params[] = $param;
            }
            $array[] = '('.implode(' AND ', $titles).')';
            $array[] = '('.implode(' AND ', $params).')';
            self::getSearchFields($search, 'search');
            $subStr = self::$cacheData->search->fields;
            if (!empty($subStr)) {
                $array[] = 'p.id IN ('.$subStr.')';
            }
            $searchStr = implode(' OR ', $array);
        }
        $wheres = self::getItemsFilterWheres($app, $object);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('COUNT(p.id)')
            ->from('#__gridbox_pages AS p')
            ->where('p.app_id = '.$app);
        if ($category > 0 && empty($tag) && empty($author)) {
            $categories = self::getBlogPostsChildCategories($category);
            $catStr = (string)$category;
            foreach ($categories as $value) {
                $catStr .= ','.$value->id;
            }
            $query->where('p.page_category in ('.$catStr.')');
        }
        $query->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if (!empty($tag)) {
            $query->where('t.tag_id = '.$tag)
                ->leftJoin('`#__gridbox_tags_map` AS t ON p.id = t.page_id');
        } else if (!empty($author)) {
            $query->where('t.author_id = '.$author)
                ->leftJoin('`#__gridbox_authors_map` AS t ON p.id = t.page_id');
        }
        if (!empty($wheres)) {
            $query->where(implode(' AND ', $wheres));
        }
        if (!empty($search)) {
            $query->where('('.$searchStr.')');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count;
    }

    public static function getBlogPostsQuery($id, $category, $order = 'p.id ASC')
    {
        $input = JFactory::getApplication()->input;
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        $queryStr = $input->get('query', '', 'raw');
        $search = $input->get('search', '', 'string');
        $search = trim($search);
        $db = JFactory::getDbo();
        if (!empty($queryStr) && strpos($queryStr, '__')) {
            $array = explode('__', $queryStr);
            $object = new stdClass();
            $values = [];
            $keys = [];
            foreach ($array as $k => $v) {
                if ($k % 2 == 0) {
                    $keys[] = $v;
                } else {
                    $values[] = $v;
                }
            }
            foreach ($keys as $i => $key) {
                $object->{$key} = explode('--', $values[$i]);
            }
            $wheres = self::getItemsFilterWheres($id, $object);
        }
        if (!empty($search)) {
            $array = [];
            $searchWords = explode(' ', $search);
            $titles = [];
            $params = [];
            foreach ($searchWords as $word) {
                $title = '(p.title REGEXP '.$db->quote('^'.$word).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
                $param = '(p.params REGEXP '.$db->quote('^'.$word).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
                $text = mb_strtoupper($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $text = mb_strtolower($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
                $text = mb_ucfirst($word);
                $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
                $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
                $titles[] = $title;
                $params[] = $param;
            }
            $array[] = '('.implode(' AND ', $titles).')';
            $array[] = '('.implode(' AND ', $params).')';
            self::getSearchFields($search, 'search');
            $subStr = self::$cacheData->search->fields;
            if (!empty($subStr)) {
                $array[] = 'p.id IN ('.$subStr.')';
            }
            $searchStr = implode(' OR ', $array);
        }
        if ($order == 'newest') {
            $order = 'p.created DESC';
        } else if ($order == 'popular') {
            $order = 'p.hits DESC';
        } else if ($order == 'event-date') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_fields')
                ->where('field_type = '.$db->quote('event-date'))
                ->where('app_id = '.$id);
            $db->setQuery($query);
            $event_id = $db->loadResult();
            $pks = [];
            if ($event_id) {
                $query = $db->getQuery(true)
                    ->select('page_id, value')
                    ->from('#__gridbox_page_fields')
                    ->where('field_id = '.$event_id);
                $db->setQuery($query);
                $list = $db->loadObjectList();
                usort($list, function($a, $b){
                    return ($a->value < $b->value) ? -1 : 1;
                });
                foreach ($list as $obj) {
                    $pks[] = $obj->page_id;
                }
            }
            if (!empty($pks)) {
                $order = 'FIELD(p.id, '.implode(',', $pks).')';
            } else {
                $order = 'p.id ASC';
            }
        } else if ($order == 'price-low-high' || $order == 'price-high-low'
            || $order == 'highest-rated' || $order == 'most-reviewed') {
            if ($order == 'highest-rated' || $order == 'most-reviewed') {
                self::setCommentUser();
                self::setReviewsModerators();
            }
            $query = self::getFilterProductsQuery($id, $db);
            if (!empty($queryStr) && !empty($wheres)) {
                $query->where(implode(' AND ', $wheres));
            }
            if (!empty($search)) {
                $query->where('('.$searchStr.')');
            }
            if ($category > 0 && empty($tag) && empty($author)) {
                $categories = self::getBlogPostsChildCategories($category);
                $catStr = (string)$category;
                foreach ($categories as $value) {
                    $catStr .= ','.$value->id;
                }
                $query->leftJoin('#__gridbox_category_page_map AS pm ON p.id = pm.page_id')
                    ->where('(p.page_category in ('.$catStr.') OR pm.category_id IN ('.$catStr.'))');
            }
            $db->setQuery($query);
            $list = $db->loadObjectList();
            foreach ($list as $obj) {
                if ($order == 'price-low-high' || $order == 'price-high-low') {
                    $prices = self::prepareProductPrices($obj->product_id, $obj->price, $obj->sale_price);
                    $obj->price_value = $prices->sale_price != '' ? $prices->sale_price * 1 : $prices->price * 1;
                } else {
                    $obj->reviews = self::getReviewsCount($obj->product_id);
                }
            }
            if ($order == 'price-low-high') {
                usort($list, function($a, $b){
                    return ($a->price_value < $b->price_value) ? -1 : 1;
                });
            } else if ($order == 'price-high-low') {
                usort($list, function($a, $b){
                    return ($a->price_value < $b->price_value) ? 1 : -1;
                });
            } else if ($order == 'highest-rated') {
                usort($list, function($a, $b){
                    return ($a->reviews->rating < $b->reviews->rating) ? 1 : -1;
                });
            } else {
                usort($list, function($a, $b){
                    return ($a->reviews->count < $b->reviews->count) ? 1 : -1;
                });
            }
            $pks = [];
            foreach ($list as $obj) {
                $pks[] = $obj->product_id;
            }
            if (!empty($pks)) {
                $order = 'FIELD(p.id, '.implode(',', $pks).')';
            } else {
                $order = 'p.id ASC';
            }
        }
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $query = $db->getQuery(true)
            ->from('#__gridbox_pages AS p')
            ->where('p.app_id = '.$id);
        if ($category > 0 && empty($tag) && empty($author)) {
            $categories = self::getBlogPostsChildCategories($category);
            $catStr = (string)$category;
            foreach ($categories as $value) {
                $catStr .= ','.$value->id;
            }
            $query->leftJoin('#__gridbox_category_page_map AS pm ON p.id = pm.page_id')
                ->where('(p.page_category in ('.$catStr.') OR pm.category_id IN ('.$catStr.'))');
        }
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->order($order)
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id');
        if (!empty($tag)) {
            $query->where('t.tag_id = '.$tag)
                ->leftJoin('`#__gridbox_tags_map` AS t ON p.id = t.page_id');
        } else if (!empty($author)) {
            $query->where('t.author_id = '.$author)
                ->leftJoin('`#__gridbox_authors_map` AS t ON p.id = t.page_id');
        }
        if (!empty($queryStr) && !empty($wheres)) {
            $query->where(implode(' AND ', $wheres));
        }
        if (!empty($search)) {
            $query->where('('.$searchStr.')');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }

        return $query;
    }

    public static function getSubscriptionProducts()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('d.subscription')
            ->from('#__gridbox_store_product_data AS d')
            ->where('d.product_type = '.$db->quote('subscription'))
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $pages = [];
        foreach ($items as $item) {
            $subscription = !empty($item->subscription) ? json_decode($item->subscription) : new stdClass();
            $action = isset($subscription->action) ? $subscription->action : '';
            $remove = isset($subscription->remove) ? $subscription->remove : false;
            if (($action == 'products' || $action == 'full') && $remove) {
                foreach ($subscription->products as $product) {
                    $pages[] = $product;
                }
            }
        }
        
        return $pages;
    }

    public static function getBlogPosts($id, $max, $limit, $start, $category, $order, $pagination = '')
    {
        $start *= $limit;
        if (!empty($pagination)) {
            $limit = $start + $limit;
            $start = 0;
        }
        $list = self::getBlogPostsSortingList();
        if (isset($list[$order]) || $order == 'event-date' || $order == 'title ASC' || $order == 'title DESC') {
            $dir = '';
        } else if ($order == 'order_list') {
            $dir = ' ASC';
            if ($category == 0) {
                $order = 'root_order_list';
            }
        } else {
            $dir = ' DESC';
        }
        if ($order == 'random') {
            $order = 'RAND()';
        } else if (!isset($list[$order]) && $order != 'event-date') {
            $order = 'p.'.$order;
        }
        $html = '';
        $db = JFactory::getDbo();
        $query = self::getBlogPostsQuery($id, $category, $order.$dir)
            ->select('DISTINCT p.id, p.title, p.intro_text, p.created, p.hits, p.intro_image, p.page_category,
                p.app_id, p.meta_title, c.title as category, a.title as blog, a.type');
        $db->setQuery($query, $start, $limit);
        $pages = $db->loadObjectList();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts.php';
        foreach ($pages as $key => $page) {
            $html .= self::getRecentPostsHTML($page, $out, $max);
        }

        return $html;
    }

    public static function checkGlobalItem($body, $items)
    {
        $regex = '/\[global item=+(.*?)\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        $db = JFactory::getDBO();
        self::$globalItems = [];
        foreach ($matches as $index => $match) {
            $query = $db->getQuery(true)
                ->select('item, id')
                ->from('#__gridbox_library')
                ->where('`global_item` = '.$db->quote($match[1]));
            $db->setQuery($query);
            $obj = $db->loadObject();
            $html = '';
            if ($obj) {
                self::$globalItems[] = $obj->id;
                $item = json_decode($obj->item);
                $html = $item->html;
                foreach ($item->items as $key => $value) {
                    $items->{$key} = $value;
                }
            }
            $body = str_replace('[global item='.$match[1].']', $html, $body);
        }
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $body = self::checkGlobalItem($body, $items);
        }

        return $body;
    }

    public static function getCategoryBreadcrumb($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title, id, parent, app_id')
            ->from('#__gridbox_categories')
            ->where('`id` = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByCategory($obj->app_id, $id);
        if (!empty($itemId)) {
            return array();
        } else {
            $url = self::getGridboxCategoryLinks($id, $obj->app_id);
        }
        $result = array(array('title' => $obj->title, 'link' => JRoute::_($url)));
        if ($obj->parent != 0) {
            $array = self::getCategoryBreadcrumb($obj->parent);
            $result = array_merge($result, $array);
        }

        return $result;
        
    }

    public static function getCategoryId($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('page_category')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $category = $db->loadResult();
        $array = [$category];
        $array2 = self::getCategoryIdPath($category);
        $result = array_merge($array, $array2);
        
        return $result;
    }

    public static function getCategoryIdPath($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('parent')
            ->from('#__gridbox_categories')
            ->where('`id` = '.$id * 1);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $array1 = [$obj->parent];
        if ($obj->parent != 0) {
            $array2 = self::getCategoryIdPath($obj->parent);
        } else {
            $array2 = [];
        }
        $result = array_merge($array1, $array2);
        
        return $result;
    }

    public static function getCategoryPath($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias, app_id, parent')
            ->from('#__gridbox_categories')
            ->where('`id` = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!$obj) {
            return array();
        }
        self::getGridboxMenuItems();
        $itemId = self::getGridboxMenuItemidByCategory($obj->app_id, $id);
        if (!empty($itemId)) {
            return array();
        }
        $result = array($obj->alias);
        if ($obj->parent != 0) {
            $array = self::getCategoryPath($obj->parent);
            $result = array_merge($result, $array);
        }
        
        return $result;
    }

    public static function getAuthorsHtml($authors, $className, $app_id)
    {
        $str = '';
        foreach ($authors as $author) {
            $url = self::getGridboxAuthorLinks($author->id, $app_id);
            if ($className == 'event-calendar-event-item-author') {
                $url = JRoute::_($url);
            }
            if (empty($author->avatar)) {
                $author->avatar = 'components/com_gridbox/assets/images/default-user.png';
            }
            if (!self::isExternal($author->avatar)) {
                $author->avatar= JUri::root().$author->avatar;
            }
            $str .= '<span class="'.$className.'"><a href="'.$url.'"><span class="ba-author-avatar"';
            $str .= ' style="background-image: url('.$author->avatar.')"></span>';
            $str .= $author->title.'</a></span>';
        }

        return $str;
    }

    public static function getBlogPostIntro()
    {
        $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $id = $input->get('id', 0, 'int');
        $edit_type = $input->get('edit_type', '', 'string');
        $pageView = $input->get('view', 'gridbox', 'string');
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            if ($page) {
                $id = $page->id;
            } else {
                $id = 0;
            }
        }
        if (!empty(self::$editItem) && $pageView != 'gridbox') {
            $desktop = self::$editItem->desktop;
        } else {
            $desktop = null;
        }
        $query = $db->getQuery(true)
            ->select('p.*')
            ->from('`#__gridbox_pages` as p')
            ->where('p.id = '.$id)
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->select('a.type as app_type')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->select('c.title AS category_title')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->order('p.id ASC');
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!$item) {
            $item = new stdClass();
            $item->id = 0;
            $item->intro_image = 'components/com_gridbox/assets/images/default-theme.png';
            $item->category_title = 'category';
            $item->created = date('Y-m-d');
            $item->hits = $item->page_category = 0;
            $item->title = JText::_('PAGE_TITLE');
            $item->app_id = $id;
        }
        $query = $db->getQuery(true)
            ->select('au.title, au.avatar, au.id')
            ->from('`#__gridbox_authors_map` AS au_m')
            ->where('au_m.page_id = '.$id)
            ->leftJoin('`#__gridbox_authors` AS au ON au.id = au_m.author_id')
            ->where('au.published = 1')
            ->order('au_m.id ASC');
        $db->setQuery($query);
        $item->authors = $db->loadObjectList();
        $url = self::getGridboxCategoryLinks($item->page_category, $item->app_id);
        $category = '<a href="'.JRoute::_($url).'">'.$item->category_title.'</a>';
        $date = self::formatDate($item->created);
        $views = $item->hits.' '.JText::_('VIEWS');
        $intro_image = self::prepareIntroImage($item->intro_image);
        if (empty($item->intro_image)) {
            $item->intro_image = 'components/com_gridbox/assets/images/default-theme.png';
        }
        if (!self::isExternal($item->intro_image)) {
            $item->intro_image = JUri::root().$item->intro_image;
        }
        $app = JFactory::getApplication();
        $view = $app->input->get('view', 'gridbox', 'string');
        $obj = new stdClass();
        if (!empty($intro_image) || $view == 'gridbox') {
            $obj->image = '<div class="intro-post-image-wrapper"><div class="ba-overlay"></div>';
            $obj->image .= '<div class="intro-post-image" style="background-image: url(';
            $obj->image .= str_replace(' ', '%20', $intro_image).');"></div></div>';
        } else {
            $obj->image = '';
        }
        $obj->title = $item->title;
        $author = self::getAuthorsHtml($item->authors, 'intro-post-author', $item->app_id);
        if ($item->id == 0) {
            $author = '<span class="intro-post-author"><a href="#"><span class="ba-author-avatar" ';
            $author .= 'style="background-image: url('.JUri::root().'components/com_gridbox/assets/images/default-user.png';
            $author .= ')"></span>admin</a></span>';
        }
        $comments = self::getCommentsCount($item->id);
        if ($comments == 0) {
            $commentsStr = JText::_('LEAVE_COMMENT');
        } else {
            $commentsStr = $comments.' '.JText::_('COMMENTS');
        }
        $reviews = self::getReviewsCount($item->id);
        if ($reviews->count == 0) {
            $reviewsStr = JText::_('LEAVE_REVIEW');
        } else {
            $reviewsStr = $reviews->count.' '.JText::_('REVIEWS');
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/intro-post-content.php';
        $obj->info = $out;

        return $obj;
    }

    public static function getPostAuthor($id)
    {
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $pageView = $input->get('view', 'gridbox', 'string');
        $edit_type = $input->get('edit_type', '', 'string');
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            if ($page) {
                $id = $page->id;
            } else {
                $id = 0;
            }
        }
        if (!empty(self::$editItem) && $pageView != 'gridbox') {
            $desktop = self::$editItem->desktop;
        } else {
            $desktop = null;
        }
        $tag = !empty(self::$editItem) ? self::$editItem->tag : 'h3';
        $query = $db->getQuery(true)
            ->select('DISTINCT p.app_id, a.title, a.id, a.avatar, a.description, a.author_social')
            ->from('#__gridbox_pages AS p')
            ->where('p.id = '.$id)
            ->leftJoin('`#__gridbox_authors_map` AS m ON m.page_id = p.id')
            ->leftJoin('`#__gridbox_authors` AS a ON m.author_id = a.id')
            ->where('a.published = 1');
        $db->setQuery($query);
        $authors = $db->loadObjectList();
        $html = '';
        foreach ($authors as $author) {
            $url = self::getGridboxAuthorLinks($author->id, $author->app_id);
            if (empty($author->avatar)) {
                $author->avatar = 'components/com_gridbox/assets/images/default-user.png';
            }
            $html .= '<div class="ba-post-author">';
            if (($desktop && $desktop->view->image) || !$desktop) {
                $html .= '<div class="ba-post-author-image"><div class="ba-overlay"></div><a href="';
                $html .= $url.'" style="background-image: url('.JUri::root().$author->avatar.');"></a></div>';
            }
            $html .= '<div class="ba-post-author-content"><a href="'.$url.'"></a>';
            if (($desktop && $desktop->view->title) || !$desktop) {
                $html .= '<div class="ba-post-author-title-wrapper"><'.$tag.' class="ba-post-author-title"><a href="';
                $html .= $url.'">'.$author->title.'</a></'.$tag.'></div>';
            }
            if (($desktop && $desktop->view->intro) || !$desktop) {
                $html .= '<div class="ba-post-author-description">'.$author->description.'</div>';
            }
            $socials = json_decode($author->author_social);
            $socialHTML = '';
            foreach ($socials as $key => $social) {
                if (!empty($social->link)) {
                    $icon = str_replace('zmdi ', 'ba-icons ', $social->icon);
                    $icon = str_replace('zmdi-', 'ba-icon-', $icon);
                    $socialHTML .= '<a target="_blank" href="'.$social->link.'" class="'.$icon.'"></a>';
                }
            }
            if (!empty($socialHTML)) {
                $html .= '<div class="ba-post-author-social-wrapper">'.$socialHTML.'</div>';
            }
            $html .= '</div></div>';
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getPostTagsData($id)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $input = JFactory::getApplication()->input;
        $edit_type = $input->get('edit_type', '', 'string');
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            if ($page) {
                $id = $page->id;
            } else {
                $id = 0;
            }
        }
        $query = $db->getQuery(true)
            ->select('m.tag_id as id')
            ->from('#__gridbox_tags_map AS m')
            ->where('m.page_id = '.$id)
            ->select('t.title')
            ->leftJoin('`#__gridbox_tags` AS t ON m.tag_id = t.id')
            ->order('t.hits desc')
            ->where('t.published = 1')
            ->where('t.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('t.access in ('.$groups.')')
            ->select('p.app_id, p.page_category')
            ->leftJoin('`#__gridbox_pages` AS p ON m.page_id = p.id');
        $db->setQuery($query);
        $tags = $db->loadObjectList();

        return $tags;
    }

    public static function getPostTags($id)
    {
        $tags = self::getPostTagsData($id);
        $html = '';
        foreach ($tags as $tag) {
            $url = self::getGridboxTagLinks($tag->id, $tag->app_id);
            $html .= '<a href="'.JRoute::_($url).'" class="ba-btn-transition fields-post-tags"><span>';
            $html .= $tag->title.'</span></a>';
        }

        return $html;
    }

    public static function checkPostTags($body, $id)
    {
        $regex = '/\[blog_post_tags\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $key => $value) {
            $str = self::getPostTags($id);
            $body = @preg_replace("|\[blog_post_tags\]|", $str, $body, 1);
        }

        return $body;
    }

    public static function getAppId($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $app = $db->loadResult();
        if (empty($app)) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_app')
                ->where('type <> '.$db->quote('single'))
                ->where('type <> '.$db->quote('system_apps'))
                ->order('id desc');
            $db->setQuery($query);
            $app = $db->loadResult();
        }

        return $app;
    }

    public static function getCustomerInfo($id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_customer_info')
            ->order('order_list ASC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        foreach ($data as $info) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_customer_info_data')
                ->where('page_id = '.$id)
                ->where('type = '.$db->quote($info->type))
                ->where('field_id = '.$info->id);
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result) {
                $info->title = $result->title;
                $info->options = $result->options;
            }
            $info->settings = json_decode($info->options);
        }

        return $data;
    }

    public static function getCustomerInfoHTML($id, $cart = null, $account = false)
    {
        $info = self::getCustomerInfo($id);
        $html  = '';
        $db = JFactory::getDbo();
        $user_id = JFactory::getUser()->id;
        foreach ($info as $obj) {
            if ($account && $obj->type == 'acceptance') {
                continue;
            }
            if (!empty($user_id)) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_user_info')
                    ->where('user_id = '.$user_id)
                    ->where('customer_id = '.$obj->id);
                $db->setQuery($query);
                $customer = $db->loadObject();
            } /*else if ($cart) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_order_customer_info')
                    ->where('cart_id = '.$cart->id)
                    ->where('customer_id = '.$obj->id);
                $db->setQuery($query);
                $customer = $db->loadObject();
            } */else {
                $customer = null;
            }
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/checkout-form-fields.php';
            $html .= $out;
        }

        return $html;
    }

    public static function getBlogTags($id, $category = '', $limit = 0, $sorting = 'hits')
    {
        $html = '';
        if (!empty($id)) {
            $db = JFactory::getDbo();
            $user = JFactory::getUser();
            $groups = $user->getAuthorisedViewLevels();
            $groups = implode(',', $groups);
            $query = $db->getQuery(true)
                ->select('DISTINCT t.title, t.id')
                ->from('`#__gridbox_tags` AS t')
                ->where('t.published = 1')
                ->where('t.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('t.access in ('.$groups.')')
                ->leftJoin('`#__gridbox_tags_map` AS m ON m.tag_id = t.id')
                ->leftJoin('`#__gridbox_pages` AS p ON m.page_id = p.id')
                ->where('p.app_id = '.$id)
                ->where('p.page_category <> '.$db->quote('trashed'));
            if ($sorting == 'hits') {
                $query->order('t.'.$sorting.' desc');
            } else if ($sorting == 'id' || $sorting == 'title') {
                $query->order('t.'.$sorting.' asc');
            } else if ($sorting == 'random') {
                $query->order('RAND() desc');
            } else if ($sorting == 'title ASC' || $sorting == 'title DESC') {
                $query->order('t.'.$sorting);
            }
            if (!empty($category)) {
                $query->where('p.page_category in ('.$category.')');
            }
            $db->setQuery($query, 0, $limit);
            $tags = $db->loadObjectList();
            foreach ($tags as $tag) {
                $url = self::getGridboxTagLinks($tag->id, $id);
                $html .= '<a href="'.JRoute::_($url).'" class="ba-btn-transition"><span>'.$tag->title.'</span></a>';
            }
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getBlogCategoriesCount($id, $digital, $counter = true)
    {
        if (!$counter) {
            return 0;
        }
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('COUNT(p.id)')
            ->from('#__gridbox_pages AS p')                    
            ->where('p.page_category = '.$id)
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')');
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $count = $db->loadResult();

        $query = $db->getQuery(true)
            ->select('COUNT(p.id)')
            ->from('#__gridbox_pages AS p')                    
            ->leftJoin('#__gridbox_category_page_map AS pm ON p.id = pm.page_id')
            ->where('pm.category_id = '.$id)
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')');
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $count += $db->loadResult();

        return $count;
    }

    public static function getBlogCategories($id, $parent = 0, $counter = true, $sub = true, $digital = [])
    {
        $key = 'categories-'.$id;
        $subkey = strval(intval($counter)).'-'.strval(intval($sub));
        if (isset(self::$cacheData->{$key}->{$subkey})) {
            return self::$cacheData->{$key}->{$subkey};
        }
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('id, title, app_id, image, description')
            ->from('#__gridbox_categories')
            ->where('published = 1')
            ->where('app_id = '.$db->quote($id))
            ->where('parent = '.$db->quote($parent))
            ->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('access in ('.$groups.')')
            ->order('order_list ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            $item->count = self::getBlogCategoriesCount($item->id, $digital, $counter);
            if ($sub) {
                $item->childs = self::getBlogCategories($id, $item->id, $counter, $sub, $digital);
            } else {
                $item->childs = [];
            }
            foreach ($item->childs as $child) {
                $item->count += $child->count;
            }
        }
        if ($parent == 0) {
            self::addCacheData($items, $key, $subkey);
        }

        return $items;
    }

    public static function getBlogCategoriesChilds($categories, $level = 0, $collapsible = false, $counter = true)
    {
        $html = '<div class="ba-app-sub-categories">';
        $input = JFactory::getApplication()->input;
        $option = $input->get('option', '', 'string');
        $view = $input->get('view', '', 'string');
        $app = $input->get('app', '', 'string');
        $id = $input->get('id', '', 'string');
        foreach ($categories as $category) {
            $url = self::getGridboxCategoryLinks($category->id, $category->app_id);
            $url = JRoute::_($url);
            $className = '';
            if ($option == 'com_gridbox' && $view == 'blog' && $app == $category->app_id && $id == $category->id) {
                $className .= 'active" data-active="true';
            }
            $html .= '<div class="ba-app-sub-category-wrapper';
            if (!empty($category->childs)) {
                $childs = self::getBlogCategoriesChilds($category->childs, $level + 1, $collapsible, $counter);
            }
            if (!empty($category->childs) && $collapsible && strpos($childs, 'data-active="true') === false) {
                $html .= ' ba-categories-collapsed ba-categories-icon-rotated" style="--categories-collapse-height: 0;';
            } else if (!empty($category->childs) && $collapsible) {
                $html .= '" style="--categories-collapse-height: auto;';
            }
            $html .= '"><span class="ba-app-sub-category';
            $html .= '" style="--sub-category-level: '.$level;
            $html .= '" data-level="'.$level.'"><a href="'.$url.'" class="'.$className.'"><span>'.$category->title;
            $html .= '</span>';
            if ($counter) {
                $html .= '<span class="ba-app-category-counter">('.$category->count.')</span>';
            }
            $html .= '</a>';
            if (!empty($category->childs)) {
                $html .= '<i class="ba-icons ba-icon-chevron-right collapse-categories-list"></i>';
            }
            $html .= '</span>';
            if (!empty($category->childs)) {
                $html .= '<div class="ba-app-sub-category-childs">';
                $html .= $childs;
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public static function getBlogCategoriesHtml($categories, $max = 75, $collapsible = false, $counter = true, $title = true, $img = true)
    {
        $html = '';
        $input = JFactory::getApplication()->input;
        $option = $input->get('option', '', 'string');
        $view = $input->get('view', '', 'string');
        $app = $input->get('app', '', 'string');
        $id = $input->get('id', '', 'string');
        foreach ($categories as $category) {
            $url = self::getGridboxCategoryLinks($category->id, $category->app_id);
            $url = JRoute::_($url);
            $className = '';
            if ($option == 'com_gridbox' && $view == 'blog' && $app == $category->app_id && $id == $category->id) {
                $className .= ' active';
            }
            $html .= '<div class="ba-blog-post'.$className.'" data-id="'.$category->id.'">';
            if (!empty($category->image) && $img) {
                $image = (!self::isExternal($category->image) ? JUri::root() : '').$category->image;
                $html .= '<div class="ba-blog-post-image"><img src="';
                $html .= str_replace(' ', '%20', $image).'" alt="'.$category->title;
                $html .= '"><div class="ba-overlay"></div><a href="'.$url.'" style="background-image: url(';
                $html .= str_replace(' ', '%20', $image).');"></a></div>';
            }
            $htmlTag = isset(self::$editItem->tag) ? self::$editItem->tag : 'h3';
            $html .= '<div class="ba-blog-post-content';
            if (!empty($category->childs)) {
                $childs = self::getBlogCategoriesChilds($category->childs, 0, $collapsible, $counter);
            }
            if (!empty($category->childs) && $collapsible && strpos($childs, 'data-active="true') === false) {
                $html .= ' ba-categories-collapsed ba-categories-icon-rotated" style="--categories-collapse-height: 0;';
            } else if (!empty($category->childs) && $collapsible) {
                $html .= '" style="--categories-collapse-height: auto;';
            }
            $html .= '"><a href="'.$url.'"></a>';
            if ($title) {
                $html .= '<div class="ba-blog-post-title-wrapper">';
                $html .= '<'.$htmlTag.' class="ba-blog-post-title"><a href="'.$url.'"><span>'.$category->title.'</span>';
                if ($counter) {
                    $html .= '<span class="ba-app-category-counter">('.$category->count.')</span>';
                }
                $html .= '</a>';
                if (!empty($category->childs)) {
                    $html .= '<i class="ba-icons ba-icon-chevron-right collapse-categories-list"></i>';
                }
                $html .= '</'.$htmlTag.'></div>';
            }
            if (!empty($category->childs)) {
                $html .= '<div class="ba-blog-post-info-wrapper">'.$childs.'</div>';
            }
            $html .= '</div></div>';
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getRecentComments($id, $sorting, $limit, $max, $category = '')
    {
        $order = 'c.date desc';
        if ($sorting == 'popular') {
            $order = 'c.likes desc';
        } else if ($sorting == 'random') {
            $order = 'RAND() desc';
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title, u.email AS user_email')
            ->from('#__gridbox_comments AS c')
            ->where('c.status = '.$db->quote('approved'))
            ->where('p.app_id = '.$id)
            ->leftJoin('`#__gridbox_pages` AS p ON '.$db->quoteName('p.id').' = '.$db->quoteName('c.page_id'))
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->order($order);
        if (!empty($category)) {
            $query->where('p.page_category in ('.$category.')');
        }
        $db->setQuery($query, 0, $limit);
        $comments = $db->loadObjectList();
        $html = '';
        foreach ($comments as $comment) {
            $url = JUri::root().'index.php/commentID-'.$comment->id;
            if (empty($comment->avatar)) {
                if ($comment->user_type == 'user' && !empty($comment->user_email)) {
                    $comment->email = $comment->user_email;
                }
                $author = self::getAuthor($comment->user_id);
                $comment->name = $author->title ?? $comment->name;
                $avatar = self::getUserAvatar($comment->email, 'enable_gravatar', $author);
            } else {
                $avatar = $comment->avatar;
            }
            $message = $comment->message;
            if ($message && mb_strlen($message) != 0 && $max != 0) {
                $text = mb_substr($message, 0, $max);
                if (mb_strlen($message) > $max) {
                    $text .= '...';
                }
                $introStr = '<div class="ba-blog-post-intro-wrapper">'.$text.'</div>';
            } else {
                $introStr = '';
            }
            $time = time() - strtotime($comment->date);
            $hour = 60 * 60;
            if ($time < 60) {
                $comment->date = '1 '.JText::_('MINUTES_AGO');
            } else if ($time < $hour) {
                $comment->date = floor($time / 60).' '.JText::_('MINUTES_AGO');
            } else if ($time < 86400) {
                $comment->date = floor($time / $hour).' '.JText::_('HOURS_AGO');
            } else {
                $comment->date = self::formatDate($comment->date);
            }
            $htmlTag = isset(self::$editItem->tag) ? self::$editItem->tag : 'h3';
            $titleStr = '<a href="'.$url.'"></a><div class="ba-blog-post-title-wrapper"><';
            $titleStr .= $htmlTag.' class="ba-blog-post-title">'.$comment->name.' '.JText::_('COMMENTS_ON').' <a href="'.$url;
            $titleStr .= '">'.$comment->title.'</a></'.$htmlTag.'></div>';
            $html .= '<div class="ba-blog-post"><div class="ba-blog-post-image"><img src="';
            $html .= str_replace(' ', '%20', $avatar).'" alt="'.$comment->name;
            $html .= '" onerror="this.src = JUri+\'components/com_gridbox/assets/images/default-user.png\'; ';
            $html .= 'this.parentNode.querySelector(\'a\').style.backgroundImage = \'url(\'+this.src+\')\';">';
            $html .= '<div class="ba-overlay"></div><a href="'.$url.'" style="background-image: url('.$avatar;
            $html .= ');"></a></div><div class="ba-blog-post-content">';
            $html .= $titleStr.'<div class="ba-blog-post-info-wrapper"><span class="ba-blog-post-date">'.$comment->date;
            $html .= '</span></div>'.$introStr.'</div></div>';
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getRecentReviews($id, $sorting, $limit, $max, $category = '')
    {
        $order = 'c.date desc';
        if ($sorting == 'popular') {
            $order = 'c.likes desc';
        } else if ($sorting == 'random') {
            $order = 'RAND() desc';
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title, u.email AS user_email')
            ->from('#__gridbox_reviews AS c')
            ->where('c.status = '.$db->quote('approved'))
            ->where('parent = 0')
            ->where('p.app_id = '.$id)
            ->leftJoin('`#__gridbox_pages` AS p ON '.$db->quoteName('p.id').' = '.$db->quoteName('c.page_id'))
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->order($order);
        if (!empty($category)) {
            $query->where('p.page_category in ('.$category.')');
        }
        $db->setQuery($query, 0, $limit);
        $reviews = $db->loadObjectList();
        $html = '';
        foreach ($reviews as $review) {
            $url = JUri::root().'index.php/reviewID-'.$review->id;
            if (empty($review->avatar)) {
                if ($review->user_type == 'user' && !empty($review->user_email)) {
                    $review->email = $review->user_email;
                }
                $author = self::getAuthor($review->user_id);
                $review->name = $author->title ?? $review->name;
                $avatar = self::getUserAvatar($review->email, 'reviews_enable_gravatar', $author);
            } else {
                $avatar = $review->avatar;
            }
            $message = $review->message;
            if ($message && mb_strlen($message) != 0 && $max != 0) {
                $text = mb_substr($message, 0, $max);
                if (mb_strlen($message) > $max) {
                    $text .= '...';
                }
                $introStr = '<div class="ba-blog-post-intro-wrapper">'.$text.'</div>';
            } else {
                $introStr = '';
            }
            $time = time() - strtotime($review->date);
            $hour = 60 * 60;
            if ($time < 60) {
                $review->date = '1 '.JText::_('MINUTES_AGO');
            } else if ($time < $hour) {
                $review->date = floor($time / 60).' '.JText::_('MINUTES_AGO');
            } else if ($time < 86400) {
                $review->date = floor($time / $hour).' '.JText::_('HOURS_AGO');
            } else {
                $review->date = self::formatDate($review->date);
            }
            $htmlTag = isset(self::$editItem->tag) ? self::$editItem->tag : 'h3';
            $titleStr = '<a href="'.$url.'"></a><div class="ba-blog-post-title-wrapper"><';
            $titleStr .= $htmlTag.' class="ba-blog-post-title"><span class="ba-reviews-name">'.$review->name;
            $titleStr .= '</span> <span class="ba-reviews-source">';
            $titleStr .= JText::_('COMMENTS_ON').'</span> <a class="ba-reviews-source" href="'.$url;
            $titleStr .= '">'.$review->title.'</a></'.$htmlTag.'></div>';
            $html .= '<div class="ba-blog-post"><div class="ba-blog-post-image"><img src="';
            $html .= str_replace(' ', '%20', $avatar).'" alt="'.$review->name;
            $html .= '" onerror="this.src = JUri+\'components/com_gridbox/assets/images/default-user.png\'; ';
            $html .= 'this.parentNode.querySelector(\'a\').style.backgroundImage = \'url(\'+this.src+\')\';">';
            $html .= '<div class="ba-overlay"></div><a href="'.$url.'" style="background-image: url('.$avatar;
            $html .= ');"></a></div><div class="ba-blog-post-content">';
            $html .= $titleStr.'<div class="ba-blog-post-info-wrapper"><span class="ba-blog-post-date">'.$review->date;
            $html .= '</span></div><div class="ba-review-stars-wrapper">';
            for ($i = 1; $i < 6; $i++) {
                $html .= '<i class="ba-icons ba-icon-star'.($i <= $review->rating ? ' active' : '');
                $html .= '" data-rating="'.$i.'" style="width:'.($i <= $review->rating ? 'auto' : '0').';"></i>';
            }
            $html .= '</div>'.$introStr.'</div></div>';
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getRecentPostsPagination($id, $limit, $category, $featured, $active, $type, $posts_type = '', $tags = '')
    {
        if (!$id || empty($type)) {
            return '';
        }
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('COUNT(DISTINCT(p.id))')
            ->from('#__gridbox_pages AS p')
            ->where('p.app_id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if ($featured) {
            $query->where('p.featured = 1');
        }
        if (empty($posts_type) && !empty($category)) {
            $query->leftJoin('#__gridbox_category_page_map AS pm ON p.id = pm.page_id')
                ->where('(p.page_category in ('.$category.') OR pm.category_id IN ('.$category.'))');
        } else if (!empty($posts_type) && !empty($tags)) {
            $query->where('t.tag_id IN ('.$tags.')')
                ->leftJoin('`#__gridbox_tags_map` AS t ON p.id = t.page_id');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $count = $db->loadResult();
        if ($count == 0) {
            return '';
        }
        if ($limit == 0) {
            $limit = 1;
        }
        $pages = ceil($count / $limit);
        if ($pages == 1) {
            return '';
        }
        $html = '';
        if ($active != $pages - 1) {
            $next = $active == $pages - 1 ? $pages : $active + 2;
            $html .= '<div class="ba-blog-posts-pagination"';
            if ($type == 'infinity' || ($type == 'load-more-infinity' && $active > 0)) {
                $html .= ' style="display:none !important"';
            }
            $html .= '><span><a href="'.JRoute::_('&page='.$next).'" class="ba-btn-transition">';
            $html .= JText::_('LOAD_MORE').'</a></span></div>';
        }

        return $html;
    }

    public static function getRecentPostsQuery($id, $category, $featured, $order, $type, $tags)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        $groups = implode(',', $levels);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $languages = $db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*');
        $query = $db->getQuery(true)
            ->from('#__gridbox_pages AS p')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$languages.')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$languages.')')
            ->where('c.access in ('.$groups.')');
        if ($order != 'top_selling') {
            $query->where('p.app_id = '.$id);
        }
        if ($featured && $order != 'top_selling') {
            $query->where('p.featured = 1');
        }
        if (!empty($category) && $order != 'top_selling' && empty($type)) {
            $query->leftJoin('#__gridbox_category_page_map AS pm ON p.id = pm.page_id')
                ->where('(p.page_category in ('.$category.') OR pm.category_id IN ('.$category.'))');
        } else if (!empty($type) && !empty($tags) && $order != 'top_selling') {
            $query->where('t.tag_id IN ('.$tags.')')
                ->leftJoin('`#__gridbox_tags_map` AS t ON p.id = t.page_id');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }

        return $query;
    }

    public static function getRecentPosts($id, $order, $limit, $max, $category = '', $featured = false, $start = 0, $not = '', $type = '', $tags = '')
    {
        if (!$id || is_object($id)) {
            return self::getEmptyList();
        }
        $db = JFactory::getDbo();
        $pks = [];
        $dir = '';
        if ($order == 'top_selling') {
            $query = self::getRecentPostsQuery($id, $category, $featured, $order, $type, $tags)
                ->select('DISTINCT op.product_id, COUNT(op.product_id) AS count')
                ->leftJoin('#__gridbox_store_order_products AS op ON op.product_id = p.id')
                ->where('o.published = 1')
                ->where('o.status = '.$db->quote('completed'))
                ->leftJoin('#__gridbox_store_orders AS o ON o.id = op.order_id')
                ->group('op.product_id')
                ->order('count DESC');
            if (!empty($not)) {
                $query->where('p.id NOT IN('.$not.')');
            }
            $db->setQuery($query);
            $products = $db->loadObjectList();
            foreach ($products as $product) {
                $pks[] = $product->product_id;
            }
            $length = count($pks);
            if ($length != 0 && ($length < $limit || !empty($not))) {
                $query = self::getRecentPostsQuery($id, $category, $featured, $order, $type, $tags)
                    ->select('DISTINCT p.id')
                    ->where('p.id NOT IN('.implode(', ', $pks).')')
                    ->where('a.type = '.$db->quote('products'))
                    ->order('p.id ASC');
                if (!empty($not)) {
                    $query->where('p.id NOT IN('.$not.')');
                }
                $l = !empty($not) ? 0 : $limit - count($pks);
                $db->setQuery($query, 0, $l);
                $products = $db->loadObjectList();
                foreach ($products as $product) {
                    $pks[] = $product->id;
                }
            }
            if ($length == 0) {
                $order = 'id';
            }
            $dir = ' ASC';
        } else if ($order == 'order_list') {
            $dir = ' ASC';
            if (empty($category)) {
                $order = 'root_order_list';
            }
        } else if ($order != 'title ASC' && $order != 'title DESC') {
            $dir = ' DESC';
        }
        if ($order == 'random') {
            $order = 'RAND()';
        } else if ($order == 'event-date') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_fields')
                ->where('field_type = '.$db->quote('event-date'))
                ->where('app_id = '.$id);
            $db->setQuery($query);
            $event_id = $db->loadResult();
            $ids = [];
            if ($event_id) {
                $query = $db->getQuery(true)
                    ->select('page_id, value')
                    ->from('#__gridbox_page_fields')
                    ->where('field_id = '.$event_id);
                $db->setQuery($query);
                $list = $db->loadObjectList();
                usort($list, function($a, $b){
                    return ($a->value < $b->value) ? -1 : 1;
                });
                foreach ($list as $obj) {
                    $ids[] = $obj->page_id;
                }
            }
            if (!empty($ids)) {
                $order = 'FIELD(p.id, '.implode(',', $ids).')';
                $dir = '';
            } else {
                $order = 'p.id';
                $dir = ' ASC';
            }
        } else if ($order != 'top_selling') {
            $order = 'p.'.$order;
        }
        if (!empty($not) && $order != 'top_selling') {
            $order = 'p.id';
        }
        $query = self::getRecentPostsQuery($id, $category, $featured, $order, $type, $tags)
            ->select('DISTINCT p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category, p.app_id,
                p.meta_title, c.title as category, a.type, p.hits');
        if ($order != 'top_selling') {
            $query->order($order.$dir);
        } else if (!empty($pks)) {
            $query->where('p.id IN ('.implode(', ', $pks).')')
                ->order('FIELD(p.id, '.implode(', ', $pks).')');
        }
        if (empty($not)) {
            $db->setQuery($query, $start, $limit);
            $pages = $db->loadObjectList();
        } else {
            $db->setQuery($query);
            $data = $db->loadObjectList();
            $notArray = explode(',', $not);
            $result = [];
            foreach ($data as $key => $value) {
                if (!in_array($value->id, $notArray)) {
                    $result[] = $value;
                }
            }
            if (count($result) <= $limit) {
                $pages = $result;
            } else {
                $keys = array_rand($result, $limit);
                $pages = [];
                foreach ($keys as $key) {
                    $pages[] = $result[$key];
                }
            }
        }
        $html = '';
        if (is_object(self::$editItem) && self::$editItem->type == 'recent-posts-slider') {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts-slider.php';
        } else {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts.php';
        }
        foreach ($pages as $key => $page) {
            $html .= self::getRecentPostsHTML($page, $out, $max);
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getLoginHTML($view)
    {
        $html = '';
        if ($view == 'gridbox' || self::$editItem->options->login) {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/login/login.php';
            $html .= $out;
        }
        if ($view == 'gridbox' || self::$editItem->options->registration) {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/login/registration.php';
            $html .= $out;
        }
        if ($view == 'gridbox' || (self::$editItem->options->login && self::$editItem->options->password)) {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/login/forgot-password.php';
            $html .= $out;
        }
        if ($view == 'gridbox' || (self::$editItem->options->login && self::$editItem->options->username)) {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/login/forgot-username.php';
            $html .= $out;
        }

        return $html;
    }

    public static function getRecentlyViewedProducts($limit, $max)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $edit_type = $input->get('edit_type', '', 'string');
        $id = $input->get('id', 0, 'int');
        $option = $app->input->getCmd('option', '');
        $view = $app->input->getCmd('view', '');
        if ($option != 'com_gridbox' || $view == 'blog') {
            return '';
        }
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            $id = $page ? $page->id : 0;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.type')
            ->from('#__gridbox_pages AS p')
            ->where('p.id = '.$id)
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');
        $db->setQuery($query);
        $type = $db->loadResult();
        if ($type != 'products') {
            return '';
        }
        $array = $input->cookie->get('gridbox_viewed_products', array(), 'array');
        $time = time() + 604800;
        $viewed = array($id);
        foreach ($array as $value) {
            if ($value != $id) {
                $viewed[] = $value;
            }
        }
        foreach ($viewed as $key => $value) {
            self::setcookie('gridbox_viewed_products['.$key.']', $value, $time);
        }
        if (count($viewed) != 1) {
            unset($viewed[0]);
        }
        $pks = implode(', ', $viewed);
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('DISTINCT p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category,
                p.app_id, p.meta_title, a.type, c.title as category, p.hits')
            ->from('#__gridbox_pages AS p')
            ->where('p.id IN ('.$pks.')')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')')
            ->order('FIELD(p.id, '.$pks.')');
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query, 0, $limit);
        $pages = $db->loadObjectList();
        $html = '';
        include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts-slider.php';
        foreach ($pages as $key => $page) {
            $html .= self::getRecentPostsHTML($page, $out, $max);
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;


    }

    public static function getRelatedPosts($id, $relate, $limit, $max, $order = 'created', $pageId = null)
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $edit_type = $input->get('edit_type', '', 'string');
        if ($edit_type == 'post-layout') {
            if (!$pageId) {
                $pageId = $app->input->get('id', 0, 'int');
            }
            $page = self::getPostLayoutPage($pageId);
            if ($page) {
                $pageId = $page->id;
            } else {
                $pageId = 0;
            }
        }
        if (!$pageId) {
            $pageId = $app->input->get('id', 0, 'int');
            $option = $app->input->getCmd('option', '');
            $view = $app->input->getCmd('view', '');
            if ($option != 'com_gridbox' || $view == 'blog') {
                return '';
            }
        }
        if ($order == 'random') {
            $order = 'RAND()';
        } else {
            $order = 'p.'.$order;
        }
        if ($order != 'p.title ASC' && $order != 'p.title DESC') {
            $order .= ' DESC';
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$pageId);
        $db->setQuery($query);
        $id = $db->loadResult();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        if ($relate == 'tags') {
            $query = $db->getQuery(true)
                ->select('m.tag_id')
                ->from('#__gridbox_tags_map AS m')
                ->leftJoin('`#__gridbox_tags` AS t ON t.id = m.tag_id')
                ->where('m.page_id = '.$pageId)
                ->where('t.published = 1')
                ->where('t.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('t.access in ('.$groups.')');
            $db->setQuery($query);
            $tags = $db->loadObjectList();
            $array = array();
            if (empty($tags)) {
                return self::getEmptyList();
            }
            foreach ($tags as $tag) {
                $array[] = $tag->tag_id;
            }
            $array = implode(',', $array);
        } else if ($relate == 'categories') {
            $query = $db->getQuery(true)
                ->select('page_category')
                ->from('#__gridbox_pages')
                ->where('id = '.$pageId);
            $db->setQuery($query);
            $category = $db->loadResult();
            if (empty($category)) {
                return self::getEmptyList();
            }
        } else if ($relate == 'custom') {
            $query = $db->getQuery(true)
                ->select('p.id')
                ->from('#__gridbox_pages AS p')
                ->where('r.product_id = '.$pageId)
                ->leftJoin('#__gridbox_store_related_products AS r ON r.related_id = p.id')
                ->order('r.order_list ASC');
            $db->setQuery($query);
            $custom = $db->loadObjectList();
            if (empty($custom)) {
                return self::getEmptyList();
            }
        }
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('DISTINCT p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category,
                p.app_id, p.meta_title, a.type, c.title as category, p.hits')
            ->from('#__gridbox_pages AS p')
            ->where('p.id <> '.$pageId)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if ($relate != 'custom') {
            $query->where('p.app_id = '.$id)
                ->order($order);
        }
        if ($relate == 'tags') {
            $query->leftJoin('`#__gridbox_tags_map` AS m ON p.id = m.page_id')
                ->where('m.tag_id in('.$array.')');
        } else if ($relate == 'categories') {
            $query->where('p.page_category = '.$category);
        } else if ($relate == 'custom') {
            $pks = array();
            foreach ($custom as $value) {
                $pks[] = $value->id;
            }
            $str = implode(', ', $pks);
            $query->where('p.id IN ('.$str.')')
                ->order('FIELD(p.id, '.$str.')');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query, 0, $limit);
        $pages = $db->loadObjectList();
        $html = '';
        if (is_object(self::$editItem) && self::$editItem->type == 'related-posts-slider') {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts-slider.php';
        } else {
            include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts.php';
        }
        foreach ($pages as $key => $page) {
            $html .= self::getRecentPostsHTML($page, $out, $max);
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }

        return $html;
    }

    public static function getPostNavigation($max, $id = null)
    {
        if (!$id) {
            $app = JFactory::getApplication();
            $id = $app->input->get('id', 0, 'int');
            $option = $app->input->getCmd('option', '');
            $view = $app->input->getCmd('view', '');
            if ($option != 'com_gridbox' || $view == 'blog') {
                return '';
            }
        }
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $date = date("Y-m-d H:i:s");
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $edit_type = $input->get('edit_type', '', 'string');
        if ($edit_type == 'post-layout') {
            $page = self::getPostLayoutPage($id);
            if ($page) {
                $id = $page->id;
            } else {
                $id = 0;
            }
        }
        $query = $db->getQuery(true)
            ->select('created, app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (empty($obj->app_id)) {
            return self::getEmptyList();
        }
        $nullDate = $db->quote($db->getNullDate());
        $digital = self::getSubscriptionProducts();
        $query = $db->getQuery(true)
            ->select('p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category, p.app_id,
                c.title as category, a.type, p.hits')
            ->from('#__gridbox_pages AS p')
            ->where('p.id <> '.$id)
            ->where('p.app_id = '.$obj->app_id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('p.created <= '.$db->quote($obj->created))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->order('p.created desc')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $prev = $db->loadObject();
        $query = $db->getQuery(true)
            ->select('p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category, p.app_id,
                c.title as category, a.type, p.hits')
            ->from('#__gridbox_pages AS p')
            ->where('p.id <> '.$id)
            ->where('p.app_id = '.$obj->app_id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('p.created >= '.$db->quote($obj->created))
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->order('p.created asc')
            ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where('c.published = 1')
            ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('c.access in ('.$groups.')');
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $next = $db->loadObject();
        if (!$next) {
            $query = $db->getQuery(true)
                ->select('p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category,
                    p.app_id, p.meta_title, a.type, p.hits, c.title as category')
                ->from('#__gridbox_pages AS p')
                ->where('p.app_id = '.$obj->app_id)
                ->where('p.page_category <> '.$db->quote('trashed'))
                ->where('p.published = 1')
                ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
                ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('p.page_access in ('.$groups.')')
                ->order('p.created asc')
                ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
                ->where('c.published = 1')
                ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('c.access in ('.$groups.')');
            if (!empty($digital)) {
                $str = implode(', ', $digital);
                $query->where('p.id NOT IN ('.$str.')');
            }
            $db->setQuery($query);
            $next = $db->loadObject();
        }
        if (!$prev) {
            $query = $db->getQuery(true)
                ->select('p.id, p.title, p.intro_text, p.created, p.intro_image, p.page_category, p.app_id,
                    p.meta_title, a.type, p.hits, c.title as category')
                ->from('#__gridbox_pages AS p')
                ->where('p.app_id = '.$obj->app_id)
                ->where('p.page_category <> '.$db->quote('trashed'))
                ->where('p.published = 1')
                ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
                ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('p.page_access in ('.$groups.')')
                ->order('p.created desc')
                ->leftJoin('`#__gridbox_categories` AS c ON p.page_category = c.id')
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
                ->where('c.published = 1')
                ->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('c.access in ('.$groups.')');
            if (!empty($digital)) {
                $str = implode(', ', $digital);
                $query->where('p.id NOT IN ('.$str.')');
            }
            $db->setQuery($query);
            $prev = $db->loadObject();
        }
        $html = '';
        include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts.php';
        if (isset($prev->id)) {
            $html .= self::getRecentPostsHTML($prev, $out, $max);
        }
        if (isset($next->id)) {
            $html .= self::getRecentPostsHTML($next, $out, $max);
        }
        if (empty($html)) {
            $html = self::getEmptyList();
        }
        
        return $html;
    }

    public static function translateMonth($n)
    {
        $month = array(1 => JText::_('JANUARY'), 2 => JText::_('FEBRUARY'), 3 => JText::_('MARCH'),
            4 => JText::_('APRIL'), 5 => JText::_('MAY'), 6 => JText::_('JUNE'),
            7 => JText::_('JULY'), 8 => JText::_('AUGUST'), 9 => JText::_('SEPTEMBER'),
            10 => JText::_('OCTOBER'), 11 => JText::_('NOVEMBER'), 12 =>JText::_('DECEMBER'));

        return $month[$n];
    }

    public static function getRecentPostsHTML($page, $out, $max)
    {
        $type = $page->app_id == 0 || $page->page_category == '' ? 'single' : 'blog';
        $url = self::getGridboxPageLinks($page->id, $type, $page->app_id, $page->page_category);
        $input = JFactory::getApplication()->input;
        $pageView = $input->get('view', 'gridbox', 'string');
        $className = '';
        $fields = self::getCategoryListFields($page->id, $page->app_id);
        if (!empty(self::$editItem) && ($pageView != 'gridbox' || self::$isError)) {
            $desktop = self::$editItem->desktop;
        } else {
            $desktop = null;
        }
        if (!empty($fields) && (empty(self::$editItem) ||
            (!empty(self::$editItem) && self::$editItem->type != 'search-result'
                && self::$editItem->type != 'store-search-result'))) {
            $desktopFiles = self::getDesktopFieldFiles($page->id);
            $fieldsStr = '<div class="ba-blog-post-fields"><div class="ba-blog-post-field-row-wrapper">';
            foreach ($fields as $field) {
                if (!isset($field->value)) {
                    $field->value = '';
                }
                if ($pageView != 'gridbox' && (empty($field->value) || $field->value == '[]')) {
                    continue;
                }
                if ($desktop && (!isset($desktop->fields->{$field->field_key}) || !$desktop->fields->{$field->field_key})) {
                    continue;
                }
                $options = json_decode($field->options);
                $label = $field->label;
                $value = '';
                if (empty($field->value)) {
                    $value = $field->value;
                } else if ($field->field_type == 'select' || $field->field_type == 'radio') {
                    foreach ($options->items as $option) {
                        if ($option->key == $field->value) {
                            if (!empty($value)) {
                                $value .= ', ';
                            }
                            $value .= $option->title;
                        }
                    }
                } else if ($field->field_type == 'checkbox') {
                    $fieldValue = json_decode($field->value);
                    foreach ($options->items as $option) {
                        if (in_array($option->key, $fieldValue)) {
                            $value .= '<span class="ba-blog-post-field-checkbox-value">'.$option->title.'</span>';
                        }
                    }
                } else if ($field->field_type == 'url') {
                    $fieldOptions = json_decode($field->options);
                    $valueOptions = json_decode($field->value);
                    $link = self::prepareGridboxLinks($valueOptions->link);
                    if (empty($link)) {
                        continue;
                    }
                    $value = '<a href="'.$link.'" '.$fieldOptions->download.' target="'.$fieldOptions->target;
                    $value .= '">'.$valueOptions->label.'</a>';
                } else if ($field->field_type == 'tag') {
                    $value = self::getPostTags($page->id);
                } else if ($field->field_type == 'time') {
                    if (!empty($field->value)) {
                        $valueOptions = json_decode($field->value);
                        $value = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
                    }
                } else if ($field->field_type == 'date' || $field->field_type == 'event-date') {
                    if (!empty($field->value)) {
                        $value = self::formatDate($field->value);
                    }
                } else if ($field->field_type == 'price' && !empty($field->value)) {
                    $fieldOptions = json_decode($field->options);
                    $thousand = $fieldOptions->thousand;
                    $separator = $fieldOptions->separator;
                    $decimals = $fieldOptions->decimals;
                    $value = self::preparePrice($field->value, $thousand, $separator, $decimals, 1);
                    if ($fieldOptions->position == '') {
                        $value = $fieldOptions->symbol.$value;
                    } else {
                        $value .= $fieldOptions->symbol;
                    }
                } else if ($field->field_type == 'file') {
                    if (!empty($field->value)) {
                        $fieldOptions = json_decode($field->options);
                        if (is_numeric($field->value) && isset($desktopFiles->{$field->value})) {
                            $desktopFile = $desktopFiles->{$field->value};
                            $src = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                        } else {
                            $src = $field->value;
                        }
                        $value = '<a href="'.JUri::root().$src.'" download>'.$fieldOptions->title.'</a>';
                    }
                } else if ($field->field_type == 'text') {
                    $value = htmlspecialchars($field->value);
                } else {
                    $value = $field->value;
                }
                $fieldsStr .= '<div class="ba-blog-post-field-row" data-id="'.$field->field_key
                    .'"><div class="ba-blog-post-field-title">';
                $fieldsStr .= $label.'</div><div class="ba-blog-post-field-value">'.$value.'</div></div>';
            }
            $fieldsStr .= '</div></div>';
        } else {
            $fieldsStr = '';
        }
        $url = JRoute::_($url);
        $productImages = self::getProductImages($page->id, $page->app_id);
        if (isset($page->type) && $page->type == 'products') {
            $className .= ' ba-store-app-product';
            $currency = self::$store->currency;
            $data = self::$storeHelper->getProductData($page->id);
            foreach ($data->variations as $key => $variation) {
                if (isset($variation->default) && $variation->default) {
                    $data->default = $variation;
                    $data->default->variation = $key;
                    $data->default->images = [];
                    break;
                }
            }
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/blog-post-add-to-cart.php';
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/blog-post-badge-wishlist.php';
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/blog-post-product-options.php';
        }
        if (isset($page->type) && $page->type == 'products' && isset($data->default) && !empty($data->default->images)) {
            $productImages = array();
            foreach ($data->default->images as $i => $image) {
                $img = new stdClass();
                $img->img = $image;
                $productImages[] = $img;
                if ($i == 1) {
                    break;
                }
            }
        }
        $intro_image = self::prepareIntroImage($page->intro_image);
        if (!empty($productImages)) {
            $intro_image = $productImages[0]->img;
        }
        $imageUrl = empty($intro_image) ? 'components/com_gridbox/assets/images/default-theme.png' : $intro_image;
        if (!self::isExternal($imageUrl)) {
            $imageUrl = JUri::root().$imageUrl;
        }
        $imageUrl = str_replace(' ', '%20', $imageUrl);
        $str = $out;
        $isproduct = isset($page->type) && $page->type == 'products';
        if (is_object(self::$editItem) &&
            (self::$editItem->type == 'recent-posts-slider' || self::$editItem->type == 'related-posts-slider'
                || self::$editItem->type == 'recently-viewed-products')) {
            $image = '<div class="ba-slideshow-img" style="';
            if ($isproduct && !empty($productImages)) {
                foreach ($productImages as $key => $imgObj) {
                    $imgObj->img = !self::isExternal($imgObj->img) ? JUri::root().$imgObj->img : $imgObj->img;
                    $imgObj->img = str_replace(' ', '%20', $imgObj->img);
                    $image .= '--product-image-'.$key.': url('.$imgObj->img.'); ';
                }
            } else if ($isproduct && empty($productImages) && !empty($intro_image)) {
                $image .= '--product-image-0: url('.$imageUrl.'); ';
            } else if (!empty($intro_image)) {
                $image .= 'background-image: url('.$imageUrl.');';
            }
            $image .= '">';
            if ($isproduct) {
                $image .= $badges;
            }
            $image .= '<a href="'.$url.'"></a></div>';
        } else if (!empty($intro_image) && (($desktop && $desktop->view->image) || !$desktop)) {
            $alt = !empty($page->meta_title) ? $page->meta_title : $page->title;
            $alt = strip_tags($alt);
            $image = '<div class="ba-blog-post-image"><img src="'.$imageUrl.'" alt="'.$alt;
            $image .= '"><div class="ba-overlay"></div><a href="'.$url.'" style="';
            if ($isproduct && !empty($productImages)) {
                foreach ($productImages as $key => $imgObj) {
                    $imgObj->img = !self::isExternal($imgObj->img) ? JUri::root().$imgObj->img : $imgObj->img;
                    $imgObj->img = str_replace(' ', '%20', $imgObj->img);
                    $image .= '--product-image-'.$key.': url('.$imgObj->img.'); ';
                }
            } else if ($isproduct && empty($productImages) && !empty($intro_image)) {
                $image .= '--product-image-0: url('.$imageUrl.'); ';
            } else {
                $image .= 'background-image: url('.$imageUrl.');';
            }
            $image .= '"></a>';
            if ($isproduct) {
                $image .= $badges;
            }
            $image .= '</div>';
        } else {
            $image = '';
        }
        if (($desktop && $desktop->view->date) || !$desktop) {
            $date = '<span>'.self::formatDate($page->created).'</span>';
            $dateStr = '<span class="ba-blog-post-date">'.$date.'</span>';
        } else {
            $dateStr = '';
        }
        if ($page->page_category != '' && (($desktop && $desktop->view->category) || !$desktop)) {
            $catUrl = self::getGridboxCategoryLinks($page->page_category, $page->app_id);
            $catStr = '<span class="ba-blog-post-category"><a href="';
            $catStr .= JRoute::_($catUrl).'">'.$page->category.'</a></span>';
        } else {
            $catStr = '';
        }
        if (($desktop && $desktop->view->hits) || !$desktop) {
            $viewStr = '<span class="ba-blog-post-hits"><span>'.$page->hits.' '.JText::_('VIEWS').'</span></span>';
        } else {
            $viewStr = '';
        }
        if (($desktop && $desktop->view->comments) || !$desktop) {
            $comments = self::getCommentsCount($page->id);
            $viewStr .= '<span class="ba-blog-post-comments"><a href="'.$url.'#total-count-wrapper">';
            if ($comments == 0) {
                $viewStr .= JText::_('LEAVE_COMMENT');
            } else {
                $viewStr .= $comments.' '.JText::_('COMMENTS');
            }
            $viewStr .= '</a></span>';
        }
        if (($desktop && $desktop->view->reviews) || !$desktop) {
            $reviews = self::getReviewsCount($page->id);
            if ($reviews->count == 0) {
                $reviews->rating = 0;
            }
            $reviewsStr = '<div class="ba-blog-post-reviews"><span class="ba-blog-post-rating-stars">';
            $floorRating = floor($reviews->rating);
            for ($i = 1; $i < 6; $i++) {
                $width = 'auto';
                if ($i == $floorRating + 1) {
                    $width = (($reviews->rating - $floorRating) * 100).'%';
                }
                $reviewsStr .= '<i class="ba-icons ba-icon-star'.($i<=$floorRating ? ' active' : '')
                    .'" style="width: '.$width.'"></i>';
            }
            $reviewsStr .= '</span><a class="ba-blog-post-rating-count" href="'.$url.'#total-reviews-count-wrapper">';
            if ($reviews->count == 0) {
                $reviewsStr .= JText::_('LEAVE_REVIEW');
            } else {
                $reviewsStr .= $reviews->count.' '.JText::_('REVIEWS');
            }
            $reviewsStr .= '</a></div>';
        } else {
            $reviewsStr = '';
        }
        if (($mblen = mb_strlen($page->intro_text)) != 0 && $max != 0 &&
            ($desktop && $desktop->view->intro) || !$desktop) {
            if (strpos($page->intro_text, 'ba-search-highlighted-word') === false) {
                $text = mb_substr($page->intro_text, 0, $max);
                if ($mblen > $max) {
                    $text .= '...';
                }
            } else {
                $text = $page->intro_text;
            }
            $introStr = '<div class="ba-blog-post-intro-wrapper">'.$text.'</div>';
        } else {
            $introStr = '';
        }
        $introStr = $reviewsStr.$introStr;
        if (($desktop && $desktop->view->button) || !$desktop) {
            $btnStr = '<div class="ba-blog-post-button-wrapper"><a class="ba-btn-transition" href="';
            $btnStr .= $url.'">'.(isset(self::$editItem->buttonLabel)? self::$editItem->buttonLabel : JText::_('READ_MORE'));
            $btnStr .= '</a></div>';
        } else {
            $btnStr = '';
        }
        if ($isproduct) {
            $fieldsStr = $addToCart.$fieldsStr;
        }
        $btnStr = $fieldsStr.$btnStr;
        $htmlTag = isset(self::$editItem->tag) ? self::$editItem->tag : 'h3';
        $titleStr = '<a href="'.$url.'"></a>';
        if ($isproduct) {
            $titleStr .= $productOptions;
        }
        if (self::$editItem && self::$editItem->type == 'post-navigation') {
            $titleStr .= '<div class="ba-post-navigation-info"><a href="'.$url.'"></a></div>';
        }
        if (($desktop && $desktop->view->title) || !$desktop) {
            $titleStr .= '<div class="ba-blog-post-title-wrapper"><';
            $titleStr .= $htmlTag.' class="ba-blog-post-title"><a href="'.$url;
            $titleStr .= '">'.$page->title.'</a></'.$htmlTag.'></div>';
        }
        $page->authors = self::getRecentPostAuthor($page->id);
        if (($desktop && $desktop->view->author) || !$desktop) {
            $authorsHtml = self::getAuthorsHtml($page->authors, 'ba-blog-post-author', $page->app_id);
        } else {
            $authorsHtml = '';
        }
        $str = str_replace('data-id="0"', 'data-id="'.$page->id.'"', $str);
        $str = str_replace('[ba-blog-post-date]', $authorsHtml.$dateStr, $str);
        $str = str_replace('[ba-blog-post-category]', $catStr, $str);
        $str = str_replace('[ba-blog-post-views]', $viewStr, $str);
        $str = str_replace('[ba-blog-post-intro]', $introStr, $str);
        $str = str_replace('[ba-blog-post-title]', $titleStr, $str);
        $str = str_replace('[ba-blog-post-image]', $image, $str);
        $str = str_replace('[ba-blog-post-btn]', $btnStr, $str);
        $str = str_replace('[classname]', $className, $str);

        return $str;
    }

    public static function getRecentPostAuthor($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('au.title, au.avatar, au.id')
            ->from('`#__gridbox_authors_map` AS au_m')
            ->where('au_m.page_id = '.$id)
            ->leftJoin('`#__gridbox_authors` AS au ON au.id = au_m.author_id')
            ->where('au.published = 1')
            ->order('au_m.id ASC');
        $db->setQuery($query);
        $authors = $db->loadObjectList();

        return $authors;
    }

    public static function addCacheData($data, $key, $subkey)
    {
        if (empty(self::$cacheData)) {
            self::$cacheData = new stdClass();
        }
        if (!isset(self::$cacheData->{$key})) {
            self::$cacheData->{$key} = new stdClass();
        }
        self::$cacheData->{$key}->{$subkey} = $data;
    }

    public static function checkEventField()
    {
        $id = JFactory::getApplication()->input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_fields')
            ->where('field_type = '.$db->quote('event-date'))
            ->where('app_id = '.$id);
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }

    public static function getEditorSearchResult()
    {
        $str = '';
        $date = '<span>'.self::formatDate(date('Y-m-d')).'</span>';
        for ($i = 0; $i < 6; $i++) {
            $str .= '<div class="ba-blog-post'.(self::$editItem->type==' store-search-result' ? 'ba-store-app-product' : '');
            $str .= '"><div class="ba-blog-post-image"><img src="'.JUri::root();
            $str .= 'components/com_gridbox/assets/images/default-theme.png" alt="'.JText::_('TITLE');
            $str .= '"><div class="ba-overlay"></div><a href="'.JUri::root();
            $str .= '" style="background-image: url('.JUri::root()
                .'components/com_gridbox/assets/images/default-theme.png);"></a>';
            if (self::$editItem->type == 'store-search-result') {
                $str .= '<div class="ba-blog-post-wishlist-wrapper"><i class="ba-icons ba-icon-heart"></i>';
                $str .= '<span class="ba-tooltip ba-left">'.JText::_('ADD_TO_WISHLIST').'</span></div>';
            }
            $str .= '</div><div class="ba-blog-post-content"><div class="ba-blog-post-title-wrapper">';
            $str .= '<'.self::$editItem->tag.' class="ba-blog-post-title"><a href="'.JUri::root().'">'
                .JText::_('PAGE_TITLE');
            $str .= '</a></'.self::$editItem->tag.'></div><div class="ba-blog-post-info-wrapper">';
            $str .= '<span class="ba-blog-post-author"><a href="#"><span class="ba-author-avatar"';
            $str .= ' style="background-image: url('.JUri::root();
            $str .= 'components/com_gridbox/assets/images/default-user.png)"></span>';
            $str .= JText::_('AUTHOR').'</a></span>';
            $str .= '<span class="ba-blog-post-date">';
            $str .= $date.'</span>';
            $str .= '<span class="ba-blog-post-category"><a href="'.JUri::root().'">';
            $str .= JText::_('CATEGORY').'</a></span><span class="ba-blog-post-comments"><a href="';
            $str .= JUri::root().'#total-count-wrapper">0 '.JText::_('COMMENTS').'</a></span>';
            $str .= '<span class="ba-blog-post-hits"><span>'.JText::_('VIEWS').'</span></span>';
            $str .= '</div>';
            $str .= '<div class="ba-blog-post-reviews"><span class="ba-blog-post-rating-stars">';
            for ($j = 1; $j < 6; $j++) {
                $str .= '<i class="ba-icons ba-icon-star"></i>';
            }
            $str .= '</span><a class="ba-blog-post-rating-count" href="#total-reviews-count-wrapper">';
            $str .= JText::_('LEAVE_REVIEW');
            $str .= '</a></div>';
            $str .= '<div class="ba-blog-post-intro-wrapper">';
            $str .= JText::_('INTRO_TEXT').'</div>';
            if (self::$editItem->type == 'store-search-result') {
                $currency = self::$store->currency;
                $total = self::preparePrice(36.99, $currency->thousand, $currency->separator, $currency->decimals);
                $str .= '<div class="ba-blog-post-add-to-cart-wrapper"><div class="ba-blog-post-add-to-cart-price">';
                $str .= '<span class="ba-blog-post-add-to-cart-price-wrapper '.self::$store->currency->position;
                $str .= '"><span class="ba-blog-post-add-to-cart-price-currency">'.self::$store->currency->symbol;
                $str .= '</span><span class="ba-blog-post-add-to-cart-price-value">'.$total.'</span></span></div>';
                $str .= '<div class="ba-blog-post-add-to-cart-button"><span class="ba-blog-post-add-to-cart">';
                $str .= JText::_('ADD_TO_CART').'</span></div></div>';
            }
            $str .= '<div class="ba-blog-post-button-wrapper">';
            $str .= '<a class="ba-btn-transition" href="'.JUri::root().'">';
            $str .= (isset(self::$editItem->buttonLabel) ? self::$editItem->buttonLabel : JText::_('READ_MORE'));
            $str .= '</a></div></div></div>';
        }

        return $str;
    }

    public static function getSearchFields($search, $type)
    {
        $db = JFactory::getDbo();
        $searchWords = explode(' ', $search);
        $query = $db->getQuery(true)
            ->select('distinct option_key, field_id')
            ->from('#__gridbox_fields_data');
        $wheres = [];
        foreach ($searchWords as $word) {
            $wheres[] = '(value REGEXP '.$db->quote('^'.$word).' OR value REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word).')';
        }$query->where('('.implode(' AND ', $wheres).')');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $wheres = [];
        foreach ($searchWords as $word) {
            $wheres[] = '(pf.value REGEXP '.$db->quote('^'.$word).' OR pf.value REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word).')';
        }
        $where = '('.implode(' AND ', $wheres);
        if ($time = strtotime($search)) {
            $dateStr = date('Y-m-d', $time);
            $where .= ' OR pf.value LIKE '.$db->quote('%'.$db->escape($dateStr, true).'%');
        }
        foreach ($result as $value) {
            $where .= ' OR (pf.value = '.$value->option_key.' AND pf.field_id = '.$value->field_id.')';
        }
        $where .= ')';
        $query = $db->getQuery(true)
            ->select('distinct p.id')
            ->from('`#__gridbox_pages` AS p')
            ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.page_id = p.id')
            ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id')
            ->where($where)
            ->where('pf.field_type <> '.$db->quote('field-google-maps'))
            ->where('pf.field_type <> '.$db->quote('field-simple-gallery'))
            ->where('pf.field_type <> '.$db->quote('product-gallery'))
            ->where('pf.field_type <> '.$db->quote('field-slideshow'))
            ->where('pf.field_type <> '.$db->quote('product-slideshow'))
            ->where('pf.field_type <> '.$db->quote('field-video'))
            ->where('pf.field_type <> '.$db->quote('image-field'))
            ->where('pf.field_type <> '.$db->quote('field-button'))
            ->where('pf.field_type <> '.$db->quote('file'));
        if ($type == 'store-search') {
            $query->where('a.type ='.$db->quote('products'));
        }
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $array = [];
        foreach ($result as $value) {
            $array[] = $value->id;
        }
        $subStr = implode(', ', $array);
        self::addCacheData($subStr, $type, 'fields');
    }

    public static function getSearchResult($search, $limit, $start, $max, $type, $pagination = '')
    {
        $view = JFactory::getApplication()->input->get('view', '');
        $app_id = JFactory::getApplication()->input->get('app', '', 'string');
        $apps = JFactory::getApplication()->input->get('apps', '', 'string');
        if ($view == 'gridbox') {
            return self::getEditorSearchResult();
        } else if (empty($search)) {
            return '';
        }
        $active = $start;
        $start *= $limit;
        if (!empty($pagination)) {
            $limit = $start + $limit;
            $start = 0;
        }
        $html = '';
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        $groups = implode(',', $groups);
        $wheres = [];
        $searchWords = explode(' ', $search);
        $titles = [];
        $params = [];
        foreach ($searchWords as $word) {
            $title = '(p.title REGEXP '.$db->quote('^'.$word).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $param = '(p.params REGEXP '.$db->quote('^'.$word).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $text = mb_strtoupper($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_strtolower($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_ucfirst($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $titles[] = $title;
            $params[] = $param;
        }
        $wheres[] = '('.implode(' AND ', $titles).')';
        $wheres[] = '('.implode(' AND ', $params).')';
        self::getSearchFields($search, $type);
        $subStr = self::$cacheData->{$type}->fields;
        if (!empty($subStr)) {
            $wheres[] = 'p.id IN ('.$subStr.')';
        }
        if ($type == 'store-search') {
            $wheres[] = 'pd.sku LIKE '.$db->quote('%'.$db->escape($search, true).'%', false);
        }
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('p.id, p.title, p.created, p.intro_image, p.page_category, p.app_id,
                p.intro_text, p.meta_title, p.params, p.hits')
            ->from('#__gridbox_pages AS p')
            ->where('('.implode(' OR ', $wheres).')')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->order('p.created desc');
        if ($type == 'store-search') {
            $query->leftJoin('#__gridbox_app AS a ON p.app_id = a.id')
                ->select('a.type')
                ->where('a.type ='.$db->quote('products'));
            $query->leftJoin('#__gridbox_store_product_data AS pd ON pd.product_id = p.id');
        }
        if ($app_id !== '' && empty($apps)) {
            $query->where('p.app_id = '.$app_id);
        } else if (!empty($apps)) {
            $data = explode(',', $apps);
            $array = [];
            foreach ($data as $id) {
                $array[] = $id * 1;
            }
            $str = implode(', ', $array);
            $query->where('p.app_id IN ('.$str.')');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query, $start, $limit);
        $pages = $db->loadObjectList();
        include JPATH_ROOT.'/components/com_gridbox/views/layout/blog-posts.php';
        foreach ($pages as $key => $page) {
            $word = preg_replace('#\xE3\x80\x80#', ' ', $search);
            $words = preg_split("/\s+/u", $word);
            $needle = $words[0];
            $page->params = preg_replace('/\[main_menu=+(.*?)\]/i', '', $page->params);
            $page->params = preg_replace('/\[global item=+(.*?)\]/i', '', $page->params);
            $page->params = self::clearDOM($page->params);
            $page->title = self::highLight($page->title, $search, $words, null);
            if (empty($page->intro_text)) {
                $text = self::highLight($page->params, $search, $words, $max);
            } else {
                $text1 = self::highLight($page->intro_text, $search, $words, $max);
                $text2 = self::highLight($page->params, $search, $words, $max);
                $pos1 = strpos($text1, 'ba-search-highlighted-word');
                $pos2 = strpos($text2, 'ba-search-highlighted-word');
                if ($pos1 !== false) {
                    $text = $text1;
                } else if ($pos2 !== false) {
                    $text = $text2;
                } else if (!empty($page->intro_text)) {
                    $text = $text1;
                } else {
                    $text = $text2;
                }
            }
            if (trim($text) == 'Click Here And Start Typing') {
                $text = '';
            }
            $page->intro_text = $text;
            if ($page->app_id != 0 && $page->page_category != '') {
                $query = $db->getQuery(true)
                    ->select('c.title')
                    ->from('#__gridbox_categories AS c')
                    ->leftJoin('`#__gridbox_pages` AS p ON p.page_category = c.id')
                    ->where('p.id = '.$page->id);
                $db->setQuery($query);
                $page->category = $db->loadResult();
            }
            $html .= self::getRecentPostsHTML($page, $out, $max);
        }

        return $html;
    }

    public static function prepareSearchContent($text, $searchword, $max)
    {
        $text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
        $text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);
        $text = preg_replace('/[ \t\n\r\f]/', " ", $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = strip_tags($text);

        return self::smartSubstr($text, $searchword, $max);
    }

    public static function remove_accents($str)
    {
        $str = JLanguageTransliterate::utf8_latin_to_ascii($str);

        return preg_replace("/[\"'^]([a-z])/ui", '\1', $str);
    }

    public static function smartSubstr($text, $searchword, $max)
    {
        $ltext = mb_strtolower(self::remove_accents($text));
        $length = mb_strlen($ltext);
        $lsearchword = mb_strtolower(self::remove_accents($searchword));
        $pos = mb_strpos($ltext, $lsearchword);
        if ($pos !== false && $max !== null) {
            $end = $pos + $max < $length ? $max : $length - $pos;
            $text = (($pos > 0) ? '...' : '').(mb_substr($text, $pos, $end)).'...';
        }
        
        return $text;
    }

    public static function highLight($string, $needle, $words, $max)
    {
        $hl1 = '<span class="ba-search-highlighted-word">';
        $hl2 = '</span>';
        $highlighterLen = strlen($hl1.$hl2);
        $quoteStyle = version_compare(PHP_VERSION, '5.4', '>=') ? ENT_NOQUOTES | ENT_HTML401 : ENT_NOQUOTES;
        $row = html_entity_decode($string, $quoteStyle, 'UTF-8');
        $row = self::prepareSearchContent($row, $needle, $max);
        $lowerCaseRow = mb_strtolower($row);
        $transliteratedLowerCaseRow = self::remove_accents($lowerCaseRow);
        $searchWords = explode(' ', $needle);
        $posCollector = array();
        foreach ($searchWords as $word) {
            $found = false;
            $lowerCaseHighlightWord = mb_strtolower($word);
            if (($pos = mb_strpos($lowerCaseRow, $lowerCaseHighlightWord)) !== false
                && (!isset($lowerCaseRow[$pos - 1]) || $lowerCaseRow[$pos - 1] == ' ')) {
                $found = true;
            } else if (($pos = mb_strpos($transliteratedLowerCaseRow, $lowerCaseHighlightWord)) !== false
                && (!isset($transliteratedLowerCaseRow[$pos - 1]) || $transliteratedLowerCaseRow[$pos - 1] == ' ')) {
                $found = true;
            }
            if ($found === true) {
                $posCollector[$pos] = $word;
            }
        }
        if (count($posCollector)) {
            ksort($posCollector);
            $cnt = 0;
            $lastHighlighterEnd = -1;
            foreach ($posCollector as $pos => $highlightWord) {
                $pos += $cnt * $highlighterLen;
                $chkOverlap = $pos - $lastHighlighterEnd;
                if ($chkOverlap >= 0) {
                    $highlightWordLen = mb_strlen($highlightWord);
                    $row = mb_substr($row, 0, $pos) . $hl1 . mb_substr($row, $pos, $highlightWordLen)
                        .$hl2.mb_substr($row, $pos + $highlightWordLen);
                    $cnt++;
                    $lastHighlighterEnd = $pos+$highlightWordLen+$highlighterLen;
                }
            }
        }

        return $row;
    }

    public static function getEditorSearchResultPaginator($type = '')
    {
        $str = '<div class="ba-blog-posts-pagination-wrapper">';
        $style = $type == '' ? '' : 'style="display:none;"';
        $str .= '<div class="ba-blog-posts-pagination" data-type="" '.$style.'>';
        $str .= '<span class="disabled ba-search-first-page"><a href="#"><i class="ba-icons ba-icon-skip-previous"></i>';
        $str .= '</a></span><span class="disabled ba-search-prev-page"><a href="#">';
        $str .= '<i class="ba-icons ba-icon-fast-rewind">';
        $str .= '</i></a></span><span class="active ba-search-pages"><a href="#">1</a></span>';
        $str .= '<span class="ba-search-pages"><a href="#">2</a></span><span class="ba-search-next-page">';
        $str .= '<a href="#"><i class="ba-icons ba-icon-fast-forward"></i></a></span><span class="ba-search-last-page">';
        $str .= '<a href="#"><i class="ba-icons ba-icon-skip-next"></i></a></span></div>';
        $style = $type == 'load-more' || $type == 'load-more-infinity' ? '' : 'style="display:none;"';
        $str .= '<div class="ba-blog-posts-pagination" data-type="load" '.$style.'>';
        $str .= '<span ><a href="#" data-page="2">'.JText::_('LOAD_MORE').'</a></span>';
        $str .= '</div>';
        $str .= '</div>';


        return $str;
    }

    public static function getSearchResultPaginator($search, $limit, $start, $max, $type, $pagination = '')
    {
        $view = JFactory::getApplication()->input->get('view', '');
        $app_id = JFactory::getApplication()->input->get('app', '', 'string');
        $apps = JFactory::getApplication()->input->get('apps', '', 'string');
        if ($view == 'gridbox') {
            return self::getEditorSearchResultPaginator($pagination);
        } else if (empty($search)) {
            return '';
        }
        $active = $start;
        $start *= $limit;
        $html = '';
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        $groups = implode(',', $levels);
        $wheres = [];
        $searchWords = explode(' ', $search);
        $titles = [];
        $params = [];
        foreach ($searchWords as $word) {
            $title = '(p.title REGEXP '.$db->quote('^'.$word).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $param = '(p.params REGEXP '.$db->quote('^'.$word).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$word);
            $text = mb_strtoupper($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_strtolower($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text);
            $text = mb_ucfirst($word);
            $title .= ' OR p.title REGEXP '.$db->quote('^'.$text).' OR p.title REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $param .= ' OR p.params REGEXP '.$db->quote('^'.$text).' OR p.params REGEXP '.$db->quote('[ !@#$%^&*():;>"]'.$text).')';
            $titles[] = $title;
            $params[] = $param;
        }
        $wheres[] = '('.implode(' AND ', $titles).')';
        $wheres[] = '('.implode(' AND ', $params).')';
        $fieldsStr = self::$cacheData->{$type}->fields;
        if (!empty($fieldsStr)) {
            $wheres[] = 'p.id in ('.$fieldsStr.')';
        }
        if ($type == 'store-search') {
            $wheres[] = 'pd.sku LIKE '.$db->quote('%'.$db->escape($search, true).'%', false);
        }
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('COUNT(p.id)')
            ->from('#__gridbox_pages AS p')
            ->where('('.implode(' OR ', $wheres).')')
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
            ->where('p.page_access in ('.$groups.')')
            ->order('p.created desc');
        if ($type == 'store-search') {
            $query->leftJoin('#__gridbox_app AS a ON p.app_id = a.id')
                ->where('a.type ='.$db->quote('products'));
            $query->leftJoin('#__gridbox_store_product_data AS pd ON pd.product_id = p.id');
        }
        if ($app_id !== '' && empty($apps)) {
            $query->where('p.app_id = '.$app_id);
        } else if (!empty($apps)) {
            $data = explode(',', $apps);
            $array = [];
            foreach ($data as $id) {
                $array[] = $id * 1;
            }
            $str = implode(', ', $array);
            $query->where('p.app_id IN ('.$str.')');
        }
        $digital = self::getSubscriptionProducts();
        if (!empty($digital)) {
            $str = implode(', ', $digital);
            $query->where('p.id NOT IN ('.$str.')');
        }
        $db->setQuery($query);
        $count = $db->loadResult();
        if ($limit == 0) {
            $limit = 1;
        }
        $allPages = ceil($count / $limit);
        if ($count != 0 && $allPages != 1) {
            $start = 0;
            $max = $allPages;
            if ($active > 2 && $allPages > 4) {
                $start = $active - 2;
            }
            if ($allPages > 4 && ($allPages - $active) < 3) {
                $start = $allPages - 5;
            }
            if ($allPages > $active + 2) {
                $max = $active + 3;
                if ($allPages > 3 && $active < 2) {
                    $max = 4;
                }
                if ($allPages > 4 && $active < 2) {
                    $max = 5;
                }
            }
            $prev = $active == 0 ? 1 : $active;
            $next = $active == $allPages - 1 ? $allPages : $active + 2;
            $system = self::getSystemParamsByType($type);
            $url = self::getGridboxSystemLinks($system->id);
            $url .= '&query='.$search;
            if (!empty($app_id)) {
                $url .= '&app='.$app_id;
            }
            if (!empty($apps)) {
                $url .= '&apps='.$apps;
            }
            include JPATH_ROOT.'/components/com_gridbox/views/layout/search-result-pagination.php';
            $html .= $out;
        }

        return $html;
    }

    public static function checkMenuItems($menuItems, $itemId)
    {
        $flag = true;
        foreach ($menuItems as $menu) {
            if ($menu->id == $itemId) {
                $flag = false;
                break;
            }
        }

        return $flag;
    }

    public static function getAppCategories($app_id, $id = 0, $level = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('title, id, app_id')
            ->from('#__gridbox_categories')
            ->where('app_id = '.$app_id)
            ->where('parent = '.$id)
            ->order('order_list ASC');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $data = [];
        foreach ($categories as $category) {
            $category->level = $level;
            $data[] = $category;
            $sub = self::getAppCategories($app_id, $category->id, $level + 1);
            $data = array_merge($data, $sub);
        }

        return $data;
    }

    public static function getSubmissionFields($id)
    {
        $items = self::getAppFields($id);
        $obj = new stdClass();
        $array = ['title' => 'TITLE', 'category' => 'CATEGORY', 'image' => 'MAIN_IMAGE', 'description' => 'SHORT_DESCRIPTION'];
        foreach ($array as $ind => $title) {
            $type = $ind == 'title' ? 'text' : ($ind == 'category' ? 'category' : ($ind == 'image' ? 'image-field' : 'textarea'));
            $item = new stdClass();
            $item->id = $ind;
            $item->field_key = $ind;
            $item->app_id = $id;
            $item->field_type = $type;
            $item->label = JText::_($title);
            $item->required = 1;
            $settings = new stdClass();
            $settings->label = '';
            $settings->description = '';
            $settings->type = $type;
            if ($ind == 'category') {
                $settings->items = self::getAppCategories($id);
            } else if ($ind == 'image') {
                $settings->source = '';
                $params = JComponentHelper::getParams('com_media');
                $settings->size = $params->get('upload_maxsize') * 1000;
            } else if ($ind == 'description') {
                $settings->texteditor = false;
            }
            $item->options = json_encode($settings);
            $item->order_list = 0;
            $item->product = false;
            $obj->{$ind} = $item;
        }
        foreach ($items as $item) {
            $item->product = false;
            $obj->{$item->field_key} = $item;
        }
        $groups = self::getFieldsGroups($id);
        $array = json_decode($groups);
        foreach ($array as $key => $group) {
            $type = 'group-headline';
            $item = new stdClass();
            $item->id = $key;
            $item->field_key = $key;
            $item->app_id = $id;
            $item->field_type = $type;
            $item->label = $group->title;
            $item->required = 0;
            $settings = new stdClass();
            $settings->label = '';
            $settings->description = '';
            $settings->type = $type;
            $item->options = json_encode($settings);
            $item->order_list = 0;
            $item->product = false;
            $obj->{$key} = $item;
        }

        return $obj;
    }

    public static function getAppFilterFields($id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();
        if ($type == 'booking') {
            $item = (object)[
                'label' => JText::_('DATE_PICKER'),
                'product' => true,
                'title' => JText::_('SELECT_DATE'),
                'field_type' => 'date-picker',
                'field_key' => 'date-picker',
                'options' => '{}'
            ];
            $obj->{'date-picker'} = $item;

            $item = (object)[
                'label' => JText::_('DATE_RANGE_PICKER'),
                'product' => true,
                'title' => JText::_('SELECT_DATES'),
                'field_type' => 'date-range-picker',
                'field_key' => 'date-range-picker',
                'options' => '{}'
            ];
            $obj->{'date-range-picker'} = $item;
        }
        if ($type == 'products' || $type == 'booking') {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_products_fields')
                ->where('field_type <> '.$db->quote('textinput'))
                ->where('field_type <> '.$db->quote('textarea'))
                ->where('field_type <> '.$db->quote('file'));
            $db->setQuery($query);
            $fields = $db->loadObjectList();
            $item = (object)[
                'label' => JText::_('PRODUCT').': '.JText::_('PRICE'),
                'product' => true,
                'title' => JText::_('PRICE'),
                'field_type' => 'price',
                'field_key' => 'price',
                'options' => '{}'
            ];
            $obj->price = $item;
            foreach ($fields as $item) {
                $query = $db->getQuery(true)
                    ->select('COUNT(vm.product_id)')
                    ->from('#__gridbox_store_products_fields_data AS fd')
                    ->where('fd.field_id = '.$db->quote($item->id))
                    ->leftJoin('#__gridbox_store_product_variations_map AS vm ON vm.option_key = fd.option_key');
                $db->setQuery($query);
                $count = $db->loadResult();
                if ($count == 0) {
                    continue;
                }
                $item->product = true;
                $item->label = JText::_('PRODUCT').': '.$item->title;
                $obj->{$item->field_key} = $item;
            }
        }
        $query = $db->getQuery(true)
            ->select('options, label, id, field_key, field_type')
            ->from('#__gridbox_fields')
            ->where('app_id = '.$id)
            ->where('field_type <> '.$db->quote('field-simple-gallery'))
            ->where('field_type <> '.$db->quote('field-slideshow'))
            ->where('field_type <> '.$db->quote('product-gallery'))
            ->where('field_type <> '.$db->quote('product-slideshow'))
            ->where('field_type <> '.$db->quote('field-google-maps'))
            ->where('field_type <> '.$db->quote('field-video'))
            ->where('field_type <> '.$db->quote('image-field'))
            ->where('field_type <> '.$db->quote('field-button'));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $item) {
            $item->product = false;
            $obj->{$item->field_key} = $item;
        }

        return $obj;
    }

    public static function getFilterProductsQuery($id, $db)
    {
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('DISTINCT d.product_id, d.price, d.sale_price')
            ->from('#__gridbox_store_product_data AS d')
            ->where('a.id = '.$id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$date)
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')')
            ->leftJoin('#__gridbox_pages AS p ON p.id = d.product_id')
            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id');

        return $query;
    }

    public static function getItemsFilter($id)
    {
        $str = '';
        if (empty($id)) {
            return $str;
        }
        $input = JFactory::getApplication()->input;
        $queryStr = $input->get('query', '', 'raw');
        $object = new stdClass();
        $db = JFactory::getDbo();
        $appFields = self::getAppFilterFields($id);
        $view = $input->get('view', 'gridbox', 'string');
        if (!empty(self::$editItem) && $view != 'gridbox') {
            $desktop = self::$editItem->desktop;
        } else {
            $desktop = null;
        }
        if (!empty($queryStr)) {
            $url .= '&query='.$queryStr;
            $array = explode('__', $queryStr);
            $values = [];
            $keys = [];
            foreach ($array as $k => $v) {
                if ($k % 2 == 0) {
                    $keys[] = $v;
                }
                else {
                    $values[] = $v;
                }
            }
            foreach ($keys as $i => $key) {
                $object->{$key} = explode('--', $values[$i]);
            }
        }
        $selectedArea = '<div class="ba-selected-filter-values-wrapper"><div class="ba-selected-filter-values-header">';
        $selectedArea .= '<span class="ba-selected-filter-values-title">'.JText::_('SELECTED').'</span>';
        $selectedArea .= '</div>';
        $selectedArea .= '<div class="ba-selected-filter-values-body">';
        $totalChecked = 0;
        $list = ['checkbox', 'radio', 'select', 'price', 'date', 'event-date'];
        foreach ($appFields as $appField) {
            if ((!in_array($appField->field_type, $list) && !$appField->product) || empty($appField->label)) {
                continue;
            }
            if ($desktop && (!isset($desktop->fields->{$appField->field_key}) || !$desktop->fields->{$appField->field_key})) {
                continue;
            }
            if ($appField->product) {
                $label = $appField->title;
            } else {
                $label = $appField->label;
            }
            $str .= '<div class="ba-field-filter" data-id="' . $appField->field_key . '">';
            $str .= '<div class="ba-field-filter-label"><span>' . $label . '</span><i class="ba-icons ba-icon-caret-down"></i></div>';
            $str .= '<div class="ba-field-filter-value-wrapper" data-type="'. $appField->field_type .'"><div class="ba-field-filter-value">';
            if (!$appField->product || ($appField->product && $appField->field_key != 'price')) {
                $options = json_decode($appField->options);
            }
            if ($appField->field_type == 'price') {
                if ($appField->field_key == 'price') {
                    $array = [];
                    $options = self::$store->currency;
                    $query = self::getFilterProductsQuery($id, $db);
                    $db->setQuery($query);
                    $data = $db->loadObjectList();
                    foreach ($data as $value) {
                        $prices = self::prepareProductPrices($value->product_id, $value->price, $value->sale_price);
                        $obj = new stdClass();
                        $obj->value = $prices->sale_price != '' ? $prices->sale_price : $prices->price;
                        $array[] = $obj;
                    }
                } else {
                    $query = $db->getQuery(true)
                        ->select('f.value')
                        ->from('#__gridbox_page_fields AS f')
                        ->where('f.field_id = '.$appField->id);
                    $db->setQuery($query);
                    $array = $db->loadObjectList();
                }
                $minMax = new stdClass();
                foreach ($array as $value) {
                    if (is_numeric($value->value)) {
                        $minMax->max = !isset($minMax->max) || $value->value * 1 > $minMax->max ?
                            $value->value * 1 : $minMax->max;
                        $minMax->min = !isset($minMax->min) || $value->value * 1 < $minMax->min ?
                            $value->value * 1 : $minMax->min;
                    }
                }
                if (!isset($minMax->min)) {
                    $minMax->min = 0;
                }
                if (!isset($minMax->max)) {
                    $minMax->max = 0;
                }
                $minMax->min = floor($minMax->min);
                $minMax->max = ceil($minMax->max);
                if (isset($object->{$label})) {
                    $minMax->minValue = $object->{$label}[0];
                    $minMax->maxValue = $object->{$label}[1];
                    if (empty($minMax->minValue)) {
                        $minMax->minValue = $minMax->min;
                    }
                    if (empty($minMax->maxValue)) {
                        $minMax->maxValue = $minMax->max;
                    }
                    $totalChecked++;
                    $selectedArea .= '<span class="ba-selected-filter-values" data-name="'.$label;
                    $selectedArea .= '" data-value="'.$value->title.'"><span class="ba-selected-filter-value">';
                    $selectedArea .= $options->symbol.' '.$minMax->minValue.' - '.$options->symbol.' '.$minMax->maxValue;
                    $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                } else {
                    $minMax->minValue = $minMax->min;
                    $minMax->maxValue = $minMax->max;
                }
                $diff = $minMax->max - $minMax->min;
                if ($diff == 0) {
                    $diff = 1;
                }
                $percentage = [
                    ($minMax->minValue - $minMax->min) * 100 / $diff,
                    ($minMax->maxValue - $minMax->min) * 100 / $diff
                ];
                $str .= '<div class="ba-field-filter-range-wrapper">';
                $str .= '<div class="price-range-track" data-min="'.$minMax->min.'" data-max="'.$minMax->max;
                $str .= '" data-min-value="'.$minMax->minValue.'" data-max-value="'.$minMax->maxValue.'">';
                $str .= '<div class="price-range-selection" style="left: '.$percentage[0];
                $str .= '%; width: '.($percentage[1] - $percentage[0]).'%;"></div>';
                $str .= '<div class="price-range-handle" style="left: '.$percentage[0].'%;"></div>';
                $str .= '<div class="price-range-handle" style="left: '.$percentage[1].'%;"></div></div>';
                $str .= '</div>';
                $str .= '<div class="ba-field-filter-input-wrapper">';
                $str .= '<span class="ba-field-filter-price-symbol">'.$options->symbol.'</span>';
                $str .= '<input type="number" name="'.$label.'" value="'.$minMax->minValue.'" data-min="'.$minMax->min.'">';
                $str .= '<span class="ba-field-filter-price-delimiter">-</span>';
                $str .= '<span class="ba-field-filter-price-symbol">'.$options->symbol.'</span>';
                $str .= '<input type="number" name="'.$label.'" value="'.$minMax->maxValue.'" data-max="'.$minMax->max.'">';
                $str .= '</div>';
            } else if ($appField->field_type == 'date-picker') {
                $offset = JFactory::getConfig()->get('offset');
                $zone = new DateTimeZone($offset);
                if (isset($object->{$label})) {
                    $totalChecked++;
                    $date = JDate::getInstance($object->{$label}[0], $zone)->format('M d, Y', true);
                    $selectedArea .= '<span class="ba-selected-filter-values" data-name="'.$label;
                    $selectedArea .= '" data-value=""><span class="ba-selected-filter-value">'.$date;
                    $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                }
                $str .= '<div class="ba-field-filter-date-calendars">';
                $str .= '<div><div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
                $str .= '<input type="text" class="open-calendar-dialog" placeholder="' . JText::_('SELECT_DATE') .
                    '" readonly data-format="M d, Y" name="'.$label.'" data-value="';
                $str .= (isset($object->{$label}) ? $object->{$label}[0] : '').'" value="'.(isset($object->{$label}) ? $date : '').'">';
                $str .= '</div></div>';
            } else if ($appField->field_type == 'date-range-picker') {
                $offset = JFactory::getConfig()->get('offset');
                $zone = new DateTimeZone($offset);
                if (isset($object->{$label})) {
                    $totalChecked++;
                    $dates = [];
                    $dates[] = JDate::getInstance($object->{$label}[0], $zone)->format('M d, Y', true);
                    $dates[] = JDate::getInstance($object->{$label}[1], $zone)->format('M d, Y', true);
                    $selectedArea .= '<span class="ba-selected-filter-values" data-name="'.$label;
                    $selectedArea .= '" data-value=""><span class="ba-selected-filter-value">'.implode(' - ', $dates);
                    $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                }
                $str .= '<div class="ba-field-filter-date-calendars">';
                $str .= '<div><div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
                $str .= '<input type="text" class="open-calendar-dialog" data-multiple="1" placeholder="' . JText::_('FROM') .
                    '" readonly data-key="from" data-format="M d, Y" name="'.$label.'" data-value="';
                $str .= (isset($object->{$label}) ? $object->{$label}[0] : '').'" value="'.(isset($object->{$label}) ? $dates[0] : '').'">';
                $str .= '</div>';
                $str .= '<div><div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
                $str .= '<input type="text" class="open-calendar-dialog" data-multiple="1" placeholder="' . JText::_('TO') .
                    '" readonly data-key="to" data-format="M d, Y" name="'.$label.'" data-value="';
                $str .= (isset($object->{$label}) ? $object->{$label}[1] : '').'" value="'.(isset($object->{$label}) ? $dates[1] : '').'">';
                $str .= '</div></div>';
            } else if ($appField->field_type == 'date' || $appField->field_type == 'event-date') {
                $offset = JFactory::getConfig()->get('offset');
                $zone = new DateTimeZone($offset);
                if (isset($object->{$label})) {
                    $totalChecked++;
                    $dates = [];
                    $dates[] = JDate::getInstance($object->{$label}[0], $zone)->format('M d, Y', true);
                    $dates[] = JDate::getInstance($object->{$label}[1], $zone)->format('M d, Y', true);
                    $selectedArea .= '<span class="ba-selected-filter-values" data-name="'.$label;
                    $selectedArea .= '" data-value=""><span class="ba-selected-filter-value">'.implode(' - ', $dates);
                    $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                }
                $str .= '<div class="ba-field-filter-date-calendars">';
                $str .= '<div><div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
                $str .= '<input type="text" class="open-calendar-dialog" placeholder="' . JText::_('FROM') .
                    '" readonly data-format="M d, Y" name="'.$label.'" data-value="';
                $str .= (isset($object->{$label}) ? $object->{$label}[0] : '').'" value="'.(isset($object->{$label}) ? $dates[0] : '').'">';
                $str .= '</div>';
                $str .= '<div><div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
                $str .= '<input type="text" class="open-calendar-dialog" placeholder="' . JText::_('TO') .
                    '" readonly data-format="M d, Y" name="'.$label.'" data-value="';
                $str .= (isset($object->{$label}) ? $object->{$label}[1] : '').'" value="'.(isset($object->{$label}) ? $dates[1] : '').'">';
                $str .= '</div></div>';
                $str .= '<div class="ba-field-date-tags">';
                $dates = [
                    ['now', 'now', 'TODAY'],
                    ['monday this week', 'sunday this week', 'THIS_WEEK'],
                    ['saturday this week', 'sunday this week', 'WEEKEND'],
                    ['monday next week', 'sunday next week', 'NEXT_WEEK'],
                    ['first day of this month', 'last day of this month', 'THIS_MONTH']
                ];
                foreach ($dates as $array) {
                    $date = JDate::getInstance($array[0], $zone);
                    $d1 = $date->format('Y-m-d', true);
                    $d3 = $date->format('M d, Y', true);
                    $date = JDate::getInstance($array[1], $zone);
                    $d2 = $date->format('Y-m-d', true);
                    $d4 = $date->format('M d, Y', true);
                    $str .= '<span data-date="'.$d1.' - '.$d2.'" data-formated="'.$d3.' - '.$d4.'">'.JText::_($array[2]).'</span>';
                }
                $str .= '</div>';
            } else if (isset($options->items) || $appField->product) {
                $count = 0;
                $items = isset($options->items) ? $options->items : $options;
                foreach ($items as $value) {
                    $exist = true;
                    if ($appField->product) {
                        $query = $db->getQuery(true)
                            ->select('app_id')
                            ->from('#__gridbox_store_product_variations_map AS vm')
                            ->where('vm.option_key = '.$db->quote($value->key))
                            ->where('a.id = '.$id)
                            ->leftJoin('#__gridbox_pages AS p ON p.id = vm.product_id')
                            ->leftJoin('#__gridbox_app AS a ON a.id = p.app_id')
                            ->where('p.published = 1');
                        $db->setQuery($query);
                        $exist = $db->loadResult();
                    }
                    if (!$exist) {
                        continue;
                    }
                    $count++;
                    if ($appField->product && $appField->field_type == 'color') {
                        $str .= '<div class="ba-filter-color-value" style="--variation-color-value: '.$value->color.';">';
                        $str .= '<span class="ba-tooltip ba-top">'.$value->title.'</span>';
                    } else if ($appField->product && $appField->field_type == 'image') {
                        if (!self::isExternal($value->image)) {
                            $value->image = 'url('.JUri::root().$value->image.')';
                        }
                        $str .= '<div class="ba-filter-image-value" style="--variation-image-value: '.$value->image.';">';
                        $str .= '<span class="ba-tooltip ba-top">'.$value->title.'</span>';
                    } else {
                        $str .= '<div class="ba-checkbox-wrapper"><span>'.$value->title.'</span>';
                    }
                    $str .= '<label class="ba-checkbox">';
                    $str .= '<input type="checkbox" name="'.$label.'" value="'.$value->title.'"';
                    if (isset($object->{$label}) && in_array($value->title, $object->{$label})) {
                        $totalChecked++;
                        $str .= ' checked';
                        $selectedArea .= '<span class="ba-selected-filter-values" data-name="'.$label;
                        $selectedArea .= '" data-value="'.$value->title
                            .'"><span class="ba-selected-filter-value">'.$value->title;
                        $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                    }
                    $str .= '><span></span></label></div>';
                }
                if ($count > 10) {
                    $str .= '<span class="ba-show-all-filters">'.JText::_('SHOW_ALL').'</span>';
                    $str .= '<span class="ba-hide-filters">'.JText::_('HIDE').'</span>';
                }
            }
            $str .= '</div></div></div>';
        }

        if (($desktop && isset($desktop->fields->rating) && $desktop->fields->rating) || !$desktop) {
            $str .= '<div class="ba-field-filter" data-id="rating">';
            $str .= '<div class="ba-field-filter-label"><span>'.JText::_('RATING')
                .'</span><i class="ba-icons ba-icon-caret-down"></i></div>';
            $str .= '<div class="ba-field-filter-value-wrapper"><div class="ba-field-filter-value">';
            for ($i = 5; $i > 0; $i--) {
                $str .= '<div class="ba-checkbox-wrapper"><span class="ba-filter-rating">';
                $stars = '';
                for ($j = 1; $j < 6; $j++) {
                    $stars .= '<i class="ba-icons ba-icon-star'.($j <= $i ? ' active' : '').'"></i>';
                }
                $str .= $stars.'</span>';
                $str .= '<label class="ba-checkbox">';
                $str .= '<input type="checkbox" name="rating" value="'.$i.'"';
                if (isset($object->{'rating'}) && in_array($i, $object->{'rating'})) {
                    $totalChecked++;
                    $str .= ' checked';
                    $selectedArea .= '<span class="ba-selected-filter-values" data-name="rating';
                    $selectedArea .= '" data-value="'.$i.'"><span class="ba-selected-filter-value">'.$stars;
                    $selectedArea .= '</span><i class="ba-icons ba-icon-close"></i></span>';
                }
                $str .= '><span></span></label></div>';
            }
            $str .= '</div></div></div>';
        }
        $selectedArea .= '</div><div class="ba-selected-filter-values-footer">';
        $selectedArea .= '<span class="ba-selected-filter-values-remove-all"><span>'.JText::_('CANCEL_ALL').'</span></span>';
        $selectedArea .= '</div></div>';
        if (empty($str)) {
            $str = self::getEmptyList();
        } else {
            $str .= '<span class="ba-items-filter-search-button">'.JText::_('SEARCH').'</span>';
        }
        $selectedAreaWrapper = '<div class="ba-selected-values-wrapper">';
        if ($totalChecked > 0) {
            $selectedAreaWrapper .= $selectedArea;
        }
        $selectedAreaWrapper .= '</div>';
        $str = $selectedAreaWrapper.$str;

        return $str;
    }

    public static function getFieldsGroups($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('fields_groups')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $fields_groups = $db->loadResult();
        $groups = !empty($fields_groups) ? json_decode($fields_groups) : new stdClass();
        $exists = false;
        $productsGroups = ['ba-group-product-pricing', 'ba-group-product-variations',
            'ba-group-related-product', 'ba-group-product-booking',
            'ba-group-digital-product', 'ba-group-subscription-product',
            'ba-group-subscription-renewal'
        ];;
        foreach ($groups as $key => $value) {
            if (!in_array($key, $productsGroups)) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $groups->{'ba-group-1552307734035'} = new stdClass();
            $groups->{'ba-group-1552307734035'}->title = 'Group';
            $groups->{'ba-group-1552307734035'}->fields = [];
            $fields_groups = json_encode($groups);
        }

        return $fields_groups;
    }

    public static function getAppFields($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields')
            ->where('app_id = '.$id * 1)
            ->order('order_list DESC');
        $db->setQuery($query);
        $fields = $db->loadObjectList();

        return $fields;
    }

    public static function getFieldsData($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_page_fields')
            ->where('page_id = '.$id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $fields = new stdClass();
        foreach ($items as $item) {
            $fields->{$item->field_id} = $item;
        }

        return $fields;
    }

    public static function getProductImages($id, $app_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('pf.value')
            ->from('#__gridbox_page_fields AS pf')
            ->where('(pf.field_type = '.$db->quote('product-slideshow').' OR pf.field_type = '.$db->quote('product-gallery').')')
            ->where('page_id = '.$id)
            ->leftJoin('#__gridbox_fields AS f ON pf.field_id = f.id')
            ->where('f.app_id = '.$app_id)
            ->order('f.id ASC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $images = array();
        $files = self::getDesktopFieldFiles($id);
        foreach ($data as $field) {
            $values = json_decode($field->value);
            $array = array();
            foreach ($values as $value) {
                if (isset($value->unpublish) && $value->unpublish) {
                    continue;
                }
                $array[] = $value;
            }
            if (!empty($array)) {
                $images = array_slice($array, 0, 2);
                break;
            }
        }
        foreach ($images as $key => $image) {
            if (is_numeric($image->img) && isset($files->{$image->img})) {
                $file = $files->{$image->img};
                $image->img = 'components/com_gridbox/assets/uploads/app-'.$file->app_id.'/'.$file->filename;
                $images[$key] = $image;
            }
        }

        return $images;
    }

    public static function getCategoryListFields($id, $app_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('f.*, pf.value')
            ->from('#__gridbox_fields as f')
            ->where('f.app_id = '.$app_id)
            ->where('f.field_type <> '.$db->quote('field-simple-gallery'))
            ->where('f.field_type <> '.$db->quote('product-gallery'))
            ->where('f.field_type <> '.$db->quote('field-slideshow'))
            ->where('f.field_type <> '.$db->quote('field-button'))
            ->where('f.field_type <> '.$db->quote('product-slideshow'))
            ->where('f.field_type <> '.$db->quote('field-google-maps'))
            ->where('f.field_type <> '.$db->quote('field-video'))
            ->where('f.field_type <> '.$db->quote('image-field'))
            ->where('pf.page_id = '.$id)
            ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.field_id = f.id');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $not = '0';
        foreach ($data as $field) {
            $not .= ','.$field->id;
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_fields')
            ->where('id NOT IN('.$not.')')
            ->where('app_id = '.$app_id)
            ->where('field_type <> '.$db->quote('field-simple-gallery'))
            ->where('field_type <> '.$db->quote('product-gallery'))
            ->where('field_type <> '.$db->quote('field-slideshow'))
            ->where('field_type <> '.$db->quote('field-button'))
            ->where('field_type <> '.$db->quote('product-slideshow'))
            ->where('field_type <> '.$db->quote('field-google-maps'))
            ->where('field_type <> '.$db->quote('field-video'))
            ->where('field_type <> '.$db->quote('image-field'));
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $data = array_merge($data, $array);

        return $data;
    }

    public static function getPageFieldData()
    {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $input = $app->input;
        $view = $input->get('view', '', 'string');
        $option = $input->get('option', '', 'string');
        $id = $input->get('id', 0, 'int');
        $data = [];
        if ($option == 'com_gridbox' && $view == 'page') {
            $query = $db->getQuery(true)
                ->select('app_id')
                ->from('#__gridbox_pages')
                ->where('id = '.$id);
            $db->setQuery($query);
            $app_id = $db->loadResult();
            if ($app_id) {
                $query = $db->getQuery(true)
                    ->select('f.*')
                    ->from('#__gridbox_fields as f')
                    ->where('f.app_id = '.$app_id)
                    ->select('pf.value')
                    ->where('pf.page_id = '.$id)
                    ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.field_id = f.id');
                $db->setQuery($query);
                $data = $db->loadObjectList();
                $not = '0';
                foreach ($data as $field) {
                    $not .= ','.$field->id;
                }
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_fields')
                    ->where('id NOT IN('.$not.')')
                    ->where('app_id = '.$app_id);
                $db->setQuery($query);
                $array = $db->loadObjectList();
                $data = array_merge($data, $array);
            }
        }

        return $data;
    }

    public static function preparePrice($price, $thousand, $separator, $decimals, $rate = null)
    {
        $price = floatval($price);
        if (!$rate) {
            $rate = self::$store->currency->rate;
        }
        $price = round($price * $rate, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);

        return $price;
    }

    public static function getBlogPostsSortingList()
    {
        $list = array(
            'price-low-high' => JText::_('PRICE_LOW_TO_HIGH'), 'price-high-low' => JText::_('PRICE_HIGH_TO_LOW'),
            'newest' => JText::_('NEWEST'), 'highest-rated' => JText::_('HIGHEST_RATED'),
            'most-reviewed' => JText::_('MOST_REVIEWED'), 'popular' => JText::_('MOST_POPULAR'));

        return $list;
    }

    public static function getTaxCountries($setObject = null)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_countries')
            ->order('title ASC');
        $db->setQuery($query);
        $countries = $db->loadObjectList();
        if ($setObject) {
            $data = new stdClass();
            foreach ($countries as $country) {
                $data->{$country->id} = $country;
            }
            $countries = $data;
        }
        foreach ($countries as $country) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_country_states')
                ->where('country_id = '.$country->id)
                ->order('title ASC');
            $db->setQuery($query);
            $country->states = $db->loadObjectList();
        }
        
        return $countries;
    }

    public static function getCurrencySwithcer($view)
    {
        $html = new stdClass();
        $html->list = '';
        $html->active = '';
        foreach (self::$store->currencies->list as $item) {
            if ($item->code == self::$store->currency->code) {
                $html->active = '<div class="ba-currency-switcher-item"><span class="ba-currency-switcher-symbol">'.$item->symbol.'</span>';
                $html->active .= '<span class="ba-currency-switcher-code">'.$item->code.'</span><i class="zmdi zmdi-chevron-down"></i></div>';
            }
            $html->list .= '<div class="ba-currency-switcher-item" data-currency="'.$item->code;
            $html->list .= '"><span class="ba-currency-switcher-symbol">'.$item->symbol.'</span>';
            $html->list .= '<span class="ba-currency-switcher-title">'.$item->title.'</span>';
            $html->list .= '<span class="ba-currency-switcher-code">'.$item->code.'</span></div>';
        }

        return $html;
    }

    public static function getLanguageSwithcer($view)
    {
        if (JVERSION < '4.0.0' && !class_exists('ModLanguagesHelper')) {
            JLoader::register('ModLanguagesHelper', JPATH_ROOT.'/modules/mod_languages/helper.php');
        }
        $params = new Registry;
        if (JVERSION < '4.0.0') {
            $items = ModLanguagesHelper::getList($params);
        } else {
            $items = LanguagesHelper::getList($params);
        }
        $html = new stdClass();
        $html->list = '';
        $flags = JUri::root().'components/com_gridbox/assets/images/flags/';
        $html->active = '';
        $tooltip = self::$editItem->layout == 'ba-default-layout' ? 'ba-tooltip' : '';
        $currencies = [];
        foreach (self::$store->currencies->list as $currency) {
            $currencies[$currency->language] = $currency->code;
        }
        foreach ($items as $item) {
            if ($item->active) {
                $html->active = '<div class="ba-language-switcher-item"><img src="'.$flags.$item->lang_code;
                $html->active .= '.png"><span>'.$item->title.'</span><i class="zmdi zmdi-chevron-down"></i></div>';
            }
            $html->list .= '<div class="ba-language-switcher-item"><a href="'.$item->link.'"';
            $html->list .= (isset($currencies[$item->lang_code]) ? 'data-currency="'.$currencies[$item->lang_code].'"' : '').'></a><img src="';
            $html->list .= $flags.$item->lang_code.'.png"><span class="';
            $html->list .= $tooltip.' ba-bottom">'.$item->title.'</span></div>';
        }
        if (empty($items) && $view == 'gridbox') {
            $html->active = '<div class="ba-language-switcher-item"><img src="'.$flags;
            $html->active .= 'no-flag.png"><span>No language</span><i class="zmdi zmdi-chevron-down"></i></div>';
            $html->list .= '<div class="ba-language-switcher-item"><a href="#"></a><img src="';
            $html->list .= $flags.'no-flag.png"><span class="';
            $html->list .= $tooltip.' ba-bottom">No language</span></div>';
        }

        return $html;
    }

    public static function getBreadCrumbList($view)
    {
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $items = $pathway->getPathWay();
        $lang = JFactory::getLanguage();
        $menu = $app->getMenu();
        if (JLanguageMultilang::isEnabled()) {
            $home = $menu->getDefault($lang->getTag());
        } else {
            $home = $menu->getDefault();
        }
        $count = count($items);
        $list = [];
        if ($view == 'gridbox' || self::$editItem->home != 'none') {
            $item = new stdClass;
            $item->name = stripslashes(htmlspecialchars($home->title, ENT_COMPAT, 'UTF-8'));
            $item->link = JRoute::_('index.php?Itemid='.$home->id);
            $item->class = 'ba-'.self::$editItem->home.'-home-item';
            $item->home = true;
            $list[] = $item;
        }
        foreach ($items as $item) {
            $obj = new stdClass;
            $obj->name = stripslashes(htmlspecialchars($item->name, ENT_COMPAT, 'UTF-8'));
            $obj->link = JRoute::_($item->link);
            $obj->class = '';
            $obj->home = false;
            $list[] = $obj;
        }
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link)
                && $list[$i]->link === $list[$i - 1]->link) {
                unset($list[$i]);
            }
        }
        if ($view == 'gridbox') {
            $obj = new stdClass;
            $obj->name = stripslashes(htmlspecialchars('Gridbox', ENT_COMPAT, 'UTF-8'));
            $obj->link = JRoute::_('index.php?Itemid='.$home->id);
            $obj->class = '';
            $obj->home = false;
            $list[] = $obj;
            $obj = new stdClass;
            $obj->name = stripslashes(htmlspecialchars("Editor", ENT_COMPAT, 'UTF-8'));
            $obj->link = '';
            $obj->class = '';
            $obj->home = false;
            $list[] = $obj;
        }
        end($list);
        $last = key($list);
        $ulClass = self::$editItem->current ? ' class="ba-hide-current-breadcrumbs"' : '';
        $html = '<ul itemscope itemtype="https://schema.org/BreadcrumbList"'.$ulClass.'>';
        foreach ($list as $key => $item) {
            if ($view != 'gridbox' && $key === $last && self::$editItem->current) {
                continue;
            }
            $attr = $key === $last ? 'active' : '';
            if (!empty($item->class)) {
                $attr .= (!empty($attr) ? ' ' : '').$item->class;
            }
            $class = ' class="'.$attr.'"';
            $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"'.$class.'>';
            if (!empty($item->link) && $key !== $last) {
                $html .= '<a itemprop="item" href="'.$item->link.'">';
            }
            if ($item->home && ($view == 'gridbox' || self::$editItem->home == 'icon'
                || self::$editItem->home == 'title-icon')) {
                $html .= '<i class="ba-home-icon '.self::$editItem->{'home-icon'}.'"></i>';
            }
            if (!$item->home || ($item->home && ($view == 'gridbox' || self::$editItem->home != 'icon'))) {
                $html .= '<span itemprop="name">'.$item->name.'</span>';
            } else if ($item->home) {
                $html .= '<meta itemprop="name" content="'.$item->name.'">';
            }
            if ($key !== $last) {
                $html .= '<i class="ba-breadcrumbs-separator '.self::$editItem->{'separator-icon'}.'"></i>';
            }
            if (!empty($item->link) && $key !== $last) {
                $html .= '</a>';
            }
            $html .= '<meta itemprop="position" content="'.($key + 1).'"></li>';
        }
        $html .= '</ul>';
        
        return $html;
    }

    public static function increment($string)
    {
        if (preg_match('#\((\d+)\)$#', $string, $matches)) {
            $n = $matches[1] + 1;
            $string = preg_replace('#\(\d+\)$#', sprintf('(%d)', $n), $string);
        } else {
            $n = 2;
            $string .= sprintf(' (%d)', $n);
        }

        return $string;
    }

    public static function raiseError($code, $message)
    {
        switch ($code) {
            case 403:
                $app = JFactory::getApplication();
                $app->enqueueMessage($message, 'error');
                $app->setHeader('status', $code, true);
                break;
            
            default:
                throw new \Exception($message, $code);
                break;
        }
    }

    public static function getAnalitycs()
    {
        $str = '';
        $files = ['google-analitycs', 'google-tag-manager', 'yandex-metrica', 'facebook-pixel'];
        foreach ($files as $file) {
            include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/analitycs/'.$file.'.php');
            $str .= $out;
        }

        return $str;
    }
}