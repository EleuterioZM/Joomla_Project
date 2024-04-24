/**
* @package   Gridbox template
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

console.log = function(){
    return false;
};

var recaptchaCommentsOnload = function() {
    let str = '.ba-item-comments-box > .ba-comments-box-wrapper > .ba-comment-message-wrapper .ba-comments-captcha-wrapper';
    $g(str).each(function(){
        app.initCommentsRecaptcha(this);
    });
    $g('.ba-login-captcha-wrapper').each(function(){
        app.initLoginRecaptcha(this);
    });
};

var verifyLoginCaptcha = () => {
    document.querySelectorAll('.login-recaptcha').forEach(function($this){
        app[app.login ? 'login' : 'checkoutForm'].removeAlertTooltip($this);
    });
}

if (window.integrations && window.integrations.facebook) {
    window.fbAsyncInit = () => {
        app.facebook.initialize();
    }
}

var $g = jQuery,
    delay = '',
    itemsInit = [],
    app = {
        hash: window.location.hash,
        view : 'desktop',
        modules : {},
        loading : {},
        edit : {},
        items : {},
        isExternal: function(link){
            return link.indexOf('https://') != -1 || link.indexOf('http://') != -1;
        },
        facebook: {
            load: () => {
                app.loading.facebook = true;
                let js = document.createElement('script');
                if (document.getElementById('facebook-jssdk')) return;
                js.id = 'facebook-jssdk';
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                js.crossorigin = 'anonymous';
                document.head.append(js);
            },
            initialize: () => {
                FB.init({
                    appId : window.integrations.facebook,
                    autoLogAppEvents : true,
                    xfbml : true,
                    version : 'v14.0'
                });
            },
            getUserInfo: (response) => {
                return new Promise((resolve, reject) => {
                    if (response.status === 'connected') {
                        FB.api('/'+response.authResponse.userID+'/?fields=id,name,email,picture', 'GET', {}, (fields) => {
                            let data = {
                                    name: fields.name,
                                    email: fields.email,
                                    avatar: fields.picture.data.url,
                                    id: fields.id
                                }
                            resolve(data);
                        });
                    }
                });
            }
        },
        google: {
            load: (callback, selector, options) => {
                let js = document.createElement('script');
                js.src = 'https://accounts.google.com/gsi/client'
                js.onload = () => {
                    app.google.initialize(callback, selector, options);
                }
                document.head.append(js);
            },
            initialize: (callback, selector, options) => {
                google.accounts.id.initialize({
                    client_id: window.integrations.google,
                    callback: callback
                });
                setTimeout(() => {
                    document.querySelectorAll(selector).forEach((parent) => {
                        google.accounts.id.renderButton(parent, options);
                    });
                }, 1000);
            },
            renderButton: (selector, options) => {
                document.querySelectorAll(selector).forEach((parent) => {
                    google.accounts.id.renderButton(parent, options);
                });
            },
            JSONWebToken: (token) => {
                let url = token.split('.')[1],
                    base = url.replace(/-/g, '+').replace(/_/g, '/'),
                    json = decodeURIComponent(window.atob(base).split('').map(function(c) {
                        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                    }).join(''));

                return JSON.parse(json);
            },
            getUserInfo: function(profile){
                let data = {
                        name: profile.name,
                        email: profile.email,
                        avatar: profile.picture,
                        id: profile.sub
                    }

                return data;
            }
        },
        getFormData: function(data){
            let formData = new FormData();
            if (data) {
                for (let ind in data) {
                    if (Array.isArray(data[ind])) {
                        data[ind].forEach(function(v){
                            formData.append(ind+'[]', v);
                        })
                    } else if (typeof data[ind] == 'object') {
                        for (let i in data[ind]) {
                            let value = typeof data[ind][i] == 'object' ? JSON.stringify(data[ind][i]) : data[ind][i];
                            formData.append(ind+'['+i+']', value);
                        }
                    } else {
                        formData.append(ind, data[ind]);
                    }
                }
            }

            return formData;
        },
        decimalAdjust: function(type, value, exp){
            if (typeof exp === 'undefined' || +exp === 0) {
                return Math[type](value);
            }
            value = +value;
            exp = +exp;
            if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
                return NaN;
            }
            value = value.toString().split('e');
            value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
            value = value.toString().split('e');

            return +(value[0]+'e'+(value[1] ? (+value[1] + exp) : exp));
        },
        strrev: function(string){
            let ret = '', i = 0;
            for (i = string.length - 1; i >= 0; i--) {
                ret += string[i];
            }

            return ret;
        },
        renderPrice: function(value, thousand, separator, decimals, rate){
            rate = !rate ? 1 : rate;
            value *= rate;
            value = app.decimalAdjust('round', value, decimals * -1);
            value = String(value);
            let delta = value < 0 ? '-' : '',
                priceArray = value.replace('-', '').trim().split('.'),
                priceThousand = priceArray[0],
                priceDecimal = priceArray[1] ? priceArray[1] : '',
                price = '';
            if (priceThousand.length > 3 && thousand != '') {
                for (let i = 0; i < priceThousand.length; i++) {
                    if (i % 3 == 0 && i != 0) {
                        price += thousand;
                    }
                    price += priceThousand[priceThousand.length - 1 - i];
                }
                price = app.strrev(price);
            } else {
                price += priceThousand;
            }
            if (decimals != 0) {
                price += separator;
                for (let i = 0; i < decimals; i++) {
                    price += priceDecimal[i] ? priceDecimal[i] : '0';
                }
            }

            return delta+price;
        },
        getObject: function(key){
            var object = $g.extend(true, {}, app.items[key].desktop);
            if (app.view != 'desktop') {
                for (var ind in breakpoints) {
                    if (!app.items[key][ind]) {
                        app.items[key][ind] = {};
                    }
                    object = $g.extend(true, {}, object, app.items[key][ind]);
                    if (ind == app.view) {
                        break;
                    }
                }
            }

            return object;
        },
        initLoginRecaptcha: function(parent){
            if (!recaptchaObject) {

                return false;
            }
            let elem = document.createElement('div'),
                options = {
                    sitekey: recaptchaObject.public_key,
                    callback: verifyLoginCaptcha
                };
            elem.id = 'login-recaptcha-'+(+new Date());
            elem.className = 'login-recaptcha';
            parent.innerHTML = '';
            parent.append(elem);
            if (parent.dataset.type == 'recaptcha') {
                options.theme = recaptchaObject.theme;
                options.size = recaptchaObject.size;
            } else {
                options.badge = recaptchaObject.badge;
                options.size = 'invisible';
                parent.classList.add(options.badge+'-style');
            }
            recaptchaObject.data[elem.id] = grecaptcha.render(elem, options);
            if (parent.dataset.type != 'recaptcha') {
                grecaptcha.execute(recaptchaObject.data[elem.id]);
            }
        },
        initCommentsRecaptcha: function(parent){
            if (!recaptchaObject) {

                return false;
            }
            let elem = document.createElement('div'),
                options = {
                    sitekey : recaptchaObject.public_key
                };
            elem.id = 'comments-recaptcha-'+(+new Date());
            elem.className = 'comments-recaptcha';
            parent.innerHTML = '';
            parent.append(elem);
            if (recaptchaObject.type == 'recaptcha') {
                options.theme = recaptchaObject.theme;
                options.size = recaptchaObject.size;
            } else {
                options.badge = recaptchaObject.badge;
                options.size = 'invisible';
                elem.closest('.ba-comments-captcha-wrapper').classList.add(options.badge+'-style');
            }
            recaptchaObject.data[elem.id] = grecaptcha.render(elem, options);
            if (recaptchaObject.type != 'recaptcha') {
                grecaptcha.execute(recaptchaObject.data[elem.id]);
            }
        },
        hideNotice:function(){
            app.notification.classList.remove('notification-in');
            app.notification.classList.add('animation-out');
        },
        checkOverlay: function(obj, key){
            $g('.ba-item-overlay-section').each(function(){
                $g(this).find('.ba-overlay-section-backdrop').appendTo(document.body);
            });
        },
        _: function(key){
            if (window.gridboxLanguage && gridboxLanguage[key]) {
                return gridboxLanguage[key];
            } else {
                return key;
            }
        },
        checkGridboxPaymentError: function(){
            let gridbox_payment_error = localStorage.getItem('gridbox_payment_error');
            if (gridbox_payment_error) {
                app.showNotice(gridbox_payment_error, 'ba-alert');
                localStorage.removeItem('gridbox_payment_error');
            }
        },
        loadNotice: function(){
            return new Promise(function(resolve, reject) {
                fetch(JUri+'components/com_gridbox/views/layout/patterns/css/notification.css').then(function(request){
                    return request.text();
                }).then(function(text){
                    let style = document.createElement('style');
                    document.head.append(style);
                    style.innerHTML = text;
                    app.notification = document.createElement('div');
                    app.notification.id = 'ba-notification';
                    app.notification.innerHTML = '<i class="zmdi zmdi-close"></i><h4>'+app._('ERROR')+'</h4><p></p>';
                    app.notification.querySelector('.zmdi-close').addEventListener('click', function(){
                        app.hideNotice();
                    });
                    document.body.appendChild(app.notification);
                    resolve();
                });
            });
        },
        showNotice: function(message, className){
            if (!app.notification) {
                app.loadNotice().then(() => {
                    app.showNotice(message, className);
                });
                return false;
            }
            app.notification.showCallback = function(){};
            if (!className) {
                className = '';
            }
            if (app.notification.classList.contains('notification-in')) {
                app.notification.showCallback = function(){
                    app.notification.showCallback = function(){};
                    app.addNoticeText(message, className);
                };
            } else {
                app.addNoticeText(message, className);
            }
        },
        addNoticeText: function(message, className){
            var time = 3000;
            if (className == 'ba-alert') {
                time = 6000;
            }
            app.notification.querySelector('p').innerHTML = message;
            if (className) {
                app.notification.classList.add(className);
            } else {
                app.notification.classList.remove('ba-alert');
            }
            app.notification.classList.remove('animation-out')
            app.notification.classList.add('notification-in');
            clearTimeout(app.notification.hideDelay);
            app.notification.hideDelay = setTimeout(function(){
                app.hideNotice();
                setTimeout(function(){
                    if (className) {
                        app.notification.classList.remove(className);
                    }
                    app.notification.showCallback();
                }, 400);
            }, time);
        },
        checkAnimation: function(){
            app.viewportItems = [];
            app.motionItems = [];
            $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
                if (app.items[this.id]) {
                    let object = $g.extend(true, {}, app.items[this.id].desktop.animation),
                        motion = app.items[this.id].desktop.motions ? $g.extend(true, {}, app.items[this.id].desktop.motions) : {},
                        inMotion = false,
                        motions = {
                            desktop: motion
                        };
                    if (app.view != 'desktop') {
                        for (let ind in breakpoints) {
                            if (!app.items[this.id][ind]) {
                                app.items[this.id][ind] = {
                                    animation : {}
                                };
                            }
                            object = $g.extend(true, {}, object, app.items[this.id][ind].animation);
                            if (ind == app.view) {
                                break;
                            }
                        }
                    }
                    for (let ind in breakpoints) {
                        if (!app.items[this.id][ind]) {
                            app.items[this.id][ind] = {
                                animation : {}
                            };
                        }
                        motion = app.items[this.id][ind].motions ? $g.extend(true, {}, motion, app.items[this.id][ind].motions) : motion;
                        motions[ind] = motion;
                    }
                    if (object.effect && app.items[this.id].type != 'sticky-header') {
                        app.viewportItems.push({
                            animation: object,
                            item: $g(this)
                        });
                    } else if (!object.effect && app.items[this.id].type != 'sticky-header' && $g(this).viewportChecker) {
                        $g(this).viewportChecker(object)
                    } else if (object.effect) {
                        this.classList.add('visible');
                    }
                    for (let ind in motions) {
                        for (let key in motions[ind]) {
                            if (motions[ind][key].enable) {
                                app.motionItems.push({
                                    motions: motions,
                                    item: $g(this)
                                });
                                inMotion = true;
                                break;
                            }
                        }
                        if (inMotion) {
                            break;
                        }
                    }
                    if ($g(this).motion && !inMotion) {
                        $g(this).motion(motions);
                    }
                }
            });
            $g('.ba-item').each(function(){
                if (app.items[this.id] && app.items[this.id].desktop && app.items[this.id].desktop.appearance) {
                    let object = $g.extend(true, {}, app.items[this.id].desktop.appearance),
                        motion = app.items[this.id].desktop.motions ? $g.extend(true, {}, app.items[this.id].desktop.motions) : {},
                        inMotion = false,
                        motions = {
                            desktop: motion
                        };
                    if (app.view != 'desktop') {
                        for (let ind in breakpoints) {
                            if (!app.items[this.id][ind]) {
                                app.items[this.id][ind] = {
                                    appearance : {}
                                };
                            }
                            object = $g.extend(true, {}, object, app.items[this.id][ind].appearance);
                            if (ind == app.view) {
                                break;
                            }
                        }
                    }
                    for (let ind in breakpoints) {
                        motion = app.items[this.id][ind].motions ? $g.extend(true, {}, motion, app.items[this.id][ind].motions) : motion;
                        motions[ind] = motion;
                    }
                    if (object.effect) {
                        app.viewportItems.push({
                            animation: object,
                            item: $g(this)
                        });
                    } else if (!object.effect && $g(this).viewportChecker) {
                        $g(this).viewportChecker(object)
                    } else if (object.effect) {
                        this.classList.add('visible');
                    }
                    for (let ind in motions) {
                        for (let key in motions[ind]) {
                            if (motions[ind][key].enable) {
                                app.motionItems.push({
                                    motions: motions,
                                    item: $g(this)
                                });
                                inMotion = true;
                                break;
                            }
                        }
                        if (inMotion) {
                            break;
                        }
                    }
                    if ($g(this).motion && !inMotion) {
                        $g(this).motion(motions);
                    }
                }
            });
            if (app.viewportItems.length > 0 || app.motionItems.length > 0) {
                app.checkModule('loadAnimations');
            }
        },
        checkModule : function(name, obj){
            if (name == 'loadVideoApi' && app.modules[name] && obj && obj.data.type != 'youtube+vimeo'
                && app.modules[name].data.type != 'youtube+vimeo' && app.modules[name].data.type != obj.data.type) {
                obj.data.type = 'youtube+vimeo';
            } else if (typeof(obj) != 'undefined') {
                app.modules[name] = obj;
            }
            if (typeof(app[name]) == 'undefined' && !app.loading[name]) {
                app.loading[name] = true;
                app.loadModule(name);
            } else if (typeof(app[name]) != 'undefined') {
                if (typeof(obj) != 'undefined') {
                    app[name](obj.data, obj.selector);
                } else {
                    app[name]();
                }
            }
        },
        checkVideoBackground : function(){
            var flag = false;
            $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
                if (app.items[this.id] && app.items[this.id].desktop.background.type == 'video') {
                    flag = true;
                    return false;
                }
            });
            $g('.ba-item-flipbox').each(function(){
                if (app.items[this.id] && app.items[this.id].sides.frontside.desktop.background
                    && app.items[this.id].sides.frontside.desktop.background.type == 'video') {
                    flag = true;
                    return false;
                }
                if (app.items[this.id] && app.items[this.id].sides.backside.desktop.background
                    && app.items[this.id].sides.backside.desktop.background.type == 'video') {
                    flag = true;
                    return false;
                }
            });
            if (app.theme.desktop.background.type == 'video') {
                flag = true;
            }
            if (flag) {
                app.checkModule('createVideo', {});
            }
        },
        loadModule : function(key){
            if (key != 'setCalendar' && key != 'defaultElementsStyle' && key != 'gridboxLanguage' &&
                key != 'shapeDividers' && key != 'presetsPatern') {
                var script = document.createElement('script');
                script.src = JUri+'components/com_gridbox/libraries/modules/'+key+'.js?'+gridboxVersion;
                document.head.append(script);
                return false;
            }
            $g.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_gridbox&task=editor.loadModule&module="+key+"&"+gridboxVersion,
                data:{
                    module : key
                },
                complete: function(msg){
                    let script = document.createElement('script');
                    document.head.append(script);
                    script.innerHTML = msg.responseText;
                }
            });
        },
        checkView: function(){
            var width = $g(window).width();
            app.view = 'desktop';
            for (var ind in breakpoints) {
                if (width <= breakpoints[ind]) {
                    app.view = ind;
                }
            }
        },
        hideCommentsModal: function(){
            let str = '.ba-comments-modal .ba-comments-modal-backdrop, .ba-comments-modal .ba-btn';
            $g(str).off('click.hide').on('click.hide', function(){
                this.closest('.ba-comments-modal').classList.remove('visible-comments-dialog');
            });
        },
        resize: function(){
            clearTimeout(delay);
            app.checkView();
            delay = setTimeout(function(){
                if ('setPostMasonryHeight' in window) {
                    $g('.ba-blog-posts-wrapper.ba-masonry-layout').each(function(){
                        var key = $g(this).closest('.ba-item').attr('id');
                        setPostMasonryHeight(key);
                    });
                }
                if ('setGalleryMasonryHeight' in window) {
                    $g('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                        setGalleryMasonryHeight(this.closest('.ba-item').id);
                    });
                }
            }, 300);
        },
        checkCommentInTabs: function(hash){
            $g('a.'+hash.replace('#', '')).first().each(function(){
                let tab = this.closest('.tab-pane, .accordion-body');
                if (tab && !tab.classList.contains('active') && !tab.classList.contains('in')) {
                    $g('a[href="#'+tab.id+'"]').trigger('click');
                }
            });
        },
        gridboxLoaded: function(){
            app.checkView();
            app.checkAnimation();
            checkOnePage();
            window.addEventListener('resize', app.resize);
            $g(window).on('scroll', function(){
                var top = window.pageYOffset;
                if (!('lastPageYOffset' in window)) {
                    window.lastPageYOffset = top;
                }
                $g('header')[top > 40 ? 'addClass' : 'removeClass']('fixed-header');
                $g('.ba-sticky-header').each(function(){
                    this.closest('header, footer, .body').classList.add('ba-sticky-header-parent');
                    if (this.querySelector('.ba-sticky-header > .ba-section')) {
                        var section = this.querySelector('.ba-sticky-header > .ba-section'),
                            obj = app.items[section.id],
                            offset = obj.desktop.offset;
                        if (app.view != 'desktop') {
                            for (var ind in breakpoints) {
                                if (!obj[ind]) {
                                    obj[ind] = {};
                                }
                                offset = obj[ind].offset ? obj[ind].offset : offset;
                                if (ind == app.view) {
                                    break;
                                }
                            }
                        }
                        if (!this.classList.contains('visible-sticky-header')) {
                            if (top >= offset * 1 &&
                                (!obj.scrollup || (obj.scrollup && top - window.lastPageYOffset < 0))) {
                                this.classList.add('visible-sticky-header');
                                document.body.classList.add('sticky-header-opened');
                                if (obj.desktop.animation.effect) {
                                    section.classList.add(obj.desktop.animation.effect);
                                    setTimeout(function(){
                                        section.classList.remove(obj.desktop.animation.effect);
                                    }, obj.desktop.animation.delay * 1 + obj.desktop.animation.duration * 1000);
                                }
                                $g(window).trigger('scroll');
                            }
                        }
                        if ((top < offset * 1 && !obj.scrollup) || (obj.scrollup && (top - window.lastPageYOffset > 0
                            || top <= offset * 1))) {
                            this.classList.remove('visible-sticky-header');
                            document.body.classList.remove('sticky-header-opened');
                        }
                    }
                });
                window.lastPageYOffset = top;
            });
            $g(window).trigger('scroll');
            $g('.ba-item [contenteditable]').removeAttr('contenteditable');
            if ($g('.ba-item-overlay-section').length > 0) {
                app.checkModule('checkOverlay');
            }
            $g('.ba-item-main-menu, .ba-item-one-page-menu, .ba-item-overlay-section').each(function(){
                if (app.items[this.id]) {
                    var obj = {
                        data : app.items[this.id],
                        selector : this.id
                    };
                    itemsInit.push(obj);
                }
            });
            $g('.ba-item').not('.ba-item-main-menu, .ba-item-one-page-menu, .ba-item-overlay-section').each(function(){
                if (app.items[this.id]) {
                    var obj = {
                        data : app.items[this.id],
                        selector : this.id
                    };
                    itemsInit.push(obj);
                }
            });
            if (itemsInit.length > 0) {
                itemsInit.reverse();
                app.checkModule('initItems', itemsInit.pop());
            }
            app.checkVideoBackground();
            $g('.ba-lightbox-backdrop').find('.ba-lightbox-close').on('click', function(){
                lightboxVideoClose(this.closest('.ba-lightbox-backdrop'));
                this.closest('.ba-lightbox-backdrop').classList.remove('visible-lightbox');
                document.body.classList.remove('lightbox-open');
            });
            $g('.ba-lightbox-backdrop').each(function(){
                let obj = app.items[this.dataset.id];
                if (obj.type == 'cookies' || !obj.session.enable) {
                    initLightbox(this, obj);
                } else {
                    let flag = true;
                    if (localStorage[this.dataset.id]) {
                        let date =  new Date().getTime(),
                            expires = new Date(localStorage[this.dataset.id]);
                        expires.getTime();
                        if (date >= expires) {
                            flag = true;
                            localStorage.removeItem(this.dataset.id);
                        } else {
                            flag = false;
                        }
                    }
                    if (flag) {
                        let expiration = new Date();
                        expiration.setDate(expiration.getDate() + obj.session.duration * 1);
                        localStorage.setItem(this.dataset.id, expiration);
                        initLightbox(this, obj);
                    }
                }
            });
            $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
                if (app.items[this.id] && app.items[this.id].parallax && app.items[this.id].parallax.enable) {
                    app.checkModule('loadParallax');
                    return false;
                }
            });
            if (document.querySelector('.open-calendar-dialog')) {
                app.loadModule('calendar');
            }
        }
    };

app.fetch = function(url, data){
    return new Promise(function(resolve, reject) {
        fetch(url, {
            method: 'POST',
            cache: 'no-cache',
            body: app.getFormData(data)
        }).then(function(request){
            if (request.ok) {
                request.text().then(function(response){
                    resolve(response)
                })
            } else {
                let utf8Decoder = new TextDecoder("utf-8"),
                    reader = request.body.getReader();
                reader.read().then(function(textData){
                    console.info(utf8Decoder.decode(textData.value));
                })
            }
        });
    });
}

app.preloaded = {
    total: 0,
    loaded: 0,
    loadGridbox: function(){
        app.lazyLoad ? app.lazyLoad.check() : '';
        app.preloaded.end = true;
        app.gridboxLoaded();
    },
    callback: function(){
        app.preloaded.loaded++;
        if (app.preloaded.total <= app.preloaded.loaded && !app.preloaded.end && document.readyState != 'loading') {
            app.preloaded.loadGridbox();
        }
    }
}

document.querySelectorAll('link[rel="preload"][as="style"]').forEach(function(link){
    link.onload = app.preloaded.callback;
    link.onerror = app.preloaded.callback;
    app.preloaded.total++;
});

document.addEventListener("DOMContentLoaded", function(){
    $g('link[rel="preload"][as="style"]').attr('rel', 'stylesheet');
    document.body.style.opacity = '';
    document.body.style.overflow = '';
    document.body.style.margin = '';
    let preloader = document.querySelector('.ba-item-preloader');
    if (preloader) {
        setTimeout(function(){
            preloader.classList.add('preloader-animation-out');
            app.checkGridboxPaymentError();
        }, preloader.dataset.delay * 1000);
    } else {
        app.checkGridboxPaymentError();
    }
    app.hideCommentsModal();
    if ('setPostMasonryHeight' in window) {
        $g('.ba-blog-posts-wrapper.ba-masonry-layout').each(function(){
            var key = $g(this).closest('.ba-item').attr('id');
            setPostMasonryHeight(key);
        });
    }
    if ('setGalleryMasonryHeight' in window) {
        $g('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
            setGalleryMasonryHeight(this.closest('.ba-item').id);
        });
    }
    if (app.hash == '#total-count-wrapper' || app.hash == '#total-reviews-count-wrapper') {
        let item = $g('a.'+app.hash.replace('#', ''));
        app.checkCommentInTabs(app.hash);
        app.scrollIntoView(item);
    }
    $g('body').on('click', function(){
        $g('.visible-select').removeClass('visible-select');
        if (app.storeSearch && app.storeSearch.visible) {
            app.storeSearch.clearSearch();
        }
    }).on('hide', '.modal', function(){
        this.classList.add('ba-modal-close');
        setTimeout(function(){
            $g('.ba-modal-close').removeClass('ba-modal-close');
        }, 500);
    });
    $g('.ba-custom-select').on('click', 'i, input', function(){
        let parent = $g(this).closest('.ba-custom-select');
        if (!parent.find('ul').hasClass('visible-select')) {
            setTimeout(function(){
                parent.find('ul').addClass('visible-select');
            }, 100);
            parent.trigger('show');
        }
    }).on('click', 'li', function(){
        let parent = $g(this).closest('.ba-custom-select');
        parent.find('li.selected').removeClass('selected');
        this.classList.add('selected');
        parent.find('input[type="text"]').val(this.textContent.trim());
        parent.find('input[type="hidden"]').val(this.dataset.value).trigger('change');
        parent.trigger('customAction');
    });
    $g('.intro-post-reviews a, .intro-post-comments a').on('click', function(){
        let item = $g('a.'+this.hash.replace('#', ''));
        app.checkCommentInTabs(this.hash);
        app.scrollIntoView(item);
    });
    $g('li.megamenu-item').on('mouseenter', function(){
        var rectangle = this.getBoundingClientRect(),
            left = rectangle.left * -1,
            wrapper = $g(this).find(' > div.tabs-content-wrapper'),
            width = document.documentElement.clientWidth,
            maxwidth = width - rectangle.right;
        if (wrapper.hasClass('megamenu-center') && wrapper.hasClass('ba-container')) {
            left = $g(this).width() / 2;
        }
        if (rectangle.left < maxwidth) {
            maxwidth = rectangle.left;
        }
        if (!wrapper.hasClass('megamenu-center')) {
            maxwidth = width - rectangle.left;
        } else if (wrapper.hasClass('ba-container')) {
            left -= wrapper.outerWidth() / 2;
        }
        if (wrapper.hasClass('megamenu-center')) {
            maxwidth = (maxwidth + (rectangle.right - rectangle.left) / 2) * 2;
        }
        if ($g(this).closest('.ba-menu-wrapper').hasClass('vertical-menu')) {
            maxwidth = width - rectangle.right;
        }
        wrapper.css({
            'margin-left' : left+'px',
            'width' : width+'px',
            'max-width' : maxwidth+'px'
        });
    });
    $g('.ba-item-main-menu').closest('.ba-row').addClass('row-with-menu');
    for (var key in gridboxItems) {
        if (key != 'theme') {
            app.items = $g.extend(true, app.items, gridboxItems[key]);
        }
    }
    app.theme = gridboxItems.theme;
    if (app.preloaded.total == 0) {
        app.gridboxLoaded();
    } else if (app.preloaded.total <= app.preloaded.loaded && !app.preloaded.end) {
        app.preloaded.loadGridbox();
    }
});

var lightboxVideo = {};

function lightboxVideoClose(item)
{
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe');
    for (var i = 0; i < iframes.length; i++) {
        var src = iframes[i].src,
            videoId = iframes[i].id;
        if (src && src.indexOf('youtube.com') !== -1 && 'pauseVideo' in lightboxVideo[videoId]) {
            lightboxVideo[videoId].pauseVideo();
        } else if (src && src.indexOf('vimeo.com') !== -1 && 'pause' in lightboxVideo[videoId]) {
            lightboxVideo[videoId].pause();
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video');
    for (var i = 0; i < iframes.length; i++) {
        var videoId = iframes[i].id;
        lightboxVideo[videoId].pause();
    }
}

function lightboxVideoOpen(item)
{
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe'),
        youtube = false,
        vimeo = false,
        id = +new Date();
    for (var i = 0; i < iframes.length; i++) {
        var src = iframes[i].src,
            videoId;
        if (src && src.indexOf('youtube.com') !== -1) {
            if (!app.youtube) {
                youtube = true;
            } else {
                if (src.indexOf('enablejsapi=1') === -1) {
                    if (src.indexOf('?') === -1) {
                        src += '?';
                    } else {
                        src += '&'
                    }
                    src += 'enablejsapi=1';
                    iframes[i].src = src;
                }
                if (!iframes[i].id) {
                    iframes[i].id = id++;
                }
                videoId = iframes[i].id;
                if (!lightboxVideo[videoId] || !('playVideo' in lightboxVideo[videoId])) {
                    lightboxVideo[videoId] = new YT.Player(videoId, {
                        events: {
                            onReady: function(event){
                                lightboxVideo[videoId].playVideo();
                            }
                        }
                    });
                } else {
                    lightboxVideo[videoId].playVideo();
                }
            }
        } else if (src && src.indexOf('vimeo.com') !== -1) {
            if (!app.vimeo) {
                vimeo = true;
            } else {
                if (!iframes[i].id) {
                    iframes[i].id = id++;
                }
                videoId = iframes[i].id;
                if (!lightboxVideo[videoId] || !('play' in lightboxVideo[videoId])) {
                    src = src.split('/');
                    src = src.slice(-1);
                    src = src[0].split('?');
                    src = src[0];
                    var options = {
                        id: src * 1,
                        loop: true,
                    };
                    lightboxVideo[videoId] = new Vimeo.Player(videoId, options);
                }
                lightboxVideo[videoId].play();
            }
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video');
    for (var i = 0; i < iframes.length; i++) {
        if (!iframes[i].id) {
            iframes[i].id = id++;
        }
        videoId = iframes[i].id;
        if (!lightboxVideo[videoId]) {
            lightboxVideo[videoId] = iframes[i];
        }
        lightboxVideo[videoId].play();
    }
    if (youtube || vimeo) {
        var object = {
            data : {}
        };
        if (youtube && !vimeo) {
            object.data.type = 'youtube';
        } else if (vimeo && !youtube) {
            object.data.type = 'vimeo';
        } else {
            object.data.type = 'youtube+vimeo';
        }
        app.checkModule('loadVideoApi', object);
    }
    if (youtube) {
        lightboxVideo.overlay = item;
    } else if (vimeo) {
        lightboxVideo.overlay = item;
    }

    return !youtube && !vimeo;
}

function initLightbox($this, obj)
{
    var obj = app.items[$this.dataset.id];
    if (obj.type == 'cookies') {
        if (localStorage['ba-item-cookie']) {
            return false;
        }
        $g($this).find('.ba-item-button[data-cookie="accept"]').on('click', function(event){
            event.preventDefault();
            localStorage.setItem('ba-item-cookie', 'accept');
            $g(this).closest('.ba-lightbox-backdrop').removeClass('visible-lightbox');
            $g('body').removeClass('lightbox-open');
        });
        showLightbox($this);
    } else if (obj.trigger.type == 'time-delay') {
        setTimeout(function(){
            showLightbox($this);
        }, obj.trigger.time);
    } else if (obj.trigger.type == 'scrolling') {
        lightboxScroll($this, obj.trigger.scroll * 1);
    } else if (obj.trigger.type == 'exit-intent') {
        $g(document).one('mouseleave.ba-lightbox'+$this.dataset.id, function(){
            showLightbox($this);
        });
    } else {
        lightboxScroll($this, 100);
    }
}

function lightboxScroll($this, scroll)
{
    let top, docHeight, htmlHeight;
    $g(window).on('scroll.ba-lightbox'+$this.dataset.id+' load.ba-lightbox'+$this.dataset.id, function(){
        top = $g(window).scrollTop();
        docHeight = document.documentElement.clientHeight
        htmlHeight = Math.max(
            document.body.scrollHeight, document.documentElement.scrollHeight,
            document.body.offsetHeight, document.documentElement.offsetHeight,
            document.body.clientHeight, document.documentElement.clientHeight
        );
        let x = (docHeight + top) * 100 / htmlHeight;
        if (x >= scroll || (scroll > 97 && x >= 97)) {
            $g(window).off('scroll.ba-lightbox'+$this.dataset.id+' load.ba-lightbox'+$this.dataset.id);
            showLightbox($this);
        }
    });
}

function showLightbox($this)
{
    let obj = app.getObject($this.dataset.id);
    if (!lightboxVideoOpen($this) || obj.disable == 1) {
        return false;
    }
    $this.classList.add('visible-lightbox');
    if (obj.position == 'lightbox-center') {
        document.body.classList.add('lightbox-open');
    }
}

function compileOnePageValue(item)
{
    var value = item.offset().top,
        header = $g('header.header'),
        comp = header[0] ? getComputedStyle(header[0]) : {},
        top = window.pageYOffset,
        stickies = $g('.ba-sticky-header'),
        sticky = 0;
    if (item.closest('.ba-wrapper').parent().hasClass('header')) {
        value = 0;
    } else {
        stickies.each(function(){
            if (this.offsetHeight > 0) {
                let section = this.querySelector('.ba-sticky-header > .ba-section'),
                    obj = app.items[section.id],
                    offset = obj ? obj.desktop.offset : 0;
                if (app.view != 'desktop') {
                    for (var ind in breakpoints) {
                        if (!obj[ind]) {
                            obj[ind] = {};
                        }
                        offset = obj[ind].offset ? obj[ind].offset : offset;
                        if (ind == app.view) {
                            break;
                        }
                    }
                }
                if (obj && ((!obj.scrollup && offset < value) || (obj.scrollup && offset < value && value < top))) {
                    sticky = this.offsetHeight > sticky ? this.offsetHeight : sticky;
                }
            }
        });
        if ((!header.hasClass('sidebar-menu') || (app.view != 'desktop' && app.view != 'laptop'))
            && comp.position == 'fixed') {
            sticky = header[0].offsetHeight > sticky ? header[0].offsetHeight : sticky;
            if (header.find('.resizing-header').length > 0) {
                var resizingSection = getComputedStyle(header.find('.resizing-header')[0]);
                value += resizingSection.paddingTop.replace('px', '') * 1;
                value += resizingSection.paddingBottom.replace('px', '') * 1;
            }
        }
        value -= sticky;
    }

    return Math.ceil(value);
}

function checkOnePage()
{
    var alias = location.hash.replace('#', '');
    alias = decodeURIComponent(alias);
    if (alias && document.querySelector('.ba-item-one-page-menu a[data-alias="'+alias+'"]')) {
        $g('.ba-item-one-page-menu a[data-alias="'+alias+'"]').each(function(){
            let item = $g(this.hash);
            if ($g(this.parentNode).height() > 0 && this.hash && item.length > 0) {
                $g(this).closest('ul').find('.active').removeClass('active');
                $g('.ba-item-one-page-menu ul.nav.menu a[href*="'+this.hash+'"]').parent().addClass('active');
                app.scrollIntoView(item);
                return false;
            }
        });
    } else if ((alias == 'total-reviews-count-wrapper' || alias == 'total-count-wrapper')
        && document.querySelector('a.'+alias)) {
        let item = $g('a.'+alias);
        app.scrollIntoView(item);
    } else {
        checkOnePageActive();
    }
}

app.scrollIntoView = function(item){
    let value = compileOnePageValue(item);
    if (window.pageYOffset != value) {
        $g('html, body').stop().animate({
            'scrollTop' : value
        }, 1000);
    }
}

function checkOnePageActive()
{
    var items = [],
        alias = '',
        replace = null,
        flag = false;
    $g('.ba-item-one-page-menu ul li a').each(function(){
        if (this.offsetHeight > 0 && this.hash && $g(this.hash).height() > 0) {
            var computed = getComputedStyle(document.querySelector(this.hash));
            if (computed.display != 'none') {
                items.push(this);
            }
        }
    });
    items.sort(function(item1, item2){
        var target1 = $g(item1.hash),
            target2 = $g(item2.hash),
            top1 = target1.closest('header.header').length == 0 ? target1.offset().top : 0,
            top2 = target2.closest('header.header').length == 0 ? target2.offset().top : 0;
        if (top1 > top2) {
            return 1;
        } else if (top1 < top2) {
            return -1;
        } else {
            return 0;
        }
    });
    for (var i = items.length - 1; i >= 0; i--) {
        alias = items[i].dataset.alias;
        if (decodeURI(window.location.hash) == '#'+alias) {
            replace = location.href.replace(window.location.hash, '');
        }
        var value = compileOnePageValue($g(items[i].hash)),
            url = location.href.replace(window.location.hash, '')+'#'+alias;
        if (value <= Math.ceil(window.pageYOffset) + 1) {
            flag = true;
            $g('.ba-item-one-page-menu ul.nav.menu a[href*="'+items[i].hash+'"]').closest('ul')
                .find('.active').removeClass('active');
            $g('.ba-item-one-page-menu ul.nav.menu a[href*="'+items[i].hash+'"]').parent().addClass('active');
            break;
        }
    }
    if (!flag) {
        $g('.ba-item-one-page-menu .main-menu ul.nav.menu .active').removeClass('active');
        replace ? window.history.replaceState(null, null, replace) : '';
    } else if (decodeURI(window.location.hash) != '#'+alias) {
        window.history.replaceState(null, null, url);
    }
}

window.addEventListener('resize', function(){
    document.documentElement.style.setProperty('--vh', window.innerHeight * 0.01+'px');
});

jQuery(window).on('popstate.onepage', function(){
    onePageScroll = false;
    setTimeout(function(){
        onePageScroll = true;
    }, 300);
});

/*
    Default joomla
*/

document.addEventListener('DOMContentLoaded', function(){
    document.documentElement.style.setProperty('--vh', window.innerHeight * 0.01+'px');
    $g('*[rel=tooltip]').tooltip();
    $g('.radio.btn-group label').addClass('btn');
    $g('fieldset.btn-group').each(function() {
        if (this.disabled) {
            $g(this).css('pointer-events', 'none').off('click');
            $g(this).find('.btn').addClass('disabled');
        }
    });
    $g(".btn-group label:not(.active)").click(function(){
        var label = $g(this),
            input = $g('#'+label.attr('for'));
        if (!this.checked) {
            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
            if (input.val() == '') {
                label.addClass('active btn-primary');
            } else if (input.val() == 0) {
                label.addClass('active btn-danger');
            } else {
                label.addClass('active btn-success');
            }
            input.prop('checked', true).trigger('change');
        }
    });
    $g(".btn-group input[checked=checked]").each(function(){
        if (this.value == '') {
            $g("label[for="+this.id+"]").addClass('active btn-primary');
        } else if (this.value == 0) {
            $g("label[for="+this.id+"]").addClass('active btn-danger');
        } else {
            $g("label[for="+this.id+"]").addClass('active btn-success');
        }
    });
});