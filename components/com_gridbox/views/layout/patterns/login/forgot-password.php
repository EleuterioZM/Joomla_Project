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
<div class="ba-forgot-password-wrapper" data-wrapper="forgot-password" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('FORGOT_PASSWORD'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('FORGOT_PASSWORD_DESCRIPTION'); ?></span>
    <div class="ba-login-fields-wrapper">
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('EMAIL'); ?></span>
            <input class="ba-login-field" type="email">
        </div>
    </div>
<?php
if ($view != 'gridbox' && !empty(self::$editItem->options->recaptcha)) {
?>
    <div class="ba-login-captcha-wrapper" data-type="<?php echo self::$editItem->options->recaptcha; ?>">
        
    </div>
<?php
}
?>
    <div class="ba-login-btn-wrapper">
        <span class="ba-login-btn" data-action="remindPassword"><?php echo JText::_('SUBMIT'); ?></span>
    </div>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<div class="ba-password-request-wrapper" data-wrapper="password-request" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('PASSWORD_RESET'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('EMAIL_HAS_SENT_TO_YOUR_EMAIL_ADDRESS'); ?></span>
    <div class="ba-login-fields-wrapper">
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('USERNAME'); ?></span>
            <input class="ba-login-field" type="text" name="username">
        </div>
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('VERIFICATION_CODE'); ?></span>
            <input class="ba-login-field" type="text" name="code">
        </div>
    </div>
    <div class="ba-login-btn-wrapper">
        <span class="ba-login-btn" data-action="requestPassword"><?php echo JText::_('SUBMIT'); ?></span>
    </div>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<div class="ba-password-reset-wrapper" data-wrapper="password-reset" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('PASSWORD_RESET'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('ENTER_NEW_PASSWORD'); ?></span>
    <div class="ba-login-fields-wrapper">
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('PASSWORD'); ?></span>
            <input type="hidden" name="id">
            <input class="ba-login-field" type="password" name="password1">
        </div>
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('CONFIRM_PASSWORD'); ?></span>
            <input class="ba-login-field" type="password" name="password2">
        </div>
    </div>
    <div class="ba-login-btn-wrapper">
        <span class="ba-login-btn" data-action="resetPassword"><?php echo JText::_('SUBMIT'); ?></span>
    </div>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<div class="ba-password-successful-reset-wrapper" data-wrapper="password-successful-reset" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('PASSWORD_RESET'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('RESET_PASSWORD_SUCCESSFUL'); ?></span>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();