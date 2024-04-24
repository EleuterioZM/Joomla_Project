/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function createRules(obj)
{
    var str = "";
    app.itemType = null;
    for (var key in obj) {
        if (key == 'padding') {
            str += "body {";
            for (var ind in obj[key]) {
                str += key+'-'+ind+" : "+app.cssRules.getValueUnits(obj[key][ind])+";";
            }
            str += "}";
            str += ".page-layout {left: calc(100% + "+app.cssRules.getValueUnits(obj[key][ind])+" + 1px);}";
        } else if (key == 'links') {
            str += "body a {";
            str += "color : "+getCorrectColor(obj[key].color)+";";
            str += "}";
            str += "body a:hover {";
            str += "color : "+getCorrectColor(obj[key]['hover-color'])+";";
            str += "}";
        } else if (key != 'background' && key != 'overlay' && key != 'shadow' && key != 'video' && key != 'image') {
            str += key;
            if (key == 'body') {
                str += ', ul, ol, table, blockquote, html';
            } else if (key == 'p') {
                str += ', .content-text pre';
            }
            str += " {";
            str += getTypographyRule(obj[key]);
            str += "}";
            if (key == 'body') {
                str += key+' {'
                str += '--icon-list-line-height: '+app.cssRules.getValueUnits(obj.body['line-height'])+';';
                str += "}";
            }
        }
    }
    str += app.backgroundRule(obj, 'body');
    return str;
};

app.setColorVariables = function(){
    for (let ind in app.theme.colorVariables) {
        document.body.parentNode.style.setProperty(ind.replace('@', '--'), app.theme.colorVariables[ind].color);
    }
}

app.themeRules = function(){
    var str = createRules(app.theme.desktop);
    str += app.setMediaRules(app.theme, '', 'createRules');
    if (app.theme.desktop.background.type != 'video') {
        $g('body > .ba-video-background').remove();
    }
    this.style.text(str);
};
app.setColorVariables();
app.themeRules();