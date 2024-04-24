/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.inithypercomments = function(obj){
    jQuery("#hypercomments_widget").empty().removeClass('empty-content');
    let app_id = themeData.page.view == 'gridbox' ? top.integrations.hypercomments.key : integrations.hypercomments;
    if (app_id) {
        _hcwp = window._hcwp || [];
        _hcwp.push({widget:"Stream", widget_id: app_id});
        HC_LOAD_INIT = true;
        var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "en").substr(0, 2).toLowerCase(),
            hcc = document.createElement("script"),
            src = ("https:" == document.location.protocol ? "https" : "http");
        src += "://w.hypercomments.com/widget/hc/"+app_id+"/"+lang+"/widget.js"
        hcc.src = src;
        hcc.async = true;
        document.head.appendChild(hcc);
    } else {
        $g("#hypercomments_widget").addClass('empty-content');
    }
    initItems();
}

if (app.modules.inithypercomments) {
    app.inithypercomments(app.modules.inithypercomments.data);
}