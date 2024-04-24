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
$year = date('Y');
$month = date('n');
$help = '';
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$today = JHtml::date(time(), 'j F Y');
$defaultValue = $field->options->default == 'today' ? $today : '';
$dataDefault = $field->options->default == 'today' ? date('Y-m-d') : '';
$hidden = $field->options->hidden ? 'true' : 'false';
$readonly = $field->options->readonly ? ' ba-readonly-calendar' : '';
$disable = '';
foreach ($field->options->disable as $data => $value) {
    $disable .= ' data-disable-'.$data.'="';
    if ($data == 'previous') {
        $disable .= (int)$value;
    } else {
        $array = array();
        foreach ($value as $key => $val) {
            $array[] = $key;
        }
        $disable .= implode(',', $array);
    }
    $disable .= '"';
}
$type = !empty($field->options->type) ? ' calendar-range-type' : '';
$attributes = $field->options->required ? ' required' : '';
$attributes .= ' aria-labelledby="label-'.$field->id.'"';
?>
<div class="ba-form-field-item ba-form-calendar-field <?php echo $className; ?>" data-type="calendar"
    data-hidden="<?php echo $hidden; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container<?php echo $readonly.$type; ?>">
            <div class="calendar-field-wrapper">
                <i class="ba-form-icons ba-icon-calendar"></i>
                <input type="text" readonly value="<?php echo $defaultValue; ?>" data-value="<?php echo $dataDefault; ?>"
                    data-start="<?php echo $field->options->start; ?>" data-year="<?php echo $year; ?>" data-month="<?php echo $month - 1; ?>"
                    <?php echo $disable; ?> data-index="0" <?php echo $attributes; ?>
                    placeholder="<?php echo htmlspecialchars($field->options->placeholder, ENT_QUOTES); ?>">
            </div>
<?php
        if (!empty($field->options->type)) {
?>
            <span class="calendar-range-delimiter"><i class="ba-form-icons ba-icon-minus"></i></span>
            <div class="calendar-field-wrapper">
                <i class="ba-form-icons ba-icon-calendar"></i>
                <input type="text" readonly 
                    data-start="<?php echo $field->options->start; ?>" data-year="<?php echo $year; ?>" data-month="<?php echo $month - 1; ?>"
                    <?php echo $disable; ?> data-index="1" <?php echo $attributes; ?>
                    placeholder="<?php echo htmlspecialchars($field->options->placeholder, ENT_QUOTES); ?>">
            </div>
<?php
        }
?>
            <input type="hidden" name="<?php echo $field->id; ?>" data-field-id="<?php echo $field->key; ?>"
                data-calendar="calendar" value="<?php echo $defaultValue; ?>">
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();