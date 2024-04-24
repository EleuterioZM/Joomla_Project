/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.searchEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#search-settings-dialog'),
        results = modal.find('.search-select-display-results'),
        text = app.edit.type == 'store-search' ? 'STORE_SEARCH_RESULTS_PAGE' : 'SEARCH_RESULTS_PAGE';
    modal.find('.active').removeClass('active');
    modal.find('a[href="#search-general-options"]').parent().addClass('active');
    $g('#search-general-options').addClass('active');
    results.find('li[data-value=""]').text(app._(text));
    if (!('app' in app.edit)) {
        app.edit.app = '*';
    }
    if (!('results' in app.edit)) {
        app.edit.results = '';
    }
    results.each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.results;
        this.querySelector('input[readonly]').value = this.querySelector('li[data-value="'+app.edit.results+'"]').textContent.trim();
    });
    modal.find('.search-app-select').each(function(){
        let li = this.querySelector('li[data-value="'+app.edit.app+'"]');
        if (!li) {
            app.edit.app = '*';
            li = this.querySelector('li[data-value="'+app.edit.app+'"]');
        }
        this.querySelector('input[type="hidden"]').value = app.edit.app;
        this.querySelector('input[readonly]').value = li.textContent.trim();
        if (li.dataset.type != 'single' && li.dataset.value != '*' && li.dataset.value != 'multiple') {
            results.closest('.ba-settings-item').css('display', '');
        } else {
            results.closest('.ba-settings-item').css('display', 'none');
        }
        if (li.dataset.value == 'multiple') {
            modal.find('.multiple-apps-list').css('display', '');
        } else {
            modal.find('.multiple-apps-list').css('display', 'none');
        }
    });
    if (!('live' in app.edit)) {
        app.edit.live = false;
    }
    modal.find('.live-store-search').prop('checked', app.edit.live);
    if (!app.edit.apps) {
        app.edit.apps = {};
    }
    modal.find('.selected-apps li:not(.search-app)').remove();
    modal.find('.all-apps-list .selected-app').removeClass('selected-app');
    for (let key in app.edit.apps) {
        let str = getChosenAppHtml(key, app.edit.apps[key].title);
        modal.find('.selected-apps li.search-app').before(str);
        modal.find('.all-apps-list [data-id="'+key+'"]').addClass('selected-app');
    }
    if (modal.find('.selected-apps li:not(.search-app)').length > 0) {
        modal.find('.ba-settings-item.multiple-apps-list').addClass('not-empty-list');
    } else {
        modal.find('.ba-settings-item.multiple-apps-list').removeClass('not-empty-list');
    }
    modal.find('.apps-select-wrapper .all-apps-list li').hide();
    if (!app.edit.desktop.background) {
        app.edit.desktop.background = {
            "default":{
                "color":"rgba(255, 255, 255, 0)"
            },
            "state":false,
            "transition":{
                "duration":0.3,
                "x1":0.42,
                "y1":0,
                "x2":0.58,
                "y2":1
            }
        }
    }
    app.setDefaultState(modal.find('.background-settings-group'), 'default');
    app.setFeatureBackgroundValues(modal.find('.background-settings-group'));
    app.setDefaultState(modal.find('.padding-settings-group'), 'default');
    app.setPaddingValues(modal.find('.padding-settings-group'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    modal.find('input.search-placeholder').val(app.edit.placeholder);
    value = app.getValue('icons', 'size');
    app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
    value = app.edit.icon.icon.replace('zmdi zmdi-', '').replace('fa fa-', '').replace('flaticon-', '');
    modal.find('[data-option="icon"][data-group="icon"]').val(value);
    modal.find('.search-icon-position input[type="hidden"]').val(app.edit.desktop.icons.position);
    value = modal.find('.search-icon-position li[data-value="'+app.edit.desktop.icons.position+'"]').text().trim();
    modal.find('.search-icon-position input[readonly]').val(value);
    app.setTypography(modal.find('.typography-options'), 'typography');
    app.setDefaultState('#search-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#search-layout-options .margin-settings-group');
    app.setDefaultState('#search-layout-options .border-settings-group', 'default');
    app.setBorderValues('#search-layout-options .border-settings-group');
    if (!app.edit.desktop.shadow) {
        app.edit.desktop.shadow = {
            "value" : 0,
            "color" : "@shadow"
        }
    }
    app.setDefaultState('#search-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#search-layout-options .shadow-settings-group');
    setDisableState('#search-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function getChosenAppHtml(id, title)
{
    var str = '<li class="chosen-app"><span>'+title;
    str += '</span><i class="zmdi zmdi-close" data-remove="'+id+'"></i></li>';

    return str;
}

$g('.selected-apps .search-app input').on('click', function(event){
    event.stopPropagation();
    $g('.all-apps-list li:not(.selected-app)').css('display', '');
    $g('body').one('click', function(){
        $g('.all-apps-list li').hide();
    });
});

$g('.all-apps-list li').on('click', function(){
    if (!this.classList.contains('selected-app')) {
        this.classList.add('selected-app');
        let obj = {
                title : this.textContent.trim(),
                id : this.dataset.value
            },
            str = getChosenAppHtml(obj.id, obj.title);
        app.edit.apps[obj.id] = obj;
        $g(this).closest('.apps-select-wrapper').find('.selected-apps li.search-app').before(str);
        $g('.ba-settings-item.multiple-apps-list').addClass('not-empty-list');
    }
});

$g('.selected-apps').on('click', 'li.chosen-app .zmdi-close', function(){
    $g('.all-apps-list li[data-id="'+this.dataset.remove+'"]').removeClass('selected-app');
    delete(app.edit.apps[this.dataset.remove]);
    $g(this).closest('li').remove();
    if ($g('.selected-apps li:not(.search-app)').length > 0) {
        $g('.ba-settings-item.multiple-apps-list').addClass('not-empty-list');
    } else {
        $g('.ba-settings-item.multiple-apps-list').removeClass('not-empty-list');
    }
});

$g('#search-settings-dialog .live-store-search').on('change', function(){
    app.edit.live = this.checked;
    app.addHistory();
});

$g('#search-settings-dialog .search-placeholder').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        var input = app.editor.document.querySelector(app.selector+' .ba-search-wrapper input');
        app.edit.placeholder = $this.value;
        input.placeholder = $this.value;
        app.addHistory();
    });
});

$g('#search-settings-dialog input[data-option="icon"][data-group="icon"]').on('click', function(){
    uploadMode = 'addSearchIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('.search-app-select').on('customAction', function(){
    app.edit.app = this.querySelector('input[type="hidden"]').value;
    let li = this.querySelector('li[data-value="'+app.edit.app+'"]');
    if (li.dataset.type != 'single' && li.dataset.value != '*' && li.dataset.value != 'multiple') {
        $g('.search-select-display-results').closest('.ba-settings-item').css('display', '');
    } else {
        app.edit.results = '';
        $g('.search-select-display-results').closest('.ba-settings-item').css('display', 'none');
    }
    if (li.dataset.value == 'multiple') {
        $g('#search-settings-dialog .multiple-apps-list').css('display', '');
    } else {
        $g('#search-settings-dialog .multiple-apps-list').css('display', 'none');
    }
    app.addHistory();
});

$g('.search-select-display-results').on('customAction', function(){
    app.edit.results = this.querySelector('input[type="hidden"]').value;
    app.addHistory();
});

function removeSearchIcon()
{
    app.editor.$g(app.selector+' .ba-search-wrapper i').remove();
}

app.modules.searchEditor = true;
app.searchEditor();