/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addToMenu = function(){
    var id = app.editor.$g('.ba-item-main-menu').attr('id');
    if (id) {
        id = app.editor.app.items[id].integration;
    } else {
        addToMenu({});
        return false;
    }
    $g.ajax({
        type : "POST",
        dataType : 'text',
        url : JUri+"index.php?option=com_gridbox&task=editor.getMenu",
        data : {
            id : id
        },
        complete : function(msg){
            addToMenu(msg);
        }
    });
};

function addToMenu(msg)
{
    var li = '',
        obj = {
            menutype : '',
            title : '',
            items : []
        };
    $g('.menu-items-select li').not('.item-root').remove();
    if (msg.responseText) {
        obj = JSON.parse(msg.responseText);
        obj.title = $g('.menu-type-select li[data-value="'+obj.menutype+'"]').text();
        obj.items.forEach(function(el){
            li = '<li data-value="'+el.id+'">'+el.title+'</li>';
            $g('.menu-items-select ul').append(li);
        });
    }
    $g('.menu-type-select input[type="hidden"]').val(obj.menutype);
    $g('.menu-type-select input[type="text"]').val(obj.title.trim());
    li = $g('.menu-items-select li.item-root').text();
    $g('.menu-items-select input[type="hidden"]').val(1);
    $g('.menu-items-select input[type="text"]').val(li.trim());
    $g('.menu-item-title').val('');
    $g('#add-to-menu').removeClass('active-button');
    setTimeout(function(){
        $g('#add-to-menu-modal').modal();
    }, 10);
}

$g('.menu-type-select').on('customAction', function(){
    var menutype = $g(this).find('input[type="hidden"]').val();
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.getMenuItems",
        data:{
            menutype : menutype
        },
        complete: function(msg){
            var li = '';
            msg = JSON.parse(msg.responseText);
            $g('.menu-items-select li').not('.item-root').remove();
            msg.forEach(function(el){
                li = '<li data-value="'+el.id+'">'+el.title+'</li>';
                $g('.menu-items-select ul').append(li);
            });
            li = $g('.menu-items-select li.item-root').text();
            $g('.menu-items-select input[type="hidden"]').val(1);
            $g('.menu-items-select input[type="text"]').val(li.trim());
        }
    });
});

$g('#add-to-menu').on('mousedown', function(){
    if ($g(this).hasClass('active-button')) {
        var menus = Array.apply(null, app.editor.document.querySelectorAll('.ba-item-main-menu')),
            data = {
                title: $g('.menu-item-title').val().trim(),
                id: app.editor.document.getElementById('grid_id').value,
                menutype: $g('.menu-type-select input[type="hidden"]').val(),
                parent: $g('.menu-items-select input[type="hidden"]').val()
            };
        if (app.editor.themeData.edit_type) {
            data.edit_type = app.editor.themeData.edit_type;
        }
        app.fetch(JUri+"index.php?option=com_gridbox&task=editor.setMenuItem", data).then(function(){
            menus.forEach(function(menu){
                updateMenuItems(menu);
            });
            $g('#add-to-menu-modal').modal('hide');
        });
    }
});

function updateMenuItems(menu)
{
    var id = menu.id,
        item = $g(menu).find(' > .ba-menu-wrapper > .main-menu > .integration-wrapper');
    app.fetch(JUri+'index.php?option=com_gridbox&task=editor.checkMainMenu', {
        main_menu: app.editor.app.items[id].integration,
        id: id,
        items: JSON.stringify(app.editor.app.items[id])
    }).then(function(text){
        item.each(function(){
            var div = document.createElement('div');
            div.innerHTML = text;
            $g(this).find('.tabs-content-wrapper').each(function(){
                var classList = $g(this).closest('li')[0].classList,
                    id = '';
                for (var j = 0; j < classList.length; j++) {
                    if (classList[j].indexOf('item-') != -1) {
                        id = classList[j].replace('item-', '') * 1;
                        break;
                    }
                }
                $g(div).find('li.item-'+id).prepend(this);
            });
            $g(this).empty().append($g(div).find('> ul'));
        });
        app.editor.app.buttonsPrevent();
    });
}

$g('.menu-item-title').on('input', function(){
    if (this.value.trim()) {
        $g('#add-to-menu').addClass('active-button');
    } else {
        $g('#add-to-menu').removeClass('active-button');
    }
});

app.addToMenu();
app.modules.addToMenu = true;