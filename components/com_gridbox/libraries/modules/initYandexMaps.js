/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initYandexMaps = function(obj, key){
    if (!obj && !key) {
        $g('.ba-item-yandex-maps').each(function(){
            if (app.items[this.id]) {
                app.initYandexMaps(app.items[this.id], this.id);
            }
        });
    } else if (app.ymaps && obj && key) {
        var mapContainer =  $g('#'+key+' .ba-map-wrapper').empty(),
            options = {
                autoFitToViewport: 'always',
                center: obj.map.center,
                zoom: obj.map.zoom,
                controls: !obj.map.controls ? [] :[
                    "geolocationControl",
                    "routeButtonControl",
                    "trafficControl",
                    "typeSelector",
                    "fullscreenControl",
                    "zoomControl",
                    "rulerControl"
                ]
            },
            yandexMap = new ymaps.Map(mapContainer[0], options);
        if (!obj.map.scrollwheel) {
            yandexMap.behaviors.disable('scrollZoom');
        }
        if (!obj.map.draggable) {
            yandexMap.behaviors.disable('drag');
        }
        for (var ind in obj.marker) {
            var object = obj.marker[ind];
            if ('position' in object) {
                var marker = new ymaps.Placemark(object.position, {
                    balloonContentHeader: object.title,
                    balloonContentBody: object.description
                }, {
                    preset: 'islands#icon',
                    iconColor: app.getCorrectColor(object.color)
                });
                yandexMap.geoObjects.add(marker);
            }
        }
    }
    initItems();
}

if (window.themeData && themeData.page.view == 'gridbox' && !app.ymaps) {
    app.yandexMapsScript = document.createElement('script');
    app.yandexMapsScript.onload = function(){
        ymaps.ready(function(){
            app.ymaps = true;
            if (app.modules.initYandexMaps) {
                app.initYandexMaps(app.modules.initYandexMaps.data, app.modules.initYandexMaps.selector);
            }
        });
    }
    app.yandexMapsScript.onerror = function(){
        initItems();
    }
    app.yandexMapsScript.src = 'https://api-maps.yandex.ru/2.1/?apikey='+top.integrations.yandex_maps.key+'&lang=ru_RU';
    document.head.appendChild(app.yandexMapsScript);
} else {
    if (app.ymaps) {
        app.initYandexMaps(app.modules.initYandexMaps.data, app.modules.initYandexMaps.selector);
    } else if (!window.ymaps) {
        initItems();
    }
}