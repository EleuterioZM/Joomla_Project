/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initCart = function(obj, key){
    let a = $g('#'+key+' > .ba-button-wrapper > a');
    app.storeCart.url = a.attr('data-url');
    a.removeAttr('data-url').on('click', function(event){
        event.preventDefault();
        if (this.clicked) {
            return false;
        }
        this.clicked = true;
        let $this = this;
        if (!app.cartDialog) {
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.getStoreCart', {
                view: themeData.page.view
            }).then(function(html){
                if (html) {
                    let div = document.createElement('div');
                    div.innerHTML = html;
                    div = div.querySelector('.ba-store-cart-backdrop');
                    document.body.append(div);
                    app.cartDialog = $g(div).attr('data-layout', obj.layout);
                    app.storeCart.addEvents();
                    setTimeout(function(){
                        app.storeCart.show(obj.layout);
                    }, 100);
                }
                $this.clicked = false;
            });
        } else {
            app.storeCart.show(obj.layout);
            $this.clicked = false;
        }
    });
    if (themeData.page.view != 'gridbox' && document.documentElement.dataset.cached == 'true') {
        app.storeCart.updateCartTotal();
    }
    initItems();
}

app.storeCart = {
    applyPromoCode: function(promo){
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.applyPromoCode', {
            promo: promo
        }).then(function(html){
            if (html == 'invalid') {
                app.showNotice(gridboxLanguage['COUPON_CODE_INVALID'], 'ba-alert');
            } else {
                app.storeCart.updateCartTotal();
                if (app.storeCart.promoDialog && app.storeCart.promoDialog.hasClass('in')) {
                    app.storeCart.promoDialog.modal('hide');
                }
            }
        });
    },
    addEvents: function(){
        app.cartDialog.on('click', '.ba-store-cart-close', function(){
            app.cartDialog.removeClass('ba-visible-store-cart').addClass('ba-store-cart-backdrop-out');
            setTimeout(function(){
                app.cartDialog.removeClass('ba-store-cart-backdrop-out');
                document.body.classList.remove('ba-store-cart-opened');
                document.body.classList.remove('ba-not-default-header');
                document.body.style.removeProperty('--body-scroll-width');
            }, 400);
        }).on('click', 'a', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
        }).on('click', '.ba-remove-promo', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
            app.storeCart.applyPromoCode('');
        }).on('click', '.show-promo-code', function(){
            if (!app.storeCart.promoDialog) {
                let div = document.createElement('div');
                div.style.display = 'none';
                div.className = 'ba-modal-sm modal hide';
                div.innerHTML = '<div class="modal-body"><h3 class="ba-modal-title">'+gridboxLanguage['ENTER_PROMO_CODE']+'</h3>'+
                    '<div class="ba-input-lg"><input type="text" placeholder="'+gridboxLanguage['ENTER_PROMO_CODE']+
                    '"><span class="focus-underline"></span></div></div>'+
                    '<div class="modal-footer"><a href="#" class="ba-btn" data-dismiss="modal">'+gridboxLanguage['CANCEL']+'</a>'+
                    ' <a href="#" class="ba-btn-primary active-button">'+gridboxLanguage['APPLY']+'</a></div>';
                document.body.append(div);
                app.storeCart.promoDialog = $g(div);
                app.storeCart.promoDialog.find('.ba-btn-primary').on('click', function(event){
                    event.preventDefault();
                    if (themeData.page.view == 'gridbox') {
                        return false;
                    }
                    let promo = app.storeCart.promoDialog.find('input').val();
                    app.storeCart.applyPromoCode(promo);
                });
            }
            app.storeCart.promoDialog.modal().find('input').val(this.dataset.code);
        }).on('click', '.ba-cart-product-remove-extra-option i', function(){
            let extra = this.closest('.ba-cart-product-extra-option');
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeExtraOptionCart', {
                id: extra.closest('.ba-cart-product-row').dataset.id,
                key: extra.dataset.key,
                field_id: extra.dataset.id,
            }).then(function(text){
                app.storeCart.updateCartTotal();
            });
            extra.remove();
        }).on('click', '.ba-cart-product-remove-cell', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
            let row = this.closest('.ba-cart-product-row'),
                id = row.dataset.id;
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeProductFromCart', {
                id: id
            }).then(function(text){
                app.storeCart.updateCartTotal();
            });
            row.remove();
        }).on('click', '.ba-cart-product-quantity-cell i[data-action]', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
            if (!this.input) {
                this.input = this.closest('.ba-cart-product-quantity-cell').querySelector('input');
            }
            let value = this.dataset.action == '+' ? this.input.value * 1 + 1 : this.input.value * 1 - 1,
                min = this.input.dataset.min ? this.input.dataset.min * 1 : 1,
                $this = this,
                stock = this.input.dataset.stock;
            if (value >= min && (stock == '' || value <= stock * 1)) {
                this.input.value = value;
                $g(this.input).trigger('customInput');
            } else if (stock != '' && stock != '0' && value > stock * 1) {            
                if (!this.notice) {
                    this.notice =  document.createElement('span');
                    this.notice.className = 'ba-variation-notice';
                    this.notice.textContent = gridboxLanguage['IN_STOCK']+' '+stock;
                    this.closest('.ba-cart-product-quantity-cell').append(this.notice);
                }
                clearTimeout(this.delay);
                this.delay = setTimeout(function(){
                    $this.notice.remove();
                    $this.notice = null;
                }, 3000);
            }
        }).on('input', '.ba-cart-product-quantity-cell input', function(){
            let match = this.value.match(/\d+/),
                value = this.value;
            if (!match || value == 0) {
                value = '';
            } else if (match) {
                value = match[0] * 1;
            }
            if (this.dataset.stock != '' && value > this.dataset.stock * 1) {
                value = this.dataset.stock * 1;
            }
            if (String(value) != this.value) {
                this.value = value;
            }
            $g(this).trigger('customInput');
        }).on('customInput', '.ba-cart-product-quantity-cell input', function(){
            if (themeData.page.view == 'gridbox') {
                return false;
            }
            if (this.value != '') {
                let quantity = this.value * 1,
                    min = this.dataset.min ? this.dataset.min * 1 : 1,
                    thousand = this.dataset.thousand,
                    separator = this.dataset.separator,
                    decimals = this.dataset.decimals,
                    rate = this.dataset.rate,
                    price = (this.dataset.sale == '' ? this.dataset.price : this.dataset.sale) * quantity,
                    row = this.closest('.ba-cart-product-row');
                price = app.renderPrice(price, thousand, separator, decimals, rate);
                row.querySelector('.ba-cart-price-wrapper .ba-cart-price-value').textContent = price;
                if (this.dataset.sale != '') {
                    price = this.dataset.price * quantity;
                    price = app.renderPrice(price, thousand, separator, decimals, rate);
                    row.querySelector('.ba-cart-sale-price-wrapper .ba-cart-price-value').textContent = price;
                }
                row.querySelectorAll('.ba-cart-product-extra-option-price').forEach(function($this){
                    price = $this.dataset.price * quantity;
                    price = app.renderPrice(price, thousand, separator, decimals, rate);
                    $this.querySelector('.ba-cart-price-value').textContent = price;
                });
                clearTimeout(this.delay);
                this.delay = setTimeout(function(){
                    if (quantity < min) {
                        quantity = min;
                    }
                    app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.updateProductQuantity', {
                        id: row.dataset.id,
                        quantity: quantity
                    }).then(function(text){
                        app.storeCart.updateCartTotal('.ba-cart-checkout');
                    });
                }, 200);
            }
        }).on('blur', '.ba-cart-product-quantity-cell input', function(){
            let min = this.dataset.min ? this.dataset.min * 1 : 1;
            if (this.value != '' && this.value * 1 < min) {
                this.value = min;
                let quantity = this.value * 1,
                    thousand = this.dataset.thousand,
                    separator = this.dataset.separator,
                    decimals = this.dataset.decimals,
                    rate = this.dataset.rate,
                    price = (this.dataset.sale == '' ? this.dataset.price : this.dataset.sale) * quantity,
                    row = this.closest('.ba-cart-product-row');
                price = app.renderPrice(price, thousand, separator, decimals, rate);
                row.querySelector('.ba-cart-price-wrapper .ba-cart-price-value').textContent = price;
                if (this.dataset.sale != '') {
                    price = this.dataset.price * quantity;
                    price = app.renderPrice(price, thousand, separator, decimals, rate);
                    row.querySelector('.ba-cart-sale-price-wrapper .ba-cart-price-value').textContent = price;
                }
                row.querySelectorAll('.ba-cart-product-extra-option-price').forEach(function($this){
                    price = $this.dataset.price * quantity;
                    price = app.renderPrice(price, thousand, separator, decimals, rate);
                    $this.querySelector('.ba-cart-price-value').textContent = price;
                });
            }
        }).on('click', '.ba-cart-checkout-btn', function(){
            if (!this.classList.contains('disabled') && themeData.page.view != 'gridbox') {
                window.location.href = app.storeCart.url;
            }
        });
    },
    show: function(layout){
        let header = document.querySelector('body > header.header'),
            style = header ? getComputedStyle(header): {};
        app.cartDialog.attr('data-layout', layout).addClass('ba-visible-store-cart');
        document.body.style.setProperty('--body-scroll-width', (window.innerWidth - document.documentElement.clientWidth)+'px');
        document.body.classList.add('ba-store-cart-opened');
        if (style.position != 'relative') {
            document.body.classList.add('ba-not-default-header');
        }
        document.querySelectorAll('.ba-item-cart').forEach((cart) => {
            cart.style.display = '';
        })
    },
    updateCartTotal: function(search){
        if (!search) {
            search = '.ba-store-cart';
        }
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.getStoreCart', {
            view: themeData.page.view
        }).then(function(html){
            if (html) {
                let div = document.createElement('div'),
                    count = total = 0;
                div.innerHTML = html;
                div.querySelectorAll('.ba-cart-products-list').forEach(function($this){
                    total = $this.dataset.total;
                    count = $this.dataset.quantity;
                });
                if (app.cartDialog) {
                    app.cartDialog.find(search)[0].innerHTML = div.querySelector(search).innerHTML;
                }
                document.querySelectorAll('.ba-item-cart').forEach(function($this){
                    let obj = app.items[$this.id],
                        i = $this.querySelector('a i'),
                        span = $this.querySelector('.store-currency-price');
                    span ? span.textContent = total : '';
                    i ? i.dataset.productsCount = count : '';
                    if (obj && obj.desktop.view.empty && count == 0) {
                        $this.style.display = 'none';
                    }
                });
                if (app.checkoutForm) {
                    app.checkoutForm.reloadCheckout();
                }
            }
        });
    }
}

if (app.modules.initCart) {
    app.initCart(app.modules.initCart.data, app.modules.initCart.selector);
}