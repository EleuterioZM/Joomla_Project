app.lazyLoad = {
    setEvents: function(){
        $g(window).on('scroll.gridbox-lazyload resize.gridbox-lazyload', app.lazyLoad.check);
        $g('.ba-overlay-section-backdrop').on('scroll.gridbox-lazyload', app.lazyLoad.check);
        $g(document).on('shown.gridbox-lazyload', app.lazyLoad.check);
        app.lazyLoad.events = true;
    },
    setImage: function(item){
        if (item.localName == 'img') {
            item.onload = () => {
                $g(window).trigger("resize");
            }
            item.src = item.dataset.gridboxLazyloadSrc;
            if (item.dataset.gridboxLazyloadSrcset) {
                item.srcset = item.dataset.gridboxLazyloadSrcset;
            }
        }
    },
    check: function(){
        if (app.preloaded.total != 0 && app.preloaded.total > app.preloaded.loaded) {
            return;
        }
        let images = $g('.lazy-load-image'),
            style;
        images.each(function(){
            style = this.getBoundingClientRect();
            if (window.innerHeight * 2 >= style.top && (style.offsetWidth != 0 || style.offsetHeight != 0)) {
                this.classList.remove('lazy-load-image');
                app.lazyLoad.setImage(this);
                if (this.classList.contains('slideshow-content')) {
                    $g(this).find('.lazy-load-image').removeClass('lazy-load-image').each(function(){
                        app.lazyLoad.setImage(this);
                    });
                }
            }
        });
        if (images.length == 0) {
            $g(window).off('scroll.gridbox-lazyload resize.gridbox-lazyload');
            $g('.ba-overlay-section-backdrop').off('scroll.gridbox-lazyload');
            $g(document).off('shown.gridbox-lazyload');
            $g('li.megamenu-item').off('mouseenter.gridbox-lazyload');
            app.lazyLoad.events = false;
        } else if (!app.lazyLoad.events) {
            app.lazyLoad.setEvents();
        }
    }
}

app.lazyLoad.setEvents();
document.addEventListener('DOMContentLoaded', function(){
    app.lazyLoad.check();
    $g('li.megamenu-item').on('mouseenter.gridbox-lazyload', app.lazyLoad.check);
});