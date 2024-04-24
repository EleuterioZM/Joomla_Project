<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$button = 'item-'.$now;
$obj->items->{$button} = gridboxHelper::getOptions('overlay-button');
$now++;
$overlay = 'item-'.$now;
$obj->items->{$overlay} = gridboxHelper::getOptions('overlay-section');
$now++;
?>
<div class="ba-item-overlay-section ba-item" id="<?php echo $button; ?>" data-overlay="<?php echo $overlay; ?>">
    <div class="ba-button-wrapper">
        <a class="ba-btn-transition">
            <span class="empty-textnode"></span>
        	<i class="zmdi zmdi-apps"></i>
        </a>
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
                <i class="zmdi zmdi-open-in-new open-overlay-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("OPEN"); ?>
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
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model">
        
    </div>
</div>
<div class="ba-overlay-section-backdrop vertical-left" data-id="<?php echo $overlay; ?>">
    <div class="ba-overlay-section-close"></div>
    <div class="ba-wrapper ba-overlay-section ba-container" data-id="<?php echo $overlay; ?>">
        <div class="ba-section row-fluid" id="<?php echo $overlay; ?>">
<?php $now++; ?>
            <div class="close-overlay-section">
                <i class="ba-icons ba-icon-close ba-overlay-section-close"></i>
            </div>
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
                        <i class="zmdi zmdi-delete delete-item"></i>
                        <span class="ba-tooltip tooltip-delay settings-tooltip">
                            <?php echo JText::_("DELETE"); ?>
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
    $count = 1;
    $span = array(0 => 12);
    include JPATH_ROOT.'/components/com_gridbox/views/layout/row.php';
    echo $out;
    ?>
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();