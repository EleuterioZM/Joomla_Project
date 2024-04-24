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
<div class="ba-row-wrapper ba-container">
<?php $obj->items->{'item-'.$now} = gridboxHelper::getOptions('row'); ?>
    <div class="ba-row row-fluid" id="item-<?php echo $now++; ?>">
        <div class="ba-overlay"></div>
        <div class="ba-edit-item">
            <span class="ba-edit-wrapper edit-settings">
                <i class="zmdi zmdi-settings"></i>
                <span class="ba-tooltip tooltip-delay">
                    <?php echo JText::_("ROW"); ?>
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
                    <i class="zmdi zmdi-graphic-eq modify-columns"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        <?php echo JText::_("MODIFY_COLUMNS"); ?>
                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-delete delete-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        <?php echo JText::_("DELETE_ITEM"); ?>
                    </span>
                </span>
                <span class="ba-edit-text">
                    <?php echo JText::_("ROW"); ?>
                </span>
            </div>
        </div>
        <div class="ba-box-model">
            <div class="ba-bm-top"></div>
            <div class="ba-bm-left"></div>
            <div class="ba-bm-bottom"></div>
            <div class="ba-bm-right"></div>
        </div>
        <div class="column-wrapper">
<?php
for ($i = 0; $i < $count; $i++) {
    $span[$i] = $span[$i] * 1;
    $obj->items->{'item-'.$now} = gridboxHelper::getOptions('column');
?>
            <div class="span<?php echo $span[$i]; ?> ba-grid-column-wrapper" data-span="<?php echo $span[$i]; ?>">
                <div class="ba-grid-column" id="item-<?php echo $now++; ?>">
                    <div class="ba-overlay"></div>
                    <div class="ba-edit-item">
                        <div class="ba-buttons-wrapper">
                            <span class="ba-edit-wrapper">
                                <i class="zmdi zmdi-plus-circle add-item"></i>
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
                                <i class="zmdi zmdi-sort-amount-desc add-columns-in-columns"></i>
                                <span class="ba-tooltip tooltip-delay settings-tooltip">
                                    <?php echo JText::_("ADD_NESTED_ROW"); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-box-model">
                        <div class="ba-bm-top"></div>
                        <div class="ba-bm-left"></div>
                        <div class="ba-bm-bottom"></div>
                        <div class="ba-bm-right"></div>
                    </div>
                    <div class="empty-item">
                        <span>
                            <i class="zmdi zmdi-layers"></i>
                            <span class="ba-tooltip add-section-tooltip">
                                <?php echo JText::_("ADD_NEW_PLUGIN"); ?>
                            </span>
                        </span>
                    </div>
                    <div class="column-info">
                        Span <?php echo $span[$i]; ?>
                    </div>
                </div>
            </div>
<?php
    if ($count > 1 && ($count - $i) != 1) {
?>
            <div class="ba-column-resizer">
                <span>
                    <i class="zmdi zmdi-more-vert"></i>
                </span>
            </div>
<?php
    }
?>
<?php
}
?>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();