/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var formsAppClk = {
    showLightbox: function(id, target){
        $f(formsApp.loaded[id]).closest('.ba-form-lightbox-layout').each(function(){
            formsApp.showLightbox(this);
        });
        target.clicked = '';
    },
    click: function(target){
        let matches = target.className.match(/ba-click-lightbox-form-\d+/) || target.href.match(/ba-click-lightbox-form-\d+/),
            match = matches[0].match(/\d+/),
            id = match[0],
            loaded = 'formsApp' in window;
        if (loaded && formsApp.loaded[id]) {
            formsAppClk.showLightbox(id, target);
        } else {
            let xhr = new XMLHttpRequest();
            xhr.onload = xhr.onerror = function(){
                let text = this.responseText,
                    link = null,
                    script = null;
                if (xhr.readyState == 4 && text != '') {
                    var div = document.createElement('div');
                    div.innerHTML = text;
                    document.body.append(div.querySelector('.ba-form-lightbox-layout.ba-form-'+id));
                    div.querySelectorAll('style, link').forEach(function(el){
                        if (loaded && el.localName == 'link' && el.href.indexOf('ba-style.css') != -1) {
                            return true;
                        } else if (el.localName == 'link' && el.href.indexOf('ba-style.css') != -1) {
                            link = el;
                            el.onload = function(){
                                this.loaded = true;
                                if (this.script && this.script.loaded) {
                                    formsAppClk.showLightbox(id, target);
                                }
                            }
                        }
                        document.head.append(el);
                    });
                    div.querySelectorAll('script').forEach(function(el){
                        if ((el.src.indexOf('jquery.min.js') != -1 && ('jQuery' in window)) ||
                            (loaded && el.src.indexOf('ba-form.js') != -1)) {
                            return true;
                        }
                        let script = document.createElement('script');
                        script.type = el.type;
                        if (el.src) {
                            script.src = el.src;
                        } else {
                            script.innerHTML = el.innerHTML;
                        }
                        if (el.src.indexOf('ba-form.js') != -1) {
                            link.script = script
                            script.link = link;
                            script.onload = function(){
                                this.loaded = true;
                                if (this.link && this.link.loaded) {
                                    formsAppClk.showLightbox(id, target);
                                }
                            }
                        }
                        document.head.append(script);
                    });
                    if (loaded && !formsApp.loaded[id]) {
                        formsApp.checkGoogleMaps();
                        formsApp.createForms();
                        formsAppClk.showLightbox(id, target);
                    }
                } else if (xhr.readyState == 4 && text == '') {
                    target.clicked = '';
                }
            }
            xhr.open("GET", JUri+'index.php?option=com_baforms&task=form.loadAjaxForm&id='+id, true);
            xhr.send();
        }
    }
}