/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

window.mediaManager = {
    search: '',
    path: '',
    page: 0,
    images: [],
    clientHeight: document.documentElement.clientHeight,
    uploadFiles: function(files){
        if (files.length > 0) {
            this.executeAction({
                action: 'uploadFile',
                path: mediaManager.path,
                file: files.pop()
            }).then(function(text){
                let count = top.app.notification[0].querySelector('.upload-image-count'),
                    n = count.textContent * 1;
                count.textContent = ++n;
                mediaManager.uploadFiles(files)
            });
        } else {
            top.app.showNotice(top.app._('SUCCESS_UPLOAD'));
            this.reloadFolder();
        }
    },
    getFoldersTree: function(text){
        let obj = JSON.parse(text),
            ul = document.querySelector('.ba-folder-tree > ul'),
            div = document.createElement('div');
        div.innerHTML = obj.tree;
        ul.querySelectorAll('.ba-folder-tree li.visible-branch').forEach(function(li){
            let path = li.querySelector('span[data-path]').dataset.path;
            $g(div).find('span[data-path="'+path+'"]').closest('li').addClass('visible-branch');
        });
        ul.querySelectorAll('.ba-folder-tree span.active[data-path]').forEach(function(span){
            let path = span.dataset.path;
            $g(div).find('span[data-path="'+path+'"]').addClass('active');
        });
        ul.innerHTML = div.querySelector('ul').innerHTML;
    },
    testName: function(name){
        let array = ['#','%','&','{','}','\\\\','<','>','\\*','\\?','/','\\$','!',"'",'"',':','@','\\+','`','\\|','='],
            flag = true,
            pattern;
        for (let i = 0; i < array.length; i++) {
            patt = new RegExp(array[i]);
            if (patt.test(name)) {
                flag = false;
                break;
            }
        }

        return name;
    },
    getImageObject: function(){
        let str = mediaManager.item.querySelector('.select-item').value;
        
        return JSON.parse(str);
    },
    getImages: function(){
        this.images = [];
        $g('div.ba-image img').elements.forEach(function(img){
            let obj = {
                    img: img,
                    path: img.dataset.path,
                    src: img.dataset.src
                }
            img.onload = function(){
                img.closest('td').classList.add('loaded');
            }
            mediaManager.images.push(obj);
        });
        this.checkImages();
    },
    getImageTime: function(obj){
        let time = 1,
            item = localStorage.getItem('mediaManager');
        if (item) {
            let storage = JSON.parse(item);
            time = storage[obj.path] ? storage[obj.path] : 1;
        }

        return time;
    },
    setImageTime: function(obj){
        let time = +new Date(),
            item = localStorage.getItem('mediaManager'),
            storage = {};
        if (item) {
            storage = JSON.parse(item);
        }
        storage[obj.path] = time;
        item = JSON.stringify(storage);
        localStorage.setItem('mediaManager', item);
    },
    checkImages: function(){
        var array = [];
        this.images.forEach(function(obj, ind){
            if (obj.img.getBoundingClientRect().top < mediaManager.clientHeight * 2) {
                let time = mediaManager.getImageTime(obj);
                obj.img.src = obj.src+time;
            } else {
                array.push(obj)
            }
        });
        this.images = array;
    },
    isImage: function(ext){
        return this.imageTypes.indexOf(ext) != -1;
    },
    query: function(q){
        if (q instanceof Node) {
            this.elements = [q];
        } else if (q instanceof Array) {
            this.elements = q;
        } else {
            this.elements = document.querySelectorAll(q);
        }
    },
    hideContext: function(){
        $g('.context-active').removeClass('context-active');
        $g('.visible-context-menu').removeClass('visible-context-menu');
    },
    showContext: function(q, event){
        let context = $g(q),
            deltaX = document.documentElement.clientWidth - event.pageX,
            deltaY = mediaManager.clientHeight - event.clientY;
        context.css({
            'top': event.pageY+'px',
            'left': event.pageX+'px',
        }).addClass('visible-context-menu');
        context[deltaX - context.get('offsetWidth') < 0 ? 'addClass' : 'removeClass']('ba-left');
        context[deltaY - context.get('offsetHeight') < 0 ? 'addClass' : 'removeClass']('ba-top');
    },
    reloadFolder: function(){
        mediaManager.loadFolder({
            action: 'setPage',
            path: mediaManager.path,
            search: mediaManager.search,
            page: mediaManager.page
        });
    },
    loadFolder: function(data){
        mediaManager.executeAction(data).then(function(text){
            let obj = JSON.parse(text);
            document.querySelector('.ba-breadcrumb').innerHTML = obj.breadcrumb;
            document.querySelector('.table-body').innerHTML = obj.table;
            document.querySelector('.pagination').innerHTML = obj.paginator;
            $g('#check-all').set('checked', false).trigger('click');
            mediaManager.getImages();
            mediaManager.checkActive();
            if (window.makeDrag) {
                makeDrag();
            }
        })
    },
    executeAction: function(data){
        return new Promise(function(resolve, reject){
            top.app.fetch(mediaManager.action, data, true).then(function(text){
                resolve(text);
            })
        });
    },
    checkActive: function(){
        let checked = false,
            imageChecked = false;
        $g('.select-item').elements.forEach(function(element){
            if (!checked) {
                checked = element.checked;
            }
            if (!imageChecked) {
                imageChecked = element.closest('.ba-images') && element.checked;
            }
        });
        $g('#delete-items, #move-to')[checked ? 'addClass' : 'removeClass']('active');
        $g('#ba-apply')[imageChecked ? 'addClass' : 'removeClass']('active');
    },
    getMoveTree: function(){
        let ul = document.querySelector('.ba-folder-tree > ul').cloneNode(true);
        $g('#move-to-modal .availible-folders > ul > li > ul').remove();
        $g(ul).find('.active').removeClass('active');
        $g(ul).find('.visible-branch').removeClass('visible-branch');        
        $g(ul).find('i.zmdi-chevron-right').on('click', function(){
            let parent = this.closest('li'),
                h = 0;
            parent.querySelectorAll('ul > li').forEach(function(li){
                h += li.offsetHeight;
            });
            parent.style.setProperty('--branch-height', h+'px');
            setTimeout(function(){
                parent.classList.toggle('visible-branch');
                setTimeout(function(){
                    parent.style.setProperty('--branch-height', 'auto');
                }, 300);
            }, 50);
        });
        document.querySelector('#move-to-modal .availible-folders > ul > li').append(ul);
    }
}

let $g = function(q){
        return new mediaManager.query(q);
    };

mediaManager.query.prototype = {
    modal: function(action){
        if (!action) {
            this.elements.forEach(function(element){
                element.backdrop = document.createElement('div');
                element.backdrop.modal = element;
                element.backdrop.className = 'modal-backdrop';
                document.body.append(element.backdrop);
                element.backdrop.classList.add('in');
                element.classList.add('in');
            });
        } else {
            this.elements.forEach(function(element){
                element.classList.remove('in');
                element.backdrop.classList.remove('in');
                element.classList.add('ba-modal-close');
                element.backdrop.remove();
                setTimeout(function(){
                    element.classList.remove('ba-modal-close');
                }, 500)
            });
        }

        return this;
    },
    set: function(property, value){
        this.elements.forEach(function(element){
            element[property] = value;
        });

        return this;
    },
    trigger: function(name, data){
        let event = new CustomEvent(name, data);
        this.elements.forEach(function(element){
            element.dispatchEvent(event);
        });

        return this;
    },
    find: function(q){
        let array = [],
            childs, $this;
        this.elements.forEach(function(element){
            childs = Array.from(element.querySelectorAll(q));
            array = array.concat(childs);
        });
        $this = $g(array);

        return $this;
    },
    closest: function(q){
        let array = [],
            parent, $this;
        this.elements.forEach(function(element){
            parent = element.closest(q);
            parent ? array.push(parent) : null
        });
        $this = $g(array);

        return $this;
    },
    get: function(property){
        return this.elements[0][property];
    },
    css: function(obj){
        this.elements.forEach(function(element){
            for (let ind in obj) {
                element.style[ind] = obj[ind];
            }
        });

        return this;
    },
    classList: function(name, action){
        this.elements.forEach(function(element){
            element.classList[action](name);
        });
    },
    removeClass: function(name){
        this.classList(name, 'remove');
    },
    addClass: function(name){
        this.classList(name, 'add');
    },
    on: function(name, action){
        this.elements.forEach(function(element){
            element.addEventListener(name, action);
        });

        return this;
    },
    remove: function(){
        this.elements.forEach(function(element){
            element.remove();
        });

        return this;
    }
}

document.addEventListener('DOMContentLoaded', function(){
    mediaManager.modal = top.document.querySelector('#uploader-modal');

    document.body.addEventListener('click', function(event){
        if (event.target.closest('[data-dismiss="modal"]')) {
            event.preventDefault();
            $g(event.target.closest('.modal')).modal('hide');
        } else if (event.target.classList.contains('modal-backdrop')) {
            $g(event.target.modal).modal('hide');
        }
        $g('.visible-select').removeClass('visible-select');
        mediaManager.hideContext();
    });

    $g('.ba-work-area, .ba-folder-tree').on('contextmenu', function(event){
        event.preventDefault();
        mediaManager.hideContext();
        setTimeout(function(){
            mediaManager.showContext('.empty-context-menu', event);
        }, 50);
    });

    $g('.table-body, .ba-folder-tree ul').on('contextmenu', function(event){
        let $this = event.target.closest('tr, span[data-path]');
        if (!$this) {
            return;
        }
        event.stopPropagation();
        event.preventDefault();
        mediaManager.hideContext();
        $this.classList.add('context-active');
        mediaManager.item = $this;
        let context = '.folders-context-menu';
        if ($this.classList.contains('ba-images')) {
            context = '.files-context-menu';
            $g('.files-context-menu .edit-image').css({
                display: mediaManager.isImage($this.dataset.ext) ? '' : 'none'
            });
        }
        setTimeout(function(){
            mediaManager.showContext(context, event);
        }, 50);
    });
    $g('.ba-folder-tree').on('click', function(event){
        let btn = event.target.closest('.ba-branch-action');
        if (!btn) {
            return;
        }
        let parent = btn.closest('li'),
            h = 0;
        parent.querySelectorAll('ul > li').forEach(function(li){
            h += li.offsetHeight;
        });
        parent.style.setProperty('--branch-height', h+'px');
        setTimeout(function(){
            parent.classList.toggle('visible-branch');
            setTimeout(function(){
                parent.style.setProperty('--branch-height', 'auto');
            }, 300);
        }, 50);
    });
    $g('.ba-folder-tree, .ba-breadcrumb, .table-body').on('click', function(event){
        let span = event.target.closest('span[data-path]');
        if (!span) {
            return;
        }
        if (span.closest('li')) {
            $g('.ba-folder-tree .active').removeClass('active');
            span.classList.add('active')
        } else {
            $g('.ba-folder-tree .active').removeClass('active');
            $g('.ba-folder-tree span[data-path="'+span.dataset.path+'"]').addClass('active');
        }
        document.querySelector('.ba-media-manager-search-input').value = mediaManager.search = '';
        mediaManager.path = span.dataset.path;
        mediaManager.page = 0;
        mediaManager.loadFolder({
            action: 'loadFolder',
            path: span.dataset.path
        });
        
    });
    $g('#check-all').on('click', function(){
        var checked = this.checked;
        if (mediaManager.modal.dataset.check == 'single') {
            this.checked = false;
            return;
        }
        $g('.select-item').set('checked', checked);
        mediaManager.checkActive();
    });

    $g('.ba-custom-select').on('click', function(event){
        if (!event.target.closest('i, input')) {
            return;
        }
        let parent = this;
        if (!parent.querySelector('ul.visible-select')) {
            setTimeout(function(){
                parent.querySelector('ul').classList.add('visible-select');
            }, 100);
        }
    }).on('click', function(event){
        if (!event.target.closest('li')) {
            return;
        }
        let li = event.target,
            $this = $g(this);
        $this.find('li.selected').removeClass('selected');
        $this.find('i.zmdi-check').remove();
        li.classList.add('selected');
        li.insertAdjacentHTML('afterbegin', '<i class="zmdi zmdi-check"></i>');
        this.querySelector('input[type="text"]').value = li.textContent.trim();
        $this.find('input[type="hidden"]').set('value', li.dataset.value).trigger('change');
        $this.trigger('customAction', {
            detail: li.dataset.value
        });
    });

    $g('.pagination-limit-select').on('customAction', function(event){
        mediaManager.page = 0;
        mediaManager.loadFolder({
            action: 'setLimit',
            search: mediaManager.search,
            path: mediaManager.path,
            limit: event.detail
        });
    });

    $g('.table-head span[data-sorting]').on('click', function(){
        mediaManager.page = 0;
        if (mediaManager.sorting == this.dataset.sorting) {
            mediaManager.direction = mediaManager.direction == 'ASC' ? 'DESC' : 'ASC';
        }
        $g('.table-head span.active').removeClass('active');
        this.classList.add('active');
        mediaManager.sorting = this.dataset.sorting;
        mediaManager.loadFolder({
            action: 'setSorting',
            search: mediaManager.search,
            path: mediaManager.path,
            direction: mediaManager.direction,
            sorting: mediaManager.sorting
        });
    });

    $g('.pagination').on('click', function(event){
        event.preventDefault();
        let btn = event.target.closest('a');
        if (btn.classList.contains('disabled') || btn.classList.contains('active')) {
            return;
        }
        mediaManager.page = btn.dataset.page;
        mediaManager.reloadFolder();
    });

    $g('.ba-media-manager-search-icon').on('click', function(){
        this.closest('#ba-media-manager').classList.add('media-search-focus-in');
        document.querySelector('.ba-media-manager-search-input').focus();
    });

    $g('.ba-media-manager-search-input').on('input', function(){
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            mediaManager.search = $this.value.trim();
            mediaManager.path = '';
            mediaManager.page = 0;
            mediaManager.reloadFolder();
        }, 500);
    });

    $g('#ba-media-manager').on('click', function(event){
        if (this.classList.contains('media-search-focus-in') && !event.target.closest('.ba-media-manager-search-wrapper')) {
            document.querySelector('#ba-media-manager').classList.remove('media-search-focus-in');
        }
    });

    window.addEventListener('resize', function(){
        setTimeout(function(){
            mediaManager.clientHeight = document.documentElement.clientHeight;
            mediaManager.checkImages();
        }, 500);
    });

    $g('.ba-work-area').on('scroll',function(){
        mediaManager.checkImages();
    });

    $g('.ba-context-menu .download').on('click', function(){
        let obj = mediaManager.getImageObject(),
            a = document.createElement('a');
        a.setAttribute('download', '');
        a.href = top.JUri+obj.url;
        a.click();
    });

    $g('.edit-image').on('mousedown', function(){
        let obj = mediaManager.getImageObject();
        top.app.itemDelete = obj;
        top.app.checkModule('photoEditor');
    });

    $g('#apply-rename').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return false;
        }
        let value = document.querySelector('.new-name').value,
            name = value,
            path;
        if (mediaManager.item.classList.contains('ba-images')) {
            let obj = mediaManager.getImageObject();
            path = obj.path;
            name = obj.folder+name+'.'+obj.ext;
        } else if (mediaManager.item.localName == 'tr') {
            path = mediaManager.item.querySelector('.folder-list').dataset.path;
        } else {
            path = mediaManager.item.dataset.path;
        }
        if (!mediaManager.item.classList.contains('ba-images')) {
            let array = path.split('/');
            array[array.length - 1] = name;
            name = array.join('/');
        }
        if (mediaManager.path == path) {
            mediaManager.path = name;
            $g('.ba-folder-tree span[data-path="'+path+'"] span').set('textContent', value)
                .elements[0].closest('[data-path]').dataset.path = name;
        }
        $g('#rename-modal').modal('hide');
        mediaManager.executeAction({
            action: 'rename',
            path: path,
            name: name
        }).then(function(text){
            top.app.showNotice(top.app._('SUCCESS_RENAME'));
            mediaManager.getFoldersTree(text);
            mediaManager.reloadFolder();
        });
    });

    $g('.ba-context-menu .rename').on('click', function(){
        let name = '';
        if (mediaManager.item.classList.contains('ba-images')) {
            let obj = mediaManager.getImageObject(),
                array = obj.name.split('.');
            for (var i = 0; i < array.length - 1; i++) {
                name += array[i];
            }
        } else if (mediaManager.item.localName == 'tr') {
            name = mediaManager.item.querySelector('.folder-list').textContent;
        } else {
            name = mediaManager.item.textContent;
        }
        name = name.trim();
        document.querySelector('.new-name').value = name;
        document.querySelector('#apply-rename').classList.remove('active-button');
        $g('#rename-modal').modal();
    });

    $g('#rename-modal .new-name').on('input', function(){
        let name = this.value,
            flag = mediaManager.testName(name);
        $g('#apply-rename')[name.trim() && flag ? 'addClass' : 'removeClass']('active-button');
    });

    $g('#show-folder').on('click', function(){
        $g('#create-folder-modal').modal().find('[name="new-folder"]').set('value', '');
        $g('#add-folder').removeClass('active-button');
    });

    $g('.ba-context-menu .create-folder').on('mousedown', function(){
        $g('#show-folder').trigger('click');
    });

    $g('#add-folder').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button')) {
            $g('#create-folder-modal').modal('hide');
            let name = $g('#create-folder-modal [name="new-folder"]').get('value');
            mediaManager.executeAction({
                action: 'createFolder',
                path: mediaManager.path,
                name: name
            }).then(function(text){
                top.app.showNotice(top.app._('FOLDER_IS_CREATED'));
                mediaManager.getFoldersTree(text);
                mediaManager.reloadFolder();
            });
        }            
    });
    $g('#create-folder-modal [name="new-folder"]').on('input', function(){
        let name = this.value,
            flag = mediaManager.testName(name);
        $g('#add-folder')[name.trim() && flag ? 'addClass' : 'removeClass']('active-button');
    });

    $g('.ba-context-menu .delete').on('mousedown', function(){
        mediaManager.delete = 'context';
        $g('#delete-modal').modal();
    });

    $g('#apply-delete').on('click', function(event){
        event.preventDefault();
        let data = {},
            path = '';
        if (mediaManager.delete == 'context') {
            if (mediaManager.item.classList.contains('ba-images')) {
                let obj = mediaManager.getImageObject();
                path = obj.path;
            } else if (mediaManager.item.localName == 'tr') {
                path = mediaManager.item.querySelector('.folder-list').dataset.path;
            } else {
                path = mediaManager.item.dataset.path;
            }
            if (mediaManager.path == path) {
                mediaManager.path =  '';
            }
            data = {
                action: 'contextDelete',
                path: path
            }
        } else if (mediaManager.delete == 'multiple') {
            let array = [];
            $g('.table-body .select-item').elements.forEach(function(element){
                if (element.checked) {
                    let obj = JSON.parse(element.value);
                    array.push(obj.path);
                }
            });
            data = {
                action: 'multipleDelete',
                path: mediaManager.path,
                array: array
            }
        }
        mediaManager.executeAction(data).then(function(text){
            top.app.showNotice(top.app._('SUCCESS_DELETE'));
            mediaManager.getFoldersTree(text);
            mediaManager.reloadFolder();
        });
        $g('#delete-modal').modal('hide');
    });

    $g('.table-body').on('click', function(event){
        if (!event.target.closest('i')) {
            return;
        }
        let checkbox = event.target.closest('td.select-td').querySelector('.select-item'),
            checked = checkbox.checked;
        if (mediaManager.modal.dataset.check == 'single') {
            $g('.select-item').set('checked', false);
        }
        checkbox.checked = !checked;
        mediaManager.checkActive();
    });

    $g('#delete-items').on('click', function(event){
        if (this.classList.contains('active')) {
            mediaManager.delete = 'multiple';
            $g('#delete-modal').modal();
        }
    });

    $g('.ba-context-menu .upload-file').on('mousedown', function(){
        $g('#show-upload').trigger('click');
    });

    $g('#show-upload').on('click', function(){
        document.querySelector('#file-upload-form [type="file"]').remove();
        document.querySelector('#file-upload-form form').innerHTML = '<input type="file" multiple>';
        document.querySelector('#file-upload-form [type="file"]').click();
    });

    $g('#file-upload-form').on('change', function(event){
        let btn = event.target.closest('input[type="file"]');
        if (!btn) {
            return;
        }
        let files = [],
            str = top.app._('UPLOADING_MEDIA');
        for (let i = 0; i < btn.files.length; i++) {
            let name = btn.files[i].name.split('.'),
                ext = name[name.length - 1].toLowerCase();
            if (mediaManager.types.indexOf(ext) != -1) {
                files.push(btn.files[i]);
            }
        }
        if (files.length > 0) {
            str += ' <span class="upload-image-count">0</span> / '+files.length;
            str +='</span><img src="'+top.JUri+'components/com_gridbox/assets/images/reload.svg"></img>'
            top.app.notification[0].querySelector('p').innerHTML = str;
            top.app.notification[0].className = 'notification-in';
            mediaManager.uploadFiles(files);
        } else {
            top.app.showNotice(top.app._('NOT_SUPPORTED_FILE'), 'ba-alert');
        }
    });

    $g('.close-media').on('click', function(){
        top.$g(mediaManager.modal).modal('hide');
    });

    $g('.media-fullscrean').on('click', function(){
        if (!mediaManager.modal.classList.contains('fullscrean')) {
            this.classList.remove('zmdi-fullscreen');
            this.classList.add('zmdi-fullscreen-exit');
        } else {
            this.classList.add('zmdi-fullscreen');
            this.classList.remove('zmdi-fullscreen-exit');
        }
        mediaManager.modal.classList.toggle('fullscrean');
    });

    $g('#move-to').on('click', function(){
        if (!this.classList.contains('active')) {
            return false;
        }
        mediaManager.move = 'multiple';
        $g('#move-to-modal .availible-folders .active').removeClass('active');
        $g('#move-to-modal .active-button').removeClass('active-button');
        mediaManager.getMoveTree();
        $g('#move-to-modal').modal();
    });

    $g('.ba-context-menu .move-to').on('click', function(){
        mediaManager.move = 'context';
        $g('#move-to-modal .availible-folders .active').removeClass('active');
        $g('#move-to-modal .active-button').removeClass('active-button');
        mediaManager.getMoveTree();
        $g('#move-to-modal').modal();
    });

    $g('#move-to-modal .availible-folders').on('click', function(event){
        let span = event.target.closest('span[data-path]');
        if (!span) {
            return
        }
        $g('#move-to-modal .availible-folders .active').removeClass('active');
        span.classList.add('active');
        $g('.apply-move').addClass('active-button');
    });

    $g('.apply-move').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return;
        }
        let array = [],
            path = '';
        if (mediaManager.move == 'context') {
            let path = '';
            if (mediaManager.item.classList.contains('ba-images')) {
                let obj = mediaManager.getImageObject();
                path = obj.path;
            } else if (mediaManager.item.localName == 'tr') {
                path = mediaManager.item.querySelector('.folder-list').dataset.path;
            } else {
                path = mediaManager.item.dataset.path;
            }
            array.push(path);
        } else if (mediaManager.move == 'multiple') {
            $g('.table-body .select-item').elements.forEach(function(element){
                if (element.checked) {
                    let obj = JSON.parse(element.value);
                    array.push(obj.path);
                }
            });
        }
        path = document.querySelector('#move-to-modal .availible-folders .active').dataset.path;
        array.forEach(function(value){
            if (mediaManager.path && mediaManager.path.indexOf(value) != -1) {
                mediaManager.path =  '';
            }
        })
        mediaManager.executeAction({
            action: 'multipleMove',
            path: path,
            array: array
        }).then(function(text){
            top.app.showNotice(top.app._('SUCCESS_MOVED'));
            mediaManager.getFoldersTree(text);
            mediaManager.reloadFolder();
        });
        $g('#move-to-modal').modal('hide');
    });
    
    $g('#ba-apply').on('click', function(){
        let data = [];
        $g('.table-body .ba-images .select-item').elements.forEach(function(element){
            if (element.checked) {
                let obj = JSON.parse(element.value);
                data.push(obj);
            }
        });
        document.querySelector('#check-all').checked = false;
        $g('.select-item').set('checked', false);
        $g('.active').removeClass('active');
        if (document.body.classList.contains('photo-editor-enabled')) {
            top.$g('#uploader-modal').modal('hide');
            top.app.itemDelete = data[0];
            top.app.checkModule('photoEditor');
        } else {
            top.app.messageData = data;
            top.app.checkModule('messageListener');
        }
    });
    if (window.makeDrag) {
        makeDrag();
    }
    mediaManager.getImages();
});