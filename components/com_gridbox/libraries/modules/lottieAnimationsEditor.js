/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.lottieAnimationsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#lottie-animations-settings-dialog').attr('data-edit', app.edit.type);
    modal.find('.active').removeClass('active');
    modal.find('a[href="#lottie-animations-general-options"]').parent().addClass('active');
    modal.find('#lottie-animations-general-options').addClass('active');
    value = app.getValue('style', 'align');
    modal.find('[data-option="align"][data-value="'+value+'"]').addClass('active');
    value = app.getValue('style', 'width');
    app.setLinearInput(modal.find('input[data-option="width"]'), value);
    modal.find('.lottie-animations-loop').prop('checked', app.edit.loop);
    modal.find('.lottie-animations-trigger-select').each(function(){
        let text = this.querySelector('li[data-value="'+app.edit.trigger+'"]').textContent.trim();
        this.querySelector('input[type="hidden"]').value = app.edit.trigger;
        this.querySelector('input[type="text"]').value = text;
    });
    app.setLinearInput(modal.find('input[data-option="speed"]'), app.edit.speed);
    modal.find('.lottie-animations-source-select').each(function(){
        let text = this.querySelector('li[data-value="'+app.edit.source+'"]').textContent.trim();
        this.querySelector('input[type="hidden"]').value = app.edit.source;
        this.querySelector('input[type="text"]').value = text;
    });
    modal.find('.lottie-link-source-options, .lottie-file-source-options').css('display', 'none');
    modal.find('.lottie-'+app.edit.source+'-source-options').css('display', '');
    modal.find('.lottie-link-source-options input').val(app.edit.link);
    modal.find('.lottie-file-source-options input').val(app.edit.file);
    app.setDefaultState('#lottie-animations-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#lottie-animations-layout-options .margin-settings-group');
    setDisableState('#lottie-animations-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    setPresetsList(modal);
    app.setDefaultState('#lottie-animations-settings-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#lottie-animations-settings-dialog .shadow-settings-group');
    app.setDefaultState('#lottie-animations-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#lottie-animations-settings-dialog .border-settings-group');
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.lottie-animations-trigger-select').on('customAction', function(){
    app.edit.trigger = this.querySelector('input[type="hidden"]').value;
    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
    app.addHistory();
});

$g('.lottie-animations-loop').on('change', function(){
    app.edit.loop = this.checked;
    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
    app.addHistory();
});

$g('.lottie-animations-source-select').on('customAction', function(){
    app.edit.source = this.querySelector('input[type="hidden"]').value;
    $g('.lottie-link-source-options, .lottie-file-source-options').css('display', 'none');
    $g('.lottie-'+app.edit.source+'-source-options').css('display', '');
    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
    app.addHistory();
});

$g('.lottie-link-source-options input').on('input', function(){
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.edit.link = this.value;
        app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
        app.addHistory();
    }.bind(this), 300);
});

$g('.lottie-file-source-options input').on('click', function(){
    fontBtn = this;
    uploadMode = 'lottie';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
}).on('change', function(){
    app.edit.file = this.value;
    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
    app.addHistory();
});

app.lottieCallback = () => {
    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
}

app.modules.lottieAnimationsEditor = true;
app.lottieAnimationsEditor();