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
<table class="ba-items-list">
    <tbody>
<?php
$img = JUri::root().'index.php?option=com_gridbox';
$img .= '&layout=uploader&task=uploader.showImage&image=';
foreach ($items as $item) {
    if (!isset($item->size)) {
?>
        <tr>
            <td class="select-td">
                <input class="select-item" type="checkbox" value="<?php echo htmlentities(json_encode($item)); ?>">
                <div class="folder-icons">
                    <span data-path="<?php echo $item->path; ?>" class="zmdi zmdi-folder"></span>
                    <i class="zmdi zmdi-circle-o"></i>
                    <i class="zmdi zmdi-check"></i>
                </div>
            </td>
            <td class="draggable-handler">
                <span class="folder-list" data-path="<?php echo $item->path; ?>"><?php echo $item->name; ?></span>
            </td>
            <td class="draggable-handler">
            </td>
            <td class="draggable-handler">
            </td>
        </tr>
<?php
    } else {
        $isImage = $this->isImage($item->ext);
        if ($item->ext == 'svg' || $item->ext == 'ico') {
            $src = JUri::root().$item->url.'?time=';
        } else if ($isImage) {
            $src = $img.$item->path.'&time=';
        }
?>
        <tr class="ba-images" data-ext="<?php echo $item->ext; ?>">
            <td class="select-td<?php echo !$isImage ? ' loaded ba-file-wrapper' : ''; ?>">
                <div class="ba-image">
<?php
                if ($isImage) {
?>
                    <img data-src="<?php echo $src; ?>" data-path="<?php echo $item->path; ?>">
<?php
                } else {
?>
                    <i class="zmdi zmdi-file ba-file-icon"></i>
<?php
                }
?>
                    <input class="select-item" type="checkbox" value="<?php echo htmlentities(json_encode($item)); ?>">
                    <i class="zmdi zmdi-circle-o"></i>
                    <i class="zmdi zmdi-check"></i>
                </div>
            </td>
            <td class="draggable-handler">
                <?php echo $item->name; ?>
            </td>
            <td class="draggable-handler">
                <?php echo $item->modified; ?>
            </td>
            <td class="draggable-handler">
                <?php echo $this->getFileSize($item->size); ?>
            </td>
        </tr>
<?php
    }
}
?>                                
    </tbody>
</table>
<?php
$out = ob_get_contents();
ob_end_clean();