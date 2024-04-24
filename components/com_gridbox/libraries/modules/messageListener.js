/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.messageListener = function(){
	switch (uploadMode) {
        case 'association':
            fontBtn.dataset.id = app.messageData.id;
            fontBtn.value = app.messageData.title;
            $g(fontBtn).trigger('input')
            $g('#association-pages-list-modal').modal('hide');
            break;
        case 'fonts':
            var font = app.messageData.font.split(' '),
                callback = fontBtn.dataset.callback,
                subgroup = fontBtn.dataset.subgroup,
                group = fontBtn.dataset.group;
            if (!app.messageData.custom) {
                addFontLink(font);
            } else {
                addFontStyle(app.messageData);
            }
            fontBtn.value = font[0].replace(/\+/g, ' ')+' '+font[1].replace('i', 'italic');
            if (!subgroup) {
                app.edit.desktop[group]['font-family'] = font[0];
                app.edit.desktop[group]['font-weight'] = font[1];
                app.edit.desktop[group]['custom'] = app.messageData.custom;
            } else {
                app.edit.desktop[group][subgroup]['font-family'] = font[0];
                app.edit.desktop[group][subgroup]['font-weight'] = font[1];
                app.edit.desktop[group][subgroup]['custom'] = app.messageData.custom;
            }
            $g('#fonts-editor-dialog').modal('hide');
            setTimeout(function(){
                app[callback]();
            }, 300);
            app.addHistory();
            break;
        case 'addNewSlides': 
            var array = app.messageData,
                index = 1;
            for (var ind in app.edit.desktop.slides) {
                index++;
            }
            app.messageData.forEach(function(item){
                var obj = {
                        image: item.path,
                        index: index++,
                        type: 'image',
                        video: null,
                        caption: {
                            title: '',
                            description:''
                        },
                        button: {
                            href: '#',
                            type: 'ba-btn-transition',
                            title: '',
                            target: '_blank'
                        }
                    },
                    str = getSlideHtml(obj);
                app.editor.$g(app.selector+' .slideshow-content').append(str);
                str = '<div data-ba-slide-to="'+(obj.index - 1)+'" class="zmdi zmdi-circle"></div>';
                app.editor.$g(app.selector+' .ba-slideshow-dots').append(str);
                app.edit['desktop'].slides[obj.index] = {
                    image : obj.image,
                    type : obj.type,
                    link : "",
                    video : obj.video
                }
            });
            var object = {
                data : app.edit,
                selector : app.editor.app.edit
            }
            app.sectionRules();
            app.editor.app.checkModule('initItems', object);
            getSlideshowSorting();
            app.addHistory();
            $g('#uploader-modal').modal('hide');
            break;
        case 'addFieldSortingItem' :
            var array = app.messageData;
            for (var i = 0; i < array.length; i++) {
                let str = addFieldSortingList(array[i].path, '');
                app.addFieldSortingWrapper.append(str);
            }
            app.addFieldSortingWrapper.closest('.blog-post-editor-options-group').removeClass('ba-alert-label');
            app.addFieldSortingWrapper.closest('.field-sorting-wrapper')
                .find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
            $g('#uploader-modal').modal('hide');
            break;
        case 'reselctSlideshowFieldSortingImg':
            app.addFieldSortingItem.dataset.img = app.messageData[0].path;
            app.addFieldSortingItem.dataset.path = app.messageData[0].path;
            app.addFieldSortingItem.querySelector('img').src = JUri+app.messageData[0].path;
            var array = app.messageData[0].path.split('/');
            app.addFieldSortingItem.querySelector('.sorting-title').textContent = array[array.length - 1];
            $g('#uploader-modal').modal('hide');
            break;
        case 'uploadVariationsPhotos':
            var array = app.messageData;
            for (var i = 0; i < array.length; i++) {
                app.productImages[fontBtn].push(array[i].path);
            }
            updateOptionsImageCount(fontBtn);
            if (document.querySelector('#product-variations-photos-dialog').classList.contains('in')) {
                prepareVariationsPhotosDialog(fontBtn);
            }
            $g('#uploader-modal').modal('hide');
            break;
        case 'reselctFieldSortingImg':
            var array = app.messageData[0].path.split('/');
            fontBtn.dataset.image = app.messageData[0].path;
            fontBtn.dataset.path = app.messageData[0].path;
            fontBtn.value = array[array.length - 1];
            $g('#uploader-modal').modal('hide');
            break;
        case 'addSimpleImages' :
            let wrapper = app.editor.$g(app.selector+' .instagram-wrapper'),
                list = wrapper.find('.empty-list');
            app.messageData.forEach(function(image){
                var str = '<div class="ba-instagram-image" style="background-image: url(';
                str += JUri+image.path.replace(/\s/g, '%20')+')"><img src="'+JUri+image.path.replace(/\s/g, '%20')+
                    '" data-src="'+image.path+'"><div class="ba-simple-gallery-image"></div>'+
                    '<div class="ba-simple-gallery-caption"><div class="ba-caption-overlay"></div>'+
                    '<'+app.edit.tag+' class="ba-simple-gallery-title"></'+app.edit.tag+
                    '><div class="ba-simple-gallery-description"></div></div></div>';
                if (list.length > 0) {
                    list.before(str);
                } else {
                    wrapper.append(str);
                }
            });
            getSimpleSortingList();
            app.addHistory();
            app.editor.$g(app.selector+' .instagram-wrapper.simple-gallery-masonry-layout').each(function(){
                app.editor.setGalleryMasonryHeight(app.editor.app.edit);
            });
            $g('#uploader-modal').modal('hide');
            break;
        case 'reselectSimpleImage':
            var img = app.messageData[0].path;
            fontBtn.value = img;
            $g(fontBtn).trigger('input');
            $g('#uploader-modal').modal('hide');
            break;
        case 'selectFile' :
            var img = app.messageData[0].path;
            $g(fontBtn).val(img).trigger('input');
            $g('#uploader-modal').modal('hide');
            break;
        case 'slideImage' :
            var img = app.messageData[0].path;
            $g('#uploader-modal').modal('hide');
            fontBtn.value = img;
            $g(fontBtn).trigger('input');
            break;
        case 'reselectLibraryImage':
            var obj = {
                    id: app.itemDelete,
                    image: app.messageData[0].path
                };
            $g('.camera-container[data-id="'+obj.id+'"]').parent()
                .css('background-image', 'url('+obj.image.replace(/\s/g, '%20')+')')
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task=editor.setLibraryImage",
                data:{
                    object: JSON.stringify(obj)
                },
                complete: function(msg){
                    
                }
            });
            $g('#uploader-modal').modal('hide');
            break;
        case 'itemSimpleGallery' :
            var array = app.messageData,
                obj = {
                    data : 'simple-gallery',
                    selector : []
                }
            for (var i = 0; i < array.length; i++) {
                obj.selector.push(array[i].path)
            }
            app.editor.app.checkModule('loadPlugin' , obj);
            $g('#uploader-modal').modal('hide');
            break;
        case 'itemSlideshow':
        case 'itemSlideset':
        case 'itemCarousel':
        case 'itemContent-Slider':
            var array = app.messageData,
                obj = {
                    data : uploadMode.replace('item', '').toLowerCase(),
                    selector : []
                }
            for (var i = 0; i < array.length; i++) {
                obj.selector.push(array[i].path)
            }
            app.editor.app.checkModule('loadPlugin' , obj);
            $g('#uploader-modal').modal('hide');
            break;
        case 'contentSliderAdd':
            var array = app.messageData,
                data = [];
            for (var i = 0; i < array.length; i++) {
                data.push(array[i].path)
            }
            contentSliderAdd(data);
            $g('#uploader-modal').modal('hide');
            break;
        case 'itemImage' :
            var obj = {
                    data : 'image',
                    selector : app.messageData[0].path,
                }
            app.editor.app.checkModule('loadPlugin' , obj);
            if ($g('#uploader-modal').hasClass('in')) {
                $g('#uploader-modal').modal('hide');
            }
            break;
        case 'itemLogo' :
            var obj = {
                    data : 'logo',
                    selector : app.messageData[0].path,
                }
            app.editor.app.checkModule('loadPlugin' , obj);
            $g('#uploader-modal').modal('hide');
            break;
        case 'reselectSocialIcon':
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            fontBtn.dataset.icon = app.messageData;
            $g(fontBtn).trigger('change');
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'addSocialIcon':
            var obj = {
                    "icon" : app.messageData,
                    "title": app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, ''),
                    "link" : {
                        "link" : "",
                        "target" : "_blank"
                    }
                },
                str = '<a href="'+obj.link.link+'" target="'+obj.link.target,
                i = 0;
            str += '"><i class="'+obj.icon+' ba-btn-transition"></i></a>';
            for (var ind in app.edit.icons) {
                i++;
            }
            app.edit.icons[i] = obj;
            app.editor.$g(app.selector+' .ba-icon-wrapper').append(str);
            drawSocialIconsSorting();
            $g('#icon-upload-dialog').modal('hide');
            app.addHistory();
            break;
        case 'itemIcon' :
            var obj = {
                    data: 'icon',
                    selector: app.messageData,
                };
            app.editor.app.checkModule('loadPlugin' , obj);
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'scrolltopIcon' :
            var i = app.editor.document.getElementById(app.editor.app.edit),
                classList;
            i = i.querySelector('i.ba-btn-transition');
            classList = app.edit.icon;
            $g(i).removeClass(classList);
            classList = app.messageData;
            $g(i).addClass(classList);
            app.edit.icon = app.messageData;
            fontBtn.value = classList.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'smoothScrollingIcon' :
            var item = app.editor.document.querySelector('#'+app.editor.app.edit+' a'),
                i = item.querySelector('a i');
            if (i) {
                i.className = app.messageData;
            } else {
                i = document.createElement('i');
                i.className = app.messageData;
                item.appendChild(i);
            }
            app.edit.icon = app.messageData;
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'selectItemIcon' :
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            fontBtn.dataset.value = app.messageData;
            $g(fontBtn).trigger('input');
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'setBreadcrumbsIcon':
            fontBtn.dataset.value = app.messageData;
            $g(fontBtn).trigger('input');
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'reselectIcon' :
            var i = app.editor.document.getElementById(app.editor.app.edit),
                classList;
            i = i.querySelector('.ba-icon-wrapper i');
            classList = i.dataset.icon;
            $g(i).removeClass(classList);
            classList = app.messageData;
            $g(i).addClass(classList);
            i.dataset.icon = app.messageData;
            fontBtn.value = classList.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'addSearchIcon':
            var item = app.editor.document.getElementById(app.editor.app.edit),
                classList,
                i = item.querySelector('.ba-search-wrapper i');
            if (i) {
                classList = i.className;
                $g(i).removeClass(classList);
                classList = app.messageData;
                $g(i).addClass(classList);
            } else {
                i = document.createElement('i');
                i.className = app.messageData;
                item = item.querySelector('.ba-search-wrapper');
                item.appendChild(i);
            }
            app.edit.icon.icon = app.messageData;
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'addFieldIcon':
            var label = app.editor.$g(app.selector+' .ba-field-label'),
                i = label.find('> i')[0];
            if (i) {
                i.className = app.messageData;
            } else {
                i = document.createElement('i');
                i.className = app.messageData;
                label.prepend(i);
            }
            app.edit.icon = app.messageData;
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'addCartIcon' :
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            fontBtn.dataset.value = app.messageData;
            $g(fontBtn).trigger('change');
            $g('#icon-upload-dialog').modal('hide');
            break;    
        case 'addButtonIcon' :
            var item = app.editor.document.getElementById(app.editor.app.edit),
                i = item.querySelector('a i');
            if (!i) {
                i = document.createElement('i');
                item.querySelector('a').append(i);
            }
            i.className = app.messageData;
            fontBtn.value = app.messageData.replace(/zmdi zmdi-|fa fa-|flaticon-/, '');
            app.addHistory();
            $g('#icon-upload-dialog').modal('hide');
            break;
        case 'selectMarker' :
            fontBtn.value = app.messageData[0].path;
            $g(fontBtn).trigger('input change')
            $g('#uploader-modal').modal('hide');
        case 'reselectImage' :
            var img = app.editor.document.getElementById(app.editor.app.edit);
            app.edit.image = app.messageData[0].path;
            img = img.querySelector('img');
            img.src = JUri+app.messageData[0].url;
            fontBtn.value = app.edit.image;
            app.addHistory();
            $g('#uploader-modal').modal('hide');
            break;
        case 'selectImageCaption' :
            app.setValue(app.messageData[0].path, 'image');
            fontBtn.value = app.messageData[0].path;
            app.sectionRules();
            app.addHistory();
            $g('#uploader-modal').modal('hide');
            break;
        case 'image':
            var group = fontBtn.dataset.group,
                subgroup = fontBtn.dataset.subgroup,
                option = fontBtn.dataset.option,
                action  = fontBtn.dataset.action;
            app.setValue(app.messageData[0].path, 'background', 'image', 'image');
            if (app.edit.type) {
                app.setValue(app.messageData[0].path, group, 'image', subgroup);
            }
            app.edit[app.view].background.type = 'image';
            app[action]();
            fontBtn.value = app.messageData[0].path;
            $g('#uploader-modal').modal('hide');
            app.addHistory();
            break;
        case 'ckeImage':
            $g('.cke-upload-image').val(JUri+app.messageData[0].path);
            $g('#add-cke-image').addClass('active-button');
            $g('#uploader-modal').modal('hide');
            break;
        case 'attachmentFileField':
            var size = fontBtn.dataset.size * 1000,
                array = app.messageData[0].path.split('/'),
                types = fontBtn.dataset.types.replace(/ /g, '').split(',');
            if (size < app.messageData[0].size || types.indexOf(app.messageData[0].ext) == -1) {
                app.showNotice(app._('FILE_COULD_NOT_UPLOADED'), 'ba-alert');
                return false;
            }
            fontBtn.dataset.value = app.messageData[0].path;
            fontBtn.value = array[array.length - 1];
            $g(fontBtn).trigger('input');
            $g('#uploader-modal').modal('hide');
            break;
        case 'imageField':
            var array = app.messageData[0].path.split('/');
            fontBtn.value = array[array.length - 1];
            fontBtn.dataset.value = app.messageData[0].path;
            $g(fontBtn).trigger('input');
            $g('#uploader-modal').modal('hide');
            break;
        case 'shareImage':
            fontBtn.value = app.messageData[0].path;
            $g(fontBtn).trigger('input');
            $g('#uploader-modal').modal('hide');
            break;
        case 'introImage':
            var img = app.messageData[0].path,
                meta = app.editor.document.querySelector('meta[property="og:image"]'),
                intro = app.editor.document.querySelector('.ba-item-post-intro .intro-post-image');
            if (intro) {
                intro.style.backgroundImage = 'url('+JUri+img.replace(/\s/g, '%20')+')';
            }
            $g('.blog-post-editor-img-thumbnail').css({
                'background-image': 'url('+JUri+img.replace(/\s/g, '%20')+')'
            }).removeClass('empty-intro-image');
            $g('.intro-image').val(img).attr('data-value', img).prev().css({
                'background-image': 'url('+JUri+img.replace(/\s/g, '%20')+')'
            });
            $g('#uploader-modal').modal('hide');
            meta.content = JUri+img;
            break;
        case 'videoSource':
            var file = app.messageData[0].path,
                array = app.messageData[0].path.split('/'),
                ext = file.split('.');
            ext = ext[ext.length - 1];
            if (ext == 'mp4') {
                fontBtn.value = array[array.length - 1];
                fontBtn.dataset.value = file;
                $g(fontBtn).trigger('change');
            } else {
                app.showNotice(app._('NOT_SUPPORTED_FILE'), 'ba-alert');
            }
            $g('#uploader-modal').modal('hide');
            break;
        case 'pluginVideoSource':
            var file = app.messageData[0].path,
                ext = file.split('.');
            ext = ext[ext.length - 1];
            if (ext == 'mp4') {
                fontBtn.value = file;
                $g(fontBtn).trigger('change');
            } else {
                app.showNotice(app._('NOT_SUPPORTED_FILE'), 'ba-alert');
            }
            $g('#uploader-modal').modal('hide');
            break;
        case 'favicon' :
            var img = app.messageData[0].path,
                ext = img.split('.');
            ext = ext[ext.length - 1];
            if (ext == 'png') {
                $g('input.favicon').val(img);
            } else {
                app.showNotice(app._('NOT_SUPPORTED_FILE'), 'ba-alert');
            }
            $g('#uploader-modal').modal('hide');
            break;
        case 'lottie':
             var file = app.messageData[0].path,
                ext = file.split('.');
            ext = ext[ext.length - 1];
            if (ext == 'json') {
                fontBtn.value = file;
                $g(fontBtn).trigger('change');
            } else {
                app.showNotice(app._('NOT_SUPPORTED_FILE'), 'ba-alert');
            }
            $g('#uploader-modal').modal('hide');
            break;
        case 'LibraryImage':
            $g('.library-item-image').val(app.messageData[0].path);
            $g('#uploader-modal').modal('hide');
            break;
    }
}

app.modules.messageListener = true;
app.messageListener();