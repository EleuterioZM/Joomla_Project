/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    var dragEl,
        sortGroups = {},
        sortable = function (element, options) {
            this.delete = function(){
                var item = $(element);
                item.off('mousedown.sortable')
            }
            this.getDefaultPosition = function(dragEl){
                var rectangle = dragEl.getBoundingClientRect(),
                    comp = getComputedStyle(dragEl),
                    obj = {
                        width: rectangle.right - rectangle.left,
                        display: 'block',
                        left: rectangle.left,
                        top: rectangle.top - comp.marginTop.replace('px', '') * 1
                    }

                return obj;
            }
            this.init = function(){
                var $this = this,
                    item = $(element),
                    div = document.createElement('div');
                if (!sortGroups[options.group]) {
                    sortGroups[options.group] = [];
                }
                sortGroups[options.group].unshift(item);
                item.on('mousedown.sortable', options.handle, function(event){
                    if ((this.closest('.ba-item-in-positioning') && !this.closest('.ba-hotspot-popover'))
                        || event.button != 0) {
                        return false;
                    }
                    item.closest('.ba-wrapper').addClass('sortable-parent-node');
                    item.closest('.ba-item-flipbox').addClass('sortable-started');
                    item.closest('li.megamenu-item').addClass('megamenu-editing')
                        .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                        .closest('.ba-wrapper').addClass('section-with-megamenu')
                        .closest('body').addClass('body-megamenu-editing');
                    if (item.closest('.ba-hotspot-popover').length > 0) {
                        item.closest('.ba-hotspot-popover').closest('.ba-item').find('.open-popover-item').trigger('mousedown');
                    }
                    options.start(item[0]);
                    dragEl = $(this).closest(element.children)[0];
                    options.helper[0].className = dragEl.className+' sortable-helper';
                    $(dragEl).addClass('element-in-sorting');
                    var comp = null,
                        deltaY = top != window ? top.document.querySelector('.editor-iframe').getBoundingClientRect().top : 0,
                        deltaX = top != window ? (top.innerWidth - window.innerWidth) / 2 : 0,
                        target = null,
                        method = null,
                        obj = $this.getDefaultPosition(dragEl);
                    if ((options.group == 'column' || options.group == 'lightbox-column')
                        && dragEl.classList.contains('ba-row-wrapper')) {
                        options.helper.addClass('nested-row-helper');
                    }
                    options.helper.css({
                        top : (event.clientY + deltaY)+'px',
                        display: 'block',
                        left : (event.clientX + deltaX)+'px',
                    });
                    options.placeholder.css(obj);
                    options.backdrop.css({
                        display: 'block'
                    }).closest('body').addClass('body-sortable-started');
                    window.top.document.body.classList.add(options.group+'-sortable-started');
                    if (options.group == 'responsive-menu') {
                        $(dragEl).closest('.visible-menu').addClass('menu-sortable-started');
                    }
                    item.removeClass('active-item');
                    $(document).on('mousemove.sortable', function(event){
                        options.helper.css({
                            'top' : (event.clientY + deltaY)+'px',
                            'left' : (event.clientX + deltaX)+'px',
                        });
                        var array = sortGroups[options.group];
                        target = null;
                        for (var i = 0; i < array.length; i++) {
                            if ((array[i].closest('.ba-item-content-slider').length > 0 && array[i].parent().hasClass('item')
                                 && dragEl.classList.contains('ba-item'))) {
                                continue;
                            }
                            if (options.group == 'column' && array[i].closest('.ba-flipbox-backside').length > 0
                                && !array[i].closest('.ba-item-flipbox').hasClass('backside-fliped')) {
                                continue;
                            } else if (options.group == 'column' && array[i].closest('.ba-flipbox-frontside').length > 0
                                && array[i].closest('.ba-item-flipbox').hasClass('backside-fliped')) {
                                continue;
                            }
                            var rect = null;
                            array[i].find(options.selector).each(function(){
                                rect = this.getBoundingClientRect();
                                comp = getComputedStyle(this);
                                var object = {
                                        top : rect.top - comp.marginTop.replace('px', '') * 1,
                                        bottom : rect.bottom + comp.marginBottom.replace('px', '') * 1,
                                        left : rect.left,
                                        right: rect.right
                                    };
                                if (this.classList.contains('blog-post-editor-options-group')) {
                                    object.bottom = rect.bottom;
                                }
                                rect = object;
                                if (rect.top < event.clientY && rect.bottom > event.clientY &&
                                    rect.left < event.clientX && event.clientX < rect.right) {
                                    target = this;
                                    return false;
                                }
                            });
                            if (dragEl.classList.contains('ba-row-wrapper') && array[i].hasClass('ba-grid-column')
                                && array[i].closest('.ba-row-wrapper').parent().hasClass('ba-grid-column')) {
                                continue;
                            }
                            if (target) {
                                var next = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5;
                                if (next) {
                                    options.placeholder.css({
                                        width: rect.right - rect.left,
                                        left: rect.left,
                                        top: rect.bottom
                                    });
                                    method = 'after';
                                } else {
                                    options.placeholder.css({
                                        width: rect.right - rect.left,
                                        left: rect.left,
                                        top: rect.top
                                    });
                                    method = 'before';
                                }
                            } else {
                                var rect = array[i][0].getBoundingClientRect(),
                                    length = $(array[i][0]).find(options.selector).not(dragEl).length;
                                if (rect.top < event.clientY && rect.bottom > event.clientY &&
                                    rect.left < event.clientX && event.clientX < rect.right && length == 0) {
                                    target = array[i][0];
                                }
                                if (target && !target.classList.contains('ba-grid-column')
                                    && !target.classList.contains('ba-hotspot-popover')) {
                                    var targetW = rect.right - rect.left,
                                        targetL = rect.left,
                                        targetT = rect.bottom
                                    if (target.classList.contains('ba-fields-group')) {
                                        comp = getComputedStyle(target);
                                        targetT = rect.top + comp.paddingTop.replace('px', '') * 1;
                                        targetW -= (comp.paddingLeft.replace('px', '') * 1 + comp.paddingRight.replace('px', '') * 1);
                                        targetL += comp.paddingLeft.replace('px', '') * 1;
                                    }
                                    method = 'append';
                                    options.placeholder.css({
                                        width: targetW,
                                        left: targetL,
                                        top: targetT
                                    });
                                } else if (target && target.classList.contains('ba-hotspot-popover')) {
                                    target = $(target).find('> .add-new-item')[0];
                                    method = 'before';
                                    options.placeholder.css({
                                        width: rect.right - rect.left,
                                        left: rect.left,
                                        top: rect.top
                                    });
                                } else if (target) {
                                    target = $(target).find('> .empty-item')[0];
                                    method = 'before';
                                    options.placeholder.css({
                                        width: rect.right - rect.left,
                                        left: rect.left,
                                        top: rect.top
                                    });
                                }
                            }
                            if (target) {
                                $('.placeholder-parent').removeClass('placeholder-parent');
                                $(target.parentNode).closest('.ba-item-flipbox').addClass('placeholder-parent');
                                break;
                            }
                        }
                        if (!target) {
                            options.placeholder.css($this.getDefaultPosition(dragEl))
                        }
                        return false;
                    }).off('mouseleave.sortable').on('mouseleave.sortable', function(){
                        $(document).trigger('mouseup.sortable');
                    }).off('mouseup.sortable').on('mouseup.sortable', function(){
                        var classList = dragEl.classList;
                        if (target && (((classList.contains('ba-item-post-intro') || classList.contains('ba-item-blog-content')
                                || classList.contains('ba-item-blog-posts') || classList.contains('ba-item-error-message')
                                || classList.contains('ba-item-search-result-headline') || classList.contains('ba-item-search-result')
                                || classList.contains('ba-item-store-search-result')
                                || classList.contains('ba-item-checkout-form') || classList.contains('ba-item-checkout-order-form')
                                || $(dragEl).find('> .ba-row').hasClass('row-with-intro-items'))
                            && ($(target).closest('header').length > 0 || $(target).closest('footer').length > 0))
                            || $(target).parent().closest('.ba-item-blog-content').length > 0)) {
                            
                        } else if (target) {
                            $(target)[method](dragEl);
                        }
                        $(dragEl).removeClass('element-in-sorting').find('> .ba-edit-item').css({
                            top: '',
                            left: ''
                        });
                        target = null;
                        options.helper.css('display', 'none');
                        options.placeholder.css('display', 'none');
                        options.backdrop.css('display', 'none').closest('body').removeClass('body-sortable-started');
                        window.top.document.body.classList.remove(options.group+'-sortable-started');
                        $(document).off('mousemove.sortable mouseup.sortable mouseleave.sortable');
                        $('.menu-sortable-started').removeClass('menu-sortable-started');
                        $('.sortable-parent-node').removeClass('sortable-parent-node');
                        $('.sortable-started').removeClass('sortable-started');
                        $('.placeholder-parent').removeClass('placeholder-parent');
                        window.top.app.setRowWithIntro();
                        setTimeout(() => {
                            $('li.megamenu-item.megamenu-editing').removeClass('megamenu-editing')
                                .closest('.ba-row-wrapper').removeClass('row-with-megamenu')
                                .closest('.ba-wrapper').removeClass('section-with-megamenu')
                                .closest('body').removeClass('body-megamenu-editing');
                        }, 100);
                        $(window).trigger("resize");
                        options.change(dragEl);
                    });
                    return false;
                });
            }
        }

    $.fn.sortable = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('sortable'),
                options = $.extend({}, $.fn.sortable.defaults, typeof option == 'object' && option);
            if (data) {
                data.delete();
                $this.removeData();
            }
            $this.data('sortable', (data = new sortable(this, options)));
            data.init();
        });
    }

    $.fn.sortable.defaults = {
        'selector' : '> *',
        change : function(){
            
        },
        start : function(){

        }
    }

    if (!top.document.querySelector('.sortable-helper')) {
        top.document.body.insertAdjacentHTML('beforeEnd', '<div class="sortable-helper"><i class="zmdi zmdi-apps"></i></div>');
    }
    if (!document.querySelector('.sortable-placeholder')) {
        document.body.insertAdjacentHTML('beforeEnd', '<div class="sortable-placeholder"><div></div></div>');
    }
    if (!document.querySelector('.sortable-backdrop')) {
        document.body.insertAdjacentHTML('beforeEnd', '<div class="sortable-backdrop"><div></div></div>');
    }
    $.fn.sortable.defaults.helper = top.$g('.sortable-helper');
    $.fn.sortable.defaults.placeholder = $('.sortable-placeholder');
    $.fn.sortable.defaults.backdrop = $('.sortable-backdrop');
    $.fn.sortable.Constructor = sortable;
}(window.$g ? window.$g : window.jQuery);