<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="ba-blog-posts-pagination-wrapper">
    <div class="ba-blog-posts-pagination">
<?php
if ($pagination != '') {
    $style = ($pagination == 'infinity'||$active == $allPages - 1||($pagination == 'load-more-infinity'&&$active > 0))?'style="display:none !important"' : '';
?>
        <span class="<?php echo $active == $allPages - 1 ? 'disabled' : ''; ?>" <?php echo $style; ?>>
            <a href="<?php echo JRoute::_($url.'&page='.$next); ?>" data-page="<?php echo $next; ?>">
<?php
            echo JText::_('LOAD_MORE');
?>
            </a>
        </span>
<?php
} else {
?>
        <span class="<?php echo $active == 0 ? 'disabled' : ''; ?>">
            <a href="<?php echo JRoute::_($url.'&page=1'); ?>" data-page="1">
                <i class="ba-icons ba-icon-skip-previous"></i>
            </a>
        </span>
        <span class="<?php echo $active == 0 ? 'disabled' : ''; ?>">
            <a href="<?php echo JRoute::_($url.'&page='.$prev); ?>" data-page="<?php echo $prev; ?>">
                <i class="ba-icons ba-icon-fast-rewind"></i>
            </a>
        </span>
    <?php
        for ($i = $start; $i < $max; $i++) {
    ?>
        <span class="<?php echo $i == $active ? 'active' : ''; ?>">
            <a href="<?php echo JRoute::_($url.'&page='.($i + 1)); ?>" data-page="<?php echo ($i + 1); ?>">
                <?php echo ($i + 1); ?>
            </a>
        </span>
    <?php
        }
    ?>
        <span class="<?php echo $active == $allPages - 1 ? 'disabled' : ''; ?>">
            <a href="<?php echo JRoute::_($url.'&page='.$next); ?>" data-page="<?php echo $next; ?>">
                <i class="ba-icons ba-icon-fast-forward"></i>
            </a>
        </span>
        <span class="<?php echo $active == $allPages - 1 ? 'disabled' : ''; ?>">
            <a href="<?php echo JRoute::_($url.'&page='.$allPages); ?>" data-page="<?php echo $allPages; ?>">
                <i class="ba-icons ba-icon-skip-next"></i>
            </a>
        </span>
<?php
}
?>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();