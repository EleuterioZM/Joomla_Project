/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function($){
    var parallax = function(element, options){
        this.parent = $(element);
        this.id = element.id
        this.options = options;
        this.ix = this.iy = this.mx = this.my = this.vx = this.vy = 0;
        this.setPosition = this.setPosition.bind(this);
        this.animate = this.animate.bind(this);
    }

    parallax.prototype = {
        delete : function(){
            this.parent.closest('.ba-wrapper').off('mousemove.parallax'+this.id);
            $(window).off("scroll.parallax"+this.id);
            this.parent.find('> .parallax-wrapper').remove();
            cancelAnimationFrame(this.requestId);
        },
        animate : function(){
            this.mx = this.ix * 20;
            this.my = this.iy * 20;
            this.vx += (this.mx - this.vx) * 0.3;
            this.vy += (this.my - this.vy) * 0.3;
            var xOffset = this.vx * this.options.offset * (this.options.invert ? -1 : 1),
                yOffset = this.vy * this.options.offset * (this.options.invert ? -1 : 1);
            this.setPosition(xOffset, yOffset);
            if (this.newX != xOffset && this.newY != yOffset) {
                this.requestId = requestAnimationFrame(this.animate);
                this.newX = xOffset;
                this.newY = yOffset;
            } else {
                cancelAnimationFrame(this.requestId);
            }
        },
        onMouseMove : function(clientX, clientY) {
            var wcx = window.innerWidth,
                wcy = window.innerHeight;
            this.ix = ((clientX / 2) / (wcx / 2)) - 0.5;
            this.iy = ((clientY / 2) / (wcy / 2)) - 0.5;
            cancelAnimationFrame(this.requestId);
            this.requestId = requestAnimationFrame(this.animate);
        },
        setPosition: function(xOffset, yOffset){
            if (xOffset > 8.3) {
                xOffset = 8.3;
            } else if (xOffset < -8.3) {
                xOffset = -8.3;
            }
            if (yOffset > 8.3) {
                yOffset = 8.3;
            } else if (yOffset < -8.3) {
                yOffset = -8.3;
            }
            this.parallax.css({
                "-webkit-transform": "translate("+xOffset+"%, "+yOffset+"%)",
                "transform": "translate("+xOffset+"%, "+yOffset+"%)"
            });
        },
        init : function(){
            this.parent.find("> .parallax-wrapper").remove();
            if (this.options.enable) {
                var $this = this,
                    str = '<div class="parallax-wrapper '+this.options.type+'"><div class="parallax"></div></div>';
                this.parent.find(' > .ba-overlay').after(str);
                this.parallax = this.parent.find("> .parallax-wrapper .parallax");
                if (this.options.type == 'mousemove') {
                    this.parent.closest('.ba-wrapper').on('mousemove.parallax'+this.id, function(event){
                        $this.onMouseMove(event.clientX, event.clientY);
                    });
                } else {
                    $(window).on("scroll.parallax"+this.id, function(event){
                        var top = window.pageYOffset,
                            windowHeight = $(window).height(),
                            bottom = top + windowHeight,
                            elemTop = Math.round($this.parent.offset().top),
                            elemBottom = elemTop + $this.parent[0].offsetHeight;
                        if (elemTop < bottom && elemBottom >= top) {
                            var y = (windowHeight / 2 - (Math.abs(bottom - elemTop) - $this.parent.innerHeight() / 2));
                            y *= ($this.options.invert ? -$this.options.offset : $this.options.offset);
                            $this.parallax.css({
                                "-webkit-transform": "translate(0%, "+y+"px)",
                                "transform": "translate(0%, "+y+"px)"
                            });
                        }
                    });
                }
            }
        }
    }

    $.fn.parallax = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('parallax'),
                options = $.extend({}, $.fn.parallax.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('parallax', (data = new parallax(this, options)));
            data.init();
        });
    }
    
    $.fn.parallax.defaults = {
        "enable" : true,
        "offset" : 0.5,
        "type" : "mousemove",
        "invert" : false
    }
}(window.$g ? window.$g : window.jQuery);