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
<div class="ba-form-field-item ba-form-slider-field <?php echo $className; ?>" data-type="slider">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if ($field->options->type == 'range') {
?>
            <div class="form-range-wrapper">
                <span class="ba-form-range-liner"></span>
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    aria-labelledby="label-<?php echo $field->id; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->min; ?>">
            </div>
<?php
        } else {
?>
            <div class="form-slider-wrapper">
                <span class="ba-form-range-liner" style="width: 100%;"></span>
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    aria-labelledby="label-<?php echo $field->id; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->min; ?>" data-index="0">
                <input type="range" min="<?php echo $field->options->min; ?>" max="<?php echo $field->options->max; ?>"
                    aria-labelledby="label-<?php echo $field->id; ?>"
                    step="<?php echo $field->options->step; ?>" value="<?php echo $field->options->max; ?>" data-index="1">
            </div>
<?php
        }
?>
            <div class="form-slider-input-wrapper">
<?php
            if ($field->options->type == 'range') {
?>
                <span class="set-slider-range"><?php echo $field->options->min; ?></span>
                <input type="number" value="<?php echo $field->options->min; ?>" step="<?php echo $field->options->step; ?>"
                    aria-labelledby="label-<?php echo $field->id; ?>" data-type="range">
                <span class="set-slider-range"><?php echo $field->options->max; ?></span>
                <input type="hidden" name="<?php echo $field->id; ?>" value="<?php echo $field->options->min; ?>"
                    data-field-id="<?php echo $field->key; ?>">
<?php
            } else {
?>
                <input type="number" data-type="slider" step="<?php echo $field->options->step; ?>" data-index="0"
                    aria-labelledby="label-<?php echo $field->id; ?>" value="<?php echo $field->options->min; ?>">
                <input type="number" data-type="slider" step="<?php echo $field->options->step; ?>" data-index="1"
                    aria-labelledby="label-<?php echo $field->id; ?>" value="<?php echo $field->options->max; ?>">
                <input type="hidden" name="<?php echo $field->id; ?>" value="<?php echo $field->options->min.' '.$field->options->max; ?>"
                    data-field-id="<?php echo $field->key; ?>">
<?php
            }
?>
            </div>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();