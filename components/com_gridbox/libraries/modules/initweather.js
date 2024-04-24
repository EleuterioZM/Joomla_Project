/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.weather) {
    var file = document.createElement('script');
    file.onload = function(){
        if (app.modules.initweather) {
            app.initweather(app.modules.initweather.data, app.modules.initweather.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/weather/js/weather.js';
    document.head.append(file);
} else if (app.modules.initweather) {
    app.initweather(app.modules.initweather.data, app.modules.initweather.selector);
}

var file = document.createElement('link');
file.rel = 'stylesheet';
file.type = 'text/css';
file.href = 'https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css';
document.head.append(file);

app.initweather = function(obj, key){
    $g('#'+key+' .ba-weather').weather(obj.weather, obj.desktop.view);
    initItems();
}