/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('gridboxImage', {
    icons: 'image',
    init: function(editor){
        editor.addCommand('gridboxImgComand', {
            exec: function(editor){
                app.blogEditor.insertImage();
            }
        });
        editor.ui.addButton('gridboxImage', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].common.image,
            command: 'gridboxImgComand',
            toolbar: 'plugins',
            icon: 'image'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',gridboxImage' : 'gridboxImage';