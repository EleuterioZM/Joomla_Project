/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function photoEditor(url)
{
    var canvas = document.getElementById('photo-editor'),
        obj = $g.extend(true, {}, app.itemDelete),
        container = $g(canvas),
        scaleH = 1,
        scaleV = 1,
        ctx = canvas.getContext('2d'),
        ocanvas = document.getElementById('ba-overlay-canvas'),
        octx = ocanvas.getContext('2d'),
        angle = 0,
        orig = new Image(),
        image = new Image(),
        lastAction = '',
        canvasSize = {
            width: $g('.resize-image-wrapper').width(),
            height: $g('.resize-image-wrapper').height()
        },
        keep = {
            enable: false,
            ratio: null
        },
        type = 'image/png',
        eState = {},
        prop = {},
        effect = null,
        filters = {
            "default": {
                "contrast": 100,
                "brightness": 100,
                "saturate": 100,
                "sepia": 0,
                "grayscale": 0,
                "blur": 0
            }
        }
        effects = {
            "original": {
                "filter": "none"
            },
            "1977": {
                "filter": "sepia(0%) brightness(110%) contrast(110%) saturate(130%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(243, 106, 188, 0.3)",
                    "blend": "screen"
                }
            },
            "aden": {
                "filter": "sepia(0%) brightness(120%) contrast(90%) saturate(85%) grayscale(0%) invert(0%) hue-rotate(20deg) blur(0px)",
                "overlay": {
                    "type": "linear",
                    "direction": "right",
                    "color0": "rgba(66, 10, 14, 0.2)",
                    "color1": "rgba(66, 10, 14, 0)",
                    "blend": "darken"
                }
            },
            "amaro": {
                "filter": "sepia(0%) brightness(110%) contrast(90%) saturate(150%) grayscale(0%) invert(0%) hue-rotate(-10deg) blur(0px)"
            },
            "brannan": {
                "filter": "sepia(50%) brightness(100%) contrast(140%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(161, 44, 199, 0.31)",
                    "blend": "lighten"
                }
            },
            "brooklyn": {
                "filter": "sepia(0%) brightness(110%) contrast(90%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0,
                    "color0": "rgba(168, 223, 193, 0.4)",
                    "color1": "rgba(183, 196, 200, 0.2)",
                    "blend": "overlay"
                }
            },
            "clarendon": {
                "filter": "sepia(0%) brightness(100%) contrast(120%) saturate(125%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(127, 187, 227, 0.2)",
                    "blend": "overlay"
                }
            },
            "earlybird": {
                "filter": "sepia(20%) brightness(100%) contrast(90%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0.2,
                    "color0": "rgb(208, 186, 142)",
                    "color1": "rgba(29, 2, 16, 0.2)",
                    "blend": "overlay"
                }
            },
            "gingham": {
                "filter": "sepia(0%) brightness(105%) contrast(100%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(350deg) blur(0px)",
                "overlay": {
                    "type": "linear",
                    "direction": "right",
                    "color0": "rgba(66, 10, 14, 0.2)",
                    "color1": "rgba(0, 0, 0, 0)",
                    "blend": "darken"
                }
            },
            "hudson": {
                "filter": "sepia(0%) brightness(120%) contrast(90%) saturate(110%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0.5,
                    "color0": "rgba(255, 177, 166, 0.5)",
                    "color1": "rgba(52, 33, 52, 0.5)",
                    "blend": "multiply"
                }
            },
            "inkwell": {
                "filter": "sepia(30%) brightness(110%) contrast(110%) saturate(100%) grayscale(100%) invert(0%) hue-rotate(0deg) blur(0px)"
            },
            "lofi": {
                "filter": "sepia(0%) brightness(100%) contrast(150%) saturate(110%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0.7,
                    "color0": "rgba(0, 0, 0, 0)",
                    "color1": "rgba(34, 34, 34, 1)",
                    "blend": "multiply"
                }
            },
            "maven": {
                "filter": "sepia(25%) brightness(95%) contrast(95%) saturate(150%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(3, 230, 26, 0.2)",
                    "blend": "hue"
                }
            },
            "perpetua": {
                "filter": "sepia(0%) brightness(100%) contrast(100%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "linear",
                    "direction": "bottom",
                    "color0": "rgba(0, 91, 154, 0.5)",
                    "color1": "rgba(61, 193, 230, 0)",
                    "blend": "soft-light"
                }
            },
            "reyes": {
                "filter": "sepia(22%) brightness(110%) contrast(85%) saturate(75%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(173, 205, 239, 0.5)",
                    "blend": "soft-light"
                }
            },
            "stinson": {
                "filter": "sepia(0%) brightness(115%) contrast(75%) saturate(85%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(240, 149, 128, 0.2)",
                    "blend": "soft-light"
                }
            },
            "toaster": {
                "filter": "sepia(0%) brightness(90%) contrast(150%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0,
                    "color0": "rgba(15, 78, 128, 0.5)",
                    "color1": "rgba(59, 0, 59, 0.5)",
                    "blend": "screen"
                }
            },
            "walden": {
                "filter":"sepia(30%) brightness(110%) contrast(100%) saturate(160%) grayscale(0%) invert(0%) hue-rotate(350deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(204, 68, 0, 0.3)",
                    "blend": "screen"
                }
            },
            "valencia": {
                "filter": "sepia(8%) brightness(108%) contrast(108%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "color",
                    "color": "rgba(58, 3, 57, 0.5)",
                    "blend": "exclusion"
                }
            },
            "xpro2": {
                "filter": "sepia(30%) brightness(100%) contrast(100%) saturate(100%) grayscale(0%) invert(0%) hue-rotate(0deg) blur(0px)",
                "overlay": {
                    "type": "radial",
                    "start": 0.4,
                    "color0": "rgb(224, 231, 230)",
                    "color1": "rgba(43, 42, 161, 0.6)",
                    "blend": "color-burn"
                }
            }
        };

    function getImageSize(width, height, imgWidth, imgHeight)
    {
        var ratio = imgWidth / imgHeight;
        if (imgWidth > width || imgHeight > height) {
            if (ratio > 1) {
                imgWidth = width;
                imgHeight = imgWidth / ratio;
                if (imgHeight > height) {
                    imgHeight = height;
                    imgWidth = imgHeight * ratio;
                }
            } else {
                imgHeight = height;
                imgWidth = imgHeight * ratio;
                if (imgWidth > width) {
                    imgWidth = width;
                    imgHeight = imgWidth / ratio;
                }
            }
        }
        eState.imgWidth = Math.floor(imgWidth);
        eState.imgHeight = Math.floor(imgHeight);
        canvas.width = eState.imgWidth;
        canvas.height = eState.imgHeight;
    }

    function generateOverlayCanvas()
    {
        var left = eState.oLeft - eState.minLeft,
            top = eState.oTop - eState.minTop;
        ocanvas.width = eState.oWidth;
        ocanvas.height = eState.oHeight;
        octx.save();
        octx.clearRect(0, 0, eState.oWidth, eState.oHeight);
        octx.drawImage(canvas, left, top, eState.oWidth, eState.oHeight, 0, 0, eState.oWidth, eState.oHeight)
        octx.restore();
    }

    function generateFilterEffects()
    {
        $g('#filter-effects-image-options .filter-effect-canvas').each(function(){
            var context = this.getContext('2d'),
                effect = effects[this.dataset.key],
                parent = $g(this).parent(),
                imgWidth = image.width, 
                imgHeight = image.height,
                ratio = imgWidth / imgHeight, 
                w = parent.width(),
                h = w / ratio;
            w = h = 200;
            if (imgWidth > w || imgHeight > h) {
                if (ratio > 1) {
                    imgWidth = w;
                    imgHeight = imgWidth / ratio;
                    if (imgHeight > h) {
                        imgHeight = h;
                        imgWidth = imgHeight * ratio;
                    }
                } else {
                    imgHeight = h;
                    imgWidth = imgHeight * ratio;
                    if (imgWidth > w) {
                        imgWidth = w;
                        imgHeight = imgWidth / ratio;
                    }
                }
            }
            this.width = imgWidth;
            this.height = imgHeight;
            context.save();
            context.clearRect(0, 0, imgWidth, imgHeight);
            context.filter = effect.filter;
            context.drawImage(canvas, 0, 0, imgWidth, imgHeight);
            applyEffect(effect, context, this);
            context.restore();
        });
    }

    function applyFilter()
    {
        effect = effects[this.dataset.key];
        ctx.save();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.filter = effect.filter;
        ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
        applyEffect(effect, ctx, canvas);
        ctx.restore();
        $g('#photo-editor-dialog ul').off('click')
            .one('click', 'a:not(a[href="#filter-effects-image-options"])', function(event){
            effect = effects.original;
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.filter = effect.filter;
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            applyEffect(effect, ctx, canvas);
            ctx.restore();
        });
        if (this.dataset.key != 'original') {
            $g('.filter-effects-action').addClass('active-button');
        } else {
            $g('.filter-effects-action').removeClass('active-button');
        }
    }

    function applyEffect(effect, context, canvas)
    {
        if (effect.overlay) {
            context.globalCompositeOperation = effect.overlay.blend;
            var x0 = y0 = x1 = y1 = r0 = r1 = 0;
            if (effect.overlay.type == 'color') {
                context.fillStyle = effect.overlay.color;
                context.fillRect(0, 0, canvas.width, canvas.height);
            } else if (effect.overlay.type == 'linear') {
                if (effect.overlay.direction == 'right') {
                    x1 = canvas.width;
                } else if (effect.overlay.direction == 'bottom') {
                    y1 = canvas.height;
                }
                var grd = context.createLinearGradient(x0, y0, x1, y1);
                grd.addColorStop(0, effect.overlay.color0);
                grd.addColorStop(1, effect.overlay.color1);
                context.fillStyle = grd;
                context.fillRect(0, 0, canvas.width, canvas.height);
            } else if (effect.overlay.type == 'radial') {
                x0 = x1 = canvas.width / 2;
                y0 = y1 = canvas.height / 2;
                if (canvas.width > canvas.height) {
                    r1 = canvas.width * 0.6;
                } else {
                    r1 = canvas.height * 0.6;
                }
                var grd = context.createRadialGradient(x0, y0, r0, x1, y1, r1);
                grd.addColorStop(effect.overlay.start, effect.overlay.color0);
                grd.addColorStop(1, effect.overlay.color1);
                context.fillStyle = grd;
                context.fillRect(0, 0, canvas.width, canvas.height);
            }
        }
    }

    function resetFilters()
    {
        $g('#adjust-image-options [data-filter]').each(function(){
            setRangeValue($g(this), filters.default[this.dataset.filter]);
        });
    }

    function adjustChange()
    {
        var str = '';
        $g('#adjust-image-options [data-filter]').each(function(){
            var value = this.value < this.min ? this.min : this.value;
            if (this.dataset.filter == 'blur') {
                value += 'px';
            } else {
                value += '%';
            }
            str += this.dataset.filter+'('+value+') ';
        });
        ctx.save();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.filter = str;
        ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
        ctx.restore();
        $g('#photo-editor-dialog ul').off('click')
            .one('click', 'a:not(a[href="#adjust-image-options"])', function(event){
            resetFilters();
            var str = '';
            $g('#adjust-image-options [data-filter]').each(function(){
                var value = this.value;
                if (this.dataset.filter == 'blur') {
                    value += 'px';
                } else {
                    value += '%';
                }
                str += this.dataset.filter+'('+value+') ';
            });
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.filter = str;
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
        });
        $g('.adjust-action').addClass('active-button');
    }

    function getImageType()
    {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.responseType = "blob";
        xhr.onload = function() {
            if (xhr.status === 200) {
                type = xhr.response.type;
            }
        };
        xhr.send();
    }

    image.onload = function(){
        getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
        ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
        keep.ratio = this.width / this.height;
        init();
    }

    getImageType()
    orig.onload = restoreImage;
    orig.src = url;

    function checkSaveBtn()
    {
        if (image.src.indexOf('data:') === 0) {
            $g('.photo-editor-save-image').attr('data-context', 'save-image-context-menu');
        } else {
            $g('.photo-editor-save-image').removeAttr('data-context');
        }
        generateFilterEffects();
    }

    function init()
    {
        $g('.ba-crop-overlay').off('mousedown').on('mousedown', startMoving)
            .find('.ba-crop-overlay-resize-handle').off('mousedown').on('mousedown', handleResize);
        $g('.flip-action').off('click').on('click', flip);
        $g('.rotate-action').off('click').on('click', rotate);
        $g('.crop-action').off('click').on('click', cropAction);
        $g('.resize-action').off('click').on('click', resizeAction);
        $g('.reset-image').off('click').on('click', restoreImage);
        $g('.filter-effects-action').off('click').on('click', filterAction);
        $g('.flip-rotate-action').off('click').on('click', flipRotateAction);
        $g('.adjust-action').off('click').on('click', adjustAction);
        $g('#filter-effects-image-options .filter-effect-canvas').off('click').on('click', applyFilter);
        $g('.keep-proportions').prop('checked', false).off('change').on('change', checkProportion);
        $g('.aspect-ratio-select input[type="hidden"]').val('original').prev().val(gridboxLanguage['ORIGINAL']);
        $g('.aspect-ratio-select').off('customAction').on('customAction', changeRatio);
        $g('.crop-width').off('input').on('input', cropWidth);
        $g('.crop-height').off('input').on('input', cropHeight);
        $g('.resize-width').off('input').on('input', resizeWidth);
        $g('.resize-height').off('input').on('input', resizeHeight);
        $g('#adjust-image-options').off('adjustChange').on('adjustChange', adjustChange);
        resetFilters();
        generateFilterEffects();
        $g('.photo-editor-save-copy').off('mousedown').on('mousedown', function(){
            if (image.src.indexOf('data:') === 0) {
                $g('.photo-editor-file-title').val('');
                $g('.save-as-webp').prop('checked', obj.ext == 'webp').prop('disabled', obj.ext == 'webp');
                $g('#save-copy-dialog').modal().find('#apply-save-copy')
                    .removeClass('active-button').addClass('disable-button');
            }
        });
        $g('.photo-editor-file-title').off('input').on('input', function(){
            if (this.value.trim()) {
                $g('#apply-save-copy').addClass('active-button').removeClass('disable-button');
            } else {
                $g('#apply-save-copy').removeClass('active-button').addClass('disable-button');
            }
        });
        $g('#apply-save-copy').off('click').on('click', function(event){
            event.preventDefault();
            if (this.classList.contains('active-button')) {
                var object = {
                        ext: $g('.save-as-webp').prop('checked') ? 'webp' : obj.ext,
                        name: obj.name,
                        path: obj.path,
                        title: $g('.photo-editor-file-title').val().trim()
                    },
                    data = JSON.stringify(object),
                    XHR = new XMLHttpRequest();
                XHR.onreadystatechange = function(e) {
                    if (XHR.readyState == 4) {
                        if (!Boolean(XHR.responseText)) {
                            if (object.ext == 'webp') {
                                obj.webp = true;
                            }
                            $g('#save-copy-dialog').modal('hide');
                            obj.title = object.title;
                            saveImage();
                        } else {
                            $g('#save-copy-notice-dialog').modal();
                        }
                    }
                };
                XHR.open("POST", JUri+'index.php?option=com_gridbox&task=uploader.checkFileExists', true);
                XHR.send(data);
            }
        });
        $g('#apply-overwrite-copy').off('click').on('click', function(event){
            event.preventDefault();
            $g('#save-copy-notice-dialog').modal('hide');
            $g('#save-copy-dialog').modal('hide');
            obj.title = $g('.photo-editor-file-title').val().trim();
            saveImage();
        });
        $g('.save-photo-editor-image').off('mousedown').on('mousedown', saveImage);
        container.off('mousedown.createOverlay').on('mousedown.createOverlay', createOverlay);
        $g('#photo-editor-dialog .resize-image-wrapper').addClass('photo-editor-loaded');
        setTimeout(function(){
            $g('.resize-image-wrapper').addClass('resize-enabled');
        }, 300);
        $g(window).off('resize.photoEditor').on('resize.photoEditor', function(){
            var offset = canvas.getBoundingClientRect();
            $g('.ba-crop-overlay').css({
                top : offset.top,
                left : offset.left
            });
        });
    }

    function saveImage()
    {
        obj.image = image.src;
        if (obj.image.indexOf('data:') === 0) {
            obj.method = window.atob('YmFzZTY0X2RlY29kZQ==');
            var data = JSON.stringify(obj),
                XHR = new XMLHttpRequest(),
                str = gridboxLanguage['SAVING']+'<img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
            app.notification.find('p').html(str);
            app.notification.removeClass('animation-out').addClass('notification-in');
            setTimeout(function(){
                if (obj.webp && obj.ext != 'webp') {
                    obj.ext = 'webp';
                    var webp = document.createElement('canvas'),
                        webpCtx = webp.getContext('2d');
                    webp.width = image.width;
                    webp.height = image.height;
                    webpCtx.drawImage(image, 0, 0, image.width, image.height);
                    obj.image = webp.toDataURL('image/webp');
                    data = JSON.stringify(obj);
                }
                XHR.onreadystatechange = function(e) {
                    if (XHR.readyState == 4) {
                        app.showNotice(gridboxLanguage['SUCCESS_UPLOAD']);
                        window.frames['uploader-iframe'].mediaManager.setImageTime(obj);
                        window.frames['uploader-iframe'].mediaManager.reloadFolder();
                        setTimeout(function(){
                            $g('#photo-editor-dialog').modal('hide');
                        }, 3000);
                    }
                };
                XHR.open("POST", JUri+'index.php?option=com_gridbox&task=uploader.savePhotoEditorImage', true);
                XHR.send(data);
            }, 400);
        }
    }

    function checkProportion()
    {
        keep.enable = this.checked;
        keepProportion();
        $g('.crop-action').addClass('active-button');
    }

    function changeRatio()
    {
        var ratio = $g('.aspect-ratio-select').find('input[type="hidden"]').val(),
            array = [];
        if (ratio == 'original') {
            ratio = image.width+':'+image.height;
        }
        array = ratio.split(':');
        keep.ratio = array[0] / array[1];
        keep.enable = true;
        $g('.keep-proportions')[0].checked = true;
        keepProportion();
        $g('.crop-action').addClass('active-button');
    }

    function keepProportion(wFlag, hFlag)
    {
        if (keep.enable) {
            var h = Math.floor(eState.oWidth / keep.ratio),
                w = Math.floor(eState.oWidth),
                t = eState.oTop,
                b = '';
            if (h > eState.imgHeight) {
                h = Math.floor(eState.oHeight);
                w = Math.floor(h * keep.ratio);
            }
            if (t + h > eState.cBottom) {
                t = '';
                b = document.documentElement.clientHeight - eState.cBottom;
            }
            $g('.ba-crop-overlay').css({
                width: w,
                top: t,
                bottom: b,
                height : h
            });
            saveEventState();
            generateOverlayCanvas();
            var width = w * prop.x,
                height = h * prop.y;
            if (image.width > image.height) {
                width = keep.ratio * height;
            } else {
                height = width / keep.ratio;
            }
            if (!wFlag) {
                $g('.crop-width').val(Math.round(width));
            }
            if (!hFlag) {
                $g('.crop-height').val(Math.round(height));
            }
        }
    }

    function resizeWidth()
    {
        var height = this.value / (image.width / image.height);
        $g('.resize-height').val(Math.round(height));
        $g('.resize-action').addClass('active-button');
    }

    function resizeHeight()
    {
        var width = this.value * (image.width / image.height);
        $g('.resize-width').val(Math.round(width));
        $g('.resize-action').addClass('active-button');
    }

    function cropWidth()
    {
        var w = this.value,
            l = eState.oLeft,
            r = '';
        if (w > image.width) {
            w = image.width;
        }
        w = Math.floor(w / prop.x);
        if (l + w > eState.cRight) {
            l = '';
            r = document.documentElement.clientWidth - eState.cRight;
        }
        $g('.ba-crop-overlay').css({
            width: w,
            left: l,
            right : r
        });
        saveEventState();
        generateOverlayCanvas();
        keepProportion(true, false);
        $g('.crop-action').addClass('active-button');
    }

    function cropHeight()
    {
        var h = this.value,
            t = eState.oTop,
            l = eState.oLeft,
            w = eState.oWidth,
            b = '';
        if (h > image.height) {
            h = image.height;
        }
        h = h / prop.y;
        if (t + h > eState.cBottom) {
            t = '';
            b = document.documentElement.clientHeight - eState.cBottom;
        }
        if (keep.enable) {
            w = h * keep.ratio;
            if (w > image.width / prop.x) {
                w = image.width / prop.x;
                h = w / keep.ratio
            }
        }
        $g('.ba-crop-overlay').css({
            width: w,
            height: h,
            left: l,
            right: '',
            top: t,
            bottom: b
        });
        saveEventState();
        generateOverlayCanvas();
        keepProportion(false, true);
        $g('.crop-action').addClass('active-button');
    }

    function hideOverlay()
    {
        var offset = canvas.getBoundingClientRect();
        $g('.ba-crop-overlay').css({
            top : offset.top,
            left : offset.left,
            width : canvas.width,
            height : canvas.height,
            bottom : '',
            right : ''
        });
        var width = Math.round(canvas.width * prop.x),
            height = Math.round(canvas.height * prop.y),
            propWidth = image.width,
            propHeight = image.height;
        if (width >= propWidth) {
            width = propWidth;
            height = propHeight;
        }
        if (height >= propHeight) {
            width = propWidth;
            height = propHeight;
        }
        $g('.crop-width, .resize-width').val(width);
        $g('.crop-height, .resize-height').val(height);
        saveEventState();
        generateOverlayCanvas();
    }

    function restoreImage(event)
    {
        if (event) {
            event.preventDefault();
        }
        if (this.localName == 'a' && !this.classList.contains('active-button')) {
            return false;
        }
        getImageSize(canvasSize.width, canvasSize.height, orig.width, orig.height);
        ctx.drawImage(orig, 0, 0, eState.imgWidth, eState.imgHeight);
        scaleH = 1;
        scaleV = 1;
        angle = 0;
        image.src = orig.src;
        saveProportion();
        hideOverlay();
        setRangeValue($g('.photo-editor-quality'), 100);
        checkSaveBtn();
        $g('#photo-editor-dialog .active-button').removeClass('active-button');
    }

    function handleResize(event)
    {
        if (event.button == 0) {
            event.stopPropagation();
            saveEventState();
            var dir = this.dataset.resize
                item = $g('.ba-crop-overlay'),
                start = item[0].getBoundingClientRect();
            start.bottom = start.top + eState.oHeight;
            start.right = start.left + eState.oWidth;
            item.css({
                'transition' : 'none'
            });
            $g(document).on('mousemove.resizable', function(e){
                var w = h = l = t = b = r = '';
                if (dir == 'bottom-right') {
                    w = e.clientX - start.left;
                    h = e.clientY - start.top;
                    b = document.documentElement.clientHeight - e.clientY;
                    r = document.documentElement.clientWidth - e.clientX;
                    if (w < 0) {
                        w = start.left - e.clientX;
                        r = document.documentElement.clientWidth - start.left;
                    }
                    if (h < 0) {
                        h = start.top - e.clientY;
                        b = document.documentElement.clientHeight - start.top;
                    }
                } else if (dir == 'top-right') {
                    w = e.clientX - start.left;
                    h = start.bottom - e.clientY;
                    t = e.clientY
                    r = document.documentElement.clientWidth - e.clientX;
                    if (w < 0) {
                        w = start.left - e.clientX;
                        r = document.documentElement.clientWidth - start.left;
                    }
                    if (h < 0) {
                        t = start.bottom;
                        h = e.clientY - start.bottom;
                    }
                } else if (dir == 'bottom-left') {
                    w = start.right - e.clientX;
                    h = e.clientY - start.top;
                    b = document.documentElement.clientHeight - e.clientY;
                    l = e.clientX;
                    if (w < 0) {
                        w = e.clientX - start.right;
                        l = start.right;
                    }
                    if (h < 0) {
                        h = start.top - e.clientY;
                        b = document.documentElement.clientHeight - start.top;
                    }
                } else if (dir == 'top-left') {
                    w = start.right - e.clientX;
                    h = start.bottom - e.clientY;
                    t = e.clientY;
                    l = e.clientX;
                    if (w < 0) {
                        w = e.clientX - start.right;
                        l = start.right;
                    }
                    if (h < 0) {
                        t = start.bottom;
                        h = e.clientY - start.bottom;
                    }
                }
                if (e.clientX >= eState.cRight) {
                    if (l !== '') {
                        w = eState.cRight - start.right;
                    } else {
                        w = eState.cRight - start.left;
                    }
                    if (r !== '') {
                        r = document.documentElement.clientWidth - eState.cRight;
                    }
                }
                if (e.clientY >= eState.cBottom) {
                    if (t !== '') {
                        h = eState.cBottom - start.bottom;
                    } else {
                        h = eState.cBottom - start.top;
                    }
                    if (b !== '') {
                        b = document.documentElement.clientHeight - eState.cBottom;
                    }
                }
                if (e.clientX <= eState.minLeft) {
                    if (r !== '') {
                        w = start.left - eState.minLeft;
                    } else {
                        w = start.right - eState.minLeft;
                    }
                    if (l !== '') {
                        l = eState.minLeft
                    }
                }
                if (e.clientY < eState.minTop) {
                    if (t !== '') {
                        h = start.bottom - eState.minTop;
                    } else {
                        h = start.top - eState.minTop;
                    }
                    if (t !== '') {
                        t = eState.minTop
                    }
                }
                if (keep.enable) {
                    h = w / keep.ratio;
                    t = '';
                    if (b !== '') {
                        b = document.documentElement.clientHeight - start.top - h;
                    } else {
                        b = document.documentElement.clientHeight - start.bottom;
                    }
                    if (document.documentElement.clientHeight - b - h < eState.minTop) {
                        h = document.documentElement.clientHeight - b - eState.minTop;
                        w = h * keep.ratio;
                        if (r !== '') {
                            r = document.documentElement.clientWidth - start.left - w;
                        }
                        if (l !== '') {
                            l = start.right - w;
                        }
                    } else if (document.documentElement.clientHeight - b > eState.cBottom) {
                        b = document.documentElement.clientHeight - eState.cBottom;
                        h = eState.cBottom - start.top;
                        w = h * keep.ratio;
                        if (r !== '') {
                            r = document.documentElement.clientWidth - start.left - w;
                        }
                        if (l !== '') {
                            l = start.right - w;
                        }
                    }
                }
                $g('.ba-crop-overlay').css({
                    width: w,
                    height: h,
                    left: l,
                    top: t,
                    bottom : b,
                    right: r
                });
                var width = w * prop.x,
                    height = h * prop.y;
                if (keep.enable) {
                    height = width / keep.ratio;
                }
                $g('.crop-width').val(Math.round(width));
                $g('.crop-height').val(Math.round(height));
                saveEventState();
                generateOverlayCanvas();
                return false;
            }).on('mouseup.resizable', function(event){
                saveEventState();
                $g(document).off('mousemove.resizable mouseup.resizable');
                $g('.crop-action').addClass('active-button');
            });
        }
    }

    function createOverlay(e)
    {
        if (e.button != 0 || !$g('#crop-image-options').hasClass('active')) {
            return false;
        }
        $g('.ba-crop-overlay').hide().css({
            top : e.clientY,
            left : e.clientX,
            width : 0,
            height : 0,
            bottom : e.clientY,
            right : e.clientX
        }).find('> *').hide();
        var start = {
            top : e.clientY,
            left : e.clientX,
            bottom : e.clientY,
            right : e.clientX
        };
        saveEventState();
        $g(document).on('mousemove.createOverlay', function(e){
            var w = h = r = b = '';
            w = e.clientX - start.left;
            h = e.clientY - start.top;
            b = document.documentElement.clientHeight - e.clientY;
            r = document.documentElement.clientWidth - e.clientX;
            if (w < 0) {
                w = start.left - e.clientX;
                r = document.documentElement.clientWidth - start.left;
            }
            if (h < 0) {
                h = start.top - e.clientY;
                b = document.documentElement.clientHeight - start.top;
            }
            if (e.clientX >= eState.cRight) {
                w = eState.cRight - start.left;
                r = document.documentElement.clientWidth - eState.cRight;
            }
            if (e.clientY >= eState.cBottom) {
                h = eState.cBottom - start.top;
                b = document.documentElement.clientHeight - eState.cBottom;
            }
            if (e.clientX <= eState.minLeft) {
                w = start.left - eState.minLeft;
            }
            if (e.clientY < eState.minTop) {
                h = start.top - eState.minTop;
            }
            $g('.ba-crop-overlay').css({
                top: '',
                left: '',
                width: w,
                height: h,
                bottom: b,
                right: r,
                display: ''
            });
            $g('.crop-width').val(Math.round(w * prop.x));
            $g('.crop-height').val(Math.round(h * prop.y));
            saveEventState();
            generateOverlayCanvas();
        }).on('mouseup.createOverlay', function(event){
            $g(document).off('mousemove.createOverlay mouseup.createOverlay');
            $g('.ba-crop-overlay').find('> *').css('display', '');
        });
        return false;
    }

    function saveEventState()
    {
        var overlay = $g('.ba-crop-overlay'),
            rect = overlay[0].getBoundingClientRect();
        eState.oLeft = rect.left;
        eState.oTop = rect.top;
        eState.oWidth = overlay.outerWidth();
        eState.oHeight = overlay.outerHeight();
        rect = container[0].getBoundingClientRect();
        eState.minLeft = rect.left;
        eState.maxLeft = rect.left + eState.imgWidth - eState.oWidth;
        eState.minTop = rect.top;
        eState.maxTop = rect.top + eState.imgHeight - eState.oHeight;
        eState.cRight = rect.left + eState.imgWidth;
        eState.cBottom = rect.top + eState.imgHeight;
    };

    function startMoving(e)
    {
        if (e.button != 0) {
            return false;
        }
        e.preventDefault();
        e.stopPropagation();
        saveEventState();
        eState.deltaX = e.clientX - eState.oLeft;
        eState.deltaY = e.clientY - eState.oTop;
        $g(document).on('mousemove', moving);
        $g(document).on('mouseup', endMoving);
    };

    function endMoving(e)
    {
        e.preventDefault();
        $g(document).off('mouseup', endMoving);
        $g(document).off('mousemove', moving);
        $g('.crop-action').addClass('active-button');
    };

    function moving(e)
    {
        e.preventDefault();
        e.stopPropagation();
        var x = e.clientX - eState.deltaX,
            y = e.clientY - eState.deltaY;
        if (x > eState.maxLeft) {
            x = eState.maxLeft;
        }
        if (x < eState.minLeft) {
            x = eState.minLeft;
        }
        if (y > eState.maxTop) {
            y = eState.maxTop;
        }
        if (y < eState.minTop) {
            y = eState.minTop;
        }
        $g('.ba-crop-overlay').css({
            'left': x,
            'top':  y
        });
        saveEventState();
        generateOverlayCanvas();
    }

    function transformImage()
    {
        if (angle == 90 || angle == 270) {
            getImageSize(canvasSize.width, canvasSize.height, image.height, image.width);
        } else {
            getImageSize(canvasSize.width, canvasSize.height, image.width, image.height);
        }
        ctx.save();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.translate(eState.imgWidth / 2, eState.imgHeight / 2);
        ctx.rotate(angle * Math.PI / 180);
        ctx.scale(scaleH, scaleV);
        if (angle == 90 || angle == 270) {
            ctx.drawImage(image, -eState.imgHeight / 2, -eState.imgWidth / 2, eState.imgHeight, eState.imgWidth);
        } else {
            ctx.drawImage(image, -eState.imgWidth / 2, -eState.imgHeight / 2, eState.imgWidth, eState.imgHeight);
        }
        ctx.restore();
        applyImageTransform();
    }

    function applyImageTransform()
    {
        $g('#photo-editor-dialog ul').off('click').one('click', 'a:not(a[href="#flip-rotate-image-options"])', function(){
            getImageSize(canvasSize.width, canvasSize.height, image.width, image.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            checkSaveBtn();
            $g('.active-button').addClass('active-button');
        });
        $g('.flip-rotate-action').addClass('active-button');
    }

    function rotate()
    {
        if (angle == 0) {
            angle = 360;
        }
        angle = (angle + this.dataset.rotate * 1) % 360;
        transformImage();
    }

    function flip()
    {
        if (this.dataset.flip == 'horizontal') {
            scaleH *= -1;
        } else {
            scaleV *= -1;
        }
        transformImage();
    }

    function saveProportion()
    {
        prop = {
            x: image.width / canvas.width,
            y: image.height / canvas.height,
        }
    }

    function cropAction(event)
    {
        if (!this.classList.contains('active-button')) {
            return false;
        }
        event.preventDefault();
        var cropC = document.createElement('canvas'),
            context = cropC.getContext('2d'),
            left = eState.oLeft - eState.minLeft,
            top = eState.oTop - eState.minTop,
            width = $g('.crop-width').val(),
            height = $g('.crop-height').val();
        cropC.width = width;
        cropC.height = height;
        context.save();
        context.clearRect(0, 0, cropC.width, cropC.height);
        context.drawImage(image, left * prop.x, top * prop.y, width, height, 0, 0, width, height);
        context.restore();
        image.onload = function(){
            getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            checkSaveBtn();
        }
        if (lastAction && lastAction != 'cropAction') {
            setRangeValue($g('.photo-editor-quality'), 100);
            orig.onload = function(){};
            orig.src = image.src;
        }
        image.src = cropC.toDataURL(type, 0.90);
        lastAction = 'cropAction';
        $g('#resize-image-options .active-button').removeClass('active-button');
        $g('#crop-image-options .reset-image').addClass('active-button');
    }

    function adjustAction()
    {
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return false;
        }
        var cropC = document.createElement('canvas'),
            context = cropC.getContext('2d');
        if (lastAction && lastAction != 'adjustAction') {
            setRangeValue($g('.photo-editor-quality'), 100);
            orig.onload = function(){};
            orig.src = image.src;
        }
        cropC.width = image.width;
        cropC.height = image.height;
        context.save();
        context.clearRect(0, 0, cropC.width, cropC.height);
        var str = '';
        $g('#adjust-image-options [data-filter]').each(function(){
            var value = this.value < this.min ? this.min : this.value;
            if (this.dataset.filter == 'blur') {
                value += 'px';
            } else {
                value += '%';
            }
            str += this.dataset.filter+'('+value+') ';
        });
        context.filter = str;
        context.drawImage(image, 0, 0, orig.width, orig.height, 0, 0, cropC.width, cropC.height);
        context.restore();
        image.onload = function(){
            getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            checkSaveBtn();
        }
        image.src = cropC.toDataURL(type, 0.90);
        lastAction = 'adjustAction';
        resetFilters();
        $g('#photo-editor-dialog .active-button').removeClass('active-button');
        $g('#adjust-image-options .reset-image').addClass('active-button');
    }

    function filterAction(event)
    {
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return false;
        }
        var cropC = document.createElement('canvas'),
            context = cropC.getContext('2d');
        if (lastAction && lastAction != 'filterAction') {
            setRangeValue($g('.photo-editor-quality'), 100);
            orig.onload = function(){};
            orig.src = image.src;
        }
        cropC.width = image.width;
        cropC.height = image.height;
        context.save();
        context.clearRect(0, 0, cropC.width, cropC.height);
        context.filter = effect.filter;
        context.drawImage(image, 0, 0, orig.width, orig.height, 0, 0, cropC.width, cropC.height);
        applyEffect(effect, context, cropC);
        context.restore();
        image.onload = function(){
            getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            checkSaveBtn();
        }
        image.src = cropC.toDataURL(type, 0.90);
        lastAction = 'filterAction';
        $g('#photo-editor-dialog .active-button').removeClass('active-button');
        $g('.filter-effects-action').removeClass('active-button');
        $g('#filter-effects-image-options .reset-image').addClass('active-button');
    }

    function flipRotateAction(event)
    {
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return false;
        }
        var cropC = document.createElement('canvas'),
            context = cropC.getContext('2d');
        if (lastAction && lastAction != 'flipRotateAction') {
            setRangeValue($g('.photo-editor-quality'), 100);
            orig.onload = function(){};
            orig.src = image.src;
        }
        if (angle == 90 || angle == 270) {
            cropC.width = image.height;
            cropC.height = image.width;
        } else {
            cropC.width = image.width;
            cropC.height = image.height;
        }
        context.translate(cropC.width / 2, cropC.height / 2);
        context.rotate(angle * Math.PI / 180);
        context.scale(scaleH, scaleV);
        context.drawImage(image, -image.width / 2, -image.height / 2);
        image.onload = function(){
            getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            keepProportion();
            checkSaveBtn();
        }
        image.src = cropC.toDataURL(type, 0.90);
        lastAction = 'flipRotateAction';
        $g('#photo-editor-dialog .active-button').removeClass('active-button');
        $g('.flip-rotate-action').removeClass('active-button');
        $g('#flip-rotate-image-options .reset-image').addClass('active-button');
        $g('#photo-editor-dialog ul').off('click');
    }

    function resizeAction(event)
    {
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return false;
        }
        var cropC = document.createElement('canvas'),
            context = cropC.getContext('2d'),
            width = $g('.resize-width').val(),
            quality = $g('.photo-editor-quality').val() / 100,
            height = $g('.resize-height').val();
        if (lastAction && lastAction != 'resizeAction') {
            setRangeValue($g('.photo-editor-quality'), 100);
            orig.onload = function(){};
            orig.src = image.src;
        }
        if (quality >= 0.90) {
            quality = 0.90;
        }
        cropC.width = width;
        cropC.height = height;
        context.save();
        context.clearRect(0, 0, cropC.width, cropC.height);
        context.drawImage(orig, 0, 0, orig.width, orig.height, 0, 0, width, height);
        context.restore();
        image.onload = function(){
            getImageSize(canvasSize.width, canvasSize.height, this.width, this.height);
            ctx.save();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, eState.imgWidth, eState.imgHeight);
            ctx.restore();
            scaleH = 1;
            scaleV = 1;
            angle = 0;
            saveProportion();
            hideOverlay();
            checkSaveBtn();
        }
        image.src = cropC.toDataURL(type, quality);
        lastAction = 'resizeAction';
        $g('#photo-editor-dialog .active-button').removeClass('active-button');
        $g('#resize-image-options .reset-image').addClass('active-button');
    }
}

function setRangeValue(input, value)
{
    var range = input.val(value).prev().val(value);
    setLinearWidth(range);
}

app.photoEditor = function(){
    var canvas = document.getElementById('photo-editor')
        ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.restore();
    $g('.crop-width, .crop-height, .resize-width, .resize-height').val('');
    $g('.photo-editor-save-image').removeAttr('data-context');
    setRangeValue($g('.photo-editor-quality'), 100);
    $g('#photo-editor-dialog .active').removeClass('active');
    $g('#photo-editor-dialog').find('ul li:first, #resize-image-options').addClass('active');
    $g('.resize-image-wrapper').removeClass('crop-enabled');
    $g('.resize-image-wrapper').addClass('crop-disabled');
    $g('#photo-editor-dialog .resize-image-wrapper').removeClass('photo-editor-loaded');
    $g('#photo-editor-dialog').modal().find('.active-button').removeClass('active-button');
    $g('#photo-editor-dialog ul').off('click');
    $g('#photo-editor-dialog').removeClass('disabled-photo-editor');
    setTimeout(function(){
        photoEditor(JUri+app.itemDelete.url+'?'+(+new Date()));
    }, 600);
}
app.photoEditorQuality = function(){
    $g('#resize-image-options .resize-action').addClass('active-button');
}
app.photoEditorFilters = function(){
    $g('#adjust-image-options').trigger('adjustChange');
}
$g('#photo-editor-dialog .nav-tabs').on('show', function(event){
    var className = event.target.hash == '#crop-image-options' ? 'crop-enabled' : 'crop-disabled';
    $g('.resize-image-wrapper').removeClass('crop-enabled crop-disabled').addClass(className);
});
$g('#photo-editor-dialog').on('mousedown', function(){
    $g('.save-image-context-menu').hide();
});
$g('.save-image-context-menu').on('mousedown', function(event){
    event.stopPropagation();
    this.style.display = 'none';
});
$g('#photo-editor-dialog').on('hide', function(){
    $g(window).off('resize.photoEditor');
});

app.photoEditor();
app.modules.photoEditor = true;