<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$currency = self::$store->currency;
$shipping = self::getStoreShippingItems($cart);
$shippingHTML = self::getStoreShippingHTML($cart, $shipping);
$payments = self::getStorePaymentsHTML($cart);
$products = self::getStoreCheckoutProductsHTML($cart);
$promoCodes = self::getPublishedPromoCode();
$minimum = self::$store->checkout->minimum;
$minimumFlag = !empty($minimum) && $cart->total * 1 < $minimum * 1;
?>
<div class="ba-checkout-order-form-section ba-checkout-order-form-orders-wrapper">
    <div class="ba-checkout-order-form-title-wrapper">
        <span class="ba-checkout-order-form-title"><?php echo JText::_('YOUR_ORDER'); ?></span>
        <span class="ba-checkout-edit-order"><?php echo JText::_('EDIT'); ?></span>
    </div>
<?php
    echo $products->html;
?>
</div>
<?php
if (!empty($shipping)) {
?>
<div class="ba-checkout-order-form-section ba-checkout-order-form-shipping-wrapper">
    <div class="ba-checkout-order-form-title-wrapper">
        <span class="ba-checkout-order-form-title"><?php echo JText::_('SHIPPING'); ?></span>
    </div>
<?php
    echo $shippingHTML;
?>
</div>
<?php
}
if (!empty($payments)) {
?>
<div class="ba-checkout-order-form-section ba-checkout-order-form-payments-wrapper">
    <div class="ba-checkout-order-form-title-wrapper">
        <span class="ba-checkout-order-form-title"><?php echo JText::_('PAYMENT'); ?></span>
    </div>
<?php
    echo $payments;
?>
</div>
<?php
}
$price = self::preparePrice($cart->subtotal, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div class="ba-checkout-order-form-section ba-checkout-order-form-total-wrapper">
<?php
if ($promoCodes) {
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-promo-code">
        <span class="ba-checkout-order-form-row-title show-promo-code"
            data-code="<?php echo $cart->validPromo ? $cart->promo->code : '' ?>">
            <?php echo JText::_('HAVE_PROMO_CODE'); ?>
        </span>
<?php
    if ($cart->validPromo) {
?>
        <span class="ba-activated-promo-code">
            <?php echo $cart->promo->code; ?>
            <i class="ba-icons ba-icon-close ba-remove-promo"></i>
        </span>
<?php
    }
?>
    </div>
<?php
    }
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-subtotal">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('SUBTOTAL'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
if (!empty($cart->discount)) {
    $price = self::preparePrice($cart->discount, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-discount">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('DISCOUNT'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-cart-price-minus">-</span>
            <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
$shippingCount = count($shipping);
$shippingTaxItem = null;
if (!empty($shipping)) {
    $price = self::preparePrice(0, $currency->thousand, $currency->separator, $currency->decimals);
    foreach ($shipping as $shippingItem) {
        if ($shippingItem->default || $shippingCount == 1) {
            $price = self::preparePrice($shippingItem->price, $currency->thousand, $currency->separator, $currency->decimals);
            $cart->total += $shippingItem->price;
        }
    }
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-shipping">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('SHIPPING'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
$shippingTax = self::getStoreShippingTax($cart);
if (!empty($shipping) && $shippingTax && self::$store->tax->mode == 'excl') {
    $price = self::preparePrice(0, $currency->thousand, $currency->separator, $currency->decimals);
    foreach ($shipping as $shippingItem) {
        if ($shippingItem->default || $shippingCount == 1) {
            $price = self::preparePrice($shippingItem->tax, $currency->thousand, $currency->separator, $currency->decimals);
            $cart->total += $shippingItem->tax;
        }
    }
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-shipping-tax">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('TAX_ON_SHIPPING'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
} else if (!empty($shipping) && $shippingTax) {
    $price = self::preparePrice(0, $currency->thousand, $currency->separator, $currency->decimals);
    foreach ($shipping as $shippingItem) {
        if ($shippingItem->default || $shippingCount == 1) {
            $cart->tax += $shippingItem->tax;
            $price = self::preparePrice($shippingItem->tax, $currency->thousand, $currency->separator, $currency->decimals);
        }
    }
    if ($shippingTax && $cart->taxes->count == 1) {
        foreach ($cart->taxes as $key => $tax) {
            if ($key == 'count') {
                continue;
            }
            if ($tax->title != $shippingTax->title || $tax->rate != $shippingTax->rate) {
                $cart->taxes->count++;
            }
        }
    }
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-shipping-includes-tax">
        <span class="ba-checkout-order-form-row-title">
            <span><?php echo JText::_('INCLUDES'); ?></span>
            <span><?php echo $shippingTax->title; ?></span>
            <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                    <?php echo $currency->symbol; ?>
                </span>
                <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
            </span>
        </span>
    </div>
<?php
}
if (self::$store->tax->mode == 'excl') {
    foreach ($cart->taxes as $key => $tax) {
        if ($key == 'count') {
            continue;
        }
        $price = self::preparePrice($tax->amount, $currency->thousand, $currency->separator, $currency->decimals);
        $cart->total += $tax->amount;
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-tax">
        <span class="ba-checkout-order-form-row-title"><?php echo $tax->title; ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
    }
}
if ($cart->later == 0) {
    $price = self::preparePrice($cart->total, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-total">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('TOTAL'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                <?php echo $currency->symbol; ?>
            </span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
} else {
    $price = self::preparePrice($cart->total - $cart->later, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-paying-now">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('PAYING_NOW'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                <?php echo $currency->symbol; ?>
            </span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
    $price = self::preparePrice($cart->later, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-pay-leter">
        <span class="ba-checkout-order-form-row-title"><?php echo JText::_('LEFT_TO_PAY'); ?></span>
        <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                <?php echo $currency->symbol; ?>
            </span>
            <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
if ($cart->taxes->count == 1 && self::$store->tax->mode == 'incl') {
    foreach ($cart->taxes as $key => $tax) {
        if ($key == 'count') {
            continue;
        }
        $price = self::preparePrice($cart->tax, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-includes-tax">
        <span class="ba-checkout-order-form-row-title">
            <span><?php echo JText::_('INCLUDES'); ?></span>
            <span><?php echo $tax->rate; ?>%</span>
            <span><?php echo $tax->title; ?></span>
            <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                    <?php echo $currency->symbol; ?>
                </span>
                <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
            </span>
        </span>
    </div>
<?php
    }
} else if ($cart->taxes->count > 1 && self::$store->tax->mode == 'incl') {
    $price = self::preparePrice($cart->tax, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-checkout-order-form-includes-tax">
        <span class="ba-checkout-order-form-row-title">
            <span><?php echo JText::_('INCLUDING_TAXES'); ?></span>
            <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                    <?php echo $currency->symbol; ?>
                </span>
                <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
            </span>
        </span>
    </div>
<?php
}

if ($minimumFlag) {
    $price = self::preparePrice($minimum, $currency->thousand, $currency->separator, $currency->decimals);
?>
    <div class="ba-checkout-order-form-row ba-minimum-order-amount">
        <span class="ba-checkout-order-form-row-title">
            <span><?php echo JText::_('MINIMUM_ORDER_AMOUNT_IS'); ?></span>
            <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-checkout-order-price-currency" data-currency="<?php echo $currency->code; ?>">
                    <?php echo $currency->symbol; ?>
                </span>
                <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
            </span>
        </span>
    </div>
<?php
} else if (!empty($cart->products)) {
?>
    <div class="ba-checkout-order-form-row ba-checkout-place-order">
        <span class="ba-checkout-place-order-btn"><span><?php echo JText::_('PLACE_ORDER'); ?></span></span>
    </div>
<?php
}
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();