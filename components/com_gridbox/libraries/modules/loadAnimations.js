/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.loadAnimations = function(){
    if (app.viewportItems.length > 0 && !('viewportChecker' in $g.fn)) {
        let file = document.createElement('script');
        file.src = JUri+'components/com_gridbox/libraries/animation/js/viewportchecker.js';
        document.head.append(file);
        file.onload = function(){
            app.animations.set();
        }
    } else if (app.viewportItems.length > 0) {
        app.animations.set();
    }
    if (app.motionItems.length > 0 && !('motion' in $g.fn)) {
        let file = document.createElement('script');
        file.src = JUri+'components/com_gridbox/libraries/motion/js/motion.js';
        document.head.append(file);
        file.onload = function(){
            app.animations.setMotions();
        }
    } else if (app.motionItems.length > 0) {
        app.animations.setMotions();
    }
}

app.animations = {
    set: () => {
        app.viewportItems.forEach((obj) => {
            obj.item.viewportChecker(obj.animation);
        });
    },
    setMotions: () => {
        app.motionItems.forEach((obj) => {
            obj.item.motion(obj.motions);
        });
    }
}

app.loadAnimations();