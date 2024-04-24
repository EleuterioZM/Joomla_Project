/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.customerInfoEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#customer-info-settings-dialog').attr('data-edit', app.edit.type);
    modal.find('.active').removeClass('active');
    modal.find('a[href="#customer-info-general-options"]').parent().addClass('active');
    modal.find('#customer-info-general-options').addClass('active');
    if (!app.edit.desktop.headline) {
        app.edit.desktop.headline = {
            "typography":{
                "color":"@title",
                "font-family":"@default",
                "font-size":"14",
                "font-style":"normal",
                "font-weight":"@default",
                "letter-spacing":0,
                "line-height":"36",
                "text-decoration":"none",
                "text-align":"left",
                "text-transform":"none"
            }
        }
        app.sectionRules();
    }
    if (!app.edit.desktop.headline.margin) {
        app.edit.desktop.headline.margin = {
            "bottom":"0",
            "top":"0"
        }
        app.edit.desktop.title.margin = {
            "bottom":"0",
            "top":"0"
        }
        app.edit.desktop.field.margin = {
            "bottom":"10",
            "top":"0"
        }
        app.edit.desktop.field.padding = {
            "default": {
                "bottom": 6,
                "left": 4,
                "right": 4,
                "top": 6
            },
            "state": false,
            "transition": {
                "duration": 0.3,
                "x1": 0.42,
                "y1": 0,
                "x2": 0.58,
                "y2": 1
            }
        }
    }
    if (app.edit.type == 'checkout-form') {
        getCustomerInfoList();
    } else {
        modal.find('.submission-form-app-select input[type="hidden"]').val(app.edit.app);
        value = modal.find('.submission-form-app-select li[data-value="'+app.edit.app+'"]').text().trim();
        modal.find('.submission-form-app-select input[readonly]').val(value);
    }
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    app.setDefaultState(modal.find('#customer-info-layout-options .margin-settings-group'), 'default');
    app.setMarginValues(modal.find('#customer-info-layout-options .margin-settings-group'));
    setDisableState('#customer-info-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.find('input[type="checkbox"][data-group="view"]').each(function(){
        this.checked = app.getValue('view', this.dataset.option);
    });
    modal.find('.slideshow-style-custom-select input[type="hidden"]').val('title');
    modal.find('.slideshow-style-custom-select input[readonly]').val(app._('LABEL'));
    showSlideshowDesign('title', modal.find('.slideshow-style-custom-select'));
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('#customer-info-general-options .submission-form-app-select').on('customAction', function(){
    let id = this.querySelector('input[type="hidden"]').value,
        data = {
            id: id,
            type: app.edit.type,
            edit_type: app.editor.themeData.edit_type
        };
    if (app.edit.app != id) {
        app.edit.app = id;
        app.fetch(JUri+"index.php?option=com_gridbox&task=editor.getAppFields", data).then((text) => {
            let object = JSON.parse(text),
                array = [];
            app.edit.desktop.fields = {};
            for (let ind in object) {
                array.push(ind);
                app.edit.desktop.fields[ind] = object[ind].required == 1;
            }
            app.edit.fields = array;
            getSubmissionForm();
            app.sectionRules();
            app.addHistory();
        });
    }
});

function getSubmissionForm()
{
    let fields = {},
        data = {
            id: app.edit.app,
            order: app.edit.fields,
            fields: fields
        };
    for (let ind in app.edit.desktop.fields) {
        fields[ind] = Number(app.edit.desktop.fields[ind]);
    }
    app.fetch(JUri+"index.php?option=com_gridbox&task=editor.getSubmissionForm", data).then((text) => {
        app.editor.$g(app.selector+' .ba-submission-form-wrapper').html(text);
    });
}

app.fetch(JUri+'index.php?option=com_gridbox&task=store.getTaxCountries').then(function(text){
    app.customerInfoEditor.countries = JSON.parse(text);
})

function getCustomerInfoList()
{
    let container = $g('#customer-info-settings-dialog .sorting-container').empty();
    app.edit.items.forEach(function(obj, i){
        container.append(getCustomerInfoSortingHTML(obj, i));
    });
}

function getCustomerInfoSortingHTML(obj, key)
{
    var disabled = obj.type == 'email' || obj.id == 1 || obj.type == 'country' ? 'disabled' : '',
        title = obj.type == 'acceptance' ? app._('ACCEPTANCE') : (obj.title ? obj.title : obj.settings.placeholder),
        str = '<div class="sorting-item" data-key="'+key;
    str += '"><div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
    str += '<div class="sorting-title">'+title+'</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit" data-action="edit"></i></span>';
    str += '<span class="'+disabled+'"><i class="zmdi zmdi-copy" data-action="copy"></i></span>';
    obj.type == 'country' || obj.type == 'email' ? disabled = '' : '';
    str += '<span class="'+disabled+'"><i class="zmdi zmdi-delete" data-action="delete"></i></span>';
    str += '</div></div>';

    return str;
}

$g('#customer-info-settings-dialog .add-new-item .zmdi-plus-circle').on('click', function(){
    let modal = $g('#edit-custom-info-dialog');
    modal.find('[data-key]').each(function(){
        if (this.type == 'checkbox') {
            this.checked = false;
        } else if (this.type == 'hidden') {
            this.value = 'text';
            value = this.closest('.ba-custom-select').querySelector('li[data-value="text"]').textContent.trim();
            this.closest('.ba-custom-select').querySelector('input[type="text"]').value = value;
        } else {
            this.value = '';
        }
    });
    modal.find('.ba-input-lg, .ba-checkbox-parent').removeClass('disabled');
    modal.modal().find('#apply-customer-info').addClass('active-button').attr('data-key', -1);
});

$g('#customer-info-settings-dialog .sorting-container').on('click', 'i[data-action]', function(){
    let parent = this.closest('.sorting-item'),
        item = {
            placeholder: '',
            html: '',
            options: [],
            width: 100
        },
        obj = app.edit.items[parent.dataset.key];
    obj.settings = $g.extend(true, item, obj.settings);
    if (this.dataset.action == 'edit') {
        let modal = $g('#customer-info-item-dialog'),
            wrapper = modal.find('.sorting-container').empty(),
            country = false;
        app.edit.items.forEach(function(object){
            if (object.type == 'country') {
                country = true;
            }
        });
        if (country && obj.type != 'country') {
            modal.find('.customer-info-type-select li[data-value="country"]').hide();
        } else {
            modal.find('.customer-info-type-select li[data-value="country"]').css('display', '');
        }
        obj.settings.options.forEach(function(title){
            wrapper.append(getCustomerInfoSortingOption(title));
        });
        modal.find('[data-key]').each(function(){
            if (this.type == 'checkbox') {
                this.checked = Boolean(Number(obj[this.dataset.key]));
            } else {
                this.value = obj[this.dataset.key];
            }
            if (this.type == 'hidden') {
                this.closest('.ba-custom-select').querySelector('input[type="text"]').value = app._(obj.type.toUpperCase());
            }
        });
        modal.find('[data-settings]').each(function(){
            let value = obj.settings[this.dataset.settings];
            if (this.type == 'checkbox') {
                this.checked = Boolean(Number(value));
            } else if (this.type == 'number' || this.type == 'text') {
                app.setLinearInput($g(this), value);
            } else {
                this.value = value;
            }
        });
        setCustomerInfoType(obj.type)
        modal.modal().find('#apply-customer-info-item').attr('data-key', parent.dataset.key);
    } else if (this.dataset.action == 'copy') {
        let cloneObj = null,
            ind = 0,
            array = [];
        app.editor.$g(app.selector+' .ba-checkout-form-fields:nth-child('+(parent.dataset.key * 1 + 1)+')').each(function(){
            let clone = this.cloneNode(true);
            $g(this).after(clone);
        });
        app.edit.items.forEach(function(object, i){
            object.order_list = ind++;
            array.push(object);
            if (i == parent.dataset.key) {
                cloneObj = $g.extend(true, {}, object);
                cloneObj.id = 0;
                cloneObj.order_list = ind++;
                array.push(cloneObj);
            }
        });
        app.edit.items = array;
        getCustomerInfoList();
    } else {
        app.itemDelete = parent.dataset.key;
        app.checkModule('deleteItem');
    }
});

$g('#customer-info-item-dialog').on('shown', function(){
    $g(this).find('.ba-range-wrapper input[type="range"]').each(function(){
        setLinearWidth($g(this));
    })
})

function setCustomerInfoType(type, selected)
{
    let modal = $g('#customer-info-item-dialog');
    modal.find('div[class*="-customer-info-options"]').hide();
    modal.find('div.'+type+'-customer-info-options').css('display', '');
    if (selected) {
        modal.find('.sorting-container').empty();
    }
    if (type == 'acceptance') {
        modal.find('input[data-key="required"]').prop('checked', true).closest('.ba-group-element').addClass('disabled');
        modal.find('input[data-key="title"]').hide();
        modal.find('textarea[data-settings="html"]').css('display', '').each(function(){
            if (selected) {
                this.value = 'I have read and agree to the <a href="#" target="_blank">Terms and Conditions</a>';
            }
        });
    } else if (type == 'headline') {
        modal.find('input[data-key="required"]').prop('checked', false).closest('.ba-group-element').addClass('disabled');
        modal.find('input[data-key="title"]').css('display', '');
        modal.find('textarea[data-settings="html"]').hide();
    } else {
        modal.find('input[data-key="required"]').closest('.ba-group-element').removeClass('disabled');
        modal.find('input[data-key="title"]').css('display', '');
        modal.find('textarea[data-settings="html"]').hide();
    }
    if (type == 'country') {
        modal.find('input[data-settings="width"]').closest('.ba-group-element').addClass('disabled');
    } else {
        modal.find('input[data-settings="width"]').closest('.ba-group-element').removeClass('disabled');
    }
    if ((type == 'dropdown' || type == 'country') && selected) {
        modal.find('input[data-settings="placeholder"]').val(app._('SELECT'));
    } else if (selected) {
        modal.find('input[data-settings="placeholder"]').val('');
    }
    if (type == 'email') {
        modal.find('.customer-info-type-select').closest('.ba-group-element').addClass('disabled');
    } else {
        modal.find('.customer-info-type-select').closest('.ba-group-element').removeClass('disabled');
    }
}

$g('#customer-info-item-dialog .customer-info-type-select').on('customAction', function(){
    let type = this.querySelector('input[type="hidden"]').value;
    setCustomerInfoType(type, true);
});

$g('#customer-info-item-dialog .add-new-item-action').on('click', function(){
    let modal = $g('#add-'+this.dataset.action+'-option-modal');
    modal.find('.apply-'+this.dataset.action+'-option').addClass('disable-button').removeClass('active-button').attr('data-key', -1);
    modal.find('input, textarea').val('');
    modal.modal();
});

$g('#customer-info-item-dialog .sorting-container').on('click', 'i[data-action]', function(){
    if (this.dataset.action == 'edit') {
        let modal = $g('#add-single-option-modal'),
            item = this.closest('.sorting-item'),
            key = $g(item).index(),
            title = item.querySelector('.sorting-title').textContent.trim();
        modal.find('.apply-single-option').addClass('disable-button').removeClass('active-button').attr('data-key', key);
        modal.find('input').val(title);
        modal.modal();
    } else if (this.dataset.action == 'duplicate') {
        let item = this.closest('.sorting-item'),
            clone = item.cloneNode(true);
        $g(item).after(clone);
    } else if (this.dataset.action == 'delete') {
        app.itemDelete = this.closest('.sorting-item');
        app.checkModule('deleteItem');
    }
});

$g('#add-single-option-modal, #add-bulk-option-modal').on('input', 'textarea, input', function(){
    let flag = this.value.trim(),
        disable = flag ? 'removeClass' : 'addClass',
        active = flag ? 'addClass' : 'removeClass';
    $g(this).closest('.modal').find('.ba-btn-primary')[disable]('disable-button')[active]('active-button');
});

function getCustomerInfoSortingOption(title)
{
    let str = '<div class="sorting-item"><div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
    str += '<div class="sorting-title">'+title+'</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit" data-action="edit"></i></span>';
    str += '<span><i class="zmdi zmdi-copy" data-action="duplicate"></i></span>';
    str += '<span><i class="zmdi zmdi-delete" data-action="delete"></i></span></div></div>';

    return str;
}

$g('.apply-single-option').on('click', function(){
    if (this.classList.contains('active-button')) {
        let modal = $g(this).closest('.modal'),
            key = this.dataset.key * 1,
            wrapper = $g('#customer-info-item-dialog .sorting-container')
            title = modal.find('input').val().trim();
        if (key == -1) {
            wrapper.append(getCustomerInfoSortingOption(title));
        } else {
            wrapper.find('.sorting-item').get(key).querySelector('.sorting-title').textContent = title;
        }
        modal.modal('hide');
    }
});

$g('.apply-bulk-option').on('click', function(){
    let modal = $g(this).closest('.modal'),
        data = modal.find('textarea').val().trim().split('\n'),
        wrapper = $g('#customer-info-item-dialog .sorting-container');
    if (this.classList.contains('active-button')) {
        data.forEach(function(title){
            if (title.trim()) {
                wrapper.append(getCustomerInfoSortingOption(title));
            }
        });
        modal.modal('hide');
    }
});

$g('#edit-custom-info-dialog .ba-custom-select').on('customAction', function(){
    let modal = $g(this).closest('.modal'),
        type = this.querySelector('input[type="hidden"]').value;
    if (type == 'acceptance') {
        modal.find('.ba-input-lg, .ba-checkbox-parent').addClass('disabled').find('input').each(function(){
            if (this.type == 'checkbox') {
                this.checked = true;
            } else {
                this.value = '';
            }
        });
    } else if (type == 'headline') {
        modal.find('.ba-input-lg').removeClass('disabled');
        modal.find('.ba-checkbox-parent').addClass('disabled').find('input').prop('checked', false);
    } else {
        modal.find('.ba-input-lg, .ba-checkbox-parent').removeClass('disabled');
    }
});

$g('#edit-custom-info-dialog input[data-key="type"]').on('change', function(){
    let action = 'addClass',
        type = this.value;
    if (type == 'country' || type == 'email') {
        app.edit.items.forEach(function(item){
            if (item.type == type) {
                action = 'removeClass';
            }
        });
    }
    $g('#apply-customer-info')[action]('active-button');
})

$g('#apply-customer-info').on('click', function(){
    if (!this.classList.contains('active-button')) {
        return false;
    }
    let obj = {
            invoice: 0,
            settings: {
                placeholder: '',
                options: [],
                html: 'I have read and agree to the <a href="#" target="_blank">Terms and Conditions</a>'
            }
        },
        modal = $g('#edit-custom-info-dialog');
    modal.find('.modal-body [data-key]').each(function(){
        obj[this.dataset.key] = this.type == 'checkbox' ? String(Number(this.checked)) : this.value;
    });
    if (obj.type == 'dropdown' || obj.type == 'country') {
        obj.settings.placeholder = app._('SELECT');
    }
    if (['dropdown', 'radio', 'checkbox'].indexOf(obj.type) != -1) {
        obj.settings.options.push(app._('VALUE'));
    } else if (obj.type == 'acceptance') {
        obj.required = '1';
    }
    obj.order_list = app.edit.items.length;
    obj.id = 0;
    app.edit.items.push(obj);
    let str = getCustomerInfoSortingHTML(obj, app.edit.items.length - 1),
        wrapper = null,
        div = document.createElement('div');
    $g('#customer-info-settings-dialog .sorting-container').append(str);
    str = '<div class="ba-checkout-form-fields" data-type="'+obj.type+'"><div class="ba-checkout-form-title-wrapper">';
    str += '<span class="ba-checkout-form-title">';
    str += obj.title+'</span>'+(obj.required == 1 && obj.title ? '<span class="ba-checkout-form-required-star">*</span>' : '');
    str += '</div><div class="ba-checkout-form-field-wrapper"></div></div>';
    div.innerHTML = str;
    wrapper = div.querySelector('.ba-checkout-form-field-wrapper');
    createCustomerInfoField(wrapper, obj);
    app.editor.$g(app.selector+' .ba-checkout-form-wrapper').append(div.innerHTML);
    modal.modal('hide');
});

$g('#apply-customer-info-item').on('click', function(){
    let obj = app.edit.items[this.dataset.key],
        title = '',
        modal = $g(this).closest('.modal');
    modal.find('.modal-body [data-key]').each(function(){
        obj[this.dataset.key] = this.type == 'checkbox' ? String(Number(this.checked)) : this.value;
    });
    modal.find('.modal-body [data-settings]').each(function(){
        obj.settings[this.dataset.settings] = this.type == 'checkbox' ? String(Number(this.checked)) : this.value;
    });
    title = obj.title ? obj.title : obj.settings.placeholder;
    if (obj.type == 'acceptance') {
        title = app._('ACCEPTANCE');
    }
    obj.settings.options = [];
    modal.find('.sorting-container .sorting-item').each(function(){
        obj.settings.options.push(this.querySelector('.sorting-title').textContent);
    });
    $g('#customer-info-settings-dialog .sorting-item[data-key="'+this.dataset.key+'"] .sorting-title').text(title);
    app.editor.$g(app.selector+' .ba-checkout-form-fields:nth-child('+(this.dataset.key * 1 + 1)+')').each(function(){
        this.dataset.type = obj.type;
        this.style.setProperty('--ba-checkout-field-width', obj.settings.width+'%');
        this.querySelector('.ba-checkout-form-title').textContent = obj.title;
        let $this = $g(),
            wrapper = this.querySelector('.ba-checkout-form-field-wrapper');
        wrapper.innerHTML = '';
        $this.find('.ba-checkout-form-required-star').remove();
        if (obj.required == 1 && obj.title) {
            $this.find('.ba-checkout-form-title-wrapper').append('<span class="ba-checkout-form-required-star">*</span>');
        }    
        createCustomerInfoField(wrapper, obj);
    });
    modal.modal('hide');
});

function createCustomerInfoField(wrapper, obj)
{
    let element = option = null;
    if (['text', 'textarea', 'email'].indexOf(obj.type) != -1) {
        element = document.createElement(obj.type == 'textarea' ? 'textarea' : 'input');
        if (obj.type != 'textarea') {
            element.type = 'text';
        }
        element.placeholder = obj.settings.placeholder;
        wrapper.append(element);
    } else if (obj.type == 'dropdown') {
        element = document.createElement('select');
        option = document.createElement('option');
        option.hidden = true;
        option.selected = true;
        option.value = obj.settings.placeholder;
        option.textContent = obj.settings.placeholder;
        element.append(option);
        obj.settings.options.forEach(function(title){
            option = document.createElement('option');
            option.value = title;
            option.textContent = title;
            element.append(option);
        });
        wrapper.append(element);
    } else if (obj.type == 'checkbox' || obj.type == 'radio') {
        obj.settings.options.forEach(function(title){
            let str = '<span>'+title+'</span><label class="ba-'+obj.type+'"><input type="'+obj.type+'"><span></span></label>';
            element = document.createElement('div');
            element.className = 'ba-checkbox-wrapper';
            element.innerHTML = str;
            wrapper.append(element);
        });
    } else if (obj.type == 'acceptance') {
        let str = '<label class="ba-checkbox"><input type="checkbox"><span></span></label>';
        element = document.createElement('div');
        element.className = 'ba-checkbox-wrapper acceptance-checkbox-wrapper';
        element.innerHTML = str;
        wrapper.append(element);
        element = document.createElement('div');
        element.className = 'ba-checkout-acceptance-html';
        element.innerHTML = obj.settings.html;
        wrapper.append(element);
    } else if (obj.type == 'country') {
        element = document.createElement('select');
        option = document.createElement('option');
        element.dataset.type = 'country';
        option.hidden = true;
        option.selected = true;
        option.value = obj.settings.placeholder;
        option.textContent = obj.settings.placeholder;
        element.append(option);
        for (let ind in app.customerInfoEditor.countries) {
            let country = app.customerInfoEditor.countries[ind]
            option = document.createElement('option');
            option.value = country.id;
            option.textContent = country.title;
            element.append(option);
        }
        let div = document.createElement('div');
        div.className = 'ba-checkout-country-wrapper';
        div.append(element);
        wrapper.append(div);
        element = document.createElement('select');
        element.dataset.type = 'region';
        div = document.createElement('div');
        div.className = 'ba-checkout-country-wrapper';
        div.append(element);
        wrapper.append(div);
        wrapper.classList.remove('visible-region-select');
    }
}

app.modules.customerInfoEditor = true;
app.customerInfoEditor();