<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="<?php echo $column->width; ?> ba-form-column">
<?php
    $fields = self::getFormItems($id, $column->key);
    foreach ($fields as $field) {
        $field->options = json_decode($field->options);
        include $path.'/'.$field->type.'.php';
        echo $out;
    }
?>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();