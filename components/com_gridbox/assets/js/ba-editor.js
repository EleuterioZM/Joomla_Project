/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var $g = jQuery,
    uploadMode = '',
    gridboxCallback,
    delay = '',
    app = {
        view : 'desktop',
        itemDelete : null,
        messageData : '',
        modules : {},
        loading : {},
        actionStack : {},
        _: function(key){
            if (gridboxLanguage && gridboxLanguage[key]) {
                return gridboxLanguage[key];
            } else {
                return key;
            }
        },
        isExternal: function(link){
            return link.indexOf('https://') != -1 || link.indexOf('http://') != -1;
        },
        getErrorText: function(text){
            let div = document.createElement('div');
            div.innerHTML = text;
            if (div.querySelector('title')) {
                text = div.querySelector('title').textContent;
            }

            return text;
        },
        fetch: async function(url, data, isFile){
            let request = await fetch(url, {
                    method: 'POST',
                    body: app.getFormData(data, isFile)
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
        getFormData: function(data, isFile){
            let formData = new FormData();
            if (data) {
                for (let ind in data) {
                    if (Array.isArray(data[ind])) {
                        data[ind].forEach(function(v){
                            formData.append(ind+'[]', v);
                        });
                    } else if (!isFile && typeof data[ind] == 'object') {
                        for (let i in data[ind]) {
                            formData.append(ind+'['+i+']', data[ind][i]);
                        }
                    } else {
                        formData.append(ind, data[ind]);
                    }
                }
            }

            return formData;
        },
        checkModule : function(name){
            if (!app.modules[name] && !app.loading[name]) {
                app.loading[name] = true;
                app.loadModule(name);
            } else if (app.modules[name]) {
                app[name]();
            }
        },
        loadModule : function(name){
            let script = document.createElement('script');
            if (name != 'setCalendar' && name != 'defaultElementsStyle' && name != 'gridboxLanguage' &&
                name != 'shapeDividers' && name != 'presetsPatern') {
                script.src = JUri+'components/com_gridbox/libraries/modules/'+name+'.js?'+gridboxVersion;
            } else {
                script.src = JUri+'index.php?option=com_gridbox&task=editor.loadModule&module='+name+'&'+gridboxVersion;
            }
            document.head.append(script);
        }
    };

document.addEventListener("DOMContentLoaded", function(){
    app.checkModule('editorLoaded');
    let scrollDiv = document.querySelector('.gridbox-scroll-div'),
        scrollWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
    document.body.parentNode.style.setProperty('--scroll-width', scrollWidth+'px');
});