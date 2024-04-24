/**
* @package   gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

for (let ind in shapeDividers) {
    let div = document.createElement('div'),
        viewBox = {
            "blot": "0 0 500 71",
            "blot-2": '0 0 500 70',
            "circles": '0 0 11 1.7',
            "hexagons": '0 0 800 69',
            "hexas": '0 0 800 69',
            "triangles": '0 0 400 89',
            "waves": '0 0 180 27'
        };
    div.innerHTML = shapeDividers[ind];
    if (viewBox[ind]) {
        div.querySelector('svg').setAttribute('viewBox', viewBox[ind])
    }
    $g('.shape-dividers-preset[data-value="'+ind+'"]').append(div.querySelector('svg'));
}

$g('#section-settings-dialog input[data-option="max-width"]').on('change', function(){
    app.edit.desktop.full.fullwidth = this.checked;
    app.sectionRules();
    app.editor.$g(app.selector).closest('li').trigger('mouseenter');
    if (!this.checked) {
        $g('.megamenu-width').css('display', '');
    } else {
        $g('.megamenu-width').hide();
    }
    app.setLinearInput($g('#section-settings-dialog .image-width input[data-option="width"]'), app.edit.view.width);
    app.addHistory();
});

$g('#section-settings-dialog input[data-option="fullscreen"]').on('change', function(){
    app.setValue(this.checked, 'full', 'fullscreen');
    app.sectionRules();
    app.addHistory();
});

$g('[data-group="parallax"][type="checkbox"]').on('change', function(){
    app.edit.parallax[this.dataset.option] = this.checked;
    app.editor.app.loadParallax();
    app.addHistory();
    if (this.dataset.option == 'enable') {
        if (this.checked) {
            $g('.parallax-options').css('display', '').addClass('ba-active-options');
            setTimeout(function(){
                $g('.parallax-options').removeClass('ba-active-options');
            }, 1);
        } else {
            $g('.parallax-options').css('display', 'none');
        }
    } else if (this.dataset.option == 'invert') {
        app.sectionRules();
    }
});

$g('.parallax-type-select').on('customAction', function(){
    app.edit.parallax.type = this.querySelector('input[type="hidden"]').value;
    app.editor.app.loadParallax();
    app.addHistory();
});

$g('.flipbox-select-side').on('customAction', function(){
    app.edit.side = this.querySelector('input[type="hidden"]').value;
    app.editor.setFlipboxSide(app.edit, app.edit.side);
    setSectionBackgroundOptions();
    app.editor.$g(app.selector).addClass('flipbox-animation-started');
    if (app.edit.side == 'frontside') {
        app.editor.$g(app.selector).removeClass('backside-fliped');
    } else {
        app.editor.$g(app.selector).addClass('backside-fliped');
    }
    var duration = app.getValue('animation', 'duration');
    setTimeout(function(){
        app.editor.$g(app.selector).removeClass('flipbox-animation-started');
    }, duration * 1000);
});

$g('.flipbox-effect-select').on('customAction', function(){
    var value = this.querySelector('input[type="hidden"]').value,
        item = app.editor.$g(app.selector),
        match = value.match(/\w+-flip/g),
        duration = app.getValue('animation', 'duration');
    item.addClass(match[0]);
    app.editor.$g(app.selector+' > .ba-flipbox-wrapper').removeClass(app.edit.desktop.animation.effect);
    setTimeout(function(){
        app.editor.$g(app.selector+' > .ba-flipbox-wrapper').addClass(value);
        app.edit.desktop.animation.effect = value;
        setTimeout(function(){
            item.addClass('flipbox-animation-started backside-fliped');
            setTimeout(function(){
                item.removeClass('backside-fliped');
                item.removeClass(match[0]);
                app.addHistory();
            }, duration * 1000);
            setTimeout(function(){
                item.removeClass('flipbox-animation-started');
            }, duration * 2000)
        }, 50);
    }, 50);
});

function setSectionBackgroundOptions()
{
    let object = null,
        range = null,
        states = app.getValue('overlay-states');
    value = app.getValue('background', 'effect', 'gradient');
    $g('#section-settings-dialog .background-linear-gradient').hide();
    $g('#section-settings-dialog .background-'+value+'-gradient').css('display', '');
    $g('#section-settings-dialog .gradient-options .gradient-effect-select input[type="hidden"]').val(value);
    value = $g('#section-settings-dialog .gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
    $g('#section-settings-dialog .gradient-options .gradient-effect-select input[type="text"]').val(value);
    $g('#section-settings-dialog input[data-subgroup="gradient"][data-group="background"]').each(function(){
        value = app.getValue('background', this.dataset.option, 'gradient');
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    if (!states || !states.default || !states.default.type) {
        object = app.getValue('overlay');
        states = {
            type: object.type,
            color: object.color,
            blur: object.blur ? object.blur : 10
        }
        app.edit.desktop['overlay-states'] = states;
    } else if (!('blur' in app.edit.desktop['overlay-states'].default)) {
        app.edit.desktop['overlay-states'].default.blur = app.edit.desktop.overlay.blur;
    }
    app.setDefaultState('#section-settings-dialog .overlay-settings-group', 'default');
    app.setOverlayValues('#section-settings-dialog .overlay-settings-group');
    states = app.getValue('background-states');
    if (!states || !states.default || !states.default.color) {
        object = app.getValue('background');
        states = {
            color: object.color,
            blur: object.blur ? object.blur : 10
        }
        object = app.getValue('image');
        states.image = object.image;
        app.edit.desktop['background-states'] = states
    } else if (!('blur' in app.edit.desktop['background-states'].default)) {
        app.edit.desktop['background-states'].default.blur = app.edit.desktop.background.blur;
    }
    app.setDefaultState('#section-settings-dialog .background-settings-group', 'default');
    app.setBackgroundValues('#section-settings-dialog .background-settings-group');
    $g('[data-group="parallax"]').each(function(){
        if (this.type == 'checkbox') {
            this.checked = app.edit.parallax[this.dataset.option];
        } else {
            app.setLinearInput($g(this), app.edit.parallax[this.dataset.option]);
        }
    });
    if (!app.edit.parallax.type) {
        app.edit.parallax.type = 'mousemove';
    }
    $g('#section-settings-dialog .parallax-type-select input[type="hidden"]').val(app.edit.parallax.type);
    value = $g('#section-settings-dialog .parallax-type-select li[data-value="'+app.edit.parallax.type+'"]').text();
    $g('#section-settings-dialog .parallax-type-select input[readonly]').val(value.trim());
    if (app.edit.parallax.enable) {
        $g('.parallax-options').css('display', '');
    } else {
        $g('.parallax-options').css('display', 'none');
    }
}

$g('input[data-option="effect3D"]').on('change', function(){
    app.edit.effect3D = this.checked;
    if (this.checked) {
        app.editor.$g(app.selector+' > .ba-flipbox-wrapper').addClass('flipbox-3d-effect');
    } else {
        app.editor.$g(app.selector+' > .ba-flipbox-wrapper').removeClass('flipbox-3d-effect');
    }
    app.addHistory();
});

app.sectionEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    var value = '',
        modal = $g('#section-settings-dialog'),
        flipboxEffect = $g('.flipbox-effect-select').closest('.flipbox-options');
    $g('#section-settings-dialog .active').removeClass('active');
    $g('#section-settings-dialog a[href="#section-general-options"]').parent().addClass('active');
    $g('#section-general-options').addClass('active');
    if (app.edit.type == 'flipbox') {
        value = app.getValue('animation', 'duration');
        app.setLinearInput(modal.find('input[data-option="duration"][data-group="animation"]'), value);
        app.edit.side = 'frontside';
        app.editor.setFlipboxSide(app.edit, app.edit.side);
        app.editor.$g(app.selector).addClass('flipbox-animation-started').removeClass('backside-fliped');
        setTimeout(function(){
            app.editor.$g(app.selector).removeClass('flipbox-animation-started');
        }, value * 1000);
        $g('.flipbox-select-side input[type="hidden"]').val(app.edit.side);
        $g('.flipbox-select-side input[type="text"]').val(gridboxLanguage[app.edit.side.toUpperCase()]);
        $g('.flipbox-options').css('display', '');
        flipboxEffect.find('input[type="hidden"]').val(app.edit.desktop.animation.effect);
        value = flipboxEffect.find('li[data-value="'+app.edit.desktop.animation.effect+'"]').text().trim();
        flipboxEffect.find('input[type="text"]').val(value);
        flipboxEffect.next().hide();
        value = app.getValue('view', 'height');
        app.setLinearInput($g('.flipbox-options input[data-option="height"]'), value);
        $g('#section-settings-dialog input[data-option="effect3D"]').prop('checked', app.edit.effect3D);
        $g('#section-settings-dialog .full-width').next().hide();
        $g('#section-settings-dialog input[data-option="enable"][data-group="parallax"]')
            .closest('.ba-settings-item').hide();
    } else {
        $g('.flipbox-options').hide();
        flipboxEffect.next().css('display', '');
        $g('#section-settings-dialog .full-width').next().css('display', '');
        $g('#section-settings-dialog input[data-option="enable"][data-group="parallax"]')
            .closest('.ba-settings-item').css('display', '');
    }
    if (app.edit.type == 'column') {
        if (!app.edit.link) {
            app.edit.link = {
                "link" : "",
                "target" : "_self",
                "type": ""
            }
            app.edit.embed = '';
        }
        $g('#section-settings-dialog [data-option="link"]').val(app.edit.link.link);
        $g('#section-settings-dialog .link-target-select input[type="hidden"]').val(app.edit.link.target);
        value = $g('#section-settings-dialog .link-target-select li[data-value="'+app.edit.link.target+'"]').text();
        $g('#section-settings-dialog .link-target-select input[readonly]').val(value.trim());
        $g('#section-settings-dialog .link-type-select input[type="hidden"]').val(app.edit.link.type);
        value = $g('#section-settings-dialog .link-type-select li[data-value="'+app.edit.link.type+'"]').text().trim();
        $g('#section-settings-dialog .link-type-select input[readonly]').val(value);
        $g('#section-settings-dialog .button-embed-code').val(app.edit.embed);
        $g('#section-settings-dialog .ba-column-options').css('display', '');
    } else {
        $g('#section-settings-dialog .ba-column-options').hide();
    }
    if (!app.edit.desktop.video || !app.edit.desktop.video.type) {
        app.edit.desktop.video = $g.extend(true, {}, app.edit.desktop.background.video);
    }
    if (!('blur' in app.edit.desktop.background)) {
        app.edit.desktop.background.blur = 10
    }
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
    if (!('blur' in app.edit.desktop.overlay)) {
        app.edit.desktop.overlay.blur = 10
    }
    setSectionBackgroundOptions();
    if (app.edit.type == 'section' || app.edit.type == 'row' || app.edit.type == 'column' || app.edit.type == 'flipbox') {
        setPresetsList($g('#section-settings-dialog'));
        $g('#section-settings-dialog .presets-options').css('display', '');
    } else {
        $g('#section-settings-dialog .presets-options').hide();
    }
    if (app.edit.type == 'column') {
        $g('#section-general-options .full-width').hide();
        value = app.getValue('span', 'width');
        if (!value && app.view != 'desktop' && !app.edit[app.view].span) {
            app.edit[app.view].span = {};
        }
        if (!value && app.editor.$g(app.selector).closest('header').length == 0 && app.view == 'phone-portrait') {
            value = 12;
        } else if (!value) {
            value = app.editor.document.querySelector(app.selector).parentNode.dataset.span;
        }
        app.setLinearInput($g('.mobile-column-width input[data-option="width"][data-group="span"]'), value);
        value = app.getValue('span', 'order');
        if (!value) {
            value = 1;
        }
        app.setLinearInput($g('.mobile-column-width input[data-option="order"][data-group="span"]'), value);
    } else {
        $g('#section-general-options .full-width')[0].style.display = '';
        $g('#section-settings-dialog input[data-option="max-width"]').prop('checked', app.edit.desktop.full.fullwidth);
        $g('.sticky-column-option').hide();
    }
    if (app.edit.type == 'column' || app.edit.type == 'row' || app.edit.type == 'section') {
        $g('.sticky-column-option, .on-scroll-animations-wrapper').css('display', '');
        if (!app.edit.desktop.sticky) {
            app.edit.desktop.sticky = {
                enable: false,
                offset: 0
            }
        }
        $g('.sticky-column-option').css('display', '');
        value = app.getValue('sticky', 'enable');
        let input = $g('.sticky-offset input[data-option]');
        $g('.enable-sticky input[type="checkbox"]').prop('checked', value);
        if (!value) {
            input.closest('.sticky-column-option').hide();
        }
        value = app.getValue('sticky', 'offset');
        app.setLinearInput(input, value);
    }
    if (app.edit.type == 'overlay-section') {
        if (app.edit.lightbox.layout.match('vertical-')) {
            $g('.ba-settings-item.full-width').css('display', '').next().css('display', 'none');
        } else if (app.edit.lightbox.layout.match('horizontal-')) {
            $g('.ba-settings-item.full-width').css('display', 'none').next().css('display', '');
        } else {
            $g('.ba-settings-item.full-width').css('display', '').next().css('display', '');
        }
    }
    if (app.edit.type == 'footer') {
        value = $g('#section-settings-dialog .typography-select input[type="hidden"]').val();
        app.setTypography($g('#section-settings-dialog .typography-options'), value);
    }
    if (app.edit.type == 'header') {
        if (!app.edit.layout) {
            $g('.full-group').removeAttr('style');
        } else {
            $g('.full-group').hide();
        }
        if (app.edit.layout == "sidebar-menu" && (app.view == 'desktop' || app.view == 'laptop')) {
            $g('#section-settings-dialog .header-position').hide();
            $g('#section-settings-dialog .header-sidebar-width').css('display', '');
        } else {
            $g('#section-settings-dialog .header-position').css('display', '');
            $g('#section-settings-dialog .header-sidebar-width').hide();
        }
        if (!app.edit.desktop.width) {
            app.edit.desktop.width = 250;
        }
        value = app.getValue('width');
        app.setLinearInput($g('#section-settings-dialog .header-sidebar-width input[data-option="width"]'), value);
        $g('#section-settings-dialog .header-layout-select input[type="hidden"]').val(app.edit.layout);
        value = $g('#section-settings-dialog .header-layout-select li[data-value="'+app.edit.layout+'"]').text();
        $g('#section-settings-dialog .header-layout-select input[readonly]').val(value.trim());
        value = app.getValue('position');
        $g('#section-settings-dialog .header-position-select input[type="hidden"]').val(value);
        value = $g('#section-settings-dialog .header-position-select li[data-value="'+value+'"]').text();
        $g('#section-settings-dialog .header-position-select input[readonly]').val(value.trim());
    } else {
        $g('.full-group').removeAttr('style');
    }
    if (app.edit.type == 'row') {
        value = app.getValue('view', 'gutter');
        $g('.column-gutter [data-option="gutter"]').prop('checked', value);
        $g('.column-gutter').css('display', '');
    } else {
        $g('.column-gutter').hide();
    }
    if (app.edit.type == 'mega-menu-section') {
        if (app.edit.desktop.full.fullwidth) {
            $g('.megamenu-width').hide();
        } else {
            $g('.megamenu-width').css('display', '');
        }
        app.setLinearInput($g('#section-settings-dialog .image-width input[data-option="width"]'), app.edit.view.width);
        $g('#section-settings-dialog .megamenu-position-select input[type="hidden"]').val(app.edit.view.position);
        value = $g('#section-settings-dialog .megamenu-position-select li[data-value="'+app.edit.view.position+'"]')
            .text().trim();
        $g('#section-settings-dialog .megamenu-position-select input[type="text"]').val(value);
    }
    value = app.getValue('full', 'fullscreen');
    $g('#section-settings-dialog input[data-option="fullscreen"]').prop('checked', value);
    app.setDefaultState('#section-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#section-settings-dialog .margin-settings-group');
    app.setDefaultState('#section-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#section-settings-dialog .padding-settings-group');
    app.setDefaultState('#section-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#section-settings-dialog .border-settings-group');
    setDisableState('#section-settings-dialog');
    if (typeof(app.edit.desktop.animation.delay) == 'undefined') {
        app.edit.desktop.animation.delay = 0;
    }
    if (typeof(app.edit.desktop.animation.repeat) == 'undefined') {
        app.edit.desktop.animation.repeat = false;
    }
    app.setAccessSettings(modal);
    app.setAnimationSettings('animation', modal);
    $g('#section-settings-dialog').attr('data-edit', app.edit.type);
    if (app.edit.desktop.shadow) {
        app.setDefaultState('#section-settings-dialog .shadow-settings-group', 'default');
        app.setShadowValues('#section-settings-dialog .shadow-settings-group');
    }
    $g('.shape-divider-options').hide();
    if (app.edit.type != 'column' && app.edit.type != 'flipbox') {
        $g('.shape-divider-options').css('display', '');
        if (!app.edit.desktop.shape) {
            app.edit.desktop.shape = {
                top : {
                    effect : '',
                    color : '@primary',
                    height : 500,
                    width: 300
                },                
                bottom : {
                    effect : '',
                    color : '@primary',
                    height : 500,
                    width: 300
                }
            }
        }
        if (!app.edit.desktop.shape.top.height) {
            app.edit.desktop.shape.top.height = app.edit.desktop.shape.top.value * 10;
            app.edit.desktop.shape.bottom.height = app.edit.desktop.shape.bottom.value * 10;
            app.edit.desktop.shape.top.width = app.edit.desktop.shape.bottom.width = 100;
        }
        for (let ind in app.edit.desktop.shape) {
            value = value = app.getValue('shape', 'effect', ind);
            value = $g('#shape-dividers-modal .shape-dividers-preset[data-value="'+value+'"] .ba-tooltip').text().trim();
            $g('.shape-divider-action[data-position="'+ind+'"]').val(value);
        }
    }
    if (app.edit.type == 'sticky-header') {
        $g('.sticky-header-options').css('display', '');
        value = app.getValue('offset');
        $g('.sticky-header-options input[data-option="offset"]').val(value);
        $g('.sticky-header-options input[data-option="scrollup"]').prop('checked', app.edit.scrollup);
    } else {
        $g('.sticky-header-options').hide();
    }
    if (app.edit.type == 'cookies') {
        $g('#section-settings-dialog .cookies-options').css('display', '');
        $g('#section-settings-dialog').find('.full-group').hide().next().next().hide();
        $g('.cookies-layout-select input[type="hidden"]').val(app.edit.lightbox.layout);
        value = $g('.cookies-layout-select li[data-value="'+app.edit.lightbox.layout+'"]').text().trim();
        $g('.cookies-layout-select input[type="text"]').val(value);
        value = app.getValue('view', 'width');
        var input = $g('#section-settings-dialog .cookies-options .width-options input[data-option="width"]');
        app.setLinearInput(input, value);
        setCookiesPosition();
    } else {
        $g('#section-settings-dialog .cookies-options').hide();
        $g('#section-settings-dialog').find('.full-group').css('display', '').next().next().next().css('display', '');
    }
    if (app.edit.type == 'column' || app.edit.type == 'flipbox') {
        if (!('content_align' in app.edit.desktop)) {
            app.edit.desktop.content_align = app.edit.content_align || '';
        }
        if (!('horizontal_align' in app.edit.desktop)) {
            app.edit.desktop.horizontal_align = app.edit.horizontal_align || '';
        }
        if ('horizontal_align' in app.edit) {
            delete app.edit.horizontal_align
        }
        if ('content_align' in app.edit) {
            delete app.edit.content_align
        }
        let display = ('sticky' in app.edit.desktop && app.getValue('sticky', 'enable')) ? 'none' : '',
            values = {'desktop': '', 'laptop': '-lp', 'tablet': '-md', 'tablet-portrait': '-md-pt', 'phone': '-sm', 'phone-portrait': '-sm-pt'};
        $g('#section-settings-dialog .column-content-align').find('label').each(function(){
            value = app.getValue(this.dataset.option);
            for (let ind in values) {
                value = value.replace(values[ind], '');
            }
            this.classList[this.dataset.value == value ? 'add' : 'remove']('active');
            this.style.display = this.dataset.option == 'content_align' ? display : '';
        });
        $g('#section-settings-dialog').find('.column-content-align, .column-content-direction').css('display', '');
        $g('#section-settings-dialog .column-direction-select').each(function(){
            value = ('direction' in app.edit) ? app.edit.direction : '';
            $g(this).find('input[type="hidden"]').val(value);
            value = $g(this).find('li[data-value="'+value+'"]').text().trim();
            $g(this).find('input[readonly]').val(value);
        })
    } else {
        $g('#section-settings-dialog').find('.column-content-align, .column-content-direction').hide();
    }
    setTimeout(function(){
        $g('#section-settings-dialog').modal();
    }, 150);
}

$g('#section-settings-dialog .column-direction-select').on('customAction', function(){
    let column = app.editor.$g(app.selector);
    if (app.edit.type == 'flipbox') {
        column = column.find('.ba-grid-column');
    }
    value = $g(this).find('input[type="hidden"]').val();
    column.removeClass(app.edit.direction);
    app.edit.direction = value;
    column.addClass(app.edit.direction);
    app.addHistory();
});

function setCookiesPosition()
{
    $g('.cookies-position-select input[type="hidden"]').val(app.edit.lightbox.position);
    var value = $g('.cookies-position-select li[data-value="'+app.edit.lightbox.position+'"]').text().trim();
    $g('.cookies-position-select input[type="text"]').val(value);
    $g('.cookies-position-select li').hide();
    $g('.cookies-position-select li[data-value*="'+app.edit.lightbox.layout+'"]').css('display', '');
    if (app.edit.lightbox.layout == 'lightbox') {
        $g('#section-settings-dialog .cookies-options .width-options').css('display', '');
    } else {
        $g('#section-settings-dialog .cookies-options .width-options').hide();
    }
}

$g('.cookies-layout-select').on('customAction', function(){
    app.edit.lightbox.layout = this.querySelector('input[type="hidden"]').value;
    if (app.edit.lightbox.layout == 'lightbox') {
        app.edit.lightbox.position = 'lightbox-bottom-right';
    } else {
        app.edit.lightbox.position = 'notification-bar-bottom';
    }
    setCookiesPosition();
    app.sectionRules();
    app.addHistory();
});

$g('#section-settings-dialog .enable-sticky input').on('change', function(){
    let view = app.editor.app.view,
        action = '',
        closest = app.edit.type == 'column' ? '.ba-grid-column-wrapper' : (app.edit.type == 'row' ? '.ba-row-wrapper' : '.ba-wrapper') ,
        wrapper = app.editor.$g(app.selector).closest(closest),
        name = 'ba-'+view.replace('tablet-portrait', 'tb-pt').replace('tablet', 'tb-la')
            .replace('phone-portrait', 'sm-pt').replace('phone', 'sm-la')+'-sticky-'+app.edit.type+'-',
        className = name+'enabled';
    app.setValue(this.checked, 'sticky', 'enable');
    if (view == 'desktop') {
        action = this.checked ? 'addClass' : 'removeClass';
    } else if (!this.checked && !wrapper.hasClass(name+'enabled')) {
        action = 'addClass';
        className = name+'disabled';
    } else if (!this.checked && wrapper.hasClass(name+'enabled')) {
        action = 'removeClass';
        className = name+'enabled';
    } else if (this.checked && !wrapper.hasClass(name+'disabled')) {
        action = 'addClass';
        className = name+'enabled';
    } else if (this.checked && wrapper.hasClass(name+'disabled')) {
        action = 'removeClass';
        className = name+'disabled';
    }
    wrapper[action](className);
    $g('#section-settings-dialog .sticky-offset').css('display', this.checked ? '' : 'none');
    if (app.edit.type == 'column') {
        $g('#section-settings-dialog .column-content-align label[data-option="content_align"]').css('display', !this.checked ? '' : 'none');
    }
    app.sectionRules();
    app.addHistory();
});

$g('.cookies-position-select').on('customAction', function(){
    app.edit.lightbox.position = this.querySelector('input[type="hidden"]').value;
    app.sectionRules();
    app.addHistory();
});

$g('.megamenu-position-select').on('customAction', function(){
    var value = this.querySelector('input[type="hidden"]').value,
        wrapper = app.editor.$g(app.selector).closest('.ba-wrapper');
    if (value) {
        wrapper.addClass(value);
    } else {
        wrapper.removeClass(app.edit.position);
    }
    app.edit.view.position = value;
    app.sectionRules();
    wrapper.closest('li').trigger('mouseenter');
});

$g('#section-general-options .full-group .image-width input[data-option="width"]').on('input', function(){
    app.editor.$g(app.selector).closest('li').trigger('mouseenter');
});

$g('#section-settings-dialog .shape-divider-action').on('click', function(){
    let modal = $g('#shape-dividers-modal'),
        position = this.dataset.position;
    modal.find('input[data-group="shape"]').attr('data-subgroup', position);
    modal.find('.shape-dividers-presets-wrapper').attr('data-position', position);
    value = app.getValue('shape', 'color', position);
    updateInput(modal.find('input[data-option="color"][data-group="shape"]'), value);
    value = app.getValue('shape', 'height', position);
    app.setLinearInput(modal.find('input[data-option="height"][data-group="shape"]'), value);
    value = app.getValue('shape', 'width', position);
    app.setLinearInput(modal.find('input[data-option="width"][data-group="shape"]'), value);
    openPickerModal(modal, this);
});

$g('#shape-dividers-modal .shape-dividers-preset').on('click', function(){
    let type = this.closest('.shape-dividers-presets-wrapper').dataset.position,
        text = this.querySelector('span').textContent.trim();
    app.editor.$g(app.selector+' > .ba-shape-divider').remove();
    app.setValue(this.dataset.value, 'shape', 'effect', type);
    $g('#section-settings-dialog .shape-divider-action[data-position="'+type+'"]').val(text);
    if (app.edit.preset) {
        var str = '.ba-'+app.edit.type.replace('column', 'grid-column');
        app.editor.$g(str).each(function(){
            if (app.editor.app.items[this.id] && app.editor.app.items[this.id].preset == app.edit.preset) {
                setShapeDividers(app.editor.app.items[this.id], this.id);
            }
        });
    } else {
        setShapeDividers(app.edit, app.editor.app.edit);
    }
    app.sectionRules();
    app.addHistory();
});

$g('#section-settings-dialog .header-layout-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        item = app.editor.document.querySelector('header.header');
    if (app.edit.layout) {
        item.classList.remove(app.edit.layout);
    }
    app.edit.layout = value;
    if (app.edit.layout) {
        item.classList.add(app.edit.layout);
    }
    if (!app.edit.layout) {
        $g('.full-group').removeAttr('style');
    } else {
        $g('.full-group').hide();
    }
    if (app.edit.layout == "sidebar-menu" && (app.view == 'desktop' || app.view == 'laptop')) {
        $g('#section-settings-dialog .header-position').hide();
        $g('#section-settings-dialog .header-sidebar-width').css('display', '');
    } else {
        $g('#section-settings-dialog .header-position').css('display', '');
        $g('#section-settings-dialog .header-sidebar-width').hide();
    }
    app.addHistory();
});

$g('#section-settings-dialog .header-position-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    app.setValue(value, 'position');
    app.sectionRules();
    app.addHistory();
    app.editor.$g('header.header').css('top', '');
});

app.modules.sectionEditor = true;
app.sectionEditor();