/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {

    var smoothScroll = function (element, options) {
        this.item = $(element);
        this.options = options;
    }

    smoothScroll.prototype = {
        init : function (event) {
            var $this = this;
            this.item.on('click', function(event){
                event.preventDefault();
                if ($this.options.target) {
                    var position = $('#'+$this.options.target),
                        wrapper = $g(this).closest('.ba-wrapper'),
                        header = wrapper.hasClass('ba-sticky-header') ? wrapper : $('header.header'),
                        comp = header[0] ? getComputedStyle(header[0]) : {};
                    if (position.length > 0) {
                        position = position.offset().top;
                        if ((!header.hasClass('sidebar-menu') || (app.view != 'desktop' && app.view != 'laptop'))
                            && comp.position == 'fixed') {
                            position -= header.height();
                        }
                        $('html, body').stop().animate({
                            scrollTop: position
                        }, $this.options.speed * 1, $this.options.animation);
                    }
                }
            });
        },
        delete : function(){
            this.item.off('click');
        }
    }

    $.fn.smoothScroll = function (option) {
        return this.each(function () {
            $.fn.smoothScroll.defaults.ind += 1;
            var $this = $(this),
                data = $this.data('smoothScroll'),
                options = $.extend({}, $.fn.smoothScroll.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('smoothScroll', (data = new smoothScroll(this, options)));
            data.init();
        });
    }

    $.fn.smoothScroll.defaults = {
        speed : 500,
        'target' : '',
        animation : 'easeInSine'
    }
}(window.$g ? window.$g : window.jQuery);