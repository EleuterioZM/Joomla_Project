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
<span class="input-append input-group">
    <input type="text" class="input-medium form-control" id="<?php echo $this->id; ?>_name" value="<?php echo $title; ?>"
        placeholder="<?php echo JText::_('CATEGORY'); ?>" disabled size="35">
<?php
    echo gridboxHelper::renderBootstrapModalBtn('gridbox-category-modal');
?>
</span>
<input type="hidden" id="<?php echo $this->id; ?>_id" name="jform[request][id]" value="<?php echo $this->value; ?>">
<div class="control-group" id="select-app" style="display: none;">
    <div class="control-label">
        <label>
            <?php echo JText::_('APP'); ?>
        </label>
    </div>
    <div class="controls">
        <span class="input-append input-group">
            <input type="text" class="input-medium form-control" id="gridbox_app_name"
                value="<?php echo $appTitle; ?>" disabled size="35"
                placeholder="<?php echo JText::_('APP'); ?>">
<?php
    echo gridboxHelper::renderBootstrapModalBtn('gridbox_app_modal');
?>
        </span>
        <input type="hidden" id="gridbox_app_id" value="<?php echo $appId; ?>">
<?php
        $url = 'index.php?option=com_gridbox&view=apps&layout=modal&tmpl=component';
        echo gridboxHelper::renderBootstrapModal('gridbox_app_modal', 'APP', $url);
?>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();