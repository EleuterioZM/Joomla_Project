/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.introPostEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#intro-post-settings-dialog');
    $g('#intro-post-settings-dialog .active').removeClass('active');
    $g('#intro-post-settings-dialog a[href="#intro-post-general-options"]').parent().addClass('active');
    $g('#intro-post-general-options').addClass('active');
    $g('#intro-post-settings-dialog').attr('data-edit', app.edit.type);
    var items = app.editor.document.querySelector(app.selector+' .intro-post-wrapper').children;
    $g('#intro-post-settings-dialog .sorting-container').empty();
    for (var i = 0; i < items.length; i++) {
        var key = items[i].className.replace('intro-post-', '').replace('-wrapper', ''),
            str = '<div class="sorting-item" data-key="'+key+'"><div class="sorting-handle">'
        str += '<i class="zmdi zmdi-apps"></i></div><div class="sorting-title">'+key+'</div></div>';
        $g('#intro-post-settings-dialog .sorting-container').append(str);
    }
    app.setDefaultState('#intro-post-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#intro-post-layout-options .margin-settings-group');
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    value = app.getValue('image', 'fullscreen');
    $g('#intro-post-settings-dialog [data-group="image"][data-option="fullscreen"]')[0].checked = value;
    setDisableState('#intro-post-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#intro-post-settings-dialog .intro-post-design-group input[type="hidden"]').val('title');
    value = $g('#intro-post-settings-dialog .intro-post-design-group li[data-value="title"]').text();
    $g('#intro-post-settings-dialog .intro-post-design-group input[readonly]').val(value.trim());
    $g('#intro-post-settings-dialog .intro-post-layout-select input[type="hidden"]').val(app.edit.layout.layout);
    value = $g('#intro-post-settings-dialog .intro-post-layout-select li[data-value="'+app.edit.layout.layout+'"]').text();
    $g('#intro-post-settings-dialog .intro-post-layout-select input[readonly]').val(value.trim());
    if (app.edit.layout.layout == 'fullscreen-post') {
        $g('.intro-show-image').hide();
    } else {
        $g('.intro-show-image').css('display', '');
    }
    if (!app.edit.desktop.image.gradient) {
        app.edit.desktop.image.type = 'color';
        app.edit.desktop.image.gradient = {
            "effect": "linear",
            "angle": 45,
            "color1": "@bg-dark",
            "position1": 25,
            "color2": "@bg-dark-accent",
            "position2": 75
        }
    }
    if (!('blur' in app.edit.desktop.image)) {
        app.edit.desktop.image.blur = 10
    }
    let object = app.getValue('image'),
        states = null;
    app.getValue('image', 'overlay-states');
    if (!object['overlay-states']) {
        states = {
            type: object.type,
            blur: object.blur,
            color: object.color
        }
        app.edit.desktop.image['overlay-states'] = states;
    }
    if (app.edit.desktop.image['overlay-states'].default && !('blur' in app.edit.desktop.image['overlay-states'].default)) {
        app.edit.desktop.image['overlay-states'].default.blur = app.edit.desktop.image.blur;
    }
    showIntroPostDesign('title');
    if (app.edit.type == 'post-intro' && !app.edit.info) {
        app.edit.info = ['author', 'date', 'category', 'comments', 'hits', 'reviews'];
    }
    $g('#intro-post-settings-dialog').find('.category-intro-view, .post-intro-view').hide()
    $g('#intro-post-settings-dialog .'+app.edit.type+'-view').css('display', '');
    $g('.intro-post-view-options input[type="checkbox"]').each(function(){
        var option = this.dataset.option,
            group = this.dataset.group;
        if (group == 'info' && app.edit.type != 'category-intro') {
            return true;
        }
        this.checked = app.edit.desktop[group][option];
    });
    setTimeout(function(){
        $g('#intro-post-settings-dialog').modal();
    }, 150);
}

$g('#intro-post-settings-dialog .intro-post-design-group .ba-custom-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    showIntroPostDesign(value);
});

function showIntroPostDesign(search)
{
    let parent = $g('#intro-post-design-options'),
        object = app.getValue(search);
    parent.children().not('.intro-post-design-group').hide();
    parent.find('.last-element-child').removeClass('last-element-child');
    parent.find('.info-hover-color').hide();
    switch (search) {
        case 'info' :
            if (app.edit.type != 'category-intro') {
                parent.find('.info-hover-color').css('display', '');
            }
        case 'title' :
            parent.find('.intro-post-margin-options').show().addClass('last-element-child')
                .find('[data-subgroup]').attr('data-group', search);
            parent.find('.intro-post-typography-color')[0].style.display = '';
            parent.find('.intro-post-typography-options').show().find('[data-subgroup="typography"]').attr('data-group', search);
            parent.find('.intro-post-typography-options .typography-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.intro-post-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'image' :
            parent.find('.intro-post-image-options').show().last().addClass('last-element-child');
            if (object.size == 'cover') {
                parent.find('.contain-size-options').hide();
            } else {
                parent.find('.contain-size-options').css('display', '');
            }
            break;
    }
    for (var ind in object) {
        if (search == 'image' && (ind == 'overlay-states' || ind == 'gradient')) {
            continue;
        } else if (typeof(object[ind]) == 'object') {
            if (ind == 'typography') {
                app.setTypography(parent.find('.intro-post-typography-options .typography-options'), search, ind);
            } else if (ind == 'margin') {
                app.setDefaultState(parent.find('.margin-settings-group'), 'default');
                app.setMarginValues(parent.find('.margin-settings-group'));
            } else {
                for (var key in object[ind]) {
                    var input = parent.find('[data-group="'+search+'"][data-option="'+key+'"][data-subgroup="'+ind+'"]');
                    if (input.attr('data-type') == 'color') {
                        updateInput(input, object[ind][key]);
                    } else if (input.attr('type') == 'number' || input.attr('type') == 'text') {
                        app.setLinearInput(input, object[ind][key]);
                    } else {
                        input.val(object[ind][key]);
                        let text = input.closest('.ba-custom-select').find('li[data-value="'+object[ind][key]+'"]').text();
                        input.closest('.ba-custom-select').find('input[readonly]').val(text.trim());
                    }
                }
            }
        } else {
            var input = parent.find('[data-group="'+search+'"][data-option="'+ind+'"]');
            if (input.attr('data-type') == 'color') {
                updateInput(input, object[ind]);
            } else if (input.attr('type') == 'number' || input.attr('type') == 'text') {
                app.setLinearInput(input, object[ind]);
            } else if (input.attr('type') == 'hidden') {
                input.val(object[ind]);
                var name = input.closest('.ba-custom-select').find('li[data-value="'+object[ind]+'"]').text();
                input.closest('.ba-custom-select').find('input[readonly]').val(name.trim());
            }
        }
    }
    app.setDefaultState(parent.find('.overlay-settings-group'), 'default');
    app.setOverlayValues(parent.find('.overlay-settings-group'));
}

$g('.intro-post-layout-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    if (value) {
        $g('.intro-show-image').hide();
        $g('.intro-show-image input[type="checkbox"]').prop('checked', true);
    } else {
        $g('.intro-show-image').css('display', '');
    }
    app.edit.layout.layout = value;
    var type = app.edit.type,
        patern = $g.extend(true, {}, presetsPatern[type]),
        is_object = null,
        theme = app.editor.app.theme,
        object = defaultElementsStyle[type];
    for (var ind in patern) {
        if (ind == 'desktop') {
            for (var key in patern[ind]) {
                is_object = typeof(app.edit[ind][key]) == 'object';
                app.edit[ind][key] = is_object ? $g.extend(true, {}, object[ind][key]) : object[ind][key];
            }
            for (var ind in app.editor.breakpoints) {
                if (app.edit[ind]) {
                    for (var key in patern.desktop) {
                        is_object = typeof(app.edit[ind][key]) == 'object';
                        if (is_object && object[ind] && object[ind][key]) {
                            app.edit[ind][key] = $g.extend(true, {}, object[ind][key]);
                        } else if (!is_object && object[ind] && object[ind][key]) {
                            app.edit[ind][key] = object[ind][key];
                        } else if (is_object) {
                            app.edit[ind][key] = {};
                        } else {
                            delete(app.edit[ind][key]);
                        }
                    }
                }
            }
        } else {
            is_object = typeof(app.edit[ind]) == 'object';
            app.edit[ind] = is_object ? $g.extend(true, {}, object[ind]) : object[ind];
        }
    }
    app.edit.desktop.view = $g.extend(true, app.edit.desktop.view, object.desktop.view);
    if (app.edit.layout.layout == 'fullscreen-post') {
        app.edit.desktop.title.typography.color = '@title-inverse';
        app.edit.desktop.info.typography.color = '@text-inverse';
        app.edit.desktop.title.typography['text-align'] = 'center';
        app.edit.desktop.info.typography['text-align'] = 'center';
        app.edit.desktop.image.color = '@overlay';
        app.edit.desktop.image.show = true;
        for (var ind in app.edit.desktop.padding) {
            app.edit.desktop.padding[ind] = 25;
        }
    }
    if (app.edit.type == 'category-intro') {
        app.edit.desktop.info.show = false;
    }
    app.editor.app.checkModule('editItem');
    app.editor.app.setNewFont = true;
    app.editor.app.fonts = {};
    app.editor.app.customFonts = {};
    app.sectionRules();
    app.addHistory();
});

$g('.intro-post-image-options .ba-custom-select').on('customAction', function(){
    var input = $g(this).find('input[type="hidden"]')[0];
    if (input.dataset.option && input.dataset.action) {
        app.setValue(input.value, 'image', input.dataset.option);
        app[input.dataset.action]();
        app.addHistory();
    }
});

app.modules.introPostEditor = true;
app.introPostEditor();