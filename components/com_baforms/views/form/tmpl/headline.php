<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$style = '';
foreach ($field->options->label->typography as $option => $value) {
    if ($option == 'font-family' && $value != 'inherit' && !in_array($value, self::$fonts)) {
        self::$fonts[] = $value;
    }
    $style .= self::setDesignCssVariable('label', 'typography', $option, $field->options, 'headline').';';
}
?>
<div class="ba-form-field-item ba-form-headline-field <?php echo $field->options->suffix ?>" data-type="headline">
    <div class="ba-input-wrapper" style="<?php echo $style; ?>">
        <div class="ba-field-label-wrapper" data-field-id="<?php echo $field->key; ?>">
            <<?php echo $field->options->tag; ?> class="ba-input-label-wrapper"
                ><?php echo $field->options->title; ?></<?php echo $field->options->tag; ?>>
        </div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();