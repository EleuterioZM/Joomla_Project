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
<div class="span12 ba-form-column" data-span="12" id="bacolumn-1" data-id="0">
	[ba-forms-fields]
    <div class="empty-item">
        <span>
            <i class="zmdi zmdi-layers"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('EMPTY_ITEM_TOOLTIP') ?></span>
        </span>
    </div>
    <div class="column-info">Span 12</div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();