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
<script>
(function(c,d){"JUri"in d||(d.JUri="<?php echo JUri::root(); ?>");c.addEventListener("click",function(a){var b=a.target?a.target.closest('[class*="ba-click-lightbox-form-"], [href*="ba-click-lightbox-form-"]'):null;if(b){a.preventDefault();if("pending"==b.clicked)return!1;b.clicked="pending";"formsAppClk"in window?formsAppClk.click(b):(a=document.createElement("script"),a.src=JUri+"components/com_baforms/assets/js/click-trigger.js",a.onload=function(){formsAppClk.click(b)},
c.head.append(a))}})})(document,window);
</script>
<?php
$out = ob_get_contents();
ob_end_clean();