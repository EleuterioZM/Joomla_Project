<?php
if (empty($items)) {
?>
    <span class="havent-any-bookings"><?php echo JText::_('HAVENT_ANY_BOOKINGS'); ?></span>
<?php
}
foreach ($items as $item) {
    $ispaid = $item->paid == 1 ? 'PAID' : 'NOT_PAID';
    $date = gridboxHelper::formatDate($item->start_date);
    $date .= (!empty($item->end_date) ? (' - '.gridboxHelper::formatDate($item->end_date)) : '');
    $date .= (!empty($item->start_time) ? (', '.$item->start_time.' - '.$item->end_time) : '');
?>
    <div class="ba-booking-item" data-id="<?php echo $item->id; ?>" data-unread="<?php echo $item->unread == 1 ?>">
<?php
    if (!empty($item->image)) {
        $image = (gridboxHelper::isExternal($item->image) ? '' : JUri::root()).$item->image;
?>
        <div class="ba-booking-item-image" style="background-image: url(<?php echo $image ?>)"></div>
<?php
    }
?>
        <div class="ba-booking-item-content">
            <span class="ba-booking-item-title"><?php echo $item->title; ?></span>
            <span class="ba-booking-item-date"><?php echo $date; ?></span>
            <span class="ba-booking-item-badge" data-paid="<?php echo $item->paid; ?>"><?php echo JText::_($ispaid); ?></span>
<?php
        if ($isNew && $item->unread == 1) {
?>
            <span class="ba-booking-item-badge" data-status=""><?php echo JText::_('NEW') ?></span>
<?php
        }
?>
        </div>
    </div>
<?php
}
echo $paginator;