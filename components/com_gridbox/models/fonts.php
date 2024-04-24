<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.http.http');
jimport('joomla.filesystem.folder');

class gridboxModelFonts extends JModelItem
{
    public function getTable($type = 'Fonts', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getItem($id = null)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('font, styles, id, custom_src')
            ->from('`#__gridbox_fonts`')
            ->order($db->quoteName('font') . ' ASC');
        $db->setQuery($query);
        $item = $db->loadObjectList();
        
        return $item;
    }

    public function getGoogleFonts()
    {
        $file = JPATH_COMPONENT.'/libraries/google-fonts/font.json';
        $str = gridboxHelper::readFile($file);
        $obj = json_decode($str);

        return $obj;
    }
    
    public function delete($pks)
    {
        $db = JFactory::getDbo();
        foreach ($pks as $id) {
            $query = $db->getQuery(true)
                ->select('custom_src, font')
                ->from('#__gridbox_fonts')
                ->where('id = '.$db->quote($id));
            $db->setQuery($query);
            $obj = $db->loadObject();
            if (!empty($obj->custom_src) && $obj->custom_src != 'web-safe-fonts') {
                $dir = JPATH_ROOT. '/templates/gridbox/library/fonts/';
                if (JFile::exists($dir.$obj->custom_src)) {
                    JFile::delete($dir.$obj->custom_src);
                }
                $folder = str_replace('+', '-', $obj->font);
                $files = JFolder::files($dir.$folder);
                if ($files && count($files) == 0) {
                    gridboxHelper::deleteFolder($dir.$folder);
                }
            }
            $query = $db->getQuery(true)
                ->delete('#__gridbox_fonts')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function refreshList()
    {
        $file = JPATH_COMPONENT.'/libraries/google-fonts/font.json';
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBNJxvxv5f7Xp-I0ZkmCO-Y5JyggF5AHbg';
        $http = new JHttp();
        $obj = $http->get($url);
        $fonts = json_decode($obj->body);
        $list = [];
        foreach ($fonts->items as $font) {
            $object = new stdClass();
            $object->family = $font->family;
            $object->variants = $font->variants;
            $list[] = $object;
        }
        $str = json_encode($list);
        JFile::write($file, $str);
        $str = gridboxHelper::createFontString($list);

        return $str;
    }

    public function addFont($custom_src = '')
    {
        $input = JFactory::getApplication()->input;
        $font = $input->get('font_family', '', 'string');
        $style = $input->get('font_style', '', 'string');
        $webSafeFonts = $input->get('web_safe_fonts', '', 'string');
        if (empty($style)) {
            $style = 400;
        }
        if ($webSafeFonts == 'web-safe-fonts') {
            $custom_src = $webSafeFonts;
        }
        $font = trim($font);
        $font = str_replace(' ', '+', $font);
        if ($this->checkFont($font, $style)) {
            $table = $this->getTable();
            $array = array('font' => $font, 'styles' => $style, 'custom_src' => $custom_src);
            $table->bind($array);
            if ($table->store()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function checkFont($font, $style)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_fonts')
            ->where('`font` = ' .$db->quote($font))
            ->where('`styles` = ' .$db->quote($style));
        $db->setQuery($query);
        $id = $db->loadResult();

        return empty($id);
    }
}
