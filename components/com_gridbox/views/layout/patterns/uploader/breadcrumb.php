<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
foreach ($folders as $key => $folder) {
    $parts[] = $folder;
    $path = implode('/', $parts);
    $attr = $key != $n ? ' data-path="'.$path.'"' : '';
?>
	<span<?php echo $attr; ?>><?php echo $folder; ?></span>
<?php
	if ($key != $n) {
?>
	<i class="zmdi zmdi-chevron-right"></i>
<?php
	}
}
$out = ob_get_contents();
ob_end_clean();