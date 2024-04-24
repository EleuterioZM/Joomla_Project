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
<div class="ba-form-field-item ba-form-text-field" data-type="text">
    <div class="text-content-wrapper" data-field-id="<?php echo $field->key; ?>"><?php echo $field->options->html; ?></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();