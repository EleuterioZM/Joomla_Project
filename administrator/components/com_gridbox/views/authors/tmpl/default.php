<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$sortFields = $this->getSortFields();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$state = $this->state->get('filter.state');
$accessState = $this->state->get('filter.access');
$languageState = $this->state->get('filter.language');
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
<div id="edit-author-social-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3 class="ba-modal-header"><?php echo JText::_('SOCIAL_LINKS'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="ba-custom-select">
            <input readonly class="reset-input-margin" type="text">
            <input type="hidden">
            <ul>
                
            </ul>
            <i class="zmdi zmdi-caret-down"></i>
        </div>
        <input type="text" class="author-link-url" placeholder="<?php echo JText::_('LINK'); ?>">
        <span class="focus-underline"></span>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
        <a href="#" class="ba-btn-primary apply-author-link"><?php echo JText::_('JTOOLBAR_APPLY') ?></a>
    </div>
</div>
<?php
$view = 'author';
include JPATH_COMPONENT.'/views/layouts/users-dialog.php';
?>
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=authors'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div id="create-new-tag-modal" class="ba-modal-sm modal hide" style="display:none">
        <div class="modal-header">
            <h3 class="ba-modal-header"><?php echo JText::_('ADD_AUTHOR'); ?></h3>
        </div>
        <div class="modal-body">
            <input type="text" name="tag_name" id="tag-name" class="reset-input-margin"
                placeholder="<?php echo JText::_('ENTER_AUTHOR_NAME'); ?>">
            <span class="focus-underline"></span>
            <div class="ba-input-lg">
                <input type="hidden" name="user_id">
                <input type="text" class="select-user" readonly="" onfocus="this.blur()"
                    placeholder="<?php echo JText::_('SELECT_USER'); ?>">
                <i class="zmdi zmdi-attachment-alt"></i>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
            <a href="#" class="ba-btn-primary create-new-author"><?php echo JText::_('JTOOLBAR_APPLY') ?></a>
        </div>
    </div>
    <div id="category-settings-dialog" class="ba-modal-lg modal hide" style="display:none">
        <div class="modal-header">
            <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-check authors-settings-apply"></i>
                <i class="zmdi zmdi-close" data-dismiss="modal"></i>
            </div>
        </div>
        <div class="modal-body">
            <div class="general-tabs">
                <ul class="nav nav-tabs uploader-nav">
                    <li class="active">
                        <a href="#category-general-options" data-toggle="tab">
                            <i class="zmdi zmdi-settings"></i>
                            <?php echo JText::_('GENERAL'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#category-seo-options" data-toggle="tab">
                            <i class="zmdi zmdi-globe"></i>
                            SEO
                        </a>
                    </li>
                </ul>
                <div class="tabs-underline"></div>
                <div class="tab-content">
                    <div id="category-general-options" class="row-fluid tab-pane active">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('NAME'); ?>
                                </label>
                                <input type="text" name="category_title" class="category-title"
                                    placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                                    <input type="hidden" name="category-id" class="category-id">
                                <div class="ba-alert-container" style="display: none;">
                                    <i class="zmdi zmdi-alert-circle"></i>
                                    <span></span>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('THIS_FIELD_REQUIRED'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>
                                </label>
                                <input type="text" name="category_alias" class="category-alias"
                                    placeholder="<?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>">
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('IMAGE'); ?>
                                </label>
                                <div class="share-image-wrapper">
                                    <div class="image-field-tooltip"></div>
                                    <input type="text" class="category-intro-image input-with-icon" name="category_intro_image"
                                        placeholder="<?php echo JText::_('IMAGE'); ?>" readonly="" onfocus="this.blur()">
                                    <i class="zmdi zmdi-camera"></i>
                                    <div class="reset disabled-reset reset-share-image">
                                        <i class="zmdi zmdi-close"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('USER'); ?>
                                </label>
                                <input type="hidden" name="author_username">
                                <input type="text" readonly class="select-author-username input-with-icon"
                                    placeholder="<?php echo JText::_('SELECT'); ?>">
                                <i class="zmdi zmdi-attachment-alt"></i>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element profile-picture-wrapper">
                                <label>
                                    <?php echo JText::_('PROFILE_PICTURE'); ?>
                                </label>
                                <div class="share-image-wrapper">
                                    <div class="image-field-tooltip"></div>
                                    <input type="text" class="select-author-avatar" name="author_avatar"
                                        placeholder="<?php echo JText::_('PROFILE_PICTURE'); ?>" readonly="" onfocus="this.blur()">
                                    <i class="zmdi zmdi-camera"></i>
                                    <div class="reset disabled-reset reset-share-image">
                                        <i class="zmdi zmdi-close"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element authors-links-list-wrapper">
                                <label>
                                    <?php echo JText::_('SOCIAL_LINKS'); ?>
                                </label>
                                <div class="authors-links-wrapper">
                                    <div class="authors-links-list"></div>
                                    <div class="add-new-item add-new-author-social-link">
                                        <span>
                                            <i class="zmdi zmdi-plus-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('ADD_NEW_ITEM') ?>
                                            </span>
                                        </span>
                                    </div>
                                    <textarea name="author_social" style="display: none !important;"></textarea>
                                </div>
                            </div>
                        </div>
                        <p class="ba-group-title"><?php echo JText::_('DESCRIPTION'); ?></p>
                        <div class="ba-options-group">
                            <div class="ba-group-element cke-editor-container">
                                <textarea class="category-description" name="category_description" data-key="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="category-seo-options" class="row-fluid tab-pane left-tabs-wrapper">
                        <div class="left-tabs">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#category-seo-general-options" data-toggle="tab">
                                        <i class="zmdi zmdi-settings"></i>
                                        <?php echo JText::_('BASIC'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#category-sharing-options" data-toggle="tab">
                                        <i class="zmdi zmdi-share"></i>
                                        <?php echo JText::_('SHARING'); ?>
                                    </a>
                                </li>
<?php
                            if (gridboxHelper::checkSystemApp('sitemap')) {
?>
                                <li>
                                    <a href="#category-sitemap-options" data-toggle="tab">
                                        <i class="zmdi zmdi-device-hub"></i>
                                        <?php echo JText::_('SITEMAP'); ?>
                                    </a>
                                </li>
<?php
                            }
?>
                                <li>
                                    <a href="#category-schema-markup" data-toggle="tab">
                                        <i class="zmdi zmdi-code"></i>
                                        <?php echo JText::_('SCHEMA_MARKUP'); ?>
                                    </a>
                                </li>
                            </ul>
                            <span class="seo-default-settings" data-type="author">
                                <i class="zmdi zmdi-globe"></i>
                                <?php echo JText::_('DEFAULT_SETTINGS'); ?>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT_SETTINGS_TOOLTIP'); ?></span>
                            </span>
                            <div class="tab-content">
                                <div id="category-seo-general-options" class="row-fluid tab-pane active">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('BROWSER_PAGE_TITLE'); ?>
                                            </label>
                                            <input type="text" name="category_meta_title" class="category-meta-title"
                                                placeholder="<?php echo JText::_('BROWSER_PAGE_TITLE'); ?>">
                                            <div class="select-data-tags input-action-icon" data-template="author-data-tags-template">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>
                                            </label>
                                            <textarea name="category_meta_description" class="category-meta-description"
                                                placeholder="<?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>"></textarea>
                                            <div class="select-data-tags input-action-icon" data-template="author-data-tags-template">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>
                                            </label>
                                            <textarea name="category_meta_keywords" class="category-meta-keywords"
                                                placeholder="<?php echo JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>"></textarea>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JFIELD_METADATA_ROBOTS_LABEL'); ?>
                                            </label>
                                            <div class="ba-custom-select category-robots-select visible-select-top">
                                                <input readonly value="" type="text">
                                                <input type="hidden" name="category_robots" id="category_robots" value="">
                                                <ul>
                                                    <li data-value=""><?php echo JText::_('JGLOBAL_USE_GLOBAL'); ?></li>
                                                    <li data-value="index, follow">index, follow</li>
                                                    <li data-value="noindex, follow">noindex, follow</li>
                                                    <li data-value="index, nofollow">index, nofollow</li>
                                                    <li data-value="noindex, nofollow">noindex, nofollow</li>
                                                </ul>
                                                <i class="zmdi zmdi-caret-down"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="category-sharing-options" class="row-fluid tab-pane">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('IMAGE'); ?>
                                            </label>
                                            <div class="share-image-wrapper">
                                                <div class="image-field-tooltip"></div>
                                                <input type="text" class="category-share-image" name="category_share_image"
                                                    placeholder="<?php echo JText::_('IMAGE'); ?>" readonly="" onfocus="this.blur()">
                                                <i class="zmdi zmdi-camera"></i>
                                                <div class="reset disabled-reset reset-share-image">
                                                    <i class="zmdi zmdi-close"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                            </label>
                                            <input type="text" name="category_share_title" class="category-share-title"
                                                placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                                            <div class="select-data-tags input-action-icon" data-template="author-data-tags-template">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('DESCRIPTION'); ?>
                                            </label>
                                            <textarea name="category_share_description" class="category-share-description"
                                                placeholder="<?php echo JText::_('DESCRIPTION'); ?>"></textarea>
                                            <div class="select-data-tags input-action-icon" data-template="author-data-tags-template">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="category-sitemap-options" class="row-fluid tab-pane">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('OVERRIDE_DEFAULT_SETTINGS'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" name="category_sitemap_override" value="1"
                                                    class="sitemap-override ba-hide-element set-group-display">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-subgroup-element " style="--subgroup-childs:3;">
                                            <div class="ba-group-element">
                                                <label>
                                                    <?php echo JText::_('INCLUDE_ITEM'); ?>
                                                </label>
                                                <label class="ba-checkbox">
                                                    <input type="checkbox" name="category_sitemap_include" value="1"
                                                        class="sitemap-include ba-hide-element">
                                                    <span></span>
                                                </label>
                                                <label class="ba-help-icon">
                                                    <i class="zmdi zmdi-help"></i>
                                                    <span class="ba-tooltip ba-help ba-hide-element">
                                                        <?php echo JText::_('INCLUDE_ITEM_TOOLTIP'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="ba-group-element">
                                                <label>Changefreq</label>
                                                <div class="ba-custom-select">
                                                    <input readonly="" onfocus="this.blur()" type="text">
                                                    <input type="hidden" name="category_changefreq" class="changefreq">
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
                                            <div class="ba-group-element">
                                                <label>Priority</label>
                                                <div class="ba-range-wrapper">
                                                    <span class="ba-range-liner"></span>
                                                    <input type="range" class="ba-range" min="0" max="1" step="0.1">
                                                    <input type="number" data-callback="emptyCallback" name="category_priority" class="priority">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="category-schema-markup" class="row-fluid tab-pane">
                                    <div class="ba-options-group schema-markup-wrapper">
                                        <div class="ba-group-element">
                                            <div class="schema-markup-label">
                                                <label>
                                                    <?php echo JText::_('JSON_LD_SCHEMA_MARKUP'); ?>
                                                </label>
                                                <div class="select-data-tags input-action-icon" data-template="author-data-tags-template">
                                                    <i class="zmdi zmdi-playlist-plus"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                                </div>
                                            </div>
                                            <textarea name="category_schema_markup"></textarea>
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
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('AUTHORS'); ?></h1>
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
                                    <input readonly value="<?php echo $pagLimit[$limit]; ?>"  type="text">
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
                            <div class="enable-custom-pages-order<?php echo $listOrder == 'order_list' ? ' active' : ''; ?>">
                                <i class="zmdi zmdi-format-line-spacing"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DRAG_DROP_SORT_ITEMS'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="main-table pages-list authors-table">
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
                                    <th class="status-th <?php echo $listOrder == 'published' ? 'active' : ''; ?>">
                                        <span data-sorting="published">
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
                                    <th class="<?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                                        <span data-sorting="title">
                                            <?php echo JText::_('NAME'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="count-th">
                                        <?php echo JText::_('COUNT'); ?>
                                    </th>
                                    <th class="username-th<?php echo $listOrder == 'user_id' ? ' active' : ''; ?>">
                                        <span data-sorting="user_id">
                                            <?php echo JText::_('USERNAME'); ?>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'hits' ? 'active' : ''; ?>">
                                        <span data-sorting="hits">
                                            <?php echo JText::_('VIEWS'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'id' ? 'active' : ''; ?>">
                                        <span data-sorting="id">
                                            <?php echo JText::_('ID'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                               foreach ($this->items as $i => $item) { 
                                    $str = json_encode($item);
                                    $avatar = !empty($item->avatar) ? $item->avatar : 'components/com_gridbox/assets/images/default-user.png';
                                    if (strpos($avatar, 'https://') === false && strpos($avatar, 'http://') === false) {
                                        $avatar = JUri::root().$avatar;
                                    }
                                    $avatar = str_replace(' ', '%20', $avatar);
                                    $canChange = $user->authorise('core.edit.state', 'com_gridbox');
?>
                                <tr>
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
                                        echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'authors.', $canChange);
?>
                                    </td>
                                    <td class="title-cell">
                                        <span class="ba-author-avatar" style="background-image: url(<?php echo $avatar; ?>)">
                                        </span>
                                        <span class="ba-title-click-trigger"><?php echo $item->title; ?></span>
                                        <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
                                    </td>
                                    <td class="count-cell">
                                        <?php echo $item->count; ?>
                                    </td>
                                    <td class="username-cell">
                                        <?php echo $item->username; ?>
                                    </td>
                                    <td class="hits-cell">
                                        <?php echo $item->hits; ?>
                                    </td>
                                    <td>
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo $this->pagination->getListFooter(); 
                    if ($user->authorise('core.create', 'com_gridbox')) { ?>
                    <div class="ba-create-item ba-create-tags">
                        <a href="#">
                            <i class="zmdi zmdi-file"></i>
                            <span class="ba-tooltip ba-top ba-hide-element align-center">
                                <?php echo JText::_('ADD_NEW_ITEM'); ?>
                            </span>
                        </a>
                    </div>
                    <?php } ?>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="access_filter" value="<?php echo $accessState; ?>">
                        <input type="hidden" name="ba_view" value="authors">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="tags-settings"><i class="zmdi zmdi-settings"></i><?php echo JText::_('SETTINGS'); ?></span>
    <span class="authors-delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<div id="data-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">
    <div class="modal-body">
        
    </div>
</div>
<template class="author-data-tags-template">
    <div class="data-tags-searchbar">
        <div class="ba-settings-group">
            <div class="ba-settings-item ba-settings-select-type">
                <select class="select-data-tags-type">
                    <option value=""><?php echo JText::_('All'); ?></option>
                    <option value="general"><?php echo JText::_('GENERAL'); ?></option>
                    <option value="author"><?php echo JText::_('AUTHOR'); ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="">
        <div class="ba-settings-group general-data-tags">
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('SITE_NAME'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Site Name]">
            </div>
        </div>
        <div class="ba-settings-group author-data-tags">
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('AUTHOR_NAME'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Author Name]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('AUTHOR_PROFILE_PICTURE_URL'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Author Profile Picture URL]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('AUTHOR_PAGE_URL'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Author Page URL]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('AUTHOR_DESCRIPTION'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Author Description]">
            </div>
        </div>
    </div>
</template>
<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>
<?php include(JPATH_COMPONENT.'/views/layouts/photo-editor.php'); ?>