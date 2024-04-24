/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.socialIconsEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#social-icons-settings-dialog');
    $g('#social-icons-settings-dialog .active').removeClass('active');
    $g('#social-icons-settings-dialog a[href="#social-icons-general-options"]').parent().addClass('active');
    $g('#social-icons-general-options').addClass('active');
    var value = '';
    setPresetsList($g('#social-icons-settings-dialog'));
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    drawSocialIconsSorting();
    value = app.getValue('icon', 'text-align');
    $g('#social-icons-settings-dialog [data-option="text-align"][data-value="'+value+'"]').addClass('active');
    value = app.getValue('icon', 'size');
    app.setLinearInput(modal.find('[data-option="size"]'), value);
    app.setDefaultState('#social-icons-settings-dialog .padding-settings-group', 'default');
    app.setPaddingValues('#social-icons-settings-dialog .padding-settings-group');
    app.editor.app.cssRules.prepareColors(app.edit);
    app.setDefaultState('#social-icons-settings-dialog .colors-settings-group', 'default');
    app.setColorsValues('#social-icons-settings-dialog .colors-settings-group');
    app.setDefaultState('#social-icons-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#social-icons-settings-dialog .margin-settings-group');
    app.setDefaultState('#social-icons-settings-dialog .border-settings-group', 'default');
    app.setBorderValues('#social-icons-settings-dialog .border-settings-group');
    setDisableState('#social-icons-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    $g('#social-icons-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

function drawSocialIconsSorting()
{
    let modal = $g('#social-icons-settings-dialog'),
        wrapper = app.editor.document.querySelector(app.selector+' .ba-icon-wrapper'),
        container = modal.find('.sorting-container').empty();
    sortingList = {};
    for (var ind in app.edit.icons) {
        let obj = $g.extend(true, {}, app.edit.icons[ind]);
        ind *= 1;
        obj.parent = wrapper.querySelector('a:nth-child('+(ind + 1)+')');
        obj.unpublish = obj.parent.classList.contains('ba-unpublished-html-item');
        sortingList[ind] = obj;
        container.append(addSortingList(obj, ind));
    }
    modal.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function copySocialIconSorting(keys)
{
    let list = {},
        i = 0;
    for (let ind in app.edit.icons) {
        list[i++] = app.edit.icons[ind];
        if (keys.indexOf(ind * 1) != -1) {
            let obj = $g.extend({}, app.edit.icons[ind]),
                li = sortingList[ind].parent,
                clone = li.cloneNode(true);
            $g(li).after(clone);
            list[i++] = obj;
        }
    }
    app.edit.icons = list;
    app.editor.app.buttonsPrevent();
    drawSocialIconsSorting();
    app.addHistory();
}

$g('#social-icons-settings-dialog .sorting-container').on('click', '.delete-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key;
    app.itemDelete = [key];
    app.checkModule('deleteItem');
});

$g('#social-icons-settings-dialog .sorting-container').on('click', '.edit-sorting-item', function(){
    let key = this.closest('.sorting-item').dataset.key * 1;
    $g('#edit-social-icon-dialog input[data-property]').each(function(){
        var prop = this.dataset.property,
            value = sortingList[key].link[prop];
        if (typeof(value) == 'undefined') {
            this.value = sortingList[key].title;
            this.dataset.icon = sortingList[key].icon;
        } else {
            this.value = value;
        }
        if (this.type == 'hidden') {
            value = $g(this).closest('.ba-custom-select').find('li[data-value="'+value+'"]').text().trim();
            $g(this).closest('.ba-custom-select').find('input[type="text"]').val(value);
        }
    });
    $g('#social-icon-apply').removeClass('active-button').addClass('disable-button').attr('data-key', key);
    $g('#edit-social-icon-dialog').modal();
});

$g('#social-icons-settings-dialog .sorting-container').on('click', '.copy-sorting-item', function(){
    var key = this.closest('.sorting-item').dataset.key * 1;
    copySocialIconSorting([key]);
});

$g('#social-icons-settings-dialog .sorting-toolbar-action[data-action="copy"]').on('click', function(){
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
    copySocialIconSorting(array);
});

$g('#social-icons-settings-dialog .sorting-toolbar-action[data-action="delete"]').on('click', function(){
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

$g('#edit-social-icon-dialog input[data-property="icon"]').on('click', function(){
    uploadMode = 'reselectSocialIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    fontBtn = this;
    return false;
});

$g('#edit-social-icon-dialog input[data-property]').on('change input', function(){
    var link = $g('#edit-social-icon-dialog input[data-property="link"]').val().trim();
    if (link) {
        $g('#social-icon-apply').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#social-icon-apply').removeClass('active-button').addClass('disable-button');
    }
});

$g('#social-icon-apply').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        let modal = $g('#edit-social-icon-dialog'),
            key = this.dataset.key * 1,
            obj = app.edit.icons[key],
            prop = null,
            parent = sortingList[key].parent;
        modal.find('input[data-property]').each(function(){
            prop = this.dataset.property;
            if (prop == 'icon') {
                obj.title = this.value;
                obj.icon = this.dataset.icon;
            } else {
                obj.link[prop] = this.value;
            }
        });
        parent.href = obj.link.link;
        parent.target = obj.link.target;
        parent.querySelector('i').className = obj.icon+' ba-btn-transition';
        drawSocialIconsSorting();
        modal.modal('hide');
    }
});

$g('#social-icons-settings-dialog .sorting-toolbar-action[data-action="add"]').on('click', function(){
    uploadMode = 'addSocialIcon';
    checkIframe($g('#icon-upload-dialog'), 'icons');
    return false;
});

app.modules.socialIconsEditor = true;
app.socialIconsEditor();