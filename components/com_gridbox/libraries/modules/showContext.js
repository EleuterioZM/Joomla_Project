/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var libHandle = document.getElementById('library-item-handle');
app.buffer = null;

app.showEditorRbtnContext = function(){
    $g('.ba-context-menu').hide();
    var iframe = document.querySelector('.editor-iframe'),
        rect = app.context.iframe ? iframe.getBoundingClientRect() : {top: 0, left: 0},
        deltaX = window.innerWidth - app.context.event.clientX + rect.left,
        deltaY = window.innerHeight - app.context.event.clientY + rect.top,
        content,
        top = app.context.event.clientY + rect.top,
        left = app.context.event.clientX + rect.left,
        context = document.querySelector('.'+app.context.context);
    context.style.display = 'block';
    context.style.setProperty('--context-height', context.offsetHeight+'px');
    context.dataset.type = app.context.itemType;
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
    var buffer = localStorage.getItem('gridboxBuffer'),
        type = app.context.item.type,
        introStr = app.getIntroStr();
    if (buffer) {
        app.buffer = JSON.parse(buffer);
    }
    if (app.context.itemType != 'column') {
        content = $g(app.context.target).find('> .ba-section-items > .ba-row-wrapper > .ba-row');
    } else {
        content = $g(app.context.target).find('> .ba-item, > .ba-row-wrapper > .ba-row');
    }
    if (content.length > 0) {
        $g('span.context-copy-content, span.context-delete-content').removeClass('disable-button');
    } else {
        $g('span.context-copy-content, span.context-delete-content').addClass('disable-button');
    }
    if (type == 'lightbox' || type == 'cookies') {
        $g('span.context-add-to-library, span.context-copy-item').addClass('disable-button');
    } else if (type == 'footer' || type == 'header' || type == 'overlay-section' || type == 'mega-menu-section'
        || app.context.target.dataset.cookie == 'accept' || type == 'category-intro' || type == 'error-message'
        || type == 'blog-posts' || type == 'post-intro' || type == 'search-result' || type == 'store-search-result'
        || type == 'sticky-header' || type == 'checkout-form' || type == 'submission-form' || type == 'submit-button'
        || type == 'preloader' || app.context.target.classList.contains('row-with-intro-items')
        || app.editor.$g(app.context.target).find('.row-with-intro-items').length > 0) {
        $g('.context-add-to-library, .context-copy-item, .context-delete-item, .context-add-new-section-after').addClass('disable-button');
    } else {
        $g('.context-add-to-library, .context-copy-item, .context-delete-item, .context-add-new-section-after').removeClass('disable-button');
    }
    if (app.editor.themeData.edit_type == 'post-layout' && app.editor.themeData.app_type != 'blog') {
        $g('span.context-delete-item').removeClass('disable-button');
    }
    if (type == 'overlay-section' || type == 'lightbox' || type == 'cookies'
        || app.context.target.classList.contains('row-with-intro-items')
        || app.editor.$g(app.context.target).find('.row-with-intro-items').length > 0
        || app.editor.$g(app.context.target).find(introStr).length > 0) {
        $g('.context-copy-content').addClass('disable-button');
    }
    if (app.editor.themeData.edit_type == 'post-layout' && app.editor.themeData.app_type != 'blog') {
        $g('.context-delete-content').removeClass('disable-button');
    } else if (type == 'cookies'|| app.context.target.classList.contains('row-with-intro-items')
        || app.editor.$g(app.context.target).find('.row-with-intro-items').length > 0
        || app.editor.$g(app.context.target).find(introStr).length > 0) {
        $g('.context-delete-content').addClass('disable-button');
    }
    if (app.buffer && (app.buffer.store == 'item' || app.buffer.store == 'cut-item' || app.buffer.store == 'content' || app.buffer.store == 'cut-content')
        && app.editor.themeData.app_type != 'single'
        && (app.buffer.data.html.indexOf('ba-item-related-posts') != -1 || app.buffer.data.html.indexOf('ba-item-post-tags') != -1
            || app.buffer.data.html.indexOf('ba-item-post-navigation') != -1)) {
        $g('span.context-paste-buffer').addClass('disable-button');
    } else if (app.buffer && (app.buffer.store == 'item' || app.buffer.store == 'cut-item') && app.context.context == 'plugin-context-menu' &&
        app.buffer.type != 'section' && app.buffer.type != 'row' && app.buffer.type != 'column') {
        $g('span.context-paste-buffer').removeClass('disable-button');
    } else if ((type == 'overlay-section' || type == 'lightbox' || type == 'cookies' || type == 'mega-menu-section') && app.buffer
        && (app.buffer.store == 'item' || app.buffer.store == 'cut-item' || app.buffer.store == 'content' || app.buffer.store == 'cut-content')) {
        $g('span.context-paste-buffer').addClass('disable-button');
    } else if (app.buffer && app.buffer.type == app.context.itemType &&
        ((app.buffer.store != 'item' && app.buffer.store != 'cut-item')
            || ((app.buffer.store == 'item' || app.buffer.store == 'cut-item') && type != 'footer' && type != 'header'))) {
        $g('span.context-paste-buffer').removeClass('disable-button');
    } else {
        $g('span.context-paste-buffer').addClass('disable-button');
    }
    if (app.context.itemType == 'column' && app.editor.$g(app.context.target).parent().closest('.ba-grid-column').length > 0) {
        $g('span.context-add-nested-row').addClass('disable-button');
    } else {
        $g('span.context-add-nested-row').removeClass('disable-button');
    }
    app.editor.$g(app.context.target).closest('div[class*="-wrapper"]').addClass('active-context-item')
        .parent().parents('.ba-grid-column-wrapper').addClass('active-context-item');
    app.editor.$g(app.context.target).addClass('active-context-item-editing')
        .parents('div[class*="-wrapper"]').addClass('active-context-item-editing');
    app.editor.$g(app.context.target).closest('li.megamenu-item').addClass('megamenu-editing')
        .closest('.ba-row-wrapper').addClass('row-with-megamenu')
        .closest('.ba-wrapper').addClass('section-with-megamenu')
        .closest('body').addClass('body-megamenu-editing');
}

app.showContext = function(){
    if (!app.context) {
        return false;
    }
    if (app.context.type && app.context.type == 'contextEvent') {
        setTimeout(function(){
            app.showEditorRbtnContext();
        }, 50);
        return false;
    } else if (app.context.dataset.context == 'responsive-context-menu' && app.context.classList.contains('disable-button')) {
        return false;
    } else if (app.context.dataset.context == 'page-structure') {
        if (!app.pageStructure.visible) {
            app.pageStructure.show();
        }
        return false;
    }
    var rect = app.context.getBoundingClientRect(),
        target = app.context.dataset.context,
        context = document.getElementsByClassName(target)[0];
    context.style.top = rect.bottom+'px';
    context.style.left = rect.left+'px';
    if (app.context.dataset.context == 'page-context-menu') {
        context.style.left = rect.right+'px';
    }
    setTimeout(function(){
        if (app.context.dataset.context == 'section-library-list') {
            if (app.context.classList.contains('system-type-preloader')) {
                return false;
            }
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task=editor.getLibraryItems",
                complete: function(msg){
                    var obj = JSON.parse(msg.responseText),
                        str = returnLibraryHtml(obj.sections, 'section', obj.delete, obj.global);
                    $g('.section-library-list .ba-library-item').parent().remove();
                    $g('#section-library-cell').prepend(str);
                    str = returnLibraryHtml(obj.plugins, 'plugin', obj.delete, obj.global);
                    $g('#plugins-library-cell').prepend(str);
                    $g('.editor-iframe').addClass('push-left-body');
                    if (app.editor) {
                        app.editor.document.getElementById('library-backdrop').classList.add('visible-backdrop');
                        app.editor.document.body.classList.add('push-left-body');
                    }
                }
            });
            $g(context).addClass('ba-sidebar-panel');
        } else if (app.context.dataset.context == 'section-page-blocks-list') {
            if (($g('body').hasClass('blog-post-editor-parent') && !$g('body').hasClass('advanced-blog-editor'))
                || app.context.classList.contains('system-type-preloader')) {
                return false;
            }
            $g('.editor-iframe').addClass('push-left-body');
            if (app.editor) {
                app.editor.document.getElementById('library-backdrop').classList.add('visible-backdrop');
                app.editor.document.body.classList.add('push-left-body');
            }
            $g(context).addClass('ba-sidebar-panel');
        }
        context.style.display = 'block';
    }, 15);
};

function returnLibraryHtml(array, type, delete_item, global_item)
{
    var str = '';
    for (var i = 0; i < array.length; i++) {
        str += '<span class="library-item-wrapper">';
        if (array[i].image) {
            str += '<span class="library-image" style="background-image:url('+JUri+array[i].image+');"><img src="';
            str += JUri+'components/com_gridbox/assets/images/default-theme.png">';
            str += '<div class="camera-container" data-id="'+array[i].id;
            str += '"><i class="zmdi zmdi-camera"></i></div></span>';
        }
        str += '<span class="ba-library-item" data-id="'+array[i].id+'">';
        str += '<span class="library-handle" data-type="'+type+'" data-id="'+array[i].id+'">';
        str += '<i class="zmdi zmdi-apps"></i></span><span class="library-title">';
        str += array[i].title+'</span>';
        if (array[i].global_item) {
            str += '<span class="library-global-item" data-id="'+array[i].global_item+'">';
            str += '<i class="zmdi zmdi-star"></i><span class="ba-tooltip ba-top">'+global_item+'</span></span>';
        }
        str += '<span class="delete-from-library" data-id="'+array[i].id+'">';
        str += '<i class="zmdi zmdi-delete"></i><span class="ba-tooltip ba-top">'+delete_item;
        str += '</span></span></span></span>';
    }

    return str;
}

function returnPointLibraryItem(event, type, offset)
{
    var pageY = event.clientY,
        pageX = event.clientX,
        item = null,
        rect = null,
        editSection = app.editor.document.getElementById('ba-edit-section'),
        str = '.ba-wrapper:not(.ba-lightbox):not(.ba-overlay-section):not(.tabs-content-wrapper)';
    if (type == 'section' || type == 'blocks') {
        $g(editSection).find(str).each(function(){
            rect = this.getBoundingClientRect();
            if (rect.top + offset < event.clientY && rect.bottom + offset > event.clientY &&
                rect.left < event.clientX && event.clientX < rect.right) {
                item = this;
                return false;
            }
        });
        if (!item) {
            item = editSection;
        }
    } else {
        editSection = app.editor.document.body;
        str = '.ba-grid-column-wrapper > .ba-grid-column';
        if (app.editor.document.querySelector('.ba-menu-wrapper.ba-hamburger-menu > .main-menu.visible-menu') &&
            app.editor.document.documentElement.offsetWidth <= app.editor.menuBreakpoint) {
            str = '.ba-menu-wrapper.ba-hamburger-menu > .main-menu.visible-menu';
        }
        var columns = [].slice.call(app.editor.document.querySelectorAll(str));
        columns = columns.reverse();
        for (var i = 0; i < columns.length; i ++) {
            $g(columns[i]).find(' > .ba-item, > .ba-row-wrapper, > .integration-wrapper').each(function(){
                rect = this.getBoundingClientRect();
                if (rect.top + offset < event.clientY && rect.bottom + offset > event.clientY &&
                    rect.left < event.clientX && event.clientX < rect.right) {
                    item = this;
                    return false;
                }
            });
            if (!item) {
                rect = columns[i].getBoundingClientRect();
                if (rect.top + offset < event.clientY && rect.bottom + offset > event.clientY &&
                    rect.left < event.clientX && event.clientX < rect.right) {
                    item = columns[i];
                    break;
                }
            } else {
                break;
            }
        }
    }
    
    return item;
}

$g('.associations-context-menu > span').on('mousedown', function(){
    let link = this.dataset.link;
    $g('.gridbox-save').off('saved.associations').one('saved.associations', function(){
        window.location.href = link;
    }).trigger('click');
});

$g('span.pages-list').on('mousedown', function(){
    setTimeout(function(){
        checkIframe($g('#pages-list-modal'), 'pages');
    }, 200);
    $g('body').trigger('mousedown');
    return false;
});

$g('.left-context-menu, #login-modal').on('mousedown', function(event){
    event.stopPropagation();
});

$g('.section-page-blocks-list .ba-page-block-item').on('mousedown', function(event){
    if (this.classList.contains('disabled')) {
        window.gridboxCallback = 'blocksAction';
        app.checkModule('login');
        return false;
    } else {
        var id = this.dataset.id,
            item = null,
            next;
        app.editor.app.edit = null;
        app.editor.app.checkModule('copyItem');
        $g('body').trigger('mousedown');
        libHandle.style.display = '';
        libHandle.style.top = event.clientY+'px';
        libHandle.style.left = event.clientX+'px';
        var placeholder = app.editor.document.getElementById('library-placeholder'),
            backdrop = app.editor.document.getElementById('library-backdrop');
        backdrop.dataset.id = id;
        $g(document).on('mousemove.library', function(event){
            libHandle.style.top = event.clientY+'px';
            libHandle.style.left = event.clientX+'px';
            placeholder.style.display = '';
            if (!backdrop.classList.contains('visible-backdrop')) {
                backdrop.classList.add('visible-backdrop');
            }
            item = returnPointLibraryItem(event, 'blocks', 80);
            if (item) {
                var rect = item.getBoundingClientRect(),
                    obj = {
                        "left" : rect.left + 16,
                        "width" : rect.right - rect.left - 30
                    };
                next = (event.clientY - (rect.top + 80)) / (rect.bottom - rect.top) > .5;
                if (next || item.classList.contains('ba-grid-column')) {
                    obj.top = rect.bottom;
                } else {
                    obj.top = rect.top;
                }
                $g(placeholder).css(obj);
            } else {
                placeholder.style.display = 'none';
            }
            return false;
        }).on('mouseup.library', function(event){
            libHandle.style.display = 'none';
            placeholder.style.display = 'none';
            backdrop.classList.remove('visible-backdrop');
            $g(document).off('mouseup.library mousemove.library');
            $g(app.editor.document).off('mouseup.library mousemove.library');
            var obj =  {
                "data" : item,
                "selector" : {
                    id : id,
                    type : 'blocks',
                    globalItem : null
                }
            };
            if (obj.data) {
                rect = obj.data.getBoundingClientRect();
                obj.selector.next = (event.clientY - (rect.top + 80)) / (rect.bottom - rect.top) > .5;
                app.editor.app.checkModule('setLibraryItem', obj);
            }
        });
        $g(app.editor.document).on('mousemove.library', function(event){
            libHandle.style.top = (event.clientY + 80)+'px';
            libHandle.style.left = (event.clientX + (window.innerWidth - app.editor.innerWidth) / 2)+'px';
            placeholder.style.display = '';
            item = returnPointLibraryItem(event, 'blocks', 0);
            if (item) {
                var rect = item.getBoundingClientRect(),
                    obj = {
                        "left" : rect.left + 16,
                        "width" : rect.right - rect.left - 30
                    };
                next = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5;
                if (next || item.classList.contains('ba-grid-column')) {
                    obj.top = rect.bottom;
                } else {
                    obj.top = rect.top;
                }
                $g(placeholder).css(obj);
            } else {
                placeholder.style.display = 'none';
            }
            return false;
        }).on('mouseup.library', function(event){
            libHandle.style.display = 'none';
            placeholder.style.display = 'none';
            $g(document).off('mouseup.library mousemove.library');
            $g(app.editor.document).off('mouseup.library mousemove.library');
            var obj =  {
                "data" : item,
                "selector" : {
                    id : id,
                    type : 'blocks',
                    next : next,
                    globalItem : null
                }
            };
            if (obj.data) {
                rect = obj.data.getBoundingClientRect();
                obj.selector.next = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5;
                app.editor.app.checkModule('setLibraryItem', obj);
            }
        });
        return false;
    }
});

$g('#section-library-cell, #plugins-library-cell').on('mousedown', '.library-handle', function(event){
    var id = this.dataset.id,
        type = this.dataset.type,
        item = null,
        globalItem = this.parentNode;
    globalItem = globalItem.querySelector('.library-global-item');
    if (globalItem) {
        globalItem = globalItem.dataset.id;
        var item = app.editor.document.getElementById(globalItem);
        if (item) {
            app.showNotice(gridboxLanguage['GLOBAL_ITEM_NOTICE']);
            return false;
        }
    }
    app.editor.app.edit = null;
    app.editor.app.checkModule('copyItem');
    $g('body').trigger('mousedown');
    libHandle.style.display = '';
    libHandle.style.top = event.clientY+'px';
    libHandle.style.left = event.clientX+'px';
    var placeholder = app.editor.document.getElementById('library-placeholder'),
        backdrop = app.editor.document.getElementById('library-backdrop');
    backdrop.dataset.id = id;
    $g(document).on('mousemove.library', function(event){
        libHandle.style.top = event.clientY+'px';
        libHandle.style.left = event.clientX+'px';
        placeholder.style.display = '';
        if (!backdrop.classList.contains('visible-backdrop')) {
            backdrop.classList.add('visible-backdrop');
        }
        item = returnPointLibraryItem(event, type, 80);
        if (item) {
            var rect = item.getBoundingClientRect(),
                next = (event.clientY - (rect.top + 80)) / (rect.bottom - rect.top) > .5,
                obj = {
                    "left" : rect.left + 16,
                    "width" : rect.right - rect.left - 30
                };
            if (next || item.classList.contains('ba-grid-column')) {
                obj.top = rect.bottom;
            } else {
                obj.top = rect.top;
            }
            $g(placeholder).css(obj);
        } else {
            placeholder.style.display = 'none';
        }
        return false;
    }).on('mouseup.library', function(event){
        libHandle.style.display = 'none';
        placeholder.style.display = 'none';
        backdrop.classList.remove('visible-backdrop');
        $g(document).off('mouseup.library mousemove.library');
        $g(app.editor.document).off('mouseup.library mousemove.library');
        var obj =  {
            "data" : item,
            "selector" : {
                id : id,
                type : type,
                globalItem : globalItem
            }
        };
        if (obj.data) {
            rect = obj.data.getBoundingClientRect();
            obj.selector.next = (event.clientY - (rect.top + 80)) / (rect.bottom - rect.top) > .5;
            app.editor.app.checkModule('setLibraryItem', obj);
        }
    });
    $g(app.editor.document).on('mousemove.library', function(event){
        libHandle.style.top = event.clientY+80+'px';
        libHandle.style.left = (event.clientX + (window.innerWidth - app.editor.innerWidth) / 2)+'px';
        placeholder.style.display = '';
        item = returnPointLibraryItem(event, type, 0);
        if (item) {
            var rect = item.getBoundingClientRect(),
                next = (event.clientY - (rect.top)) / (rect.bottom - rect.top) > .5,
                obj = {
                    "left" : rect.left + 16,
                    "width" : rect.right - rect.left - 30
                };
            if (next || item.classList.contains('ba-grid-column')) {
                obj.top = rect.bottom;
            } else {
                obj.top = rect.top;
            }
            $g(placeholder).css(obj);
        } else {
            placeholder.style.display = 'none';
        }
        return false;
    }).on('mouseup.library', function(event){
        libHandle.style.display = 'none';
        placeholder.style.display = 'none';
        $g(document).off('mouseup.library mousemove.library');
        $g(app.editor.document).off('mouseup.library mousemove.library');
        var obj =  {
            "data" : item,
            "selector" : {
                id : id,
                type : type,
                globalItem : globalItem
            }
        };
        if (obj.data) {
            rect = obj.data.getBoundingClientRect();
            obj.selector.next = (event.clientY - (rect.top)) / (rect.bottom - rect.top) > .5;
            app.editor.app.checkModule('setLibraryItem', obj);
        }
    });
    return false;
});

$g('#section-library-cell, #plugins-library-cell').on('mousedown', '.delete-from-library', function(event){
    app.itemDelete = this.dataset.id;
    if ($g(this).closest('.ba-library-item').find('.library-global-item').length > 0) {
        $g('#delete-dialog p').text(app._('ATTENTION_DELETE_GLOBAL'));
    }
    app.checkModule('deleteItem');
});

$g('#section-library-cell, #plugins-library-cell').on('mousedown', '.camera-container', function(event){
    app.itemDelete = this.dataset.id;
    uploadMode = 'reselectLibraryImage';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('span.add-to-menu').on('mousedown', function(){
    app.checkModule('addToMenu');
});

$g('span.ba-page-versions').on('mousedown', function(){
    $g('.draggable-modal-cp.in').not('#page-structure-dialog').modal('hide');
    app.cp.show();
    app.versions.show();
});

app.versions = {
    modal: $g('#version-history-dialog'),
    wrapper: document.querySelector('.versions-history-wrapper'),
    template: document.querySelector('template.version-history-item'),
    items: [],
    get: () => {
        app.versions.wrapper.innerHTML = '';
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.getVersionsHistory', {
            id: app.editor.themeData.id
        }).then((text) => {
            app.versions.items = JSON.parse(text);
            app.versions.render();
        });
    },
    cleanup: () => {
        let str = app._('LOADING')+'<img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
        app.notification[0].querySelector('p').innerHTML = str;
        app.notification[0].className = 'notification-in';
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.cleanupVersionsHistory').then(() => {
            app.notification.removeClass('notification-in').addClass('animation-out');
            app.versions.wrapper.innerHTML = '';
            app.versions.items = [];
        })
    },
    delete: (item) => {
        let ind = item.dataset.ind;
        item.remove();
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.deleteVersionsHistory', {
            id: app.versions.items[ind].id,
            page_id: app.versions.items[ind].page_id
        }).then((text) => {
            app.versions.wrapper.innerHTML = '';
            app.versions.items = JSON.parse(text);
            app.versions.render();
        })
    },
    rename: (title) => {
        let ind = app.versions.edit;
        app.versions.items[ind].title = title;
        app.versions.wrapper.querySelector('.version-history-item[data-ind="'+ind+'"] .version-history-item-title span').textContent = title;
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.renameVersionsHistory', {
            id: app.versions.items[ind].id,
            title: title
        });
    },
    apply: (ind) => {
        let div = app.editor.document.body,
            json = JSON.parse(app.versions.items[ind].items),
            items = app.editor.app.items;
        div.querySelectorAll('.ba-item-scroll-to-top, .ba-social-sidebar').forEach(function(el){
            let id = items[el.id].parent,
                item = div.querySelector('#'+id);
            if (!item) {
                item = div.querySelector('.ba-grid-column');
            }
            if (item) {
                items[el.id].parent = item.id;
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
            let parent = div.querySelector('#'+items[el.id].positioning.parent);
            if (!parent) {
                parent = div.querySelector('.ba-grid-column');
                items[el.id].positioning.parent = parent.id;
            }
            $g(parent).find('> .empty-item').before(el);
        });
        div.querySelectorAll('.ba-item-reading-progress-bar').forEach(function(el){
            let parent = div.querySelector('#'+items[el.id].parent);
            if (!parent) {
                parent = div.querySelector('.ba-grid-column');
            }
            $g(parent).find('> .empty-item').before(el);
        });
        div.querySelector('#ba-edit-section').querySelectorAll('.ba-item, .ba-section, .ba-row, .ba-grid-column').forEach((el) => {
            if (items[el.id]) {
                delete items[el.id];
            }
        });
        div.querySelector('#ba-edit-section').innerHTML = app.versions.items[ind].html;
        for (let i in json) {
            items[i] = json[i];
        }
        app.editor.$g('.ba-section, .ba-row, .ba-grid-column').each(function(){
            if (app.editor.app.items[this.id] && app.editor.app.items[this.id].desktop.animation.effect &&
                app.editor.$g(this).closest('.ba-item-content-slider').length == 0) {
                this.classList.remove(app.editor.app.items[this.id].desktop.animation.effect);
            }
        });
        app.editor.app.edit = 'body';
        app.editor.app.checkModule('sectionRules');
        app.editor.app.setDefaultElementsBox();
        app.editor.app.init();
        app.editor.app.checkAnimation();
        app.editor.app.checkOverlay();
        if (app.pageStructure && app.pageStructure.visible) {
            app.pageStructure.updateStructure(true);
        }
    },
    addEvents: () => {
        $g(app.versions.wrapper).on('click', '.delete-version-history', function(){
            app.itemDelete = {
                type: 'version',
                item: this.closest('.version-history-item')
            }
            app.checkModule('deleteItem');
        }).on('click', '.edit-version-history', function(){
            let ind = this.closest('.version-history-item').dataset.ind,
                modal = $g('#rename-modal');
            app.versions.edit = ind;
            app.renameAction = 'version';
            modal.find('.new-name').val(app.versions.items[ind].title);
            modal.find('#apply-rename').addClass('active-button');
            modal.modal();
        }).on('click', '.version-history-item-title', function(){
            let item = this.closest('.version-history-item'),
                ind = item.dataset.ind;
            app.versions.modal.find('.version-history-item.active').removeClass('active');
            item.classList.add('active');
            app.versions.apply(ind);
        })
        app.versions.modal.find('.cleanup-versions-history').on('click', () => {
            app.itemDelete = {
                type: 'cleanup'
            }
            $g('#delete-dialog').find('h3, p').each((i, el) => {
                el.textContent = app._(el.localName == 'h3' ? 'ATTENTION' : 'CLEANUP_VERSIONS_MESSAGE')
            })
            app.checkModule('deleteItem');
        });
    },
    render: () => {
        app.versions.items.forEach((item, i) => {
            let clone = app.versions.template.content.cloneNode(true);
            clone.querySelector('.version-history-item-title span').textContent = item.title;
            clone.querySelector('.version-history-item').dataset.ind = i;
            app.versions.wrapper.append(clone);
        });
    },
    show: () => {
        app.versions.get();
        setTimeout(function(){
            app.versions.modal.modal();
        }, 150);
    }
}

app.versions.addEvents();

$g('span.context-edit-item').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .edit-item').trigger('mousedown');
    }
});
$g('span.context-add-new-row').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .add-columns').trigger('mousedown');
    }
});
$g('span.context-add-new-section-after').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('section');
    }
});
$g('span.context-add-new-row-after').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('row');
    }
});
$g('span.context-add-new-element-after').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('plugin');
    }
});
$g('span.context-modify-columns').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .modify-columns').trigger('mousedown');
    }
});

$g('span.context-add-nested-row').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .add-columns-in-columns').trigger('mousedown');
    }
});
$g('span.context-add-new-element').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .add-item').trigger('mousedown');
    }
});

$g('span.context-add-to-library').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .add-library').trigger('mousedown');
    }
});
$g('span.context-delete-item').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.editor.$g(app.context.target).find('> .ba-edit-item .delete-item').trigger('mousedown');
    }
});
$g('span.context-cut-content').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('cutContent');
    }
});
$g('span.context-cut-item').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('cut');
    }
});
$g('span.context-copy-item').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('copy');
    }
});
$g('span.context-in-page-structure').on('mousedown', function(){
    app.pageStructure.inStructure(app.context.target.id);
});
$g('span.context-copy-style').on('mousedown', function(){
    if (presetsPatern[app.context.itemType] && !this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('style');
    }
});
$g('span.context-copy-content').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('content');
    }
});
$g('span.context-paste-buffer').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.items = [];
        app.pageStructure.context.items.push(app.editor.$g(app.context.target));
        app.pageStructure.context.execute('paste');
    }
});
$g('span.context-delete-content').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.itemDelete = null;
        app.deleteAction = 'context';
        app.checkModule('deleteItem');
    }
});
$g('span.context-reset-style').on('mousedown', function(){
    if (!this.classList.contains('disable-button')) {
        app.pageStructure.context.resetStyle(app.context.target.id);
    }
});

$g('.save-as-context-menu span[data-action]').on('mousedown', function(){
    let button = document.querySelector('.gridbox-save');
    if (button.dataset.action == 'clicked' || button.classList.contains('.gridbox-enabled-save') || !this.dataset.action) {
        return;
    }
    app.saveMode = this.dataset.action;
    app.checkModule('gridboxSave');
});

app.modules.showContext = true;
app.showContext();