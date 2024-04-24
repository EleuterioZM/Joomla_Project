/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
var vkFlag = false;
app.initvkcomments = function(obj){
    $g("#ba-vk-comments").empty().attr('style', '').removeClass('empty-content');
    let app_id = themeData.page.view == 'gridbox' ? top.integrations.vk_comments.key : integrations.vk_comments;
    if (app_id) {
        if (!vkFlag) {
            var vkScript = document.createElement('script');
            vkScript.src = '//vk.com/js/api/openapi.js?125';
            $g(vkScript).on('load', function(){
                VK.init({
                    apiId: app_id,
                    onlyWidgets: true
                });
                VK.Widgets.Comments("ba-vk-comments", obj.options);
                vkFlag = true;
            });
            document.head.append(vkScript);
        } else {
            VK.Widgets.Comments("ba-vk-comments", obj.options);
        }
    } else {
        $g("#ba-vk-comments").addClass('empty-content');
    }
    initItems();
}

if (app.modules.initvkcomments) {
    app.initvkcomments(app.modules.initvkcomments.data);
}