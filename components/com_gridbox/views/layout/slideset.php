<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('slideset');
?>
<div class="ba-item-slideset ba-item" id="<?php echo 'item-'.$now; ?>">
	<div class="slideset-wrapper">
        <ul class="ba-slideset nav-hover caption-over">
            <div class="slideshow-content"></div>
            <div class="empty-list">
                <i class="zmdi zmdi-alert-polygon"></i>
                <p><?php echo JText::_('NO_ITEMS_HERE'); ?></p>
            </div>
            <div class="ba-slideset-nav">
                <a class="ba-btn-transition slideset-btn-prev ba-icons ba-icon-chevron-left" data-slide="prev"></a>
                <a class="ba-btn-transition slideset-btn-next ba-icons ba-icon-chevron-right" data-slide="next"></a>
            </div>
            <div class="ba-slideset-dots"></div>
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