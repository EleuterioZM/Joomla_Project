/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.openPageSettings = function(){
    setTimeout(function(){
        $g("#settings-dialog").modal();
    }, 150);
}

$g("#settings-dialog").on('hide', function(){
    var metaTags = $g('select.meta_tags option'),
        str = '';
    if (metaTags.length > 0) {
        metaTags.each(function(){
            str += '<a href="#" class="ba-btn-transition"><span>'+this.textContent+'</span></a>';
        });
        var items = app.editor.document.querySelectorAll('.ba-item-post-tags .ba-button-wrapper');
        for (var i = 0; i < items.length; i++) {
            items[i].innerHTML = str;
        }
    }
});

$g("#settings-dialog").on('shown', function(){
    $g('.alert-backdrop').addClass('active');
}).on('hide', function(){
    $g('.alert-backdrop').removeClass('active');
});

$g('div.alert-backdrop, #settings-dialog .modal-header-icon i').on('mousedown', function(event){
    event.preventDefault();
    event.stopPropagation();
    var title = $g('.page-title').val();
    title = $g.trim(title);
    if (title) {
        $g("#settings-dialog").modal('hide');
        $g('.alert-backdrop').removeClass('active');
    }
});

$g('.page-title').on('input', function(){
    var $this = $g(this),
        title = $this.val();
    title = $g.trim(title);
    if (!title) {
        $g('#settings-dialog i.zmdi-check').addClass('disabled-button');
        $this.parent().find('.ba-alert-container').show();
    } else {
        $g('#settings-dialog i.zmdi-check').removeClass('disabled-button');
        $this.parent().find('.ba-alert-container').hide();
    }
});

app.openPageSettings();
app.modules.openPageSettings = true;