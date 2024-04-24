<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions($layout);
?>
<div class="ba-item-weather ba-item" id="<?php echo 'item-'.$now; ?>">
	<div class="ba-weather">
        <div class="weather">
            <span class="city"></span>
            <span class="date"></span>
            <span class="condition">
                <span class="icon">
                    <i></i>
                </span>
                <span class="temp-wrapper">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </span>
        </div>
        <div class="weather-info">
            <div>
                <span class="wind"></span>
            </div>
            <div>
                <span class="humidity"></span>
                <span class="pressure"></span>
            </div>
            <div class="sunrise-wrapper">
                <span class="sunrise"></span>
                <span class="sunset"></span>
            </div>
        </div>
        <div class="">
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
            <div class="forecast">
                <span class="day"></span>
                <span class="icon">
                    <i></i>
                </span>
                <span class="day-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
                <span class="night-temp">
                    <span class="temp"></span>
                    <span class="unit"></span>
                </span>
            </div>
        </div>
    </div>
	<div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("COPY_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_TO_LIBRARY"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model">
        <div class="ba-bm-top"></div>
        <div class="ba-bm-left"></div>
        <div class="ba-bm-bottom"></div>
        <div class="ba-bm-right"></div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();