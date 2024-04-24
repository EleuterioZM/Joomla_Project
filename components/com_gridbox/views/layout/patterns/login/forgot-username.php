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
<div class="ba-forgot-username-wrapper" data-wrapper="forgot-username" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('FORGOT_USERNAME'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('FORGOT_USERNAME_DESCRIPTION'); ?></span>
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
        <span class="ba-login-btn" data-action="username"><?php echo JText::_('SUBMIT'); ?></span>
    </div>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<div class="ba-forgot-username-sended-wrapper" data-wrapper="forgot-username-sended" style="display: none;">
    <span class="ba-login-headline"><?php echo JText::_('FORGOT_USERNAME'); ?></span>
    <span class="ba-login-description"><?php echo JText::_('EMAIL_WITH_USERNAME_SENT'); ?></span>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="login"><?php echo JText::_('BACK_TO_LOGIN'); ?></span>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();