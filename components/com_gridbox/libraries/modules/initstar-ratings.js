/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app['initstar-ratings'] = function(obj, key){
    $g('#'+key).find('.stars-wrapper i').on('click', function(){
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:JUri+"index.php?option=com_gridbox&task=editor.setStarRatings",
            data:{
                rating : this.dataset.rating,
                page : JSON.stringify(themeData.page),
                id : key
            },
            complete: function(msg){
                var obj = JSON.parse(msg.responseText),
                    rating = Math.floor(obj.rating),
                    last = null,
                    width = (obj.rating - rating) * 100,
                    stars = $g('#'+key+' .stars-wrapper i').removeAttr('style');
                $g('#'+key+' .info-wrapper').html(obj.result);
                stars.removeClass('active');
                for (var i = 0; i < rating; i++) {
                    stars[i].classList.add('active');
                    stars[i].style.width = '';
                    last = stars[i];
                }
                if (rating != 5) {
                    $g(last).next().css('width', width+'%');
                }
                setTimeout(function(){
                    $g('#'+key+' .info-wrapper').replaceWith(obj.str);
                }, 2000);
            }
        });
    });
    initItems();
}

if (app.modules['initstar-ratings']) {
    app['initstar-ratings'](app.modules['initstar-ratings'].data, app.modules['initstar-ratings'].selector);
}