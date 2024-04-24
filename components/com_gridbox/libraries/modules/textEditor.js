/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.textEditor = function() {
    let modal = $g('#text-editor-dialog');
    app.selector = '#'+app.editor.app.edit;
    $g('#text-editor-dialog .active').removeClass('active');
    $g('#text-editor-dialog a[href="#text-editor-general-options"]').parent().addClass('active');
    $g('#text-editor-general-options').addClass('active');
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    let array = ['h1' ,'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'links'];
    if (app.edit.global) {
        delete(app.edit.global);
        array.forEach(function(key){
            delete(app.edit.desktop[key]);
            for (var ind in app.editor.breakpoints) {
                delete(app.edit[ind][key]);
            }
        });
    }
    if (!app.edit.desktop.p) {
        array.forEach(function(key){
            if (key != 'links') {
                app.edit.desktop[key] = {
                    "font-family" : "@default",
                    "font-style" : "@default"
                };
                for (var ind in app.editor.breakpoints) {
                    app.edit[ind][key] = {};
                }
            }
        });
    }
    if (!app.edit.desktop.p.gradient) {
        array.forEach((key) => {
            if (key != 'links') {
                app.edit.desktop[key].type = '';
                app.edit.desktop[key].gradient = {
                    "effect": "linear",
                    "angle": 45,
                    "color1": "@bg-dark",
                    "position1": 25,
                    "color2": "@bg-dark-accent",
                    "position2": 75
                }
            }
        });
    }
    if (!app.edit.desktop.links) {
        app.edit.desktop.links = {};
    }
    value = app.editor.document.querySelector(app.selector+' > .content-text > *');
    if (value) {
        value = value.localName;
        if (array.indexOf(value) == -1) {
            value = 'h1';
        }
    } else {
        value = 'h1';
    }
    $g('#text-editor-dialog .typography-select input[type="hidden"]').val(value);
    $g('#text-editor-dialog .typography-select input[type="text"]').val(value.toUpperCase().replace('P', 'Paragraph'));
    $g('#text-editor-dialog .typography-options .ba-settings-item').css('display', '').last().hide().prev().hide();
    app.setTypography($g('#text-editor-dialog .typography-options'), value);
    app.setDefaultState('#text-editor-dialog .margin-settings-group', 'default');
    app.setMarginValues('#text-editor-dialog .margin-settings-group');
    setDisableState('#text-editor-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    setTimeout(function(){
        if ('WFEditor' in window || CKE.loaded) {
            app.setTextContent();
            modal.modal();
        }
    }, 150);
}

app.setTextContent = function(){
    var item = app.editor.document.querySelector(app.selector+' .content-text');
    app.setContent(item.innerHTML);
    app.editor.$g(item).trigger('input');
}

app.getTextContent = function(textarea, content){
    if (textarea) {
        app.editor.document.querySelector(app.selector+' .content-text').innerHTML = textarea.value;
    } else {
        app.editor.document.querySelector(app.selector+' .content-text').innerHTML = app.getContent(content);
    }
    app.editor.$g(app.selector+' .content-text').trigger('input');
    clearTimeout(this.gridboxTextDelay);
    this.gridboxTextDelay = setTimeout(function(){
        app.addHistory();
    }, 300);
}

if ('WFEditor' in window) {
    app.setContent = function(data){
        WFEditor.setContent('editor', data);
    }
    app.getContent = function(content){
        var data = typeof content == 'string' ? content : WFEditor.getContent('editor');

        return data;
    }
    app.jce = {
        get: (key) => {
            let editor = WFEditor['getEditor' in WFEditor ? 'getEditor' : '_getEditor']('editor');

            return editor.editor || editor;
        },
        setEvents: (key, callback) => {
            let editor = app.jce.get(key);
            editor.onKeyUp.add(callback);
            editor.onChange.add(callback);
        }
    }
    app.jce.setEvents('editor', function(editor, data){
        let content = data && ('content' in data) ? data.content : null;
        app.getTextContent(null, content);
    });
    $g('#editor').on('keyup', function(){
        app.getTextContent();
    });
    $g('.ba-editor-wrapper').addClass('jce-editor-enabled');
} else {
    app.setContent = function(data){
        CKE.setData(data);
    }
    app.getContent = function(){
        var data = CKE.getData();

        return data;
    }
    CKE.on('change', function(){
        app.getTextContent();
    });
    CKE.on('selectionChange', function(){
        CKE.plugins.myTextColor.setBtnColorEvent(CKE);
    });
    $g('#cke_1_contents').on('keyup', 'textarea', function(){
        app.getTextContent(this);
    });
}

$g('#text-editor-dialog .resize-text-editor').on('mousedown', function(event){
    event.preventDefault();
    event.stopPropagation();
    var $this = $g(this),
        modal = $g('#text-editor-dialog'),
        offset = modal[0].getBoundingClientRect();
        left = offset.left,
        right = document.documentElement.clientWidth - offset.right;
    if (left + 970 > document.documentElement.clientWidth) {
        left = 'auto';
    } else {
        right = 'auto';
    }
    if ($this.hasClass('zmdi-fullscreen')) {
        $this.removeClass('zmdi-fullscreen').addClass('zmdi-fullscreen-exit');
        modal.css({
            left : left,
            right : right,
            position: 'fixed',
            'margin-left': 0
        });
        if (left == 'auto' && offset.right - 970 < 0) {
            modal.animate({
                'right': document.documentElement.clientWidth - 995
            }, 300);
        }
        modal.addClass('text-editor-resized').addClass('text-editor-animation');
        setTimeout(function(){
            modal.removeClass('text-editor-animation');
        }, 300);
    } else {
        $this.removeClass('zmdi-fullscreen-exit').addClass('zmdi-fullscreen');
        modal.removeClass('text-editor-resized').addClass('text-editor-animation');
        setTimeout(function(){
            modal.removeClass('text-editor-animation');
            offset = modal[0].getBoundingClientRect();
            modal.css({
                left : offset.left,
                right : '',
            });
        }, 300);
    }
});

$g('#text-editor-dialog .ba-editor-wrapper').on('keydown', 'textarea', function(event){
    event.stopPropagation();
});

app.modules.textEditor = true;
app.textEditor();