/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.fieldEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#field-settings-dialog');
    $g('#field-settings-dialog .active').removeClass('active');
    $g('#field-settings-dialog a[href="#field-general-options"]').parent().addClass('active');
    $g('#field-general-options').addClass('active');
    setPresetsList($g('#field-settings-dialog'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    if (app.edit.type == 'field') {
        $g('#field-settings-dialog .fields-group-plugin-options').hide();
        $g('#field-settings-dialog .fields-plugin-options').css('display', '');
        $g('#field-settings-dialog .field-label').val(app.edit.label);
        $g('#field-settings-dialog .field-admin-label').val(app.edit.options.label);
        $g('#field-settings-dialog .field-admin-description').val(app.edit.options.description);
        $g('#field-settings-dialog .field-description').val(app.edit.description);
        value = app.edit.icon ? app.edit.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '') : '';
        $g('#field-settings-dialog .select-field-icon').val(value);
        $g('#field-settings-dialog input[data-option="required"]').prop('checked', app.edit.required);
        $g('#field-settings-dialog .field-type-select input[type="hidden"]').val(app.edit.options.type);
        value = $g('#field-settings-dialog .field-type-select li[data-value="'+app.edit.options.type+'"]').text().trim();
        $g('#field-settings-dialog .field-type-select input[readonly]').val(value);
        $g('#field-settings-dialog div[class*="-type-options"]').hide();
        $g('#field-settings-dialog .field-'+app.edit.options.type+'-type-options').css('display', '');
        setFieldTypeOptions();
    } else {
        $g('#field-settings-dialog .fields-group-plugin-options').css('display', '');
        $g('#field-settings-dialog .fields-plugin-options').hide();
        drawFieldGroupSortingList();
    }
    app.setDefaultState('#field-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#field-settings-dialog .margin-settings-group');
    $g('#field-settings-dialog .select-field-text-type-html-tag input[type="hidden"]').val(app.edit.tag);
    value = $g('#field-settings-dialog .select-field-text-type-html-tag li[data-value="'+app.edit.tag+'"]').text().trim();
    $g('#field-settings-dialog .select-field-text-type-html-tag input[type="text"]').val(value);
    app.setDefaultState('#field-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#field-settings-dialog .padding-settings-group');
    app.setDefaultState('#field-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#field-settings-dialog .border-settings-group');
    $g('#field-settings-dialog .ba-style-custom-select input[type="hidden"]').val('title');
    $g('#field-settings-dialog .ba-style-custom-select input[readonly]').val(app._('LABEL'));
    showBaStyleDesign('title', document.querySelector('#field-settings-dialog .ba-style-custom-select'));
    value = app.getValue('icons', 'size');
    app.setLinearInput(modal.find('[data-group="icons"][data-option="size"]'), value);
    value = app.getValue('icons', 'color');
    updateInput($g('#field-settings-dialog input[data-option="color"][data-group="icons"]'), value);
    $g('#field-settings-dialog .field-label-position-select input[type="hidden"]').val(app.edit.layout.position);
    value = $g('#field-settings-dialog .field-label-position-select li[data-value="'+app.edit.layout.position+'"]').text().trim();
    $g('#field-settings-dialog .field-label-position-select input[type="text"]').val(value);
    hideLabelPositionOptions();
    setDisableState('#field-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#field-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#field-settings-dialog').modal();
    }, 150);
}

function restoreDefaultFieldOptions(type, label, description)
{
    var obj = {
        label: label,
        description: description,
        type: type
    };
    switch (obj.type) {
        case 'textarea':
            obj.texteditor = false;
            break;
        case 'radio':
        case 'checkbox':
        case 'select':
            obj.items = {
                "0" : {
                    title: 'Value',
                    key : +new Date()
                }
            };
            break;
        case 'file':
            obj.title = 'Download'
            obj.size = 1024;
            obj.source = '';
            obj.types = 'csv,doc,gif,ico,jpg,jpeg,pdf,png,txt,xls,svg,mp4,webp';
            break;
        case 'range':
            obj.min = 0;
            obj.max = 100;
            break;
        case 'price':
            obj.symbol = '$';
            obj.position = '';
            obj.thousand = ',';
            obj.separator = '.';
            obj.decimals = 2
            break;
        case 'url':
            obj.target = '_blank';
            obj.download = '';
            break;
    }

    return obj;
}

function setFieldContentValue(options)
{
    var value = 'Value';
    if (options.type == 'file') {
        value = '<a>'+options.title+'</a>';
    } else if (options.type == 'price') {
        let price = '1'+options.thousand+'000';
        if (options.decimals != 0) {
            price += options.separator;
            for (let i = 0; i < options.decimals; i++) {
                price += '0';
            }
        }
        value = '<span class="field-price-wrapper '+options.position+'"><span class="field-price-currency">'+options.symbol+'</span>'+
            '<span class="field-price-value">'+price+'</span></span>';
    } else if (options.type == 'url') {
        value = '<a>Value</a>';
    }

    return value;
}

function drawFieldSortingList()
{
    var container = $g('#field-settings-dialog .fields-plugin-options .sorting-container').empty();
    sortingList = [];
    for (var ind in app.edit.options.items) {
        var obj = $g.extend(true, {}, app.edit.options.items[ind]);
        sortingList.push(obj);
        container.append(addFieldSortingList(obj, sortingList.length - 1));
    }
}

function addFieldSortingList(obj, key)
{
    var str = '<div class="sorting-item" data-key="'+key;
    str += '"><div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
    str += '<div class="sorting-title">'+obj.title+'</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit"></i></span>';
    str += '<span><i class="zmdi zmdi-copy"></i></span>';
    str += '<span><i class="zmdi zmdi-delete"></i></span></div></div>';

    return str;
}

function drawFieldGroupSortingList()
{
    var container = $g('#field-settings-dialog .fields-group-plugin-options .sorting-container').empty();
    sortingList = [];
    for (var ind in app.edit.items) {
        var obj = $g.extend(true, {}, app.edit.items[ind]);
        sortingList.push(obj);
        container.append(addFieldGroupSortingList(obj, sortingList.length - 1));
    }
}

function addFieldGroupSortingList(obj, key)
{
    var str = '<div class="sorting-item" data-key="'+key;
    str += '"><div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
    str += '<div class="sorting-title">'+obj.label+'</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit"></i></span>';
    str += '<span><i class="zmdi zmdi-copy"></i></span>';
    str += '<span><i class="zmdi zmdi-delete"></i></span></div></div>';

    return str;
}

function setFieldTypeOptions()
{
    if (app.edit.options.type == 'textarea') {
        $g('#field-settings-dialog input[data-option="texteditor"][data-group="options"]').prop('checked', app.edit.options.texteditor);
    } else if (app.edit.options.type == 'radio' || app.edit.options.type == 'select' || app.edit.options.type == 'checkbox') {
        drawFieldSortingList();
    } else if (app.edit.options.type == 'file' || app.edit.options.type == 'range') {
        $g('#field-settings-dialog .field-'+app.edit.options.type+'-type-options input[data-group="options"]').each(function(){
            this.value = app.edit.options[this.dataset.option];
        });
        $g('#field-settings-dialog .select-field-upload-source input[type="hidden"]').val(app.edit.options.source);
        value = app.edit.options.source == 'desktop' ? app._('DESKTOP') : app._('MEDIA_MANAGER');
        $g('#field-settings-dialog .select-field-upload-source input[type="text"]').val(value);
    } else if (app.edit.options.type == 'price') {
        $g('#field-settings-dialog .field-'+app.edit.options.type+'-type-options input[data-group="options"]').each(function(){
            this.value = app.edit.options[this.dataset.option];
            if (this.type == 'hidden') {
                let text = this.closest('.ba-custom-select').querySelector('li[data-value="'+this.value+'"]').textContent.trim();
                this.previousElementSibling.value = text;
            } else if (this.type == 'number' || this.type == 'text') {
                app.setLinearInput($g(this), this.value);
            }
        });
    } else if (app.edit.options.type == 'url') {
        $g('#field-settings-dialog .field-url-type-options input[data-option]').each(function(){
            this.value = app.edit.options[this.dataset.option];
            var text = $g(this).closest('.ba-custom-select').find('li[data-value="'+this.value+'"]').text().trim();
            this.previousElementSibling.value = text;
        });
    }
}

function renderGroupRadioOption(obj)
{
    let str = '<div class="sorting-item" data-key="'+obj.key;
    str += '"><div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
    str += '<div class="sorting-title">'+obj.title+'</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit"></i></span>';
    str += '<span><i class="zmdi zmdi-copy"></i></span>';
    str += '<span><i class="zmdi zmdi-delete"></i></span></div></div>';

    return str;
}

function drawGroupRadioFieldSortingList(obj)
{
    let container = $g('#group-field-item-dialog .sorting-container').empty();
    for (let ind in obj.options.items) {
        container.append(renderGroupRadioOption(obj.options.items[ind]));
    }
}

function setFieldGroupTypeOptions(obj)
{
    if (obj.options.type == 'textarea') {
        $g('#group-field-item-dialog input[data-element="texteditor"]').prop('checked', obj.options.texteditor);
    } else if (obj.options.type == 'radio' || obj.options.type == 'select' || obj.options.type == 'checkbox') {
        drawGroupRadioFieldSortingList(obj);
    } else if (obj.options.type == 'file' || obj.options.type == 'range') {
        $g('#group-field-item-dialog .field-'+obj.options.type+'-type-options input[data-element]').each(function(){
            this.value = obj.options[this.dataset.element];
            if (this.dataset.element == 'source') {
                var value = obj.options[this.dataset.element] == 'desktop' ? app._('DESKTOP') : app._('MEDIA_MANAGER');
                $g(this).prev().val(value);
            }
        });
    } else if (obj.options.type == 'price') {
        $g('#group-field-item-dialog .field-price-type-options input[data-element]').each(function(){
            this.value = obj.options[this.dataset.element];
            if (this.type == 'hidden') {
                let text = this.closest('.ba-custom-select').querySelector('li[data-value="'+this.value+'"]').textContent.trim();
                this.previousElementSibling.value = text;
            } else if (this.type == 'number' || this.type == 'text') {
                app.setLinearInput($g(this), this.value)
            }
        });
    } else if (obj.options.type == 'url') {
        $g('#group-field-item-dialog .field-url-type-options input[data-element]').each(function(){
            this.value = obj.options[this.dataset.element];
            var text = $g(this).closest('.ba-custom-select').find('li[data-value="'+this.value+'"]').text().trim();
            this.previousElementSibling.value = text;
        });
    }
}

function hideLabelPositionOptions()
{
    $g('#field-settings-dialog label[data-option="text-align"]')
        .removeClass('position-right position-left').addClass(app.edit.layout.position.replace('ba-label-', ''));
}

function renderNewFieldElement(obj)
{
    let str = '<div class="ba-field-wrapper '+app.edit.layout.position+'" data-id="'+obj.field_key+'">';
    str += '<div class="ba-field-label'+(!obj.label && !obj.description ? ' empty-content' : '')+'">';
    str += (obj.icon ? '<i class="'+obj.icon+'"></i>' : '');
    str += '<'+app.edit.tag+'>'+obj.label+'</'+app.edit.tag+'>';
    if (obj.description) {
        str += '<span class="field-description-wrapper"><i class="zmdi zmdi-help-outline"></i>';
        str += '<span class="ba-tooltip">'+obj.description+'</span></span>';
    }
    str += '</div><div class="ba-field-content">'+setFieldContentValue(obj.options)+'</div></div>';
    

    return str;
}

function removeFieldDescriptionHTML(html)
{
    let div = document.createElement('div');
    div.innerHTML = html;

    return div.textContent;
}

$g('#field-settings-dialog .field-price-type-options input[data-group="options"]').on('change input', function(){
    app.edit.options[this.dataset.option] = this.value;
    let value = setFieldContentValue(app.edit.options);
    app.editor.$g(app.selector+' .ba-field-content').html(value);
});

$g('#field-settings-dialog .select-field-icon').on('click', function(){
    uploadMode = 'addFieldIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('#field-settings-dialog .reset-field-icon i').on('click', function(){
    app.editor.$g(app.selector+' .ba-field-label > i').remove();
    app.edit.icon = '';
    $g('#field-settings-dialog .select-field-icon').val('');
    app.addHistory();
});

$g('#field-settings-dialog .field-label-position-select').on('customAction', function(){
    app.edit.layout.position = this.querySelector('input[type="hidden"]').value;
    hideLabelPositionOptions();
    app.sectionRules();
    app.addHistory();
});

$g('#field-settings-dialog .field-url-type-options .ba-custom-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]');
    app.edit.options[input.dataset.option] = input.value;
    app.addHistory();
});

$g('#field-settings-dialog .field-file-type-options input[data-group="options"]').on('input', function(){
    app.edit.options[this.dataset.option] = this.value.trim();
    clearTimeout(this.delay);
    if (this.dataset.option == 'title') {
        app.editor.$g(app.selector+' .ba-field-content a').text(app.edit.options.title);
    }
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#field-settings-dialog .field-label').on('input', function(){
    var label = this.value.trim(),
        item = app.editor.$g(app.selector+' .ba-field-label');
    item.find(app.edit.tag).text(label);
    app.edit.label = label;
    if (app.edit.label || app.edit.description) {
        item.removeClass('empty-content');
    } else {
        item.addClass('empty-content');
    }
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#field-settings-dialog .field-admin-label').on('input', function(){
    app.edit.options.label = this.value.trim();
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#field-settings-dialog .field-description').on('input', function(){
    var item = item = app.editor.$g(app.selector+' .ba-field-label'),
        span = app.editor.document.querySelector(app.selector+' .ba-field-label > .field-description-wrapper');
    app.edit.description = removeFieldDescriptionHTML(this.value.trim());
    if (!span && app.edit.description) {
        span = document.createElement('span');
        span.className = 'field-description-wrapper';
        span.innerHTML = '<i class="zmdi zmdi-help-outline"></i><span class="ba-tooltip"></span>';
        app.editor.$g(app.selector+' .ba-field-label').append(span);
    } else if (span && !app.edit.description) {
        $g(span).remove();
    }
    if (app.edit.description) {
        span.querySelector('.ba-tooltip').textContent = app.edit.description;
    }
    if (app.edit.label || app.edit.description) {
        item.removeClass('empty-content');
    } else {
        item.addClass('empty-content');
    }
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#field-settings-dialog .field-type-select').on('customAction', function(){
    app.edit.options.type = this.querySelector('input[type="hidden"]').value;
    $g('#field-settings-dialog div[class*="-type-options"]').hide();
    $g('#field-settings-dialog .field-'+app.edit.options.type+'-type-options').css('display', '');
    app.edit.options = restoreDefaultFieldOptions(app.edit.options.type, app.edit.options.label, app.edit.options.description);
    setFieldTypeOptions();
    var title = setFieldContentValue(app.edit.options);
    app.editor.$g(app.selector+' .ba-field-content').html(title);
    app.addHistory();
});

$g('#group-field-item-dialog .field-type-select').on('customAction', function(){
    let type = this.querySelector('input[type="hidden"]').value,
        obj = {
            options: restoreDefaultFieldOptions(type, '', '')
        };
    $g('#group-field-item-dialog div[class*="-type-options"]').hide();
    $g('#group-field-item-dialog .field-'+type+'-type-options').css('display', '');
    setFieldGroupTypeOptions(obj);
});

$g('#field-settings-dialog .select-field-text-type-html-tag').on('customAction', function(){
    let old = app.edit.tag;
    app.edit.tag = this.querySelector('input[type="hidden').value;
    app.editor.$g(app.selector+' .ba-field-label > '+old).each(function(){
        let tag = document.createElement(app.edit.tag);
        tag.textContent = this.textContent;
        $g(this).replaceWith(tag);
    });
    app.addHistory();
});

$g('#add-new-field-element-modal .field-element-title').on('input', function(){
    if (this.value.trim()) {
        $g('#apply-new-field-element').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#apply-new-field-element').removeClass('active-button').addClass('disable-button').attr('data-key', sortingList.length);
    }
});

$g('#apply-new-field-element').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        let type = $g('#add-new-field-element-modal .field-element-type-select input[type="hidden"]').val(),
            obj = {
                "label": $g('#add-new-field-element-modal .field-element-title').val().trim(),
                "description": "",
                "icon": "",
                "required": false,
                "field_key": 'item-'+(+new Date()),
                "options": restoreDefaultFieldOptions(type, '', '')
            },
            str = renderNewFieldElement(obj);
        app.edit.items[this.dataset.key] = obj;
        app.editor.$g(app.selector+' .ba-field-group-wrapper').append(str);
        drawFieldGroupSortingList();
        $g('#add-new-field-element-modal').modal('hide');
    }
})

$g('#field-settings-dialog .fields-plugin-options .add-new-item .zmdi-plus-circle').on('click', function(){
    $g('.radio-field-value').val('');
    $g('#apply-new-radio-value').removeClass('active-button').addClass('disable-button').attr('data-key', sortingList.length);
    $g('#edit-radio-field-element-modal').modal();
});

$g('#field-settings-dialog .fields-group-plugin-options .add-new-item .zmdi-plus-circle').on('click', function(){
    $g('#add-new-field-element-modal .field-element-title').val('');
    $g('#add-new-field-element-modal .field-element-type-select input[type="hidden"]').val('text').prev().val(app._('TEXT'));
    $g('#apply-new-field-element').removeClass('active-button').addClass('disable-button').attr('data-key', sortingList.length);
    $g('#add-new-field-element-modal').modal();
});

$g('#field-settings-dialog .fields-plugin-options .sorting-container').on('click', '.zmdi-edit', function(){
    var key = $g(this).closest('.sorting-item').attr('data-key') * 1;
    $g('.radio-field-value').val(app.edit.options.items[key].title);
    $g('#apply-new-radio-value').addClass('active-button').removeClass('disable-button').attr('data-key', key);
    $g('#edit-radio-field-element-modal').modal();
});

$g('#field-settings-dialog .fields-group-plugin-options .sorting-container').on('click', '.zmdi-edit', function(){
    let key = $g(this).closest('.sorting-item').attr('data-key') * 1,
        obj = app.edit.items[key],
        modal = $g('#group-field-item-dialog');
    modal.find('.field-label').val(obj.label);
    modal.find('.field-admin-label').val(obj.options.label);
    modal.find('.field-admin-description').val(obj.options.description);
    modal.find('.field-description').val(obj.description);
    value = obj.icon ? obj.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '') : '';
    modal.find('.select-item-icon').val(value).attr('data-value', obj.icon);
    modal.find('.field-required').prop('checked', obj.required);
    modal.find('.field-type-select input[type="hidden"]').val(obj.options.type);
    value = modal.find('.field-type-select li[data-value="'+obj.options.type+'"]').text().trim();
    modal.find('.field-type-select input[readonly]').val(value);
    modal.find('div[class*="-type-options"]').hide();
    modal.find('.field-'+obj.options.type+'-type-options').css('display', '');
    setFieldGroupTypeOptions(obj);
    $g('#apply-group-field-item-settings').removeClass('disable-button').attr('data-key', key);
    modal.modal();
});

$g('#apply-group-field-item-settings').on('click', function(){
    let key = this.dataset.key,
        obj = app.edit.items[key],
        modal = $g('#group-field-item-dialog');
    obj.label = modal.find('.field-label').val().trim();
    obj.options = {
        label: modal.find('.field-admin-label').val().trim(),
        description: modal.find('.field-admin-description').val().trim(),
        type: type = modal.find('.field-type-select input[type="hidden"]').val()
    }
    obj.description = modal.find('.field-description').val().trim();
    obj.description = removeFieldDescriptionHTML(obj.description);
    obj.icon = modal.find('.select-item-icon').attr('data-value');
    obj.required = modal.find('.field-required').prop('checked');
    if (obj.options.type == 'textarea') {
        obj.options.texteditor = modal.find('input[data-element="texteditor"]').prop('checked');
    } else if (obj.options.type == 'radio' || obj.options.type == 'select' || obj.options.type == 'checkbox') {
        obj.options.items = {};
        modal.find('.sorting-container .sorting-item').each(function(ind){
            obj.options.items[ind] = {
                key: this.dataset.key,
                title: this.querySelector('.sorting-title').textContent
            };
        })
    } else if (obj.options.type == 'file' || obj.options.type == 'range' || obj.options.type == 'price') {
        modal.find(' .field-'+obj.options.type+'-type-options input[data-element]').each(function(){
            obj.options[this.dataset.element] = this.value;
        });
    } else if (obj.options.type == 'url') {
        modal.find('.field-url-type-options input[data-element]').each(function(){
            obj.options[this.dataset.element] = this.value;
        });
    }
    let str = renderNewFieldElement(obj);
    app.editor.$g(app.selector+' .ba-field-group-wrapper .ba-field-wrapper:nth-child('+(key * 1 + 1)+')').replaceWith(str);
    drawFieldGroupSortingList();
    modal.modal('hide');
});

$g('#apply-new-radio-value').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        if (app.edit.type == 'field') {
            var key = this.dataset.key,
                obj = {
                    title: $g('.radio-field-value').val().trim(),
                    key: app.edit.options.items[key] ? app.edit.options.items[key].key : +new Date()
                };
            app.edit.options.items[key] = obj;
            drawFieldSortingList();
        } else {
            var key = this.dataset.key,
                obj = {
                    title: $g('.radio-field-value').val().trim(),
                    key: key ? key : +new Date()
                };
            if (key) {
                $g('#group-field-item-dialog .sorting-item[data-key="'+key+'"] .sorting-title').text(obj.title);
            } else {
                $g('#group-field-item-dialog .sorting-container').append(renderGroupRadioOption(obj));
            }
        }
        $g('#edit-radio-field-element-modal').modal('hide');
    }
});

$g('#group-field-item-dialog .add-new-item .zmdi-plus-circle').on('click', function(){
    $g('.radio-field-value').val('');
    $g('#apply-new-radio-value').removeClass('active-button').addClass('disable-button').attr('data-key', '');
    $g('#edit-radio-field-element-modal').modal();
});

$g('#group-field-item-dialog .sorting-container').on('click', '.zmdi-edit', function(){
    let item = $g(this).closest('.sorting-item'),
        title = item.find('.sorting-title').text(),
        key = item.attr('data-key') * 1;
    $g('.radio-field-value').val(title);
    $g('#apply-new-radio-value').addClass('active-button').removeClass('disable-button').attr('data-key', key);
    $g('#edit-radio-field-element-modal').modal();
});

$g('.radio-field-value').on('input', function(){
    if (this.value.trim()) {
        $g('#apply-new-radio-value').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#apply-new-radio-value').removeClass('active-button').addClass('disable-button');
    }
});

$g('#field-settings-dialog .sorting-container').on('click', '.zmdi.zmdi-delete', function(){
    app.itemDelete = $g(this).closest('.sorting-item').attr('data-key');
    app.checkModule('deleteItem');
});

$g('#field-settings-dialog .fields-plugin-options .sorting-container').on('click', '.zmdi-copy', function(){
    var key = $g(this).closest('.sorting-item').attr('data-key') * 1,
        obj = $g.extend({}, app.edit.options.items[key]),
        list = {};
    obj.key = +new Date();
    for (var ind in app.edit.options.items) {
        if (ind == key) {
            list[ind] = app.edit.options.items[ind];
            list[key + 1] = obj;
        } else if (ind >= key + 1) {
            list[ind * 1 + 1] = app.edit.options.items[ind];
        } else {
            list[ind] = app.edit.options.items[ind];
        }
    }
    app.edit.options.items = list;
    drawFieldSortingList();
    app.addHistory();
});

$g('#field-settings-dialog .fields-group-plugin-options .sorting-container').on('click', '.zmdi-copy', function(){
    var key = $g(this).closest('.sorting-item').attr('data-key') * 1,
        obj = $g.extend({}, app.edit.items[key]),
        original = app.editor.$g(app.selector+' .ba-field-wrapper[data-id="'+obj.field_key+'"]'),
        clone = original.clone(),
        list = {};
    obj.field_key = 'item-'+(+new Date());
    clone.attr('data-id', obj.field_key);
    original.after(clone);
    for (var ind in app.edit.items) {
        if (ind == key) {
            list[ind] = app.edit.items[ind];
            list[key + 1] = obj;
        } else if (ind >= key + 1) {
            list[ind * 1 + 1] = app.edit.items[ind];
        } else {
            list[ind] = app.edit.items[ind];
        }
    }
    app.edit.items = list;
    drawFieldGroupSortingList();
    app.addHistory();
});

$g('#group-field-item-dialog .sorting-container').on('click', '.zmdi-copy', function(){
    var original = $g(this).closest('.sorting-item'),
        clone = original.clone();
    clone.attr('data-key', +new Date());
    original.after(clone);
});

$g('#group-field-item-dialog .sorting-container').on('click', '.zmdi-delete', function(){
    app.itemDelete = $g(this).closest('.sorting-item').attr('data-key');
    app.checkModule('deleteItem');
});

app.modules.fieldEditor = true;
app.fieldEditor();