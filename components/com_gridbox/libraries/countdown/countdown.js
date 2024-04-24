/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    var countdown = function(element, options) {
        this.item = $(element);
        this.interval;
        this.options = options;
        
    }
    
    countdown.prototype = {
        init : function() {
            let $this = this;
            app.fetch(JUri+'index.php?option=com_gridbox&task=editor.getCountdownTimes', {
                end: this.options.end
            }).then((text) => {
                let obj = JSON.parse(text);
                $this.start = obj.start;
                $this.end = obj.end;
                $this.calculate();
            });
        },
        calculate: function(){
            let time = this.end - this.start;
            this.item.find('.ba-countdown > *').removeAttr('style');
            switch (this.options.mode) {
                case 'minutes' :
                    this.item.find('.hours').hide();
                case 'hours' :
                    this.item.find('.days').hide();
                    break;
            }
            if (time > 0) {
                this.item.find('.ba-countdown').css('display', '');
                let days = Math.floor(time / 86400),
                    hours = Math.floor((time - (days * 86400)) / 3600),
                    minutes = Math.floor((time - (days * 86400) - (hours * 3600)) / 60),
                    seconds = Math.floor((time - (days * 86400) - (hours * 3600) - (minutes * 60)));
                if (this.options.mode == 'hours') {
                    hours = Math.floor(time / 3600);
                } else if (this.options.mode == 'minutes') {
                    minutes = Math.floor(time / 60);
                }
                if (hours < "10") {
                    hours = "0" + hours;
                }
                if (minutes < "10") {
                    minutes = "0" + minutes;
                }
                if (seconds < "10") {
                    seconds = "0" + seconds;
                }
                this.item.find('.days .countdown-time').text(days);
                this.item.find('.hours .countdown-time').text(hours);
                this.item.find('.minutes .countdown-time').text(minutes);
                this.item.find('.seconds .countdown-time').text(seconds);
                this.interval = setTimeout(function(){
                    this.end--;
                    this.calculate();
                }.bind(this), 1000);
            } else {
                clearTimeout(this.interval);
                this.item.find('.days .countdown-time').text('0');
                this.item.find('.hours .countdown-time').text('00');
                this.item.find('.minutes .countdown-time').text('00');
                this.item.find('.seconds .countdown-time').text('00');
                this.options.callback();
            }
        },
        delete : function(){
            clearInterval(this.interval);
            this.interval = null;
        }
    }
    
    $.fn.countdown = function(option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('countdown'),
                options = $.extend({}, $.fn.countdown.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('countdown', (data = new countdown(this, options)));
            data.init();
        });
    }

    $.fn.countdown.defaults = {
        end : new Date(),
        mode : 'full',
        callback : function(){

        }
    }
    
    $.fn.countdown.Constructor = countdown;
}(window.$g ? window.$g : window.jQuery);