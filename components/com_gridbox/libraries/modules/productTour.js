/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.productTour = function(){
    var sidebar = [];
    sidebar.push($g('.ba-toolbar-element[data-context="responsive-context-menu"]'));
    sidebar.push($g('.product-tour-add-section'));
    sidebar.push($g('.add-page-block'));
    $g('.editor-tour').parent().addClass('active-tour');
    productTour(sidebar, '.editor-tour');
}

function productTour(sidebar, tour)
{
    $g(tour+'.step-1').addClass('visible');
    var span = sidebar.pop();
    span.addClass('active-product-tour');
    $g('body').append('<div class="saving-backdrop"></div>');
    $g('.tour-parent .next').on('click', function(event){
        event.preventDefault();
        $g(this).closest('.product-tour').removeClass('visible').next().addClass('visible');
        $g('.active-product-tour').removeClass('active-product-tour');
        var span = sidebar.pop();
        span.addClass('active-product-tour');
        if (span.hasClass('product-tour-add-section')) {
            app.editor.$g('body').addClass('product-tour-add-section-step');
        } else {
            app.editor.$g('body').removeClass('product-tour-add-section-step');
        }
    });
    $g('.tour-parent .close, .tour-parent i.zmdi.zmdi-close').on('click', function(event){
        event.preventDefault();
        $g(this).closest('.product-tour').removeClass('visible');
        $g('.active-product-tour').removeClass('active-product-tour');
        $g('.saving-backdrop').addClass('animation-out');
        $g('.editor-tour').parent().addClass('animation-out');
        setTimeout(function(){
            $g('.saving-backdrop').remove();
            $g('.active-tour').removeClass('active-tour');
        }, 400);
    });
}

app.modules.productTour = true;
app.productTour();