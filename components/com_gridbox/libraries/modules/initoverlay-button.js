/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var overlayVideo = {};

function overlayClose(item)
{
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe, .ba-item-field-video iframe');
    for (var i = 0; i < iframes.length; i++) {
        let src = iframes[i].src,
            videoId = iframes[i].id;
        if (src && src.indexOf('youtube.com') !== -1 && 'pauseVideo' in overlayVideo[videoId]) {
            overlayVideo[videoId].pauseVideo();
        } else if (src && src.indexOf('vimeo.com') !== -1 && 'pause' in overlayVideo[videoId]) {
            overlayVideo[videoId].pause();
        } else if (src && src.indexOf('youtube-nocookie.com') !== -1) {
            iframes[i].closest('.ba-video-wrapper').innerHTML = iframes[i].closest('.ba-video-wrapper').innerHTML;
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video, .ba-item-field-video video');
    for (var i = 0; i < iframes.length; i++) {
        let videoId = iframes[i].id;
        overlayVideo[videoId].pause();
    }
}

function overlayOpen(item)
{
    if (item.querySelectorAll('.video-lazy-load-thumbnail')) {
        $g(item).find('.video-lazy-load-thumbnail').trigger('click');
    }
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe, .ba-item-field-video iframe'),
        youtube = false,
        vimeo = false,
        id = +new Date();
    for (var i = 0; i < iframes.length; i++) {
        var src = iframes[i].src,
            videoId;
        if (src && src.indexOf('youtube.com') !== -1) {
            if (!app.youtube) {
                youtube = true;
            } else {
                if (src.indexOf('enablejsapi=1') === -1) {
                    if (src.indexOf('?') === -1) {
                        src += '?';
                    } else {
                        src += '&'
                    }
                    src += 'enablejsapi=1';
                    iframes[i].src = src;
                }
                if (!iframes[i].id) {
                    iframes[i].id = id++;
                }
                videoId = iframes[i].id;
                if (!overlayVideo[videoId] || !('playVideo' in overlayVideo[videoId])) {
                    overlayVideo[videoId] = new YT.Player(videoId, {
                        events: {
                            onReady: function(event){
                                if (item.classList.contains('visible-section')) {
                                    overlayVideo[videoId].playVideo();
                                }
                            }
                        }
                    });
                } else {
                    overlayVideo[videoId].playVideo();
                }
            }
        } else if (src && src.indexOf('vimeo.com') !== -1) {
            if (!app.vimeo) {
                vimeo = true;
            } else {
                if (!iframes[i].id) {
                    iframes[i].id = id++;
                }
                videoId = iframes[i].id;
                if (!overlayVideo[videoId] || !('play' in overlayVideo[videoId])) {
                    src = src.split('/');
                    src = src.slice(-1);
                    src = src[0].split('?');
                    src = src[0];
                    var options = {
                        id: src * 1,
                        loop: true,
                    };
                    overlayVideo[videoId] = new Vimeo.Player(videoId, options);
                }
                overlayVideo[videoId].play();
            }
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video, .ba-item-field-video video');
    for (var i = 0; i < iframes.length; i++) {
        if (!iframes[i].id) {
            iframes[i].id = id++;
        }
        videoId = iframes[i].id;
        if (!overlayVideo[videoId]) {
            overlayVideo[videoId] = iframes[i];
        }
        overlayVideo[videoId].play();
    }
    if (youtube || vimeo) {
        var object = {
            data : {}
        };
        if (youtube && !vimeo) {
            object.data.type = 'youtube';
        } else if (vimeo && !youtube) {
            object.data.type = 'vimeo';
        } else {
            object.data.type = 'youtube+vimeo';
        }
        app.checkModule('loadVideoApi', object);
    }
    if (youtube) {
        overlayVideo.overlay = item;
    } else if (vimeo) {
        overlayVideo.overlay = item;
    }

    return !youtube && !vimeo;
}

app['initoverlay-button'] = function(obj, key){
    var button = $g('#'+key)[0],
        id = button.id,
        overlay = button.dataset.overlay;
    $g('#'+key).on('click', ' > .ba-button-wrapper > a, > .ba-image-wrapper', function(event){
        event.preventDefault();
        openOverlaySection(button);
    });
    $g('.ba-overlay-section-backdrop[data-id="'+overlay+'"] .ba-overlay-section-close').on('click', function(){
        var item = $g(this).closest('.ba-overlay-section-backdrop');
        item.removeClass('visible-section').addClass('overlay-section-backdrop-out');
        setTimeout(function(){
            item.removeClass('overlay-section-backdrop-out');
        }, 400);
        if (!$g('.ba-overlay-section-backdrop').not(item).hasClass('visible-section')) {
            document.body.classList.remove('lightbox-open');
            document.body.classList.remove('ba-not-default-header');
            document.body.style.width = '';
            $g('body > header.header').css('width', '');
        }
        item.find('div.ba-overlay-section-close').css('width', '');
        $g('.ba-sticky-header').css('width', '');
        overlayClose(item[0]);
    }).on('mouseover', function(event){
        event.stopPropagation();
    });
    initItems();
}

function openOverlaySection(button)
{
    let overlay = button.dataset.overlay,
        item = document.querySelector('.ba-overlay-section-backdrop[data-id="'+overlay+'"]');
    if (app.items[overlay][app.view].disable == 1 && !document.body.classList.contains('show-hidden-elements')) {
        item.classList.remove('visible-section');
        document.body.classList.remove('lightbox-open');
        document.body.classList.remove('ba-not-default-header');
    } else {
        overlayOpen(item) ? openOverlay(item) : '';
    }
}

function openOverlay(item)
{
    var header = document.querySelector('body > header.header'),
        style = header ? getComputedStyle(header): {},
        width = window.innerWidth - document.documentElement.clientWidth,
        hWidth = width + (themeData.page.view == 'gridbox' && app.view == 'desktop' ? 103 : 0),
        calc = 'calc(100% - '+width+'px)',
        headerWidth = 'calc(100% - '+hWidth+'px)';
    document.body.style.width = calc;
    item.querySelector('div.ba-overlay-section-close').style.width = calc;
    if (header && header.classList.contains('sidebar-menu') && (app.view == 'desktop' || app.view == 'laptop')) {
        headerWidth = 'calc(100% - '+hWidth+'px - var(--sidebar-menu-width))';
    }
    $g('.ba-sticky-header').css('width', headerWidth);
    item.classList.add('visible-section');
    document.body.classList.add('lightbox-open');
    if (style.position == 'fixed') {
        $g('body > header.header').css('width', 'calc(100% - '+hWidth+'px)');
    }
    if (style.position != 'relative') {
        document.body.classList.add('ba-not-default-header');
    }
    $g(window).trigger("scroll");
}
if (app.modules['initoverlay-button']) {
    app['initoverlay-button'](app.modules['initoverlay-button'].data, app.modules['initoverlay-button'].selector);
}