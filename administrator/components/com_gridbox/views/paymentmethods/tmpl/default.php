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
?>
<script src="<?php echo JUri::root(); ?>/administrator/components/com_gridbox/assets/js/sortable.js" type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=paymentmethods'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('PAYMENT_METHODS'); ?></h1>
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
                    <div class="main-table payment-methods-table twin-view-table">
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
                                    <th>
                                        <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="order-list-sorting" data-handle="> tr > td.sortable-handle-td i">
<?php
                            foreach ($this->items as $i => $item) {
                                $canChange = $user->authorise('core.edit.state', 'com_gridbox');
                                $img = $this->methods->{$item->type}->image;
                                $image = JUri::root().'/administrator/components/com_gridbox/assets/images/'.$img;
                                if ($item->type != 'offline') {
                                    $this->methods->{$item->type}->installed = true;
                                }
?>
                                <tr data-type="<?php echo $item->type; ?>" data-id="<?php echo $item->id; ?>">
                                    <td class="select-td ">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                    </td>
                                    <td class="status-td">
                                    <?php echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'paymentmethods.', $canChange); ?>
                                    </td>
                                    <td class="title-cell">
                                        <span class="payment-methods-text-wrapper">
                                            <span class="ba-item-thumbnail" style="background-image: url(<?php echo $image; ?>);"></span>
                                            <span class="payment-methods-title">
                                                <?php echo $item->title; ?>
                                            </span>
                                            <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
                                        </span>
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
                        <div class="twin-view-right-sidebar">
                            <div class="twin-view-sidebar-header">
<?php
                            if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                                <span class="disabled apply-payment-methods">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
<?php
                            if ($user->authorise('core.delete', 'com_gridbox')) {
?>
                                <span class="disabled delete-payment-method">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </span>
<?php
                            }
?>
                                
                            </div>
                            <div class="twin-view-sidebar-body">
                                <div class="ba-options-group-header-wrapper">
                                    <span class="ba-options-group-header">Paypal</span>
                                </div>
                                <div class="ba-options-group-wrapper">
                                    <div class="ba-options-group-element">
                                        <label class="ba-options-group-label"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
                                        <input type="text" data-key="title">
                                    </div>
                                    <div class="offline-payment-options">
                                        <div class="ba-options-group-element ckeditor-options-wrapper">
                                            <label class="ba-options-group-label"><?php echo JText::_('DESCRIPTION'); ?></label>
                                            <textarea data-settings="description" data-cke="simple"></textarea>
                                        </div>
                                    </div>
                                    <div class="mono-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">X-Token</label>
                                            <input type="text" data-settings="token">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">ISO Default currency code</label>
                                            <input type="text" data-settings="ccy">
                                        </div>
                                    </div>
                                    <div class="paypal-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Client ID</label>
                                            <input type="text" data-settings="client_id">
                                        </div>
                                    </div>
                                    <div class="mollie-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">API Key</label>
                                            <input type="text" data-settings="api_key">
                                        </div>
                                    </div>
                                    <div class="payupl-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">POS ID</label>
                                            <input type="text" data-settings="pos_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Second key</label>
                                            <input type="text" data-settings="second_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Client ID</label>
                                            <input type="text" data-settings="client_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Client Secret</label>
                                            <input type="text" data-settings="client_secret">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="twocheckout-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Account Number</label>
                                            <input type="text" data-settings="account_number">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="cloudpayments-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Public ID</label>
                                            <input type="text" data-settings="public_id">
                                        </div>
                                    </div>
                                    <div class="liqpay-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Public Key</label>
                                            <input type="text" data-settings="public_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Private Key</label>
                                            <input type="text" data-settings="private_key">
                                        </div>
                                    </div>
                                    <div class="stripe-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">API Key</label>
                                            <input type="text" data-settings="api_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Secret Key</label>
                                            <input type="text" data-settings="secret_key">
                                        </div>
                                    </div>
                                    <div class="yandex-kassa-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Shop ID</label>
                                            <input type="text" data-settings="shop_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Secret Key</label>
                                            <input type="text" data-settings="secret_key">
                                        </div>
                                    </div>
                                    <div class="klarna-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Merchant Username</label>
                                            <input type="text" data-settings="username">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Merchant Password</label>
                                            <input type="text" data-settings="password">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Region</label>
                                            <select data-settings="region">
                                                <option value="" hidden=""></option>
                                                <option value="europe">Europe</option>
                                                <option value="america">North America</option>
                                                <option value="oceania">Oceania</option>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Country Code</label>
                                            <input type="text" data-settings="country">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Terms and Conditions URL</label>
                                            <input type="text" data-settings="terms">
                                        </div>
                                    </div>
                                    <div class="authorize-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">API Login ID</label>
                                            <input type="text" data-settings="login_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Transaction Key</label>
                                            <input type="text" data-settings="transaction_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="payfast-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Merchant ID</label>
                                            <input type="text" data-settings="merchant_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Merchant Key</label>
                                            <input type="text" data-settings="merchant_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="robokassa-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Shop Identifier</label>
                                            <input type="text" data-settings="merchant_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Password #1</label>
                                            <input type="text" data-settings="merchant_password">
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper">
                                            <label class="ba-options-group-label">Fiscalization</label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-settings="fiscalization" class="set-group-display">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-subgroup-element">
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">Tax System</label>
                                                <select data-settings="sno">
                                                    <option value="" hidden=""></option>
                                                    <option value="osn">General</option>
                                                    <option value="usn_income">Simplified (Income)</option>
                                                    <option value="usn_income_outcome">Simplified (Income minus expenses)</option>
                                                    <option value="esn">Unified agricultural tax</option>
                                                    <option value="patent">Patent</option>
                                                </select>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">Payment method</label>
                                                <select data-settings="payment_method">
                                                    <option value="" hidden=""></option>
                                                    <option value="full_prepayment">Full prepayment</option>
                                                    <option value="prepayment">Prepayment</option>
                                                    <option value="advance">Advance</option>
                                                    <option value="full_payment">Full payment</option>
                                                    <option value="partial_payment">Partial payment</option>
                                                    <option value="credit">Credit</option>
                                                    <option value="credit_payment">Credit payment</option>
                                                </select>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">Payment object</label>
                                                <select data-settings="payment_object">
                                                    <option value="" hidden=""></option>
                                                    <option value="commodity">Commodity</option>
                                                    <option value="excise">Excise</option>
                                                    <option value="job">Job</option>
                                                    <option value="service">Service</option>
                                                    <option value="gambling_bet">Gambling bet</option>
                                                    <option value="gambling_prize">Gambling prize</option>
                                                    <option value="lottery">Lottery</option>
                                                    <option value="lottery_prize">Lottery prize</option>
                                                    <option value="intellectual_activity">Intellectual activity</option>
                                                    <option value="payment">Payment</option>
                                                    <option value="agent_commission">Agent commission</option>
                                                    <option value="composite">Composite</option>
                                                    <option value="resort_fee">Resort fee</option>
                                                    <option value="another">Another</option>
                                                    <option value="property_right">Property right</option>
                                                    <option value="non-operating_gain">Non-operating gain</option>
                                                    <option value="insurance_premium">Insurance premium</option>
                                                    <option value="sales_tax">Sales tax</option>
                                                </select>
                                            </div>
                                            <div class="ba-options-group-element">
                                                <label class="ba-options-group-label">Tax </label>
                                                <select data-settings="tax">
                                                    <option value="" hidden=""></option>
                                                    <option value="none">Without VAT</option>
                                                    <option value="vat0">VAT at 0%</option>
                                                    <option value="vat10">VAT check at a rate of 10%</option>
                                                    <option value="vat110">VAT check at the estimated rate 10/110</option>
                                                    <option value="vat20">VAT check at a rate of 20%</option>
                                                    <option value="vat120">VAT check at the estimated rate of 20/120</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dotpay-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Account ID</label>
                                            <input type="text" data-settings="account_id" class="integer-validation" data-decimals="0">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">PIN</label>
                                            <input type="text" data-settings="pin">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="pagseguro-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Email</label>
                                            <input type="text" data-settings="email">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Token</label>
                                            <input type="text" data-settings="token">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="square-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Access Token</label>
                                            <input type="text" data-settings="access_token">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Location ID</label>
                                            <input type="text" data-settings="location_id">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="barion-payment-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Public Key</label>
                                            <input type="text" data-settings="public_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Secret Key</label>
                                            <input type="text" data-settings="secret_key">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Barion Valid Email</label>
                                            <input type="text" data-settings="email">
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Environment</label>
                                            <select data-settings="environment">
                                                <option value="" hidden=""></option>
                                                <option value="production">Production</option>
                                                <option value="sandbox">Sandbox</option>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper">
                                            <label class="ba-options-group-label">Guest Checkout</label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-settings="guest">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper">
                                            <label class="ba-options-group-label">Disable Debit and Credit Cards</label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-settings="sources">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label">Language</label>
                                            <select data-settings="locale">
                                                <option value="" hidden=""></option>
                                                <option value="cs-CZ">Czech</option>
                                                <option value="de-DE">German</option>
                                                <option value="en-US">English</option>
                                                <option value="es-ES">Spanish</option>
                                                <option value="fr-FR">French</option>
                                                <option value="hu-HU">Hungarian</option>
                                                <option value="sk-SK">Slovak</option>
                                                <option value="sl-SI">Slovenian</option>
                                            </select>
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
                    <div class="ba-create-item ba-add-payment-method">
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
                        <input type="hidden" name="ba_view" value="paymentmethods">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
                <div id="gridbox-payment-methods-dialog" class="ba-modal-lg modal hide" style="display: none;">
                    <div class="modal-header">
                        <span class="ba-dialog-title"><?php echo JText::_('PAYMENT_METHODS'); ?></span>
                        <div class="modal-header-icon">
                            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="ba-filter-bar">
                            <input type="text" class="search-gridbox-apps">
                            <i class="zmdi zmdi-search"></i>
                        </div>
                        <div class="ba-group-wrapper gridbox-apps-wrapper">
<?php
                        foreach ($this->methods as $key => $method) {
                            $img = JUri::root().'administrator/components/com_gridbox/assets/images/'.$method->image;
?>
                            <div class="gridbox-app-element" data-type="<?php echo $key; ?>"
                                    data-installed="<?php echo (int)$method->installed; ?>">
                                <div class="gridbox-app-item-body">
                                    <img src="<?php echo $img; ?>">
                                    <span class="ba-title"><?php echo $method->title; ?></span>
                                    <span class="default-theme<?php echo $method->installed ? '' : ' ba-hide-element'; ?>">
                                        <i class="zmdi zmdi-check-circle"></i>
                                    </span>
                                </div>
                            </div>
<?php
                        }
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/photo-editor.php');
?>