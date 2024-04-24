/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.actionUndo = function(){
    if ($g('.ba-action-undo').hasClass('active')) {
        app.hIndex--;
        app.historyObj = {
            key : app.history[app.hIndex].edit,
            data : app.history[app.hIndex - 1]
        }
        app.checkModule('actionHistory');
        if (app.hIndex == 1) {
            $g('.ba-action-undo').removeClass('active');
        }
        $g('.ba-action-redo').addClass('active');
    }
};

app.modules.actionUndo = true;
app.actionUndo();