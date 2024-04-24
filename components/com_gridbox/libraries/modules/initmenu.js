/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initmenu = function(obj, key){
    $g('#'+key).find('li[class*="item-"].current, li[class*="item-"].active').each(function(event){
        this.classList.add('active');
        $g(this).parents('li').addClass('active visible-nav-child');
    });
    $g('#'+key+' > .ba-menu-wrapper > .open-menu i').on('mousedown', function(event){
        if (event.button && event.button != 0) {
            return false;
        }
        event.stopPropagation();
        setTimeout(function(){
            var width = window.innerWidth - document.documentElement.clientWidth;
            $g('#'+key+' > .ba-menu-wrapper > .main-menu')
                .find('li.deeper.parent > ul, .ba-wrapper[data-megamenu]').css('max-height', '');
            $g('#'+key+' > .ba-menu-wrapper > .main-menu').addClass('visible-menu').removeClass('hide-menu')
                .css('right', -width+'px').closest('.column-wrapper').addClass('column-with-menu')
                .parents('.ba-row-wrapper').addClass('row-with-menu');
            if (themeData.page.view == 'gridbox') {
                var computed = getComputedStyle(document.querySelector('header.header'));
                document.querySelector('header.header').classList.add('ba-header-position-'+computed.position);
            }
            $g('#'+key+' > .ba-menu-backdrop').addClass('ba-visible-menu-backdrop');
            $g('body').addClass('ba-opened-menu');
        }, 50);
    });
    $g('#'+key+' > .ba-menu-backdrop, #'+key+' > .ba-menu-wrapper > .main-menu > .close-menu i').off('click').on('click', function(){
        if (!document.body.classList.contains('body-megamenu-editing')) {
            $g('.visible-menu').addClass('hide-menu').removeClass('visible-menu').css('right', '')
                .closest('.column-wrapper').removeClass('column-with-menu');
            $g('.ba-menu-backdrop').removeClass('ba-visible-menu-backdrop').addClass('ba-menu-backdrop-out');
            setTimeout(function(){
                $g('.ba-menu-backdrop.ba-menu-backdrop-out').removeClass('ba-menu-backdrop-out');
            }, 300);
            $g('#'+key+' > .ba-menu-wrapper > .main-menu')
                .find('li.deeper.parent > ul, .ba-wrapper[data-megamenu]').css('max-height', '');
            setTimeout(function(){
                if (themeData.page.view == 'gridbox') {
                    var computed = getComputedStyle(document.querySelector('header.header'));
                    document.querySelector('header.header').classList.remove('ba-header-position-'+computed.position);
                }
                $g('body').removeClass('ba-opened-menu');
                $g('.row-with-menu').removeClass('row-with-menu');
            }, 500);
        }
    });
    if (themeData.page.view == 'gridbox') {
        $g('#'+key+' .ba-menu-wrapper').on('click', '.ba-edit-item', function(event){
            event.preventDefault();
            event.stopPropagation();
        });
        var addNewItem = $g('#'+key+' > .ba-menu-wrapper > .main-menu > .add-new-item');
        if (addNewItem.length == 0) {
            addNewItem = '<div class="add-new-item"><span><i class="zmdi zmdi-layers"></i>'+
                '<span class="ba-tooltip ba-top">'+top.app._('ADD_NEW_ITEM')+'</span></span></div>';
            $g('#'+key+' > .ba-menu-wrapper > .main-menu').append(addNewItem);
            addNewItem = $g('#'+key+' > .ba-menu-wrapper > .main-menu > .add-new-item');
        }
        addNewItem.find('i').on('click', function(){
            app.edit = this.closest('.ba-item').id;
            window.parent.app.checkModule('addPlugins');
        });
        if ($g('#'+key+' > .ba-edit-item .open-mobile-menu').length == 0) {
            var str = '<span class="ba-edit-wrapper"><i class="zmdi zmdi-open-in-new open-mobile-menu"></i>'+
                '<span class="ba-tooltip tooltip-delay settings-tooltip">'+top.app._('OPEN')+
                '</span></span>';
            $g('#'+key+' > .ba-edit-item .ba-buttons-wrapper .ba-edit-wrapper').first().before(str);
        }
        $g('.edit-mobile-menu-item').off('mousedown').on('mousedown', function(event){
            if (event.button && event.button != 0) {
                return false;
            }
            event.stopPropagation();
            $g(this).closest('.ba-item').find('> .ba-edit-item .edit-item').trigger('mousedown');
        });
        $g('.open-mobile-menu').off('mousedown').on('mousedown', function(event){
            if (event.button && event.button != 0) {
                return false;
            }
            event.stopPropagation();
            $g(this).closest('.ba-item').find('> .ba-menu-wrapper > .open-menu i').first().trigger('mousedown');
        });
    }
    $g('#'+key+' > .ba-menu-wrapper .tabs-content-wrapper[data-megamenu]').each(function(){
        var li = $g('#'+key+' > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li.'+this.dataset.megamenu);
        if (li.length != 0 && $g(this).closest('li').length == 0) {
            li.prepend(this);
        }
    });
    $g('#'+key).on('mouseenter', 'li.megamenu-item', function(){
        let rectangle = this.getBoundingClientRect(),
            comp = getComputedStyle(document.body),
            borderLeft = comp.borderLeftWidth.replace('px', '') * 1,
            left = rectangle.left - borderLeft,
            wrapper = $g(this).find(' > div.tabs-content-wrapper'),
            width = document.body.clientWidth;
        wrapper[0].style.setProperty('--menu-item-left-offset', left+'px');
        wrapper[0].style.setProperty('--menu-item-width', this.offsetWidth+'px');
        wrapper.css({
            'margin-left': '',
            'width': width+'px',
            'max-width': ''
        });
    });
    $g('#'+key).off('click.deeper')
        .on('click.deeper', 'li.deeper.parent > a > i.ba-icons, li.deeper.parent > span > i.ba-icons', function(event){
        event.preventDefault();
        event.stopPropagation();
        let li = $g(this).closest('li.deeper.parent');
        if (li.hasClass('visible-nav-child')) {
            li.removeClass('visible-nav-child').addClass('hidden-nav-child');
        } else {
            $g('#'+key+' .visible-nav-child').find(' > ul, > .ba-wrapper').css('max-height', '');
            li.addClass('visible-nav-child').removeClass('hidden-nav-child');
            setTimeout(function(){
                $g('#'+key+' .visible-nav-child').find(' > ul, > .ba-wrapper').each(function(){
                   this.style.maxHeight = $g(this).outerHeight()+'px';
                });
            }, 500);
        }
    });
    $g('#'+key).on('mouseenter.test', '.integration-wrapper > ul > li.deeper.parent:not(.megamenu-item)', function(){
        let vertical = this.closest('.ba-menu-wrapper').classList.contains('vertical-menu'),
            ul = this.querySelector('ul'),
            rect = this.getBoundingClientRect();
        if (!vertical && document.documentElement.clientWidth < rect.left + ul.clientWidth) {
            ul.classList.add('dropdown-left-direction');
        } else if (vertical && document.documentElement.clientHeight < rect.bottom + ul.clientHeight) {
            ul.classList.add('dropdown-top-direction');
            ul.style.setProperty('--dropdown-top-diff', (rect.bottom + ul.clientHeight - document.documentElement.clientHeight)+'px');
        }
    });
    $g('#'+key).on('mouseenter.test', 'li.deeper.parent > ul li.deeper.parent', function(){
        let vertical = this.closest('.ba-menu-wrapper').classList.contains('vertical-menu'),
            ul = this.querySelector('ul'),
            rect = this.getBoundingClientRect();
        if (!vertical && document.documentElement.clientWidth < rect.right + ul.clientWidth) {
            ul.classList.add('child-dropdown-left-direction');
        } else if (vertical && document.documentElement.clientHeight < rect.bottom + ul.clientHeight) {
            ul.classList.add('dropdown-top-direction');
            ul.style.setProperty('--dropdown-top-diff', (rect.bottom + ul.clientHeight - document.documentElement.clientHeight)+'px');
        }
    });
    if (themeData.page.view != 'gridbox') {
        var endCoords = startCoords = {}
        $g('#'+key).on('touchstart', function(event){
            endCoords = event.originalEvent.targetTouches[0];
            startCoords = event.originalEvent.targetTouches[0];
        }).on('touchmove', function(event){
            endCoords = event.originalEvent.targetTouches[0];
        }).on('touchend', function(event){
            var hDistance = endCoords.pageX - startCoords.pageX,
                xabs = Math.abs(endCoords.pageX - startCoords.pageX),
                yabs = Math.abs(endCoords.pageY - startCoords.pageY);
            if(hDistance >= 100 && xabs >= yabs * 2) {
                $g('#'+key+' .ba-menu-backdrop').trigger('click');
            }
        });
    }
    initItems();
}

if (app.modules.initmenu) {
    app.initmenu(app.modules.initmenu.data, app.modules.initmenu.selector);
}