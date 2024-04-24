/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.iconListEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#icon-list-settings-dialog');
    $g('#icon-list-settings-dialog .active').removeClass('active');
    $g('#icon-list-settings-dialog a[href="#icon-list-general-options"]').parent().addClass('active');
    $g('#icon-list-general-options').addClass('active');
    setPresetsList($g('#icon-list-settings-dialog'));
    drawIconListSortingList();
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    $g('.icons-list-layout-select input[type="hidden"]').val(app.edit.layout);
    value = $g('.icons-list-layout-select li[data-value="'+app.edit.layout+'"]').text().trim();
    $g('.icons-list-layout-select input[type="text"]').val(value);
    if (!('listType' in app.edit)) {
        app.edit.listType = '';
    }
    $g('.icons-list-type-select input[type="hidden"]').val(app.edit.listType);
    value = $g('.icons-list-type-select li[data-value="'+app.edit.listType+'"]').text().trim();
    $g('.icons-list-type-select input[type="text"]').val(value);
    value = app.getValue('icons', 'size');
    app.setLinearInput(modal.find('[data-option="size"]'), value);
    app.setTypography($g('#icon-list-settings-dialog .typography-options'), 'body');
    if (!app.edit.desktop.background) {
        app.edit.desktop.background = {
            "color": "rgba(255, 255, 255, 0)"
        }
    }
    value = app.getValue('background', 'color');
    updateInput($g('#icon-list-settings-dialog input[data-option="color"][data-group="background"]'), value);
    $g('.icons-list-select-position input[type="hidden"]').val(app.edit.icon.position);
    value = $g('.icons-list-select-position li[data-value="'+app.edit.icon.position+'"]').text().trim();
    $g('.icons-list-select-position input[type="text"]').val(value);
    value = app.getValue('icons', 'color');
    updateInput($g('#icon-list-settings-dialog input[data-option="color"][data-group="icons"]'), value);
    if (!app.edit.desktop.icons.background) {
        app.edit.desktop.icons.background = 'rgba(255,255,255,0)';
        app.edit.desktop.icons.padding = 0;
        app.edit.desktop.icons.radius = 0;
    }
    value = app.getValue('icons', 'background');
    updateInput($g('#icon-list-settings-dialog input[data-option="background"][data-group="icons"]'), value);
    value = app.getValue('icons', 'padding');
    app.setLinearInput(modal.find('[data-option="padding"]'), value);
    value = app.getValue('icons', 'radius');
    app.setLinearInput(modal.find('[data-option="radius"]'), value);
    app.setDefaultState('#icon-list-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#icon-list-settings-dialog .margin-settings-group');
    setDisableState('#icon-list-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    if (!app.edit.desktop.padding) {
        app.edit.desktop.padding = {
            "bottom" : "0",
            "left" : "0",
            "right" : "0",
            "top" : "0"
        };
        app.edit.desktop.border = {
            "color" : "@border",
            "style" : "solid",
            "radius" : 0,
            "top" : "0",
            "width" : "0"
        };
        app.edit.desktop.shadow = {
            "value" : 0,
            "color" : "@shadow"
        };
        app.edit.desktop.background = {
            "color": "rgba(255, 255, 255, 0)"
        }
    }
    app.setDefaultState('#icon-list-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#icon-list-settings-dialog .padding-settings-group');
    app.setDefaultState('#icon-list-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#icon-list-settings-dialog .border-settings-group');
    app.setDefaultState('#icon-list-settings-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#icon-list-settings-dialog .shadow-settings-group');
    $g('#icon-list-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#icon-list-settings-dialog').modal();
    }, 150);
}

function drawIconListSortingList()
{
    let modal = $g('#icon-list-settings-dialog'),
        ul = app.editor.document.querySelector(app.selector+' ul'),
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    for (let ind in app.edit.list) {
        let obj = $g.extend(true, {}, app.edit.list[ind]);
        obj.parent = ul.querySelector('li:nth-child('+ind+')');
        obj.unpublish = obj.parent.classList.contains('ba-unpublished-html-item');
        sortingList[ind] = obj;
        container.append(addSortingList(obj, ind));
    }
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copyIconList(keys)
{
    let list = {},
        i = 1;
    for (let ind in app.edit.list) {
        list[i++] = app.edit.list[ind];
        if (keys.indexOf(ind * 1) != -1) {
            let obj = $g.extend({}, app.edit.list[ind]),
                li = sortingList[ind].parent,
                clone = li.cloneNode(true);
            $g(li).after(clone);
            list[i++] = obj;
        }
    }
    app.edit.list = list;
    app.editor.app.buttonsPrevent();
    drawIconListSortingList();
    app.addHistory();
}

$g('#icon-list-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1;
    copyIconList([key]);
});

$g('#icon-list-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1,
        obj = sortingList[key * 1],
        modal = $g('#icons-list-item-modal');
    $g('#apply-icons-list-item').removeClass('disable-button').addClass('active-button').attr('data-key', key);
    modal.find('.element-title').val(obj.title);
    modal.find('.select-item-icon').val(obj.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, ''))
        .attr('data-value', obj.icon);
    modal.find('.element-link').val(obj.link);
    modal.find('.element-target-select input[type="hidden"]').val(obj.target);
    value = modal.find('.element-target-select li[data-value="'+obj.target+'"]').text().trim();
    modal.find('.element-target-select input[type="text"]').val(value);
    modal.modal();
});

$g('#icon-list-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    var modal = $g('#icons-list-item-modal');
    $g('#apply-icons-list-item').addClass('disable-button').removeClass('active-button').attr('data-key', -1);
    modal.find('.element-title, .element-link, .select-item-icon').val('').attr('data-value', '');
    modal.find('.element-target-select input[type="hidden"]').val('_blank');
    value = modal.find('.element-target-select li[data-value="_blank"]').text().trim();
    modal.find('.element-target-select input[type="text"]').val(value);
    modal.modal();
});

$g('#icon-list-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
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
    copyIconList(array);
});

$g('#icon-list-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#icons-list-item-modal .element-title').on('input', function(){
    clearTimeout(delay);
    var $this = this;
    delay = setTimeout(function(){
        if ($g.trim($this.value)) {
            $g('#apply-icons-list-item').removeClass('disable-button').addClass('active-button');
        } else {
            $g('#apply-icons-list-item').addClass('disable-button').removeClass('active-button');
        }
    });
});

$g('#apply-icons-list-item').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        let key = this.dataset.key * 1,
            li = key == -1 ? document.createElement('li') : sortingList[key].parent,
            modal = $g('#icons-list-item-modal'),
            str = '',
            obj = {
                title: modal.find('.element-title').val().trim(),
                icon: modal.find('.select-item-icon').attr('data-value'),
                link: modal.find('.element-link').val().trim(),
                target: modal.find('.element-target-select input[type="hidden"]').val()
            }
        str += obj.link ? '<a href="'+obj.link+'" target="'+obj.target+'">' : '';
        str += (obj.icon ? '<i class="'+obj.icon+'"></i>' : '')+'<span>'+obj.title+'</span>';
        str += obj.link ? '</a>' : '';
        if (key == -1) {
            app.editor.$g(app.selector+' ul').append(li);
            for (let ind in sortingList) {
                key = ind * 1;
            }
            key = key == -1 ? 1 : key + 1;
        }
        li.classList[obj.link ? 'remove' : 'add']('list-item-without-link');
        app.edit.list[key] = obj;
        li.innerHTML = str;
        app.editor.app.buttonsPrevent();
        drawIconListSortingList();
        app.addHistory();
        modal.modal('hide');
    }
});

$g('#icon-list-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('.icons-list-select-position').on('customAction', function(){
    app.edit.icon.position = this.querySelector('input[type="hidden"]').value;
    app.sectionRules();
    app.addHistory();
});

$g('.icons-list-layout-select').on('customAction', function(){
    app.editor.$g(app.selector+' ul').removeClass(app.edit.layout);
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' ul').addClass(app.edit.layout);
    app.addHistory();
});

$g('.icons-list-type-select').on('customAction', function(){
    app.editor.$g(app.selector+' ul li').each(function(){
        this.classList[this.querySelector('a') ? 'remove' : 'add']('list-item-without-link');
    });
    app.editor.$g(app.selector+' ul').removeClass(app.edit.listType);
    app.edit.listType = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' ul').addClass(app.edit.listType);
    if (app.edit.listType == 'bullets-type') {
        app.edit.desktop.icons.size = 10;
    } else {
        app.edit.desktop.icons.size = 24;
    }
    value = app.getValue('icons', 'size');
    app.setLinearInput($g('#icon-list-settings-dialog [data-option="size"]'), value);
    app.sectionRules();
    app.addHistory();
});

app.modules.iconListEditor = true;
app.iconListEditor();