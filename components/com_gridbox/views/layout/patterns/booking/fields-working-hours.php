<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$times = [];
for ($i = 0; $i < 48; $i++) {
    $times[] = JDate::getInstance(mktime(0, $i * 30, 0))->format('H:i');
}
?>
<div class="booking-working-hours-wrapper">
<?php
foreach ($week as $day) {
    $obj = $hours->{$day};
?>
    <div class="booking-working-hours-group" data-day="<?php echo $day ?>">
        <div class="booking-working-hours-element <?php echo $obj->enable ? 'booking-working-hours-enabled' : ''; ?>">
            <label><?php echo ucfirst($day); ?></label>
            <label class="ba-checkbox">
                <input type="checkbox" class=" set-group-display" <?php echo $obj->enable ? 'checked' : ''; ?>>
                <span></span>
            </label>
            <select data-option="start">
<?php
            foreach ($times as $time) {
?>
                <option value="<?php echo $time; ?>" <?php echo $obj->hours[0]->start == $time ? 'selected' : ''; ?>>
                    <?php echo $time; ?>
                </option>
<?php
            }
?>
            </select>
            <select data-option="end">
<?php
            foreach ($times as $time) {
?>
                <option value="<?php echo $time; ?>" <?php echo $obj->hours[0]->end == $time ? 'selected' : ''; ?>>
                    <?php echo $time; ?>
                </option>
<?php
            }
?>
            </select>
            <span class="booking-calendar-add-hours">
                <i class="zmdi zmdi-plus-circle"></i>
                <span class="ba-tooltip ba-bottom"><?php echo JText::_('ADD_HOURS'); ?></span>
            </span>
        </div>
        <div class="ba-subgroup-element <?php echo $obj->enable ? 'visible-subgroup subgroup-animation-ended' : ''; ?>"
            style="--subgroup-childs: <?php echo count($obj->hours) - 1; ?>;">
<?php
        for ($i = 1; $i < count($obj->hours); $i++) {
?>
            <div class="booking-working-hours-element">
                <select data-option="start">
<?php
                foreach ($times as $time) {
?>
                    <option value="<?php echo $time; ?>" <?php echo $obj->hours[$i]->start == $time ? 'selected' : ''; ?>>
                        <?php echo $time; ?>
                    </option>
<?php
                }
?>
                </select>
                <select data-option="end">
<?php
                foreach ($times as $time) {
?>
                    <option value="<?php echo $time; ?>" <?php echo $obj->hours[$i]->end == $time ? 'selected' : ''; ?>>
                        <?php echo $time; ?>
                    </option>
<?php
                }
?>
                </select>
                <span class="booking-calendar-delete-hours">
                    <i class="zmdi zmdi-delete"></i>
                    <span class="ba-tooltip ba-bottom"><?php echo JText::_('DELETE'); ?></span>
                </span>
            </div>
<?php
        }
?>
        </div>
    </div>
<?php
}
?>
</div>
<template class="booking-calendar-default-hours">
    <div class="booking-working-hours-element">
        <select data-option="start">
<?php
        foreach ($times as $time) {
?>
            <option value="<?php echo $time; ?>"><?php echo $time; ?></option>
<?php
        }
?>
        </select>
        <select data-option="end">
<?php
        foreach ($times as $time) {
?>
            <option value="<?php echo $time; ?>"><?php echo $time; ?></option>
<?php
        }
?>
        </select>
        <span class="booking-calendar-delete-hours">
            <i class="zmdi zmdi-delete"></i>
            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
        </span>
    </div>
</template>
<?php
$out = ob_get_contents();
ob_end_clean();