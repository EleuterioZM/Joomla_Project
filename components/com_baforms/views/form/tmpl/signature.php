<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$className = $field->options->suffix;
if (in_array($field->key, self::$conditionLogic->hidden)) {
    $className .= ' hidden-condition-field';
}
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
?>
<div class="ba-form-field-item ba-form-signature-field <?php echo $className; ?>" data-type="signature"
    <?php echo $field->options->required ? 'data-required="true"' : ''; ?>>
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <canvas class="ba-signature-canvas" data-bg="<?php echo self::$design->field->background->color; ?>"
                data-color="<?php echo self::$design->field->typography->color; ?>"></canvas>
            <span class="ba-clear-signature-canvas"><?php echo JText::_('CLEAR'); ?></span>
            <textarea name="<?php echo $field->id; ?>" data-field-id="<?php echo $field->key; ?>" style="display: none !important;"></textarea>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();