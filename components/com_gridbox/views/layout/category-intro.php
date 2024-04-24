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
<div class="ba-item-category-intro ba-item" id="item-15003687281">
    <div class="intro-post-wrapper">
        <div class="intro-post-image-wrapper">
            <div class="ba-overlay"></div>
            <div class="intro-post-image"></div>
        </div>
        <div class="intro-post-title-wrapper">
            <h1 class="intro-post-title"></h1>
        </div>
        <div class="intro-post-info">
            <div class="intro-category-description"></div>
        </div>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">Item</span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    Edit
                </span>
            </span>
            <span class="ba-edit-text">Item</span>
        </div>
    </div>
    <div class="ba-box-model">
        <div class="ba-bm-top"></div>
        <div class="ba-bm-left"></div>
        <div class="ba-bm-bottom"></div>
        <div class="ba-bm-right"></div>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();