<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="language-switcher-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
    <div class="modal-header">
        <span class="ba-dialog-title"></span>
        <div class="modal-header-icon">
            <div class="ba-custom-select select-modal-cp-position">
                <i class="zmdi zmdi-more-vert"></i>
                <input type="hidden">
                <ul>
                    <li data-value=""><?php echo JText::_('SEPARATE_WINDOW') ?></li>
                    <li data-value="right"><?php echo JText::_('PANEL_TO_RIGHT') ?></li>
                </ul>
            </div>
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#language-switcher-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#language-switcher-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#language-switcher-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="language-switcher-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('LAYOUT'); ?>
                            </span>
                            <div class="ba-custom-select language-switcher-layout-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="ba-default-layout"><?php echo JText::_('DEFAULT'); ?></li>
                                    <li data-value="ba-dropdown-layout"><?php echo JText::_('DROPDOWN'); ?></li>
                                    <li data-value="ba-lightbox-layout"><?php echo JText::_('LIGHTBOX'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="language-switcher-design-options" class="row-fluid tab-pane">
                    <div class="ba-settings-group
                        slideshow-design-group ba-dropdown-layout-options ba-lightbox-layout-options">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-style-custom-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="switcher"><?php echo JText::_('LANGUAGE_SWITCHER'); ?></li>
                                    <li data-value="list"><?php echo JText::_('LANGUAGE_LIST'); ?></li>
                                    <li data-value="dropdown" class="ba-dropdown-layout-options">
                                        <?php echo JText::_('DROPDOWN'); ?>
                                    </li>
                                    <li data-value="dropdown" class="ba-lightbox-layout-options">
                                        <?php echo JText::_('LIGHTBOX'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group slideshow-typography-options switcher-options list-options
                        ba-dropdown-layout-options ba-lightbox-layout-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-format-size"></i>
                            <span><?php echo JText::_('TYPOGRAPHY'); ?></span>
                        </div>
                        <div class="theme-typography-options">
                            <div class="typography-options">
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_FAMILY'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-family" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_WEIGHT'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-weight"
                                        data-group="" data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item ba-style-typography-color">
                                    <span>
                                        <?php echo JText::_('COLOR'); ?>
                                    </span>
                                    <input type="text" data-type="color" data-option="color" data-group="" data-subgroup="typography">
                                    <span class="minicolors-opacity-wrapper">
                                        <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                        min="0" max="1" step="0.01">
                                        <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                    </span>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('SIZE'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner"></span>
                                        <input type="range" class="ba-range" min="0" max="320">
                                        <input type="text" data-option="font-size" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LETTER_SPACING'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner letter-spacing"></span>
                                        <input type="range" class="ba-range" min="-10" max="10">
                                        <input type="text" data-option="letter-spacing" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LINE_HEIGHT'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner"></span>
                                        <input type="range" class="ba-range" min="0" max="640">
                                        <input type="text" data-option="line-height" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-toolbar">
                                    <label data-option="text-decoration" data-value="underline" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-underlined"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UNDERLINE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-italic"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('ITALIC'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="left" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-left"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="center" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="right" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-right"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group switcher-options list-options
                        ba-dropdown-layout-options ba-lightbox-layout-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-globe-alt"></i>
                            <span><?php echo JText::_('FLAG'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="320">
                                <input type="text" data-option="size" data-subgroup="flag" data-group=""
                                    data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('BORDER_RADIUS'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="320">
                                <input type="text" data-option="radius" data-subgroup="flag" data-group=""
                                    data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group dropdown-options ba-dropdown-layout-options ba-lightbox-layout-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-format-color-fill"></i>
                            <span><?php echo JText::_('BACKGROUND'); ?></span>
                        </div>
                        <div class="ba-settings-item background">
                            <span>
                                <?php echo JText::_('COLOR'); ?>
                            </span>
                            <input type="text" data-type="color" data-group="" data-subgroup="background" data-option="color">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                min="0" max="1" step="0.01">
                                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                    </div>
<?php
                    $options = ['group' => 'dropdown', 'subgroup' => 'padding',
                        'class' => 'dropdown-options ba-dropdown-layout-options ba-lightbox-layout-options'];
                    echo $this->html->load('padding', $options);
                    $options = ['group' => 'dropdown', 'subgroup' => 'border',
                        'class' => 'dropdown-options ba-dropdown-layout-options ba-lightbox-layout-options'];
                    echo $this->html->load('border', $options);
                    $options = ['group' => 'dropdown', 'subgroup' => 'shadow',
                        'class' => 'dropdown-options ba-dropdown-layout-options ba-lightbox-layout-options'];
                    echo $this->html->load('shadow', $options);
?>


                    <div class="ba-settings-group ba-default-layout-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-globe-alt"></i>
                            <span><?php echo JText::_('FLAG'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="320">
                                <input type="text" data-option="size" data-group="flag" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('BORDER_RADIUS'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="320">
                                <input type="text" data-option="radius" data-group="flag" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-toolbar">
                            <label data-option="align" data-value="left" data-group="flag" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-left"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('LEFT'); ?>
                                </span>
                            </label>
                            <label data-option="align" data-value="center" data-group="flag" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-center"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('CENTER'); ?>
                                </span>
                            </label>
                            <label data-option="align" data-value="right" data-group="flag" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-right"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('RIGHT'); ?>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="language-switcher-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>