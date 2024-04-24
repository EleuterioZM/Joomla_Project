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
<div class="ba-column-resizer">
    <span>
        <i class="zmdi zmdi-more-vert"></i>
    </span>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();