/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var file = document.createElement('link');
file.rel = 'stylesheet';
file.href = JUri+'components/com_gridbox/libraries/preloader/css/animation.css';
document.head.appendChild(file);

app.preloaderEditor = function(){
    let modal = $g('#preloader-settings-dialog');
    app.selector = '#'+app.editor.app.edit;
    $g('#preloader-settings-dialog .active').removeClass('active');
    $g('#preloader-settings-dialog a[href="#preloader-general-options"]').parent().addClass('active');
    $g('#preloader-general-options').addClass('active');
    $g('#preloader-settings-dialog .preloader-type-select input[type="hidden"]').val(app.edit.layout);
    value = $g('#preloader-settings-dialog .preloader-type-select li[data-value="'+app.edit.layout+'"]').text().trim();
    $g('#preloader-settings-dialog .preloader-type-select input[type="text"]').val(value);
    $g('#preloader-settings-dialog').find('.spinner-options, .image-options').hide();
    $g('#preloader-settings-dialog .'+app.edit.layout+'-options').css('display', '');
    $g('.select-preloader-image').val(app.edit.image);
    $g('#preloader-settings-dialog input[data-group="session"]').prop('checked', app.edit.session.enable);
    value = app.getValue('width');
    app.setLinearInput(modal.find('input[data-option="width"]'), value);
    $g('.select-spinner').attr('data-value', app.edit.spinner).val(app.edit.spinner.replace('ba-', ''));
    value = app.getValue('size');
    app.setLinearInput(modal.find('input[data-option="size"]'), value)
    value = app.getValue('color');
    updateInput($g('#preloader-settings-dialog input[data-option="color"]'), value);
    value = app.getValue('background');
    updateInput($g('#preloader-settings-dialog input[data-option="background"]'), value);
    $g('#preloader-settings-dialog .preloader-animation-select input[type="hidden"]').val(app.edit.animation);
    value = $g('.preloader-animation-select li[data-value="'+app.edit.animation+'"]').text().trim();
    $g('#preloader-settings-dialog .preloader-animation-select input[type="text"]').val(value);
    app.setLinearInput(modal.find('input[data-option="delay"]'), app.edit.delay);
    setDisableState('#preloader-settings-dialog');
    app.setAccessSettings(modal);
    $g('#preloader-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function setPreloaderPoints()
{
    var str = '',
        n = 0;
    switch (app.edit.spinner) {
        case 'ba-spinner-1':
            n = 4;
            break;
        case 'ba-spinner-2':
        case 'ba-spinner-4':
        case 'ba-spinner-6':
        case 'ba-spinner-8':
            n = 2;
            break;
        case 'ba-spinner-3':
        case 'ba-spinner-7':
            n = 1;
            break;
        case 'ba-spinner-5':
            n = 5;
            break;
        case 'ba-spinner-9':
            n = 3;
            break;
        case 'ba-spinner-10':
            n = 10;
            break;
    }
    str += '<div class="preloader-point-wrapper">';
    if (app.edit.spinner == 'ba-spinner-2') {
        str += '<div>';
    }
    for (var i = 0; i < n; i++) {
        str += '<div class="preloader-point-'+(i + 1)+'"></div>'
    }
    if (app.edit.spinner == 'ba-spinner-2') {
        str += '</div><div>';
        for (var i = 0; i < n; i++) {
            str += '<div class="preloader-point-'+(i + 1)+'"></div>'
        }
        str += '<div>';
    }
    str += '</div>';

    return str;
}

$g('#preloader-settings-dialog .preloader-type-select').on('customAction', function(){
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    $g('#preloader-settings-dialog').find('.spinner-options, .image-options').hide();
    $g('#preloader-settings-dialog .'+app.edit.layout+'-options').css('display', '');
    var str = '';
    if (app.edit.layout == 'image' && app.edit.image) {
        str += '<div class="preloader-image-wrapper"><img src="'+JUri+app.edit.image+'"></div>';
    } else if (app.edit.layout == 'spinner') {
        str += setPreloaderPoints();
    } else {
        str += '<div class="preloader-image-wrapper"></div>';
    }
    app.editor.$g(app.selector+' .preloader-wrapper')
        .html(str)[app.edit.layout == 'spinner' ? 'addClass' : 'removeClass'](app.edit.spinner);
    app.addHistory();
});

$g('.select-spinner').on('click', function(){
    openPickerModal($g('#spinners-dialog'), this);
    fontBtn = this;
});

$g('.preloader-animation-select').on('customAction', function(){
    clearTimeout(this.delay);
    app.editor.$g(app.selector+' .preloader-wrapper').removeClass(app.edit.animation).removeClass('preloader-editor-out');
    app.edit.animation = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' .preloader-wrapper').addClass(app.edit.animation).addClass('preloader-editor-out');
    this.delay = setTimeout(function(){
        app.editor.$g(app.selector+' .preloader-wrapper').removeClass('preloader-editor-out');
    }, 1000);
    app.addHistory();
});

$g('#spinners-dialog .preloader-wrapper').on('click', function(){
    app.editor.$g(app.selector+' .preloader-wrapper').removeClass(app.edit.spinner);
    app.edit.spinner = this.dataset.value;
    var str = setPreloaderPoints();
    app.editor.$g(app.selector+' .preloader-wrapper').html(str).addClass(app.edit.spinner);
    $g('.select-spinner').attr('data-value', app.edit.spinner).val(app.edit.spinner.replace('ba-', ''));
    $g('#spinners-dialog').modal('hide');
    app.addHistory();
});

$g('.select-preloader-image').on('click', function(){
    fontBtn = this;
    uploadMode = 'reselectSimpleImage';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
}).on('input', function(){
    app.edit.image = this.value;
    var str = '<div class="preloader-image-wrapper"><img src="'+JUri+app.edit.image+'"></div>';
    app.editor.$g(app.selector+' .preloader-wrapper').html(str);
    app.addHistory();
})

app.modules.preloaderEditor = true;
app.preloaderEditor();