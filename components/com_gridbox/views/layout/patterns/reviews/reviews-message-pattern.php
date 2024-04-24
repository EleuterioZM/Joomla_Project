<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if (!gridboxHelper::$commentUser) {
    $placeholder = 'PLEASE_LOGIN_TO_REVIEW';
} else {
    $placeholder = 'WRITE_REVIEW_HERE';
}
?>
<textarea placeholder="<?php echo JText::_($placeholder); ?>" class="ba-comment-message"></textarea>
<div class="ba-comment-xhr-attachment-wrapper"></div>
<div class="ba-comments-icons-wrapper">
<?php
    if (gridboxHelper::$website->reviews_enable_attachment == 1) {
?>
    <span class="ba-comments-attachments-wrapper">
        <span class="ba-comments-attachment-file-wrapper" data-type="image">
            <i class="ba-icons ba-icon-camera ba-comment-attachment-trigger"></i>
            <input class="ba-comment-attachment" type="file" style="display: none !important;" multiple
                data-size="<?php echo gridboxHelper::$website->reviews_attachment_size; ?>"
                data-types="gif, jpg, jpeg, png, svg, webp" data-attach="image">
            <span class="ba-comment-attachment-trigger"><?php echo JText::_('UPLOAD_PHOTOS'); ?></span>
        </span>
    </span>
<?php
    }
?>
</div>
<div class="ba-comments-captcha-wrapper">
    
</div>
<span class="ba-submit-comment-wrapper">
    <span class="ba-submit-cancel"><?php echo JText::_('CANCEL'); ?></span>
    <span class="ba-submit-comment" data-type="submit"><?php echo JText::_('SUBMIT_REVIEW'); ?></span>
</span>
<?php
$string = ob_get_contents();
ob_end_clean();