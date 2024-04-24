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
<?php
if (($desktop && $desktop->view->author) || !$desktop) {
    echo $author;
}
if (($desktop && $desktop->view->date) || !$desktop) {
?>
<span class="intro-post-date"><?php echo $date; ?></span>
<?php
}
if (($desktop && $desktop->view->category) || !$desktop) {
?>
<span class="intro-post-category"><?php echo $category; ?></span>
<?php
}
if (($desktop && $desktop->view->comments) || !$desktop) {
?>
<span class="intro-post-comments"><a href="#total-count-wrapper"><?php echo $commentsStr; ?></a></span>
<?php
}
if (($desktop && $desktop->view->hits) || !$desktop) {
?>
<span class="intro-post-hits"><?php echo $views; ?></span>
<?php
}
if (($desktop && $desktop->view->reviews) || !$desktop) {
    if ($reviews->count == 0) {
        $reviews->rating = 0;
    }
    $floorRating = floor($reviews->rating);
?>
<span class="intro-post-reviews">
    <span class="ba-blog-post-rating-stars">
<?php
    for ($i = 1; $i < 6; $i++) {
        $width = 'auto';
        if ($i == $floorRating + 1) {
            $width = (($reviews->rating - $floorRating) * 100).'%';
        }
        echo '<i class="ba-icons ba-icon-star'.($i <= $floorRating ? ' active' : '').'" style="width: '.$width.';"></i>';
    }
?>
    </span>
    <a class="ba-blog-post-rating-count" href="#total-reviews-count-wrapper"><?php echo $reviewsStr; ?></a>
</span>
<?php
}
$out = ob_get_contents();
ob_end_clean();