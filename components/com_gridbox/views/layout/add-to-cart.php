<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('add-to-cart');
$obj->items->{'item-'.$now}->{'button-label'} = JText::_('ADD_TO_CART');
$currency = gridboxHelper::$store->currency;
?>
<div class="ba-item-add-to-cart ba-item" id="item-<?php echo $now++; ?>">
    <div class="ba-add-to-cart-wrapper">
        <div class="ba-add-to-cart-variations"></div>
        <div class="ba-add-to-cart-info">
            <div class="ba-add-to-cart-sku">
                <div class="ba-add-to-cart-row-label">
                    <?php echo JText::_('SKU'); ?>
                </div>
                <div class="ba-add-to-cart-row-value">
                    00000001
                </div>
            </div>
            <div class="ba-add-to-cart-stock">
                <div class="ba-add-to-cart-row-label">
                    <?php echo JText::_('IN_STOCK'); ?>
                </div>
                <div class="ba-add-to-cart-row-value">
                    27
                </div>
            </div>
        </div>
        <div class="ba-add-to-cart-price">
<?php
            $price = gridboxHelper::preparePrice(36.99, $currency->thousand, $currency->separator, $currency->decimals);
?>
            <span class="ba-add-to-cart-sale-price-wrapper <?php echo gridboxHelper::$store->currency->position; ?>">
                <span class="ba-add-to-cart-price-currency"><?php echo gridboxHelper::$store->currency->symbol; ?></span>
                <span class="ba-add-to-cart-price-value"><?php echo $price; ?></span>
            </span>
<?php
            $price = gridboxHelper::preparePrice(47.77, $currency->thousand, $currency->separator, $currency->decimals);
?>
            <span class="ba-add-to-cart-price-wrapper <?php echo gridboxHelper::$store->currency->position; ?>">
                <span class="ba-add-to-cart-price-currency"><?php echo gridboxHelper::$store->currency->symbol; ?></span>
                <span class="ba-add-to-cart-price-value"><?php echo $price; ?></span>
            </span>
        </div>
        <div class="ba-add-to-cart-button-wrapper">
            <div class="ba-add-to-cart-quantity">
                <i class="ba-icons ba-icon-minus" data-action="-"></i>
                <input type="text" value="1">
                <i class="ba-icons ba-icon-plus" data-action="+"></i>
            </div>
            <div class="ba-add-to-cart-buttons-wrapper">
                <a class="ba-btn-transition" href="#"><?php echo JText::_('ADD_TO_CART'); ?></a>
                <span class="ba-add-to-wishlist">
                    <i class="ba-icons ba-icon-heart"></i>
                    <span class="ba-tooltip ba-top"><?php echo JText::_('ADD_TO_WISHLIST'); ?></span>
                </span>
            </div>
        </div>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("COPY_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_TO_LIBRARY"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();