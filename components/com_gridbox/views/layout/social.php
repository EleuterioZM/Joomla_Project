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
<div class="ba-item-social ba-item" id="<?php echo 'item-'.$now; ?>">
	<div class="ba-social ba-social-md ba-social-flat">
        <div class="facebook">
            <span class="social-button">
                <i class="ba-icons ba-icon-facebook"></i>Facebook
            </span>
            <span class="social-counter">0</span>
        </div>
        <div class="twitter">
            <span class="social-button">
                <i class="ba-icons ba-icon-twitter"></i>Twitter
            </span>
        </div>
        <div class="linkedin">
            <span class="social-button">
                <i class="ba-icons ba-icon-linkedin"></i>LinkedIn
            </span>
            <span class="social-counter">0</span>
        </div>
        <div class="vk">
            <span class="social-button">
                <i class="ba-icons ba-icon-vk"></i>VKontakte
            </span>
            <span class="social-counter">0</span>
        </div>
        <div class="pinterest">
            <span class="social-button">
                <i class="ba-icons ba-icon-pinterest"></i>Pinterest
            </span>
            <span class="social-counter">0</span>
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