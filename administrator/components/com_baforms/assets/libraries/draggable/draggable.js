/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
	var draggable = function (element, options) {
        this.init = function(){
            var item = $(element),
                handle = item;
            if (options.handle) {
                handle = handle.find(options.handle);
            }
            handle.addClass('draggable-handle');
            handle.on('mousedown.draggable', function(event){
                if (event.target.closest('.select-modal-cp-position') || event.button != 0) {
                    return;
                }
                $('.draggable-backdrop').addClass('draggable-started');
                let coordinates = element.getBoundingClientRect(),
                    top = coordinates.top,
                    left = coordinates.left,
                    offsetX = event.clientX - left,
                    offsetY = event.clientY - top;
                item.css({
                    'position' : 'fixed',
                    'top' : top+'px',
                    'left' : left+'px',
                    'margin-left' : 0,
                    'transition' : 'none'
                });
                $(document).on('mousemove.draggable', function(event){
                    left = event.clientX - offsetX;
                    top = event.clientY - offsetY;
                    item.css({
                        'top' : top+'px',
                        'left' : left+'px'
                    });
                    return false;
                }).off('mouseup.draggable').on('mouseup.draggable', function(){
                    $('.draggable-backdrop').removeClass('draggable-started');
                    $(document).off('mousemove.draggable mouseup.draggable');
                    if (options.change) {
                        options.change(item);
                    }
                });
            });
        }
    }

    $.fn.draggable = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('draggable'),
                options = $.extend({}, {}, typeof option == 'object' && option);
            if (!data) {
                $this.data('draggable', (data = new draggable(this, options)));
            }
            data.init();
        });
    }
    
    $.fn.draggable.Constructor = draggable;
}(window.$f ? window.$f : window.jQuery);