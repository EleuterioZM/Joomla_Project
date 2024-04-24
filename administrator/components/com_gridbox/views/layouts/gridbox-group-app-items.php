<?php
$user = JFactory::getUser();
foreach ($items as $item) {
    $view = $item->type == 'single' ? 'single' : 'apps';
    $link = 'index.php?option=com_gridbox&view='.$view.'&id='.$item->id;
    if ($item->id == 0) {
        $link = 'index.php?option=com_gridbox&view=pages';
        $canDelete = false;
        $canEdit = $user->authorise('core.edit', 'com_gridbox');
    } else {
        $canEdit = $user->authorise('core.edit', 'com_gridbox.app.'.$item->id);
        $canDelete = $user->authorise('core.delete', 'com_gridbox.app.'.$item->id);
    }    
    $sorting = $canEdit ? ' grid-sorting-handle' : '';
?>
    <div class="group-apps-list-item">
        <a href="<?php echo $link; ?>">
            <span class="gridbox-app-item-icon-wrapper" data-type="<?php echo $item->type; ?>"
                data-id="<?php echo $item->id; ?>" data-order="<?php echo $item->order_ind; ?>">
                <i class="<?php echo gridboxHelper::getIcon($item); ?>"></i>
            </span>
            <span class="app-item-title"><?php echo $item->title; ?></span>
        </a>
<?php
    if ($canDelete) {
?>
        <i class="zmdi zmdi-delete remove-group-app" data-id="<?php echo $item->id; ?>"></i>
<?php
    }
?>
        <div class="<?php echo $sorting; ?>"></div>
    </div>
<?php
}
?>