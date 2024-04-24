/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.breadcrumbsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#breadcrumbs-settings-dialog').attr('data-edit', app.edit.type);
    modal.find('.active').removeClass('active');
    modal.find('a[href="#breadcrumbs-general-options"]').parent().addClass('active');
    modal.find('#breadcrumbs-general-options').addClass('active');
    setPresetsList(modal);
    modal.find('.tab-content [style*="display"]').css('display', '');
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    app.setDefaultState('#breadcrumbs-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#breadcrumbs-layout-options .margin-settings-group');
    setDisableState('#breadcrumbs-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.find('.breadcrumbs-layout-select input[type="hidden"]').val(app.edit.layout);
    value = modal.find('.breadcrumbs-layout-select li[data-value="'+app.edit.layout+'"]').text().trim();
    modal.find('.breadcrumbs-layout-select input[readonly]').val(value);
    modal.find('.breadcrumbs-home-select input[type="hidden"]').val(app.edit.home);
    value = modal.find('.breadcrumbs-home-select li[data-value="'+app.edit.home+'"]').text().trim();
    modal.find('.breadcrumbs-home-select input[readonly]').val(value);
    app.editor.app.cssRules.prepareColors(app.edit.desktop.style);
    if (!app.edit.desktop.style.colors.active) {
        app.edit.desktop.style.colors.active = $g.extend(true, {}, app.edit.desktop.style.colors.hover);
    }
    showSlideshowDesign('style', modal.find('.theme-typography-options'));
    modal.find('#breadcrumbs-design-options .ba-settings-group').css('display', '');
    modal.find('.reselect-icon').each(function(){
        this.value = app.edit[this.dataset.option].replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
    });
    let display = app.edit.home == 'icon' || app.edit.home == 'title-icon' ? '' : 'none'
    modal.find('.select-breadcrumbs-home-icon').css('display', display);
    modal.find('input[data-option="current"]').prop('checked', app.edit.current);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('#breadcrumbs-settings-dialog .reselect-icon').on('click', function(){
    uploadMode = 'setBreadcrumbsIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
}).on('input', function(){
    let value = this.dataset.value,
        search = this.dataset.option == 'home-icon' ? 'ba-home-icon' : 'ba-breadcrumbs-separator';
    app.edit[this.dataset.option] = value;
    this.value = value.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
    app.editor.$g(app.selector+' .'+search).each(function(){
        this.className = search+' '+value;
    });
});

$g('.breadcrumbs-layout-select').on('customAction', function(){
    let layout = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' .ba-breadcrumbs-wrapper').removeClass(app.edit.layout).addClass(layout);
    app.edit.layout = layout;
    if (layout == 'ba-classic-breadcrumbs') {
        app.edit.desktop.style.colors.default.color = '@title';
        app.edit.desktop.style.colors.default['background-color'] = 'rgba(255,255,255,0)';
        app.edit.desktop.style.colors.hover.color = '@primary';
        app.edit.desktop.style.colors.hover['background-color'] = 'rgba(255,255,255,0)';
    } else {
        app.edit.desktop.style.colors.default.color = '@title';
        app.edit.desktop.style.colors.default['background-color'] = '@bg-secondary';
        app.edit.desktop.style.colors.hover.color = '@title-inverse';
        app.edit.desktop.style.colors.hover['background-color'] = '@primary';
    }
    app.sectionRules();
    app.breadcrumbsEditor();
    app.addHistory();
});

$g('.breadcrumbs-home-select').on('customAction', function(){
    let home = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' li').first()
        .removeClass('ba-'+app.edit.home+'-home-item').addClass('ba-'+home+'-home-item');
    app.edit.home = home;
    let display = app.edit.home == 'icon' || app.edit.home == 'title-icon' ? '' : 'none'
    $g('#breadcrumbs-settings-dialog .select-breadcrumbs-home-icon').css('display', display);
    app.addHistory();
});

$g('#breadcrumbs-settings-dialog input[data-option="current"]').on('change', function(){
    app.edit.current = this.checked;
    app.editor.$g(app.selector+' ul')[this.checked ? 'addClass' : 'removeClass']('ba-hide-current-breadcrumbs');
    app.addHistory();
});

app.modules.breadcrumbsEditor = true;
app.breadcrumbsEditor();