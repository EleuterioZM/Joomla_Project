/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addPlugins = function(){
    setTimeout(function(){
    	$g('#add-plugin-dialog').modal();
    }, 150);
}

$g('.plugin-search').on('input', function(){
    var search = this.value.toLowerCase();
    search = $g.trim(search);
    clearTimeout(delay);
    delay = setTimeout(function(){
        if (!search) {
            $g('#add-plugin-dialog .ba-plugin-group > *').css('display', '');
            return false;
        }
        $g('#add-plugin-dialog .ba-plugin-group').each(function(){
            var count = 0,
                elements = $g(this).find('.ba-plugin');
            elements.each(function(){
                var title = $g(this).find('span.ba-title').text().toLowerCase();
                title = $g.trim(title);
                if (title.indexOf(search) < 0) {
                    this.style.display = 'none';
                    count++;
                } else {
                    this.style.display = '';
                }
            });
            if (count == elements.length) {
                $g(this).find('p').hide();
            } else {
                $g(this).find('p').show();
            }
        });
    }, 300);
});

$g('#add-plugin-dialog .ba-plugin-group').on('click', '.ba-plugin', function(){
    var obj = {
        data : this.dataset.plugin.replace('ba-', ''),
        selector : 0,
    }
    if ((obj.data == 'blog-content' || obj.data == 'post-intro') && app.editor.$g('.ba-item-'+obj.data).length > 0) {
        app.showNotice(gridboxLanguage['ITEM_ALREADY_ADDED'], 'ba-alert');
        return false;
    }
    if (this.classList.contains('disable-plugin')) {
        if (obj.data != 'forms' && obj.data != 'gallery') {
            window.gridboxCallback = 'pluginAction';
            app.checkModule('login');
        }
    } else {
        switch (obj.data){
            case 'gallery' :
            case 'forms' :
                checkIframe($g('#'+obj.data+'-list-modal'), 'ba'+obj.data);
                break;
            case 'modules' :
                checkIframe($g('#modules-list-modal'), 'modules');
                break;
            case 'menu' :
                checkIframe($g('#menu-select-modal'), 'menu');
                break;
            case 'image' :
                uploadMode = 'itemImage';
                checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
                break;
            case 'simple-gallery' :
                uploadMode = 'itemSimpleGallery';
                checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
                break;
            case 'slideshow' :
                uploadMode = 'itemSlideshow';
                checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
                break;
            case 'content-slider' :
                uploadMode = 'itemContent-Slider';
                checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
                break;
            case 'slideset' :
                uploadMode = 'itemSlideset';
                checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
                break;
            case 'carousel' :
                uploadMode = 'itemCarousel';
                checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
                break;
            case 'logo' :
                uploadMode = 'itemLogo';
                checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
                break;
            case 'icon' :
                uploadMode = 'itemIcon';
                checkIframe($g('#icon-upload-dialog'), 'icons');
                break;
            default:
                app.editor.app.checkModule('loadPlugin' , obj);
        }
    }
});

app.modules.addPlugins = true;
app.addPlugins();