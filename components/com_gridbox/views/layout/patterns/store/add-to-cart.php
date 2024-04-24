<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$currency = self::$store->currency;
ob_start();
?>
<div class="ba-add-to-cart-variations">
<?php
foreach ($variations as $variation) {
?>
    <div class="ba-add-to-cart-variation" data-type="<?php echo $variation->type; ?>">
        <div class="ba-add-to-cart-row-label"><?php echo $variation->title; ?></div>
        <div class="ba-add-to-cart-row-value" data-type="<?php echo $variation->type; ?>">
<?php
            if ($variation->type == 'dropdown') {
                $li = '';
                $textValue = JText::_('SELECT');
                $value = '';
            }
            foreach ($variation->items as $item) {
                $flag = in_array($item->option_key, $active);
                if ($variation->type == 'dropdown') {
                    if ($flag) {
                        $textValue = $item->value;
                        $value = $item->option_key;
                    }
                    $li .= '<li data-value="'.$item->option_key.'" class="'.($flag ? 'selected' : '')
                        .'">'.$item->value.'</li>';
                } else if ($variation->type == 'tag') {
?>
                    <span data-value="<?php echo $item->option_key; ?>" class="<?php echo $flag ? 'active' : ''; ?>">
                        <?php echo $item->value; ?>
                    </span>
<?php
                } else if ($variation->type == 'color') {
?>
                    <span data-value="<?php echo $item->option_key; ?>" class="<?php echo $flag ? 'active' : ''; ?>">
                        <span style="--variation-color-value: <?php echo $item->color; ?>;"></span>
                        <span class="ba-tooltip ba-top"><?php echo $item->value; ?></span>
                    </span>
<?php
                }  else if ($variation->type == 'image') {
                    $images = json_decode($item->images);
                    if (!empty($images)) {
                        $item->image = $images[0];
                    }
                    $image = !gridboxHelper::isExternal($item->image) ? JUri::root().$item->image : $item->image;
?>
                    <span data-value="<?php echo $item->option_key; ?>" class="<?php echo $flag ? 'active' : ''; ?>">
                        <span style="--variation-image-value: url(<?php echo $image; ?>);"></span>
                        <span class="ba-tooltip ba-top"><?php echo $item->value; ?></span>
                    </span>
<?php
                } else if ($variation->type == 'radio') {
?>
                    <div class="ba-checkbox-wrapper">
                        <span><?php echo $item->value; ?></span>
                        <label class="ba-radio">
                            <input type="radio" name="variation-<?php echo $item->field_id; ?>"
                                class="<?php echo $flag ? 'active' : ''; ?>"
                                value="<?php echo $item->option_key; ?>"<?php echo $flag ? ' checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>
<?php
                }
            }
            if ($variation->type == 'dropdown') {
?>
                <div class="ba-custom-select">
                <input readonly="" onfocus="this.blur()" type="text" value="<?php echo $textValue; ?>">
                <input type="hidden" value="<?php echo $value; ?>">
                <i class="ba-icons ba-icon-caret-down"></i>
                <ul><?php echo $li; ?></ul>
            </div>
<?php
            }
?>
        </div>
    </div>
<?php
}
?>
</div>
<?php
require_once JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/add-to-cart-extra-options.php';
?>
<div class="ba-add-to-cart-info">
    <div class="ba-add-to-cart-sku">
        <div class="ba-add-to-cart-row-label">
            <?php echo JText::_('SKU'); ?>
        </div>
        <div class="ba-add-to-cart-row-value">
            <?php echo $sku; ?>
        </div>
    </div>
    <div class="ba-add-to-cart-stock">
        <div class="ba-add-to-cart-row-label">
            <?php echo JText::_('IN_STOCK'); ?>
        </div>
        <div class="ba-add-to-cart-row-value">
            <?php echo $stock; ?>
        </div>
    </div>
</div>
<div class="ba-add-to-cart-price">
<?php
if ($prices->sale !== '') {
    
?>
    <span class="ba-add-to-cart-sale-price-wrapper <?php echo $currency->position; ?>">
        <span class="ba-add-to-cart-price-currency"><?php echo $currency->symbol; ?></span>
        <span class="ba-add-to-cart-price-value"><?php echo $prices->sale; ?></span>
    </span>
<?php
}
?>
    <span class="ba-add-to-cart-price-wrapper <?php echo $currency->position; ?>">
        <span class="ba-add-to-cart-price-currency"><?php echo $currency->symbol; ?></span>
        <span class="ba-add-to-cart-price-value"><?php echo $prices->regular; ?></span>
    </span>
</div>
<div class="ba-add-to-cart-button-wrapper<?php echo $disabled; ?>">
<?php
if (!isset($data->product_type) || ($data->product_type != 'digital' && $data->product_type != 'subscription')) {
?>
    <div class="ba-add-to-cart-quantity<?php echo $hasFileQty ? ' file-quantity-enabled' : ''; ?>">
        <i class="ba-icons ba-icon-minus" data-action="-"></i>
        <input type="text" value="<?php echo $min; ?>">
        <i class="ba-icons ba-icon-plus" data-action="+"></i>
    </div>
<?php
}
?>
    <div class="ba-add-to-cart-buttons-wrapper">
        <a class="ba-btn-transition" href="#"><?php echo $btn; ?></a>
        <span class="ba-add-to-wishlist">
            <i class="ba-icons ba-icon-heart"></i>
            <span class="ba-tooltip ba-top"><?php echo JText::_('ADD_TO_WISHLIST'); ?></span>
        </span>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();