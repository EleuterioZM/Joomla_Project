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

class gridboxModelIcons extends JModelItem
{
    public $fonts;
    public $name;

    public function getTable($type = 'Fonts', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getItem($id = null)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('`#__gridbox_custom_user_icons`');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $groups = array();
        foreach ($items as $item) {
            if (!isset($groups[$item->path])) {
                $groups[$item->path] = new stdClass();
                $groups[$item->path]->items = array();
                $groups[$item->path]->title = $item->group;
                $groups[$item->path]->css = JUri::root().'templates/gridbox/library/icons/custom-icons/'.$item->path.'/font.css';
            }
            $groups[$item->path]->items[] = $item;
        }
        
        return $groups;
    }
    
    public function delete()
    {
        $input = JFactory::getApplication()->input;
        $pks = $input->get('icons_id', array(), 'array');
        $db = JFactory::getDbo();
        $array = array();
        foreach ($pks as $id) {
            $query = $db->getQuery(true)
                ->select('path')
                ->from('#__gridbox_custom_user_icons')
                ->where('id = '.$db->quote($id));
            $db->setQuery($query);
            $path = $db->loadResult();
            if (!in_array($path, $array)) {
                $array[] = $path;
            }
            $db->setQuery('DELETE FROM `#__gridbox_custom_user_icons` WHERE `id` = '.$db->quote($id));
            $db->execute();
        }
        foreach ($array as $key => $path) {
            $query = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from('#__gridbox_custom_user_icons')
                ->where('path = '.$db->quote($path));
            $db->setQuery($query);
            $count = $db->loadResult();
            if ($count == 0) {
                $dir = JPATH_ROOT.'/templates/gridbox/library/icons/custom-icons/'.$path;
                if (JFolder::exists($dir)) {
                    gridboxHelper::deleteFolder($dir);
                }
            }
        }
    }

    public function getFolders($dir)
    {
        $path = JPATH_ROOT.'/templates/gridbox/library/icons/custom-icons/tmp/'.$dir;
        $array = array();
        $files = JFolder::files($path);
        $folders = JFolder::folders($path);
        foreach ($files as $file) {
            $ext = JFile::getExt($file);
            if ($ext == 'css') {
                $array[] = gridboxHelper::readFile($path.'/'.$file);
            } else if ($ext == 'ttf' || $ext == 'woff') {
                $dst = JPATH_ROOT.'/templates/gridbox/library/icons/custom-icons/'.$this->name.'/'.$file;
                $this->fonts[$ext] = $file;
                JFile::move($path.'/'.$file, $dst);
            }
        }
        foreach ($folders as $folder) {
            $array1 = $this->getFolders($dir.'/'.$folder);
            $array = array_merge($array, $array1);
        }

        return $array;
    }

    public function parseCss($css)
    {
        preg_match_all( '/(?ims)([a-z0-9\s\.\:#_\-@,]+)\{([^\}]*)\}/', $css, $arr);
        $exp = '/[a-zA-Z0-9_\-]+:before|[a-zA-Z0-9_\-]+:after|[a-zA-Z0-9_\-]+::before|[a-zA-Z0-9_\-]+::after/';
        $result = array();
        foreach ($arr[0] as $i => $x) {
            $selector = trim($arr[1][$i]);
            $rules = explode(';', trim($arr[2][$i]));
            $rules_arr = array();
            foreach ($rules as $strRule) {
                if (!empty($strRule)){
                    $rule = explode(":", $strRule);
                    $rules_arr[trim($rule[0])] = trim($rule[1]);
                }
            }
            $selectors = explode(',', trim($selector));
            foreach ($selectors as $strSel){
                if (!empty($strSel) && preg_match($exp, $strSel, $matches)) {
                    $key = '.'.$matches[0];
                    if (isset($rules_arr['content'])) {
                        $content = str_replace(' ', '', $rules_arr['content']);
                    }
                    if (!isset($rules_arr['content']) || empty($content) || $content == '""' ||
                        $content == "''" || $content == 'none'|| $content == "[" || $content == ']') {
                        continue;
                    }
                    $result[$key] = $rules_arr;
                }
            }
        }

        return $result;
    }

    public function installIcons($name, $group)
    {
        $this->name = $name;
        $this->fonts = array();
        $files = $this->getFolders($name);
        if (count($files) > 0 && (isset($this->fonts['woff']) || isset($this->fonts['ttf']))) {
            $str = "@font-face {\n\tfont-family: ".$name.";\n\tsrc: url(\"".$this->fonts['woff'];
            $str .= "\") format(\"woff\"), url(\"".$this->fonts['ttf']."\") format(\"truetype\");\n}";
            $icons = array();
            foreach ($files as $file) {
                $data = $this->parseCss($file);
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $selector = preg_replace('/\.\w+/', '.'.$name, $key);
                        if (in_array($selector, $icons)) {
                            unset($data[$key]);
                            continue;
                        }
                        $data[$key]['font-family'] = $name;
                        $data[$key]['font-weight'] = 'normal';
                        $data[$key]['font-style'] = 'normal';
                        if (strpos($file, 'font-smoothing')) {
                            $data[$key]['-webkit-font-smoothing'] = 'antialiased';
                            $data[$key]['-moz-osx-font-smoothing'] = 'grayscale';
                        }
                        $icons[] = $selector;
                        $array = $data[$key];
                        unset($data[$key]);
                        $data[$selector] = $array;
                    }
                    foreach ($data as $key => $value) {
                        $str .= "\n".$key." {";
                        foreach ($value as $ind => $property) {
                            $str .= "\n\t".$ind.": ".$property.";";
                        }
                        $str .= "\n}";
                    }
                }
            }
            JFile::write(JPATH_ROOT.'/templates/gridbox/library/icons/custom-icons/'.$name.'/font.css', $str);
            $db = JFactory::getDbo();
            foreach ($icons as $icon) {
                preg_match('/[a-zA-Z0-9_\-]+:before|[a-zA-Z0-9_\-]+:after|[a-zA-Z0-9_\-]+::before|[a-zA-Z0-9_\-]+::after/', $icon, $match);
                $icon = $match[0];
                $title = str_replace('::before', '', $icon);
                $title = str_replace('::after', '', $title);
                $title = str_replace(':before', '', $title);
                $title = str_replace(':after', '', $title);
                $obj = new stdClass();
                $obj->title  = $title;
                $obj->path = $name;
                $obj->group = $group;
                $db->insertObject('#__gridbox_custom_user_icons', $obj);
            }
        } else {
            gridboxHelper::deleteFolder(JPATH_ROOT.'/templates/gridbox/library/icons/custom-icons/'.$name);
        }
    }
}
