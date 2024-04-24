<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$general = gridboxHelper::$store->general;
include JPATH_ROOT.'/components/com_gridbox/libraries/php/tfpdf/pdf.php';
$pdf = new pdf('Portrait', 'mm', 'A4');
$pdf->store = gridboxHelper::$store;
$path = $pdf->create($this->item, $general);
exit;