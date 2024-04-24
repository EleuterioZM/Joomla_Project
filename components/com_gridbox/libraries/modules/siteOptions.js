/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.siteOptions = function(){
    setTimeout(function(){
        $g("#site-dialog").modal();
    }, 150);
}

$g('#site-dialog .update-sitemap').on('change', function(){
    if (!this.dataset.queue) {
        this.dataset.queue = 'queue';
        var str = '<span>'+gridboxLanguage['SITEMAP_GENERATION'];
        str += '</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
        app.notification.find('p').html(str);
        app.notification.removeClass('animation-out').addClass('notification-in');
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : JUri+"index.php?option=com_gridbox&task=editor.generateSitemap",
            data: {
                sitemap_domain: $g('.sitemap-domain').val().trim(),
                sitemap_slash: Number($g('.sitemap-trailing-slash').prop('checked'))
            },
            success : function(msg){
                setTimeout(function(){
                    $g('#site-dialog .update-sitemap').removeAttr('data-queue');
                    app.notification.addClass('animation-out').removeClass('notification-in');
                }, 3000);
            }
        });
    }
    setTimeout(function(){
        $g('#site-dialog .update-sitemap').prop('checked', false);
    }, 200);
});

if (!app.modules.loadCodemirror && !app.loading.loadCodemirror) {
    app.actionStack.codemirror = null;
    app.checkModule('loadCodemirror');
}

$g('.ba-subgroup-element').each(function(){
    let count = $g(this).find('.ba-group-element').length;
    this.style.setProperty('--subgroup-childs', count);
});

$g('.date-format-select').on('customAction', function(){
    let value = this.querySelector('input[type="hidden"]').value,
        action = !value ? 'addClass' : 'removeClass',
        $this = $g(this).closest('.ba-group-element').nextAll();
    $this[action]('visible-subgroup').removeClass('subgroup-animation-ended');
    $g('.ba-custom-date-format input[type="text"]').val(value);
    clearTimeout(this.subDelay);
    if (this.checked) {
        this.subDelay = setTimeout(function(){
            $this.addClass('subgroup-animation-ended');
        }, 750);
    }

}).each(function(){
    if (!this.querySelector('input[type="hidden"]').value) {
        $g(this).closest('.ba-group-element').nextAll().addClass('visible-subgroup subgroup-animation-ended');
    }
});
$g('.website-container').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.editor.app.checkModule('sectionRules');
        app.editor.app.checkModule('themeRules');
        app.editor.app.checkModule('siteRules');
    }, 300);
});
$g('.breakpoints-container input[data-breakpoint]').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.editor.breakpoints[$this.dataset.breakpoint] = $this.value * 1;
        document.querySelector('.responsive-context-menu [data-view="'+$this.dataset.breakpoint+'"]').dataset.width = $this.value;
        app.editor.app.checkModule('sectionRules');
        app.editor.app.checkModule('themeRules');
        app.editor.app.checkModule('siteRules');
        if (app.view == $this.dataset.breakpoint) {
            document.querySelector('.editor-iframe').style.width = $this.value+'px';
        }
    }, 300);
});
$g('.menu-breakpoint').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.editor.menuBreakpoint = $this.value * 1;
        app.editor.app.checkModule('sectionRules');
        app.editor.app.checkModule('themeRules');
        app.editor.app.checkModule('siteRules');
    }, 300);
});
$g('.disable-responsive').on('change', function(){
    var $this = $g('.responsive-context-menu span[data-view="desktop"]'),
        className = $this.find('i')[0].className,
        text = $this.find('span').text().trim(),
        button = $g('div[data-context="responsive-context-menu"]');
    button.find('i').first()[0].className = className;
    button.find('span').text(text);
    $g('body').removeClass(app.view).addClass('desktop');
    app.view = 'desktop';
    $g('.editor-iframe').css('width', '100%');
    app.editor.disableResponsive = this.checked;
    app.editor.app.checkModule('sectionRules');
    app.editor.app.checkModule('themeRules');
    app.editor.app.checkModule('siteRules');
    if (app.editor.disableResponsive) {
        $g('.ba-toolbar-element[data-context="responsive-context-menu"]').addClass('disable-button');
    } else {
        $g('.ba-toolbar-element[data-context="responsive-context-menu"]').removeClass('disable-button');
    }
});
$g('#site-dialog .google-fonts').on('change', function(){
    app.editor.google_fonts = Number(!this.checked);
    app.editor.app.checkModule('themeRules');
    app.editor.app.edit = 'body';
    app.editor.app.checkModule('sectionRules');
});

app.siteOptions();
app.modules.siteOptions = true;