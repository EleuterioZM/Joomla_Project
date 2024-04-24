/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initText = function(obj, key){
    $g('#'+key).on('click', 'a[href]', function(event){
        if (this.hash && this.href == window.location.href.replace(window.location.hash, '')+this.hash) {
            let target = $g(this.hash+', [name="'+this.hash.replace('#', '')+'"]');
            if (target.length) {
                event.preventDefault();
                window.history.replaceState(null, null, this.href);
                let position = window.compileOnePageValue ? compileOnePageValue(target) : target.offset().top;
                $g('html, body').stop().animate({
                    scrollTop: position
                }, 500);
            }
        }
    }).on('click', '.ba-copy-to-clipboard', function(event){
        var textarea = document.createElement('textarea'),
            text = this.closest('pre').querySelector('code').textContent;
        document.body.appendChild(textarea);
        textarea.value = text;
        textarea.select()
        document.execCommand('copy');
        textarea.remove();
        app.showNotice(app._('SUCCESSFULLY_COPIED_TO_CLIPBOARD'));
    }).on('paste', function(event){
        if (!this.querySelector('.content-text').classList.contains('cke_editable_inline')) {
            event.preventDefault();
            let text = event.originalEvent.clipboardData.getData('text/plain').trim();
            document.execCommand('insertText', false, text);
        }
    }).find('pre').each(function(){
        if (themeData.page.view != 'gridbox' && this.querySelector('code')) {
            let span = document.createElement('span');
            span.className = 'ba-copy-to-clipboard';
            span.innerHTML = '<i class="ba-icons ba-icon-copy"></i><span class="ba-tooltip">'+
                app._('COPY_TO_CLIPBOARD')+'</span>';
            this.append(span);
        }
    });
    initItems();
}

if (app.modules.initText) {
    app.initText(app.modules.initText.data, app.modules.initText.selector);
}