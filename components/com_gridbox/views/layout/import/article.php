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
<?php
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('section');
$obj->items->{'item-'.$now}->desktop->padding->top = 0;
$obj->items->{'item-'.$now}->desktop->padding->bottom = 0;
?>
    <div class="ba-section row-fluid" id="item-<?php echo $now++; ?>">
        <div class="ba-overlay"></div>
        <div class="ba-edit-item">
            <span class="ba-edit-wrapper edit-settings">
                <i class="zmdi zmdi-settings"></i>
                <span class="ba-tooltip tooltip-delay">
                    Section
                </span>
            </span>
            <div class="ba-buttons-wrapper">
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-plus-circle add-columns"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Add New Row
                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-edit edit-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Edit
                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-copy copy-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Copy Item
                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-globe add-library"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Add to Library
                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-delete delete-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Delete Item
                    </span>
                </span>
                <span class="ba-edit-text">
                    Section
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
        <div class="ba-row-wrapper ba-container">
<?php
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('row');
$obj->items->{'item-'.$now}->desktop->margin->top = 0;
$obj->items->{'item-'.$now}->desktop->margin->bottom = 0;
?>
            <div class="ba-row row-fluid" id="item-<?php echo $now++; ?>">
                <div class="ba-overlay"></div>
                <div class="ba-edit-item">
                    <span class="ba-edit-wrapper edit-settings">
                        <i class="zmdi zmdi-settings"></i>
                        <span class="ba-tooltip tooltip-delay">
                            Row
                        </span>
                    </span>
                    <div class="ba-buttons-wrapper">
                        <span class="ba-edit-wrapper">
                            <i class="zmdi zmdi-edit edit-item"></i>
                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                Edit
                            </span>
                        </span>
                        <span class="ba-edit-wrapper">
                            <i class="zmdi zmdi-copy copy-item"></i>
                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                Copy Item
                            </span>
                        </span>
                        <span class="ba-edit-wrapper">
                            <i class="zmdi zmdi-graphic-eq modify-columns"></i>
                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                Modify Columns
                            </span>
                        </span>
                        <span class="ba-edit-wrapper">
                            <i class="zmdi zmdi-delete delete-item"></i>
                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                Delete Item
                            </span>
                        </span>
                        <span class="ba-edit-text">
                            Row
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
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('column');
?>
                    <div class="span12 ba-grid-column-wrapper" data-span="12">
                        <div class="ba-grid-column" id="item-<?php echo $now++; ?>">
                            <div class="ba-overlay"></div>
                            <div class="ba-edit-item">
                                <div class="ba-buttons-wrapper">
                                    <span class="ba-edit-wrapper">
                                        <i class="zmdi zmdi-plus-circle add-item"></i>
                                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                                            Add New Plugin
                                        </span>
                                    </span>
                                    <span class="ba-edit-wrapper">
                                        <i class="zmdi zmdi-edit edit-item"></i>
                                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                                            Edit
                                        </span>
                                    </span>
                                    <span class="ba-edit-wrapper">
                                        <i class="zmdi zmdi-sort-amount-desc add-columns-in-columns"></i>
                                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                                            Add Nested Row
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
<?php
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('text');
?>
                            <div class="ba-item-text ba-item" id="<?php echo 'item-'.$now++; ?>">
                                <div class="content-text" contenteditable="true">
                                    <p>
                                        <?php echo $text; ?>
                                    </p>
                                </div>
                                <div class="ba-edit-item">
                                    <span class="ba-edit-wrapper edit-settings">
                                        <i class="zmdi zmdi-settings"></i>
                                        <span class="ba-tooltip tooltip-delay">
                                            Item
                                        </span>
                                    </span>
                                    <div class="ba-buttons-wrapper">
                                        <span class="ba-edit-wrapper">
                                            <i class="zmdi zmdi-edit edit-item"></i>
                                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                                Edit
                                            </span>
                                        </span>
                                        <span class="ba-edit-wrapper">
                                            <i class="zmdi zmdi-copy copy-item"></i>
                                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                                Copy Item
                                            </span>
                                        </span>
                                        <span class="ba-edit-wrapper">
                                            <i class="zmdi zmdi-globe add-library"></i>
                                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                                Add to Library
                                            </span>
                                        </span>
                                        <span class="ba-edit-wrapper">
                                            <i class="zmdi zmdi-delete delete-item"></i>
                                            <span class="ba-tooltip tooltip-delay settings-tooltip">
                                                Delete Item
                                            </span>
                                        </span>
                                        <span class="ba-edit-text">
                                            Item
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
                            <div class="empty-item">
                                <span>
                                    <i class="zmdi zmdi-layers"></i>
                                    <span class="ba-tooltip add-section-tooltip">
                                        Add New Plugin
                                    </span>
                                </span>
                            </div>
                            <div class="column-info">
                                Span 12
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();