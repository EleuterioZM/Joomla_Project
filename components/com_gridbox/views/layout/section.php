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
<div class="ba-wrapper">
<?php $obj->items->{'item-'.$now} = gridboxHelper::getOptions('section'); ?>
    <div class="ba-section row-fluid" id="item-<?php echo $now++; ?>">
        <div class="ba-overlay"></div>
        <div class="ba-edit-item">
            <span class="ba-edit-wrapper edit-settings">
                <i class="zmdi zmdi-settings"></i>
                <span class="ba-tooltip tooltip-delay">
                    <?php echo JText::_("SECTION"); ?>
                </span>
            </span>
            <div class="ba-buttons-wrapper">
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-plus-circle add-columns"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        <?php echo JText::_("ADD_NEW_ROW"); ?>
                    </span>
                </span>
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
                    <?php echo JText::_("SECTION"); ?>
                </span>
            </div>
        </div>
        <div class="ba-box-model">
            <div class="ba-bm-top"></div>
            <div class="ba-bm-left"></div>
            <div class="ba-bm-bottom"></div>
            <div class="ba-bm-right"></div>
        </div>
        <div class="ba-section-items">
<?php
if ($count > 0) {
    include JPATH_ROOT.'/components/com_gridbox/views/layout/row.php';
    echo $out;
}
?>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();