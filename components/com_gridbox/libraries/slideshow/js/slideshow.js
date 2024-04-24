/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    
    var slideshow = function(element, options) {
        this.parent = $(element);
        this.modal = null;
        this.modalImage = null;
        this.itemKey = this.parent.closest('.ba-item').attr('id');
        this.itemObject = app.items[this.itemKey];
        this.content = this.parent.find('> .slideshow-content');
        this.dots = this.parent.find('> .ba-slideshow-dots');
        this.dot = this.dots[0];
        this.dotsChildrens = null;
        this.dotsTranslateIndex = 0;
        this.childrens = this.content.find('> li:not(.ba-unpublished-html-item)');
        this.options = options;
        this.startCoords = {};
        this.endCoords = {};
        this.flag = true;
    }
    
    slideshow.prototype = {
        init : function() {
            var $this = this,
                count = this.getThumbCount(),
                leftThumb = this.dot ? this.dot.closest('.ba-left-thumbnails-navigation') : null;
            if (this.options.pause) {
                this.parent.on('mouseenter', $.proxy(this.pause, this))
                    .on('mouseleave', $.proxy(this.cycle, this));
            }
            this.dots.removeClass('move-started disable-move count-matched');
            this.dots.empty();
            for (var i = 0; i < this.childrens.length; i++) {
                this.dots.append('<div data-ba-slide-to="'+i+'" class="ba-icons ba-icon-circle"></div>');
            }
            this.dotsChildrens = this.dots.find('> div');
            if (this.dot) {
                this.dot.style.setProperty('--dots-count', this.dotsChildrens.length);
            }
            if (this.dotsChildrens.length <= count) {
                this.dots.addClass('disable-move');
            }
            if (this.dotsChildrens.length == count) {
                this.dots.addClass('count-matched');
            }
            if (this.options.navigation && this.dotsChildrens.length > 1) {
                this.setNavigation();
            }
            this.childrens.removeClass('active');
            this.dotsChildrens.removeClass('active');
            this.childrens.first().addClass('active');
            this.dotsChildrens.first().addClass('active');
            this.cycle();
            this.childrens.find('> .ba-slideshow-img').on('click.slideshow', function(event){
                if ($this.content.hasClass('lightbox-enabled')) {
                    $this.modalImage = this;
                    var bgImage = $this.getImageURI();
                    if (bgImage) {
                        $this.pause();
                        $this.openModal(bgImage, this);
                    }
                }
            });
            if (this.dots.hasClass('thumbnails-dots')) {
                this.dots.on('mousedown.slideshow', function(event){
                    var eventKey = leftThumb ? 'clientY' : 'clientX',
                        translate = leftThumb ? 'translateY' : 'translateX',
                        startPos = event[eventKey],
                        size = leftThumb ? window.innerHeight : window.innerWidth,
                        delta = 1,
                        mousemove = 0,
                        outside = !($this.dot && $this.dot.closest('.dots-position-outside')) ? ' + 20px' : '';
                    if ($this.dotsChildrens.length <= count) {
                        return false;
                    }
                    $this.dotsChildrens.css({
                        transition: 'none'
                    });
                    $(document).on('mousemove.slideshow', function(event){
                        mousemove = startPos - event[eventKey];
                        delta = mousemove / size * count;
                        $this.dotsChildrens.css({
                            transform : translate+'(calc((100% + 20px) * '+($this.dotsTranslateIndex - delta)+outside+'))'
                        });
                        if (mousemove != 0) {
                            $this.dots.addClass('move-started');
                        }
                        return false;
                    }).on('mouseup.slideshow', function(event){
                        $(document).off('mousemove.slideshow mouseup.slideshow');
                        if (mousemove != 0) {
                            if (delta > 0) {
                                $this.dotsTranslateIndex -= Math.ceil(delta);
                            } else {
                                $this.dotsTranslateIndex -= Math.floor(delta);
                            }
                            if (event[eventKey] > startPos && $this.dotsTranslateIndex > 0) {
                                $this.dotsTranslateIndex = 0;
                            } else if (event[eventKey] < startPos
                                && Math.abs($this.dotsTranslateIndex) > $this.childrens.length - count) {
                                $this.dotsTranslateIndex = ($this.childrens.length - count) * -1;
                            }
                            $this.dots.removeClass('move-started');
                            $this.dotsChildrens.css({
                                transform : translate+'(calc((100% + 20px) * '+$this.dotsTranslateIndex+outside+'))',
                                transition: 'transform 0.25s linear'
                            });
                        }
                    });
                    return false;
                });
            }
            this.dots.on('click.slideshow', '[data-ba-slide-to]', function(event){
                event.preventDefault();
                var index = $(this).attr('data-ba-slide-to');
                $this.to(index);
            });
            this.parent.find('> .ba-slideshow-nav [data-slide]').on('click.slideshow',  function(event){
                event.preventDefault();
                var action = $(this).attr('data-slide');
                $this[action]();
            });
            this.parent.on('touchstart.slideshow', function(event){
                $this.endCoords = event.originalEvent.targetTouches[0];
                $this.startCoords = event.originalEvent.targetTouches[0];
            });
            this.parent.on('touchmove.slideshow', function(event){
                $this.endCoords = event.originalEvent.targetTouches[0];
            });
            this.parent.on('touchend.slideshow', function(event){
                var hDistance = $this.endCoords.pageX - $this.startCoords.pageX,
                    xabs = Math.abs($this.endCoords.pageX - $this.startCoords.pageX),
                    yabs = Math.abs($this.endCoords.pageY - $this.startCoords.pageY);
                if (hDistance >= 100 && xabs >= yabs * 2) {
                    $this.prev();
                } else if (hDistance <= -100 && xabs >= yabs * 2) {
                    $this.next();
                }
            });
            var event = $.Event('ba-slide', {
                prevItem : null,
                currentItem : this.parent.find('.item').first()[0]
            });
            this.parent.trigger(event);
        },
        getThumbCount: function(){
            var count = 5;
            if (this.itemObject.desktop.thumbnails) {
                var object = $g.extend(true, {}, this.itemObject.desktop.thumbnails);
                if (app.view != 'desktop') {
                    for (var ind in breakpoints) {
                        if (!this.itemObject[ind]) {
                            this.itemObject[ind] = {
                                thumbnails : {}
                            };
                        }
                        object = $g.extend(true, {}, object, this.itemObject[ind].thumbnails);
                        if (ind == app.view) {
                            break;
                        }
                    }
                }
                count = object.count * 1;
            }

            return count;
        },
        setThumbOrder:function(prev, next, direction){
            var outside = !(this.dot && this.dot.closest('.dots-position-outside')) ? ' + 20px' : '',
                translate = (this.dot && this.dot.closest('.ba-left-thumbnails-navigation')) ? 'translateY' : 'translateX',
                count = this.getThumbCount(),
                prevIndex = next.attr('data-ba-slide-to') * 1,
                nextIndex = next.attr('data-ba-slide-to') * 1 + 1;
            if (count >= this.dotsChildrens.length) {
            	return false;
            }
            if (outside && this.dots.hasClass('disable-move') && this.dots.hasClass('center-align')
                && !this.dots.hasClass('count-matched')) {
                outside = '';
            } else if (outside && this.dots.hasClass('disable-move') && this.dots.hasClass('right-align')
                && !this.dots.hasClass('count-matched')) {
                outside = ' - 20px';
            }
            if (direction == 'next' && nextIndex - Math.abs(this.dotsTranslateIndex) == count
                && nextIndex != this.childrens.length) {
                this.dotsTranslateIndex -= 1
            } else if (direction == 'prev' && this.dotsTranslateIndex != 0 &&
                Math.abs(this.dotsTranslateIndex) + count - prevIndex == count) {
                this.dotsTranslateIndex += 1;
            } else if (direction == 'prev' && prevIndex == this.childrens.length - 1) {
                this.dotsTranslateIndex = count - this.childrens.length;
            } else if (direction == 'next' && prevIndex == 0) {
                this.dotsTranslateIndex = 0;
            }
            this.dotsChildrens.css({
                transform : translate+'(calc((100% + 20px) * '+this.dotsTranslateIndex+outside+'))',
                transition : 'transform 0.5s linear'
            });
        },
        getImageURI: function(){
            var styles = getComputedStyle(this.modalImage),
                match = styles.backgroundImage.match(/url\(([^\)]*)\)/),
                bgImage = null;
            if (match) {
                bgImage = match[1];
                if (bgImage.indexOf('h') !== 0) {
                    bgImage = bgImage.substring(1, bgImage.length - 1)
                }
            }

            return bgImage;
        },
        setSimpleImage: function(image) {
            var modal = this.modal,
                imgHeight = image.naturalHeight,
                imgWidth = image.naturalWidth,
                comp = getComputedStyle(document.body),
                wWidth = document.body.clientWidth,
                wHeigth = window.innerHeight,
                percent = imgWidth / imgHeight;
            if (wWidth > 1024) {
                if (imgWidth < wWidth && imgHeight < wHeigth) {
                
                } else {
                    if (imgWidth > imgHeight) {
                        imgWidth = wWidth - 100;
                        imgHeight = imgWidth / percent;
                    } else {
                        imgHeight = wHeigth - 100;
                        imgWidth = percent * imgHeight;
                    }
                    if (imgHeight > wHeigth) {
                        imgHeight = wHeigth - 100;
                        imgWidth = percent * imgHeight;
                    }
                    if (imgWidth > wWidth) {
                        imgWidth = wWidth - 100;
                        imgHeight = imgWidth / percent;
                    }
                }
            } else {
                percent = imgWidth / imgHeight;
                if (percent >= 1) {
                    imgWidth = wWidth * 0.90;
                    imgHeight = imgWidth / percent;
                    if (wHeigth - imgHeight < wHeigth * 0.1) {
                        imgHeight = wHeigth * 0.90;
                        imgWidth = imgHeight * percent;
                    }
                } else {
                    imgHeight = wHeigth * 0.90;
                    imgWidth = imgHeight * percent;
                    if (wWidth - imgWidth < wWidth * 0.1) {
                        imgWidth = wWidth * 0.90;
                        imgHeight = imgWidth / percent;
                    }
                }
            }
            var modalTop = (wHeigth - imgHeight) / 2,
                left = (wWidth - imgWidth) / 2 + comp.borderLeftWidth.replace('px', '') * 1;
            setTimeout(function(){
                modal.find('> div').css({
                    'background-image': 'url('+image.src+')',
                    'width' : Math.round(imgWidth),
                    'height' : Math.round(imgHeight),
                    'left' : Math.round(left),
                    'top' : Math.round(modalTop)
                }).addClass('instagram-fade-animation');
            }, 1);
        },
        simpleGetPrev: function(){
            var el = $(this.modalImage).closest('li.item'),
                $this = this;
            if (el.prev().length > 0) {
                el = el.prev();
            } else {
                el = this.childrens.last();
            }
            this.modalImage = el.find('> .ba-slideshow-img')[0];
            var bgImage = this.getImageURI();
            if (bgImage) {
                var image = document.createElement('img');
                image.onload = function(){
                    $this.setSimpleImage(this);
                }
                image.src = bgImage;
            }
        },
        simpleGetNext: function(img, images, index){
            var el = $(this.modalImage).closest('li.item'),
                $this = this;
            if (el.next().length > 0) {
                el = el.next();
            } else {
                el = this.childrens.first();
            }
            this.modalImage = el.find('> .ba-slideshow-img')[0];
            var bgImage = this.getImageURI();
            if (bgImage) {
                var image = document.createElement('img');
                image.onload = function(){
                    $this.setSimpleImage(this);
                }
                image.src = bgImage;
            }
        },
        simpleModalClose: function(modal, images, index){
            var $this = this;
            this.modal.addClass('image-lightbox-out');
            $g(window).off('keyup.slideshow');
            setTimeout(function(){
                $this.modal.remove();
                $this.cycle();
            }, 500);
        },
        openModal: function(src, original){
            var image = document.createElement('img'),
                $this = this,
                div = document.createElement('div'),
                origImage = $(original),
                key = origImage.closest('.ba-item').attr('id'),
                width = origImage.width(),
                height = origImage.height(),
                offset = origImage.offset(),
                imgHeight = this.naturalHeight,
                imgWidth = this.naturalWidth,
                wWidth = $g(window).width(),
                wHeigth = $g(window).height(),
                percent = imgWidth / imgHeight,
                img = document.createElement('div');
            $this.modal = $g(div);
            img.style.top = (offset.top - $g(window).scrollTop())+'px';
            img.style.left = offset.left+'px';
            img.style.width = width+'px';
            img.style.height = height+'px';
            div.className = 'ba-image-modal instagram-modal carousel-modal';
            div.style.backgroundColor = app.getCorrectColor(app.items[key].lightbox.color);
            div.appendChild(img);
            $this.modal.on('click', function(){
                $this.simpleModalClose();
            });
            $g('body').append(div);
            image.onload = function(){
                $this.setSimpleImage(this);
                setTimeout(function(){
                    var str = '<i class="ba-icons ba-icon-chevron-left"></i><i class="ba-icons ba-icon-chevron-right"></i>';
                    str += '<i class="ba-icons ba-icon-close">';
                    $this.modal.append(str);
                    $this.modal.find('.ba-icon-chevron-left').on('click', function(event){
                        event.stopPropagation();
                        $this.simpleGetPrev();
                    });
                    $this.modal.find('.ba-icon-chevron-right').on('click', function(event){
                        event.stopPropagation();
                        $this.simpleGetNext();
                    });
                    $this.modal.find('.ba-icon-close').on('click', function(event){
                        event.stopPropagation();
                        $this.simpleModalClose();
                    });
                    $g(window).on('keyup.slideshow', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        if (event.keyCode === 37) {
                            $this.simpleGetPrev();
                        } else if (event.keyCode === 39) {
                            $this.simpleGetNext();
                        } else if (event.keyCode === 27) {
                            $this.simpleModalClose();
                        }
                    });
                }, 600);
            }
            image.src = src;
        },
        cycle: function() {
            if (this.options.autoplay) {
                this.flag = true;
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.interval = setInterval($.proxy(this.next, this), this.options.delay);
                
                return this;
            }
        },
        setNavigation: function(){
            this.parent.addClass('navigation-style');
            var active = this.getActiveIndex(),
                items = this.childrens,
                ind = active - 1,
                div = document.createElement('div'),
                h3 = document.createElement('h3'),
                img;
            if (ind < 0 ) {
                ind = items.length - 1;
            }
            img = $(items[ind]).find('> .ba-slide-img').attr('data-img-url');
            this.parent.find('.navigation-prev-content, .navigation-next-content').remove();
            if (img) {
                $(h3).css(this.options.style);
                $(h3).text($(items[ind]).find('.ba-slideshow-title').text());
                div.className = 'navigation-prev-content';
                div.style.backgroundImage = 'url('+img+')';
                div.appendChild(h3);
                this.parent.find('.slideshow-btn-prev').after(div);
            }
            ind = active * 1 + 1;
            if (ind == items.length) {
                ind = 0;
            }
            img = $(items[ind]).find('> .ba-slide-img').attr('data-img-url');
            if (img) {
                div = document.createElement('div');
                h3 = document.createElement('h3')
                div.className = 'navigation-next-content';
                div.style.backgroundImage = 'url('+img+')';
                $(h3).css(this.options.style);
                $(h3).text($(items[ind]).find('.ba-slideshow-title').text());
                div.appendChild(h3);
                this.parent.find('.slideshow-btn-next').after(div);
            }
            var $this = this;
            $('.navigation-prev-content').on('click', function(){
                $this.parent.find('.slideshow-btn-prev').trigger('click');
            });
            $('.navigation-next-content').on('click', function(){
                $this.parent.find('.slideshow-btn-next').trigger('click');
            });
        },
        delete: function(){
            if (this.interval) {
                clearInterval(this.interval);
            }
            this.interval = null;
            this.parent.find('> .ba-slideshow-nav [data-slide]').off('click.slideshow');
            this.dots.off('click.slideshow mousedown.slideshow');
            this.parent.off('touchstart.slideshow touchmove.slideshow touchend.slideshow');
            this.parent.removeClass('navigation-style');
            this.parent.find('.navigation-prev-content, .navigation-next-content').remove();
            this.content.find('> .ba-next').removeClass('ba-next');
            this.content.find('> .ba-prev').removeClass('ba-prev');
            this.content.find('> .ba-left').removeClass('ba-left');
            this.content.find('> .ba-right').removeClass('ba-right');
            this.content.find('> .burns-out').removeClass('burns-out');
            this.parent.off('mouseenter mouseleave');
            this.childrens.find('> .ba-slideshow-img').off('click.slideshow');
        },
        getActiveIndex: function(){
            this.active = this.content.find('> .item.active:not(.ba-unpublished-html-item)');

            return this.childrens.index(this.active);
        },
        to: function(pos){
            var activeIndex = this.getActiveIndex();
            if (activeIndex == pos) {
                return this.cycle();
            }
            if (this.interval) {
                clearInterval(this.interval);
            }

            return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.childrens[pos]));
        },
        pause: function(){
            if (this.interval) {
                clearInterval(this.interval);
            }
            this.interval = null;
            this.flag = false;
        },
        next: function(){
            if (this.interval) {
                clearInterval(this.interval);
            }
            let index = this.getActiveIndex(),
                item = this.childrens[index + 1] ? this.childrens[index + 1] : this.childrens[0];

            return this.slide('next', $(item));
        },
        prev: function(){
            if (this.interval) {
                clearInterval(this.interval);
            }
            let index = this.getActiveIndex(),
                item = this.childrens[index - 1] ? this.childrens[index - 1] : this.childrens.last();

            return this.slide('prev', $(item));
        },
        slide: function(type, next){
            var active = this.content.find('> .item.active'),
                $next = next || active[type](),
                fallback  = type == 'next' ? 'first' : 'last',
                event,
                parent = this.parent;
            this.parent.removeClass('first-load-slideshow');
            if ($next.length == 0) {
                $next = this.content.find('> .item')[fallback]();
            }
            parent.find('.select-animation').removeClass('select-animation');
            event = $.Event('ba-slide', {
                prevItem : active[0],
                currentItem : $next[0]
            });
            if ($next.hasClass('active')) {
                return;
            }
            this.content.find('> .ba-next, > .ba-prev, > .ba-left, > .ba-right')
                .removeClass('ba-next ba-prev ba-left ba-right');
            if (fallback == 'first') {
                active.addClass('ba-next').addClass('burns-out');
                $next.addClass('ba-right');
            } else {
                active.addClass('ba-prev').addClass('burns-out');
                $next.addClass('ba-left');
            }
            setTimeout(function(){
                active.removeClass('burns-out');
            }, 600);
            active.removeClass('active');
            $next.addClass('active');
            var lastDotActive = this.dots.find('.active').removeClass('active'),
                index = this.getActiveIndex(),
                nextActiveDot = $(this.dotsChildrens[index]).addClass('active');
            if (this.dots.hasClass('thumbnails-dots')) {
                this.setThumbOrder(lastDotActive, nextActiveDot, type);
            }
            parent.trigger(event);
            if (this.flag) {
                this.cycle();
            }
            if (this.options.navigation && this.dotsChildrens.length > 1) {
                this.setNavigation();
            }

            return this;
        }
    }
    
    $.fn.slideshow = function(option){
        return this.each(function(){
            var $this = $(this),
                data = $this.data('slideshow'),
                options = $.extend({}, $.fn.slideshow.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('slideshow', (data = new slideshow(this, options)));
            data.init();
        });
    }
    
    $.fn.slideshow.defaults = {
        delay : 3000,
        autoplay : true,
        pause : false,
        navigation : false
    }
    
    $.fn.slideshow.Constructor = slideshow;

}(window.$g ? window.$g : window.jQuery);