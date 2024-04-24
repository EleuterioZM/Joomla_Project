<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class formsTableForms extends JTable
{
    function __construct(&$db) 
    {
        parent::__construct('#__baforms_forms', 'id', $db);
    }
}