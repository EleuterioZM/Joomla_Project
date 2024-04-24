/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
	var resizable = function (element, options) {
        this.init = function(){
            var item = $(element),
                handle = item.find(options.handle);
            handle.on('mousedown.resizable', function(event){
                if (event.button != 0) {
                    return;
                }
                event.stopPropagation();
                $('.draggable-backdrop').addClass('draggable-started');
                let x = event.clientX,
                    y = event.clientY,
                    direction = this.dataset.direction,
                    width = item.width(),
                    height = item.height();
                item.css({
                    'transition' : 'none'
                }).addClass('resizable-started');
                $(document).on('mousemove.resizable', function(event){
                    let deltaX = x - event.clientX,
                        deltaY = y - event.clientY,
                        css = {};
                    if (direction == 'right' || direction == 'bottom') {
                        height += Math.abs(deltaY) * (deltaY > 0 ? -1 : 1);
                        css.height = height+'px';
                    }
                    if (direction == 'right') {
                        width += Math.abs(deltaX) * (deltaX < 0 ? 1 : -1);
                        css.width = width+'px';
                    }
                    if (direction == 'left' && document.body.classList.contains('forms-cp-panel-right')) {
                        width += Math.abs(deltaX) * (deltaX < 0 ? -1 : 1);
                        document.body.style.setProperty('--modal-cp-width', width+'px');
                        css.width = width+'px';
                    }
                    item.css(css);
                    x = event.clientX;
                    y = event.clientY;
                    options.change();
                    return false;
                }).on('mouseup.resizable', function(){
                    item.removeClass('resizable-started');
                    if (options.update) {
                        options.update(item, direction);
                    }
                    $('.draggable-backdrop').removeClass('draggable-started');
                    $(document).off('mousemove.resizable mouseup.resizable');
                });
                return false;
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
}(window.$f ? window.$f : window.jQuery);