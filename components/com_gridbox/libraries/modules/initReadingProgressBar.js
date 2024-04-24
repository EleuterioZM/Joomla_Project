/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initReadingProgressBar = function(obj, key){
    $g('#'+key).each(function(){
        this.bar = this.querySelector('.ba-animated-bar');
        let column = this.closest('.ba-grid-column');
        if (column) {
            app.items[key].parent = column.id;
            document.body.append(this);
        }
        $g(window).off('scroll.reading-progress-'+key)
            .on('scroll.reading-progress-'+key, $g.proxy(app.readingScroll.set, this, this));
        $g(window).off('resize.reading-progress-'+key)
            .on('resize.reading-progress-'+key, $g.proxy(app.readingScroll.set, this, this));
        app.readingScroll.set(this);
    });
    initItems();
}

app.readingScroll = {
    setOffset: function($this){
        let delta = 40 - window.pageYOffset,
            delta2 = 40 - (document.documentElement.offsetHeight - window.pageYOffset - window.innerHeight);
        if ($this.dataset.position == 'top' && $this.style.top != (delta)+'px' && delta > 0) {
            $this.style.top = (delta)+'px';
        } else if ($this.dataset.position == 'top' && $this.style.top != '' && delta < 0) {
            $this.style.top = '';
        } else if ($this.dataset.position != 'top' && $this.style.bottom != (delta2)+'px' && delta2 > 0) {
            $this.style.bottom = (delta2)+'px';
        } else if ($this.dataset.position != 'top' && $this.style.bottom != '' && delta2 < 0) {
            $this.style.bottom = '';
        }
    },
    unset: function($this){
        $g(window).off('scroll.reading-progress-'+$this.id);
        $g(window).off('resize.reading-progress-'+$this.id);
    },
    set: function($this){
        if (!$this.parentNode) {
            app.readingScroll.unset($this);
            return;
        }
        let content = app.items[$this.id].display == 'content',
            isEditor = themeData.page.view == 'gridbox',
            header = document.querySelector('header.header'),
            style = header ? getComputedStyle(header) : {},
            hHeight = (style.position == 'relative' && content ? header.offsetHeight : 0) + (isEditor ? 40 : 0),
            dHeight = document.documentElement.offsetHeight,
            footer = document.querySelector('footer.footer'),
            fHeight = (footer && content ? footer.offsetHeight : 0) + (isEditor ? 40 : 0),
            width = ((window.pageYOffset - hHeight) / (dHeight - hHeight - fHeight - innerHeight)) * 100;
        if (isEditor) {
            app.readingScroll.setOffset($this);
        }
        $this.bar.style.width = (width < 0 ? 0 : width)+'%';
    }
}

if (app.modules.initReadingProgressBar) {
    app.initReadingProgressBar(app.modules.initReadingProgressBar.data, app.modules.initReadingProgressBar.selector);
}