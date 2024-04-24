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
<div class="ba-settings-group">
    <div class="settings-group-title">
        <i class="zmdi zmdi-skip-next"></i>
        <span><?php echo JText::_('ANIMATION'); ?></span>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('APPEARANCE'); ?>
        </span>
        <div class="animation-appearance-action-wrapper">
            <input class="animation-appearance-action" readonly onfocus="this.blur()" type="text">
            <i class="zmdi zmdi-caret-down"></i>
        </div>
    </div>
    <div class="ba-settings-item on-scroll-animations-wrapper">
        <span>
            <?php echo JText::_('ON_SCROLL'); ?>
        </span>
        <div class="ba-settings-toolbar">
            <label data-value="translateY">
                <i class="zmdi zmdi-swap-vertical"></i>
                <span class="ba-tooltip"><?php echo JText::_('VERTICAL_SCROLLING'); ?></span>
            </label>
            <label data-value="translateX">
                <i class="zmdi zmdi-swap"></i>
                <span class="ba-tooltip"><?php echo JText::_('HORIZONTAL_SCROLLING'); ?></span>
            </label>
            <label data-value="scale">
                <i class="zmdi zmdi-fullscreen"></i>
                <span class="ba-tooltip"><?php echo JText::_('SCALE'); ?></span>
            </label>
            <label data-value="rotate">
                <i class="zmdi zmdi-rotate-right"></i>
                <span class="ba-tooltip"><?php echo JText::_('ROTATE'); ?></span>
            </label>
            <label data-value="opacity">
                <i class="zmdi zmdi-texture"></i>
                <span class="ba-tooltip"><?php echo JText::_('TRANSPARENCY'); ?></span>
            </label>
            <label data-value="blur">
                <i class="zmdi zmdi-center-focus-strong"></i>
                <span class="ba-tooltip"><?php echo JText::_('BLUR'); ?></span>
            </label>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();