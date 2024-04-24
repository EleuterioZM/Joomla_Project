/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('resizeEditor', {
    icons: 'strike',
    init: function(editor){
        editor.addCommand('resizeEditorComand', {
            exec: function(editor){
                let button = document.querySelector('#cke_'+editor.name+' .cke_button__resizeeditor'),
                    data = editor.getData(),
                    $this = this,

                    d = window.top.document;
                if (!button) {
                    button = d.querySelector('#cke_'+editor.name+' .cke_button__resizeeditor');
                }
                window.top.app.cke.resized.setData(data);
                window.top.$g('#resized-ckeditor-dialog').modal().find('.set-resized-ckeditor-data')[0].ckeditor = editor;
            }
        });
        editor.ui.addButton('resizeEditor', {
            label: gridboxLanguage['FULLSCREEN'],
            command: 'resizeEditorComand',
            toolbar: 'resize-editor',
            icon: 'strike'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',resizeEditor' : 'resizeEditor';