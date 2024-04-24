/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.featureBoxEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#feature-box-settings-dialog');
    $g('#feature-box-settings-dialog .active').removeClass('active');
    $g('#feature-box-settings-dialog a[href="#feature-box-general-options"]').parent().addClass('active');
    $g('#feature-box-general-options').addClass('active');
    setPresetsList($g('#feature-box-settings-dialog'));
    drawFeatureBoxSortingList();
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    $g('.feature-box-layout-select input[type="hidden"]').val(app.edit.layout);
    value = $g('.feature-box-layout-select li[data-value="'+app.edit.layout+'"]').text().trim();
    $g('.feature-box-layout-select input[type="text"]').val(value);
    value = app.getValue('view', 'count');
    $g('#feature-box-settings-dialog input[data-option="count"][data-group="view"]').val(value);
    app.setDefaultState('#feature-box-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#feature-box-layout-options .shadow-settings-group');
    app.setDefaultState('#feature-box-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#feature-box-layout-options .margin-settings-group');
    setDisableState('#feature-box-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.setDefaultState('#feature-box-layout-options .border-settings-group', 'default');
    app.setBorderValues('#feature-box-layout-options .border-settings-group');
    app.setDefaultState('#feature-box-layout-options .padding-settings-group', 'default');
    app.setPaddingValues('#feature-box-layout-options .padding-settings-group');
    app.setDefaultState('#feature-box-design-options .background-settings-group', 'default');
    app.setFeatureBackgroundValues('#feature-box-design-options .background-settings-group');    
    $g('#feature-box-settings-dialog').attr('data-edit', app.edit.type);
    $g('#feature-box-settings-dialog .ba-style-custom-select input[type="hidden"]').val('icon');
    $g('#feature-box-settings-dialog .ba-style-custom-select input[readonly]').val(gridboxLanguage['ICON']);
    $g('#feature-box-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
    $g('#feature-box-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
    showBaStyleDesign('icon', document.querySelector('#feature-box-settings-dialog .ba-style-custom-select'));
    setTimeout(function(){
        $g('#feature-box-settings-dialog').modal();
    }, 150);
}

function drawFeatureBoxSortingList()
{
    let modal = $g('#feature-box-settings-dialog'),
        wrapper = app.editor.document.querySelector(app.selector+' .ba-feature-box-wrapper'),
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    for (let ind in app.edit.items) {
        let obj = $g.extend(true, {}, app.edit.items[ind]);
        ind *= 1;
        obj.parent = wrapper.querySelector('.ba-feature-box:nth-child('+(ind + 1)+')');
        obj.unpublish = obj.parent.classList.contains('ba-unpublished-html-item');
        app.edit.items[ind].unpublish = obj.unpublish;
        sortingList[ind] = obj;
        container.append(addSortingList(obj, ind));
    }
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copyFeatureBox(keys)
{
    let list = {},
        i = 0;
    for (let ind in app.edit.items) {
        list[i++] = app.edit.items[ind];
        if (keys.indexOf(ind * 1) != -1) {
            let obj = $g.extend({}, app.edit.items[ind]),
                li = sortingList[ind].parent,
                clone = li.cloneNode(true);
            $g(li).after(clone);
            list[i++] = obj;
        }
    }
    app.edit.items = list;
    app.editor.app.buttonsPrevent();
    drawFeatureBoxSortingList();
    app.sectionRules();
    app.addHistory();
}

$g('#feature-box-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1;
    copyFeatureBox([key]);
});

$g('#feature-box-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#feature-box-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null,
        array = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key * 1;
            array.push(key);
        }
    });
    copyFeatureBox(array);
});

$g('#feature-box-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null;
    app.itemDelete = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key;
            app.itemDelete.push(key);
        }
    });
    app.checkModule('deleteItem');
});

$g('#feature-box-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1,
        obj = sortingList[key],
        modal = $g('#feature-box-item-modal');
    modal.find('.feature-box-type-select input[type="hidden"]').val(obj.type);
    value = modal.find('.feature-box-type-select li[data-value="'+obj.type+'"]').text().trim();
    modal.find('.feature-box-type-select input[type="text"]').val(value);
    modal.find('.feature-box-type-option').hide();
    modal.find('.feature-box-type-option[data-type="'+obj.type+'"]').css('display', '');
    modal.find('.slide-title').val(obj.title);
    modal.find('.slide-description').val(obj.description);
    modal.find('.select-item-icon').val(obj.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '')).attr('data-value', obj.icon);
    modal.find('.image-item-upload-image').val(obj.image);
    if (obj.button.view == 'link') {
        modal.find('.slideshow-button-label').hide();
    } else {
        modal.find('.slideshow-button-label').show();
    }
    modal.find('.slide-button-type-select input[type="hidden"]').val(obj.button.view);
    value = modal.find('.slide-button-type-select li[data-value="'+obj.button.view+'"]').text().trim();
    modal.find('.slide-button-type-select input[readonly]').val(value);
    modal.find('.slide-button-link').val(obj.button.href);
    modal.find('.slide-button-label').val(obj.button.title);
    modal.find('.slide-button-embed-code').val(obj.button.embed);
    modal.find('.slide-button-target-select input[type="hidden"]').val(obj.button.target);
    value = modal.find('.slide-button-target-select li[data-value="'+obj.button.target+'"]').text().trim();
    modal.find('.slide-button-target-select input[readonly]').val(value);
    modal.find('.slide-button-attribute-select input[type="hidden"]').val(obj.button.type);
    value = modal.find('.slide-button-attribute-select li[data-value="'+obj.button.type+'"]').text().trim();
    modal.find('.slide-button-attribute-select input[readonly]').val(value);
    $g('#apply-feature-box-item').attr('data-key', key);
    modal.modal();
});

$g('.feature-box-type-select').on('customAction', function(){
    var type = this.querySelector('input[type="hidden"]').value,
        modal = $g(this).closest('.modal');
    modal.find('.feature-box-type-option').hide();
    modal.find('.feature-box-type-option[data-type="'+type+'"]').css('display', '');
});

$g('#apply-feature-box-item').on('click', function(){
    var key = this.dataset.key * 1,
        item = key == -1 ? document.createElement('div') : sortingList[key].parent,
        div = document.createElement('div'),
        obj = {
            button: {}
        },
        modal = $g('#feature-box-item-modal');
    obj.type = modal.find('.feature-box-type-select input[type="hidden"]').val();
    obj.title = modal.find('.slide-title').val().trim();
    obj.description = modal.find('.slide-description').val().trim();
    obj.icon = modal.find('.select-item-icon').attr('data-value');
    obj.image = modal.find('.image-item-upload-image').val();
    obj.button.view = modal.find('.slide-button-type-select input[type="hidden"]').val();
    obj.button.href = modal.find('.slide-button-link').val().trim();
    obj.button.title = modal.find('.slide-button-label').val().trim();
    obj.button.embed = modal.find('.slide-button-embed-code').val().trim();
    obj.button.target = modal.find('.slide-button-target-select input[type="hidden"]').val();
    obj.button.type = modal.find('.slide-button-attribute-select input[type="hidden"]').val();
    if ((obj.icon && obj.type == 'icon') || (obj.image && obj.type == 'image')) {
        var image = '<div class="ba-feature-image-wrapper" data-type="'+obj.type+'">';
        image += obj.icon && obj.type == 'icon' ? '<i class="'+obj.icon+'"></i>' : '<div class="ba-feature-image"></div>';
        image += '</div>';
        div.innerHTML = image;
    }
    if (obj.title || obj.description || obj.button.title || (obj.button.href && obj.button.view == 'link')) {
        var caption = document.createElement('div');
        caption.className = 'ba-feature-caption';
        if (obj.title) {
            var title = document.createElement('div'),
                tag = document.createElement(app.edit.tag);
            title.className = 'ba-feature-title-wrapper';
            tag.className = 'ba-feature-title';
            tag.textContent = obj.title;
            title.appendChild(tag);
            caption.appendChild(title);
        }
        if (obj.description) {
            var description = document.createElement('div')
                desc = document.createElement('div');
            description.className = 'ba-feature-description-wrapper';
            desc.className = 'ba-feature-description';
            desc.innerHTML = obj.description;
            description.appendChild(desc);
            caption.appendChild(description);
        }
        if (obj.button.title || (obj.button.href && obj.button.view == 'link')) {
            var button = document.createElement('div'),
                a = document.createElement('a');
            button.className = 'ba-feature-button'+(obj.button.view == 'link' ? ' empty-content' : '');
            button.appendChild(a);
            caption.appendChild(button);
            var object = {
                href: obj.button.href,
                target: obj.button.target,
                type: obj.button.view == 'link' ? 'ba-overlay-slideshow-button' : '',
                download: obj.button.type,
                embed: obj.button.embed,
                title: obj.button.title
            }
            replaceSlideEmbed($g(a), object);
        }
        div.appendChild(caption);
    }
    if (key == -1) {
        item.classList.add('ba-feature-box');
        app.editor.document.querySelector(app.selector+' .ba-feature-box-wrapper').append(item);
        for (let ind in sortingList) {
            key = ind * 1;
        }
        key = key == -1 ? 0 : key + 1;
    }
    item.innerHTML = div.innerHTML;
    app.edit.items[key] = obj;
    app.editor.app.buttonsPrevent();
    drawFeatureBoxSortingList();
    modal.modal('hide');
    app.sectionRules();
    app.addHistory();
});

$g('#feature-box-item-modal .slide-button-type-select').on('customAction', function(){
    if (this.querySelector('input[type="hidden"]').value == 'button') {
        $g('#feature-box-item-modal .slideshow-button-label').show();
    } else {
        $g('#feature-box-item-modal .slideshow-button-label').hide();
    }
});

$g('#feature-box-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    var modal = $g('#feature-box-item-modal');
    modal.find('.feature-box-type-select input[type="hidden"]').val('icon');
    modal.find('.feature-box-type-select input[type="text"]').val(gridboxLanguage['ICON']);
    modal.find('.feature-box-type-option').hide();
    modal.find('.feature-box-type-option[data-type="icon"]').css('display', '');
    modal.find('.slide-title, .slide-description, .select-item-icon').val('').attr('data-value', '');
    modal.find('.image-item-upload-image, .slide-button-link, .slide-button-label, .slide-button-embed-code').val('');
    modal.find('.slideshow-button-label').show();
    modal.find('.slide-button-type-select input[type="hidden"]').val('button');
    modal.find('.slide-button-type-select input[readonly]').val(gridboxLanguage['BUTTON']);
    modal.find('.slide-button-target-select input[type="hidden"]').val('_blank');
    modal.find('.slide-button-target-select input[readonly]').val(gridboxLanguage['NEW_WINDOW']);
    modal.find('.slide-button-attribute-select input[type="hidden"]').val('');
    modal.find('.slide-button-attribute-select input[readonly]').val(gridboxLanguage['DEFAULT']);
    $g('#apply-feature-box-item').attr('data-key', -1);
    modal.modal();
});

$g('.feature-box-layout-select').on('customAction', function(){
    app.editor.$g(app.selector+' .ba-feature-box-wrapper').removeClass(app.edit.layout);
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' .ba-feature-box-wrapper').addClass(app.edit.layout);

    if (!app.edit.preset && !app.editor.app.theme.defaultPresets[app.edit.type]) {
        var type = app.edit.type,
            patern = $g.extend(true, {}, presetsPatern[type]),
            is_object = null,
            object = defaultElementsStyle[type];
        for (var ind in patern) {
            if (ind == 'desktop') {
                for (var key in patern[ind]) {
                    is_object = typeof(app.edit[ind][key]) == 'object';
                    app.edit[ind][key] = is_object ? $g.extend(true, {}, object[ind][key]) : object[ind][key];
                }
                for (var ind in app.editor.breakpoints) {
                    if (app.edit[ind]) {
                        for (var key in patern.desktop) {
                            is_object = typeof(app.edit[ind][key]) == 'object';
                            if (is_object && object[ind] && object[ind][key]) {
                                app.edit[ind][key] = $g.extend(true, {}, object[ind][key]);
                            } else if (!is_object && object[ind] && object[ind][key]) {
                                app.edit[ind][key] = object[ind][key];
                            } else if (is_object) {
                                app.edit[ind][key] = {};
                            } else {
                                delete(app.edit[ind][key]);
                            }
                        }
                    }
                }
            } else {
                is_object = typeof(app.edit[ind]) == 'object';
                app.edit[ind] = is_object ? $g.extend(true, {}, object[ind]) : object[ind];
            }
        }
        if (app.edit.layout == 'ba-feature-list-layout') {
            app.edit.desktop.title.margin.top = 0;
            app.edit.desktop.title.typography['text-align'] = 'left';
            app.edit.desktop.description.typography['text-align'] = 'left';
            app.edit.desktop.button.typography['text-align'] = 'left';
        }
        app.editor.app.checkModule('editItem');
        app.editor.app.setNewFont = true;
        app.editor.app.fonts = {};
        app.editor.app.customFonts = {};
    }
    app.sectionRules();
    app.addHistory();
});

app.modules.featureBoxEditor = true;
app.featureBoxEditor();