<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$className = $field->options->suffix;
if (in_array($field->key, self::$conditionLogic->hidden)) {
    $className .= ' hidden-condition-field';
}
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
?>
<div class="ba-form-field-item ba-form-acceptance-field <?php echo $className; ?>" data-type="acceptance">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container"<?php echo $field->options->required ? ' data-required="true"' : ''; ?>>
            <label class="ba-form-checkbox">
                <input type="checkbox" name="<?php echo $field->id; ?>" value="<?php echo strip_tags($field->options->html); ?>"
                    data-field-id="<?php echo $field->key; ?>">
                <span></span>
                <span style="display: none !important;"><?php echo $field->options->title; ?></span>
            </label>
            <div class="ba-form-acceptance-html"><?php echo $field->options->html; ?></div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();