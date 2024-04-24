<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div class="ba-form-total-wrapper">
    <div class="ba-form-products-cart">
        <div class="ba-form-product-row">
            <div class="ba-form-product-title-cell"></div>
            <div class="ba-form-product-quantity-cell"></div>
            <div class="ba-form-product-total-cell">
                <span class="field-price-currency"></span>
                <span class="field-price-value"></span>
            </div>
        </div>
    </div>
    <div class="ba-field-container">
        <div class="ba-cart-total-wrapper">
            <div class="ba-cart-total-container">
                <div class="ba-cart-total-container-row ba-cart-subtotal-row">
                    <span class="ba-cart-row-title"><?php echo JText::_('SUBTOTAL'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper">
                            <span class="field-price-currency"></span>
                            <span class="field-price-value"></span>
                        </div>
                    </div>
                </div>
                <div class="ba-cart-total-container-row ba-cart-shipping-row">
                    <span class="ba-cart-row-title"><?php echo JText::_('SHIPPING'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-cart-shipping-item">
                            <span class="ba-shipping-title">
                                <span class="ba-form-shipping-title"></span>
                            </span>
                            <div class="ba-form-calculation-price-wrapper">
                                <span class="field-price-currency"></span>
                                <span class="field-price-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ba-cart-total-container-row ba-cart-discount-row">
                    <span class="ba-cart-row-title"><?php echo JText::_('DISCOUNT'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper">
                            <span class="field-price-currency"></span>
                            <span class="field-price-value"></span>
                        </div>
                    </div>
                </div>
                <div class="ba-cart-total-container-row ba-cart-tax-row">
                    <span class="ba-cart-row-title"></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper">
                            <span class="field-price-currency"></span>
                            <span class="field-price-value"></span>
                        </div>
                    </div>
                </div>
                <div class="ba-cart-total-container-row ba-cart-total-row">
                    <span class="ba-cart-row-title"><?php echo JText::_('TOTAL'); ?></span>
                    <div class="ba-cart-row-content">
                        <div class="ba-form-calculation-price-wrapper">
                            <span class="field-price-currency"></span>
                            <span class="field-price-value"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>