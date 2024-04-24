<?php
/**
* @package   gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$sortFields = $this->getSortFields();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$user = JFactory::getUser();
$newPage = JUri::root().'index.php?option=com_gridbox&view=editor&tmpl=component&edit_type=system&id=';
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
$flags = JUri::root().'components/com_gridbox/assets/images/flags/';
$editPage = gridboxHelper::getEditorLink().'&edit_type=system&id=';
?>
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js"
    type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>"
    type="text/javascript"></script>
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=system'); ?>" method="post" name="adminForm"
    id="adminForm">
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
    <div id="system-settings-dialog" class="ba-modal-lg modal hide" style="display:none">
        <div class="modal-header">
            <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-check apply-system-settings"></i>
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
                    <li class="languages-options">
                        <a href="#languages-options" data-toggle="tab">
                            <i class="zmdi zmdi-globe"></i>
                            <?php echo JText::_('LANGUAGES'); ?>
                        </a>
                    </li>
                    <li class="submission-form-options">
                        <a href="#publishing-options" data-toggle="tab">
                            <i class="zmdi zmdi-calendar-note"></i>
                            <?php echo JText::_('PUBLISHING'); ?>
                        </a>
                    </li>
                </ul>
                <div class="tabs-underline"></div>
                <div class="tab-content">
                    <div id="general-options" class="row-fluid tab-pane active">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JGLOBAL_TITLE'); ?><span class="required-fields-star">*</span>
                                </label>
                                <input type="text" class="page-title"
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
                                <input type="text" class="page-alias"
                                    placeholder="<?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>">
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('THEME'); ?>
                                </label>
                                <div class="ba-custom-select theme-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" class="page-theme" value="">
                                    <ul>
<?php
                                    foreach ($this->themes as $theme) {
?>
                                        <li data-value="<?php echo $theme->id; ?>">
                                            <?php echo $theme->title; ?>
                                        </li>
<?php
                                    }
?>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ba-options-group error-page-settings">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('ENABLE_HEADER_FOOTER'); ?>
                                </label>
                                <label class="ba-checkbox">
                                    <input type="checkbox" class="page-enable-header ba-hide-element">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('CLASS_SUFFIX'); ?>
                                </label>
                                <input type="text" class="page-class-suffix" 
                                    placeholder="<?php echo JText::_('CLASS_SUFFIX'); ?>">
                                <label class="ba-help-icon">
                                    <i class="zmdi zmdi-help"></i>
                                    <span class="ba-tooltip ba-help ba-hide-element">
                                        <?php echo JText::_('CLASS_SUFFIX_TOOLTIP'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="languages-options" class="row-fluid tab-pane">
                        <div class="ba-options-group">
                            <div class="ba-group-element">
                                <label>
                                    <?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>
                                </label>
                                <div class="ba-custom-select language-select">
                                    <input readonly value="" type="text">
                                    <input type="hidden" value="" class="page-language">
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
                    <div id="publishing-options" class="row-fluid tab-pane left-tabs-wrapper">
                        <div class="left-tabs">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#publishing-basic-options" data-toggle="tab">
                                        <i class="zmdi zmdi-settings"></i>
                                        <?php echo JText::_('BASIC'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#publishing-notifications-options" data-toggle="tab">
                                        <i class="zmdi zmdi-notifications"></i>
                                        <?php echo JText::_('NOTIFICATIONS'); ?>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="publishing-basic-options" class="row-fluid tab-pane active">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('SUBMISSINS_PREMODERATION'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" value="1" class="submission-form-moderation ba-hide-element set-group-display">
                                                <span></span>
                                            </label>
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element"><?php echo JText::_('SUBMISSINS_PREMODERATION_HELP'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('ASSIGN_USER_AS_AUTHOR'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" value="1" class="submission-form-author ba-hide-element set-group-display">
                                                <span></span>
                                            </label>
                                            <label class="ba-help-icon">
                                                <i class="zmdi zmdi-help"></i>
                                                <span class="ba-tooltip ba-help ba-hide-element"><?php echo JText::_('ASSIGN_USER_AS_AUTHOR_TOOLTIP'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
                                            </label>
                                            <div class="ba-custom-select submission-form-access">
                                                <input readonly type="text">
                                                <input type="hidden">
                                                <i class="zmdi zmdi-caret-down"></i>
                                                <ul>
<?php
                                                    foreach ($this->access as $key => $access) {
                                                        echo '<li data-value="'.$key.'">'.$access.'</li>';
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
                                                <input type="hidden" value="" class="page-language">
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
                                <div id="publishing-notifications-options" class="row-fluid tab-pane">
                                    <div class="ba-options-group">
                                        <div class="ba-group-element">
                                            <label>
                                                <?php echo JText::_('EMAIL_NOTIFICATIONS'); ?>
                                            </label>
                                            <label class="ba-checkbox">
                                                <input type="checkbox" value="1" class="submission-form-notifications ba-hide-element set-group-display">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-subgroup-element " style="--subgroup-childs:2;">
                                            <div class="ba-group-element">
                                                <label>
                                                    <?php echo JText::_('NEW_SUBMISSION'); ?>
                                                </label>
                                                <label class="ba-checkbox">
                                                    <input type="checkbox" value="1" class="submission-form-submited ba-hide-element set-group-display">
                                                    <span></span>
                                                </label>
                                                <label class="ba-help-icon">
                                                    <i class="zmdi zmdi-help"></i>
                                                    <span class="ba-tooltip ba-help ba-hide-element"><?php echo JText::_('NEW_SUBMISSION_HELP'); ?></span>
                                                </label>
                                            </div>
                                            <div class="ba-group-element">
                                                <label>
                                                    <?php echo JText::_('SUBMISSION_PUBLISHING'); ?>
                                                </label>
                                                <label class="ba-checkbox">
                                                    <input type="checkbox" value="1" class="submission-form-publishing ba-hide-element set-group-display">
                                                    <span></span>
                                                </label>
                                                <label class="ba-help-icon">
                                                    <i class="zmdi zmdi-help"></i>
                                                    <span class="ba-tooltip ba-help ba-hide-element"><?php echo JText::_('SUBMISSION_PUBLISHING_HELP'); ?></span>
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
    </div>
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('SYSTEM_PAGES') ?></h1>
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
                                    <th class="<?php echo $listOrder == 'theme' ? 'active' : ''; ?>">
                                        <span data-sorting="theme">
                                            <?php echo JText::_('THEME'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
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
                                    if (($item->type == 'search' && !gridboxHelper::checkInstalledBlog())
                                        || (($item->type == 'checkout' || $item->type == 'thank-you-page' || $item->type == 'store-search')
                                            && (!gridboxHelper::checkInstalledBlog('products') && !gridboxHelper::checkInstalledBlog('booking')))) {
                                        continue;
                                    }
                                    $canChange = $user->authorise('core.edit.state', 'com_gridbox');
                                    $str = json_encode($item);
?>
                                <tr>
                                    <td class="select-td">
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
                                        echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'system.', $canChange);
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
                                        <a target="_blank"
                                           href="<?php echo $editPage.$item->id; ?>">
                                            <?php echo $item->title; ?>
                                            <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
                                        </a>
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
                                    <td>
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
<?php
                    echo $this->pagination->getListFooter();
                    if ($user->authorise('core.create', 'com_gridbox')) {
?>
                    <div class="ba-create-item">
                        <a href="<?php echo $newPage; ?>" target="_blank">
                            <i class="zmdi zmdi-file"></i>
                        </a>
                        <span class="ba-tooltip ba-top ba-hide-element align-center">
                            <?php echo JText::_('ADD_NEW_ITEM'); ?>
                        </span>
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
                        <input type="hidden" name="ba_view" value="system">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="page-settings" data-callback="showSystemSettings">
        <i class="zmdi zmdi-settings"></i>
        <?php echo JText::_('SETTINGS'); ?>
    </span>
    <span class="system-page-duplicate">
        <i class="zmdi zmdi-copy"></i>
        <?php echo JText::_('DUPLICATE'); ?>
    </span>
    <span class="system-page-trash ba-group-element">
        <i class="zmdi zmdi-delete"></i>
        <?php echo JText::_('TRASH'); ?>
    </span>
</div>
<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>
<?php include(JPATH_COMPONENT.'/views/layouts/photo-editor.php'); ?>