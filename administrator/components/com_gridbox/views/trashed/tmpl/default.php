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
$appState = $this->state->get('filter.app');
$themeState = $this->state->get('filter.theme');
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
<script src="<?php echo JUri::root(); ?>administrator/components/com_gridbox/assets/js/sortable.js" type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }    
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
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
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=trashed'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div id="move-to-modal" class="ba-modal-md modal hide" style="display:none">
        <div class="modal-body">
            <div class="ba-modal-header">
                <h3><?php echo JText::_('MOVE_TO'); ?></h3>
                <i data-dismiss="modal" class="zmdi zmdi-close"></i>
            </div>
            <div class="availible-folders">
                <ul class="root-list">
                    <li></li>
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
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('TRASHED_ITEMS'); ?></h1>
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
                    <div class="main-table trashed-list">
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
                                    <th class="<?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                                        <span data-sorting="title">
                                            <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                    </th>
                                    <th class="<?php echo $listOrder == 'app_id' ? 'active' : ''; ?>">
                                        <span data-sorting="app_id">
                                            <?php echo JText::_('APP'); ?>
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                        </span>
                                        <div class="state-filter">
                                            <div class="ba-custom-select">
                                                <input type="hidden" data-name="filter_state" value="<?php echo $appState; ?>">
                                                <ul>
                                                    <li data-value=""><?php echo JText::_('APP'); ?></li>
                                                    <?php
                                                    foreach ($this->apps as $obj) {
                                                    	if ($obj->type == 'tags') {
                                                    		continue;
                                                    	}
                                                    	$str = '<li data-value="'.$obj->id.'">';
                                                        $str .= $obj->title.'</li>';
                                                        echo $str;
                                                    }
                                                    ?>
                                                </ul>
                                                <i class="zmdi zmdi-caret-down"></i>
                                            </div>
                                        </div>
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
                            <tbody>
                               <?php foreach ($this->items as $i => $item) { 
                                        $str = json_encode($item);
                                        $canChange = $user->authorise('core.edit.state', 'com_gridbox'); ?>
                                <tr data-type="<?php echo $item->app_type; ?>">
                                    <td class="select-td">
                                        <label class="ba-hide-checkbox">
                                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                            <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                            <i class="zmdi zmdi-check ba-icon-md"></i>
                                        </label>
                                        <input type="hidden"
                                               value='<?php echo htmlspecialchars($str, ENT_QUOTES); ?>'>
                                    </td>
                                    <td class="title-cell">
                                        <?php echo $item->title; ?>
                                    </td>
                                    <td class="app-cell">
                                        <?php
                                            echo $item->app_name;
                                        ?>
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
                                                <?php echo $item->language; ?>
                                            </span>
                                        </span>
<?php
                                    }
?>
                                    </td>
                                    <td class="hits-cell">
<?php
                                        echo $item->hits != -1 ? $item->hits : '';
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
?>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                        <input type="hidden" name="theme_filter" value="<?php echo $themeState; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="filter_state" value="<?php echo $appState; ?>">
                        <input type="hidden" name="ba_view" value="trashed">
                        <input type="hidden" name="types" value="">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="ba-context-menu page-context-menu" style="display: none">
    <span class="trashed-restore">
        <i class="zmdi zmdi-time-restore"></i>
        <?php echo JText::_('RESTORE'); ?>
    </span>
    <span class="trashed-delete ba-group-element">
        <i class="zmdi zmdi-delete"></i>
        <?php echo JText::_('DELETE'); ?>
    </span>
</div>
<div class="ba-context-menu system-page-context-menu" style="display: none">
    <span class="system-restore">
        <i class="zmdi zmdi-time-restore"></i>
        <?php echo JText::_('RESTORE'); ?>
    </span>
    <span class="system-page-delete ba-group-element">
        <i class="zmdi zmdi-delete"></i>
        <?php echo JText::_('DELETE'); ?>
    </span>
</div>
<?php include(JPATH_COMPONENT.'/views/layouts/context.php'); ?>