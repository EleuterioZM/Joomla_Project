/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

document.addEventListener("DOMContentLoaded", function(){
    var $g = jQuery,
        category;

    function getModal()
    {
        var query = window.parent.document.querySelectorAll('.ba-modal-lg.in'),
            wind = query[query.length - 1];

        return $g(wind);
    }
    
    $g('input[data-pages]').on('customChange', function(){
        var key = this.dataset.pages,
        	prefix = $g('#ba-media-manager').attr('data-type'),
            value = this.value;
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:top.JUri+"index.php?option=com_gridbox&task=pages.setCookie",
            data : {
                key : prefix+'_'+key,
                value : value
            },
            complete: function(){
            	var url = window.location.href;
        		url += ' .ba-group-wrapper';
    			$g('.fonts-table').load(url);
            }
        });
    });

    $g('.ba-custom-select > i, div.ba-custom-select input').on('click', function(event){
        event.stopPropagation();
        var $this = $g(this),
            parent = $this.parent();
        $g('.visible-select').removeClass('visible-select');
        parent.find('ul').addClass('visible-select');
        parent.find('li').off('click').one('click', function(){
            parent.find('input[type="text"]').val(this.textContent.trim());
            parent.find('input[type="hidden"]').val(this.dataset.value).trigger('change');
            parent.trigger('customAction');
        });
        parent.trigger('show');
        setTimeout(function(){
            $g('body').one('click', function(){
                $g('.visible-select').parent().trigger('customHide');
                $g('.visible-select').removeClass('visible-select');
            });
        }, 50);
    });

    $g('div.ba-custom-select').on('show', function(){
        var $this = $g(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    }).on('customAction', function(){
        $g(this).find('[data-pages]').trigger('customChange');
    });

    $g('input[data-pages="search"]').on('keyup', function(){
        if (event.keyCode == 13) {
            $g(this).trigger('customChange');
        }
    });

    $g('.media-fullscrean').on('click', function(){
        var modal = getModal();
        if (!modal.hasClass('fullscrean')) {
            modal.addClass('fullscrean');
            $g(this).removeClass('zmdi-fullscreen').addClass('zmdi-fullscreen-exit');
        } else {
            modal.removeClass('fullscrean');
            $g(this).addClass('zmdi-fullscreen').removeClass('zmdi-fullscreen-exit');
        }        
    });

    $g('.close-media').on('click', function(){
        var modal = getModal();
        modal.find('[data-dismiss="modal"]').trigger('click');
    });

    $g('.fonts-table').on('click', 'span[data-id]', function(){
        var obj = {
                data : $g('#ba-media-manager').attr('data-type'),
                selector : this.dataset.id,
            },
            modal = window.parent.document.getElementById('add-plugin-dialog');
        if (obj.data == 'bagallery') {
            obj.selector += $g(this).closest('.ba-group-element').find('.element-category span').attr('data-category');
        }
        if (obj.data == 'menu' && !modal.classList.contains('in')) {
            window.parent.selectMenu(obj);
        } else if (!modal.classList.contains('in')) {
            window.parent.reloadModules(obj);
        } else {
            window.parent.app.editor.app.checkModule('loadPlugin' , obj);
        }
        $g('.close-media').trigger('click');
    });
    $g('#category-dialog .gallery-categories-body').on('click', '.gallery-category-line', function(){
        var id = $g(this).attr('data-id'),
            title = $g(this).find('.gallery-category-title').text();
        category.attr('data-category', id);
        category.text(title);
        $g('#category-dialog').modal('hide')
    });
    $g('.element-category span').on('click', function(e){
        e.preventDefault();
        category = $g(this);
        var id = $g(this).closest('.ba-group-element').find('.element-title span').attr('data-id'),
            cat = this.dataset.category;
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : top.JUri+"index.php?option=com_gridbox&task=pages.getGalleryCategories&tmpl=component",
            data : {
                gallery : id,
            },
            success: function(msg){
                msg = JSON.parse(msg);
                var str = '<div class="gallery-category-line" data-id=""><div class="checkbox"';
                str += '><input type="radio"';
                if (!cat) {
                    str += ' checked';
                }
                str += '><i class="zmdi zmdi-circle-o"></i>';
                str += '<i class="zmdi zmdi-check"></i></div><div class="gallery-category-title">'+$g('.constant-all').val();
                str += '</div><div></div></div>';
                msg.forEach(function(el){
                    var settings = el.settings.split(';');
                    if (settings[3] != '*') {
                        str += '<div class="gallery-category-line" data-id=" category ID='+el.id+'"><div';
                        str += ' class="checkbox"><input type="radio"';
                        if (cat == ' category ID='+el.id) {
                            str += ' checked';
                        }
                        str += '><i class="zmdi zmdi-circle-o"></i>';
                        str += '<i class="zmdi zmdi-check"></i></div><div class="gallery-category-title">';
                        str += el.title+'</div><div>'+el.id+'</div></div>';
                    }
                });
                $g('#category-dialog .gallery-categories-body').html(str);
                $g('#category-dialog').modal();
            }
        });
    });
});