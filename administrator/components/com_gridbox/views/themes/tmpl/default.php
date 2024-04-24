<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js" type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
<?php
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('MODAL_DELETE') ?></p>
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
<div id="default-message-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <p class="modal-text"><?php echo JText::_('CANNOT_DELETE_DEFAULT') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CLOSE') ?></a>
    </div>
</div>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<div id="ba-gridbox-themes-dialog" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('THEMES'); ?></span>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="ba-filter-bar">
            <input type="text" class="search-gridbox-apps">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-group-wrapper gridbox-apps-wrapper upload-theme">
            
        </div>
    </div>
</div>
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=themes'); ?>"
    enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
    <div id="update-dialog" class="modal hide" style="display:none">
        <div class="modal-header">
            <h3><?php echo JText::_('ACCOUNT_LOGIN') ?></h3>
        </div>
        <div class="modal-body">
            <div id="form-update">
                
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CLOSE') ?></a>
        </div>
    </div>
    <div id="theme-edit-dialog" class="ba-modal-sm modal hide" style="display:none">
        <div class="modal-header">
            <h3><?php echo JText::_('THEME_SETTINGS') ?></h3>
        </div>
        <div class="modal-body">
            <input type="text" class="theme-name reset-input-margin" placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
            <span class="focus-underline"></span>
            <div class="ba-input-lg">
                <input type="text" class="theme-image" readonly onfocus="this.blur()"
                    placeholder="<?php echo JText::_('UPLOAD_IMAGE') ?>">
                <i class="zmdi zmdi-attachment-alt"></i>
            </div>
            <div class="ba-checkbox-parent">
                <label class="ba-checkbox ba-hide-checkbox">
                    <input type="checkbox" class="theme-default ">
                    <span></span>
                </label>
                <label><?php echo JText::_('DEFAULT_THEME') ?></label>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
            <a href="#" class="ba-btn-primary theme-apply"><?php echo JText::_('JTOOLBAR_APPLY') ?></a>
        </div>
    </div>
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('THEMES') ?></h1>
                        </div>
                        <div class="filter-search-wrapper">
                            <div>
                                <input type="text" name="filter_search" id="filter_search"
                                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                                       placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                                <i class="zmdi zmdi-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="apps-list-wrapper installed-themes-view">
<?php
                    foreach ($this->items as $i => $item) {
?>
                        <div class="gridbox-app-item" data-id="<?php echo $item->id; ?>">
                            <div class="gridbox-app-item-body">
                                <div class="image-container"
                                    style="background-image: url(<?php echo str_replace(' ', '%20', $item->image); ?>);"
                                    data-image="<?php echo str_replace('../', '', $item->image); ?>">
                                    <img src="components/com_gridbox/assets/images/default-theme.png">
                                </div>
<?php
                            if ($item->home == 1) {
?>
                                <span class="default-theme">
                                    <i class="zmdi zmdi-star"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('DEFAULT_THEME'); ?>
                                    </span>
                                </span>
<?php
                            }
?>
                                <p data-default="<?php echo $item->home; ?>">
                                    <span><?php echo $item->title; ?></span>
                                </p>
                            </div>
                            <div class="gridbox-app-item-footer">
                                <a class="gridbox-app-item-footer-action footer-action-create theme-settings" href="#">
                                    <i class="zmdi zmdi-settings"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('SETTINGS'); ?></span>
                                </a>
                                <a class="gridbox-app-item-footer-action footer-action-view theme-duplicate" href="#">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JTOOLBAR_DUPLICATE'); ?></span>
                                </a>
                                <a class="gridbox-app-item-footer-action footer-action-delete theme-delete" href="#">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </a>
                            </div>
                        </div>
<?php
                    }
?>
                        <div class="gridbox-app-item add-item add-new-theme">
                            <div class="gridbox-app-item-body">
                                <i class="zmdi zmdi-plus"></i>
                                <span><?php echo JText::_('UPLOAD_THEME'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                    	<input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="ba_view" value="themes">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>
<?php include(JPATH_COMPONENT.'/views/layouts/photo-editor.php'); ?>