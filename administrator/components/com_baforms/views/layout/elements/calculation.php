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
$hidden = $field->options->hidden ? 'true' : 'false';
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$position = !empty($field->options->position) ? ' right-currency-position' : '';
$price = baformsHelper::renderPrice('0', $field->options->thousand, $field->options->separator, $field->options->decimals);
$keys = array('background', 'padding', 'label', 'field');
$style = '';
if (isset($field->options->design) && !$field->options->design) {
    foreach ($keys as $value) {
        foreach ($field->options->{$value} as $option => $optionValue) {
            if ($option == 'link') {
                continue;
            }
            if ($value == 'background' || $value == 'padding') {
                $style .= self::setDesignCssVariable($value, '', $option, $field->options, 'calculation').';';
            } else if ($option == 'typography') {
                foreach ($optionValue as $typographyKey => $typographyValue) {
                    if ($typographyKey == 'font-family' && $typographyValue != 'inherit' && !in_array($typographyValue, self::$fonts)) {
                        self::$fonts[] = $typographyValue;
                    }
                    $style .= self::setDesignCssVariable($value, $option, $typographyKey, $field->options, 'calculation').';';
                }
            }
            
            
        }
    }
}
?>
<div class="ba-form-field-item ba-form-calculation-field <?php echo $field->options->suffix ?>" data-type="calculation"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>" data-hidden="<?php echo $hidden; ?>">
    <div class="ba-input-wrapper" style="<?php echo $style; ?>">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container<?php echo $position; ?>">
            <div class="ba-form-calculation-price-wrapper">
                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                <span class="field-price-value"><?php echo $price; ?></span>
                <input type="hidden">
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