<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

trait DateTrait
{
	public static $dateFormat;

	public static function formatDate($date)
    {
        $date = JHtml::date($date, self::$dateFormat, null);

        return $date;
    }
}