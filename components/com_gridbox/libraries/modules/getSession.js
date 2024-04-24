/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.getSession = function(){
    setInterval(function(){
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : JUri+"index.php?option=com_gridbox&task=gridbox.getSession&tmpl=component",
            success : function(msg){
            }
        });
    }, 600000);
    $g.ajax({
        type : "POST",
        dataType : 'text',
        url : JUri+"index.php?option=com_gridbox&task=editor.getUserAuthorisedLevels&tmpl=component",
        success : function(msg){
            app.userLevel = JSON.parse(msg);
            app.checkUserLevel();
        }
    });
}

app.checkUserLevel = function(){
    var access = 1,
        item = null;
    for (var key in app.editor.app.items) {
        item = app.editor.document.getElementById(key);
        access = app.editor.app.items[key].access;
        if (app.editor.app.items[key].type == 'footer') {
            if (!app.editor.app.items[key].desktop.body) {
                app.editor.app.items[key].desktop.body = $g.extend(true, {}, app.editor.app.items[key].desktop.p);
            }
        }
        if (!item || !access) {
            continue;
        }
        if ($g.inArray(access * 1, app.userLevel) == -1) {
            item.classList.add('ba-user-level-edit-denied');
            if (app.editor.app.items[key].type == 'lightbox') {
                document.querySelector('.lightbox-options-panel[data-id="'+key+'"]').classList.add('not-allowed-user-edit');
            }
        } else {
            item.classList.remove('ba-user-level-edit-denied');
        }
    }
    app.addHistory('init');
}

function prepareItem(item, obj)
{
    switch(obj.type) {
        case 'map':
        case 'yandex-maps':
        case 'openstreetmap':
        case 'field-google-maps':
        case 'google-maps-places':
            item.querySelector('.ba-map-wrapper').innerHTML = '';
            break;
        case 'forms' :
        case 'gallery' :
        case 'modules' :
            var integration = item.querySelector('.integration-wrapper');
            integration.innerHTML = '['+obj.type+' ID='+obj.integration+']';
            break;
        case 'menu' :
            var integration = $g(item).find('>.ba-menu-wrapper > .main-menu > .integration-wrapper')[0];
            integration.innerHTML = '[main_menu='+obj.integration+']';
            break;
        case 'custom-html' :
        	item.querySelector('.custom-html').innerHTML = obj.html;
            item.firstElementChild.innerHTML = obj.css;
            break;
        case 'logo' :
        case 'image' :
            item.querySelector('img').src = obj.image;
            break;
        case 'overlay-button' :
            if (obj.image) {
                $g(item).find('> .ba-image-wrapper img').attr('src', obj.image);
            }
            break;
        case 'flipbox':
            item.classList.remove('backside-fliped');
            item.classList.remove('flipbox-animation-started');
            obj.side = "frontside";
            break;
        case 'counter':
            item.querySelector('.counter-number').textContent = 0;
            break;
        case 'headline':
            var tag = item.querySelector('.headline-wrapper > *'),
                text = tag ? tag.textContent.trim() : '',
                duration = obj.desktop.animation.duration,
                data = '',
                delta = duration / text.length,
                delay = 0;
            if (obj.desktop.animation.effect) {
                data += '<span>';
                for (var i = 0; i < text.length; i++) {
                    data += '<span style="animation-delay: '+delay+'s">'+(text[i].trim() == '' ? '&nbsp;' : text[i])+'</span>';
                    if (text[i].trim() == '') {
                        data += '</span><span>';
                    }
                    delay += delta;
                }
                data += '</span>';
                if (obj.desktop.animation.effect == 'type') {
                    tag.style.animationDelay = duration+'s';
                }
                text = data;
            }
            if (tag) {
                tag.innerHTML = text;
            }
            break;
        case 'simple-gallery':
            obj.desktop.images = [];
            $g(item).find('.ba-instagram-image').each(function(){
                if (!this.classList.contains('ba-unpublished-html-item')) {
                    obj.desktop.images.push(this.querySelector('img').dataset.src);
                }
            });
            break;
    }
}

app.modules.getSession = true;
app.getSession();