document.addEventListener('DOMContentLoaded', function(){
    !function ($) {
        'use strict';

        $.minicolors = {
            defaults: {
                animationSpeed: 50,
                animationEasing: 'swing',
                change: null,
                changeDelay: 0,
                control: 'hue',
                dataUris: true,
                defaultValue: '',
                format: 'hex',
                hide: null,
                hideSpeed: 100,
                inline: false,
                keywords: '',
                letterCase: 'lowercase',
                opacity: false,
                position: 'bottom left',
                show: null,
                showSpeed: 100,
                theme: 'default',
                swatches: []
            }
        };

        $.extend($.fn, {
            minicolors: function(method, data) {
                switch(method) {
                    case 'opacity':
                        if( data === undefined ) {
                            return $(this).attr('data-opacity');
                        } else {
                            $(this).each( function() {
                                updateFromInput($(this).attr('data-opacity', data), false, true);
                            });
                        }
                        return $(this);
                    case 'rgbObject':
                        return rgbObject($(this), method === 'rgbaObject');
                    case 'rgbString':
                    case 'rgbaString':
                        return rgbString($(this), method === 'rgbaString');
                    case 'value':
                        if( data === undefined ) {
                            return $(this).val();
                        } else {
                            $(this).each( function() {
                                var flag = true;
                                if ( typeof(data) === 'object' ) {
                                    if(typeof(data.opacity) != 'undefined') {
                                        $(this).attr('data-opacity', keepWithin(data.opacity, 0, 1));
                                    }
                                    if( data.color ) {
                                        $(this).val(data.color);
                                    }
                                    if (typeof(data.update) != 'undefined') {
                                        flag = data.update;
                                    }
                                } else {
                                    $(this).val(data);
                                }
                                updateFromInput($(this), false, flag);
                            });
                        }
                        return $(this);
                    default:
                        if( method !== 'create' ) data = method;
                        $(this).each( function() {
                            init($(this), data);
                        });
                        return $(this);

                }

            }
        });

        function init(input, settings) {
            var minicolors = $('<div class="minicolors" />'),
                defaults = $.minicolors.defaults,
                size,
                swatches,
                swatch,
                panel,
                i;
            if( input.data('minicolors-initialized') ) return;
            settings = $.extend(true, {}, defaults, settings);
            minicolors
                .addClass('minicolors-theme-' + settings.theme)
                .toggleClass('minicolors-with-opacity', settings.opacity)
                .toggleClass('minicolors-no-data-uris', settings.dataUris !== true);
            if (settings.position !== undefined) {
                $.each(settings.position.split(' '), function() {
                    minicolors.addClass('minicolors-position-' + this);
                });
            }
            if (settings.format === 'rgb') {
                size = settings.opacity ? '25' : '20';
            } else {
                size = settings.keywords ? '11' : '7';
            }
            input
                .addClass('minicolors-input')
                .data('minicolors-initialized', false)
                .data('minicolors-settings', settings)
                .prop('size', size)
                .wrap(minicolors)
                .after(
                    '<div class="minicolors-panel minicolors-slider-' + settings.control + '">' +
                        '<div class="minicolors-slider minicolors-sprite">' +
                            '<div class="minicolors-picker"></div>' +
                        '</div>' +
                        '<div class="minicolors-opacity-slider minicolors-sprite">' +
                            '<div class="minicolors-picker"></div>' +
                        '</div>' +
                        '<div class="minicolors-grid minicolors-sprite">' +
                            '<div class="minicolors-grid-inner"></div>' +
                            '<div class="minicolors-picker"><div></div></div>' +
                        '</div>' +
                    '</div>'
                );
            if (!settings.inline ) {
                input.after('<span class="minicolors-swatch minicolors-sprite minicolors-input-swatch">'+
                    '<span class="minicolors-swatch-color"></span></span>');
                input.next('.minicolors-input-swatch').on('click', function(event) {
                    event.preventDefault();
                    input.focus();
                });
            }
            panel = input.parent().find('.minicolors-panel');
            panel.on('selectstart', function() { return false; }).end();
            if (settings.swatches && settings.swatches.length !== 0) {
                if (settings.swatches.length > 7) {
                    settings.swatches.length = 7;
                }
                panel.addClass('minicolors-with-swatches');
                swatches = $('<ul class="minicolors-swatches"></ul>').appendTo(panel);
                for (i = 0; i < settings.swatches.length; ++i) {
                    swatch = settings.swatches[i];
                    swatch = isRgb(swatch) ? parseRgb(swatch, true) : hex2rgb(parseHex(swatch, true));
                    $('<li class="minicolors-swatch minicolors-sprite"><span class="minicolors-swatch-color"></span></li>')
                        .appendTo(swatches)
                        .data('swatch-color', settings.swatches[i])
                        .find('.minicolors-swatch-color')
                        .css({
                            backgroundColor: rgb2hex(swatch),
                            opacity: swatch.a
                        });
                    settings.swatches[i] = swatch;
                }
            }
            if( settings.inline ) input.parent().addClass('minicolors-inline');
            updateFromInput(input, false, true);
            input.data('minicolors-initialized', true);
        }

        function move(target, event, animate) {
            var input = target.parents('.minicolors').find('.minicolors-input'),
                settings = input.data('minicolors-settings'),
                picker = target.find('[class$=-picker]'),
                offsetX = target.offset().left,
                offsetY = target.offset().top,
                x = Math.round(event.pageX - offsetX),
                y = Math.round(event.pageY - offsetY),
                duration = animate ? settings.animationSpeed : 0,
                wx, wy, r, phi;
            if (x < 0) x = 0;
            if (y < 0) y = 0;
            if (x > target.width()) x = target.width();
            if (y > target.height()) y = target.height();
            if (target.parent().is('.minicolors-slider-wheel') && picker.parent().is('.minicolors-grid')) {
                wx = 75 - x;
                wy = 75 - y;
                r = Math.sqrt(wx * wx + wy * wy);
                phi = Math.atan2(wy, wx);
                if (phi < 0) phi += Math.PI * 2;
                if (r > 75) {
                    r = 75;
                    x = 75 - (75 * Math.cos(phi));
                    y = 75 - (75 * Math.sin(phi));
                }
                x = Math.round(x);
                y = Math.round(y);
            }
            if (target.is('.minicolors-grid')) {
                picker.stop(true).animate({
                    top: y + 'px',
                    left: x + 'px'
                }, duration, settings.animationEasing, function(){
                    updateFromControl(input, target);
                });
            } else {
                picker.stop(true).animate({
                    top: y + 'px'
                }, duration, settings.animationEasing, function() {
                    updateFromControl(input, target);
                });
            }
        }

        function updateFromControl(input, target) {

            function getCoords(picker, container) {
                var left, top;
                if (!picker.length || !container) return null;
                left = picker.offset().left;
                top = picker.offset().top;

                return {
                    x: left - container.offset().left + (picker.outerWidth() / 2),
                    y: top - container.offset().top + (picker.outerHeight() / 2)
                };
            }
            var hue, saturation, brightness, x, y, r, phi,
                hex = input.val(),
                opacity = input.attr('data-opacity'),
                minicolors = input.parent(),
                settings = input.data('minicolors-settings'),
                swatch = minicolors.find('.minicolors-input-swatch'),
                grid = minicolors.find('.minicolors-grid'),
                slider = minicolors.find('.minicolors-slider'),
                opacitySlider = minicolors.find('.minicolors-opacity-slider'),
                gridPicker = grid.find('[class$=-picker]'),
                sliderPicker = slider.find('[class$=-picker]'),
                opacityPicker = opacitySlider.find('[class$=-picker]'),
                gridPos = getCoords(gridPicker, grid),
                sliderPos = getCoords(sliderPicker, slider),
                opacityPos = getCoords(opacityPicker, opacitySlider);
            if (target.is('.minicolors-grid, .minicolors-slider, .minicolors-opacity-slider')) {
                switch (settings.control) {
                    case 'wheel':
                        x = (grid.width() / 2) - gridPos.x;
                        y = (grid.height() / 2) - gridPos.y;
                        r = Math.sqrt(x * x + y * y);
                        phi = Math.atan2(y, x);
                        if (phi < 0) phi += Math.PI * 2;
                        if (r > 75) {
                            r = 75;
                            gridPos.x = 69 - (75 * Math.cos(phi));
                            gridPos.y = 69 - (75 * Math.sin(phi));
                        }
                        saturation = keepWithin(r / 0.75, 0, 100);
                        hue = keepWithin(phi * 180 / Math.PI, 0, 360);
                        brightness = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
                        hex = hsb2hex({
                            h: hue,
                            s: saturation,
                            b: brightness
                        });
                        slider.css('backgroundColor', hsb2hex({ h: hue, s: saturation, b: 100 }));
                        break;

                    case 'saturation':
                        hue = keepWithin(parseInt(gridPos.x * (360 / grid.width()), 10), 0, 360);
                        saturation = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
                        brightness = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
                        hex = hsb2hex({
                            h: hue,
                            s: saturation,
                            b: brightness
                        });
                        slider.css('backgroundColor', hsb2hex({ h: hue, s: 100, b: brightness }));
                        minicolors.find('.minicolors-grid-inner').css('opacity', saturation / 100);
                        break;
                    case 'brightness':
                        hue = keepWithin(parseInt(gridPos.x * (360 / grid.width()), 10), 0, 360);
                        saturation = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
                        brightness = keepWithin(100 - Math.floor(sliderPos.y * (100 / slider.height())), 0, 100);
                        hex = hsb2hex({
                            h: hue,
                            s: saturation,
                            b: brightness
                        });
                        slider.css('backgroundColor', hsb2hex({ h: hue, s: saturation, b: 100 }));
                        minicolors.find('.minicolors-grid-inner').css('opacity', 1 - (brightness / 100));
                        break;
                    default:
                        hue = keepWithin(360 - parseInt(sliderPos.y * (360 / slider.height()), 10), 0, 360);
                        saturation = keepWithin(Math.floor(gridPos.x * (100 / grid.width())), 0, 100);
                        brightness = keepWithin(100 - Math.floor(gridPos.y * (100 / grid.height())), 0, 100);
                        hex = hsb2hex({
                            h: hue,
                            s: saturation,
                            b: brightness
                        });
                        grid.css('backgroundColor', hsb2hex({ h: hue, s: 100, b: 100 }));
                        break;
                }
                if (settings.opacity) {
                    opacity = parseFloat(1 - (opacityPos.y / opacitySlider.height())).toFixed(2);
                } else {
                    opacity = 1;
                }
                updateInput(input, hex, opacity);
            } else {
                swatch.find('span').css({
                    backgroundColor: hex,
                    opacity: opacity
                });
                doChange(input, hex, opacity, true);
            }
        }

        function updateInput(input, value, opacity) {
            var rgb,
            minicolors = input.parent(),
            settings = input.data('minicolors-settings'),
            swatch = minicolors.find('.minicolors-input-swatch');

            if (settings.opacity) input.attr('data-opacity', opacity);
            if( settings.format === 'rgb' ) {
                if (isRgb(value)) {
                    rgb = parseRgb(value, true);
                } else {
                    rgb = hex2rgb(parseHex(value, true));
                }
                opacity = input.attr('data-opacity') === '' ? 1 : keepWithin(parseFloat(input.attr('data-opacity')).toFixed(2), 0, 1);
                if (isNaN(opacity) || !settings.opacity) opacity = 1;
                if (input.minicolors('rgbObject').a <= 1 && rgb && settings.opacity) {
                    value = 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + parseFloat( opacity ) + ')';
                } else {
                    value = 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
                }
            } else {
                if (isRgb(value)) {
                    value = rgbString2hex(value);
                }
                value = convertCase( value, settings.letterCase );
            }
            input.val(value);
            swatch.find('span').css({
                backgroundColor: value,
                opacity: opacity
            });
            doChange(input, value, opacity, true);
        }

        function updateFromInput(input, preserveInputValue, flag) {
            var hex,
                hsb,
                opacity,
                keywords,
                alpha,
                value,
                x, y, r, phi,
                minicolors = input.parent(),
                settings = input.data('minicolors-settings'),
                swatch = minicolors.find('.minicolors-input-swatch'),
                grid = minicolors.find('.minicolors-grid'),
                slider = minicolors.find('.minicolors-slider'),
                opacitySlider = minicolors.find('.minicolors-opacity-slider'),
                gridPicker = grid.find('[class$=-picker]'),
                sliderPicker = slider.find('[class$=-picker]'),
                opacityPicker = opacitySlider.find('[class$=-picker]');
            if (isRgb(input.val())) {
                hex = rgbString2hex(input.val());
                alpha = keepWithin(parseFloat(getAlpha(input.val())).toFixed(2), 0, 1);
                if (alpha) {
                    input.attr('data-opacity', alpha);
                }
            } else {
                hex = convertCase(parseHex(input.val(), true), settings.letterCase);
            }
            if(!hex){
                hex = convertCase(parseInput(settings.defaultValue, true), settings.letterCase);
            }
            hsb = hex2hsb(hex);
            keywords = !settings.keywords ? [] : $.map(settings.keywords.split(','), function(a){
                return $.trim(a.toLowerCase());
            });
            if (input.val() !== '' && $.inArray(input.val().toLowerCase(), keywords) > -1) {
                value = convertCase(input.val());
            } else {
                value = isRgb(input.val()) ? parseRgb(input.val()) : hex;
            }
            if (!preserveInputValue) input.val(value);
            if (settings.opacity) {
                opacity = input.attr('data-opacity') === '' ? 1 : keepWithin(parseFloat(input.attr('data-opacity')).toFixed(2), 0, 1);
                if( isNaN(opacity) ) opacity = 1;
                input.attr('data-opacity', opacity);
                swatch.find('span').css('opacity', opacity);
                y = keepWithin(opacitySlider.height() - (opacitySlider.height() * opacity), 0, opacitySlider.height());
                opacityPicker.css('top', y + 'px');
            }
            if (input.val().toLowerCase() === 'transparent') {
                swatch.find('span').css('opacity', 0);
            }
            swatch.find('span').css('backgroundColor', hex);
            switch(settings.control) {
                case 'wheel':
                    r = keepWithin(Math.ceil(hsb.s * 0.75), 0, grid.height() / 2);
                    phi = hsb.h * Math.PI / 180;
                    x = keepWithin(75 - Math.cos(phi) * r, 0, grid.width());
                    y = keepWithin(75 - Math.sin(phi) * r, 0, grid.height());
                    gridPicker.css({
                        top: y + 'px',
                        left: x + 'px'
                    });
                    y = 150 - (hsb.b / (100 / grid.height()));
                    if (hex === '') y = 0;
                    sliderPicker.css('top', y + 'px');
                    slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: hsb.s, b: 100 }));
                    break;
                case 'saturation':
                    x = keepWithin((5 * hsb.h) / 12, 0, 150);
                    y = keepWithin(grid.height() - Math.ceil(hsb.b / (100 / grid.height())), 0, grid.height());
                    gridPicker.css({
                        top: y + 'px',
                        left: x + 'px'
                    });
                    y = keepWithin(slider.height() - (hsb.s * (slider.height() / 100)), 0, slider.height());
                    sliderPicker.css('top', y + 'px');
                    slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: 100, b: hsb.b }));
                    minicolors.find('.minicolors-grid-inner').css('opacity', hsb.s / 100);
                    break;
                case 'brightness':
                    x = keepWithin((5 * hsb.h) / 12, 0, 150);
                    y = keepWithin(grid.height() - Math.ceil(hsb.s / (100 / grid.height())), 0, grid.height());
                    gridPicker.css({
                        top: y + 'px',
                        left: x + 'px'
                    });
                    y = keepWithin(slider.height() - (hsb.b * (slider.height() / 100)), 0, slider.height());
                    sliderPicker.css('top', y + 'px');
                    slider.css('backgroundColor', hsb2hex({ h: hsb.h, s: hsb.s, b: 100 }));
                    minicolors.find('.minicolors-grid-inner').css('opacity', 1 - (hsb.b / 100));
                    break;
                default:
                    x = keepWithin(Math.ceil(hsb.s / (100 / grid.width())), 0, grid.width());
                    y = keepWithin(grid.height() - Math.ceil(hsb.b / (100 / grid.height())), 0, grid.height());
                    gridPicker.css({
                        top: y + 'px',
                        left: x + 'px'
                    });
                    y = keepWithin(slider.height() - (hsb.h / (360 / slider.height())), 0, slider.height());
                    sliderPicker.css('top', y + 'px');
                    grid.css('backgroundColor', hsb2hex({ h: hsb.h, s: 100, b: 100 }));
                    break;
            }
            if (input.data('minicolors-initialized')) {
                doChange(input, value, opacity, flag);
            }
        }

        function doChange(input, value, opacity, flag) {
            var settings = input.data('minicolors-settings'),
                lastChange = input.data('minicolors-lastChange'),
                obj,
                sel,
                i;
            if (!lastChange || lastChange.value !== value || lastChange.opacity !== opacity) {
                input.data('minicolors-lastChange', {
                    value: value,
                    opacity: opacity
                });
                if (settings.swatches && settings.swatches.length !== 0) {
                    if (!isRgb(value)) {
                        obj = hex2rgb(value);
                    } else {
                        obj = parseRgb(value, true);
                    }
                    sel = -1;
                    for (i = 0; i < settings.swatches.length; ++i) {
                        if (obj.r === settings.swatches[i].r && obj.g === settings.swatches[i].g && obj.b === settings.swatches[i].b
                            && obj.a === settings.swatches[i].a) {
                            sel = i;
                            break;
                        }
                    }
                    input.parent().find('.minicolors-swatches .minicolors-swatch').removeClass('selected');
                    if (i !== -1) {
                        input.parent().find('.minicolors-swatches .minicolors-swatch').eq(i).addClass('selected');
                    }
                }
                if (settings.change && flag) {
                    if(settings.changeDelay) {
                        clearTimeout(input.data('minicolors-changeTimeout'));
                        input.data('minicolors-changeTimeout', setTimeout( function() {
                            settings.change.call(input.get(0), value, opacity);
                        }, settings.changeDelay));
                    } else {
                        settings.change.call(input.get(0), value, opacity);
                    }
                }
                input.trigger('change').trigger('input');
            }
        }

        function rgbObject(input) {
            var hex = parseHex($(input).val(), true),
                rgb = hex2rgb(hex),
                opacity = $(input).attr('data-opacity');
            if (!rgb) return null;
            if (opacity !== undefined) $.extend(rgb, { a: parseFloat(opacity) });

            return rgb;
        }

        function rgbString(input, alpha) {
            var hex = parseHex($(input).val(), true),
                rgb = hex2rgb(hex),
                opacity = $(input).attr('data-opacity');
            if (!rgb) return null;
            if (opacity === undefined) opacity = 1;
            if (alpha) {
                return 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + parseFloat(opacity) + ')';
            } else {
                return 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
            }
        }

        function convertCase(string, letterCase) {
            return letterCase === 'uppercase' ? string.toUpperCase() : string.toLowerCase();
        }

        function parseHex(string, expand) {
            string = string.replace(/^#/g, '');
            if (!string.match(/^[A-F0-9]{3,6}/ig)) return '';
            if (string.length !== 3 && string.length !== 6) return '';
            if (string.length === 3 && expand) {
                string = string[0] + string[0] + string[1] + string[1] + string[2] + string[2];
            }

            return '#' + string;
        }

        function parseRgb(string, obj) {
            var values = string.replace(/[^\d,.]/g, ''),
                rgba = values.split(',');
            rgba[0] = keepWithin(parseInt(rgba[0], 10), 0, 255);
            rgba[1] = keepWithin(parseInt(rgba[1], 10), 0, 255);
            rgba[2] = keepWithin(parseInt(rgba[2], 10), 0, 255);
            if (rgba[3]) {
                rgba[3] = keepWithin(parseFloat(rgba[3], 10), 0, 1);
            }
            if (obj) {
                return {
                    r: rgba[0],
                    g: rgba[1],
                    b: rgba[2],
                    a: rgba[3] ? rgba[3] : null
                };
            }
            if (typeof(rgba[3]) !== 'undefined' && rgba[3] <= 1) {
                return 'rgba(' + rgba[0] + ', ' + rgba[1] + ', ' + rgba[2] + ', ' + rgba[3] + ')';
            } else {
                return 'rgb(' + rgba[0] + ', ' + rgba[1] + ', ' + rgba[2] + ')';
            }
        }

        function parseInput(string, expand) {
            if (isRgb(string)) {
                return parseRgb(string);
            } else {
                return parseHex(string, expand);
            }
        }

        function keepWithin(value, min, max) {
            if (value < min) value = min;
            if (value > max) value = max;

            return value;
        }

        function isRgb(string) {
            var rgb = string.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);

            return (rgb && rgb.length === 4) ? true : false;
        }

        function getAlpha(rgba) {
            rgba = rgba.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+(\.\d{1,2})?|\.\d{1,2})[\s+]?/i);

            return (rgba && rgba.length === 6) ? rgba[4] : '1';
        }

        function hsb2rgb(hsb) {
            var rgb = {},
                h = Math.round(hsb.h),
                s = Math.round(hsb.s * 255 / 100),
                v = Math.round(hsb.b * 255 / 100);
            if (s === 0) {
                rgb.r = rgb.g = rgb.b = v;
            } else {
                var t1 = v,
                    t2 = (255 - s) * v / 255,
                    t3 = (t1 - t2) * (h % 60) / 60;
                if (h === 360) h = 0;
                if (h < 60) {
                    rgb.r = t1;
                    rgb.b = t2;
                    rgb.g = t2 + t3;
                } else if (h < 120) {
                    rgb.g = t1;
                    rgb.b = t2;
                    rgb.r = t1 - t3;
                } else if (h < 180) {
                    rgb.g = t1;
                    rgb.r = t2;
                    rgb.b = t2 + t3;
                } else if (h < 240) {
                    rgb.b = t1;
                    rgb.r = t2;
                    rgb.g = t1 - t3;
                } else if (h < 300) {
                    rgb.b = t1;
                    rgb.g = t2;
                    rgb.r = t2 + t3;
                } else if (h < 360) {
                    rgb.r = t1;
                    rgb.g = t2;
                    rgb.b = t1 - t3;
                } else {
                    rgb.r = 0;
                    rgb.g = 0;
                    rgb.b = 0;
                }
            }

            return {
                r: Math.round(rgb.r),
                g: Math.round(rgb.g),
                b: Math.round(rgb.b)
            };
        }

        function rgbString2hex(rgb){
            rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
            return (rgb && rgb.length === 4) ? '#' +
            ('0' + parseInt(rgb[1],10).toString(16)).slice(-2) +
            ('0' + parseInt(rgb[2],10).toString(16)).slice(-2) +
            ('0' + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
        }

        function rgb2hex(rgb) {
            var hex = [
                rgb.r.toString(16),
                rgb.g.toString(16),
                rgb.b.toString(16)
            ];
            $.each(hex, function(nr, val) {
                if (val.length === 1) hex[nr] = '0' + val;
            });
            return '#' + hex.join('');
        }

        function hsb2hex(hsb) {
            return rgb2hex(hsb2rgb(hsb));
        }

        function hex2hsb(hex) {
            var hsb = rgb2hsb(hex2rgb(hex));
            if(hsb.s === 0) hsb.h = 360;

            return hsb;
        }

        function rgb2hsb(rgb) {
            var hsb = { h: 0, s: 0, b: 0 },
                min = Math.min(rgb.r, rgb.g, rgb.b),
                max = Math.max(rgb.r, rgb.g, rgb.b),
                delta = max - min;
            hsb.b = max;
            hsb.s = max !== 0 ? 255 * delta / max : 0;
            if( hsb.s !== 0 ) {
                if( rgb.r === max ) {
                    hsb.h = (rgb.g - rgb.b) / delta;
                } else if( rgb.g === max ) {
                    hsb.h = 2 + (rgb.b - rgb.r) / delta;
                } else {
                    hsb.h = 4 + (rgb.r - rgb.g) / delta;
                }
            } else {
                hsb.h = -1;
            }
            hsb.h *= 60;
            if( hsb.h < 0 ) {
                hsb.h += 360;
            }
            hsb.s *= 100/255;
            hsb.b *= 100/255;

            return hsb;
        }

        function hex2rgb(hex) {
            hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);

            return {
                r: hex >> 16,
                g: (hex & 0x00FF00) >> 8,
                b: (hex & 0x0000FF)
            };
        }

        $(document)
            .on('mousedown.minicolors', '.minicolors-grid, .minicolors-slider, .minicolors-opacity-slider', function(event) {
                var target = $(this);
                event.preventDefault();
                $(document).data('minicolors-target', target);
                move(target, event, true);
                $('body').addClass('start-minicolors-move');
            })
            .on('mousemove.minicolors', function(event) {
                var target = $(document).data('minicolors-target');
                if(target) move(target, event);
            })
            .on('mouseup.minicolors', function() {
                $(this).removeData('minicolors-target');
                $('body').removeClass('start-minicolors-move');
            })
            .on('click.minicolors', '.minicolors-swatches li', function(event) {
                event.preventDefault();
                var target = $(this), input = target.parents('.minicolors').find('.minicolors-input'), color = target.data('swatch-color');
                updateInput(input, color, getAlpha(color));
                updateFromInput(input, false, true);
            })
            .on('blur.minicolors', '.minicolors-input', function() {
                var input = $(this),
                    settings = input.data('minicolors-settings'),
                    keywords,
                    hex,
                    rgba,
                    swatchOpacity,
                    value;
                if (!input.data('minicolors-initialized')) return;
                keywords = !settings.keywords ? [] : $.map(settings.keywords.split(','), function(a) {
                    return $.trim(a.toLowerCase());
                });
                if(input.val() !== '' && $.inArray(input.val().toLowerCase(), keywords) > -1) {
                    value = input.val();
                } else {
                    if (isRgb(input.val())) {
                        rgba = parseRgb(input.val(), true);
                    } else {
                        hex = parseHex(input.val(), true);
                        rgba = hex ? hex2rgb(hex) : null;
                    }
                    if (rgba === null) {
                        value = settings.defaultValue;
                    } else if(settings.format === 'rgb') {
                        value = settings.opacity ?
                            parseRgb('rgba(' + rgba.r + ',' + rgba.g + ',' + rgba.b + ',' + input.attr('data-opacity') + ')') :
                            parseRgb('rgb(' + rgba.r + ',' + rgba.g + ',' + rgba.b + ')');
                    } else {
                        value = rgb2hex(rgba);
                    }
                }
                swatchOpacity = settings.opacity ? input.attr('data-opacity') : 1;
                if (value.toLowerCase() === 'transparent') swatchOpacity = 0;
                input.closest('.minicolors').find('.minicolors-input-swatch > span').css('opacity', swatchOpacity);
                input.val(value);
                if(input.val() === '') input.val(parseInput(settings.defaultValue, true));
                input.val(convertCase(input.val(), settings.letterCase));
            })
            .on('keyup.minicolors', '.minicolors-input', function() {
                var input = $(this);
                if (!input.data('minicolors-initialized')) return;
                updateFromInput(input, true, true);
            })
            .on('paste.minicolors', '.minicolors-input', function() {
                var input = $(this);
                if (!input.data('minicolors-initialized')) return;
                setTimeout( function() {
                    updateFromInput(input, true, true);
                }, 1);
            });
    }(window.jQuery);
});