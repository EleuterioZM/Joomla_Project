<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="seo-default-settings-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="order-status-tabs-wrapper">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#seo-default-settings-general" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#seo-default-settings-sharing" data-toggle="tab">
                        <i class="zmdi zmdi-share"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SHARING'); ?></span>
                    </a>
                </li>
<?php
            if (gridboxHelper::checkSystemApp('sitemap')) {
?>
                <li>
                    <a href="#seo-default-settings-sitemap" data-toggle="tab">
                        <i class="zmdi zmdi-device-hub"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SITEMAP'); ?></span>
                    </a>
                </li>
<?php
            }
?>
                <li>
                    <a href="#seo-default-settings-schema-markup" data-toggle="tab">
                        <i class="zmdi zmdi-code"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SCHEMA_MARKUP'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="seo-default-settings-general">
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('BROWSER_PAGE_TITLE'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <input type="hidden" data-key="id">
                            <input type="hidden" data-key="item_id">
                            <input type="hidden" data-key="item_type">
                            <input type="text" data-key="meta_title" placeholder="<?php echo JText::_('BROWSER_PAGE_TITLE'); ?>">
                        </div>
                    </div>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <textarea data-key="meta_description" placeholder="<?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="seo-default-settings-sharing">
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('IMAGE'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <input type="text" data-key="share_image" placeholder="<?php echo JText::_('IMAGE'); ?>">
                        </div>
                    </div>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <input type="text" data-key="share_title" placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                        </div>
                    </div>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('DESCRIPTION'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <textarea data-key="share_description" placeholder="<?php echo JText::_('DESCRIPTION'); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="seo-default-settings-sitemap">
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('INCLUDE_ITEM'); ?>
                                </label>
                            </div>
                            <div class="ba-custom-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden" data-key="sitemap_include">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="1"><?php echo JText::_('INCLUDE'); ?></li>
                                    <li data-value="0"><?php echo JText::_('EXCLUDE'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    Changefreq
                                </label>
                            </div>
                            <div class="ba-custom-select ba-fixed-custom-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden" data-key="changefreq">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="always"><?php echo JText::_('ALWAYS'); ?></li>
                                    <li data-value="hourly"><?php echo JText::_('HOURLY'); ?></li>
                                    <li data-value="daily"><?php echo JText::_('DAILY'); ?></li>
                                    <li data-value="weekly"><?php echo JText::_('WEEKLY'); ?></li>
                                    <li data-value="monthly"><?php echo JText::_('MONTHLY'); ?></li>
                                    <li data-value="yearly"><?php echo JText::_('YEARLY'); ?></li>
                                    <li data-value="never"><?php echo JText::_('NEVER'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    Priority
                                </label>
                            </div>
                            <div class="ba-custom-select ba-fixed-custom-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden" data-key="priority">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="0">0</li>
                                    <li data-value="0.1">0.1</li>
                                    <li data-value="0.2">0.2</li>
                                    <li data-value="0.3">0.3</li>
                                    <li data-value="0.4">0.4</li>
                                    <li data-value="0.5">0.5</li>
                                    <li data-value="0.6">0.6</li>
                                    <li data-value="0.7">0.7</li>
                                    <li data-value="0.8">0.8</li>
                                    <li data-value="0.9">0.9</li>
                                    <li data-value="1">1</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="seo-default-settings-schema-markup">
                    <div class="ba-options-group schema-markup-wrapper">
                        <div class="ba-group-element">
                            <div class="schema-markup-label">
                                <label>
                                    <?php echo JText::_('JSON_LD_SCHEMA_MARKUP'); ?>
                                </label>
                                <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <textarea data-key="schema_markup"></textarea>
                        </div>
                    </div>
                </div>
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
        <a href="#" class="ba-btn-primary apply-seo-default-settings active-button">
            <?php echo JText::_('JTOOLBAR_APPLY') ?>
        </a>
<?php
    }
?>
    </div>
</div>
<div class="ba-context-menu options-context-menu" data-source="gridbox-options" style="display: none">
    <span class="export-gridbox">
        <i class="zmdi zmdi-download "></i>
        <span class="ba-context-menu-title">
            <?php echo JText::_('EXPORT'); ?>
        </span>
    </span>
    <span class="import-gridbox">
        <i class="zmdi zmdi-upload"></i>
        <span class="ba-context-menu-title">
            <?php echo JText::_('IMPORT'); ?>
        </span>
    </span>
    <span class="import-joomla-content">
        <i class="zmdi zmdi-inbox"></i>
        <span class="ba-context-menu-title">
            <?php echo JText::_('IMPORT_JOOMLA_CONTENT'); ?>
        </span>
    </span>
    <span class="context-menu-item-link ba-group-element">
        <a href="<?php echo $this->preferences(); ?>" class="default-action">
            <i class="zmdi zmdi-accounts"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?>
            </span>
        </a>
    </span>
    <span class="context-menu-item-link ba-group-element">
        <a href="index.php?option=com_gridbox&view=integrations" class="default-action">
            <i class="zmdi zmdi-cloud-done"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('INTEGRATIONS'); ?>
            </span>
        </a>
    </span>
</div>
<div class="ba-context-menu store-context-menu" data-source="gridbox-store" style="display: none">
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=orders" class="default-action">
            <i class="zmdi zmdi-shopping-cart"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('ORDERS'); ?>
            </span>
<?php
        if ($ordersCount > 0) {
?>
            <span class="unread-comments-count" data-type="orders"><?php echo $ordersCount; ?></span>
<?php
        }
?>            
        </a>
    </span>
<?php
if ($installedBooking) {
?>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=bookingcalendar" class="default-action">
            <i class="zmdi zmdi-calendar-note"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('CALENDAR'); ?>
            </span>
<?php
        if ($bookingCount > 0) {
?>
            <span class="unread-comments-count" data-type="booking"><?php echo $bookingCount; ?></span>
<?php
        }
?> 
        </a>
    </span>
<?php
}
if ($hasSubscriptions) {
?>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=subscriptions" class="default-action">
            <i class="zmdi zmdi-time-restore"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('SUBSCRIPTIONS'); ?>
            </span>
        </a>
    </span>
<?php
}
?>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=paymentmethods" class="default-action">
            <i class="zmdi zmdi-card"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('PAYMENT_METHODS'); ?>
            </span>
        </a>
    </span>
<?php
if ($installedStore) {
?>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=shipping" class="default-action">
            <i class="zmdi zmdi-truck"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('SHIPPING'); ?>
            </span>
        </a>
    </span>
<?php
}
?>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=promocodes" class="default-action">
            <i class="zmdi zmdi-card-giftcard"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('PROMO_CODES'); ?>
            </span>
        </a>
    </span>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=sales" class="default-action">
            <i class="zmdi zmdi-bookmark"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('DISCOUNTS'); ?>
            </span>
        </a>
    </span>
    <span class="context-menu-item-link">
        <a href="index.php?option=com_gridbox&view=productoptions" class="default-action">
            <i class="zmdi zmdi-invert-colors"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('PRODUCT_OPTIONS'); ?>
            </span>
        </a>
    </span>
    <span class="context-menu-item-link ba-group-element">
        <a href="index.php?option=com_gridbox&view=storesettings" class="default-action">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-context-menu-title">
                <?php echo JText::_('SETTINGS'); ?>
            </span>
        </a>
    </span>
</div>
<div id="languages-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <div class="languages-wrapper">

        </div>
    </div>
</div>
<div id="import-joomla-content-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="ba-modal-header">
            <h3><?php echo JText::_('SELECT_APP_TO_IMPORT_ARTICLES'); ?></h3>
            <i data-dismiss="modal" class="zmdi zmdi-close"></i>
        </div>
        <div class="availible-folders">
            <ul class="root-list">
                
            </ul>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary apply-import-joomla-content">
            <?php echo JText::_('IMPORT') ?>
        </a>
    </div>
</div>
<div id="import-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('IMPORT'); ?></h3>
        <label class="ba-help-icon">
            <i class="zmdi zmdi-help"></i>
            <span class="ba-tooltip ba-help ba-hide-element">
                <?php echo JText::_('IMPORT_PAGES_THEMES_TOOLTIP'); ?> 
            </span>
        </label>
    </div>
    <div class="modal-body">
        <div class="ba-input-lg">
            <input id="theme-import-trigger" class="theme-import-trigger" readonly
                type="text" placeholder="<?php echo JText::_('SELECT'); ?>">
            <i class="zmdi zmdi-attachment-alt theme-import-trigger"></i>
            <input type="file" id="theme-import-file" name="ba-files[]" style="display: none;">
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary apply-import">
            <?php echo JText::_('INSTALL') ?>
        </a>
    </div>
</div>
<input type="hidden" id="installing-const" value="<?php echo JText::_('INSTALLING'); ?>">