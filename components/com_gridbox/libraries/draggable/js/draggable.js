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
                if (event.button != 0 || event.target.closest('.close-cp-modal') || event.target.closest('.select-modal-cp-position')
                    || event.target.closest('[data-dismiss="modal"]') || event.target.closest('.select-page-structure-position')) {
                    return;
                }
                event.stopPropagation();
                $('.draggable-backdrop').addClass('draggable-started');
                var coordinates = element.getBoundingClientRect(),
                    top = coordinates.top,
                    left = coordinates.left,
                    offsetX = event.clientX - left,
                    offsetY = event.clientY - top;
                item.css({
                    'position' : 'fixed',
                    'top' : top+'px',
                    'left' : left+'px',
                    'right': 'auto',
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
                return false;
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
}(window.$g ? window.$g : window.jQuery);