<div class="ba-context-menu apps-list-context-menu" data-source="app-list" style="display: none">
<?php
$sidebarApps = gridboxHelper::getGridboxAppsList();
foreach ($sidebarApps as $key => $app) {
    $attr = 'class="'.($app->type != 'group' ? 'default-action' : 'sidebar-context-parent apps-group-'.$app->id).'"';
    $attr .= ' '.($app->type != 'group' ? '' : ' data-context="apps-list-context-menu-'.$app->id.'"');
?>
    <span class="context-menu-item-link" data-id="<?php echo $app->id; ?>">
        <a href="<?php echo gridboxHelper::getUrl($app); ?>" <?php echo $attr; ?>>
            <i class="<?php echo gridboxHelper::getIcon($app); ?>"></i>
            <span>
                <?php echo $app->title; ?>
            </span>
<?php
        if ($app->type == 'group') {
?>
            <i class="zmdi zmdi-caret-right"></i>
<?php
        }
?>
        </a>
    </span>
<?php
}
?>
</div>
<?php
foreach ($sidebarApps as $key => $app) {
    if ($app->type != 'group') {
        continue;
    }
?>
<div class="ba-context-menu apps-group-childs apps-list-context-menu-<?php echo $app->id ?>"
    data-source="apps-group-<?php echo $app->id ?>" style="display: none">
<?php
    $childs = gridboxHelper::getGridboxAppsList($app->id);
    foreach ($childs as $child) {
?>
    <span class="context-menu-item-link" data-id="<?php echo $child->id; ?>">
        <a href="<?php echo gridboxHelper::getUrl($child); ?>" class="default-action">
            <i class="<?php echo gridboxHelper::getIcon($child); ?>"></i>
            <span>
                <?php echo $child->title; ?>
            </span>
        </a>
    </span>
<?php
    }
?>
</div>
<?php
}
?>