<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$hidden = !self::$editItem->facebook->enable || !self::$editItem->google->enable ? ' ba-login-integrations-hidden-element' : '';
?>
<div class="ba-login-integrations-wrapper<?php echo $hidden; ?>">
<?php
if ($view == 'gridbox' || self::$editItem->facebook->enable) {
?>
    <div class="ba-login-integration-btn" data-integration="facebook">
        <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/social-login/facebook-logo.svg">
        <span>Facebook</span>
    </div>
<?php
}
if ($view == 'gridbox' || self::$editItem->google->enable) {
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
