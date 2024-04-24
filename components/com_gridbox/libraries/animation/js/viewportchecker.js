/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

(function($){
    let viewportChecker = function(element, options){
        this.element = element;
        this.parent = $(element);
        this.options = options;
        this.canRepeat = false;
        this.flag = true;
    }

    viewportChecker.prototype = {
        set: function(){
            $(window).on("scroll.viewportChecker"+this.element.id, this.check.bind(this));
            $(window).on("touchmove.viewportChecker"+this.element.id, this.check.bind(this));
            this.check();
        },
        delete: function(){
            $(window).off("scroll.viewportChecker"+this.element.id);
            $(window).off("touchmove.viewportChecker"+this.element.id);
            clearTimeout(this.delay);
            this.clear();
        },
        clear: function(){
            this.parent.removeClass(this.options.effect);
            this.parent.removeClass('visible animated');
            this.canRepeat = false;
            this.flag = true;
        },
        check: function(){
            let top = window.pageYOffset,
                bottom = top + window.innerHeight,
                elemTop = Math.round(this.parent.offset().top),
                elemBottom = elemTop + this.element.offsetHeight,
                overlay = this.element.closest('.ba-overlay-section-backdrop');
            if (this.flag && this.element.closest('.ba-item-content-slider')) {
                this.parent.addClass('visible animated '+this.options.effect);
                this.flag = false;
            } else if (this.flag && ((!overlay && elemTop < bottom && elemBottom > top) || (overlay && overlay.classList.contains('visible-section')))) {
                this.parent.addClass('visible animated '+this.options.effect);
                this.flag = false;
                this.delay = setTimeout(function(){
                    this.parent.removeClass(this.options.effect);
                    this.canRepeat = true;
                }.bind(this), this.options.delay * 1000 + this.options.duration * 1000);
            } else if (this.options.repeat && this.canRepeat && !(elemTop < bottom && elemBottom > top)) {
                this.clear();
            }
        }
    }

    $.fn.viewportChecker = function(option) {
        return this.each(function(){
            let $this = $(this),
                data = $this.data('viewportChecker'),
                options = $.extend({}, option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            if (options.effect) {
                $this.data('viewportChecker', (data = new viewportChecker(this, options)));
                data.set();
            }
        });
    }
})(window.$g ? window.$g : window.jQuery);