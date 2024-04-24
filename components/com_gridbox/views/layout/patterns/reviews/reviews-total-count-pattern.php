<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$floorRating = floor($rating);
$totalRating = (string)$rating;
if (strlen($totalRating) == 1) {
    $totalRating = $totalRating.'.0';
}
?>
<span class="ba-reviews-total-rating-wrapper">
    <span class="ba-reviews-total-rating"><?php echo $totalRating; ?></span>
    <span class="ba-review-rate-wrapper">
        <span class="ba-review-stars-wrapper">
<?php
        for ($i = 1; $i < 6; $i++) {
            $width = 'auto';
            if ($i == $floorRating + 1) {
                $width = (($rating - $floorRating) * 100).'%';
            }
?>
            <i class="ba-icons ba-icon-star<?php echo $i <= $floorRating ? ' active' : ''; ?>"
                style="width: <?php echo $width; ?>"></i>
<?php
        }
?>
        </span>
<?php
    if ($count > 0) {
?>
        <span itemscope="" itemtype="http://schema.org/CreativeWorkSeries">
            <meta itemprop="name" content="">
            <span itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                <meta itemprop="ratingValue" content="<?php echo $totalRating; ?>">
                <meta itemprop="reviewCount" content="<?php echo $count; ?>">
            </span>
        </span>
<?php
    }
?>
    </span>
</span>
<span class="ba-comments-total-count"><?php echo $count.' '.JText::_('REVIEWS'); ?></span>
<?php
if ($count > 0) {
?>
<select>
    <option value="recent"<?php echo $sortBy == 'recent' ? ' selected' : ''; ?>><?php echo JText::_('RECENT'); ?></option>
    <option value="oldest"<?php echo $sortBy == 'oldest' ? ' selected' : ''; ?>><?php echo JText::_('OLDEST'); ?></option>
    <option value="popular"<?php echo $sortBy == 'popular' ? ' selected' : ''; ?>><?php echo JText::_('POPULAR'); ?></option>
</select>
<?php
}
$string = ob_get_contents();
ob_end_clean();