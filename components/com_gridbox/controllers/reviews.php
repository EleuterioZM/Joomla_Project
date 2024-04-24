<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class gridboxControllerReviews extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function sendCommentReport()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->sendReportEmail($id);
        exit();
    }

    public function getReviewById()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $page = $input->get('page', 0, 'int');
        $replyName = $input->get('replyName', '', 'string');
        $sortBy = $input->get('sort-by', 'recent', 'string');
        $object = new stdClass();
        $userStatus = gridboxHelper::getCommentsUserLoginHTML('reviews');
        gridboxHelper::setReviewsModerators();
        $object->commentsCount = gridboxHelper::getReviewsCountHTML($page, 'page', $sortBy);
        $object->captcha = gridboxHelper::$website->reviews_recaptcha;
        $object->comment = gridboxHelper::getReviewById($id, $replyName);
        $object->login = $userStatus->str;
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-message-pattern.php');
        $object->commentMessage = $string;
        $object->userStatus = $userStatus->status;
        $object->commentUser = gridboxHelper::$commentUser;
        if (empty(gridboxHelper::$commentUser) || (gridboxHelper::$website->reviews_recaptcha_guests == 1 &&
            !empty(gridboxHelper::$commentUser) &&
            (gridboxHelper::$commentUser->type == 'user' || gridboxHelper::$commentUser->type == 'social'))) {
            $object->captcha = '';
        }
        $json = json_encode($object);
        echo $json;exit();
    }

    public function getReviewsMoreReply()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $parent = $input->get('parent', 0, 'int');
        $replyName = $input->get('replyName', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $object = new stdClass();
        $userStatus = gridboxHelper::getCommentsUserLoginHTML('reviews');
        gridboxHelper::setReviewsModerators();
        $object->captcha = gridboxHelper::$website->reviews_recaptcha;
        $object->comments = gridboxHelper::getReviews($id, $parent, 1, $replyName, 1, $limit);
        $object->login = $userStatus->str;
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-message-pattern.php');
        $object->commentMessage = $string;
        $object->userStatus = $userStatus->status;
        $object->commentUser = gridboxHelper::$commentUser;
        if (empty(gridboxHelper::$commentUser) || (gridboxHelper::$website->reviews_recaptcha_guests == 1 &&
            !empty(gridboxHelper::$commentUser) &&
            (gridboxHelper::$commentUser->type == 'user' || gridboxHelper::$commentUser->type == 'social'))) {
            $object->captcha = '';
        }
        $json = json_encode($object);
        echo $json;exit();
    }

    public function getCommentsPatterns()
    {
        $object = new stdClass();
        $input = JFactory::getApplication()->input;
        $sortBy = $input->get('sort-by', 'recent', 'string');
        $id = $input->get('id', 0, 'int');
        $next = $input->get('next', 1, 'int');
        $reviewID = $input->get('reviewID', 0, 'int');
        $userStatus = gridboxHelper::getCommentsUserLoginHTML('reviews');
        gridboxHelper::setReviewsModerators();
        $object->commentsCount = gridboxHelper::getReviewsCountHTML($id, 'page', $sortBy);
        $object->captcha = gridboxHelper::$website->reviews_recaptcha;
        $object->comments = gridboxHelper::getReviews($id, 0, 0, '', $next, 2, $reviewID);
        $object->login = $userStatus->str;
        include(JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/reviews/reviews-message-pattern.php');
        $object->commentMessage = $string;
        $object->userStatus = $userStatus->status;
        $object->commentUser = gridboxHelper::$commentUser;
        if (empty(gridboxHelper::$commentUser) || (gridboxHelper::$website->reviews_recaptcha_guests == 1 &&
            !empty(gridboxHelper::$commentUser) &&
            (gridboxHelper::$commentUser->type == 'user' || gridboxHelper::$commentUser->type == 'social'))) {
            $object->captcha = '';
        }
        $json = json_encode($object);
        echo $json;exit();
    }

    public function sendCommentsEmails()
    {
        $model = $this->getModel();
        $model->sendCommentsEmails();
        exit;
    }

    public function unsubscribe()
    {
        $input = JFactory::getApplication()->input;
        $key = $input->get('key', '', 'string');
        $model = $this->getModel();
        $model->unsubscribe($key);
        gridboxHelper::setcookie('gridbox-comments-unsubscribe', 'unsubscribe', time()+3600);
        header('Location: '.JUri::root());
        exit;
    }

    public function moderatorDelete()
    {
        $moderator = $this->checkModerator();
        if ($moderator) {
            $input = JFactory::getApplication()->input;
            $id = $input->get('id', 0, 'int');
            $model = $this->getModel();
            $model->deleteComment($id);
        }
        exit;
    }

    public function moderatorBanUser()
    {
        $moderator = $this->checkModerator();
        if ($moderator) {
            $input = JFactory::getApplication()->input;
            $id = $input->get('id', 0, 'int');
            $model = $this->getModel();
            $msg = $model->moderatorBanUser($id);
            print_r($msg);
        }
        exit;
    }

    public function moderatorApprove()
    {
        $moderator = $this->checkModerator();
        if ($moderator) {
            $input = JFactory::getApplication()->input;
            $id = $input->get('id', 0, 'int');
            $model = $this->getModel();
            $model->moderatorApprove($id);
        }
        exit();
    }

    public function moderatorSpam()
    {
        $moderator = $this->checkModerator();
        if ($moderator) {
            $input = JFactory::getApplication()->input;
            $id = $input->get('id', 0, 'int');
            $model = $this->getModel();
            $model->moderatorSpam($id);
        }
        exit();
    }

    public function checkModerator()
    {
        $input = JFactory::getApplication()->input;
        $cookie = gridboxHelper::getCommentsUser();
        $flag = false;
        if (!empty($cookie)) {
            $user = json_decode($cookie);
            if ($user->type == 'user') {
                if (gridboxHelper::$website->reviews_moderator_admins == 'super_user') {
                    $moderators = array();
                    $db = JFactory::getDbo();
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
                } else {
                    $moderators = explode(',', gridboxHelper::$website->reviews_moderator_admins);
                }
                $flag = in_array($user->id, $moderators);
            }
        }

        return $flag;
    }

    public function deleteComment()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $cookie = gridboxHelper::getCommentsUser();
        $user = json_decode($cookie);
        $model = $this->getModel();
        $flag = $model->checkUserPermission($user, $id);
        if ($flag) {
            $model->deleteComment($id);
        }
        exit;
    }

    public function setLikes()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $action = $input->get('action', 'likes', 'string');
        $model->setLikes($id, $action);
        exit;
    }

    public function uploadAttachmentFile()
    {
        $input = JFactory::getApplication()->input;
        $file = $input->files->get('file', array(), 'array');
        $type = $input->post->get('type', 'file', 'string');
        $model = $this->getModel();
        $obj = $model->uploadAttachmentFile($file, $type);
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function removeTmpAttachment()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $filename = $input->get('filename', '', 'string');
        gridboxHelper::removeTmpReviewsAttachment($id, $filename);
        exit();
    }

    public function sendCommentMesssage()
    {
        $input = JFactory::getApplication()->input;
        $cookie = gridboxHelper::getCommentsUser();
        $user = json_decode($cookie);
        $data = new stdClass();
        $data->id = $input->get('id', 0, 'int');
        $data->page_id = $input->get('page_id', 0, 'int');
        $data->parent = $input->get('parent', 0, 'int');
        $data->rating = $input->get('rating', 0, 'int');
        $data->message = $input->get('message', '', 'raw');
        $data->type = $input->get('type', 'reply', 'string');
        $data->name = $user->name;
        $data->email = $user->email;
        $data->user_type = $user->type;
        $data->user_id = $user->id;
        $data->avatar = $user->avatar;
        $queue = $input->get('queue', '{}', 'string');
        $queueFiles = json_decode($queue);
        foreach ($queueFiles as $key => $file) {
            gridboxHelper::removeTmpReviewsAttachment($key, $file);
        }
        $attachments = $input->get('attachments', '{}', 'string');
        $data->files = json_decode($attachments);
        $model = $this->getModel();
        $response = new stdClass();
        $response->message = $data->parent == 0 ? JText::_('REVIEW_SUCCESSFULY_POSTED') : JText::_('COMMENT_SUCCESSFULY_POSTED');
        if ($data->type == 'submit' && $model->checkUserReviews($user, $data->page_id)) {
            $response->message = JText::_('ALREADY_VOTED');
            $response->type = 'error';
        } else if (!empty($data->message) || $attachments != '{}') {
            $response->message = $model->sendCommentMesssage($data);
        }
        $str = json_encode($response);
        echo $str;exit;
    }
}