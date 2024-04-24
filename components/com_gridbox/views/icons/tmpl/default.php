<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<script>
    jQuery(window).on('keydown', function(event){
        window.parent.$g(window.parent).trigger(event);
    });
</script>
<div id="add-custom-icons-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <div>
            <h3 class="ba-modal-title"><?php echo JText::_('UPLOAD_CUSTOM_ICONS'); ?></h3>
            <label class="ba-help-icon">
                <i class="zmdi zmdi-help"></i>
                <span class="ba-tooltip ba-help">
                    <?php echo JText::_('UPLOAD_CUSTOM_ICONS_TOOLTIP'); ?>
                </span>
            </label>
        </div>
        <div class="ba-input-lg">
            <input type="text" class="custom-font-title reset-input-margin" placeholder="<?php echo JText::_('TITLE'); ?>">
            <span class="focus-underline"></span>
        </div>
        <div class="ba-input-lg">
            <input type="text" class="custom-font-select" readonly="" onfocus="this.blur()"
                placeholder="<?php echo JText::_('SELECT_ZIP_FILE'); ?>">
            <i class="zmdi zmdi-attachment-alt"></i>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
        <a href="#" class="ba-btn-primary install-custom-icons disable-button"><?php echo JText::_('SAVE') ?></a>
    </div>
</div>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3 class="ba-modal-title"><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('MODAL_DELETE') ?></p>
        <p class="modal-text global-library-delete" style="display: none;">
            <?php echo JText::_('ATTENTION_DELETE_GLOBAL') ?>
        </p>
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
<div class="general-tabs ba-icons-wrapper">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#outline-icons" data-toggle="tab">
                <i class="zmdi zmdi-texture"></i>
                <span class="ba-tooltip ba-bottom">Outline</span>
            </a>
        </li>
        <li>
            <a href="#material-icons" data-toggle="tab">
                <i class="zmdi zmdi-android"></i>
                <span class="ba-tooltip ba-bottom">Material</span>
            </a>
        </li>
        <li>
            <a href="#fontawesome-icons" data-toggle="tab">
                <i class="zmdi zmdi-flag"></i>
                <span class="ba-tooltip ba-bottom">Font Awesome 5</span>
            </a>
        </li>
        <li>
            <a href="#user-icons" data-toggle="tab">
                <i class="zmdi zmdi-face"></i>
                <span class="ba-tooltip ba-bottom"><?php echo JText::_('CUSTOM_FONT_ICONS'); ?></span>
            </a>
        </li>
    </ul>
    <div class="tabs-underline"></div>
    <div class="toolbar-wrapper">
        <div class="search-wrapper">
            <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>">
            <div class="ba-icon-search">
                <i class="zmdi zmdi-search"></i>
            </div>
        </div>
        <div class="right-icons-wrapper">
            <span class="add-custom-icons disable-button">
                <i class="zmdi zmdi-cloud-upload"></i>
                <span class="ba-tooltip ba-bottom"><?php echo JText::_('UPLOAD_CUSTOM_ICONS') ?></span>
            </span>
            <span class="delete-icons disable-button">
                <i class="zmdi zmdi-delete"></i>
                <span class="ba-tooltip ba-bottom"><?php echo JText::_('DELETE') ?></span>
            </span>
        </div>
    </div>
    <div class="tab-content">
        <div id="outline-icons" class="row-fluid tab-pane active">
            <?php include_once 'outline.php'; ?>
        </div>
        <div id="material-icons" class="row-fluid tab-pane">
            <?php include_once 'material-icons.php'; ?>
        </div>
        <div id="fontawesome-icons" class="row-fluid tab-pane">
            <?php include_once 'fontawesome.php'; ?>
        </div>
        <div id="user-icons" class="row-fluid tab-pane">
<?php
        foreach ($this->items as $value) {
?>
            <div class='ba-options-group'>
                <p class='ba-group-title'>
                    <label class="check-all">
                        <input type="checkbox">
                        <i class="zmdi zmdi-check-circle"></i>
                    </label>
                    <?php echo $value->title; ?>
                </p>
<?php
            echo '<link href="'.$value->css.'" rel="stylesheet" type="text/css" />';
            foreach ($value->items as $item) {
?>
                <div class='ba-group-element'>
                    <label class="font-checkbox">
                        <input type="checkbox" value="<?php echo $item->id; ?>">
                        <i class="zmdi zmdi-circle-o"></i>
                        <i class="zmdi zmdi-check"></i>
                    </label>
                    <i class='<?php echo $item->title; ?>'></i>
                    <span><?php echo $item->title; ?></span>
                </div>
<?php
                }
?>
            </div>
<?php
        }
?>
            <div class="empty-list">
                <i class="zmdi zmdi-alert-polygon"></i>
                <p><?php echo JText::_('NO_ITEMS_HERE'); ?></p>
            </div>
        </div>
    </div>
</div>
<form name="custom_fonts" style="display: none;">
    <input type="file" class="custom-fonts-files" name="custom-files">
</form>