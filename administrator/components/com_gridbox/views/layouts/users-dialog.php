<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$users = gridboxHelper::getUsers();
$userGroups = gridboxHelper::getUserGroups();
?>
<div id="ba-<?php echo $view; ?>-users-dialog" class="ba-modal-lg modal hide"
    style="display:none" data-modal-type="users-dialog">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('SELECT_USER'); ?></span>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="ba-filter-bar">
            <input type="text" class="search-ba-author-users" placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
            <i class="zmdi zmdi-search"></i>
            <div class="user-direction-select">
                <div class="ba-custom-select">
                    <input readonly="" onfocus="this.blur()" type="text">
                    <input type="hidden">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="asc"><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></li>
                        <li data-value="desc"><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></li>
                    </ul>
                </div>
            </div>
            <div class="user-sorting-select">
                <div class="ba-custom-select">
                    <input readonly="" onfocus="this.blur()" type="text">
                    <input type="hidden">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="username"><?php echo JText::_('USERNAME'); ?></li>
                        <li data-value="name"><?php echo JText::_('NAME'); ?></li>
                        <li data-value="usergroups"><?php echo JText::_('USER_GROUPS'); ?></li>
                        <li data-value="id"><?php echo JText::_('ID'); ?></li>
                    </ul>
                </div>
            </div>
            <div class="user-group-select">
                <div class="ba-custom-select">
                    <input readonly="" onfocus="this.blur()" type="text">
                    <input type="hidden">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value=""><?php echo JText::_('SELECT_GROUP'); ?></li>
<?php
                    foreach ($userGroups as $group) {
?>
                        <li data-value="<?php echo $group->title; ?>"><?php echo $group->title; ?></li>
<?php
                    }
?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="ba-group-wrapper users-table-list">
            <p class="ba-group-title">
                <span><?php echo JText::_('USERNAME'); ?></span>
                <span><?php echo JText::_('NAME'); ?></span>
                <span><?php echo JText::_('USER_GROUPS'); ?></span>
                <span><?php echo JText::_('ID'); ?></span>
            </p>
<?php
      foreach ($users as $item) {
        $groups = '';
        foreach ($item->groups as $value) {
            $groups .= '<span>'.$value->title.'</span>';
        }
?>
            <div class="ba-options-group">
                <div class="ba-group-element" data-count="<?php echo count($item->groups) ?>">
                    <label class="ba-author-username">
                        <span data-id="<?php echo $item->id ?>">
                            <?php echo $item->username; ?>
                        </span>
                    </label>
                    <label class="ba-author-name">
                        <?php echo $item->name; ?>
                    </label>
                    <label class="ba-author-usergroup">
                        <?php echo $groups; ?>
                    </label>
                    <label class="ba-author-id">
                        <?php echo $item->id; ?>
                    </label>
                </div>
            </div>
<?php
        }
?>
        </div>
    </div>
</div>