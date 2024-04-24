/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!app.mapScript) {
    app.mapScript = document.createElement('script');
    app.mapScript.onload = function(){
        app.mapEditor();
    }
    app.mapScript.src = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='+integrations.google_maps.key;
    document.head.appendChild(app.mapScript);
}

var locationMap,
    locationMarkers = {},
    markerIndex = null,
    autocompleteClone = document.getElementById('choose-location').cloneNode(),
    mapStyles = {
        'standart' : [],
        'silver' : [
            {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }]
            },
            {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#616161"
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#f5f5f5"
                }]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#bdbdbd"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#eeeeee"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#e5e5e5"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#9e9e9e"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dadada"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#616161"
                }]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#9e9e9e"
                }]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#e5e5e5"
                }]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#eeeeee"
                }]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#c9c9c9"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#9e9e9e"
                }]
            }
        ],
        'retro' : [
            {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ebe3cd"
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#523735"
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#f5f1e6"
                }]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#c9b2a6"
                }]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#dcd2be"
                }]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ae9e90"
                }]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dfd2ae"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dfd2ae"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#93817c"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#a5b076"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#447530"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f1e6"
                }]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#fdfcf8"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f8c967"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#e9bc62"
                }]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#e98d58"
                }]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#db8555"
                }]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#806b63"
                }]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dfd2ae"
                }]
            },
            {
                "featureType": "transit.line",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#8f7d77"
                }]
            },
            {
                "featureType": "transit.line",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#ebe3cd"
                }]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dfd2ae"
                }]
            },
            {
                "featureType": "water",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#b9d3c2"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#92998d"
                }]
            }
        ],
        'dark' : [
            {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#212121"
                }]
            },
            {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#212121"
                }]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "featureType": "administrative.country",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#9e9e9e"
                }]
            },
            {
                "featureType": "administrative.land_parcel",
                "stylers": [{
                    "visibility": "off"
                }]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#bdbdbd"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#181818"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#616161"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1b1b1b"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#2c2c2c"
                }]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#8a8a8a"
                }]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#373737"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#3c3c3c"
                }]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#4e4e4e"
                }]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#616161"
                }]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#757575"
                }]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#3d3d3d"
                }]
            }
        ],
        'night' : [
            {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#242f3e"
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#746855"
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#242f3e"
                }]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#d59563"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#d59563"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#263c3f"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#6b9a76"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#38414e"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#212a37"
                }]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#9ca5b3"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#746855"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#1f2835"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#f3d19c"
                }]
            },
            {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#2f3948"
                }]
            },
            {
                "featureType": "transit.station",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#d59563"
                }]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#17263c"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#515c6d"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#17263c"
                }]
            }
        ],
        'aubergine' : [
            {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#1d2c4d"
                }]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#8ec3b9"
                }]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1a3646"
                }]
            },
            {
                "featureType": "administrative.country",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#4b6878"
                }]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#64779e"
                }]
            },
            {
                "featureType": "administrative.province",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#4b6878"
                }]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#334e87"
                }]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#023e58"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#283d6a"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#6f9ba5"
                }]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1d2c4d"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#023e58"
                }]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#3C7680"
                }]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#304a7d"
                }]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#98a5be"
                }]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1d2c4d"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#2c6675"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#255763"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#b0d5ce"
                }]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#023e58"
                }]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#98a5be"
                }]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1d2c4d"
                }]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#283d6a"
                }]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#3a4762"
                }]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#0e1626"
                }]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#4e6d70"
                }]
            }
        ]
    };

app.mapEditor = function() {
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#map-editor-dialog');
    value = app.edit.type == 'map' ? gridboxLanguage['GOOGLE_MAP'] : gridboxLanguage['FIELD_GOOGLE_MAPS'];
    if (app.edit.type == 'google-maps-places') {
        value = gridboxLanguage['GOOGLE_MAPS_PLACES'];
    }
    $g('#map-editor-dialog .ba-dialog-title').text(value);
    setDisableState('#map-editor-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    if (app.edit.type == 'field-google-maps') {
        $g('#map-editor-dialog .google-maps-location-options').hide().prev().hide();
        $g('#map-editor-dialog .google-maps-places-options').hide();
        $g('#map-editor-dialog .field-google-maps-options').css('display', '');
        $g('#map-editor-dialog input[data-option="label"]').val(app.edit.label);
        $g('#map-editor-dialog input[data-option="description"][data-group="options"]').val(app.edit.options.description);
    } else if (app.edit.type == 'google-maps-places') {
        $g('#map-editor-dialog .google-maps-location-options').hide().prev().hide();
        $g('#map-editor-dialog .field-google-maps-options').hide();
        $g('#map-editor-dialog .google-maps-places-options').css('display', '');
        $g('#map-editor-dialog .google-maps-places-app-select input[type="hidden"]').val(app.edit.app);
        value = $g('#map-editor-dialog .google-maps-places-app-select li[data-value="'+app.edit.app+'"]').text().trim();
        $g('#map-editor-dialog .google-maps-places-app-select input[readonly]').val(value);
        $g('#map-editor-dialog input[data-group="view"][type="checkbox"]').each(function(){
            if (this.dataset.option in app.edit.desktop.view) {
                value = app.getValue('view', this.dataset.option);
                this.checked = value;
            }
        });
        $g('#map-editor-dialog .event-calendar-layout-select input[type="hidden"]').val(app.edit.layout);
        value = $g('#map-editor-dialog .event-calendar-layout-select li[data-value="'+app.edit.layout+'"]').text().trim();
        $g('#map-editor-dialog .event-calendar-layout-select input[readonly]').val(value);
    } else {
        $g('#map-editor-dialog .google-maps-location-options').css('display', '').prev().css('display', '');
        $g('#map-editor-dialog .field-google-maps-options').hide();
        $g('#map-editor-dialog .google-maps-places-options').hide();
    }
    if (app.edit.controls == 1) {
        $g('#map-editor-dialog input[data-option="controls"]').prop('checked', true);
    } else {
        $g('#map-editor-dialog input[data-option="controls"]').prop('checked', false);
    }
    if (app.edit.scrollwheel == 1) {
        $g('#map-editor-dialog input[data-option="scrollwheel"]').prop('checked', true);
    } else {
        $g('#map-editor-dialog input[data-option="scrollwheel"]').prop('checked', false);
    }
    if (app.edit.draggable == 1) {
        $g('#map-editor-dialog input[data-option="draggable"]').prop('checked', true);
    } else {
        $g('#map-editor-dialog input[data-option="draggable"]').prop('checked', false);
    }
    value = app.getValue('height');
    app.setLinearInput(modal.find('input[data-option="height"]'), value);
    $g('.map-theme-select input[type="hidden"]').val(app.edit.styleType);
    value = $g('.map-theme-select li[data-value="'+app.edit.styleType+'"]').text();
    $g('.map-theme-select input[readonly]').val(value.trim());
    app.setDefaultState('#map-editor-dialog .margin-settings-group', 'default');
    app.setMarginValues('#map-editor-dialog .margin-settings-group');
    app.setDefaultState('#map-editor-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#map-editor-dialog .shadow-settings-group');
    setTimeout(function(){
        if (typeof(google) == 'object') {
            modal.modal();
        }
    }, 150);
}

$g('#map-editor-dialog').on('shown', function(){
    initChooseLocation();
}).on('hide', function(){
    app.editor.app.initmap(app.edit, app.editor.app.edit);
});

$g('#map-editor-dialog .sorting-container').on('click', 'i.zmdi-edit', function(){
    var ind = $g(this).closest('.sorting-item').attr('data-marker');
    $g('#apply-marker-info').removeClass('active-button').addClass('disable-button').attr('data-index', ind);
    $g('#map-item-dialog .ba-group-element').find('input, textarea').each(function(){
        if (this.type == 'checkbox') {
            this.checked = Boolean(app.edit.marker[ind][this.dataset.option] * 1);
        } else {
            this.value = app.edit.marker[ind][this.dataset.option];
        }
    });
    $g('#map-item-dialog').modal();
});

$g('#map-editor-dialog .sorting-container').on('click', 'i.zmdi-close', function(){
    app.itemDelete = $g(this).closest('.sorting-item').attr('data-marker');
    app.checkModule('deleteItem');
});

$g('#map-item-dialog input[data-option="icon"]').on('mousedown', function(){
    fontBtn = this;
    uploadMode = 'selectMarker';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('#map-item-dialog .ba-group-element').find('input, textarea').on('input change', function(){
    $g('#apply-marker-info').addClass('active-button').removeClass('disable-button');
});

$g('#map-item-dialog .reset i').on('click', function(){
    $g('#map-item-dialog input[data-option="icon"]').val('').trigger('change');
});

$g('#apply-marker-info').on('click', function(){
    if (this.classList.contains('active-button')) {
        var ind = this.dataset.index;
        $g('#map-item-dialog .ba-group-element').find('input, textarea').each(function(){
            if (this.type == 'checkbox') {
                app.edit.marker[ind][this.dataset.option] = Number(this.checked);
            } else {
                app.edit.marker[ind][this.dataset.option] = this.value;
            }
        });
        setMarker();
        app.addHistory();
        $g('#map-item-dialog').modal('hide');
    }
});

$g('#map-editor-dialog').find('[data-option="controls"], [data-option="scrollwheel"], [data-option="draggable"]').on('change', function(){
    var val = 0,
        option = this.dataset.option;
    if (this.checked) {
        val = 1;
    }
    var flag = Boolean(val);
    app.edit[option] = val;
    switch (option) {
        case 'controls' :
            app.edit.map["zoomControl"] = flag;
            app.edit.map["navigationControl"] = flag;
            app.edit.map["mapTypeControl"] = flag;
            app.edit.map["scaleControl"] = flag;
            app.edit.map["disableDefaultUI"] = flag ? false : true;
            app.edit.map["disableDoubleClickZoom"] = flag ? false : true;
            break;
        default :
            app.edit.map[option] = flag;
    }
    app.addHistory();
});

$g('.map-theme-select').on('customAction', function(){
    var style = $g(this).find('input[type="hidden"]').val();
    app.edit.styleType = style;
    initChooseLocation();
    app.addHistory();
});

function initChooseLocation()
{
    if (typeof(google) != 'object') {
        return false;
    }
    var clone = autocompleteClone.cloneNode(),
        input = document.getElementById('choose-location');
    input.parentNode.insertBefore(clone, input);
    input.parentNode.removeChild(input);
    var obj = {
            "scrollwheel": true,
            "navigationControl": true,
            "mapTypeControl": true,
            "scaleControl": true,
            "draggable": true,
            "zoomControl": true,
            "disableDefaultUI": false,
            "disableDoubleClickZoom": false
        },
        options = $g.extend({}, app.edit.map, obj);
    locationMap = new google.maps.Map(document.getElementById('map-location'), options);
    locationMap.setOptions({styles: mapStyles[app.edit.styleType]});
    markerIndex = null
    locationMarkers = {};
    setMarker();
    $g('#map-editor-dialog .sorting-item:not([data-marker="0"])').remove();
    for (var ind in app.edit.marker) {
        if (ind != 0) {
            var div = document.querySelector('#choose-location').parentNode.cloneNode(true),
                input = div.querySelector('input');
            input.id = '';
            input.dataset.marker = ind;
            div.dataset.marker = ind;
            $g('#map-editor-dialog .sorting-container').append(div);
        }
        setAutocomplete(ind);
    }
    locationMap.addListener('maptypeid_changed',function(event){
        setCenter(locationMap);
    });
    locationMap.addListener('idle',function(event){
        setCenter(locationMap);
        if (app.edit.events && app.edit.events.length) {
            app.editor.setGoogleMapsPlacesZoom(locationMap, 14);
        }
    });
    locationMap.addListener('click', function(event) {
        if (markerIndex) {
            if (locationMarkers[markerIndex]) {
                locationMarkers[markerIndex].marker.setMap(null);
            } else {
                locationMarkers[markerIndex] = {
                    marker: null
                };
            }
            var obj = {
                position: event.latLng,
                map: locationMap
            }
            if (app.edit.marker[markerIndex].icon) {
                obj.icon = JUri+app.edit.marker[markerIndex].icon;
            }
            if (!app.edit.marker[markerIndex].position) {
                app.edit.marker[markerIndex].position = {}
            }
            app.edit.marker[markerIndex].position.lat = event.latLng.lat();
            app.edit.marker[markerIndex].position.lng = event.latLng.lng();
            locationMarkers[markerIndex].marker = new google.maps.Marker(obj);
            if (app.edit.marker[markerIndex].description) {
                locationMarkers[markerIndex].marker.infoWindow = new google.maps.InfoWindow({
                    content : app.edit.marker[markerIndex].description
                });
                if (app.edit.marker[markerIndex].infobox == 1) {
                    locationMarkers[markerIndex].marker.infoWindow.open(locationMap, locationMarkers[markerIndex].marker);
                }
                locationMarkers[markerIndex].marker.addListener('click', function(event){
                    this.infoWindow.open(locationMap, this);
                });
            }
        }
    });
}

function setMarker()
{
    for (var ind in app.edit.marker) {
        var object = app.edit.marker[ind];
        if (typeof(object.position) == 'object') {
            var obj = {
                position : object.position,
                map : locationMap
            }
            if (object.icon) {
                obj.icon = JUri+object.icon;
            }
            if (locationMarkers[ind]) {
                locationMarkers[ind].marker.setMap(null);
            } else {
                locationMarkers[ind] = {
                    marker: null
                };
            }
            locationMarkers[ind].marker = new google.maps.Marker(obj);
            if (!locationMap.markers) {
                locationMap.markers = [];
            }
            locationMap.markers.push(locationMarkers[ind].marker);
            if (object.description) {
                if (locationMarkers[ind].marker.infoWindow) {
                    locationMarkers[ind].marker.infoWindow.close();
                }
                locationMarkers[ind].marker.infoWindow = new google.maps.InfoWindow({
                    content : object.description
                });
                if (object.infobox == 1) {
                    locationMarkers[ind].marker.infoWindow.open(locationMap, locationMarkers[ind].marker);
                }
                locationMarkers[ind].marker.addListener('click', function(event){
                    this.infoWindow.open(locationMap, this);
                });
            }
        }
    }
}

function setCenter(map)
{
    var center = map.getCenter();
    app.edit.map.center.lat = center.lat();
    app.edit.map.center.lng = center.lng();
    app.edit.map.zoom = map.getZoom();
    app.edit.map.mapTypeId = map.getMapTypeId();
}

function setAutocomplete(ind)
{
    if (!app.edit.marker[ind]) {
        app.edit.marker[ind] = {
            "description" : "",
            "infobox" : "0",
            "icon" : ""
        }
    }
    var locationInput = document.querySelector('.choose-location-input[data-marker="'+ind+'"]'),
        autocomplete = new google.maps.places.Autocomplete(locationInput);
    locationInput.value = '';
    if (app.edit.marker[ind].place) {
        locationInput.value = app.edit.marker[ind].place;
    }
    $g(locationInput).on('input', function(){
        $g('.pac-container').on('mousedown', function(event){
            event.stopPropagation();
        });
    });
    autocomplete.addListener('place_changed', function(){
        var place = autocomplete.getPlace();
        app.edit.marker[ind].place = locationInput.value;
        if (place.geometry.viewport) {
            locationMap.fitBounds(place.geometry.viewport);
        } else {
            locationMap.setCenter(place.geometry.location);
        }
    });
    locationInput.addEventListener('click', function(){
        markerIndex = this.dataset.marker;
    });
    $g('#map-editor-dialog .add-new-item i').attr('data-index', ind * 1 + 1);
}

$g('#map-editor-dialog .add-new-item i').on('click', function(){
    var div = document.querySelector('#choose-location').parentNode.cloneNode(true),
        input = div.querySelector('input');
    input.id = '';
    input.value = '';
    input.dataset.marker = this.dataset.index;
    div.dataset.marker = this.dataset.index;
    $g('#map-editor-dialog .sorting-container').append(div);
    setAutocomplete(this.dataset.index);
});

$g('#map-editor-dialog .google-maps-places-app-select').on('customAction', function(){
    let value = this.querySelector('input[type="hidden"]').value;
    if (value != app.edit.app) {
        app.edit.app = value;
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.getMapsPlaces",
            data: {
                app: value
            },
            complete: function(msg){
                app.edit.events = JSON.parse(msg.responseText);
                app.edit.marker = {};
                if (app.edit.events && app.edit.events.length) {
                    let center = {
                        lat: 0,
                        lng: 0
                    },
                    count = 0;
                    for (let i = 0; i < app.edit.events.length; i++) {
                        count++;
                        center.lat += app.edit.events[i].map.marker.position.lat;
                        center.lng += app.edit.events[i].map.marker.position.lng;
                        app.edit.marker[i] = {
                            description: '',
                            icon: '',
                            infobox: '0',
                            page: app.edit.events[i],
                            position: app.edit.events[i].map.marker.position
                        }
                    }
                    center.lat = center.lat / count;
                    center.lng = center.lng / count;
                    app.edit.map.center = center;
                    app.edit.map.zoom = 14;
                }
                initChooseLocation();
            }
        });
    }
});

app.modules.mapEditor = true;
app.mapEditor();