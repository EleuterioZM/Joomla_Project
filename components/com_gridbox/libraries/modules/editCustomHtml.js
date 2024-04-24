/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.editCustomHtml = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#custom-html-dialog');
    $g('#custom-html-dialog .show-general-cell').removeClass('show-general-cell').addClass('hide-general-cell');
    $g('#custom-html-dialog .active').removeClass('active');
    $g('#custom-html-dialog li').first().addClass('active');
    $g('#custom-edit-html').addClass('active');
    app.setDefaultState('#custom-html-dialog .margin-settings-group', 'default');
    app.setMarginValues('#custom-html-dialog .margin-settings-group');
    setDisableState('#custom-html-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    app.customHtmlEditor.setValue(app.edit.html);
    app.customCssEditor.setValue(app.edit.css);
    setTimeout(function(){
        modal.one('shown', function(){
            app.customHtmlEditor.refresh();
        }).modal();
    }, 150);
}

$g('#custom-html-dialog a').on('click', function(){
    delay = setInterval(function(){
        app.customCssEditor.refresh();
        app.customHtmlEditor.refresh();
    }, 50);
}).on('shown', function(){
    clearInterval(delay);
    app.customCssEditor.refresh();
    app.customHtmlEditor.refresh();
});

app.modules.editCustomHtml = true;

if (!app.modules.loadCodemirror && !app.loading.loadCodemirror) {
    app.actionStack.codemirror = app.editCustomHtml;
    app.checkModule('loadCodemirror');
} else {
    app.editCustomHtml();
}