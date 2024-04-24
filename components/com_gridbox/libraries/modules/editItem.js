/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.editItem = function(obj, key){
    restoreTabs(app.edit);
    let parent = window.parent.app,
        left = window.top.document.body.style.getPropertyValue('--modal-cp-left').replace('px', ''),
        item = document.querySelector('#'+app.edit),
        title = str = '',
        modals = top.$g('.ba-modal-cp.draggable-modal-cp:not(#theme-settings-dialog):not(#page-structure-dialog):not(#version-history-dialog)'),
        type = app.items[app.edit].type;
    if (top.document.querySelector('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)')) {
        top.$g('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)').modal('hide');
    }
    if (left > window.top.innerWidth - 400) {
        window.top.document.body.style.setProperty('--modal-cp-left', '100px');
        window.top.document.body.style.setProperty('--modal-cp-top', '100px');
        window.top.document.body.style.setProperty('--modal-cp-height', window.top.innerHeight+'px');
    }
    parent.edit = app.items[app.edit];
    if (type == 'row' && $g(item).parent().parent().hasClass('ba-grid-column')) {
        type = 'nested-row';
    }
    title = parent.getTitle(type);
    modals.find('.ba-dialog-title').text(title);
    modals.find('.modal-header > span.status-icons').remove();
    if (app.items[app.edit].preset) {
        str = '<span class="status-icons"><i class="zmdi zmdi-roller"></i><span class="ba-tooltip ba-top">'+
            parent._('PRESET')+'</span></span>';
        modals.find('.ba-dialog-title').after(str);
    }
    if (document.getElementById(app.edit).dataset.global) {
        str = '<span class="status-icons"><i class="zmdi zmdi-globe"></i><span class="ba-tooltip ba-top">'+
            parent._('GLOBAL_ITEM')+'</span></span>';
        modals.find('.ba-dialog-title').after(str);
    }
    top.app.cp.show();
    switch (app.items[app.edit].type) {
        case 'breadcrumbs': 
            parent.checkModule('breadcrumbsEditor');
            break;
        case 'checkout-form':
        case 'submission-form':
            parent.checkModule('customerInfoEditor');
            break;
        case 'comments-box': 
        case 'reviews': 
            parent.checkModule('commentsBoxEditor');
            break;
        case 'recent-comments':
        case 'recent-reviews':
            parent.checkModule('recentCommentsEditor');
            break;
        case 'field':
        case 'field-group':
            parent.checkModule('fieldEditor');
            break;
        case 'lottie-animations':
            parent.checkModule('lottieAnimationsEditor');
            break;
        case 'login':
            parent.checkModule('loginEditor');
            break;
        case 'add-to-cart':
            parent.checkModule('addToCartEditor');
            break;
        case 'fields-filter':
            parent.checkModule('fieldsFilterEditor');
            break;
        case 'feature-box':
            parent.checkModule('featureBoxEditor');
            break;
        case 'hotspot':
            parent.checkModule('hotspotEditor');
            break;
        case 'search':
        case 'store-search':
            parent.checkModule('searchEditor');
            break;
        case 'yandex-maps':
            parent.checkModule('yandexMapsEditor');
            break;
        case 'icon-list':
            parent.checkModule('iconListEditor');
            break;
        case 'preloader':
            parent.checkModule('preloaderEditor');
            break;
        case 'recent-posts' :
        case 'search-result' :
        case 'store-search-result' :
        case 'related-posts' :
        case 'post-navigation' :
        case 'author' :
            parent.checkModule('recentPostsEditor');
            break;
        case 'blog-posts' :
            parent.checkModule('blogPostsEditor');
            break;
        case 'before-after-slider' :
            parent.checkModule('beforeAfterSliderEditor');
            break;
        case 'star-ratings' :
            parent.checkModule('starRatingsEditor');
            break;
        case 'post-intro' :
        case 'category-intro' :
            parent.checkModule('introPostEditor');
            break;
        case 'blog-content' :
            break;
        case 'disqus' :
        case 'vk-comments' :
        case 'hypercomments' :
        case 'facebook-comments' :
        case 'gallery' :
        case 'modules' :
        case 'forms' :
        case 'logo' :
        case 'simple-gallery':
        case 'field-simple-gallery':
        case 'product-gallery':
            parent.checkModule('itemEditor');
            break;
        case 'event-calendar':
            parent.checkModule('eventCalendarEditor');
            break;
        case 'video':
        case 'field-video':
        case 'image-field':
            parent.checkModule('imageEditor');
            break;
        case 'accordion' :
        case 'tabs' :
            parent.checkModule('tabsEditor');
            break;
        case 'field-google-maps':
        case 'google-maps-places':
            parent.checkModule('mapEditor');
            break;
        case 'image' :
        case 'text' :
        case 'map' :
        case 'social' :
        case 'slideshow' :
        case 'categories' :
        case 'headline' :
        case 'openstreetmap' :
            parent.checkModule(app.items[app.edit].type+'Editor');
            break;
        case 'testimonials-slider' :
            parent.checkModule('testimonialsEditor');
            break;
        case 'language-switcher' :
            parent.checkModule('languageSwitcherEditor');
            break;
        case 'currency-switcher' :
            parent.checkModule('currencySwitcherEditor');
            break;
        case 'search-result-headline' :
            parent.checkModule('headlineEditor');
            break;
        case 'weather':
        case 'error-message':
            parent.checkModule('weatherEditor');
            break;
        case 'field-slideshow':
        case 'product-slideshow':
        case 'slideset':
        case 'carousel':
        case 'recent-posts-slider':
        case 'related-posts-slider':
        case 'recently-viewed-products':
            parent.checkModule('slideshowEditor');
            break;
        case 'one-page' :
        case 'menu' :
            parent.checkModule('menuEditor');
            break;
        case 'social-icons' :
            parent.checkModule('socialIconsEditor');
            break;
        case 'content-slider' :
            parent.checkModule('contentSliderEditor');
            break;
        case 'cart':
        case 'wishlist':
            parent.checkModule('cartEditor');
            break;
        case 'icon':
        case 'button':
        case 'submit-button':
        case 'tags':
        case 'post-tags':
        case 'overlay-button':
        case 'scroll-to-top':
        case 'scroll-to':
        case 'countdown':
        case 'counter':
        case 'field-button':
            parent.checkModule('countdownEditor');
            break;
        case 'progress-bar' :
        case 'progress-pie' :
            parent.checkModule('progressBarEditor');
            break;
        case 'reading-progress-bar' :
            parent.checkModule('readingProgressBarEditor');
            break;
        case 'custom-html' :
            parent.checkModule('editCustomHtml');
            break;
        default :
            parent.checkModule('sectionEditor');
    }
}

app.editItem();