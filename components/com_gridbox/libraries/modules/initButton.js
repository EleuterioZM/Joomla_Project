/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initButton = function(obj, key){
    if (obj.link.link.match(/\[product ID=\d+\]/) && themeData.page.view != 'gridbox') {
        let id = obj.link.link.match(/\d+/)[0];
        $g('#'+key+' a').on('click', function(event){
            event.preventDefault();
            let link = this.href;
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.addPostToCart', {
                id: id
            }).then(function(text){
                let response = JSON.parse(text);
                if (response.status) {
                    if (app.storeCart) {
                        app.storeCart.updateCartTotal();
                        $g('.ba-item-cart a').first().trigger('click');
                    }
                } else {
                    localStorage.setItem('select-options', app._('PLEASE_SELECT_OPTION'));
                    window.location.href = link;
                }
            });
        });
    }
    initItems();
}

if (app.modules.initButton) {
    app.initButton(app.modules.initButton.data, app.modules.initButton.selector);
}