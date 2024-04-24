/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

CKEDITOR.plugins.add('myTextColor', {
    icons: 'TextColor',
    init: function(editor){
        editor.addCommand('colorComand', {
            exec: function(editor){
                var button = document.querySelector('#cke_'+editor.name+' .cke_button__mytextcolor');
                if (!button) {
                    button = window.parent.document.querySelector('#cke_'+editor.name+' .cke_button__mytextcolor');
                }
                var rect = button.getBoundingClientRect(),
                    left = rect.left + button.offsetWidth / 2,
                    bottom = rect.bottom + (top != window ? 81 : 0);
                top.app.currentCKE = editor;
                top.$g('#color-variables-dialog').removeClass('ba-right-position').addClass('ba-top-position')
                    .find('li').css('display', '');
                if (button.dataset.rgba) {
                    top.setMinicolorsColor(button.dataset.rgba);
                }
                top.fontBtn = button;
                top.$g('#color-variables-dialog').css({
                    left : left,
                    top : bottom
                }).modal();
                if (!('onminicolorsInput' in button)) {
                    button.onminicolorsInput = function(){
                        let color = this.dataset.rgba;
                        if (color.indexOf('@') != -1) {
                            color = 'var('+this.dataset.rgba.replace('@', '--')+')';
                        }
                        this.style.color = color;
                        top.app.currentCKE.document.$.execCommand('styleWithCSS', false, true);
                        top.app.currentCKE.document.$.execCommand('foreColor', false, color);
                        top.app.currentCKE.fire( 'change' );
                    }
                }
            }
        });
        editor.textColorInterval = setInterval(function(){
            if (editor.document && editor.document.$) {
                clearInterval(editor.textColorInterval);
                editor.plugins.myTextColor.setBtnColorEvent(editor);
            }
        }, 100);
        editor.ui.addButton('myTextColor', {
            label: CKEDITOR.lang[CKEDITOR.lang.detect()].colorbutton.textColorTitle,
            command: 'colorComand',
            toolbar: 'colors',
            icon: 'TextColor'
        });
    },
    setBtnColorEvent: function(editor){
        editor.document.$.addEventListener('mouseup', function(){
            let selection = editor.window.$.getSelection(),
                btn = document.querySelector('#cke_'+editor.name+' .cke_button__mytextcolor'),
                color = '#2f3243';
            if (selection.rangeCount > 0) {
                let range = selection.getRangeAt(0),
                    start = range.startContainer.localName ? range.startContainer : range.startContainer.parentNode,
                    end = range.endContainer.localName ? range.endContainer : range.endContainer.parentNode,
                    startComp = getComputedStyle(start),
                    endComp = getComputedStyle(end);
                if (startComp.color == endComp.color) {
                    color = startComp.color;
                }
            }
            if (!btn) {
                btn = window.parent.document.querySelector('#cke_'+editor.name+' .cke_button__mytextcolor');
            }
            btn.style.color = color;
            btn.dataset.rgba = color;
            if (top == window) {
                top.$g('.modal-backdrop.color-variables-dialog').trigger('click');
            }
        });
    }
});
CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myTextColor' : 'myTextColor';