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
<span class="ba-author-avatar" style="background-image: url(<?php echo $avatar; ?>);"></span>
<?php
if (self::$website->enable_gravatar == 1) {
?>
<img src="<?php echo $avatar; ?>" class="ba-gravatar-img" style="display: none !important;"
onerror="this.previousElementSibling.style.backgroundImage = 'url('+JUri+'components/com_gridbox/assets/images/default-user.png)';">
<?php
}
?>
<span class="comment-user-name"><?php echo $obj->name; ?></span>
<span class="comment-logout-action"><?php echo JText::_('LOGOUT'); ?></span>
<?php
$string = ob_get_contents();
ob_end_clean();