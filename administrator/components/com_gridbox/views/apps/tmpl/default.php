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
$themeState = $this->state->get('filter.theme');
$authorState = $this->state->get('filter.author');
$accessState = $this->state->get('filter.access');
$languageState = $this->state->get('filter.language');
$user = JFactory::getUser();
$appAssets = new gridboxAssetsHelper($this->blog->id, 'app');
if (!empty($this->category)) {
    $create = gridboxHelper::assetsCheckPermission($this->category, 'category', 'core.create');
} else {
    $create = $appAssets->checkPermission('core.create');
}
$flags = JUri::root().'components/com_gridbox/assets/images/flags/';
$limit = $this->pagination->limit;
$pagLimit = [
    5 => 5,
    10 => 10,
    15 => 15,
    20 => 20,
    25 => 25,
    30 => 30,
    50 => 50,
    100 => 100,
    0 => JText::_('JALL'),
];
if (!isset($pagLimit[$limit])) {
    $limit = 0;
}
if (!empty($this->category)) {
    $url = gridboxHelper::getEditorLink($this->blog->type).'&app_id='.$this->blog->id.'&category='.$this->category.'&id=';
}
$catUrl = 'index.php?option=com_gridbox&view=apps&id='.$this->blog->id.'&category=';
$editBlog = gridboxHelper::getEditorLink().'&edit_type=blog&id='.$this->blog->id;
$editPostLayout = gridboxHelper::getEditorLink().'&edit_type=post-layout&id='.$this->blog->id;
$action = JUri::root().'administrator/index.php?option=com_gridbox&view=apps&id='.$this->blog->id;
if (!empty($this->category)) {
    $action .= '&category='.$this->category;
}
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<script type="text/javascript">
    document.body.classList.add('view-blogs');
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/ckeditor.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>"
    class="jlib-selection">
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
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<div id="pages-list-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display: display:none;">
    <div class="modal-body">
        <iframe src="javascript:''"></iframe>
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
<form action="<?php echo $action; ?>" method="post"
    name="adminForm" id="adminForm" autocomplete="off">
    <div id="create-category-modal" class="ba-modal-sm modal hide" style="display:none">
        <div class="modal-body">
            <h3><?php echo JText::_('CREATE_CATEGORY'); ?></h3>
            <input type="text" class="category-name" name="category_name" placeholder="<?php echo JText::_('CATEGORY_NAME') ?>">
            <span class="focus-underline"></span>
            <input type="hidden" name="parent_id" class="parent-id">
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal">
                <?php echo JText::_('CANCEL') ?>
            </a>
            <a href="#" class="ba-btn-primary" id="create-new-category">
                <?php echo JText::_('JTOOLBAR_APPLY') ?>
            </a>
        </div>
    </div>
    <div id="move-to-modal" class="ba-modal-md modal hide" style="display:none">
        <div class="modal-body">
            <div class="ba-modal-header">
                <h3><?php echo JText::_('MOVE_TO'); ?></h3>
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
            <a href="#" class="ba-btn-primary apply-move">
                <?php echo JText::_('JTOOLBAR_APPLY') ?>
            </a>
        </div>
    </div>
    <div id="settings-dialog" class="ba-modal-lg modal hide" style="display:none">
        <div class="modal-header">
            <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-check settings-apply"></i>
                <i class="zmdi zmdi-close" data-dismiss="modal"></i>
            </div>
        </div>
        <div class="modal-body">
            <div class="general-tabs">
                <ul class="nav nav-tabs uploader-nav">
                    <li class="active">
                        <a href="#general-options" data-toggle="tab">
                            <i class="zmdi zmdi-settings"></i>
                            <?php echo JText::_('GENERAL'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#publishing-options" data-toggle="tab">
                            <i class="zmdi zmdi-calendar-note"></i>
                            <?php echo JText::_('PUBLISHING'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#seo-options" data-toggle="tab">
                            <i class="zmdi zmdi-globe"></i>
                            SEO
                        </a>
                    </li>
<?php
                    if ($user->authorise('core.admin', 'com_gridbox')) {
?>
                    <li>
                        <a href="#permissions-options" data-toggle="tab">
                            <i class="zmdi zmdi-account-circle"></i>
                            <?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?>
                        </a>
                    </li>
<?php
                    }
?>
                </ul>
                <div class="tabs-underline"></div>
                <div class="tab-content">
                    <div id="general-options" class="row-fluid tab-pane active">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JGLOBAL_TITLE'); ?><span class="required-fields-star">*</span>
                                </label>
                                <input type="hidden" name="ba_id" class="page-id">
                                <input type="text" name="page_title" class="page-title"
                                    placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
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
                                <input type="text" name="page_alias" class="page-alias"
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
                                    <input type="text" class="intro-image" name="intro_image"
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
                                    <?php echo JText::_('INTRO_TEXT'); ?>
                                </label>
                                <textarea placeholder="<?php echo JText::_('INTRO_TEXT'); ?>"
                                    name="intro_text" class="intro-text"></textarea>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('CATEGORY'); ?>
                                </label>
                                <div class="page-multicategory-select">
                                    <input readonly onfocus="this.blur()" type="text" value="">
                                    <input type="hidden" id="page-category" name="page_category" value="">
                                    <input type="hidden" name="page_categories" id="page-categories">
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group gridbox-page-tags-wrapper">
                            <div class="ba-group-element">
                                <div class="ba-tags">
                                    <label>
                                        <?php echo JText::_('TAGS'); ?>
                                    </label>
                                    <div class="meta-tags">
                                        <select style="display: none;" name="meta_tags[]" class="meta_tags" multiple></select>
                                        <ul class="picked-tags">
                                            <li class="search-tag">
                                                <input type="text" placeholder="<?php echo JText::_('TAGS'); ?>">
                                            </li>
                                        </ul>
                                        <div class="select-post-tags input-action-icon">
                                            <i class="zmdi zmdi-playlist-plus"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('TAGS'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('TAGS_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('THEME'); ?>
                                </label>
                                <div class="ba-custom-select theme-select visible-select-top">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="theme_list" class="theme-list" value="">
                                    <ul>
                                        <?php
                                        foreach ($this->themes as $theme) {
                                            $str = '<li data-value="'.$theme->id.'">';
                                            $str .= $theme->title.'</li>';
                                            echo $str;
                                        }
                                        ?>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('CLASS_SUFFIX'); ?>
                                </label>
                                <input type="text" class="page-class-suffix" 
                                    placeholder="<?php echo JText::_('CLASS_SUFFIX'); ?>" name="class_suffix">
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('CLASS_SUFFIX_TOOLTIP'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="publishing-options" class="row-fluid tab-pane">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JTOOLBAR_PUBLISH'); ?>
                                </label>
                                <label class="ba-checkbox ba-hide-checkbox">
                                    <input type="checkbox" name="publish" class="publish" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
                                </label>
                                <div class="ba-custom-select access-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="access" id="access" value="">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
                                        <?php
                                        foreach ($this->access as $key => $access) {
                                            $str = '<li data-value="'.$key.'">';
                                            $str .= $access.'</li>';
                                            echo $str;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('JFIELD_ACCESS_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('START_PUBLISHING'); ?>
                                </label>
                                <div class="container-icon">
                                    <input type="text" name="published_on" id="published_on">
                                    <div class="icons-cell" id="calendar-button">
                                        <i class="zmdi zmdi-calendar-alt"></i>
                                    </div>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('START_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('END_PUBLISHING'); ?>
                                </label>
                                <div class="container-icon">
                                    <input type="text" name="published_down" id="published_down">
                                    <div class="icons-cell" id="calendar-down-button">
                                        <i class="zmdi zmdi-calendar-alt"></i>
                                    </div>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('END_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element ba-author-element-wrapper">
                                <label>
                                    <?php echo JText::_('AUTHOR'); ?>
                                </label>
                                <div class="ba-custom-author-select-wrapper">
                                    <div class="ba-custom-author-select select-post-author">
                                        <input readonly type="text" placeholder="<?php echo JText::_('AUTHOR'); ?>">
                                        <input type="hidden" name="author">
                                        <ul>
                                            <?php
                                            foreach ($this->authors as $author) {
                                                if (empty($author->avatar)) {
                                                    $author->avatar = 'components/com_gridbox/assets/images/default-user.png';
                                                }
                                                $str = '<li data-value="'.$author->id.'" data-image="'.JUri::root().$author->avatar;
                                                $str .= '"><span class="ba-author-avatar" ';
                                                $str .= 'style="background-image: url('.JUri::root();
                                                $str .= str_replace(' ', '%20', $author->avatar).')"></span>';
                                                $str .= $author->title.'</li>';
                                                echo $str;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>
                                </label>
                                <div class="ba-custom-select language-select visible-select-top">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="language" id="language" value="">
                                    <ul>
<?php
                                    foreach ($this->languages as $key => $language) {
                                        $style = $key == '*' ? '' : ' style="--flag-img: url('.$flags.$key.'.png)"';
?>
                                        <li data-value="<?php echo $key; ?>"<?php echo $style; ?>>
                                            <?php echo $language; ?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('LANGUAGE_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
<?php
                    if (JLanguageAssociations::isEnabled()) {
?>
                        <div class="ba-options-group language-associations-group">
<?php
                        foreach ($this->languages as $key => $language) {
                            if ($key == '*') {
                                continue;
                            }
?>
                            <div class="ba-group-element" data-lang="<?php echo $key; ?>">
                                <label>
                                    <?php echo JText::_('ASSOCIATIONS'); ?>
                                </label>
                                <div class="association-wrapper">
                                    <span class="ba-language-flag"
                                        style="background-image: url(<?php echo $flags.$key; ?>.png);">
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo $language; ?>
                                        </span>
                                    </span>
                                    <input type="text" class="association-page"
                                        placeholder="<?php echo JText::_('SELECT'); ?>" readonly="" onfocus="this.blur()">
                                    <div class="reset disabled-reset reset-association">
                                        <i class="zmdi zmdi-close"></i>
                                    </div>
                                </div>
                            </div>
<?php
                        }
?>
                        </div>
<?php
                    }
?>
                    </div>
                    <div id="seo-options" class="row-fluid tab-pane left-tabs-wrapper">
                        <div class="left-tabs">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#seo-general-options" data-toggle="tab">
                                        <i class="zmdi zmdi-settings"></i>
                                        <?php echo JText::_('BASIC'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#sharing-options" data-toggle="tab">
                                        <i class="zmdi zmdi-share"></i>
                                        <?php echo JText::_('SHARING'); ?>
                                    </a>
                                </li>
<?php
                            if (gridboxHelper::checkSystemApp('sitemap')) {
?>
                                <li>
                                    <a href="#sitemap-options" data-toggle="tab">
                                        <i class="zmdi zmdi-device-hub"></i>
                                        <?php echo JText::_('SITEMAP'); ?>
                                    </a>
                                </li>
<?php
                            }
?>
                                <li>
                                    <a href="#schema-markup" data-toggle="tab">
                                        <i class="zmdi zmdi-code"></i>
                                        <?php echo JText::_('SCHEMA_MARKUP'); ?>
                                    </a>
                                </li>
                            </ul>
                            <span class="seo-default-settings" data-type="page">
                                <i class="zmdi zmdi-globe"></i>
                                <?php echo JText::_('DEFAULT_SETTINGS'); ?>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT_SETTINGS_TOOLTIP'); ?></span>
                            </span>
                            <div class="tab-content">
                                <div id="seo-general-options" class="row-fluid tab-pane active">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('BROWSER_PAGE_TITLE'); ?>
                                            </label>
                                            <input type="text" name="page_meta_title" class="page-meta-title"
                                                placeholder="<?php echo JText::_('BROWSER_PAGE_TITLE'); ?>">
                                            <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
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
                                            <textarea name="page_meta_description" class="page-meta-description"
                                                placeholder="<?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>"></textarea>
                                            <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
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
                                            <textarea name="page_meta_keywords" class="page-meta-keywords"
                                                placeholder="<?php echo JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>"></textarea>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JFIELD_METADATA_ROBOTS_LABEL'); ?>
                                            </label>
                                            <div class="ba-custom-select robots-select visible-select-top">
                                                <input readonly value="" type="text">
                                                <input type="hidden" name="robots" id="robots" value="">
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
                                <div id="sharing-options" class="row-fluid tab-pane">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('IMAGE'); ?>
                                            </label>
                                            <div class="share-image-wrapper">
                                                <div class="image-field-tooltip"></div>
                                                <input type="text" class="share-image" name="share_image"
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
                                            <input type="text" name="share_title" class="share-title"
                                                placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                                            <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
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
                                            <textarea name="share_description" class="share-description"
                                                placeholder="<?php echo JText::_('DESCRIPTION'); ?>"></textarea>
                                            <div class="select-data-tags input-action-icon" data-template="page-data-tags-template">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="sitemap-options" class="row-fluid tab-pane">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('OVERRIDE_DEFAULT_SETTINGS'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" name="sitemap_override" value="1"
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
                                                    <input type="checkbox" name="sitemap_include" value="1"
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
                                                    <input type="hidden" name="changefreq" class="changefreq">
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
                                                    <input type="number" data-callback="emptyCallback" name="priority" class="priority">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="schema-markup" class="row-fluid tab-pane">
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
                                            <textarea name="schema_markup"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="permissions-options" class="row-fluid tab-pane permissions-options">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label><?php echo JText::_('USERGROUP'); ?></label>
                                <div class="ba-custom-select select-permission-usergroup">
<?php
                                    $userGroups = gridboxHelper::getUserGroups();
?>
                                    <input readonly="" onfocus="this.blur()" type="text">
                                    <input type="hidden">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
<?php
                                    foreach ($userGroups as $key => $group) {
                                        $str = '';
                                        for ($i = 0; $i < $group->level; $i++) {
                                            $str .= '- ';
                                        }
                                        if ($group->level != 0) {
                                            $str .= '-';
                                        }
?>
                                        <li data-value="<?php echo $group->id; ?>"
                                            style="--permissions-level: <?php echo $group->level; ?>; --content: '<?php echo $str; ?>';">
<?php
                                            echo $group->title;
?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                </div>
                            </div>
                            <div class="ba-subgroup-element visible-subgroup permission-action-wrapper">
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('DELETE'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.delete">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="allowed">
                                            <i class="zmdi zmdi-check-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('EDIT'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('JACTION_EDITSTATE'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit.state">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="category-settings-dialog" class="ba-modal-lg modal hide" style="display:none">
        <div class="modal-header">
            <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-check apply-blog-settings"></i>
                <i class="zmdi zmdi-check category-settings-apply"></i>
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
                        <a href="#category-publishing-options" data-toggle="tab">
                            <i class="zmdi zmdi-calendar-note"></i>
                            <?php echo JText::_('PUBLISHING'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#category-seo-options" data-toggle="tab">
                            <i class="zmdi zmdi-globe"></i>
                            SEO
                        </a>
                    </li>
<?php
                    if ($user->authorise('core.admin', 'com_gridbox')) {
?>
                    <li>
                        <a href="#category-permissions-options" data-toggle="tab">
                            <i class="zmdi zmdi-account-circle"></i>
                            <?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?>
                        </a>
                    </li>
<?php
                    }
?>
                </ul>
                <div class="tabs-underline"></div>
                <div class="tab-content">
                    <div id="category-general-options" class="row-fluid tab-pane active">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JGLOBAL_TITLE'); ?><span class="required-fields-star">*</span>
                                </label>
                                <input type="text" name="category_title" class="category-title"
                                    placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                                    <input type="hidden" name="category-id" class="category-id">
                                    <input type="hidden" name="category_parent" class="category-parent">
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
                                    <?php echo JText::_('THEME'); ?>
                                </label>
                                <div class="ba-custom-select blog-theme-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="blog_theme" class="blog-theme" value="">
                                    <ul>
                                        <?php
                                        foreach ($this->themes as $theme) {
                                            $str = '<li data-value="'.$theme->id.'">';
                                            $str .= $theme->title.'</li>';
                                            echo $str;
                                        }
                                        ?>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
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
                    <div id="category-publishing-options" class="row-fluid tab-pane">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JTOOLBAR_PUBLISH'); ?>
                                </label>
                                <label class="ba-checkbox ba-hide-checkbox">
                                    <input type="checkbox" name="category_publish" class="category-publish" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
                                </label>
                                <div class="ba-custom-select category-access-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="category_access" id="category-access" value="">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
                                        <?php
                                        foreach ($this->access as $key => $access) {
                                            $str = '<li data-value="'.$key.'">';
                                            $str .= $access.'</li>';
                                            echo $str;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('JFIELD_ACCESS_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>
                                </label>
                                <div class="ba-custom-select language-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" name="category_language" id="category-language" value="">
                                    <ul>
<?php
                                    foreach ($this->languages as $key => $language) {
                                        $style = $key == '*' ? '' : ' style="--flag-img: url('.$flags.$key.'.png)"';
?>
                                        <li data-value="<?php echo $key; ?>"<?php echo $style; ?>>
                                            <?php echo $language; ?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('LANGUAGE_DESC'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
<?php
                    if (JLanguageAssociations::isEnabled()) {
?>
                        <div class="ba-options-group language-associations-group">
<?php
                        foreach ($this->languages as $key => $language) {
                            if ($key == '*') {
                                continue;
                            }
?>
                            <div class="ba-group-element" data-lang="<?php echo $key; ?>">
                                <label>
                                    <?php echo JText::_('ASSOCIATIONS'); ?>
                                </label>
                                <div class="association-wrapper">
                                    <span class="ba-language-flag"
                                        style="background-image: url(<?php echo $flags.$key; ?>.png);">
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo $language; ?>
                                        </span>
                                    </span>
                                    <input type="text" class="association-page"
                                        placeholder="<?php echo JText::_('SELECT'); ?>" readonly="" onfocus="this.blur()">
                                    <div class="reset disabled-reset reset-association">
                                        <i class="zmdi zmdi-close"></i>
                                    </div>
                                </div>
                            </div>
<?php
                        }
?>
                        </div>
<?php
                    }
?>
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
                            <span class="seo-default-settings" data-type="category">
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
                                            <div class="select-data-tags input-action-icon" data-template="category-data-tags-template">
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
                                            <div class="select-data-tags input-action-icon" data-template="category-data-tags-template">
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
                                            <div class="select-data-tags input-action-icon" data-template="category-data-tags-template">
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
                                            <div class="select-data-tags input-action-icon" data-template="category-data-tags-template">
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
                                                <?php echo JText::_('INCLUDE_ITEM'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" name="category_sitemap_include" value="1"
                                                    class="sitemap-include ba-hide-element set-group-display">
                                                <span></span>
                                            </label>
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element">
                                                    <?php echo JText::_('INCLUDE_ITEM_TOOLTIP'); ?>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="ba-subgroup-element " style="--subgroup-childs:2;">
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
                                                    <input type="number" data-callback="emptyCallback" name="category_priority"
                                                        class="priority">
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
                                                <div class="select-data-tags input-action-icon" data-template="category-data-tags-template">
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
                    <div id="category-permissions-options" class="row-fluid tab-pane permissions-options">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label><?php echo JText::_('USERGROUP'); ?></label>
                                <div class="ba-custom-select select-permission-usergroup">
<?php
                                    $userGroups = gridboxHelper::getUserGroups();
?>
                                    <input readonly="" onfocus="this.blur()" type="text">
                                    <input type="hidden">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
<?php
                                    foreach ($userGroups as $key => $group) {
                                        $str = '';
                                        for ($i = 0; $i < $group->level; $i++) {
                                            $str .= '- ';
                                        }
                                        if ($group->level != 0) {
                                            $str .= '-';
                                        }
?>
                                        <li data-value="<?php echo $group->id; ?>"
                                            style="--permissions-level: <?php echo $group->level; ?>; --content: '<?php echo $str; ?>';">
<?php
                                            echo $group->title;
?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                </div>
                            </div>
                            <div class="ba-subgroup-element visible-subgroup permission-action-wrapper">
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('JACTION_CREATE'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.create">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="allowed">
                                            <i class="zmdi zmdi-check-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('DELETE'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.delete">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="allowed">
                                            <i class="zmdi zmdi-check-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('EDIT'); ?></label>
                                    <div class="ba-custom-select select-permission-action">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ba-group-element">
                                    <label><?php echo JText::_('JACTION_EDITSTATE'); ?></label>
                                    <div class="ba-custom-select select-permission-action visible-select-top">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit.state">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="ba-group-element">
                                    <label><?php echo JText::_('JACTION_EDITOWN'); ?></label>
                                    <div class="ba-custom-select select-permission-action visible-select-top">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit.own">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="ba-group-element">
                                    <label><?php echo JText::_('EDIT_LAYOUTS'); ?></label>
                                    <div class="ba-custom-select select-permission-action visible-select-top">
                                        <input readonly="" onfocus="this.blur()" type="text"
                                            value="<?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?>">
                                        <input type="hidden" data-key="core.edit.layouts">
                                        <i class="zmdi zmdi-caret-down"></i>
                                        <ul>
                                            <li data-value=""><?php echo JText::_('JLIB_FORM_VALUE_INHERITED'); ?></li>
                                            <li data-value="1"><?php echo JText::_('JLIB_RULES_ALLOWED'); ?></li>
                                            <li data-value="0"><?php echo JText::_('JLIB_RULES_DENIED'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="calculated-permission-wrapper">
                                        <span class="calculated-permission" data-status="not-allowed">
                                            <i class="zmdi zmdi-close-circle"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('JLIB_RULES_NOT_ALLOWED'); ?>
                                            </span>
                                        </span>
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
                            <h1><?php echo $this->blog->title; ?></h1>
                            <span class="blog-icons">
<?php
                if ($appAssets->checkPermission('core.edit') || $appAssets->checkPermission('core.edit.layouts')
                    || $user->authorise('core.duplicate', 'com_gridbox')) {
?>
                                <span class="ba-dashboard-popover-trigger" data-target="blog-settings-context-menu">
                                    <i class="zmdi zmdi-settings"></i>
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SETTINGS'); ?></span>
                                </span>
<?php
                }
?>
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
                            <div class="enable-custom-pages-order<?php echo $listOrder == 'order_list' ? ' active' : ''; ?>">
                                <i class="zmdi zmdi-format-line-spacing"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DRAG_DROP_SORT_ITEMS'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <div class="category-list">
                            <a class="create-categery" href="#"<?php echo !$create ? ' data-permitted="false"': ''; ?>>
                                + <?php echo JText::_('CATEGORY'); ?>
                            </a>
                            <ul class="root-list">
                                <li class="root <?php echo $this->root; ?>">
                                    <a href="index.php?option=com_gridbox&view=apps&id=<?php echo $this->blog->id; ?>">
                                        <label><i class="zmdi zmdi-folder"></i></label><span><?php echo JText::_('ROOT'); ?></span>
                                    </a>
                                    <?php echo $this->drawCategoryList($this->categories); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="span9 blog-layout">
                        <div class="main-table pages-list">
<?php
                            if ($this->blog->type == 'products') {
                                include JPATH_COMPONENT.'/views/apps/tmpl/products-table.php';
                            } else if ($this->blog->type == 'booking') {
                                include JPATH_COMPONENT.'/views/apps/tmpl/booking-table.php';
                            } else {
                                include JPATH_COMPONENT.'/views/apps/tmpl/apps-table.php';
                            }
?>
                        </div>
<?php
                        echo $this->pagination->getListFooter();
                        if ($create && !empty($this->category) && $this->blog->type != 'products') {
?>
                        <div class="ba-create-item">
                            <a href="<?php echo $url; ?>" target="_blank">
                                <i class="zmdi zmdi-file"></i>
                            </a>
                            <span class="ba-tooltip ba-top ba-hide-element align-center">
                                <?php echo JText::_('ADD_NEW_ITEM'); ?>
                            </span>
                        </div>
<?php
                        } else if ($create && !empty($this->category)) {
?>
                        <div class="ba-create-item ba-create-store-product">
                            <a href="#" target="_blank">
                                <i class="zmdi zmdi-file"></i>
                            </a>
                        </div>
                        <div class="ba-select-store-product-type">
                            <a href="<?php echo str_replace('{product_type}', 'physical', $url); ?>"
                                target="_blank" data-type="physical">
                                <i class="zmdi zmdi-shopping-basket"></i>
                                <span class="ba-tooltip ba-left ba-hide-element">
                                    <?php echo JText::_('PHYSICAL'); ?>
                                </span>
                            </a>
                            <a href="<?php echo str_replace('{product_type}', 'digital', $url); ?>"
                                target="_blank" data-type="digital">
                                <i class="zmdi zmdi-cloud-download"></i>
                                <span class="ba-tooltip ba-left ba-hide-element">
                                    <?php echo JText::_('DIGITAL'); ?>
                                </span>
                            </a>
                            <a href="<?php echo str_replace('{product_type}', 'subscription', $url); ?>"
                                target="_blank" data-type="subscription">
                                <i class="zmdi zmdi-time-restore"></i>
                                <span class="ba-tooltip ba-left ba-hide-element">
                                    <?php echo JText::_('SUBSCRIPTION'); ?>
                                </span>
                            </a>
                        </div>
<?php
                        }
                        if ($create && empty($this->category)) {
?>
                        <div class="ba-create-item ba-uncategorised">
                            <a href="#" onclick="return false;">
                                <i class="zmdi zmdi-file"></i>
                            </a>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_ITEM'); ?></span>
                        </div>
<?php
                        }
?>
                    </div>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="blog" value="<?php echo $this->blog->id; ?>" />
                        <input type="hidden" name="task" value=""/>
                        <input type="hidden" value='<?php echo htmlspecialchars(json_encode($this->blog), ENT_QUOTES); ?>'
                            id="blog-data">
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="ba_category" value="<?php echo $this->category; ?>">
                        <input type="hidden" name="category_order_list" value="1">
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="author_filter" value="<?php echo $authorState; ?>">
                        <input type="hidden" name="theme_filter" value="<?php echo $themeState; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="access_filter" value="<?php echo $accessState; ?>">
                        <input type="hidden" name="ba_view" value="apps">
<?php
                        echo JHtml::_('form.token');
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="page-settings"><i class="zmdi zmdi-settings"></i><?php echo JText::_('SETTINGS'); ?></span>
    <span class="view-frontend-page"><i class="zmdi zmdi-eye"></i><?php echo JText::_('VIEW_PAGE'); ?></span>
    <span class="blog-duplicate"><i class="zmdi zmdi-copy"></i><?php echo JText::_('DUPLICATE'); ?></span>
    <span class="page-move"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
    <span class="blog-trash ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('TRASH'); ?></span>
</div>
<div class="ba-context-menu category-context-menu" style="display: none">
    <span class="category-settings"><i class="zmdi zmdi-settings"></i><?php echo JText::_('SETTINGS'); ?></span>
    <span class="category-duplicate"><i class="zmdi zmdi-copy"></i><?php echo JText::_('DUPLICATE'); ?></span>
    <span class="category-move"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
    <span class="category-delete ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<div class="ba-dashboard-apps-dialog blog-settings-context-menu">
    <div class="ba-dashboard-apps-body">
<?php
    if ($appAssets->checkPermission('core.edit')) {
?>
        <div class="ba-gridbox-dashboard-row blog-settings">
            <i class="zmdi zmdi-settings"></i>
            <span><?php echo JText::_('SETTINGS'); ?></span>
        </div>
<?php
    }
    if ($appAssets->checkPermission('core.edit.layouts')) {
?>
        <div class="ba-gridbox-dashboard-row context-link-wrapper">
            <a href="<?php echo $editBlog; ?>" class="default-action" target="_blank">
                <i class="zmdi zmdi-file-text"></i>
                <span><?php echo JText::_('CATEGORY_LIST_LAYOUT') ?></span>
            </a>
        </div>
        <div class="ba-gridbox-dashboard-row context-link-wrapper">
            <a href="<?php echo $editPostLayout; ?>" class="default-action single-post-layout" target="_blank">
                <i class="zmdi zmdi-file"></i>
                <span><?php echo JText::_('SINGLE_POST_LAYOUT') ?></span>
            </a>
        </div>
<?php
    }
    if ($user->authorise('core.duplicate', 'com_gridbox')) {
?>
        <div class="ba-gridbox-dashboard-row app-duplicate ba-group-element">
            <i class="zmdi zmdi-copy"></i>
            <span><?php echo JText::_('JTOOLBAR_DUPLICATE'); ?></span>
        </div>
<?php
    }
?>
    </div>
</div>
<div id="data-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">
    <div class="modal-body">
        
    </div>
</div>
<div id="post-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">
    <div class="modal-body">
        <div class="data-tags-searchbar">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-select-type select-data-tags-wrapper">
                    <select class="select-data-tags-type">
<?php
                    foreach ($this->tagsFolders->folders as $folder) {
?>
                        <option value="<?php echo $folder->id; ?>"><?php echo $folder->title; ?></option>
<?php
                    }
?>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type search-tags-wrapper">
                    <input type="text" class="search-post-tags" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>">
                    <i class="zmdi zmdi-search"></i>
                </div>
            </div>
        </div>
        <div class="post-tags-wrapper">
<?php
        foreach ($this->tagsFolders->tags as $tag) {
?>
            <div class="ba-settings-item ba-settings-input-type" data-folder="<?php echo $tag->folder_id; ?>"
                data-id="<?php echo $tag->id; ?>">
                <i class="zmdi zmdi-label"></i>
                <span class="ba-settings-item-title"><?php echo $tag->title; ?></span>
            </div>
<?php
        }
?>
        </div>
    </div>
</div>
<template class="page-data-tags-template">
    <div class="data-tags-searchbar">
        <div class="ba-settings-group">
            <div class="ba-settings-item ba-settings-select-type">
                <select class="select-data-tags-type">
                    <option value=""><?php echo JText::_('All'); ?></option>
                    <option value="general"><?php echo JText::_('GENERAL'); ?></option>
                    <option value="page"><?php echo JText::_('PAGE'); ?></option>
<?php
                if ($this->blog->type == 'products') {
?>
                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                    <option value="store"><?php echo JText::_('STORE'); ?></option>
<?php
                }
                if ($this->blog->type != 'blog') {
?>
                    <option value="fields"><?php echo JText::_('FIELDS'); ?></option>
<?php
                }
?>
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
        <div class="ba-settings-group page-data-tags">
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PAGE_TITLE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page Title]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PAGE_IMAGE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page Image]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PAGE_URL'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page URL]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('CATEGORY_TITLE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Category Title]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PAGE_TAGS'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page Tags]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('AUTHOR'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Author]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('START_PUBLISHING'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Start Publishing]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('DATE_MODIFIED'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Date Modified]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('INTRO_TEXT'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Intro Text]">
            </div>
        </div>
<?php
    if ($this->blog->type == 'products') {
?>
        <div class="ba-settings-group product-data-tags">
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_SKU'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product SKU]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_PRICE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product Price]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_SALE_PRICE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product Sale Price]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_CURRENCY'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product Currency]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('PRODUCT_IN_STOCK'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Product In Stock]">
            </div>
        </div>
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
        </div>
<?php
    }
    if ($this->blog->type != 'blog') {
?>
        <div class="ba-settings-group fields-data-tags">
<?php
        foreach ($this->fields as $field) {
?>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('FIELD').' '.$field->title; ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Field <?php echo $field->id; ?>]">
            </div>
<?php
        }
?>
        </div>
<?php
    }
?>
    </div>
</template>
<template class="category-data-tags-template">
    <div class="data-tags-searchbar">
        <div class="ba-settings-group">
            <div class="ba-settings-item ba-settings-select-type">
                <select class="select-data-tags-type">
                    <option value=""><?php echo JText::_('All'); ?></option>
                    <option value="general"><?php echo JText::_('GENERAL'); ?></option>
                    <option value="category"><?php echo JText::_('CATEGORY'); ?></option>
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
        <div class="ba-settings-group category-data-tags">
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('CATEGORY_TITLE'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Category Title]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('CATEGORY_IMAGE_URL'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Category Image URL]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('CATEGORY_PAGE_URL'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Category Page URL]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('CATEGORY_DESCRIPTION'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Category Description]">
            </div>
        </div>
    </div>
</template>
<template class="category-sitemap-template">
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
</template>
<template class="apps-sitemap-template">
    <div class="ba-options-group">
        <div class="ba-group-element">
            <label>
                <?php echo JText::_('INCLUDE_ITEM'); ?>
            </label>
            <label class="ba-checkbox">
                <input type="checkbox" name="category_sitemap_include" value="1"
                    class="sitemap-include ba-hide-element set-group-display">
                <span></span>
            </label>
            <label class="ba-help-icon">
                <i class="zmdi zmdi-help"></i>
                <span class="ba-tooltip ba-help ba-hide-element">
                    <?php echo JText::_('INCLUDE_ITEM_TOOLTIP'); ?>
                </span>
            </label>
        </div>
        <div class="ba-subgroup-element " style="--subgroup-childs:2;">
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
                    <input type="number" data-callback="emptyCallback" name="category_priority"
                        class="priority">
                </div>
            </div>
        </div>
    </div>
</template>
<template class="page-multicategory-list">
    <ul class="page-multicategory-list">
<?php
        foreach ($this->categoryList as $key => $category) {
            $content = '';
            for ($i = 0; $i < $category->level; $i++) {
                $content .= '- ';
            }
            if ($category->level != 0) {
                $content .= '-';
            }
?>
            <li data-value="<?php echo $category->id; ?>" style="--content: '<?php echo $content; ?>';">
                <label class="ba-hide-checkbox">
                    <input type="checkbox" value="<?php $category->id; ?>">
                    <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                    <i class="zmdi zmdi-check ba-icon-md"></i>
                </label>
                <span class="multicategory-title"><?php echo $category->title; ?></span>
                <span class="set-default-page-category">
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT'); ?></span>
                    <i class="zmdi zmdi-star"></i>
                </span>
            </li>
<?php
        }
?>
    </ul>
</template>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/photo-editor.php');
if ($this->blog->type == 'products') {
?>
<div id="import-export-csv-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="import-export-csv-tabs-wrapper">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#import-csv-tab" data-toggle="tab">
                        <i class="zmdi zmdi-assignment"></i>
                    </a>
                </li>
                <li>
                    <a href="#export-csv-tab" data-toggle="tab">
                        <i class="zmdi zmdi-inbox"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="import-csv-tab">
                    <div class="tab-body">
                        <div class="csv-import-step-1">
                            <h3><?php echo JText::_('IMPORT'); ?></h3>
                            <div class="csv-import-options csv-content-wrapper" data-key="csv-import-options"></div>
                        </div>
                        <div class="csv-import-step-2" style="display: none;">
                            <h3><?php echo JText::_('MATCH_FIELDS'); ?></h3>
                            <div class="csv-match-fields csv-content-wrapper" data-key="csv-match-fields"></div>
                        </div>
                        <div class="csv-import-step-3" style="display: none;">
                            <h3><?php echo JText::_('PRELIMINARY_IMPORT_CHECK'); ?></h3>
                            <div class="csv-content-wrapper" data-key="csv-import-check"></div>
                        </div>
                    </div>
                    <div class="tab-footer">
                        <a href="#" class="ba-btn disabled-button csv-import-back">
                            <?php echo JText::_('BACK') ?>
                        </a>
                        <a href="#" class="ba-btn" data-dismiss="modal">
                            <?php echo JText::_('CANCEL') ?>
                        </a>
                        <a href="#" class="ba-btn-primary apply-csv-import">
                            <?php echo JText::_('NEXT') ?>
                        </a>
                    </div>
                </div>
                <div class="tab-pane" id="export-csv-tab">
                    <div class="tab-body">
                        <h3><?php echo JText::_('EXPORT'); ?></h3>
                        <div class="csv-export-fields csv-content-wrapper" data-key="csv-export-fields"></div>
                    </div>
                    <div class="tab-footer">
                        <a href="#" class="ba-btn" data-dismiss="modal">
                            <?php echo JText::_('CANCEL') ?>
                        </a>
                        <a href="#" class="ba-btn-primary active-button apply-export-csv">
                            <?php echo JText::_('EXPORT') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="csv-import-error-log-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="ba-modal-header">
            <h3><?php echo JText::_('ERRORS'); ?></h3>
            <i data-dismiss="modal" class="zmdi zmdi-close"></i>
        </div>
        <div class="csv-error-log-table">
            <div class="csv-error-log-thead">
                <div class="csv-error-log-row">
                    <div class="csv-error-log-cell"><?php echo JText::_('COLUMN'); ?></div>
                    <div class="csv-error-log-cell"><?php echo JText::_('LINE'); ?></div>
                    <div class="csv-error-log-cell"></div>
                </div>
            </div>
            <div class="csv-error-log-tbody csv-content-wrapper" data-key="csv-error-log-row"></div>
        </div>
    </div>
</div>
<template data-key="csv-error-log-row" class="csv-template">
    <div class="csv-error-log-row">
        <div class="csv-error-log-cell" data-key="column"></div>
        <div class="csv-error-log-cell" data-key="line"></div>
        <div class="csv-error-log-cell" data-key="code"><span></span></div>
    </div>
</template>
<template data-key="csv-import-check" class="csv-template">
    <span class="csv-import-check-field" data-type="new">
        <label class="ba-options-group-label"><?php echo JText::_('NEW_PRODUCTS'); ?></label>
        <span class="csv-import-status-color"></span>
    </span>
    <span class="csv-import-check-field" data-type="updated">
        <label class="ba-options-group-label"><?php echo JText::_('UPDATED_PRODUCTS'); ?></label>
        <span class="csv-import-status-color"></span>
    </span>
    <span class="csv-import-check-field" data-type="errors">
        <label class="ba-options-group-label"><?php echo JText::_('ERRORS'); ?></label>
        <span class="csv-import-status-text"><?php echo JText::_('VIEW_ERRORS'); ?></span>
        <span class="csv-import-status-color"></span>
    </span>
</template>
<template data-key="csv-export-field" class="csv-template">
    <span class="csv-export-field toggle-button-wrapper">
        <label class="csv-export-field-title ba-options-group-label"></label>
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" checked>
            <span></span>
        </label>
    </span>
</template>
<template data-key="csv-import-options" class="csv-template">
    <div class="ba-options-group-element">
        <select class="csv-file-type">
            <option value="match"><?php echo JText::_('MATCH_FIELDS'); ?></option>
            <option value="gridbox"><?php echo JText::_('GRIDBOX_CSV_FILE'); ?></option>
        </select>
    </div>
    <div class="ba-options-group-element">
        <input class="trigger-csv-import" readonly type="text"
            placeholder="<?php echo JText::_('SELECT_CSV_FILE'); ?>">
        <i class="zmdi zmdi-attachment-alt"></i>
        <input type="file" accept=".csv" style="display: none;">
    </div>
    <div class="ba-checkbox-parent">
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" class="import-property" data-key="backup">
            <span></span>
        </label>
        <span><?php echo JText::_('CREATE_STORE_BACKUP_BEFORE_IMPORT'); ?></span>
    </div>
    <div class="ba-checkbox-parent">
        <label class="ba-checkbox ba-hide-checkbox">
            <input type="checkbox" class="import-property" data-key="overwrite">
            <span></span>
        </label>
        <span><?php echo JText::_('OVERWRITE_PRODUCTS_WITH_SAME_ID'); ?></span>
    </div>
</template>
<template data-key="csv-match-field" class="csv-template">
    <div class="ba-options-group-element">
        <span class="ba-options-group-element-title"></span>
        <select class="csv-file-type"></select>
    </div>
</template>
<?php
}