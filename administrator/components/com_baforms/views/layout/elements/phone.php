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
$default = baformsHelper::$countries->{$field->options->default};
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
?>
<div class="ba-form-field-item ba-form-phone-field <?php echo $field->options->suffix ?>" data-type="phone"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <div class="ba-phone-countries-wrapper">
                <div class="ba-phone-selected-country">
                    <span class="ba-phone-flag ba-phone-flag-<?php echo $default->flag; ?>"></span>
                    <span class="ba-phone-prefix">+<?php echo $default->prefix; ?></span>
                </div>
                <div class="ba-phone-countries-list-wrapper">
                    <input type="text" class="ba-phone-countries-search" placeholder="<?php echo JText::_('SEARCH'); ?>">
                    <ul class="ba-phone-countries-list">
<?php
                   foreach (baformsHelper::$countries as $country) {
?>
                        <li class="ba-phone-country-item" data-prefix="+<?php echo $country->prefix; ?>"
                            data-flag="<?php echo $country->flag; ?>" data-title="<?php echo $country->title; ?>"
                            data-placeholder="<?php echo str_replace('X', '_', $country->placeholder); ?>">
                            <span class="ba-phone-flag ba-phone-flag-<?php echo $country->flag; ?>"></span>
                            <span class="ba-phone-country-title"><?php echo $country->title; ?></span>
                            <span class="ba-phone-country-prefix">+<?php echo $country->prefix; ?></span>
                        </li>
<?php
                    }
?>
                    </ul>
                </div>
            </div>
            <input type="text" class="ba-phone-number-input" placeholder="<?php echo str_replace('X', '_', $default->placeholder); ?>"
                value="<?php echo str_replace('X', '_', $default->placeholder); ?>">
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