/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var file = document.createElement('script');
file.src = JUri+'components/com_gridbox/libraries/resizable/js/resizable.js';
document.head.append(file);
file.onload = function(){
    $g('#code-editor-dialog, #ckeditor-code-editor-dialog').resizable({
        handle : '.resizable-handle-right',
        direction : 'right-bottom',
        change : function(){
        	app.codeCss.refresh();
        	app.codeJs.refresh();
        }
    });
    $g('.draggable-modal-cp').find('.tab-content').resizable({
        handle : '.resize-handle-bottom',
        direction : 'bottom',
        change: function(direction, item){
            let modal = item[0].closest('.draggable-modal-cp');
            if (modal.querySelector('.select-modal-cp-position')) {
                let rect = item[0].getBoundingClientRect();
                document.body.style.setProperty('--modal-cp-height', rect.height+'px');
                app.cp.set({
                    height : rect.height
                });
                item[0].style.height = '';
            }
        }
    });
    $g('#page-structure-dialog').resizable({
        handle : '.ba-modal-resizer, .resize-handle-bottom',
        direction : 'bottom',
        change: function(direction){
            direction = direction == 'left' || direction == 'right' ? 'width' : 'height';
            let data = {}
            data[direction] = 1;
            app.pageStructure.setPanel(data);
        }
    });
    app.modules.resizable = true;
}