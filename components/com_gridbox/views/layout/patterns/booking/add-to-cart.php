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
<div class="add-to-cart-booking-calendar-wrapper">
<?php
if (!$isEditor && $bookingOptions->type == 'multiple') {
    $end_date = $end->format('Y-m-d');
    $today = gridboxHelper::formatDate($end_date);
    $attributes2 .= ' data-now="'.$end_date.'" data-value="'.$end_date.'" value="'.$today.'"';
?>
    <div class="add-to-cart-booking-calendar">
        <div class="ba-add-to-cart-row-label"><?php echo JText::_('CHECK_IN'); ?></div>
        <div class="open-calendar-wrapper">
            <div class="icons-cell">
                <i class="zmdi zmdi-calendar-alt"></i>
            </div>
            <input type="text" readonly class="open-calendar-dialog" data-type="range-dates"
                data-key="from" <?php echo $attributes; ?>>
        </div>
    </div>
    <div class="add-to-cart-booking-calendar">
        <div class="ba-add-to-cart-row-label"><?php echo JText::_('CHECK_OUT'); ?></div>
        <div class="open-calendar-wrapper">
            <div class="icons-cell">
                <i class="zmdi zmdi-calendar-alt"></i>
            </div>
            <input type="text" readonly class="open-calendar-dialog" data-type="range-dates"
                data-key="to" <?php echo $attributes2; ?>>
        </div>
    </div>
<?php
} else if (!$isEditor) {
?>
    <div class="add-to-cart-booking-calendar">
        <div class="ba-add-to-cart-row-label"><?php echo JText::_('SELECT_DATE'); ?></div>
        <div class="open-calendar-wrapper">
            <div class="icons-cell">
                <i class="zmdi zmdi-calendar-alt"></i>
            </div>
            <input type="text" readonly class="open-calendar-dialog" data-type="single-date" <?php echo $attributes; ?>>
        </div>
    </div>
<?php
}
?>
</div>

<?php
if (!$isEditor && $bookingOptions->type == 'single' && $bookingOptions->single->time == 'yes') {
?>
<div class="add-to-cart-booking-hours-wrapper">
    <div class="ba-add-to-cart-row-label"><?php echo JText::_('AVAILABLE_HOURS'); ?></div>
        <div class="add-to-cart-booking-available-hours-wrapper">
            
        
<?php
        foreach ($times as $slot) {
            $attributes = '';
            foreach ($slot as $key => $attribute) {
                $attributes .= ' data-' . $key . '="' . $attribute . '"';
            }
?>
            <span class="add-to-cart-booking-available-hours"
                <?php  echo $attributes; ?>><?php echo $slot->start ?></span>
<?php
        }
?>
        </div>
</div>
<?php
}
if (!$isEditor && $bookingOptions->type == 'single' &&
    ($bookingOptions->single->type == 'group' || $bookingOptions->single->type == 'group-session')) {
?>
<div class="add-to-cart-booking-guests-wrapper">
    <div class="ba-add-to-cart-row-label"><?php echo JText::_('GUESTS'); ?></div>
    <div class="ba-add-to-cart-guests">
        <i class="ba-icons ba-icon-minus disabled" data-action="-"></i>
        <input type="text" value="1" data-max="<?php echo $guests; ?>">
        <i class="ba-icons ba-icon-plus" data-action="+"></i>
    </div>
</div>
<?php
}
require_once JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/add-to-cart-extra-options.php';
?>    
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
<div class="ba-add-to-cart-button-wrapper">
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