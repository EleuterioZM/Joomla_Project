<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$style = '';
foreach ($field->options->label->typography as $option => $value) {
    if ($option == 'font-family' && $value != 'inherit' && !in_array($value, self::$fonts)) {
        self::$fonts[] = $value;
    }
    $style .= self::setDesignCssVariable('label', 'typography', $option, $field->options, 'headline').';';
}
?>
<div class="ba-form-field-item ba-form-headline-field <?php echo $field->options->suffix ?>" data-type="headline"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>">
    <div class="ba-input-wrapper" style="<?php echo $style; ?>">
        <div class="ba-field-label-wrapper">
            <<?php echo $field->options->tag; ?> class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></<?php echo $field->options->tag; ?>>
        </div>
    </div>
    <div class="ba-edit-item close-all-modals">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip ba-top tooltip-delay ba-hide-element"><?php echo JText::_('ITEM'); ?></span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('EDIT'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element"><?php echo JText::_('COPY_ITEM'); ?></span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip ba-top tooltip-delay settings-tooltip ba-hide-element">
                    <?php echo JText::_('DELETE_ITEM'); ?>
                </span>
            </span>
            <span class="ba-edit-text ba-hide-element"><?php echo JText::_('INPUT'); ?></span>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();