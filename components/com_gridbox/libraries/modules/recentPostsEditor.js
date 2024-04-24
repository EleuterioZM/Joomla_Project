/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.recentPostsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#recent-posts-settings-dialog');
    $g('#recent-posts-settings-dialog .active').removeClass('active');
    $g('#recent-posts-settings-dialog a[href="#recent-posts-general-options"]').parent().addClass('active');
    $g('#recent-posts-general-options').addClass('active');
    $g('#recent-posts-settings-dialog').attr('data-edit', app.edit.type);
    if (app.edit.type == 'store-search-result') {
        $g('#recent-posts-settings-dialog').attr('data-edit', 'search-result');
    }
    checkAppFields($g('#recent-posts-settings-dialog'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    if (app.edit.type == 'recent-posts') {
        app.recentPostsTags.check(modal);
    }
    if (app.edit.type != 'search-result' && app.edit.type != 'store-search-result') {
        setPresetsList($g('#recent-posts-settings-dialog'));
        $g('#recent-posts-general-options .preset-options').css('display', '');
    } else {
        $g('#recent-posts-general-options .preset-options').hide();
    }
    if (app.edit.type != 'search-result' && app.edit.type != 'store-search-result') {
        $g('#recent-posts-settings-dialog li[data-value="ba-one-column-grid-layout"]').hide();
    } else {
        $g('#recent-posts-settings-dialog li[data-value="ba-one-column-grid-layout"]').css('display', '');
    }
    if (app.edit.type != 'author') {
        if (!app.edit.desktop.image.size) {
            app.edit.desktop.image.size = 'cover';
        }
        if (!app.edit.desktop.store) {
            app.edit.desktop.store = {}
        }
        $g('#recent-posts-settings-dialog .recent-posts-app-select input[type="hidden"]').val(app.edit.app);
        var value = $g('#recent-posts-settings-dialog .recent-posts-app-select li[data-value="'+app.edit.app+'"]').text();
        $g('#recent-posts-settings-dialog .recent-posts-app-select input[readonly]').val(value.trim());
        $g('#recent-posts-settings-dialog input[data-option="limit"]').val(app.edit.limit);
        $g('#recent-posts-settings-dialog input[data-option="maximum"]').val(app.edit.maximum);
        $g('#recent-posts-settings-dialog').find('.not-author-options').css('display', '');
        $g('#recent-posts-settings-dialog').find('.author-options').hide();
        if (!app.edit.info) {
            app.edit.info = ['author', 'date', 'category', 'comments'];
        }
        if (app.edit.info['hits'] == -1) {
            app.edit.info.push('hits')
        }
        if (!app.edit.desktop.postFields) {
            app.edit.desktop.postFields = {
                "margin":{
                    "bottom":"25",
                    "top":"0"
                },
                "typography":{
                    "color":"@text",
                    "font-family":"@default",
                    "font-size":"16",
                    "font-style":"normal",
                    "font-weight":"400",
                    "letter-spacing":"0",
                    "line-height":"26",
                    "text-decoration":"none",
                    "text-align":"left",
                    "text-transform":"uppercase"
                }
            }
        }
    } else {
        $g('#recent-posts-settings-dialog').find('.not-author-options').hide();
        $g('#recent-posts-settings-dialog').find('.author-options').css('display', '');
    }
    if (!app.edit.desktop.padding) {
        app.edit.desktop.padding = {
            "bottom":0,
            "left": 0,
            "right": 0,
            "top":0
        }
    }
    app.setDefaultState('#recent-posts-layout-options .padding-settings-group', 'default');
    app.setPaddingValues('#recent-posts-layout-options .padding-settings-group');
    $g('#recent-posts-settings-dialog .blog-posts-layout-select input[type="hidden"]').val(app.edit.layout.layout);
    value = $g('#recent-posts-settings-dialog .blog-posts-layout-select li[data-value="'+app.edit.layout.layout+'"]').text();
    $g('#recent-posts-settings-dialog .blog-posts-layout-select input[readonly]').val(value.trim());
    $g('.blog-posts-cover-options').hide();
    if (app.edit.layout.layout == 'ba-classic-layout' || app.edit.layout.layout == 'ba-one-column-grid-layout') {
        $g('.blog-posts-grid-options').hide();
    } else {
        $g('.blog-posts-grid-options').css('display', '');
    }
    if (app.edit.layout.layout == 'ba-cover-layout') {
        $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
        $g('.blog-posts-cover-options').css('display', '');
        $g('.blog-posts-background-options').hide();
    }
    value = app.getValue('view', 'count');
    $g('#recent-posts-settings-dialog input[data-option="count"]').val(value);
    app.setDefaultState('#recent-posts-settings-dialog .background-settings-group', 'default');
    app.setFeatureBackgroundValues('#recent-posts-settings-dialog .background-settings-group');
    app.setDefaultState('#recent-posts-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#recent-posts-layout-options .shadow-settings-group');
    app.setDefaultState('#recent-posts-layout-options .border-settings-group', 'default');
    app.setBorderValues('#recent-posts-layout-options .border-settings-group');
    if (app.edit.type == 'related-posts') {
        checkRecentPostsAppType('', $g('#recent-posts-settings-dialog'));
        $g('#recent-posts-settings-dialog .related-posts-display-select input[type="hidden"]').val(app.edit.related);
        value = $g('#recent-posts-settings-dialog .related-posts-display-select li[data-value="'+app.edit.related+'"]').text();
        $g('#recent-posts-settings-dialog .related-posts-display-select input[readonly]').val(value.trim());
        app.recentPostsCallback = 'getRelatedPosts';
    } else if (app.edit.type == 'recent-posts') {
        checkRecentPostsAppType(app.edit.app, $g('#recent-posts-settings-dialog'));
        if (!('featured' in app.edit)) {
            app.edit.featured = false;
        }
        if (!app.edit.desktop.pagination) {
            app.edit.layout.pagination = '';
            app.edit.desktop.pagination = {
                "typography":{
                    "font-family":"@default",
                    "font-size":10,
                    "font-style":"normal",
                    "font-weight":"700",
                    "letter-spacing":4,
                    "line-height":26,
                    "text-align":"center",
                    "text-decoration":"none",
                    "text-transform":"uppercase"
                },
                "margin":{
                    "bottom":"25",
                    "top":"25"
                },
                "padding":{
                    "bottom":"20",
                    "left":"80",
                    "right":"80",
                    "top":"20"
                },
                "border":{
                    "color":"@border",
                    "radius":"50",
                    "style":"solid",
                    "width":"0"
                },
                "normal":{
                    "color":"@title-inverse",
                    "background":"@primary"
                },
                "hover":{
                    "color":"@title-inverse",
                    "background":"@hover"
                },
                "shadow":{
                    "value":"0",
                    "color":"@shadow"
                }
            }
        }
        $g('#recent-posts-settings-dialog .recent-posts-pagination-select input[type="hidden"]').val(app.edit.layout.pagination);
        value = $g('#recent-posts-settings-dialog .recent-posts-pagination-select li[data-value="'+app.edit.layout.pagination+'"]').text();
        $g('#recent-posts-settings-dialog .recent-posts-pagination-select input[readonly]').val(value.trim());
        $g('#recent-posts-settings-dialog input[data-option="featured"]').prop('checked', app.edit.featured);
        

        app.recentPostsCallback = 'getRecentPosts';
    } else if (app.edit.type == 'post-navigation') {
        app.recentPostsCallback = 'getPostNavigation';
    } else if (app.edit.type == 'search-result' || app.edit.type == 'store-search-result') {
        if (!app.edit.desktop.pagination.typography) {
            app.edit.layout.pagination = '';
            app.edit.desktop.pagination = {
                "typography":{
                    "font-family":"@default",
                    "font-size":"16",
                    "font-style":"normal",
                    "font-weight":"@default",
                    "letter-spacing":"0",
                    "line-height":26,
                    "text-align":"center",
                    "text-decoration":"none",
                    "text-transform":"none",
                    "custom":""
                },
                "margin":{
                    "default":{
                        "bottom":"0",
                        "top":"50"
                    },
                    "state":false,
                    "transition":{
                        "duration":0.3,
                        "x1":0.42,
                        "y1":0,
                        "x2":0.58,
                        "y2":1
                    }
                },
                "padding":{
                    "default":{
                        "bottom":"4",
                        "left":"8",
                        "right":"8",
                        "top":"4"
                    },
                    "state":false,
                    "transition":{
                        "duration":0.3,
                        "x1":0.42,
                        "y1":0,
                        "x2":0.58,
                        "y2":1
                    }
                },
                "border":{
                    "default":{
                        "color":"@border",
                        "radius":"0",
                        "style":"solid",
                        "width":"0",
                        "bottom":0,
                        "left":0,
                        "top":0,
                        "right":0
                    },
                    "state":false,
                    "transition":{
                        "duration":0.3,
                        "x1":0.42,
                        "y1":0,
                        "x2":0.58,
                        "y2":1
                    }
                },
                "shadow":{
                    "default":{
                        "value":"0",
                        "color":"@shadow"
                    },
                    "state":false,
                    "transition":{
                        "duration":0.3,
                        "x1":0.42,
                        "y1":0,
                        "x2":0.58,
                        "y2":1
                    }
                },
                "colors":{
                    "default":{
                        "color": app.edit.desktop.pagination.color,
                        "background-color":"rgba(255, 255, 255, 0)"
                    },
                    "hover":{
                        "color":app.edit.desktop.pagination.hover,
                        "background-color":"rgba(255, 255, 255, 0)"
                    },
                    "state":true,
                    "transition":{
                        "duration":0.3,
                        "x1":0.42,
                        "y1":0,
                        "x2":0.58,
                        "y2":1
                    }
                }
            }
        }
        $g('#recent-posts-settings-dialog .search-result-pagination-select input[type="hidden"]').val(app.edit.layout.pagination);
        value = $g('#recent-posts-settings-dialog .search-result-pagination-select li[data-value="'+app.edit.layout.pagination+'"]').text();
        $g('#recent-posts-settings-dialog .search-result-pagination-select input[readonly]').val(value.trim());
        app.recentPostsCallback = null
    }
    if (app.edit.type == 'related-posts' || app.edit.type == 'recent-posts') {
        $g('#recent-posts-settings-dialog .recent-posts-display-select input[type="hidden"]').val(app.edit.sorting);
        value = $g('#recent-posts-settings-dialog .recent-posts-display-select li[data-value="'+app.edit.sorting+'"]').text();
        $g('#recent-posts-settings-dialog .recent-posts-display-select input[readonly]').val(value.trim());
    }
    $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
    if (!app.edit.desktop.overlay.gradient) {
        app.edit.desktop.overlay.type = 'color';
        app.edit.desktop.overlay.gradient = {
            "effect": "linear",
            "angle": 45,
            "color1": "@bg-dark",
            "position1": 25,
            "color2": "@bg-dark-accent",
            "position2": 75
        }
    }
    if (!('blur' in app.edit.desktop.overlay)) {
        app.edit.desktop.overlay.blur = 10;
    }
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
    app.setDefaultState('#recent-posts-settings-dialog .overlay-settings-group', 'default');
    if (!('blur' in app.edit.desktop['overlay-states'].default)) {
        app.edit.desktop['overlay-states'].default.blur = app.edit.desktop.overlay.blur;
    }
    app.setOverlayValues('#recent-posts-settings-dialog .overlay-settings-group');
    if (!app.edit.tag) {
        app.edit.tag = 'h3';
    }
    $g('#recent-posts-settings-dialog input[data-group="view"][type="checkbox"]').each(function(){
        if (this.dataset.option in app.edit.desktop.view) {
            this.checked = app.edit.desktop.view[this.dataset.option];
        }
    });
    app.setDefaultState('#recent-posts-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#recent-posts-layout-options .margin-settings-group');
    setDisableState('#recent-posts-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#recent-posts-settings-dialog .ba-style-custom-select input[type="hidden"]').val('image');
    $g('#recent-posts-settings-dialog .ba-style-custom-select input[readonly]').val(gridboxLanguage['IMAGE']);
    $g('#recent-posts-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
    $g('#recent-posts-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
    showBaStyleDesign('image', document.querySelector('#recent-posts-settings-dialog .ba-style-custom-select'));
    setTimeout(function(){
        $g('#recent-posts-settings-dialog').modal();
    }, 150);
}

function getPostNavigation()
{
    app.editor.$g(app.selector).attr('data-maximum', app.edit.maximum);
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.getPostNavigation&tmpl=component",
        data: {
            id : app.editor.themeData.id,
            edit_type: app.editor.themeData.edit_type,
            maximum : app.edit.maximum
        },
        complete: function(msg){
            app.editor.document.querySelector(app.selector+' .ba-blog-posts-wrapper').innerHTML = msg.responseText;
            app.editor.$g(app.selector+' .ba-blog-posts-wrapper .ba-blog-post').each(function(ind){
                let title = ind != 0 ? gridboxLanguage['NEXT'] : gridboxLanguage['PREVIOUS'],
                    href = $g(this).find('.ba-blog-post-title-wrapper .ba-blog-post-title a').attr('href'),
                    str = '<div class="ba-post-navigation-info"><a href="'+href+'">'+title+'</a></div>';
                $g(this).find('.ba-blog-post-title-wrapper').before(str);
            })
            replaceBlogPostsTag();
            app.editor.app.buttonsPrevent();
            app.sectionRules();
            app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
            app.editor.$g(app.selector).find('.ba-blog-post-button-wrapper a')
                .text(app.edit.buttonLabel ? app.edit.buttonLabel : 'Read More');
            checkAppFields($g('#recent-posts-settings-dialog'));
            app.addHistory();
        }
    });
}

function getRelatedPosts()
{
    app.editor.$g(app.selector).attr('data-app', app.edit.app).attr('data-count', app.edit.limit)
        .attr('data-related', app.edit.related).attr('data-maximum', app.edit.maximum);
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.getRelatedPosts&tmpl=component",
        data: {
            id : app.editor.themeData.id,
            edit_type: app.editor.themeData.edit_type,
            app : app.edit.app,
            limit : app.edit.limit,
            related : app.edit.related,
            sorting : app.edit.sorting,
            maximum : app.edit.maximum
        },
        complete: function(msg){
            app.editor.document.querySelector(app.selector+' .ba-blog-posts-wrapper').innerHTML = msg.responseText;
            replaceBlogPostsTag();
            app.editor.app.buttonsPrevent();
            app.sectionRules();
            app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
            app.editor.$g(app.selector).find('.ba-blog-post-button-wrapper a')
                .text(app.edit.buttonLabel ? app.edit.buttonLabel : 'Read More');
            checkAppFields($g('#recent-posts-settings-dialog'));
            app.addHistory();
        }
    });
}

function getRecentPosts()
{
    let array = [],
        category = tags = '';
    for (let key in app.edit.categories) {
        array.push(key);
    }
    category = array.join(',');
    array = [];
    for (let key in app.edit.tags) {
        array.push(key);
    }
    tags = array.join(',');
    checkRecentPostsAppType(app.edit.app, $g('#recent-posts-settings-dialog'));
    app.editor.$g(app.selector).attr('data-app', app.edit.app).attr('data-count', app.edit.limit)
        .attr('data-sorting', app.edit.sorting).attr('data-maximum', app.edit.maximum).attr('data-category', category);
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.getRecentPosts",
        data: {
            id : app.edit.app,
            limit : app.edit.limit,
            sorting : app.edit.sorting,
            category : category,
            tags: tags,
            type: app.edit.posts_type,
            maximum : app.edit.maximum,
            featured: Number(app.edit.featured),
            pagination: app.edit.layout.pagination
        },
        complete: function(msg){
            let obj = JSON.parse(msg.responseText);
            app.editor.$g(app.selector+' .ba-blog-posts-pagination').remove();
            app.editor.$g(app.selector+' .ba-blog-posts-wrapper').html(obj.posts).after(obj.pagination);
            replaceBlogPostsTag();
            app.editor.app.buttonsPrevent();
            app.sectionRules();
            app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
            app.editor.$g(app.selector).find('.ba-blog-post-button-wrapper a')
                .text(app.edit.buttonLabel ? app.edit.buttonLabel : 'Read More');
            checkAppFields($g('#recent-posts-settings-dialog'));
            app.addHistory();
        }
    });
}

$g('.recent-posts-pagination-select').on('customAction', function(){
    app.edit.layout.pagination = this.querySelector('input[type="hidden"]').value;
    getRecentPosts();
});

$g('.search-result-pagination-select').on('customAction', function(){
    app.edit.layout.pagination = this.querySelector('input[type="hidden"]').value;
    let type = app.edit.layout.pagination,
        style = '';
    app.editor.$g(app.selector+' .ba-blog-posts-pagination').each((i, $this) => {
        style = (type == '' && !$this.dataset.type) || ((type == 'load-more' || type == 'load-more-infinity') && $this.dataset.type) ? '' : 'none';
        $this.style.display = style;
    });
    app.addHistory();
});

app.modules.recentPostsEditor = true;
app.recentPostsEditor();