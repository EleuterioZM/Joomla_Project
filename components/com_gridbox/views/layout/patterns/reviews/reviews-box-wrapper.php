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
<a class="total-reviews-count-wrapper"></a>
<div class="ba-comments-total-count-wrapper">
    <span class="ba-comments-total-count"></span>
    <select>
        <option value="recent"><?php echo JText::_('RECENT'); ?></option>
        <option value="oldest"><?php echo JText::_('OLDEST'); ?></option>
        <option value="popular"><?php echo JText::_('POPULAR'); ?></option>
    </select>
</div>
<div class="ba-leave-review-wrapper">
    <span class="ba-leave-review-btn"><?php echo JText::_('LEAVE_REVIEW'); ?></span>
    <div class="ba-leave-review-box-wrapper">
        <div class="ba-comments-login-wrapper"></div>
        <span class="ba-review-rate-wrapper">
            <span class="ba-review-rate-title"><?php echo JText::_('RATE') ?></span>
            <span class="ba-review-stars-wrapper">
                <i class="ba-icons ba-icon-star" data-rating="1"></i>
                <i class="ba-icons ba-icon-star" data-rating="2"></i>
                <i class="ba-icons ba-icon-star" data-rating="3"></i>
                <i class="ba-icons ba-icon-star" data-rating="4"></i>
                <i class="ba-icons ba-icon-star" data-rating="5"></i>
            </span>
        </span>
        <div class="ba-comment-message-wrapper">
            
        </div>
    </div>
</div>
<div class="users-comments-wrapper"></div>
<?php
$string = ob_get_contents();
ob_end_clean();