/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.addLibrary = function(){
    setTimeout(function(){
        $g('.library-item-title, .library-item-image').val('');
        $g('.save-as-global').each(function(){
            if (app.pageStructure && app.pageStructure.context.task == 'library' && app.pageStructure.context.items.length > 1) {
                this.setAttribute('disabled', true);
            } else {
                this.removeAttribute('disabled');
            }
        }).prop('checked', false);
        $g('#add-to-library-dialog').modal();
    }, 50);
}

app.librarySaver = {
    after: () => {
        if (top.app.pageStructure) {
            top.app.pageStructure.context.task = null;
        }
        app.editor.app.checkModule('checkOverlay');
        app.editor.app.setMarginBox();
        $g('#add-to-library-dialog').modal('hide');
    },
    save: ($this) => {
        let data = JSON.stringify($this.obj);
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=editor.addLibrary",
            data: {
                object: data
            },
            complete: function(msg){
                let obj = null;
                if (msg.responseText == 'empty_data') {
                    let XHR = new XMLHttpRequest();
                    XHR.onreadystatechange = function(e) {
                        if (XHR.readyState == 4) {
                            obj = JSON.parse(XHR.responseText);
                            app.showNotice(obj.text, obj.type);
                        }
                    };
                    XHR.open("POST", JUri+"index.php?option=com_gridbox&task=editor.requestAddLibrary", true);
                    XHR.send(data);
                } else {
                    obj = JSON.parse(msg.responseText);
                    app.showNotice(obj.text, obj.type);
                }
            }
        });
        $this.after();
    },
    set: (array) => {
        let $this = app.librarySaver;
        $this.prepare($this);
        array.forEach((item) => {
            let id = item.attr('id');
            $this.obj.type = app.editor.app.items[id].type != 'section' ? 'plugin' : 'section';
            app.editor.$g('.ba-overlay-section-backdrop').each(function(){
                let button = app.editor.document.querySelector('.ba-item-overlay-section[data-overlay="'+this.dataset.id+'"]');
                if (button) {
                    button.append(this);
                }
            });
            if ($g('.save-as-global').prop('checked')) {
                $this.obj.global_item = id;
                if ($this.obj.type == 'section') {
                    item[0].parentNode.dataset.global = id;
                } else {
                    item[0].dataset.global = id;
                }
            }
            if ($this.obj.type == 'section') {
                item = item.parent();
            }
            item = item.clone();
            item.removeClass('page-structure-item-active').find('.page-structure-item-active').removeClass('.page-structure-item-active');
            item.find('.ba-menu-wrapper .tabs-content-wrapper').each(function(){
                this.closest('.ba-menu-wrapper').append(this);
            });
            item.find('.ba-section').each(function(){
                if (app.editor.app.items[this.id]) {
                    $this.items[this.id] = app.editor.app.items[this.id];
                }
            });
            item.find('.ba-row').each(function(){
                if (app.editor.app.items[this.id]) {
                    $this.items[this.id] = app.editor.app.items[this.id];
                }
            });
            item.find('.ba-grid-column').each(function(){
                if (app.editor.app.items[this.id]) {
                    $this.items[this.id] = app.editor.app.items[this.id];
                }
            });
            item.find('.ba-item').each(function(){
                if (app.editor.app.items[this.id]) {
                    $this.items[this.id] = app.editor.app.items[this.id];
                    prepareItem(this, $this.items[this.id]);
                }
            });
            if ($this.obj.type != 'section') {
                if (app.editor.app.items[id]) {
                    $this.items[id] = app.editor.app.items[id];
                    prepareItem(item[0], $this.items[id]);
                }
            }
            $this.html += item[0].outerHTML;
        });
        $this.obj.item.items = $this.items;
        $this.obj.item.html = $this.html;
        $this.save($this);
    },
    prepare: ($this) => {
        $this.items = {};
        $this.html = '';
        $this.obj = {
            title: $g('.library-item-title').val().trim(),
            image: $g('.library-item-image').val(),
            item: {},
            type: 'section',
            global_item: ''
        }
    }
}

$g('.library-item-image').on('mousedown', function(){
    uploadMode = 'LibraryImage';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('.library-item-title').on('input', function(){
    if (this.value.trim()) {
        $g('#library-apply').addClass('active-button').removeClass('disable-button');
    } else {
        $g('#library-apply').removeClass('active-button').addClass('disable-button');
    }
});

$g('#library-apply').on('click', function(){
    if (!this.classList.contains('active-button')) {
        return;
    }
    let array = []
    if (app.pageStructure && app.pageStructure.context.task == 'library') {
        array = app.pageStructure.context.items;
    } else {
        array.push(app.editor.$g('#'+app.editor.app.edit));
    }
    app.librarySaver.set(array);
});

app.modules.addLibrary = true;
app.addLibrary();