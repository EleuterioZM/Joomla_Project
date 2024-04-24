/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.currencySwitcherEditor = function(){
	app.selector = '#'+app.editor.app.edit;
    let modal = $g('#currency-switcher-settings-dialog');
    modal.find('.active').removeClass('active');
    modal.find('a[href="#currency-switcher-general-options"]').parent().addClass('active');
    $g('#currency-switcher-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    modal.find('.currency-switcher-layout-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.layout;
        value = this.querySelector('li[data-value="'+app.edit.layout+'"]').textContent.trim();
        this.querySelector('input[type="text"]').value = value;
    });
    modal.find('#currency-switcher-design-options [class*="-layout-options"]').hide();
    modal.find('#currency-switcher-design-options .'+app.edit.layout+'-options').css('display', '');
    modal.find('.slideshow-style-custom-select input[type="hidden"]').val('switcher');
    modal.find('.slideshow-style-custom-select input[readonly]').val(app._('CURRENCY_SWITCHER'));
    showSlideshowDesign('switcher', modal.find('.slideshow-style-custom-select'));
    app.setDefaultState('#currency-switcher-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#currency-switcher-settings-dialog .margin-settings-group');
    setDisableState('#currency-switcher-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.currency-switcher-layout-select').on('customAction', function(){
    let item = app.editor.$g(app.selector).find('.ba-currency-switcher-wrapper').removeClass(app.edit.layout),
        modal = $g('#currency-switcher-settings-dialog');
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    modal.find('#currency-switcher-design-options [class*="-layout-options"]').hide();
    modal.find('#currency-switcher-design-options .'+app.edit.layout+'-options').css('display', '');
    item.addClass(app.edit.layout);
    if (app.edit.layout == 'ba-lightbox-layout') {
        app.edit.desktop.dropdown.padding = {
            "bottom": 50,
            "left": 50,
            "right": 50,
            "top": 50
        }
    } else if (app.edit.layout == 'ba-dropdown-layout') {
        app.edit.desktop.dropdown.padding = {
            "bottom": 20,
            "left": 30,
            "right": 30,
            "top": 20
        }
    }
    modal.find('.slideshow-style-custom-select input[type="hidden"]').val('switcher');
    modal.find('.slideshow-style-custom-select input[readonly]').val(app._('CURRENCY_SWITCHER'));
    showSlideshowDesign('switcher', modal.find('.slideshow-style-custom-select'));
    app.sectionRules();
    app.addHistory();
});

$g('#currency-switcher-settings-dialog .slideshow-style-custom-select').on('showedDesign', function(){
    let style = this.querySelector('input[type="hidden"]').value,
        wrapper = app.editor.$g(app.selector).find('.ba-currency-switcher-wrapper');
    if ((style == 'list' || style == 'dropdown') && !wrapper.hasClass('visible-currency-switcher-list')) {
        wrapper.find('.ba-currency-switcher-active .ba-currency-switcher-item').trigger('click');
    } else if (style == 'switcher' && wrapper.hasClass('visible-currency-switcher-list')) {
        app.editor.app.currencySwitcher.hide();
    }
});

app.modules.currencySwitcherEditor = true;
app.currencySwitcherEditor();