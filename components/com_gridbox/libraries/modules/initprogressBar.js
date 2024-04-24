/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function compareProgressBarPosition($this)
{
    let wHeight = $g(window).height(),
        itemTop = Math.round($g($this).offset().top) + 50,
        itemBottom = itemTop + ($g($this).height()),
        top = window.pageYOffset,
        bottom = (top + wHeight);
    if ((itemTop < bottom) && (itemBottom > top)){
        startProgressBar(app.items[$this.id], $this);
        $g(window).off('scroll.progress-bar-'+$this.id);
    }
}

function startProgressBar(obj, $this)
{
    let bar = $g($this.bar);
    updateProgressBarNumber($this, 0)
    bar.stop().css('width', '0%').animate({
        width: obj.target+'%'
    }, {
        duration: obj.duration * 1,
        easing: obj.easing,
        step: function(now, fx) {
            updateProgressBarNumber($this, Math.floor(now))
        },
        complete: function(){
            updateProgressBarNumber($this, obj.target)
        }
    });
}

function updateProgressBarNumber($this, percent)
{
    if ($this.numberBar) {
        $this.numberBar.textContent = percent+'%';
    }
}

app.initprogressBar = function(obj, key){
    let $this = $g('#'+key)[0];
    $this.bar = $this.querySelector('.ba-animated-bar');
    $this.numberBar = $this.bar.querySelector('.progress-bar-number');
    $g(window).off('scroll.progress-bar-'+key)
        .on('scroll.progress-bar-'+key, $g.proxy(compareProgressBarPosition, $this, $this));
    compareProgressBarPosition($this);
    initItems();
}

if (app.modules.initprogressBar) {
    app.initprogressBar(app.modules.initprogressBar.data, app.modules.initprogressBar.selector);
}