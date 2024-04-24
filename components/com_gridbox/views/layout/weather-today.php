<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();

?>
<div class="weather">
    <span class="city"><?php echo $name; ?></span>
    <span class="date"><?php echo $weather->weather->date; ?></span>
    <span class="condition">
        <span class="icon">
            <i class="<?php echo $weather->weather->icon; ?>"></i>
        </span>
        <span class="temp-wrapper">
            <span class="temp"><?php echo $weather->weather->temp; ?></span>
            <span class="unit">°<?php echo strtoupper($item->weather->unit); ?></span>
        </span>
    </span>
</div>
<div class="weather-info">
    <div>
        <span class="wind"><?php echo $weather->weather->wind; ?></span>
    </div>
    <div>
        <span class="humidity"><?php echo $weather->weather->humidity; ?></span>
        <span class="pressure"><?php echo $weather->weather->pressure; ?></span>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();