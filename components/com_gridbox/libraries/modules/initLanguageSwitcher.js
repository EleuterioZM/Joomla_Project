/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initLanguageSwitcher = function(obj, key){
    $g('#'+key).on('click', '.ba-language-switcher-active .ba-language-switcher-item', function(event){
        event.stopPropagation();
        app.languageSwitcher.hide(0);
        if (app.currencySwitcher) {
            app.currencySwitcher.hide(0);
        }
        $g('body').off('click.switcher').one('click.switcher', function(){
            app.languageSwitcher.hide();
        });
        app.languageSwitcher.show(this);
    }).on('click', '.ba-language-switcher-list', function(event){
        event.stopPropagation();
    }).on('click', '.ba-language-switcher-list i, .ba-language-switcher-list .ba-language-switcher-item', function(event){
        app.languageSwitcher.hide();
    });
    $g('#'+key+' .ba-language-switcher-item a[data-currency]').on('click', function(event){
        if (themeData.page.view == 'gridbox') {
            return;
        }
        event.preventDefault();
        let href = this.href;
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCurrency', {
            currency: this.dataset.currency
        }).then((text) => {
            if (text) {
                console.info(text);
            }
            window.location.href = href;
        });
    });
    if (themeData.page.view != 'gridbox') {
        $g('#'+key+' .ba-default-layout').on('mouseenter', function(){
            $g(this).parents('.ba-row').addClass('language-switcher-hovered');
        }).on('mouseleave', function(){
            $g(this).parents('.ba-row').removeClass('language-switcher-hovered');
        });
    }
    initItems();
}

app.languageSwitcher = {
    show: function($this){
        let wrapper = $this.closest('.ba-language-switcher-wrapper'),
            rect = $this.getBoundingClientRect(),
            rect2 = $this.closest('.ba-language-switcher-active').getBoundingClientRect();
        wrapper.style.setProperty('--switcher-left', (rect.left - rect2.left + rect.width / 2)+'px');
        wrapper.classList.add('visible-language-switcher-list');
        wrapper.closest('.ba-wrapper').classList.add('language-switcher-visible');
        wrapper.closest('header, footer, .body').classList.add('visible-language-switcher-lightbox');
        $g($this).parents('.ba-row, .ba-row-wrapper, .ba-grid-column-wrapper').addClass('language-switcher-hovered');
    },
    removeClass: function(){
        $g('.visible-language-switcher-list').removeClass('visible-language-switcher-list language-switcher-out');
        $g('.language-switcher-visible').removeClass('language-switcher-visible')
            .closest('header, footer, .body').removeClass('visible-language-switcher-lightbox');
        $g('.language-switcher-hovered').removeClass('language-switcher-hovered');
    },
    hide: function(time){
        $g('body').off('click.switcher');
        $g('.visible-language-switcher-list').addClass('language-switcher-out');
        if (typeof time == 'undefined') {
            setTimeout(function(){
                app.languageSwitcher.removeClass();
            }, 300);
        } else {
            app.languageSwitcher.removeClass();
        }
        
    }
}

if (app.modules.initLanguageSwitcher) {
    app.initLanguageSwitcher(app.modules.initLanguageSwitcher.data, app.modules.initLanguageSwitcher.selector);
}