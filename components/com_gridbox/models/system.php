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

class gridboxModelSystem extends JModelItem
{
    public $fonts;
    public $name;

    public function getTable($type = '', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getItem($id = null)
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $item = gridboxHelper::getSystemParams($id);
        
        return $item;
    }
}
