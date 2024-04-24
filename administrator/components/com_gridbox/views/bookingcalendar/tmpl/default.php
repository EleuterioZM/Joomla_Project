<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$user = JFactory::getUser();
$booking_view = $this->state->get('filter.booking_view');
$booking_layout = $this->state->get('filter.booking_layout');
$booking_week = $this->state->get('filter.booking_week');

$calendar_date = $this->state->get('filter.calendar_date');

$filter_services = $this->state->get('filter.services');

$layout = $booking_layout == 'w' ? 'weekly' : ($booking_layout == 'd' ? 'daily' : 'monthly');

$filter_paid = $this->state->get('filter.paid');
$filter_not_paid = $this->state->get('filter.not_paid');

$services = explode(',', $filter_services);
$week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$formats = ['h' => JText::_('HOURS'), 'd' => JText::_('DAYS'), 'm' => JText::_('MONTHS')];
$limitation = $this->settings->limitation;
$min_time = $max_time = '';
foreach ($this->settings->default as $day) {
    if (!$day->enable) {
        continue;
    }
    foreach ($day->hours as $hour) {
        if ($min_time == '' || $min_time > $hour->start) {
            $min_time = $hour->start;
        }
        if ($max_time == '' || $max_time < $hour->end) {
            $max_time = $hour->end;
        }
    }
}
$times = [];
for ($i = 0; $i < 48; $i++) {
    $times[] = JDate::getInstance(mktime(0, $i * 30, 0))->format('H:i');
}
$dates = [];
$weekDates = [];
foreach ($week as $day) {
    $time = empty($calendar_date) ? 'this week' : $calendar_date;
    $timestamp = strtotime($time);
    $date = JDate::getInstance(strtotime($day.' this week', $timestamp), true);
    $dates[] = $date->format('Y-m-d');
    $weekDates[] = $date;
}
$time = empty($calendar_date) ? 'now' : $calendar_date;
$bookingDay = JDate::getInstance($time, true);
$prevDate = JDate::getInstance($time, true);
$prevDate->modify('-1 '.($booking_layout == 'd' ? 'day' : ($booking_layout == 'w' ? 'week' : 'month')));
$nextDate = JDate::getInstance($time, true);
$nextDate->modify('+1 '.($booking_layout == 'd' ? 'day' : ($booking_layout == 'w' ? 'week' : 'month')));

$countries = gridboxHelper::getTaxCountries();
$colors = [];
$style = '';
?>
<script src="<?php echo JUri::root(); ?>/administrator/components/com_gridbox/assets/js/sortable.js" type="text/javascript"></script>
<script src="components/com_gridbox/assets/js/ba-admin.js?<?php echo $this->about->version; ?>" type="text/javascript"></script>
<script>
    window.booking = <?php echo json_encode($this->settings); ?>;
    app.store = <?php echo json_encode(gridboxHelper::$store); ?>;
    app.countries = <?php echo json_encode($countries); ?>;
</script>
<?php
include(JPATH_COMPONENT.'/views/layouts/calendar.php');
include(JPATH_COMPONENT.'/views/layouts/notification.php');
?>
<input type="hidden" value="<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>" class="jlib-selection">
<div class="ba-dashboard-apps-dialog booking-calendar-filter">
    <div class="ba-dashboard-apps-body">
        <div class="accordion" id="accordion-1">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle active" data-toggle="collapse"
                        data-parent="#accordion-1" href="#collapse-1">
                        <span>
                            <span class="accordion-title"><?php echo JText::_('SERVICES'); ?></span>
                        </span>
                        <i class="zmdi zmdi-chevron-right accordion-icon"></i>
                    </a>
                </div>
                <div id="collapse-1" class="accordion-body in collapse" style="height: auto;">
                    <div class="accordion-inner">
                        <ul class="booking-calendar-services-filter">
<?php
                        foreach ($this->services as $i => $service) {
                            $color = $this->colors->{$service->id} ?? ($this->color->colors[$i] ?? $this->color->default);
                            $colors['--service-color-'.$service->id] = $color;
                            $style .= '--service-color-'.$service->id.': '.$color.';';
?>
                            <li data-id="<?php echo $service->id; ?>">
                                <span class="booking-calendar-services-color-wrapper" style="--badge-color: <?php echo $color; ?>;">
                                    <span class="booking-calendar-services-color"></span>
                                    <span class="edit-booking-calendar-services-color" data-rgba="<?php echo $color; ?>"></span>
                                    <i class="zmdi zmdi-eyedropper"></i>
                                </span>
                                <span class="booking-calendar-services-title"><?php echo $service->title; ?></span>
                                <label class="ba-checkbox filter-services">
                                    <input type="checkbox"<?php echo !in_array($service->id, $services) ? ' checked' : ''; ?>>
                                    <span></span>
                                </label>
                            </li>
<?php
                        }
?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle collapsed" data-toggle="collapse"
                        data-parent="#accordion-1" href="#collapse-2">
                        <span>
                            <span class="accordion-title"><?php echo JText::_('PAYMENT_STATE'); ?></span>
                        </span>
                        <i class="zmdi zmdi-chevron-right accordion-icon"></i>
                    </a>
                </div>
                <div id="collapse-2" class="accordion-body collapse" style="height: 0;">
                    <div class="accordion-inner">
                        <ul class="booking-calendar-payment-state-filter">
                            <li>
                                <span class="booking-calendar-payment-state-title"><?php echo JText::_('PAID'); ?></span>
                                <label class="ba-checkbox trigger-paid-filters">
                                    <input type="checkbox" name="filter_paid"
                                        <?php echo $filter_paid == 1 ? ' checked' : ''; ?>>
                                    <span></span>
                                </label>
                            </li>
                            <li>
                                <span class="booking-calendar-payment-state-title"><?php echo JText::_('NOT_PAID'); ?></span>
                                <label class="ba-checkbox trigger-paid-filters">
                                    <input type="checkbox" name="filter_not_paid"
                                        <?php echo $filter_not_paid == 1 ? ' checked' : ''; ?>>
                                    <span></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_gridbox&view=bookingcalendar'); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
        <div id="gridbox-container">
            <div id="gridbox-content">
<?php
                include(JPATH_COMPONENT.'/views/layouts/sidebar.php');
?>
                <div class="ba-main-view">
                    <div id="filter-bar">
                        <div class="app-title-wrapper">
                            <h1><?php echo JText::_('CALENDAR'); ?></h1>
                            <span class="blog-icons">
                                <span class="booking-calendar-settings">
                                    <i class="zmdi zmdi-settings"></i>
                                    <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('SETTINGS'); ?></span>
                                </span>
                            </span>
                        </div>
                        <div class="filter-search-wrapper">
                            <div class="ba-booking-calendar-action-wrapper">
                                <span class="ba-booking-calendar-action" data-action="-" data-value="<?php echo $prevDate->format('Y-m-d'); ?>">
                                    <i class="zmdi zmdi-caret-left"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('PREV'); ?></span>
                                </span>
                            </div>
                            <div class="ba-booking-calendar-today-wrapper">
                                <span class="ba-booking-calendar-today">
                                    <input type="hidden" class="open-calendar-dialog" data-format="Y-m-d" data-footer="1">
                                    <i class="zmdi zmdi-calendar"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SELECT_A_DATE'); ?></span>
                                </span>
                            </div>
                            <div class="ba-booking-calendar-select-wrapper">
<?php
                            $d = JDate::getInstance()->format('M d, Y');
                            $w = JDate::getInstance(strtotime('monday this week'))->format('M d, Y');
                            $w .= ' - '.JDate::getInstance(strtotime('sunday this week'))->format('M d, Y');
                            $m = JDate::getInstance()->format('M Y');
                            if ($booking_layout == 'd') {
                                $text = $bookingDay->format('M d, Y');
                            } else if ($booking_layout == 'w') {
                                $text = $weekDates[0]->format('M d, Y').' - '.$weekDates[6]->format('M d, Y');
                            } else {
                                $text = $bookingDay->format('M Y');
                            }
                            
?>
                                <div class="ba-custom-select ba-booking-calendar-select">
                                    <input readonly type="text" value="<?php echo $text; ?>">
                                    <input type="hidden" name="booking_layout" value="<?php echo $booking_layout ?>">
                                    <ul>
                                        <li data-value="d" data-text="<?php echo $d; ?>">
                                            <?php echo JText::_('DAILY'); ?>
                                        </li>
                                        <li data-value="w" data-text="<?php echo $w; ?>">
                                            <?php echo JText::_('WEEKLY'); ?>
                                        </li>
                                        <li data-value="m" data-text="<?php echo $m; ?>">
                                            <?php echo JText::_('MONTHLY'); ?>
                                        </li>
                                    </ul>
                                    <i class="zmdi zmdi-caret-down"></i>
                                </div>
                            </div>
                            <div class="ba-booking-calendar-action-wrapper">
                                <span class="ba-booking-calendar-action" data-action="+" data-value="<?php echo $nextDate->format('Y-m-d'); ?>">
                                    <i class="zmdi zmdi-caret-right"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('NEXT'); ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="ba-booking-calendar-layout-action-wrapper">
                            <span class="layout-action<?php echo $booking_view == 'calendar' ? ' active' : ''; ?>" data-layout="calendar">
                                <i class="zmdi zmdi-calendar-note"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('CALENDAR'); ?></span>
                            </span>
                            <span class="layout-action<?php echo $booking_view == 'schedule' ? ' active' : ''; ?>" data-layout="schedule">
                                <i class="zmdi zmdi-sort-amount-desc"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('SCHEDULE'); ?></span>
                            </span>
                        </div>
                        <div class="filter-icons-wrapper">
                            <div class="ba-dashboard-popover-trigger" data-target="booking-calendar-notifications">
                                <i class="zmdi zmdi-notifications"></i>
                                <span class="about-notifications-count"
                                    data-count="<?php echo $bookingCount; ?>"><?php echo $bookingCount; ?></span>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('NOTIFICATIONS'); ?></span>
                            </div>
                            <div class="ba-dashboard-popover-trigger" data-target="booking-calendar-filter">
                                <i class="zmdi zmdi-filter-list"></i>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('FILTER'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="main-table booking-calendar-content" data-view="<?php echo $booking_view; ?>"
                        data-layout="<?php echo $layout ?>" style="<?php echo $style; ?>">
<?php
                    if ($booking_view == 'schedule') {
                        include 'schedule.php';
                    } else {
                        include $layout.'.php';
                    }
?>
                    </div>
                    <div class="ba-create-item ba-create-store-product">
                        <a href="#" target="_blank">
                            <i class="zmdi zmdi-file"></i>
                        </a>
                    </div>
                    <div class="ba-select-store-product-type create-new-booking-appointment">
                        <span data-type="block-time">
                            <i class="zmdi zmdi-block"></i>
                            <span class="ba-tooltip ba-left ba-hide-element">
                                <?php echo JText::_('BLOCK_TIME'); ?>
                            </span>
                        </span>
                        <span href="#" data-type="new-booking">
                            <i class="zmdi zmdi-calendar"></i>
                            <span class="ba-tooltip ba-left ba-hide-element">
                                <?php echo JText::_('NEW_BOOKING'); ?>
                            </span>
                        </span>
                    </div>
                    <div>
                        <input type="hidden" name="context-item" value="" id="context-item" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="boxchecked" value="0"/>
                        <input type="hidden" name="app_order_list" value="1">
                        <input type="hidden" name="booking_view" value="<?php echo $booking_view; ?>">
                        <input type="hidden" name="booking_week" value="<?php echo $booking_week; ?>">
                        <input type="hidden" name="calendar_date" value="<?php echo $calendar_date; ?>">
                        <input type="hidden" name="services" value="<?php echo $filter_services; ?>">
                        <input type="hidden" name="ba_view" value="bookingcalendar">
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="booking-calendar-settings-dialog" class="ba-modal-lg modal hide" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('SETTINGS'); ?></span>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check booking-calendar-settings-apply"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#booking-calendar-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <?php echo JText::_('GENERAL'); ?>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="booking-calendar-general-options" class="row-fluid tab-pane left-tabs-wrapper active">
                    <div class="left-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#booking-calendar-booking-options" data-toggle="tab">
                                    <i class="zmdi zmdi-settings"></i>
                                    <?php echo JText::_('BOOKING'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#booking-calendar-default-hours-options" data-toggle="tab">
                                    <i class="zmdi zmdi-notifications"></i>
                                    <?php echo JText::_('DEFAULT_HOURS'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="booking-calendar-booking-options">
                                <div class="ba-options-group">
                                    <div class="ba-group-element">
                                        <label><?php echo JText::_('BOOKING_LIMITATION'); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="set-group-display ba-hide-element"
                                                data-group="limitation" data-option="enable"
                                                <?php echo $limitation->enable ? 'checked' : ''; ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="ba-subgroup-element <?php echo $limitation->enable ? 'visible-subgroup subgroup-animation-ended' : ''; ?>"
                                        style="<?php echo $limitation->enable ? '--subgroup-childs: 2;' : ''; ?>">
                                        <div class="ba-group-element" data-limitation="early">
                                            <label><?php echo JText::_('EARLY_BOOKING'); ?></label>
                                            <input type="text" value="<?php echo $limitation->early->value; ?>" data-option="value">
                                            <select data-option="format">
<?php
                                            foreach ($formats as $key => $format) {
?>
                                                <option value="<?php echo $key; ?>" <?php echo $limitation->early->format == $key ? 'selected' : ''; ?>>
                                                    <?php echo $format; ?>
                                                </option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                        <div class="ba-group-element" data-limitation="late">
                                            <label><?php echo JText::_('LATE_BOOKING'); ?></label>
                                            <input type="text" value="<?php echo $limitation->late->value; ?>" data-option="value">
                                            <select data-option="format">
<?php
                                            foreach ($formats as $key => $format) {
?>
                                                <option value="<?php echo $key; ?>" <?php echo $limitation->late->format == $key ? 'selected' : ''; ?>>
                                                    <?php echo $format; ?>
                                                </option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="booking-calendar-default-hours-options">
<?php
                            foreach ($week as $day) {
                                $obj = $this->settings->default->{$day};
?>
                                <div class="ba-options-group" data-day="<?php echo $day ?>">
                                    <div class="ba-group-element <?php echo $obj->enable ? 'booking-calendar-default-hours-enabled' : ''; ?>">
                                        <label><?php echo ucfirst($day); ?></label>
                                        <label class="ba-checkbox">
                                            <input type="checkbox" class="ba-hide-element set-group-display"
                                                data-group="default" data-subgroup="<?php echo $day ?>" data-option="enable"
                                                <?php echo $obj->enable ? 'checked' : ''; ?>>
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
                                            <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('ADD_HOURS'); ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-subgroup-element <?php echo $obj->enable ? 'visible-subgroup subgroup-animation-ended' : ''; ?>"
                                        style="--subgroup-childs: <?php echo count($obj->hours) - 1; ?>;">
<?php
                                    for ($i = 1; $i < count($obj->hours); $i++) {
?>
                                        <div class="ba-group-element">
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
                                                <span class="ba-tooltip ba-bottom ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<template class="booking-calendar-default-hours">
    <div class="ba-group-element">
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
<div class="ba-dashboard-apps-dialog booking-calendar-notifications">
    <div class="ba-dashboard-apps-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#booking-calendar-new-bookings" data-toggle="tab">
                        <?php echo JText::_('NEW_BOOKINGS'); ?>
                    </a>
                </li>
                <li>
                    <a href="#booking-calendar-upcomings" data-toggle="tab">
                        <?php echo JText::_('UPCOMING'); ?>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="booking-calendar-new-bookings" class="row-fluid tab-pane active">
<?php
                $items = $this->newBookings->items;
                $paginator = $this->newBookings->paginator;
                $isNew = true;
                include JPATH_COMPONENT.'/views/layouts/booking-items.php';
?>
                </div>
                <div id="booking-calendar-upcomings" class="row-fluid tab-pane">
<?php
                $items = $this->upcoming->items;
                $paginator = $this->upcoming->paginator;
                $isNew = false;
                include JPATH_COMPONENT.'/views/layouts/booking-items.php';
?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="block-time-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('BLOCK_TIME'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="block-time-wrapper">
            <div>
                <label><?php echo JText::_('START_DATE'); ?></label>
                <input type="text" class="open-calendar-dialog" onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT') ?>"
                    data-format="<?php echo gridboxHelper::$website->date_format ?>" readonly name="start_date" data-name="0" data-link="1"
                    data-type="range-dates" data-key="from">
            </div>
            <div>
                <label><?php echo JText::_('TIME'); ?></label>
                <select name="start_time">
                    <option value="">
                        <?php echo JText::_('SELECT') ?>
                    </option>
<?php
                 foreach ($times as $time) {
?>
                    <option value="<?php echo $time; ?>">
                        <?php echo $time; ?>
                    </option>
<?php
                }
?>
                </select>
            </div>
        </div>
        <div class="block-time-wrapper">
            <div>
                <label><?php echo JText::_('END_DATE'); ?></label>
                <input type="text" class="open-calendar-dialog" onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT') ?>"
                    data-format="<?php echo gridboxHelper::$website->date_format ?>" readonly name="end_date" data-name="1" data-link="0"
                    data-type="range-dates" data-key="to">
            </div>
            <div>
                <label><?php echo JText::_('TIME'); ?></label>
                <select name="end_time">
                    <option value="">
                        <?php echo JText::_('SELECT') ?>
                    </option>
<?php
                 foreach ($times as $time) {
?>
                    <option value="<?php echo $time; ?>">
                        <?php echo $time; ?>
                    </option>
<?php
                }
?>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary" id="apply-block-time">
            <?php echo JText::_('SAVE') ?>
        </a>
    </div>
</div>
<div id="booking-details-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('BOOKING_DETAILS'); ?></h3>
        <span class="ba-booking-item-badge" data-paid="0"></span>
        <i class="zmdi zmdi-more-vert trigger-context-menu" data-context="booking-details-context-menu"></i>
    </div>
    <div class="modal-body">
        <div class="booking-details-wrapper">
            
        </div>
    </div>
    <div class="modal-footer">
        <div class="booking-details-footer-row">
            <label class="booking-details-label"><?php echo JText::_('PAYMENT'); ?></label>
        </div>
        <div class="booking-details-footer-row" data-type="paid">
            <label class="booking-details-payment"></label>
            <span class="ba-booking-price"></span>
        </div>
        <div class="booking-details-footer-row" data-type="left">
            <label class="booking-details-payment"><?php echo JText::_('LEFT_TO_PAY'); ?></label>
            <span class="ba-booking-price"></span>
        </div>
    </div>
</div>
<div id="monthly-product-booking-details-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3></h3>
        <div class="monthly-product-date-wrapper">
            <span class="monthly-product-date"></span>
            <span class="monthly-product-year"></span>
        </div>
    </div>
    <div class="modal-body">
        <div class="monthly-products-wrapper"></div>
    </div>
</div>
<template class="booking-details">
    <div class="booking-details" data-detail="">
        <label class="booking-details-label"></label>
        <span class="booking-details-info"></span>
    </div>
</template>
<div class="ba-context-menu booking-details-context-menu" style="display: none">
    <span class="edit-booking-details"><i class="zmdi zmdi-settings"></i><?php echo JText::_('EDIT'); ?></span>
    <span class="mark-booking-as-paid" data-action="1"><i class="zmdi zmdi-money"></i><?php echo JText::_('MARK_AS_PAID'); ?></span>
    <span class="mark-booking-as-unpaid" data-action="0"><i class="zmdi zmdi-money-off"></i><?php echo JText::_('MARK_AS_UNPAID'); ?></span>
    <span class="delete-booking ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<div class="ba-context-menu booking-appointment-context-menu" style="display: none">
    <span class="view-booking-appointment"><i class="zmdi zmdi-info"></i><?php echo JText::_('VIEW'); ?></span>
    <span class="edit-booking-appointment"><i class="zmdi zmdi-settings"></i><?php echo JText::_('EDIT'); ?></span>
    <span class="delete-booking-appointment ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<div class="ba-context-menu monthly-product-context-menu" style="display: none">
    <span class="view-monthly-product-appointment"><i class="zmdi zmdi-info"></i><?php echo JText::_('VIEW'); ?></span>
</div>
<div class="ba-context-menu block-time-context-menu" style="display: none">
    <span class="edit-block-time"><i class="zmdi zmdi-settings"></i><?php echo JText::_('EDIT'); ?></span>
    <span class="delete-block-time ba-group-element"><i class="zmdi zmdi-delete"></i><?php echo JText::_('DELETE'); ?></span>
</div>
<div class="ba-context-menu calendar-cell-context-menu" style="display: none">
    <span class="set-new-booking"><i class="zmdi zmdi-calendar"></i><?php echo JText::_('NEW_BOOKING'); ?></span>
    <span class="set-block-time ba-group-element"><i class="zmdi zmdi-block"></i><?php echo JText::_('BLOCK_TIME'); ?></span>
</div>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('MODAL_DELETE') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary red-btn" id="apply-delete">
            <?php echo JText::_('DELETE') ?>
        </a>
    </div>
</div>
<?php
$now = JDate::getInstance();
$attributes = 'data-format="'.gridboxHelper::$website->date_format.'"';
if ($limitation->enable) {
    $str = '+'.$limitation->late->value.' '.$formats[$limitation->late->format];
    $now = JDate::getInstance(strtotime($str));
    $str = '+'.$limitation->early->value.' '.$formats[$limitation->early->format];
    $attributes .= ' data-early="'.JDate::getInstance(strtotime($str))->format('Y-m-d').'"';
}
$now_date = $now->format('Y-m-d');
$today = gridboxHelper::formatDate($now_date);
$attributes .= ' data-now="'.$now_date.'" data-value="'.$now_date.'" value="'.$today.'"';
?>
<div id="new-booking-modal" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-header">
        <h3><?php echo JText::_('NEW_BOOKING'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="new-booking-details-wrapper">
            <div class="new-booking-details service-booking-details">
                <label>
                    <span><?php echo JText::_('SERVICE'); ?></span>
                    <span class="booking-required-star">*</span>
                </label>
                <input type="text" name="service" readonly onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT'); ?>" required>
            </div>
            <div class="booking-single-details" data-type="single">
                <div class="new-booking-details">
                    <label>
                        <span><?php echo JText::_('DATE'); ?></span>
                        <span class="booking-required-star">*</span>
                    </label>
                    <input type="text" name="start_date" readonly onfocus="this.blur()"
                        class="open-calendar-dialog"<?php echo $attributes; ?>
                        placeholder="<?php echo JText::_('SELECT'); ?>" required>
                </div>
                <div class="new-booking-details">
                    <label>
                        <span><?php echo JText::_('TIME'); ?></span>
                        <span class="booking-required-star">*</span>
                    </label>
                    <select name="start_time" required>
                        <option value="">
                            <?php echo JText::_('SELECT') ?>
                        </option>
<?php
                     foreach ($times as $time) {
?>
                        <option value="<?php echo $time; ?>">
                            <?php echo $time; ?>
                        </option>
<?php
                    }
?>
                    </select>
                </div>
            </div>
            <div class="new-booking-details" data-type="single">
                <label>
                    <span><?php echo JText::_('GUESTS'); ?></span>
                    <span class="booking-required-star">*</span>
                </label>
                <input type="number" required name="guests" placeholder="<?php echo JText::_('SELECT'); ?>">
            </div>
            <div class="booking-multiple-details" data-type="multiple">
                <div class="new-booking-details">
                    <label>
                        <span><?php echo JText::_('CHECK_IN'); ?></span>
                        <span class="booking-required-star">*</span>
                    </label>
                    <input type="text" name="start_date" readonly data-name="start_date" data-link="end_date"
                        class="open-calendar-dialog" data-type="range-dates" data-key="from"<?php echo $attributes; ?>
                        onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT'); ?>" required>
                </div>
                <div class="new-booking-details">
                    <label>
                        <span><?php echo JText::_('CHECK_OUT'); ?></span>
                        <span class="booking-required-star">*</span>
                    </label>
                    <input type="text" name="end_date" readonly data-name="end_date" data-link="start_date"
                        class="open-calendar-dialog" data-type="range-dates" data-key="to"<?php echo $attributes; ?>
                        onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT'); ?>" required>
                </div>
            </div>
            <div class="new-booking-details" data-type="user">
                <label><?php echo JText::_('USER') ?></label>
                <input type="text" name="user" readonly onfocus="this.blur()" placeholder="<?php echo JText::_('SELECT'); ?>">
            </div>

<?php
        foreach ($this->info as $info) {
            if ($info->type == 'headline' || $info->type == 'acceptance') {
                continue;
            }
            $title = '<span>'.$info->title.'</span>';
            if ($info->required == 1 && !empty($info->title)) {
                $title .= '<span class="booking-required-star">*</span>';
            }
?>
            <div class="new-booking-details" data-type="<?php echo $info->type; ?>">
                <label><?php echo $title; ?></label>
<?php
            $attr = $info->required == 1 ? ' required' : '';
            if ($info->type == 'textarea' || $info->type == 'text' || $info->type == 'email') {
                $attr .= isset($info->settings->placeholder) ? ' placeholder="'.$info->settings->placeholder.'"' : '';
            }
            if ($info->type == 'textarea') {
?>
                <textarea name="<?php echo $info->id; ?>"<?php echo $attr; ?>></textarea>
                <span class="focus-underline"></span>
<?php
            } else if ($info->type == 'text' || $info->type == 'email') {
?>
                <input type="<?php echo $info->type; ?>" name="<?php echo $info->id; ?>"<?php echo $attr; ?>>
                <span class="focus-underline"></span>
<?php
            } else if ($info->type == 'dropdown') {
?>
                <select name="<?php echo $info->id; ?>"<?php echo $attr; ?>>
                    <option value=""><?php echo $info->settings->placeholder; ?></option>
<?php
                foreach ($info->settings->options as $option) {
?>
                    <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
<?php
                }
?>
                </select>
<?php
            } else if ($info->type == 'checkbox' || $info->type == 'radio') {
                foreach ($info->settings->options as $option) {
                    $value = strip_tags($option);
                    $value = htmlspecialchars($value, ENT_QUOTES);
?>
                    <div class="ba-checkbox-wrapper">
                        <label class="ba-<?php echo $info->type; ?>">
                            <input type="<?php echo $info->type; ?>" name="<?php echo $info->id; ?>"<?php echo $attr; ?>
                                value="<?php echo $value; ?>">
                            <span></span>
                        </label>
                        <span><?php echo $option; ?></span>
                    </div>
<?php
                }
            } else if ($info->type == 'country') {
?>
                <select data-type="country"<?php echo $attr; ?>>
                    <option value=""><?php echo $info->settings->placeholder; ?></option>
<?php
                foreach ($countries as $country) {
?>
                    <option value="<?php echo $country->id; ?>"><?php echo $country->title; ?></option>
<?php
                }
?>
                </select>
                <input type="hidden" name="<?php echo $info->id; ?>">
<?php
            }
?>
            </div>
<?php
        }
?>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary active-button" id="apply-new-booking">
            <?php echo JText::_('SAVE') ?>
        </a>
    </div>
</div>
<div id="product-applies-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker"
    style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="picker-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul></ul>
        </div>
    </div>
</div>
<?php
$view = 'orders';
include JPATH_COMPONENT.'/views/layouts/users-dialog.php';
include JPATH_COMPONENT.'/views/layouts/context.php';
include JPATH_COMPONENT.'/views/layouts/photo-editor.php';
include JPATH_COMPONENT.'/views/layouts/color-variables-dialog.php';