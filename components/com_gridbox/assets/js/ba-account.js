/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

document.addEventListener('DOMContentLoaded', function(){

    let countries = null,
        templates = {
            data: {},
            set: function($this){
                this.data[$this.dataset.key] = $this;
            },
            get: function(key){
                let content = this.data[key].content.cloneNode(true);

                return content;
            }
        },
        alertTooltip = {
            toggle: function(alert, $this, parent, key){
                if (alert && !$this.alertTooltip) {
                    $this.alertTooltip = document.createElement('span');
                    $this.alertTooltip.className = 'ba-account-alert-tooltip';
                    $this.alertTooltip.textContent = app._(key);
                    parent.classList.add('ba-account-alert');
                    parent.append($this.alertTooltip);
                } else if (alert && $this.alertTooltip) {
                    $this.alertTooltip.textContent = app._(key);
                } else if (!alert && $this.alertTooltip) {
                    this.remove($this);
                }
            },
            remove: function($this){
                if ($this.alertTooltip) {
                    $this.alertTooltip.remove();
                    $this.alertTooltip = null;
                    $this.closest('.ba-account-alert').classList.remove('ba-account-alert');
                }
            },
            test: function($this, data){
                let alert = false,
                    key = 'THIS_FIELD_REQUIRED',
                    name = $this.name,
                    value = data[name];
                if ((name == 'name' || name == 'email1') && !value) {
                    alert = true;
                    alertTooltip.toggle(true, $this, $this.closest('.ba-account-profile-fields'), 'THIS_FIELD_REQUIRED');
                } else if ((name == 'password1' || name == 'password2') && data.password1 != data.password2) {
                    alert = true;
                    key = 'PASSWORDS_ENTERED_NOT_MATCH';
                } else if ($this.required && $this.type != 'email' && !value) {
                    alert = true;
                    alertTooltip.toggle(true, $this, $this.closest('.ba-checkout-form-fields'), 'THIS_FIELD_REQUIRED');
                } else if ($this.required && $this.type == 'email'
                    && !(/@/g.test(value) && value.match(/@/g).length == 1)) {
                    alert = true;
                    alertTooltip.toggle(true, $this, $this.closest('.ba-checkout-form-fields'), 'THIS_FIELD_REQUIRED');
                }
                if (alert) {
                    alertTooltip.toggle(alert, $this, $this.closest('.ba-account-profile-fields'), key);
                }

                return alert;
            }
        }

    app.imageTypes = ['jpg', 'png', 'gif', 'svg', 'jpeg', 'ico', 'webp'];
    app.getExt = function(name){
        let array = name.split('.');
        
        return array[array.length - 1].toLowerCase();
    };
    app.isImage = function(name){
        return this.imageTypes.indexOf(this.getExt(name)) != -1;
    };
    app.fileUploader = {
        createFile: () => {
            let input = document.createElement('input');
            input.type = 'file';
            input.onchange = app.fileUploader.uploadFiles;
            app.fileUploader.input = input;
            input.style.display = 'none';
            document.body.append(input);
            setTimeout(function(){
                input.click();
            }, 100);
        },
        uploadFiles: () => {
            let files = [].slice.call(app.fileUploader.input.files),
                flag = true,
                msg = '';
            for (let i = 0; i < files.length; i++) {
                let name = files[i].name.split('.'),
                    ext = name[name.length - 1].toLowerCase();
                if (app.imageTypes.indexOf(ext) == -1) {
                    flag = false;
                    app.showNotice(app._('NOT_SUPPORTED_FILE'), 'ba-alert');
                    break;
                }
            }
            if (!flag) {
                return;
            }
            if (!app.notification) {
                app.loadNotice().then(() => {
                    app.fileUploader.startUpload(files);
                });
            } else {
                app.fileUploader.startUpload(files);
            }
        },
        startUpload: (files) => {
            let str = '<span>'+app._('UPLOADING_MEDIA')+'</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
            app.notification.querySelector('p').innerHTML = str;
            app.notification.className = 'notification-in';
            app.fileUploader.uploadFile(files);
        },
        uploadFile: (files) => {
            let xhr = new XMLHttpRequest(),
                file = files.shift(),
                formData = new FormData();
            formData.append('file', file);
            xhr.onload = xhr.onerror = function(){
                let obj = JSON.parse(xhr.responseText);
                if (!obj.error) {
                    app.fileUploader.afterUpload(obj);
                } else if (obj.error && !files.length) {
                    app.showNotice(obj.msg, 'ba-alert');
                }
                if (files.length) {
                    app.fileUploader.uploadFile(files)
                } else if (!obj.error) {
                    setTimeout(function(){
                        app.notification.className = 'animation-out';
                    }, 2000);
                }
            };
            xhr.open("POST", JUri+"index.php?option=com_gridbox&task=account.uploadProfileImage", true);
            xhr.send(formData);
        },
        afterUpload: (obj) => {
            app.fileUploader.btn.dataset.value = obj.path;
            app.fileUploader.btn.value = obj.filename;
            app.fileUploader.btn.closest('.image-profile-wrapper').querySelector('.image-field-tooltip').style.backgroundImage = 'url('+JUri+obj.path+')';
        }
    }

    function setCartRegions(wrapper, value, $default)
    {
        let html = '';
        countries.forEach(function(country){
            if (country.id == value) {
                country.states.forEach(function(region){
                    html += '<option value="'+region.id+'"'+($default == region.id ? ' selected' : '')+'>'+region.title+'</option>';
                });
            }
        });
        if (html) {
            wrapper.classList.add('visible-region-select');
        } else {
            wrapper.classList.remove('visible-region-select');
        }
        wrapper.querySelector('select[data-type="region"]').innerHTML = html;
    }

    function setCartCountry()
    {
        let div = $g('.ba-checkout-form-fields[data-type="country"]'),
            data = {};
        div.find('select').each(function(){
            data[this.dataset.type] = this.value;
        });
        div.find('input[type="hidden"]').val(JSON.stringify(data)).trigger('input');
    }

    function setDetailsPrice(div, price, symbol, position)
    {
        div.querySelector('.ba-cart-price-value').textContent = price;
        div.querySelector('.ba-account-order-price-currency').textContent = symbol;
        if (position) {
            div.classList.add(position);
        }
    }

    function checkEmptyWishlist()
    {
        if (!document.querySelector('#ba-my-account-wishlist .ba-wishlist-product-row')) {
            let html = '<div class="ba-empty-cart-products"><i class="ba-icons ba-icon-heart"></i>'
                +'<span class="ba-empty-cart-products-message">'+app._('EMPTY_WISHLIST')+'</span></div>';
            document.querySelector('#ba-my-account-wishlist .ba-wishlist-products-list').innerHTML = html;
        }
    }

    function renewSubscription(id, renew_id, plan_key)
    {
        let url = document.querySelector('#ba-my-account-subscriptions').dataset.checkout;
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.addProductToCart', {
            id: id,
            renew_id: renew_id,
            plan_key: plan_key,
            quantity: 1
        }).then(function(text){
            window.location.href = url;
        });
    }

    function showAccountModal(modal)
    {
        let scroll = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.setProperty('--body-scroll-width', scroll+'px');
        document.body.classList.add('ba-visible-account-modal');
        modal.removeClass('ba-hidden-account-modal');
    }

    $g('template').each(function(){
        templates.set(this);
    });
    $g('#ba-my-account-subscriptions').on('click', '.ba-upgrade-subscription', function(){
        if (!this.plans) {
            let text = this.closest('div').querySelector('template').content.textContent;
            this.plans = JSON.parse(text);
        }
        let modal = $g('.ba-account-modal-backdrop[data-type="upgrade"]'),
            body = modal.find('.ba-account-modal-body').empty(),
            id = this.dataset.id,
            content = element = price = null;
        this.plans.forEach(function(plan, i){
            content = templates.get('account-modal-row');
            element = content.querySelector('.ba-account-order-price-wrapper');
            if (plan.price < 0) {
                element.querySelector('.ba-cart-price-value').textContent = app._('FREE');
                element.querySelector('.ba-account-order-price-currency').remove();
            } else {
                price = app.renderPrice(plan.price, currency.thousand, currency.separator, currency.decimals);
                setDetailsPrice(element, price, currency.symbol, currency.position);
            }
            if (plan.additional) {
                let span = document.createElement('span');
                span.className = 'ba-account-modal-row-additional';
                span.textContent = '+ '+app._('ADDITIONAL')+' '+plan.additional+' '+app._('DAYS');
                content.querySelector('.ba-account-modal-row-title').append(span);
            }
            if (plan.price > 0 && plan.prices.sale_price) {
                let clone = element.cloneNode(true);
                element.dataset.price = 'sale';
                price = app.renderPrice(plan.prices.sale_price,currency.thousand,currency.separator,currency.decimals);
                setDetailsPrice(clone, price, currency.symbol, currency.position);
                element.parentNode.insertBefore(clone, element);
            }
            element = content.querySelector('.ba-account-modal-row-title-value');
            element.textContent = plan.title;
            element = content.querySelector('input[type="radio"]');
            element.checked = i == 0;
            element.dataset.id = id;
            element.dataset.product = plan.id;
            body.append(content);
        });
        showAccountModal(modal);
    });
    $g('#ba-my-account-subscriptions').on('click', '.ba-renew-subscription', function(){
        if (!this.plans) {
            let text = this.closest('div').querySelector('template').content.textContent;
            this.plans = JSON.parse(text);
        }
        let product = this.dataset.product,
            id = this.dataset.id;
        if (this.plans.length > 1) {
            let modal = $g('.ba-account-modal-backdrop[data-type="renewal"]'),
                body = modal.find('.ba-account-modal-body').empty(),
                expires = {h: app._('HOURS'), d: app._('DAYS'), m: app._('MONTHS'), y: app._('YEARS')},
                content = element = price = null;
            this.plans.forEach(function(plan, i){
                price = app.renderPrice(plan.price, currency.thousand, currency.separator, currency.decimals);
                content = templates.get('account-modal-row');
                element = content.querySelector('.ba-account-order-price-wrapper');
                setDetailsPrice(element, price, currency.symbol, currency.position);
                if (plan.prices.sale_price) {
                    let clone = element.cloneNode(true);
                    element.dataset.price = 'sale';
                    price = app.renderPrice(plan.prices.sale_price,currency.thousand,currency.separator,currency.decimals);
                    setDetailsPrice(clone, price, currency.symbol, currency.position);
                    element.parentNode.insertBefore(clone, element);
                }
                element = content.querySelector('.ba-account-modal-row-title-value');
                element.textContent = plan.length.value+' '+expires[plan.length.format];
                element = content.querySelector('input[type="radio"]');
                element.checked = i == 0;
                element.dataset.key = plan.key;
                element.dataset.product = product;
                element.dataset.id = id;
                body.append(content);
            });
            showAccountModal(modal);
        } else {
            renewSubscription(product, id, this.plans[0].key);
        }
    });
    $g('.ba-account-modal-backdrop').on('click', function(){
        this.classList.add('ba-hidden-account-modal');
        document.body.classList.remove('ba-visible-account-modal');
    }).find('.ba-account-modal-close').on('click', function(){
        $g(this).closest('.ba-account-modal-backdrop').trigger('click');
    });
    $g('.ba-account-md-modal-wrapper').on('click', function(event){
        event.stopPropagation();
    });
    $g('.ba-subscription-renewal-btn').on('click', function(){
        let plan = null;
        this.closest('.ba-account-modal-backdrop').querySelectorAll('input[type="radio"]').forEach(function(input){
            if (input.checked) {
                plan = input;
            }
        });
        renewSubscription(plan.dataset.product, plan.dataset.id, plan.dataset.key);
    });
    $g('.ba-subscription-upgrade-btn').on('click', function(){
        let plan = null,
            url = document.querySelector('#ba-my-account-subscriptions').dataset.checkout;
        this.closest('.ba-account-modal-backdrop').querySelectorAll('input[type="radio"]').forEach(function(input){
            if (input.checked) {
                plan = input;
            }
        });
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.upgradePlan', {
            id: plan.dataset.product,
            upgrade_id: plan.dataset.id,
            quantity: 1
        }).then(function(text){
            if (text == 'upgraded') {
                $g('.ba-account-modal-backdrop').trigger('click');
                app.showNotice(app._('PLAN_CHANGED_SUCCESSFULLY'));
                fetch(window.location.href).then(function(response){
                    return response.text()
                }).then(function(text){
                    let div = document.createElement('div'),
                        query = '#ba-my-account-subscriptions .ba-account-table';
                    div.innerHTML = text;
                    document.querySelector(query).innerHTML = div.querySelector(query).innerHTML;
                    query = '#ba-my-account-downloads .ba-account-table';
                    document.querySelector(query).innerHTML = div.querySelector(query).innerHTML;
                })
            } else {
                window.location.href = url;
            }
        });
    });
    $g('#ba-my-account-profile input, #ba-my-account-billing-details [name]').on('focus', function(){
        alertTooltip.remove(this);
    });
    $g('.save-user-profile-data').on('click', function(){
        let data = {},
            alert = false;
        $g('#ba-my-account-profile [name]').each(function(){
            data[this.name] = this.value.trim();
            if (this.name == 'image') {
                data[this.name] = this.dataset.value;
            }
        }).each(function(){
            let flag = alertTooltip.test(this, data);
            if (!alert) {
                alert = flag;
            }
        });
        if (alert) {
            return false;
        }
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.saveUserProfile', data).then(function(text){
            app.showNotice(app._('GRIDBOX_SAVED'));
        });
    });
    $g('.ba-wishlist-product-remove-extra-option i').on('click', function(){
        let extra = this.closest('.ba-wishlist-product-extra-option'),
            options = extra.closest('.ba-wishlist-product-extra-options');
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeExtraOptionWishlist', {
            id: extra.closest('.ba-wishlist-product-row').dataset.id,
            key: extra.dataset.key,
            field_id: extra.dataset.id,
        }).then(function(text){
            if (app.wishlist) {
                app.wishlist.updateWishlist();
            }
        });
        extra.remove();
        if (!options.querySelector('.ba-wishlist-product-extra-option')) {
            options.remove();
        }
    });
    $g('.ba-wishlist-add-to-cart-btn').on('click', function(){
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
            if (app.wishlist) {
                app.wishlist.setWishlistHTML(html);
            }
            if (app.storeCart) {
                app.storeCart.updateCartTotal();
            }
        });
        row.remove();
        checkEmptyWishlist();
    });
    $g('.ba-wishlist-product-remove-cell i').on('click', function(){
        let row = this.closest('.ba-wishlist-product-row');
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.removeProductFromWishlist', {
            id: row.dataset.id,
            view: themeData.page.view
        }).then(function(html){
            if (app.wishlist) {
                app.wishlist.setWishlistHTML(html);
            }
        });
        row.remove();
        checkEmptyWishlist();
    });
    $g('#ba-my-account-orders .ba-account-tr[data-id]').on('click', function(){
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=account.getOrder', {
            id: this.dataset.id
        }).then(function(text){
            let order = JSON.parse(text),
                paid = 0,
                left = 0,
                hasBooking = false,
                taxes = {
                    count: 0
                },
                content = null,
                status = statuses[order.status] ? statuses[order.status] : statuses.undefined,
                modal = $g('.ba-account-order-details-backdrop'),
                scroll = window.innerWidth - document.documentElement.clientWidth;
            console.info(order)
            modal.find('.ba-account-order-number').text(order.order_number);
            modal.find('.ba-account-order-status').text(status.title)[0].style.setProperty('--status-color', status.color);
            if (status.key != 'completed') {
                modal.find('.ba-account-order-icons-wrapper').hide();
            } else {
                modal.find('.ba-account-order-icons-wrapper').css('display', '').find('i[data-layout]').attr('data-id', order.id);
            }
            modal.find('.ba-account-order-date').text(order.date);
            modal.find('.ba-account-order-body').html('');
            order.products.forEach(function(product){
                let div = null,
                    extraCount = product.extra_options.count ? product.extra_options.count : 0,
                    amount = product.sale_price != '' ? product.sale_price : product.price,
                    price = app.renderPrice(amount, currency.thousand, currency.separator, currency.decimals);
                content = templates.get('product-row');
                if (product.image) {
                    let src = (!app.isExternal(product.image) ? JUri : '')+product.image;
                    content.querySelector('.ba-account-order-product-image-cell img').src = src;
                } else {
                    content.querySelector('.ba-account-order-product-image-cell').remove();
                }
                content.querySelector('.ba-account-order-product-title').textContent = product.title;
                if (product.info) {
                    content.querySelector('.ba-account-order-product-info').innerHTML = product.info;
                } else {
                    content.querySelector('.ba-account-order-product-info').remove();
                }
                if (product.product_type == 'booking') {
                    hasBooking = true;
                    paid += product.booking.paid == 1 ? product.booking.price * 1 : product.booking.prepaid * 1;
                    left += product.booking.paid == 1 ? 0 : product.booking.later * 1;
                    let text = app._('DATE') + ': '+product.booking.formated.start_date + 
                        (product.booking.end_date ? ' - ' + product.booking.formated.end_date : '');
                    content.querySelector('.ba-account-order-product-booking-date').textContent = text;
                    text = product.booking.start_time ? app._('TIME') + ': ' + product.booking.start_time : '';
                    content.querySelector('.ba-account-order-product-booking-time').textContent = text;
                    text = product.booking.guests ? app._('GUESTS') + ': ' + product.booking.guests : '';
                    content.querySelector('.ba-account-order-product-booking-guests').textContent = text;
                } else {
                    content.querySelector('.ba-account-order-product-booking').remove();
                }
                if (product.product_type == 'digital' || product.product_type == 'booking') {
                    content.querySelector('.ba-account-order-product-quantity-cell').remove();
                } else {
                    content.querySelector('.ba-account-order-product-quantity-cell').textContent = ' x '+product.quantity;
                }
                div = content.querySelector('.ba-account-order-product-price-cell .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                content.querySelector('.ba-account-order-product-row').dataset.extraCount = extraCount;
                if (product.extra_options.items) {
                    for (let ind in product.extra_options.items) {
                        let extra = product.extra_options.items[ind],
                            options = templates.get('extra-options');
                        options.querySelector('.ba-account-order-product-extra-options-title').textContent = extra.title;
                        for (let key in extra.values) {
                            let option = templates.get('extra-option'),
                                obj = extra.values[key];
                            option.querySelector('.ba-account-order-product-extra-option-value').textContent = obj.value;
                            if (obj.price != '') {
                                amount = obj.price * product.quantity;
                                price = app.renderPrice(amount, currency.thousand, currency.separator, currency.decimals);
                                div = option.querySelector('.ba-account-order-product-extra-option-price');
                                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                            } else {
                                option.querySelector('.ba-account-order-product-extra-option-price').remove();
                            }
                            options.querySelector('.ba-account-order-product-extra-options-content').append(option);
                        }
                        if (extra.attachments) {
                            extra.attachments.forEach((file) => {
                                let attachment = templates.get('product-attachment');
                                if (!app.isImage(file.name)) {
                                    attachment.querySelector('.attachment-image').remove();
                                } else {
                                    let src = JUri+'components/com_gridbox/assets/uploads/attachments/'+file.filename;
                                    attachment.querySelector('i').remove();
                                    attachment.querySelector('.attachment-image').style.backgroundImage = 'url('+src+')';
                                }
                                attachment.querySelector('.attachment-title').textContent = file.name;
                                options.querySelector('.ba-account-order-product-extra-options-content').append(attachment);
                            });
                        }
                        content.querySelector('.ba-account-order-product-content-cell').append(options)
                    }
                }
                modal.find('.ba-account-order-info .ba-account-order-body').append(content);
                if (product.tax) {
                    let exist = false,
                        key = 0;
                    price = product.sale_price ? product.sale_price : product.price;
                    for (let ind in taxes) {
                        if (ind == 'count') {
                            continue;
                        }
                        if (taxes[ind].title == product.tax_title && taxes[ind].rate == product.tax_rate) {
                            taxes[ind].amount += product.tax * 1;
                            exist = true;
                            break;
                        }
                        key++;
                    }
                    if (!exist) {
                        taxes.count++;
                        taxes[key] = {
                            amount: product.tax * 1,
                            title: product.tax_title,
                            rate: product.tax_rate
                        }
                    }
                }
            });
            content = templates.get('order-methods');
            if (order.shipping) {
                content.querySelector('.ba-account-order-shipping-method .ba-account-order-row-value').textContent = order.shipping.title;
            } else {
                content.querySelector('.ba-account-order-shipping-method').remove();
            }
            if (hasBooking) {
                price = app.renderPrice(paid, currency.thousand, currency.separator, currency.decimals);
                content.querySelector('.ba-account-order-payment-method').remove();
                div = content.querySelector('.ba-account-booking-payment-row[data-type="paid"] .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                price = app.renderPrice(left, currency.thousand, currency.separator, currency.decimals);
                div = content.querySelector('.ba-account-booking-payment-row[data-type="left"] .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            } else if (order.payment) {
                content.querySelector('.ba-account-order-payment-method .ba-account-order-row-value').textContent = order.payment.title;
                content.querySelector('.ba-account-booking-payment-method').remove();
            } else {
                content.querySelector('.ba-account-order-payment-method').remove();
                content.querySelector('.ba-account-booking-payment-method').remove();
            }
            if (order.promo) {
                content.querySelector('.ba-account-order-coupon-code .ba-account-order-row-value').textContent = order.promo.title;
            } else {
                content.querySelector('.ba-account-order-coupon-code').remove();
            }
            if (order.shipping || order.payment || order.promo) {
                modal.find('.ba-account-order-info .ba-account-order-body').append(content);
            }
            content = templates.get('subtotal');
            price = app.renderPrice(order.subtotal, currency.thousand, currency.separator, currency.decimals);
            div = content.querySelector('.ba-account-order-subtotal .ba-account-order-price-wrapper');
            setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            if (order.promo) {
                price = app.renderPrice(order.promo.value, currency.thousand, currency.separator, currency.decimals);
                div = content.querySelector('.ba-account-order-discount .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            } else {
                content.querySelector('.ba-account-order-discount').remove();
            }
            if (order.shipping) {
                price = app.renderPrice(order.shipping.price, currency.thousand, currency.separator, currency.decimals);
                div = content.querySelector('.ba-account-order-shipping .ba-account-order-price-wrapper');
                if (order.shipping.type == 'free' || order.shipping.type == 'pickup') {
                    setDetailsPrice(div, app._('FREE'), '', '');
                } else {
                    setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                }
                amount = order.shipping.tax ? order.shipping.tax : 0;
                price = app.renderPrice(amount, currency.thousand, currency.separator, currency.decimals);
                if (order.shipping.tax && order.shipping.tax != 0 && order.tax_mode == 'incl') {
                    content.querySelector('.ba-account-order-shipping-tax[data-tax="excl"]').remove();
                    let title = app._('INCLUDES')+' '+order.shipping.tax_title;
                    content.querySelector('.ba-account-tax-title').textContent = title;
                    if (taxes.count == 1) {
                        for (let ind in taxes) {
                            if (ind == 'count') {
                                continue;
                            }
                            if (taxes[ind].title != order.shipping.tax_title || taxes[0].rate != order.shipping.tax_rate) {
                                taxes.count++;
                            }
                        }
                    }
                } else if (order.shipping.tax && order.shipping.tax != 0 && order.tax_mode != 'incl') {
                    content.querySelector('.ba-account-order-shipping-tax[data-tax="incl"]').remove();
                } else {
                    content.querySelector('.ba-account-order-shipping-tax[data-tax="incl"]').remove();
                    content.querySelector('.ba-account-order-shipping-tax[data-tax="excl"]').remove();
                }
                if (order.shipping.tax && order.shipping.tax != 0) {
                    div = content.querySelector('.ba-account-order-shipping-tax .ba-account-order-price-wrapper');
                    setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                }
            } else {
                content.querySelectorAll('.ba-account-order-shipping, .ba-account-order-shipping-tax').forEach(function(div){
                    div.remove();
                });
            }
            if (!order.tax || order.tax == 0 || order.tax_mode == 'incl') {
                content.querySelector('.ba-account-order-tax').remove()
            } else if (order.tax && order.tax != 0 && order.tax_mode != 'incl' && taxes.count != 0) {
                let taxElement = content.querySelector('.ba-account-order-tax'),
                    clone = null;
                taxElement.remove();
                for (let ind in taxes) {
                    if (ind == 'count') {
                        continue;
                    }
                    clone = taxElement.cloneNode(true);
                    clone.querySelector('.ba-account-order-row-title').textContent = taxes[ind].title;
                    price = app.renderPrice(taxes[ind].amount, currency.thousand, currency.separator, currency.decimals);
                    div = clone.querySelector('.ba-account-order-price-wrapper');
                    setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
                    content.querySelector('.ba-account-order-subtotal-wrapper').append(clone);
                }
            } else if (order.tax && order.tax != 0 && order.tax_mode != 'incl') {
                price = app.renderPrice(order.tax, currency.thousand, currency.separator, currency.decimals);
                div = content.querySelector('.ba-account-order-tax .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            }
            modal.find('.ba-account-order-info .ba-account-order-body').append(content);
            content = templates.get('total');
            price = app.renderPrice(order.total, currency.thousand, currency.separator, currency.decimals);
            div = content.querySelector('.ba-account-order-total .ba-account-order-price-wrapper');
            setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            if (!order.tax || order.tax == 0 || order.tax_mode != 'incl') {
                content.querySelector('.ba-account-order-tax').remove();
            } else if (order.tax && order.tax != 0) {
                let title = taxes.count == 1 ? app._('INCLUDES')+' '+taxes[0].rate+'% '+taxes[0].title : app._('INCLUDING_TAXES');
                content.querySelector('.ba-account-order-tax .ba-account-tax-title').textContent = title;
                price = app.renderPrice(order.tax, currency.thousand, currency.separator, currency.decimals);
                div = content.querySelector('.ba-account-order-tax .ba-account-order-price-wrapper');
                setDetailsPrice(div, price, order.currency_symbol, order.currency_position);
            }
            modal.find('.ba-account-order-info .ba-account-order-body').append(content);
            let customer_info = [];
            customer.forEach(function(info){
                customer_info = [];
                info.items.forEach(function(id){
                    for (let i = 0; i < order.info.length; i++) {
                        if (id == order.info[i].customer_id) {
                            content = templates.get('info-row');
                            let obj = order.info[i],
                                title = obj.title ? obj.title+': ' : '',
                                value = obj.value;
                            if (obj.type == 'checkbox') {
                                value = value.replace(/; /g, '<br>');
                            } else if (obj.type == 'country' && value) {
                                let object = JSON.parse(value);
                                value = (object.region ? object.region+', ': '')+object.country;
                            }
                            if (value != '') {
                                content.querySelector('.ba-account-order-customer-info-row').textContent = title+value;
                                customer_info.push(content);
                            }
                            order.info.splice(i, 1);
                            break;
                        }
                    }
                });
                if (customer_info.length != 0) {
                    if (info.title) {
                        content = templates.get('info-title');
                        content.querySelector('.ba-account-order-customer-info-title').textContent = info.title;
                        modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                    }
                    customer_info.forEach(function(content){
                        modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                    });
                }
            });
            order.info.forEach(function(obj){
                if (obj.type != 'headline' && obj.type != 'acceptance') {
                    content = templates.get('info-row');
                    let title = obj.title ? obj.title+': ' : '',
                        value = obj.value;
                    if (obj.type == 'checkbox') {
                        value = value.replace(/; /g, '<br>');
                    } else if (obj.type == 'country' && value) {
                        let object = JSON.parse(value);
                        value = (object.region ? object.region+', ': '')+object.country;
                    }
                    if (value != '') {
                        content.querySelector('.ba-account-order-customer-info-row').textContent = title+value;
                        modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                    }
                }
            });
            if (order.tracking.number || order.tracking.url || order.tracking.title) {
                content = templates.get('info-title');
                content.querySelector('.ba-account-order-customer-info-title').textContent = app._('TRACKING_INFO');
                modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                let title = '';
                if (order.tracking.title) {
                    title = app._('CARRIER')+': '+order.tracking.title;
                    content = templates.get('info-row');
                    content.querySelector('.ba-account-order-customer-info-row').innerHTML = title;
                    modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                }
                if (order.tracking.number) {
                    title = app._('TRACKING_NUMBER')+': '+order.tracking.number;
                    content = templates.get('info-row');
                    content.querySelector('.ba-account-order-customer-info-row').textContent = title;
                    modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                }
                if (order.tracking.url) {
                    title = app._('TRACK_SHIPMENT')+': '+'<a href="'+order.tracking.url+
                        '" target="_blank">'+app._('VIEW')+'</a>';
                    content = templates.get('info-row');
                    content.querySelector('.ba-account-order-customer-info-row').innerHTML = title;
                    modal.find('.ba-account-order-customer-info .ba-account-order-body').append(content);
                }
            }
            document.body.style.setProperty('--body-scroll-width', scroll+'px');
            document.body.classList.add('ba-visible-order-details');
            modal.removeClass('ba-hidden-order-details');
        });
    });
    $g('.ba-account-order-details-backdrop').on('click', function(){
        this.classList.add('ba-hidden-order-details');
        document.body.classList.remove('ba-visible-order-details');
    }).find('.ba-account-close-order-details').on('click', function(){
        $g(this).closest('.ba-account-order-details-backdrop').trigger('click');
    });
    $g('.ba-account-order-details-wrapper').on('click', function(event){
        event.stopPropagation();
    });
    $g('.ba-account-order-icon-wrapper i').on('click', function(){
        let iframe = document.createElement('iframe'),
            layout = this.dataset.layout;
        iframe.className = 'download-exist-order-iframe';
        document.body.appendChild(iframe);
        iframe.src = JUri+'index.php?option=com_gridbox&view=account&layout='+layout+'&tmpl=component&id='+this.dataset.id;
        iframe.onload = function(){
            if (layout == 'print') {
                iframe.contentWindow.print();
            }
        }
    });
    $g('.save-user-customer-info').on('click', function(){
        let data = {},
            alert = false;
        $g('#ba-my-account-billing-details [name]').each(function(){
            data[this.name] = this.value.trim();
        }).each(function(){
            let flag = alertTooltip.test(this, data);
            if (!alert) {
                alert = flag;
            }
        });
        if (alert) {
            return false;
        }
        app.fetch(JUri+'index.php?option=com_gridbox&task=account.saveCustomerInfo', data).then(function(text){
            app.showNotice(app._('GRIDBOX_SAVED'));
        });
    });
    $g('.ba-checkout-form-fields[data-type="country"]').on('change', 'select[data-type="country"]', function(){
        let wrapper = this.closest('.ba-checkout-form-field-wrapper');
        setCartRegions(wrapper, this.value);
        setCartCountry();
    }).on('change', 'select[data-type="region"]', function(){
        setCartCountry();
    });
    if (document.querySelector('.ba-checkout-form-fields[data-type="country"]')) {
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.getTaxCountries').then(function(text){
            countries = JSON.parse(text);
            $g('.ba-checkout-form-fields[data-type="country"] .ba-checkout-form-field-wrapper').each(function(){
                let country = this.querySelector('select[data-type="country"]').value,
                    region = this.querySelector('select[data-type="region"]').dataset.selected;
                if (country && region) {
                    setCartRegions(this, country, region);
                }
            });
        });
    }

    $g('.submitted-items-list .delete-item').on('click', function(){
        let id = this.closest('.ba-account-tr').dataset.id;
        $g('#delete-dialog').modal().find('#apply-delete').attr('data-id', id);
    });
    $g('#apply-delete').on('click', function(){
        $g('#delete-dialog').modal('hide');
        let id = this.dataset.id;
        app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=account.deleteSubmitted', {
            id: id
        }).then(function(){
            $g('.submitted-items-list .ba-account-tr[data-id="'+id+'"]').remove();
            app.showNotice(app._('COM_GRIDBOX_N_ITEMS_DELETED'));
        });
    });

    $g('.select-image-profile').on('click', function(){
        app.fileUploader.btn = this;
        app.fileUploader.createFile();
    });
    $g('.reset-image-profile').on('click', function(){
        $g('.select-image-profile').val('').attr('data-value', '').closest('.image-profile-wrapper')
            .find('.image-field-tooltip').css('background-image', '');
    });
});