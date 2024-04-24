<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();

$calendars = 1;
if ((!empty($product_id) && $options->type != 'single') || $multiple == 1) {
    $calendars++;
}
for ($k = 0; $k < $calendars; $k++) {
    $todayDate = $dateObject->format('n Y', true);
    $firstDay = $dateObject->format('w', true);
    if ($firstDay == 0 && $start == 1) {
        $firstDay = 7;
    }
    $daysInMonth = $dateObject->format('t', true);
    $day = 1;
?>
<div class="ba-gridbox-calendar-inner">
    <div class="ba-gridbox-calendar-title-wrapper">
<?php
    if ($k != 1) {
?>
        <span class="gridbox-calendar-btn">
            <i class="zmdi zmdi-chevron-left" data-action="prev-year"></i>
            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JYEAR'); ?></span>
        </span>
        <span class="gridbox-calendar-btn">
            <i class="zmdi zmdi-chevron-left" data-action="prev"></i>
            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JMONTH'); ?></span>
        </span>
<?php
    }
?>
        <span class="ba-gridbox-calendar-title"><?php echo $dateObject->format('F Y', true); ?></span>
<?php
    if ($calendars == 1 || $k == 1) {
?>
        <span class="gridbox-calendar-btn">
            <i class="zmdi zmdi-chevron-right" data-action="next"></i>
            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JMONTH'); ?></span>
        </span>
        <span class="gridbox-calendar-btn">
            <i class="zmdi zmdi-chevron-right" data-action="next-year"></i>
            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('JYEAR'); ?></span>
        </span>
<?php
    }
?>
    </div>
    <div class="ba-gridbox-calendar-header">
<?php
    for ($i = $start; $i <= $end; $i++) {
?>
        <div class="ba-calendar-day-name"><?php echo $dateData->days[$i] ?></div>
<?php
    }
?>
    </div>
    <div class="ba-gridbox-calendar-body">
<?php
    for ($i = 0; $i < 6; $i++) {
        if ($day > $daysInMonth) {
            break;
        }
?>
        <div class="ba-gridbox-calendar-row">
<?php
        for ($j = $start; $j <= $end; $j++) {
            if (($i === 0 && $j < $firstDay) || $day > $daysInMonth) {
?>
            <div class="ba-empty-date-cell"></div>
<?php
            } else {
                $d = $day < 10 ? '0'.(string)$day : (string)$day;
                $date = $dateObject->format('Y-m-d', true);
                $formated = $dateObject->format($format, true);
                $attributes = 'data-day="'.$d.'" data-date="'.$date.'" data-formated="'.$formated.'" data-time="'.$time.'"';
                $classname = 'ba-date-cell'.($day == $today && $nowDate->date == $todayDate ? ' ba-curent-date' : '');
                if (!empty($product_id) && $options->type == 'single' && $options->single->time == 'yes') {
                    $times = $booking->getSingleSlots($options, $dateObject, $product_id);
                    $attributes .= ' data-slots="'.count($times).'"';
                } else if (!empty($product_id) && ($options->type == 'single' || $options->type == 'multiple')) {
                    $isBlocked = $booking->isBlockedDay($dateObject, $product_id, $options->type == 'multiple');
                    $attributes .= $isBlocked ? ' data-blocked="1"' :
                        ($booking->isGroupSession ? ' data-guests="' . $booking->getGroupSessionGuest($date) . '"' : '');
                }
?>
            <div class="<?php echo $classname; ?>" <?php echo $attributes; ?>><?php echo $day; ?></div>
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
<?php
if ($footer == 1) {
?>
    <div class="ba-gridbox-calendar-footer">
        <span class="ba-gridbox-today-btn" data-date="<?php echo $now->format('Y-m-d', true); ?>"
            data-formated="<?php echo $now->format($format, true); ?>"
            data-time="<?php echo $time; ?>"><?php echo JText::_('TODAY'); ?></span>
    </div>
<?php
}
?>
</div>
<?php
}
$out = ob_get_contents();
ob_end_clean();