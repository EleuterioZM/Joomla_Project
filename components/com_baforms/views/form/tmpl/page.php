<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$percentage = floor(100 / $pagesCount * $key).'%';
?>
<div class="ba-form-page<?php echo $key != 0 ? ' ba-hidden-form-page' : ''; ?>" data-page-key="<?php echo $page->key; ?>">
<?php
if (self::$design->theme->layout == 'lightbox') {
?>
    <div class="ba-forms-lightbox-row">
        <i class="ba-form-icons ba-icon-close" data-dismiss="formsModal"></i>
    </div>
<?php
}
?>
<?php
if ($navigation->style != 'hidden-navigation-style' && $pageCount > 1) {
?>
    <div class="ba-form-page-navigation-wrapper <?php echo $navigation->suffix; ?>"
        style="--progress-navigation-percentage: <?php echo $percentage; ?>;">
        <div class="ba-form-page-progress-navigation-header">
            <span class="progress-navigation-title"><?php echo JText::_('COMPLETE'); ?></span>
            <span class="progress-navigation-percentage"><?php echo $percentage; ?></span>
        </div>
        <div class="ba-form-page-navigation">
<?php
        foreach ($navigation->items as $i => $item) {
?>
            <span class="ba-form-page-navigation-title<?php echo $i == $key ? ' current-page' : ''; ?>">
                <span class="ba-form-page-navigation-counter"><?php echo ($i + 1); ?></span>
                <span class="ba-page-navigation-title"><?php echo $item->title; ?></span>
            </span>
<?php
        }
?>
        </div>
    </div>
<?php
}
?>
    <div class="ba-page-items">
<?php
    $width = 0;
    $columns_order = json_decode($page->columns_order, true);
    foreach ($columns_order as $ind) {
        if ($width == 0) {
?>
        <div class="ba-form-row">
            <div class="ba-form-column-wrapper">
<?php
        }
        $column = self::getFormColumns($ind, $id);
        $w = str_replace('span', '', $column->width);
        $width += intval($w);
        include $path.'column.php';
        echo $out;
        if ($width == 12) {
            $width = 0;
?>
            </div>
        </div>
<?php
        }
    }
?>
    </div>
<?php
if ($pageCount > 1) {
?>
    <div class="ba-form-page-break <?php echo $navigation->suffix; ?>">
        <div class="ba-form-page-break-buttons">
            <span class="ba-form-page-break-button" data-action="back"><?php echo JText::_('BACK'); ?></span>
            <span class="ba-form-page-break-button" data-action="next"
                <?php echo $navigation->auto ? 'data-auto="auto"' : ''; ?>><?php echo JText::_('NEXT'); ?></span>
<?php
        if ($navigation->progress) {
?>
            <span class="ba-form-save-progress-link" ><?php echo JText::_('SAVE_PROGRESS'); ?></span>
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
$out = ob_get_contents();
ob_end_clean();