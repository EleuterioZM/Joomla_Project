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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class gridboxControllergridbox extends JControllerForm
{
    public function getModel($name = 'gridbox', $prefix = 'gridboxModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function compressAdaptiveImage($ind = '')
    {
        $image = $this->input->get('image', '', 'string');
        $compressed = gridboxHelper::getCompressFolder($image, $ind);
        $dir = JPATH_ROOT.$image;
        $ext = strtolower(JFile::getExt($dir));
        $gd_info = gd_info();
        $imageCreate = $this->imageCreate($compressed->ext);        
        $dev = $this->input->get('dev_mode', '', 'string');
        if (!$gd_info['WebP Support'] && !empty($dev)) {
            print_r('Your PHP image library GD was compiled without WebP Support');exit;
        }
        $imageSave = $this->imageSave($compressed->endExt);
        $size = gridboxHelper::$website->images_max_size * 1;
        if (!empty($ind)) {
            $size = gridboxHelper::$breakpoints->{$ind} * 1;
        }
        $quality = gridboxHelper::$website->images_quality * 1;
        if (!empty($ind)) {
            $quality = gridboxHelper::$website->adaptive_quality * 1;
        }
        $exists = JFile::exists($compressed->path) && filesize($compressed->path) != 0;
        $origFlag = JFile::exists($dir);
        if (($origFlag && !$exists && !$im = $imageCreate($dir)) || !$origFlag) {
            $compressed->url = JUri::root(true).$image;
        } else if ($origFlag && !$exists) {
            $width = imagesx($im);
            $height = imagesy($im);
            if ($width <= $size && $height <= $size) {
                $w = $width;
                $h = $height;
            } else {
                $ratio = $width / $height;
                if ($width > $height) {
                    $w = $size;
                    $h = $size / $ratio;
                } else {
                    $h = $size;
                    $w = $size * $ratio;
                }
            }
            $out = imagecreatetruecolor($w, $h);
            if ($compressed->ext == 'png' || $compressed->ext == 'webp') {
                imagealphablending($out, false);
                imagesavealpha($out, true);
                $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
                imagefilledrectangle($out, 0, 0, $w, $h, $transparent);
            }
            imagecopyresampled($out, $im, 0, 0, 0, 0, $w, $h, $width, $height);
            if ($compressed->endExt == 'png') {
                $quality = 9 - round($quality / 11.111111111111);
            }
            $imageSave($out, $compressed->path, $quality);
            imagedestroy($out);
            imagedestroy($im);
        }
        if ($origFlag) {
            header('Location: '.$compressed->url);
        }
        exit;
    }

    public function compressImagelaptop()
    {
        $this->compressAdaptiveImage('laptop');
    }

    public function compressImagetb()
    {
        $this->compressAdaptiveImage('tablet');
    }

    public function compressImagetbpt()
    {
        $this->compressAdaptiveImage('tablet-portrait');
    }

    public function compressImagesm()
    {
        $this->compressAdaptiveImage('phone');
    }

    public function compressImagesmpt()
    {
        $this->compressAdaptiveImage('phone-portrait');
    }

    public function compressImage()
    {
        self::compressAdaptiveImage();
    }

    public function imageSave($type) {
        switch ($type) {
            case 'png':
                $imageSave = 'imagepng';
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
            case 'webp':
                $imageCreate = 'imagecreatefromwebp';
                break;
            default:
                $imageCreate = 'imagecreatefromjpeg';
        }
        return $imageCreate;
    }

    public function login()
    {
        $input = JFactory::getApplication()->input;
        $login = $input->get('ba_login', '', 'string');
        $password = $input->get('ba_password', '', 'string');
        $credentials = array('username' => $login, 'password' => $password);
        $msg = '';
        if (!JFactory::getApplication()->login($credentials)) {
            $msg = JText::_('LOGIN_ERROR');
        }
        echo $msg;
        exit;
    }

    public function createSystemPage()
    {
        $model = $this->getModel();
        $id = $model->createSystemPage();
        echo $id;
        exit;
    }

    public function createPage()
    {
        $model = $this->getModel();
        $id = $model->createPage();
        echo $id;
        exit;
    }

    public function getSession()
    {
        $session = JFactory::getSession();
        echo new JResponseJson($session->getState());
        exit;
    }

    public function save($key = NULL, $urlVar = NULL)
    {
        
    }
}