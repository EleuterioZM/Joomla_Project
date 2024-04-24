/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var file = document.createElement('script');
file.src = JUri+'components/com_gridbox/libraries/draggable/js/draggable.js';
document.head.append(file);
file.onload = function(){
    $g('#code-editor-dialog, #ckeditor-code-editor-dialog, .draggable-modal-cp').draggable({
        handle: '.modal-header',
        change: function(item){
            let modal = item[0];
            if (modal.id == 'page-structure-dialog') {
                app.pageStructure.setPanel({
                    top: 1,
                    left: 1
                });
            } else if (modal.querySelector('.select-modal-cp-position')) {
                let rect = modal.getBoundingClientRect();
                document.body.style.setProperty('--modal-cp-left', rect.left+'px');
                document.body.style.setProperty('--modal-cp-top', rect.top+'px');
                app.cp.set({
                    left: rect.left,
                    top: rect.top
                });
                modal.style.left = '';
                modal.style.top = '';
            }
        }
    });
    app.modules.draggable = true;
}