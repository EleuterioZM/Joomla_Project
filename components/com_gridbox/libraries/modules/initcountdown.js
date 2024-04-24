/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.countdown) {
    var file = document.createElement('script');
    file.onload = function(){
        if (app.modules.initcountdown) {
            app.initcountdown(app.modules.initcountdown.data, app.modules.initcountdown.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/countdown/countdown.js';
    document.head.append(file);
} else if (app.modules.initcountdown) {
    app.initcountdown(app.modules.initcountdown.data, app.modules.initcountdown.selector);
}

app.initcountdown = function(obj, key){
    $g('#'+key).countdown({
        end : obj.date,
        mode : obj.display,
        callback : function(){
            if (obj['hide-after']) {
                $g('#'+key).find('.ba-countdown').hide();
            }
        }
    });
    initItems();
}