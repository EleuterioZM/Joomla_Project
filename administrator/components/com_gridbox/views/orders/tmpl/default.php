<?php
/**
* @package   gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$sortFields = $this->getSortFields();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$state = $this->state->get('filter.state');
$publish_up = $this->state->get('filter.publish_up');
$publish_down = $this->state->get('filter.publish_down');
$user = JFactory::getUser();
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
$countries = gridboxHelper::getTaxCountries();
$price = gridboxHelper::preparePrice(0);
$date = date('Y-m-d H:i:s');
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"></script>
<script type="text/javascript">
    app.statuses = <?php echo json_encode($this->statuses); ?>;
    app.store = <?php echo json_encode(gridboxHelper::$store); ?>;
    app.store.shipping = <?php echo json_encode($this->shipping); ?>;
    app.store.sales = <?php echo json_encode($this->sales); ?>;
    app.countries = <?php echo json_encode($countries); ?>;
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
<form autocomplete="off" action="index.php?option=com_gridbox&view=orders" method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('ORDERS') ?></h1>
                        </div>
                        <div class="filter-search-wrapper">
                            <div>
                                <input type="text" name="filter_search" id="filter_search"
                                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                                   placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                                <i class="zmdi zmdi-search"></i>
                            </div>
                            <div class="order-date-range">
                                <div class="">
                                    <i class="zmdi zmdi-calendar-alt"></i>
                                    <input type="text" class="open-calendar-dialog" data-name="0" data-link="1"
                                        placeholder="<?php echo JText::_('FROM'); ?>"
                                        data-action="filter" data-format="Y-m-d" data-type="range-dates" data-key="from"
                                        readonly name="publish_up" value="<?php echo $publish_up; ?>">
                                </div>
                                <div class="">
                                    <i class="zmdi zmdi-calendar-alt"></i>
                                    <input type="text" class="open-calendar-dialog" data-name="1" data-link="0"
                                        placeholder="<?php echo JText::_('TO'); ?>"
                                        data-action="filter" data-format="Y-m-d" data-type="range-dates" data-key="to"
                                        readonly name="publish_down" value="<?php echo $publish_down; ?>">
                                </div>
<?php
                            if (!empty($publish_up) && !empty($publish_down)) {
?>
                                <div class="reset-calendar-filtering">
                                    <i class="zmdi zmdi-replay"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET_FILTER'); ?></span>
                                </div>
<?php
                            }
?>
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
                            <div class="reset-filtering">
                                <i class="zmdi zmdi-replay"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET_FILTER'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="main-table orders-list">
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
                                    <th class="order-number-th <?php echo $listOrder == 'order_number' ? 'active' : ''; ?>">
                                        <span data-sorting="order_number">
                                            <?php echo JText::_('ORDER'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="status-th <?php echo $listOrder == 'status' ? 'active' : ''; ?>">
                                        <span data-sorting="status">
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
<?php
                                                foreach ($this->statuses as $key => $status) {
                                                    if ($key == 'undefined') {
                                                        continue;
                                                    }
?>
                                                    <li data-value="<?php echo $status->key ?>">
                                                        <?php echo $status->title;?>
                                                    </li>
<?php
                                                }
?>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="date-th <?php echo $listOrder == 'date' ? 'active' : ''; ?>">
                                        <span data-sorting="date">
                                            <?php echo JText::_('DATE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="customer-th">
                                        <?php echo JText::_('CUSTOMER'); ?>
                                    </th>
                                    <th class="email-th">
                                        <?php echo JText::_('EMAIL'); ?>
                                    </th>
                                    <th class="total-th <?php echo $listOrder == 'total' ? 'active' : ''; ?>">
                                        <span data-sorting="total">
                                            <?php echo JText::_('TOTAL'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                           foreach ($this->items as $i => $item) { 
                                $date = JDate::getInstance($item->date)->format('Y-m-d H:i');
                                if (isset($this->statuses->{$item->status})) {
                                    $status = $this->statuses->{$item->status};
                                } else {
                                    $status = $this->statuses->undefined;
                                }
                                $className = $item->unread == 1 ? 'unread-order': '';
                                $className .= $item->hasBooking ? ' order-with-booking' : '';
?>
                                <tr data-id="<?php echo $item->id; ?>" class="<?php echo $className; ?>">
                                    <td class="select-td">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                        <input type="hidden" value='<?php echo htmlspecialchars($str, ENT_QUOTES); ?>'>
                                    </td>
                                    <td class="order-number-td">
                                        <?php echo $item->order_number; ?>
                                    </td>
                                    <td class="status-td">
                                        <span class="order-status-cell" style="--order-status-color: <?php echo $status->color; ?>;">
                                            <span class="order-status-color"></span>
                                            <span class="order-status-title"><?php echo $status->title; ?></span>
                                        </span>
                                    </td>
                                    <td class="date-td">
                                        <?php echo $date; ?>
                                    </td>
                                    <td class="customer-td">
                                        <?php echo $item->customer_name; ?>
                                    </td>
                                    <td class="email-td">
                                        <?php echo $item->email; ?>
                                    </td>
                                    <td class="total-td">
<?php
                                        echo gridboxHelper::preparePrice($item->total, $item->currency_symbol, $item->currency_position);
?>
                                    </td>
                                </tr>
<?php
                            }
?>
                            </tbody>
                        </table>
                    </div>
<?php
                    echo $this->pagination->getListFooter();
                    if (JFactory::getUser()->authorise('core.create', 'com_gridbox')) {
?>
                    <div class="ba-create-item ba-add-order">
                        <a href="#">
                            <i class="zmdi zmdi-file"></i>
                        </a>
                        <span class="ba-tooltip ba-top ba-hide-element align-center">
                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                        </span>
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
                        <input type="hidden" name="ba_view" value="orders">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="orders-status-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="order-status-tabs-wrapper">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#change-order-status" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                    </a>
                </li>
                <li>
                    <a href="#order-status-history" data-toggle="tab">
                        <i class="zmdi zmdi-calendar-note"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="change-order-status">
                    <div class="order-status-options-group">
                        <div class="order-status-group-element">
                            <div class="ba-custom-select orders-status-select">
                                <input readonly onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul class="">
<?php
                                foreach ($this->statuses as $key => $status) {
                                    if ($key == 'undefined') {
                                        continue;
                                    }
?>
                                    <li data-value="<?php echo $key; ?>" data-color="<?php echo $status->color; ?>"
                                        style="--status-color: <?php echo $status->color; ?>;">
                                        <?php echo $status->title; ?>
                                    </li>
<?php
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="order-status-group-element">
                            <textarea placeholder="<?php echo JText::_('WRITE_COMMENT_HERE'); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="order-status-history"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
<?php
    if ($user->authorise('core.edit', 'com_gridbox')) {
?>
        <a href="#" class="ba-btn-primary apply-order-status">
            <?php echo JText::_('JTOOLBAR_APPLY') ?>
        </a>
<?php
    }
?>
    </div>
</div>
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
<div id="create-new-order-dialog" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('NEW_ORDER'); ?></h3>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="orders-details-header">
            <span class="orders-details-number"></span>
            <div class="orders-details-icons">
                <span class="edit-exist-order">
                    <i class="zmdi zmdi-edit"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
                </span>
                <span class="add-tracking-number">
                    <i class="zmdi zmdi-pin-drop"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('ADD_TRACKING_INFO'); ?></span>
                </span>
                <span class="download-exist-order" data-layout="pdf">
                    <i class="zmdi zmdi-assignment-returned"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DOWNLOAD'); ?></span>
                </span>
                <span class="download-exist-order" data-layout="print">
                    <i class="zmdi zmdi-print"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('PRINT'); ?></span>
                </span>
            </div>
            <div class="order-edit-icons-wrapper">
<?php
            if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                <span>
                    <i class="zmdi zmdi-check save-order-cart"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JTOOLBAR_APPLY'); ?></span>
                </span>
<?php
            }
?>
                <span>
                    <i class="zmdi zmdi-arrow-right back-order-cart"></i>
                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('BACK'); ?></span>
                </span>
            </div>
        </div>
        <div class="row-fluid">
            <div class="customer-info-wrapper">
                <div class="ba-options-group-header-wrapper">
                    <span class="ba-options-group-header"><?php echo JText::_('CUSTOMER_INFO'); ?></span>
                </div>
                <div class="ba-options-group-wrapper"></div>
            </div>
            <div class="order-info-wrapper">
                <div class="ba-options-group-header-wrapper">
                    <span class="ba-options-group-header"><?php echo JText::_('ORDER'); ?></span>
                    <span class="order-buttons-wrapper">
                        <span class="edit-order-status">
                            <span class="order-status-color">
                                <i class="zmdi zmdi-edit"></i>
                            </span>
                            <span class="order-status-title"></span>
                            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('ORDER_STATUS'); ?></span>
                        </span>
                    </span>
                </div>
                <div class="ba-options-group-wrapper">
                    <div class="ba-options-group-element ba-options-group-sorting-wrapper">
                        <div class="ba-options-group-toolbar">
                            <div>
                                <label class="add-order-product" data-modal="product-applies-dialog">
                                    <i class="zmdi zmdi-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_ITEM'); ?></span>
                                </label>
                                <label class="ba-add-product-extra-option disabled" data-action="add-extra">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('EXTRA_OPTIONS'); ?></span>
                                </label>
                                <label class="delete-order-product disabled" data-action="delete">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="sorting-container"></div>
                    </div>
                </div>
                <div class="ba-options-group-wrapper order-methods-wrapper <?php echo $className; ?>">
                    <div class="ba-options-group-element order-payment-method">
                        <label class="ba-options-group-label">
                            <?php echo JText::_('PAYMENT'); ?>
                        </label>
                        <div class="customer-info-data"></div>
                    </div>
<?php
                    $className = empty($this->shipping) ? ' empty-shipping-methods' : '';
?>
                    <div class="ba-options-group-element order-shipping-method<?php echo $className; ?>">
                        <label class="ba-options-group-label">
                            <?php echo JText::_('SHIPPING'); ?><span class="ba-options-group-required-star">*</span>
                        </label>
                        <select class="select-order-shipping" required>
                            <option value=""><?php echo JText::_('SELECT'); ?></option>
<?php
                        foreach ($this->shipping as $key => $shipping) {
?>
                            <option value="<?php echo $key; ?>"><?php echo $shipping->title; ?></option>
<?php
                        }
?>
                        </select>
                        <div class="customer-info-data"></div>
                    </div>
                    <div class="ba-options-group-element order-shipping-carrier<?php echo $className; ?>">
                        <label class="ba-options-group-label">
                            <?php echo JText::_('ENTER_PARCEL_LOCKER_ADDRESS'); ?>
                        </label>
                        <input type="text" class="enter-carrier-address">
                    </div>
<?php
                    $className = empty($this->promo) ? ' empty-promo-methods' : '';
?>
                    <div class="ba-options-group-element order-promo-code<?php echo $className; ?>">
                        <label class="ba-options-group-label"><?php echo JText::_('COUPON_CODE'); ?></label>
                        <div class="ba-options-input-action-wrapper">
                            <input type="text" readonly onfocus="this.blur()">
                            <div class="order-coupon-code input-action-icon">
                                <i class="zmdi zmdi-playlist-plus"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
                            </div>
                            <div class="reset-coupon-code input-action-icon">
                                <i class="zmdi zmdi-close"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ba-options-group-wrapper order-footer-total-wrapper">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tracking-number-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('ADD_TRACKING_INFO'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="ba-input-lg">
            <input type="text" class="tracking-title-input reset-input-margin"
                placeholder="<?php echo JText::_('CARRIER'); ?>">
            <span class="focus-underline"></span>
        </div>
        <div class="ba-input-lg">
            <input type="text" class="tracking-number-input reset-input-margin"
                placeholder="<?php echo JText::_('TRACKING_NUMBER'); ?>">
            <span class="focus-underline"></span>
        </div>
        <div class="ba-input-lg">
            <input type="text" class="tracking-url-input"
                placeholder="<?php echo JText::_('TRACKING_URL'); ?>">
            <span class="focus-underline"></span>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL'); ?>
        </a>
        <a href="#" class="ba-btn-primary apply-tracking-number active-button" data-dismiss="modal">
            <?php echo JText::_('APPLY'); ?>
        </a>
    </div>
</div>
<div id="product-applies-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker"
    style="display: none;">
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
<div id="extra-options-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker"
    style="display: none;">
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
<div id="order-coupon-code-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker"
    style="display: none;">
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
<template class="template-order-footer-total-wrapper">
    <div class="ba-options-group-element order-subtotal-element">
        <label class="ba-options-group-label"><?php echo JText::_('SUBTOTAL'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
$className = !empty($this->promo) ? '' : ' ba-hide-element';
?>
    <div class="ba-options-group-element order-discount-element<?php echo $className ?>">
        <label class="ba-options-group-label"><?php echo JText::_('DISCOUNT'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-minus">-</span>
            <span class="ba-cart-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
$mode = gridboxHelper::$store->tax->mode;
$className = !empty($this->shipping) ? '' : ' ba-hide-element';
$shippingTax = null;
if (!empty($this->shipping)) {
    $title = $mode == 'excl' ? JText::_('TAX_ON_SHIPPING') : JText::_('INCLUDES');
    foreach (gridboxHelper::$store->tax->rates as $rate) {
        if ($rate->shipping && empty($rate->country_id)) {
            $shippingTax = $rate;
            $title .= $mode == 'excl' ? '' : ' '.$rate->title;
            break;
        }
    }
    if (!$shippingTax) {
        foreach (gridboxHelper::$store->tax->rates as $rate) {
            if ($rate->shipping) {
                $shippingTax = $rate;
                $title = $mode == 'excl' ? $title : JText::_('INCLUDING_TAXES');
                break;
            }
        }
    }
}
?>
    <div class="ba-options-group-element order-shipping-element<?php echo $className; ?>" data-mode="<?php echo $mode; ?>">
        <div class="ba-options-group-element-content">
            <label class="ba-options-group-label"><?php echo JText::_('SHIPPING'); ?></label>
            <span class="ba-cart-price-wrapper ">
                <span class="ba-cart-price-value"><?php echo $price; ?></span>
            </span>
        </div>
<?php
if ($shippingTax && $mode == 'incl') {
?>
    <div class="ba-options-group-element order-shipping-tax-element" data-mode="incl">
        <label class="ba-options-group-label"><?php echo $title.' '.$price; ?></label>
    </div>
<?php
}
?>
    </div>
<?php
if ($shippingTax && $mode == 'excl') {
?>
    <div class="ba-options-group-element order-shipping-tax-element" data-mode="excl">
        <label class="ba-options-group-label"><?php echo $title; ?></label>
        <span class="ba-cart-price-wrapper">
            <span class="ba-cart-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
if (!empty(gridboxHelper::$store->tax->rates) && $mode == 'excl') {
?>
    <div class="ba-options-group-element order-tax-element" data-mode="<?php echo $mode; ?>">
        <label class="ba-options-group-label"><?php echo JText::_('TAX'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-value"><?php echo $price; ?></span>
        </span>
    </div>
<?php
}
?>
    <div class="ba-options-group-element order-total-element" data-mode="<?php echo $mode; ?>">
        <div class="ba-options-group-element-content">
            <label class="ba-options-group-label"><?php echo JText::_('TOTAL'); ?></label>
            <span class="ba-cart-price-wrapper">
                <span class="ba-cart-price-value"><?php echo $price; ?></span>
            </span>
        </div>
<?php
    if (!empty(gridboxHelper::$store->tax->rates) && $mode == 'incl') {
?>
        <div class="ba-options-group-element order-tax-element" data-mode="incl">
            <label class="ba-options-group-label"><?php echo JText::_('INCLUDING_TAXES').' '.$price; ?></label>
        </div>
<?php
    }
?>
    </div>
</template>
<template class="view-order-footer-total-wrapper">
    <div class="ba-options-group-element order-subtotal-element">
        <label class="ba-options-group-label"><?php echo JText::_('SUBTOTAL'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-value"></span>
        </span>
    </div>
    <div class="ba-options-group-element order-discount-element">
        <label class="ba-options-group-label"><?php echo JText::_('DISCOUNT'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-minus">-</span>
            <span class="ba-cart-price-value"></span>
        </span>
    </div>
    <div class="ba-options-group-element order-shipping-element" data-mode="">
        <div class="ba-options-group-element-content">
            <label class="ba-options-group-label"><?php echo JText::_('SHIPPING'); ?></label>
            <span class="ba-cart-price-wrapper ">
                <span class="ba-cart-price-value"></span>
            </span>
        </div>
        <div class="ba-options-group-element order-shipping-tax-element" data-mode="incl">
            <label class="ba-options-group-label"></label>
        </div>
    </div>
    <div class="ba-options-group-element order-shipping-tax-element" data-mode="excl">
        <label class="ba-options-group-label"><?php echo JText::_('TAX_ON_SHIPPING'); ?></label>
        <span class="ba-cart-price-wrapper">
            <span class="ba-cart-price-value"></span>
        </span>
    </div>
    <div class="ba-options-group-element order-tax-element" data-mode="excl">
        <label class="ba-options-group-label"><?php echo JText::_('TAX'); ?></label>
        <span class="ba-cart-price-wrapper ">
            <span class="ba-cart-price-value"></span>
        </span>
    </div>
    <div class="ba-options-group-element order-total-element" data-mode="">
        <div class="ba-options-group-element-content">
            <label class="ba-options-group-label"><?php echo JText::_('TOTAL'); ?></label>
            <span class="ba-cart-price-wrapper">
                <span class="ba-cart-price-value"></span>
            </span>
        </div>
        <div class="ba-options-group-element order-tax-element" data-mode="incl">
            <label class="ba-options-group-label"></label>
        </div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="text">
    <div class="ba-options-group-element" data-type="text">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <input type="text">
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="country">
    <div class="ba-options-group-element" data-type="country">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <div class="customer-info-data" data-type="country"></div>
        <div class="customer-info-data" data-type="region"></div>
        <input type="hidden">
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="email">
    <div class="ba-options-group-element" data-type="email">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <input type="email">
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="textarea">
    <div class="ba-options-group-element" data-type="textarea">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <textarea></textarea>
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="dropdown">
    <div class="ba-options-group-element" data-type="dropdown">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <select></select>
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="checkbox">
    <div class="ba-options-group-element" data-type="checkbox">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <div class="ba-checkbox-wrapper">
            <label class="ba-checkbox">
                <input type="checkbox">
                <span></span>
            </label>
            <span></span>
        </div>
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="radio">
    <div class="ba-options-group-element" data-type="radio">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <div class="ba-checkbox-wrapper">
            <label class="ba-radio">
                <input type="radio">
                <span></span>
            </label>
            <span></span>
        </div>
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="acceptance">
    <div class="ba-options-group-element" data-type="acceptance">
        <label class="ba-options-group-label"><span class="customer-info-title"></span></label>
        <div class="ba-checkbox-wrapper acceptance-checkbox-wrapper">
            <label class="ba-checkbox">
                <input type="checkbox">
                <span></span>
            </label>
        </div>
        <div class="ba-checkout-acceptance-html"></div>
        <div class="customer-info-data"></div>
    </div>
</template>
<template class="customer-info-fields-pattern" data-type="user">
    <div class="ba-options-group-element" data-type="user">
        <label class="ba-options-group-label"><?php echo JText::_('USER'); ?></label>
        <div class="ba-options-input-action-wrapper">
            <input type="text" name="user_id" readonly onfocus="this.blur()">
            <div class="set-order-user input-action-icon">
                <i class="zmdi zmdi-playlist-plus"></i>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
            </div>
            <div class="reset-order-user input-action-icon">
                <i class="zmdi zmdi-close"></i>
            </div>
            <div class="customer-info-data"></div>
        </div>
    </div>
</template>
<template class="exist-customer-info-fields">
    <div class="ba-options-group-element" data-type="user">
        <label class="ba-options-group-label"><?php echo JText::_('USER'); ?></label>
        <div class="ba-options-input-action-wrapper">
            <input type="text" name="user_id" readonly onfocus="this.blur()">
            <div class="set-order-user input-action-icon">
                <i class="zmdi zmdi-playlist-plus"></i>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
            </div>
            <div class="reset-order-user input-action-icon">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>
    </div>
<?php
    foreach ($this->info as $info) {
        if ($info->type == 'headline' || $info->type == 'acceptance') {
            continue;
        }
        $title = '<span class="customer-info-title">'.$info->title.'</span>';
        if ($info->required == 1 && !empty($info->title)) {
            $title .= '<span class="ba-options-group-required-star">*</span>';
        }
?>
        <div class="ba-options-group-element" data-type="<?php echo $info->type; ?>">
            <label class="ba-options-group-label"><?php echo $title; ?></label>
<?php
        $attr = $info->required == 1 ? ' required' : '';
        if ($info->type == 'textarea' || $info->type == 'text' || $info->type == 'email') {
            $attr .= isset($info->settings->placeholder) ? ' placeholder="'.$info->settings->placeholder.'"' : '';
        }
        if ($info->type == 'textarea') {
?>
            <textarea name="<?php echo $info->id; ?>"<?php echo $attr; ?>></textarea>
<?php
        } else if ($info->type == 'text' || $info->type == 'email') {
?>
            <input type="<?php echo $info->type; ?>" name="<?php echo $info->id; ?>"<?php echo $attr; ?>>
<?php
        } else if ($info->type == 'dropdown') {
?>
            <select name="<?php echo $info->id; ?>"<?php echo $attr; ?>>
                <option value=""><?php echo $info->settings->placeholder; ?></option>
<?php
            foreach ($info->settings->options as $option) {
?>
                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
<?php
            }
?>
            </select>
<?php
        } else if ($info->type == 'checkbox' || $info->type == 'radio') {
            foreach ($info->settings->options as $option) {
                $value = strip_tags($option);
                $value = htmlspecialchars($value, ENT_QUOTES);
?>
                <div class="ba-checkbox-wrapper">
                    <label class="ba-<?php echo $info->type; ?>">
                        <input type="<?php echo $info->type; ?>" name="<?php echo $info->id; ?>"<?php echo $attr; ?>
                            value="<?php echo $value; ?>">
                        <span></span>
                    </label>
                    <span><?php echo $option; ?></span>
                </div>
<?php
            }
        } else if ($info->type == 'acceptance') {
?>
            <div class="ba-checkbox-wrapper acceptance-checkbox-wrapper">
                <label class="ba-checkbox">
                    <input type="checkbox" name="<?php echo $info->id; ?>"<?php echo $attr; ?>
                        value="<?php echo strip_tags($info->settings->html); ?>">
                    <span></span>
                </label>
            </div>
            <div class="ba-checkout-acceptance-html"><?php echo $info->settings->html; ?></div>
<?php
        } else if ($info->type == 'country') {
?>
            <select data-type="country"<?php echo $attr; ?>>
                <option value=""><?php echo $info->settings->placeholder; ?></option>
<?php
            foreach ($countries as $country) {
?>
                <option value="<?php echo $country->id; ?>"><?php echo $country->title; ?></option>
<?php
            }
?>
            </select>
            <input type="hidden" name="<?php echo $info->id; ?>">
<?php
        }
?>
            <div class="customer-info-data"></div>
        </div>
<?php
    }
?>
</template>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="context-view-order"><i class="zmdi zmdi-settings"></i><?php echo JText::_('VIEW'); ?></span>
    <span class="context-download-order"><i class="zmdi zmdi-copy"></i><?php echo JText::_('DOWNLOAD'); ?></span>
    <span class="context-delete-order ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<?php
$view = 'orders';
include JPATH_COMPONENT.'/views/layouts/users-dialog.php';
include(JPATH_COMPONENT.'/views/layouts/context.php');
?>
<div id="import-export-csv-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="import-export-csv-tabs-wrapper">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#import-csv-tab" data-toggle="tab">
                        <i class="zmdi zmdi-assignment"></i>
                    </a>
                </li>
                <li>
                    <a href="#export-csv-tab" data-toggle="tab">
                        <i class="zmdi zmdi-inbox"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="import-csv-tab">
                    <div class="tab-body">
                        <div class="csv-import-step-1">
                            <h3><?php echo JText::_('IMPORT'); ?></h3>
                            <div class="csv-import-options csv-content-wrapper" data-key="csv-import-options"></div>
                        </div>
                        <div class="csv-import-step-2" style="display: none;">
                            <h3><?php echo JText::_('MATCH_FIELDS'); ?></h3>
                            <div class="csv-match-fields csv-content-wrapper" data-key="csv-match-fields"></div>
                        </div>
                        <div class="csv-import-step-3" style="display: none;">
                            <h3><?php echo JText::_('PRELIMINARY_IMPORT_CHECK'); ?></h3>
                            <div class="csv-content-wrapper" data-key="csv-import-check"></div>
                        </div>
                    </div>
                    <div class="tab-footer">
                        <a href="#" class="ba-btn disabled-button csv-import-back">
                            <?php echo JText::_('BACK') ?>
                        </a>
                        <a href="#" class="ba-btn" data-dismiss="modal">
                            <?php echo JText::_('CANCEL') ?>
                        </a>
                        <a href="#" class="ba-btn-primary apply-csv-import">
                            <?php echo JText::_('NEXT') ?>
                        </a>
                    </div>
                </div>
                <div class="tab-pane" id="export-csv-tab">
                    <div class="tab-body">
                        <h3><?php echo JText::_('EXPORT'); ?></h3>
                        <div class="csv-export-fields csv-content-wrapper" data-key="csv-export-fields"></div>
                    </div>
                    <div class="tab-footer">
                        <a href="#" class="ba-btn" data-dismiss="modal">
                            <?php echo JText::_('CANCEL') ?>
                        </a>
                        <a href="#" class="ba-btn-primary active-button apply-export-csv">
                            <?php echo JText::_('EXPORT') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="csv-import-error-log-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="ba-modal-header">
            <h3><?php echo JText::_('ERRORS'); ?></h3>
            <i data-dismiss="modal" class="zmdi zmdi-close"></i>
        </div>
        <div class="csv-error-log-table">
            <div class="csv-error-log-thead">
                <div class="csv-error-log-row">
                    <div class="csv-error-log-cell"><?php echo JText::_('COLUMN'); ?></div>
                    <div class="csv-error-log-cell"><?php echo JText::_('LINE'); ?></div>
                    <div class="csv-error-log-cell"></div>
                </div>
            </div>
            <div class="csv-error-log-tbody csv-content-wrapper" data-key="csv-error-log-row"></div>
        </div>
    </div>
</div>
<template data-key="csv-error-log-row" class="csv-template">
    <div class="csv-error-log-row">
        <div class="csv-error-log-cell" data-key="column"></div>
        <div class="csv-error-log-cell" data-key="line"></div>
        <div class="csv-error-log-cell" data-key="code"><span></span></div>
    </div>
</template>
<template data-key="csv-import-check" class="csv-template">
    <span class="csv-import-check-field" data-type="new">
        <label class="ba-options-group-label"><?php echo JText::_('NEW_ORDERS'); ?></label>
        <span class="csv-import-status-color"></span>
    </span>
    <span class="csv-import-check-field" data-type="updated">
        <label class="ba-options-group-label"><?php echo JText::_('UPDATED_PRODUCTS'); ?></label>
        <span class="csv-import-status-color"></span>
    </span>
    <span class="csv-import-check-field" data-type="errors">
        <label class="ba-options-group-label"><?php echo JText::_('ERRORS'); ?></label>
        <span class="csv-import-status-text"><?php echo JText::_('VIEW_ERRORS'); ?></span>
        <span class="csv-import-status-color"></span>
    </span>
</template>
<template data-key="csv-export-field" class="csv-template">
    <span class="csv-export-field toggle-button-wrapper">
        <label class="csv-export-field-title ba-options-group-label"></label>
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" checked>
            <span></span>
        </label>
    </span>
</template>
<template data-key="csv-import-options" class="csv-template">
    <div class="ba-options-group-element">
        <select class="csv-file-type">
            <option value="match"><?php echo JText::_('MATCH_FIELDS'); ?></option>
            <option value="gridbox"><?php echo JText::_('GRIDBOX_CSV_FILE'); ?></option>
        </select>
    </div>
    <div class="ba-options-group-element">
        <input class="trigger-csv-import" readonly type="text"
            placeholder="<?php echo JText::_('SELECT_CSV_FILE'); ?>">
        <i class="zmdi zmdi-attachment-alt"></i>
        <input type="file" accept=".csv" style="display: none;">
    </div>
    <div class="ba-checkbox-parent">
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" class="import-property" data-key="backup">
            <span></span>
        </label>
        <span><?php echo JText::_('BACKUP_ORDERS_BEFORE_IMPORT'); ?></span>
    </div>
    <div class="ba-checkbox-parent">
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" class="import-property" data-key="overwrite">
            <span></span>
        </label>
        <span><?php echo JText::_('OVERWRITE_ORDERS_WITH_SAME_ID'); ?></span>
    </div>
</template>
<template data-key="csv-match-field" class="csv-template">
    <div class="ba-options-group-element">
        <span class="ba-options-group-element-title"></span>
        <select class="csv-file-type"></select>
    </div>
</template>