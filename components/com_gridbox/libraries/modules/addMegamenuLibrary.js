/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addMegamenuLibrary = function(){
    setTimeout(function(){
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.getLibraryItems",
            complete: function(msg){
                var obj = JSON.parse(msg.responseText),
                    str = getMegamenuLibraryHtml(obj.plugins, obj.delete, obj.global),
                    method = str === '' ? 'addClass' : 'removeClass';
                app.selector = app.editor.app.edit;
                app.editor.app.edit = null;
                app.editor.app.checkModule('copyItem');
                $g('#megamenu-library-dialog .ba-options-group').remove();
                $g('#megamenu-library-dialog .ba-group-title').after(str);
                $g('#megamenu-library-dialog').modal().find('.ba-group-wrapper')[method]('empty-library-list');
            }
        });
    }, 50);
}

function getMegamenuLibraryHtml(array, delete_item, global_item)
{
    var str = '';
    for (var i = 0; i < array.length; i++) {
        str += '<div class="ba-options-group"><div class="ba-group-element">';
        str += '<label class="element-title"><span data-id="'+array[i].id;
        str += '" data-global="'+array[i].global_item+'">'+array[i].title+'</span></label>';
        str += '<label class="element-id">'+array[i].id+'</label></div></div>';
    }

    return str;
}

$g('#megamenu-library-dialog .ba-group-wrapper').on('click', '.element-title span', function(){
    var item = app.editor.document.getElementById(app.selector),
        obj =  {
        "data" : item,
        "selector" : {
            id : this.dataset.id,
            type : 'plugin',
            next : false,
            globalItem : this.dataset.global
        }
    };
    if (this.dataset.global && app.editor.document.getElementById(this.dataset.global)) {
        app.showNotice(gridboxLanguage['GLOBAL_ITEM_NOTICE']);
        return false;
    }
    app.editor.app.checkModule('setLibraryItem', obj);
});

$g('.megamenu-library-search').on('input', function(){
    var search = this.value.toLowerCase();
    search = $g.trim(search);
    clearTimeout(delay);
    delay = setTimeout(function(){
        if (!search) {
            $g('#megamenu-library-dialog .ba-options-group').css('display', '');
            return false;
        }
        $g('#megamenu-library-dialog .ba-options-group').each(function(){
            var title = $g(this).find('.element-title span').text().trim().toLowerCase();
            if (title.indexOf(search) < 0) {
                this.style.display = 'none';
            } else {
                this.style.display = '';
            }
        });
    }, 300);
});

app.modules.addMegamenuLibrary = true;
app.addMegamenuLibrary();