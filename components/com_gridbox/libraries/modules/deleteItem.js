/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.deleteItem = function(){
	setTimeout(function(){
		$g("#delete-dialog").modal();
	}, 50);
}

$g("#delete-dialog").on('hidden-gridbox-modal', function(){
    this.querySelector('h3').textContent = app._('DELETE_ITEM');
    this.querySelector('p').textContent = app._('MODAL_DELETE');
    app.deleteAction = null;
});

function removeItem(item, search)
{
    item.find(search).each(function(){
        if (app.editor.app.items[this.id] && app.editor.app.items[this.id].type == 'overlay-button') {
            var overlay =  app.editor.document.querySelector('.ba-overlay-section-backdrop[data-id="'+this.dataset.overlay+'"]'),
                $overlay = $g(overlay);
            removeItem($overlay, '.ba-section');
            removeItem($overlay, '.ba-row');
            removeItem($overlay, '.ba-grid-column');
            removeItem($overlay, '.ba-item');
            $overlay.remove();
        } else if (app.editor.app.items[this.id] && app.editor.app.items[this.id].type == 'one-page') {
            if (app.editor.app.items[this.id].autoscroll) {
                app.editor.app.items[this.id].autoscroll.enable = false;
            }
        }
        delete(app.editor.app.items[this.id]);
    });
}

$g('#delete-dialog a.ba-btn[data-dismiss="modal"]').on('mousedown', function(){
    if ($g('#menu-item-edit-modal').hasClass('in')) {
        $g('#menu-item-edit-modal input[data-property="megamenu"]').prop('checked', true);
    }
});

app.DOMdeleteItem = function(item, key){
    var childApp = app.editor.app,
        type = null;
    if (childApp.items[key]) {
        type = childApp.items[key].type;
    }
    if (type == 'sticky-header') {
        item.closest('header, footer, .body').classList.remove('ba-sticky-header-parent');
    }
    if ((!childApp.items[key] && item && !item.classList.contains('ba-item')) || type == 'section' || type == 'row'
         || type == 'lightbox' || type == 'cookies' || type == 'sticky-header') {
        if (item.parentNode.localName != 'body') {
            item = item.parentNode;
        }
        removeItem($g(item), '.ba-section');
        removeItem($g(item), '.ba-row');
        removeItem($g(item), '.ba-grid-column');
        removeItem($g(item), '.ba-item');
    } else {
        if (childApp.items[key] && childApp.items[key].type == 'one-page') {
            if (childApp.items[key].autoscroll) {
                childApp.items[key].autoscroll.enable = false;
            }
        }
        delete(childApp.items[key]);
        removeItem($g(item), '.ba-item');
    }
    if (type == 'lightbox' || type == 'cookies') {
        $g('#lightbox-panels').find('div[data-id="'+key+'"]').remove();
        item = item.parentNode;
    }
    if (type == 'sticky-header') {
        $g('#lightbox-panels').find('div[data-id="'+key+'"]').remove();
        document.body.classList.remove('sticky-header-opened');
    }
    if (type == 'overlay-button') {
        var overlay =  app.editor.$g('.ba-overlay-section-backdrop[data-id="'+item.dataset.overlay+'"]');
        removeItem(overlay, '.ba-section');
        removeItem(overlay, '.ba-row');
        removeItem(overlay, '.ba-grid-column');
        removeItem(overlay, '.ba-item');
        overlay.remove();
    }
    item.parentNode.removeChild(item);
    app.editor.$g('.row-with-sidebar-menu').each(function(){
        if (!this.querySelector('.ba-item-one-page-menu.side-navigation-menu')) {
            this.classList.remove('row-with-sidebar-menu');
        }
    });
    if (app.pageStructure && app.pageStructure.visible) {
        app.pageStructure.updateStructure(true);
    }
}

$g('#apply-delete').off('mousedown').on('mousedown', function(){
    if ($g('#menu-item-edit-modal').hasClass('in')) {
        $g("#delete-dialog").modal('hide');
        return false;
    }
    if (app.itemDelete) {
        if (typeof(app.itemDelete) == 'object' && app.itemDelete.type == 'cleanup') {
            app.versions.cleanup();
        } else if (typeof(app.itemDelete) == 'object' && app.itemDelete.type == 'version') {
            app.versions.delete(app.itemDelete.item);
        } else if (typeof(app.itemDelete) == 'object' && app.itemDelete.type == 'delete-field-item') {
            let variation = container = null;
            app.itemDelete.items.forEach(function(item){
                container = item.closest('.field-sorting-wrapper');
                variation = item.closest('.blog-post-editor-options-group[data-field-type="product-options"]');
                item.remove();
                if (variation) {
                    $g('.variations-table-body .variations-table-row').remove();
                    app.productVariations = {};
                    prepareProductVariations();
                }
            });
            if (container) {
                $g(container).find('.sorting-toolbar-action[data-action="check"]')
                    .attr('data-checked', true).trigger('click');
            }
        } else if (typeof(app.itemDelete) == 'object' && app.itemDelete.type == 'delete-badge-item') {
            app.badges.deleteBadge(app.itemDelete.item);
        } else if ($g('#customer-info-item-dialog').hasClass('in')) {
            app.itemDelete.remove();
        } else if (app.itemDelete.indexOf('ba-delete-preset:') === 0) {
            var key = app.itemDelete.replace('ba-delete-preset:', '');
            delete(app.editor.app.theme.presets[app.edit.type][key]);
            if (app.editor.app.theme.defaultPresets[app.edit.type] == key) {
                delete(app.editor.app.theme.defaultPresets[app.edit.type]);
            }
            app.editor.app.checkModule('editItem');
            for (var ind in app.editor.app.items) {
                if (app.editor.app.items[ind].preset == key) {
                    app.editor.comparePresets(app.editor.app.items[ind]);
                    app.editor.app.items[ind].preset = '';
                }
            }
            app.editor.app.checkModule('editItem');
        } else if (app.itemDelete == 'deleteVariationsPhotos') {
            $g('#product-variations-photos-dialog input[type="checkbox"]').each(function(){
                if (this.checked) {
                    this.closest('.sorting-item').remove();
                }
            });
            reorderVariationsPhotos()
        } else if ($g('#customer-info-settings-dialog').hasClass('in')) {
            app.edit.items.splice(app.itemDelete, 1);
            app.edit.items.forEach(function(obj, i){
                obj.order_list = i;
            });
            app.editor.$g(app.selector+' .ba-checkout-form-fields:nth-child('+(app.itemDelete * 1 + 1)+')').remove();
            getCustomerInfoList();
        } else if ($g('#social-icons-settings-dialog').hasClass('in')) {
            let i = 0,
                list = {};
            for (let ind in app.edit.icons) {
                if (app.itemDelete.indexOf(ind) == -1) {
                    list[i++] = app.edit.icons[ind];
                } else {
                    sortingList[ind].parent.remove();
                }
            }
            app.edit.icons = list;
            drawSocialIconsSorting();
            app.addHistory();
        } else if ($g('#menu-settings-dialog').hasClass('in') && app.edit.type == 'one-page') {
            for (let ind in sortingList) {
                if (app.itemDelete.indexOf(ind) != -1) {
                    sortingList[ind].parent.remove();
                }
            }
            createOnePageSortingList();
            app.addHistory();
        } else if ($g('#menu-settings-dialog').hasClass('in') && app.edit.type == 'menu') {
            let data = {
                    parent_id: [],
                    id: []
                };
            app.itemDelete.forEach(function(i){
                let parent_id = 1,
                    id = sortingList[i].id,
                    li = app.editor.$g(app.selector+' li.item-'+id),
                    item = $g('#menu-settings-dialog .menu-options .sorting-item[data-key="'+i+'"]'),
                    parent = item.closest('.deeper-sorting-container');
                if (parent.length > 0) {
                    parent_id = parent.attr('data-parent') * 1;
                }
                item.find('+ .deeper-sorting-container > .sorting-item-wrapper').each(function(){
                    let key = $g(this).find('> .sorting-item').attr('data-key');
                    item.parent().before(this);
                    li.before(app.editor.$g('li.item-'+sortingList[key].id));
                });
                data.parent_id.push(parent_id);
                data.id.push(id);
                li.remove();
                item.remove();
            });
            app.fetch(JUri+"index.php?option=com_gridbox&task=editor.deleteMenuItem", data);
            createMenuSortingList();
        } else if ($g('#map-editor-dialog').hasClass('in')) {
            if (locationMarkers[app.itemDelete]) {
                locationMarkers[app.itemDelete].marker.setMap(null);
                if (locationMarkers[app.itemDelete].marker.infoWindow) {
                    locationMarkers[app.itemDelete].marker.infoWindow.close();
                }
            }
            if (app.itemDelete != 0) {
                delete(locationMarkers[app.itemDelete]);
                delete(app.edit.marker[app.itemDelete]);
                $g('#map-editor-dialog .sorting-item[data-marker="'+app.itemDelete+'"]').remove();
            } else {
                delete(app.edit.marker[app.itemDelete].position);
                app.edit.marker[app.itemDelete].place = '';
                app.edit.marker[app.itemDelete].description = '';
                $g('#choose-location').val('');
            }
            setMarker();
            app.addHistory();
        } else if ($g('#yandex-maps-editor-dialog').hasClass('in')) {
            if (locationMarkers[app.itemDelete]) {
                locationMarkers[app.itemDelete].marker.setParent(null);
            }
            if (app.itemDelete != 0) {
                delete(locationMarkers[app.itemDelete]);
                delete(app.edit.marker[app.itemDelete]);
                $g('#yandex-maps-editor-dialog .sorting-item[data-marker="'+app.itemDelete+'"]').remove();
            } else {
                delete(app.edit.marker[app.itemDelete].position);
                app.edit.marker[app.itemDelete].place = '';
                app.edit.marker[app.itemDelete].description = '';
                $g('#yandex-choose-location').val('');
            }
            var i = 0,
                object = {};
            for (var ind in app.edit.marker) {
                if (locationMarkers[ind] && locationMarkers[ind].marker) {
                    locationMarkers[ind].marker.setParent(null);
                }
                object[i++] = app.edit.marker[ind];
            }
            app.edit.marker = object;
            initYandexMarkers();
            app.addHistory();
        } else if ($g('#openstreetmap-editor-dialog').hasClass('in')) {
            if (locationMarkers[app.itemDelete]) {
                locationMarkers[app.itemDelete].marker.remove();
            }
            if (app.itemDelete != 0) {
                delete(locationMarkers[app.itemDelete]);
                delete(app.edit.marker[app.itemDelete]);
                $g('#openstreetmap-editor-dialog .sorting-item[data-marker="'+app.itemDelete+'"]').remove();
            } else {
                delete(app.edit.marker[app.itemDelete].position);
                app.edit.marker[app.itemDelete].place = '';
                app.edit.marker[app.itemDelete].description = '';
                $g('#openstreetmap-choose-location').val('');
            }
            var i = 0,
                object = {};
            for (var ind in app.edit.marker) {
                if (locationMarkers[ind] && locationMarkers[ind].marker) {
                    locationMarkers[ind].marker.remove();
                }
                object[i++] = app.edit.marker[ind];
            }
            app.edit.marker = object;
            initOpenstreetmapMarkers();
            app.addHistory();
        } else if ($g('#item-settings-dialog').hasClass('in')) {
            app.itemDelete.forEach(function(i){
                sortingList[i].parent.remove();
            });
            getSimpleSortingList();
            app.addHistory();
        } else if ($g('#tabs-settings-dialog').hasClass('in')) {
            app.itemDelete.forEach(function(ind){
                let obj = sortingList[ind];
                obj.parent.remove();
                if (app.edit.type == 'tabs') {
                    app.editor.document.querySelector(obj.href).remove();
                }
            });
            createTabsSortingList();
            if (app.edit.type == 'tabs') {
                let active = false;
                for (let ind in sortingList) {
                    if (sortingList[ind].parent.classList.contains('active')) {
                        break;
                    }
                }
                if (!active) {
                    for (let ind in sortingList) {
                        sortingList[ind].parent.classList.add('active');
                        app.editor.document.querySelector(sortingList[ind].href).classList.add('active');
                        break;
                    }
                }
            }
            app.addHistory();
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
        } else if ($g('#slideshow-settings-dialog').hasClass('in')) {
            let slides = {},
                i = 1;
            for (var ind in app.edit.desktop.slides) {
                if (app.itemDelete.indexOf(ind * 1) != -1) {
                    sortingList[ind].parent.remove();
                } else {
                    slides[i++] = app.edit.desktop.slides[ind];
                }
            }
            app.edit.desktop.slides = slides;
            for (var point in app.editor.breakpoints) {
                if (app.edit[point] && app.edit[point].slides) {
                    slides = {};
                    i = 1;
                    for (var ind in app.edit[point].slides) {
                        if (app.itemDelete.indexOf(ind * 1) == -1) {
                            slides[i++] = app.edit[point].slides[ind];
                        }
                    }
                    app.edit[point].slides = slides;
                }
            }
            app.sectionRules();
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            }
            app.editor.app.checkModule('initItems', object);
            getSlideshowSorting();
            app.addHistory();
        } else if ($g('.section-library-list').hasClass('ba-sidebar-panel')) {
            $g.ajax({
                type:"POST",
                dataType:'text',
                url:JUri+"index.php?option=com_gridbox&task=editor.removeLibrary",
                data:{
                    id : app.itemDelete
                }
            });
            var item = document.querySelector('.ba-library-item[data-id="'+app.itemDelete+'"]').parentNode;
            item.parentNode.removeChild(item);
        } else if ($g('#content-slider-settings-dialog').hasClass('in')) {
            let i = 1,
                slides = {};
            for (let ind in app.edit.slides) {
                ind *= 1;
                if (app.itemDelete.indexOf(ind) == -1) {
                    slides[i++] = app.edit.slides[ind];
                } else {
                    let li = $g(sortingList[ind].parent),
                        id = li.find('> .ba-grid-column').attr('id');
                    app.DOMdeleteItem(app.editor.document.getElementById(id), id);
                    li.remove();
                }
            }
            app.edit.slides = slides;
            drawContentSliderSortingList();
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            }
            app.editor.app.checkModule('initItems', object);
            app.sectionRules();
            app.addHistory();
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
        } else if ($g('#testimonials-settings-dialog').hasClass('in')) {
            let i = 1,
                slides = {};
            for (let ind in app.edit.slides) {
                if (app.itemDelete.indexOf(ind) == -1) {
                    slides[i++] = app.edit.slides[ind];
                } else {
                    sortingList[ind].parent.remove();
                }
            }
            app.edit.slides = slides;
            drawTestimonialsSortingList();
            app.testimonialsCallback();
            app.addHistory();
        } else if ($g('#icon-list-settings-dialog').hasClass('in')) {
            let i = 1,
                list = {};
            for (let ind in app.edit.list) {
                if (app.itemDelete.indexOf(ind) == -1) {
                    list[i++] = app.edit.list[ind];
                } else {
                    sortingList[ind].parent.remove();
                }
            }
            app.edit.list = list;
            drawIconListSortingList();
            app.addHistory();
        } else if ($g('#feature-box-settings-dialog').hasClass('in')) {
            let i = 0,
                list = {};
            for (let ind in app.edit.items) {
                if (app.itemDelete.indexOf(ind) == -1) {
                    list[i++] = app.edit.items[ind];
                } else {
                    sortingList[ind].parent.remove();
                }
            }
            app.edit.items = list;
            drawFeatureBoxSortingList();
            app.addHistory();
        } else if ($g('#group-field-item-dialog').hasClass('in')) {
            $g('#group-field-item-dialog .sorting-item[data-key="'+app.itemDelete+'"]').remove();
        } else if ($g('#field-settings-dialog').hasClass('in') && app.edit.type == 'field') {
            var ind = 0,
                list = {};
            $g('#field-settings-dialog .fields-plugin-options .sorting-item').each(function(){
                if (app.itemDelete != this.dataset.key) {
                    list[ind++] = app.edit.options.items[this.dataset.key];
                }
            });
            app.edit.options.items = list;
            drawFieldSortingList();
            app.addHistory();
        } else if ($g('#field-settings-dialog').hasClass('in') && app.edit.type == 'field-group') {
            var ind = 0,
                list = {};
            $g('#field-settings-dialog .fields-group-plugin-options .sorting-item').each(function(){
                if (app.itemDelete != this.dataset.key) {
                    list[ind++] = app.edit.items[this.dataset.key];
                } else {
                    app.editor.$g(app.selector+' .ba-field-wrapper[data-id="'+app.edit.items[this.dataset.key].field_key+'"]').remove();
                }
            });
            app.edit.items = list;
            drawFieldGroupSortingList();
            app.addHistory();
        }
    } else {
        if (app.deleteAction == 'context') {
            let content = null;
            if (app.context.itemType != 'column') {
                content = $g(app.context.target).find('> .ba-section-items > .ba-row-wrapper > .ba-row');
            } else {
                content = $g(app.context.target).find('> .ba-item, > .ba-row-wrapper > .ba-row');
            }
            content.each(function(){
                app.DOMdeleteItem(this, this.id);
            });
        } else if (app.deleteAction == 'page-structure-delete') {
            app.pageStructure.context.items.forEach((item) => {
                app.DOMdeleteItem(item[0], item[0].id);
            });
        } else if (app.deleteAction == 'page-structure-empty') {
            app.pageStructure.context.items.forEach((item) => {
                let content = null;
                if (app.editor.app.items[item[0].id].type != 'column') {
                    content = item.find('> .ba-section-items > .ba-row-wrapper > .ba-row');
                } else {
                    content = item.find('> .ba-item, > .ba-row-wrapper > .ba-row');
                }
                content.each(function(){
                    app.DOMdeleteItem(this, this.id);
                });
            });
        } else {
            app.DOMdeleteItem(app.editor.document.getElementById(app.editor.app.edit), app.editor.app.edit);
        }
        if (app.selector && app.cp.inPosition()) {
            app.editor.app.edit = app.selector.replace('#', '');
        }
        if (app.selector && app.cp.inPosition() && !app.editor.document.querySelector(app.selector)) {
            $g('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)').modal('hide');
        }
        app.addHistory();
    }
    for (var key in app.videoBg) {
        if (!document.getElementById(key)) {
            delete(app.videoBg[key])
        }
    }
    for (var key in app.videoSlides) {
        if (!document.getElementById(key)) {
            delete(app.videoSlides[key])
        } else {
            for (var ind in app.videoSlides[key]) {
                if (!document.getElementById(ind)) {
                    delete(app.videoSlides[key][ind]);
                }
            }
        }
    }
    $g("#delete-dialog").modal('hide');
    app.setRowWithIntro();
    app.showNotice(app._('COM_GRIDBOX_N_ITEMS_DELETED'));
});
app.modules.deleteItem = true;
app.deleteItem();