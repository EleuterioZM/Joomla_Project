<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$badges = self::$storeHelper->getProductBadges($page->id, $data);
ob_start();
if (($desktop && $desktop->store->badge) || !$desktop) {
?>
<div class="ba-blog-post-badge-wrapper">
<?php
$min = !empty($data->min) ? $data->min * 1 : 1;
if (!empty($variations_map) || (empty($variations_map) && ($data->stock === '' || $data->stock >= $min))) {
	foreach ($badges as $badge) {
?>
	<span class="ba-blog-post-badge" style="--badge-color:<?php echo $badge->color; ?>;"><?php echo $badge->title; ?></span>
<?php
	}
} else {
?>
	<span class="ba-blog-post-badge out-of-stock-badge"><?php echo JText::_('OUT_OF_STOCK'); ?></span>
<?php
}
?>
</div>
<?php
}
if (($desktop && $desktop->store->wishlist) || !$desktop) {
?>
<div class="ba-blog-post-wishlist-wrapper">
    <i class="ba-icons ba-icon-heart"></i>
    <span class="ba-tooltip ba-left"><?php echo JText::_('ADD_TO_WISHLIST'); ?></span>
</div>
<?php
}
$badges = ob_get_contents();
ob_end_clean();