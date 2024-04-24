<?php
$date = $bookingDay->format('Y-m-d');
?>
<div class="ba-booking-calendar-header">
    <div class="ba-booking-calendar-row">
        <div class="ba-booking-calendar-cell">
            <?php echo JText::_('DATE'); ?>
        </div>
        <div class="ba-booking-calendar-cell">
            <?php echo JText::_('BOOKINGS'); ?>
        </div>
        <div class="ba-booking-calendar-cell">
            
        </div>
        <div class="ba-booking-calendar-cell">
            <?php echo JText::_('PAYMENT_STATUS'); ?>
        </div>
    </div>
</div>
<div class="ba-booking-calendar-body">
<?php
foreach ($this->items as $date => $data) {
    $dateObject = JDate::getInstance(strtotime($date));
    foreach ($data as $key => $item) {
        $title = $item->isBlock ? JText::_('BLOCKED_TIME') : $item->title;
        
?>
    <div class="ba-booking-calendar-row" data-id="<?php echo $item->id; ?>" data-type="<?php echo $item->isBlock ? 'block' : 'appointment'; ?>">
        <div class="ba-booking-calendar-cell">
<?php
        if ($key == 0) {
?>
            <span><?php echo $dateObject->format('D'); ?></span>
            <span><?php echo $dateObject->format('d'); ?></span>
<?php
        }
?>
        </div>
        <div class="ba-booking-calendar-cell">
<?php
        if (!empty($item->image)) {
            $image = (gridboxHelper::isExternal($item->image) ? '' : JUri::root()).$item->image;
?>
            <div class="ba-booking-item-image" style="background-image: url(<?php echo $image ?>)"></div>
<?php
        }
?>
            <span class="ba-booking-item-title"><?php echo $title; ?></span>
        </div>
        <div class="ba-booking-calendar-cell">
<?php
        if (!empty($item->start_time)) {
            echo $item->start_time.' - '.$item->end_time;
        } else if (!empty($item->end_date)) {
            echo gridboxHelper::formatDate($item->start_date).' - '.gridboxHelper::formatDate($item->end_date);
        }
?>
        </div>
        <div class="ba-booking-calendar-cell">
<?php
        if (!$item->isBlock) {
            $ispaid = $item->paid == 1 ? 'PAID' : 'NOT_PAID';
?>
            <span class="ba-booking-item-badge" data-paid="<?php echo $item->paid; ?>"><?php echo JText::_($ispaid); ?></span>
<?php
        }
?>
        </div>
    </div>
<?php
    }
}
?>
</div>