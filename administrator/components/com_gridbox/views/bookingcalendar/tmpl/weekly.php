<div class="ba-booking-calendar-header">
    <div class="ba-booking-calendar-row">
        <div class="ba-booking-calendar-cell">
        
        </div>
<?php
    foreach ($weekDates as $date) {
?>
        <div class="ba-booking-calendar-cell" data-date="<?php echo $date->format('Y-m-d'); ?>">
            <span><?php echo $date->format('D'); ?></span>
            <span><?php echo $date->format('d'); ?></span>
        </div>
<?php
    }
?>
    </div>
</div>
<div class="ba-booking-calendar-body">
<?php
$formated = new stdClass();
foreach ($times as $time) {
    if ($time < $min_time || $time > $max_time) {
        continue;
    }
?>
    <div class="ba-booking-calendar-row" data-time="<?php echo $time; ?>">
        <div class="ba-booking-calendar-cell">
            <span><?php echo $time; ?></span>
        </div>
<?php
    foreach ($week as $i => $day) {
        $date = $dates[$i];
        $flag = isset($this->items->{$date}->{$time});
        $count = $flag ? count($this->items->{$date}->{$time}) : 0;
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
        if (!isset($formated->{$date})) {
            $formated->{$date} = gridboxHelper::formatDate($date);
        }
?>
        <div class="ba-booking-calendar-cell<?php echo $blocked ? ' blocked-booking-calendar-cell' : '' ?>"
            data-slots="<?php echo $count ?>" data-date="<?php echo $date; ?>" data-formated="<?php echo $formated->{$date}; ?>">
<?php
        if ($flag && !$blocked) {
            foreach ($this->items->{$date}->{$time} as $item) {
                if (isset($colors['--service-color-'.$item->item_id])) {
                    $color = 'var(--service-color-'.$item->item_id.')';
                } else {
                    $color = $this->color->default;
                }
?>
                <span class="booking-appointment" data-id="<?php echo $item->id; ?>"
                    style="--service-color: <?php echo $color; ?>"><?php echo $item->title; ?></span>
<?php
            }
        } else if ($blocked) {
            $isFirst = $date == $block->start_date && $time == $block->start_time;
            $isLast = $date == $block->end_date && $time == $block->end_time;
?>
            <span class="booking-appointment-time-block" data-id="<?php echo $block->id ?>"
                data-first="<?php echo (int)$isFirst; ?>" data-last="<?php echo (int)$isLast; ?>"></span>
<?php
        }
?>
        </div>
<?php
    }
?>
    </div>
<?php
}

?>
</div>