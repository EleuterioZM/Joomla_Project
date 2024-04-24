/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.shareComment = null;
getCommentUser();

app.checkReview = function(){
    if (app.hash && app.hash.match(/reviewID-\d+/) && !document.querySelector(app.hash)) {
        let id = app.hash.replace('#reviewID-', '');
        app.getCommentsPatterns('reviews', 2, id);
    }
}

app.getCommentsRecaptchaResponse = function(elem){
    let response = '';
    try {
        response = grecaptcha.getResponse(recaptchaObject.data[elem.id]);
    } catch (err) {
        console.info(err)
    }

    return response != '';
}

app.getVkUserInfo = function(response){
    if (response && response.status == 'connected') {
        var obj = {
            user_ids: response.session.mid,
            fields: 'photo_50, first_name, last_name',
            v: 5.101
        };
        VK.Api.call('users.get', obj, function(data){
            let object = {
                name: data.response[0].first_name+' '+data.response[0].last_name,
                avatar: data.response[0].photo_50,
                id: obj.user_ids
            }
            app.loginCommentSocial(object);
        });
    }
}

app.loginCommentSocial = function(data){
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=comments.loginSocial",
        data: data,
        complete: function(msg){
            app.setCommentsUser(msg.responseText);
        }
    });
}

app.getReviewById = function($this){
    let wrapper = $g($this).closest('.user-comment-container-wrapper'),
        id = wrapper.find('.user-comment-wrapper')[0].id.match(/\d+/),
        replyName = wrapper.find('.comment-reply-name').text();
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=reviews.getReviewById",
        data: {
            page: themeData.id,
            id: id[0],
            replyName: replyName,
            'sort-by': $g('.ba-comments-total-count-wrapper select').val()
        },
        success: function(msg){
            let object = JSON.parse(msg),
                div = document.createElement('div');
            div.innerHTML = object.comment;
            wrapper.closest('.ba-item').find('.ba-comments-total-count-wrapper').html(object.commentsCount);
            wrapper.html(div.querySelector('.user-comment-container-wrapper').innerHTML);
            wrapper.find('.ba-comments-login-wrapper').html(object.login);
            wrapper.find('.ba-comment-message-wrapper').html(object.commentMessage);
            wrapper.find('.comment-reply-form-wrapper .ba-submit-comment').text(gridboxLanguage['COMMENT']);
            wrapper.find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper').find('.ba-comments-attachments-wrapper')
                .remove();
            wrapper.find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper').find('.ba-comment-message')
                .attr('placeholder', gridboxLanguage['WRITE_COMMENT_HERE']);
            wrapper.find('.comment-reply-form-wrapper .ba-submit-comment').attr('data-type', 'reply');
            if (object.userStatus) {
                let editStr = '<span class="ba-submit-cancel">';
                editStr += gridboxLanguage['CANCEL']+'</span><span class="ba-submit-comment" data-type="edit">';
                editStr += gridboxLanguage['SAVE']+'</span>';
                wrapper.find('.comment-edit-form-wrapper .ba-submit-comment-wrapper').html(editStr);
            } else {
                wrapper.find('.ba-submit-comment').remove();
                wrapper.find('textarea.ba-comment-message').attr('disabled', 'disabled');
            }
            if (object.captcha == '') {
                wrapper.find('.ba-comments-captcha-wrapper').remove();
            }
        }
    });
}

app.getCommentsPatterns = function(controller, next, reviewID){
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task="+controller+".getCommentsPatterns",
        data: {
            id: themeData.id,
            next: next ? next : 1,
            'sort-by': $g('.ba-comments-total-count-wrapper select').val(),
            reviewID: reviewID ? reviewID : ''
        },
        error: function(msg){
            console.info(msg.responseText)
        },
        success: function(msg){
            let object = JSON.parse(msg),
                parent = controller == 'comments' ? $g('.ba-item-comments-box') : $g('.ba-item-reviews');
            parent.find('.ba-comments-total-count-wrapper').html(object.commentsCount).find('meta[itemprop="name"]')
                .each(function(){
                this.content = $g('head title').text();
            });
            parent.find('.users-comments-wrapper').html(object.comments);
            parent.find('.ba-comments-login-wrapper').html(object.login);
            parent.find('.ba-comment-message-wrapper').html(object.commentMessage);
            parent.find('.comment-reply-form-wrapper .ba-submit-comment').text(gridboxLanguage['COMMENT']);
            if (controller == 'reviews') {
                parent.find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper')
                    .find('.ba-comments-attachments-wrapper').remove();
            }
            parent.find('.comment-reply-form-wrapper, .ba-comment-reply-wrapper').find('.ba-comment-message')
                .attr('placeholder', gridboxLanguage['WRITE_COMMENT_HERE']);
            parent.find('.comment-reply-form-wrapper .ba-submit-comment').attr('data-type', 'reply');
            if (object.userStatus) {
                let editStr = '<span class="ba-submit-cancel">';
                editStr += gridboxLanguage['CANCEL']+'</span><span class="ba-submit-comment" data-type="edit">';
                editStr += gridboxLanguage['SAVE']+'</span>';
                parent.find('.comment-edit-form-wrapper .ba-submit-comment-wrapper').html(editStr);
                parent.find('.ba-leave-review-box-wrapper').removeAttr('data-disabled');
            } else {
                parent.find('.ba-submit-comment').remove();
                parent.find('textarea.ba-comment-message').attr('disabled', 'disabled');
                parent.find('.ba-leave-review-box-wrapper').attr('data-disabled', 'disabled');
            }
            if (object.captcha == '') {
                parent.find('.ba-comments-captcha-wrapper').remove();
            } else if ('initCommentsRecaptcha' in app) {
                parent.find('> .ba-comments-box-wrapper .ba-comment-message-wrapper .ba-comments-captcha-wrapper')
                    .each(function(){
                    app.initCommentsRecaptcha(this);
                });
            }
            if (themeData.page.view != 'gridbox') {
                parent.each(function(){
                    let desktop = app.items[this.id].view,
                        $this = $g(this);
                    if (!desktop.user) {
                        $this.find('.ba-user-login-wrapper').remove();
                    }
                    if (!desktop.social) {
                        $this.find('.ba-social-login-wrapper').remove();
                    }
                    if (!desktop.guest) {
                        $this.find('.ba-guest-login-wrapper').remove();
                    }
                    if (!desktop.share) {
                        $this.find('.comment-share-action').remove();
                    }
                    if (!desktop.rating) {
                        $this.find('.comment-likes-action-wrapper').remove();
                    }
                    if (!desktop.files) {
                        $this.find('.ba-comments-attachment-file-wrapper[data-type="file"]').remove();
                    }
                    if (!desktop.images) {
                        $this.find('.ba-comments-attachment-file-wrapper[data-type="image"]').remove();
                    }
                    if (!desktop.report) {
                        $this.find('.comment-report-user-comment').remove();
                    }
                    if (('reply' in desktop) && !desktop.reply) {
                        $this.find('.comment-reply-action').remove();
                    }
                });
            }
            if (reviewID) {
                let item = document.querySelector(app.hash);
                item ? item.scrollIntoView(true) : '';
            }
        }
    });
}

app.getReviewsMoreReply = function(wrapper, limit){
    let parent = wrapper.prev().find('.user-comment-wrapper')[0],
        id = parent.id.match(/\d+/),
        replyName = parent.querySelector('.comment-user-name').textContent;
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=reviews.getReviewsMoreReply",
        data: {
            id: themeData.id,
            parent: id[0],
            replyName: replyName,
            limit: limit,
            'sort-by': wrapper.closest('.ba-comments-box-wrapper').find('.ba-comments-total-count-wrapper select').val()
        },
        success: function(msg){
            let object = JSON.parse(msg);
            wrapper.html(object.comments);
            wrapper.find('.ba-comments-login-wrapper').html(object.login);
            wrapper.find('.ba-comment-message-wrapper').html(object.commentMessage);
            wrapper.find('.ba-submit-comment').text(gridboxLanguage['COMMENT']);
            wrapper.find('.ba-comments-attachments-wrapper').remove();
            wrapper.find('.ba-comment-message').attr('placeholder', gridboxLanguage['WRITE_COMMENT_HERE']);
            wrapper.find('.ba-submit-comment').attr('data-type', 'reply');
            if (object.userStatus) {
                let editStr = '<span class="ba-submit-cancel">';
                editStr += gridboxLanguage['CANCEL']+'</span><span class="ba-submit-comment" data-type="edit">';
                editStr += gridboxLanguage['SAVE']+'</span>';
                wrapper.find('.comment-edit-form-wrapper .ba-submit-comment-wrapper').html(editStr);
            } else {
                wrapper.find('.ba-submit-comment').remove();
                wrapper.find('textarea.ba-comment-message').attr('disabled', 'disabled');
                wrapper.find('.ba-leave-review-box-wrapper').attr('data-disabled', 'disabled');
            }
            if (object.captcha == '') {
                wrapper.find('.ba-comments-captcha-wrapper').remove();
            }
        }
    });
}

function googleCredentialResponse(response)
{
    let json = app.google.JSONWebToken(response.credential),
        data = app.google.getUserInfo(json);
    app.loginCommentSocial(data);
}

function vkAsyncInit()
{
    VK.init({apiId: window.integrations.vk});
}

if (window.integrations && window.integrations.facebook) {
    app.facebook.load();
}

if (window.integrations && window.integrations.google) {
    app.google.load(googleCredentialResponse, '.ba-google-login-btn-parent', {
        type: 'icon'
    });
}

if (window.integrations && window.integrations.vk) {
    (function(d, s) {
        var js, fjs = d.getElementsByTagName(s)[0];
        js = d.createElement(s);
        js.src = "https://vk.com/js/api/openapi.js?160";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script'));
}

app.getCommentsModeratorsContext = function(){
    if (!app.commentsModeratorsContext) {
        var div = document.createElement('div'),
            str = '<span class="comments-approve"><i class="ba-icons ba-icon-check"></i>'+
            gridboxLanguage['APPROVE']+'</span>'+
            '<span class="comments-spam"><i class="ba-icons ba-icon-alert"></i>'+
            gridboxLanguage['SPAM']+'</span>'+
            '<span class="comments-ban-user"><i class="ba-icons ba-icon-block"></i>'+
            gridboxLanguage['BAN_USER']+'</span>'+
            '<span class="comments-delete ba-group-element"><i class="ba-icons ba-icon-trash"></i>'+
            gridboxLanguage['DELETE']+'</span>';
        div.className = 'ba-context-menu comments-moderators-context-menu';
        div.innerHTML = str;
        document.body.append(div);
        app.commentsModeratorsContext = $g(div);
        app.commentsModeratorsContext.on('click', '.comments-approve', function(){
            var comment = $g(app.shareComment),
                controller = app.shareComment.id.replace(/ID-\d+/, '')+'s';
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task="+controller+".moderatorApprove",
                data: {
                    id: app.shareComment.id.replace(/reviewID-|commentID-/, '')
                },
                complete: function(msg){
                    comment.removeClass('ba-not-approved-comment').find('.comment-not-approved-label').remove();
                    app.showNotice(gridboxLanguage['COM_GRIDBOX_N_ITEMS_APPROVED']);
                }
            });
        });
        app.commentsModeratorsContext.on('click', '.comments-spam', function(){
            var comment = $g(app.shareComment),
                controller = app.shareComment.id.replace(/ID-\d+/, '')+'s';
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task="+controller+".moderatorSpam",
                data: {
                    id: app.shareComment.id.replace(/reviewID-|commentID-/, '')
                },
                complete: function(msg){
                    comment.addClass('ba-not-approved-comment').find('.comment-not-approved-label').remove();
                    comment.find('.comment-user-info').append('<span class="comment-not-approved-label">'+gridboxLanguage['SPAM']+'</span>');
                    app.showNotice(gridboxLanguage['COM_GRIDBOX_N_ITEMS_SPAMED']);
                }
            });
        });
        app.commentsModeratorsContext.on('click', '.comments-ban-user', function(){
            let controller = app.shareComment.id.replace(/ID-\d+/, '')+'s';
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task="+controller+".moderatorBanUser",
                data: {
                    id: app.shareComment.id.replace(/reviewID-|commentID-/, '')
                },
                complete: function(msg){
                    app.showNotice(msg.responseText);
                }
            });
        });
        app.commentsModeratorsContext.on('click', '.comments-delete', function(){
            let dialog = app.getCommentsDeleteDialog();
            dialog.addClass('visible-comments-dialog');
            dialog.find('.apply-comment-delete').attr('data-action', 'moderator');
        });
    }

    return app.commentsModeratorsContext;
}

app.getCommentsModalPatern = function(str, className){
    var div = document.createElement('div'),
        dialog = $g(div);
    div.className = 'ba-comments-modal '+className;
    div.innerHTML = str;
    document.body.append(div);
    dialog.find('input:not(.comment-clipboard)').each(function(ind){
        if (ind == 0) {
            this.classList.add('reset-input-margin');
        }
        $g(this).wrap('<div></div>').after('<span class="focus-underline"></span>');
    });
    app.hideCommentsModal();

    return div;
}

app.reloadAllCommentsPatterns = function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    $g('.ba-item-reviews').each(function(){
        app.getCommentsPatterns('reviews');
    })
    $g('.ba-item-comments-box').each(function(){
        app.getCommentsPatterns('comments');
    });
    getCommentUser();
}

app.setCommentsUser = function(str){
    $g('.visible-comments-dialog').removeClass('visible-comments-dialog');
    app.reloadAllCommentsPatterns();
}

app.getCommentsShareDialog = function(){
    if (!app.commentsShareDialog) {
        var div = null,
            str = '<div class="ba-comments-modal-body"><div class="ba-comments-share-icons-wrapper">';
        str += '<i class="ba-icons ba-icon-twitter twitter-share-comment"></i>';
        str += '<i class="ba-icons ba-icon-facebook facebook-share-comment"></i>';
        str += '<i class="ba-icons ba-icon-vk vk-share-comment"></i>';
        str += '<span><i class="ba-icons ba-icon-link copy-comment-link"></i><span class="ba-tooltip">';
        str += gridboxLanguage['COPY_LINK']+'</span></span><input type="text" class="comment-clipboard">';
        str += '</div></div><div class="ba-comments-modal-backdrop"></div>';
        div = app.getCommentsModalPatern(str, 'ba-comment-share-dialog');
        app.commentsShareDialog = $g(div);
        app.commentsShareDialog.find('.copy-comment-link').on('click', function(){
            document.querySelector('.ba-comment-share-dialog .comment-clipboard').select();
            document.execCommand('copy');
            app.showNotice(gridboxLanguage['LINK_SUCCESSFULLY_COPIED']);
        });
        app.commentsShareDialog.find('.twitter-share-comment').on('click', function(event){
            var url = 'https://twitter.com/intent/tweet?url=',
                text = app.shareComment.querySelector('.comment-message').textContent,
                id = app.shareComment.id,
                href = JUri+'index.php/'+id;
            url += encodeURIComponent(href);
            url += '&text='+encodeURIComponent(text);
            window.open(url, 'sharer', 'toolbar=0, status=0, width=626, height=436');
        });
        app.commentsShareDialog.find('.facebook-share-comment').on('click touchend', function(event){
            var id = app.shareComment.id,
                href = JUri+'index.php/'+id,
                url = 'http://www.facebook.com/sharer.php?u=';
            url += encodeURIComponent(href);
            window.open(url, 'sharer', 'toolbar=0, status=0, width=626, height=436');
        });
        app.commentsShareDialog.find('.vk-share-comment').on('click touchend', function(event){
            var url = 'http://vk.com/share.php?url=',
                text = app.shareComment.querySelector('.comment-message').textContent,
                id = app.shareComment.id,
                href = JUri+'index.php/'+id;
            url += encodeURIComponent(href)+'&title=';
            url += encodeURIComponent(text);
            window.open(url, 'sharer', 'toolbar=0, status=0, width=626, height=436');
        });
    }

    return app.commentsShareDialog;
}

app.getCommentsDeleteDialog = function(){
    if (!app.commentsDeleteDialog) {
        var div = null,
            str = '<div class="ba-comments-modal-body"><span class="ba-comments-modal-title">';
        str += gridboxLanguage['DELETE_ITEM']+'</span>';
        str += '<p class="ba-comments-modal-text can-delete">'+gridboxLanguage['MODAL_DELETE']+'</p>'
        str += '<div class="ba-comments-modal-footer"><span class="ba-btn">'+gridboxLanguage['CANCEL']+'</span>';
        str += ' <span class="ba-btn-primary red-btn apply-comment-delete">'+gridboxLanguage['DELETE']+'</span></div>';
        str += '</div><div class="ba-comments-modal-backdrop"></div>';
        div = app.getCommentsModalPatern(str, 'ba-comment-delete-dialog');
        app.commentsDeleteDialog = $g(div);
        app.commentsDeleteDialog.find('.apply-comment-delete').on('click', function(){
            let id = app.shareComment.id.replace(/reviewID-|commentID-/),
                controller = app.shareComment.id.replace(/ID-\d+/, '')+'s',
                task = 'deleteComment';
            app.commentsDeleteDialog.find('.ba-btn').trigger('click');
            if (this.dataset.action != 'user') {
                task = 'moderatorDelete';
            }
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task="+controller+"."+task,
                data: {
                    id: id
                },
                complete: function(msg){
                    if (controller == 'reviews' && app.shareComment.closest('.ba-comment-reply-wrapper')) {
                        let wrapper = $g(app.shareComment).closest('.ba-comment-reply-wrapper'),
                            limit = wrapper.find('.ba-view-more-replies').length ? 2 : 0;
                        app.getReviewsMoreReply(wrapper, limit);
                    } else if (controller == 'reviews'
                        && !app.shareComment.closest('.users-comments-wrapper').querySelector('.ba-load-more-reviews-btn')) {
                        app.getCommentsPatterns('reviews', 2);
                    } else {
                        app.getCommentsPatterns(controller);
                    }
                }
            });
        });
    }

    return app.commentsDeleteDialog;
}

app.getUserLoginDialog = function(){
    if (!app.userLoginDialog) {
        var div = null,
            str = '<div class="ba-comments-modal-body"><span class="ba-comments-modal-title">';
        str += gridboxLanguage['LOGIN']+'</span><input type="text" placeholder="'+gridboxLanguage['USERNAME'];
        str += '" data-name="username"><input type="password" placeholder="';
        str += gridboxLanguage['PASSWORD']+'" data-name="password"><div class="ba-user-login-action">';
        str += gridboxLanguage['LOGIN']+'</div></div><div class="ba-comments-modal-backdrop"></div>';
        div = app.getCommentsModalPatern(str, 'ba-comment-user-login-dialog');
        app.userLoginDialog = $g(div);
        app.userLoginDialog.on('keyup', function(event){
            if (event.keyCode == 13) {
                app.userLoginDialog.find('.ba-user-login-action').trigger('click');
            }
        });
        app.userLoginDialog.find('.ba-user-login-action').on('click', function(){
            let data = {
                username: app.userLoginDialog.find('input[data-name="username"]').val().trim(),
                password: app.userLoginDialog.find('input[data-name="password"]').val().trim()
            }
            $g.ajax({
                type: "POST",
                dataType: 'text',
                url: JUri+"index.php?option=com_gridbox&task=comments.loginUser",
                data: data,
                complete: function(msg){
                    let obj = JSON.parse(msg.responseText);
                    if (obj.msg) {
                        app.showNotice(obj.msg, 'ba-alert');
                    } else {
                        app.setCommentsUser(msg.responseText);
                    }
                }
            });
        });
    }

    return app.userLoginDialog;
}

app.getGuestLoginDialog = function(){
    if (!app.guestLoginDialog) {
        var div = null,
            str = '<div class="ba-comments-modal-body"><span class="ba-comments-modal-title">';
        str += gridboxLanguage['LOGIN_AS_GUEST']+'</span><input type="text" placeholder="'+gridboxLanguage['NAME'];
        str += '" data-name="name"><input type="email" placeholder="';
        str += gridboxLanguage['EMAIL']+'" data-name="email"><div class="ba-guest-login-action">';
        str += gridboxLanguage['LOGIN']+'</div></div><div class="ba-comments-modal-backdrop"></div>';
        div = app.getCommentsModalPatern(str, 'ba-comment-guest-login-dialog');
        app.guestLoginDialog = $g(div);
        let name = app.guestLoginDialog.find('input[data-name="name"]'),
            email = app.guestLoginDialog.find('input[data-name="email"]');
        name.on('input', function(){
            if (this.value.trim()) {
                this.classList.remove('ba-alert');
            }
        });
        email.on('input', function(){
            if (this.value.trim() && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/.test(this.value.trim())) {
                this.classList.remove('ba-alert');
            }
        });
        app.guestLoginDialog.on('keyup', function(event){
            if (event.keyCode == 13) {
                app.guestLoginDialog.find('.ba-guest-login-action').trigger('click');
            }
        });
        app.guestLoginDialog.find('.ba-guest-login-action').on('click', function(){
            let data = {
                    name: name.val().trim(),
                    email: email.val().trim()
                },
                nameFlag = data.name != '',
                emailFlag = data.email && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/.test(data.email);
            if (nameFlag && emailFlag) {
                $g.ajax({
                    type: "POST",
                    dataType: 'text',
                    url: JUri+"index.php?option=com_gridbox&task=comments.loginGuest",
                    data: data,
                    complete: function(msg){
                        app.setCommentsUser(msg.responseText);
                    }
                });
            }
            if (!nameFlag) {
                name.addClass('ba-alert');
            }
            if (!emailFlag) {
                email.addClass('ba-alert');
            }
        });
    }

    return app.guestLoginDialog;
}

function setCommentsImage(image)
{
    var imgHeight = image.naturalHeight,
        imgWidth = image.naturalWidth,
        modal = $g('.ba-image-modal.instagram-modal').removeClass('instagram-fade-animation'),
        wWidth = $g(window).width(),
        wHeigth = $g(window).height(),
        percent = imgWidth / imgHeight;
    if (wWidth > 1024) {
        if (imgWidth < wWidth && imgHeight < wHeigth) {
        
        } else {
            if (imgWidth > imgHeight) {
                imgWidth = wWidth - 100;
                imgHeight = imgWidth / percent;
            } else {
                imgHeight = wHeigth - 100;
                imgWidth = percent * imgHeight;
            }
            if (imgHeight > wHeigth) {
                imgHeight = wHeigth - 100;
                imgWidth = percent * imgHeight;
            }
            if (imgWidth > wWidth) {
                imgWidth = wWidth - 100;
                imgHeight = imgWidth / percent;
            }
        }
    } else {
        percent = imgWidth / imgHeight;
        if (percent >= 1) {
            imgWidth = wWidth * 0.90;
            imgHeight = imgWidth / percent;
            if (wHeigth - imgHeight < wHeigth * 0.1) {
                imgHeight = wHeigth * 0.90;
                imgWidth = imgHeight * percent;
            }
        } else {
            imgHeight = wHeigth * 0.90;
            imgWidth = imgHeight * percent;
            if (wWidth - imgWidth < wWidth * 0.1) {
                imgWidth = wWidth * 0.90;
                imgHeight = imgWidth / percent;
            }
        }
    }
    var modalTop = (wHeigth - imgHeight) / 2,
        left = (wWidth - imgWidth) / 2;
    setTimeout(function(){
        modal.find('> div').css({
            'width' : Math.round(imgWidth),
            'height' : Math.round(imgHeight),
            'left' : Math.round(left),
            'top' : Math.round(modalTop)
        }).addClass('instagram-fade-animation');
    }, 1);
}

function commentsImageGetPrev(img, images, index)
{
    var ind = images[index - 1] ? index - 1 : images.length - 1;
    image = document.createElement('img');
    image.onload = function(){
        setCommentsImage(this);
    }
    image.src = images[ind].dataset.img;
    img.style.backgroundImage = 'url('+image.src+')';

    return ind;
}

function commentsImageGetNext(img, images, index)
{
    var ind = images[index + 1] ? index + 1 : 0;
    image = document.createElement('img');
    image.onload = function(){
        setCommentsImage(this);
    }
    image.src = images[ind].dataset.img;
    img.style.backgroundImage = 'url('+image.src+')';

    return ind;
}

function commentsImageModalClose(modal, images, index)
{
    $g(window).off('keyup.instagram');
    modal.addClass('image-lightbox-out');
    var $image = $g(images[index]), 
        width = $image.width(),
        height = $image.height(),
        offset = $image.offset();
    modal.find('> div').css({
        'width' : width,
        'height' : height,
        'left' : offset.left,
        'top' : offset.top - $g(window).scrollTop()
    });
    setTimeout(function(){
        modal.remove();
        document.body.style.width = '';
        document.body.style.overflow = '';
        $g('.ba-sticky-header').css('');
        $g('body > header.header').css('width', '');
    }, 500);
}

$g('body').on('click', function(event){
    $g('.ba-comment-smiles-picker-dialog.visible-smiles-picker').removeClass('visible-smiles-picker');
    $g('.ba-context-menu.visible-context-menu').removeClass('visible-context-menu');
});

app.tmpAttachments = {};

$g('body').on('click', '.ba-comment-attachment-trigger', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    let $this = $g(this).parent().find('input[type="file"]');
    if (!$this[0].dataset.uploading) {
        setTimeout(function(){
            $this.trigger('click');
        }, 150);
    }
});

$g('body').on('change', '.ba-comment-attachment', function(){
    this.dataset.uploading = 'uploading';
    let files = [].slice.call(this.files),
        container = $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-xhr-attachment-wrapper'),
        flag = true;
    for (let i = 0; i < files.length; i++) {
        let size = this.dataset.size * 1000,
            msg = '',
            name = files[i].name.split('.'),
            ext = name[name.length - 1].toLowerCase(),
            types = this.dataset.types.replace(/ /g, '').split(',');
        if (size < files[i].size) {
            msg = 'NOT_ALLOWED_FILE_SIZE';
        } else if (types.indexOf(ext) == -1) {
            msg = 'NOT_SUPPORTED_FILE';
        }
        if (size < files[i].size || types.indexOf(ext) == -1) {
            flag = false;
            app.showNotice(app._(msg), 'ba-alert');
            this.dataset.uploading = '';
            break
        }
    }
    if (flag) {
        uploadCommentAttachmentFile(files, this.dataset.attach, container);
    }
});

function removeTmpAttachment($this)
{
    if ($this.dataset.id) {
        let controller = $g(this).closest('.ba-item').hasClass('ba-item-reviews') ? 'reviews' : 'comments';
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : JUri+"index.php?option=com_gridbox&task="+controller+".removeTmpAttachment",
            data: {
                id: $this.dataset.id,
                filename: app.tmpAttachments[$this.dataset.id].filename
            },
            complete:function(msg){
                $this.remove();
                delete(app.tmpAttachments[$this.dataset.id]);
            }
        });
    }
}

function uploadCommentAttachmentFile(files, type, container)
{
    if (files.length) {
        var file = files.shift(),
            attachment = document.createElement('div'),
            controller = container.closest('.ba-item').hasClass('ba-item-reviews') ? 'reviews' : 'comments',
            str = '',
            xhr = new XMLHttpRequest(),
            formData = new FormData();
        attachment.className = 'ba-comment-xhr-attachment';
        if (type == 'file') {
            str += '<i class="ba-icons ba-icon-attachment"></i>';
        } else {
            str += '<span class="post-intro-image"></span>';
        }
        str += '<span class="attachment-title">'+file.name;
        str += '</span><span class="attachment-progress-bar-wrapper"><span class="attachment-progress-bar">';
        str += '</span></span><i class="ba-icons ba-icon-trash"></i>';
        attachment.innerHTML = str;
        if (type == 'image') {
            let reader = new FileReader();
            reader.onloadend = function() {
                attachment.querySelector('.post-intro-image').style.backgroundImage = 'url('+reader.result+')';
            }
            reader.readAsDataURL(file);
        }
        $g(attachment).find('.ba-icon-trash').on('click', function(){
            removeTmpAttachment(this.closest('.ba-comment-xhr-attachment'));
        });
        formData.append('file', file);
        formData.append('type', type);
        xhr.upload.onprogress = function(event){
            attachment.querySelector('.attachment-progress-bar').style.width = Math.round(event.loaded / event.total * 100)+"%";
        }
        xhr.onload = xhr.onerror = function(){
            uploadCommentAttachmentFile(files, type, container);
            try {
                let obj = JSON.parse(this.responseText);
                app.tmpAttachments[obj.id] = obj;
                attachment.dataset.id = obj.id;
            } catch (e){
                console.info(e)
                console.info(this.responseText)
            }
            setTimeout(function(){
                attachment.classList.add('attachment-file-uploaded')
            }, 300);
        };
        container.append(attachment);
        xhr.open("POST", JUri+"index.php?option=com_gridbox&task="+controller+".uploadAttachmentFile", true);
        xhr.send(formData);
    } else {
        $g('body .ba-comment-attachment[data-uploading="uploading"]').removeAttr('data-uploading');
    }
}

function getCommentUser()
{
    app.commentsUser = null;
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=comments.getCommentsUser",
        complete: function(msg){
            if (msg.responseText) {
                try {
                    app.commentsUser = JSON.parse(msg.responseText);
                } catch (e) {
                    console.info(e)
                }
            }
        }
    });
}

function getClearCommentMessage(message)
{
    let div = document.createElement('div');
    div.innerHTML = message;

    return div.textContent;
}

app.commentsHelper = function(){}

$g('body').on('click', '.comment-report-user-comment', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    let wrapper = $g(this).closest('.user-comment-wrapper'),
        controller = wrapper[0].id.replace(/ID-\d+/, '')+'s',
        msg = controller == 'reviews' && !wrapper.closest('.ba-comment-reply-wrapper').length ? 'REVIEW_REPORTED_TO_MODERATOR' : 'COMMENT_REPORTED_TO_MODERATOR',
        id = wrapper.attr('id').replace(/reviewID-|commentID-/, '');
    this.remove();
    app.showNotice(gridboxLanguage[msg]);
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task="+controller+".sendCommentReport",
        data: {
            id: id
        }
    });
});
$g('.ba-item-comments-box, .ba-item-reviews').on('click', '.comment-moderator-user-settings', function(event){
    event.stopPropagation();
    let dialog = app.getCommentsModeratorsContext(),
        rect = this.getBoundingClientRect(),
        computed = getComputedStyle(document.body),
        borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
        borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
        div = dialog.addClass('visible-context-menu')[0];
    app.shareComment = this.closest('.user-comment-wrapper');
    div.style.top = (rect.bottom + window.pageYOffset + 10 - borderTopWidth)+'px';
    div.style.left = (rect.right - div.offsetWidth - borderLeftWidth)+'px';
});
$g('body').on('click', '.ba-comments-facebook-login', function(e){
    if (window.integrations && window.integrations.facebook && !app.commentsUser && window.FB) {
        FB.login(function(response){
            app.facebook.getUserInfo(response).then((data) => {
                app.loginCommentSocial(data);
            });
        });
    }
});
$g('body').on('click', '.ba-comments-vk-login', function(e){
    if (window.integrations && window.integrations.vk && !app.commentsUser && window.VK) {
        VK.Auth.login(function(response) {
            app.getVkUserInfo(response);
        });
    }
});
$g('body').on('click', '.ba-submit-comment', function(e){
    if (themeData.page.view == 'gridbox' || this.dataset.clicked == 'true') {
        return false;
    }
    var parent = $g(this).closest('.ba-comment-message-wrapper'),
        attachments = {},
        $this = this,
        controller = parent.closest('.ba-item').hasClass('ba-item-reviews') ? 'reviews' : 'comments',
        captchaResponse = true,
        data = {
            page_id: themeData.id,
            type: this.dataset.type,
            message: parent.find('.ba-comment-message').val().trim()
        }
    if (controller == 'reviews' && this.dataset.type == 'submit') {
        data.rating = parent.closest('.ba-leave-review-box-wrapper').find('.ba-review-rate-wrapper i.active').last().attr('data-rating');
    } else if (controller == 'reviews' && this.dataset.type == 'edit') {
        data.rating = parent.closest('.comment-data-wrapper').find('i.active').last().attr('data-rating');
        if (data.rating == undefined) {
            data.rating = 0;
        }
    } else if (controller == 'reviews') {
        data.rating = 0;
    }
    if (this.dataset.type == 'edit') {
        let id = $g(this).closest('.user-comment-container-wrapper').find('> .user-comment-wrapper').attr('id'),
            queue = {};
        data.id = id.replace(/reviewID-|commentID-/, '');
        $g(this).closest('.comment-data-wrapper').find('[data-queue="delete"] .delete-comment-attachment-file').each(function(){
            queue[this.dataset.id] = this.dataset.filename;
        });
        data.queue = JSON.stringify(queue);
    }
    if (this.dataset.type == 'reply') {
        let id = $g(this).closest('.user-comment-container-wrapper').find('> .user-comment-wrapper').attr('id');
        data.parent = id.replace(/reviewID-|commentID-/, '');
    } else {
        data.parent = 0;
    }
    parent.find('.ba-comments-captcha-wrapper').each(function(){
        if (recaptchaObject) {
            captchaResponse = app.getCommentsRecaptchaResponse(this.querySelector('.comments-recaptcha'));
        }
    });
    if (!captchaResponse) {
        app.showNotice(app._('RECAPTCHA_ERROR'), 'ba-alert');
        return false;
    }
    data.message = getClearCommentMessage(data.message)
    parent.find('.ba-comment-xhr-attachment').each(function(){
        attachments[this.dataset.id] = app.tmpAttachments[this.dataset.id];
    });
    data.attachments = JSON.stringify(attachments);
    let allowSubmit = ((controller == 'comments' && app.commentsUser &&
        (data.message || data.attachments != '{}' || data.queue != '{}')) ||
        (controller == 'reviews' && app.commentsUser && data.message && data.rating != undefined));
    if (allowSubmit) {
        var matches = data.message.match(/(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe23\u20d0-\u20f0]|\ud83c[\udffb-\udfff])?(?:\u200d(?:[^\ud800-\udfff]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe23\u20d0-\u20f0]|\ud83c[\udffb-\udfff])?)*/g);
        if (matches) {
            for (var i = 0; i < matches.length; i++) {
                let charCode = '&#'+matches[i].codePointAt(0)+';';
                data.message = data.message.replace(matches[i], charCode);
            }
        }
        this.dataset.clicked = 'true';
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task="+controller+".sendCommentMesssage",
            data: data,
            complete: function(msg){
                let response = JSON.parse(msg.responseText);
                if (response.type == 'error') {
                    $this.dataset.clicked = 'false';
                    app.showNotice(response.message, 'ba-alert');
                } else {
                    app.fetch(JUri+'index.php?option=com_gridbox&task='+controller+'.sendCommentsEmails');
                    app.showNotice(response.message);
                    parent.find('.ba-comment-xhr-attachment').remove();
                    parent.find('> .ba-comments-box-wrapper > .ba-review-rate-wrapper i.active').removeClass('active');
                    parent.find('.ba-comment-message').val('');
                    $this.dataset.clicked = '';
                    if (controller == 'reviews' && $this.dataset.type == 'edit') {
                        app.getReviewById($this);
                    } else if (controller == 'reviews' && $this.dataset.type == 'reply') {
                        let container = $g($this).closest('.user-comment-container-wrapper'),
                            wrapper = container.next(),
                            limit = 0;
                        if (!wrapper.hasClass('ba-comment-reply-wrapper')) {
                            let div = document.createElement('div');
                            div.className = 'ba-comment-reply-wrapper';
                            container.after(div);
                            wrapper = $g(div);
                        }
                        limit = wrapper.find('.ba-view-more-replies').length ? 2 : 0;
                        app.getReviewsMoreReply(wrapper, limit);
                        $g($this).closest('.ba-submit-comment-wrapper').find('.ba-submit-cancel').trigger('click');
                    } else {
                        app.getCommentsPatterns(controller);
                        parent.find('.ba-submit-cancel').trigger('click');
                    }
                }
            }
        });
    } else {
        app.showNotice(gridboxLanguage['COMPLETE_REQUIRED_FIELDS'], 'ba-alert');
    }
});
$g('body').on('click', '.ba-user-login-btn, .ba-guest-login-btn', function(event){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    let name = this.dataset.type.charAt(0).toUpperCase() + this.dataset.type.slice(1),
        action = 'get'+name+'LoginDialog',
        dialog = app[action]();
    dialog.addClass('visible-comments-dialog');
});
$g('body').on('click', '.comment-delete-action', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    app.shareComment = this.closest('.user-comment-wrapper');
    let dialog = app.getCommentsDeleteDialog();
    dialog.addClass('visible-comments-dialog');
    dialog.find('.apply-comment-delete').attr('data-action', 'user')
});
$g('body').on('click', '.comment-edit-action', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    $g('.user-comment-edit-enable .ba-submit-cancel, .leave-review-enabled .ba-submit-cancel').trigger('click');
    $g('.comment-reply-form-wrapper').hide();
    app.shareComment = this.closest('.user-comment-wrapper');
    let parent = $g(app.shareComment),
        message = parent.find('p.comment-message').html().trim().replace(/<br>/g, '\n');
    parent.find('.ba-comment-message').val(message);
    app.shareComment.classList.add('user-comment-edit-enable');
    parent.find('.ba-comments-captcha-wrapper').each(function(){
        if (!this.querySelector('*')) {
            app.initCommentsRecaptcha(this);
        }
    });
});
$g('body').on('click', '.delete-comment-attachment-file', function(){
    let parent = this.parentNode;
    parent.style.display = 'none';
    parent.dataset.queue = 'delete';
});
$g('body').on('click', '.ba-submit-cancel', function(){
    if (this.closest('.ba-leave-review-wrapper')) {
        this.closest('.leave-review-enabled').classList.remove('leave-review-enabled');
    } else if (this.closest('.comment-reply-form-wrapper')) {
        this.closest('.comment-reply-form-wrapper').style.display = 'none';
    } else {
        $g('[data-queue="delete"]').removeAttr('data-queue').css('display', '');
        app.shareComment.classList.remove('user-comment-edit-enable');
        $g(app.shareComment).find('.ba-review-stars-wrapper i.active').removeClass('active');
        $g(app.shareComment).find('.ba-review-stars-wrapper i[data-active]').addClass('active');
        $g(this).closest('.comment-data-wrapper').find('.ba-comment-xhr-attachment i.ba-icon-trash').trigger('click');
    }
});
$g('body').on('click', '.comment-share-action', function(event){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    app.shareComment = this.closest('.user-comment-wrapper');
    let dialog = app.getCommentsShareDialog(),
        id = app.shareComment.id,
        url = JUri+'index.php/'+id,
        rect = this.getBoundingClientRect(),
        computed = getComputedStyle(document.body),
        borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
        borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
        div = dialog.find('.ba-comments-modal-body')[0];
    dialog.addClass('visible-comments-dialog').find('.comment-clipboard').val(url);
    app.commentBtn = $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-message')[0];
    div.style.top = (rect.top - div.offsetHeight + window.pageYOffset - 10 - borderTopWidth)+'px';
    div.style.left = (rect.left - div.offsetWidth / 2 + rect.width / 2 - borderLeftWidth)+'px';
});
$g('body').on('click', '.comment-reply-action', function(event){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    $g('.user-comment-edit-enable .ba-submit-cancel, .leave-review-enabled .ba-submit-cancel').trigger('click');
    $g('.comment-reply-form-wrapper').hide();
    $g(this).closest('.user-comment-container-wrapper').find('.comment-reply-form-wrapper').css('display', '')
        .find('.ba-comments-captcha-wrapper').each(function(){
        if (!this.querySelector('*')) {
            app.initCommentsRecaptcha(this);
        }
    });
});
$g('body').on('click', '.ba-leave-review-btn', function(event){
    $g('.user-comment-edit-enable .ba-submit-cancel, .leave-review-enabled .ba-submit-cancel').trigger('click');
    $g('.comment-reply-form-wrapper').hide();
    $g(this).closest('.ba-comments-box-wrapper').addClass('leave-review-enabled').css('display', '').find('.ba-comments-captcha-wrapper').each(function(){
        if (!this.querySelector('*')) {
            app.initCommentsRecaptcha(this);
        }
    });
    app.google.renderButton('.ba-google-login-btn-parent', {
        type: 'icon'
    });
});
$g('body').on('click', '.ba-load-more-reviews-btn', function(event){
    app.getCommentsPatterns('reviews', this.dataset.next);
});

$g('body').on('click', '.ba-view-more-replies', function(event){
    let wrapper = $g(this).closest('.ba-comment-reply-wrapper');
    app.getReviewsMoreReply(wrapper, 0);
});
$g('body').on('click', '.comment-logout-action', function(event){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    $g('.user-comment-edit-enable .ba-submit-cancel, .leave-review-enabled .ba-submit-cancel').trigger('click');
    $g('.comment-reply-form-wrapper').hide();
    $g.ajax({
        type: "POST",
        dataType: 'text',
        url: JUri+"index.php?option=com_gridbox&task=comments.logoutUser",
        complete: function(msg){
            app.reloadAllCommentsPatterns();
        }
    });
});
$g('body').on('change', '.ba-comments-total-count-wrapper select', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    let controller = $g(this).closest('.ba-item').hasClass('ba-item-reviews') ? 'reviews' : 'comments'
    app.getCommentsPatterns(controller);
});
$g('body').on('click.lightbox', '.comment-attachment-image-type', function(){
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    var wrapper = $g(this).closest('.comment-attachments-image-wrapper'),
        div = document.createElement('div'),
        index = 0,
        ind = 0,
        $this = this,
        endCoords = startCoords = {},
        image = document.createElement('img'),
        images = [],
        width = this.offsetWidth,
        height = this.offsetHeight,
        offset = $g(this).offset(),
        modal = $g(div),
        img = document.createElement('div');
    img.style.backgroundImage = 'url('+this.dataset.img+')';
    div.className = 'ba-image-modal instagram-modal ba-comments-image-modal';
    img.style.top = (offset.top - $g(window).scrollTop())+'px';
    img.style.left = offset.left+'px';
    img.style.width = width+'px';
    img.style.height = height+'px';
    div.appendChild(img);
    modal.on('click', function(){
        commentsImageModalClose(modal, images, index)
    }).on('touchstart', function(event){
        endCoords = event.originalEvent.targetTouches[0];
        startCoords.pageX = event.originalEvent.targetTouches[0].pageX;
        startCoords.pageY = event.originalEvent.targetTouches[0].pageY;
    }).on('touchmove', function(event){
        endCoords = event.originalEvent.targetTouches[0];
    }).on('touchend', function(event){
        var vDistance = endCoords.pageY - startCoords.pageY,
            hDistance = endCoords.pageX - startCoords.pageX,
            xabs = Math.abs(endCoords.pageX - startCoords.pageX),
            yabs = Math.abs(endCoords.pageY - startCoords.pageY);
        if(hDistance >= 100 && xabs >= yabs) {
            index = commentsImageGetPrev(img, images, index);
        } else if (hDistance <= -100 && xabs >= yabs) {
            index = commentsImageGetNext(img, images, index);
        }
    });
    $g('body').append(div);
    var header = document.querySelector('body > header.header'),
        style = header ? getComputedStyle(header): {},
        width = window.innerWidth - document.documentElement.clientWidth,
        hWidth = width + (themeData.page.view == 'gridbox' && app.view =='desktop' ? 103 : 0);
    document.body.style.width = 'calc(100% - '+width+'px)';
    document.body.style.overflow = 'hidden';
    $g('.ba-sticky-header').css('width', 'calc(100% - '+hWidth+'px)');
    if (style.position == 'fixed') {
        $g('body > header.header').css('width', 'calc(100% - '+hWidth+'px)');
    }
    image.onload = function(){
        setCommentsImage(this);
    }
    image.src = this.dataset.img;
    setTimeout(function(){
        var str = '';
        if (wrapper.find('.comment-attachment-image-type').length > 1) {
            str += '<i class="ba-icons ba-icon-chevron-left"></i><i class="ba-icons ba-icon-chevron-right"></i>';
        }
        str += '<i class="ba-icons ba-icon-close">';
        modal.append(str);
        modal.find('.ba-icon-chevron-left').on('click', function(event){
            event.stopPropagation();
            index = commentsImageGetPrev(img, images, index);
        });
        modal.find('.ba-icon-chevron-right').on('click', function(event){
            event.stopPropagation();
            index = commentsImageGetNext(img, images, index);
        });
        modal.find('.ba-icon-close').on('click', function(event){
            event.stopPropagation();
            commentsImageModalClose(modal, images, index)
        });
    }, 600);
    wrapper.find('.comment-attachment-image-type').each(function(){
        if (this.parentNode.dataset.queue != 'delete') {
            images.push(this);
            if (this == $this) {
                index = ind;
            }
            ind++;
        }
    });
    $g(window).on('keyup.instagram', function(event) {
        event.preventDefault();
        event.stopPropagation();
        if (event.keyCode === 37) {
            index = commentsImageGetPrev(img, images, index);
        } else if (event.keyCode === 39) {
            index = commentsImageGetNext(img, images, index);
        } else if (event.keyCode === 27) {
            commentsImageModalClose(modal, images, index)
        }
    });
});
$g('body').on('click', '.comment-likes-action', function(event){
    if (this.dataset.disabled || themeData.page.view == 'gridbox') {
        return false;
    }
    let wrapper = $g(this).closest('.user-comment-wrapper'),
        id = wrapper[0].id.replace(/reviewID-|commentID-/, ''),
        controller = wrapper[0].id.replace(/ID-\d+/, '')+'s',
        action = this.dataset.action;
    if (!app.commentsUser) {
        let alert = controller == 'reviews' && !wrapper[0].closest('.ba-comment-reply-wrapper') ? 'LOGIN_TO_RATE_REVIEW' : 'LOGIN_TO_RATE_COMMENT';
        app.showNotice(gridboxLanguage[alert], 'ba-alert');
        return false;
    }
    wrapper.find('.comment-likes-action').attr('data-disabled', 'disabled');
    $g.ajax({
        type : "POST",
        dataType : 'text',
        url : JUri+"index.php?option=com_gridbox&task="+controller+".setLikes",
        data: {
            id: id,
            action: this.dataset.action
        },
        complete:function(msg){
            let obj = JSON.parse(msg.responseText);
            setTimeout(function(){
                wrapper.find('.comment-likes-action').removeAttr('data-disabled');
            }, 100);
            wrapper.find('.comment-likes-action[data-action="likes"] .likes-count').text(obj.likes);
            wrapper.find('.comment-likes-action[data-action="dislikes"] .likes-count').text(obj.dislikes);
            wrapper.find('.comment-likes-action').removeClass('active');
            wrapper.find('.comment-likes-action[data-action="'+obj.status+'"]').addClass('active');
        }
    });
});

app.commentsHelper();
app.checkReview();
app.reloadAllCommentsPatterns();