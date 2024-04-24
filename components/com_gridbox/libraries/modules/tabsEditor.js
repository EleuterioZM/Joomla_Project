/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function addNewTab(title, icon)
{
    if (app.edit.type == 'tabs') {
        var div = document.createElement('div'),
            href = '#tab-'+(+new Date()),
            li = document.createElement('li'),
            a = document.createElement('a'),
            pspan = document.createElement('span'), 
            span = document.createElement('span'),
            i = document.createElement('i'),
            tabContent = app.editor.document.querySelector('#'+app.editor.app.edit+' > .ba-tabs-wrapper > .tab-content'),
            ul = app.editor.document.querySelector('#'+app.editor.app.edit+' > .ba-tabs-wrapper > ul');
        div.className = 'tab-pane';
        div.id = href.replace('#', '');
        tabContent.appendChild(div);
        a.href = href;
        a.dataset.toggle = 'tab';
        span.innerText = title;
        span.className = 'tabs-title';
        if (!title) {
            span.classList.add('empty-textnode');
        }
        li.appendChild(a);
        pspan.appendChild(span);
        a.appendChild(pspan);
        if (icon) {
            i.className = obj;
            pspan.appendChild(i);
        }
        ul.appendChild(li);
    } else {
        var accordion = app.editor.document.querySelector('#'+app.editor.app.edit+' > .accordion'),
            href = '#collapse-'+(+new Date()),
            group = document.createElement('div'),
            heading = document.createElement('div'),
            body = document.createElement('div'),
            div = document.createElement('div'),
            a = document.createElement('a'),
            pspan = document.createElement('span'), 
            span = document.createElement('span'),
            i = document.createElement('i'),
            i2 = document.createElement('i'),
            tabContent = app.editor.document.querySelector('#'+app.editor.app.edit+' > .ba-tabs-wrapper > .tab-content'),
            ul = app.editor.document.querySelector('#'+app.editor.app.edit+' > .ba-tabs-wrapper > ul');
        i2.className = 'ba-icons ba-icon-chevron-right accordion-icon';
        group.className = 'accordion-group';
        heading.className = 'accordion-heading';
        group.appendChild(heading);
        a.href = href;
        a.className = 'accordion-toggle';
        a.dataset.toggle = 'collapse';
        a.dataset.parent = '#'+accordion.id;
        span.innerText = title;
        span.className = 'accordion-title';
        if (!title) {
            span.classList.add('empty-textnode');
        }
        heading.appendChild(a);
        pspan.appendChild(span);
        a.appendChild(pspan);
        a.appendChild(i2);
        if (icon) {
            i.className = icon;
            pspan.appendChild(i);
        }
        group.appendChild(body);
        body.className = 'accordion-body collapse';
        body.style.height = 0;
        body.id = href.replace('#', '');
        div.className = 'accordion-inner';
        body.appendChild(div);
        accordion.appendChild(group);
    }
    app.fetch(JUri+"index.php?option=com_gridbox&task=editor.loadLayout", {
        layout : 'sectionTabs',
        count : 12
    }).then(function(text){
        let obj = JSON.parse(text);
        for (var key in obj.items) {
            if (obj.items[key].type == 'section') {
                continue;
            }
            obj.items[key].desktop.margin = {
                top: 0,
                bottom: 0
            }
            app.editor.app.items[key] = obj.items[key];
        }
        div.innerHTML = obj.html;
        app.editor.editItem(key);
        var item = app.editor.document.getElementById(key);
        app.editor.makeRowSortable($g(item).find('.ba-section-items'), 'tabs-row');
        app.editor.makeColumnSortable($g(item).find('.ba-grid-column'), 'column');
        app.editor.setColumnResizer(app.editor.document.getElementById(key));
        createTabsSortingList();
        if (app.pageStructure && app.pageStructure.visible) {
            app.pageStructure.updateStructure(true);
        }
    });
}

function createTabsSortingList()
{
    let query = app.edit.type == 'accordion' ? '.accordion > .accordion-group' : '.ba-tabs-wrapper > ul > li',
        list = app.editor.document.querySelectorAll('#'+app.editor.app.edit+' > '+query),
        modal = $g('#tabs-settings-dialog'),
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    list.forEach(function(parent){
        let title = parent.querySelector('.tabs-title, .accordion-title'),
            icon = title.parentNode.querySelector('i'),
            obj = {
                parent: parent,
                unpublish: parent.classList.contains('ba-unpublished-html-item'),
                title: title.textContent.trim(),
                href: title.closest('[data-toggle]').hash,
                icon: icon ? icon.className : ''
            },
            key = obj.href.replace('#', ''),
            html = addSortingList(obj, key);
        sortingList[key] = obj;
        container.append(html);
    });
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

app.tabsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#tabs-settings-dialog');
    modal.find('.active').removeClass('active');
    modal.find('a[href="#tabs-general-options"]').parent().addClass('active');
    $g('#tabs-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    createTabsSortingList();
    value = app.getValue('icon', 'size');
    app.setLinearInput(modal.find('[data-option="size"][data-group="icon"]'), value);
    modal.find('.tabs-icon-position input[type="hidden"]').val(app.edit.desktop.icon.position);
    value = modal.find('.tabs-icon-position li[data-value="'+app.edit.desktop.icon.position+'"]').text();
    modal.find('.tabs-icon-position input[readonly]').val(value.trim());
    app.setTypography(modal.find('.typography-options'), 'typography');
    if (app.edit.type == 'tabs') {
        value = modal.find('.hover-group [data-option="color"]');
        value[0].dataset.group = 'hover';
        updateInput(value, app.getValue('hover', 'color'));
        value = modal.find('[data-option="border"][data-group="header"]');
        updateInput(value, app.getValue('header', 'border'));
        modal.find('.tabs-position-select input[type="hidden"]').val(app.edit.position);
        value = modal.find('.tabs-position-select li[data-value="'+app.edit.position+'"]').text();
        modal.find('.tabs-position-select input[readonly]').val(value.trim());
    } else {
        value = modal.find('[data-option="color"][data-group="border"]');
        updateInput(value, app.getValue('border', 'color'));
        if (!app.edit.autoscroll) {
            app.edit.autoscroll = {
                enable: false
            }
        }
        modal.find('input[data-group="autoscroll"][data-option="enable"]').prop('checked', app.edit.autoscroll.enable);
    }
    value = app.getValue('header', 'color');
    updateInput(modal.find('[data-option="color"][data-group="header"]'), value);
    value = app.getValue('background', 'color');
    updateInput(modal.find('[data-group="background"][data-option="color"]'), value);
    app.setDefaultState(modal.find('.margin-settings-group'), 'default');
    app.setMarginValues(modal.find('.margin-settings-group'));
    app.setDefaultState(modal.find('.padding-settings-group'), 'default');
    app.setPaddingValues(modal.find('.padding-settings-group'));
    setDisableState('#tabs-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('#tabs-settings-dialog input[data-group="autoscroll"][data-option="enable"]').on('change', function(){
    app.edit.autoscroll.enable = this.checked;
    app.addHistory();
});

$g('#tabs-settings-dialog .tabs-icon-position').on('customAction', function(){
    app.edit.desktop.icon.position = this.querySelector('input[type="hidden"]').value;
    app.sectionRules()
    app.addHistory();
});

$g('#tabs-settings-dialog .tabs-position-select').on('customAction', function(){
    var item = app.editor.document.querySelector('#'+app.editor.app.edit+' .ba-tabs-wrapper');
    item.classList.remove(app.edit.position);
    app.edit.position = $g(this).find('input[type="hidden"]').val();
    item.classList.add(app.edit.position);
    app.addHistory();
});

$g('#tabs-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    $g('#add-new-element-modal input').val('');
    $g('#add-new-element-modal .select-item-icon').attr('data-value', '');
    $g('#apply-new-element').addClass('disable-button').removeClass('active-button').attr('data-edit', 'new');
    $g('#add-new-element-modal').modal();
});

$g('#tabs-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
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
    app.editItemId = app.editor.app.edit;
    app.editor.app.copyAction = 'copyTabPane';
    app.editor.app.checkModule('copyItem');
});

$g('#tabs-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#tabs-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key;
    $g('#add-new-element-modal .element-title').val(sortingList[key].title);
    $g('#add-new-element-modal .select-item-icon')
        .attr('data-value', sortingList[key].icon)
        .val(sortingList[key].icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, ''));
    $g('#apply-new-element').removeClass('disable-button').addClass('active-button').attr('data-edit', key);
    $g('#add-new-element-modal').modal();
});

$g('#tabs-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key;
    app.editItemId = app.editor.app.edit;
    app.editor.app.copyAction = 'copyTabPane';
    app.itemDelete = [key];
    app.editor.app.checkModule('copyItem');
});

function copyTabPane()
{
    app.itemDelete.forEach(function(ind){
        if (app.edit.type == 'tabs') {
            let obj = sortingList[ind],
                div = document.createElement('div'),
                wrapper = app.editor.$g(obj.href+' > .ba-wrapper').last(),
                href = 'tab-'+(+new Date()),
                clone = obj.parent.cloneNode(true);
            clone.classList.remove('active');
            clone.querySelector('a').href = '#'+href;
            div.className = 'tab-pane';
            div.id = href;
            obj.parent.after(clone);
            wrapper.parent().after(div);
            div.appendChild(wrapper[0]);
        } else {
            var obj = sortingList[ind],
                div = obj.parent,
                clone = div.cloneNode(true),
                wrapper = app.editor.$g(obj.href+' > .accordion-inner > .ba-wrapper').last(),
                href = 'collapse-'+(+new Date());
            $g(clone).find('> .accordion-heading a').removeClass('active').attr('href', '#'+href);
            $g(clone).find('> .accordion-body').removeClass('in').attr('id', href).css({
                height: 0
            }).find('> .accordion-inner').html(wrapper);
            $g(div).after(clone);
        }
    });
    createTabsSortingList();
    if (app.pageStructure && app.pageStructure.visible) {
        app.pageStructure.updateStructure(true);
    }
}

$g('#tabs-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#add-new-element-modal input').on('input', function(){
    clearTimeout(delay);
    var $this = this,
        that = $g('#add-new-element-modal input').not(this)[0];
    delay = setTimeout(function(){
        if ($this.value.trim() || that.value.trim()) {
            $g('#apply-new-element').removeClass('disable-button').addClass('active-button');
        } else {
            $g('#apply-new-element').addClass('disable-button').removeClass('active-button');
        }
    });
});

$g('#apply-new-element').on('click', function(){
    if (!this.classList.contains('active-button')) {
        return false;
    }
    let title = $g('#add-new-element-modal .element-title').val().trim(),
        icon = $g('#add-new-element-modal .select-item-icon').attr('data-value').trim();
    if (this.dataset.edit == 'new') {
        addNewTab(title, icon);
    } else {
        let key = this.dataset.edit,
            obj = sortingList[key],
            span = obj.parent.querySelector('.tabs-title, .accordion-title');
        span.textContent = title;
        span.classList[title ? 'remove' : 'add']('empty-textnode');
        if (obj.icon && !icon) {
            span.parentNode.querySelector('i').remove();
        } else if (!obj.icon && icon) {
            let i = document.createElement('i');
            i.className = icon;
            span.parentNode.append(i);
        } else if (obj.icon && icon) {
            span.parentNode.querySelector('i').className = icon;
        }
        obj.title = title;
        obj.icon = icon;
        $g('#tabs-settings-dialog .sorting-container .sorting-item[data-key="'+key+'"] .sorting-title').each(function(){
            this.textContent = title ? title : icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
        });
    }
    app.addHistory();
    $g('#add-new-element-modal').modal('hide');
});

app.modules.tabsEditor = true;
app.tabsEditor();