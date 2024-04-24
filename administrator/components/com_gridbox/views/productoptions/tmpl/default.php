<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$state = $this->state->get('filter.status');
$user = JFactory::getUser();
$themeState = $this->state->get('filter.theme');
$accessState = $this->state->get('filter.access');
$languageState = $this->state->get('filter.language');
$limit = $this->pagination->limit;
$pagLimit = array(
    5 => 5,
    10 => 10,
    15 => 15,
    20 => 20,
    25 => 25,
    30 => 30,
    50 => 50,
    100 => 100,
    0 => JText::_('JALL'),
);
if (!isset($pagLimit[$limit])) {
    $limit = 0;
}
$currency = gridboxHelper::$store->currency;
?>
<script src="<?php echo JUri::root(); ?>/administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<?php
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=productoptions'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('PRODUCT_OPTIONS'); ?></h1>
                        </div>
                        <div class="filter-search-wrapper">
                            <div>
                                <input type="text" name="filter_search" id="filter_search"
                                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                                       placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                                <i class="zmdi zmdi-search"></i>
                            </div>
                        </div>
                        <div class="filter-icons-wrapper">
                            <div class="pagination-limit">
                                <div class="ba-custom-select">
                                    <input readonly value="<?php echo $pagLimit[$limit]; ?>" type="text">
                                    <input type="hidden" name="limit" id="limit" value="<?php echo $limit; ?>">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
<?php
                                        foreach ($pagLimit as $key => $lim) {
                                            $str = '<li data-value="'.$key.'">';
                                            if ($key == $limit) {
                                                $str .= '<i class="zmdi zmdi-check"></i>';
                                            }
                                            $str .= $lim.'</li>';
                                            echo $str;
                                        }
?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-table product-options-table twin-view-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="ba-hide-checkbox">
                                            <input type="checkbox" name="checkall-toggle" value=""
                                                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                                            <i class="zmdi zmdi-check-circle check-all"></i>
                                        </label>
                                    </th>
                                    <th class="<?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                                        <span data-sorting="title">
                                            <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                            foreach ($this->items as $i => $item) {
?>
                                <tr data-id="<?php echo $item->id; ?>">
                                    <td class="select-td ">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                    </td>
                                    <td class="title-cell">
                                        <?php echo $item->title; ?>
                                    </td>
                                </tr>
<?php
                            }
?>
                            </tbody>
                        </table>
                        <div class="twin-view-right-sidebar" data-edit="0">
                            <div class="twin-view-sidebar-header">
<?php
                            if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                                <span class="disabled apply-product-options">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
<?php
                            if ($user->authorise('core.delete', 'com_gridbox')) {
?>
                                <span class="disabled delete-product-options">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </span>
<?php
                            }
?>
                            </div>
                            <div class="twin-view-sidebar-body">
                                <div class="ba-options-group-wrapper">
                                    <div class="ba-options-group-header-wrapper">
                                        <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
                                    </div>
                                    <div class="ba-options-group-element">
                                        <label class="ba-options-group-label"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
                                        <input type="text" data-key="title">
                                    </div>
                                    <div class="ba-options-group-element">
                                        <label class="ba-options-group-label"><?php echo JText::_('TYPE'); ?></label>
                                        <select data-key="field_type">
                                            <option value="dropdown"><?php echo JText::_('DROPDOWN'); ?></option>
                                            <option value="radio"><?php echo JText::_('RADIO'); ?></option>
                                            <option value="checkbox"><?php echo JText::_('CHECKBOX'); ?></option>
                                            <option value="color"><?php echo JText::_('COLOR_PICKER'); ?></option>
                                            <option value="image"><?php echo JText::_('IMAGE_PICKER'); ?></option>
                                            <option value="tag"><?php echo JText::_('TAG'); ?></option>
                                            <option value="file"><?php echo JText::_('UPLOAD_FILES'); ?></option>
                                            <option value="textinput"><?php echo JText::_('TEXTINPUT'); ?></option>
                                            <option value="textarea"><?php echo JText::_('TEXTAREA'); ?></option>
                                        </select>
                                    </div>
                                    <div class="ba-options-group-element toggle-button-wrapper">
                                        <label class="ba-options-group-label"><?php echo JText::_('REQUIRED'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" data-key="required">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ba-options-group-wrapper product-options-values">
                                    <div class="ba-options-group-header-wrapper">
                                        <span class="ba-options-group-header"><?php echo JText::_('VALUES'); ?></span>
                                    </div>
                                    <div class="ba-options-group-element ba-options-group-sorting-wrapper">
                                        <div class="ba-options-group-toolbar">
                                            <div>
                                                <label data-action="add" data-object="productoptions">
                                                    <i class="zmdi zmdi-plus"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                    </span>
                                                </label>
                                                <label data-action="delete">
                                                    <i class="zmdi zmdi-delete"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('DELETE'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="sorting-container"></div>
                                    </div>
                                </div>
                                <div class="ba-options-group-wrapper product-options-file-type">
                                    <div class="ba-options-group-header-wrapper">
                                        <span class="ba-options-group-header"><?php echo JText::_('SETTINGS'); ?></span>
                                    </div>
                                    <div class="ba-options-group-element">
                                        <label class="ba-options-group-label"><?php echo JText::_('ALLOWED_FILE_TYPES'); ?></label>
                                        <input type="text" data-file="types">
                                    </div>
                                    <div class="ba-options-group-element">
                                        <label class="ba-options-group-label"><?php echo JText::_('MAX_UPLOAD_FILE_SIZE'); ?></label>
                                        <input type="text" data-file="size">
                                    </div>
                                    <div class="ba-options-group-element toggle-button-wrapper">
                                        <label class="ba-options-group-label"><?php echo JText::_('DRAG_DROP_FILE_UPLOADING'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" data-file="droppable">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="ba-options-group-element toggle-button-wrapper">
                                        <label class="ba-options-group-label"><?php echo JText::_('ALLOW_MULTIPLE_FILES'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" data-file="multiple">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="ba-options-group-element product-options-files-count">
                                        <label class="ba-options-group-label"><?php echo JText::_('MAX_NUMBER_FILES'); ?></label>
                                        <input type="text" data-file="count">
                                    </div>
                                    <div class="ba-options-group-element toggle-button-wrapper">
                                        <label class="ba-options-group-label"><?php echo JText::_('CHARGE_PRODUCT_QUANTITY_PER_FILE'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" data-file="quantity">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="ba-options-group-element toggle-button-wrapper">
                                        <label class="ba-options-group-label"><?php echo JText::_('CHARGE_PER_FILE'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" data-file="charge">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->pagination->getListFooter(); 
                    if ($user->authorise('core.create', 'com_gridbox')) {
?>
                    <div class="ba-create-item ba-add-product-options">
                        <a href="#">
                            <i class="zmdi zmdi-file"></i>
                            <span class="ba-tooltip ba-top ba-hide-element align-center">
                                <?php echo JText::_('ADD_NEW_ITEM'); ?>
                            </span>
                        </a>
                    </div>
<?php
                    }
?>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="theme_filter" value="<?php echo $themeState; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="access_filter" value="<?php echo $accessState; ?>">
                        <input type="hidden" name="ba_view" value="productoptions">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/categories-modal.php');
include(JPATH_COMPONENT.'/views/layouts/color-variables-dialog.php');
?>
<div id="product-applies-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker" style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="picker-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul></ul>
        </div>
    </div>
</div>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>