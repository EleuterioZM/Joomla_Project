/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    
    var gridboxCarousel = function (element, options) {
        this.parent = $(element);
        this.modal = null;
        this.modalImage = null;
        this.options = options;
        this.options.count *= 1;
        if (this.parent.hasClass('slideshow-type')) {
            this.options.count = 1;
        }
        this.currentIndex = 0;
        this.content = this.parent.find('.slideshow-content');
        this.childrens = this.content.find('li.item:not(.ba-unpublished-html-item)');
        this.firstOrder = this.childrens.first();
        this.flag = true;
        this.allowSlide = true;
        this.outAnimation = {}
        this.isEdge = /Edge/.test(navigator.userAgent) || /Trident.*rv\:11\./.test(navigator.userAgent);
    }
    
    gridboxCarousel.prototype = {
        setArrows: function(){
            let length = this.childrens.length,
                count = this.options.count,
                nav = this.parent.find('.ba-slideset-nav'),
                navs = nav.find('a');
            if (count >= length && navs.length != 0) {
                navs.remove();
            } else if (count < length && navs.length == 0) {
                let str = '<a class="ba-btn-transition slideset-btn-prev ba-icons ba-icon-chevron-left"'+
                    ' data-slide="prev"></a><a class="ba-btn-transition slideset-btn-next ba-icons'+
                    ' ba-icon-chevron-right" data-slide="next"></a>';
                nav.append(str)
            }
        },
        init: function(){
            var $this = this,
                count = this.options.count,
                margin = this.options.gutter ? 30 : 0;
            this.parent.find('.ba-slideset-dots').remove();
            if (themeData.page.view != 'gridbox') {
                this.setArrows();
            }
            if (this.options.pause) {
                this.parent.on('mouseenter.slideset', $.proxy(this.pause, this)).on('mouseleave.slideset', $.proxy(this.cycle, this));
            }
            this.childrens.css({
                transition: '',
                visibility: '',
                left: ''
            });
            this.parent[0].style.setProperty('--carousel-caption-height', '');
            this.parent.find('li.active').removeClass('active');
            for (var i = 0; i < count; i++) {
                $(this.childrens[i]).addClass('active').attr('data-position', i);
            }
            this.content.css('left', '');
            this.content.removeClass('move-started');
            this.childrens.css('order', '');
            this.clearAnimation();
            this.setLeft();
            this.cycle();
            this.parent.addClass('slideset-loaded');
            this.setHeight();
            this.parent[this.childrens.length > count ? 'addClass' : 'removeClass']('enabled-carousel-sliding');
            if (this.childrens.length > count && !this.parent.hasClass('slideshow-type')) {
                this.content.on('mousedown.carousel touchstart.carousel', function(event){
                    var width = $(window).width(),
                        pwidth = $this.content.width(),
                        move = {
                            x: 0,
                            y: 0
                        },
                        swipe = event.type == 'mousedown' ? event : event.originalEvent.targetTouches[0],
                        start = {
                            x: swipe.clientX,
                            y: swipe.clientY
                        },
                        type = event.type == 'mousedown' ? 'mouse' : 'touch',
                        left;

                    $this.childrens.css({
                        transition: 'none'
                    });
                    $this.pause();
                    $(document).on('mousemove.carousel touchmove.carousel', function(event){
                        swipe = event.type == 'mousemove' ? event : event.originalEvent.targetTouches[0];
                        var delta = start.x - swipe.clientX,
                            calc;
                        move.x = start.x - swipe.clientX;
                        move.y = start.y - swipe.clientY;
                        if (move.x != 0 && Math.abs(move.x) > Math.abs(move.y)) {
                            $this.content.addClass('move-started');
                            var ind = 0;
                            $this.content.find('li.active').each(function(){
                                ind = this.dataset.position * 1;
                                calc = '(((100% - '+(margin * (count - 1))+'px) / -'+count+') - '+margin+'px) * '+(delta / width)+
                                    ' + ((100% - '+(margin * (count - 1))+'px) / '+count+')*'+ind+' + '+margin+'px*'+ind;
                                left = 'calc('+calc+')';
                                if ($this.isEdge) {
                                    left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (delta / width) + 
                                            ((pwidth - (margin * (count - 1))) / count) * ind + margin * ind)+'px';
                                }
                                this.style.left = left;
                            });
                            var actionNext = start.x > swipe.clientX;
                                pos = 0;
                            if (actionNext) {
                                pos = $this.currentIndex == $this.childrens.length - 1 ? 0 : $this.currentIndex + 1;
                            } else {
                                pos = $this.currentIndex == 0 ? $this.childrens.length - 1 : $this.currentIndex - 1;
                            }
                            if ($this.childrens.length - pos < count) {
                                for (var i = pos; i < $this.childrens.length; i++) {
                                    if (!$this.childrens[i].classList.contains('active')) {
                                        $this.setMoveStartPosition(actionNext, delta, width, pwidth, $this.childrens[i]);
                                    }
                                }
                                for (var i = 0; i < count - ($this.childrens.length - pos); i++) {
                                    if (!$this.childrens[i].classList.contains('active')) {
                                        $this.setMoveStartPosition(actionNext, delta, width, pwidth, $this.childrens[i]);
                                    }
                                }
                            } else {
                                for (var i = pos; i < pos + count; i++) {
                                    if (!$this.childrens[i].classList.contains('active')) {
                                        $this.setMoveStartPosition(actionNext, delta, width, pwidth, $this.childrens[i]);
                                    }   
                                }
                            }
                        }
                        if (event.type == 'mousemove') {
                            return false;
                        }
                    }).on('mouseup.carousel touchend.carousel', function(event){
                        $(document).off('mousemove.carousel mouseup.carousel touchmove.carousel touchend.carousel');
                        $this.cycle();
                        if (move.x != 0 && Math.abs(move.x) > Math.abs(move.y)) {
                            $this.content.removeClass('move-started');
                            $this.childrens.css({
                                'visibility': '',
                                'transition': ''
                            });
                            if (swipe.clientX > start.x) {
                                $this.slideAction('prev');
                            } else if (swipe.clientX < start.x) {
                                $this.slideAction('next');
                            }
                        }
                    });
                });
            } else if (this.parent.hasClass('slideshow-type')) {
                this.firstOrder.addClass('active');
                this.parent.removeClass('first-load-slideshow');
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
            this.parent.find('[data-slide]').on('click.slideset',  function(event){
                event.preventDefault();
                $this.slideAction(this.dataset.slide);
            });
        },
        setMoveStartPosition:function(actionNext, delta, width, pwidth, child){
            var calc, left,
                count = this.options.count,
                margin = this.options.gutter ? 30 : 0;
            if (actionNext) {
                calc = '(((100% - '+(margin * (count - 1))+'px) / -'+count+') - '+margin+'px) * '+(delta / width)+
                    ' + ((100% - '+(margin * (count - 1))+'px) / '+count+')*'+count+' + '+margin+'px*'+count;
                left = 'calc('+calc+')';
                if (this.isEdge) {
                    left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (delta / width) + 
                            ((pwidth - (margin * (count - 1))) / count) * count + margin * count)+'px';
                }
            } else {
                calc = '((((100% - '+(margin * (count - 1))+'px)/-'+count+') - '+margin+
                    'px) * (1 + '+(delta / width)+'))';
                left = 'calc('+calc+')';
                if (this.isEdge) {
                    left = ((((pwidth - (margin * (count - 1))) / -count) - margin) * (1 + delta / width))+'px';
                }
            }
            child.style.left = left;
            child.style.visibility = 'visible'
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
                left = (wWidth - imgWidth) / 2+ comp.borderLeftWidth.replace('px', '') * 1;
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
        cycle: function(){
            if (this.options.autoplay == 1) {
                this.flag = true;
                if (this.interval) {
                    clearInterval(this.interval);
                }
                let $this = this;
                this.interval = setInterval(function(){
                    $this.slideAction('next');
                }, this.options.delay);

                return this;
            }
        },
        pause: function(){
            if (this.interval) {
                clearInterval(this.interval);
            }
            this.interval = null;
            this.flag = false;
        },
        delete: function(){
            clearInterval(this.interval);
            this.interval = null;
            this.parent.off('mouseenter.slideset mouseleave.slideset');
            this.parent.find('[data-slide]').off('click.slideset');
            this.parent.find('.ba-next, .ba-prev, .ba-left, .ba-right').removeClass('ba-next ba-prev ba-left ba-right');
            this.content.off('mousedown.carousel touchstart.carousel');
            this.childrens.find('.ba-slideshow-img').off('mousedown.carousel click.carousel');
        },
        setOutAnimation: function(action){
            var count = this.options.count,
                margin = this.options.gutter ? 30 : 0,
                width = this.content.width(),
                $this = this,
                outItem = null,
                parentRect = this.content[0].getBoundingClientRect(),
                left = '';
            this.lastActive.each(function(i){
                if (!this.classList.contains('active')) {
                    outItem = this;
                    if (action == 'next') {
                        left = 'calc(((100% - '+(margin * (count - 1))+'px)/-'+count+') - '+margin+'px)';
                        if ($this.isEdge) {
                            left = (((width - (margin * (count - 1))) / -count) - margin)+'px';
                        }
                    } else {
                        left = 'calc(100% + '+margin+'px)';
                        if ($this.isEdge) {
                            left = (width + margin)+'px';
                        }
                    }
                    this.style.left = left;
                    this.classList.add('slideset-out-animation');
                }
            });
            for (var ind in this.outAnimation) {
                if (this.outAnimation[ind].classList.contains('active')) {
                    continue;
                }
                if (action == 'next') {
                    left = 'calc((((100% - '+(margin * (count - 1))+'px)/-'+count+') - '+margin+'px) * 2)';
                    if ($this.isEdge) {
                        left = ((((width - (margin * (count - 1))) / -count) - margin) * 2)+'px';
                    }
                } else {
                    left = 'calc(100% + '+margin+'px + (((100% - '+(margin * (count - 1))+'px)/'+count+') + '+margin+'px))';
                    if ($this.isEdge) {
                        left = (width + margin + (((width - (margin * (count - 1))) / count) + margin))+'px';
                    }
                }
                this.outAnimation[ind].style.left = left;
            }
            this.outAnimation[+new Date()] = outItem;
        },
        setStartPosition:function(child, action, nextPosition, prevPosition){
            if (child.classList.contains('slideset-out-animation')) {
                child.style.transition = 'none';
                child.style.left = '';
                child.style.visibility = '';
                child.classList.remove('slideset-out-animation');
            }
            if (!child.style.left) {
                var count = this.options.count,
                    width = this.content.width(),
                    margin = this.options.gutter ? 30 : 0;
                if (action == 'next') {
                    left = (nextPosition + (((width - (margin * (count - 1))) / count) + margin))+'px';
                } else {
                    left = (prevPosition - (((width - (margin * (count - 1))) / count) + margin))+'px';
                }
                child.style.left = left;
            }
        },
        slide: function(action){
            var $this = this;
            if (this.childrens.length > this.options.count && !this.parent.hasClass('slideshow-type')) {
                if (this.interval) {
                    clearInterval(this.interval);
                }
                this.childrens.css({
                    transition: ''
                });
                if (action == 'next') {
                    this.currentIndex = this.currentIndex == this.childrens.length - 1 ? 0 : ++this.currentIndex;
                } else {
                    this.currentIndex = this.currentIndex == 0 ? this.childrens.length - 1 : --this.currentIndex;
                }
                this.lastActive = this.parent.find('li.active').css('visibility', 'visible').removeClass('active');
                var ind = 0,
                    count = this.options.count,
                    margin = this.options.gutter ? 30 : 0,
                    nextRect, prevRect,
                    nextPosition = prevPosition = 0,
                    parentRect = this.content[0].getBoundingClientRect();
                this.lastActive.each(function(){
                    if (this.dataset.position == count - 1) {
                        nextRect = this.getBoundingClientRect();
                    }
                    if (this.dataset.position == 0) {
                        prevRect = this.getBoundingClientRect();
                    }
                });
                nextPosition = nextRect.left - parentRect.left;
                prevPosition = prevRect.left - parentRect.left;
                if (this.childrens.length - this.currentIndex < this.options.count) {
                    for (var i = this.currentIndex; i < this.childrens.length; i++) {
                        this.setStartPosition(this.childrens[i], action, nextPosition, prevPosition);
                        $(this.childrens[i]).addClass('active').attr('data-position', ind++);
                    }
                    for (var i = 0; i < this.options.count - (this.childrens.length - this.currentIndex); i++) {
                        this.setStartPosition(this.childrens[i], action, nextPosition, prevPosition);
                        $(this.childrens[i]).addClass('active').attr('data-position', ind++);
                    }
                } else {
                    for (var i = this.currentIndex; i < this.currentIndex + this.options.count; i++) {
                        this.setStartPosition(this.childrens[i], action, nextPosition, prevPosition);
                        $(this.childrens[i]).addClass('active').attr('data-position', ind++);
                    }
                }
                setTimeout(function(){
                    $this.setLeft();
                    $this.setOutAnimation(action);
                    setTimeout(function(){
                        $this.clearAnimation();
                    }, 850);
                    clearTimeout($this.slideDelay);
                    $this.slideDelay = setTimeout(function(){
                        $this.allowSlide = true;
                    }, 250);
                }, 1);
                if (this.flag) {
                    this.cycle();
                }
            } else if (this.parent.hasClass('slideshow-type')) {
                var active = this.parent.find('.item.active'),
                    el = this[action](this.firstOrder),
                    fallback  = action == 'next' ? 'first' : 'last';
                this.parent.find('.select-animation').removeClass('select-animation');
                if (el.hasClass('active')) {
                    return;
                }
                this.parent.find('.ba-next, .ba-prev, .ba-left, .ba-right').removeClass('ba-next ba-prev ba-left ba-right');
                if (fallback == 'first') {
                    active.addClass('ba-next').addClass('burns-out');
                    el.addClass('ba-right');
                } else {
                    active.addClass('ba-prev').addClass('burns-out');
                    el.addClass('ba-left');
                }
                setTimeout(function(){
                    active.removeClass('burns-out');
                }, 600);
                active.removeClass('active');
                this.firstOrder = el.addClass('active');
                this.allowSlide = true;
            }
        },
        next: function(el){
            if (el.next().length > 0) {
                return el.next();
            } else {
                return this.childrens.first();
            }
        },
        prev: function(el){
            if (el.prev().length > 0) {
                return el.prev();
            } else {
                return this.childrens.last();
            }
        },
        clearAnimation: function(){
            let date = +new Date(),
                ind = null;
            for (ind in this.outAnimation) {
                if (date - ind >= 850) {
                    if (this.outAnimation[ind].classList.contains('slideset-out-animation')) {
                        this.outAnimation[ind].style.left = '';
                        this.outAnimation[ind].style.visibility = '';
                        this.outAnimation[ind].classList.remove('slideset-out-animation');
                    }
                    delete(this.outAnimation[ind]);
                }
            }
            if (!ind) {
                this.parent.find('.slideset-out-animation').css({
                    'left': '',
                    'visibility': ''
                }).removeClass('slideset-out-animation');
            }
        },
        setHeight: function(){
            if (this.parent.hasClass('slideshow-type')) {
                this.content.css({
                    'height': ''
                });
            } else {
                var height = caption = 0;
                this.parent.find('li.item').each(function(){
                    if (this.offsetHeight > height) {
                        height = this.offsetHeight;
                    }
                });
                this.parent.find('.ba-slideshow-caption').each(function(){
                    if (this.offsetHeight > caption) {
                        caption = this.offsetHeight;
                    }
                });
                this.parent[0].style.setProperty('--carousel-caption-height', caption+'px');
                this.content.height(height);
            }
        },
        setLeft: function(){
            var count = this.options.count,
                margin = this.options.gutter ? 30 : 0,
                width = this.content.width(),
                $this = this,
                left;
            this.content.find('li.active').each(function(){
                ind = this.dataset.position * 1;
                left = 'calc(((100% - '+(margin * (count - 1))+'px)/'+count+')*'+ind+' + '+margin+'px*'+ind+')';
                if ($this.isEdge) {
                    left = (((width - (margin * (count - 1))) / count) * ind + margin * ind)+'px';
                }
                this.style.left = left;
                this.style.transition = '';
            });
        },
        slideAction: function(action){
            if (this.allowSlide) {
                this.allowSlide = false;
                this.slide(action);
            }
        }
    }
    
    $.fn.gridboxCarousel = function(option){
        return this.each(function(){
            var $this = $(this),
                data = $this.data('gridboxCarousel'),
                options = $.extend({}, $.fn.gridboxCarousel.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('gridboxCarousel', (data = new gridboxCarousel(this, options)));
            data.init();
        });
    }
    
    $.fn.gridboxCarousel.defaults = {
        delay: 3000,
        autoplay: true,
        pause: false,
        mode: 'set',
        gutter: true,
        count: 3
    }
}(window.$g ? window.$g : window.jQuery);