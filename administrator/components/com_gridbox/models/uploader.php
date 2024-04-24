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

class gridboxModelUploader extends JModelLegacy
{
    public function getUploader($dir = '')
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/uploader.php';
        $uploader = new uploaderHelper($dir);

        return $uploader;
    }
}
