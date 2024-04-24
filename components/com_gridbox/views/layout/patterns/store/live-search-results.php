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
<div class="ba-live-search-body">
<?php
if ($data->count == 0) {
?>
    <div class="ba-empty-live-search">
        <span><?php echo JText::_('NO_MATCHING_SEARCH_RESULTS'); ?></span>
    </div>
<?php
} else {
    foreach ($data->pages as $product) {
        $image = gridboxHelper::prepareIntroImage($product->intro_image);
        if (!empty($image) && !gridboxHelper::isExternal($image)) {
            $image = JUri::root().$image;
        }
        $link = JRoute::_($product->link);
?>
        <div class="ba-live-search-product-row row-fluid">
<?php
        if (!empty($image)) {
?>
            <div class="ba-live-search-product-image-cell">
                <img src="<?php echo $image; ?>">
                <a href="<?php echo $link; ?>"></a>
            </div>
<?php
        }
?>
            <div class="ba-live-search-product-content-cell">
                <div class="ba-live-search-product-content-inner-cell">
                    <div class="ba-live-search-product-title-cell">
                        <span class="ba-live-search-product-title">
                            <a href="<?php echo $link; ?>"><?php echo $product->title; ?></a>
                        </span>
<?php
                    if (isset($product->category)) {
                        $catLink = JRoute::_($product->catLink);
?>
                        <span class="ba-live-search-product-category">
                            <a href="<?php echo $catLink; ?>"><?php echo $product->category; ?></a>
                        </span>
<?php
                    }
?>
                    </div>
<?php
                if (isset($product->prices)) {
                    $price = $product->prices->sale_price !== '' ? $product->prices->sale : $product->prices->regular;
?>
                    <div class="ba-live-search-product-price-cell">
<?php
                    if ($product->prices->sale_price !== '') {
?>
                        <span class="ba-live-search-sale-price-wrapper <?php echo $currency->position; ?>">
                            <span class="ba-live-search-price-currency"><?php echo $currency->symbol; ?></span>
                            <span class="ba-live-search-price-value"><?php echo $product->prices->regular; ?></span>
                        </span>
<?php
                    }
?>

                        <span class="ba-live-search-price-wrapper <?php echo $currency->position; ?>">
                            <span class="ba-live-search-price-currency"><?php echo $currency->symbol; ?></span>
                            <span class="ba-live-search-price-value"><?php echo $price; ?></span>
                        </span>
                    </div>
<?php
                }
?>
                </div>
            </div>
<?php
        if (isset($product->prices)) {
?>
            <div class="ba-live-search-add-to-cart-cell">
                 <span class="ba-live-search-add-to-cart-btn" data-id="<?php echo $product->id; ?>">
                    <?php echo JText::_('ADD_TO_CART'); ?>
                </span>
            </div>
<?php
        }
?>
        </div>
<?php
    }
}
?>
</div>
<div class="ba-live-search-footer">
<?php
if ($data->count > 10) {
?>
    <span class="ba-live-search-show-all-btn"><?php echo JText::_('ALL_SEARCH_RESULTS'); ?></span>
<?php
}
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();