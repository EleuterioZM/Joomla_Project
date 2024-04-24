/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.cartEditor = function(){
    let modal = $g('#cart-settings-dialog');
    app.selector = '#'+app.editor.app.edit;
    modal.find('.active').removeClass('active');
    modal.find('a[href="#cart-general-options"]').parent().addClass('active');
    modal.find('#cart-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    modal.find('.ba-wishlist-options, .ba-cart-options').hide();
    if (app.edit.type == 'cart') {
        modal.find('.ba-cart-options').css('display', '');
        value = app.getValue('view', 'subtotal');
        modal.find('[data-group="view"][data-option="subtotal"]').prop('checked', value);
        if (!('empty' in app.edit.desktop.view)) {
            app.edit.desktop.view.empty = false;
        }
        value = app.getValue('view', 'empty');
        modal.find('[data-group="view"][data-option="empty"]').prop('checked', value);
        modal.find('.select-cart-layout input[type="hidden"]').val(app.edit.layout);
        modal.find('.select-cart-layout input[type="text"]').val(app._(app.edit.layout.toUpperCase()));
    } else {
        modal.find('.ba-wishlist-options').css('display', '');
        modal.find('.ba-wishlist-title').val(app.edit.title);
    }
    app.setDefaultState(modal.find('.padding-settings-group'), 'default');
    app.setPaddingValues(modal.find('.padding-settings-group'));
    app.editor.app.cssRules.prepareColors(app.edit);
    app.setDefaultState(modal.find('.colors-settings-group'), 'default');
    app.setColorsValues(modal.find('.colors-settings-group'));
    app.setDefaultState(modal.find('.shadow-settings-group'), 'default');
    app.setShadowValues(modal.find('.shadow-settings-group'));
    value = app.getValue('icons', 'size');
    app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
    value = app.edit.icon.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
    modal.find('[data-option="icon"][data-group="icon"]').val(value);
    modal.find('.button-icon-position input[type="hidden"]').val(app.edit.icon.position);
    value = modal.find('.button-icon-position li[data-value="'+app.edit.icon.position+'"]').text();
    modal.find('.button-icon-position input[readonly]').val(value.trim());
    app.setTypography(modal.find('.typography-options'), 'typography');
    app.setDefaultState('#cart-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#cart-layout-options .margin-settings-group');
    app.setDefaultState('#cart-layout-options .border-settings-group', 'default');
    app.setBorderValues('#cart-layout-options .border-settings-group');
    setDisableState('#cart-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.set-cart-empty-option').on('change', function(){
    app.edit.desktop.view.empty = this.checked;
    app.editor.$g(app.selector+' .ba-button-wrapper')[this.checked ? 'addClass' : 'removeClass']('hide-empty-cart');
    app.addHistory();
})

$g('.select-cart-layout').on('customAction', function(){
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    app.editor.$g('.ba-store-cart-backdrop').attr('data-layout', app.edit.layout);
    app.addHistory();
});

$g('#cart-settings-dialog input[data-option="icon"][data-group="icon"]').on('click', function(){
    uploadMode = 'addCartIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
}).on('change', function(){
    app.edit.icon.icon = this.dataset.value;
    app.editor.$g(app.selector+' .ba-button-wrapper a').each(function(){
        let i = this.querySelector('i');
        if (app.edit.icon.icon && !i) {
            i = document.createElement('i');
            this.append(i);
        }
        if (app.edit.icon.icon) {
            i.className = app.edit.icon.icon;
        } else if (i) {
            i.remove()
        }
    });
    app.addHistory();
});

$g('.ba-wishlist-title').on('input', function(){
    app.edit.title = this.value;
    app.editor.$g(app.selector+' .ba-wishlist-title').text(this.value);
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 500);
})

app.modules.cartEditor = true;
app.cartEditor();