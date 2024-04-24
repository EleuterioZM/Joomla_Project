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
$group = isset($options['group']) ? $options['group'] : 'background';
$attr = isset($options['group']) ? 'data-group="'.$options['group'].'"' : 'data-group="background-states"';
$attr .= isset($options['subgroup']) ? 'data-subgroup="'.$options['subgroup'].'"' : '';

?>
<div class="ba-settings-group states-settings-group background-settings-group <?php echo $className; ?>">
    <div class="settings-group-title">
        <span><?php echo JText::_('STATES'); ?></span>
        <div class="ba-states-wrapper">
            <div class="ba-states-actions-wrapper">
                <div class="ba-states-icons-wrapper">
                    <span class="ba-states-icon" <?php echo $attr; ?>
                        data-action="default" data-method="setBackgroundValues">
                        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/states/state-normal.png">
                        <span class="ba-tooltip ba-bottom"><?php echo JText::_('NORMAL'); ?></span>
                    </span>
                    <span class="ba-states-icon" <?php echo $attr; ?>
                        data-action="hover" data-method="setBackgroundValues">
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
    <div class="ba-settings-item ba-disable-states">
        <span>
            <?php echo JText::_('TYPE'); ?>
        </span>
        <div class="ba-custom-select background-select" data-group="background"
            data-option="type" data-callback="sectionRules">
            <input readonly onfocus="this.blur()" value="<?php echo JText::_('COLOR'); ?>" type="text">
            <input type="hidden" value="color" data-group="background" data-option="type">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value="color"><?php echo JText::_('COLOR'); ?></li>
                <li data-value="blur"><?php echo JText::_('BLUR'); ?></li>
                <li data-value="gradient"><?php echo JText::_('GRADIENT'); ?></li>
                <li data-value="image"><?php echo JText::_('IMAGE'); ?></li>
                <li data-value="video"><?php echo JText::_('VIDEO'); ?></li>
                <li data-value="none"><?php echo JText::_('NO_NE'); ?></li>
            </ul>
        </div>
    </div>
    <div class="background-options">
        <div class="color-options">
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
        <div class="blur-options">
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
        <div class="image-options" style="display: none;">
            <div class="ba-settings-item">
                <span>
                    <?php echo JText::_('UPLOAD_BG_IMAGE'); ?>
                </span>
                <input type="text" class="select-input" readonly onfocus="this.blur()"
                    data-type="upload-image" <?php echo $attr; ?> data-option="image"
                    placeholder="<?php echo JText::_('SELECT'); ?>" data-action="sectionRules">
                <i class="zmdi zmdi-attachment-alt"></i>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('ATTACHMENT'); ?>
                </span>
                <div class="ba-custom-select attachment">
                    <input readonly onfocus="this.blur()" value="fixed" type="text">
                    <input type="hidden" value="fixed" data-option="attachment" data-group="background"
                        data-subgroup="image" data-action="sectionRules">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="fixed">Fixed</li>
                        <li data-value="scroll">Scroll</li>
                    </ul>
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('SIZE'); ?>
                </span>
                <div class="ba-custom-select backround-size">
                    <input readonly onfocus="this.blur()" value="cover" type="text">
                    <input type="hidden" value="cover" data-option="size" data-group="background"
                        data-subgroup="image" data-action="sectionRules">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="cover">Cover</li>
                        <li data-value="contain">Contain</li>
                        <li data-value="initial">Auto</li>
                    </ul>
                </div>
            </div>
            <div class="contain-size-options" style="display: none;">
                <div class="ba-settings-item ba-disable-states">
                    <span>
                        <?php echo JText::_('POSITION'); ?>
                    </span>
                    <div class="ba-custom-select backround-position">
                        <input readonly onfocus="this.blur()" value="center center" type="text">
                        <input type="hidden" value="center center" data-option="position" data-group="background"
                            data-subgroup="image" data-action="sectionRules">
                        <i class="zmdi zmdi-caret-down"></i>
                        <ul>
                            <li data-value="left top">Left Top</li>
                            <li data-value="left center">Left Center</li>
                            <li data-value="left bottom">Left Bottom</li>
                            <li data-value="right top">Right Top</li>
                            <li data-value="right center">Right Center</li>
                            <li data-value="right bottom">Right Bottom</li>
                            <li data-value="center top">Center Top</li>
                            <li data-value="center center">Center Center</li>
                            <li data-value="center bottom">Center Bottom</li>
                        </ul>
                    </div>
                </div>
                <div class="ba-settings-item ba-disable-states">
                    <span>
                        <?php echo JText::_('REPEAT'); ?>
                    </span>
                    <div class="ba-custom-select backround-repeat">
                        <input readonly onfocus="this.blur()" value="no-repeat" type="text">
                        <input type="hidden" value="no-repeat" data-option="repeat" data-group="background"
                            data-subgroup="image" data-action="sectionRules">
                        <i class="zmdi zmdi-caret-down"></i>
                        <ul>
                            <li data-value="repeat">Repeat</li>
                            <li data-value="repeat-x">Repeat-x</li>
                            <li data-value="repeat-y">Repeat-y</li>
                            <li data-value="no-repeat">No-repeat</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states desktop-only">
                <span>
                    <?php echo JText::_('PARALLAX'); ?>
                </span>
                <label class="ba-checkbox">
                    <input type="checkbox" data-option="enable" data-group="parallax">
                    <span></span>
                </label>
            </div>
            <div class="parallax-options">
                <div class="ba-settings-item ba-disable-states desktop-only">
                    <span>
                        <?php echo JText::_('TYPE'); ?>
                    </span>
                    <div class="ba-custom-select parallax-type-select">
                        <input readonly onfocus="this.blur()" value="" type="text">
                        <input type="hidden" value="" data-action="sectionRules">
                        <i class="zmdi zmdi-caret-down"></i>
                        <ul>
                            <li data-value="scroll"><?php echo JText::_('SCROLL'); ?></li>
                            <li data-value="mousemove"><?php echo JText::_('MOUSE_MOVEMENT'); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="ba-settings-item ba-disable-states desktop-only">
                    <span>
                        <?php echo JText::_('PARALLAX_OFFSET'); ?>
                    </span>
                    <div class="ba-range-wrapper">
                        <span class="ba-range-liner"></span>
                        <input type="range" class="ba-range" min="0.1" max="0.5" step="0.1">
                        <input type="number" data-option="offset" data-group="parallax" step="0.1"
                            data-module="loadParallax" data-callback="sectionRules">
                    </div>
                </div>
                <div class="ba-settings-item ba-disable-states desktop-only">
                    <span>
                        <?php echo JText::_('INVERT'); ?>
                    </span>
                    <label class="ba-checkbox">
                        <input type="checkbox" data-option="invert" data-group="parallax">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="gradient-options" style="display: none;">
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('EFFECT'); ?>
                </span>
                <div class="ba-custom-select gradient-effect-select">
                    <input readonly onfocus="this.blur()" value="" type="text">
                    <input type="hidden" value="" data-property="background">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="linear">Linear</li>
                        <li data-value="radial">Radial</li>
                    </ul>
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states background-linear-gradient">
                <span>
                    <?php echo JText::_('ANGLE'); ?>
                </span>
                <div class="ba-range-wrapper">
                    <span class="ba-range-liner"></span>
                    <input type="range" class="ba-range" min="0" max="360" step="1">
                    <input type="number" data-option="angle" data-group="background" data-subgroup="gradient"
                        step="1" data-callback="sectionRules">
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('START_COLOR'); ?>
                </span>
                <input type="text" data-type="color" data-option="color1" data-group="background"
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
                    <input type="number" data-option="position1" data-group="background" data-subgroup="gradient"
                        step="1" data-callback="sectionRules">
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('END_COLOR'); ?>
                </span>
                <input type="text" data-type="color" data-option="color2" data-group="background"
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
                    <input type="number" data-option="position2" data-group="background" data-subgroup="gradient"
                        step="1" data-callback="sectionRules">
                </div>
            </div>
        </div>
        <div class="video-options desktop-only" style="display: none;">
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('VIDEO_SOURCE'); ?>
                </span>
                <div class="ba-custom-select video-select">
                    <input readonly onfocus="this.blur()" value="Youtube" type="text">
                    <input type="hidden" value="youtube" data-option="video-type" data-group="video">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="youtube">Youtube</li>
                        <li data-value="vimeo">Vimeo</li>
                        <li data-value="source"><?php echo JText::_('SOURCE_FILE'); ?></li>
                    </ul>
                </div>
            </div>
            <div class="ba-settings-item ba-disable-states video-id">
                <span>
                    <?php echo JText::_('VIDEO_ID'); ?>
                </span>
                <input type="text" data-option="id" data-group="video"
                    placeholder="<?php echo JText::_('VIDEO_ID'); ?>">
            </div>
            <div class="ba-settings-item ba-disable-states video-source-select">
                <span>
                    <?php echo JText::_('SOURCE_FILE'); ?>
                </span>
                <input type="text" class="select-input" readonly onfocus="this.blur()" data-option="source"
                    data-group="video" placeholder="<?php echo JText::_('SELECT'); ?>">
                <i class="zmdi zmdi-attachment-alt"></i>
                <label class="ba-help-icon">
                    <i class="zmdi zmdi-help"></i>
                    <span class="ba-tooltip ba-help">
                        <?php echo JText::_('SOURCE_FILE_TOOLTIP'); ?>
                    </span>
                </label>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('MUTE'); ?>
                </span>
                <label class="ba-checkbox">
                    <input type="checkbox" data-option="mute" data-group="video">
                    <span></span>
                </label>
            </div>
            <div class="ba-settings-item ba-disable-states">
                <span>
                    <?php echo JText::_('START'); ?>
                </span>
                <input type="text" data-option="start" data-group="video"
                    placeholder="<?php echo JText::_('START'); ?>">
            </div>
            <div class="ba-settings-item ba-disable-states youtube-quality">
                <span>
                    <?php echo JText::_('QUALITY'); ?>
                </span>
                <div class="ba-custom-select video-quality">
                    <input readonly onfocus="this.blur()" value="720p" type="text">
                    <input type="hidden" value="hd720" data-option="quality" data-group="video">
                    <i class="zmdi zmdi-caret-down"></i>
                    <ul>
                        <li data-value="hd720">720p</li>
                        <li data-value="large">480p</li>
                        <li data-value="medium">360p</li>
                        <li data-value="small">240p</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();