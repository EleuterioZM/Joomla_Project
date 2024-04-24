/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addToCartEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#add-to-cart-settings-dialog').attr('data-edit', app.edit.type);
    modal.find('.active').removeClass('active');
    modal.find('a[href="#add-to-cart-general-options"]').parent().addClass('active');
    modal.find('#add-to-cart-general-options').addClass('active');
    setPresetsList(modal);
    modal.find('.tab-content [style*="display"]').css('display', '');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.setDefaultState('#add-to-cart-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#add-to-cart-layout-options .margin-settings-group');
    setDisableState('#add-to-cart-settings-dialog');
    modal.find('input[type="checkbox"][data-group="view"]').each(function(){
        this.checked = app.edit.desktop['view'][this.dataset.option];
    });
    modal.find('.slideshow-style-custom-select input[type="hidden"]').val('info');
    modal.find('.slideshow-style-custom-select input[readonly]').val(app._('INFO'));
    showSlideshowDesign('info', modal.find('.slideshow-style-custom-select'));
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.add-to-cart-button-label').on('input', function(){
    clearTimeout(this.delay);
    let $this = this;
    this.delay = setTimeout(function(){
        app.edit['button-label'] = $this.value.trim();
        app.editor.$g(app.selector).find('.ba-add-to-cart-button-wrapper a').text(app.edit['button-label']);
        app.addHistory();
    }, 500);
})

app.modules.addToCartEditor = true;
app.addToCartEditor();