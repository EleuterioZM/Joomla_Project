/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function($){
    var motion = function(element, options){
        this.element = element;
        this.parent = $(element);
        this.options = options;
        this.observers = {};
    }

    motion.prototype = {
        resize: function(){
            this.delete();
            this.set();
        },
        set: function(){
            this.clear();
            this.motions = $g.extend(true, {}, this.options[app.view]);
            $(window).on("resize.motion-"+this.element.id, this.resize.bind(this));
            for (let key in this.motions) {
                let obj = this.motions[key];
                if (!obj.enable) {
                    continue;
                }
                if (key == 'translateY') {
                    obj.property.end = window.innerHeight / 2;
                } else if (key == 'translateX') {
                    let rect = this.element.getBoundingClientRect();
                    obj.property.end = window.innerWidth / 2;
                    obj.property.start = obj.direction == -2 || obj.direction == 2 ? obj.property.end : obj.property.start;
                    obj.property.end = obj.direction == -2 || obj.direction == 2 ? 0 : obj.property.end;
                    obj.direction = obj.direction == -2 || obj.direction == 2 ? obj.direction / 2 : obj.direction;
                } else if (key == 'opacity') {
                    obj.property.end /= 100;
                    obj.property.start = obj.direction == -1 ? obj.property.start : obj.property.end;
                    obj.property.end = obj.direction == -1 ? obj.property.end : 0;
                } else if (key == 'blur') {
                    obj.property.start = obj.direction == 1 ? obj.property.start : obj.property.end;
                    obj.property.end = obj.direction == 1 ? obj.property.end : 0;
                } else if (key == 'scale') {
                    obj.property.start = obj.direction == 1 ? obj.property.start : obj.property.end;
                    obj.property.end = obj.direction == 1 ? obj.property.end : 1;
                }
                let observer = new IntersectionObserver(function(entries){
                    if (key == 'translateX' && obj.speed > 1 && themeData.page.view == 'gridbox') {
                        $(window).off('scroll.motion-'+key+'-'+this.element.id)
                            .on('scroll.motion-'+key+'-'+this.element.id, this.animate.bind(this, obj, key));
                    } else if (entries[0].isIntersecting) {
                        $(window).on('scroll.motion-'+key+'-'+this.element.id, this.animate.bind(this, obj, key));
                    } else {
                        $(window).off('scroll.motion-'+key+'-'+this.element.id);
                    }
                }.bind(this), {
                    rootMargin: obj.viewport.end - 100 + "% 10000px " + (0 - obj.viewport.start) + "% 10000px"
                });
                observer.observe(this.element);
                this.observers[key] = observer;
            }
            this.check();
        },
        check: function(){
            this.boundingRect = this.element.getBoundingClientRect();
            let transform = [];
            for (let key in this.motions) {
                let obj = this.motions[key];
                if (!obj.enable) {
                    continue;
                }
                obj.delta = this.getDelta(obj);
                this.animate(obj, key);
                if (key != 'opacity' && key != 'blur') {
                    transform.push(key+'(var(--'+key+'))');
                }
            }
            this.element.style.transform = transform.join(' ');
        },
        delete: function(){
            for (let key in this.observers) {
                this.observers[key].unobserve(this.element);
                $(window).off('scroll.motion-'+key+'-'+this.element.id);
            }
            this.observers = {};
            $(window).off('resize.motion-'+this.element.id);
            this.clear();
        },
        clear: function(){
            this.element.style.transform = '';
            for (let key in this.motions) {
                this.removeProperty(key);
            }
        },
        getDelta: function(obj){
            let top = window.pageYOffset,
                h = window.innerHeight,
                rect = this.boundingRect;

            return [top - (h - (h + rect.height) * obj.viewport.start / 100) + rect.top, top - (h - (h + rect.height) * obj.viewport.end / 100) + rect.top]
        },
        animate: function(obj, key){
            let top = window.pageYOffset,
                direction = key != 'opacity' && key != 'blur' && key != 'scale' ? obj.direction : 1;
            if ('speed' in obj) {
                direction *= obj.speed;
            }
            if (top < obj.delta[0]) {
                this.setProperty(obj, key, obj.property.start * direction)
            } else if (top > obj.delta[1]) {
                this.setProperty(obj, key, obj.property.end * direction)
            } else if (!obj.animating) {
                obj.animating = true;
                window.requestAnimationFrame(function(obj, key, direction) {
                    top = window.pageYOffset;
                    let value = obj.property.start + (obj.property.end - obj.property.start) * (top - obj.delta[0]) / (obj.delta[1] - obj.delta[0]);
                    value *= direction;
                    if (key == 'rotate') {
                        value = Math.round(value)
                    }
                    this.setProperty(obj, key, value);
                    obj.animating = false;
                }.bind(this, obj, key, direction));
            }
        },
        removeProperty: function(key){
            let property = key == 'opacity' ? key : key == 'blur' ? 'filter' : '--'+key;
            this.element.style.removeProperty(property);
        },
        setProperty: function(obj, key, value){
            let property = key == 'opacity' ? key : key == 'blur' ? 'filter' : '--'+key;
            value = key != 'blur' ? value+obj.property.unit : 'blur('+value+obj.property.unit+')';
            this.element.style.setProperty(property, value);
        }
    }

    $.fn.motion = function(option) {
        return this.each(function(){
            let $this = $(this),
                inMotion = false,
                data = $this.data('motion'),
                options = $.extend({}, option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            for (let ind in options) {
                for (let key in options[ind]) {
                    if (options[ind][key].enable) {
                        inMotion = true;
                        break;
                    }
                }
                if (inMotion) {
                    break;
                }
            }
            if (inMotion) {
                $this.data('motion', (data = new motion(this, options)));
                data.set();
            }
        });
    }
}(window.$g ? window.$g : window.jQuery);