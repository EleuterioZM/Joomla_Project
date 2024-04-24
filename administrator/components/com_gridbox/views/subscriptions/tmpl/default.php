<?php
/**
* @package   gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

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
$mode = gridboxHelper::$store->tax->mode;
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"></script>
<script type="text/javascript">
    app.store = <?php echo json_encode(gridboxHelper::$store); ?>;
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
<form autocomplete="off" action="index.php?option=com_gridbox&view=subscriptions" method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('SUBSCRIPTIONS'); ?></h1>
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
                    <div class="main-table subscriptions-list">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="ba-hide-checkbox">
                                            <input type="checkbox" name="checkall-toggle" value=""
                                                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                                                   onclick="Joomla.checkAll(this)" />
                                            <i class="zmdi zmdi-check-circle check-all"></i>
                                        </label>
                                    </th>
                                    <th class="status-th <?php echo $listOrder == 'status' ? 'active' : ''; ?>">
                                        <span>
                                            <?php echo JText::_('JSTATUS'); ?>
                                        </span>
                                        <div class="state-filter">
                                            <div class="ba-custom-select">
                                                <input type="hidden" data-name="filter_state" value="<?php echo $state; ?>">
                                                <i class="zmdi zmdi-caret-down"></i>
                                                <ul>
                                                    <li data-value="">
                                                        <?php echo JText::_('JSTATUS');?>
                                                    </li>
                                                    <li data-value="active">
                                                        <?php echo JText::_('ACTIVE');?>
                                                    </li>
                                                    <li data-value="expired">
                                                        <?php echo JText::_('EXPIRED');?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="title-th <?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                                        <span data-sorting="title">
                                            <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="date-th <?php echo $listOrder == 'date' ? 'active' : ''; ?>">
                                        <span data-sorting="date">
                                            <?php echo JText::_('DATE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="expires-th <?php echo $listOrder == 'expires' ? 'active' : ''; ?>">
                                        <span data-sorting="expires">
                                            <?php echo JText::_('EXPIRES'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="customer-th">
                                        <?php echo JText::_('USERNAME'); ?>
                                    </th>
                                    <th class="email-th">
                                        <?php echo JText::_('EMAIL'); ?>
                                    </th>
                                    <th class="id-th <?php echo $listOrder == 'id' ? 'active' : ''; ?>">
                                        <span data-sorting="id">
                                            <?php echo JText::_('ID'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                            </span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                            $now = date('Y-m-d H:i:s');
                            foreach ($this->items as $i => $item) { 
                                $status = empty($item->expires) || $now < $item->expires ? 'ACTIVE' : 'EXPIRED';
                                $date = JDate::getInstance($item->date)->format('Y-m-d');
                                $expires = !empty($item->expires) ? JDate::getInstance($item->expires)->format('Y-m-d') : '';
?>
                                <tr data-id="<?php echo $item->id; ?>">
                                    <td class="select-td">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                        <input type="hidden" value='<?php echo htmlspecialchars($str, ENT_QUOTES); ?>'>
                                    </td>
                                    <td class="status-td">
                                        <?php echo JText::_($status); ?>
                                    </td>
                                    <td class="title-td">
                                        <?php echo $item->title; ?>
                                    </td>
                                    <td class="date-td">
                                        <?php echo $date; ?>
                                    </td>
                                    <td class="expires-td">
                                        <?php echo $expires; ?>
                                    </td>
                                    <td class="customer-td">
                                        <?php echo $item->username; ?>
                                    </td>
                                    <td class="email-td">
                                        <?php echo $item->email; ?>
                                    </td>
                                    <td class="id-td">
<?php
                                        echo $item->id;
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
?>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="ba_view" value="subscriptions">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="view-subscriptions-dialog" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('SUBSCRIPTION_DETAILS'); ?></h3>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="subscription-details-header">
            <span class="subscription-details-title"></span>
        </div>
        <div class="row-fluid">
            <div class="subscription-info-wrapper">
                <div class="ba-options-group-header-wrapper">
                    <span class="ba-options-group-header"><?php echo JText::_('SUBSCRIPTION_INFO'); ?></span>
                </div>
                <div class="ba-options-group-wrapper">
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">User</label>
                        <div class="ba-options-input-action-wrapper">
                            <div class="customer-info-data" data-key="username"></div>
                        </div>
                    </div>
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">Email</label>
                        <div class="ba-options-input-action-wrapper">
                            <div class="customer-info-data" data-key="email"></div>
                        </div>
                    </div>
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">Date</label>
                        <div class="ba-options-input-action-wrapper">
                            <div class="customer-info-data" data-key="date"></div>
                        </div>
                    </div>
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">Expires</label>
                        <div class="ba-options-input-action-wrapper">
                            <div class="customer-info-data" data-key="expires"></div>
                            <div class="edit-subscription-expires">
                                <input type="hidden" class="open-calendar-dialog" data-format="Y-m-d">
                                <i class="zmdi zmdi-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="subscription-orders-history-wrapper">
                <div class="ba-options-group-header-wrapper">
                    <span class="ba-options-group-header"><?php echo JText::_('ORDERS_HISTORY'); ?></span>
                    <span class="order-buttons-wrapper">
                        <span class="renew-subscription-btn">
                            <span class="renew-subscription-btn-icon">
                                <i class="zmdi zmdi-plus"></i>
                            </span>
                            <span class="renew-subscription-btn-title"><?php echo JText::_('RENEW'); ?></span>
                        </span>
                    </span>
                </div>
                <div class="ba-options-group-wrapper">
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">#00000054</label>
                        <div class="customer-info-data">
                            <span>2021-06-22 22:18</span>
                            <span class="ba-cart-price-wrapper">
                                <span class="ba-cart-price-value">90.00 €</span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">#00000055</label>
                        <div class="customer-info-data">
                            <span>2021-06-23 22:18</span>
                            <span class="ba-cart-price-wrapper">
                                <span class="ba-cart-price-value">75.00 €</span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-options-group-element">
                        <label class="ba-options-group-label">#00000056</label>
                        <div class="customer-info-data">
                            <span>2021-06-24 22:18</span>
                            <span class="ba-cart-price-wrapper">
                                <span class="ba-cart-price-value">90.00 €</span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-options-group-element order-total-element">
                        <div class="ba-options-group-element-content">
                            <label class="ba-options-group-label">Total</label>
                            <span class="ba-cart-price-wrapper">
                                <span class="ba-cart-price-value">255.00 €</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="subscription-renew-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('RENEW'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="subscriptions-renew-plans-wrapper">
<?php
        if ($this->promo) {
?>
            <div class="ba-options-group-wrapper subscriptions-coupon-code-wrapper">
                <div class="ba-options-group-element">
                    <label class="ba-options-group-label"><?php echo JText::_('COUPON_CODE'); ?></label>
                    <div class="ba-options-input-action-wrapper">
                        <input type="text" readonly="" onfocus="this.blur()">
                        <div class="subscription-renew-coupon-code input-action-icon">
                            <i class="zmdi zmdi-playlist-plus"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
                        </div>
                        <div class="reset-coupon-code input-action-icon">
                            <i class="zmdi zmdi-close"></i>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
?>
            <div class="ba-options-group-wrapper subscriptions-total-wrapper">
                <div class="ba-options-group-element order-subtotal-element">
                    <label class="ba-options-group-label"><?php echo JText::_('SUBTOTAL'); ?></label>
                    <span class="ba-cart-price-wrapper ">
                        <span class="ba-cart-price-value">0.00 €</span>
                    </span>
                </div>
<?php
            if ($this->promo) {
?>
                <div class="ba-options-group-element order-discount-element">
                    <label class="ba-options-group-label"><?php echo JText::_('DISCOUNT'); ?></label>
                    <span class="ba-cart-price-wrapper ">
                        <span class="ba-cart-price-minus">-</span>
                        <span class="ba-cart-price-value">0.00 €</span>
                    </span>
                </div>
<?php
            }
            if (!empty(gridboxHelper::$store->tax->rates) && $mode == 'excl') {
?>
                <div class="ba-options-group-element order-tax-element" data-mode="<?php echo $mode; ?>">
                    <label class="ba-options-group-label"><?php echo JText::_('TAX'); ?></label>
                    <span class="ba-cart-price-wrapper">
                        <span class="ba-cart-price-value">0.00 €</span>
                    </span>
                </div>
<?php
            }
?>
                <div class="ba-options-group-element order-total-element" data-mode="<?php echo $mode; ?>">
                    <label class="ba-options-group-label"><?php echo JText::_('TOTAL'); ?></label>
                    <span class="ba-cart-price-wrapper">
                        <span class="ba-cart-price-value"></span>
                    </span>
                </div>
<?php
            if (!empty(gridboxHelper::$store->tax->rates) && $mode == 'incl') {
?>
                <div class="ba-options-group-element order-tax-element" data-mode="<?php echo $mode; ?>">
                    <label class="ba-options-group-label">
                        <?php echo JText::_('INCLUDING_TAXES').' 0.00 €'; ?>
                    </label>
                </div>
<?php
            }
?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary apply-subscription-renew">
            <?php echo JText::_('JTOOLBAR_APPLY') ?>
        </a>
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
<template data-key="subscription-order">
    <div class="ba-options-group-element">
        <label class="ba-options-group-label"></label>
        <div class="customer-info-data">
            <span class="subscription-order-date"></span>
            <span class="ba-cart-price-wrapper">
                <span class="ba-cart-price-value"></span>
            </span>
        </div>
    </div>
</template>
<template data-key="subscription-order-total">
    <div class="ba-options-group-element order-total-element">
        <div class="ba-options-group-element-content">
            <label class="ba-options-group-label"><?php echo JText::_('TOTAL'); ?></label>
            <span class="ba-cart-price-wrapper">
                <span class="ba-cart-price-value"></span>
            </span>
        </div>
    </div>
</template>
<template data-key="subscriptions-renew-plan">
    <div class="subscriptions-renew-plan">
        <div class="ba-checkbox-wrapper">
            <span class="ba-cart-price-wrapper">
                <span class="ba-cart-price-value"></span>
            </span>
            <span class="subscriptions-renew-plan-title">
                
            </span>
            <label class="ba-radio">
                <input type="radio" name="renew-radio">
                <span></span>
            </label>
        </div>
    </div>
</template>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');