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
$default = baformsHelper::$countries->{$field->options->default};
if ($field->options->required && !empty($field->options->title)) {
    $help .= '<span class="required-star">*</span>';
}
if (!empty($field->options->description)) {
    $help .= '<span class="ba-input-help"><i class="ba-form-icons ba-icon-help"></i>';
    $help .= '<span class="ba-tooltip ba-top ba-hide-element">';
    $help .= $field->options->description.'</span></span>';
}
?>
<div class="ba-form-field-item ba-form-phone-field <?php echo $className; ?>" data-type="phone">
    <div class="ba-input-wrapper">
        <div class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper" id="label-<?php echo $field->id; ?>">
                <?php echo $field->options->title; ?>
            </span>
            <?php echo $help; ?>
        </div>
        <div class="ba-field-container">
            <div class="ba-phone-countries-wrapper">
                <div class="ba-phone-selected-country" data-default="<?php echo $default->flag; ?>">
                    <span class="ba-phone-flag ba-phone-flag-<?php echo $default->flag; ?>"></span>
                    <span class="ba-phone-prefix">+<?php echo $default->prefix; ?></span>
                </div>
                <div class="ba-phone-countries-list-wrapper">
                    <span id="search-<?php echo $field->id; ?>"><?php echo JText::_('SEARCH'); ?></span>
                    <input type="text" class="ba-phone-countries-search"
                        aria-labelledby="search-<?php echo $field->id; ?>"
                        placeholder="<?php echo JText::_('SEARCH'); ?>">
                    <ul class="ba-phone-countries-list"></ul>
                </div>
            </div>
            <input type="text" class="ba-phone-number-input"<?php echo $field->options->required ? ' required' : ''; ?>
                aria-labelledby="label-<?php echo $field->id; ?>"
                placeholder="<?php echo str_replace('X', '_', $default->placeholder); ?>" data-prefix="+<?php echo $default->prefix; ?>">
            <input type="hidden" name="<?php echo $field->id; ?>" data-field-id="<?php echo $field->key; ?>">
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();