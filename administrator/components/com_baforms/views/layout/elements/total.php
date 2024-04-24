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
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$position = !empty($field->options->position) ? ' right-currency-position' : '';
$position .= $field->options->cart ? '' : ' disabled-cart-products';
$thousand = $field->options->thousand;
$separator = $field->options->separator;
$decimals = $field->options->decimals;
$total = 100;
$price = baformsHelper::renderPrice('100', $thousand, $separator, $decimals);
$shippingFlag = false;
foreach ($field->options->items as $item) {
    $shippingFlag = true;
    break;
}
$subtotal = $field->options->promo->enable || $field->options->tax->enable || $shippingFlag;
?>
<div class="ba-form-field-item ba-form-total-field <?php echo $field->options->suffix ?>" data-type="total"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-form-products-cart<?php echo $position; ?>">
            <div class="ba-form-product-row">
                <div class="ba-form-product-title-cell">
                    <?php echo JText::_('PRODUCT_NAME'); ?>
                </div>
                <div class="ba-form-product-quantity-cell">
                    <i class="zmdi zmdi-minus-circle-outline" data-action="-"></i>
                    <input type="number" value="1" min="1" step="1">
                    <i class="zmdi zmdi-plus-circle-o" data-action="+"></i>
                </div>
                <div class="ba-form-product-total-cell">
                    <div class="ba-form-calculation-price-wrapper">
                        <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                        <span class="field-price-value"><?php echo $price; ?></span>
                    </div>
                </div>
                <div class="ba-form-product-remove-cell">
                    <i class="zmdi zmdi-delete"></i>
                </div>
            </div>
        </div>
        <div class="ba-field-container<?php echo $position; ?>">
<?php
        $style = $field->options->promo->enable ? '' : ' style="display: none;"';
?>
            <div class="ba-cart-promo-code-wrapper"<?php echo $style; ?>>
                <span class="ba-cart-container-title"><?php echo JText::_('COUPON_CODE'); ?></span>
                <div class="ba-cart-promo-code-container">
                    <input type="text" class="ba-cart-promo-code-input">
                    <span class="ba-cart-promo-code-btn"><?php echo JText::_('ACTIVATE'); ?></span>
                </div>
            </div>
<?php
        $style = $subtotal ? '' : ' style="display: none;"';
?>
            <div class="ba-cart-total-wrapper">
                <div class="ba-cart-total-container">
                    <div class="ba-cart-total-container-row ba-cart-subtotal-row"<?php echo $style; ?>>
                        <span class="ba-cart-row-title"><?php echo JText::_('SUBTOTAL'); ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    $style = $shippingFlag ? '' : ' style="display: none;"';
                    $shipping = 0;
?>
                    <div class="ba-cart-total-container-row ba-cart-shipping-row"<?php echo $style; ?>>
                        <span class="ba-cart-row-title"><?php echo JText::_('SHIPPING'); ?></span>
                        <div class="ba-cart-row-content">
<?php
                        foreach ($field->options->items as $item) {
                            $price = baformsHelper::renderPrice($item->price, $thousand, $separator, $decimals);
                            if ($item->default) {
                                $shipping = $item->price * 1;
                            }
?>
                            <div class="ba-cart-shipping-item">
                                <label class="ba-form-radio">
                                    <input type="radio" name="shipping-<?php echo $field->key; ?>"
                                        <?php echo $item->default ? ' checked' : ''; ?>
                                        data-price="<?php echo $item->price; ?>">
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
                    $style = $field->options->promo->enable ? '' : ' style="display: none;"';
                    $discount = empty($style) && !empty($field->options->promo->discount) ? $field->options->promo->discount * 1 : 0;
                    if ($field->options->promo->unit == '%') {
                        $discount = $total * $discount / 100;
                    }
                    $total -= $discount;
                    $price = baformsHelper::renderPrice((string)$discount, $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-discount-row"<?php echo $style; ?>>
                        <span class="ba-cart-row-title"><?php echo JText::_('DISCOUNT'); ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value">-<?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    $style = $field->options->tax->enable ? '' : ' style="display: none;"';
                    $tax = $field->options->tax->enable ? $field->options->tax->value * 1 : 0;
                    $tax = $total * $tax / 100;
                    $total += $tax;
                    $price = baformsHelper::renderPrice((string)$tax, $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-tax-row"<?php echo $style; ?>>
                        <span class="ba-cart-row-title"><?php echo $field->options->tax->title; ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
<?php
                    $total += $shipping;
                    $price = baformsHelper::renderPrice((string)$total, $thousand, $separator, $decimals);
?>
                    <div class="ba-cart-total-container-row ba-cart-total-row">
                        <span class="ba-cart-row-title"><?php echo JText::_('TOTAL'); ?></span>
                        <div class="ba-cart-row-content">
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
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