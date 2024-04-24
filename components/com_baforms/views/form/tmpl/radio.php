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
<div class="ba-form-field-item ba-form-radio-field <?php echo $className; ?>" data-type="radio">
    <fieldset class="ba-input-wrapper">
        <legend class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </legend>
        <div class="ba-field-container">
            <div class="ba-form-checkbox-group-wrapper"
                style="--checkbox-field-count:<?php echo $field->options->count; ?>;"
                <?php echo $field->options->required ? 'data-required="true"' : ''; ?>>
<?php
            $index = 1;
            foreach ($field->options->items as $item) {
                $checkboxClassName = !empty($item->image) ? ' checkbox-image-wrapper' : '';
                $checkboxClassName .= !empty($item->image) && $item->default ? ' checked-image-container' : '';
                if ($index == $field->options->count) {
                    $checkboxClassName .= ' last-row-checkbox-wrapper';
                    $index = 0;
                }
                $index++;
                $value = strip_tags($item->title);
                $value = htmlspecialchars($value, ENT_QUOTES);
?>
                <div class="ba-form-checkbox-wrapper<?php echo $checkboxClassName; ?>">
<?php
                if (!empty($item->image)) {
?>
                    <div class="ba-checkbox-image"><img src="<?php echo JUri::root().$item->image; ?>"
                        alt="<?php echo $value; ?>"></div>
<?php
                }
?>
                    <div class="ba-checkbox-wrapper">
                        <span class="ba-checkbox-title">
                            <span class="ba-form-checkbox-title">
                                <?php echo $item->title; ?>
                            </span>
                        </span>
                        <label class="ba-form-radio">
                            <input type="radio" name="<?php echo $field->id; ?>" value="<?php echo $value; ?>"
                                data-field-id="<?php echo $field->key; ?>" data-price="<?php echo $item->price; ?>"
                                data-product="<?php echo $field->options->type; ?>"
                                <?php echo $item->default ? ' checked' : ''; ?> <?php echo $field->options->required ? ' required' : ''; ?>>
                            <span></span>
                            <span style="display: none !important;"><?php echo $item->title; ?></span>
                        </label>
                    </div>
                </div>
<?php
            }
?>
            </div>
        </div>
    </fieldset>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();