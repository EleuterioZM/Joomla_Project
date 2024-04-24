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
<div class="ba-item-accordion ba-item" id="item-<?php echo $now++; ?>">
<?php
$parent = $now++;
$tab1 = $now++;
$tab2 = $now++;
?>
    <div class="accordion" id="accordion-<?php echo $parent; ?>">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle active" data-toggle="collapse"
                    data-parent="#accordion-<?php echo $parent; ?>" href="#collapse-<?php echo $tab1; ?>">
                    <span>
                        <span class="accordion-title">
                           Collapse 1
                        </span>
                    </span>
                    <i class="ba-icons ba-icon-chevron-right accordion-icon"></i>
                </a>
            </div>
            <div id="collapse-<?php echo $tab1; ?>" class="accordion-body in collapse" style="height: auto;">
                <div class="accordion-inner">
<?php
$count = 1;
$span = [0 => 12];
include JPATH_ROOT.'/components/com_gridbox/views/layout/sectionTabs.php';
echo $out;
?>
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle collapsed" data-toggle="collapse"
                    data-parent="#accordion-<?php echo $parent; ?>" href="#collapse-<?php echo $tab2; ?>">
                    <span>
                        <span class="accordion-title">
                           Collapse 2
                        </span>
                    </span>
                    <i class="ba-icons ba-icon-chevron-right accordion-icon"></i>
                </a>
            </div>
            <div id="collapse-<?php echo $tab2; ?>" class="accordion-body collapse" style="height: 0;">
                <div class="accordion-inner">
<?php
$count = 1;
$span = [0 => 12];
include JPATH_ROOT.'/components/com_gridbox/views/layout/sectionTabs.php';
echo $out;
?>
                </div>
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
    <div class="ba-box-model"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();