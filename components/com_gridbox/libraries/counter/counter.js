/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    var counter = function(element, options) {
        this.parent = $(element);
        this.options = options;
        this.interval;
        this.numbers = [];
        this.n = 100;
        this.flag = true;
    }

    counter.prototype = {
        init : function (event) {
            var cels = Math.ceil(this.options.speed / 100),
                ind = this.options.ind;
            for (var i = cels; i >= 1; i--) {
                var newNum = parseInt(this.options.number / cels * i);
                this.numbers.unshift(newNum);
            }
            this.parent.text('0');
            this.check();
            $(window).on('load scroll.counter'+ind+' touchmove.counter'+ind, $.proxy(this.check, this));
        },
        delete : function(){
            clearInterval(this.interval);
            this.interval = null;
            $(window).off('scroll.counter'+this.options.ind+' touchmove.counter'+this.options.ind);
        },
        count : function(){
            this.parent.text(this.numbers.shift());
            this.n += 100;
            if (this.n > this.options.speed) {
                this.parent.text(this.options.number);
                this.delete();
            }
        },
        check : function(){
            var wHeight = $(window).height(),
                itemTop = Math.round(this.parent.offset().top ) + 100,
                itemBottom = itemTop + (this.parent.height()),
                top = window.pageYOffset,
                bottom = (top + wHeight);
            if ((itemTop < bottom) && (itemBottom > top)){
                if (this.flag) {
                    this.interval = setInterval($.proxy(this.count, this), 100);
                    this.flag = false;
                }
            }
        }
    }

    $.fn.counter = function(option){
        return this.each(function(){
            $.fn.counter.defaults.ind += 1;
            var $this = $(this),
                data = $this.data('counter'),
                options = $.extend({}, $.fn.counter.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('counter', (data = new counter(this, options)));
            data.init();
        });
    }

    $.fn.counter.defaults = {
        speed : 1500,
        number : 596,
        ind : 0
    }

    $.fn.counter.Constructor = counter;
}(window.$g ? window.$g : window.jQuery);