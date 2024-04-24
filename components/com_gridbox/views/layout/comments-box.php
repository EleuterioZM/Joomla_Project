<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('comments-box');
?>
<div class="ba-item-comments-box ba-item" id="<?php echo 'item-'.$now; ?>">
    <div class="ba-comments-box-wrapper">
        <div class="ba-comments-login-wrapper">
            
        </div>
        <div class="ba-comment-message-wrapper">
            <textarea placeholder="<?php echo JText::_('WRITE_COMMENT_HERE'); ?>" class="ba-comment-message"></textarea>
            <div class="ba-comment-xhr-attachment-wrapper"></div>
            <div class="ba-comments-icons-wrapper">
                <i class="ba-icons ba-icon-mood ba-comment-smiles-picker"></i>
                <span class="ba-comments-attachments-wrapper">
                    <span class="ba-comments-attachment-file-wrapper" data-type="file">
                        <i class="ba-icons ba-icon-attachment ba-comment-attachment-trigger"></i>
                        <input class="ba-comment-attachment" type="file" style="display: none !important;" multiple
                            data-size="<?php echo gridboxHelper::$website->attachment_size; ?>"
                            data-types="<?php echo gridboxHelper::$website->attachment_types; ?>" data-attach="file">
                    </span>
                    <span class="ba-comments-attachment-file-wrapper" data-type="image">
                        <i class="ba-icons ba-icon-camera ba-comment-attachment-trigger"></i>
                        <input class="ba-comment-attachment" type="file" style="display: none !important;" multiple
                            data-size="<?php echo gridboxHelper::$website->attachment_size; ?>"
                            data-types="gif, jpg, jpeg, png, svg, webp" data-attach="image">
                    </span>
                </span>
            </div>
            <div class="ba-comments-captcha-wrapper">
                
            </div>
            <span class="ba-submit-comment" data-type="submit"><?php echo JText::_('COMMENT'); ?></span>
        </div>
        <a class="total-count-wrapper"></a>
        <div class="ba-comments-total-count-wrapper">
            <span class="ba-comments-total-count"></span>
            <select>
                <option value="recent"><?php echo JText::_('RECENT'); ?></option>
                <option value="oldest"><?php echo JText::_('OLDEST'); ?></option>
                <option value="popular"><?php echo JText::_('POPULAR'); ?></option>
            </select>
        </div>
        <div class="users-comments-wrapper"></div>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("COPY_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_TO_LIBRARY"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model">
        <div class="ba-bm-top"></div>
        <div class="ba-bm-left"></div>
        <div class="ba-bm-bottom"></div>
        <div class="ba-bm-right"></div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();