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

class gridboxModelComments extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'status', 'name', 'date'
            );
        }
        parent::__construct($config);
    }

    public function getIntegrations()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('(service = '.$db->quote('google_login').' OR service = '.$db->quote('facebook_login').' OR service = '.$db->quote('vk_login').')')
            ->where('type = '.$db->quote('integration'));
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $integrations = new stdClass();
        foreach ($array as $obj) {
            $integrations->{$obj->service} = $obj;
        }

        return $integrations;
    }

    public function saveCommentsOptions($obj)
    {
        $db = JFactory::getDbo();
        $obj->id = 1;
        $db->updateObject('#__gridbox_website', $obj, 'id');
    }

    public function setCommetsBannedList($obj)
    {
        $list = $this->getBannedCommentsLists();
        $db = JFactory::getDbo();
        $data = new stdClass();
        foreach ($list->emails as $value) {
            if (!in_array($value->email, $obj->emails)) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_comments_banned_emails')
                    ->where('id = '.$value->id);
                $db->setQuery($query)
                    ->execute();
            } else {
                $data->{$value->email} = $value->id;
            }
        }
        foreach ($obj->emails as $email) {
            if (!isset($data->{$email})) {
                $object = new stdClass();
                $object->email = $email;
                $db->insertObject('#__gridbox_comments_banned_emails', $object);
            }
        }
        $data = new stdClass();
        foreach ($list->words as $value) {
            if (!in_array($value->word, $obj->words)) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_comments_banned_words')
                    ->where('id = '.$value->id);
                $db->setQuery($query)
                    ->execute();
            } else {
                $data->{$value->word} = $value->id;
            }
        }
        foreach ($obj->words as $word) {
            if (!isset($data->{$word})) {
                $object = new stdClass();
                $object->word = $word;
                $db->insertObject('#__gridbox_comments_banned_words', $object);
            }
        }
        $data = new stdClass();
        foreach ($list->ip as $value) {
            if (!in_array($value->ip, $obj->ip)) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_comments_banned_ip')
                    ->where('id = '.$value->id);
                $db->setQuery($query)
                    ->execute();
            } else {
                $data->{$value->ip} = $value->id;
            }
        }
        foreach ($obj->ip as $ip) {
            if (!isset($data->{$ip})) {
                $object = new stdClass();
                $object->ip = $ip;
                $db->insertObject('#__gridbox_comments_banned_ip', $object);
            }
        }
    }

    public function getUserGroups($id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, id')
            ->from('`#__usergroups`')
            ->where('parent_id = '.$id)
            ->order('title ASC');
        $db->setQuery($query);
        $usergroups = $db->loadObjectList();
        foreach ($usergroups as $key => $group) {
            $array = $this->getUserGroups($group->id);
            if (!empty($array)) {
                array_splice($usergroups, $key + 1, 0, $array);
            }
        }
        
        return $usergroups;
    }

    public function getUsers()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('u.id, u.name, u.username, g.title, g.id as level')
            ->from('`#__users` AS u')
            ->leftJoin('`#__user_usergroup_map` AS m ON '.$db->quoteName('u.id').' = '.$db->quoteName('m.user_id'))
            ->leftJoin('`#__usergroups` AS g ON '.$db->quoteName('g.id').' = '.$db->quoteName('m.group_id'));
        $db->setQuery($query);
        $users = $db->loadObjectList();
        
        return $users;
    }

    public function getBannedCommentsLists()
    {
        $db = JFactory::getDbo();
        $list = new stdClass();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_banned_emails');
        $db->setQuery($query);
        $list->emails = $db->loadObjectList();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_banned_words');
        $db->setQuery($query);
        $list->words = $db->loadObjectList();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_banned_ip');
        $db->setQuery($query);
        $list->ip = $db->loadObjectList();

        return $list;
    }

    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }

    public function banUser($email, $ip)
    {
        $db = JFactory::getDbo();
        if (!empty($email)) {
            $flag = gridboxHelper::checkCommentUserBanStatus($email, '#__gridbox_comments_banned_emails', 'email');
            if (!$flag) {
                $obj = new stdClass();
                $obj->email = $email;
                $db->insertObject('#__gridbox_comments_banned_emails', $obj);
            }
        }
        if (!empty($ip)) {
            $flag = gridboxHelper::checkCommentUserBanStatus($ip, '#__gridbox_comments_banned_ip', 'ip');
            if (!$flag) {
                $obj = new stdClass();
                $obj->ip = $ip;
                $db->insertObject('#__gridbox_comments_banned_ip', $obj);
            }
        }
    }

    public function sendCommentMesssage($parent, $message, $files, $type)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        if ($type == 'reply') {
            $query = $db->getQuery(true)
                ->select('page_id')
                ->from('#__gridbox_comments')
                ->where('id = '.$parent);
            $db->setQuery($query);
            $page_id = $db->loadResult();
            $user = JFactory::getUser();
            $obj->name = $user->name;
            $obj->email = $user->email;
            $obj->message = $message;
            $obj->date = date("Y-m-d H:i:s");
            if (gridboxHelper::$website->email_notifications == 0) {
                $obj->user_notification = 1;
                $obj->admin_notification = 1;
            } else if (gridboxHelper::$website->user_notifications == 0) {
                $obj->user_notification = 1;
            }
            $obj->status = 'approved';
            $obj->parent = $parent;
            $obj->unread = 0;
            $obj->user_type = 'user';
            $obj->user_id = $user->id;
            $obj->page_id = $page_id;
            $db->insertObject('#__gridbox_comments', $obj);
            $id = $db->insertid();
        } else {
            $obj->id = $parent;
            $obj->message = $message;
            $db->updateObject('#__gridbox_comments', $obj, 'id');
            $id = $parent;
        }
        foreach ($files as $file) {
            $file->comment_id = $id;
            $db->updateObject('#__gridbox_comments_attachments', $file, 'id');
        }
    }

    public function uploadAttachmentFile($file, $type)
    {
        $obj = new stdClass();
        if (gridboxHelper::$website->enable_attachment == 1 && isset($file['error']) && $file['error'] == 0) {
            if ($type == 'image') {
                $str = 'gif,jpg,jpeg,png,svg,webp';
            } else {
                $str = str_replace(' ', '', gridboxHelper::$website->attachment_types);
            }
            $types = explode(',', $str);
            $ext = strtolower(JFile::getExt($file['name']));
            if (gridboxHelper::$website->attachment_size * 1000 > $file['size'] && in_array($ext, $types)) {
                $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/comments/';
                if (!JFolder::exists($dir)) {
                    JFolder::create($dir);
                }
                $name = str_replace('.'.$ext, '', $file['name']);
                $fileName = gridboxHelper::replace($name);
                $fileName = JFile::makeSafe($fileName);
                $name = str_replace('-', '', $fileName);
                $name = str_replace('.', '', $name);
                if ($name == '') {
                    $fileName = date("Y-m-d-H-i-s").'.'.$ext;
                }
                $i = 2;
                $name = $fileName;
                while (JFile::exists($dir.$name.'.'.$ext)) {
                    $name = $fileName.'-'.($i++);
                }
                $fileName = $name.'.'.$ext;
                JFile::upload($file['tmp_name'], $dir.$fileName);
                $obj = $this->addAttachmentFile($file['name'], $fileName, $type);
            }
        }

        return $obj;
    }

    public function addAttachmentFile($name, $filename, $type)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->comment_id = 0;
        $obj->name = $name;
        $obj->filename = $filename;
        $obj->type = $type;
        $obj->date = date("Y-m-d-H-i-s");
        $db->insertObject('#__gridbox_comments_attachments', $obj);
        $obj->id = $db->insertid();

        return $obj;
    }

    public function removeTmpAttachments()
    {
        $mktime  = mktime(0, 0, 0, date("m")  , date("d") - 1, date("Y"));
        $date = date("Y-m-d-H-i-s", $mktime);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_attachments')
            ->where('date < '.$db->quote($date))
            ->where('comment_id = 0');
        $db->setQuery($query, 0, 10);
        $files = $db->loadObjectList();
        $dir = JPATH_ROOT.'/components/com_gridbox/assets/uploads/comments/';
        foreach ($files as $file) {
            gridboxHelper::removeTmpAttachment($file->id, $file->filename);
        }
    }

    public function getCommentLikeStatus($id)
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

    public function setLikes($id, $action)
    {
        $db = JFactory::getDbo();
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_likes_map')
            ->where('comment_id = '.$id)
            ->where('ip = '.$db->quote($ip));
        $db->setQuery($query);
        $user = $db->loadObject();
        if (!$user) {
            $fields = array(
                $db->quoteName($action).' = '.$db->quoteName($action).'+1'
            );
            $user = new stdClass();
            $user->comment_id = $id;
            $user->ip = $ip;
            $user->status = $action;
            $db->insertObject('#__gridbox_comments_likes_map', $user);
        } else {
            if ($action == $user->status) {
                $fields = array(
                    $db->quoteName($action).' = '.$db->quoteName($action).'-1'
                );
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_comments_likes_map')
                    ->where('id = '.$user->id);
                $db->setQuery($query)
                    ->execute();
            } else {
                $fields = array(
                    $db->quoteName($user->status).' = '.$db->quoteName($user->status).'-1',
                    $db->quoteName($action).' = '.$db->quoteName($action).'+1'
                );
                $user->status = $action;
                $db->updateObject('#__gridbox_comments_likes_map', $user, 'id');
            }
        }
        $query = $db->getQuery(true)
            ->update('#__gridbox_comments')
            ->set($fields)
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->select('likes, dislikes')
            ->from('#__gridbox_comments')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        $obj->status = $this->getCommentLikeStatus($id);
        $str = json_encode($obj);
        echo $str;
    }

    public function setReadStatus($id, $state = 0)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->id = $id;
        $obj->unread = $state;
        $db->updateObject('#__gridbox_comments', $obj, 'id');
    }

    public function readAll()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->update('#__gridbox_comments')
            ->set('unread = 0');
        $db->setQuery($query)
            ->execute();
    }

    public function approve($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $obj = new stdClass();
            $obj->id = $id;
            $obj->status = 'approved';
            $db->updateObject('#__gridbox_comments', $obj, 'id');
        }
    }

    public function spam($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $obj = new stdClass();
            $obj->id = $id;
            $obj->status = 'spam';
            $db->updateObject('#__gridbox_comments', $obj, 'id');
        }
    }

    public function delete($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            gridboxHelper::deleteComment($id);
        }
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
    
    protected function getListQuery()
    {
        $this->removeTmpAttachments();
        $this->getGridboxFilters();
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title, u.email AS user_email')
            ->from('#__gridbox_comments AS c')
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->leftJoin('`#__gridbox_pages` AS p ON '.$db->quoteName('p.id').' = '.$db->quoteName('c.page_id'));
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%', false);
            $query->where('c.name LIKE '.$search.' OR c.email LIKE '.$search.' OR c.message LIKE '.$search);
        }
        $status = $this->getState('filter.status');
        if ($status !== '') {
            $query->where('c.status = '.$db->quote($status));
        }
        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDirn = $this->state->get('list.direction', 'desc');
        if ($orderCol == 'order_list') {
            $orderDirn = 'ASC';
        }
        $orderCol = 'c.'.$orderCol;
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        
        return $query;
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.status');
        return parent::getStoreId($id);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_state', '', 'string');
        $this->setState('filter.status', $status);
        parent::populateState('id', 'desc');
    }
}