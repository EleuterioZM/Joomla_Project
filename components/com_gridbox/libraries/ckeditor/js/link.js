/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myLink', {
    icons: 'link',
    init: function(editor){
        editor.addCommand('linkComand', {
            exec: function(editor){
                var link = '',
                    target = '_self',
                    type = '',
                    ckeLinkModal = top.$g('#edit-post-link-dialog'),
                    selection = editor.document.$.getSelection(),
                    range = selection.getRangeAt(0),
                    startParent = range.startContainer.parentNode;
                top.app.currentCKE = editor;
                if (startParent.localName && startParent.localName == 'a' && startParent == range.endContainer.parentNode) {
                    range.setStartBefore(startParent);
                    range.setEndAfter(startParent);
                    link = startParent.getAttribute('href');
                    target = startParent.getAttribute('target') ? startParent.getAttribute('target') : target;
                    type = startParent.hasAttribute('download') ? 'download' : '';
                }
                ckeLinkModal.find('.post-link-input').val(link);
                ckeLinkModal.find('.cke-link-target-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = target;
                    target = this.querySelector('li[data-value="'+target+'"]').textContent.trim();
                    this.querySelector('input[type="text"]').value = target;
                });
                ckeLinkModal.find('.post-link-type-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = type;
                    type = this.querySelector('li[data-value="'+type+'"]').textContent.trim();
                    this.querySelector('input[type="text"]').value = type;
                });
                top.app.checkModule('ckeLink');
            }
        });
        editor.ui.addButton('myLink', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].link.title,
            command: 'linkComand',
            toolbar: 'links',
            icon: 'link'
        });
    },
    insertLink:function(obj){
        var selection = top.app.currentCKE.document.$.getSelection();
        top.app.currentCKE.document.$.execCommand('createlink', false, obj.href);
        selection.anchorNode.parentElement.target = obj.target;
        if (!obj.type) {
            selection.anchorNode.parentElement.removeAttribute('download');
        } else {
            selection.anchorNode.parentElement.setAttribute('download', '');
        }
        top.app.currentCKE.fire( 'change' );
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myLink' : 'myLink';