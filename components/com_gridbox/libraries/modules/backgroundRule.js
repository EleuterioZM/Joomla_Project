/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.backgroundRule = function(obj, selector){
    let str = '',
        object = app.itemType ? app.items[app.itemType] : null,
        bg = $g.extend(true, {}, obj.background),
        states = obj['background-states'] ? $g.extend(true, {}, obj['background-states']) : null;
    if (!bg.image && bg.default) {
        bg = bg.default;
    }
    bg.image.image = obj.image ? obj.image.image : bg.image.image;
    if (!states || !states.default) {
        states = {
            default: {
                color: bg.color,
                image: bg.image.image
            }
        }
    }
    delete bg.image.image;
    delete bg.color;
    states.default.type = bg.type;
    if (states && states.hover) {
        states.hover.type = bg.type;
    }
    str += selector+" {";
    if ((bg.type == 'blur' && ('blur' in states.default)) || (bg.type != 'gradient' && bg.type != 'blur')) {
        str += app.cssRules.get('background', states, 'default');
    }
    if (bg.type == 'gradient') {
        app.cssRules.backgrounds(bg);
        str += app.cssRules.css;
    } else if (bg.type == 'image') {
        app.cssRules.backgroundImage(bg.image);
        str += app.cssRules.css;
    } else if (bg.type == 'blur' && !('blur' in states.default)) {
        app.cssRules.backgroundBlur(bg.blur);
        str += app.cssRules.css;
    }
    if (obj.shadow) {
        str += app.cssRules.get('shadow', obj.shadow, 'default');
    }
    str += "}";
    str += app.cssRules.getStateRule(selector+":hover", 'hover');
    str += app.cssRules.getTransitionRule(selector);
    if (object && object.parallax) {
        str += selector+" > .parallax-wrapper .parallax {";
        str += app.cssRules.get('background', states, 'default');
        str += "}";
        str += app.cssRules.getStateRule(selector+":hover > .parallax-wrapper .parallax", 'hover');
        str += app.cssRules.getTransitionRule(selector+" > .parallax-wrapper .parallax");
    }
    str += selector+" > .ba-overlay {";
    str += app.cssRules.getOverlayRules(obj);
    str += "}";
    str += app.cssRules.getStateRule(selector+":hover > .ba-overlay", 'hover');
    str += app.cssRules.getTransitionRule(selector+" > .ba-overlay");
    str += selector+" > .ba-video-background {display: "+(obj.background.type == 'video' ? 'block' : 'none')+";}";
    
    return str;
}

app.cssRules = {
    css: "",
    states: {},
    transitions: [],
    transition:{
        duration: 0.3,
        x1: 0.42,
        y1: 0,
        x2: 0.58,
        y2: 1
    },
    keys: {
        states: ['hover'],
        border: ['bottom', 'left', 'top', 'right']
    },
    prepareColor: function(obj){
        let object = {},
            key;
        for (let ind in obj) {
            key = ind == 'background' ? 'background-color' : ind
            object[key] = obj[ind];
        }

        return object;
    },
    prepareColors: function(obj){
        let object = null;
        if (!obj.desktop && !obj.colors) {
            obj.colors = {
                default: this.prepareColor(obj.normal),
                hover: this.prepareColor(obj.hover),
                state: true,
                transition: this.transition
            }
            object = obj;
        } else if (!obj.desktop && obj.colors) {
            object = obj;
        } else if (obj.desktop && !obj.desktop.colors) {
            obj.desktop.colors = {
                default: obj.desktop.normal,
                hover: obj.hover,
                state: true,
                transition: this.transition
            }
            object = obj.desktop;
        } else if (obj.desktop && obj.desktop.colors) {
            object = obj.desktop;
        }
        if (!object['colors-bg'] || !object['colors-bg'].gradient) {
            object['colors-bg'] = {
                type: '',
                gradient: {
                    effect: "linear",
                    angle: 45,
                    color1: "@bg-dark",
                    position1: 25,
                    color2: "@bg-dark-accent",
                    position2: 75
                }
            }
        }
    },
    get: function(key, obj, state, variable, calc, states){
        let object = obj[state] ? obj[state] : obj,
            css = '';
        this[key](object, variable, calc);
        css = this.css;
        if (!states) {
            states = this.keys.states;
        }
        states.forEach(function(ind){
            app.cssRules.checkState(key, obj, ind, variable, calc);
        });

        return css;
    },
    checkState: function(key, obj, state, variable, calc){
        if (!this.states[state]) {
            this.states[state] = [];
        }
        if (obj.state && obj[state]) {
            this[key](obj[state], variable, calc);
            this.states[state].push(this.css);
            this.updateTransitions(obj, key);
        }
    },
    getStateRule:function(selector, state){
        if (this.states[state] && this.states[state].length > 0) {
            let css = this.states[state].join(' ');
            this.getFullRule(selector, css);
        } else {
            this.css = '';
        }

        return this.css;
    },
    getFullRule: function(selector, css){
        this.css = selector+" {";
        this.css += css;
        this.css += "}";
    },
    updateTransitions: function(obj, key){
        if (!obj.transition) {
            return;
        }
        let transition = obj.transition,
            property = key == 'shadow' ? 'box-shadow' : key,
            easing = transition.x1+', '+transition.y1+', '+transition.x2+', '+transition.y2;
        if (property == 'overlay') {
            property = 'background';
        } else if (property == 'backgroundColor') {
            property = 'background-color';
        } else if (property == 'colors') {
            property = 'color';
        }
        this.transitions.push(property+' '+transition.duration+'s cubic-bezier('+easing+')');
        if (key == 'border') {
            this.updateTransitions(obj, 'border-radius');
        } else if (key == 'colors') {
            this.updateTransitions(obj, 'background-color');
        } else if (key == 'overlay' || key == 'background') {
            this.updateTransitions(obj, 'backdrop-filter');
            this.updateTransitions(obj, '-webkit-backdrop-filter');
        }
    },
    getOverlayRules: function(obj, key){
        if (!key) {
            key = 'overlay';
        }
        let object = obj['overlay-states'] || obj[key]['overlay-states'],
            css = '';
        if (!object || obj[key].type == 'gradient' || (obj[key].type == 'blur' && !('blur' in object.default))) {
            css += app.cssRules.get('overlay', obj[key], 'default');
        } else {
            css += app.cssRules.get('overlay', object, 'default');
        }

        return css;
    },
    getTransitionRule: function(selector){
        if (this.transitions.length > 0) {
            this.getFullRule(selector, 'transition: '+(this.transitions.join(', '))+';');
        } else {
            this.css = '';
        }
        this.transitions = [];
        this.states = {};

        return this.css;
    },
    background: function(obj, variable, calc){
        this.css = '';
        let image = blur = 'none',
            color = 'rgba(0, 0, 0, 0)';
        if (obj.type == 'image') {
            image = 'url('+(app.isExternal(obj.image) ? obj.image : JUri+encodeURI(obj.image))+')';
        } else if (obj.type == 'color') {
            color = getCorrectColor(obj.color);
        } else if (obj.type == 'blur' && obj.blur) {
            blur = "blur("+obj.blur+"px)";
        }
        this.css += "background-image: "+image+";";
        this.css += "background-color: "+color+";";
        this.css += 'backdrop-filter: '+blur+';';
        this.css += '-webkit-backdrop-filter: '+blur+';';
    },
    backgroundColor: function(obj, variable, calc){
        this.css = (variable ? variable : '')+"background-color: "+getCorrectColor(obj.color)+";";
    },
    backgroundImage: function(obj, variable, calc){
        this.css = '';
        for (let key in obj) {
            let value = key == 'image' ? 'url('+obj[key]+')' : obj[key];
            this.css += "background-"+key+": "+value+";";
        }
    },
    backgroundBlur: function(blur, variable, calc){
        this.css = (variable ? variable : '')+"backdrop-filter: blur("+blur+"px);";
        this.css += "-webkit-backdrop-filter: blur("+blur+"px);";
    },
    getColors: function(key, obj, state, variable, calc, states){
        if (obj['colors-bg']) {
            obj.colors.default.type = obj['colors-bg'].type;
            obj.colors.default.gradient = obj['colors-bg'].gradient;
        }
        let css = app.cssRules.get(key, obj.colors, state, variable, calc, states);

        return css;
    },
    colors: function(obj, variable, calc){
        this.css = '';
        for (let key in obj) {
            if (key == 'type') {
                continue;
            }
            if (key == 'color' || (key == 'background-color' && !obj.type)) {
                this.css += (variable ? variable : '')+key+': '+getCorrectColor(obj[key])+';';
            } else if (key == 'gradient' && obj.type == 'gradient') {
                this.gradient(obj.gradient, variable, calc);
            }
        }
    },
    backgrounds: function(obj, variable, calc){
        this.css = '';
        if (obj.type == 'image') {
            this.backgroundImage(obj.image);
            this.css += "background-color: rgba(0, 0, 0, 0);";
        } else if (obj.type == 'gradient') {
            this.gradient(obj.gradient);
            this.css += "background-color: rgba(0, 0, 0, 0);";
            this.css += 'background-attachment: scroll;';
        } else if (obj.type == 'color') {
            this.css += obj.color ? "background-color: "+getCorrectColor(obj.color)+";" : '';
            this.css += "background-image: none;";
        } else {
            this.css += "background-image: none;";
            this.css += "background-color: rgba(0, 0, 0, 0);";
        }
    },
    overlay: function(obj, variable, calc){
        this.css = '';
        if (!obj.type || obj.type == 'color') {
            this.css += "background-color: "+getCorrectColor(obj.color)+";";
            this.css += 'background-image: none;';
            this.css += 'backdrop-filter: none;';
            this.css += '-webkit-backdrop-filter: none;';
        } else if (obj.type == 'none') {
            this.css += "background-color: rgba(0, 0, 0, 0);";
            this.css += 'background-image: none;';
            this.css += 'backdrop-filter: none;';
            this.css += '-webkit-backdrop-filter: none;';
        } else if (obj.type == 'blur') {
            this.backgroundBlur(obj.blur);
            this.css += 'background-image: none;';
            this.css += "background-color: rgba(0, 0, 0, 0);";
        } else if (obj.gradient) {
            this.gradient(obj.gradient);
            this.css += "background-color: rgba(0, 0, 0, 0);";
            this.css += 'backdrop-filter: none;';
            this.css += '-webkit-backdrop-filter: none;';
        }
    },
    gradient: function(obj, variable, calc){
        this.css += 'background-image: '+obj.effect+'-gradient(';
        this.css += obj.effect == 'linear' ? obj.angle+'deg' : 'circle';
        this.css += ', '+getCorrectColor(obj.color1)+' ';
        this.css += obj.position1+'%, '+getCorrectColor(obj.color2);
        this.css += ' '+obj.position2+'%);';
        this.css += 'background-attachment: scroll;';
    },
    border: function(obj, variable, calc){
        if (!('top' in obj) || !('right' in obj) || !('bottom' in obj) || !('left' in obj)) {
            this.keys.border.forEach(function(key){
                obj[key] = 1;
            });
        }
        let flag = false;
        this.keys.border.forEach(function(key){
            flag = Number(obj[key]) == 1  || flag;
        });
        this.css = '';
        if (!variable) {
            variable = '--';
        }
        if (!flag && !obj.radius) {
            return;
        } else if (!flag) {
            this.css += variable+"border-radius: "+this.getValueUnits(obj.radius)+";";
            return;
        }
        for (let ind in obj) {
            let value = obj[ind];
            if (ind == 'color') {
                value = getCorrectColor(value);
            } else if (ind == 'width' || ind == 'radius') {
                value = this.getValueUnits(value);
            } else if (ind != 'style') {
                value = Number(value);
            }
            this.css += variable+"border-"+ind+": "+value+";";
        }
    },
    margin: function(obj, variable, calc){
        this.css = '';
        for (var ind in obj) {
            if (obj[ind] === '') {
                continue;
            }
            this.css += (variable ? variable : '')+'margin-'+ind+': ';
            this.css += (calc ? 'calc(' : '')+this.getValueUnits(obj[ind])+(calc ? calc+')' : '')+';';
        }
    },
    padding: function(obj, variable, calc){
        this.css = '';
        for (var ind in obj) {
            if (obj[ind] === '') {
                continue;
            }
            this.css += (variable ? variable : '')+'padding-'+ind+': ';
            this.css += (calc ? 'calc(' : '')+this.getValueUnits(obj[ind])+(calc ? calc+')' : '')+';';
        }
    },
    shadow: function(obj, variable, calc){
        this.css = (variable ? variable : '')+"box-shadow: ";
        if (!obj.advanced) {
            this.css += "0 "+(obj.value * 10)+"px "+(obj.value * 20)+"px 0 ";
        } else {
            this.css += obj.horizontal+"px "+obj.vertical+"px "+obj.blur+"px "+obj.spread+"px ";
        }
        this.css += getCorrectColor(obj.color)+";";
    },
    getValueUnits: (value) => {
        value = String(value).replace(/\s+/g, '');

        return value+(value.match(/[a-zA-Z%]+/) ? '' : 'px');
    }
}

function comparePresets(obj)
{
    if (obj.preset && app.theme.presets[obj.type] && app.theme.presets[obj.type][obj.preset]) {
        let object = app.theme.presets[obj.type][obj.preset];
        for (let ind in object.data) {
            if (ind == 'desktop' || ind in breakpoints) {
                for (key in object.data[ind]) {
                    obj[ind][key] = object.data[ind][key];
                }
            } else if (obj.type == 'flipbox' && ind == 'sides') {
                compareFlipboxPresets(obj.sides.backside, object.data[ind].backside);
                compareFlipboxPresets(obj.sides.frontside, object.data[ind].frontside);
            } else {
                obj[ind] = app.theme.presets[obj.type][obj.preset].data[ind];
            }
        }
    } else {
        obj.presets = '';
        for (let ind in obj) {
            if (typeof(obj[ind]) == 'object' && !Array.isArray(obj[ind])) {
                obj[ind] = $g.extend(true, {}, obj[ind]);
            }
        }
    }
}

function compareFlipboxPresets(obj, object)
{
    obj.parallax = object.parallax;
    obj.desktop.background = object.desktop.background;
    obj.desktop.overlay = object.desktop.overlay;
    for (var i in breakpoints) {
        if (object[i] && object[i].background) {
            obj[i].background = object[i].background;
        }
        if (object[i] && object[i].overlay) {
            obj[i].overlay = object[i].overlay;
        }
    }
}

function getCorrectColor(key)
{
    if (typeof key != 'string') {
        return '';
    }
    
    return key.indexOf('@') === -1 ? key : 'var('+key.replace('@', '--')+')';
}

function getFontUrl()
{
    var str = '';
    for (var key in app.fonts) {
        str += key+':';
        for (var i = 0; i < app.fonts[key].length; i++) {
            str += app.fonts[key][i];
            if (i != app.fonts[key].length - 1) {
                str += ',';
            } else {
                str += '%7C';
            }
        }
    }
    if (str) {
        app.setNewFont = false;
        str = '//fonts.googleapis.com/css?family='+str.slice(0, -3);
        str += '&subset=latin,cyrillic,greek,latin-ext,greek-ext,vietnamese,cyrillic-ext';
        var file = document.createElement('link');
        file.rel = 'stylesheet';
        file.type = 'text/css';
        file.href = str;
        document.head.append(file);
    }
    prepareCustomFonts();
}

function prepareCustomFonts()
{
    var str = '';
    for (var ind in app.customFonts) {
        var url = '',
            obj = app.customFonts[ind],
            font = top.fontsLibrary[ind];
        if (!font) {
            continue;
        }
        for (var i = 0; i < font.length; i++) {
            if (obj[font[i].styles]) {
                var family = ind.replace(/\+/g, ' ');
                str += "@font-face {font-family: '"+family+"'; ";
                str += "font-weight: "+font[i].styles+"; ";
                str += "src: url("+JUri+"templates/gridbox/library/fonts/"+font[i].custom_src+");} "; 
            }
        }
    }
    if (str) {
        let file = document.createElement('style');
        file.innerHTML = str;
        document.head.append(file);
    }
}

function getTextParentFamily(obj, key)
{
    let family = obj[key]['font-family'];
    if (family == '@default') {
        family = obj.body['font-family'];
    }

    return family;
}

function getTextParentWeight(obj, key)
{
    let weight = obj[key]['font-weight'];
    if (weight == '@default') {
        weight = obj.body['font-weight'];
    }

    return weight;
}

function getTextParentCustom(obj, key)
{
    let custom = obj[key].custom,
        family = obj[key]['font-family'];
    if (family == '@default') {
        custom = obj.body.custom;
    }

    return custom;
}

function getTypographyRule(obj, not, key, variables, variableKey)
{
    var str = "",
        object = app.theme,
        family, weight, custom,
        font = 'body';
    if (app.itemType && ($g('#'+app.itemType).closest('footer.footer').length > 0 ||
        ($g('#'+app.itemType).length == 0 && app.edit && typeof(app.edit) == 'string' &&
            app.edit != 'body' && $g('#'+app.edit).closest('footer.footer').length > 0))) {
        object = app.footer;
    }
    if (app.itemType && key) {
        font = key;
    }
    if (obj['font-family'] && obj['font-family'] == '@default') {
        custom = getTextParentCustom(object.desktop, font);
    } else if (obj.custom) {
        custom = obj.custom;
    }
    for (var ind in obj) {
        if (ind == not || ind == 'custom' || ind == 'type' || (ind == 'color' && obj.type) || (ind == 'gradient' && !obj.type))  {
            continue;
        }
        if (obj[ind] !== '@default' && ind != 'gradient') {
            str += (variables ? variableKey+'-' : '')+ind+": ";
        }
        if (ind == 'font-family') {
            family = obj[ind];
            if (family == '@default') {
                family = getTextParentFamily(object.desktop, font)
            } else if (google_fonts == 0 && !custom) {
                str += "'Helvetica', 'Arial', sans-serif";
            } else {
                str += "'"+family.replace(/\+/g, ' ')+"'";
            }
        } else if (ind == 'font-weight') {
            weight = obj[ind];
            if (weight == '@default') {
                weight = getTextParentWeight(object.desktop, font);
            } else {
                str += weight.replace('i', '');
            }
        } else if (ind == 'color' && !obj.type) {
            str += getCorrectColor(obj[ind])+';';
            str += 'background-image: none';
        } else if (ind == 'gradient' && obj.type == 'gradient') {
            app.cssRules.gradient(obj.gradient);
            str += app.cssRules.css;
            str += '-webkit-background-clip: text;'
            str += 'color: transparent;';
        } else if (ind == 'letter-spacing' || ind == 'font-size' || ind == 'line-height') {
            str += app.cssRules.getValueUnits(obj[ind]);
        } else {
            str += String(obj[ind]).replace(/\s+/g, '');
        }
        if (obj[ind] !== '@default' && ind != 'gradient') {
            str += ";";
        }
    }
    if (app.setNewFont && family && (!app.breakpoint  || app.breakpoint == 'desktop')) {
        if (custom && custom != 'web-safe-fonts') {
            if (!app.customFonts[family]) {
                app.customFonts[family] = {};
            }
            if (!app.customFonts[family][weight]) {
                app.customFonts[family][weight] = custom;
            }
        } else if (!custom) {
            if (!app.fonts[family]) {
                app.fonts[family] = [];
            }
            if ($g.inArray(weight, app.fonts[family]) == -1) {
                app.fonts[family].push(weight);
            }
        }
    }
    
    return str;
}

app.setNewFont = true;
app.fonts = {};
app.customFonts = {};
app.checkModule('themeRules');
app.checkModule('sectionRules');
app.checkModule('siteRules');