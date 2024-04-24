/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

window.sortableGroups = {};

function sortable(element, settings){
    this.options = $g.extend({}, this.defaults, typeof settings == 'object' && settings);
    if (element.sortable) {
        this.delete(element);
    }
    this.element = element;
    this.element.sortable = this;
    this.init();
}

sortable.prototype.defaults = {
    selector: '> *',
    change: function(el){
        $g(el).trigger('change');
    },
    start: function(){}
}

sortable.prototype.delete = function(element){
    $g(element).off('mousedown.sortable');
    element.sortable = null;
}

sortable.prototype.init = function(){
    var element = this.element,
        options = this.options,
        dragEl = cloneEl = placeEl = null,
        item = $g(element),
        div = document.createElement('div');
    if (!sortableGroups[options.group]) {
        sortableGroups[options.group] = [];
    }
    sortableGroups[options.group].unshift(item);
    item.on('mousedown.sortable', options.handle, function(event){
        if (event.button != 0  || (options.group == 'pages' && !event.target.classList.contains('sortable-handle') 
            && (event.target.localName == 'a' || event.target.localName == 'i'))) {
            return false;
        }
        $g(item).closest('.ba-wrapper').addClass('sortable-parent-node');
        options.start(item[0]);
        dragEl = $g(this).closest(element.children)[0];
        cloneEl = dragEl.cloneNode(true);
        $g(cloneEl).find('.ba-edit-item').parent().find('> *').not('.ba-edit-item').remove();
        placeEl = cloneEl.cloneNode(true);
        placeEl.classList.add('sortable-placeholder');
        cloneEl.classList.add('sortable-helper');
        element.insertBefore(cloneEl, dragEl);
        element.insertBefore(placeEl, cloneEl);
        $g(cloneEl).css({
            'width' : $g(dragEl).width()+'px',
            'position' : 'fixed',
            'top' : event.clientY+'px',
            'left' : event.clientX+'px',
            'margin-left' : 0,
            'transition' : 'none'
        }).on('mouseover', function(event){
            event.stopPropagation();
        })
        $g(dragEl).find('.edit-settings').trigger('mouseleave');
        div.appendChild(dragEl)
        item.removeClass('active-item');
        $g(document).on('mousemove.sortable', function(event){
            $g(cloneEl).css({
                'top' : event.clientY+'px',
                'left' : event.clientX+'px',
            });
            var target = null,
                array = sortableGroups[options.group];
            for (var i = 0; i < array.length; i++) {
                array[i].find(options.selector).not(placeEl).not(cloneEl).each(function(){
                    var rect = this.getBoundingClientRect();
                    if (rect.top < event.clientY && rect.bottom > event.clientY &&
                        rect.left < event.clientX && event.clientX < rect.right) {
                        target = this;
                        return false;
                    }
                });
                if (target) {
                    var rect = target.getBoundingClientRect(),
                        next = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5,
                        after = next && target.nextSibling || target;
                    if (next && !target.nextSibling) {
                        after.parentNode.appendChild(placeEl);
                    } else {
                        after.parentNode.insertBefore(placeEl, after);
                    }
                } else {
                    var rect = array[i][0].getBoundingClientRect(),
                        length = $g(array[i][0]).find(options.selector).not(placeEl).not(cloneEl).length;
                    if (rect.top < event.clientY && rect.bottom > event.clientY &&
                        rect.left < event.clientX && event.clientX < rect.right && length == 0) {
                        target = array[i][0];
                    }
                    if (target && !target.classList.contains('ba-grid-column')) {
                        target.appendChild(placeEl);
                    } else if (target) {
                        $g(target).find('> .empty-item').before(placeEl);
                    }
                }
                if (target) {
                    break;
                }
            }
            return false;
        }).off('mouseup.sortable').on('mouseup.sortable', function(){
            var classList = cloneEl.classList;
            cloneEl.parentNode.removeChild(cloneEl);
            placeEl.parentNode.insertBefore(dragEl, placeEl);
            placeEl.parentNode.removeChild(placeEl);
            $g(document).off('mousemove.sortable mouseup.sortable');
            $g('.sortable-parent-node').removeClass('sortable-parent-node');
            options.change(dragEl);
        });
        return false;
    });
}