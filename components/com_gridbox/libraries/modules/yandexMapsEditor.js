/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.yandexMapsScript = document.createElement('script');
app.yandexMapsScript.onload = function(){
    ymaps.ready(function(){
        app.ymaps = true;
        initYandexLocation();
    });
}
app.yandexMapsScript.src = 'https://api-maps.yandex.ru/2.1/?apikey='+integrations.yandex_maps.key+'&lang=ru_RU';
document.head.appendChild(app.yandexMapsScript);

var yandexMap,
    locationMarkers = {},
    markerIndex = null;

app.yandexMapsEditor = function() {
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#yandex-maps-editor-dialog');
    setDisableState('#yandex-maps-editor-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    value = app.getValue('height');
    app.setLinearInput(modal.find('input[data-option="height"]'), value);
    $g('.map-theme-select input[type="hidden"]').val(app.edit.styleType);
    value = $g('.map-theme-select li[data-value="'+app.edit.styleType+'"]').text();
    $g('.map-theme-select input[readonly]').val(value.trim());
    app.setDefaultState('#yandex-maps-editor-dialog .margin-settings-group', 'default');
    app.setMarginValues('#yandex-maps-editor-dialog .margin-settings-group');
    app.setDefaultState('#yandex-maps-editor-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#yandex-maps-editor-dialog .shadow-settings-group');
    $g('#yandex-maps-editor-dialog input[data-group="map"]').each(function(){
        this.checked = app.edit.map[this.dataset.option];
    });
    initYandexLocation();
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function setYandexMarker(ind)
{
    var object = app.edit.marker[ind];
    if ('position' in object) {
        if (locationMarkers[ind]) {
            locationMarkers[ind].marker.setParent(null);
        } else {
            locationMarkers[ind] = {
                marker: null
            };
        }
        locationMarkers[ind].marker = new ymaps.Placemark(object.position, {
            balloonContentHeader: object.title,
            balloonContentBody: object.description
        }, {
            preset: 'islands#icon',
            iconColor: app.editor.app.getCorrectColor(object.color)
        });
        yandexMap.geoObjects.add(locationMarkers[ind].marker);
    }
}

function setYandexAutocomplete(ind)
{
    var locationInput = $g('.yandex-choose-location-input[data-marker="'+ind+'"]');
    locationInput.val(app.edit.marker[ind].place).off('input').on('input', function(){
        clearTimeout(this.delay);
        var $this = this;
        this.delay = setTimeout(function(){
            ymaps.geocode($this.value.trim(), {
                results: 1
            }).then(function(res){
                var center = res.geoObjects.get(0).geometry.getCoordinates();
                yandexMap.setCenter(center);
                app.edit.marker[ind].place = $this.value;
                app.edit.map.center = center;
            });
        }, 500);
    }).off('click').on('click', function(){
        markerIndex = this.dataset.marker;
    });
    $g('#yandex-maps-editor-dialog .add-new-item i').attr('data-index', ind * 1 + 1);
}

function initYandexMarker(ind)
{
    if (ind != 0) {
        var div = document.querySelector('#yandex-choose-location').parentNode.cloneNode(true),
            input = div.querySelector('input');
        input.id = '';
        input.dataset.marker = ind;
        div.dataset.marker = ind;
        $g('#yandex-maps-editor-dialog .sorting-container').append(div);
    }
    setYandexMarker(ind);
    setYandexAutocomplete(ind);
}

function initYandexMarkers()
{
    markerIndex = null;
    locationMarkers = {};
    $g('#yandex-maps-editor-dialog .sorting-item:not([data-marker="0"])').remove();
    for (var ind in app.edit.marker) {
        initYandexMarker(ind)
    }
}

function initYandexLocation()
{
    if (!app.ymaps) {
        return false;
    }
    $g('#yandex-map-location').empty();
    var options = {
        center: app.edit.map.center,
        zoom: app.edit.map.zoom,
        controls: ['zoomControl']
    }
    yandexMap = new ymaps.Map("yandex-map-location", options);
    initYandexMarkers();
    yandexMap.events.add('click', function(event) {
        if (markerIndex) {
            app.edit.marker[markerIndex].position = event.get('coords');
            setYandexMarker(markerIndex);
        }
    });
}

$g('#yandex-maps-editor-dialog .sorting-container').on('click', 'i.zmdi-close', function(){
    app.itemDelete = $g(this).closest('.sorting-item').attr('data-marker');
    app.checkModule('deleteItem');
});

$g('#yandex-maps-editor-dialog .add-new-item i').on('click', function(){
    app.edit.marker[this.dataset.index] = {
        "place": "",
        "color": "#0095b6",
        "title" : "",
        "description" : ""
    }
    initYandexMarker(this.dataset.index);
    app.addHistory();
});

$g('#yandex-maps-editor-dialog .sorting-container').on('click', 'i.zmdi-edit', function(){
    var ind = $g(this).closest('.sorting-item').attr('data-marker'),
        modal = $g('#yandex-maps-item-dialog');
    $g('#apply-yandex-marker').attr('data-index', ind);
    modal.find('input[data-option="title"]').val(app.edit.marker[ind].title);
    modal.find('textarea[data-option="description"]').val(app.edit.marker[ind].description);
    updateInput(modal.find('input[data-option="color"]'), app.edit.marker[ind].color);
    modal.modal();
});

$g('#apply-yandex-marker').on('click', function(){
    var ind = this.dataset.index,
        modal = $g('#yandex-maps-item-dialog');
    app.edit.marker[ind].title = modal.find('input[data-option="title"]').val().trim();
    app.edit.marker[ind].description = modal.find('textarea[data-option="description"]').val().trim();
    app.edit.marker[ind].color = modal.find('input[data-option="color"]').attr('data-rgba').trim();
    setYandexMarker(ind);
    app.addHistory();
    modal.modal('hide');
});

$g('#yandex-maps-editor-dialog').on('hide', function(){
    app.edit.map.center = yandexMap.getCenter();
    app.edit.map.zoom = yandexMap.getZoom();
    app.editor.app.initYandexMaps(app.edit, app.editor.app.edit);
});

app.modules.yandexMapsEditor = true;
app.yandexMapsEditor();