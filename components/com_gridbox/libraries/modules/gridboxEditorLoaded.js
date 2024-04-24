/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.buttonsPrevent = function(){
    $g('a, input[type="submit"], button').on('click', function(event){
        event.preventDefault();
    });
}

app.checkAnimation = function(){
    app.viewportItems = [];
    app.motionItems = [];
    $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
        if (app.items[this.id]) {
            let object = $g.extend(true, {}, app.items[this.id].desktop.animation),
                motion = app.items[this.id].desktop.motions ? $g.extend(true, {}, app.items[this.id].desktop.motions) : {},
                inMotion = false,
                motions = {
                    desktop: motion
                };
            if (app.view != 'desktop') {
                for (let ind in breakpoints) {
                    if (!app.items[this.id][ind]) {
                        app.items[this.id][ind] = {
                            animation : {}
                        };
                    }
                    object = $g.extend(true, {}, object, app.items[this.id][ind].animation);
                    if (ind == app.view) {
                        break;
                    }
                }
            }
            for (let ind in breakpoints) {
                if (!app.items[this.id][ind]) {
                    app.items[this.id][ind] = {
                        animation : {}
                    };
                }
                motion = app.items[this.id][ind].motions ? $g.extend(true, {}, motion, app.items[this.id][ind].motions) : motion;
                motions[ind] = motion;
            }
            if (object.effect && app.items[this.id].type != 'sticky-header') {
                app.viewportItems.push({
                    animation: object,
                    item: $g(this)
                });
            } else if (!object.effect && app.items[this.id].type != 'sticky-header' && $g(this).viewportChecker) {
                $g(this).viewportChecker(object)
            } else if (object.effect) {
                this.classList.add('visible');
            }
            for (let ind in motions) {
                for (let key in motions[ind]) {
                    if (motions[ind][key].enable) {
                        app.motionItems.push({
                            motions: motions,
                            item: $g(this)
                        });
                        inMotion = true;
                        break;
                    }
                }
                if (inMotion) {
                    break;
                }
            }
            if ($g(this).motion && !inMotion) {
                $g(this).motion(motions);
            }
        }
    });
    $g('.ba-item').each(function(){
        if (app.items[this.id] && app.items[this.id].desktop && app.items[this.id].desktop.appearance) {
            let object = $g.extend(true, {}, app.items[this.id].desktop.appearance),
                motion = app.items[this.id].desktop.motions ? $g.extend(true, {}, app.items[this.id].desktop.motions) : {},
                inMotion = false,
                motions = {
                    desktop: motion
                };
            if (app.view != 'desktop') {
                for (let ind in breakpoints) {
                    if (!app.items[this.id][ind]) {
                        app.items[this.id][ind] = {
                            appearance : {}
                        };
                    }
                    object = $g.extend(true, {}, object, app.items[this.id][ind].appearance);
                    if (ind == app.view) {
                        break;
                    }
                }
            }
            for (let ind in breakpoints) {
                motion = app.items[this.id][ind].motions ? $g.extend(true, {}, motion, app.items[this.id][ind].motions) : motion;
                motions[ind] = motion;
            }
            if (object.effect) {
                app.viewportItems.push({
                    animation: object,
                    item: $g(this)
                });
            } else if (!object.effect && $g(this).viewportChecker) {
                $g(this).viewportChecker(object)
            } else if (object.effect) {
                this.classList.add('visible');
            }
            for (let ind in motions) {
                for (let key in motions[ind]) {
                    if (motions[ind][key].enable) {
                        app.motionItems.push({
                            motions: motions,
                            item: $g(this)
                        });
                        inMotion = true;
                        break;
                    }
                }
                if (inMotion) {
                    break;
                }
            }
            if ($g(this).motion && !inMotion) {
                $g(this).motion(motions);
            }
        }
    });
    if (app.viewportItems.length > 0 || app.motionItems.length > 0) {
        app.checkModule('loadAnimations');
    }
}

app.checkOverlay = function(obj, key){
    $g('.ba-item-overlay-section').each(function(){
        $g(this).find('.ba-overlay-section-backdrop').appendTo(document.body);
    });
}

app.setMediaRules = function(obj, key, callback){
    let desktop =  $g.extend(true, {}, obj.desktop),
        str = '';
    if (disableResponsive) {
        return str;
    }
    for (let ind in breakpoints) {
        app.breakpoint = ind;
        if (!obj[ind]) {
            obj[ind] = {};
        }
        let object = $g.extend(true, {}, desktop, obj[ind]);
        for (let i in object) {
            let sub = obj[ind][i] ? $g.extend(true, {}, obj[ind][i]) : null;
            if (object[i].default && sub && !sub.default && !sub.hover && !sub.active) {
                for (let a in obj[ind][i]) {
                    delete obj[ind][i][a];
                }
                obj[ind][i].default = (sub.normal ? sub.normal : sub);
                object[i] = $g.extend(true, object[i], obj[ind][i]);
            } else if (typeof sub == 'object') {
                for (let j in sub) {
                    try {
                        if (object[i][j].default && !sub[j].default && !sub[j].hover && !sub[j].active) {
                            for (let a in obj[ind][i][j]) {
                                delete obj[ind][i][j][a];
                            }
                            obj[ind][i][j].default = sub[j].normal ? sub[j].normal : sub[j];
                            object[i][j] = $g.extend(true, object[i][j], obj[ind][i][j]);
                        }
                    } catch (e) {
                        
                    }
                }
            }
        }
        str += "@media (max-width: "+breakpoints[ind]+"px) {"
        str += window[callback](object, key, obj.type);
        str += "}";
        desktop =  $g.extend(true, {}, object);
    }
    
    return str;
}

app.checkVideoBackground = function(){
    var flag = false;
    $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
        if (app.items[this.id] && app.items[this.id].desktop.background
            && app.items[this.id].desktop.background.type == 'video') {
            flag = true;
            return false;
        }
    });
    $g('.ba-item-flipbox').each(function(){
        if (app.items[this.id] && app.items[this.id].sides.frontside.desktop.background
            && app.items[this.id].sides.frontside.desktop.background.type == 'video') {
            flag = true;
            return false;
        }
        if (app.items[this.id] && app.items[this.id].sides.backside.desktop.background
            && app.items[this.id].sides.backside.desktop.background.type == 'video') {
            flag = true;
            return false;
        }
    });
    if (app.theme.desktop.background.type == 'video') {
        flag = true;
    }
    if (flag) {
        app.checkModule('createVideo', {});
    }
}

app.listenMessage = function(obj){
    app.checkModule(obj.callback, obj);
}

app.checkView = function(){
    let width = $g(window).width();
    app.view = 'desktop';
    document.documentElement.style.setProperty('--scroll-width', (window.innerWidth - document.documentElement.offsetWidth)+'px');
    for (let ind in breakpoints) {
        if (width <= breakpoints[ind]) {
            app.view = ind;
        }
    }
}

app.resize = function(){
    clearTimeout(delay);
    app.checkView();
    delay = setTimeout(function(){
        app.checkAnimation();
        if ($g('.ba-item-map').length > 0) {
            $g('.ba-item-map').each(function(){
                app.initmap(app.items[this.id], this.id);
            });
        }
        if ('setPostMasonryHeight' in window) {
            $g('.ba-blog-posts-wrapper.ba-masonry-layout').each(function(){
                var key = $g(this).closest('.ba-item').attr('id');
                setPostMasonryHeight(key);
            });
        }
        if ('setGalleryMasonryHeight' in window) {
            $g('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                setGalleryMasonryHeight(this.closest('.ba-item').id);
            });
        }
        app.positioning.setOffsets();
    }, 300);
}

var lightboxVideo = {};

function lightboxVideoClose(item)
{
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe');
    for (var i = 0; i < iframes.length; i++) {
        var src = iframes[i].src,
            videoId = iframes[i].id;
        if (!lightboxVideo[videoId]) {
            continue;
        }
        if (src && src.indexOf('youtube.com') !== -1 && 'pauseVideo' in lightboxVideo[videoId]) {
            lightboxVideo[videoId].pauseVideo();
        } else if (src && src.indexOf('vimeo.com') !== -1 && 'pause' in lightboxVideo[videoId]) {
            lightboxVideo[videoId].pause();
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video');
    for (var i = 0; i < iframes.length; i++) {
        var videoId = iframes[i].id;
        if (!lightboxVideo[videoId]) {
            continue;
        }
        lightboxVideo[videoId].pause();
    }
}

function lightboxVideoOpen(item)
{
    var iframes = item.querySelectorAll('.ba-item-custom-html iframe, .ba-item-video iframe'),
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
                if (!lightboxVideo[videoId] || !('playVideo' in lightboxVideo[videoId])) {
                    lightboxVideo[videoId] = new YT.Player(videoId, {
                        events: {
                            onReady: function(event){
                                lightboxVideo[videoId].playVideo();
                            }
                        }
                    });
                } else {
                    lightboxVideo[videoId].playVideo();
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
                if (!lightboxVideo[videoId] || !('play' in lightboxVideo[videoId])) {
                    src = src.split('/');
                    src = src.slice(-1);
                    src = src[0].split('?');
                    src = src[0];
                    var options = {
                        autopause: false,
                        id: src * 1,
                        loop: true,
                    };
                    lightboxVideo[videoId] = new Vimeo.Player(videoId, options);
                }
                lightboxVideo[videoId].play();
            }
        }
    }
    iframes = item.querySelectorAll('.ba-item-video video, .ba-item-custom-html video');
    for (var i = 0; i < iframes.length; i++) {
        if (!iframes[i].id) {
            iframes[i].id = id++;
        }
        videoId = iframes[i].id;
        if (!lightboxVideo[videoId]) {
            lightboxVideo[videoId] = iframes[i];
        }
        lightboxVideo[videoId].play();
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
        lightboxVideo.overlay = item;
    } else if (vimeo) {
        lightboxVideo.overlay = item;
    }

    return !youtube && !vimeo;
}

function showLightbox($this)
{
    if (!lightboxVideoOpen($this)) {
        return false;
    }
    $this.classList.add('visible-lightbox');
    document.body.classList.add('ba-lightbox-open');
    if (app.items[app.edit].position == 'lightbox-center') {
        var width = window.innerWidth - document.documentElement.clientWidth;
        document.body.classList.add('lightbox-open');
        document.body.style.width = 'calc(100% - '+width+'px)';
    }
}

app.initStickyHeaderPanel = function($this){
    var title = app.items[$this.id].structureTitle ? app.items[$this.id].structureTitle : 'Sticky Header',
        div = window.parent.document.createElement('div'),
        str = '<i class="sticky-header-type-icon zmdi zmdi-'+($this.closest('header, footer') ? 'globe' : 'file'),
        panels = window.parent.document.getElementById('lightbox-panels');
    str += '"></i><p>'+title+'</p><span><i class="zmdi zmdi-edit"></i>'+
        '<span class="ba-tooltip settings-tooltip ba-top">Edit</span></span><span><i class="zmdi '+
        'zmdi-delete"></i><span class="ba-tooltip settings-tooltip ba-top">Delete</span></span>';
    div.dataset.id = $this.id;
    div.className = 'lightbox-options-panel';
    div.innerHTML = str;
    $this.closest('header, footer, .body').classList.add('ba-sticky-header-parent');
    panels.appendChild(div);
    $g(div).find('i.zmdi-delete').off('click').on('click', function(){
        $g('#'+this.parentNode.parentNode.dataset.id).find(' > .ba-edit-item .delete-item').trigger('mousedown');
    });
    $g(div).find('i.zmdi-edit').off('click').on('click', function(){
        var section = $g('#'+this.parentNode.parentNode.dataset.id),
            animation = app.items[this.parentNode.parentNode.dataset.id].desktop.animation,
            top = window.pageYOffset;
        section.addClass(animation.effect);
        document.body.classList.add('sticky-header-opened');
        setTimeout(function(){
            section.removeClass(animation.effect);
        }, animation.delay * 1 + animation.duration * 1000);
        section.parent().addClass('visible-sticky-header').css('top', 40 - top);
        section.find(' > .ba-edit-item .edit-item').trigger('mousedown');
    });
}

app.initLightboxPanel = function($this){
    if ($g($this).closest('.ba-item-blog-content').length > 0) {
        return false;
    }
    var div = window.parent.document.createElement('div'),
        title = app.items[$this.dataset.id] && app.items[$this.dataset.id].structureTitle ? app.items[$this.dataset.id].structureTitle : 'Lightbox',
        panel = '',
        panels = window.parent.document.getElementById('lightbox-panels');
    if (app.items[$this.dataset.id] && app.items[$this.dataset.id].type == 'cookies' && !app.items[$this.dataset.id].structureTitle) {
        title = 'Cookies'
    }
    panel += '<p>'+title+'</p><span><i class="zmdi zmdi-edit"></i><span class="ba-tooltip';
    panel += ' settings-tooltip ba-top">Edit</span></span>';
    if (app.items[$this.dataset.id] && app.items[$this.dataset.id].type == 'cookies') {
        panel += '<span><i class="zmdi zmdi-close"></i><span class="ba-tooltip';
        panel += ' settings-tooltip ba-top">Close</span></span>';
    }
    panel += '<span><i class="zmdi ';
    panel += 'zmdi-delete"></i><span class="ba-tooltip settings-tooltip ba-top">Delete</span></span>';
    div.dataset.id = $this.dataset.id;
    div.className = 'lightbox-options-panel';
    div.innerHTML = panel;
    panels.appendChild(div);
    $g(div).find('i.zmdi-delete').off('click').on('click', function(){
        $g('#'+this.parentNode.parentNode.dataset.id).find(' > .ba-edit-item .delete-item').trigger('mousedown');
    });
    $g(div).find('i.zmdi-close').off('click').on('click', function(){
        $g('.ba-lightbox-backdrop[data-id="'+this.parentNode.parentNode.dataset.id+'"]').removeClass('visible-lightbox');
        lightboxVideoClose($g('.ba-lightbox-backdrop[data-id="'+this.parentNode.parentNode.dataset.id+'"]')[0]);
        document.body.style.width = '';
        $g('body').removeClass('lightbox-open ba-lightbox-open');
    });
    $g(div).find('i.zmdi-edit').off('click').on('click', function(){
        $g('div.ba-lightbox-close').trigger('click');
        $g(panels).find('i.zmdi-close').trigger('click');
        app.edit = this.parentNode.parentNode.dataset.id;
        var item = document.querySelector('.ba-lightbox-backdrop[data-id="'+app.edit+'"]'),
            width = window.innerWidth - document.documentElement.clientWidth;
        if (app.items[app.edit][app.view].disable == 1 && !document.body.classList.contains('show-hidden-elements')) {
            item.classList.remove('visible-lightbox');
            document.body.classList.remove('lightbox-open');
            document.body.classList.remove('ba-lightbox-open');
            document.body.style.width = '';
        } else {
            showLightbox(item);
        }
        if (app.items[app.edit].type == 'cookies') {
            $g('#'+this.parentNode.parentNode.dataset.id).find(' > .ba-edit-item .edit-item').trigger('mousedown');
        } else {
            window.parent.app.edit = app.items[app.edit];
            window.parent.app.checkModule('lightboxEditor');
        }
    });
}

app.setSortable = function(){
    let search = 'header.header, footer.footer, #ba-edit-section',
        str = '> .ba-wrapper:not(.ba-lightbox):not(.ba-overlay-section):not(.ba-sticky-header)';
    str += ':not(.tabs-content-wrapper) > .ba-section > .ba-section-items';
    if (themeData.edit_type) {
        document.body.classList.add('ba-'+themeData.edit_type+'-editing');
    }
    makeRowSortable($g(search).find(str), 'row');
    str = '.tabs-content-wrapper > .ba-section > .ba-section-items'
    makeRowSortable($g(str), 'row');
    str = '.ba-wrapper:not(.ba-lightbox):not(.ba-overlay-section):not(.ba-sticky-header)';
    str += ' > .ba-section > .ba-section-items';
    str += ' > .ba-row-wrapper > .ba-row > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
    str += ', .ba-item-flipbox > .ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
    str += ', .ba-item-content-slider > .slideshow-wrapper > ul > .slideshow-content > li > .ba-grid-column';
    makeColumnSortable($g(search).find(str), 'column');
    str = ' > .ba-section > .ba-section-items';
    search = '.ba-lightbox, .ba-overlay-section, .ba-sticky-header';
    makeRowSortable($g(search).find(str), 'lightbox-row');
    search = '.ba-lightbox, .ba-overlay-section, .ba-wrapper[data-megamenu], .ba-sticky-header';
    str += ' > .ba-row-wrapper > .ba-row > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
    str += ', .ba-item .ba-wrapper .ba-row > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
    str += ', .ba-item-flipbox > .ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
    str += ', .ba-item-content-slider > .slideshow-wrapper > ul > .slideshow-content > li > .ba-grid-column';
    makeColumnSortable($g(search).find(str), 'lightbox-column');
}

app.init = function(){
    app.setSortable();
    app.buttonsPrevent();
    $g('.ba-section').each(function(){
        if ($g(this).closest('#ba-edit-section').length != 0 || $g('body').hasClass('blog-post-editor')) {
            $g(this).find('> .ba-edit-item .ba-buttons-wrapper').each(function(){
                if ($g(this).find('.ba-edit-wrapper').length != 5) {
                    this.innerHTML = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-plus-circle add-columns"></i>'+
                        '<span class="ba-tooltip tooltip-delay settings-tooltip">'+
                        top.app._('ADD_NEW_ROW')+'</span></span>'+
                        '<span class="ba-edit-wrapper"><i class="zmdi zmdi-edit edit-item"></i>'+
                        '<span class="ba-tooltip tooltip-delay settings-tooltip">'+
                        top.app._('EDIT')+'</span></span>'+
                        '<span class="ba-edit-wrapper"><i class="zmdi zmdi-copy copy-item"></i>'+
                        '<span class="ba-tooltip tooltip-delay settings-tooltip">'+
                        top.app._('COPY_ITEM')+'</span></span>'+
                        '<span class="ba-edit-wrapper"><i class="zmdi zmdi-globe add-library"></i>'+
                        '<span class="ba-tooltip tooltip-delay settings-tooltip">'+
                        top.app._('ADD_TO_LIBRARY')+'</span></span>'+
                        '<span class="ba-edit-wrapper"><i class="zmdi zmdi-delete delete-item"></i>'+
                        '<span class="ba-tooltip tooltip-delay settings-tooltip">'+
                        top.app._('DELETE_ITEM')+'</span></span>'+
                        '<span class="ba-edit-text">'+top.app._('SECTION')+'</span>';
                }
            });
        }
        if (this.closest('.ba-item-tabs') && app.items[this.id]
            && this.parentNode.classList.contains('tabs-content-wrapper')) {
            delete app.items[this.id];
        }
        editItem(this.id);
        setColumnResizer(this);
    });
    $g('.ba-item-preloader').each(function(){
        editItem(this.id);
    })
    $g('.ba-item').each(function(){
        if (app.items[this.id]) {
            var obj = {
                data : app.items[this.id],
                selector : this.id
            };
            itemsInit.push(obj);
        }
        if (this.classList.contains('ba-item-blog-content')) {
            if (this.querySelector('.ba-item')) {
                this.classList.remove('empty-blog-content');
            } else {
                this.classList.add('empty-blog-content');
            }
        }
    });
    if (itemsInit.length > 0) {
        app.checkModule('initItems', itemsInit.pop());
    }
    app.checkVideoBackground();
    $g('.ba-lightbox-backdrop').find('.ba-lightbox-close').off('click').on('click', function(){
        $g(this).closest('.ba-lightbox-backdrop').removeClass('visible-lightbox');
        document.body.style.width = '';
        $g('body').removeClass('lightbox-open');
        document.body.classList.remove('ba-lightbox-open');
        lightboxVideoClose($g(this).closest('.ba-lightbox-backdrop')[0]);
    });
    window.parent.document.getElementById('lightbox-panels').innerHTML = '';
    $g('.ba-lightbox').each(function(){
        app.initLightboxPanel(this);
    });
    $g('.ba-sticky-header > .ba-section').each(function(){
        app.initStickyHeaderPanel(this);
    });
    app.checkModule('loadParallax');
    app.positioning.init();
    $g('.ba-item-scroll-to-top, .ba-social-sidebar').each(function(){
        let column = this.closest('.ba-grid-column');
        if (column) {
            app.items[this.id].parent = column.id;
            document.body.append(this);
        }
    });
}

function restoreTabs(id)
{
    if (!app.items[id]) {
        var item = $g('#'+id),
            obj = null;
        if (item.hasClass('ba-section')) {
            obj = $g.extend(true, {}, top.defaultElementsStyle.section);
            obj.desktop.padding = {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        } else if (item.hasClass('ba-row')) {
            obj = $g.extend(true, {}, top.defaultElementsStyle.row);
        } else if (item.hasClass('ba-grid-column')) {
            obj = $g.extend(true, {}, top.defaultElementsStyle.column);
        } else if (item.hasClass('ba-item')) {
            var match = item[0].className.match(/ba-item-[-\w]+/);
            if (match) {
                var type = match[0].replace('ba-item-', '');
                if (top.defaultElementsStyle[type]) {
                    obj = $g.extend(true, {}, top.defaultElementsStyle[type]);
                }
            }
        }
        if (obj) {
            if (obj.desktop.margin) {
                obj.desktop.margin = {
                    top: 0,
                    bottom: 0
                }
            }
            app.items[id] = obj;
        }
    }
}

function getCKECSSrulesString()
{
    var str = 'body.cke_editable {font-family: Arial, Helevtica, sans-serif;}';
    str += ' body.cke_editable img {max-width: 100%;}';
    str += 'a { text-decoration: none; } :focus { outline: none; }';
    str += 'html {';
    for (let ind in app.theme.colorVariables) {
        str += ind.replace('@', '--')+': '+app.theme.colorVariables[ind].color+';';
    }
    str += '}';
    str += 'a[name] {border: 1px dotted #1da6f4;; padding: 0 5px 0 0;} a[name]:before ';
    str += '{content: "\\2693"; font-size: inherit; color: #1da6f4;padding: 0px 5px;}';
    str += 'pre {background: var(--bg-secondary); margin: 0; max-height: 400px; overflow-x: hidden;';
    str += 'overflow-y: auto; padding: 20px; text-decoration: none; text-transform: none;';
    str += ' white-space: pre-wrap; word-break: break-all;}';

    return str;
}

app.contextmenu = function(target, event, iframe){
    let stop = false;
    if ((target.classList.contains('ba-grid-column') && target.parentNode.localName == 'li')
        || (target.classList.contains('ba-section') && (target.closest('.ba-item-tabs') || target.closest('.ba-item-accordion')))) {
        stop = true;
    } else if (target.classList.contains('ba-item-checkout-order-form')) {
        stop = true;
        event.preventDefault();
        event.stopPropagation();
    }
    if (!stop) {
        restoreTabs(target.id);
        let type = app.items[target.id] ? app.items[target.id].type : '',
            flag = false,
            obj = {
                iframe: iframe,
                event: event,
                target: target,
                type: 'contextEvent'
            };
        type = type.replace('header', 'section').replace('footer', 'section').replace('overlay-section', 'section')
            .replace('lightbox', 'section').replace('cookies', 'section').replace('mega-menu-section', 'section')
            .replace('sticky-header', 'section');
        if (flag = (target.classList.contains('ba-section') && app.items[target.id] && type == 'section')) {
            obj.context = 'section-context-menu';
        } else if (flag = (target.classList.contains('ba-row') && app.items[target.id] && type == 'row')) {
            obj.context = 'row-context-menu';
        } else if (flag = (target.classList.contains('ba-grid-column') && app.items[target.id] && type == 'column')) {
            obj.context = 'column-context-menu';
        } else if (flag = (target.classList.contains('ba-item') && app.items[target.id] && type != 'blog-content')) {
            obj.context = 'plugin-context-menu';
        }
        if ($g(target).closest('.ba-user-level-edit-denied').length > 0) {
            flag = false;
        }
        if (flag) {
            obj.itemType = type;
            obj.item = app.items[target.id];
            top.app.context = obj;
            top.app.checkModule('showContext');
        }
        if (!target.hasAttribute('contenteditable')) {
            event.preventDefault();
        }
        event.stopPropagation();
    }
}

app.initGridboxEditor = function(){
    if (document.querySelector('.blog-post-editor-header-panel')) {
        app.blogEditor = {
            setSelection: function(){
                this.selection = window.getSelection();
                if (this.selection.rangeCount > 0) {
                    this.string = this.selection.toString();
                    this.html = this.selection.toString();
                    this.range = this.selection.getRangeAt(0);
                    this.start = this.range.startContainer;
                    this.end = this.range.endContainer;
                    this.startTags = $g(this.start).parentsUntil('.content-text');
                    this.endTags = $g(this.end).parentsUntil('.content-text');
                }
            },
            checkActive: function(){
                
            },
            copyPastText: function(){
                app.blogEditor.setSelection();
                app.blogEditor.checkActive();
                app.blogEditor.range.deleteContents();
                var content = $g(app.blogEditor.start).closest('.content-text'),
                    start = content.find('> *:first-child')[0],
                    data;
                app.blogEditor.range.setStartBefore(start);
                data = app.blogEditor.range.extractContents();
                if (app.blogEditor.end && app.blogEditor.end.localName
                    && app.blogEditor.end.firstChild.localName == 'br') {
                    app.blogEditor.end.firstChild.remove();
                }
                app.edit = content.closest('.ba-item-text')[0].id;
                if (!data.textContent && !data.querySelector('img') && data.querySelectorAll('p').length == 1) {
                    var target = content.closest('.ba-item-text').next();
                    content.closest('.ba-item-text').before(target);
                } else {
                    app.checkModule('copyItem');
                    var copyText = content.closest('.ba-item-text').next(),
                        target = copyText.next();
                    target.after(copyText);
                    content.closest('.content-text').html(data);
                }
                setTextPlaceholder(content[0]);
                $g('.blog-posts-add-plugins').hide();
                window.parent.app.addHistory();
            },
            insertPlugins: function(){
                app.copyAction = 'blogPostsText';
                app.edit = $g(app.blogEditor.start).closest('.ba-grid-column')[0].id;
                window.parent.app.checkModule('addPlugins');
            },
            insertImage: function(){
                app.copyAction = 'blogPostsText';
                app.edit = $g(app.blogEditor.start).closest('.ba-grid-column')[0].id;
                top.uploadMode = 'itemImage';
                top.checkIframe(top.$g('#uploader-modal').attr('data-check', 'single'), 'uploader');
            },
            insertVideo: function(){
                app.copyAction = 'blogPostsText';
                app.edit = $g(app.blogEditor.start).closest('.ba-grid-column')[0].id;
                var obj = {
                    data : 'video',
                    selector : 0,
                }
                app.checkModule('loadPlugin' , obj);
            }
        }
        // app.checkModule('copyItem');
        $g('body').on('mouseup', function(event){
            if (event.target && event.target.closest('.content-text')) {
                app.blogEditor.setSelection();
                app.blogEditor.checkActive();
            }
        });
        $g(document).on('mouseenter', '.content-text[title]', function(){
            $g(this).removeAttr('title');
        });
        $g('.advanced-blog-editor-toggle').on('change', function(){
            if (this.checked) {
                document.body.classList.add('advanced-blog-editor');
                top.document.body.classList.add('advanced-blog-editor');
            } else {
                document.body.classList.remove('advanced-blog-editor');
                top.document.body.classList.remove('advanced-blog-editor');
            }
            localStorage.setItem('advanced-blog-editor', this.checked);
        });
    }
    if (typeof(top.CKEDITOR) != 'undefined') {
        top.CKEDITOR.config.contentsCss = [getCKECSSrulesString()];
    }
    $g('#ba-edit-section').sortable({
        handle : '.ba-wrapper > .ba-section > .ba-edit-item .edit-settings',
        change: function(element){
            $g(element).find('.ba-item').each(function(){
                if (app.items[this.id]) {
                    initMapTypes(app.items[this.id].type, this.id);
                }
            });
            if (top.app.pageStructure && top.app.pageStructure.visible) {
                top.app.pageStructure.updateStructure(true);
            }
            window.parent.app.addHistory();
        },
        selector : '> .ba-wrapper:not(.ba-lightbox):not(.ba-overlay-section)',
        group : 'section'
    });
    $g('body').on('contextmenu', '.ba-item, .ba-row, .ba-section, .ba-grid-column', function(event){
        app.contextmenu(event.currentTarget, event, true);
    });
    $g('body').on('mouseover', '.ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column > .ba-item',
        function(event){
        var item = this,
            $this = $g(this),
            top = left = '';
        if (!$this.hasClass('sortable-helper') && !$this.hasClass('sortable-placeholder')) {
            var rect = item.getBoundingClientRect(),
                obj = app.items[item.id],
                parent = $this.closest('.ba-grid-column')[0].getBoundingClientRect();
            if (item.classList.contains('ba-row')) {
                top = rect.top - 25;
                left = rect.right - 100;
            } else {
                top = rect.top - 25 + ((rect.bottom - rect.top) / 2);
                left = parent.left - 25 + ((parent.right - parent.left) / 2);
            }
            if (obj && (obj.type == 'accordion' || obj.type == 'tabs')) {
                if (obj.type == 'tabs' && obj.position == 'tabs-left') {
                    left = rect.left + 10;
                } else if (obj.type == 'tabs' && obj.position == 'tabs-right') {
                    left = rect.right - 60;
                } else {
                    top = rect.top + 10;
                }
            }
        }
        $this.find('> .ba-edit-item').css({
            'top': top,
            'left': left
        });
    });
    $g(window).on('scroll', function(){
        $g('.ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column > .ba-item').each(function(){
            var item = this,
                $this = $g(this),
                top = left = '';
            if (!$this.hasClass('sortable-helper') && !$this.hasClass('sortable-placeholder')) {
                var rect = item.getBoundingClientRect(),
                    obj = app.items[item.id],
                    parent = $this.closest('.ba-grid-column')[0].getBoundingClientRect();
                if (item.classList.contains('ba-row')) {
                    top = rect.top - 25;
                    left = rect.right - 100;
                } else {
                    top = rect.top - 25 + ((rect.bottom - rect.top) / 2);
                    left = parent.left - 25 + ((parent.right - parent.left) / 2);
                }
                if (obj && (obj.type == 'accordion' || obj.type == 'tabs')) {
                    if (obj.type == 'tabs' && obj.position == 'tabs-left') {
                        left = rect.left + 10;
                    } else if (obj.type == 'tabs' && obj.position == 'tabs-right') {
                        left = rect.right - 60;
                    } else {
                        top = rect.top + 10;
                    }
                }
            }
            $this.find('> .ba-edit-item').css({
                'top': top,
                'left': left
            });
        });
    });
    $g('body').on('mousedown', function(event){
        let target = event.target.closest('.ba-item, .ba-grid-column, .ba-row, .ba-section');
        if (target && target.classList.contains('ba-grid-column') && (target.closest('.column-wrapper').classList.contains('ba-flipbox-frontside')
            || target.closest('.column-wrapper').classList.contains('ba-flipbox-frontside'))) {
            target = target.closest('.ba-item')
        }
        if (!event.target.closest('.ba-edit-item') && target && app.items[target.id] && top.app.pageStructure) {
            top.app.pageStructure.inStructure(target.id, true);
        }
        if ((event.target.closest('.add-new-item') && event.target.closest('.ba-hotspot-popover')) || event.target.closest('i.delete-item')
            || event.target.closest('i.flip-flipbox-item')) {
            target = null;
        }
        if (target && top.app.cp.inPosition()) {
            app.cp.edit(target);
        }
    });
    app.checkAnimation();
    window.addEventListener('resize', app.resize);
    app.checkView();
    app.positioning.setOffsets();
    $g(window).on('scroll', function(){
        var top = window.pageYOffset,
            delta = 40 - top,
            header = app.query('header.header');
        app.positioning.setOffsets();
        if (header) {
            if (!('lastPageYOffset' in window)) {
                window.lastPageYOffset = top;
            }
            if (top > 40) {
                header.classList.add('fixed-header');
            } else {
                header.classList.remove('fixed-header');
            }
            if (getComputedStyle(header).position == 'fixed' && header.style.top != (delta)+'px' && delta > 0) {
                header.style.top = (delta)+'px';
            } else if (header.style.top != '') {
                header.style.top = '';
            }
            $g('.ba-sticky-header').each(function(){
                var section = this.querySelector('.ba-sticky-header > .ba-section'),
                    obj = app.items[section.id],
                    offset = obj.desktop.offset;
                if (app.view != 'desktop') {
                    for (var ind in breakpoints) {
                        if (!obj[ind]) {
                            obj[ind] = {};
                        }
                        offset = obj[ind].offset ? obj[ind].offset : offset;
                        if (ind == app.view) {
                            break;
                        }
                    }
                }
                if (!this.classList.contains('visible-sticky-header')) {
                    if (top - 40 >= offset * 1 && (!obj.scrollup || (obj.scrollup && top - window.lastPageYOffset < 0))) {
                        this.classList.add('visible-sticky-header');
                        document.body.classList.add('sticky-header-opened');
                        if (obj.desktop.animation.effect) {
                            section.classList.add(obj.desktop.animation.effect);
                            setTimeout(function(){
                                section.classList.remove(obj.desktop.animation.effect);
                            }, obj.desktop.animation.delay * 1 + obj.desktop.animation.duration * 1000);
                        }
                    }
                }
                if ((top - 40 < offset * 1 && !obj.scrollup) || (obj.scrollup && (top - window.lastPageYOffset > 0
                    || top - 40 <= offset * 1))) {
                    this.classList.remove('visible-sticky-header');
                    document.body.classList.remove('sticky-header-opened');
                }
            });
            window.lastPageYOffset = top;
        }
    });
    $g(document).on('mousedown.positioning', '.ba-item-in-positioning', function(event){
        if (event.button != 0 || event.target.closest('.ba-buttons-wrapper')
            || event.target.closest('.ba-hotspot-popover')) {
            return;
        }
        let $this = this,
            rect = this.getBoundingClientRect(),
            w = document.documentElement.offsetWidth,
            h = window.innerHeight,
            x = y = null,
            comp = getComputedStyle(document.body),
            bLeft = comp.borderLeftWidth.replace('px', '') * 1,
            bRight = comp.borderRightWidth.replace('px', '') * 1,
            delta = {
                y: event.clientY - rect.top,
                x: event.clientX - rect.left
            };
        this.style.position = 'fixed';
        this.style.top = event.clientY - delta.y+'px';
        this.style.left = event.clientX - delta.x+'px';
        this.style.right = 'auto';
        this.style.bottom = 'auto';
        $g(document).on('mousemove.positioning', function(event){
            if (!document.body.classList.contains('moved-positioning-item')) {
                document.body.classList.add('moved-positioning-item');
            }
            y = event.clientY - delta.y;
            x = event.clientX - delta.x;
            if (app.view == 'desktop' && x < bLeft) {
                x = bLeft;
            } else if (app.view == 'desktop' && x + rect.width > w - bRight) {
                x = w - bRight - rect.width;
            } else if (x + rect.width > w) {
                x = w - rect.width;
            } else if (x < 0) {
                x = 0;
            }
            if (window.pageYOffset < 40 && y < 40) {
                y = 40 - window.pageYOffset;
            } else if (y < 0) {
                y = 0;
            } else if (document.documentElement.offsetHeight - (window.pageYOffset + y + rect.height) < 40) {
                y = h - rect.height - 40;
            } else if (y + rect.height > h) {
                y = h - rect.height;
            }
            $this.style.top = y+'px';
            $this.style.left = x+'px';
        }).on('mouseleave.positioning', function(){
            $g(document).trigger('mouseup.positioning');
        }).on('mouseup.positioning', function(){
            document.body.classList.remove('moved-positioning-item');
            $g(document).off('mousemove.positioning mouseup.positioning mouseleave.positioning');
            app.edit = $this.id;
            top.app.positioning.item = $this;
            let item = app.items[app.edit],
                horizontal = vertical = '',
                offsets = app.positioning.getOffsets(),
                isFixed = item.positioning.position == 'fixed',
                rect = top.app.positioning.getRect(item);
            if (isFixed && x > w / 2) {
                x = Math.round(w - x - rect.width) - bRight;
                horizontal = 'right';
            } else {
                x = Math.round(rect.left) - (isFixed ? bLeft : 0);
            }
            if (isFixed && y > h / 2) {
                y = Math.round(h - y - rect.height);
                vertical = 'bottom';
            } else {
                y = Math.round(rect.top);
            }
            if (isFixed && vertical != 'bottom') {
                delta = 40 - window.pageYOffset;
                y -= (delta < 0 ? 0 : delta);
            } else if (isFixed) {
                delta = 40 - (document.documentElement.offsetHeight - window.pageYOffset - window.innerHeight);
                y -= (delta < 0 ? 0 : delta);
            }
            $this.style.position = $this.style.top = $this.style.left = $this.style.right = $this.style.bottom = '';
            if (!item[top.app.view]) {
                item[top.app.view] = {}
            }
            if (!item[top.app.view].positioning) {
                item[top.app.view].positioning = {}
            }
            item[top.app.view].positioning.x = x;
            item[top.app.view].positioning.y = y;
            item[top.app.view].positioning.horizontal = horizontal;
            item[top.app.view].positioning.vertical = vertical;
            app.sectionRules();
        });
    });
    $g('body').on('mousedown', function(event){
        top.app.closeOpenedModal(event);
        top.$g('.all-tags li').hide();
        top.$g('body').off('click.customHide');
        top.$g('.visible-select').parent().trigger('customHide');
        top.$g('.visible-select').removeClass('visible-select');
        if (top._dynarch_popupCalendar) {
            top._dynarch_popupCalendar.callCloseHandler();
        }
    });
    app.pageCss = {};
    app.style = $g('#global-css-sheets style');
    $g('#custom-css-editor').each(function(){
        if (this.dataset.enabled == 1) {
            var code = $g(this).find('.custom-css-editor-code').text();
            $g(this).find('> style').html(code);
        }
    });
    $g('body .modal').on('mousedown', function(event){
        $g(document).trigger(event);
        event.stopPropagation();
    });
    app.checkModule('checkOverlay');
    app.init();
    $g('.ba-add-section').on('mousedown', function(){
        window.parent.document.getElementById('add-section-dialog').classList.remove('add-columns');
        window.parent.app.checkModule('addSection');
    });
    fetch(JUri+'index.php?option=com_gridbox&task=editor.loadModule&module=defaultElementsStyle').then((response) => {
        return response.text();
    }).then((text) => {
        let script = document.createElement('script');
        script.innerHTML = text;
        top.document.body.append(script);
    }).then(() => {
        window.parent.app.checkModule('windowLoaded');
    });
}

app.positioning = {
    init: () => {
        $g('.ba-item-in-positioning').each(function(){
            if (this.parentNode != document.body) {
                app.items[this.id].positioning.parent = this.parentNode.id;
                document.body.append(this);
            }
            app.positioning.setTranslate(this);
        });
    },
    getOffsets: () => {
        let top = window.pageYOffset,
            delta1 = 40 - top,
            delta2 = 40 - (document.documentElement.offsetHeight - window.pageYOffset - window.innerHeight),
            offsets = {
                top: (delta1 < 0 ? 0 : delta1)+'px',
                bottom: (delta2 < 0 ? 0 : delta2)+'px'
            };

        return offsets;
    },
    setOffsets: function(){
        let offsets = this.getOffsets(),
            comp = getComputedStyle(document.body),
            bLeft = comp.borderLeftWidth.replace('px', '') * 1,
            bRight = comp.borderRightWidth.replace('px', '') * 1,
            p1, p2;
        document.querySelectorAll('.ba-item-in-positioning').forEach(function(item){
            p1 = item.style.getPropertyValue('--top-page-offset');
            p2 = item.style.getPropertyValue('--bottom-page-offset');
            if (offsets.top != p1) {
                item.style.setProperty('--top-page-offset', offsets.top);
            }
            if (offsets.bottom != p2) {
                item.style.setProperty('--bottom-page-offset', offsets.bottom);
            }
        });
        document.body.style.setProperty('--translate-border-left', bLeft+'px');
        document.body.style.setProperty('--translate-border-right', bRight+'px');
    },
    getParent: function(obj){
        let parent = document.querySelector('#'+obj.positioning.parent);
        if (!parent) {
            parent = document.querySelector('.ba-grid-column');
        }

        return parent;
    },
    updateTranslate: function($this){
        if (!app.items[$this.id] || !app.items[$this.id].positioning.position) {
            clearInterval($this.translateDelay);
            return;
        }
        let rect = this.getParent(app.items[$this.id]).getBoundingClientRect(),
            comp = getComputedStyle(document.body),
            top = Math.round(window.pageYOffset + rect.top - 40)+'px',
            bLeft = comp.borderLeftWidth.replace('px', '') * 1,
            bRight = comp.borderRightWidth.replace('px', '') * 1,
            left = Math.round(rect.left - bLeft)+'px',
            right = Math.round(document.documentElement.offsetWidth - rect.right - bRight)+'px',
            bottom = Math.round(document.documentElement.offsetHeight - (window.pageYOffset +rect.bottom) - 40)+'px',
            p1 = $this.style.getPropertyValue('--translate-top'),
            p2 = $this.style.getPropertyValue('--translate-left'),
            p3 = $this.style.getPropertyValue('--translate-right'),
            p4 = $this.style.getPropertyValue('--translate-bottom');
        if (top != p1 || left != p2 || right != p3 || bottom != p4) {
            $this.style.setProperty('--translate-top', top);
            $this.style.setProperty('--translate-left', left);
            $this.style.setProperty('--translate-right', right);
            $this.style.setProperty('--translate-bottom', bottom);
            $this.style.setProperty('--translate-border-left', bLeft+'px');
            $this.style.setProperty('--translate-border-right', bRight+'px');
        }
    },
    setTranslate: function($this){
        if (!$this.translateDelay) {
            $this.translateDelay = setInterval(function(){
                app.positioning.updateTranslate($this);
            }, 300);
        }
        this.updateTranslate($this);
    }
}

app.setMarginBox = () => {
    $g('.ba-section, .ba-grid-column, .ba-item, .ba-row').find('> .ba-box-model').each(function(){
        if (!this.querySelector('.ba-box-model-margin')) {
            $g(this).append(defaultElementsBox.margin);
        }
    })
}

app.setDefaultElementsBox = () => {
    if ('defaultElementsBox' in window) {
        $g('.ba-item').each(function(){
            var className = this.className,
                match = className.match(/[-\w]+/g)
            if (match[0] == 'ba-item-post-intro' || match[0] == 'ba-item-blog-content') {
                $g(this).append(defaultElementsBox[match[0]].edit);
                if (defaultElementsBox[match[0]].box) {
                    $g(this).append(defaultElementsBox[match[0]].box);
                }
                if (!themeData.app_type || themeData.app_type == 'blog') {
                    $g(this).find('> .ba-edit-item .delete-item').closest('.ba-edit-wrapper').remove();
                    if (match[0] == 'ba-item-blog-content') {
                        $g(this).find('> .ba-edit-item > .ba-buttons-wrapper').remove();
                    }
                }
            } else if (defaultElementsBox[match[0]] && $g(this).find('> .ba-edit-item').length == 0) {
                $g(this).append(defaultElementsBox[match[0]].edit);
                $g(this).append(defaultElementsBox[match[0]].box);
                if (this.dataset.cookie) {
                    $g(this).find('> .ba-edit-item .ba-buttons-wrapper .ba-edit-wrapper:not(:first-child)').remove();
                };
            }
        });
        $g('.ba-row-wrapper > .ba-row').each(function(){
            if (defaultElementsBox['ba-row'] && $g(this).find('> .ba-edit-item').length == 0) {
                $g(this).append(defaultElementsBox['ba-row'].edit);
                $g(this).append(defaultElementsBox['ba-row'].box);
            }
        });
        $g('.ba-grid-column').each(function(){
            if ($g(this).find('> .ba-edit-item').length == 0 &&
                ($g(this).closest('.ba-row-wrapper').parent().hasClass('ba-grid-column') ||
                    !$g(this).closest('.ba-wrapper').hasClass('tabs-content-wrapper'))) {
                $g(this).append(defaultElementsBox['ba-grid-column'].edit);
                $g(this).append(defaultElementsBox['ba-grid-column'].box);
            }
        });
        app.setMarginBox();
    }
}

app.gridboxEditorLoaded = function(){
    app.setDefaultElementsBox();
    $g('.ba-item-text .content-text a[data-link]').removeAttr('data-cke-saved-href').each(function(){
        this.href = this.dataset.link;
    });
    var POST_CONTENT = top.gridboxLanguage['POST_CONTENT'] ? top.gridboxLanguage['POST_CONTENT'] : 'Post Content';
    $g('.ba-item-blog-content .empty-list p').text(POST_CONTENT);
    $g('.open-search-results').remove();
    $g(window).on('keydown', function(event){
        window.parent.$g(window.parent).trigger(event);
    });
    $g('body').on('keydown', '.content-text[contenteditable], .headline-wrapper[contenteditable]', function(event){
        event.stopPropagation();
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            top.app.addHistory();
        }, 300);
    });
    $g('.row-with-megamenu').removeClass('row-with-megamenu');
    $g('.section-with-megamenu').removeClass('section-with-megamenu');
    for (var key in gridboxItems) {
        if (key != 'theme') {
            app.items = $g.extend(true, app.items, gridboxItems[key]);
        } else if (!gridboxItems.theme.desktop.body) {
            gridboxItems.theme.desktop.body = $g.extend(true, {}, gridboxItems.theme.desktop.p);
        }
    }
    for (var ind in app.items) {
        if (app.items[ind].type == 'footer') {
            app.footer = app.items[ind];
            break;
        }
    }
    app.theme = gridboxItems.theme;
    window.parent = window.top;
    app.preloader = window.parent.document.querySelector('.preloader');
    app.initGridboxEditor();
}

function checkMegamenuLibrary(item)
{
    var nested = window.parent.gridboxLanguage ? window.parent.gridboxLanguage['NESTED_ROW'] : 'Nested Row';
    item.find('.ba-grid-column > .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings .ba-tooltip').text(nested);
    item.find('.ba-grid-column > .ba-edit-item').each(function(){
        var $this = $g(this),
            wrapper = $this.closest('.ba-wrapper');
        $this.find('.add-library-item').parent().remove();
        if ($this.find('.add-columns-in-columns').length == 0) {
            var str = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-sort-amount-desc add-columns-in-columns"></i>',
                lib = window.parent.gridboxLanguage ? window.parent.gridboxLanguage['ADD_NESTED_ROW'] : 'Add Nested Row';
            str += '<span class="ba-tooltip tooltip-delay settings-tooltip">'+lib;
            str += '</span></span>';
            var icon = $this.find('.ba-edit-wrapper:last-child').after(str).next();
        }
        if (wrapper.attr('data-megamenu') || wrapper.hasClass('ba-overlay-section')
            || wrapper.hasClass('ba-lightbox')) {
            var str = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-collection-text add-library-item"></i>',
                lib = window.parent.gridboxLanguage ? window.parent.gridboxLanguage['LIBRARY'] : 'Library';
            str += '<span class="ba-tooltip tooltip-delay settings-tooltip">'+lib;
            str += '</span></span>';
            var icon = $this.find('.ba-edit-wrapper:last-child').after(str).next();
        }
    });
    item.find('.ba-edit-item').each(function(){
        var $this = $g(this);
        if ($this.parent().hasClass('ba-row') && $this.find('.modify-columns').length == 0) {
            var str = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-graphic-eq modify-columns"></i>',
                lib = window.parent.gridboxLanguage ? window.parent.gridboxLanguage['MODIFY_COLUMNS'] : 'Modify Columns';
            str += '<span class="ba-tooltip tooltip-delay settings-tooltip">'+lib;
            str += '</span></span>';
            var icon = $this.find('.ba-edit-wrapper').last().before(str).prev();
        }
    });
    item.find('.ba-section-items + .ba-edit-wrapper').each(function(){
        $g(this).parent().find('> .ba-edit-item .ba-buttons-wrapper').prepend(this);
    });
}

function setTextPlaceholder($this)
{
    var content = $this.querySelectorAll('.content-text > *');
    if (content.length == 0) {
        $this.innerHTML = '<p><br></p>';
    }
}

function editItem(id)
{
    var item = $g('#'+id);
    item.find('.content-text').each(function(){
        if ('createInlineCKE' in window) {
            createInlineCKE();
        }
        setTextPlaceholder(this);
    }).on('input', function(){
        setTextPlaceholder(this);
    }).on('keydown', function(){
        if (app.blogEditor) {
            this.textInterval = setInterval(function(){
                app.blogEditor.setSelection();
                app.blogEditor.checkActive();
            }, 1);
        }
    }).on('keyup', function(){
        clearInterval(this.textInterval);
        setTextPlaceholder(this);
    });
    checkMegamenuLibrary(item);
    item.off('mouseenter').on('mouseenter', function(){
        app.cp.mouseenter(this);
    }).off('mouseleave').on('mouseleave', function(){
        app.cp.mouseleave(this);
    }).find('.ba-section, .ba-row, .ba-grid-column, .ba-item').off('mouseenter').on('mouseenter', function(){
        app.cp.mouseenter(this);
    }).off('mouseleave').on('mouseleave', function(){
        app.cp.mouseleave(this);
    }).find('.main-menu .integration-wrapper').off('mouseenter').on('mouseenter', function(){
        if (!this.closest('.main-menu').classList.contains('visible-menu') || window.innerWidth > menuBreakpoint) {
            return;
        }
        app.cp.mouseenter(this);
    }).off('mouseleave').on('mouseleave', function(){
        app.cp.mouseleave(this);
    });
    if (item.hasClass('ba-item-preloader')) {
        item.off('mouseenter mouseleave').off('mouseout').on('mouseout', function(event){
            if (event.toElement && (event.toElement.closest('.ba-edit-item') || event.toElement.closest('.preloader-point-wrapper')
                    || event.toElement.classList.contains('preloader-point-wrapper') || event.toElement.localName == 'img'
                    || event.toElement.classList.contains('preloader-image-wrapper'))) {
                app.cp.mouseenter(this);
            } else {
                app.cp.mouseleave(this);
            }
        });
    }
    item.find('.ba-grid-column-wrapper').off('mouseenter').on('mouseenter', function(){
        if ($g(this).closest('.ba-grid-column').length > 0) {
            this.style.zIndex = 6;
        }
    }).off('mouseleave').on('mouseleave', function(){
        if ($g(this).closest('.ba-grid-column').length > 0) {
            this.style.zIndex = '';
        }
    });
    item.find('.ba-row-wrapper').off('mouseenter').on('mouseenter', function(){
        if ($g(this).closest('.ba-grid-column').length > 0) {
            this.style.zIndex = 20;
        }
    }).off('mouseleave').on('mouseleave', function(){
        if ($g(this).closest('.ba-grid-column').length > 0) {
            this.style.zIndex = '';
        }
    });
    item.find('.ba-column-resizer').off('mouseenter').on('mouseenter', function(){
        $g(this).find('> span').css({
            'z-index': 20
        });
    }).off('mouseleave').on('mouseleave', function(){
        $g(this).find('> span').css({
            'z-index': ''
        });
    });
    item.find('.open-overlay-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        if (top.app.cp.inPosition()) {
            top.$g('.ba-modal-cp.draggable-modal-cp.in:not(#page-structure-dialog)').modal('hide');
        }
        let parent = this.closest('.ba-edit-item').parentNode,
            overlay = document.querySelector('.ba-overlay-section-backdrop[data-id="'+parent.dataset.overlay+'"]');
        app.edit = overlay.querySelector('.ba-section').id;
        openOverlaySection(parent);
        window.parent.app.edit = app.items[app.edit];
        window.parent.app.checkModule('lightboxEditor');
    });
    item.find('.open-popover-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        app.hotspot.show(this.closest('.ba-item'), 200);
    });
    item.find('.flip-flipbox-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        if (this.fliped == 'started') {
            return false;
        }
        this.fliped = 'started';
        let $this = this,
            parent = $g(this).closest('.ba-item-flipbox'),
            id = parent.attr('id'),
            obj = app.items[id];
        parent.addClass('flipbox-animation-started');
        setTimeout(function(){
            $this.fliped = 'ended';
            parent.removeClass('flipbox-animation-started');
        }, obj.desktop.animation.duration * 1000);
        if (obj.side == 'frontside') {
            obj.side = 'backside';
            parent.addClass('backside-fliped');
        } else {
            obj.side = 'frontside';
            parent.removeClass('backside-fliped');
        }
    });
    item.find('.edit-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        $g('body').trigger('mousedown');
        app.cp.edit(this.closest('.ba-item, .ba-grid-column, .ba-row, .ba-section'));
    });
    item.find('.add-library-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = $g(this).closest('.ba-grid-column')[0].id;
        window.parent.app.checkModule('addMegamenuLibrary');
    });
    item.find('.flipbox-add-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        var flipbox = $g(this).closest('.ba-item-flipbox'),
            id = flipbox.attr('id'),
            search = ' > .ba-flipbox-wrapper > .ba-flipbox-'+app.items[id].side;
        flipbox.find(search+' > .ba-grid-column-wrapper > .ba-grid-column > .empty-item span span').trigger('mousedown');
    });
    item.find('.add-item, .empty-item span span, .empty-item span i').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        app.copyAction = null;
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = $g(this).closest('.ba-grid-column')[0].id;
        window.parent.app.checkModule('addPlugins');
    });
    item.find('.delete-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = this.closest('.ba-edit-item').parentNode.id;
        var item = $g('#'+app.edit);
        if (themeData.edit_type == 'post-layout' && themeData.app_type != 'blog') {
            window.parent.app.checkModule('deleteItem');
        } else if (item.hasClass('row-with-intro-items') || item.parent().hasClass('row-with-intro-items') ||
            item.find('.row-with-intro-items').length > 0) {
            window.parent.app.showNotice(top.app_('DEFAULT_ITEMS_NOTICE'), 'ba-alert');
        } else {
            window.parent.app.checkModule('deleteItem');
        }
    });
    item.find('.copy-item').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        app.edit = $g(this).closest('.ba-edit-item').parent()[0].id;
        var item = $g('#'+app.edit);
        if (item.hasClass('row-with-intro-items') || item.parent().hasClass('row-with-intro-items') ||
            item.find('.row-with-intro-items').length > 0) {
            window.parent.app.showNotice(window.parent.gridboxLanguage['DEFAULT_ITEMS_NOTICE'], 'ba-alert');
        } else {
            app.checkModule('copyItem');
        }
    });
    item.find('.modify-columns').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = $g(this).closest('.ba-edit-item').parent()[0].id;
        window.parent.document.getElementById('add-section-dialog').classList.add('add-columns');
        window.parent.document.getElementById('add-section-dialog').classList.remove('blog-editor');
        window.parent.app.checkModule('addSection');
    });
    item.find('.add-columns-in-columns').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = $g(this).closest('.ba-grid-column')[0].id;
        window.parent.document.getElementById('add-section-dialog').classList.add('add-columns');
        window.parent.app.checkModule('addSection');
    });
    item.find('.add-nested-row').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        var parent = $g(this).closest('.ba-edit-item').parent(),
            key = parent[0].id,
            search = '> .ba-flipbox-wrapper > .ba-flipbox-'+app.items[key].side;
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = parent.find(search+' > .ba-grid-column-wrapper > .ba-grid-column')[0].id;
        window.parent.document.getElementById('add-section-dialog').classList.add('add-columns');
        window.parent.document.getElementById('add-section-dialog').classList.remove('blog-editor');
        window.parent.app.checkModule('addSection');
    });
    item.find('.content-slider-add-nested-row').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        var parent = $g(this).closest('.ba-edit-item').parent(),
            key = parent[0].id;
        app.edit = parent.find('> .slideshow-wrapper > ul > .slideshow-content > li.active > .ba-grid-column')[0].id;
        window.parent.document.getElementById('add-section-dialog').classList.add('add-columns');
        window.parent.document.getElementById('add-section-dialog').classList.remove('blog-editor');
        window.parent.app.checkModule('addSection');
    });
    item.find('.add-columns').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = $g(this).closest('.ba-section')[0].id;
        window.parent.document.getElementById('add-section-dialog').classList.add('add-columns');
        window.parent.app.checkModule('addSection');
    });
    item.find('.add-library').off('mousedown').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        let parent = $g(this).closest('.ba-edit-item').parent();
        setTimeout(() => {
            $g(this).closest('li.megamenu-item').addClass('megamenu-editing')
                .closest('.ba-row-wrapper').addClass('row-with-megamenu')
                .closest('.ba-wrapper').addClass('section-with-megamenu')
                .closest('body').addClass('body-megamenu-editing');
        }, 50);
        app.edit = parent[0].id;
        if (parent.hasClass('row-with-intro-items') || parent.parent().hasClass('row-with-intro-items') ||
            parent.find('.row-with-intro-items').length > 0) {
            top.app.showNotice(top.app._('DEFAULT_ITEMS_NOTICE'), 'ba-alert');
        } else {
            top.app.checkModule('addLibrary');
        }
    });
}

app.cp = {
    box: {
        marginTop: 'margin-top',
        marginRight: 'margin-right',
        marginBottom: 'margin-bottom',
        marginLeft: 'margin-left',
        paddingTop: 'padding-top',
        paddingRight: 'padding-right',
        paddingBottom: 'padding-bottom',
        paddingLeft: 'padding-left',
    },
    edit: ($this) => {
        $g($this).closest('li.megamenu-item').addClass('megamenu-editing')
            .closest('.ba-row-wrapper').addClass('row-with-megamenu')
            .closest('.ba-wrapper').addClass('section-with-megamenu')
            .closest('body').addClass('body-megamenu-editing');
        app.edit = $this.id;
        app.checkModule('editItem');
        if (top.app.pageStructure && top.app.pageStructure.visible) {
            top.app.pageStructure.inStructure(app.edit);
        }
    },
    mouseenter: ($this) => {
        let style = getComputedStyle($this),
            box = $g($this).find(' > .ba-box-model')[0];
        for (let ind in app.cp.box) {
            box ? box.style.setProperty('--box-'+app.cp.box[ind], style[ind]) : null;
        }
        $g($this).find('> .ba-edit-item').css({
            animation: 'edit-item-show .15s ease-in-out both',
            display: 'inline-flex'
        });
    },
    mouseleave: ($this) => {
        $g($this).find('> .ba-edit-item').css({
            animation: 'none',
            display: 'none'
        });
    }
}

function makeColumnSortable(parent, group)
{
    var handle = '> .ba-item:not(.ba-item-scroll-to-top):not(.ba-social-sidebar)';
    handle += ':not(.side-navigation-menu) > .ba-edit-item .edit-settings';
    handle += ', > .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings';
    parent.each(function(){
        $g(this).sortable({
            handle : handle,
            selector : '> .ba-item, > .ba-row-wrapper',
            change: function(element){
                if (element.classList.contains('ba-row-wrapper')) {
                    $g(element).find('.ba-item').each(function(){
                        if (app.items[this.id]) {
                            initMapTypes(app.items[this.id].type, this.id);
                        }
                    });
                } else if (app.items[element.id]) {
                    initMapTypes(app.items[element.id].type, element.id);
                }
                if (top.app.pageStructure && top.app.pageStructure.visible) {
                    top.app.pageStructure.updateStructure(true);
                }
                window.parent.app.addHistory();
            },
            group : group
        });
        if ($g(this).find('> .ba-row-wrapper').length > 0) {
            var str = ' > .ba-row-wrapper > .ba-row > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column';
            makeColumnSortable($g(this).find(str), group);
        }
    });
    makeResponsiveMenuSortable(parent.find('> .ba-item-main-menu, .ba-item-one-page-menu'));
    makeHotspotSortable(parent);
}

function makeResponsiveMenuSortable(parent)
{
    let handle = '> .ba-item:not(.ba-item-scroll-to-top):not(.ba-social-sidebar)';
    handle += ':not(.side-navigation-menu) > .ba-edit-item .edit-settings';
    handle += ', > .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings';
    parent.each(function(){
        $g(this).find('> .ba-menu-wrapper > .main-menu').sortable({
            handle : handle,
            selector : '> .ba-item, .integration-wrapper',
            change: function(element){
                if (top.app.pageStructure && top.app.pageStructure.visible) {
                    top.app.pageStructure.updateStructure(true);
                }
                window.parent.app.addHistory();
            },
            group : 'responsive-menu'
        });
    });
}

function makeHotspotSortable(parent)
{
    let handle = '> .ba-item:not(.ba-item-scroll-to-top):not(.ba-social-sidebar)';
    handle += ':not(.side-navigation-menu) > .ba-edit-item .edit-settings';
    handle += ', > .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings';
    parent.each(function(){
        $g(this).find('> .ba-item-hotspot > .ba-hotspot-popover').sortable({
            handle : handle,
            selector : '> .ba-item',
            change: function(element){
                if (top.app.pageStructure && top.app.pageStructure.visible) {
                    top.app.pageStructure.updateStructure(true);
                }
                window.parent.app.addHistory();
            },
            group : 'hotspot'
        });
    });
}

function initMapTypes(type, id)
{
    let array = ['map', 'yandex-maps', 'openstreetmap', 'slideset', 'carousel', 'blog-posts', 'recent-posts',
        'recent-reviews', 'search-result', 'store-search-result', 'post-navigation', 'related-posts',
        'recent-posts-slider', 'related-posts-slider', 'recently-viewed-products', 'testimonials-slider',
        'field-google-map', 'before-after-slider', 'lottie-animations'];
    if (array.indexOf(type) != -1) {
        setTimeout(function(){
            let obj = {
                data : app.items[id],
                selector : id
            }
            app.checkModule('initItems', obj);
        }, 300);
    }
}

function makeRowSortable(parent, group)
{
    parent.each(function(){
        $g(this).sortable({
            handle : '> .ba-row-wrapper > .ba-row > .ba-edit-item .edit-settings',
            selector : '> .ba-row-wrapper',
            change: function(element){
                $g('.prevent-default').removeClass('prevent-default');
                $g(element).find('.ba-item').each(function(){
                    if (app.items[this.id]) {
                        initMapTypes(app.items[this.id].type, this.id);
                    }
                });
                if (top.app.pageStructure && top.app.pageStructure.visible) {
                    top.app.pageStructure.updateStructure(true);
                }
                window.parent.app.addHistory();
            },
            start : function(el){
                if ($g(el).closest('.ba-item').length > 0) {
                    $g(el).closest('.ba-row').addClass('prevent-default');
                }
            },
            group : group
        });
    });
}

function setColumnResizer(item)
{
    $g(item).columnResizer({
        change : function(right, left){
            right.find('.ba-item').each(function(){
                if (app.items[this.id]) {
                    initMapTypes(app.items[this.id].type, this.id);
                }
            });
            left.find('.ba-item').each(function(){
                if (app.items[this.id]) {
                    initMapTypes(app.items[this.id].type, this.id);
                }
            });
            if ('setPostMasonryHeight' in window) {
                $g('.ba-blog-posts-wrapper.ba-masonry-layout').each(function(){
                    var key = $g(this).closest('.ba-item').attr('id');
                    setPostMasonryHeight(key);
                });
            }
            if ('setGalleryMasonryHeight' in window) {
                $g('.instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                    setGalleryMasonryHeight(this.closest('.ba-item').id);
                });
            }
            window.parent.app.addHistory();
        }
    });
}

app.gridboxEditorLoaded();
app.modules.gridboxEditorLoaded = true;