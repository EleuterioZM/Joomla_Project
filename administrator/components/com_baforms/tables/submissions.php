<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class formsTableSubmissions extends JTable
{
    function __construct(&$db) 
    {
        parent::__construct('#__baforms_submissions', 'id', $db);
    }
}