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
<span class="ba-review-rate-title"><?php echo JText::_('RATE') ?></span>
<span class="ba-review-stars-wrapper<?php echo !gridboxHelper::$commentUser ? ' logout-reviews-user' : ''; ?>">
    <i class="ba-icons ba-icon-star" data-rating="1"></i>
    <i class="ba-icons ba-icon-star" data-rating="2"></i>
    <i class="ba-icons ba-icon-star" data-rating="3"></i>
    <i class="ba-icons ba-icon-star" data-rating="4"></i>
    <i class="ba-icons ba-icon-star" data-rating="5"></i>
</span>
<?php
$string = ob_get_contents();
ob_end_clean();