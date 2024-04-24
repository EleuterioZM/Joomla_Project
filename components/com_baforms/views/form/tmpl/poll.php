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
$showResults = isset($renderResults) ? $renderResults : $field->options->close;
if (!$showResults && !empty($field->options->end)) {
    $now = strtotime("now");
    $end = strtotime($field->options->end);
    $showResults = $now > $end;
}
if (!$showResults && !$field->options->again) {
    $showResults = !self::checkUserPoll($field);
}
if ($showResults) {
    $pollResults = self::getPollResults($field->id, $field->options->items);
    $closedPoll = true;
}
$attr = $field->options->required ? 'data-required="true"' : '';
?>
<div class="ba-form-field-item ba-form-poll-field <?php echo $className; ?>" data-type="poll">
    <fieldset class="ba-input-wrapper">
        <legend class="ba-field-label-wrapper">
            <span class="ba-input-label-wrapper"><?php echo $field->options->title; ?></span><?php echo $help; ?>
        </legend>
        <div class="ba-field-container">
            <div class="ba-form-checkbox-group-wrapper<?php echo $showResults ? ' ba-poll-results' : ''; ?>"
                style="--checkbox-field-count:<?php echo $field->options->count; ?>;" <?php echo $attr; ?>>
<?php
            $index = 1;
            $pollType = $field->options->multiple ? 'checkbox' : 'radio';
            foreach ($field->options->items as $item) {
                $checkboxClassName = !empty($item->image) ? ' checkbox-image-wrapper' : '';
                if ($index == $field->options->count) {
                    $checkboxClassName .= ' last-row-checkbox-wrapper';
                    $index = 0;
                }
                $index++;
                $value = strip_tags($item->title);
                $value = htmlspecialchars($value, ENT_QUOTES);
                $style = '';
                if ($showResults) {
                    $pollResult = $pollResults->{$item->key};
                    $style = ' style="--poll-order: '.$pollResult->order.'; --poll-color:'.$item->color.';"';
                } else if (!empty($item->image) && $item->default) {
                    $checkboxClassName .= ' checked-image-container';
                }
?>
                <div class="ba-form-checkbox-wrapper<?php echo $checkboxClassName; ?>"<?php echo $style; ?>>
<?php
                if (!empty($item->image)) {
?>
                    <div class="ba-checkbox-image"><img src="<?php echo JUri::root().$item->image; ?>"></div>
<?php
                }
?>
                    <div class="ba-checkbox-wrapper">
                        <span class="ba-checkbox-title">
                            <span class="ba-form-checkbox-title">
                                <?php echo $item->title; ?>
                            </span>
                        </span>
<?php
                    if ($showResults) {
                        if ($field->options->{'vote-count'}) {
?>
                        <span class="ba-poll-votes-count"><?php echo $pollResult->votes.' '.JText::_('VOTES'); ?></span>
<?php
                        }
?>
                        <span class="ba-poll-votes-percent"><?php echo $pollResult->percent; ?>%</span>
<?php
                    } else {
?>
                        <label class="ba-form-<?php echo $pollType; ?>">
                            <input type="<?php echo $pollType; ?>" name="<?php echo $field->id; ?>[]"
                                value="<?php echo $item->key; ?>"
                                data-field-id="<?php echo $field->key; ?>"
                                <?php echo $item->default ? ' checked' : ''; ?> <?php echo $field->options->required ? ' required' : ''; ?>>
                            <span></span>
                            <span style="display: none !important;"><?php echo $item->title; ?></span>
                        </label>
<?php
                    }
?>
                    </div>
<?php
                if ($showResults) {
?>
                    <span class="ba-poll-votes-progress" style="--poll-votes-percent: <?php echo $pollResult->percent; ?>%;"></span>
<?php
                }
?>
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