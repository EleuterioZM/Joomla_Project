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
<div class="ba-settings-group positioning-settings-group">
    <div class="settings-group-title">
        <span><?php echo JText::_('POSITIONING'); ?></span>
    </div>
    <div class="ba-settings-item">
        <span>
            <?php echo JText::_('POSITION'); ?>
        </span>
        <div class="ba-custom-select positioning-select">
            <input readonly onfocus="this.blur()" type="text">
            <input type="hidden">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value=""><?php echo JText::_('DEFAULT'); ?></li>
                <li data-value="absolute"><?php echo JText::_('ABSOLUTE'); ?></li>
                <li data-value="fixed"><?php echo JText::_('FIXED'); ?></li>
            </ul>
        </div>
    </div>
    <div class="positioning-sub-options">
        <div class="ba-settings-item">
            <span>z-index</span>
            <div class="ba-custom-select positioning-z-index-select">
                <input readonly onfocus="this.blur()" type="text">
                <input type="hidden" data-option="z" data-group="positioning">
                <i class="zmdi zmdi-caret-down"></i>
                <ul>
                    <li data-value="1">1</li>
                    <li data-value="2">2</li>
                    <li data-value="3">3</li>
                    <li data-value="4">4</li>
                    <li data-value="5">5</li>
                </ul>
            </div>
        </div>
        <div class="ba-settings-item">
            <span>
                <?php echo JText::_('HORIZONTAL'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="2000">
                <input type="number" data-option="x" data-group="positioning"
                    data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-item">
            <span>
                <?php echo JText::_('VERTICAL'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="0" max="2000">
                <input type="number" data-option="y" data-group="positioning"
                    data-callback="sectionRules">
            </div>
        </div>
        <div class="ba-settings-toolbar">
            <label data-option="horizontal" data-value="left"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-left"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('LEFT'); ?>
                </span>
            </label>
            <label data-option="horizontal" data-value="center"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-vertical"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('CENTER'); ?>
                </span>
            </label>
            <label data-option="horizontal" data-value="right"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-right"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('RIGHT'); ?>
                </span>
            </label>
            <label data-option="vertical" data-value="top"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-top"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('TOP'); ?>
                </span>
            </label>
            <label data-option="vertical" data-value="center"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-horizontal"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('CENTER'); ?>
                </span>
            </label>
            <label data-option="vertical" data-value="bottom"
                data-group="positioning" data-callback="sectionRules">
                <i class="zmdi zmdi-border-bottom"></i>
                <span class="ba-tooltip">
                    <?php echo JText::_('BOTTOM'); ?>
                </span>
            </label>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();