/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.testimonialsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    $g('#testimonials-settings-dialog .active').removeClass('active');
    $g('#testimonials-settings-dialog a[href="#testimonials-general-options"]').parent().addClass('active');
    $g('#testimonials-general-options').addClass('active');
    let modal = $g('#testimonials-settings-dialog')
    drawTestimonialsSortingList();
    $g('#testimonials-settings-dialog [data-group="slideset"]').each(function(){
        value = app.getValue('slideset', this.dataset.option);
        if (this.type == 'checkbox') {
            this.checked = value;
        } else {
            this.value = value;
        }
    });
    $g('#testimonials-settings-dialog .select-testimonial-layout input[type="hidden"]').val(app.edit.layout);
    value = $g('#testimonials-settings-dialog .select-testimonial-layout li[data-value="'+app.edit.layout+'"]').text().trim();
    $g('#testimonials-settings-dialog .select-testimonial-layout input[readonly]').val(value);
    value = app.edit.desktop.view.dots;
    $g('#testimonials-settings-dialog [data-group="view"][data-option="dots"]')[0].checked = value;
    value = app.edit.desktop.view.arrows;
    $g('#testimonials-settings-dialog [data-group="view"][data-option="arrows"]')[0].checked = value;
    app.setDefaultState('#testimonials-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#testimonials-settings-dialog .margin-settings-group');
    app.setDefaultState('#testimonials-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#testimonials-settings-dialog .padding-settings-group');
    setDisableState('#testimonials-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    setPresetsList($g('#testimonials-settings-dialog'));
    app.setDefaultState('#testimonials-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#testimonials-layout-options .shadow-settings-group');
    app.setDefaultState('#testimonials-layout-options .border-settings-group', 'default');
    app.setBorderValues('#testimonials-layout-options .border-settings-group');
    $g('#testimonials-settings-dialog .select-testimonials-options input[type="hidden"]').val('testimonial');
    $g('#testimonials-settings-dialog .select-testimonials-options input[readonly]').val(gridboxLanguage['TESTIMONIAL']);
    showTestimonialsDesign('testimonial', $g('#testimonials-settings-dialog .select-testimonials-options'));
    value = app.getValue('background', 'color');
    updateInput($g('#testimonials-settings-dialog input[data-option="color"][data-group="background"]'), value);
    $g('#testimonials-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        $g('#testimonials-settings-dialog').modal();
    }, 150);
}

app.testimonialsCallback = function(){
    app.sectionRules();
    var object = {
        data : app.edit,
        selector : app.editor.app.edit
    }
    app.editor.app.checkModule('initItems', object);
}

function drawTestimonialsSortingList()
{
    var ul = app.editor.document.querySelector(app.selector+' ul'),
        modal = $g('#testimonials-settings-dialog')
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    for (let ind in app.edit.slides) {
        let slide = app.edit.slides[ind],
            li = ul.querySelector('li.item:nth-child('+ind+')')
            obj = {
                parent: li,
                unpublish: li.classList.contains('ba-unpublished-html-item'),
                image: slide.image,
                link: slide.link,
                title: li.querySelector('.ba-testimonials-name').textContent.trim(),
                testimonial: li.querySelector('.ba-testimonials-testimonial').textContent.trim(),
                caption: li.querySelector('.ba-testimonials-caption').textContent.trim(),
            }
        sortingList[ind] = obj;
        container.append(addSortingList(obj, ind));
    }
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function showTestimonialsDesign(search, select)
{
    var parent = $g(select).closest('.tab-pane');
    parent.children().not('.slideshow-design-group, .testimonials-background-options').hide();
    switch (search) {
        case 'name' :
        case 'testimonial' :
        case 'caption' :
            parent.find('.slideshow-typography-options').css('display', '').find('[data-subgroup="typography"]').attr('data-group', search);
            parent.find('.slideshow-typography-options .typography-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.slideshow-typography-options .typography-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'arrows' :
            parent.find('.colors-settings-group').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-arrows-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-shadow-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.colors-settings-group, .testimonials-arrows-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.colors-settings-group, .testimonials-arrows-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'dots' :
            parent.find('.testimonials-border-options').css('display', '').find('[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-dots-options').css('display', '').find('input[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-dots-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.testimonials-dots-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'icon' :
            parent.find('.testimonials-icon-options').css('display', '').find('input[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-icon-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.testimonials-icon-options').removeClass('ba-active-options');
            }, 1);
            break;
        case 'image' :
            parent.find('.testimonials-image-options').css('display', '').find('input[data-subgroup]').attr('data-group', search);
            parent.find('.testimonials-image-options').addClass('ba-active-options');
            setTimeout(function(){
                parent.find('.testimonials-image-options').removeClass('ba-active-options');
            }, 1);
            break;
    }
    let object = app.getValue(search);
    if (search == 'arrows') {
        app.editor.app.cssRules.prepareColors(object);
    }
    for (var ind in object) {
        if (typeof(object[ind]) == 'object') {
            if (ind == 'typography') {
                app.setTypography(parent.find('.slideshow-typography-options .typography-options'), search, ind);
            } else if (ind == 'colors') {
                app.setDefaultState(parent.find('.colors-settings-group'), 'default');
                app.setColorsValues(parent.find('.colors-settings-group'));
            } else if (ind == 'shadow') {
                app.setDefaultState(parent.find('.shadow-settings-group'), 'default');
                app.setShadowValues(parent.find('.shadow-settings-group'));
            } else if (ind == 'border') {
                app.setDefaultState(parent.find('.border-settings-group'), 'default');
                app.setBorderValues(parent.find('.border-settings-group'));
            } else {
                for (var key in object[ind]) {
                    var input = parent.find('[data-group="'+search+'"][data-option="'+key+'"][data-subgroup="'+ind+'"]');
                    if (input.attr('data-type') == 'color') {
                        updateInput(input, object[ind][key]);
                    } else if (input.attr('type') == 'number' || input.attr('type') == 'text') {
                        app.setLinearInput(input, object[ind][key]);
                    } else {
                        input.val(object[ind][key]);
                        if (input.attr('type') == 'hidden') {
                            var text = input.closest('.ba-custom-select').find('li[data-value="'+object[ind][key]+'"]').text().trim();
                            input.closest('.ba-custom-select').find('input[readonly]').val(text);
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
            } else {
                input.val(object[ind]);
                if (input.attr('type') == 'hidden') {
                    var text = input.closest('.ba-custom-select').find('li[data-value="'+object[ind]+'"]').text().trim();
                    input.closest('.ba-custom-select').find('input[readonly]').val(text);
                }
            }
        }
    }
}

$g('#testimonials-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1,
        obj = sortingList[key * 1],
        modal = $g('#testimonial-item-edit-modal');
    $g('#apply-testimonial-item').attr('data-key', key);
    modal.find('.image-item-testimonial').val(obj.testimonial);
    modal.find('.image-item-upload-image').val(obj.image);
    modal.find('.image-item-name').val(obj.title);
    modal.find('.image-item-caption').val(obj.caption);
    modal.find('.image-item-link').val(obj.link);
    modal.modal();
});

$g('#testimonials-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    var modal = $g('#testimonial-item-edit-modal');
    $g('#apply-testimonial-item').attr('data-key', -1);
    modal.find('.image-item-testimonial, .image-item-upload-image, .image-item-name').val('');
    modal.find('.image-item-caption, .image-item-link').val('');
    modal.modal();
});

$g('#apply-testimonial-item').on('click', function(){
    let key = this.dataset.key * 1,
        li = key != -1 ? sortingList[key].parent : null,
        modal = $g('#testimonial-item-edit-modal'),
        obj = {
            testimonial : modal.find('.image-item-testimonial').val().trim(),
            image: modal.find('.image-item-upload-image').val(),
            title: modal.find('.image-item-name').val().trim(),
            caption: modal.find('.image-item-caption').val().trim(),
            link: modal.find('.image-item-link').val().trim(),
        };
    if (key == -1) {
        let str = '<li class="item">'+
                '<div class="testimonials-wrapper">'+
                    '<div class="testimonials-icon-wrapper"><i class="ba-icons ba-icon-quote"></i></div>'+
                    '<div class="ba-testimonials-img"><div class="testimonials-img"></div></div>'+
                    '<div class="testimonials-info">'+
                        '<div class="testimonials-icon-wrapper"><i class="ba-icons ba-icon-quote"></i></div>'+
                        '<div class="testimonials-testimonial-wrapper"><div class="ba-testimonials-testimonial">'+
                        '</div></div>'+
                    '</div>'+
                    '<div class="testimonials-title-wrapper">'+
                        '<div class="testimonials-name-wrapper"><span class="ba-testimonials-name"></span></div>'+
                        '<div class="testimonials-caption-wrapper"><span class="ba-testimonials-caption"></span></div>'+
                    '</div>'+
                 '</div>'+
             '</li>';
        li = app.editor.$g(app.selector+' .slideshow-content').append(str).find('li').last()[0];
        key = 1;
        for (var ind in app.edit.slides) {
            key++;
        }
        app.edit.slides[key] = {
            image: '',
            link: ''
        }
    }
    let img = li.querySelector('.testimonials-img'),
        caption = li.querySelector('.ba-testimonials-caption');
    app.edit.slides[key].image = obj.image;
    app.edit.slides[key].link = obj.link;
    if (!obj.image && img) {
        img.parentNode.removeChild(img);
    } else if (obj.image && !img) {
        li.querySelector('.ba-testimonials-img').innerHTML = '<div class="testimonials-img"></div>';
    }
    if (obj.link && caption.localName != 'a') {
        caption.parentNode.innerHTML = '<a class="ba-testimonials-caption" target="_blank"></a>';
        caption = li.querySelector('.ba-testimonials-caption');
    } else if (!obj.link && caption.localName == 'a') {
        caption.parentNode.innerHTML = '<span class="ba-testimonials-caption"></span>';
        caption = li.querySelector('.ba-testimonials-caption');
    }
    if (obj.link) {
        caption.setAttribute('href', obj.link);
    }
    li.querySelector('.ba-testimonials-name').textContent = obj.title;
    li.querySelector('.ba-testimonials-testimonial').textContent = obj.testimonial;
    caption.textContent = obj.caption;
    drawTestimonialsSortingList();
    app.testimonialsCallback();
    app.addHistory();
    modal.modal('hide');
});

function copyTestimonialsSortingItem(keys)
{
    let slides = {},
        i = 1;
    for (let ind in app.edit.slides) {
        slides[i++] = app.edit.slides[ind];
        if (keys.indexOf(ind * 1) != -1) {
            let obj = $g.extend({}, app.edit.slides[ind]),
                li = sortingList[ind].parent,
                clone = li.cloneNode(true);
            $g(li).after(clone);
            slides[i++] = obj;
        }
    }
    app.edit.slides = slides;
    drawTestimonialsSortingList();
    app.testimonialsCallback();
    app.addHistory();
}

$g('#testimonials-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
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
    copyTestimonialsSortingItem(array);
});

$g('#testimonials-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#testimonials-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1;
    copyTestimonialsSortingItem([key]);
});

$g('#testimonials-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#testimonials-settings-dialog [data-group="slideset"]').on('change input', function(){
    var option = this.dataset.option,
        value = this.value;
    if (this.type == 'checkbox') {
        value = this.checked;
    } else if (value == '') {
        value = app.getValue('slideset', option);
    }
    app.setValue(value, 'slideset', option);
    app.testimonialsCallback();
    delay = setTimeout(function(){
        app.addHistory();
    }, 300);
});

$g('#testimonials-settings-dialog .select-testimonial-layout').on('customAction', function(){
    value = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector+' ul.ba-testimonials').removeClass(app.edit.layout).addClass(value);
    app.edit.layout = value;
    app.testimonialsCallback();
    app.addHistory();
});

$g('.select-testimonials-options').on('customAction', function(){
    value = this.querySelector('input[type="hidden"]').value;
    showTestimonialsDesign(value, $g('#testimonials-settings-dialog .select-testimonials-options'));
});

app.modules.testimonialsEditor = true;
app.testimonialsEditor();