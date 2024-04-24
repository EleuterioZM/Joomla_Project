/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function setGalleryMasonryHeight(key)
{
    var wrapper = document.querySelector('#'+key+' .instagram-wrapper'),
        computed = null,
        gap = 20,
        height = 0;
    $g('#'+key+' .instagram-wrapper:not(.simple-gallery-masonry-layout) .ba-instagram-image').each(function(ind){
        this.classList.remove('ba-masonry-image-loaded');
        this.style.gridRowEnd = '';
    });
    $g('#'+key+' .simple-gallery-masonry-layout .ba-instagram-image').each(function(ind){
        var post = this,
            offsetHeight = 0,
            $this = this.querySelector('img'),
            img = document.createElement('img');
        if (!computed) {
            computed = getComputedStyle(this)
        }
        offsetHeight += (computed.borderBottomWidth.replace(/[^\d\.]/g, '') * 1)+(computed.borderTopWidth.replace(/[^\d\.]/g, '') * 1);
        if ($this.src.indexOf('default-lazy-load.webp') != -1) {
            $this.onload = function(){
                offsetHeight += $this.offsetHeight;
                post.style.gridRowEnd = "span "+Math.ceil(((offsetHeight + gap) / (height + gap)) + 0);
                if (!post.classList.contains('ba-masonry-image-loaded')) {
                    post.classList.add('ba-masonry-image-loaded');
                }
            }
        } else {
            img.onload = function(){
                offsetHeight += $this.offsetHeight;
                post.style.gridRowEnd = "span "+Math.ceil(((offsetHeight + gap) / (height + gap)) + 0);
                if (!post.classList.contains('ba-masonry-image-loaded')) {
                    post.classList.add('ba-masonry-image-loaded');
                }
            }
            img.src = $this.src;
        }
    });
}

if (('$g' in window) && window.$g) {
    $g('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
        setGalleryMasonryHeight(this.closest('.ba-item').id);
    });
}