<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class pkg_baformsInstallerScript
{
    public function cleardir($dir)
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    filetype($dir."/".$object) == "dir" ? $this->cleardir($dir."/".$object) : unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function install($parent)
    {
        
    }

	public function uninstall($parent)
    {
        $dir = JPATH_ROOT.'/images/baforms';
        $this->cleardir($dir);
    }

	public function update($parent)
    {
        $dir = JPATH_ROOT.'/components/com_baforms/libraries/pdf-submissions/font/unifont';
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != ".." && $object != 'ttfonts.php') {
                unlink($dir."/".$object);
            }
        }
    }

	public function preflight($type, $parent) {}

    public function postflight($type, $parent) {
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
        $query->update('#__extensions')
            ->set('enabled = 1')
            ->where('element='.$db->quote('baforms'))
            ->where('folder='.$db->quote('editors-xtd'));
        $db->setQuery($query);
		$db->execute();
        $query = $db->getQuery(true);
        $query->update('#__extensions')
            ->set('enabled = 1')
            ->where('element='.$db->quote('baforms'))
            ->where('folder='.$db->quote('system'));
        $db->setQuery($query);
		$db->execute();
		$conf = JFactory::getConfig();
		$options = array( 'defaultgroup' => '', 'storage' => $conf->get('cache_handler', ''),
						  'caching' => true, 'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache') );
	  	$cache = JCache::getInstance('', $options);
	  	$data = $cache->getAll();
	  	if ($data) {
	  		foreach ($data as $item) {
	  			$cache->clean($item->group);
	  		}
	  	}
		$cache = JFactory::getCache('');
		$cache->gc();
    }
}