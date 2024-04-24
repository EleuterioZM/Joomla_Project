<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

if (JVERSION >= '3.4.0') {
    JHtml::_('behavior.formvalidator');
} else {
    JHtml::_('behavior.formvalidation');
}
$uploading = new StdClass();
$uploading->const = JText::_('UPLOADING_MEDIA');
$uploading->url = JUri::root();
$uploading = json_encode($uploading);
$mediaHelper = new JHelperMedia;
$language = JFactory::getLanguage();
$language->load('com_media', JPATH_ADMINISTRATOR);
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
?>

<link rel="stylesheet" href="components/com_baforms/assets/css/ba-admin.css?<?php echo $this->about->version; ?>" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'/components/com_baforms/assets/icons/material/material.css'; ?>">
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
                        jQuery(this).find('> li > a').droppable('enable');
                    } else {
                        jQuery(this).find('> li > a').droppable('disable');
                    }
                })
            }
        }).disableSelection();
        jQuery(".ba-folder-tree li a, tbody tr:not(.ba-images)").droppable({
            greedy: true,
            hoverClass: "droppable-over",
            tolerance: 'pointer',
            drop: function(event, ui) {
                var draggable = ui.draggable,
                    move = '';
                ui.draggable.remove();
                if (ui.helper.hasClass('ba-images')) {
                    path = ui.helper.find('.ba-obj').val();
                    path = JSON.parse(path);
                    path = path.path;
                } else {
                    path = ui.helper.find('a.folder-list').attr('href');
                    path = path.split('&');
                    for (var i = 0; i < path.length; i++) {
                        path[i] = path[i].split('=');
                        if (path[i][0] == 'folder') {
                            path = path[i][1];
                            break;
                        }
                    }
                }
                var clone = ui.helper.clone();
                clone.addClass('ba-dropping');
                setTimeout(function(){
                    clone.remove();
                }, 400)
                jQuery('tbody').append(clone)
                var target = jQuery(this).find('a').attr('href');
                if (!target) {
                    target = jQuery(this).attr('href')
                }
                target = target.split('&');
                for (var i = 0; i < target.length; i++) {
                    target[i] = target[i].split('=');
                    if (target[i][0] == 'folder') {
                        move = target[i][1];
                        break;
                    }
                }
                jQuery.ajax({
                    type:"POST",
                    dataType:'text',
                    url:"index.php?option=com_baforms&view=uploader&task=uploader.moveTo",
                    data:{
                        'ba_image' : path,
                        'ba_folder' : move
                    },
                    success: function(msg) {
                        msg = JSON.parse(msg);
                        top.showNotice(msg.message)
                    }
                });
            }
        });
    }
</script>
<script src="components/com_baforms/assets/js/ba-uploader.js?<?php echo $this->about->version; ?>"></script>
<input type="hidden" id="uploading-media" value="<?php echo htmlentities($uploading); ?>">
<input type="hidden" id="post-max-size" value="<?php echo $mediaHelper->toBytes(ini_get('post_max_size')); ?>">
<input type="hidden" id="post-max-error" value="<?php echo $language->_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'); ?>">
<input type="hidden" id="success-upload" value="<?php echo JText::_('SUCCESS_UPLOAD'); ?>">
<div id="ba-media-manager">
    <form autocomplete="off" target="form-target"
        action="<?php echo JRoute::_('index.php?option=com_baforms&layout=uploader&id=&tmpl=component'); ?>"
        method="post" autocomplete="off" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <div id="create-folder-modal" class="ba-modal-sm modal hide" style="display:none">
            <div class="modal-body">
                <h3><?php echo JText::_('CREATE_FOLDER'); ?></h3>
                <input type="text" maxlength="260" name="new-folder" placeholder="<?php echo JText::_('ENTER_FOLDER_NAME') ?>">
                <span class="focus-underline"></span>
                <input type="hidden" name="current-dir" value="<?php echo $this->_parent; ?>">
            </div>
            <div class="modal-footer">
                <a href="#" class="ba-btn" data-dismiss="modal">
                    <?php echo JText::_('CANCEL') ?>
                </a>
                <a href="#" class="ba-btn-primary" id="add-folder">
                    <?php echo JText::_('JAPPLY') ?>
                </a>
            </div>
        </div>
        <div id="delete-modal" class="ba-modal-sm modal hide" style="display:none">
            <div class="modal-body">
                <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
                <p><?php echo JText::_('DELETE_QUESTION'); ?></p>
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
        <div id="move-to-modal" class="ba-modal-md modal hide" style="display:none">
            <div class="modal-body">
                <div class="ba-modal-header">
                    <h3><?php echo JText::_('MOVE_TO'); ?></h3>
                    <i data-dismiss="modal" class="zmdi zmdi-close"></i>
                </div>
                <div class="availible-folders">
                    <ul>
                        <li data-path="<?php echo JPATH_ROOT.'/'.IMAGE_PATH; ?>">
                            <span>
                                <i class="zmdi zmdi-folder"></i>
                                <?php echo IMAGE_PATH; ?>
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
                    <?php echo JText::_('JAPPLY') ?>
                </a>
            </div>
        </div>
        <div id="rename-modal" class="ba-modal-sm modal hide" style="display:none">
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
                    <?php echo JText::_('JAPPLY') ?>
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
                    if (!empty($this->_breadcrumb)) {
?>
                        <a class="folder-list" data-href="&tmpl=component">
                            <?php echo IMAGE_PATH; ?>
                        </a><i class="zmdi zmdi-chevron-right"></i>
<?php
                    } else {
                        echo '<a>'.IMAGE_PATH.'</a>';
                    }
                    foreach ($this->_breadcrumb as $value) {
                        if ($this->folder != $value->path) {
?>
                        <a class="folder-list" data-href="folder=<?php echo $value->path; ?>&tmpl=component">
                            <?php echo $value->title; ?>
                        </a><i class="zmdi zmdi-chevron-right"></i>
<?php
                        } else {
                            echo $value->title;
                        }
                    }
?>
                    </div>
                        <div class="control-toolbar">
                            <label>
                                <i class="zmdi zmdi-plus" id="ba-apply"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('INSERT_SELECTED_ITEMS'); ?></span>
                            </label>
                            <label>
                                <i class="zmdi zmdi-cloud-upload" id="show-upload"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('UPLOAD_FILE'); ?></span>
                            </label>
                            <label>
                                <i class="zmdi zmdi-folder" id="show-folder"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('CREATE_FOLDER'); ?></span>
                            </label>
                            <label>
                                <i class="zmdi zmdi-forward" id="move-to"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('MOVE_TO'); ?></span>
                            </label>
                            <label>
                                <i class="zmdi zmdi-delete" id="delete-items"></i>
                            </label>
                            <div class="pagination-limit">
                                <div class="ba-custom-select">
                                    <input readonly value="<?php echo $pagLimit[$this->_limit]; ?>"
                                       data-value="<?php echo $this->_limit; ?>" type="text">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
                                        <?php
                                        foreach ($pagLimit as $key => $lim) {
                                            $str = '<li data-value="'.$key.'">';
                                            if ($key == $this->_limit) {
                                                $str .= '<i class="zmdi zmdi-check"></i>';
                                            }
                                            $str .= $lim.'</li>';
                                            echo $str;
                                        }
                                        ?>
                                    </ul>
                                    <a data-href="<?php echo 'folder='.$this->folder.'&tmpl=component&page=0'; ?>" style="display: none;"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid ba-media-manager">
                <div class="ba-folder-tree">
                    <?php echo $this->_list; ?>
                </div>
                <div class="ba-work-area">
                    <div class="table-head">
                        <div class="files-name ba-hide-checkbox">
                            <input type="checkbox" name="ba-rm[]" value="" id="check-all">
                            <span class="check-all-wrapper">
                                <i class="zmdi zmdi-check check-all ba-icon-md"></i>
                            </span>
                            <?php echo JText::_('NAME'); ?>
                        </div>
                        <div class="files-size">
                            <?php echo JText::_('FILE_SIZE'); ?>
                        </div>
                    </div>
                    <div>
                        <table class="ba-items-list">
                            <tbody>
<?php
                                $img = JUri::root().'administrator/index.php?option=com_baforms&task=uploader.showImage&image=';
                                $now = strtotime('now');
                                foreach ($this->_items as $item) {
                                    if (!isset($item->size)) {
?>
                                <tr>
                                    <td class="select-td ba-hide-checkbox">
                                        <input class="select-item" type="checkbox" name="ba-rm[]" value="<?php echo $item->name; ?>">
                                        <div class="folder-icons">
                                            <a data-href="folder=<?php echo $item->path; ?>&tmpl=component" class="zmdi zmdi-folder"></a>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </div>
                                    </td>
                                    <td class="draggable-handler">
                                        <a class="folder-list" data-href="folder=<?php echo $item->path; ?>&tmpl=component">
                                            <?php echo $item->name; ?>
                                        </a>
                                    </td>
                                    <td class="draggable-handler">
                                    </td>
                                </tr>
<?php
                                    } else {
                                        $item->url = $item->url.'?'.$now;
                                        if ($item->ext == 'svg' || $item->ext == 'ico') {
                                            $url = $item->url;
                                        } else if (in_array($item->ext, $this->_imagesExt)) {
                                            $url = $img.$item->path.'&time='.$now;
                                        }
                                        if (in_array($item->ext, $this->_imagesExt)) {
                                            $imageFlag = true;
                                        } else {
                                            $imageFlag = false;
                                        }
?>
                                <tr class="ba-images" data-ext="<?php echo $item->ext; ?>">
                                    <td class="select-td<?php echo !$imageFlag ? ' ba-file-wrapper' : ''; ?>">
                                        <div class="ba-image ba-hide-checkbox">
<?php
                                        if ($imageFlag) {
?>
                                            <img data-src="<?php echo $url; ?>">
<?php
                                        } else {
                                            echo '<i class="zmdi zmdi-file ba-file-icon"></i>';
                                        }
?>
                                            <input class="select-item" type="checkbox" name="ba-rm[]" value="<?php echo $item->name; ?>">
                                            <input type="hidden" value="<?php echo htmlentities(json_encode($item)); ?>" class="ba-obj">
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </div>
                                    </td>
                                    <td class="draggable-handler">
                                        <?php echo $item->name; ?>
                                    </td>
                                    <td class="draggable-handler">
                                        <?php echo $this->getFileSize($item->size); ?>
                                    </td>
                                </tr>
                                <?php
                                    }
                                }
                                ?>                                
                            </tbody>
                        </table>
                    </div>
<?php
                    if ($this->_pages > 1) {
                        $prev = $this->_page - 1;
?>
                    <div class="pagination">
                        <ul class="pagination-list">
                            <li class="<?php echo ($this->_page == 0) ? 'disabled' : ''; ?>">
                                <a data-href="<?php echo $this->_page > 0 ? 'folder='.$this->folder.'&tmpl=component&page=0&ba_limit='.$this->_limit : ''; ?>">
                                    <span class="zmdi zmdi-skip-previous"></span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->_page == 0) ? 'disabled' : ''; ?>">
                                <a data-href="<?php echo $this->_page > 0 ? 'folder='.$this->folder.'&tmpl=component&page='.$prev.'&ba_limit='.$this->_limit : ''; ?>">
                                    <span class="zmdi zmdi-fast-rewind"></span>
                                </a>
                            </li>
<?php
                        $start = 0;
                        $max = $this->_pages;
                        if ($this->_page > 2 && $this->_pages > 4) {
                            $start = $this->_page - 2;
                        }
                        if ($this->_pages > 4 && ($this->_pages - $this->_page) < 3) {
                            $start = $this->_pages - 5;
                        }
                        if ($this->_pages > $this->_page + 2) {
                            $max = $this->_page + 3;
                            if ($this->_pages > 4 && $this->_page < 2) {
                                $max = 5;
                            }
                        }
                        for ($i = $start; $i < $max; $i++) { ?>
                            <li class="<?php echo ($this->_page == $i) ? 'active' : ''; ?>">
                                <?php 
                                $numb = $i + 1;
                                ?>
                                <a data-href="<?php echo 'folder='.$this->folder.'&tmpl=component&page='.$i.'&ba_limit='.$this->_limit; ?>"><?php echo $numb; ?></a>
                            </li>
<?php
                        }
                        $next = $this->_page + 1;
                        $end = $this->_pages - 1;
?>
                            <li class="<?php echo ($this->_page == $end) ? 'disabled' : ''; ?>">
                                <a data-href="<?php echo $this->_page < $end ? 'folder='.$this->folder.'&tmpl=component&page='.$next.'&ba_limit='.$this->_limit : ''; ?>">
                                    <span class="zmdi zmdi-fast-forward"></span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->_page == $end) ? 'disabled' : ''; ?>">
                                <a data-href="<?php echo $this->_page < $end ? 'folder='.$this->folder.'&tmpl=component&page='.$end.'&ba_limit='.$this->_limit : ''; ?>">
                                    <span class="zmdi zmdi-skip-next"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
<?php
                    }
?>
                </div>
            </div>
        </div>
        <div class="ba-context-menu empty-context-menu" style="display: none">
            <span class="upload-file ba-group-element"><i class="zmdi zmdi-cloud-upload"></i><?php echo JText::_('UPLOAD_FILE'); ?></span>
            <span class="create-folder"><i class="zmdi zmdi-folder"></i><?php echo JText::_('CREATE_FOLDER'); ?></span>
        </div>
        <div class="ba-context-menu files-context-menu" style="display: none">
            <span class="rename"><i class="zmdi zmdi-edit"></i><?php echo JText::_('RENAME'); ?></span>
            <span class="move-to"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
            <span class="download"><i class="zmdi zmdi-download"></i><?php echo JText::_('DOWNLOAD'); ?></span>
            <span class="delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
        </div>
        <div class="ba-context-menu folders-context-menu" style="display: none">
            <span class="rename"><i class="zmdi zmdi-edit"></i><?php echo JText::_('RENAME'); ?></span>
            <span class="move-to"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
            <span class="delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
        </div>
        <input type="hidden" name="task" value="grid.uploader" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
<iframe id="form-target" name="form-target" style="display: none;"></iframe>
<div id="file-upload-form" style="display: none;">
    <form target="file-upload-form-target" enctype="multipart/form-data" method="post"
        action="<?php echo JUri::base(); ?>index.php?option=com_baforms&task=uploader.formUpload">
        <input type="file" multiple name="files[]">
        <input type="hidden" name="current_folder" value="">
    </form>
    <iframe src="javascript:''" name="file-upload-form-target"></iframe>
</div>