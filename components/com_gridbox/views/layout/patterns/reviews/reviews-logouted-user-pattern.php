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
<div class="ba-user-login-wrapper">
    <span class="ba-author-avatar" style="background-image: url(<?php echo $avatar; ?>);"></span>
    <span class="ba-user-login-btn" data-type="user">
        <?php echo JText::_('LOGIN') ?>
    </span>
</div>
<?php
if (self::$website->reviews_facebook_login == 1 || self::$website->reviews_google_login == 1 || self::$website->reviews_vk_login == 1) {
?>
<div class="ba-social-login-wrapper">
    <span>
        <?php echo JText::_('LOGIN_WITH'); ?>
    </span>
    <div class="ba-social-login-icons">
<?php
    if (self::$website->reviews_facebook_login == 1) {
?>
        <span>
            <i class="ba-icons ba-icon-facebook ba-comments-facebook-login"></i>
            <span class="ba-tooltip">Facebook</span>
        </span>
<?php
    }
    if (self::$website->reviews_google_login == 1) {
?>
        <span>
            <i class="ba-icons ba-icon-google ba-comments-google-login"></i>
            <div class="ba-google-login-btn-parent"></div>
            <span class="ba-tooltip">Google</span>
        </span>
<?php
    }
    if (self::$website->reviews_vk_login == 1) {
?>
        <span>
            <i class="ba-icons ba-icon-vk ba-comments-vk-login"></i>
            <span class="ba-tooltip">Vkontakte</span>
        </span>
<?php
    }
?>
    </div>
</div>
<?php
}
?>
<div class="ba-guest-login-wrapper">
    <span class="ba-guest-login-btn" data-type="guest">
        <?php echo JText::_('LOGIN_AS_GUEST'); ?>
    </span>
</div>
<?php
$string = ob_get_contents();
ob_end_clean();