<?php
/**
* @package   BaGrid
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class pkg_gridboxInstallerScript
{
    public function install($parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__template_styles')
            ->set('home = 0')
            ->where('`client_id` = 0')
            ->where('`home` = 1');
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->update('#__template_styles')
            ->set('home = 1')
            ->where('`client_id` = 0')
            ->where('`template` = '.$db->quote('gridbox'));
        $db->setQuery($query)
            ->execute();
    }

	public function uninstall($parent){}

	public function update($parent){}

	public function preflight($type, $parent){}

    public function postflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions')
            ->set('enabled = 1')
            ->where('element = '.$db->quote('gridbox'))
            ->where('folder = '.$db->quote('system'))
            ->where('type = '.$db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
        $custom = JPATH_ROOT. '/templates/gridbox/css/custom.css';
        if (!JFile::exists($custom)) {
            JFile::write($custom, '');
        }
        $file = JPATH_ROOT. '/templates/gridbox/css/storage/responsive.css';
        if (JFile::exists($file)) {
            JFile::delete($file);
        }
        $conf = JFactory::getConfig();
        $options = ['defaultgroup' => '', 'storage' => $conf->get('cache_handler', ''),
            'caching' => true, 'cachebase' => $conf->get('cache_path', JPATH_SITE.'/cache')];
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