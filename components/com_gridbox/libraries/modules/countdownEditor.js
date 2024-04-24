/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.countdownEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#countdown-settings-dialog'),
        match, v;
    $g('#countdown-settings-dialog .active').removeClass('active');
    $g('#countdown-settings-dialog a[href="#countdown-general-options"]').parent().addClass('active');
    $g('#countdown-general-options').addClass('active');
    var value = '';
    setPresetsList($g('#countdown-settings-dialog'));
    $g('#countdown-settings-dialog').find('.ba-settings-group, .ba-settings-item').css('display', '');
    if ((app.edit.type == 'overlay-button' || app.edit.type == 'button' || app.edit.type == 'submit-button' || app.edit.type == 'icon')
        && !('embed' in app.edit)) {
        app.edit.embed = '';
    }
    app.positioning.hasWidth = false;
    app.positioning.setValues(modal);
    if (app.edit.type == 'field-button') {
        modal.find('.field-admin-label').val(app.edit.options.label);
        modal.find('.field-admin-description').val(app.edit.options.description);
        modal.find('.field-description').val(app.edit.description);
        modal.find('.select-field-icon').val(app.edit.options.icon.replace(/zmdi zmdi-|fa fa-|flaticon-/, ''));
        modal.find('input[data-option="required"]').prop('checked', app.edit.required);
        modal.find('.field-button-options .ba-custom-select input[data-option]').each(function(){
            this.value = app.edit.options[this.dataset.option];
            let text = $g(this).closest('.ba-custom-select').find('li[data-value="'+this.value+'"]').text().trim();
            this.previousElementSibling.value = text;
        });
        modal.find('.field-constant-label-option').css('display', app.edit.options.label_type == '' ? 'none' : '').find('input').each(function(){
            this.value = app.edit.options.constant;
        })
        modal.find('.button-icon-position input[type="hidden"]').val(app.edit.icon.position);
        value = modal.find('.button-icon-position li[data-value="'+app.edit.icon.position+'"]').text();
        modal.find('.button-icon-position input[readonly]').val(value.trim());
        value = app.getValue('icons', 'size');
        app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
        app.setTypography(modal.find('.button-options .typography-options'), 'typography');
    }
    if (app.edit.type == 'overlay-button') {
        if (!app.edit.trigger) {
            app.edit.trigger = 'button';
            app.edit.desktop.style = {
                "width" : "650",
                "align" : "center"
            }
            app.edit.image = "components/com_gridbox/assets/images/default-theme.png";
            app.edit.alt = "";
            app.edit.sides = {
                image: {
                    "desktop":{
                        "border" : {
                            "color" : "@border",
                            "radius" : "9",
                            "style" : "solid",
                            "width" : "0"
                        },
                        "margin" : {
                            "bottom" : "25",
                            "top" : "25"
                        },
                        "shadow" : {
                            "value" : "0",
                            "color" : "@shadow"
                        }
                    },
                    "tablet":{},
                    "phone":{},
                    "tablet-portrait":{},
                    "phone-portrait":{}
                },
                button: {
                    "desktop":{},
                    "tablet":{},
                    "phone":{},
                    "tablet-portrait":{},
                    "phone-portrait":{}
                }
            }
            app.edit.caption = {
                title: '',
                description: '',
            }
            app.edit.desktop.overlay = {
                type: 'none',
                color: '@overlay',
                gradient: {
                    "effect": "linear",
                    "angle": 45,
                    "color1": "@bg-dark",
                    "position1": 25,
                    "color2": "@bg-dark-accent",
                    "position2": 75
                }
            }
            app.edit.desktop.title = {
                "typography" : {
                    "color" : "@title-inverse",
                    "font-family" : "@default",
                    "font-size" : 32,
                    "font-style" : "normal",
                    "font-weight" : "900",
                    "letter-spacing" : 0,
                    "line-height" : 42,
                    "text-decoration" : "none",
                    "text-align" : "center",
                    "text-transform" : "none"
                },
                "margin" : {
                    "bottom" : "0",
                    "top" : "0"
                }
            };
            app.edit.desktop.description = {
                "typography" : {
                    "color" : "@title-inverse",
                    "font-family" : "@default",
                    "font-size" : 21,
                    "font-style" : "normal",
                    "font-weight" : "300",
                    "letter-spacing" : 0,
                    "line-height" : 36,
                    "text-decoration" : "none",
                    "text-align" : "center",
                    "text-transform" : "none"
                },
                "margin" : {
                    "bottom" : "0",
                    "top" : "0"
                }
            };
            app.edit.desktop.animation = {
                "effect": "ba-fade",
                "duration": 0.3
            }
        }
        if (!app.edit.tag) {
            app.edit.tag = 'h3';
        }
        $g('.overlay-button-trigger-select input[type="hidden"]').val(app.edit.trigger);
        $g('.overlay-button-trigger-select input[type="text"]').val(gridboxLanguage[app.edit.trigger.toUpperCase()]);
        var src = app.edit.image,
            array = src.split('/'),
            str = '<div class="sorting-item"><div class="sorting-image">';                
        if (!app.isExternal(src)) {
            src = JUri+src;
        }
        str += '<img src="'+src+'"></div><div class="sorting-title">'+array[array.length - 1]+
            '</div><div class="sorting-icons"><span><i class="zmdi zmdi-edit"></i></span></div></div>';
        $g('#countdown-settings-dialog .sorting-container').html(str);
        value = app.getValue('overlay', 'effect', 'gradient');
        $g('#countdown-settings-dialog .overlay-linear-gradient').hide();
        $g('#countdown-settings-dialog .overlay-'+value+'-gradient').css('display', '');
        $g('#countdown-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="hidden"]').val(value);
        value = $g('#countdown-settings-dialog .overlay-gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
        $g('#countdown-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="text"]').val(value);
        value = app.getValue('overlay', 'type');
        $g('#countdown-settings-dialog .overlay-color-options, .overlay-gradient-options').hide();
        $g('#countdown-settings-dialog .overlay-'+value+'-options').css('display', '');
        $g('#countdown-settings-dialog .background-overlay-select input[type="hidden"]').val(value);
        value = $g('#countdown-settings-dialog .background-overlay-select li[data-value="'+value+'"]').text().trim();
        $g('#countdown-settings-dialog .background-overlay-select input[type="text"]').val(value);
        $g('#countdown-settings-dialog .slideshow-style-custom-select input[type="hidden"]').val('title');
        $g('#countdown-settings-dialog .slideshow-style-custom-select input[readonly]').val(gridboxLanguage['TITLE']);
        $g('#countdown-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
        $g('#countdown-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
        showSlideshowDesign('title', $g('#countdown-settings-dialog .slideshow-style-custom-select'));
        $g('.overlay-button-options').find('[data-group="style"], [data-group="animation"], [data-group="overlay"]').each(function(){
            value = app.getValue(this.dataset.group, this.dataset.option, this.dataset.subgroup);
            if (this.dataset.type == 'color') {
                updateInput($g(this), value);
            } if (this.type == 'number' || this.type == 'text') {
                app.setLinearInput($g(this), value);
            } else if (this.type == 'hidden') {
                this.value = value;
                value = this.parentNode.querySelector('li[data-value="'+value+'"]').textContent.trim();
                this.previousElementSibling.value = value;
            } else if (this.dataset.type == 'color') {
                updateInput($g(this), value);
            } else if (this.dataset.value == value) {
                this.classList.add('active');
            } else {
                this.classList.remove('active');
            }
        });
        if (app.edit.trigger == 'button') {
            $g('.button-label').closest('.button-options').css('display', '');
            $g('#countdown-settings-dialog .padding-options').css('display', '');
            $g('#countdown-design-options .ba-settings-group').css('display', '');
            $g('.overlay-image-options').hide();
            app.editor.$g(app.selector+' > .ba-image-wrapper').remove();
        } else {
            $g('.button-label').closest('.button-options').hide();
            $g('#countdown-settings-dialog .padding-options').hide();
            $g('.overlay-image-options').css('display', '');
        }
        $g('#countdown-settings-dialog .button-embed-code').val(app.edit.embed);
    } else {
        $g('.overlay-button-options').hide();
    }
    switch (app.edit.type) {
        case 'countdown' :
            app.setTypography($g('#countdown-settings-dialog .countdown-options .typography-options'), 'counter');
            $g('#countdown-settings-dialog .typography-select input[type="hidden"]').val('counter');
            value = $g('#countdown-settings-dialog .typography-select li[data-value="counter"]').text();
            $g('#countdown-settings-dialog .typography-select input[readonly]').val($g.trim(value));
            value = app.getValue('background', 'color');
            updateInput($g('#countdown-settings-dialog .background input[data-option="color"]'), value);
            $g('#countdown-input').val(app.edit.date);
            $g('.countdown-display-select input[type="hidden"]').val(app.edit.display);
            value = $g('.countdown-display-select li[data-value="'+app.edit.display+'"]').text();
            $g('.countdown-display-select input[readonly]').val(value);
            $g('input[data-option="hide-after"]').prop('checked', app.edit['hide-after']);
            $g('.constants.countdown-options [data-option]').each(function(){
                this.value = app.edit[this.dataset.option];
            });
            break;
        case 'icon' :
            value = app.getValue('icon', 'text-align');
            $g('#countdown-settings-dialog [data-option="text-align"][data-value="'+value+'"]').addClass('active');
            value = app.getValue('icon', 'size');
            $g('#countdown-settings-dialog .icon-options [data-option="size"]').val(value);
            app.setLinearInput(modal.find('.icon-options [data-option="size"]'), value);
            value = app.editor.document.getElementById(app.editor.app.edit);
            value = value.querySelector('.ba-icon-wrapper i');
            value = value.dataset.icon.replace('zmdi zmdi-', '').replace('fa fa-', '').replace('flaticon-', '');
            $g('#countdown-settings-dialog .reselect-icon').val(value);
        case 'button':
        case 'submit-button':
            $g('#countdown-settings-dialog [data-option="link"][data-group="link"]').val(app.edit.link.link);
            $g('#countdown-settings-dialog .link-target-select input[type="hidden"]').val(app.edit.link.target);
            value = $g('#countdown-settings-dialog .link-target-select li[data-value="'+app.edit.link.target+'"]').text();
            $g('#countdown-settings-dialog .link-target-select input[readonly]').val($g.trim(value));
            if (!app.edit.link.type) {
                app.edit.link.type = '';
            }
            $g('#countdown-settings-dialog .link-type-select input[type="hidden"]').val(app.edit.link.type);
            value = $g('#countdown-settings-dialog .link-type-select li[data-value="'+app.edit.link.type+'"]').text();
            $g('#countdown-settings-dialog .link-type-select input[readonly]').val($g.trim(value));
            $g('#countdown-settings-dialog .button-embed-code').val(app.edit.embed);
        case 'overlay-button' :
        case 'tags' :
        case 'post-tags' :
        case 'scroll-to-top' :
        case 'scroll-to' :
        case 'cart' :
        case 'field-button' :
            app.setDefaultState('#countdown-settings-dialog .padding-settings-group', 'default');
            app.setPaddingValues('#countdown-settings-dialog .padding-settings-group');
            app.editor.app.cssRules.prepareColors(app.edit);
            app.setDefaultState('#countdown-settings-dialog .colors-settings-group', 'default');
            app.setColorsValues('#countdown-settings-dialog .colors-settings-group');
        case 'counter' :
            app.setDefaultState('#countdown-settings-dialog .shadow-settings-group', 'default');
            app.setShadowValues('#countdown-settings-dialog .shadow-settings-group');
    }
    if (app.editor.document.getElementById(app.editor.app.edit).dataset.cookie == 'accept' || app.edit.type == 'submit-button') {
        $g('.button-link-options').hide();
    } else {
        $g('.button-link-options').css('display', '');
    }
    if (app.edit.type == 'submit-button') {
        modal.find('.submit-button-onsubmit-select input[type="hidden"]').val(app.edit.onsubmit.action);
        value = modal.find('.submit-button-onsubmit-select li[data-value="'+app.edit.onsubmit.action+'"]').text().trim();
        modal.find('.submit-button-onsubmit-select input[type="text"]').val(value);
        modal.find('.thank-you-onsubmit-options, .redirect-onsubmit-options').hide();
        modal.find('.'+app.edit.onsubmit.action+'-onsubmit-options').css('display', '');
        modal.find('.thank-you-onsubmit-options input').val(app.edit.onsubmit.message);
        modal.find('.redirect-onsubmit-options input').val(app.edit.onsubmit.redirect);
    }
    if (app.edit.type == 'button' || app.edit.type == 'overlay-button' || app.edit.type == 'submit-button') {
        value = app.editor.document.querySelector(app.selector+' .ba-button-wrapper a span').textContent;
        $g('#countdown-settings-dialog input.button-label').val(value);
        value = app.getValue('icons', 'size');
        app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
        value = app.editor.document.getElementById(app.editor.app.edit);
        value = value.querySelector('.ba-button-wrapper a i');
        if (value) {
            value = value.className.replace('zmdi zmdi-', '').replace('fa fa-', '');
        } else {
            value = '';
        }
        $g('#countdown-settings-dialog [data-option="icon"][data-group="icon"]').val(value);
        $g('#countdown-settings-dialog .button-icon-position input[type="hidden"]').val(app.edit.icon.position);
        value = $g('#countdown-settings-dialog .button-icon-position li[data-value="'+app.edit.icon.position+'"]').text();
        $g('#countdown-settings-dialog .button-icon-position input[readonly]').val(value.trim());
        app.setTypography($g('#countdown-settings-dialog .button-options .typography-options'), 'typography');
    } else if (app.edit.type == 'cart') {
        value = app.getValue('icons', 'size');
        app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
        value = app.editor.document.getElementById(app.editor.app.edit);
        value = value.querySelector('.ba-button-wrapper a i');
        if (value) {
            value = value.className.replace('zmdi zmdi-', '').replace('fa fa-', '');
        } else {
            value = '';
        }
        $g('#countdown-settings-dialog [data-option="icon"][data-group="icon"]').val(value);
        $g('#countdown-settings-dialog .button-icon-position input[type="hidden"]').val(app.edit.icon.position);
        value = $g('#countdown-settings-dialog .button-icon-position li[data-value="'+app.edit.icon.position+'"]').text();
        $g('#countdown-settings-dialog .button-icon-position input[readonly]').val(value.trim());
        app.setTypography($g('#countdown-settings-dialog .button-options .typography-options'), 'typography');
    } else if (app.edit.type == 'post-tags' || app.edit.type == 'tags') {
        app.setTypography($g('#countdown-settings-dialog .typography-options'), 'typography');
    } else if (app.edit.type == 'counter') {
        app.setTypography($g('#countdown-settings-dialog .typography-options'), 'counter');
        value = app.getValue('background', 'color');
        updateInput($g('#countdown-settings-dialog .background input[data-option="color"]'), value);
        $g('#countdown-settings-dialog .counter-general input[data-option="number"]').val(app.edit.counter.number);
        $g('#countdown-settings-dialog .counter-general input[data-option="speed"]').val(app.edit.counter.speed);
    } else if (app.edit.type == 'scroll-to-top' || app.edit.type == 'scroll-to') {
        if (app.edit.type == 'scroll-to-top') {
            value = app.edit.text.align;
            $g('#countdown-settings-dialog [data-option="align"][data-group="text"][data-value="'+value+'"]').addClass('active');
            value = app.getValue('icons', 'size');
            app.setLinearInput(modal.find('.scrolltop-options [data-option="size"]'), value);
            value = app.edit.icon.replace('zmdi zmdi-', '').replace('fa fa-', '');
            $g('#countdown-settings-dialog .scrolltop-icon').val(value);
        } else {
            value = app.editor.document.querySelector(app.selector+' .ba-button-wrapper a span').textContent;
            $g('#countdown-settings-dialog input.button-label').val(value);
            value = app.getValue('icons', 'size');
            app.setLinearInput(modal.find('[data-option="size"][data-group="icons"]'), value);
            if (app.edit.type != 'scroll-to') {
                value = app.editor.document.getElementById(app.editor.app.edit);
                value = value.querySelector('.ba-button-wrapper a i');
                if (value) {
                    value = value.className.replace('zmdi zmdi-', '').replace('fa fa-', '');
                } else {
                    value = '';
                }
                $g('#countdown-settings-dialog [data-option="icon"][data-group="icon"]').val(value);
            }
            value = app.getValue('icons', 'position');
            $g('#countdown-settings-dialog .scroll-to-icon-position input[type="hidden"]').val(value);
            value = $g('#countdown-settings-dialog .scroll-to-icon-position li[data-value="'+value+'"]').text().trim();
            $g('#countdown-settings-dialog .scroll-to-icon-position input[readonly]').val(value);
            app.setTypography($g('#countdown-settings-dialog .typography-options'), 'typography');
            value = app.edit.icon.replace('zmdi zmdi-', '').replace('fa fa-', '');
            $g('#countdown-settings-dialog .scroll-to-icon').val(value);
        }
        $g('#countdown-settings-dialog .scrolltop-general input[data-option]').each(function(){
            this.value = app.edit.init[this.dataset.option];
        });
        $g('#countdown-settings-dialog .select-end-point').val(app.edit.init.target);
    }
    if (app.edit.type == 'tags') {
        app.recentPostsCallback = 'getBlogTags';
        $g('#countdown-settings-dialog .tags-app-select input[type="hidden"]').val(app.edit.app);
        value = $g('#countdown-settings-dialog .tags-app-select li[data-value="'+app.edit.app+'"]').text();
        $g('#countdown-settings-dialog .tags-app-select input[readonly]').val(value.trim());
        if (!app.edit.sorting) {
            app.edit.sorting = 'hits';
        }
        $g('#countdown-settings-dialog .tags-display-select input[type="hidden"]').val(app.edit.sorting);
        value = $g('#countdown-settings-dialog .tags-display-select li[data-value="'+app.edit.sorting+'"]').text();
        $g('#countdown-settings-dialog .tags-display-select input[readonly]').val(value.trim());

        if (value) {
            $g('#countdown-settings-dialog .tags-categories-list').css('display', '');
        } else {
            $g('#countdown-settings-dialog .tags-categories-list').hide();
        }
        $g('#countdown-settings-dialog input[data-option="count"]').val(app.edit.count);
        $g('.selected-categories li:not(.search-category)').remove();
        $g('.all-categories-list .selected-category').removeClass('selected-category');
        for (var key in app.edit.categories) {
            var str = getCategoryHtml(key, app.edit.categories[key].title);
            $g('#countdown-settings-dialog .selected-categories li.search-category').before(str);
            $g('#countdown-settings-dialog .all-categories-list [data-id="'+key+'"]').addClass('selected-category');
        }
        if ($g('.selected-categories li:not(.search-category)').length > 0) {
            $g('.ba-settings-item.tags-categories-list').addClass('not-empty-list');
        } else {
            $g('.ba-settings-item.tags-categories-list').removeClass('not-empty-list');
        }
        modal.find('.search-category')[0].dataset.enabled = Number(modal.find('.all-categories-list li[data-app="'+app.edit.app+'"]').length > 0);
        $g('.tags-categories .all-categories-list li').hide();
    }
    if (app.edit.type != 'scroll-to-top') {
        app.setDefaultState('#countdown-layout-options .margin-settings-group', 'default');
        app.setMarginValues('#countdown-layout-options .margin-settings-group');
    }
    app.setDefaultState('#countdown-layout-options .border-settings-group', 'default');
    app.setBorderValues('#countdown-layout-options .border-settings-group');
    setDisableState('#countdown-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#countdown-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#countdown-settings-dialog').modal();
    }, 150);
}

$g('#countdown-settings-dialog .select-field-icon').on('click', function(){
    uploadMode = 'reselectSocialIcon';
    fontBtn = this;
    checkIframe($g('#icon-upload-dialog'), 'icons');
}).on('change', function(){
    app.edit.options.icon = this.dataset.icon;
    let item = app.editor.document.getElementById(app.editor.app.edit),
        i = item.querySelector('a i');
    if (!i) {
        i = document.createElement('i');
        item.querySelector('a').append(i);
    }
    i.className = this.dataset.icon;
    app.addHistory();
});

$g('#countdown-settings-dialog .reset-field-icon i').on('click', function(){
    app.editor.$g(app.selector+' a i').remove();
    app.edit.options.icon = '';
    $g('#countdown-settings-dialog .select-field-icon').val('').attr('data-icon', '');
    app.addHistory();
});

$g('#countdown-settings-dialog .field-button-options .ba-custom-select')
    .not('.gradient-effect-select').not('.colors-type-select').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]');
    app.edit.options[input.dataset.option] = input.value;
    if (input.dataset.option == 'label_type') {
        let flag = app.edit.options.label_type == '';
        $g('#countdown-settings-dialog .field-constant-label-option').css('display', flag ? 'none' : '');
        app.editor.$g(app.selector).find('a span').text(flag ? app._('VALUE') : app.edit.options.constant);
    }
    app.addHistory();
});

$g('#countdown-settings-dialog .field-admin-label').on('input', function(){
    app.edit.options.label = this.value.trim();
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#countdown-settings-dialog .field-constant-label').on('input', function(){
    app.edit.options.constant = this.value.trim();
    app.editor.$g(app.selector).find('a span').text(app.edit.options.constant);
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('.overlay-button-trigger-select').on('customAction', function(){
    app.edit.trigger = this.querySelector('input[type="hidden"]').value;
    $g('#countdown-design-options .ba-settings-group').css('display', '');
    app.editor.$g(app.selector+' > .ba-image-wrapper').remove();
    if (app.edit.trigger == 'button') {
        $g('.button-label').closest('.button-options').css('display', '');
        $g('#countdown-settings-dialog .padding-options').css('display', '');
        $g('.overlay-image-options').hide();
    } else {
        $g('.button-label').closest('.button-options').hide();
        $g('#countdown-settings-dialog .padding-options').hide();
        $g('#countdown-design-options .slideshow-margin-options.overlay-button-options').nextAll().hide();
        $g('.overlay-image-options').css('display', '');
        var wrapper = document.createElement('div'),
            img = document.createElement('img');
        wrapper.className = 'ba-image-wrapper '+app.edit.desktop.animation.effect;
        img.src = JUri+app.edit.image;
        img.alt = JUri+app.edit.alt;
        wrapper.appendChild(img);
        let str = '<div class="ba-image-item-caption"><div class="ba-caption-overlay"></div>'+
            '<'+app.edit.tag+' class="ba-image-item-title"></'+app.edit.tag+
            '><div class="ba-image-item-description"></div></div>';
        $g(wrapper).append(str);
        wrapper.querySelector('.ba-image-item-title').textContent = app.edit.caption.title;
        wrapper.querySelector('.ba-image-item-description').innerHTML = app.edit.caption.description;
        app.editor.$g(app.selector+' > .ba-button-wrapper').before(wrapper);
    }
    app.sectionRules();
    app.addHistory();
});

$g('.overlay-image-alt').on('input', function(){
    app.edit.image.alt = this.value;
    app.editor.$g(app.selector+' > .ba-image-wrapper img').attr('alt', app.edit.image.alt);
});

$g('#countdown-settings-dialog input[data-option="count"]').on('input', function(){
    let $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.count = $this.value;
        getBlogTags();
    });
});

$g('.tags-display-select').on('customAction', function(){
    app.edit.sorting = this.querySelector('input[type="hidden"]').value;
    getBlogTags();
});

$g('.tags-app-select').on('customAction', function(){
    var id = this.querySelector('input[type="hidden"]').value;
    if (id != app.edit.app) {
        app.edit.categories = {};
        app.edit.app = id;
        $g('.selected-categories li:not(.search-category)').remove();
        $g('.all-categories-list .selected-category').removeClass('selected-category');
        $g('.ba-settings-item.tags-categories-list').removeClass('not-empty-list');
        $g('#countdown-settings-dialog .tags-categories-list').css('display', '');
        getBlogTags();
    }
});

function getBlogTags()
{
    let category = [],
        sorting = app.edit.sorting ? app.edit.sorting : 'hits';
    for (var key in app.edit.categories) {
        category.push(key);
    }
    category = category.join(',');
    app.editor.$g(app.selector).attr('data-app', app.edit.app).attr('data-category', category)
        .attr('data-limit', app.edit.count);
    app.fetch(JUri+"index.php?option=com_gridbox&task=editor.getBlogTags", {
        category: category,
        limit: app.edit.count,
        id: app.edit.app,
        sorting: sorting
    }).then(function(text){
        app.editor.document.querySelector(app.selector+' .ba-button-wrapper').innerHTML = text;
        app.editor.app.buttonsPrevent();
        app.addHistory();
    });
}

$g('#countdown-settings-dialog .select-end-point').on('click', function(){
    app.editor.app.checkModule('setEndPoint');
    fontBtn = this;
});

$g('#countdown-settings-dialog .icon-options [data-option="inline"]').on('change', function(){
    app.setValue(this.checked, 'inline');
    app.sectionRules();
    app.addHistory();
});

$g('#countdown-settings-dialog .scrolltop-general input[data-option]').on('input', function(){
    let $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.init[$this.dataset.option] = $this.value;
        app.editor.app['init'+app.edit.type](app.edit, app.editor.app.edit);
        app.addHistory();
    }, 300);
});

$g('.end-point-cover').on('mousedown', function(event){
    event.stopPropagation();
});

$g('.scrolltop-animation-select').on('customAction', function(){
    $g(this).find('input[type="hidden"]').trigger('input');
});

$g('.constants.countdown-options [data-option]').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit[$this.dataset.option] = $this.value;
        var element = app.editor.document.getElementById(app.editor.app.edit);
        element = element.querySelector('.'+$this.dataset.option+' .countdown-label');
        element.innerText = $this.value;
        app.addHistory();
    }, 300);
});

$g('#countdown-general-options [data-option="hide-after"]').on('change', function(){
    app.edit['hide-after'] = this.checked;
    app.addHistory();
});

$g('.countdown-display-select').on('customAction', function(){
    app.edit.display = $g(this).find('input[type="hidden"]').val();
    app.editor.app.initcountdown(app.edit, app.editor.app.edit);
    app.addHistory();
});

$g('#countdown-input').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.date = $this.value;
        app.editor.app.initcountdown(app.edit, app.editor.app.edit);
        app.addHistory();
    }, 300);
});

$g('#countdown-settings-dialog .counter-general input[data-option]').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.counter[$this.dataset.option] = $this.value;
        app.editor.app.initcounter(app.edit, app.editor.app.edit);
        app.addHistory();
    }, 300);
})

$g('#countdown-settings-dialog .button-label').on('input', function(){
    var $this = this;
    clearTimeout(delay);
    delay = setTimeout(function(){
        var span = app.editor.document.getElementById(app.editor.app.edit);
        span = span.querySelector('a span');
        span.innerText = $this.value;
        if (!$g.trim($this.value)) {
            span.classList.add('empty-textnode');
        } else {
            span.classList.remove('empty-textnode');
        }
        app.addHistory();
    });
});

$g('#countdown-settings-dialog input[data-option="icon"][data-group="icon"]').on('click', function(){
    uploadMode = 'addButtonIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('#countdown-settings-dialog .reselect-icon').on('click', function(){
    uploadMode = 'reselectIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('#countdown-settings-dialog .scrolltop-icon').on('click', function(){
    uploadMode = 'scrolltopIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('#countdown-settings-dialog .scroll-to-icon').on('click', function(){
    uploadMode = 'smoothScrollingIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
});

$g('.select-product-link').on('click', function(){
    fontBtn = this;
    showDataTagsDialog('add-to-cart-products-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value);
    $g(this).closest('.link-picker-container').find('input').val('[product ID='+obj.id+']').trigger('input');
})

document.querySelectorAll('#add-to-cart-products-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.getProductsList').then(function(json){
        let ul = modal.querySelector('ul');
        json.list.forEach(function(el){
            let li = document.createElement('li'),
                price = json.currency.position ? el.price+' '+json.currency.symbol : json.currency.symbol+' '+el.price,
                html = '<span class="ba-item-thumbnail"';
            if (el.image) {
                html += ' style="background-image: url('+el.image+')"';
            }
            html += '>';
            if (!el.image) {
                html += '<i class="zmdi zmdi-label"></i>';
            }
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            html += '<span class="picker-item-price">'+price+'</span>';
            li.dataset.value = JSON.stringify(el);
            li.dataset.id = el.id;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

$g('.submit-button-onsubmit-select').on('customAction', function(){
    app.edit.onsubmit.action = this.querySelector('input[type="hidden"]').value;
    $g('.thank-you-onsubmit-options, .redirect-onsubmit-options').hide();
    $g('.'+app.edit.onsubmit.action+'-onsubmit-options').css('display', '');
    app.addHistory();
});
$g('.thank-you-onsubmit-options input').on('input', function(){
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.edit.onsubmit.message = this.value.trim();
        app.addHistory();
    }.bind(this), 400);
});
$g('.redirect-onsubmit-options input').on('input', function(){
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        app.edit.onsubmit.redirect = this.value.trim();
        app.addHistory();
    }.bind(this), 400);
});

function removeIcon()
{
    var i = app.editor.document.getElementById(app.editor.app.edit);
    i = i.querySelector('a i');
    if (i) {
        i.parentNode.removeChild(i);
    }
}

if (!app.modules.calendar) {
    app.loadModule('calendar');
}
app.modules.countdownEditor = true;
app.countdownEditor();