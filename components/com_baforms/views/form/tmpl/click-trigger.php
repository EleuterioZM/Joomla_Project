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
(function(d, w){
    if (!('JUri' in w)) {
        w.JUri = '<?php echo JUri::root(); ?>';
    }
    d.addEventListener('click', function(event){
        let target = event.target ? event.target.closest('[class*="ba-click-lightbox-form-"], [href*="ba-click-lightbox-form-"]') : null;
        if (target) {
            event.preventDefault();
            if (target.clicked == 'pending') {
                return false;
            }
            target.clicked = 'pending';
            if (!('formsAppClk' in window)) {
                let s = document.createElement('script');
                s.src = JUri+'components/com_baforms/assets/js/click-trigger.js';
                s.onload = function(){
                    formsAppClk.click(target);
                }
                d.head.append(s);
            } else {
                formsAppClk.click(target);
            }
        }
    });
}(document, window));
</script>
<?php
$out = ob_get_contents();
ob_end_clean();