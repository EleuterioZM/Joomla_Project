/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.testimonials) {
    var file = document.createElement('script'),
        slidesetDelay = null,
        windowWidth = $g(window).width();
    file.onload = function(){
        if (app.modules.initTestimonials) {
            app.initTestimonials(app.modules.initTestimonials.data, app.modules.initTestimonials.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/testimonials/js/testimonials.js';
    document.head.appendChild(file);
} else if (app.modules.initTestimonials) {
    app.initTestimonials(app.modules.initTestimonials.data, app.modules.initTestimonials.selector);
}


$g(window).on('resize', function(){
    clearTimeout(slidesetDelay);
    slidesetDelay = setTimeout(function(){
        var width = $g(window).width();
        if (!disableResponsive && width != windowWidth) {
            windowWidth = width;
            $g('ul.ba-testimonials').each(function(){
                var key = $g(this).closest('.ba-item')[0].id,
                    object = getSlidesetObject(key);
                $g(this).testimonials(object);
            });
        }
    }, 300);
});

app.initTestimonials = function(obj, key){
    var content = $g('#'+key+' .slideshow-content'),
        object = getSlidesetObject(key);
    if (content.find('li.item:not(.ba-unpublished-html-item)').length == 0) {
        content.addClass('empty-content');
    } else {
        content.removeClass('empty-content');
    }
    $g('#'+key+' > .slideset-wrapper > ul').testimonials(object);
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
                }
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