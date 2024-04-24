/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.counter) {
    var file = document.createElement('script');
    file.onload = function(){
        if (app.modules.initcounter) {
            app.initcounter(app.modules.initcounter.data, app.modules.initcounter.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/counter/counter.js';
    document.head.append(file);
} else if (app.modules.initcounter) {
    app.initcounter(app.modules.initcounter.data, app.modules.initcounter.selector);
}

app.initcounter = function(obj, key){
    $g('#'+key+' span.counter-number').counter(obj.counter);
    initItems();
}