<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
?>
<div class="ba-form-field-item ba-form-rating-field <?php echo $field->options->suffix ?>" data-type="rating"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <div class="ba-form-rating-group-wrapper <?php echo $field->options->layout; ?>-layout">
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->key; ?>" value="1">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('VERY_UNSATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->key; ?>" value="2">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('UNSATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->key; ?>" value="3">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('NEUTRAL'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->key; ?>" value="4">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SATISFIED'); ?></span>
                </label>
                <label class="ba-form-rating">
                    <input type="radio" name="<?php echo $field->key; ?>" value="5">
                    <span></span>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('VERY_SATISFIED'); ?></span>
                </label>
            </div>
        </div>
    </div>
    <div class="ba-edit-item close-all-modals">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip ba-top tooltip-delay ba-hide-element"><?php echo JText::_('ITEM'); ?></span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('COPY_ITEM'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element">
                    <?php echo JText::_('DELETE_ITEM'); ?>
                </span>
            </span>
            <span class="ba-edit-text ba-hide-element"><?php echo JText::_('INPUT'); ?></span>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();