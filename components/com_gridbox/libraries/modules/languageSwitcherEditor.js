/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.languageSwitcherEditor = function(){
	app.selector = '#'+app.editor.app.edit;
    let modal = $g('#language-switcher-settings-dialog');
    modal.find('.active').removeClass('active');
    modal.find('a[href="#language-switcher-general-options"]').parent().addClass('active');
    $g('#language-switcher-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    modal.find('.language-switcher-layout-select').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.layout;
        value = this.querySelector('li[data-value="'+app.edit.layout+'"]').textContent.trim();
        this.querySelector('input[type="text"]').value = value;
    });
    modal.find('[data-group="flag"]').each(function(){
        value = app.getValue('flag', this.dataset.option);
        if (this.type == 'number' || this.type == 'text') {
            app.setLinearInput($g(this), value);
        } else if (this.dataset.value == value) {
            this.classList.add('active');
        }
    });
    modal.find('#language-switcher-design-options [class*="-layout-options"]').hide();
    modal.find('#language-switcher-design-options .'+app.edit.layout+'-options').css('display', '');
    if (app.edit.layout != 'ba-default-layout') {
        modal.find('.slideshow-style-custom-select input[type="hidden"]').val('switcher');
        modal.find('.slideshow-style-custom-select input[readonly]').val(app._('LANGUAGE_SWITCHER'));
        showSlideshowDesign('switcher', modal.find('.slideshow-style-custom-select'));
    }
    app.setDefaultState('#language-switcher-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#language-switcher-settings-dialog .margin-settings-group');
    setDisableState('#language-switcher-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.language-switcher-layout-select').on('customAction', function(){
    let item = app.editor.$g(app.selector).find('.ba-language-switcher-wrapper').removeClass(app.edit.layout),
        modal = $g('#language-switcher-settings-dialog');
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    modal.find('#language-switcher-design-options [class*="-layout-options"]').hide();
    modal.find('#language-switcher-design-options .'+app.edit.layout+'-options').css('display', '');
    item.addClass(app.edit.layout);
    item.find('span')[app.edit.layout == 'ba-default-layout' ? 'addClass' : 'removeClass']('ba-tooltip');
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
    if (app.edit.layout != 'ba-default-layout') {
        modal.find('.slideshow-style-custom-select input[type="hidden"]').val('switcher');
        modal.find('.slideshow-style-custom-select input[readonly]').val(app._('LANGUAGE_SWITCHER'));
        showSlideshowDesign('switcher', modal.find('.slideshow-style-custom-select'));
    }
    app.sectionRules();
    app.addHistory();
});

$g('#language-switcher-settings-dialog .slideshow-style-custom-select').on('showedDesign', function(){
    let style = this.querySelector('input[type="hidden"]').value,
        wrapper = app.editor.$g(app.selector).find('.ba-language-switcher-wrapper');
    if ((style == 'list' || style == 'dropdown') && !wrapper.hasClass('visible-language-switcher-list')) {
        wrapper.find('.ba-language-switcher-active .ba-language-switcher-item').trigger('click');
    } else if (style == 'switcher' && wrapper.hasClass('visible-language-switcher-list')) {
        app.editor.app.languageSwitcher.hide();
    }
});

app.modules.languageSwitcherEditor = true;
app.languageSwitcherEditor();