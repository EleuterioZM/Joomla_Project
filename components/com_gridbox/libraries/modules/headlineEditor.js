/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.headlineEditor = function(){
    app.selector = '#'+app.editor.app.edit;
    let modal = $g('#headline-settings-dialog');
    $g('#headline-settings-dialog .active').removeClass('active');
    $g('#headline-settings-dialog a[href="#headline-general-options"]').parent().addClass('active');
    $g('#headline-general-options').addClass('active');
    app.setDefaultState('#headline-settings-dialog .margin-settings-group', 'default');
    app.setMarginValues('#headline-settings-dialog .margin-settings-group');
    setDisableState('#headline-settings-dialog');
    app.setAccessSettings(modal);
    app.setAnimationSettings('appearance', modal);
    app.positioning.hasWidth = true;
    app.positioning.setValues(modal);
    if (app.edit.type == 'headline') {
        if (!app.edit.desktop.p.gradient) {
            ['h1' ,'h2', 'h3', 'h4', 'h5', 'h6', 'p'].forEach((key) => {
                if (key != 'links') {
                    app.edit.desktop[key].type = '';
                    app.edit.desktop[key].gradient = {
                        "effect": "linear",
                        "angle": 45,
                        "color1": "@bg-dark",
                        "position1": 25,
                        "color2": "@bg-dark-accent",
                        "position2": 75
                    }
                }
            });
        }
        modal.find('.headline-option, .select-text-type').css('display', '');
        app.setTypography($g('#headline-settings-dialog .typography-options'), app.edit.tag);
        $g('#headline-settings-dialog .headline-effect-select input[type="hidden"]').val(app.edit.desktop.animation.effect);
        value = $g('#headline-settings-dialog .headline-effect-select li[data-value="'+app.edit.desktop.animation.effect+'"]').text().trim();
        $g('#headline-settings-dialog .headline-effect-select input[type="text"]').val(value);
        value = app.getValue('animation', 'duration');
        app.setLinearInput(modal.find('input[data-group="animation"][data-option="duration"]'), value)
    } else {
        app.setTypography($g('#headline-settings-dialog .typography-options'), 'typography');
        modal.find('.headline-option, .text-gradient-options, .select-text-type').hide();
        modal.find('.text-color-options').css('display', '');
    }
    value = app.editor.document.querySelector(app.selector+' > div[class*="-wrapper"] '+app.edit.tag).textContent.trim();
    $g('#headline-settings-dialog .headline-label').val(value);
    $g('#headline-settings-dialog .select-headline-html-tag input[type="hidden"]').val(app.edit.tag);
    value = $g('#headline-settings-dialog .select-headline-html-tag li[data-value="'+app.edit.tag+'"]').text().trim();
    $g('#headline-settings-dialog .select-headline-html-tag input[readonly]').val(value);
    setPresetsList($g('#headline-settings-dialog'));
    $g('#headline-settings-dialog').attr('data-edit', app.edit.type);
    setTimeout(function(){
        modal.modal();
    }, 150);
}

$g('.headline-effect-select').on('customAction', function(){
    var value = app.edit.desktop.animation.effect,
        tag = app.editor.document.querySelector(app.selector+' > .headline-wrapper '+app.edit.tag),
        text = tag.textContent.trim(),
        data = '',
        duration = app.getValue('animation', 'duration'),
        delta = duration / text.length,
        delay = 0;
    app.editor.$g(app.selector+' .headline-wrapper').removeClass(value);
    value = this.querySelector('input[type="hidden"]').value;
    app.edit.desktop.animation.effect = value;
    app.editor.$g(app.selector+' .headline-wrapper').addClass(value);
    tag.style.animationDelay = '';
    if (value) {
        data += '<span>';
        for (var i = 0; i < text.length; i++) {
            data += '<span style="animation-delay: '+delay+'s">'+(text[i].trim() == '' ? '&nbsp;' : text[i])+'</span>';
            if (text[i].trim() == '') {
                data += '</span><span>';
            }
            delay += delta;
        }
        data += '</span>';
        if (value == 'type') {
            tag.style.animationDelay = duration+'s';
        }
    } else {
        data = text;
    }
    tag.innerHTML = data;
});

$g('#headline-settings-dialog .headline-label').on('input', function(){
    app.editor.document.querySelector(app.selector+' > div[class*="-wrapper"] '+app.edit.tag).textContent = this.value;
});

$g('#headline-settings-dialog .select-headline-html-tag').on('customAction', function(){
    var value = this.querySelector('input[type="hidden"]').value,
        text = $g('#headline-settings-dialog .headline-label').val().trim(),
        tag = document.createElement(value);
    tag.textContent = text;
    app.editor.$g(app.selector+' > div[class*="-wrapper"] '+app.edit.tag).replaceWith(tag);
    app.edit.tag = value;
    if (app.edit.type == 'headline') {
        app.setTypography($g('#headline-settings-dialog .typography-options'), app.edit.tag);
    }
    app.addHistory();
});

app.modules.headlineEditor = true;
app.headlineEditor();