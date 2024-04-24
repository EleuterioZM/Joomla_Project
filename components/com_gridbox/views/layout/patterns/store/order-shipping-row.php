<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$attr = $item->default || $count == 1 ? ' checked' : '';
?>
<div class="ba-checkout-order-form-row ba-checkout-order-form-shipping<?php echo $item->default || $count == 1 ? ' selected' : ''; ?>">
    <label class="ba-radio">
        <input type="radio" name="ba-checkout-shipping" value="<?php echo $item->id; ?>"<?php echo $attr; ?>
            data-price="<?php echo $price; ?>" data-tax="<?php echo $taxPrice; ?>"
            data-total="<?php echo $total; ?>" data-total-tax="<?php echo $totalTax; ?>">
        <span></span>
    </label>
    <div class="ba-checkout-order-row-title-wrapper">
        <span class="ba-checkout-order-row-title">
            <?php echo $item->title; ?>
        </span>
<?php
    if ($item->params->time->enabled) {
?>
        <div class="ost-delivery-time-wrapper">
            <span><?php echo $item->params->time->text; ?></span>
        </div>
<?php
    }
    if ($item->params->description->enabled || $item->carrier != 0) {
?>
        <div class="ba-checkout-order-description">
            <div class="ba-checkout-order-description-inner">
<?php
                if ($item->carrier != 0 && $item->carrier_item->service == 'inpost') {
                    $inpost = $item->carrier_item;
?>
                <div class="ba-inpost-shipping-wrapper">
                    <span class="inpost-selected-address"></span>
                    <span class="inpost-trigger-modal"><?php echo JText::_('SELECT_PARCEL_LOCKER'); ?></span>
                </div>
<?php
                }
                if ($item->params->description->enabled) {
                    echo $item->params->description->text;
                }
?>
            </div>
        </div>
<?php
    }
?>
    </div>
<?php
if ($item->params->type == 'free' || $item->params->type == 'pickup') {
?>
    <span class="ba-checkout-order-price-wrapper"><?php echo JText::_('FREE'); ?></span>
</div>
<?php
} else {
?>
    <span class="ba-checkout-order-price-wrapper <?php echo $currency->position; ?>">
        <span class="ba-checkout-order-price-currency"><?php echo $currency->symbol; ?></span>
        <span class="ba-checkout-order-price-value"><?php echo $price; ?></span>
    </span>
</div>
<?php
}
?>
<?php
$out = ob_get_contents();
ob_end_clean();