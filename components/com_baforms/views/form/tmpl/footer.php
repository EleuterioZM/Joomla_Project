<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<input type="hidden" name="form-id" value="<?php echo $id; ?>">
<input type="hidden" name="task" value="form.sendMessage">
<input type="hidden" name="submit-btn" value="0">
<input type="hidden" name="page-title" value="<?php echo htmlspecialchars(self::$shortCodes->{'[Page Title]'}, ENT_QUOTES); ?>">
<input type="hidden" name="page-url" value="<?php echo htmlspecialchars(self::$shortCodes->{'[Page URL]'}, ENT_QUOTES); ?>">
<input type="hidden" name="page-id" value="<?php echo htmlspecialchars(self::$shortCodes->{'[Page ID]'}, ENT_QUOTES); ?>">
<?php
if ($copyright) {
?>
<p style="text-align: center; font-size: 12px; font-style: italic;">
<a href="http://www.balbooa.com/joomla-forms">Joomla Forms</a> makes it right. Balbooa.com</p>
<?php
}

$out = ob_get_contents();
ob_end_clean();