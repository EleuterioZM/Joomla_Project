/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var onePageScroll = true,
    pageAS = {
        scrolling :false,
        delta :1,
        startCoords : {},
        endCoords : {},
        anchor : [],
        compileValue: function(item){
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
                        if ((!obj.scrollup && offset < value) || (obj.scrollup && offset < value && value < top)) {
                            sticky = this.offsetHeight > sticky ? this.offsetHeight : sticky;
                        }
                    }
                });
                if ((!header.hasClass('sidebar-menu') || (app.view != 'desktop' && app.view != 'laptop'))
                    && comp.position == 'fixed') {
                    sticky = header[0].offsetHeight > sticky ? header[0].offsetHeight : sticky;
                    if (header.find('.resizing-header').length > 0) {
                        let resizingSection = getComputedStyle(header.find('.resizing-header')[0]);
                        value += resizingSection.paddingTop.replace('px', '') * 1;
                        value += resizingSection.paddingBottom.replace('px', '') * 1;
                    }
                }
                value -= sticky;
            }

            return Math.ceil(value);
        },
        animateScroll: function(){
            if (onePageScroll && pageAS.anchor.length > 0) {
                let scroll = $g(window).scrollTop(),
                    index = 0;
                for (let ind = 0; ind < pageAS.anchor.length; ind++) {
                    let $this = pageAS.anchor[ind],
                        value = pageAS.compileValue($g($this)),
                        li = $g('.ba-item-one-page-menu li a[href="#'+$this.id+'"][data-alias]');
                    value = value < 0 ? 0 : value;
                    if (value == Math.ceil(scroll)) {
                        index = ind + (-1 * pageAS.delta);
                        break;
                    } else if (value > Math.ceil(scroll)) {
                        index = ind - (pageAS.delta == -1 ? 0 : 1);
                        break;
                    }
                }
                if (index >= 0 && index < pageAS.anchor.length) {
                    pageAS.scrolling = true;
                    var obj = app.items[this.item].autoscroll,
                        value = pageAS.compileValue($g(pageAS.anchor[index]));
                    onePageScroll = false;
                    $g('html, body').stop().animate({
                        scrollTop: value
                    }, obj.speed * 1, obj.animation, function(){
                        if (window.pageYOffset != value) {
                            window.scrollTo(0, value);
                        }
                        onePageScroll = true;
                        checkOnePageActive();
                        setTimeout(function(){
                            pageAS.scrolling = false;
                        }, 200);
                    });
                }
            }
        },
        hasScrollBar: function(event){
            let q = '.ba-overlay-section-backdrop, .instagram-modal, .ba-lightbox-backdrop, '+
                '.visible-language-switcher-list, .ba-live-search-results, .ba-store-cart-backdrop, '+
                '.ba-store-wishlist-backdrop, .modal-scrollable, .ba-forms-modal-wrapper';

            return !event.target.closest(q);
        },
        wheelHandle: function(event){
            pageAS.checkItems();
            if (pageAS.anchor.length > 0 && pageAS.hasScrollBar(event)) {
                event.preventDefault();
                let value = event.wheelDelta || -event.deltaY || -event.detail;
                pageAS.delta = Math.max(-1, Math.min(1, value));
                if (pageAS.scrolling) {
                    return false;
                }
                pageAS.animateScroll();
            }
        },
        keydownHandle: function(event){
            let flag = false;
            if (flag = event.keyCode == 38 || event.keyCode == 33) {
                pageAS.delta = 1;
            } else if (flag = event.keyCode == 40 || event.keyCode == 34) {
                pageAS.delta = -1;
            }
            if (flag && pageAS.hasScrollBar(event)) {
                pageAS.checkItems();
                if (pageAS.anchor.length > 0) {
                    event.preventDefault();            
                    if (!pageAS.scrolling) {
                        pageAS.animateScroll();
                    }
                }
            }
        },
        checkItems: function(){
            this.anchor = [];
            if (app.view == 'desktop' || app.view == 'laptop') {
                $g('#'+this.item).find('> .ba-menu-wrapper > .main-menu > .integration-wrapper > ul li a').each(function(){
                    if (this.offsetHeight > 0 && this.hash && $g(this.hash).height() > 0) {
                        let item = document.querySelector(this.hash);
                        item.verticalOffset = pageAS.compileValue($g(item))
                        pageAS.anchor.push(item)
                    }
                });
                this.anchor.sort(function(a, b){
                    if (a.verticalOffset < b.verticalOffset) {
                        return -1;
                    } else if (a.verticalOffset > b.verticalOffset) {
                        return 1;
                    }

                    return 0;
                });
            }
        },
        setEvents: function(key){
            this.item = key;
            pageAS[key] = true;
            window.addEventListener('wheel', pageAS.wheelHandle, {passive: false});
            $g(window).on('keydown.'+key, pageAS.keydownHandle);
        },
        removeEvents: function(key){
            if (pageAS[key]) {
                window.removeEventListener('wheel', pageAS.wheelHandle, {passive: false});
                $g(window).off('keydown.'+key);
            }
        }
    }

app['initone-page'] = function(obj, key){
    if (!obj.autoscroll) {
        obj.autoscroll = {
            "enable": false,
            "speed": 1000,
            "animation": "easeInSine"
        }
    }
    if (themeData.page.view != 'gridbox' && obj.autoscroll.enable) {
        pageAS.setEvents(key);
    } else {
        pageAS.removeEvents(key);
    }
    $g('#'+key+' > .ba-menu-wrapper > .open-menu i').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        setTimeout(function(){
            var width = window.innerWidth - document.documentElement.clientWidth;
            $g('#'+key+' > .ba-menu-wrapper > .main-menu').addClass('visible-menu')
                .removeClass('hide-menu').css('right', -width+'px')
                .closest('.column-wrapper').addClass('column-with-menu')
                .closest('.ba-row-wrapper').addClass('row-with-menu');
            if (themeData.page.view == 'gridbox') {
                var computed = getComputedStyle(document.querySelector('header'));
                document.querySelector('header').classList.add('ba-header-position-'+computed.position);
            }
            $g('#'+key+' > .ba-menu-backdrop').addClass('ba-visible-menu-backdrop');
            $g('body').addClass('ba-opened-menu');
        }, 50);
    });
    $g('#'+key+' > .ba-menu-backdrop, #'+key+' > .ba-menu-wrapper > .main-menu > .close-menu i')
        .off('click').on('click', function(){
        closeOnePageMenu();
    });
    $g('#'+key+' > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul').on('click', ' > li > a', function(event){
        event.preventDefault();
        var item = $g(this.hash);
        if (this.hash && item.length > 0) {
            $g(this).closest('ul').find('.active').removeClass('active');
            this.parentNode.classList.add('active');
            var value = pageAS.compileValue(item),
                speed = app.items[key].autoscroll.speed * 1,
                animation = app.items[key].autoscroll.animation,
                alias = this.dataset.alias,
                url = location.href.replace(location.hash, '')+'#'+alias;
            $g('.ba-item-one-page-menu a[href="'+this.hash+'"]').not(this).each(function(){
                $g(this).closest('ul').find('.active').removeClass('active');
                this.parentNode.classList.add('active');
            });
            if (window.pageYOffset != value) {
                onePageScroll = false;
                $g('html, body').stop().animate({
                    'scrollTop' : value
                }, speed, animation, function(){
                    setTimeout(function(){
                        onePageScroll = true;
                    }, 200);
                });
            }
            window.history.replaceState(null, null, url);
        }
        closeOnePageMenu();
    });
    if (themeData.page.view != 'gridbox') {
        var endCoords = startCoords = {};
        $g('#'+key).on('touchstart', function(event){
            endCoords = event.originalEvent.targetTouches[0];
            startCoords = event.originalEvent.targetTouches[0];
        }).on('touchmove', function(event){
            endCoords = event.originalEvent.targetTouches[0];
        }).on('touchend', function(event){
            var hDistance = endCoords.pageX - startCoords.pageX,
                xabs = Math.abs(endCoords.pageX - startCoords.pageX),
                yabs = Math.abs(endCoords.pageY - startCoords.pageY);
            if(hDistance >= 100 && xabs >= yabs * 2) {
                $g('#'+key+' .ba-menu-backdrop').trigger('click');
            }
        });
    } else {
        var addNewItem = $g('#'+key+' > .ba-menu-wrapper > .main-menu > .add-new-item');
        if (addNewItem.length == 0) {
            addNewItem = '<div class="add-new-item"><span><i class="zmdi zmdi-layers"></i>'+
                '<span class="ba-tooltip ba-top">'+top.gridboxLanguage['ADD_NEW_ITEM']+'</span></span></div>';
            $g('#'+key+' > .ba-menu-wrapper > .main-menu').append(addNewItem);
            addNewItem = $g('#'+key+' > .ba-menu-wrapper > .main-menu > .add-new-item');
        }
        addNewItem.find('i').on('click', function(){
            app.edit = $g(this).closest('.ba-item')[0].id;
            window.parent.app.checkModule('addPlugins');
        });
        if ($g('#'+key+' > .ba-edit-item .open-mobile-menu').length == 0) {
            var str = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-open-in-new open-mobile-menu"></i>'+
                '<span class="ba-tooltip tooltip-delay settings-tooltip">'+window.parent.gridboxLanguage['OPEN']+
                '</span></span>';
            $g('#'+key+' > .ba-edit-item .ba-buttons-wrapper .ba-edit-wrapper').first().before(str);
        }
        $g('.open-mobile-menu').off('mousedown').on('mousedown', function(event){
            if (event.button && event.button != 0) {
                return false;
            }
            event.stopPropagation();
            $g(this).closest('.ba-item').find('> .ba-menu-wrapper > .open-menu i').first().trigger('mousedown');
        })
    }
    initItems();
}

function closeOnePageMenu()
{
    $g('.visible-menu').addClass('hide-menu').removeClass('visible-menu').css('right', '')
        .closest('.column-wrapper').removeClass('column-with-menu').closest('.ba-row-wrapper').removeClass('row-with-menu');
    $g('.ba-menu-backdrop').removeClass('ba-visible-menu-backdrop').addClass('ba-menu-backdrop-out');
    setTimeout(function(){
        $g('.ba-menu-backdrop.ba-menu-backdrop-out').removeClass('ba-menu-backdrop-out');
    }, 300);
    setTimeout(function(){
        if (themeData.page.view == 'gridbox') {
            var computed = getComputedStyle(document.querySelector('header'));
            document.querySelector('header').classList.remove('ba-header-position-'+computed.position);
        }
        $g('body').removeClass('ba-opened-menu');
    }, 500);
}

if (!$g('body').hasClass('gridbox')) {
    $g(window).off('scroll.onepage').on('scroll.onepage', function(){
        if (onePageScroll) {
            checkOnePageActive();
        }
    });
}

if (app.modules['initone-page']) {
    app['initone-page'](app.modules['initone-page'].data, app.modules['initone-page'].selector);
}