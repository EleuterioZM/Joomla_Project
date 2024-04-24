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
<script type="text/javascript">
    var joomlaUser = {
        email: '<?php echo $user->email; ?>',
        id: '<?php echo $user->id; ?>',
        name: '<?php echo $user->name; ?>'
    }
</script>
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
<div id="comments-settings-dialog" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check comments-settings-apply"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#comments-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <?php echo JText::_('GENERAL'); ?>
                    </a>
                </li>
                <li>
                    <a href="#comments-integration-options" data-toggle="tab">
                        <i class="zmdi zmdi-cloud-done"></i>
                        <?php echo JText::_('INTEGRATIONS'); ?>
                    </a>
                </li>
                <li>
                    <a href="#comments-anti-spam-options" data-toggle="tab">
                        <i class="zmdi zmdi-alert-octagon"></i>
                        <?php echo JText::_('ANTI_SPAM'); ?>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="comments-general-options" class="row-fluid tab-pane left-tabs-wrapper active">
                    <div class="left-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#general-basic-comments-options" data-toggle="tab">
                                    <i class="zmdi zmdi-settings"></i>
                                    <?php echo JText::_('BASIC'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#general-notifications-comments-options" data-toggle="tab">
                                    <i class="zmdi zmdi-notifications"></i>
                                    <?php echo JText::_('NOTIFICATIONS'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#general-admins-comments-options" data-toggle="tab">
                                    <i class="zmdi zmdi-account-circle"></i>
                                    <?php echo JText::_('ADMINS'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="general-basic-comments-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('SUBMISSIONS_PREMODERATION'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                data-website="reviews_premoderation">
                                            <span></span>
                                        </label>
                                        <label class="ba-help-icon">
                                            <i class="zmdi zmdi-help"></i>
                                            <span class="ba-tooltip ba-help ba-hide-element">
                                                <?php echo JText::_('SUBMISSIONS_PREMODERATION_TOOLTIP'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('IP_TRACKING'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                data-website="reviews_ip_tracking">
                                            <span></span>
                                        </label>
                                        <label class="ba-help-icon">
                                            <i class="zmdi zmdi-help"></i>
                                            <span class="ba-tooltip ba-help ba-hide-element">
                                                <?php echo JText::_('IP_TRACKING_TOOLTIP'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('PHOTOS'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings set-group-display ba-hide-element"
                                                data-website="reviews_enable_attachment">
                                            <span></span>
                                        </label>
                                        <label class="ba-help-icon">
                                            <i class="zmdi zmdi-help"></i>
                                            <span class="ba-tooltip ba-help ba-hide-element">
                                                <?php echo JText::_('PHOTOS_TOOLTIP'); ?>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="ba-subgroup-element">
                                        <div class="ba-group-element">
                                            <label><?php echo JText::_('MAX_UPLOAD_FILE_SIZE'); ?></label>
                                            <input type="text" class="website-comments-settings"
                                                data-website="reviews_attachment_size">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="general-notifications-comments-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('EMAIL_NOTIFICATIONS'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings set-group-display ba-hide-element"
                                                data-website="reviews_email_notifications">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="ba-subgroup-element">
                                        <div class="ba-group-element">
                                            <label><?php echo JText::_('AUTHOR_NOTIFICATIONS'); ?></label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                    data-website="reviews_author_notifications">
                                                <span></span>
                                            </label>
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element">
                                                    <?php echo JText::_('AUTHOR_NOTIFICATIONS_DESC'); ?>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="ba-group-element">
                                            <label><?php echo JText::_('USER_NOTIFICATIONS'); ?></label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                    data-website="reviews_user_notifications">
                                                <span></span>
                                            </label>
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element">
                                                    <?php echo JText::_('USER_NOTIFICATIONS_TOOLTIP'); ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="general-admins-comments-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('ADMIN_LABEL'); ?></label>
                                        <input type="text" class="website-comments-settings"
                                            data-website="reviews_moderator_label">
                                    </div>
                                    <div class="ba-group-element">
                                        <div class="ba-tags">
                                            <label>
                                                <?php echo JText::_('ADMINS'); ?>
                                            </label>
                                            <div class="comments-moderators-list-wrapper">
                                                <ul class="comments-moderators-list" data-website="reviews_moderator_admins">
                                                    <li class="add-comments-moderator">
                                                        <span>
                                                            <i class="zmdi zmdi-plus-circle"></i>
                                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                                <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                                            </span>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="comments-integration-options" class="row-fluid tab-pane">
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <label>Gravatar</label>
                            <label class="ba-checkbox">
                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                    data-website="reviews_enable_gravatar">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="ba-options-group">
<?php
                        $configured = !empty($this->integrations->facebook_login->key);
?>
                        <div class="ba-group-element" data-configured="<?php echo intval($configured); ?>">
                            <label>
                                Facebook Login
                                <span class="integrations-configuration-icon">
                                    <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                    <span class="ba-tooltip ba-hide-element ba-top">
                                        <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                    </span>
                                </span>
                            </label>
                            <label class="ba-checkbox">
                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                    data-website="reviews_facebook_login">
                                <span></span>
                            </label>
                            <a class="integrations-configuratio-link" target="_blank" href="index.php?option=com_gridbox&view=integrations">
                                <i class="zmdi zmdi-settings"></i>
                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                            </a>
                        </div>
                    </div>
                    <div class="ba-options-group">
<?php
                        $configured = !empty($this->integrations->google_login->key);
?>
                        <div class="ba-group-element" data-configured="<?php echo intval($configured); ?>">
                            <label>
                                Google Login
                                <span class="integrations-configuration-icon">
                                    <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                    <span class="ba-tooltip ba-hide-element ba-top">
                                        <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                    </span>
                                </span>
                            </label>
                            <label class="ba-checkbox">
                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                    data-website="reviews_google_login">
                                <span></span>
                            </label>
                            <a class="integrations-configuratio-link" target="_blank" href="index.php?option=com_gridbox&view=integrations">
                                <i class="zmdi zmdi-settings"></i>
                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                            </a>
                        </div>
                    </div>
                    <div class="ba-options-group">
<?php
                        $configured = !empty($this->integrations->vk_login->key);
?>
                        <div class="ba-group-element" data-configured="<?php echo intval($configured); ?>">
                            <label>
                                VK Login
                                <span class="integrations-configuration-icon">
                                    <i class="<?php echo $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon'; ?>"></i>
                                    <span class="ba-tooltip ba-hide-element ba-top">
                                        <?php echo $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED'); ?>
                                    </span>
                                </span>
                            </label>
                            <label class="ba-checkbox">
                                <input type="checkbox" class="website-comments-settings ba-hide-element"
                                    data-website="reviews_vk_login">
                                <span></span>
                            </label>
                            <a class="integrations-configuratio-link" target="_blank" href="index.php?option=com_gridbox&view=integrations">
                                <i class="zmdi zmdi-settings"></i>
                                <span class="ba-tooltip ba-hide-element ba-top"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
                            </a>
                        </div>
                    </div>
                </div>
                <div id="comments-anti-spam-options" class="row-fluid tab-pane left-tabs-wrapper">
                    <div class="left-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#anti-spam-captcha-comments-options" data-toggle="tab">
                                    <i class="zmdi zmdi-shield-security"></i>
                                    Captcha
                                </a>
                            </li>
                            <li>
                                <a href="#anti-spam-spam-filters-comments-options" data-toggle="tab">
                                    <i class="zmdi zmdi-block"></i>
                                    <?php echo JText::_('SPAM_FILTERS'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="anti-spam-captcha-comments-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label>reCAPTCHA</label>
                                        <div class="ba-custom-select">
                                            <input readonly="" onfocus="this.blur()" value="" type="text">
                                            <input type="hidden" class="website-comments-settings"
                                                data-website="reviews_recaptcha">
                                            <i class="zmdi zmdi-caret-down"></i>
                                            <ul>
                                                <li data-value=""><?php echo JText::_('NONE_SELECTED'); ?></li>
                                            </ul>
                                        </div>
                                        <div style="display: none !important;">
<?php
                                            echo $this->form->getInput('comments_recaptcha');
?>
                                        </div>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('ONLY_FOR_GUEST'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                data-website="reviews_recaptcha_guests">
                                            <span></span>
                                        </label>
                                        <label class="ba-help-icon">
                                            <i class="zmdi zmdi-help"></i>
                                            <span class="ba-tooltip ba-help ba-hide-element">
                                                <?php echo JText::_('ONLY_FOR_GUEST_TOOLTIP'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="anti-spam-spam-filters-comments-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <div class="ba-tags">
                                            <label>
                                                <?php echo JText::_('BANNED_EMAILS'); ?>
                                            </label>
                                            <div class="comments-banned-list-wrapper">
                                                <ul class="comments-banned-emails" data-type="emails">
                                                    <li class="enter-comments-banned-item">
                                                        <input type="text" placeholder="<?php echo JText::_('BANNED_EMAILS'); ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <div class="ba-tags">
                                            <label>
                                                <?php echo JText::_('BANNED_WORDS'); ?>
                                            </label>
                                            <div class="comments-banned-list-wrapper">
                                                <ul class="comments-banned-words" data-type="words">
                                                    <li class="enter-comments-banned-item">
                                                        <input type="text" placeholder="<?php echo JText::_('BANNED_WORDS'); ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <div class="ba-tags">
                                            <label>
                                                <?php echo JText::_('BANNED_IP_ADDRESSES'); ?>
                                            </label>
                                            <div class="comments-banned-list-wrapper">
                                                <ul class="comments-banned-ip" data-type="ip">
                                                    <li class="enter-comments-banned-item">
                                                        <input type="text" placeholder="<?php echo JText::_('BANNED_IP_ADDRESSES'); ?>">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('BLOCK_LINKS'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                data-website="reviews_block_links">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('AUTO_DELETING'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="website-comments-settings ba-hide-element"
                                                data-website="reviews_auto_deleting_spam">
                                            <span></span>
                                        </label>
                                        <label class="ba-help-icon">
                                            <i class="zmdi zmdi-help"></i>
                                            <span class="ba-tooltip ba-help ba-hide-element">
                                                <?php echo JText::_('AUTO_DELETING_TOOLTIP'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$view = 'comments';
include JPATH_COMPONENT.'/views/layouts/users-dialog.php';
?>
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=reviews'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('REVIEWS'); ?></h1>
                            <span class="blog-icons">
                                <span class="comments-settings">
                                    <i class="zmdi zmdi-settings"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('SETTINGS'); ?></span>
                                </span>
                            </span>
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
                            <div class="reset-filtering">
                                <i class="zmdi zmdi-replay"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET_FILTER'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="main-table twin-view-table comments-table<?php echo count($this->items) == 0 ? ' empty-comments-table':''; ?>">
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
                                                    <li data-value="approved" >
                                                        <?php echo JText::_('APPROVED');?>
                                                    </li>
                                                    <li data-value="pending">
                                                        <?php echo JText::_('PENDING');?>
                                                    </li>
                                                    <li data-value="spam">
                                                        <?php echo JText::_('SPAM');?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="<?php echo $listOrder == 'name' ? 'active' : ''; ?>">
                                        <span data-sorting="name">
                                            <?php echo JText::_('NAME'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'date' ? 'active' : ''; ?>">
                                        <span data-sorting="date">
                                            <?php echo JText::_('DATE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                            foreach ($this->items as $i => $item) {
                                $item->link = JUri::root().'index.php/reviewID-'.$item->id;
                                $timestamp = strtotime($item->date);
                                $item->time = date('H:i', $timestamp);
                                $item->attachments = gridboxHelper::getReviewAttachments($item->id);
                                if (!empty($item->user_email)) {
                                    $item->email = $item->user_email;
                                }
                                $avatar = $item->avatar;
                                if (empty($avatar)) {
                                    $author = gridboxHelper::getAuthor($item->user_id);
                                    $item->name = $author->title ?? $item->name;
                                    $avatar = gridboxHelper::getUserAvatar($item->email, 'reviews_enable_gravatar', $author);
                                }
                                $str = json_encode($item);
?>
                                <tr class="<?php echo $item->unread == 1 ? 'ba-comment-unread' : '' ?>">
                                    <td class="select-td ">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                        <input type="hidden"
                                               value='<?php echo htmlspecialchars($str, ENT_QUOTES); ?>'>
                                    </td>
                                    <td class="status-td">
<?php
                                    switch ($item->status) {
                                        case 'approved':
                                            $iconClassName = 'zmdi zmdi-eye ba-icon-md';
                                            $iconTooltip = JText::_('APPROVED');
                                            break;
                                        case 'pending':
                                            $iconClassName = 'zmdi zmdi-eye-off ba-icon-md';
                                            $iconTooltip = JText::_('PENDING');
                                            break;
                                        case 'spam':
                                            $iconClassName = 'zmdi zmdi-alert-octagon ba-icon-md';
                                            $iconTooltip = JText::_('SPAM');
                                            break;
                                    }
?>
                                        <span>
                                            <i class="<?php echo $iconClassName; ?>"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo $iconTooltip; ?></span>
                                        </span>
                                    </td>
                                    <td class="name-cell">
                                        <span class="comments-text-wrapper">
                                            <span class="ba-author-avatar"
                                                style="background-image: url(<?php echo str_replace(' ', '%20', $avatar); ?>);"></span>
                                            <img src="<?php echo $avatar; ?>" style="display: none !important;"
                                                onerror="setGravatarDefault(this);">
                                            <span class="comments-text">
                                                <span class="comments-name">
                                                    <span><?php echo $item->name; ?></span>
<?php
                                                if ($item->parent == 0) {
?>
                                                    <span class="review-rating-wrapper">
<?php
                                                    for ($i = 0; $i < 5; $i++) {
?>
                                                        <i class="zmdi zmdi-star<?php echo $i < $item->rating ? ' active' : ''; ?>"></i>
<?php                                                        
                                                    }
?>
                                                    </span>
<?php
                                                }
?>
                                                </span>
                                                <span class="comments-message"><?php echo $item->message; ?></span>
                                            </span>
                                        </span>
                                    </td>
                                    <td class="date-cell">
<?php
                                        $date = date('Y-m-d', $timestamp);
                                        echo $date;
?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="comments-right-sidebar">
                            <div class="comments-sidebar-header">
                                <span class="disabled approve-user-comment" data-status="approved" data-task="contextApprove">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('APPROVE'); ?></span>
                                </span>
                                <span class="disabled spam-user-comment" data-status="spam" data-task="contextSpam">
                                    <i class="zmdi zmdi-alert-octagon"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('SPAM'); ?></span>
                                </span>
                                <span class="disabled ban-user-comment">
                                    <i class="zmdi zmdi-block"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('BAN_USER'); ?></span>
                                </span>
                                <span class="disabled delete-user-comment">
                                    <i class="zmdi zmdi-delete"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                                </span>
                            </div>
                            <div class="comments-sidebar-body">
                                
                            </div>
                        </div>
                    </div>
                    <?php echo $this->pagination->getListFooter(); ?>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="ba_view" value="reviews">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div style="display: none;" class="comment-data-view-pattern">
    <div class="comments-sidebar-scroll-wrapper">
        <div class="comment-user-info-wrapper">
            <div class="comment-user-info">
                <span class="comment-user-date"></span>
                <span class="comment-user-name"></span>
                <span class="comment-user-email"></span>
                <span class="comment-user-ip"></span>
            </div>
        </div>
        <div class="comment-data-wrapper">
            <div class="comment-page-title-wrapper">
                <span class="comment-page-title"></span>
                <a href="<?php echo JUri::root(); ?>" target="_blank" class="comment-page-url">
                    <i class="zmdi zmdi-open-in-new"></i>
                </a>
            </div>
            <div class="review-rating-wrapper">
                <i class="zmdi zmdi-star"></i>
                <i class="zmdi zmdi-star"></i>
                <i class="zmdi zmdi-star"></i>
                <i class="zmdi zmdi-star"></i>
                <i class="zmdi zmdi-star"></i>
            </div>
            <div class="comment-user-message-wrapper">
                <p class="comment-message"></p>
                <span class="edit-user-comment-wrapper">
                    <i class="zmdi zmdi-edit edit-user-comment"></i>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
                </span>
                <div class="ba-comment-message-wrapper">
                    <textarea placeholder="<?php echo JText::_('WRITE_COMMENT_HERE'); ?>" class="ba-comment-message"></textarea>
                    <div class="ba-comment-xhr-attachment-wrapper"></div>
                    <i class="zmdi zmdi-camera ba-comment-attachment-trigger"></i>
                    <input class="ba-comment-attachment" type="file" style="display: none !important;" multiple
                        data-size="<?php echo gridboxHelper::$website->attachment_size; ?>"
                        data-types="gif, jpg, jpeg, png, svg, webp" data-attach="image">
                    <span class="ba-submit-comment-wrapper">
                        <span class="ba-submit-cancel">
                            <?php echo JText::_('CANCEL') ?>
                        </span>
                        <span class="ba-submit-comment" data-type="submit">
                            <?php echo JText::_('JTOOLBAR_APPLY'); ?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="comment-likes-wrapper">
                <span class="comment-likes-action" data-action="likes">
                    <i class="zmdi zmdi-thumb-up"></i>
                    <span class="likes-count"></span>
                </span>
                <span class="comment-likes-action" data-action="dislikes">
                    <i class="zmdi zmdi-thumb-down"></i>
                    <span class="likes-count"></span>
                </span>
            </div>
            <div class="comment-attachments-image-wrapper">
                
            </div>
            <div class="comment-attachments-wrapper">
                
            </div>
        </div>
    </div>
    <div class="ba-comment-message-wrapper">
        <textarea placeholder="<?php echo JText::_('WRITE_COMMENT_HERE'); ?>" class="ba-comment-message"></textarea>
        <div class="ba-comment-xhr-attachment-wrapper"></div>
        <span class="ba-submit-comment ba-disabled-submit" data-type="reply">
            <i class="zmdi zmdi-mail-reply"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('REPLY'); ?></span>
        </span>
    </div>
</div>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="comments-approve"><i class="zmdi zmdi-check"></i><?php echo JText::_('APPROVE'); ?></span>
    <span class="comments-spam"><i class="zmdi zmdi-alert-octagon"></i><?php echo JText::_('SPAM'); ?></span>
    <span class="comments-delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>
<?php include(JPATH_COMPONENT.'/views/layouts/photo-editor.php'); ?>