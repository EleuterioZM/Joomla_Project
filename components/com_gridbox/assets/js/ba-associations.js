/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function getVisibleBranchClilds(parent)
{
    let childs = parent.find('> ul > li').length;
    parent.find('> ul > li.visible-branch').each(function(){
        childs += getVisibleBranchClilds(jQuery(this));
    });
    parent[0].style.setProperty('--category-childs', childs);

    return childs;
}

function getParentVisibleBranchClilds(el)
{
    let parents = el.parent().parents('li.visible-branch');
    if (parents.length) {
        let parent = parents[parents.length - 1];
        getVisibleBranchClilds(jQuery(parent));
    }
}

document.addEventListener("DOMContentLoaded", function(){
    let $g = jQuery,
        modal = top.document.querySelector('#association-pages-list-modal');

    function loadPage(url)
    {
        url += ' #workspace-wrapper > div';
        $g('#workspace-wrapper').load(url, paginationAction);
    }

    function callback()
    {
        loadPage(window.location.href);
    }

    function empty()
    {
        
    }

    function paginationAction()
    {
        $g('.pagination-list a').on('click', function(event){
            event.preventDefault();
            if (!$g(this).parent().hasClass('disabled') && !$g(this).parent().hasClass('active')) {
                setCookie('start', this.dataset.page, callback, true);
            }
        });
    }

    function setCookie(key, value, callback, async)
    {
        $g.ajax({
            type:"POST",
            async : async,
            dataType:'text',
            url:top.JUri+"index.php?option=com_gridbox&task=pages.setCookie",
            data : {
                key : "pages_"+key,
                value : value
            },
            complete: callback
        });
    }
    
    $g('input[data-pages]').on('customChange', function(){
        let key = this.dataset.pages;
        if (key != 'ordering' && key != 'direction') {
            setCookie('start', 0, empty, true);
        }
        setCookie(key, this.value, callback, true);
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

    $g('.ba-folder-tree a').on('click', function(event){
        event.preventDefault();
        window.history.pushState(null, null, this.href);
        $g('.ba-folder-tree li.active').removeClass('active');
        $g(this).parent().addClass('active');
        setCookie('start', 0, empty, false);
        loadPage(this.href);
    });

    $g('.ba-folder-tree i.zmdi-chevron-right').on('mousedown', function(){
        let parent = jQuery(this).parent();
        getVisibleBranchClilds(parent);
        if (parent.hasClass('visible-branch')) {
            parent.removeClass('visible-branch');
        } else {
            parent.addClass('visible-branch');
        }
        getParentVisibleBranchClilds(parent);
    });

    $g('.media-fullscrean').on('click', function(){
        this.classList.toggle('zmdi-fullscreen');
        this.classList.toggle('zmdi-fullscreen-exit');
        modal.classList.toggle('fullscrean');
    });

    $g('.close-media').on('click', function(){
        top.$g(modal).modal('hide');
    });

    $g('body').on('click', '.title-td span', function(){
        top.app.messageData = {
            id: this.dataset.id,
            title: this.textContent.trim()
        }
        top.app.checkModule('messageListener');
    });

    paginationAction();
});