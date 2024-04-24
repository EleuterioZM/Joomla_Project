<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="category-applies-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker" style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="picker-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul></ul>
        </div>
    </div>
</div>