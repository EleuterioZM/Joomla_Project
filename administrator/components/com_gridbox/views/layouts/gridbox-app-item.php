<?php
$user = JFactory::getUser();
foreach ($items as $item) {
    $view = $item->type == 'single' ? 'single' : 'apps';
    $itemType = $item->type;
    $viewLink = 'index.php?option=com_gridbox&view='.$view.'&id='.$item->id;
    $viewIcon = 'zmdi zmdi-eye';
    if ($item->id == 0 || $item->type == 'group') {
        $viewLink = 'index.php?option=com_gridbox&view=pages';
        $canDelete = false;
        $canEdit = $user->authorise('core.edit', 'com_gridbox');
    } else {
        $canEdit = $user->authorise('core.edit', 'com_gridbox.app.'.$item->id);
        $canDelete = $user->authorise('core.delete', 'com_gridbox.app.'.$item->id);
    }
    if ($item->type == 'group') {
        $itemType .= ' gridbox-app-folder';
        $viewLink = '#';
        $viewIcon = 'zmdi zmdi-widgets';
    }
    $attr = $item->id == 0 ? '' : ' contenteditable="true"';
    $sorting = $canEdit ? ' grid-sorting-handle' : '';

?>
    <div class="gridbox-app-item gridbox-app-item-<?php echo $itemType; ?>"
        data-type="<?php echo $item->type; ?>" data-id="<?php echo $item->id; ?>"
        data-order="<?php echo $item->order_ind; ?>">
        <div class="gridbox-app-item-header<?php echo $sorting; ?>">
            <span<?php echo $canEdit ? $attr : ''; ?>><?php echo $item->title; ?></span>
        </div>
        <div class="gridbox-app-item-body">
            <a href="<?php echo $viewLink; ?>" target="_self">
<?php
                if ($item->type == 'group') {
                    foreach ($item->apps as $app) {
                        echo gridboxHelper::getAppItemIcon($app);
                    }
                } else {
                    echo gridboxHelper::getAppItemIcon($item);
                }
?>
            </a>
        </div>
        <div class="gridbox-app-item-footer">
            <a class="gridbox-app-item-footer-action footer-action-view"
                href="<?php echo $viewLink; ?>" target="_self">
                <i class="<?php echo $viewIcon; ?>"></i>
                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('VIEW'); ?></span>
            </a>
<?php
        if ($canDelete) {
?>
            <a class="gridbox-app-item-footer-action footer-action-delete delete-gridbox-app-item"
                href="#" data-id="<?php echo $item->id; ?>">
                <i class="zmdi zmdi-close"></i>
                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
            </a>
<?php
        }
?>
        </div>
    </div>
<?php
}
?>