/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addHistory = function(task){
    let div = document.createElement('div'),
        obj = {
            items : $g.extend(true, {}, app.editor.app.items),
            content : div
        };
    obj.items.body = $g.extend(true, {}, app.editor.app.theme);
    div.innerHTML = app.editor.document.body.innerHTML;
    div.querySelectorAll('.ba-item-scroll-to-top, .ba-social-sidebar').forEach(function(el){
        let id = obj.items[el.id].parent,
            item = div.querySelector('#'+id);
        if (!item) {
            item = div.querySelector('.ba-grid-column');
        }
        if (item) {
            obj.items[el.id].parent = item.id;
            $g(item).find('> .empty-item').before(el);
        }
    });
    div.querySelectorAll('.ba-item-overlay-section').forEach(function(el){
        let overlay =  div.querySelector('.ba-overlay-section-backdrop[data-id="'+el.dataset.overlay+'"]');
        if (overlay) {
            overlay.classList.remove('visible-section');
            el.append(overlay);
        }
    });
    div.querySelectorAll('.ba-item-in-positioning').forEach(function(el){
        let parent = div.querySelector('#'+app.editor.app.items[el.id].positioning.parent);
        if (!parent) {
            parent = div.querySelector('.ba-grid-column');
            app.editor.app.items[el.id].positioning.parent = parent.id;
        }
        $g(parent).find('> .empty-item').before(el);
    });
    div.querySelectorAll('.ba-item-reading-progress-bar').forEach(function(el){
        let parent = div.querySelector('#'+app.editor.app.items[el.id].parent);
        if (!parent) {
            parent = div.querySelector('.ba-grid-column');
        }
        $g(parent).find('> .empty-item').before(el);
    });
    if (task == 'init') {
        app.history = [];
        $g('.ba-action-undo, .ba-action-redo').removeClass('active');
    } else {
        app.history.length = app.hIndex;
        obj.edit = app.editor.app.edit;
        $g('.ba-action-undo').addClass('active');
        $g('.ba-action-redo').removeClass('active');
    }
    if (app.editor.app.blogEditor) {
        app.editor.$g('.content-text').each(function(){
            app.editor.setTextPlaceholder(this);
        });
    }
    app.editor.$g('.ba-item-in-positioning').each(function(){
        app.editor.app.positioning.setTranslate(this);
    });
    app.history.push(obj);
    app.hIndex = app.history.length;
}

app.windowLoaded = function(){
    app.editor = window.frames['editor-iframe'];
    if (!app.editor.themeData.edit_type && !document.querySelector('.gridbox-apps-editor-wrapper')) {
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : JUri+"index.php?option=com_gridbox&task=editor.checkProductTour&tmpl=component",
            success : function(msg){
                if (msg == 'true') {
                    app.checkModule('productTour');
                }
            }
        });
    }
    if (document.querySelector('#page-structure-dialog').classList.contains('in')) {
        app.pageStructure.show();
    }
    app.checkModule('getSession');
    app.editor.app.loadModule('backgroundRule');
    app.loadModule('pageSettings');
}

app.pageStructure = {
    shiftItems: [],
    childs: {
        section: {
            sortable: 'rows',
            key: 'section',
            selector: ' > .ba-section-items > .ba-row-wrapper > .ba-row',
            icon: 'zmdi zmdi-texture'
        },
        row: {
            sortable: 'columns',
            key: 'row',
            selector: ' > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column',
            icon: 'zmdi zmdi-border-horizontal'
        },
        column: {
            sortable: 'plugins',
            key: 'column',
            selector: '>.ba-item:not(.ba-item-reading-progress-bar):not(.ba-item-scroll-to-top):not(.ba-item-in-positioning), >.ba-row-wrapper > .ba-row',
            icon: 'zmdi zmdi-border-vertical'
        },
        plugin: {
            key: 'plugin',
            icon: 'zmdi zmdi-widgets'
        }
    },
    selectors: {
        getTabs: (element) => {
            let array = [],
                selector = '';
            $g(element).find('> .ba-tabs-wrapper > ul li a').each((i, a) => {
                array.push(a.hash+' > .ba-wrapper > .ba-section');
            });
            selector = array.join(', ');

            return selector;
        },
        get: ($this, key, restrict, obj) => {
            let selector = key && $this.selectors.list[key] ? $this.selectors.list[key].join(', ') : '';
            if (restrict && ($this.selectors.restricts.indexOf(key) != -1 || !$this.selectors.list[key]) && obj) {
                selector = obj.selector;
            }
            
            return selector;
        },
        restricts: ['header', 'footer'],
        list: {
            'content-slider': ['> .slideshow-wrapper > ul > .slideshow-content > li > .ba-grid-column'],
            tabs: ['> .ba-tabs-wrapper > .tab-content > .tab-pane > .ba-wrapper > .ba-section'],
            hotspot: ['> .ba-hotspot-popover > .ba-item'],
            accordion: ['> .accordion > .accordion-group > .accordion-body > .accordion-inner > .ba-wrapper > .ba-section'],
            menu: ['> .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li > .ba-wrapper > .ba-section'],
            header: ['header.header  > .ba-wrapper:not(.ba-sticky-header)  > .ba-section'],
            page: ['#ba-edit-section  > .ba-wrapper:not(.ba-sticky-header)  > .ba-section'],
            footer: ['footer.footer  > .ba-wrapper:not(.ba-sticky-header)  > .ba-section'],
            canvas: ['.ba-sticky-header > .ba-section, .ba-lightbox > .ba-section, .ba-item-in-positioning',
                '.ba-item-scroll-to-top, .ba-overlay-section > .ba-section, .ba-item-reading-progress-bar'
                ]
        }
    },
    root: {
        header: {
            selector: 'header.header'
        },
        page: {
            selector: '#ba-edit-section',
            sortable: 'section'
        },
        footer: {
            selector: 'footer.footer'
        },
        canvas: {}
    },
    getType: (id) => {
        let item = app.editor.app.items[id],
            el = app.editor.document.querySelector('#'+id),
            type = item ? item.type : null;
        if (type == 'row' && app.editor.$g(el).parent().parent().hasClass('ba-grid-column')) {
            type = 'nested-row';
        } else if (!type && el.classList.contains('ba-grid-column') && el.closest('.ba-item-content-slider')) {
            type = 'slide';
        } else if (el.classList.contains('ba-section') && el.closest('.ba-item-tabs')) {
            type = 'tab';
        } else if (el.classList.contains('ba-section') && el.closest('.ba-item-accordion')) {
            type = 'accordion';
        } else if (el.classList.contains('ba-row') && el.closest('.ba-cookies')) {
            type = 'row';
        } else if (el.classList.contains('ba-grid-column') && el.closest('.ba-cookies')) {
            type = 'column';
        }

        return type;
    },
    getTitle: (id) => {
        let item = app.editor.app.items[id],
            type = app.pageStructure.getType(id),
            title = item && item.structureTitle ? item.structureTitle : app.getTitle(type, true);

        return title;
    },
    createLi: (id, obj, childs) => {
        let li = document.createElement('li'),
            i = document.createElement('i'),
            span = document.createElement('span'),
            el = app.editor.document.querySelector('#'+id),
            item = app.editor.app.items[id],
            icon = obj.icon,
            span2 = document.createElement('span');
        if ((!item && el.classList.contains('ba-grid-column') && el.closest('.ba-item-content-slider'))
            || (el.classList.contains('ba-section') && el.closest('.ba-item-tabs'))
            || (el.classList.contains('ba-section') && el.closest('.ba-item-accordion'))) {
            icon = 'zmdi zmdi-folder';
        }
        li.dataset.element = app.pageStructure.getType(id);
        li.dataset.id = id;
        li.dataset.type = obj.key;
        i.className = icon;
        span2.textContent = app.pageStructure.getTitle(id);
        span.append(i);
        span.append(span2);
        li.append(span);
        if (item && item[app.view] && ('disable' in item[app.view]) && item[app.view].disable == 1) {
            i = document.createElement('i')
            i.className = 'zmdi zmdi-eye-off page-structure-disable-icon';
            li.append(i);
        }
        if (childs) {
            i = document.createElement('i')
            i.className = 'zmdi zmdi-caret-right ba-branch-action';
            li.append(i);
            li.append(childs);
        }

        return li;
    },
    getPanel: (create) => {

        return window.pagestructure;;
    },
    savePanel: (obj) => {
        let panel = JSON.stringify(obj);
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.setModalSettings', {
            service: 'page-structure-panel',
            key: JSON.stringify(obj)
        });
    },
    setPanel: (values) => {
        let obj = app.pageStructure.getPanel(true);
        for (let key in values) {
            if (key == 'position') {
                obj.position = app.pageStructure.modal.attr('data-position');
            } else if (key == 'visible') {
                obj.visible = app.pageStructure.visible;
            } else {
                let rect = app.pageStructure.modal[0].getBoundingClientRect();
                obj[key] = rect[key];
            }
        }
        app.pageStructure.savePanel(obj);
    },
    getChild: (element) => {
        let obj = null;
        if (element.classList.contains('ba-section')) {
            obj = app.pageStructure.childs.section;
        } else if (element.classList.contains('ba-row')) {
            obj = app.pageStructure.childs.row;
        } else if (element.classList.contains('ba-grid-column')) {
            obj = app.pageStructure.childs.column;
        } else {
            obj = app.pageStructure.childs.plugin;
        }

        return obj;
    },
    getStructureChilds: (selector, level, parent, $this, ul) => {
        app.editor.$g(parent).find(selector).each((i, element) => {
            if (app.editor.app.items[element.id]) {
                $this.items.push(element.id);
            }
            if (app.editor.app.items[element.id] ||
                (element.classList.contains('ba-grid-column') && element.closest('.ba-item-content-slider')) ||
                ((element.classList.contains('ba-row') || element.classList.contains('ba-grid-column')) && element.closest('.ba-cookies')) ||
                (element.classList.contains('ba-section') && (element.closest('.ba-item-tabs') || element.closest('.ba-item-accordion')))) {
                let obj = $this.getChild(element),
                    item = app.editor.app.items[element.id],
                    type = item ? item.type : null,
                    selector = type == 'tabs' ? $this.selectors.getTabs(element) :  $this.selectors.get($this, type, true, obj),
                    childs = selector ? $this.getStructure(selector, level, element, obj.sortable) : null,
                    li = $this.createLi(element.id, obj, childs);
                ul.append(li);
            }
        });
    },
    getStructure: (selector, level, parent, sortable) => {
        let ul = document.createElement('ul'),
            $this = app.pageStructure;
        ul.className = 'childs-structure';
        ul.style.setProperty('--structure-level', level++);
        if (parent instanceof app.editor.Node && parent.classList.contains('ba-item-tabs')) {
            sortable = parent.id;
            selector.split(', ').forEach((query) => {
                $this.getStructureChilds(query, level, parent, $this, ul);
            })
        } else {
            $this.getStructureChilds(selector, level, parent, $this, ul);
        }
        if (parent instanceof app.editor.Node && parent.classList.contains('ba-item-accordion')) {
            sortable = parent.id;
        } else if (parent instanceof app.editor.Node && parent.classList.contains('ba-item-hotspot')) {
            sortable = parent.id;
        }
        if (sortable) {
            ul.dataset.sortable = sortable == 'columns' ? parent.id : sortable;
        }

        return ul;
    },
    updateStructure: (rebuild) => {
        let $this = app.pageStructure;
        if (!rebuild) {
            $this.lastActive = null;
        }
        $this.items = [];
        for (let key in $this.root) {
            let obj = $this.root[key],
                ul = $this.getStructure($this.selectors.get($this, key), 1, 'body', obj.sortable);
            $this.modal.find('.ba-page-structure-root-item[data-structure="'+key+'"]').each(function(){
                let $ul = $g(this).find('> ul');
                if (rebuild) {
                    $ul.find('.visible-branch').each((i, li) => {
                        ul.querySelectorAll('li[data-id="'+li.dataset.id+'"]').forEach((child) => {
                            child.classList.add('visible-branch');
                        });
                    });
                    $ul.find('.active').each((i, li) => {
                        ul.querySelectorAll('li[data-id="'+li.dataset.id+'"]').forEach((child) => {
                            child.classList.add('active');
                        });
                    });
                }
                $ul.remove();
                this.append(ul);
            });
        }
        $this.findEmpty();
        $this.modal.find('li[data-id] > span').on('mouseenter', function(){
            let id = this.closest('li').dataset.id;
            app.editor.$g('#'+id).addClass('page-structure-item-hovered');
        }).on('mouseleave', function(){
            let id = this.closest('li').dataset.id;
            app.editor.$g('#'+id).removeClass('page-structure-item-hovered');
        });
        $this.modal.find('ul[data-sortable]').each(function(){
            $this.sortable.init(this, {
                group: this.dataset.sortable
            });
        });
    },
    inStructure: (id, update) => {
        app.pageStructure.show(update).then(() => {
            let li = null;
            app.pageStructure.modal.find('li[data-id="'+id+'"]').each(function(){
                li = this;
                app.pageStructure.clickEvent(this, null, false);
            }).parentsUntil('.ba-page-structure-list', 'li').each(function(){
                this.classList.add('visible-branch');
            });
            li.scrollIntoView();
        })
    },
    show: (update) => {
        return new Promise((resolve, reject) => {
            if (update && !app.pageStructure.visible) {
                app.pageStructure.updateStructure(false);
            }
            if (app.pageStructure.visible || update) {
                return resolve();
            }
            let obj = app.pageStructure.getPanel();
            if (obj) {
                app.pageStructure.modal.attr('data-position', obj.position).css({
                    width: obj.width+'px',
                    height: obj.height+'px',
                    top: obj.top+'px',
                    left: obj.left+'px',
                    marginLeft : 0,
                    right: 'auto'
                }).find('.select-page-structure-position input').val(obj.position);
            }
            setTimeout(function(){
                app.pageStructure.updateStructure(false);
                app.pageStructure.modal.modal({
                    backdrop: false
                });
                app.pageStructure.visible = true;
                app.pageStructure.setPanel({
                    visible: 1
                });
                resolve();
            }, 50);
        });
    },
    init: () => {
        app.pageStructure.modal = $g('#page-structure-dialog');
        app.pageStructure.positions = $g('.page-structure-position-context');
        app.pageStructure.addEvents();
    },
    findEmpty: () => {
        app.pageStructure.modal.find('.childs-structure').each(function(){
            let li = this.closest('li');
            if (this.children.length > 0) {
                li.classList.remove('empty-branch-structure');
            } else {
                li.classList.add('empty-branch-structure');
                li.classList.remove('visible-branch');
            }
            
        });
    },
    sortable: {
        groups: {},
        prepareData: (placeholder) => {
            let rect = placeholder.getBoundingClientRect();
            app.pageStructure.sortable.css = {
                width: rect.width,
                height: rect.height,
                left: rect.left,
                top: rect.top
            }
        },
        isVisible: (item) => {
            let li = item.closest('ul').closest('li'),
                flag = true;
            while (li && flag) {
                flag = li.classList.contains('visible-branch');
                li = li.closest('ul').closest('li');
            }

            return flag;
        },
        updatePlaceholders: ($this, target, method, placeholders) => {
            if (method == 'after') {
                placeholders.reverse();
            }
            placeholders.forEach((li) => {
                $this.update(target, method, li);
            });
            app.addHistory();
        },
        update: (target, method, item) => {
            let id = (method == 'append' ? target.closest('li') : target).dataset.id,
                div = app.editor.$g('#'+id),
                child = app.editor.$g('#'+item.dataset.id);
            if (div.hasClass('ba-grid-column') && !child.hasClass('ba-grid-column')) {
                div = div.find('> .empty-item');
                method = 'before';
            } else if (div.hasClass('ba-grid-column') && child.hasClass('ba-grid-column')) {
                div = div.parent();
                child = child.parent();
            }
            if (div.hasClass('ba-section') && div[0].closest('.ba-item-tabs')) {
                div = app.editor.$g('a[href="#'+div.closest('.tab-pane').attr('id')+'"]').closest('li');
            } else if (div.hasClass('ba-section') && div[0].closest('.ba-item-accordion')) {
                div = div.closest('.accordion-group');
            } else if (div.hasClass('ba-row') || div.hasClass('ba-section')) {
                div = div.parent();
            }
            if (child.hasClass('ba-section') && child[0].closest('.ba-item-tabs')) {
                child = app.editor.$g('a[href="#'+child.closest('.tab-pane').attr('id')+'"]').closest('li');
            } else if (child.hasClass('ba-section') && child[0].closest('.ba-item-accordion')) {
                child = child.closest('.accordion-group');
            } else if (child.hasClass('ba-row') || child.hasClass('ba-section')) {
                child = child.parent();
            }
            div[method](child);
            if (div.hasClass('ba-grid-column-wrapper') && child.hasClass('ba-grid-column-wrapper')) {
                let wrapper = div.closest('.column-wrapper'),
                    columns = wrapper.find('> .ba-grid-column-wrapper');
                wrapper.find('> .ba-column-resizer').each((i, resizer) => {
                    app.editor.$g(columns[i]).after(resizer);
                })
            }
            app.setRowWithIntro();
            if (app.edit && (app.edit.type == 'tabs' || app.edit.type == 'accordion')) {
                createTabsSortingList();
            }
        },
        init: (item, options) => {
            let $this = app.pageStructure.sortable;
            if (!$this.groups[options.group]) {
                $this.groups[options.group] = [];
            }
            $this.groups[options.group].unshift(item);
            $g(item).off('mousedown.gridSorting').on('mousedown.gridSorting', '> li[data-id] > span', function(e){
                if (e.button == 0) {
                    let placeholder = this.closest('li[data-id]'),
                        placeholders = [],
                        handle = placeholder.cloneNode(true),
                        rect = null,
                        method = '',
                        element = null,
                        helper = $g(handle),
                        place = $g(placeholder),
                        array = $this.groups[options.group],
                        delta = {};
                    $this.prepareData(placeholder);
                    $g(document).on('mousemove.gridSorting', function(event){
                        let deltaX = Math.abs(e.clientX - event.clientX),
                            deltaY = Math.abs(e.clientY - event.clientY);
                        if (!document.body.classList.contains('grid-sorting-started') && deltaX < 5 && deltaY < 5) {
                            return false;
                        }
                        if (!document.body.classList.contains('grid-sorting-started')) {
                            for (let i = array.length - 1; i >= 0; i--) {
                                $g(array[i]).find('> li[data-id]').each(function(){
                                    if (this == placeholder || (this.classList.contains('active') && placeholder.classList.contains('active'))) {
                                        placeholders.push(this)
                                    }
                                });
                            }
                            let level = placeholder.closest('ul.childs-structure').style.getPropertyValue('--structure-level');
                            handle.style.setProperty('--structure-level', level);
                            handle.classList.add('sorting-grid-handle-item');
                            handle.classList.add(options.group);
                            document.body.append(handle);
                            placeholders.forEach((li) => {
                                li.classList.add('sorting-grid-placeholder-item');
                            });
                            delta.x = $this.css.left - event.clientX;
                            delta.y = $this.css.top - event.clientY;
                            helper.css($this.css);
                            document.body.classList.add('grid-sorting-started');
                            document.body.classList.add('page-structure-sorting');
                        }
                        let target = null,
                            top = event.clientY + delta.y,
                            left = event.clientX + delta.x,
                            bottom = top + $this.css.height,
                            right = left + $this.css.width;
                        for (let i = 0; i < array.length; i++) {
                            $g(array[i]).find('> li[data-id]').each(function(){
                                rect = this.getBoundingClientRect();
                                if (!this.classList.contains('sorting-grid-placeholder-item') && rect.top < event.clientY && rect.bottom > event.clientY
                                    && rect.left < event.clientX && event.clientX < rect.right && $this.isVisible(this)) {
                                    target = this;
                                    return false;
                                }
                            });
                            if (target) {
                                method = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5 ? 'after' : 'before';
                                break;
                            } else if (array[i].children.length == 0) {
                                let li = array[i].closest('li');
                                rect = li.getBoundingClientRect();
                                if (rect.top < event.clientY && rect.bottom > event.clientY && rect.left < event.clientX && event.clientX < rect.right) {
                                    target = array[i];
                                    method = 'append';
                                    li.classList.add('visible-branch');
                                    break;
                                }
                            }
                        }
                        if (target) {
                            element = target;
                            if (method != 'after') {
                                placeholders.forEach((li) => {
                                    $g(target)[method](li);
                                });
                            } else {
                                for (let i = placeholders.length - 1; i >= 0; i--) {
                                    $g(target)[method](placeholders[i]);
                                }
                            }
                            $this.prepareData(placeholder);
                            app.pageStructure.findEmpty();
                        }
                        helper.css({
                            top: top,
                            left: left,
                        });
                    }).off('mouseleave.gridSorting').on('mouseleave.gridSorting', function(){
                        $g(document).trigger('mouseup.gridSorting');
                    }).off('mouseup.gridSorting').on('mouseup.gridSorting', function(){
                        if (document.body.classList.contains('grid-sorting-started')) {
                            handle.classList.add('grid-sorting-return-animation');
                            helper.css($this.css);
                            handle.delay = setTimeout(function(){
                                handle.remove();
                                placeholders.forEach((li) => {
                                    li.classList.remove('sorting-grid-placeholder-item');
                                });
                                if (element) {
                                    $this.updatePlaceholders($this, element, method, placeholders);
                                    app.editor.$g('.ba-item').each(function(){
                                        if (app.editor.app.items[this.id]) {
                                            app.editor.initMapTypes(app.editor.app.items[this.id].type, this.id);
                                        }
                                    });
                                }
                            }, 300);
                            document.body.classList.remove('grid-sorting-started');
                            document.body.classList.remove('page-structure-sorting');
                        }
                        $g(document).off('mousemove.gridSorting mouseup.gridSorting mouseleave.gridSorting');
                    });
                }
            });
        }
    },
    context: {
        task: null,
        menus: {},
        items: [],
        getElementType: (item, click) => {
            let type = '';
            if (item.hasClass('ba-item') || (click && item.parent().parent().hasClass('ba-grid-column'))) {
                type = 'plugin';
            } else if (item.hasClass('ba-row')) {
                type = 'row';
            } else if (item.hasClass('ba-section')) {
                type = 'section';
            } else {
                type = 'column';
            }
            return type
        },
        execute: (task) => {
            app.pageStructure.context.task = task;
            app.pageStructure.context[task]();
        },
        renameAction: (title) => {
            let id = app.pageStructure.context.items[0].attr('id');
            app.editor.app.items[id].structureTitle = title;
            app.pageStructure.modal.find('li[data-id="'+id+'"] > span span').text(title);
            $g('.lightbox-options-panel[data-id="'+id+'"] p').text(title);
        },
        rename: () => {
            setTimeout(() => {
                let id = app.pageStructure.context.items[0].attr('id'),
                    modal = $g('#rename-modal');
                app.renameAction = 'pageStructure';
                modal.find('.new-name').val(app.pageStructure.getTitle(id));
                modal.find('#apply-rename').addClass('active-button');
                modal.modal();
            }, 100);
        },
        edit: () => {
            app.pageStructure.context.items[0].find('> .ba-edit-item .edit-item').trigger('mousedown');
        },
        delete: () => {
            app.itemDelete = null;
            app.deleteAction = 'page-structure-delete';
            app.checkModule('deleteItem');
        },
        library: () => {
            app.checkModule('addLibrary');
        },
        section: () => {
            $g('#add-section-dialog').removeClass('add-columns');
            app.checkModule('addSection');
        },
        row: () => {
            $g('#add-section-dialog').addClass('add-columns');
            app.checkModule('addSection');
        },
        columns: () => {
            app.pageStructure.context.row();
        },
        plugin: () => {
            app.checkModule('addPlugins');
        },
        empty: () => {
            app.itemDelete = null;
            app.deleteAction = 'page-structure-empty';
            app.checkModule('deleteItem');
        },
        paste: () => {
            app.editor.app.setNewFont = true;
            app.editor.app.fonts = {};
            app.editor.app.customFonts = {};
            if (app.buffer.store == 'style') {
                let is_object = obj = null,
                    id = '';
                app.pageStructure.context.items.forEach((item) => {
                    id = item.attr('id');
                    obj = app.editor.app.items[id];
                    for (let ind in app.buffer.data) {
                        if (ind == 'desktop') {
                            for (let key in app.buffer.data[ind]) {
                                is_object = typeof(obj[ind][key]) == 'object';
                                if (is_object) {
                                    obj[ind][key] = $g.extend(true, {}, app.buffer.data[ind][key]);
                                } else {
                                    obj[ind][key] = app.buffer.data[ind][key];
                                }
                            }
                            for (let breakpoint in app.editor.breakpoints) {
                                if (app.buffer.data[breakpoint]) {
                                    for (var key in app.buffer.data.desktop) {
                                        is_object = typeof(obj[breakpoint][key]) == 'object';
                                        if (is_object && app.buffer.data[breakpoint][key]) {
                                            obj[breakpoint][key] = $g.extend(true, {}, app.buffer.data[breakpoint][key]);
                                        } else if (!is_object && app.buffer.data[breakpoint][key]) {
                                            obj[breakpoint][key] = app.buffer.data[breakpoint][key];
                                        } else if (is_object) {
                                            obj[breakpoint][key] = {};
                                        } else {
                                            delete(obj[breakpoint][key]);
                                        }
                                    }
                                }
                            }
                        } else {
                            is_object = typeof(obj[ind]) == 'object';
                            obj[ind] = is_object ? $g.extend(true, {}, app.buffer.data[ind]) : app.buffer.data[ind];
                        }
                    }
                    app.editor.app.edit = id;
                    app.editor.app.checkModule('sectionRules');
                    if (obj.desktop.shape && 'setShapeDividers' in window) {
                        setShapeDividers(obj, id);
                    }
                    if (obj.type == 'progress-pie') {
                        app.drawPieLine();
                    }
                });
                app.editor.app.checkModule('checkOverlay');
                app.editor.app.checkVideoBackground();
                app.editor.app.checkModule('loadParallax');
                app.addHistory();
                if (app.selector && app.cp.inPosition()) {
                    app.editor.app.edit = app.selector.replace('#', '');
                }
            } else if (app.buffer.store == 'content' || app.buffer.store == 'cut-content'
                || app.buffer.store == 'item' || app.buffer.store == 'cut-item') {
                app.editor.app.copyAction = 'context';
                app.editor.app.checkModule('copyItem');
            }
            if (app.buffer.store == 'cut-item') {
                localStorage.setItem('gridboxBuffer', '');
                app.context = null;
            }
        },
        content: () => {
            let clone = buffer = null,
                id = type = html = '';
            app.pageStructure.context.items.forEach((item) => {
                id = item.attr('id');
                type = app.editor.app.items[id].type.replace('header', 'section').replace('footer', 'section')
                    .replace('overlay-section', 'section').replace('lightbox', 'section').replace('cookies', 'section')
                    .replace('mega-menu-section', 'section').replace('sticky-header', 'section');
                clone = item.clone();
                clone.removeClass('active-context-item active-context-item-editing page-structure-item-active')
                    .find('.page-structure-item-active').removeClass('page-structure-item-active');
                clone.find('.ba-item-overlay-section').each(function(){
                    app.editor.$g('.ba-overlay-section-backdrop[data-id="'+this.dataset.overlay+'"]').each((i, overlay) => {
                        overlay = overlay.cloneNode(true);
                        this.append(overlay);
                    });
                });
                clone.find('> .ba-section-items, > .ba-item, > .ba-row-wrapper').each((i, element) => {
                    html += element.outerHTML;
                })
            });            
            app.buffer = {
                type: type,
                store: 'content',
                data: {
                    html: html,
                    items: $g.extend(true, {}, app.editor.app.items)
                }
            }
            buffer = JSON.stringify(app.buffer);
            localStorage.setItem('gridboxBuffer', buffer);
        },
        cutContent: () => {
            let clone = buffer = null,
                id = type = html = '';
            app.pageStructure.context.items.forEach((item) => {
                id = item.attr('id');
                type = app.editor.app.items[id].type.replace('header', 'section').replace('footer', 'section')
                    .replace('overlay-section', 'section').replace('lightbox', 'section').replace('cookies', 'section')
                    .replace('mega-menu-section', 'section').replace('sticky-header', 'section');
                clone = item;
                clone.removeClass('active-context-item active-context-item-editing page-structure-item-active')
                    .find('.page-structure-item-active').removeClass('page-structure-item-active');
                clone.find('.ba-item-overlay-section').each(function(){
                    app.editor.$g('.ba-overlay-section-backdrop[data-id="'+this.dataset.overlay+'"]').each((i, overlay) => {
                        this.append(overlay);
                    });
                });
                clone.find('> .ba-item, > .ba-row-wrapper').each((i, element) => {
                    html += element.outerHTML;
                    element.remove();
                });
            });            
            app.buffer = {
                type: type,
                store: 'cut-content',
                data: {
                    html: html,
                    items: $g.extend(true, {}, app.editor.app.items)
                }
            }
            buffer = JSON.stringify(app.buffer);
            localStorage.setItem('gridboxBuffer', buffer);
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
            if (app.selector && app.cp.inPosition() && !app.editor.document.querySelector(app.selector)) {
                $g('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)').modal('hide');
            }
        },
        cut: () => {
            let clone = buffer = null,
                id = type = html = '';
            app.pageStructure.context.items.forEach((item) => {
                id = item.attr('id');
                type = app.editor.app.items[id].type;
                clone = item.hasClass('ba-section') || item.hasClass('ba-row') ? item.parent() : item;
                clone.removeClass('active-context-item active-context-item-editing page-structure-item-active')
                    .find('.page-structure-item-active').removeClass('page-structure-item-active');
                if (type == 'overlay-button' && clone.find('.ba-overlay-section-backdrop').length == 0) {
                    app.editor.$g('.ba-overlay-section-backdrop[data-id="'+clone[0].dataset.overlay+'"]').each((i, overlay) => {
                        clone.append(overlay);
                    });
                } else if (type == 'row' || type == 'section') {
                    clone.find('.ba-item-overlay-section').each(function(){
                        app.editor.$g('.ba-overlay-section-backdrop[data-id="'+this.dataset.overlay+'"]').each((i, overlay) => {
                            this.append(overlay);
                        });
                    });
                }
                html += clone[0].outerHTML;
                clone.remove();
            });
            app.buffer = {
                type: app.pageStructure.context.getElementType(app.pageStructure.context.items[0]),
                store: 'cut-item',
                data: {
                    html: html,
                    items: $g.extend(true, {}, app.editor.app.items)
                }
            }
            buffer = JSON.stringify(app.buffer);
            localStorage.setItem('gridboxBuffer', buffer);
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
            if (app.selector && app.cp.inPosition() && !app.editor.document.querySelector(app.selector)) {
                $g('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)').modal('hide');
            }
        },
        copy: () => {
            let clone = buffer = null,
                id = type = html = '';
            app.pageStructure.context.items.forEach((item) => {
                id = item.attr('id');
                type = app.editor.app.items[id].type;
                clone = item.hasClass('ba-section') || item.hasClass('ba-row') ? item.parent().clone() : item.clone();
                clone.removeClass('active-context-item active-context-item-editing page-structure-item-active')
                    .find('.page-structure-item-active').removeClass('page-structure-item-active');
                if (type == 'overlay-button' && clone.find('.ba-overlay-section-backdrop').length == 0) {
                    app.editor.$g('.ba-overlay-section-backdrop[data-id="'+clone[0].dataset.overlay+'"]').each((i, overlay) => {
                        overlay = overlay.cloneNode(true);
                        clone.append(overlay);
                    });
                } else if (type == 'row' || type == 'section') {
                    clone.find('.ba-item-overlay-section').each(function(){
                        app.editor.$g('.ba-overlay-section-backdrop[data-id="'+this.dataset.overlay+'"]').each((i, overlay) => {
                            overlay = overlay.cloneNode(true);
                            this.append(overlay);
                        });
                    });
                }
                html += clone[0].outerHTML;
            });
            app.buffer = {
                type: app.pageStructure.context.getElementType(app.pageStructure.context.items[0]),
                store: 'item',
                data: {
                    html: html,
                    items: $g.extend(true, {}, app.editor.app.items)
                }
            }
            buffer = JSON.stringify(app.buffer);
            localStorage.setItem('gridboxBuffer', buffer);
        },
        style: () => {
            let buffer = null,
                item = app.pageStructure.context.items[0],
                id = item.attr('id'),
                obj = app.editor.app.items[id],
                type = obj.type;
            if (presetsPatern[type]) {
                app.buffer = {
                    type: type,
                    store: 'style',
                    data: {}
                }
                let patern = $g.extend(true, {}, presetsPatern[type]),
                    is_object = null;;
                if (type == 'section' || type == 'row' || type == 'column') {
                    patern.desktop.image = '';
                    patern.desktop.video = '';
                    patern.desktop['background-states'] = '';
                }
                for (var ind in patern) {
                    if (ind == 'desktop') {
                        app.buffer.data[ind] = {};
                        for (var key in patern[ind]) {
                            is_object = typeof(obj[ind][key]) == 'object';
                            app.buffer.data[ind][key] = is_object ? $g.extend(true, {}, obj[ind][key]) : obj[ind][key];
                        }
                        for (var ind in app.editor.breakpoints) {
                            if (obj[ind]) {
                                app.buffer.data[ind] = {};
                                for (var key in patern.desktop) {
                                    is_object = typeof(obj[ind][key]) == 'object';
                                    if (is_object && obj[ind][key]) {
                                        app.buffer.data[ind][key] = $g.extend(true, {}, obj[ind][key]);
                                    } else if (!is_object && obj[ind][key]) {
                                        app.buffer.data[ind][key] = obj[ind][key];
                                    } else if (is_object) {
                                        app.buffer.data[ind][key] = {};
                                    }
                                }
                            }
                        }
                    } else {
                        is_object = typeof(obj[ind]) == 'object';
                        app.buffer.data[ind] = is_object ? $g.extend(true, {}, obj[ind]) : obj[ind];
                    }
                }
                buffer = JSON.stringify(app.buffer);
                localStorage.setItem('gridboxBuffer', buffer);
            }
        },
        resetStyle: (id) => {
            let obj = app.editor.app.items[id],
                type = obj.type.replace('header', 'section')
                    .replace('footer', 'section').replace('overlay-section', 'section')
                    .replace('lightbox', 'section').replace('cookies', 'section').replace('mega-menu-section', 'section')
                    .replace('sticky-header', 'section');
            if (!presetsPatern[type]) {
                return;
            }
            let patern = $g.extend(true, {}, presetsPatern[type]),
                is_object = null,
                theme = app.editor.app.theme,
                object = defaultElementsStyle[obj.type];
            if (type == 'section' || type == 'row' || type == 'column') {
                patern.desktop.image = '';
                patern.desktop.video = '';
                patern.desktop['background-states'] = '';
            }
            if (theme.defaultPresets[type] && theme.presets[type] && theme.presets[type][theme.defaultPresets[type]]) {
                object = $g.extend(true, object, theme.presets[type][theme.defaultPresets[type]].data);
            }
            for (let ind in patern) {
                if (ind == 'desktop') {
                    for (let key in patern[ind]) {
                        is_object = typeof(obj[ind][key]) == 'object';
                        obj[ind][key] = is_object ? $g.extend(true, {}, object[ind][key]) : object[ind][key];
                    }
                    for (let breakpoint in app.editor.breakpoints) {
                        if (obj[breakpoint]) {
                            for (let key in patern.desktop) {
                                is_object = typeof(obj[breakpoint][key]) == 'object';
                                if (is_object && object[breakpoint] && object[breakpoint][key]) {
                                    obj[breakpoint][key] = $g.extend(true, {}, object[breakpoint][key]);
                                } else if (!is_object && object[breakpoint] && object[breakpoint][key]) {
                                    obj[breakpoint][key] = object[breakpoint][key];
                                } else if (is_object) {
                                    obj[breakpoint][key] = {};
                                } else {
                                    delete(obj[breakpoint][key]);
                                }
                            }
                        }
                    }
                } else {
                    is_object = typeof(obj[ind]) == 'object';
                    obj[ind] = is_object ? $g.extend(true, {}, object[ind]) : object[ind];
                }
            }
            app.editor.app.setNewFont = true;
            app.editor.app.fonts = {};
            app.editor.app.customFonts = {};
            app.editor.app.edit = id;
            app.editor.app.checkModule('sectionRules');
            if (obj.desktop.shape && 'setShapeDividers' in window) {
                setShapeDividers(obj, id);
            }
            if (obj.type == 'progress-pie') {
                app.drawPieLine();
            }
            app.editor.app.checkModule('checkOverlay');
            app.editor.app.checkVideoBackground();
            app.editor.app.checkModule('loadParallax');
            app.addHistory();
            if (app.selector && app.cp.inPosition()) {
                app.editor.$g(app.selector+' > .ba-edit-item .edit-item').trigger('mousedown');
            }
        },
        reset: () => {
            app.pageStructure.context.items.forEach((item) => {
                app.pageStructure.context.resetStyle(item[0].id);
            });
        },
        checkMenu: (li) => {
            if (!li) {
                return false;
            }
            let key = li.dataset.type,
                intro = app.getIntroStr(),
                buffer = localStorage.getItem('gridboxBuffer'),
                context = $context = null;
            if (app.pageStructure.items.indexOf(li.dataset.id) == -1) {
                return false;
            }
            if (buffer) {
                app.buffer = JSON.parse(buffer);
            } else {
                app.buffer = null;
            }
            $g('.ba-context-menu').hide();
            if (!li.classList.contains('active')) {
                app.pageStructure.clickEvent(li, event, true);
            }
            app.pageStructure.context.items = [];
            app.pageStructure.modal.find('li.active[data-id]').each(function(){
                if (key != this.dataset.type) {
                    key = 'various';
                }
                app.pageStructure.context.items.push(app.editor.$g('#'+this.dataset.id));
            });
            if (key == 'various' || app.editor.app.items[li.dataset.id].type == 'checkout-order-form') {
                return false;
            }            
            context = app.pageStructure.context.menus[key];
            $context = $g(context);
            $context.find('.disable-button').removeClass('disable-button');
            if (app.pageStructure.context.items.length > 1) {
                $context.find('span[data-action="style"], span[data-action="edit"], span[data-action="rename"]').addClass('disable-button');
            }
            app.pageStructure.context.items.forEach(function(item){
                let type = app.editor.app.items[item[0].id].type,
                    eType = app.pageStructure.context.getElementType(item);
                if (type == 'lightbox' || type == 'cookies' || type == 'sticky-header') {
                    $context.find('span[data-action="library"], span[data-action="copy"], span[data-action="section"]').addClass('disable-button');
                } else if (type == 'footer' || type == 'header' || type == 'overlay-section' || type == 'mega-menu-section'
                    || item[0].dataset.cookie == 'accept' || type == 'category-intro' || type == 'error-message'
                    || type == 'blog-posts' || type == 'post-intro' || type == 'search-result' || type == 'store-search-result'
                    || type == 'checkout-form' || type == 'preloader' || item.hasClass('row-with-intro-items')
                    || item.find('.row-with-intro-items').length > 0) {
                    $context.find('[data-action="library"], [data-action="copy"], [data-action="delete"], [data-action="section"]')
                        .addClass('disable-button');
                }
                if (app.editor.themeData.edit_type == 'post-layout' && app.editor.themeData.app_type != 'blog') {
                    $context.find('span[data-action="delete"]').removeClass('disable-button');
                }
                if (type == 'overlay-section' || type == 'lightbox' || type == 'cookies' || item.hasClass('row-with-intro-items')
                    || item.find('.row-with-intro-items').length > 0 || item.find(intro).length > 0) {
                    $context.find('span[data-action="content"]').addClass('disable-button');
                }
                if (app.editor.themeData.edit_type == 'post-layout' && app.editor.themeData.app_type != 'blog') {
                    $context.find('span[data-action="empty"]').removeClass('disable-button');
                } else if (type == 'cookies'|| item.hasClass('row-with-intro-items')
                    || item.find('.row-with-intro-items').length > 0 || item.find(intro).length > 0) {
                    $context.find('span[data-action="empty"]').addClass('disable-button');
                }
                if (type == 'column' && item.parent().closest('.ba-grid-column').length > 0) {
                    $context.find('span[data-action="nested"]').addClass('disable-button');
                } else {
                    $context.find('span[data-action="nested"]').removeClass('disable-button');
                }
                if (!app.buffer || (app.buffer.store == 'style' && type != app.buffer.type)) {
                    $context.find('span[data-action="paste"]').addClass('disable-button');
                } else if (app.buffer && (app.buffer.store == 'item' || app.buffer.store == 'cut-item') &&
                    ((app.buffer.type == 'plugin' && eType != 'plugin' && eType != 'column')
                        || (app.buffer.type != 'plugin' && eType != app.buffer.type) || type == 'footer' || type == 'header'
                        || type == 'overlay-section' || type == 'lightbox' || type == 'cookies' || type == 'mega-menu-section')) {
                    $context.find('span[data-action="paste"]').addClass('disable-button');
                }
                type = type.replace('header', 'section').replace('footer', 'section').replace('overlay-section', 'section')
                    .replace('lightbox', 'section').replace('cookies', 'section').replace('mega-menu-section', 'section')
                    .replace('sticky-header', 'section');
                if (app.buffer && (app.buffer.store == 'content' || app.buffer.store == 'cut-content') && type != app.buffer.type) {
                    $context.find('span[data-action="paste"]').addClass('disable-button');
                }
            });

            return context;
        }
    },
    addEvents: () => {
        app.pageStructure.modal.on('hide', () => {
            app.pageStructure.visible = false;
            app.pageStructure.setPanel({
                visible: 1
            });
            app.editor.document.body.classList.remove('gridbox-page-structure-left');
            document.body.classList.remove('gridbox-page-structure-left');
        }).on('show', () => {
            if (app.pageStructure.modal.attr('data-position') == 'left') {
                app.editor.document.body.classList.add('gridbox-page-structure-left');
                document.body.classList.add('gridbox-page-structure-left');
            }
        });
        $g('.page-structure-context-menu').each((i, div) => {
            app.pageStructure.context.menus[div.dataset.type] = div;
        }).on('mousedown', 'span[data-action]', function(){
            if (!this.classList.contains('disable-button')) {
                app.pageStructure.context.execute(this.dataset.action)
            }
        });
        app.pageStructure.modal.on('hide', () => {
            app.editor.$g('.page-structure-item-active').removeClass('page-structure-item-active');
        });
        app.pageStructure.modal.find('.ba-page-structure-root-item > span').on('click', function(){
            let structure = this.closest('li').dataset.structure,
                obj = app.pageStructure.root[structure];
            if (obj && structure != 'canvas') {
                app.pageStructure.scrollIntoView(obj.selector, true);
            }
        });
        app.pageStructure.modal.find('.ba-page-structure-list').on('contextmenu', 'li[data-id] > span', function(event){
            event.stopPropagation();
            event.preventDefault();
            let top = event.clientY,
                left = event.clientX,
                deltaX = window.innerWidth - left,
                deltaY = window.innerHeight - top,
                li = this.closest('li'),
                context = app.pageStructure.context.checkMenu(li);
            if (!context) {
                return false;
            }
            context.style.display = 'block';
            context.style.setProperty('--context-height', context.offsetHeight+'px');
            if (deltaX - context.offsetWidth < 0) {
                context.classList.add('ba-left');
            } else {
                context.classList.remove('ba-left');
            }
            if (deltaY - context.offsetHeight < 0) {
                context.classList.add('ba-top');
                if (top < context.offsetHeight) {
                    top = context.offsetHeight + 10;
                }
            } else {
                context.classList.remove('ba-top');
                if (top + context.offsetHeight > window.innerHeight - 50) {
                    top = top - 10 - (top + context.offsetHeight - (window.innerHeight - 50));
                }
            }
            context.style.top = top+'px';
            context.style.left = left+'px';
        })
        app.pageStructure.modal.find('.select-page-structure-position').on('customAction', function(){
            let position = this.querySelector('input[type="hidden"]').value;
            app.pageStructure.modal.attr('data-position', position);
            app.editor.document.body.classList[position == '' ? 'remove' : 'add']('gridbox-page-structure-left');
            document.body.classList[position == '' ? 'remove' : 'add']('gridbox-page-structure-left');
            app.pageStructure.setPanel({
                position: 1
            });
        }).on('show', function(){
            let ul = this.querySelector('ul'),
                rect = null;
            ul.style.marginLeft = '';
            rect = ul.getBoundingClientRect();
            ul.style.marginLeft = rect.left + 265 > window.innerWidth ? '-185px' : '';
        });
        app.pageStructure.modal.find('.ba-page-structure-list').on('click', '.ba-branch-action', function(){
            let parent = this.closest('li'),
                h = 0;
            parent.querySelectorAll('ul > li').forEach(function(li){
                h += li.offsetHeight;
            });
            parent.style.setProperty('--branch-height', h+'px');
            setTimeout(function(){
                parent.classList.toggle('visible-branch');
                setTimeout(function(){
                    parent.style.setProperty('--branch-height', 'auto');
                }, 300);
            }, 50);
        });
        app.pageStructure.modal.find('.ba-page-structure-list').on('click', 'li[data-id] > span', function(event){
            let li = this.closest('li');
            app.pageStructure.clickEvent(li, event.originalEvent, true);
            event.preventDefault();
            if (app.cp.inPosition()) {
                app.editor.$g('#'+li.dataset.id+' > .ba-edit-item .edit-item').trigger('mousedown');
            }
            return false;
        });
        app.pageStructure.modal
    },
    clickEvent: (li, event, scroll) => {
        let start = null,
            type = lastType = app.pageStructure.context.getElementType(app.editor.$g('#'+li.dataset.id), true);
        scroll = li.closest('li.ba-page-structure-root-item[data-structure="canvas"]') ? false : scroll;
        if (li.dataset.element == 'tab' && li.dataset.type == 'section') {
            app.editor.$g('#'+li.dataset.id).each(function(){
                let tab = app.editor.$g(this).closest('.tab-pane'),
                    content = tab.closest('.tab-content');
                if (!tab.hasClass('active')) {
                    content.closest('.ba-item-tabs').find('a[href="#'+tab.attr('id')+'"]').trigger('click');
                }
                app.pageStructure.scrollIntoView(content, true);
            })
            return false;
        } else if (li.dataset.element == 'accordion' && li.dataset.type == 'section') {
            app.editor.$g('#'+li.dataset.id).each(function(){
                let content = app.editor.$g(this).closest('.accordion-body');
                if (!content.hasClass('in')) {
                    content.closest('.ba-item-accordion').find('a[href="#'+content.attr('id')+'"]').trigger('click');
                }
                setTimeout(function(){
                    app.pageStructure.scrollIntoView(content, true);
                }, 350);
            })
            return false;
        }
        if (event && event.shiftKey && app.pageStructure.lastActive) {
            lastType = app.pageStructure.context.getElementType(app.editor.$g('#'+app.pageStructure.lastActive), true);
            app.pageStructure.shiftItems.forEach((id) => {
                app.pageStructure.modal.find('li[data-id="'+id+'"]').removeClass('active');
                app.editor.$g('#'+id).removeClass('page-structure-item-active');
            });
            app.pageStructure.shiftItems = [];
            app.pageStructure.items.forEach((id) => {
                type = app.pageStructure.context.getElementType(app.editor.$g('#'+id), true);
                if (!start && (id == li.dataset.id || id == app.pageStructure.lastActive) && type == lastType) {
                    start = id;
                }
                if (start && type == lastType) {
                    app.pageStructure.modal.find('li[data-id="'+id+'"]').addClass('active');
                    app.editor.$g('#'+id).addClass('page-structure-item-active');
                    app.pageStructure.shiftItems.push(id);
                }
                if (start && start != id && (id == li.dataset.id || id == app.pageStructure.lastActive)) {
                    start = null;
                }
            });
        } else if (!event || (!event.shiftKey && !(event.ctrlKey || event.metaKey))) {
            app.pageStructure.modal.find('.ba-page-structure-list .active').removeClass('active');
            app.editor.$g('.page-structure-item-active').removeClass('page-structure-item-active');
        }
        type = app.pageStructure.context.getElementType(app.editor.$g('#'+li.dataset.id), true);
        if (event && (event.ctrlKey || event.metaKey)) {
            app.pageStructure.modal.find('li.active[data-id]').not(li).each(function(){
                lastType = app.pageStructure.context.getElementType(app.editor.$g('#'+this.dataset.id), true);
            });
        }
        if (type == lastType && (!event || !event.shiftKey || !app.pageStructure.lastActive)) {
            app.pageStructure.lastActive = li.dataset.id;
            app.pageStructure.shiftItems = [];
        }
        if (event && (event.ctrlKey || event.metaKey) && li.classList.contains('active')) {
            li.classList.remove('active');
            app.editor.$g('#'+li.dataset.id).removeClass('page-structure-item-active');
        } else if (type == lastType) {
            li.classList.add('active');
            app.pageStructure.scrollIntoView('#'+li.dataset.id, scroll).addClass('page-structure-item-active')
        }
    },
    scrollIntoView: (selector, scroll) => {
        let item = app.editor.$g(selector),
            top = item.offset().top - 40;
        if (scroll) {
            app.editor.$g('html, body').stop().animate({
                scrollTop: top
            }, 500);
        }

        return item;
    }
}

app.pageStructure.init();
app.modules.windowLoaded = true;
app.windowLoaded();