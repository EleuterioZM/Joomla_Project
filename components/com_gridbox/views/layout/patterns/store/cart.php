<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$thousand = $currency->thousand;
$separator = $currency->separator;
$decimals = $currency->decimals;
$position = $currency->position;
$symbol = $currency->symbol;

ob_start();
?>
<div class="ba-store-cart-backdrop">
    <div class="ba-store-cart-close"></div>
    <div class="ba-store-cart ba-container">
        <div class="ba-store-cart-close-wrapper">
            <i class="ba-icons ba-icon-close ba-store-cart-close"></i>
        </div>
        <div class="row-fluid">
            <div class="ba-cart-headline-wrapper">
                <span class="ba-cart-headline"><?php echo JText::_('CART'); ?></span>
            </div>
<?php
            $price = gridboxHelper::preparePrice($cart->total, $thousand, $separator, $decimals);
?>
            <div class="ba-cart-products-list" data-quantity="<?php echo $cart->quantity; ?>"
                data-total="<?php echo $price; ?>">
<?php
            if ($cart->empty) {
?>
                <div class="ba-empty-cart-products">
                    <i class="ba-icons ba-icon-mall"></i>
                    <span class="ba-empty-cart-products-message"><?php echo JText::_('EMPTY_SHOPPING_CART'); ?></span>
                </div>
<?php
            } else {
                foreach ($cart->products as $product) {
                    $image = !empty($product->images) ? $product->images[0] : $product->intro_image;
                    if (!empty($image) && !gridboxHelper::isExternal($image)) {
                        $image = JUri::root().$image;
                    }
                    $price = $product->prices->sale_price !== '' ? $product->prices->sale : $product->prices->regular;
                    $single = $product->data->single;
                    $attr = ' data-price="'.$single->price.'" data-sale="'.$single->sale_price.'"';
                    $attr .= ' data-stock="'.$product->data->stock.'" data-thousand="'.$thousand.'"';
                    $attr .= ' data-separator="'.$separator.'" data-decimals="'.$decimals.'" data-rate="'.$currency->rate.'"';
                    $attr .= ' data-min="'.$product->min.'"';
                    $link = $product->link;
                    if (isset($product->variationURL)) {
                        $link .= '?'.$product->variationURL;
                    }
?>
                    <div class="ba-cart-product-row row-fluid" data-id="<?php echo $product->id; ?>"
                        data-renew="<?php echo $product->renew_id; ?>"
                        data-extra-count="<?php echo $product->extra_options->count; ?>">
<?php
                    if (!empty($image)) {
?>
                        <div class="ba-cart-product-image-cell">
                            <img src="<?php echo $image; ?>">
                            <a href="<?php echo $link; ?>"></a>
                        </div>
<?php
                    }
?>
                        <div class="ba-cart-product-content-cell">
                            <div class="ba-cart-product-content-inner-cell">
                                <div class="ba-cart-product-title-cell">
                                    <span class="ba-cart-product-title">
                                        <a href="<?php echo $link; ?>"><?php echo $product->title; ?></a>
                                    </span>
                                    <span class="ba-cart-product-info">
<?php
                                    $info = [];
                                    foreach ($product->variations as $variation) {
                                        $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
                                    }
                                    $infoStr = implode('/', $info);
                                    echo $infoStr;
?>
                                    </span>
                                </div>
<?php
                            if (isset($product->data->app_type) && $product->data->app_type != 'booking' &&
                                (!isset($product->data->product_type) ||
                                    ($product->data->product_type != 'digital' && $product->data->product_type != 'subscription'))) {
?>
                                <div class="ba-cart-product-quantity-cell<?php echo $product->hasFileQty ? ' file-quantity-enabled' : ''; ?>">
                                    <i class="ba-icons ba-icon-minus" data-action="-"></i>
                                    <input type="text" value="<?php echo $product->quantity; ?>"<?php echo $attr; ?>>
                                    <i class="ba-icons ba-icon-plus" data-action="+"></i>
                                </div>
<?php
                            }
?>
                                <div class="ba-cart-product-price-cell">
<?php
                                if ($product->prices->sale_price !== '') {
?>
                                    <span class="ba-cart-sale-price-wrapper <?php echo $position; ?>">
                                        <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                                        <span class="ba-cart-price-value"><?php echo $product->prices->regular; ?></span>
                                    </span>
<?php
                                }
?>

                                    <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                                        <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                                        <span class="ba-cart-price-value"><?php echo $price; ?></span>
                                    </span>
                                </div>
                                <div class="ba-cart-product-remove-cell">
                                    <i class="ba-icons ba-icon-trash"></i>
                                </div>
                            </div>
<?php
                            foreach ($product->extra_options->items as $field_id => $item) {
?>
                            <div class="ba-cart-product-extra-options">
                                <span class="ba-cart-product-extra-options-title"><?php echo $item->title; ?></span>
                                <div class="ba-cart-product-extra-options-content">
<?php
                                foreach ($item->values as $key => $value) {
                                    if ($key == 0) {
                                        $key = strval($field_id).'-0';
                                    }
?>
                                    <div class="ba-cart-product-extra-option" data-key="<?php echo $key ?>"
                                        data-field="<?php echo $field_id; ?>">
                                        <span class="ba-cart-product-extra-option-value"><?php echo $value->value; ?></span>
<?php
                                    if ($value->price != '') {

                                        $extraPrice = $value->price * $product->quantity;
                                        $extraPrice = gridboxHelper::preparePrice($extraPrice, $thousand, $separator, $decimals);
?>
                                        <span class="ba-cart-product-extra-option-price <?php echo $position; ?>"
                                            data-price="<?php echo $value->price; ?>">
                                            <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                                            <span class="ba-cart-price-value"><?php echo $extraPrice; ?></span>
                                        </span>
<?php
                                    }
                                    if (!$item->required) {
?>
                                        <span class="ba-cart-product-remove-extra-option">
                                            <i class="ba-icons ba-icon-trash"></i>
                                        </span>
<?php
                                    }
?>
                                    </div>
<?php
                                }
                                if (isset($item->attachments)) {
                                    foreach ($item->attachments as $attachment) {
                                        $ext = $uploader->getExt($attachment->filename);
?>
                                    <div class="ba-cart-attachment attachment-file-uploaded">
<?php
                                        if ($uploader->isImage($ext)) {
                                            $src = JUri::root().gridboxHelper::$storeHelper->attachments.'/'.$attachment->filename;
?>
                                        <span class="post-intro-image" style="background-image: url(<?php echo $src; ?>);"></span>
<?php
                                        } else {
?>
                                        <i class="ba-icons ba-icon-attachment"></i>
<?php
                                        }
?>
                                        <span class="attachment-title"><?php echo $attachment->name; ?></span>
                                        <span class="attachment-progress-bar-wrapper">
                                            <span class="attachment-progress-bar" style="width: 100%;"></span>
                                        </span>
                                    </div>
<?php
                                    }
                                }
?>
                                </div>
                            </div>
<?php
                            }
                            if (isset($product->data->app_type) && $product->data->app_type == 'booking') {
                                $dates = [];
                                foreach ($product->booking->dates as $date) {
                                    $dates[] = gridboxHelper::formatDate($date);
                                }
?>
                                <div class="ba-cart-product-booking-options">
                                    <span class="ba-cart-product-booking-title"><?php echo JText::_('DATE'); ?>:</span>
                                    <div class="ba-cart-product-booking-content">
                                        <div class="ba-cart-product-booking-option">
                                            <span class="ba-cart-product-booking-option-value"><?php echo implode(' - ', $dates); ?></span>
                                        </div>
                                    </div>
                                </div>
<?php
                                if (isset($product->booking->time->start)) {
                                    $slot = $product->booking->time;
?>
                                    <div class="ba-cart-product-booking-options">
                                        <span class="ba-cart-product-booking-title"><?php echo JText::_('TIME'); ?>:</span>
                                        <div class="ba-cart-product-booking-content">
                                            <div class="ba-cart-product-booking-option">
                                                <span class="ba-cart-product-booking-option-value"><?php echo $slot->start; ?></span>
                                            </div>
                                        </div>
                                    </div>
<?php
                                }
                                if (!empty($product->booking->guests)) {
?>
                                    <div class="ba-cart-product-booking-options">
                                        <span class="ba-cart-product-booking-title"><?php echo JText::_('GUESTS'); ?>:</span>
                                        <div class="ba-cart-product-booking-content">
                                            <div class="ba-cart-product-booking-option">
                                                <span class="ba-cart-product-booking-option-value"><?php echo $product->booking->guests; ?></span>
                                            </div>
                                        </div>
                                    </div>
<?php
                                }
                            }
?>
                        </div>
                        
                    </div>
<?php
                }
            }
?>
            </div>
<?php
        if (!$cart->empty) {
?>
            <div class="ba-cart-checkout">
<?php
            if ($promoCodes) {
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-promo-code">
                    <span class="ba-cart-checkout-title show-promo-code"
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
                if (!empty($cart->discount)) {
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-discount">
                    <span class="ba-cart-checkout-title"><?php echo JText::_('DISCOUNT'); ?></span>
                    <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                        <span class="ba-cart-price-minus">-</span>
                        <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
<?php
                    $price = gridboxHelper::preparePrice($cart->discount, $thousand, $separator, $decimals);
?>
                        <span class="ba-cart-price-value"><?php echo $price; ?></span>
                    </span>
                </div>
<?php
                }
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-total">
                    <span class="ba-cart-checkout-title"><?php echo JText::_('CART_TOTAL'); ?></span>
                    <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                        <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
<?php
                        $price = gridboxHelper::preparePrice($cart->total, $thousand, $separator, $decimals);
?>
                        <span class="ba-cart-price-value"><?php echo $price; ?></span>
                    </span>
                </div>
<?php
            if ($cart->taxes->count == 1 && gridboxHelper::$store->tax->mode == 'incl') {
                foreach ($cart->taxes as $key => $tax) {
                if ($key == 'count') {
                    continue;
                }
                $price = gridboxHelper::preparePrice($tax->amount, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-includes-tax">
                    <span class="ba-cart-checkout-title">
                        <span><?php echo JText::_('INCLUDES'); ?></span>
                        <span><?php echo $tax->rate; ?>%</span>
                        <span><?php echo $tax->title; ?></span>
                        <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                            <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                            <span class="ba-cart-price-value"><?php echo $price; ?></span>
                        </span>
                    </span>
                </div>
<?php
                }
            } else if ($cart->taxes->count > 1 && gridboxHelper::$store->tax->mode == 'incl') {
                $price = gridboxHelper::preparePrice($cart->tax, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-includes-tax">
                    <span class="ba-cart-checkout-title">
                        <span><?php echo JText::_('INCLUDING_TAXES'); ?></span>
                        <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                            <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                            <span class="ba-cart-price-value"><?php echo $price; ?></span>
                        </span>
                    </span>
                </div>
<?php
            }
            if (!empty($checkout->minimum) && $cart->total * 1 < $checkout->minimum * 1) {
                $price = gridboxHelper::preparePrice($checkout->minimum, $thousand, $separator, $decimals);
?>
                <div class="ba-cart-checkout-row ba-minimum-order-amount">
                    <span class="ba-cart-checkout-title">
                        <span><?php echo JText::_('MINIMUM_ORDER_AMOUNT_IS'); ?></span>
                        <span class="ba-cart-price-wrapper <?php echo $position; ?>">
                            <span class="ba-cart-price-currency"><?php echo $symbol; ?></span>
                            <span class="ba-cart-price-value"><?php echo $price; ?></span>
                        </span>
                    </span>
                </div>
<?php
            } else {
                $disabled = $cart->empty ? ' disabled' : '';
?>
                <div class="ba-cart-checkout-row ba-cart-checkout-btn-wrapper">
                    <span class="ba-cart-checkout-btn<?php echo $disabled; ?>"><?php echo JText::_('CHECKOUT'); ?></span>
                </div>
<?php
            }
?>
            </div>
<?php
        }
?>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();