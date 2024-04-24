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
$drag = $field->options->drag ? ' drag-drop-upload-file' : '';
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$attributes = $field->options->required ? ' required' : '';
if ($field->options->multiple) {
    $attributes .= ' multiple data-count="'.$field->options->count.'"';
}
?>
<div class="ba-form-field-item ba-form-upload-field <?php echo $className; ?>" data-type="upload">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <div class="upload-file-input<?php echo $drag; ?>">
<?php
            if ($field->options->drag) {
?>
                <i class="ba-form-icons ba-icon-cloud-upload"></i>
                <span class="upload-file-drag-drop-title"><?php echo JText::_('DRAG_AND_DROP_HERE'); ?></span>
<?php
            }
?>
                <span class="upload-file-btn"><?php echo JText::_('BROWSE_FILES'); ?></span>
<?php
            if (!$field->options->drag) {
?>
                <span class="upload-file-empty-text"><?php echo JText::_('NO_FILE_CHOSEN'); ?></span>
<?php
            }
?>
                <input type="file" style="display: none !important;" class="ba-forms-attachment"
                    data-field-id="<?php echo $field->key; ?>" data-id="<?php echo $field->id;?>"
                    aria-labelledby="label-<?php echo $field->id; ?>"
                    data-size="<?php echo $field->options->filesize * 1000; ?>" data-types="<?php echo $field->options->types; ?>"
                    <?php echo $attributes; ?>>
                <textarea readonly name="<?php echo $field->id;?>"
                    aria-labelledby="label-<?php echo $field->id; ?>"
                    style="display: none !important;"></textarea>
            </div>
            <div class="ba-forms-xhr-attachment-wrapper" data-type="file"></div>
            <div class="ba-forms-xhr-attachment-wrapper" data-type="image"></div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();