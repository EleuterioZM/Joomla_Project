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
$group = isset($options['group']) ? $options['group'] : 'overlay';
$property = $group;
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"' : 'data-group="overlay-states"';
$attr .= isset($options['subgroup']) ? 'data-subgroup="'.$options['subgroup'].'"' : '';

?>
<div class="ba-settings-group states-settings-group overlay-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <i class="zmdi zmdi-format-color-fill"></i>
        <span><?php echo JText::_('OVERLAY'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?>
                        data-action="default" data-method="setOverlayValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?>
                        data-action="hover" data-method="setOverlayValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-hover.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('HOVER'); ?></span>
                    </span>
                </div>
                <div class="ba-states-transition-wrapper">
                    <span class="ba-states-transition-action" <?php echo $attr; ?>>
                        <i class="zmdi zmdi-timer"></i>
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('TRANSITION'); ?></span>
                    </span>
                </div>
            </div>
            <div class="ba-states-toggle">
                <label class="ba-checkbox">
                    <input type="checkbox" <?php echo $attr; ?> data-option="state">
                    <span></span>
                    <i class="fas fa-mouse-pointer"></i>
                </label>
                <span class="ba-tooltip ba-bottom"><?php echo JText::_('HOVER_SETTINGS'); ?></span>
            </div>
        </div>
    </div>
<?php
$attr .= isset($options['subgroup']) ? 'data-state="default"' : 'data-subgroup="default"';
?>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('TYPE'); ?>
        </span>
        <div class="ba-custom-select background-overlay-select">
            <input readonly onfocus="this.blur()" type="text">
            <input type="hidden" <?php echo $attr; ?> data-option="type"
                data-property="<?php echo $property; ?>">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value="color"><?php echo JText::_('COLOR'); ?></li>
                <li data-value="blur"><?php echo JText::_('BLUR'); ?></li>
                <li data-value="gradient"><?php echo JText::_('GRADIENT'); ?></li>
                <li data-value="none"><?php echo JText::_('NO_NE'); ?></li>
            </ul>
        </div>
    </div>
    <div class="overlay-color-options">
        <div class="ba-settings-item">
            <span>
                <?php echo JText::_('COLOR'); ?>
            </span>
            <input type="text" data-type="color" <?php echo $attr; ?> data-option="color">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                min="0" max="1" step="0.01">
                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
    </div>
    <div class="overlay-blur-options">
        <div class="ba-settings-item">
            <span>
                <?php echo JText::_('EFFECT'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="20" step="1">
                <input type="number" data-option="blur" <?php echo $attr; ?> step="1" data-callback="sectionRules">
            </div>
        </div>
    </div>
    <div class="overlay-gradient-options">
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('EFFECT'); ?>
            </span>
            <div class="ba-custom-select gradient-effect-select">
                <input readonly onfocus="this.blur()" type="text">
                <input type="hidden" data-property="overlay" data-group="<?php echo $group; ?>" data-subgroup="gradient" data-option="effect">
                <i class="zmdi zmdi-caret-down"></i>
                <ul>
                    <li data-value="linear">Linear</li>
                    <li data-value="radial">Radial</li>
                </ul>
            </div>
        </div>
        <div class="ba-settings-item overlay-linear-gradient ba-disable-states">
            <span>
                <?php echo JText::_('ANGLE'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="360" step="1">
                <input type="number" data-option="angle" data-group="<?php echo $group; ?>" data-subgroup="gradient"
                    step="1" data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('START_COLOR'); ?>
            </span>
            <input type="text" data-type="color" data-option="color1" data-group="<?php echo $group; ?>"
                data-subgroup="gradient">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                min="0" max="1" step="0.01">
                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('POSITION'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="100" step="1">
                <input type="number" data-option="position1" data-group="<?php echo $group; ?>" data-subgroup="gradient"
                    step="1" data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('END_COLOR'); ?>
            </span>
            <input type="text" data-type="color" data-option="color2" data-group="<?php echo $group; ?>"
                data-subgroup="gradient">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                min="0" max="1" step="0.01">
                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('POSITION'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="100" step="1">
                <input type="number" data-option="position2" data-group="<?php echo $group; ?>" data-subgroup="gradient"
                    step="1" data-callback="sectionRules">
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();