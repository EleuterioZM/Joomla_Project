<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$installedBlog = gridboxHelper::checkInstalledBlog();
$installedStore = gridboxHelper::checkInstalledBlog('products');
$installedBooking = gridboxHelper::checkInstalledBlog('booking');
$installedComments = gridboxHelper::checkSystemApp('comments');
$commentsCount = gridboxHelper::getUnreadCount('#__gridbox_comments');
$installedReviews = gridboxHelper::checkSystemApp('reviews');
$reviewsCount = gridboxHelper::getUnreadCount('#__gridbox_reviews');
$ordersCount = gridboxHelper::getUnreadCount('#__gridbox_store_orders', 'published = 1');
$bookingCount = gridboxHelper::getUnreadCount('#__gridbox_store_bookings', '', true);
$emptySysActions = ($installedBlog || $installedComments || $installedReviews);
$hasSubscriptions = gridboxHelper::checkSubscriptions();
?>
<script src="<?php echo JUri::root() ?>components/com_gridbox/libraries/bootstrap/bootstrap.js?<?php echo $this->about->version; ?>"
     type="text/javascript"></script>
<script type="text/javascript">
var JUri = '<?php echo JUri::root(); ?>',
    IMAGE_PATH = '<?php echo IMAGE_PATH; ?>';
<?php echo gridboxHelper::getGridboxLanguage(); ?>
</script>
<div class="ba-sidebar">
    <div>
        <span class="toggle-sidebar">
            <a href="#">
                <span class="zmdi zmdi-format-clear-all"></span>
            </a>
        </span>
    </div>
    <div>
        <span class="dashboard <?php echo gridboxHelper::checkActive('dashboard'); ?>">
            <a href="index.php?option=com_gridbox">
                <span class="zmdi zmdi-home"></span>
                <span class="sidebar-title"><?php echo JText::_('DASHBOARD'); ?></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('DASHBOARD'); ?></span>
        </span>
        <span class="app-list sidebar-context-parent <?php echo gridboxHelper::checkActive('appslist'); ?>" data-context="apps-list-context-menu">
            <a href="index.php?option=com_gridbox&view=appslist">
                <span class="zmdi zmdi-widgets"></span>
                <span class="sidebar-title"><?php echo JText::_('APPS'); ?></span>
                <i class="zmdi zmdi-caret-right"></i>
            </a>
        </span>
        <span class="gridbox-themes <?php echo gridboxHelper::checkActive('themes'); ?>">
            <a href="index.php?option=com_gridbox&view=themes">
                <span class="zmdi zmdi-format-color-fill"></span>
                <span class="sidebar-title"><?php echo JText::_('THEMES'); ?></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('THEMES'); ?></span>
        </span>
        <div class="ba-system-actions<?php echo $emptySysActions ? '' : ' empty-system-actions'; ?>">
<?php
        if ($installedStore || $installedBooking) {
?>
            <span class="gridbox-store sidebar-context-parent <?php echo gridboxHelper::checkActive('store'); ?>"
                data-context="store-context-menu">
                <a href="index.php?option=com_gridbox&view=orders">
                    <span class="zmdi zmdi-shopping-cart"></span>
                    <span class="sidebar-title"><?php echo JText::_('STORE'); ?></span>
                    <i class="zmdi zmdi-caret-right"></i>
<?php
                if ($ordersCount > 0 || $bookingCount > 0) {
?>
                    <span class="unread-comments-count" data-type="orders"><?php echo $ordersCount + $bookingCount; ?></span>
<?php
                }
?>
                </a>
            </span>
<?php
        }
?>


<?php
        if ($installedComments) {
?>
            <span class="app <?php echo gridboxHelper::checkActive('comments'); ?>">
                <a href="index.php?option=com_gridbox&view=comments">
                    <span class="zmdi zmdi-comment-more"></span>
                    <span class="sidebar-title"><?php echo JText::_('COMMENTS'); ?></span>
<?php
                if ($commentsCount > 0) {
?>
                    <span class="unread-comments-count" data-type="comments"><?php echo $commentsCount; ?></span>
<?php
                }
?>
                </a>
                <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('COMMENTS'); ?></span>
            </span>
<?php
        }
?>
<?php
        if ($installedReviews) {
?>
            <span class="app <?php echo gridboxHelper::checkActive('reviews'); ?>">
                <a href="index.php?option=com_gridbox&view=reviews">
                    <span class="zmdi zmdi-ticket-star"></span>
                    <span class="sidebar-title"><?php echo JText::_('REVIEWS'); ?></span>
<?php
                if ($reviewsCount > 0) {
?>
                    <span class="unread-comments-count" data-type="reviews"><?php echo $reviewsCount; ?></span>
<?php
                }
?>
                </a>
                <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('REVIEWS'); ?></span>
            </span>
<?php
        }
?>
<?php
            if ($installedBlog > 0) {
?>
            <span class="authors-pages <?php echo gridboxHelper::checkActive('authors'); ?>">
                <a href="index.php?option=com_gridbox&view=authors">
                    <span class="zmdi zmdi-account-circle"></span>
                    <span class="sidebar-title"><?php echo JText::_('AUTHORS'); ?></span>
                </a>
                <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('AUTHORS'); ?></span>
            </span>
            <span class="app <?php echo gridboxHelper::checkActive('tags'); ?>">
                <a href="index.php?option=com_gridbox&view=tags">
                    <span class="zmdi zmdi-label"></span>
                    <span class="sidebar-title"><?php echo JText::_('TAGS'); ?></span>
                </a>
                <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('TAGS'); ?></span>
            </span>
<?php
            }
?>
        </div>
        <span class="system-pages <?php echo gridboxHelper::checkActive('system'); ?>">
            <a href="index.php?option=com_gridbox&view=system">
                <span class="zmdi zmdi-alert-polygon"></span>
                <span class="sidebar-title"><?php echo JText::_('SYSTEM_PAGES'); ?></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('SYSTEM_PAGES'); ?></span>
        </span>
        <span class="trashed-items <?php echo gridboxHelper::checkActive('trashed'); ?>">
            <a href="index.php?option=com_gridbox&view=trashed">
                <span class="zmdi zmdi-delete"></span>
                <span class="sidebar-title"><?php echo JText::_('TRASHED_ITEMS'); ?></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('TRASHED_ITEMS'); ?></span>
        </span>
        <span class="gridbox-options sidebar-context-parent" data-context="options-context-menu">
            <a href="#">
                <span class="zmdi zmdi-settings"></span>
                <span class="sidebar-title"><?php echo JText::_('OPTIONS'); ?></span>
                <i class="zmdi zmdi-caret-right"></i>
            </a>
        </span>
    </div>
</div>
<div id='login-modal' class='ba-modal-sm modal hide'>
    <div class='modal-body'>
        <div class="ba-login-dialog">
            <div class="ba-header-content">
                <h3 class='ba-modal-header'>
                    <?php echo JText::_('ACTIVATE_LICENSE'); ?>
                </h3>
                <label class="ba-help-icon">
                    <i class="zmdi zmdi-help"></i>
                    <span class="ba-tooltip ba-help ba-hide-element">
                        <?php echo JText::_('LOGIN_TOOLTIP'); ?>
                    </span>
                </label>
            </div>
            <div class="ba-body-content">
                <div class="ba-input-lg">
                    <input class='ba-username reset-input-margin' type='text' autocomplete="off"
                        placeholder="<?php echo JText::_('USERNAME'); ?>">
                    <span class="focus-underline"></span>
                </div>
                <div class="ba-input-lg">
                    <input class='ba-password' type='password' name="ba-password" autocomplete="off"
                        placeholder="<?php echo JText::_('PASSWORD'); ?>">
                    <span class="focus-underline"></span>
                </div>
                <input type="hidden" id="theme-id">
            </div>
            <div class="ba-footer-content">
                <a href="#" class="ba-btn-primary login-button active-button">
                    <?php echo JText::_('ACTIVATE'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
<?php
include JPATH_COMPONENT.'/views/layouts/apps-list-context-menu.php';
?>