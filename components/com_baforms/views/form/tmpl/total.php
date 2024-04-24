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
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$position = !empty($field->options->position) ? ' right-currency-position' : '';
$total = 0;
$thousand = $field->options->thousand;
$separator = $field->options->separator;
$decimals = $field->options->decimals;
$price = self::renderPrice('0', $thousand, $separator, $decimals);
$shippingFlag = false;
foreach ($field->options->items as $item) {
    $shippingFlag = true;
    break;
}
$subtotal = $field->options->promo->enable || $field->options->tax->enable || $shippingFlag;
?>
<div class="ba-form-field-item ba-form-total-field <?php echo $className; ?>" data-type="total"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <?php
    if ($field->options->cart) {
?>
        <div class="ba-form-products-cart<?php echo $position; ?>"></div>
<?php
    }
?>
        <div class="ba-field-container<?php echo $position; ?>">
<?php
        if ($field->options->promo->enable) {
?>
            <div class="ba-cart-promo-code-wrapper">
                <span class="ba-cart-container-title"><?php echo JText::_('COUPON_CODE'); ?></span>
                <div class="ba-cart-promo-code-container">
                    <input type="text" class="ba-cart-promo-code-input">
                    <span class="ba-cart-promo-code-btn" data-name="<?php echo $field->id; ?>"><?php echo JText::_('ACTIVATE'); ?></span>
                </div>
            </div>
<?php
        }
?>
            <div class="ba-cart-total-wrapper">
                <div class="ba-cart-total-container">
<?php
                    if ($subtotal) {
?>
                    <div class="ba-cart-total-container-row ba-cart-subtotal-row">
                        <span class="ba-cart-row-title"><?php echo JText::_('SUBTOTAL'); ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    }
?>
<?php
                    $shipping = 0;
                    if ($shippingFlag) {
?>
                    <div class="ba-cart-total-container-row ba-cart-shipping-row">
                        <span class="ba-cart-row-title"><?php echo JText::_('SHIPPING'); ?></span>
                        <div class="ba-cart-row-content">
<?php
                        foreach ($field->options->items as $item) {
                            $price = self::renderPrice($item->price, $thousand, $separator, $decimals);
                            if ($item->default) {
                                $shipping = $item->price * 1;
                            }
?>
                            <div class="ba-cart-shipping-item">
                                <label class="ba-form-radio">
                                    <input type="radio" name="shipping-<?php echo $field->id; ?>"
                                        <?php echo $item->default ? ' checked' : ''; ?>
                                        data-price="<?php echo $item->price; ?>"
                                        data-title="<?php echo htmlspecialchars($item->title, ENT_QUOTES); ?>">
                                    <span></span>
                                </label>
                                <span class="ba-shipping-title">
                                    <span class="ba-form-shipping-title"><?php echo $item->title; ?></span>
                                </span>
                                <div class="ba-form-calculation-price-wrapper">
                                    <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                    <span class="field-price-value"><?php echo $price; ?></span>
                                </div>
                            </div>
<?php
                        }
?>
                        </div>
                    </div>
<?php
                    }
                    if ($field->options->promo->enable) {
                    $price = self::renderPrice('0', $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-discount-row" style="display: none;">
                        <span class="ba-cart-row-title"><?php echo JText::_('DISCOUNT'); ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value">-<?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    }
                    if ($field->options->tax->enable) {
                    $price = baformsHelper::renderPrice('0', $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-tax-row">
                        <span class="ba-cart-row-title"><?php echo $field->options->tax->title; ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    }
                    $total += $shipping;
                    $tax = $field->options->tax->enable && !empty($field->options->tax->value) ? $field->options->tax->value * 1 : 0;
                    $price = baformsHelper::renderPrice((string)$total, $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-total-row">
                        <span class="ba-cart-row-title" id="total-<?php echo $field->id; ?>">
                            <?php echo JText::_('TOTAL'); ?>
                        </span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value" data-symbol="<?php echo $field->options->symbol; ?>"
                                    data-thousand="<?php echo $field->options->thousand; ?>"
                                    data-tax="<?php echo $tax; ?>" data-code="<?php echo $field->options->code; ?>"
                                    data-separator="<?php echo $field->options->separator; ?>"
                                    data-decimals="<?php echo $field->options->decimals; ?>"><?php echo $price; ?></span>
                                <textarea name="<?php echo $field->id; ?>" value="0" data-field-id="<?php echo $field->key; ?>"
                                    aria-labelledby="total-<?php echo $field->id; ?>"
                                    style="display: none !important;" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();