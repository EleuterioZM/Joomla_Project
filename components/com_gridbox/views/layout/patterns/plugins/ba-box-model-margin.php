<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="ba-box-model-margin">
    <div class="ba-box-model-margin-top"></div>
    <div class="ba-box-model-margin-left"></div>
    <div class="ba-box-model-margin-bottom"></div>
    <div class="ba-box-model-margin-right"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();