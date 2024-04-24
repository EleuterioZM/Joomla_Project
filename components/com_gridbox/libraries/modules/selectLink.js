/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.selectLink = function(){
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=selectlink.getString",
        complete: function(msg){
            $g('.apply-link').removeClass('active-button');
            $g('#link-select-modal .availible-folders').html(msg.responseText);
            if ($g('#menu-item-add-modal').hasClass('in')) {
                $g('#link-select-modal .availible-folders > ul > li').last().hide();
            }
            $g('#link-select-modal').modal();
        }
    });
}

$g('#link-select-modal .availible-folders').on('click', 'i.zmdi-chevron-right', function(event){
    event.stopPropagation();
    if ($g(this).parent().hasClass('visible-branch')) {
        $g(this).parent().removeClass('visible-branch');
    } else {
        $g(this).parent().addClass('visible-branch');
    }
});

$g('#link-select-modal .availible-folders').on('click', 'li[data-url]', function(event){
    event.stopPropagation();
    if (this.dataset.url) {
        $g('#link-select-modal .availible-folders .active').removeClass('active');
        this.classList.add('active');
        $g('.apply-link').addClass('active-button');
    }
});

$g('.apply-link').on('click', function(){
    if (this.classList.contains('active-button')) {
        fontBtn.val($g('#link-select-modal .availible-folders li.active')[0].dataset.url).trigger('input').trigger('change');
        $g('#link-select-modal').modal('hide');
    }
});

app.modules.selectLink = true;
app.selectLink();