<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if (!isset($pagesCount)) {
    $pagesCount = 1;
    $ind = 0;
}
$percentage = floor(100 / $pagesCount * $ind).'%';
?>
<div class="ba-form-page" id="ba-form-page-1" data-id="0" data-title="">
    <div class="ba-forms-lightbox-row">
        <i class="zmdi zmdi-close"></i>
    </div>
    <div class="ba-edit-item close-all-modals">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip ba-top tooltip-delay ba-hide-element"><?php echo JText::_('PAGE'); ?></span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-plus-circle ba-add-rows"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('ADD_NEW_ROW'); ?></span>
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
            <span class="ba-edit-text ba-hide-element"><?php echo JText::_('PAGE'); ?></span>
        </div>
    </div>
    <div class="ba-form-page-navigation-wrapper" style="--progress-navigation-percentage: <?php echo $percentage; ?>;">
        <div class="ba-form-page-progress-navigation-header">
            <span class="progress-navigation-title"><?php echo JText::_('COMPLETE'); ?></span>
            <span class="progress-navigation-percentage"><?php echo $percentage; ?></span>
        </div>
        <div class="ba-form-page-navigation">
<?php
    if (isset($navigation)) {
        foreach ($navigation->items as $i => $item) {
?>
            <span class="ba-form-page-navigation-title<?php echo $i == $ind ? ' current-page' : ''; ?>">
                <span class="ba-form-page-navigation-counter"><?php echo ($i + 1); ?></span>
                <span class="ba-page-navigation-title"><?php echo $item->title; ?></span>
            </span>
<?php
        }
    }
?>
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
                <span class="ba-edit-text ba-hide-element"><?php echo JText::_('TEXT'); ?></span>
            </div>
        </div>
    </div>
    <div class="ba-page-items">[ba-rows]</div>
    <div class="ba-page-resizer" data-position="left">
        <span>
            <i class="zmdi zmdi-more-vert"></i>
        </span>
    </div>
    <div class="ba-page-resizer" data-position="right">
        <span>
            <i class="zmdi zmdi-more-vert"></i>
        </span>
    </div>
    <div class="ba-form-page-break">
        <div class="ba-form-page-break-buttons">
            <span class="ba-form-page-break-button" data-action="back"><?php echo JText::_('BACK'); ?></span>
            <span class="ba-form-page-break-button" data-action="next"><?php echo JText::_('NEXT'); ?></span>
            <span class="ba-form-save-progress-link" ><?php echo JText::_('SAVE_PROGRESS'); ?></span>
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
                <span class="ba-edit-text ba-hide-element"><?php echo JText::_('TEXT'); ?></span>
            </div>
        </div>
    </div>
    <div class="page-info">100%</div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();