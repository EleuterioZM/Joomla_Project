<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$className = isset($options['class']) ? $options['class'] : '';
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="shadow"';
$attr .= isset($options['subgroup']) ? 'data-subgroup="'.$options['subgroup'].'"' : '';

?>
<div class="ba-settings-group states-settings-group shadow-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <i class="zmdi zmdi-select-all"></i>
        <span><?php echo JText::_('SHADOW'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="default" data-method="setShadowValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-top"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="hover" data-method="setShadowValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-hover.png">
                        <span class="ba-tooltip ba-top"><?php echo JText::_('HOVER'); ?></span>
                    </span>
                </div>
                <div class="ba-states-transition-wrapper">
                    <span class="ba-states-transition-action" <?php echo $attr; ?>>
                        <i class="zmdi zmdi-timer"></i>
                        <span class="ba-tooltip ba-top"><?php echo JText::_('TRANSITION'); ?></span>
                    </span>
                </div>
            </div>
            <div class="ba-states-toggle">
                <label class="ba-checkbox">
                    <input type="checkbox" <?php echo $attr; ?> data-option="state">
                    <span></span>
                    <i class="fas fa-mouse-pointer"></i>
                </label>
                <span class="ba-tooltip ba-top"><?php echo JText::_('HOVER_SETTINGS'); ?></span>
            </div>
        </div>
<?php
    $attr .= isset($options['subgroup']) ? 'data-state="'.$options['subgroup'].'"' : '';
?>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('VALUE'); ?>
        </span>
        <div class="ba-range-wrapper">
            <span class="ba-range-liner"></span>
            <input type="range" class="ba-range" min="0" max="10">
            <input type="number" data-option="value" <?php echo $attr; ?> data-callback="shadowCallback">
        </div>
        <span class="trigger-advanced-shadows-wrapper">
            <i class="zmdi zmdi-tune trigger-advanced-shadows" <?php echo $attr; ?>></i>
        </span>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('COLOR'); ?>
        </span>
        <input type="text" data-type="color" data-option="color" <?php echo $attr; ?> class="minicolors-top">
        <span class="minicolors-opacity-wrapper">
            <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                min="0" max="1" step="0.01">
            <span class="ba-tooltip"><?php echo JText::_('OPACITY'); ?></span>
        </span>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();