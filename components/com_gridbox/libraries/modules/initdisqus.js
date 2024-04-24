/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initdisqus = function(obj){
    $g('#disqus_thread').removeClass('empty-content').empty();
    let subdomen = themeData.page.view == 'gridbox' ? top.integrations.disqus.key : integrations.disqus;
    if (subdomen) {
        var disqus = document.createElement('script');
        disqus.async = true;
        if (typeof(DISQUS) != 'undefined') {
            delete(DISQUS)
        }
        disqus.src = '//'+subdomen+'.disqus.com/embed.js';
        document.head.append(disqus);
    } else {
        $g('#disqus_thread').addClass('empty-content');
    }
    initItems();
}

if (app.modules.initdisqus) {
    app.initdisqus(app.modules.initdisqus.data);
}