<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$pagLimit = array(
    5 => 5,
    10 => 10,
    15 => 15,
    20 => 20,
    25 => 25,
    30 => 30,
    50 => 50,
    100 => 100,
    1 => JText::_('JALL'),
);
$user = JFactory::getUser();
?>
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-admin.css?<?php echo $this->version; ?>" type="text/css"/>
<style type="text/css">
    .ba-context-menu:not(.visible-context-menu) {
        display: none;
    }
</style>
<?php
    if ($user->authorise('core.edit', 'com_gridbox')) {
?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript">
    function makeDrag()
    {
        jQuery("tbody tr").draggable({
            cursor: 'move',
            cancel: null,
            helper: 'clone',
            revert: 'invalid',
            cursorAt: {
                left: 90,
                top: 20
            },
            handle : '.draggable-handler',
            start : function(){
                jQuery('.ba-folder-tree > ul ul').each(function(){
                    if (jQuery(this).closest('li').hasClass('visible-branch')) {
                        jQuery(this).find('> li > span').droppable('enable');
                    } else {
                        jQuery(this).find('> li > span').droppable('disable');
                    }
                })
            }
        }).disableSelection();
        jQuery(".ba-folder-tree li span[data-path], tbody tr:not(.ba-images)").droppable({
            greedy: true,
            hoverClass: "droppable-over",
            tolerance: 'pointer',
            drop: function(event, ui) {
                let str = ui.helper.find('.select-item').val(),
                    path = '',
                    obj = JSON.parse(str),
                    clone = ui.helper.clone();
                if (this.localName == 'tr') {
                    path = this.querySelector('span[data-path]').dataset.path;
                } else {
                    path = this.dataset.path;
                }
                clone.addClass('ba-dropping');
                setTimeout(function(){
                    clone.remove();
                }, 400);
                mediaManager.executeAction({
                    action: 'multipleMove',
                    path: path,
                    array: [obj.path]
                }).then(function(text){
                    top.app.showNotice(top.app._('SUCCESS_MOVED'));
                    mediaManager.getFoldersTree(text);
                    mediaManager.reloadFolder();
                });
                jQuery('tbody').append(clone);
            }
        });
    }
</script>
<?php
}
?>
<script src="<?php echo JUri::root(); ?>components/com_gridbox/assets/js/ba-uploader.js?<?php echo $this->version; ?>">
</script>
<script>
mediaManager.action = '<?php echo JUri::root(); ?>administrator/index.php?option=com_gridbox&task=uploader.executeAction';
mediaManager.types = <?php echo json_encode($this->uploader->types); ?>;
mediaManager.imageTypes = <?php echo json_encode($this->uploader->images); ?>;
mediaManager.direction = <?php echo json_encode($this->uploader->direction); ?>;
mediaManager.sorting = <?php echo json_encode($this->uploader->sorting); ?>;
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.ba-tooltip').forEach(function(tooltip){
        tooltip.parentNode.addEventListener('mouseenter', function(){
            this.tooltip = tooltip.cloneNode(true);
            document.body.append(this.tooltip);
            let width = this.tooltip.offsetWidth,
                height = this.tooltip.offsetHeight,
                coord = this.getBoundingClientRect(),
                y = tooltip.classList.contains('ba-bottom') ? coord.bottom : coord.top,
                center = coord.left +((coord.right - coord.left) / 2);
            if (tooltip.classList.contains('ba-top') || tooltip.classList.contains('ba-help')) {
                y -= (15 + height);
                center -= (width / 2)
            }
            if (tooltip.classList.contains('ba-bottom')) {
                y += 10;
                center -= (width / 2)
            }
            $g(this.tooltip).css({
                top: y+'px',
                left: center+'px'
            });
        })
        tooltip.parentNode.addEventListener('mouseleave', function(){
            let $tooltip = this.tooltip;
            $tooltip.classList.add('tooltip-hidden');
            setTimeout(function(){
                $tooltip.remove();
            }, 500);
        });
    });
});
</script>
<div id="ba-media-manager">
    <form autocomplete="off" target="form-target"
        action="<?php echo JRoute::_('index.php?option=com_gridbox&layout=uploader&id=&tmpl=component'); ?>"
        method="post" autocomplete="off" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <div id="create-folder-modal" class="ba-modal-sm modal hide">
            <div class="modal-body">
                <h3><?php echo JText::_('CREATE_FOLDER'); ?></h3>
                <input type="text" maxlength="260" name="new-folder" placeholder="<?php echo JText::_('ENTER_FOLDER_NAME') ?>">
                <span class="focus-underline"></span>
            </div>
            <div class="modal-footer">
                <a href="#" class="ba-btn" data-dismiss="modal">
                    <?php echo JText::_('CANCEL') ?>
                </a>
                <a href="#" class="ba-btn-primary" id="add-folder">
                    <?php echo JText::_('JTOOLBAR_APPLY') ?>
                </a>
            </div>
        </div>
        <div id="delete-modal" class="ba-modal-sm modal hide">
            <div class="modal-body">
                <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
                <p><?php echo JText::_('MODAL_DELETE'); ?></p>
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
        <div id="move-to-modal" class="ba-modal-md modal hide">
            <div class="modal-body">
                <div class="ba-modal-header">
                    <h3><?php echo JText::_('MOVE_TO'); ?></h3>
                    <i data-dismiss="modal" class="zmdi zmdi-close"></i>
                </div>
                <div class="availible-folders">
                    <ul>
                        <li>
                            <span data-path="<?php echo IMAGE_PATH; ?>">
                                <i class="zmdi zmdi-folder"></i>
                                <span><?php echo IMAGE_PATH; ?></span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="ba-btn" data-dismiss="modal">
                    <?php echo JText::_('CANCEL') ?>
                </a>
                <a href="#" class="ba-btn-primary apply-move">
                    <?php echo JText::_('JTOOLBAR_APPLY') ?>
                </a>
            </div>
        </div>
        <div id="rename-modal" class="ba-modal-sm modal hide">
            <div class="modal-body">
                <h3><?php echo JText::_('RENAME'); ?></h3>
                <input type="text" maxlength="260" class="new-name">
                <span class="focus-underline"></span>
            </div>
            <div class="modal-footer">
                <a href="#" class="ba-btn" data-dismiss="modal">
                    <?php echo JText::_('CANCEL') ?>
                </a>
                <a href="#" class="ba-btn-primary" id="apply-rename">
                    <?php echo JText::_('JTOOLBAR_APPLY') ?>
                </a>
            </div>
        </div>
        <div class ="row-fluid">
            <div class="row-fluid ba-media-header">
                <div class="span12">
                    <span class="ba-dialog-title"><?php echo JText::_('MEDIA_MANAGER'); ?></span>
                    <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
                    <i class="close-media zmdi zmdi-close"></i>
                </div>
                <div class="span12">
                    <div class="uploader-nav">
                        <div class="ba-breadcrumb">
<?php 
                        echo $this->uploader->getbreadcrumb();
?>
                        </div>
                        <div class="ba-media-manager-search-wrapper">
                            <input type="text" class="ba-media-manager-search-input"
                                placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>">
                            <i class="ba-media-manager-search-icon zmdi zmdi-search"></i>
                        </div>
                        <div class="control-toolbar">
                            <label class="media-manager-apply-wrapper">
                                <i class="zmdi zmdi-plus" id="ba-apply"></i>
                                <span class="ba-tooltip ba-top ba-hide-element">
                                    <?php echo JText::_('INSERT_SELECTED_ITEMS'); ?>
                                </span>
                            </label>
<?php
                        if ($user->authorise('core.create', 'com_gridbox')) {
?>
                            <label>
                                <i class="zmdi zmdi-cloud-upload" id="show-upload"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('UPLOAD_IMAGE'); ?>
                                </span>
                            </label>
                            <label>
                                <i class="zmdi zmdi-folder" id="show-folder"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('CREATE_FOLDER'); ?>
                                </span>
                            </label>
<?php
                        }
                        if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                            <label>
                                <i class="zmdi zmdi-forward" id="move-to"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('MOVE_TO'); ?>
                                </span>
                            </label>
<?php
                        }
                        if ($user->authorise('core.delete', 'com_gridbox')) {
?>
                            <label>
                                <i class="zmdi zmdi-delete" id="delete-items"></i>
                            </label>
<?php
                        }
?>
                            <div class="pagination-limit">
                                <div class="ba-custom-select pagination-limit-select">
                                    <input readonly value="<?php echo $pagLimit[$this->_limit]; ?>"
                                       data-value="<?php echo $this->_limit; ?>"
                                       size="<?php echo strlen($this->_limit); ?>" type="text">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
<?php
                                    foreach ($pagLimit as $key => $title) {
?>
                                        <li data-value="<?php echo $key; ?>"
                                            class="<?php echo $key == $this->_limit ? 'selected' : ''; ?>">
<?php
                                        if ($key == $this->_limit) {
?>
                                            <i class="zmdi zmdi-check"></i>
<?php
                                        }
                                        echo $title;
?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid ba-media-manager">
                <div class="ba-folder-tree" style="width: 30%;">
<?php
                    echo $this->uploader->getFoldersTree();
?>
                </div>
                <div class="ba-work-area" style="width: 70%;">
                    <div class="table-head">
                        <div class="files-name">
                            <label>
                                <input type="checkbox" name="ba-rm[]" value="" id="check-all">
                                <i class="zmdi zmdi-check-circle check-all"></i>
                            </label>
                            <span data-sorting="name"
                                <?php echo $this->uploader->sorting == 'name' ? ' class="active"' : '' ?>>
                                <?php echo JText::_('NAME'); ?>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                </span>
                            </span>
                        </div>
                        <div class="modified">
                            <span data-sorting="modified"
                                <?php echo $this->uploader->sorting == 'modified' ? ' class="active"' : '' ?>>
                                <?php echo JText::_('MODIFIED'); ?>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                </span>
                            </span>
                        </div>
                        <div class="files-size">
                            <span data-sorting="size"
                                <?php echo $this->uploader->sorting == 'size' ? ' class="active"' : '' ?>>
                                <?php echo JText::_('FILE_SIZE'); ?>
                                <span class="ba-tooltip ba-bottom ba-hide-element">
                                    <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="table-body">
<?php
                        echo $this->uploader->getItemsTable();
?>
                    </div>
                    <div class="pagination">
<?php
                        echo $this->uploader->getPaginator();
?>
                    </div>
                </div>
            </div>
        </div>
        <div class="ba-context-menu empty-context-menu">
<?php
        if ($user->authorise('core.create', 'com_gridbox')) {
?>
            <span class="upload-file ba-group-element"><i class="zmdi zmdi-cloud-upload"></i><?php echo JText::_('UPLOAD_FILE'); ?></span>
            <span class="create-folder"><i class="zmdi zmdi-folder"></i><?php echo JText::_('CREATE_FOLDER'); ?></span>
<?php
        }
?> 
        </div>
        <div class="ba-context-menu files-context-menu">
<?php
        if ($user->authorise('core.edit', 'com_gridbox')) {
            if (gridboxHelper::checkSystemApp('photo-editor')) {
?>
            <span class="edit-image"><i class="zmdi zmdi-camera-alt"></i><?php echo JText::_('PHOTO_EDITOR'); ?></span>
<?php
            }
?>
            <span class="rename"><i class="zmdi zmdi-edit"></i><?php echo JText::_('RENAME'); ?></span>
            <span class="move-to"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
<?php
        }
?>
            <span class="download"><i class="zmdi zmdi-download"></i><?php echo JText::_('DOWNLOAD'); ?></span>
<?php
        if ($user->authorise('core.delete', 'com_gridbox')) {
?>
            <span class="delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
<?php
        }
?> 
        </div>
        <div class="ba-context-menu folders-context-menu">
<?php
        if ($user->authorise('core.edit', 'com_gridbox')) {
?>
            <span class="rename"><i class="zmdi zmdi-edit"></i><?php echo JText::_('RENAME'); ?></span>
            <span class="move-to"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
<?php
        }
        if ($user->authorise('core.delete', 'com_gridbox')) {
?>
            <span class="delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
<?php
        }
?>
        </div>
        <input type="hidden" name="task" value="grid.uploader" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
<div id="file-upload-form" style="display: none;">
    <form enctype="multipart/form-data" method="post"
        action="<?php echo JUri::base(); ?>index.php?option=com_gridbox&task=uploader.formUpload">
        <input type="file" multiple name="files[]">
    </form>
    
</div>