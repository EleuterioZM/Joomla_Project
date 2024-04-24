/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initReviews = function(obj, key){
    app.checkModule('commentsHelper');
    $g('#'+key).on('click', '.ba-review-stars-wrapper i[data-rating]', function(event){
        let $this = $g(this),
            closest = $this.closest('.user-comment-wrapper');
        if (!closest.length || (closest.length && closest.hasClass('user-comment-edit-enable'))) {
            $this.parent().find('i.active').removeClass('active');
            $this.addClass('active').prevAll().addClass('active');
            $this.nextAll().css('width', 0);
        }
    });
    initItems();
}

if (app.modules.initReviews) {
    app.initReviews(app.modules.initReviews.data, app.modules.initReviews.selector);
}