/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    var columnResizer = function(element, options) {
            this.init = function(){
                var item = $(element),
                    $this = this;
                item.find('.ba-column-resizer').on('mousedown', function(event){
                    var isRtl = document.documentElement.dir == 'rtl',
                        leftEl = $(this)[isRtl ? 'next' : 'prev'](),
                        rightEl = $(this)[isRtl ? 'prev' : 'next'](),
                        style = getComputedStyle(rightEl[0]),
                        lstyle = getComputedStyle(leftEl[0]),
                        padding = style.paddingRight.replace('px', '') * 1 + style.paddingLeft.replace('px', '') * 1,
                        lpadding = lstyle.paddingRight.replace('px', '') * 1 + lstyle.paddingLeft.replace('px', '') * 1,
                        leftSpan = leftEl[0].dataset.span,
                        rightSpan = rightEl[0].dataset.span,
                        maxSpan = leftSpan * 1 + rightSpan * 1 - 1,
                        rowWidth = leftEl.parent().width(),
                        startX = event.pageX,
                        rightWidth = $this.getSpanWidth(rightSpan * 1, style, rowWidth),
                        leftWidth = $this.getSpanWidth(leftSpan * 1, style, rowWidth),
                        minResize = $this.getSpanWidth(1, style, rowWidth),
                        maxResize = $this.getSpanWidth(maxSpan, style, rowWidth);
                    leftEl.addClass('ba-column-resize');
                    rightEl.addClass('ba-column-resize');
                    padding += style.borderLeftWidth.replace('px', '') * 1 + style.borderRightWidth.replace('px', '') * 1;
                    lpadding += lstyle.borderLeftWidth.replace('px', '') * 1 + lstyle.borderRightWidth.replace('px', '') * 1;
                    $(document).on('mousemove.resize', function(event){
                        if (startX > event.pageX) {
                            rightWidth = rightWidth + (startX - event.pageX);
                            leftWidth = leftWidth - (startX - event.pageX);
                        } else {
                            rightWidth = rightWidth - (event.pageX - startX);
                            leftWidth = leftWidth + (event.pageX - startX);
                        }
                        if (rightWidth < minResize || leftWidth > maxResize) {
                            rightWidth = minResize;
                            leftWidth = maxResize;
                        }
                        if (leftWidth < minResize || rightWidth > maxResize) {
                            rightWidth = maxResize;
                            leftWidth = minResize;
                        }
                        rightEl.width(rightWidth - padding);
                        leftEl.width(leftWidth - lpadding);
                        var percent = rightWidth * 100 / rowWidth,
                            span;
                        percent = Math.round(percent * 100) / 100;
                        if (style.marginLeft.replace('px', '') * 1 == 0) {
                            span = $this.getSpanGutter(percent);
                        } else {
                            span = $this.getSpan(percent);
                        }
                        rightSpan = $this.updateEl(rightEl, span);
                        span = maxSpan - span + 1;
                        leftSpan = $this.updateEl(leftEl, span);
                        startX = event.pageX;
                    }).on('mouseup.resize contextmenu.resize', function(event){
                        $(document).off('mouseup.resize contextmenu.resize mousemove.resize');
                        rightEl[0].style.width = '';
                        leftEl[0].style.width = '';
                        leftEl.removeClass('ba-column-resize');
                        rightEl.removeClass('ba-column-resize');
                        options.change(rightEl, leftEl);
                    });
                    return false;
                });
            }
            this.updateEl = function(el, span){
                el.find('.column-info').text(top.app._('COLUMN')+' '+span);
                for (let i = 1; i <= 12; i++) {
                    el.removeClass('ba-col-'+i);
                }
                el.addClass('ba-col-'+span).attr('data-span', span);

                return span;
            }
            this.getSpanWidth = function(i, style, w){
                if (style.marginLeft.replace('px', '') * 1 > 0) {
                    switch(i) {
                        case 1 : return Math.floor(w * 6 / 100 - 2);
                            break;
                        case 2 : return Math.floor(w * 14.5 / 100 - 2);
                            break;
                        case 3 : return Math.floor(w * 23 / 100 - 2);
                            break;
                        case 4 : return Math.floor(w * 31 / 100 - 2);
                            break;
                        case 5 : return Math.floor(w * 40 / 100 - 2);
                            break;
                        case 6 : return Math.floor(w * 48 / 100 - 2);
                            break;
                        case 7 : return Math.floor(w * 57 / 100 - 2);
                            break;
                        case 8 : return Math.floor(w * 65 / 100 - 2);
                            break;
                        case 9 : return Math.floor(w * 74 / 100 - 2);
                            break;
                        case 10 : return Math.floor(w * 82.6 / 100 - 2);
                            break;
                        case 11 : return Math.floor(w * 91.1 / 100 - 2);
                            break;
                    }
                } else {
                    switch(i) {
                        case 1 : return Math.floor(w * 8 / 100 - 2);
                            break;
                        case 2 : return Math.floor(w * 16.2 / 100 - 2);
                            break;
                        case 3 : return Math.floor(w * 24.7 / 100 - 2);
                            break;
                        case 4 : return Math.floor(w * 32.7 / 100 - 2);
                            break;
                        case 5 : return Math.floor(w * 40.7 / 100 - 2);
                            break;
                        case 6 : return Math.floor(w * 49.7 / 100 - 2);
                            break;
                        case 7 : return Math.floor(w * 57.7 / 100 - 2);
                            break;
                        case 8 : return Math.floor(w * 65.7 / 100 - 2);
                            break;
                        case 9 : return Math.floor(w * 74.7 / 100 - 2);
                            break;
                        case 10 : return Math.floor(w * 83 / 100 - 2);
                            break;
                        case 11 : return Math.floor(w * 91.3 / 100 - 2);
                            break;
                    }
                }
            }
            this.getSpan = function(i){
                if (i < 14.8) {
                    if (i - 6 < 14.8 - i) {
                        return 1;
                    } else {
                        return 2;
                    }
                } else if (i >= 14.8 && i <= 23.4) {
                    if (i - 14.8 < 23.4 - i) {
                        return 2;
                    } else {
                        return 3;
                    }
                } else if (i >= 23.4 && i <= 31.91) {
                    if (i - 23.4 < 31.91 - i) {
                        return 3;
                    } else {
                        return 4;
                    }
                } else if (i >= 31.91 && i <= 40.42) {
                    if (i - 31.91 < 40.42 - i) {
                        return 4;
                    } else {
                        return 5;
                    }
                } else if (i >= 40.42 && i <= 48.93) {
                    if (i - 40.42 < 48.93 - i) {
                        return 5;
                    } else {
                        return 6;
                    }
                } else if (i >= 48.93 && i <= 57.44) {
                    if (i - 48.93 < 57.44 - i) {
                        return 6;
                    } else {
                        return 7;
                    }
                } else if (i >= 57.44 && i <= 65.95) {
                    if (i - 57.44 < 65.95 - i) {
                        return 7;
                    } else {
                        return 8;
                    }
                } else if (i >= 65.95 && i <= 74.46) {
                    if (i - 65.95 < 74.46 - i) {
                        return 8;
                    } else {
                        return 9;
                    }
                } else if (i >= 74.46 && i <= 82.9) {
                    return 10;
                } else {
                    return 11;
                }
            }
            this.getSpanGutter = function(i){
                if (i < 16.6) {
                    if (i - 8.3 < 16.6 - i) {
                        return 1;
                    } else {
                        return 2;
                    }
                } else if (i >= 16.6 && i <= 25) {
                    if (i - 16.6 < 25 - i) {
                        return 2;
                    } else {
                        return 3;
                    }
                } else if (i >= 25 && i <= 33.3) {
                    if (i - 25 < 33.3 - i) {
                        return 3;
                    } else {
                        return 4;
                    }
                } else if (i >= 33.3 && i <= 41.666) {
                    if (i - 33.3 < 41.666 - i) {
                        return 4;
                    } else {
                        return 5;
                    }
                } else if (i >= 41.666 && i <= 50) {
                    if (i - 41.666 < 50 - i) {
                        return 5;
                    } else {
                        return 6;
                    }
                } else if (i >= 50 && i <= 58.334) {
                    if (i - 50 < 58.334 - i) {
                        return 6;
                    } else {
                        return 7;
                    }
                } else if (i >= 58.334 && i <= 66.7) {
                    if (i - 58.334 < 66.7 - i) {
                        return 7;
                    } else {
                        return 8;
                    }
                } else if (i >= 66.7 && i <= 75) {
                    if (i - 66.7 < 75 - i) {
                        return 8;
                    } else {
                        return 9;
                    }
                } else if (i >= 75 && i <= 83.3) {
                    return 10;
                } else {
                    return 11;
                }
            }
        }

    $.fn.columnResizer = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('columnResizer'),
                options = $.extend({}, $.fn.columnResizer.defaults, typeof option == 'object' && option);
            if (!data) {
                $this.data('columnResizer', (data = new columnResizer(this, options)));
            }
            data.init();
        });
    }

    $.fn.columnResizer.defaults = {
        change : function(){
            
        }
    }
    
    $.fn.columnResizer.Constructor = columnResizer;
}(window.$g ? window.$g : window.jQuery);