/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    
    var slideset = function (element, options) {
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
        this.isEdge = /Edge/.test(navigator.userAgent) || /Trident.*rv\:11\./.test(navigator.userAgent);
    }
    
    slideset.prototype = {
        init : function(){
            var $this = this,
                dots = this.parent.find('.ba-slideset-dots'),
                count = this.options.count,
                margin = this.options.gutter ? 30 : 0;
            if (this.options.pause) {
                this.parent.on('mouseenter.slideset', $.proxy(this.pause, this))
                    .on('mouseleave.slideset', $.proxy(this.cycle, this));
            }
            dots.empty();
            this.childrens.css({
                transition: '',
                left: ''
            });
            this.parent[0].style.setProperty('--testimonials-info-height', '');
            this.childCount = Math.floor(this.childrens.length / this.options.count);
            if (this.childCount < this.childrens.length / this.options.count) {
                this.childCount++;
            }
            for (var i = 0; i < this.childCount; i++) {
                dots.append('<div data-ba-slide-to="'+i+'" class="ba-icons ba-icon-circle"></div>');
            }
            this.content.removeClass('move-started');
            this.parent.find('.active').removeClass('active');
            for (var i = 0; i < this.options.count; i++) {
                $(this.childrens[i]).addClass('active').attr('data-position', i);
            }
            $('.ba-slideset-dots [data-ba-slide-to="'+this.currentIndex+'"]').addClass('active');
            this.clearAnimation();
            this.setLeft();
            this.cycle();
            this.parent.addClass('slideset-loaded');
            this.setHeight();
            if (this.childCount > 1) {
                this.content.on('mousedown.slideset touchstart.slideset', function(event){
                    var width = $(window).width(),
                        pwidth = $this.parent.width(),
                        move = {
                            x: 0,
                            y: 0
                        },
                        swipe = event.type == 'mousedown' ? event : event.originalEvent.targetTouches[0],
                        start = {
                            x: swipe.clientX,
                            y: swipe.clientY
                        },
                        left;
                    $this.childrens.css({
                        transition: 'none'
                    });
                    $this.pause();
                    $(document).on('mousemove.slideset touchmove.slideset', function(event){
                        swipe = event.type == 'mousemove' ? event : event.originalEvent.targetTouches[0];
                        var delta = start.x - swipe.clientX,
                            calc;
                        move.x = start.x - swipe.clientX;
                        move.y = start.y - swipe.clientY;
                        if (move.x != 0 && Math.abs(move.x) > Math.abs(move.y)) {
                            $this.content.addClass('move-started');
                            $this.content.find('li.active').each(function(ind, el){
                                calc = '(((100% - '+(margin * (count - 1))+'px) / -'+count+') - '+margin+'px) * '+(delta / width)+
                                    ' + ((100% - '+(margin * (count - 1))+'px) / '+count+')*'+ind+' + '+margin+'px*'+ind;
                                left = 'calc('+calc+')';
                                if ($this.isEdge) {
                                    left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (delta / width) + 
                                            ((pwidth - (margin * (count - 1))) / count) * ind + margin * ind)+'px';
                                }
                                this.style.left = left;
                            });
                            var pos = swipe.clientX > start.x ? $this.currentIndex - 1 : $this.currentIndex + 1;
                                position = 0,
                                flag = start.x > swipe.clientX;
                            if (pos > $this.childCount - 1) {
                                pos = 0;
                            } else if (pos < 0) {
                                pos = $this.childCount - 1;
                            }
                            for (var i = pos * count; i < pos * count + count; i++) {
                                if (!$this.childrens[i]) {
                                    continue;
                                }
                                var ind = $this.childrens[i].dataset.position = position++;
                                if (flag) {
                                    calc = '(((100% - '+(margin * (count - 1))+'px) / -'+count+') - '+margin+'px) * '+(delta / width)+
                                        ' + (100% + ((100% - '+(margin * (count - 1))+'px) / '+count+') * '+
                                        ind+' + '+margin+'px*'+(ind + 1)+')';
                                    left = 'calc('+calc+')';
                                    if ($this.isEdge) {
                                        left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (delta / width) + 
                                                (pwidth + ((pwidth - (margin * (count - 1))) / count) * ind + margin * (ind + 1)))+'px';
                                    }
                                    $this.childrens[i].style.visibility = 'visible';
                                    $this.childrens[i].style.left = left;
                                } else {
                                    calc = '(((100% - '+(margin * (count - 1))+'px) / -'+count+') - '+margin+'px) * '+(delta / width)+
                                        ' + ((((100% - '+(margin * (count - 1))+'px)/'+count+')*'+
                                        (count - ind)+' + '+margin+'px*'+(count - ind)+') * -1)';
                                    left = 'calc('+calc+')';
                                    if ($this.isEdge) {
                                        left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (delta / width) + 
                                        ((((pwidth - (margin * (count - 1))) / count) * (count - ind) + margin * (count - ind)) * -1))+'px';
                                    }
                                    $this.childrens[i].style.visibility = 'visible';
                                    $this.childrens[i].style.left = left;
                                }
                            }
                        }
                        if (event.type == 'mousemove') {
                            return false;
                        }
                    }).on('mouseup.slideset touchend.slideset', function(event){
                        $(document).off('mousemove.slideset mouseup.slideset touchmove.slideset touchend.slideset');
                        $this.cycle();
                        if (move.x != 0 && Math.abs(move.x) > Math.abs(move.y)) {
                            $this.content.removeClass('move-started');
                            $this.childrens.css({
                                'visibility': '',
                                'transition': ''
                            });
                            if (swipe.clientX > start.x) {
                                $this.prev();
                            } else if (swipe.clientX < start.x) {
                                $this.next();
                            }
                        }
                    });
                });
            }
            this.childrens.find('.ba-slideshow-img').on('mousedown.carousel', function(event){
                if ($this.content.hasClass('lightbox-enabled')) {
                    $g('body').trigger(event);
                    event.stopPropagation();
                }
            }).on('click.carousel', function(event){
                if ($this.content.hasClass('lightbox-enabled')) {
                    $this.modalImage = this;
                    var bgImage = $this.getImageURI();
                    if (bgImage) {
                        $this.pause();
                        $this.openModal(bgImage, this);
                    }
                }
            });
            this.parent.find('[data-ba-slide-to]').on('click.slideset', function(event){
                event.preventDefault();
                if ($this.allowSlide) {
                    $this.allowSlide = false;
                    var index = $(this).attr('data-ba-slide-to');
                    $this.dotsClicked = true;
                    $this.slideTo(index);
                }
            });
            this.parent.find('[data-slide]').on('click.slideset',  function(event){
                event.preventDefault();
                var action = $(this).attr('data-slide');
                $this[action]();
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
            var el = $(this.modalImage).closest('li.item'),
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
            this.clearAnimation();
            this.parent.off('mouseenter.slideset mouseleave.slideset');
            this.parent.find('[data-slide]').off('click.slideset');
            this.parent.find('[data-ba-slide-to]').off('click.slideset');
            this.content.off('mousedown.slideset touchstart.slideset');
            this.childrens.find('.ba-slideshow-img').off('mousedown.carousel click.carousel');
        },
        slideTo: function(pos, direction){
            pos = pos * 1;
            if (pos != this.currentIndex) {
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.clearAnimation();
                this.parent.find('.ba-slideset-dots .active').removeClass('active');
                this.lastActive = this.parent.find('li.active').css('visibility', 'visible').removeClass('active');
                this.childrens.css({
                    transition: ''
                });
                var position = 0,
                    length = 0,
                    count = this.options.count,
                    margin = this.options.gutter ? 30 : 0,
                    left = '',
                    that = this,
                    width = this.parent.width(),
                    flag = (this.dotsClicked && this.currentIndex == 0 && this.childCount - 1 == pos) ||
                        (this.currentIndex == this.childCount - 1 && pos == 0 && !this.dotsClicked) ||
                        (!(this.currentIndex == 0 && this.childCount - 1 == pos) && this.currentIndex < pos);
                if (!this.dotsClicked && direction == 'next') {
                    flag = true;
                } else if (!this.dotsClicked) {
                    flag = false;
                }
                for (var i = pos * this.options.count; i < pos * this.options.count + this.options.count; i++) {
                    if (this.childrens[i] && !this.childrens[i].style.left) {
                        length++
                    }
                }
                for (var i = pos * this.options.count; i < pos * this.options.count + this.options.count; i++) {
                    if (!this.childrens[i]) {
                        continue;
                    }
                    this.childrens[i].dataset.position = position++;
                    if (!this.childrens[i].style.left) {
                        var ind = this.childrens[i].dataset.position * 1;
                        if (flag) {
                            left = 'calc(100% + ((100% - '+(margin * (count - 1))+'px) / '+count+') * '+
                                ind+' + '+margin+'px*'+(ind + 1)+')';
                            if (this.isEdge) {
                                left = (width + ((width - (margin * (count - 1))) / count) * ind + margin * (ind + 1))+'px';
                            }
                            this.childrens[i].style.left = left;
                        } else {
                            left = 'calc((((100% - '+(margin * (count - 1))+'px)/'+count+')*'+
                                (count - ind)+' + '+margin+'px*'+(count - ind)+') * -1)';
                            if (this.isEdge) {
                                left = ((((width - (margin * (count - 1))) / count) * (count - ind) + margin * (count - ind)) * -1)+'px';
                            }
                            this.childrens[i].style.left = left;
                        }
                    }
                    $(this.childrens[i]).addClass('active');
                }
                setTimeout(function(){
                    that.setLeft();
                    that.setOutAnimation(flag);
                }, 100);
                this.dotsClicked = false;
                this.parent.find('.ba-slideset-dots [data-ba-slide-to="'+pos+'"]').addClass('active');
                setTimeout(function(){
                    that.clearAnimation();
                    that.allowSlide = true;
                }, 850);
                this.currentIndex = pos;
                if (this.flag) {
                    this.cycle();
                }
            } else {
                this.allowSlide = true;
                this.dotsClicked = false;
            }
        },
        setOutAnimation: function(flag){
            var count = this.options.count,
                margin = this.options.gutter ? 30 : 0,
                width = this.parent.width(),
                $this = this,
                left = '';
            this.lastActive.each(function(i){
                var position = this.dataset.position * 1;
                if (flag) {
                    left = 'calc((((100% - '+(margin * (count - 1))+'px)/'+count+')*'+
                        (count - position)+' + '+margin+'px*'+(count - position)+') * -1)';
                    this.classList.add('slideset-out-animation');
                    if ($this.isEdge) {
                        left = ((((width - (margin * (count - 1))) / count) * (count - position) + margin * (count - position)) * -1)+'px';
                    }
                    this.style.left = left;
                } else {
                    left = 'calc(100% + ((100% - '+(margin * (count - 1))+'px) / '+count+') * '+
                        position+' + '+margin+'px*'+(position + 1)+')';
                    if ($this.isEdge) {
                        left = (width + ((width - (margin * (count - 1))) / count) * position + margin * (position + 1))+'px';
                    }
                    this.classList.add('slideset-out-animation');
                    this.style.left = left;
                }
            });
        },
        clearAnimation: function(){
            this.parent.find('.slideset-out-animation').css({
                'left': '',
                'visibility': ''
            }).removeClass('slideset-out-animation');
        },
        setHeight: function(){
            let height = testimonialsInfo = 0;
            this.parent.find('li.item > div').each(function(){
                if (this.offsetHeight > height) {
                    height = this.offsetHeight;
                }
            });
            this.parent.find('.testimonials-info').each(function(){
                if (this.offsetHeight > testimonialsInfo) {
                    testimonialsInfo = this.offsetHeight;
                }
            });
            this.parent[0].style.setProperty('--testimonials-info-height', testimonialsInfo+'px');
            this.content.height(height);
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
        setLeft: function(){
            var count = this.options.count,
                margin = this.options.gutter ? 30 : 0,
                width = this.parent.width(),
                $this = this,
                left;
            this.content.find('li.active').each(function(ind, el){
                left = 'calc(((100% - '+(margin * (count - 1))+'px)/'+count+')*'+ind+' + '+margin+'px*'+ind+')';
                if ($this.isEdge) {
                    left = (((width - (margin * (count - 1))) / count) * ind + margin * ind)+'px';
                }
                this.style.left = left;
            });
        },
        slide: function (){
            var pos = this.currentIndex + 1;
            if (pos > this.childCount - 1) {
                pos = 0;
            }
            this.slideTo(pos, 'next');
        }
    }
    
    $.fn.slideset = function(option){
        return this.each(function(){
            var $this = $(this),
                data = $this.data('slideset'),
                options = $.extend({}, $.fn.slideset.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('slideset', (data = new slideset(this, options)));
            data.init();
        });
    }
    
    $.fn.slideset.defaults = {
        delay: 3000,
        autoplay: true,
        pause: false,
        mode: 'set',
        gutter: true,
        count: 3
    }
}(window.$g ? window.$g : window.jQuery);