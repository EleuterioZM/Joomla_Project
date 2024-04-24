/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.imageEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#image-settings-dialog');
    $g('#image-settings-dialog .active').removeClass('active');
    $g('#image-settings-dialog a[href="#image-general-options"]').parent().addClass('active');
    $g('#image-general-options').addClass('active');
    app.setDefaultState('#image-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#image-layout-options .margin-settings-group');
    setDisableState('#image-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = app.edit.type == 'field-video' || app.edit.type == 'video';
    app.positioning.setValues(modal);
    modal.find('.video-item-options, .ba-image-options').css('display', '');
    modal.find('.image-fields-item-options, .video-field-item-options').hide();
    setPresetsList(modal);
    if (app.edit.type == 'image-field') {
        $g('#image-design-options .ba-image-options, #image-general-options .ba-image-options').hide();
        $g('#image-general-options').find('input[data-group="style"], input[data-option="popup"]')
            .closest('.ba-settings-group').css('display', '');
    }
    if (app.edit.type == 'image-field' || app.edit.type == 'field-video') {
        $g('#image-settings-dialog .image-fields-item-options').css('display', '');
        $g('#image-settings-dialog input[data-option="label"]').val(app.edit.label);
        $g('#image-settings-dialog input[data-option="description"][data-group="options"]').val(app.edit.options.description);
        $g('#image-settings-dialog input[data-option="required"]').prop('checked', app.edit.required);
    }
    switch (app.edit.type) {
        case 'image' :
            if (!('mouseover' in app.edit)) {
                app.edit.mouseover = true;
            }
            if (!app.edit.desktop.overlay) {
                app.edit.desktop.overlay = {
                    type: 'none',
                    color: '@overlay',
                    gradient: {
                        "effect": "linear",
                        "angle": 45,
                        "color1": "@bg-dark",
                        "position1": 25,
                        "color2": "@bg-dark-accent",
                        "position2": 75
                    }
                }
                app.edit.desktop.title = {
                    "typography" : {
                        "color" : "@title-inverse",
                        "font-family" : "@default",
                        "font-size" : 32,
                        "font-style" : "normal",
                        "font-weight" : "900",
                        "letter-spacing" : 0,
                        "line-height" : 42,
                        "text-decoration" : "none",
                        "text-align" : "center",
                        "text-transform" : "none"
                    },
                    "margin" : {
                        "bottom" : "0",
                        "top" : "0"
                    }
                };
                app.edit.desktop.description = {
                    "typography" : {
                        "color" : "@title-inverse",
                        "font-family" : "@default",
                        "font-size" : 21,
                        "font-style" : "normal",
                        "font-weight" : "300",
                        "letter-spacing" : 0,
                        "line-height" : 36,
                        "text-decoration" : "none",
                        "text-align" : "center",
                        "text-transform" : "none"
                    },
                    "margin" : {
                        "bottom" : "0",
                        "top" : "0"
                    }
                };
                app.edit.desktop.animation = {
                    "effect": "ba-fade",
                    "duration": 0.3
                }
                app.sectionRules();
            }
            if (!('embed' in app.edit)) {
                app.edit.embed = '';
            }
            if (!app.edit.tag) {
                app.edit.tag = 'h3';
            }
            var src = app.edit.image,
                array = src.split('/'),
                str = '<div class="sorting-item"><div class="sorting-image">';                
            if (!app.isExternal(src)) {
                src = JUri+src;
            }
            str += '<img src="'+src+'"></div><div class="sorting-title">'+array[array.length - 1]+
                '</div><div class="sorting-icons"><span><i class="zmdi zmdi-edit"></i></span></div></div>';
            $g('#image-settings-dialog .sorting-container').html(str);
            value = app.getValue('overlay', 'effect', 'gradient');
            $g('#image-settings-dialog .overlay-linear-gradient').hide();
            $g('#image-settings-dialog .overlay-'+value+'-gradient').css('display', '');
            $g('#image-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="hidden"]').val(value);
            value = $g('#image-settings-dialog .overlay-gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
            $g('#image-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="text"]').val(value);
            value = app.getValue('overlay', 'type');
            $g('#image-settings-dialog .overlay-color-options, .overlay-gradient-options').hide();
            $g('#image-settings-dialog .overlay-'+value+'-options').css('display', '');
            $g('#image-settings-dialog .background-overlay-select input[type="hidden"]').val(value);
            value = $g('#image-settings-dialog .background-overlay-select li[data-value="'+value+'"]').text().trim();
            $g('#image-settings-dialog .background-overlay-select input[type="text"]').val(value);
            $g('#image-settings-dialog .slideshow-style-custom-select input[type="hidden"]').val('title');
            $g('#image-settings-dialog .slideshow-style-custom-select input[readonly]').val(gridboxLanguage['TITLE']);
            $g('#image-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
            $g('#image-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
            showSlideshowDesign('title', $g('#image-settings-dialog .slideshow-style-custom-select'));
            $g('#image-settings-dialog').find('[data-group="overlay"],[data-group="animation"]').each(function(){
                if (this.dataset.subgroup) {
                    value = app.getValue(this.dataset.group, this.dataset.option, this.dataset.subgroup);
                } else if (this.dataset.group) {
                    value = app.getValue(this.dataset.group, this.dataset.option);
                } else {
                    value = app.getValue(this.dataset.option);
                }
                if (this.dataset.type == 'color') {
                    updateInput($g(this), value);
                } else if (this.type == 'hidden') {
                    this.value = value;
                    value = this.parentNode.querySelector('li[data-value="'+value+'"]').textContent.trim();
                    this.previousElementSibling.value = value;
                } else {
                    app.setLinearInput($g(this), value);
                }
            });
            $g('#image-settings-dialog [data-option="link"]').val(app.edit.link.link);
            $g('#image-settings-dialog .link-target-select input[type="hidden"]').val(app.edit.link.target);
            value = $g('#image-settings-dialog .link-target-select li[data-value="'+app.edit.link.target+'"]').text();
            $g('#image-settings-dialog .link-target-select input[readonly]').val($g.trim(value));
            if (!app.edit.link.type) {
                app.edit.link.type = '';
            }
            $g('#image-settings-dialog .link-type-select input[type="hidden"]').val(app.edit.link.type);
            value = $g('#image-settings-dialog .link-type-select li[data-value="'+app.edit.link.type+'"]').text();
            $g('#image-settings-dialog .link-type-select input[readonly]').val($g.trim(value));
            $g('#image-settings-dialog .button-embed-code').val(app.edit.embed);
            $g('#image-settings-dialog .mouseover-image-caption').prop('checked', app.edit.mouseover);
        case 'image-field':
            $g('#image-settings-dialog .video-item-options').hide();
            $g('#image-settings-dialog [data-option="align"].active').removeClass('active');
            value = app.getValue('style', 'align');
            $g('#image-settings-dialog [data-option="align"][data-value="'+value+'"]').addClass('active');
            value = app.getValue('style', 'width');
            app.setLinearInput(modal.find('.image-width input[data-option="width"]'), value);
            $g('#image-settings-dialog [data-option="popup"]').prop('checked', app.edit.popup);
            value = app.edit.lightbox.color;
            updateInput($g('#image-settings-dialog input[data-option="color"][data-group="lightbox"]'), value);
            if (app.edit.type == 'image-field') {
                $g('#image-settings-dialog .image-field-only .select-field-upload-source input[type="hidden"]').val(app.edit.options.source);
                value = app.edit.options.source == 'desktop' ? gridboxLanguage['DESKTOP'] : gridboxLanguage['MEDIA_MANAGER'];
                $g('#image-settings-dialog .image-field-only .select-field-upload-source input[type="text"]').val(value);
                $g('#image-settings-dialog .image-field-only.desktop-source-filesize input').val(app.edit.options.size);
                if (app.edit.options.source == 'desktop') {
                    $g('#image-settings-dialog .image-field-only.desktop-source-filesize').css('display', '');
                } else {
                    $g('#image-settings-dialog .image-field-only.desktop-source-filesize').hide();
                }
            }
            break;
        case 'video':
            $g('#image-settings-dialog .ba-image-options').hide();
            $g('.select-video-source input[type="hidden"]').val(app.edit.video.type);
            value = $g('.select-video-source li[data-value="'+app.edit.video.type+'"]').text().trim();
            $g('.select-video-source input[type="text"]').val(value);
            $g('.video-item-options input[data-option="id"]').val(app.edit.video.id);
            for (var ind in app.edit.video.vimeo) {
                $g('.video-item-options input[data-option="'+ind+'"][data-subgroup="vimeo"]').prop('checked', app.edit.video.vimeo[ind]);
            }
            for (var ind in app.edit.video.youtube) {
                var input = $g('.video-item-options input[data-option="'+ind+'"][data-subgroup="youtube"]')
                if (ind != 'start') {
                    input.prop('checked', app.edit.video.youtube[ind]);
                } else {
                    input.val(app.edit.video.youtube[ind]);
                }
            }
            for (var ind in app.edit.video.source) {
                var input = $g('.video-item-options input[data-option="'+ind+'"][data-subgroup="source"]');
                if (ind != 'file') {
                    input.prop('checked', app.edit.video.source[ind]);
                } else {
                    input.val(app.edit.video.source[ind]);
                }
            }
            $g('.video-vimeo-options, .video-youtube-options, .video-source-options').hide();
            if (app.edit.video.type != 'source') {
                $g('#image-settings-dialog .video-id').css('display', '');
            } else {
                $g('#image-settings-dialog .video-id').hide();
            }
            $g('.video-'+app.edit.video.type+'-options').css('display', '');
            if (!('lazyLoad' in app.edit)) {
                app.edit.lazyLoad = false;
            }
            if (!('nocookie' in app.edit)) {
                app.edit.nocookie = false;
            }
            $g('#image-settings-dialog input[data-option="lazyLoad"]').prop('checked', app.edit.lazyLoad);
            $g('#image-settings-dialog input[data-option="nocookie"]').prop('checked', app.edit.nocookie);
            break;
        case 'field-video':
            $g('#image-settings-dialog').find('.video-item-options, .ba-image-options').hide();
            $g('#image-settings-dialog .video-field-item-options').css('display', '');
            if (!('youtube' in app.edit.options)) {
                app.edit.options.youtube = true;
                app.edit.options.vimeo = true;
                app.edit.options.file = true;
            }
            $g('#image-settings-dialog .video-field-upload-from').css('display', app.edit.options.file ? '' : 'none');
            $g('#image-settings-dialog .video-field-item-options .select-field-upload-source input[type="hidden"]').val(app.edit.options.source);
            value = app.edit.options.source == 'desktop' ? gridboxLanguage['DESKTOP'] : gridboxLanguage['MEDIA_MANAGER'];
            $g('#image-settings-dialog .video-field-item-options .select-field-upload-source input[type="text"]').val(value);
            $g('#image-settings-dialog .video-field-item-options .desktop-source-filesize input').val(app.edit.options.size);
            if (app.edit.options.source == 'desktop') {
                $g('#image-settings-dialog .video-field-item-options .desktop-source-filesize').css('display', '');
            } else {
                $g('#image-settings-dialog .video-field-item-options .desktop-source-filesize').hide();
            }
            $g('#image-settings-dialog .video-field-item-options input[type="checkbox"]').each(function(){
                this.checked = app.edit.options[this.dataset.option];
            });
            break;
    }
    app.setDefaultState('#image-settings-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#image-settings-dialog .shadow-settings-group');
    app.setDefaultState('#image-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#image-settings-dialog .border-settings-group');
    $g('#image-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#image-settings-dialog').modal();
    }, 150);
}

function setItemVideo()
{
    app.editor.$g(app.selector+' .ba-video-wrapper video').removeAttr('autoplay');
    if (app.edit.video.type != 'source') {
        var iframe = app.editor.document.querySelector(app.selector+' iframe'),
            src = 'https://www.youtube'+(app.edit.nocookie ? '-nocookie' : '')+'.com/embed/',
            obj = app.edit.video[app.edit.video.type];
        if (!iframe) {
            iframe = '<iframe src="" frameborder="0" allowfullscreen></iframe>';
            app.editor.document.querySelector(app.selector+' .ba-video-wrapper').innerHTML = iframe;
            iframe = app.editor.document.querySelector(app.selector+' iframe')
        }
        if (app.edit.video.type == 'vimeo') {
            src = 'https://player.vimeo.com/video/';
        }
        src += app.edit.video.id+'?';
        for (var ind in obj) {
            src += ind+'='+String(Number(obj[ind]))+'&';
        }
        iframe.src = src.substr(0, src.length - 1);
    } else {
        var obj = app.edit.video.source,
            video = '<video><source src="'+obj.file+'" type="video/mp4"></video>';
        app.editor.document.querySelector(app.selector+' .ba-video-wrapper').innerHTML = video;
        video = app.editor.document.querySelector(app.selector+' video');
        for (var ind in obj) {
            if (ind == 'autoplay' || ind == 'file') {
                continue;
            }
            if (obj[ind]) {
                video.setAttribute(ind, '');
            } else {
                video.removeAttribute(ind);
            }
        }
        var object = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initvideo', object);
    }
    app.addHistory();
}

$g('input[data-option="file"][data-group="video"]').on('click', function(){
    fontBtn = this;
    uploadMode = 'pluginVideoSource';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
}).on('change', function(){
    app.edit.video.source.file = this.value;
    setItemVideo();
});

$g('#image-settings-dialog .video-field-item-options input[data-option="file"]').on('change', function(){
    $g('#image-settings-dialog .video-field-upload-from').css('display', this.checked ? '' : 'none');
});

$g('.select-video-source').on('customAction', function(){
    app.edit.video.type = $g(this).find('input[type="hidden"]').val();
    app.edit.video.id = '';
    app.edit.video.source.file = '';
    setItemVideo();
    $g('#image-settings-dialog').find('.video-vimeo-options, .video-youtube-options, .video-source-options').hide();
    if (app.edit.video.type != 'source') {
        $g('#image-settings-dialog .video-id').css('display', '');
    } else {
        $g('#image-settings-dialog .video-id').hide();
    }
    $g('#image-settings-dialog').find('.video-'+app.edit.video.type+'-options').css('display', '').addClass('ba-active-options');
    $g('#image-settings-dialog').find('.video-item-options').find('input[data-option="id"], input[data-option="file"]').val('');
    setTimeout(function(){
        $g('.video-item-options .ba-active-options').removeClass('ba-active-options');
    }, 1);
});

$g('.video-item-options input[type="checkbox"][data-subgroup]').on('change', function(){
    app.edit.video[this.dataset.subgroup][this.dataset.option] = this.checked;
    setItemVideo();
});

$g('.video-item-options input[data-option="nocookie"]').on('change', function(){
    app.edit.nocookie = this.checked;
    setItemVideo();
});

$g('.video-item-options input[data-option="start"]').on('input', function(){
    clearTimeout(delay);
    var $this = this;
    delay = setTimeout(function(){
        app.edit.video.youtube.start = $this.value;
        setItemVideo();
    }, 300);
});

$g('.video-item-options input[data-option="id"]').on('input', function(){
    clearTimeout(delay);
    var $this = this;
    delay = setTimeout(function(){
        app.edit.video.id = $this.value;
        setItemVideo();
    }, 300);
});

$g('#image-settings-dialog .mouseover-image-caption').on('change', function(){
    app.edit.mouseover = this.checked;
    if (this.checked) {
        app.editor.$g(app.selector+' .ba-image-wrapper').removeClass('visible-image-caption');
    } else {
        app.editor.$g(app.selector+' .ba-image-wrapper').addClass('visible-image-caption');
    }
    app.addHistory();
});

app.modules.imageEditor = true;
app.imageEditor();