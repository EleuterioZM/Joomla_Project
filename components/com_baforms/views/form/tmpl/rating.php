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
<div class="ba-form-field-item ba-form-rating-field <?php echo $className; ?>" data-type="rating">
    <fieldset class="ba-input-wrapper">
        <legend class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </legend>
        <div class="ba-field-container">
            <div class="ba-form-rating-group-wrapper <?php echo $field->options->layout; ?>-layout"
                <?php echo $field->options->required ? 'data-required="true"' : ''; ?>>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->id; ?>" value="1" data-field-id="<?php echo $field->key; ?>" data-price="1">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('VERY_UNSATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->id; ?>" value="2" data-field-id="<?php echo $field->key; ?>" data-price="2">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('UNSATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->id; ?>" value="3" data-field-id="<?php echo $field->key; ?>" data-price="3">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('NEUTRAL'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->id; ?>" value="4" data-field-id="<?php echo $field->key; ?>" data-price="4">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->id; ?>" value="5" data-field-id="<?php echo $field->key; ?>" data-price="5">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('VERY_SATISFIED'); ?></span>
                </label>
            </div>
        </div>
    </fieldset>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();