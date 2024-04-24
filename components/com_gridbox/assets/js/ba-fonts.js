/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var $g = jQuery

document.addEventListener('DOMContentLoaded', function(){
    var delay,
        notification = top.document.getElementById('ba-notification'),
        click = true,
        files = [];

    fetch(top.JUri+"index.php?option=com_gridbox&task=fonts.getFontString&"+(+new Date())).then((response) => {
        return response.text();
    }).then((text) => {
        $g('.ba-custom-select.fonts-select ul').html(text);
    });

    $g('body').addClass('component');

    function deleteFont()
    {
        $g('.font-checkbox input').on('change', function(){
            var checked = false;
            $g('.font-checkbox input').each(function(){
                if (this.checked) {
                    checked = true;
                    return false;
                }
            });
            if (checked) {
                $g('.delete-fonts').removeClass('disable-button');
            } else {
                $g('.delete-fonts').addClass('disable-button');
            }
        });
    }

    function refreshFontsList()
    {
        $g.ajax({
            type:"POST",
            dataType:'text',
            url: top.JUri+"index.php?option=com_gridbox&task=fonts.getFonts",
            complete: function(msg){
                window.parent.fontsLibrary = JSON.parse(msg.responseText);
            }
        });
    }

    function previewAction()
    {
        $g('.font-preview-text').on('input', function(){
            var $this = this,
                text = this.innerText;
            clearTimeout(delay);
            delay = setTimeout(function(){
                $g('.font-preview-text').not($this).text(text);
            }, 300);
        });
    }

    $g('body').on('click', function(){
        $g('.visible-select').removeClass('visible-select');
    });
    $g('.ba-custom-select').on('click', 'i, input', function(){
        let parent = $g(this).closest('.ba-custom-select');
        if (!parent.find('ul').hasClass('visible-select')) {
            setTimeout(function(){
                parent.find('ul').addClass('visible-select');
                parent.trigger('show');
            }, 100);
        }
    }).on('click', 'li', function(){
        let parent = $g(this).closest('.ba-custom-select');
        parent.find('li.selected').removeClass('selected');
        this.classList.add('selected');
        parent.find('input[type="text"]').val(this.textContent.trim());
        parent.find('input[type="hidden"]').val(this.dataset.value).trigger('change');
        parent.trigger('customAction');
    });

    $g('.modal').on('hide', function(){
        $g(this).addClass('ba-modal-close');
        setTimeout(function(){
            $g('.ba-modal-close').removeClass('ba-modal-close');
        }, 500);
    });

    $g('input.filter-search').on('input', function(){
        var search = this.value.toLowerCase();
        clearTimeout(delay);
        delay = setTimeout(function(){
            if (!search) {
                $g('.ba-options-group').css('display', '').prev().css('display', '');
            } else {
                $g('.ba-options-group').each(function(){
                    var font = this.dataset.font.toLowerCase();
                    if (font.indexOf(search) < 0) {
                        $g(this).hide().prev().hide();
                    } else {
                        $g(this).css('display', '').prev().css('display', '');
                    }
                });
            }
        }, 300);
    });

    previewAction();
    deleteFont();

    $g('.font-search').on('input', function(){
        var search = this.value.toLowerCase();
        clearTimeout(delay);
        delay = setTimeout(function(){
            if (!search) {
                $g('div.fonts-select li[data-value]').show();
            } else {
                $g('div.fonts-select li[data-value]').each(function(){
                    var font = this.dataset.value.toLowerCase();
                    if (font.indexOf(search) < 0) {
                        this.style.display = 'none';
                    } else {
                        this.style.display = 'block';
                    }
                });
            }
        }, 300);
    });

    $g('div.fonts-select').on('customAction', function(){
        var font = $g(this).find('input[type="hidden"]').val(),
            styles = $g(this).find('li[data-value="'+font+'"]')[0],
            str = '';
        if (styles) {
            styles = styles.dataset.style;
            styles = JSON.parse(styles);
            for (var i = 0; i < styles.length; i++) {
                if (styles[i] == 'italic') {
                    styles[i] = '400italic';
                }
                str += '<li data-value="'+styles[i].replace('talic', '')+'">';
                str += styles[i].replace('italic', ' Italic')+'</li>';
            }
            $g('.fonts-style-select ul').html(str);
            $g('.fonts-style-select').addClass('active');
            $g('.install-font').removeClass('active-button');
        }
        $g('.fonts-style-select input').val('');
    });

    $g('div.fonts-style-select').on('customAction', function(){
        var font = $g(this).find('input[type="hidden"]').val(),
            style = $g(this).find('li[data-value="'+font+'"]')[0];
        if (style) {
            $g('.install-font').addClass('active-button');
        }
    });

    $g('.install-font').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        var family = $g('#font-family').val(),
            style = $g('#font-style').val();
        if (family && style && click) {
            click = false;
            $g.ajax({
                type:"POST",
                dataType:'text',
                url: top.JUri+"index.php?option=com_gridbox&task=fonts.addFont",
                data:{
                    font_family : family,
                    font_style : style
                },
                complete: function(msg){
                    var obj = JSON.parse(msg.responseText),
                        link;
                    window.parent.app.showNotice(obj.msg, obj.type);
                    click = true;
                    if (obj.type != 'ba-alert') {
                        var file = document.createElement('link'),
                            text = 'Click Here And Start Typing';
                        $g('.font-preview-text').first().each(function(){
                            text = this.textContent;
                        });
                        link = 'https://fonts.googleapis.com/css?family='+family+':'+style;
                        link += '&subset=latin,cyrillic,greek,latin-ext,greek-ext,vietnamese,cyrillic-ext';
                        file.rel = 'stylesheet';
                        file.href = link;
                        document.head.append(file);
                        $g('.fonts-table').load(window.location.href+' #fonts-list', function(){
                            $g('.font-preview-text').text(text);
                            $g('#add-google-font-dialog').modal('hide');
                            previewAction();
                            deleteFont();
                            refreshFontsList();
                        });
                    }
                }
            });
        }
    });

    $g('div.ba-custom-select').on('show', function(){
        var $this = $g(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    });

    $g('a.add-new-font').on('click', function(event){
        event.preventDefault();
        $g('#add-google-font-dialog').modal();
    });

    $g('a.add-web-safe-fonts').on('click', function(event){
        event.preventDefault();
        let modal = $g('#add-web-safe-fonts-dialog');
        modal.find('input').val('');
        modal.find('.install-web-safe-font').removeClass('active-button');
        modal.modal();
    });

    $g('#add-web-safe-fonts-dialog').find('.ba-custom-select').on('customAction', function(){
        let modal = $g('#add-web-safe-fonts-dialog'),
            array = [];
        modal.find('input[type="hidden"]').each(function(){
            array.push(this.value.trim());
        });
        if (array[0] && array[1]) {
            modal.find('.install-web-safe-font').addClass('active-button');
        } else {
            modal.find('.install-web-safe-font').removeClass('active-button');
        }
    });0

    $g('.install-web-safe-font').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        var family = $g('.web-safe-fonts-family-select input[type="hidden"]').val().trim(),
            style = $g('.web-safe-fonts-weight-select input[type="hidden"]').val().trim();
        if (family && style && click) {
            click = false;
            $g.ajax({
                type:"POST",
                dataType:'text',
                url:top.JUri+"index.php?option=com_gridbox&task=fonts.addFont",
                data:{
                    font_family : family,
                    font_style : style,
                    web_safe_fonts: 'web-safe-fonts'
                },
                complete: function(msg){
                    var obj = JSON.parse(msg.responseText),
                        link;
                    window.parent.app.showNotice(obj.msg, obj.type);
                    click = true;
                    if (obj.type != 'ba-alert') {
                        var text = $g('.font-preview-text').first().text();
                        $g('.fonts-table').load(window.location.href+' #fonts-list', function(){
                            $g('.font-preview-text').text(text);
                            $g('#add-web-safe-fonts-dialog').modal('hide');
                            previewAction();
                            deleteFont();
                            refreshFontsList();
                        });
                    }
                }
            });
        }
    });

    $g('a.add-custom-font').on('click', function(event){
        event.preventDefault();
        $g('.custom-font-title, .custom-font-select').val('');
        $g('.custom-fonts-style-select input, .custom-fonts-files').val('');
        $g('.custom-fonts-style-select .selected i').remove();
        $g('.custom-fonts-style-select .selected').removeClass('selected');
        $g('.install-custom-font').removeClass('active-button');
        $g('#add-custom-font-dialog').modal();
    });

    $g('.custom-font-style, .custom-font-select').on('change', function(){
        $g('.install-custom-font').addClass('active-button');
        $g('.custom-font-title, .custom-font-select, .custom-font-style').each(function(){
            if (!this.value.trim()) {
                $g('.install-custom-font').removeClass('active-button');
                return false;
            }
        });
    });

    $g('.custom-font-title').on('input', function(){
        $g('.install-custom-font').addClass('active-button');
        $g('.custom-font-title, .custom-font-select, .custom-font-style').each(function(){
            if (!this.value.trim()) {
                $g('.install-custom-font').removeClass('active-button');
                return false;
            }
        });
    });

    $g('.custom-font-select').on('click', function(){
        $g('.custom-fonts-files').trigger('click');
    });

    $g('.custom-fonts-files').on('change', function(event){
        files = event.target.files;
        var types = ['woff2', 'woff', 'ttf', 'svg', 'eot', 'otf'],
            flag = true,
            nameStr = '';
        if (files.length != 0) {
            for (var i = 0; i < files.length; i++) {
                if (nameStr) {
                    nameStr += ', ';
                }
                nameStr += files[i].name;
                var name = files[i].name.split('.'),
                    ext = name[name.length - 1].toLowerCase();
                if ($g.inArray(ext, types) == -1) {
                    flag = false;
                    break;
                }
            }
            if (flag) {
                $g('.custom-font-select').val(nameStr).trigger('change');
            } else {
                $g('.custom-font-select').val('').trigger('change');
                window.parent.app.showNotice(window.parent.gridboxLanguage['NOT_SUPPORTED_FILE']);
            }
        }
    });

    $g('.install-custom-font').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button') && click) {
            click = false;
            var formData = new FormData(document.forms.custom_fonts),
                XHR = new XMLHttpRequest(),
                str = window.parent.gridboxLanguage['LOADING']+'<img src="';
            formData.append("font_family", $g('.custom-font-title').val());
            formData.append("font_style", $g('.custom-font-style').val());                
            str += window.parent.JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
            window.parent.app.showNotice(str);
            XHR.onreadystatechange = function(e) {
                if (XHR.readyState == 4) {
                    click = true;
                    var obj = JSON.parse(XHR.responseText);
                    if (obj.type != 'ba-alert') {
                        var text = $g('.font-preview-text').first().text();
                        $g('.fonts-table').load(window.location.href+' #fonts-list', function(){
                            window.parent.app.showNotice(obj.msg, obj.type);
                            $g('.font-preview-text').text(text);
                            $g('#add-custom-font-dialog').modal('hide');
                            previewAction();
                            deleteFont();
                            refreshFontsList();
                        });
                    } else {
                        window.parent.app.showNotice(obj.msg, obj.type);
                    }
                }
            }
            XHR.open("POST", top.JUri+"index.php?option=com_gridbox&task=fonts.addCustomFont");
            XHR.send(formData);
        }
    });

    $g('.refresh-fonts').on('click', function(){
        if (click) {
            click = false;
            $g(this).find('i').addClass('zmdi-hc-spin');
            setTimeout(function(){
                $g('.refresh-fonts i').removeClass('zmdi-hc-spin');
            }, 1500);
            $g.ajax({
                type : "POST",
                dataType : 'text',
                url : top.JUri+"index.php?option=com_gridbox&task=fonts.refreshList",
                complete : function(msg){
                    var obj = JSON.parse(msg.responseText);
                    window.parent.app.showNotice(obj.msg);
                    $g('.fonts-select ul').html(obj.str);
                    click = true;
                }
            });
        }
    });

    $g('.delete-fonts').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('disable-button')) {
            $g("#delete-dialog").modal();
        }
    });

    $g('#apply-delete').on('click', function(event){
        event.preventDefault();
        let array = [];
        $g('.font-checkbox input').each(function(){
            this.checked ? array.push(this.value) : null;
        });
        top.app.fetch(top.JUri+"index.php?option=com_gridbox&task=fonts.delete", {
            font_id: array
        }).then((text) => {
            window.parent.app.showNotice(text);
            $g("#delete-dialog").modal('hide');
            $g('.delete-fonts').addClass('disable-button');
            $g('.font-checkbox input').each(function(){
                this.checked ? this.closest('.ba-group-element').remove() : null;
            });
            $g('.ba-options-group').each(function(){
                if (!this.querySelector('.ba-group-element')) {
                    this.previousElementSibling.remove();
                    this.remove();
                }
            });
            refreshFontsList();
        });
    });
});