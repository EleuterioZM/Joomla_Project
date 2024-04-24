/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('code', {
    icons: 'code',
    init: function(editor){
        editor.addCommand('codeComand', {
            exec: function(editor){
                let text = '',
                    selection = editor.document.$.getSelection(),
                    range = selection.getRangeAt(0),
                    startParent = range.startContainer.parentNode;
                top.app.currentCKE = editor;
                if (startParent.localName && startParent.localName == 'code') {
                    text = startParent.textContent.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                }
                if (!top.app.ckeCode.events) {
                    top.app.ckeCode.addEvent();
                }
                top.app.ckeCode.text = text;
                top.app.ckeCode.show();
            }
        });
        editor.ui.addButton('code', {
            label: top.app._('CODESNIPPET'),
            command: 'codeComand',
            toolbar: 'code',
            icon: 'link'
        });
    },
    insertCode:function(text){
        let selection = top.app.currentCKE.document.$.getSelection(),
            range = selection.getRangeAt(0),
            startParent = range.startContainer.parentNode,
            isCode = startParent.localName && startParent.localName == 'code';
        text = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        if (isCode && text) {
            startParent.innerHTML = text;
        } else if (isCode) {
            startParent.closest('pre') ? startParent.closest('pre').remove() : startParent.remove();
        } else if (text) {
            top.app.currentCKE.insertHtml('<pre><code>'+text+'</code></pre>');
        }
        top.app.currentCKE.fire('change');
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',code' : 'code';

top.app.ckeCode = {
    events: false,
    text: '',
    show: function() {
        if (!top.app.modules.loadCodemirror && !top.app.loading.loadCodemirror) {
            top.app.actionStack.codemirror = top.app.ckeCode.setValue;
            top.app.checkModule('loadCodemirror');
        } else {
            top.app.ckeCode.setValue();
        }
    },
    setValue: function(){
        top.$g('#ckeditor-code-editor-dialog').modal();
        top.app.ckecodemirror.setValue(top.app.ckeCode.text);
    },
    addEvent: function(){
        top.app.ckeCode.events = true;
        top.$g('#ckeditor-code-editor-dialog').on('hide', function(){
            let text = top.app.ckecodemirror.getValue();
            app.currentCKE.plugins.code.insertCode(text);
        });
    }
}