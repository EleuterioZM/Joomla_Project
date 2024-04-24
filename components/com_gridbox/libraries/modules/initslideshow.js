/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!$g.fn.slideshow) {
    var file = document.createElement('link');
    file.rel = 'stylesheet';
    file.href = JUri+'components/com_gridbox/libraries/slideshow/css/animation.css';
    document.head.append(file);
    file = document.createElement('script');
    file.onload = function(){
        if (app.modules.initslideshow) {
            app.initslideshow(app.modules.initslideshow.data, app.modules.initslideshow.selector);
        }
    }
    file.src = JUri+'components/com_gridbox/libraries/slideshow/js/slideshow.js';
    document.head.append(file);
} else if (app.modules.initslideshow) {
    app.initslideshow(app.modules.initslideshow.data, app.modules.initslideshow.selector);
}
app.videoSlides = {};

function onPlayerSlideshowReady(event)
{
    var obj = event.target,
        iframe = id = ind = null;
    for (var key in obj) {
        if (typeof(obj[key]) == 'object' && obj[key].localName == 'iframe') {
            id = obj[key].id;
            iframe = obj[key];
            break;
        }
    }
    ind = $g(iframe).closest('.ba-item')[0].id;
    var object = app.videoSlides[ind][id];
    if (object.mute) {
        event.target.mute();
    }
    if ($g(iframe).closest('li.item').hasClass('active') && $g(window).width() > 1024) {
        event.target.playVideo();
    }
}

app.initVideoSlides = function(obj){
    for (var ind in app.videoSlides[obj.key]) {
        var object = app.videoSlides[obj.key][ind];
        if (object.type == 'youtube' && app.youtube && !object.player) {
            object.player = new YT.Player(ind, {
                width: 1360,
                height: 765,
                videoId: object.id,
                playerVars: {
                    controls: 0,
                    showinfo: 0,
                    modestbranding: 1,
                    loop : 1,
                    start : object.start * 1,
                    autohide: 1,
                    iv_load_policy: 3,
                    wmode: 'transparent',
                    vq: object.quality
                },
                events: {
                    'onReady': onPlayerSlideshowReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        } else if (object.type == 'vimeo' && app.vimeo && !object.player) {
            var options = {
                    autopause: false,
                    background: true,
                    id: object.id,
                    loop: true,
                    byline : false,
                    portrait : false,
                    title : false
                },
                object = app.videoSlides[obj.key][ind];
            app.videoSlides[obj.key][ind].player = new Vimeo.Player(ind, options);
            if (object.mute) {
                object.player.setVolume(0);
            }
            object.player.setCurrentTime(object.start * 1);
            if ($g('#'+ind).closest('li.item').hasClass('active') && $g(window).width() > 1024) {
                object.player.play();
            }
        } else if (object.type == 'source' && object.source) {
            var object = app.videoSlides[obj.key][ind];
            object.player = document.createElement("video");
            object.player.loop = true;
            object.player.innerHTML = '<source src="'+JUri+object.source+'" type="video/mp4">';
            $g('#'+ind).html(object.player);
            if (object.mute == 1) {
                object.player.muted = true;
            }
            if (!object.start) {
                object.start = 0;
            }
            object.player.currentTime += object.start;
            if ($g('#'+ind).closest('li.item').hasClass('active') && $g(window).width() > 1024) {
                object.player.play();
            }
        }
    }
}

app.initslideshow = function(obj, key){
    let videoType = {},
        i = 1,
        slide = li = id = div = null,
        slides = obj.desktop.slides ? obj.desktop.slides : obj.slides,
        query = '#'+key+':not(.ba-item-field-slideshow):not(.ba-item-product-slideshow) > .slideshow-wrapper',
        content = $g('#'+key+' .slideshow-content');
    app.videoSlides[key] = {};
    query += ' > .ba-slideshow > .slideshow-content > li';
    for (let ind in slides) {
        slide = slides[ind];
        if (!slide.unpublish) {
            li = document.querySelector(query+':nth-child('+(i++)+') > .ba-slideshow-img');
            id = li.firstElementChild.id;
            div = document.createElement('div');
            div.id = id;
            li.innerHTML = ''
            li.appendChild(div);
            if (slide && slide.video) {
                app.videoSlides[key][id] = $g.extend({}, slide.video);
                if (!videoType[app.videoSlides[key][id].type]) {
                    videoType[app.videoSlides[key][id].type] = app.videoSlides[key][id].type;
                }
            } else if (slide.desktop && slide.desktop.background.type == 'video') {
                app.videoSlides[key][id] = $g.extend({}, slide.desktop.background.video);
                if (!videoType[app.videoSlides[key][id].type]) {
                    videoType[app.videoSlides[key][id].type] = app.videoSlides[key][id].type;
                }
            }
        } else if (themeData.page.view == 'gridbox') {
            i++;
        }
    }
    if (content.find('li.item:not(.ba-unpublished-html-item)').length == 0) {
        content.addClass('empty-content');
    } else {
        content.removeClass('empty-content');
    }
    for (var ind in videoType) {
        if ((ind == 'youtube' && !app.youtube && videoType.vimeo && !app.vimeo) ||
            (ind == 'vimeo' && !app.vimeo && videoType.youtube && !app.youtube)) {
            var object = {
                    data : {
                        type : 'youtube+vimeo',
                        key : key
                    },
                    selector : null
                }
            app.checkModule('loadVideoApi', object);
            break;
        } else if (ind == 'youtube' && !app.youtube) {
            var object = {
                    data : {
                        type : 'youtube',
                        key : key
                    },
                    selector : null
                }
            app.checkModule('loadVideoApi', object);
        } else if (ind == 'vimeo' && !app.vimeo) {
            var object = {
                    data : {
                        type : 'vimeo',
                        key : key
                    },
                    selector : null
                }
            app.checkModule('loadVideoApi', object);
        } else if (ind == 'vimeo' && app.vimeo) {
            var object = {
                type : 'vimeo',
                key : key
            }
            app.initVideoSlides(object);
        } else if (ind == 'youtube' && app.youtube) {
            var object = {
                type : 'youtube',
                key : key
            }
            app.initVideoSlides(object);
        } else if (ind == 'source') {
            var object = {
                type : 'source',
                key : key
            }
            app.initVideoSlides(object);
        }
    }
    $g('#'+key+' > .slideshow-wrapper > ul.ba-slideshow').slideshow(obj.slideshow)
        .off('ba-slide').on('ba-slide', function(event){
        if (!document.getElementById(key)) {
            return false;
        }
        var prevSLide = $g(event.prevItem).find('.ba-slideshow-img'),
            thisSlide = $g(event.currentItem).find('.ba-slideshow-img'),
            id = prevSLide.children().attr('id'),
            object = app.videoSlides[key][id];
        if (object && object.player) {
            if (object.type == 'youtube' && typeof(object.player.pauseVideo) != 'undefined') {
                object.player.pauseVideo();
            } else if (object.type == 'vimeo' && typeof(object.player.pause) != 'undefined') {
                object.player.pause();
            } else if (object.type == 'source') {
                object.player.pause();
            }
        }
        var id = thisSlide.children().attr('id'),
            object = app.videoSlides[key][id];
        if (object && object.player) {
            if (object.type == 'youtube' && typeof(object.player.playVideo) != 'undefined') {
                if ($g(window).width() <= breakpoints["tablet"]) {
                    object.player.pauseVideo();
                } else {
                    object.player.playVideo();
                }
            } else if (object.type == 'vimeo' && typeof(object.player.play) != 'undefined') {
                if ($g(window).width() <= breakpoints["tablet"]) {
                    object.player.pause();
                } else {
                    object.player.play();
                }
            } else if (object.type == 'source') {
                if ($g(window).width() <= breakpoints["tablet"]) {
                    object.player.pause();
                } else {
                    object.player.play();
                }
            }
        }
    });
    initItems();
}