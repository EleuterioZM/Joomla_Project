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
<div class="user-comment-container-wrapper" data-parent="<?php echo $comment->parent; ?>">
    <div class="user-comment-wrapper<?php echo $comment->status != 'approved' ? ' ba-not-approved-comment' : ''; ?>"
        id="commentID-<?php echo $comment->id; ?>">
        <div class="comment-user-info-wrapper">
            <span class="ba-author-avatar" style="background-image: url(<?php echo $avatar; ?>);"></span>
<?php
        if (self::$website->enable_gravatar == 1) {
?>
        <img src="<?php echo $avatar; ?>" class="ba-gravatar-img" style="display: none !important;"
            onerror="this.previousElementSibling.style.backgroundImage = 'url('+JUri+'components/com_gridbox/assets/images/default-user.png)';">
<?php
        }
?>
        </div>
        <div class="comment-data-wrapper">
            <div class="comment-user-info">
<?php
                if (!empty($user) && $user->type == 'user' && in_array($user->id, $moderators)) {
?>
                <span class="comment-moderator-user-settings">
                    <i class="ba-icons ba-icon-settings"></i>
                    <span class="ba-tooltip"><?php echo JText::_('MODERATE'); ?></span>
                </span>
<?php
                } else {
?>
                <span class="comment-report-user-comment">
                    <i class="ba-icons ba-icon-flag"></i>
                    <span class="ba-tooltip"><?php echo JText::_('REPORT_COMMENT'); ?></span>
                </span>
<?php
                }
?>
                <span class="comment-user-date"><?php echo $comment->date; ?></span>
                <span class="comment-user-name"><?php echo $comment->name; ?></span>
<?php
            if ($comment->user_type == 'user' && in_array($comment->user_id, $moderators)) {
?>
                <span class="comment-moderator-label"><?php echo gridboxHelper::$website->comments_moderator_label; ?></span>
<?php
            }
            if ($comment->status != 'approved') {
?>
                <span class="comment-not-approved-label"><?php echo JText::_(strtoupper($comment->status)); ?></span>
<?php
            }
?>
<?php
        if ($parent != 0) {
?>
            <span class="comment-reply-name"><i class="ba-icons ba-icon-mail-reply"></i><?php echo $replyName; ?></span>
<?php
        }
?>
            </div>
            <div class="comment-user-message-wrapper">
                <p class="comment-message"><?php echo $message; ?></p>
<?php
                if (!empty($user) && $user->type == $comment->user_type && $user->type != 'guest' && $user->id == $comment->user_id) {
?>
                    <div class="comment-edit-form-wrapper">
                        <div class="ba-comment-message-wrapper">
                            
                        </div>
                    </div>
<?php
                }
?>
            </div>
            <div class="comment-attachments-image-wrapper<?php echo !empty($attachments->images) ? ' not-empty-container' : ''; ?>">
<?php
            foreach ($attachments->images as $file) {
?>
                <span class="comment-attachment-image-type-wrapper">
                    <span class="comment-attachment-image-type" data-img="<?php echo $file->link ?>"
                        style="background-image: url(<?php echo $file->link ?>);"></span>
<?php
                if (!empty($user) && $user->type == $comment->user_type && $user->type != 'guest' && $user->id == $comment->user_id) {
?>
                    <i class="ba-icons ba-icon-close delete-comment-attachment-file" data-id="<?php echo $file->id; ?>"
                        data-filename="<?php echo $file->filename; ?>"></i>
<?php
                }
?>
                </span>
<?php
            }
?>
            </div>
            <div class="comment-attachments-wrapper<?php echo !empty($attachments->files) ? ' not-empty-container' : ''; ?>">
<?php
            foreach ($attachments->files as $file) {
?>
                <div class="comment-attachment-file">
                    <i class="ba-icons ba-icon-attachment"></i>
                    <a target="_blank" href="<?php echo $file->link ?>">
                        <?php echo $file->name ?>
                    </a>
<?php
                if (!empty($user) && $user->type == $comment->user_type && $user->type != 'guest' && $user->id == $comment->user_id) {
?>
                    <i class="ba-icons ba-icon-trash delete-comment-attachment-file" data-id="<?php echo $file->id; ?>"
                        data-filename="<?php echo $file->filename; ?>" data-type="file"></i>
<?php
                }
?>
                </div>
<?php
            }
?>
            </div>
            <div class="comment-likes-wrapper">
                <span class="comment-action-wrapper">
<?php
                if (!empty($user) && $user->type == $comment->user_type && $user->type != 'guest' && $user->id == $comment->user_id) {
?>
                    <span class="comment-edit-action">
                        <i class="ba-icons ba-icon-edit"></i>
                        <span><?php echo JText::_('EDIT'); ?></span>
                    </span>
<?php
                }
?>
                    <span class="comment-reply-action">
                        <i class="ba-icons ba-icon-keyboard"></i>
                        <span><?php echo JText::_('REPLY'); ?></span>
                    </span>
                    <span class="comment-share-action">
                        <i class="ba-icons ba-icon-share"></i>
                        <span><?php echo JText::_('SHARE'); ?></span>
                    </span>
<?php
                if (!empty($user) && $user->type == $comment->user_type && $user->type != 'guest' && $user->id == $comment->user_id) {
?>
                    <span class="comment-delete-action">
                        <i class="ba-icons ba-icon-trash"></i>
                        <span><?php echo JText::_('DELETE'); ?></span>
                    </span>
<?php
                }
?>
                </span>
                <span class="comment-likes-action-wrapper">
                    <span class="comment-likes-action<?php echo $status == 'likes' ? ' active' : ''; ?>" data-action="likes">
                        <i class="ba-icons ba-icon-thumb-up"></i>
                        <span class="likes-count"><?php echo $comment->likes; ?></span>
                    </span>
                    <span class="comment-likes-action<?php echo $status == 'dislikes' ? ' active' : ''; ?>" data-action="dislikes">
                        <i class="ba-icons ba-icon-thumb-down"></i>
                        <span class="likes-count"><?php echo $comment->dislikes; ?></span>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="comment-reply-form-wrapper" style="display: none;">
        <div class="ba-comments-login-wrapper">
            
        </div>
        <div class="ba-comment-message-wrapper">
            
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();