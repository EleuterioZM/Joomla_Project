<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

include JPATH_ROOT.'/components/com_baforms/libraries/pdf-submissions/pdf.php';
$pdf = new pdf();
$pdf->saveSubmission($this->submission);
exit();
?>