/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.readingProgressBarEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#reading-progress-bar-settings-dialog').attr('data-edit', app.edit.type);
    setPresetsList(modal);
    $g('#reading-progress-bar-settings-dialog .active').removeClass('active');
    $g('#reading-progress-bar-settings-dialog a[href="#reading-progress-bar-general-options"]').parent().addClass('active');
    $g('#reading-progress-bar-general-options').addClass('active');
    value = app.getValue('view', 'height');
    app.setLinearInput(modal.find('input[data-option="height"]'), value)
    value = app.getValue('view', 'bar');
    updateInput($g('#reading-progress-bar-settings-dialog input[data-option="bar"]'), value);
    value = app.getValue('view', 'background');
    updateInput($g('#reading-progress-bar-settings-dialog input[data-option="background"]'), value);
    $g('.reading-progress-bar-display-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.display;
        this.querySelector('input[type="text"]').value = app._(app.edit.display.toUpperCase()) ;
    });
    $g('.reading-progress-bar-position-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.position;
        this.querySelector('input[type="text"]').value = app._(app.edit.position.toUpperCase()) ;
    });
    setDisableState('#reading-progress-bar-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.reading-progress-bar-position-select').on('customAction', function(){
    app.edit.position = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector).attr('data-position', app.edit.position);
    app.editor.app.initReadingProgressBar(app.edit, app.editor.app.edit);
    app.addHistory();
});

$g('.reading-progress-bar-display-select').on('customAction', function(){
    app.edit.display = this.querySelector('input[type="hidden"]').value;
    app.editor.app.initReadingProgressBar(app.edit, app.editor.app.edit);
    app.addHistory();
});

app.modules.readingProgressBarEditor = true;
app.readingProgressBarEditor();