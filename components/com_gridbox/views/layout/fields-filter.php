<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions($layout);
if ($edit_type == 'blog') {
    $appFields = gridboxHelper::getAppFilterFields($id);
    $obj->items->{'item-'.$now}->app = $id;
} else {
    $appFields = array();
    $id = 0;
}
foreach ($appFields as $appField) {
    if (!empty($appField->label) && ($appField->field_type == 'checkbox' || $appField->field_type == 'radio' ||
            $appField->field_type == 'select' || $appField->field_type == 'price' || $appField->product)) {
        $obj->items->{'item-'.$now}->fields[] = $appField->field_key;
        $obj->items->{'item-'.$now}->desktop->fields->{$appField->field_key} = true;
    }
}
$obj->items->{'item-'.$now}->fields[] = 'posts-rating';
$obj->items->{'item-'.$now}->desktop->fields->{'posts-rating'} = true;
?>
<div class="ba-item-fields-filter ba-item" id="<?php echo 'item-'.$now; ?>">
    <div class="ba-items-filter-wrapper">
        <div class="open-responsive-filters">
            <i class="ba-icons ba-icon-filter-list"></i>
            <span><?php echo JText::_('FILTERS'); ?></span>
        </div>
        <div class="ba-fields-filter-wrapper">
<?php
            $str = gridboxHelper::getItemsFilter($id);
            echo $str;
?>
        </div>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("COPY_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_TO_LIBRARY"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model">
        <div class="ba-bm-top"></div>
        <div class="ba-bm-left"></div>
        <div class="ba-bm-bottom"></div>
        <div class="ba-bm-right"></div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();