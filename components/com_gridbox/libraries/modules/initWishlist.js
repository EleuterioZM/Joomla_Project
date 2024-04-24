/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initWishlist = function(obj, key){
    let a = $g('#'+key+' > .ba-button-wrapper a');
    a.on('click', function(event){
        event.preventDefault();
        if (this.clicked) {
            return false;
        }
        this.clicked = true;
        let $this = this;
        if (!app.wishlistDialog) {
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.getWishlist', {
                view: themeData.page.view
            }).then(function(html){
                if (html) {
                    let div = document.createElement('div');
                    div.innerHTML = html;
                    div = div.querySelector('.ba-store-wishlist-backdrop');
                    document.body.append(div);
                    app.wishlistDialog = $g(div);
                    app.wishlist.addEvents();
                    setTimeout(function(){
                        app.wishlist.show(obj.layout);
                    }, 100);
                }
                $this.clicked = false;
            });
        } else {
            app.wishlist.show(obj.layout);
            $this.clicked = false;
        }
    });
    if (themeData.page.view != 'gridbox' && document.documentElement.dataset.cached == 'true') {
        app.wishlist.updateWishlist();
    }
    initItems();
}

app.wishlist = {
    addEvents: function(){
        app.wishlistDialog.on('click', '.ba-store-wishlist-close', function(){
            app.wishlistDialog.removeClass('ba-visible-store-wishlist').addClass('ba-store-wishlist-backdrop-out');
            setTimeout(function(){
                app.wishlistDialog.removeClass('ba-store-wishlist-backdrop-out');
                document.body.classList.remove('ba-store-wishlist-opened');
                document.body.classList.remove('ba-not-default-header');
                document.body.style.removeProperty('--body-scroll-width');
            }, 400);
        }).on('click', '.ba-wishlist-product-remove-extra-option i', function(){
            let extra = this.closest('.ba-wishlist-product-extra-option');
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeExtraOptionWishlist', {
                id: extra.closest('.ba-wishlist-product-row').dataset.id,
                key: extra.dataset.key,
                field_id: extra.dataset.id,
            }).then(function(text){
                app.wishlist.updateWishlist();
            });
            extra.remove();
        }).on('click', 'a', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
        }).on('click', '.ba-clear-wishlist', function(){
            if (themeData.page.view != 'gridbox' && app.wishlistDialog.find('.ba-wishlist-products-list').attr('data-quantity') != 0) {
                app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.clearWishlist', {
                    view: themeData.page.view
                }).then(function(html){
                    app.wishlist.setWishlistHTML(html);
                });
            }
        }).on('click', '.ba-wishlist-add-to-cart-btn', function(){
            if (themeData.page.view != 'gridbox') {
                let row = this.closest('.ba-wishlist-product-row');
                app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.moveProductFromWishlist', {
                    id: row.dataset.id,
                    view: themeData.page.view
                }).then(function(html){
                    let img = row.querySelector('img'),
                        title = row.querySelector('.ba-wishlist-product-title').textContent.trim(),
                        str = '<span class="ba-product-notice-message">';
                    if (img) {
                        str += '<span class="ba-product-notice-image-wrapper"><img src="'+img.src+'"></span>';
                    }
                    str += '<span class="ba-product-notice-text-wrapper">'+title+
                        ' '+gridboxLanguage['HAS_BEEN_ADDED_TO_CART']+'</span></span>';
                    app.showNotice(str, 'ba-product-notice');
                    app.wishlist.setWishlistHTML(html);
                    if (app.storeCart) {
                        app.storeCart.updateCartTotal();
                    }
                });
            }
        }).on('click', '.ba-wishlist-add-all-btn', function(){
            if (themeData.page.view != 'gridbox') {
                app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.moveProductsFromWishlist', {
                    view: themeData.page.view
                }).then(function(html){
                    app.showNotice(gridboxLanguage['PRODUCTS_ADDED_TO_CART'], 'ba-product-notice');
                    app.wishlist.setWishlistHTML(html);
                    if (app.storeCart) {
                        app.storeCart.updateCartTotal();
                    }
                });
            }
        }).on('click', '.ba-wishlist-product-remove-cell i', function(){
            if (themeData.page.view != 'gridbox') {
                let row = this.closest('.ba-wishlist-product-row');
                app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeProductFromWishlist', {
                    id: row.dataset.id,
                    view: themeData.page.view
                }).then(function(html){
                    app.wishlist.setWishlistHTML(html);
                });
            }
        })
    },
    show: function(layout){
        let header = document.querySelector('body > header.header'),
            style = header ? getComputedStyle(header): {};
        app.wishlistDialog.attr('data-layout', layout).addClass('ba-visible-store-wishlist');
        document.body.style.setProperty('--body-scroll-width', (window.innerWidth - document.documentElement.clientWidth)+'px');
        document.body.classList.add('ba-store-wishlist-opened');
        if (style.position != 'relative') {
            document.body.classList.add('ba-not-default-header');
        }
    },
    setWishlistHTML:function(html, search){
        let div = document.createElement('div'),
            count = 0;
        if (!search) {
            search = '.ba-store-wishlist';
        }
        div.innerHTML = html;
        count = div.querySelector('.ba-wishlist-products-list').dataset.quantity;
        if (app.wishlistDialog) {
            app.wishlistDialog.find(search)[0].innerHTML = div.querySelector(search).innerHTML;
        }
        document.querySelectorAll('.ba-item-wishlist a i').forEach(function($this){
            $this.dataset.productsCount = count;
        });
    },
    updateWishlist: function(search){
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.getWishlist', {
            view: themeData.page.view
        }).then(function(html){
            if (html) {
                app.wishlist.setWishlistHTML(html, search);
            }
        });
    }
}

if (app.modules.initWishlist) {
    app.initWishlist(app.modules.initWishlist.data, app.modules.initWishlist.selector);
}