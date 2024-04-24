<?php
$days = [JText::_('MON'), JText::_('TUE'), JText::_('WED'),
    JText::_('THU'), JText::_('FRI'), JText::_('SAT'), JText::_('SUN')];
?>
<div class="ba-booking-calendar-header">
    <div class="ba-booking-calendar-row">
<?php
    foreach ($days as $day) {
?>
        <div class="ba-booking-calendar-cell">
            <span><?php echo $day; ?></span>
        </div>
<?php
    }
?>
    </div>
</div>
<div class="ba-booking-calendar-body">
<?php
$day = 1;
$timestamp = $bookingDay->format('Y').'-'.$bookingDay->format('m').'-01';
$dateObject = JDate::getInstance($timestamp, true);
$daysInMonth = $dateObject->format('t', true);
$firstDay = (int)$dateObject->format('w', true);
if ($firstDay == 0) {
    $firstDay = 7;
}
for ($i = 0; $i < 6; $i++) {
    if ($day > $daysInMonth) {
        break;
    }
?>
    <div class="ba-booking-calendar-row" data-time="">
<?php
    for ($j = 1; $j <= 7; $j++) {
        if (($i === 0 && $j < $firstDay) || $day > $daysInMonth) {
?>
        <div class="ba-booking-calendar-cell"></div>
<?php
        } else {
            $date = $dateObject->format('Y-m-d');
            $blocked = false;
            foreach ($this->blocks as $block) {
                if (
                    ($date > $block->start_date && $date < $block->end_date) ||
                    ($date == $block->start_date && $date == $block->end_date && $min_time >= $block->start_time && $max_time < $block->end_time) ||
                    ($date == $block->start_date && $date < $block->end_date && $min_time >= $block->start_time) ||
                    ($date > $block->start_date && $date == $block->end_date && $max_time <= $block->end_time) ||
                    $date >= $block->start_date && $date <= $block->end_date && $min_time >= $block->start_time && $max_time <= $block->end_time
                ) {
                    $blocked = true;
                    break;
                }
            }
            $blocked = isset($this->items->{$date}) ? false : $blocked;
?>
        <div class="ba-booking-calendar-cell<?php echo $blocked ? ' blocked-booking-calendar-cell' : '' ?>"
            data-date="<?php echo $date; ?>" data-formated="<?php echo gridboxHelper::formatDate($date); ?>">
            <span class="monthly-booking-calendar-day"><?php echo $day; ?></span>
<?php
        if (isset($this->items->{$date})) {
            $object = $this->items->{$date};
            foreach ($object->multiple as $item) {
                $colorKey = '--service-color-'.$item->item_id;
                $color = isset($colors[$colorKey]) ? 'var('.$colorKey.')' : $this->color->default;
?>
                <span class="booking-appointment multiple-appointment" data-id="<?php echo $item->id; ?>"
                    data-start="<?php echo $item->start_date; ?>" data-end="<?php echo $item->end_date; ?>"
                    style="--service-color: <?php echo $color; ?>"><?php echo $item->title; ?></span>
<?php
            }
            foreach ($object->single as $obj) {
                $item = $obj->item;
                $colorKey = '--service-color-'.$item->item_id;
                $color = isset($colors[$colorKey]) ? 'var('.$colorKey.')' : $this->color->default;
                $attribute = $obj->count > 1 ? 'data-product="'.$item->item_id.'"' : 'data-id="'.$item->id.'"';
                $attribute .= ' data-date="' . $date . '"';
?>
                <span class="booking-appointment" <?php echo $attribute; ?>
                    style="--service-color: <?php echo $color; ?>">
                    <span class="booking-appointment-title"><?php echo $item->title; ?></span>
<?php
                if ($obj->count > 1) {
?>
                    <span class="booking-appointment-count"><?php echo $obj->count; ?></span>
<?php
                }
?>
                </span>
<?php
            }
        }
        if ($blocked) {
?>
            <span class="booking-appointment-time-block" data-id="<?php echo $block->id ?>"></span>
<?php
        }
?>
        </div>
<?php
            $day++;
            $dateObject->modify('+1 day');
        }
    }
?>
    </div>
<?php
}
?>
</div>