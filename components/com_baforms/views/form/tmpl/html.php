<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="ba-form-field-item ba-form-html-field" data-type="html">
    <div class="custom-html-wrapper"><?php echo $field->options->html; ?></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();