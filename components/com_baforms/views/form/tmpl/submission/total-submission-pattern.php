<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$thousand = $field->options->thousand;
$separator = $field->options->separator;
$decimals = $field->options->decimals;
$position = !empty($field->options->position) ? ' right-currency-position' : '';
$total = $object->total * 1;
?>
<div class="ba-form-total-wrapper<?php echo $position; ?>">
<?php
if ($field->options->cart) {
?>
    <div class="ba-form-products-cart">
<?php
    foreach ($object->products as $products) {
        foreach ($products as $product) {
            $price = baformsHelper::renderPrice((string)$product->total, $thousand, $separator, $decimals);
?>
            <div class="ba-form-product-row">
                <div class="ba-form-product-title-cell"><?php echo $product->title; ?></div>
                <div class="ba-form-product-quantity-cell"><?php echo $product->quantity; ?></div>
                <div class="ba-form-product-total-cell">
                    <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                    <span class="field-price-value"><?php echo $price; ?></span>
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
        <div class="ba-cart-total-wrapper">
            <div class="ba-cart-total-container">
<?php
                if (isset($object->shipping) || isset($object->promo) || $field->options->tax->enable) {
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
                if (isset($object->shipping)) {
                    $shipping = $object->shipping->price * 1;
?>
                <div class="ba-cart-total-container-row ba-cart-shipping-row">
                    <span class="ba-cart-row-title"><?php echo JText::_('SHIPPING'); ?></span>
                    <div class="ba-cart-row-content">
<?php
                        $price = baformsHelper::renderPrice((string)$object->shipping->price, $thousand, $separator, $decimals);
?>
                        <div class="ba-cart-shipping-item">
                            <span class="ba-shipping-title">
                                <span class="ba-form-shipping-title"><?php echo $object->shipping->title; ?></span>
                            </span>
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"><?php echo $field->options->symbol; ?></span>
                                <span class="field-price-value"><?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
<?php
                }
                if (isset($object->promo) && $object->promo == $field->options->promo->code) {
                    $discount = $field->options->promo->discount * 1;
                    if ($field->options->promo->unit == '%') {
                        $discount = $total * $discount / 100;
                    }
                    $total -= $discount;
                    $price = baformsHelper::renderPrice((string)$discount, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-total-container-row ba-cart-discount-row">
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
                    $tax = $total * $field->options->tax->value / 100;
                    $total += $tax;
                    $price = baformsHelper::renderPrice((string)$tax, $thousand, $separator, $decimals);
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
<?php
$out = ob_get_contents();
ob_end_clean();