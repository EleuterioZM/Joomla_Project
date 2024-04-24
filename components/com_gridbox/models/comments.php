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

class gridboxModelComments extends JModelItem
{
    public function getTable($type = 'Fonts', $prefix = 'gridboxTable', $config = []) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function moderatorBanUser($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('email, ip')
            ->from('#__gridbox_comments')
            ->where('id = '.$id);
        $db->setQuery($query);
        $user = $db->loadObject();
        if (!empty($user->email)) {
            $flag = $this->checkCommentUserBanStatus($user->email, '#__gridbox_comments_banned_emails', 'email');
            if (!$flag) {
                $obj = new stdClass();
                $obj->email = $user->email;
                $db->insertObject('#__gridbox_comments_banned_emails', $obj);
            }
        }
        if (!empty($user->ip)) {
            $flag = $this->checkCommentUserBanStatus($user->ip, '#__gridbox_comments_banned_ip', 'ip');
            if (!$flag) {
                $obj = new stdClass();
                $obj->ip = $user->ip;
                $db->insertObject('#__gridbox_comments_banned_ip', $obj);
            }
        }
        if (empty($user->email) && empty($user->ip)) {
            $msg = JText::_('USER_CANNOT_BE_BANNED');
        } else {
            $msg = JText::_('SUCCESSFULLY_BANNED');
        }

        return $msg;
    }

    public function checkCommentUserBanStatus($value, $table, $key)
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

    public function moderatorApprove($id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->id = $id;
        $obj->status = 'approved';
        $db->updateObject('#__gridbox_comments', $obj, 'id');
    }

    public function moderatorSpam($id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->id = $id;
        $obj->status = 'spam';
        $db->updateObject('#__gridbox_comments', $obj, 'id');
    }

    public function checkUserPermission($user, $id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('user_type, user_id')
            ->from('#__gridbox_comments')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();

        return $obj->user_type != 'guest' && $obj->user_type == $user->type && $obj->user_id == $user->id;
    }

    public function deleteComment($id)
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
            gridboxHelper::removeTmpAttachment($file->id, $file->filename);
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
            $this->deleteComment($child->id);
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

    public function sendCommentMesssage($data)
    {
        $db = JFactory::getDbo();
        $ip = $_SERVER['REMOTE_ADDR'];
        $jtext = JText::_('COMMENT_SUCCESSFULY_POSTED');
        $spam = $this->checkBanLists($data->email, $ip, $data->message);
        if ($spam && gridboxHelper::$website->comments_auto_deleting_spam == 1) {
            $jtext = JText::_('COMMENT_MARKED_SPAM_DELETED');
            foreach ($data->files as $file) {
                gridboxHelper::removeTmpAttachment($file->id, $file->filename);
            }
        } else {

            $obj = new stdClass();
            $obj->name = $data->name;
            $obj->email = $data->email;
            $obj->message = $data->message;
            $obj->avatar = $data->avatar;
            $obj->user_type = $data->user_type;
            $obj->user_id = $data->user_id;
            gridboxHelper::setCommentsModerators();
            $moderators = gridboxHelper::$commentsModerators;
            if ($data->type == 'edit') {
                $obj->status = 'approved';
                $obj->id = $data->id;
            } else {
                $obj->status = gridboxHelper::$website->comments_premoderation == 1 ? 'pending' : 'approved';
                if ($obj->user_type == 'user' && in_array($obj->user_id * 1, $moderators)) {
                    $obj->status = 'approved';
                }
                if (gridboxHelper::$website->email_notifications == 0) {
                    $obj->user_notification = 1;
                    $obj->admin_notification = 1;
                } else if (gridboxHelper::$website->user_notifications == 0) {
                    $obj->user_notification = 1;
                }
                $obj->date = date("Y-m-d H:i:s");
                $obj->parent = $data->parent;
            }
            $obj->status = $spam ? 'spam' : $obj->status;
            if ($obj->status == 'pending') {
                $jtext = JText::_('COMMENT_AWAITING_MODERATION');
            } else if ($obj->status == 'spam') {
                $jtext = JText::_('COMMENT_MARKED_SPAM');
            }
            $obj->page_id = $data->page_id;
            if (gridboxHelper::$website->ip_tracking == 1) {
                $obj->ip = $ip;
            }
            if ($data->type == 'edit') {
                $db->updateObject('#__gridbox_comments', $obj, 'id');
                $id = $obj->id;
            } else {
                $db->insertObject('#__gridbox_comments', $obj);
                $id = $db->insertid();
            }
            foreach ($data->files as $file) {
                $file->comment_id = $id;
                $db->updateObject('#__gridbox_comments_attachments', $file, 'id');
            }
        }

        return $jtext;
    }

    public function sendCommentsEmails()
    {
        if (gridboxHelper::$website->email_notifications == 1) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_comments')
                ->where('admin_notification = 0');
            $db->setQuery($query);
            $array = $db->loadObjectList();
            foreach ($array as $value) {
                $this->sendModeratorEmail($value->id);
            }
            $query = $db->getQuery(true)
                ->select('id, parent')
                ->from('#__gridbox_comments')
                ->where('user_notification = 0')
                ->where('parent <> 0')
                ->where('status = '.$db->quote('approved'));
            $db->setQuery($query);
            $array = $db->loadObjectList();
            foreach ($array as $value) {
                if (gridboxHelper::$website->user_notifications == 1) {
                    $this->sendReplyEmail($value->id);
                }
            }
        }
    }

    public function checkUserUnsubscribe($email)
    {
        if (!empty($email)) {
            $db = JFactory::getDbo();
            $hash = md5(strtolower(trim($email)));
            $query = $db->getQuery(true)
                ->select('COUNT(id)')
                ->from('#__gridbox_comments_unsubscribed_users')
                ->where('user = '.$db->quote($hash));
            $db->setQuery($query);
            $count = $db->loadResult();
            $flag = $count == 0;
        } else {
            $flag = false;
        }

        return $flag;
    }

    public function unsubscribe($key)
    {
        if (!empty($key)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('COUNT(id)')
                ->from('#__gridbox_comments_unsubscribed_users')
                ->where('user = '.$db->quote($key));
            $db->setQuery($query);
            $count = $db->loadResult();
            if ($count == 0) {
                $obj = new stdClass();
                $obj->user = $key;
                $db->insertObject('#__gridbox_comments_unsubscribed_users', $obj);
            }
        }
    }

    public function sendReplyEmail($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title, pc.email as recipient')
            ->from('#__gridbox_comments AS c')
            ->where('c.id = '.$id)
            ->leftJoin('`#__gridbox_pages` AS p ON p.id = c.page_id')
            ->leftJoin('`#__gridbox_comments` AS pc ON pc.id = c.parent');
        $db->setQuery($query);
        $data = $db->loadObject();
        $flag = $this->checkUserUnsubscribe($data->recipient);
        if ($data->user_notification == 0 && !empty($data->recipient) && $flag) {
            $query = $db->getQuery(true)
                ->update('#__gridbox_comments')
                ->set(['user_notification = 1'])
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
            $recipients = [$data->recipient];
            $this->sendEmail($id, $data, 'NEW_REPLY_TO_COMMENT_ON', $recipients, 'comments-box-reply-email-pattern.php');
        }
    }

    public function sendModeratorEmail($id)
    {
        gridboxHelper::setCommentsModerators();
        $moderators = gridboxHelper::$commentsModerators;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title')
            ->from('#__gridbox_comments AS c')
            ->where('c.id = '.$id)
            ->leftJoin('`#__gridbox_pages` AS p ON p.id = c.page_id');
        $db->setQuery($query);
        $data = $db->loadObject();
        if ($data->admin_notification == 0) {
            $query = $db->getQuery(true)
                ->update('#__gridbox_comments')
                ->set('admin_notification = 1')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
        if (!empty($moderators) && $data->admin_notification == 0) {
            $recipients = [];
            foreach ($moderators as $moderator) {
                $query = $db->getQuery(true)
                    ->select('email')
                    ->from('#__users')
                    ->where('id = '.$moderator);
                $db->setQuery($query);
                $email = $db->loadResult();
                if ($email != $data->email) {
                    $recipients[] = $email;
                }
            }
            $this->sendEmail($id, $data, 'NEW_COMMENT_POSTED_ON', $recipients, 'comments-box-moderator-email-pattern.php');
        }
        if (gridboxHelper::$website->author_notifications == 1 && $data->admin_notification == 0) {
            $recipients = [];
            $query = $db->getQuery(true)
                ->select('email')
                ->from('#__gridbox_authors_map AS m')
                ->leftJoin('#__gridbox_authors AS a ON a.id = m.author_id')
                ->leftJoin('#__users AS u ON u.id = a.user_id')
                ->where('m.page_id = '.$data->page_id);
            $db->setQuery($query);
            $array = $db->loadObjectList();
            foreach ($array as $obj) {
                $recipients[] = $obj->email;
            }
            $this->sendEmail($id, $data, 'NEW_COMMENT_POSTED_ON', $recipients, 'comments-box-moderator-email-pattern.php');
        }
    }

    protected function sendEmail($id, $data, $subject, $recipients, $pattern)
    {
        if (empty($recipients)) {
            return;
        }
        $config = JFactory::getConfig();
        $db = JFactory::getDbo();
        $sender = [$config->get('mailfrom'), $config->get('fromname')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_attachments')
            ->where('comment_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $attachment = [];
        foreach ($files as $file) {
            $attachment[] = JPATH_ROOT.'/components/com_gridbox/assets/uploads/comments/'.$file->filename;
        }
        if (empty($data->avatar)) {
            $author = gridboxHelper::getAuthor($data->user_id);
            $data->name = $author->title ?? $data->name;
            $avatar = gridboxHelper::getUserAvatar($data->email, 'enable_gravatar', $author);
        } else {
            $avatar = $data->avatar;
        }
        $message = str_replace("\n", '<br>', $data->message);
        $date = gridboxHelper::formatDate($data->date);
        $subject = JText::_($subject).' '.$data->title;
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/comments-box/'.$pattern);
        try {
            $mailer = JFactory::getMailer();
            $mailer->sendMail($sender[0], $sender[1], $recipients, $subject, $out, true, null, null, $attachment);
        } catch (Exception $e) {}
    }

    public function sendReportEmail($id)
    {
        gridboxHelper::setCommentsModerators();
        $moderators = gridboxHelper::$commentsModerators;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('c.*, p.title')
            ->from('#__gridbox_comments AS c')
            ->where('c.id = '.$id)
            ->leftJoin('`#__gridbox_pages` AS p ON p.id = c.page_id');
        $db->setQuery($query);
        $data = $db->loadObject();
        if (!empty($moderators)) {
            $recipients = [];
            foreach ($moderators as $moderator) {
                $query = $db->getQuery(true)
                    ->select('email')
                    ->from('#__users')
                    ->where('id = '.$moderator);
                $db->setQuery($query);
                $email = $db->loadResult();
                if ($email != $data->email) {
                    $recipients[] = $email;
                }
            }
            $this->sendEmail($id, $data, 'COMMENT_FLAGGED_SPAM_ABUSIVE_ON', $recipients, 'comments-box-report-email-pattern.php');
        }
    }

    public function checkBanLists($email, $ip, $message)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_comments_banned_emails')
            ->where('email = '.$db->quote($email));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {

            return true;
        }
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_comments_banned_ip')
            ->where('ip = '.$db->quote($ip));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {

            return true;
        }
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_comments_banned_words');
        $db->setQuery($query);
        $words = $db->loadObjectList();
        $flag = false;
        $mb_message = mb_strtolower($message);
        $wordsArray = [];
        foreach ($words as $obj) {
            $wordsArray[] = mb_strtolower($obj->word);
        }
        if (!empty($wordsArray)) {
            $wordsStr = implode('|', $wordsArray);
            $regexp = '/(?i)(\s|,|\.|^)('.$wordsStr.')(\s|,|\.|$)/';
            preg_match_all($regexp, $mb_message, $matches, PREG_SET_ORDER);
            $flag = !empty($matches);
        }
        if (gridboxHelper::$website->comments_block_links == 1 && !$flag) {
            $flag = preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $message);
        }

        return $flag;
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
}
