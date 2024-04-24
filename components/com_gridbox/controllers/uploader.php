<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class gridboxControllerUploader extends JControllerForm
{
    public function executeAction()
    {
        $action = $this->input->get('action', '', 'string');
        $level = 'core.edit';
        if ($action == 'createFolder' || $action == 'uploadFile') {
            $level = 'core.create';
        } else if ($action == 'contextDelete' || $action == 'multipleDelete') {
            $level = 'core.delete';
        }
        if ($action != 'loadFolder' && $action != 'setLimit' && $action != 'setSorting'  && $action != 'setPage') {
            gridboxHelper::checkUserEditLevel($level);
        }
        $path = $this->input->get('path', '', 'raw');
        $model = $this->getModel();
        $uploader = $model->getUploader($path);
        $response = call_user_func([$uploader, $action]);
        $str = json_encode($response);
        print_r($str);exit();
    }

    public function checkFileExists()
    {
        gridboxHelper::checkUserEditLevel();
        $content = file_get_contents('php://input');
        $obj = json_decode($content);
        $name = $obj->title;
        $file = gridboxHelper::replace($name);
        $file = JFile::makeSafe($file.'.'.$obj->ext);
        $name = str_replace('-', '', $file);
        $name = str_replace($obj->ext, '', $name);
        $name = str_replace('.', '', $name);
        if ($name == '') {
            $file = date("Y-m-d-H-i-s").'.'.$obj->ext;
        }
        $obj->path = str_replace($obj->name, '', $obj->path).$file;
        echo JFile::exists(JPATH_ROOT.'/'.$obj->path);exit;
    }

    public function savePhotoEditorImage()
    {
        gridboxHelper::checkUserEditLevel();
        $content = file_get_contents('php://input');
        $obj = json_decode($content);
        if (isset($obj->title)) {
            $name = $obj->title;
            $file = gridboxHelper::replace($name);
            $file = JFile::makeSafe($file.'.'.$obj->ext);
            $name = str_replace('-', '', $file);
            $name = str_replace($obj->ext, '', $name);
            $name = str_replace('.', '', $name);
            if ($name == '') {
                $file = date("Y-m-d-H-i-s").'.'.$obj->ext;
            }
            $obj->path = str_replace($obj->name, '', $obj->path).$file;
        }
        $data = explode(',', $obj->image);
        $method = $obj->method;
        $str = $method($data[1]);
        if ($obj->ext == 'png') {
            $imageSave = $this->imageSave($obj->ext);
            $imageCreate = $this->imageCreate($obj->ext);
            $img = imagecreatefromstring($str);
            $width = imagesx($img);
            $height = imagesy($img);
            $out = imagecreatetruecolor($width, $height);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
            imagefilledrectangle($out, 0, 0, $width, $height, $transparent);          
            imagecopyresampled($out, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            $imageSave($out, JPATH_ROOT.'/'.$obj->path, 9);
        } else {
            JFile::write(JPATH_ROOT.'/'.$obj->path, $str);
        }
        echo JPATH_ROOT.'/'.$obj->path;
        exit();
    }

    public function showImage()
    {
        $input = JFactory::getApplication()->input;
        $image = $input->get('image', '', 'string');
        $dir = JPATH_ROOT.'/'.$image;
        $ext = strtolower(JFile::getExt($dir));
        $imageCreate = $this->imageCreate($ext);
        $imageSave = $this->imageSave($ext);
        Header("Content-type: image/".$ext);
        if (!function_exists($imageCreate) || !$im = $imageCreate($dir)) {
            $f = fopen($dir, "r");
            fpassthru($f);
        } else {
            $width = imagesx($im);
            $height = imagesy($im);
            $ratio = $width / $height;
            if ($width > $height) {
                $w = 100;
                $h = round(100 / $ratio);
            } else {
                $h = 100;
                $w = round(100 * $ratio);
            }
            $out = imagecreatetruecolor($w, $h);
            if ($ext == 'png' || $ext == 'webp') {
                imagealphablending($out, false);
                imagesavealpha($out, true);
                $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
                imagefilledrectangle($out, 0, 0, $w, $h, $transparent);
            }
            imagecopyresampled($out, $im, 0, 0, 0, 0, $w, $h, $width, $height);
            $imageSave($out);
            imagedestroy($im);
            imagedestroy($out);
        }
        exit;
    }

    public function imageSave($type) {
        switch ($type) {
            case 'png':
                $imageSave = 'imagepng';
                break;
            case 'gif':
                $imageSave = 'imagegif';
                break;
            case 'webp':
                $imageSave = 'imagewebp';
                break;
            default:
                $imageSave = 'imagejpeg';
        }

        return $imageSave;
    }

    public function imageCreate($type) {
        switch ($type) {
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
}