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
                    modal = top.$f('#color-variables-dialog'),
                    width = modal.innerWidth(),
                    height = modal.innerHeight()
                    left = rect.left - width / 2 + rect.width / 2,
                    topValue = rect.bottom + 10,
                    delta = 100 - ((rect.left + rect.width / 2 - left) * 100 / width);
                top.app.currentCKE = editor;
                modal.removeClass('ba-right-position ba-bottom-position').addClass('ba-top-position');
                if (button.dataset.rgba) {
                    top.setMinicolorsColor(button.dataset.rgba);
                }
                top.fontBtn = button;
                if (left+width > window.innerWidth) {
                    left = window.innerWidth - width - 5;
                    delta = 100 - ((rect.left + rect.width / 2 - left) * 100 / width)
                }
                if (topValue + height > window.innerHeight) {
                    topValue = rect.top - height - 10;
                    modal.removeClass('ba-top-position').addClass('ba-bottom-position');
                }
                modal[0].style.setProperty('--color-variables-arrow-right', delta+'%');
                modal.css({
                    left : left,
                    top : topValue
                }).modal();
                if (!('onminicolorsInput' in button)) {
                    button.onminicolorsInput = function(){
                        this.style.color = this.dataset.rgba;
                        clearTimeout(button.delay);
                        button.delay = setTimeout(function(){
                            let colorStyle = CKEDITOR.config.textColor;
                            editor.applyStyle(new CKEDITOR.style(colorStyle, {color: button.dataset.rgba}));
                            top.app.currentCKE.fire('change');
                        }, 300);
                    }
                }
            }
        });
        editor.testInterval = setInterval(function(){
            if (editor.document && editor.document.$) {
                clearInterval(editor.testInterval);
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
                top.$f('.modal-backdrop.color-variables-dialog').trigger('click');
            }
        });
    }
});

CKEDITOR.config.textColor = {
    element: 'span',
    styles: {
        color: '#(color)'
    },
    overrides: [{
        element: 'font',
        attributes: {
            color: null
        }
    }],
    childRule: function(element) {
        return !(element.is('a') || element.getElementsByTag('a').count()) || isUnstylable(element);
    }
};

CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins+',myTextColor' : 'myTextColor';