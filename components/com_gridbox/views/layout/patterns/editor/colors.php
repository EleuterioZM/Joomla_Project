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
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="colors"';
$attr .= isset($options['subgroup']) ? ' data-subgroup="'.$options['subgroup'].'"' : '';
$states = isset($options['states']) ? $options['states'] : [];
?>
<div class="ba-settings-group states-settings-group colors-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <span><?php echo JText::_('COLORS'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="default" data-method="setColorsValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="hover" data-method="setColorsValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-hover.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('HOVER'); ?></span>
                    </span>
<?php
                foreach ($states as $state) {
                    $img = JUri::root().'components/com_gridbox/assets/images/states/state-'.$state.'.png';
                    $title = strtoupper($state);
?>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="<?php echo $state; ?>"
                        data-method="setColorsValues">
                        <img src="<?php echo $img; ?>">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_($title); ?></span>
                    </span>
<?php
                }
?>
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
<?php
    $attr .= isset($options['subgroup']) ? ' data-state="'.$options['subgroup'].'"' : '';
?>
    </div>
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
<?php
$prop = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="colors-bg"';
$prop .= isset($options['group']) ? ' data-subgroup="colors-bg"' : '';
?>
    <div class="ba-settings-item select-colors-type ba-disable-states">
        <span>
            <?php echo JText::_('TYPE'); ?>
        </span>
        <div class="ba-custom-select colors-type-select">
            <input readonly onfocus="this.blur()" type="text">
            <input type="hidden" data-property="text" <?php echo $prop; ?> data-option="type">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value=""><?php echo JText::_('COLOR') ?></li>
                <li data-value="gradient"><?php echo JText::_('GRADIENT') ?></li>
            </ul>
        </div>
    </div>
    <div class="ba-settings-item colors-color-options">
        <span>
            <?php echo JText::_('BACKGROUND'); ?>
        </span>
        <input type="text" data-type="color" <?php echo $attr; ?> data-option="background-color">
        <span class="minicolors-opacity-wrapper">
            <input type="number" class="minicolors-opacity" data-callback="sectionRules"
            min="0" max="1" step="0.01">
            <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
        </span>
    </div>
<?php
$prop = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="colors-bg"';
$prop .= isset($options['group']) ? ' data-subgroup="colors-bg"' : 'data-subgroup="gradient"';
$prop .= isset($options['group']) ? ' data-state="gradient"' : '';
?>
    <div class="colors-gradient-options">
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('EFFECT'); ?>
            </span>
            <div class="ba-custom-select gradient-effect-select">
                <input readonly onfocus="this.blur()" type="text">
                <input type="hidden" data-property="colors-bg" <?php echo $prop; ?> data-option="effect">
                <i class="zmdi zmdi-caret-down"></i>
                <ul>
                    <li data-value="linear">Linear</li>
                    <li data-value="radial">Radial</li>
                </ul>
            </div>
        </div>
        <div class="ba-settings-item colors-bg-linear-gradient ba-disable-states">
            <span>
                <?php echo JText::_('ANGLE'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="360" step="1">
                <input type="number" data-option="angle" <?php echo $prop; ?> step="1" data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('START_COLOR'); ?>
            </span>
            <input type="text" data-type="color" data-option="color1" <?php echo $prop; ?>>
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
                <input type="number" data-option="position1" <?php echo $prop; ?> step="1" data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-item ba-disable-states">
            <span>
                <?php echo JText::_('END_COLOR'); ?>
            </span>
            <input type="text" data-type="color" data-option="color2" <?php echo $prop; ?>>
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
                <input type="number" data-option="position2" <?php echo $prop; ?> step="1" data-callback="sectionRules">
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();