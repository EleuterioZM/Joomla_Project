/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var $g = jQuery,
    delay = '',
    itemsInit = [],
    app = {
        view : 'desktop',
        modules : {},
        loading : {},
        edit : null,
        cache: {},
        _: function(key){
            if (gridboxLanguage && gridboxLanguage[key]) {
                return gridboxLanguage[key];
            } else {
                return key;
            }
        },
        isExternal: function(link){
            return link ? (link.indexOf('https://') != -1 || link.indexOf('http://') != -1) : '';
        },
        getErrorText: function(text){
            let div = document.createElement('div');
            div.innerHTML = text;
            if (div.querySelector('title')) {
                text = div.querySelector('title').textContent;
            }

            return text;
        },
        fetch: async function(url, data){
            let request = await fetch(url, {
                    method: 'POST',
                    body: app.getFormData(data)
                }),
                response = null;
            if (request.ok) {
                response = await request.text();
            } else {
                let utf8Decoder = new TextDecoder("utf-8"),
                    reader = request.body.getReader(),
                    textData = await reader.read(),
                    text = utf8Decoder.decode(textData.value);
                console.info(app.getErrorText(text));
            }

            return response;
        },
        getFormData: function(data){
            let formData = new FormData();
            if (data) {
                for (let ind in data) {
                    formData.append(ind, data[ind]);
                }
            }

            return formData;
        },
        query: function(selector){
            if (!this.cache[selector]) { 
                this.cache[selector] = document.querySelector(selector);
            }
            
            return this.cache[selector];
        },
        getObject: function(key){
            var object = $g.extend(true, {}, app.items[key].desktop);
            if (app.view != 'desktop') {
                for (var ind in breakpoints) {
                    if (!app.items[key][ind]) {
                        app.items[key][ind] = {};
                    }
                    object = $g.extend(true, {}, object, app.items[key][ind]);
                    if (ind == app.view) {
                        break;
                    }
                }
            }

            return object;
        },
        checkModule : function(name, obj){
            if (name == 'loadVideoApi' && app.modules[name] && obj && obj.data.type != 'youtube+vimeo'
                && app.modules[name].data.type != 'youtube+vimeo' && app.modules[name].data.type != obj.data.type) {
                obj.data.type = 'youtube+vimeo';
            } else if (typeof(obj) != 'undefined') {
                app.modules[name] = obj;
            }
            if (typeof(app[name]) == 'undefined' && !app.loading[name]) {
                app.loading[name] = true;
                app.loadModule(name);
            } else if (typeof(app[name]) != 'undefined') {
                if (typeof(obj) != 'undefined') {
                    app[name](obj.data, obj.selector);
                } else {
                    app[name]();
                }
            }
        },
        loadModule : function(name){
            let script = document.createElement('script');
            if (name != 'defaultElementsStyle' && name != 'gridboxLanguage' &&
                name != 'shapeDividers' && name != 'presetsPatern') {
                script.src = JUri+'components/com_gridbox/libraries/modules/'+name+'.js?'+gridboxVersion;
            } else {
                script.src = JUri+'index.php?option=com_gridbox&task=editor.loadModule&module='+name+'&'+gridboxVersion;
            }
            document.head.append(script);
        }
    };

document.addEventListener("DOMContentLoaded", function(){
    app.checkModule('gridboxEditorLoaded');
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
});