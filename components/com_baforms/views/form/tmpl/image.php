<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();

$style = '--image-field-width:'.$field->options->width.$field->options->units->width.'; --image-field-align:'.$field->options->align.';';
?>
<div class="ba-form-field-item ba-form-image-field <?php echo $field->options->suffix ?>" data-type="image">
    <div class="ba-image-wrapper" data-field-id="<?php echo $field->key; ?>" style="<?php echo $style; ?>">
        <img src="<?php echo JUri::root().$field->options->src; ?>" alt="<?php echo $field->options->alt; ?>">
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();