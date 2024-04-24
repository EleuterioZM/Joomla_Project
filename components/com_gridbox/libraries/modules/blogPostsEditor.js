/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.blogPostsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#blog-posts-settings-dialog');
    $g('#blog-posts-settings-dialog .active').removeClass('active');
    $g('#blog-posts-settings-dialog a[href="#blog-posts-general-options"]').parent().addClass('active');
    $g('#blog-posts-general-options').addClass('active');
    $g('#blog-posts-settings-dialog').attr('data-edit', app.edit.type);
    app.setDefaultState('#blog-posts-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#blog-posts-layout-options .margin-settings-group');
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    if (!('pagination' in app.edit)) {
        app.edit.pagination = '';
    }
    if (!app.edit.desktop.pagination.typography) {
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
    if (!app.edit.desktop.image.size) {
        app.edit.desktop.image.size = 'cover';
    }
    if (!app.edit.desktop.padding) {
        app.edit.desktop.padding = {
            "bottom":0,
            "left": 0,
            "right": 0,
            "top":0
        }
    }
    if (!app.edit.desktop.image.border) {
        app.edit.desktop.image.border = {
            "color":"@border",
            "radius":0,
            "style":"solid",
            "width":"0"
        }
    }
    if (!app.edit.desktop.price) {
        ['desktop', 'laptop', 'phone', 'phone-portrait', 'tablet', 'tablet-portrait'].forEach(function(key){
            if (app.edit[key].title) {
                app.edit[key].price = $g.extend(true, {}, app.edit[key].title);
            }
        })
    }
    if (!app.edit.info) {
        app.edit.info = ['author', 'date', 'category', 'hits', 'comments'];
    }
    value = app.getValue('view', 'gutter');
    $g('#blog-posts-settings-dialog [data-option="gutter"]')[0].checked = value;
    setDisableState('#blog-posts-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.setDefaultState('#blog-posts-layout-options .border-settings-group', 'default');
    app.setBorderValues('#blog-posts-layout-options .border-settings-group');
    $g('#blog-posts-settings-dialog .blog-posts-layout-select input[type="hidden"]').val(app.edit.layout.layout);
    value = $g('#blog-posts-settings-dialog .blog-posts-layout-select li[data-value="'+app.edit.layout.layout+'"]').text();
    $g('#blog-posts-settings-dialog .blog-posts-layout-select input[readonly]').val(value.trim());


    $g('#blog-posts-settings-dialog .blog-posts-pagination-select input[type="hidden"]').val(app.edit.pagination);
    value = $g('#blog-posts-settings-dialog .blog-posts-pagination-select li[data-value="'+app.edit.pagination+'"]').text();
    $g('#blog-posts-settings-dialog .blog-posts-pagination-select input[readonly]').val(value.trim());


    $g('#blog-posts-settings-dialog .ba-style-custom-select input[type="hidden"]').val('image');
    $g('#blog-posts-settings-dialog .ba-style-custom-select input[readonly]').val(app._('IMAGE'));
    app.setDefaultState('#blog-posts-settings-dialog .background-settings-group', 'default');
    app.setFeatureBackgroundValues('#blog-posts-settings-dialog .background-settings-group');
    app.setDefaultState('#blog-posts-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#blog-posts-layout-options .shadow-settings-group');
    app.setDefaultState('#blog-posts-layout-options .padding-settings-group', 'default');
    app.setPaddingValues('#blog-posts-layout-options .padding-settings-group');
    value = app.getValue('view', 'count');
    $g('#blog-posts-settings-dialog input[data-option="count"]').val(value);
    $g('#blog-posts-settings-dialog input[data-option="maximum"]').val(app.edit.maximum);
    $g('#blog-posts-settings-dialog input[data-option="limit"]').val(app.edit.limit);
    if (!app.edit.order) {
        app.edit.order = 'created';
    }
    $g('.blog-posts-sort-select input[type="hidden"]').val(app.edit.order);
    value = $g('.blog-posts-sort-select li[data-value="'+app.edit.order+'"]').text().trim();
    $g('.blog-posts-sort-select input[type="text"]').val(value);
    if (typeof(app.edit.desktop.overlay) == 'string') {
        app.edit.desktop.overlay = {
            color: app.edit.desktop.overlay
        }
    }
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
    if (!app.edit.tag) {
        app.edit.tag = 'h3';
    }
    app.setDefaultState('#blog-posts-settings-dialog .overlay-settings-group', 'default');
    if (!('blur' in app.edit.desktop['overlay-states'].default)) {
        app.edit.desktop['overlay-states'].default.blur = app.edit.desktop.overlay.blur;
    }
    app.setOverlayValues('#blog-posts-settings-dialog .overlay-settings-group');
    $g('#blog-posts-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
    $g('#blog-posts-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
    showBaStyleDesign('image', document.querySelector('#blog-posts-settings-dialog .ba-style-custom-select'));
    $g('.blog-posts-cover-options').hide();
    $g('.blog-posts-background-options').css('display', '');
    $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
    if (app.edit.layout.layout == 'ba-classic-layout' || app.edit.layout.layout == 'ba-one-column-grid-layout') {
        $g('.blog-posts-grid-options').hide();
        $g('.blog-posts-grid-options input[data-option="count"]').closest('.ba-settings-group');
    } else {
        $g('.blog-posts-grid-options').css('display', '');
        $g('.blog-posts-grid-options input[data-option="count"]').closest('.ba-settings-group');
        if (app.edit.layout.layout == 'ba-cover-layout') {
            $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
            $g('.blog-posts-cover-options').css('display', '');
            $g('.blog-posts-background-options').hide();
        }
    }
    $g('.blog-posts-view-options input[type="checkbox"]').each(function(){
        this.checked = app.edit.desktop.view[this.dataset.option];
    });
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
    setTimeout(function(){
        $g('#blog-posts-settings-dialog').modal();
    }, 150);
}

$g('#blog-posts-settings-dialog').find('input[data-option="maximum"], input[data-option="limit"]').on('input', function(){
    clearTimeout(delay);
    var $this = this,
        value = 0;
    delay = setTimeout(function(){
        if ($this.value) {
            value = $this.value
        }
        app.edit[$this.dataset.option] = value;
        getBlogPosts(app.edit.maximum, app.edit.limit, app.editor.themeData.id, app.edit.order);
    }, 500);
});

$g('.blog-posts-sort-select').on('customAction', function(){
    app.edit.order = this.querySelector('input[type="hidden"]').value;
    getBlogPosts(app.edit.maximum, app.edit.limit, app.editor.themeData.id, app.edit.order);
});

$g('.blog-posts-pagination-select').on('customAction', function(){
    app.edit.pagination = this.querySelector('input[type="hidden"]').value;
    getBlogPosts(app.edit.maximum, app.edit.limit, app.editor.themeData.id, app.edit.order);
});

function getBlogPosts(max, limit, id, order)
{
    var posts = false,
        pagination = app.edit.pagination ? app.edit.pagination : '',
        pag = false;
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.getBlogPosts",
        data:{
            max: max,
            limit: limit,
            order: order,
            id: id,
            pagination: pagination
        },
        complete: function(msg){
            app.editor.document.querySelector(app.selector+' .ba-blog-posts-wrapper').innerHTML = msg.responseText;
            app.editor.app.buttonsPrevent();
            posts = true;
            app.sectionRules();
            app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
            if (posts && pag) {
                app.addHistory();
            }
        }
    });
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.getBlogPagination",
        data:{
            max: max,
            limit: limit,
            id: id,
            pagination: pagination
        },
        complete: function(msg){
            app.editor.document.querySelector(app.selector+' .ba-blog-posts-pagination-wrapper').innerHTML = msg.responseText;
            app.editor.app.buttonsPrevent();
            pag = true;
            if (posts && pag) {
                app.addHistory();
            }
        }
    });
}

app.modules.blogPostsEditor = true;
app.blogPostsEditor();