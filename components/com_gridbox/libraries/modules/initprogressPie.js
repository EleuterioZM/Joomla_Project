/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function checkProgressPie($this)
{
    var wHeight = $g(window).height(),
        itemTop = Math.round($g($this).offset().top) + 50,
        itemBottom = itemTop + ($g($this).height()),
        top = window.pageYOffset,
        bottom = top + wHeight;
    if ((itemTop < bottom) && (itemBottom > top)) {
        $this.started = true;
        startProgressPie($this);
        $g(window).off('scroll.progress-pie-'+$this.id);
    }
}

function resizeProgressPie($this)
{
    if ($this.started) {
        clearTimeout($this.timeout);
        $this.timeout = setTimeout(function(){
            var obj = app.items[$this.id],
                object = getProgressPieObject($this.id),
                canvas = $g('#'+$this.id).find('canvas')[0],
                context = canvas.getContext('2d');
            canvas.width = object.width;
            canvas.height = canvas.width;
            context.lineCap = 'round';
            drawPieLine(obj.target * 3.6, canvas, context, $this);
        }, 300);
    }
}

function startProgressPie($this)
{
    var obj = app.items[$this.id],
        object = getProgressPieObject($this.id),
        canvas = $g('#'+$this.id).find('canvas')[0],
        context = canvas.getContext('2d');
    canvas.width = object.width;
    canvas.height = canvas.width;
    context.lineCap = 'round';
    animateProgressPie(obj.target, obj.easing, obj.duration, canvas, context, $this);
}

function animateProgressPie(percent, easing, duration, canvas, context, $this)
{
    clearTimeout($this.pieDelay);
    Date.now || (Date.now = function() {
        return +(new Date);
    });
    var anim,
        startTime = Date.now(),
        to = percent * 3.6;
    anim = function(){
        var deegres,
            nowDate = Date.now() - startTime;
        if (nowDate < duration) {
            $this.pieDelay = window.setTimeout(anim, 1000 / 60);
        } else {
            drawPieLine(to, canvas, context, $this);
            return false;
        }
        deegres = $g.easing[easing](0, nowDate, 0, to, duration);
        drawPieLine(deegres, canvas, context, $this);
    };
    $this.pieDelay = window.setTimeout(anim, 1000 / 60);
}

function drawPieLine(deegres, canvas, context, $this)
{
    var obj = getProgressPieObject($this.id),
        posX = canvas.width / 2,
        posY = canvas.height / 2,
        line = obj.line,
        radius = posX - line / 2,
        percent = deegres / 3.6;
    percent = percent.toFixed();
    $g('#'+$this.id+' .progress-pie-number').text(percent+'%');
    context.clearRect(0, 0, canvas.width, canvas.height);
    context.beginPath();
    context.arc(posX, posY, radius > 0 ? radius : 0, (Math.PI / 180) * 270, (Math.PI / 180) * (270 + 360));
    context.strokeStyle = app.theme.colorVariables[obj.background] ? app.theme.colorVariables[obj.background].color : obj.background;
    context.lineWidth = line;
    context.stroke();
    context.beginPath();
    context.strokeStyle = app.theme.colorVariables[obj.bar] ? app.theme.colorVariables[obj.bar].color : obj.bar;
    context.lineWidth = line;
    context.arc(posX, posY, radius > 0 ? radius : 0, (Math.PI / 180) * 270, (Math.PI / 180) * (270 + deegres));
    context.stroke();
}

function getProgressPieObject(key)
{
    var object = $g.extend(true, {}, app.items[key].desktop.view);
    if (app.view != 'desktop') {
        for (var ind in breakpoints) {
            if (!app.items[key][ind]) {
                app.items[key][ind] = {
                    view : {}
                };
            }
            object = $g.extend(true, {}, object, app.items[key][ind].view);
            if (ind == app.view) {
                break;
            }
        }
    }

    return object;
}

app.initprogressPie = function(obj, key){
    var $this = $g('#'+key)[0];
    $this.started = false;
    $g(window).off('scroll.progress-pie-'+key).on('scroll.progress-pie-'+key, $g.proxy(checkProgressPie, $this, $this));
    if (themeData.page.view == 'gridbox') {
        $g(window).off('resize.progress-pie-'+key).on('resize.progress-pie-'+key, $g.proxy(resizeProgressPie, $this, $this));
    }
    checkProgressPie($this);
    initItems();
}

if (app.modules.initprogressPie) {
    app.initprogressPie(app.modules.initprogressPie.data, app.modules.initprogressPie.selector);
}