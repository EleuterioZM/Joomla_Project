/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initaccordion = function(obj, key){
    $g('#'+key+' .accordion').on('click', '.accordion-toggle', function(e){
        var $this = this;
        if (this.dataset.clicked != 'true') {
            this.dataset.clicked = true;
            setTimeout(function(){
                $this.dataset.clicked = false;
            }, 500);
            if (this.classList.contains('active')) {
                this.classList.remove('active');
            } else {
                $g(this).closest('.accordion').find('> .accordion-group > .accordion-heading .active').removeClass('active');
                this.classList.add('active');
                var search = '.ba-item-slideshow, .ba-item-slideset, .ba-item-carousel, .ba-item-map, '+
                    '.ba-item-recent-posts-slider, .ba-item-related-posts-slider, .ba-item-recently-viewed-products';
                $g(this.hash).find(search).each(function(){
                    var object = {
                        data : app.items[this.id],
                        selector : this.id
                    };
                    app.checkModule('initItems', object);
                });
                if ('setGalleryMasonryHeight' in window) {
                    $g(this.hash).find('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                        setGalleryMasonryHeight(this.closest('.ba-item').id);
                    });
                }
            }
        }
    }).on('shown', function(e){
        if (obj.autoscroll && obj.autoscroll.enable) {
            let position = compileOnePageValue(jQuery('a[href="#'+e.target.id+'"]'));
            jQuery('html, body').animate({
                scrollTop: position
            }, 'slow');
        }
    });
    initItems();
}

if (app.modules.initaccordion) {
    app.initaccordion(app.modules.initaccordion.data, app.modules.initaccordion.selector);
}