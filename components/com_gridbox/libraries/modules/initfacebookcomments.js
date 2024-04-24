/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initfacebookcomments = function(obj){
    $g('#fb-jssdk').remove();
    delete(window.FB);
    var comments = '<div class="fb-comments" data-numposts="'+obj.options.limit,
        app_id = themeData.page.view == 'gridbox' ? top.integrations.facebook_comments.key : integrations.facebook_comments,
        url = window.location.href.replace(window.location.hash, '');
    comments += '" data-width="100%" data-href="'+url+'"></div>';
    $g('#fb-root').replaceWith('<div id="fb-root"></div>');
    $g('.fb-comments').replaceWith(comments);
    if (app_id) {
        var js = document.createElement('script');
        js.id = 'fb-jssdk';
        js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0&appId='+app_id+'&autoLogAppEvents=1';
        document.head.append(js);
    } else {
        $g('.fb-comments').addClass('empty-content');
    }
    initItems();
}

if (app.modules.initfacebookcomments) {
    app.initfacebookcomments(app.modules.initfacebookcomments.data);
}