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
                    ckeLinkModal = top.$f('#edit-post-link-dialog'),
                    selection = editor.document.$.getSelection(),
                    range = selection.getRangeAt(0),
                    startParent = range.startContainer.parentNode;
                top.app.currentCKE = editor;
                if (startParent.localName && startParent.localName == 'a' && startParent == range.endContainer.parentNode) {
                    range.setStartBefore(startParent);
                    range.setEndAfter(startParent);
                    link = startParent.getAttribute('href');
                    target = startParent.getAttribute('target') ? startParent.getAttribute('target') : target;
                }
                ckeLinkModal.find('.post-link-input').val(link);
                ckeLinkModal.find('.cke-link-target-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = target;
                    target = this.querySelector('li[data-value="'+target+'"]').textContent.trim();
                    this.querySelector('input[type="text"]').value = target;
                });
                top.$f('#post-link-apply').removeClass('active-button');
                top.$f('#edit-post-link-dialog').modal();
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
        top.app.currentCKE.fire('change');
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myLink' : 'myLink';