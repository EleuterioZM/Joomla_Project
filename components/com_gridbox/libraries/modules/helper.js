/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var file = document.createElement('link'),
    color = '',
    sortingList = [],
    fontBtn = '',
    toolbarButtons = {
        'text-decoration' : true,
        'text-transform' : true,
        'font-style' : true,
        'text-align' : true
    };
file.rel = 'stylesheet';
file.href = JUri+'components/com_gridbox/libraries/minicolors/css/minicolors.css';
document.head.append(file);
file = document.createElement('script');
file.src = JUri+'components/com_gridbox/libraries/minicolors/js/minicolors.js';
file.onload = function(){
    $g('.variables-color-picker').minicolors({
        opacity: true,
        theme: 'bootstrap',
        change: function(hex, opacity) {
            let rgba = $g(this).minicolors('rgbaString');
            fontBtn.value = hex;
            $g('.variables-color-picker').closest('#color-picker-cell').find('.minicolors-opacity').val(opacity * 1);
            fontBtn.dataset.rgba = rgba;
            app.editor.$g(fontBtn).trigger('minicolorsInput');
            $g(fontBtn).trigger('minicolorsInput').next().find('.minicolors-swatch-color')
                .css('background-color', rgba).closest('.minicolors').next()
                .find('.minicolors-opacity').val(opacity * 1).removeAttr('readonly');
        }
    });
    $g('.color-variables-group').on('click', '.color-variables-item', function(){
        var variable = this.dataset.variable,
            value = app.editor.app.theme.colorVariables[variable].color,
            color = rgba2hex(value);
        fontBtn.value = variable;
        fontBtn.dataset.rgba = variable;
        $g(fontBtn).trigger('minicolorsInput').next().find('.minicolors-swatch-color')
            .css('background-color', value).closest('.ba-settings-item')
            .find('.minicolors-opacity').val('').attr('readonly', true);
        $g(this).trigger('mouseleave');
        $g('#color-variables-dialog').modal('hide');
    });
    $g('#color-variables-dialog').on('show', function(){
        $g(this).find('.color-variables-item').each(function(){
            var color = app.editor.app.theme.colorVariables[this.dataset.variable].color;
            $g(this).find('.color-varibles-color-swatch').css('background-color', color);
        });
    }).on('hide', function(){
        app.addHistory();
    });
    /*
    if ('EyeDropper' in window) {
        app.eyeDropper = new EyeDropper();
        $g('.ba-eyedropper-icon').on('click', function(){
            app.eyeDropper.open().then(function(result){
                setMinicolorsColor(result.sRGBHex, true);
            });
        });
    } else {
        $g('.ba-eyedropper-icon').remove();
    }
    */
    $g('.ba-eyedropper-icon').remove();
}
app.modules.helper = true;

app.setFieldsSortable = function($this){
    $g($this).sortable({
        handle : '> .blog-post-editor-options-group > .blog-post-editor-group-element > .sorting-handle i',
        selector : '> .blog-post-editor-options-group',
        group : 'fields'
    });
}

function showDataTagsDialog(dialog)
{
    var rect = fontBtn.getBoundingClientRect(),
        modal = $g('#'+dialog),
        width = modal.innerWidth(),
        height = modal.innerHeight(),
        top = rect.bottom - height / 2 - rect.height / 2,
        left = rect.left - width - 10,
        margin = 20,
        bottom = '50%';
    if (window.innerHeight - top < height) {
        top = window.innerHeight - height - margin;
        bottom = (window.innerHeight - rect.bottom + rect.height / 2 - margin)+'px';
    } else if (top < 0) {
        top = margin;
        bottom = (height - rect.bottom + rect.height / 2 + margin)+'px';
    }
    if (modal[0].dataset.position == 'right') {
        left = rect.right + 10;
    }
    modal.css({
        left: left,
        top: top
    }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
}

function checkRecentPostsAppType(id, modal)
{
    let type = modal.find('.recent-posts-app-select li[data-value="'+id+'"]').attr('data-type'),
        ul = modal.find('.recent-posts-display-select ul');
    ul.find('li[data-value="top_selling"], li[data-value="event-date"]').remove();
    if (type == 'products') {
        ul.append('<li data-value="top_selling">'+app._('BEST_SELLING')+'</li>');
    } else if (type != 'blog') {
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.checkEventField', {
            id: id
        }).then(function(text){
            let obj = JSON.parse(text);
            if (obj.flag) {
                ul.append('<li data-value="event-date">'+app._('EVENT_DATE')+'</li>');
            } else if (app.edit.sorting == 'event-date') {
                app.edit.sorting = 'created';
                ul.closest('.ba-custom-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = 'created';
                    this.querySelector('input[type="text"]').value = app._('RECENT');
                });
            }
            if (app.edit.sorting == 'event-date') {
                ul.closest('.ba-custom-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = 'event-date';
                    this.querySelector('input[type="text"]').value = app._('EVENT_DATE');
                });
            }
        })
    }
    if (app.edit.sorting == 'top_selling' && type != 'products') {
        app.edit.sorting = 'created';
        ul.closest('.ba-custom-select').each(function(){
            this.querySelector('input[type="hidden"]').value = 'created';
            this.querySelector('input[type="text"]').value = app._('RECENT');
        });
    }
}

function checkAppFields(modal)
{
    if (app.edit.type == 'store-search-result' || app.edit.type == 'recently-viewed-products') {
        modal.find('.category-list-fields-view-options, li[data-value="postFields"]').hide();
        modal.find('.category-list-store-view-options').css('display', '');
        if (!app.edit.desktop.price) {
            ['desktop', 'laptop', 'phone', 'phone-portrait', 'tablet', 'tablet-portrait'].forEach(function(key){
                if (app.edit[key].title) {
                    app.edit[key].price = $g.extend(true, {}, app.edit[key].title);
                }
            })
        }
    } else if (app.edit.type != 'author' && app.edit.type != 'search-result') {
        let id = app.edit.type == 'recent-posts' || app.edit.type == 'recent-posts-slider' ? app.edit.app : app.editor.themeData.id;
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.checkAppFields', {
            id: id
        }).then(function(text){
            if (text != '' && text != 'blog') {
                modal.find('.category-list-fields-view-options, li[data-value="postFields"]').css('display', '');
            } else {
                modal.find('.category-list-fields-view-options, li[data-value="postFields"]').hide();
            }
            if (text == 'products') {
                modal.find('.category-list-store-view-options').css('display', '');
                modal.find('li[data-value="price"]').css('display', '');
            } else {
                modal.find('.category-list-store-view-options').hide();
                modal.find('li[data-value="price"]').hide();
            }
            if (text == 'products' && !app.edit.desktop.price) {
                ['desktop', 'laptop', 'phone', 'phone-portrait', 'tablet', 'tablet-portrait'].forEach(function(key){
                    if (app.edit[key].title) {
                        app.edit[key].price = $g.extend(true, {}, app.edit[key].title);
                    }
                })
            }
        })
    } else {
        modal.find('.category-list-fields-view-options, li[data-value="postFields"], .category-list-store-view-options').hide();
    }
}

document.head.appendChild(file);
file = document.createElement('script');
file.src = JUri+'components/com_gridbox/libraries/sortable/sortable.js';
file.onload = function(){
    $g('#customer-info-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle i',
        selector : '> .sorting-item',
        group : 'tabs',
        change: function(){
            let container = [],
                div = app.editor.$g(app.selector+' .ba-checkout-form-wrapper'),
                array = [];
            $g('#customer-info-settings-dialog .sorting-item').each(function(i){
                let div = app.editor.$g(app.selector+' .ba-checkout-form-fields:nth-child('+(this.dataset.key * 1 + 1)+')')[0],
                    obj = app.edit.items[this.dataset.key];
                obj.order_list = i;
                container.push(div);
                array.push(obj);
                this.dataset.key = i;
            });
            app.edit.items = array;
            container.forEach(function(field){
                div.append(field);
            });
        }
    });
    $g('#product-variations-photos-dialog .sorting-container').sortable({
        handle : '> .sorting-item i.sortable-handle',
        selector : '> .sorting-item',
        group : 'product-variations-photos',
        change: function(){
            reorderVariationsPhotos();
        }
    });
    $g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-container').each(function(ind){
        $g(this).sortable({
            handle : '> .sorting-item .sorting-handle i, > .sorting-item .sorting-handle',
            selector : '> .sorting-item',
            group : 'field-sorting-'+ind,
            change: function(el){
                if (el.closest('.blog-post-editor-options-group[data-field-type="product-options"]')) {
                    prepareProductVariations();
                }
            }
        });
    });
    $g('.renewal-plans').sortable({
        handle : '> .renewal-plan .sorting-handle i',
        selector : '> .renewal-plan',
        group : 'renewal-plans'
    });
    $g('#category-list-fields-modal .category-list-fields-view-wrapper').sortable({
        handle : '.sorting-handle i',
        selector : '> .fields-view-row',
        group : 'field-groups',
        change : function(){
            let array = [],
                key = null;
            $g('#category-list-fields-modal .fields-view-row').each(function(){
                if (!key) {
                    key = this.dataset.group;
                }
                array.push(this.dataset.id);
            });
            app.edit[key] = array;
            app.sectionRules();
            app.addHistory();
            if (app.edit.type == 'recent-posts-slider' || app.edit.type == 'related-posts-slider' 
                || app.edit.type == 'recently-viewed-products') {
                var object = {
                    data : app.edit,
                    selector : app.editor.app.edit
                };
                app.editor.app.checkModule('initItems', object);
            }
        }
    });
    $g('#blog-post-editor-fields-options .ba-app-fields-groups-wrapper').sortable({
        handle : '> .ba-fields-group-wrapper .ba-fields-group-title i.zmdi-apps',
        selector : '> .ba-fields-group-wrapper',
        group : 'field-groups'
    });
    $g('#blog-post-editor-fields-options .ba-fields-group-wrapper .ba-fields-group:not([data-disable-sorting])').each(function(){
        app.setFieldsSortable(this);
    });
    $g('#group-field-item-dialog, #customer-info-item-dialog').find('.sorting-container').sortable({
        handle : '.sorting-handle i',
        selector : '> .sorting-item',        
        group : 'group-field-tabs'
    });
    $g('#field-settings-dialog .fields-plugin-options .sorting-container').sortable({
        handle : '.sorting-handle i',
        selector : '> .sorting-item',
        change : function(){
            var ind = 0,
                slides = {};
            $g('#field-settings-dialog .sorting-item').each(function(){
                slides[ind++] = app.edit.options.items[this.dataset.key * 1];
            });
            app.edit.options.items = slides;
            drawFieldSortingList();
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#field-settings-dialog .fields-group-plugin-options .sorting-container').sortable({
        handle : '.sorting-handle i',
        selector : '> .sorting-item',
        change : function(){
            var li = [].slice.call(app.editor.document.querySelectorAll(app.selector+' .ba-field-wrapper[data-id]')),
                container = app.editor.$g(app.selector+' .ba-field-group-wrapper'),
                ind = 0,
                slides = {};
            $g('#field-settings-dialog .fields-group-plugin-options .sorting-item').each(function(){
                slides[ind++] = app.edit.items[this.dataset.key * 1];
                container.append(li[this.dataset.key * 1]);
            });
            app.edit.items = slides;
            drawFieldGroupSortingList();
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#feature-box-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            var container = app.editor.$g(app.selector+' .ba-feature-box-wrapper'),
                list = {},
                slist = {},
                i = 0;
            $g('#feature-box-settings-dialog .sorting-item').each(function(ind){
                let key = this.dataset.key * 1;
                this.dataset.key = i;
                slist[i] = sortingList[key];
                list[i++] = app.edit.items[key];
                container.append(sortingList[key].parent);
            });
            app.edit.items = list;
            sortingList = slist;
            app.sectionRules();
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#testimonials-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            var i = 1,
                container = app.editor.$g(app.selector+' .slideshow-content'),
                slides = {};
            $g('#testimonials-settings-dialog .sorting-item').each(function(){
                let key = this.dataset.key * 1;
                slides[i++] = app.edit.slides[key];
                container.append(sortingList[key].parent);
            });
            app.edit.slides = slides;
            drawTestimonialsSortingList();
            app.testimonialsCallback();
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#icon-list-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            var container = app.editor.$g(app.selector+' ul'),
                list = {},
                slist = {},
                i = 1;
            $g('#icon-list-settings-dialog .sorting-item').each(function(ind){
                let key = this.dataset.key * 1;
                this.dataset.key = i;
                slist[i] = sortingList[key];
                list[i++] = app.edit.list[key];
                container.append(sortingList[key].parent);
            });
            app.edit.list = list;
            sortingList = slist;
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#intro-post-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle i',
        selector : '> .sorting-item',
        change : function(){
            var div = document.createElement('div'),
                wrapper = app.editor.document.querySelector('#'+app.editor.app.edit+' .intro-post-wrapper');
            $g('#intro-post-settings-dialog .sorting-container .sorting-item').each(function(){
                var key = this.dataset.key.replace('title', 'title-wrapper').replace('image', 'image-wrapper');
                div.appendChild(wrapper.querySelector('.intro-post-'+key));
            });
            $g(div.children).each(function(){
                wrapper.appendChild(this)
            });
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#content-slider-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            var content = app.editor.$g(app.selector+' > .slideshow-wrapper > ul > .slideshow-content'),
                i = 1,
                key = null,
                slides = {};
            $g('#content-slider-settings-dialog .sorting-container .sorting-item').each(function(key){
                key = this.dataset.key * 1;
                slides[i++] = app.edit.slides[key];
                content.append(sortingList[key].parent);
            });
            app.edit.slides = slides;
            drawContentSliderSortingList();
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            }
            app.editor.app.checkModule('initItems', object);
            app.sectionRules();
            app.addHistory();
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
        },
        group : 'tabs'
    });
    $g('#tabs-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            let list = {},
                query = app.edit.type == 'accordion' ? '.accordion' : '.ba-tabs-wrapper > ul',
                wrapper = app.editor.document.querySelector('#'+app.editor.app.edit+' > '+query);
            $g('#tabs-settings-dialog .sorting-container .sorting-item').each(function(){
                let key = this.dataset.key;
                list[key] = sortingList[key];
                wrapper.append(list[key].parent);
            });
            sortingList = list;
            app.addHistory();
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
        },
        group : 'tabs'
    });
    $g('#menu-settings-dialog .one-page-options .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            let list = {},
                wrapper = app.editor.document.querySelector('#'+app.editor.app.edit+' ul.nav.menu');
            $g('#menu-settings-dialog .one-page-options .sorting-container .sorting-item').each(function(){
                var key = this.dataset.key * 1;
                list[key] = sortingList[key];
                wrapper.append(sortingList[key].parent);
            });
            sortingList = list;
            app.addHistory();
        },
        group : 'tabs'
    });
    $g('#menu-settings-dialog .menu-options .sorting-container').sortable({
        handle : '> .sorting-item-wrapper > .sorting-item > .sorting-handle',
        selector : '> .sorting-item-wrapper',
        change : function(dragEl){
            sortMenuItems();
        },
        group : 'menu-items'
    });
    $g('#slideshow-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            let content = app.editor.document.querySelector('#'+app.editor.app.edit+' .slideshow-content'),
                slides = {},
                items = $g('#slideshow-settings-dialog .sorting-container .sorting-item'),
                key = null,
                i = 1;
            items.each(function(){
                key = this.dataset.key * 1;
                slides[i++] = app.edit.desktop.slides[key];
                content.append(sortingList[key].parent);
            });
            app.edit.desktop.slides = slides;
            for (var point in app.editor.breakpoints) {
                if (app.edit[point] && app.edit[point].slides) {
                    slides = {};
                    i = 1;
                    items.each(function(){
                        key = this.dataset.key * 1;
                        slides[i++] = app.edit[point].slides[key];
                    });
                    app.edit[point].slides = slides;
                }
            }
            getSlideshowSorting();
            app.sectionRules();
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            }
            app.editor.app.checkModule('initItems', object);
            app.addHistory();
        },
        group : 'slide'
    });
    $g('#item-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            let wrapper = app.editor.$g(app.selector+' .instagram-wrapper'),
                list = wrapper.find('.empty-list');
            $g('#item-settings-dialog .sorting-container .sorting-item').each(function(){
                let image = sortingList[this.dataset.key * 1].parent
                if (list.length > 0) {
                    list.before(image);
                } else {
                    wrapper.append(image);
                }
            });
            getSimpleSortingList();
            app.addHistory();
        },
        group : 'slide'
    });
    $g('#social-icons-settings-dialog .sorting-container').sortable({
        handle : '.sorting-handle',
        selector : '> .sorting-item',
        change : function(){
            let container = app.editor.$g(app.selector+' .ba-icon-wrapper'),
                list = {},
                slist = {},
                i = 0;
            $g('#social-icons-settings-dialog .sorting-item').each(function(ind){
                let key = this.dataset.key * 1;
                this.dataset.key = i;
                slist[i] = sortingList[key];
                list[i++] = app.edit.icons[key];
                container.append(sortingList[key].parent);
            });
            app.edit.icons = list;
            sortingList = slist;
            app.addHistory();
        },
        group : 'slide'
    });
}
document.head.append(file);

app.sectionRules = function(){
    var obj = {
        callback : 'sectionRules'
    }
    app.editor.app.listenMessage(obj);
    if (app.edit && app.edit.type && (app.edit.type == 'blog-posts' || app.edit.type == 'recent-posts'
        || app.edit.type == 'recent-reviews' || app.edit.type == 'search-result' || app.edit.type == 'store-search-result'
        || app.edit.type == 'post-navigation'|| app.edit.type == 'related-posts' || app.edit.type == 'categories')) {
        app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
    }
}

app.setTypography = function(parent, target, subgroup){
    var obj = app.edit.desktop[target],
        parentObject = app.editor.app.theme.desktop,
        fontKey = 'body';
    if (app.view != 'desktop') {
        parent.find('.desktop-only').hide();
    } else if (target != 'links') {
        parent.find('.desktop-only').removeAttr('style');
    }
    if (!subgroup) {
        parent.find('[data-group]').attr('data-group', target);
    } else {
        obj = app.edit.desktop[target][subgroup];
    }
    if (app.edit.type == 'text' || app.edit.type == 'headline' || app.edit.type == 'icon-list') {
        obj = $g.extend(true, {}, app.editor.app.theme.desktop[target], obj);
        fontKey = target;
    }
    if (app.editor.$g(app.selector).closest('footer.footer').length > 0) {
        parentObject = app.editor.app.footer.desktop;
    }
    var parentFont = parentObject[fontKey]['font-family'];
    if (parentFont == '@default') {
        parentFont = parentObject.body['font-family'];
    }
    for (var key in obj) {
        var element = parent.find('[data-option="'+key+'"][data-group="'+target+'"]'),
            display = '',
            val = app.getValue(target, key);
        if (subgroup) {
            val = app.getValue(target, key, subgroup);
            element = parent.find('[data-option="'+key+'"][data-group="'+target+'"][data-subgroup="'+subgroup+'"]');
        }
        if ((app.edit.type == 'text' || app.edit.type == 'headline' || app.edit.type == 'icon-list') && val == undefined) {
            val = getTextTypographyValue(target, key);
            display = 'none';
        } else if ((app.edit.type == 'text' || app.edit.type == 'headline' || app.edit.type == 'icon-list')
            && app.edit[app.view][target] && val != app.edit[app.view][target][key]) {
            display = 'none';
        }
        if (key == 'font-family'){
            var family = val == '@default' ? gridboxLanguage['INHERIT'] : val.replace(/\+/g, ' '),
                wrapper = $g('.font-family-wrapper').empty(),
                p = null;
            element.val(family).attr('data-value', val);
            if (target != 'body' || app.edit.type == 'icon-list') {
                p = document.createElement('p');
                p.innerHTML = '<span class="font-weight-title">'+gridboxLanguage['BASE_FONT_FAMILY']+
                    '</span><span class="font-family-title">'+parentFont.replace(/\+/g, ' ')+'</span>';
                p.style.fontFamily = "'"+parentFont.replace(/\+/g, ' ')+"'";
                p.dataset.value = '@default';
                wrapper.append(p);
            }
            for (var ind in fontsLibrary) {
                p = document.createElement('p');
                p.textContent = ind.replace(/\+/g, ' ');
                p.dataset.value = ind;
                p.style.fontFamily = "'"+ind.replace(/\+/g, ' ')+"'";
                wrapper.append(p);
                if (ind == val || (val == '@default' && ind == parentFont)) {
                    var weightWrapper = $g('.font-weight-wrapper').empty();
                    if (target != 'body' || app.edit.type == 'icon-list') {
                        var str = '<span class="font-weight-title">'+gridboxLanguage['BASE_FONT_WEIGHT'],
                            inheritFont = ind,
                            inheritWeight = parentObject[fontKey]['font-weight'];
                        if (inheritFont == '@default') {
                            inheritFont = parentObject[fontKey]['font-family'];
                        }
                        if (inheritFont == '@default') {
                            inheritFont = parentObject['body']['font-family'];
                        }
                        if (inheritWeight == '@default') {
                            inheritWeight = parentObject['body']['font-weight'];
                        }
                        str += '</span><span class="font-family-title">'+inheritWeight.replace('i', 'Italic')+'</span>';
                        var p = document.createElement('p');
                        p.dataset.value = '@default';
                        p.style.fontFamily = "'"+inheritFont.replace(/\+/g, ' ')+"'";
                        p.style.fontWeight = inheritWeight.replace('i', '');
                        p.style.fontStyle = inheritWeight.indexOf('i') == -1 ? 'normal' : 'italic';
                        p.innerHTML = str;
                        weightWrapper.append(p);
                    }
                    for (var i = 0; i < fontsLibrary[ind].length; i++) {
                        var weight = fontsLibrary[ind][i].styles,
                            str = '<span class="font-weight-title">'+weight.replace('i', ' Italic')+
                            '</span><span class="font-family-title">'+ind.replace(/\+/g, ' ')+'</span>';
                        p = document.createElement('p');
                        p.dataset.value = weight;
                        p.style.fontFamily = "'"+ind.replace(/\+/g, ' ')+"'";
                        p.style.fontWeight = weight.replace('i', '');
                        p.style.fontStyle = weight.indexOf('i') == -1 ? 'normal' : 'italic';
                        p.innerHTML = str;
                        weightWrapper.append(p)
                    }
                }
            }
        } else if (key == 'font-weight') {
            var weight = val == '@default' ? gridboxLanguage['INHERIT'] : val.replace('i', ' Italic');
            element.val(weight).attr('data-value', val);
        } else if (toolbarButtons[key]) {
            element.each(function(){
                if (this.dataset.value == val) {
                    $g(this).addClass('active');
                } else {
                    $g(this).removeClass('active');
                }
            });
        } else if (key == 'color' || key == 'hover-color' || key == 'hover') {
            updateInput(element, val);
        } else if (key == 'gradient') {
            parent.find('input[data-subgroup="gradient"][data-group="'+target+'"]').each(function(){
                value = app.getValue(target, this.dataset.option, 'gradient');
                if (this.dataset.type == 'color') {
                    updateInput($g(this), value);
                } else if (this.type == 'hidden') {
                    parent.find('.text-linear-gradient').hide();
                    parent.find('.text-'+value+'-gradient').css('display', '');
                    this.value = value;
                    value = parent.find('.gradient-effect-select li[data-value="'+value+'"]').text().trim();
                    parent.find('.gradient-effect-select input[type="text"]').val(value);
                } else {
                    app.setLinearInput($g(this), value);
                }
            });
        } else if (key == 'type') {
            element.val(val);
            value = parent.find('.text-type-select li[data-value="'+val+'"]').text().trim();
            parent.find('.text-type-select input[type="text"]').val(value);
            val = val == '' ? 'color' : val;
            parent.find('.text-color-options, .text-gradient-options').hide();
            parent.find('.text-'+val+'-options').css('display', '');
        } else {
            if (app.edit.type == 'text' || app.edit.type == 'headline') {
                element.closest('.ba-settings-item').find('> div:last-child').css('display', display);
            }
            app.setLinearInput(element, val)
        }
    }
}

$g('.text-type-select').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]'),
        parent = $g(this).closest('.typography-options');
    value = input.value;
    app.setValue(value, input.dataset.group, input.dataset.option);
    value = value == '' ? 'color' : value;
    parent.find('.text-color-options, .text-gradient-options').hide();
    parent.find('.text-'+value+'-options').css('display', '');
    app.sectionRules();
    app.addHistory();
});

function openPickerModal(modal, $this)
{
    if (!modal.hasClass('in')) {
        let rect = $this.getBoundingClientRect(),
            w = modal.width(),
            h = modal.height(),
            left = rect.left - w - 10,
            bottom = '50%',
            top = rect.bottom - ((rect.bottom - rect.top) / 2) - h / 2;
        if (rect.left < w + 50) {
            left = rect.right + 10;
            modal.attr('data-positioning', 'right');
        } else {
            modal.removeAttr('data-positioning');
        }
        if (modal[0].id == 'product-badges-dialog') {
            left = (rect.width + 10) * -1;
        }
        if (window.innerHeight - top < h + 25) {
            top = window.innerHeight - h - 25;
            bottom = ((window.innerHeight - rect.bottom + rect.height / 2) - 25)+'px';
        } else if (top < 25) {
            top = 25;
            bottom = ((h - rect.bottom + rect.height / 2) + 25)+'px';
        }
        modal.css({
            left: left,
            top: top
        }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
    }
}

$g('input[type="text"][data-option="font-family"]').on('click', function(){
    if (!$g('#font-family-dialog').hasClass('in')) {
        createFontFamilyList();
        var wrapper = $g('.font-family-wrapper');
        fontBtn = this;
        wrapper.find('p').not('p[data-value="@default"]').remove();
        for (var ind in fontsLibrary) {
            var p = document.createElement('p');
            p.textContent = ind.replace(/\+/g, ' ');
            p.dataset.value = ind;
            p.style.fontFamily = "'"+ind.replace(/\+/g, ' ')+"'";
            wrapper.append(p);
        }
        $g('#font-family-dialog .active').removeClass('active');
        $g('#font-family-dialog p[data-value="'+this.dataset.value+'"]').addClass('active');
    }
    openPickerModal($g('#font-family-dialog'), this);
});

$g('input[type="text"][data-option="font-weight"]').on('click', function(){
    if (!$g('#font-weight-dialog').hasClass('in')) {
        createFontFamilyList();
        fontBtn = this;
        $g('#font-weight-dialog .active').removeClass('active');
        $g('#font-weight-dialog p[data-value="'+this.dataset.value+'"]').addClass('active');
    }
    openPickerModal($g('#font-weight-dialog'), this);
});

$g('.open-font-library').on('click', function(){
    $g('.show-font-library').trigger('mousedown');
});

$g('.font-family-wrapper').on('click', 'p[data-value]', function(){
    fontBtn.dataset.value = this.dataset.value;
    fontBtn.value = this.dataset.value == '@default' ? gridboxLanguage['INHERIT'] : this.dataset.value.replace(/\+/g, ' ');
    $g(fontBtn).trigger('change');
    $g('#font-family-dialog').modal('hide');
});

$g('.font-weight-wrapper').on('click', 'p[data-value]', function(){
    fontBtn.dataset.value = this.dataset.value;
    fontBtn.value = this.dataset.value == '@default' ? gridboxLanguage['INHERIT'] : this.dataset.value.replace(/\+/g, ' ');
    $g(fontBtn).trigger('change');
    $g('#font-weight-dialog').modal('hide');
});

function createFontFamilyList()
{
    var link = '',
        str = '';
    for (var ind in fontsLibrary) {
        if (!fontsLibrary[ind][0].custom_src) {
            link += ind+":";
            for (var i = 0; i < fontsLibrary[ind].length; i++) {
                link += fontsLibrary[ind][i].styles;
                if (i < fontsLibrary[ind].length - 1) {
                    link += ',';
                }
            }
            link += '%7C';
        } else if (fontsLibrary[ind][0].custom_src != 'web-safe-fonts') {
            str += "@font-face {font-family: '"+ind.replace(/\+/g, ' ')+"';";
            str += 'font-weight : '+fontsLibrary[ind][0].styles+';';
            str += ' src: url('+JUri+'templates/gridbox/library/fonts/'+fontsLibrary[ind][0].custom_src+');}';
        }
    }
    if (link) {
        link = link.substring(0, link.length - 3);
        link += '&subset=latin,cyrillic,greek,latin-ext,greek-ext,vietnamese,cyrillic-ext';
        var file = document.createElement('link');
        file.rel = 'stylesheet';
        file.type = 'text/css';
        file.href = '//fonts.googleapis.com/css?family='+link;
        document.head.appendChild(file);
    }
    if (str) {
        var file = document.createElement('style');
        file.innerHTML = str;
        document.head.appendChild(file);
    }
}

function addSortingList(obj, key)
{
    let str = '<div class="sorting-item'+(obj.unpublish ? ' unpublished-sorting-item' : '')+'" data-key="'+key+'">';
    str += '<div class="sorting-checkbox"><label><input type="checkbox"><span></span></label></div>';
    if (obj.type == 'video') {
        str += '<div class="sorting-image sorting-handle">';
        str += '<i class="zmdi zmdi-play-circle-outline"></i>';
        str += '</div>';
    } else if (obj.type == 'color') {
        str += '<div class="sorting-image sorting-handle">';
        str += '<i class="zmdi zmdi-format-color-fill"></i>';
        str += '</div>';
    } else if (obj.type == 'gradient') {
        str += '<div class="sorting-image sorting-handle">';
        str += '<i class="zmdi zmdi-exposure-alt"></i>';
        str += '</div>';
    } else if (obj.type == 'none') {
        str += '<div class="sorting-image sorting-handle">';
        str += '<i class="zmdi zmdi-block"></i>';
        str += '</div>';
    } else if (obj.image) {
        let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
        str += '<div class="sorting-image sorting-handle">';
        str += '<img src="'+image+'">';
        str += '</div>'
    }
    str += '<div class="sorting-title sorting-handle">';
    if (obj.title) {
        str += obj.title;
    } else if (obj.icon) {
        str += obj.icon.replace('zmdi zmdi-', '').replace('fa fa-', '');
    }
    str += '</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit edit-sorting-item"></i></span>';
    if (app.edit.type != 'menu') {
        str += '<span><i class="zmdi zmdi-copy copy-sorting-item"></i></span>';
        str += '<span><i class="zmdi zmdi-eye-off unpublish-sorting-item"></i></span>';
    }
    str += '<span><i class="zmdi zmdi-delete delete-sorting-item"></i></span></div></div>';

    return str;
}

function editClass(val, target, obj)
{
    clearTimeout(delay);
    delay = setTimeout(function(){
        if (obj.suffix != 'ba-section' && obj.suffix != 'ba-row' && obj.suffix != 'ba-grid-column' && obj.suffix != 'ba-item') {
            var classNames = obj.suffix.split(' ');
            if (target == 'body') {
                target = app.editor.document.body;
            } else {
                target = app.editor.document.getElementById(target);
            }
            classNames.forEach(function(el, ind){
                if (el) {
                    target.classList.remove(el);
                }
            });
        }
        obj.suffix = val;
        classNames = obj.suffix.split(' ');
        classNames.forEach(function(el, ind){
            if (el) {
                target.classList.add(el);
            }
        });
        app.addHistory();
    }, 300);
}

function updateInput(input, rgba)
{
    if (rgba.indexOf('@') === 0) {
        var color = [],
            bg = '';
        if (app.editor.app.theme.colorVariables[rgba]) {
            bg = app.editor.app.theme.colorVariables[rgba].color;
        }
        color.push(rgba);
        color.push('');
    } else {
        var color = rgba2hex(rgba),
            bg = rgba;
    }
    $g(input).attr('data-rgba', rgba).val(color[0]).next().find('.minicolors-swatch-color').css('background-color', bg);
    input.closest('.minicolors').next().find('.minicolors-opacity').val(color[1]);
}

function rgba2hex(rgb)
{
    var parts = rgb.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
        hex = '#',
        part,
        color = [];
    if (parts) {
        for (var i = 1; i <= 3; i++) {
            part = parseInt(parts[i]).toString(16);
            if (part.length < 2) {
                part = '0'+part;
            }
            hex += part;
        }
        if (!parts[4]) {
            parts[4] = 1;
        }
        color.push(hex);
        color.push(parts[4] * 1);
        
        return color;
    } else {
        color.push(rgb.trim());
        color.push(1);
        
        return color;
    }
}

function setTabsAnimation()
{
    $g('.general-tabs > ul').off('show').on('show', function(event){
        event.stopPropagation();
        let ind = [],
            ul = $g(event.currentTarget),
            id = event.relatedTarget.hash,
            aId = event.target.hash;
        if (!id || !aId) {
            return;
        }
        ul.find('li a').each(function(i){
            if (this == event.target) {
                ind[0] = i;
            }
            if (this == event.relatedTarget) {
                ind[1] = i;
            }
        });
        if (ind[0] > ind[1]) {
            $g(id).addClass('out-left');
            $g(aId).addClass('right');
            setTimeout(function(){
                $g(id).removeClass('out-left');
                $g(aId).removeClass('right');
            }, 500);
        } else {
            $g(id).addClass('out-right');
            $g(aId).addClass('left');
            setTimeout(function(){
                $g(id).removeClass('out-right');
                $g(aId).removeClass('left');
            }, 500);
        }
        if ((event.target.hash == '#section-general-options' || event.target.hash == '#section-layout-options')
            && app.edit && app.edit.type == 'flipbox') {
            if (app.edit.side != 'frontside') {
                app.edit.side = 'frontside';
                app.editor.setFlipboxSide(app.edit, app.edit.side);
                let duration = app.getValue('animation', 'duration');
                app.editor.$g(app.selector).addClass('flipbox-animation-started').removeClass('backside-fliped');
                setTimeout(function(){
                    app.editor.$g(app.selector).removeClass('backside-fliped');
                }, duration * 1000);
                setSectionBackgroundOptions();
                $g('.flipbox-select-side input[type="hidden"]').val(app.edit.side);
                $g('.flipbox-select-side input[type="text"]').val(gridboxLanguage[app.edit.side.toUpperCase()]);
            }
        }
    }).on('shown', function(event){
        event.stopPropagation();
    });
    $g('.general-tabs > ul a').off('show').on('show', function(event){
        var parent = $g(this).closest('.general-tabs'),
            prev = event.relatedTarget.getBoundingClientRect(),
            next = event.target.getBoundingClientRect();
        parent.find('.tabs-underline').stop().css({
            'left' : prev.left,
            'right' : document.documentElement.clientWidth - prev.right,
        }).show().animate({
            'left' : next.left,
            'right' : document.documentElement.clientWidth - next.right,
        }, 500, function(){
            parent.find('.tabs-underline').hide();
        });
    });
}

function setDisableState(search)
{
    let parent = $g(search),
        value = Boolean(app.edit.desktop.disable * 1),
        item = app.editor.$g(app.selector);
    parent.find('label[data-option="disable"][data-group="desktop"]')[value ? 'addClass' : 'removeClass']('active');
    for (var key in app.editor.breakpoints) {
        if (!app.edit[key]) {
            app.edit[key] = {};
        }
        if (typeof(app.edit[key].disable) == 'undefined') {
            app.edit[key].disable = Number(value);
        }
        value = Boolean(app.edit[key].disable * 1);
        parent.find('label[data-option="disable"][data-group="'+key+'"]')[value ? 'addClass' : 'removeClass']('active');
    }
    if (item.closest('.ba-menu-wrapper').length > 0 && item.closest('.ba-wrapper[data-megamenu]').length == 0) {
        parent.find('label[data-option="disable"][data-group="desktop"]').addClass('active');
    }
}

function getCategoryHtml(id, title)
{
    var str = '<li class="chosen-category"><span>'+title;
    str += '</span><i class="zmdi zmdi-close" data-remove="'+id+'"></i></li>';

    return str;
}

function showSlideshowDesign(search, select)
{
    let parent = $g(select).closest('.tab-pane');
    parent.children().not('.slideshow-design-group').hide();
    parent.find('.last-element-child').removeClass('last-element-child');
    parent.find('.slideshow-typography-hover').hide();
    parent.find('.ba-style-intro-options').hide();
    parent.find('.title-html-tag').hide();
    $g(select).closest('.ba-settings-group').find('.slideshow-button-options').hide();
    $g(select).trigger('showedDesign');
    if (app.edit.type == 'login') {
        parent.find('label[data-option="text-align"]').css('display', search != 'button' ? '' : 'none');
    }
    switch (search) {
        case 'switcher':
        case 'list':
        case 'dropdown':
        case 'divider':
        case 'slider':
            parent.find('.'+search+'-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            break;
        case 'title':
            parent.find('.title-html-tag').css('display', '');
        case 'headline':
        case 'description':
        case 'intro':
        case 'info':
        case 'price':
        case 'reviews':
        case 'postFields':
        case 'field':
            if ((app.edit.type == 'recent-posts-slider' || app.edit.type == 'related-posts-slider'
                || app.edit.type == 'recently-viewed-products') &&
                (search == 'title' || search == 'info' || search == 'info' || search == 'price')) {
                parent.find('.slideshow-typography-hover').css('display', '').find('[data-subgroup]').attr('data-group', search);
            }
            if (search == 'intro') {
                parent.find('.ba-style-intro-options').css('display', '');
            }
            if (search == 'field' && (app.edit.type == 'login' || app.edit.type == 'checkout-form' || app.edit.type == 'submission-form')) {
                parent.find('.padding-settings-group').css('display', '').find('[data-subgroup]').attr('data-group', search);
                parent.find('.slideshow-background-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
                parent.find('.slideshow-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            }
            parent.find('.slideshow-margin-options').css('display', '').addClass('last-element-child')
                .find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-typography-color')[0].style.display = '';
            parent.find('.slideshow-typography-options').css('display', '')
                .find('[data-subgroup="typography"]').attr('data-group', search);
            parent.find('.slideshow-animation-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-typography-options .typography-options').addClass('ba-active-options');
            if (search == 'postFields') {
                parent.find('label[data-option="text-align"]').hide();
            } else {
                parent.find('label[data-option="text-align"]').css('display', '');
            }
            setTimeout(function(){
                parent.find('.slideshow-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'button' :
            if (app.edit.type == 'recent-posts-slider' || app.edit.type == 'related-posts-slider'
                || app.edit.type == 'recently-viewed-products') {
                parent.find('.recent-posts-button-label').val(app.edit.buttonLabel ? app.edit.buttonLabel : 'Read More');
            }
            parent.find('.slideshow-typography-color').hide();
            parent.find('.slideshow-typography-options').css('display', '')
                .find('[data-subgroup="typography"]').attr('data-group', search);
            parent.find('.slideshow-animation-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-margin-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.colors-settings-group').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-button-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-shadow-options').css('display', '').addClass('last-element-child')
                .find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-typography-options .typography-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.slideshow-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'arrows' :
            parent.find('.colors-settings-group').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-arrows-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-shadow-options').css('display', '').addClass('last-element-child')
                .find('[data-subgroup]').attr('data-group', search);
            parent.find('.colors-settings-group, .slideshow-arrows-options')
                .addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.colors-settings-group, .slideshow-arrows-options')
                    .removeClass('ba-active-options');
            }, 1);
            break;
        case 'dots' :
            parent.find('.slideshow-dots-options').css('display', '').addClass('last-element-child')
                .find('[data-subgroup]').attr('data-group', search);
            parent.find('.slideshow-dots-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.slideshow-dots-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'image' :
            parent.find('.slideshow-image-options').css('display', '').last().addClass('last-element-child');
            setTimeout(function(){
                parent.find('.slideshow-image-options').removeClass('ba-active-options');
            }, 1);
            break;
    }
    if (app.edit.type != 'slideshow') {
        parent.find('.slideshow-animation-options').hide();
    }
    if (search == 'field' && app.edit.type != 'login') {
        parent.find('.slideshow-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
        parent.find('.colors-settings-group').css('display', '').find('[data-subgroup]').attr('data-group', search);
    }
    if (app.edit.type == 'add-to-cart' && search == 'button') {
        $g(select).closest('.ba-settings-group').find('.slideshow-button-options').css('display', '')
            .find('input').val(app.edit['button-label']);
    } else if (app.edit.type != 'recent-posts-slider' && app.edit.type != 'related-posts-slider'
        && app.edit.type != 'recently-viewed-products') {
        parent.find('.last-element-child').removeClass('last-element-child');
        $g(select).closest('.ba-settings-group').find('.slideshow-button-options').hide();
    }
    if ((app.edit.type == 'checkout-form' || app.edit.type == 'submission-form') && search != 'field') {
        parent.find('.slideshow-margin-options').addClass('last-element-child');
    } else if (app.edit.type == 'checkout-form' || app.edit.type == 'submission-form') {
        parent.find('.slideshow-border-options').addClass('last-element-child');
    }
    parent.find('.last-element-child').nextAll().not('.slideshow-design-group').each(function(){
        if (this.style.display != 'none') {
            parent.find('.last-element-child').removeClass('last-element-child');
            return false;
        }
    });
    let object = app.getValue(search),
        match, v;
    for (var ind in object) {
        if (typeof object[ind] == 'object') {
            if (ind == 'typography') {
                app.setTypography(parent.find('.slideshow-typography-options .typography-options'), search, ind);
            } else if (ind == 'colors') {
                app.editor.app.cssRules.prepareColors(object);
                app.setDefaultState(parent.find('.colors-settings-group'), 'default');
                app.setColorsValues(parent.find('.colors-settings-group'));
            } else if (ind == 'shadow') {
                app.setDefaultState(parent.find('.shadow-settings-group'), 'default');
                app.setShadowValues(parent.find('.shadow-settings-group'));
            } else if (ind == 'background' && parent.find('.background-settings-group.slideshow-background-options').length == 1) {
                app.setDefaultState(parent.find('.background-settings-group.slideshow-background-options'), 'default');
                app.setFeatureBackgroundValues(parent.find('.background-settings-group.slideshow-background-options'));
            } else if (ind == 'margin') {
                app.setDefaultState(parent.find('.margin-settings-group'), 'default');
                app.setMarginValues(parent.find('.margin-settings-group'));
            } else if (ind == 'padding') {
                app.setDefaultState(parent.find('.padding-settings-group'), 'default');
                app.setPaddingValues(parent.find('.padding-settings-group'));
            } else if (ind == 'border') {
                app.setDefaultState(parent.find('.border-settings-group'), 'default');
                app.setBorderValues(parent.find('.border-settings-group'));
            } else {
                if (ind == 'animation' && !(delay in object.animation)) {
                    object.animation.delay = 0;
                }
                for (var key in object[ind]) {
                    var input = parent.find('[data-group="'+search+'"][data-option="'+key+'"][data-subgroup="'+ind+'"]');
                    if (input.attr('data-type') == 'color') {
                        updateInput(input, object[ind][key]);
                    } else if (input.attr('type') == 'number' || input.attr('type') == 'number') {
                        app.setLinearInput(input, object[ind][key]);
                    } else if (input.attr('type') == 'checkbox') {
                        input.prop('checked', object[ind][key]);
                    } else {
                        input.val(object[ind][key]);
                        if (input.attr('type') == 'hidden') {
                            var text = input.closest('.ba-custom-select')
                                .find('li[data-value="'+object[ind][key]+'"]').text();
                            input.closest('.ba-custom-select').find('input[readonly]').val(text.trim());
                        }
                    }
                }
            }
        } else {
            var input = parent.find('[data-group="'+search+'"][data-option="'+ind+'"]');
            if (input.attr('data-type') == 'color') {
                updateInput(input, object[ind]);
            } else {
                app.setLinearInput(input, object[ind]);
            }
        }
    }
}

app.setLinearInput = (input, text) => {
    let match = String(text).match(/\-{0,1}\d+\.{0,1}\d{0,99}/),
        v = match ? match[0] : 0,
        range = input.closest('.ba-settings-item, .ba-group-element').find('.ba-range').val(v);
    input.val(text);
    setLinearWidth(range);
}

function showBaStyleDesign(search, $this)
{
    var parent = $g($this).closest('.tab-pane'),
        object = app.getValue(search);
    parent.find('> .ba-settings-group:not(.blog-posts-background-options):not(.blog-posts-shadow-options)').hide();
    parent.find('> .ba-settings-group:first-child').css('display', '');
    parent.find('.last-element-child').removeClass('last-element-child');
    parent.find('.ba-style-typography-color')[0].style.display = '';
    parent.find('.ba-style-typography-hover-color').hide();
    parent.find('.title-html-tag, .ba-style-intro-options').hide();
    $g($this).closest('.ba-settings-group').find('.ba-style-button-options').hide();
    if (search == 'title') {
        parent.find('.title-html-tag').css('display', '');
    }
    switch (search) {
        case 'image' :
            parent.find('.ba-style-'+search+'-options').css('display', '');
            parent.find('.ba-style-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            break;
        case 'stars':
            parent.find('.ba-style-margin-options').show().find('[data-subgroup]').attr('data-group', search);
        case 'icon':
            parent.find('.ba-style-'+search+'-options').css('display', '').find('[data-group]').attr('data-group', search);
            break;
        case 'pagination' :
            if (app.edit.type != 'recent-posts' && app.edit.type != 'blog-posts' && app.edit.type != 'search-result'
                && app.edit.type != 'store-search-result') {
                parent.find('.ba-style-'+search+'-options').css('display', '');
            } else {
                parent.find('.ba-style-typography-color').hide();
                parent.find('.ba-style-typography-options').show().find('[data-subgroup="typography"]')
                    .attr('data-group', search);
                parent.find('.ba-style-margin-options').show().find('[data-subgroup]').attr('data-group', search);
                parent.find('.ba-style-button-options').show().find('[data-subgroup]').attr('data-group', search);
                parent.find('.ba-style-border-options').show().find('[data-subgroup]').attr('data-group', search);
                parent.find('.slideshow-design-group .ba-style-button-options').hide();
                parent.find('.ba-style-typography-options .typography-options').addClass('ba-active-options');
                setTimeout(function(){
                    parent.find('.ba-style-typography-options .typography-options').removeClass('ba-active-options');
                }, 1);
            }
            break;
        case 'button' :
            parent.find('.recent-posts-button-label').val(app.edit.buttonLabel ? app.edit.buttonLabel : 'Read More');
            parent.find('.ba-style-typography-color').hide();
            parent.find('.ba-style-typography-options').show().find('[data-subgroup="typography"]')
                .attr('data-group', search);
            parent.find('.ba-style-margin-options').show().find('[data-subgroup]').attr('data-group', search);
            parent.find('.ba-style-'+search+'-options').show().find('[data-subgroup]').attr('data-group', search);
            parent.find('.ba-style-border-options').show().find('[data-subgroup]').attr('data-group', search);
            parent.find('.ba-style-typography-options .typography-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.ba-style-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'intro' :
            if (app.edit.type != 'author') {
                parent.find('.ba-style-'+search+'-options').css('display', '');
            }
        default:
            parent.find('.ba-style-typography-options').show().find('[data-subgroup="typography"]').attr('data-group', search);
            parent.find('.ba-style-margin-options').show().find('[data-subgroup]').attr('data-group', search);
            parent.find('.ba-style-typography-options .typography-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.ba-style-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
    }
    if (search == 'postFields') {
        parent.find('label[data-option="text-align"]').hide();
    } else {
        parent.find('label[data-option="text-align"]').css('display', '');
    }
    if (app.edit.layout && app.edit.layout.layout == 'ba-cover-layout') {
        $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
        $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
        let groups = [].slice.call(parent[0].querySelectorAll('.ba-settings-group'));
        groups.reverse();
        for (let i = 0; i < groups.length; i++) {
            if (groups[i].style.display != 'none') {
                groups[i].classList.add('last-element-child');
                break;
            }
        }
    } else {
        $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
        $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
        $g('.blog-posts-cover-options').hide();
    }
    if (app.edit.layout && app.edit.layout.layout == 'ba-masonry-layout' && search == 'image') {
        $g('#recent-posts-design-options').find('.ba-style-image-options').first().hide();
        $g('#blog-posts-design-options').find('.ba-style-image-options').first().hide();
    } else if (app.edit.layout && app.edit.layout.layout != 'ba-masonry-layout' && search == 'image') {
        $g('#recent-posts-design-options').find('.ba-style-image-options').first().css('display', '');
        $g('#blog-posts-design-options').find('.ba-style-image-options').first().css('display', '');
    }
    if (object.typography) {
        app.setTypography(parent.find('.ba-style-typography-options .typography-options'), search, 'typography');
        parent.find('.ba-style-typography-hover-color').hide();
    }
    if (search == 'title' || search == 'info' || search == 'description' || search == 'reviews') {
        parent.find('.ba-style-typography-hover-color input[data-option="color"]').attr('data-group', search);
        parent.find('.ba-style-typography-hover-color').css('display', '');
    }
    if (search == 'pagination' && app.edit.type == 'recent-posts') {
        parent.find('.ba-style-pagination-options').hide();
    }
    for (var ind in object) {
        if (typeof(object[ind]) == 'object') {
            if (ind == 'shadow') {
                app.setDefaultState(parent.find('.shadow-settings-group'), 'default');
                app.setShadowValues(parent.find('.shadow-settings-group'));
            } else if (ind == 'colors') {
                app.editor.app.cssRules.prepareColors(object);
                app.setDefaultState(parent.find('.colors-settings-group'), 'default');
                app.setColorsValues(parent.find('.colors-settings-group'));
            } else if (ind == 'margin') {
                app.setDefaultState(parent.find('.margin-settings-group'), 'default');
                app.setMarginValues(parent.find('.margin-settings-group'));
            } else if (ind == 'padding') {
                app.setDefaultState(parent.find('.padding-settings-group'), 'default');
                app.setPaddingValues(parent.find('.padding-settings-group'));
            } else if (ind == 'border') {
                app.setDefaultState(parent.find('.border-settings-group'), 'default');
                app.setBorderValues(parent.find('.border-settings-group'));
            } else if (ind != 'typography') {
                for (var key in object[ind]) {
                    var input = parent.find('[data-group="'+search+'"][data-option="'+key+'"][data-subgroup="'+ind+'"]');
                    if (input.attr('data-type') == 'color') {
                        updateInput(input, object[ind][key]);
                    } else if (input.attr('type') == 'number' || input.attr('type') == 'text') {
                        app.setLinearInput(input, object[ind][key]);
                    } else if (input[0] && input[0].localName == 'label') {
                        input.each(function(){
                            this.classList[this.dataset.object == object[ind][key] ? 'add' : 'remove']('active');
                        });
                    } else {
                        input.val(object[ind][key]);
                        if (input.attr('type') == 'hidden') {
                            var text = input.closest('.ba-custom-select')
                                .find('li[data-value="'+object[ind][key]+'"]').text();
                            input.closest('.ba-custom-select').find('input[readonly]').val(text.trim());
                        }
                    }
                }
            }
        } else {
            var input = parent.find('[data-group="'+search+'"][data-option="'+ind+'"]');
            if (input.attr('data-type') == 'color') {
                updateInput(input, object[ind]);
            } else if (input.attr('type') == 'number' || input.attr('type') == 'text') {
                app.setLinearInput(input, object[ind]);
            } else if (input.attr('type') == 'hidden') {
                input.val(object[ind]);
                var name = input.closest('.ba-custom-select').find('li[data-value="'+object[ind]+'"]').text();
                input.closest('.ba-custom-select').find('input[readonly]').val(name.trim());
            } else if (input[0] && input[0].localName == 'label') {
                input.each(function(){
                    this.classList[this.dataset.value == object[ind] ? 'add' : 'remove']('active');
                });
            }
        }
    }
}

function createVideo(selector)
{
    var obj = {
        callback : 'createVideo',
        selector : selector,
        data : app.edit.desktop.video
    }
    if (app.edit.type == 'flipbox') {
        obj.selector = '#'+app.editor.$g(selector+' .ba-flipbox-'+app.edit.side+' > .ba-grid-column-wrapper > .ba-grid-column')
            .attr('id');
        obj.data = app.edit.sides[app.edit.side].desktop.video;
    }
    app.editor.app.listenMessage(obj);
}

function setShapeDividers(obj, id)
{
    app.editor.$g('#'+id+' > .ba-shape-divider').remove();
    var divider = document.createElement('div'),
        dividerBottom = document.createElement('div'),
        topKeys = [],
        bottomKeys = [];
    if (obj.desktop.shape.bottom.effect) {
        bottomKeys.push(obj.desktop.shape.bottom.effect);
    }
    if (obj.desktop.shape.top.effect) {
        topKeys.push(obj.desktop.shape.top.effect);
    }
    for (var key in app.editor.breakpoints) {
        if (obj[key] && obj[key].shape) {
            if (obj[key].shape.bottom && obj[key].shape.bottom.effect && bottomKeys.indexOf(obj[key].shape.bottom.effect) == -1) {
                bottomKeys.push(obj[key].shape.bottom.effect)
            }
            if (obj[key].shape.top && obj[key].shape.top.effect && topKeys.indexOf(obj[key].shape.top.effect) == -1) {
                topKeys.push(obj[key].shape.top.effect)
            }
        }
    }
    if (bottomKeys.length > 0) {
        var str = '';
        for (var i = 0; i < bottomKeys.length; i++) {
            str += shapeDividers[bottomKeys[i]] ? shapeDividers[bottomKeys[i]] : '';
        }
        dividerBottom.className = 'ba-shape-divider ba-shape-divider-bottom';
        dividerBottom.innerHTML = str;
        app.editor.$g('#'+id+' > .ba-overlay').after(dividerBottom);
    }
    if (topKeys.length > 0) {
        var str = '';
        for (var i = 0; i < topKeys.length; i++) {
            str += shapeDividers[topKeys[i]] ? shapeDividers[topKeys[i]] : '';
        }
        divider.className = 'ba-shape-divider ba-shape-divider-top';
        divider.innerHTML = str;
        app.editor.$g('#'+id+' > .ba-overlay').after(divider);
    }
}

function rangeAction(range, callback)
{
    var $this = $g(range),
        max = $this.attr('max') * 1,
        min = $this.attr('min') * 1,
        number = $this.closest('.ba-settings-item, .ba-group-element').find('input[type="number"], input[type="text"]');
    number.on('input', function(){
        let text = this.value,
            match = text.match(/\-{0,1}\d+\.{0,1}\d{0,99}/),
            match2 = text.match(/\s{0,1}[a-zA-Z%]+/),
            v = (match ? match[0] : 0) * 1,
            t = match2 ? match2[0] : '';
        if (max && v > max) {
            v = max;
            this.value = v+t;
        }
        if (min && v < min) {
            v = min;
        }
        $this.val(v);
        setLinearWidth($this);
        callback(number);
    });
    $this.on('input', function(){
        let text = number.val(),
            match = text.match(/[a-zA-Z%]+/),
            v = this.value+(match ? match[0] : '');
        number.val(v).trigger('input');
    });
}

function setLinearWidth(range)
{
    if (range.length == 0) {
        return;
    }
    var max = range.attr('max') * 1,
        min = range.attr('min') * 1,
        value = range.val() * 1,
        sx = ((Math.abs(value) * 100) / max) * range.width() / 100,
        linear = range.prev();
    if (min > 0) {
        max -= min;
        value -= min;
        sx = ((Math.abs(value) * 100) / max) * range.width() / 100;
    }
    linear[value < 0 ? 'addClass' : 'removeClass']('ba-mirror-liner');
    if (linear.hasClass('letter-spacing')) {
        sx = sx / 2;
    }
    linear.width(sx);
}

function videoDelay(selector)
{
    clearTimeout(delay);
    if (app.edit.desktop.video.id || app.edit.desktop.video.source) {
        delay = setTimeout(function(){
            createVideo(selector);
            app.addHistory();
        }, 300);
    }
}

app.setValue = function(value, group, option, subgroup, state){
    if (typeof(app.edit.desktop[group]) == 'undefined' && group != 'span') {
        if (state) {
            app.edit[group][subgroup][state][option] = value;
        } else if (subgroup) {
            app.edit[group][subgroup][option] = value;
        } else {
            if (option) {
                app.edit[group][option] = value;
            } else {
                app.edit[group] = value;
            }
        }
    } else {
        if (!app.edit[app.view][group]) {
            app.edit[app.view][group] = {};
        }
        if (state) {
            if (!app.edit[app.view][group][subgroup]) {
                app.edit[app.view][group][subgroup] = {};
            }
            if (!app.edit[app.view][group][subgroup][state]) {
                app.edit[app.view][group][subgroup][state] = {};
            }
            app.edit[app.view][group][subgroup][state][option] = value;
        } else if (subgroup) {
            if (!app.edit[app.view][group][subgroup]) {
                app.edit[app.view][group][subgroup] = {};
            }
            app.edit[app.view][group][subgroup][option] = value;
        } else {
            if (option) {
                app.edit[app.view][group][option] = value;
            } else {
                app.edit[app.view][group] = value;
            }
        }
    }
}

function getSlideHtml(obj)
{
    var li = document.createElement('li'),
        caption = document.createElement('div'),
        inner = document.createElement('div'),
        str = '<div class="slideshow-title-wrapper',
        img = document.createElement('div');
    li.className = 'item';
    img.className = 'ba-slideshow-img';
    if (app.edit.type == 'slideshow') {
        if (obj.video) {
            img.dataset.video = true;
        }
        var video = document.createElement('div');
        video.id = new Date().getTime() + Math.floor(Math.random() * 100);
        img.appendChild(video);
        li.appendChild(img);
        li.appendChild(caption);
        var animation = {
            title : app.edit.desktop.title.animation.effect,
            description : app.edit.desktop.description.animation.effect,
            button : app.edit.desktop.button.animation.effect,
        }
    } else {
        inner.className = 'slideset-inner';
        li.appendChild(inner);
        inner.appendChild(img);
        inner.appendChild(caption);
        var animation = {
            title : '',
            description : '',
            button : ''
        }
    }
    caption.className = 'ba-slideshow-caption';
    if (!obj.caption.title) {
        str += ' empty-content';
    }
    str += '"><'+app.edit.tag+' class="ba-slideshow-title '+animation.title+'">';
    str += obj.caption.title+'</'+app.edit.tag+'></div><div class="slideshow-description-wrapper';
    if (!obj.caption.description) {
        str += ' empty-content';
    }
    str += '"><div class="ba-slideshow-description '+animation.description;
    str += '">'+obj.caption.description+'</div></div><div class="slideshow-button';
    if (!obj.button.title) {
        str += ' empty-content';
    }
    str += '"><a class="'+obj.button.type+" ";
    str += animation.button+'" href="'+obj.button.href;
    str += '" target="'+obj.button.target+'"';
    if (obj.button.download) {
        str += ' download';
    }
    str += '>'+obj.button.title+'</a></div>';
    caption.innerHTML = str;

    return li
}

function getTextTypographyValue(group, option, type)
{
    var obj = app.editor.$g(app.selector).closest('footer.footer').length > 0 ? app.editor.app.footer : app.editor.app.theme,
        object = $g.extend(true, {}, obj.desktop);
    if (app.view != 'desktop') {
        for (var ind in app.editor.breakpoints) {
            if (!obj[ind]) {
                obj[ind] = {};
            }
            object = $g.extend(true, {}, object, obj[ind]);
            if (ind == app.view) {
                break;
            }
        }
    }
    if (type) {
        return object[group][type][option];
    } else if (option) {
        return object[group][option];
    } else {
        return object[group];
    }
}

app.getValue = function(group, option, subgroup, state){
    let result = false;
    if (typeof(app.edit.desktop[group]) == 'undefined' && group != 'span' && app.edit[group]) {
        if (state) {
            result = app.edit[group][subgroup][state][option];
        } else if (subgroup) {
            result = app.edit[group][subgroup][option];
        } else if (option) {
            result = app.edit[group][option];
        } else {
            result = app.edit[group];
        }
    } else {
        let object = $g.extend(true, {}, app.edit.desktop);
        if (app.view != 'desktop') {
            for (let ind in app.editor.breakpoints) {
                if (!app.edit[ind]) {
                    app.edit[ind] = {};
                }
                object = $g.extend(true, {}, object, app.edit[ind]);
                if (ind == app.view) {
                    break;
                }
            }
        }
        if (!(group in object)) {
            result = false;
        } else if (state) {
            result = object[group][subgroup][state][option];
        } else if (subgroup) {
            result = object[group][subgroup][option];
        } else if (option) {
            result = object[group][option];
        } else {
            result = object[group];
        }
    }

    return result;
}

function inputCallback(input)
{
    var val = input.val(),
        moduleName = input.attr('data-module'),
        option = input.attr('data-option'),
        callback = input.attr('data-callback'),
        subgroup = input.attr('data-subgroup'),
        state = input.attr('data-state'),
        group = input.attr('data-group');
    if (callback == 'emptyCallback') {
        return false;
    } else if (callback == 'onScrollAnimationCallback') {
        app.onScrollAnimationCallback(input[0]);
        return false;
    }
    if (!group) {
        group = option;
        option = '';
    }
    if (group || option || subgroup) {
        app.setValue(val, group, option, subgroup, state);
        clearTimeout(delay)
        delay = setTimeout(function(){
            app[callback](input);
            if (app.edit.type == 'slideset' || app.edit.type == 'carousel' || app.edit.type == 'recent-posts-slider'
                || app.edit.type == 'related-posts-slider' || app.edit.type == 'recently-viewed-products'
                || app.edit.type == 'testimonials-slider') {
                var object = {
                    data : app.edit,
                    selector : app.editor.app.edit
                };
                app.editor.app.checkModule('initItems', object);
            } else if (app.edit.type == 'progress-pie') {
                app.drawPieLine();
            }
            app.addHistory();
            if (moduleName) {
                app.editor.app.checkModule(moduleName);
            }
        }, 300);
    } else {
        app[callback](input);
    }
    input.closest('.ba-settings-item').find('.reset-text-typography-wrapper').css('display', '');
}

function linkAction(query, $this)
{
    var img = app.editor.document.getElementById(app.editor.app.edit).querySelector(query);
    if (img.parentNode.localName == 'a') {
        if ($this.value.trim()) {
            img.parentNode.href = $this.value;
        } else {
            var a = img.parentNode;
            a.parentNode.insertBefore(img, a);
            a.parentNode.removeChild(a);
        }
    } else {
        if ($this.value.trim()) {
            var a = document.createElement('a');
            a.href = $this.value;
            a.target = app.edit.link.target;
            if (app.edit.link.type) {
                a.setAttribute('download', '');
            } else {
                a.removeAttribute('download');
            }
            img.parentNode.insertBefore(a, img);
            a.appendChild(img);
            a.addEventListener('click', function(event){
                event.preventDefault();
            });
            var html = a.innerHTML,
                str = '<a class="ba-btn-transition" target="'+app.edit.link.target+'"';
            str += ' href="'+$this.value+'"';
            if (app.edit.link.type) {
                str += ' download';
            }
            str += ' '+app.edit.embed;
            str += '></a>';
            var div = document.createElement('div');
            div.innerHTML = str;
            if (div.querySelector('a')) {
                div.querySelector('a').innerHTML = html;
                $g(a).replaceWith(div.querySelector('a'));
            }
        }
    }
    app.editor.app.buttonsPrevent();
    app.edit.link.link = $this.value;
}

function setMinicolorsColor(value, update)
{
    let rgba = app.editor.app.theme.colorVariables[value] ? app.editor.app.theme.colorVariables[value].color : value,
        color = rgba2hex(rgba),
        obj = {
            color : color[0],
            opacity : color[1],
            update: update ? update : false
        }
    $g('.variables-color-picker').minicolors('value', obj).closest('#color-picker-cell').find('.minicolors-opacity').val(color[1]);
    $g('#color-variables-dialog .active').removeClass('active');
    $g('#color-picker-cell, #color-variables-dialog .nav-tabs li:first-child').addClass('active');
}

function inputColor()
{
    var value = this.value.trim().toLowerCase();
    if (value.indexOf('@') === 0) {
        $g(this).closest('.ba-settings-item').find('.minicolors-opacity').val('').attr('readonly', true);
        if (app.editor.app.theme.colorVariables[value]) {
            this.dataset.rgba = value;
            var color = app.editor.app.theme.colorVariables[value].color
            $g(this).next().find('.minicolors-swatch-color').css('background-color', color);
            setMinicolorsColor(value);
            $g(this).trigger('minicolorsInput');
        }
    } else {
        var parts = value.match(/[^#]\w/g),
            opacity = 1;
        if (parts && parts.length == 3) {
            var rgba = 'rgba(';
            for (var i = 0; i < 3; i++) {
                rgba += parseInt(parts[i], 16);
                rgba += ', ';
            }
            if (!this.dataset.rgba || this.dataset.rgba.indexOf('@') === 0) {
                rgba += '1)';
            } else {
                parts = this.dataset.rgba.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
                if (!parts) {
                    rgba += '1)';
                } else {
                    opacity = parts[4];
                    rgba += parts[4]+')';
                }
            }
            this.dataset.rgba = rgba;
            $g(this).next().find('.minicolors-swatch-color').css('background-color', rgba);
            $g(this).trigger('minicolorsInput');
            setMinicolorsColor(rgba);
        }
        $g(this).closest('.ba-settings-item').find('.minicolors-opacity').val(opacity).removeAttr('readonly');
    }
}

$g('input[data-type="color"]').each(function(){
    var div = document.createElement('div'),
        callback = $g(this).parent().find('.minicolors-opacity').attr('data-callback');
    div.className = 'minicolors minicolors-theme-bootstrap';
    this.dataset.callback = callback;
    this.classList.add('minicolors-input');
    $g(this).wrap(div);
    $g(this).after('<span class="minicolors-swatch"><span class="minicolors-swatch-color"></span></span>');
});

$g('.ba-range-wrapper input[type="range"]').each(function(){
    rangeAction(this, inputCallback);
});

$g('.input-size-wrapper-template').each(function(){
    let content = this.content,
        clone = triggers = wrapper = null;
    document.querySelectorAll('.ba-settings-toolbar input[type="text"], .ba-range-wrapper input[type="text"]').forEach((input) => {
        clone = content.cloneNode(true);
        wrapper = clone.querySelector('.input-size-wrapper');
        triggers = clone.querySelector('.input-size-triggers');
        input.parentNode.insertBefore(clone, input);
        wrapper.insertBefore(input, triggers);
    });
})
$g('.input-size-wrapper').on('keyup', 'input[type="text"]', function(event){
    if (event.originalEvent.keyCode == 38 || event.originalEvent.keyCode == 40) {
        app.inputSizer.update(this, event.originalEvent.keyCode == 38 ? 1 : -1);
    }
}).on('click', '.input-size-triggers i', function(){
    let input = this.closest('.input-size-wrapper').querySelector('input[type="text"]')
    app.inputSizer.update(input, this.dataset.action == '+' ? 1 : -1);
});

app.inputSizer = {
    update: (input, delta) => {
        let match = String(input.value).match(/\-{0,1}\d+\.{0,1}\d{0,99}/),
            unit = String(input.value).replace(/\s+/g, '').match(/[a-zA-Z%]+/),
            text = Number(((match ? match[0] * 1 : 0) + delta).toFixed(2))+(unit ? unit[0] : '');
        $g(input).val(text).trigger('input');
    }
}

$g('.ba-settings-toolbar').find('input[type="range"], input[type="text"]').on('input', function(){
    inputCallback($g(this));
});

$g('[data-type="upload-image"]').on('mousedown', function(){
    fontBtn = this;
    var modal = $g('#uploader-modal').attr('data-check', 'single');
    uploadMode = 'image';
    checkIframe(modal, 'uploader');
});

$g('.border-style-select').on('customAction', function(){
    var $this = this.querySelector('input[type="hidden"]'),
        subgroup = $this.dataset.subgroup,
        group = $this.dataset.group,
        state = $this.dataset.state,
        option = $this.dataset.option;
    app.setValue($this.value, group, option, subgroup, state);
    app.sectionRules();
    app.addHistory();
});

$g('.ba-modal-cp .image-options .ba-custom-select').on('customAction', function(){
    var option = $g(this).find('input[type="hidden"]')[0];
    app.setValue(option.value, 'background', option.dataset.option, 'image');
    app[option.dataset.action]();
    app.addHistory();
});

$g('.video-select').on('customAction', function(){
    var type = this.querySelector('input[type="hidden"]').value,
        value = this.querySelector('li[data-value="'+type+'"]').textContent.trim(),
        parent = $g(this).closest('.video-options');
    parent.addClass('ba-active-options');
    parent.find('.video-source-select, .youtube-quality').hide();
    parent.find('.video-id').css('display', '');
    if (type == 'youtube') {
        parent.find('.youtube-quality').show();
    } else if (type == 'source') {
        parent.find('.video-id').hide();
        parent.find('.video-source-select').css('display', '');
    }
    this.querySelector('input[readonly]').value = value;
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
});

$g('.video-source-select input').on('click', function(){
    fontBtn = this;
    uploadMode = 'pluginVideoSource';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
}).on('change', function(){
    if (this.dataset.option && this.dataset.callback != 'emptyCallback') {
        var option = this.dataset.option,
            type = $g(this).closest('.video-options').find('[data-option="video-type"]').val();
        app.edit.desktop.video.type = type;
        app.edit.desktop.video[option] = this.value;
        app.edit.desktop.video['id'] = '';
        $g(this).closest('.ba-settings-group').find('[data-option="id"]').val('');
        videoDelay(app.selector);
    } else {
        $g('#apply-new-slide').removeClass('disable-button').addClass('active-button');
    }
});

$g('.video-options [data-option="id"], .video-options [data-option="start"]').on('input', function(){
    if (this.dataset.callback != 'emptyCallback') {
        var option = this.dataset.option,
            type = $g(this).closest('.video-options').find('[data-option="video-type"]').val();
        app.edit.desktop.video.type = type;
        app.edit.desktop.video[option] = this.value;
        if (option == 'id') {
            app.edit.desktop.type = 'video';
            app.edit.desktop.video['source'] = '';
            $g(this).closest('.ba-settings-group').find('[data-option="source"]').val('');
        }
        videoDelay(app.selector);
    }
});

$g('.video-options [data-option="mute"]').on('change', function(){
    if (this.dataset.callback != 'emptyCallback') {
        var type = $g(this).closest('.video-options').find('[data-option="video-type"]').val();
        app.edit.desktop.video.type = type;
        if (app.edit.desktop.video.mute) {
            app.edit.desktop.video.mute = 0;
        } else {
            app.edit.desktop.video.mute = 1;
        }
        videoDelay(app.selector);
    }
});

$g('.video-quality').on('customAction', function(){
    if ($g(this).find('input[type="hidden"]').attr('data-callback') != emptyCallback) {
        var quality = $g(this).find('input[type="hidden"]').val(),
            type = $g(this).closest('.video-options').find('[data-option="video-type"]').val();
        app.edit.desktop.video.type = type;
        app.edit.desktop.video.quality = quality;
        videoDelay(app.selector);
    }
});

function backgroundSelectAction($this, callback)
{
    let input = $this.find('input[type="hidden"]')[0],
        group = input.dataset.group,
        option = input.dataset.option,
        subgroup = input.dataset.subgroup,
        target = input.value,
        parent = $g('.'+target+'-options');
    app.setValue(target, 'background', 'type');
    if (input.dataset.option) {
        app.setValue(target, group, option, subgroup);
    }
    app[callback]();
    $this.closest('.ba-settings-group').find('.background-options').find('> div').hide();
    parent.css('display', '').addClass('ba-active-options');
    if (typeof(app.editor.videoResize) != 'undefined') {
        app.editor.videoResize();
    }
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
}

$g('.background-select').on('customAction', function(){
    let $this = $g(this);
    app.setValue('', 'video', 'id');
    app.setValue('', 'video', 'source');
    app.setValue('', 'background', 'image', 'image');
    app.setValue('', 'image', 'image');
    if (app.edit.parallax) {
        app.edit.parallax.enable = false;
        $g('[data-group="parallax"][data-option="enable"]').prop('checked', app.edit.parallax.enable);
        app.editor.app.loadParallax();
        $g('.parallax-options').css('display', 'none');
    }
    backgroundSelectAction($this, this.dataset.callback);
    $this.closest('.ba-settings-group').find('[data-type="upload-image"], [data-option="id"], [data-option="source"]').val('');
    app.addHistory();
});

$g('.backround-size').on('customAction', function(){
    var size = $g(this).find('input[type="hidden"]').val(),
        parent = $g(this).closest('.image-options').find('.contain-size-options');
    if (size == 'contain' || size == 'initial') {
        parent.show().addClass('ba-active-options');
    } else {
        parent.hide();
    }
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
});

$g('[data-type="reset"]').not('.reset-text-typography').on('mousedown', function(){
    let option = subgroup = group = state = null,
        action = this.dataset.action;
    this.closest('.ba-settings-toolbar').querySelectorAll('input[type="number"], input[type="text"]').forEach(function(input){
        option = input.dataset.option;
        subgroup = input.dataset.subgroup;
        group = input.dataset.group;
        state = input.dataset.state;
        input.value = '0';
        app.setValue('0', group, option, subgroup, state);
    });
    app[action]();
    app.addHistory();
});

$g('.reset-text-typography').on('mousedown', function(){
    var input = $g(this).closest('.ba-settings-item').find('input[type="number"], input[type="text"]'),
        group = input.attr('data-group'),
        option = input.attr('data-option'),
        val = getTextTypographyValue(group, option);
    app.setLinearInput(input, val);
    delete(app.edit[app.view][group][option]);
    app.sectionRules();
    app.addHistory();
    $g(this).closest('div').hide();
});

$g('label[data-option="disable"]').on('change', function(){
    var val = this.classList.contains('active') ? 0 : 1;
    if ((app.edit.type == 'lightbox' || app.edit.type == 'cookies') && this.dataset.group == app.view) {
        var item = app.editor.document.querySelector('.ba-lightbox-backdrop[data-id="'+app.editor.app.edit+'"]');
        if (val == 1 && $g('.show-hidden-elements')[0].style.display != 'none') {
            item.classList.remove('visible-lightbox');
            app.editor.document.body.classList.remove('lightbox-open');
            app.editor.document.body.classList.remove('ba-lightbox-open');
        } else {
            item.classList.add('visible-lightbox');
            app.editor.document.body.classList.remove('ba-lightbox-open');
            if (app.edit.position == 'lightbox-center') {
                app.editor.document.body.classList.add('lightbox-open');
            }
        }
    }
    if (app.edit.type == 'overlay-section' && this.dataset.group == app.view) {
        var item = app.editor.document.querySelector('.ba-overlay-section-backdrop[data-id="'+app.editor.app.edit+'"]');
        if (val == 1 && $g('.show-hidden-elements')[0].style.display != 'none') {
            item.classList.remove('visible-section');
            app.editor.document.body.classList.remove('lightbox-open');
        } else {
            item.classList.add('visible-section');
            app.editor.document.body.classList.add('lightbox-open');
        }
    }
    if (app.edit.type == 'column') {
        var item = app.editor.document.getElementById(app.editor.app.edit).parentNode,
            className = '';
        switch (this.dataset.group) {
            case 'laptop':
                className = 'lp';
                break;
            case 'tablet':
                className = 'md';
                break;
            case 'tablet-portrait':
                className = 'md-pt';
                break;
            case 'phone' : 
                className = 'sm';
                break;
            case 'phone-portrait' : 
                className = 'sm-pt';
                break;
            default : 
                className = 'lg';
        }
        item.classList[val == 0 ? 'remove' : 'add']('ba-hidden-'+className);
        if (this.dataset.group == 'desktop' && item.parentNode.lastElementChild == item && item.previousElementSibling) {
            item.previousElementSibling.classList[val == 0 ? 'remove' : 'add']('ba-hidden-node');
        }
    }
    this.classList[val == 1 ? 'add' : 'remove']('active');
    app.edit[this.dataset.group].disable = val;
    app.sectionRules();
    app.addHistory();
    if (app.pageStructure && app.pageStructure.visible) {
        app.pageStructure.updateStructure(true);
    }
});

$g('.section-access-select').on('customAction', function(){
    var val = $g(this).find('input[type="hidden"]').val();
    app.edit.access = val;
    app.addHistory();
});

$g('.class-suffix').on('input', function(){
    var id = app.editor.app.edit,
        val = this.value;
    editClass(val, id, app.edit);
});

$g('.typography-select').on('customAction', function(){
    var target = $g(this).find('input[type="hidden"]').val(),
        parent = $g(this).closest('.ba-settings-group').find('.typography-options');
    parent.find('> div').hide();
    if (target == 'links') {
        parent.find('.links').removeAttr('style');
    } else {
        parent.find('> div').not('.links').removeAttr('style')
    }
    app.setTypography(parent, target);
    parent.addClass('ba-active-options');
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
});

$g('label[data-option]').on('click', function(){
    var val = this.dataset.value,
        option = this.dataset.option,
        state = this.dataset.state,
        group = this.dataset.group,
        subgroup = this.dataset.subgroup,
        inPosition = app.edit.positioning && app.edit.positioning.position,
        callback = this.dataset.callback;
    if (inPosition && (((app.edit.type == 'button' || app.edit.type == 'icon' || app.edit.type == 'scroll-to'
            || app.edit.type == 'tags' || app.edit.type == 'post-tags' || app.edit.type == 'counter'
            || app.edit.type == 'breadcrumbs' || app.edit.type == 'cart' || app.edit.type == 'wishlist')
            && option == 'text-align')
        || (app.edit.type == 'overlay-button' && (option == 'align' || (option == 'text-align' && group == 'typography')))
        || (app.edit.type == 'image' && option == 'align')
        )) {
        return;
    }
    switch (option) {
        case 'disable':
            $g(this).trigger('change');
            break;
        case 'horizontal_align':
        case 'content_align':
            let values = {'desktop': '', 'laptop': '-lp', 'tablet': '-md', 'tablet-portrait': '-md-pt', 'phone': '-sm', 'phone-portrait': '-sm-pt'},
                label = $g(this).closest('.ba-settings-toolbar').find('label[data-option="'+option+'"].active'),
                column = app.editor.$g(app.selector);
            val = this.classList.contains('active') ? 'disabled-column-align' : val+values[app.view];
            if (app.edit.type == 'flipbox') {
                column = column.find('.ba-grid-column');
            }
            label.removeClass('active');
            if (app.edit[app.view][option] != this.dataset.value) {
                this.classList.add('active');
            }
            if (app.edit[app.view][option]) {
                column.removeClass(app.edit[app.view][option]);
            }
            app.edit[app.view][option] = val;
            if (val != 'disabled-column-align') {
                column.addClass(app.edit[app.view][option]);
            }
            app.addHistory();
            break;
        case 'text-align':
        case 'open-align':
        case 'close-align':
        case 'align':
        case 'horizontal':
        case 'vertical':
            if (!this.classList.contains('active')) {
                let label = $g(this).closest('.ba-settings-toolbar').find('label[data-option="'+option+'"].active');
                if (!group) {
                    group = option;
                    option = '';
                }
                app.setValue(val, group, option, subgroup);
                if (option == 'horizontal' || option == 'vertical') {
                    app.positioning.setDirection(option, val);
                }
                label.removeClass('active');
                app[callback]();
                this.classList.add('active');
                app.addHistory();
            }
            break;
        case 'top':
        case 'bottom':
        case 'right':
        case 'left':
            val = this.classList.contains('active') ? 0 : 1;
            app.setValue(val, group, option, subgroup, state);
            this.classList[val == 1 ? 'add' : 'remove']('active');
            app[callback]();
            break;
        case 'font-style':
            val = this.classList.contains('active') ? 'normal' : val;
            this.classList[this.classList.contains('active') ? 'remove' : 'add']('active');
            app.setValue(val, group, option, subgroup);
            app[callback]();
            app.addHistory();
            break;
        default:
            val = this.classList.contains('active') ? 'none' : val;
            this.classList[this.classList.contains('active') ? 'remove' : 'add']('active')
            app.setValue(val, group, option, subgroup);
            app[callback]();
            app.addHistory();
    }
});

$g('#color-variables-dialog .minicolors-opacity').on('input', function(){
    var obj = {
        color: $g('.variables-color-picker').val(),
        opacity: this.value * 1,
        update: false
    }
    $g('.variables-color-picker').minicolors('value', obj);
    fontBtn.dataset.rgba = $g('.variables-color-picker').minicolors('rgbaString');
    $g(fontBtn).trigger('minicolorsInput');
    if (fontBtn.localName == 'input') {
        $g(fontBtn).next().find('.minicolors-swatch-color').css('background-color', fontBtn.dataset.rgba)
            .closest('.minicolors').next().find('.minicolors-opacity').val(this.value);
    }
});

$g('.minicolors-opacity[data-callback]').on('input', function(){
    var input = $g(this).parent().prev().find('.minicolors-input')[0],
        opacity = this.value * 1
        value = input.dataset.rgba;
    if (value.indexOf('@') === -1 && this.value) {
        var parts = value.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
            rgba = 'rgba(';
        if (parts) {
            for (var i = 1; i < 4; i++) {
                rgba += parts[i]+', ';
            }
        } else {
            parts = value.match(/[^#]\w/g);
            for (var i = 0; i < 3; i++) {
                rgba += parseInt(parts[i], 16);
                rgba += ', ';
            }
        }
        rgba += this.value+')';
        input.dataset.rgba = rgba;
        $g(input).next().find('.minicolors-swatch-color').css('background-color', rgba);
        $g(input).trigger('minicolorsInput');
    }
});

$g('input[data-type="color"]').on('click', function(){
    let modal = $g('#color-variables-dialog');
    fontBtn = this;
    setMinicolorsColor(this.dataset.rgba);
    openPickerModal(modal, this);
    modal.removeClass('ba-right-position ba-top-position').find('.nav-tabs li:last').css('display', '');
    if (this.closest('#yandex-maps-item-dialog')) {
        modal.find('.nav-tabs li:last').hide();
    }
}).on('minicolorsInput', function(){
    let $this = this,
        rgba = this.dataset.rgba,
        option = this.dataset.option,
        subgroup = this.dataset.subgroup,
        state = this.dataset.state,
        group = this.dataset.group;
    if (this.dataset.callback =='emptyCallback') {
        return false;
    }
    if (!group) {
        group = option;
        option = '';
    }
    app.setValue(rgba, group, option, subgroup, state);
    clearTimeout(this.minicolorsDelay);
    this.minicolorsDelay = setTimeout(function(){
        app[$this.dataset.callback]();
        if (app.edit.type == 'progress-pie' && (option == 'bar' || option == 'background')) {
            app.drawPieLine();
        }
    }, 300);
}).on('input', inputColor).next().on('click', function(){
    $g(this).prev().trigger('click');
});

$g('.show-text-editor-general').on('click', function(event){
    event.preventDefault();
    var parent = $g(this).closest('.general-tabs'),
        show = $g.Event('show', {
        relatedTarget : parent.find('li.active a')[0],
        target : this
    });
    parent.find('.hide-general-cell').removeClass('hide-general-cell').addClass('show-general-cell');
    parent.find('li.active').removeClass('active');
    $g(this).trigger(show).trigger('shown').parent().addClass('active');
});

$g('.hide-text-editor-general').on('click', function(event){
    event.preventDefault();
    var parent = $g(this).closest('.general-tabs'),
        show = $g.Event('show', {
        relatedTarget : parent.find('li.active a')[0],
        target : this
    });
    parent.find('.show-general-cell').removeClass('show-general-cell').addClass('hide-general-cell');
    if (!this.dataset.toggle) {
        parent.find('li.active').removeClass('active');
        $g(this).trigger(show).parent().addClass('active');
    }
});

$g('.link-target-select').on('customAction', function(){
    app.edit.link.target = $g(this).find('input[type="hidden"]').val();
    var a = app.editor.document.getElementById(app.editor.app.edit).querySelector('a');
    if (app.edit.type == 'column') {
        a = app.editor.document.querySelector(app.selector+' > a');
    }
    if (a) {
        a.target = app.edit.link.target;
    }
    app.addHistory();
});

$g('.media-fullscrean').on('click', function(){
    var modal = $g(this).closest('.modal');
    if (!modal.hasClass('fullscrean')) {
        modal.addClass('fullscrean');
        $g(this).removeClass('zmdi-fullscreen').addClass('zmdi-fullscreen-exit');
    } else {
        modal.removeClass('fullscrean');
        $g(this).addClass('zmdi-fullscreen').removeClass('zmdi-fullscreen-exit');
    }        
});

$g('.reset:not(.disabled-reset) i').on('click', function(){
    var option = this.dataset.option,
        callback = this.dataset.callback,
        group  = this.dataset.group;
    $g('input[data-option="'+option+'"][data-group="'+group+'"]').val('').attr('data-value', '').trigger('change');
    app.setValue('', group, option);
    if (callback) {
        window[callback]();
    }
    app.addHistory();
});

$g('[data-option="link"][data-group="link"]').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        switch (app.edit.type) {
            case 'icon' :
                linkAction('.ba-icon-wrapper i', $this);
                break
            case 'image' :
                linkAction('img', $this);
                break;
            case 'column':
                var a = app.editor.document.querySelector(app.selector+' > a'),
                    value = $this.value.trim();
                if (a) {
                    if (value) {
                        a.href = value;
                    } else {
                        a.remove();
                    }
                } else {
                    a = document.createElement('a');
                    a.href = value;
                    a.target = app.edit.link.target;
                    if (app.edit.link.type) {
                        a.setAttribute('download', '');
                    } else {
                        a.removeAttribute('download');
                    }
                    app.editor.$g(app.selector).prepend(a);
                    let str = '<a class="ba-btn-transition" target="'+app.edit.link.target+'"',
                        div = document.createElement('div');
                    str += ' href="'+value+'"';
                    if (app.edit.link.type) {
                        str += ' download';
                    }
                    str += ' '+app.edit.embed;
                    str += '></a>';
                    div.innerHTML = str;
                    if (div.querySelector('a')) {
                        $g(a).replaceWith(div.querySelector('a'));
                    }
                    app.editor.app.buttonsPrevent();
                }
                app.edit.link.link = $this.value;
                break;
            default :
                var a = app.editor.document.getElementById(app.editor.app.edit).querySelector('a');
                a.href = $this.value;
                app.edit.link.link = $this.value;
        }
        app.addHistory();
    }, 300);
});

$g('[data-option="type"][data-group="link"]').on('change', function(){
    var a = app.editor.document.querySelector(app.selector+' a');
    app.edit.link.type = this.value;
    if (app.edit.type == 'column') {
        a = app.editor.document.querySelector(app.selector+' > a');
    }
    if (a) {
        if (app.edit.link.type) {
            a.setAttribute('download', '');
        } else {
            a.removeAttribute('download');
        }
    }
});

$g('input[data-option="alt"]').on('input', function(){
    clearTimeout(delay);
    var $this = this,
        img = app.editor.document.getElementById(app.editor.app.edit);
    img = img.querySelector('img');
    delay = setTimeout(function(){
        app.edit.alt = $this.value;
        img.alt = $this.value;
        app.addHistory();
    }, 300);
});

$g('input[data-option="image"].reselect-image').on('mousedown', function(){
    fontBtn = this;
    uploadMode = 'reselectImage';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('.border-settings-group .ba-settings-item input[type="checkbox"]').on('change', function(){
    let val = Number(this.checked),
        option = this.dataset.option,
        subgroup = this.dataset.subgroup,
        state = this.dataset.state,
        group = this.dataset.group;
    app.setValue(val, group, option, subgroup, state);
    app.sectionRules();
    app.addHistory();
});

$g('.set-desktop-view-value').on('change', function(){
    app.edit.desktop[this.dataset.group][this.dataset.option] = this.checked;
    app.sectionRules();
    app.addHistory();
    if (app.edit.type == 'slideset' || app.edit.type == 'carousel' || app.edit.type == 'recent-posts-slider'
        || app.edit.type == 'related-posts-slider' || app.edit.type == 'recently-viewed-products'
        || app.edit.type == 'testimonials-slider') {
        var object = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initItems', object);
    }
});

$g('.set-value-css').on('change input', function(){
    var $this = this,
        time = 300;
    if (this.type == 'checkbox') {
        time = 0;
    }
    clearTimeout(delay);
    delay = setTimeout(function(){
        var option = $this.dataset.option,
            value = $this.value,
            subgroup = $this.dataset.subgroup,
            group = $this.dataset.group;
        if (!group) {
            group = option;
            option = '';
        }
        if ($this.type == 'checkbox') {
            value = $this.checked;
        }
        app.setValue(value, group, option, subgroup);
        app.sectionRules();
        app.addHistory();
        if (app.edit.type == 'slideset' || app.edit.type == 'carousel' || app.edit.type == 'recent-posts-slider'
            || app.edit.type == 'related-posts-slider' || app.edit.type == 'recently-viewed-products'
            || app.edit.type == 'testimonials-slider') {
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            };
            app.editor.app.checkModule('initItems', object);
        } else if (app.edit.type == 'slideshow' && group == 'thumbnails' && option == 'count') {
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            };
            app.editor.app.checkModule('initslideshow', object);
        }
    }, time);
});

$g('.ba-style-custom-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    showBaStyleDesign(value, this);
});

$g('.blog-posts-layout-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    app.editor.$g(app.selector+' > div[class*="-wrapper"]').not('.ba-blog-posts-pagination-wrapper')
        .removeClass(app.edit.layout.layout);
    app.edit.layout.layout = value;
    app.editor.$g(app.selector+' > div[class*="-wrapper"]').not('.ba-blog-posts-pagination-wrapper')
        .addClass(app.edit.layout.layout);
    $g('.blog-posts-cover-options').hide();
    $g('.blog-posts-background-options').css('display', '');
    $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
    $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().css('display', '');
    if (app.edit.layout.layout == 'ba-classic-layout' || app.edit.layout.layout == 'ba-one-column-grid-layout') {
        $g('.blog-posts-grid-options').hide();
        $g('.blog-posts-grid-options input[data-option="count"]').closest('.ba-settings-group');
        if (app.edit.type != 'blog-posts') {
            $g('#recent-posts-design-options .ba-style-image-options').first().css('display', '');
        }
    } else {
        $g('.blog-posts-grid-options').css('display', '');
        $g('.blog-posts-grid-options input[data-option="count"]').closest('.ba-settings-group');
        if (app.edit.layout.layout == 'ba-cover-layout') {
            $g('.ba-style-image-options .blog-posts-grid-options').hide();
            $g('.blog-posts-cover-options').css('display', '');
            $g('.blog-posts-background-options').hide();
            $g('#recent-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
            $g('#blog-posts-design-options .ba-style-image-options').first().find('.ba-settings-item').first().hide();
        }
    }
    if (!app.edit.preset && !app.editor.app.theme.defaultPresets[app.edit.type]) {
        var type = app.edit.type,
            patern = $g.extend(true, {}, presetsPatern[type]),
            is_object = null,
            object = defaultElementsStyle[type];
        for (var ind in patern) {
            if (ind == 'desktop') {
                for (var key in patern[ind]) {
                    if (key == 'view') {
                        continue;
                    }
                    is_object = typeof(app.edit[ind][key]) == 'object';
                    app.edit[ind][key] = is_object ? $g.extend(true, {}, object[ind][key]) : object[ind][key];
                }
                for (var ind in app.editor.breakpoints) {
                    if (app.edit[ind]) {
                        for (var key in patern.desktop) {
                            is_object = typeof(app.edit[ind][key]) == 'object';
                            if (is_object && object[ind] && object[ind][key]) {
                                app.edit[ind][key] = $g.extend(true, {}, object[ind][key]);
                            } else if (!is_object && object[ind] && object[ind][key]) {
                                app.edit[ind][key] = object[ind][key];
                            } else if (is_object) {
                                app.edit[ind][key] = {};
                            } else {
                                delete(app.edit[ind][key]);
                            }
                        }
                    }
                }
            } else {
                is_object = typeof(app.edit[ind]) == 'object';
                app.edit[ind] = is_object ? $g.extend(true, {}, object[ind]) : object[ind];
            }
        }
        switch (app.edit.type) {
            case 'author':
                if (app.edit.layout.layout == 'ba-grid-layout') {
                    app.edit.desktop.view.count = 1;
                    app.edit.desktop.title.typography['text-align'] = 'center';
                    app.edit.desktop.intro.typography['text-align'] = 'center';
                }
                break;
            case 'blog-posts':
            case 'search-result':
            case 'store-search-result':
                if (app.edit.layout.layout == 'ba-cover-layout') {
                    app.edit.desktop.image.height = 400;
                    if (app.edit.type == 'blog-posts') {
                        app.edit.desktop.border.radius = 9;
                    } else {
                        app.edit.desktop.image.border.radius = 9;
                    }
                    app.edit.desktop.background.color = '@bg-primary';
                    app.edit.desktop.title.typography.color = '@title-inverse';
                    app.edit.desktop.info.typography.color = '@title-inverse';
                    app.edit.desktop.intro.typography.color = '@title-inverse';
                    app.edit.desktop.overlay.color = '@overlay';
                    app.edit.desktop.title.typography['font-size'] = 24;
                    app.edit.desktop.title.typography['line-height'] = 34;
                    app.edit.desktop.title.margin.top = 200;
                    app.edit.desktop.view.count = 2;
                    if (app.edit.type == 'search-result' || app.edit.type == 'store-search-result') {
                        app.edit.desktop.view.count = 3;
                    }
                    app.edit.desktop.postFields.typography.color = '@title-inverse';
                    app.edit.desktop.reviews.typography.color = '@title-inverse';
                } else if (app.edit.layout.layout == 'ba-grid-layout' || app.edit.layout.layout == 'ba-masonry-layout') {
                    app.edit.desktop.view.count = 2;
                    if (app.edit.type == 'search-result' || app.edit.type == 'store-search-result') {
                        app.edit.desktop.view.count = 3;
                    }
                    app.edit.desktop.image.width = 1170;
                    app.edit.desktop.image.height = 250;
                    app.edit.desktop.title.typography['font-size'] = 20;
                    app.edit.desktop.title.typography['line-height'] = 30;
                } else if (app.edit.layout.layout == 'ba-classic-layout') {
                    app.edit.desktop.image.width = 300;
                    app.edit.desktop.image.height = 300;
                    app.edit.desktop.background.color = 'rgba(255, 255, 255, 0)';
                    app.edit.desktop.title.typography['font-size'] = 20;
                    app.edit.desktop.title.typography['line-height'] = 30;
                } else if (app.edit.layout.layout == 'ba-one-column-grid-layout') {
                    app.edit.desktop.title.typography['font-size'] = 32;
                    app.edit.desktop.title.typography['line-height'] = 42;
                    app.edit.desktop.image.width = 1170;
                    app.edit.desktop.image.height = 400;
                    app.edit.desktop.view.count = 1;
                }
                break;
            case 'recent-posts':
            case 'related-posts':
                if (app.edit.layout.layout == 'ba-classic-layout') {
                    app.edit.desktop.image.width = 100;
                    app.edit.desktop.image.height = 100;
                    app.edit.desktop.image.border.radius = 100;
                    app.edit.desktop.background.color = 'rgba(255, 255, 255, 0)';
                    app.edit.desktop.title.typography.color = '@title';
                    app.edit.desktop.info.typography.color = '@subtitle';
                    app.edit.desktop.intro.typography.color = '@text';
                    app.edit.desktop.overlay.color = 'rgba(0, 0, 0, 0.3)';
                } else if (app.edit.layout.layout == 'ba-grid-layout' || app.edit.layout.layout == 'ba-masonry-layout') {
                    app.edit.desktop.image.width = 1170;
                    app.edit.desktop.image.height = 250;
                    app.edit.desktop.image.border.radius = 0;
                    app.edit.desktop.background.color = '@bg-secondary';
                    app.edit.desktop.title.typography.color = '@title';
                    app.edit.desktop.info.typography.color = '@subtitle';
                    app.edit.desktop.intro.typography.color = '@text';
                    app.edit.desktop.overlay.color = 'rgba(0, 0, 0, 0.3)';
                } else if (app.edit.layout.layout == 'ba-cover-layout') {
                    app.edit.desktop.image.height = 400;
                    app.edit.desktop.image.border.radius = 9;
                    app.edit.desktop.border.radius = 9;
                    app.edit.desktop.background.color = '@bg-primary';
                    app.edit.desktop.title.typography.color = '@title-inverse';
                    app.edit.desktop.info.typography.color = '@title-inverse';
                    app.edit.desktop.intro.typography.color = '@title-inverse';
                    app.edit.desktop.overlay.color = '@overlay';
                    app.edit.desktop.title.typography['font-size'] = 24;
                    app.edit.desktop.title.typography['line-height'] = 34;
                    app.edit.desktop.title.margin.top = 200;
                    if (app.edit.desktop.postFields) {
                        app.edit.desktop.postFields.typography.color = '@title-inverse';
                        app.edit.desktop.reviews.typography.color = '@title-inverse';
                    }
                }
                break;
            case 'categories':
                if (app.edit.layout.layout == 'ba-classic-layout') {
                    app.edit.desktop.image.width = 200;
                    app.edit.desktop.image.height = 200;
                    app.edit.desktop.image.border.radius = 6;
                    app.edit.desktop.title.typography.color = '@title';
                    app.edit.desktop.info.typography.color = '@subtitle';
                    app.edit.desktop.intro.typography.color = '@text';
                    app.edit.desktop.overlay.color = 'rgba(0, 0, 0, 0.3)';
                } else if (app.edit.layout.layout == 'ba-grid-layout' || app.edit.layout.layout == 'ba-masonry-layout') {
                    app.edit.desktop.image.width = 1170;
                    app.edit.desktop.image.height = 250;
                    app.edit.desktop.image.border.radius = 0;
                    app.edit.desktop.title.margin.top = 25;
                    app.edit.desktop.title.margin.bottom = 25;
                    app.edit.desktop.info.margin.top = 0;
                    app.edit.desktop.title.typography.color = '@title';
                    app.edit.desktop.info.typography.color = '@subtitle';
                    app.edit.desktop.intro.typography.color = '@text';
                    app.edit.desktop.overlay.color = 'rgba(0, 0, 0, 0.3)';
                } else if (app.edit.layout.layout == 'ba-cover-layout') {
                    app.edit.desktop.image.height = 400;
                    app.edit.desktop.image.border.radius = 9;
                    app.edit.desktop.border.radius = 9;
                    app.edit.desktop.title.typography.color = '@title-inverse';
                    app.edit.desktop.info.typography.color = '@title-inverse';
                    app.edit.desktop.intro.typography.color = '@title-inverse';
                    app.edit.desktop.overlay.color = '@overlay';
                    app.edit.desktop.title.typography['font-size'] = 24;
                    app.edit.desktop.title.typography['line-height'] = 34;
                }
                var view = {
                    "count": app.edit.layout.layout == 'ba-classic-layout' ? 1 : 3
                }
                app.edit.desktop.view = $g.extend(true, app.edit.desktop.view, view);
                break;
        }
        app.editor.app.checkModule('editItem');
        app.editor.app.setNewFont = true;
        app.editor.app.fonts = {};
        app.editor.app.customFonts = {};
    }
    app.sectionRules();
    if (app.edit.type != 'author') {
        app.editor.app.initMasonryBlog(app.edit, app.editor.app.edit);
    }
    app.addHistory();
});

$g('.selected-categories .search-category input').on('click', function(event){
    event.stopPropagation();
    $g('.all-categories-list').addClass('vasible-all-categories-list').find('li[data-app="'+app.edit.app+'"]:not(.selected-category)').css('display', '');
    $g('body').one('click', function(){
        $g('.all-categories-list').removeClass('vasible-all-categories-list').find('li').hide();
    });
});

$g('.all-categories-list li').on('click', function(){
    if (!this.classList.contains('selected-category')) {
        this.classList.add('selected-category');
        var obj = {
                title : this.textContent.trim(),
                id : this.dataset.id
            },
            str = getCategoryHtml(obj.id, obj.title);
        app.edit.categories[obj.id] = obj;
        $g(this).closest('.tags-categories').find('.selected-categories li.search-category').before(str);
        $g('.ba-settings-item.tags-categories-list').addClass('not-empty-list');
        window[app.recentPostsCallback]();
    }
});

$g('.selected-categories').on('click', 'li.chosen-category .zmdi-close', function(){
    $g('.all-categories-list li[data-id="'+this.dataset.remove+'"]').removeClass('selected-category');
    delete(app.edit.categories[this.dataset.remove]);
    $g(this).closest('li').remove();
    if ($g('.selected-categories li:not(.search-category)').length > 0) {
        $g('.ba-settings-item.tags-categories-list').addClass('not-empty-list');
    } else {
        $g('.ba-settings-item.tags-categories-list').removeClass('not-empty-list');
    }
    window[app.recentPostsCallback]();
});

$g('.recent-posts-app-select').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]'),
        modal = $g(this).closest('.ba-modal-cp');
    if (input.value != app.edit[input.dataset.option]) {
        app.edit[input.dataset.option] = input.value;
        app.edit.categories = {};
        $g('.selected-categories li:not(.search-category)').remove();
        $g('.all-categories-list .selected-category').removeClass('selected-category');
        $g('.ba-settings-item.tags-categories-list').removeClass('not-empty-list');
        modal.find('.search-category')[0].dataset.enabled = Number(modal.find('.all-categories-list li[data-app="'+app.edit.app+'"]').length > 0);
        modal.find('.trigger-post-tags-modal')[0].dataset.enabled = Number(app.edit.app != 0);
        window[app.recentPostsCallback]();
    }
});

$g('.recent-posts-display-select, .related-posts-display-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]');
    if (input.value != app.edit[input.dataset.option]) {
        app.edit[input.dataset.option] = input.value;
        window[app.recentPostsCallback]();
    }
});

$g('.set-featured-posts').on('change', function(){
    app.edit.featured = this.checked;
    window[app.recentPostsCallback]();
});

$g('#recent-posts-settings-dialog, #slideshow-settings-dialog, #recent-comments-settings-dialog, #categories-settings-dialog')
    .find('input.recent-limit, input[data-option="maximum"]').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit[$this.dataset.option] = $this.value;
        if (app.recentPostsCallback) {
            window[app.recentPostsCallback]();
        }
    });
});

$g('.select-anchor').on('click', function(){
    fontBtn = $g(this).parent().find('input[type="text"]');
    let modal = $g('#text-anchor-picker-dialog'),
        wrapper = modal.find('.text-anchors-wrapper').empty();
    app.editor.$g('.ba-item-text > .content-text a[name]').each(function(){
        let p = document.createElement('p');
        p.dataset.value = '#'+this.name;
        p.textContent = this.name;
        wrapper.append(p);
    });
    if (app.fieldsCKE) {
        for (let ind in app.fieldsCKE) {
            let div = document.createElement('div');
            div.innerHTML = app.fieldsCKE[ind].getData();
            $g(div).find('a[name]').each(function(){
                let p = document.createElement('p');
                p.dataset.value = '#'+this.name;
                p.textContent = this.name;
                wrapper.append(p);
            });
        }
    }
    openPickerModal(modal, this);
});

$g('#text-anchor-picker-dialog').on('click', 'p', function(){
    fontBtn.val(this.dataset.value).trigger('input');
    $g('#text-anchor-picker-dialog').modal('hide');
});

$g('.select-link').on('click', function(){
    fontBtn = $g(this).parent().find('input[type="text"]');
    app.checkModule('selectLink');
});

$g('.select-file').on('click', function(){
    fontBtn = $g(this).parent().find('input[type="text"]');
    uploadMode = 'selectFile';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('.reset-element-icon i').on('click', function(){
    var parent = $g(this).closest('.modal');
    $g(parent).find('.select-item-icon').attr('data-value', '').val('').trigger('input');
});

$g('.select-item-icon').on('click', function(){
    uploadMode = 'selectItemIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('.background-overlay-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]'),
        subgroup = input.dataset.subgroup,
        group = input.dataset.group,
        state = input.dataset.state,
        option = input.dataset.option;
        parent = $g('.overlay-'+input.value+'-options');
    $g('.overlay-color-options, .overlay-gradient-options, .overlay-blur-options').hide();
    parent.css('display', '').addClass('ba-active-options');
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
    app.setValue(input.value, input.dataset.property, 'type');
    if (option) {
        app.setValue(input.value, group, option, subgroup, state);
    }
    app[input.dataset.callback ? input.dataset.callback : 'sectionRules']();
    app.addHistory();
});

$g('.gradient-effect-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]'),
        subgroup = input.dataset.subgroup,
        group = input.dataset.group,
        state = input.dataset.state,
        option = input.dataset.option;
        parent = $g(this).closest('.tab-pane');
    parent.find('.'+input.dataset.property+'-linear-gradient').hide();
    parent.find('.'+input.dataset.property+'-'+input.value+'-gradient').css('display', '');
    if (!input.dataset.option) {
        app.setValue(input.value, input.dataset.property, 'effect', 'gradient');
    } else {
        app.setValue(input.value, group, option, subgroup, state);
    }
    app[input.dataset.callback ? input.dataset.callback : 'sectionRules']();
    app.addHistory();
});

$g('.slideshow-style-custom-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val();
    showSlideshowDesign(value, this);
});

function addFontLink(obj, key)
{
    var object = app.editor.$g(app.selector).closest('footer.footer').length > 0 ? app.editor.app.footer : app.editor.app.theme,
        font = obj.font == '@default' ? getTextParentFamily(object.desktop, key) : obj.font,
        styles = obj.styles == '@default' ? getTextParentWeight(object.desktop, key) : obj.styles,
        link = '//fonts.googleapis.com/css?family='+font+':'+styles,
        file = app.editor.document.createElement('link');
    link += '&subset=latin,cyrillic,greek,latin-ext,greek-ext,vietnamese,cyrillic-ext';
    file.rel = 'stylesheet';
    file.type = 'text/css';
    file.href = link;
    app.editor.document.head.appendChild(file);
}

function addFontStyle(obj, key)
{
    var object = app.editor.$g(app.selector).closest('footer.footer').length > 0 ? app.editor.app.footer : app.editor.app.theme,
        style = document.createElement('style'),
        font = obj.font == '@default' ? getTextParentFamily(object.desktop, key) : obj.font,
        styles = obj.styles == '@default' ? getTextParentWeight(object.desktop, key) : obj.styles,
        str = "@font-face {font-family: '"+font.replace(/\+/g, ' ')+"';";
    str += 'font-weight : '+styles+';';
    str += ' src: url('+JUri+'templates/gridbox/library/fonts/'+obj.custom_src+');}';
    style.type = 'text/css';
    app.editor.document.head.appendChild(style);
    style.innerHTML = str;
}

function addFontFamily(obj, $this)
{
    var callback = $this.dataset.callback,
        key = (app.edit.type == 'text' || app.edit.type == 'headline') ? $this.dataset.group : 'body',
        subgroup = $this.dataset.subgroup,
        group = $this.dataset.group;
    if (!obj.custom_src) {
        addFontLink(obj, key);
    } else if (obj.custom_src != 'web-safe-fonts') {
        addFontStyle(obj, key);
    }
    if (!subgroup) {
        app.edit.desktop[group]['font-family'] = obj.font;
        app.edit.desktop[group]['font-weight'] = obj.styles;
        app.edit.desktop[group]['custom'] = obj.custom_src;
    } else {
        app.edit.desktop[group][subgroup]['font-family'] = obj.font;
        app.edit.desktop[group][subgroup]['font-weight'] = obj.styles;
        app.edit.desktop[group][subgroup]['custom'] = obj.custom_src;
    }
    setTimeout(function(){
        app[callback]();
        if (callback != 'sectionRules') {
            app.sectionRules();
        }
    }, 300);
    app.addHistory();
}

function getTextParentFamily(obj, key)
{
    var family = obj[key]['font-family'];
    if (family == '@default') {
        family = obj.body['font-family'];
    }

    return family;
}

function getTextParentWeight(obj, key)
{
    var weight = obj[key]['font-weight'];
    if (weight == '@default') {
        weight = obj.body['font-weight'];
    }

    return weight;
}

function getTextParentCustom(obj, key)
{
    var object = obj[key];
    if (object['font-family'] == '@default') {
        object = obj.body;
    }

    return object['custom'];
}

function getTextParentList(obj, key)
{
    var family = getTextParentFamily(obj, key), array;
    if (fontsLibrary[family]) {
        array = fontsLibrary[family];
    } else {
        array =  [];
    }

    return array;
}

$g('input[data-option="font-family"]').on('change', function(){
    var weightWrapper = $g('.font-weight-wrapper').empty(),
        parentFont = this.dataset.value;
        key = 'body',
        array = fontsLibrary[this.dataset.value],
        input = $g(this).closest('.typography-options').find('input[data-option="font-weight"]'),
        parent = app.editor.app.theme.desktop;
    if (app.edit.type == 'text' || app.edit.type == 'headline') {
        key = this.dataset.group;
    }
    if (!app.edit.type || app.edit.type == 'footer') {
        app.editor.app.setNewFont = true;
        app.editor.app.fonts = {};
        app.editor.app.customFonts = {};
    }
    if (app.editor.$g(app.selector).closest('footer.footer').length > 0) {
        parent = app.editor.app.footer.desktop;
    }
    if (this.dataset.value == '@default') {
        parentFont = parent[key]['font-family'];
        if (parentFont == '@default') {
            parentFont = parent.body['font-family'];
        }
        array = getTextParentList(parent, key);
        var object = {
                font: this.dataset.value,
                styles: this.dataset.value,
                custom_src: getTextParentCustom(parent, key)
            };
    } else {
        var object = $g.extend({}, array[0]);
    }
    if (this.dataset.group != 'body') {
        object.styles = '@default';
    }
    if (this.dataset.group != 'body') {
        var str = '<span class="font-weight-title">'+gridboxLanguage['BASE_FONT_WEIGHT'],
            inheritFont = this.dataset.value,
            inheritWeight = parent[key]['font-weight'];
        if (inheritFont == '@default') {
            inheritFont = parent[key]['font-family'];
        }    
        if (inheritFont == '@default') {
            inheritFont = parent['body']['font-family'];
        }
        if (inheritWeight == '@default') {
            inheritWeight = parent['body']['font-weight'];
        }
        str += '</span><span class="font-family-title">'+inheritWeight.replace('i', 'Italic')+'</span>';
        var p = document.createElement('p');
        p.dataset.value = '@default';
        p.style.fontFamily = "'"+inheritFont.replace(/\+/g, ' ')+"'";
        p.style.fontWeight = inheritWeight.replace('i', '');
        p.style.fontStyle = inheritWeight.indexOf('i') == -1 ? 'normal' : 'italic';
        p.innerHTML = str;
        weightWrapper.append(p);
    }
    for (var i = 0; i < array.length; i++) {
        var weight = array[i].styles,
            str = '<span class="font-weight-title">'+weight.replace('i', ' Italic')+
            '</span><span class="font-family-title">'+parentFont.replace(/\+/g, ' ')+'</span>';
        var p = document.createElement('p');
        p.dataset.value = weight;
        p.style.fontFamily = "'"+parentFont.replace(/\+/g, ' ')+"'";
        p.style.fontWeight = weight.replace('i', '');
        p.style.fontStyle = weight.indexOf('i') == -1 ? 'normal' : 'italic';
        p.innerHTML = str;
        weightWrapper.append(p);
    }
    var value = object.styles == '@default' ? gridboxLanguage['INHERIT'] : object.styles.replace('i', ' Italic');
    input.attr('data-value', object.styles).val(value);
    addFontFamily(object, this);
});

$g('input[data-option="font-weight"]').on('change', function(){
    var font = $g(this).closest('.typography-options').find('input[data-option="font-family"]')[0].dataset.value,
        obj = {};
    if (font == '@default' || this.dataset.value == '@default') {
        var key = 'body';
        if (app.edit.type == 'text' || app.edit.type == 'headline') {
            key = this.dataset.group;
        }
        if (app.editor.$g(app.selector).closest('footer.footer').length > 0) {
            var parent = app.editor.app.footer.desktop;
        } else {
            var parent = app.editor.app.theme.desktop;
        }
        obj = {
            font: font,
            styles: this.dataset.value,
            custom_src: font == '@default' ? getTextParentCustom(parent, key) : fontsLibrary[font][0].custom_src
        }
        if (this.dataset.value != '@default' && !this.dataset.subgroup) {
            obj.custom_src = app.edit.desktop[this.dataset.group]['custom'];
        } else if (this.dataset.value != '@default') {
            obj.custom_src = app.edit.desktop[this.dataset.group][this.dataset.subgroup]['custom'];
        }
    } else {
        for (var i = 0; i < fontsLibrary[font].length; i++) {
            if (fontsLibrary[font][i].styles == this.dataset.value) {
                obj = fontsLibrary[font][i];
            }
        }
    }
    if (!app.edit.type || app.edit.type == 'footer') {
        app.editor.app.setNewFont = true;
        app.editor.app.fonts = {};
        app.editor.app.customFonts = {};
    }
    addFontFamily(obj, this);
});

$g('.create-new-preset').on('click', function(){
    $g('.preset-title').val('');
    $g('#save-preset').removeClass('active-button').addClass('disable-button').attr('data-key', '');
    $g('.save-as-default-preset').prop('checked', false);
    $g('#create-preset-dialog').modal();
    $g(this).closest('.select-preset').trigger('click');
});

$g('.edit-preset-item').on('click', function(){
    if (!this.classList.contains('disable-button')) {
        $g('.preset-title').val(app.editor.app.theme.presets[app.edit.type][this.dataset.value].title);
        $g('#save-preset').removeClass('active-button').addClass('disable-button').attr('data-key', this.dataset.value);
        $g('.save-as-default-preset').prop('checked', app.editor.app.theme.defaultPresets[app.edit.type] == this.dataset.value);
        $g('#create-preset-dialog').modal();
        $g(this).closest('.select-preset').trigger('click');
    }
});

$g('.delete-preset-item').on('click', function(){
    if (!this.classList.contains('disable-button')) {
        app.itemDelete = 'ba-delete-preset:'+this.dataset.value;
        app.checkModule('deleteItem');
        $g(this).closest('.select-preset').trigger('click');
    }
});

$g('.save-as-default-preset').on('change', function(){
    var value = $g('.preset-title').val().trim();
    if (value) {
        $g('#save-preset').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#save-preset').removeClass('active-button').addClass('disable-button');
    }
});

$g('.preset-title').on('input', function(){
    if (this.value.trim() && !app.editor.app.theme.presets[app.edit.type][this.value.trim()]) {
        $g('#save-preset').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#save-preset').removeClass('active-button').addClass('disable-button');
    }
});

$g('#save-preset').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        if (!this.dataset.key) {
            var patern = presetsPatern[app.edit.type],
                title = $g('.preset-title').val().trim(),
                value = title,
                obj = {};
            for (var ind in patern) {
                if (ind == 'desktop') {
                    obj[ind] = {};
                    for (var key in patern[ind]) {
                        obj[ind][key] = $g.extend(true, {}, app.edit[ind][key]);
                    }
                    for (var ind in app.editor.breakpoints) {
                        if (app.edit[ind]) {
                            obj[ind] = {};
                            for (var key in patern.desktop) {
                                if (obj[ind][key]) {
                                    obj[ind][key] = $g.extend(true, {}, app.edit[ind][key]);
                                } else {
                                    obj[ind][key] = {};
                                }
                            }
                        }
                    }
                } else {
                    obj[ind] = $g.extend(true, {}, app.edit[ind]);
                }
            }
            app.editor.app.theme.presets[app.edit.type][value] = {
                'title': title,
                'data' : obj
            };
            app.edit.preset = value;
            app.editor.comparePresets(app.edit);
            checkPresetProperties();
            app.addHistory();
        } else {
            var title = $g('.preset-title').val().trim(),
                value = this.dataset.key;
            app.editor.app.theme.presets[app.edit.type][value].title = title;
            if (!$g('.save-as-default-preset').prop('checked') && app.editor.app.theme.defaultPresets[[app.edit.type]] == value) {
                delete(app.editor.app.theme.defaultPresets[[app.edit.type]]);
            }
        }
        if ($g('.save-as-default-preset').prop('checked')) {
            app.editor.app.theme.defaultPresets[[app.edit.type]] = value;
        }
        app.editor.app.checkModule('editItem');
        $g('#create-preset-dialog').modal('hide');
    }
});

$g('.select-preset').on('customAction', function(){
    app.edit.preset = this.querySelector('input[type="hidden"]').value;
    app.editor.comparePresets(app.edit);
    app.editor.app.checkModule('editItem');
    app.editor.app.setNewFont = true;
    app.editor.app.fonts = {};
    app.editor.app.customFonts = {};
    app.sectionRules();
    checkPresetProperties();
    app.addHistory();
});

function checkPresetProperties()
{
    if (app.edit.preset) {
        if (app.edit.desktop.shape && 'setShapeDividers' in window) {
            var str = '.ba-'+app.edit.type.replace('column', 'grid-column');
            app.editor.$g(str).each(function(){
                if (app.editor.app.items[this.id] && app.editor.app.items[this.id].preset == app.edit.preset) {
                    setShapeDividers(app.editor.app.items[this.id], this.id);
                }
            });
        }
        if (app.edit.type == 'progress-pie') {
            app.drawPieLine();
        }
        app.editor.$g(app.selector).closest('li').trigger('mouseenter');
    }
}

function setPresetsList(modal)
{
    if (app.edit.preset && app.editor.app.theme.presets[app.edit.type]
        && app.editor.app.theme.presets[app.edit.type][app.edit.preset]) {
        modal.find('.select-preset input[type="hidden"]').val(app.edit.preset)
            .prev().val(app.editor.app.theme.presets[app.edit.type][app.edit.preset].title);
    } else {
        modal.find('.select-preset input[type="hidden"]').val('');
        modal.find('.select-preset input[type="text"]').val(gridboxLanguage['NO_NE']);
    }
    getPresetsLi(modal);
}

function getPresetsLi(modal)
{
    var str = getPresetLi('', gridboxLanguage['NO_NE']);
    if (!app.editor.app.theme.presets[app.edit.type]) {
        app.editor.app.theme.presets[app.edit.type] = {};
    }
    for (var ind in app.editor.app.theme.presets[app.edit.type]) {
        str += getPresetLi(ind, app.editor.app.theme.presets[app.edit.type][ind].title);
    }
    modal.find('.select-preset .ba-lg-custom-select-body').empty().append(str);
}

function getPresetLi(value, title)
{
    var str = '<li data-value="'+value+'"><label>';
    str += '<input type="radio" name="preset-checkbox" value="'+value+'">';
    str += '<i class="zmdi zmdi-circle-o"></i><i class="zmdi zmdi-check"></i></label><span>'+title+'</span>';
    if (app.editor.app.theme.defaultPresets[app.edit.type] == value) {
        str += '<i class="zmdi zmdi-star"></i>';
    }
    str += '</li>';

    return str;
}

app.drawPieLine = function(){
    app.editor.$g('.ba-item-progress-pie').each(function(){
        var canvas = this.querySelector('canvas'),
            context = canvas.getContext('2d'),
            obj = app.editor.app.items[this.id],
            view = app.editor.getProgressPieObject(this.id);
        canvas.width = view.width;
        canvas.height = canvas.width;
        context.lineCap = 'round';
        app.editor.drawPieLine(obj.target * 3.6, canvas, context, this);
    });
}

$g('.ba-slider-wrapper').each(function(){
    this.querySelectorAll('input[type="range"]').forEach(($this) => {
        let parent = $this.closest('.ba-settings-item');
        $this.linear = parent.querySelector('.ba-range-liner');
        $this.slides = parent.querySelectorAll('input[type="range"]');
        $this.numbers = parent.querySelectorAll('.ba-slider-text-wrapper span[data-index]');
        $this.input = parent.querySelector('input[type="hidden"]');
        if (!$this.input.values) {
            $this.input.values = [];
        }
        $this.input.values.push(this.value);
    });
}).on('input', 'input[type="range"]', function(){
    let max = this.max * 1,
        min = this.min * 1,
        ind = this.dataset.index * 1,
        index = ind == 0 ? 1 : 0,
        value1 = this.value * 1,
        callback = this.input.dataset.callback,
        value2 = this.slides[index].value * 1,
        sx = left = 0;
    if (this.slides[0].value * 1 > this.slides[1].value * 1) {
        ind = ind == 0 ? 1 : 0;
        index = index == 0 ? 1 : 0;
    }
    this.input.values[index] = value2;
    this.numbers[index].textContent = value2;
    this.numbers[ind].textContent = value1;
    this.input.values[ind] = value1;
    sx = (this.input.values[1] * 1 - this.input.values[0] * 1) * 100 / (max - min)
    left = (max - this.input.values[1] * 1) * 100 / (max - min);
    this.linear.style.width = sx+'%';
    this.linear.style.marginLeft = (100 - sx - left)+'%';
    if (callback) {
        app[callback](this.input);
    }
}).on('customChange', 'input[type="range"]', function(){
    let max = this.max * 1,
        min = this.min * 1,
        ind = this.dataset.index * 1,
        index = ind == 0 ? 1 : 0,
        value1 = this.value * 1,
        value2 = this.slides[index].value * 1,
        sx = left = 0;
    if (this.slides[0].value * 1 > this.slides[1].value * 1) {
        ind = ind == 0 ? 1 : 0;
        index = index == 0 ? 1 : 0;
    }
    this.input.values[index] = value2;
    this.numbers[index].textContent = value2;
    this.numbers[ind].textContent = value1;
    this.input.values[ind] = value1;
    sx = (this.input.values[1] * 1 - this.input.values[0] * 1) * 100 / (max - min)
    left = (max - this.input.values[1] * 1) * 100 / (max - min);
    this.linear.style.width = sx+'%';
    this.linear.style.marginLeft = (100 - sx - left)+'%';
});

$g('#image-settings-dialog, #countdown-settings-dialog').find('.sorting-container')
    .on('click', '.zmdi.zmdi-edit', function(){
    let title = '',
        description = '';
    if (!app.edit.caption) {
        app.edit.caption = {
            title: '',
            description: '',
        }
    }
    $g('.image-item-upload-image').val(app.edit.image);
    $g('.image-item-alt').val(app.edit.alt);
    $g('.image-item-title').val(app.edit.caption.title);
    $g('.image-item-description').val(app.edit.caption.description);
    $g('#image-item-edit-modal').modal();
});

$g('.image-item-upload-image').on('click', function(){
    uploadMode = 'reselectSimpleImage';
    fontBtn = this;
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('#apply-image-item').on('click', function(event){
    event.preventDefault();
    var wrapper = app.editor.$g(app.selector+' > .ba-image-wrapper').addClass(app.edit.desktop.animation.effect)[0],
        image = wrapper.querySelector('img'),
        title = wrapper.querySelector('.ba-image-item-title'),
        description = wrapper.querySelector('.ba-image-item-description');
    app.edit.image = $g('.image-item-upload-image').val();
    app.edit.alt = $g('.image-item-alt').val().trim();
    app.edit.caption.title = $g('.image-item-title').val().trim();
    app.edit.caption.description = $g('.image-item-description').val().trim();
    image.src = JUri+app.edit.image;
    image.alt = app.edit.alt;
    if (!title) {
        var caption = '<div class="ba-image-item-caption">'+
            '<div class="ba-caption-overlay"></div><'+app.edit.tag+' class="ba-image-item-title"></'+app.edit.tag+'>'+
            '<div class="ba-image-item-description"></div></div>';
        $g(wrapper).append(caption);
        title = wrapper.querySelector('.ba-image-item-title');
        description = wrapper.querySelector('.ba-image-item-description');
    }
    if (!app.edit.caption.title) {
        title.classList.add('empty-content');
    } else {
        title.classList.remove('empty-content');
    }
    if (!app.edit.caption.description) {
        description.classList.add('empty-content');
    } else {
        description.classList.remove('empty-content');
    }
    title.textContent = app.edit.caption.title;
    description.innerHTML = app.edit.caption.description;
    app.addHistory();
    $g('#image-general-options, #countdown-general-options').find('.sorting-item').each(function(){
        this.querySelector('img').src = JUri+app.edit.image;
        var array = app.edit.image.split('/');
        this.querySelector('.sorting-title').textContent = array[array.length - 1];
    });
    $g('#image-item-edit-modal').modal('hide');
});

$g('.image-item-caption-effect-select').on('customAction', function(){
    app.editor.$g('#'+app.editor.app.edit+' > .ba-image-wrapper').removeClass(app.edit.desktop.animation.effect);
    app.edit.desktop.animation.effect = this.querySelector('input[type="hidden"]').value;
    app.editor.$g('#'+app.editor.app.edit+' > .ba-image-wrapper').addClass(app.edit.desktop.animation.effect);
    app.addHistory();
});

$g('.button-embed-code').on('input', function(){
    var $this = this;
    clearTimeout(this.embedDelay);
    this.embedDelay = setTimeout(function(){
        var a = app.editor.$g(app.selector+' a');
        if (app.edit.type == 'column') {
            a = app.editor.$g(app.selector+' > a');
        }
        if (a.length > 0) {
            var html = a.html(),
                str = '<a class="ba-btn-transition"';
            if (app.edit.link) {
                ' target="'+app.edit.link.target+'"';
                if (app.edit.link.link) {
                    str += ' href="'+app.edit.link.link+'"';
                }
                if (app.edit.link.type) {
                    str += ' download';
                }
            }
            str += ' '+$this.value;
            str += '></a>';
            var div = document.createElement('div');
            div.innerHTML = str;
            if (div.querySelector('a')) {
                div.querySelector('a').innerHTML = html;
                a.replaceWith(div.querySelector('a'));
            }
        }
        app.edit.embed = $this.value;
        app.addHistory();
    }, 300);
});

$g('.select-title-html-tag').on('customAction', function(){
    var tag = this.querySelector('input[type="hidden"]').value;
    if (tag != app.edit.tag) {
        app.editor.$g(app.selector+' '+app.edit.tag+'[class*="-title"]').each(function(){
            var h = document.createElement(tag);
            h.className = this.className;
            h.innerHTML = this.innerHTML;
            $g(this).replaceWith(h);
        });
        app.edit.tag = tag;
        app.addHistory();
    }
});

function replaceBlogPostsTag()
{
    if (app.edit.tag != 'h3') {
        app.editor.$g(app.selector+' h3[class*="-title"]').each(function(){
            var h = document.createElement(app.edit.tag);
            h.className = this.className;
            h.innerHTML = this.innerHTML;
            $g(this).replaceWith(h);
        });
    }
}

function replaceSlideEmbed(a, obj)
{
    var str = '<a class="'+obj.type+'" target="'+obj.target+'" href="'+obj.href+'"';
    if (obj.download) {
        str += ' download';
    }
    str += ' '+obj.embed;
    str += '>'+obj.title+'</a>';
    var div = document.createElement('div');
    div.innerHTML = str;
    if (div.querySelector('a')) {
        a.replaceWith(div.querySelector('a'));
    }
}

$g('.reset-item-edit-image').on('click', function(){
    $g(this).parent().find('input').val('').attr('data-value', '');
});

$g('.recent-posts-button-label').on('input', function(){
    app.edit.buttonLabel = this.value;
    app.editor.$g(app.selector).find('.ba-blog-post-button-wrapper a').text(app.edit.buttonLabel);
});

$g('.set-group-display').on('change', function(){
    let action = this.checked ? 'addClass' : 'removeClass',
        $this = $g(this).closest('.ba-group-element, .ba-settings-item').nextAll('.ba-subgroup-element');
    $this[action]('visible-subgroup').removeClass('subgroup-animation-ended');
    clearTimeout(this.subDelay);
    if (this.checked) {
        this.subDelay = setTimeout(function(){
            $this.addClass('subgroup-animation-ended');
        }, 750);
    }
}).each(function(){
    if (this.checked) {
        $g(this).closest('.ba-group-element, .ba-settings-item').nextAll('.ba-subgroup-element').addClass('visible-subgroup subgroup-animation-ended');
    }
});

$g('#site-dialog, #settings-dialog').find('.ba-custom-select').each(function(){
    let value = this.querySelector('input[type="hidden"]').value,
        text = $g(this).find('li[data-value="'+value+'"]').text().trim();
    $g(this).find('input[type="text"]').val(text);
});

$g('#site-dialog, #settings-dialog').find('input[type="range"]').each(function(){
    setLinearWidth($g(this));
});

$g('.select-field-upload-source').on('customAction', function(){
    app.edit.options.source = this.querySelector('input[type="hidden"]').value;
    if (app.edit.options.source == 'desktop') {
        this.closest('.ba-settings-group').querySelector('.desktop-source-filesize').style.display = '';
    } else {
        this.closest('.ba-settings-group').querySelector('.desktop-source-filesize').style.display = 'none';
    }
});

$g('.ba-states-toggle input[data-option="state"]').on('change', function(){
    let group = this.dataset.group,
        subgroup = this.dataset.subgroup,
        state = this.dataset.state,
        isOverlay = (group == 'overlay-states' || subgroup == 'overlay-states') && this.checked,
        method = null,
        obj = state ? app.getValue(group, state, subgroup) : app.getValue(group, subgroup),
        query = this.closest('.ba-settings-group'),
        wrapper = this.closest('.ba-states-wrapper');
    wrapper.classList[this.checked ? 'add' : 'remove']('active-states');
    if (isOverlay && obj.default.type == 'gradient') {
        obj.default.type = 'none';
        let sub = group == 'overlay-states' ? '' : 'default';
        app.setValue('none', group, 'type', (subgroup ? subgroup : 'default'), sub);
        app.setValue('none', (group == 'overlay-states' ? 'overlay' : group), 'type');
        app.setOverlayValues(query);
    } else if (group == 'background-states' && this.checked) {
        obj.default.type = 'none';
        app.setValue('none', 'background', 'type');
        app.setBackgroundValues(query);
    }
    if ((group == 'colors' || subgroup == 'colors') && this.checked) {
        app.setValue('', group == 'colors' ? 'colors-bg' : group, 'type', subgroup == 'colors' ? 'colors-bg' : '');
        $g(wrapper).closest('.ba-settings-group').find('.select-colors-type').css('display', 'none').find('.colors-type-select').each(function(){
            this.querySelector('input[type="hidden"]').value = '';
            this.querySelector('input[type="text"]').value = app._('COLOR');
        }).trigger('customAction');
    } else if (group == 'colors' || subgroup == 'colors') {
        $g(wrapper).closest('.ba-settings-group').find('.select-colors-type').css('display', '');
    }
    if (isOverlay) {
        query.querySelector('.background-overlay-select li[data-value="gradient"]').style.display = 'none';
    } else if (group == 'overlay-states' || subgroup == 'overlay-states') {
        query.querySelector('.background-overlay-select li[data-value="gradient"]').style.display = '';
    } else if (group == 'background-states' && this.checked) {
        query.querySelector('.background-select').querySelectorAll('li[data-value="gradient"], li[data-value="video"]')
            .forEach(function(li){
            li.style.display = 'none';
        });
    } else if (group == 'background-states') {
        query.querySelector('.background-select').querySelectorAll('li[data-value="gradient"], li[data-value="video"]')
            .forEach(function(li){
            li.style.display = '';
        });
    }
    wrapper.querySelectorAll('.ba-states-icon').forEach(function(icon){
        method = icon.dataset.method;
        if (!obj[icon.dataset.action]) {
            app.setValue(obj.default, group, icon.dataset.action, subgroup);
        }
    });
    app.setValue(this.checked, group, 'state', subgroup, state);
    app.setDefaultState(query, 'default');
    app[method](query);
    app.sectionRules();
    app.addHistory();
});

$g('.ba-states-wrapper .ba-states-icon').on('click', function(){
    let state = this.dataset.action,
        group = this.dataset.group,
        subgroup = this.dataset.subgroup,
        obj = app.getValue(group, subgroup),
        div = this.closest('.ba-settings-group');
    if (state == 'active' && !obj.active) {
        app.setValue(obj.hover, group, state, subgroup);
    }
    app.setState(div, state);
    app[this.dataset.method](div);
});

$g('.ba-states-wrapper .ba-states-transition-action').on('click', function(){
    let modal = $g('#states-transition-modal'),
        group = this.dataset.group,
        subgroup = this.dataset.subgroup,
        obj = app.getValue(group, subgroup),
        data = value = range = null;
    data = [];
    ['x1', 'y1', 'x2', 'y2'].forEach(function(i){
        data.push(obj.transition[i]);
    });
    modal.find('li.active, .tab-pane.active').removeClass('active');
    modal.find('li').first().addClass('active');
    modal.find('.tab-pane').first().addClass('active');
    openPickerModal(modal, this);
    modal.find('input[data-group]').each(function(){
        this.dataset.group = group;
        this.dataset.subgroup = subgroup ? subgroup : 'transition';
        subgroup ? this.dataset.state = 'transition' : this.removeAttribute('data-state');
        value = obj.transition[this.dataset.option];
        app.setLinearInput($g(this), value);
    });
    value = data.join(', ');
    modal.find('.cubic-bezier-preset').removeClass('active');
    modal.find('.cubic-bezier-preset[data-values="'+value+'"]').addClass('active');
});

$g('.cubic-bezier-preset').on('click', function(){
    let modal = $g('#states-transition-modal'),
        data = this.dataset.values.split(', ');
    $g('.cubic-bezier-settings-wrapper input[data-group]').each(function(i){
        let option = this.dataset.option,
            subgroup = this.dataset.subgroup,
            state = this.dataset.state,
            group = this.dataset.group;
        app.setValue(data[i], group, option, subgroup, state);
        app.setLinearInput($g(this), data[i]);
    });
    modal.find('.cubic-bezier-preset.active').removeClass('active');
    this.classList.add('active');
    app.sectionRules();
    app.addHistory();
});

app.setDefaultState = function(query, state){
    let obj = group = subgroup = object = view = null,
        item = $g(query);
    item.find('input[data-option="state"]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        obj = app.getValue(group, subgroup);
        if (!obj.default) {
            object = {
                default: $g.extend(true, {}, (obj.normal ? obj.normal : obj)),
                state: (obj.normal ? true : false),
                transition: $g.extend(true, {}, app.setDefaultState.transition)
            }
            view = app.view;
            app.view = 'desktop';
            let sub = app.edit.desktop[group];
            if (sub && sub[subgroup]) {
                sub = sub[subgroup];
            }
            for (let a in sub) {
                delete sub[a];
            }
            app.setValue(object.default, group, 'default', subgroup);
            app.setValue(object.state, group, 'state', subgroup);
            app.setValue(object.transition, group, 'transition', subgroup);
            app.view = view;
            obj = object;
        }
        this.checked = obj.state;
        this.closest('.ba-states-wrapper').classList[this.checked ? 'add' : 'remove']('active-states');
        if ((group == 'colors' || subgroup == 'colors') && obj.state) {
            item.find('.select-colors-type').css('display', 'none');
        } else if (group == 'colors' || subgroup == 'colors') {
            item.find('.select-colors-type').css('display', '');
        }
    });
    app.setState(item, state);
}

app.setDefaultState.transition = {
    duration: 0.3,
    x1: 0.42,
    y1: 0,
    x2: 0.58,
    y2: 1
}

app.setState = function(query, state){
    $g(query).find('.ba-settings-item:not(.ba-disable-states), .ba-settings-toolbar').find('[data-group]').each(function(){
        this.dataset[this.dataset.state ? 'state' : 'subgroup'] = state;
    });
    $g(query).find('.ba-states-icon').each(function(){
        this.classList[this.dataset.action == state ? 'add' : 'remove']('active');
    });
}

app.setOverlayValues = function(query){
    let group = subgroup = state = value = element = type = effect = null,
        item = $g(query);
    item.find('.ba-settings-item input[data-option]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
        value = app.getValue(group, this.dataset.option, subgroup, state);
        type = this.dataset.option == 'type' ? value : type;
        effect = this.dataset.option == 'effect' ? value : effect;
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else if (this.type == 'hidden') {
            this.value = value;
            element = this.closest('.ba-custom-select');
            value = element.querySelector('li[data-value="'+value+'"]').textContent.trim();
            element.querySelector('input[readonly]').value = value;
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    item.find('.overlay-linear-gradient').hide();
    item.find('.overlay-'+effect+'-gradient').css('display', '');
    item.find('.overlay-gradient-options, .overlay-color-options, .overlay-blur-options').hide();
    item.find('.overlay-'+type+'-options').css('display', '');
}

app.setBackgroundValues = function(query){
    let group = subgroup = state = option = value = element = size = type = video = null,
        item = $g(query);
    item.find('.ba-settings-item input[data-option]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
        option = this.dataset.option;
        option = option == 'video-type' ? 'type' : option;
        value = app.getValue(group, option, subgroup, state);
        size = option == 'size' ? value : size;
        type = option == 'type' && group == 'background' ? value : type;
        video = option == 'type' && group == 'video' ? value : video;
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else if (this.type == 'hidden') {
            this.value = value;
            element = this.closest('.ba-custom-select');
            value = element.querySelector('li[data-value="'+value+'"]').textContent.trim();
            element.querySelector('input[readonly]').value = value;
        } else if (this.type == 'checkbox') {
            this.checked = value == 1;
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    if (size == 'contain' || size == 'initial') {
        item.find('.contain-size-options').show().addClass('ba-active-options');
        setTimeout(function(){
            item.find('.contain-size-options').removeClass('ba-active-options');
        }, 1);
    } else {
        item.find('.contain-size-options').hide();
    }
    item.find('.background-options').find('> div').hide();
    item.find('.'+type+'-options').css('display', '');
    item.find('.video-source-select, .youtube-quality').hide();
    item.find('.video-id').css('display', '');
    if (video == 'youtube') {
        item.find('.youtube-quality').css('display', '');
    } else if (video == 'source') {
        item.find('.video-id').hide();
        item.find('.video-source-select').css('display', '');
    }
}

app.setBorderValues = function(query){
    let group = subgroup = state = value = element = null;
    $g(query).find('.ba-settings-item input[data-option], .ba-settings-toolbar label[data-option]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
        value = app.getValue(group, this.dataset.option, subgroup, state);
        if (value == undefined) {
            value = 1;
        }
        if (this.localName == 'label') {
            this.classList[value == 1 ? 'add' : 'remove']('active');
        } else if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else if (this.type == 'hidden') {
            this.value = value;
            element = this.closest('.ba-custom-select');
            value = element.querySelector('li[data-value="'+value+'"]').textContent.trim();
            element.querySelector('input[readonly]').value = value;
        } else {
            app.setLinearInput($g(this), value);
        }
    });
}

app.setPaddingValues = function(query){
    app.setMarginValues(query);
}

app.setMarginValues = function(query){
    let group = subgroup = state = value = null;
    $g(query).find('input[type="text"]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
        value = app.getValue(group, this.dataset.option, subgroup, state);
        if (!value) {
            value = '0';
        }
        this.value = value;
    });
}

app.setFeatureBackgroundValues = function(query){
    let group = subgroup = state = value = input = null;
    input = $g(query).find('input[data-option="color"]').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
    });
    value = app.getValue(group, 'color', subgroup, state);
    updateInput(input, value);
}

app.setShadowValues = function(query){
    let group = subgroup = state = value = input = null,
        item = $g(query);
    item.find('.trigger-advanced-shadows').each(function(){
        group = this.dataset.group;
        subgroup = this.dataset.subgroup;
        state = this.dataset.state;
        value = app.getValue(group, 'advanced', subgroup, state);
        this.closest('.ba-settings-item').classList[value ? 'add' : 'remove']('active-advanced-shadow');
    });
    value = app.getValue(group, 'value', subgroup, state);
    app.setLinearInput(item.find('input[data-option="value"]'), value);
    value = app.getValue(group, 'color', subgroup, state);
    updateInput(item.find('input[data-option="color"]'), value);
}

app.setColorsValues = function(query){
    let value = text = null;
    $g(query).find('.ba-settings-item input[data-option]').each(function(){
        value = app.getValue(this.dataset.group, this.dataset.option, this.dataset.subgroup, this.dataset.state);
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else if (this.type == 'hidden') {
            this.value = value;
            element = this.closest('.ba-custom-select');
            text = element.querySelector('li[data-value="'+value+'"]').textContent.trim();
            element.querySelector('input[readonly]').value = text;
        } else {
            app.setLinearInput($g(this), value);
        }
        if (this.dataset.option == 'type') {
            let parent = $g(this).closest('.ba-settings-group');
            value = value == '' ? 'color' : value;
            parent.find('.colors-color-options, .colors-gradient-options').hide();
            parent.find('.colors-'+value+'-options').css('display', '');
        }
    });
}

$g('.colors-type-select').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]'),
        parent = $g(this).closest('.ba-settings-group');
    value = input.value;
    app.setValue(value, input.dataset.group, input.dataset.option, input.dataset.subgroup);
    value = value == '' ? 'color' : value;
    parent.find('.colors-color-options, .colors-gradient-options').hide();
    parent.find('.colors-'+value+'-options').css('display', '');
    app.sectionRules();
    app.addHistory();
});

app.shadowCallback = function(input){
    let option = input.attr('data-option'),
        group = input.attr('data-group'),
        subgroup = input.attr('data-subgroup'),
        state = input.attr('data-state'),
        advanced = option != 'value';
    app.setValue(advanced, group, 'advanced', subgroup, state);
    if (advanced) {
        fontBtn.closest('.ba-settings-item').classList.add('active-advanced-shadow');
    } else {
        input.closest('.ba-settings-item').removeClass('active-advanced-shadow');
    }
    app.sectionRules();
}

$g('.trigger-advanced-shadows').on('click', function(){
    let modal = $g('#advanced-shadow-modal'),
        group = this.dataset.group,
        subgroup = this.dataset.subgroup,
        state = this.dataset.state,
        obj = state ? app.getValue(group, state, subgroup) : app.getValue(group, subgroup);
    fontBtn = this;
    if (!obj.advanced) {
        app.setValue(0, group, 'horizontal', subgroup, state);
        app.setValue(obj.value * 2, group, 'vertical', subgroup, state);
        app.setValue(obj.value * 4, group, 'blur', subgroup, state);
        app.setValue(0, group, 'spread', subgroup, state);
        obj = state ? app.getValue(group, state, subgroup) : app.getValue(group, subgroup);
    }
    openPickerModal(modal, this);
    modal.find('input[data-group]').each(function(){
        this.dataset.group = group;
        this.dataset.subgroup = subgroup;
        state ? this.dataset.state = state : this.removeAttribute('data-state');
        let value = obj[this.dataset.option];
        app.setLinearInput($g(this), value);
    });
});

$g('.open-category-list-fields').on('click', function(){
    let str = '',
        $this = this,
        id = app.editor.themeData.id,
        modal = $g('#category-list-fields-modal'),
        wrapper = modal.find('.category-list-fields-view-wrapper').empty();
    if (app.edit.type == 'recent-posts' || app.edit.type == 'recent-posts-slider' || app.edit.type == 'fields-filter'
        || app.edit.type == 'google-maps-places' || app.edit.type == 'event-calendar' || app.edit.type == 'submission-form') {
        id = app.edit.app;
    }
    if (this.dataset.target == 'fields') {
        $g.ajax({
            type:"POST",
            dataType:'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.getAppFields",
            data:{
                id: id,
                type: app.edit.type,
                edit_type: app.editor.themeData.edit_type
            },
            complete: function(msg){
                let object = JSON.parse(msg.responseText);
                if (!app.edit.fields) {
                    app.edit.fields = [];
                    for (let ind in object) {
                        app.edit.fields.push(ind);
                    }
                    app.edit.desktop.fields = {};
                    for (let ind in object) {
                        app.edit.desktop.fields[ind] = false;
                    }
                } else {
                    let array = [];
                    for (let i = 0; i < app.edit.fields.length; i++) {
                        if (object[app.edit.fields[i]]) {
                            array.push(app.edit.fields[i]);
                        }
                    }
                    for (let ind in object) {
                        if (array.indexOf(ind) == -1 && app.edit.type != 'fields-filter' && !object[ind].product) {
                            array.push(ind);
                        } else if (array.indexOf(ind) == -1 && app.edit.type == 'fields-filter' && object[ind].label &&
                            (object[ind].field_type == 'checkbox' || object[ind].field_type == 'radio' || object[ind].product
                                || object[ind].field_type == 'select' || object[ind].field_type == 'price'
                                || object[ind].field_type == 'date' || object[ind].field_type == 'event-date')) {
                            array.push(ind);
                        }
                    }
                    if (app.edit.type == 'fields-filter' && array.indexOf('rating') == -1) {
                        array.push('rating');
                    }
                    app.edit.fields = array;
                    for (let ind in app.edit.desktop.fields) {
                        if (app.edit.fields.indexOf(ind) == -1) {
                            delete(app.edit.desktop.fields[ind])
                        }
                    }
                    for (let ind in object) {
                        if (!app.edit.desktop.fields[ind] && app.edit.type != 'fields-filter' && !object[ind].product) {
                            app.edit.desktop.fields[ind] = false;
                        } else if (!app.edit.desktop.fields[ind] && app.edit.type == 'fields-filter' && object[ind].label &&
                            (object[ind].field_type == 'checkbox' || object[ind].field_type == 'radio' || object[ind].product
                                || object[ind].field_type == 'select' || object[ind].field_type == 'price'
                                || object[ind].field_type == 'date' || object[ind].field_type == 'event-date')) {
                            app.edit.desktop.fields[ind] = false;
                        }
                    }
                    if (app.edit.type == 'fields-filter' && !app.edit.desktop.fields['rating']) {
                        app.edit.desktop.fields['rating'] = false;
                    }
                    for (let key in app.editor.breakpoints) {
                        if (app.edit[key] && app.edit[key].fields) {
                            for (let ind in app.edit[key].fields) {
                                if (app.edit[key].indexOf(ind) == -1) {
                                    delete(app.edit[key].fields[ind])
                                }
                            }
                        }
                    }
                }
                for (let i = 0; i < app.edit.fields.length; i++) {
                    let ind = app.edit.fields[i],
                        obj = object[ind],
                        value = app.edit.desktop.fields[ind],
                        label = ind == 'rating' ? gridboxLanguage['RATING'] : obj.label,
                        options = app.edit.type == 'fields-filter' ? null : JSON.parse(obj.options);
                    if ((app.edit.type == 'fields-filter' && !label) || (app.edit.type != 'fields-filter' && obj.product)) {
                        continue;
                    }
                    if (!label) {
                        label = options.label;
                    }
                    str += '<div class="fields-view-row'+(value ? ' active' : '')+'" data-group="fields" data-id="'+ind+'">'+
                        '<span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span><span class="fields-view-title">'+label+
                        '</span><label class="ba-checkbox"><input type="checkbox"'+(value ? ' checked' : '')+
                        (app.edit.type == 'submission-form' && obj.required == 1 && obj.id != 'image' && obj.id != 'description' ? ' disabled' : '')+
                        ' data-group="fields" data-option="'+ind+'"><span></span></label></div>';
                }
                wrapper.append(str);
                checkCategoryListFieldsActive();
                openPickerModal(modal, $this);
            }
        });
    } else if (this.dataset.target == 'store') {
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.getAppStoreFields', {
            id: id,
            type : app.edit.type,
        }).then(function(text){
            let json = JSON.parse(text);
            for (let ind in json) {
                let value = app.edit.desktop.store[ind];
                str += '<div class="fields-view-row'+(value ? ' active' : '')+'" data-group="info" data-id="'+ind+'">'+
                    '<span class="fields-view-title">'+json[ind]+
                    '</span><label class="ba-checkbox"><input type="checkbox"'+(value ? ' checked' : '')+
                    ' data-group="store" data-option="'+ind+'"><span></span></label></div>';
            }
            wrapper.append(str);
            checkCategoryListFieldsActive();
            openPickerModal(modal, $this);
        });
    } else {
        for (let i = 0; i < app.edit.info.length; i++) {
            let ind = app.edit.info[i],
                value = app.edit.desktop.view[ind],
                label = ind == 'hits' ? gridboxLanguage['VIEWS'] : gridboxLanguage[ind.toUpperCase()];
            str += '<div class="fields-view-row'+(value ? ' active' : '')+'" data-group="info" data-id="'+ind+'">'+
                '<span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span><span class="fields-view-title">'+label+
                '</span><label class="ba-checkbox"><input type="checkbox"'+(value ? ' checked' : '')+
                ' data-group="view" data-option="'+ind+'"><span></span></label></div>';
        }
        wrapper.append(str);
        checkCategoryListFieldsActive();
        openPickerModal(modal, this);
    }
});

function checkCategoryListFieldsActive()
{
    $g('#category-list-fields-modal .fields-view-row').removeClass('first-disabled-field');
    if ($g('#category-list-fields-modal .fields-view-row.active').length) {
        $g('#category-list-fields-modal .fields-view-row').not('.active').first().addClass('first-disabled-field');
    }
}

$g('#category-list-fields-modal .category-list-fields-view-wrapper').on('change', 'input[type="checkbox"]', function(){
    app.edit.desktop[this.dataset.group][this.dataset.option] = this.checked;
    app.sectionRules();
    if (app.edit.type == 'recent-posts-slider' || app.edit.type == 'related-posts-slider'
        || app.edit.type == 'recently-viewed-products') {
        var object = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initItems', object);
    } else if (app.edit.type == 'submission-form') {
        getSubmissionForm();
    }
    app.addHistory();
    if (this.checked) {
        this.closest('.fields-view-row').classList.add('active');
    } else {
        this.closest('.fields-view-row').classList.remove('active');
    }
    checkCategoryListFieldsActive();
});

$g('.event-calendar-layout-select').on('customAction', function(){
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
});


$g('.ba-modal-cp .sorting-container').on('change', 'input[type="checkbox"]', function(){
    let checked = [],
        parent = this.closest('.items-list'),
        checkbox = parent.querySelectorAll('input[type="checkbox"]'),
        btns = {};
    parent.querySelectorAll('.sorting-toolbar-action').forEach(function(btn){
        btns[btn.dataset.action] = btn;
    });
    checkbox.forEach(function($this){
        $this.checked ? checked.push($this.closest('.sorting-item')) : '';
    });
    if (btns.copy) {
        btns.copy.classList[checked.length ? 'remove' : 'add']('disabled');
    }
    if (btns.delete) {
        btns.delete.classList[checked.length ? 'remove' : 'add']('disabled');
    }
    btns.check.dataset.checked = checked.length && checked.length == checkbox.length ? 'true' : 'false';
});

$g('.ba-modal-cp .sorting-toolbar-action[data-action="check"]').on('click', function(){
    let parent = this.closest('.items-list'),
        checked = this.dataset.checked == 'true',
        checkbox = parent.querySelectorAll('input[type="checkbox"]'),
        flag = !checked && checkbox.length != 0,
        btns = {};
    checkbox.forEach(function($this){
        $this.checked = !checked;
    });
    parent.querySelectorAll('.sorting-toolbar-action').forEach(function(btn){
        btns[btn.dataset.action] = btn;
    });
    if (btns.copy) {
        btns.copy.classList[flag ? 'remove' : 'add']('disabled');
    }
    if (btns.delete) {
        btns.delete.classList[flag ? 'remove' : 'add']('disabled');
    }
    this.dataset.checked = flag;
    this.classList[checkbox.length != 0 ? 'remove' : 'add']('disabled');
});

$g('.ba-modal-cp .sorting-container').on('click', '.unpublish-sorting-item', function(){
    let item = this.closest('.sorting-item'),
        key = item.dataset.key,
        type = app.edit.type,
        obj = sortingList[key],
        action = !obj.unpublish ? 'add' : 'remove';
    obj.unpublish = !obj.unpublish;
    item.classList[action]('unpublished-sorting-item');
    obj.parent.classList[action]('ba-unpublished-html-item');
    if (type == 'tabs') {
        app.editor.document.querySelector(obj.href).classList[action]('ba-unpublished-html-item');
    } else if (type == 'slideset' || type == 'carousel' || type == 'testimonials-slider'
        || type == 'slideshow' || type == 'content-slider') {
        obj.parent.classList.remove('active');
        var object = {
            data : app.edit,
            selector : app.editor.app.edit
        };
        app.editor.app.checkModule('initItems', object);
    }
    if (type == 'content-slider' || type == 'testimonials-slider') {
        app.edit.slides[key].unpublish = obj.unpublish;
    } else if (type == 'slideset' || type == 'carousel' || type == 'slideshow') {
        app.edit.desktop.slides[key].unpublish = obj.unpublish;
    } else if (type == 'feature-box') {
        app.edit.items[key].unpublish = obj.unpublish;
    }
    app.sectionRules();
    app.addHistory();
});

app.positioning = {
    getWidthRange: function(){
        if (!this.width) {
            this.width = document.querySelector('.positioning-width-template').content.cloneNode(true).querySelector('div');
            rangeAction(this.width.querySelector('input[type="range"]'), inputCallback);
        }

        return this.width;
    },
    setValues: function(modal){
        this.modal = modal;
        this.item = app.editor.document.querySelector(app.selector);
        if (!app.edit.positioning) {
            app.edit.positioning = {
                position: '',
                parent: ''
            }
            app.edit.desktop.positioning = {
                x: '',
                y: '',
                z: 1,
                horizontal: '',
                vertical: ''
            }
        }
        let position = app.edit.positioning.position,
            query = '',
            isAbs = position == 'absolute';
        modal.attr('data-positioning', position == '' ? '' : 'enable');
        modal.find('.positioning-sub-options').css('display', position == '' ? 'none' : '');
        modal.find('.positioning-select').each(function(){
            this.querySelector('input[type="hidden"]').value = position;
            this.querySelector('input[type="text"]').value = app._((position == '' ? 'default' : position).toUpperCase());
        });
        if (this.hasWidth) {
            modal.find('.positioning-sub-options').prepend(this.getWidthRange());
        } else if (this.width) {
            this.width.remove();
        }
        modal.find('.positioning-sub-options [data-group="positioning"]').each(function(){
            if (this.dataset.option == 'x' || this.dataset.option == 'y') {
                let wrapper = this.closest('.ba-range-wrapper');
                wrapper.querySelector('.ba-range-liner').classList[isAbs ? 'add' : 'remove']('letter-spacing');
                wrapper.querySelector('.ba-range').setAttribute('min', isAbs ? -2000 : 0);
            } else if (this.dataset.option == 'width' && !('width' in app.edit.desktop.positioning)) {
                let rect = app.positioning.getRect(app.edit);
                app.edit.desktop.positioning.width = rect.width;
            }
            value = app.getValue('positioning', this.dataset.option);
            if (this.type == 'number' || this.type == 'text') {
                app.setLinearInput($g(this), value);
            } else if (this.type == 'hidden') {
                this.value = value;
                this.closest('div').querySelector('input[type="text"]').value = value;
            } else {
                this.classList[value == this.dataset.value ? 'add' : 'remove']('active');
            }
        });
    },
    getParent: function(obj){
        return app.editor.app.positioning.getParent(obj);
    },
    getRect: function(obj){
        let item = this.item,
            rect = null;
        if (obj.type == 'button' || obj.type == 'overlay-button' || obj.type == 'image'
            || obj.type == 'scroll-to' || obj.type == 'cart' || obj.type == 'wishlist' || obj.type == 'hotspot') {
            item = item.querySelector('.ba-button-wrapper a, .ba-image-wrapper');
        } else if (obj.type == 'icon') {
            item = item.querySelector('.ba-icon-wrapper i');
        } else if (obj.type == 'counter') {
            item = item.querySelector('.counter-number');
        } else if (obj.type == 'logo') {
            item = item.querySelector('img');
        }
        rect = $g.extend({}, item.getBoundingClientRect());
        if (obj.type == 'menu' || obj.type == 'one-page') {
            let li = item.querySelector('li'),
                rect2 = li ? li.getBoundingClientRect() : null,
                delta = li ? rect2.left - rect.left : 0;
            rect.left = rect.left + delta;
            rect.width = rect.width - delta;
        }
        if (obj.positioning.position == 'absolute') {
            let position = this.getParent(obj).getBoundingClientRect();
            rect.top -= position.top;
            rect.left -= position.left;
        }

        return rect;
    },
    getAbsoluteHorizontal: function(x, rect, direction){
        let parent = this.getParent(app.edit).getBoundingClientRect(),
            comp = app.editor.getComputedStyle(app.editor.document.body),
            w = app.editor.document.documentElement.offsetWidth;
        if (direction == 'left') {
            x = parent.left * -1 + comp.borderLeftWidth.replace('px', '') * 1;
        } else if (direction == 'center') {
            x = w / 2 - rect.width / 2 - parent.left;
        } else {
            x = (w - parent.right - comp.borderRightWidth.replace('px', '') * 1) * -1;
        }

        return x;
    },
    getAbsoluteVertical: function(y, rect, direction){
        let parent = this.getParent(app.edit).getBoundingClientRect(),
            delta,
            offset = app.editor.pageYOffset,
            h = app.editor.innerHeight;
        if (direction == 'top') {
            delta = 40 - offset;
            y = parent.top * -1 + (delta < 0 ? 0 : delta);
        } else if (direction == 'center') {
            y = h / 2 - rect.height / 2 - parent.top;
        } else {
            delta = 40 - (app.editor.document.documentElement.offsetHeight - offset - app.editor.innerHeight);
            y = (h - parent.bottom) * -1 + (delta < 0 ? 0 : delta)
        }

        return y;
    },
    getHorizontal: function(direction){
        let rect = app.positioning.getRect(app.edit),
            comp = app.editor.getComputedStyle(app.editor.document.body),
            x = 0;
        if (app.edit.positioning.position == 'absolute') {
            x = this.getAbsoluteHorizontal(x, rect, direction);
        } else if (direction == 'center') {
            x = app.editor.document.documentElement.offsetWidth / 2 - rect.width / 2 - comp.borderLeftWidth.replace('px', '') * 1;
        }

        return x;
    },
    getVertical: function(direction){
        let rect = app.positioning.getRect(app.edit),
            y = 0;
        if (app.edit.positioning.position == 'absolute') {
            y = this.getAbsoluteVertical(y, rect, direction);
        } else if (direction == 'center') {
            y = app.editor.innerHeight / 2 - rect.height / 2;
        }

        return y;
    },
    setDirection: function(option, direction){
        value = option == 'horizontal' ? this.getHorizontal(direction) : this.getVertical(direction);
        option = option == 'horizontal' ? 'x' : 'y';
        value = Math.round(value);
        app.setValue(value, 'positioning', option);
        app.positioning.setValues(app.positioning.modal);
    },
    setPosition: function(obj, position, item){
        obj.positioning.position = position;
        if (item.closest('.ba-grid-column')) {
            obj.positioning.parent = item.closest('.ba-grid-column').id;
            obj.positioning.row = item.closest('.ba-row').id;
            obj.positioning.section = item.closest('.ba-section').id;
        }
        let win = app.editor,
            x = y = '',
            doc = win.document,
            offset = win.pageYOffset,
            $this = app.positioning,
            rect = $this.getRect(obj);
        if (position) {
            doc.body.append(item);
            item.classList.add('ba-item-in-positioning');
            y = Math.round(rect.top);
            x = Math.round(rect.left) - (app.view == 'desktop' && position == 'fixed' ? 52 : 0);
        } else {
            let parent = $this.getParent(obj);
            $g(parent).find('> .empty-item').before(item);
            item.classList.remove('ba-item-in-positioning');
        }
        if (position && $this.hasWidth) {
            obj.desktop.positioning.width = Math.ceil(rect.width);
        }
        if (position == 'fixed') {
            let delta = 40 - win.pageYOffset;
            y -= (delta < 0 ? 0 : delta);
        }
        obj.desktop.positioning.x = x;
        obj.desktop.positioning.y = y;
        obj.desktop.positioning.horizontal = '';
        obj.desktop.positioning.vertical = '';
        for (let ind in app.editor.breakpoints) {
            if (obj[ind] && obj[ind].positioning) {
                delete obj[ind].positioning;
            }
        }
    },
    setEvents: function(){
        $g('.positioning-select').on('customAction', function(){
            value = this.querySelector('input[type="hidden"]').value;
            if (value != app.edit.positioning.position) {
                let item = app.positioning.item,
                    $this = app.positioning;
                $this.setPosition(app.edit, value, item);
                $this.setValues($this.modal);
                app.editor.app.positioning.setOffsets();
                app.sectionRules();
                app.addHistory();
                if (app.pageStructure && app.pageStructure.visible) {
                    app.pageStructure.updateStructure(true);
                    app.pageStructure.inStructure(app.editor.app.edit);
                }
                if (app.edit.type == 'lottie-animations') {
                    app.editor.app.initLottieAnimations(app.edit, app.editor.app.edit);
                }
            }
        });
        $g('.positioning-z-index-select').on('customAction', function(){
            value = this.querySelector('input[type="hidden"]').value;
            app.setValue(value, 'positioning', 'z');
            app.sectionRules();
            app.addHistory();
        });
    }
}

$g('#login_recaptcha option').each(function(){
    if (this.value == 'recaptcha' || this.value == 'recaptcha_invisible') {
        let str = '<li data-value="'+this.value+'">'+this.textContent.trim()+'</li>';
        $g(this).closest('.ba-settings-item').find('ul').append(str);
    }
});

$g('.animation-appearance-action').on('click', function(){
    fontBtn = this;
    let modal = $g('#section-animations-modal');
    openPickerModal(modal, this);
});

$g('.repeat-animation').on('change', function(){
    app.setValue(this.checked, this.dataset.group, 'repeat');
    app.editor.app.checkAnimation();
})

$g('.effect-select').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]')
        text = this.querySelector('input[type="text"]').value.trim();
    value = input.value;
    fontBtn.value = text;
    app.setValue(value, input.dataset.group, 'effect');
    app.sectionRules();
    app.editor.app.checkAnimation();
});

$g('.on-scroll-animations-wrapper label').on('click', function(){
    let modal = $g('#on-scroll-animations-modal'),
        text = '',
        range = null,
        group = this.dataset.value,
        motion = app.getValue('motions', this.dataset.value);
    modal.find('.enable-on-scroll-animation input').prop('checked', motion.enable);
    modal.find('.viewport-on-scroll-animation input[type="range"]').each(function(i){
        this.value = i == 0 ? motion.viewport.start : motion.viewport.end;
    }).trigger('customChange');
    modal.find('input[data-group]').attr('data-group', group);
    modal.find('input[data-option="speed"]').each(function(){
        app.setLinearInput($g(this), motion.speed);
    });
    modal.find('input[data-option="end"][data-subgroup="property"]').each(function(){
        app.setLinearInput($g(this), motion.property.end);
    });
    modal.find('.'+group+'-animation-options .set-on-scroll-direction').each(function(){
        text = this.querySelector('li[data-value="'+motion.direction+'"]').textContent.trim();
        this.querySelector('input[type="hidden"]').value = motion.direction;
        this.querySelector('input[type="text"]').value = text;
    });
    modal.find('.ba-settings-item[class*="-animation-options"]').css('display', 'none');
    modal.find('.'+group+'-animation-options').css('display', '');
    openPickerModal(modal, this);
});

$g('#on-scroll-animations-modal .enable-on-scroll-animation input').on('change', function(){
    app.setValue(this.checked, 'motions', 'enable', this.dataset.group);
    $g('.on-scroll-animations-wrapper label[data-value="'+this.dataset.group+'"]')[this.checked ? 'addClass' : 'removeClass']('active');
    app.editor.app.checkAnimation();
    app.addHistory();
});

$g('#on-scroll-animations-modal .set-on-scroll-direction').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]');
    value = input.value;
    app.setValue(value * 1, 'motions', 'direction', input.dataset.group);
    app.editor.app.checkAnimation();
    app.addHistory();
});

app.onScrollAnimationCallback = function(input){
    clearTimeout(delay)
    delay = setTimeout(function(){
        if (input.closest('.ba-slider-wrapper')) {
            app.setValue(input.values[0] * 1, 'motions', 'start', input.dataset.group, 'viewport');
            app.setValue(input.values[1] * 1, 'motions', 'end', input.dataset.group, 'viewport');
        } else if (input.dataset.option == 'speed') {
            app.setValue(input.value * 1, 'motions', 'speed', input.dataset.group);
        } else if (input.dataset.option == 'end' && input.dataset.subgroup == 'property') {
            app.setValue(input.value * 1, 'motions', 'end', input.dataset.group, 'property');
        }
        app.editor.app.checkAnimation();
        app.addHistory();
    }, 300);
}

app.setAnimationSettings = function(animation, modal){
    if (!(animation in app.edit.desktop)) {
        app.edit.desktop[animation] = {
            duration: 0.9,
            delay: 0,
            repeat: false,
            effect: ""
        }
    }
    for (var key in app.edit.desktop[animation]) {
        value = app.getValue(animation, key);
        var input = $g('#section-animations-modal input[data-option="'+key+'"]').attr('data-group', animation);
        switch (key) {
            case 'effect' :
                input.val(value);
                var select = input.closest('.ba-custom-select');
                value = select.find('li[data-value="'+value+'"]').text().trim();
                select.find('input[readonly]').val(value);
                modal.find('.animation-appearance-action').val(value);
                break;
            case 'repeat':
                input.prop('checked', value);
                break;
            default :
                app.setLinearInput(input, value);
        }
    }
    if (!app.edit.desktop.motions) {
        app.edit.desktop.motions = {};
    }
    if (!app.edit.desktop.motions.translateY) {
        app.edit.desktop.motions.translateY = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            speed: 0.6,
            direction: -1,
            property: {
                start: 0,
                end: 100,
                unit: 'px'
            }
        }
    }
    if (!app.edit.desktop.motions.translateX) {
        app.edit.desktop.motions.translateX = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            speed: 0.6,
            direction: -1,
            property: {
                start: 0,
                end: 100,
                unit: 'px'
            }
        }
    }
    if (!app.edit.desktop.motions.rotate) {
        app.edit.desktop.motions.rotate = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            direction: -1,
            property: {
                start: 0,
                end: 720,
                unit: 'deg'
            }
        }
    }
    if (!app.edit.desktop.motions.opacity) {
        app.edit.desktop.motions.opacity = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            direction: -1,
            property: {
                start: 0,
                end: 100,
                unit: ''
            }
        }
    }
    if (!app.edit.desktop.motions.blur) {
        app.edit.desktop.motions.blur = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            direction: -1,
            property: {
                start: 0,
                end: 10,
                unit: 'px'
            }
        }
    }
    if (!app.edit.desktop.motions.scale) {
        app.edit.desktop.motions.scale = {
            enable: false,
            viewport: {
                start: 0,
                end : 100
            },
            direction: -1,
            property: {
                start: 1,
                end: 3,
                unit: ''
            }
        }
    }
    let motions = app.getValue('motions');
    for (let key in motions) {
        modal.find('.on-scroll-animations-wrapper label[data-value="'+key+'"]')[motions[key].enable ? 'addClass' : 'removeClass']('active');
    }
}

app.setAccessSettings = function(modal){
    if (!app.edit.access_view) {
        app.edit.access_view = 1;
    }
    modal.find('.section-access-select input[type="hidden"]').val(app.edit.access);
    value = modal.find('.section-access-select li[data-value="'+app.edit.access+'"]').text().trim();
    modal.find('.section-access-select input[readonly]').val(value);
    modal.find('.class-suffix').val(app.edit.suffix);
    modal.find('.section-access-view-select input[type="hidden"]').val(app.edit.access_view);
    value = modal.find('.section-access-view-select li[data-value="'+app.edit.access_view+'"]').text().trim();
    modal.find('.section-access-view-select input[readonly]').val(value);
}

$g('.field-admin-description').on('input', function(){
    app.edit.options.description = this.value.trim();
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

app.recentPostsTags = {
    check: (modal) => {
        if (!app.edit.tags) {
            app.edit.tags = {};
            app.edit.posts_type = '';
        }
        if (!app.edit.categories) {
            app.edit.categories = {};
        }
        modal.find('.tags-type-options, .categories-type-options').hide();
        modal.find('.'+(app.edit.posts_type ? 'tags' : 'categories')+'-type-options').css('display', '');
        modal.find('.recent-posts-type-select input').each(function(){
            this.value = this.type == 'hidden' ? app.edit.posts_type : app._(app.edit.posts_type ? 'TAGS' : 'CATEGORY');
        });
        modal.find('ul.post-tags-list').each(function(){
            let ul = $g(this),
                str = title = '';
            ul.find('li:not(.trigger-post-tags-modal)').remove();
            for (let id in app.edit.tags) {
                title = document.querySelector('#post-tags-dialog .ba-settings-item[data-id="'+id+'"]');
                if (!title) {
                    continue;
                }
                str = app.recentPostsTags.getHTML(title.textContent.trim(), id);
                ul.find('.trigger-post-tags-modal').before(str);
            }
            modal.find('.tags-type-options')[this.querySelector('li.tags-chosen') ? 'addClass' : 'removeClass']('not-empty-list');
        });
        modal.find('.selected-categories li:not(.search-category)').remove();
        modal.find('.all-categories-list .selected-category').removeClass('selected-category');
        for (let key in app.edit.categories) {
            let str = getCategoryHtml(key, app.edit.categories[key].title);
            modal.find('.selected-categories li.search-category').before(str);
            modal.find('.all-categories-list [data-id="'+key+'"]').addClass('selected-category');
        }
        if (modal.find('.selected-categories li:not(.search-category)').length > 0) {
            modal.find('.ba-settings-item.tags-categories-list').addClass('not-empty-list');
        } else {
            modal.find('.ba-settings-item.tags-categories-list').removeClass('not-empty-list');
        }
        modal.find('.search-category')[0].dataset.enabled = Number(modal.find('.all-categories-list li[data-app="'+app.edit.app+'"]').length > 0);
        modal.find('.trigger-post-tags-modal')[0].dataset.enabled = Number(app.edit.app != 0);
        $g('.tags-categories .all-categories-list li').hide();
    },
    getHTML: (title, id) => {
        return '<li class="tags-chosen"><span>'+title+'</span><i class="zmdi zmdi-close" data-remove="'+id+'"></i></li>';
    },
    set: () => {
        $g('.recent-posts-type-select').on('customAction', function(){
            let modal = $g(this).closest('.modal');
            app.edit.posts_type = this.querySelector('input[type="hidden"]').value;
            modal.find('.tags-type-options, .categories-type-options').hide();
            modal.find('.'+(app.edit.posts_type ? 'tags' : 'categories')+'-type-options').css('display', '');
            window[app.recentPostsCallback]();
        });
        $g('.trigger-post-tags-modal').on('click', function(){
            if (this.dataset.enabled == 0) {
                return;
            }
            fontBtn = this;
            let ul = this.closest('ul');
            $g('#post-tags-dialog .ba-settings-item[data-id]').each(function(){
                this.classList[ul.querySelector('li i[data-remove="'+this.dataset.id+'"]') ? 'add' : 'remove']('selected');
            });
            showDataTagsDialog('post-tags-dialog');
        }).on('change', function(){
            let id = this.postTag.id,
                title = this.postTag.title;
            if (this.closest('ul').querySelector('li i[data-remove="'+id+'"]')) {
                return;
            }
            app.edit.tags[id] = {
                id: id
            }
            $g(this).before(app.recentPostsTags.getHTML(title, id));
            $g('#post-tags-dialog').modal('hide');
            this.closest('.tags-type-options').classList.add('not-empty-list');
            window[app.recentPostsCallback]();
        });
        $g('ul.post-tags-list').on('click', 'i[data-remove]', function(){
            delete app.edit.tags[this.dataset.remove];
            let wrapper = this.closest('.tags-type-options');
            this.closest('li.tags-chosen').remove();
            wrapper.classList[wrapper.querySelector('li.tags-chosen') ? 'add' : 'remove']('not-empty-list');
            window[app.recentPostsCallback]();
        });
    }
}

app.recentPostsTags.set();

app.positioning.setEvents();
app.loadModule('presetsPatern');
app.loadModule('draggable');
app.loadModule('resizable');
setTabsAnimation();