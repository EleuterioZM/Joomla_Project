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
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=promocodes'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('PROMO_CODES'); ?></h1>
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
                    <div class="main-table promo-codes-table twin-view-table">
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
                                    <th class="status-th <?php echo $listOrder == 'published' ? 'active' : ''; ?>">
                                        <span data-sorting="published">
                                            <?php echo JText::_('JSTATUS'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                        <div class="state-filter">
                                            <div class="ba-custom-select">
                                                <input type="hidden" data-name="filter_state" value="<?php echo $state; ?>">
                                                <i class="zmdi zmdi-caret-down"></i>
                                                <ul>
                                                    <li data-value="">
                                                        <?php echo JText::_('JSTATUS');?>
                                                    </li>
                                                    <li data-value="1" >
                                                        <?php echo JText::_('JPUBLISHED');?>
                                                    </li>
                                                    <li data-value="0">
                                                        <?php echo JText::_('JUNPUBLISHED');?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="<?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                                        <span data-sorting="title">
                                            <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>

                                    <th class="<?php echo $listOrder == 'code' ? 'active' : ''; ?>">
                                        <span data-sorting="code">
                                            <?php echo JText::_('CODE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'discount' ? 'active' : ''; ?>">
                                        <span data-sorting="discount">
                                            <?php echo JText::_('DISCOUNT'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'used' ? 'active' : ''; ?>">
                                        <span data-sorting="used">
                                            <?php echo JText::_('USED'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                            foreach ($this->items as $i => $item) {
                                $canChange = $user->authorise('core.edit.state', 'com_gridbox');
?>
                                <tr data-id="<?php echo $item->id; ?>">
                                    <td class="select-td ">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                    </td>
                                    <td class="status-td">
                                        <?php echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'promocodes.', $canChange); ?>
                                    </td>
                                    <td class="title-cell">
                                        <?php echo $item->title; ?>
                                    </td>
                                    <td class="code-cell">
                                        <?php echo $item->code; ?>
                                    </td>
                                    <td class="discount-cell">
<?php
                                        $price = $item->discount;
                                        if (!empty($price) && $item->unit == '%') {
                                            $price = $item->discount.' %';
                                        } else if (!empty($price) && $item->unit != '%') {
                                            $price = gridboxHelper::preparePrice($price);
                                        }
                                        echo $price;
?>
                                    </td>
                                    <td class="used-cell">
<?php
                                        echo $item->used.(!empty($item->limit) ? ' / '.$item->limit : '');
?>
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
                                <span class="disabled apply-promo-code">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
<?php
                            if ($user->authorise('core.duplicate', 'com_gridbox')) {
?>
                                <span class="disabled duplicate-promo-code">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DUPLICATE'); ?></span>
                                </span>
<?php
                            }
?>
<?php
                            if ($user->authorise('core.delete', 'com_gridbox')) {
?>
                                <span class="disabled delete-promo-code">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </span>
<?php
                            }
?>
                            </div>
                            <div class="twin-view-sidebar-body">
                                <div class="promo-code-options">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
                                            <input type="text" data-key="title">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('COUPON_CODE'); ?></label>
                                            <input type="text" data-key="code">
                                            <div class="copy-to-clipboard">
                                                <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                            </div>
                                        </div>
                                        <div class="ba-options-group-element coupon-type-select">
                                            <label class="ba-options-group-label"><?php echo JText::_('TYPE'); ?></label>
                                            <select data-key="unit" data-decimals="<?php echo $currency->decimals; ?>"
                                                data-symbol="<?php echo $currency->symbol; ?>">
                                                <option value="$"><?php echo JText::_('AMOUNT'); ?></option>
                                                <option value="%"><?php echo JText::_('PERCENTAGE'); ?></option>
                                            </select>
                                            <div class="ba-options-price-wrapper">
                                                <span class="ba-options-price-currency">$</span>
                                                <input type="text" class="integer-validation" data-decimals="2" data-key="discount">
                                            </div>
                                        </div>
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('COUPON_RESTRICTIONS'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element ba-options-group-applies-wrapper">
                                            <label class="ba-options-group-label"><?php echo JText::_('APPLIES_TO'); ?></label>
                                            <select data-key="applies_to">
                                                <option value="*"><?php echo JText::_('ALL_PRODUCTS'); ?></option>
                                                <option value="category"><?php echo JText::_('CATEGORY'); ?></option>
                                                <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                            </select>
                                            <div class="ba-options-applies-wrapper">
                                                <span>
                                                    <i class="zmdi zmdi-playlist-plus trigger-picker-modal"
                                                        data-modal="category-applies-dialog"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
                                                </span>
                                            </div>
                                            <div class="selected-applies-wrapper selected-items-wrapper"></div>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('JFIELD_ACCESS_LABEL'); ?></label>
                                            <select data-key="access">
<?php
                                            foreach ($this->access as $access) {
?>
                                                <option value="<?php echo $access->id; ?>"><?php echo $access->title; ?></option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('USAGE_LIMIT'); ?></label>
                                            <input type="text" class="integer-validation" data-decimals="0" data-key="limit">
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper">
                                            <label class="ba-options-group-label"><?php echo JText::_('DISABLE_FOR_DISCOUNT'); ?></label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="disable_sales">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('DATE_LIMITATIONS'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('START_DATE'); ?></label>
                                            <div class="date-field-wrapper">
                                                <input type="text" class="open-calendar-dialog" data-key="publish_up">
                                                <div class="icons-cell">
                                                    <i class="zmdi zmdi-calendar-alt"></i>
                                                </div>
                                                <div class="reset reset-date-field">
                                                    <i class="zmdi zmdi-close"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('END_DATE'); ?></label>
                                            <div class="date-field-wrapper">
                                                <input type="text" class="open-calendar-dialog" data-key="publish_down">
                                                <div class="icons-cell">
                                                    <i class="zmdi zmdi-calendar-alt"></i>
                                                </div>
                                                <div class="reset reset-date-field">
                                                    <i class="zmdi zmdi-close"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->pagination->getListFooter(); 
                    if ($user->authorise('core.create', 'com_gridbox')) {
?>
                    <div class="ba-create-item ba-add-promocodes-method">
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
                        <input type="hidden" name="ba_view" value="promocodes">
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