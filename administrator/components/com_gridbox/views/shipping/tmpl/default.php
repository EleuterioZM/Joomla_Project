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
$types = array('pickup' => JText::_('STORE_PICKUP'), 'free' => JText::_('FREE_SHIPPING'),
    'flat' => JText::_('FLAT_RATE'), 'prices' => JText::_('RATE_BY_PRICE'),
    'weight' => JText::_('RATE_BY_WEIGHT_RANGE'), 'weight-unit' => JText::_('RATE_BY_WEIGHT_UNIT'),
    'product' => JText::_('RATE_PER_PRODUCT'), 'category' => JText::_('RATE_PER_CATEGORY')
    );
?>
<script src="<?php echo JUri::root(); ?>/administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<?php
include(JPATH_COMPONENT.'/views/layouts/ckeditor.php');
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=shipping'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('SHIPPING'); ?></h1>
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
                    <div class="main-table shipping-table twin-view-table">
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
                                    <th class="status-th">
                                        <span><?php echo JText::_('JSTATUS'); ?></span>
                                        <div class="state-filter">
                                            <div class="ba-custom-select">
                                                <input type="hidden" data-name="filter_state" value="<?php echo $state; ?>">
                                                <i class="zmdi zmdi-caret-down"></i>
                                                <ul>
                                                    <li data-value="">
                                                        <?php echo JText::_('JSTATUS'); ?>
                                                    </li>
                                                    <li data-value="1" >
                                                        <?php echo JText::_('JPUBLISHED'); ?>
                                                    </li>
                                                    <li data-value="0">
                                                        <?php echo JText::_('JUNPUBLISHED'); ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                    </th>
                                    <th>
                                        <?php echo JText::_('TYPE'); ?>
                                    </th>
                                    <th>
                                        <?php echo JText::_('PRICE'); ?>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="order-list-sorting" data-handle="> tr > td.sortable-handle-td i">
<?php
                            foreach ($this->items as $i => $item) {
                                $canChange = $user->authorise('core.edit.state', 'com_gridbox');
                                $params = json_decode($item->options);
                                $price = '';
                                if ($params->type == 'pickup' || $params->type == 'free') {
                                    $price = JText::_('FREE');
                                } else if (($params->type == 'flat' || $params->type == 'weight-unit' || $params->type == 'product')
                                    && $params->{$params->type}->price !== '') {
                                    $price = gridboxHelper::preparePrice($params->{$params->type}->price);
                                } else if ($params->type == 'prices' || $params->type == 'weight' || $params->type == 'category') {
                                    $array = array();
                                    foreach ($params->{$params->type}->range as $value) {
                                        $array[] = $value->price;
                                    }
                                    $count = count($array);
                                    if ($count == 1) {
                                        $array[0] = $array[0] === '' ? 0 : $array[0];
                                        $price = gridboxHelper::preparePrice($array[0]);
                                    } else if ($count != 0) {
                                        $min = min($array);
                                        $max = max($array);
                                        $price = gridboxHelper::preparePrice($min).' - '.gridboxHelper::preparePrice($max);
                                    }
                                }
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
                                        <?php echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'shipping.', $canChange); ?>
                                    </td>
                                    <td class="title-cell">
                                        <span><?php echo $item->title; ?></span>
                                        <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
                                    </td>
                                    <td class="type-cell">
                                        <?php echo $types[$params->type]; ?>
                                    </td>
                                    <td class="price-cell">
<?php
                                        echo $price;
?>
                                    </td>
                                    <td class="sortable-handle-td">
                                        <i class="zmdi zmdi-swap-vertical sortable-handle"></i>
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
                                <span class="disabled apply-shipping">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
<?php
                            if ($user->authorise('core.delete', 'com_gridbox')) {
?>
                                <span class="disabled delete-shipping">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </span>
<?php
                            }
?>
                            </div>
                            <div class="twin-view-sidebar-body">
                                <div class="shipping-options">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('TYPE'); ?></label>
                                            <select data-settings="type">
<?php
                                            foreach ($types as $key => $type) {
?>
                                                <option value="<?php echo $key; ?>"><?php echo $type; ?></option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element"<?php echo count($this->carriers) == 0 ? ' style="display:none;"' : ''; ?>>
                                            <label class="ba-options-group-label"><?php echo JText::_('CARRIER'); ?></label>
                                            <select data-key="carrier">
                                                <option value="0"><?php echo JText::_('NONE_SELECTED'); ?></option>
<?php
                                            foreach ($this->carriers as $carrier) {
?>
                                                <option value="<?php echo $carrier->id; ?>"><?php echo $carrier->title; ?></option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
                                            <input type="text" data-key="title">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                <label><?php echo JText::_('EST_DELIVERY_TIME'); ?></label>
                                                <label class="ba-checkbox">
                                                    <input type="checkbox" class="label-toggle-btn"
                                                        data-group="time" data-settings="enabled">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <input type="text" placeholder="<?php echo JText::_('2_4_BUSINESS_DAYS'); ?>"
                                                data-group="time" data-settings="text">
                                        </div>
                                        <div class="ba-options-group-element ckeditor-options-wrapper">
                                            <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                <label><?php echo JText::_('DESCRIPTION'); ?></label>
                                                <label class="ba-checkbox">
                                                    <input type="checkbox" class="label-toggle-btn"
                                                        data-group="description" data-settings="enabled">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <textarea data-group="description" data-settings="text" data-cke="simple"></textarea>
                                        </div>
                                        <div class="ba-options-group-header-wrapper shipping-type-options-label">
                                            <span class="ba-options-group-header"><?php echo JText::_('OPTIONS'); ?></span>
                                        </div>
                                        <div class="flat-shipping-type shipping-type-options" data-type="flat">
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label"><?php echo JText::_('DELIVERY_COST'); ?></label>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="flat"
                                                        data-settings="price" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="flat" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="flat"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-unit-shipping-type shipping-type-options" data-type="weight-unit">
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">
                                                    <?php echo JText::_('DELIVERY_COST_PER_WEIGHT_UNIT'); ?>
                                                </label>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="weight-unit"
                                                        data-settings="price" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="weight-unit" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="weight-unit"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-shipping-type shipping-type-options" data-type="product">
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">
                                                    <?php echo JText::_('DELIVERY_COST_PRODUCT_UNIT'); ?>
                                                </label>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="product"
                                                        data-settings="price" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="product" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="product"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prices-shipping-type shipping-type-options" data-type="prices">
                                            <div class="ba-rate-by-wrapper">
                                                <div class="ba-rate-by-list" data-group="prices" data-settings="range">
                                                    
                                                </div>
                                                <div class="ba-rate-by-add-new">
                                                    <span class="add-new-rate-by" data-target="prices">
                                                        <i class="zmdi zmdi-plus-circle"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_RANGE'); ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="prices" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="prices"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="weight-shipping-type shipping-type-options" data-type="weight">
                                            <div class="ba-rate-by-wrapper">
                                                <div class="ba-rate-by-list" data-group="weight" data-settings="range">
                                                    
                                                </div>
                                                <div class="ba-rate-by-add-new">
                                                    <span class="add-new-rate-by" data-target="weight">
                                                        <i class="zmdi zmdi-plus-circle"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_RANGE'); ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="weight" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="weight"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category-shipping-type shipping-type-options" data-type="category">
                                            <div class="ba-rate-by-wrapper">
                                                <div class="ba-rate-by-list" data-group="category" data-settings="range">
                                                    
                                                </div>
                                                <div class="ba-rate-by-add-new">
                                                    <span class="add-new-rate-by" data-target="category">
                                                        <i class="zmdi zmdi-plus-circle"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_RANGE'); ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <div class="ba-options-group-label-wrapper toggle-button-wrapper">
                                                    <label><?php echo JText::_('OFFER_FREE_SHIPPING_OVER_AMOUNT'); ?></label>
                                                    <label class="ba-checkbox">
                                                        <input type="checkbox" class="label-toggle-btn"
                                                            data-group="category" data-settings="enabled">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                                                    <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                                                    <input type="text" class="integer-validation" data-group="category"
                                                        data-settings="free" data-decimals="<?php echo $currency->decimals; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('SHIPPING_REGIONS'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('AVAILABLE'); ?></label>
                                            <div class="shipping-countries-wrapper">
                                                <div class="shipping-countries-list" data-group="regions" data-settings="available"></div>
                                                <div class="shipping-add-countries">
<?php
                                                    $tooltip = JText::_('ADD_COUNTRY');
?>
                                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo $tooltip; ?></span>
                                                    <i class="zmdi zmdi-globe"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('RESTRICTED'); ?></label>
                                            <div class="shipping-countries-wrapper">
                                                <div class="shipping-countries-list" data-group="regions" data-settings="restricted"></div>
                                                <div class="shipping-add-countries">
<?php
                                                    $tooltip = JText::_('ADD_COUNTRY');
?>
                                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo $tooltip; ?></span>
                                                    <i class="zmdi zmdi-globe"></i>
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
                    <div class="ba-create-item ba-add-shipping">
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
                        <input type="hidden" name="ba_view" value="shipping">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/countries-modal.php');
include(JPATH_COMPONENT.'/views/layouts/categories-modal.php');
?>
<div id="store-states-list-dialog" class="modal hide ba-modal-picker picker-modal-arrow visible-country"
    style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-list-type">
            <div class="states-modal-header-wrapper">
                <span class="states-modal-header"></span>
            </div>
            <ul>
                
            </ul>
            <template class="states-list-li">
                <li class="toggle-button-wrapper">
                    <span class="picker-item-title"></span>
                    <label class="ba-checkbox">
                        <input type="checkbox">
                        <span></span>
                    </label>
                </li>
            </template>
        </div>
    </div>
</div>
<template class="rate-by-prices">
    <div class="ba-rate-by-line">
        <div class="up-to-rate-value">
            <label class="ba-options-group-label">
                <?php echo JText::_('UP_TO'); ?>
            </label>
            <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                <input type="text" class="integer-validation" data-ind="rate"
                    data-decimals="<?php echo $currency->decimals; ?>">
            </div>
        </div>
        <div class="up-to-rate-value">
            <label class="ba-options-group-label">
                <?php echo JText::_('DELIVERY_COST'); ?>
            </label>
            <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                <input type="text" class="integer-validation" data-ind="price"
                    data-decimals="<?php echo $currency->decimals; ?>">
            </div>
        </div>
        <div class="delete-up-to-rate-line">
            <i class="zmdi zmdi-delete"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
        </div>
    </div>
</template>
<template class="rate-by-weight">
    <div class="ba-rate-by-line">
        <div class="up-to-rate-value">
            <label class="ba-options-group-label">
                <?php echo JText::_('UP_TO'); ?>
            </label>
            <div class="ba-options-price-wrapper right-currency-position">
                <span class="ba-options-price-currency"><?php echo gridboxHelper::$store->units->weight; ?></span>
                <input type="text" class="integer-validation" data-ind="rate" data-decimals="2">
            </div>
        </div>
        <div class="up-to-rate-value">
            <label class="ba-options-group-label">
                <?php echo JText::_('DELIVERY_COST'); ?>
            </label>
            <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                <input type="text" class="integer-validation" data-ind="price"
                    data-decimals="<?php echo $currency->decimals; ?>">
            </div>
        </div>
        <div class="delete-up-to-rate-line">
            <i class="zmdi zmdi-delete"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
        </div>
    </div>
</template>
<template class="rate-by-category">
    <div class="ba-rate-by-line">
        <div class="up-to-rate-value">
            <label class="ba-options-group-label">
                <?php echo JText::_('DELIVERY_COST'); ?>
            </label>
            <div class="ba-options-price-wrapper <?php echo $currency->position; ?>">
                <span class="ba-options-price-currency"><?php echo $currency->symbol; ?></span>
                <input type="text" class="integer-validation" data-ind="price"
                    data-decimals="<?php echo $currency->decimals; ?>">
            </div>
        </div>
        <div class="up-to-rate-value">
            <div class="ba-options-price-wrapper right-currency-position">
                <div class="selected-items-list-wrapper">
                    <div class="selected-items-list-btn-wrapper">
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_CATEGORY'); ?></span>
                        <i class="zmdi zmdi-folder add-category-rate"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="delete-up-to-rate-line">
            <i class="zmdi zmdi-delete"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
        </div>
        <div class="selected-items-list" data-ind="rate"></div>
    </div>
</template>