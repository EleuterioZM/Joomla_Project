/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var mapScript = document.createElement('script'),
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

function setGoogleMapsPlacesZoom(map, zoom)
{
    let flag = false;
    for (let i = 0; i < map.markers.length; i++) {
        flag = map.getBounds().contains(map.markers[i].getPosition());
        if (!flag) {
            break;
        }
    }
    if (!flag && zoom >= 0) {
        map.setZoom(zoom - 1);
        setGoogleMapsPlacesZoom(map, zoom - 1);
    }
}

var gridboxMaps = {
    init: function(obj, key){
        if (obj.type == 'google-maps-places') {
            this.getEvents(obj, key);
        } else {
            this.createMap(obj, key);
        }
    },
    createMap: function(obj, key) {
        var map = new google.maps.Map($g('#'+key+' .ba-map-wrapper')[0], obj.map);
        map.setOptions({styles: mapStyles[obj.styleType]});
        if (typeof(obj.marker.infobox) == 'string') {
            var object = $g.extend(true, {}, obj.marker);
            obj.marker = {
                "0" : object
            };
        }
        if (obj.events && obj.events.length) {
            map.markers = [];
            google.maps.event.addListenerOnce(map, 'idle', function(){
                setGoogleMapsPlacesZoom(map, 14);
            });
        }
        for (var ind in obj.marker) {
            var object = obj.marker[ind];
            if (typeof(object.position) == 'object') {
                var markerObj = {
                        position : object.position,
                        map : map
                    },
                    marker;
                if (object.icon) {
                    markerObj.icon = (!app.isExternal(object.icon) ? JUri : '')+object.icon;
                }
                marker = new google.maps.Marker(markerObj);
                if (obj.events && obj.events.length) {
                    marker.page = object.page;
                    map.markers.push(marker);
                    marker.addListener('click', function(event){
                        let rect = {
                            top: 0,
                            left: 0
                        };
                        for (let ind in event) {
                            if (event[ind] && event[ind].clientX) {
                                rect = {
                                    left: event[ind].clientX,
                                    top: event[ind].clientY
                                };
                                break;
                            }
                        }
                        let computed = getComputedStyle(document.body),
                            borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
                            borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
                            div = document.createElement('div'),
                            row = document.createElement('div'),
                            url = this.page.intro_image ? this.page.intro_image : '',
                            content = document.createElement('div'),
                            viewObject = app.getObject(key),
                            str = '';
                        div.className = 'event-calendar-events-list ba-'+obj.layout+'-layout';
                        div.innerHTML = '<i class="zmdi zmdi-close close-event-calendar-list"></i>'+
                            '<div class="event-calendar-row-wrapper"></div>';
                        let wrapper = div.querySelector('.event-calendar-row-wrapper');
                        content.className = 'event-calendar-event-item-content';
                        if (viewObject.view.title) {
                            str += '<a href="'+this.page.url+'" class="event-calendar-event-item-title">'+this.page.title+'</a>';
                        }
                        if (viewObject.view.author || viewObject.view.date || viewObject.view.category || viewObject.view.comments) {
                            str += '<div class="event-calendar-event-item-info-wrapper">';
                            let info = {};
                            if (viewObject.view.author) {
                                info.author = this.page.authors;
                            }
                            if (viewObject.view.date) {
                                info.date = '<span class="event-calendar-event-item-date">'+this.page.created+'</span>';
                            }
                            if (viewObject.view.category) {
                                info.category = '<span><a href="'+this.page.catUrl+'" class="event-calendar-event-item-category">'+
                                    this.page.category+'</a></span>';
                            }
                            if (viewObject.view.comments) {
                                info.comments = this.page.comments;
                            }
                            for (let i = 0; i < obj.info.length; i++) {
                                if (obj.info[i] in info) {
                                    str += info[obj.info[i]];
                                }
                            }
                            str += '</div>';
                        }
                        if (viewObject.view.reviews) {
                            str += this.page.reviews;
                        }
                        if (obj.fields.length) {
                            let eventFields = document.createElement('div'),
                                fieldsDiv = document.createElement('div');
                            fieldsDiv.innerHTML = this.page.fields;
                            for (let i = 0; i < obj.fields.length; i++) {
                                let ind = obj.fields[i],
                                    fieldsRow = fieldsDiv.querySelector('.ba-blog-post-field-row[data-id="'+ind+'"]');
                                if (viewObject.fields[ind] && fieldsRow) {
                                    eventFields.appendChild(fieldsRow);
                                }
                            }
                            if (eventFields.querySelector('.ba-blog-post-field-row')) {
                                str += '<div class="event-calendar-event-item-fields-wrapper">';
                                str += eventFields.innerHTML;
                                str += '</div>';
                            }
                        }
                        if (viewObject.view.button) {
                            str += '<div class="event-calendar-event-item-button-wrapper">'+
                                '<a class="ba-btn-transition" href="'+this.page.url+'">'+gridboxLanguage['READ_MORE']+'</a></div>';
                        }
                        content.innerHTML = str;
                        if (viewObject.view.image) {
                            row.innerHTML = '<div class="event-calendar-event-item-image-wrapper"><div><a href="'+this.page.url+
                                '" class="event-calendar-event-item-image" style="background-image: url('+
                                (app.isExternal(url) ? url : JUri+encodeURI(url))+');"></a><img src="'+
                                (app.isExternal(url) ? url : JUri+encodeURI(url))+'"></div></div>';
                        }
                        if (content.innerHTML) {
                            row.appendChild(content);
                        }
                        row.className = 'event-calendar-event-item';
                        wrapper.appendChild(row);
                        if (row.innerHTML) {
                            setTimeout(function(){
                                document.body.appendChild(div);
                                if (('buttonsPrevent' in app)) {
                                    app.buttonsPrevent();
                                }
                                div.style.top = (rect.top + window.pageYOffset - div.offsetHeight - borderTopWidth - 10)+'px';
                                div.style.left = (rect.left - div.offsetWidth / 2 - borderLeftWidth)+'px';
                                div.style.setProperty('--event-calendar-list-height', div.offsetHeight+'px');
                                $g('body, .close-event-calendar-list').one('mousedown', function(){
                                    $g('.event-calendar-events-list').remove();
                                });
                            }, 100);
                            div.addEventListener('mousedown', function(event){
                                event.stopPropagation();
                            });
                        }
                    });
                }
                if (object.description) {
                    marker.infoWindow = new google.maps.InfoWindow({
                        content : object.description
                    });
                    if (object.infobox == 1) {
                        marker.infoWindow.open(map, marker);
                    }
                    marker.addListener('click', function(event){
                        this.infoWindow.open(map, this);
                    });
                }
            }
        }
    },
    getEvents: function(obj, key){
        let wrapper = document.querySelector('#'+key+' .ba-map-wrapper'),
            menuitem = wrapper.dataset.menuitem;
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.getMapsPlaces",
            data: {
                app: obj.app,
                pages: wrapper.dataset.pages,
                menuitem: menuitem
            },
            complete: function(msg){
                obj.events = JSON.parse(msg.responseText);
                obj.marker = {};
                if (obj.events && obj.events.length) {
                    let center = {
                        lat: 0,
                        lng: 0
                    },
                    count = 0;
                    for (let i = 0; i < obj.events.length; i++) {
                        count++;
                        center.lat += obj.events[i].map.marker.position.lat;
                        center.lng += obj.events[i].map.marker.position.lng;
                        obj.marker[i] = {
                            description: '',
                            icon: '',
                            infobox: '0',
                            page: obj.events[i],
                            position: obj.events[i].map.marker.position
                        }
                    }
                    center.lat = center.lat / count;
                    center.lng = center.lng / count;
                    obj.map.center = center;
                    obj.map.zoom = 14;
                }
                gridboxMaps.createMap(obj, key);
            }
        })
    }
}

app.initmap = function(obj, key){
    if (typeof(google) == 'object') {
        gridboxMaps.init(obj, key);
    }
    initItems();
}

if (themeData.page.view == 'gridbox') {
    mapScript.onload = function(){
        if (app.modules.initmap) {
            app.initmap(app.modules.initmap.data, app.modules.initmap.selector);
        }
    }
    mapScript.src = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='+top.integrations.google_maps.key;
    document.head.appendChild(mapScript);
} else if (app.modules.initmap) {
    app.initmap(app.modules.initmap.data, app.modules.initmap.selector);
}