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
$value = self::renderDefaultValue($field->options->default);
$attributes = ' placeholder="'.htmlspecialchars($field->options->placeholder, ENT_QUOTES).'"';
$attributes .= ' data-default="'.htmlspecialchars($value, ENT_QUOTES).'"';
$value = preg_replace('/\[Field ID=+(.*?)\]/i', '', $value);
$attributes .= ' value="'.htmlspecialchars($value, ENT_QUOTES).'"';
$attributes .= $field->options->required ? ' required' : '';
$attributes .= ' aria-labelledby="label-'.$field->id.'"';
?>
<div class="ba-form-field-item ba-form-address-field <?php echo $className; ?>" data-type="address">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->icon)) {
            echo '<i class="'.$field->options->icon.'"></i>';
        }
?>
            <input type="text" name="<?php echo $field->id; ?>" data-field-id="<?php echo $field->key; ?>"<?php echo $attributes; ?>>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();