/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (window.themeData && themeData.page.view == 'gridbox') {
    var file = document.createElement('link');
    file.rel = 'stylesheet';
    file.href = JUri+'components/com_gridbox/libraries/headline/css/animation.css';
    document.head.append(file);
}

function checkHeadline($this)
{
    var wHeight = $g(window).height(),
        itemTop = Math.round($g($this).offset().top) + 50,
        itemBottom = itemTop + ($g($this).height()),
        top = window.pageYOffset,
        bottom = (top + wHeight);
    if ((itemTop < bottom) && (itemBottom > top)) {
        $this.headlineWrapper.classList.add(app.items[$this.id].desktop.animation.effect);
        $g(window).off('scroll.headline-'+$this.id);
    } else if ($this.headlineWrapper.classList.contains(app.items[$this.id].desktop.animation.effect)) {
        $this.headlineWrapper.classList.remove(app.items[$this.id].desktop.animation.effect);
    }
}

app.initheadline = function(obj, key){
    var item = $g('#'+key)
        $this = item[0];
    if (app.items[key].desktop.animation.effect) {
        $this.headlineWrapper = $this.querySelector('.headline-wrapper');
        $g(window).off('scroll.headline-'+key).on('scroll.headline-'+key, $g.proxy(checkHeadline, $this, $this));
        checkHeadline($this);
    }
    if (themeData.page.view == 'gridbox') {
        item.find('.headline-wrapper').attr('contenteditable', true).on('keydown', function(event){
            if (event.keyCode == 13) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    }
    initItems();
}

if (app.modules.initheadline) {
    app.initheadline(app.modules.initheadline.data, app.modules.initheadline.selector);
}