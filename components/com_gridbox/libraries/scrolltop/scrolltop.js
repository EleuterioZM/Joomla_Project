/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {

	var scrolltop = function (element, options) {
        this.item = $(element);
        this.options = options;
    }

    scrolltop.prototype = {
        init : function (event) {
            var $this = this;
        	this.item.on('click', function(event){
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: 0
                }, $this.options.speed * 1, $this.options.animation);
            });
            this.check();
            $(window).on('load scroll.scrolltop'+this.options.ind+' touchmove.scrolltop'+this.options.ind, $.proxy(this.check, this));
        },
        delete : function(){
            this.item.off('click');
            $(window).off('scroll.scrolltop'+this.options.ind+' touchmove.scrolltop'+this.options.ind);
        },
        check : function(){
            var top  = $(window).scrollTop();
            if (top >= this.options.offset * 1) {
                this.item.closest('.ba-item').addClass('visible-scroll-to-top');
            } else {
                this.item.closest('.ba-item').removeClass('visible-scroll-to-top');
            }
        }
    }

	$.fn.scrolltop = function (option) {
        return this.each(function () {
            $.fn.scrolltop.defaults.ind += 1;
            var $this = $(this),
                data = $this.data('scrolltop'),
                options = $.extend({}, $.fn.scrolltop.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('scrolltop', (data = new scrolltop(this, options)));
            data.init();
        });
    }

	$.fn.scrolltop.defaults = {
        speed : 500,
        offset : 50,
        animation : 'easeInSine',
        ind : 0
    }
}(window.$g ? window.$g : window.jQuery);