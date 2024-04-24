/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
console.log = function(){
    return false;
};

document.addEventListener('DOMContentLoaded', function(){
    var $ = jQuery,
        delay,
        click = true,
        files = [];

    $('body').addClass('component');

    function checkDeleteState()
    {
        $('.delete-icons').addClass('disable-button');
        $('.font-checkbox input[type="checkbox"]').each(function(){
            if (this.checked) {
                $('.delete-icons').removeClass('disable-button');
                return false;
            }
        })
    }

    function checkboxAction()
    {
        $('.font-checkbox').on('click', function(event){
            event.stopPropagation();
            checkDeleteState();
        });
        $('.check-all').on('click', function(event){
            if (this.querySelector('input').checked) {
                $(this).closest('.ba-options-group').find('.font-checkbox input[type="checkbox"]').prop('checked', true);
            } else {
                $(this).closest('.ba-options-group').find('.font-checkbox input[type="checkbox"]').prop('checked', false);
            }
            checkDeleteState();
        });
    }

    checkboxAction();

    $('.tab-content').on('click', '.ba-group-element', function(event){
        top.app.messageData = $(this).find('> i')[0].className;
        top.app.checkModule('messageListener');
    });

    $('.delete-icons').on('click', function(){
        if (!this.classList.contains('disable-button')) {
            $('#delete-dialog').modal();
        }
    });

    $('#apply-delete').on('click', function(event){
        event.preventDefault();
        if (this.clicked == true) {
            return false;
        }
        this.clicked = true;
        var array = [],
            str = window.parent.gridboxLanguage['LOADING']+'<img src="';
        str += window.parent.JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
        window.parent.app.notification.find('p').html(str);
        window.parent.app.notification.removeClass('animation-out').addClass('notification-in');
        $('.font-checkbox input').each(function(){
            if (this.checked) {
                array.push(this.value);
            }
        });
        $.ajax({
            type:"POST",
            dataType:'text',
            url:top.JUri+"index.php?option=com_gridbox&task=icons.delete",
            data:{
                icons_id : array
            },
            complete: function(msg){
                $('#apply-delete')[0].clicked = false;
                window.parent.app.showNotice(msg.responseText);
                $('#delete-dialog').modal('hide');
                $('.delete-icons').addClass('disable-button');
                $('.font-checkbox input').each(function(){
                    if (this.checked) {
                        $(this).closest('.ba-group-element').remove();
                    }
                });
                $('.ba-options-group').each(function(){
                    if ($(this).find('.ba-group-element').length == 0) {
                        $(this).remove();
                    }
                });
            }
        });
    });

    $('.add-custom-icons').on('click', function(){
        if (!this.classList.contains('disable-button')) {
            $('.custom-font-select, .custom-font-title').val('');
            $('.install-custom-icons').addClass('disable-button').removeClass('active-button');
            $('#add-custom-icons-dialog').modal();
        }
    });

    $('.custom-font-select').on('click', function(){
        setTimeout(function(){
            $('.custom-fonts-files').trigger('click');
        }, 300);
    }).on('change', function(){
        if ($('.custom-font-title').val().trim()) {
            $('.install-custom-icons').removeClass('disable-button').addClass('active-button');
        }
    });

    $('.custom-font-title').on('input', function(){
        if ($('.custom-font-select').val().trim() && this.value.trim()) {
            $('.install-custom-icons').removeClass('disable-button').addClass('active-button');
        } else {
            $('.install-custom-icons').addClass('disable-button').removeClass('active-button');
        }
    });

    $('.custom-fonts-files').on('change', function(event){
        files = event.target.files;
        if (files.length != 0) {
            var file = files[0],
                name = file.name.split('.'),
            ext = name[name.length - 1].toLowerCase();
            if (ext == 'zip') {
                $('.custom-font-select').val(file.name).trigger('change');
            } else {
                $('.custom-font-select').val('').trigger('change');
                window.parent.app.showNotice(window.parent.gridboxLanguage['NOT_SUPPORTED_FILE']);
            }
        }
    });


    $('.install-custom-icons').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button') && click) {
            click = false;
            var formData = new FormData(document.forms.custom_fonts),
                XHR = new XMLHttpRequest(),
                str = window.parent.gridboxLanguage['LOADING']+'<img src="';
            formData.append("icon_name", $('.custom-font-title').val());
            str += window.parent.JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
            window.parent.app.notification.find('p').html(str);
            window.parent.app.notification.removeClass('animation-out').addClass('notification-in');
            XHR.onreadystatechange = function(e) {
                if (XHR.readyState == 4) {
                    click = true;
                    var obj = JSON.parse(XHR.responseText);
                    if (obj.type != 'ba-alert') {
                        var div = document.createElement('div');
                        $(div).load(window.location.href+' #user-icons', function(){
                            $('#user-icons')[0].innerHTML = $(div).find('#user-icons').html();
                            $(div).find('link').each(function(){
                                window.parent.app.editor.document.head.appendChild(this);
                            });
                            window.parent.app.showNotice(obj.msg, obj.type);
                            $('.delete-icons').addClass('disable-button');
                            checkboxAction();
                            $('#add-custom-icons-dialog').modal('hide');
                        });
                    } else {
                        window.parent.app.showNotice(obj.msg, obj.type);
                    }
                }
            }
            XHR.open("POST", top.JUri+"index.php?option=com_gridbox&task=icons.addCustomIcons");
            XHR.send(formData);
        }
    });
    $('.search-wrapper input[type="text"]').on('input', function(){
        var $this = this;
        clearTimeout(delay);
        delay = setTimeout(function(){
            var search = $this.value.toLowerCase();
            if (!search) {
                $('.row-fluid.tab-pane .ba-options-group > *').show();
            } else {
                $('.row-fluid.tab-pane .ba-options-group').each(function(){
                    var count = 0,
                        elements = $(this).find('.ba-group-element');
                    elements.each(function(){
                        var value = $(this).find('span').text().toLowerCase();
                        value = $.trim(value);
                        if (value.indexOf(search) < 0) {
                            this.style.display = 'none';
                            count++;
                        } else {
                            this.style.display = 'block';
                        }
                    });
                    if (count == elements.length) {
                        $(this).find('p').hide();
                    } else {
                        $(this).find('p').show();
                    }
                });
            }
        }, 300);
    });
    $('a[data-toggle="tab"]').on('shown', function(event){
        if (this.hash == '#user-icons') {
            $('.add-custom-icons').removeClass('disable-button');
        } else {
            $('.add-custom-icons').addClass('disable-button');
        }
        $('.delete-icons').addClass('disable-button');
        $('.check-all input').prop('checked', false);
        $('.font-checkbox input[type="checkbox"]').prop('checked', false);
    });
});