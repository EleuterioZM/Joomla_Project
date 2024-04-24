/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


!function ($) {

    var weather = function (element, options, view) {
        this.item = $(element);
        this.options = options;
        this.view = view;
    }
    
    weather.prototype = {
        init : function(){
            var $this = this;
            $.ajax({
                type:"POST",
                dataType:'text',
                data:{
                    weather: JSON.stringify($this.options),
                    view: themeData.page.view
                },
                url:JUri+"index.php?option=com_gridbox&task=editor.renderWeather",
                success: function(msg){
                    if (msg) {
                        $this.item.html(msg);
                    }
                    if (themeData.page.view != 'gridbox' && !$this.view.wind) {
                        $this.item.find('.weather-info .wind').remove();
                    }
                    if (themeData.page.view != 'gridbox' && !$this.view.humidity) {
                        $this.item.find('.weather-info .humidity').remove();
                    }
                    if (themeData.page.view != 'gridbox' && !$this.view.pressure) {
                        $this.item.find('.weather-info .pressure').remove();
                    }
                    if (themeData.page.view != 'gridbox' && (!$this.view.pressure && !$this.view.humidity
                            && !$this.view.wind)) {
                        $this.item.find('.weather-info').remove();
                    }
                    if (themeData.page.view != 'gridbox') {
                        $this.item.find('.forecast').each(function(i){
                            if (i >= $this.view.forecast) {
                                this.remove();
                            }
                        })
                    }
                }
            });
        }
    }
    
    $.fn.weather = function(option, view) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('weather'),
                options = $.extend({}, $.fn.weather.defaults, typeof option == 'object' && option);
            if (data) {
                $this.removeData();
            }
            $this.data('weather', (data = new weather(this, options, view)));
            data.init();
        });
    }
    
    $.fn.weather.defaults = {
        location : 'New York, NY, United States',
        unit : 'c'
    }
    $.fn.weather.Constructor = weather;
}(window.$g ? window.$g : window.jQuery);