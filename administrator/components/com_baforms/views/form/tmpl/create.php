<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<link rel="stylesheet" type="text/css" href="components/com_baforms/assets/css/ba-admin.css?<?php echo $this->about->version; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'/components/com_baforms/assets/icons/material/material.css'; ?>">
<script type="text/javascript">
var $f = null,
    JUri = '<?php echo JUri::root(); ?>';
document.addEventListener('DOMContentLoaded', function(){
    $f = window.jQuery;
    var notification = $f('#ba-notification');

    function showNotice(message)
    {
        if (notification.hasClass('notification-in')) {
            setTimeout(function(){
                notification.removeClass('notification-in').addClass('animation-out');
                setTimeout(function(){
                    addNoticeText(message);
                }, 400);
            }, 2000);
        } else {
            addNoticeText(message);
        }
    }

    function addNoticeText(message)
    {
        notification.find('p').text(message);
        notification.removeClass('animation-out').addClass('notification-in');
        setTimeout(function(){
            notification.removeClass('notification-in').addClass('animation-out');
        }, 3000);
    }

    $f('.ba-custom-select > i, div.ba-custom-select input').on('click', function(event){
        event.stopPropagation();
        var parent = $f(this).parent();
        $f('.visible-select').removeClass('visible-select');
        parent.find('ul').addClass('visible-select');
        parent.find('li').one('click', function(){
            var text = this.textContent.trim(),
                val = this.dataset.value;
            parent.find('input[type="text"]').val(text);
            parent.find('input[type="hidden"]').val(val).trigger('change');
        });
        parent.trigger('show');
        setTimeout(function(){
            $f('body').one('click', function(){
                $f('.visible-select').parent().trigger('customHide');
                $f('.visible-select').removeClass('visible-select');
            });
        }, 50);
    });
    $f('div.ba-custom-select').on('show', function(){
        var $this = $f(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    });
    $f('#ba-title').on('keydown', function(event){
        if (event.keyCode == 13) {
            event.preventDefault();
            event.stopPropagation();
            $f('.create-button').trigger('click');
        }
    }).on('input', function(){
        if (this.value.trim()) {
            $f('.create-button').addClass('active-button');
        } else {
            $f('.create-button').removeClass('active-button');
        }
    });
    $f('.create-button').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button') && this.dataset.clicked != 'true') {
            this.dataset.clicked = 'true';
            $f.ajax({
                type : "POST",
                dataType : 'text',
                url : "index.php?option=com_baforms&task=form.createForm",
                data : {
                    title : $f('#ba-title').val().trim()
                },
                complete : function(msg){
                    window.location.href = JUri+'administrator/index.php?option=com_baforms&view=form&id='+msg.responseText;
                }
            });
        }
    });
});
</script>
<div id="ba-notification" class="ba-alert">
    <i class="zmdi zmdi-close"></i>
    <h4><?php echo JText::_('ERROR'); ?></h4>
    <p></p>
</div>
<div id='create-form-modal' class='ba-modal-sm modal ba-modal-dialog in'>
    <div class='modal-body'>
        <div class="ba-create-page">
            <div class="ba-header-content">
                <h3 class='ba-modal-title'>
                    <?php echo JText::_('NEW_FORM'); ?>
                </h3>
            </div>
            <div class="ba-body-content">
                <form name="create_form" id="create_form" method='post'>
                    <div class="ba-input-lg">
                        <input name="ba-title" type="text" id="ba-title" placeholder="<?php echo JText::_('TITLE'); ?>">
                        <span class="focus-underline"></span>
                    </div class="ba-input-lg">
                </form>
            </div>
            <div class="ba-footer-content">
                <a href="index.php?option=com_baforms" class="ba-btn">
                    <?php echo JText::_('CANCEL'); ?>
                </a>
                <a href="#" class="ba-btn-primary create-button disable-button">
                    <?php echo JText::_('NEXT'); ?>
                </a>
            </div>
        </div>
    </div>
</div>