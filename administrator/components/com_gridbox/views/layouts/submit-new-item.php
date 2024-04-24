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
    <input type="text" class="input-medium form-control" id="<?php echo $this->id; ?>_name"
        value="<?php echo $title; ?>" placeholder="<?php echo JText::_('APP'); ?>" disabled size="35">
<?php
    echo gridboxHelper::renderBootstrapModalBtn('gridbox-app-modal');
?>    
</span>
<input type="hidden" id="<?php echo $this->id ?>_id" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>">
<?php
$out = ob_get_contents();
ob_end_clean();