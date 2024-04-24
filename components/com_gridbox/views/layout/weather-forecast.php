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
<div class="forecast">
    <span class="day"><?php echo $forecast->day; ?></span>
    <span class="icon">
        <i class="<?php echo $forecast->icon; ?>"></i>
    </span>
    <span class="day-temp">
        <span class="temp"><?php echo $forecast->dayTemp; ?></span>
        <span class="unit">°<?php echo strtoupper($item->weather->unit); ?></span>
    </span>
    <span class="night-temp">
        <span class="temp"><?php echo $forecast->nightTemp; ?></span>
        <span class="unit">°<?php echo strtoupper($item->weather->unit); ?></span>
    </span>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();