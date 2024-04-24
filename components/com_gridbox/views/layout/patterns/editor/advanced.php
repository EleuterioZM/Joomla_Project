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
<div class="ba-settings-group item-animation-settings">
    <div class="settings-group-title">
        <i class="zmdi zmdi-settings"></i>
        <span><?php echo JText::_('ADVANCED'); ?></span>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('EDIT'); ?>
        </span>
        <div class="ba-custom-select section-access-select visible-select-top">
            <input readonly onfocus="this.blur()" type="text" value="">
            <input type="hidden" value="">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
<?php
                foreach ($options as $key => $access) {
                    echo '<li data-value="'.$key.'">'.$access.'</li>';
                }
?>
            </ul>
        </div>
        <label class="ba-help-icon">
            <i class="zmdi zmdi-help"></i>
            <span class="ba-tooltip ba-help">
                <?php echo JText::_('ACCESS_EDIT_TOOLTIP'); ?>
            </span>
        </label>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('VIEW'); ?>
        </span>
        <div class="ba-custom-select section-access-view-select visible-select-top">
            <input readonly onfocus="this.blur()" type="text" value="">
            <input type="hidden" data-group="access_view" class="set-value-css">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
<?php
                foreach ($options as $key => $access) {
                    echo '<li data-value="'.$key.'">'.$access.'</li>';
                }
?>
            </ul>
        </div>
        <label class="ba-help-icon">
            <i class="zmdi zmdi-help"></i>
            <span class="ba-tooltip ba-help">
                <?php echo JText::_('ACCESS_TOOLTIP'); ?>
            </span>
        </label>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('CLASS_SUFFIX'); ?>
        </span>
        <input type="text" class="class-suffix" placeholder="<?php echo JText::_('CLASS_SUFFIX'); ?>">
        <label class="ba-help-icon">
            <i class="zmdi zmdi-help"></i>
            <span class="ba-tooltip ba-help">
                <?php echo JText::_('CLASS_SUFFIX_TOOLTIP'); ?>
            </span>
        </label>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();