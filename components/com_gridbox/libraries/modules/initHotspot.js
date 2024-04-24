/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initHotspot = function(obj, key){
    $g('#'+key).on('click touchend', ' > .ba-button-wrapper a', function(event){
        event.preventDefault();
        let item = this.closest('.ba-item');
        if (item.dataset.display != 'hover' || event.type == 'touchend') {
            app.hotspot.show(item);
        }
    }).on('click', '.ba-hotspot-backdrop', function(event){
        app.hotspot.hide();
    }).on('mouseenter', function(){
        if (this.dataset.display == 'hover') {
            app.hotspot.setParents(this);
        }
    }).on('mouseleave', function(){
        if (this.dataset.display == 'hover') {
            app.hotspot.unsetParents(this);
        }
    });
    if (themeData.page.view == 'gridbox') {
        $g('#'+key+' > .ba-hotspot-popover > .add-new-item i').on('click', function(){
            app.edit = this.closest('.ba-item').id;
            window.parent.app.checkModule('addPlugins');
        });
    }
    initItems();
}

app.hotspot = {
    hide: function(){
        document.querySelectorAll('.ba-visible-hotspot-popover').forEach(function($this){
            $this.classList.remove('ba-visible-hotspot-popover');
            app.hotspot.unsetParents($this);
        });
    },
    unsetParents: function($this){
        $g($this).parents('.ba-item, .ba-row, header, footer, .body, .ba-wrapper').removeClass('ba-hotspot-popover-visible');
    },
    setParents: function($this){
        $g($this).addClass('ba-hotspot-popover-visible').parents('.ba-row, header, footer, .body, .ba-wrapper')
            .addClass('ba-hotspot-popover-visible');
    },
    show: function($this){
        this.hide();
        let rect = $this.getBoundingClientRect();
        $this.style.setProperty('--horizontal-offset', rect.left+'px');
        $this.style.setProperty('--vertical-offset', rect.top+'px');
        $this.querySelector('.ba-hotspot-popover').classList.add('ba-visible-hotspot-popover');
        this.setParents($this);
    }
}

if (app.modules.initHotspot) {
    app.initHotspot(app.modules.initHotspot.data, app.modules.initHotspot.selector);
}