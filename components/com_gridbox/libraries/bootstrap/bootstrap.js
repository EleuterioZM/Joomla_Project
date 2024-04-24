try {
    jQuery.event.special.touchstart = {
        setup: function( _, ns, handle ) {
            this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    jQuery.event.special.touchmove = {
        setup: function( _, ns, handle ) {
            this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    jQuery.event.special.scroll = {
        setup: function( _, ns, handle ){
            this.addEventListener("scroll", handle, { passive: true });
        }
    };
    jQuery.event.special.wheel = {
        setup: function( _, ns, handle ){
            this.addEventListener("wheel", handle, { passive: true });
        }
    };
    jQuery.event.special.mousewheel = {
        setup: function( _, ns, handle ){
            this.addEventListener("mousewheel", handle, { passive: true });
        }
    };
} catch (e){
    console.info(e)
}

document.addEventListener('DOMContentLoaded', function(){
    jQuery(document).off('click.bs.tab.data-api click.bs.modal.data-api click.bs.collapse.data-api click.dropdown.data-api');

    !function ($) {
        "use strict";
        var toggle = '[data-toggle=dropdown]',
            Dropdown = function(element){
                var $el = $(element).on('click.dropdown.data-api', this.toggle)
                    .on('mouseover.dropdown.data-api', this.toggle)
                $('html').on('click.dropdown.data-api', function () {
                    $el.parent().parent().removeClass('nav-hover')
                    $el.parent().removeClass('open');
                });
            }

        Dropdown.prototype = {
            constructor: Dropdown,
            toggle: function(e){
                var $this = $(this),
                    $parent, isActive, url, isHover;
                if ($this.is('.disabled, :disabled')) return;
                $parent = getParent($this);
                isActive = $parent.hasClass('open');
                isHover = $parent.parent().hasClass('nav-hover');
                if (!isHover && e.type == 'mouseover') return;
                url = $this.attr('href');
                if (e.type == 'click' && (url) && (url !== '#')) {
                    window.location = url;
                    return;
                }
                clearMenus()
                if ((!isActive && e.type != 'mouseover') || (isHover && e.type == 'mouseover')) {
                    if ('ontouchstart' in document.documentElement) {
                        $('<div class="dropdown-backdrop"/>').insertBefore($(this)).on('click', clearMenus);
                        $this.on('hover', function () {
                            $('.dropdown-backdrop').remove()
                        });
                    }
                    $parent.parent().toggleClass('nav-hover');
                    $parent.toggleClass('open')
                }
                $this.focus()
                return false
            },
            keydown: function (e) {
                var $this, $items, $active, $parent, isActive, index;
                if (!/(38|40|27)/.test(e.keyCode)) return
                $this = $(this);
                e.preventDefault()
                e.stopPropagation()
                if ($this.is('.disabled, :disabled')) return
                $parent = getParent($this)
                isActive = $parent.hasClass('open')
                if (!isActive || (isActive && e.keyCode == 27)) {
                    if (e.which == 27) $parent.find(toggle).focus()
                    return $this.click()
                }
                $items = $('[role=menu] li:not(.divider):visible a', $parent)
                if (!$items.length) return
                index = $items.index($items.filter(':focus'))
                if (e.keyCode == 38 && index > 0) index--
                if (e.keyCode == 40 && index < $items.length - 1) index++
                if (!~index) index = 0
                $items.eq(index).focus();
            }
        }
        function clearMenus(){
            $(toggle).parent().parent().removeClass('nav-hover')
            $('.dropdown-backdrop').remove()
            $(toggle).each(function () {
                getParent($(this)).removeClass('open')
            })
        }
        function getParent($this) {
            var selector = $this.attr('data-target'),
                $parent;
            if (!selector) {
                selector = $this.attr('href')
                selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
            }
            selector = selector === '#' ? [] : selector;
            $parent = selector && $(document).find(selector)
            if (!$parent || !$parent.length) $parent = $this.parent()

            return $parent
        }
        var old = $.fn.dropdown
        $.fn.dropdown = function (option) {
            return this.each(function () {
                var $this = $(this),
                    data = $this.data('dropdown');
                if (!data) $this.data('dropdown', (data = new Dropdown(this)))
                if (typeof option == 'string') data[option].call($this)
            });
        }
        $.fn.dropdown.Constructor = Dropdown
        $.fn.dropdown.noConflict = function () {
            $.fn.dropdown = old
            return this
        }
        $(document)
        .on('click.dropdown.data-api', clearMenus)
        .on('click.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
        .on('click.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
        .on('keydown.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)
        .on('mouseover.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    }(window.$g ? window.$g : window.jQuery);

    !function ($) {
        "use strict";

        var dismiss = '[data-dismiss="alert"]',
            Alert = function(el){
                $(el).on('click', dismiss, this.close)
            }
        Alert.prototype.close = function(e){
            var $this = $(this),
                selector = $this.attr('data-target'),
                $parent;
            if (!selector) {
                selector = $this.attr('href')
                selector = selector === '#' ? '' : selector
            }
            $parent = $(document).find(selector);
            e && e.preventDefault()
            $parent.length || ($parent = $this.hasClass('alert') ? $this : $this.parent())
            $parent.trigger(e = $.Event('close'))
            if (e.isDefaultPrevented()) return
            $parent.removeClass('in')
            function removeElement() {
                $parent.trigger('closed').remove()
            }
            $.support.transition && $parent.hasClass('fade') ?
                $parent.on($.support.transition.end, removeElement) : removeElement()
        }
        var old = $.fn.alert
        $.fn.alert = function (option) {
            return this.each(function () {
                var $this = $(this),
                    data = $this.data('alert');
                if (!data) $this.data('alert', (data = new Alert(this)))
                if (typeof option == 'string') data[option].call($this)
            });
        }
        $.fn.alert.Constructor = Alert;
        $.fn.alert.noConflict = function(){
            $.fn.alert = old;
            return this
        }
        $(document).on('click.alert.data-api', dismiss, Alert.prototype.close);
    }(window.$g ? window.$g : window.jQuery);

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
    }(window.$g ? window.$g : window.jQuery);

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
    }(window.$g ? window.$g : window.jQuery);

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
                this.$element.trigger('show-gridbox-modal');
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
                    that.$element.trigger('hidden-gridbox-modal');
                });
            },
            removeBackdrop: function(){
                if (!this.outBackdrop) {
                    return;
                }
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
    }(window.$g ? window.$g : window.jQuery);

    !function ($){
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
    }(window.$g ? window.$g : window.jQuery);

    !function ($) {
        "use strict";
        var Tooltip = function (element, options) {
            this.init('tooltip', element, options);
        }
        Tooltip.prototype = {
            constructor: Tooltip,
            init: function (type, element, options) {
                var eventIn, eventOut, triggers, trigger, i;
                this.type = type;
                this.$element = $(element);
                this.options = this.getOptions(options);
                this.enabled = true;
                triggers = this.options.trigger.split(' ');
                for (i = triggers.length; i--;) {
                    trigger = triggers[i];
                    if (trigger == 'click') {
                        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this));
                    } else if (trigger != 'manual') {
                        eventIn = trigger == 'hover' ? 'mouseenter' : 'focus';
                        eventOut = trigger == 'hover' ? 'mouseleave' : 'blur';
                        this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this));
                        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this));
                    }
                }
                this.options.selector ?
                (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) : this.fixTitle()
            },
            getOptions: function (options) {
                options = $.extend({}, $.fn[this.type].defaults, this.$element.data(), options);
                if (options.delay && typeof options.delay == 'number') {
                    options.delay = {
                        show: options.delay,
                        hide: options.delay
                    }
                }

                return options
            },
            enter: function (e) {
                var defaults = $.fn[this.type].defaults,
                    options = {}, self;

                this._options && $.each(this._options, function (key, value) {
                    if (defaults[key] != value) options[key] = value;
                }, this);
                self = $(e.currentTarget)[this.type](options).data(this.type)
                if (!self.options.delay || !self.options.delay.show) return self.show();
                clearTimeout(this.timeout)
                self.hoverState = 'in'
                this.timeout = setTimeout(function() {
                    if (self.hoverState == 'in') self.show()
                }, self.options.delay.show)
            },
            leave: function (e) {
                var self = $(e.currentTarget)[this.type](this._options).data(this.type)
                if (this.timeout) clearTimeout(this.timeout)
                if (!self.options.delay || !self.options.delay.hide) return self.hide()
                self.hoverState = 'out'
                this.timeout = setTimeout(function() {
                    if (self.hoverState == 'out') self.hide()
                }, self.options.delay.hide)
            },
            show: function () {
                var $tip, pos, actualWidth, actualHeight, placement, tp, e = $.Event('show');
                if (this.hasContent() && this.enabled) {
                    this.$element.trigger(e)
                    if (e.isDefaultPrevented()) return
                    $tip = this.tip()
                    this.setContent()
                    if (this.options.animation) {
                        $tip.addClass('fade')
                    }
                    placement = typeof this.options.placement == 'function' ?
                        this.options.placement.call(this, $tip[0], this.$element[0]) : this.options.placement;
                    $tip.detach().css({ top: 0, left: 0, display: 'block' });
                    this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)
                    pos = this.getPosition()
                    actualWidth = $tip[0].offsetWidth
                    actualHeight = $tip[0].offsetHeight
                    switch (placement) {
                        case 'bottom':
                            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
                            break
                        case 'top':
                            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
                            break
                        case 'left':
                            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
                            break
                        case 'right':
                            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
                            break
                    }
                    this.applyPlacement(tp, placement)
                    this.$element.trigger('shown')
                }
            },
            applyPlacement: function(offset, placement){
                var $tip = this.tip(),
                    width = $tip[0].offsetWidth,
                    height = $tip[0].offsetHeight,
                    actualWidth, actualHeight, delta, replace;
                $tip.offset(offset).addClass(placement).addClass('in');
                actualWidth = $tip[0].offsetWidth
                actualHeight = $tip[0].offsetHeight
                if (placement == 'top' && actualHeight != height) {
                    offset.top = offset.top + height - actualHeight
                    replace = true
                }
                if (placement == 'bottom' || placement == 'top') {
                    delta = 0
                    if (offset.left < 0){
                        delta = offset.left * -2
                        offset.left = 0
                        $tip.offset(offset)
                        actualWidth = $tip[0].offsetWidth
                        actualHeight = $tip[0].offsetHeight
                    }
                    this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
                } else {
                    this.replaceArrow(actualHeight - height, actualHeight, 'top')
                }
                if (replace) $tip.offset(offset)
            },
            replaceArrow: function(delta, dimension, position){
                this.arrow().css(position, delta ? (50 * (1 - delta / dimension) + "%") : '');
            },
            setContent: function () {
                var $tip = this.tip(),
                    title = this.getTitle();
                $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
                $tip.removeClass('fade in top bottom left right')
            },
            hide: function () {
                var that = this,
                    $tip = this.tip(),
                    e = $.Event('hideme');
                this.$element.trigger(e)
                if (e.isDefaultPrevented()) return
                $tip.removeClass('in')
                function removeWithAnimation() {
                    var timeout = setTimeout(function () {
                        $tip.off($.support.transition.end).detach()
                    }, 500);
                    $tip.one($.support.transition.end, function () {
                        clearTimeout(timeout)
                        $tip.detach()
                    })
                }
                $.support.transition && this.$tip.hasClass('fade') ? removeWithAnimation() : $tip.detach();
                this.$element.trigger('hidden')

                return this
            },
            fixTitle: function () {
                var $e = this.$element
                if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
                    $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
                }
            },
            hasContent: function () {
                return this.getTitle()
            },
            getPosition: function () {
                var el = this.$element[0]
                return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
                    width: el.offsetWidth,
                    height: el.offsetHeight
                }, this.$element.offset())
            },
            getTitle: function () {
                var title,
                    $e = this.$element,
                    o = this.options
                title = $e.attr('data-original-title') || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

                return title
            },
            tip: function (){
                return this.$tip = this.$tip || $(this.options.template)
            },
            arrow: function(){
                return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
            },
            validate: function (){
                if (!this.$element[0].parentNode) {
                    this.hide()
                    this.$element = null
                    this.options = null
                }
            },
            enable: function (){
                this.enabled = true
            },
            disable: function (){
                this.enabled = false
            },
            toggleEnabled: function (){
                this.enabled = !this.enabled
            },
            toggle: function (e){
                var self = e ? $(e.currentTarget)[this.type](this._options).data(this.type) : this
                self.tip().hasClass('in') ? self.hide() : self.show()
            },
            destroy: function () {
                this.hide().$element.off('.' + this.type).removeData(this.type)
            }
        }
        var old = $.fn.tooltip
        $.fn.tooltip = function(option) {
            return this.each(function(){
                var $this = $(this),
                    data = $this.data('tooltip'),
                    options = typeof option == 'object' && option;
                if (!data) $this.data('tooltip', (data = new Tooltip(this, options)))
                if (typeof option == 'string') data[option]()
            });
        }
        $.fn.tooltip.Constructor = Tooltip
        $.fn.tooltip.defaults = {
            animation: true,
            placement: 'top',
            selector: false,
            template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
            trigger: 'hover focus',
            title: '',
            delay: 0,
            html: true,
            container: false
        }
        $.fn.tooltip.noConflict = function(){
            $.fn.tooltip = old
            return this
        }
    }(window.$g ? window.$g : window.jQuery);
});