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
<div class="ba-item-tabs ba-item" id="item-<?php echo $now++; ?>">
<?php 
$tab1 = $now++;
$tab2 = $now++;
?>
    <div class="ba-tabs-wrapper tabs-top">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-<?php echo $tab1; ?>" data-toggle="tab">
                    <span>
                        <span class="tabs-title">
                            Tab 1
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="#tab-<?php echo $tab2; ?>" data-toggle="tab">
                    <span>
                        <span class="tabs-title">
                            Tab 2
                        </span>
                    </span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-<?php echo $tab1; ?>">
<?php
$count = 1;
$span = array(0 => 12);
include JPATH_ROOT.'/components/com_gridbox/views/layout/sectionTabs.php';
echo $out;
?>
            </div>
            <div class="tab-pane " id="tab-<?php echo $tab2; ?>">
<?php
$count = 1;
$span = array(0 => 12);
include JPATH_ROOT.'/components/com_gridbox/views/layout/sectionTabs.php';
echo $out;
?>
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
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();