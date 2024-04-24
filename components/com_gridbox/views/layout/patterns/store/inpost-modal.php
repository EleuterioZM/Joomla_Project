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
<div id="ba-inpost-map-dialog" class="ba-modal-lg modal hide" style="display: none;">
    <div class="modal-header">
        <div class="modal-header-icon">
            <i class="ba-icons ba-icon-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <inpost-geowidget onpoint="inpostSelectPoint" token='<?php echo $inpost->key; ?>' language='pl' config='parcelcollect'></inpost-geowidget>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();