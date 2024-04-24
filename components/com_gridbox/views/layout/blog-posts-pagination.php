<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$prev = $active == 0 ? 1 : $active;
$next = $active == $pages - 1 ? $pages : $active + 2;
?>
<div class="ba-blog-posts-pagination">
<?php
if ($type != '') {
    $style = ($type == 'infinity' || $active == $pages - 1 || ($type == 'load-more-infinity' && $active > 0)) ? 'style="display:none !important"' : '';
?>
    <span class="<?php echo $active == $pages - 1 ? 'disabled' : ''; ?>" <?php echo $style; ?>>
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
        <a href="<?php echo JRoute::_($url.'&page=1'); ?>"
            <?php echo $active == 0 ? 'onclick="return false;"' : ''; ?>>
            <i class="ba-icons ba-icon-skip-previous"></i>
        </a>
    </span>
    <span class="<?php echo $active == 0 ? 'disabled' : ''; ?>">
        <a href="<?php echo JRoute::_($url.'&page='.$prev); ?>"
            <?php echo $active == 0 ? 'onclick="return false;"' : ''; ?>>
            <i class="ba-icons ba-icon-fast-rewind"></i>
        </a>
    </span>
<?php
for ($i = $start; $i < $max; $i++) {
?>
    <span class="<?php echo $i == $active ? 'active' : ''; ?>">
        <a href="<?php echo JRoute::_($url.'&page='.($i + 1)); ?>"
            <?php echo $i == $active ? 'onclick="return false;"' : ''; ?>>
            <?php echo ($i + 1); ?>
        </a>
    </span>
<?php
}
?>
    <span class="<?php echo $active == $pages - 1 ? 'disabled' : ''; ?>">
        <a href="<?php echo JRoute::_($url.'&page='.$next); ?>"
            <?php echo $active == $pages - 1 ? 'onclick="return false;"' : ''; ?>>
            <i class="ba-icons ba-icon-fast-forward"></i>
        </a>
    </span>
    <span class="<?php echo $active == $pages - 1 ? 'disabled' : ''; ?>">
        <a href="<?php echo JRoute::_($url.'&page='.$pages); ?>"
            <?php echo $active == $pages - 1 ? 'onclick="return false;"' : ''; ?>>
            <i class="ba-icons ba-icon-skip-next"></i>
        </a>
    </span>
<?php
}
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();