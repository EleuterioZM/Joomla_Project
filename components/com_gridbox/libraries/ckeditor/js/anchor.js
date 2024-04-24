/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myAnchor', {
    icons: 'anchor',
    init: function(editor){
        editor.addCommand('myAnchorComand', {
            exec: function(editor){
                var name = '',
                    ckeLinkModal = top.$g('#text-anchor-dialog'),
                    selection = editor.document.$.getSelection(),
                    range = selection.getRangeAt(0),
                    startParent = range.startContainer.parentNode;
                top.app.currentCKE = editor;
                if (startParent.localName && startParent.localName == 'a' && startParent == range.endContainer.parentNode) {
                    range.setStartBefore(startParent);
                    range.setEndAfter(startParent);
                    name = startParent.getAttribute('name');
                }
                ckeLinkModal.find('.text-anchor-name').val(name);
                top.app.checkModule('ckeAnchor');
            }
        });
        editor.ui.addButton('myAnchor', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].link.anchor.toolbar,
            command: 'myAnchorComand',
            toolbar: 'links',
            icon: 'anchor'
        });
    },
    insertAnchor:function(name){
        var selection = top.app.currentCKE.document.$.getSelection(),
            anchor = document.createElement("a");
            anchor.name = name;
        if (selection.getRangeAt && selection.rangeCount) {
            let range = selection.getRangeAt(0);
            anchor.appendChild(document.createTextNode(range.toString()));
            range.deleteContents();
            range.insertNode(anchor);
            range = range.cloneRange();
            range.setStartAfter(anchor);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        }
        top.app.currentCKE.fire('change');
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myAnchor' : 'myAnchor';