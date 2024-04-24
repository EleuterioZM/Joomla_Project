/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.videoBg = {};

function getVideoTypeValue(key)
{
    var player = document.getElementById(key),
        parent = $g(player).closest('.ba-video-background').parent(),
        ind = parent.attr('id'),
        obj = app.items[ind];
    if (parent[0].localName == 'body') {
        obj = app.theme;
    }
    if (!obj && parent.closest('.column-wrapper').hasClass('ba-flipbox-frontside')) {
        var id = parent.closest('.ba-item-flipbox').attr('id');
        obj = app.items[id].sides.frontside;
    }
    if (!obj && parent.closest('.column-wrapper').hasClass('ba-flipbox-backside')) {
        var id = parent.closest('.ba-item-flipbox').attr('id');
        obj = app.items[id].sides.backside;
    }
    var object = $g.extend(true, {}, obj.desktop);
    if (app.view != 'desktop') {
        for (var ind in breakpoints) {
            if (!obj[ind]) {
                obj[ind] = {};
            }
            object = $g.extend(true, {}, object, obj[ind]);
            if (ind == app.view) {
                break;
            }
        }
    }

    return object.background.type
}

function videoResize()
{
    clearTimeout(delay);
    for (var key in app.videoBg) {
        if (!document.getElementById(key)) {
            delete(app.videoBg[key])
        }
    }
    delay = setTimeout(function(){
        if (app.view != 'desktop') {
            for (var key in app.videoBg) {
                var type = getVideoTypeValue(key);
                if (type != 'video') {
                    if (app.videoBg[key].type == 'youtube') {
                        app.videoBg[key].player.pauseVideo();
                    } else if (app.videoBg[key].type == 'vimeo') {
                        app.videoBg[key].player.pause();
                    } else if (app.videoBg[key].type == 'source') {
                        app.videoBg[key].player.pause();
                    }
                } else {
                    if (app.videoBg[key].type == 'youtube') {
                        app.videoBg[key].player.playVideo();
                    } else if (app.videoBg[key].type == 'vimeo') {
                        app.videoBg[key].player.play();
                    } else if (app.videoBg[key].type == 'source') {
                        app.videoBg[key].player.play();
                    }
                }
            }
        } else {
            for (var key in app.videoBg) {
                if (app.videoBg[key].type == 'youtube') {
                    app.videoBg[key].player.playVideo();
                } else if (app.videoBg[key].type == 'vimeo') {
                    app.videoBg[key].player.play();
                } else if (app.videoBg[key].type == 'source') {
                    app.videoBg[key].player.play();
                }
            }
        }
    }, 300);
}

window.addEventListener('resize', videoResize);

app.createVideo = function(obj, selector) {
    if (!obj) {
        initAllVideoBackgrounds();
        return false;
    }
    if (!selector) {
        selector = 'body'
    }
    var id = new Date().getTime()+'',
        old = $g(selector+' > .ba-video-background'),
        oldId,
        str = '<div class="ba-video-background';
        if (selector == 'body') {
            str += ' global-video-bg';
        }
        str += '"><div id="'+id+'"></div></div>';
    if (old.length > 0) {
        oldId = old.find('iframe')[0];
        if (oldId) {
            oldId = oldId.id;
        }
        oldId = old.find('video').parent[0];
        if (oldId) {
            oldId = oldId.id;
        }
        old.remove();
        delete(app.videoBg[oldId]);
    }
    $g(selector).append(str);
    app.setVideo(id, obj);
}

app.setVideo = function(id, obj){
    if ((obj.type == 'youtube' && !app.youtube) || (obj.type == 'vimeo' && !app.vimeo)) {
        var object = {
            data : obj,
            selector : id
        }
        app.checkModule('loadVideoApi', object);
    } else if (obj.type == 'youtube') {
        app.videoBg[id] = $g.extend({}, obj);
        app.videoBg[id].player = new YT.Player(id, {
            host: obj.nocookie ? 'https://www.youtube-nocookie.com' : 'https://www.youtube.com',
            width: 1360,
            height: 765,
            videoId: obj.id,
            playerVars: {
                controls: 0,
                showinfo: 0,
                modestbranding: 1,
                loop : 1,
                start : obj.start,
                autohide: 1,
                iv_load_policy: 3,
                wmode: 'transparent',
                vq: obj.quality
            },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    } else if (obj.type == 'vimeo') {
        var options = {
            autopause: false,
            background: true,
            id: obj.id,
            loop: true
        };
        app.videoBg[id] = $g.extend({}, obj);
        app.videoBg[id].player = new Vimeo.Player(id, options);
        if (obj.mute == 1) {
            app.videoBg[id].player.setVolume(0);
        }
        if (!obj.start) {
            obj.start = 0;
        }
        app.videoBg[id].player.setCurrentTime(obj.start);
        var type = getVideoTypeValue(id);
        if (type == 'video') {
            app.videoBg[id].player.play();
        }
    } else if (obj.type == 'source') {
        app.videoBg[id] = $g.extend({}, obj);
        app.videoBg[id].player = document.createElement("video");
        app.videoBg[id].player.setAttribute('playsinline', '');
        app.videoBg[id].player.setAttribute('webkit-playsinline', '');
        app.videoBg[id].player.loop = true;
        app.videoBg[id].player.innerHTML = '<source src="'+(app.isExternal(obj.source) ? '' : JUri)+obj.source+'" type="video/mp4">';
        $g('#'+id).append(app.videoBg[id].player);
        if (obj.mute == 1) {
            app.videoBg[id].player.muted = true;
            app.videoBg[id].player.setAttribute('muted', true);
        }
        if (!obj.start) {
            obj.start = 0;
        }
        app.videoBg[id].player.currentTime += obj.start;
        var type = getVideoTypeValue(id);
        if (type == 'video') {
            app.videoBg[id].player.play();
        }
    }
}

function initAllVideoBackgrounds()
{
    $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
        if (app.items[this.id] && app.items[this.id].desktop.background.type == 'video') {
            if (!app.items[this.id].desktop.video) {
                app.items[this.id].desktop.video = $g.extend(true, {}, app.items[this.id].desktop.background.video);
            }
            app.createVideo(app.items[this.id].desktop.video, '#'+this.id);
        }
    });
    $g('.ba-item-flipbox').each(function(){
        if (app.items[this.id] && app.items[this.id].sides.frontside.desktop.background.type == 'video') {
            var id = this.querySelector('.ba-flipbox-frontside > .ba-grid-column-wrapper > .ba-grid-column').id;
            app.createVideo(app.items[this.id].sides.frontside.desktop.video, '#'+id);
        }
        if (app.items[this.id] && app.items[this.id].sides.backside.desktop.background.type == 'video') {
            var id = this.querySelector('.ba-flipbox-backside > .ba-grid-column-wrapper > .ba-grid-column').id;
            app.createVideo(app.items[this.id].sides.backside.desktop.video, '#'+id);
        }
    });
    if (app.theme.desktop.background.type == 'video') {
        if (!app.theme.desktop.video) {
            app.theme.desktop.video = $g.extend(true, {}, app.theme.desktop.background.video);
        }
        app.createVideo(app.theme.desktop.video, 'body');
    }
}

initAllVideoBackgrounds();