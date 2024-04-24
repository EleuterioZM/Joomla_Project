<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="customer-info-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#customer-info-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#customer-info-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#customer-info-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="customer-info-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group items-list">
                        <div class="sorting-container"></div>
                        <div class="add-new-item">
                            <span>
                                <i class="zmdi zmdi-plus-circle"></i>
                                <span class="ba-tooltip ba-right"><?php echo JText::_('ADD_NEW_ITEM'); ?></span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-settings-group submission-form-options">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('APP'); ?>
                            </span>
                            <div class="ba-custom-select submission-form-app-select">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="app">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
<?php
                                foreach ($this->apps as $value) {
                                    if ($value->type != 'blog' && $value->type != 'products' && $value->type != 'booking') {
                                        echo '<li data-value="'.$value->id.'">'.$value->title.'</li>';
                                    }
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('FIELDS'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="fields"></i>
                            </span>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="customer-info-design-options" class="row-fluid tab-pane">
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
                                    <li data-value="headline"><?php echo JText::_('HEADLINE'); ?></li>
                                    <li data-value="title"><?php echo JText::_('LABEL'); ?></li>
                                    <li data-value="field"><?php echo JText::_('FIELD'); ?></li>
                                </ul>
                            </div>
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
<?php
                    $options = ['class' => 'slideshow-margin-options', 'group' => '', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'slideshow-button-options', 'group' => 'button', 'subgroup' => 'padding'];
                    echo $this->html->load('padding', $options);
                    $options = ['class' => 'slideshow-background-options', 'group' => '', 'subgroup' => 'background'];
                    echo $this->html->load('feature-background', $options);
                    $options = ['class' => 'slideshow-border-options', 'group' => '', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
?>
                </div>
                <div id="customer-info-layout-options" class="row-fluid tab-pane">
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
<div id="edit-custom-info-dialog" class="ba-modal-sm modal hide">
    <div class="modal-body">
        <h3 class="ba-modal-title">
            <?php echo JText::_('ITEM'); ?>
        </h3>
        <div class="ba-input-lg">
            <input type="text" class="reset-input-margin" data-key="title" placeholder="<?php echo JText::_('LABEL'); ?>">
            <span class="focus-underline"></span>
        </div>
        <div class="ba-custom-select">
            <input readonly="" onfocus="this.blur()" type="text" class="reset-input-margin">
            <input type="hidden" data-key="type">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value="text"><?php echo JText::_('TEXT_INPUT'); ?></li>
                <li data-value="email"><?php echo JText::_('EMAIL'); ?></li>
                <li data-value="textarea"><?php echo JText::_('TEXTAREA'); ?></li>
                <li data-value="dropdown"><?php echo JText::_('DROPDOWN'); ?></li>
                <li data-value="checkbox"><?php echo JText::_('CHECKBOX'); ?></li>
                <li data-value="radio"><?php echo JText::_('RADIO'); ?></li>
                <li data-value="acceptance"><?php echo JText::_('ACCEPTANCE'); ?></li>
                <li data-value="headline"><?php echo JText::_('HEADLINE'); ?></li>
                <li data-value="country"><?php echo JText::_('COUNTRY'); ?></li>
            </ul>
        </div>
        <div class="ba-checkbox-parent">
            <label class="ba-checkbox ba-hide-checkbox">
                <input type="checkbox" data-key="required">
                <span></span>
            </label>
            <label><?php echo JText::_('REQUIRED'); ?></label>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL'); ?>
        </a>
        <a href="#" class="ba-btn-primary active-button" id="apply-customer-info">
            <?php echo JText::_('SAVE'); ?>
        </a>
    </div>
</div>
<div id="customer-info-item-dialog" class="ba-modal-lg modal hide">
    <div class="modal-header">
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check" id="apply-customer-info-item"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#customer-info-edit-item" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <?php echo JText::_('ITEM'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div id="customer-info-edit-item">
                <div class="ba-options-group">
                    <div class="ba-group-element">
                        <label>
                            <?php echo JText::_('TYPE'); ?>
                        </label>
                        <div class="ba-custom-select customer-info-type-select">
                            <input readonly onfocus="this.blur()" type="text">
                            <input type="hidden" data-key="type">
                            <i class="zmdi zmdi-caret-down"></i>
                            <ul>
                                <li data-value="text"><?php echo JText::_('TEXT'); ?></li>
                                <li data-value="textarea"><?php echo JText::_('TEXTAREA'); ?></li>
                                <li data-value="dropdown"><?php echo JText::_('DROPDOWN'); ?></li>
                                <li data-value="checkbox"><?php echo JText::_('CHECKBOX'); ?></li>
                                <li data-value="radio"><?php echo JText::_('RADIO'); ?></li>
                                <li data-value="acceptance"><?php echo JText::_('ACCEPTANCE'); ?></li>
                                <li data-value="headline"><?php echo JText::_('HEADLINE'); ?></li>
                                <li data-value="country"><?php echo JText::_('COUNTRY'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ba-options-group">
                    <div class="ba-group-element">
                        <label><?php echo JText::_('LABEL'); ?></label>
                        <input type="text" placeholder="<?php echo JText::_('LABEL'); ?>" data-key="title">
                        <textarea data-settings="html"></textarea>
                    </div>
                    <div class="text-customer-info-options textarea-customer-info-options email-customer-info-options
                        dropdown-customer-info-options country-customer-info-options">
                        <div class="ba-group-element">
                            <label>
                                <?php echo JText::_('PLACEHOLDER'); ?>
                            </label>
                            <input type="text" placeholder="<?php echo JText::_('PLACEHOLDER'); ?>" data-settings="placeholder">
                        </div>
                    </div>
                    <div class="items-list radio-customer-info-options checkbox-customer-info-options dropdown-customer-info-options">
                        <div class="sorting-container"></div>
                        <div class="add-new-item">
                            <span>
                                <i class="zmdi zmdi-plus-circle add-new-item-action" data-action="single"></i>
                                <span class="ba-tooltip ba-top"><?php echo JText::_('ADD_NEW_ITEM'); ?></span>
                            </span>
                            <span>
                                <i class="zmdi zmdi-playlist-plus add-new-item-action" data-action="bulk"></i>
                                <span class="ba-tooltip ba-top"><?php echo JText::_('BULK_ADDING'); ?></span>
                            </span>
                        </div>
                    </div>
                    <div class="ba-group-element">
                        <label><?php echo JText::_('REQUIRED'); ?></label>
                        <label class="ba-checkbox">
                            <input type="checkbox" data-key="required">
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="ba-options-group">
                    <div class="ba-group-element">
                        <label><?php echo JText::_('INFO_WIDTH'); ?>, %</label>
                        <div class="ba-range-wrapper">
                            <span class="ba-range-liner"></span>
                            <input type="range" class="ba-range" min="25" max="100" step="25">
                            <input type="number" readonly step="25" data-callback="emptyCallback" data-settings="width">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add-single-option-modal" class="ba-modal-sm modal hide" style="display: none;" aria-hidden="false">
    <div class="modal-body">
        <h3 class="ba-modal-title"><?php echo JText::_('ITEM'); ?></h3>
        <div class="ba-input-lg">
            <input type="text" data-key="title" placeholder="<?php echo JText::_('TITLE'); ?>">
            <span class="focus-underline"></span>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL'); ?>
        </a>
        <a href="#" class="ba-btn-primary disable-button apply-single-option">
            <?php echo JText::_('SAVE'); ?>
        </a>
    </div>
</div>
<div id="add-bulk-option-modal" class="ba-modal-md modal hide" style="display:none">
    <div class="modal-body">
        <div class="ba-modal-header">
            <h3 class="ba-modal-title"><?php echo JText::_('BULK_ADDING'); ?></h3>
            <i data-dismiss="modal" class="zmdi zmdi-close"></i>
        </div>
        <div class="bulk-options-wrapper">
            <textarea placeholder="<?php echo JText::_('ENTER_ONE_OPTION_PER_LINE'); ?>"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary apply-bulk-option disable-button">
            <?php echo JText::_('SAVE') ?>
        </a>
    </div>
</div>