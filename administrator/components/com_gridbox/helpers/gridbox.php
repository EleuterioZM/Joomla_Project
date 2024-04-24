<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filter.output');
jimport('joomla.filesystem.file');
include JPATH_ROOT.'/components/com_gridbox/helpers/functions.php';
include JPATH_ROOT.'/components/com_gridbox/helpers/traits/DateTrait.php';

abstract class gridboxHelper 
{
    use DateTrait;

    public static $website;
    public static $installComments;
    public static $installReviews;
    public static $store;
    public static $storeHelper;
    public static $templates;
    public static $taxRates;
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

    public static function getBooking()
    {
        if (self::$booking) {
            return self::$booking;
        }
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/booking.php';
        self::$booking = new gridboxBooking();

        return self::$booking;
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

    public static function isExternal($link)
    {
        return (strpos($link, 'https://') !== false || strpos($link, 'http://') !== false);
    }

    public static function readFile($path)
    {
        $handle = fopen($path, "r");
        $size = filesize($path);
        $content = $size != 0 ? fread($handle, $size) : '';
        fclose($handle);

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

    public static function getGridboxAppsList($parent_id = 0)
    {
        $db = JFactory::getDbo();
        $map = gridboxHelper::getAppsMap($parent_id);
        $items = [];
        foreach ($map as $obj) {
            $item = gridboxHelper::getAppObject($obj);
            if ($item) {
                $item->order_ind = $obj->order_ind;
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function getAppObject($obj)
    {
        $db = JFactory::getDbo();
        if ($obj->type == 'app' && $obj->item_id != 0) {
            $query = $db->getQuery(true)
                ->select('id, title, type')
                ->from('#__gridbox_app')
                ->where('id = '.$obj->item_id);
            $db->setQuery($query);
            $item = $db->loadObject();
        } else if ($obj->type == 'app' && $obj->item_id == 0) {
            $item = new stdClass();
            $item->id = 0;
            $item->title = JText::_('PAGES');
            $item->type = 'single';
        } else {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_apps_groups')
                ->where('id = '.$obj->item_id);
            $db->setQuery($query);
            $item = $db->loadObject();
            $item->type = 'group';
        }

        return $item;
    }

    public static function getAppsMap($parent_id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('item_id, type, order_ind')
            ->from('#__gridbox_apps_order_map')
            ->where('parent_id = '.$parent_id)
            ->order('order_ind ASC');
        $db->setQuery($query);
        $map = $db->loadObjectList();

        return $map;
    }

    public static function getAppItemIcon($item)
    {
        if (!is_array(self::$templates)) {
            self::$templates = [];
        }
        if (!isset(self::$templates['gridbox-app-item-icon'])) {
            $path = JPATH_COMPONENT.'/views/layouts/gridbox-app-item-icon.php';
            self::$templates['gridbox-app-item-icon'] = self::readfile($path);
        }
        $user = JFactory::getUser();
        $str = self::$templates['gridbox-app-item-icon'];
        if ($item->id == 0) {
            $canDelete = false;
            $canEdit = $user->authorise('core.edit', 'com_gridbox');
        } else {
            $canEdit = $user->authorise('core.edit', 'com_gridbox.app.'.$item->id);
            $canDelete = $user->authorise('core.delete', 'com_gridbox.app.'.$item->id);
        }
        $str = str_replace('[item-type]', $item->type, $str);
        $str = str_replace('[item-id]', $item->id, $str);
        $str = str_replace('[item-icon]', self::getIcon($item), $str);
        if ($canEdit) {
            $str = str_replace('[draggable-helper]', ' draggable-helper', $str);
        } else {
            $str = str_replace('[draggable-helper]', '', $str);
        }

        return $str;
    }

    public static function checkAppsOrder()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->order('id ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $pages = new stdClass();
        $pages->id = 0;
        array_unshift($items, $pages);
        $query = $db->getQuery(true)
            ->select('MAX(order_ind)')
            ->where('parent_id = 0')
            ->from('#__gridbox_apps_order_map');
        $db->setQuery($query);
        $ind = $db->loadResult();
        $ind = $ind ? ++$ind : 1;
        foreach ($items as $item) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_apps_order_map')
                ->where('type = '.$db->quote('app'))
                ->where('item_id = '.$item->id);
            $db->setQuery($query);
            $map = $db->loadResult();
            if (!$map) {
                $obj = new stdClass();
                $obj->item_id = $item->id;
                $obj->order_ind = $ind++;
                $obj->type = 'app';
                $db->insertObject('#__gridbox_apps_order_map', $obj);
            }
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_apps_order_map')
            ->where('type = '.$db->quote('group'))
            ->where('parent_id <> 0');
        $db->setQuery($query);
        $groups = $db->loadObjectList();
        foreach ($groups as $group) {
            $group->parent_id = 0;
            $group->order_ind = $ind++;
            $db->updateObject('#__gridbox_apps_order_map', $group, 'id');
        }
    }

    public static function getProductExtraOptions($str)
    {
        $str = !empty($str) ? $str : '{}';
        $options = json_decode($str);
        $db = JFactory::getDbo();
        $extra_options = new stdClass();
        foreach ($options as $id => $option) {
            $query =  $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_products_fields')
                ->where('id = '.$option->id);
            $db->setQuery($query);
            $field = $db->loadObject();
            if (!$field) {
                continue;
            }
            $obj = new stdClass();
            $obj->title = $field->title;
            $obj->type = $field->field_type;
            $obj->required = $field->required;
            $obj->items = new stdClass();
            $items = json_decode($field->options);
            if ($field->field_type == 'file') {
                $obj->file = json_decode($field->file_options);
            }
            if (isset($option->items->{0})) {
                $obj->items->{0} = $option->items->{0};
            }
            foreach ($items as $key => $item) {
                if (isset($option->items->{$item->key})) {
                    $object = $option->items->{$item->key};
                    $object->title = $item->title;
                    $item->price = $object->price;
                    $item->default = $object->default;
                    $item->weight = isset($object->weight) ? $object->weight : '';
                    $obj->items->{$item->key} = $item;
                }
            }
            $extra_options->{$field->id} = $obj;
        }
        
        return $extra_options;
    }

    public static function getShopStatistic($date, $type)
    {
        $start = [];
        $end = [];
        $dates = [];
        if ($type == 'd') {
            $start[] = $date;
            $end[] = $date;
            $dates[] = JDate::getInstance($date)->format('M d, Y');
        } else if ($type == 'w') {
            $i = 7;
            while ($i > 0) {
                $d = date('Y-m-d', strtotime($date.' -'.$i.' days'));
                $start[] = $d;
                $end[] = $d;
                $dates[] = JDate::getInstance($d)->format('D');
                $i--;
            }
        } else if ($type == 'm') {
            $i = 1;
            while ($i <= 12) {
                $d = date('Y-m-d', strtotime($date.'-'.($i < 10 ? '0'.$i : $i).'-01'));
                $start[] = $d;
                $end[] = date('Y-m-t', strtotime($d));
                $dates[] = JDate::getInstance($d)->format('M');
                $i++;
            }
        } else if ($type == 'y') {
            $date = self::getFirstOrderDate();
            $i = date('Y', strtotime($date));
            $current = date('Y');
            for ($i; $i <= $current; $i++) {
                $d = date('Y-m-d', strtotime($i.'-01-01'));
                $start[] = $d;
                $end[] = date('Y-m-t', strtotime($i));
                $dates[] = JDate::getInstance($d)->format('Y');
                $i++;
            }
        } else if ($type == 'c') {
            $array = explode(' - ', $date);
            $start[] = $array[0];
            $end[] = $array[1];
            $d = JDate::getInstance($start[0])->format('M d, Y');
            $dates[] = $d.' - '.JDate::getInstance($end[0])->format('M d, Y');
        }
        $data = new stdClass();
        $data->total = 0;
        $data->counts = ['orders' => 0, 'completed' => 0, 'refunded' => 0];
        $data->products = [];
        $data->chart = [];
        foreach ($start as $key => $value) {
            self::getStatisticData($start[$key], $end[$key], $dates[$key], $data);
        }

        return $data;
    }

    public static function getFirstOrderDate()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('date')
            ->from('#__gridbox_store_orders')
            ->where('published = 1')
            ->order('date ASC');
        $db->setQuery($query);
        $date = $db->loadResult();

        return $date;
    }

    public static function getStatisticData($start, $end, $date, $data)
    {
        $db = JFactory::getDbo();
        $start = $db->quote($start.' 00:00:01');
        $end = $db->quote($end.' 23:59:59');
        $query = $db->getQuery(true)
            ->select('total, tax, status, id')
            ->from('#__gridbox_store_orders')
            ->where('published = 1')
            ->where('date > '.$start)
            ->where('date < '.$end);
        $db->setQuery($query);
        $orders = $db->loadObjectList();
        $chart = new stdClass();
        $chart->label = $date;
        $chart->value = 0;
        $pks = [];
        $products = [];
        foreach ($orders as $order) {
            if (isset($data->counts[$order->status])) {
                $data->counts[$order->status]++;
            }
            if ($order->status == 'completed') {
                $pks[] = $order->id;
            }
            $data->counts['orders']++;
        }
        if (!empty($pks)) {
            $str = implode(', ', $pks);
            $products = self::getStatisticProducts($db, $str);
            foreach ($products as $product) {
                if (count($data->products) < 10) {
                    $data->products[] = $product;
                }
                $data->total += $product->price;
                $chart->value += $product->price;
            }
            foreach ($pks as $pk) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gridbox_store_orders_discount')
                    ->where('order_id = '.$pk);
                $db->setQuery($query);
                $promo = $db->loadObject();
                if ($promo) {
                    $data->total -= $promo->value;
                    $chart->value -= $promo->value;
                }
            }
        }
        $data->chart[] = $chart;
    }

    public static function getStatisticProducts($db, $order_id, $i = 0, $pks = array())
    {
        $user = JFactory::getUser();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_order_products')
            ->where('order_id IN ('.$order_id.')');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $object = new stdClass();
        foreach ($items as $item) {
            $id = !empty($item->variation) ? $item->variation : $item->product_id;
            $item->quantity = $item->quantity * 1;
            if ($item->sale_price != '') {
                $item->price = $item->sale_price;
            }
            if (!isset($object->{$id})) {
                $object->{$id} = $item;
            } else {
                $object->{$id}->quantity += $item->quantity;
                $object->{$id}->price += $item->price;
            }
        }
        $products = array();
        foreach ($object as $obj) {
            $products[] = $obj;
        }
        uasort($products, function($a, $b){
            if ($a->price == $b->price) {
                return 0;
            }
            return ($a->price < $b->price) ? 1 : -1;
        });
        foreach ($products as $product) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_store_order_product_variations')
                ->where('product_id = '.$product->id);
            $db->setQuery($query);
            $variations = $db->loadObjectList();
            $info = array();
            foreach ($variations as $variation) {
                $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
            }
            $product->info = implode('/', $info);
            if (!empty($product->image) && strpos($product->image, 'https://') === false
                && strpos($product->image, 'http://') === false) {
                $product->image = JUri::root().$product->image;
            }
            $query = $db->getQuery(true)
                ->select('p.id')
                ->from('#__gridbox_pages AS p')
                ->where('d.product_id = '.$product->product_id)
                ->leftJoin('#__gridbox_store_product_data AS d ON d.product_id = p.id');
            $db->setQuery($query);
            $product->product_id = $db->loadResult();
            $product->link = '';
            if ($product->product_id && $user->authorise('core.edit', 'com_gridbox.page.'.$product->product_id)) {
                $product->link = 'index.php?option=com_gridbox&task=gridbox.edit&id='.$product->product_id;
            }
        }

        return $products;
    }

    public static function getStatuses()
    {
        $data = new stdClass();
        $data->undefined = new stdClass();
        $data->undefined->title = 'Undefined';
        $data->undefined->color = '#f10000';
        foreach (self::$store->statuses as $status) {
            $data->{$status->key} = $status;
        }

        return $data;
    }

    public static function getProductCategoryId($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('page_category')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $category = $db->loadResult();
        $array = array($category);
        $array2 = self::getProductCategoryIdPath($category);
        $result = array_merge($array, $array2);
        
        return $result;
    }

    public static function getProductCategoryIdPath($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('parent')
            ->from('#__gridbox_categories')
            ->where('`id` = '.$id * 1);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $array1 = array($obj->parent);
        if ($obj->parent != 0) {
            $array2 = self::getProductCategoryIdPath($obj->parent);
        } else {
            $array2 = array();
        }
        $result = array_merge($array1, $array2);
        
        return $result;
    }

    public static function getUserGroups()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__usergroups'))
            ->order('lft ASC');
        $db->setQuery($query);
        $groups = $db->loadObjectList();
        foreach ($groups as $group) {
            $group->level = self::getUserGroupLevel($group->parent_id);
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

    public static function getUsers()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('u.id, u.name, u.username')
            ->from('`#__users` AS u');
        $db->setQuery($query);
        $users = $db->loadObjectList();
        foreach ($users as $user) {
            $query = $db->getQuery(true)
                ->select('g.title')
                ->from('#__user_usergroup_map AS m')
                ->where('m.user_id = '.$user->id)
                ->leftJoin('#__usergroups AS g ON '.$db->quoteName('g.id').' = '.$db->quoteName('m.group_id'));
            $db->setQuery($query);
            $user->groups = $db->loadObjectList();
        }
        
        return $users;
    }

    public static function checkCommentUserBanStatus($value, $table, $key)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($table)
            ->where($key.' = '.$db->quote($value));
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    public static function checkSystemApp($type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_app')
            ->where('type = '.$db->quote('system_apps'))
            ->where('title = '.$db->quote($type));
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }

    public static function checkSubscriptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__gridbox_store_subscriptions');
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }

    public static function deleteComment($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_comments')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_attachments')
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        foreach ($files as $file) {
            self::removeTmpAttachment($file->id, $file->filename);
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_comments_likes_map')
            ->where('comment_id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_comments')
            ->where('parent = '.$id);
        $db->setQuery($query);
        $childs = $db->loadObjectList();
        foreach ($childs as $key => $child) {
            self::deleteComment($child->id);
        }
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

    public static function deleteReview($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_reviews')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_reviews_attachments')
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        foreach ($files as $file) {
            self::removeTmpReviewsAttacment($file->id, $file->filename);
        }
        $query = $db->getQuery(true)
            ->delete('#__gridbox_reviews_likes_map')
            ->where('comment_id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_reviews')
            ->where('parent = '.$id);
        $db->setQuery($query);
        $childs = $db->loadObjectList();
        foreach ($childs as $key => $child) {
            self::deleteReview($child->id);
        }
    }

    public static function removeTmpReviewsAttacment($id, $filename)
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
        foreach ($files as $key => $file) {
            $file->link = $dir.$file->filename;
        }

        return $files;
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
        foreach ($files as $key => $file) {
            $file->link = $dir.$file->filename;
        }

        return $files;
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

    public static function getReviewsGravatarImage($email)
    {
        $avatar = JUri::root().'components/com_gridbox/assets/images/default-user.png';
        if (self::$website->reviews_enable_gravatar == 1) {
            $hash = md5(strtolower(trim($email)));
            $avatar = "https://www.gravatar.com/avatar/".$hash."?d=".$avatar."&s=50";
        }

        return $avatar;
    }

    public static function getUnreadCount($table, $where = '', $isBooking = false)
    {
        $db = JFactory::getDbo();
        $table .= ' AS t';
        $query = $db->getQuery(true)
            ->select('COUNT(t.id)')
            ->from($table)
            ->where('t.unread = 1');
        if (!empty($where)) {
            $query->where($where);
        }
        if ($isBooking) {
            $query->leftJoin('#__gridbox_store_orders AS o ON o.id = t.order_id')
                ->where('o.published = 1');
        }
        $db->setQuery($query);
        $count  = $db->loadResult();

        return $count;
    }

    public static function getEditorLink($type = '')
    {
        $user = JFactory::getUser();
        $link = JUri::root().'index.php?option=com_gridbox&view=editor&tmpl=component&name=';
        $link .= urlencode($user->username).'&pwd='.urlencode($user->password);
        if ($type == 'products') {
            $link .= '&product_type={product_type}';
        }

        return $link;
    }

    public static function assetsCheckPermission($id, $type, $action, $name = '')
    {
        $assets = new gridboxAssetsHelper($id, $type);

        return $assets->checkPermission($action, $name);
    }

    public static function checkUserEditLevel($id = '', $type = '')
    {
        $action = 'com_gridbox';
        if (!empty($id)) {
            $action .= '.'.$type.'.'.$id;
        }
        if (!JFactory::getUser()->authorise('core.edit', $action)) {
            exit;
        }
    }

    public static function movePageFields($id, $app_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result != $app_id) {
            self::deletePageFields([$id]);
        }

        return $result;
    }

    public static function afterDeleteAction($cid)
    {
        gridboxHelper::deletePageCss($cid);
        gridboxHelper::deleteTagsLink($cid);
        gridboxHelper::deletePageFields($cid);
        gridboxHelper::deleteProductData($cid);
        gridboxHelper::triggerEvent('onGidboxPagesAfterDelete', [$cid], 'finder');
    }

    public static function deleteProductData($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_product_variations_map')
                ->where('product_id = '.$id);
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->delete('#__gridbox_store_product_data')
                ->where('product_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function deletePageFields($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true)
                ->delete('#__gridbox_page_fields')
                ->where('page_id = '.$id);
            $db->setQuery($query)
                ->execute();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_fields_desktop_files')
                ->where('page_id = '.$id);
            $db->setQuery($query);
            $files = $db->loadObjectList();
            $desktopArray = [];
            foreach ($files as $file) {
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
            $query = $db->getQuery(true)
                ->delete('#__gridbox_category_page_map')
                ->where('page_id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public static function getOptions($type)
    {
        $json = self::readFile(JPATH_ROOT.'/components/com_gridbox/libraries/json/'.$type.'.json');
        
        return json_decode($json);
    }

    public static function checkInstalledBlog($type = '')
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

    public static function setGridboxFilters($ordering, $direction, $context)
    {
        if ($ordering == 'order_list') {
            $direction = 'ASC';
        }
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__gridbox_filter_state')
            ->where('name = '.$db->quote($context.'.list.ordering').' OR name = '.$db->quote($context.'.list.direction'))
            ->where('user = '.$user->id);
        $db->setQuery($query);
        $array = $db->loadObjectList();
        if (!empty($array)) {
            foreach ($array as $obj) {
                if ($obj->name == $context.'.list.ordering') {
                    $obj->value = $ordering;
                } else {
                    $obj->value = $direction;
                }
                $db->updateObject('#__gridbox_filter_state', $obj, 'id');
            }
        } else {
            $obj = new stdClass();
            $obj->user = $user->id;
            $obj->name = $context.'.list.ordering';
            $obj->value = $ordering;
            $db->insertObject('#__gridbox_filter_state', $obj);
            $obj->name = $context.'.list.direction';
            $obj->value = $direction;
            $db->insertObject('#__gridbox_filter_state', $obj);
        }
    }

    public static function getGridboxFilters($context)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $query = $db->getQuery(true)
            ->select('id, name, value')
            ->from('#__gridbox_filter_state')
            ->where('name = '.$db->quote($context.'.list.ordering').' OR name = '.$db->quote($context.'.list.direction'))
            ->where('user = '.$user->id);
        $db->setQuery($query);
        $array = $db->loadObjectList();

        return $array;
    }

    public static function getGridboxLanguage()
    {
        $language = JFactory::getLanguage();
        $result = ['EDIT_NOT_PERMITTED' => JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
            'CREATE_NOT_PERMITTED' => JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'),
            'SAVE_SUCCESS' => JText::_('JLIB_APPLICATION_SAVE_SUCCESS'), 'TITLE' => JText::_('JGLOBAL_TITLE')];
        $date = JDate::getInstance('2023-01-01');
        for ($i = 1; $i <= 12; $i++) {
            if ($i != 1) {
                $date->modify('+1 month');
            }
            $result['SHORT_M'.$i] = $date->format('M');
        }
        $path = JPATH_ROOT.'/administrator/components/com_gridbox/language/admin/en-GB/en-GB.com_gridbox.ini';
        if (JFile::exists($path)) {
            $contents = self::readFile($path);
            $contents = str_replace('_QQ_', '"\""', $contents);
            $data = parse_ini_string($contents);
            foreach ($data as $ind => $value) {
                $result[$ind] = JText::_($ind);
            }
        }
        
        $data = 'var gridboxLanguage = '.json_encode($result).';';

        return $data;
    }
    
    public static function getThemes()
    {
        $url = 'http://www.balbooa.com/updates/gridbox/themes/themes.xml';
        $curl = self::getContentsCurl($url);
        $xml = simplexml_load_string($curl);
        $themes = array();
        foreach ($xml->themes->theme as $theme) {
            $obj = new stdClass();
            $obj->id = trim((string)$theme->id);
            $obj->title = trim((string)$theme->title);
            $obj->image = trim((string)$theme->image);
            $themes[] = $obj;
        }

        return $themes;
    }

    public static function getTemplate()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__template_styles')
            ->where('`template` = '.$db->Quote('gridbox'))
            ->where('`client_id` = 0');
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function deleteTagsLink($pages)
    {
        $db = JFactory::getDbo();
        foreach ($pages as $value) {
            $query = $db->getQuery(true)
                ->select('tag_id')
                ->from('#__gridbox_tags_map')
                ->where('`page_id` = '. $value);
            $db->setQuery($query);
            $tags = $db->loadObjectList();
            $query = $db->getQuery(true)
                ->delete('#__gridbox_tags_map')
                ->where('`page_id` = '. $value);
            $db->setQuery($query)
                ->execute();
            if (!empty($tags) && is_array($tags)) {
                foreach ($tags as $tag) {
                    $query = $db->getQuery(true)
                        ->select('COUNT(id)')
                        ->from('#__gridbox_tags_map')
                        ->where('`tag_id` = '. $tag->tag_id);
                    $db->setQuery($query);
                    $count = $db->loadResult();
                    if (empty($count)) {
                        $query = $db->getQuery(true)
                            ->delete('#__gridbox_tags')
                            ->where('`id` = '. $tag->tag_id);
                        $db->setQuery($query)
                            ->execute();
                    }
                }
            }
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_comments')
                ->where('`page_id` = '. $value);
            $db->setQuery($query);
            $comments = $db->loadObjectList();
            foreach ($comments as $comment) {
                self::deleteComment($comment->id);
            }
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_reviews')
                ->where('`page_id` = '. $value);
            $db->setQuery($query);
            $reviews = $db->loadObjectList();
            foreach ($reviews as $review) {
                self::deleteReview($review->id);
            }
        }
    }

    public static function findGridboxLinks($html, $items, $apps, $categories, $pages)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        foreach ($items as $key => $item) {
            if ($item->type == 'logo' || $item->type == 'image' || $item->type == 'icon' || $item->type == 'button') {
                $item->link->link = self::importGridboxLinks($item->link->link, $apps, $categories, $pages);
            } else if ($item->type == 'column' && isset($item->link)) {
                $item->link->link = self::importGridboxLinks($item->link->link, $apps, $categories, $pages);
            } else if ($item->type == 'slideshow' || $item->type == 'slideset' || $item->type == 'carousel') {
                foreach ($item->desktop->slides as $slide) {
                    if (isset($slide->link) && !empty($slide->link)) {
                        $slide->link = self::importGridboxLinks($slide->link, $apps, $categories, $pages);
                    }
                }
            } else if ($item->type == 'content-slider') {
                foreach ($item->slides as $slide) {
                    $slide->link->href = self::importGridboxLinks($slide->link->href, $apps, $categories, $pages);
                }
            } else if ($item->type == 'icon-list') {
                foreach ($item->list as $listValue) {
                    $listValue->link = self::importGridboxLinks($listValue->link, $apps, $categories, $pages);
                }
            }
        }
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $dom = phpQuery::newDocument($html);
        foreach (pq('.ba-item-text .content-text a[href]') as $value) {
            $link = pq($value)->attr('href');
            $link = self::importGridboxLinks($link, $apps, $categories, $pages);
            pq($value)->attr('href', $link);
        }
        $obj = new stdClass();
        $obj->html = $dom->htmlOuter();
        $obj->items = $items;

        return $obj;
    }

    public static function importGridboxLinks($link, $apps, $categories, $pages)
    {
        if (strpos($link, 'option=com_gridbox')) {
            $link = str_replace('index.php?', '', $link);
            parse_str($link, $array);
            if (isset($array['app']) && isset($apps[$array['app']])) {
                $array['app'] = $apps[$array['app']];
            }
            if (isset($array['blog']) && isset($apps[$array['blog']])) {
                $array['blog'] = $apps[$array['blog']];
            }
            if ($array['view'] == 'page') {
                if (isset($array['category']) && isset($categories[$array['category']])) {
                    $array['category'] = $categories[$array['category']];
                }
                if (isset($array['id']) && isset($pages[$array['id']])) {
                    $array['id'] = $pages[$array['id']];
                }
            } else if ($array['view'] == 'blog') {
                if (isset($array['id']) && isset($categories[$array['id']])) {
                    $array['id'] = $categories[$array['id']];
                }
            }
            $data = array();
            foreach ($array as $key => $value) {
                $data[] = $key.'='.$value;
            }
            $link = implode('&', $data);
            $link = 'index.php?'.$link;
        }

        return $link;
    }

    public static function importBlogContent($obj, $apps, $categories)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $dom = phpQuery::newDocument($obj->html);
        foreach (pq('.ba-item-event-calendar, .ba-item-fields-filter, .ba-item-google-maps-places') as $value) {
            $id = pq($value)->attr('id');
            if (!empty($obj->items->{$id}->app) && isset($apps[$obj->items->{$id}->app])) {
                $obj->items->{$id}->app = $apps[$obj->items->{$id}->app];
            }
        }
        if (!self::$installComments) {
            foreach (pq('.ba-item-comments-box') as $key => $value) {
                self::$installComments = true;
                break;
            }
        }
        if (!self::$installReviews) {
            foreach (pq('.ba-item-reviews') as $key => $value) {
                self::$installReviews = true;
                break;
            }
        }
        $tags = pq('.ba-item-tags');
        foreach ($tags as $value) {
            $app = pq($value)->attr('data-app');
            $cat = pq($value)->attr('data-category');
            $id = pq($value)->attr('id');
            pq($value)->attr('data-app', $apps[$app]);
            $item = $obj->items->{$id};
            $item->app = $apps[$app];
            if (!empty($cat)) {
                $catList = explode(',', $cat);
                $object = new stdClass();
                foreach ($catList as $category) {
                    if (!isset($categories[$category])) {
                        continue;
                    }
                    $catObj = new stdClass();
                    $catObj->id = $categories[$category];
                    $catObj->title = $item->categories->{$category}->title;
                    $object->{$catObj->id} = $catObj;
                    $category = $categories[$category];
                }
                $item->categories = $object;
                $cat = implode(',', $catList);
                pq($value)->attr('data-category', $cat);
            }
        }
        $itemCategories = pq('.ba-item-categories');
        foreach ($itemCategories as $value) {
            $app = pq($value)->attr('data-app');
            $id = pq($value)->attr('id');
            pq($value)->attr('data-app', $apps[$app]);
            $obj->items->{$id}->app = $apps[$app];
        }
        $recent = pq('.ba-item-recent-posts');
        foreach ($recent as $value) {
            $app = pq($value)->attr('data-app');
            $cat = pq($value)->attr('data-category');
            $id = pq($value)->attr('id');
            pq($value)->attr('data-app', $apps[$app]);
            $obj->items->{$id}->app = $apps[$app];
            $item = $obj->items->{$id};
            $item->app = $apps[$app];
            if (!empty($cat)) {
                $catList = explode(',', $cat);
                $object = new stdClass();
                $newCats = array();
                foreach ($catList as $category) {
                    if (!isset($categories[$category])) {
                        continue;
                    }
                    $catObj = new stdClass();
                    $catObj->id = $categories[$category];
                    $catObj->title = $item->categories->{$category}->title;
                    $object->{$catObj->id} = $catObj;
                    $newCats[] = $categories[$category];
                }
                $item->categories = $object;
                $cat = implode(',', $newCats);
                pq($value)->attr('data-category', $cat);
            }
        }
        foreach (pq('.ba-item-recent-posts-slider') as $value) {
            $id = pq($value)->attr('id');
            $item = $obj->items->{$id};
            $item->app = $apps[$item->app];
            $object = new stdClass();
            foreach ($item->categories as $key => $category) {
                if (!isset($categories[$key])) {
                    continue;
                }
                $category->id = $categories[$key];
                $object->{$key} = $category;
            }
            $item->categories = $object;
        }
        $related = pq('.ba-item-related-posts');
        foreach ($related as $value) {
            $app = pq($value)->attr('data-app');
            $id = pq($value)->attr('id');
            pq($value)->attr('data-app', $apps[$app]);
        }
        $obj->html = $dom->htmlOuter();

        return $obj;

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
    
    public static function getLanguagesList()
    {
        $url = 'http://www.balbooa.com/updates/gridbox/language/language.xml';
        $curl = self::getContentsCurl($url);
        $xml = simplexml_load_string($curl);
        $array = array();
        if (isset($xml->languages)) {
            foreach ($xml->languages->language as $language) {
                $obj = new StdClass();
                $obj->flag = 'http://www.balbooa.com/updates/gridbox/language/flags/'.trim((string)$language->flag);
                $obj->title = trim((string)$language->title);
                $obj->code = trim((string)$language->tag);
                $obj->url = trim((string)$language->url);
                $array[] = $obj;
            }
        }

        return $array;
    }

    public static function deletePageCss($cid)
    {
        foreach ($cid as $id) {
            $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/style-'.$id.'.css';
            JFile::delete($file);
        }
    }

    public static function deleteThemeCss($cid)
    {
        foreach ($cid as $id) {
            $file = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$id.'.css';
            self::deleteFile($file);
            $file = JPATH_ROOT. '/templates/gridbox/css/storage/style-'.$id.'.css';
            self::deleteFile($file);
            $file = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-'.$id.'.js';
            self::deleteFile($file);
        }
    }

    public static function deleteFile($file)
    {
        if (JFile::exists($file)) {
            JFile::delete($file);
        }
    }

    public static function getApps()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, alias, theme, type, published, access, language, image, meta_title, schema_markup,
            share_image, share_title, share_description, meta_description, meta_keywords, description, robots,
            sitemap_include, changefreq, priority')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->order('id ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public static function saveCodeEditor($obj, $id)
    {
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$id.'.css';
        JFile::write($file, (string)$obj->css);
        $file = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-'.$id.'.js';
        JFile::write($file, (string)$obj->js);
    }

    public static function copyThemeFiles($pk, $id)
    {
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$pk.'.css';
        if (JFile::exists($file)) {
            $target = JPATH_ROOT. '/templates/gridbox/css/storage/code-editor-'.$id.'.css';
            JFile::copy($file, $target);
        }
        $file = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-'.$pk.'.js';
        if (JFile::exists($file)) {
            $target = JPATH_ROOT. '/templates/gridbox/js/storage/code-editor-' . $id . '.js';
            JFile::copy($file, $target);
        }
    }

    public static function copyCss($pk, $id)
    {
        $file = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/style-' . $pk . '.css';
        $target = JPATH_ROOT. '/components/com_gridbox/assets/css/storage/style-' . $id . '.css';
        JFile::copy($file, $target);
    }

    public static function replace($str)
    {

        $str = mb_strtolower($str, 'utf-8');
        $search = ['?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '***91;',
            '***93;', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=',
            '|', '"', '\'', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ъ',
            'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я'];
        $replace = ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'j', 'i', 'e', '-', 'zh', 'ts',
            'ch', 'sh', 'shch', '', 'yu', 'ya'];
        $str = str_replace($search, $replace, $str);
        $str = trim($str);
        $str = preg_replace("/_{2,}/", "-", $str);

        return $str;
    }
    
    public static function checkActive($app)
    {
        $active = '';
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $view = $input->get('view', 'pages', 'string');
        $type = gettype($app);
        $appslist = ['pages', 'apps', 'single'];
        $store = ['paymentmethods', 'shipping', 'storesettings', 'promocodes',
            'productoptions', 'orders', 'subscriptions', 'bookingcalendar'];
        $viewFlag = $type == 'string' && ($app == $view || ($app == 'appslist' && in_array($view, $appslist)));
        $storeFlag = $type == 'string' && $app == 'store' && in_array($view, $store);
        if ($viewFlag || $storeFlag || ($type != 'string' && $app->id == $id)) {
            $active = 'active';
        }

        return $active;
    }

    public static function getUrl($app)
    {
        if ($app->type == 'group') {
            $url = '#';
        } else if ($app->id != 0) {
            $view = $app->type == 'single' ? 'single' : 'apps';
            $url = 'index.php?option=com_gridbox&view='.$view.'&id='.$app->id;
        } else {
            $url = 'index.php?option=com_gridbox&view=pages';
        }

        return $url;
    }

    public static function setAppLicenseBalbooa($data)
    {
        $balbooa = self::getGridboxApi('balbooa_activation');
        if (!$balbooa->key) {
            $balbooa->key = self::checkGridboxState();
        }
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        $balbooa->key = json_encode($balbooa->key);
        $db = JFactory::getDbo();
        $db->updateObject('#__gridbox_api', $balbooa, 'id');
    }

    public static function getIcon($app)
    {
        $type = $app->type != 'system_apps' ? $app->type : $app->title;
        switch ($type) {
            case 'group':
                return 'zmdi zmdi-folder';
                break;
            case 'blog':
                return 'zmdi zmdi-format-color-text';
                break;
            case 'blank':
                return 'zmdi zmdi-crop-free';
                break;
            case 'products':
                return 'zmdi zmdi-shopping-basket';
                break;
            case 'booking':
                return 'zmdi zmdi-calendar-check';
                break;
            case 'portfolio':
                return 'zmdi zmdi-camera';
                break;
            case 'hotel-rooms':
                return 'zmdi zmdi-hotel';
                break;
            case 'comments':
                return 'zmdi zmdi-comment-more';
                break;
            case 'reviews':
                return 'zmdi zmdi-ticket-star';
                break;
            case 'photo-editor':
                return 'zmdi zmdi-camera-alt';
                break;
            case 'code-editor':
                return 'zmdi zmdi-code-setting';
                break;
            case 'performance':
                return 'zmdi zmdi-time-restore-setting';
                break;
            case 'preloader':
                return 'zmdi zmdi-spinner';
                break;
            case 'canonical':
                return 'zmdi zmdi-link';
                break;
            case 'sitemap':
                return 'zmdi zmdi-device-hub';
                break;
            default:
                return 'zmdi zmdi-file';
                break;
        }
    }
    
    public static function ajaxReload($text, $type = '')
    {
        echo $type.JText::_($text);
        exit;
    }

    public static function stringURLSafe($string, $language = '')
    {
        if (JFactory::getConfig()->get('unicodeslugs') == 1) {
            $output = JFilterOutput::stringURLUnicodeSlug($string);
        } else {
            if ($language === '*' || $language === '') {
                $languageParams = JComponentHelper::getParams('com_languages');
                $language = $languageParams->get('site');
            }
            $output = JFilterOutput::stringURLSafe($string, $language);
        }

        return $output;
    }

    public static function getAlias($alias, $table, $item_id = 0, $cell = 'alias')
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
            ->where($cell.' = ' .$db->quote($alias))
            ->where('id <> ' .$db->quote($item_id));
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!empty($id)) {
            $alias = self::increment($alias);
            $alias = self::getAlias($alias, $table, $item_id, $cell);
        }
        
        return $alias;
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

    public static function getCategories($map)
    {
        $array = array();
        if (!empty($map)) {
            $db = JFactory::getDbo();
            $pks = implode(', ', $map);
            $query = $db->getQuery(true)
                ->select('c.id, c.title, c.image')
                ->from('#__gridbox_categories AS c')
                ->leftJoin('#__gridbox_app AS a ON c.app_id = a.id')
                ->where('a.type = '.$db->quote('products'))
                ->where('c.id IN ('.$pks.')');
            $db->setQuery($query);
            $array = $db->loadObjectList();
        }

        return $array;
    }

    public static function preparePrice($price, $symbol = null, $position = null)
    {
        if ($symbol == null) {
            $symbol = self::$store->currency->symbol;
            $position = self::$store->currency->position;
        }
        $decimals = self::$store->currency->decimals;
        $separator = self::$store->currency->separator;
        $thousand = self::$store->currency->thousand;
        $price = round(floatval($price), $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);
        if ($position == '') {
            $value = $symbol.' '.$price;
        } else {
            $value = $price.' '.$symbol;
        }

        return $value;
    }

    public static function prepareGridbox()
    {
        $db = JFactory::getDbo();
        $balbooa = self::getGridboxApi('balbooa');
        if (!$balbooa) {
            $obj = new stdClass();
            $obj->key = '{}';
            $obj->service = 'balbooa';
            $db->insertObject('#__gridbox_api', $obj);
        }
        $balbooa = self::getGridboxApi('balbooa_activation');
        if (!$balbooa) {
            $obj = new stdClass();
            $obj->key = self::checkGridboxState();
            $obj->service = 'balbooa_activation';
            $db->insertObject('#__gridbox_api', $obj);
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_website')
            ->where('1');
        $db->setQuery($query);
        $website = $db->loadObject();
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
        $array = ['service = '.$db->quote('google_maps'), 'service = '.$db->quote('openweathermap'), 'service = '.$db->quote('yandex_maps')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('('.implode(' OR ', $array).')')
            ->where('type = '.$db->quote(''));
        $db->setQuery($query);
        $array = $db->loadObjectList();
        foreach ($array as $obj) {
            $obj->type = 'integration';
            $obj->title = $obj->service == 'google_maps' ? 'Google Maps' : ($obj->service == 'yandex_maps' ? 'Yandex Maps' : 'OpenWeatherMap');
            $db->updateObject('#__gridbox_api', $obj, 'id');
        }
        $array = ['service = '.$db->quote('google_login'), 'service = '.$db->quote('facebook_login'), 'service = '.$db->quote('vk_login')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('('.implode(' OR ', $array).')');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        if (empty($array)) {
            $key = $website->comments_google_login_key != '' ? $website->comments_google_login_key : $website->reviews_google_login_key;
            self::setIntegration($db, 'google_login', 'Google Login', $key);
            $key = $website->comments_facebook_login_key != '' ? $website->comments_facebook_login_key : $website->reviews_facebook_login_key;
            self::setIntegration($db, 'facebook_login', 'Facebook Login', $key);
            $key = $website->comments_vk_login_key != '' ? $website->comments_vk_login_key : $website->reviews_vk_login_key;
            self::setIntegration($db, 'vk_login', 'VK Login', $key);
        }
        $array = ['service = '.$db->quote('hypercomments'), 'service = '.$db->quote('disqus'),
            'service = '.$db->quote('facebook_comments'), 'service = '.$db->quote('vk_comments')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('('.implode(' OR ', $array).')');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        if (empty($array)) {
            $key = self::getCommentsKey($db, 'ba-item-facebook-comments', 'facebook-comments', 'app_id');
            self::setIntegration($db, 'facebook_comments', 'Facebook Comments', $key);
            $key = self::getCommentsKey($db, 'ba-item-hypercomments', 'hypercomments', 'app_id');
            self::setIntegration($db, 'hypercomments', 'Hypercomments', $key);
            $key = self::getCommentsKey($db, 'ba-item-vk-comments', 'vk-comments', 'app_id');
            self::setIntegration($db, 'vk_comments', 'VK Comments', $key);
            $key = self::getCommentsKey($db, 'ba-item-disqus', 'disqus', 'subdomen');
            self::setIntegration($db, 'disqus', 'Disqus', $key);
        }
        include JPATH_ROOT.'/components/com_gridbox/helpers/store.php';
        self::$storeHelper = new store();
        self::$store = self::$storeHelper->getSettings();
        $rates = new stdClass();
        $rates->categories = [];
        $rates->empty = [];
        foreach (self::$store->tax->rates as $key => $rate) {
            $rate->key = $key;
            if (!empty($rate->categories)) {
                $rates->categories[] = $rate;
            } else {
                $rates->empty[] = $rate;
            }
        }
        self::$taxRates = $rates;
        self::checkAppsOrder();
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

    public static function checkPromoCode($promo, $product)
    {
        $valid = false;
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

    public static function prepareProductPrices($id, $price, $sale_price, $variation = '', $qty = 1)
    {
        $currency = self::$store->currency;
        $sales = self::$storeHelper->sales;
        $prices = new stdClass();
        $prices->price = $price * $qty;
        $prices->regular = self::prepareCartPrice($price * $qty, $currency->thousand, $currency->separator, $currency->decimals);
        $prices->sale = '';
        $prices->sale_price = $sale_price;
        $date = date('Y-m-d H:i:s');
        foreach ($sales as $sale) {
            if ($sale_price !== '' || empty($sale->discount)) {
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
            $prices->sale_price = $sale_price * $qty;
            $prices->sale = self::prepareCartPrice($sale_price * $qty, $currency->thousand, $currency->separator, $currency->decimals);
        }

        return $prices;
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

    public static function prepareCartPrice($price, $thousand, $separator, $decimals, $rate = null)
    {
        if (!$rate) {
            $rate = self::$store->currency->rate;
        }
        $price = round($price * $rate, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);

        return $price;
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

    public static function getCommentsKey($db, $plugin, $type, $ind)
    {
        $key = '';
        $items = self::searchComents($db, '#__gridbox_app', 'page_items', 'page_layout', $plugin);
        if (empty($items)) {
            $items = self::searchComents($db, '#__gridbox_pages', 'style', 'params', $plugin);
        }
        if (!empty($items)) {
            $data = json_decode($items);
            foreach ($data as $obj) {
                if ($obj->type != $type) {
                    continue;
                }
                $key = $obj->{$ind};
                break;
            }
        }

        return $key;
    }

    public static function searchComents($db, $table, $select, $column, $search)
    {
        $query = $db->getQuery(true)
            ->select($select)
            ->from($table)
            ->where($column.' LIKE '.$db->quote('%'.$search.'%'));
        $db->setQuery($query);
        $items = $db->loadResult();

        return $items;
    }

    public static function setIntegration($db, $service, $title, $key)
    {
        $obj = new stdClass();
        $obj->type = 'integration';
        $obj->service = $service;
        $obj->title = $title;
        $obj->key = $key;
        $db->insertObject('#__gridbox_api', $obj);
    }

    public static function getNewPageAlias($type, $orig)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_pages')
            ->where('`page_alias` = '.$db->quote($type));
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!empty($id)) {
            if (empty($orig)) {
                $type = self::increment($type);
            } else {
                $type = self::increment($orig);
            }
            $orig = $type;
            $type = self::stringURLSafe($type);
            if (empty($type)) {
                $type = $orig;
                $type = self::replace($type);
                $type = JFilterOutput::stringURLSafe($type);
            }
            if (empty($type)) {
                $type = date('Y-m-d-H-i-s');
            }
            $type = self::getNewPageAlias($type, $orig);
        }

        return $type;
    }

    public static function setAppLicense($data)
    {
        $db = JFactory::getDbo();
        $balbooa = self::getGridboxApi('balbooa');
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        $balbooa->key = json_encode($balbooa->key);
        $db->updateObject('#__gridbox_api', $balbooa, 'id');
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
    
    public static function getContentsCurl($url)
    {
        $http = JHttpFactory::getHttp();
        $body = '';
        $host = 'balbooa.com';
        if($socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
            $data = $http->get($url);
            $body = $data->body;
            fclose($socket);
        }
        
        return $body;
    }
    
    public static function getSystemPlugin()
    {
        $flag = JPluginHelper::isEnabled('system', 'gridbox');
        
        return $flag;
    }
    
    public static function getGlobal($body, $array)
    {
        $regex = '/\[global item=+(.*?)\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        $db = JFactory::getDBO();
        foreach ($matches as $index => $match) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__gridbox_library')
                ->where('`global_item` = ' . $db->quote($match[1]));
            $db->setQuery($query);
            $result = $db->loadObject();
            $array[] = $result;
        }
        
        return $array;
    }

    public static function getBaforms($body, $array)
    {
        $regex = '/\[forms ID=+(.*?)\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        $db = JFactory::getDbo();
        $query = 'SHOW TABLES LIKE '.$db->quote('%baforms_forms');
        $db->setQuery($query);
        $result = $db->loadResult();
        if (!empty($result)) {
            foreach ($matches as $match) {
                if (!array_key_exists($match[1], $array)) {
                    $id = $match[1];
                    $obj = new StdClass();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__baforms_forms')
                        ->where('`id` = ' .$db->quote($id));
                    $db->setQuery($query);
                    $obj->forms = $db->loadObject();
                    if (empty($obj)) {
                        continue;
                    }
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__baforms_items')
                        ->where('`form_id` = ' .$db->quote($id));
                    $db->setQuery($query);
                    $obj->items = $db->loadObjectList();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__baforms_columns')
                        ->where('`form_id` = ' .$db->quote($id));
                    $db->setQuery($query);
                    $obj->columns = $db->loadObjectList();
                    $query = 'SHOW TABLES LIKE '.$db->quote('%baforms_forms_settings');
                    $db->setQuery($query);
                    $settings = $db->loadResult();
                    if (!empty($settings)) {
                        $query = $db->getQuery(true)
                            ->select('*')
                            ->from('#__baforms_forms_settings')
                            ->where('`form_id` = ' .$db->quote($id));
                        $db->setQuery($query);
                        $obj->settings = $db->loadObjectList();
                        $query = $db->getQuery(true)
                            ->select('*')
                            ->from('#__baforms_pages')
                            ->where('`form_id` = ' .$db->quote($id));
                        $db->setQuery($query);
                        $obj->pages = $db->loadObjectList();
                    }
                    $array[$id] = $obj;
                }
            }
        }
        
        return $array;
    }

    public static function getMainMenu($body, $array)
    {
        $regex = '/\[main_menu=+(.*?)\]/i';
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        if ($matches) {
            foreach ($matches as $match) {
                if (!array_key_exists($match[1], $array)) {
                    $id = $match[1];
                    $obj = new StdClass();
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__modules')
                        ->where('`id` = ' .$db->quote($id));
                    $db->setQuery($query);
                    $obj->module = $db->loadObject();
                    if (empty($obj->module)) {
                        $query = $db->getQuery(true)
                            ->select('*')
                            ->from('#__modules')
                            ->where('client_id = 0')
                            ->where('published = 1')
                            ->where('position = '.$db->quote('main-menu'))
                            ->where('module = '.$db->quote('mod_menu'));
                        $db->setQuery($query);
                        $obj->module = $db->loadObject();
                        if (empty($obj->module)) {
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__modules')
                                ->where('client_id = 0')
                                ->where('published = 1')
                                ->where('module = '.$db->quote('mod_menu'));
                            $db->setQuery($query);
                            $obj->module = $db->loadObject();
                        }
                    }
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__assets')
                        ->where('`id` = ' .$db->quote($obj->module->asset_id));
                    $db->setQuery($query);
                    $obj->asset = $db->loadObject();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__modules_menu')
                        ->where('`moduleid` = ' .$db->quote($obj->module->id));
                    $db->setQuery($query);
                    $obj->module_menu = $db->loadObject();
                    $params = $obj->module->params;
                    $params = json_decode($params);
                    $query = $db->getQuery(true);
                    $query->select("extension_id");
                    $query->from("#__extensions");
                    $query->where("type=" .$db->quote('component'))
                        ->where('element=' .$db->quote('com_gridbox'));
                    $db->setQuery($query);
                    $com_id = $db->loadResult();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__menu_types')
                        ->where('`menutype` = ' .$db->quote($params->menutype));
                    $db->setQuery($query);
                    $obj->menu = $db->loadObject();
                    $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__menu')
                        ->where('`menutype` = ' .$db->quote($params->menutype))
                        ->where('`component_id` = ' .$db->quote($com_id))
                        ->order('`id` DESC');
                    $db->setQuery($query);
                    $obj->menu_items = $db->loadObjectList();
                    $array[$id] = $obj;
                }
            }
        }
        
        return $array;
    }

    public static function getTags()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_tags');
        $db->setQuery($query);
        $tags = $db->loadObjectList();

        return $tags;
    }

    public static function getTaxCountries()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_countries')
            ->order('title ASC');
        $db->setQuery($query);
        $countries = $db->loadObjectList();
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

    public static function renderBootstrapModal($id, $title, $url)
    {
        $data = JVERSION >= '4.0.0' ? 'data-bs' : 'data';
        $btn = '<button type="button" class="btn" '.$data.'-dismiss="modal">';
        $btn .= JText::_('JLIB_HTML_BEHAVIOR_CLOSE').'</button>';
        $params = array('title' => JText::_($title), 'url' => $url, 'height' => '400px',
            'width' => '800px', 'bodyHeight' => 70, 'modalWidth' => 80, 'footer' => $btn);
        if (empty($url)) {
            //unset($params['url']);
        }
        $html = JHtml::_(
            'bootstrap.renderModal',
            $id,
            $params
        );

        return $html;
    }

    public static function renderBootstrapModalBtn($target)
    {
        $data = JVERSION >= '4.0.0' ? 'data-bs' : 'data';
        $btn = '<span '.$data.'-target="#'.$target.'" class="btn btn-primary" '.$data.'-toggle="modal">';
        $btn .= '<span class="icon-file"></span> '.JText::_('JSELECT').'</span>';

        return $btn;
    }
}