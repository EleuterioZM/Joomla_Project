/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('dataTags', {
    icons: 'strike',
    init: function(editor){
        editor.addCommand('dataTagsComand', {
            exec: function(editor){
                var button = document.querySelector('#cke_'+editor.name+' .cke_button__datatags');
                if (!button) {
                    button = window.parent.document.querySelector('#cke_'+editor.name+' .cke_button__datatags');
                }
                fontBtn = button;
                showDataTagsDialog('default-value-dialog');
                if (!('ondataTagsInput' in button)) {
                    button.ondataTagsInput = function(){
                        editor.insertHtml(this.dataset.value);
                        editor.fire('change');
                    }
                }
            }
        });
        editor.ui.addButton('dataTags', {
            label: formsLanguage['DATA_TAGS'],
            command: 'dataTagsComand',
            toolbar: 'data-tags',
            icon: 'strike'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',dataTags' : 'dataTags';