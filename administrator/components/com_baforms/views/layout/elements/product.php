<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();

?>
<div class="ba-form-field-item ba-form-product-field <?php echo $field->options->suffix ?>" data-type="product"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-product-wrapper">
            <div class="ba-field-product-images-wrapper">
                <div class="ba-forms-slideshow">
                    <div class="ba-forms-slideshow-content">
<?php
                    $dotsCount = 0;
                    foreach ($field->options->images as $image) {
                        $dotsCount++;
?>
                        <div class="ba-forms-slideshow-item<?php echo $dotsCount == 1 ? ' active' : ''; ?>">
                            <div class="ba-forms-slideshow-image">
                                <img src="<?php echo JUri::root().$image->src; ?>">
                            </div>
                        </div>
<?php
                    }
?>
                    </div>
                    <div class="ba-forms-slideshow-navigation">
                        <a class="zmdi zmdi-chevron-left" data-slide="prev"></a>
                        <a class="zmdi zmdi-chevron-right" data-slide="next"></a>
                    </div>
                    <div class="ba-forms-slideshow-thumbnails" style="--dots-count: <?php echo $dotsCount; ?>;">
<?php
                    foreach ($field->options->images as $image) {
?>
                        <div class="ba-forms-slideshow-thumbnail" style="background-image: url(<?php echo JUri::root().$image->src; ?>); "></div>
<?php
                    }
?>
                    </div>
                </div>
            </div>
            <div class="ba-field-product-content-wrapper">
                <div class="ba-field-label-wrapper">
                    <<?php echo $field->options->tag; ?> class="ba-input-label-wrapper"
                        contenteditable="true"><?php echo $field->options->title; ?></<?php echo $field->options->tag; ?>>
                </div>
                <div class="ba-field-product-content-price">
                    <div class="ba-form-calculation-price-wrapper">
                        <span class="field-price-currency">$</span>
                        <span class="field-price-value">100.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ba-edit-item close-all-modals">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip ba-top tooltip-delay ba-hide-element"><?php echo JText::_('ITEM'); ?></span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('COPY_ITEM'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element">
                    <?php echo JText::_('DELETE_ITEM'); ?>
                </span>
            </span>
            <span class="ba-edit-text ba-hide-element"><?php echo JText::_('INPUT'); ?></span>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();