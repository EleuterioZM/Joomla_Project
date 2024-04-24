/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function appendItemStyles(key, type, preset)
{
    if (app.pageCss[key] && !app.pageCss[key].parentNode) {
        document.head.append(app.pageCss[key]);
    }
    if (!app.pageCss[key]) {
        app.pageCss[key] = document.createElement('style');
        app.pageCss[key].type = 'text/css';
        let str = getPageCSS(app.items[key], key);
        app.pageCss[key].innerHTML = str;
        document.head.append(app.pageCss[key]);
    } else if (app.edit == 'body' || app.edit == key ||
        (preset && app.items[key].type == type && app.items[key].preset == preset)) {
        let str = getPageCSS(app.items[key], key);
        app.pageCss[key].innerHTML = str;
    }
}

app.sectionRules = function(){
    var type = preset = '';
    if (app.edit && app.edit != 'body' && app.items[app.edit]) {
        type = app.items[app.edit].type;
        preset = app.items[app.edit].preset;
    }
    for (var key in app.items) {
        appendItemStyles(key, type, preset)
    }
    if (app.preloader) {
        app.preloader.classList.add('ba-hide');
        app.preloader.classList.remove('ba-preloader-slide');
        window.top.$g('.gridbox-save').on('click', function(){
            window.top.app.saveMode = 'save';
            window.top.app.checkModule('gridboxSave');
        }).addClass('gridbox-enabled-save');
        app.preloader = null;
    }
    if (app.setNewFont) {
        getFontUrl();
    }
}

function getPageCSS(obj, key)
{
    let str = '';
    app.itemType = key;
    comparePresets(obj);
    app.breakpoint = 'desktop';
    switch (obj.type) {
        case 'checkout-order-form':
            break;
        case 'breadcrumbs':
            str += createBreadcrumbsRules(obj, key);
            break;
        case 'checkout-form':
        case 'submission-form':
            str += createCheckoutFormRules(obj, key);
            break;
        case 'login':
            str += createLoginRules(obj, key);
            break;
        case 'preloader':
            str += createPreloaderRules(obj, key);
            break;
        case 'icon-list':
            str += createIconListRules(obj, key);
            break;
        case 'search':
        case 'store-search':
            str += createSearchRules(obj, key);
            break;
        case 'logo' :
            str += createLogoRules(obj, key);
            break;
        case 'feature-box' :
            str += createFeatureBoxRules(obj, key);
            break;
        case 'before-after-slider' :
            str += createBeforeAfterSliderRules(obj, key);
            break;
        case 'slideshow' :
        case 'field-slideshow' :
        case 'product-slideshow' :
            str += createSlideshowRules(obj, key);
            break;
        case 'carousel' :
        case 'slideset' :
            str += createCarouselRules(obj, key);
            break;
        case 'testimonials-slider' :
            str += createTestimonialsRules(obj, key);
            break;
        case 'recent-posts-slider':
        case 'related-posts-slider':
        case 'recently-viewed-products':
            str += createRecentSliderRules(obj, key);
            break;
        case 'content-slider':
            str += createContentRules(obj, key);
            break;
        case 'menu' :
            str += createMenuRules(obj, key);
            break;
        case 'one-page' :
            str += createOnePageRules(obj, key);
            break;
        case 'map':
        case 'yandex-maps':
        case 'openstreetmap':
        case 'field-google-maps':
        case 'google-maps-places':
            str += createMapRules(obj, key);
            break;
        case 'weather' :
            str += createWeatherRules(obj, key);
            break;
        case 'scroll-to-top' :
            str += createScrollTopRules(obj, key);
            break;
        case 'image' :
        case 'image-field' :
            str += createImageRules(obj, key);
            break;
        case 'lottie-animations' :
            str += createLottieRules(obj, key);
            break;
        case 'video':
        case 'field-video':
            str += createVideoRules(obj, key);
            break;
        case 'tabs' :
            str += createTabsRules(obj, key);
            break;
        case 'accordion' :
            str += createAccordionRules(obj, key);
            break;
        case 'icon' :
        case 'social-icons':
            str += createIconRules(obj, key);
            break;
        case 'cart':
        case 'button':
        case 'submit-button':
        case 'tags':
        case 'post-tags':
        case 'overlay-button':
        case 'scroll-to':
        case 'wishlist':
        case 'field-button':
            str += createButtonRules(obj, key);
            break;
        case 'hotspot':
            str += createHotspotRules(obj, key);
            break;
        case 'countdown' :
            str += createCountdownRules(obj, key);
            break;
        case 'counter' :
            str += createCounterRules(obj, key);
            break;
        case 'text':
        case 'headline':
            str += createTextRules(obj, key);
            break;
        case 'progress-bar' :
            str += createProgressBarRules(obj, key);
            break;
        case 'reading-progress-bar' :
            str += createReadingProgressBarRules(obj, key);
            break;
        case 'progress-pie' :
            str += createProgressPieRules(obj, key);
            break;
        case 'social' :
            str += createSocialRules(obj, key);
            break;
        case 'disqus':
        case 'vk-comments':
        case 'hypercomments':
        case 'facebook-comments':
        case 'modules':
        case 'custom-html':
        case 'gallery':
        case 'forms':
            str += createModulesRules(obj, key);
            break;
        case 'language-switcher':
            str += createLanguageSwitcherRules(obj, key);
            break;
        case 'currency-switcher':
            str += createCurrencySwitcherRules(obj, key);
            break;
        case 'comments-box':
        case 'reviews':
            str += createCommentsBoxRules(obj, key);
            break;
        case 'event-calendar':
            str += createEventCalendarRules(obj, key);
            break;
        case 'field':
        case 'field-group':
            str += createFieldRules(obj, key);
            break;
        case 'fields-filter':
            str += createFieldsFilterRules(obj, key);
            break;
        case 'blog-posts' :
        case 'search-result':
        case 'store-search-result':
        case 'recent-posts' :
        case 'post-navigation' :
        case 'related-posts' :
            str += createBlogPostsRules(obj, key);
            break;
        case 'add-to-cart' :
            str += createAddToCartRules(obj, key);
            break;
        case 'categories' :
            str += createCategoriesRules(obj, key);
            break;
        case 'recent-comments':
        case 'recent-reviews':
            str += createRecentCommentsRules(obj, key);
            break;
        case 'author':
            str += createAuthorRules(obj, key);
            break;
        case 'star-ratings' :
            str += createStarRatingsRules(obj, key);
            break;
        case 'post-intro' :
        case 'category-intro' :
            str += createPostIntroRules(obj, key);
            break;
        case 'instagram':
            str += '';
            break;
        case 'simple-gallery':
        case 'field-simple-gallery':
        case 'product-gallery':
            str += createSimpleGalleryRules(obj, key);
            break;
        case 'blog-content' :
            break;
        case 'mega-menu-section' :
            str += createMegaMenuSectionRules(obj, key);
            break;
        case 'flipbox' :
            str += createFlipboxRules(obj, key);
            break;
        case 'error-message':
            str += createErrorRules(obj, key);
            break;
        case 'search-result-headline':
            str += createSearchHeadlineRules(obj, key);
            break;
        default :
            str += createSectionRules(obj, key);
    }
    
    return str;
}

function positioningRules(obj, selector)
{
    let str = "",
        item = app.items[app.itemType];
    if (item.positioning && item.positioning.position) {
        let position = item.positioning.position,
            h = obj.positioning.horizontal,
            v = obj.positioning.vertical,
            x1 = (h == '' || h == 'left' || h == 'center') ? 'left' : 'right',
            x = 'calc('+app.cssRules.getValueUnits(obj.positioning.x)+' + var(--translate-border-'+x1+'))',
            y = app.cssRules.getValueUnits(obj.positioning.y),
            x2 = x1 == 'left' ? 'right' : 'left',
            y1 = (v == '' || v == 'top' || v == 'center') ? 'top' : 'bottom',
            y2 = y1 == 'top' ? 'bottom' : 'top';
        if (position == 'fixed' && v != 'bottom') {
            y = 'calc('+y+' + var(--top-page-offset))';
        } else if (position == 'fixed') {
            y = 'calc('+y+' + var(--bottom-page-offset))';
        }
        if (position == 'absolute') {
            x = 'calc('+app.cssRules.getValueUnits(obj.positioning.x)+' + var(--translate-'+x1+'))';
            y = 'calc('+y+' + var(--translate-'+y1+'))';
        }
        str += "#"+selector+" {";
        str += x1+": "+x+";";
        str += x2+": auto;";
        str += y1+": "+y+";";
        str += y2+": auto;";
        str += "z-index: "+(obj.positioning.z * 1 + 32)+";";
        str += "position: "+position+";";
        if ('width' in obj.positioning) {
            str += "width: "+app.cssRules.getValueUnits(obj.positioning.width)+" !important;";
        }
        str += "}";
    }
    if (obj.appearance) {
        str += "#"+selector+" {";
        str += "animation-duration: "+obj.appearance.duration+"s;";
        str += "animation-delay: "+obj.appearance.delay+"s;";
        str += "opacity: "+(obj.appearance.effect ? 0 : 1)+";";
        str += "}";
    }
    if (obj.appearance && obj.appearance.effect && obj.disable == 1) {
        str += "body.show-hidden-elements #"+selector+".visible {opacity : 0.3;}";
    } else if (obj.appearance && obj.appearance.effect) {
        str += "#"+selector+".visible {opacity : 1;}";
    }

    return str;
}

function setItemsVisability(disable, display, selector)
{
    var str = 'body.show-hidden-elements '+selector+' {';
    if (disable == 1) {
        str += "opacity : 0.3;";
    } else {
        str += "opacity : 1;";
    }
    str += "display : "+display+";";
    str += '}';
    str += 'body:not(.show-hidden-elements) '+selector+' {';
    if (disable == 1) {
        str += "display : none;";
    } else {
        str += "display : "+display+";";
    }
    str += '}';

    return str;
}

function setBoxModel(obj, selector)
{
    return positioningRules(obj, selector);
}

function createOnePageRules(obj, key)
{
    if (!obj.desktop.nav) {
        let $nav = '{"padding":{"bottom":"15","left":"15","right":"15","top":"15"},"margin":{"left":"0","right":"0"}';
        $nav += ',"icon":{"size":24},"border":{"bottom":"0","left":"0","right":"0","top":"0","color":"#000000",';
        $nav += '"style":"solid","radius":"0","width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,';
        $nav += '0)"},"hover":{"color":"color","background":"rgba(0,0,0,0)"}}';
        obj.desktop.nav = JSON.parse($nav);
        obj.desktop.nav.normal.color = obj.desktop['nav-typography'].color;
        obj.desktop.nav.hover.color = obj.desktop['nav-hover'].color;
    }
    app.cssRules.prepareColors(obj.desktop.nav);
    if (!obj.desktop.nav.colors.active) {
        obj.desktop.nav.colors.active = $g.extend(true, {}, obj.desktop.nav.colors.hover);
    }
    let str = getOnePageRules(obj.desktop, key);
    if (!disableResponsive) {
        str += "@media (max-width: "+menuBreakpoint+"px) {"
        str += "#"+key+" > .ba-hamburger-menu > .main-menu {";
        str += "background-color : "+getCorrectColor(obj.hamburger.background)+";";
        if (obj.hamburger.width) {
            str += "width : "+app.cssRules.getValueUnits(obj.hamburger.width)+";";
            str += app.cssRules.get('padding', obj.hamburger.padding, 'default');
            str += app.cssRules.get('shadow', obj.hamburger.shadow, 'default');
        }
        str += "}"
        if (obj.hamburger.overlay) {
            str += "#"+key+" > .ba-menu-backdrop {";
            str += "background-color : "+getCorrectColor(obj.hamburger.overlay)+";";
            str += "}"
        }
        str += "#"+key+" .ba-hamburger-menu .open-menu {";
        str += "color : "+getCorrectColor(obj.hamburger.open)+";";
        str += "text-align : "+obj.hamburger['open-align']+";";
        if (obj.hamburger.icons) {
            str += "font-size: "+app.cssRules.getValueUnits(obj.hamburger.icons.open.size)+";";
        }
        str += "}";
        str += "#"+key+" .ba-hamburger-menu .close-menu {";
        str += "color : "+getCorrectColor(obj.hamburger.close)+";";
        str += "text-align : "+obj.hamburger['close-align']+";";
        if (obj.hamburger.icons) {
            str += "font-size: "+app.cssRules.getValueUnits(obj.hamburger.icons.close.size)+";";
        }
        str += "}";
        str += "}";
    }
    str += app.setMediaRules(obj, key, 'getOnePageRules');
    $g('#'+key).removeClass('side-navigation-menu').addClass(obj.layout.type).find('.ba-menu-wrapper').each(function(){
        $g(this).removeClass('vertical-menu ba-menu-position-left ba-hamburger-menu ba-menu-position-center');
        if (obj.hamburger.enable) {
            $g(this).addClass('ba-hamburger-menu');
        }
    }).addClass(obj.layout.layout).addClass(obj.hamburger.position);

    return str;
}

function createBreadcrumbsRules(obj, key)
{
    app.cssRules.prepareColors(obj.desktop.style);
    if (!obj.desktop.style.colors.active) {
        obj.desktop.style.colors.active = $g.extend(true, {}, obj.desktop.style.colors.hover);
    }
    let str = getBreadcrumbsRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getBreadcrumbsRules');

    return str;
}

function createMenuRules(obj, key)
{
    if (!obj.desktop.nav) {
        let $nav = '{"padding":{"bottom":"15","left":"15","right":"15","top":"15"},"margin":{"left":"0","right":"0"}';
        $nav += ',"icon":{"size":24},"border":{"bottom":"0","left":"0","right":"0","top":"0","color":"#000000",';
        $nav += '"style":"solid","radius":"0","width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,';
        $nav += '0)"},"hover":{"color":"color","background":"rgba(0,0,0,0)"}}';
        obj.desktop.nav = JSON.parse($nav);
        obj.desktop.nav.normal.color = obj.desktop['nav-typography'].color;
        obj.desktop.nav.hover.color = obj.desktop['nav-hover'].color;
        let sub = '{"padding":{"bottom":"10","left":"20","right":"20","top":"10"},"icon":{"size":24},"border":{';
        sub += '"bottom":"0","left":"0","right":"0","top":"0","color":"#000000","style":"solid","radius":"0",';
        sub += '"width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,0)"},"hover":{"color":"color",';
        sub += '"background":"rgba(0,0,0,0)"}}';
        obj.desktop.sub = JSON.parse(sub);
        obj.desktop.sub.normal.color = obj.desktop['sub-typography'].color;
        obj.desktop.sub.hover.color = obj.desktop['sub-hover'].color;
        sub = '{"width":250,"animation":{"effect":"fadeInUp","duration":"0.2"},"padding":{"bottom":"10",';
        sub += '"left":"0","right":"0","top":"10"}}';
        obj.desktop.dropdown = JSON.parse(sub);
    }
    app.cssRules.prepareColors(obj.desktop.nav);
    app.cssRules.prepareColors(obj.desktop.sub);
    if (!obj.desktop.nav.colors.active) {
        obj.desktop.nav.colors.active = $g.extend(true, {}, obj.desktop.nav.colors.hover);
    }
    if (!obj.desktop.sub.colors.active) {
        obj.desktop.sub.colors.active = $g.extend(true, {}, obj.desktop.sub.colors.hover);
    }
    if (!obj.desktop.dropdown.padding) {
        obj.desktop.dropdown = obj.desktop.dropdown.default;
    }
    let str = getMenuRules(obj.desktop, key);
    str += "#"+key+" li.deeper.parent > ul {";
    str += "width: "+app.cssRules.getValueUnits(obj.desktop.dropdown.width)+";";
    str += "background-color : "+getCorrectColor(obj.desktop.background.color)+";";
    str += "}";
    str += "#"+key+" li.deeper.parent > ul, ";
    str += "#"+key+" li.megamenu-item > .tabs-content-wrapper > .ba-section {";
    str += app.cssRules.get('shadow', obj.desktop.shadow, 'default');
    str += "animation-duration: "+obj.desktop.dropdown.animation.duration+"s;"
    str += "}";
    let selector = "#"+key+" li.deeper.parent > ul:hover, "
    selector += "#"+key+" li.megamenu-item > .tabs-content-wrapper > .ba-section:hover";
    str += app.cssRules.getStateRule(selector, 'hover');
    selector = "#"+key+" li.deeper.parent > ul, "
    selector += "#"+key+" li.megamenu-item > .tabs-content-wrapper > .ba-section";
    str += app.cssRules.getTransitionRule("#"+selector);
    let padding = obj.desktop.dropdown.padding,
        top = padding.default ? padding.default.top : padding.top;
    str += "#"+key+" li.deeper.parent > ul > .deeper:hover > ul {";
    str += "top : -"+app.cssRules.getValueUnits(top)+";";
    str += "}";
    if (!disableResponsive) {
        str += "@media (max-width: "+menuBreakpoint+"px) {"
        str += "#"+key+" > .ba-hamburger-menu > .main-menu {";
        str += "background-color : "+getCorrectColor(obj.hamburger.background)+";";
        if (obj.hamburger.width) {
            str += "width: "+app.cssRules.getValueUnits(obj.hamburger.width)+";";
            str += app.cssRules.get('padding', obj.hamburger.padding, 'default');
            str += app.cssRules.get('shadow', obj.hamburger.shadow, 'default');
        }
        str += "}"
        if (obj.hamburger.overlay) {
            str += "#"+key+" > .ba-menu-backdrop {";
            str += "background-color : "+getCorrectColor(obj.hamburger.overlay)+";";
            str += "}"
        }
        str += "#"+key+" .ba-hamburger-menu .open-menu {";
        str += "color : "+getCorrectColor(obj.hamburger.open)+";";
        str += "text-align : "+obj.hamburger['open-align']+";";
        if (obj.hamburger.icons) {
            str += "font-size: "+app.cssRules.getValueUnits(obj.hamburger.icons.open.size)+";";
        }
        str += "}";
        str += "#"+key+" .ba-hamburger-menu .close-menu {";
        str += "color : "+getCorrectColor(obj.hamburger.close)+";";
        str += "text-align : "+obj.hamburger['close-align']+";";
        if (obj.hamburger.icons) {
            str += "font-size: "+app.cssRules.getValueUnits(obj.hamburger.icons.close.size)+";";
        }
        str += "}";
        str += "}";
    }
    $g('#'+key).find('> .ba-menu-wrapper').each(function(){
        $g(this).removeClass('vertical-menu ba-menu-position-left ba-hamburger-menu ba-collapse-submenu ba-menu-position-center');
        if (obj.hamburger.enable) {
            $g(this).addClass('ba-hamburger-menu');
        }
        if (obj.hamburger.collapse) {
            $g(this).addClass('ba-collapse-submenu');
        }
    }).addClass(obj.layout.layout).addClass(obj.hamburger.position);
    str += app.setMediaRules(obj, key, 'getMenuRules');

    return str;
}

function createLogoRules(obj, key)
{
    var str = getLogoRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getLogoRules');

    return str;
}

function createWeatherRules(obj, key)
{
    let desktop = obj.desktop,
        str = getWeatherRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getWeatherRules');
    str += "#"+key+" .weather-info .wind {";
    if (desktop.view.wind) {
        str += "display : inline;";
    } else {
        str += "display : none;";
    }
    str += "}";
    str += "#"+key+" .weather-info .humidity {";
    if (desktop.view.humidity) {
        str += "display : inline-block;";
    } else {
        str += "display : none;";
    }
    str += "}";
    str += "#"+key+" .weather-info .pressure {";
    if (desktop.view.pressure) {
        str += "display : inline-block;";
    } else {
        str += "display : none;";
    }
    str += "}";
    str += "#"+key+" .forecast:nth-child(n) {";
    str += "display : none;";
    str += "}";
    for (var i = 0; i < desktop.view.forecast; i++) {
        str += "#"+key+" .forecast:nth-child("+(i + 1)+")";
        if (i != desktop.view.forecast - 1 ){
            str += ","
        }
    }
    str += " {";
    if (desktop.view.layout == 'forecast-block') {
        str += "display: inline-block;";
    } else {
        str += "display: block;";
    }
    str += "}";

    return str;
}

function createScrollTopRules(obj, key)
{
    app.cssRules.prepareColors(obj);
    let str = getScrollTopRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getScrollTopRules');
    if (obj.type == 'scroll-to-top') {
        $g("#"+key).removeClass('scroll-btn-left scroll-btn-right').addClass('scroll-btn-'+obj.text.align);
    }

    return str;
}

function createCarouselRules(obj, key)
{
    var str = getCarouselRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getCarouselRules');
    str += "#"+key+" .ba-slideset-nav {";
    if (obj.desktop.view.arrows == 1) {
        str += 'display:block;';
    } else {
        str += 'display:none;';
    }
    str += "}";
    $g('#'+key+' ul').removeClass('caption-over caption-hover')
        .addClass(obj.desktop.caption.position).addClass(obj.desktop.caption.hover);

    return str;
}

function createTestimonialsRules(obj, key)
{
    app.cssRules.prepareColors(obj.desktop.arrows);
    let str = getTestimonialsRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getTestimonialsRules');
    for (var ind in obj.slides) {
        if (obj.slides[ind].image) {
            str += "#"+key+" li.item:nth-child("+ind+") .testimonials-img,";
            str += " #"+key+" ul.style-6 .ba-slideset-dots > div:nth-child("+ind+") {background-image: url(";
            if (app.isExternal(obj.slides[ind].image)) {
                str += obj.slides[ind].image+");";
            } else {
                str += JUri+encodeURI(obj.slides[ind].image)+");";
            }
            str += "}"; 
        }
    }
    str += "#"+key+" .ba-slideset-nav {";
    str += 'display:'+(obj.desktop.view.arrows == 1 ? 'block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-slideset-dots {";
    str += 'display:'+(obj.desktop.view.dots == 1 ? 'flex' : 'none')+';';
    str += "}";

    return str;
}

function createRecentSliderRules(obj, key)
{
    if (!obj.info) {
        obj.info = ['author', 'date', 'category', 'hits', 'comments'];
    }
    if (!obj.desktop.store) {
        obj.desktop.store = {
            badge: true,
            wishlist: true,
            price: true,
            cart: true
        }
    }
    var str = getRecentSliderRules(obj.desktop, key);
    if (obj.fields) {
        for (let i = 0; i < obj.fields.length; i++) {
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+obj.fields[i]+'"] {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    if (obj.info) {
        for (let i = 0; i < obj.info.length; i++) {
            str += '#'+key+' .ba-blog-post-'+obj.info[i]+' {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    str += app.setMediaRules(obj, key, 'getRecentSliderRules');
    let desktop = obj.desktop;
    if (!'author' in desktop.view) {
        desktop.view.author = false;
    }
    if (!'comments' in desktop.view) {
        desktop.view.comments = false;
    }
    if (!'reviews' in desktop.view) {
        desktop.view.reviews = false;
    }
    str += "#"+key+" .ba-blog-post-info-wrapper span.ba-blog-post-author {";
    str += 'display:'+(desktop.view.author ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper span.ba-blog-post-date {";
    str += 'display:'+(desktop.view.date ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper span.ba-blog-post-category {";
    str += 'display:'+(desktop.view.category ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper span.ba-blog-post-comments {";
    str += 'display:'+(desktop.view.comments ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper span.ba-blog-post-hits {";
    str += 'display:'+(desktop.view.hits ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-reviews {";
    str += 'display:'+(desktop.view.reviews ? 'flex' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-product-options {";
    str += "display: none;";
    str += "}";
    if (desktop.store) {
        str += "#"+key+" .ba-blog-post-badge-wrapper {";
        str += "display:"+(desktop.store.badge ? "flex" : "none")+";";
        str += "}";
        str += "#"+key+" .ba-blog-post-wishlist-wrapper {";
        str += "display:"+(desktop.store.wishlist ? "flex" : "none")+";";
        str += "}";
        str += "#"+key+" .ba-blog-post-add-to-cart-price {";
        str += "display:"+(desktop.store.price ? "flex" : "none")+";";
        str += "}";
        str += "#"+key+" .ba-blog-post-add-to-cart-button {";
        str += "display:"+(desktop.store.cart ? "flex" : "none")+";";
        str += "}";
        for (let ind in desktop.store) {
            if (ind == 'badge' || ind == 'wishlist' || ind == 'price' || ind == 'cart') {
                continue;
            }
            str += "#"+key+' .ba-blog-post-product-options[data-key="'+ind+'"] {';
            str += "display:"+(desktop.store[ind] ? "flex" : "none")+";";
            str += "}";
        }
    }
    for (let i = 0; i < obj.info.length; i++) {
        if (desktop.view[obj.info[i]]) {
            for (let j = i + 1; j < obj.info.length; j++) {
                str += "#"+key+" .ba-blog-post-"+obj.info[j]+":before {";
                str += 'margin: 0 10px; content: "'+(obj.info[j] == 'author' ? '' : '\\2022')+'"; color: inherit;';
                str += "}";
            }
            break;
        }
    }
    str += "#"+key+" .ba-blog-post-field-row {";
    str += "display: none;"
    str += "}";
    if (desktop.fields) {
        let visibleField = null;
        for (let i = 0; i < obj.fields.length; i++) {
            if (desktop.fields[obj.fields[i]]) {
               visibleField = obj.fields[i];
            }
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+obj.fields[i]+'"] {';
            str += "display: "+(desktop.fields[obj.fields[i]] ? 'flex' : 'none')+";";
            str += "margin-bottom: 10px;";
            str += "}";
        }
        if (visibleField) {
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+visibleField+'"] {';
            str += "margin-bottom: 0;";
            str += "}";
        }
    }
    str += "#"+key+" .ba-blog-post-intro-wrapper {";
    str += 'display:'+(desktop.view.intro ? 'block' : 'none')+';'
    str += "}";
    str += "#"+key+" .ba-blog-post-title {";
    str += 'display:'+(desktop.view.title ? 'block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-blog-post-button-wrapper a {";
    str += 'display:'+(desktop.view.button ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .ba-slideset-nav {";
    str += 'display:'+(desktop.view.arrows == 1 ? 'block' : 'none')+';';
    str += "}";
    $g('#'+key+' ul').removeClass('caption-over caption-hover')
        .addClass(obj.desktop.caption.position).addClass(obj.desktop.caption.hover);

    return str;
}

function createContentRules(obj, key)
{
    app.cssRules.prepareColors(obj.desktop.arrows);
    let str = getContentSliderRules(obj.desktop, key),
        slideStr = '';
    str += app.setMediaRules(obj, key, 'getContentSliderRules');
    for (var ind in obj.slides) {
        slideStr = "#"+key+" > .slideshow-wrapper > .ba-slideshow > .slideshow-content > li.item:nth-child("+ind+")";
        str += getContentSliderItemsRules(obj.slides[ind].desktop, slideStr);
        str += app.setMediaRules(obj.slides[ind], slideStr, 'getContentSliderItemsRules');
    }

    return str;
}

function createFeatureBoxRules(obj, key)
{
    let str = getFeatureBoxRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getFeatureBoxRules');
    for (let ind in obj.items) {
        if (obj.items[ind].type == 'image' && obj.items[ind].image) {
            str += "#"+key+" .ba-feature-box:nth-child("+(ind * 1 + 1)+") .ba-feature-image {background-image: url(";
            if (app.isExternal(obj.items[ind].image)) {
                str += obj.items[ind].image+");";
            } else {
                str += JUri+encodeURI(obj.items[ind].image)+");";
            }
            str += "}";
        }
    }

    return str;
}

function createBeforeAfterSliderRules(obj, key)
{
    let str = getBeforeAfterSliderRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getBeforeAfterSliderRules');

    return str;
}

function createSlideshowRules(obj, key)
{
    let str = getSlideshowRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getSlideshowRules');
    if (obj.type == 'field-slideshow' || obj.type == 'product-slideshow') {
        str += "body.com_gridbox.gridbox #"+key+" li.item .ba-slideshow-img,"
        str += "body.com_gridbox.gridbox #"+key+" .thumbnails-dots div {";
        str += "background-image: url("+JUri+"components/com_gridbox/assets/images/default-theme.png);"
        str += "}";
    }
    str += "#"+key+" .ba-slideshow-nav {";
    if (obj.desktop.view.arrows == 1) {
        str += 'display:block;';
    } else {
        str += 'display:none;';
    }
    str += "}";

    return str;
}

function createAccordionRules(obj, key)
{
    var str = getAccordionRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getAccordionRules');

    return str;
}

function createTabsRules(obj, key)
{
    var str = getTabsRules(obj.desktop, key);
    str += "#"+key+" ul.nav.nav-tabs li a:hover {";
    str += "color : "+getCorrectColor(obj.desktop.hover.color)+";";
    str += "}";
    if (obj.desktop.icon.position == 'icon-position-left') {
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span {direction: rtl;display: inline-flex;'
        str += 'flex-direction: row;}';
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span i {margin-bottom:0;}';
    } else if (obj.desktop.icon.position == 'icon-position-top') {
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span {display: inline-flex;';
        str += 'flex-direction: column-reverse;}';
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span i {margin-bottom:10px;}';
    } else {
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span {direction: ltr;display: inline-flex;'
        str += 'flex-direction: row;}';
        str += '#'+key+' .ba-tabs-wrapper > ul li a > span i {margin-bottom:0;}';
    }
    str += app.setMediaRules(obj, key, 'getTabsRules');

    return str;
}

function createMapRules(obj, key)
{
    var str = getMapRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getMapRules');

    return str;
}

function createCounterRules(obj, key)
{
    var str = getCounterRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getCounterRules');

    return str;
}

function createCountdownRules(obj, key)
{
    var str = getCountdownRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getCountdownRules');

    return str;
}

function createSearchRules(obj, key)
{
    let str = getSearchRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getSearchRules');
    $g('#'+key).find('.ba-search-wrapper').removeClass('after').addClass(obj.desktop.icons.position);

    return str;
}

function createCheckoutFormRules(obj, key)
{
    let str = getCheckoutFormRules(obj.desktop, key);
    if (obj.fields) {
        for (let i = 0; i < obj.fields.length; i++) {
            str += '#'+key+' .blog-post-submission-form-options-group[data-field-key="'+obj.fields[i]+'"] {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    str += app.setMediaRules(obj, key, 'getCheckoutFormRules');

    return str;
}

function createLoginRules(obj, key)
{
    let str = getLoginRules(obj.desktop, key);
    if (!obj.options.login) {
        str += "#"+key+" .ba-login-footer-wrapper, #"+key+" .ba-login-wrapper {";
        str += "display: none;";
        str += "}";
    }
    if (!obj.options.username) {
        str += "#"+key+' .ba-login-forgot-wrapper [data-step="forgot-username"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.options.password) {
        str += "#"+key+' .ba-login-forgot-wrapper [data-step="forgot-password"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.options.registration) {
        str += "#"+key+' .ba-create-account-wrapper, #'+key+' .ba-login-wrapper .ba-login-footer-wrapper {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.options.login && obj.options.registration) {
        str += "#"+key+' .ba-create-account-wrapper {';
        str += "display: flex !important;";
        str += "}";
    }
    if (!obj.facebook.enable) {
        str += "#"+key+' .ba-login-integration-btn[data-integration="facebook"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.google.enable) {
        str += "#"+key+' .ba-login-integration-btn[data-integration="google"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.acceptance.enable) {
        str += "#"+key+' .ba-login-acceptance-wrapper {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.google.enable && !obj.facebook.enable) {
        str += "#"+key+' .ba-login-integrations-wrapper, #'+key+' .ba-login-or-wrapper {';
        str += "display: none;";
        str += "}";
    } else if (!obj.google.enable || !obj.facebook.enable) {
        $g("#"+key+' .ba-login-integrations-wrapper').addClass('ba-login-integrations-hidden-element')
    } else {
        $g("#"+key+' .ba-login-integrations-wrapper').removeClass('ba-login-integrations-hidden-element')
    }
    str += app.setMediaRules(obj, key, 'getLoginRules');

    return str;
}

function createIconListRules(obj, key)
{
    var str = getIconListRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getIconListRules');
    str += "#"+key+" .ba-icon-list-wrapper ul li a:hover span {";
    str += "color : inherit;";
    str += "}";
    str += "#"+key+" .ba-icon-list-wrapper ul li i, #"+key+" ul li a:before, #"+key+" ul li.list-item-without-link:before {";
    str += "order: "+(obj.icon.position == '' ? 0 : 2)+";";
    str += "margin-"+(obj.icon.position == '' ? 'right' : 'left')+": 20px;";
    str += "}";

    return str;
}

function createHotspotRules(obj, key)
{
    let str = getHotspotRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getHotspotRules');

    return str;
}

function createButtonRules(obj, key)
{
    let str = '';
    if (obj.type == 'overlay-button' && obj.trigger == 'image') {
        str = getImageRules(obj.desktop, key);
        str += app.setMediaRules(obj, key, 'getImageRules');
    } else {
        app.cssRules.prepareColors(obj);
        str = getButtonRules(obj.desktop, key);
        if (typeof(obj.icon) == 'object') {
            str += "#"+key+" .ba-button-wrapper a {";
            if (obj.icon.position == '') {
                str += 'flex-direction: row-reverse;';
            } else {
                str += 'flex-direction: row;';
            }
            str += "}";
            if (obj.icon.position == '') {
                str += "#"+key+" .ba-button-wrapper a i {";
                str += 'margin: 0 10px 0 0;';
                str += "}";
            } else {
                str += "#"+key+" .ba-button-wrapper a i {";
                str += 'margin: 0 0 0 10px;';
                str += "}";
            }
        }
        str += app.setMediaRules(obj, key, 'getButtonRules');
    }

    return str;
}

function createRecentCommentsRules(obj, key)
{
    let desktop = obj.desktop,
        str = getRecentCommentsRules(desktop, key, obj.type);
    str += app.setMediaRules(obj, key, 'getRecentCommentsRules');
    str += "#"+key+" .ba-blog-post-image {";
    str += "display:"+(desktop.view.image ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-date {";
    str += "display:"+(desktop.view.date ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-intro-wrapper {";
    str += "display:"+(desktop.view.intro ? "block" : "none")+";";
    str += "}";
    if ('source' in desktop.view) {
        str += "#"+key+" .ba-reviews-source {";
        str += "display:"+(desktop.view.source ? "inline-block" : "none")+";";
        str += "}";
        str += "#"+key+" .ba-reviews-name {";
        str += "display:"+(desktop.view.title ? "inline-block" : "none")+";";
        str += "}";
        str += "#"+key+" .ba-blog-post-title-wrapper {";
        str += "display:"+(desktop.view.title || desktop.view.source ? "block" : "none")+";";
        str += "}";
    } else {
        str += "#"+key+" .ba-blog-post-title-wrapper {";
        str += "display:"+(desktop.view.title ? "block" : "none")+";";
        str += "}";
    }

    return str;
}

function createCategoriesRules(obj, key)
{
    let desktop = obj.desktop,
        str = getCategoriesRules(desktop, key);
    str += "#"+key+" .ba-blog-post-title a:hover, ";
    str += "#"+key+" .ba-blog-post.active .ba-blog-post-title a, ";
    str += "#"+key+" .ba-blog-post-title i.collapse-categories-list:hover {";
    str += "color: "+getCorrectColor(obj.desktop.title.hover.color)+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper a:hover, ";
    str += "#"+key+" .ba-blog-post-info-wrapper a.active, ";
    str += "#"+key+" .ba-blog-post-info-wrapper i.collapse-categories-list:hover {";
    str += "color: "+getCorrectColor(obj.desktop.info.hover.color)+";";
    str += "}";
    str += app.setMediaRules(obj, key, 'getCategoriesRules');
    str += "#"+key+" .ba-blog-post-image {";
    str += "display:"+(desktop.view.image ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-title {";
    str += "display:"+(desktop.view.title ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-intro-wrapper {";
    str += "display:"+(desktop.view.intro ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-app-category-counter {";
    str += "display:"+(desktop.view.counter ? "inline" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper {";
    str += "display:"+(desktop.view.sub ? "block" : "none")+";";
    str += "}";
    if (!desktop.view.sub) {
        str += "#"+key+" .collapse-categories-list {";
        str += "display:none;";
        str += "}";
    }

    return str;
}

function createAddToCartRules(obj, key)
{
    var desktop = obj.desktop,
        str = getAddToCartRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getAddToCartRules');
    str += "#"+key+" .ba-add-to-cart-stock {";
    str += "display:"+(desktop.view.availability ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-add-to-cart-sku {";
    str += "display:"+(desktop.view.sku ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-add-to-cart-quantity {";
    str += "display:"+(desktop.view.quantity ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-add-to-cart-button-wrapper a {";
    str += "display:"+(desktop.view.button ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-add-to-wishlist {";
    str += "display:"+(desktop.view.wishlist ? "flex" : "none")+";";
    str += "}";

    return str;
}

function createBlogPostsRules(obj, key)
{
    let desktop = obj.desktop
    if (!desktop.store) {
        desktop.store = {
            badge: true,
            wishlist: true,
            price: true,
            cart: true
        }
    }
    let str = getBlogPostsRules(obj.desktop, key, obj.type);
    str += "#"+key+" .ba-blog-post-title a:hover {";
    str += "color: "+getCorrectColor(obj.desktop.title.hover.color)+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-info-wrapper > * a:hover, #"+key+" .ba-post-navigation-info a:hover {";
    str += "color: "+getCorrectColor(obj.desktop.info.hover.color)+";";
    str += "}";
    if (obj.fields) {
        for (let i = 0; i < obj.fields.length; i++) {
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+obj.fields[i]+'"] {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    if (obj.info) {
        for (let i = 0; i < obj.info.length; i++) {
            str += '#'+key+' .ba-blog-post-'+obj.info[i]+' {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    str += app.setMediaRules(obj, key, 'getBlogPostsRules');
    if (!('author' in desktop.view)) {
        desktop.view.author = false;
    }
    if (!('comments' in desktop.view)) {
        desktop.view.comments = false;
    }
    if (!('reviews' in desktop.view)) {
        desktop.view.reviews = false;
    }
    str += "#"+key+" .blog-posts-sorting-wrapper {";
    str += "display:"+(desktop.view.sorting ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-image {";
    str += "display:"+(desktop.view.image ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-title-wrapper {";
    str += "display:"+(desktop.view.title ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-author {";
    str += "display:"+(desktop.view.author ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-date {";
    str += "display:"+(desktop.view.date ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-category {";
    str += "display:"+(desktop.view.category ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-comments {";
    str += "display:"+(desktop.view.comments ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-hits {";
    str += "display:"+(desktop.view.hits ? "inline-block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-product-options {";
    str += "display: none;";
    str += "}";
    str += "#"+key+" .ba-blog-post-badge-wrapper {";
    str += "display:"+(desktop.store.badge ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-wishlist-wrapper {";
    str += "display:"+(desktop.store.wishlist ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-add-to-cart-price {";
    str += "display:"+(desktop.store.price ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-add-to-cart-button {";
    str += "display:"+(desktop.store.cart ? "flex" : "none")+";";
    str += "}";
    for (let ind in desktop.store) {
        if (ind == 'badge' || ind == 'wishlist' || ind == 'price' || ind == 'cart') {
            continue;
        }
        str += "#"+key+' .ba-blog-post-product-options[data-key="'+ind+'"] {';
        str += "display:"+(desktop.store[ind] ? "flex" : "none")+";";
        str += "}";
    }
    if (!obj.info) {
        obj.info = ['author', 'date', 'category', 'hits', 'comments'];
    }
    let visible = false;
    for (let i = 0; i < obj.info.length; i++) {
        if (desktop.view[obj.info[i]]) {
            for (let j = i + 1; j < obj.info.length; j++) {
                str += "#"+key+" .ba-blog-post-"+obj.info[j]+":before {";
                str += 'margin: 0 10px;content: "'+(obj.info[j] == 'author' ? '' : '\\2022')+'";color: inherit;';
                str += "}";
            }
            str += "#"+key+" .ba-blog-post-info-wrapper {";
            str += '--visible-info: 1;';
            str += "}";
            visible = true;
            break;
        }
    }
    if (!visible) {
        str += "#"+key+" .ba-blog-post-info-wrapper {";
        str += '--visible-info: 0;';
        str += "}";
    }
    str += "#"+key+" .ba-blog-post-reviews {";
    str += "display:"+(desktop.view.reviews ? "flex" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-intro-wrapper {";
    str += "display:"+(desktop.view.intro ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-blog-post-field-row {";
    str += "display: none;"
    str += "}";
    if (desktop.fields) {
        let visibleField = null;
        for (let i = 0; i < obj.fields.length; i++) {
            if (desktop.fields[obj.fields[i]]) {
               visibleField = obj.fields[i];
            }
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+obj.fields[i]+'"] {';
            str += "display: "+(desktop.fields[obj.fields[i]] ? 'flex' : 'none')+";";
            str += "margin-bottom: 10px;";
            str += "}";
        }
        if (visibleField) {
            str += '#'+key+' .ba-blog-post-field-row[data-id="'+visibleField+'"] {';
            str += "margin-bottom: 0;";
            str += "}";
        }
    }
    str += "#"+key+" .ba-blog-post-button-wrapper {";
    str += "display:"+(desktop.view.button ? "block" : "none")+";";
    str += "}";

    return str;
}

function createAuthorRules(obj, key)
{
    var str = getAuthorRules(obj.desktop, key);
    str += "#"+key+" .ba-post-author-title a:hover {";
    str += "color: "+getCorrectColor(obj.desktop.title.hover.color)+";";
    str += "}";
    str += "#"+key+" .ba-post-author-image {";
    str += "display:"+(obj.desktop.view.image ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-post-author-title-wrapper {";
    str += "display:"+(obj.desktop.view.title ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" .ba-post-author-description {";
    str += "display:"+(obj.desktop.view.intro ? "block" : "none")+";";
    str += "}";
    str += app.setMediaRules(obj, key, 'getAuthorRules');

    return str;
}

function createPostIntroRules(obj, key)
{
    if (!obj.info) {
        obj.info = ['author', 'date', 'category', 'comments', 'hits', 'reviews'];
    }
    let desktop = obj.desktop,
        str = getPostIntroRules(desktop, key);
    str += "#"+key+" .intro-post-wrapper .intro-post-info > * a:hover {";
    str += "color: "+getCorrectColor(obj.desktop.info.hover.color)+";";
    str += "}";
    if (obj.info) {
        for (let i = 0; i < obj.info.length; i++) {
            str += '#'+key+' .intro-post-'+obj.info[i]+' {';
            str += "order: "+i+";";
            str += "}";
        }
    }
    str += app.setMediaRules(obj, key, 'getPostIntroRules');
    str += "#"+key+" .intro-post-info {";
    if (typeof(desktop.info.show) != 'undefined') {
        str += 'display:'+(desktop.info.show ? 'block' : 'none')+';';
    }
    str += "}";
    str += "#"+key+" .intro-post-info *:not(i):not(a) {";
    if (typeof(desktop.info.show) != 'undefined') {
        str += 'display:'+(desktop.info.show ? 'block' : 'none')+';';
    }
    str += "}";    
    if (!('author' in desktop.view)) {
        desktop.view.author = false;
    }
    if (!('comments' in desktop.view)) {
        desktop.view.comments = false;
    }
    if (!('reviews' in desktop.view)) {
        desktop.view.reviews = false;
    }
    str += "#"+key+" .intro-post-wrapper:not(.fullscreen-post) .intro-post-image-wrapper {";
    str += 'display:'+(desktop.image.show ? 'block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-title-wrapper {";
    str += 'display:'+(desktop.title.show ? 'block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-author {";
    str += 'display:'+(desktop.view.author ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-date {";
    str += 'display:'+(desktop.view.date ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-category {";
    str += 'display:'+(desktop.view.category ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-comments {";
    str += 'display:'+(desktop.view.comments ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-hits {";
    str += 'display:'+(desktop.view.hits ? 'inline-block' : 'none')+';';
    str += "}";
    str += "#"+key+" .intro-post-reviews {";
    str += 'display:'+(desktop.view.reviews ? 'inline-flex' : 'none')+';';
    str += "}";
    for (let i = 0; i < obj.info.length; i++) {
        if (desktop.view[obj.info[i]]) {
            for (let j = i + 1; j < obj.info.length; j++) {
                str += "#"+key+" .intro-post-"+obj.info[j]+":before {";
                str += 'margin: 0 10px;content: "'+(obj.info[j] == 'author' ? '' : '\\2022')+'";color: inherit;';
                str += "}";
            }
            break;
        }
    }
    $g('#'+key).find('.intro-post-wrapper').removeClass('fullscreen-post').addClass(obj.layout.layout);

    return str;
}

function createIconRules(obj, key)
{
    app.cssRules.prepareColors(obj);
    let str = getIconRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getIconRules');

    return str;
}

function createStarRatingsRules(obj, key)
{
    let desktop = obj.desktop,
        str = getStarRatingsRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getStarRatingsRules');
    str += "#"+key+" .rating-wrapper {";
    if (desktop.view.rating == 1) {
        str += 'display: inline;';
    } else {
        str += 'display: none;';
    }
    str += "}";
    str += "#"+key+" .votes-wrapper {";
    if (desktop.view.votes == 1) {
        str += 'display: inline;';
    } else {
        str += 'display: none;';
    }
    str += "}";

    return str;
}

function createSimpleGalleryRules(obj, key)
{
    var str = getSimpleGalleryRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getSimpleGalleryRules');
    str += '#'+key+' .ba-instagram-image {';
    str += 'cursor: zoom-in;';
    str += '}';

    return str;
}

function createErrorRules(obj, key)
{
    let desktop = obj.desktop,
        str = getErrorRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getErrorRules');

    str += "#"+key+" h1.ba-error-code {";
    str += "display: "+(desktop.view.code ? "block" : "none")+";";
    str += "}";
    str += "#"+key+" p.ba-error-message {";
    str += "display: "+(desktop.view.message ? "block" : "none")+";";
    str += "}";

    return str;
}

function createSearchHeadlineRules(obj, key)
{
    var str = getSearchHeadlineRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getSearchHeadlineRules');

    return str;
}


function createTextRules(obj, key)
{
    var array = ['h1' ,'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'links'];
    if (obj.global) {
        delete(obj.global);
        array.forEach(function(el){
            delete(obj.desktop[el]);
            for (var ind in breakpoints) {
                if (obj[ind]) {
                    delete(obj[ind][el]);
                }
            }
        });
    }
    if (!obj.desktop.p) {
        array.forEach(function(el){
            if (el != 'links') {
                obj.desktop[el] = {
                    "font-family" : "@default",
                    "font-weight" : "@default"
                };
                for (var ind in breakpoints) {
                    if (!obj[ind]) {
                        obj[ind] = {};
                    }
                    obj[ind][el] = {};
                }
            }
        });
    }
    if (!obj.desktop.links) {
        obj.desktop.links = {};
    }
    var str = getTextRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getTextRules');

    return str;
}

function createProgressPieRules(obj, key)
{
    let str = getProgressPieRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getProgressPieRules');
    str += "#"+key+" .progress-pie-number {display: "+(obj.desktop.display.target ? 'inline-block;' : 'none;')+"}";

    return str;
}

function createReadingProgressBarRules(obj, key)
{
    let desktop = obj.desktop,
        str = getReadingProgressBarRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getReadingProgressBarRules');

    return str;
}

function createProgressBarRules(obj, key)
{
    let desktop = obj.desktop,
        str = getProgressBarRules(desktop, key);
    str += app.setMediaRules(obj, key, 'getProgressBarRules');
    str += "#"+key+" .progress-bar-title {display: "+(desktop.display.label ? 'inline-block;' : 'none;')+"}";
    str += "#"+key+" .progress-bar-number {display: "+(desktop.display.target ? 'inline-block;' : 'none;')+"}";

    return str;
}

function createSocialRules(obj, key)
{
    let str = getModulesRules(obj.desktop, key),
        keys = ['facebook', 'linkedin', 'pinterest', 'twitter', 'vk'];
    str += app.setMediaRules(obj, key, 'getModulesRules');
    str += '#'+key+' .social-counter {display:'+(obj.view.counters ? 'inline-block' : 'none')+'}';
    keys.forEach(function(social){
        if (!obj[social]) {
            str += '#'+key+' .ba-social .'+social+' {display:none;}';
        }
    })
    $g('#'+key).removeClass('ba-social-sidebar').each(function(){
        if (obj.view.layout == 'ba-social-sidebar') {
            if (this.parentNode.localName != 'body') {
                obj.parent = this.parentNode.id;
                document.body.appendChild(this);
            }
        } else {
            if (this.parentNode.localName == 'body') {
                var parent = document.getElementById(obj.parent);
                if (!parent) {
                    parent = document.querySelector('.ba-grid-column');
                    if (!parent) {
                        return false;
                    }
                }
                obj.parent = parent.id;
                $g(parent).find(' > .empty-item').before(this);
            }
        }
        let count = 0;
        for (let i = 0; i < keys.length; i++) {
            if (obj[keys[i]]) {
                count++;
            }
        }
        this.style.setProperty('--social-count', count);
    }).addClass(obj.view.layout).attr('data-size', obj.view.size).attr('data-style', obj.view.style)
        .find('.ba-social').removeClass('ba-social-sm ba-social-md ba-social-lg')
        .addClass(obj.view.size).removeClass('ba-social-classic ba-social-flat ba-social-circle ba-social-minimal')
        .addClass(obj.view.style);

    return str;
}

function createEventCalendarRules(obj, key)
{
    var str = getEventCalendarRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getEventCalendarRules');

    return str;
}

function createCommentsBoxRules(obj, key)
{
    var str = getCommentsBoxRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getCommentsBoxRules');
    if (!obj.view.user) {
        str += "#"+key+" .ba-user-login-wrapper {";
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.social) {
        str += "#"+key+" .ba-social-login-wrapper {";
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.guest) {
        str += "#"+key+" .ba-guest-login-wrapper {";
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.share) {
        str += "#"+key+" .comment-share-action {";
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.rating) {
        str += '#'+key+' .comment-likes-action-wrapper {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.files) {
        str += '#'+key+' .ba-comments-attachment-file-wrapper[data-type="file"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.images) {
        str += '#'+key+' .ba-comments-attachment-file-wrapper[data-type="image"] {';
        str += "display: none;";
        str += "}";
    }
    if (!obj.view.report) {
        str += '#'+key+' .comment-report-user-comment {';
        str += "display: none;";
        str += "}";
    }
    if (('reply' in obj.view) && !obj.view.reply) {
        str += '#'+key+' .comment-reply-action {';
        str += "display: none;";
        str += "}";
    }

    return str;
}

function createFieldRules(obj, key)
{
    var str = getFieldRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getFieldRules');
    $g('#'+key+' .ba-field-wrapper').removeClass('ba-label-position-right ba-label-position-left').addClass(obj.layout.position);

    return str;
}

function createFieldsFilterRules(obj, key)
{
    let desktop = obj.desktop,
        visibleField = null;
        str = getFieldsFilterRules(desktop, key);
    for (let i = 0; i < obj.fields.length; i++) {
        str += '#'+key+' .ba-field-filter[data-id="'+obj.fields[i]+'"] {';
        str += "order: "+i+";";
        str += "}";
    }
    if (obj.auto) {
        str += '#'+key+' .ba-items-filter-search-button {';
        str += 'display: none;';
        str += "}";
    }
    str += app.setMediaRules(obj, key, 'getFieldsFilterRules');

    str += "#"+key+" .ba-field-filter {";
    str += "display: none;"
    str += "}";
    for (let i = 0; i < obj.fields.length; i++) {
        if (desktop.fields[obj.fields[i]]) {
           visibleField = obj.fields[i];
        }
        str += '#'+key+' .ba-field-filter[data-id="'+obj.fields[i]+'"] {';
        str += "display: "+(desktop.fields[obj.fields[i]] ? 'flex' : 'none')+";";
        str += "}";
    }
    if (visibleField) {
        str += '#'+key+' .ba-field-filter[data-id="'+visibleField+'"] {';
        str += "margin-bottom: 0;";
        str += "}";
    }

    return str;
}

function createCurrencySwitcherRules(obj, key)
{
    let str = getCurrencySwitcherRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getCurrencySwitcherRules');

    return str;
}

function createLanguageSwitcherRules(obj, key)
{
    let str = getLanguageSwitcherRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getLanguageSwitcherRules');

    return str;
}

function createModulesRules(obj, key)
{
    let str = getModulesRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getModulesRules');

    return str;
}

function createPreloaderRules(obj, key)
{
    var str = getPreloaderRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getPreloaderRules');

    return str;
}

function createLottieRules(obj, key)
{
    var str = getLottieRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getLottieRules');

    return str;
}

function createImageRules(obj, key)
{
    var str = getImageRules(obj.desktop, key);
    if (obj.link && obj.link.link) {
        str += '#'+key+' .ba-image-wrapper { cursor: pointer; }';
    } else if (obj.popup) {
        str += '#'+key+' .ba-image-wrapper { cursor: zoom-in; }';
    } else {
        str += '#'+key+' .ba-image-wrapper { cursor: default; }';
    }
    str += app.setMediaRules(obj, key, 'getImageRules');

    return str;
}

function createVideoRules(obj, key)
{
    let str = getVideoRules(obj.desktop, key);
    str += app.setMediaRules(obj, key, 'getVideoRules');

    return str;
}

function createHeaderRules(obj, view)
{
    if (app.breakpoint == 'desktop' && obj.position != 'relative') {
        $g('body header.header').addClass('not-default-header');
    } else if (app.breakpoint == 'desktop') {
        $g('body header.header').removeClass('not-default-header');
    }
    var str = "body header.header {";
    str += "position:"+obj.position+";";
    str += "}";
    if (!obj.width) {
        obj.width = 250;
    }
    str += "body {";
    str += "--sidebar-menu-width:"+app.cssRules.getValueUnits(obj.width)+";";
    str += "}";
    str += "body.com_gridbox.gridbox header.header {";
    if (obj.position == 'fixed') {
        str += "width: calc(100% - var(--translate-border-left) - var(--translate-border-right));";
        str += "left: var(--translate-border-left);";
        str += "top: 40px;";
    } else {
        str += "width: 100%;";
        str += "left: 0;";
        str += "top: 0;";
    }
    if (obj.position == 'relative') {
        str += "z-index: auto;";
    } else {
        str += "z-index: 40;";
    }
    str += "}";
    str += "body.com_gridbox.gridbox header.header:hover, body.body-megamenu-editing.com_gridbox.gridbox header.header {";
    if (obj.position == 'relative') {
        str += "z-index: 32;";
    } else {
        str += "z-index: 40;";
    }
    str += "}";
    if (obj.position == 'fixed') {
        str += ".ba-container .header {margin-left: calc((100vw - 1280px)/2);";
        str += "max-width: 1170px;}";
    } else {
        str += ".ba-container .header {margin-left:0;max-width: none;}";
    }

    return str;
}

function createMegaMenuSectionRules(obj, key)
{
    if (!obj.desktop.full) {
        obj.desktop.full = {
            fullscreen: obj.desktop.fullscreen == '1'
        };
        if (obj['max-width']) {
            obj.desktop.full.fullwidth = obj['max-width'] == '100%';
            delete(obj['max-width']);
        }
        delete(obj.desktop.fullscreen);
        for (var ind in breakpoints) {
            if (obj[ind] && obj[ind].fullscreen) {
                obj[ind].full = {
                    fullscreen: obj[ind].fullscreen == '1'
                };
                delete(obj[ind].fullscreen);
            }
        }
        obj.view = {
            width: obj.width,
            position: obj.position
        }
        delete(obj.width);
        delete(obj.position);
    }
    var str = createMegaMenuRules(obj.desktop, key);
    if (obj.parallax) {
        var pHeight = 100 + obj.parallax.offset * 2 * 200,
            pTop = obj.parallax.offset * 2 * -100;
        str += "#"+key+" > .parallax-wrapper.scroll .parallax {";
        str += "height: "+pHeight+"%;"
        str += "top: "+pTop+"%;"
        str += "}";
    }
    str += "#"+key+" {width: "+app.cssRules.getValueUnits(obj.view.width)+"; }";
    str += '.tabs-content-wrapper[data-id="'+key+'"] {--megamenu-width: '+app.cssRules.getValueUnits(obj.view.width)+'; }';
    str += app.setMediaRules(obj, key, 'createMegaMenuRules');
    if (obj.desktop.background && obj.desktop.background.type != 'video') {
        $g('#'+key+' > .ba-video-background').remove();
    }
    if (!obj.desktop.full.fullwidth) {
        $g('#'+key).parent().addClass('ba-container');
    } else {
        $g('#'+key).parent().removeClass('ba-container');
    }
    $g('#'+key).parent().removeClass('megamenu-center').addClass(obj.view.position);
    
    return str;
}

function setFlipboxSide(obj, side)
{
    let array = ['background', 'background-states', 'overlay', 'overlay-states', 'image', 'video'],
        object = obj.sides[side];
    if (!object.desktop['overlay-states']) {
        object.desktop['overlay-states'] = {
            default: {
                type: object.desktop.overlay.type,
                color: object.desktop.overlay.color
            },
            state: false,
            transition: {
                duration: 0.3,
                x1: 0.42,
                y1: 0,
                x2: 0.58,
                y2: 1
            }
        }
    }
    if (!object.desktop['background-states']) {
        object.desktop['background-states'] = {
            default: {
                image: object.desktop.image.image,
                color: object.desktop.background.color
            },
            state: false,
            transition: {
                duration: 0.3,
                x1: 0.42,
                y1: 0,
                x2: 0.58,
                y2: 1
            }
        }
    }
    obj.parallax = object.parallax;
    for (let i = 0; i < array.length; i++) {
        obj.desktop[array[i]] = object.desktop[array[i]];
    }
    for (let ind in breakpoints) {
        if (!obj[ind]) {
            obj[ind] = {};
        }
        if (!object[ind]) {
            object[ind] = {};
        }
        for (let i = 0; i < array.length; i++) {
            if (!obj[ind][array[i]]) {
                obj[ind][array[i]] = {}
            }
            if (!object[ind][array[i]]) {
                object[ind][array[i]] = {}
            }
            obj[ind][array[i]] = object[ind][array[i]];
        }
    }
}

function createFlipboxRules(obj, key)
{
    setFlipboxSide(obj, obj.side);
    var str = getFlipboxRules(obj.desktop, key),
        object = $g.extend(true, {}, obj);
    str += app.setMediaRules(obj, key, 'getFlipboxRules');
    setFlipboxSide(object, 'frontside');
    var key1 = key+' > .ba-flipbox-wrapper > .ba-flipbox-frontside > .ba-grid-column-wrapper > .ba-grid-column';
    if (object.parallax) {
        var pHeight = 100 + object.parallax.offset * 2 * 200,
            pTop = object.parallax.offset * 2 * -100;
        str += "#"+key1+" > .parallax-wrapper.scroll .parallax {";
        str += "height: "+pHeight+"%;"
        str += "top: "+pTop+"%;"
        str += "}";
    }
    str += getFlipsidesRules(object.desktop, key1);
    str += app.setMediaRules(object, key1, 'getFlipsidesRules');
    if (object.desktop.background && object.desktop.background.type != 'video') {
        $g('#'+key1+' > .ba-video-background').remove();
    }
    setFlipboxSide(object, 'backside');
    key1 = key+' > .ba-flipbox-wrapper > .ba-flipbox-backside > .ba-grid-column-wrapper > .ba-grid-column';
    if (object.parallax) {
        var pHeight = 100 + object.parallax.offset * 2 * 200,
            pTop = object.parallax.offset * 2 * -100;
        str += "#"+key1+" > .parallax-wrapper.scroll .parallax {";
        str += "height: "+pHeight+"%;"
        str += "top: "+pTop+"%;"
        str += "}";
    }
    str += getFlipsidesRules(object.desktop, key1);
    str += app.setMediaRules(object, key1, 'getFlipsidesRules');
    if (object.desktop.background && object.desktop.background.type != 'video') {
        $g('#'+key1+' > .ba-video-background').remove();
    }
    
    return str;
}

function createSectionRules(obj, key)
{
    if (obj.type == 'row' && !obj.desktop.view) {
        obj.desktop.view = {
            gutter: obj.desktop.gutter == '1'
        }
        delete(obj.desktop.gutter);
        for (var ind in breakpoints) {
            if (obj[ind] && obj[ind].gutter) {
                obj[ind].view = {
                    gutter: obj[ind].gutter == '1'
                };
                delete(obj[ind].gutter);
            }
        }
    }
    if (!obj.desktop.full) {
        obj.desktop.full = {
            fullscreen: obj.desktop.fullscreen == '1'
        };
        if (obj['max-width']) {
            obj.desktop.full.fullwidth = obj['max-width'] == '100%';
            delete(obj['max-width']);
        }
        delete(obj.desktop.fullscreen);
        obj.desktop.image = {
            image: obj.desktop.background.image.image
        };
        for (var ind in breakpoints) {
            if (obj[ind]) {
                if (obj[ind].fullscreen) {
                    obj[ind].full = {
                        fullscreen: obj[ind].fullscreen == '1'
                    };
                    delete(obj[ind].fullscreen);
                }
                if (obj[ind].background && obj[ind].background.image && obj[ind].background.image.image) {
                    obj[ind].image = {
                        image: obj[ind].background.image.image
                    };
                }
            }
        }
        if (obj.type == 'column') {
            for (var ind in breakpoints) {
                if (obj[ind] && obj[ind]['column-width']) {
                    obj[ind].span = {
                        width: obj[ind]['column-width']
                    }
                    delete(obj[ind]['column-width']);
                }
            }
        } else if (obj.type == 'overlay-section') {
            obj.lightbox = {
                layout: obj.layout,
                background: obj['background-overlay']
            }
            delete(obj.layout);
            delete(obj['background-overlay']);
        } else if (obj.type == 'lightbox') {
            obj.lightbox = {
                layout: obj.position,
                background: obj['background-overlay']
            }
            delete(obj.position);
            delete(obj['background-overlay']);
        } else if (obj.type == 'cookies') {
            obj.lightbox = {
                layout: obj.layout,
                position: obj.position
            }
            delete(obj.layout);
            delete(obj.position);
        }
        if (obj.desktop.width) {
            obj.desktop.view = {
                width: obj.desktop.width
            };
            delete(obj.desktop.width);
            if (obj.desktop.height) {
                obj.desktop.view.height = obj.desktop.height;
                delete(obj.desktop.height);
            }
            for (var ind in breakpoints) {
                if (obj[ind]) {
                    obj[ind].view = {};
                    if (obj[ind].width) {
                        obj[ind].view.width = obj[ind].width;
                        delete(obj[ind].width);
                    }
                    if (obj[ind].height) {
                        obj[ind].view.height = obj[ind].height;
                        delete(obj[ind].height);
                    }
                }
            }
        }
    }
    app.cssRulesFlag = 'desktop';
    var str = createPageRules(obj.desktop, key, obj.type);
    if (obj.type == 'footer') {
        app.footer = obj;
    }
    if (obj.type == 'lightbox') {
        str += ".ba-lightbox-backdrop[data-id="+key+"] .close-lightbox {";
        str += "color: "+getCorrectColor(obj.close.color)+";";
        str += "text-align: "+obj.close['text-align']+";";
        str += "}";
        str += "body.gridbox .ba-lightbox-backdrop[data-id="+key+"] > .ba-lightbox-close {";
        str += "background-color: "+getCorrectColor(obj.lightbox.background)+";";
        str += "}";
        str += "body:not(.gridbox) .ba-lightbox-backdrop[data-id="+key+"] {";
        str += "background-color: "+getCorrectColor(obj.lightbox.background)+";";
        str += "}";
        $g('#'+key).closest('.ba-lightbox-backdrop')
            .removeClass('lightbox-top-left lightbox-top-right lightbox-center lightbox-bottom-left lightbox-bottom-right')
            .addClass(obj.lightbox.layout);
    } else if (obj.type == 'overlay-section') {
        str += ".ba-overlay-section-backdrop[data-id="+key+"] .close-overlay-section {";
        str += "color: "+getCorrectColor(obj.close.color)+";";
        str += "text-align: "+obj.close['text-align']+";";
        str += "}";
        str += "body.gridbox .ba-overlay-section-backdrop[data-id="+key+"] > .ba-overlay-section-close {";
        str += "background-color: "+getCorrectColor(obj.lightbox.background)+";";
        str += "}";
        str += "body:not(.gridbox) .ba-overlay-section-backdrop[data-id="+key+"] {";
        str += "background-color: "+getCorrectColor(obj.lightbox.background)+";";
        str += "}";
        $g('#'+key).closest('.ba-overlay-section-backdrop')
            .removeClass('vertical-right vertical-left horizontal-top horizontal-bottom lightbox').addClass(obj.lightbox.layout);
    } else if (obj.type == 'cookies') {
        $g('#'+key).closest('.ba-lightbox-backdrop')
            .removeClass('notification-bar-top notification-bar-bottom lightbox-top-left lightbox-top-right lightbox-bottom-left')
            .removeClass('lightbox-bottom-right').addClass(obj.lightbox.position);
    }
    if (obj.parallax) {
        var pHeight = 100 + obj.parallax.offset * 2 * 200,
            pTop = obj.parallax.offset * 2 * -100;
        str += "#"+key+" > .parallax-wrapper.scroll .parallax {";
        str += "height: "+pHeight+"%;"
        str += "top: "+pTop+"%;"
        str += "}";
    }
    app.cssRulesFlag = 'tablet';
    str += app.setMediaRules(obj, key, 'createPageRules');
    if (obj.desktop.background && obj.desktop.background.type != 'video') {
        $g('#'+key+' > .ba-video-background').remove();
    }
    if (obj.type != 'column' && 'fullwidth' in obj.desktop.full) {
        if (!obj.desktop.full.fullwidth) {
            $g('#'+key).parent().addClass('ba-container');
        } else {
            $g('#'+key).parent().removeClass('ba-container');
        }
    }
    if (obj.type == 'row') {
        if (obj.desktop.view.gutter) {
            $g('#'+key).removeClass('no-gutter-desktop');
        } else {
            $g('#'+key).addClass('no-gutter-desktop');
        }
    } else if (obj.type == 'column') {
        var parent = $g('#'+key).parent();
        for (var ind in breakpoints) {
            var name = ind.replace('tablet-portrait', 'ba-tb-pt-').replace('tablet', 'ba-tb-la-')
                .replace('phone-portrait', 'ba-sm-pt-').replace('phone', 'ba-sm-la-');
            if (obj[ind] && obj[ind].span && obj[ind].span.width) {
                for (var i = 1; i <= 12; i++) {
                    parent.removeClass(name+i);
                }
                parent.addClass(name+obj[ind].span.width);
            }
            name += 'order-';
            if (obj[ind] && obj[ind].span && obj[ind].span.order) {
                for (var i = 1; i <= 12; i++) {
                    parent.removeClass(name+i);
                }
                parent.addClass(name+obj[ind].span.order);
            }
        }
    }
    
    return str;
}

function createFooterStyle(obj)
{
    var str = "";
    for (var key in obj) {
        switch(key) {
            case 'links' : 
                str += "body footer a {";
                str += "color : "+getCorrectColor(obj[key].color)+";";
                str += "}";
                str += "body footer a:hover {";
                str += "color : "+getCorrectColor(obj[key]['hover-color'])+";";
                str += "}";
                break;
            case 'body':
                str += "body footer, footer ul, footer ol, footer table, footer blockquote";
                str += " {";
                str += getTypographyRule(obj[key]);
                str += "}";
                break;
            case 'p' :
            case 'h1' :
            case 'h2' :
            case 'h3' :
            case 'h4' :
            case 'h5' :
            case 'h6' :
                str += "footer "+key;
                str += " {";
                str += getTypographyRule(obj[key]);
                str += "}";
                break;
        }
    }
    return str;
}

function createMegaMenuRules(obj, selector)
{
    var str = "#"+selector+" {";
    str += "min-height: 50px;";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    str += "}";
    str += app.backgroundRule(obj, '#'+selector);
    str += 'li.deeper > .tabs-content-wrapper[data-id="'+selector+'"] + a > i.ba-icon-caret-right {';
    if (obj.disable == 1) {
        str += 'display: none;';
    } else {
        str += 'display: inline-block;';
    }
    str += "}";
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    str += setBoxModel(obj, selector);

    return str;
}

function getFlipboxRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" > .ba-flipbox-wrapper {"
    str += "height: "+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += "#"+selector+" > .ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column {"
    if (obj.full.fullscreen) {
        str += "justify-content: center;";
        str += "min-height: 100vh;";
    } else {
        str += "min-height: 50px;";
    }
    str += "}";
    str += "#"+selector+" > .ba-flipbox-wrapper > .column-wrapper {"
    str += "transition-duration: "+obj.animation.duration+"s;"
    str += "}";
    str += setItemsVisability(obj.disable, "block", "#"+selector);
    str += setBoxModel(obj, selector);

    return str;
}


function getFlipsidesRules(obj, selector)
{
    var str = '#'+selector+" {"
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.backgroundRule(obj, '#'+selector);

    return str;
}

function createPageRules(obj, selector, type)
{
    var str = "#"+selector+" {";
    for (var key in obj) {
        switch (key) {
            case 'border':
                str += app.cssRules.get('border', obj.border, 'default');
                break;
            case 'animation':
                str += "animation-duration: "+obj.animation.duration+"s;";
                str += "animation-delay: "+obj.animation.delay+"s;";
                str += "opacity: "+(obj.animation.effect ? 0 : 1)+";";
                break;
            case 'full':
                if (obj[key].fullscreen) {
                    if (type != 'column') {
                        str += "align-items: center;";
                    }
                    str += "justify-content: center;";
                    if (type != 'lightbox') {
                        str += "min-height: 100vh;";
                    } else {
                        str += "min-height: calc(100vh - 50px);";
                    }
                } else {
                    if (obj.view && obj.view.height) {
                        str += "min-height: "+app.cssRules.getValueUnits(obj.view.height)+";";
                    } else {
                        str += "min-height: 50px;";
                    }
                }
                break;
            case 'view':
                if (obj.view.width) {
                    str += "width: "+app.cssRules.getValueUnits(obj.view.width)+";";
                }
                break;
            case 'padding':
            case 'margin':
                str += app.cssRules.get(key, obj[key], 'default');
                break;
        }
    }
    str += "}";
    str += app.backgroundRule(obj, '#'+selector);
    if (obj.full.fullscreen) {
        str += setItemsVisability(obj.disable, "flex", "#"+selector);
    } else {
        str += setItemsVisability(obj.disable, "block", "#"+selector);
    }
    if (obj.disable == 1) {
        str += "body.show-hidden-elements #"+selector+".visible {opacity : 0.3;}";
    } else {
        str += "#"+selector+".visible {opacity : 1;}";
    }
    if (obj.shape) {
        str += getShapeRules(selector, obj.shape.bottom, 'bottom');
        str += getShapeRules(selector, obj.shape.top, 'top');
    }
    if (obj.sticky && obj.sticky.enable && type == 'column') {
        str += "#"+selector+" {";
        str += "top: "+app.cssRules.getValueUnits(obj.sticky.offset)+";"
        str += "}";
    } else if (obj.sticky && obj.sticky.enable && type == 'row') {
        $g('#'+selector).closest('.ba-row-wrapper').each(function(){
            this.style.setProperty('--row-sticky-offset', app.cssRules.getValueUnits(obj.sticky.offset));
        });
    } else if (obj.sticky && obj.sticky.enable && type == 'section') {
        $g('#'+selector).closest('.ba-wrapper').each(function(){
            this.style.setProperty('--section-sticky-offset', app.cssRules.getValueUnits(obj.sticky.offset));
        });
    }
    str += setBoxModel(obj, selector);
    if (type == 'header') {
        str += createHeaderRules(obj, app.cssRulesFlag);
    }
    if (type == 'footer') {
        str += createFooterStyle(obj);
    }

    return str;
}

function getShapeRules(selector, obj, type)
{
    let str = "#"+selector+" > .ba-shape-divider.ba-shape-divider-"+type+" {",
        width = obj.width ? obj.width : 100,
        height = obj.height ? obj.height : obj.value * 10;
    if (obj.effect == 'arrow') {
        str += "clip-path: polygon(100% "+(100 - height / 10);
        str += "%, 100% 100%, 0 100%, 0 "+(100 - height / 10);
        str += "%, "+(50 - (height / 10) / 2)+"% "+(100 - (height / 10))+"%, 50% 100%, "+(50 + (height / 10) / 2)+"% ";
        str += (100 - (height / 10))+"%);";
    } else if (obj.effect == 'zigzag') {
        let pyramids = "clip-path: polygon(",
            delta = 0,
            delta2 = 100 / ((height / 10) * 2);
        for (var i = 0; i < (height / 10); i++) {
            if (i != 0) {
                pyramids += ",";
            }
            pyramids += delta+"% 100%,";
            pyramids += delta2+"% calc(100% - 15px),";
            delta += 100 / (height / 10);
            delta2 += 100 / (height / 10);
            pyramids += delta+"% 100%";
        }
        pyramids += ");";
        str += pyramids;
    } else if (obj.effect == 'circle') {
        str += "clip-path: circle("+(height / 10)+"% at 50% 100%);";
    } else if (obj.effect == 'vertex') {
        str += "clip-path: polygon(20% calc("+(100 - (height / 10))+"% + 15%), 35%  calc("+(100 - (height / 10));
        str += "% + 45%), 65%  "+(100 - (height / 10))+"%, 100% 100%, 100% 100%, 0% 100%, 0  calc(";
        str += (100 - (height / 10))+"% + 10%), 10%  calc("+(100 - (height / 10))+"% + 30%));";
    } else if (obj.effect != 'arrow' && obj.effect != 'zigzag' && obj.effect != 'circle' && obj.effect != 'vertex') {
        str += "clip-path: none;";
        str += "background: none;";
        str += "color: "+getCorrectColor(obj.color)+";";
    }
    if (obj.effect == 'arrow' || obj.effect == 'zigzag' || obj.effect == 'circle' || obj.effect == 'vertex') {
        str += "background-color: "+getCorrectColor(obj.color)+";";
    }
    if (!obj.effect) {
        str += 'display: none;';
    } else {
        str += 'display: block;';
    }
    str += "}";
    str += "#"+selector+" > .ba-shape-divider.ba-shape-divider-"+type+" {";
    str += "width: "+width+"%;";
    str += "}";
    str += "#"+selector+" > .ba-shape-divider.ba-shape-divider-"+type+" svg {";
    str += "display: block;";
    str += "height: "+app.cssRules.getValueUnits(height)+";";
    str += "}";

    return str;
}

function getBreadcrumbsRules(obj, selector)
{
    let str = "#"+selector+" {",
        justify = obj.style.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" ul {";
    str += "justify-content: "+justify+";";
    str += "}";
    str += "#"+selector+" li > * {";
    str += app.cssRules.get('padding', obj.style.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" li > *:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" li > *");
    str += "#"+selector+" li {";
    str += app.cssRules.get('padding', obj.style.padding, 'default', '--');
    str += "--typography-line-height : "+app.cssRules.getValueUnits(obj.style.typography['line-height'])+";";
    str += getTypographyRule(obj.style.typography, 'text-align');
    str += app.cssRules.getColors('colors', obj.style, 'default', '--', '', ['hover', 'active']);
    str += 'color: var(--color);';
    str += 'background-color: var(--background-color);';
    str += "}";
    app.cssRules.updateTransitions(obj.style.colors, 'border-color');
    str += app.cssRules.getStateRule("#"+selector+" li:hover", 'hover');
    str += app.cssRules.getStateRule("#"+selector+" li.active", 'active');
    str += app.cssRules.getTransitionRule("#"+selector+" li, #"+selector+" li a:after, #"+selector+" li a:before");
    str += "#"+selector+" li span {";
    str += "text-decoration:"+obj.style.typography['text-decoration']+";";
    str += "}";
    str += "#"+selector+" li i {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.style.icon.size)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getOnePageRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .integration-wrapper > ul > li {";
    str += app.cssRules.get('margin', obj.nav.margin, 'default', '', '', ['hover', 'active']);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .integration-wrapper > ul > li.active", 'active');
    str += app.cssRules.getStateRule("#"+selector+" .integration-wrapper > ul > li:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .integration-wrapper > ul > li");
    str += "#"+selector+" i.ba-menu-item-icon {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.nav.icon.size)+";";
    str += "}";
    str += "#"+selector+" .main-menu li a {";
    str += getTypographyRule(obj['nav-typography']);
    str += app.cssRules.getColors('colors', obj.nav, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('padding', obj.nav.padding, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('border', obj.nav.border, 'default', '', '', ['hover', 'active']);
    str += "}"
    str += app.cssRules.getStateRule("#"+selector+" .main-menu li.active > a", 'active');
    let query = "#"+selector+" .main-menu li a:hover, #"+selector+" .main-menu li.active a:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .main-menu li a");
    str += "#"+selector+" ul {";
    str += "text-align : "+obj['nav-typography']['text-align']+";";
    str += "}"
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getMenuRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    let query = "#"+selector+" > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li";
    str += query+" {";
    str += app.cssRules.get('margin', obj.nav.margin, 'default', '', '', ['hover', 'active']);
    str += "}";
    str += app.cssRules.getStateRule(query+".active", 'active');
    str += app.cssRules.getStateRule(query+":hover", 'hover');
    str += app.cssRules.getTransitionRule(query);
    str += query+" > *:not(ul):not(div) > i.ba-menu-item-icon {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.nav.icon.size)+";";
    str += "}";
    str += query+" > *:not(ul):not(div) {";
    str += getTypographyRule(obj['nav-typography']);
    str += app.cssRules.getColors('colors', obj.nav, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('padding', obj.nav.padding, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('border', obj.nav.border, 'default', '', '', ['hover', 'active']);
    str += "}";
    str += app.cssRules.getStateRule(query+".active > *:not(ul):not(div)", 'active');
    query = query+" > *:not(ul):not(div):hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li > *:not(ul):not(div)";
    str += app.cssRules.getTransitionRule(query);
    query = "#"+selector+" .main-menu li.deeper.parent > ul li";
    str += query+" i.ba-menu-item-icon {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.sub.icon.size)+";";
    str += "}";
    str += query+" > *:not(ul):not(div) {";
    str += getTypographyRule(obj['sub-typography']);
    str += app.cssRules.getColors('colors', obj.sub, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('padding', obj.sub.padding, 'default', '', '', ['hover', 'active']);
    str += app.cssRules.get('border', obj.sub.border, 'default', '', '', ['hover', 'active']);
    str += "}"
    str += app.cssRules.getStateRule(query+".active > *:not(ul):not(div)", 'active');
    str += app.cssRules.getStateRule(query+" > *:not(ul):not(div):hover", 'hover');
    str += app.cssRules.getTransitionRule(query+" > *:not(ul):not(div)");
    str += "#"+selector+" > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul {";
    str += "text-align : "+obj['nav-typography']['text-align']+";";
    str += "}"
    str += "#"+selector+" li.deeper.parent > ul {";
    str += app.cssRules.get('padding', obj.dropdown.padding, 'default');
    if (obj.dropdown.border) {
        str += app.cssRules.get('border', obj.dropdown.border, 'default');
    }
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" li.deeper.parent > ul:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" li.deeper.parent > ul");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getWeatherRules(obj, selector)
{
    var str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .weather .city {";
    str += getTypographyRule(obj.city);
    str += "}";
    str += "#"+selector+" .weather .condition {";
    str += getTypographyRule(obj.condition);
    str += "}";
    str += "#"+selector+" .weather-info > div,#"+selector+" .weather .date {";
    str += getTypographyRule(obj.info);
    str += "}";
    str += "#"+selector+" .forecast > span {";
    str += getTypographyRule(obj.forecasts);
    str += "}";
    if (obj.view.layout == 'forecast-block') {
        str += '#'+selector+' .forecast > span {display: block;width: initial;}';
        str += '#'+selector+' .weather-info + div {text-align: center;}';
        str += '#'+selector+' .ba-weather div.forecast {margin: 0 20px 0 10px;}';
        str += '#'+selector+' .ba-weather div.forecast .day-temp,';
        str += '#'+selector+' .ba-weather div.forecast .night-temp {margin: 0 5px;}';
        str += '#'+selector+' .ba-weather div.forecast span.night-temp,';
        str += '#'+selector+' .ba-weather div.forecast span.day-temp {padding-right: 0;width: initial;}';
    } else {
        str += '#'+selector+' .forecast > span {display: inline-block;width: 33.3%;}';
        str += '#'+selector+' .weather-info + div {text-align: left;}';
        str += '#'+selector+' .ba-weather div.forecast .day-temp,';
        str += '#'+selector+' .ba-weather div.forecast .night-temp {margin: 0;}';
        str += '#'+selector+' .ba-weather div.forecast {margin: 0;}';
        str += '#'+selector+' .ba-weather div.forecast span.night-temp,';
        str += '#'+selector+' .ba-weather div.forecast span.day-temp {padding-right: 1.5%;width: 14%;}';
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getAccordionRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .accordion-group, #"+selector+" .accordion-inner {";
    str += "border-color: "+getCorrectColor(obj.border.color)+";"; 
    str += "}";
    str += "#"+selector+" .accordion-inner {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .accordion-inner:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .accordion-inner");
    str += "#"+selector+" .accordion-heading a {";
    str += getTypographyRule(obj.typography, 'text-decoration');
    str += "}";
    if (obj.typography['text-decoration']) {
        str += "#"+selector+" .accordion-heading span.accordion-title {";
        str += "text-decoration: "+obj.typography['text-decoration']+";";
        str += "}";
    }
    str += "#"+selector+" .accordion-heading a i {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "}";
    str += "#"+selector+" .accordion-heading {";
    str += "background-color: "+getCorrectColor(obj.header.color)+";";
    str += "}";
    if (obj.icon.position == 'icon-position-left') {
        str += "#"+selector+' .accordion-toggle > span {flex-direction: row-reverse;}';
    } else {
        str += "#"+selector+' .accordion-toggle > span {flex-direction: row;}';
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getSimpleGalleryRules(obj, selector)
{
    var str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-instagram-image {"
    if (obj.border) {
        str += app.cssRules.get('border', obj.border, 'default');
    }
    str += "}";
    app.cssRules.transitions.push('transform 0.3s');
    str += app.cssRules.getStateRule("#"+selector+" .ba-instagram-image:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-instagram-image");
    str += "#"+selector+" .instagram-wrapper:not(.simple-gallery-masonry-layout) .ba-instagram-image {";
    str += "height: "+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";

    str += "#"+selector+" .instagram-wrapper:not(.simple-gallery-masonry-layout) {";
    str += 'display: grid;';
    str += 'grid-gap:'+(obj.gutter ? 10 : 0)+'px;';
    str += "}";

    str += "#"+selector+" .instagram-wrapper {";
    str += "grid-template-columns: repeat(auto-fill, minmax(calc((100% / "+obj.count+") - 20px),1fr));";
    str += "}";
    if (obj.overlay) {
        str += "#"+selector+" .ba-instagram-image > * {";
        str += "--transition-duration: "+obj.animation.duration+"s;"
        str += "}";
        str += "#"+selector+" .ba-simple-gallery-caption .ba-caption-overlay {background-color :";
        if (!obj.overlay.type || obj.overlay.type == 'color') {
            str += getCorrectColor(obj.overlay.color)+";";
            str += 'background-image: none;';
        } else if (obj.overlay.type == 'none') {
            str += 'rgba(0, 0, 0, 0);';
            str += 'background-image: none;';
        } else {
            str += 'rgba(0, 0, 0, 0);';
            str += 'background-image: '+obj.overlay.gradient.effect+'-gradient(';
            if (obj.overlay.gradient.effect == 'linear') {
                str += obj.overlay.gradient.angle+'deg';
            } else {
                str += 'circle';
            }
            str += ', '+getCorrectColor(obj.overlay.gradient.color1)+' ';
            str += obj.overlay.gradient.position1+'%, '+getCorrectColor(obj.overlay.gradient.color2);
            str += ' '+obj.overlay.gradient.position2+'%);';
            str += 'background-attachment: scroll;';
        }
        str += "}";
        str += "#"+selector+" .ba-simple-gallery-title {";
        str += getTypographyRule(obj.title.typography);
        str += app.cssRules.get('margin', obj.title.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-simple-gallery-title:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-simple-gallery-title");
        str += "#"+selector+" .ba-simple-gallery-description {";
        str += getTypographyRule(obj.description.typography);
        str += app.cssRules.get('margin', obj.description.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-simple-gallery-description:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-simple-gallery-description");
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getErrorRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" h1.ba-error-code {";
    str += getTypographyRule(obj.code.typography, '');
    str += app.cssRules.get('margin', obj.code.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" h1.ba-error-code:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" h1.ba-error-code");
    str += "#"+selector+" p.ba-error-message {";
    str += getTypographyRule(obj.message.typography, '');
    str += app.cssRules.get('margin', obj.message.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" p.ba-error-message:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" p.ba-error-message");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getSearchHeadlineRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .search-result-headline-wrapper > * {"
    str += getTypographyRule(obj.typography);
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getTextRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function(el){
        if (obj[el]['font-style'] && obj[el]['font-style'] == '@default') {
            delete(obj[el]['font-style']);
        }
        str += "#"+selector+" "+el+" {";
        str += getTypographyRule(obj[el], '', el);
        if (obj.animation) {
            str += 'animation-duration: '+obj.animation.duration+'s;';
        }
        str += "}";
    });
    if (obj.links && obj.links.color) {
        str += "#"+selector+' a {';
        str += 'color:'+getCorrectColor(obj.links.color)+';'
        str += '}';
    }
    if (obj.links && obj.links['hover-color']) {
        str += "#"+selector+' a:hover {';
        str += 'color:'+getCorrectColor(obj.links['hover-color'])+';'
        str += '}';
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getProgressPieRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-progress-pie {";
    str += 'width: '+app.cssRules.getValueUnits(obj.view.width)+';';
    str += getTypographyRule(obj.typography);
    str += "}";
    str += "#"+selector+" .ba-progress-pie canvas {";
    str += 'width: '+app.cssRules.getValueUnits(obj.view.width)+';';
    str += "}";
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    str += setBoxModel(obj, selector);

    return str;
}

function getReadingProgressBarRules(obj, selector)
{
    let str = "";
    str += "#"+selector+" .ba-reading-progress-bar {";
    str += 'height: '+app.cssRules.getValueUnits(obj.view.height)+';';
    str += "background-color: "+getCorrectColor(obj.view.background)+";";
    str += "}";
    str += "#"+selector+" .ba-animated-bar {";
    str += "background-color: "+getCorrectColor(obj.view.bar)+";";
    str += "}";
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    str += setBoxModel(obj, selector);

    return str;
}

function getProgressBarRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-progress-bar {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    str += 'height: '+app.cssRules.getValueUnits(obj.view.height)+';';
    str += "background-color: "+getCorrectColor(obj.view.background)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-progress-bar:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-progress-bar");
    str += "#"+selector+" .ba-animated-bar {";
    str += "background-color: "+getCorrectColor(obj.view.bar)+";";
    str += getTypographyRule(obj.typography);
    str += "}";
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    str += setBoxModel(obj, selector);

    return str;
}

function getEventCalendarRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-event-calendar-title-wrapper {";
    str += getTypographyRule(obj.months.typography);
    str += "}";
    str += "#"+selector+" .ba-event-calendar-header * {";
    str += getTypographyRule(obj.weeks.typography);
    str += "}";
    str += "#"+selector+" .ba-event-calendar-body * {";
    str += getTypographyRule(obj.days.typography);
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getCommentsBoxRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-comment-message, #"+selector+" .user-comment-wrapper {";
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    let query = "#"+selector+" .ba-comment-message:hover, #"+selector+" .user-comment-wrapper:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .ba-comment-message, #"+selector+" .user-comment-wrapper";
    str += app.cssRules.getTransitionRule(query);
    str += "#"+selector+" .comment-message, #"+selector+" .ba-comment-message::placeholder, ";
    str += "#"+selector+" .ba-comments-total-count-wrapper select, #"+selector+" .ba-comment-message, ";
    str += "#"+selector+" .comment-delete-action, #"+selector+" .comment-edit-action, ";
    str += "#"+selector+" .comment-likes-action-wrapper > span > span, ";
    str += "#"+selector+" .ba-review-rate-title, ";
    str += "#"+selector+" span.ba-comment-attachment-trigger, ";
    str += "#"+selector+" .comment-likes-wrapper .comment-action-wrapper > span.comment-reply-action > span, ";
    str += "#"+selector+" .comment-likes-wrapper .comment-action-wrapper > span.comment-share-action > span, ";
    str += "#"+selector+" .comment-user-date, #"+selector+" .ba-social-login-wrapper > span, ";
    str += "#"+selector+" .ba-user-login-btn, #"+selector+" .ba-guest-login-btn, #"+selector+" .comment-logout-action, ";
    str += "#"+selector+" .comment-user-name, #"+selector+" .ba-comments-total-count {";
    str += getTypographyRule(obj.typography);
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getFieldRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-field-wrapper {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-field-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-field-wrapper");
    str += "#"+selector+" .ba-field-label, #"+selector+" .ba-field-label *:not(i):not(.ba-tooltip) {";
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += "#"+selector+" .ba-field-label i {";
    str += "color : "+getCorrectColor(obj.icons.color)+";";
    str += "font-size : "+app.cssRules.getValueUnits(obj.icons.size)+";";
    str += "}";
    str += "#"+selector+" .ba-field-content {";
    str += getTypographyRule(obj.value.typography);
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getFieldsFilterRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-field-filter-label, #"+selector+" .ba-selected-filter-values-title {";
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += "#"+selector+" .ba-field-filter-value-wrapper, #"+selector+" .ba-selected-filter-values-remove-all {";
    str += getTypographyRule(obj.value.typography);
    str += '--filter-value-line-height: '+app.cssRules.getValueUnits(obj.value.typography['line-height'])+';'
    str += "}";
    let justify = obj.value.typography['text-align'].replace('right', 'flex-start').replace('left', 'flex-end');
    str += "#"+selector+" .ba-checkbox-wrapper {";
    str += "justify-content: "+justify+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getCurrencySwitcherRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-currency-switcher-active span {";
    str += getTypographyRule(obj.switcher.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-currency-switcher-active i {";
    str += "color: "+getCorrectColor(obj.switcher.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-currency-switcher-active {";
    str += "text-align: "+obj.switcher.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-currency-switcher-list span {";
    str += getTypographyRule(obj.list.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-currency-switcher-list {";
    str += "text-align: "+obj.list.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-lightbox-layout .ba-currency-switcher-list i {";
    str += "color: "+getCorrectColor(obj.list.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-currency-switcher-list {";
    str += app.cssRules.get('padding', obj.dropdown.padding, 'default');
    str += app.cssRules.get('border', obj.dropdown.border, 'default');
    str += "background-color: "+getCorrectColor(obj.dropdown.background.color)+";";
    str += "--background-color: "+getCorrectColor(obj.dropdown.background.color)+";";
    str += app.cssRules.get('shadow', obj.dropdown.shadow, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-currency-switcher-list:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-currency-switcher-list");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getLanguageSwitcherRules(obj, selector)
{
    let str = "#"+selector+" {",
        query = '',
        justify = obj.flag.align.replace('left', 'flex-start').replace('right', 'flex-end');
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-default-layout .ba-language-switcher-item img {";
    str += "width: "+app.cssRules.getValueUnits(obj.flag.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.flag.size)+";";
    str += "border-radius: "+app.cssRules.getValueUnits(obj.flag.radius)+";";
    str += "}";
    str += "#"+selector+" .ba-default-layout .ba-language-switcher-list {";
    str += "justify-content: "+justify+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-active img, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-active img {";
    str += "width: "+app.cssRules.getValueUnits(obj.switcher.flag.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.switcher.flag.size)+";";
    str += "border-radius: "+app.cssRules.getValueUnits(obj.switcher.flag.radius)+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-active span, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-active span {";
    str += getTypographyRule(obj.switcher.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-active i, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-active i {";
    str += "color: "+getCorrectColor(obj.switcher.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-active, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-active {";
    str += "text-align: "+obj.switcher.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list img, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list img {";
    str += "width: "+app.cssRules.getValueUnits(obj.list.flag.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.list.flag.size)+";";
    str += "border-radius: "+app.cssRules.getValueUnits(obj.list.flag.radius)+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list span, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list span {";
    str += getTypographyRule(obj.list.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list {";
    str += "text-align: "+obj.list.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list i {";
    str += "color: "+getCorrectColor(obj.list.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list, ";
    str += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list {";
    str += app.cssRules.get('padding', obj.dropdown.padding, 'default');
    str += app.cssRules.get('border', obj.dropdown.border, 'default');
    str += "background-color: "+getCorrectColor(obj.dropdown.background.color)+";";
    str += "--background-color: "+getCorrectColor(obj.dropdown.background.color)+";";
    str += app.cssRules.get('shadow', obj.dropdown.shadow, 'default');
    str += "}";
    query = "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list:hover, ";
    query += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .ba-dropdown-layout .ba-language-switcher-list, ";
    query += "#"+selector+" .ba-lightbox-layout .ba-language-switcher-list";
    str += app.cssRules.getTransitionRule(query);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getModulesRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getTabsRules(obj, selector)
{
    let str = "#"+selector+" {",
        align = obj.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .tab-content {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .tab-content:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .tab-content");
    str += "#"+selector+" ul.nav.nav-tabs li a {";
    str += getTypographyRule(obj.typography, 'text-decoration');
    str += 'align-items:'+align+';';
    str += "}";
    if (obj.typography['text-decoration']) {
        str += "#"+selector+" li span.tabs-title {";
        str += "text-decoration : "+obj.typography['text-decoration']+";";
        str += "}";
    }
    str += "#"+selector+" ul.nav.nav-tabs li a i {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "}";
    str += "#"+selector+" ul.nav.nav-tabs li.active a {";
    str += "color : "+getCorrectColor(obj.hover.color)+";";
    str += "}";
    str += "#"+selector+" ul.nav.nav-tabs li.active a:before {";
    str += "background-color : "+getCorrectColor(obj.hover.color)+";";
    str += "}";
    str += "#"+selector+" ul.nav.nav-tabs {";
    str += "background-color: "+getCorrectColor(obj.header.color)+";";
    str += "border-color: "+getCorrectColor(obj.header.border)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getCounterRules(obj, selector)
{
    let str = "#"+selector+" .ba-counter span.counter-number {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += getTypographyRule(obj.counter, 'text-align');
    str += "width : "+app.cssRules.getValueUnits(obj.counter['line-height'])+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-counter span.counter-number:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-counter span.counter-number");
    str += "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "text-align : "+obj.counter['text-align']+";"
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getCountdownRules(obj, selector)
{
    let str = "#"+selector+" .ba-countdown > span {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-countdown > span:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-countdown > span");
    str += "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .countdown-time {";
    str += getTypographyRule(obj.counter);
    str += "}";
    str += "#"+selector+" .countdown-label {";
    str += getTypographyRule(obj.label);
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getSearchRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}"
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-search-wrapper input::-webkit-input-placeholder {";
    str += getTypographyRule(obj.typography);
    str += "}";
    str += "#"+selector+" .ba-search-wrapper input::-moz-placeholder {";
    str += getTypographyRule(obj.typography);
    str += "}";
    str += "#"+selector+" .ba-search-wrapper input {";
    str += getTypographyRule(obj.typography);
    str += "height : "+app.cssRules.getValueUnits(obj.typography['line-height'])+";";
    str += "}";
    str += "#"+selector+" .ba-search-wrapper {";
    if (obj.background) {
        str += app.cssRules.get('backgroundColor', obj.background, 'default');
    }
    if (obj.shadow) {
        str += app.cssRules.get('shadow', obj.shadow, 'default');
    }
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-search-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-search-wrapper");
    if (obj.icons && obj.icons.size) {
        str += "#"+selector+" .ba-search-wrapper i {";
        str += "color: "+getCorrectColor(obj.typography.color)+";";
        str += "font-size : "+app.cssRules.getValueUnits(obj.icons.size)+";";
        str += "}";
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getCheckoutFormRules(obj, selector)
{
    let str = "#"+selector+" {"
    str += app.cssRules.get('margin', obj.margin, 'default', '--');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-checkout-form-fields, .ba-item-checkout-order-form, .ba-item-submission-form {"
    str += getTypographyRule(obj.title.typography, '', '', true, '--title');
    if (obj.headline) {
        str += getTypographyRule(obj.headline.typography, '', '', true, '--headline');
    }
    if (obj.headline && obj.headline.margin) {
        str += app.cssRules.get('margin', obj.headline.margin, 'default', '--headline-');
        str += app.cssRules.get('margin', obj.title.margin, 'default', '--title-');
    }
    str += "}";
    str += "#"+selector+" .ba-checkout-form-field-wrapper *, .ba-item-checkout-order-form, #"+selector+".ba-item-submission-form {"
    str += app.cssRules.get('backgroundColor', obj.field.background, 'default', '--');
    str += app.cssRules.get('border', obj.field.border, 'default', '--');
    str += getTypographyRule(obj.field.typography, '', '', true, '--field');
    if (obj.field.margin) {
        str += app.cssRules.get('margin', obj.field.margin, 'default', '--field-');
        str += app.cssRules.get('padding', obj.field.padding, 'default', '--field-');
    }
    str += "}";
    let query = "#"+selector+" .ba-checkout-form-field-wrapper *:hover, .ba-item-checkout-order-form:hover, ";
    query += "#"+selector+" input:hover, #"+selector+" select:hover, #"+selector+" textarea:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .ba-checkout-form-field-wrapper *, .ba-item-checkout-order-form, ";
    query += "#"+selector+" input, #"+selector+" select, #"+selector+" textarea";
    str += app.cssRules.getTransitionRule(query);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getLoginRules(obj, selector)
{
    let str = "#"+selector+" {"
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-login-content-wrapper {"
    str += app.cssRules.get('border', obj.border, 'default', '--');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('backgroundColor', obj.background, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-content-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-content-wrapper");
    str += "#"+selector+" .ba-login-headline {";
    str += getTypographyRule(obj.headline.typography);
    str += app.cssRules.get('margin', obj.headline.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-headline:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-headline");
    str += "#"+selector+" .ba-login-field-label {";
    str += getTypographyRule(obj.title.typography);
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-field-label:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-field-label");
    str += "#"+selector+" .ba-checkbox-wrapper > * {";
    str += getTypographyRule(obj.title.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-login-description {";
    str += getTypographyRule(obj.description.typography);
    str += app.cssRules.get('margin', obj.description.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-description:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-description");
    str += "#"+selector+" .ba-login-field {";
    str += getTypographyRule(obj.field.typography);
    str += app.cssRules.get('padding', obj.field.padding, 'default');
    str += app.cssRules.get('margin', obj.field.margin, 'default');
    str += app.cssRules.get('backgroundColor', obj.field.background, 'default');
    str += app.cssRules.get('border', obj.field.border, 'default', '--');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-field:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-field");
    str += "#"+selector+" .ba-login-integration-btn {";
    str += getTypographyRule(obj.field.typography, 'text-align');
    str += app.cssRules.get('backgroundColor', obj.field.background, 'default');
    str += app.cssRules.get('border', obj.field.border, 'default', '--');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-integration-btn:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-integration-btn");
    str += "#"+selector+" .ba-login-btn {";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-login-btn:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-login-btn");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getIconListRules(obj, selector)
{
    let str = "#"+selector+" {",
        align  = obj.body['text-align'];
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-icon-list-wrapper ul {";
    str += "align-items: "+align.replace('left', 'flex-start').replace('right', 'flex-end')+";";
    str += "justify-content: "+align.replace('left', 'flex-start').replace('right', 'flex-end')+";";
    str += "}";
    if (obj.padding) {
        str += "#"+selector+" .ba-icon-list-wrapper ul li {";
        str += "background-color:"+getCorrectColor(obj.background.color)+';';
        str += app.cssRules.get('border', obj.border, 'default');
        str += app.cssRules.get('shadow', obj.shadow, 'default');
        str += app.cssRules.get('padding', obj.padding, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-icon-list-wrapper ul li:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+' .ba-icon-list-wrapper ul li');
    }
    str += "#"+selector+" .ba-icon-list-wrapper ul li span {";
    str += getTypographyRule(obj.body);
    str += "}";
    str += "#"+selector+" .ba-icon-list-wrapper ul li {";
    if (obj.body['line-height']) {
        str += '--icon-list-line-height: '+app.cssRules.getValueUnits(obj.body['line-height'])+';';
    }
    str += "}";
    str += "#"+selector+" .ba-icon-list-wrapper ul li i, #"+selector+" ul li a:before, #";
    str += selector+" ul li.list-item-without-link:before {";
    str += "color: "+getCorrectColor(obj.icons.color)+";";
    str += "font-size: "+app.cssRules.getValueUnits(obj.icons.size)+";";
    if (obj.icons.background) {
        str += "background-color: "+getCorrectColor(obj.icons.background)+";";
        str += "padding: "+app.cssRules.getValueUnits(obj.icons.padding)+";";
        str += "border-radius: "+app.cssRules.getValueUnits(obj.icons.radius)+";";
    }
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getHotspotRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" > .ba-button-wrapper a {";
    str += app.cssRules.getColors('colors', obj, 'default', '--');
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" > .ba-button-wrapper a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" > .ba-button-wrapper a");
    str += "#"+selector+" > .ba-button-wrapper i {";
    str += "font-size : "+app.cssRules.getValueUnits(obj.style.size)+";"
    str += "}";
    str += '#'+selector+" > .ba-hotspot-popover {";
    str += "width: "+app.cssRules.getValueUnits(obj.popover.style.width)+";";
    str += "--background-color: "+getCorrectColor(obj.popover.background.color)+";";
    str += app.cssRules.get('border', obj.popover.border, 'default');
    str += app.cssRules.get('shadow', obj.popover.shadow, 'default');
    str += app.cssRules.get('padding', obj.popover.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" > .ba-hotspot-popover:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" > .ba-hotspot-popover");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getButtonRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-button-wrapper {";
    str += "text-align: "+obj.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-button-wrapper a span {";
    str += getTypographyRule(obj.typography);
    str += "}";
    str += "#"+selector+" .ba-button-wrapper a {";
    str += app.cssRules.getColors('colors', obj, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-button-wrapper a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-button-wrapper a");
    if (obj.icons && obj.icons.size) {
        str += "#"+selector+" .ba-button-wrapper a i {";
        str += "font-size : "+app.cssRules.getValueUnits(obj.icons.size)+";"
        str += "}";
    }
    if (obj.icons && ('position' in obj.icons)) {
        str += "#"+selector+" .ba-button-wrapper a {";
        if (obj.icons.position == '') {
            str += 'flex-direction: row-reverse;';
        } else {
            str += 'flex-direction: row;';
        }
        str += "}";
        if (obj.icons.position == '') {
            str += "#"+selector+" .ba-button-wrapper a i {";
            str += 'margin: 0 10px 0 0;';
            str += "}";
        } else {
            str += "#"+selector+" .ba-button-wrapper a i {";
            str += 'margin: 0 0 0 10px;';
            str += "}";
        }
    }
    if (obj.view && 'subtotal' in obj.view) {
        str += "#"+selector+" .ba-button-wrapper a span.ba-cart-subtotal {";
        str += 'display: '+(obj.view.subtotal ? 'flex' : 'none')+';';
        str += "}";
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getCategoriesRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-masonry-layout {";
    str += "grid-template-columns: repeat(auto-fill, minmax(calc((100% / "+obj.view.count+") - 21px),1fr));";
    str += "}";
    str += "#"+selector+" .ba-grid-layout .ba-blog-post, #"+selector+" .ba-classic-layout .ba-blog-post {";
    str += "width: calc((100% / "+obj.view.count+") - 21px);";
    str += "}";
    if (obj.view.gutter) {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
        str += "margin-left: 10px;margin-right: 10px;";
        str += "width: calc((100% / "+obj.view.count+") - 21px);";
        str += "}";
        str += "#"+selector+" .ba-cover-layout {margin-left: -10px;margin-right: -10px;}";
    } else {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
        str += "margin-left: 0;margin-right: 0;";
        str += "width: calc(100% / "+obj.view.count+");";
        str += "}";
        str += "#"+selector+" .ba-cover-layout {margin-left: 0;margin-right: 0;}";
    }
    str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child(n) {";
    str += "margin-top: 30px;";
    str += "}";
    str += "#"+selector+" .ba-classic-layout .ba-blog-post:nth-child(n) {";
    str += "margin-top: "+(obj.view.image ? 30 : 0)+"px;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child("+(i + 1)+"), #";
        str += selector+" .ba-classic-layout .ba-blog-post:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    str += "#"+selector+" .ba-cover-layout .ba-blog-post:nth-child(n) {";
    str += "margin-top: "+(obj.view.gutter ? 30 : 0)+"px;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    str += "#"+selector+" .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post:hover .ba-overlay", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-overlay");
    str += "#"+selector+" .ba-blog-post {";
    str += "background-color:"+getCorrectColor(obj.background.color)+';';
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post");
    str += "#"+selector+" .ba-blog-post-image {";
    str += app.cssRules.get('border', obj.image.border, 'default');
    str += "width :"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "background-size: "+obj.image.size+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-image:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-image");
    str += "#"+selector+" .ba-masonry-layout .ba-blog-post-image {";
    str += "width :100%;";
    str += "height :auto;";
    str += "}";
    str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-title {";
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += 'color'+getCorrectColor(obj.title.typography.color);
    str += "}";
    str += "#"+selector+" .ba-blog-post-title a {";
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-title");
    str += "#"+selector+" .ba-app-sub-categories {";
    str += app.cssRules.get('margin', obj.info.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-app-sub-categories:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-app-sub-categories");
    str += "#"+selector+" .ba-app-sub-category a {";
    str += getTypographyRule(obj.info.typography);
    str += "}";
    str += "#"+selector+" .ba-app-sub-category i {";
    str += 'color'+getCorrectColor(obj.info.typography.color);
    str += "}";
    str += "#"+selector+" .ba-blog-post-intro-wrapper {";
    str += getTypographyRule(obj.intro.typography);
    str += app.cssRules.get('margin', obj.intro.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-intro-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-intro-wrapper");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getAddToCartRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-add-to-cart-price {";
    str += app.cssRules.get('margin', obj.price.margin, 'default');
    str += getTypographyRule(obj.price.typography, 'text-align');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-add-to-cart-price:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-add-to-cart-price");
    str += "#"+selector+" .ba-add-to-cart-info {";
    str += app.cssRules.get('margin', obj.info.margin, 'default');
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-add-to-cart-info:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-add-to-cart-info");
    str += "#"+selector+" .ba-add-to-cart-upload-file {";
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";
    let family = obj.button.typography['font-family'];
    if (family == '@default') {
        family = getTextParentFamily(app.theme.desktop, 'body');
    }
    str += "#"+selector+" .ba-add-to-cart-quantity {";
    str += "font-family: '"+family.replace(/\+/g, ' ')+"';";
    str += 'font-size: '+app.cssRules.getValueUnits(obj.button.typography['font-size'])+';';
    str += 'letter-spacing: '+app.cssRules.getValueUnits(obj.button.typography['letter-spacing'])+';';
    str += 'color: '+getCorrectColor(obj.price.typography.color)+';';
    str += "}";
    str += "#"+selector+" .ba-add-to-cart-button-wrapper {";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-add-to-cart-button-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-add-to-cart-button-wrapper");
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .ba-add-to-cart-buttons-wrapper {";
    str += "background-color: "+getCorrectColor(obj.button.colors.default['background-color'])+";";
    str += app.cssRules.get('border', obj.button.border, 'default', '--');
    str += "--display-wishlist: "+(obj.view.wishlist ? 0 : 1)+";"
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default', '--');
    str += "}";
    let query = "#"+selector+' .ba-add-to-cart-buttons-wrapper';
    str += app.cssRules.getStateRule(query+":hover", 'hover');
    str += app.cssRules.getTransitionRule(query);
    str += "#"+selector+" .ba-add-to-cart-button-wrapper a, #"+selector+" .ba-add-to-wishlist {";
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += "}";
    query = "#"+selector+" .ba-add-to-cart-button-wrapper a:hover, #"+selector+" .ba-add-to-wishlist:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    if (obj.button.border.transition) {
        app.cssRules.updateTransitions(obj.button.border, 'border-radius');
    }
    if (obj.button.padding.transition) {
        app.cssRules.updateTransitions(obj.button.padding, 'padding');
    }
    query = "#"+selector+" .ba-add-to-cart-button-wrapper a, #"+selector+" .ba-add-to-wishlist";
    str += app.cssRules.getTransitionRule(query);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getBlogPostsRules(obj, selector, type)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-masonry-layout {";
    str += "grid-template-columns: repeat(auto-fill, minmax(calc((100% / "+obj.view.count+") - 21px),1fr));";
    str += "}";
    str += "#"+selector+" .ba-grid-layout .ba-blog-post {";
    str += "width: calc((100% / "+obj.view.count+") - 21px);";
    str += "}";
    str += "#"+selector+" .ba-one-column-grid-layout .ba-blog-post {";
    str += "width: calc(100% - 21px);";
    str += "}";
    if (obj.view.gutter) {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
        str += "margin-left: 10px;margin-right: 10px;";
        str += "width: calc((100% / "+obj.view.count+") - 21px);";
        str += "}";
        str += "#"+selector+" .ba-cover-layout {margin-left: -10px;margin-right: -10px;}";
    } else {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
        str += "margin-left: 0;margin-right: 0;";
        str += "width: calc(100% / "+obj.view.count+");";
        str += "}";
        str += "#"+selector+" .ba-cover-layout {margin-left: 0;margin-right: 0;}";
    }
    str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child(n) {";
    str += "margin-top: 30px;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    str += "#"+selector+" .ba-cover-layout .ba-blog-post:nth-child(n) {";
    str += "margin-top: "+(obj.view.gutter ? 30 : 0)+"px;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-cover-layout .ba-blog-post:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    str += "#"+selector+" .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post:hover .ba-overlay", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-overlay");
    if (obj.background) {
        str += "#"+selector+" .ba-blog-post {";
        str += app.cssRules.get('backgroundColor', obj.background, 'default');
        str += app.cssRules.get('border', obj.border, 'default');
        str += app.cssRules.get('shadow', obj.shadow, 'default');
        str += "}";
    }
    if (obj.padding) {
        str += "#"+selector+" .ba-blog-post {";
        str += app.cssRules.get('padding', obj.padding, 'default');
        str += "}";
    }
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post");
    if (obj.image.border) {
        str += "#"+selector+" .ba-blog-post-image {";
        str += app.cssRules.get('border', obj.image.border, 'default');
        str += "}";
    }
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-image:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-image");
    str += "#"+selector+" .ba-blog-post-image {";
    str += "width :"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "background-size: "+(obj.image.size ? obj.image.size : 'cover')+";";
    str += "}";
    str += "#"+selector+" .ba-masonry-layout .ba-blog-post-image {";
    str += "width :100%;";
    str += "height :auto;";
    str += "}";
    str += "#"+selector+" .ba-cover-layout .ba-blog-post {";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-title {";
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-title");
    let price = obj.price && typeof obj.price == 'object' ? obj.price : obj.title;
    str += "#"+selector+" .ba-blog-post-add-to-cart-wrapper {";
    str += app.cssRules.get('margin', price.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-add-to-cart-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-add-to-cart-wrapper");
    let justify = price.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');;
    str += "#"+selector+" .ba-blog-post-add-to-cart-price {";
    if (type == 'post-navigation' && price.typography['text-align'] == 'left') {
        str += "align-items :flex-end;";
    } else if (type == 'post-navigation' && price.typography['text-align'] == 'right') {
        str += "align-items :flex-start;";
    } else {
        str += "align-items :"+justify+";";
    }
    str += getTypographyRule(price.typography, 'text-align');
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-add-to-cart-price {";
        str += "align-items :"+justify+";";
        str += "}";
    }
    str += "#"+selector+" .ba-blog-post-title {";
    str += getTypographyRule(obj.title.typography, 'text-align');
    if (type == 'post-navigation' && obj.title.typography['text-align'] == 'left') {
        str += "text-align :right;";
    } else if (type == 'post-navigation' && obj.title.typography['text-align'] == 'right') {
        str += "text-align :left;";
    } else {
        str += "text-align :"+obj.title.typography['text-align']+";";
    }
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-title {";
        str += "text-align :"+obj.title.typography['text-align']+";";
        str += "}";
    }
    justify = obj.reviews.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += "#"+selector+" .ba-blog-post-reviews {";
    if (type == 'post-navigation' && obj.reviews.typography['text-align'] == 'left') {
        str += "justify-content :flex-end;";
    } else if (type == 'post-navigation' && obj.reviews.typography['text-align'] == 'right') {
        str += "justify-content :flex-start;";
    } else {
        str += "justify-content :"+justify+";";
    }
    str += getTypographyRule(obj.reviews.typography, 'text-align');
    str += app.cssRules.get('margin', obj.reviews.margin, 'default');
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-reviews {";
        str += "justify-content :"+justify+";";
        str += "}";
    }
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-reviews:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-reviews");
    if (obj.postFields) {
        str += "#"+selector+" .ba-blog-post-field-row-wrapper {";
        str += getTypographyRule(obj.postFields.typography, 'text-align');
        str += app.cssRules.get('margin', obj.postFields.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-field-row-wrapper:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-field-row-wrapper");
    }
    str += "#"+selector+" .ba-blog-post-reviews a:hover {";
    str += "color: "+getCorrectColor(obj.reviews.hover.color)+";";
    str += "}";
    justify = obj.info.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += "#"+selector+" .ba-blog-post-info-wrapper {";
    str += app.cssRules.get('margin', obj.info.margin, 'default', null, ' * var(--visible-info)');
    if (type == 'post-navigation' && obj.info.typography['text-align'] == 'left') {
        str += "justify-content :flex-end;";
    } else if (type == 'post-navigation' && obj.info.typography['text-align'] == 'right') {
        str += "justify-content :flex-start;";
    } else {
        str += "justify-content :"+justify+";";
    }
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-info-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-info-wrapper");
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-info-wrapper {";
        str += "justify-content :"+justify+";";
        str += "}";
    }
    str += "#"+selector+" .ba-post-navigation-info {";
    if (type == 'post-navigation' && obj.info.typography['text-align'] == 'left') {
        str += "text-align :right;";
    } else if (type == 'post-navigation' && obj.info.typography['text-align'] == 'right') {
        str += "text-align :left;";
    } else {
        str += "text-align :"+obj.info.typography['text-align']+";";
    }
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-post-navigation-info {";
        str += "text-align :"+obj.info.typography['text-align']+";";
        str += "}";
    }
    str += "#"+selector+" .ba-blog-post-info-wrapper > span *, #"+selector+" .ba-post-navigation-info a {";
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-blog-post-info-wrapper > span {";
    str += "color: "+getCorrectColor(obj.info.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-intro-wrapper {";
    str += getTypographyRule(obj.intro.typography, 'text-align');
    str += app.cssRules.get('margin', obj.intro.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-intro-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-intro-wrapper");
    str += "#"+selector+" .ba-blog-post-intro-wrapper {";
    if (type == 'post-navigation' && obj.intro.typography['text-align'] == 'left') {
        str += "text-align :right;";
    } else if (type == 'post-navigation' && obj.intro.typography['text-align'] == 'right') {
        str += "text-align :left;";
    } else {
        str += "text-align :"+obj.intro.typography['text-align']+";";
    }
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-intro-wrapper {";
        str += "text-align :"+obj.intro.typography['text-align']+";";
        str += "}";
    }
    str += "#"+selector+" .ba-blog-post-button-wrapper {";
    if (type == 'post-navigation' && obj.button.typography['text-align'] == 'left') {
        str += "text-align :right;";
    } else if (type == 'post-navigation' && obj.button.typography['text-align'] == 'right') {
        str += "text-align :left;";
    } else {
        str += "text-align :"+obj.button.typography['text-align']+";";
    }
    str += "}";
    if (type == 'post-navigation') {
        str += "#"+selector+" .ba-blog-post:first-child .ba-blog-post-button-wrapper {";
        str += "text-align :"+obj.button.typography['text-align']+";";
        str += "}";
    }
    str += "#"+selector+" .ba-blog-post-button-wrapper a {";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-button-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-button-wrapper");
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .ba-blog-post-button-wrapper a, #"+selector+" .ba-blog-post-add-to-cart {";
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += "}";
    query = "#"+selector+" .ba-blog-post-button-wrapper a:hover, #"+selector+" .ba-blog-post-add-to-cart:hover"
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .ba-blog-post-button-wrapper a, #"+selector+" .ba-blog-post-add-to-cart"
    str += app.cssRules.getTransitionRule(query);
    if (obj.pagination && !obj.pagination.typography) {
        str += "#"+selector+" .ba-blog-posts-pagination span a {";
        str += "color: "+getCorrectColor(obj.pagination.color)+";";
        str += "}";
        str += "#"+selector+" .ba-blog-posts-pagination span.active a,#"+selector;
        str += " .ba-blog-posts-pagination span:hover a {";
        str += "color: "+getCorrectColor(obj.pagination.hover)+";";
        str += "}";
    } else if (obj.pagination && obj.pagination.typography) {
        str += "#"+selector+" .ba-blog-posts-pagination {";
        str += "text-align :"+obj.pagination.typography['text-align']+";";
        str += app.cssRules.get('margin', obj.pagination.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-blog-posts-pagination", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+' .ba-blog-posts-pagination');
        app.cssRules.prepareColors(obj.pagination);
        str += "#"+selector+" .ba-blog-posts-pagination a {";
        str += getTypographyRule(obj.pagination.typography, 'text-align');
        str += app.cssRules.get('border', obj.pagination.border, 'default');
        str += app.cssRules.get('shadow', obj.pagination.shadow, 'default');
        str += app.cssRules.getColors('colors', obj.pagination, 'default');
        str += app.cssRules.get('padding', obj.pagination.padding, 'default');
        str += "}";
        query = "#"+selector+" .ba-blog-posts-pagination a:hover, #"+selector+" .ba-blog-posts-pagination span.active a";
        str += app.cssRules.getStateRule(query, 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+' .ba-blog-posts-pagination a');
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getRecentCommentsRules(obj, selector, type)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    if (obj.view.count) {
        str += "#"+selector+" .ba-masonry-layout {";
        str += "grid-template-columns: repeat(auto-fill, minmax(calc((100% / "+obj.view.count+") - 21px),1fr));";
        str += "}";
        str += "#"+selector+" .ba-grid-layout .ba-blog-post {";
        str += "width: calc((100% / "+obj.view.count+") - 21px);";
        str += "}";
        str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child(n) {";
        str += "margin-top: 30px;";
        str += "}";
        for (var i = 0; i < obj.view.count; i++) {
            str += "#"+selector+" .ba-grid-layout .ba-blog-post:nth-child("+(i + 1)+") {";
            str += "margin-top: 0;";
            str += "}";
        }
    }
    str += "#"+selector+" .ba-blog-post {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "background-color:"+getCorrectColor(obj.background.color)+';';
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post");
    str += "#"+selector+" .ba-blog-post-image {";
    str += app.cssRules.get('border', obj.image.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-image:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-image");
    str += "#"+selector+" .ba-blog-post-image {";
    str += "width :"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-title {";
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-title");
    str += "#"+selector+" .ba-blog-post-info-wrapper {";
    str += app.cssRules.get('margin', obj.info.margin, 'default');
    str += "text-align :"+obj.info.typography['text-align']+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-info-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-info-wrapper");
    str += "#"+selector+" .ba-blog-post-info-wrapper > * {";
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";
    if ('stars' in obj) {
        let justify = obj.stars.icon['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
        str += "#"+selector+" .ba-review-stars-wrapper {";
        str += app.cssRules.get('margin', obj.stars.margin, 'default');
        str += "font-size: "+app.cssRules.getValueUnits(obj.stars.icon.size)+";";
        str += "justify-content: "+justify+";";
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-review-stars-wrapper:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-review-stars-wrapper");
    }
    str += "#"+selector+" .ba-blog-post-intro-wrapper {";
    str += getTypographyRule(obj.intro.typography);
    str += app.cssRules.get('margin', obj.intro.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-intro-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-intro-wrapper");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getAuthorRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-posts-author-wrapper .ba-post-author {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-posts-author-wrapper .ba-post-author:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-posts-author-wrapper .ba-post-author");
    str += "#"+selector+" .ba-grid-layout .ba-post-author {";
    str += "width: calc((100% / "+obj.view.count+") - 21px);";
    str += "}";
    str += "#"+selector+" .ba-grid-layout .ba-post-author:nth-child(n) {";
    str += "margin-top: 30px;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-grid-layout .ba-post-author:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    if (obj.background) {
        str += "#"+selector+" .ba-post-author {";
        str += app.cssRules.get('backgroundColor', obj.background, 'default');
        str += app.cssRules.get('border', obj.border, 'default');
        str += app.cssRules.get('shadow', obj.shadow, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-post-author:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+' .ba-post-author');
    }
    if (obj.image.border) {
        str += "#"+selector+" .ba-post-author-image {";
        str += app.cssRules.get('border', obj.image.border, 'default');
        str += "}";
    }
    str += app.cssRules.getStateRule("#"+selector+" .ba-post-author-image:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .ba-post-author-image');
    str += "#"+selector+" .ba-post-author-image {";
    str += "width :"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "}";
    str += "#"+selector+" .ba-post-author-title {";
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-post-author-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-post-author-title");
    str += "#"+selector+" .ba-post-author-social-wrapper {";
    str += "text-align: "+obj.intro.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-post-author-social-wrapper a {";
    str += "color: "+getCorrectColor(obj.intro.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-post-author-description {";
    str += getTypographyRule(obj.intro.typography);
    str += app.cssRules.get('margin', obj.intro.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-post-author-description:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-post-author-description");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getPostIntroRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .intro-post-wrapper.fullscreen-post {";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    if (obj.image.fullscreen) {
        str += "min-height: 100vh;";
    } else {
        str += "min-height: auto;";
    }
    str += "}";
    str += "#"+selector+" .ba-box-model > *:not(.ba-box-model-margin) {display: none;}";
    str += "#"+selector+" .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj, 'image');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-overlay:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-overlay");
    str += "#"+selector+" .intro-post-image {";
    str += "height :"+app.cssRules.getValueUnits(obj.image.height)+";";
    str += "background-attachment: "+obj.image.attachment+";";
    str += "background-position: "+obj.image.position+";";
    str += "background-repeat: "+obj.image.repeat+";";
    str += "background-size: "+obj.image.size+";";
    if (obj.image.fullscreen) {
        str += "min-height: 100vh;";
    } else {
        str += "min-height: auto;";
    }
    str += "}";
    str += "#"+selector+" .intro-post-title-wrapper {";
    str += "text-align :"+obj.title.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .intro-post-title {";
    str += getTypographyRule(obj.title.typography, 'text-align');
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .intro-post-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .intro-post-title");
    let justify = obj.info.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += "#"+selector+" .intro-post-info {";
    str += "text-align :"+obj.info.typography['text-align']+";";
    str += "justify-content: "+justify+";";
    str += app.cssRules.get('margin', obj.info.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .intro-post-info:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .intro-post-info");
    str += "#"+selector+" .intro-post-info *:not(i):not(a) {";
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";    
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getStarRatingsRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .star-ratings-wrapper {";
    str += "text-align: "+obj.icon['text-align']+";";
    str += "}";
    str += "#"+selector+" .stars-wrapper {";
    str += "color:"+getCorrectColor(obj.icon.color)+";";
    str += "}";
    str += "#"+selector+" .star-ratings-wrapper i {";
    str += "font-size:"+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "}";
    str += "#"+selector+" .star-ratings-wrapper i.active,#"+selector+" .star-ratings-wrapper i.active + i:after";
    str += ",#"+selector+" .stars-wrapper:hover i {";
    str += "color:"+getCorrectColor(obj.icon.hover)+";";
    str += "}";
    str += "#"+selector+" .info-wrapper * {";
    str += getTypographyRule(obj.info, 'text-align');
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getIconRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += "text-align: "+obj.icon['text-align']+";";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}"
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-icon-wrapper i {";
    str += "width : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "height : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "font-size : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += app.cssRules.getColors('colors', obj, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    if (obj.shadow) {
        str += app.cssRules.get('shadow', obj.shadow, 'default');
    }
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-icon-wrapper i:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-icon-wrapper i");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getRecentSliderRules(obj, selector)
{
    let str = "#"+selector+" {",
        margin = obj.gutter ? 30 : 0;
    margin = margin * (obj.slideset.count - 1);
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    if (obj.overflow) {
        str += "#"+selector+" ul.carousel-type .slideshow-content {";
        str += "width: calc(100% + (100% / "+obj.slideset.count+") * 2);";
        str += "margin-left: calc((100% / "+obj.slideset.count+") * -1);";
        str += "}";
    } else {
        str += "#"+selector+" ul.carousel-type .slideshow-content {";
        str += "width: 100%;";
        str += "margin-left: auto;";
        str += "}";
    }
    str += "#"+selector+" ul.carousel-type li {"
    str += "width: calc((100% - "+margin+"px) / "+obj.slideset.count+");";
    str += "}";
    str += "#"+selector+" ul.carousel-type:not(.slideset-loaded) li {";
    str += "position: relative; float:left;";
    str += "}";
    str += "#"+selector+" ul.carousel-type:not(.slideset-loaded) li.item.active:not(:first-child) {";
    str += "margin-left: "+(obj.gutter ? 30 : 0)+"px;";
    str += "}";
    str += "#"+selector+" ul.slideshow-type {";
    if (obj.view.fullscreen) {
        str += "min-height: 100vh;";
    } else {
        str += "min-height: auto;";
    }
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += "#"+selector+" ul.carousel-type .ba-slideshow-img {";
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-img {";
    str += "background-size :"+obj.view.size+";";
    str += "}";
    str += "#"+selector+" .slideset-wrapper .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj);
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .slideset-wrapper:hover .ba-overlay", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-overlay");
    str += "#"+selector+" .ba-slideshow-caption {";
    str += app.cssRules.get('overlay', obj.overlay, 'default');
    str += "}";
    app.cssRules.transitions = [];
    app.cssRules.states = {};
    str += "#"+selector+" .ba-blog-post-title {";
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-title");
    let price = obj.price && typeof obj.price == 'object' ? obj.price : obj.title;
    str += "#"+selector+" .ba-blog-post-add-to-cart-wrapper {";
    str += app.cssRules.get('margin', price.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-add-to-cart-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-add-to-cart-wrapper");
    str += "#"+selector+" .ba-blog-post-add-to-cart-price {";
    str += "align-items: "+price.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end')+";";
    str += getTypographyRule(price.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-blog-post-title {";
    str += getTypographyRule(obj.title.typography);
    str += "}";
    str += "#"+selector+" .ba-blog-post-title:hover {";
    str += "color: "+getCorrectColor(obj.title.hover.color)+";";
    str += "}";
    let justify = obj.reviews.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += "#"+selector+" .ba-blog-post-reviews {";
    str += "justify-content: "+justify+";";
    str += getTypographyRule(obj.reviews.typography, 'text-align');
    str += app.cssRules.get('margin', obj.reviews.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-reviews:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-reviews");
    str += "#"+selector+" .ba-blog-post-reviews a:hover {";
    str += "color: "+getCorrectColor(obj.reviews.hover.color)+";";
    str += "}";
    if (obj.postFields) {
        str += "#"+selector+" .ba-blog-post-field-row-wrapper {";
        str += getTypographyRule(obj.postFields.typography, 'text-align');
        str += app.cssRules.get('margin', obj.postFields.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-field-row-wrapper:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-field-row-wrapper");
    }
    justify = obj.info.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += "#"+selector+" .ba-blog-post-info-wrapper {";
    str += app.cssRules.get('margin', obj.info.margin, 'default');
    str += "justify-content: "+justify+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-info-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-info-wrapper");
    str += "#"+selector+" .ba-blog-post-info-wrapper > span * {";
    str += getTypographyRule(obj.info.typography, 'text-align');
    str += "}";
    str += "#"+selector+" .ba-blog-post-info-wrapper > span {";
    str += "color: "+getCorrectColor(obj.info.typography.color)+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-info-wrapper > * a:hover {";
    str += "color: "+getCorrectColor(obj.info.hover.color)+";";
    str += "}";
    str += "#"+selector+" .slideshow-button {";
    str += "text-align :"+obj.button.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-intro-wrapper {";
    str += getTypographyRule(obj.intro.typography);
    str += app.cssRules.get('margin', obj.intro.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-intro-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-intro-wrapper");
    str += "#"+selector+" .ba-blog-post-button-wrapper {";
    str += "text-align :"+obj.button.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-blog-post-button-wrapper a {";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-blog-post-button-wrapper a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-blog-post-button-wrapper a");
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .ba-blog-post-button-wrapper a, #"+selector+" .ba-blog-post-add-to-cart {";
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += "}";
    let query = "#"+selector+" .ba-blog-post-button-wrapper a:hover, #"+selector+" .ba-blog-post-add-to-cart:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .ba-blog-post-button-wrapper a, #"+selector+" .ba-blog-post-add-to-cart";
    str += app.cssRules.getTransitionRule(query);
    app.cssRules.prepareColors(obj.arrows);
    str += "#"+selector+" .ba-slideset-nav a {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.arrows.padding)+";";
    str += app.cssRules.getColors('colors', obj.arrows, 'default');
    str += app.cssRules.get('shadow', obj.arrows.shadow, 'default');
    str += app.cssRules.get('border', obj.arrows.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideset-nav a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-slideset-nav a");
    str += "#"+selector+" .ba-slideset-dots {";
    str += 'display:'+(obj.view.dots == 1 ? 'flex' : 'none')+';';
    str += "}";
    str += "#"+selector+" .ba-slideset-dots > div {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "color: "+getCorrectColor(obj.dots.normal.color)+";";
    str += "}";
    str += "#"+selector+" .ba-slideset-dots > div:hover,#"+selector+" .ba-slideset-dots > div.active {";
    str += "color: "+getCorrectColor(obj.dots.hover.color)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getTestimonialsRules(obj, selector)
{
    let str = "#"+selector+" {",
        margin = 30 * (obj.slideset.count - 1),
        query = '';
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" li {"
    str += "width: calc((100% - "+margin+"px) / "+obj.slideset.count+");";
    str += "}";
    str += "#"+selector+" ul.style-6 li {";
    str += "width: 100%;";
    str += "}";
    str += "#"+selector+" .slideshow-content .testimonials-wrapper, #"+selector+" .testimonials-info {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    str += "background-color: "+getCorrectColor(obj.background.color)+";";
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    query = "#"+selector+" .slideshow-content .testimonials-wrapper:hover, #"+selector+" .testimonials-info:hover";
    str += app.cssRules.getStateRule(query, 'hover');
    query = "#"+selector+" .slideshow-content .testimonials-wrapper, #"+selector+" .testimonials-info";
    str += app.cssRules.getTransitionRule(query);
    str += "#"+selector+" .testimonials-info:before {";
    str += "border-color: "+getCorrectColor(obj.background.color)+";";
    str += "left: calc("+app.cssRules.getValueUnits(obj.image.width)+" / 2);";
    str += "}";
    str += "#"+selector+" .testimonials-icon-wrapper i {";
    str += "width : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "height : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "font-size : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += "color : "+getCorrectColor(obj.icon.color)+";";
    str += "}";
    str += "#"+selector+" .testimonials-img {";
    str += "width:"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height:"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += app.cssRules.get('border', obj.image.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .testimonials-img:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .testimonials-img");
    str += "#"+selector+" ul.style-6 .ba-slideset-dots div {";
    str += "width:"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height:"+app.cssRules.getValueUnits(obj.image.width)+";";
    str += app.cssRules.get('border', obj.image.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" ul.style-6 .ba-slideset-dots div:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" ul.style-6 .ba-slideset-dots div");
    str += "#"+selector+" .ba-testimonials-name {";
    str += getTypographyRule(obj.name.typography);
    str += "}";
    str += "#"+selector+" .ba-testimonials-testimonial {";
    str += getTypographyRule(obj.testimonial.typography);
    str += "}";
    str += "#"+selector+" .ba-testimonials-caption {";
    str += getTypographyRule(obj.caption.typography);
    str += "}";
    str += "#"+selector+" .testimonials-slideshow-content-wrapper {";
    if (obj.view.arrows == 1) {
        str += "width: calc(100% - (40px + "+app.cssRules.getValueUnits(obj.arrows.padding)+" * 2 + "+app.cssRules.getValueUnits(obj.arrows.size)+"));"
    } else {
        str += "width: calc(100% - 50px);";
    }
    str += "}";
    str += "#"+selector+" .ba-slideset-nav a {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.arrows.padding)+";";
    str += app.cssRules.getColors('colors', obj.arrows, 'default');
    str += app.cssRules.get('shadow', obj.arrows.shadow, 'default');
    str += app.cssRules.get('border', obj.arrows.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideset-nav a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .ba-slideset-nav a');
    str += "#"+selector+" .ba-slideset-dots > div {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "color: "+getCorrectColor(obj.dots.normal.color)+";";
    str += "}";
    str += "#"+selector+" .ba-slideset-dots > div:hover,#"+selector+" .ba-slideset-dots > div.active {";
    str += "color: "+getCorrectColor(obj.dots.hover.color)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getCarouselRules(obj, selector)
{
    let str = "#"+selector+" {",
        margin = obj.gutter ? 30 : 0;
    margin = margin * (obj.slideset.count - 1);
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    if (obj.overflow) {
        str += "#"+selector+" .slideshow-content {";
        str += "width: calc(100% + (100% / "+obj.slideset.count+") * 2);";
        str += "margin-left: calc((100% / "+obj.slideset.count+") * -1);";
        str += "}";
    } else {
        str += "#"+selector+" .slideshow-content {";
        str += "width: 100%;";
        str += "margin-left: auto;";
        str += "}";
    }
    str += "#"+selector+" li {"
    str += "width: calc((100% - "+margin+"px) / "+obj.slideset.count+");";
    str += "}";
    str += "#"+selector+" ul:not(.slideset-loaded) li {";
    str += "position: relative; float:left;";
    str += "}";
    str += "#"+selector+" ul:not(.slideset-loaded) li.item.active:not(:first-child) {";
    str += "margin-left: "+(obj.gutter ? 30 : 0)+"px;";
    str += "}";
    for (var ind in obj.slides) {
        if (obj.slides[ind].image) {
            str += "#"+selector+" li.item:nth-child("+ind+") .ba-slideshow-img {background-image: url(";
            if (app.isExternal(obj.slides[ind].image)) {
                str += obj.slides[ind].image+");";
            } else {
                str += JUri+encodeURI(obj.slides[ind].image)+");";
            }
            str += "}"; 
        }
    }
    str += "#"+selector+" .ba-slideshow-img {";
    str += "background-size :"+obj.view.size+";";
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-caption {background-color :";
    if (!obj.overlay.type || obj.overlay.type == 'color') {
        str += getCorrectColor(obj.overlay.color)+";";
        str += 'background-image: none;';
    } else if (obj.overlay.type == 'none') {
        str += 'rgba(0, 0, 0, 0);';
        str += 'background-image: none;';
    } else {
        str += 'rgba(0, 0, 0, 0);';
        str += 'background-image: '+obj.overlay.gradient.effect+'-gradient(';
        if (obj.overlay.gradient.effect == 'linear') {
            str += obj.overlay.gradient.angle+'deg';
        } else {
            str += 'circle';
        }
        str += ', '+getCorrectColor(obj.overlay.gradient.color1)+' ';
        str += obj.overlay.gradient.position1+'%, '+getCorrectColor(obj.overlay.gradient.color2);
        str += ' '+obj.overlay.gradient.position2+'%);';
        str += 'background-attachment: scroll;';
    }
    str += "}";
    str += "#"+selector+" .slideshow-title-wrapper {";
    str += "text-align :"+obj.title.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-title {";
    str += getTypographyRule(obj.title.typography, 'text-align');
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideshow-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-slideshow-title");
    str += "#"+selector+" .slideshow-description-wrapper {";
    str += "text-align :"+obj.description.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-description {";
    str += getTypographyRule(obj.description.typography, 'text-align');
    str += app.cssRules.get('margin', obj.description.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideshow-description:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-slideshow-description");
    str += "#"+selector+" .slideshow-button {";
    str += "text-align :"+obj.button.typography['text-align']+";";
    str += "}";
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .slideshow-button:not(.empty-content) a {";
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .slideshow-button a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .slideshow-button a');
    app.cssRules.prepareColors(obj.arrows);
    str += "#"+selector+" .ba-slideset-nav a {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.arrows.padding)+";";
    str += app.cssRules.getColors('colors', obj.arrows, 'default');
    str += app.cssRules.get('shadow', obj.arrows.shadow, 'default');
    str += app.cssRules.get('border', obj.arrows.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideset-nav a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .ba-slideset-nav a');
    str += "#"+selector+" .ba-slideset-dots {";
    if (obj.view.dots == 1) {
        str += 'display:flex;';
    } else {
        str += 'display:none;';
    }
    str += "}";
    str += "#"+selector+" .ba-slideset-dots > div {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "color: "+getCorrectColor(obj.dots.normal.color)+";";
    str += "}";
    str += "#"+selector+" .ba-slideset-dots > div:hover,#"+selector+" .ba-slideset-dots > div.active {";
    str += "color: "+getCorrectColor(obj.dots.hover.color)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getContentSliderItemsRules(obj, selector)
{
    var str = '';
    str += selector+" > .ba-overlay {background-color: ";
    if (obj.overlay.type == 'color') {
        str += getCorrectColor(obj.overlay.color)+";";
        str += 'background-image: none;';
    } else if (obj.overlay.type == 'none') {
        str += 'rgba(0, 0, 0, 0);';
        str += 'background-image: none;';
    } else {
        str += 'rgba(0, 0, 0, 0);';
        str += 'background-image: '+obj.overlay.gradient.effect+'-gradient(';
        if (obj.overlay.gradient.effect == 'linear') {
            str += obj.overlay.gradient.angle+'deg';
        } else {
            str += 'circle';
        }
        str += ', '+getCorrectColor(obj.overlay.gradient.color1)+' ';
        str += obj.overlay.gradient.position1+'%, '+getCorrectColor(obj.overlay.gradient.color2);
        str += ' '+obj.overlay.gradient.position2+'%);';
        str += 'background-attachment: scroll;';
    }
    str += "}";
    str += selector+" > .ba-slideshow-img {";
    switch (obj.background.type) {
        case 'image' :
            if (obj.background.image) {
                var image = obj.background.image.image;
                if (app.isExternal(image)) {
                    str += "background-image: url("+image+");";
                } else {
                    str += "background-image: url("+JUri+encodeURI(image)+");";
                }
                for (var key in obj.background.image) {
                    if (key == 'image') {
                        continue;
                    }
                    str += "background-"+key+": "+obj.background.image[key]+";";
                }
            }
            str += "background-color: rgba(0, 0, 0, 0);";
            break;
        case 'gradient' :
            str += 'background-image: '+obj.background.gradient.effect+'-gradient(';
            if (obj.background.gradient.effect == 'linear') {
                str += obj.background.gradient.angle+'deg';
            } else {
                str += 'circle';
            }
            str += ', '+getCorrectColor(obj.background.gradient.color1)+' ';
            str += obj.background.gradient.position1+'%, '+getCorrectColor(obj.background.gradient.color2);
            str += ' '+obj.background.gradient.position2+'%);';
            str += "background-color: rgba(0, 0, 0, 0);";
            str += 'background-attachment: scroll;';
            break;
        case 'color' :
            str += "background-color: "+getCorrectColor(obj.background.color)+";";
            str += "background-image: none;";
            break;
        default :
            str += "background-image: none;";
            str += "background-color: rgba(0, 0, 0, 0);";
    }
    str += "}";
    
    return str;
}

function getContentSliderRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" > .slideshow-wrapper > .ba-slideshow:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" > .slideshow-wrapper > .ba-slideshow");
    str += "#"+selector+" > .slideshow-wrapper {";
    str += "min-height: "+(obj.view.fullscreen ? "100vh" : "auto")+";";
    str += "}";
    str += "#"+selector+" > .slideshow-wrapper > ul > .slideshow-content, #"+selector+" > .slideshow-wrapper > ul > .empty-list {";
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    let query = "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .slideshow-content > li.item > .ba-grid-column";
    str += query+" {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule(query+":hover", 'hover');
    str += app.cssRules.getTransitionRule(query);
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav {";
    str += 'display:'+(obj.view.arrows == 1 ? 'block' : 'none')+';';
    str += "}";
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav a {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.arrows.padding)+";";
    str += app.cssRules.getColors('colors', obj.arrows, 'default');
    str += app.cssRules.get('shadow', obj.arrows.shadow, 'default');
    str += app.cssRules.get('border', obj.arrows.border, 'default');
    str += "}";
    query = ' > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav a:hover';
    str += app.cssRules.getStateRule("#"+selector+query, 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav a');
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots {";
    str += 'display:'+(obj.view.dots == 1 ? 'flex' : 'none')+';';
    str += "}";
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "color: "+getCorrectColor(obj.dots.normal.color)+";";
    str += "}";
    str += "#"+selector+" > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div:hover,#"+selector;
    str += " > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div.active {";
    str += "color: "+getCorrectColor(obj.dots.hover.color)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getFeatureBoxRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-feature-box {";
    str += "width: calc((100% - "+((obj.view.count - 1) * 30)+"px) / "+obj.view.count+")";
    str += "}";
    str += "#"+selector+" .ba-feature-box:nth-child(n) {";
    str += "margin-right: 30px;";
    str += "margin-top: 30px;";
    str += "}";
    str += "#"+selector+" .ba-feature-box:nth-child("+obj.view.count+"n) {";
    str += "margin-right: 0;";
    str += "}";
    for (var i = 0; i < obj.view.count; i++) {
        str += "#"+selector+" .ba-feature-box:nth-child("+(i + 1)+") {";
        str += "margin-top: 0;";
        str += "}";
    }
    if (!obj.shadow.default) {
        obj.shadow.default = obj.shadow.normal;
        obj.shadow.state = true;
        obj.shadow.transition = app.cssRules.transition;
    }
    if (!obj.background.default) {
        obj.background.default = obj.background.normal;
        obj.background.state = true;
        obj.background.transition = app.cssRules.transition;
    }
    str += "#"+selector+" .ba-feature-box {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('backgroundColor', obj.background, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-box:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-feature-box");
    str += "#"+selector+" .ba-feature-box:hover .ba-feature-title {";
    str += "color : "+getCorrectColor(obj.title.hover.color)+";";
    str += "}";
    str += "#"+selector+" .ba-feature-box:hover .ba-feature-description-wrapper * {";
    str += "color : "+getCorrectColor(obj.description.hover.color)+";";
    str += "}";
    str += '#'+selector+' .ba-feature-image-wrapper[data-type="icon"] {';
    str += "text-align: "+obj.icon['text-align']+";";
    str += "}";
    str += '#'+selector+' .ba-feature-image-wrapper:not([data-type="icon"]) {';
    str += "text-align: "+obj.image['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-feature-image {";
    str += "width : "+app.cssRules.getValueUnits(obj.image.width)+";";
    str += "height : "+app.cssRules.getValueUnits(obj.image.height)+";";
    str += app.cssRules.get('border', obj.image.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-box:hover .ba-feature-image", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-feature-image");
    app.cssRules.prepareColors(obj.icon);
    str += "#"+selector+" .ba-feature-image-wrapper i {";
    str += "padding : "+app.cssRules.getValueUnits(obj.icon.padding)+";";
    str += "font-size : "+app.cssRules.getValueUnits(obj.icon.size)+";";
    str += app.cssRules.get('border', obj.icon.border, 'default');
    str += app.cssRules.getColors('colors', obj.icon, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-box:hover .ba-feature-image-wrapper i", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-feature-image-wrapper i");
    str += "#"+selector+" .ba-feature-title {";
    str += getTypographyRule(obj.title.typography);
    str += app.cssRules.get('margin', obj.title.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-title:hover", 'hover');
    app.cssRules.updateTransitions(obj.icon.colors, 'color');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-feature-title");
    str += "#"+selector+" .ba-feature-description-wrapper {";
    str += "text-align :"+obj.description.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-feature-description-wrapper * {";
    str += getTypographyRule(obj.description.typography, 'text-align');
    str += app.cssRules.get('margin', obj.description.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-description-wrapper *:hover", 'hover');
    app.cssRules.updateTransitions(obj.icon.colors, 'color');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-feature-description-wrapper *");
    str += "#"+selector+" .ba-feature-button {";
    str += "text-align :"+obj.button.typography['text-align']+";";
    str += "}";
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .ba-feature-button:not(.empty-content) a {";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-feature-button a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .ba-feature-button');
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getBeforeAfterSliderRules(obj, selector)
{
    let str = "#"+selector+" {",
        justify = obj.title.typography['text-align'].replace('left', 'flex-start').replace('right', 'flex-end');
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-before-after-wrapper {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-before-after-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-before-after-wrapper");
    app.cssRules.prepareColors(obj.slider);
    str += "#"+selector+" .ba-before-after-slider {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.slider.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.slider.padding)+";";
    str += app.cssRules.get('shadow', obj.slider.shadow, 'default');
    str += app.cssRules.get('border', obj.slider.border, 'default');
    str += app.cssRules.getColors('colors', obj.slider, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-before-after-slider:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-before-after-slider");
    str += "#"+selector+" .ba-before-after-divider {";
    str += "--divider-color: "+getCorrectColor(obj.divider.color)+";";
    str += "--divider-width: "+app.cssRules.getValueUnits(obj.divider.width)+";";
    str += "}";
    str += "#"+selector+" .ba-before-after-label {";
    str += getTypographyRule(obj.title.typography, 'text-align');
    str += "background-color: "+getCorrectColor(obj.title.background.color)+";";
    str += "}";
    str += "#"+selector+" .ba-before-after-overlay {";
    str += "align-items:"+justify;
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getSlideshowRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    for (var ind in obj.slides) {
        if (obj.slides[ind].image) {
            str += "#"+selector+" li.item:nth-child("+ind+") .ba-slideshow-img {background-image: url(";
            if (app.isExternal(obj.slides[ind].image)) {
                str += obj.slides[ind].image+");";
            } else {
                str += JUri+encodeURI(obj.slides[ind].image)+");";
            }
            str += "}";
            str += "#"+selector+' .thumbnails-dots div[data-ba-slide-to="'+(ind * 1 - 1)+'"] {background-image: url(';
            if (app.isExternal(obj.slides[ind].image)) {
                str += obj.slides[ind].image+");";
            } else {
                str += JUri+encodeURI(obj.slides[ind].image)+");";
            }
            str += "}";
        } else if (obj.slides[ind].type == 'video' && obj.slides[ind].video.type == 'youtube') {
            str += "#"+selector+' .thumbnails-dots div[data-ba-slide-to="'+(ind * 1 - 1)+'"] {';
            str += 'background-image: url(https://img.youtube.com/vi/'+obj.slides[ind].video.id+'/maxresdefault.jpg);';
            str += "}";
        } else if (obj.slides[ind].type == 'video' && obj.slides[ind].video.type == 'vimeo') {
            str += "#"+selector+' .thumbnails-dots div[data-ba-slide-to="'+(ind * 1 - 1)+'"] {';
            str += 'background-image: url(https://vumbnail.com/'+obj.slides[ind].video.id+'.jpg);';
            str += "}";
        } else if (obj.slides[ind].type == 'video' && !obj.slides[ind].video.thumbnail) {
            str += "#"+selector+' .thumbnails-dots div[data-ba-slide-to="'+(ind * 1 - 1)+'"] {';
            str += 'background-image: url('+JUri+'components/com_gridbox/assets/images/thumb-square.png);';
            str += "}";
        }
    }
    str += "#"+selector+" .slideshow-wrapper {";
    if (obj.view.fullscreen) {
        str += "min-height: 100vh;";
    } else {
        str += "min-height: auto;";
    }
    str += "}";
    str += "#"+selector+" .slideshow-content, #"+selector+" .empty-list {";
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-img, #"+selector+" .thumbnails-dots div {";
    str += "background-size :"+obj.view.size+";";
    str += "}";
    str += "#"+selector+" .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj);
    str += "height:"+app.cssRules.getValueUnits(obj.view.height)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .slideshow-wrapper:hover .ba-overlay", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-overlay");
    str += "#"+selector+" .slideshow-title-wrapper {";
    str += "text-align :"+obj.title.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-title {";
    str += "animation-duration :"+obj.title.animation.duration+"s;";
    str += "animation-delay :"+(obj.title.animation.delay ? obj.title.animation.delay : 0)+"s;";
    str += getTypographyRule(obj.title.typography, 'text-align');
    if (obj.title.margin) {
        str += app.cssRules.get('margin', obj.title.margin, 'default');
    }
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideshow-title:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-slideshow-title");
    str += "#"+selector+" .slideshow-description-wrapper {";
    str += "text-align :"+obj.description.typography['text-align']+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-description {";
    str += "animation-duration :"+obj.description.animation.duration+"s;";
    str += "animation-delay :"+(obj.description.animation.delay ? obj.description.animation.delay : 0)+"s;";
    str += getTypographyRule(obj.description.typography, 'text-align');
    str += app.cssRules.get('margin', obj.description.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideshow-description:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-slideshow-description");
    str += "#"+selector+" .slideshow-button {";
    str += "text-align :"+obj.button.typography['text-align']+";";
    str += "}";
    app.cssRules.prepareColors(obj.button);
    str += "#"+selector+" .slideshow-button:not(.empty-content) a {";
    str += "animation-duration :"+obj.button.animation.duration+"s;";
    str += "animation-delay :"+(obj.button.animation.delay ? obj.button.animation.delay : 0)+"s;";
    str += app.cssRules.get('margin', obj.button.margin, 'default');
    str += getTypographyRule(obj.button.typography, 'text-align');
    str += app.cssRules.get('border', obj.button.border, 'default');
    str += app.cssRules.get('shadow', obj.button.shadow, 'default');
    str += app.cssRules.getColors('colors', obj.button, 'default');
    str += app.cssRules.get('padding', obj.button.padding, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .slideshow-button a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .slideshow-button a");
    app.cssRules.prepareColors(obj.arrows);
    str += "#"+selector+" .ba-slideshow-nav a {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.arrows.size)+";";
    str += "padding : "+app.cssRules.getValueUnits(obj.arrows.padding)+";";
    str += app.cssRules.getColors('colors', obj.arrows, 'default');
    str += app.cssRules.get('shadow', obj.arrows.shadow, 'default');
    str += app.cssRules.get('border', obj.arrows.border, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-slideshow-nav a:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+' .ba-slideshow-nav a');
    if (!obj.thumbnails) {
        str += "#"+selector+" .ba-slideshow-dots {";
        str += 'display:'+(obj.view.dots == 1 ? 'flex' : 'none')+';';
        str += "}";
    } else {
        str += "#"+selector+" .slideshow-wrapper {";
        str += "--thumbnails-count:" +obj.thumbnails.count+";";
        str += "--bottom-thumbnails-height: "+app.cssRules.getValueUnits(obj.thumbnails.height)+";";
        if (obj.thumbnails.width) {
            str += "--left-thumbnails-width: "+app.cssRules.getValueUnits(obj.thumbnails.width)+";";
        }
        str += "}";
    }
    str += "#"+selector+" .ba-slideshow-dots:not(.thumbnails-dots) > div {";
    str += "font-size: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "width: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.dots.size)+";";
    str += "color: "+getCorrectColor(obj.dots.normal.color)+";";
    str += "}";
    str += "#"+selector+" .ba-slideshow-dots:not(.thumbnails-dots) > div:hover,#"+selector;
    str += " .ba-slideshow-dots:not(.thumbnails-dots) > div.active {";
    str += "color: "+getCorrectColor(obj.dots.hover.color)+";";
    str += "}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);
    
    return str;
}

function getVideoRules(obj, selector)
{
    let str = "#"+selector+" .ba-video-wrapper {";
    if (obj.border) {
        str += app.cssRules.get('border', obj.border, 'default');
    }
    if (obj.shadow) {
        str += app.cssRules.get('shadow', obj.shadow, 'default');
    }
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-video-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-video-wrapper");
    str += "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getPreloaderRules(obj, selector)
{
    var str = "#"+selector+" .preloader-wrapper, #"+selector+" .preloader-wrapper:before, ";
    str += "#"+selector+" .preloader-wrapper:after {";
    str += "background-color: "+getCorrectColor(obj.background)+";";
    str += "}";
    str += "#"+selector+" .preloader-wrapper:before, #"+selector+" .preloader-wrapper:after {";
    str += "border-color: "+getCorrectColor(obj.background)+";";
    str += "}";
    str += "#"+selector+" .preloader-point-wrapper {";
    str += "width: "+app.cssRules.getValueUnits(obj.size)+";";
    str += "height: "+app.cssRules.getValueUnits(obj.size)+";";
    str += "}";
    str += "#"+selector+" .preloader-point-wrapper div, #"+selector+" .preloader-point-wrapper div:before {";
    str += "background-color: "+getCorrectColor(obj.color)+";";
    str += "}";
    str += "#"+selector+" .preloader-image-wrapper {";
    str += "width: "+app.cssRules.getValueUnits(obj.width)+";";
    str += "}";
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getLottieRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += "text-align: "+obj.style.align+";";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-lottie-animations-wrapper {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "width: "+app.cssRules.getValueUnits(obj.style.width)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-lottie-animations-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-lottie-animations-wrapper");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getImageRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += "text-align: "+obj.style.align+";";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-image-wrapper {";
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "width: "+app.cssRules.getValueUnits(obj.style.width)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" .ba-image-wrapper:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" .ba-image-wrapper");
    if (obj.overlay) {
        str += "#"+selector+" .ba-image-wrapper {";
        str += "--transition-duration: "+obj.animation.duration+"s;"
        str += "}";
        str += "#"+selector+" .ba-image-item-caption .ba-caption-overlay {background-color :";
        if (!obj.overlay.type || obj.overlay.type == 'color') {
            str += getCorrectColor(obj.overlay.color)+";";
            str += 'background-image: none;';
        } else if (obj.overlay.type == 'none') {
            str += 'rgba(0, 0, 0, 0);';
            str += 'background-image: none;';
        } else {
            str += 'rgba(0, 0, 0, 0);';
            str += 'background-image: '+obj.overlay.gradient.effect+'-gradient(';
            if (obj.overlay.gradient.effect == 'linear') {
                str += obj.overlay.gradient.angle+'deg';
            } else {
                str += 'circle';
            }
            str += ', '+getCorrectColor(obj.overlay.gradient.color1)+' ';
            str += obj.overlay.gradient.position1+'%, '+getCorrectColor(obj.overlay.gradient.color2);
            str += ' '+obj.overlay.gradient.position2+'%);';
            str += 'background-attachment: scroll;';
        }
        str += "}";
        str += "#"+selector+" .ba-image-item-title {";
        str += getTypographyRule(obj.title.typography);
        str += app.cssRules.get('margin', obj.title.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-image-item-title:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-image-item-title");
        str += "#"+selector+" .ba-image-item-description {";
        str += getTypographyRule(obj.description.typography);
        str += app.cssRules.get('margin', obj.description.margin, 'default');
        str += "}";
        str += app.cssRules.getStateRule("#"+selector+" .ba-image-item-description:hover", 'hover');
        str += app.cssRules.getTransitionRule("#"+selector+" .ba-image-item-description");
    }
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getScrollTopRules(obj, selector)
{
    let str = "#"+selector+" {";
    if (obj.icons.align) {
        str += "text-align : "+obj.icons.align+";";
    }
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" i.ba-btn-transition {";
    str += app.cssRules.get('padding', obj.padding, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += app.cssRules.get('border', obj.border, 'default');
    str += app.cssRules.getColors('colors', obj, 'default');
    str += "font-size : "+app.cssRules.getValueUnits(obj.icons.size)+";";
    str += "width : "+app.cssRules.getValueUnits(obj.icons.size)+";";
    str += "height : "+app.cssRules.getValueUnits(obj.icons.size)+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+" i.ba-btn-transition:hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector+" i.ba-btn-transition");
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getLogoRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += "text-align: "+obj['text-align']+";";
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" img {";
    str += "width: "+app.cssRules.getValueUnits(obj.width)+";}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

function getMapRules(obj, selector)
{
    let str = "#"+selector+" {";
    str += app.cssRules.get('margin', obj.margin, 'default');
    str += app.cssRules.get('shadow', obj.shadow, 'default');
    str += "}";
    str += app.cssRules.getStateRule("#"+selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule("#"+selector);
    str += "#"+selector+" .ba-map-wrapper {";
    str += "height: "+app.cssRules.getValueUnits(obj.height)+";}";
    str += setBoxModel(obj, selector);
    str += setItemsVisability(obj.disable, "block", '#'+selector);

    return str;
}

app.sectionRules();