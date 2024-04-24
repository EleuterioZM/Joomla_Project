/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initAddToCart = function(obj, key){
    let parent = $g('#'+key),
        variations = parent.find('.ba-add-to-cart-variation'),
        extra = parent.find('.ba-add-to-cart-extra-option');
    app.addToCart.getProduct(obj, parent);
    app.addToCart.updateExtraPrice(obj, extra);
    if (themeData.page.view != 'gridbox') {
        for (let ind in obj.productData.variations) {
            if (obj.productData.variations[ind].default) {
                obj.defaultProduct = obj.productData.variations[ind];
                break;
            }
        }
        if (obj.defaultProduct && !obj.product) {
            app.addToCart.updateVariationData(parent, obj, obj.defaultProduct.variation, true);
        }
    }
    parent.find('.add-to-cart-booking-hours-wrapper').on('click', '.add-to-cart-booking-available-hours', function(){
        parent.find('.add-to-cart-booking-available-hours.active').removeClass('active');
        parent.find('.add-to-cart-booking-hours-wrapper .ba-variation-notice').remove();
        this.classList.add('active');
        obj.product.time = {
            start: this.dataset.start,
            end: this.dataset.end
        }
        app.addToCart.booking.guest.update(parent, this);
    });
    app.addToCart.booking.guest.setEvents(parent, obj);
    parent.find('.add-to-cart-booking-calendar input').each(function(){
        if (this.dataset.type == 'range-dates') {
            this.range = $g('[data-type="range-dates"]').not(this)[0];
            this.disableFunc = app.addToCart.booking.calendar.disableMultiple;
        } else {
            this.disableFunc = app.addToCart.booking.calendar.disableSingle;
        }
    }).on('update', function(event, cell){
        let booking = app.addToCart.product.booking
        if (booking.type == 'multiple') {
            app.addToCart.setCartPrices(parent, obj);
        } else if (booking.single.time == 'yes') {
            app.fetch(JUri+'index.php?option=com_gridbox&task=calendar.getSingleSlots', {
                id: app.addToCart.product.product_id,
                date: this.dataset.value
            }).then((text) => {
                let times = JSON.parse(text),
                    wrapper = document.querySelector('.add-to-cart-booking-available-hours-wrapper');
                wrapper.innerHTML = '';
                times.forEach((time) => {
                    let hours = document.createElement('span');
                    hours.className = 'add-to-cart-booking-available-hours';
                    for (let key in time) {
                        hours.dataset[key] = time[key];
                    }
                    hours.textContent = time.start;
                    wrapper.append(hours)
                });
                app.addToCart.booking.guest.update(parent, parent.find('.add-to-cart-booking-available-hours')[0]);
            })
        } else if (booking.single.type == 'group-session') {
            app.addToCart.booking.guest.update(parent, cell);
        }
    });
    parent.find('.ba-add-to-cart-attached-files').on('click', '.post-intro-image', function(){
        let wrapper = $g(this).closest('.ba-add-to-cart-attached-files'),
            div = document.createElement('div'),
            index = 0,
            $this = this,
            endCoords = startCoords = {},
            image = document.createElement('img'),
            images = [],
            width = this.offsetWidth,
            height = this.offsetHeight,
            offset = $g(this).offset(),
            modal = $g(div),
            img = document.createElement('div');
        img.style.backgroundImage = 'url('+this.dataset.image+')';
        div.className = 'ba-image-modal instagram-modal ba-comments-image-modal';
        img.style.top = (offset.top - $g(window).scrollTop())+'px';
        img.style.left = offset.left+'px';
        img.style.width = width+'px';
        img.style.height = height+'px';
        div.appendChild(img);
        modal.on('click', function(){
            app.addToCart.closeModalImage(modal, images, index)
        }).on('touchstart', function(event){
            endCoords = event.originalEvent.targetTouches[0];
            startCoords.pageX = event.originalEvent.targetTouches[0].pageX;
            startCoords.pageY = event.originalEvent.targetTouches[0].pageY;
        }).on('touchmove', function(event){
            endCoords = event.originalEvent.targetTouches[0];
        }).on('touchend', function(event){
            let vDistance = endCoords.pageY - startCoords.pageY,
                hDistance = endCoords.pageX - startCoords.pageX,
                xabs = Math.abs(endCoords.pageX - startCoords.pageX),
                yabs = Math.abs(endCoords.pageY - startCoords.pageY);
            if (hDistance >= 100 && xabs >= yabs) {
                index = app.addToCart.getPrevImage(img, images, index);
            } else if (hDistance <= -100 && xabs >= yabs) {
                index = app.addToCart.getNextImage(img, images, index);
            }
        });
        $g('body').append(div);
        width = window.innerWidth - document.documentElement.clientWidth;
        let header = document.querySelector('body > header.header'),
            style = header ? getComputedStyle(header): {},
            hWidth = width + (themeData.page.view == 'gridbox' && app.view =='desktop' ? 103 : 0);
        document.body.style.width = 'calc(100% - '+width+'px)';
        document.body.style.overflow = 'hidden';
        $g('.ba-sticky-header').css('width', 'calc(100% - '+hWidth+'px)');
        if (style.position == 'fixed') {
            $g('body > header.header').css('width', 'calc(100% - '+hWidth+'px)');
        }
        image.onload = function(){
            app.addToCart.setImage(this);
        }
        image.src = this.dataset.image;
        wrapper.find('.post-intro-image').each(function(ind){
            images.push(this);
            if (this == $this) {
                index = ind;
            }
        });
        setTimeout(function(){
            let str = '';
            if (images.length > 1) {
                str += '<i class="ba-icons ba-icon-chevron-left"></i><i class="ba-icons ba-icon-chevron-right"></i>';
            }
            str += '<i class="ba-icons ba-icon-trash remove-attachment-image"></i>';
            str += '<i class="ba-icons ba-icon-close">';
            modal.append(str);
            modal.find('.ba-icon-chevron-left').on('click', function(event){
                event.stopPropagation();
                index = app.addToCart.getPrevImage(img, images, index);
            });
            modal.find('.ba-icon-chevron-right').on('click', function(event){
                event.stopPropagation();
                index = app.addToCart.getNextImage(img, images, index);
            });
            modal.find('.ba-icon-close').on('click', function(event){
                event.stopPropagation();
                app.addToCart.closeModalImage(modal, images, index)
            });
            modal.find('.remove-attachment-image').on('click', function(event){
                event.stopPropagation();
                let $image = $g(images[index]),
                    array = [];
                images.forEach((el, i) => {
                    if (i != index) {
                        array.push(el);
                    }
                });
                images = array;
                $image.closest('.ba-add-to-cart-attachment').find('.remove-attachment-file').trigger('click');
                if (images.length == 0) {
                    app.addToCart.closeModalImage(modal, images, 0);
                } else {
                    index = app.addToCart.getNextImage(img, images, index - 1);
                }
            });
        }, 600);
        $g(window).on('keyup.instagram', function(event) {
            event.preventDefault();
            event.stopPropagation();
            if (event.keyCode === 37) {
                index = app.addToCart.getPrevImage(img, images, index);
            } else if (event.keyCode === 39) {
                index = app.addToCart.getNextImage(img, images, index);
            } else if (event.keyCode === 27) {
                app.addToCart.closeModalImage(modal, images, index)
            }
        });
    });
    parent.find('.ba-add-to-cart-attached-files').on('click', '.remove-attachment-file', function(){
        let $this = this.closest('.ba-add-to-cart-attachment'),
            wrapper = this.closest('.ba-add-to-cart-row-value');
        $this.remove();
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.removeAttachment', {
            id: $this.dataset.id
        });
        app.addToCart.extraAction(wrapper, parent, extra, obj);
    });
    parent.find('.ba-add-to-cart-attach-file[data-droppable="1"]').on('dragenter dragover', function(event){
        event.preventDefault();
        event.stopPropagation();
        this.classList.add('ba-add-to-cart-attach-file-drag-over');
        return false;
    }).on('dragleave', function(event){
        event.preventDefault();
        event.stopPropagation();
        this.classList.remove('ba-add-to-cart-attach-file-drag-over');        
        return false;
    }).on('drop', function(event){
        event.preventDefault();
        event.stopPropagation();
        this.classList.remove('ba-add-to-cart-attach-file-drag-over');
        let files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;
        $g(this).find('input').each(function(){
            this.files = files;
        }).trigger('change');
    });
    parent.find('.ba-add-to-cart-drag-drop-attach-file-btn').on('click', function(){
        this.closest('.ba-add-to-cart-attach-file').querySelector('input').click();
    })
    parent.find('.ba-add-to-cart-attach-file input').on('change', function(){
        let uploaded = this.closest('.ba-add-to-cart-upload-file').querySelectorAll('.ba-add-to-cart-attachment').length;
        if (this.dataset.count !== '' && this.files.length + uploaded > this.dataset.count * 1) {
            app.showNotice(app._('MAXIMUM_NUMBER_FILES_EXCEEDED'), 'ba-alert');
            this.value = '';
            return;
        }
        let files = [].slice.call(this.files),
            size = msg = ext = null,
            id = this.closest('.ba-add-to-cart-extra-option').dataset.ind * 1,
            types = this.dataset.types.replace(/ /g, '').split(','),
            flag = this.dataset.uploading != 'pending';
        for (let i = 0; i < files.length; i++) {
            size = this.dataset.size * 1000;
            ext = app.addToCart.getExt(files[i].name);
            if (flag && (size < files[i].size || types.indexOf(ext) == -1)) {
                msg = size < files[i].size ? 'NOT_ALLOWED_FILE_SIZE' : 'NOT_SUPPORTED_FILE';
                flag = false;
                app.showNotice(app._(msg), 'ba-alert');
                break
            }
        }
        if (flag) {
            this.closest('.ba-add-to-cart-upload-file').querySelectorAll('.ba-variation-notice').forEach((el) => {
                el.remove();
            });
            this.dataset.uploading == 'pending';
            app.addToCart.uploadAttachmentFile(files, this, id);
        }
        this.value = '';
    }).on('uploaded', function(){
        app.addToCart.extraAction(this, parent, extra, obj);
    });
    parent.find('.ba-add-to-wishlist').on('click', function(){
        app.addToCart.showNotices(parent);
        if (obj.product && !this.clicked && !document.querySelector('.ba-variation-notice')) {
            let $this = this;
            this.clicked = true;
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.addProductToWishlist', {
                id: obj.productData.data.product_id,
                variation: obj.product.variation ? obj.product.variation : '',
                extra_options: JSON.stringify(app.addToCart.extra_options.options),
                booking: app.addToCart.isBooking() ? app.addToCart.booking.get(parent, obj.product) : '{}'
            }).then(function(text){
                let object = JSON.parse(text),
                    str = '';
                if (('status' in object) && !object.status && object.message) {
                    app.showNotice(object.message, 'ba-alert');
                } else {
                    if (app.wishlist) {
                        app.wishlist.updateWishlist();
                    }
                    app.addToCart.clear(obj, parent);
                    $this.clicked = false;
                    if (object.images.length) {
                        object.image = object.images[0];
                    }
                    if (object.image && !app.isExternal(object.image)) {
                        object.image = JUri+object.image;
                    }
                    str = '<span class="ba-product-notice-message">';
                    if (object.image) {
                        str += '<span class="ba-product-notice-image-wrapper"><img src="'+object.image+'"></span>';
                    }
                    str += '<span class="ba-product-notice-text-wrapper">'+object.title+
                        ' '+app._('ADDED_TO_WISHLIST')+'</span></span>';
                    app.showNotice(str, 'ba-product-notice');
                }
            });
        }
    });
    parent.find('.ba-add-to-cart-button-wrapper a').on('click', function(event){
        event.preventDefault();
        app.addToCart.showNotices(parent);
        let min = obj.productData.data.min ? obj.productData.data.min * 1 : 1;
        if (obj.product && (obj.product.stock == '' || obj.product.stock * 1 >= min) && !this.clicked
            && !document.querySelector('.ba-variation-notice')) {
            let $this = this,
                qty = document.querySelector('.ba-add-to-cart-quantity'),
                quantity = qty ? qty.querySelector('input').value * 1 : min;
            this.clicked = true;
            app.fetch(JUri+'index.php?option=com_gridbox&view=editor&task=store.addProductToCart', {
                id: obj.productData.data.product_id,
                variation: obj.product.variation ? obj.product.variation : '',
                extra_options: JSON.stringify(app.addToCart.extra_options.options),
                quantity: quantity,
                booking: app.addToCart.isBooking() ? app.addToCart.booking.get(parent, obj.product) : '{}'
            }).then(function(text){
                if (app.storeCart) {
                    app.storeCart.updateCartTotal();
                    $g('.ba-item-cart a').first().trigger('click');
                } else {
                    window.location.href = $this.dataset.url
                }
                app.addToCart.clear(obj, parent);
                $this.clicked = false;
            });
        }
    });
    parent.find('.ba-add-to-cart-quantity i[data-action]').on('click', function(){
        if (!obj.product && !this.closest('.ba-add-to-cart-button-wrapper').classList.contains('disabled')) {
            app.addToCart.showNotices(parent);
        }
        if (!obj.product) {
            return false;
        }
        if (!this.input) {
            this.input = this.closest('.ba-add-to-cart-quantity').querySelector('input');
        }
        let value = this.dataset.action == '+' ? this.input.value * 1 + 1 : this.input.value * 1 - 1,
            min = obj.productData.data.min ? obj.productData.data.min * 1 : 1,
            $this = this;
        if (value >= min && (obj.product.stock == '' || value <= obj.product.stock * 1)) {
            this.input.value = value;
            app.addToCart.setCartPrices(parent, obj);
        } else if (obj.product.stock != '' && obj.product.stock != '0' && value > obj.product.stock * 1) {            
            if (!this.notice) {
                this.notice =  document.createElement('span');
                this.notice.className = 'ba-variation-notice';
                this.notice.textContent = app._('IN_STOCK')+' '+obj.product.stock;
                this.closest('.ba-add-to-cart-quantity').append(this.notice);
            }
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                $this.notice.remove();
                $this.notice = null;
            }, 3000);
        }
    });
    parent.find('.ba-add-to-cart-quantity input').on('input', function(){
        let match = this.value.match(/\d+/),
            value = this.value;
        if (!obj.product) {
            value = 1;
        } else if (!match) {
            value = '';
        } else if (match) {
            value = match[0] * 1;
        }
        if (obj.product && obj.product.stock == '0' && value > 0) {
            value = 1;
        } else if (obj.product && obj.product.stock != '' && value > obj.product.stock * 1) {
            value = obj.product.stock * 1;
        }
        if (String(value) != this.value) {
            this.value = value;
        }
        app.addToCart.setCartPrices(parent, obj);
    }).on('blur', function(){
        let min = obj.productData.data.min ? obj.productData.data.min * 1 : 1;
        if (this.value != '' && this.value * 1 < min) {
            this.value = min;
            app.addToCart.setCartPrices(parent, obj);
        }
    });
    variations.find('.ba-add-to-cart-row-value:not([data-type="dropdown"]) > span').on('click', function(){
        app.addToCart.variationAction(this, parent, variations, obj);
    });
    variations.find('.ba-add-to-cart-row-value[data-type="dropdown"]').on('customAction', function(){
        app.addToCart.variationAction(this.querySelector('li.selected'), parent, variations, obj);
    });
    variations.find('.ba-add-to-cart-row-value').on('change', 'input', function(){
        app.addToCart.variationAction(this, parent, variations, obj);
    });
    extra.find('.ba-add-to-cart-row-value:not([data-type="dropdown"]) > span').on('click', function(){
        app.addToCart.extraAction(this, parent, extra, obj);
    });
    extra.find('.ba-add-to-cart-row-value[data-type="dropdown"]').on('customAction', function(){
        app.addToCart.extraAction(this.querySelector('li.selected'), parent, extra, obj);
    });
    extra.find('.ba-add-to-cart-row-value').on('change', 'input', function(){
        app.addToCart.extraAction(this, parent, extra, obj);
    });
    extra.find('.ba-add-to-cart-row-value').on('input focus', 'input[type="text"], textarea', function(){
        app.addToCart.extraAction(this, parent, extra, obj);
    });
    let select = localStorage.getItem('select-options');
    if (select) {
        localStorage.removeItem('select-options');
        app.addToCart.showNotices(parent);
    }
    initItems();
}

app.addToCart = {
    booking: {
        guest: {
            update: (parent, cell) => {
                if (app.addToCart.product.booking.single.type != 'group-session') {
                    return
                }
                parent.find('.ba-add-to-cart-guests input').each(function(){
                    this.value = 1;
                    this.dataset.max = cell.dataset.guests;
                    parent.find('.ba-add-to-cart-guests input').trigger('update');
                });
            },
            isGroup: () => {
                let options = app.addToCart.product.booking;

                return options.type == 'single' && options.single.type == 'group' || options.single.type == 'group-session'
            },
            check: () => {
                if (!app.addToCart.booking.guest.input) {
                    app.addToCart.booking.guest.input = document.querySelector('.ba-add-to-cart-guests input');
                }
            },
            get: () => {
                app.addToCart.booking.guest.check();

                return app.addToCart.booking.guest.input.value * 1;
            },
            setEvents: (parent, obj) => {
                parent.find('.ba-add-to-cart-guests i[data-action]').on('click', function(){
                    app.addToCart.booking.guest.check();
                    let input = app.addToCart.booking.guest.input,
                        value = this.dataset.action == '+' ? input.value * 1 + 1 : input.value * 1 - 1;
                    if (value >= 1 && value <= input.dataset.max * 1) {
                        input.value = value;
                        parent.find('.ba-add-to-cart-guests input').trigger('update');
                    }
                });
                parent.find('.ba-add-to-cart-guests input').on('update', function(){
                    parent.find('.ba-add-to-cart-guests i[data-action]').removeClass('disabled');
                    if (this.value >= this.dataset.max * 1) {
                        parent.find('.ba-add-to-cart-guests i[data-action="+"]').addClass('disabled');
                    } else if (this.value <= 1) {
                        parent.find('.ba-add-to-cart-guests i[data-action="-"]').addClass('disabled');
                    }
                    app.addToCart.setCartPrices(parent, obj);
                })
            }
        },
        get: (parent, product, notParse) => {
            let data = {
                    dates: Array.from(parent[0].querySelectorAll('.add-to-cart-booking-calendar input'), el => el.dataset.value),
                    time: product.time ? product.time : '',
                    guests: app.addToCart.booking.guest.isGroup() ? app.addToCart.booking.guest.get() : ''
                }

            return notParse ? data : JSON.stringify(data);
        },
        getCartMultiple: (parent) => {
            let multiple = 1;
            if (app.addToCart.booking.guest.isGroup()) {
                multiple = app.addToCart.booking.guest.get()
            } else if (app.addToCart.product.booking.type == 'multiple') {
                let data = app.addToCart.booking.get(parent, app.addToCart.product, true);
                multiple = (+new Date(data.dates[1]) - +new Date(data.dates[0])) / 1000 / 60 / 60 / 24;
            }

            return multiple;
        },
        calendar: {
            blocked: {},
            addDays: (date, days) => {
                let result = new Date(date);
                result.setDate(result.getDate() + days * 1);

              return result;
            },
            isBlocked: (first, date) => {
                flag = false;
                for (let blocked in app.addToCart.booking.calendar.blocked) {
                    if (first < date && date > blocked && first < blocked) {
                        flag = true;
                        break;
                    }
                }

                return flag
            },
            disableMultiple: function(date, cell){
                let flag = cell.dataset.blocked == 1,
                    calendar = app.addToCart.booking.calendar,
                    booking = app.addToCart.product.booking.multiple;
                if (cell.dataset.blocked == 1) {
                    calendar.blocked[date] = true;
                }
                if (gridboxCalendar.multiple.length == 1) {
                    let min = booking.min ? booking.min * 1 : 0,
                        first = gridboxCalendar.multiple[0].dataset.date,
                        max = booking.max ? calendar.addDays(first, booking.max * 1) < new Date(date) : false;
                    flag = flag || calendar.isBlocked(first, date) || first > date || calendar.addDays(first, min) > new Date(date) || max;
                } else if (gridboxCalendar.multiple.length == 0) {
                    flag = flag || (this.dataset.now > date) || (this.dataset.early ? this.dataset.early < date : false);
                }

                return flag;
            },
            disableSingle: function(date, cell){
                let booking = app.addToCart.product.booking.single,
                    slot = booking.time == 'yes' ? cell.dataset.slots * 1 == 0 : cell.dataset.blocked == 1;

                return (this.dataset.now > date) || (this.dataset.early ? this.dataset.early < date : false) || slot;
            }
        }
    },
    isBooking: () => {
        return app.addToCart.product && app.addToCart.product.booking && app.addToCart.product.booking.type;
    },
    extra_options: {
        options:{},
        price: 0
    },
    imageTypes: ['jpg', 'png', 'gif', 'svg', 'jpeg', 'ico', 'webp'],
    getExt: function(name){
        let array = name.split('.');
        
        return array[array.length - 1].toLowerCase();
    },
    isImage: function(name){
        return this.imageTypes.indexOf(this.getExt(name)) != -1;
    },
    uploadAttachmentFile: function(files, $this, option_id){
        if (files.length == 0) {
            $this.dataset.uploading == '';
            $g($this).trigger('uploaded');
            return;
        }
        let file = files.shift(),
            container = $g($this).closest('.ba-add-to-cart-upload-file').find('.ba-add-to-cart-attached-files'),
            str = '',
            attachment = document.createElement('div'),
            xhr = new XMLHttpRequest(),
            formData = new FormData(),
            isImage = this.isImage(file.name);
        attachment.className = 'ba-add-to-cart-attachment';
        if (isImage) {
            str += '<span class="post-intro-image"></span>';
        } else {
            str += '<i class="ba-icons ba-icon-attachment"></i>';
        }
        str += '<span class="attachment-title">'+file.name;
        str += '</span><span class="attachment-progress-bar-wrapper"><span class="attachment-progress-bar">';
        str += '</span></span><i class="ba-icons ba-icon-trash remove-attachment-file"></i>';
        attachment.innerHTML = str;
        if (isImage) {
            let reader = new FileReader();
            reader.onloadend = function() {
                attachment.querySelectorAll('.post-intro-image').forEach((img) => {
                    img.style.backgroundImage = 'url('+reader.result+')';
                    img.dataset.image = reader.result;
                });
            }
            reader.readAsDataURL(file);
        }
        container.append(attachment);
        formData.append('file', file);
        formData.append('id', themeData.page.id);
        formData.append('option_id', option_id);
        xhr.upload.onprogress = function(event){
            attachment.querySelector('.attachment-progress-bar').style.width = Math.round(event.loaded / event.total * 100)+"%";
        }
        xhr.onload = xhr.onerror = function(){
            try {
                let obj = JSON.parse(this.responseText);
                attachment.dataset.id = obj.id;
                attachment.dataset.attachment = obj.attachment_id;
            } catch (e){
                console.info(e)
                console.info(this.responseText)
            }
            app.addToCart.uploadAttachmentFile(files, $this, option_id);
            setTimeout(function(){
                attachment.classList.add('attachment-file-uploaded');
            }, 300);
        };
        xhr.open("POST", JUri+"index.php?option=com_gridbox&task=store.uploadAttachmentFile", true);
        xhr.send(formData);
    },
    setImage: function(image){
        let imgHeight = image.naturalHeight,
            imgWidth = image.naturalWidth,
            modal = $g('.ba-image-modal.instagram-modal').removeClass('instagram-fade-animation'),
            wWidth = $g(window).width(),
            wHeigth = $g(window).height(),
            percent = imgWidth / imgHeight,
            modalTop, left;
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
        modalTop = (wHeigth - imgHeight) / 2,
        left = (wWidth - imgWidth) / 2;
        setTimeout(function(){
            modal.find('> div').css({
                width: Math.round(imgWidth),
                height: Math.round(imgHeight),
                left: Math.round(left),
                top: Math.round(modalTop)
            }).addClass('instagram-fade-animation');
        }, 1);
    },
    getPrevImage: function(img, images, index){
        let ind = images[index - 1] ? index - 1 : images.length - 1;
        image = document.createElement('img');
        image.onload = function(){
            app.addToCart.setImage(this);
        }
        image.src = images[ind].dataset.image;
        img.style.backgroundImage = 'url('+image.src+')';

        return ind;
    },
    getNextImage: function(img, images, index){
        let ind = images[index + 1] ? index + 1 : 0;
        image = document.createElement('img');
        image.onload = function(){
            app.addToCart.setImage(this);
        }
        image.src = images[ind].dataset.image;
        img.style.backgroundImage = 'url('+image.src+')';

        return ind;
    },
    closeModalImage: function(modal, images, index){
        $g(window).off('keyup.instagram');
        modal.addClass('image-lightbox-out');
        if (images.length == 0) {
            modal.addClass('image-lightbox-fade-out')
        } else {
            let $image = $g(images[index]), 
                width = $image.width(),
                height = $image.height(),
                offset = $image.offset();
            modal.find('> div').css({
                'width' : width,
                'height' : height,
                'left' : offset.left,
                'top' : offset.top - $g(window).scrollTop()
            });
        }
        setTimeout(function(){
            modal.remove();
            document.body.style.width = '';
            document.body.style.overflow = '';
            $g('.ba-sticky-header').css('');
            $g('body > header.header').css('width', '');
        }, 500);
    },
    createNotice: function(text, $this){
        let span = document.createElement('span');
        span.className = 'ba-variation-notice';
        span.textContent = text;
        $this.querySelector('.ba-add-to-cart-row-label').append(span);
    },
    showNotices: function(parent){
        parent.find('.ba-add-to-cart-variation, .add-to-cart-booking-hours-wrapper').each(function(){
            if (!this.querySelector('.active') && !this.querySelector('.ba-variation-notice')) {
                app.addToCart.createNotice(app._('PLEASE_SELECT_OPTION'), this);
            }
        });
        parent.find('.ba-add-to-cart-extra-option').each(function(){
            if (this.dataset.required == 1 && this.dataset.type == 'textinput' && !this.querySelector('input').value.trim()) {
                app.addToCart.createNotice(app._('PLEASE_SELECT_OPTION'), this);
            } else if (this.dataset.required == 1 && this.dataset.type == 'textarea' && !this.querySelector('textarea').value.trim()) {
                app.addToCart.createNotice(app._('PLEASE_SELECT_OPTION'), this);
            } else if (this.dataset.required == 1 && this.dataset.type != 'textarea' && this.dataset.type != 'textinput'
                && (!this.querySelector('.active') && !this.querySelector('.ba-add-to-cart-attachment'))
                && !this.querySelector('.ba-variation-notice')) {
                app.addToCart.createNotice(app._('PLEASE_SELECT_OPTION'), this);
            }
        });
    },
    getProduct: function(obj, parent){
        let variations = parent.find('.ba-add-to-cart-variation');
        if (themeData.page.view == 'gridbox') {
            obj.product = null;
        } else if (variations.length) {
            let keys = [],
                key = '';
            variations.each(function(){
                this.querySelectorAll('.active, .selected').forEach(function($this){
                    keys.push($this.dataset.value);
                });
            });
            key = keys.join('+');
            obj.product = obj.productData.variations[key] ? obj.productData.variations[key] : null;
        } else {
            obj.product = obj.productData.data;
        }
        this.product = obj.product;
    },
    clear: function(obj, parent){
        let variations = parent.find('.ba-add-to-cart-variation').each(function(){
                let variation = $g(this);
                variation.find('.active').removeClass('active');
                variation.find('.selected').removeClass('selected');
                variation.find('.disabled').removeClass('disabled');
                variation.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
                variation.find('.ba-custom-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = '';
                    this.querySelector('input[type="text"]').value = app._('SELECT');
                });
            }),
            requiredExtra = false,
            extra = parent.find('.ba-add-to-cart-extra-option').each(function(){
                let variation = $g(this);
                requiredExtra = this.dataset.required == 1 ? true : false;
                variation.find('.active').removeClass('active');
                variation.find('.selected').removeClass('selected');
                variation.find('.disabled').removeClass('disabled');
                variation.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
                variation.find('input[type="text"], textarea').val('');
                variation.find('.ba-custom-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = '';
                    this.querySelector('input[type="text"]').value = app._('SELECT');
                });
            });
        this.extra_options.options = {};
        this.extra_options.price = 0;
        parent.find('.add-to-cart-booking-available-hours.active').removeClass('active');
        parent.find('.ba-add-to-cart-guests input').val(1);
        parent.find('.ba-add-to-cart-attachment').remove();
        if (obj.defaultProduct) {
            app.addToCart.updateVariationData(parent, obj, obj.defaultProduct.variation, true);
        } else {
            app.addToCart.getProduct(obj, parent);
            parent.find('.ba-add-to-cart-button-wrapper').removeClass('disabled');
            $g('.ba-item-product-slideshow').each(function(){
                app.addToCart.initSlideshow(app.items[this.id], 'original', this);
            });
            $g('.ba-item-product-gallery').each(function(){
                app.addToCart.initGallery(app.items[this.id], 'original', this);
            });
            let min = obj.productData.data.min ? obj.productData.data.min * 1 : 1,
                stock = obj.productData.data.stock,
                data = {
                    price: obj.productData.data.price,
                    sale_price: obj.productData.data.sale_price,
                    button: variations.length == 0 && !requiredExtra ? obj['button-label'] : app._('SELECT_AN_OPTION'),
                    stock: stock !== '' && stock * 1 < min ? app._('OUT_OF_STOCK') : stock,
                    sku: obj.productData.data.sku
                }
            app.addToCart.setCartValues(parent, data, obj);
            app.addToCart.clearSearch(obj);
        }
    },
    clearSearch: function(obj){
        let search = window.location.search,
            url = window.location.href;
        if (window.location.search) {
            for (let variation in obj.productData.variations) {
                if (window.location.search.indexOf(obj.productData.variations[variation].url) != -1) {
                    url = url.replace(obj.productData.variations[variation].url, '');
                    search = search.replace(obj.productData.variations[variation].url, '');
                    if (search == '?') {
                        url = url.replace('?', '');
                    }
                    window.history.replaceState(null, null, url);
                    break;
                }
            }
        }
    },
    updateExtraPrice: function(obj, extra){
        this.extra_options.options = {};
        this.extra_options.price = 0;
        extra.find('.active[data-value], .selected[data-value], input, textarea').each(function(){
            let parent = this.closest('.ba-add-to-cart-extra-option'),
                id = parent.dataset.ind;
            if ((parent.dataset.type == 'textinput' || parent.dataset.type == 'textarea') && this.value.trim()) {
                let value = this.value.trim(),
                    option = obj.productData.data.extra_options[id].items[0];
                app.addToCart.extra_options.options[id+'-0'] = {
                    price: option.price,
                    text: value,
                    field_id: id
                };
                if (option.price) {
                    app.addToCart.extra_options.price += option.price * 1;
                }
            } else if ((this.localName != 'input' && this.localName != 'textarea') || this.checked) {
                let value = this.localName == 'input' ? this.value : this.dataset.value,
                    option = obj.productData.data.extra_options[id].items[value];
                app.addToCart.extra_options.options[value] = {
                    price: option.price,
                    field_id: id
                };
                if (option.price) {
                    app.addToCart.extra_options.price += option.price * 1;
                }
            }
        });
        let hasFileQty = false,
            fileQty = 0;
        extra.find('.ba-add-to-cart-attached-files').each(function(){
            let charge = this.dataset.charge,
                quantity = this.dataset.quantity,
                id = this.closest('.ba-add-to-cart-extra-option').dataset.ind,
                files = this.querySelectorAll('.ba-add-to-cart-attachment'),
                option = obj.productData.data.extra_options[id].items[0];
            if (quantity == 1 && (this.closest('.ba-add-to-cart-extra-option').dataset.required == 1 || files.length > 0)) {
                hasFileQty = true;
            }
            files.forEach(function(file){
                let attachment = file.dataset.attachment * 1,
                    value = file.dataset.id * 1;
                if (!app.addToCart.extra_options.options[attachment]) {
                    app.addToCart.extra_options.options[attachment] = {
                        attachments: [],
                        price: option.price,
                        field_id: id
                    };
                }
                app.addToCart.extra_options.options[attachment].attachments.push(value);
                if (option.price && charge == 1) {
                    app.addToCart.extra_options.price += option.price * 1;
                }
                if (quantity == 1) {
                    fileQty++;
                }
            });
            if (files.length > 0 && charge == 0 && option.price) {
                app.addToCart.extra_options.price += option.price * 1;
            }
        });
        extra.closest('.ba-add-to-cart-wrapper').find('.ba-add-to-cart-quantity').each(function(){
            this.classList[hasFileQty ? 'add' : 'remove']('file-quantity-enabled');
            if (hasFileQty) {
                this.querySelector('input').value = fileQty;
                let parent = $g(this).closest('.ba-item-add-to-cart');
                app.addToCart.setCartPrices(parent, obj);
            }
        });
    },
    extraAction: function($this, parent, extra, obj){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let variations = parent.find('.ba-add-to-cart-variation'),
            variation = $g($this).closest('.ba-add-to-cart-extra-option'),
            flag = true;
        variation.find('.active').removeClass('active');
        variation.find('.ba-variation-notice').remove();
        if ($this.type != 'text' && $this.localName != 'textarea') {
            $this.classList.add('active');
        }
        extra.find('.disabled').removeClass('disabled');
        extra.each(function(){
            if (flag && this.dataset.required == 1 && (this.dataset.type == 'textarea' || this.dataset.type == 'textinput')) {
                flag = this.querySelector('input, textarea').value.trim() != '';
            } else if (flag && this.dataset.required == 1 && !this.querySelector('input[type="radio"], input[type="checkbox"]')) {
                flag = this.querySelector('.active[data-value], .selected[data-value], .ba-add-to-cart-attachment');
            } else if (flag && this.dataset.required == 1) {
                let checked = false;
                this.querySelectorAll('input').forEach(function(input){
                    if (!checked) {
                        checked = input.checked;
                    }
                });
                flag = checked;
            }
        });
        variations.each(function(){
            if (flag && !this.querySelector('input[type="radio"]')) {
                flag = this.querySelector('.active[data-value], .selected[data-value]');
            } else if (flag) {
                let checked = false;
                this.querySelectorAll('input').forEach(function(input){
                    if (!checked) {
                        checked = input.checked;
                    }
                });
                flag = checked;
            }
        });
        this.updateExtraPrice(obj, extra);
        parent.find('.ba-add-to-cart-buttons-wrapper a').text(flag ? obj['button-label'] : app._('SELECT_AN_OPTION'));
        this.setCartPrices(parent, obj);
    },
    variationAction: function($this, parent, variations, obj){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let variation = $g($this).closest('.ba-add-to-cart-variation'),
            keys = [],
            key = '';
        variation.find('.active').removeClass('active');
        variation.find('.ba-variation-notice').remove();
        $this.classList.add('active');
        variations.find('.disabled').removeClass('disabled');
        variations.find('.active[data-value], .selected[data-value], input[type="radio"]').each(function(){
            if (this.checked) {
                keys.push(this.value);
            } else if (this.classList.contains('active') || this.classList.contains('selected')) {
                keys.push(this.dataset.value);
            }
        });
        if (keys.length) {
            key = keys.join('+');
            app.addToCart.updateVariationData(parent, obj, key);
        }
    },
    updateVariationData: function(parent, obj, key, setActive){
        let keys = key.split('+'),
            img = 'original';
        keys.forEach(function(value){
            if (obj.productData.images[value] && obj.productData.images[value].length) {
                img = value;
            }
        });
        app.addToCart.updateProductItems(obj.productData, img);
        if (setActive) {
            keys.forEach(function(ind){
                parent.find('[data-value="'+ind+'"]').each(function(){
                    this.classList.add('active');
                    if (this.localName == 'li') {
                        this.classList.add('selected');
                        let text = this.textContent.trim(),
                            select = this.closest('.ba-custom-select');
                        select.querySelector('input[type="text"]').value = text;
                        select.querySelector('input[type="hidden"]').value = ind;
                    }
                });
                parent.find('input[value="'+ind+'"]').each(function(){
                    this.classList.add('active');
                    this.checked = true;
                })
            });
        }
        if (obj.productData.variations[key]) {
            obj.product = obj.productData.variations[key];
            app.addToCart.clearSearch(obj);
            let stock = obj.product.stock,
                min = obj.productData.data.min ? obj.productData.data.min * 1 : 1,
                url = window.location.href,
                extra = parent.find('.ba-add-to-cart-extra-option'),
                flag = true,
                data = {
                    price: obj.product.price,
                    sale_price: obj.product.sale_price,
                    button: obj['button-label'],
                    stock: stock !== '' && stock * 1 < min ? app._('OUT_OF_STOCK') : stock,
                    sku: obj.product.sku
                };
            extra.each(function(){
                if (flag && this.dataset.required == 1 && (this.dataset.type == 'textarea' || this.dataset.type == 'textinput')) {
                    flag = this.querySelector('input, textarea').value.trim() != '';
                } else if (flag && this.dataset.required == 1 && !this.querySelector('input[type="radio"], input[type="checkbox"]')) {
                    flag = this.querySelector('.active[data-value], .selected[data-value], .ba-add-to-cart-attachment');
                } else if (flag && this.dataset.required == 1) {
                    let checked = false;
                    this.querySelectorAll('input').forEach(function(input){
                        if (!checked) {
                            checked = input.checked;
                        }
                    });
                    flag = checked;
                }
            });
            if (!flag) {
                data.button = app._('SELECT_AN_OPTION');
            }
            if (window.location.hash) {
                url = url.replace(window.location.hash, '');
            }
            url += window.location.search ? '&' : '?';
            url += obj.productData.variations[key].url;
            if (window.location.hash) {
                url += window.location.hash;
            }
            window.history.replaceState(null, null, url);
            app.addToCart.setCartValues(parent, data, obj);
            if (stock !== '' && stock * 1 < min) {
                parent.find('.ba-add-to-cart-button-wrapper').addClass('disabled').find('a').text(app._('OUT_OF_STOCK'));
            } else {
                parent.find('.ba-add-to-cart-button-wrapper').removeClass('disabled').find('a').text(data.button);
            }
        } else {
            obj.product = null;
        }
    },
    setCartValues: function(parent, data, obj){
        let min = obj.productData.data.min ? obj.productData.data.min : 1,
            hasFileQty = false,
            fileQty = 0;
        parent.find('.ba-add-to-cart-attached-files').each(function(){
            let files = this.querySelectorAll('.ba-add-to-cart-attachment');
            if (this.dataset.quantity == 1 && (this.closest('.ba-add-to-cart-extra-option').dataset.required == 1 || files.length > 0)) {
                hasFileQty = true;
                fileQty += files.length;
            }
        });
        if (hasFileQty) {
            min = fileQty;
        }
        parent.find('.ba-add-to-cart-quantity input').val(min);
        parent.find('.ba-add-to-cart-sku .ba-add-to-cart-row-value').text(data.sku);
        parent.find('.ba-add-to-cart-stock .ba-add-to-cart-row-value').text(data.stock);
        parent.find('.ba-add-to-cart-buttons-wrapper a').text(data.button);
        app.addToCart.setCartPrices(parent, obj);
    },
    getCartQuantity: function(parent){
        let input = parent[0].querySelector('.ba-add-to-cart-quantity input'),
            quantity = input ? input.value * 1 : (app.addToCart.isBooking() ? app.addToCart.booking.getCartMultiple(parent) : 1);

        return quantity;
    },
    setCartPrices: function(parent, obj){
        let qty = app.addToCart.getCartQuantity(parent),
            price = (+(obj.product ? obj.product.price : obj.productData.data.price) + this.extra_options.price) * qty,
            sale_price = obj.product ? obj.product.sale_price : obj.productData.data.sale_price,
            thousand = obj.productData.thousand,
            separator = obj.productData.separator,
            rate = obj.productData.rate,
            decimals = obj.productData.decimals;
        parent.find('.ba-add-to-cart-price .ba-add-to-cart-price-wrapper').each(function(){
            let div = this.closest('.ba-add-to-cart-price'),
                clone = this.cloneNode(true);
            price = app.renderPrice(price, thousand, separator, decimals, rate);
            div.innerHTML = '';
            if (sale_price != '') {
                let saleClone = this.cloneNode(true);
                sale_price = (sale_price * 1 + app.addToCart.extra_options.price) * qty;
                sale_price = app.renderPrice(sale_price, thousand, separator, decimals, rate);
                saleClone.classList.remove('ba-add-to-cart-price-wrapper');
                saleClone.classList.add('ba-add-to-cart-sale-price-wrapper');
                saleClone.querySelector('.ba-add-to-cart-price-value').textContent = sale_price;
                div.append(saleClone);
            }
            clone.querySelector('.ba-add-to-cart-price-value').textContent = price;
            div.append(clone);
        });
    },
    getSlideshowDefault: function(object, $this){
        if (!object.images) {
            let wrapper = $this.querySelector('ul.ba-slideshow'),
                key = 'original';
            object.images = {
                key: 'original',
                original: []
            };
            if (wrapper.dataset.original) {
                object.images.original = JSON.parse(wrapper.dataset.original);
                key = wrapper.dataset.variation;
                object.images.key = key;
                object.images[key] = [];
            }
            $this.querySelectorAll('li .ba-slideshow-img').forEach(function(img){
                object.images[key].push('url('+img.dataset.src+')');
            });
        }
    },
    initSlideshow: function(object, key, $this){
        app.addToCart.getSlideshowDefault(object, $this);
        if (object.images[key] != object.images[object.images.key]) {
            object.images.key = key;
            let style = html = '';
            object.images[key].forEach(function(image, i){
                style += '--thumbnails-dots-image-'+i+': '+image+';';
                html += '<li class="item'+(i == 0 ? ' active' : '');
                html += '"><div class="ba-slideshow-img" style="background-image: '+image+';"></div></li>';
            });
            $this.querySelector('ul.ba-slideshow .slideshow-content').innerHTML = html;
            $this.querySelectorAll('ul.ba-slideshow .ba-slideshow-dots').forEach((dots) => {
                dots.setAttribute('style', style);
            });
            app.initslideshow(object, $this.id);
        }
    },
    getGalleryDefault: function(object, $this){
        if (!object.images) {
            let wrapper = $this.querySelector('.instagram-wrapper'),
                key = 'original';
            object.images = {
                key: 'original',
                original: []
            };
            if (wrapper.dataset.original) {
                object.images.original = JSON.parse(wrapper.dataset.original);
                key = wrapper.dataset.variation;
                object.images.key = key;
                object.images[key] = [];
            }
            $this.querySelectorAll('.ba-instagram-image img').forEach(function(img){
                object.images[key].push(img.src ? img.src : img.dataset.gridboxLazyloadSrc);
            });
        }
    },
    initGallery:function(object, key, $this){
        app.addToCart.getGalleryDefault(object, $this);
        if (object.images[key] != object.images[object.images.key]) {
            object.images.key = key;
            let html = '';
            object.images[key].forEach(function(image, i){
                html += '<div class="ba-instagram-image" style="background-image: url('+image+');"><img alt="" src="'+
                image+'"><div class="ba-simple-gallery-image"></div></div>';
            });
            $this.querySelector('.instagram-wrapper').innerHTML = html;
            app.initSimpleGallery(object, $this.id);
            if ($this.querySelector('.instagram-wrapper').classList.contains('simple-gallery-masonry-layout')) {
                setGalleryMasonryHeight($this.id);
            }
        }
    },
    updateProductItems: function(product, key){
        let defs = {
            slideshow: ['getSlideshowDefault', 'initSlideshow'],
            gallery: ['getGalleryDefault', 'initGallery']
        }
        $g('.ba-item-product-slideshow, .ba-item-product-gallery').each(function(){
            let def = this.classList.contains('ba-item-product-slideshow') ? 'slideshow' : 'gallery';
                object = app.items[this.id];
            app.addToCart[defs[def][0]](object, this);
            if (key != 'original' && !object.images[key]) {
                object.images[key] = [];
                product.images[key].forEach(function(image){
                    image = !app.isExternal(image) ? (JUri+image) : image;
                    object.images[key].push(def == 'slideshow' ? 'url('+image+')' : image);
                });
            }
            app.addToCart[defs[def][1]](object, key, this);
        });
    }
}

if (app.modules.initAddToCart) {
    app.initAddToCart(app.modules.initAddToCart.data, app.modules.initAddToCart.selector);
}