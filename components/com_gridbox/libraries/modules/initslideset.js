/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var slidesetDelay = null,
    windowWidth = $g(window).width();

if (!$g.fn.slideset && !$g.fn.gridboxCarousel) {
    var file = document.createElement('script');
    file.onload = function(){
        app.slidesetFlag = true;
        if (app.carouselFlag && app.slidesetFlag && app.modules.initslideset) {
            app.initslideset(app.modules.initslideset.data, app.modules.initslideset.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/slideset/js/slideset.js';
    document.head.append(file);
    file = document.createElement('script')
    file.onload = function(){
        app.carouselFlag = true;
        if (app.carouselFlag && app.slidesetFlag && app.modules.initslideset) {
            app.initslideset(app.modules.initslideset.data, app.modules.initslideset.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/carousel/js/carousel.js';
    document.head.append(file);
    file = document.createElement('link');
    file.rel = 'stylesheet';
    file.href = JUri+'components/com_gridbox/libraries/slideshow/css/animation.css';
    document.head.append(file);
} else if (app.modules.initslideset) {
    app.initslideset(app.modules.initslideset.data, app.modules.initslideset.selector);
}

$g(window).on('resize', function(){
    clearTimeout(slidesetDelay);
    slidesetDelay = setTimeout(function(){
        var width = $g(window).width();
        if (!disableResponsive && width != windowWidth) {
            windowWidth = width;
            $g('ul.ba-slideset').each(function(){
                var key = $g(this).closest('.ba-item')[0].id,
                    object = getSlidesetObject(key),
                    action = object.mode == 'set' ? 'slideset' : 'gridboxCarousel';
                $g(this)[action](object);
            });
        }
    }, 300);
});

app.initslideset = function(obj, key){
    var content = $g('#'+key+' .slideshow-content'),
        object = getSlidesetObject(key),
        action = object.mode == 'set' ? 'slideset' : 'gridboxCarousel';
    if (content.find('li.item').length == 0) {
        content.addClass('empty-content');
    } else {
        content.removeClass('empty-content');
    }

    $g('#'+key).off('mouseover.options').on('mouseover.options', '.ba-blog-post-product-option', function(event){
        let search = 'ba-blog-post-product-option',
            t1 = event.target ? event.target.closest('.'+search) : null,
            t2 = event.relatedTarget ? event.relatedTarget.closest('.'+search) : null;
        if (t1 != t2) {
            let post = this.closest('li');
            if (this.dataset.image) {
                let image = !app.isExternal(this.dataset.image) ? JUri+this.dataset.image : this.dataset.image;
                post.style.setProperty('--product-option-image', 'url('+image+')');
                post.classList.add('product-option-hovered');
            } else {
                post.classList.remove('product-option-hovered');
                post.style.setProperty('--product-option-image', '');
            }
        }
    }).off('mouseout.options').on('mouseout.options', '.ba-blog-post-product-option', function(event){
        let search = 'ba-blog-post-product-option',
            t1 = event.target ? event.target.closest('.'+search) : null,
            t2 = event.relatedTarget ? event.relatedTarget.closest('.'+search) : null;
        if (t1 != t2 && (!t2 || !t2.classList.contains(search))) {
            let post = this.closest('li');
            post.classList.remove('product-option-hovered');
            post.style.setProperty('--product-option-image', '');
        }
    }).off('click.wishlist').on('click.wishlist', '.ba-blog-post-wishlist-wrapper', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let post = this.closest('li')
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.addPostToWishlist', {
            id: post.dataset.id
        }).then(function(text){
            let response = JSON.parse(text),
                str = '';
            if (response.status) {
                if (response.data.images.length) {
                    response.data.image = response.data.images[0];
                }
                if (response.data.image && !app.isExternal(response.data.image)) {
                    response.data.image = JUri+response.data.image;
                }
                str = '<span class="ba-product-notice-message">';
                if (response.data.image) {
                    str += '<span class="ba-product-notice-image-wrapper"><img src="'+response.data.image+'"></span>';
                }
                str += '<span class="ba-product-notice-text-wrapper">'+response.data.title+
                    ' '+gridboxLanguage['ADDED_TO_WISHLIST']+'</span></span>';
                app.showNotice(str, 'ba-product-notice');
                if (app.wishlist) {
                    app.wishlist.updateWishlist();
                }
            } else {
                localStorage.setItem('select-options', gridboxLanguage['PLEASE_SELECT_OPTION']);
                post.querySelector('.ba-blog-post-title a').click();
            }
        });
    }).off('click.cart').on('click.cart', '.ba-blog-post-add-to-cart', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let post = this.closest('li')
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.addPostToCart', {
            id: post.dataset.id
        }).then(function(text){
            let response = JSON.parse(text),
                str = '';
            if (response.status) {
                if (app.storeCart) {
                    app.storeCart.updateCartTotal();
                    $g('.ba-item-cart a').first().trigger('click');
                }
            } else {
                localStorage.setItem('select-options', gridboxLanguage['PLEASE_SELECT_OPTION']);
                post.querySelector('.ba-blog-post-title a').click();
            }
        });
    });
    $g('#'+key+' > .slideset-wrapper > ul')[action](object);
    initItems();
}

function getSlidesetObject(key)
{
    var object = $g.extend(true, {}, app.items[key].desktop.slideset);
    if (app.view != 'desktop' && !disableResponsive) {
        for (var ind in breakpoints) {
            if (!app.items[key][ind]) {
                app.items[key][ind] = {
                    slideset : {}
                };
            }
            object = $g.extend(true, {}, object, app.items[key][ind].slideset);
            if (ind == app.view) {
                break;
            }
        }
    }
    object.gutter = app.items[key].desktop.gutter;
    object.overflow = app.items[key].desktop.overflow;
    if (app.view != 'desktop') {
        for (var ind in breakpoints) {
            if (!app.items[key][ind]) {
                continue;
            }
            if ('gutter' in app.items[key][ind]) {
                object.gutter = app.items[key][ind].gutter;
            }
            if ('overflow' in app.items[key][ind]) {
                object.overflow = app.items[key][ind].overflow;
            }
            if (ind == app.view) {
                break;
            }
        }
    }

    return object;
}