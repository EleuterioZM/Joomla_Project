/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.smoothScroll) {
    var file = document.createElement('script');
    file.onload = function(){
        if (app.modules['initscroll-to']) {
            app['initscroll-to'](app.modules['initscroll-to'].data, app.modules['initscroll-to'].selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/smoothScroll/smoothScroll.js';
    document.head.append(file);
} else if (app.modules['initscroll-to']) {
    app['initscroll-to'](app.modules['initscroll-to'].data, app.modules['initscroll-to'].selector);
}

app['initscroll-to'] = function(obj, key){
    $g('#'+key+' a.ba-btn-transition').smoothScroll(obj.init);
    initItems();
}