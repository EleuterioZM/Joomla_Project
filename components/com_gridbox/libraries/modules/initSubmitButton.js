/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initSubmitButton = function(obj, key){
    $g('#'+key+' a').on('click', function(event){
        event.preventDefault();
        if (themeData.page.view == 'gridbox' || this.submited) {
            return;
        }
        app.submissinForm.getData();
        let alert = document.querySelector('.ba-submission-alert')
        if (alert) {
            alert.scrollIntoView({
                behavior: 'smooth'
            });
            return;
        }
        this.submited = true;
        app.submissinForm.submit(obj, this);
    })
    initItems();
}

if (app.modules.initSubmitButton) {
    app.initSubmitButton(app.modules.initSubmitButton.data, app.modules.initSubmitButton.selector);
}