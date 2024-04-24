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
<div class="info-wrapper">
<?php
if ($obj->count > 0) {
?>
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="<?php echo $obj->rating; ?>">
        <meta itemprop="reviewCount" content="<?php echo $obj->count; ?>">
    </div>
<?php
}
?>
    <span class="rating-wrapper">
        <span class="rating-title"><?php echo JText::_('RATING'); ?> </span>
        <span class="rating-value"><?php echo $obj->rating; ?></span>
    </span>
    <span class="votes-wrapper">
        (<span class="votes-count"><?php echo $obj->count; ?></span>
        <span class="votes-title"> <?php echo JText::_('VOTES'); ?></span>)
    </span>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();