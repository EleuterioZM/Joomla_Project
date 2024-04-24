<div id="lottie-animations-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#lottie-animations-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li class="">
                    <a href="#lottie-animations-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="lottie-animations-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SOURCE'); ?>
                            </span>
                            <div class="ba-custom-select lottie-animations-source-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="link"><?php echo JText::_('EXTERNAL_URL'); ?></li>
                                    <li data-value="file"><?php echo JText::_('SOURCE_FILE'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item lottie-link-source-options">
                            <span>
                                <?php echo JText::_('LINK'); ?>
                            </span>
                            <input type="text" placeholder="<?php echo JText::_('LINK'); ?>">
                        </div>
                        <div class="ba-settings-item lottie-file-source-options">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <input type="text" readonly onfocus="this.blur()" class="select-input" data-action="sectionRules"
                                placeholder="<?php echo JText::_('SELECT'); ?>">
                            <i class="zmdi zmdi-attachment-alt"></i>
                            <label class="ba-help-icon">
                                <i class="zmdi zmdi-help"></i>
                                <span class="ba-tooltip ba-help">
                                    <?php echo JText::_('LOTTIE_SOURCE_FILE_TOOLTIP'); ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-desktop-windows"></i>
                            <span><?php echo JText::_('VIEW'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('WIDTH'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="2500">
                                <input type="text" data-option="width" data-group="style" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-toolbar">
                            <label data-value="left" data-option="align" data-group="style" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-left"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('LEFT'); ?>
                                </span>
                            </label>
                            <label data-value="center" data-option="align" data-group="style" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-center"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('CENTER'); ?>
                                </span>
                            </label>
                            <label data-value="right" data-option="align" data-group="style" data-callback="sectionRules">
                                <i class="zmdi zmdi-format-align-right"></i>
                                <span class="ba-tooltip">
                                    <?php echo JText::_('RIGHT'); ?>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-tune"></i>
                            <span><?php echo JText::_('SETTINGS'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('TRIGGER'); ?>
                            </span>
                            <div class="ba-custom-select lottie-animations-trigger-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="autoplay"><?php echo JText::_('AUTOPLAY'); ?></li>
                                    <li data-value="viewport"><?php echo JText::_('VIEWPORT'); ?></li>
                                    <li data-value="scroll"><?php echo JText::_('ON_SCROLL'); ?></li>
                                    <li data-value="hover"><?php echo JText::_('ON_HOVER'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('LOOP'); ?></span>
                            <label class="ba-checkbox">
                                <input type="checkbox" class="lottie-animations-loop">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ANIMATION_SPEED'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="2.5" step="0.1">
                                <input type="number" data-option="speed" step="0.1" data-callback="lottieCallback">
                            </div>
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
                <div id="lottie-animations-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
                    echo $this->html->load('border');
                    echo $this->html->load('shadow');
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>