/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    
    var testimonials = function (element, options) {
        this.parent = $(element);
        this.modal = null;
        this.modalImage = null;
        if (this.parent.hasClass('style-6')) {
            options.count = 1;
        }
        this.options = options;
        this.options.count *= 1;
        this.currentIndex = 0;
        this.content = this.parent.find('.slideshow-content');
        this.childrens = this.content.find('li.item:not(.ba-unpublished-html-item)');
        this.startCoords = {};
        this.endCoords = {};
        this.allowSlide = true;
        this.flag = true;
    }
    
    testimonials.prototype = {
        init : function(){
            var $this = this,
                dots = this.parent.find('.ba-slideset-dots');
            if (this.options.pause) {
                this.parent.on('mouseenter.testimonials', $.proxy(this.pause, this))
                    .on('mouseleave.testimonials', $.proxy(this.cycle, this));
            }
            dots.empty();
            this.parent[0].style.setProperty('--testimonials-info-height', '');
            this.parent[0].dataset.count = this.options.count;
            this.childCount = Math.floor(this.childrens.length / this.options.count);
            if (this.childCount < this.childrens.length / this.options.count) {
                this.childCount++;
            }
            for (var i = 0; i < this.childCount; i++) {
                dots.append('<div data-ba-slide-to="'+i+'" class="ba-icons ba-icon-circle"></div>');
            }
            this.parent.find('.active').removeClass('active');
            for (var i = 0; i < this.options.count; i++) {
                $(this.childrens[i]).css({
                    'animation-delay': (0.1 * i)+'s'
                });
            }
            for (var i = 0; i < this.options.count; i++) {
                $(this.childrens[i]).addClass('active');
            }
            this.setHeight();
            $('.ba-slideset-dots [data-ba-slide-to="'+this.currentIndex+'"]').addClass('active');
            this.cycle();
            this.parent.addClass('slideset-loaded');
            this.childrens.find('.ba-slideshow-img').on('mousedown.testimonials', function(event){
                if ($this.content.hasClass('lightbox-enabled')) {
                    $g('body').trigger(event);
                    event.stopPropagation();
                }
            }).on('click.testimonials', function(event){
                if ($this.content.hasClass('lightbox-enabled')) {
                    $this.modalImage = this;
                    var bgImage = $this.getImageURI();
                    if (bgImage) {
                        $this.pause();
                        $this.openModal(bgImage, this);
                    }
                }
            });
            this.parent.find('[data-ba-slide-to]').on('click.testimonials', function(event){
                event.preventDefault();
                if ($this.allowSlide) {
                    $this.allowSlide = false;
                    var index = $(this).attr('data-ba-slide-to');
                    $this.slideTo(index);
                }
            });
            this.parent.find('[data-slide]').on('click.testimonials',  function(event){
                event.preventDefault();
                var action = $(this).attr('data-slide');
                $this[action]();
            });
            this.parent.on('touchstart.testimonials', function(event){
                $this.endCoords = event.originalEvent.targetTouches[0];
                $this.startCoords = event.originalEvent.targetTouches[0];
            });
            this.parent.on('touchmove.testimonials', function(event){
                $this.endCoords = event.originalEvent.targetTouches[0];
            });
            this.parent.on('touchend.testimonials', function(event){
                var hDistance = $this.endCoords.pageX - $this.startCoords.pageX,
                    xabs = Math.abs($this.endCoords.pageX - $this.startCoords.pageX),
                    yabs = Math.abs($this.endCoords.pageY - $this.startCoords.pageY);
                if(hDistance >= 100 && xabs >= yabs * 2) {
                    $this.parent.find('[data-slide="prev"]').trigger('click');
                } else if (hDistance <= -100 && xabs >= yabs * 2) {
                    $this.parent.find('[data-slide="next"]').trigger('click');
                }
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
                wWidth = $g(window).width(),
                wHeigth = $g(window).height(),
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
                left = (wWidth - imgWidth) / 2;
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
            var el = $(this.modalImage).closest('li.item:not(.ba-unpublished-html-item)'),
                $this = this;
            if (el.prev().length > 0) {
                el = el.prev();
            } else {
                el = this.childrens.last();
            }
            this.modalImage = el.find('.ba-slideshow-img')[0];
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
            var el = $(this.modalImage).closest('li.item:not(.ba-unpublished-html-item)'),
                $this = this;
            if (el.next().length > 0) {
                el = el.next();
            } else {
                el = this.childrens.first();
            }
            this.modalImage = el.find('.ba-slideshow-img')[0];
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
            div.className = 'ba-image-modal instagram-modal testimonials-modal';
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
                }, 600);
            }
            image.src = src;
        },
        cycle: function(event){
            if (this.options.autoplay == 1) {
                this.flag = true;
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.interval = setInterval($.proxy(this.slide, this), this.options.delay);
                return this;
            }
        },
        pause: function() {
            if (this.interval) {
                clearInterval(this.interval);
            }
            this.interval = null;
            this.flag = false;
        },
        delete: function(){
            clearInterval(this.interval);
            this.interval = null;
            this.parent.off('mouseenter.testimonials mouseleave.testimonials');
            this.parent.find('[data-slide]').off('click.testimonials');
            this.parent.find('[data-ba-slide-to]').off('click.testimonials');
            this.parent.off('touchstart.testimonials touchmove.testimonials touchend.testimonials');
            this.content.off('mousedown.testimonials');
            this.childrens.find('.ba-slideshow-img').off('mousedown.testimonials click.testimonials');
        },
        slideTo: function(pos, direction){
            pos = pos * 1;
            if (pos != this.currentIndex) {
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.parent.find('.ba-slideset-dots .active').removeClass('active');
                this.parent.find('li.active:not(.ba-unpublished-html-item)')
                    .removeClass('active').addClass('testimonials-out-animation');
                var position = 0,
                    that = this;
                setTimeout(function(){
                    that.parent.find('li.testimonials-out-animation').removeClass('testimonials-out-animation');
                    for (var i = pos * that.options.count; i < pos * that.options.count + that.options.count; i++) {
                        if (!that.childrens[i]) {
                            continue;
                        }
                        $(that.childrens[i]).css({
                            'animation-delay': (0.1 * position++)+'s'
                        }).addClass('active');
                    }
                    that.setHeight();
                    that.parent.find('.ba-slideset-dots [data-ba-slide-to="'+pos+'"]').addClass('active');
                    setTimeout(function(){
                        that.allowSlide = true;
                    }, 300);
                    that.currentIndex = pos;
                    if (that.flag) {
                        that.cycle();
                    }
                }, 300);
            } else {
                this.allowSlide = true;
            }
        },
        setHeight: function(){
            var testimonialsInfo = 0,
                $this = this;
            this.parent.find('.testimonials-info').each(function(){
                if (this.offsetHeight > testimonialsInfo) {
                    testimonialsInfo = this.offsetHeight;
                }
            });
            this.parent[0].style.setProperty('--testimonials-info-height', testimonialsInfo+'px');
        },
        next : function(){
            if (this.allowSlide) {
                this.allowSlide = false;
                var pos = this.currentIndex + 1;
                if (pos > this.childCount - 1) {
                    pos = 0;
                }
                this.slideTo(pos, 'next');
            }
        },
        prev : function(){
            if (this.allowSlide) {
                this.allowSlide = false;
                var pos = this.currentIndex - 1;
                if (pos < 0) {
                    pos = this.childCount - 1;
                }
                this.slideTo(pos, 'prev');
            }
        },
        slide: function (){
            var pos = this.currentIndex + 1;
            if (pos > this.childCount - 1) {
                pos = 0;
            }
            this.slideTo(pos, 'next');
        }
    }
    
    $.fn.testimonials = function(option){
        return this.each(function(){
            var $this = $(this),
                data = $this.data('testimonials'),
                options = $.extend({}, $.fn.testimonials.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('testimonials', (data = new testimonials(this, options)));
            data.init();
        });
    }
    
    $.fn.testimonials.defaults = {
        delay: 3000,
        autoplay: true,
        pause: false,
        mode: 'set',
        gutter: true,
        count: 3
    }
}(window.$g ? window.$g : window.jQuery);