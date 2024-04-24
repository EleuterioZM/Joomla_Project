<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewCreate extends JViewLegacy
{
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0);
        $src = 'index.php?option=com_gridbox&view=editor';
        if ($id != 0) {
        	$src .= '&app_id='.$id;
        }
        $src .= '&tmpl=component&id=';
        header('Location: '.JUri::root().$src);
        exit;
    }
}