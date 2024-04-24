/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myUnlink', {
    icons: 'unlink',
    init: function(editor){
        editor.addCommand('unlinkComand', {
            exec: function(editor){
                editor.document.$.execCommand('unlink', false, false);
            }
        });
        editor.ui.addButton('myUnlink', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].link.unlink,
            command: 'unlinkComand',
            toolbar: 'links',
            icon: 'unlink'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myUnlink' : 'myUnlink';