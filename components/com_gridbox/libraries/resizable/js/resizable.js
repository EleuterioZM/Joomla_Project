/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
	var resizable = function (element, options) {
        this.init = function(){
            let item = $(element),
                handle = item.find(options.handle);
            handle.on('mousedown.resizable', function(event){
                if (event.button == 0) {
                    event.stopPropagation();
                    $('.draggable-backdrop').addClass('draggable-started');
                    item.addClass('resizable-started');
                    let direction = this.dataset.direction || options.direction,
                        x = event.clientX,
                        y = event.clientY,
                        rect = item[0].getBoundingClientRect(),
                        width = rect.width,
                        height = rect.height;
                    item.css({
                        'transition': 'none'
                    });
                    if (direction == 'left') {
                        item.css({
                            right: window.innerWidth - rect.right,
                            left: 'auto'
                        })
                    }
                    $(document).on('mousemove.resizable', function(event){
                        let deltaX = x - event.clientX,
                            deltaY = y - event.clientY;
                        if (direction == 'right' || direction == 'right-bottom') {
                            width = width - deltaX;
                        } else if (direction == 'left') {
                            width = width + deltaX;
                        }
                        if (direction == 'bottom' || direction == 'right-bottom') {
                            height = height - deltaY;
                        }
                        item.css({
                            width : width+'px',
                            height : height+'px'
                        });
                        x = event.clientX;
                        y = event.clientY;
                        return false;
                    }).on('mouseup.resizable', function(){
                        $('.draggable-backdrop').removeClass('draggable-started');
                        item.removeClass('resizable-started');
                        $(document).off('mousemove.resizable mouseup.resizable');
                        if (direction == 'left') {
                            rect = item[0].getBoundingClientRect();
                            item.css({
                                right: 'auto',
                                left: rect.left
                            })
                        }
                        options.change(direction, item);
                    });
                    return false;
                }
            });
        }
    }

    $.fn.resizable = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('resizable'),
                options = $.extend({}, $.fn.resizable.defaults, typeof option == 'object' && option);
            if (!data) {
                $this.data('resizable', (data = new resizable(this, options)));
            }
            data.init();
        });
    }

    $.fn.resizable.defaults = {
        direction : 'right',
        change : function(){
            
        }
    }
    
    $.fn.resizable.Constructor = resizable;
}(window.$g ? window.$g : window.jQuery);