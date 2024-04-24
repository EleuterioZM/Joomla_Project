/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.hotspotEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#hotspot-settings-dialog').attr('data-edit', app.edit.type);
    $g('#hotspot-settings-dialog .active').removeClass('active');
    $g('#hotspot-settings-dialog a[href="#hotspot-general-options"]').parent().addClass('active');
    $g('#hotspot-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    modal.find('input[data-option="icon"]').each(function(){
        this.value = app.edit.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
        this.dataset.icon = app.edit.icon;
    });
    modal.find('.hotspot-animation-select').each(function(){
        value = this.querySelector('li[data-value="'+app.edit.animation+'"]').textContent.trim();
        this.querySelector('input[type="hidden"]').value = app.edit.animation;
        this.querySelector('input[type="text"]').value = value;
    });
    modal.find('.hotspot-options-select').each(function(){
        this.querySelector('input[type="hidden"]').value = 'icon';
        this.querySelector('input[type="text"]').value = app._('ICON');
    });
    modal.find('.hotspot-position-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.position;
        this.querySelector('input[type="text"]').value = app._(app.edit.position.toUpperCase());
    });
    if (!('display' in app.edit)) {
        app.edit.display = ''
    }
    modal.find('.hotspot-display-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.display;
        value = this.querySelector('li[data-value="'+app.edit.display+'"]').textContent.trim();
        this.querySelector('input[type="text"]').value = app._(value);
    });
    value = app.getValue('popover', 'color', 'background');
    updateInput(modal.find('input[data-option="color"][data-subgroup="background"][data-group="popover"]'), value);
    value = app.getValue('popover', 'width', 'style');
    app.setLinearInput(modal.find('input[data-option="width"][data-group="popover"][data-subgroup="style"]'), value);
    showHotspotOptions('icon');
    value = app.getValue('style', 'size');
    app.setLinearInput(modal.find('input[data-option="size"][data-group="style"]'), value);
    app.setDefaultState('#hotspot-design-options .border-settings-group', 'default');
    app.setBorderValues('#hotspot-design-options .border-settings-group');
    app.setDefaultState('#hotspot-design-options .shadow-settings-group', 'default');
    app.setShadowValues('#hotspot-design-options .shadow-settings-group');
    app.setDefaultState('#hotspot-design-options .padding-settings-group', 'default');
    app.setPaddingValues('#hotspot-design-options .padding-settings-group');
    app.editor.app.cssRules.prepareColors(app.edit);
    app.setDefaultState('#hotspot-design-options .colors-settings-group', 'default');
    app.setColorsValues('#hotspot-design-options .colors-settings-group');
    app.setDefaultState('#hotspot-layout-options .border-settings-group', 'default');
    app.setBorderValues('#hotspot-layout-options .border-settings-group');
    app.setDefaultState('#hotspot-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#hotspot-layout-options .shadow-settings-group');
    app.setDefaultState('#hotspot-layout-options .padding-settings-group', 'default');
    app.setPaddingValues('#hotspot-layout-options .padding-settings-group');
    app.setDefaultState('#hotspot-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#hotspot-layout-options .margin-settings-group');
    setDisableState('#hotspot-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function showHotspotOptions(type)
{
    let modal = $g('#hotspot-settings-dialog'),
        item = app.editor.document.querySelector(app.selector);
    modal.find('.hotspot-icon-options, .hotspot-popover-options').hide();
    modal.find('.hotspot-'+type+'-options').css('display', '');
    if (type == 'popover') {
        app.editor.app.hotspot.show(item);
    } else {
        app.editor.app.hotspot.hide();
    }
}

$g('.hotspot-options-select').on('customAction', function(){
    let type = this.querySelector('input[type="hidden"]').value;
    showHotspotOptions(type);
});

$g('.hotspot-position-select').on('customAction', function(){
    app.edit.position = this.querySelector('input[type="hidden"]').value;
    app.editor.document.querySelector(app.selector+' .ba-hotspot-popover').dataset.position = app.edit.position;
    app.addHistory();
});

$g('.hotspot-display-select').on('customAction', function(){
    app.edit.display = this.querySelector('input[type="hidden"]').value;
    app.editor.document.querySelector(app.selector).dataset.display = app.edit.display;
    app.addHistory();
});

$g('#hotspot-settings-dialog input[data-option="icon"]').on('click', function(){
    uploadMode = 'reselectSocialIcon';
    fontBtn = this;
    checkIframe($g('#icon-upload-dialog'), 'icons');
}).on('change', function(){
    app.edit.icon = this.dataset.icon;
    app.editor.$g(app.selector).find('.ba-button-wrapper i').attr('class', app.edit.icon);
    app.addHistory();
});

$g('.hotspot-animation-select').on('customAction', function(){
    app.edit.animation = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector).find('.ba-button-wrapper').attr('data-animation', '');
    setTimeout(function(){
        app.editor.$g(app.selector).find('.ba-button-wrapper').attr('data-animation', app.edit.animation);
        app.addHistory();
    }, 100)
});

app.modules.hotspotEditor = true;
app.hotspotEditor();