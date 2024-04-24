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
<div itemscope itemtype="http://schema.org/CreativeWorkSeries">
    <meta itemprop="name" content="">
    <div class="star-ratings-wrapper">
        <div class="stars-wrapper">
            <i class="ba-icons ba-icon-star active" data-rating="1"></i>
            <i class="ba-icons ba-icon-star active" data-rating="2"></i>
            <i class="ba-icons ba-icon-star active" data-rating="3"></i>
            <i class="ba-icons ba-icon-star active" data-rating="4"></i>
            <i class="ba-icons ba-icon-star active" data-rating="5"></i>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();