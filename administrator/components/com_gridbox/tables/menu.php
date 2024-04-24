<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxTableMenu extends JTableMenu
{
	public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
}
