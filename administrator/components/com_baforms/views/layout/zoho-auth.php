<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<p style="font: 400 20px/30px monospace; margin-top: 50vh; text-align: center;">
	Authentication was successful. Close the tab and return to the Form editor
</p>
<script type="text/javascript">
    let obj = <?php echo json_encode($obj); ?>;
    localStorage.setItem('zoho_crm', JSON.stringify(obj));
</script>