/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.contentSliderEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#content-slider-settings-dialog');
    $g('#content-slider-settings-dialog .active').removeClass('active');
    $g('#content-slider-settings-dialog a[href="#content-slider-general-options"]').parent().addClass('active');
    $g('#content-slider-general-options').addClass('active');
    var value;
    drawContentSliderSortingList();
    setPresetsList($g('#content-slider-settings-dialog'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    $g('#content-slider-settings-dialog [data-group="slideshow"]').each(function(){
        value = app.edit.slideshow[this.dataset.option];
        if (this.type == 'checkbox') {
            this.checked = value;
        } else {
            this.value = value;
        }
    });
    value = app.getValue('view', 'height');
    app.setLinearInput(modal.find('[data-group="view"][data-option="height"]'), value);
    value = app.getValue('view', 'fullscreen');
    $g('#content-slider-settings-dialog [data-option="fullscreen"]')[0].checked = value;
    $g('#content-slider-settings-dialog .slideshow-animation-select input[type="hidden"]').val(app.edit.animation);
    value = $g('#content-slider-settings-dialog .slideshow-animation-select li[data-value="'+app.edit.animation+'"]').text().trim();
    $g('#content-slider-settings-dialog .slideshow-animation-select input[readonly]').val(value);
    value = app.getValue('view', 'dots');
    $g('#content-slider-settings-dialog [data-group="view"][data-option="dots"]')[0].checked = value;
    value = app.getValue('view', 'arrows');
    $g('#content-slider-settings-dialog [data-group="view"][data-option="arrows"]')[0].checked = value;
    app.setDefaultState('#content-slider-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#content-slider-settings-dialog .margin-settings-group');
    app.setDefaultState('#content-slider-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#content-slider-settings-dialog .padding-settings-group');
    app.setDefaultState('#content-slider-layout-options .border-settings-group', 'default');
    app.setBorderValues('#content-slider-layout-options .border-settings-group');
    app.setDefaultState('#content-slider-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#content-slider-layout-options .shadow-settings-group');
    setDisableState('#content-slider-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#content-slider-settings-dialog .slideshow-design-group input[type="hidden"]').val('arrows');
    $g('#content-slider-settings-dialog .slideshow-design-group input[readonly]').val(app._('ARROWS'));
    showSlideshowDesign('arrows', $g('#content-slider-settings-dialog .slideshow-style-custom-select'));
    $g('#content-slider-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function getContentSliderObject(obj)
{
    var object = $g.extend(true, {}, obj.desktop);
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

    return object;
}

function drawContentSliderSortingList()
{
    let modal = $g('#content-slider-settings-dialog'),
        list = app.editor.document.querySelectorAll(app.selector+' > .slideshow-wrapper > ul > .slideshow-content > li'),
        container = $g('#content-slider-settings-dialog .sorting-container').empty();
    sortingList = {};
    for (var ind in app.edit.slides) {
    	let li = list[ind * 1 - 1],
            slide = app.edit.slides[ind],
            data = getContentSliderObject(slide),
            type = data.background.type,
            obj = {
                parent: li,
                unpublish: li.classList.contains('ba-unpublished-html-item'),
                type: type,
                image: data.background.image.image,
                title: slide.title,
                data: data,
                link: slide.link
            }
        sortingList[ind] = obj;
        container.append(addSortingList(obj, ind));
    }
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copyContentSlide()
{
    let slides = {},
        title = 0,
        i = 1;
    for (var ind in app.edit.slides) {
        match = app.edit.slides[ind].title.match(/\d+/);
        if (match[0] * 1 > title) {
            title = match[0] * 1;
        }
    }
    app.itemDelete.forEach(function(key){
        let li = app.editor.$g(sortingList[key].parent),
            clone = li.clone(),
            column = li.find('> .ba-grid-column').last(),
            match;
        clone.find('> .ba-slideshow-img > *').each(function(){
            this.id = column.attr('id').match(/\d+/)[0] * 1 - 1;
        });
        clone.find('> .ba-grid-column').remove();
        clone.find('> .ba-slideshow-img').after(column);
        li.after(clone);
    });
    for (var ind in app.edit.slides) {
        slides[i++] = app.edit.slides[ind];
        if (app.itemDelete.indexOf(ind * 1) != -1) {
            var extend = $g.extend(true, {}, app.edit.slides[ind]);
            extend.title = 'Slide '+(++title);
            slides[i++] = extend;
        }
    }
    app.edit.slides = slides;
    app.sectionRules();
    var object = {
        data : app.edit,
        selector : app.editor.app.edit
    }
    app.editor.app.checkModule('initItems', object);
    drawContentSliderSortingList();
    if (app.pageStructure && app.pageStructure.visible) {
        app.pageStructure.updateStructure(true);
    }
}

$g('#content-slider-settings-dialog .slideshow-animation-select').on('customAction', function(){
    var value = $g(this).find('input[type="hidden"]').val(),
        item = app.editor.document.querySelector(app.selector+' > .slideshow-wrapper > ul');
    item.classList.remove(app.edit.animation);
    app.edit.animation = value;
    item.classList.add(app.edit.animation);
    app.addHistory();
});

$g('#content-slider-settings-dialog [data-group="slideshow"]').on('change input', function(){
    var value = this.value;
    if (this.type == 'checkbox') {
        value = this.checked;
    }
    app.edit.slideshow[this.dataset.option] = value;
    var object = {
        data : app.edit,
        selector : app.editor.app.edit
    }
    app.sectionRules();
    app.editor.app.checkModule('initItems', object);
    delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#content-slider-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    uploadMode = 'contentSliderAdd';
    checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
});

function contentSliderAdd(data)
{
    var ind = 0,
        match,
        title = 0;
    for (ind in app.edit.slides) {
        match = app.edit.slides[ind].title.match(/\d+/);
        if (match[0] * 1 > title) {
            title = match[0] * 1
        }
    }
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=editor.contentSliderAdd&tmpl=component",
        data: {
            ind : ind * 1 + 1,
            data : data,
            title : title * 1 + 1
        },
        complete: function(msg){
            var obj = JSON.parse(msg.responseText);
            app.editor.$g(app.selector+'  > .slideshow-wrapper > ul > .slideshow-content').append(obj.html);
            for (var ind in obj.slides) {
                app.edit.slides[ind] = obj.slides[ind];
            }
            var object = {
                    data : app.edit,
                    selector : app.editor.app.edit
                },
                id = app.editor.app.edit,
                array = [];
            for (var key in obj.items) {
                app.editor.app.items[key] = obj.items[key];
                app.editor.app.edit = key;
                app.sectionRules();
                var column = app.editor.$g('#'+key).closest('.ba-grid-column').attr('id');
                if (array.indexOf(column) === -1) {
                    array.push(column);
                }
            }
            for (var i = 0; i < array.length; i++) {
                app.editor.makeColumnSortable(app.editor.$g('#'+array[i]), 'column');
                app.editor.editItem(array[i]);
            }
            app.editor.app.checkModule('initItems', object);           
            app.editor.app.edit = id;
            app.sectionRules();
            drawContentSliderSortingList();
            if (app.pageStructure && app.pageStructure.visible) {
                app.pageStructure.updateStructure(true);
            }
            app.addHistory();
        }
    });
}

$g('#content-slider-settings-dialog .sorting-container').on('click', '.zmdi.zmdi-edit', function(){
    var key = this.closest('.sorting-item').dataset.key,
        obj = sortingList[key].data,
        modal = $g('#edit-content-slider-item-modal');
    value = obj.background.gradient.effect;
    modal.find('.background-linear-gradient').hide();
    modal.find('.background-'+value+'-gradient').css('display', '');
    modal.find('.gradient-options .content-slider-gradient-effect-select input[type="hidden"]').val(value);
    value = modal.find('.gradient-options .content-slider-gradient-effect-select li[data-value="'+value+'"]').text().trim();
    modal.find('.gradient-options .content-slider-gradient-effect-select input[type="text"]').val(value);
    value = obj.overlay.gradient.effect;
    modal.find('.overlay-linear-gradient').hide();
    modal.find('.overlay-'+value+'-gradient').css('display', '');
    modal.find('.overlay-gradient-options .content-slider-gradient-effect-select input[type="hidden"]').val(value);
    value = modal.find('.overlay-gradient-options .content-slider-gradient-effect-select li[data-value="'+value+'"]').text().trim();
    modal.find('.overlay-gradient-options .content-slider-gradient-effect-select input[type="text"]').val(value);
    modal.find('input[data-subgroup="gradient"][data-group="background"]').each(function(){
        value = obj.background.gradient[this.dataset.option];
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    value = obj.overlay.type;
    modal.find('.overlay-color-options, .overlay-gradient-options').hide();
    modal.find('.overlay-'+value+'-options').css('display', '');
    modal.find('.background-overlay-select input[type="hidden"]').val(value);
    value = modal.find('.background-overlay-select li[data-value="'+value+'"]').text().trim();
    modal.find('.background-overlay-select input[type="text"]').val(value);
    modal.find('input[data-subgroup="gradient"][data-group="overlay"]').each(function(){
        value = obj.overlay.gradient[this.dataset.option];
        if (!value && this.dataset.type == 'color') {
            value = obj.overlay.gradient[this.dataset.option] = '@bg-dark';
        }
        if (this.dataset.type == 'color') {
            updateInput($g(this), value);
        } else {
            app.setLinearInput($g(this), value);
        }
    });
    value = obj.background.color;
    updateInput(modal.find('input[data-option="color"][data-group="background"]'), value);
    value = obj.overlay.color;
    updateInput(modal.find('input[data-option="color"][data-group="overlay"]'), value);
    value = obj.overlay.type;
    modal.find('.overlay-color-options, .overlay-gradient-options').hide();
    modal.find('.overlay-'+value+'-options').css('display', '');
    modal.find('.content-slider-background-overlay-select input[type="hidden"]').val(value);
    value = modal.find('.content-slider-background-overlay-select li[data-value="'+value+'"]').text().trim();
    modal.find('.content-slider-background-overlay-select input[type="text"]').val(value);
    value = obj.background.image.image;
    modal.find('input[data-option="image"]').val(value);
    value = obj.background.image.attachment;
    modal.find('[data-option="attachment"]').val(value);
    value = modal.find('.attachment li[data-value="'+value+'"]').text().trim();
    modal.find('.attachment input[readonly]').val(value);
    value = obj.background.image.size;
    if (value == 'contain' || value == 'initial') {
        modal.find('.contain-size-options').show().addClass('ba-active-options');
        setTimeout(function(){
            modal.find('.contain-size-options').removeClass('ba-active-options');
        }, 1);
    } else {
        modal.find('.contain-size-options').hide();
    }
    modal.find('.backround-size input[type="hidden"]').val(value);
    value = modal.find('.backround-size li[data-value="'+value+'"]').text().trim();
    modal.find('.backround-size input[readonly]').val(value);
    value = obj.background.image.position;
    modal.find('[data-option="position"]').val(value);
    name = modal.find('.backround-position li[data-value="'+value+'"]').text().trim();
    modal.find('.backround-position input[readonly]').val(name);
    value = obj.background.image.repeat;
    modal.find('[data-option="repeat"]').val(value);
    name = modal.find('.backround-repeat li[data-value="'+value+'"]').text().trim();
    modal.find('.backround-repeat input[readonly]').val(name);
    modal.find('.video-select [data-option="video-type"]').val(obj.background.video.type);
    value = modal.find('.video-select li[data-value="'+obj.background.video.type+'"]').text().trim();
    modal.find('.video-select input[readonly]').val(value);
    modal.find('.video-select').trigger('customAction');
    modal.find('[data-option="id"]').val(obj.background.video.id);
    modal.find('[data-option="source"]').val(obj.background.video.source);
    modal.find('[data-option="start"]').val(obj.background.video.start);
    modal.find('[data-option="mute"]').prop('checked', Boolean(obj.background.video.mute * 1));
    modal.find('.video-quality [data-option="quality"]').val(obj.background.video.quality);
    value = modal.find('.video-quality li[data-value="'+obj.background.video.quality+'"]').text().trim();
    modal.find('.video-quality [readonly]').val(value);
    value = obj.background.type;
    modal.find('.background-options').find('> div').hide();
    modal.find('.'+value+'-options').css('display', '');
    modal.find('.content-slider-background-select input[type="hidden"]').val(value);
    value = modal.find('.content-slider-background-select li[data-value="'+value+'"]').text().trim();
    modal.find('.content-slider-background-select input[readonly]').val(value);
    modal.find('.slide-button-link').val(sortingList[key].link.href);
    modal.find('.slide-button-target-select input[type="hidden"]').val(sortingList[key].link.target);
    value = modal.find('.slide-button-target-select li[data-value="'+sortingList[key].link.target+'"]').text().trim();
    modal.find('.slide-button-target-select input[type="text"]').val(value);
    modal.find('.slide-button-attribute-select input[type="hidden"]').val(sortingList[key].link.download);
    value = modal.find('.slide-button-attribute-select li[data-value="'+sortingList[key].link.download+'"]').text().trim();
    modal.find('.slide-button-attribute-select input[type="text"]').val(value);
    modal.find('.slide-button-embed-code').val(sortingList[key].link.embed);
    $g('#apply-content-slider-item').attr('data-key', key);
    modal.modal();
});

$g('#apply-content-slider-item').on('click', function(){
    var key = this.dataset.key * 1,
        obj = getContentSliderObject(app.edit.slides[key]),
        modal = $g('#edit-content-slider-item-modal');
    value = modal.find('.gradient-options .content-slider-gradient-effect-select input[type="hidden"]').val();
    obj.background.gradient.effect = value;
    value = modal.find('.overlay-gradient-options .content-slider-gradient-effect-select input[type="hidden"]').val();
    obj.overlay.gradient.effect = value;
    modal.find('input[data-subgroup="gradient"][data-group="background"]').each(function(){
        obj.background.gradient[this.dataset.option] = this.dataset.type == 'color' ? this.dataset.rgba : this.value;
    });
    obj.overlay.type = modal.find('.background-overlay-select input[type="hidden"]').val();
    modal.find('input[data-subgroup="gradient"][data-group="overlay"]').each(function(){
        obj.overlay.gradient[this.dataset.option] = this.dataset.type == 'color' ? this.dataset.rgba : this.value;
    });
    obj.background.color = modal.find('input[data-option="color"][data-group="background"]').attr('data-rgba');
    obj.overlay.color = modal.find('input[data-option="color"][data-group="overlay"]').attr('data-rgba');
    obj.overlay.type = modal.find('.content-slider-background-overlay-select input[type="hidden"]').val();
    obj.background.image.image = modal.find('input[data-option="image"]').val();
    obj.background.image.attachment = modal.find('[data-option="attachment"]').val();
    obj.background.image.size = modal.find('.backround-size input[type="hidden"]').val();
    obj.background.image.position = modal.find('[data-option="position"]').val();
    obj.background.image.repeat = modal.find('[data-option="repeat"]').val();
    obj.background.video.type = modal.find('.video-select [data-option="video-type"]').val();
    obj.background.video.id = modal.find('[data-option="id"]').val();
    obj.background.video.source = modal.find('[data-option="source"]').val();
    obj.background.video.start = modal.find('[data-option="start"]').val();
    obj.background.video.mute = Number(modal.find('[data-option="mute"]').prop('checked'));
    obj.background.video.quality = modal.find('.video-quality [data-option="quality"]').val();
    obj.background.type = modal.find('.content-slider-background-select input[type="hidden"]').val();
    app.edit.slides[key][app.view] = obj;
    app.edit.slides[key].link.href = modal.find('.slide-button-link').val().trim();
    app.edit.slides[key].link.target = modal.find('.slide-button-target-select input[type="hidden"]').val();
    app.edit.slides[key].link.download = modal.find('.slide-button-attribute-select input[type="hidden"]').val();
    app.edit.slides[key].link.embed = modal.find('.slide-button-embed-code').val();
    app.editor.$g(sortingList[key].parent).find(' > a').remove();
    if (app.edit.slides[key].link.href) {
        var a = document.createElement('a');
        sortingList[key].parent.append(a);
        var str = '<a target="'+app.edit.slides[key].link.target+'" href="'+app.edit.slides[key].link.href+'"';
        if (app.edit.slides[key].link.download) {
            str += ' download';
        }
        str += ' '+app.edit.slides[key].link.embed+'></a>';
        var div = document.createElement(div);
        div.innerHTML = str;
        if (div.querySelector('a')) {
            a.replaceWith(div.querySelector('a'));
        }
        app.editor.app.buttonsPrevent();
    }
    var object = {
        data : app.edit,
        selector : app.editor.app.edit
    }
    app.sectionRules();
    app.editor.app.checkModule('initItems', object);
    app.addHistory();
    drawContentSliderSortingList();
    modal.modal('hide');
});

$g('#edit-content-slider-item-modal [data-option="image"][data-group="image"]').on('mousedown', function(){
    fontBtn = this;
    var modal = $g('#uploader-modal').attr('data-check', 'single');
    uploadMode = 'reselectSimpleImage';
    checkIframe(modal, 'uploader');
});

$g('.content-slider-background-overlay-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]'),
        parent = $g('.overlay-'+input.value+'-options');
    $g('.overlay-color-options, .overlay-gradient-options').hide();
    parent.css('display', '').addClass('ba-active-options');
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
});

$g('.content-slider-gradient-effect-select').on('customAction', function(){
    var input = this.querySelector('input[type="hidden"]'),
        parent = $g(this).closest('.modal');
    parent.find('.'+input.dataset.property+'-linear-gradient').hide();
    parent.find('.'+input.dataset.property+'-'+input.value+'-gradient').css('display', '');
});

$g('.content-slider-background-select').on('customAction', function(){
    var $this = $g(this),
        target = $this.find('input[type="hidden"]').val(),
        parent = $g('.'+target+'-options');
    $this.closest('.ba-options-group').find('.background-options').find('> div').hide();
    parent.css('display', '').addClass('ba-active-options');
    setTimeout(function(){
        parent.removeClass('ba-active-options');
    }, 1);
    app.addHistory();
});

$g('#content-slider-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    app.editItemId = app.editor.app.edit;
    app.editor.app.copyAction = 'copyContentSlide';
    app.itemDelete = [key];
    app.editor.app.checkModule('copyItem');
});

$g('.content-slider-item-title').on('input', function(){
    if (this.value.trim()) {
        $g('#apply-content-slider-item').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#apply-content-slider-item').removeClass('active-button').addClass('disable-button');
    }
});

$g('#content-slider-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#content-slider-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null;
    app.itemDelete = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key *1;
            app.itemDelete.push(key);
        }
    });
    app.editItemId = app.editor.app.edit;
    app.editor.app.copyAction = 'copyContentSlide';
    app.editor.app.checkModule('copyItem');
});

$g('#content-slider-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.items-list'),
        key = null;
    app.itemDelete = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            key = checkbox.closest('.sorting-item').dataset.key * 1;
            app.itemDelete.push(key);
        }
    });
    app.checkModule('deleteItem');
});

app.modules.contentSliderEditor = true;
app.contentSliderEditor();