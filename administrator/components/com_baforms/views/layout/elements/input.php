<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$help = '';
$masks = array('phone', 'zip', 'time', 'date', 'card');
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
$hidden = $field->options->hidden ? 'true' : 'false';
$placeholder = $field->options->placeholder;
if (isset($formShortCodes)) {
    $default = baformsHelper::renderDefaultValue($field->options->default, $formShortCodes);
} else {
    $default = '';
}
if (in_array($field->options->type, $masks)) {
    $default = str_replace('#', '_', $field->options->mask);
    $placeholder = $default;
}
$attributes = 'value="'.htmlspecialchars($default, ENT_QUOTES).'"';
$attributes .= ' placeholder="'.htmlspecialchars($placeholder, ENT_QUOTES).'"';
$attributes .= $field->options->readonly ? ' readonly' : '';
$attributes .= ' data-type="'.$field->options->type.'"';
?>
<div class="ba-form-field-item ba-form-input-field <?php echo $field->options->suffix ?>" data-type="input"
    data-id="<?php echo $field->id ?>" id="<?php echo $field->key; ?>" data-hidden="<?php echo $hidden; ?>">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->icon)) {
            echo '<i class="'.$field->options->icon.'"></i>';
        }
        if ($field->options->type == 'email') {
?>
            <input type="email" <?php echo $attributes; ?>>
<?php
        } else if ($field->options->type == 'textarea') {
?>
            <textarea <?php echo $attributes; ?>><?php echo $default; ?></textarea>
<?php
        } else if ($field->options->type == 'password') {
?>
            <input type="password" <?php echo $attributes; ?>>
<?php
        } else {
?>
            <input type="text" <?php echo $attributes; ?>>
<?php
        }
        if (($field->options->type == 'textarea' || $field->options->type == 'text') && $field->options->characters->length != '') {
            $langKey = strtoupper($field->options->characters->key);
            $str = '('.JText::_($langKey).'. '.$field->options->characters->length.' '.JText::_('CHARACTERS').')';
?>
            <span class="characters-wrapper">
                <span class="current-characters"><?php echo strlen($field->options->default); ?></span>
                <span class="limit-characters"><?php echo $str; ?></span>
            </span>
<?php
        }
?>
            <div class="ba-input-password-icons">
                <i class="zmdi zmdi-eye" data-action="hide"></i>
                <i class="zmdi zmdi-eye-off" data-action="show"></i>
            </div>
        </div>
    </div>
<?php
if ($field->options->type == 'email' && $field->options->confirm->enable) {
    $help = '';
    if (!empty($field->options->confirm->title)) {
        $help .= '<span class="required-star">*</span>';
    }
    if (!empty($field->options->confirm->description)) {
        $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
        $help .= $field->options->confirm->description.'</span></span>';
    }
    $attributes = 'value="'.htmlspecialchars($field->options->confirm->default, ENT_QUOTES).'"';
    $attributes .= ' placeholder="'.htmlspecialchars($field->options->confirm->placeholder, ENT_QUOTES).'"';
    $attributes .= $field->options->readonly ? ' readonly' : '';
?>
    <div class="confirm-email-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->confirm->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->confirm->icon)) {
            echo '<i class="'.$field->options->confirm->icon.'"></i>';
        }
?>
            <input type="email" <?php echo $attributes; ?>>
        </div>
    </div>
<?php
} else if ($field->options->type == 'password' && $field->options->{'confirm-password'}->enable) {
    $help = '';
    if ($field->options->required && !empty($field->options->{'confirm-password'}->title)) {
        $help .= '<span class="required-star">*</span>';
    }
    if (!empty($field->options->{'confirm-password'}->description)) {
        $help .= '<span class="ba-input-help"><i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element">';
        $help .= $field->options->{'confirm-password'}->description.'</span></span>';
    }
    if (isset($formShortCodes)) {
        $default = baformsHelper::renderDefaultValue($field->options->{'confirm-password'}->default, $formShortCodes);
    } else {
        $default = '';
    }
    $attributes = 'value="'.htmlspecialchars($default, ENT_QUOTES).'"';
    $attributes .= ' placeholder="'.htmlspecialchars($field->options->{'confirm-password'}->placeholder, ENT_QUOTES).'"';
    $attributes .= ' data-type="password"';
?>
    <div class="confirm-password-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"
                contenteditable="true"><?php echo $field->options->{'confirm-password'}->title; ?></span><?php echo $help; ?>
        </div>
        <div class="ba-field-container">
<?php
        if (!empty($field->options->{'confirm-password'}->icon)) {
            echo '<i class="'.$field->options->{'confirm-password'}->icon.'"></i>';
        }
?>
            <input type="password" <?php echo $attributes; ?>>
            <div class="ba-input-password-icons">
                <i class="zmdi zmdi-eye" data-action="hide"></i>
                <i class="zmdi zmdi-eye-off" data-action="show"></i>
            </div>
        </div>
    </div>
<?php
}
?>
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