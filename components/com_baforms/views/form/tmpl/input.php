<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$masks = array('phone', 'zip', 'time', 'date', 'card');
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
$hidden = $field->options->hidden ? 'true' : 'false';
if (in_array($field->options->type, $masks)) {
    $value = str_replace('#', '_', $field->options->mask);
} else {
    $value = self::renderDefaultValue($field->options->default);
}
$attributes = ' placeholder="'.htmlspecialchars($field->options->placeholder, ENT_QUOTES).'"';
$attributes .= ' data-default="'.htmlspecialchars($value, ENT_QUOTES).'"';
$value = preg_replace('/\[Field ID=+(.*?)\]/i', '', $value);
$attributes .= ' value="'.htmlspecialchars($value, ENT_QUOTES).'"';
$attributes .= $field->options->readonly ? ' readonly' : '';
$attributes .= $field->options->required ? ' required' : '';
$attributes .= ' aria-labelledby="label-'.$field->id.'"';
if (($field->options->type == 'textarea' || $field->options->type == 'text') && $field->options->characters->length != ''
    && $field->options->characters->key == 'max') {
    $attributes .= ' maxlength="'.$field->options->characters->length.'"';
}
if (!empty($field->options->validation) && !in_array($field->options->type, $masks) && $field->options->type != 'password') {
    $attributes .= ' data-validation="'.$field->options->validation.'"';
}
if (in_array($field->options->type, $masks)) {
    $attributes .= ' data-mask="'.$field->options->mask.'"';
}
?>
<div class="ba-form-field-item ba-form-input-field <?php echo $className; ?>" data-type="input"
    data-hidden="<?php echo $hidden; ?>">
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
        if ($field->options->type == 'email') {
?>
            <input type="email" name="<?php echo $field->id; ?>"<?php echo $attributes; ?> data-field-id="<?php echo $field->key; ?>">
<?php
        } else if ($field->options->type == 'password') {
?>
            <input type="password" name="<?php echo $field->id; ?>"<?php echo $attributes; ?> data-field-id="<?php echo $field->key; ?>">
            <div class="ba-input-password-icons">
                <i class="ba-form-icons ba-icon-eye" data-action="hide"></i>
                <i class="ba-form-icons ba-icon-eye-off" data-action="show"></i>
            </div>
<?php
        } else if ($field->options->type == 'textarea') {
?>
            <textarea name="<?php echo $field->id; ?>"<?php echo $attributes; ?>
                data-field-id="<?php echo $field->key; ?>"><?php echo $value; ?></textarea>
<?php
        } else {
?>
            <input type="text" name="<?php echo $field->id; ?>"
                <?php echo $attributes; ?> data-field-id="<?php echo $field->key; ?>">
<?php
        }
        if (($field->options->type == 'textarea' || $field->options->type == 'text') && $field->options->characters->length != '') {
            $langKey = strtoupper($field->options->characters->key);
            $str = '('.JText::_($langKey).'. '.$field->options->characters->length.' '.JText::_('CHARACTERS').')';
?>
            <span class="characters-wrapper" data-length="<?php echo $field->options->characters->length; ?>"
                data-direction="<?php echo $field->options->characters->key; ?>">
                <span class="current-characters"><?php echo strlen($field->options->default); ?></span>
                <span class="limit-characters"><?php echo $str; ?></span>
            </span>
<?php
        }
?>
        </div>
    </div>
<?php
if ($field->options->type == 'email' && $field->options->confirm->enable) {
    $help = '';
    if (!empty($field->options->confirm->title)) {
        $help .= '<span class="required-star">*</span>';
    }
    if (!empty($field->options->confirm->description)) {
        $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
        $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
        $help .= $field->options->confirm->description.'</span></span>';
    }
    $value = self::renderDefaultValue($field->options->confirm->default);
    $attributes = ' placeholder="'.htmlspecialchars($field->options->confirm->placeholder, ENT_QUOTES).'"';
    $attributes .= ' data-default="'.htmlspecialchars($value, ENT_QUOTES).'"';
    $value = preg_replace('/\[Field ID=+(.*?)\]/i', '', $value);
    $attributes .= ' value="'.htmlspecialchars($value, ENT_QUOTES).'"';
    $attributes .= $field->options->readonly ? ' readonly' : '';
?>
    <div class="confirm-email-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->confirm->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->confirm->icon)) {
            echo '<i class="'.$field->options->confirm->icon.'"></i>';
        }
?>
            <input type="email"<?php echo $attributes; ?>>
        </div>
    </div>
<?php
} else if ($field->options->type == 'password' && $field->options->{'confirm-password'}->enable) {
    $help = '';
    if ($field->options->required && !empty($field->options->{'confirm-password'}->title)) {
        $help .= '<span class="required-star">*</span>';
    }
    if (!empty($field->options->{'confirm-password'}->description)) {
        $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
        $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
        $help .= $field->options->{'confirm-password'}->description.'</span></span>';
    }
    $default = baformsHelper::renderDefaultValue($field->options->{'confirm-password'}->default);
    $attributes = 'value="'.htmlspecialchars($default, ENT_QUOTES).'"';
    $attributes .= ' placeholder="'.htmlspecialchars($field->options->{'confirm-password'}->placeholder, ENT_QUOTES).'"';
    $attributes .= $field->options->required ? ' required' : '';
?>
    <div class="confirm-password-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->{'confirm-password'}->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->{'confirm-password'}->icon)) {
            echo '<i class="'.$field->options->{'confirm-password'}->icon.'"></i>';
        }
?>
            <input type="password" <?php echo $attributes; ?>>
            <div class="ba-input-password-icons">
                <i class="ba-form-icons ba-icon-eye" data-action="hide"></i>
                <i class="ba-form-icons ba-icon-eye-off" data-action="show"></i>
            </div>
        </div>
    </div>
<?php
}
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();