<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$stateStr = baformsHelper::checkFormsState();
$state = json_decode($stateStr);
$stateCount = !isset($state->data) ? 1 : 0;
if ($this->about->tag != 'pro') {
    $stateCount = 0;
}
?>
<script type="text/javascript" src="components/com_baforms/assets/libraries/bootstrap/bootstrap.js"></script>
<script type="text/javascript">
var JUri = '<?php echo JUri::root(); ?>',
    uploads_storage = '<?php echo UPLOADS_STORAGE; ?>',
    pdf_storage = '<?php echo PDF_STORAGE; ?>';
<?php echo baformsHelper::getFormsLanguage(); ?>
</script>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
    var str = "<div class='btn-wrapper' id='toolbar-language'>";
    str += "<button value='About' class='btn btn-small'><span class='icon-star'>";
    str += "</span><?php echo JText::_('LANGUAGE'); ?></button></div>";
    str += "<div class='btn-wrapper ba-dashboard-popover-trigger' id='toolbar-about' data-target='ba-dashboard-about'>";
    str += "<button class='btn btn-small'><span class='icon-bookmark' data-notification='<?php echo $stateCount; ?>'></span>";
    str += "<?php echo JText::_('ABOUT') ?></button></div>";
    jQuery('#toolbar').append(str);
});
</script>
<div id="ba-notification">
    <i class="zmdi zmdi-close"></i>
    <h4><?php echo JText::_('ERROR'); ?></h4>
    <p></p>
</div>
<div class="ba-context-menu options-context-menu" style="display: none">
    <span class="export-forms">
        <i class="zmdi zmdi-upload"></i>
        <span class="ba-context-menu-title">
            <?php echo JText::_('EXPORT'); ?>
        </span>
    </span>
    <span class="import-forms">
        <i class="zmdi zmdi-download "></i>
        <span class="ba-context-menu-title">
            <?php echo JText::_('IMPORT'); ?>
        </span>
    </span>
<?php
if ($this->user->authorise('core.admin', 'com_baforms') || $this->user->authorise('core.options', 'com_baforms')) {
?>
    <span class="options ba-group-element">
        <a href="index.php?option=com_config&view=component&component=com_baforms&path=" class="default-action">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('SETTINGS'); ?>
            </span>
        </a>
    </span>
<?php
}
?>
</div>
<div class="ba-dashboard-apps-dialog ba-dashboard-about">
    <div class="ba-dashboard-apps-body">
        <div class="ba-forms-dashboard-row forms-version-wrapper">
            <i class="zmdi zmdi-info"></i>
            <span>Forms</span>
            <span class="forms-version"><?php echo $this->about->version; ?></span>
        </div>
<?php
    if ($this->about->tag == 'pro' && $this->user->get('isRoot', false, 'bool')) {
?>
        <div class="ba-forms-dashboard-row forms-deactivate-license"
            <?php echo isset($state->data) ? '' : 'style="display:none;"'; ?>>
            <i class="zmdi zmdi-shield-check"></i>
            <span><?php echo JText::_('YOUR_LICENSE_ACTIVE'); ?></span>
            <a class="deactivate-link dashboard-link-action" href="#"><?php echo JText::_('DEACTIVATE'); ?></a>
        </div>
        <div class="ba-forms-dashboard-row forms-activate-license"
            <?php echo !isset($state->data) ? '' : 'style="display:none;"'; ?>>
            <i class="zmdi zmdi-shield-check"></i>
            <span><?php echo JText::_('ACTIVATE_LICENSE'); ?></span>
            <a class="activate-link dashboard-link-action" href="#"><?php echo JText::_('ACTIVATE'); ?></a>
        </div>
<?php
    }
    if ($this->user->authorise('core.edit', 'com_baforms')) {
?>
        <div class="ba-forms-dashboard-row forms-update-wrapper">
            <i class="zmdi zmdi-check-circle"></i>
            <span><?php echo JText::_('FORMS_IS_UP_TO_DATE'); ?></span>
        </div>
<?php
    }
?>
    </div>
    <div class="ba-dashboard-apps-footer">
        <span>© <?php echo date('Y'); ?> <a href="https://www.balbooa.com/" target="_blink">Balbooa.com</a> All Rights Reserved.</span>
    </div>
</div>
<div id="deactivate-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('LICENSE_DEACTIVATION'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('ARE_YOU_SURE_DEACTIVATE') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary red-btn" id="apply-deactivate">
            <?php echo JText::_('APPLY') ?>
        </a>
    </div>
</div>
<div id="login-modal" class="ba-modal-sm modal hide" aria-hidden="true" style="display: none;">
    <div class="modal-body">
        
    </div>
</div>
<div id="languages-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <div class="languages-wrapper">

        </div>
    </div>
</div>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('DELETE_QUESTION') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary red-btn" id="apply-delete">
            <?php echo JText::_('DELETE') ?>
        </a>
    </div>
</div>