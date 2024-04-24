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
<div class="ba-form-field-item ba-form-radio-field <?php echo $field->options->suffix ?>" data-type="radio"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <div class="ba-form-checkbox-group-wrapper" style="--checkbox-field-count:<?php echo $field->options->count; ?>;">
<?php
            $index = 1;
            foreach ($field->options->items as $item) {
                $checkboxClassName = !empty($item->image) ? ' checkbox-image-wrapper' : '';
                $checkboxClassName .= !empty($item->image) && $item->default ? ' checked-image-container' : '';
                if ($index == $field->options->count) {
                    $checkboxClassName .= ' last-row-checkbox-wrapper';
                    $index = 0;
                }
                $index++;
?>
                <div class="ba-form-checkbox-wrapper<?php echo $checkboxClassName; ?>">
<?php
                if (!empty($item->image)) {
?>
                    <div class="ba-checkbox-image"><img src="<?php echo JUri::root().$item->image; ?>"></div>
<?php
                }
?>
                    <div class="ba-checkbox-wrapper">
                        <span class="ba-checkbox-title">
                            <span class="ba-form-checkbox-title" contenteditable="true"><?php echo $item->title; ?></span>
                        </span>
                        <label class="ba-form-radio">
                            <input type="radio" name="<?php echo $field->key; ?>" <?php echo $item->default ? ' checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>
                </div>
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