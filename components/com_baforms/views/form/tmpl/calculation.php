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
$hidden = $field->options->hidden ? 'true' : 'false';
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$position = !empty($field->options->position) ? ' right-currency-position' : '';
$price = self::renderPrice('0', $field->options->thousand, $field->options->separator, $field->options->decimals);
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
<div class="ba-form-field-item ba-form-calculation-field <?php echo $className; ?>" data-type="calculation"
    data-hidden="<?php echo $hidden; ?>">
    <div class="ba-input-wrapper" style="<?php echo $style; ?>">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container<?php echo $position; ?>">
            <div class="ba-form-calculation-price-wrapper">
                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                <span class="field-price-value"
                    data-formula="<?php echo $field->options->formula; ?>"
                    data-thousand="<?php echo $field->options->thousand; ?>"
                    data-separator="<?php echo $field->options->separator; ?>"
                    data-decimals="<?php echo $field->options->decimals; ?>"><?php echo $price; ?></span>
                <input type="hidden" name="<?php echo $field->id; ?>" value="0" data-field-id="<?php echo $field->key; ?>"
                    data-product="<?php echo $field->options->type; ?>"
                    data-title="<?php echo htmlspecialchars($field->options->title, ENT_QUOTES); ?>">
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();