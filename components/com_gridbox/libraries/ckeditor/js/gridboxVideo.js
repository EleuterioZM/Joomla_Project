/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('gridboxVideo', {
    icons: 'image',
    init: function(editor){
        editor.addCommand('gridboxVideoComand', {
            exec: function(editor){
                app.blogEditor.insertVideo();
            }
        });
        editor.ui.addButton('gridboxVideo', {
            label: "Video",
            command: 'gridboxVideoComand',
            toolbar: 'plugins',
            icon: 'video'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',gridboxVideo' : 'gridboxVideo';