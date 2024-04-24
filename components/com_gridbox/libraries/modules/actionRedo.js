/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.actionRedo = function(){
    if ($g('.ba-action-redo').hasClass('active')) {
        app.historyObj = {
            key : app.history[app.hIndex].edit,
            data : app.history[app.hIndex]
        }
        app.checkModule('actionHistory');
        app.hIndex++;
        if (app.hIndex == app.history.length) {
            $g('.ba-action-redo').removeClass('active');
        }
        $g('.ba-action-undo').addClass('active');
    }
};

app.modules.actionRedo = true;
app.actionRedo();