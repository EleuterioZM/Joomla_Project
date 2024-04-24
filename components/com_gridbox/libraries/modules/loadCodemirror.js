/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.CodeMirror = {
    files: {
        css: [
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/material.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/ttcn.min.css'
        ],
        js: [
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/css-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/javascript-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js'
        ]
    },
    init: function(){
        this.load.css();
        this.load.js();
    },
    load: {
        css: () => {
            app.CodeMirror.files.css.forEach((href) => {
                let file = document.createElement('link');
                file.rel = 'stylesheet';
                file.href = href;
                document.head.append(file);
            });
        },
        js: () => {
            let src = app.CodeMirror.files.js.shift();
            if (src) {
                let file = document.createElement('script');
                file.src = src;
                file.onload = function(){
                    app.CodeMirror.load.js();
                }
                document.head.append(file);
            } else {
                app.CodeMirror.setup();
            }
        }
    },
    setup: function(){
        app.ckecodemirror = CodeMirror.fromTextArea(document.getElementById('ckeditor-code-editor'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2
        });
        app.ckecodemirror.on('keydown', function(cm, event){
            event.stopPropagation();
        });



        app.codeCss = CodeMirror.fromTextArea(document.getElementById('code-editor-css'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2,
            mode: "css"
        });
        app.codeCss.field = app.editor.document.getElementById('code-css-value');
        app.codeCss.setValue(app.codeCss.field.value);
        app.customCssEditor = CodeMirror.fromTextArea(document.getElementById('custom-html-edit-css'), {
            lineNumbers: true,
            theme: 'ttcn',
            lineWrapping: true,
            tabSize: 2,
            mode: "css"
        });
        app.customCssEditor.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                let value = app.customCssEditor.getValue(),
                    item = app.editor.document.querySelector('#'+app.editor.app.edit+' > style');
                item.innerHTML = value;
                app.edit.css = value;
            }, 500);
        });
        app.customCssEditor.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codeCss.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codeCss.on("inputRead", function(cm, event) {
            if (!cm.state.completionActive && event.text[0] != ':' && event.text[0] != ';'
                && event.text[0] != '{' && $g.trim(event.text[0]) != '' && event.origin != 'paste') {
                CodeMirror.commands.autocomplete(cm, null, {completeSingle: false});
            }
        });
        var style = app.editor.document.querySelector('#custom-css-editor > style');
        app.codeCss.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                app.codeCss.field.value = app.codeCss.getValue();
                style.innerHTML = app.codeCss.getValue();
            }, 500);
        });



        app.codeJs = CodeMirror.fromTextArea(document.getElementById('code-editor-javascript'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2,
            mode: "javascript"
        });
        app.codeJs.field = app.editor.document.getElementById('code-js-value');
        app.codeJs.setValue(app.codeJs.field.value);
        
        app.codeJs.on("inputRead", function(cm, event) {
            if (!cm.state.completionActive && event.text[0] != ':' && event.text[0] != ';'
                && event.text[0] != '{' && $g.trim(event.text[0]) != '' && event.origin != 'paste') {
                CodeMirror.commands.autocomplete(cm, null, {completeSingle: false});
            }
        });
        app.codeJs.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codeJs.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                app.codeJs.field.value = app.codeJs.getValue();
            }, 500);
        });



        app.customHtmlEditor = CodeMirror.fromTextArea(document.getElementById('custom-html-edit-html'), {
            lineNumbers: true,
            theme: 'ttcn',
            lineWrapping: true,
            tabSize: 2,
            mode: "htmlmixed"
        });
        
        app.customHtmlEditor.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                var value = app.customHtmlEditor.getValue(),
                    item = app.editor.document.getElementById(app.editor.app.edit);
                item = item.querySelector('div.custom-html');
                item.innerHTML = value;
                app.edit.html = value;
            }, 500);
        });
        app.customHtmlEditor.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        var headerCodemirror = CodeMirror.fromTextArea(document.querySelector('.header-code'), {
            lineNumbers: true,
            theme: 'ttcn',
            lineWrapping: true,
            tabSize: 2,
            mode: "htmlmixed"
        });
        headerCodemirror.field = document.querySelector('.header-code');
        headerCodemirror.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                headerCodemirror.field.value = headerCodemirror.getValue();
            }, 500);
        });

        var bodyCodemirror = CodeMirror.fromTextArea(document.querySelector('.body-code'), {
            lineNumbers: true,
            theme: 'ttcn',
            lineWrapping: true,
            tabSize: 2,
            mode: "htmlmixed"
        });
        bodyCodemirror.field = document.querySelector('.body-code');
        bodyCodemirror.on('change', function(from, too) {
            clearTimeout(delay);
            delay = setTimeout(function(){
                bodyCodemirror.field.value = bodyCodemirror.getValue();
            }, 500);
        });
        $g('#site-options a[href="#site-scripts-options"]').one('shown', function(){
            headerCodemirror.refresh();
            bodyCodemirror.refresh();
        })




        app.modules.loadCodemirror = true;
        if (app.actionStack.codemirror) {
            app.actionStack.codemirror();
        }
    }
}


app.CodeMirror.init()