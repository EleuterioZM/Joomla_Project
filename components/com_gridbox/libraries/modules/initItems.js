/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$g.easing['jswing'] = $g.easing['swing'];

$g.extend($g.easing, {
    def: 'easeOutQuad',
    swing: function (x, t, b, c, d) {
        return $g.easing[$g.easing.def](x, t, b, c, d);
    },
    easeOutQuad: function (x, t, b, c, d) {
        return -c *(t /= d) * (t - 2) + b;
    },
    easeOutCubic: function (x, t, b, c, d) {
        return c * ((t = t / d - 1) * t * t + 1) + b;
    },
    easeInQuart: function (x, t, b, c, d) {
        return c * (t /= d) * t * t * t + b;
    },
    easeOutQuart: function (x, t, b, c, d) {
        return -c * ((t = t / d - 1) * t * t * t - 1) + b;
    },
    easeInSine: function (x, t, b, c, d) {
        return -c * Math.cos(t / d * (Math.PI / 2)) + c + b;
    },
    easeOutSine: function (x, t, b, c, d) {
        return c * Math.sin(t / d * (Math.PI / 2)) + b;
    },
    easeInExpo: function (x, t, b, c, d) {
        return (t == 0) ? b : c * Math.pow(2, 10 * (t / d - 1)) + b;
    },
    easeOutExpo: function (x, t, b, c, d) {
        return (t == d) ? b + c : c * (-Math.pow(2, -10 * t / d) + 1) + b;
    }
});

app.getCorrectColor = function(key){
    return key.indexOf('@') === -1 ? key : 'var('+key.replace('@', '--')+')';
}

app.initItems = function(obj, key){
    presetsCompatibility(obj);
    var object = {
        data : obj,
        selector : key
    };
    switch (obj.type) {
        case 'submission-form':
            app.checkModule('initSubmissionForm', object);
            break;
        case 'checkout-order-form':
            app.checkModule('initCheckoutOrderForm', object);
            break;
        case 'lottie-animations':
            app.checkModule('initLottieAnimations', object);
            break;
        case 'language-switcher':
            app.checkModule('initLanguageSwitcher', object);
            break;
        case 'currency-switcher':
            app.checkModule('initCurrencySwitcher', object);
            break;
        case 'button':
        case 'icon':
            app.checkModule('initButton', object);
            break;
        case 'submit-button':
            app.checkModule('initSubmitButton', object);
            break;
        case 'add-to-cart':
            app.checkModule('initAddToCart', object);
            break;
        case 'cart':
            app.checkModule('initCart', object);
            break;
        case 'login':
            app.checkModule('initLogin', object);
            break;
        case 'wishlist':
            app.checkModule('initWishlist', object);
            break;
        case 'text':
            app.checkModule('initText', object);
            break;
        case 'vk-comments':
            app.checkModule('initvkcomments', object);
            break;
        case 'comments-box':
            app.checkModule('initCommentsBox', object);
            break;
        case 'reviews':
            app.checkModule('initReviews', object);
            break;
        case 'facebook-comments':
            app.checkModule('initfacebookcomments', object);
            break;
        case 'progress-pie':
            app.checkModule('initprogressPie', object);
            break;
        case 'before-after-slider':
            app.checkModule('initBeforeAfterSlider', object);
            break;
        case 'hotspot':
            app.checkModule('initHotspot', object);
            break;
        case 'reading-progress-bar':
            app.checkModule('initReadingProgressBar', object);
            break;
        case 'progress-bar':
            app.checkModule('initprogressBar', object);
            break;
        case 'yandex-maps':
            app.checkModule('initYandexMaps', object);
            break;
        case 'openstreetmap':
            app.checkModule('initOpenstreetmap', object);
            break;
        case 'image-field':
            app.checkModule('initimage', object);
            break;
        case 'field-google-maps':
        case 'google-maps-places':
            app.checkModule('initmap', object);
            break;
        case 'scroll-to' :
        case 'star-ratings' :
        case 'scroll-to-top' :
        case 'disqus' :
        case 'image' :
        case 'countdown' :
        case 'counter' :
        case 'map' :
        case 'weather' :
        case 'menu' :
        case 'accordion' :
        case 'tabs' :
        case 'one-page' :
        case 'social' :
        case 'overlay-button' :
        case 'video' :
        case 'hypercomments' :
        case 'headline' :
        case 'flipbox' :
            app.checkModule('init'+obj.type, object);
            break;
        case 'search':
        case 'store-search':
            app.checkModule('initStoreSearch', object);
            break;
        case 'content-slider' :
        case 'slideshow' :
        case 'field-slideshow' :
        case 'product-slideshow' :
            app.checkModule('initslideshow', object);
            break;
        case 'simple-gallery':
        case 'field-simple-gallery':
        case 'product-gallery':
            app.checkModule('initSimpleGallery', object);
            break;
        case 'slideset' :
        case 'carousel' :
        case 'recent-posts-slider' :
        case 'related-posts-slider' :
        case 'recently-viewed-products' :
            app.checkModule('initslideset', object);
            break;
        case 'testimonials-slider' :
            app.checkModule('initTestimonials', object);
            break;
        case 'categories':
            app.checkModule('initCategories', object);
        case 'blog-posts':
        case 'search-result':
        case 'store-search-result':
        case 'recent-posts':
        case 'post-navigation':
        case 'related-posts':
        case 'recent-reviews':
            app.checkModule('initMasonryBlog', object);
            break;
        case 'event-calendar' :
            app.checkModule('initEventCalendar', object);
            break;
        case 'fields-filter' :
            app.checkModule('initItemsFilter', object);
            break;
        default : 
            initItems();
            break;
    }
}

function initItems()
{
    if (itemsInit.length > 0) {
        app.checkModule('initItems', itemsInit.shift());
    }
}

function presetsCompatibility(obj)
{
    switch (obj.type) {
        case 'button':
        case 'overlay-button':
        case 'scroll-to':
        case 'scroll-to-top':
            if (!obj.desktop.icons) {
                obj.desktop.icons = {
                    size : obj.desktop.size
                }
                delete(obj.desktop.size);
                if (obj.type == 'scroll-to') {
                    obj.desktop.icons.align = 'center';
                }
                for (var ind in breakpoints) {
                    if (obj[ind] && obj[ind].size) {
                        obj[ind].icons = {
                            size : obj[ind].size
                        };
                        delete(obj[ind].size);
                    }
                }
            }
            if (obj.type == 'scroll-to-top' && !obj.text) {
                obj.text = {
                    align: obj["scrolltop-align"]
                }
                delete(obj["scrolltop-align"]);
            }
            if (obj.type == 'scroll-to' && !obj.desktop.typography) {
                obj.desktop.icons.position = 'after';
                obj.desktop.typography = {
                    "font-family":"@default",
                    "font-size":10,
                    "font-style":"normal",
                    "font-weight":"700",
                    "letter-spacing":4,
                    "line-height":26,
                    "text-align":obj.desktop.icons.align,
                    "text-decoration":"none",
                    "text-transform":"uppercase"
                }
                delete(obj.desktop.icons.align);
                for (var ind in breakpoints) {
                    if (obj[ind] && obj[ind].icons && obj[ind].icons.align) {
                        obj[ind].typography = {
                            "text-align": obj[ind].icons.align
                        };
                        delete(obj[ind].icons.align);
                    }
                }
            }
        case 'tags':
        case 'post-tags':
        case 'icon':
        case 'social-icons':
            if (!obj.desktop.normal) {
                obj.desktop.normal = {
                    color: obj.desktop.color,
                    'background-color' : obj.desktop['background-color']
                }
                delete(obj.desktop.color);
                delete(obj.desktop['background-color']);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if (obj[ind].color || obj[ind]['background-color']) {
                            obj[ind].normal = {};
                            if (obj[ind].color) {
                                obj[ind].normal.color = obj[ind].color;
                                delete(obj[ind].color);
                            }
                            if (obj[ind]['background-color']) {
                                obj[ind].normal['background-color'] = obj[ind]['background-color'];
                                delete(obj[ind]['background-color']);
                            }
                        }
                    }
                }
            }
            break;
        case 'counter':
        case 'countdown':
            if (!obj.desktop.background) {
                obj.desktop.background = {
                    color: obj.desktop.color
                }
                delete(obj.desktop.color);
                for (var ind in breakpoints) {
                    if (obj[ind] && obj[ind].color) {
                        obj[ind].background = {
                            color: obj[ind].color
                        };
                        delete(obj[ind].color);
                    }
                }
            }
            break;
        case 'categories':
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    counter: obj.desktop.counter,
                    sub: obj.desktop.sub
                }
                delete(obj.desktop.counter);
                delete(obj.desktop.sub);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('counter' in obj[ind] || 'sub' in obj[ind]) {
                            obj[ind].view = {};
                            if ('counter' in obj[ind]) {
                                obj[ind].view.counter = obj[ind].counter;
                                delete(obj[ind].counter);
                            }
                            if ('sub' in obj[ind]) {
                                obj[ind].view.sub = obj[ind].sub;
                                delete(obj[ind].sub);
                            }
                        }
                    }
                }
            }
            break;
        case 'carousel':
        case 'slideset':
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    dots: obj.desktop.dots.enable,
                    arrows: obj.desktop.arrows.enable,
                    height: obj.desktop.height,
                    size: obj.desktop.size
                }
                obj.desktop.overlay = {
                    color: obj.desktop.caption.color
                }
                obj.desktop.gutter = obj.gutter != '';
                delete(obj.desktop.dots.enable);
                delete(obj.desktop.arrows.enable);
                delete(obj.desktop.caption.color);
                delete(obj.gutter);
                delete(obj.desktop.overflow);
                delete(obj.desktop.height);
                delete(obj.desktop.size);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('overflow' in obj[ind] || 'height' in obj[ind] || 'size' in obj[ind]) {
                            obj[ind].view = {};
                            if ('overflow' in obj[ind]) {
                                obj[ind].view.overflow = obj[ind].overflow;
                                delete(obj[ind].overflow);
                            }
                            if ('height' in obj[ind]) {
                                obj[ind].view.height = obj[ind].height;
                                delete(obj[ind].height);
                            }
                            if ('size' in obj[ind]) {
                                obj[ind].view.size = obj[ind].size;
                                delete(obj[ind].size);
                            }
                        }
                    }
                }
            }
            break;
        case 'slideshow':
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    dots: obj.desktop.dots.enable,
                    arrows: obj.desktop.arrows.enable,
                    fullscreen: obj.desktop.fullscreen,
                    height: obj.desktop.height,
                    size: obj.desktop.size
                }
                delete(obj.desktop.dots.enable);
                delete(obj.desktop.arrows.enable);
                delete(obj.desktop.fullscreen);
                delete(obj.desktop.height);
                delete(obj.desktop.size);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('fullscreen' in obj[ind] || 'height' in obj[ind] || 'size' in obj[ind]) {
                            obj[ind].view = {};
                            if ('fullscreen' in obj[ind]) {
                                obj[ind].view.fullscreen = obj[ind].fullscreen;
                                delete(obj[ind].fullscreen);
                            }
                            if ('height' in obj[ind]) {
                                obj[ind].view.height = obj[ind].height;
                                delete(obj[ind].height);
                            }
                            if ('size' in obj[ind]) {
                                obj[ind].view.size = obj[ind].size;
                                delete(obj[ind].size);
                            }
                        }
                    }
                }
            }
            break;
        case 'accordion':
            if (!obj.desktop.icon) {
                obj.desktop.icon = {
                    position : obj['icon-position'],
                    size: obj.desktop.size
                }
                obj.desktop.background = {
                    color: obj.desktop.background
                }
                delete(obj['icon-position']);
                delete(obj.desktop.size);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if (obj[ind].size) {
                            obj[ind].icon = {
                                size: obj[ind].size
                            }
                            delete(obj[ind].size);
                        }
                        if (obj[ind].background) {
                            obj[ind].background = {
                                color: obj[ind].background
                            }
                        }
                    }
                }
            }
            break;
        case 'tabs':
            if (!obj.desktop.icon) {
                obj.desktop.icon = {
                    position : obj['icon-position'],
                    size: obj.desktop.size
                }
                obj.desktop.background = {
                    color: obj.desktop.background
                }
                delete(obj['icon-position']);
                delete(obj.desktop.size);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if (obj[ind].size) {
                            obj[ind].icon = {
                                size: obj[ind].size
                            }
                            delete(obj[ind].size);
                        }
                        if (obj[ind].background) {
                            obj[ind].background = {
                                color: obj[ind].background
                            }
                        }
                    }
                }
            }
            break;
        case 'image':
            if (!obj.desktop.style) {
                if (!obj.desktop.width) {
                    obj.desktop.width = obj.width;
                    delete(obj.width);
                }
                obj.popup = Boolean(obj.lightbox.enable * 1);
                obj.desktop.style = {
                    width: obj.desktop.width,
                    align: obj.align
                }
                delete(obj.desktop.width);
                delete(obj.desktop.align);
                delete(obj.lightbox.enable);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('width' in obj[ind] || 'align' in obj[ind]) {
                            obj[ind].style = {};
                            if ('width' in obj[ind]) {
                                obj[ind].style.width = obj[ind].width;
                                delete(obj[ind].width);
                            }
                            if ('align' in obj[ind]) {
                                obj[ind].style.align = obj[ind].align;
                                delete(obj[ind].align);
                            }
                        }
                    }
                }
            }
            break;
        case 'simple-gallery':
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    "height": obj.desktop.height
                }
                delete(obj.desktop.height);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('height' in obj[ind]) {
                            obj[ind].view = {};
                            obj[ind].view.height = obj[ind].height;
                            delete(obj[ind].height);
                        }
                    }
                }
            }
            break;
        case 'weather':
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    "layout" : obj.layout,
                    "forecast" : obj.desktop.forecast,
                    "wind" : obj.desktop.wind,
                    "humidity" : obj.desktop.humidity,
                    "pressure" : obj.desktop.pressure,
                    "sunrise-wrapper" : obj.desktop['sunrise-wrapper']
                }
                delete(obj.layout);
                delete(obj.desktop.forecast);
                delete(obj.desktop.wind);
                delete(obj.desktop.humidity);
                delete(obj.desktop.pressure);
                delete(obj.desktop['sunrise-wrapper']);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        if ('forecast' in obj[ind] || 'wind' in obj[ind] || 'humidity' in obj[ind] ||
                            'pressure' in obj[ind] || 'sunrise-wrapper' in obj[ind]) {
                            obj[ind].view = {};
                            if ('forecast' in obj[ind]) {
                                obj[ind].view.forecast = obj[ind].forecast;
                                delete(obj[ind].forecast);
                            }
                            if ('wind' in obj[ind]) {
                                obj[ind].view.wind = obj[ind].wind;
                                delete(obj[ind].wind);
                            }
                            if ('humidity' in obj[ind]) {
                                obj[ind].view.humidity = obj[ind].humidity;
                                delete(obj[ind].humidity);
                            }
                            if ('pressure' in obj[ind]) {
                                obj[ind].view.pressure = obj[ind].pressure;
                                delete(obj[ind].pressure);
                            }
                            if ('sunrise-wrapper' in obj[ind]) {
                                obj[ind].view['sunrise-wrapper'] = obj[ind]['sunrise-wrapper'];
                                delete(obj[ind]['sunrise-wrapper']);
                            }
                        }
                    }
                }
            }
            break;
        case "menu":
            if (!obj.desktop.background) {
                obj.desktop.background = {
                    color: obj.desktop['background-color']
                }
                delete(obj.desktop['background-color']);
                for (var ind in breakpoints) {
                    if (obj[ind] && 'background-color' in obj[ind]) {
                        obj[ind].background = {
                            color: obj[ind]['background-color']
                        }
                        delete(obj[ind]['background-color']);
                    }
                }
                obj.layout = {
                    layout: obj.layout
                }
            }
            break;
        case "one-page":
            if (typeof(obj.layout) == 'string') {
                obj.layout = {
                    layout: obj.layout,
                    type: obj['menu-type']
                }
                delete(obj['menu-type']);
            }
            break;
        case 'social':
            if (!obj.view) {
                obj.view = {
                    "layout" : obj.layout,
                    "size" : obj.size,
                    "style" : obj.style,
                    "counters" : obj.counters
                }
                delete(obj.layout);
                delete(obj.size);
                delete(obj.style);
                delete(obj.counters);
            }
            break;
        case 'recent-posts-slider':
            if (!obj.desktop.reviews) {
                obj.desktop.reviews = {
                    margin: {
                        top: 0,
                        bottom: 25
                    },
                    "hover": {
                        "color" : "@primary"
                    },
                    "typography": {
                        "color":"@title",
                        "font-family":"@default",
                        "font-size":"12",
                        "font-style":"normal",
                        "font-weight":"900",
                        "letter-spacing":"0",
                        "line-height":"18",
                        "text-decoration":"none",
                        "text-align":"left",
                        "text-transform":"none"
                    }
                }
            }
            break;
        case 'recent-posts':
        case 'search-result':
        case 'store-search-result':
        case 'post-navigation':
        case 'related-posts':
        case 'blog-posts':
            if (!obj.desktop.reviews) {
                obj.desktop.reviews = {
                    margin: {
                        top: 0,
                        bottom: 25
                    },
                    "hover": {
                        "color" : "@primary"
                    },
                    "typography": {
                        "color":"@title",
                        "font-family":"@default",
                        "font-size":"12",
                        "font-style":"normal",
                        "font-weight":"900",
                        "letter-spacing":"0",
                        "line-height":"18",
                        "text-decoration":"none",
                        "text-align":"left",
                        "text-transform":"none"
                    }
                }
            }
            if (obj.type == 'blog-posts' && obj.desktop.image.show == undefined) {
                obj.desktop.image.show = true;
                obj.desktop.title.show = true;
                obj.desktop.date = true;
                obj.desktop.category = true;
                obj.desktop.intro.show = true;
                obj.desktop.button.show = true;
                obj.desktop.hits = true;
            } else if (obj.type != 'blog-posts' && obj.desktop.hits == undefined) {
                obj.desktop.hits = false;
            }
            if (!obj.desktop.title.hover) {
                obj.desktop.title.hover = {
                    "color" : '#fc5859'
                }
                obj.desktop.info.hover = {
                    "color" : '#fc5859'
                }
            }
            if (!obj.desktop.background) {
                obj.desktop.background = {
                    "color" : "rgba(242, 250, 250, 0)"
                };
                obj.desktop.shadow = {
                    "value":0,
                    "color":"rgba(0, 0, 0, 0.15)"
                };
                obj.desktop.border = {
                    "color" : "#000000",
                    "radius" : "0",
                    "style" : "solid",
                    "width" : "0"
                }
            }
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    "count" : obj.desktop.count,
                    "gutter" : obj.desktop.gutter,
                    "date" : obj.desktop.date,
                    "category" : obj.desktop.category,
                    "image": obj.desktop.image.show,
                    "title": obj.desktop.title.show,
                    "intro": obj.desktop.intro.show,
                    "button": obj.desktop.button.show,
                    "hits" : obj.desktop.hits
                }
                delete(obj.desktop.count);
                delete(obj.desktop.gutter);
                delete(obj.desktop.date);
                delete(obj.desktop.category);
                delete(obj.desktop.title.show);
                delete(obj.desktop.intro.show);
                delete(obj.desktop.button.show);
                obj.desktop.overlay = {
                    color: obj.desktop.overlay
                }
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        obj[ind].view = {};
                        if ('count' in obj[ind]) {
                            obj[ind].view.count = obj[ind].count;
                            delete(obj[ind].count);
                        }
                        if ('gutter' in obj[ind]) {
                            obj[ind].view.gutter = obj[ind].gutter;
                            delete(obj[ind].gutter);
                        }
                        if ('date' in obj[ind]) {
                            obj[ind].view.date = obj[ind].date;
                            delete(obj[ind].date);
                        }
                        if ('category' in obj[ind]) {
                            obj[ind].view.category = obj[ind].category;
                            delete(obj[ind].category);
                        }
                        if ('hits' in obj[ind]) {
                            obj[ind].view.hits = obj[ind].hits;
                            delete(obj[ind].hits);
                        }
                        if (obj[ind].image && 'show' in obj[ind].image) {
                            obj[ind].view.image = obj[ind].image.show;
                        }
                        if (obj[ind].title && 'show' in obj[ind].title) {
                            obj[ind].view.title = obj[ind].title.show;
                            delete(obj[ind].title.show);
                        }
                        if (obj[ind].intro && 'show' in obj[ind].intro) {
                            obj[ind].view.intro = obj[ind].intro.show;
                            delete(obj[ind].intro.show);
                        }
                        if (obj[ind].button && 'show' in obj[ind].button) {
                            obj[ind].view.button = obj[ind].button.show;
                            delete(obj[ind].button.show);
                        }
                    }
                }
                obj.layout = {
                    layout: obj.layout
                }
            }
            break;
        case 'search':
            if (!obj.desktop.icons) {
                obj.desktop.icons = {
                    size: obj.desktop.size,
                    position: obj.icon.position
                }
                delete(obj.desktop.size);
                delete(obj.icon.position);
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        obj[ind].icons = {};
                        if ('size' in obj[ind]) {
                            obj[ind].icons.size = obj[ind].size;
                            delete(obj[ind].size);
                        }
                    }
                }
            }
            break;
        case 'category-intro':
        case 'post-intro':
            if (!obj.desktop.info.hover) {
                obj.desktop.info.hover = {
                    color : '#fc5859'
                };
            }
            if (obj.desktop.image.show == undefined) {
                obj.desktop.image.show = true;
                obj.desktop.title.show = true;
                obj.desktop.date = true;
                obj.desktop.category = true;
                obj.desktop.hits = true;
            }
            if (!obj.desktop.view) {
                obj.desktop.view = {
                    "date": obj.desktop.date,
                    "category": obj.desktop.category,
                    "hits": obj.desktop.hits
                }
                delete(obj.desktop.date);
                delete(obj.desktop.date);
                delete(obj.desktop.date);
                obj.layout = {
                    "layout": obj.layout
                }
                for (var ind in breakpoints) {
                    if (obj[ind]) {
                        obj[ind].view = {};
                        if ('date' in obj[ind]) {
                            obj[ind].view.date = obj[ind].date;
                            delete(obj[ind].date);
                        }
                        if ('category' in obj[ind]) {
                            obj[ind].view.category = obj[ind].category;
                            delete(obj[ind].category);
                        }
                        if ('hits' in obj[ind]) {
                            obj[ind].view.hits = obj[ind].hits;
                            delete(obj[ind].hits);
                        }
                    }
                }
            }
            break;
    }
    if (obj.type == 'icon' || obj.type == 'social-icons') {
        if (!obj.desktop.icon) {
            obj.desktop.icon = {
                'size' : obj.desktop.size,
                'text-align': obj.desktop['text-align'],
            }
            delete(obj.desktop.size);
            delete(obj.desktop['text-align']);
            for (var ind in breakpoints) {
                if (obj[ind]) {
                    obj[ind].icon = {};
                    if (obj[ind].size) {
                        obj[ind].icon.size = obj[ind].size;
                        delete(obj[ind].size);
                    }
                    if (obj[ind]['text-align']) {
                        obj[ind].icon['text-align'] = obj[ind]['text-align'];
                        delete(obj[ind]['text-align']);
                    }
                }
            }
        }
    }
}

if (app.modules.initItems) {
    app.initItems(app.modules.initItems.data, app.modules.initItems.selector);
}