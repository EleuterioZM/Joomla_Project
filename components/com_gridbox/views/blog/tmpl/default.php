<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


defined('_JEXEC') or die;

if ($this->schema) {
    echo $this->schema;
}
?>
<script>
    themeData.edit_type = 'blog';
</script>
<div class="row-fluid">
<?php
    if ($this->canEdit) {
?>
        <a class="edit-page-btn" target="_blank"
            href="<?php echo JUri::root().'index.php?option=com_gridbox&view=editor&edit_type=blog&tmpl=component&id='.$this->item->id; ?>">
            <i class="ba-icons ba-icon-settings"></i>
            <span class="ba-tooltip ba-top"><?php echo JText::_('EDIT_PAGE'); ?></span>
        </a>
<?php
    }
?>
    <div class="ba-gridbox-page row-fluid">
        <?php echo $this->item->params; ?>
    </div>
</div>