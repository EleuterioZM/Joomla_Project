/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (window.themeData && themeData.page.view == 'gridbox') {
    app.checkModule('setGalleryMasonryHeight');
}

app.initSimpleGallery = function(obj, key){
    var wrapper = $g('#'+key+' .instagram-wrapper');
    wrapper.off('click.lightbox').on('click.lightbox', '.ba-instagram-image', function(){
        var div = document.createElement('div'),
            index = 0,
            $this = this,
            endCoords = startCoords = {},
            image = this.querySelector('img'),
            images = [],
            width = $g(this).width(),
            height = $g(this).height(),
            offset = $g(this).offset(),
            modal = $g(div),
            img = document.createElement('div');
        img.style.backgroundImage = 'url('+image.src+')';
        div.className = 'ba-image-modal instagram-modal';
        img.style.top = (offset.top - $g(window).scrollTop())+'px';
        img.style.left = offset.left+'px';
        img.style.width = width+'px';
        img.style.height = height+'px';
        div.style.backgroundColor = app.getCorrectColor(app.items[key].lightbox.color);
        div.appendChild(img);
        this.parentNode.imageOffset = offset;
        modal.on('click', function(){
            simpleModalClose(modal, images, index)
        }).on('touchstart', function(event){
            endCoords = event.originalEvent.targetTouches[0];
            startCoords.pageX = event.originalEvent.targetTouches[0].pageX;
            startCoords.pageY = event.originalEvent.targetTouches[0].pageY;
        }).on('touchmove', function(event){
            endCoords = event.originalEvent.targetTouches[0];
        }).on('touchend', function(event){
            var vDistance = endCoords.pageY - startCoords.pageY,
                hDistance = endCoords.pageX - startCoords.pageX,
                xabs = Math.abs(endCoords.pageX - startCoords.pageX),
                yabs = Math.abs(endCoords.pageY - startCoords.pageY);
            if(hDistance >= 100 && xabs >= yabs) {
                index = simpleGetPrev(img, images, index);
            } else if (hDistance <= -100 && xabs >= yabs) {
                index = simpleGetNext(img, images, index);
            }
        });
        $g('body').append(div);
        setSimpleImage(image);
        setTimeout(function(){
            var str = '';
            if (images.length > 1) {
                str += '<i class="ba-icons ba-icon-chevron-left"></i><i class="ba-icons ba-icon-chevron-right"></i>';
            }
            str += '<i class="ba-icons ba-icon-close">';
            modal.append(str);
            modal.find('.ba-icon-chevron-left').on('click', function(event){
                event.stopPropagation();
                index = simpleGetPrev(img, images, index);
            });
            modal.find('.ba-icon-chevron-right').on('click', function(event){
                event.stopPropagation();
                index = simpleGetNext(img, images, index);
            });
            modal.find('.ba-icon-close').on('click', function(event){
                event.stopPropagation();
                simpleModalClose(modal, images, index)
            });
        }, 600);
        wrapper.find('.ba-instagram-image').each(function(ind){
            if (!this.classList.contains('ba-unpublished-html-item')) {
                images.push(this);
            }
            if (this == $this) {
                index = ind;
            }
        });
        $g(window).on('keyup.instagram', function(event) {
            event.preventDefault();
            event.stopPropagation();
            if (event.keyCode === 37) {
                index = simpleGetPrev(img, images, index);
            } else if (event.keyCode === 39) {
                index = simpleGetNext(img, images, index);
            } else if (event.keyCode === 27) {
                simpleModalClose(modal, images, index)
            }
        });
    });
    initItems();
}

function setSimpleImage(image)
{
    var imgHeight = image.naturalHeight,
        imgWidth = image.naturalWidth,
        modal = $g('.ba-image-modal.instagram-modal').removeClass('instagram-fade-animation'),
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
            'width' : Math.round(imgWidth),
            'height' : Math.round(imgHeight),
            'left' : Math.round(left),
            'top' : Math.round(modalTop)
        }).addClass('instagram-fade-animation');
    }, 1);
}

function simpleGetPrev(img, images, index)
{
    var ind = images[index - 1] ? index - 1 : images.length - 1;
    image = images[ind].querySelector('img');
    img.style.backgroundImage = 'url('+image.src+')';
    setSimpleImage(image);

    return ind;
}

function simpleGetNext(img, images, index)
{
    var ind = images[index + 1] ? index + 1 : 0;
    image = images[ind].querySelector('img');
    img.style.backgroundImage = 'url('+image.src+')';
    setSimpleImage(image);

    return ind;
}

function simpleModalClose(modal, images, index)
{
    $g(window).off('keyup.instagram');
    modal.addClass('image-lightbox-out');
    var $image = $g(images[index]), 
        width = $image.width(),
        height = $image.height(),
        offset = $image.offset();
    if ($image.closest('.ba-flipbox-backside').length > 0) {
        offset = images[index].parentNode.imageOffset;
    }
    modal.find('> div').css({
        'width' : width,
        'height' : height,
        'left' : offset.left,
        'top' : offset.top - $g(window).scrollTop()
    });
    setTimeout(function(){
        modal.remove();
    }, 500);
}

if (app.modules.initSimpleGallery) {
    app.initSimpleGallery(app.modules.initSimpleGallery.data, app.modules.initSimpleGallery.selector);
}