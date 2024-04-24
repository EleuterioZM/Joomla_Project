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
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="border"';
$attr .= isset($options['subgroup']) ? 'data-subgroup="'.$options['subgroup'].'"' : '';
$states = isset($options['states']) ? $options['states'] : [];
?>
<div class="ba-settings-group states-settings-group border-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <i class="zmdi zmdi-border-left"></i>
        <span><?php echo JText::_('BORDER'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="default" data-method="setBorderValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="hover" data-method="setBorderValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-hover.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('HOVER'); ?></span>
                    </span>
<?php
                foreach ($states as $state) {
                    $img = JUri::root().'components/com_gridbox/assets/images/states/state-'.$state.'.png';
                    $title = strtoupper($state);
?>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="<?php echo $state; ?>"
                        data-method="setBorderValues">
                        <img src="<?php echo $img; ?>">
                        <span class="ba-tooltip ba-top"><?php echo JText::_($title); ?></span>
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
    </div>
<?php
$attr .= isset($options['subgroup']) ? 'data-state="'.$options['subgroup'].'"' : '';
?>
    <div class="ba-settings-toolbar">
        <label <?php echo $attr; ?> data-option="top" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-border-top"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('TOP'); ?>
            </span>
        </label>
        <label <?php echo $attr; ?> data-option="right" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-border-right"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('RIGHT'); ?>
            </span>
        </label>
        <label <?php echo $attr; ?> data-option="bottom" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-border-bottom"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('BOTTOM'); ?>
            </span>
        </label>
        <label <?php echo $attr; ?> data-option="left" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-border-left"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('LEFT'); ?>
            </span>
        </label>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('BORDER_RADIUS'); ?>
        </span>
        <div class="ba-range-wrapper">
            <span class="ba-range-liner"></span>
            <input type="range" class="ba-range" min="0" max="500">
            <input type="text" <?php echo $attr; ?> data-option="radius" data-callback="sectionRules">
        </div>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('COLOR'); ?>
        </span>
        <input type="text" data-type="color" <?php echo $attr; ?> data-option="color" class="minicolors-top">
        <span class="minicolors-opacity-wrapper">
            <input type="number" class="minicolors-opacity" data-callback="sectionRules"
            min="0" max="1" step="0.01">
            <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
        </span>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('WIDTH'); ?>
        </span>
        <div class="ba-range-wrapper">
            <span class="ba-range-liner"></span>
            <input type="range" class="ba-range" min="0" max="20">
            <input type="text" <?php echo $attr; ?> data-option="width" data-callback="sectionRules">
        </div>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('STYLE'); ?>
        </span>
        <div class="ba-custom-select border-style-select visible-select-top">
            <input readonly onfocus="this.blur()" type="text">
            <input type="hidden" <?php echo $attr; ?> data-option="style">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value="solid">Solid</li>
                <li data-value="dashed">Dashed</li>
                <li data-value="dotted">Dotted</li>
            </ul>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();