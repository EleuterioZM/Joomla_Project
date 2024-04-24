/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myJustifyLeft', {
    icons: 'justifyLeft',
    init: function(editor){
        editor.addCommand('justifyLeftComand', {
            exec: function(editor){
                var button = document.querySelector('#cke_'+editor.name+' .cke_button__myjustifyleft');
                if (!button) {
                    button = window.parent.document.querySelector('#cke_'+editor.name+' .cke_button__myjustifyleft');
                }
                button.classList.add('cke_button_on');
                button.closest('.cke_toolgroup').querySelectorAll('a[class*="cke_button__justify"]').forEach(function(el){
                    el.classList.remove('cke_button_on');
                    el.classList.add('cke_button_off');
                });
                editor.document.$.execCommand('justifyLeft', false, false);
            }
        });
        editor.justifyLeftInterval = setInterval(function(){
            if (editor.document && editor.document.$) {
                clearInterval(editor.justifyLeftInterval);
                var buttons = document.querySelectorAll('#cke_'+editor.name+' a[class*="cke_button__justify"]'),
                    button = document.querySelector('#cke_'+editor.name+' .cke_button__myjustifyleft');
                if (!button) {
                    buttons = window.parent.document.querySelectorAll('#cke_'+editor.name+' a[class*="cke_button__justify"]');
                    button = window.parent.document.querySelector('#cke_'+editor.name+' .cke_button__myjustifyleft');
                }
                buttons.forEach(function(el){
                    el.addEventListener('click', function(){
                        button.classList.remove('cke_button_on');
                    })
                });
            }
        }, 100);
        editor.ui.addButton('myJustifyLeft', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].common.alignLeft,
            command: 'justifyLeftComand',
            toolbar: 'justify',
            icon: 'justifyLeft'
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myJustifyLeft' : 'myJustifyLeft';