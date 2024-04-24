/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.menuEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#menu-settings-dialog');
    $g('#menu-settings-dialog .active').removeClass('active');
    $g('#menu-settings-dialog a[href="#menu-general-options"]').parent().addClass('active');
    $g('#menu-general-options').addClass('active');
    setPresetsList($g('#menu-settings-dialog'));
    app.setDefaultState('#menu-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#menu-layout-options .margin-settings-group');
    setDisableState('#menu-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    if (app.edit.type == 'menu' || app.edit.type == 'one-page') {
        $g('#menu-settings-dialog .menu-position-select input[type="hidden"]').val(app.edit.hamburger.position);
        value = $g('#menu-settings-dialog .menu-position-select li[data-value="'+app.edit.hamburger.position+'"]').text();
        $g('#menu-settings-dialog .menu-position-select input[readonly]').val(value.trim());
        $g('#menu-settings-dialog .menu-layout-custom-select input[type="hidden"]').val(app.edit.layout.layout);
        value = $g('#menu-settings-dialog .menu-layout-custom-select li[data-value="'+app.edit.layout.layout+'"]').text();
        $g('#menu-settings-dialog .menu-layout-custom-select input[readonly]').val(value.trim());
        $g('#menu-settings-dialog [data-option="enable"][data-group="hamburger"]').prop('checked', app.edit.hamburger.enable);
        value = app.edit.hamburger.open;
        updateInput($g('#menu-settings-dialog input[data-option="open"][data-group="hamburger"]'), value);
        $g('#menu-settings-dialog [data-option="open-align"][data-value="'+app.edit.hamburger['open-align']+'"]').addClass('active');
        value = app.edit.hamburger.close;
        updateInput($g('#menu-settings-dialog input[data-option="close"][data-group="hamburger"]'), value);
        $g('#menu-settings-dialog [data-option="close-align"][data-value="'+app.edit.hamburger['close-align']+'"]').addClass('active');
        updateInput($g('#menu-settings-dialog input[data-option="background"][data-group="hamburger"]'), app.edit.hamburger.background);
        $g('#menu-settings-dialog .menu-style-custom-select input[type="hidden"]').val('nav-menu');
        value = $g('#menu-settings-dialog .menu-style-custom-select li[data-value="nav-menu"]').text();
        $g('#menu-settings-dialog .menu-style-custom-select input[readonly]').val(value.trim());
        if (!app.edit.hamburger.width) {
            app.edit.hamburger.overlay = 'rgba(0, 0, 0, 0.1)';
            app.edit.hamburger.width = 360;
            app.edit.hamburger.padding = {
                default: {
                    bottom: 30,
                    left: 30,
                    right: 30,
                    top: 75,
                },
                state: false
            }
            app.edit.hamburger.shadow = {
                default: {
                    value: 0,
                    color: "@shadow"
                },
                state: false
            }
            app.edit.hamburger.icons = {
                open: {
                    size: 24,
                    icon: 'zmdi zmdi-menu'
                },
                close: {
                    size: 24,
                    icon: 'zmdi zmdi-close'
                },
            }
        }
        updateInput($g('#menu-settings-dialog input[data-option="overlay"][data-group="hamburger"]'), app.edit.hamburger.overlay);
        app.setDefaultState('#menu-mobile-options .padding-settings-group', 'default');
        app.setPaddingValues('#menu-mobile-options .padding-settings-group');
        app.setDefaultState('#menu-mobile-options .shadow-settings-group', 'default');
        app.setShadowValues('#menu-mobile-options .shadow-settings-group');
        app.setLinearInput($g('#menu-mobile-options .hamburger-width-options input[data-option="width"]'), app.edit.hamburger.width);
        $g('#menu-mobile-options input[data-state="open"][data-option="icon"]').each(function(){
            this.value = app.edit.hamburger.icons.open.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            this.dataset.icon = app.edit.hamburger.icons.open.icon;
        });
        app.setLinearInput($g('#menu-mobile-options input[data-state="open"][data-option="size"]'), app.edit.hamburger.icons.open.size);
        $g('#menu-mobile-options input[data-state="close"][data-option="icon"]').each(function(){
            this.value = app.edit.hamburger.icons.close.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            this.dataset.icon = app.edit.hamburger.icons.close.icon;
        });
        app.setLinearInput($g('#menu-mobile-options input[data-state="close"][data-option="size"]'), app.edit.hamburger.icons.close.size);
        if (app.edit.hamburger.position == 'ba-menu-position-center') {
            $g('#menu-mobile-options .hamburger-width-options').hide();
        } else {
            $g('#menu-mobile-options .hamburger-width-options').css('display', '');
        }
    }
    setMenuStyle('nav-menu');
    $g('#menu-settings-dialog').find('.menu-options, .one-page-options, .submenu-layout-option').hide();
    if (app.edit.type == 'menu') {
        if (!app.edit.desktop.dropdown.border) {
            app.edit.desktop.dropdown.border = {
                "bottom": "0",
                "left": "0",
                "right": "0",
                "top": "0",
                "color": "@border",
                "style": "solid",
                "radius": "0",
                "width": "0"
            }
        }
        if (!('submenu' in app.edit.layout)) {
            app.edit.layout.submenu = '';
        }
        $g('#menu-settings-dialog .submenu-layout-option').each(function(){
            this.querySelector(' input[type="hidden"]').value = app.edit.layout.submenu;
            value = this.querySelector('li[data-value="'+app.edit.layout.submenu+'"]').textContent;
            this.querySelector('input[readonly]').value = value.trim();
            this.style.display = app.edit.layout.layout == 'vertical-menu' ? '' : 'none';
        });
        $g('#menu-settings-dialog .menu-options').removeAttr('style');
        $g('#menu-settings-dialog [data-option="collapse"][data-group="hamburger"]')
            .prop('checked', app.edit.hamburger.collapse);
        $g('.select-mainmenu').val('module ID='+app.edit.integration);
        value = app.getValue('background', 'color');
        updateInput($g('#menu-settings-dialog [data-group="background"][data-option="color"]'), value);
        value = app.getValue('dropdown', 'width');
        app.setLinearInput(modal.find('input[data-option="width"][data-group="dropdown"]'), value);
        app.setDefaultState('#menu-design-options .shadow-settings-group', 'default');
        app.setShadowValues('#menu-design-options .shadow-settings-group');
        value = app.getValue('dropdown', 'effect', 'animation');
        $g('#menu-settings-dialog .dropdown-menu-animation input[type="hidden"]').val(value);
        value = $g('#menu-settings-dialog .dropdown-menu-animation li[data-value="'+value+'"]').text();
        $g('#menu-settings-dialog .dropdown-menu-animation input[readonly]').val(value.trim());
        value = app.getValue('dropdown', 'duration', 'animation');
        app.setLinearInput(modal.find('input[data-option="duration"][data-group="dropdown"]'), value);
        createMenuSortingList();
        $g('.menu-layout-option').css('display', '');
    } else if (app.edit.type == 'one-page') {
        $g('#menu-settings-dialog .one-page-options').removeAttr('style');
        createOnePageSortingList();
        if (!app.edit.autoscroll) {
            app.edit.autoscroll = {
                "enable": false,
                "speed": 1000,
                "animation": "easeInSine"
            }
        }
        if (!app.edit.layout.type) {
            $g('.menu-layout-option').css('display', '');
        } else {
            $g('.menu-layout-option').hide();
        }
        $g('#menu-settings-dialog [data-group="autoscroll"]').each(function(){
            if (this.type == 'checkbox') {
                this.checked = app.edit.autoscroll[this.dataset.option];
            } else {
                this.value = app.edit.autoscroll[this.dataset.option];
                if (this.type == 'hidden') {
                    $g(this).prev().val(app.edit.autoscroll[this.dataset.option]);
                }
            }
        });
        $g('#menu-settings-dialog .select-one-page-type input[type="hidden"]').val(app.edit.layout.type);
        value = $g('#menu-settings-dialog .select-one-page-type li[data-value="'+app.edit.layout.type+'"]').text().trim();
        $g('#menu-settings-dialog .select-one-page-type input[readonly]').val(value);
    }
    $g('#menu-settings-dialog a[href="#menu-mobile-options"]').parent().css('display', '');
    $g('.menu-layout-custom-select').closest('.ba-settings-group').css('display', '');
    setTimeout(function(){
        $g('#menu-settings-dialog').modal();
    }, 50);
}

$g('#menu-settings-dialog input[data-group="hamburger"][data-option="icon"]').on('click', function(){
    uploadMode = 'reselectSocialIcon';
    fontBtn = this;
    checkIframe($g('#icon-upload-dialog'), 'icons');
}).on('change', function(){
    app.edit.hamburger.icons[this.dataset.state].icon = this.dataset.icon;
    app.editor.$g(app.selector).find('.'+this.dataset.state+'-menu i').attr('class', this.dataset.icon);
    app.addHistory();
});

$g('#menu-settings-dialog input[data-group="autoscroll"][data-option="enable"]').on('change', function(){
    app.edit.autoscroll.enable = this.checked;
    app.addHistory();
});

function createMenuSortingList()
{
    sortingList = [];
    let modal = $g('#menu-settings-dialog'),
        ul = app.editor.$g(app.selector+' > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul'),
        str = getMenuTree(ul);
    modal.find('.menu-options .sorting-container').html(str);
    modal.find('.menu-options .deeper-sorting-container').each(function(ind){
        $g(this).sortable({
            handle : '> .sorting-item-wrapper > .sorting-item > .sorting-handle',
            selector : '> .sorting-item-wrapper',
            change : function(dragEl){
                sortMenuItems(dragEl.parentNode);
            },
            group : 'menu-items'
        });
    });
    modal.find('.menu-options .sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copyOnePageMenuItem(keys)
{
    for (let ind in sortingList) {
        if (keys.indexOf(ind * 1) != -1) {
            let li = sortingList[ind].parent,
                clone = li.cloneNode(true);
            $g(li).after(clone);
        }
    }
    createOnePageSortingList();
    app.addHistory();
}

function createOnePageSortingList()
{
    let query = app.selector+' > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul li a',
        modal = $g('#menu-settings-dialog'),
        container =  modal.find('.one-page-options .sorting-container').empty();
    sortingList = {};
    app.editor.document.querySelectorAll(query).forEach(function(a, i){
        let parent = a.closest('li'),
            icon = a.querySelector('a > i.ba-menu-item-icon'),
            obj = {
                parent: parent,
                unpublish: parent.classList.contains('ba-unpublished-html-item'),
                title : a.textContent,
                href : a.hash,
                alias : a.dataset.alias,
                icon: icon ? icon.dataset.value : ''
            };
        sortingList[i] = obj;
        container.append(addSortingList(obj, i));
    });
    modal.find('.one-page-options .sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function getMenuTree(parent)
{
    let li = parent.find('> li'),
        str = '';
    for (let i = 0; i < li.length; i++) {
        let classList = li[i].classList,
            obj = {
                "title" : $g(li[i]).find('> a, > span').text().trim(),
                "id" : null
            };
        for (let j = 0; j < classList.length; j++) {
            if (classList[j].indexOf('item-') != -1) {
                obj.id = classList[j].replace('item-', '') * 1;
                break;
            }
        }
        if (!app.edit.items) {
            app.edit.items = {};
        }
        if (!app.edit.items[obj.id]) {
            app.edit.items[obj.id] = {
                "icon" : "",
                "megamenu" : false
            }
        }
        obj.item = app.edit.items[obj.id];
        sortingList.push(obj);
        let div = document.createElement('div');
        div.innerHTML = addSortingList(obj, sortingList.length - 1);
        div.className = 'sorting-item-wrapper';
        if ($g(li[i]).find(' > ul').length > 0) {
            let substr = '<div class="deeper-sorting-container" data-parent="'+obj.id+'">';
            substr += getMenuTree($g(li[i]).find('> ul'))
            substr += '</div>';
            $g(div).append(substr);
        }
        str += div.outerHTML;
    }

    return str;
}

function sortMenuItems(parent)
{
    var idArray = [];
    $g('#menu-settings-dialog .sorting-container > .sorting-item-wrapper > .sorting-item').each(function(){
        var obj = sortingList[this.dataset.key * 1];
        app.editor.$g('li.item-'+obj.id).each(function(){
            $g(this).closest('.integration-wrapper').find('> ul').append(this);
        });
    });
    $g('#menu-settings-dialog .deeper-sorting-container > .sorting-item-wrapper > .sorting-item').each(function(){
        var obj = sortingList[this.dataset.key * 1],
            parent = $g(this).closest('.deeper-sorting-container').attr('data-parent');
        app.editor.$g('li.item-'+obj.id).each(function(){
            $g(this).closest('.integration-wrapper').find('li.item-'+parent+' > ul').append(this);
        });
    });
    $g('#menu-settings-dialog .deeper-sorting-container').each(function(){
        if ($g(this).find('.sorting-item-wrapper').length == 0) {
            $g(this).remove();
        }
    });
    app.editor.$g(app.selector+' li.deeper.parent > ul').each(function(){
        if ($g(this).find('li').length == 0) {
            $g(this).remove();
        }
    });
    $g(parent).find('> .sorting-item-wrapper > .sorting-item').each(function(ind){
        var obj = sortingList[this.dataset.key * 1];
        app.editor.$g('li.item-'+obj.id).each(function(){
            $g(this).parent().append(this);
        });
    });
    $g('#menu-settings-dialog .menu-options .sorting-item').each(function(ind){
        var obj = sortingList[this.dataset.key * 1],
            object = {
                id : obj.id,
                parent_id : 1
            },
            parent = $g(this).closest('.deeper-sorting-container');
        if (parent.length > 0) {
            object.parent_id = parent.attr('data-parent') * 1;
        }
        idArray.push(object);
    });
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.sortMenuItems",
        data:{
            idArray : idArray
        },
        complete: function(msg){
            
        }
    });
}

function addNewOnePageMenuItem(obj)
{
    var li = document.createElement('li'),
        a = document.createElement('a'),
        ul = app.editor.document.querySelector('#'+app.editor.app.edit+' ul');
    a.href = obj.href;
    a.dataset.alias = obj.alias;
    a.textContent = obj.title;
    if (obj.icon) {
        var i = document.createElement('i');
        i.className = 'ba-menu-item-icon '+obj.icon;
        i.dataset.value = obj.icon;
        $g(a).prepend(i);
    }
    li.appendChild(a);
    ul.appendChild(li);
}

$g('#menu-settings-dialog .menu-options .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key,
        obj = sortingList[key];
    app.itemDelete = [key];
    if (app.editor.$g(app.selector).find('li.item-'+obj.id).hasClass('default')) {
        app.showNotice(gridboxLanguage['DEFAULT_ITEMS_NOTICE']);
        return false;
    }
    app.checkModule('deleteItem');
});

$g('#menu-settings-dialog .menu-options .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1,
        obj = sortingList[key];
    if (!this.closest('.deeper-sorting-container')) {
        $g('#menu-item-edit-modal .ba-checkbox-parent').css('display', '');
    } else {
        $g('#menu-item-edit-modal .ba-checkbox-parent').hide();
    }
    $g('#menu-item-edit-modal input[data-property]').each(function(){
        if (typeof(obj[this.dataset.property]) != 'undefined') {
            var value = obj[this.dataset.property];
        } else {
            var value = obj.item[this.dataset.property];
        }
        if (this.type == 'checkbox') {
            this.checked = value;
        } else {
            this.value = value.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            this.dataset.value = value;
        }
    });
    $g('#apply-menu-item').addClass('disable-button').removeClass('active-button').attr('data-edit', key);
    $g('#menu-item-edit-modal').modal();
});

$g('#apply-menu-item').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button') && this.dataset.click != 'true') {
        this.dataset.click = 'true';
        var key = this.dataset.edit * 1,
            obj = sortingList[key];
        $g('#menu-item-edit-modal input[data-property]').each(function(){
            if (obj[this.dataset.property]) {
                obj[this.dataset.property] = this.value.trim();
            } else if (this.type == 'checkbox') {
                obj.item[this.dataset.property] = this.checked;
            } else {
                obj.item[this.dataset.property] = this.dataset.value.trim();
            }
        });
        if (!obj.item.megamenu) {
            app.editor.$g(app.selector+' li.item-'+obj.id+' > .tabs-content-wrapper').remove();
        }
        if (obj.item.megamenu && app.editor.$g(app.selector+' li.item-'+obj.id+' > .tabs-content-wrapper').length == 0) {
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task=editor.loadLayout",
                async : false,
                data: {
                    layout : 'megamenu',
                    count : '4+4+4'
                },
                complete: function(msg){
                    msg = JSON.parse(msg.responseText);
                    var key = '';
                    for (var ind in msg.items) {
                        if (msg.items[ind].type == 'mega-menu-section') {
                            key = ind;
                            msg.items[ind].desktop.background.color = app.edit.desktop.background.color;
                        } else if (msg.items[ind].type == 'row') {
                            msg.items[ind].desktop.margin = {
                                "bottom" : "0",
                                "top" : "0"
                            }
                        }
                        app.editor.app.items[ind] = msg.items[ind];
                    }
                    app.sectionRules();
                    app.editor.$g(app.selector+' > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li.item-'+obj.id)
                        .addClass('megamenu-item').prepend(msg.html);
                    var item = app.editor.document.getElementById(key);
                    item.parentNode.dataset.megamenu = 'item-'+obj.id;
                    app.editor.editItem(key);
                    app.editor.makeRowSortable($g(item).find('.ba-section-items'), 'tabs-row');
                    app.editor.makeColumnSortable($g(item).find('.ba-grid-column'), 'lightbox-column');
                    app.editor.setColumnResizer(app.editor.document.getElementById(key));
                    app.editor.$g(app.selector+' .megamenu-item .tabs-content-wrapper .ba-section')
                        .addClass(app.edit.desktop.dropdown.animation.effect);
                    app.editor.app.setMarginBox();
                }
            });
        } else if (!obj.item.megamenu) {
            app.editor.$g(app.selector+' li.item-'+obj.id+' > .tabs-content-wrapper').remove();
        }
        app.edit.items[obj.id] = obj.item;
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:JUri+"index.php?option=com_gridbox&task=editor.saveMenuItemTitle",
            data:{
                title : obj.title,
                id : obj.id
            },
            complete: function(msg){
                $g.ajax({
                    type: "POST",
                    dataType: 'text',
                    url: JUri+"index.php?option=com_gridbox&task=editor.checkMainMenu&tmpl=component",
                    data: {
                        main_menu : app.edit.integration,
                        id : app.editor.app.edit,
                        items : JSON.stringify(app.edit)
                    },
                    complete: function(msg){
                        $g('#menu-settings-dialog .menu-options .sorting-item[data-key="'+key+'"] .sorting-title').text(obj.title);
                        app.editor.$g(app.selector+' > .ba-menu-wrapper > .main-menu > .integration-wrapper').each(function(){
                            var div = document.createElement('div');
                            div.innerHTML = msg.responseText;
                            $g(this).find('> ul > li > .tabs-content-wrapper').each(function(){
                                var classList = $g(this).closest('li')[0].classList,
                                    id = '';
                                for (var j = 0; j < classList.length; j++) {
                                    if (classList[j].indexOf('item-') != -1) {
                                        id = classList[j].replace('item-', '') * 1;
                                        break;
                                    }
                                }
                                $g(div).find('li.item-'+id).prepend(this);
                            });
                            $g(this).empty().append($g(div).find('> ul'));
                        });
                        app.editor.app.buttonsPrevent();
                    }
                });
                $g('#apply-menu-item')[0].dataset.click = 'false';
                $g('#menu-item-edit-modal').modal('hide');
            }
        });
    }
});

$g('#menu-item-edit-modal input[data-property]').on('change input', function(){
    var parent = $g(this).closest('.ba-modal-sm');
    if (parent.find('input[data-property="title"]').val().trim()) {
        parent.find('.ba-btn-primary').removeClass('disable-button').addClass('active-button');
    } else {
        parent.find('.ba-btn-primary').addClass('disable-button').removeClass('active-button');
    }
});

$g('#menu-item-edit-modal input[data-property="megamenu"]').on('change', function(){
    var key = $g('#apply-menu-item').attr('data-edit')
    if (!this.checked && app.editor.$g(app.selector+' li.item-'+sortingList[key].id+' > .tabs-content-wrapper').length > 0) {
        app.checkModule('deleteItem');
    }
});

$g('#menu-settings-dialog .select-one-page-type').on('customAction', function(){
    app.edit.layout.type = this.querySelector('input[type="hidden"]').value;
    if (!app.edit.layout.type) {
        $g('.menu-layout-option').css('display', '');
    } else {
        $g('.menu-layout-option').hide();
    }
    app.sectionRules();
    app.addHistory();
});

$g('.dropdown-menu-animation').on('customAction', function(){
    var effect = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector).find('li.deeper.parent > ul, .megamenu-item > .tabs-content-wrapper > .ba-section')
        .removeClass(app.edit.desktop.dropdown.animation.effect).addClass(effect);
    app.edit.desktop.dropdown.animation.effect = effect;
    app.addHistory();
});

$g('#menu-settings-dialog .one-page-options .sorting-toolbar-action[data-action="add"]').on('click', function(){
    $g('#one-page-item-modal input').val('');
    $g('#apply-one-page-item').addClass('disable-button').removeClass('active-button').attr('data-edit', -1);
    $g('#one-page-item-modal').modal();
});

$g('#menu-settings-dialog .one-page-options .sorting-toolbar-action[data-action="copy"]').on('click', function(){
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
    copyOnePageMenuItem(array);
});

$g('#menu-settings-dialog .one-page-options .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#menu-settings-dialog .menu-options .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#menu-settings-dialog .menu-options .sorting-toolbar-action[data-action="add"]').on('click', function(){
    $g('#menu-item-add-modal input').val('');
    $g('#menu-item-add-modal .menu-items-select-parent ul li').not('.item-root').remove();
    sortingList.forEach(function(el){
        var li = '<li data-value="'+el.id+'">'+el.title+'</li>';
        $g('#menu-item-add-modal .menu-items-select-parent ul').append(li);
    });
    $g('#menu-item-add-modal .menu-items-select-parent input[type="hidden"]').val(1);
    var title = $g('#menu-item-add-modal .menu-items-select-parent li[data-value="1"]').text().trim();
    $g('#menu-item-add-modal .menu-items-select-parent input[type="text"]').val(title);
    $g('#apply-new-menu-item').addClass('disable-button').removeClass('active-button');
    $g('#menu-item-add-modal').modal();
});

$g('#menu-item-add-modal input').on('change input', function(){
    var flag = true;
    $g('#menu-item-add-modal input').each(function(){
        if (!this.value.trim()) {
            flag = false;
            return false;
        }
    });
    if (!flag) {
        $g('#apply-new-menu-item').addClass('disable-button').removeClass('active-button');
    } else {
        $g('#apply-new-menu-item').removeClass('disable-button').addClass('active-button');
    }
});

$g('#apply-new-menu-item').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button') && this.dataset.click != 'true') {
        this.dataset.click = 'true';
        var data = {
            title : $g('#menu-item-add-modal input[data-property="title"]').val().trim(),
            link : $g('#menu-item-add-modal input[data-property="link"]').val().trim(),
            parent : $g('.menu-items-select-parent input[type="hidden"]').val().trim(),
            id : app.edit.integration
        }
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.setNewMenuItem",
            data: data,
            complete: function(msg){
                $g.ajax({
                    type: "POST",
                    dataType: 'text',
                    url: JUri+"index.php?option=com_gridbox&task=editor.checkMainMenu&tmpl=component",
                    data: {
                        main_menu : app.edit.integration,
                        id : app.editor.app.edit,
                        items : JSON.stringify(app.edit)
                    },
                    complete: function(msg){
                        app.editor.$g(app.selector+' > .ba-menu-wrapper > .main-menu > .integration-wrapper').each(function(){
                            var div = document.createElement('div');
                            div.innerHTML = msg.responseText;
                            $g(this).find('> ul > li > .tabs-content-wrapper').each(function(){
                                var classList = $g(this).closest('li')[0].classList,
                                    id = '';
                                for (var j = 0; j < classList.length; j++) {
                                    if (classList[j].indexOf('item-') != -1) {
                                        id = classList[j].replace('item-', '') * 1;
                                        break;
                                    }
                                }
                                $g(div).find('li.item-'+id).prepend(this);
                            });
                            $g(this).empty().append($g(div).find('> ul'));
                        });
                        app.editor.app.buttonsPrevent();
                        createMenuSortingList();
                        $g('#apply-new-menu-item')[0].dataset.click = 'false';
                        $g('#menu-item-add-modal').modal('hide');
                    }
                });
            }
        });
    }
});

$g('#menu-settings-dialog .one-page-options .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = $g(this).closest('.sorting-item').attr('data-key');
    $g('#one-page-item-modal .element-title').val(sortingList[key].title);
    $g('#one-page-item-modal .element-alias').val(sortingList[key].alias);
    $g('#one-page-item-modal .select-end-point').val(sortingList[key].href.replace('#', ''));
    var icon = sortingList[key].icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
    $g('#one-page-item-modal .select-item-icon').val(icon).attr('data-value', sortingList[key].icon);
    $g('#apply-one-page-item').removeClass('disable-button').addClass('active-button').attr('data-edit', key);
    $g('#one-page-item-modal').modal();
});

$g('#menu-settings-dialog .one-page-options .sorting-container').on('click', '.copy-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    copyOnePageMenuItem([key]);
});

$g('#menu-settings-dialog .one-page-options .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#one-page-item-modal .select-end-point').on('click', function(){
    app.editor.app.checkModule('setEndPoint');
    fontBtn = this;
});

$g('#one-page-item-modal .element-title').on('input', function(){
    clearTimeout(delay);
    var $this = this;
    delay = setTimeout(function(){
        if ($this.value.trim()) {
            $g('#apply-one-page-item').removeClass('disable-button').addClass('active-button');
        } else {
            $g('#apply-one-page-item').addClass('disable-button').removeClass('active-button');
        }
    });
});

$g('#apply-one-page-item').on('click', function(event){
    event.preventDefault();
    if (!this.classList.contains('active-button')) {
        return false;
    }
    let modal = $g('#one-page-item-modal'),
        obj = {
            title : modal.find('.element-title').val().trim(),
            alias : modal.find('.element-alias').val().trim(),
            icon : modal.find('.select-item-icon').attr('data-value'),
            href : '#'+modal.find('.select-end-point').val()
        },
        key = this.dataset.edit * 1;
    if (!obj.alias) {
        obj.alias = obj.title;
    }
    obj.alias = obj.alias.toLowerCase().replace(/ /g, '-');
    if (key == -1) {
        addNewOnePageMenuItem(obj);
    } else {
        let a = sortingList[key].parent.querySelector('a'),
            icon = a.querySelector('i.ba-menu-item-icon'),
            text = document.createTextNode(obj.title);
        a.href = obj.href;
        a.dataset.alias = obj.alias;
        a.innerHTML = obj.icon ? '<i class="ba-menu-item-icon '+obj.icon+'" data-value="'+obj.icon+'"></i>' : '';
        a.append(text);
    }
    createOnePageSortingList();
    app.addHistory();
    modal.modal('hide');
});

$g('.menu-style-custom-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        parent = $g('#menu-settings-dialog .typography-options').addClass('ba-active-options');
    setMenuStyle(value);
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
});

$g('.select-mainmenu').on('click', function(){
    fontBtn = this;
    checkIframe($g('#menu-select-modal').attr('data-check', 'single'), 'menu');
});

function selectMenu(obj)
{
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.checkMainMenu&tmpl=component",
        data: {
            main_menu : obj.selector,
            id : app.editor.app.edit,
            items : JSON.stringify(app.edit)
        },
        complete: function(msg){
            var item = app.editor.document.getElementById(app.editor.app.edit);
            item = item.querySelector('.integration-wrapper');
            item.innerHTML = msg.responseText;
            fontBtn.value = 'module ID='+obj.selector;
            app.edit.integration = obj.selector;
            $g('a, input[type="submit"], button').on('click', function(event){
                event.preventDefault();
            });
            createMenuSortingList();
            app.addHistory();
        }
    });
}

function setMenuStyle(type)
{
    $g('#menu-settings-dialog').attr('data-edit', type);
    let query = '.ba-settings-group:not(.dropdown-options):not(.nav-menu-options):not(.sub-menu-options)',
        group = type.replace('-menu', '');
    if (group == 'nav' || group == 'sub') {
        query += ', .'+type+'-options'
    }
    $g('#menu-design-options').find(query).find('[data-group]').each(function(){
        this.dataset.group = group;
    });
    type = group;
    app.setTypography($g('#menu-settings-dialog .typography-options'), type+'-typography');
    query = '.ba-settings-group:not(.states-settings-group):not(.dropdown-options)'
    $g('#menu-design-options').find(query).find('input[data-type="color"][data-group="'+type+'"]').each(function(){
        var option = this.dataset.option,
            subgroup = this.dataset.subgroup;
        value = app.getValue(type, option, subgroup);
        updateInput($g(this), value);
    });
    app.setDefaultState('#menu-design-options .padding-settings-group', 'default');
    app.setPaddingValues('#menu-design-options .padding-settings-group');
    app.setDefaultState('#menu-design-options .margin-settings-group', 'default');
    app.setMarginValues('#menu-design-options .margin-settings-group');
    app.editor.app.cssRules.prepareColors(app.edit.desktop[type]);
    if (!app.edit.desktop[type].colors.active) {
        app.edit.desktop[type].colors.active = $g.extend(true, {}, app.edit.desktop[type].colors.hover);
    }
    app.setDefaultState('#menu-design-options .colors-settings-group', 'default');
    app.setColorsValues('#menu-design-options .colors-settings-group');
    $g('#menu-design-options').find(query).find('input[type="range"] + input[data-group="'+type+'"]').each(function(){
        value = app.getValue(type, this.dataset.option, this.dataset.subgroup);
        app.setLinearInput($g(this), value);
    });
    app.setDefaultState('#menu-design-options .border-settings-group', 'default');
    app.setBorderValues('#menu-design-options .border-settings-group');
    $g('#menu-design-options i[data-type="reset"]').attr('data-option', type);
}

$g('.menu-layout-custom-select').on('customAction', function(){
    app.edit.layout.layout = this.querySelector('input[type="hidden"]').value;
    $g('#menu-settings-dialog .submenu-layout-option').each(function(){
        this.style.display = (app.edit.type == 'menu' && app.edit.layout.layout == 'vertical-menu') ? '' : 'none';
    });
    app.sectionRules();
    app.addHistory();
});

$g('.submenu-layout-custom-select').on('customAction', function(){
    app.edit.layout.submenu = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' > .ba-menu-wrapper')[app.edit.layout.submenu ? 'addClass' : 'removeClass']('collapsible-vertical-submenu');
    app.sectionRules();
    app.addHistory();
});

$g('.menu-position-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    app.edit.hamburger.position = value;
    if (app.edit.hamburger.position == 'ba-menu-position-center') {
        $g('#menu-mobile-options .hamburger-width-options').hide();
    } else {
        $g('#menu-mobile-options .hamburger-width-options').css('display', '');
    }
    app.sectionRules();
    app.addHistory();
});

$g('#menu-settings-dialog [data-group="hamburger"][data-option="enable"]').on('change', function(){
    app.edit.hamburger.enable = this.checked;
    app.sectionRules();
    app.addHistory();
});

$g('#menu-settings-dialog [data-group="hamburger"][data-option="collapse"]').on('change', function(){
    app.edit.hamburger.collapse = this.checked;
    app.sectionRules();
    app.addHistory();
});

app.modules.menuEditor = true;
app.menuEditor();