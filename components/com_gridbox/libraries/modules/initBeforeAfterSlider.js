/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initBeforeAfterSlider = function(obj, key){
    $g('#'+key).each(function(){
        let $this = this;
        this.wrapper = this.querySelector('.ba-before-after-wrapper');
        this.divider = this.querySelector('.ba-before-after-divider');
        this.before = this.querySelector('img.ba-before-img');
        this.after = this.querySelector('img.ba-after-img');
        if (this.before.src.indexOf('default-lazy-load.webp') != -1) {
            this.before.onload = function(){
                app.beforeAfterSlider.set(this.closest('.ba-item-before-after-slider'));
            }
        }
        app.beforeAfterSlider.unset(this);
        app.beforeAfterSlider.set(this);
        $g(window).on('scroll.before-after-'+key, $g.proxy(app.beforeAfterSlider.update, this, this));
        $g(window).on('resize.before-after-'+key, $g.proxy(app.beforeAfterSlider.update, this, this));
        $g(this.divider).on('mousedown touchstart', function(event){
            event.stopPropagation();
            event.preventDefault();
            if (event.type == 'touchstart') {
                document.body.classList.add('before-after-touch-divider');
                document.body.style.setProperty('--scrollbar-width', (window.innerWidth - document.documentElement.offsetWidth)+'px')
            }
            document.body.classList.add('before-after-active-slider');
            $this.classList.add('before-after-sliding');
            $g(document).on('mousemove.before-after touchmove.before-after', function(event){
                let obj = event;
                if (event.type != 'touchmove') {
                    event.preventDefault();
                } else {
                    obj = event.originalEvent.targetTouches[0];
                }
                app.beforeAfterSlider.move(obj, $this);
            }).on('mouseleave.before-after', function(event){
                $g(document).trigger('mouseup.before-after');
            }).on('mouseup.before-after touchend.before-after', function(){
                document.body.classList.remove('before-after-active-slider');
                document.body.classList.remove('before-after-touch-divider');
                $this.classList.remove('before-after-sliding');
                $g(document).off('mousemove.before-after touchmove.before-after');
                $g(document).off('mouseleave.before-after');
                $g(document).off('mouseup.before-after touchend.before-after');
            });
        });
    });
    initItems();
}


app.beforeAfterSlider = {
    isHorizontal($this){
        return $this.wrapper.dataset.direction == 'horizontal';
    },
    unset: function($this){
        $g(window).off('scroll.before-after-'+$this.id);
        $g(window).off('resize.before-after-'+$this.id);
        $g($this.divider).off('mousedown');
    },
    move: function(event, $this){
        let rect = $this.before.getBoundingClientRect(),
            isHorizontal = this.isHorizontal($this),
            d = isHorizontal ? (event.pageX - rect.left) : (event.clientY - rect.top);
        this.updateDivider($this, d);
        this.update($this);
    },
    updateDivider: function($this, d){
        let key = app.beforeAfterSlider.isHorizontal($this) ? 'left' : 'top',
            max = key == 'left' ? $this.before.offsetWidth : $this.before.offsetHeight;
            percent = 0;
        if (d < 0) {
            d = 0;
        } else if (d > max) {
            d = max;
        }
        percent = d * 100 / max;
        $this.divider.style[key] = d+'px';
    },
    update: function($this){
        let w = $this.before.offsetWidth,
            h = $this.before.offsetHeight,
            isHorizontal = app.beforeAfterSlider.isHorizontal($this),
            key = isHorizontal ? 'left' : 'top'
            d = $this.divider.style[key].replace('px', '');
        if (isHorizontal && d > w) {
            app.beforeAfterSlider.updateDivider($this, w);
        } else if (!isHorizontal && d > h) {
            app.beforeAfterSlider.updateDivider($this, h);
        }
        if (isHorizontal) {
            $this.after.style.clip = 'rect(0, '+w+'px, '+h+'px, '+d+'px)';
        } else {
            $this.after.style.clip = 'rect('+d+'px, '+w+'px, '+h+'px, 0)';
        }
    },
    set: function($this){
        let obj = app.items[$this.id],
            isHorizontal = app.beforeAfterSlider.isHorizontal($this),
            d = (isHorizontal ? $this.before.offsetWidth : $this.before.offsetHeight) * obj.start / 100;
        app.beforeAfterSlider.updateDivider($this, d);
        app.beforeAfterSlider.update($this);
    }
}

if (app.modules.initBeforeAfterSlider) {
    app.initBeforeAfterSlider(app.modules.initBeforeAfterSlider.data, app.modules.initBeforeAfterSlider.selector);
}