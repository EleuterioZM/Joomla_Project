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
<div class="ba-item-blog-content ba-item empty-blog-content" id="item-<?php echo $now; ?>">
	<div class="blog-content-wrapper">
        [blog_content]
    </div>
    <div class="empty-list">
        <i class="zmdi zmdi-gesture"></i>
        <p><?php echo JText::_('POST_CONTENT'); ?></p>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_('ITEM'); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_('ITEM'); ?>
            </span>
        </div>
    </div>
    <div class="blog-content-backdrop"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();