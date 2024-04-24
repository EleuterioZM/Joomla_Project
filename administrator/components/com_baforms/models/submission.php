<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/ 

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
 
class baformsModelSubmission extends JModelAdmin
{
    public function getTable($type = 'Submissions', $prefix = 'formsTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function deleteFiles($cid)
    {
        $db = JFactory::getDbo();
        foreach ($cid as $id) {
            $this->deleteFile($id, $db);
        }
    }

    public function deleteFile($id, $db)
    {
        $query = $db->getQuery(true)
            ->select('id, filename')
            ->from('#__baforms_submissions_attachments')
            ->where('submission_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        foreach ($files as $file) {
            $this->removeTmpAttachment($file->id, $file->filename, 0);
        }
    }

    public function contextDelete($id)
    {
        $db = JFactory::getDbo();
        $this->deleteFile($id, $db);
        $query= $db->getQuery(true)
            ->delete('#__baforms_submissions')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function removeTmpAttachment($id, $filename, $submission)
    {
        if (!empty($id) && !empty($filename)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__baforms_submissions_attachments')
                ->where('id = '.$id);
            $db->setQuery($query);
            $file = $db->loadObject();
            $target = JPATH_ROOT.'/'.UPLOADS_STORAGE.'/form-'.$file->form_id.'/'.$file->filename;
            if (JFile::exists($target)) {
                JFile::delete($target);
            }
            $query = $db->getQuery(true)
                ->delete('#__baforms_submissions_attachments')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        } else if (empty($id) && !empty($filename)) {
            $params = JComponentHelper::getParams('com_baforms');
            $uploaded_path = $params->get('uploaded_path', 'images');
            $target = JPATH_ROOT.'/'.$uploaded_path.'/baforms/'.$filename;
            if (JFile::exists($target)) {
                JFile::delete($target);
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__baforms_submissions')
                ->where('id = '.$submission);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $message = explode('_-_', $obj->message);
            $array = array();
            foreach ($message as $value) {
                $item = explode('|-_-|', $value);
                if (empty($item[0]) || !($item[2] == 'upload' && $item[1] == $filename)) {
                    $array[] = $value;
                }
            }
            $obj->message = implode('_-_', $array);
            $db->updateObject('#__baforms_submissions', $obj, 'id');
        }
    }

    public function getFiles($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_submissions_attachments')
            ->where('submission_id = '.$id);
        $db->setQuery($query);
        $files = $db->loadObjectList();
        $items = new stdClass();
        foreach ($files as $file) {
            if (!isset($items->{$file->field_id})) {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__baforms_items')
                    ->where('id = '.$file->field_id);
                $db->setQuery($query);
                $field = $db->loadObject();
                $obj = new stdClass();
                if (!empty($field->options)) {
                    $params = json_decode($field->options);
                    $obj->title = isset($params->title) ? $params->title : '';
                } else {
                    $obj->title = 'Upload file button';
                }
                $obj->files = array();
                $items->{$file->field_id} = $obj;
            }
            $items->{$file->field_id}->files[] = $file;
        }

        return $items;
    }

    public function getMessage($id)
    {
        $result = baformsHelper::getSubmission($id);

        return $result;
    }
    
    public function getForm($data = [], $loadData = true)
    {
        
        $form = $this->loadForm($this->option . '.submissions', 'submissions', array('control' => 'jform', 'load_data' => $loadData)); 
        if (empty($form)) {
            return false;
        }
 
        return $form;
    }

    public function readAll()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->update('#__baforms_submissions')
            ->set('submission_state = 0');
        $db->setQuery($query)
            ->execute();
    }
    
    public function setReadStatus($id, $state = 0)
    {
        $db = $this->getDbo();
        $obj = new stdClass();
        $obj->id = $id;
        $obj->submission_state = $state;
        $db->updateObject('#__baforms_submissions', $obj, 'id');
    }    
}