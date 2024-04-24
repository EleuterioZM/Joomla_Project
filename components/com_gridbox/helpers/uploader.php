<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class uploaderHelper
{
    public $dir;
    public $limit;
    public $direction;
    public $sorting;
    public $page;
    public $input;
    public $pages;
    public $search;
    public $types;
    public $images;

    public function __construct($dir = '')
    {
        $session = JFactory::getSession();
        $this->dir = empty($dir) ? IMAGE_PATH : $dir;
        $this->limit = $session->get('media-manager-limit', 25);
        $this->sorting = $session->get('media-manager-sorting', 'name');
        $this->direction = $session->get('media-manager-direction', 'ASC');
        $this->input = JFactory::getApplication()->input;
        $this->page = 0;
        $this->pages = 1;
        $this->types = $this->getTypes();
        $this->images = ['jpg', 'png', 'gif', 'svg', 'jpeg', 'ico', 'webp'];
    }

    public function getTypes()
    {
        $array = explode(',', gridboxHelper::$website->file_types);
        foreach ($array as $key => $value) {
            $array[$key] = strtolower(trim($value));
        }

        return $array;
    }

    public function makeSafe($file)
    {
        $file = rtrim($file, '.');
        if (function_exists('transliterator_transliterate') && function_exists('iconv')) {
            $file = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $file);
            $file = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $file);
        }
        $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');

        return trim(preg_replace($regex, '', $file));
    }

    public function prepareFilename($name, $ext)
    {
        $dir = JPATH_ROOT.'/'.$this->dir.'/';
        $name = strtolower($name);
        $name = str_replace('.'.$ext, '', $name);
        $fileName = gridboxHelper::replace($name);
        $fileName = $this->makeSafe($fileName);
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

        return $fileName;
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

    public function checkExif($source, $file, $string, $img, $ext)
    {
        if (($ext == 'jpg' || $ext == 'jpeg') && function_exists('exif_read_data')) {
            $path = JPATH_ROOT.'/tmp/'.$file;
            if ($string) {
                file_put_contents($path, $source);
            } else {
                move_uploaded_file($source, $path);
            }
            $exif = @exif_read_data($path);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $img = imagerotate($img, 180, 0);
                        break;
                    case 6:
                        $img = imagerotate($img, -90, 0);
                        break;
                    case 8:
                        $img = imagerotate($img, 90, 0);
                        break;
                }
            }
            unlink($path);
        }

        return $img;
    }

    public function getExt($file)
    {
        $dot = strrpos($file, '.');
        if ($dot === false) {
            return '';
        }
        $ext = substr($file, $dot + 1);
        if (strpos($ext, '/') !== false) {
            return '';
        }

        return strtolower($ext);
    }

    public function canComress($ext)
    {
        $array = array('png', 'jpg', 'jpeg', 'webp');

        return gridboxHelper::$website->upload_compress_images == 1 && in_array($ext, $array);
    }

    public function compressImage($source, $dir, $file, $ext, $string = true)
    {
        $website = gridboxHelper::$website;
        $endExt = $ext;
        $gd_info = gd_info();
        if ($website->upload_images_webp == 1 && $gd_info['WebP Support']) {
            $name = basename($file);
            $name = JFile::stripExt($name);
            $file = str_replace($name.'.'.$ext, $name.'.webp', $file);
            $endExt = 'webp';
        }
        $path = $dir.$file;
        $imageSave = $this->imageSave($endExt);
        if ($string) {
            $img = imagecreatefromstring($source);
        } else {
            $imageCreate = $this->imageCreate($ext);
            $img = $imageCreate($source);
        }
        $img = $this->checkExif($source, $file, $string, $img, $ext);
        $width = imagesx($img);
        $height = imagesy($img);
        $size = $website->upload_images_size;
        $quality = $website->upload_images_quality;
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
        if ($ext == 'png' || $ext == 'webp') {
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
            imagefilledrectangle($out, 0, 0, $w, $h, $transparent);
        }
        imagecopyresampled($out, $img, 0, 0, 0, 0, $w, $h, $width, $height);
        if ($endExt == 'png') {
            $quality = 9 - round($quality / 11.111111111111);
        }
        $imageSave($out, $path, $quality);
        imagedestroy($out);
        imagedestroy($img);

        return $file;
    }

    public function uploadFile()
    {
        $file = $this->input->files->get('file', [], 'array');
        $response = new stdClass();
        if (isset($file['error']) && $file['error'] == 0 && ($ext = $this->getExt($file['name']))) {
            $dir = JPATH_ROOT.'/'.$this->dir.'/';
            $fileName = $this->prepareFilename($file['name'], $ext);
            if ($this->canComress($ext)) {
                $fileName = $this->compressImage($file['tmp_name'], $dir, $fileName, $ext, false);
                $ext = $this->getExt($fileName);
            } else {
                move_uploaded_file($file['tmp_name'], $dir.$fileName);
            }
            $response = $this->getImageObject($this->dir, $ext, $fileName);
        }

        return $response;
    }

    public function uploadVideoImage()
    {
        $id = $this->input->get('id', '', 'string');
        $type = $this->input->get('type', '', 'string');
        $ext = 'jpg';
        if ($type == 'youtube') {
            $url = 'https://img.youtube.com/vi/'.$id.'/maxresdefault.jpg';
        } else {
            $url = 'https://vumbnail.com/'.$id.'.jpg';
        }
        $fileName = $this->prepareFilename($id, $ext);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $dir = JPATH_ROOT.'/'.$this->dir.'/';
        if ($this->canComress($ext)) {
            $fileName = $this->compressImage($body, $dir, $fileName, $ext);
            $ext = $this->getExt($fileName);
        } else {
            $file = fopen($dir.$fileName, 'wb');
            fwrite($file, $body);
            fclose($file);
        }
        curl_close($curl);
        $response = $this->getImageObject($this->dir, $ext, $fileName);

        return $response;
    }

    public function multipleMove()
    {
        $array = $this->input->get('array', array(), 'array');
        foreach ($array as $path) {
            $name = basename($path);
            if (is_dir(JPATH_ROOT.'/'.$path)) {
                JFolder::move(JPATH_ROOT.'/'.$path, JPATH_ROOT.'/'.$this->dir.'/'.$name);
            } else {
                JFile::move(JPATH_ROOT.'/'.$path, JPATH_ROOT.'/'.$this->dir.'/'.$name);
            }
        }
        $response = $this->setTree();
        
        return $response;
    }

    public function multipleDelete()
    {
        $array = $this->input->get('array', array(), 'array');
        foreach ($array as $path) {
            if (is_dir(JPATH_ROOT.'/'.$path)) {
                gridboxHelper::deleteFolder(JPATH_ROOT.'/'.$path);
            } else {
                unlink(JPATH_ROOT.'/'.$path);
            }
        }
        $response = $this->setTree();
        
        return $response;
    }

    public function contextDelete()
    {
        if (is_dir(JPATH_ROOT.'/'.$this->dir)) {
            gridboxHelper::deleteFolder(JPATH_ROOT.'/'.$this->dir);
        } else {
            unlink(JPATH_ROOT.'/'.$this->dir);
        }
        $response = $this->setTree();
        
        return $response;
    }

    public function createFolder()
    {
        $name = $this->input->get('name', '', 'string');
        $name = str_replace(' ', '-', $name);
        mkdir(JPATH_ROOT.'/'.$this->dir.'/'.$name, 0755);
        $response = $this->setTree();
        
        return $response;
    }

    public function setTree()
    {
        $this->dir = IMAGE_PATH;
        $response = new stdClass();
        $response->tree = $this->getFoldersTree();
        
        return $response;
    }

    public function rename()
    {
        $name = $this->input->get('name', '', 'string');
        $name = str_replace(' ', '-', $name);
        if (file_exists(JPATH_ROOT.'/'.$this->dir)) {
            rename(JPATH_ROOT.'/'.$this->dir, JPATH_ROOT.'/'.$name);
        }
        $response = $this->setTree();
        
        return $response;
    }

    public function loadFolder()
    {
        $response = new stdClass();
        $response->breadcrumb = $this->getbreadcrumb();
        $response->table = $this->getItemsTable();
        $response->paginator = $this->getPaginator();

        return $response;
    }

    public function setPage()
    {
        $this->page = $this->input->get('page', 0, 'int');
        $this->search = $this->input->get('search', '', 'string');
        $response = $this->loadFolder();

        return $response;
    }

    public function setSorting()
    {
        $this->direction = $this->input->get('direction', 'ASC', 'string');
        $this->sorting = $this->input->get('sorting', 'name', 'string');
        $session = JFactory::getSession();
        $session->set('media-manager-direction', $this->direction);
        $session->set('media-manager-sorting', $this->sorting);
        $response = $this->setPage();

        return $response;
    }

    public function setLimit()
    {
        $this->limit = $this->input->get('limit', 1, 'int');
        $session = JFactory::getSession();
        $session->set('media-manager-limit', $this->limit);
        $response = $this->setPage();

        return $response;
    }

    public function getFileSize($size)
    {
        $size = floor($size / 1024);
        if ($size >= 1024) {
            $size = floor($size / 1024);
            $filesize = (string)$size .' MB';
        } else {
            $filesize = (string)$size .' KB';
        }

        return $filesize;
    }

    public function checkExt($ext)
    {
        return in_array($ext, $this->types);
    }

    public function isImage($ext)
    {
        return in_array($ext, $this->images);
    }

    public function searchItems($directory)
    {
        $dir = JPATH_ROOT.'/'.$directory.'/';
        $files = scandir($dir);
        $data = new stdClass();
        $data->folders = [];
        $data->images = [];
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $ext = $this->getExt($dir.$file);
            $isDir = is_dir($dir.$file);
            if ($isDir && strpos($file, $this->search) !== false) {
                $data->folders[] = $this->getFolderObject($directory, $file);
            } else if (!$isDir && $this->checkExt($ext) && strpos($file, $this->search) !== false) {
                $data->images[] = $this->getImageObject($directory, $ext, $file);
            }
            if ($isDir) {
                $object = $this->searchItems($directory.'/'.$file);
                $data->folders = array_merge($data->folders, $object->folders);
                $data->images = array_merge($data->images, $object->images);
            }
        }
        
        return $data;
    }

    public function getFolderObject($dir, $file)
    {
        $folder = new stdClass();
        $folder->path = $dir.'/'.$file;
        $folder->name = $file;

        return $folder;
    }

    public function getImageObject($dir, $ext, $file)
    {
        $image = new stdClass;
        $image->ext = $ext;
        $image->name = $file;
        $image->folder = $dir.'/';
        $image->path = $dir.'/'.$file;
        $image->url = $image->path;
        $image->size = filesize(JPATH_ROOT.'/'.$image->path);
        $image->time = filemtime(JPATH_ROOT.'/'.$image->path);
        $image->modified = JHtml::date($image->time, 'Y-m-d H:i');

        return $image;
    }

    public function scanDirectory()
    {
        $dir = JPATH_ROOT.'/'.$this->dir.'/';
        $files = scandir($dir);
        $data = new stdClass();
        $data->folders = [];
        $data->images = [];
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $ext = $this->getExt($dir.$file);
            if (is_dir($dir.$file)) {
                $data->folders[] = $this->getFolderObject($this->dir, $file);
            } else if ($this->checkExt($ext)) {
                $data->images[] = $this->getImageObject($this->dir, $ext, $file);
            }
        }

        return $data;
    }

    public function sortModifiedASC($a, $b)
    {
        if ($a->time == $b->time) {
            return 0;
        }

        return ($a->time < $b->time) ? -1 : 1;
    }

    public function sortModifiedDESC($a, $b)
    {
        if ($a->time == $b->time) {
            return 0;
        }

        return ($a->time < $b->time) ? 1 : -1;
    }

    public function sortSizeASC($a, $b)
    {
        if ($a->size == $b->size) {
            return 0;
        }

        return ($a->size < $b->size) ? -1 : 1;
    }

    public function sortSizeDESC($a, $b)
    {
        if ($a->size == $b->size) {
            return 0;
        }

        return ($a->size < $b->size) ? 1 : -1;
    }

    public function sortNameDESC($a, $b)
    {
        return strcmp($a->name, $b->name) * -1;
    }

    public function getItems()
    {
        if (!empty($this->search)) {
            $data = $this->searchItems($this->dir);
        } else {
            $data = $this->scanDirectory();
        }
        if ($this->sorting != 'name' || $this->direction != 'ASC') {
            $func = 'sort'.ucfirst($this->sorting).$this->direction;
            usort($data->images, array($this, $func));
        }
        $items = array_merge($data->folders, $data->images);
        
        return $items;
    }

    public function getFolders($dir = '')
    {
        if (empty($dir)) {
            $dir = $this->dir;
        }
        $files = scandir(JPATH_ROOT.'/'.$dir);
        $items = array();
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $path = $dir.'/'.$file;
            if (is_dir(JPATH_ROOT.'/'.$path)) {
                $folder = new stdClass();
                $folder->path = $path;
                $folder->name = $file;
                $folder->childs = $this->getFolders($path);
                $items[] = $folder;
            }
        }

        return $items;
    }

    public function getItemsTable()
    {
        $items = $this->getItems();
        if ($this->limit != 1) {
            $this->pages = ceil(count($items) / $this->limit);
            $items = array_slice($items, $this->page * $this->limit, $this->limit);
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/uploader/table.php';

        return $out;
    }

    public function getPaginator()
    {
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/uploader/paginator.php';

        return $out;
    }

    public function getFoldersTree($folders = null)
    {
        if (!$folders) {
            $folders = $this->getFolders();
        }
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/uploader/folders-tree.php';

        return $out;
    }

    public function getbreadcrumb()
    {
        $folders = explode('/', $this->dir);
        $parts = [];
        $n = count($folders) - 1;
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/uploader/breadcrumb.php';

        return $out;
    }
}