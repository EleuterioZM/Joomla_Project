/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.inittabs = function(obj, key){
    $g('#'+key+' a[data-toggle="tab"]').on('shown', function(e){
        let search = '.ba-item-slideshow, .ba-item-slideset, .ba-item-carousel, .ba-item-map, .ba-item-recent-posts-slider, .ba-item-openstreetmap';
        $g(this.hash).find(search).each(function(){
            let object = {
                data: app.items[this.id],
                selector: this.id
            };
            app.checkModule('initItems', object);
        });
        if ('setGalleryMasonryHeight' in window) {
            $g(this.hash).find('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                setGalleryMasonryHeight(this.closest('.ba-item').id);
            });
        }
    });
    initItems();
}

if (app.modules.inittabs) {
    app.inittabs(app.modules.inittabs.data, app.modules.inittabs.selector);
}