/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function getVisibleBranchClilds(parent)
{
    let childs = parent.find('> ul > li').length;
    parent.find('> ul > li.visible-branch').each(function(){
        childs += getVisibleBranchClilds(jQuery(this));
    });
    parent[0].style.setProperty('--category-childs', childs);

    return childs;
}

function getParentVisibleBranchClilds(el)
{
    let parents = el.parent().parents('li.visible-branch');
    if (parents.length) {
        let parent = parents[parents.length - 1];
        getVisibleBranchClilds(jQuery(parent));
    }
}

document.addEventListener('DOMContentLoaded', function(){
    var replaceTable = new Array('#','%','&','{','}','\\\\','<','>','\\*','\\?','/','\\$','!',"'",'"',':','@','\\+','`','\\|','='),
        oldName = '',
        moveTo = false;
    
    function createDocument()
    {
        jQuery('a[data-href]').each(function(){
            this.href = 'index.php?option=com_baforms&view=uploader&'+this.dataset.href;
        });
        jQuery('.modal').on('hide', function(){
            jQuery(this).addClass('ba-modal-close');
            setTimeout(function(){
                jQuery('.ba-modal-close').removeClass('ba-modal-close');
            }, 500)
        });
        jQuery('.pagination-limit input[type="text"]').each(function(){
            this.size = this.value.length;
        });

        jQuery('.pagination-limit .ba-custom-select').off('customAction').on('customAction', function(){
            var input = this.querySelector('input[type="text"]');
            input.size = input.value.length;
        });

        var allImages = new Array(),
            clientHeight = document.documentElement.clientHeight;

        getAllImages();
        function getAllImages()
        {
            allImages = new Array();
            jQuery('div.ba-image img').each(function(){
                var src = jQuery(this).attr('data-src');
                this.onload = function(){
                    jQuery(this).closest('td').addClass('loaded');
                }
                var obj = {
                    el : this,
                    img : src
                }
                allImages.push(obj)                    
            });
            jQuery('div.ba-image i.ba-file-icon').closest('td').addClass('loaded');
            checkImages();
        }

        jQuery(window).on('resize', function(){
            setTimeout(function(){
                checkImages();
            },500);            
        })

        function checkImages()
        {
            var newArray = new Array();
            clientHeight = document.documentElement.clientHeight
            allImages.forEach(function(el, ind){
                if (jQuery(el.el).offset().top < clientHeight * 2) {
                    el.el.src = el.img;
                } else {
                    newArray.push(el)
                }
            });
            allImages = newArray
        }

        jQuery('.ba-work-area').on('scroll',function(){
            checkImages();
            if (allImages.length == 0) {
                jQuery(this).off('scroll')
            }
        });

        jQuery('#adminForm').on('submit', function(){
            window.addEventListener("message", listenMessage, false);
        });

        function listenMessage(event)
        {
            if (event.origin == location.origin) {
                var url = location.href,
                    message = event.data;
                window.history.pushState(null, null, url);
                window.removeEventListener("message", listenMessage, false);
                url += ' #ba-media-manager > form';
                jQuery('#ba-media-manager').load(url, function(){
                    top.showNotice(message);
                    createDocument();
                });
            }
        }

        jQuery('#add-folder').on('click', function(event){
            event.preventDefault();
            var target = jQuery('#create-folder-modal');
            if (jQuery(this).hasClass('active-button')) {
                target.modal('hide');
                var name = jQuery('#create-folder-modal [name="new-folder"]').val();
                jQuery('#create-folder-modal [name="new-folder"]').val(name);
                Joomla.submitbutton('uploader.addfolder');
            }            
        });

        function showBranch()
        {
            jQuery('.ba-folder-tree i.zmdi-chevron-right').on('click', function(){
                let parent = jQuery(this).parent();
                getVisibleBranchClilds(parent);
                if (parent.hasClass('visible-branch')) {
                    parent.removeClass('visible-branch');
                } else {
                    parent.addClass('visible-branch');
                }
                getParentVisibleBranchClilds(parent);
            });
        }

        showBranch();

        var notification = top.document.getElementById('ba-notification');
        
        function getFileSize(size)
        {
            size = size / 1024;
            size = Math.floor(size);
            if (size >= 1024) {
                size = size / 1024;
                size = Math.floor(size);
                size = size+' MB';
            } else {
                size = size+' KB';
            }

            return size;
        }

        function UploadFiles()
        {
            if (files.length > 0) {
                var file = files.pop(),
                    XHR = new XMLHttpRequest(),
                    url = "index.php?option=com_baforms&&task=uploader.uploadAjax&file="+file.name;
                if (XHR.upload && file.size <= 66000000) {
                    var upload = jQuery('#uploading-media').val(),
                        str = '';
                    upload = JSON.parse(upload);
                    str += upload.const+'<img src="'+upload.url;
                    str += 'components/com_baforms/assets/images/reload.svg"></img>';
                    if (notification.lastElementChild.innerHTML != str) {
                        notification.lastElementChild.innerHTML = str;
                        notification.className = 'notification-in';
                    }
                    XHR.onreadystatechange = function(e) {
                        if (XHR.readyState == 4) {
                            if (XHR.responseText) {
                                try {
                                    var obj = JSON.parse(XHR.responseText);
                                    if (obj.size == 0) {
                                        var dir = jQuery('input[name="current-dir"]').val();
                                        jQuery('#file-upload-form [name="current_folder"]').val(dir+'/');
                                        jQuery('#file-upload-form form')[0].submit();
                                        return false;
                                    }
                                } catch (error) {
                                    var dir = jQuery('input[name="current-dir"]').val();
                                    jQuery('#file-upload-form [name="current_folder"]').val(dir+'/');
                                    jQuery('#file-upload-form form')[0].submit();
                                    return false;
                                }
                                var str = '<td class="select-td loaded',
                                    img = new Image(),
                                    canvas = document.createElement('canvas'),
                                    ratio = 1,
                                    imageTypes = new Array('jpg', 'png', 'gif', 'jpeg', 'svg', 'ico', 'webp');
                                if (jQuery.inArray(obj.ext, imageTypes) == -1) {
                                    str += ' ba-file-wrapper';
                                }
                                str += '"><div class="ba-image ba-hide-checkbox">';
                                if (jQuery.inArray(obj.ext, imageTypes) != -1) {
                                    str += '<img>';
                                } else {
                                    str += '<i class="zmdi zmdi-file ba-file-icon"></i>';
                                }
                                str += '<input class="select-item"';
                                str += ' type="checkbox" name="ba-rm[]" value="'+obj.name+'">';
                                str += '<input type="hidden" value=""';
                                str += ' class="ba-obj"><i class="zmdi zmdi-circle-o ba-icon-md"></i><i class="zmdi zmdi-check"></i>';
                                str += '</div></td><td class="draggable-handler">'+obj.name+'</td>';
                                str += '<td class="draggable-handler">'+getFileSize(obj.size);
                                str += '</td>';
                                var tr = document.createElement('tr');
                                tr.className = 'ba-images';
                                tr.dataset.ext = obj.ext;
                                jQuery(tr).append(str);
                                jQuery(tr).find('input.ba-obj').val(JSON.stringify(obj));
                                if (jQuery.inArray(obj.ext, imageTypes) != -1) {
                                    img.onload = function(){
                                        ratio = this.width / this.height;
                                        if (this.width > this.height) {
                                            canvas.width = 100;
                                            canvas.height = 100 / ratio;
                                        } else {
                                            canvas.height = 100;
                                            canvas.width = 100 * ratio;
                                        }
                                        canvas.getContext('2d').drawImage(this, 0, 0, canvas.width, canvas.height);
                                        jQuery(tr).find('img')[0].onload = function(){
                                            var names = new Array(),
                                                flag = true,
                                                max = jQuery('.pagination-limit input').attr('data-value') * 1,
                                                count = jQuery('table.ba-items-list tbody tr').length * 1;
                                            if (count >= max) {
                                                return false;
                                            }
                                            jQuery('table.ba-items-list tbody tr.ba-images').each(function(){
                                                var name = jQuery(this).find('td.select-td').next().text();
                                                names[0] = jQuery.trim(name);
                                                names[1] = obj.name;
                                                if (jQuery.trim(name) == obj.name) {
                                                    jQuery(this).replaceWith(jQuery(tr));
                                                    flag = false;
                                                    return false;
                                                }
                                                names = names.sort();
                                                if (names[0] == obj.name) {
                                                    jQuery(this).before(tr);
                                                    flag = false;
                                                    return false;
                                                }
                                            });
                                            if (flag) {
                                                jQuery('table.ba-items-list tbody').append(tr);
                                            }
                                            makeDrag();
                                        }
                                        jQuery(tr).find('img')[0].src = canvas.toDataURL();
                                    }
                                    img.src = obj.url;
                                } else {
                                    var names = new Array(),
                                        flag = true,
                                        max = jQuery('.pagination-limit input').attr('data-value') * 1,
                                        count = jQuery('table.ba-items-list tbody tr').length * 1;
                                    if (count < max) {
                                        jQuery('table.ba-items-list tbody tr.ba-images').each(function(){
                                            var name = jQuery(this).find('td.select-td').next().text();
                                            names[0] = jQuery.trim(name);
                                            names[1] = obj.name;
                                            if (jQuery.trim(name) == obj.name) {
                                                jQuery(this).replaceWith(jQuery(tr));
                                                flag = false;
                                                return false;
                                            }
                                            names = names.sort();
                                            if (names[0] == obj.name) {
                                                jQuery(this).before(tr);
                                                flag = false;
                                                return false;
                                            }
                                        });
                                        if (flag) {
                                            jQuery('table.ba-items-list tbody').append(tr);
                                        }
                                        makeDrag();
                                    }
                                }
                            }
                            UploadFiles();
                        }
                    };
                    url += '&folder='+jQuery('input[name="current-dir"]').val();
                    XHR.open("POST", url, true);
                    XHR.send(file);
                } else {
                    if (total > max) {
                        top.showNotice(jQuery('#post-max-error').val());
                    } else {
                        var upload = jQuery('#uploading-media').val(),
                            str = '';
                        upload = JSON.parse(upload);
                        str += upload.const+'<img src="'+upload.url;
                        str += 'administrator/components/com_baforms/assets/images/reload.svg"></img>';
                        notification.lastElementChild.innerHTML = str;
                        notification.className = 'notification-in';
                        Joomla.submitbutton('uploader.upload');
                    }
                }
            } else {
                var sBackdrop = top.document.getElementsByClassName('saving-backdrop')[0];
                sBackdrop.className += ' animation-out';
                setTimeout(function(){
                    top.document.getElementsByTagName('body')[0].removeChild(sBackdrop);
                }, 300);                
                top.showNotice(jQuery('#success-upload').val());
            }
        }

        window.uploadCallback = function(array){
            for (var i = 0; i < array.length; i++) {
                var obj = array[i],
                    str = '<td class="select-td loaded',
                    ratio = 1,
                    imageTypes = new Array('jpg', 'png', 'gif', 'jpeg', 'svg', 'ico', 'webp');
                if (jQuery.inArray(obj.ext, imageTypes) == -1) {
                    str += ' ba-file-wrapper';
                }
                str += '"><div class="ba-image ba-hide-checkbox">';
                if (jQuery.inArray(obj.ext, imageTypes) != -1) {
                    str += '<img src="'+obj.url+'">';
                } else {
                    str += '<i class="zmdi zmdi-file ba-file-icon"></i>';
                }
                str += '<input class="select-item"';
                str += ' type="checkbox" name="ba-rm[]" value="'+obj.name+'">';
                str += '<input type="hidden" value=""';
                str += ' class="ba-obj"><i class="zmdi zmdi-circle-o ba-icon-md"></i><i class="zmdi zmdi-check"></i>';
                str += '</div></td><td class="draggable-handler">'+obj.name+'</td>';
                str += '<td class="draggable-handler">'+getFileSize(obj.size);
                str += '</td>';
                var tr = document.createElement('tr');
                tr.className = 'ba-images';
                tr.dataset.ext = obj.ext;
                jQuery(tr).append(str);
                jQuery(tr).find('input.ba-obj').val(JSON.stringify(obj));
                var names = new Array(),
                    flag = true,
                    max = jQuery('.pagination-limit input').attr('data-value') * 1,
                    count = jQuery('table.ba-items-list tbody tr').length * 1;
                if (count >= max) {
                    return false;
                }
                jQuery('table.ba-items-list tbody tr').each(function(){
                    var name = jQuery(this).find('td.select-td').next().text();
                    names[0] = jQuery.trim(name);
                    names[1] = obj.name;
                    if (jQuery.trim(name) == obj.name) {
                        jQuery(this).replaceWith(jQuery(tr));
                        flag = false;
                        return false;
                    }
                    names = names.sort();
                    if (names[0] == obj.name) {
                        jQuery(this).before(tr);
                        flag = false;
                        return false;
                    }
                });
                if (flag) {
                    jQuery('table.ba-items-list tbody').append(tr);
                }
                makeDrag();
            }
            var sBackdrop = top.document.querySelector('.saving-backdrop');
            sBackdrop.classList.add('animation-out');
            setTimeout(function(){
                sBackdrop.parentNode.removeChild(sBackdrop);
            }, 300);
            top.showNotice(jQuery('#success-upload').val());
        }

        var files = new Array(),
            total = 0,
            max = jQuery('#post-max-size').val() * 1;
        
        jQuery('#show-upload').on('click', function(){
            jQuery('#file-upload-form [type="file"]').trigger('click');
        });

        jQuery('#file-upload-form [type="file"]').off('change').on('change', function(event){
            if (this.files.length > 0) {
                total = 0;
                var invalidExt = new Array();
                for (var i = 0; i < this.files.length; i++) {
                    var fileName = this.files[i].name.split('.'),
                        ext = fileName[fileName.length - 1].toLowerCase();
                    if (jQuery.inArray(ext, $fileTypes) == -1) {
                        invalidExt.push(ext);
                    } else {
                        total += this.files[i].size;
                        files.push(this.files[i]);
                    }
                }
                if (files.length == 0) {
                    top.showNotice(top.formsLanguage['NOT_SUPPORTED_FILE'], 'ba-alert');
                } else {
                    var sBackdrop = jQuery('<div/>', {
                        'class' : 'saving-backdrop'
                    });
                    top.document.getElementsByTagName('body')[0].appendChild(sBackdrop[0]);
                    UploadFiles();
                }
            }
        }).on('click', function(event){
            
        });

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js';
        script.onload = makeDrag;
        document.head.append(script);

        jQuery('body').on('mousedown', function(){
            jQuery('.context-active').removeClass('context-active');
            jQuery('.ba-context-menu').hide();
        });

        function checkContext(context, deltaY, deltaX)
        {
            if (deltaX - context.width() < 0) {
                context.addClass('ba-left');
            } else {
                context.removeClass('ba-left');
            }
            if (deltaY - context.height() < 0) {
                context.addClass('ba-top');
            } else {
                context.removeClass('ba-top');
            }
        }

        jQuery('#apply-rename').on('click', function(event){
            event.preventDefault();
            if (!jQuery(this).hasClass('active-button')) {
                return false;
            }
            var name = jQuery('.new-name').val(),
                target;
            if (currentItem.hasClass('ba-images')) {
                target = currentItem.find('.ba-obj').val();
                target = JSON.parse(target);
                target = target.path;
            } else {
                target = currentItem.find('a.folder-list').attr('href');
                if (!target) {
                    target = currentItem.attr('href');
                }
                target = target.split('&');
                for (var i = 0; i < target.length; i++) {
                    target[i] = target[i].split('=');
                    if (target[i][0] == 'folder') {
                        target = target[i][1];
                        break;
                    }
                }
            }
            jQuery('#rename-modal').modal('hide');
            jQuery.ajax({
                type : "POST",
                dataType : 'text',
                url : "index.php?option=com_baforms&view=uploader&task=uploader.renameTarget",
                data : {
                    'ba_target' : target,
                    'ba_name' : name
                },
                success : function(msg){
                    msg = JSON.parse(msg);
                    var url = location.href,
                        search = decodeURIComponent(location.search);
                    if (search.indexOf(target) >= 0) {
                        search = search.replace(target, msg.data);
                        url = url.replace(location.search, search);
                        window.history.pushState(null, null, url);
                    }
                    top.showNotice(msg.message);
                    url += ' #ba-media-manager > form';
                    jQuery('#ba-media-manager').load(url, function(){
                        createDocument();
                    });
                }
            });
        });

        jQuery('.ba-context-menu .rename').on('mousedown', function(){
            var target = '',
                name = '';
            if (currentItem.hasClass('ba-images')) {
                target = currentItem.find('.ba-obj').val();
                target = JSON.parse(target);
                target = target.name.split('.');
                for (var i = 0; i < target.length - 1; i++) {
                    name += target[i];
                }
            } else {
                name = currentItem.find('a.folder-list').text();
                if (!name) {
                    name = currentItem.text();
                }
                name = jQuery.trim(name);
            }
            oldName = name;
            jQuery('.new-name').val(name);
            jQuery('#apply-rename').removeClass('active-button');
            jQuery('#rename-modal').modal();
        });

        jQuery('.ba-context-menu .download').on('mousedown', function(){
            var str = currentItem.find('.ba-obj').val(),
                obj = JSON.parse(str),
                a = document.createElement('a');
            a.setAttribute('download', '');
            a.href = obj.url;
            a.click();
        });

        jQuery('.ba-context-menu .upload-file').on('mousedown', function(){
            jQuery('#show-upload').trigger('click');
        });
        
        jQuery('.ba-context-menu .create-folder').on('mousedown', function(){
            jQuery('#show-folder').trigger('click');
        });

        jQuery('.ba-context-menu .delete').on('mousedown', function(){
            deleteMode = 'ajax';
            jQuery('#delete-modal').modal();
        });

        var currentItem;

        jQuery('.close-media').on('click', function(){
            top.$f('#uploader-modal').modal('hide');
        });

        jQuery('.media-fullscrean').on('click', function(){
            var wind = top.document.getElementById('uploader-modal');
            if (!jQuery(wind).hasClass('fullscrean')) {
                jQuery(wind).addClass('fullscrean');
                jQuery(this).removeClass('zmdi-fullscreen').addClass('zmdi-fullscreen-exit');
            } else {
                jQuery(wind).removeClass('fullscrean');
                jQuery(this).addClass('zmdi-fullscreen').removeClass('zmdi-fullscreen-exit');
            }
        });

        jQuery('body').on('contextmenu', function(event){
            event.preventDefault();
        });

        jQuery('.ba-work-area, .ba-folder-tree > ul').on('contextmenu', function(event){
            jQuery('.context-active').removeClass('context-active');
            var deltaX = document.documentElement.clientWidth - event.pageX,
                deltaY = document.documentElement.clientHeight - event.clientY,
                context;
            setTimeout(function(){
                context = jQuery('.empty-context-menu');
                context.css({
                    'top' : event.pageY,
                    'left' : event.pageX,
                }).show();
                checkContext(context, deltaY, deltaX);
            }, 50);
        });

        jQuery('tbody, .ba-folder-tree').on('contextmenu', 'tr, a', function(event){
            event.stopPropagation();
            event.preventDefault();
            jQuery('.context-active').removeClass('context-active');
            jQuery(this).addClass('context-active');
            var deltaX = document.documentElement.clientWidth - event.pageX,
                deltaY = document.documentElement.clientHeight - event.clientY,
                context;
            currentItem = jQuery(this)
            if (jQuery(this).hasClass('ba-images')) {
                var imageTypes = new Array('jpg', 'png', 'jpeg', 'webp')
                if (imageTypes.indexOf(this.dataset.ext) != -1) {
                    jQuery('.files-context-menu').find('.edit-image').css('display', '');
                } else {
                    jQuery('.files-context-menu').find('.edit-image').hide();
                }
                setTimeout(function(){
                    context = jQuery('.files-context-menu');
                    context.css({
                        'top' : event.pageY,
                        'left' : event.pageX,
                    }).show();
                    checkContext(context, deltaY, deltaX);
                }, 50);
            } else {
                setTimeout(function(){
                    context = jQuery('.folders-context-menu');
                    context.css({
                        'top' : event.pageY,
                        'left' : event.pageX,
                    }).show();
                    checkContext(context, deltaY, deltaX);
                }, 50);
            }
        });

        jQuery('#move-to').on('mousedown', function(){
            if (!jQuery(this).hasClass('active')) {
                return false;
            }
            moveTo = true;
            jQuery('#move-to-modal .availible-folders .active').removeClass('active');
            jQuery('#move-to-modal .active-button').removeClass('active-button');
            jQuery('#move-to-modal').modal();
        });

        jQuery('.ba-context-menu .move-to').on('mousedown', function(){
            moveTo = false;
            jQuery('#move-to-modal .availible-folders .active').removeClass('active');
            jQuery('#move-to-modal .active-button').removeClass('active-button');
            jQuery('#move-to-modal').modal();
        });

        jQuery('#move-to-modal .availible-folders').on('click', 'li', function(event){
            event.stopPropagation();
            jQuery('#move-to-modal .availible-folders .active').removeClass('active');
            jQuery(this).addClass('active');
            jQuery('.apply-move').addClass('active-button');
        });

        jQuery('.ba-tooltip').each(function(){
            jQuery(this).parent().children().first().on('mouseenter', function(){
                var coord = this.getBoundingClientRect(),
                    top = coord.top,
                    data = jQuery(this).parent().find('.ba-tooltip').html(),
                    center = (coord.right - coord.left) / 2;
                    className = jQuery(this).parent().find('.ba-tooltip')[0].className;
                center = coord.left + center;
                if (jQuery(this).parent().find('.ba-tooltip').hasClass('ba-bottom')) {
                    top = coord.bottom;
                }
                jQuery('body').append('<span class="'+className+'">'+data+'</span>');
                var tooltip = jQuery('body > .ba-tooltip').last(),
                    width = tooltip.outerWidth(),
                    height = tooltip.outerHeight();
                if (tooltip.hasClass('ba-top') || tooltip.hasClass('ba-help')) {
                    top -= (15 + height);
                    center -= (width / 2)
                }
                if (tooltip.hasClass('ba-bottom')) {
                    top += 10;
                    center -= (width / 2)
                }
                tooltip.css({
                    'top' : top+'px',
                    'left' : center+'px'
                });
            }).on('mouseleave', function(){
                var tooltip = jQuery('body').find(' > .ba-tooltip');
                tooltip.addClass('tooltip-hidden');
                setTimeout(function(){
                    tooltip.remove();
                }, 500);
            });
        });

        jQuery('.apply-move').on('click', function(event){
            event.preventDefault();
            var path = jQuery('#move-to-modal .availible-folders .active').attr('data-path'),
                target = '';
            if (!path) {
                return false;
            }
            if (!moveTo) {
                if (currentItem.hasClass('ba-images')) {
                    target = currentItem.find('.ba-obj').val();
                    target = JSON.parse(target);
                    target = target.path;
                } else {
                    target = currentItem.find('a.folder-list').attr('href');
                    if (!target) {
                        target = currentItem.attr('href');
                    }
                    target = target.split('&');
                    for (var i = 0; i < target.length; i++) {
                        target[i] = target[i].split('=');
                        if (target[i][0] == 'folder') {
                            target = target[i][1];
                            break;
                        }
                    }
                }
            } else {
                target = new Array();
                var obj,
                    parent;
                jQuery('input.select-item').each(function(){
                    if (jQuery(this).prop('checked')) {
                        parent = jQuery(this).parent();
                        if (parent.hasClass('ba-image')) {
                            obj = parent.find('.ba-obj').val();
                            obj = JSON.parse(obj);
                            target.push(obj.path);
                        } else {
                            parent = parent.closest('tr');
                            obj = parent.find('a.folder-list').attr('href');
                            obj = obj.split('&');
                            for (var i = 0; i < obj.length; i++) {
                                obj[i] = obj[i].split('=');
                                if (obj[i][0] == 'folder') {
                                    target.push(obj[i][1]);
                                    break;
                                }
                            }
                        }
                    }
                });
                target = target.join(';');
            }
            jQuery.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_baforms&view=uploader&task=uploader.moveTarget",
                data:{
                    'ba_target' : target,
                    'ba_path' : path,
                    'ba_flag' : moveTo
                },
                success: function(msg){
                    msg = JSON.parse(msg);
                    var url = location.href+' #ba-media-manager > form',
                        search = decodeURIComponent(location.search);
                    if (search.indexOf(target) >= 0) {
                        search = search.replace(target, msg.data);
                        url = url.replace(location.search, search);
                        window.history.pushState(null, null, url);
                    };
                    top.showNotice(msg.message);
                    jQuery('#ba-media-manager').load(url, function(){
                        createDocument()
                    });
                }
            });
            jQuery('#move-to-modal').modal('hide');
        });

        jQuery('#move-to-modal').on('show', function(){
            jQuery('#move-to-modal .availible-folders > ul > li > ul').remove();
            var ul = jQuery('.ba-folder-tree > ul').clone(),
                li,
                target = '',
                a,
                text;
            ul.find('.active').removeClass('active');
            ul.find('li').each(function(){
                li = jQuery(this);
                target = '';
                a = jQuery(this).find('> a');
                text = a.text();
                text = jQuery.trim(text);
                target = a.attr('href');
                target = target.split('&');
                for (var i = 0; i < target.length; i++) {
                    target[i] = target[i].split('=');
                    if (target[i][0] == 'folder') {
                        target = target[i][1];
                        break;
                    }
                };
                var span = document.createElement('span'),
                    i = document.createElement('i');
                i.className = 'zmdi zmdi-folder';
                text = document.createTextNode(text);
                span.appendChild(i);
                span.appendChild(text);
                li.find('> a').remove();
                li.attr('data-path', target).prepend(span);
            })
            jQuery('#move-to-modal .availible-folders > ul > li').append(ul);
            ul.find('i.zmdi-chevron-right').on('click', function(){
                let parent = jQuery(this).parent();
                getVisibleBranchClilds(parent);
                if (parent.hasClass('visible-branch')) {
                    parent.removeClass('visible-branch');
                } else {
                    parent.addClass('visible-branch');
                }
                getParentVisibleBranchClilds(parent);
            });
        });

        jQuery('.ba-custom-select > i, div.ba-custom-select input').on('click', function(event){
            event.stopPropagation()
            var $this = jQuery(this),
                parent = $this.parent();
            parent.find('ul').addClass('visible-select');
            parent.find('li').off('click').one('click', function(){
                var text = jQuery(this).text(),
                    val = jQuery(this).attr('data-value');
                parent.find('input').val(text).attr('data-value', val);
                parent.trigger('customHide');
            });
            parent.trigger('show');
            setTimeout(function(){
                jQuery('body').one('click', function(){
                    jQuery('.visible-select').removeClass('visible-select');
                });
            }, 50);
        });

        jQuery('.pagination-limit .ba-custom-select').on('show', function(){
            var value = jQuery(this).find('input').attr('data-value');
            jQuery(this).find('li').each(function(){
                var $this = jQuery(this).removeClass('selected');
                $this.find('i.zmdi-check').remove();
                if ($this.attr('data-value') == value) {
                    $this.addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
                }
            });
        });

        jQuery('.pagination-limit .ba-custom-select').on('customHide', function(){
            var a = jQuery(this).find('a'),
                url = ' #ba-media-manager > form';
                limit = jQuery(this).find('input').attr('data-value');
            a[0].href += '&ba_limit='+limit;
            window.history.pushState(null, null, a[0].href);
            jQuery('#ba-media-manager').load(a[0].href+url, function(){
                createDocument();
            });
            a.trigger('click');
        })

        jQuery('.ba-folder-tree, tbody, .ba-breadcrumb, .pagination').on('click', 'a', function(event){
            event.preventDefault();
            if (jQuery(this).attr('href') && !this.parentNode.classList.contains('disabled')) {
                var url = jQuery(this)[0].href;
                window.history.pushState(null, null, url);
                url += ' #ba-media-manager > form';
                jQuery('#ba-media-manager').load(url, function(){
                    createDocument();
                });
            }
        });

        var deleteMode = ''

        jQuery('#apply-delete').on('click', function(event){
            event.preventDefault();
            if (deleteMode == 'default') {
                Joomla.submitbutton('uploader.delete');
            } else {
                var target = '';
                if (currentItem.hasClass('ba-images')) {
                    target = currentItem.find('.ba-obj').val();
                    target = JSON.parse(target);
                    target = target.path;
                } else {
                    target = currentItem.find('a.folder-list').attr('href');
                    if (!target) {
                        target = currentItem.attr('href');
                    }
                    target = target.split('&');
                    for (var i = 0; i < target.length; i++) {
                        target[i] = target[i].split('=');
                        if (target[i][0] == 'folder') {
                            target = target[i][1];
                            break;
                        }
                    }
                }
                jQuery.ajax({
                    type:"POST",
                    dataType:'text',
                    url:"index.php?option=com_baforms&view=uploader&task=uploader.deleteTarget",
                    data:{
                        'ba_target' : target
                    },
                    success : function(msg) {
                        msg = JSON.parse(msg);
                        var url = location.href,
                            search = decodeURIComponent(location.search);
                        if (search.indexOf(target) >= 0) {
                            var pos = target.indexOf('/'+IMAGE_PATH+'/'),
                                repl = target.substr(0, pos)+'/'+IMAGE_PATH+'/';
                            search = search.replace(target, repl);
                            url = url.replace(location.search, search);
                            window.history.pushState(null, null, url);
                        }
                        top.showNotice(msg.message);
                        url += ' #ba-media-manager > form';
                        jQuery('#ba-media-manager').load(url, function(){
                            createDocument();
                        });
                    }
                });
            }
            jQuery('#delete-modal').modal('hide');
        });

        jQuery('#delete-items').on('click', function(event){
            event.preventDefault();
            jQuery('.select-item').each(function(){
                if (jQuery(this).prop('checked')) {
                    deleteMode = 'default';
                    jQuery('#delete-modal').modal();
                    return false;
                }
            });
        });

        function addActive()
        {
            var checked = false,
                imageChecked = false;
            jQuery('.select-item').each(function(){
                if (jQuery(this).prop('checked')) {
                    checked = true;
                }
                if (jQuery(this).closest('.ba-images').hasClass('ba-images') && jQuery(this).prop('checked')) {
                    imageChecked = true;
                }
            });
            if (checked) {
                jQuery('#delete-items, #move-to').addClass('active');
            } else {
                jQuery('#delete-items, #move-to').removeClass('active');
            }
            if (imageChecked) {
                jQuery('#ba-apply').addClass('active');
            } else {
                jQuery('#ba-apply').removeClass('active');
            }
        }

        jQuery('.ba-items-list').on('click', 'td.select-td i.zmdi-check', function(){
            var td = jQuery(this).closest('td.select-td');
            if (td.find('.select-item').prop('checked')) {
                td.find('.select-item').prop('checked', false);
            } else {
                var modal = top.document.getElementById('uploader-modal'),
                    flag = modal.dataset.check != 'single';
                if (!flag) {
                    jQuery('.select-item').prop('checked', false);
                }
                td.find('.select-item').prop('checked', true);
            }
            addActive();
        });

        jQuery('.check-all').on('click', function(){
            jQuery('#check-all').trigger('click');
        })

        jQuery('#check-all').on('click', function(){
            var modal = top.document.getElementById('uploader-modal'),
                flag = modal.dataset.check != 'single',
                checked = this.checked;
            if (flag) {
                jQuery('.select-item').each(function(){
                    this.checked = checked;
                });
                addActive();
            } else {
                this.checked = false;
            }
        });
        
        jQuery('#show-folder').on('click', function(){
            var target = jQuery('#create-folder-modal');
            target.find('[name="new-folder"]').val('');
            jQuery('#add-folder').removeClass('active-button');
            target.modal();
        });

        jQuery('.new-name').on('input', function(){
            var name = jQuery(this).val(),
                flag = true,
                patt;
            for (var i = 0; i < replaceTable.length; i++) {
                patt = new RegExp(replaceTable[i]);
                if (patt.test(name)) {
                    flag = false;
                    break;
                }
            }
            if (jQuery.trim(name) && flag && name != oldName) {
                jQuery('#apply-rename').addClass('active-button');
            } else {
                jQuery('#apply-rename').removeClass('active-button');
            }
        });

        jQuery('#create-folder-modal [name="new-folder"]').on('input', function(){
            var name = jQuery(this).val(),
                flag = true,
                patt;
            for (var i = 0; i < replaceTable.length; i++) {
                patt = new RegExp(replaceTable[i]);
                if (patt.test(name)) {
                    flag = false;
                    break;
                }
            }
            if (jQuery(this).val() && flag) {
                jQuery('#add-folder').addClass('active-button');
            } else {
                jQuery('#add-folder').removeClass('active-button');
            }
        });
        
        function sendMessage()
        {
            var msg = [],
                target = top.document.getElementById('uploader-modal'),
                flag = true;
            if (target.dataset.check == 'single') {
                flag = false;
            }
            if (flag) {
                jQuery('.select-item').each(function(){
                    if (jQuery(this).prop('checked')) {
                        var item = {},
                            values = jQuery(this).parent().find('.ba-obj').val();
                        if (values) {
                            item = JSON.parse(values);
                            item.path = IMAGE_PATH+item.path;
                            msg.push(item);
                        }
                    }
                });
            } else {
                jQuery('.select-item').each(function(){
                    if (jQuery(this).prop('checked')) {
                        var value = jQuery(this).parent().find('.ba-obj').val();
                        if (value) {
                            var item = {};
                            item = JSON.parse(value);
                            item.path = IMAGE_PATH+item.path
                        }
                        msg = item;
                        return false;
                    }
                });
            }
            if (msg.length > 0 || typeof(msg) == 'object') {
                top.app.messageListener(msg);
                jQuery('#check-all').prop('checked', false);
                jQuery('.select-item').prop('checked', false);
                jQuery('.active').removeClass('active');
                top.$f('#uploader-modal').modal('hide');
            }            
        }
        
        jQuery('#ba-apply').on('click', function(){
            sendMessage();
        });
    }
    createDocument();    
});