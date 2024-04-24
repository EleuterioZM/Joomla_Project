/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.lightboxEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    var modal = $g('#lightbox-settings-dialog'),
        title = gridboxLanguage[app.edit.type.replace(/-/g, '_').toUpperCase()];
    modal.find('.ba-dialog-title').text(title);
    modal.find('.modal-header > span.status-icons').remove();
    if (app.edit.preset) {
        var str = '<span class="status-icons"><i class="zmdi zmdi-roller"></i><span class="ba-tooltip ba-top">'+
            gridboxLanguage['PRESET']+'</span></span>';
        modal.find('.ba-dialog-title').after(str);
    }
    if (app.editor.document.getElementById(app.editor.app.edit).dataset.global) {
        var str = '<span class="status-icons"><i class="zmdi zmdi-globe"></i><span class="ba-tooltip ba-top">'+
            gridboxLanguage['GLOBAL_ITEM']+'</span></span>';
        modal.find('.ba-dialog-title').after(str);
    }
    app.setDefaultState('#lightbox-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#lightbox-settings-dialog .margin-settings-group');
    setDisableState('#lightbox-settings-dialog');
    app.setAccessSettings(modal);
    $g('.lightbox-overlay-backdrop-color').css('display', '');
    if (app.edit.type == 'cookies') {
        $g('.lightbox-trigger-options').hide();
    } else {
        $g('.lightbox-trigger-options').css('display', '');
    }
    if (app.edit.type == 'lightbox' || app.edit.type == 'cookies') {
        if (app.edit.lightbox.layout != 'lightbox-center') {
            $g('.lightbox-overlay-backdrop-color').css('display', 'none');
        }
        $g('#lightbox-settings-dialog .lightbox-position-select input[type="hidden"]').val(app.edit.lightbox.layout);
        value = $g('#lightbox-settings-dialog .lightbox-position-select li[data-value="'+app.edit.lightbox.layout+'"]').text();
        $g('#lightbox-settings-dialog .lightbox-position-select input[readonly]').val($g.trim(value));
        $g('#lightbox-settings-dialog .lightbox-trigger-select input[type="hidden"]').val(app.edit.trigger.type);
        value = $g('#lightbox-settings-dialog .lightbox-trigger-select li[data-value="'+app.edit.trigger.type+'"]').text();
        $g('#lightbox-settings-dialog .lightbox-trigger-select input[readonly]').val($g.trim(value));
        $g('#lightbox-settings-dialog [data-option="time"][data-group="trigger"]').val(app.edit.trigger.time);
        $g('#lightbox-settings-dialog [data-option="scroll"][data-group="trigger"]').val(app.edit.trigger.scroll);
        $g('#lightbox-settings-dialog [data-option="enable"][data-group="session"]').prop('checked', app.edit.session.enable);
        $g('#lightbox-settings-dialog [data-option="duration"][data-group="session"]').val(app.edit.session.duration);
        $g('#lightbox-settings-dialog .width-options')[0].style.display = '';
        if (app.edit.trigger.type == 'time-delay') {
            $g('#lightbox-settings-dialog .time-delay-trigger').removeAttr('style');
        } else {
            $g('#lightbox-settings-dialog .time-delay-trigger').hide();
        }
        if (app.edit.trigger.type == 'scrolling') {
            $g('#lightbox-settings-dialog .scrolling-trigger').removeAttr('style');
        } else {
            $g('#lightbox-settings-dialog .scrolling-trigger').hide();
        }
    } else {
        if (app.edit.lightbox.layout.indexOf('vertical') != -1) {
            value = 'vertical';
            $g('.overlay-section-slide-select').parent()[0].style.display = '';
            $g('.overlay-section-slide-select li[data-value="horizontal-top"]').hide();
            $g('.overlay-section-slide-select li[data-value="horizontal-bottom"]').hide();
            $g('.overlay-section-slide-select li[data-value="vertical-right"]').show();
            $g('.overlay-section-slide-select li[data-value="vertical-left"]').show();
            $g('#lightbox-settings-dialog .width-options')[0].style.display = '';
            $g('#lightbox-settings-dialog .height-options')[0].style.display = 'none';
        } else if (app.edit.lightbox.layout.indexOf('horizontal') != -1) {
            value = 'horizontal';
            $g('.overlay-section-slide-select').parent()[0].style.display = '';
            $g('.overlay-section-slide-select li[data-value="vertical-right"]').hide();
            $g('.overlay-section-slide-select li[data-value="vertical-left"]').hide();
            $g('.overlay-section-slide-select li[data-value="horizontal-top"]').show();
            $g('.overlay-section-slide-select li[data-value="horizontal-bottom"]').show();
            $g('#lightbox-settings-dialog .width-options')[0].style.display = 'none';
            $g('#lightbox-settings-dialog .height-options')[0].style.display = '';
        } else {
            value = 'lightbox';
            $g('.overlay-section-slide-select').parent().hide();
            $g('#lightbox-settings-dialog .width-options')[0].style.display = '';
            $g('#lightbox-settings-dialog .height-options')[0].style.display = 'none';
        }
        $g('#lightbox-settings-dialog .overlay-section-layout-select input[type="hidden"]').val(value);
        value = $g('#lightbox-settings-dialog .overlay-section-layout-select li[data-value="'+value+'"]').text();
        $g('#lightbox-settings-dialog .overlay-section-layout-select input[readonly]').val(value.trim());
        $g('#lightbox-settings-dialog .overlay-section-slide-select input[type="hidden"]').val(app.edit.lightbox.layout);
        value = $g('#lightbox-settings-dialog .overlay-section-slide-select li[data-value="'+app.edit.lightbox.layout+'"]').text();
        $g('#lightbox-settings-dialog .overlay-section-slide-select input[readonly]').val($g.trim(value));
        value = app.getValue('view', 'height');
        app.setLinearInput(modal.find('[data-option="height"]'), value);
    }
    value = app.edit.lightbox.background;
    updateInput($g('#lightbox-settings-dialog [data-option="background"][data-group="lightbox"]'), value);
    value = app.getValue('view', 'width');
    app.setLinearInput(modal.find('[data-option="width"]'), value);
    value = app.edit.close.color;
    updateInput($g('#lightbox-settings-dialog [data-option="color"][data-group="close"]'), value);
    $g('#lightbox-settings-dialog [data-option="text-align"]').removeClass('active');
    $g('#lightbox-settings-dialog [data-option="text-align"][data-value="'+app.edit.close['text-align']+'"]').addClass('active');
    $g('#lightbox-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#lightbox-settings-dialog').modal();
    }, 150);
}

$g('#lightbox-settings-dialog .overlay-section-layout-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        item = app.editor.document.getElementById(app.editor.app.edit);
    if (app.edit.lightbox.layout.indexOf(value) == -1) {
        if (value == 'vertical') {
            value = 'vertical-right';
            $g('.overlay-section-slide-select').parent().css('display', '');
            $g('.overlay-section-slide-select li[data-value*="horizontal-"]').hide();
            $g('.overlay-section-slide-select li[data-value*="vertical-"]').show();
            $g('#lightbox-settings-dialog .width-options').css('display', '');
            $g('#lightbox-settings-dialog .height-options').hide();
        } else if (value == 'horizontal') {
            value = 'horizontal-top';
            $g('.overlay-section-slide-select').parent().css('display', '');
            $g('.overlay-section-slide-select li[data-value*="vertical-"]').hide();
            $g('.overlay-section-slide-select li[data-value*="horizontal-"]').show();
            $g('#lightbox-settings-dialog .width-options').hide();
            $g('#lightbox-settings-dialog .height-options').css('display', '');
        } else {
            $g('.overlay-section-slide-select').parent().hide();
            $g('#lightbox-settings-dialog .width-options').css('display', '');
            $g('#lightbox-settings-dialog .height-options').hide();
        }
        app.edit.lightbox.layout = value;
        app.sectionRules();
        $g('#lightbox-settings-dialog .overlay-section-slide-select input[type="hidden"]').val(app.edit.lightbox.layout);
        value = $g('#lightbox-settings-dialog .overlay-section-slide-select li[data-value="'+app.edit.lightbox.layout+'"]').text();
        $g('#lightbox-settings-dialog .overlay-section-slide-select input[readonly]').val($g.trim(value));
    }
});

$g('#lightbox-settings-dialog .overlay-section-slide-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        item = app.editor.document.getElementById(app.editor.app.edit);
    app.edit.lightbox.layout = value;
    app.sectionRules();
});

$g('#lightbox-settings-dialog .lightbox-position-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        item = app.editor.document.getElementById(app.editor.app.edit);
    app.edit.lightbox.layout = value;
    app.sectionRules();
    if (app.edit.lightbox.layout != 'lightbox-center') {
        $g('.lightbox-overlay-backdrop-color').css('display', 'none');
        app.editor.document.body.style.width = '';
        app.editor.document.body.classList.remove('lightbox-open');
    } else {
        $g('.lightbox-overlay-backdrop-color').css('display', '');
        app.editor.document.body.classList.add('lightbox-open');
        var width = window.innerWidth - document.documentElement.clientWidth;
        app.editor.document.body.style.width = 'calc(100% - '+width+'px)';
    }
});

$g('#lightbox-settings-dialog .lightbox-trigger-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    app.edit.trigger.type = value;
    if (app.edit.trigger.type == 'time-delay') {
        $g('#lightbox-settings-dialog .time-delay-trigger').removeAttr('style');
    } else {
        $g('#lightbox-settings-dialog .time-delay-trigger').hide();
    }
    if (app.edit.trigger.type == 'scrolling') {
        $g('#lightbox-settings-dialog .scrolling-trigger').removeAttr('style');
    } else {
        $g('#lightbox-settings-dialog .scrolling-trigger').hide();
    }
});

$g('#lightbox-settings-dialog').find('[type="number"][data-group="trigger"], [type="number"][data-group="session"]').on('input', function(){
    var option = this.dataset.option,
        group = this.dataset.group;
    app.edit[group][option] = this.value;
});

$g('#lightbox-settings-dialog [data-group="session"][data-option="enable"]').on('change', function(){
    app.edit.session.enable = this.checked;
});

app.modules.lightboxEditor = true;
app.lightboxEditor();