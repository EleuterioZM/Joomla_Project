<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('flipbox');
?>
<div class="ba-item-flipbox ba-item" id="item-<?php echo $now++; ?>">
	<div class="ba-flipbox-wrapper horizontal-flip-right">
        <div class="column-wrapper ba-flipbox-frontside">
            <div class="span12 ba-grid-column-wrapper" data-span="12">
                <div class="ba-grid-column column-content-align-middle" id="item-<?php echo $now++; ?>">
                    <div class="ba-overlay"></div>
                    <div class="empty-item">
                        <span>
                            <i class="zmdi zmdi-layers"></i>
                            <span class="ba-tooltip add-section-tooltip">
                                <?php echo JText::_("ADD_NEW_PLUGIN"); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-wrapper ba-flipbox-backside">
            <div class="span12 ba-grid-column-wrapper" data-span="12">
                <div class="ba-grid-column column-content-align-middle" id="item-<?php echo $now++; ?>">
                    <div class="ba-overlay"></div>
                    <div class="empty-item">
                        <span>
                            <i class="zmdi zmdi-layers"></i>
                            <span class="ba-tooltip add-section-tooltip">
                                <?php echo JText::_("ADD_NEW_PLUGIN"); ?>
                            </span>
                        </span>
                    </div>
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
                <i class="zmdi zmdi-refresh flip-flipbox-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("FLIP"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-plus-circle flipbox-add-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_NEW_PLUGIN"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-sort-amount-desc add-nested-row"></i>
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