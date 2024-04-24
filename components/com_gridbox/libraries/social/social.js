/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function ($) {
    
    var social = function (element, options) {
        this.parent = $(element);
        this.options = options;
        this.desc = $('meta[name="description"]').attr('content');
        if (!this.desc) {
            this.desc = '';
        }
        this.desc = encodeURIComponent(this.desc);
    },
    socialNetwork = {
        facebook : {
            getNumber : function(data){
                if (data.og_object === undefined) {
                    return 0;
                } else {
                    return data.og_object.engagement.count;
                }
            },
            popupUrl : 'https://www.facebook.com/sharer/sharer.php?u={url}'
        },
        twitter: {
            popupUrl: 'https://twitter.com/intent/tweet?text='+encodeURIComponent($('title').text())+'&url={url}'
        },
        pinterest: {
            counterUrl: location.protocol + '//api.pinterest.com/v1/urls/count.json?url={url}&callback=?',
            getNumber: function(data) {
                return data.count;
            },
            popupUrl: 'https://pinterest.com/pin/create/button/?url={url}&description='+
                        encodeURIComponent($('title').text())+'&media='
        },
        vk: {
            counterUrl: 'https://vk.com/share.php?act=count&index=1&url={url}&callback=?',
            getNumber: function(data) {
                
            },
            popupUrl: 'http://vk.com/share.php?url={url}&title='+
                encodeURIComponent($('title').text())+'&description='
        },
        linkedin : {
            counterUrl : 'https://www.linkedin.com/countserv/count/share?url={url}&callback=?',
            getNumber: function(data) {
                return data.count;
            },
            popupUrl: 'http://www.linkedin.com/shareArticle?url={url}'
                    + '&text='+ encodeURIComponent($('title').text())
        }
    };
    
    social.prototype = {
        init : function(){
            for (var key in socialNetwork) {
                if (this.options.counters) {
                    if (socialNetwork[key].counterUrl && this.options[key]) {
                        this.setCounters(key);
                    }
                }
                this.click('.'+key, key)
            }
        },
        click :function(selector, key){
            var that = this;
            this.parent.find(selector).on('click.social', function(){
                var url = socialNetwork[key].popupUrl.replace('{url}', encodeURIComponent(window.location.href)),
                    image = $('meta[property="og:image"]')[0];
                url = socialNetwork[key].popupUrl.replace('{url}', window.location.href)
                if (image) {
                    image = image.content;
                } else {
                    image = document.querySelector('img');
                    if (image) {
                        image = image.src;
                    }
                }
                if (key == 'pinterest') {
                    url += image;
                    if (!image) {
                        return false;
                    }
                } else if (key == 'vk') {
                    url += that.desc+'&image='+image;
                }
                var win = window.open(url, 'sharer', 'toolbar=0, status=0, width=626, height=436');
                if (socialNetwork[key].counterUrl && that.options.counters) {
                    var interval = setInterval(function(){
                        if (win.closed) {
                            clearInterval(interval);
                            that.setCounters(key);
                        }
                    }, 500);
                }
            });
        },
        delete : function(){
            this.parent.find('.facebook, .twitter, .pinterest, .linkedin, .vk').off('click.social');
        },
        post: function(path, data, success){
            var xhr = new XMLHttpRequest();
            xhr.open('POST', path, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (this.readyState === 4) {
                    if ((this.status >= 200 && this.status < 300) || this.status === 304) {
                        var response = JSON.parse(this.responseText);
                        success(response);
                    }
                }
            };
            xhr.send(data);
            xhr = null;
        },
        setCounters : function(key){
            var parent = this.parent,
                url = socialNetwork[key].counterUrl.replace('{url}', encodeURIComponent(window.location.href));
            if (key == 'vk' && !window.VK) {
                VK = {};
                VK.Share = {
                    count: function(index, count) {
                        $('.vk .social-counter').text(count);
                    }
                };
            }
            $.getJSON(url, function(data){
                var number = socialNetwork[key].getNumber(data);
                if (!number) {
                    number = 0;
                }
                parent.find('.'+key+' .social-counter').text(number);
            });
        }
    }
    
    $.fn.social = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('social'),
                options = $.extend({}, $.fn.social.defaults, typeof option == 'object' && option);
            $this.find('.google').remove();
            if (!data) {
                $this.data('social', (data = new social(this, options)));
            }
            if (option == 'delete') {
                data.delete('social');
                $this.removeData();
            } else {
                data.init();
            }
        });
    }
    
    $.fn.social.defaults = {
        "facebook" : true,
        "twitter" : true,
        "linkedin" : true,
        "pinterest" : true,
        "vk" : true,
        counters : true
    }
    
}(window.$g ? window.$g : window.jQuery);