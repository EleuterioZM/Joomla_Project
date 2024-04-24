/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.loadParallax = function(){
    $g('.ba-section, .ba-row, .ba-grid-column').each(function(){
        if (app.items[this.id]) {
            var obj = {
                enable : false,
                offset : 0.5
            }
            if (app.items[this.id].type == 'row') {
                obj.offset = 0.3;
            }
            if (app.items[this.id].type == 'column') {
                obj.offset = 0.1;
            }
            if (!app.items[this.id].parallax) {
                app.items[this.id].parallax = obj;
            }
            $g(this).parallax(app.items[this.id].parallax);
            if (app.items[this.id].parallax.enable) {
                this.classList.add('parallax-container');
            } else {
                this.classList.remove('parallax-container');
            }
        }
    });
}

var file = document.createElement('script');
file.src = JUri+'components/com_gridbox/libraries/parallax/parallax.js';
document.head.append(file);
file.onload = function(){
    app.loadParallax();
}