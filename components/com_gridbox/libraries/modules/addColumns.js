/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addColumns = function(count){
    let layout = 'section',
        method = 'append',
        array = [];
        target = $g('#ba-edit-section');
    if (top.$g('#add-section-dialog').hasClass('add-columns')) {
        layout = 'row';
        target = $g('#'+app.edit+'> .ba-section-items');
        if ($g('#'+app.edit).hasClass('ba-grid-column') || $g('#'+app.edit).hasClass('ba-row')) {
            target = $g('#'+app.edit);
        }
    }
    array.push(target);
    if (top.app.pageStructure && (top.app.pageStructure.context.task == 'section' || top.app.pageStructure.context.task == 'row')) {
        array = top.app.pageStructure.context.items;
        method = 'after';
    } else if (top.app.pageStructure && top.app.pageStructure.context.task == 'columns') {
        array = top.app.pageStructure.context.items;
        method = 'append';
    }
    app.columnLoader.set(layout, count, array, method);
}

app.columnLoader = {
    after: () => {
        if (top.app.pageStructure) {
            top.app.pageStructure.context.task = null;
        }
        if (top.app.pageStructure && top.app.pageStructure.visible) {
            top.app.pageStructure.updateStructure(true);
        }
        app.setMarginBox();
        window.parent.app.addHistory();
    },
    set: (layout, count, array, method) => {
        let target = array.pop();
        if (top.app.pageStructure && top.app.pageStructure.context.task == 'section') {
            target = target.closest('.ba-wrapper');
        } else if (top.app.pageStructure && top.app.pageStructure.context.task == 'row' && target.hasClass('ba-section')) {
            method = 'append';
            target = target.find('> .ba-section-items');
        } else if (top.app.pageStructure && top.app.pageStructure.context.task == 'row' && target.hasClass('ba-row')) {
            target = target.closest('.ba-row-wrapper');
        }
        app.columnLoader.load(layout, count, target, method).then(() => {
            if (array.length != 0) {
                app.columnLoader.set(layout, count, array, method);
            } else {
                app.columnLoader.after();
            }
        });
    },
    load: (layout, count, target, method) => {
        return new Promise((resolve, reject) => {
            app.fetch(JUri+'index.php?option=com_gridbox&task=editor.loadLayout', {
                layout: layout,
                count: count,
                time: Date.now()
            }).then((text) => {
                let msg = JSON.parse(text),
                    id = '',
                    wrapper = target.closest('.ba-wrapper');
                for (let key in msg.items) {
                    let type = msg.items[key].type;
                    if (app.theme.defaultPresets[type] && app.theme.presets[type] && app.theme.presets[type][app.theme.defaultPresets[type]]) {
                        msg.items[key] = $g.extend(true, msg.items[key], app.theme.presets[type][app.theme.defaultPresets[type]].data);
                    }
                    app.items[key] = msg.items[key];
                    if (msg.items[key].type == layout) {
                        id = key;
                    }
                }
                if (target.hasClass('ba-grid-column')) {
                    target.find('> .empty-item').before(msg.html);
                    target.find('> .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings .ba-tooltip').text(top.app._('NESTED_ROW'));
                } else {
                    target[method](msg.html);
                }
                for (let key in msg.items) {
                    document.getElementById(key).classList.add('visible');
                    window.parent.setShapeDividers(app.items[key], key);
                }
                app.checkModule('sectionRules');
                let str = '';
                top.$g('#add-section-dialog').modal('hide');
                editItem(id);
                if (target.hasClass('ba-section-items') && (wrapper.hasClass('ba-overlay-section')
                    || wrapper.hasClass('ba-lightbox'))) {
                    makeRowSortable($g('#'+id).find('.ba-section-items'), 'lightbox-row');
                    makeColumnSortable($g('#'+id).find('.ba-grid-column'), 'lightbox-column');
                } else if (target.hasClass('ba-section-items') && !wrapper.hasClass('tabs-content-wrapper')) {
                    makeRowSortable($g('#'+id).find('.ba-section-items'), 'tabs-row');
                    makeColumnSortable($g('#'+id).find('.ba-grid-column'), 'column');
                } else if (wrapper.attr('data-megamenu')) {
                    makeRowSortable($g('#'+id).find('.ba-section-items'), 'row');
                    makeColumnSortable($g('#'+id).find('.ba-grid-column'), 'lightbox-column');
                } else if (target.hasClass('ba-row') && (wrapper.hasClass('ba-overlay-section')
                    || wrapper.hasClass('ba-lightbox') || wrapper.hasClass('ba-sticky-header'))) {
                    makeRowSortable($g('#'+id).find('.ba-section-items'), 'lightbox-row');
                    makeColumnSortable($g('#'+id).find('.ba-grid-column'), 'lightbox-column');
                } else {
                    makeRowSortable($g('#'+id).find('.ba-section-items'), 'row');
                    makeColumnSortable($g('#'+id).find('.ba-grid-column'), 'column');
                }
                setColumnResizer($g('#'+id)[0]);
                if (target.hasClass('ba-row')) {
                    let div = target.find('> .ba-row-wrapper > .ba-row');
                    target.append(div.find('.column-wrapper'));
                    delete(app.items[div.attr('id')]);
                    target.append(div.find('.column-wrapper'));
                    let rowColumns = target.find('> .column-wrapper').first().find('> .ba-grid-column-wrapper > .ba-grid-column'),
                        newRowColumns = target.find('> .column-wrapper').last().find('> .ba-grid-column-wrapper > .ba-grid-column');
                    rowColumns.each(function(ind){
                        if (newRowColumns[ind]) {
                            delete(app.items[newRowColumns[ind].id]);
                            let columnWrapper = this.closest('.ba-grid-column-wrapper'),
                                className = columnWrapper.className,
                                span = columnWrapper.dataset.span,
                                $this = newRowColumns[ind].closest('.ba-grid-column-wrapper');
                            $this.className = className;
                            $this.classList.remove('ba-col-'+span);
                            $this.classList.add('ba-col-'+$this.dataset.span);
                            $g(newRowColumns[ind]).replaceWith(this);
                            newRowColumns[ind] = this;
                        } else {
                            let column = newRowColumns[newRowColumns.length - 1];
                            $g(column).find('> .empty-item').before($g(this).find('> .ba-item, > .ba-row-wrapper'));
                            delete(app.items[this.id]);
                        }
                    });
                    target.find('.ba-item').each(function(){
                        if (app.items[this.id]) {
                            initMapTypes(app.items[this.id].type, this.id);
                        }
                    });
                    target.find('> .column-wrapper').first().remove();
                    div.closest('.ba-row-wrapper').remove();
                }
                setTimeout(() => {
                    resolve();
                }, 10);
            });
        });
    }
}
app.addColumns(app.modules.addColumns.data);