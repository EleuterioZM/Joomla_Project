/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('gridboxPlugins', {
    icons: 'plugins',
    init: function(editor){
        editor.addCommand('gridboxPluginsComand', {
            exec: function(editor){
                app.blogEditor.insertPlugins();
            }
        });
        editor.ui.addButton('gridboxPlugins', {
            label: "Plugins",
            command: 'gridboxPluginsComand',
            toolbar: 'plugins',
            icon: 'plugins'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',gridboxPlugins' : 'gridboxPlugins';