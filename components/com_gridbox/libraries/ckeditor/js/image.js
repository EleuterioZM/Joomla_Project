/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myImage', {
    icons: 'image',
    init: function(editor){
        editor.addCommand('imgComand', {
            exec: function(editor){
                var align = src = w = h = alt = label = '',
                    ckeImageModal = top.$g('#cke-image-modal'),
                    selected = editor.getSelection().getSelectedElement();
                top.app.currentCKE = editor;
                if (selected && selected.$.localName == 'img') {
                    var ckeImage = selected.$;
                    src = ckeImage.src;
                    alt = ckeImage.alt;
                    w = ckeImage.style.width.replace('px', '');
                    h = ckeImage.style.height.replace('px', '');
                    align = ckeImage.style.float;
                    label = ckeImageModal.find('.cke-image-select li[data-value="'+align+'"]').text();
                    label = $g.trim(label);
                }
                ckeImageModal.find('.cke-upload-image').val(src);
                ckeImageModal.find('.cke-image-alt').val(alt);
                ckeImageModal.find('.cke-image-width').val(w);
                ckeImageModal.find('.cke-image-height').val(h);
                ckeImageModal.find('#cke-image-align').val(align);
                ckeImageModal.find('.cke-image-align').val(label);
                top.app.checkModule('ckeImage');
            }
        });
        editor.ui.addButton('myImage', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].common.image,
            command: 'imgComand',
            toolbar: 'insert',
            icon: 'image'
        });
    },
    insertImage:function(obj){
        if (obj.width) {
            obj.width += 'px';
        }
        if (obj.height) {
            obj.height += 'px';
        }
        var selected = top.app.currentCKE.getSelection().getSelectedElement();
        if (selected && selected.$.localName == 'img') {
            var ckeImage = selected.$;
            ckeImage.src = obj.url;
            ckeImage.alt = $g.trim(obj.alt);
            ckeImage.style.width = obj.width;
            ckeImage.style.height = obj.height;
            ckeImage.style.float = obj.align;
        } else {
            var style = 'style="'+(obj.width ? 'width: '+obj.width+'; ' : '' )+(obj.height ? 'height: '+obj.height+';' : '')+
                (obj.align ? ' float: '+obj.align+';' : '')+'"',
                img = '<img src="'+obj.url+'" alt="'+$g.trim(obj.alt)+'" '+style+'>';
            top.app.currentCKE.insertHtml(img);
        }
        if (top.app.currentCKE.element.$.classList.contains('content-text')) {
            $g(top.app.currentCKE.element.$).trigger('input');
        }
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myImage' : 'myImage';