/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function insertTextAtCursor(el, text)
{
    var val = el.value, endIndex, range;
    if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined") {
        endIndex = el.selectionEnd;
        el.value = val.slice(0, el.selectionStart) + text + val.slice(endIndex);
        el.selectionStart = el.selectionEnd = endIndex + text.length;
    } else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined") {
        el.focus();
        range = document.selection.createRange();
        range.collapse(false);
        range.text = text;
        range.select();
    }
}

app.getSmilepickerDialog = function(){
    if (!app.smilePickerDialog) {
        var div = document.createElement('div'),
            str = '<div class="ba-comment-smiles-picker-body">',
            smiles = ["1f600", "1f601", "1f602", "1f603", "1f604", "1f605", "1f606", "1f607", "1f608", "1f609",
                "1f60a", "1f60b", "1f60c", "1f60d", "1f60e", "1f60f", "1f610", "1f611", "1f612", "1f613",
                "1f614", "1f615", "1f616", "1f617", "1f618", "1f619", "1f61a", "1f61b", "1f61c", "1f61d",
                "1f61e", "1f61f", "1f620", "1f621", "1f622", "1f623", "1f624", "1f625", "1f626", "1f627",
                "1f628", "1f629", "1f62a", "1f62b", "1f62c", "1f62d", "1f62e", "1f62f", "1f630", "1f631",
                "1f632", "1f633", "1f634", "1f635", "1f636", "1f637", "1f638", "1f639", "1f63a", "1f63b",
                "1f63c", "1f63d", "1f63e", "1f63f", "1f640", "1f641", "1f642", "1f643", "1f644", "1f645",
                "1f646", "1f647", "1f648", "1f649", "1f64a", "1f64b", "1f64c", "1f64d", "1f64e", "1f64f"];
        for (let i = 0; i < smiles.length; i++) {
            str += '<span>&#x'+smiles[i]+';</span>';
        }
        str += '</div>';
        div.className = 'ba-comment-smiles-picker-dialog';
        div.innerHTML = str;
        document.body.append(div);
        app.smilePickerDialog = $g(div).on('click', 'span', function(event){
            event.stopPropagation();
            insertTextAtCursor(app.commentBtn, this.textContent);
        });
    }

    return app.smilePickerDialog;
}

app.initCommentsBox = function(obj, key){
    app.checkModule('commentsHelper');
    $g('#'+key).on('click', '.ba-comment-smiles-picker', function(event){
        event.stopPropagation();
        let dialog = app.getSmilepickerDialog(),
            rect = this.getBoundingClientRect(),
            computed = getComputedStyle(document.body),
            borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
            borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
            div = dialog.addClass('visible-smiles-picker')[0];
        app.commentBtn = $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-message')[0];
        div.style.top = (rect.bottom + window.pageYOffset + 10 - borderTopWidth)+'px';
        div.style.left = (rect.left - div.offsetWidth / 2 + rect.width / 2 - borderLeftWidth)+'px';
    });
    initItems();
}

if (app.modules.initCommentsBox) {
    app.initCommentsBox(app.modules.initCommentsBox.data, app.modules.initCommentsBox.selector);
}