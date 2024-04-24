<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$currency = gridboxHelper::$store->currency;
$thousand = $currency->thousand;
$separator = $currency->separator;
$decimals = $currency->decimals;
$position = $currency->position;
$symbol = $currency->symbol;
$wishlist = $this->wishlist;
$lang = JFactory::getLanguage();
$lang->load('com_users');
$hasSubmitted = false;
?>
<script>
    let statuses = <?php echo json_encode($this->statuses); ?>,
        currency = <?php echo json_encode($currency); ?>,
        customer = <?php echo json_encode($this->customer); ?>;
</script>
<div class="ba-account-wrapper">
    <ul class="nav nav-tabs">
<?php
    $i = 0;
    foreach ($this->submitted as $app) {
        $hasSubmitted = true;
?>
        <li class="<?php echo $i == 0 ? 'active' : '' ?>">
            <a href="#ba-submitted-app-<?php echo $app->id; ?>" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo $app->title; ?></span>
                    <i class="zmdi zmdi-file"></i>
                </span>
            </a>
        </li>
<?php
        $i++;
    }
?>
        <li class="<?php echo !$hasSubmitted ? 'active' : '' ?>">
            <a href="#ba-my-account-orders" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_ORDERS'); ?></span>
                    <i class="ba-icons ba-icon-shopping-basket"></i>
                </span>
            </a>
        </li>
<?php
    if (!empty($this->subscriptions->items)) {
?>
        <li>
            <a href="#ba-my-account-subscriptions" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_SUBSCRIPTIONS'); ?></span>
                    <i class="zmdi zmdi-time-restore"></i>
                </span>
            </a>
        </li>
<?php
    }
    if (!empty($this->digital->products)) {
?>
        <li>
            <a href="#ba-my-account-downloads" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_DOWNLOADS'); ?></span>
                    <i class="zmdi zmdi-cloud-download"></i>
                </span>
            </a>
        </li>
<?php
    }
    if (gridboxHelper::$store->wishlist->login) {
?>
        <li>
            <a href="#ba-my-account-wishlist" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_WISHLIST'); ?></span>
                    <i class="ba-icons ba-icon-heart"></i>
                </span>
            </a>
        </li>
<?php
    }
?>
        <li>
            <a href="#ba-my-account-billing-details" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_ADDRESS'); ?></span>
                    <i class="ba-icons ba-icon-truck"></i>
                </span>
            </a>
        </li>
        <li>
            <a href="#ba-my-account-profile" data-toggle="tab">
                <span>
                    <span class="tabs-title"><?php echo JText::_('MY_ACCOUNT'); ?></span>
                    <i class="ba-icons ba-icon-settings"></i>
                </span>
            </a>
        </li>
        <li>
            <a href="<?php echo JUri::root().'index.php?option=com_gridbox&view=editor&task=store.logout'; ?>">
                <span>
                    <span class="tabs-title"><?php echo JText::_('LOG_OUT'); ?></span>
                    <i class="ba-icons ba-icon-power"></i>
                </span>
            </a>
        </li>
    </ul>
    <div class="tab-content">
<?php
    $i = 0;
    foreach ($this->submitted as $app) {
?>
        <div class="tab-pane <?php echo $i == 0 ? 'active' : '' ?>" id="ba-submitted-app-<?php echo $app->id; ?>">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo $app->title; ?></span>
            </div>
            <div class="ba-account-table">
                <div class="ba-account-tbody submitted-items-list">
<?php
                foreach ($app->items as $item) {
                    
                    $view = gridboxHelper::getGridboxPageLinks($item->id, 'zero', $app->id, $item->page_category);
                    $image = gridboxHelper::prepareIntroImage($item->intro_image);
                    $date = explode(' ', $item->created);
                    if (!empty($image) && !gridboxHelper::isExternal($image)) {
                        $image = JUri::root().$image;
                    }
?>
                    <div class="ba-account-tr" data-id="<?php echo $item->id; ?>">
                        <div class="ba-account-td">
<?php
                        if (!empty($image)) {
?>
                            <span class="post-intro-image" style="background-image: url(<?php echo $image; ?>);"></span>
<?php
                        }
?>
                            <a href="<?php echo JRoute::_($view); ?>" target="_blank"><?php echo $item->title; ?></a>
                        </div>
                        <div class="ba-account-td">
                            <span><?php echo $item->category_title; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span><?php echo $date[0]; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span class="sumitted-item-icons">
<?php
                            if (isset($item->submission_form)) {
                                $link = gridboxHelper::getGridboxSystemLinks($item->submission_form).'&page_id='.$item->id;
?>
                                <span>
                                    <a class="zmdi zmdi-edit" href="<?php echo JRoute::_($link); ?>" target="_blank"></a>
                                    <span class="ba-tooltip ba-bottom"><?php echo JText::_('EDIT'); ?></span>
                                </span>
<?php
                            }
?>
                                <span>
                                    <i class="zmdi zmdi-delete delete-item"></i>
                                    <span class="ba-tooltip ba-bottom"><?php echo JText::_('DELETE'); ?></span>
                                </span>
                            </span>
                        </div>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
<?php
        $i++;
    }
?>
        <div class="tab-pane <?php echo !$hasSubmitted ? 'active' : '' ?>" id="ba-my-account-orders">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_ORDERS'); ?></span>
            </div>
            <div class="ba-account-table">
                <div class="ba-account-tbody">
<?php
                if (count($this->orders) == 0) {
?>
                    <div class="ba-empty-cart-products">
                        <span class="ba-empty-cart-products-message"><?php echo JText::_('NO_ORDERS_HAVE_BEEN_FOUND'); ?></span>
                    </div>
<?php
                }
                foreach ($this->orders as $order) {
                    $date = JHtml::date($order->date, gridboxHelper::$dateFormat);
                    $price = gridboxHelper::$storeHelper->preparePrice($order->total, $thousand, $separator, $decimals);
                    $status = isset($this->statuses->{$order->status}) ? $this->statuses->{$order->status} : $this->statuses->undefined;
?>
                    <div class="ba-account-tr" data-id="<?php echo $order->id; ?>">
                        <div class="ba-account-td">
                            <span><?php echo $date; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span><?php echo $order->order_number; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span style="--status-color: <?php echo $status->color; ?>"><?php echo $status->title; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span class="ba-account-price-wrapper <?php echo $order->currency_position; ?>">
                                <span class="ba-account-price-currency"><?php echo $order->currency_symbol; ?></span>
                                <span class="ba-account-price-value"><?php echo $price; ?></span>
                            </span>
                        </div>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
<?php
    if (!empty($this->subscriptions->items)) {
        $checkout = gridboxHelper::getStoreSystemUrl('checkout');
?>
        <div class="tab-pane" id="ba-my-account-subscriptions" data-checkout="<?php echo $checkout; ?>">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_SUBSCRIPTIONS'); ?></span>
            </div>
            <div class="ba-account-table">
                <div class="ba-account-thead">
                    <div class="ba-account-tr">
                        <div class="ba-account-td">
                            <span><?php echo JText::_('SUBSCRIPTION'); ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span><?php echo JText::_('STATUS'); ?></span>
                        </div>
<?php
                    if ($this->subscriptions->expires) {
?>
                        <div class="ba-account-td ba-expire-td">
                            <span><?php echo JText::_('EXPIRES'); ?></span>
                        </div>
<?php
                    }
                    if ($this->subscriptions->renew) {
?>
                        <div class="ba-account-td"></div>
<?php
                    }
                    if ($this->subscriptions->upgrade) {
?>
                        <div class="ba-account-td"></div>
<?php
                    }
?>
                    </div>
                </div>
                <div class="ba-account-tbody">
<?php
                $now = date('Y-m-d H:i:s');
                foreach ($this->subscriptions->items as $item) {
                    if ($item->refunded) {
                        continue;
                    }
                    $status = empty($item->expires) || $now < $item->expires ? 'ACTIVE' : 'EXPIRED';
?>
                    <div class="ba-account-tr">
                        <div class="ba-account-td">
                            <span><?php echo $item->title; ?></span>
                        </div>
                        <div class="ba-account-td">
                            <span data-status="<?php echo strtolower($status); ?>"><?php echo JText::_($status); ?></span>
                        </div>
<?php
                    if ($this->subscriptions->expires) {
                        $expire = !empty($item->expires) ? gridboxHelper::formatDate($item->expires) : '';
?>
                        <div class="ba-account-td ba-expire-td">
                            <span><?php echo $expire; ?></span>
                        </div>
<?php
                    }
                    if ($this->subscriptions->renew) {
?>
                        <div class="ba-account-td ba-renew-td">
<?php
                        if (!empty($item->plans)) {
?>
                            <span class="ba-account-btn ba-renew-subscription"
                                data-product="<?php echo $item->product_id; ?>" data-id="<?php echo $item->id ?>">
                                <?php echo JText::_('RENEW'); ?>
                            </span>
                            <template><?php echo json_encode($item->plans); ?></template>
<?php
                        }
?>
                        </div>
<?php
                    }
                    if ($this->subscriptions->upgrade) {
?>
                        <div class="ba-account-td">
<?php
                        if (!empty($item->upgrade_plans)) {
?>
                            <span class="ba-upgrade-subscription"
                                data-id="<?php echo $item->id ?>">
                                <?php echo JText::_('CHANGE_PLAN'); ?>
                            </span>
                            <template><?php echo json_encode($item->upgrade_plans); ?></template>
<?php
                        }
?>
                        </div>
<?php
                    }
?>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
<?php
    }
    if (!empty($this->digital->products)) {
?>
        <div class="tab-pane" id="ba-my-account-downloads">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_DOWNLOADS'); ?></span>
            </div>
            <div class="ba-account-table">
                <div class="ba-account-thead">
                    <div class="ba-account-tr">
                        <div class="ba-account-td">
                            <span><?php echo JText::_('PRODUCT'); ?></span>
                        </div>
<?php
                    if (!empty($this->digital->expires)) {
?>
                        <div class="ba-account-td ba-expire-td">
                            <span><?php echo JText::_('EXPIRES'); ?></span>
                        </div>
<?php
                    }
                    if (!empty($this->digital->limit)) {
?>
                        <div class="ba-account-td">
                            <span><?php echo JText::_('REMAINING'); ?></span>
                        </div>
<?php
                    }
?>
                        <div class="ba-account-td"></div>
                    </div>
                </div>
                <div class="ba-account-tbody">
<?php
                foreach ($this->digital->products as $product) {
                    if (!empty($product->license->expires)) {
                        $expire = gridboxHelper::formatDate($product->license->expires);
                    } else  {
                        $expire = '-';
                    }
                    $remaining = !empty($product->license->limit) ? $product->license->downloads.' / '.$product->license->limit : '-';
?>
                    <div class="ba-account-tr">
                        <div class="ba-account-td">
<?php
                        if (!empty($product->image)) {
                            $image = (!gridboxHelper::isExternal($product->image) ? JUri::root() : '').$product->image;
?>
                            <span class="ba-account-product-image">
                                <img src="<?php echo $image; ?>">
                            </span>
<?php
                        }
?>
                            <span><?php echo $product->title; ?></span>
                        </div>
<?php
                    if (!empty($this->digital->expires)) {
?>
                        <div class="ba-account-td ba-expire-td">
                            <span><?php echo $expire; ?></span>
                        </div>
<?php
                    }
                    if (!empty($this->digital->limit)) {
?>
                        <div class="ba-account-td">
                            <span><?php echo $remaining; ?></span>
                        </div>
<?php
                    }
?>
                        <div class="ba-account-td">
                            <a class="ba-account-btn" href="<?php echo $product->link; ?>">
                                <?php echo JText::_('DOWNLOAD'); ?>
                            </a>
                        </div>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
<?php
    }
    if (gridboxHelper::$store->wishlist->login) {
?>
        <div class="tab-pane" id="ba-my-account-wishlist">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_WISHLIST'); ?></span>
            </div>
            <div class="ba-my-account-wishlist">
<?php
            include_once JPATH_ROOT.'/components/com_gridbox/helpers/uploader.php';
            $uploader = new uploaderHelper();
            include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/store/wishlist-products-list.php';
?>
            </div>
        </div>
<?php
    }
?>
        <div class="tab-pane" id="ba-my-account-billing-details">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_ADDRESS'); ?></span>
                <span class="ba-account-btn save-user-customer-info"><?php echo JText::_('SAVE'); ?></span>
            </div>
            <div class="ba-my-account-billing-details">
<?php
                $out = gridboxHelper::getCustomerInfoHTML(0, null, true);
                echo $out;
?>
            </div>
        </div>
        <div class="tab-pane" id="ba-my-account-profile">
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('MY_ACCOUNT'); ?></span>
                <span class="ba-account-btn save-user-profile-data"><?php echo JText::_('SAVE'); ?></span>
            </div>
            <div class="ba-my-account-profile">
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:50%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('NAME'); ?></span>
                        <span class="ba-account-profile-required-star">*</span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="text" name="name" value="<?php echo $this->user->name; ?>">
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:50%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="text" name="username" value="<?php echo $this->user->username; ?>" readonly>
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:50%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title">
                            <?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL'); ?>
                        </span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="password" name="password1" autocomplete="new-password">
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:50%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title">
                            <?php echo JText::_('COM_USERS_PROFILE_PASSWORD2_LABEL'); ?>
                        </span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="password" name="password2" autocomplete="new-password">
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:100%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('COM_USERS_PROFILE_EMAIL1_LABEL'); ?></span>
                        <span class="ba-account-profile-required-star">*</span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="text" name="email1" value="<?php echo $this->user->email; ?>">
                    </div>
                </div>
            </div>
<?php
        if ($this->author) {
            $img = '';
            if (!empty($this->author->avatar)) {
                $img = 'background-image: url('.JUri::root().$this->author->avatar.');';
            }
?>
            <div class="ba-account-title-wrapper">
                <span class="ba-account-title"><?php echo JText::_('AUTHOR_INFO'); ?></span>
            </div>
            <div class="ba-author-info">
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:100%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('NAME'); ?></span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <input type="text" name="title" value="<?php echo $this->author->title; ?>">
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:100%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('PROFILE_PICTURE'); ?></span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <div class="image-profile-wrapper">
                            <input type="text" name="image" placeholder="<?php echo JText::_('SELECT'); ?>" readonly
                                onfocus="this.blur()" class="select-image-profile"
                                data-value="<?php echo $this->author->avatar; ?>"
                                value="<?php echo !empty($this->author->avatar) ? basename($this->author->avatar) : ''; ?>">
                            <i class="zmdi zmdi-camera"></i>
                            <div class="image-field-tooltip" style="<?php echo $img; ?>"></div>
                            <div class="reset-image-profile">
                                <i class="zmdi zmdi-close"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ba-account-profile-fields" style="--ba-checkout-field-width:100%;">
                    <div class="ba-account-profile-title-wrapper">
                        <span class="ba-account-profile-title"><?php echo JText::_('DESCRIPTION'); ?></span>
                    </div>
                    <div class="ba-account-profile-field-wrapper">
                        <textarea name="description"><?php echo $this->author->description; ?></textarea>
                    </div>
                </div>
            </div>
<?php
        }
?>
        </div>
    </div>
</div>
<div class="ba-account-order-details-backdrop ba-hidden-order-details">
    <div class="ba-account-order-details-wrapper">
        <div class="ba-account-order-details">
            <div class="ba-account-order-header-wrapper">
                <div class="ba-account-order-header">
                    <span class="ba-account-order-number"></span>
                    <span class="ba-account-order-status"></span>
                </div>
                <div class="ba-account-order-header">
                    <span class="ba-account-order-date"></span>
                    <span class="ba-account-order-icons-wrapper">
                        <span class="ba-account-order-icon-wrapper">
                            <i class="ba-icons ba-icon-download ba-btn-transition" data-layout="pdf"></i>
                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('DOWNLOAD') ?></span>
                        </span>
                        <span class="ba-account-order-icon-wrapper">
                            <i class="ba-icons ba-icon-print ba-btn-transition" data-layout="print"></i>
                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('PRINT') ?></span>
                        </span>
                    </span>
                    <i class="ba-icons ba-icon-close ba-btn-transition ba-account-close-order-details"></i>
                </div>
            </div>
            <div class="ba-account-order-customer-info">
                <div class="ba-account-order-body">
                    
                </div>
            </div>
            <div class="ba-account-order-info">
                <div class="ba-account-order-body">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="ba-account-modal-backdrop ba-hidden-account-modal" data-type="renewal">
    <div class="ba-account-md-modal-wrapper">
        <div class="ba-account-modal">
            <div class="ba-account-modal-header">
                <span class="ba-account-modal-title"><?php echo JText::_('RENEWAL') ?></span>
                <i class="ba-icons ba-icon-close ba-btn-transition ba-account-modal-close"></i>
            </div>
            <div class="ba-account-modal-body">
                
            </div>
            <div class="ba-account-modal-footer">
                <span class="ba-account-modal-footer-btn ba-subscription-renewal-btn"><?php echo JText::_('RENEW') ?></span>
            </div>
        </div>
    </div>
</div>
<div class="ba-account-modal-backdrop ba-hidden-account-modal" data-type="upgrade">
    <div class="ba-account-md-modal-wrapper">
        <div class="ba-account-modal">
            <div class="ba-account-modal-header">
                <span class="ba-account-modal-title"><?php echo JText::_('CHANGE_PLAN') ?></span>
                <i class="ba-icons ba-icon-close ba-btn-transition ba-account-modal-close"></i>
            </div>
            <div class="ba-account-modal-body">
                
            </div>
            <div class="ba-account-modal-footer">
                <span class="ba-account-modal-footer-btn ba-subscription-upgrade-btn"><?php echo JText::_('CONTINUE') ?></span>
            </div>
        </div>
    </div>
</div>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3 class="ba-modal-title"><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text"><?php echo JText::_('MODAL_DELETE') ?></p>
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
<template data-key="product-row">
    <div class="ba-account-order-product-row row-fluid" data-extra-count="8">
        <div class="ba-account-order-product-image-cell">
            <img src="">
        </div>
        <div class="ba-account-order-product-content-cell">
            <div class="ba-account-order-product-content-inner-cell">
                <div class="ba-account-order-product-title-cell">
                    <span class="ba-account-order-product-title"></span>
                    <span class="ba-account-order-product-booking">
                        <span class="ba-account-order-product-booking-date"></span>
                        <span class="ba-account-order-product-booking-time"></span>
                        <span class="ba-account-order-product-booking-guests"></span>
                    </span>
                    <span class="ba-account-order-product-info"></span>
                </div>
                <div class="ba-account-order-product-quantity-cell"></div>
                <div class="ba-account-order-product-price-cell">
                    <span class="ba-account-order-price-wrapper">
                        <span class="ba-account-order-price-currency"></span>
                        <span class="ba-cart-price-value"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<template data-key="product-attachment">
    <div class="ba-product-attachment">
        <span class="attachment-image"></span>
        <i class="ba-icons ba-icon-attachment"></i>
        <span class="attachment-title"></span>
    </div>
</template>
<template data-key="extra-options">
    <div class="ba-account-order-product-extra-options">
        <span class="ba-account-order-product-extra-options-title"></span>
        <div class="ba-account-order-product-extra-options-content">
            
        </div>
    </div>
</template>
<template data-key="extra-option">
    <div class="ba-account-order-product-extra-option">
        <span class="ba-account-order-product-extra-option-value"></span>
        <span class="ba-account-order-product-extra-option-price">
            <span class="ba-account-order-price-currency"></span>
            <span class="ba-cart-price-value"></span>
        </span>
    </div>
</template>
<template data-key="order-methods">
    <div class="ba-account-order-methods-wrapper">
        <div class="ba-account-order-shipping-method">
            <span class="ba-account-order-row-title"><?php echo JText::_('SHIPPING'); ?></span>
            <span class="ba-account-order-row-value"></span>
        </div>
        <div class="ba-account-order-payment-method">
            <span class="ba-account-order-row-title"><?php echo JText::_('PAYMENT'); ?></span>
            <span class="ba-account-order-row-value"></span>
        </div>
        <div class="ba-account-booking-payment-method">
            <div class="ba-account-booking-payment-row">
                <span class="ba-account-order-row-title"><?php echo JText::_('PAYMENT'); ?></span>
            </div>
            <div class="ba-account-booking-payment-row" data-type="paid">
                <label class="ba-account-order-row-title"><?php echo JText::_('ALREADY_PAID'); ?></label>
                <div class="ba-account-order-product-price-cell">
                    <span class="ba-account-order-price-wrapper">
                        <span class="ba-account-order-price-currency"></span>
                        <span class="ba-cart-price-value"></span>
                    </span>
                </div>
            </div>
            <div class="ba-account-booking-payment-row" data-type="left">
                <label class="ba-account-order-row-title"><?php echo JText::_('LEFT_TO_PAY'); ?></label>
                <div class="ba-account-order-product-price-cell">
                    <span class="ba-account-order-price-wrapper">
                        <span class="ba-account-order-price-currency"></span>
                        <span class="ba-cart-price-value"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="ba-account-order-coupon-code">
            <span class="ba-account-order-row-title"><?php echo JText::_('COUPON_CODE'); ?></span>
            <span class="ba-account-order-row-value"></span>
        </div>
    </div>
</template>
<template data-key="subtotal">
    <div class="ba-account-order-subtotal-wrapper">
        <div class="ba-account-order-subtotal">
            <span class="ba-account-order-row-title"><?php echo JText::_('SUBTOTAL'); ?></span>
            <div class="ba-account-order-product-price-cell">
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </div>
        </div>
        <div class="ba-account-order-discount">
            <span class="ba-account-order-row-title"><?php echo JText::_('DISCOUNT'); ?></span>
            <div class="ba-account-order-product-price-cell">
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-cart-price-minus">-</span>
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </div>
        </div>
        <div class="ba-account-order-shipping">
            <span class="ba-account-order-row-title"><?php echo JText::_('SHIPPING'); ?></span>
            <div class="ba-account-order-product-price-cell">
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </div>
        </div>
        <div class="ba-account-order-shipping-tax" data-tax="excl">
            <span class="ba-account-order-row-title"><?php echo JText::_('TAX_ON_SHIPPING'); ?></span>
            <div class="ba-account-order-product-price-cell">
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </div>
        </div>
        <div class="ba-account-order-shipping-tax" data-tax="incl">
            <span class="ba-account-order-row-title">
                <span class="ba-account-tax-title"></span>
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </span>
        </div>
        <div class="ba-account-order-tax" data-tax="excl">
            <span class="ba-account-order-row-title"></span>
            <div class="ba-account-order-product-price-cell">
                <span class="ba-account-order-price-wrapper">
                    <span class="ba-account-order-price-currency"></span>
                    <span class="ba-cart-price-value"></span>
                </span>
            </div>
        </div>
    </div>
</template>
<template data-key="total">
    <div class="ba-account-order-total">
        <span class="ba-account-order-row-title"><?php echo JText::_('TOTAL'); ?></span>
        <div class="ba-account-order-product-price-cell">
            <span class="ba-account-order-price-wrapper">
                <span class="ba-account-order-price-currency"></span>
                <span class="ba-cart-price-value"></span>
            </span>
        </div>
    </div>
    <div class="ba-account-order-tax" data-tax="incl">
        <span class="ba-account-order-row-title">
            <span class="ba-account-tax-title"></span>
            <span class="ba-account-order-price-wrapper">
                <span class="ba-account-order-price-currency"></span>
                <span class="ba-cart-price-value"></span>
            </span>
        </span>
    </div>
</template>
<template data-key="info-title">
    <div class="ba-account-order-customer-info-title">
        Contact Information
    </div>
</template>
<template data-key="info-row">
    <div class="ba-account-order-customer-info-row">
        First Name: Vladimir
    </div>
</template>
<template data-key="account-modal-row">
    <div class="ba-account-modal-row">
        <div class="ba-checkbox-wrapper">
            <span class="ba-account-order-price-wrapper">
                <span class="ba-account-order-price-currency"></span>
                <span class="ba-cart-price-value"></span>
            </span>
            <span class="ba-account-modal-row-title"><span class="ba-account-modal-row-title-value"></span></span>
            <label class="ba-radio">
                <input type="radio" name="account-radio">
                <span></span>
            </label>
        </div>
    </div>
</template>