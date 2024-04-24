/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.itemEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#item-settings-dialog');
    $g('#item-settings-dialog .active').removeClass('active');
    $g('#item-settings-dialog a[href="#item-general-options"]').parent().addClass('active');
    $g('#item-general-options').addClass('active');
    app.setDefaultState('#item-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#item-layout-options .margin-settings-group');
    app.positioning.hasWidth = app.edit.type != 'logo';
    app.positioning.setValues(modal);
    if (app.edit.type == 'logo') {
        $g('#item-settings-dialog .logo-options').css('display', '');
    }
    if (app.edit.type == 'logo' || app.edit.type == 'simple-gallery' || app.edit.type == 'field-simple-gallery'
        || app.edit.type == 'product-gallery') {
        $g('a[href="#item-design-options"]').parent().css('display', '');
    } else {
        $g('a[href="#item-design-options"]').parent().hide();
    }
    $g('#item-settings-dialog .reselect-module').removeAttr('title');
    if (app.edit.type == 'modules') {
        $g('#item-settings-dialog .modules-options').css('display', '');
        $g('#item-settings-dialog .reselect-module').val('module ID='+app.edit.integration);
    } else if (app.edit.type == 'forms') {
        $g('#item-settings-dialog .modules-options').css('display', '');
        $g('#item-settings-dialog .reselect-module').val('forms ID='+app.edit.integration);
    } else if (app.edit.type == 'gallery') {
        value = 'gallery ID='+app.edit.integration;
        $g('#item-settings-dialog .modules-options').css('display', '');
        $g('#item-settings-dialog .reselect-module').val(value).attr('title', value);
    } else {
        $g('#item-settings-dialog .modules-options').hide();
    }
    if (app.edit.type == 'simple-gallery') {
        if (!app.edit.desktop.overlay) {
            app.edit.desktop.overlay = {
                type: 'color',
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
            app.sectionRules();
        }
        if (!app.edit.tag) {
            app.edit.tag = 'h3';
        }
        value = app.getValue('overlay', 'effect', 'gradient');
        $g('#item-settings-dialog .overlay-linear-gradient').hide();
        $g('#item-settings-dialog .overlay-'+value+'-gradient').css('display', '');
        $g('#item-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="hidden"]').val(value);
        value = $g('#item-settings-dialog .overlay-gradient-options .gradient-effect-select li[data-value="'+value+'"]').text().trim();
        $g('#item-settings-dialog .overlay-gradient-options .gradient-effect-select input[type="text"]').val(value);
        value = app.getValue('overlay', 'type');
        $g('#item-settings-dialog .overlay-color-options, .overlay-gradient-options').hide();
        $g('#item-settings-dialog .overlay-'+value+'-options').css('display', '');
        $g('#item-settings-dialog .background-overlay-select input[type="hidden"]').val(value);
        value = $g('#item-settings-dialog .background-overlay-select li[data-value="'+value+'"]').text().trim();
        $g('#item-settings-dialog .background-overlay-select input[type="text"]').val(value);
        $g('#item-settings-dialog .slideshow-style-custom-select input[type="hidden"]').val('title');
        $g('#item-settings-dialog .slideshow-style-custom-select input[readonly]').val(gridboxLanguage['TITLE']);
        $g('#item-settings-dialog .select-title-html-tag input[type="hidden"]').val(app.edit.tag);
        $g('#item-settings-dialog .select-title-html-tag input[readonly]').val(app.edit.tag.toUpperCase());
        showSlideshowDesign('title', $g('#item-settings-dialog .slideshow-style-custom-select'));
        getSimpleSortingList();
    }
    if (app.edit.type == 'logo') {
        $g('#item-settings-dialog [data-option="image"]').val(app.edit.image);
        $g('#item-settings-dialog [data-option="alt"]').val(app.edit.alt);
        $g('#item-settings-dialog [data-option="align"].active').removeClass('active');
        $g('#item-settings-dialog [data-option="align"][data-value="'+app.edit.align+'"]').addClass('active');
        value = app.getValue('width');
        app.setLinearInput(modal.find('.image-width input[data-option="width"]'), value);
        $g('#item-settings-dialog [data-option="text-align"].active').removeClass('active');
        value = app.getValue('text-align');
        $g('#item-settings-dialog [data-option="text-align"][data-value="'+value+'"]').addClass('active');
        $g('#item-settings-dialog [data-option="link"]').val(app.edit.link.link);
    } else if (app.edit.type == 'simple-gallery' || app.edit.type == 'field-simple-gallery' || app.edit.type == 'product-gallery') {
        setPresetsList($g('#item-settings-dialog'));
        if (!app.edit.desktop.border) {
            app.edit.desktop.border = {
                "color" : "@border",
                "radius" : "0",
                "style" : "solid",
                "width" : "0"
            }
        }
        let options = app.edit.type == 'product-gallery' ? 'field-simple-gallery' : app.edit.type,
            str = '.'+options+'-options:not(.border-settings-group):not(.margin-settings-group)'+
                ' [data-option]:not([data-subgroup="typography"])';
        $g(str).each(function(){
            if (app.edit[this.dataset.group]) {
                value = app.edit[this.dataset.group][this.dataset.option];
            } else if (this.dataset.subgroup) {
                value = app.getValue(this.dataset.group, this.dataset.option, this.dataset.subgroup);
            } else if (this.dataset.group) {
                value = app.getValue(this.dataset.group, this.dataset.option);
            } else {
                value = app.getValue(this.dataset.option);
            }
            if (this.dataset.group == '') {

            } else  if (this.type == 'checkbox') {
                this.checked = value;
            } else if (this.dataset.type == 'color') {
                updateInput($g(this), value);
            } else if (this.type == 'hidden') {
                this.value = value;
                value = this.parentNode.querySelector('li[data-value="'+value+'"]').textContent.trim();
                this.previousElementSibling.value = value;
            } else {
                app.setLinearInput($g(this), value);
            }
        });
    }
    if (app.edit.type == 'field-simple-gallery' || app.edit.type == 'product-gallery' || app.edit.type == 'simple-gallery') {
        if (!('layout' in app.edit)) {
            app.edit.layout = '';
        }
        if (!app.edit.desktop.border) {
            app.edit.desktop.border = {
                "color" : "@border",
                "radius" : "0",
                "style" : "solid",
                "width" : "0"
            }
        }
        app.setDefaultState('#item-settings-dialog .border-settings-group', 'default');
        app.setBorderValues('#item-settings-dialog .border-settings-group');
        $g('#item-settings-dialog .simple-gallery-layout-select input[type="hidden"]').val(app.edit.layout);
        value = $g('#item-settings-dialog .simple-gallery-layout-select li[data-value="'+app.edit.layout+'"]').text().trim();
        $g('#item-settings-dialog .simple-gallery-layout-select input[type="text"]').val(value);
        if (app.edit.layout) {
            $g('#item-settings-dialog .simple-gallery-layout-select').closest('.ba-settings-item').nextAll().hide();
        } else {
            $g('#item-settings-dialog .simple-gallery-layout-select').closest('.ba-settings-item').nextAll().css('display', '');
        }
    }
    if (app.edit.type == 'field-simple-gallery' || app.edit.type == 'product-gallery') {
        $g('#item-settings-dialog input[data-option="label"]').val(app.edit.label);
        $g('#item-settings-dialog input[data-option="description"][data-group="options"]').val(app.edit.options.description);
        $g('#item-settings-dialog input[data-option="required"]').prop('checked', app.edit.required);
        $g('#item-settings-dialog .select-field-upload-source input[type="hidden"]').val(app.edit.options.source);
        value = app.edit.options.source == 'desktop' ? gridboxLanguage['DESKTOP'] : gridboxLanguage['MEDIA_MANAGER'];
        $g('#item-settings-dialog .select-field-upload-source input[type="text"]').val(value);
        $g('#item-settings-dialog .desktop-source-filesize input').val(app.edit.options.size);
        if (app.edit.options.source == 'desktop') {
            $g('#item-settings-dialog .desktop-source-filesize').css('display', '');
        } else {
            $g('#item-settings-dialog .desktop-source-filesize').hide();
        }
    }
    setDisableState('#item-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#item-settings-dialog').attr('data-edit', app.edit.type);
    if (app.edit.type == 'product-gallery') {
        $g('#item-settings-dialog').attr('data-edit', 'field-simple-gallery');
    }
    if (app.edit.type == 'vk-comments') {
        var attach = app.edit.options.attach.split(',');
        $g('.vk-comments-attach').each(function(){
            if (attach[0] == '*' || attach.indexOf(this.dataset.option)) {
                this.checked = true;
            } else {
                this.checked = false;
            }
        });
        $g('.vk-comments-autopublish').prop('checked', Boolean(app.edit.options.autoPublish));
        app.setLinearInput($g('.vk-comments-limit'), app.edit.options.limit);
        $g('.vk-comments-options').css('display', '');
    } else {
        $g('.vk-comments-options').hide();
    }
    if (app.edit.type == 'facebook-comments') {
        app.setLinearInput($g('.facebook-comments-limit'), app.edit.options.limit);
        $g('.facebook-comments-options').css('display', '');
    } else {
        $g('.facebook-comments-options').hide();
    }
    if (app.edit.type == 'hypercomments') {
        $g('.hypercomments-options').css('display', '');
    } else {
        $g('.hypercomments-options').hide();
    }
    setTimeout(function(){
        $g('#item-settings-dialog').modal();
    }, 150);
}

function getSimpleSortingList()
{
    let modal = $g('#item-settings-dialog'),
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    app.editor.document.querySelectorAll(app.selector+' .ba-instagram-image').forEach(function(image, i){
        let img = image.querySelector('img'),
            array = img.dataset.src.split('/')
            obj = {
                parent: image,
                unpublish: image.classList.contains('ba-unpublished-html-item'),
                image: img.dataset.src,
                alt: img.alt,
                title: array[array.length - 1],
                caption: {
                    title: '',
                    description: ''
                }
            }
        if (image.querySelector('.ba-simple-gallery-title')) {
            obj.caption.title = image.querySelector('.ba-simple-gallery-title').textContent.trim();
            obj.caption.description = image.querySelector('.ba-simple-gallery-description').innerHTML.trim();
        }
        sortingList[i] = obj;
        modal.find('.sorting-container').append(addSortingList(obj, i));
    });
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copySimpleGalleryItems(keys)
{
    keys.forEach(function(key){
        let image = sortingList[key].parent,
            clone = image.cloneNode(true);
        $g(image).after(clone);
    });
    getSimpleSortingList();
    app.addHistory();
}

function reloadModules(obj)
{
    app.edit.integration = obj.selector;
    if (app.edit.type == 'modules') {
        $g('#item-settings-dialog .reselect-module').val('module ID='+app.edit.integration);
    } else {
        $g('#item-settings-dialog .reselect-module').val(app.edit.type+' ID='+app.edit.integration);
    }
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.reloadModules",
        data: {
            type : app.edit.type,
            id : obj.selector
        },
        complete: function(msg){
            app.editor.$g(app.selector).find('.integration-wrapper').html(msg.responseText);
            if ('initGalleries' in app.editor) {
                app.editor.initGalleries();
            }
        }
    });
}

$g('#item-settings-dialog .reselect-module').on('click', function(){
    if (app.edit.type == 'modules') {
        checkIframe($g('#modules-list-modal'), 'modules');
    } else {
        checkIframe($g('#'+app.edit.type+'-list-modal'), 'ba'+app.edit.type);
    }
});

$g('.vk-comments-attach').on('change', function(){
    var attach = [],
        str = '';
    $g('.vk-comments-attach').each(function(){
        if (this.checked) {
            attach.push(this.dataset.option);
        }
    });
    str = attach.join(',');
    if (str == 'graffiti,photo,audio,video,link') {
        str = '*';
    }
    app.edit.options.attach = str;
    app.editor.app.initvkcomments(app.edit);
    app.addHistory();
});

$g('.vk-comments-autopublish').on('change', function(){
    app.edit.options.autoPublish = Number(this.checked);
    app.editor.app.initvkcomments(app.edit);
    app.addHistory();
});

$g('.vk-comments-limit').on('input', function(){
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.options.limit = Number($g('.vk-comments-limit').val().trim());
        app.editor.app.initvkcomments(app.edit);
        app.addHistory();
    }, 300);
});

$g('.facebook-comments-limit').on('input', function(){
    clearTimeout(delay);
    delay = setTimeout(function(){
        app.edit.options.limit = Number($g('.facebook-comments-limit').val().trim());
        app.editor.app.initfacebookcomments(app.edit);
        app.addHistory();
    }, 300);
});

$g('#item-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    uploadMode = 'addSimpleImages';
    checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
});

$g('#item-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null;
    app.itemDelete = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key;
            app.itemDelete.push(key);
        }
    });
    app.checkModule('deleteItem');
});

$g('#item-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null,
        array = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key * 1;
            array.push(key);
        }
    });
    copySimpleGalleryItems(array);
});

$g('#item-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#item-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var ind = this.closest('.sorting-item').dataset.key * 1;
    copySimpleGalleryItems([ind]);
});

$g('#item-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    var ind = this.closest('.sorting-item').dataset.key * 1;
    $g('#apply-simple-gallery-item').attr('data-index', ind);
    $g('.simple-gallery-upload-image').val(sortingList[ind].image);
    $g('.simple-gallery-alt').val(sortingList[ind].alt);
    $g('.simple-gallery-title').val(sortingList[ind].caption.title);
    $g('.simple-gallery-description').val(sortingList[ind].caption.description);
    $g('#simple-gallery-item-edit-modal').modal();
});

$g('.simple-gallery-upload-image').on('click', function(){
    uploadMode = 'reselectSimpleImage';
    fontBtn = this;
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('.simple-gallery-caption-effect-select').on('customAction', function(){
    app.editor.$g('#'+app.editor.app.edit+' .instagram-wrapper').removeClass(app.edit.desktop.animation.effect);
    app.edit.desktop.animation.effect = this.querySelector('input[type="hidden"]').value;
    app.editor.$g('#'+app.editor.app.edit+' .instagram-wrapper').addClass(app.edit.desktop.animation.effect);
    app.addHistory();
});

$g('#apply-simple-gallery-item').on('click', function(event){
    event.preventDefault();
    let ind = this.dataset.index * 1,
        parent = sortingList[ind].parent,
        image = parent.querySelector('img'),
        title = parent.querySelector('.ba-simple-gallery-title'),
        description = parent.querySelector('.ba-simple-gallery-description'),
        obj = {
            src: $g('.simple-gallery-upload-image').val(),
            alt: $g('.simple-gallery-alt').val().trim(),
            title: $g('.simple-gallery-title').val().trim(),
            description: $g('.simple-gallery-description').val().trim()
        }
    
    parent.style.backgroundImage = 'url('+JUri+obj.src+')';
    image.src = JUri+obj.src;
    image.dataset.src = obj.src;
    image.alt = obj.alt;
    if (!title) {
        let caption = '<div class="ba-simple-gallery-image"></div><div class="ba-simple-gallery-caption">'+
            '<div class="ba-caption-overlay"></div><'+app.edit.tag+' class="ba-simple-gallery-title"></'+app.edit.tag+'>'+
            '<div class="ba-simple-gallery-description"></div></div>';
        $g(parent).append(caption);
        title = parent.querySelector('.ba-simple-gallery-title');
        description = parent.querySelector('.ba-simple-gallery-description');
    }
    title.classList[!obj.title ? 'add' : 'remove']('empty-content');
    description.classList[!obj.description ? 'add' : 'remove']('empty-content');
    title.textContent = obj.title;
    description.innerHTML = obj.description;
    getSimpleSortingList();
    app.addHistory();
    $g('#simple-gallery-item-edit-modal').modal('hide');
});

$g('#item-settings-dialog .simple-gallery-layout-select').on('customAction', function(){
    app.editor.$g(app.selector+' .instagram-wrapper').removeClass(app.edit.layout);
    app.edit.layout = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' .instagram-wrapper').addClass(app.edit.layout);
    app.editor.setGalleryMasonryHeight(app.editor.app.edit);
    if (app.edit.layout) {
        $g('#item-settings-dialog .simple-gallery-layout-select').closest('.ba-settings-item').nextAll().hide();
    } else {
        $g('#item-settings-dialog .simple-gallery-layout-select').closest('.ba-settings-item').nextAll().css('display', '');
    }
    app.addHistory();
});

app.modules.itemEditor = true;
app.itemEditor();