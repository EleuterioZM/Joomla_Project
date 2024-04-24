<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="search-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#search-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#search-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#search-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="search-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('APP'); ?>
                            </span>
                            <div class="ba-custom-select search-app-select">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="app">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="*"><?php echo JText::_('JALL'); ?></li>
                                    <li data-value="multiple"><?php echo JText::_('MULTIPLE'); ?></li>
<?php
                                foreach ($this->allApps as $value) {
                                    echo '<li data-value="'.$value->id.'" data-type="'.$value->type.'">'.$value->title.'</li>';
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item multiple-apps-list">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="apps-select-wrapper">
                                <ul class="selected-apps">
                                    <li class="search-app">
                                        <input type="text" placeholder="<?php echo JText::_('APP'); ?>" readonly>
                                    </li>
                                </ul>
                                <ul class="all-apps-list">
<?php
                                foreach ($this->allApps as $value) {
                                    echo '<li data-value="'.$value->id.'" data-type="'.$value->type.'">'.$value->title.'</li>';
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('DISPLAY_SEARCH_RESULTS_IN'); ?>
                            </span>
                            <div class="ba-custom-select search-select-display-results">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="app">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('SEARCH_RESULTS_PAGE'); ?></li>
                                    <li data-value="category"><?php echo JText::_('CATEGORY_LIST_LAYOUT'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('PLACEHOLDER'); ?>
                            </span>
                            <input type="text" placeholder="<?php echo JText::_('PLACEHOLDER'); ?>" class="search-placeholder">
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ICON'); ?>
                            </span>
                            <input class="select-input" type="text" readonly onfocus="this.blur()"
                                data-option="icon" data-group="icon"
                                placeholder="<?php echo JText::_('ICON'); ?>">
                            <i class="zmdi zmdi-attachment-alt"></i>
                            <div class="reset">
                                <i class="zmdi zmdi-close" data-group="icon" data-option="icon"
                                    data-action="sectionRules" data-callback="removeSearchIcon"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('RESET'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('LIVE_SEARCH'); ?></span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="live" class="live-store-search">
                                <span></span>
                            </label>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="search-design-options" class="row-fluid tab-pane">
                    <div class="ba-settings-group">
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
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-family"
                                        data-group="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_WEIGHT'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-weight"
                                        data-group="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('COLOR'); ?>
                                    </span>
                                    <input type="text" data-type="color" data-option="color" data-group="typography">
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
                                        <input type="text" data-option="font-size" data-group="typography" data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LETTER_SPACING'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner letter-spacing"></span>
                                        <input type="range" class="ba-range" min="-10" max="10">
                                        <input type="text" data-option="letter-spacing" data-group="typography"
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
                                        <input type="text" data-option="line-height" data-group="typography" data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-toolbar">
                                    <label data-option="text-decoration" data-value="underline" data-group="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-underlined"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UNDERLINE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-group="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-group="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-italic"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('ITALIC'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="left" data-group="h1" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-left"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="center" data-group="h1" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="right" data-group="h1" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-right"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-local-florist"></i>
                            <span><?php echo JText::_('ICON'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('POSITION'); ?>
                            </span>
                            <div class="ba-custom-select search-icon-position visible-select-top">
                                <input readonly onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="" data-option="position" data-group="icons" class="set-value-css">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('BEFORE'); ?></li>
                                    <li data-value="after"><?php echo JText::_('AFTER'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="320">
                                <input type="text" data-option="size" data-group="icons" data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->html->load('feature-background');
?>
                </div>
                <div id="search-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
                    echo $this->html->load('padding');
                    echo $this->html->load('border');
                    echo $this->html->load('shadow');
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>