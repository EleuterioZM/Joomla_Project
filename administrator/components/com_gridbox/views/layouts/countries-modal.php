<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="store-countries-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker visible-country"
    style="display: none;">
    <div class="modal-body-wrapper">
        <div class="modal-body modal-list-type-wrapper country-modal-body">
            <div class="ba-settings-item ba-settings-input-type">
                <div class="picker-search-wrapper">
                    <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="picker-search">
                    <i class="zmdi zmdi-search"></i>
                </div>
                <span class="add-new-picker-item add-new-country prevent-event">
                    <i class="zmdi zmdi-globe"></i>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_COUNTRY'); ?></span>
                </span>
            </div>
            <div class="ba-settings-item ba-settings-list-type">
                <ul>
                    
                </ul>
                <template class="country-li">
                    <li>
                        <span class="picker-item-icon-wrapper">
                            <i class="zmdi zmdi-globe"></i>
                        </span>
                        <input type="text">
                        <span class="country-title"></span>
                        <span class="picker-item-action-icon prevent-event" data-action="show" data-wrapper="country">
                            <i class="zmdi zmdi-pin"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('REGIONS'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="edit" data-wrapper="country">
                            <i class="zmdi zmdi-edit"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="save" data-wrapper="country">
                            <i class="zmdi zmdi-check"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="delete" data-wrapper="country">
                            <i class="zmdi zmdi-delete"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="close" data-wrapper="country">
                            <i class="zmdi zmdi-close"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('CLOSE'); ?></span>
                        </span>
                    </li>
                </template>
            </div>
        </div>
        <div class="modal-body modal-list-type-wrapper states-modal-body">
            <div class="ba-settings-item ba-settings-input-type">
                <div class="picker-search-wrapper">
                    <input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" class="picker-search">
                    <i class="zmdi zmdi-search"></i>
                </div>
                <span class="add-new-picker-item add-new-state prevent-event">
                    <i class="zmdi zmdi-pin"></i>
                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_REGION'); ?></span>
                </span>
            </div>
            <div class="ba-settings-item ba-settings-list-type">
                <div class="states-modal-header-wrapper">
                    <span class="states-back-wrapper">
                        <i class="zmdi zmdi-arrow-left "></i>
                    </span>
                    <span class="states-modal-header"></span>
                </div>
                <ul>
                    
                </ul>
                <template class="state-li">
                    <li class="prevent-event">
                        <span class="picker-item-icon-wrapper">
                            <i class="zmdi zmdi-pin"></i>
                        </span>
                        <input type="text">
                        <span class="country-title"></span>
                        <span class="picker-item-action-icon prevent-event" data-action="edit" data-wrapper="states">
                            <i class="zmdi zmdi-edit"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="save" data-wrapper="states">
                            <i class="zmdi zmdi-check"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('JAPPLY'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="delete" data-wrapper="states">
                            <i class="zmdi zmdi-delete"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE'); ?></span>
                        </span>
                        <span class="picker-item-action-icon prevent-event" data-action="close" data-wrapper="states">
                            <i class="zmdi zmdi-close"></i>
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('CLOSE'); ?></span>
                        </span>
                    </li>
                </template>
            </div>
        </div>
    </div>
</div>