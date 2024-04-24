<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="facebook">
    <span class="social-button">
        <i class="ba-icons ba-icon-facebook"></i>Facebook
    </span>
</div>
<div class="twitter">
    <span class="social-button">
        <i class="ba-icons ba-icon-twitter"></i>Twitter
    </span>
</div>
<div class="linkedin">
    <span class="social-button">
        <i class="ba-icons ba-icon-linkedin"></i>LinkedIn
    </span>
    <span class="social-counter">0</span>
</div>
<div class="vk">
    <span class="social-button">
        <i class="ba-icons ba-icon-vk"></i>VKontakte
    </span>
    <span class="social-counter">0</span>
</div>
<div class="pinterest">
    <span class="social-button">
        <i class="ba-icons ba-icon-pinterest"></i>Pinterest
    </span>
    <span class="social-counter">0</span>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();