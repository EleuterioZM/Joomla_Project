/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


var openstreetmapLink = document.createElement('link'),
    openstreetmapScript = document.createElement('script');
openstreetmapLink.rel = 'stylesheet';
openstreetmapLink.type = 'text/css';
openstreetmapLink.href = 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.css'
openstreetmapScript.src = 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.js';
document.head.appendChild(openstreetmapLink);
$g(openstreetmapLink).after(openstreetmapScript);
window.openstreetmapInterval = setInterval(function(){
    if (window.L) {
        clearInterval(window.openstreetmapInterval);
        app.openstreetmap = true;
        initOpenstreetmapLocation();
    }
}, 100);
var openstreetmap,
    locationMarkers = {},
    markerIndex = null;

app.openstreetmapEditor = function() {
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#openstreetmap-editor-dialog');
    setDisableState('#openstreetmap-editor-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    value = app.getValue('height');
    app.setLinearInput(modal.find('input[data-option="height"]'), value);
    app.setDefaultState('#openstreetmap-editor-dialog .margin-settings-group', 'default');
    app.setMarginValues('#openstreetmap-editor-dialog .margin-settings-group');
    app.setDefaultState('#openstreetmap-editor-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#openstreetmap-editor-dialog .shadow-settings-group');
    $g('#openstreetmap-editor-dialog input[data-group="map"]').each(function(){
        this.checked = app.edit.map[this.dataset.option];
    });
    $g('.openstreetmap-theme-select input[type="hidden"]').val(app.edit.map.theme);
    value = $g('.openstreetmap-theme-select li[data-value="'+app.edit.map.theme+'"]').text().trim();
    $g('.openstreetmap-theme-select input[readonly]').val(value);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('#openstreetmap-editor-dialog').on('shown', function(){
    this.modalShown = true;
    initOpenstreetmapLocation();
});

function setOpenstreetmapMarker(ind)
{
    var object = app.edit.marker[ind];
    if ('position' in object) {
        if (locationMarkers[ind]) {
            locationMarkers[ind].marker.remove();
        } else {
            locationMarkers[ind] = {
                marker: null
            };
        }
        locationMarkers[ind].marker = L.marker(object.position).addTo(openstreetmap);
        if (object.description) {
            locationMarkers[ind].marker.bindPopup(object.description);
            if (object.infobox) {
                locationMarkers[ind].marker.openPopup();
            }
        }
    }
}

function setOpenstreetmapAutocomplete(ind)
{
    
    var locationInput = $g('.openstreetmap-choose-location-input[data-marker="'+ind+'"]'),
        array = ind == 0 ? app.edit.map.center : app.edit.marker[ind].place,
        value = array ? array.join(', ') : '';
    locationInput.val(value).off('input').on('input', function(){
        clearTimeout(this.delay);
        var $this = this;
        this.delay = setTimeout(function(){
            var array = $this.value.split(',');
            if (array.length == 2 && array[0] * 1 != NaN && array[1] * 1 != NaN) {
                app.edit.map.center = array;
                app.edit.marker[ind].place = array;
                openstreetmap.setView(app.edit.map.center, openstreetmap.getZoom())
            }
        }, 500);
    }).off('click').on('click', function(){
        markerIndex = this.dataset.marker;
    });
    $g('#openstreetmap-editor-dialog .add-new-item i').attr('data-index', ind * 1 + 1);
}

function initOpenstreetmapMarker(ind)
{
    if (ind != 0) {
        var div = document.querySelector('#openstreetmap-choose-location').parentNode.cloneNode(true),
            input = div.querySelector('input');
        input.id = '';
        input.dataset.marker = ind;
        div.dataset.marker = ind;
        $g('#openstreetmap-editor-dialog .sorting-container').append(div);
    }
    setOpenstreetmapMarker(ind);
    setOpenstreetmapAutocomplete(ind);
}

function initOpenstreetmapMarkers()
{
    markerIndex = null;
    locationMarkers = {};
    $g('#openstreetmap-editor-dialog .sorting-item:not([data-marker="0"])').remove();
    for (var ind in app.edit.marker) {
        initOpenstreetmapMarker(ind)
    }
}

function initOpenstreetmapLocation()
{
    if (!app.openstreetmap && document.getElementById('openstreetmap-editor-dialog').modalShown) {
        return false;
    }
    if (openstreetmap) {
        openstreetmap.remove();
    }
    $g('#openstreetmap-location').empty();
    openstreetmap = L.map('openstreetmap-location').setView(app.edit.map.center, app.edit.map.zoom);
    L.tileLayer(app.edit.map.theme, {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(openstreetmap);
    L.control.scale({
        metric: true,
        imperial: false
    }).addTo(openstreetmap);
    initOpenstreetmapMarkers();
    openstreetmap.on('click', function(event){
        if (markerIndex) {
            app.edit.marker[markerIndex].position = [event.latlng.lat, event.latlng.lng];
            setOpenstreetmapMarker(markerIndex);
        }
    });
}

$g('.openstreetmap-theme-select').on('customAction', function(){
    app.edit.map.theme = this.querySelector('input[type="hidden"]').value;
    initOpenstreetmapLocation();
    app.addHistory();
});

$g('#openstreetmap-editor-dialog .sorting-container').on('click', 'i.zmdi-close', function(){
    app.itemDelete = $g(this).closest('.sorting-item').attr('data-marker');
    app.checkModule('deleteItem');
});

$g('#openstreetmap-editor-dialog .add-new-item i').on('click', function(){
    app.edit.marker[this.dataset.index] = {
        "place": "",
        "infobox": false,
        "description" : ""
    }
    initOpenstreetmapMarker(this.dataset.index);
    app.addHistory();
});

$g('#openstreetmap-editor-dialog .sorting-container').on('click', 'i.zmdi-edit', function(){
    var ind = $g(this).closest('.sorting-item').attr('data-marker'),
        modal = $g('#openstreetmap-item-dialog');
    $g('#apply-openstreetmap-marker').attr('data-index', ind);
    modal.find('input[data-option="infobox"]').prop('checked', app.edit.marker[ind].infobox);
    modal.find('textarea[data-option="description"]').val(app.edit.marker[ind].description);
    modal.modal();
});

$g('#apply-openstreetmap-marker').on('click', function(){
    var ind = this.dataset.index,
        modal = $g('#openstreetmap-item-dialog');
    app.edit.marker[ind].infobox = modal.find('input[data-option="infobox"]').prop('checked');
    app.edit.marker[ind].description = modal.find('textarea[data-option="description"]').val().trim();
    setOpenstreetmapMarker(ind);
    app.addHistory();
    modal.modal('hide');
});

$g('#openstreetmap-editor-dialog').on('hide', function(){
    this.modalShown = false;
    var center = openstreetmap.getCenter();
    app.edit.map.center = [center.lat, center.lng];
    app.edit.map.zoom = openstreetmap.getZoom();
    app.editor.app.initOpenstreetmap(app.edit, app.editor.app.edit);
});

app.modules.openstreetmapEditor = true;
app.openstreetmapEditor();