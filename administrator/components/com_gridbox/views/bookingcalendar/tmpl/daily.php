<?php
$date = $bookingDay->format('Y-m-d');
?>
<div class="ba-booking-calendar-header">
    <div class="ba-booking-calendar-row">
        <div class="ba-booking-calendar-cell">
        
        </div>
        <div class="ba-booking-calendar-cell" data-date="<?php echo $date; ?>">
            <span><?php echo $bookingDay->format('D'); ?></span>
            <span><?php echo $bookingDay->format('d'); ?></span>
        </div>
    </div>
</div>
<div class="ba-booking-calendar-body">
<?php

foreach ($times as $time) {
    if ($time < $min_time || $time > $max_time) {
        continue;
    }
    $blocked = false;
    foreach ($this->blocks as $block) {
        if (
            ($date > $block->start_date && $date < $block->end_date) ||
            ($date == $block->start_date && $date == $block->end_date && $time >= $block->start_time && $time < $block->end_time) ||
            ($date == $block->start_date && $date < $block->end_date && $time >= $block->start_time) || 
            ($date == $block->end_date && $date > $block->start_date && $time < $block->end_time) ||
            ($date >= $block->start_date && $date <= $block->end_date && $time >= $block->start_time && $time < $block->end_time)
        ) {
            $blocked = true;
            break;
        }
    }
?>
    <div class="ba-booking-calendar-row" data-time="<?php echo $time; ?>">
        <div class="ba-booking-calendar-cell">
            <span><?php echo $time; ?></span>
        </div>
        <div class="ba-booking-calendar-cell<?php echo $blocked ? ' blocked-booking-calendar-cell' : '' ?>"
            data-date="<?php echo $date; ?>" data-formated="<?php echo gridboxHelper::formatDate($date); ?>">
<?php
        if ($blocked) {
            $isFirst = $date == $block->start_date && $time == $block->start_time;
            $isLast = $date == $block->end_date && $time == $block->end_time;
?>
            <span class="booking-appointment-time-block" data-id="<?php echo $block->id ?>"
                data-first="<?php echo (int)$isFirst; ?>" data-last="<?php echo (int)$isLast; ?>"></span>
<?php
        }
?>
        </div>
    </div>

<?php
}
foreach ($this->items as $time => $object) {
    foreach ($object as $obj) {
        $item = $obj->item;
        if (isset($colors['--service-color-'.$item->item_id])) {
            $color = 'var(--service-color-'.$item->item_id.')';
        } else {
            $color = $this->color->default;
        }
        $top = gridboxHelper::$booking->calculateDaylyTopOffset($times, $item->start_time, $min_time);
        $height = gridboxHelper::$booking->calculateDaylyHeight($times, $item->start_time, $item->end_time);
        $style = '--service-color: ' . $color . '; --column-offset: ' . $item->column .
            '; --top-offset:' . $top . '; --height-offset: ' . $height . ';';
        $attribute = $obj->count > 1 ? 'data-product="'.$item->item_id.'"' : 'data-id="'.$item->id.'"';
        $attribute .= ' data-date="' . $date . '" data-time="' . $time . '"';
?>
        <span class="booking-appointment<?php echo $top != (int)$top ? ' second-daily-appointment' : ''; ?>"
            <?php echo $attribute; ?> style="<?php echo $style; ?>">
            <span class="booking-appointment-title"><?php echo $item->title; ?></span>
<?php
        if ($obj->count > 1) {
?>
            <span class="booking-appointment-count"><?php echo $obj->count; ?></span>
<?php
        }
?>
            <span class="booking-appointment-time"><?php echo $item->start_time.' - '.$item->end_time; ?></span>
        </span>
<?php
    }
}
?>
</div>