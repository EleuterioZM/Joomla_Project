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
<div class="ba-store-wishlist-backdrop">
    <div class="ba-store-wishlist-close"></div>
    <div class="ba-store-wishlist ba-container">
        <div class="ba-store-wishlist-close-wrapper">
            <i class="ba-icons ba-icon-close ba-store-wishlist-close"></i>
        </div>
        <div class="row-fluid">
            <div class="ba-wishlist-headline-wrapper">
                <span class="ba-wishlist-headline"><?php echo JText::_('WISHLIST'); ?></span>
            </div>
<?php
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/wishlist-products-list.php';
        if (!$wishlist->empty) {
?>
            <div class="ba-wishlist-checkout">
                <div class="ba-wishlist-checkout-row">
                    <span class="ba-wishlist-checkout-title ba-clear-wishlist">
                        <?php echo JText::_('CLEAR_MY_WISHLIST'); ?>
                    </span>
                </div>
                <div class="ba-wishlist-checkout-row ba-wishlist-btn-wrapper" data-exists="<?php echo $existsProducts; ?>">
                    <span class="ba-wishlist-add-all-btn"><?php echo JText::_('ADD_ALL_TO_CART'); ?></span>
                </div>
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