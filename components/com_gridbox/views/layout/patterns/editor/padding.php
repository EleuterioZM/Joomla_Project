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
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"': 'data-group="padding"';
$attr .= isset($options['subgroup']) ? 'data-subgroup="'.$options['subgroup'].'"' : '';
$states = isset($options['states']) ? $options['states'] : [];
?>
<div class="ba-settings-group states-settings-group padding-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <span><?php echo JText::_('PADDING'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="default" data-method="setPaddingValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="hover" data-method="setPaddingValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-hover.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('HOVER'); ?></span>
                    </span>
<?php
                foreach ($states as $state) {
                    $img = JUri::root().'components/com_gridbox/assets/images/states/state-'.$state.'.png';
                    $title = strtoupper($state);
?>
                    <span class="ba-states-icon" <?php echo $attr; ?> data-action="<?php echo $state; ?>"
                        data-method="setPaddingValues">
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
$keys = isset($options['keys']) ? $options['keys'] : $this->keys['padding'];
?>
    <div class="ba-settings-toolbar">
<?php
    foreach ($keys as $key) {
?>
        <div>
            <span>
                <?php echo JText::_(strtoupper($key)); ?>
            </span>
            <input type="text" <?php echo $attr; ?> data-option="<?php echo $key; ?>" data-callback="sectionRules">
        </div>
<?php
    }
?>
        <div>
            <i class="zmdi zmdi-close" data-type="reset" data-action="sectionRules"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('RESET'); ?>
            </span>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();