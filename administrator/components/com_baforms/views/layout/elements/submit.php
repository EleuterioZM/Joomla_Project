<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$title = $field->options->label;
$icon = !empty($field->options->{'submit-icon'}) ? '<i class="'.$field->options->{'submit-icon'}.'"></i>' : '';
$keys = ['background', 'padding', 'border', 'typography', 'icon', 'shadow'];
$style = '';
$animation = !empty($field->options->animation) ? ' '.$field->options->animation : '';
foreach ($keys as $value) {
    foreach ($field->options->{$value} as $option => $optionValue) {
        if ($option == 'link') {
            continue;
        }
        if ($option == 'font-family' && $optionValue != 'inherit' && !in_array($optionValue, self::$fonts)) {
            self::$fonts[] = $optionValue;
        }
        $style .= self::setDesignCssVariable($value, '', $option, $field->options, 'submit').';';
    }
}
?>
<div class="ba-form-field-item ba-form-submit-field <?php echo $field->options->suffix; ?>" data-type="submit"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-form-submit-wrapper<?php echo $animation; ?>" style="<?php echo $style; ?>">
        <div class="ba-form-submit-recaptcha-wrapper"></div>
        <div class="ba-form-submit-btn-wrapper">
            <button class="ba-form-submit-btn"><?php echo $icon; ?><span class="ba-form-submit-title"><?php echo $title; ?></span></button>
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
            <span class="ba-edit-text ba-hide-element"><?php echo JText::_('IMAGE'); ?></span>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();