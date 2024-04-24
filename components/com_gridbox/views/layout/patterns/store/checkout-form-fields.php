<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();

$style = isset($obj->settings->width) ? 'style="--ba-checkout-field-width:'.$obj->settings->width.'%;"' : '';
?>
<div class="ba-checkout-form-fields"<?php echo $style; ?> data-type="<?php echo $obj->type; ?>" data-name="<?php echo $obj->id; ?>">
    <div class="ba-checkout-form-title-wrapper">
        <span class="ba-checkout-form-title"><?php echo $obj->title ?></span>
<?php
    if ($obj->required == 1 && !empty($obj->title)) {
?>
        <span class="ba-checkout-form-required-star">*</span>
<?php
    }
?>
    </div>
    <div class="ba-checkout-form-field-wrapper">
<?php
    $attr = $obj->required == 1 ? ' required' : '';
    if ($obj->type == 'textarea' || $obj->type == 'text' || $obj->type == 'email') {
        $attr .= isset($obj->settings->placeholder) ? ' placeholder="'.$obj->settings->placeholder.'"' : '';
    }
    if ($obj->type == 'textarea') {
?>
        <textarea name="<?php echo $obj->id; ?>"<?php echo $attr; ?>><?php echo $customer ? $customer->value : ''; ?></textarea>
<?php
    } else if ($obj->type == 'text' || $obj->type == 'email') {
        $attr .= $customer ? ' value="'.$customer->value.'"' : '';
?>
        <input type="<?php echo $obj->type; ?>" name="<?php echo $obj->id; ?>"<?php echo $attr; ?>>
<?php
    } else if ($obj->type == 'dropdown') {
?>
        <select name="<?php echo $obj->id; ?>"<?php echo $attr; ?>>
            <option value=""><?php echo $obj->settings->placeholder; ?></option>
<?php
        foreach ($obj->settings->options as $option) {
            $selected = $customer && $option == $customer->value ? ' selected' : '';
?>
            <option value="<?php echo $option; ?>"<?php echo $selected; ?>><?php echo $option; ?></option>
<?php
        }
?>
        </select>
<?php
    } else if ($obj->type == 'checkbox' || $obj->type == 'radio') {
        foreach ($obj->settings->options as $option) {
            $value = strip_tags($option);
            $value = htmlspecialchars($value, ENT_QUOTES);
            $checked = $customer && $value == $customer->value ? ' checked' : '';
?>
            <div class="ba-checkbox-wrapper">
                <span><?php echo $option; ?></span>
                <label class="ba-<?php echo $obj->type; ?>">
                    <input type="<?php echo $obj->type; ?>" name="<?php echo $obj->id; ?>"<?php echo $attr.$checked; ?>
                        value="<?php echo $value; ?>">
                    <span></span>
                </label>
            </div>
<?php
        }
    } else if ($obj->type == 'acceptance') {
        $value = strip_tags($obj->settings->html);
        $checked = $customer && $value == $customer->value ? ' checked' : '';
?>
        <div class="ba-checkbox-wrapper acceptance-checkbox-wrapper">
            <label class="ba-checkbox">
                <input type="checkbox" name="<?php echo $obj->id; ?>"<?php echo $attr.$checked; ?>
                    value="<?php echo $value; ?>">
                <span></span>
            </label>
        </div>
        <div class="ba-checkout-acceptance-html"><?php echo $obj->settings->html; ?></div>
<?php
    } else if ($obj->type == 'country') {
        $countries = gridboxHelper::getTaxCountries();
        $value = $customer ? $customer->value : '{"country":"","region":""}';
        if (!empty($value)) {
            $object = json_decode($value);
        } else {
            $object = null;
        }
?>
        <div class="ba-checkout-country-wrapper">
            <select data-type="country"<?php echo $attr; ?>>
                <option value=""><?php echo $obj->settings->placeholder; ?></option>
    <?php
            foreach ($countries as $country) {
                $selected = $object && $country->id == $object->country ? ' selected' : '';
    ?>
                <option value="<?php echo $country->id; ?>"<?php echo $selected; ?>><?php echo $country->title; ?></option>
    <?php
            }
    ?>
            </select>
        </div>
        <div class="ba-checkout-country-wrapper">
            <select data-type="region" data-selected="<?php echo $object ? $object->region : ''; ?>"<?php echo $attr; ?>></select>
        </div>
        <input type="hidden" name="<?php echo $obj->id; ?>" value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>">
<?php
    }
?>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();