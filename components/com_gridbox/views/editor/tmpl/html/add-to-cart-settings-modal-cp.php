<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="add-to-cart-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#add-to-cart-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#add-to-cart-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#add-to-cart-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="add-to-cart-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-desktop-windows"></i>
                            <span><?php echo JText::_('VIEW'); ?></span>
                        </div>
<?php
                    if ($this->edit_type == 'post-layout' && $this->item->type == 'products') {
?>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('AVAILABILITY'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="availability"
                                    class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SKU'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="sku" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('QUANTITY'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="quantity" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
<?php
                    }
?>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ADD_TO_CART'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="button" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ADD_TO_WISHLIST'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="wishlist" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
?>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-roller"></i>
                            <span><?php echo JText::_('PRESETS'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-lg-custom-select select-preset">
                                <input type="text" readonly onfocus="this.blur()">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <div class="ba-lg-custom-select-header">
                                        <span class="create-new-preset">
                                            <i class="zmdi zmdi-plus-circle"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('SAVE_PRESET'); ?></span>
                                        </span>
                                        <span class="edit-preset-item">
                                            <i class="zmdi zmdi-edit"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('EDIT'); ?></span>
                                        </span>
                                        <span class="delete-preset-item">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('DELETE'); ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-lg-custom-select-body">
                                        <li data-value="">
                                            <label>
                                                <input type="radio" name="preset-checkbox" value="">
                                                <i class="zmdi zmdi-circle-o"></i>
                                                <i class="zmdi zmdi-check"></i>
                                            </label>
                                            <span><?php echo JText::_('NO_NE'); ?></span>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="add-to-cart-design-options" class="row-fluid tab-pane">
                    <div class="ba-settings-group slideshow-design-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-style-custom-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="info"><?php echo JText::_('INFO'); ?></li>
                                    <li data-value="price"><?php echo JText::_('PRICE'); ?></li>
                                    <li data-value="button"><?php echo JText::_('BUTTON'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item slideshow-button-options">
                            <span>
                                <?php echo JText::_('LABEL'); ?>
                            </span>
                            <input type="text" class="add-to-cart-button-label">
                        </div>
                    </div>
                    <div class="ba-settings-group slideshow-typography-options">
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
                                        data-group="" data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_WEIGHT'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-weight" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item slideshow-typography-color">
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
                                        <input type="text" data-option="letter-spacing" data-group=""
                                            data-subgroup="typography" data-callback="sectionRules">
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
                                    <label data-option="text-decoration" data-value="underline" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-underlined"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UNDERLINE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-italic"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('ITALIC'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
                    $options = ['group' => '', 'subgroup' => 'colors'];
                    echo $this->html->load('colors', $options);
                    $options = ['class' => 'slideshow-margin-options', 'group' => 'description', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'slideshow-button-options', 'group' => 'button', 'subgroup' => 'padding'];
                    echo $this->html->load('padding', $options);
                    $options = ['class' => 'slideshow-border-options', 'group' => 'button', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
                    $options = ['class' => 'slideshow-shadow-options', 'subgroup' => 'shadow', 'group' => ''];
                    echo $this->html->load('shadow', $options);
?>
                </div>
                <div id="add-to-cart-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->load('margin');
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>