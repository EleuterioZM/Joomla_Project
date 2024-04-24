/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.ckeLink = function() {
    $g('#post-link-apply').removeClass('active-button');
    $g('#edit-post-link-dialog').modal();
}

$g('.post-link-input').on('input', function(){
    if (this.value.trim()) {
        $g('#post-link-apply').addClass('active-button');
    } else {
        $g('#post-link-apply').removeClass('active-button');
    }
}).on('focus', function(){
    this.closest('.link-picker-container').classList.add('focus-link-input');
}).on('blur', function(){
    this.closest('.link-picker-container').classList.remove('focus-link-input');
});

$g('.cke-link-target-select').on('customAction', function(){
    if ($g('.post-link-input').val().trim()) {
        $g('#post-link-apply').addClass('active-button');
    } else {
        $g('#post-link-apply').removeClass('active-button');
    }
});

$g('.post-link-type-select').on('customAction', function(){
    if ($g('.post-link-input').val().trim()) {
        $g('#post-link-apply').addClass('active-button');
    } else {
        $g('#post-link-apply').removeClass('active-button');
    }
});

$g('#post-link-apply').on('click', function(event){
    event.preventDefault();
    if ($g(this).hasClass('active-button')) {
        var obj = {
            href: $g('.post-link-input').val().trim(),
            target: $g('.cke-link-target-select input[type="hidden"]').val(),
            type: $g('.post-link-type-select input[type="hidden"]').val()
        }
        app.currentCKE.plugins.myLink.insertLink(obj)
        $g('#edit-post-link-dialog').modal('hide');
    }
});

app.modules.ckeLink = true;
app.ckeLink();