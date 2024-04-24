<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
if (isset($data->default)) {
    $prices = self::prepareProductPrices($page->id,$data->default->price,$data->default->sale_price,$data->default->variation);
} else {
    $prices = self::prepareProductPrices($page->id, $data->price, $data->sale_price);
}
$price = $prices->regular;
$variations_map = self::$storeHelper->getProductVariationsMap($page->id);
if (isset($data->default)) {
    $variations = self::getProductVariations($data->variations, $variations_map);
    if (strpos($url, '?')) {

    }
    $url .= strpos($url, '?') ? '&' : '?';
    $url .= $variations->{$data->default->variation}->url;
    $images = new stdClass();
    foreach ($variations_map as $value) {
        $images->{$value->option_key} = json_decode($value->images);
    }
    $vars = explode('+', $data->default->variation);
    foreach ($vars as $value) {
        if (!empty($images->{$value})) {
            $data->default->images = $images->{$value};
        }
    }
}
$extra_flag = false;
foreach ($data->extra_options as $extra_option) {
    if ($extra_option->required == 1) {
        $extra_default = false;
        foreach ($extra_option->items as $value) {
            if ($value->default) {
                $extra_default = true;
                break;
            }
        }
        if (!$extra_default) {
            $extra_flag = true;
            break;
        }
    }
}
ob_start();
?>
<div class="ba-blog-post-add-to-cart-wrapper">
<?php
if (($desktop && $desktop->store->price) || !$desktop) {
?>
    <div class="ba-blog-post-add-to-cart-price">
<?php
    if ($prices->sale_price !== '') {
        $price = $prices->sale;
?>
        <span class="ba-blog-post-add-to-cart-sale-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-blog-post-add-to-cart-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-blog-post-add-to-cart-price-value"><?php echo $prices->regular; ?></span>
        </span>
<?php
    }
?>
        <span class="ba-blog-post-add-to-cart-price-wrapper <?php echo $currency->position; ?>">
            <span class="ba-blog-post-add-to-cart-price-currency"><?php echo $currency->symbol; ?></span>
            <span class="ba-blog-post-add-to-cart-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
$min = !empty($data->min) ? $data->min * 1 : 1;
if ((!empty($variations_map) || (empty($variations_map) && ($data->stock === '' || $data->stock >= $min)))
    && ($desktop && $desktop->store->cart) || !$desktop) {
?>
    <div class="ba-blog-post-add-to-cart-button">
<?php
        $text = 'ADD_TO_CART';
        if ((!empty($variations_map) && !isset($data->default)) || $extra_flag) {
            $text = JText::_('SELECT_AN_OPTION');
        }
?>
        <span class="ba-blog-post-add-to-cart"><?php echo JText::_($text); ?></span>
    </div>
<?php
}
?>
</div>
<?php
$addToCart = ob_get_contents();
ob_end_clean();