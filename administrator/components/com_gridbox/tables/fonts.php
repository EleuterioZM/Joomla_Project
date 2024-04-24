<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class gridboxTableFonts extends JTable
{
    public function __construct(&$db)
	{
		parent::__construct('#__gridbox_fonts', 'id', $db);
	}
}
