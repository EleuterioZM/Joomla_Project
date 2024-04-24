<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$user = JFactory::getUser();
$store = gridboxHelper::$store;
$notifications = $store->notifications;
$countries = gridboxHelper::getTaxCountries();
$countriesList = new stdClass();
foreach ($countries as $country) {
    $countriesList->{$country->id} = $country;
    $country->regions = new stdClass();
    foreach ($country->states as $state) {
        $country->regions->{$state->id} = $state;
    }
}
$currencies = $store->currencies;
$added = isset($store->added) ? $store->added : [];

?>
<script src="<?php echo JUri::root(); ?>/administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/ckeditor.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/dataTags.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/resizeEditor.js"></script>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=promocodes'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
<?php
                include(JPATH_COMPONENT.'/views/layouts/sidebar.php');
?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('STORE_SETTINGS'); ?></h1>
                        </div>
                        <div class="filter-search-wrapper">
                            
                        </div>
                    </div>
                    <div class="main-table store-settings-table">
                        <div class="store-settings-header">
                            <div class="store-settings-header-left-panel"></div>
                            <div class="store-settings-header-right-panel">
<?php
                            if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                                <span class="apply-store-settings" data-id="<?php echo $this->store->id; ?>">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
                            </div>
                        </div>
                        <div class="store-settings-body">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#store-general-options" data-toggle="tab">
                                        <i class="zmdi zmdi-store"></i>
                                        <?php echo JText::_('GENERAL'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#store-email-options" data-toggle="tab">
                                        <i class="zmdi zmdi-notifications-active"></i>
                                        <?php echo JText::_('EMAIL_NOTIFICATIONS'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#store-currency-options" data-toggle="tab">
                                        <i class="zmdi zmdi-money"></i>
                                        <?php echo JText::_('CURRENCY_UNITS'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#store-tax-options" data-toggle="tab">
                                        <i class="zmdi zmdi-balance-wallet"></i>
                                        <?php echo JText::_('TAX'); ?>
                                    </a>
                                </li>
<?php
                            if ($installedStore) {
?>
                                <li>
                                    <a href="#store-order-statuses-options" data-toggle="tab">
                                        <i class="zmdi zmdi-assignment-check"></i>
                                        <?php echo JText::_('ORDER_STATUSES'); ?>
                                    </a>
                                </li>
<?php
                            }
?>
                            </ul>
                            <div class="tab-content">
                                <div id="store-general-options" class="tab-pane active">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('GENERAL_INFO'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element full-width-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('BUSINESS_INFO'); ?></label>
                                            <span class="trigger-general-modals" data-modal="business-info-modal">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top">
                                                    <?php echo JText::_('EDIT'); ?>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="ba-options-group-element full-width-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('INVOICE'); ?></label>
                                            <span class="trigger-general-modals" data-modal="invoice-modal">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top">
                                                    <?php echo JText::_('EDIT'); ?>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="ba-options-group-header-wrapper toggle-buttons-header">
                                            <span class="ba-options-group-header"><?php echo JText::_('CHECKOUT'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element full-width-group-element minimum-order-amount">
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('MINIMUM_ORDER_AMOUNT'); ?>
                                            </label>
                                            <div class="ba-options-price-wrapper <?php echo $store->currency->position; ?>">
                                                <span class="ba-options-price-currency">
                                                    <?php echo $store->currency->symbol; ?>
                                                </span>
                                                <input type="text" class="integer-validation" data-key="minimum"
                                                    data-group="checkout" data-decimals="2"
                                                    value="<?php echo $store->checkout->minimum; ?>">
                                            </div>
                                        </div>
                                        <div class="ba-options-group-header-wrapper toggle-buttons-header">
                                            <span class="ba-options-group-header"><?php echo JText::_('LOGIN'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element">
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('GUEST_CHECKOUT'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="guest" data-group="checkout"
                                                    <?php echo $store->checkout->guest ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element">
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('LOGIN'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="login" data-group="checkout"
                                                    <?php echo $store->checkout->login ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
<?php
                                        $style = $store->checkout->login ? '' : ' style="display: none;"';
?>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            <?php echo $style; ?>>
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('PASSWORD_REMINDER'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="password" data-group="checkout"
                                                    <?php echo $store->checkout->password ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            <?php echo $style; ?>>
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('USERNAME_REMINDER'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="username" data-group="checkout"
                                                    <?php echo $store->checkout->username ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            <?php echo $style; ?>>
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('USER_REGISTRATION'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="registration" data-group="checkout"
                                                    <?php echo $store->checkout->registration ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
<?php
                                        $style2 = $store->checkout->registration && empty($style) ? '' : ' style="display: none;"';
?>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            <?php echo $style2; ?>>
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('ACCEPTANCE'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="terms" data-group="checkout"
                                                    <?php echo $store->checkout->terms ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                            <textarea data-key="terms_text" data-group="checkout"
                                                style="display: none !important;"><?php echo $store->checkout->terms_text; ?></textarea>
                                            <span class="edit-login-acceptance">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-bottom"><?php echo JText::_('EDIT'); ?></span>
                                            </span>
                                        </div>
<?php
                                        $configured = !empty($this->integrations->facebook_login->key);
?>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            <?php echo $style; ?> data-configured="<?php echo intval($configured); ?>">
                                            <label class="ba-options-group-label">
                                                Facebook Login
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="facebook" data-group="checkout"
                                                    <?php echo $store->checkout->facebook ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                            <span class="integrations-configuration-icon">
                                                <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top">
                                                    <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                                </span>
                                            </span>
                                            <a class="integrations-configuratio-link" target="_blank"
                                                href="index.php?option=com_gridbox&view=integrations">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                                            </a>
                                        </div>
<?php
                                        $configured = !empty($this->integrations->google_login->key);
?>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element"
                                            data-configured="<?php echo intval($configured); ?>"
                                            <?php echo $style; ?>>
                                            <label class="ba-options-group-label">
                                                Google Login
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="google" data-group="checkout"
                                                    <?php echo $store->checkout->google ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                            <span class="integrations-configuration-icon">
                                                <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top">
                                                    <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                                </span>
                                            </span>
                                            <a class="integrations-configuratio-link" target="_blank"
                                                href="index.php?option=com_gridbox&view=integrations">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                                            </a>
                                        </div>
                                        <div class="ba-options-group-element full-width-group-element recaptcha-wrapper"
                                            <?php echo $style; ?>>
                                            <label class="ba-options-group-label">
                                                reCAPTCHA
                                            </label>
                                            <select data-value="<?php echo $store->checkout->recaptcha; ?>" data-key="recaptcha" data-group="checkout">
                                                <option value=""><?php echo JText::_('NONE_SELECTED'); ?></option>
                                            </select>
                                            <div style="display: none !important;">
<?php
                                                echo $this->form->getInput('comments_recaptcha');
?>
                                            </div>
                                        </div>
                                        <div class="ba-options-group-header-wrapper toggle-buttons-header">
                                            <span class="ba-options-group-header"><?php echo JText::_('WISHLIST'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element">
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('WISHLIST_ONLY_AUTHENTICATED'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="login" data-group="wishlist"
                                                    <?php echo $store->wishlist->login ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="store-email-options" class="tab-pane">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-element ba-options-group-sorting-wrapper full-width-group-element">
                                            <div class="ba-options-group-toolbar">
<?php
                                            if ($installedStore) {
?>
                                                <div>
                                                    <label data-action="add" class="add-email-notification">
                                                        <i class="zmdi zmdi-plus"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-action="delete" class="disabled">
                                                        <i class="zmdi zmdi-delete"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('DELETE'); ?>
                                                        </span>
                                                    </label>
                                                </div>
<?php
                                            }
?>
                                            </div>
                                            <div class="sorting-container">
<?php
                                            foreach ($notifications as $key => $notification) {
                                                if (isset($this->statuses[$notification->status])) {
                                                    $status = $this->statuses[$notification->status];
                                                } else {
                                                    $status = null;
                                                }
                                                if (!$installedStore && $notification->status != 'new-booking'
                                                    && $notification->status != 'appointment-reminder') {
                                                    continue;
                                                }
                                                $canDelete = !(in_array($notification->status, $this->systemStatuses));
?>
                                                <div class="sorting-item notification-sorting-item"
                                                    data-ind="<?php echo $key; ?>">
                                                    <div class="sorting-icon">
                                                        <i class="zmdi zmdi-more-vert sortable-handle"></i>
                                                    </div>
                                                    <div class="sorting-checkbox">
                                                        <label class="ba-checkbox ba-hide-checkbox">
                                                            <input type="checkbox" <?php echo $canDelete ? '' : 'disabled' ?>>
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    <div class="sorting-title">
                                                        <?php echo $notification->title; ?>
                                                    </div>
                                                    <div class="notification-sorting-type">
                                                        <?php echo JText::_(strtoupper($notification->type)); ?>
                                                    </div>
                                                    <div class="notification-sorting-status">
                                                        <span class="notification-status"
                                                        data-status="<?php echo $status ? $status->key : ''; ?>"
                                                        style="--status-color: <?php echo $status ? $status->color : ''; ?>">
                                                            <?php echo $status ? $status->title : ''; ?>
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
                                <div id="store-currency-options" class="tab-pane">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('CURRENCY'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element ba-options-group-sorting-wrapper full-width-group-element">
                                            <div class="ba-options-group-toolbar">
                                                <div>
                                                    <label data-action="add" class="add-currency">
                                                        <i class="zmdi zmdi-plus"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-action="delete" class="disabled">
                                                        <i class="zmdi zmdi-delete"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('DELETE'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="sorting-container">
<?php
                                            foreach ($currencies->list as $key => $currency) {
                                                $canDelete = !$currency->default;
?>
                                                <div class="sorting-item currency-sorting-item"
                                                    data-ind="<?php echo $key; ?>">
                                                    <div class="sorting-icon">
                                                        <i class="zmdi zmdi-more-vert sortable-handle"></i>
                                                    </div>
                                                    <div class="sorting-checkbox">
                                                        <label class="ba-checkbox ba-hide-checkbox">
                                                            <input type="checkbox" <?php echo $canDelete ? '' : 'disabled' ?>>
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    <div class="sorting-title">
                                                        <?php echo $currency->title; ?>
                                                    </div>
                                                    <div class="sorting-currency-symbol">
                                                        <?php echo $currency->symbol; ?>
                                                    </div>
                                                    <div class="sorting-currency-code">
                                                        <?php echo $currency->code; ?>
                                                    </div>
                                                    <div class="sorting-currency-rate">
                                                        <?php echo number_format($currency->rate, 2, '.', ''); ?>
                                                    </div>
                                                    <div class="sorting-currency-default" data-default="<?php echo intval($currency->default); ?>">
                                                        <i class="zmdi zmdi-star"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('DEFAULT'); ?>
                                                        </span>
                                                    </div>
                                                </div>
<?php
                                            }
?>
                                            </div>
                                        </div>
                                        <?php
                                        $configured = !empty($this->integrations->exchangerates->key);
?>
                                        <div class="ba-options-group-element toggle-button-wrapper full-width-group-element auto-exchangerates"
                                            data-configured="<?php echo intval($configured); ?>">
                                            <label class="ba-options-group-label">
                                                <?php echo JText::_('AUTO_UPDATE_EXCHANGE_RATES'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" data-key="auto" data-group="currencies"
                                                    <?php echo $currencies->auto ? ' checked' : ''; ?>>
                                                <span></span>
                                            </label>
                                            <span class="integrations-configuration-icon">
                                                <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top">
                                                    <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                                </span>
                                            </span>
                                            <a class="integrations-configuratio-link" target="_blank"
                                                href="index.php?option=com_gridbox&view=integrations">
                                                <i class="zmdi zmdi-settings"></i>
                                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                                            </a>
                                        </div>


                                        
                                        
                                        
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('UNITS'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('DEFAULT_WEIGHT_UNIT'); ?></label>
                                            <select data-key="weight" data-group="units">
<?php
                                                $selected = $store->units->weight == 'kg' ? ' selected' : '';
?>
                                                <option value="kg"<?php echo $selected; ?>><?php echo JText::_('KILOGRAMS'); ?></option>
<?php
                                                $selected = $store->units->weight == 'g' ? ' selected' : '';
?>
                                                <option value="g"<?php echo $selected; ?>><?php echo JText::_('GRAMS'); ?></option>
<?php
                                                $selected = $store->units->weight == 'lb' ? ' selected' : '';
?>
                                                <option value="lb"<?php echo $selected; ?>><?php echo JText::_('POUNDS'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="store-tax-options" class="tab-pane">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element full-width-group-element">
                                            <label class="ba-options-group-label"><?php echo JText::_('TAX_MODE'); ?></label>
<?php
                                            $array = array('excl' => JText::_('TAX_EXCLUSIVE'), 'incl' => JText::_('TAX_INCLUSIVE'));
?>
                                            <select class="store-tax-mode-select" data-key="mode" data-group="tax">
<?php
                                            foreach ($array as $key => $value) {
                                                $attr = $store->tax->mode == $key ? ' selected' : '';
?>
                                                <option value="<?php echo $key; ?>"<?php echo $attr; ?>><?php echo $value; ?></option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                        <div class="ba-options-group-header-wrapper">
                                            <span class="ba-options-group-header"><?php echo JText::_('TAXES_RATES'); ?></span>
                                        </div>
                                        <div class="ba-options-group-element ba-options-group-sorting-wrapper full-width-group-element">
                                            <div class="ba-options-group-toolbar">
                                                <div>
                                                    <label data-action="add" data-object="taxes">
                                                        <i class="zmdi zmdi-plus"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-action="delete" class="disabled">
                                                        <i class="zmdi zmdi-delete"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('DELETE'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="sorting-container">
<?php
                                            foreach ($store->tax->rates as $tax) {
                                                
                                                if (isset($countriesList->{$tax->country_id})) {
                                                    $country = $countriesList->{$tax->country_id};
                                                } else {
                                                    $country = null;
                                                }
?>
                                                <div class="sorting-item">
                                                    <div class="sorting-checkbox">
                                                        <label class="ba-checkbox ba-hide-checkbox">
                                                            <input type="checkbox">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    <div class="sorting-title">
                                                        <input type="text" value="<?php echo $tax->title; ?>"
                                                            placeholder="<?php echo JText::_('TITLE'); ?>">
                                                    </div>
                                                    <div class="sorting-tax-rate">
                                                        <input type="text" value="<?php echo $tax->rate; ?>" placeholder="%">
<?php
                                                    foreach ($tax->regions as $region) {
                                                        if ($country && isset($country->regions->{$region->state_id})) {
?>
                                                        <input type="text" value="<?php echo $region->rate; ?>" placeholder="%">
<?php
                                                        }
                                                    }
?>
                                                    </div>
                                                    <div class="sorting-tax-countries-wrapper">
                                                        <div class="sorting-tax-country">
                                                            <div class="tax-rates-items-wrapper">
<?php
                                                            if ($country) {
?>
                                                                <span class="selected-items" data-id="<?php echo $country->id ?>">
                                                                    <span class="selected-items-name"><?php echo $country->title; ?></span>
                                                                    <i class="zmdi zmdi-close delete-tax-country"></i>
                                                                </span>
<?php
                                                                $target = 'region';
                                                                $icon = 'zmdi zmdi-pin';
                                                            } else {
                                                                $target = 'country';
                                                                $icon = 'zmdi zmdi-globe';
                                                            }
?>
                                                            </div>
                                                            <div class="select-items-wrapper add-tax-country-region"
                                                                data-target="<?php echo $target ?>">
<?php
                                                            $tooltip = $country ? JText::_('ADD_REGION') : JText::_('ADD_COUNTRY');
?>
                                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo $tooltip; ?></span>
                                                                <i class="<?php echo $icon; ?>"></i>
                                                            </div>
                                                        </div>
<?php
                                                    foreach ($tax->regions as $region) {
                                                        if ($country && isset($country->regions->{$region->state_id})) {
                                                            $regionObj = $country->regions->{$region->state_id};
?>
                                                        <div class="tax-country-state">
                                                            <span class="selected-items" data-id="<?php echo $regionObj->id ?>">
                                                                <span class="selected-items-name"><?php echo $regionObj->title ?></span>
                                                                <i class="zmdi zmdi-close delete-country-region"></i>
                                                            </span>
                                                        </div>
<?php
                                                        }
                                                    }
?>
                                                    </div>
                                                    <div class="sorting-tax-category-wrapper"
                                                        style="--placeholder-text: '<?php echo JText::_('CATEGORY'); ?>';">
<?php
                                                        $categories = gridboxHelper::getCategories($tax->categories);
                                                        $str = '';
                                                        foreach ($categories as $category) {
                                                            $str .= '<span class="selected-items" data-id="'.$category->id;
                                                            $str .= '"><span class="selected-items-name">'.$category->title;
                                                            $str .= '</span><i class="zmdi zmdi-close remove-selected-items"></i></span>';
                                                        }
?>
                                                        <div class="tax-rates-items-wrapper"><?php echo $str; ?></div>
                                                        <div class="select-items-wrapper">
<?php
                                                            $tooltip = JText::_('ADD_CATEGORY');
?>
                                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo $tooltip; ?></span>
                                                            <i class="zmdi zmdi-folder add-tax-category"></i>
                                                        </div>
                                                    </div>
<?php
                                                    $attr = ' data-shipping="'.((int)$tax->shipping).'"';
?>
                                                    <div class="sorting-more-options-wrapper">
                                                        <i class="zmdi zmdi-more show-more-tax-options"<?php echo $attr; ?>></i>
                                                    </div>
                                                </div>
<?php
                                            }
?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="store-order-statuses-options" class="tab-pane">
                                    <div class="ba-options-group-wrapper">
                                        <div class="ba-options-group-element ba-options-group-sorting-wrapper">
                                            <div class="ba-options-group-toolbar">
                                                <div>
                                                    <label data-action="add" data-object="statuses">
                                                        <i class="zmdi zmdi-plus"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-action="delete" class="disabled">
                                                        <i class="zmdi zmdi-delete"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('DELETE'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="sorting-container color-picker-sorting-item">
<?php
                                            foreach ($store->statuses as $status) {
?>
                                                <div class="sorting-item">
                                                    <div class="sorting-icon">
                                                        <i class="zmdi zmdi-more-vert sortable-handle"></i>
                                                    </div>
                                                    <div class="sorting-checkbox">
                                                        <label class="ba-checkbox ba-hide-checkbox">
                                                            <input type="checkbox" data-ind="<?php echo $status->key; ?>">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    <div class="sorting-title">
                                                        <input type="text" value="<?php echo $status->title; ?>">
                                                    </div>
                                                    <div class="sorting-color-picker">
                                                        <div class="minicolors minicolors-theme-bootstrap">
                                                            <input type="text" data-type="color" class="minicolors-input"
                                                                data-rgba="<?php echo $status->color; ?>">
                                                            <span class="minicolors-swatch minicolors-trigger">
                                                                <span class="minicolors-swatch-color"
                                                                    style="background-color: <?php echo $status->color; ?>;"></span>
                                                            </span>
                                                        </div>
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
                    </div>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="ba_view" value="storesettings">
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
<div id="more-tax-options-dialog" class="modal hide ba-modal-picker picker-modal-arrow" style="display: none;">
    <div class="modal-body">
        <div class="picker-modal-options-wrapper">
            <div class="picker-modal-options-row">
                <span class="picker-modal-options-title"><?php echo JText::_('TAX_ON_SHIPPING'); ?></span>
                <label class="ba-checkbox">
                    <input type="checkbox" class="ba-hide-element" data-option="shipping">
                    <span></span>
                </label>
            </div>
        </div>
    </div>
</div>
<div id="data-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">
    <div class="modal-body">
        <div class="data-tags-searchbar">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-select-type">
                    <select class="select-data-tags-type">
                        <option value=""><?php echo JText::_('All'); ?></option>
                        <option value="store"><?php echo JText::_('STORE'); ?></option>
                        <option value="order"><?php echo JText::_('ORDER'); ?></option>
                        <option value="customer"><?php echo JText::_('CUSTOMER'); ?></option>
                        <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                        <option value="subscription"><?php echo JText::_('SUBSCRIPTION'); ?></option>
                        <option value="booking"><?php echo JText::_('BOOKING'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="">
            <div class="ba-settings-group store-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('STORE_NAME'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Store Name]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('STORE_LEGAL_NAME'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Store Legal Business Name]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('STORE_PHONE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Store Phone]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('STORE_EMAIL'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Store Email]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('STORE_ADDRESS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Store Address]">
                </div>
                <div class="ba-settings-item ba-settings-input-type invoice-all-fields" style="display: none;">
                    <span class="ba-settings-item-title"><?php echo JText::_('ALL_FIELDS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[All Fields]">
                </div>
            </div>
            <div class="ba-settings-group order-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ORDER_NUMBER'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Order Number]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ORDER_DATE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Order Date]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ORDER_DETAILS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Order Details]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ORDER_REVIEW'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Order Review]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ORDER_WEIGHT'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Order Weight]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('INVOICE_ATTACHED'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Invoice: Attached]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('TRACKING_CARRIER'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Tracking Carrier]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('TRACKING_NUMBER'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Tracking Number]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('TRACKING_URL'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Tracking URL]">
                </div>
            </div>
            <div class="ba-settings-group customer-data-tags">
<?php
            foreach ($this->customerInfo as $info) {
?>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('CUSTOMER').': '.$info->title; ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Customer ID=<?php echo $info->id; ?>]">
                </div>
<?php
            }
?>
                <div class="ba-settings-item ba-settings-input-type invoice-all-fields" style="display: none;">
                    <span class="ba-settings-item-title"><?php echo JText::_('ALL_FIELDS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[All Fields]">
                </div>
            </div>
            <div class="ba-settings-group product-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_TITLE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product Title]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_SKU'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product SKU]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_QUANTITY'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product Quantity]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('ATTACHED_FILES'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Attached Files]">
                </div>
            </div>
            <div class="ba-settings-group subscription-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('EXPIRATION_DATE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Expiration Date]">
                </div>
            </div>
            <div class="ba-settings-group booking-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('BOOKING_TIME'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Booking Time]">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<div id="cke-image-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('ADD_IMAGE'); ?></h3>
        <div>
            <input type="text" class="cke-upload-image" readonly placeholder="<?php echo JText::_('BROWSE_PICTURE'); ?>">
            <span class="focus-underline"></span>
            <i class="zmdi zmdi-camera"></i>
        </div>
        <input type="text" class="cke-image-alt" placeholder="<?php echo JText::_('IMAGE_ALT'); ?>">
        <span class="focus-underline"></span>
        <div>
            <input type="text" class="cke-image-width" placeholder="<?php echo JText::_('WIDTH'); ?>">
            <span class="focus-underline"></span>
            <input type="text" class="cke-image-height" placeholder="<?php echo JText::_('HEIGHT'); ?>">
            <span class="focus-underline"></span>
        </div>
        <div class="ba-custom-select visible-select-top cke-image-select">
            <input type="text" class="cke-image-align" data-value="" readonly=""
                placeholder="<?php echo JText::_('ALIGNMENT'); ?>">
            <ul class="select-no-scroll">
                <li data-value=""><?php echo JText::_('NONE_SELECTED'); ?></li>
                <li data-value="left"><?php echo JText::_('LEFT'); ?></li>
                <li data-value="right"><?php echo JText::_('RIGHT'); ?></li>
            </ul>
            <i class="zmdi zmdi-caret-down"></i>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary" id="add-cke-image">
            <?php echo JText::_('JTOOLBAR_APPLY') ?>
        </a>
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
<div id="resized-ckeditor-dialog" class="ba-modal-lg modal hide" style="display: none;" aria-hidden="true">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('EMAIL_EDITOR'); ?></span>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check set-resized-ckeditor-data"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <textarea data-key="resized"></textarea>
    </div>
</div>
<div id="add-email-notification-modal" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('EMAIL_NOTIFICATION'); ?></h3>
        <i class="zmdi zmdi-check apply-email-notification"></i>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label">
                    <?php echo JText::_('JGLOBAL_TITLE'); ?>
                </label>
                <input type="text" data-key="title">
            </div>
            <div class="ba-options-group-element notification-recipient-option">
                <label class="ba-options-group-label">
                    <?php echo JText::_('RECIPIENT'); ?>
                </label>
                <select data-key="type">
                    <option value="admin"><?php echo JText::_('ADMIN'); ?></option>
                    <option value="customer"><?php echo JText::_('CUSTOMER'); ?></option>
                </select>
            </div>
            <div class="ba-options-group-element notification-status-option">
                <label class="ba-options-group-label">
                    <?php echo JText::_('JSTATUS'); ?>
                </label>
                <div class="ba-custom-select email-notification-status-select">
                    <input readonly onfocus="this.blur()" type="text">
                    <input type="hidden" data-key="status">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
<?php
                    foreach ($store->statuses as $status) {
                        if ($key == 'undefined') {
                            continue;
                        }
?>
                        <li data-value="<?php echo $status->key; ?>" data-color="<?php echo $status->color; ?>"
                            style="--status-color: <?php echo $status->color; ?>;">
                            <?php echo $status->title; ?>
                        </li>
<?php
                    }
?>
                    </ul>
                </div>
            </div>
            <div class="ba-options-group-element notification-stock-options">
                <label class="ba-options-group-label"><?php echo JText::_('MINIMUM_STOCK_QUANTITY'); ?></label>
                <input type="text" class="integer-validation" data-decimals="0" data-key="quantity">
            </div>
            <div class="ba-options-group-element notification-appointment-reminder-options">
                <label class="ba-options-group-label"><?php echo JText::_('SEND_BEFORE_THE_APPOINTMENT'); ?></label>
                <input type="number" data-key="value" data-group="reminder">
                <select data-key="format" data-group="reminder">
                    <option value="h"><?php echo JText::_('HOURS'); ?></option>
                    <option value="d"><?php echo JText::_('DAYS'); ?></option>
                </select>
            </div>
        </div>
        <div class="ba-options-group-wrapper email-sending-delay-options-wrapper">
            <div class="ba-options-group-element toggle-button-wrapper email-sending-delay-checkbox">
                <label class="ba-options-group-label"><?php echo JText::_('EMAIL_SENDING_DELAY'); ?></label>
                <label class="ba-checkbox">
                    <input type="checkbox" data-key="enabled" data-group="delay">
                    <span></span>
                </label>
            </div>
            <div class="ba-options-group-element email-sending-delay-options">
                <input type="number" data-key="value" data-group="delay">
                <select data-key="format" data-group="delay">
                    <option value="h"><?php echo JText::_('HOURS'); ?></option>
                    <option value="d"><?php echo JText::_('DAYS'); ?></option>
                    <option value="m"><?php echo JText::_('MONTHS'); ?></option>
                    <option value="y"><?php echo JText::_('YEARS'); ?></option>
                </select>
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('EMAIL'); ?></span>
            </div>
            <div class="ba-options-group-element customer-email-options">
                <label class="ba-options-group-label">
                    <?php echo JText::_('FROM_NAME'); ?>
                </label>
                <input type="text" data-key="name" placeholder="<?php echo JText::_('STORE_NAME'); ?>">
            </div>
            <div class="ba-options-group-element customer-email-options">
                <label class="ba-options-group-label">
                    <?php echo JText::_('FROM_EMAIL'); ?>
                </label>
                <input type="text" data-key="email" placeholder="store@noreply.com">
            </div>
            <div class="ba-options-group-element admin-email-options">
                <label class="ba-options-group-label">
                    <?php echo JText::_('ADMIN_EMAILS_ADDRESSES'); ?>
                </label>
                <input type="text" class="ba-add-email-action"
                    placeholder="<?php echo JText::_('ADD_EMAIL_AND_PRESS_ENTER'); ?>">
                <div class="entered-emails-wrapper selected-items-wrapper" data-key="admins">

                </div>
            </div>
            <div class="ba-options-group-element ba-options-input-action-element notification-subject-option">
                <label class="ba-options-group-label"><?php echo JText::_('EMAIL_SUBJECT'); ?></label>
                <div class="ba-options-input-action-wrapper">
                    <input type="text" data-key="subject">
                    <div class="select-data-tags input-action-icon">
                        <i class="zmdi zmdi-playlist-plus"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"
                            ><?php echo JText::_('DATA_TAGS'); ?></span>
                    </div>
                </div>
            </div>
            <div class="ba-options-group-element full-width-group-element ckeditor-options-wrapper">
                <textarea data-key="body"></textarea>
            </div>
        </div>
    </div>
</div>
<div id="add-currency-modal" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('CURRENCY'); ?></h3>
        <i class="zmdi zmdi-check apply-currency"></i>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('GENERAL'); ?></span>
            </div>
            <div class="ba-options-group-element full-width-group-element">
                <label class="ba-options-group-label">
                    <?php echo JText::_('JGLOBAL_TITLE'); ?>
                </label>
                <input type="text" data-key="title">
            </div>
            
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('CODE'); ?></label>
                <input type="text" data-key="code">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('SYMBOL'); ?></label>
                <input type="text" data-key="symbol">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('POSITION'); ?></label>
                <select data-key="position">
                    <option value=""><?php echo JText::_('LEFT'); ?></option>
                    <option value="right-currency-position"><?php echo JText::_('RIGHT'); ?></option>
                </select>
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('CURRENCY_SEPARATOR'); ?></span>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('THOUSAND_SEPARATOR'); ?></label>
                <select data-key="thousand">
                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                    <option value="."><?php echo JText::_('DOT'); ?></option>
                    <option value=" "><?php echo JText::_('SPACE'); ?></option>
                    <option value=""><?php echo JText::_('NONE_SELECTED'); ?></option>
                </select>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('DECIMAL_SEPARATOR'); ?></label>
                <select data-key="separator">
                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                    <option value="."><?php echo JText::_('DOT'); ?></option>
                </select>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('NUMBER_OF_DECIMALS'); ?></label>
                <input type="number" data-key="decimals">
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('MULTICURRENCY'); ?></span>
            </div>
            <div class="ba-options-group-element exchange-rate">
                <label class="ba-options-group-label"><?php echo JText::_('EXCHANGE_RATE'); ?></label>
                <input type="text" class="integer-validation" data-key="rate" data-decimals="2">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('ASSIGN_TO_LANGUAGE'); ?></label>
                <select data-key="language">
<?php
                foreach ($this->languages as $key => $language) {
?>
                    <option value="<?php echo $key; ?>"><?php echo $language; ?></option>
<?php
                }
?>
                </select>
            </div>
        </div>
    </div>
</div>
<div id="business-info-modal" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('BUSINESS_INFO'); ?></h3>
        <i class="zmdi zmdi-check apply-store-settings-modal"></i>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('GENERAL_INFO'); ?></span>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('STORE_NAME'); ?></label>
                <input type="text" data-key="store_name" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('LEGAL_BUSINESS_NAME'); ?></label>
                <input type="text" data-key="business_name" data-group="general">
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-header-wrapper">
                <span class="ba-options-group-header"><?php echo JText::_('CONTACT_INFO'); ?></span>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('PHONE'); ?></label>
                <input type="text" data-key="phone" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('EMAIL'); ?></label>
                <input type="text" data-key="email" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('COUNTRY'); ?></label>
                <input type="text" data-key="country" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('STATE_PROVINCE'); ?></label>
                <input type="text" data-key="region" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('CITY'); ?></label>
                <input type="text" data-key="city" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label">
                    <?php echo JText::_('STREET_ADDRESS'); ?>
                </label>
                <input type="text" data-key="street" data-group="general">
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('ZIP_CODE'); ?></label>
                <input type="text" data-key="zip_code" data-group="general">
            </div>
        </div>
    </div>
</div>
<div id="invoice-modal" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('INVOICE'); ?></h3>
        <i class="zmdi zmdi-check apply-store-settings-modal"></i>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-element full-width-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('LOGO'); ?></label>
                <input type="text" data-key="logo" data-group="invoice">
                <div class="set-invoice-logo input-action-icon">
                    <i class="zmdi zmdi-camera"></i>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT'); ?></span>
                </div>
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('FROM'); ?></label>
                <textarea data-key="from" data-group="invoice"></textarea>
                <div class="select-invoice-data-tags input-action-icon" data-target="store">
                    <i class="zmdi zmdi-playlist-plus"></i>
                    <span class="ba-tooltip ba-top ba-hide-element">Data Tags</span>
                </div>
            </div>
            <div class="ba-options-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('BILLED_TO'); ?></label>
                <textarea data-key="billed" data-group="invoice"></textarea>
                <div class="select-invoice-data-tags input-action-icon" data-target="customer">
                    <i class="zmdi zmdi-playlist-plus"></i>
                    <span class="ba-tooltip ba-top ba-hide-element">Data Tags</span>
                </div>
            </div>
        </div>
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-element full-width-group-element">
                <label class="ba-options-group-label"><?php echo JText::_('FOOTER'); ?></label>
                <textarea data-key="footer" data-group="invoice"></textarea>
            </div>
        </div>
    </div>
</div>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<div id="acceptance-html-modal" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('ACCEPTANCE'); ?></h3>
        <i class="zmdi zmdi-check apply-acceptance-html"></i>
        <i data-dismiss="modal" class="zmdi zmdi-close"></i>
    </div>
    <div class="modal-body">
        <div class="ba-options-group-wrapper">
            <div class="ba-options-group-element full-width-group-element ckeditor-options-wrapper">
                <textarea data-key="acceptance"></textarea>
            </div>
        </div>
    </div>
</div>
<template class="empty-notification">
<?php
    $dir = JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/store-options/';
    echo gridboxHelper::readFile($dir.'empty-notification.html');
?>
</template>
<script>
    app.currencies = <?php echo json_encode($currencies); ?>;
    app.exchangerates_data = <?php echo $this->integrations->exchangerates_data->key; ?>;
    app.exchangerates_key = '<?php echo $this->integrations->exchangerates->key; ?>';
    app.notifications = <?php echo json_encode($notifications); ?>;
    app.statuses = <?php echo json_encode($this->statuses); ?>;
    app.systemStatuses = <?php echo json_encode($this->systemStatuses); ?>;
    app.store = <?php echo json_encode(gridboxHelper::$store); ?>;
    app.added = <?php echo json_encode($added) ?>
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/countries-modal.php');
include(JPATH_COMPONENT.'/views/layouts/color-variables-dialog.php');