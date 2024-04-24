/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.setEndPoint = function(obj, key){
    $g('.notification-placeholder').hide();
    $g('.notification-backdrop').addClass('visible-notification-backdrop');
    window.parent.document.body.classList.add('ba-set-end-point');
    document.body.classList.add('ba-set-end-point-iframe');
}

function returnPointItem(event)
{
    var pageY = event.clientY,
        pageX = event.clientX,
        item = null,
        str = '.ba-wrapper:not(.ba-lightbox):not(.ba-overlay-section)',
        row = ' > .ba-section-items > .ba-row-wrapper > .ba-row',
        nested = ' > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column > .ba-row-wrapper > .ba-row';
    str += ':not(.tabs-content-wrapper) > .ba-section';
    var section = document.querySelectorAll(str+row+nested);
    for (var i = 0; i < section.length; i++) {
        var coordinates = section[i].getBoundingClientRect()
        if (coordinates.top < pageY && coordinates.bottom > pageY
            && coordinates.left < pageX && coordinates.right > pageX) {
            item = section[i];
            break;
        }
    }
    if (!item) {
        section = document.querySelectorAll(str+row);
        for (var i = 0; i < section.length; i++) {
            var coordinates = section[i].getBoundingClientRect()
            if (coordinates.top < pageY && coordinates.bottom > pageY
                && coordinates.left < pageX && coordinates.right > pageX) {
                item = section[i];
                break;
            }
        }
    }
    if (!item) {
        section = document.querySelectorAll(str);
        for (var i = 0; i < section.length; i++) {
            var coordinates = section[i].getBoundingClientRect()
            if (coordinates.top < pageY && coordinates.bottom > pageY
                && coordinates.left < pageX && coordinates.right > pageX) {
                item = section[i];
                break;
            }
        }
    }

    return item;
}

$g('.notification-backdrop').on('mousedown', function(event){
    event.stopPropagation();
}).on('click', function(event){
    var item = returnPointItem(event);
    if (item) {
        if (window.parent.app.edit.init) {
            window.parent.app.edit.init.target = item.id;
            app['init'+window.parent.app.edit.type](window.parent.app.edit, app.edit);
        }
        window.parent.fontBtn.value = item.id;
        window.parent.app.addHistory();
    }
    window.parent.document.body.classList.remove('ba-set-end-point');
    document.body.classList.remove('ba-set-end-point-iframe');
    $g(this).removeClass('visible-notification-backdrop');
}).on('mousemove', function(event){
    var item = returnPointItem(event);
    if (item) {
        var rect = item.getBoundingClientRect(),
            css = $g.extend(true, {}, rect);
        css.width = css.width - 10;
        delete(css.height);
        delete(css.toJSON);
        $g('.notification-placeholder').show().css(css);
    } else {
        $g('.notification-placeholder').hide();
    }
});

app.setEndPoint();