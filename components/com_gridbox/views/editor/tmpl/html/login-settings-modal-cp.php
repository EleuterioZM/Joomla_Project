<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="login-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#login-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#login-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#login-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="login-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('LOGIN'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="options" data-option="login">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item login-sub-options">
                            <span>
                                <?php echo JText::_('PASSWORD_REMINDER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="options" data-option="password">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item login-sub-options">
                            <span>
                                <?php echo JText::_('USERNAME_REMINDER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="options" data-option="username">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('USER_REGISTRATION'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="options" data-option="registration">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item login-acceptance-option">
                            <span>
                                <?php echo JText::_('ACCEPTANCE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="acceptance" data-option="enable" class="set-value-css">
                                <span></span>
                            </label>
                            <span class="edit-login-acceptance">
                                <i class="zmdi zmdi-settings"></i>
                                <span class="ba-tooltip ba-bottom"><?php echo JText::_('EDIT'); ?></span>
                            </span>
                        </div>
                        <div class="ba-settings-item link-picker-container login-redirect-option">
                            <span>
                                <?php echo JText::_('USER_REDIRECT'); ?>
                            </span>
                            <input type="text" data-option="redirect" data-group="options" placeholder="<?php echo JText::_('LINK'); ?>">
                            <div class="select-link">
                                <i class="zmdi zmdi-attachment-alt"></i>
                                <span class="ba-tooltip"><?php echo JText::_('LINK_PICKER'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-settings"></i>
                            <span><?php echo JText::_('INTEGRATIONS'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                Facebook login
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="facebook" data-option="enable">
                                <span></span>
                            </label>
<?php
                            echo $this->html->load('integrations', $this->integrations->facebook_login);
?>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                Google login
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="google" data-option="enable">
                                <span></span>
                            </label>
<?php
                            echo $this->html->load('integrations', $this->integrations->google_login);
?>
                        </div>
                        <div class="ba-settings-item">
                            <span>reCAPTCHA</span>
                            <div class="ba-custom-select login-select-recaptcha">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('NO_NE'); ?></li>
                                </ul>
                            </div>
                            <div style="display: none !important;">
<?php
                                echo $this->form->getInput('login_recaptcha');
?>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="login-design-options" class="row-fluid tab-pane">
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
                                    <li data-value="description"><?php echo JText::_('DESCRIPTION'); ?></li>
                                    <li data-value="field"><?php echo JText::_('FIELD'); ?></li>
                                    <li data-value="button"><?php echo JText::_('button'); ?></li>
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
                    $options = ['group' => '', 'subgroup' => 'colors'];
                    echo $this->html->load('colors', $options);
                    $options = ['class' => 'slideshow-margin-options', 'group' => '', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'slideshow-button-options', 'group' => 'button', 'subgroup' => 'padding'];
                    echo $this->html->load('padding', $options);
                    $options = ['class' => 'slideshow-background-options', 'group' => '', 'subgroup' => 'background'];
                    echo $this->html->load('feature-background', $options);
                    $options = ['class' => 'slideshow-border-options', 'group' => '', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
                    $options = ['class' => 'slideshow-shadow-options', 'subgroup' => 'shadow', 'group' => ''];
                    echo $this->html->load('shadow', $options);
                    $options = ['class' => 'slideshow-design-group'];
                    echo $this->html->load('feature-background', $options);
?>
                </div>
                <div id="login-layout-options" class="row-fluid tab-pane">
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
<div id="login-acceptance-edit-modal" class="ba-modal-lg modal hide">
    <div class="modal-header">
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check" id="apply-login-acceptance-html"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs uploader-nav">
                <li class="active">
                    <a href="#edit-login-acceptance" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <?php echo JText::_('ITEM'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div id="edit-login-acceptance">
                <div class="ba-options-group">
                    <div class="ba-group-element">
                        <label>
                            <?php echo JText::_('LABEL'); ?>
                        </label>
                        <input type="text" class="login-acceptance-html" placeholder="<?php echo JText::_('LABEL'); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>