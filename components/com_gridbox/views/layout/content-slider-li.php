<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$slides = new stdClass();
$link = new stdClass();
$link->href = "";
$link->target = "_self";
$link->embed = "";
$link->download = "";
foreach ($data as $key => $img) {
    $slides->{$ind} = new stdClass();
    $slides->{$ind}->title = 'Slide '.$title++;
    $slides->{$ind}->desktop = gridboxHelper::getOptions('contentSliderPatern');
    $slides->{$ind}->desktop->background->image->image = $img;
    $slides->{$ind}->link = $link;
    $ind++;
?>
    <li class="item">
        <div class="ba-overlay"></div>
        <div class="ba-slideshow-img"><div id="<?php echo $now++; ?>"></div></div>
        <div class="ba-grid-column" id="item-<?php echo $now++; ?>">
<?php
            $count = 1;
            $span = array(12);
            include JPATH_ROOT.'/components/com_gridbox/views/layout/row.php';
            echo $out;
?>
            <div class="empty-item">
                <span>
                    <i class="zmdi zmdi-layers"></i>
                    <span class="ba-tooltip add-section-tooltip">
                        <?php echo JText::_("ADD_NEW_PLUGIN"); ?>
                    </span>
                </span>
            </div>
        </div>
    </li>
<?php
}
$out = ob_get_contents();
ob_end_clean();