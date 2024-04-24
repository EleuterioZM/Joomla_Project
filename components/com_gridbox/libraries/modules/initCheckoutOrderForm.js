/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initCheckoutOrderForm = function(obj, key){
    let not = '[type="checkbox"], [type="radio"]',
        required = $g('.ba-checkout-form-wrapper').find('input[required], textarea[required], select[required]').not(not),
        search = '.ba-checkout-order-form-shipping-wrapper, .ba-checkout-order-form-payments-wrapper'+
            ', .ba-checkout-form-fields[data-type="checkbox"], .ba-checkout-form-fields[data-type="radio"],'+
            ' .ba-checkout-form-fields[data-type="acceptance"]',
        checkout = $g('body');
    if (!document.querySelector('.ba-item-cart')) {
        $g('.ba-checkout-edit-order').remove();
    }
    required.on('focus', function(){
        app.checkoutForm.removeAlertTooltip(this);
    }).on('checkAlert', function(){
        let alert = !this.value.trim(),
            wrapper = this.closest('.ba-checkout-form-field-wrapper'),
            key = 'THIS_FIELD_REQUIRED';
        if (this.value && this.type == 'email') {
            alert = !(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*$/.test(this.value));
            key = 'ENTER_VALID_VALUE';
        } else if (this.dataset.type == 'country' || this.dataset.type == 'region') {
            wrapper = this.closest('.ba-checkout-country-wrapper');
        }
        if (this.dataset.type == 'region' && !this.closest('.visible-region-select')) {
            alert = false;
        }
        app.checkoutForm.toggleAlertTooltip(alert, this, wrapper, key);
    });
    $g('#'+key).on('checkAlert', '.ba-inpost-shipping-wrapper', function(){
        console.info(this.closest('.ba-checkout-order-form-shipping.selected'))
        if (!this.closest('.ba-checkout-order-form-shipping.selected')) {
            return;
        }
        console.info(this.querySelector('.inpost-selected-address').textContent.trim())
        let alert = this.querySelector('.inpost-selected-address').textContent.trim() == '';
        console.info(alert)
        app.checkoutForm.toggleAlertTooltip(alert, this, this, 'THIS_FIELD_REQUIRED');
    });
    checkout.on('change', search, function(){
        app.checkoutForm.removeAlertTooltip(this);
    }).on('checkAlert', search, function(){
        let isField = this.classList.contains('ba-checkout-form-fields'),
            alert = !isField || (isField && this.querySelector('input[required]'));
        this.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(function($this){
            if ($this.checked && (!isField || (isField && $this.required))) {
                alert = false;
            }
        });
        app.checkoutForm.toggleAlertTooltip(alert, this, this, 'THIS_FIELD_REQUIRED');
    }).on('click', '.ba-checkout-edit-order', () => {
        $g('.ba-item-cart a').first().trigger('click');
    }).on('click', '.ba-checkout-order-promo-code .show-promo-code', () => {
        if (!app.checkoutForm.promoDialog) {
            let div = document.createElement('div');
            div.style.display = 'none';
            div.className = 'ba-modal-sm modal hide checkout-promocode-dialog';
            div.innerHTML = '<div class="modal-body"><h3 class="ba-modal-title">'+gridboxLanguage['ENTER_PROMO_CODE']+'</h3>'+
                '<div class="ba-input-lg"><input type="text" placeholder="'+gridboxLanguage['ENTER_PROMO_CODE']+
                '"><span class="focus-underline"></span></div></div>'+
                '<div class="modal-footer"><a href="#" class="ba-btn" data-dismiss="modal">'+gridboxLanguage['CANCEL']+'</a>'+
                ' <a href="#" class="ba-btn-primary active-button">'+gridboxLanguage['APPLY']+'</a></div>';
            document.body.append(div);
            app.checkoutForm.promoDialog = $g(div);
            app.checkoutForm.promoDialog.find('.ba-btn-primary').on('click', function(event){
                event.preventDefault();
                if (themeData.page.view == 'gridbox') {
                    return false;
                }
                let promo = app.checkoutForm.promoDialog.find('input').val();
                app.checkoutForm.applyPromoCode(promo);
            });
        }
        app.checkoutForm.promoDialog.modal().find('input').val(this.dataset.code);
    }).on('click', '.ba-checkout-order-promo-code .ba-remove-promo', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        app.checkoutForm.applyPromoCode('');
    });
    $g('.ba-item-checkout-order-form').on('click', '.inpost-trigger-modal', function(){
        app.checkoutForm.removeAlertTooltip(this.closest('.ba-inpost-shipping-wrapper'));
        app.inpost.show(this);
    });
    $g('#'+key).on('change', 'input[name="ba-checkout-shipping"]', function(){
        let parent = $g(this).closest('.ba-item-checkout-order-form').find('.ba-checkout-order-form-total-wrapper');
        parent.find('.ba-checkout-order-form-shipping .ba-checkout-order-price-value').text(this.dataset.price);
        parent.find('.ba-checkout-order-form-shipping-tax, .ba-checkout-order-form-shipping-includes-tax')
            .find('.ba-checkout-order-price-value').text(this.dataset.tax);
        parent.find('.ba-checkout-order-form-total .ba-checkout-order-price-value').text(this.dataset.total);
        parent.find('.ba-checkout-order-form-includes-tax .ba-checkout-order-price-value').text(this.dataset.totalTax);
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCartShipping', {
            id: this.value
        });
    });
    $g('#'+key).on('change', 'input[name="ba-checkout-payment"], input[name="ba-checkout-shipping"]', function(){
        let row = $g(this).closest('.ba-checkout-order-form-row'),
            selected = row.parent().find('.selected');
        selected.each(function(){
            if (this.querySelector('.ba-checkout-order-description-inner')) {
                let h = $g(this).find('.ba-checkout-order-description-inner').height()+'px';
                this.style.setProperty('--description-height', h);
            }
        });
        row.each(function(){
            if (this.querySelector('.ba-checkout-order-description-inner')) {
                let h = $g(this).find('.ba-checkout-order-description-inner').height()+'px';
                this.style.setProperty('--description-height', h);
            }
        });
        setTimeout(function(){
            selected.removeClass('selected');
            row.addClass('selected');
        }, 50);
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCartPayment', {
            id: this.value
        });
    });
    $g('#'+key).on('click', '.ba-checkout-place-order-btn', function(){
        if (themeData.page.view == 'gridbox' || this.submited) {
            return false;
        }
        required.trigger('checkAlert');
        checkout.find(search).trigger('checkAlert');
        $g('.ba-inpost-shipping-wrapper').trigger('checkAlert')
        let alert = $g('.ba-checkout-alert');
        if (alert.length) {
            alert[0].scrollIntoView(true);
            return false;
        }
        app.checkoutForm.placeOrder(this);
    });
    $g('.ba-item-checkout-form').on('change', 'select[data-type="country"]', function(){
        let wrapper = this.closest('.ba-checkout-form-field-wrapper'),
            region = wrapper.querySelector('select[data-type="region"]');
            app.checkoutForm.removeAlertTooltip(region);
        app.checkoutForm.setCartRegions(wrapper, this.value);
        app.checkoutForm.setCartCountry();
    }).on('change', 'select[data-type="region"]', function(){
        app.checkoutForm.setCartCountry();
    });
    $g('.ba-checkout-form-fields [name]').on('input', function(){
        if (themeData.page.view == 'gridbox' || this.submited) {
            return false;
        }
        let $this = this,
            time = (this.localName == 'select' || this.type == 'hidden') ? 0 : 500;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCustomerInfo', {
                id: $this.name,
                value: $this.value
            });
        }, time);
    });
    $g('.ba-guest-authentication').on('click', function(){
        document.body.classList.add('ba-out-checkout-authentication');
        setTimeout(function(){
            document.body.classList.remove('ba-out-checkout-authentication');
            document.body.classList.remove('ba-visible-checkout-authentication');
        }, 300)
    });
    $g('.ba-checkout-authentication-backdrop [data-step]').on('click', function(event){
        event.preventDefault();
        this.closest('div[data-wrapper]').style.display = 'none';
        $g('div[data-wrapper="'+this.dataset.step+'"]').css('display', '');
    });
    $g('.ba-checkout-login-wrapper .ba-checkout-authentication-input').on('keyup', function(event){
        let data = {};
        $g('.ba-checkout-login-wrapper .ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
        });
        if (event.keyCode == 13 && data.password && data.username) {
            $g('.ba-user-authentication').trigger('click');
        }
    });
    $g('.ba-user-authentication').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let wrapper = $g('.ba-checkout-login-wrapper'),
            data = {
                remember: Number(document.querySelector('.ba-checkout-authentication-checkbox input').checked)
            };
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            if (!data[this.name]) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'THIS_FIELD_REQUIRED');
            }
        });
        app.checkoutForm.checkCaptcha(wrapper);
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=store.login', data, true);
        }
    });
    $g('.ba-user-registration').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let key = 'THIS_FIELD_REQUIRED',
            wrapper = $g('.ba-checkout-registration-wrapper'),
            data = {};
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            this.inputWrapper = this.closest('.ba-checkout-authentication-input');
            if (!data[this.name]) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.inputWrapper, 'THIS_FIELD_REQUIRED');
            } else if (this.type == 'email' && !(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*$/.test(data[this.name]))) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.inputWrapper, 'ENTER_VALID_VALUE');
            }
        });
        wrapper.find('.ba-checkout-authentication-checkbox input').each(function(){
            this.inputWrapper = this.closest('.ba-checkout-authentication-checkbox');
            if (!this.checked) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.inputWrapper, 'THIS_FIELD_REQUIRED');
            }
        });
        if (data.password1 && data.password2 && data.password1 != data.password2) {
            wrapper.find('.ba-checkout-authentication-input input[name="password2"]').each(function(){
                key = 'PASSWORDS_ENTERED_NOT_MATCH';
                app.checkoutForm.toggleAlertTooltip(true, this, this.inputWrapper, key);
            });
        }
        app.checkoutForm.checkCaptcha(wrapper);
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=store.register', data, true);
        }
    });
    $g('.ba-leave-checkout').on('click', function(){
        window.history.back();
    });
    $g('.ba-checkout-authentication-input input').on('focus', function(){
        app.checkoutForm.removeAlertTooltip(this);
    });
    $g('.ba-checkout-authentication-checkbox input').on('change', function(){
        app.checkoutForm.removeAlertTooltip(this);
    });
    $g('.ba-remind-password-authentication').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let wrapper = $g('.ba-forgot-password-wrapper'),
            data = {};
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            if (!(/@/g.test(data.email) && data.email.match(/@/g).length == 1)) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'ENTER_VALID_VALUE');
            }
        });
        app.checkoutForm.checkCaptcha(wrapper);
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.remindPassword', data, false).then((response) => {
                if (response.status) {
                    wrapper.css('display', 'none');
                    $g('.ba-password-request-wrapper').css('display', '');
                }
            });
        }
    });
    $g('.ba-request-password-authentication').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let wrapper = $g('.ba-password-request-wrapper'),
            data = {};
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            if (!data[this.name]) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.requestPassword', data, false).then((response) => {
                if (response.status) {
                    wrapper.css('display', 'none');
                    $g('.ba-password-reset-wrapper').css('display', '').find('input[type="hidden"]').val(response.id);
                }
            });
        }
    });
    $g('.ba-reset-password-authentication').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let wrapper = $g('.ba-password-reset-wrapper'),
            data = {};
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            if (!data[this.name]) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (data.password1 && data.password2 && data.password1 != data.password2) {
            wrapper.find('input[name="password2"]').each(function(){
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'PASSWORDS_ENTERED_NOT_MATCH');
            });
        }
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.resetPassword', data, false).then((response) => {
                if (response.status) {
                    wrapper.css('display', 'none');
                    $g('.ba-password-successful-reset-wrapper').css('display', '');
                }
            });
        }
    });
    $g('.ba-username-authentication').on('click', function(){
        if (app.checkoutForm.process) {
            return false;
        }
        let wrapper = $g('.ba-forgot-username-wrapper'),
            data = {};
        wrapper.find('.ba-checkout-authentication-input input').each(function(){
            data[this.name] = this.value.trim();
            if (!(/@/g.test(data.email) && data.email.match(/@/g).length == 1)) {
                app.checkoutForm.toggleAlertTooltip(true, this, this.closest('.ba-checkout-authentication-input'), 'ENTER_VALID_VALUE');
            }
        });
        app.checkoutForm.checkCaptcha(wrapper);
        if (wrapper.find('.ba-checkout-alert').length == 0) {
            app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.remindUsername', data, false).then((response) => {
                if (response.status) {
                    wrapper.css('display', 'none');
                    $g('.ba-forgot-username-sended-wrapper').css('display', '');
                }
            });
        }
    });
    if (themeData.page.view == 'gridbox' || document.querySelector('.ba-checkout-form-fields[data-type="country"]')) {
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.getTaxCountries').then(function(text){
            app.checkoutForm.countries = JSON.parse(text);
            $g('.ba-checkout-form-fields[data-type="country"] .ba-checkout-form-field-wrapper').each(function(){
                let country = this.querySelector('select[data-type="country"]').value,
                    region = this.querySelector('select[data-type="region"]').dataset.selected;
                if (country) {
                    app.checkoutForm.setCartRegions(this, country, region);
                }
            });
        });
    }
    $g('.ba-checkout-authentication-wrapper .ba-login-integration-btn[data-integration="facebook"]').on('click', function(e){
        if (window.FB) {
            FB.login(function(response){
                app.facebook.getUserInfo(response).then((data) => {
                    app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.socialLogin', data, true);
                });
            });
        }
    });
    if (document.querySelector('.ba-checkout-authentication-wrapper [data-integration="facebook"]') && !app.loading.facebook) {
        app.facebook.load();
    }
    if (document.querySelector('.ba-checkout-authentication-wrapper [data-integration="google"]') && !app.loading.google) {
        app.google.load(app.checkoutForm.google, '.ba-google-login-button', {
            type: 'standard',
            large: 'size',
            width: 400
        });
    }
    initItems();
}

function inpostSelectPoint(point)
{
    app.inpost.set(point);
}

app.inpost = {
    modal: null,
    show: (trigger) => {
        app.inpost.modal = $g('#ba-inpost-map-dialog');
        app.inpost.trigger = trigger;
        app.inpost.modal.modal();
        document.body.classList.add('inpost-map-dialog-opened');
    },
    set: (point) => {
        let address = point.address.line1+' '+point.address.line2;
        app.inpost.trigger.textContent = app._('CHANGE_PARCEL_LOCKER');
        app.inpost.trigger.closest('.ba-inpost-shipping-wrapper').querySelector('.inpost-selected-address').textContent = address;
        app.inpost.modal.modal('hide');
        document.body.classList.remove('inpost-map-dialog-opened');
    }
}

$g('.ba-item-checkout-order-form').on('hide', '#ba-inpost-map-dialog', function(){
    document.body.classList.remove('inpost-map-dialog-opened');
});

app.checkoutForm = {
    applyPromoCode: (promo) => {
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.applyPromoCode', {
            promo: promo
        }).then(function(html){
            if (html == 'invalid') {
                app.showNotice(gridboxLanguage['COUPON_CODE_INVALID'], 'ba-alert');
            } else {
                app.checkoutForm.reloadCheckout().then(function(){
                    if (app.storeCart) {
                        app.storeCart.updateCartTotal();
                    }
                });
                if (app.checkoutForm.promoDialog && app.checkoutForm.promoDialog.hasClass('in')) {
                    app.checkoutForm.promoDialog.modal('hide');
                }
            }
        });
    },
    google: (response) => {
        let json = app.google.JSONWebToken(response.credential),
            data = app.google.getUserInfo(json);
        app.checkoutForm.request(JUri+'index.php?option=com_gridbox&task=account.socialLogin', data, true);
    },
    request: (url, data, redirect) => {
        app.checkoutForm.process = true;
        return new Promise((resolve, reject) => {
            app.fetch(url, data).then(function(text){
                app.checkoutForm.process = false;
                let response = JSON.parse(text);
                if (!response.status) {
                    app.showNotice(response.message, 'ba-alert');
                } else if (redirect) {
                    window.location.href = window.location.href;
                }
                resolve(response);
            });
        });
    },
    checkCaptcha: (wrapper) => {
        wrapper.find('.login-recaptcha').each(function(){
            let response = app.checkoutForm.getRecaptchaResponse(this);
            if (!response) {
                app.checkoutForm.toggleAlertTooltip(true, this, this, 'THIS_FIELD_REQUIRED');
            }
        });
    },
    getRecaptchaResponse: (elem) => {
        let response = '';
        try {
            response = grecaptcha.getResponse(recaptchaObject.data[elem.id]);
        } catch (err) {
            console.info(err)
        }

        return response != '';
    },
    setCartRegions: function(wrapper, value, $default){
        let html = '';
        app.checkoutForm.countries.forEach(function(country){
            if (country.id == value) {
                country.states.forEach(function(region){
                    html += '<option value="'+region.id+'"'+($default == region.id ? ' selected' : '')+'>'+region.title+'</option>';
                });
            }
        });
        if (html) {
            html = '<option value="">'+app._('SELECT')+'</option>'+html;
            wrapper.classList.add('visible-region-select');
        } else {
            wrapper.classList.remove('visible-region-select');
        }
        wrapper.querySelector('select[data-type="region"]').innerHTML = html;
    },
    reloadCheckout: async function(){
        document.querySelector('.ba-checkout-place-order-btn').submited = true;
        return new Promise(function(resolve, reject) {
                app.fetch(window.location.href).then(function(html){
                let div = document.createElement('div'),
                    search = '.ba-item-checkout-order-form';
                div.innerHTML = html;
                document.querySelector(search).innerHTML = div.querySelector(search).innerHTML;
                resolve();
            });
        });
    },
    setCartCountry: function(){
        if (themeData.page.view != 'gridbox') {
            let div = $g('.ba-checkout-form-fields[data-type="country"]'),
                data = {};
            div.find('select').each(function(){
                data[this.dataset.type] = this.value;
            });
            div.find('input[type="hidden"]').val(JSON.stringify(data)).trigger('input');
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.setCartCountry', data).then(function(txt){
                setTimeout(function(){
                    app.checkoutForm.reloadCheckout().then(function(){
                        if (app.storeCart) {
                            app.storeCart.updateCartTotal();
                        }
                    });
                }, 250);
            });
        }
    },
    showModal: function(div){
        let scroll = window.innerWidth - document.documentElement.clientWidth;
        document.body.classList.add('ba-checkout-modal-opened');
        document.body.style.setProperty('--checkout-modal-body-scroll', scroll+'px');
        div.classList.add('ba-visible-checkout-modal');
        scroll = div.offsetWidth - div.clientWidth;
        div.style.setProperty('--checkout-modal-scroll', scroll+'px');
    },
    hideModal: function(div){
        div.classList.remove('ba-visible-checkout-modal');
        setTimeout(function(){
            document.body.classList.remove('ba-checkout-modal-opened');
            document.body.style.removeProperty('--checkout-modal-body-scroll');
        }, 300);
    },
    executeMask: function(input, mask){
        let i = 0,
            flag = true,
            value = '';
        for (i = 0; i < input.value.length; i++) {
            flag = input.value[i] == mask[i] || mask[i] == '#' && /\d/.test(input.value[i])
            if (flag) {
                value += input.value[i];
            } else {
                break;
            }
        }
        if (!flag) {
            let subValue = input.value.substr(i),
                match = subValue.match(/\d+/),
                ind = 0;
            for (i; i < mask.length; i++) {
                if (mask[i] != '#') {
                    value += mask[i];
                } else if (match && match[0][ind]) {
                    value += match[0][ind++];
                } else {
                    break;
                }
            }
            input.value = value;
        }
    },
    getModalHTML: function(className, title, body){
        let div = document.createElement('div');
        div.innerHTML = '<div class="ba-checkout-form-modal-backdrop ba-close-checkout-modal"></div>'+
            '<div class="ba-checkout-form-modal">'+
            '<div class="ba-checkout-form-modal-header"><span class="ba-checkout-form-modal-title">'+title+'</span>'+
            '<i class="ba-icons ba-icon-close ba-close-checkout-modal"></i></div>'+
            '<div class="ba-checkout-form-modal-body">'+body+'</div>'+
            '</div>';
        div.className = 'ba-checkout-form-modal-wrapper ba-checkout-form-'+className+'-modal';
        document.body.append(div);
        app.checkoutForm.addModalEvents(div);

        return div;
    },
    addModalEvents: function(div){
        div.querySelectorAll('.ba-close-checkout-modal').forEach(function($this){
            $this.addEventListener('click', function(){
                app.checkoutForm.hideModal(div);
            });
        });
    },
    placeOrder: function($this){
        app.checkoutForm.btn = $this;
        $this.submited = true;
        let data = app.checkoutForm.pickData();
        $this.classList.add('ba-checkout-btn-animation-in');
        app.checkoutForm.createOrder(data).then(function(obj){
            if (obj.denied) {
                return false;
            }
            app.checkoutForm[obj.payment.type+'Payment'](obj);
        });
    },
    monoPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitMono&'+(+new Date());
    },
    liqpayPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitLiqpay&'+(+new Date());
    },
    squarePayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitSquare&'+(+new Date());
    },
    barionPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitBarion&'+(+new Date());
    },
    dotpayPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitDotpay&'+(+new Date());
    },
    payfastPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitPayfast&'+(+new Date());
    },
    molliePayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitMollie&'+(+new Date());
    },
    robokassaPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitRobokassa&'+(+new Date());
    },
    payuplPayment: function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitPayupl&'+(+new Date());
    },
    pagseguroPayment: function(obj){
        if (!('PagSeguroLightbox' in window)) {
            let script = document.createElement('script'),
                url = obj.payment.environment == 'sandbox' ? 'sandbox.' : '';
            script.onload = function(){
                app.checkoutForm.pagseguroPayment(obj);
            }
            script.src = 'https://stc.'+url+'pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js';
            document.head.append(script);
        } else {
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.submitPagseguro').then(function(text){
                let response = {};
                try {
                    response = JSON.parse(text);
                } catch (e) {
                    response = {
                        error: text
                    }
                }
                if (response.status) {
                    var isOpenLightbox = PagSeguroLightbox(response.code[0], {
                        success : function(transactionCode) {
                            app.fetch(JUri+'index.php?option=com_gridbox&task=store.pagseguroCallback', {
                                transactionCode: transactionCode
                            }).then(function(text){
                                window.location.href = text;
                            });
                        },
                    });
                } else {
                    console.info(response.error);
                    app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
                    app.checkoutForm.btn.submited = false;
                }
            });
        }
    },
    klarnaPayment: function(obj){
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.submitKlarna').then(function(text){
            let response = JSON.parse(text);
            if (response.html_snippet) {
                if (!app.checkoutForm.klarna) {
                    let html = '<div id="my-checkout-container"></div>';
                    app.checkoutForm.klarna = app.checkoutForm.getModalHTML('klarna', obj.payment.title, html);
                }
                var checkoutContainer = document.getElementById('my-checkout-container');
                checkoutContainer.innerHTML = response.html_snippet;
                var scriptsTags = checkoutContainer.getElementsByTagName('script');
                for (var i = 0; i < scriptsTags.length; i++) {
                    var parentNode = scriptsTags[i].parentNode,
                        newScriptTag = document.createElement('script');
                    newScriptTag.text = scriptsTags[i].text;
                    parentNode.removeChild(scriptsTags[i]);
                    parentNode.appendChild(newScriptTag);
                }
                app.checkoutForm.showModal(app.checkoutForm.klarna);
            } else {
                console.error(response.error_messages[0]);
            }
            app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
            app.checkoutForm.btn.submited = false;
        })
    },
    'yandex-kassaPayment': function(obj){
        window.location.href = JUri+'index.php?option=com_gridbox&task=store.submitYandexKassa&'+(+new Date());
    },
    twocheckoutPayment:function(obj){
        //window.location.href = JUri+'index.php?option=com_gridbox&task=store.submit2checkout&'+(+new Date());
        if (!window.TwoCoInlineCart) {
            let script = document.createElement('script'),
                title = [],
                currency = obj.currency_code,
                config = {
                    "app":{
                        "merchant": obj.payment.params.account_number,
                        "iframeLoad":"checkout"
                    },
                    "cart":{
                        "host":"https:\/\/secure.2checkout.com",
                        "customization":"inline-one-step"
                    }
                };
            for (let ind in obj.products) {
                title.push(obj.products[ind].title)
            }
            if (obj.shipping) {
                title.push(obj.shipping.title);
            }
            script.src = 'https://secure.avangate.com/checkout/client/twoCoInlineCart.js';
            script.async = true;
            script.onload = function () {
                for (let namespace in config) {
                    if (config.hasOwnProperty(namespace)) {
                        window.TwoCoInlineCart.setup.setConfig(namespace, config[namespace]);
                    }
                }
                window.TwoCoInlineCart.setup.setMode('DYNAMIC');
                window.TwoCoInlineCart.register();
                window.TwoCoInlineCart.cart.setCurrency(currency);
                if (obj.payment.params.environment == "sandbox") {
                    window.TwoCoInlineCart.cart.setTest(true);
                }
                window.TwoCoInlineCart.products.add({
                    name: title.join(', '),
                    quantity: 1,
                    price: obj.total - obj.later,
                });
                app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
                app.checkoutForm.btn.submited = false;
                window.TwoCoInlineCart.events.subscribe('payment:finalized', function(){
                    let data = TwoCoInlineCart.cart.getCheckoutData();
                    app.checkoutForm.updateOrder(obj, data)
                });
                window.TwoCoInlineCart.cart.checkout();
            };
            document.head.append(script);
        } else {
            app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
            app.checkoutForm.btn.submited = false;
            window.TwoCoInlineCart.cart.checkout();
        }
    },
    stripePayment: function(obj){
        if (!('Stripe' in window)) {
            let script = document.createElement('script');
            script.onload = function(){
                app.checkoutForm.stripePayment(obj);
            }
            script.src = "https://js.stripe.com/v3";
            document.head.append(script);
        } else {
            app.checkoutForm.stripe = Stripe(obj.payment.params.api_key);
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.stripeCharges', {
                id: obj.payment.id
            }).then(function(text){
                let object = JSON.parse(text);
                if (object.error) {
                    console.error(object.error.message);
                } else {
                    app.checkoutForm.updateOrder(obj, {'id': object.id}, true).then(function(){
                        app.checkoutForm.stripe.redirectToCheckout({
                            sessionId: object.id
                        }).then(function(result) {
                            console.info(result)
                        });
                    });
                }
            });
        }
    },
    cloudpaymentsPayment:function(obj){
        if (!('cp' in window)) {
            let script = document.createElement('script');
            script.onload = function(){
                app.checkoutForm.cloudpaymentsPayment(obj);
            }
            script.src = "https://widget.cloudpayments.ru/bundles/cloudpayments";
            document.head.append(script);
        } else {
            let title = [],
                currency = obj.currency_code,
                widget = new cp.CloudPayments(),
                invoiceId = +(new Date());
            for (let ind in obj.products) {
                title.push(obj.products[ind].title)
            }
            if (obj.shipping) {
                title.push(obj.shipping.title);
            }
            app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
            widget.charge({
                publicId: obj.payment.params.public_id,
                description: title.join(', '),
                amount: obj.total - obj.later,
                invoiceId: invoiceId,
                currency: currency,
                skin: 'modern'
            }, function(options){
                app.checkoutForm.updateOrder(obj, options);
            }, function(reason, options){
                app.checkoutForm.btn.submited = false;
            });
        }
    },
    offlinePayment:function(obj){
        window.location.href = obj.url;
    },
    authorizePayment: function(obj){
        let btn = null;
        if (!app.checkoutForm.authorize) {
            let html = '<div class="ba-authorize-field-wrapper">'+
                '<input type="text" class="ba-authorize-card-number" placeholder="Card Number">'+
                '</div><div class="ba-authorize-fields-wrapper"><div class="ba-authorize-field-wrapper">'+
                '<input type="text" class="ba-authorize-expiration-date" placeholder="MM / YYYY">'+
                '</div><div class="ba-authorize-field-wrapper">'+
                '<input type="text" class="ba-authorize-card-code" placeholder="CVC">'+
                '</div></div><div class="ba-authorize-pay-btn">'+
                '<span class="ba-authorize-pay">Pay</span></div>',
                div = null;
            app.checkoutForm.authorize = div = app.checkoutForm.getModalHTML('authorize', obj.payment.title, html);
            btn = div.querySelector('.ba-authorize-pay-btn');
            btn.cardNumber = div.querySelector('.ba-authorize-card-number');
            btn.expirationDate = div.querySelector('.ba-authorize-expiration-date');
            btn.cardCode = div.querySelector('.ba-authorize-card-code');
            btn.cardNumber.addEventListener('input', function(){
                let value = this.value.replace(/\s/g, ''),
                    match = value.match(/\d{1,16}/);
                if (!match) {
                    this.value = '';
                } else {
                    let str = '',
                        j = 1;
                    for (let i = 0; i < match[0].length; i++) {
                        if (j == 5) {
                            j = 1;
                            str += ' ';
                        }
                        str += match[0][i];
                        j++;
                    }
                    if (str != this.value) {
                        this.value = str;
                    }
                }
                this.filled = this.value.length >= 14 ? true : false;
            });
            btn.expirationDate.addEventListener('input', function(){
                app.checkoutForm.executeMask(this, '##/####');
                this.filled = this.value.match(/\d\d\/\d{4}/) ? true : false;
            });
            btn.cardCode.addEventListener('input', function(){
                let match = this.value.match(/\d{1,4}/);
                if (!match) {
                    this.value = '';
                } else if (match[0] != this.value) {
                    this.value = match[0];
                }
                this.filled = this.value.length >= 3 ? true : false;
            });
            btn.cardNumber.addEventListener('focus', function(){
                app.checkoutForm.removeAlertTooltip(this);
            });
            btn.expirationDate.addEventListener('focus', function(){
                app.checkoutForm.removeAlertTooltip(this);
            });
            btn.cardCode.addEventListener('focus', function(){
                app.checkoutForm.removeAlertTooltip(this);
            });
            btn.addEventListener('click', function(){
                if (!btn.status && btn.cardNumber.filled && btn.expirationDate.filled && btn.cardCode.filled) {
                    btn.status = 'pending';
                    app.fetch(JUri+'index.php?option=com_gridbox&task=store.payAuthorize', {
                        cardNumber: btn.cardNumber.value,
                        expirationDate: btn.expirationDate.value,
                        cardCode: btn.cardCode.value
                    }).then(function(text){
                        let response = JSON.parse(text),
                            flag = response.transactionResponse.transId != '' && response.transactionResponse.transId != 0;
                        if (flag) {
                            app.checkoutForm.updateOrder(obj, response);
                        } else {
                            app.showNotice(response.messages.message[0].text, 'ba-alert');
                            btn.status = false;
                        }
                    })
                } else if (!btn.status) {
                    ['cardNumber', 'expirationDate', 'cardCode'].forEach(function(el){
                        if (!btn[el].filled) {
                            let key = btn[el].value ? 'ENTER_VALID_VALUE' : 'THIS_FIELD_REQUIRED';
                            app.checkoutForm.toggleAlertTooltip(true, btn[el], btn[el].closest('.ba-authorize-field-wrapper'), key);
                        }
                    });
                }
            });
        }
        btn = app.checkoutForm.authorize.querySelector('.ba-authorize-pay-btn');
        let price = document.querySelector('.ba-checkout-order-form-total .ba-checkout-order-price-wrapper').cloneNode(true);
        btn.querySelectorAll('.ba-checkout-order-price-wrapper').forEach(function(span){
            span.remove();
        });
        btn.append(price);
        app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
        app.checkoutForm.showModal(app.checkoutForm.authorize);
        app.checkoutForm.btn.submited = false;
    },
    paypalPayment: function(obj){
        if (!app.checkoutForm.paypal) {
            let html = '<div id="paypal-buttons-wrapper"></div>';
            app.checkoutForm.paypal = app.checkoutForm.getModalHTML('paypal', obj.payment.title, html);
        }
        let currency = obj.currency_code;
        if (!('paypal' in window)) {
            let script = document.createElement('script');
            script.onload = function(){
                app.checkoutForm.paypalPayment(obj);
            }
            script.src = 'https://www.paypal.com/sdk/js?client-id='+obj.payment.params.client_id+'&currency='+currency;
            document.head.append(script);
        } else {
            let total = app.decimalAdjust('round', obj.total - obj.later, -2),
                names = [];
            for (let i in obj.products) {
                names.push(obj.products[i].title);
            }            
            if (obj.shipping) {
                names.push(obj.shipping.title);
            }
            $g('#paypal-buttons-wrapper').html('');
            paypal.Buttons({
                createOrder: function(data, actions){
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                currency: currency,
                                breakdown: {
                                    item_total: {
                                        currency_code: currency,
                                        value: total
                                    }
                                },
                                value: total
                            },
                            items: [{
                                name: names.join(', ').substr(0, 127),
                                unit_amount: {
                                    currency_code: currency,
                                    value: total,
                                },
                                quantity: 1
                            }],
                        }]
                    });
                },
                onApprove: function(data, actions){
                    return actions.order.capture().then(function(details){
                        app.checkoutForm.btn.submited = true;
                        app.checkoutForm.hideModal(app.checkoutForm.paypal);
                        app.checkoutForm.updateOrder(obj, details);
                    });
                }
            }).render('#paypal-buttons-wrapper').then(function(){
                app.checkoutForm.btn.classList.remove('ba-checkout-btn-animation-in');
                app.checkoutForm.showModal(app.checkoutForm.paypal);
                app.checkoutForm.btn.submited = false;
            });
        }
    },
    updateOrder: async function(obj, params, flag){
        return new Promise(function(resolve, reject) {
            let methods = ['paypal', 'cloudpayments', 'authorize'];
            if (methods.indexOf(app.checkoutForm.payment) != -1) {
                app.checkoutForm.btn.classList.add('ba-checkout-btn-animation-in');
            }
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.updateOrder', {
                params: JSON.stringify(params)
            }).then(function(text){
                if (!flag) {
                    window.location.href = obj.url;
                }
                resolve(text);
            });
        });
    },
    createOrder: function(data){
        return new Promise(function(resolve, reject) {
            app.fetch(JUri+'index.php?option=com_gridbox&task=store.createOrder', data).then(function(text){
                let obj = JSON.parse(text);
                if (obj.payment) {
                    obj.payment.params = JSON.parse(obj.payment.settings);
                }
                app.fetch(JUri+'index.php?option=com_gridbox&task=store.getPaymentOptions', {
                    payment: data.payment
                }).then(function(text){
                    obj.payment = JSON.parse(text)
                    obj.payment.params = JSON.parse(obj.payment.settings);
                    resolve(obj);
                });
            });
        });
    },
    pickData: function(){
        let data = {
                checkout_id: themeData.id,
                shipping: 0,
                payment: 0
            },
            wrapper = $g('.ba-checkout-form-wrapper');
        wrapper.find('.ba-checkout-form-fields').each(function(){
            data[this.dataset.name] = '';
            if (["checkbox", "radio", "acceptance"].indexOf(this.dataset.type) != -1) {
                let values = [];
                this.querySelectorAll('input').forEach(function(input){
                    if (input.checked) {
                        values.push(input.value);
                    }
                });
                data[this.dataset.name] = values.join('; ');
            } else if (this.dataset.type == 'country') {
                data[this.dataset.name] = this.querySelector('input[type="hidden"]').value;
            } else if (this.dataset.type != 'headline') {
                data[this.dataset.name] = this.querySelector('input, textarea, select').value;
            }
        });
        $g('.ba-checkout-order-form-shipping-wrapper input[type="radio"]').each(function(){
            if (this.checked) {
                data.shipping = this.value;
                return false;
            }
        });
        $g('.ba-checkout-order-form-payments-wrapper input[type="radio"]').each(function(){
            if (this.checked) {
                data.payment = this.value;
                app.checkoutForm.payment = this.dataset.type;
                return false;
            }
        });
        $g('.inpost-selected-address').each(function(){
            if (this.closest('.ba-checkout-order-form-shipping.selected')) {
                data.carrier = this.textContent.trim();
            }
        })

        return data;
    },
    toggleAlertTooltip: function(alert, $this, parent, key){
        if (alert && !$this.alertTooltip) {
            $this.alertTooltip = document.createElement('span');
            $this.alertTooltip.className = 'ba-checkout-alert-tooltip';
            $this.alertTooltip.textContent = gridboxLanguage[key];
            parent.classList.add('ba-checkout-alert');
            parent.appendChild($this.alertTooltip);
        } else if (alert && $this.alertTooltip) {
            $this.alertTooltip.textContent = gridboxLanguage[key];
        } else if (!alert && $this.alertTooltip) {
            app.checkoutForm.removeAlertTooltip($this);
        }
    },
    removeAlertTooltip: function($this){
        if ($this.alertTooltip) {
            $this.alertTooltip.remove();
            $this.alertTooltip = null;
            $this.closest('.ba-checkout-alert').classList.remove('ba-checkout-alert');
        }
    }
}

if (app.modules.initCheckoutOrderForm) {
    app.initCheckoutOrderForm(app.modules.initCheckoutOrderForm.data, app.modules.initCheckoutOrderForm.selector);
}