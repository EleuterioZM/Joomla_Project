/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.codemirror = function(){
    setTimeout(function(){
        $g('#code-editor-dialog').one('shown', function(){
            app.codeCss.refresh();
        }).modal();
    }, 50);
}

$g('#code-editor-dialog a[href="#code-edit-javascript"]').one('shown', function(){
    app.codeJs.refresh();
});
app.modules.codemirror = true;

if (!app.modules.loadCodemirror && !app.loading.loadCodemirror) {
    app.actionStack.codemirror = app.codemirror;
    app.checkModule('loadCodemirror');
} else {
    app.codemirror();
}