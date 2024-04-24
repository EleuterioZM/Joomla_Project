<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$submissionsCount = baformsHelper::getUnreadSubmissionsCount();
?>
<div class="ba-sidebar">
    <div>
        <span class="<?php echo baformsHelper::checkActive('forms'); ?>">
            <a href="index.php?option=com_baforms">
                <span class="zmdi zmdi-file"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('FORMS'); ?></span>
        </span>
        <span class="<?php echo baformsHelper::checkActive('submissions'); ?>">
            <a href="index.php?option=com_baforms&view=submissions">
                <span class="zmdi zmdi-inbox"></span>
                <?php
                if ($submissionsCount > 0) {
?>
                    <span class="unread-submissions-count"><?php echo $submissionsCount; ?></span>
<?php
                }
?>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('SUBMISSIONS'); ?></span>
        </span>
        <div class="ba-system-actions">
            <span class="<?php echo baformsHelper::checkActive('trashed'); ?>">
                <a href="index.php?option=com_baforms&view=trashed">
                    <span class="zmdi zmdi-delete"></span>
                </a>
                <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('TRASHED_ITEMS'); ?></span>
            </span>
        </div>
        <span class="forms-options">
            <a href="#">
                <span class="zmdi zmdi-settings"></span>
            </a>
        </span>
    </div>
</div>
<div id="import-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('IMPORT'); ?></h3>
        <label class="ba-help-icon">
            <i class="zmdi zmdi-help"></i>
            <span class="ba-tooltip ba-help ba-hide-element">
                <?php echo JText::_('IMPORT_FORMS_TOOLTIP'); ?> 
            </span>
        </label>
    </div>
    <div class="modal-body">
        <div class="ba-input-lg">
            <input id="theme-import-trigger" class="theme-import-trigger" readonly
                type="text" placeholder="<?php echo JText::_('SELECT'); ?>">
            <i class="zmdi zmdi-attachment-alt theme-import-trigger"></i>
            <input type="file" id="theme-import-file" name="ba-files[]" style="display: none;">
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary apply-import">
            <?php echo JText::_('INSTALL') ?>
        </a>
    </div>
</div>