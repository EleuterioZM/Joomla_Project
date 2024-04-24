<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$state = $this->state->get('filter.state');

$title = $this->state->get('filter.title');

$user = JFactory::getUser();
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
    0 => JText::_('JALL')
];
if (!isset($pagLimit[$limit])) {
    $limit = 0;
}
if (empty($title)) {
    $title = '*';
}
?>
<script src="components/com_baforms/assets/js/ba-about.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
<div id="export-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3 class="ba-modal-header"><?php echo JText::_('EXPORT'); ?></h3>
    </div>
    <div class="modal-body">
        <div>
            <ul>
                <li>
                    <label>
                        CSV
                    </label>
                    <label class="ba-radio ba-hide-checkbox">
                        <input type="radio" name="export-submissions" value="CSV">
                        <span></span>
                    </label>
                </li>
                <li>
                    <label>
                        XML
                    </label>
                    <label class="ba-radio ba-hide-checkbox">
                        <input type="radio" name="export-submissions" value="XML">
                        <span></span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CANCEL') ?></a>
        <a href="#" class="ba-btn-primary apply-submissions-export"><?php echo JText::_('EXPORT') ?></a>
    </div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_baforms&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="forms-container">
        <div id="forms-content">
            <?php include(JPATH_COMPONENT.'/views/layout/sidebar.php'); ?>
            <div class="ba-main-view">
                <div id="filter-bar">
                    <div class="app-title-wrapper">
                        <h1><?php echo JText::_('SUBMISSIONS'); ?></h1>
                    </div>
                    <div class="filter-search-wrapper">
                        <div>
                            <input type="text" name="filter_search" id="filter_search"
                               value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                               placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                            <i class="zmdi zmdi-search"></i>
                        </div>
                    </div>
                    <div class="submissions-forms-filter">
                        <div class="ba-custom-select">
<?php
                            $str = '';
                            $value = JText::_('JALL');
                            foreach ($this->titles as $obj) {
                                $str .= '<li data-value="'.$obj->title.'">';
                                if ($obj->title == $title) {
                                    $str .= '<i class="zmdi zmdi-check"></i>';
                                    $value = $obj->title;
                                }
                                $str .= $obj->title.'</li>';
                            }
?>
                            <input readonly value="<?php echo $value; ?>" type="text">
                            <input type="hidden" name="filter_title" id="filter_title" value="<?php echo $title; ?>">
                            <i class="zmdi zmdi-caret-down"></i>
                            <ul>
                                <li data-value="*"><?php echo ($title == '*' ? '<i class="zmdi zmdi-check"></i>' : '').JText::_('JALL'); ?></li>
<?php
                                echo $str;
?>
                            </ul>
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
                <div class="main-table submissions-list<?php echo count($this->items) == 0 ? ' empty-comments-table' : ''; ?>">
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
                                        <?php echo JText::_('FORMS'); ?>
                                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                    </span>
                                </th>
                                <th class="<?php echo $listOrder == 'date' ? 'active' : ''; ?>">
                                    <span data-sorting="date">
                                        <?php echo JText::_('DATE'); ?>
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
<?php
                        foreach ($this->items as $i => $item) {
                            $date = date('Y-m-d', strtotime($item->date_time));
?>
                            <tr class="<?php echo $item->submission_state == 1 ? 'ba-submission-unread' : '' ?>">
                                <td class="select-td">
                                    <label class="ba-hide-checkbox">
                                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                        <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                        <i class="zmdi zmdi-check ba-icon-md"></i>
                                    </label>
                                </td>
                                <td>
                                    <span class="submission-title"><?php echo $item->title; ?></span>
                                </td>
                                <td>
                                    <?php echo $date; ?>
                                </td>
                                <td class="id-cell">
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
<?php
                            }
?>
                        </tbody>
                    </table>
                    <div class="submissions-right-sidebar">
                        <div class="submissions-sidebar-header">
                            <span class="disabled save-pdf-submission"
                                data-url="index.php?option=com_baforms&view=submissions&layout=pdf&tmpl=component&id=">
                                <i class="zmdi zmdi-file-text"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('SAVE_AS_PDF'); ?></span>
                            </span>
                            <span class="disabled print-submission"
                                data-url="index.php?option=com_baforms&view=submissions&layout=print&tmpl=component&id=">
                                <i class="zmdi zmdi-print"></i>
                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('PRINT'); ?></span>
                            </span>
                        </div>
                        <div class="submissions-sidebar-body">
                            
                        </div>
                    </div>
                </div>
                <?php echo $this->pagination->getListFooter(); ?>
                <div>
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                    <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                    <input type="hidden" name="ba_view" value="submissions">
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>
</form>
<template class="total-submission-pattern">
    <?php include(JPATH_COMPONENT.'/views/layout/total-submission-pattern.php'); ?>
</template>
<template class="poll-results-pattern">
    <div class="ba-poll-results-wrapper">
        <span class="ba-poll-results-title"></span>
        <div class="ba-poll-results-rows-wrapper">
            <div class="ba-poll-results-row">
                <span class="ba-poll-results-value"></span>
                <span class="ba-poll-results-votes"></span>
                <span class="ba-poll-results-percent"></span>
            </div>
        </div>
    </div>
</template>


<div class="ba-context-menu submissions-context-menu" style="display: none">
    <span class="context-read-submission"><i class="zmdi zmdi-eye"></i><?php echo JText::_('MARK_AS_READ'); ?></span>
    <span class="context-unread-submission"><i class="zmdi zmdi-eye-off"></i><?php echo JText::_('MARK_AS_UNREAD'); ?></span>
    <span class="context-export-submission ba-group-element"><i class="zmdi zmdi-download"></i><?php echo JText::_('EXPORT'); ?></span>
    <span class="context-pdf-submission"><i class="zmdi zmdi-file-text"></i><?php echo JText::_('SAVE_AS_PDF'); ?></span>
    <span class="context-print-submission"><i class="zmdi zmdi-print"></i><?php echo JText::_('PRINT'); ?></span>
    <span class="context-delete-submission ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<?php
include(JPATH_COMPONENT.'/views/layout/notification.php');
?>