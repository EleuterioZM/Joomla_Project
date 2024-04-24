<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
ob_start();
?>
<div class="ba-checkout-order-product-row row-fluid" data-extra-count="<?php echo $product->extra_options->count; ?>">
<?php
if (!empty($image)) {
?>
    <div class="ba-checkout-order-product-image-cell">
        <img src="<?php echo $image; ?>">
    </div>
<?php
}
?>
    <div class="ba-checkout-order-product-content-cell">
        <div class="ba-checkout-order-product-content-inner-cell">
            <div class="ba-checkout-order-product-title-cell">
                <span class="ba-checkout-order-product-title"><?php echo $product->title; ?></span>
                <span class="ba-checkout-order-product-info"><?php echo $infoStr; ?></span>
            </div>
<?php
        if (empty($product->data->product_type) || $product->data->product_type == 'physical') {
?>
            <div class="ba-checkout-order-product-quantity-cell"><?php echo 'x '.$product->quantity; ?></div>
<?php
        }
?>
            <div class="ba-checkout-order-product-price-cell">
                <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
                    <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
                    <span class="ba-cart-price-value"><?php echo $price; ?></span>
                </span>
            </div>
        </div>
<?php
        foreach ($product->extra_options->items as $field_id => $item) {
?>
        <div class="ba-checkout-order-product-extra-options">
            <span class="ba-checkout-order-product-extra-options-title"><?php echo $item->title; ?></span>
            <div class="ba-checkout-order-product-extra-options-content">
<?php
            foreach ($item->values as $key => $value) {
?>
                <div class="ba-checkout-order-product-extra-option" data-key="<?php echo $key ?>"
                    data-field="<?php echo $field_id; ?>">
                    <span class="ba-checkout-order-product-extra-option-value"><?php echo $value->value; ?></span>
<?php
                if ($value->price != '') {
                    $extraPrice = $value->price * $product->quantity;
                    $extraPrice = gridboxHelper::preparePrice($extraPrice, $currency->thousand, $currency->separator, $currency->decimals);
?>
                    <span class="ba-checkout-order-product-extra-option-price <?php echo $currency->position; ?>"
                        data-price="<?php echo $value->price; ?>">
                        <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
                        <span class="ba-checkout-order-price-value"><?php echo $extraPrice; ?></span>
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
        if ($product->data->app_type == 'booking') {
            $dates = [];
            foreach ($product->booking->dates as $date) {
                $dates[] = gridboxHelper::formatDate($date);
            }
?>
            <div class="ba-checkout-order-product-booking-options">
                <span class="ba-checkout-order-product-booking-title"><?php echo JText::_('DATE'); ?>:</span>
                <div class="ba-checkout-order-product-booking-content">
                    <div class="ba-checkout-order-product-booking-option">
                        <span class="ba-checkout-order-product-booking-option-value"><?php echo implode(' - ', $dates); ?></span>
                    </div>
                </div>
            </div>
<?php
            if (isset($product->booking->time->start)) {
?>
                <div class="ba-checkout-order-product-booking-options">
                    <span class="ba-checkout-order-product-booking-title"><?php echo JText::_('TIME'); ?>:</span>
                    <div class="ba-checkout-order-product-booking-content">
                        <div class="ba-checkout-order-product-booking-option">
                            <span class="ba-checkout-order-product-booking-option-value"><?php echo $product->booking->time->start; ?></span>
                        </div>
                    </div>
                </div>
<?php
            }
            if (!empty($product->booking->guests)) {
?>
                <div class="ba-checkout-order-product-booking-options">
                    <span class="ba-checkout-order-product-booking-title"><?php echo JText::_('GUESTS'); ?>:</span>
                    <div class="ba-checkout-order-product-booking-content">
                        <div class="ba-checkout-order-product-booking-option">
                            <span class="ba-checkout-order-product-booking-option-value"><?php echo $product->booking->guests; ?></span>
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
$out = ob_get_contents();
ob_end_clean();