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
<div class="ba-blog-post[classname]" data-id="0">
    [ba-blog-post-image]
    <div class="ba-blog-post-content">
        [ba-blog-post-title]
        <div class="ba-blog-post-info-wrapper">
            [ba-blog-post-date]
            [ba-blog-post-category]
            [ba-blog-post-views]
        </div>
        [ba-blog-post-intro]
        [ba-blog-post-btn]
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();