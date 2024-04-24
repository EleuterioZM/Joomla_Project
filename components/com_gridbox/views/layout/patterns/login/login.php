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
<div class="ba-login-wrapper" data-wrapper="login">
    <span class="ba-login-headline"><?php echo JText::_('LOGIN'); ?></span>
<?php
if ($view == 'gridbox' || self::$editItem->facebook->enable || self::$editItem->google->enable) {
    include 'integrations.php';
}
?>
    <div class="ba-login-fields-wrapper">
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('USERNAME'); ?></span>
            <input class="ba-login-field" type="text" name="username">
        </div>
        <div class="ba-login-field-wrapper">
            <span class="ba-login-field-label"><?php echo JText::_('PASSWORD'); ?></span>
            <input class="ba-login-field" type="password" name="password" autocomplete="new-password">
        </div>
<?php
    if (JPluginHelper::isEnabled('system', 'remember')) {
?>
        <div class="ba-login-field-wrapper">
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
?>
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
        <span class="ba-login-btn" data-action="login"><?php echo JText::_('LOGIN'); ?></span>
    </div>
<?php
if ($view == 'gridbox' || self::$editItem->options->username || self::$editItem->options->password) {
?>
    <div class="ba-login-forgot-wrapper">
<?php
    if ($view == 'gridbox' || self::$editItem->options->password) {
?>
        <span class="ba-login-field-label" data-step="forgot-password"><?php echo JText::_('FORGOT_PASSWORD'); ?></span>
<?php
    }
    if ($view == 'gridbox' || self::$editItem->options->username) {
?>
        <span class="ba-login-field-label" data-step="forgot-username"><?php echo JText::_('FORGOT_USERNAME'); ?></span>
<?php
    }
?>
    </div>
<?php
}
if ($view == 'gridbox' || self::$editItem->options->registration) {
?>
    <div class="ba-login-footer-wrapper">
        <span class="ba-login-field-label" data-step="create-account"><?php echo JText::_('CREATE_AN_ACCOUNT'); ?></span>
    </div>
<?php
}
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();