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
<div class="ba-form-field-item ba-form-slider-field <?php echo $field->options->suffix; ?>" data-type="slider"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if ($field->options->type == 'range') {
?>
            <div class="form-range-wrapper">
                <span class="ba-form-range-liner"></span>
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->min; ?>">
            </div>
<?php
        } else {
?>
            <div class="form-slider-wrapper">
                <span class="ba-form-range-liner" style="width: 100%;"></span>
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->min; ?>" data-index="0">
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->max; ?>" data-index="1">
            </div>
<?php
        }
?>
            <div class="form-slider-input-wrapper">
<?php
            if ($field->options->type == 'range') {
?>
                <input type="text" data-type="min" class="set-slider-range" value="<?php echo $field->options->min; ?>">
                <input type="number" value="<?php echo $field->options->min; ?>" step="<?php echo $field->options->step; ?>" data-type="range">
                <input type="text" data-type="max" class="set-slider-range" value="<?php echo $field->options->max; ?>">
<?php
            } else {
?>
                <input type="number" data-type="slider" step="<?php echo $field->options->step; ?>" data-index="0"
                    value="<?php echo $field->options->min; ?>">
                <input type="number" data-type="slider" step="<?php echo $field->options->step; ?>" data-index="1"
                    value="<?php echo $field->options->max; ?>">
                <input type="hidden" value="<?php echo $field->options->min.' '.$field->options->max; ?>">
<?php
            }
?>
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