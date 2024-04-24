/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.ckeImage = function() {
    $g('#add-cke-image').removeClass('active-button');
    $g('#cke-image-modal').modal();
}

$g('.cke-upload-image').on('mousedown', function(){
    uploadMode = 'ckeImage';
    checkIframe($g('#uploader-modal').attr('data-check', "single"), 'uploader');
});

$g('.cke-image-alt, .cke-image-width, .cke-image-height').on('input', function(){
    if ($g('.cke-upload-image').val()) {
        $g('#add-cke-image').addClass('active-button');
    }
});

$g('.cke-image-select').on('customHide', function(){
    if ($g('.cke-upload-image').val()) {
        $g('#add-cke-image').addClass('active-button');
    }
});

$g('#add-cke-image').on('click', function(event){
    event.preventDefault();
    if ($g(this).hasClass('active-button')) {
        var obj = {
            url : $g('.cke-upload-image').val(),
            alt : $g('.cke-image-alt').val(),
            width : $g('.cke-image-width').val(),
            height : $g('.cke-image-height').val(),
            align : $g('#cke-image-align').val()
        }
        app.currentCKE.plugins.myImage.insertImage(obj)
        $g('#cke-image-modal').modal('hide');
    }
});

app.modules.ckeImage = true;
app.ckeImage();