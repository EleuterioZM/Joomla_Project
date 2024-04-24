/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (window.themeData && themeData.page.view == 'gridbox') {
    var file = document.createElement('link');
    file.rel = 'stylesheet';
    file.href = JUri+'components/com_gridbox/libraries/flipbox/css/animation-editor.css';
    document.head.append(file);
}

app.initflipbox = function(){
    initItems();
}

app.initflipbox();