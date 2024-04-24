/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


$g('.progress-bar-label').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.editor.$g(app.selector+' .progress-bar-title').text($this.value);
        app.edit.label = $this.value;
        app.addHistory();
    }, 300);
});

$g('.progress-bar-target').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.target = $this.value;
        var obj = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initItems', obj);
        app.addHistory();
    }, 300);
});

$g('.progress-bar-duration').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.duration = $this.value * 1000;
        var obj = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initItems', obj);
        app.addHistory();
    }, 300);
});

$g('.progress-bar-effect-select').on('customAction', function(){
    app.edit.easing = this.querySelector('input[type="hidden"]').value;
    var obj = {
        data : app.edit,
        selector : app.editor.app.edit
    };
    app.editor.app.checkModule('initItems', obj);
    app.addHistory();
});

app.progressBarEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#progress-bar-settings-dialog').attr('data-edit', app.edit.type);
    setPresetsList(modal);
    app.positioning.hasWidth = app.edit.type == 'progress-bar';
    app.positioning.setValues(modal);
    $g('#progress-bar-settings-dialog .active').removeClass('active');
    $g('#progress-bar-settings-dialog a[href="#progress-bar-general-options"]').parent().addClass('active');
    $g('#progress-bar-general-options').addClass('active');
    var value = '',
        color = '';
    $g('.progress-bar-label').val(app.edit.label);
    app.setLinearInput($g('.progress-bar-target'), app.edit.target);
    if (app.edit.type == 'progress-bar') {
        value = app.getValue('view', 'height');
        app.setLinearInput(modal.find('input[data-option="height"]'), value);
        value = app.edit.desktop.display.label;
        $g('#progress-bar-settings-dialog input[data-option="label"][data-group="display"]').prop('checked', value);
        app.setDefaultState('#progress-bar-settings-dialog .shadow-settings-group', 'default');
        app.setShadowValues('#progress-bar-settings-dialog .shadow-settings-group');
        app.setDefaultState('#progress-bar-settings-dialog .border-settings-group', 'default');
        app.setBorderValues('#progress-bar-settings-dialog .border-settings-group');
        $g('.progress-pie-options').hide();
        $g('.progress-bar-options').css('display', '');
        $g('#progress-bar-design-options .progress-bar-options').prev().removeClass('last-element-child');
    } else {
        value = app.getValue('view', 'width');
        app.setLinearInput(modal.find('.ba-settings-item.progress-pie-options input[data-option="width"]'), value);
        value = app.getValue('view', 'line');
        app.setLinearInput(modal.find('.ba-settings-item.progress-pie-options input[data-option="line"]'), value)
        $g('.progress-bar-options').hide();
        $g('.progress-pie-options').css('display', '');
        $g('#progress-bar-design-options .progress-bar-options').prev().addClass('last-element-child');
    }
    value = app.edit.desktop.display.target;
    $g('#progress-bar-settings-dialog input[data-option="target"][data-group="display"]').prop('checked', value);
    $g('.progress-bar-effect-select input[type="hidden"]').val(app.edit.easing);
    value = $g('.progress-bar-effect-select li[data-value="'+app.edit.easing+'"]').text().trim();
    $g('.progress-bar-effect-select input[type="text"]').val(value);
    app.setLinearInput($g('.progress-bar-duration'), app.edit.duration / 1000);
    app.setTypography($g('#progress-bar-settings-dialog .typography-options'), 'typography');
    value = app.getValue('view', 'bar');
    updateInput($g('#progress-bar-settings-dialog input[data-option="bar"]'), value);
    value = app.getValue('view', 'background',);
    updateInput($g('#progress-bar-settings-dialog input[data-option="background"]'), value);
    app.setDefaultState('#progress-bar-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#progress-bar-settings-dialog .padding-settings-group');
    app.setDefaultState('#progress-bar-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#progress-bar-settings-dialog .margin-settings-group');
    setDisableState('#progress-bar-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

app.modules.progressBarEditor = true;
app.progressBarEditor();