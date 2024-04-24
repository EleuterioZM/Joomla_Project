<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if ($count > 0) {
?>
<span class="ba-comments-total-count"><?php echo $count.' '.JText::_('COMMENTS'); ?></span>
<select>
    <option value="recent"<?php echo $sortBy == 'recent' ? ' selected' : ''; ?>><?php echo JText::_('RECENT'); ?></option>
    <option value="oldest"<?php echo $sortBy == 'oldest' ? ' selected' : ''; ?>><?php echo JText::_('OLDEST'); ?></option>
    <option value="popular"<?php echo $sortBy == 'popular' ? ' selected' : ''; ?>><?php echo JText::_('POPULAR'); ?></option>
</select>
<?php
} else {
?>
    <span class="ba-comments-be-first-message ba-comments-total-count"><?php echo JText::_('BE_FIRST_TO_COMMENT'); ?></span>
<?php
}
$string = ob_get_contents();
ob_end_clean();