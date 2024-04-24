/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.beforeAfterSliderEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#before-after-slider-settings-dialog').attr('data-edit', app.edit.type);
    $g('#before-after-slider-settings-dialog .active').removeClass('active');
    $g('#before-after-slider-settings-dialog a[href="#before-after-slider-general-options"]').parent().addClass('active');
    $g('#before-after-slider-general-options').addClass('active');
    setPresetsList(modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    setBeforeAfterSorting(modal);
    modal.find('.select-before-after-slider-direction').each(function(){
        this.querySelector('input[type="hidden"]').value = app.edit.direction;
        this.querySelector('input[type="text"]').value = app._(app.edit.direction.toUpperCase());
    });
    updateBeforeAfterDirection();
    app.setLinearInput(modal.find('input[data-option="start"]'), app.edit.start);
    modal.find('.before-after-label-mouseover').prop('checked', app.edit.mouseover);
    app.setDefaultState('#before-after-slider-layout-options .margin-settings-group', 'default');
    app.setMarginValues('#before-after-slider-layout-options .margin-settings-group');
    app.setDefaultState('#before-after-slider-layout-options .border-settings-group', 'default');
    app.setBorderValues('#before-after-slider-layout-options .border-settings-group');
    app.setDefaultState('#before-after-slider-layout-options .shadow-settings-group', 'default');
    app.setShadowValues('#before-after-slider-layout-options .shadow-settings-group');
    setDisableState('#before-after-slider-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    modal.find('.slideshow-style-custom-select').each(function(){
        this.querySelector('input[type="hidden"]').value = 'title';
        this.querySelector('input[type="text"]').value = app._('LABEL');
        showSlideshowDesign('title', this);
    });
    setTimeout(function(){
        modal.modal();
    }, 150);
}

app.beforeAfterCallback = function(){
    app.editor.$g(app.selector).each(function(){
        app.editor.app.beforeAfterSlider.set(this);
    });
}

function updateBeforeAfterDirection()
{
    let modal = $g('#before-after-slider-settings-dialog'),
        isH = app.edit.direction == 'horizontal',
        array = {
            left: {
                i: isH ? 'zmdi zmdi-border-top' : 'zmdi zmdi-border-left',
                text: isH ? app._('TOP') : app._('LEFT')
            },
            center: {
                i: isH ? 'zmdi zmdi-border-horizontal' : 'zmdi zmdi-border-vertical',
                text: isH ? app._('CENTER') : app._('CENTER')
            },
            right: {
                i: isH ? 'zmdi zmdi-border-bottom' : 'zmdi zmdi-border-right',
                text: isH ? app._('BOTTOM') : app._('RIGHT')
            }
        },
        obj = null,
        i = text = '';
    modal.find('.theme-typography-options label[data-option="text-align"]').each(function(){
        obj = array[this.dataset.value];
        this.querySelector('i').className = obj.i;
        this.querySelector('span').textContent = obj.text;
    });
}

function setBeforeAfterSorting(modal)
{
    let image = null,
        str = '',
        img = null;;
    for (let ind in app.edit.imgs) {
        img = app.edit.imgs[ind];
        image = !app.isExternal(img.src) ? JUri+img.src : img.src;
        str += '<div class="sorting-item"><div class="sorting-image">'+
            '<img src="'+image+'"></div><div class="sorting-title">'+img.label+
            '</div><div class="sorting-icons"><span><i class="zmdi zmdi-edit" data-key="'+
            ind+'"></i></span></div></div>';
    }
    modal.find('.sorting-container').html(str);
}

$g('#before-after-slider-settings-dialog .before-after-label-mouseover').on('change', function(){
    app.edit.mouseover = this.checked;
    app.editor.$g(app.selector).find('.ba-before-after-wrapper').attr('data-mouseover', this.checked ? 'enabled' : '');
    app.addHistory();
});

$g('.select-before-after-slider-direction').on('customAction', function(){
    app.edit.direction = this.querySelector('input[type="hidden"]').value;
    app.editor.$g(app.selector).find('.ba-before-after-wrapper').attr('data-direction', app.edit.direction);
    updateBeforeAfterDirection();
    app.beforeAfterCallback();
    app.addHistory();
});

$g('#before-after-slider-settings-dialog').find('.sorting-container').on('click', '.zmdi.zmdi-edit', function(){
    let obj = app.edit.imgs[this.dataset.key],
        modal = $g('#before-after-item-edit-modal');
    modal.find('.image-item-upload-image').val(obj.src);
    modal.find('.image-item-alt').val(obj.alt);
    modal.find('.image-item-label').val(obj.label);
    modal.find('#apply-before-after-item').attr('data-key', this.dataset.key);
    modal.modal();
});

$g('#apply-before-after-item').on('click', function(){
    let modal = $g('#before-after-item-edit-modal'),
        item = app.editor.$g(app.selector),
        obj = {
            "src": modal.find('.image-item-upload-image').val(),
            "alt": modal.find('.image-item-alt').val(),
            "label": modal.find('.image-item-label').val()
        },
        image = !app.isExternal(obj.src) ? JUri+obj.src : obj.src;
    app.edit.imgs[this.dataset.key] = obj;
    item.find('.ba-'+this.dataset.key+'-img').attr('src', image).attr('alt', obj.alt);
    item.find('.ba-'+this.dataset.key+'-label').text(obj.label);
    setBeforeAfterSorting($g('#before-after-slider-settings-dialog'));
    app.beforeAfterCallback();
    app.addHistory();
    modal.modal('hide');
});

app.modules.beforeAfterSliderEditor = true;
app.beforeAfterSliderEditor();