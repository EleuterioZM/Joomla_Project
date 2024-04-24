/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.themeRules = function(){
    var obj = {
        callback : 'themeRules',
        selector : 'body'
    }
    app.editor.app.listenMessage(obj);
}

app.themeEditor = function(){
    app.editor.app.edit = 'body';
    app.edit = app.editor.app.theme;
    app.selector = 'body';
    if (!app.edit.desktop.video) {
        app.edit.desktop.video = $g.extend(true, {}, app.edit.desktop.background.video);
    }
    var value = $g('#theme-settings-dialog .typography-select input[type="hidden"]').val();
    app.setTypography($g('#theme-settings-dialog .typography-options'), value);
    $g('#theme-settings-dialog [data-group="padding"]').each(function(){
        var option = this.dataset.option;
        value = app.getValue('padding', option);
        this.value = value;
    });
    $g('#theme-settings-dialog .section-access-select input[type="hidden"]').val(app.edit.access);
    var name = $g('#theme-settings-dialog .section-access-select li[data-value="'+app.edit.access+'"]').text();
    $g('#theme-settings-dialog .section-access-select input[readonly]').val($g.trim(name));
    $g('#theme-settings-dialog .class-suffix').val(app.edit.suffix);
    value = app.getValue('background', 'color');
    updateInput($g('#theme-background-options input[data-option="color"][data-group="background"]'), value);
    value = app.getValue('overlay', 'color');
    updateInput($g('#theme-background-options input[data-option="color"][data-group="overlay"]'), value);
    $g('#theme-background-options input[data-option="image"]').val(app.getValue('background', 'image', 'image'));
    $g('#theme-background-options [data-option="attachment"]').val(app.getValue('background', 'attachment', 'image'));
    name = $g('#theme-background-options .attachment li[data-value="'+app.getValue('background', 'attachment', 'image')+'"]').text();
    $g('#theme-background-options .attachment input[readonly]').val($g.trim(name));
    value = app.getValue('background', 'size', 'image');
    $g('#theme-background-options .backround-size input[type="hidden"]').val(value);
    if (value == 'contain' || value == 'initial') {
        $g('#theme-background-options .contain-size-options').show().addClass('ba-active-options');
        setTimeout(function(){
            $g('#theme-background-options .contain-size-options').removeClass('ba-active-options');
        }, 1);
    } else {
        $g('#theme-background-options .contain-size-options').hide();
    }
    name = $g('#theme-background-options .backround-size li[data-value="'+value+'"]').text();
    $g('#theme-background-options .backround-size input[readonly]').val($g.trim(name));
    value = app.getValue('background', 'position', 'image');
    $g('#theme-background-options [data-option="position"]').val(value);
    name = $g('#theme-background-options .backround-position li[data-value="'+value+'"]').text();
    $g('#theme-background-options .backround-position input[readonly]').val($g.trim(name));
    value = app.getValue('background', 'repeat', 'image');
    $g('#theme-background-options [data-option="repeat"]').val(value);
    name = $g('#theme-background-options .backround-repeat li[data-value="'+value+'"]').text();
    $g('#theme-background-options .backround-repeat input[readonly]').val($g.trim(name));
    $g('#theme-settings-dialog .video-select [data-option="video-type"]').val(app.edit.desktop.background.video.type);
    $g('#theme-settings-dialog .video-select').trigger('customAction');
    $g('#theme-background-options [data-option="id"]').val(app.edit.desktop.background.video.id);
    $g('#theme-background-options [data-option="start"]').val(app.edit.desktop.background.video.start);
    if (app.edit.desktop.background.video.mute == 1) {
        $g('#theme-background-options [data-option="mute"]').prop('checked', true);
    }
    $g('#theme-settings-dialog .video-quality [data-option="quality"]').val(app.edit.desktop.background.video.quality);
    name = $g('#theme-settings-dialog .video-quality li[data-value="'+app.edit.desktop.background.video.quality+'"]').text();
    $g('#theme-settings-dialog .video-quality [readonly]').val($g.trim(name));
    name = app.getValue('background', 'type');
    if(app.view != 'desktop' && name == 'video') {
        name = 'color';
    }
    $g('#theme-settings-dialog .background-select input[type="hidden"]').val(name);
    name = $g('#theme-settings-dialog .background-select li[data-value="'+name+'"]').text();
    $g('#theme-settings-dialog .background-select input[readonly]').val($g.trim(name));
    backgroundSelectAction($g('#theme-settings-dialog .background-select'), 'themeRules');
    if (!app.edit.desktop.background.gradient) {
        app.edit.desktop.background.gradient = {
            "effect": "linear",
            "angle": 45,
            "color1": "@bg-dark",
            "position1": 25,
            "color2": "@bg-dark-accent",
            "position2": 75
        }
        app.edit.desktop.overlay.type = 'color';
        app.edit.desktop.overlay.gradient = {
            "effect": "linear",
            "angle": 45,
            "color1": "@bg-dark",
            "position1": 25,
            "color2": "@bg-dark-accent",
            "position2": 75
        }
    }
    value = app.getValue('background', 'effect', 'gradient');
    $g('#theme-settings-dialog .background-linear-gradient').hide();
    $g('#theme-settings-dialog .background-'+value+'-gradient').css('display', '');
    $g('#theme-settings-dialog .gradient-options .gradient-effect-select input[type="hidden"]').val(value);
    value = $g('#theme-settings-dialog .gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
    $g('#theme-settings-dialog .gradient-options .gradient-effect-select input[type="text"]').val(value);
    value = app.getValue('overlay', 'effect', 'gradient');
    $g('#theme-settings-dialog .overlay-linear-gradient').hide();
    $g('#theme-settings-dialog .overlay-'+value+'-gradient').css('display', '');
    $g('#theme-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="hidden"]').val(value);
    value = $g('#theme-settings-dialog .overlay-gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
    $g('#theme-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="text"]').val(value);
    $g('#theme-settings-dialog input[data-subgroup="gradient"][data-group="background"]').each(function(){
        value = app.getValue('background', this.dataset.option, 'gradient');
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    value = app.getValue('overlay', 'type');
    $g('#theme-settings-dialog .overlay-color-options, .overlay-gradient-options').hide();
    $g('#theme-settings-dialog .overlay-'+value+'-options').css('display', '');
    $g('#theme-settings-dialog .background-overlay-select input[type="hidden"]').val(value);
    value = $g('#theme-settings-dialog .background-overlay-select li[data-value="'+value+'"]').text().trim();
    $g('#theme-settings-dialog .background-overlay-select input[type="text"]').val(value);
    $g('#theme-settings-dialog input[data-subgroup="gradient"][data-group="overlay"]').each(function(){
        value = app.getValue('overlay', this.dataset.option, 'gradient');
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    $g('#theme-settings-dialog .ba-settings-item.colors-item[data-variable]').each(function(){
        $g(this).find('.color-varibles-color-swatch').css('background-color', app.edit.colorVariables[this.dataset.variable].color);
    });
    setTimeout(function(){
        $g('#theme-settings-dialog').modal();
    }, 150);
}

$g('#theme-colors-options .colors-wrapper').on('click', '.colors-item[data-variable]', function(){
    let modal = $g('#color-variables-dialog');
    setMinicolorsColor(this.dataset.variable);
    fontBtn = this;
    modal.find('.nav-tabs li:last').hide();
    openPickerModal(modal, this.querySelector('.color-varibles-color-swatch'));
}).on('minicolorsInput', '.colors-item[data-variable]', function(){
    app.editor.app.theme.colorVariables[this.dataset.variable].color = this.dataset.rgba;
    app.editor.document.body.parentNode.style.setProperty(this.dataset.variable.replace('@', '--'), this.dataset.rgba);
    if (typeof(CKEDITOR) != 'undefined') {
        let $this = this;
        $g('#blog-post-editor-fields-options .cke_contents iframe').each(function(){
            this.contentWindow.document.body.parentNode.style.setProperty($this.dataset.variable.replace('@', '--'), $this.dataset.rgba);
        });
        CKEDITOR.config.contentsCss = [app.editor.getCKECSSrulesString()];
    }
    $g(this).find('.color-varibles-color-swatch').css('background-color', this.dataset.rgba);
    $g('#theme-settings-dialog .minicolors-input[data-rgba="'+this.dataset.variable+'"]')
        .next().find('.minicolors-swatch-color').css('background-color', this.dataset.rgba);
    clearTimeout(this.minicolorsDelay);
    this.minicolorsDelay = setTimeout(function(){
        app.editor.$g('.ba-item-progress-pie').each(function(){
            var obj = app.editor.app.items[this.id],
                canvas = app.editor.$g('#'+this.id).find('canvas')[0],
                context = canvas.getContext('2d');
            canvas.width = obj.desktop.width;
            canvas.height = canvas.width;
            context.lineCap = 'round';
            app.editor.drawPieLine(obj.target * 3.6, canvas, context, this);
        });
    }, 300);
});

app.modules.themeEditor = true;
app.themeEditor();
app.loading.themeEditor = false;