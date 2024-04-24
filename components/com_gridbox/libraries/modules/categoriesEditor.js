app.categoriesEditor = function(){
	app.selector = '#'+app.editor.app.edit;
    let modal = $g('#categories-settings-dialog');
    $g('#categories-settings-dialog .active').removeClass('active');
    $g('#categories-settings-dialog a[href="#categories-general-options"]').parent().addClass('active');
    $g('#categories-general-options').addClass('active');
    setPresetsList($g('#categories-settings-dialog'));
    if (!('collapsible' in app.edit)) {
        app.edit.collapsible = false;
    }
    $g('#categories-settings-dialog input[type="checkbox"][data-option="collapsible"]')
        .prop('checked', app.edit.collapsible);
    app.setDefaultState('#categories-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#categories-layout-options .margin-settings-group');
    app.setDefaultState('#categories-layout-options .padding-settings-group', 'default');
    app.setPaddingValues('#categories-layout-options .padding-settings-group');
    setDisableState('#categories-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    $g('#categories-settings-dialog .categories-app-custom-select input[type="hidden"]').val(app.edit.app);
    value = $g('#categories-settings-dialog .categories-app-custom-select li[data-value="'+app.edit.app+'"]').text().trim();
    $g('#categories-settings-dialog .categories-app-custom-select input[readonly]').val(value);
    $g('#categories-settings-dialog input[data-option="maximum"]').val(app.edit.maximum);
    $g('#categories-settings-dialog .blog-posts-layout-select input[type="hidden"]').val(app.edit.layout.layout);
    value = $g('#categories-settings-dialog .blog-posts-layout-select li[data-value="'+app.edit.layout.layout+'"]').text();
    $g('#categories-settings-dialog .blog-posts-layout-select input[readonly]').val(value.trim());
    $g('.blog-posts-cover-options').hide();
    $g('.blog-posts-grid-options').css('display', app.edit.layout.layout == 'ba-classic-layout' ? 'none' : '');
    if (app.edit.layout.layout == 'ba-cover-layout') {
        $g('#categories-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
        $g('.blog-posts-cover-options').css('display', '');
        $g('.blog-posts-background-options').hide();
    }
    value = app.getValue('view', 'count');
    $g('#categories-settings-dialog input[data-option="count"]').val(value);
    value = app.getValue('background', 'color');
    updateInput($g('#categories-settings-dialog .blog-posts-background-options input[data-option="color"]'), value);
    app.setDefaultState('#categories-settings-dialog .shadow-settings-group', 'default');
    app.setShadowValues('#categories-settings-dialog .shadow-settings-group');
    app.setDefaultState('#categories-layout-options .border-settings-group', 'default');
    app.setBorderValues('#categories-layout-options .border-settings-group');
    app.recentPostsCallback = 'getBlogCategories';
    $g('#categories-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
    let object = null;
        states = app.getValue('overlay-states');
    if (!states) {
        object = app.getValue('overlay');
        states = {
            type: object.type,
            color: object.color
        }
        app.edit.desktop['overlay-states'] = states
    }
    if (!('blur' in app.edit.desktop.overlay)) {
        app.edit.desktop.overlay.blur = 10;
    }
    app.setDefaultState('#categories-settings-dialog .overlay-settings-group', 'default');
    if (!('blur' in app.edit.desktop['overlay-states'].default)) {
        app.edit.desktop['overlay-states'].default.blur = app.edit.desktop.overlay.blur;
    }
    app.setOverlayValues('#categories-settings-dialog .overlay-settings-group');
    $g('#categories-settings-dialog input[data-group="view"][type="checkbox"]').each(function(){
        if (this.dataset.option in app.edit.desktop.view) {
            this.checked = app.edit.desktop.view[this.dataset.option];
        }
    });
    $g('#categories-settings-dialog .ba-style-custom-select input[type="hidden"]').val('image');
    $g('#categories-settings-dialog .ba-style-custom-select input[readonly]').val(gridboxLanguage['IMAGE']);
    $g('#categories-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
    $g('#categories-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
    showBaStyleDesign('image', document.querySelector('#categories-settings-dialog .ba-style-custom-select'));
    setTimeout(function(){
        $g('#categories-settings-dialog').modal();
    }, 150);
}

function getBlogCategories()
{
    app.fetch(JUri+"index.php?option=com_gridbox&task=editor.getBlogCategories", {
        id: app.edit.app,
        maximum: app.edit.maximum,
        counter: Number(app.edit.desktop.view.counter),
        sub: Number(app.edit.desktop.view.sub),
        title: Number(app.edit.desktop.view.title),
        image: Number(app.edit.desktop.view.image)
    }).then((text) => {
        app.editor.document.querySelector(app.selector+' .ba-categories-wrapper').innerHTML = text;
        app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
        app.editor.app.buttonsPrevent();
        app.addHistory();
    });
}

$g('.categories-app-custom-select').on('customAction', function(){
    var id = this.querySelector('input[type="hidden"]').value;
    if (id != app.edit.app) {
        app.edit.app = id;
        app.editor.$g(app.selector).attr('data-app', app.edit.app);
        getBlogCategories()
    }
});

$g('.set-categories-view').on('change', function(){
    app.edit.desktop[this.dataset.group][this.dataset.option] = this.checked;
    app.sectionRules();
    getBlogCategories();
});

$g('#categories-settings-dialog input[type="checkbox"][data-option="collapsible"]').on('change', function(){
    app.edit.collapsible = this.checked;
    let action = this.checked ? 'addClass' : 'removeClass',
        wrapper = app.editor.$g(app.selector+' .ba-categories-wrapper');
    wrapper[action]('ba-collapsible-categories');
    wrapper.find('.ba-app-sub-category-wrapper, .ba-blog-post-content').each(function(){
        this.style.setProperty('--categories-collapse-height', '0');
    })[action]('ba-categories-collapsed ba-categories-icon-rotated')
});

app.modules.categoriesEditor = true;
app.categoriesEditor();