document.addEventListener('DOMContentLoaded', function(){
    jQuery(document).off('click.bs.tab.data-api click.bs.modal.data-api click.bs.collapse.data-api');

    !function ($) {
        "use strict";

        $(function(){
            $.support.transition = (function(){
                var transitionEnd = (function(){
                    var el = document.createElement('bootstrap'),
                        transEndEventNames = {
                            'WebkitTransition' : 'webkitTransitionEnd',
                            'MozTransition' : 'transitionend',
                            'OTransition' : 'oTransitionEnd otransitionend',
                            'transition' : 'transitionend'
                        },
                        name
                    for (name in transEndEventNames){
                        if (el.style[name] !== undefined) {
                            return transEndEventNames[name]
                        }
                    }
                }())

                return transitionEnd && {
                    end: transitionEnd
                }
            })()
        })
    }(window.jQuery);

    !function ($) {
        "use strict";

        var Tab = function(element){
            this.element = $(element);
        }

        Tab.prototype = {
            constructor: Tab,
            show: function(){
                var $this = this.element,
                    $ul = $this.closest('ul:not(.dropdown-menu)'),
                    selector = $this.attr('data-target'),
                    previous, $target, e;
                if (!selector) {
                    selector = $this.attr('href');
                }
                if ($this.parent('li').hasClass('active')) return;
                previous = $ul.find('.active:last a')[0];
                e = $.Event('show', {
                    relatedTarget: previous
                });
                $this.trigger(e);
                if (e.isDefaultPrevented()) return;
                $target = $(selector);
                this.activate($this.parent('li'), $ul);
                this.activate($target, $target.parent(), function(){
                    $this.trigger({
                        type: 'shown',
                        relatedTarget: previous
                    });
                });
            },
            activate: function(element, container, callback){
                var $active = container.find('> .active'),
                    transition = callback && $.support.transition && $active.hasClass('fade');
                function next() {
                    $active.removeClass('active').find('> .dropdown-menu > .active').removeClass('active');
                    element.addClass('active')
                    if (transition) {
                        element[0].offsetWidth;
                        element.addClass('in');
                    } else {
                        element.removeClass('fade');
                    }
                    if (element.parent('.dropdown-menu')) {
                        element.closest('li.dropdown').addClass('active');
                    }
                    callback && callback();
                }
                transition ? $active.one($.support.transition.end, next) : next();
                $active.removeClass('in');
            }
        }

        var old = $.fn.tab;
        $.fn.tab = function(option){
            return this.each(function(){
                var $this = $(this),
                    data = $this.data('tab');
                    if (!data) $this.data('tab', (data = new Tab(this)));
                    if (typeof option == 'string') data[option]();
                });
        }
        $.fn.tab.Constructor = Tab;
        $.fn.tab.noConflict = function(){
            $.fn.tab = old;
            return this;
        }

        $(document).off('click.tab.data-api').on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function(e){
            e.preventDefault();
            $(this).tab('show');
        });
    }(window.jQuery);

    !function ($) {
        "use strict";

        var Modal = function(element, options){
            this.options = options;
            this.$element = $(element).delegate('[data-dismiss="modal"]', 'click.dismiss.modal', $.proxy(this.hide, this));
            this.options.remote && this.$element.find('.modal-body').load(this.options.remote);
        }

        Modal.prototype = {
            constructor: Modal,
            toggle: function(){
                return this[!this.isShown ? 'show' : 'hide']();
            },
            show: function(){
                var that = this,
                    e = $.Event('show');
                this.$element.trigger(e);
                if (this.isShown || e.isDefaultPrevented()) return;
                this.isShown = true;
                this.escape();
                this.backdrop(function(){
                    var transition = $.support.transition && that.$element.hasClass('fade');
                    if (!that.$element.parent().length) {
                        that.$element.appendTo(document.body);
                    }
                    that.$element.show();
                    if (transition) {
                        that.$element[0].offsetWidth;
                    }
                    that.$element.addClass('in').attr('aria-hidden', false);
                    transition ?
                        that.$element.one($.support.transition.end, function(){that.$element.focus().trigger('shown')}) :
                        that.$element.focus().trigger('shown');
                    });
            },
            hide: function(e){
                e && e.preventDefault();
                var that = this;
                e = $.Event('hide');
                this.$element.trigger(e);
                if (!this.isShown || e.isDefaultPrevented()) return;
                this.isShown = false;
                this.escape();
                this.$element.removeClass('in').attr('aria-hidden', true);
                $.support.transition && this.$element.hasClass('fade') ? this.hideWithTransition() : this.hideModal();
            },
            escape: function(){
                var that = this;
                if (this.isShown && this.options.keyboard) {
                    this.$element.on('keyup.dismiss.modal', function(e){
                        e.which == 27 && that.hide();
                    });
                } else if (!this.isShown) {
                    this.$element.off('keyup.dismiss.modal');
                }
            },
            hideWithTransition: function(){
                var that = this,
                    timeout = setTimeout(function(){
                        that.$element.off($.support.transition.end);
                        that.hideModal();
                    }, 500);
                this.$element.one($.support.transition.end, function(){
                    clearTimeout(timeout);
                    that.hideModal();
                });
            },
            hideModal: function(){
                var that = this;
                this.$element.hide();
                this.backdrop(function(){
                    that.removeBackdrop();
                    that.$element.trigger('hidden');
                });
            },
            removeBackdrop: function(){
                this.outBackdrop.remove();
                this.outBackdrop = null;
            },
            backdrop: function(callback){
                var that = this,
                    animate = this.$element.hasClass('fade') ? 'fade' : '';
                if (this.$backdrop) {
                    this.outBackdrop = this.$backdrop;
                    this.$backdrop = null;
                }
                if (this.isShown && this.options.backdrop) {
                    var doAnimate = $.support.transition && animate;
                    this.$backdrop = $('<div class="modal-backdrop '+animate+'" />').appendTo(document.body);
                    this.$backdrop.click(this.options.backdrop == 'static' ?
                        $.proxy(this.$element[0].focus, this.$element[0]) : $.proxy(this.hide, this));
                    if (doAnimate) this.$backdrop[0].offsetWidth;
                    this.$backdrop.addClass('in');
                    if (!callback) return;
                    doAnimate ? this.$backdrop.one($.support.transition.end, callback) : callback();
                } else if (!this.isShown && this.outBackdrop) {
                    this.outBackdrop.removeClass('in');
                    $.support.transition && this.$element.hasClass('fade') ?
                    this.outBackdrop.one($.support.transition.end, callback) : setTimeout(callback, 300);
                } else if (callback) {
                    callback();
                }
            }
        }

        var old = $.fn.modal;
        $.fn.modal = function(option){
            return this.each(function(){
                var $this = $(this),
                    data = $this.data('modal'),
                    options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option);
                if (!data) {
                    $this.data('modal', (data = new Modal(this, options)))
                }
                if (typeof option == 'string') {
                    data[option]();
                } else if (options.show) {
                    data.show();
                }
            });
        }
        $.fn.modal.defaults = {
            backdrop: true,
            keyboard: true,
            show: true
        }
        $.fn.modal.Constructor = Modal;
        $.fn.modal.noConflict = function(){
            $.fn.modal = old;
            return this;
        }
        $(document).off('click.modal.data-api').on('click.modal.data-api', '[data-toggle="modal"]', function(e){
            var $this = $(this),
                href = $this.attr('href'),
                $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))), //strip for ie7
                option = $target.data('modal') ? 'toggle' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data());
            e.preventDefault();
            $target.modal(option).one('hide', function(){
                $this.focus();
            });
        });
    }(window.jQuery);


    !function ($) {
        "use strict";

        var Collapse = function(element, options){
            this.$element = $(element);
            this.options = $.extend({}, $.fn.collapse.defaults, options);
            if (this.options.parent) {
                this.$parent = $(this.options.parent);
            }
            this.options.toggle && this.toggle();
        }

        Collapse.prototype = {
            constructor: Collapse,
            dimension: function(){
                var hasWidth = this.$element.hasClass('width');
                return hasWidth ? 'width' : 'height';
            },
            show: function(){
                var dimension, scroll, actives, hasData;
                if (this.transitioning || this.$element.hasClass('in')) return;
                dimension = this.dimension();
                scroll = $.camelCase(['scroll', dimension].join('-'));
                actives = this.$parent && this.$parent.find('> .accordion-group > .in');
                if (actives && actives.length) {
                    hasData = actives.data('collapse');
                    if (hasData && hasData.transitioning) return;
                    actives.collapse('hide');
                    hasData || actives.data('collapse', null);
                }
                this.$element[dimension](0);
                this.transition('addClass', $.Event('show'), 'shown');
                $.support.transition && this.$element[dimension](this.$element[0][scroll]);
            },
            hide: function(){
                var dimension;
                if (this.transitioning || !this.$element.hasClass('in')) return;
                dimension = this.dimension();
                this.reset(this.$element[dimension]());
                this.transition('removeClass', $.Event('hide'), 'hidden');
                this.$element[dimension](0);
            },
            reset: function(size){
                var dimension = this.dimension();
                this.$element.removeClass('collapse')[dimension](size || 'auto')[0].offsetWidth;
                this.$element[size !== null ? 'addClass' : 'removeClass']('collapse');
                return this;
            },
            transition: function(method, startEvent, completeEvent){
                var that = this,
                    complete = function(){
                        if (startEvent.type == 'show') that.reset();
                        that.transitioning = 0;
                        that.$element.trigger(completeEvent);
                    };
                this.$element.trigger(startEvent);
                if (startEvent.isDefaultPrevented()) return;
                this.transitioning = 1;
                this.$element[method]('in');
                $.support.transition && this.$element.hasClass('collapse') ? this.$element.one($.support.transition.end, complete) : complete();
            },
            toggle: function(){
                this[this.$element.hasClass('in') ? 'hide' : 'show']();
            }
        }

        var old = $.fn.collapse;
        $.fn.collapse = function(option){
            return this.each(function(){
                var $this = $(this),
                    data = $this.data('collapse'),
                    options = $.extend({}, $.fn.collapse.defaults, $this.data(), typeof option == 'object' && option);
                if (!data) $this.data('collapse', (data = new Collapse(this, options)));
                if (typeof option == 'string') data[option]();
            });
        }
        $.fn.collapse.defaults = {
            toggle: true
        }
        $.fn.collapse.Constructor = Collapse;
        $.fn.collapse.noConflict = function(){
            $.fn.collapse = old;
            return this;
        }
        $(document).off('click.collapse.data-api').on('click.collapse.data-api', '[data-toggle=collapse]', function(e){
            var $this = $(this), href,
                target = $this.attr('data-target') || e.preventDefault() || (href = $this.attr('href')),
                option = $(target).data('collapse') ? 'toggle' : $this.data();
            $this[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed');
            $(target).collapse(option);
        });
    }(window.jQuery);
});