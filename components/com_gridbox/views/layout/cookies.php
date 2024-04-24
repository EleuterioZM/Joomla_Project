<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items = gridboxHelper::getOptions('cookies');
$cookies = 'item-'.$now;
$now++;
$text = 'item-'.$now;
$now++; 
$button = 'item-'.$now;
$now++;
$obj->items->{$cookies} = $obj->items->{'item-14970425540'};
$obj->items->{$text} = $obj->items->{'item-14970440490'};
$obj->items->{$button} = $obj->items->{'item-14970438250'};
unset($obj->items->{'item-14970425540'});
unset($obj->items->{'item-14970440490'});
unset($obj->items->{'item-14970438250'});
?>
<div class="ba-lightbox-backdrop ba-cookies notification-bar-bottom" data-id="<?php echo $cookies; ?>">
    <div class="ba-wrapper ba-lightbox ba-container" data-id="<?php echo $cookies; ?>">
        <div class="ba-section row-fluid" id="<?php echo $cookies; ?>">
            <div class="ba-overlay"></div>
            <div class="ba-edit-item">
                <div class="ba-buttons-wrapper">
                    <span class="ba-edit-wrapper">
                        <i class="zmdi zmdi-edit edit-item"></i>
                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                            <?php echo JText::_("EDIT"); ?>
                        </span>
                    </span>
                    <span class="ba-edit-wrapper">
                        <i class="zmdi zmdi-delete delete-item"></i>
                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                            <?php echo JText::_("DELETE_ITEM"); ?>
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
            <div class="ba-section-items">
    <div class="ba-row-wrapper ba-container">
    <div class="ba-row row-fluid" id="item-<?php echo $now++; ?>">
        <div class="ba-overlay"></div>
        <div class="ba-edit-item">
            <span class="ba-edit-wrapper edit-settings">
                <i class="zmdi zmdi-settings"></i>
                <span class="ba-tooltip tooltip-delay">
                    Row                </span>
            </span>
            <div class="ba-buttons-wrapper">
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-edit edit-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Edit                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-copy copy-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Copy                    </span>
                </span>
                <span class="ba-edit-wrapper">
                    <i class="zmdi zmdi-delete delete-item"></i>
                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                        Delete                    </span>
                </span>
                <span class="ba-edit-text">
                    Row                </span>
            </div>
        </div>
        <div class="ba-box-model">
            <div class="ba-bm-top"></div>
            <div class="ba-bm-left"></div>
            <div class="ba-bm-bottom"></div>
            <div class="ba-bm-right"></div>
        </div>
        <div class="column-wrapper">
            <div class="span9 ba-grid-column-wrapper" data-span="9">
                <div class="ba-grid-column" id="item-<?php echo $now++; ?>">
                    <div class="ba-overlay"></div>
                    <div class="ba-edit-item">
                        <div class="ba-buttons-wrapper">
                            <span class="ba-edit-wrapper">
                                <i class="zmdi zmdi-plus-circle add-item"></i>
                                <span class="ba-tooltip tooltip-delay settings-tooltip">
                                    Add new element                                </span>
                            </span>
                            <span class="ba-edit-wrapper">
                                <i class="zmdi zmdi-edit edit-item"></i>
                                <span class="ba-tooltip tooltip-delay settings-tooltip">
                                    Edit                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-box-model">
                        <div class="ba-bm-top"></div>
                        <div class="ba-bm-left"></div>
                        <div class="ba-bm-bottom"></div>
                        <div class="ba-bm-right"></div>
                    </div>
                    <div class="ba-item-text ba-item" id="<?php echo $text; ?>">
    <div class="content-text" contenteditable="true">
        <p>We use cookies to improve your experience on our website.</p>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                Item            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    Edit                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    Copy                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    Add to Library                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    Delete                </span>
            </span>
            <span class="ba-edit-text">
                Item            </span>
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
                                Add new element                            </span>
                        </span>
                    </div>
                    <div class="column-info">
                        Span 9                    </div>
                </div>
            </div>
            <div class="ba-column-resizer">
                <span>
                    <i class="zmdi zmdi-more-vert"></i>
                </span>
            </div>
            <div class="ba-grid-column-wrapper span3" data-span="3" style="">
                <div class="ba-grid-column" id="item-<?php echo $now++; ?>">
                    <div class="ba-overlay"></div>
                    <div class="ba-edit-item" style="">
                        <div class="ba-buttons-wrapper">
                            <span class="ba-edit-wrapper">
                                <i class="zmdi zmdi-plus-circle add-item"></i>
                                <span class="ba-tooltip tooltip-delay settings-tooltip">
                                    Add new element                                </span>
                            </span>
                            <span class="ba-edit-wrapper">
                                <i class="zmdi zmdi-edit edit-item"></i>
                                <span class="ba-tooltip tooltip-delay settings-tooltip">
                                    Edit                                </span>
                            </span><span class="ba-edit-wrapper"><i class="zmdi zmdi-collection-text add-library-item"></i><span class="ba-tooltip tooltip-delay settings-tooltip">Library</span></span>
                        </div>
                    </div>
                    <div class="ba-box-model">
                        <div class="ba-bm-top"></div>
                        <div class="ba-bm-left"></div>
                        <div class="ba-bm-bottom"></div>
                        <div class="ba-bm-right"></div>
                    </div>
                    <div class="ba-item-button ba-item" id="<?php echo $button++; ?>" data-cookie="accept">
                        <div class="ba-button-wrapper">
                            <a class="ba-btn-transition">
                                <span>Accept Cookies</span>
                            </a>
                        </div>
                        <div class="ba-edit-item">
                            <span class="ba-edit-wrapper edit-settings">
                                <i class="zmdi zmdi-settings"></i>
                                <span class="ba-tooltip tooltip-delay">
                                    Item            </span>
                            </span>
                            <div class="ba-buttons-wrapper">
                                <span class="ba-edit-wrapper">
                                    <i class="zmdi zmdi-edit edit-item"></i>
                                    <span class="ba-tooltip tooltip-delay settings-tooltip">
                                        Edit                </span>
                                </span>
                                <span class="ba-edit-text">
                                    Item            </span>
                            </div>
                        </div>
                        <div class="ba-box-model">
                            
                        </div>
                    </div>
                    <div class="empty-item">
                        <span>
                            <i class="zmdi zmdi-layers"></i>
                            <span class="ba-tooltip add-section-tooltip">
                                Add new element                            </span>
                        </span>
                    </div>
                    <div class="column-info">Span 3</div>
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