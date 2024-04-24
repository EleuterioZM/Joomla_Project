<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class gridboxControllerComments extends JControllerAdmin
{
    public function getModel($name = 'comments', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function getSettings()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = new stdClass();
        $obj->website = gridboxHelper::$website;
        $obj->users = $model->getUsers();
        $obj->commentsBanList = $model->getBannedCommentsLists();
        $obj->moderators = $obj->website->comments_moderator_admins;
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function saveCommentsOptions()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $str = $input->get('obj', '{}', 'raw');
        $obj = json_decode($str);
        $model = $this->getModel();
        $model->saveCommentsOptions($obj->website);
        $model->setCommetsBannedList($obj->commentsBannedList);
        gridboxHelper::ajaxReload('JLIB_APPLICATION_SAVE_SUCCESS');
    }

    public function banUser()
    {
        $input = JFactory::getApplication()->input;
        $email = $input->get('email', '', 'string');
        $ip = $input->get('ip', '', 'string');
        $model = $this->getModel();
        $model->banUser($email, $ip);
        if (empty($email) && empty($ip)) {
            gridboxHelper::ajaxReload('USER_CANNOT_BE_BANNED');
        } else {
            gridboxHelper::ajaxReload('SUCCESSFULLY_BANNED');
        }
    }

    public function sendCommentMesssage()
    {
        $input = JFactory::getApplication()->input;
        $parent = $input->get('parent', 0, 'int');
        $message = $input->get('message', '', 'raw');
        $type = $input->get('type', 'reply', 'string');
        $attachments = $input->get('attachments', '{}', 'string');
        $files = json_decode($attachments);
        $model = $this->getModel();
        $model->sendCommentMesssage($parent, $message, $files, $type);
        if ($type != 'reply') {
            $attachments = gridboxHelper::getCommentAttachments($parent);
            $str = json_encode($attachments);
            echo $str;
        }
        exit();
    }

    public function removeTmpAttachment()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $filename = $input->get('filename', '', 'string');
        gridboxHelper::removeTmpAttachment($id, $filename);
        exit();
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

    public function getCommentLikeStatus()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $status = $model->getCommentLikeStatus($id);
        echo $status;exit;
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

    public function setReadStatus()
    {
        $model = $this->getModel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model->setReadStatus($id);
        exit;
    }

    public function readAll()
    {
        $model = $this->getModel();
        $model->readAll();
        exit;
    }

    public function unread()
    {
        $cid = $this->input->get('cid', [], 'array');
        $model = $this->getModel();
        foreach ($cid as $id) {
            $model->setReadStatus($id, 1);
        }
        exit;
    }

    public function approve()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', [], 'post', 'array');
        $model = $this->getModel();
        $model->approve($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_APPROVED');
    }

    public function contextApprove()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = array();
        $array[] = $id;
        $model = $this->getModel();
        $model->approve($array);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_APPROVED');
    }

    public function spam()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->spam($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_SPAMED');
    }

    public function contextSpam()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = array();
        $array[] = $id;
        $model = $this->getModel();
        $model->spam($array);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_SPAMED');
    }

    public function delete()
    {
        gridboxHelper::checkUserEditLevel();
        $pks = $this->input->getVar('cid', array(), 'post', 'array');
        $model = $this->getModel();
        $model->delete($pks);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }

    public function contextDelete()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $array = array();
        $array[] = $id;
        $model = $this->getModel();
        $model->delete($array);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
    }
}