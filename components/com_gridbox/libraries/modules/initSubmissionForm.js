/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initSubmissionForm = function(obj, key){
    app.submissinForm.app_id = obj.app;
    app.submissinForm.id = document.querySelector('.ba-submission-form-wrapper').dataset.id;
    app.submissinForm.page_id = document.querySelector('.ba-submission-form-wrapper').dataset.page;
    $g('#'+key+' .select-image-field').on('click', function(){
        if (themeData.page.view == 'gridbox') {
            return;
        }
        let multiple = this.closest('.blog-post-submission-form-options-group').dataset.fieldType != 'image-field';
        app.submissinForm.btn = this;
        app.submissinForm.createFile(this.dataset.size, 'gif, jpg, jpeg, png, svg, webp', multiple);
    });
    $g('#'+key+' .ba-custom-select').on('show', function(){
        let $this = $g(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    })
    $g('#'+key).on('click focus', 'input, select, textarea', function(){
        app.submissinForm.removeAlertTooltip(this.closest('.blog-post-submission-form-options-group'));
    }).on('input', 'textarea[data-texteditor]', function(){
        app.submissinForm.removeAlertTooltip(this.closest('.blog-post-submission-form-options-group'));
    })
    $g('#'+key+' .trigger-attachment-file-field').on('click', function(){
        if (themeData.page.view == 'gridbox') {
            return;
        }
        app.submissinForm.btn = this;
        app.submissinForm.createFile(this.dataset.size, this.dataset.types);
    });
    $g('#'+key+' .reset').on('click', function(){
        $g(this).closest('.blog-post-submission-form-group-element').find('input').val('').attr('data-value', '');
    });
    $g('#'+key+' .select-field-video-type').on('change', function(){
        let type = this.value,
            parent = $g(this).closest('.field-sorting-wrapper');
        parent.find('input[data-name="file"], input[data-name="id"]').val('');
        parent.find('.field-video-id')[type == 'source' ? 'hide' : 'show']();
        parent.find('.field-video-file')[type != 'source' ? 'hide' : 'show']();
    });
    $g('#'+key+' .ba-uploaded-images-list').on('click', 'i.ba-icon-trash', function(){
        this.closest('.ba-uploaded-image-row').remove();
    });
    $g('#'+key+' .field-sorting-wrapper .field-video-file input[data-name="file"]').on('click', function(){
        app.submissinForm.btn = this;
        app.submissinForm.createFile(this.dataset.size, 'mp4');
    }).on('change', function(){
        $g(this).trigger('input');
    });
    $g('#'+key+' [data-field-type="price"] input').on('input', function(){
        let decimals = this.dataset.decimals ? this.dataset.decimals * 1 : 0,
            max = decimals > 0 ? 1 : 0,
            match = this.value.match(new RegExp('\\d+\\.{0,'+max+'}\\d{0,'+decimals+'}'));
        if (!match) {
            this.value = '';
        } else if (match[0] != this.value) {
            this.value = match[0];
        }
    });
    $g('.ba-range-wrapper input[type="range"]').each(function(){
        app.submissinForm.rangeAction(this);
    });
    $g('[data-field-type="field-simple-gallery"], [data-field-type="field-slideshow"]').find('.ba-uploaded-images-list').each(function(i){
        app.submissinForm.sortable.init(this, {
            group: 'images-'+i
        });
    });
    if (window.CKEDITOR && document.querySelector('#'+key+' textarea[data-texteditor]')) {
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.CKEThemeRules', {
            id: themeData.theme
        }).then((text) => {
            let css = [JUri+'components/com_gridbox/libraries/ckeditor/css/submission-form-ckeditor.css', text],
                fonts = document.querySelector('link[href*="fonts.googleapis.com"]');
            if (fonts) {
                css.push(fonts.href)
            }
            CKEDITOR.config.contentsCss = css;
            $g('#'+key+' textarea[data-texteditor]').each(function(){
                app.submissinForm.setCKE(this);
            });
        });
    }
    app.submissinForm.tags.init();
    if (themeData.page.view == 'gridbox' && document.querySelector('.field-google-map-wrapper')) {
        app.submissinForm.loadMap();
    } else if (document.querySelector('.field-google-map-wrapper')) {
        app.submissinForm.onloadMap();
    }
    initItems();
}

app.submissinForm = {
    setCKE: ($this) => {
        if (!app.submissinForm.CKE) {
            app.submissinForm.CKE = {};
        }
        app.submissinForm.CKE[$this.name] = CKEDITOR.replace($this);
        app.submissinForm.CKE[$this.name].on('change', function(){
            $g(this.element.$).trigger('input');
        });
    },
    tags: {
        init: () => {
            app.fetch(JUri+"index.php?option=com_gridbox&task=editor.getPageTags").then((text) => {
                app.submissinForm.tags.setModal(text);
            });
        },
        setModal: (text) => {
            let obj = JSON.parse(text),
                str = '<div id="post-tags-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker">';
            str += '<div class="modal-body"><div class="data-tags-searchbar"><div class="ba-settings-group">';
            str += '<div class="ba-settings-item ba-settings-select-type"><select class="select-data-tags-type">';
            for (let ind in obj.folders) {
                str += '<option value="'+obj.folders[ind].id+'">'+obj.folders[ind].title+'</option>';
            }
            str += '</select></div><div class="ba-settings-item ba-settings-input-type search-tags-wrapper">';
            str += '<input type="text" class="search-post-tags" placeholder="'+app._('SEARCH')+'">';
            str += '<i class="zmdi zmdi-search"></i></div></div></div><div class="post-tags-wrapper">';
            for (let ind in obj.tags) {
                str += '<div class="ba-settings-item ba-settings-input-type" data-folder="'+obj.tags[ind].folder_id+'"';
                str += 'data-id="'+obj.tags[ind].id+'"><i class="zmdi zmdi-label"></i>';
                str += '<span class="ba-settings-item-title">'+obj.tags[ind].title+'</span></div>';
            }
            str += '</div></div></div>';
            $g(document.body).append(str);
            app.submissinForm.tags.modal = $g('#post-tags-dialog');
            app.submissinForm.tags.addEvents();
        },
        addEvents: () => {
            $g('.meta-tags .picked-tags .search-tag input').on('keyup', function(event){
                let title = this.value.trim();
                if (event.keyCode == 13) {
                    if (!title) {
                        this.value = '';
                        return false;
                    }
                    let str = '<li class="tags-chosen"><span>',
                        tagId = 'new$'+title;
                    app.submissinForm.tags.modal.find('.ba-settings-item').each(function(){
                        if (title == this.textContent.trim()) {
                            tagId = this.dataset.id;
                            return false;
                        }
                    });
                    if (tagId != 'new$'+title || document.querySelector('.picked-tags .tags-chosen i[data-remove="'+tagId+'"]')) {
                        return false;
                    }
                    str += title+'</span><i class="zmdi zmdi-close" data-remove="'+tagId+'"></i></li>';
                    $g('.picked-tags').append(str);
                    str = '<option value="'+tagId+'" selected>'+title+'</option>';
                    $g('select.meta_tags').append(str);
                    $g('.meta-tags .picked-tags .search-tag input').val('');
                    $g('.ba-alert-label[data-field-type="tag"]').removeClass('ba-alert-label');
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                }
            });
            $g('.select-post-tags').on('click', function(){
                fontBtn = this;
                app.submissinForm.tags.modal.find('.ba-settings-item[data-id]').each(function(){
                    this.classList[document.querySelector('.meta-tags option[value="'+this.dataset.id+'"]') ? 'add' : 'remove']('selected');
                });
                app.submissinForm.tags.showModal();
            });
            app.submissinForm.tags.modal.find('.modal-body').on('change', '.select-data-tags-type', function(){
                let modal = app.submissinForm.tags.modal;
                modal.find('.ba-settings-item[data-id]').hide();
                modal.find('.ba-settings-item[data-id]'+(this.value != 1 ? '[data-folder="'+this.value+'"]' : '')).css('display', '');
            });
            app.submissinForm.tags.modal.find('.modal-body').on('input', '.search-post-tags', function(){
                let modal = app.submissinForm.tags.modal,
                    search = this.value.trim().toLowerCase();
                    folder = modal.find('.select-data-tags-type').val();
                modal.find('.ba-settings-item[data-id]').hide();
                modal.find('.ba-settings-item[data-id]').each(function(){
                    if (folder == this.dataset.folder) {
                        let title = this.querySelector('.ba-settings-item-title').textContent.trim().toLowerCase();
                        this.style.display = (search === '' || title.indexOf(search) != -1 ? '' : 'none');
                    }
                });
            });
            app.submissinForm.tags.modal.on('shown', function(){
                $g('.modal-backdrop').last().addClass(this.id);
            })
            app.submissinForm.tags.modal.find('.modal-body').on('click', '.post-tags-wrapper .ba-settings-input-type', function(){
                let id = this.dataset.id,
                    title = this.textContent.trim(),
                    str = '';
                if (document.querySelector('.meta-tags option[value="'+id+'"]')) {
                    return;
                }
                str = '<li class="tags-chosen"><span>'+title+'</span><i class="zmdi zmdi-close" data-remove="'+id+'"></i></li>';
                $g('.picked-tags').append(str);
                str = '<option value="'+id+'" selected>'+title+'</option>';
                $g('select.meta_tags').append(str);
                app.submissinForm.tags.modal.modal('hide');
            });
            $g('.meta-tags .picked-tags').on('click', '.zmdi.zmdi-close', function(){
                let id = this.dataset.remove;
                $g('select.meta_tags option[value="'+id+'"]').remove();
                $g('.tags-chosen i[data-remove="'+id+'"]').parent().remove();
            });
        },
        showModal: () => {
            let rect = fontBtn.getBoundingClientRect(),
                modal = app.submissinForm.tags.modal,
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
    },
    sortable: {
        groups: {},
        prepareData: (placeholder) => {
            let rect = placeholder.getBoundingClientRect();
            app.submissinForm.sortable.css = {
                width: rect.width,
                height: rect.height,
                left: rect.left,
                top: rect.top
            }
        },
        init: (item, options) => {
            let $this = app.submissinForm.sortable;
            if (!$this.groups[options.group]) {
                $this.groups[options.group] = [];
            }
            $this.groups[options.group].unshift(item);
            $g(item).off('mousedown.gridSorting').on('mousedown.gridSorting', '.ba-uploaded-image', function(e){
                if (e.button == 0) {
                    let placeholder = this.closest('.ba-uploaded-image-row'),
                        placeholders = [],
                        handle = placeholder.cloneNode(true),
                        rect = null,
                        method = '',
                        element = null,
                        helper = $g(handle),
                        place = $g(placeholder),
                        array = $this.groups[options.group],
                        delta = {};
                    $this.prepareData(placeholder);
                    $g(document).on('mousemove.gridSorting', function(event){
                        let deltaX = Math.abs(e.clientX - event.clientX),
                            deltaY = Math.abs(e.clientY - event.clientY);
                        if (!document.body.classList.contains('grid-sorting-started') && deltaX < 5 && deltaY < 5) {
                            return false;
                        }
                        if (!document.body.classList.contains('grid-sorting-started')) {
                            for (let i = array.length - 1; i >= 0; i--) {
                                $g(array[i]).find('.ba-uploaded-image-row').each(function(){
                                    if (this == placeholder || (this.classList.contains('active') && placeholder.classList.contains('active'))) {
                                        placeholders.push(this)
                                    }
                                });
                            }
                            handle.classList.add('sorting-grid-handle-item');
                            handle.classList.add(options.group);
                            document.body.append(handle);
                            placeholders.forEach((li) => {
                                li.classList.add('sorting-grid-placeholder-item');
                            });
                            delta.x = $this.css.left - event.clientX;
                            delta.y = $this.css.top - event.clientY;
                            helper.css($this.css);
                            document.body.classList.add('grid-sorting-started');
                            document.body.classList.add('page-structure-sorting');
                        }
                        let target = null,
                            top = event.clientY + delta.y,
                            left = event.clientX + delta.x,
                            bottom = top + $this.css.height,
                            right = left + $this.css.width;
                        for (let i = 0; i < array.length; i++) {
                            $g(array[i]).find('.ba-uploaded-image-row').each(function(){
                                rect = this.getBoundingClientRect();
                                if (!this.classList.contains('sorting-grid-placeholder-item') && rect.top < event.clientY && rect.bottom > event.clientY
                                    && rect.left < event.clientX && event.clientX < rect.right) {
                                    target = this;
                                    return false;
                                }
                            });
                            if (target) {
                                method = (event.clientY - rect.top) / (rect.bottom - rect.top) > .5 ? 'after' : 'before';
                                break;
                            } else if (array[i].children.length == 0) {
                                let li = array[i].closest('li');
                                rect = li.getBoundingClientRect();
                                if (rect.top < event.clientY && rect.bottom > event.clientY && rect.left < event.clientX && event.clientX < rect.right) {
                                    target = array[i];
                                    method = 'append';
                                    li.classList.add('visible-branch');
                                    break;
                                }
                            }
                        }
                        if (target) {
                            element = target;
                            if (method != 'after') {
                                placeholders.forEach((li) => {
                                    $g(target)[method](li);
                                });
                            } else {
                                for (let i = placeholders.length - 1; i >= 0; i--) {
                                    $g(target)[method](placeholders[i]);
                                }
                            }
                            $this.prepareData(placeholder);
                        }
                        helper.css({
                            top: top,
                            left: left,
                        });
                    }).off('mouseleave.gridSorting').on('mouseleave.gridSorting', function(){
                        $g(document).trigger('mouseup.gridSorting');
                    }).off('mouseup.gridSorting').on('mouseup.gridSorting', function(){
                        if (document.body.classList.contains('grid-sorting-started')) {
                            handle.classList.add('grid-sorting-return-animation');
                            helper.css($this.css);
                            handle.delay = setTimeout(function(){
                                handle.remove();
                                placeholders.forEach((li) => {
                                    li.classList.remove('sorting-grid-placeholder-item');
                                });
                            }, 300);
                            document.body.classList.remove('grid-sorting-started');
                            document.body.classList.remove('page-structure-sorting');
                        }
                        $g(document).off('mousemove.gridSorting mouseup.gridSorting mouseleave.gridSorting');
                    });
                }
            });
        }
    },
    setLinearWidth: (range) => {
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
    },
    rangeAction: (range) => {
        let $this = $g(range),
            max = $this.attr('max') * 1,
            min = $this.attr('min') * 1,
            number = $this.closest('.ba-range-wrapper').find('input[type="number"]');
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
            app.submissinForm.setLinearWidth($this);
        });
        app.submissinForm.setLinearWidth($this);
        $this.on('input', function(){
            let text = number.val(),
                match = text.match(/[a-zA-Z%]+/),
                v = this.value+(match ? match[0] : '');
            number.val(v).trigger('input');
        });
    },
    startUpload: (files) => {
        let str = '<span>'+app._('UPLOADING_MEDIA')+'</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
        app.notification.querySelector('p').innerHTML = str;
        app.notification.className = 'notification-in';
        app.submissinForm.uploadFile(files);
    },
    createFile: (size, types, multiple) => {
        let input = document.createElement('input');
        input.type = 'file';
        input.onchange = app.submissinForm.uploadFiles;
        app.submissinForm.input = input;
        if (multiple) {
            input.setAttribute('multiple', 'multiple');
        }
        app.submissinForm.size = size;
        app.submissinForm.types = types;
        app.submissinForm.multiple = multiple;
        input.style.display = 'none';
        document.body.append(input);
        setTimeout(function(){
            input.click();
        }, 100);
    },
    uploadFiles: () => {
        let files = [].slice.call(app.submissinForm.input.files),
            flag = true,
            size = app.submissinForm.size * 1000,
            msg = '',
            types = app.submissinForm.types.replace(/ /g, '').split(',');
        for (let i = 0; i < files.length; i++) {
            let name = files[i].name.split('.'),
                ext = name[name.length - 1].toLowerCase();
            if (size < files[i].size) {
                msg = 'NOT_ALLOWED_FILE_SIZE';
            } else if (types.indexOf(ext) == -1) {
                msg = 'NOT_SUPPORTED_FILE';
            }
            if (size < files[i].size || types.indexOf(ext) == -1) {
                flag = false;
                app.showNotice(app._(msg), 'ba-alert');
                break;
            }
        }
        if (!flag) {
            return;
        }
        if (!app.notification) {
            app.loadNotice().then(() => {
                app.submissinForm.startUpload(files);
            });
        } else {
            app.submissinForm.startUpload(files);
        }
    },
    uploadFile: (files) => {
        let xhr = new XMLHttpRequest(),
            file = files.shift(),
            formData = new FormData();
        formData.append('file', file);
        formData.append('app_id', app.submissinForm.app_id);
        xhr.onload = xhr.onerror = function(){
            let obj = JSON.parse(xhr.responseText);
            if (!obj.error) {
                app.submissinForm.afterUpload(obj);
            } else if (obj.error && !files.length) {
                app.showNotice(obj.msg, 'ba-alert');
            }
            if (files.length) {
                app.submissinForm.uploadFile(files)
            } else if (!obj.error) {
                setTimeout(function(){
                    app.notification.className = 'animation-out';
                }, 2000);
            }
        };
        xhr.open("POST", JUri+"index.php?option=com_gridbox&task=editor.uploadSubmissionFile", true);
        xhr.send(formData);
    },
    afterUpload: (obj) => {
        if (app.submissinForm.btn.classList.contains('select-image-field')) {
            app.submissinForm.setImage(obj);
        } else {
            app.submissinForm.setFile(obj);
        }
    },
    setFile: (obj) => {
        app.submissinForm.btn.dataset.value = obj.id;
        app.submissinForm.btn.value = obj.name;
    },
    setImage: (obj) => {
        let parent = app.submissinForm.btn.closest('.select-image-field-wrapper').querySelector('.ba-uploaded-images-list'),
            img = document.createElement('div');
        img.className = 'ba-uploaded-image-row';
        img.dataset.id = obj.id;
        img.dataset.alt = obj.alt
        img.innerHTML = '<span class="ba-uploaded-image"></span><span class="ba-uploaded-image-title"></span><i class="ba-icons ba-icon-trash"></i>';
        img.querySelector('.ba-uploaded-image').style.backgroundImage = 'url('+JUri+obj.path+')';
        img.querySelector('.ba-uploaded-image-title').textContent = obj.name;
        if (!app.submissinForm.multiple) {
            parent.innerHTML = '';
        }
        parent.append(img);
    },
    loadMap: () => {
        app.submissinForm.mapScript = document.createElement('script');
        app.submissinForm.mapScript.onload = app.submissinForm.onloadMap;
        app.submissinForm.mapScript.src = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='+top.integrations.google_maps.key;
        document.head.appendChild(app.submissinForm.mapScript);
    },
    onloadMap: () => {
        app.fieldMaps = {};
        let options = {
            "center" : {
                "lat" : 42.345573,
                "lng" : -71.098326
            },
            "zoom": 14,
            "mapTypeId" : "roadmap",
            "scrollwheel": false,
            "navigationControl": false,
            "mapTypeControl": false,
            "scaleControl": false,
            "draggable": true,
            "zoomControl": true,
            "disableDefaultUI": true,
            "disableDoubleClickZoom": true
        };
        $g('.field-google-map-wrapper').each(function(){
            let string = this.previousElementSibling.textContent,
                object = JSON.parse(string),
                data = $g.extend(true, {}, options),
                input = document.querySelector('input[data-autocomplete][name="'+this.dataset.id+'"]'),
                autocomplete = new google.maps.places.Autocomplete(input);
            if (object.center) {
                data.center = object.center;
                data.zoom = object.zoom;
            }
            app.fieldMaps[this.dataset.id] = {
                name: this.dataset.id,
                map: new google.maps.Map(this, data),
                input: input,
                marker: null
            };
            if (object.marker && object.marker.position) {
                let obj = {
                    position: object.marker.position,
                    map: app.fieldMaps[this.dataset.id].map
                }
                app.fieldMaps[this.dataset.id].marker = new google.maps.Marker(obj);
            }
            app.fieldMaps[this.dataset.id].map.name = this.dataset.id;
            autocomplete.fieldMap = app.fieldMaps[this.dataset.id];
            $g(input).on('input', function(){
                $g('.pac-container').on('mousedown', function(event){
                    event.stopPropagation();
                });
            });
            autocomplete.addListener('place_changed', function(){
                let place = autocomplete.getPlace();
                if (place.geometry.viewport) {
                    this.fieldMap.map.fitBounds(place.geometry.viewport);
                } else {
                    this.fieldMap.map.setCenter(place.geometry.location);
                }
            });
            app.fieldMaps[this.dataset.id].map.addListener('click', function(event) {
                if (app.fieldMaps[this.name].marker) {
                    app.fieldMaps[this.name].marker.setMap(null);
                }
                let obj = {
                    position: event.latLng,
                    map: this
                }
                app.fieldMaps[this.name].marker = new google.maps.Marker(obj);
            });
        });
    },
    toggleAlertTooltip: function(alert, $this, parent, key){
        if (alert && !$this.alertTooltip) {
            $this.alertTooltip = document.createElement('span');
            $this.alertTooltip.className = 'ba-submission-alert-tooltip';
            $this.alertTooltip.textContent = gridboxLanguage[key];
            parent.classList.add('ba-submission-alert');
            parent.appendChild($this.alertTooltip);
        } else if (alert && $this.alertTooltip) {
            $this.alertTooltip.textContent = gridboxLanguage[key];
        } else if (!alert && $this.alertTooltip) {
            app.submissinForm.removeAlertTooltip($this);
        }
    },
    removeAlertTooltip: function($this){
        if ($this.alertTooltip) {
            $this.alertTooltip.remove();
            $this.alertTooltip = null;
            $this.closest('.ba-submission-alert').classList.remove('ba-submission-alert');
        }
    },
    getData: () => {
        let fields = {};
        $g('.ba-item-submission-form .blog-post-submission-form-options-group[data-field-type]').each(function(){
            let field = {
                    field_id: null,
                    type: this.dataset.fieldType
                },
                input = value = null;
            switch (this.dataset.fieldType) {
                case 'text':
                case 'date':
                case 'event-date':
                case 'number':
                case 'range':
                case 'price':
                case 'category':
                    input = $g(this).find('input[name]')[0];
                    field.field_id = input.name;
                    field.value = input.value.trim();
                    break;
                case 'file':
                    input = $g(this).find('input[name]')[0];
                    field.field_id = input.name;
                    field.value = input.dataset.value;
                    break;
                case 'textarea':
                    input = $g(this).find('textarea[name="'+this.dataset.id+'"]')[0];
                    field.field_id = input.name;
                    if (input.dataset.texteditor) {
                        field.value = app.submissinForm.CKE[input.name].getData();
                    } else {
                        field.value = input.value.trim();
                    }
                    break;
                case 'select':
                    input = $g(this).find('select[name]')[0];
                    field.field_id = input.name;
                    field.value = input.value;
                    break;
                case 'radio':
                    $g(this).find('input[type="radio"][name]').each(function(){
                        if (!('value' in field)) {
                            field.value = '';
                        }
                        if (this.checked) {
                            field.field_id = this.name;
                            field.value = this.value;
                        }
                    });
                    break;
                case 'checkbox':
                    $g(this).find('input[type="checkbox"][name]').each(function(){
                        field.field_id = this.name;
                        if (!('value' in field)) {
                            field.value = [];
                        }
                        if (this.checked) {
                            field.value.push(this.value);
                        }
                    });
                    break;
                case 'url':
                case 'field-button':
                    $g(this).find('input[type="text"][name]').each(function(){
                        field.field_id = this.name;
                        if (!field.value) {
                            field.value = {
                                label: '',
                                link: ''
                            };
                        }
                        field.value[this.dataset.name] = this.value;
                    });
                    break;
                case 'image-field':
                    input = $g(this).find('input[name]')[0];
                    field.field_id = input.name;
                    value = {
                        src: '',
                        alt: ''
                    };
                    $g(this).find('.ba-uploaded-image-row').each(function(){
                        value.src = this.dataset.id;
                        value.alt = this.dataset.alt;
                    });
                    field.value = JSON.stringify(value);
                    break;
                case 'tag':
                    field.field_id = this.querySelector('.meta-tags').dataset.name;
                    value = [];
                    $g(this).find('.meta_tags option').each(function(){
                        value.push(this.value);
                    });
                    field.value = JSON.stringify(value);
                    break;
                case 'field-simple-gallery':
                case 'product-gallery':
                case 'field-slideshow':
                case 'product-slideshow':
                    field.field_id = this.dataset.id;
                    value = [];
                    $g(this).find('.ba-uploaded-image-row').each(function(){
                        let obj = {
                            img: this.dataset.id,
                            alt: this.dataset.alt,
                            unpublish: false
                        }
                        value.push(obj);
                    });
                    field.value = JSON.stringify(value);
                    break;
                case 'field-google-maps':
                    field.field_id = this.querySelector('input[data-autocomplete][name]').name;
                    let map = {
                        center: {
                            lat: app.fieldMaps[field.field_id].map.center.lat(),
                            lng: app.fieldMaps[field.field_id].map.center.lng()
                        },
                        zoom: app.fieldMaps[field.field_id].map.getZoom(),
                        marker: {
                            place: app.fieldMaps[field.field_id].input.value
                        }
                    }
                    if (app.fieldMaps[field.field_id].marker) {
                        map.marker.position = {
                            lat: app.fieldMaps[field.field_id].marker.position.lat(),
                            lng: app.fieldMaps[field.field_id].marker.position.lng()
                        }
                    }
                    field.value = JSON.stringify(map);
                    break;
                case 'field-video':
                    field.field_id = this.dataset.id;
                    value = {};
                    $g(this).find('[name][data-name]').each(function(){
                        if (this.dataset.name == 'file') {
                            value[this.dataset.name] = this.dataset.value;
                        } else {
                            value[this.dataset.name] = this.value;
                        }
                    });
                    if (value.id || value.file) {
                        field.value = JSON.stringify(value);
                    } else {
                        field.value = '';
                    }
                    break;
                case 'time':
                    field.field_id = this.dataset.id;
                    value = {};
                    $g(this).find('select[data-name]').each(function(){
                        value[this.dataset.name] = this.value;
                    });
                    if (this.hasAttribute('data-required') && (value.hours == '' || value.minuts == '')) {
                        field.value = '';
                    } else {
                        field.value = JSON.stringify(value);
                    }
                    break;
            }
            if (field.field_id) {
                fields[field.field_id] = field;
            }
            if (this.hasAttribute('data-required') &&
                ((field.type == 'url' && (!field.value.link || !field.value.label))
                    || (field.type == 'field-button' && !field.value.link)
                    || (field.type == 'image-field' && !document.querySelector('.ba-uploaded-image-row'))
                    || ((field.type == 'field-simple-gallery' || field.type == 'field-slideshow') && field.value == '[]') || !field.value)) {
                app.submissinForm.toggleAlertTooltip(true, this, this, 'THIS_FIELD_REQUIRED');
            }
        });
        fields.app_id = app.submissinForm.app_id;
        fields.id = app.submissinForm.id;
        fields.page_id = app.submissinForm.page_id;
        app.submissinForm.data = fields;
    },
    clearFields: () => {
        $g('.ba-item-submission-form .blog-post-submission-form-options-group[data-field-type]').each(function(){
            switch (this.dataset.fieldType) {
                case 'text':
                case 'date':
                case 'event-date':
                case 'number':
                case 'price':
                case 'category':
                    $g(this).find('input').val('');
                    break;
                case 'range':
                    $g(this).find('input').val(0);
                    $g(this).find('.ba-range-liner').css('width', 0);
                    break;
                case 'file':
                     $g(this).find('input[name]').val('').attr('data-value', '');
                    break;
                case 'textarea':
                    $g(this).find('textarea[name]').each(function(){
                        this.value = '';
                        if (this.dataset.texteditor) {
                            app.submissinForm.CKE[this.name].setData('');
                        }
                    });
                    break;
                case 'select':
                    $g(this).find('select[name] option').each(function(){
                        this.selected = false;
                    });
                    break;
                case 'radio':
                case 'checkbox':
                    $g(this).find('input[name]').each(function(){
                        this.checked = false;
                    });
                    break;
                case 'url':
                case 'field-button':
                    $g(this).find('input[type="text"][name]').val('');
                    break;
                case 'image-field':
                case 'field-simple-gallery':
                case 'product-gallery':
                case 'field-slideshow':
                case 'product-slideshow':
                    $g(this).find('.ba-uploaded-image-row').remove();
                    break;
                case 'tag':
                    $g(this).find('.meta_tags option, .tags-chosen').remove();
                    break;
                case 'field-google-maps':
                    this.querySelector('input[data-autocomplete][name]').value = '';
                    $g(this).find('.field-google-map-wrapper').each(function(){
                        app.fieldMaps[this.dataset.id].map.setCenter({
                            "lat" : 42.345573,
                            "lng" : -71.098326
                        });
                        app.fieldMaps[this.dataset.id].map.setZoom(14);
                        if (app.fieldMaps[this.dataset.id].marker) {
                            app.fieldMaps[this.dataset.id].marker.setMap(null);
                            app.fieldMaps[this.dataset.id].marker = null;
                        }
                    });
                    break;
                case 'field-video':
                    $g(this).find('[name][data-name]').val('');
                    break;
                case 'time':
                    $g(this).find('select[data-name] option').each(function(){
                        this.selected = false
                    });
                    break;
            }
        });
    },
    submit: (obj, btn) => {
        app.fetch(JUri+"index.php?option=com_gridbox&task=editor.submitNewItem", app.submissinForm.data).then((text) => {
            if (app.submissinForm.data.page_id == 0) {
                app.submissinForm.clearFields();
            }
            if (app.submissinForm.data.page_id != 0) {
                app.showNotice(app._('ITEM_SAVED_SUCCESFULLY'));
            } else if (obj.onsubmit.action == 'thank-you') {
                app.showNotice(obj.onsubmit.message);
            } else {
                window.location.href = btn.href;
            }
            btn.submited = false;
        });
    }
}

if (app.modules.initSubmissionForm) {
    app.initSubmissionForm(app.modules.initSubmissionForm.data, app.modules.initSubmissionForm.selector);
}