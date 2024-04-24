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
<ul>
<?php
foreach ($folders as $folder) {
?>
    <li>
    	<span data-path="<?php echo $folder->path; ?>">
			<i class="zmdi zmdi-folder"></i>
        	<span><?php echo $folder->name; ?></span>
    	</span>
<?php
    if (count($folder->childs) > 0) {
?>
        <i class="zmdi zmdi-chevron-right ba-branch-action"></i>
<?php
        echo $this->getFoldersTree($folder->childs);
?>
<?php
    }
?>
    </li>
<?php
}
?>
</ul>
<?php
$out = ob_get_contents();
ob_end_clean();