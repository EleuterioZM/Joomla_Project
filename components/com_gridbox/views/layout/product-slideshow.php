<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('product-slideshow');
$classList = $obj->items->{'item-'.$now}->dots->position.($obj->items->{'item-'.$now}->dots->outside ? ' dots-position-outside' : '');
?>
<div class="ba-item-product-slideshow ba-item" id="<?php echo 'item-'.$now; ?>">
	<div class="slideshow-wrapper <?php echo $classList; ?>">
        <ul class="ba-slideshow ba-fade-in">
            <div class="slideshow-content ba-field-content lightbox-enabled">
                <li class="item">
                    <div class="ba-slideshow-img">
                        <div id="1550481610413"></div>
                    </div>
                </li>
                <li class="item">
                    <div class="ba-slideshow-img">
                        <div id="1550481610414"></div>
                    </div>
                </li>
            </div>
            <div class="empty-list">
                <i class="zmdi zmdi-alert-polygon"></i>
                <p><?php echo JText::_('NO_ITEMS_HERE'); ?></p>
            </div>
            <div class="ba-slideshow-nav">
                <a class="ba-btn-transition slideshow-btn-prev ba-icons ba-icon-chevron-left" data-slide="prev"></a>
                <a class="ba-btn-transition slideshow-btn-next ba-icons ba-icon-chevron-right" data-slide="next"></a>
            </div>
            <div class="ba-slideshow-dots center-align <?php echo $obj->items->{'item-'.$now}->dots->layout; ?>"></div>
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