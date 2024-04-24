<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$thousand = $object->options->thousand;
$separator = $object->options->separator;
$decimals = $object->options->decimals;
$position = !empty($object->options->position) ? ' right-currency-position' : '';
$total = $object->total * 1;
?>
<div class="ba-form-total-wrapper<?php echo $position; ?>">
<?php
if ($object->options->cart) {
?>
    <div class="ba-form-products-cart">
<?php
    foreach ($object->products as $products) {
        foreach ($products as $product) {
            $price = baformsHelper::renderPrice((string)$product->total, $thousand, $separator, $decimals);
?>
            <div class="ba-form-product-row" style="align-items: center;border-bottom: 1px solid #f3f3f3;display: flex;padding: 10px 0;">
                <div class="ba-form-product-title-cell" style="color: #999;"><?php echo $product->title; ?></div>
                <div class="ba-form-product-quantity-cell" style="color: #999;"><?php echo $product->quantity; ?></div>
                <div class="ba-form-product-total-cell" style="color: #333;">
                    <span class="field-price-currency" style="font-weight: bold;line-height: 24px;"><?php echo $object->options->symbol; ?></span>
                    <span class="field-price-value" style="font-weight: bold;line-height: 24px;"><?php echo $price; ?></span>
                </div>
            </div>
<?php
        }
    }
?>
    </div>
<?php
}
$price = baformsHelper::renderPrice((string)$object->total, $thousand, $separator, $decimals);
$shipping = 0;
?>
    <div class="ba-field-container">
        <div class="ba-cart-total-wrapper" style="margin-top: 20px;width: 100%;">
            <div class="ba-cart-total-container" style="border: none;">
<?php
                if (isset($object->shipping) || isset($object->promo) || $object->options->tax->enable) {
?>
                <div class="ba-cart-total-container-row ba-cart-subtotal-row" style="padding: 0;">
                    <span class="ba-cart-row-title" style="font-weight: bold;font-size: 16px;line-height: 32px; color: #333;"><?php echo JText::_('SUBTOTAL'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper" style="color: #333;">
                            <span class="field-price-currency" style="font-weight: bold;"><?php echo $object->options->symbol; ?></span>
                            <span class="field-price-value" style="font-weight: bold;"><?php echo $price; ?></span>
                        </div>
                    </div>
                </div>
<?php
                }
?>
<?php
                if (isset($object->shipping)) {
                    $shipping = $object->shipping->price * 1;
?>
                <div class="ba-cart-total-container-row ba-cart-shipping-row" style="padding: 0;">
                    <span class="ba-cart-row-title" style="color: #999;flex-grow: 1;"><?php echo JText::_('SHIPPING'); ?></span>
                    <div class="ba-cart-row-content" style="flex-grow: 0;">
<?php
                        $price = baformsHelper::renderPrice((string)$object->shipping->price, $thousand, $separator, $decimals);
?>
                        <div class="ba-cart-shipping-item" style="margin-bottom: 0;">
                            <span class="ba-shipping-title" style="margin-right: 25px;">
                                <span class="ba-form-shipping-title" style="color: #999;"><?php echo $object->shipping->title; ?></span>
                            </span>
                            <div class="ba-form-calculation-price-wrapper" style="color: #333;">
                                <span class="field-price-currency" style="font-weight: bold;"><?php echo $object->options->symbol; ?></span>
                                <span class="field-price-value" style="font-weight: bold;"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
<?php
                }
                if (isset($object->promo) && $object->promo == $object->options->promo->code) {
                    $discount = $object->options->promo->discount * 1;
                    if ($object->options->promo->unit == '%') {
                        $discount = $total * $discount / 100;
                    }
                    $total -= $discount;
                    $price = baformsHelper::renderPrice((string)$discount, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-total-container-row ba-cart-discount-row" style="padding: 0;">
                    <span class="ba-cart-row-title"  style="color: #999;flex-grow: 1;"><?php echo JText::_('DISCOUNT'); ?></span>
                    <div class="ba-cart-row-content" style="flex-grow: 0;">
                        <div class="ba-form-calculation-price-wrapper" style="color: #333;">
                            <span class="field-price-currency" style="font-weight: bold;"><?php echo $object->options->symbol; ?></span>
                            <span class="field-price-value" style="font-weight: bold;">-<?php echo $price; ?></span>
                        </div>
                    </div>
                </div>
<?php
                }
                if ($object->options->tax->enable) {
                    $tax = $total * $object->options->tax->value / 100;
                    $total += $tax;
                    $price = baformsHelper::renderPrice((string)$tax, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-total-container-row ba-cart-tax-row" style="padding: 10px 0;margin: 0;">
                    <span class="ba-cart-row-title"  style="color: #999;flex-grow: 1;"><?php echo $object->options->tax->title; ?></span>
                    <div class="ba-cart-row-content" style="flex-grow: 0;">
                        <div class="ba-form-calculation-price-wrapper" style="color: #333;">
                            <span class="field-price-currency" style="font-weight: bold;"><?php echo $object->options->symbol; ?></span>
                            <span class="field-price-value" style="font-weight: bold;"><?php echo $price; ?></span>
                        </div>
                    </div>
                </div>
<?php
                }
                $total += $shipping;
                $price = baformsHelper::renderPrice((string)$total, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-total-container-row ba-cart-total-row" style="padding: 0;padding-top: 20px;border-top: none">
                    <span class="ba-cart-row-title" style="font-weight: bold;font-size: 16px;line-height: 32px;color: #333;"><?php echo JText::_('TOTAL'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper" style="color: #333;">
                            <span class="field-price-currency" style="font-weight: bold;"><?php echo $object->options->symbol; ?></span>
                            <span class="field-price-value" style="font-weight: bold;"><?php echo $price; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>