/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initCurrencySwitcher = function(obj, key){
    $g('#'+key).on('click', '.ba-currency-switcher-active .ba-currency-switcher-item', function(event){
        event.stopPropagation();
        app.currencySwitcher.hide(0);
        if (app.languageSwitcher) {
            app.languageSwitcher.hide(0);
        }
        $g('body').off('click.switcher').one('click.switcher', function(){
            app.currencySwitcher.hide();
        });
        app.currencySwitcher.show(this);
    }).on('click', '.ba-currency-switcher-list', function(event){
        event.stopPropagation();
    }).on('click', '.ba-currency-switcher-list i', function(event){
        app.currencySwitcher.hide();
    }).on('click', '.ba-currency-switcher-list .ba-currency-switcher-item', function(event){
        app.currencySwitcher.hide();
        if (themeData.page.view == 'gridbox') {
            return;
        }
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCurrency', {
            currency: this.dataset.currency
        }).then((text) => {
            if (text) {
                console.info(text);
            }
            window.location.reload();
        });
    });
    initItems();
}

app.currencySwitcher = {
    show: function($this){
        let wrapper = $this.closest('.ba-currency-switcher-wrapper'),
            rect = $this.getBoundingClientRect(),
            rect2 = $this.closest('.ba-currency-switcher-active').getBoundingClientRect();
        wrapper.style.setProperty('--switcher-left', (rect.left - rect2.left + rect.width / 2)+'px');
        wrapper.classList.add('visible-currency-switcher-list');
        wrapper.closest('.ba-wrapper').classList.add('currency-switcher-visible');
        wrapper.closest('header, footer, .body').classList.add('visible-currency-switcher-lightbox');
        $g($this).parents('.ba-row, .ba-row-wrapper, .ba-grid-column-wrapper').addClass('currency-switcher-hovered');
    },
    removeClass: function(){
        $g('.visible-currency-switcher-list').removeClass('visible-currency-switcher-list currency-switcher-out');
        $g('.currency-switcher-visible').removeClass('currency-switcher-visible')
            .closest('header, footer, .body').removeClass('visible-currency-switcher-lightbox');
        $g('.currency-switcher-hovered').removeClass('currency-switcher-hovered');
    },
    hide: function(time){
        $g('body').off('click.switcher');
        $g('.visible-currency-switcher-list').addClass('currency-switcher-out');
        if (typeof time == 'undefined') {
            setTimeout(function(){
                app.currencySwitcher.removeClass();
            }, 300);
        } else {
            app.currencySwitcher.removeClass();
        }
        
    }
}

if (app.modules.initCurrencySwitcher) {
    app.initCurrencySwitcher(app.modules.initCurrencySwitcher.data, app.modules.initCurrencySwitcher.selector);
}