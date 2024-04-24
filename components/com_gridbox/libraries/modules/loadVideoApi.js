/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function onYouTubeIframeAPIReady()
{
    app.youtube = true;
    if (typeof(app.setVideo) != 'undefined' && app.video.ind) {
        app.checkVideoBackground();
    }
    if (typeof(app.initVideoSlides) != 'undefined') {
        app.initVideoSlides(app.video);
    }
    if (window.overlayVideo && overlayVideo.overlay && overlayOpen(overlayVideo.overlay)) {
        overlayVideo.overlay.classList.add('visible-section');
        document.body.classList.add('lightbox-open');
    }
    if (window.lightboxVideo && lightboxVideo.overlay) {
        showLightbox(lightboxVideo.overlay);
    }
}

function slidesVideoResize()
{
    for (var key in app.videoSlides) {
        if (!document.getElementById(key)) {
            delete(app.videoSlides[key])
        } else {
            for (var ind in app.videoSlides[key]) {
                if (!document.getElementById(ind)) {
                    delete(app.videoSlides[key][ind]);
                }
            }
        }
    }
    if (app.view != 'desktop') {
        for (var key in app.videoSlides) {
            for (var ind in app.videoSlides[key]) {
                if (app.videoSlides[key][ind].type == 'youtube') {
                    app.videoSlides[key][ind].player.pauseVideo();
                } else if (app.videoSlides[key][ind].type == 'vimeo') {
                    app.videoSlides[key][ind].player.pause();
                } else if (app.videoSlides[key][ind].type == 'source') {
                    app.videoSlides[key][ind].player.pause();
                }
            }
        }
    } else {
        for (var key in app.videoSlides) {
            for (var ind in app.videoSlides[key]) {
                if ($g('#'+ind).closest('li.item').hasClass('active')) {
                    if (app.videoSlides[key][ind].type == 'youtube') {
                        app.videoSlides[key][ind].player.playVideo();
                    } else if (app.videoSlides[key][ind].type == 'vimeo') {
                        app.videoSlides[key][ind].player.play();
                    } else if (app.videoSlides[key][ind].type == 'source') {
                        app.videoSlides[key][ind].player.play();
                    }
                }
            }
        }
    }
}

window.addEventListener('resize', slidesVideoResize);

function onPlayerReady(event)
{
    var obj = event.target,
        id;
    for (var key in obj) {
        if (typeof(obj[key]) == 'object' && obj[key].localName == 'iframe') {
            id = obj[key].id;
            break;
        }
    }
    var type = getVideoTypeValue(id);
    if (app.videoBg[id].mute == 1) {
        event.target.mute();
    }
    if (type == 'video') {
        event.target.playVideo();
    }
}

function onPlayerStateChange(state)
{
    if (state.data === 0) {
        state.target.playVideo();
    }
}

app.loadVideoApi = function(obj, id){
    if (obj.type == 'youtube+vimeo') {
        var object = {
            type : 'youtube',
            key : obj.key
        }
        app.loadVideoApi(object);
        var object = {
            type : 'vimeo',
            key : obj.key
        }
        app.loadVideoApi(object);
        return false;
    }
    var tag = document.createElement('script');
    app.video = obj;
    app.video.ind = id;
    if (obj.type == 'youtube') {
        if (!window['YT']) {
            window.YT = {
                loading: 0,
                loaded: 0
            };
        }
        if (!window['YTConfig']) {
            window.YTConfig = {
                'host': 'http://www.youtube.com'
            };
        }
        if (!YT.loading) {
            YT.loading = 1;
            var l = [];
            YT.ready = function(f) {
                if (YT.loaded) {
                    f();
                } else {
                    l.push(f);
                }
            };
            window.onYTReady = function() {
                YT.loaded = 1;
                for (var i = 0; i < l.length; i++) {
                    try {
                        l[i]();
                    }
                    catch (e) {}
                }
            };
            YT.setConfig = function(c) {
                for (var k in c) {
                    if (c.hasOwnProperty(k)) {
                        YTConfig[k] = c[k];
                    }
                }
            };
        }
        tag.id = 'www-widgetapi-script';
        tag.src = 'https://s.ytimg.com/yts/jsbin/www-widgetapi-vflLM1tGT/www-widgetapi.js';
        tag.async = true;
    } else {
        tag.onload = function(){
            app.vimeo = true;
            if (typeof(app.setVideo) != 'undefined') {
                app.checkVideoBackground();
            }
            if (typeof(app.initVideoSlides) != 'undefined') {
                app.initVideoSlides(obj);
            }
            if (window.overlayVideo && overlayVideo.overlay && overlayOpen(overlayVideo.overlay)) {
                overlayVideo.overlay.classList.add('visible-section');
                document.body.classList.add('lightbox-open');
            }
            if (window.lightboxVideo && lightboxVideo.overlay) {
                showLightbox(lightboxVideo.overlay);
            }
        }
        tag.src = "https://player.vimeo.com/api/player.js";
    }
    document.head.appendChild(tag);
}

app.loadVideoApi(app.modules.loadVideoApi.data, app.modules.loadVideoApi.selector);