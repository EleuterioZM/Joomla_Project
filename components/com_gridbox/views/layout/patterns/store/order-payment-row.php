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
<div class="ba-checkout-order-form-row ba-checkout-order-form-payment<?php echo $item->default || $count == 1 ? ' selected' : ''; ?>">
    <label class="ba-radio">
        <input type="radio" name="ba-checkout-payment" value="<?php echo $item->id; ?>"
            data-type="<?php echo $item->type; ?>"<?php echo $attr; ?>>
        <span></span>
    </label>
    <div class="ba-checkout-order-row-title-wrapper">
        <span class="ba-checkout-order-row-title">
            <?php echo $item->title; ?>
        </span>
<?php
    if ($item->type == 'offline' && !empty($settings->description)) {
?>
        <div class="ba-checkout-order-description">
            <div class="ba-checkout-order-description-inner"><?php echo $settings->description; ?></div>
        </div>
<?php
    }
?>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();