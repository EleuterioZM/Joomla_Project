/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function siteRules(){
    var container = window.parent.$g('.website-container').val(),
        str = "body:not(.com_gridbox) .body .main-body, .ba-overlay-section-backdrop.horizontal-top";
    str += " .ba-overlay-section.ba-container .ba-row-wrapper.ba-container, ";
    str += ".ba-overlay-section-backdrop.horizontal-bottom .ba-overlay-section.ba-container ";
    str += ".ba-row-wrapper.ba-container, .ba-container:not(.ba-overlay-section), ";
    str += ".intro-post-wrapper > *:not(.intro-post-image-wrapper) {";
    str += "width: "+container+"px;";
    str += "}";
    str += "@media (min-width: "+(breakpoints.tablet + 1)+"px) {";
    str += app.siteCssPatern.desktop;
    str += "}";
    str += "@media (min-width: "+(breakpoints.laptop + 1)+"px) {";
    str += app.siteCssPatern.laptopDesktop;
    str += "}";
    if (!disableResponsive) {
        str += "@media (min-width: "+(menuBreakpoint + 1)+"px) {";
        str += app.siteCssPatern.desktopMenu;
        str += "}";
        str += "@media (max-width: "+menuBreakpoint+"px) {";
        str += app.siteCssPatern.menu;
        str += "}";        
        str += "@media (max-width: "+breakpoints.laptop+"px) {";
        str += app.siteCssPatern.laptop;
        str += "}";
        str += "@media (max-width: "+breakpoints.tablet+"px) {";
        str += app.siteCssPatern.tablet;
        str += "}";
        str += "@media (max-width: "+breakpoints['tablet-portrait']+"px) {";
        str += app.siteCssPatern.tabletPortrait;
        str += "}";
        str += "@media (min-width: "+(breakpoints.tablet + 1)+"px) and (max-width: "+breakpoints.laptop+"px){"
        str += app.siteCssPatern.tabletLaptop;
        str += "}";
        str += "@media (min-width: "+(breakpoints['tablet-portrait'] + 1)+"px) and (max-width: "+breakpoints.tablet+"px){"
        str += app.siteCssPatern.tabletPTLS;
        str += "}";
        str += "@media (min-width: "+(breakpoints.phone + 1)+"px) and (max-width: "+breakpoints['tablet-portrait']+"px){"
        str += app.siteCssPatern.phoneTabletPT;
        str += "}";
        str += "@media (min-width: "+(breakpoints['phone-portrait'] + 1)+"px) and (max-width: "+breakpoints.phone+"px){"
        str += app.siteCssPatern.phonePTLS;
        str += "}";
        str += "@media (max-width: "+breakpoints.phone+"px) {";
        str += app.siteCssPatern.phone;
        str += "}";
        str += "@media (max-width: "+breakpoints['phone-portrait']+"px) {";
        str += app.siteCssPatern.phonePortrait;
        str += "}";
    } else {
        str += 'body {min-width: '+container+'px;}';
        str += '.main-menu > .ba-item {display: none !important;}';
    }
    app.siteStyle.text(str);
};

$g.ajax({
    type: "POST",
    dataType: 'text',
    url: JUri+"index.php?option=com_gridbox&task=editor.getSiteCssObjeck",
    complete: function(msg){
        app.siteCssPatern = JSON.parse(msg.responseText);
        var style = document.createElement('style');
        style.type = 'text/css';
        document.head.appendChild(style);
        app.siteStyle = $g(style);
        $g('link[href*="/css/storage/"]').remove();
        app.siteRules = siteRules;
        app.siteRules();
    }
});