/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function duplicateObject(obj, id)
{
    let str = JSON.stringify(obj);

    return JSON.parse(str);
}

function checkSlideshow($this, id)
{
    if (app.items[$this.id].type == 'slideshow') {
        $g($this).find('.ba-slideshow-img').each(function(){
            this.firstElementChild.id = id;
            id++
        });
    }

    return id;
}

function checkAccordions($this, id)
{
    if (app.items[$this.id].type == 'accordion') {
        var accordion = $g($this).find('> .accordion'),
            parent = 'accordion-'+id;
        accordion[0].id = parent;
        id++;
        accordion.find('> .accordion-group > .accordion-heading a').each(function(){
            var old = this.hash;
            this.dataset.parent = '#'+parent;
            this.href = '#collapse-'+id;
            $g($this).find(old)[0].id = 'collapse-'+id;
            id++;
        });
    }

    return id;
}

function checkTabs($this, id)
{
    if (app.items[$this.id].type == 'tabs') {
        $g($this).find('> .ba-tabs-wrapper > ul.nav.nav-tabs a').each(function(){
            let old = this.hash;
            this.href = '#tab-'+id;
            $g($this).find(old)[0].id = 'tab-'+id;
            id++;
        });
    }

    return id;
}

function copyItem(child, items, data, id)
{
    child[0].id = 'item-'+id;
    id++;
    if (child.hasClass('ba-item')) {
        items.push(child[0].id);
        id = checkSlideshow(child[0], id);
        id = checkTabs(child[0], id);
        id = checkAccordions(child[0], id);
    }
    if (app.items[child[0].id] && app.items[child[0].id].type == 'overlay-button') {
        if (!child[0].querySelector('.ba-overlay-section-backdrop')) {
            var overlay =  document.querySelector('.ba-overlay-section-backdrop[data-id="'+child[0].dataset.overlay+'"]');
            if (overlay) {
                overlay = overlay.cloneNode(true);
                child[0].appendChild(overlay);
            }
        }
    }
    $g('.ba-overlay-section-backdrop').each(function(){
        var button = child[0].querySelector('.ba-item-overlay-section[data-overlay="'+this.dataset.id+'"]');
        if (button) {
            button.appendChild(this.cloneNode(true));
        }
    });
    child.find('.ba-item').each(function(){
        var ind = this.id;
        this.id = 'item-'+(id++);
        if (data[ind]) {
            app.items[this.id] = duplicateObject(data[ind], ind);
            items.push(this.id);
            id = checkTabs(this, id);
            id = checkAccordions(this, id);
            id = checkSlideshow(this, id);
        }
    });
    child.find('.ba-row, .ba-grid-column, .ba-section').each(function(){
        let ind = this.id;
        if (data[ind]) {
            app.items['item-'+id] = duplicateObject(data[ind], ind);
            if (app.items['item-'+id].type == 'overlay-section') {
                var overlay = child[0].querySelector('.ba-overlay-section-backdrop[data-id="'+this.id+'"]');
                overlay.dataset.id = 'item-'+id;
                overlay.parentNode.dataset.overlay = 'item-'+id;
            } else if (app.items['item-'+id].type == 'mega-menu-section') {
                $g(this).closest('.tabs-content-wrapper').attr('data-id', 'item-'+id);
            }
        }
        this.id = 'item-'+(id++);
    });
    child.find('.star-ratings-wrapper').each(function(){
        let ratings = $g(this)
        ratings.find('i').addClass('active');
        ratings.find('.rating-value').text('0.00');
        ratings.find('.votes-count').text('0');
        ratings.find('.info-wrapper').attr('id', 'star-ratings-'+ratings.closest('.ba-item-star-ratings').attr('id'))
    });
    child.find('.ba-field-group-wrapper').each(function(){
        let key = this.closest('.ba-item-field-group').id,
            object = app.items[key].items;
        $g(this).find('> .ba-field-wrapper').each(function(ind){
            this.dataset.id = 'item-'+id;
            object[ind].field_key = 'item-'+(id++);
        });
    });

    return id;
}

app.copyItemsContent = function(item, style, key, id){
    let array = [];
    if (app.copyAction == 'context') {
        array = top.app.pageStructure.context.items;
    } else {
        array.push(item);
    }
    array.forEach((target) => {
        let items = [],
            itemId = 'item-'+id,
            clone, child;
        if (app.copyAction == 'copyTabPane') {
            item = item.parent();
            target = target.parent();
        }
        if (style[key] && (item.hasClass('ba-section') || item.hasClass('ba-row'))) {
            item = item.parent();
        }
        if (style[key] && (style[key].type == 'section' || style[key].type == 'row') && !target.hasClass('ba-grid-column')) {
            target = target.parent();
        }
        if (style[key]) {
            app.items['item-'+id] = duplicateObject(style[key]);
        }
        if (app.items[key] && app.items[key].positioning && app.items[key].positioning.position) {
            top.app.positioning.item = item[0];
            let obj = app.items[key],
                rect = top.app.positioning.getRect(obj);
            app.items['item-'+id].desktop.positioning.y += rect.height;
        }
        clone = item.clone();
        clone.removeClass('active-context-item-editing page-structure-item-active').find('.active-context-item-editing, .page-structure-item-active')
            .removeClass('active-context-item-editing page-structure-item-active');
        clone.removeAttr('data-global');
        clone.find('[data-global]').removeAttr('data-global');
        if (app.copyAction == 'copyTabPane' || (style[key] && (style[key].type == 'section' || style[key].type == 'row'))) {
            child = clone.find('#'+key);
        } else {
            child = clone;
        }
        id = copyItem(child, items, style, id);
        if (app.copyAction == 'context' && (top.app.buffer.store == 'item' || top.app.buffer.store == 'cut-item') && top.app.context &&
            (top.app.context.itemType == 'menu' || top.app.context.itemType == 'one-page') &&
            target.find('> .ba-menu-wrapper > .main-menu').hasClass('visible-menu')) {
            target.find('> .ba-menu-wrapper > .main-menu > .integration-wrapper').after(clone);
        } else if (app.copyAction == 'context' && (top.app.buffer.store == 'item' || top.app.buffer.store == 'cut-item')
            && !target.hasClass('ba-grid-column')) {
            target.after(clone);
        } else if (app.copyAction == 'context' && target.hasClass('ba-wrapper')) {
            target.find('> .ba-section > .ba-section-items').append(clone);
        } else if (app.copyAction == 'context' && target.hasClass('ba-grid-column')) {
            target.find('> .empty-item').before(clone);
        } else {
            target.after(clone);
        }
        editItem(itemId);
        for (var i = 0; i < items.length; i++) {
            var obj = {
                data : app.items[items[i]],
                selector : items[i]
            };
            itemsInit.push(obj);
        }
        clone.columnResizer({
            change : function(right, left){
                right.find('.ba-item').each(function(){
                    if (app.items[this.id]) {
                        initMapTypes(app.items[this.id].type, this.id);
                    }
                });
                left.find('.ba-item').each(function(){
                    if (app.items[this.id]) {
                        initMapTypes(app.items[this.id].type, this.id);
                    }
                });
            }
        });
        let wrapper = clone.closest('.ba-wrapper'),
            rowSort = $g('header.header, footer.footer, #ba-edit-section').find(clone)
                .find('.tabs-content-wrapper .ba-section-items');
        makeRowSortable(rowSort, 'tabs-row');
        if (app.copyAction == 'copyContentSlide') {
            makeColumnSortable($g(clone), 'column');
            makeColumnSortable($g(clone).find('.ba-grid-column'), 'column');
        } else if (wrapper.hasClass('ba-lightbox') || wrapper.hasClass('ba-overlay-section')) {
            makeColumnSortable(clone.find('.ba-grid-column'), 'lightbox-column');
            makeRowSortable(clone.find(' > .ba-section > .ba-section-items'), 'lightbox-row');
        } else if (wrapper.attr('data-menu')) {
            makeColumnSortable($g(clone).find('.ba-grid-column'), 'lightbox-column');
            makeRowSortable($g(clone).find(' > .ba-section > .ba-section-items'), 'row');
        } else if (wrapper.hasClass('tabs-content-wrapper')) {
            makeColumnSortable($g(clone).find('.ba-grid-column'), 'column');
            makeRowSortable($g(clone).find(' > .ba-section > .ba-section-items'), 'tabs-row');
        } else {
            makeColumnSortable($g(clone).find('.ba-grid-column'), 'column');
            makeRowSortable($g(clone).find(' > .ba-section > .ba-section-items'), 'row');
        }
    });

    return ++id;
}

app.copyItem = function(){
    let id = +new Date() * 10;
    if (app.copyAction == 'context' && (top.app.buffer.store == 'item' || top.app.buffer.store == 'cut-item')) {
        let div = document.createElement('div'),
            array = [];
        div.innerHTML = top.app.buffer.data.html;
        $g(div).find('> .ba-item, > .ba-wrapper > .ba-section, > .ba-row-wrapper > .ba-row').each(function(){
            array.unshift(this);
        });
        array.forEach(($this) => {
            id = app.copyItemsContent($g($this), top.app.buffer.data.items, $this.id, id);
        });
    } else if (app.copyAction == 'context') {
        let div = document.createElement('div'),
            array = [];
        div.innerHTML = top.app.buffer.data.html;
        $g(div).find('> .ba-section-items > .ba-row-wrapper > .ba-row, > .ba-item, > .ba-row-wrapper > .ba-row').each(function(){
            id = app.copyItemsContent($g(this), top.app.buffer.data.items, this.id, id);
        });
    } else if (app.copyAction == 'copyTabPane') {
        top.app.itemDelete.forEach(function(ind){
            var obj = top.sortingList[ind],
                tab = $g(obj.href+' .ba-wrapper > .ba-section').first();
            id = app.copyItemsContent(tab, app.items, tab.attr('id'), id);
        });
        top.copyTabPane();
    } else if (app.copyAction == 'copyContentSlide') {
        top.app.itemDelete.forEach(function(ind){
            var obj = top.sortingList[ind],
                column = $g(obj.parent).find('> .ba-grid-column');
            id = app.copyItemsContent(column, app.items, column.attr('id'), id);
        });
        top.copyContentSlide();
    } else if (app.edit) {
        app.copyItemsContent($g('#'+app.edit), app.items, app.edit, id);
    }
    app.edit = null;
    app.checkModule('sectionRules');
    if (itemsInit.length > 0) {
        app.checkModule('initItems', itemsInit.pop());
    }
    app.buttonsPrevent();
    app.checkModule('checkOverlay');
    app.checkVideoBackground();
    app.checkModule('loadParallax');
    app.checkAnimation();
    app.positioning.init();
    if (app.copyAction != 'blogPostsText') {
        top.app.addHistory();
        top.app.showNotice(top.gridboxLanguage['GRIDBOX_DUPLICATED']);
    }
    if (app.copyAction == 'copyContentSlide' || app.copyAction == 'copyTabPane') {
        app.edit = top.app.editItemId;
    }
    app.copyAction = null;
    if (top.app.pageStructure && top.app.pageStructure.visible) {
        top.app.pageStructure.updateStructure(true);
    }
    if (top.app.selector && top.app.cp.inPosition()) {
        app.edit = top.app.selector.replace('#', '');
    }
}

app.copyItem();