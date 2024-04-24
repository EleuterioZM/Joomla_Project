<?php
/**
* @package   BaGrid
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>
<link rel="stylesheet" type="text/css" href="components/com_baforms/assets/css/ba-admin.css">
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'components/com_baforms/assets/icons/material/material.css'; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'components/com_baforms/assets/icons/fontawesome/fontawesome.css'; ?>">
<?php
if (JVERSION >= '4.0.0') {
?>
<script type="text/javascript" src="<?php echo JUri::root(); ?>media/vendor/jquery/js/jquery.min.js"></script>
<?php
} else {
?>
<script type="text/javascript" src="<?php echo JUri::root(); ?>media/jui/js/jquery.min.js"></script>
<?php
}
?>
<script type="text/javascript" src="components/com_baforms/assets/libraries/bootstrap/bootstrap.js"></script>
<script type="text/javascript">
var $f = jQuery;
document.addEventListener('DOMContentLoaded', function(){
    $f('.ba-icons-search').on('input', function(){
        var $this = this;
        $f('.ba-icons-search').not(this).val($this.value)
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            var search = $this.value.toLowerCase();
            if (!search) {
                $f('.ba-options-group').css('display', '').find(' > *').css('display', '');
            } else {
                $f('.ba-options-group').each(function(){
                    var count = 0,
                        group = $f(this),
                        elements = group.find('.ba-group-element');
                    elements.each(function(){
                        var value = this.querySelector('span').textContent.toLowerCase().trim();
                        if (value.indexOf(search) == -1) {
                            this.style.display = 'none';
                            count++;
                        } else {
                            this.style.display = '';
                        }
                    });
                    if (count == elements.length) {
                        group.hide();
                    } else {
                        group.css('display', '');
                    }
                });
            }
        }, 300);
    });
    $f('.ba-group-element').on('click', function(){
        top.fontBtn.value = this.querySelector('i').className.trim();
        top.$f(top.fontBtn).trigger('input');
        top.$f('#select-icon-dialog').modal('hide');
    });
});
</script>
<div class="icons-tabs">
    <ul class="nav nav-tabs code-nav">
        <li class="active">
            <a href="#fontawesome-tab" data-toggle="tab">
                Font Awesome
            </a>
        </li>
        <li>
            <a href="#material-tab" data-toggle="tab">
                Material
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="fontawesome-tab" class="row-fluid tab-pane active">
            <div class="ba-icons-search-wrapper">
                <input type="text" class="ba-icons-search">
                <i class="zmdi zmdi-search"></i>
            </div>
            <div class="ba-icons-scroll-wrapper">
                <?php include(JPATH_COMPONENT.'/views/layout/fontawesome.php'); ?>
            </div>
        </div>
        <div id="material-tab" class="row-fluid tab-pane">
            <div class="ba-icons-search-wrapper">
                <input type="text" class="ba-icons-search">
                <i class="zmdi zmdi-search"></i>
            </div>
            <div class="ba-icons-scroll-wrapper">
                <?php include(JPATH_COMPONENT.'/views/layout/material.php'); ?>
            </div>
        </div>
    </div>
</div>
<?php
exit;