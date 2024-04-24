<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$today = JHtml::date(time(), 'j F Y');
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$defaultValue = $field->options->default == 'today' ? $today : '';
$hidden = $field->options->hidden ? 'true' : 'false';
$readonly = $field->options->readonly ? ' ba-readonly-calendar' : '';
$type = !empty($field->options->type) ? ' calendar-range-type' : '';
?>
<div class="ba-form-field-item ba-form-calendar-field <?php echo $field->options->suffix ?>" data-type="calendar"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>" data-hidden="<?php echo $hidden; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container<?php echo $readonly.$type; ?>">
            <div class="calendar-field-wrapper">
                <i class="zmdi zmdi-calendar-alt"></i>
                <input type="text" readonly data-today="<?php echo $today; ?>" value="<?php echo $defaultValue; ?>"
                    placeholder="<?php echo htmlspecialchars($field->options->placeholder, ENT_QUOTES); ?>">
            </div>
            <span class="calendar-range-delimiter"><i class="zmdi zmdi-minus"></i></span>
            <div class="calendar-field-wrapper">
                <i class="zmdi zmdi-calendar-alt"></i>
                <input type="text" readonly data-today="<?php echo $today; ?>"
                    placeholder="<?php echo htmlspecialchars($field->options->placeholder, ENT_QUOTES); ?>">
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