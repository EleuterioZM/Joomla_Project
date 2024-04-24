/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.ckeAnchor = function() {
    $g('#text-anchor-apply').removeClass('active-button');
    $g('#text-anchor-dialog').modal();
}

$g('.text-anchor-name').on('input', function(){
    if (this.value.trim()) {
        $g('#text-anchor-apply').addClass('active-button');
    } else {
        $g('#text-anchor-apply').removeClass('active-button');
    }
});

$g('#text-anchor-apply').on('click', function(event){
    event.preventDefault();
    if ($g(this).hasClass('active-button')) {
        var name = $g('.text-anchor-name').val().trim()
        app.currentCKE.plugins.myAnchor.insertAnchor(name)
        $g('#text-anchor-dialog').modal('hide');
    }
});

app.modules.ckeAnchor = true;
app.ckeAnchor();