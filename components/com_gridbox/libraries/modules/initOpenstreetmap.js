/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initOpenstreetmap = function(obj, key){
    if ((window.themeData && themeData.page.view != 'gridbox') || app.openstreetmap) {
        var container = document.createElement('div'),
            options = {
                boxZoom: obj.map.scrollwheel,
                scrollWheelZoom: obj.map.scrollwheel,
                touchZoom: obj.map.scrollwheel,
                doubleClickZoom: obj.map.scrollwheel,
                zoomControl: obj.map.controls,
                dragging: obj.map.draggable,
                tap: obj.map.draggable

            };
        container.className = 'ba-map-wrapper';
        $g('#'+key+' .ba-map-wrapper').replaceWith(container);
        var map = L.map(container, options).setView(obj.map.center, obj.map.zoom);
        L.tileLayer(obj.map.theme, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.control.scale({
            metric: true,
            imperial: false
        }).addTo(map);
        for (var ind in obj.marker) {
            var object = obj.marker[ind];
            if ('position' in object) {
                var marker = L.marker(object.position).addTo(map);
                if (object.description) {
                    marker.bindPopup(object.description);
                    if (object.infobox) {
                        marker.openPopup();
                    }
                }
            }
        }
    }
    initItems();
}

if (window.themeData && themeData.page.view == 'gridbox' && !app.openstreetmap) {
    app.openstreetmapLink = document.createElement('link'),
    app.openstreetmapScript = document.createElement('script');
    app.openstreetmapLink.rel = 'stylesheet';
    app.openstreetmapLink.type = 'text/css';
    app.openstreetmapLink.href = 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.css'
    app.openstreetmapScript.src = 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.js';
    document.head.appendChild(app.openstreetmapLink);
    $g(app.openstreetmapLink).after(app.openstreetmapScript);
    window.openstreetmapInterval = setInterval(function(){
        if (window.L) {
            clearInterval(window.openstreetmapInterval);
            app.openstreetmap = true;
            if (app.modules.initOpenstreetmap) {
                app.initOpenstreetmap(app.modules.initOpenstreetmap.data, app.modules.initOpenstreetmap.selector);
            }
        }
    }, 100);
} else if (app.openstreetmap) {
    app.initOpenstreetmap(app.modules.initOpenstreetmap.data, app.modules.initOpenstreetmap.selector);
}