<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class gridboxModelDashboard extends JModelList
{
    private $filePath;
    private $fileUrl;
    private $fileTypes;

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'state'
            );
        }
        parent::__construct($config);
    }

    public function getFiletypes()
    {
        $files = gridboxHelper::$website->file_types;
        $array = explode(',', $files);
        foreach ($array as $key => $value) {
            $value = trim($value);
            $value = strtolower($value);
            $array[$key] = $value;
        }
        $this->fileTypes = $array;
    }

    protected function getFileSize($size)
    {
        $size = $size / 1024;
        $size = floor($size);
        if ($size >= 1024) {
            $size = $size / 1024;
            $size = floor($size);
            $size = (string)$size .' MB';
        } else {
            $size = (string)$size .' KB';
        }

        return $size;
    }

    public function getFiles($directory = '/')
    {
        if (empty($this->filePath)) {
            $this->filePath = JPATH_ROOT.'/'.IMAGE_PATH;
            $this->fileUrl = JUri::root().IMAGE_PATH;
        }
        $dir = $this->filePath.$directory;
        $url = $this->fileUrl.$directory;
        $files = JFolder::files($dir);
        $items = array();
        $types = $this->fileTypes;
        foreach ($files as $file) {
            $ext = strtolower(JFile::getExt($file));
            if (in_array($ext, $types)) {
                $obj = new stdClass();
                $obj->ext = $ext;
                $obj->title = $file;
                $obj->url = $url.$file;
                $obj->size = $this->getFileSize(filesize($dir.$file));
                $obj->path = $directory.$file;
                $obj->modify = filemtime($dir.$file);
                $obj->date = date('Y-m-d-H-i-s', $obj->modify);
                $items[] = $obj;
            }
        }
        $folders = JFolder::folders($dir);
        foreach ($folders as $folder) {
            if ($folder != 'attachment' && $folder != 'bagallery' && $folder != 'baforms') {
                $array = $this->getFiles($directory.$folder.'/');
                $items = array_merge($items, $array);
            }
        }

        return $items;
    }

    public function getComments()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = $db->getQuery(true)
            ->select('c.*, p.title, u.email AS user_email')
            ->from('#__gridbox_comments AS c')
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->leftJoin('`#__gridbox_pages` AS p ON '.$db->quoteName('p.id').' = '.$db->quoteName('c.page_id'))
            ->order('id desc');
        $db->setQuery($query, 0, 10);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function getReviews()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = $db->getQuery(true)
            ->select('c.*, p.title, u.email AS user_email')
            ->from('#__gridbox_reviews AS c')
            ->where('c.parent = 0')
            ->leftJoin('#__users AS u ON u.id = c.user_id')
            ->leftJoin('`#__gridbox_pages` AS p ON '.$db->quoteName('p.id').' = '.$db->quoteName('c.page_id'))
            ->order('id desc');
        $db->setQuery($query, 0, 10);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function getPages()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('p.id, p.title, p.hits, p.page_category, p.intro_image, a.type')
            ->from('#__gridbox_pages AS p')
            ->where('p.page_category <> '.$db->Quote('trashed'))
            ->leftJoin('#__gridbox_app AS a ON p.app_id = a.id')
            ->order('p.saved_time DESC');
        $db->setQuery($query, 0, 10);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public function setFilters()
    {
        $this::populateState();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        parent::populateState('id', 'desc');
    }
    
}