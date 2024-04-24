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
$themeState = $this->state->get('filter.theme');
$accessState = $this->state->get('filter.access');
$languageState = $this->state->get('filter.language');
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=integrations'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
                <?php include(JPATH_COMPONENT.'/views/layouts/sidebar.php'); ?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('INTEGRATIONS'); ?></h1>
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
                    <div class="main-table integrations-table twin-view-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo JText::_('JGLOBAL_TITLE'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
<?php
                            foreach ($this->items as $i => $item) {
                                
                                $img = str_replace('_', '-', $item->service);
                                $img = str_replace('-login', '', $img);
                                $img = str_replace('-comments', '', $img);
                                $image = JUri::root().'/administrator/components/com_gridbox/assets/images/integrations/'.$img.'.png';
?>
                                <tr data-type="<?php echo $item->type; ?>" data-id="<?php echo $item->id; ?>"
                                    data-configured="<?php echo intval(!empty($item->key)); ?>">
                                    <td class="title-cell">
                                        <span class="integrations-text-wrapper">
                                            <span class="ba-item-thumbnail" style="background-image: url(<?php echo $image; ?>);"></span>
                                            <span class="integrations-title">
                                                <?php echo $item->title; ?>
                                            </span>
<?php
                                        if (!empty($item->key)) {
?>
                                            <i class="zmdi zmdi-check-circle integrations-configured-icon"></i>
<?php
                                        }
?>
                                        </span>
                                    </td>
                                </tr>
<?php
                            }
?>
                            </tbody>
                        </table>
                        <div class="twin-view-right-sidebar">
                            <div class="twin-view-sidebar-header">
<?php
                            if ($user->authorise('core.edit', 'com_gridbox')) {
?>
                                <span class="disabled apply-integration">
                                    <i class="zmdi zmdi-check"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                                </span>
<?php
                            }
?>
                            </div>
                            <div class="twin-view-sidebar-body">
                                <div class="ba-options-group-header-wrapper">
                                    <span class="ba-options-group-header"></span>
                                </div>
                                <div class="ba-options-group-wrapper">
                                    <div class="integrations-options">
                                        <div class="ba-options-group-element">
                                            <label class="ba-options-group-label"></label>
                                            <input type="text" data-key="key">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                        <input type="hidden" name="theme_filter" value="<?php echo $themeState; ?>">
                        <input type="hidden" name="language_filter" value="<?php echo $languageState; ?>">
                        <input type="hidden" name="access_filter" value="<?php echo $accessState; ?>">
                        <input type="hidden" name="ba_view" value="integrations">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
include(JPATH_COMPONENT.'/views/layouts/context.php');
include(JPATH_COMPONENT.'/views/layouts/photo-editor.php');
?>