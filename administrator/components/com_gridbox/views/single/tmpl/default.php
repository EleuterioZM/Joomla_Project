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
$accessState = $this->state->get('filter.access');
$languageState = $this->state->get('filter.language');
$user = JFactory::getUser();
$flags = JUri::root().'components/com_gridbox/assets/images/flags/';
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
$url = gridboxHelper::getEditorLink().'&app_id='.$this->single->id.'&category=&id=';
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=single&id='.$this->single->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div id="single-settings-modal" class="ba-modal-sm modal ba-modal-dialog hide" style="display: none;">
        <div class="modal-header">
            <h3 class="ba-modal-header"><?php echo JText::_('SETTINGS'); ?></h3>
        </div>
        <div class="modal-body">
            <div>
                <input name="single_title" type="text" class="blog-title"
                    placeholder="<?php echo JText::_('JGLOBAL_TITLE'); ?>">
                <span class="focus-underline"></span>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
            <a href="#" class="ba-btn-primary apply-single-settings"><?php echo JText::_('JTOOLBAR_APPLY') ?></a>
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
                        <div style="display:none;">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('INTRO_TEXT'); ?>
                                </label>
                                <textarea name="intro_text" class="intro-text"></textarea>
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
                                <div style="display: none;">
                                    <input type="hidden" name="published_down" id="published_down">
                                </div>
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('START_DESC'); ?>
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
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element">
                                                    <?php echo JText::_('JFIELD_META_KEYWORDS_DESC'); ?>
                                                </span>
                                            </label>
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
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo $this->single->title; ?></h1>
                            <span class="blog-icons">
                                <span class="single-settings">
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
                            <div class="enable-custom-pages-order<?php echo $listOrder == 'order_list' ? ' active' : ''; ?>">
                                <i class="zmdi zmdi-format-line-spacing"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DRAG_DROP_SORT_ITEMS'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        
                    </div>
                    <div class="span9 blog-layout">
                        <div class="main-table pages-list">
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
                                                <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                            </span>
                                        </th>
                                        <th class="<?php echo $listOrder == 'created' ? 'active' : ''; ?>">
                                            <span data-sorting="created">
                                                <?php echo JText::_('DATE'); ?>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                            </span>
                                        </th>
                                        <th class="<?php echo $listOrder == 'theme' ? 'active' : ''; ?>">
                                            <span data-sorting="theme">
                                                <?php echo JText::_('THEME'); ?>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                            </span>
                                            <div class="theme-filter">
                                                <div class="ba-custom-select">
                                                    <input type="hidden" data-name="theme_filter" value="<?php echo $themeState; ?>">
                                                    <ul>
                                                        <li data-value=""><?php echo JText::_('THEME'); ?></li>
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
                                        </th>
                                        <th class="<?php echo $listOrder == 'language' ? 'active' : ''; ?>">
                                            <span data-sorting="language">
                                                <?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                            </span>
                                            <div class="language-filter">
                                                <div class="ba-custom-select">
                                                    <input type="hidden" data-name="language_filter" value="<?php echo $languageState; ?>">
                                                    <ul>
                                                        <li data-value=""><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></li>
                                                        <?php
                                                        foreach ($this->languages as $key => $language) {
                                                            $str = '<li data-value="'.$key.'">';
                                                            $str .= $language.'</li>';
                                                            echo $str;
                                                        }
                                                        ?>
                                                    </ul>
                                                    <i class="zmdi zmdi-caret-down"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="<?php echo $listOrder == 'page_access' ? 'active' : ''; ?>">
                                            <span data-sorting="page_access">
                                                <?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                            </span>
                                            <div class="access-filter">
                                                <div class="ba-custom-select">
                                                    <input type="hidden" data-name="access_filter" value="<?php echo $accessState; ?>">
                                                    <i class="zmdi zmdi-caret-down"></i>
                                                    <ul>
                                                        <li data-value=""><?php echo JText::_('JFIELD_ACCESS_LABEL'); ?></li>
                                                        <?php
                                                        foreach ($this->access as $key => $access) {
                                                            $str = '<li data-value="'.$key.'">';
                                                            $str .= $access.'</li>';
                                                            echo $str;
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
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
                                                <span class="ba-tooltip ba-top ba-hide-element ba-blog-id-tooltip">
                                                    <?php echo JText::_('SORT_BY_COLUMN'); ?>
                                                </span>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting">
<?php
                                foreach ($this->items as $i => $item) { 
                                    $str = json_encode($item);
                                    $timestamp = strtotime($item->created);
                                    $date = date('Y-m-d', $timestamp);
                                    $pageAssets = new gridboxAssetsHelper($item->id, 'page');
                                    $canChange = $pageAssets->checkPermission('core.edit.state');
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
                                        if ($canChange) {
                                            echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'pages.', $canChange);
                                        } else {
                                            $published = '<a class="disabled" href="javascript:void(0);"><i class="'.
                                                ($item->published == 1 ? 'zmdi zmdi-eye' : 'zmdi zmdi-eye-off').
                                                ' ba-icon-md"></i><span class="ba-tooltip ba-hide-element ba-top">'.
                                                ($item->published == 1 ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED')).'</span></a>';
                                            echo $published;
                                        }
?>
                                        </td>
                                        <td class="title-cell">
<?php
                                        if ($pageAssets->checkPermission('core.edit')) {
?>
                                            <a target="_blank"
                                               href="<?php echo 'index.php?option=com_gridbox&task=gridbox.edit&id='.$item->id; ?>">
                                        <?php
                                        }
?>
                                                <?php echo $item->title; ?>
                                                <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
<?php
                                        if ($pageAssets->checkPermission('core.edit')) {
?>
                                            </a>
<?php
                                        }
?>
                                        </td>
                                        <td class="date-cell">
                                            <?php echo $date; ?>
                                        </td>
                                        <td class="page-theme" data-theme="<?php echo $item->theme; ?>">
                                            <?php echo $item->themeName; ?>
                                        </td>
                                        <td class="page-language">
<?php
                                    if ($item->language == '*') {
                                        echo JText::_('JALL');
                                    } else {
                                        $src = JUri::root().'/components/com_gridbox/assets/images/flags/'.$item->language;
?>
                                        <span class="ba-language-flag" style="background-image: url(<?php echo $src; ?>.png);">
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo $this->languages[$item->language]; ?>
                                            </span>
                                        </span>
<?php
                                    }
?>
                                        </td>
                                        <td class="access-cell">
                                            <?php
                                                echo $this->access[$item->page_access];
                                            ?>
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
                        <div class="ba-create-item">
                            <a href="<?php echo $url; ?>" target="_blank">
                                <i class="zmdi zmdi-file"></i>
                            </a>
                            <span class="ba-tooltip ba-top ba-hide-element align-center">
                                <?php echo JText::_('ADD_NEW_ITEM'); ?>
                            </span>
                        </div>
                        <?php } ?>
                    </div>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="blog" value="<?php echo $this->single->id; ?>" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="theme_filter" value="<?php echo $themeState; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="access_filter" value="<?php echo $accessState; ?>">
                        <input type="hidden" value='<?php echo htmlspecialchars(json_encode($this->single), ENT_QUOTES); ?>' id="blog-data">
                        <input type="hidden" name="ba_view" value="single">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="page-settings"><i class="zmdi zmdi-settings"></i><?php echo JText::_('SETTINGS'); ?></span>
    <span class="view-frontend-page"><i class="zmdi zmdi-eye"></i><?php echo JText::_('VIEW_PAGE'); ?></span>
    <span class="page-duplicate"><i class="zmdi zmdi-copy"></i><?php echo JText::_('DUPLICATE'); ?></span>
    <span class="page-move-single"><i class="zmdi zmdi-forward"></i><?php echo JText::_('MOVE_TO'); ?>...</span>
    <span class="export-page"><i class="zmdi zmdi-download"></i><?php echo JText::_('EXPORT'); ?></span>
    <span class="page-trash ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('TRASH'); ?></span>
</div>
<div id="data-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">
    <div class="modal-body">
        
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
                <span class="ba-settings-item-title"><?php echo JText::_('START_PUBLISHING'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Start Publishing]">
            </div>
            <div class="ba-settings-item ba-settings-input-type">
                <span class="ba-settings-item-title"><?php echo JText::_('DATE_MODIFIED'); ?></span>
                <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Date Modified]">
            </div>
        </div>
    </div>
</template>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/photo-editor.php');