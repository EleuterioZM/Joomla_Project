/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.commentsBoxEditor = function(){
	app.selector = '#'+app.editor.app.edit;
    let modal = $g('#comments-box-settings-dialog');
    $g('#comments-box-settings-dialog .active').removeClass('active');
    $g('#comments-box-settings-dialog a[href="#comments-box-general-options"]').parent().addClass('active');
    $g('#comments-box-general-options').addClass('active');
    setPresetsList($g('#comments-box-settings-dialog'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    app.setTypography($g('#comments-box-settings-dialog .typography-options'), 'typography');
    app.setDefaultState('#comments-box-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#comments-box-settings-dialog .margin-settings-group');
	app.setDefaultState('#comments-box-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#comments-box-settings-dialog .padding-settings-group');
    $g('#comments-box-settings-dialog [data-group="view"]').each(function(){
        this.checked = app.edit.view[this.dataset.option]
    });
    if (app.edit.type == 'reviews') {
        $g('#comments-box-settings-dialog .comments-box-options').hide();
        $g('#comments-box-settings-dialog .reviews-options').css('display', '');
    } else {
        $g('#comments-box-settings-dialog .comments-box-options').css('display', '');
        $g('#comments-box-settings-dialog .reviews-options').hide();
    }
    value = app.getValue('background', 'color');
    updateInput($g('#comments-box-settings-dialog input[data-option="color"][data-group="background"]'), value);
    app.setDefaultState('#comments-box-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#comments-box-settings-dialog .border-settings-group');
    app.setDefaultState('#comments-box-settings-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#comments-box-settings-dialog .shadow-settings-group');
    setDisableState('#comments-box-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#comments-box-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#comments-box-settings-dialog').modal();
    }, 150);
}

app.modules.commentsBoxEditor = true;
app.commentsBoxEditor();