/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.loginEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#login-settings-dialog').attr('data-edit', app.edit.type);
    modal.find('.active').removeClass('active');
    modal.find('a[href="#login-general-options"]').parent().addClass('active');
    modal.find('#login-general-options').addClass('active');
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    modal.find('#login-general-options input[data-group="options"]').each(function(){
        this[this.type == 'checkbox' ? 'checked' : 'value'] = app.edit.options[this.dataset.option]
    });
    modal.find('input[data-group="facebook"], input[data-group="google"]').each(function(){
        this.checked = app.edit[this.dataset.group][this.dataset.option];
    });
    $g('#login-general-options .login-acceptance-option').css('display', app.edit.options.registration ? '' : 'none');
    $g('#login-general-options .login-redirect-option').css('display', app.edit.options.login || app.edit.options.registration ? '' : 'none');
    $g('#login-general-options .login-sub-options').css('display', app.edit.options.login ? '' : 'none');
    modal.find('input[data-group="acceptance"]').prop('checked', app.edit.acceptance.enable);
    modal.find('.login-select-recaptcha input[type="hidden"]').val(app.edit.options.recaptcha);
    value = modal.find('.login-select-recaptcha li[data-value="'+app.edit.options.recaptcha+'"]').text().trim();
    modal.find('.login-select-recaptcha input[readonly]').val(value);
    app.setDefaultState(modal.find('#login-layout-options .margin-settings-group'), 'default');
    app.setMarginValues(modal.find('#login-layout-options .margin-settings-group'));
    app.setDefaultState(modal.find('#login-layout-options .padding-settings-group'), 'default');
    app.setPaddingValues(modal.find('#login-layout-options .padding-settings-group'));
    app.setDefaultState(modal.find('#login-layout-options .border-settings-group'), 'default');
    app.setBorderValues(modal.find('#login-layout-options .border-settings-group'));
    app.setDefaultState(modal.find('#login-layout-options .border-settings-group'), 'default');
    app.setShadowValues(modal.find('#login-layout-options .shadow-settings-group'));
    app.setDefaultState(modal.find('.slideshow-design-group.background-settings-group'), 'default');
    app.setFeatureBackgroundValues(modal.find('.slideshow-design-group.background-settings-group'));
    setDisableState('#login-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.find('.slideshow-style-custom-select input[type="hidden"]').val('headline');
    modal.find('.slideshow-style-custom-select input[readonly]').val(app._('HEADLINE'));
    showSlideshowDesign('headline', modal.find('.slideshow-style-custom-select'));
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('#login-general-options input[data-group="options"]').on('input', function(){
    app.edit.options[this.dataset.option] = this[this.type == 'checkbox' ? 'checked' : 'value'];
    $g('#login-general-options .login-sub-options').css('display', app.edit.options.login ? '' : 'none');
    $g('#login-general-options .login-redirect-option').css('display', app.edit.options.login || app.edit.options.registration ? '' : 'none');
    $g('#login-general-options .login-acceptance-option').css('display', app.edit.options.registration ? '' : 'none');
    app.sectionRules();
    app.addHistory();
});

$g('#login-general-options input[data-group="facebook"]').on('input', function(){
    app.edit.facebook[this.dataset.option] = this.checked;
    app.sectionRules();
    app.addHistory();
});

$g('#login-general-options input[data-group="google"]').on('input', function(){
    app.edit.google[this.dataset.option] = this.checked;
    app.sectionRules();
    app.addHistory();
});

$g('#login-general-options .login-select-recaptcha').on('customAction', function(){
    app.edit.options.recaptcha = this.querySelector('input[type="hidden"]').value;
    app.addHistory();
});

$g('#login-general-options .edit-login-acceptance').on('click', () => {
    let modal = $g('#login-acceptance-edit-modal').modal();
    modal.find('#apply-login-acceptance-html').removeClass('disabled');
    modal.find('.login-acceptance-html').val(app.edit.acceptance.html);
});

$g('#login-acceptance-edit-modal .login-acceptance-html').on('input', function(){
    $g('#apply-login-acceptance-html')[this.value.trim() ? 'removeClass' : 'addClass']('disabled');
});

$g('#apply-login-acceptance-html').on('click', function(){
    if (this.classList.contains('disabled')) {
        return;
    }
    let modal = $g('#login-acceptance-edit-modal').modal('hide');
    app.edit.acceptance.html = modal.find('.login-acceptance-html').val().trim();
    app.editor.$g(app.selector).find('.ba-login-acceptance').html(app.edit.acceptance.html);
    app.addHistory();
});

app.modules.loginEditor = true;
app.loginEditor();