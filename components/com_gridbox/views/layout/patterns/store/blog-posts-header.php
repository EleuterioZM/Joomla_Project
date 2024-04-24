<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if ($isStore) {
    $list = self::getBlogPostsSortingList();
?>
<div class="blog-posts-sorting-wrapper">
    <select class="blog-posts-sorting" data-url="<?php echo $url; ?>">
<?php
    foreach ($list as $key => $text) {
?>
        <option value="<?php echo $key; ?>"<?php echo $order == $key ? ' selected' : ''; ?>><?php echo $text; ?></option>
<?php
    }
?>
    </select>
</div>
<?php
}
$header = ob_get_contents();
ob_end_clean();