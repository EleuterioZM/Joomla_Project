/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function showNotice(message, className)
{
    if (!className) {
        className = '';
    }
    if (notification.hasClass('notification-in')) {
        setTimeout(function(){
            notification.removeClass('notification-in').addClass('animation-out');
            setTimeout(function(){
                addNoticeText(message, className);
            }, 400);
        }, 2000);
    } else {
        addNoticeText(message, className);
    }
}

function formsRecaptchaOnload()
{
    app.grecaptcha.loaded = true;
    app.grecaptcha.captcha = grecaptcha;
    delete window.hcaptcha;
    delete window.grecaptcha;
    app.grecaptcha.init();
}

function addNoticeText(message, className)
{
    var time = 3000;
    if (className) {
        time = 6000;
    }
    notification.find('p').html(message);
    notification.addClass(className).removeClass('animation-out').addClass('notification-in');
    setTimeout(function(){
        notification.removeClass('notification-in').addClass('animation-out');
        setTimeout(function(){
            notification.removeClass(className);
        }, 400);
    }, time);
}

function prepareColumns(data)
{
    let content = templates.row.content.cloneNode(true),
        columns = data.split('+'),
        span = 12 / columns.length,
        wrapper = content.querySelector('.ba-form-column-wrapper'),
        column = content.querySelector('.ba-form-column'),
        resizer = templates.resizer.content,
        handle = '> .ba-form-field-item > .ba-edit-item .edit-settings',
        selector = '> .ba-form-field-item';
    for (let i = 1; i < columns.length; i++) {
        wrapper.appendChild(resizer.cloneNode(true));
        wrapper.appendChild(column.cloneNode(true));
    }
    setSortable($f(wrapper).find('.ba-form-column'), 'items', handle, selector);
    $f(wrapper).find('.ba-form-column').each(function(){
        this.id = 'bacolumn-'+(++columnNumber);
        this.dataset.span = span;
        $f(this).removeClass('span12').addClass('span'+span).find('.column-info').each(function(){
            this.textContent = this.textContent.trim().replace(/\d+/, span);
        });
    });

    return content;
}

function getSpanWidth(i, style, rowWidth)
{
    switch(i) {
        case 1 : return Math.floor(rowWidth * 6 / 100 - 2);
            break;
        case 2 : return Math.floor(rowWidth * 14.5 / 100 - 2);
            break;
        case 3 : return Math.floor(rowWidth * 23 / 100 - 2);
            break;
        case 4 : return Math.floor(rowWidth * 31 / 100 - 2);
            break;
        case 5 : return Math.floor(rowWidth * 40 / 100 - 2);
            break;
        case 6 : return Math.floor(rowWidth * 48 / 100 - 2);
            break;
        case 7 : return Math.floor(rowWidth * 57 / 100 - 2);
            break;
        case 8 : return Math.floor(rowWidth * 65 / 100 - 2);
            break;
        case 9 : return Math.floor(rowWidth * 74 / 100 - 2);
            break;
        case 10 : return Math.floor(rowWidth * 82.6 / 100 - 2);
            break;
        case 11 : return Math.floor(rowWidth * 91.1 / 100 - 2);
            break;
    }
}

function getSpan(i)
{
    if (i < 14.8) {
        if (i - 6 < 14.8 - i) {
            return 1;
        } else {
            return 2;
        }
    } else if (i >= 14.8 && i <= 23.4) {
        if (i - 14.8 < 23.4 - i) {
            return 2;
        } else {
            return 3;
        }
    } else if (i >= 23.4 && i <= 31.91) {
        if (i - 23.4 < 31.91 - i) {
            return 3;
        } else {
            return 4;
        }
    } else if (i >= 31.91 && i <= 40.42) {
        if (i - 31.91 < 40.42 - i) {
            return 4;
        } else {
            return 5;
        }
    } else if (i >= 40.42 && i <= 48.93) {
        if (i - 40.42 < 48.93 - i) {
            return 5;
        } else {
            return 6;
        }
    } else if (i >= 48.93 && i <= 57.44) {
        if (i - 48.93 < 57.44 - i) {
            return 6;
        } else {
            return 7;
        }
    } else if (i >= 57.44 && i <= 65.95) {
        if (i - 57.44 < 65.95 - i) {
            return 7;
        } else {
            return 8;
        }
    } else if (i >= 65.95 && i <= 74.46) {
        if (i - 65.95 < 74.46 - i) {
            return 8;
        } else {
            return 9;
        }
    } else if (i >= 74.46 && i <= 82.9) {
        return 10;
    } else {
        return 11;
    }
}

function columnResizer(event)
{
    var leftEl = $f(this).prev(),
        rightEl = $f(this).next(),
        style = getComputedStyle(rightEl[0]),
        lstyle = getComputedStyle(leftEl[0]),
        padding = style.paddingRight.replace('px', '') * 1 + style.paddingLeft.replace('px', '') * 1,
        lpadding = lstyle.paddingRight.replace('px', '') * 1 + lstyle.paddingLeft.replace('px', '') * 1,
        leftSpan = leftEl[0].dataset.span,
        rightSpan = rightEl[0].dataset.span,
        maxSpan = leftSpan * 1 + rightSpan * 1 - 1,
        rowWidth = leftEl.parent().width(),
        startX = event.pageX,
        rightWidth = getSpanWidth(rightSpan * 1, style, rowWidth),
        leftWidth = getSpanWidth(leftSpan * 1, style, rowWidth),
        minResize = getSpanWidth(1, style, rowWidth),
        maxResize = getSpanWidth(maxSpan, style, rowWidth);
    leftEl.addClass('ba-column-resize');
    rightEl.addClass('ba-column-resize');
    padding += style.borderLeftWidth.replace('px', '') * 1 + style.borderRightWidth.replace('px', '') * 1;
    lpadding += lstyle.borderLeftWidth.replace('px', '') * 1 + lstyle.borderRightWidth.replace('px', '') * 1;
    $f(document).on('mousemove.resize', function(event){
        if (startX > event.pageX) {
            rightWidth = rightWidth + (startX - event.pageX);
            leftWidth = leftWidth - (startX - event.pageX);
        } else {
            rightWidth = rightWidth - (event.pageX - startX);
            leftWidth = leftWidth + (event.pageX - startX);
        }
        if (rightWidth < minResize || leftWidth > maxResize) {
            rightWidth = minResize;
            leftWidth = maxResize;
        }
        if (leftWidth < minResize || rightWidth > maxResize) {
            rightWidth = maxResize;
            leftWidth = minResize;
        }
        rightEl.width(rightWidth - padding);
        leftEl.width(leftWidth - lpadding);
        var percent = rightWidth * 100 / rowWidth,
            span;
        percent = Math.round(percent * 100) / 100;
        span = getSpan(percent);
        rightEl.find('.column-info').text('Span '+span);
        rightEl.removeClass('span'+rightSpan);
        rightSpan = span;
        rightEl.addClass('span'+span).attr('data-span', span);
        span = maxSpan - span + 1;
        leftEl.find('.column-info').text('Span '+span);
        leftEl.removeClass('span'+leftSpan);
        leftSpan = span;
        leftEl.addClass('span'+span).attr('data-span', span);
        startX = event.pageX;
    }).on('mouseup.resize contextmenu.resize', function(event){
        $f(document).off('mouseup.resize contextmenu.resize mousemove.resize');
        rightEl[0].style.width = '';
        leftEl[0].style.width = '';
        leftEl.removeClass('ba-column-resize');
        rightEl.removeClass('ba-column-resize');
        app.updateSignatures();
    });
    return false;
}

function pageResizer(event)
{
    var page = $f(this).closest('.ba-form-page'),
        info = page.find('> .page-info').addClass('visible-info '+this.dataset.position),
        direction = this.dataset.position == 'right' ? 1 : -1,
        width = page.outerWidth(),
        value = 0,
        parent = page.parent(),
        maxWidth = parent.width(),
        minWidth = maxWidth * 0.25,
        startX = event.pageX;
    document.body.classList.add('page-resize-started');
    $f('input[data-option="fullwidth"]').prop('checked', false);
    app.design.form.width.fullwidth = false;
    app.setDesignCssVariable('form', 'width', 'fullwidth', app.design, document.body);
    $f(document).on('mousemove.resize', function(event){
        if (startX > event.pageX) {
            width = width - ((startX - event.pageX) * direction * 2);
        } else {
            width = width + ((event.pageX - startX) * direction * 2);
        }
        if (width > maxWidth) {
            width = maxWidth;
        } else if (width < minWidth) {
            width = minWidth;
        }
        value = width;
        if (app.design.form.units['width-value'] == '%') {
            value = width * 100 / maxWidth;
            value = Math.round(value * 100) / 100;
        }
        $f('input[type="number"][data-subgroup="width"][data-option="value"]').val(value);
        app.design.form.width.value = value;
        app.setDesignCssVariable('form', 'width', 'value', app.design, document.body);
        info.text(value+app.design.form.units['width-value']);
        startX = event.pageX;
    }).on('mouseup.resize contextmenu.resize', function(event){
        $f(document).off('mouseup.resize contextmenu.resize mousemove.resize');
        info.removeClass('visible-info left right');
        document.body.classList.remove('page-resize-started');
        app.updateSignatures();
    });
    return false;
}

function setMinicolorsColor(value)
{
    var rgba = value ? value : 'rgba(255,255,255,0)',
        color = rgba2hex(rgba);
    var obj = {
        color : color[0],
        opacity : color[1],
        update: false
    }
    $f('.variables-color-picker').minicolors('value', obj).closest('#color-picker-cell')
        .find('.minicolors-opacity').val(color[1]);
    $f('#color-variables-dialog .active').removeClass('active');
    $f('#color-picker-cell, #color-variables-dialog .nav-tabs li:first-child').addClass('active');
}

function inputColor()
{
    var value = this.value.trim().toLowerCase(),
        parts = value.match(/[^#]\w/g),
        opacity = 1;
    if (parts && parts.length == 3) {
        var rgba = 'rgba(';
        for (var i = 0; i < 3; i++) {
            rgba += parseInt(parts[i], 16);
            rgba += ', ';
        }
        if (!this.dataset.rgba) {
            rgba += '1)';
        } else {
            parts = this.dataset.rgba.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
            if (!parts) {
                rgba += '1)';
            } else {
                opacity = parts[4];
                rgba += parts[4]+')';
            }
        }
        this.dataset.rgba = rgba;
        $f(this).next().find('.minicolors-swatch-color').css('background-color', rgba);
        $f(this).trigger('minicolorsInput');
        setMinicolorsColor(rgba);
    }
    $f(this).closest('.ba-settings-item').find('.minicolors-opacity').val(opacity).removeAttr('readonly');
}

function updateInput(input, rgba)
{
    var color = rgba2hex(rgba);
    input.attr('data-rgba', rgba).val(color[0]).next().find('.minicolors-swatch-color').css('background-color', rgba);
    input.closest('.minicolors').next().find('.minicolors-opacity').val(color[1]);
}

function rgba2hex(rgb)
{
    var parts = rgb.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
        hex = '#',
        part,
        color = [];
    if (parts) {
        for (var i = 1; i <= 3; i++) {
            part = parseInt(parts[i]).toString(16);
            if (part.length < 2) {
                part = '0'+part;
            }
            hex += part;
        }
        if (!parts[4]) {
            parts[4] = 1;
        }
        color.push(hex);
        color.push(parts[4] * 1);
        
        return color;
    } else {
        color.push(rgb.trim());
        color.push(1);
        
        return color;
    }
}

function returnPointLibraryItem(event)
{
    var pageY = event.clientY,
        pageX = event.clientX,
        item = rect = style = null,
        marginTop = marginBottom = 0,
        columns = [].slice.call(document.querySelectorAll('.ba-form-column-wrapper > .ba-form-column'));
    columns = columns.reverse();
    for (var i = 0; i < columns.length; i ++) {
        $f(columns[i]).find(' > .ba-form-field-item').each(function(){
            rect = this.getBoundingClientRect();
            style = getComputedStyle(this);
            marginTop = style.marginTop.replace(/px|%/, '') * 1;
            marginBottom = style.marginBottom.replace(/px|%/, '') * 1;
            if (rect.top - marginTop < event.clientY && rect.bottom + marginBottom > event.clientY &&
                rect.left < event.clientX && event.clientX < rect.right) {
                item = this;
                return false;
            }
        });
        if (!item) {
            rect = columns[i].getBoundingClientRect();
            if (rect.top < event.clientY && rect.bottom > event.clientY &&
                rect.left < event.clientX && event.clientX < rect.right) {
                let childs = $f(columns[i]).find(' > .ba-form-field-item');
                if (childs.length) {
                    item = childs.get(childs.length - 1);
                } else {
                    item = columns[i];
                }
                break;
            }
        } else {
            break;
        }
    }
    
    return item;
}

function checkIframe(modal, view, callback)
{
    var iframe = modal.find('iframe');
    if (iframe.attr('src').indexOf('view='+view) == -1) {
        iframe[0].src = 'index.php?option=com_baforms&view='+view+'&tmpl=component';
        iframe[0].onload = function(){
            modal.modal();
            if (typeof(callback) != 'undefined') {
                callback();
            }
        }
    } else {
        modal.modal();
        if (typeof(callback) != 'undefined') {
            callback();
        }
    }
}

function setSortable(item, group, handle, selector, change)
{
    if (!change) {
        change = function(){}
    }
    item.sortable({
        handle : handle,
        selector : selector,
        group: group,
        change: change
    })
}

function prepareFieldsStyle(obj, content, type, id)
{
    let wrapper = null;
    switch (type) {
        case 'image':
            wrapper = content.querySelector('.ba-image-wrapper');
            app.setDesignCssVariable('', '', 'width', obj, wrapper, 'image-field');
            app.setDesignCssVariable('', '', 'align', obj, wrapper, 'image-field');
            break;
        case 'map':
            wrapper = content.querySelector('.ba-map-wrapper');
            app.setDesignCssVariable('', '', 'height', obj, wrapper, 'map-field');
            break;
        case 'poll':
            for (let ind in obj.items) {
                obj.items[ind].color = app.design.theme.color;
            }
        case 'checkbox':
        case 'radio':
            wrapper = content.querySelector('.ba-form-checkbox-group-wrapper');
            app.setDesignCssVariable('', '', 'count', obj, wrapper, 'checkbox-field');
            $f(wrapper).find('input').attr('name', id);
            break;
        case 'rating':
            $f(content).find('input').attr('name', id);
            break
        case 'submit':
            obj.background.color = app.design.theme.color;
            obj.border.radius = app.design.field.border.radius;
            app.setSubmitDesign(obj, content.querySelector('.ba-form-submit-wrapper'));
            break;
        case 'headline':
            wrapper = content.querySelector('.ba-input-wrapper');
            for (let option in obj.label.typography) {
                app.setDesignCssVariable('label', 'typography', option, obj, wrapper, 'headline');
            }
            break;
    }
}

function prepareFields(key, field)
{
    let content = templates[key].content.cloneNode(true),
        id = 'baform-'+(++fieldNumber);
    content.querySelector('.ba-form-field-item').id = id;
    if (field) {
        let options = JSON.parse(field.options);
        app.items[id] = options;
        if (field.type == 'image') {
            content.querySelector('img').src = JUri+options.src;
        }
    } else {
        app.items[id] = $f.extend(true, {}, formOptions[key]);
        if ('title' in app.items[id] && key != 'acceptance') {
            app.items[id].title = app._('ENTER_FIELD_TITLE');
            content.querySelector('.ba-input-label-wrapper').textContent = app._('ENTER_FIELD_TITLE');
        }
        if ('confirm' in app.items[id]) {
            app.items[id].confirm.title = app._('CONFIRM_EMAIL');
            app.items[id]['confirm-password'].title = app._('CONFIRM_PASSWORD');
        }
    }
    prepareFieldsStyle(app.items[id], content, key, id);

    return content
}

var pageNumber = 0,
    columnNumber = 0,
    fieldNumber = 0,
    templates = {},
    fields = {},
    colorScheme = {
        "scheme-1": {
            "theme": "#34dca2",
            "font": "#212121",
            "field": "#f5f8f9",
            "background": "#ffffff"
        },
        "scheme-2": {
            "theme": "#ff735e",
            "font": "#212121",
            "field": "#f5f8f9",
            "background": "#ffffff"
        },
        "scheme-3": {
            "theme": "#007df7",
            "font": "#212121",
            "field": "#f5f8f9",
            "background": "#ffffff"
        },
        "scheme-4": {
            "theme": "#00ada9",
            "font": "#212121",
            "field": "#f5f8f9",
            "background": "#ffffff"
        },
        "scheme-5": {
            "theme": "#34dca2",
            "font": "#ffffff",
            "field": "#32383d",
            "background": "#2a3035"
        },
        "scheme-6": {
            "theme": "#ff735e",
            "font": "#ffffff",
            "field": "#32383d",
            "background": "#2a3035"
        },
        "scheme-7": {
            "theme": "#007df7",
            "font": "#ffffff",
            "field": "#32383d",
            "background": "#2a3035"
        }
    },
    app = {
        items: {},
        months : ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY',
            'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'],
        shortMonths: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
        days : ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'],
        edit: null,
        selector: null,
        formsTemplates: [],
        onMessage: null,
        state: false,
        loadingLibrary: {},
        masks: ['phone', 'zip', 'time', 'date', 'card'],
        _: function(key){
            if (formsLanguage && formsLanguage[key]) {
                return formsLanguage[key];
            } else {
                return key;
            }
        },
        getTextContent: function(textarea){
            let data = textarea ? textarea.value : CKE.getData();
            document.querySelector('#'+app.selector+' .text-content-wrapper').innerHTML = data;
            app.edit.html = data;
            app.buttonsPrevent($f('.ba-forms-workspace').find('a, input[type="submit"], button'));
        },
        getErrorText: function(text){
            let div = document.createElement('div');
            div.innerHTML = text;
            if (div.querySelector('title')) {
                text = div.querySelector('title').textContent;
            }

            return text;
        },
        fetch: async function(url, data){
            let request = await fetch(url, {
                    method: 'POST',
                    body: app.getFormData(data)
                }),
                response = null;
            if (request.ok) {
                response = await request.text();
            } else {
                let utf8Decoder = new TextDecoder("utf-8"),
                    reader = request.body.getReader(),
                    textData = await reader.read(),
                    text = utf8Decoder.decode(textData.value);
                console.info(app.getErrorText(text));
            }

            return response;
        },
        getFormData: function(data){
            let formData = new FormData();
            if (data) {
                for (let ind in data) {
                    formData.append(ind, data[ind]);
                }
            }

            return formData;
        }
    },
    mailchimp = {},
    campaign = {},
    getresponse = {},
    zoho_crm = {},
    googleSheets = {},
    googleDrive = {},
    fontBtn = libHandle = notification = CKE = uploadMode = null,
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

setInterval(function(){
    $f.ajax({
        type : "POST",
        dataType : 'text',
        url : "index.php?option=com_baforms&task=form.getSession",
        success : function(msg){
        }
    });
}, 600000);

window.addEventListener('storage', function(event) {
    switch (event.key) {
        case 'zoho_crm': 
            app.zoho.auth = JSON.parse(event.newValue);
            localStorage.removeItem('zoho_crm');
            app.zoho.authenticate();
            break;
    }
});

app.zoho = {
    client_id: '',
    client_secret: '',
    url: JUri+'administrator/index.php?option=com_baforms&task=form.',
    isEmpty: function(){
        if (this.client_id != this.auth.client_id || this.client_secret != this.auth.client_secret) {
            this.hideFields();
        } else if (this.fields) {
            this.drawFields();
        }
        if (this.client_id == '' || this.client_secret == '') {
            this.toggleBtn('remove', '');
            this.hideFields();
        } else {
            this.getAuthURL();
        }
    },
    getAuthURL: function(){
        app.fetch(app.zoho.url+'getZohoAuthURL', {
            client_id: app.zoho.client_id
        }).then(function(href){
            app.zoho.toggleBtn('add', href);
        });
    },
    toggleBtn: function(action, href){
        this.btn.href = href;
        this.btn.closest('.ba-subgroup-element').classList[action]('visible-subgroup');
    },
    authenticate: function(){
        this.auth.client_id = this.client_id;
        this.auth.client_secret = this.client_secret;
        app.fetch(app.zoho.url+'authenticateZoho', this.auth).then(function(text){
            app.zoho.auth = JSON.parse(text);
            app.zoho.getFields();
        });
    },
    hideFields: function(){
        $f('.zoho-crm-fields').removeClass('visible-subgroup').find('.ba-settings-item').remove();
    },
    getFields: function(){
        app.fetch(app.zoho.url+'getZohoFields', app.zoho.auth).then(function(text){
            let object = JSON.parse(text);
            app.zoho.auth = object.auth;
            app.zoho.fields = object.fields;
            zoho_crm = object;
            app.zoho.drawFields();
        });
    },
    drawFields: function(active){
        let div = $f('.zoho-crm-fields'),
            str = '';
        div.find('.ba-settings-item').remove();
        this.fields.forEach(function(field){
            str += '<div class="ba-settings-item ba-settings-select-type"'+
                (field.required ? ' data-required="true"' : '')+'><span class="ba-settings-item-title">'+
                field.label+(field.required ? ' *' : '')+'</span><select class="forms-fields-list" data-key="'+
                field.api_name+'"><option value="" hidden></option></select></div>';
        });
        div.append(str).addClass('visible-subgroup');
        drawSubmitItemsSelect();
        if (active) {
            setIntegrationValues('zoho_crm');
        }
    },
    setIntegration: function(){
        $f('.connect-zoho-crm').each(function(){
            this.value = integrations.zoho_crm.key[this.dataset.key];
            app.zoho[this.dataset.key] = integrations.zoho_crm.key[this.dataset.key];
        });
        this.auth = integrations.zoho_auth.key;
        this.isEmpty();
        this.hideFields();
        if (this.auth.client_id &&
            (!zoho_crm.auth || zoho_crm.auth.client_id != this.auth.client_id
                || zoho_crm.auth.client_id != this.auth.client_id)) {
            this.getFields(true);
        } else if (this.auth.client_id && zoho_crm.fields) {
            this.fields = zoho_crm.fields;
            this.drawFields(true);
        }
    }
}

app.google =  {
    sheets: {},
    drive: {},
    getGoogleAuth: function(client_id, btn){
        app.fetch('index.php?option=com_baforms&task=form.getGoogleAuth', {
            client_id: client_id
        }).then(function(text){
            btn.href = text;
            btn.closest('.ba-subgroup-element').classList.add('visible-subgroup');
        });
    },
    getWorkSheets: function(accessToken, spreadsheet, obj, flag, worksheet){
        app.fetch('index.php?option=com_baforms&task=form.getWorkSheets', {
            client_id: app.google.sheets.client_id,
            client_secret: app.google.sheets.client_secret,
            accessToken: accessToken,
            spreadsheet: obj.sheets[spreadsheet].id
        }).then(function(text){
            obj.sheets[spreadsheet].worksheets = JSON.parse(text);
            if (flag) {
                notification[0].className = 'animation-out';
                document.querySelector('.google-sheet-spreadsheets').dataset.authentication = 'enabled';
            }
            app.google.drawWorksheets(obj.sheets[spreadsheet].worksheets, spreadsheet);
            if (worksheet) {
                app.google.getWorkSheetsColumns(accessToken, spreadsheet, obj, flag, worksheet)
            }
        });
    },
    getWorkSheetsColumns: function(accessToken, spreadsheet, obj, flag, worksheet){
        app.fetch('index.php?option=com_baforms&task=form.getWorkSheetsColumns', {
            client_id: app.google.sheets.client_id,
            client_secret: app.google.sheets.client_secret,
            accessToken: obj.accessToken,
            spreadsheet: obj.sheets[spreadsheet].id,
            worksheet: worksheet
        }).then(function(text){
            obj.sheets[spreadsheet].worksheets[worksheet].columns = JSON.parse(text);
            if (flag) {
                document.querySelector('.google-sheet-worksheets').dataset.authentication = 'enabled';
                notification[0].className = 'animation-out';
            } else {
                checkIntegrationsActiveState('google_sheets');
            }
            app.google.drawWorkSheetsColumns(obj.sheets[spreadsheet].worksheets[worksheet].columns);
        });
    },
    drawDriveFolders: function(obj){
        let str = '';
        $f('.google-drive-folders').addClass('visible-subgroup');    
        for (let i = 0; i < obj.folders.length; i++) {
            str += '<li data-value="'+obj.folders[i].id+'">'+obj.folders[i].title+'</li>';
        }
        $f('#drive-folders-dialog ul').html(str);
    },
    drawSpreadsheets: function(obj){
        let select = $f('.google-sheet-spreadsheets');
        select.find('option:not([hidden])').remove();
        select.closest('.ba-subgroup-element').addClass('visible-subgroup');
        for (let ind in obj.sheets) {
            select.append('<option value="'+obj.sheets[ind].id+'">'+obj.sheets[ind].title+'</option>');
        }
    },
    drawWorksheets: function(obj, spreadsheet){
        let select = $f('.google-sheet-worksheets').val('');
        $f('.google-sheets-fields .ba-settings-item').remove();
        select.find('option:not([hidden])').remove();
        select.closest('.ba-subgroup-element').addClass('visible-subgroup');
        $f('.google-sheets-fields.visible-subgroup').removeClass('visible-subgroup');
        select[0].spreadsheet = spreadsheet;
        for (let ind in obj) {
            select.append('<option value="'+obj[ind].id+'">'+obj[ind].title+'</option>');
        }
    },
    drawWorkSheetsColumns: function(columns){
        let div = $f('.google-sheets-fields'),
            str = '';
        div.find('.ba-settings-item').remove();
        for (let i = 0; i < columns.length; i++) {
            str += '<div class="ba-settings-item ba-settings-select-type"><span class="ba-settings-item-title">'+
                columns[i]+'</span><select class="forms-fields-list" data-key="'+
                columns[i]+'"><option value="" hidden></option></select></div>';
        }
        div.append(str).addClass('visible-subgroup');
        drawSubmitItemsSelect();
    },
    setClient: function(key, client_id, client_secret){
        app.google[key].client_id = client_id;
        app.google[key].client_secret = client_secret;
    },
    setGoogleAuth: function(key){
        if (app.google[key].client_id && app.google[key].client_secret) {
            app.google.getGoogleAuth(app.google[key].client_id, app.google[key].btn);
        } else {
            $f(app.google[key].btn).closest('.ba-subgroup-element').removeClass('visible-subgroup')
                .find('input[type="text"]').val('').trigger('input');
        }
    },
    setDriveIntegration: function(){
        app.google.setClient('drive', integrations.google_drive.key.client_id, integrations.google_drive.key.client_secret);
        app.google.setGoogleAuth('drive');
        if (app.google.drive.client_id && app.google.drive.client_secret && integrations.google_drive.key.accessToken) {
            app.fetch('index.php?option=com_baforms&task=form.getDriveFolders', {
                client_id: app.google.drive.client_id,
                client_secret: app.google.drive.client_secret,
                token: integrations.google_drive.key.accessToken
            }).then(function(text){
                googleDrive.code = integrations.google_drive.key.code;
                googleDrive.accessToken = integrations.google_drive.key.accessToken;
                googleDrive.folders = JSON.parse(text);
                app.google.drawDriveFolders(googleDrive);
                delete integrations.google_drive.key.accessToken;
            });
        }
        $f('.get-google-drive-auth-url').on('input', function(){
            $f('.authenticate-google-drive').val('');
            $f('.integration-options[data-group="google_drive"] .ba-subgroup-element').first()
                .nextAll('.ba-subgroup-element').removeClass('visible-subgroup');
            app.google.drive[this.dataset.key] = this.value.trim();
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                app.google.setGoogleAuth('drive');
            }, 300);
        })
        $f('.authenticate-google-drive').on('input', function(){
            if (this.dataset.authentication == 'pending' && this.value.trim()) {
                return false;
            } else if (!this.value.trim()) {
                app.googleDrive = {};
                $f('.integration-options[data-group="google_drive"]').find('.ba-subgroup-element').first()
                    .nextAll('.ba-subgroup-element').removeClass('visible-subgroup').find('select').val('');
                return false;
            }
            let $this = this;
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                var str = app._('PLEASE_WAIT')+'<img src="'+JUri;
                str += 'components/com_baforms/assets/images/reload.svg"></img>';
                notification[0].className = 'notification-in';
                notification.find('p').html(str);
                $this.dataset.authentication = 'pending';
                $f('.google-drive-folders input').val('');
                app.fetch('index.php?option=com_baforms&task=form.createDriveToken', {
                    client_id: app.google.drive.client_id,
                    client_secret: app.google.drive.client_secret,
                    code: $this.value.trim()
                }).then(function(text){
                    $this.dataset.authentication = 'enabled';
                    if (!text || text == 'INVALID_TOKEN') {
                        showNotice('Invalid Authentication Code', 'ba-alert');
                    } else {
                        app.googleDrive = JSON.parse(text);
                        notification[0].className = 'animation-out';
                        app.google.drawDriveFolders(app.googleDrive);
                    }
                });
            }, 500);
        });
        $f('.google-drive-folders').on('change', function(){
            $f('.google-drive-fields').addClass('visible-subgroup');
        });
    },
    setSheetsIntegration: function(){
        app.google.setClient('sheets', integrations.google_sheets.key.client_id, integrations.google_sheets.key.client_secret);
        app.google.setGoogleAuth('sheets');
        if (app.google.sheets.client_id && app.google.sheets.client_secret && integrations.google_sheets.key.accessToken) {
            app.fetch('index.php?option=com_baforms&task=form.getSpreadSheets', {
                client_id: app.google.sheets.client_id,
                client_secret: app.google.sheets.client_secret,
                token: integrations.google_sheets.key.accessToken
            }).then(function(text){
                googleSheets.code = integrations.google_sheets.key.code;
                googleSheets.accessToken = integrations.google_sheets.key.accessToken;
                googleSheets.sheets = JSON.parse(text);
                app.google.drawSpreadsheets(googleSheets);
                delete integrations.google_sheets.key.accessToken;
                if (integrations.google_sheets.key.spreadsheet) {
                    let spreadsheet = integrations.google_sheets.key.spreadsheet,
                        worksheet = integrations.google_sheets.key.worksheet;
                    app.google.getWorkSheets(googleSheets.accessToken, spreadsheet, googleSheets, false, worksheet)
                }
            });
        }
        $f('.get-google-sheets-auth-url').on('input', function(){
            $f('.authenticate-google-sheets').val('');
            $f('.integration-options[data-group="google_sheets"] .ba-subgroup-element').first()
                .nextAll('.ba-subgroup-element').removeClass('visible-subgroup');
            $f('.google-sheet-worksheets, .google-sheet-spreadsheets').val('');
            $f('.google-sheets-fields .ba-settings-item').remove();
            app.google.sheets[this.dataset.key] = this.value.trim();
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                app.google.setGoogleAuth('sheets');
            }, 300);
        });
        $f('.authenticate-google-sheets').on('input', function(){
            if (this.dataset.authentication == 'pending' && this.value.trim()) {
                return false;
            } else if (!this.value.trim()) {
                app.googleSheets = {};
                $f(this).closest('.ba-subgroup-element').nextAll('.ba-subgroup-element').removeClass('visible-subgroup');
                $f('.google-sheet-worksheets, .google-sheet-spreadsheets').val('');
                $f('.google-sheets-fields .ba-settings-item').remove();
                return false;
            }
            let $this = this;
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                var str = app._('PLEASE_WAIT')+'<img src="'+JUri;
                str += 'components/com_baforms/assets/images/reload.svg"></img>';
                notification[0].className = 'notification-in';
                notification.find('p').html(str);
                $this.dataset.authentication = 'pending';
                $f('.google-sheet-worksheets, .google-sheet-spreadsheets').val('');
                $f('.google-sheets-fields .ba-settings-item').remove();
                app.fetch('index.php?option=com_baforms&task=form.createSheetsToken', {
                    client_id: app.google.sheets.client_id,
                    client_secret: app.google.sheets.client_secret,
                    code: $this.value.trim(),
                }).then(function(text){
                    $this.dataset.authentication = 'enabled';
                    if (!text || text == 'SHEETS_INVALID_TOKEN') {
                        showNotice('Invalid Authentication Code', 'ba-alert');
                    } else {
                        app.googleSheets = JSON.parse(text);
                        notification[0].className = 'animation-out';
                        app.google.drawSpreadsheets(app.googleSheets);
                    }
                });
            }, 500);
        });
        $f('.google-sheet-spreadsheets').on('change', function(){
            let value = $f(this).val();
            if (this.dataset.authentication == 'pending') {
                return false;
            } else if (app.googleSheets.sheets[value].worksheets) {
                app.google.drawWorksheets(app.googleSheets.sheets[value].worksheets, value);
            }
            let str = app._('INSTALLING')+'<img src="'+JUri;
            this.dataset.authentication = 'pending';
            str += 'components/com_baforms/assets/images/reload.svg"></img>';
            notification[0].className = 'notification-in';
            notification.find('p').html(str);
            app.google.getWorkSheets(app.googleSheets.accessToken, value, app.googleSheets, true, false)
        });
        $f('.google-sheet-worksheets').on('change', function(){
            let value = $f(this).val();
            if (this.dataset.authentication == 'pending') {
                return false;
            } else if (app.googleSheets.sheets[this.spreadsheet].worksheets[value].columns) {
                app.google.drawWorkSheetsColumns(app.googleSheets.sheets[this.spreadsheet].worksheets[value].columns);
                return false;
            }
            let str = app._('INSTALLING')+'<img src="'+JUri;
            this.dataset.authentication = 'pending';
            str += 'components/com_baforms/assets/images/reload.svg"></img>';
            notification[0].className = 'notification-in';
            notification.find('p').html(str);
            app.google.getWorkSheetsColumns(app.googleSheets.accessToken, this.spreadsheet, app.googleSheets, true, value);
        });
    }
}

app.loadGoogleMaps = function(search)
{
    if (!app.googleMaps) {
        app.mapScript = document.createElement('script');
        app.mapScript.onload = function(){
            app.googleMaps = true;
            app.loadGoogleMaps(search);
            if ($f('#google-maps-field-settings-dialog').hasClass('in')) {
                app.createLocationMap();
            }
        }
        app.mapScript.src = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='+integrations.google_maps.key;
        document.head.append(app.mapScript);
    } else {
        if (!search) {
            search = '.ba-form-field-item.ba-form-map-field';
        }
        $f(search).each(function(){
            app.createGoogleMap(this.querySelector('.ba-map-wrapper'), app.items[this.id]);
        });
    }
}

app.createLocationMap = function()
{
    $f('input[data-option="place"]').removeAttr('style').removeAttr('placeholder')
        .removeAttr('class').removeAttr('autocomplete').removeAttr('disabled');
    if (app.googleMaps) {
        var obj = {
                "scrollwheel": false,
                "navigationControl": true,
                "mapTypeControl": true,
                "scaleControl": true,
                "draggable": true,
                "zoomControl": true,
                "disableDefaultUI": false,
                "disableDoubleClickZoom": false
            },
            options = $f.extend({}, app.edit.map, obj),
            locationInput = document.querySelector('input[data-option="place"]'),
            autocomplete = new google.maps.places.Autocomplete(locationInput);
        app.locationMap = new google.maps.Map(document.querySelector('.ba-address-map-wrapper'), options);
        app.locationMap.setOptions({styles: mapStyles.dark});
        locationInput.value = '';
        setMarker();
        $f(locationInput).on('input', function(){
            $f('.pac-container').on('mousedown', function(event){
                event.stopPropagation();
            });
        });
        autocomplete.addListener('place_changed', function(){
            var place = autocomplete.getPlace();
            app.edit.place = locationInput.value;
            if (place.geometry.viewport) {
                app.locationMap.fitBounds(place.geometry.viewport);
            } else {
                app.locationMap.setCenter(place.geometry.location);
            }
        });
        app.locationMap.addListener('maptypeid_changed',function(event){
            setCenter();
        });
        app.locationMap.addListener('idle',function(event){
            setCenter();
        });
        app.locationMap.addListener('click', function(event) {
            if (!app.edit.marker.position) {
                app.edit.marker.position = {}
            }
            app.edit.marker.position.lat = event.latLng.lat();
            app.edit.marker.position.lng = event.latLng.lng();
            setMarker();
        });
    }
}

!function(d){
    function getTemplatesElement(obj)
    {
        let div = templates['templates-element'].content.cloneNode(true),
            el = div.querySelector('.templates-element'),
            script = d.createElement('script');
        div.querySelector('span').textContent = obj.title;
        el.dataset.group = obj.group;
        el.dataset.key = obj.key;
        d.querySelector('#templates-modal .integrations-group').append(div);
        script.src = 'https://www.balbooa.com/updates/baforms/formsApi/'+obj.key+'.js';
        script.onload = function(){
            app.formsTemplates.push(formsApi.templates[obj.group][obj.key]);
            let img = formsApi.templates[obj.group][obj.key].imageData
            el.querySelector('.templates-element-image').style.backgroundImage = 'url('+img+')';
        }
        d.head.append(script);
    }

    d.addEventListener('DOMContentLoaded', function(){
        let script = d.createElement('script');
        script.src = 'https://www.balbooa.com/updates/baforms/formsApi/formsApi.js';
        script.onload = function(){
            if (formsApi.templates) {
                for (let ind in formsApi.templates) {
                    if (!d.querySelector('#templates-modal li[data-group="'+ind+'"]')) {
                        delete formsApi.templates[ind];
                        continue;
                    }
                    for (let key in formsApi.templates[ind]) {
                        if (d.querySelector('#templates-modal .templates-element[data-group="'+ind+'"][data-key="'+key+'"]')) {
                            delete formsApi.templates[ind][key];
                            continue;
                        }
                        getTemplatesElement(formsApi.templates[ind][key]);
                    }
                }
            }
        }
        d.head.append(script);
    });
}(document);

function setMarker()
{
    if (app.edit.marker.position) {
        if (app.locationMap.marker) {
            app.locationMap.marker.setMap(null);
        }
        var obj = {
            position: app.edit.marker.position,
            map: app.locationMap
        }
        if (app.edit.marker.icon) {
            obj.icon = JUri+app.edit.marker.icon;
        }
        app.locationMap.marker = new google.maps.Marker(obj);
        if (app.edit.marker.description) {
            app.locationMap.marker.infoWindow = new google.maps.InfoWindow({
                content : app.edit.marker.description
            });
            if (app.edit.marker.infobox) {
                app.locationMap.marker.infoWindow.open(app.locationMap, app.locationMap.marker);
            }
            app.locationMap.marker.addListener('click', function(event){
                this.infoWindow.open(app.locationMap, this);
            });
        }
    }
}

app.checkState = function(){
    fetch('index.php?option=com_baforms&task=form.checkState').then(function(response){
        return response.json();
    }).then(function(obj){
        app.state = !!obj.data;
    });
}

function setCenter()
{
    var center = app.locationMap.getCenter();
    app.edit.map.center.lat = center.lat();
    app.edit.map.center.lng = center.lng();
    app.edit.map.zoom = app.locationMap.getZoom();
    app.edit.map.mapTypeId = app.locationMap.getMapTypeId();
}

app.createGoogleMap = function(div, obj){
    let map = new google.maps.Map(div, obj.map);
    map.setOptions({styles: mapStyles[obj.styleType]});
    if (obj.marker.position) {
        let object = {
            position : obj.marker.position,
            map : map
        }
        if (obj.marker.icon) {
            object.icon = JUri+obj.marker.icon;
        }
        let marker = new google.maps.Marker(object);
        if (obj.marker.description) {
            marker.infoWindow = new google.maps.InfoWindow({
                content : obj.marker.description
            });
            if (obj.marker.infobox == 1) {
                marker.infoWindow.open(map, marker);
            }
            marker.addListener('click', function(event){
                this.infoWindow.open(map, this);
            });
        }
    }
}

app.ckeLink = function(){
    $f('.post-link-input').on('input', function(){
        if (this.value.trim()) {
            $f('#post-link-apply').addClass('active-button');
        } else {
            $f('#post-link-apply').removeClass('active-button');
        }
    }).on('focus', function(){
        this.closest('.link-picker-container').classList.add('focus-link-input');
    }).on('blur', function(){
        this.closest('.link-picker-container').classList.remove('focus-link-input');
    });
    $f('.cke-link-target-select').on('customAction', function(){
        if ($f('.post-link-input').val().trim()) {
            $f('#post-link-apply').addClass('active-button');
        } else {
            $f('#post-link-apply').removeClass('active-button');
        }
    });
    $f('#post-link-apply').on('click', function(event){
        event.preventDefault();
        if ($f(this).hasClass('active-button')) {
            var obj = {
                href: $f('.post-link-input').val().trim(),
                target: $f('.cke-link-target-select input[type="hidden"]').val()
            }
            app.currentCKE.plugins.myLink.insertLink(obj)
            $f('#edit-post-link-dialog').modal('hide');
        }
    });
}

app.customSelect = function(){
    $f('.ba-custom-select > i, div.ba-custom-select input').on('click', function(event){
        event.stopPropagation();
        var $this = $f(this),
            parent = $this.parent(),
            ul = parent.find('ul');
        if (!ul.hasClass('visible-select')) {
            event.stopPropagation();
            $f('.visible-select').removeClass('visible-select');
            ul.addClass('visible-select');
            parent.find('li').off('click').one('click', function(){
                var text = this.textContent.trim(),
                    val = this.dataset.value;
                if (this.dataset.title) {
                    text = this.dataset.title;
                }
                parent.find('input[type="text"]').val(text);
                parent.find('input[type="hidden"]').val(val).trigger('change');
                parent.trigger('customAction');
            });
            parent.trigger('show');
            setTimeout(function(){
                $f('body').one('click', function(){
                    $f('.visible-select').trigger('customHide').removeClass('visible-select');
                });
            }, 50);
        }
    });
    $f('div.ba-custom-select').on('show', function(){
        var $this = $f(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    });
    $f('.font-search').on('input', function(){
        let $this = $f(this),
            search = this.value.toLowerCase(),
            li = $this.closest('div.modal-list-type-wrapper').find('li[data-value]');
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            if (!search) {
                li.show();
            } else {
                li.each(function(){
                    var font = this.textContent.toLowerCase();
                    if (font.indexOf(search) < 0 || this.classList.contains('popular-fonts')) {
                        this.style.display = 'none';
                    } else {
                        this.style.display = '';
                    }
                });
            }
        }, 300);
    });
    $f('.default-country-search').on('input', function(){
        var $this = $f(this),
            search = this.value.toLowerCase(),
            li = $this.closest('div.modal-list-type-wrapper').find('li[data-value]');
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            if (!search) {
                li.show();
            } else {
                li.each(function(){
                    var title = this.dataset.title.toLowerCase();
                    if (title.indexOf(search) < 0) {
                        this.style.display = 'none';
                    } else {
                        this.style.display = '';
                    }
                });
            }
        }, 300);
    });
}

app.checkState();

app.setTooltip = function(parent){
    parent.off('mouseenter mouseleave').on('mouseenter', function(){
        if (this.classList.contains('ba-form-field') && !this.classList.contains('disabled-field-drag')
            && (this.dataset.key == 'map' || this.dataset.key == 'address')) {
            return false;
        }
        var coord = this.getBoundingClientRect(),
            top = coord.top,
            data = $f(this).find('.ba-tooltip').html(),
            center = coord.left + (coord.width / 2),
            className = $f(this).find('.ba-tooltip')[0].className;
        if ($f(this).find('.ba-tooltip').hasClass('ba-bottom')) {
            top = coord.bottom;
        }
        if (this.classList.contains('ba-input-help')) {
            className += ' ba-input-help-tooltip';
        }
        $f('body').append('<span class="'+className+'">'+data+'</span>');
        var tooltip = $f('body > .ba-tooltip').last(),
            width = tooltip.outerWidth(),
            height = tooltip.outerHeight();
        if (tooltip.hasClass('ba-top') || tooltip.hasClass('ba-help')) {
            top -= (15 + height);
            center -= (width / 2)
        } else if (tooltip.hasClass('ba-bottom')) {
            top += 10;
            center -= (width / 2)
        } else if (tooltip.hasClass('ba-right')) {
            center = coord.right + 10;
        }
        tooltip.css({
            'top' : top+'px',
            'left' : center+'px'
        });
    }).on('mouseleave', function(){
        var tooltip = $f('body > .ba-tooltip');
        tooltip.addClass('tooltip-hidden');
        setTimeout(function(){
            tooltip.remove();
        }, 500);
    });
}

app.showCodeEditor = function(){
    let modal = $f('#code-editor-dialog');
    setTimeout(function(){
        modal.one('shown', function(){
            app.codeCss.refresh();
        }).modal();
        modal.find('a[href="#code-edit-javascript"]').one('shown', function(){
            app.codeJs.refresh();
        });
    }, 50);
}

app.codemirror = {
    files: {
        css: [
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/material.min.css'
        ],
        js: [
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/css-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/javascript-hint.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js'
        ]
    },
    init: function(callback){
        this.callback = callback;
        this.load.css();
        this.load.js();
    },
    load: {
        css: () => {
            app.codemirror.files.css.forEach((href) => {
                let file = document.createElement('link');
                file.rel = 'stylesheet';
                file.href = href;
                document.head.append(file);
            });
        },
        js: () => {
            let src = app.codemirror.files.js.shift();
            if (src) {
                let file = document.createElement('script');
                file.src = src;
                file.onload = function(){
                    app.codemirror.load.js();
                }
                document.head.append(file);
            } else {
                app.codemirror.setup();
            }
        }
    },
    setup: function(){
        let input = document.getElementById('code-css-value'),
            style = document.querySelector('#custom-css-editor > style');
        app.codeCss = CodeMirror.fromTextArea(document.getElementById('code-editor-css'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2,
            mode: "css"
        });
        app.codeCss.setValue(input.value);
        app.codeCss.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codeCss.on("inputRead", function(cm, event) {
            if (!cm.state.completionActive && event.text[0] != ':' && event.text[0] != ';'
                && event.text[0] != '{' && $f.trim(event.text[0]) != '' && event.origin != 'paste') {
                CodeMirror.commands.autocomplete(cm, null, {completeSingle: false});
            }
        });
        app.codeCss.on('change', function(from, too) {
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                let value = app.codeCss.getValue();
                input.value = value;
                style.innerHTML = value
            }, 500);
        });
        app.codeJs = CodeMirror.fromTextArea(document.getElementById('code-editor-javascript'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2,
            mode: "javascript"
        });
        input = document.getElementById('code-js-value');
        app.codeJs.setValue(input.value);
        jsFlag = true;
        app.codeJs.on("inputRead", function(cm, event) {
            if (!cm.state.completionActive && event.text[0] != ':' && event.text[0] != ';'
                && event.text[0] != '{' && $f.trim(event.text[0]) != '' && event.origin != 'paste') {
                CodeMirror.commands.autocomplete(cm, null, {completeSingle: false});
            }
        });
        app.codeJs.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codeJs.on('change', function(from, too) {
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                input.value = app.codeJs.getValue();
            }, 500);
        });
        app.HTMLEditor = CodeMirror.fromTextArea(document.getElementById('custom-html-editor'), {
            lineNumbers: true,
            theme: 'material',
            lineWrapping: true,
            tabSize: 2,
            mode: "htmlmixed"
        });
        app.HTMLEditor.on('change', function(from, too) {
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                let value = app.HTMLEditor.getValue(),
                    item = document.getElementById(app.selector).querySelector('div.custom-html-wrapper');
                item.innerHTML = value;
                app.edit.html = value;
                app.buttonsPrevent($f('.ba-forms-workspace').find('a, input[type="submit"], button'));
            }, 500);
        });
        app.HTMLEditor.on('keydown', function(cm, event){
            event.stopPropagation();
        });
        app.codemirror.loaded = true;
        app.codemirror.callback();
    }
}

app.loadResizable = function(){
    let file = document.createElement('script');
    file.type = 'text/javascript';
    file.src = 'components/com_baforms/assets/libraries/resizable/resizable.js';
    document.head.appendChild(file);
    file.onload = function(){
        $f('.code-editor-dialog').resizable({
            handle : '.resizable-handle',
            change : function(){
                app.codeCss.refresh();
                app.codeJs.refresh();
                app.HTMLEditor.refresh();
            },
            update: (item, direction) => {
                let modal = item[0];
                if (direction == 'left' && modal.querySelector('.select-modal-cp-position')) {
                    let rect = modal.getBoundingClientRect();
                    document.body.style.setProperty('--modal-cp-width', rect.width+'px');
                    app.cp.set(modal, 'width', rect.width);
                    modal.style.width = '';
                }
            }
        });
        $f('.draggable-modal-cp, #text-editor-dialog').resizable({
            handle : '.resizable-handle',
            update: (item, direction) => {
                let modal = item[0];
                if (direction == 'left' && modal.querySelector('.select-modal-cp-position')) {
                    let rect = modal.getBoundingClientRect();
                    document.body.style.setProperty('--modal-cp-width', rect.width+'px');
                    app.cp.set(modal, 'width', rect.width);
                    modal.style.width = '';
                }
            }
        });
    }
}

app.loadDraggable = function(){
    let file = document.createElement('script');
    file.type = 'text/javascript';
    file.src = 'components/com_baforms/assets/libraries/draggable/draggable.js';
    document.head.appendChild(file);
    file.onload = function(){
        $f('.code-editor-dialog, .draggable-modal-cp, #text-editor-dialog').draggable({
            'handle' : '.modal-header',
            change: (item) => {
                let modal = item[0];
                if (modal.querySelector('.select-modal-cp-position')) {
                    let rect = modal.getBoundingClientRect();
                    document.body.style.setProperty('--modal-cp-left', rect.left+'px');
                    document.body.style.setProperty('--modal-cp-top', rect.top+'px');
                    app.cp.set(modal, 'left', rect.left);
                    app.cp.set(modal, 'top', rect.top);
                    modal.style.left = '';
                    modal.style.top = '';
                }
            }
        });
    }
}

app.cookie = {
    get: function(name) {
        let exp = new RegExp("(?:^|; )"+name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1')+"=([^;]*)"),
            match = document.cookie.match(exp);

        return match ? decodeURIComponent(match[1]) : undefined;
    },
    set: function(name, value, options) {
        options = options || {};
        let expires = options.expires;
        if (typeof expires == "number" && expires) {
            let d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }
        value = encodeURIComponent(value);
        let str = name+"="+value;
        for (let ind in options) {
            str += "; "+ind;
            if (options[ind] !== true) {
                str += "="+options[ind];
            }
        }
        document.cookie = str;
    },
    delete: function (name) {
        this.set(name, "", {
            expires: -1
        });
    }
}

app.cp = {
    position: '',
    delay: null,
    inPosition: () => {
        return app.cp.position != '';
    },
    edit: (item) => {
        if (!item) {
            app.selector = null;
            setTimeout(function(){
                app.cp.show();
                app.showNavigationSettings();
            }, 300);
        } else {
            app.selector = item.id;
            setTimeout(function(){
                app.cp.show();
                app.showFieldSettings();
            }, 300);
        }
    },
    prepare: () => {
        let content = templates['select-modal-cp-position'].content,
            obj = app.cp.get(),
            handle = templates['resize-handle-left'].content;
        if (obj) {
            app.cp.position = obj.position;
        }
        document.querySelectorAll('.ba-modal-cp, #text-editor-dialog, #html-editor-dialog').forEach((cp) => {
            cp.append(handle.cloneNode(true));
            cp.querySelectorAll('.modal-header-icon').forEach((header) => {
                header.insertBefore(content.cloneNode(true), header.querySelector('i'))
            });
        });
        $f('.select-modal-cp-position select').on('change', function(){
            let position = this.value,
                modal = $f(this).closest('.modal');
            app.cp.changePosition(position);
            app.cp.position = position;
            app.cp.set(modal, 'position', position);
        }).find('option').each((i, option) => {
            if (obj && obj.position == option.value) {
                option.selected = true;
            }
        });
        $f('.close-cp-modal').on('click', function(){
            let modal = $f(this).closest('.modal');
            app.cp.close(modal);
        });
    },
    changePosition: (position) => {
        document.body.classList[position == '' ? 'remove' : 'add']('forms-cp-panel-right');
        CKE.config.bodyClass = position == '' ? '' : 'forms-cp-panel-right-enabled';
        $f('.text-editor-textarea').each(function(){
            this.CKE.config.bodyClass = position == '' ? '' : 'forms-cp-panel-right-enabled';
        });
    },
    show: () => {
        if (app.cp.delay) {
            clearTimeout(app.cp.delay);
            app.cp.delay = null;
        }
        let obj = app.cp.get();
        if (!obj) {
            return;
        }
        if (obj.position) {
            app.cp.changePosition(obj.position);
        }
        for (let ind in obj) {
            if (ind == 'position') {
                continue;
            }
            document.body.style.setProperty('--modal-cp-'+ind, obj[ind]+'px');
        }
    },
    close: (modal) => {
        modal.modal('hide');
        if (app.cp.delay) {
            clearTimeout(app.cp.delay);
            app.cp.delay = null;
        }
        app.cp.delay = setTimeout(function(){
            document.body.classList.remove('forms-cp-panel-right');
        }, 500);
    },
    closeAll: () => {
        $f('.modal.in').each(function(){
            if (this.querySelector('.close-cp-modal')) {
                app.cp.close($f(this));
            } else {
                $f(this).modal('hide');
            }
        });
    },
    get: (modal) => {
        let obj = rect = null,
            cp = app.cookie.get('forms-modal-cp-position');
        if (cp) {
            obj = JSON.parse(cp);
        } else if (modal) {
            rect = modal.getBoundingClientRect();
            obj = {
                position: '',
                width: rect.width,
                top: rect.top,
                left: rect.left
            }
        }

        return obj;
    },
    set: (modal, key, value) => {
        let obj = app.cp.get(modal),
            cp = '';
        obj[key] = value;
        cp = JSON.stringify(obj);
        app.cookie.set('forms-modal-cp-position', cp);
    }
}

app.buttonsPrevent = function(search){
    search = search ? search : 'a:not(.brand):not(.d-flex):not(.header-item-content), input[type="submit"], button'
    $f(search).not('.default-action').on('click', function(event){
        if (!this.parentNode.classList.contains('viewsite')) {
            event.preventDefault();
        }
    });
}

app.prepareTemplates = function(){
    $f('template[data-key]').each(function(){
        templates[this.dataset.key] = this;
    });
}

app.setTabsAnimation = function(){
    $f('.general-tabs > ul').on('show', function(event){
        event.stopPropagation();
        var ind = [],
            ul = $f(event.currentTarget),
            id = $f(event.relatedTarget).attr('href'),
            aId = $f(event.target).attr('href');
        ul.find('li a').each(function(i){
            if (this == event.target) {
                ind[0] = i;
            }
            if (this == event.relatedTarget) {
                ind[1] = i;
            }
        });
        if (ind[0] > ind[1]) {
            $f(id).addClass('out-left');
            $f(aId).addClass('right');
            setTimeout(function(){
                $f(id).removeClass('out-left');
                $f(aId).removeClass('right');
            }, 500);
        } else {
            $f(id).addClass('out-right');
            $f(aId).addClass('left');
            setTimeout(function(){
                $f(id).removeClass('out-right');
                $f(aId).removeClass('left');
            }, 500);
        }
    }).on('shown', function(event){
        event.stopPropagation();
    });
    $f('.general-tabs > ul a').on('show', function(event){
        var parent = $f(this).closest('.general-tabs'),
            prev = event.relatedTarget.getBoundingClientRect(),
            next = event.target.getBoundingClientRect();
        parent.find('.tabs-underline').stop().css({
            'left' : prev.left,
            'right' : document.documentElement.clientWidth - prev.right,
        }).show().animate({
            'left' : next.left,
            'right' : document.documentElement.clientWidth - next.right,
        }, 500, function(){
            parent.find('.tabs-underline').hide();
        });
    });
    $f('.accordion').on('show', function(event){
        event.stopPropagation();
        $f(event.target).closest('.accordion-group').addClass('active');
    }).on('shown hidden', function(event){
        event.stopPropagation();
    }).on('hide', function(event){
        event.stopPropagation();
        $f(event.target).closest('.accordion-group').removeClass('active');
    }).each(function(ind){
        this.id = 'accordion-'+ind;
        $f(this).find('.accordion-toggle').each(function(i){
            this.dataset.parent = '#accordion-'+ind;
            this.href = '#collapse-'+String(ind) + String(i);
        });
        $f(this).find('.accordion-body').each(function(i){
            this.id = 'collapse-'+String(ind) + String(i);
        });
    });
}

app.setMediaManager = function(){
    $f('#uploader-modal').on('hide', function(){
        var iframe = this.querySelector('iframe').contentWindow;
        iframe.document.body.classList.remove('media-manager-enabled');
        iframe.jQuery('#check-all').prop('checked', false);
        iframe.jQuery('.select-item').prop('checked', false);
        iframe.jQuery('.active').removeClass('active');
        iframe.jQuery('.ba-context-menu').hide();
        iframe.jQuery('.ba-context-menu').hide();
        iframe.jQuery('.modal.in').modal('hide');
        iframe.jQuery('.context-active').removeClass('context-active');
    });
    $f('.show-media-manager').on('mousedown', function(){
        setTimeout(function(){
            uploadMode = null;
            checkIframe($f('#uploader-modal').attr('data-check', 'multiple'), 'uploader', function(){
                var iframe = document.querySelector('#uploader-modal iframe').contentWindow;
                iframe.document.body.classList.add('media-manager-enabled');
            });
        }, 50);
    });
}

app.showLogin = function(onMessage){
    let login = document.querySelector('#login-modal');
    if (login) {
        app.onMessage = onMessage;
        $f(login).modal();
    } else {
        showNotice(app._('AWESOME_FEATURE_AVAILABLE_IN_PRO'), 'ba-alert');
    }
}

app.setAddFields = function(){
    $f('.ba-form-fields-list').on('mousedown', '.ba-form-field', function(event){
        let enabled = ['input', 'submit', 'text', 'image', 'html'];
        if (!app.state && enabled.indexOf(this.dataset.key) == -1) {
            app.showLogin(null);
            return false;
        }
        if (this.classList.contains('disabled-field-drag')) {
            return false;
        }
        var item = rect = style = next = null,
            marginTop = marginBottom = 0,
            key = this.dataset.key;
        $f('body').trigger('mousedown');
        libHandle.style.display = '';
        libHandle.style.top = event.clientY+'px';
        libHandle.style.left = event.clientX+'px';
        var placeholder = document.getElementById('library-placeholder'),
            backdrop = document.getElementById('library-backdrop');
        $f(document).on('mousemove.library', function(event){
            libHandle.style.top = event.clientY+'px';
            libHandle.style.left = event.clientX+'px';
            placeholder.style.display = '';
            if (!backdrop.classList.contains('visible-backdrop')) {
                backdrop.classList.add('visible-backdrop');
            }
            item = returnPointLibraryItem(event);
            if (item) {
                rect = item.getBoundingClientRect();
                style = getComputedStyle(item);
                marginTop = style.marginTop.replace(/px|%/, '') * 1;
                marginBottom = style.marginBottom.replace(/px|%/, '') * 1;
                next = (event.clientY - (rect.top - marginTop)) / ((rect.bottom + marginBottom) - (rect.top - marginTop)) > .5
                var obj = {
                    "left" : rect.left + 16,
                    "width" : rect.right - rect.left - 30
                };
                if (next || item.classList.contains('ba-form-column')) {
                    obj.top = rect.bottom + marginBottom;
                } else {
                    obj.top = rect.top - marginTop;
                }
                $f(placeholder).css(obj);
            } else {
                placeholder.style.display = 'none';
            }
            return false;
        }).on('mouseup.library', function(){
            libHandle.style.display = 'none';
            placeholder.style.display = 'none';
            backdrop.classList.remove('visible-backdrop');
            $f(document).off('mouseup.library mousemove.library');
            if (item) {
                let data = {
                    item: item,
                    key: key,
                    next: next
                }
                if (key == 'image') {
                    fontBtn = data;
                    uploadMode = 'addImage';
                    checkIframe($f('#uploader-modal').attr('data-check', 'single'), 'uploader');
                } else {
                    app.dropField(data);
                }
            }
        });
        return false;
    });
}

app.addLibrary = function(type, passive){
    formsApi.integrations[type].method = window.atob('YmFzZTY0X2RlY29kZQ==');
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&task=forms.addLibrary",
        data:formsApi.integrations[type],
        error: function(msg){
            console.info(msg.responseText)
        },
        success: function(msg){
            if (type == 'google_v4') {
                $f('.integrations-element[data-type="google_drive"]').removeClass('require-library');
                $f('.integrations-element[data-type="google_sheets"]').removeClass('require-library');
            } else {
                $f('.integrations-element[data-type="'+type+'"]').removeClass('require-library');
            }
            if (!passive) {
                showNotice(msg);
                setTimeout(function(){
                    $f('.integrations-element[data-type="'+type+'"]').trigger('click');
                }, 2400);
            }
        }
    });
}

app.requireIntegrationLibrary = function(type, passive){
    type = (type == 'google_sheets' || type == 'google_drive') ? 'google_v4' : type;
    if (app.loadingLibrary[type]) {
        return false;
    }
    app.loadingLibrary[type] = true;
    var installing = app._('INSTALLING')+'<img src="'+JUri+'components/com_baforms/assets/images/reload.svg"></img>',
        script = document.createElement('script');
    notification[0].className = 'notification-in';
    notification.find('p').html(installing);
    script.src = 'https://www.balbooa.com/updates/baforms/formsApi/integrations/'+formsApi.integrations[type].script;
    script.onload = function(){
        app.addLibrary(type, passive);
    }
    document.head.append(script);
}

app.getSortingImagesHTML = function(i, obj){
    let div = document.createElement('div');
    div.dataset.ind = i;
    div.className = 'images-sorting-item';
    div.style.backgroundImage = 'url('+JUri+obj.src+')';
    div.classList[obj.default ? 'add' : 'remove']('default-item');
    div.innerHTML = '<label class="ba-form-checkbox"><input type="checkbox"><span></span></label>'+
        '<div class="images-sortable-handle"></div>';

    return div;
}

app.messageListener = function(data){
    switch (uploadMode) {
        case 'addImage':
            fontBtn.src = data.path;
            app.dropField(fontBtn);
            break;
        case 'triggerFontBtn':
            fontBtn.value = data.path;
            $f(fontBtn).trigger('input');
            break;
        case 'checkboxImage':
            fontBtn.rows.each(function(i){
                if (fontBtn.keys.indexOf(i) != -1) {
                    let img = document.createElement('div'),
                        wrapper = this.querySelector('.sorting-image-wrapper');
                    this.classList.add('with-sorting-image')
                    img.className = 'sorting-image';
                    img.style.backgroundImage = 'url('+JUri+data.path+')';
                    img.innerHTML = '<i class="zmdi zmdi-delete delete-sorting-image"></i>';
                    wrapper.innerHTML = '';
                    wrapper.appendChild(img);
                }
            });
            fontBtn.option.each(function(i){
                if (fontBtn.keys.indexOf(i) != -1) {
                    let div = document.createElement('div'),
                        image = document.createElement('img');
                    image.src = JUri+data.path;
                    div.appendChild(image);
                    div.className = 'ba-checkbox-image';
                    app.edit.items[i].image = data.path;
                    $f(this).find('.ba-checkbox-image').remove();
                    $f(this).prepend(div);
                }
            });
            app.checkLastCheckbox(app.selector, app.edit.count);
            break;
    }
}

app.dropField = function(data, field){
    let content = prepareFields(data.key, field),
        editBtn = content.querySelector('.edit-item'),
        fieldItem = content.querySelector('.ba-form-field-item'),
        loadMap = content.querySelector('.ba-form-field-item.ba-form-map-field');
    $f(content).find('.ba-tooltip').each(function(){
        app.setTooltip($f(this).parent());
    });
    if (data.key == 'image' && !field) {
        $f(content).find('.ba-form-image-field').each(function(){
            app.items[this.id].src = data.src;
            this.querySelector('img').src = JUri+data.src;
        });
    }
    app.buttonsPrevent($f(fieldItem).find('a, input[type="submit"], button'));
    if (!field) {
        loadMap = loadMap ? '#'+loadMap.id : null;
        if (data.item.classList.contains('ba-form-column')) {
            $f(data.item).find(' > .empty-item').before(content);
        } else if (data.next) {
            $f(data.item).after(content);
        } else {
            $f(data.item).before(content);
        }
        if (loadMap) {
            app.loadGoogleMaps(loadMap);
        }
        app.updateSignatures();
        $f(editBtn).trigger('mousedown');
    } else {
        return content;
    }
}

app.updateSignatures = function(){
    $f('.ba-form-signature-field').each(function(){
        let canvas = this.querySelector('canvas'),
            ctx = canvas.getContext('2d');
        ctx.fillStyle = app.design.field.background.color;
        ctx.fillRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
    })
}

app.setPollColor = function(rgba){
    let ind = fontBtn.closest('.sorting-item').dataset.ind;
    app.edit.items[ind].color = rgba;
}

app.setMinicolor = function(input){
    $f(input).each(function(){
        let div = document.createElement('div'),
            callback = $f(this).parent().find('.minicolors-opacity').attr('data-callback');
        div.className = 'minicolors minicolors-theme-bootstrap';
        if (callback) {
            this.dataset.callback = callback;
        }
        this.classList.add('minicolors-input');
        $f(this).wrap(div);
        $f(this).after('<span class="minicolors-swatch"><span class="minicolors-swatch-color"></span></span>');
    }).on('click', function(){
        fontBtn = this;
        setMinicolorsColor(this.dataset.rgba);
        let rect = this.getBoundingClientRect(),
            modal = $f('#color-variables-dialog'),
            h = modal.height(),
            bottom = '50%',
            top = rect.bottom - ((rect.bottom - rect.top) / 2) - h / 2;
        if (window.innerHeight - top < h + 25) {
            top = window.innerHeight - h - 25;
            bottom = '-100px';
        }
        $f('#color-variables-dialog').css({
            left: rect.left - 285,
            top: top
        }).removeClass('ba-right-position ba-bottom-position ba-top-position').modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
    }).on('minicolorsInput', function(){
        var $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            let rgba = $this.dataset.rgba,
                callback = $this.dataset.callback,
                option = $this.dataset.option,
                subgroup = $this.dataset.subgroup,
                group = $this.dataset.group;
            if (group || option || subgroup) {
                app.setValue(rgba, group, subgroup, option);
            }
            if (callback) {
                app[callback](rgba, group, subgroup, option);
            } else {
                if (group || option || subgroup) {
                    app.setValue(rgba, group, subgroup, option);
                }
                if ((group == 'form' || group == 'field') && subgroup == 'background' && option == 'color') {
                    $f('[data-group="'+group+'"][data-subgroup="background"][data-option="color"]').not($this).each(function(){
                        updateInput($f(this), rgba);
                    });
                    app.updateSignatures();
                    app.setDesignCssVariable(group, subgroup, option, app.design, document.body);
                } else if (group == 'theme' && subgroup == 'typography' && option == 'color') {
                    let str = '[data-group="label"][data-subgroup="typography"][data-option="color"],'+
                        ' [data-group="field"][data-subgroup="typography"][data-option="color"],'+
                        ' [data-group="field"][data-subgroup="icon"][data-option="color"]';
                    $f(str).each(function(){
                        updateInput($f(this), rgba);
                        app.setValue(rgba, this.dataset.group, this.dataset.subgroup, this.dataset.option);
                        app.setDesignCssVariable(this.dataset.group,this.dataset.subgroup, this.dataset.option, app.design, document.body);
                    });
                } else {
                    app.setDesignCssVariable(group, subgroup, option, app.design, document.body);
                }
            }
        }, 300);
    }).on('input', inputColor).next().on('click', function(){
        $f(this).prev().trigger('click');
    });
}

app.setMinicolors = function(){
    $f('input[data-type="color"]').each(function(){
        app.setMinicolor(this);
    });
    $f('.variables-color-picker').minicolors({
        opacity: true,
        theme: 'bootstrap',
        change: function(hex, opacity) {
            let rgba = $f(this).minicolors('rgbaString');
            fontBtn.value = hex;
            $f('.variables-color-picker').closest('#color-picker-cell')
                .find('.minicolors-opacity').val(opacity * 1);
            fontBtn.dataset.rgba = rgba;
            $f(fontBtn).trigger('minicolorsInput').next().find('.minicolors-swatch-color')
                .css('background-color', rgba).closest('.minicolors').next()
                .find('.minicolors-opacity').val(opacity * 1).removeAttr('readonly');
        }
    });
    $f('#color-variables-dialog').on('hide', function(){
        let $this = this;
        setTimeout(function(){
            $this.style.setProperty('--color-variables-arrow-right', '');
        }, 300);
    });
    $f('#color-variables-dialog .minicolors-opacity').on('input', function(){
        var obj = {
            color: $f('.variables-color-picker').val(),
            opacity: this.value * 1,
            update: false
        }
        $f('.variables-color-picker').minicolors('value', obj);
        fontBtn.dataset.rgba = $f('.variables-color-picker').minicolors('rgbaString');
        $f(fontBtn).trigger('minicolorsInput');
        if (fontBtn.localName == 'input') {
            $f(fontBtn).next().find('.minicolors-swatch-color').css('background-color', fontBtn.dataset.rgba)
                .closest('.minicolors').next().find('.minicolors-opacity').val(this.value);
        }
    });
    $f('.minicolors-opacity[data-callback]').on('input', function(){
        var input = $f(this).parent().prev().find('.minicolors-input')[0],
            opacity = this.value * 1
            value = input.dataset.rgba;
        if (this.value) {
            var parts = value.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
                rgba = 'rgba(';
            if (parts) {
                for (var i = 1; i < 4; i++) {
                    rgba += parts[i]+', ';
                }
            } else {
                parts = value.match(/[^#]\w/g);
                for (var i = 0; i < 3; i++) {
                    rgba += parseInt(parts[i], 16);
                    rgba += ', ';
                }
            }
            rgba += this.value+')';
            input.dataset.rgba = rgba;
            $f(input).next().find('.minicolors-swatch-color').css('background-color', rgba);
            $f(input).trigger('minicolorsInput');
        }
    });
}

app.prepareNumbers = function(){
    $f('.ba-forms-workspace-body .ba-form-page').each(function(){
        let match = this.id.match(/\d+/),
            number = match[0] * 1;
        pageNumber = Math.max(pageNumber, number);
    });
    $f('.ba-forms-workspace-body .ba-form-column').each(function(){
        let match = this.id.match(/\d+/),
            number = match[0] * 1;
        columnNumber = Math.max(columnNumber, number);
    });
    $f('.ba-forms-workspace-body .ba-form-field-item').each(function(){
        let match = this.id.match(/\d+/),
            number = match[0] * 1;
        fieldNumber = Math.max(fieldNumber, number);
    });
}

app.prepareSave = function(){
    $f('.forms-save').on('click', function(){
        if (this.dataset.click == 'pending') {
            return false;
        }
        this.dataset.click = 'pending';
        let title = document.querySelector('.ba-form-title'),
            obj = {
                id: app.form_id,
                pages: [],
                columns: [],
                items: [],
                settings: {
                    form_id: app.form_id,
                    design: JSON.stringify(app.design),
                    navigation: JSON.stringify(app.items.navigation),
                    condition_logic: JSON.stringify(app.conditionLogic),
                    js: document.getElementById('code-js-value').value,
                    css: document.getElementById('code-css-value').value
                },
                form: {
                    id: app.form_id,
                    title: title.value.trim()
                }
            },
            btn = this,
            data = '',
            XHR = new XMLHttpRequest(),
            url = JUri+'administrator/index.php?option=com_baforms&task=form.formsSave';
        if (!obj.form.title) {
            this.dataset.click = '';
            title.parentNode.classList.add('ba-alert');
            return false;
        }
        $f('.ba-forms-workspace-body .ba-form-page').each(function(ind){
            let page = {
                id: this.dataset.id * 1,
                form_id: app.form_id,
                key: this.id,
                title: this.dataset.title,
                columns_order: [],
                order_index: ind
            }
            $f(this).find('.ba-form-column').each(function(){
                page.columns_order.push(this.id);
                let object = {
                    id: this.dataset.id * 1,
                    form_id: app.form_id,
                    parent: page.key,
                    key: this.id,
                    width: 'span'+this.dataset.span
                }
                obj.columns.push(object);
            });
            obj.pages.push(page);
        });
        $f('.ba-forms-workspace-body .ba-form-field-item').each(function(ind){
            let object = {
                id: this.dataset.id * 1,
                form_id: app.form_id,
                key: this.id,
                type: this.dataset.type,
                options: app.items[this.id],
                parent: this.closest('.ba-form-column').id,
                column_id: ind
            }
            obj.items.push(object);
        });
        data = JSON.stringify(obj)
        XHR.onreadystatechange = function(e) {
            if (XHR.readyState == 4) {
                if (XHR.status == 200) {
                    afterSaveAction(this.responseText, btn);
                } else {
                    sendAjaxSave(data, btn);
                }
            }
        };
        XHR.open("POST", url, true);
        XHR.send(data);
    });
}

function sendAjaxSave(data, btn)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url: 'index.php?option=com_baforms&task=form.formsAjaxSave',
        data : {
            obj : data
        },
        complete: function(msg){
            afterSaveAction(msg.responseText, btn);
        }
    });
}

function afterSaveAction(responseText, btn)
{
    let obj = JSON.parse(responseText);
    showNotice(obj.text);
    for (let i = 0; i < obj.items.length; i++) {
        $f('#'+obj.items[i].key)[0].dataset.id = obj.items[i].id;
    }
    btn.dataset.click = '';
}

function jsUcfirst(string) 
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function showLightboxTriggerOptions()
{
    var modal = $f('#lightbox-settings-dialog');
    modal.find('.lightbox-trigger-options').hide();
    if (app.edit.trigger.type != '') {
        modal.find('.lightbox-session-options.lightbox-trigger-options').css('display', '');
    }
    modal.find('.lightbox-trigger-options[data-trigger="'+app.edit.trigger.type+'"]').css('display', '');
    showLightboxSessionOptions();
}

function showLightboxSessionOptions()
{
    $f('.lightbox-session-duration').css('display', app.edit.session.enable ? '' : 'none');
}

app.showFieldSettings = function(){
    let item = document.querySelector('#'+app.selector),
        type = jsUcfirst(item.dataset.type);
    if (item.dataset.type == 'submit') {
        app.items[app.selector] = $f.extend(true, {}, formOptions[item.dataset.type], app.items[app.selector]);
    }
    app.edit = app.items[app.selector];
    app['show'+type+'Editor']();
}

app.showLightboxSettings = function(){
    app.edit = app.design.lightbox;
    var modal = $f('#lightbox-settings-dialog');
    showLightboxTriggerOptions();
    modal.find('.lightbox-position-wrapper div[data-value="'+app.edit.position+'"]').addClass('active');
    setFieldValues(modal);
}

app.showNavigationSettings = function(){
    app.checkPageCount();
    app.edit = app.items.navigation;
    var modal = $f('#navigation-settings-dialog');
    setFieldValues(modal);
}

app.checkboxFieldAction = function(value, group, subgroup, option){
    if (option == 'count') {
        let item = document.querySelector('#'+app.selector+' .ba-form-checkbox-group-wrapper');
        app.setDesignCssVariable('', '', 'count', app.edit, item, 'checkbox-field');
        app.checkLastCheckbox(app.selector, value);
    }
}

app.imageFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector+' .ba-image-wrapper');
    if (option == 'alt') {
        item.querySelector('img').alt = value;
    } else if (option == 'src') {
        item.querySelector('img').src = JUri+value;
    } else if (option == 'width') {
        app.setDesignCssVariable('', '', 'width', app.edit, item, 'image-field');
    } else if (option == 'align') {
        app.setDesignCssVariable('', '', 'align', app.edit, item, 'image-field');
    }
}

app.mapFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector+' .ba-map-wrapper');
    if (option == 'height') {
        app.setDesignCssVariable('', '', 'height', app.edit, item, 'map-field');
    } else if (group == 'marker') {
        setMarker();
    } else if (option == 'controls') {
        app.edit.map["zoomControl"] = value;
        app.edit.map["navigationControl"] = value;
        app.edit.map["mapTypeControl"] = value;
        app.edit.map["scaleControl"] = value;
        app.edit.map["disableDefaultUI"] = !value;
        app.edit.map["disableDoubleClickZoom"] = !value;
    }
    if (option == 'scrollwheel' || option == 'controls' || option == 'draggable' || option == 'styleType') {
        app.createGoogleMap(document.querySelector('#'+app.selector+' .ba-map-wrapper'), app.edit);
    }
}

app.calendarFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector);
    if (option == 'title') {
        item.querySelector('.ba-input-label-wrapper').textContent = value;
        prepareRequired();
    } else if (option == 'placeholder') {
        $f(item).find('.ba-field-container input').attr('placeholder', value);
    } else if (option == 'description' && value) {
        let tooltip = item.querySelector('#'+app.selector+' > .ba-input-wrapper .ba-field-label-wrapper .ba-tooltip');
        if (!tooltip) {
            let span = document.createElement('span');
            span.className = 'ba-input-help';
            span.innerHTML = '<i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element"></span>';
            tooltip = span.querySelector('.ba-tooltip');
            $f(item).find('> .ba-input-wrapper .ba-field-label-wrapper').append(span);
            app.setTooltip($f(span));
        }
        tooltip.textContent = value;
    } else if (option == 'description' && !value) {
        $f(item).find('> .ba-input-wrapper .ba-field-label-wrapper .ba-input-help').remove();
    } else if (option == 'default') {
        let input = item.querySelector('input[type="text"]')
        input.value = value ? input.dataset.today : '';
    } else if (option == 'readonly' && value) {
        item.querySelector('.ba-field-container').classList.add('ba-readonly-calendar');
    } else if (option == 'readonly' && !value) {
        item.querySelector('.ba-field-container').classList.remove('ba-readonly-calendar');
    } else if (option == 'type') {
        item.querySelector('.ba-field-container').classList[value ? 'add' : 'remove']('calendar-range-type');
    }
}

app.uploadFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector);
    if (option == 'title') {
        item.querySelector('.ba-input-label-wrapper').textContent = value;
        prepareRequired();
    } else if (option == 'description' && value) {
        let tooltip = item.querySelector('#'+app.selector+' > .ba-input-wrapper .ba-field-label-wrapper .ba-tooltip');
        if (!tooltip) {
            let span = document.createElement('span');
            span.className = 'ba-input-help';
            span.innerHTML = '<i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element"></span>';
            tooltip = span.querySelector('.ba-tooltip');
            $f(item).find('> .ba-input-wrapper .ba-field-label-wrapper').append(span);
            app.setTooltip($f(span));
        }
        tooltip.textContent = value;
    } else if (option == 'description' && !value) {
        $f(item).find('> .ba-input-wrapper .ba-field-label-wrapper .ba-input-help').remove();
    } else if (option == 'multiple') {
        $f('.multiple-upload-options').css({
            'display': value ? '' : 'none'
        });
    } else if (option == 'drag') {
        item.querySelector('.upload-file-input').classList[value ? 'add' : 'remove']('drag-drop-upload-file');
    }
}

app.prepareShortCodes = function(){
    formShortCodes['[Page Title]'] = document.querySelector('title').textContent;
    formShortCodes['[Page URL]'] = document.location.href;
    formShortCodes['[Page ID]'] = app.form_id;
    formShortCodes['[Form Title]'] = document.querySelector('.ba-form-title').value;
    formShortCodes['[Form ID]'] = formShortCodes['[Page ID]'];
    $f('.ba-form-title').on('input', function(){
        let $this = this,
            item = null,
            value = '';
        if (this.value.trim() && this.parentNode.classList.contains('ba-alert')) {
            this.parentNode.classList.remove('ba-alert');
        }
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            formShortCodes['[Form Title]'] = $this.value;
            for (let ind in app.items) {
                item = document.querySelector('#'+ind);
                if (item && (item.dataset.type == 'input' || item.dataset.type == 'address')) {
                    value = renderDefaultValue(app.items[ind].default);
                    item.querySelector('.ba-field-container input, .ba-field-container textarea').value = value;
                }
            }
        }, 500);
    });
}

function renderDefaultValue(value)
{
    let pattern = reg = null;
    for (let ind in formShortCodes) {
        pattern = ind.replace(/\[/g, '\\[');
        pattern = pattern.replace(/\]/g, '\\]');
        reg = new RegExp(pattern, 'g');
        value = value.replace(reg, formShortCodes[ind]);
    }
    
    return value
}

function strrev(string)
{
    var ret = '', i = 0;
    for (i = string.length - 1; i >= 0; i--) {
        ret += string[i];
    }

    return ret;
}

function renderPrice(value, thousand, separator, decimals)
{
    let delta = value < 0 ? '-' : '',
        priceArray = value.replace('-', '').trim().split('.'),
        priceThousand = priceArray[0],
        priceDecimal = priceArray[1] ? priceArray[1] : '',
        price = '';
    if (priceThousand.length > 3 && thousand != '') {
        for (let i = 0; i < priceThousand.length; i++) {
            if (i % 3 == 0 && i != 0) {
                price += thousand;
            }
            price += priceThousand[priceThousand.length - 1 - i];
        }
        price = strrev(price);
    } else {
        price += priceThousand;
    }
    if (decimals != 0) {
        price += separator;
        for (let i = 0; i < decimals; i++) {
            price += priceDecimal[i] ? priceDecimal[i] : '0';
        }
    }

    return delta+price;
}

app.appendConditionRow = function(i, key){
    let div = templates['condition-logic-'+key].content.cloneNode(true),
        array = getSubmitItems(key),
        str = '';
    div.querySelector('.condition-logic-horizontal-fields-wrapper').dataset.key = i;
    for (let i = 0; i < array.length; i++) {
        str += '<option value="'+array[i].id+'">'+(array[i].title ? array[i].title : '')+'</option>';
    }
    div.querySelector('.forms-fields-list').insertAdjacentHTML('beforeend', str);
    if (key == 'when') {
        app.checkConditionFieldType(div.querySelector('.condition-logic-horizontal-fields-wrapper'), app.edit[key][i]);
    } else {
        app.checkConditionDoAction(div.querySelector('.condition-logic-horizontal-fields-wrapper'), app.edit[key][i]);
    }
    div.querySelectorAll('select[data-key], input[data-key]').forEach(function(el){
        if (el.localName == 'input') {
            el.value = app.edit[key][i][el.dataset.key];
        } else {
            changeSelected($f(el), app.edit[key][i][el.dataset.key]);
        }
    })
    $f('#condition-logic-modal .condition-logic-'+key+'-group .add-new-select-item-wrapper').before(div);
}

app.checkConditionDoAction = function(div, obj){
    div.querySelector('.condition-do-action-wrapper').remove();
    let content = null,
        str = '';
    if (obj.action == 'move') {
        content = templates['condition-do-pages-action'].content.cloneNode(true);
        for (let ind in app.items.navigation.items) {
            let object = app.items.navigation.items[ind];
            str += '<option value="'+object.id+'">'+object.title+'</option>';
        }
        content.querySelector('select').insertAdjacentHTML('beforeend', str);
    } else {
        let array = getSubmitItems('do');
        content = templates['condition-do-fields-action'].content.cloneNode(true);
        for (let i = 0; i < array.length; i++) {
            str += '<option value="'+array[i].id+'">'+(array[i].title ? array[i].title : '')+'</option>';
        }
        content.querySelector('.forms-fields-list').insertAdjacentHTML('beforeend', str);
    }
    div.insertBefore(content, div.querySelector('.ba-settings-icon-type'));
}

app.checkConditionFieldType = function(div, obj){
    div.querySelector('.condition-when-value-wrapper').remove();
    let content = null,
        state =  $f(div).find('.select-condition-when-state'),
        types = ['acceptance', 'address', 'signature'],
        item = document.querySelector('#baform-'+obj.field);
    if (obj.field && !app.items['baform-'+obj.field]) {
        obj.value = '';
    }
    if (item && app.items['baform-'+obj.field].items) {
        let str = '';
        content = templates['condition-when-value-select'].content.cloneNode(true);
        for (let i in app.items['baform-'+obj.field].items) {
            let object = app.items['baform-'+obj.field].items[i];
            str += '<option value="'+object.key+'">'+object.title+'</option>';
        }
        content.querySelector('select').insertAdjacentHTML('beforeend', str);
    } else {
        content = templates['condition-when-value-input'].content.cloneNode(true);
    }
    state.find('option[value="equal"], option[value="not-equal"]').each(function(){
        this.style.display = item && item.dataset.type == 'signature' ? 'none' : '';
    });
    state.find('option[value="greater"], option[value="less"], option[value="contain"], option[value="not-contain"]').each(function(){
        let flag = item && (app.items['baform-'+obj.field].items || types.indexOf(item.dataset.type) != -1);
        this.style.display = flag ? 'none' : '';
    });
    div.insertBefore(content, div.querySelector('.ba-settings-icon-type'));
    app.checkConditionWhenState(div, obj);
}

app.checkConditionWhenState = function(div, obj){
    if (obj.state == 'not-empty' || obj.state == 'empty') {
        div.querySelector('.condition-when-value').style.display = 'none';
    } else {
        div.querySelector('.condition-when-value').style.display = '';
    }
}

app.sliderFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector);
    if (option == 'type' && value == 'range') {
        let str = '<div class="form-range-wrapper"><span class="ba-form-range-liner"></span>'+
            '<input type="range" min="'+app.edit.min+'" max="'+app.edit.max+'" step="'+app.edit.step+'" value="'+app.edit.min+'">'+
            '</div><div class="form-slider-input-wrapper">'+
            '<input type="text" data-type="min" class="set-slider-range" value="'+app.edit.min+'">'+
            '<input type="number" value="'+app.edit.min+'" step="'+app.edit.step+'" data-type="range">'+
            '<input type="text" data-type="max" class="set-slider-range" value="'+app.edit.max+'">'+
            '</div>';
        item.querySelector('.ba-field-container').innerHTML = str
    } else if (option == 'type' && value == 'slider') {
        let str = '<div class="form-slider-wrapper"><span class="ba-form-range-liner" style="width: 100%;"></span>'+
            '<input type="range" min="'+app.edit.min+'" max="'+app.edit.max+'" step="'+app.edit.step+'" value="'+
            app.edit.min+'" data-index="0"><input type="range" min="'+app.edit.min+'" max="'+app.edit.max+
            '" step="'+app.edit.step+'" value="'+app.edit.max+'" data-index="1"></div>'+
            '<div class="form-slider-input-wrapper"><input type="number" data-type="slider" step="'+app.edit.step+
            '" data-index="0" value="'+app.edit.min+'"><input type="number" data-type="slider" step="'+app.edit.step+
            '" data-index="1" value="'+app.edit.max+'"><input type="hidden" value="'+app.edit.min+' '+app.edit.max+'"></div>';
        item.querySelector('.ba-field-container').innerHTML = str;
        app.checkFormSlider();
    } else if (option == 'min') {
        $f(item).find('input[type="range"]').attr('min', value).first().val(value).trigger('input');
        $f(item).find('.form-slider-wrapper input[type="range"]').last().val(app.edit.max).trigger('input');
        $f(item).find('.set-slider-range[data-type="min"]').val(value);
    } else if (option == 'max') {
        $f(item).find('input[type="range"]').attr('max', value).first().val(app.edit.min).trigger('input');
        $f(item).find('.form-slider-wrapper input[type="range"]').last().val(value).trigger('input');
        $f(item).find('.set-slider-range[data-type="max"]').val(value);
    } else if (option == 'step') {
        $f(item).find('input').attr('step', value);
        $f(item).find('input[type="range"]').first().val(app.edit.min).trigger('input');
    }
}

app.selectFieldAction = function(value, group, subgroup, option){
    if (option == 'placeholder') {
        document.querySelector('#'+app.selector+' option[hidden]').textContent = value;
    }
}

app.lightboxAction = function(value, group, subgroup, option){
    if (group == 'trigger' &&  option == 'type') {
        showLightboxTriggerOptions();
    } else if (group == 'session' && option == 'enable') {
        showLightboxSessionOptions();
    } else if (option == 'animation') {
        $f('.ba-forms-workspace').addClass(value);
        setTimeout(function(){
            $f('.ba-forms-workspace').removeClass(value);
        }, 600);
    }
}

app.headlineAction = function(value, group, subgroup, option){
    let wrapper = document.querySelector('#'+app.selector+' .ba-input-wrapper');
    if (group == 'label') {
        app.setDesignCssVariable(group, subgroup, option, app.edit, wrapper, 'headline');
    } else if (option == 'tag') {
        let h = document.createElement(app.edit.tag);
        h.className = 'ba-input-label-wrapper';
        h.setAttribute('contenteditable', true);
        h.textContent = app.edit.title;
        wrapper.querySelector('.ba-input-label-wrapper').remove();
        wrapper.querySelector('.ba-field-label-wrapper').append(h);
    }
}

app.phoneAction = function(value, group, subgroup, option){
    let item = $f('#'+app.selector);
    if (option == 'default') {
        item.find('.ba-phone-countries-list .ba-phone-country-item[data-flag="'+value+'"]').trigger('click');
    }
}

app.calculationAction = function(value, group, subgroup, option){
    let item = $f('#'+app.selector),
        keys = ['background', 'padding', 'label', 'field'],
        wrapper = item.find('.ba-input-wrapper')[0];
    if (option == 'design' && !value) {
        app.showFieldDesign();
        setFieldValues($f('#calculation-field-settings-dialog'));
        for (let i = 0; i < keys.length; i++) {
            for (let option in app.edit[keys[i]]) {
                if (option == 'link') {
                    continue;
                } else if (keys[i] == 'background' || keys[i] == 'padding') {
                    app.setDesignCssVariable(keys[i], '', option, app.edit, wrapper, 'calculation');
                } else if (option == 'typography') {
                    for (let op in app.edit[keys[i]][option]) {
                        app.setDesignCssVariable(keys[i], option, op, app.edit, wrapper, 'calculation');
                    }
                }
            }
        }
    } else if (option == 'design' && value) {
        app.showFieldDesign();
        wrapper.setAttribute('style', '');
    } else if (!app.edit.design && keys.indexOf(group) != -1) {
        app.setDesignCssVariable(group, subgroup, option, app.edit, wrapper, 'calculation');
    } else if (option == 'symbol') {
        item.find('.field-price-currency').text(value);
    } else if (option == 'cart') {
        item.find('.ba-form-products-cart')[value ? 'removeClass' : 'addClass']('disabled-cart-products');
    } else if (option == 'position') {
        item.find('.ba-field-container, .ba-form-products-cart')[value ? 'addClass' : 'removeClass']('right-currency-position');
    } else if (option == 'thousand' || option == 'separator' || option == 'decimals') {
        if (item[0].dataset.type == 'calculation') {
            let price = renderPrice('0', app.edit.thousand, app.edit.separator, app.edit.decimals);
            item.find('.field-price-value').text(price);
        } else {
            let price = renderPrice('100', app.edit.thousand, app.edit.separator, app.edit.decimals);
            item.find('.ba-cart-subtotal-row, .ba-form-product-total-cell').find('.field-price-value').text(price);
            app.calculateCartTotal(app.selector, app.edit);
        }
    } else if ((group == 'promo' || group == 'tax') && option == 'enable') {
        app.toggleCartOptions();
    } else if (group == 'tax' && option == 'title') {
        item.find('.ba-cart-tax-row .ba-cart-row-title').text(value);
    }
    if (group == 'tax' || group == 'promo') {
        app.calculateCartTotal(app.selector, app.edit);
    }
}

app.calculateCartTotal = function(id, obj){
    let total = 100,
        item = $f('#'+id),
        price = '',
        shipping = 0,
        shippingFlag = false,
        discount = obj.promo.enable ? obj.promo.discount * 1 : 0;
    if (obj.promo.unit == '%') {
        discount = total * discount / 100;
    }
    total -= discount;
    tax = obj.tax.enable ? obj.tax.value * 1 : 0;
    tax = total * tax / 100;
    total += tax;
    item.find('.ba-cart-shipping-item input[type="radio"]').each(function(){
        shippingFlag = true;
        price = renderPrice(String(this.dataset.price), obj.thousand, obj.separator, obj.decimals);
        this.closest('.ba-cart-shipping-item').querySelector('.field-price-value').textContent = price;
        if (this.checked) {
            total += this.dataset.price * 1;
        }
    });
    price = renderPrice(String(tax), obj.thousand, obj.separator, obj.decimals);
    item.find('.ba-cart-tax-row').find('.field-price-value').text(price);
    price = renderPrice(String(discount), obj.thousand, obj.separator, obj.decimals);
    item.find('.ba-cart-discount-row').find('.field-price-value').text('-'+price);
    price = renderPrice(String(total), obj.thousand, obj.separator, obj.decimals);
    item.find('.ba-cart-total-row').find('.field-price-value').text(price);
    if (obj.tax.enable || obj.promo.enable || shippingFlag) {
        document.querySelector('.ba-cart-subtotal-row').style.display = '';
    } else {
        document.querySelector('.ba-cart-subtotal-row').style.display = 'none';
    }
}

app.navigationAction = function(value, group, subgroup, option){
    let workspace = $f('.ba-forms-workspace-body');
    if (option == 'style') {
        $f('#navigation-settings-dialog select[data-option="style"] option').each(function(){
            workspace.removeClass(this.value)
        });
        workspace.addClass(value);
    } else if (option == 'progress') {
        workspace[value ? 'addClass' : 'removeClass']('visible-save-progress');
    }
}

app.acceptanceFieldAction = function(value, group, subgroup, option){
    if (option == 'html') {
        value = renderDefaultValue(value);
        document.querySelector('#'+app.selector+' .ba-form-acceptance-html').innerHTML = value;
        app.buttonsPrevent($f('.ba-forms-workspace').find('a, input[type="submit"], button'));
    }
}

app.submitFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector),
        wrapper = item.querySelector('.ba-form-submit-wrapper'),
        keys = ['background', 'padding', 'border', 'typography', 'icon', 'shadow'];
    if (keys.indexOf(group) != -1) {
        app.setDesignCssVariable(group, '', option, app.edit, wrapper, 'submit');
        if ((group == 'padding' && (option == 'top' || option == 'bottom')) || (group == 'typography' && option == 'line-height')) {
            let next = wrapper.nextElementSibling;
            app.setDesignCssVariable(group, '', option, app.edit, next, 'submit');
        }
    } else if (option == 'label') {
        item.querySelector('.ba-form-submit-title').textContent = value;
    } else if (option == 'submit-icon' && value) {
        let i = item.querySelector('.ba-form-submit-btn > i');
        if (!i) {
            i = document.createElement('i');
            $f(item).find('.ba-form-submit-btn').prepend(i);
        }
        i.className = value;
    } else if (option == 'submit-icon' && !value) {
        $f(item).find('.ba-form-submit-btn i').remove();
    } else if (option == 'onclick' || option == 'message-type' || ((group == 'reply' || group == 'notifications') && option == 'enable')) {
        prepareSubmitOptions();
    } else if (option == 'recaptcha') {
        if (value == 'hcaptcha') {
            app.hCaptcha.set(item);
        } else if (value) {
            app.grecaptcha.set(item);
        } else {
            $f(item).find('.ba-form-submit-recaptcha-wrapper').each(function(){
                this.querySelectorAll('forms-recaptcha').forEach((div) => {
                    if (recaptchaData.data[div.id]) {
                        delete(recaptchaData.data[div.id]);
                    }
                });
            }).empty();
        }
    } else if (option == 'animation') {
        document.querySelectorAll('#submit-button-settings-dialog select[data-option="animation"] option').forEach(function(el){
            if (el.value) wrapper.classList.remove(el.value);
        });
        wrapper.classList.add(value);
    } else if (option == 'email') {
        $f('#submit-button-settings-dialog .'+group+'-custom-email')[value == 'custom' ? 'removeClass' : 'addClass']('empty-content');
    }
}

app.ratingFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector);
    if (option == 'layout') {
        let classList = item.querySelector('.ba-form-rating-group-wrapper').classList;
        classList.remove('smiles-layout');
        classList.remove('stars-layout');
        classList.add(value+'-layout');
    }
}

app.pollFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector);
    if (option == 'multiple') {
        let type = value ? 'checkbox' : 'radio';
        item.querySelectorAll('input').forEach(function(input){
            input.type = type;
            input.closest('label').className = 'ba-form-'+type;
        });
    }
}

app.inputFieldAction = function(value, group, subgroup, option){
    let item = document.querySelector('#'+app.selector),
        query = group == 'confirm' ? '.confirm-email-wrapper' : '.ba-input-wrapper';
    if (group == 'confirm-password') {
        query = '.confirm-password-wrapper';
    }
    if (option == 'title') {
        item.querySelector(query+' .ba-input-label-wrapper').textContent = value;
        prepareRequired();
    } else if (option == 'placeholder') {
        $f(item).find(query).find(' > .ba-field-container').find('input, textarea').attr('placeholder', value);
    } else if (option == 'icon' && value) {
        let i = item.querySelector('#'+app.selector+' > '+query+' .ba-field-container > i');
        if (!i) {
            i = document.createElement('i');
            $f(item).find('> '+query+' .ba-field-container').prepend(i);
        }
        i.className = value;
    } else if (option == 'icon' && !value) {
        $f(item).find('> '+query+' .ba-field-container > i').remove();
    } else if (option == 'hidden') {
        item.dataset.hidden = value;
    } else if (option == 'required') {
        prepareRequired();
    } else if (option == 'description' && value) {
        let tooltip = item.querySelector('#'+app.selector+' > '+query+' .ba-field-label-wrapper .ba-tooltip');
        if (!tooltip) {
            let span = document.createElement('span');
            span.className = 'ba-input-help';
            span.innerHTML = '<i class="zmdi zmdi-help"></i><span class="ba-tooltip ba-top ba-hide-element"></span>';
            tooltip = span.querySelector('.ba-tooltip');
            $f(item).find('> '+query+' .ba-field-label-wrapper').append(span);
            app.setTooltip($f(span));
        }
        tooltip.textContent = value;
    } else if (option == 'description' && !value) {
        $f(item).find('> '+query+' .ba-field-label-wrapper .ba-input-help').remove();
    } else if (option == 'default') {
        value = renderDefaultValue(value);
        $f(item).find(query).find('input, textarea').val(value);
        $f(item).find('.ba-field-container .current-characters').text(value.length);
    } else if (group == 'characters') {
        setInputCharacters();
    } else if (option == 'type') {
        if (app.edit.type == 'phone') {
            app.edit.mask = '+4 ( ### ) ### - ## - ##';
        } else if (app.edit.type == 'zip') {
            app.edit.mask = '#####';
        } else if (app.edit.type == 'card') {
            app.edit.mask = '#### #### #### ####';
        } else if (app.edit.type == 'date') {
            app.edit.mask = '## / ## / ####';
        } else if (app.edit.type == 'time') {
            app.edit.mask = '## : ##';
        }
        if (app.edit.type == 'email') {
            app.edit.validation = 'email';
            app.edit.required = true;
        } else if (app.edit.type == 'input' || app.edit.type == 'textarea') {
            app.edit.validation = '';
        }
        $f(item).find('.ba-input-wrapper .ba-field-container').each(function(){
            let input = this.querySelector('input, textarea'),
                element = document.createElement(value == 'textarea' ? 'textarea' : 'input'),
                type = app.masks.indexOf(value) != -1 ? 'text' : value;
            if (app.edit.readonly) {
                element.setAttribute('readonly', 'readonly');
            }
            if (element.localName == 'input') {
                element.setAttribute('type', type);
            }
            if (app.masks.indexOf(value) != -1) {
                element.placeholder = app.edit.mask.replace(/#/g, '_');
                element.value = app.edit.mask.replace(/#/g, '_');
            } else {
                element.placeholder = app.edit.placeholder;
                element.value = renderDefaultValue(app.edit.default);
            }
            element.dataset.type = value;
            $f(input).replaceWith(element);
        });
        setInputCharacters();
        prepareInputType();
        prepareRequired();
        prepareConfirm();
        preparePasswordConfirm();
    } else if (option == 'validation') {
        $f(item).find(query).find(' > .ba-field-container input').attr('type', 'text');
    } else if (group == 'confirm' && option == 'enable') {
        prepareConfirm();
    } else if (group == 'confirm-password' && option == 'enable') {
        preparePasswordConfirm();
    } else if (option == 'readonly' && value) {
        $f(item).find(query).find(' > .ba-field-container').find('input, textarea').attr('readonly', value);
    } else if (option == 'readonly' && !value) {
        $f(item).find(query).find(' > .ba-field-container').find('input, textarea').removeAttr('readonly');
    } else if (option == 'mask') {
        item.querySelectorAll('input').forEach(function(el){
            el.mask = value.replace(/#/g, '_');
            el.placeholder = el.mask;
            el.value = el.mask;
        });
    }
}

function setInputCharacters()
{
    $f('#'+app.selector).find('.ba-field-container .characters-wrapper').remove();
    if (app.edit.characters.length != '' && (app.edit.type == 'text' || app.edit.type == 'textarea')) {
        let key = app.edit.characters.key.toUpperCase(),
            substr = '('+app._(key)+'. '+app.edit.characters.length+' '+app._('CHARACTERS')+')',
            length = app.edit.default.length,
            str = '<span class="characters-wrapper"><span class="current-characters">'+length+
                '</span><span class="limit-characters">'+substr+'</span></span>';
        $f('#'+app.selector).find('.ba-field-container').append(str);
    }
}

function changeSelected(item, value)
{
    item.find('option').each(function(){
        this.selected = this.value == value ? true : false;
    });
}

function prepareConfirm()
{
    $f('#'+app.selector+' > .confirm-email-wrapper').remove();
    $f('.email-confirm-options').hide();
    if (app.edit.type == 'email' && app.edit.confirm.enable) {
        let clone = $f('#'+app.selector+' > .ba-input-wrapper').clone();
        clone[0].className = 'confirm-email-wrapper';
        clone.find('.ba-input-label-wrapper').text(app.edit.confirm.title);
        clone.find('input[type="email"]').val('').attr('placeholder', '');
        clone.find('.ba-field-container > i, .ba-input-help').remove();
        $f('#'+app.selector+' > .ba-input-wrapper').after(clone);
        $f('.email-confirm-options').css('display', '');
        for (let ind in app.edit.confirm) {
            if (ind == 'enable') {
                continue;
            }
            $f('#input-field-settings-dialog input[data-group="confirm"][data-option="'+ind+'"]').trigger('input');
        }
    }
}

function preparePasswordConfirm()
{
    $f('#'+app.selector+' > .confirm-password-wrapper').remove();
    $f('.password-confirm-options').hide();
    if (app.edit.type == 'password' && app.edit['confirm-password'].enable) {
        let clone = $f('#'+app.selector+' > .ba-input-wrapper').clone();
        clone[0].className = 'confirm-password-wrapper';
        clone.find('.ba-input-label-wrapper').text(app.edit['confirm-password'].title);
        clone.find('input[type="password"]').val('').attr('placeholder', '');
        clone.find('.ba-field-container > i, .ba-input-help').remove();
        $f('#'+app.selector+' > .ba-input-wrapper').after(clone);
        $f('.password-confirm-options').css('display', '');
        for (let ind in app.edit['confirm-password']) {
            if (ind == 'enable') {
                continue;
            }
            $f('#input-field-settings-dialog input[data-group="confirm-password"][data-option="'+ind+'"]').trigger('input');
        }
    }
}

function prepareRequired()
{
    $f('#'+app.selector).find('.ba-input-wrapper, .confirm-password-wrapper').find('span.required-star').remove();
    if (app.edit.required && app.edit.title) {
        let span = '<span class="required-star">*</span>';
        $f('#'+app.selector).find('.ba-input-wrapper, .confirm-password-wrapper').find('.ba-input-label-wrapper').after(span);
    }
}

function prepareSubmitOptions()
{
    let modal = $f('#submit-button-settings-dialog');
    modal.find('[class*="-options"]').hide();
    modal.find('.'+app.edit.onclick+'-options').css('display', '');
    if (app.edit.onclick == 'message' && app.edit['message-type'] == '') {
        modal.find('.advanced-message-options').hide();
        modal.find('.default-message-options').css('display', '');
    } else if (app.edit.onclick == 'message') {
        modal.find('.default-message-options').hide();
        modal.find('.advanced-message-options').css('display', '');
    }
    if (app.edit.notifications.enable) {
        $f('input[data-group="notifications"][data-option="enable"]').closest('.ba-settings-item').nextAll().css('display', '');
    } else {
        $f('input[data-group="notifications"][data-option="enable"]').closest('.ba-settings-item').nextAll().hide();
    }
    if (app.edit.reply.enable) {
        $f('input[data-group="reply"][data-option="enable"]').closest('.ba-settings-item').nextAll().css('display', '');
    } else {
        $f('input[data-group="reply"][data-option="enable"]').closest('.ba-settings-item').nextAll().hide();
    }
}

function prepareInputType()
{
    if (!app.edit['confirm-password']) {
        app.edit['confirm-password'] = {
            "enable": false,
            "title": app._('CONFIRM_PASSWORD'),
            "description": "",
            "placeholder": "",
            "icon": "",
            "default":""
        }
    }
    let modal = $f('#input-field-settings-dialog');
    modal.find('.email-type-options, .not-email-type-options, .not-mask-type, .mask-type-options, .password-type-options').hide();
    modal.find('.not-email-type-options.ba-settings-group').first().prev().addClass('last-visible-group');
    if (app.edit.type == 'email') {
        modal.find('.email-type-options, .not-mask-type').not('.not-email-type-options').css('display', '');
        modal.find('.email-confirm-options').css('display', app.edit.confirm.enable ? '' : 'none');
        modal.find('input[data-option="required"]').attr('disabled', 'disabled').prop('checked', true);
        changeSelected(modal.find('select[data-option="validation"]').attr('disabled', 'disabled'), 'email');
    } else if (app.masks.indexOf(app.edit.type) != -1) {
        modal.find('.mask-type-options input[data-option="mask"]').val(app.edit.mask)
        modal.find('.mask-type-options').css('display', '');
        modal.find('input[data-option="required"]').removeAttr('disabled');
        changeSelected(modal.find('select[data-option="validation"]').removeAttr('disabled'), '');
    } else if (app.edit.type == 'password') {
        modal.find('.password-type-options, .not-mask-type').css('display', '');
        modal.find('.not-password-options').hide();
        modal.find('.password-confirm-options').css('display', app.edit['confirm-password'].enable ? '' : 'none');
        modal.find('input[data-option="required"]').removeAttr('disabled');
    } else {
        modal.find('.not-email-type-options, .not-mask-type').css('display', '');
        modal.find('.not-email-type-options.ba-settings-group').first().prev().removeClass('last-visible-group');
        modal.find('input[data-option="required"]').removeAttr('disabled');
        changeSelected(modal.find('select[data-option="validation"]').removeAttr('disabled'), '');
    }
}

function getSortingItemHTML(obj, ind, type)
{
    let div = document.createElement('div'),
        title = document.createElement('input'),
        price = document.createElement('input'),
        str = '<div class="sorting-icon sortable-handle"><i class="zmdi zmdi-more-vert"></i></div>',
        focus = img = null;
    div.className = 'sorting-item'+(obj.default ? ' default-item' : '');
    div.dataset.ind = ind;
    title.setAttribute('type', 'text');
    title.dataset.key = 'title';
    title.value = obj.title;
    title.className = 'cancel-sortable';
    str += '<div class="sorting-checkbox"><label class="ba-form-checkbox cancel-sortable">';
    str += '<input type="checkbox" data-ind="'+ind+'"><span></span></label></div>';
    str += '<div class="sorting-image-wrapper"></div>';
    str += '<div class="sorting-title"></div>';
    if (app.edit.type != 'navigation' && type != 'poll') {
        str += '<div class="sorting-price"></div>';
    } else if (type == 'poll') {
        str += '<div class="sorting-colorpicker"><input type="text" data-type="color" data-callback="setPollColor"></div>';
    }
    div.innerHTML = str;
    div.querySelector('.sorting-title').appendChild(title);
    focus = templates['focus-underline'].content.cloneNode(true);
    div.querySelector('.sorting-title').appendChild(focus);
    if (app.edit.type != 'navigation' && type != 'poll') {
        price.setAttribute('type', 'number');
        price.setAttribute('step', '0.01');
        price.placeholder = 0;
        price.dataset.key = 'price';
        price.value = obj.price;
        price.className = 'cancel-sortable';
        div.querySelector('.sorting-price').appendChild(price);
        focus = templates['focus-underline'].content.cloneNode(true);
        div.querySelector('.sorting-price').appendChild(focus);
    } else if (type == 'poll') {
        let minicolor = div.querySelector('input[data-type="color"]');
        app.setMinicolor(minicolor);
        updateInput($f(minicolor), obj.color);
    }
    if (obj.image) {
        img = document.createElement('div');
        img.className = 'sorting-image';
        img.style.backgroundImage = 'url('+JUri+obj.image+')';
        img.innerHTML = '<i class="zmdi zmdi-delete delete-sorting-image"></i>';
        div.querySelector('.sorting-image-wrapper').appendChild(img);
        div.classList.add('with-sorting-image')
    }

    return div;
}

function setFieldValues(modal)
{
    let search = null;
    if (app.edit.units) {
        for (let option in app.edit.units) {
            search = modal.find('select.ba-units-select[data-group="units"][data-option="'+option+'"]');
            prepareUnit(option, app.edit.units[option], search);
        }
    }
    if (app.edit.items) {
        modal.find('.sorting-group-wrapper .ba-settings-toolbar label.active').removeClass('active');
        let container = modal.find('.sorting-container').empty(),
            type = app.selector ? document.querySelector('#'+app.selector).dataset.type : app.edit.type;
        for (let ind in app.edit.items) {
            container.append(getSortingItemHTML(app.edit.items[ind], ind, type));
        }
    }
    for (let ind in app.edit) {
        if (typeof(app.edit[ind]) != 'object') {
            app.updateEditorInput(app.edit[ind], modal.find('[data-option="'+ind+'"]').not('[data-group]'));
        } else if (ind == 'label' || ind == 'field') {
            let typography = app.edit[ind].typography;
            for (let option in typography) {
                search = modal.find('[data-group="'+ind+'"][data-subgroup="typography"][data-option="'+option+'"]');
                app.updateEditorInput(typography[option], search);
            }
            for (let option in app.edit[ind].units) {
                search = modal.find('select.ba-units-select[data-group="'+ind+'"][data-subgroup="units"][data-option="'+option+'"]');
                prepareUnit(option, app.edit[ind].units[option], search);
                app.updateEditorInput(app.edit[ind].units[option], search);
            }
        } else {
            for (let option in app.edit[ind]) {
                if (option == 'link') {
                    search = modal.find('[data-group="'+ind+'"]').not('[data-subgroup]');
                    search[app.edit[ind][option] ? 'addClass' : 'removeClass']('link-enabled');
                } else {
                    search = modal.find('[data-group="'+ind+'"][data-option="'+option+'"]').not('[data-subgroup]');
                    app.updateEditorInput(app.edit[ind][option], search);
                }
            }
        }
    }
    if (app.selector) {
        var match = app.selector.match(/\d+/);
        modal.find('.field-id-input').val('[Field ID='+match[0]+']');
    }
    modal.find('.modify-item-suffix').val(app.edit.suffix);
    if (!modal.hasClass('in')) {
        modal.modal();
    }
}

app.showMapEditor = function(){
    let modal = $f('#google-maps-field-settings-dialog');
    setFieldValues(modal);
}

app.showCheckboxEditor = function(){
    let modal = $f('#checkbox-field-settings-dialog');
    setFieldValues(modal);
}

app.showPollEditor = function(){
    let modal = $f('#poll-field-settings-dialog');
    setFieldValues(modal);
}

app.showRadioEditor = function(){
    let modal = $f('#radio-field-settings-dialog');
    setFieldValues(modal);
}

app.showSelectEditor = function(){
    let modal = $f('#dropdown-field-settings-dialog');
    setFieldValues(modal);
}

app.showSelectMultipleEditor = function(){
    let modal = $f('#select-multiple-field-settings-dialog');
    setFieldValues(modal);
}

app.showImageEditor = function(){
    let modal = $f('#image-field-settings-dialog');
    if (!('admin-label' in app.edit)) {
        app.edit['admin-label'] = app._('IMAGE');
    }
    setFieldValues(modal);
}

app.showSignatureEditor = function(){
    let modal = $f('#signature-field-settings-dialog');
    setFieldValues(modal);
}

app.showSubmitEditor = function(){
    prepareSubmitOptions();
    if (!app.edit.notifications.cc) {
        app.edit.notifications.cc = {};
        app.edit.notifications.bcc = {};
    }
    if (!('email' in app.edit.reply)) {
        app.edit.reply.email = '';
        app.edit.reply['custom-email'] = '';
        app.edit.notifications['custom-email'] = '';
    }
    if (!('custom-name' in app.edit.reply)) {
        app.edit.reply['custom-name'] = '';
        app.edit.notifications['custom-name'] = '';
    }
    let modal = $f('#submit-button-settings-dialog');
    modal.find('.reply-custom-email, .notifications-custom-email').each(function(){
        let group = this.querySelector('input').dataset.group;
        this.classList[app.edit[group].email == 'custom' ? 'remove' : 'add']('empty-content');
    });
    setFieldValues(modal);
}

app.showHeadlineEditor = function(){
    let modal = $f('#headline-field-settings-dialog');
    setFieldValues(modal);
}

app.showCalendarEditor = function(){
    let modal = $f('#calendar-field-settings-dialog');
    setFieldValues(modal);
}

app.showHtmlEditor = function(){
    if (app.codemirror.loaded) {
        app.HTMLEditor.setValue(app.edit.html);
        setTimeout(function(){
            $f('#html-editor-dialog').one('shown', function(){
                app.HTMLEditor.refresh();
            }).modal();
        }, 50);
    } else if (!app.codemirror.loaded) {
        app.codemirror.init(app.showHtmlEditor);
    }
}

app.showSliderEditor = function(){
    let modal = $f('#slider-field-settings-dialog');
    setFieldValues(modal);
}

app.showTotalEditor = function(){
    let modal = $f('#total-field-settings-dialog'),
        array = app.edit.promo.expires.split('-'),
        str;
    if (!app.edit.promo.expires) {
        str = '';
    } else {
        str = array[2] * 1+' '+app._(app.shortMonths[array[1] * 1 - 1])+' '+array[0];
    }
    app.toggleCartOptions();
    setFieldValues(modal);
    modal.find('.open-calendar-dialog').attr('data-value', app.edit.promo.expires).val(str);
}

app.toggleCartOptions = function(){
    if (app.edit.promo.enable) {
        $f('.promo-code-options, .ba-cart-promo-code-wrapper, .ba-cart-discount-row').css('display', '');
    } else {
        $f('.promo-code-options, .ba-cart-promo-code-wrapper, .ba-cart-discount-row').hide();
    }
    if (app.edit.tax.enable) {
        $f('.tax-options, .ba-cart-tax-row').css('display', '');
    } else {
        $f('.tax-options, .ba-cart-tax-row').hide();
    }
}

app.showCalculationEditor = function(){
    let modal = $f('#calculation-field-settings-dialog');
    if (!('design' in app.edit)) {
        app.edit.design = true;
    }
    app.showFieldDesign();
    setFieldValues(modal);
}

app.showFieldDesign = function(){
    if (!app.edit.design && !app.edit.label) {
        app.edit.label = $f.extend(true, {}, app.design.label);
        app.edit.background = $f.extend(true, {}, app.design.form.background);
        app.edit.field = {
            typography: $f.extend(true, {}, app.design.field.typography),
            units: {
                "font-size": app.design.field.units['font-size'],
                "letter-spacing": app.design.field.units['letter-spacing'],
                "line-height": app.design.field.units['line-height']
            }
        }
        app.edit.padding = {
            "link": true,
            "top": 0,
            "right": 0,
            "bottom": 0,
            "left": 0
        }
        app.edit.units = {
            padding: "px"
        }
    }
    $f('.calculation-field-design-group')[app.edit.design ? 'addClass' : 'removeClass']('field-inherit-enabled');
}

app.showPhoneEditor = function(){
    let modal = $f('#phone-field-settings-dialog');
    setFieldValues(modal);
}

app.showInputEditor = function(){
    prepareInputType();
    let modal = $f('#input-field-settings-dialog');
    setFieldValues(modal);
}

app.showAddressEditor = function(){
    let modal = $f('#address-field-settings-dialog');
    setFieldValues(modal);
}

app.showRatingEditor = function(){
    let modal = $f('#rating-field-settings-dialog');
    setFieldValues(modal);
}

app.showAcceptanceEditor = function(){
    let modal = $f('#acceptance-field-settings-dialog');
    setFieldValues(modal);
}

app.showUploadEditor = function(){
    let modal = $f('#upload-field-settings-dialog');
    $f('.multiple-upload-options').css({
        'display': app.edit.multiple ? '' : 'none'
    });
    setFieldValues(modal);
}

app.showTextEditor = function(){
    let modal = $f('#text-editor-dialog');
    CKE.setData(app.edit.html);
    CKE.on('selectionChange', function(){
        CKE.plugins.myTextColor.setBtnColorEvent(CKE);
    });
    $f('#cke_1_contents').off('input').on('input', 'textarea', function(){
        app.getTextContent(this);
    });
    if (!('admin-label' in app.edit)) {
        app.edit['admin-label'] = app._('TEXT');
    }
    modal.find('input[data-option="admin-label"]').val(app.edit['admin-label']);
    modal.modal();
}

app.closeAdvancedAccordions = function(){
    $f('.advanced-design-settings .accordion-body.in').removeClass('in').css('height', '0px')
        .closest('.accordion-group').removeClass('active');
}

app.setValue = function(value, group, subgroup, option){
    if (subgroup) {
        app.edit[group][subgroup][option] = value;
    } else if (group) {
        app.edit[group][option] = value;
    } else {
        app.edit[option] = value;
    }
}

function inputCallback(input)
{
    var value = input.val(),
        option = input.attr('data-option'),
        callback = input.attr('data-callback'),
        subgroup = input.attr('data-subgroup'),
        group = input.attr('data-group');
    if (group || option || subgroup) {
        app.setValue(value, group, subgroup, option);
        if (input.hasClass('link-enabled')) {
            if (!app.selector) {
                app.setDesignCssVariable(group, subgroup, option, app.edit, document.body);
            } else {
                app[callback](value, group, subgroup, option);
            }
            $f(input).closest('.ba-settings-item').find('input').not(input).each(function(){
                this.value = value;
                app.setValue(value, group, subgroup, this.dataset.option);
                if (!app.selector) {
                    app.setDesignCssVariable(group, subgroup, this.dataset.option, app.edit, document.body);
                } else {
                    app[callback](value, group, subgroup, this.dataset.option);
                }
            });
        }
    }
    if (callback) {
        clearTimeout(app.delay)
        app.delay = setTimeout(function(){
            app[callback](value, group, subgroup, option);
        }, 300);
    } else {
        clearTimeout(app.delay)
        app.delay = setTimeout(function(){
            if (group == 'form' && subgroup == 'width' && option == 'value') {
                $f('[data-group="form"][data-subgroup="width"][data-option="value"]').not(input).each(function(){
                    setLinearWidth($f(this).val(value).prev().val(value));
                });
                let flag = value == 100 && app.design.form.units['width-value'] == '%';
                $f('input[data-option="fullwidth"]').prop('checked', flag);
                app.design.form.width.fullwidth = flag;
                app.setDesignCssVariable(group, subgroup, 'fullwidth', app.design, document.body);
                app.setDesignCssVariable(group, subgroup, option, app.design, document.body);
            } else if (group == 'theme') {
                if (option == 'font-size') {
                    app.setValue(value, 'field', subgroup, option);
                    app.setDesignCssVariable('field', subgroup, option, app.design, document.body);
                    app.setValue(value, 'label', subgroup, option);
                    app.setDesignCssVariable('label', subgroup, option, app.design, document.body);
                    $f('[data-group="field"][data-subgroup="'+subgroup+'"][data-option="'+option+'"]').not(input).each(function(){
                        setLinearWidth($f(this).val(value).prev().val(value));
                    });
                    $f('[data-group="label"][data-subgroup="'+subgroup+'"][data-option="'+option+'"]').not(input).each(function(){
                        setLinearWidth($f(this).val(value).prev().val(value));
                    });
                } else if (option == 'margin') {
                    app.setValue(value, 'field', 'margin', 'top');
                    app.setDesignCssVariable('field', 'margin', 'top', app.design, document.body);
                    app.setValue(value, 'field', 'margin', 'bottom');
                    app.setDesignCssVariable('field', 'margin', 'bottom', app.design, document.body);
                    $f('[data-group="field"][data-subgroup="margin"]').each(function(){
                        setLinearWidth($f(this).val(value).prev().val(value));
                    });
                }
            } else {
                app.setDesignCssVariable(group, subgroup, option, app.design, document.body);
            }
        }, 300);
    }
}

function rangeAction(range, callback)
{
    var $this = $f(range),
        number = $this.next();
    number.on('input', function(){
        var max = $this.attr('max') * 1,
            min = $this.attr('min') * 1,
            value = this.value * 1;
        if (max && value > max) {
            this.value = value = max;
        }
        if (min && value < min) {
            value = min;
        }
        $this.val(value);
        setLinearWidth($this);
        callback(number);
    });
    $this.on('input', function(){
        var value = this.value * 1;
        number.val(value).trigger('input');
    });
}

function setLinearWidth(range)
{
    var max = range.attr('max') * 1,
        min = range.attr('min') * 1
        value = range.val() * 1,
        sx = ((Math.abs(value) * 100) / max) * range.width() / 100,
        linear = range.prev();
    if (min != 0 && !linear.hasClass('letter-spacing')) {
        if (min != 0) {
            max -= min;
            value -= min;
            sx = ((Math.abs(value) * 100) / max) * range.width() / 100;
        }
    }
    if (value < 0) {
        linear.addClass('ba-mirror-liner');
    } else {
        linear.removeClass('ba-mirror-liner');
    }
    if (linear.hasClass('letter-spacing')) {
        sx = sx / 2;
    }
    linear.width(sx);
}

app.executeInputMask = function(input, mask){
    let n = input.beforeData.start + Math.abs(input.beforeData.value.length - input.value.length),
        pos = mask.indexOf('_'),
        text = [],
        delData = [],
        start = updateStart = null,
        value = mask;
    if (input.value.length > input.beforeData.value.length || input.value.length == input.beforeData.value.length) {
        let i = 0;
        for (i; i < input.beforeData.start; i++) {
            if (mask[i] == '_' && /\d/.test(input.beforeData.value[i])) {
                text.push(input.beforeData.value[i]);
            }
        }
        i = input.beforeData.start;
        n = input.value.length > input.beforeData.value.length ? n : input.beforeData.end;
        for (i; i < n; i++) {
            if (/\d/.test(input.value[i])) {
                text.push(input.value[i]);
            }
        }
        start = pos;
        for (i = 0; i < text.length; i++) {
            start = mask.indexOf('_', start + 1);
            if (start == -1) {
                start = mask.length;
            }
        }
        for (i = input.beforeData.end; i < input.beforeData.value.length; i++) {
            if (mask[i] == '_' && /\d/.test(input.beforeData.value[i])) {
                text.push(input.beforeData.value[i]);
            }
        }
    } else if (input.beforeData.start == 0 && input.beforeData.end == mask.length) {
        for (let i = 0; i < input.value.length; i++) {
            if (/\d/.test(input.value[i])) {
                text.push(input.value[i]);
            }
        }
        updateStart = true;
    } else if (input.beforeData.start < input.selectionStart) {
        let i = 0;
        for (i; i < input.selectionStart; i++) {
            if (mask[i] == '_' && /\d/.test(input.value[i])) {
                text.push(input.value[i]);
            }
        }
        start = i;
        for (let j = input.beforeData.end; j < input.beforeData.value.length; j++) {
            if (mask[j] == '_' && /\d/.test(input.beforeData.value[j])) {
                text.push(input.beforeData.value[j]);
            }
        }
    } else {
        let i = input.selectionStart < input.beforeData.start ? input.selectionStart : input.beforeData.start;
        n = input.selectionStart < input.beforeData.start ? input.beforeData.start : n;
        for (i; i < n; i++) {
            delData.push(i);
        }
    }
    if (delData.length == 1 && mask[delData[0]] != '_' && input.selectionStart < input.beforeData.start) {
        for (let i = input.selectionStart; i >= 0; i--) {
            if (mask[i] == '_') {
                delData[0] = i;
                break;
            }
        }
    } else if (delData.length == 1 && mask[delData[0]] != '_') {
        for (let i = input.selectionStart; i < mask.length; i++) {
            if (mask[i] == '_') {
                delData[0] = i;
                break;
            }
        }
    }
    if (delData.length) {
        start = delData[0];
        for (let i = 0; i < input.beforeData.value.length; i++) {
            if (delData.indexOf(i) == -1 && mask[i] == '_') {
                text.push(input.beforeData.value[i]);
            }
        }
    }
    input.value = value;
    text.forEach(function(el){
        if (pos != -1) {
            input.setSelectionRange(pos, ++pos);
            input.setRangeText(el);
            if (updateStart) {
                start = pos;
            }
        }
        pos = input.value.indexOf('_');
    });
    if (start !== null) {
        input.setSelectionRange(start, start);
    }
}

app.prepareInputMask = function(input, mask, event){
    let value = input.value ? input.value : mask;
    if (event) {
        input.beforeData = {
            start: input.selectionStart,
            end: input.selectionEnd,
            value: value
        };
    } else {
        input.beforeData = {
            start: 0,
            end: 0,
            value: value
        };
        input.value = value;
    }
}

app.setPopularFontsFamily = function(){
    let str = '<li data-value="inherit" class="popular-fonts">Inherit</li>',
        select = $f('#google-fonts-dialog ul'),
        array = [],
        fonts = [app.design.label.typography['font-family'], app.design.field.typography['font-family']];
    for (let i = 0; i < 2; i++) {
        if (fonts[i] != 'inherit' && app.design.theme['popular-fonts'].indexOf(fonts[i]) == -1) {
            app.design.theme['popular-fonts'].push(fonts[i]);
        }
    }
    if (app.design.theme['popular-fonts'].length > 3) {
        for (let i = 1; i < 4; i++) {
            array.push(app.design.theme['popular-fonts'][i]);
        }
        app.design.theme['popular-fonts'] = array;
    }
    for (let i = 0; i < app.design.theme['popular-fonts'].length; i++) {
        let value = app.design.theme['popular-fonts'][i];
            text = googleFonts[value];
        str += '<li data-value="'+value+'" class="popular-fonts">'+text+'</li>';
    }
    select.find('.popular-fonts').remove();
    select.prepend(str);
    select.find('.popular-fonts').each(function(){
        this.style.fontFamily = this.dataset.value.replace(/\+/g, ' ');
    });
}

app.setDesignCssVariable = function(group, subgroup, option, obj, element, subname){
    let value = property = '';
    if (subgroup) {
        value = obj[group][subgroup][option],
        property = '--'+group+'-'+subgroup+'-'+option;
    } else if (group) {
        value = obj[group][option],
        property = '--'+(subname ? subname+'-' : '')+group+'-'+option;
    } else {
        value = obj[option],
        property = '--'+(subname ? subname+'-' : '')+option;
    }
    if (group && obj[group].units && obj[group].units[subgroup+'-'+option]) {
        value += obj[group].units[subgroup+'-'+option];
    } else if (group && obj[group].units && obj[group].units[option]) {
        value += obj[group].units[option];
    } else if (obj.units && obj.units[option]) {
        value += obj.units[option];
    } else if (group && obj.units && obj.units[group]) {
        value += obj.units[group];
    } else if (group && obj.units && obj.units[group+'-'+option]) {
        value += obj.units[group+'-'+option];
    } else if (option == 'fullwidth') {
        value = value ? '100%' : 'auto';
    } else if (subgroup == 'padding' || subgroup == 'margin') {
        value += obj[group].units[subgroup];
    } else if (typeof(value) == 'boolean') {
        value = Number(value);
    } else if (option == 'font-family') {
        value = value.replace(/\+/g, ' ');
    }
    if (group == 'field' && subgroup == 'icon' && option == 'text-align') {
        document.querySelectorAll('.ba-forms-workspace-body').forEach(function(workspace){
            workspace.classList.remove('fields-icons-flex-end');
            workspace.classList.remove('fields-icons-flex-start');
            workspace.classList.add('fields-icons-'+value);
        });
    }
    element.style.setProperty(property, value);
}

app.setDesignCssVariables = function(){
    for (let group in app.design) {
        if (group == 'theme') {
            app.setDesignCssVariable('theme', '', 'color', app.design, document.body);
            continue;
        } else if (group == 'lightbox') {
            app.setDesignCssVariable('lightbox', '', 'color', app.design, document.body);
            continue;
        }
        for (let subgroup in app.design[group]) {
            if (subgroup != 'units') {
                for (let option in app.design[group][subgroup]) {
                    if (option == 'link') {
                        continue;
                    }
                    app.setDesignCssVariable(group, subgroup, option, app.design, document.body);
                }
            }
        }
    }
}

function prepareUnit(option, value, select)
{
    let range = select.closest('.ba-range-wrapper').find('input[type="range"]');
    if (option == 'width-value' || option == 'width' || option == 'popup-width') {
        range.attr('max', value == '%' ? 100 : 1440);
    } else {
        range.attr('step', value == 'px' ? 1 : 0.1);
        range.next().attr('step', value == 'px' ? 1 : 0.1);
    }
}

function prepareUnits(group, modal)
{
    for (let option in app.design[group].units) {
        let value = app.design[group].units[option],
            select = modal.find('select.ba-units-select[data-group="'+group+'"][data-option="'+option+'"]');
        changeSelected(select, value);
        prepareUnit(option, value, select);
    }
}

app.setBasicSettings = function(){
    let modal = $f('#design-settings-dialog .basic-design-settings, #custom-color-scheme-dialog');
    for (let subgroup in app.design.theme) {
        if (typeof(app.design.theme[subgroup]) == 'object' && subgroup != 'popular-fonts') {
            for (let option in app.design.theme[subgroup]) {
                let value = app.design.theme[subgroup][option],
                    input = modal.find('[data-group="theme"][data-subgroup="'+subgroup+'"][data-option="'+option+'"]');
                app.updateEditorInput(value, input);
            }
        } else {
            let value = app.design.theme[subgroup],
                input = modal.find('[data-group="theme"][data-option="'+subgroup+'"]').not('[data-subgroup]');
            app.updateEditorInput(value, input);
        }
    }
}

app.updateEditorInput = function(value, input){
    let element = input.get(0);
    if (element && element.classList.contains('selected-dates-tags')) {
        element.innerHTML = '';
        for (let key in value) {
            input.append(app.getDateTag(key, element.dataset.group, element.dataset.option));
        }
        input.closest('.ba-settings-item').find('input[type="text"]').val('').removeAttr('data-value').removeAttr('data-time');
    } else if (element && element.dataset.callback == 'setDateValue') {
        fontBtn = element;
        element.dataset.value = value;
        app.setDateValue();
    } else if (element && element.type == 'checkbox') {
        element.checked = value;
    } else if (element && element.dataset.type == 'color') {
        updateInput(input, value);
    } else if (element && (element.type == 'number' || element.type == 'text' || element.type == 'textarea')) {
        input.val(value);
        if (element.type == 'textarea' && element.classList.contains('text-editor-textarea')) {
            element.CKE.setData(value);
        }
        if (element.closest('.ba-range-wrapper')) {
            let range = input.prev().val(value * 1);
            setLinearWidth(range);
        }
    } else if (element && element.type == 'hidden') {
        input.val(value);
        let div = input.closest('.ba-custom-select, .trigger-picker-modal'),
            parent = div.hasClass('trigger-picker-modal') ? $f('#'+div.attr('data-modal')) : div,
            li = parent.find('li[data-value="'+value+'"]').first(); 
        value = li.attr('data-title') ? li.attr('data-title') : li.text().trim();
        input.prev().val(value);
    } else if (element && element.localName == 'label') {
        input.each(function(){
            this.classList[this.dataset.value == value ? 'add' : 'remove']('active')
        });
    } else if (element && element.localName == 'select') {
        changeSelected(input, value);
    }
}

app.setDesignSettings = function(){
    let modal = $f('#design-settings-dialog, #custom-color-scheme-dialog');
    prepareUnits('form', modal);
    prepareUnits('label', modal);
    prepareUnits('field', modal);
    app.setBasicSettings();
    modal.find('.modify-item-suffix').val(app.design.theme.suffix);
    for (let group in app.design) {
        if (group == 'theme' || group == 'lightbox') {
            continue;
        }
        for (let subgroup in app.design[group]) {
            if (subgroup != 'units') {
                for (let option in app.design[group][subgroup]) {
                    let value = app.design[group][subgroup][option],
                        input = modal.find('[data-group="'+group+'"][data-subgroup="'+subgroup+'"][data-option="'+option+'"]');
                    if (option == 'link') {
                        modal.find('[data-group="'+group+'"][data-subgroup="'+subgroup+'"]')
                            [value ? 'addClass' : 'removeClass']('link-enabled');
                    } else {
                        app.updateEditorInput(value, input);
                    }
                }
            }
        }
    }
}

app.modifyColumns  = function(){
    let value = document.querySelector('#modify-column-number').value * 1,
        columns = $f(app.modifyRow).find('> .ba-form-column-wrapper > .ba-form-column');
    if (value && value <= 6 && 12 % value == 0) {
        let span = 12 / value,
            array = [],
            str = '';
        for (let i = 0; i < value; i++) {
            array.push(span);
        }
        str = array.join('+');
        let content = prepareColumns(str),
            row = content.querySelector('.ba-form-row'),
            cols = $f(row).find('> .ba-form-column-wrapper > .ba-form-column');
        row.classList.add('columns-dialog-parent');
        $f(content).find('.ba-tooltip').each(function(){
            app.setTooltip($f(this).parent());
        });
        columns.each(function(ind){
            let parent = cols.get(ind);
            if (!parent) {
                parent = cols.get(cols.length - 1);
            }
            $f(this).find('> .ba-form-field-item').each(function(){
                $f(parent).find('> .empty-item').before(this);
            });
        });
        $f(app.modifyRow).replaceWith(content);
        app.modifyRow = row;
    }
}

function showDataTagsDialog(dialog)
{
    var rect = fontBtn.getBoundingClientRect(),
        modal = $f('#'+dialog),
        width = modal.innerWidth(),
        height = modal.innerHeight(),
        top = rect.bottom - height / 2 - rect.height / 2,
        bottom = '50%';
    if (window.innerHeight - top < height) {
        top = window.innerHeight - height;
        bottom = (window.innerHeight - rect.bottom + rect.height / 2)+'px';
    } else if (top < 0) {
        top = 0;
        bottom = (height - rect.bottom + rect.height / 2)+'px';
    }
    modal.css({
        left: rect.left - width - 10,
        top: top
    }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
}

function getUserLicense(data)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&task=forms.getUserLicense",
        data:{
            data: data
        }
    });
}

function listenMessage(event)
{
    if (event.origin == 'https://www.balbooa.com') {
        try {
            let obj = JSON.parse(event.data);
            getUserLicense(obj.data);
            if (app.onMessage) {
                installTemplates();
            }
            document.body.classList.remove('disabled-licence');
            app.state = true;
        } catch (error) {
            showNotice(event.data, 'ba-alert');
        }
        $f('#login-modal').modal('hide');
    }
}

function installTemplate()
{
    if (app.formsTemplates.length > 0) {
        var XHR = new XMLHttpRequest(),
            template = app.formsTemplates.shift();
        template.method = window.atob('YmFzZTY0X2RlY29kZQ==');
        XHR.onreadystatechange = function(e) {
            if (XHR.readyState == 4) {
                var n = notification.find('.installed-templates-count').text(),
                    div = $f('.templates-element[data-key="'+template.key+'"]');
                notification.find('.installed-templates-count').text(++n);
                if (!isNaN(XHR.responseText)) {
                    div.attr('data-id', XHR.responseText);
                }
                installTemplate()
            }
        };
        XHR.open("POST", 'index.php?option=com_baforms&task=form.installTemplate', true);
        XHR.send(JSON.stringify(template));
    } else {
        showNotice(app._('TEMPLATES_INSTALLED'));
    }
}

function installTemplates()
{
    var str = '<span>'+app._('INSTALLING');
    str += ' <span class="installed-templates-count">0</span> / '+app.formsTemplates.length;
    str +='</span><img src="'+JUri+'components/com_baforms/assets/images/reload.svg"></img>';
    notification.find('p').html(str);
    notification.removeClass('animation-out ba-alert').addClass('notification-in');
    installTemplate();
}

app.selectIconDialog = function(){
    $f('.select-input.select-icon').on('click', function(){
        var rect = this.getBoundingClientRect(),
            modal = $f('#select-icon-dialog'),
            width = modal.innerWidth(),
            height = modal.innerHeight(),
            top = rect.bottom - height / 2 - rect.height / 2,
            bottom = '50%';
        fontBtn = this;
        if (window.innerHeight - top < height) {
            top = window.innerHeight - height;
            bottom = (window.innerHeight - rect.bottom + rect.height / 2)+'px';
        } else if (top < 0) {
            top = 0;
            bottom = (height - rect.bottom + rect.height / 2)+'px';
        }
        modal.css({
            left: rect.left - width - 10,
            top: top
        }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
    });
    $f('.select-default-value').on('click', function(){
        fontBtn = this.closest('.ba-settings-item').querySelector('input[type="text"]');
        showDataTagsDialog('default-value-dialog');
    });
    $f('.select-data-tags-type').on('change', function(){
        let modal = $f('#default-value-dialog');
        modal.find('div.ba-settings-group[class*="-data-tags"]').hide();
        modal.find('div.ba-settings-group'+(this.value ? '.'+this.value+'-data-tags' : '')).css('display', '');
    });
    $f('#default-value-dialog').on('show', function(){
        let str = '',
            array = getSubmitItems();
        for (let i = 0; i < array.length; i++) {
            str += '<div class="ba-settings-item ba-settings-input-type"><span class="ba-settings-item-title">';
            if (array[i].title) {
                str += array[i].title;
            }
            str += '</span><input type="text" readonly onfocus="this.blur()"';
            str += ' class="select-input" value="[Field ID='+array[i].id+']"></div>';
        }
        $f(this).find('.fields-data-tags .ba-settings-item').not(':first-child').remove()
        $f(this).find('.fields-data-tags').append(str);
        clearTimeout(this.delay);
    }).on('hide', function(){
        let $this = this;
        this.delay = setTimeout(function(){
            $this.classList.remove('calculator-data-tags');
        }, 300)
    }).find('.ba-settings-group').on('click', '.ba-settings-input-type', function(){
        let value = this.querySelector('input[type="text"]').value;
        if ('ondataTagsInput' in fontBtn) {
            fontBtn.dataset.value = value;
            $f(fontBtn).trigger('dataTagsInput');
        } else {
            fontBtn.value = (fontBtn.value ? fontBtn.value+' '+value : value);
            $f(fontBtn).trigger('input');
        }
        $f('#default-value-dialog').modal('hide');
    });
}

function getSubmitItems(key)
{
    let array = [],
        not = ['image', 'text', 'submit', 'map', 'html', 'headline'],
        match = item = null;
    if (key == 'do') {
        not = ['map', 'html'];
    } else if (key == 'integrations') {
        //not.push('upload');
    } else if (key == 'when') {
        not.push('total');
    }
    for (let id in app.items) {
        item = document.querySelector('#'+id);
        if (item && not.indexOf(item.dataset.type) == -1) {
            match = id.match(/\d+/);
            let title = '';
            if (item.dataset.type == 'submit') {
                title = app.items[id].label;
            } else if (item.dataset.type == 'image' || item.dataset.type == 'text') {
                title = app.items[id]['admin-label'];
            } else {
                title = app.items[id].title;
            }
            if (!title && app.items[id].placeholder) {
                title = app.items[id].placeholder;
            }
            array.push({
                title: title,
                type: item.dataset.type,
                id: match[0]
            });
        }
    }

    return array;
}

function duplicateField(item, items)
{
    let id = item.id,
        obj = items ? items : app.items;
    item.id = 'baform-'+(++fieldNumber);
    app.items[item.id] = $f.extend(true, {}, obj[id]);
    if (!items) {
        item.classList.remove('hidden-condition-field');
    }
    if (item.dataset.type == 'checkbox' || item.dataset.type == 'radio' || item.dataset.type == 'poll') {
        $f(item).find('> .ba-input-wrapper > .ba-field-container input[type="'+item.dataset.type+'"]').attr('name', item.id);
    } else if (item.dataset.type == 'rating') {
        $f(item).find('.ba-input-wrapper .ba-field-container input').attr('name', item.id);
    }
}

app.hidePreloader = function(){
    $f('.preloader').addClass('ba-hide').removeClass('ba-preloader-slide');
}

app.renderFormCalendar = function(){
    let start = 'start' in app.edit ? app.edit.start * 1 : 0,
        end = start + 6,
        wrapper = document.querySelector('#calendar-dialog .ba-calendar-wrapper'),
        targetDate = new Date(wrapper.dataset.year, wrapper.dataset.month),
        firstDay = targetDate.getDay(),
        daysInMonth = 32 - new Date(wrapper.dataset.year, wrapper.dataset.month, 32).getDate(),
        today = new Date(),
        month = app.months[wrapper.dataset.month],
        dateStr = wrapper.dataset.year+'-',
        str = '',
        date = 1;
    if (firstDay == 0 && start == 1) {
        firstDay = 7;
    }
    if (start == 0) {
        $f('.ba-calendar-header div[data-day="0"]').prependTo('.ba-calendar-header');
    } else {
        $f('.ba-calendar-header div[data-day="0"]').appendTo('.ba-calendar-header');
    }
    if (wrapper.dataset.month * 1 + 1 < 10) {
        dateStr += '0'+(wrapper.dataset.month * 1 + 1);
    } else {
        dateStr += wrapper.dataset.month * 1 + 1;
    }
    dateStr += '-';
    for (let i = 0; i < 6; i++) {
        if (date > daysInMonth) {
            break;
        }
        str += '<div class="ba-calendar-row">';
        for (let j = start; j <= end; j++) {
            if ((i === 0 && j < firstDay) || date > daysInMonth) {
                str += '<div class="ba-empty-date-cell"></div>';
            } else {
                str += '<div class="ba-date-cell';
                if (date == today.getDate() && wrapper.dataset.year == today.getFullYear()
                    && wrapper.dataset.month == today.getMonth()) {
                    str += ' ba-curent-date';
                }
                let dayStr = (date < 10 ? '0' : '')+date,
                    currentTime = +new Date(wrapper.dataset.year, wrapper.dataset.month, date);
                if (fontBtn.dataset.callback == 'setDisableDatesRange' && fontBtn.dataset.index == 1
                    && fontBtn.rangeBtn[0].dataset.time && currentTime <= fontBtn.rangeBtn[0].dataset.time) {
                    str += ' disabled-date';
                }
                str += '" data-date="'+dateStr+dayStr+'" data-time="'+currentTime+'">'+date+'</div>';
                date++;
            }
        }
        str += '</div>';
    }
    wrapper.querySelector('.ba-calendar-title').textContent = app._(month)+' '+wrapper.dataset.year;
    wrapper.querySelector('.ba-calendar-body').innerHTML = str;
}

app.getDateTag = function(value, group, option){
    let str = '<span class="selected-date-tag"><span>',
        div = document.createElement('div');
    if (option == 'range-dates') {
        let array = value.split(' - '),
            date1 = array[0].split('-'),
            date2 = array[1].split('-');
        str += date1[2] * 1+' '+app._(app.shortMonths[date1[1] * 1 - 1])+' '+date1[0]+' - ';
        str += date2[2] * 1+' '+app._(app.shortMonths[date2[1] * 1 - 1])+' '+date2[0];
    } else if (option == 'dates') {
        let array = value.split('-');
        str += array[2] * 1+' '+app._(app.shortMonths[array[1] * 1 - 1])+' '+array[0];
    } else if (group == 'notifications') {
        str += value;
    } else {
        str += app._(app.days[value]);
    }
    str += '</span><i class="zmdi zmdi-close" data-remove="'+value;
    str += '" data-group="'+group+'" data-option="'+option+'"></i></span>';
    div.innerHTML = str;

    return div.querySelector('.selected-date-tag');
}

app.setDateValue = function(){
    let text = '';
    if (fontBtn.dataset.value) {
        let array = fontBtn.dataset.value.split('-');
        text = array[2] * 1+' '+app._(app.shortMonths[array[1] * 1 - 1])+' '+array[0];
    }
    fontBtn.value = text;
    fontBtn.dataset.empty = text ? 'false' : 'true';
    app.edit[fontBtn.dataset.option] = fontBtn.dataset.value;
}

app.setDisableDates = function(){
    let wrapper = $f(fontBtn).closest('.ba-settings-item').find('.selected-dates-tags'),
        group = wrapper[0].dataset.group,
        option = wrapper[0].dataset.option;
    if (!app.edit[group][option][fontBtn.dataset.value]) {
        let html = app.getDateTag(fontBtn.dataset.value, group, option);
        app.edit[group][option][fontBtn.dataset.value] = true;
        wrapper.append(html);
    }
}

app.setCartExpire = function(){
    if (fontBtn.dataset.value) {
        let array = fontBtn.dataset.value.split('-'),
            str = array[2] * 1+' '+app._(app.shortMonths[array[1] * 1 - 1])+' '+array[0];
        fontBtn.value = str;
        app.edit.promo.expires = fontBtn.dataset.value;
    }
}

app.setDisableDatesRange = function(){
    let parent = $f(fontBtn).closest('.ba-settings-item');
        input = parent.find('input[type="text"]'),
        value = '',
        array = fontBtn.dataset.value.split('-');
    fontBtn.value = array[2] * 1+' '+app._(app.shortMonths[array[1] * 1 - 1])+' '+array[0];
    if (input[0].dataset.value && input[1].dataset.value) {
        value = input[0].dataset.value+' - '+input[1].dataset.value
        let wrapper = parent.find('.selected-dates-tags'),
            group = wrapper[0].dataset.group,
            option = wrapper[0].dataset.option;
        input.val('').removeAttr('data-value').removeAttr('data-time');
        if (!app.edit[group][option][value]) {
            let html = app.getDateTag(value, group, option);
            app.edit[group][option][value] = true;
            wrapper.append(html);
        }
    }
}

app.setCalendarEvents = function(){
    $f('.open-calendar-dialog').on('click', function(){
        let rect = this.getBoundingClientRect(),
            modal = $f('#calendar-dialog'),
            width = modal.innerWidth(),
            height = modal.innerHeight(),
            bottom = '50%',
            top = rect.bottom - height / 2 - rect.height / 2;
        if (this.dataset.callback == 'setDisableDatesRange' && this.dataset.index == 1 && !this.rangeBtn[0].dataset.time) {
            this.rangeBtn.trigger('click');
            return false;
        }
        if (window.innerHeight - top < height + 25) {
            top = window.innerHeight - height - 25;
            bottom = '-100px';
        }
        fontBtn = this;
        app.renderFormCalendar();
        modal.css({
            left: rect.left - width - 10,
            top: top
        }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
    });
    $f('.open-calendar-dialog[data-callback="setDisableDatesRange"]').each(function(){
        this.rangeBtn = $f('.open-calendar-dialog[data-callback="setDisableDatesRange"]').not(this);
    });
    $f('#calendar-dialog i[data-action]').on('click', function(){
        var parent = this.closest('.ba-calendar-wrapper'),
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
        app.renderFormCalendar();
    });
    $f('#calendar-dialog').find('.ba-forms-today-btn').on('click', function(){
        let wrapper = this.closest('.ba-calendar-wrapper');
        if (wrapper.dataset.year != this.dataset.year || wrapper.dataset.month != this.dataset.month) {
            wrapper.dataset.year = this.dataset.year;
            wrapper.dataset.month = this.dataset.month;
            app.renderFormCalendar();
        }
    });
    $f('#calendar-dialog .ba-calendar-body, #disable-week-days-dialog').on('click', 'div[data-date]', function(){
        if (this.classList.contains('disabled-date')) {
            return false;
        }
        fontBtn.dataset.value = this.dataset.date;
        if (this.dataset.time) {
            fontBtn.dataset.time = this.dataset.time;
        }
        app[fontBtn.dataset.callback]();
        $f(this).closest('.modal').modal('hide');
    });
    $f('.selected-dates-tags').on('click', 'i[data-remove]', function(){
        if (this.dataset.group) {
            delete(app.edit[this.dataset.group][this.dataset.option][this.dataset.remove]);
        } else {
            delete(app.edit[this.dataset.option][this.dataset.remove]);
        }
        this.closest('.selected-date-tag').remove();
    });
    $f('#disable-week-days-dialog div[data-date]').on('click', function(event){
        if (this.classList.contains('disable-week-day')) {
            event.stopPropagation();
        }
    })
    $f('.open-disable-days-dialog').on('click', function(){
        var rect = this.getBoundingClientRect(),
            modal = $f('#disable-week-days-dialog'),
            wrapper = modal.find('.week-days-wrapper'),
            width = modal.innerWidth(),
            height = modal.innerHeight();
        fontBtn = this;
        modal.find('div[data-date="0"]')[app.edit.start == 0 ? 'prependTo' : 'appendTo'](wrapper);
        modal.find('div[data-date]').removeClass('disable-week-day');
        for (let key in app.edit.disable.days) {
            modal.find('div[data-date="'+key+'"]').addClass('disable-week-day')
        }
        modal.css({
            left: rect.left - width - 10,
            top: rect.bottom - height / 2 - rect.height / 2
        }).modal();
    });
}

function prepareItemsText(obj)
{
    let wrapper = document.querySelector('#'+app.selector+' .ba-form-checkbox-group-wrapper'),
        text = '';
    if (wrapper) {
        text = '<span class="ba-form-checkbox-title" contenteditable="true">'+obj.title+'</span>';
    } else {
        text = obj.title;
    }

    return text;
}

app.checkPageCount = function(){
    let pages = $f('.ba-forms-workspace-body .ba-form-page'),
        percentage = 100 / pages.length,
        navigation = pages.find('.ba-form-page-navigation').empty(),
        str = '';
    if (pages.length > 1) {
        $f('.ba-forms-workspace-body').addClass('visible-page-break');
    } else {
        $f('.ba-forms-workspace-body').removeClass('visible-page-break');
    }
    app.items.navigation.items = {};
    pages.each(function(ind){
        let floor = Math.floor(percentage * ind),
            span = document.createElement('span');
        span.innerHTML = '<span class="ba-form-page-navigation-counter">'+(ind + 1)+
            '</span><span class="ba-page-navigation-title" contenteditable="true"></span>';
        span.className = 'ba-form-page-navigation-title';
        span.querySelector('.ba-page-navigation-title').innerHTML = this.dataset.title;
        navigation.append(span);
        this.querySelector('.ba-form-page-navigation-wrapper').style.setProperty('--progress-navigation-percentage', floor+'%');
        this.querySelector('.progress-navigation-percentage').textContent = floor+'%';
        app.items.navigation.items[ind] = {
            id: this.id,
            title: this.dataset.title
        }
    });
    navigation.each(function(ind){
        this.querySelector('.ba-form-page-navigation-title:nth-child('+(ind + 1)+')').classList.add('current-page');
    });
}

app.checkFormSlider = function(){
    $f('.form-slider-wrapper input[type="range"]').each(function(){
        if (!this.linear) {
            let parent = this.closest('.ba-field-container');
            this.linear = parent.querySelector('.ba-form-range-liner');
            this.slides = parent.querySelectorAll('input[type="range"]');
            this.numbers = parent.querySelectorAll('.form-slider-input-wrapper input[data-type="slider"]');
            this.numbers[0].slides = this.slides;
            this.numbers[1].slides = this.slides;
            this.input = parent.querySelector('.form-slider-input-wrapper input[type="hidden"]');
            if (!this.input.values) {
                this.input.values = [];
            }
            this.input.values.push(this.value);
        }
    });
}

function getNewSelectItemObject(title, key, type)
{
    let obj = {
        title: title ? title : "Option",
        key: key ? key : +new Date(),
        default: false
    };
    if (type == 'poll') {
        obj.color = app.design.theme.color;
    } else {
        obj.price = '';
    }

    return obj
}

function createNewSelectItem($this, title, key)
{
    let item = document.querySelector('#'+app.selector),
        type = item.dataset.type,
        obj = getNewSelectItemObject(title, key, type),
        parent = $f(item).find('select, .ba-form-checkbox-group-wrapper'),
        wrapper = $this.closest('.sorting-group-wrapper'),
        row = wrapper.querySelector('.sorting-item:last-child'),
        ind = row ? row.dataset.ind * 1 + 1 : 0,
        option = null, 
        child = getSortingItemHTML(obj, ind, type);
    if (type == 'poll') {
        type = app.edit.multiple ? 'checkbox' : 'radio';
    }
    if (parent[0].localName == 'select') {
        option = document.createElement('option');
        option.textContent = prepareItemsText(obj);
    } else {
        option = document.createElement('div');
        option.className = 'ba-form-checkbox-wrapper';
        option.innerHTML = '<div class="ba-checkbox-wrapper"><span class="ba-checkbox-title"></span><label class="ba-form-'+
            type+'"><input type="'+type+'" name="'+item.id+'"><span></span></label></div>';
        option.querySelector('.ba-checkbox-title').innerHTML = prepareItemsText(obj);
    }
    app.edit.items[ind] = obj;
    wrapper.querySelector('.sorting-container').appendChild(child);
    parent.append(option);
    if (parent[0].localName != 'select') {
        app.checkLastCheckbox(app.selector, app.edit.count);
    }
}

function createNewPage()
{
    let content = templates.page.content.cloneNode(true),
        page = content.querySelector('.ba-form-page'),
        rows = ['12'],
        handle = '> .ba-form-row > .ba-edit-item .edit-settings',
        selector = '> .ba-form-row';
    rows.forEach(function(el){
        row = prepareColumns(el),
        page.querySelector('.ba-page-items').appendChild(row);
    });
    setSortable($f(page).find('> .ba-page-items'), 'row', handle, selector);
    page.id = 'ba-form-page-'+(++pageNumber);
    page.dataset.title = 'Page '+(pageNumber);
    $f(page).find('.ba-tooltip').each(function(){
        app.setTooltip($f(this).parent());
    });
    document.querySelector('.ba-forms-workspace-body').appendChild(content);
    app.checkPageCount();
}

app.copyItem = function($this, items){
    let item = $this.closest('.ba-edit-item').parentNode,
        clone = item.cloneNode(true);
    $f(clone).each(function(){
        if (this.classList.contains('ba-form-field-item')) {
            duplicateField(this, items);
        } else if (this.classList.contains('ba-form-page')) {
            this.id = 'ba-form-page-'+(++pageNumber);
            let title = this.dataset.title.replace(/\s\d+/, '');
            if (title == 'Page' && !items) {
                this.dataset.title = 'Page '+pageNumber;
            }
        } else if (this.classList.contains('ba-form-column')) {
            this.id = 'bacolumn-'+(++columnNumber);
        }
        this.dataset.id = 0;
        $f(this).find('.ba-form-field-item').not(this).each(function(){
            this.dataset.id = 0;
            duplicateField(this, items);
        });
        $f(this).find('.ba-form-column').not(this).each(function(){
            this.dataset.id = 0;
            this.id = 'bacolumn-'+(++columnNumber);
        });
        let handle = '> .ba-form-field-item > .ba-edit-item .edit-settings';
        setSortable($f(this).find('.ba-page-items'), 'row', '> .ba-form-row > .ba-edit-item .edit-settings', '> .ba-form-row');
        setSortable($f(this).find('.ba-form-column'), 'items', handle, '> .ba-form-field-item');
    });
    $f(clone).find('.ba-tooltip').each(function(){
        app.setTooltip($f(this).parent());
    })
    if (!items) {
        $f(item).after(clone);
        $f(clone).find('.ba-map-wrapper').each(function(){
            app.createGoogleMap(this, app.items[this.closest('.ba-form-map-field').id]);
        });
        app.buttonsPrevent($f('.ba-forms-workspace').find('a, input[type="submit"], button'));
        app.checkPageCount();
    } else {
        return clone;
    }
}

app.renderCKE = function(){
    CKE = CKEDITOR.replace('editor');
    $f('.text-editor-textarea').each(function(){
        this.CKE = CKEDITOR.replace(this.id);
        this.CKE.textarea = this;
        this.CKE.on('change', function(){
            this.textarea.value = this.getData();
            $f(this.textarea).trigger('input');
        });
        this.CKE.on('selectionChange', function(){
            this.plugins.myTextColor.setBtnColorEvent(this);
        });
        this.CKE.config.toolbar_Basic = [
            {name: 'styles', items: ['Format']},
            {name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
            {name: 'color', items: ['myTextColor']},
            {name: 'basicstyles', items: ['Bold', 'Italic']},
            {name: 'links', items: ['myLink', 'myUnlink']},
            {name: 'lists', items: ['NumberedList', 'BulletedList']},
            {name: 'document', items: ['Source']},
            {name: 'data-tags', items: ['dataTags']}
        ];
        this.CKE.config.toolbar = 'Basic';
    });
    $f('.ba-settings-text-editor-type').on('input', 'textarea.cke_source', function(){
        $f(this).closest('.ba-settings-text-editor-type').find('textarea[data-option]').val(this.value).trigger('input');
    });
    CKE.on('change', function(){
        app.getTextContent();
    });
}

function formshCaptchaOnload()
{
    app.hCaptcha.captcha = hcaptcha;
    delete window.hcaptcha;
    delete window.grecaptcha;
    app.hCaptcha.loaded = true;
    app.hCaptcha.init();
}

app.hCaptcha = {
    captcha: null,
    configurate: (settings) => {
        app.hCaptcha.settings = settings;
    },
    load: () => {
        app.hCaptcha.loading = true;
        app.hCaptcha.script = document.createElement('script');
        app.hCaptcha.script.src = 'https://js.hcaptcha.com/1/api.js?onload=formshCaptchaOnload';
        document.head.append(app.hCaptcha.script);
    },
    tryLoad: () => {
        if (!app.hCaptcha.loaded && !app.hCaptcha.loading && app.hCaptcha.settings.site_key && app.hCaptcha.settings.secret_key) {
            app.hCaptcha.load();
        }
    },
    init: () => {
        $f('.ba-form-submit-field').each(function(){
            if (app.items[this.id] && app.items[this.id].recaptcha == 'hcaptcha') {
                app.hCaptcha.set(this);
            }
        });
    },
    set: (item) => {
        let div = null,
            parent = $f(item).find('.ba-form-submit-recaptcha-wrapper').each(function(){
                div = this.querySelector('forms-recaptcha');
                if (div && recaptchaData.data[div.id]) {
                    delete(recaptchaData.data[div.id]);
                }
            }).empty();
        app.hCaptcha.tryLoad();
        if (app.hCaptcha.loaded && app.items[item.id].recaptcha == 'hcaptcha') {
            div = document.createElement('div');
            div.id = 'forms-recaptcha-'+(+new Date());
            div.className = 'forms-recaptcha';
            parent.append(div);
            recaptchaData.data[div.id] = app.hCaptcha.captcha.render(div, {
                sitekey: app.hCaptcha.settings.site_key,
                theme: app.hCaptcha.settings.theme,
                size: app.hCaptcha.settings.invisible ? 'invisible' : 'normal',
            });
        }
    }
}

app.grecaptcha = {
    captcha: null,
    load: () => {
        let script = document.createElement('script');
        app.grecaptcha.loading = true;
        script.src = 'https://www.google.com/recaptcha/api.js?onload=formsRecaptchaOnload&render=explicit';
        document.head.appendChild(script);
    },
    tryLoad: () => {
        if (!app.grecaptcha.loaded && !app.grecaptcha.loading) {
            app.grecaptcha.load();
        }
    },
    init: () => {
        $f('.ba-form-submit-field').each(function(){
            if (app.items[this.id].recaptcha && app.items[this.id].recaptcha != 'hcaptcha') {
                app.grecaptcha.set(this);
            }
        });
    },
    set: (element) => {
        let parent = $f(element).find('.ba-form-submit-recaptcha-wrapper').each(function(){
                this.querySelectorAll('forms-recaptcha').forEach((div) => {
                    if (recaptchaData.data[div.id]) {
                        delete(recaptchaData.data[div.id]);
                    }
                });
            }).empty();
        app.grecaptcha.tryLoad();
        if (app.items[element.id].recaptcha && app.items[element.id].recaptcha != 'hcaptcha' && app.grecaptcha.loaded) {
            let captcha = app.items[element.id].recaptcha,
                div = document.createElement('div'),
                options = {
                    sitekey : recaptchaData[captcha].public_key
                };
            div.id = 'forms-recaptcha-'+(+new Date());
            div.className = 'forms-recaptcha';
            parent.append(div);
            if (captcha == 'recaptcha') {
                options.theme = recaptchaData[captcha].theme;
                options.size = recaptchaData[captcha].size;
            } else {
                options.badge = recaptchaData[captcha].badge;
                options.size = 'invisible';
            }
            recaptchaData.data[div.id] = app.grecaptcha.captcha.render(div, options);
        }
    }
}

function checkIntegrationsActiveState(ind)
{
    let flag = false,
        obj = integrations[ind].key,
        payments = ['twocheckout', 'authorize', 'cloudpayments', 'liqpay', 'mollie', 'paypal', 'paypal_sdk',
            'payupl', 'robokassa', 'stripe', 'payu_latam', 'yandex_kassa', 'redsys', 'payfast'];
    if (ind == 'google_maps' || ind == 'telegram') {
        flag = obj != '';
        if (ind == 'google_maps' && flag) {
            $f('.ba-form-field[data-key="map"], .ba-form-field[data-key="address"]').removeClass('disabled-field-drag');
        } else if (ind == 'google_maps') {
            $f('.ba-form-field[data-key="map"], .ba-form-field[data-key="address"]').addClass('disabled-field-drag');
        }
    } else if (ind == 'pdf_submissions') {
        flag = obj.enable;
        document.body.classList[flag ? 'add' : 'remove']('pdf-submissions-activated');
    } else if (ind == 'getresponse') {
        flag = obj.api_key != '' && obj.name != '' &&  obj.email != '';
    } else if (ind == 'activecampaign') {
        flag = obj.api_key != '' && obj.account != '' &&  obj.email != '';
    } else if (payments.indexOf(ind) != -1 || ind == 'mailchimp' || ind == 'campaign_monitor') {
        for (let i in obj) {
            flag = ind == 'authorize' && i == 'return_url' ? true : obj[i] != '';
            if (!flag) {
                break;
            }
        }
    } else if (ind == 'acymailing') {
        flag = obj.list && obj.email && obj.name;
    } else if (ind == 'zoho_crm') {
        flag = obj.client_id && obj.client_secret;
        $f('.zoho-crm-fields .ba-settings-item[data-required] select').each(function(){
            flag = flag && obj[this.dataset.key];
        });
    } else if (ind == 'google_drive') {
        flag = obj.code && obj.folder && (obj.pdf || obj.files);
    } else if (ind == 'google_sheets') {
        flag = obj.spreadsheet && obj.worksheet;
    } else if (ind == 'hcaptcha') {
        flag = obj.site_key && obj.secret_key;
        app.hCaptcha.configurate(integrations.hcaptcha.key);
        app.hCaptcha.init();
    }
    if (ind == 'hcaptcha' && !flag) {
        $f('select[data-option="recaptcha"] option[value="hcaptcha"]').remove();
    } else if (ind == 'hcaptcha') {
        $f('select[data-option="recaptcha"]').each(function(){
            $f(this).find('option[value="hcaptcha"]').remove();
            $f(this).append('<option value="hcaptcha">hCaptcha</option>');
        });
    }
    if (payments.indexOf(ind) != -1) {
        let parent = $f('select[data-option="payment"][data-callback="submitFieldAction"]');
        parent.find('option[value="'+ind+'"]').remove();
        if (flag) {
            let title = jsUcfirst(ind);
            if (ind == 'authorize') {
                title = 'Authorize.Net';
            } else if (ind == 'liqpay') {
                title = 'LiqPay';
            } else if (ind == 'payu_latam') {
                title = 'PayU Latam';
            } else if (ind == 'payupl') {
                title = 'PayU Polska';
            } else if (ind == 'twocheckout') {
                title = '2Checkout';
            } else if (ind == 'paypal') {
                title = 'PayPal old API';
            } else if (ind == 'paypal_sdk') {
                title = 'PayPal';
            } else if (ind == 'payfast') {
                title = 'PayFast';
            } else if (ind == 'yandex_kassa') {
                title = 'Yandex Kassa';
            }
            parent.append('<option value="'+ind+'"'+(ind == 'paypal' ? ' style="display: none;" disabled' : '')+'>'+title+'</option>');
            for (let i = 0; i < payments.length; i++) {
                parent.find('option[value="'+payments[i]+'"]').appendTo(parent);
            }
            parent.find('option[value*="custom-payment-"]').appendTo(parent);
        }
    }
    $f('.integrations-element[data-type="'+ind+'"]')[flag ? 'addClass' : 'removeClass']('active');
}

function setIntegrationValues(ind)
{
    let obj = integrations[ind].key;
    if (ind == 'mailchimp' && obj.api_key && mailchimp[obj.api_key]
        && obj.list && mailchimp[obj.api_key][obj.list]) {
        drawMailChimpFields(mailchimp[obj.api_key][obj.list], false)
    } else if (ind == 'google_sheets' && googleSheets.sheets && obj.spreadsheet && googleSheets.sheets[obj.spreadsheet]) {
        app.google.drawSpreadsheets(googleSheets);
        if (googleSheets.sheets[obj.spreadsheet].worksheets) {
            let worksheets = googleSheets.sheets[obj.spreadsheet].worksheets;
            app.google.drawWorksheets(worksheets, obj.spreadsheet);
            if (worksheets[obj.worksheet] && worksheets[obj.worksheet].columns) {
                app.google.drawWorkSheetsColumns(worksheets[obj.worksheet].columns);
            }
        }
    } else if (ind == 'activecampaign') {
        $f('.activecampaign-list')
            .closest('.ba-subgroup-element')[obj.api_key && obj.account ?'addClass':'removeClass']('visible-subgroup');
        $f('.activecampaign-fields')[obj.api_key && obj.account && obj.list ? 'addClass' : 'removeClass']('visible-subgroup');
    } else if (ind == 'getresponse' && obj.api_key && getresponse[obj.api_key]) {
        $f('.getresponse-custom-fields')[obj.custom_fields ? 'addClass' : 'removeClass']('visible-subgroup');
    } else if (ind == 'google_drive' && googleDrive.folders) {
        app.google.drawDriveFolders(googleDrive);
        $f('.google-drive-fields')[obj.folder ? 'addClass' : 'removeClass']('visible-subgroup');
    }
    if (ind == 'pdf_submissions') {
        $f('.integration-options[data-group="'+ind+'"] .ba-subgroup-element')[obj.enable ? 'addClass' : 'removeClass']('visible-subgroup');
    } else if (ind == 'google_sheets' || ind == 'google_drive') {
        let googleKey = ind.replace('google_', '');
        app.google.setClient(googleKey, obj.client_id, obj.client_secret);
        app.google.setGoogleAuth(googleKey);
    }
    $f('.integration-options[data-group="'+ind+'"] [data-key]').each(function(){
        if (this.dataset.key == 'key') {
            this.value = obj;
        } else if (this.type == 'checkbox') {
            this.checked = obj[this.dataset.key];
        } else {
            this.value = obj[this.dataset.key];
        }
        let trigger = this.closest('.trigger-picker-modal');
        if (trigger) {
            $f('#'+trigger.dataset.modal).find('li[data-value="'+obj[this.dataset.key]+'"]').each(function(){
                trigger.querySelector('input[type="text"]').value = this.textContent;
            });
        }
    });
}

function drawSubmitItemsSelect()
{
    let array = getSubmitItems('integrations'),
        str = '';
    $f('select.forms-fields-list option:not([hidden])').remove();
    for (let i = 0; i < array.length; i++) {
        str += '<option value="'+array[i].id+'">'+(array[i].title ? array[i].title : '')+'</option>';
    }
    if (!array.length) {
        str = '<option value="">'+(app._('NO_NE'))+'</option>';
    }
    $f('select.forms-fields-list').append(str);
}

function connectActivecampaign(account, api_key, notice, checkActive)
{
    $f.ajax({
        type: "POST",
        dataType: 'text',
        url: "index.php?option=com_baforms&view=form&task=form.getActivecampaignLists",
        data: {
            api_key: api_key,
            account: account
        },
        success: function(msg){
            let obj = JSON.parse(msg);
            if (obj.success) {
                let obj = JSON.parse(msg),
                    str = '';
                campaign[api_key] = {};
                $f('.activecampaign-list option:not([hidden])').remove();
                for (var ind in obj.lists) {
                    str += '<option value="'+ind+'">'+obj.lists[ind]+'</option>';
                }
                $f('.activecampaign-list').append(str).closest('.ba-subgroup-element').addClass('visible-subgroup');
                if (checkActive && integrations.campaign_monitor.key.list) {
                    $f('.activecampaign-fields').addClass('visible-subgroup');
                }
            } else {
                if (notice) {
                    showNotice('Invalid Key or URL', 'ba-alert');
                }
                $f('.integration-options[data-group="activecampaign"] .ba-subgroup-element').removeClass('visible-subgroup');
            }
        }
    });
}

function connectCampaignMonitor(api_key, client_id, notice, checkActive)
{
    $f.ajax({
        type: "POST",
        dataType: 'text',
        url: "index.php?option=com_baforms&view=form&task=form.getCampaignLists",
        data: {
            api_key: api_key,
            client_id: client_id
        },
        success: function(msg){
            let obj = JSON.parse(msg);
            if (obj.success) {
                let obj = JSON.parse(msg),
                    str = '';
                campaign[api_key] = {};
                $f('.campaign-monitor-list option:not([hidden])').remove();
                for (var i = 0; i < obj.lists.length; i++) {
                    str += '<option value="'+obj.lists[i].ListID+'">'+obj.lists[i].Name+'</option>';
                }
                $f('.campaign-monitor-list').append(str).closest('.ba-subgroup-element').addClass('visible-subgroup');
                if (checkActive && integrations.campaign_monitor.key.list_id) {
                    getCampaignMonitorFields(api_key, client_id, integrations.campaign_monitor.key.list_id, true);
                }
            } else {
                if (notice) {
                    showNotice('Invalid Api Key or Client Id', 'ba-alert');
                }
                $f('.integration-options[data-group="campaign_monitor"] .ba-subgroup-element').removeClass('visible-subgroup');
            }
        }
    });
}

function getCampaignMonitorFields(api_key, client_id, list_id, checkActive)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&view=form&task=form.getCampaignFields",
        data:{
            api_key: api_key,
            client_id: client_id,
            list_id: list_id
        },
        success: function(msg){
            let obj = JSON.parse(msg);
            if (obj.success) {
                campaign[api_key][list_id] = obj.fields;
                drawCampaignMonitorFields(obj.fields, checkActive);
            }
        }
    });
}

function drawCampaignMonitorFields(fields, checkActive)
{
    let div = $f('.campaign-monitor-fields'),
        str = '';
    div.find('.ba-settings-item').remove();
    for (let i  in fields) {
        str += '<div class="ba-settings-item ba-settings-select-type"><span class="ba-settings-item-title">'+
            fields[i].title+'</span><select class="forms-fields-list" data-key="'+
            fields[i].key+'"><option value="" hidden></option></select></div>';
    }
    div.append(str).addClass('visible-subgroup');
    drawSubmitItemsSelect();
    if (checkActive) {
        setIntegrationValues('campaign_monitor');
        checkIntegrationsActiveState('campaign_monitor');
    }
}

function getAcymailingFields()
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&view=form&task=form.getAcymailingFields",
        success: function(msg){
            let fields = JSON.parse(msg),
                div = $f('.acymailing-fields'),
                str = '';
            fields.forEach((field) => {
                str += '<div class="ba-settings-item ba-settings-select-type"><span class="ba-settings-item-title">'+
                    field.name+'</span><select class="forms-fields-list" data-key="'+
                    field.id+'"><option value="" hidden></option></select></div>';
            });
            div.append(str).addClass('visible-subgroup');
            drawSubmitItemsSelect();
            setIntegrationValues('acymailing');
            checkIntegrationsActiveState('acymailing');
        }
    });
}

function connectGetResponse(api_key, custom, notice, checkActive)
{
    $f.ajax({
        type: "POST",
        dataType: 'text',
        url: "index.php?option=com_baforms&view=form&task=form.getResponseLists",
        data: {
            api_key: api_key
        },
        success: function(msg){
            let obj = JSON.parse(msg);
            if (obj.success) {
                let obj = JSON.parse(msg),
                    str = '';
                getresponse[api_key] = {};
                $f('.getresponse-list option:not([hidden])').remove();
                for (var i = 0; i < obj.lists.length; i++) {
                    str += '<option value="'+obj.lists[i].campaignId+'">'+obj.lists[i].name+'</option>';
                }
                $f('.getresponse-list').append(str).closest('.ba-subgroup-element').addClass('visible-subgroup');
                if (checkActive && integrations.getresponse.key.list_id) {
                    getResponseFields(api_key, integrations.getresponse.key.list_id, custom, true);
                }
            } else {
                if (notice) {
                    showNotice('Invalid Api Key', 'ba-alert');
                }
                $f('.integration-options[data-group="getresponse"] .ba-subgroup-element').removeClass('visible-subgroup');
            }
        }
    });
}

function getResponseFields(api_key, list_id, custom, checkActive)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&view=form&task=form.getResponseFields",
        data:{
            api_key: api_key,
            list_id: list_id
        },
        success: function(msg){
            let obj = JSON.parse(msg);
            if (obj.success) {
                getresponse[api_key][list_id] = obj.fields;
                drawGetResponseFields(obj.fields, custom, checkActive);
            }
        }
    });
}

function drawGetResponseFields(fields, custom, checkActive)
{
    let div = $f('.getresponse-fields'),
        requiredDiv = div.find('.getresponse-required-fields'),
        customDiv = div.find('.getresponse-custom-fields')[custom ? 'addClass' : 'removeClass']('visible-subgroup'),
        str = '';
    div.find('.ba-settings-item:not(.ba-settings-checkbox-type)').remove();
    for (let i  in fields) {
        str = '<div class="ba-settings-item ba-settings-select-type"><span class="ba-settings-item-title">'+
            fields[i].title+'</span><select class="forms-fields-list" data-key="'+
            fields[i].key+'"><option value="" hidden></option></select></div>';
        if (fields[i].key == 'email' || fields[i].key == 'name') {
            requiredDiv.append(str);
        } else {
            customDiv.append(str);
        }
    }
    div.addClass('visible-subgroup');
    drawSubmitItemsSelect();
    if (checkActive) {
        setIntegrationValues('getresponse');
        checkIntegrationsActiveState('getresponse');
    }
}

function connectMailChimp(api_key, notice, checkActive)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&view=form&task=form.connectMailChimp",
        data:{
            api_key: api_key,
        },
        success: function(msg){
            if (msg == 0) {
                if (notice) {
                    showNotice('Invalid Api Key', 'ba-alert');
                }
                $f('.integration-options[data-group="mailchimp"] .ba-subgroup-element').removeClass('visible-subgroup');
            } else {
                let obj = JSON.parse(msg),
                    str = '';
                if (obj.lists) {
                    mailchimp[api_key] = {};
                    $f('.mailchimp-list option:not([hidden])').remove();
                    for (var i = 0; i < obj.lists.length; i++) {
                        str += '<option value="'+obj.lists[i].id+'">'+obj.lists[i].name+'</option>';
                    }
                    $f('.mailchimp-list').append(str).closest('.ba-subgroup-element').addClass('visible-subgroup');
                    if (checkActive && integrations.mailchimp.key.list) {
                        getMailChimpFields(integrations.mailchimp.key.api_key, integrations.mailchimp.key.list, true);
                    }
                } else if (notice) {
                    showNotice('Invalid Api Key', 'ba-alert');
                    $f('.integration-options[data-group="mailchimp"] .ba-subgroup-element').removeClass('visible-subgroup');
                }
            }
        }
    });
}

function getMailChimpFields(api_key, list_id, checkActive)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&view=form&task=form.getMailChimpFields",
        data:{
            api_key: api_key,
            list_id: list_id
        },
        success: function(msg){
            try {
                let obj = JSON.parse(msg),
                    object = {
                        name: 'Email',
                        tag: 'EMAIL'
                    };
                obj.merge_fields.unshift(object);
                mailchimp[api_key][list_id] = obj.merge_fields;
                drawMailChimpFields(obj.merge_fields, checkActive);
            } catch (error) {

            }
        }
    });
}

function drawMailChimpFields(fields, checkActive)
{
    let div = $f('.mailchimp-fields'),
        str = '';
    div.find('.ba-settings-item').remove();
    for (let i = 0; i < fields.length; i++) {
        str += '<div class="ba-settings-item ba-settings-select-type"><span class="ba-settings-item-title">'+
            fields[i].name+'</span><select class="forms-fields-list" data-key="'+
            fields[i].tag+'"><option value="" hidden></option></select></div>';
    }
    div.append(str).addClass('visible-subgroup');
    drawSubmitItemsSelect();
    if (checkActive) {
        setIntegrationValues('mailchimp');
        checkIntegrationsActiveState('mailchimp');
    }
}

app.setCalculator = function(){
    $f('.ba-form-calculator').each(function(){
        let $this = $f(this);
            screen = this.querySelector('.ba-form-calculator-screen');
        screen.oninput = function(){
            app.edit.formula = this.value;
        }
        $this.find('.ba-form-calculator-btn[data-code]').on('click', function(){
            if (this.dataset.code) {
                screen.setRangeText(this.dataset.code);
                let start = screen.selectionStart+this.dataset.code.length;
                screen.setSelectionRange(start, start);
            } else {
                screen.value = '';
            }
            screen.focus();
            screen.oninput();
        });
        $this.find('.ba-form-calculator-btn[data-fields]').on('click', function(){
            if (!('ondataTagsInput' in this)) {
                this.ondataTagsInput = function(){
                    screen.setRangeText(this.dataset.value);
                    let start = screen.selectionStart+this.dataset.value.length;
                    screen.setSelectionRange(start, start);
                    screen.focus();
                    screen.oninput();
                }
            }
            fontBtn = this;
            $f('#default-value-dialog').addClass('calculator-data-tags');
            showDataTagsDialog('default-value-dialog');
        });
    });
}

app.checkLastCheckbox = function(id, count){
    let wrapper = $f('#'+id+' .ba-form-checkbox-group-wrapper'),
        checkbox = wrapper.find('.ba-form-checkbox-wrapper'),
        ind = 1;
    if (wrapper.find('.ba-checkbox-image').length) {
        wrapper.addClass('checkbox-image-group-wrapper');
    } else {
        wrapper.removeClass('checkbox-image-group-wrapper');
    }
    wrapper.find('.last-row-checkbox-wrapper').removeClass('last-row-checkbox-wrapper');
    wrapper.find('.ba-form-checkbox-wrapper').each(function(){
        if (ind == count) {
            this.classList.add('last-row-checkbox-wrapper');
            ind = 0;
        }
        ind++;
        if (this.querySelector('.ba-checkbox-image')) {
            this.classList.add('checkbox-image-wrapper');
        } else {
            this.classList.remove('checkbox-image-wrapper');
        }
    }).last().addClass('last-row-checkbox-wrapper');
}

app.checkConditionLogic = function(){
    $f('.hidden-condition-field').removeClass('hidden-condition-field');
    app.conditionLogic.forEach(function(el){
        if (!el.publish) {
            return true;
        }
        let flags = [],
            not = ['submit', 'map', 'html', 'headline'],
            item = null,
            value;
        el.when.forEach(function(obj){
            if (!obj.field || !obj.state) {
                return true;
            }
            item = document.querySelector('#baform-'+obj.field);
            if (!item || not.indexOf(item.dataset.type) != -1) {
                return true;
            }
            value = '';
            if (item.dataset.type == 'radio' || item.dataset.type == 'select') {
                item.querySelectorAll('option:not([hidden]), .ba-checkbox-wrapper input').forEach(function(option, i){
                    if (option.checked || option.selected) {
                        value += app.items[item.id].items[i].key;
                    }
                });
            } else if (item.dataset.type == 'checkbox' || item.dataset.type == 'selectMultiple') {
                value = []
                item.querySelectorAll('option:not([hidden]), .ba-checkbox-wrapper input').forEach(function(option, i){
                    if (option.checked || option.selected) {
                        value.push(app.items[item.id].items[i].key);
                    }
                });
            } else if (item.dataset.type == 'acceptance') {
                let checked = item.querySelector('input[type="checkbox"]').checked;
                value = checked ?  item.querySelector('.ba-form-acceptance-html').textContent.trim() : '';
            } else if (item.dataset.type != 'upload') {
                value = item.querySelector('input, textarea').value;
            }
            switch (obj.state) {
                case 'equal':
                    if (typeof value == 'string') {
                        flags.push(value == obj.value);
                    } else {
                        flags.push(value.indexOf(obj.value * 1) != -1);
                    }
                    break;
                case 'not-equal':
                    if (typeof value == 'string') {
                        flags.push(value != obj.value);
                    } else {
                        flags.push(value.indexOf(obj.value * 1) == -1);
                    }
                    break;
                case 'not-empty':
                    if (typeof value == 'string') {
                        flags.push(value != '');
                    } else {
                        flags.push(value.length != 0);
                    }
                    break;
                case 'empty':
                    if (typeof value == 'string') {
                        flags.push(value == '');
                    } else {
                        flags.push(value.length == 0);
                    }
                    break;
                case 'greater':
                    flags.push((!isNaN(value) ? value * 1 : value) > (!isNaN(obj.value) ? obj.value * 1 : obj.value));
                    break;
                case 'less':
                    flags.push((!isNaN(value) ? value * 1 : value) < (!isNaN(obj.value) ? obj.value * 1 : obj.value));
                    break;
                case 'contain':
                    flags.push(value.indexOf(obj.value) != -1);
                    break;
                case 'not-contain':
                    flags.push(value.indexOf(obj.value) == -1);
                    break;
            }
        });
        if (!flags.length) {
            return true;
        }
        let method = el.operation == 'OR' ? 'some' : 'every',
            flag = flags[method](function(state){return state});
        el.do.forEach(function(obj){
            if (obj.field && obj.action) {
                let item = document.querySelector('#baform-'+obj.field);
                if (!item) {
                    return true;
                }
                if (obj.action == 'show') {
                    item.classList[flag ? 'remove' : 'add']('hidden-condition-field');
                } else if (obj.action == 'hide') {
                    item.classList[!flag ? 'remove' : 'add']('hidden-condition-field');
                }
            }
        });
    });
}

app.setSubmitDesign = function(obj, wrapper){
    let keys = ['background', 'padding', 'border', 'typography', 'icon', 'shadow'];
    for (let i = 0; i < keys.length; i++) {
        for (let option in obj[keys[i]]) {
            if (option == 'link') {
                continue;
            }
            app.setDesignCssVariable(keys[i], '', option, obj, wrapper, 'submit');
            if ((keys[i] == 'padding' && (option == 'top' || option == 'bottom'))
                || (keys[i] == 'typography' && option == 'line-height')) {
                app.setDesignCssVariable(keys[i], '', option, obj, wrapper.nextElementSibling, 'submit');
            }
        }
    }
}

app.prepareFormsPages = function(){
    $f('.ba-form-submit-field').each(function(){
        app.setSubmitDesign(app.items[this.id], this.querySelector('.ba-form-submit-wrapper'));
        if (app.items[this.id].recaptcha && app.items[this.id].recaptcha != 'hcaptcha') {
            app.grecaptcha.set(this);
        }
    });
    if (document.querySelector('.ba-form-map-field')) {
        app.loadGoogleMaps();
    }
    $f('.page-info').text(app.design.form.width.value+app.design.form.units['width-value']);
    app.checkFormSlider();
    $f('.ba-form-checkbox-field, .ba-form-radio-field').each(function(){
        app.checkLastCheckbox(this.id, app.items[this.id].count);
    });
    $f('body div .ba-tooltip').each(function(){
        app.setTooltip($f(this).parent());
    });
    app.checkPageCount();
    app.checkConditionLogic();
}

document.addEventListener('DOMContentLoaded', function(){
    $f = jQuery;
    app.form_id = document.getElementById('form-id').value;
    app.design = $f.extend(true, formOptions.design, app.design);
    app.items.navigation = $f.extend(true, formOptions.navigation, app.items.navigation);
    libHandle = document.getElementById('library-item-handle');
    setSortable($f('.ba-forms-workspace-body'), 'page', '> .ba-form-page > .ba-edit-item .edit-settings', '> .ba-form-page', function(){
        app.checkPageCount();
    });
    $f('.ba-forms-workspace-body').on('mousedown', '.ba-column-resizer', columnResizer);
    $f('.ba-forms-workspace-body').on('mousedown', '.ba-page-resizer', pageResizer);
    notification = $f('#ba-notification');
    notification.on('mousedown', function(event){
        event.stopPropagation();
    }).find('i.zmdi-close').on('click', function(){
        notification.removeClass('notification-in').addClass('animation-out');
    });
    $f('.edit-lightbox').on('mousedown', function(event){
        app.selector = null;
        setTimeout(function(){
            app.showLightboxSettings();
        }, 300);
    });
    $f('.edit-lightbox-color').on('mousedown', function(event){
        var rect = this.getBoundingClientRect(),
            rect2 = document.querySelector('.edit-lightbox').getBoundingClientRect(),
            modal = $f('#color-variables-dialog'),
            width = modal.innerWidth(),
            left = rect2.right - width,
            delta = 100 - ((rect.left + rect.width / 2 - left) * 100 / width);
        modal[0].style.setProperty('--color-variables-arrow-right', delta+'%');
        modal.removeClass('ba-right-position ba-bottom-position').addClass('ba-top-position');
        setMinicolorsColor(this.dataset.rgba);
        fontBtn = this;
        setTimeout(function(){
            modal.css({
                left : left,
                top : rect.bottom + 10
            }).modal();
        }, 300);
    }).on('minicolorsInput', function(){
        var $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.design.lightbox.color = $this.dataset.rgba;
            app.setDesignCssVariable('lightbox', '', 'color', app.design, document.body);
        }, 300);
    });
    $f('.ba-forms-workspace').on('mousedown', '.ba-form-field-item', function(event){
        if (app.cp.inPosition() && event.target && !event.target.closest('.ba-edit-item')) {
            app.cp.closeAll();
            app.cp.edit(this);
        }
    }).on('mousedown', '.edit-item', function(event){
        let item = this.closest('.ba-form-field-item');
        app.cp.edit(item);
    }).on('mousedown', '.copy-item', function(event){
        app.copyItem(this);
    }).on('mousedown', '.delete-item', function(event){
        fontBtn = this.closest('.ba-edit-item').parentNode;
        setTimeout(function(){
            $f('#delete-dialog').modal();
        }, 100);
    });
    $f('#apply-delete').on('click', function(){
        if (typeof(fontBtn) == 'object' && fontBtn.type == 'deleteSelectedCondition') {
            let array = [],
                ind = 0;
            fontBtn.rows.each(function(i){
                if (fontBtn.keys.indexOf(i) != -1) {
                    this.remove();
                } else {
                    this.dataset.key = ind++;
                    array.push(app.conditionLogic[i])
                }
            });
            app.conditionLogic = array;
            app.checkConditionLogic();
            $f('#condition-logic-modal .ba-settings-group').hide();
            $f('#condition-logic-modal label[data-action]').removeClass('active');
        } else if (typeof(fontBtn) == 'object' && fontBtn.type == 'deleteConditionRow') {
            let array = [],
                key = fontBtn.row.dataset.ind,
                ind = 0;
            $f(fontBtn.parent).find('.condition-logic-horizontal-fields-wrapper').each(function(i){
                if (fontBtn.row == this) {
                    this.remove();
                } else {
                    this.dataset.key = ind;
                    array.push(app.edit[key][i]);
                }
            });
            app.edit[key] = array;
            app.checkConditionLogic();
            $f('.conditions-matches-wrapper').css('display', app.edit.when.length > 1 ? '' : 'none');
        } else if (typeof(fontBtn) == 'object' && fontBtn.type == 'deleteSelectedItems') {
            let object = {},
                item = document.querySelector('#'+app.selector),
                ind = 0;
            fontBtn.option.each(function(i){
                let row = fontBtn.rows.get(i);
                if (fontBtn.keys.indexOf(i) != -1) {
                    if (app.edit.type == 'navigation') {
                        $f(this).find('.ba-form-field-item').each(function(){
                            delete(app.items[this.id]);
                        });
                    }
                    this.remove();
                    row.remove();
                } else {
                    row.dataset.ind = ind;
                    row.querySelector('input[type="checkbox"]').dataset.ind = ind;
                    object[ind++] = $f.extend(true, {}, app.edit.items[i]);
                }
            });
            if (item && item.dataset.type == 'total' && !item.querySelector('.ba-cart-shipping-item')) {
                item.querySelector('.ba-cart-shipping-row').style.display = 'none';
                app.calculateCartTotal(app.selector, app.edit);
            }
            if (document.querySelector('#'+app.selector+' .ba-form-checkbox-group-wrapper')) {
                app.checkLastCheckbox(app.selector, app.edit.count);
            }
            app.edit.items = object;
            $f('.sorting-group-wrapper label[data-action].active').removeClass('active');
            if (app.edit.type == 'navigation') {
                app.checkPageCount();
            }
        } else if (typeof(fontBtn) == 'object' && fontBtn.type == 'deleteSelectedItemsImage') {
            fontBtn.row.querySelector('.sorting-image').remove();
            fontBtn.row.classList.remove('with-sorting-image');
            let ind = fontBtn.row.dataset.ind * 1;
            delete app.edit.items[ind].image;
            $f('#'+app.selector+' .ba-form-checkbox-wrapper:nth-child('+(ind + 1)+') .ba-checkbox-image').remove();
            app.checkLastCheckbox(app.selector, app.edit.count);
        } else {
            $f(fontBtn).each(function(){
                if (this.classList.contains('ba-form-field-item')) {
                    delete(app.items[this.id]);
                } else {
                    $f(this).find('.ba-form-field-item').each(function(){
                        delete(app.items[this.id]);
                    });
                }
            }).remove();
            app.checkPageCount();
        }
        $f('#delete-dialog').modal('hide');
    });
    $f('.forms-close').on('click', function(){
        window.location.href = JUri+'administrator/index.php?option=com_baforms';
    });
    $f('.add-new-page').on('click', function(){
        createNewPage();
    });
    $f('body').on('mousedown', function(event){
        if (!app.cp.inPosition() && (!event.target || !(event.target &&
                    ((typeof(event.target.className) == 'string' && event.target.className.match(/^mce|^cke_|CodeMirror-hint/)) ||
                        event.target.closest('[class^="mce"], [class^="cke_"]'))))) {
            $f('.modal-backdrop').last().trigger('click');
        }
        if (event.target && event.target.closest('.close-all-modals')) {
            app.cp.closeAll();
        }
        $f('.columns-dialog-parent').removeClass('columns-dialog-parent');
        document.body.classList.remove('visible-add-columns-dialog');
    });
    $f('body .modal').on('hide', function(){
        let modal = $f(this).addClass('ba-modal-close').data('modal');
        if (modal.$backdrop) {
            modal.$backdrop.addClass('ba-backdrop-close');
        }
        setTimeout(function(){
            $f('.ba-modal-close').removeClass('ba-modal-close');
        }, 300);
    }).on('mousedown', function(event){
        $f(document).trigger(event);
        event.stopPropagation();
    }).on('shown', function(event){
        $f('.modal-backdrop').on('mousedown', function(event){
            if (!this.classList.contains('color-variables-dialog')) {
                $f('.modal-backdrop.color-variables-dialog').trigger('click');
            }
            event.stopPropagation();
        }).last().addClass(this.id+(this.classList.contains('hidden-modal-backdrop') ? ' hidden-modal-backdrop' : ''));
    });
    $f('.ba-modal-cp > *, #text-editor-dialog > *').on('mousedown', function(event){
        let $this = this.closest('.modal');
        if ($f('.ba-modal-picker, .picker-modal-arrow').not($this).hasClass('in') && event.target != fontBtn) {
            $f('.modal-backdrop.in').last().trigger('click');
        }
    });
    $f('#custom-color-scheme-dialog > *').on('mousedown', function(event){
        if ($f('#color-variables-dialog.in').length && event.target != fontBtn) {
            $f('.modal-backdrop.in.color-variables-dialog').trigger('click');
        }
    });
    $f('body').on('click', '.ba-add-rows', function(){
        let content = prepareColumns('12');
        $f(content).find('.ba-tooltip').each(function(){
            app.setTooltip($f(this).parent());
        });
        this.closest('.ba-form-page').querySelector('.ba-page-items').appendChild(content);
    });
    $f('body').on('click', '.modify-columns', function(){
        let rect = this.closest('.ba-edit-item').querySelector('.edit-settings').getBoundingClientRect(),
            row = this.closest('.ba-form-row'),
            columns = $f(row).find('> .ba-form-column-wrapper > .ba-form-column').length,
            modal = $f('#add-columns-dialog'),
            width = modal.innerWidth(),
            height = modal.innerHeight(),
            top = rect.top - height - 10,
            left = rect.left - width / 2 + rect.width / 2;
        app.modifyRow = row;
        modal.css({
            top: top+'px',
            left: left+'px'
        }).modal().find('input[type="number"]').each(function(){
            this.value = columns;
            let range = $f(this).prev().val(columns);
            setLinearWidth(range);
        });
        row.classList.add('columns-dialog-parent');
        document.body.classList.add('visible-add-columns-dialog');
    });
    $f('#custom-css-editor').each(function(){
        var code = $f(this).find('.custom-css-editor-code').text();
        $f(this).find('> style').html(code);
    });
    $f('.ba-code-editor').on('mousedown', function(event){
        if (app.codemirror.loaded && !$f('#code-editor-dialog').hasClass('in')) {
            app.showCodeEditor();
        } else if (!app.codemirror.loaded) {
            app.codemirror.init(app.showCodeEditor);
        }
    });
    $f('.ba-design-editor').on('mousedown', function(event){
        let modal = $f('#design-settings-dialog');
        app.selector = null;
        if (modal.hasClass('in')) {
            event.stopPropagation();
        } else {
            app.edit = app.design;
            setTimeout(function(){
                app.cp.show(modal);
                modal.modal();
            }, 200);
        }
    });
    $f('.design-settings-switcher, .hide-design-settings').on('mousedown', function(){
        document.querySelector('.advanced-design-settings').classList[this.dataset.action]('active');
        if (this.dataset.action == 'remove') {
            app.closeAdvancedAccordions();
        }
    });
    $f('.advanced-design-settings .general-tabs > ul').on('show', function(){
        app.closeAdvancedAccordions();
    });
    $f('.ba-range-wrapper input[type="range"]').each(function(){
        rangeAction(this, inputCallback);
    });
    $f('.ba-settings-toolbar input[type="number"]').on('input', function(){
        inputCallback($f(this));
    });
    $f('.color-scheme-item').on('click', function(){
        if (this.dataset.scheme == 'custom') {
            var rect = this.getBoundingClientRect(),
                modal = $f('#custom-color-scheme-dialog'),
                width = modal.innerWidth(),
                height = modal.innerHeight();
            modal.css({
                left : rect.left - width - 10,
                top : rect.bottom - height / 2 - rect.height / 2
            }).modal();
        } else {
            let scheme = colorScheme[this.dataset.scheme];
            $f('#custom-color-scheme-dialog input[data-type="color"]').each(function(){
                let input = $f(this);
                updateInput(input, scheme[this.dataset.key]);
                input.trigger('minicolorsInput')
            });
        }
    });
    $f('.select-design-font-group').on('change', function(){
        let group = this.value;
        $f(this).closest('.ba-settings-group').find('[data-group]').each(function(){
            this.dataset.group = group;
            setFieldValues($f('#calculation-field-settings-dialog'));
        });
    });
    $f('.fonts-select').on('change', function(){
        let input = this.querySelector('input[type="hidden"]')
            value = input.value;
        if (value != 'inherit') {
            let link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '//fonts.googleapis.com/css?family='+value;
            document.head.appendChild(link);
        }
        if (input.dataset.group || input.dataset.subgroup || input.dataset.option) {
            app.setValue(value, input.dataset.group, input.dataset.subgroup, input.dataset.option);
            if (input.dataset.group == 'theme') {
                app.setValue(value, 'label', 'typography', 'font-family');
                app.setValue(value, 'field', 'typography', 'font-family');
                app.setDesignCssVariable('label', 'typography', 'font-family', app.design, document.body);
                app.setDesignCssVariable('field', 'typography', 'font-family', app.design, document.body);
                $f('[data-subgroup="typography"][data-option="font-family"]').each(function(){
                    this.value = value;
                    $f(this).prev().val(googleFonts[value]);
                });
            } else if (app.selector) {
                app[input.dataset.callback](value, input.dataset.group, input.dataset.subgroup, input.dataset.option);
            } else {
                app.setDesignCssVariable(input.dataset.group, input.dataset.subgroup, input.dataset.option, app.design, document.body);
            }
            app.setPopularFontsFamily();
        }
    });
    $f('.ba-settings-toolbar label[data-option]').on('click', function(){
        let classList = this.classList,
            callback = this.dataset.callback;
        if (this.dataset.default) {
            if (classList.contains('active')) {
                app.setValue(this.dataset.default, this.dataset.group, this.dataset.subgroup, this.dataset.option);
                classList.remove('active');
            } else {
                app.setValue(this.dataset.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
                classList.add('active')
            }
        } else {
            app.setValue(this.dataset.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
            let parent = this.closest('.ba-settings-toolbar');
            parent.querySelector('label[data-option="'+this.dataset.option+'"].active').classList.remove('active');
            classList.add('active');
        }
        if (callback) {
            app[callback]('', this.dataset.group, this.dataset.subgroup, this.dataset.option);
        } else {
            app.setDesignCssVariable(this.dataset.group, this.dataset.subgroup, this.dataset.option, app.design, document.body);
        }
    });
    $f('input[type="checkbox"][data-option]').on('change', function(){
        app.setValue(this.checked, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        if (this.dataset.callback) {
            app[this.dataset.callback](this.checked, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        } else {
            if (this.dataset.option == 'fullwidth' && this.checked) {
                let select = $f('.ba-units-select[data-option="width-value"]');
                changeSelected(select, '%');
                select.first().trigger('change');
            }
            app.setDesignCssVariable(this.dataset.group, this.dataset.subgroup, this.dataset.option, app.design, document.body);
        }
    });
    $f('i.spacing-link').on('click', function(){
        let flag = this.classList.contains('link-enabled'),
            str = '[data-group="'+this.dataset.group+'"]'+(this.dataset.subgroup ? '[data-subgroup="'+this.dataset.subgroup+'"]' : '');
        app.setValue(!flag, this.dataset.group, this.dataset.subgroup, 'link');
        if (flag) {
            $f(this).closest('.ba-settings-item').find(str).removeClass('link-enabled');
        } else {
            $f(this).closest('.ba-settings-item').find(str).addClass('link-enabled');
        }
    });
    $f('select.ba-units-select').on('change', function(){
        let str = (this.dataset.group ? '[data-group="'+this.dataset.group+'"]' : '')+'[data-option="'+this.dataset.option+'"]',
            select = $f(this).closest('.ba-modal-cp').find('.ba-units-select'+str),
            input = select.closest('.ba-range-wrapper').find('input[type="number"]');
        if (this.dataset.option == 'letter-spacing') {
            input.val(0);
        } else if (this.value == 'em') {
            input.val(1);
        } else if (this.value == 'px' && this.dataset.option != 'width-value' && this.dataset.option != 'width' && this.dataset.option != 'popup-width') {
            input.val(14);
        } else {
            let value = this.value == 'px' ? 500 : 100
            input.val(value);
            changeSelected(select.not(this), this.value);
            $f('.page-info').text(value+this.value);
        }
        if (this.dataset.group == 'theme') {
            changeSelected(select.not(this), this.value);
        }
        prepareUnit(this.dataset.option, this.value, select);
        app.setValue(this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        if (this.dataset.group == 'theme') {
            app.design.label.units[this.dataset.option] = this.value;
            app.design.field.units[this.dataset.option] = this.value;
        }
        input.trigger('input');
    });
    $f('.ba-form-style-select').on('change', function(){
        app.design.theme.style = this.value;
        let value = this.value == 'rounded' ? 50 : 0;
        $f('input[type="number"][data-group="field"][data-subgroup="border"][data-option="radius"]').val(value).trigger('input');
    });
    $f('.ba-form-layout-select').on('change', function(){
        app.edit.theme.layout = this.value;
        $f('.ba-forms-workspace')[this.value == 'lightbox' ? 'addClass' : 'removeClass']('ba-forms-lightbox-enabled');
    });
    var str = '.ba-settings-number-type, .ba-settings-input-type, .ba-settings-textarea-type, .ba-settings-text-editor-type';
    $f(str).find('input[data-option], textarea[data-option]').on('input', function(){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.setValue($this.value, $this.dataset.group, $this.dataset.subgroup, $this.dataset.option);
            app[$this.dataset.callback]($this.value, $this.dataset.group, $this.dataset.subgroup, $this.dataset.option);
        }, 300);
    });
    $f('.ba-settings-font-type input[data-option][data-callback]').on('change', function(){
        app.setValue(this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        app[this.dataset.callback](this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
    });
    str = '.ba-input-label-wrapper, .ba-form-checkbox-title, .ba-page-navigation-title';
    $f('.ba-forms-workspace-body').on('keydown', str, function(event){
        if (event.keyCode == 13) {
            event.preventDefault();
            event.stopPropagation();
        }
    }).on('click', '.ba-input-password-icons i', function(){
        let input = this.closest('.ba-field-container').querySelector('input'),
            icons = this.closest('.ba-input-password-icons').querySelectorAll('i');
        if (this.dataset.action == 'show') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }).on('click', '.ba-phone-selected-country', function(){
        let wrapper = this.closest('.ba-phone-countries-wrapper');
        setTimeout(function(){
            wrapper.querySelectorAll('li').forEach(function(el){
                el.style.display = '';
            });
            wrapper.classList.remove('top-countries-list');
            wrapper.classList.add('visible-countries-list');
            let rect = wrapper.querySelector('ul').getBoundingClientRect();
            if (window.innerHeight < rect.bottom) {
                wrapper.classList.add('top-countries-list');
            }
            wrapper.querySelector('.ba-phone-countries-search').value = '';
            wrapper.querySelector('.ba-phone-countries-search').focus();
            $f('body').off('click.selected-country').one('click.selected-country', function(){
                $f('.visible-countries-list').removeClass('visible-countries-list');
            });
        }, 100);
    }).on('input', '.ba-phone-countries-search', function(){
        var li = this.closest('.ba-phone-countries-wrapper').querySelectorAll('li'),
            search = this.value.toLowerCase();
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            li.forEach(function(el){
                let title = el.dataset.title.toLowerCase();
                el.style.display = !search || title.indexOf(search) != -1 ? '' : 'none';
            });
        }, 300);
    }).on('click', '.ba-phone-countries-search', function(event){
        event.stopPropagation();
    }).on('click', '.ba-phone-countries-list li.ba-phone-country-item', function(){
        let wrapper = this.closest('.ba-field-container');
        if (!wrapper.phoneInput) {
            wrapper.countryFlag = wrapper.querySelector('.ba-phone-selected-country .ba-phone-flag');
            wrapper.countryPrefix = wrapper.querySelector('.ba-phone-selected-country .ba-phone-prefix');
            wrapper.phoneInput = wrapper.querySelector('input.ba-phone-number-input');
        }
        wrapper.countryFlag.className = 'ba-phone-flag ba-phone-flag-'+this.dataset.flag;
        wrapper.countryPrefix.textContent = this.dataset.prefix;
        wrapper.phoneInput.placeholder = this.dataset.placeholder;
        wrapper.phoneInput.value = '';
        wrapper.phoneInput.mask = this.dataset.placeholder;
        app.prepareInputMask(wrapper.phoneInput, wrapper.phoneInput.mask);
    }).on('click', 'input.ba-phone-number-input', function(){
        if (!this.mask) {
            this.mask = this.placeholder;
        }
        app.prepareInputMask(this, this.mask);
    }).on('keydown', 'input.ba-phone-number-input', function(event){
        app.prepareInputMask(this, this.mask, event);
    }).on('input', '.ba-form-phone-field input.ba-phone-number-input', function(){
        app.executeInputMask(this, this.mask);
    }).on('click', '.ba-form-input-field input', function(){
        if (app.masks.indexOf(this.dataset.type) != -1) {
            if (!this.mask) {
                this.mask = this.placeholder;
            }
            app.prepareInputMask(this, this.mask);
        }
    }).on('keydown', '.ba-form-input-field input', function(event){
        if (app.masks.indexOf(this.dataset.type) != -1) {
            app.prepareInputMask(this, this.mask, event);
        }
    }).on('input', '.ba-form-input-field input', function(){
        if (app.masks.indexOf(this.dataset.type) != -1) {
            app.executeInputMask(this, this.mask);
        }
    }).on('keyup', '.ba-page-navigation-title', function(event){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            let nth = $this.closest('.ba-form-page-navigation-title').querySelector('.ba-form-page-navigation-counter').textContent * 1;
            app.items.navigation.items[nth - 1].title = $this.textContent;
            document.querySelector('.ba-form-page:nth-child('+nth+')').dataset.title = $this.textContent;
            $f('.ba-form-page-navigation .ba-form-page-navigation-title:nth-child('+nth+')')
                .find('.ba-page-navigation-title').not($this).text($this.textContent);
        }, 500);
    }).on('keyup', '.ba-input-label-wrapper', function(event){
        app.selector = this.closest('.ba-form-field-item').id;
        app.edit = app.items[app.selector];
        if (this.closest('.confirm-email-wrapper')) {
            app.edit.confirm.title = this.textContent;
        } else {
            app.edit.title = this.textContent;
            prepareRequired();
        }
    }).on('keyup', '.ba-form-checkbox-title', function(){
        let id = this.closest('.ba-form-field-item').id,
            $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            $f($this).closest('.ba-form-checkbox-group-wrapper')
                .find('> .ba-form-checkbox-wrapper .ba-form-checkbox-title').each(function(ind){
                if (this == $this) {
                    app.items[id].items[ind].title = $this.textContent;
                    return false;
                }
            });
        }, 500);
    }).on('input', '.ba-field-container input, .ba-field-container textarea', function(){
        app.checkConditionLogic();
    }).on('change', '.ba-field-container select', function(){
        app.checkConditionLogic();
    }).on('change', '.ba-form-acceptance-field input[type="checkbox"]', function(){
        app.checkConditionLogic();
    }).on('change', '.ba-form-checkbox-group-wrapper', function(){
        let input = this.querySelectorAll('input');
        $f(this).find('.checked-image-container').removeClass('checked-image-container');
        for (let i = 0; i < input.length; i++) {
            if (input[i].checked) {
                input[i].closest('.ba-form-checkbox-wrapper').classList.add('checked-image-container');
            }
        }
        app.checkConditionLogic();
    }).on('click', '.ba-form-checkbox-wrapper', function(event){
        if ($f(this).find('> .ba-checkbox-image').length && !event.target.classList.contains('ba-form-checkbox')
            && !event.target.closest('.ba-form-checkbox') && !event.target.closest('.ba-form-radio')) {
            $f(this).find('> .ba-checkbox-wrapper input').each(function(){
                if (this.type == 'radio' && !this.checked) {
                    this.checked = true;
                } else if (this.type == 'checkbox') {
                    this.checked = !this.checked;
                }
            }).trigger('change');
        }
    }).on('change', '.ba-form-rating input', function(){
        let wrapper = this.closest('.ba-form-rating-group-wrapper'),
            label = wrapper.querySelector('label.active');
        if (label) label.classList.remove('active');
        this.closest('.ba-form-rating').classList.add('active');
        wrapper.classList.add('active');
    }).on('input', '.form-range-wrapper input[type="range"]', function(){
        var max = this.max * 1,
            min = this.min * 1
            value = this.value * 1,
            sx = (value - min) * 100 / (max - min);
        if (!this.linear) {
            this.linear = this.closest('.form-range-wrapper').querySelector('.ba-form-range-liner');
        }
        if (!this.number) {
            this.number = this.closest('.ba-field-container').querySelector('.form-slider-input-wrapper input[data-type="range"]');
        }
        this.number.value = this.value;
        this.linear.style.width = sx+'%';
        app.checkConditionLogic();
    }).on('input', '.form-slider-input-wrapper input[data-type="range"]', function(){
        if (!this.range) {
            this.range = $f(this).closest('.ba-field-container').find('.form-range-wrapper input[type="range"]');
        }
        var max = this.range.attr('max') * 1,
            min = this.range.attr('min') * 1,
            value = this.value * 1;
        if (value > max) {
            this.value = value = max;
        }
        if (value < min) {
            value = min;
        }
        this.range.val(value).trigger('input');
    }).on('input', '.set-slider-range', function(){
        let decimals = 99,
            max = decimals > 0 ? 1 : 0,
            match = this.value.match(new RegExp('\\d+\\.{0,'+max+'}\\d{0,99}'));
        if (!match) {
            this.value = '';
        } else if (match[0] != this.value) {
            this.value = match[0];
        }
        let id = this.closest('.ba-form-field-item').id,
            input = $f(this).closest('.ba-form-field-item').find('input'),
            $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.items[id][$this.dataset.type] = $this.value;
            app.sliderFieldAction($this.value, '', '', $this.dataset.type);
        }, 500);
    }).on('input', '.form-slider-wrapper input[type="range"]', function(){
        var max = this.max * 1,
            min = this.min * 1,
            ind = this.dataset.index * 1,
            index = ind == 0 ? 1 : 0,
            value = this.value * 1,
            value2 = this.slides[index].value * 1,
            sx = left = 0;
        if (this.slides[0].value * 1 > this.slides[1].value * 1) {
            ind = ind == 0 ? 1 : 0;
            index = index == 0 ? 1 : 0;
        }
        this.input.values[index] = value2;
        this.numbers[index].value = value2;
        this.numbers[ind].value = value;
        this.input.values[ind] = value;
        this.input.value = this.input.values[0]+' '+this.input.values[1];
        sx = (this.input.values[1] * 1 - this.input.values[0] * 1) * 100 / (max - min)
        left = (max - this.input.values[1] * 1) * 100 / (max - min);
        this.linear.style.width = sx+'%';
        this.linear.style.marginLeft = (100 - sx - left)+'%';
        app.checkConditionLogic();
    }).on('input', '.form-slider-input-wrapper input[data-type="slider"]', function(){
        let $this = this
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            if ($this.value) {
                let value = $this.value * 1,
                    values = $this.slides[0].input.values,
                    index = $this.dataset.index * 1;
                if (index == 0 && value > values[1] * 1) {
                    index = 1;
                    $this.slides[1].numbers[1].focus();
                    $this.value = values[1];
                    $this.slides[0].input.values[0] = $this.value;
                    $f($this.slides[0]).val($this.value);
                } else if (index == 1 && value < values[0] * 1) {
                    index = 0;
                    $this.slides[0].numbers[0].focus();
                    $this.value = values[0];
                    $this.slides[0].input.values[1] = $this.value;
                    $f($this.slides[1]).val($this.value);
                }
                $this.slides[index].value = value;
                $f($this.slides[index]).trigger('input');
            }
        }, 500);
    }).on('change', '.ba-cart-shipping-item input[type="radio"]', function(){
        let id = this.closest('.ba-form-field-item').id;
        app.calculateCartTotal(id, app.items[id]);
    }).on('input', '.text-content-wrapper', function(){
        app.items[this.closest('.ba-form-text-field').id].html = this.innerHTML;
    });
    $f('div.reset').on('click', function(){
        $f(this).parent().find('input').val('').attr('data-value', '').trigger('input');
    });
    $f('.modify-item-suffix').on('input', function(){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            if (app.edit.theme) {
                $f('.ba-forms-workspace-body').removeClass(app.edit.theme.suffix).addClass($this.value);
            } else if (app.edit.session) {
                $f('.ba-forms-workspace-body').removeClass(app.edit.suffix).addClass($this.value);
            } else if (app.edit.type != 'navigation') {
                $f('#'+app.selector).removeClass(app.edit.suffix).addClass($this.value);
            } else {
                $f('.ba-form-page-navigation-wrapper, .ba-form-page-break').removeClass(app.edit.suffix).addClass($this.value);
            }
            if (app.edit.theme) {
                app.edit.theme.suffix = $this.value;
            } else {
                app.edit.suffix = $this.value;
            }
        }, 300);
    });
    $f('.copy-to-clipboard').on('click', function(event){
        var textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        textarea.value = this.closest('.ba-settings-input-type').querySelector('.select-input').value;
        textarea.select()
        document.execCommand('copy');
        textarea.remove();
        showNotice(app._('SUCCESSFULLY_COPIED_TO_CLIPBOARD'));
    });
    $f('select[data-option][data-callback]').on('change', function(){
        app.setValue(this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        app[this.dataset.callback](this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
    });
    $f('.select-input.select-image').on('click', function(){
        fontBtn = this;
        uploadMode = 'triggerFontBtn';
        checkIframe($f('#uploader-modal').attr('data-check', 'single'), 'uploader');
    }).on('input', function(){
        if (this.dataset.option) {
            app.setValue(this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
            app[this.dataset.callback](this.value, this.dataset.group, this.dataset.subgroup, this.dataset.option);
        }
    });
    $f('.input-click-trigger').on('click', function(){
        $f(this).parent().find('input').trigger('click');
    });
    $f('.ba-settings-sortable-type .sorting-container').on('input', 'input[data-key]', function(){
        let ind = this.closest('.sorting-item').dataset.ind * 1,
            key = this.dataset.key;
        app.edit.items[ind][this.dataset.key] = this.value;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            if ('promo' in app.edit && key == 'title') {
                $f('#'+app.selector).find('.ba-form-shipping-title').get(ind).textContent = app.edit.items[ind].title;
            } else if ('promo' in app.edit && key == 'price') {
                let price = renderPrice(app.edit.items[ind].price, app.edit.thousand, app.edit.separator, app.edit.decimals),
                    option = $f('#'+app.selector).find('.ba-cart-shipping-item').get(ind);
                option.querySelector('.field-price-value').textContent = price;
                option.querySelector('input[type="radio"]').dataset.price = app.edit.items[ind].price;
            } else if (app.edit.type != 'navigation') {
                let option = $f('#'+app.selector).find('select option:not([hidden]), span.ba-checkbox-title').get(ind),
                    action = option.localName == 'option' ? 'textContent' : 'innerHTML';
                option[action] = prepareItemsText(app.edit.items[ind]);
            } else {
                $f('.ba-form-page:nth-child('+(ind + 1)+')').attr('data-title', app.edit.items[ind].title);
                $f('.ba-form-page-navigation').each(function(){
                    $f(this).find('.ba-page-navigation-title').get(ind).textContent = app.edit.items[ind].title;
                });
            }
        }, 300);
    });
    $f('.sorting-group-wrapper').on('change', '.ba-form-checkbox', function(){
        let checked = 0;
        $f(this).closest('.sorting-container').find('.ba-form-checkbox input').each(function(){
            if (this.checked) {
                checked++;
            }
        });
        if (checked) {
            $f(this).closest('.sorting-group-wrapper').find('.ba-settings-toolbar label').addClass('active');
        } else {
            $f(this).closest('.sorting-group-wrapper').find('.ba-settings-toolbar label').removeClass('active');
        }
        if (checked == 1) {
            $f(this).closest('.sorting-group-wrapper').find('.ba-settings-toolbar')
                .find('label[data-action="default"], label[data-action="image"]').addClass('active');
        } else {
            $f(this).closest('.sorting-group-wrapper').find('.ba-settings-toolbar')
                .find('label[data-action="default"], label[data-action="image"]').removeClass('active');
        }
    });
    $f('.sorting-group-wrapper').on('click', '.delete-sorting-image', function(){
        fontBtn = {
            type: 'deleteSelectedItemsImage',
            row: this.closest('.sorting-item')
        }
        $f('#delete-dialog').modal();
    });
    $f('.sorting-group-wrapper .ba-settings-toolbar label').on('click', function(){
        if (this.classList.contains('active')) {
            let keys = [],
                rows = $f(this).closest('.sorting-group-wrapper').find('.sorting-container .sorting-item'),
                item = app.edit.type != 'navigation' ? $f('#'+app.selector) : $f('.ba-form-page')
                option = app.edit.type != 'navigation' ? item.find('select option:not([hidden]), div.ba-form-checkbox-wrapper') : item;
            if (item[0].dataset.type == 'total') {
                option = item.find('.ba-cart-shipping-item');
            }
            $f(this).closest('.sorting-group-wrapper').find('.sorting-container .ba-form-checkbox input').each(function(){
                if (this.checked) {
                    keys.push(this.dataset.ind * 1);
                }
            });
            switch (this.dataset.action) {
                case 'default':
                    option.each(function(i){
                        let flag = keys.indexOf(i) != -1 && !app.edit.items[i].default;
                        if (this.localName == 'option') {
                            this.selected = flag;
                        } else {
                            $f(this).find('input').prop('checked', flag).trigger('change');

                        }
                        app.edit.items[i].default = flag;
                        rows.get(i).classList[flag ? 'add' : 'remove']('default-item');
                    });
                    break;
                case 'copy':
                    let object = {},
                        ind = 0,
                        time = +new Date(),
                        $this = null;
                    option.each(function(i){
                        object[ind] = $f.extend(true, {}, app.edit.items[i]);
                        $this = rows.get(i);
                        $this.dataset.ind = ind;
                        $this.querySelector('input[type="checkbox"]').dataset.ind = ind;
                        ind++
                        if (keys.indexOf(i) != -1) {
                            object[ind] = $f.extend(true, {}, app.edit.items[i]);
                            if (item[0].dataset.type == 'total') {
                                object[ind].default = false;
                                let clone = this.cloneNode(true);
                                clone.querySelector('input').checked = false;
                                $f(this).after(clone);
                            } else if (app.edit.type != 'navigation') {
                                object[ind].default = false;
                                object[ind].key = time++;
                                let clone = this.cloneNode(true);
                                if (clone.localName == 'option') {
                                    clone.selected = false;
                                } else {
                                    clone.classList.remove('checked-image-container');
                                    clone.querySelector('input').checked = false;
                                }
                                $f(this).after(clone);
                            } else {
                                let btn = $f(this).find('> .ba-edit-item .copy-item')[0];
                                app.copyItem(btn);
                                object[ind].title = $f('.ba-form-page').get(ind).dataset.title;
                            }
                            $this = rows.get(i);
                            clone = $this.cloneNode(true);
                            clone.querySelector('input[type="checkbox"]').checked = false;
                            clone.classList.remove('default-item');
                            clone.dataset.ind = ind;
                            clone.querySelector('input[type="checkbox"]').dataset.ind = ind;
                            if (app.edit.type == 'navigation') {
                                clone.querySelector('.sorting-title input').value = object[ind].title;
                            }
                            $f($this).after(clone);
                            if (item[0].dataset.type == 'poll') {
                                clone.querySelectorAll('.sorting-colorpicker').forEach(function(el){
                                    el.innerHTML = '<input type="text" data-type="color" data-callback="setPollColor">';
                                    let minicolor = el.querySelector('input[data-type="color"]');
                                    app.setMinicolor(minicolor);
                                    updateInput($f(minicolor), object[ind].color);
                                });
                            }
                            ind++
                        }
                    });
                    app.edit.items = object;
                    app.checkLastCheckbox(app.selector, app.edit.count);
                    break;
                case 'image':
                    uploadMode = 'checkboxImage';
                    fontBtn = {
                        rows: rows,
                        option: option,
                        keys: keys
                    }
                    checkIframe($f('#uploader-modal').attr('data-check', 'single'), 'uploader');
                    break;
                case 'delete':
                    fontBtn = {
                        type: 'deleteSelectedItems',
                        rows: rows,
                        option: option,
                        keys: keys
                    }
                    $f('#delete-dialog').modal();
                    break;
            }
        }
    });
    $f('.sorting-group-wrapper .bulk-adding-items').on('click', function(event){
        fontBtn = this;
        document.querySelector('#bulk-adding-dialog textarea').value = '';
        showDataTagsDialog('bulk-adding-dialog');
    });
    $f('.apply-bulk-items').on('click', function(){
        let modal = $f('#bulk-adding-dialog'),
            value = modal.find('textarea').val().trim();
        if (value) {
            let data = value.split('\n'),
                key = +new Date();
            data.forEach(function(title){
                if (title.trim()) {
                    createNewSelectItem(fontBtn, title, key++);
                }
            });
        }
        modal.modal('hide');
    });
    $f('.sorting-group-wrapper .add-new-select-item').on('click', function(event){
        if ('promo' in app.edit) {
            let obj = {
                    title: "Option",
                    default: false,
                    price: 0
                },
                price = renderPrice('0', app.edit.thousand, app.edit.separator, app.edit.decimals),
                item = document.querySelector('#'+app.selector),
                type = item.dataset.type,
                parent = $f(item).find('.ba-cart-shipping-row').css('display', '').find('.ba-cart-row-content'),
                wrapper = this.closest('.sorting-group-wrapper'),
                row = wrapper.querySelector('.sorting-item:last-child'),
                ind = row ? row.dataset.ind * 1 + 1 : 0,
                option = document.createElement('div'),
                child = getSortingItemHTML(obj, ind, type);
            option.className = 'ba-cart-shipping-item';
            option.innerHTML = '<label class="ba-form-radio"><input type="radio" data-price="0" name="shipping-'+
                item.id+'"><span></span></label><span class="ba-shipping-title"><span class="ba-form-shipping-title">'+
                obj.title+'</span></span><div class="ba-form-calculation-price-wrapper"><span class="field-price-currency">'+
                app.edit.symbol+'</span><span class="field-price-value">'+price+'</span></div>';
            app.edit.items[ind] = obj;
            wrapper.querySelector('.sorting-container').appendChild(child);
            parent.append(option);
            app.calculateCartTotal(app.selector, app.edit);
        } else if (app.edit.type != 'navigation') {
            createNewSelectItem(this)
        } else {
            createNewPage();
            let obj = {
                    title: $f('.ba-form-page').last().attr('data-title'),
                },
                wrapper = this.closest('.sorting-group-wrapper'),
                row = wrapper.querySelector('.sorting-item:last-child'),
                ind = row ? row.dataset.ind * 1 + 1 : 0,
                child = getSortingItemHTML(obj, ind, '');
            app.edit.items[ind] = obj;
            wrapper.querySelector('.sorting-container').appendChild(child);
        }
    });
    setSortable($f('.sorting-group-wrapper .sorting-container'), 'select', '.sortable-handle', '> .sorting-item', function(el){
        let object = {},
            item = app.edit.type == 'navigation' ? $f('.ba-forms-workspace-body') : $f('#'+app.selector),
            wrapper = app.edit.type != 'navigation' ? item.find('select, .ba-form-checkbox-group-wrapper') : item,
            options = app.edit.type != 'navigation' ? wrapper.find('option:not([hidden]), .ba-form-checkbox-wrapper') : null,
            option = null;
        if (app.edit.type == 'navigation') {
            options = item.find('.ba-form-page');
        }
        $f(el).closest('.sorting-container').find('.sorting-item').each(function(ind){
            object[ind] = $f.extend(true, {}, app.edit.items[this.dataset.ind]);
            option = options.get(this.dataset.ind * 1);
            wrapper.append(option);
            this.dataset.ind = ind;
            this.querySelector('input[type="checkbox"]').dataset.ind = ind;
        });
        if (wrapper.hasClass('ba-form-checkbox-group-wrapper')) {
            app.checkLastCheckbox(app.selector, app.edit.count);
        }
        app.edit.items = object;
        if (app.edit.type == 'navigation') {
            app.checkPageCount();
        }
    });

    $f('#jform_recaptcha option').each(function(){
        let select = $f(this).closest('.ba-settings-select-type').find('select[data-option="recaptcha"]');
        if (this.value == 'recaptcha' || this.value == 'recaptcha_invisible') {
            select.append(this);
        }
    });
    $f('.add-notifications-admin-emails').on('keyup', function(event){
        let value = this.value,
            tags = this.closest('.selected-dates-wrapper').querySelector('.selected-dates-tags'),
            group = tags.dataset.group,
            option = tags.dataset.option,
            obj = app.edit[group][option];
        if (event.keyCode == 13 && /@/g.test(value) &&  value.match(/@/g).length == 1 && !obj[value]) {
            obj[value] = true;
            let str = app.getDateTag(value, group, option);
            tags.append(str);
            this.value = '';
        }
    });
    $f('#google-maps-field-settings-dialog').on('shown', function(){
        app.createLocationMap();
    }).on('hide', function(){
        app.createGoogleMap(document.querySelector('#'+app.selector+' .ba-map-wrapper'), app.edit);
    });
    $f('.show-bootstrap-modal').on('click', function(){
        $f("#"+this.dataset.modal).modal();
    });





    $f('.media-fullscrean').on('click', function(){
        let modal = this.closest('.modal');
        if (!modal.classList.contains('fullscrean')) {
            modal.classList.add('fullscrean');
            this.classList.remove('zmdi-fullscreen');
            this.classList.add('zmdi-fullscreen-exit');
        } else {
            modal.classList.remove('fullscrean');
            this.classList.add('zmdi-fullscreen');
            this.classList.remove('zmdi-fullscreen-exit');
        }
    });
    $f('li.integrations-filter').on('click', function(){
        let modal = $f(this).closest('.modal'),
            group = '[data-group="'+this.dataset.group+'"]';
        if (this.dataset.group == '*') {
            group = '';
        }
        modal.find('li.integrations-filter.active').removeClass('active');
        if (group) {
            modal.find('.integrations-group > div:not('+group+')').hide();
        }
        modal.find('.integrations-group > div'+group+'').css('display', '');
        this.classList.add('active');
    });
    $f('.ba-integrations-search').on('input', function(){
        let search = this.value.trim().toLowerCase(),
            $this = this;
        if (!this.searchElements) {
            this.searchElements = $f('.integrations-element');
        }
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            $this.searchElements.each(function(){
                if (this.dataset.type.indexOf(search) != -1) {
                    this.style.display = '';
                } else {
                    this.style.display = 'none';
                }
            });
        }, 300);
    });
    $f('.integrations-element').on('click', function(){
        if (!app.state) {
            app.showLogin(null);
            return false;
        }
        if (this.classList.contains('require-library')) {
            app.requireIntegrationLibrary(this.dataset.type);
            return false;
        }
        let type = this.dataset.type,
            modal = $f('#integration-element-options-modal').attr('data-edit', type),
            group = modal.find('.integration-options[data-group="'+type+'"]');
        drawSubmitItemsSelect();
        setIntegrationValues(type);
        if (type == 'google_sheets') {
            app.googleSheets = $f.extend(true, {}, googleSheets);
        } else if (type == 'google_drive') {
            app.googleDrive = $f.extend(true, {}, googleDrive);
        } else if (type == 'zoho_crm') {
            app.zoho.setIntegration();
        }
        modal.find('.ba-modal-title').text(this.querySelector('span').textContent);
        modal.find('.integration-options').hide();
        $f('.deactivate-integration-element').hide();
        group.css('display', '').find('.set-group-display').each(function(){
            let action = $f(this).val() ? 'addClass' : 'removeClass',
                $this = $f(this).closest('.ba-settings-item').next();
            $this[action]('visible-subgroup');
        });
        group.find('input[data-key], select[data-key]').each(function(){
            if ($f(this).val()) {
                $f('.deactivate-integration-element').css('display', '');
                return false;
            }
        });
        modal.modal();
    });
    $f('.set-group-display').on('change', function(){
        let action = $f(this).val() ? 'addClass' : 'removeClass',
            $this = $f(this).closest('.ba-settings-item').next();
        $this[action]('visible-subgroup')
    });
    for (let ind in integrations) {
        if (ind != 'google_maps' && ind != 'telegram') {
            integrations[ind].key = JSON.parse(integrations[ind].key);
        }
        if (ind == 'mailchimp' && integrations.mailchimp.key.api_key) {
            connectMailChimp(integrations.mailchimp.key.api_key, false, true);
        } else if (ind == 'campaign_monitor' && integrations.campaign_monitor.key.api_key) {
            connectCampaignMonitor(integrations.campaign_monitor.key.api_key, integrations.campaign_monitor.key.client_id, false, true);
        } else if (ind == 'activecampaign' && integrations.activecampaign.key.api_key && integrations.activecampaign.key.account) {
            connectActivecampaign(integrations.activecampaign.key.account, integrations.activecampaign.key.api_key, false, true);
        } else if (ind == 'getresponse' && integrations.getresponse.key.api_key) {
            connectGetResponse(integrations.getresponse.key.api_key, integrations.getresponse.key.custom_fields, false, true);
        } else if (ind == 'zoho_crm') {
            app.zoho.btn = document.querySelector('.auth-zoho-crm-btn');
        } else if (ind == 'acymailing') {
            getAcymailingFields();
        } else if (ind == 'google_sheets') {
            app.google.sheets.btn = document.querySelector('.auth-sheets-btn');
        } else if (ind == 'google_drive') {
            app.google.drive.btn = document.querySelector('.auth-drive-btn');
        }
        if (ind == 'google_sheets' && integrations.google_sheets.accessToken) {
            googleSheets.accessToken = integrations.google_sheets.accessToken;
            delete(integrations.google_sheets.accessToken);
        }
        if (ind == 'google_drive' && integrations.google_drive.accessToken) {
            googleDrive.accessToken = integrations.google_drive.accessToken;
            delete(integrations.google_drive.accessToken);
        }
        setIntegrationValues(ind);
        checkIntegrationsActiveState(ind);
    }
    $f('.deactivate-integration-element').on('click', function(event){
        event.preventDefault();
        if (!this.applyBtn) {
            this.applyBtn = $f('.apply-integration-element');
        }
        let group = $f('#integration-element-options-modal').attr('data-edit'),
            groupDiv = $f('.integration-options[data-group="'+group+'"]');
        if (group == 'google_sheets') {
            app.googleSheets = {
                client_id: '',
                client_secret: '',
                accessToken: '',
                code: '',
                spreadsheet: '',
                worksheet: ''
            }
            app.google.setClient('sheets', '', '');
        } else if (group == 'google_drive') {
            app.googleDrive = {
                client_id: '',
                client_secret: '',
                accessToken: '',
                code: '',
                folder: ''
            }
            app.google.setClient('drive', '', '');
        } else if (group == 'zoho_crm') {
            app.zoho.auth = {};
            app.zoho.isEmpty();
        }
        if (group == 'pdf_submissions') {
            groupDiv.find('[data-key="enable"]').prop('checked', false);
        } else {
            groupDiv.find('[data-key]').val('');
        }
        groupDiv.find('.ba-subgroup-element').removeClass('visible-subgroup')
        this.applyBtn.trigger('click');
    });
    $f('.apply-integration-element').on('click', function(event){
        event.preventDefault();
        let modal = $f('#integration-element-options-modal'),
            group = modal.attr('data-edit'),
            array = ['mailchimp', 'campaign_monitor', 'google_sheets', 'stripe', 'zoho_crm'],
            obj = integrations[group];
        if (array.indexOf(group) != -1) {
            obj.key = {};
        }
        modal.find('.integration-options[data-group="'+group+'"] [data-key]').each(function(){
            let value = this.value;
            if (this.dataset.key == 'key') {
                obj.key = value.trim();
            } else if (this.type == 'checkbox') {
                obj.key[this.dataset.key] = this.checked;
            } else {
                obj.key[this.dataset.key] = value.trim();
            }
        });
        let data = {
            id: app.form_id,
            obj: JSON.stringify(obj)
        }
        if (group == 'google_sheets') {
            googleSheets = $f.extend(true, {}, app.googleSheets);
            let object = $f.extend(true, {}, obj);
            object.key.accessToken = googleSheets.accessToken;
            data.obj = JSON.stringify(object);
            app.google.setClient('sheets', object.key.client_id, object.key.client_secret);
        } else if (group == 'google_drive') {
            googleDrive = $f.extend(true, {}, app.googleDrive);
            let object = $f.extend(true, {}, obj);
            object.key.accessToken = googleDrive.accessToken;
            data.obj = JSON.stringify(object);
            app.google.setClient('drive', object.key.client_id, object.key.client_secret);
        }
        app.fetch('index.php?option=com_baforms&task=form.saveIntegration', data).then(function(text){
            if (group == 'google_maps' && app.googleMaps) {
                app.googleMaps = null;
                app.mapScript.remove();
                window.google = null;
                app.loadGoogleMaps();
            } else if (group == 'zoho_crm') {
                integrations.zoho_auth.key = JSON.stringify(app.zoho.auth);
                data.obj = JSON.stringify(integrations.zoho_auth);
                app.fetch('index.php?option=com_baforms&task=form.saveIntegration', data);
            }
            checkIntegrationsActiveState(group);
            modal.modal('hide');
        });
    });

    $f('.mailchimp-api-key').on('input', function(){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            connectMailChimp($this.value.trim(), true, false);
        }, 500);
    });
    $f('.mailchimp-list').on('change', function(){
        let api_key = $f('.mailchimp-api-key').val().trim(),
            list_id = $f(this).val();
        if (mailchimp[api_key][list_id]) {
            drawMailChimpFields(mailchimp[api_key][list_id], false)
        } else {
            getMailChimpFields(api_key, list_id, false);
        }
    });








    $f('.connect-activecampaign').each(function(){
        let $this = this;
        this.campaign = {};
        $f('.connect-activecampaign').each(function(){
            $this.campaign[this.dataset.key] = this;
        });
    }).on('input', function(event){
        let obj = this.campaign;
        if (event.target.localName == 'input') {
            let $this = this;
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                if ((obj.api_key.value.trim() && obj.account.value.trim())) {
                    connectActivecampaign(obj.account.value.trim(), obj.api_key.value.trim(), true);
                }
            }, 500);
        }
    });
    $f('.activecampaign-list').on('change', function(){
        $f('.activecampaign-fields').addClass('visible-subgroup');
    });




    $f('.connect-campaign-monitor').each(function(){
        let $this = this;
        this.campaign = {};
        $f('.connect-campaign-monitor').each(function(){
            $this.campaign[this.dataset.key] = this;
        });
    }).on('input', function(event){
        let obj = this.campaign;
        if (event.target.localName == 'input') {
            let $this = this;
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                if ((obj.api_key.value.trim() && obj.client_id.value.trim())) {
                    connectCampaignMonitor(obj.api_key.value.trim(), obj.client_id.value.trim(), true);
                }
            }, 500);
        } else {
            getCampaignMonitorFields(obj.api_key.value.trim(), obj.client_id.value.trim(), obj.list_id.value);
        }
    });


    $f('.connect-getresponse').each(function(){
        let $this = this;
        this.getresponse = {};
        $f('.connect-getresponse').each(function(){
            $this.getresponse[this.dataset.key] = this;
        });
    }).on('input', function(event){
        let obj = this.getresponse;
        if (event.target.localName == 'input' && event.target.type == 'text') {
            let $this = this;
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                connectGetResponse(obj.api_key.value.trim(), obj.custom_fields.checked, true);
            }, 500);
        } else if (event.target.localName == 'select') {
            getResponseFields(obj.api_key.value.trim(), obj.list_id.value, obj.custom_fields.checked);
        } else {
            $f('.getresponse-custom-fields')[obj.custom_fields.checked ? 'addClass' : 'removeClass']('visible-subgroup');
        }
    });

    $f('.connect-zoho-crm').on('input', function(){
        app.zoho[this.dataset.key] = this.value.trim();
        clearTimeout(app.zoho.delay);
        app.zoho.delay = setTimeout(function(){
            app.zoho.isEmpty();
        }, 500);
    });


    $f('.integration-options[data-group="pdf_submissions"] input[data-key="enable"]').on('change', function(){
        $f(this).closest('.integration-options')
            .find('.ba-subgroup-element')[this.checked ? 'addClass' : 'removeClass']('visible-subgroup');
    });


    $f('#condition-logic-modal').on('show', function(){
        let modal = $f(this),
            ul = modal.find('.ba-folder-tree ul').empty();
        modal.find('.ba-settings-group').hide();
        modal.find('label.active[data-action]').removeClass('active');
        modal.find('label[data-action="publish"] span[data-action]').each(function(){
            this.style.display = this.dataset.action == 'unpublish' ? '' : 'none';
        });
        modal.find('label[data-action="publish"] i')[0].className = 'zmdi zmdi-eye-off';
        app.conditionLogic.forEach(function(el, i){
            let li = templates['condition-logic-filter'].content.cloneNode(true);
            li.querySelector('li').dataset.key = i;
            li.querySelector('.conditional-logic-title input').value = el.title;
            li.querySelector('.conditional-logic-icons').innerHTML = el.publish ? '' : '<i class="zmdi zmdi-eye-off"></i>';
            ul.append(li);
        });
        app.buttonsPrevent();
    });
    $f('#condition-logic-modal .add-new-condition-logic').on('click', function(){
        if (!app.state) {
            app.showLogin(null);
            return false;
        }
        if (!this.ul) {
            this.ul = $f('#condition-logic-modal .ba-folder-tree ul');
        }
        let li = templates['condition-logic-filter'].content.cloneNode(true),
            n = app.conditionLogic.length,
            obj = {
                title: li.querySelector('input[type="text"]').value,
                publish: true,
                operation: 'AND',
                when: [{field:'', state: '', value: ''}],
                do: [{field:'', action: ''}]
            };
        app.conditionLogic.push(obj);
        li.querySelector('li').dataset.key = n;
        this.ul.append(li);
        app.buttonsPrevent();
    });
    $f('#condition-logic-modal ul').on('input', '.conditional-logic-title input', function(event){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.conditionLogic[$this.closest('li').dataset.key].title = $this.value.trim();
        }, 500);
    }).on('change', 'input[type="checkbox"]', function(){
        let array = [];
        $f('#condition-logic-modal li input[type="checkbox"]').each(function(){
            if (this.checked) {
                array.push(this);
            }
        });
        if (array.length) {
            $f('#condition-logic-modal label[data-action]').addClass('active');
        } else {
            $f('#condition-logic-modal label[data-action]').removeClass('active');
        }
        if (array.length == 1) {
            let publish = app.conditionLogic[array[0].closest('li').dataset.key].publish;
            $f('#condition-logic-modal label[data-action="publish"]').addClass('active').each(function(){
                this.querySelector('i').className = publish ? 'zmdi zmdi-eye-off' : 'zmdi zmdi-eye';
                this.querySelector('span[data-action="publish"]').style.display = publish ? 'none' : '';
                this.querySelector('span[data-action="unpublish"]').style.display = publish ? '' : 'none';
            });
        } else {
            $f('#condition-logic-modal label[data-action="publish"]').removeClass('active').each(function(){
                this.querySelector('i').className = 'zmdi zmdi-eye-off';
                this.querySelector('span[data-action="publish"]').style.display = 'none';
                this.querySelector('span[data-action="unpublish"]').style.display = '';
            });
        }
    }).on('click', 'li', function(){
        if (!app.state) {
            app.showLogin(null);
            return false;
        }
        let modal = $f('#condition-logic-modal'),
            group = modal.find('.ba-settings-group'),
            key = this.dataset.key;
        app.edit = app.conditionLogic[key];
        group.find('.condition-logic-horizontal-fields-wrapper').remove();
        for (let i = 0; i < app.edit.when.length; i++) {
            app.appendConditionRow(i, 'when');
        }
        for (let i = 0; i < app.edit.do.length; i++) {
            app.appendConditionRow(i, 'do');
        }
        modal.find('li.active').removeClass('active');
        this.classList.add('active');
        changeSelected(modal.find('.conditions-matches-operation'), app.edit.operation);
        $f('.conditions-matches-wrapper').css('display', app.edit.when.length > 1 ? '' : 'none');
        group.css('display', '');
    });
    $f('#condition-logic-modal label[data-action]').on('click', function(){
        if (this.classList.contains('active')) {
            let keys = [],
                rows = $f('#condition-logic-modal ul li');
            $f('#condition-logic-modal ul li input[type="checkbox"]').each(function(){
                if (this.checked) {
                    keys.push(this.closest('li').dataset.key * 1);
                }
            });
            switch (this.dataset.action) {
                case 'publish':
                    let publish = !app.conditionLogic[keys[0]].publish;
                    app.conditionLogic[keys[0]].publish = publish;
                    rows[keys[0]].querySelector('.conditional-logic-icons').innerHTML = publish ? '' : '<i class="zmdi zmdi-eye-off"></i>';
                    this.querySelector('i').className = publish ? 'zmdi zmdi-eye-off' : 'zmdi zmdi-eye';
                    this.querySelector('span[data-action="publish"]').style.display = publish ? 'none' : '';
                    this.querySelector('span[data-action="unpublish"]').style.display = publish ? '' : 'none';
                    app.checkConditionLogic();
                    break;
                case 'copy':
                    let object = $this = null,
                        ind = 0,
                        array = [];
                    rows.each(function(i){
                        object = $f.extend(true, {}, app.conditionLogic[i]);
                        array.push(object);
                        this.dataset.key = ind++;;
                        if (keys.indexOf(i) != -1) {
                            object = $f.extend(true, {}, app.conditionLogic[i]);
                            array.push(object);
                            clone = this.cloneNode(true);
                            clone.querySelector('input[type="checkbox"]').checked = false;
                            clone.dataset.key = ind++;
                            $f(this).after(clone);
                        }
                    });
                    app.conditionLogic = array;
                    $f('#condition-logic-modal .ba-settings-group').hide();
                    break;
                case 'delete':
                    fontBtn = {
                        type: 'deleteSelectedCondition',
                        rows: rows,
                        keys: keys
                    }
                    $f('#delete-dialog').modal();
                    break;
            }
        }
    });
    $f('#condition-logic-modal .add-new-when-condition').on('click', function(){
        app.edit.when.push({field:'', state: '', value: ''});
        app.appendConditionRow(app.edit.when.length - 1, 'when');
        $f('.conditions-matches-wrapper').css('display', app.edit.when.length > 1 ? '' : 'none');
    });
    $f('#condition-logic-modal .add-new-do-condition').on('click', function(){
        app.edit.do.push({field:'', action: ''});
        app.appendConditionRow(app.edit.do.length - 1, 'do');
    });
    $f('#condition-logic-modal .ba-settings-group').on('change input', '.condition-logic-horizontal-fields-wrapper', function(event){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            app.edit[$this.dataset.ind][$this.dataset.key][event.target.dataset.key] = event.target.value;
            if ($this.dataset.ind == 'when' && event.target.dataset.key == 'field') {
                app.edit.when[$this.dataset.key].value = '';
                app.checkConditionFieldType($this, app.edit[$this.dataset.ind][$this.dataset.key]);
            } else  if ($this.dataset.ind == 'do' && event.target.dataset.key == 'action') {
                app.edit.do[$this.dataset.key].field = '';
                app.checkConditionDoAction($this, app.edit[$this.dataset.ind][$this.dataset.key]);
            }
            if (event.target.dataset.key == 'state') {
                app.checkConditionWhenState($this, app.edit[$this.dataset.ind][$this.dataset.key]);
            }
            app.checkConditionLogic();
        }, 300);
    }).on('change', '.conditions-matches-operation', function(){
        app.edit.operation = this.value;
        app.checkConditionLogic();
    }).on('click', '.delete-condition-row', function(){
        fontBtn = {
            type: 'deleteConditionRow',
            parent: this.closest('.ba-settings-group'),
            row: this.closest('.condition-logic-horizontal-fields-wrapper')
        }
        $f('#delete-dialog').modal();
    });

    $f('#templates-modal .integrations-group').on('click', '.templates-element', function(event){
        event.preventDefault();
        if (this.dataset.id) {
            $f.ajax({
                type:"POST",
                dataType:'text',
                data: {
                    id: this.dataset.id
                },
                url:"index.php?option=com_baforms&task=form.getFormsTemplate",
                success: function(msg){
                    try {
                        let obj = JSON.parse(msg),
                            div = document.createElement('div'),
                            body = document.querySelector('.ba-forms-workspace-body'),
                            clones = [],
                            data = {
                                pages:{},
                                fields:{}
                            }
                            pages = null;
                        div.innerHTML = obj.html;
                        pages = Array.prototype.slice.call(div.querySelectorAll('.ba-form-page'));
                        pages.forEach(function(page){
                            let $this = page.querySelector('.ba-form-page > .ba-edit-item .copy-item'),
                                clone = app.copyItem($this, obj.items),
                                pageFields = page.querySelectorAll('.ba-form-field-item'),
                                cloneFields = clone.querySelectorAll('.ba-form-field-item');
                            data.pages[page.id] = clone.id;
                            pageFields.forEach(function(field, i){
                                data.fields[field.id.replace('baform-', '')] = cloneFields[i].id.replace('baform-', '');
                            });
                            clones.push(clone);
                        });
                        clones.forEach(function(clone){
                            clone.querySelectorAll('.ba-form-calculation-field').forEach(function(field){
                                let formula = app.items[field.id].formula,
                                    matches = formula.match(/\[Field ID=\d+\]/g);
                                if (matches) {
                                    for (let i = 0; i < matches.length; i++) {
                                        let match = matches[i].match(/\d+/g);
                                        if (data.fields[match]) {
                                            formula = formula.replace(matches[i], '[Field ID='+data.fields[match]+']');
                                        }
                                    }
                                    app.items[field.id].formula = formula;
                                }
                            });
                            body.append(clone);
                        });
                        app.prepareFormsPages();
                        app.buttonsPrevent($f('.ba-forms-workspace').find('a, input[type="submit"], button'));
                    } catch (e) {
                        console.info(e);
                        console.info(msg);
                    }
                    $f('#templates-modal').modal('hide');
                }
            });
        } else {
            $f.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_baforms&task=forms.checkFormsState",
                success: function(msg){
                    var flag = true,
                        obj;
                    if (msg) {
                        obj = JSON.parse(msg);
                        flag = !obj.data;
                    }
                    if (flag) {
                        app.showLogin(true);
                    } else {
                        var url = 'https://www.balbooa.com/demo/index.php?',
                            domain = window.location.host.replace('www.', ''),
                            script = document.createElement('script');
                        domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
                        url += 'option=com_baupdater&task=baforms.checkFormsUser';
                        url += '&data='+obj.data;
                        if (domain[domain.length - 1] != '/') {
                            domain += '/';
                        }
                        url += '&domain='+window.btoa(domain);
                        script.onload = function(){
                            if (formsResponse) {
                                installTemplates();
                                document.body.classList.remove('disabled-licence');
                                app.state = true;
                            } else {
                                app.showLogin(true);
                            }
                        }
                        script.src = url;
                        document.head.appendChild(script);
                    }
                }
            });
        }
    });
    $f('#login-modal').on('show', function(){
        var url = 'https://www.balbooa.com/demo/index.php?option=com_baupdater&view=baforms',
            domain = window.location.host.replace('www.', '');
            iframe = document.createElement('iframe');
        domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
        if (domain[domain.length - 1] != '/') {
            domain += '/';
        }
        url += '&domain='+window.btoa(domain);
        iframe.onload = function(){
            this.classList.add('iframe-loaded');
        }
        iframe.src = url;
        $f(this).find('.modal-body').html(iframe);
        window.addEventListener("message", listenMessage, false);
    }).on('hide', function(){
        window.removeEventListener("message", listenMessage, false);
    });

    $f('.lightbox-position-wrapper div').on('click', function(){
        let workspace = document.querySelector('.ba-forms-workspace').classList;
        workspace.remove('lightbox-position-'+app.edit.position);
        this.closest('.lightbox-position-wrapper').querySelector('.active').classList.remove('active');
        this.classList.add('active');
        app.edit.position = this.dataset.value;
        workspace.add('lightbox-position-'+app.edit.position);
    });

    $f('.trigger-picker-modal').on('click', function(){
        fontBtn = this;
        $f('#'+this.dataset.modal).modal();
        showDataTagsDialog(this.dataset.modal);
    });
    $f('.ba-modal-list-picker').on('click', 'li[data-value]', function(){
        $f(this).closest('.modal').modal('hide');
        fontBtn.querySelector('input[type="text"]').value = this.dataset.title ? this.dataset.title : this.textContent;
        $f(fontBtn).find('input[type="hidden"]').val(this.dataset.value).trigger('change');
    });
    $f('#google-fonts-dialog ul').each(function(){
        for (let ind in googleFonts) {
            let li = document.createElement('li')
            li.dataset.value = ind;
            li.textContent = googleFonts[ind];
            this.append(li);
        }
    });
    if ('EyeDropper' in window) {
        app.eyeDropper = new EyeDropper();
        $f('#color-variables-dialog i.zmdi-eyedropper').on('click', function(){
            app.eyeDropper.open().then(function(result){
                setMinicolorsColor(result.sRGBHex);
                let picker = $f('.variables-color-picker'),
                    rgba = picker.minicolors('rgbaString');
                fontBtn.value = result.sRGBHex;
                picker.closest('#color-picker-cell').find('.minicolors-opacity').val(1);
                fontBtn.dataset.rgba = rgba;
                $f(fontBtn).trigger('minicolorsInput').next().find('.minicolors-swatch-color')
                    .css('background-color', rgba).closest('.minicolors').next()
                    .find('.minicolors-opacity').val(1).removeAttr('readonly');
            });
        });
    }
    setSortable($f('.ba-form-page > .ba-page-items'), 'row', '> .ba-form-row > .ba-edit-item .edit-settings', '> .ba-form-row');
    setSortable($f('.ba-form-column'), 'items', '> .ba-form-field-item > .ba-edit-item .edit-settings', '> .ba-form-field-item');
    app.prepareFormsPages();
    app.prepareNumbers();
    app.setCalculator();
    app.prepareShortCodes();
    app.setCalendarEvents()
    app.selectIconDialog();
    app.setMinicolors();
    app.setDesignCssVariables();
    app.setPopularFontsFamily();
    app.setDesignSettings();
    app.setAddFields();
    app.setMediaManager();
    app.setTabsAnimation();
    app.ckeLink();
    app.customSelect();
    app.prepareTemplates();
    app.buttonsPrevent();
    app.prepareSave();
    app.renderCKE();
    app.hidePreloader();
    app.cp.prepare();
    app.loadDraggable();
    app.loadResizable();
    app.google.setSheetsIntegration();
    app.google.setDriveIntegration();
    app.zoho.setIntegration();
});

app.createTemplate = function(event){
    let group = $f('.select-template-group').val();
    if (group) {
        $f.ajax({
            type:"POST",
            dataType:'text',
            data: {
                id: app.form_id,
                group: group
            },
            url:"index.php?option=com_baforms&task=form.createTemplate",
            success: function(msg){
                var a = document.createElement('a');
                a.href = msg;
                a.setAttribute('download', '')
                document.body.append(a);
                a.click();
                $f('#create-template-modal').modal('hide');
            }
        })
    }
}

app.createTemplateData = function(){
    if (document.querySelector('#create-template-modal')) {
        return false;
    }
    var btn = '<span class="open-create-template" onclick="$f(\'#create-template-modal\').modal();">'+
                    '<a href="#"><span class="zmdi zmdi-assignment"></span></a>'+
                    '<span class="ba-tooltip ba-right ba-hide-element">Create Template</span>'+
                '</span>',
        html = '<div id="create-template-modal" class="mce-ba-modal ba-modal-sm modal hide" style="display:none;">'+
                    '<div class="modal-body">'+
                        '<h3>Create Template</h3>'+
                        '<select class="select-template-group"></select>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                        '<a href="#" class="ba-btn" data-dismiss="modal">Cancel</a>'+
                        '<a href="#" class="ba-btn-primary active-button" id="create-template"'+
                        ' onclick="app.createTemplate(); return false;">Create</a>'+
                    '</div>'+
                '</div>';
    $f('body').append(html);
    $f('.ba-sidebar > div:nth-child(2)').append(btn);
    let select = $f('.select-template-group')
    $f('#templates-modal li').each(function(){
        if (this.dataset.group != '*') {
            select.append('<option value="'+this.dataset.group+'">'+this.textContent+'</option>');
        }
    });
    $f('.open-create-template').find('.ba-tooltip').each(function(){
        app.setTooltip($f(this).parent());
    });
}