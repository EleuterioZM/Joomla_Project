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
<script src="components/com_baforms/assets/js/ba-about.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
<form action="<?php echo JRoute::_('index.php?option=com_baforms&view=trashed'); ?>"
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div id="forms-container">
        <div id="forms-content">
            <?php include(JPATH_COMPONENT.'/views/layout/sidebar.php'); ?>
            <div class="ba-main-view">
                <div id="filter-bar">
                    <div class="app-title-wrapper">
                        <h1><?php echo JText::_('TRASHED_ITEMS') ?></h1>
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
                                <th class="<?php echo $listOrder == 'id' ? 'active' : ''; ?>">
                                    <span data-sorting="id">
                                        <?php echo JText::_('ID'); ?>
                                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SORT_BY_COLUMN'); ?></span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($this->items as $i => $item) : 
                                $canChange  = $user->authorise('core.edit.state', '.forms.' . $item->id); ?>
                            <tr>
                                <td class="select-td">
                                    <label class="ba-hide-checkbox">
                                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                        <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                                        <i class="zmdi zmdi-check ba-icon-md"></i>
                                    </label>
                                </td>
                                <td>
                                    <?php echo $item->title; ?>
                                </td>
                                <td>
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php
                echo $this->pagination->getListFooter();
?>
                <div>
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                    <input type="hidden" name="filter_state" value="<?php echo $state; ?>">
                    <input type="hidden" name="ba_view" value="trashed">
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?php include(JPATH_COMPONENT.'/views/layout/notification.php'); ?>