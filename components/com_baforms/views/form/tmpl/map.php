<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$map = json_encode($field->options->map);
$marker = json_encode($field->options->marker);
?>
<div class="ba-form-field-item ba-form-map-field <?php echo $field->options->suffix ?>" data-type="map">
    <div class="ba-map-wrapper"
    	style="--map-field-height: <?php echo $field->options->height; ?>px;"
    	data-controls="<?php echo (int)$field->options->controls; ?>"
    	data-map="<?php echo htmlentities($map, ENT_COMPAT); ?>"
    	data-marker="<?php echo htmlentities($marker, ENT_COMPAT); ?>"
    	data-style-type="<?php echo $field->options->styleType; ?>"></div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();