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
$object = $obj->items->{'item-'.$now};
?>
<div class="ba-item-before-after-slider ba-item" id="<?php echo 'item-'.$now; ?>">
    <div class="ba-before-after-wrapper" data-direction="<?php echo $object->direction; ?>"
        data-mouseover="<?php echo $object->mouseover ? 'enabled' : ''; ?>">
        <img class="ba-before-img" src="<?php echo JUri::root().$object->imgs->before->src; ?>">
        <img class="ba-after-img" src="<?php echo JUri::root().$object->imgs->after->src; ?>">
        <div class="ba-before-after-overlay">
            <span class="ba-before-after-label ba-before-label">Before</span>
            <span class="ba-before-after-label ba-after-label">After</span>
        </div>
        <div class="ba-before-after-divider">
            <span class="ba-before-after-slider">
                <i class="ba-icons ba-icon-chevron-left"></i>
                <i class="ba-icons ba-icon-chevron-right"></i>
            </span>
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
        <div class="ba-bm-top"></div>
        <div class="ba-bm-left"></div>
        <div class="ba-bm-bottom"></div>
        <div class="ba-bm-right"></div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();