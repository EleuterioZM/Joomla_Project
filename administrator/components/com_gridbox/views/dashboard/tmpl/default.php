<?php
/**
* @package   gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$user = JFactory::getUser();
$createUrl = gridboxHelper::getEditorLink().'&app_id=';
$gridboxStateStr = gridboxHelper::checkGridboxState();
$gridboxState = json_decode($gridboxStateStr);
$gridboxStateCount = !isset($gridboxState->data) ? 1 : 0;
?>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<script src="<?php echo JUri::root(); ?>components/com_gridbox/libraries/chart/chart.js" type="text/javascript"></script>
<script type="text/javascript">
    if (!window.Joomla) {
        window.Joomla = {};
    }
    app.store = <?php echo json_encode(gridboxHelper::$store); ?>;
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="multiple">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
<?php
                include(JPATH_COMPONENT.'/views/layouts/sidebar.php');
?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('DASHBOARD'); ?></h1>
                            <span class="ba-dashboard-popover-trigger" data-target="ba-dashboard-apps-list">
                                <i class="zmdi zmdi-plus-circle"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_ITEM'); ?></span>
                            </span>
                        </div>
                        <div class="filter-dashboars-icons-wrapper">
                            <span class="ba-dashboard-popover-trigger" data-target="ba-dashboard-about">
                                <i class="<?php echo ($gridboxStateCount == 0) ? 'zmdi zmdi-info"' : 'zmdi zmdi-notifications'; ?>"></i>
                                <span class="about-notifications-count"
                                <?php echo ($gridboxStateCount == 0) ? ' style="display: none;"' : '' ?>>
                                <?php echo $gridboxStateCount; ?></span>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ABOUT_GRIDBOX'); ?></span>
                            </span>
                            <span class="gridbox-languages">
                                <i class="zmdi zmdi-globe"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('LANGUAGES'); ?></span>
                            </span>
                        </div>
                    </div>
                    <div class="main-table dashboard-content">
<?php
                    if ($installedStore) {
                        $statuses = gridboxHelper::getStatuses();
?>
                        <div class="row-fluid">
                            <div class="span12 ba-store-statistic">
                                <div class="ba-store-statistic-header">
                                    <div class="ba-store-statistic-header-title-wrapper">
                                        <span class="ba-store-statistic-header-title"><?php echo JText::_('STORE_STATISTICS'); ?></span>
                                    </div>
                                    <div class="ba-store-statistic-header-filter-wrapper">
                                        <div class="ba-store-statistic-action-wrapper">
                                            <span class="ba-store-statistic-action" data-action="-">
                                                <i class="zmdi zmdi-caret-left"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('PREV'); ?></span>
                                            </span>
                                            <span class="ba-store-statistic-custom-action">
                                                <input type="hidden" class="open-calendar-dialog" data-format="Y-m-d" data-name="0" data-link="1"
                                                    data-type="range-dates" data-key="from">
                                                <i class="zmdi zmdi-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <div class="ba-store-statistic-select-wrapper">
<?php
                                        $d = JDate::getInstance()->format('M d, Y');
                                        $w = JDate::getInstance(strtotime('-7 days'))->format('M d, Y');
                                        $w .= ' - '.JDate::getInstance(strtotime('-1 day'))->format('M d, Y');
                                        $dates = ['d' => $d, 'w' => $w, 'm' => JText::_('MONTHLY').', '.date('Y'),
                                            'y' => JText::_('YEARLY'), 'c' => $d.' - '.$d];
                                        $statistic = gridboxHelper::getShopStatistic(date('Y-m-d'), 'd');
?>
                                            <div class="ba-custom-select ba-store-statistic-select">
                                                <input readonly type="text" value="<?php echo $dates['d']; ?>">
                                                <input type="hidden" value="d" data-current="<?php echo date('Y-m-d'); ?>">
                                                <ul>
                                                    <li data-value="d" data-text="<?php echo $dates['d']; ?>">
                                                        <?php echo JText::_('DAILY'); ?>
                                                    </li>
                                                    <li data-value="w" data-text="<?php echo $dates['w']; ?>">
                                                        <?php echo JText::_('WEEKLY'); ?>
                                                    </li>
                                                    <li data-value="m" data-text="<?php echo $dates['m']; ?>">
                                                        <?php echo JText::_('MONTHLY'); ?>
                                                    </li>
                                                    <li data-value="y" data-text="<?php echo $dates['y']; ?>">
                                                        <?php echo JText::_('YEARLY'); ?>
                                                    </li>
                                                    <li data-value="c" data-text="<?php echo $dates['c']; ?>">
                                                        <?php echo JText::_('CUSTOM'); ?>
                                                    </li>
                                                </ul>
                                                <i class="zmdi zmdi-caret-down"></i>
                                            </div>
                                        </div>
                                        <div class="ba-store-statistic-action-wrapper">
                                            <span class="ba-store-statistic-action" data-action="+">
                                                <i class="zmdi zmdi-caret-right"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('NEXT'); ?></span>
                                            </span>
                                            <span class="ba-store-statistic-custom-action">
                                                <input type="hidden" class="open-calendar-dialog" data-format="Y-m-d" data-name="1" data-link="0"
                                                    data-type="range-dates" data-key="to">
                                                <i class="zmdi zmdi-calendar-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ba-store-statistic-body">
                                    <div class="row-fluid ba-store-statistic-total-wrapper">
                                        <div class="span8">
                                            <div class="ba-store-statistic-count-wrapper" data-type="orders"
                                                style="--statistic-count-color: <?php echo $statuses->new->color; ?>;">
                                                <span class="ba-store-statistic-count"><?php echo $statistic->counts['orders']; ?></span>
                                                <span class="ba-store-statistic-text"><?php echo JText::_('ORDERS'); ?></span>
                                            </div>
                                            <div class="ba-store-statistic-count-wrapper" data-type="completed"
                                                style="--statistic-count-color: <?php echo $statuses->completed->color; ?>;">
                                                <span class="ba-store-statistic-count"><?php echo $statistic->counts['completed']; ?></span>
                                                <span class="ba-store-statistic-text"><?php echo JText::_('SALES_STATISTICS'); ?></span>
                                            </div>
                                            <div class="ba-store-statistic-count-wrapper" data-type="refunded"
                                                style="--statistic-count-color: <?php echo $statuses->refunded->color; ?>;">
                                                <span class="ba-store-statistic-count"><?php echo $statistic->counts['refunded']; ?></span>
                                                <span class="ba-store-statistic-text"><?php echo JText::_('REFUNDS'); ?></span>
                                            </div>
                                        </div>
                                        <div class="span4">
<?php
                                            $price = gridboxHelper::preparePrice($statistic->total);
?>
                                            <div class="ba-store-statistic-total-price">
                                                <span class="ba-store-statistic-title"><?php echo JText::_('TOTAL'); ?></span>
                                                <span class="ba-store-statistic-price"><?php echo $price; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid ba-store-statistic-body-wrapper">
                                        <div class="span8 ba-statistics-chart-wrapper">
                                            <span></span>
                                            <div class="ba-statistics-chart"></div>
                                        </div>
                                        <div class="span4 ba-store-statistic-products-wrapper">
                                            <span class="ba-store-statistic-products-title"><?php echo JText::_('TOP_10'); ?></span>
                                            <div class="ba-store-statistic-products">
<?php
                                            foreach ($statistic->products as $product) {
                                                $price = gridboxHelper::preparePrice($product->price);
?>
                                                <div class="ba-store-statistic-product">
<?php
                                                    if (!empty($product->image)) {
?>
                                                    <div class="ba-store-statistic-product-image"
                                                        style="background-image: url(<?php echo str_replace(' ', '%20', $product->image); ?>);">
                                                    </div>
<?php
                                                    }
?>
                                                    <div class="ba-store-statistic-product-content">
                                                        <span  class="ba-store-statistic-product-title-wrapper">
                                                            <span class="ba-store-statistic-product-title">
                                                                <?php echo $product->title; ?>
                                                            </span>
                                                            <span class="ba-store-statistic-product-description">
<?php
                                                            if (!empty($product->info)) {
?>
                                                                <span class="ba-store-statistic-product-info">
                                                                    <?php echo $product->info; ?>
                                                                </span>
<?php
                                                            }
?>
                                                            </span>
                                                        </span>
                                                        <span class="ba-store-statistic-product-sales-wrapper">
                                                            <span class="ba-store-statistic-product-sales">
                                                                <span class="ba-store-statistic-product-sales-count">
                                                                    <?php echo $product->quantity; ?>
                                                                </span>
                                                                <span class="ba-store-statistic-product-sales-text">
                                                                    <?php echo JText::_('SALES_STATISTICS') ?>
                                                                </span>
                                                            </span>
                                                            <span class="ba-store-statistic-price"><?php echo $price; ?></span>
                                                        </span>
                                                    </div>
<?php
                                                if (!empty($product->link)) {
?>
                                                    <a href="<?php echo $product->link; ?>" target="_blank"></a>
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
                                </div>
                            </div>
                        </div>
<?php
                    }
?>
                        <div class="row-fluid">
                           <div class="span8 last-edit-pages">
<?php
                            $count = count($this->pages);
                            if ($count > 0) {

?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span >
                                                    <?php echo JText::_('RECENTLY_OPENED'); ?>
                                                </span>
                                            </th>
                                            <th>
                                                <span>
                                                    <?php echo JText::_('VIEWS'); ?>
                                                </span>
                                            </th>
                                            <th>
                                                <span>
                                                    <?php echo JText::_('ID'); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
                                    foreach ($this->pages as $page) {
                                        $img = gridboxHelper::prepareIntroImage($page->intro_image);
                                        if (!empty($page->page_category) && !empty($img)) {
                                            $introStr = '<span class="post-intro-image" style="background-image: url(';
                                            if (!gridboxHelper::isExternal($img)) {
                                                $introStr .= str_replace(' ', '%20', JUri::root().$img);
                                            } else {
                                                $introStr .= $img;
                                            }
                                            $introStr .= ');"></span>';
                                        } else {
                                            $introStr = '<span class="post-intro-image gridbox-app-item-';
                                            if (!empty($page->type)) {
                                                $introStr .= $page->type;
                                            } else {
                                                $introStr .= 'single';
                                            }
                                            $introStr .= '"><i class="'.gridboxHelper::getIcon($page).'"></i></span>';
                                        }
?>
                                        <tr>
                                            <td class="title-cell">
<?php
                                            if ($user->authorise('core.edit', 'com_gridbox.page.'.$page->id)) {
?>
                                                <a target="_blank"
                                                   href="index.php?option=com_gridbox&task=gridbox.edit&id=<?php echo $page->id; ?>">
<?php
                                            } else {
?>
                                                <span class="not-permitted-wrapper">
<?php
                                            }
?>
                                                    <?php echo $introStr; ?>
                                                    <?php echo $page->title; ?>
<?php
                                            if ($user->authorise('core.edit', 'com_gridbox.page.'.$page->id)) {
?>
                                                </a>
<?php
                                            } else {
?>
                                                </span>
<?php
                                            }
?>
                                            </td>
                                            <td class="hits-cell">
                                                <?php echo $page->hits; ?>
                                            </td>
                                            <td>
                                                <?php echo $page->id; ?>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
<?php
                            } else {
?>
                                <div class="dashboard-create-first-page">
                                    <a href="<?php echo $createUrl.'0&id='; ?>" target="_blank">
                                        <i class="zmdi zmdi-file"></i>
                                        <span>+ <?php echo JText::_('CREATE_NEW_PAGE'); ?></span>
                                    </a>
                                </div>
<?php
                            }
?>
                            </div>
                            <div class="span4 recent-gridbox-apps">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span>
                                                    <?php echo JText::_('APPS'); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <a href="index.php?option=com_gridbox&view=pages">
                                                    <span class="post-intro-image gridbox-app-item-single">
                                                        <i class="zmdi zmdi-file"></i>
                                                    </span>
                                                    <span class="recent-apps-title">
                                                        <?php echo JText::_('PAGES'); ?>
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
<?php
    
                                    foreach ($this->apps as $app) {
                                    	$view = $app->type == 'single' ? 'single' : 'apps';
                                    	$viewLink = 'index.php?option=com_gridbox&view='.$view.'&id='.$app->id;
?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $viewLink; ?>">
                                                    <span class="post-intro-image gridbox-app-item-<?php echo $app->type; ?>">
                                                        <i class="<?php echo gridboxHelper::getIcon($app); ?>"></i>
                                                    </span>
                                                    <span class="recent-apps-title">
                                                        <?php echo $app->title; ?>
                                                    </span>
                                            </a>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
                                <div class="gridbox-app-item-footer">
                                    <a href="index.php?option=com_gridbox&view=appslist">
                                       <i class="zmdi zmdi-widgets"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4 recent-gridbox-files">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th colspan="2">
                                                <span>
                                                    <?php echo JText::_('RECENT_FILES'); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
                                    $img = JUri::root().'administrator/index.php?option=com_gridbox&task=uploader.showImage&image=';
                                    $now = strtotime('now');
                                    foreach ($this->files as $file) {
                                        if ($file->ext == 'svg' || $file->ext == 'ico') {
                                            $url = $file->url;
                                        } else if (in_array($file->ext, $this->_imagesExt)) {
                                            $url = $img.$file->path.'&time='.$now;
                                        }
                                        if (in_array($file->ext, $this->_imagesExt)) {
                                            $imageFlag = true;
                                        } else {
                                            $imageFlag = false;
                                        }
?>
                                        <tr>
                                            <td class="title-td">
<?php
                                            if ($imageFlag) {
?>
                                                <span class="recent-file-image"
                                                    style="background-image: url(<?php echo str_replace(' ', '%20', $url); ?>);"></span>
<?php
                                            } else {
                                                echo '<i class="zmdi zmdi-file ba-file-icon"></i>';
                                            }
?>
                                                <span class="file-title">
                                                    <?php echo $file->title; ?>
                                                </span>
                                            </td>
                                            <td class="filesize-td">
                                                <span>
                                                    <?php echo $file->size; ?>
                                                </span>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
                                <div class="gridbox-app-item-footer">
                                    <a href="#" class="dashboard-view-media-manager">
                                       <i class="zmdi zmdi-folder"></i>
                                    </a>
                                </div>
                            </div>
<?php
                            if (gridboxHelper::checkSystemApp('comments')) {
?>
                            <div class="span4 recent-gridbox-comments">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span>
                                                    <?php echo JText::_('RECENT_COMMENTS'); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
                                    
                                    foreach ($this->comments as $comment) {
                                        $timestamp = strtotime($comment->date);
                                        if (!empty($comment->user_email)) {
                                            $comment->email = $comment->user_email;
                                        }
                                        $avatar = $comment->avatar;
                                        if (empty($avatar)) {
                                            $author = gridboxHelper::getAuthor($comment->user_id);
                                            $comment->name = $author->title ?? $comment->name;
                                            $avatar = gridboxHelper::getUserAvatar($comment->email, 'enable_gravatar', $author);
                                        }
                                        $date = date('Y-m-d', $timestamp);
?>
                                        <tr>
                                            <td class="name-cell">
                                                <span class="comments-text-wrapper">
                                                    <span class="ba-author-avatar"
                                                        style="background-image: url(<?php echo str_replace(' ', '%20', $avatar); ?>);"></span>
                                                    <img src="<?php echo $avatar; ?>" style="display: none !important;"
                                                        onerror="setGravatarDefault(this);">
                                                    <span class="comments-text">
                                                        <span class="comments-name-wrapper">
                                                            <span class="comments-name"><?php echo $comment->name; ?></span>
                                                            <span class="comments-date"><?php echo $date; ?></span>
                                                        </span>
                                                        <span class="comments-message"><?php echo $comment->message; ?></span>
                                                    </span>
                                                </span>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
                                <div class="gridbox-app-item-footer">
                                    <a href="index.php?option=com_gridbox&view=comments">
                                       <i class="zmdi zmdi-comment-more"></i>
                                    </a>
                                </div>
                            </div>
<?php
                            }
                            if (gridboxHelper::checkSystemApp('reviews')) {
?>
                            <div class="span4 recent-gridbox-reviews">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span>
                                                    <?php echo JText::_('RECENT_REVIEWS'); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
                                    
                                    foreach ($this->reviews as $review) {
                                        $timestamp = strtotime($review->date);
                                        if (!empty($review->user_email)) {
                                            $review->email = $review->user_email;
                                        }
                                        $avatar = $review->avatar;
                                        if (empty($avatar)) {
                                            $author = gridboxHelper::getAuthor($review->user_id);
                                            $review->name = $author->title ?? $review->name;
                                            $avatar = gridboxHelper::getUserAvatar($review->email, 'reviews_enable_gravatar');
                                        }
                                        $date = date('Y-m-d', $timestamp);
?>
                                        <tr>
                                            <td class="name-cell">
                                                <span class="comments-text-wrapper">
                                                    <span class="ba-author-avatar"
                                                        style="background-image: url(<?php echo str_replace(' ', '%20', $avatar); ?>);"></span>
                                                    <img src="<?php echo $avatar; ?>" style="display: none !important;"
                                                        onerror="setGravatarDefault(this);">
                                                    <span class="comments-text">
                                                        <span class="comments-name-wrapper">
                                                            <span class="comments-name">
                                                                <span><?php echo $review->name; ?></span>
<?php
                                                            if ($review->parent == 0) {
?>
                                                                <span class="review-rating-wrapper">
<?php
                                                                for ($i = 0; $i < 5; $i++) {
?>
                                                            <i class="zmdi zmdi-star<?php echo $i < $review->rating ? ' active' : ''; ?>"></i>
<?php                                                        
                                                                }
?>
                                                                </span>
<?php
                                                            }
?>
                                                            </span>
                                                        </span>
                                                        <span class="comments-message"><?php echo $review->message; ?></span>
                                                    </span>
                                                </span>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
                                <div class="gridbox-app-item-footer">
                                    <a href="index.php?option=com_gridbox&view=reviews">
                                       <i class="zmdi zmdi-ticket-star"></i>
                                    </a>
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
<div class="ba-dashboard-apps-dialog ba-dashboard-apps-list">
    <div class="ba-dashboard-apps-body">
        <div class="ba-gridbox-dashboard-row">
            <a href="<?php echo $createUrl.'0&id='; ?>" target="_blank" class="gridbox-app-item-single">
                <i class="zmdi zmdi-file"></i>
                <span><?php echo JText::_('PAGES'); ?></span>
            </a>
        </div>
<?php
    
    foreach ($this->apps as $app) {
?>
        <div class="ba-gridbox-dashboard-row">
            <a href="<?php echo $createUrl.$app->id.'&id='; ?>" target="_blank"
                class="gridbox-app-item-<?php echo $app->type; ?>">
                <i class="<?php echo gridboxHelper::getIcon($app); ?>"></i>
                <span><?php echo $app->title; ?></span>
            </a>
        </div>
<?php
    }
?>
    </div>
</div>
<div class="ba-dashboard-apps-dialog ba-dashboard-about">
    <div class="ba-dashboard-apps-body">
        <div class="ba-gridbox-dashboard-row gridbox-version-wrapper">
            <i class="zmdi zmdi-info"></i>
            <span>Gridbox</span>
            <span class="gridbox-version"><?php echo $this->about->version; ?></span>
        </div>
        <div class="ba-gridbox-dashboard-row gridbox-app-item-blog gridbox-deactivate-license"
            <?php echo isset($gridboxState->data) ? '' : 'style="display:none;"'; ?>>
            <i class="zmdi zmdi-shield-check"></i>
            <span><?php echo JText::_('YOUR_LICENSE_ACTIVE'); ?></span>
            <a class="deactivate-link dashboard-link-action" href="#"><?php echo JText::_('DEACTIVATE'); ?></a>
        </div>
        <div class="ba-gridbox-dashboard-row gridbox-app-item-blog gridbox-activate-license"
            <?php echo !isset($gridboxState->data) ? '' : 'style="display:none;"'; ?>>
            <i class="zmdi zmdi-shield-check"></i>
            <span><?php echo JText::_('ACTIVATE_LICENSE'); ?></span>
            <a class="activate-link dashboard-link-action" href="#"><?php echo JText::_('ACTIVATE'); ?></a>
        </div>
<?php
    if ($user->authorise('core.edit', 'com_gridbox')) {
?>
        <div class="ba-gridbox-dashboard-row gridbox-app-item-blog gridbox-update-wrapper">
            <i class="zmdi zmdi-check-circle"></i>
            <span><?php echo JText::_('GRIDBOX_IS_UP_TO_DATE'); ?></span>
        </div>
<?php
    }
?>
    </div>
    <div class="ba-dashboard-apps-footer">
        <span>© <?php echo date('Y'); ?> <a href="https://www.balbooa.com/" target="_blink">Balbooa.com</a> All Rights Reserved.</span>
    </div>
</div>
<div id="deactivate-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('LICENSE_DEACTIVATION'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('ARE_YOU_SURE_DEACTIVATE') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary red-btn" id="apply-deactivate">
            <?php echo JText::_('APPLY') ?>
        </a>
    </div>
</div>
<template class="ba-store-statistic-product-template">
    <div class="ba-store-statistic-product">
        <div class="ba-store-statistic-product-image"></div>
        <div class="ba-store-statistic-product-content">
            <span  class="ba-store-statistic-product-title-wrapper">
                <span class="ba-store-statistic-product-title"></span>
                <span class="ba-store-statistic-product-description">
                    <span class="ba-store-statistic-product-info">
                        
                    </span>
                </span>
            </span>
            <span class="ba-store-statistic-product-sales-wrapper">
                <span class="ba-store-statistic-product-sales">
                    <span class="ba-store-statistic-product-sales-count"></span>
                    <span class="ba-store-statistic-product-sales-text"><?php echo JText::_('SALES_STATISTICS') ?></span>
                </span>
                <span class="ba-store-statistic-price"></span>
            </span>
        </div>
        <a href="" target="_blank"></a>
    </div>    
</template>

<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>
<?php include(JPATH_COMPONENT.'/views/layouts/photo-editor.php'); ?>