<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$id = 'item-'.$now++;
$obj->items->{$id} = gridboxHelper::getOptions('content-slider');
$ind = 1;
$slides = new stdClass();
$link = new stdClass();
$link->href = "";
$link->target = "_self";
$link->embed = "";
$link->download = "";
?>
<div class="ba-item-content-slider ba-item" id="<?php echo $id; ?>">
	<div class="slideshow-wrapper">
        <ul class="ba-slideshow ba-fade-in">
            <div class="slideshow-content">
<?php
            foreach ($data as $key => $img) {
                $slides->{$ind} = new stdClass();
                $slides->{$ind}->title = 'Slide '.$ind;
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
            $obj->items->{$id}->slides = $slides;
?>
            </div>
            <div class="empty-list">
                <i class="zmdi zmdi-alert-polygon"></i>
                <p><?php echo JText::_('NO_ITEMS_HERE'); ?></p>
            </div>
            <div class="ba-slideshow-nav">
                <a class="ba-btn-transition slideshow-btn-prev ba-icons ba-icon-chevron-left" data-slide="prev"></a>
                <a class="ba-btn-transition slideshow-btn-next ba-icons ba-icon-chevron-right" data-slide="next"></a>
            </div>
            <div class="ba-slideshow-dots center-align"></div>
        </ul>
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
                <i class="zmdi zmdi-sort-amount-desc content-slider-add-nested-row"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_NESTED_ROW"); ?>
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
    <div class="ba-box-model"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();