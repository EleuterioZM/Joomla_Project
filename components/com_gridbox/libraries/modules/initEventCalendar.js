/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.renderEventCalendar = function(key){
    let parent = $g('#'+key+' .event-calendar-wrapper'),
        obj = app.items[key],
        year = parent[0].dataset.year * 1,
        month = parent[0].dataset.month * 1,
        menuitem = parent[0].dataset.menuitem,
        array = [],
        category = tags = '';
    if (obj.categories) {
        for (let key in obj.categories) {
            array.push(key);
        }
    }
    category = array.join(',');
    array = [];
    if (obj.tags) {
        for (let key in obj.tags) {
            array.push(key);
        }
    }
    tags = array.join(',');
    if (menuitem) {
        menuitem *= 1;
    }
    if (!obj.fields) {
        obj.layout = "list";
        obj.fields = [];
        obj.info = ["author", "date", "category", "comments"];
        obj.desktop.view = {
            "author": false,
            "button": false,
            "category": true,
            "comments": false,
            "date": true,
            "image": true,
            "reviews": false,
            "title": true
        };
        obj.desktop.fields = {};
    }
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.renderEventCalendar",
        data: {
            year: year,
            month: month + 1,
            app: obj.app,
            category : category,
            tags: tags,
            type: obj.posts_type,
            start: obj.start,
            menuitem: menuitem
        },
        complete: function(msg){
            var data = JSON.parse(msg.responseText);
            parent[0].eventList = data.eventList;
            parent.find('.ba-event-calendar-title').text(data.title);
            parent.find('.ba-event-calendar-body').html(data.body);
            parent.find('.ba-event-calendar-header').html(data.header);
            parent.find('.ba-date-cell.ba-event-date').on('click', function(event){
                if (parent[0].eventList && parent[0].eventList[this.dataset.date]){
                    let events = parent[0].eventList[this.dataset.date],
                        rect = this.getBoundingClientRect(),
                        computed = getComputedStyle(document.body),
                        borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
                        borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
                        div = document.createElement('div'),
                        viewObject = app.getObject(key);
                    div.className = 'event-calendar-events-list ba-'+obj.layout+'-layout';
                    div.innerHTML = '<i class="ba-icons ba-icon-close close-event-calendar-list"></i>'+
                        '<div class="event-calendar-row-wrapper"></div>';
                    let wrapper = div.querySelector('.event-calendar-row-wrapper');
                    for (let i = 0; i < events.length; i++) {
                         let event = events[i],
                            row = document.createElement('div'),
                            url = event.intro_image ? event.intro_image : '',
                            content = document.createElement('div'),
                            str = '';
                        content.className = 'event-calendar-event-item-content';
                        if (viewObject.view.title) {
                            str += '<a href="'+event.url+'" class="event-calendar-event-item-title">'+event.title+'</a>';
                        }
                        if (viewObject.view.author || viewObject.view.date || viewObject.view.category || viewObject.view.comments) {
                            str += '<div class="event-calendar-event-item-info-wrapper">';
                            let info = {};
                            if (viewObject.view.author) {
                                info.author = event.authors;
                            }
                            if (viewObject.view.date) {
                                info.date = '<span class="event-calendar-event-item-date">'+event.created+'</span>';
                            }
                            if (viewObject.view.category) {
                                info.category = '<span><a href="'+event.catUrl+'" class="event-calendar-event-item-category">'+
                                    event.category+'</a></span>';
                            }
                            if (viewObject.view.comments) {
                                info.comments = event.comments;
                            }
                            for (let i = 0; i < obj.info.length; i++) {
                                if (obj.info[i] in info) {
                                    str += info[obj.info[i]];
                                }
                            }
                            str += '</div>';
                        }
                        if (viewObject.view.reviews) {
                            str += event.reviews;
                        }
                        if (obj.fields.length) {
                            let eventFields = document.createElement('div'),
                                fieldsDiv = document.createElement('div');
                            fieldsDiv.innerHTML = event.fields;
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
                                '<a class="ba-btn-transition" href="'+event.url+'">'+app._('READ_MORE')+'</a></div>';
                        }
                        content.innerHTML = str;
                        if (viewObject.view.image) {
                            row.innerHTML = '<div class="event-calendar-event-item-image-wrapper"><div><a href="'+event.url+
                                '" class="event-calendar-event-item-image" style="background-image: url('+
                                (app.isExternal(url) ? url : JUri+encodeURI(url))+');"></a><img src="'+
                                (app.isExternal(url) ? url : JUri+encodeURI(url))+'"></div></div>';
                        }
                        if (content.innerHTML) {
                            row.appendChild(content);
                        }
                        row.className = 'event-calendar-event-item';
                        wrapper.appendChild(row);
                    }
                    setTimeout(function(){
                        document.body.appendChild(div);
                        if (('buttonsPrevent' in app)) {
                            app.buttonsPrevent();
                        }
                        div.style.top = (rect.top + window.pageYOffset - div.offsetHeight - borderTopWidth - 10)+'px';
                        div.style.left = (rect.left - div.offsetWidth / 2 + rect.width / 2 - borderLeftWidth)+'px';
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
    });
}

app.initEventCalendar = function(obj, key){
    $g('#'+key+' .ba-event-calendar-title-wrapper i').off('click').on('click', function(){
        var parent = $g(this).closest('.event-calendar-wrapper')[0],
            year = parent.dataset.year * 1,
            month = parent.dataset.month * 1;
        if (this.dataset.action == 'next') {
            year = (month === 11) ? year + 1 : year;
            month = (month + 1) % 12;
        } else {
            year = (month === 0) ? year - 1 : year;
            month = (month === 0) ? 11 : month - 1;
        }
        parent.dataset.year = year;
        parent.dataset.month = month;
        app.renderEventCalendar(key);
    });
    app.renderEventCalendar(key);
    initItems();
}

if (app.modules.initEventCalendar) {
    app.initEventCalendar(app.modules.initEventCalendar.data, app.modules.initEventCalendar.selector);
}