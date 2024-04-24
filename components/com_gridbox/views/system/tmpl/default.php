<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($this->item->type == 'submission-form' && strpos($this->item->html, 'data-texteditor="texteditor"')) {
?>
<link rel="stylesheet" type="text/css" href="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/css/ckeditor.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.0/ckeditor.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/justifyLeft.js"></script>
<script>
    if ($g('html').attr('dir') == 'rtl') {
        CKEDITOR.config.contentsLangDirection = 'rtl';
    }
    CKEDITOR.config.forcePasteAsPlainText = true;
    CKEDITOR.dtd.$removeEmpty.span = 0;
    CKEDITOR.dtd.$removeEmpty.i = 0;
    CKEDITOR.config.uiColor = '#fafafa';
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.config.removePlugins = 'elementspath';
    CKEDITOR.config.toolbar_Basic = [
        {name: 'styles', items: ['Format']},
        {name: 'clipboard', items: ['Undo','Redo']},
        {name: 'basicstyles', items: ['Bold','Italic','Underline']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote', 'myJustifyLeft', 'JustifyCenter','JustifyRight', 'JustifyBlock']},
        {name: 'links', items: ['Link', 'Unlink']},
        {name: 'insert', items: ['Image',]},
    ];
    CKEDITOR.config.toolbar = 'Basic';
</script>
<?php
} else if ($this->item->type == 'checkout' && gridboxHelper::$store->checkout->login && empty($user->id)) {
?>
<div class="ba-checkout-authentication-backdrop">
    <i class="ba-icons ba-icon-close ba-leave-checkout"></i>
    <div class="ba-checkout-authentication-wrapper">
        <div class="ba-checkout-login-wrapper" data-wrapper="login">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('LOGIN'); ?></span>
            </div>
<?php
        if (gridboxHelper::$store->checkout->facebook || gridboxHelper::$store->checkout->google) {
            $hidden = !gridboxHelper::$store->checkout->facebook || !gridboxHelper::$store->checkout->google ? ' ba-login-integrations-hidden-element' : '';
?>
            <div class="ba-login-integrations-wrapper<?php echo $hidden; ?>">
<?php
            if (gridboxHelper::$store->checkout->facebook) {
?>
                <div class="ba-login-integration-btn" data-integration="facebook">
                    <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/social-login/facebook-logo.svg">
                    <span>Facebook</span>
                </div>
<?php
            }
            if (gridboxHelper::$store->checkout->google) {
?>
                <div class="ba-login-integration-btn" data-integration="google">
                    <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/social-login/google-logo.svg">
                    <span>Google</span>
                    <div class="ba-google-login-button"></div>
                </div>
<?php
            }
?>
            </div>
            <div class="ba-login-or-wrapper">
                <span class="ba-login-field-label"><?php echo JText::_('OR'); ?></span>
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('USERNAME'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="username">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('PASSWORD'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="password" name="password" autocomplete="new-password">
            </div>
<?php
        if (JPluginHelper::isEnabled('system', 'remember')) {
?>
            <div class="ba-checkout-authentication-checkbox">
                <div class="ba-checkbox-wrapper">
                    <span><?php echo JText::_('REMEMBER_ME'); ?></span>
                    <label class="ba-checkbox">
                        <input type="checkbox" name="remember">
                        <span></span>
                    </label>
                </div>
            </div>
<?php
        }
        if (!empty(gridboxHelper::$store->checkout->recaptcha)) {
?>
            <div class="ba-login-captcha-wrapper" data-type="<?php echo gridboxHelper::$store->checkout->recaptcha; ?>">
                
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-user-authentication"><?php echo JText::_('LOGIN'); ?></span>
            </div>
            <div class="ba-checkout-authentication-links">
                <div class="ba-checkout-authentication-forgot-wrapper">
<?php
                if (gridboxHelper::$store->checkout->password) {
?>
                    <a href="#" data-step="forgot-password">
                        <?php echo JText::_('FORGOT_PASSWORD'); ?>
                    </a>
<?php
                }
                if (gridboxHelper::$store->checkout->username) {
?>
                    <a href="#" data-step="forgot-username">
                        <?php echo JText::_('FORGOT_USERNAME'); ?>
                    </a>
<?php
                }
?>                    
                </div>
<?php
            if (gridboxHelper::$store->checkout->registration) {
?>
                <span class="ba-show-registration-dialog" data-step="create-account"><?php echo JText::_('CREATE_AN_ACCOUNT'); ?></span>
<?php
        }
?>
            </div>
        </div>
<?php
    if (gridboxHelper::$store->checkout->password) {
?>
        <div class="ba-forgot-password-wrapper" data-wrapper="forgot-password" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('FORGOT_PASSWORD'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('FORGOT_PASSWORD_DESCRIPTION'); ?></span>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('EMAIL'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="email">
            </div>
<?php
        if (!empty(gridboxHelper::$store->checkout->recaptcha)) {
?>
            <div class="ba-login-captcha-wrapper" data-type="<?php echo gridboxHelper::$store->checkout->recaptcha; ?>">
                
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-remind-password-authentication"><?php echo JText::_('SUBMIT'); ?></span>
            </div>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
        <div class="ba-password-request-wrapper" data-wrapper="password-request" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('PASSWORD_RESET'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('EMAIL_HAS_SENT_TO_YOUR_EMAIL_ADDRESS'); ?></span>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('USERNAME'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="username">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('VERIFICATION_CODE'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="code">
            </div>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-request-password-authentication"><?php echo JText::_('SUBMIT'); ?></span>
            </div>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
        <div class="ba-password-reset-wrapper" data-wrapper="password-reset" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('PASSWORD_RESET'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('ENTER_NEW_PASSWORD'); ?></span>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('PASSWORD'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="hidden" name="id">
                <input type="password" name="password1">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('CONFIRM_PASSWORD'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="password" name="password2">
            </div>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-reset-password-authentication"><?php echo JText::_('SUBMIT'); ?></span>
            </div>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
        <div class="ba-password-successful-reset-wrapper" data-wrapper="password-successful-reset" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('PASSWORD_RESET'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('RESET_PASSWORD_SUCCESSFUL'); ?></span>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
<?php
    }
    if (gridboxHelper::$store->checkout->username) {
?>
        <div class="ba-forgot-username-wrapper" data-wrapper="forgot-username" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('FORGOT_USERNAME'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('FORGOT_USERNAME_DESCRIPTION'); ?></span>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('EMAIL'); ?></span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="email">
            </div>
<?php
        if (!empty(gridboxHelper::$store->checkout->recaptcha)) {
?>
            <div class="ba-login-captcha-wrapper" data-type="<?php echo gridboxHelper::$store->checkout->recaptcha; ?>">
                
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-username-authentication"><?php echo JText::_('SUBMIT'); ?></span>
            </div>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
        <div class="ba-forgot-username-sended-wrapper" data-wrapper="forgot-username-sended" style="display: none;">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('FORGOT_USERNAME'); ?></span>
            </div>
            <span class="ba-login-description"><?php echo JText::_('EMAIL_WITH_USERNAME_SENT'); ?></span>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
<?php
    }
    if (gridboxHelper::$store->checkout->registration) {
?>
        <div class="ba-checkout-registration-wrapper" style="display: none;" data-wrapper="create-account">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('CREATE_AN_ACCOUNT'); ?></span>
            </div>
<?php
        if (gridboxHelper::$store->checkout->facebook || gridboxHelper::$store->checkout->google) {
            $hidden = !gridboxHelper::$store->checkout->facebook || !gridboxHelper::$store->checkout->google ? ' ba-login-integrations-hidden-element' : '';
?>
            <div class="ba-login-integrations-wrapper<?php echo $hidden; ?>">
<?php
            if (gridboxHelper::$store->checkout->facebook) {
?>
                <div class="ba-login-integration-btn" data-integration="facebook">
                    <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/social-login/facebook-logo.svg">
                    <span>Facebook</span>
                </div>
<?php
            }
            if (gridboxHelper::$store->checkout->google) {
?>
                <div class="ba-login-integration-btn" data-integration="google">
                    <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/social-login/google-logo.svg">
                    <span>Google</span>
                    <div class="ba-google-login-button"></div>
                </div>
<?php
            }
?>
            </div>
            <div class="ba-login-or-wrapper">
                <span class="ba-login-field-label"><?php echo JText::_('OR'); ?></span>
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('NAME'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="name">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('USERNAME'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="text" name="username">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('PASSWORD'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="password" name="password1">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('CONFIRM_PASSWORD'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="password" name="password2">
            </div>
            <div class="ba-checkout-authentication-label">
                <span><?php echo JText::_('EMAIL'); ?> *</span>
            </div>
            <div class="ba-checkout-authentication-input">
                <input type="email" name="email1">
            </div>
<?php
        if (gridboxHelper::$store->checkout->terms) {
?>
            <div class="ba-checkout-authentication-checkbox">
                <div class="ba-checkbox-wrapper">
                    <div><?php echo gridboxHelper::$store->checkout->terms_text; ?></div>
                    <label class="ba-checkbox">
                        <input type="checkbox" name="acceptance">
                        <span></span>
                    </label>
                </div>
            </div>
<?php
        }
        if (!empty(gridboxHelper::$store->checkout->recaptcha)) {
?>
            <div class="ba-login-captcha-wrapper" data-type="<?php echo gridboxHelper::$store->checkout->recaptcha; ?>">
                
            </div>
<?php
        }
?>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-user-registration"><?php echo JText::_('CREATE_AN_ACCOUNT'); ?></span>
            </div>
            <div class="ba-login-footer-wrapper">
                <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
            </div>
        </div>
<?php
    }
    if (gridboxHelper::$store->checkout->guest) {
?>
        <div class="ba-checkout-guest-wrapper">
            <div class="ba-checkout-authentication-title">
                <span><?php echo JText::_('GUEST_CHECKOUT'); ?></span>
            </div>
            <div class="ba-checkout-authentication-text">
                <span><?php echo JText::_('PURCHASE_AS_GUEST'); ?></span>
            </div>
            <div class="ba-checkout-authentication-btn-wrapper">
                <span class="ba-checkout-authentication-btn ba-guest-authentication"><?php echo JText::_('CONTINUE_AS_GUEST'); ?></span>
            </div>
        </div>
<?php
    }
?>
    </div>
</div>
<?php
}
?>
<div class="row-fluid">
<?php
if ($user->authorise('core.edit', 'com_gridbox')) {
?>
    <a class="edit-page-btn" target="_blank"
        href="<?php echo JUri::root().'index.php?option=com_gridbox&view=editor&edit_type=system&tmpl=component&id='.$this->item->id; ?>">
        <i class="ba-icons ba-icon-settings"></i>
        <span class="ba-tooltip ba-top"><?php echo JText::_('EDIT_PAGE'); ?></span>
    </a>
<?php
}
?>
    <div class="ba-gridbox-page row-fluid">
<?php
    if (!empty($this->item)) echo $this->item->html;
?>
    </div>
</div>