<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>
<div class="ba-wishlist-products-list" data-quantity="<?php echo $wishlist->quantity; ?>">
<?php
if ($wishlist->empty) {
?>
    <div class="ba-empty-cart-products">
        <i class="ba-icons ba-icon-heart"></i>
        <span class="ba-empty-cart-products-message"><?php echo JText::_('EMPTY_WISHLIST'); ?></span>
    </div>
<?php
} else {
    $existsProducts = 0;
    foreach ($wishlist->products as $product) {
        $image = !empty($product->images) ? $product->images[0] : $product->intro_image;
        if (!empty($image) && !gridboxHelper::isExternal($image)) {
            $image = JUri::root().$image;
        }
        $price = $product->prices->sale_price !== '' ? $product->prices->sale : $product->prices->regular;
        $link = $product->link;
        if (isset($product->variationURL)) {
            $link .= '?'.$product->variationURL;
        }
?>
        <div class="ba-wishlist-product-row row-fluid" data-id="<?php echo $product->id; ?>"
            data-extra-count="<?php echo $product->extra_options->count; ?>">
<?php
        if (!empty($image)) {
?>
            <div class="ba-wishlist-product-image-cell">
                <img src="<?php echo $image; ?>">
                <a href="<?php echo $link; ?>"></a>
            </div>
<?php
        }
?>
            <div class="ba-wishlist-product-content-cell">
                <div class="ba-wishlist-product-content-inner-cell">
                    <div class="ba-wishlist-product-title-cell">
                        <span class="ba-wishlist-product-title">
                            <a href="<?php echo $link; ?>"><?php echo $product->title; ?></a>
                        </span>
                        <span class="ba-wishlist-product-info">
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
                    <div class="ba-wishlist-product-price-cell">
<?php
                    if ($product->prices->sale_price !== '') {
?>
                        <span class="ba-wishlist-sale-price-wrapper <?php echo $position; ?>">
                            <span class="ba-wishlist-price-currency"><?php echo $symbol; ?></span>
                            <span class="ba-wishlist-price-value"><?php echo $product->prices->regular; ?></span>
                        </span>
<?php
                    }
?>
                        <span class="ba-wishlist-price-wrapper <?php echo $position; ?>">
                            <span class="ba-wishlist-price-currency"><?php echo $symbol; ?></span>
                            <span class="ba-wishlist-price-value"><?php echo $price; ?></span>
                        </span>
                    </div>
                    <div class="ba-wishlist-product-remove-cell">
                        <i class="ba-icons ba-icon-trash"></i>
                    </div>
                </div>
<?php
                foreach ($product->extra_options->items as $field_id => $item) {
?>
                <div class="ba-wishlist-product-extra-options">
                    <span class="ba-wishlist-product-extra-options-title"><?php echo $item->title; ?></span>
                    <div class="ba-wishlist-product-extra-options-content">
<?php
                    foreach ($item->values as $key => $value) {
                        if ($value->price != '') {
                            $extraPrice = gridboxHelper::preparePrice($value->price, $thousand, $separator, $decimals);
                            if ($position == '') {
                                $extraPrice = $symbol.' '.$extraPrice;
                            } else {
                                $extraPrice = $extraPrice.' '.$symbol;
                            }
                        } else {
                            $extraPrice = '';
                        }
?>
                        <div class="ba-wishlist-product-extra-option" data-key="<?php echo $key ?>"
                            data-field="<?php echo $field_id; ?>">
                            <span class="ba-wishlist-product-extra-option-value"><?php echo $value->value; ?></span>
                            <span class="ba-wishlist-product-extra-option-price"><?php echo $extraPrice; ?></span>
<?php
                        if (!$item->required) {
?>
                            <span class="ba-wishlist-product-remove-extra-option">
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
                        <div class="ba-wishlist-attachment attachment-file-uploaded">
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
            <div class="ba-wishlist-add-to-cart-cell">
<?php
            $stock = $product->data->stock;
            if ($stock !== '' && $stock * 1 < $product->min) {
?>
                <span class="ba-wishlist-empty-stock"><?php echo JText::_('OUT_OF_STOCK'); ?></span>
<?php
            } else {
                $existsProducts++;
?>
                <span class="ba-wishlist-add-to-cart-btn"><?php echo JText::_('ADD_TO_CART'); ?></span>
<?php
            }
?>
            </div>
        </div>
<?php
    }
}
?>
</div>