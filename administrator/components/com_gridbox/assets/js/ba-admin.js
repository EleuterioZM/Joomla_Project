/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (getCookie('gridbox-sidebar') == 'visible') {
    document.body.classList.add('visible-sidebar');
    setTimeout(function(){
        document.body.dataset.sidebar = 'hidden';
    }, 500);
}


function renderGridSorting($)
{
    let gridDraggable = function(element, options){
            this.item = $(element);
            this.options = options;
            this.placeholder = this.handle = null;
            if (!dragGroups[options.group]) {
                dragGroups[options.group] = [];
            }
            dragGroups[options.group].push(element);
        },
        dragGroups = {};

    gridDraggable.prototype = {
        getElementObj:function(key, el, rect, value, value1){
            if (value == value1 && !this.elements[key]) {
                this.elements[key] = {
                    el: el,
                    rect: rect
                };
            }
        },
        prepareData: function(){
            let rect = this.placeholder.getBoundingClientRect(),
                place = $(this.placeholder),
                $this = this;
            this.elements = {};
            this.css = {
                width: rect.width,
                height: rect.height,
                left: rect.left,
                top: rect.top
            };
            place.prevAll().each(function(){
                rect = this.getBoundingClientRect();
                $this.getElementObj('top', this, rect, rect.left, $this.css.left);
                $this.getElementObj('left', this, rect, rect.top, $this.css.top);
                if ($this.elements.top && $this.elements.left) {
                    return false;
                }
            });
            place.nextAll().each(function(){
                rect = this.getBoundingClientRect();
                $this.getElementObj('bottom', this, rect, rect.left, $this.css.left);
                $this.getElementObj('right', this, rect, rect.top, $this.css.top);
                if ($this.elements.bottom && $this.elements.right) {
                    return false;
                }
            });
        },
        getParent: function(x, y){
            let parents = dragGroups[this.options.group],
                parent = rect = null;
            for (let ind in parents) {
                rect = parents[ind].getBoundingClientRect();
                if (rect.left < x && rect.right > x && rect.top < y && rect.bottom > y) {
                    parent = parents[ind];
                    break;
                }
            }

            return parent;
        },
        init: function(){
            let $this = this;
            this.item.on('click.gridDraggable', this.options.handle, function(event){
                event.preventDefault();
                event.stopPropagation();
            }).on('mousedown.gridDraggable', this.options.handle, function(event){
                if (event.button == 0) {
                    $this.placeholder = $(this).closest($this.item[0].children)[0];
                    $this.handle = $this.placeholder.cloneNode(true);
                    let rect = null,
                        helper = $($this.handle),
                        place = $($this.placeholder),
                        parent = $this.placeholder.parentNode,
                        delta = {};
                    $this.prepareData();
                    delta.x = $this.css.left - event.clientX;
                    delta.y = $this.css.top - event.clientY;
                    $(document).on('mousemove.gridDraggable', function(event){
                        if (!document.body.classList.contains('grid-draggable-started')) {
                            $this.handle.classList.add('draggable-grid-handle-item');
                            if ($this.options.group) {
                                $this.handle.classList.add($this.options.group);
                            }
                            document.body.append($this.handle);
                            $this.placeholder.classList.add('draggable-grid-placeholder-item');
                            document.body.classList.add('grid-draggable-started');
                        }
                        let el = $this.getParent(event.clientX, event.clientY);
                        if (el && parent != el) {
                            el.append($this.placeholder);
                            $this.options.parentChange(el, parent);
                            parent = el;
                            $this.prepareData();
                        }
                        let target = null,
                            top = event.clientY + delta.y,
                            left = event.clientX + delta.x,
                            bottom = top + $this.css.height,
                            elements = $this.elements,
                            right = left + $this.css.width;
                        if (elements.right && right > elements.right.rect.left + elements.right.rect.width / 2) {
                            target = elements.right.el;
                            place.insertAfter(target);
                        } else if (elements.bottom && bottom > elements.bottom.rect.top + elements.bottom.rect.height / 2) {
                            target = elements.bottom.el;
                            place.insertAfter(target);
                        } else if (elements.left && left < elements.left.rect.left + elements.left.rect.width / 2) {
                            target = elements.left.el;
                            place.insertBefore(target);
                        } else if (elements.top && top < elements.top.rect.top + elements.top.rect.height / 2) {
                            target = elements.top.el;
                            place.insertBefore(target);
                        }
                        if (target) {
                            $this.prepareData();
                        }
                        helper.css({
                            top : top,
                            left : left,
                        });
                        
                        return false;
                    }).off('mouseleave.gridDraggable').on('mouseleave.gridDraggable', function(){
                        $(document).trigger('mouseup.gridDraggable');
                    }).off('mouseup.gridDraggable').on('mouseup.gridDraggable', function(){
                        if (document.body.classList.contains('grid-draggable-started')) {
                            let animation = 'grid-draggable-return-animation'
                            if ($this.placeholder.closest('.gridbox-app-folder')) {
                                animation = 'grid-draggable-folder-return-animation';
                            }
                            $this.handle.classList.add(animation);
                            helper.css($this.css);
                            setTimeout(function(){
                                $this.placeholder.classList.remove('draggable-grid-placeholder-item');
                                $this.handle.remove();
                                $this.elements = {};
                                $this.options.change($this.placeholder);
                                $this.placeholder = $this.handle = null;
                            }, 300);
                            document.body.classList.remove('grid-draggable-started');
                        }
                        $(document).off('mousemove.gridDraggable mouseup.gridDraggable mouseleave.gridDraggable');
                    });
                }

                return false;
            });
        }
    }

    $.fn.gridDraggable = function(option) {
        return this.each(function (){
            var $this = $(this),
                data = $this.data('gridDraggable'),
                options = $.extend({}, $.fn.gridDraggable.defaults, typeof option == 'object' && option);
            if (data) {
                $this.removeData();
            }
            $this.data('gridDraggable', (data = new gridDraggable(this, options)));
            data.init();
        });
    }
    
    $.fn.gridDraggable.defaults = {
        selector : '> *',
        handle: '.draggable-helper',
        group: '',
        change: function(){},
        start: function(){},
        parentChange: function(){}
    }
    
    let gridSorting = function(element, options){
        this.item = $(element);
        this.options = options;
        this.placeholder = this.handle = null;
    }

    gridSorting.prototype = {
        getElementObj:function(key, el, rect, value, value1){
            if (value == value1 && !this.elements[key]) {
                this.elements[key] = {
                    el: el,
                    rect: rect
                };
            }
        },
        prepareData: function($this){
            let rect = this.placeholder.getBoundingClientRect(),
                place = $(this.placeholder);
            this.elements = {};
            this.css = {
                width: rect.width,
                height: rect.height,
                left: rect.left,
                top: rect.top
            };
            place.prevAll().each(function(){
                rect = this.getBoundingClientRect();
                $this.getElementObj('top', this, rect, rect.left, $this.css.left);
                $this.getElementObj('left', this, rect, rect.top, $this.css.top);
                if ($this.elements.top && $this.elements.left) {
                    return false;
                }
            });
            place.nextAll().each(function(){
                rect = this.getBoundingClientRect();
                $this.getElementObj('bottom', this, rect, rect.left, $this.css.left);
                $this.getElementObj('right', this, rect, rect.top, $this.css.top);
                if ($this.elements.bottom && $this.elements.right) {
                    return false;
                }
            });
        },
        init: function(){
            let $this = this;
            this.item.on('mousedown.gridSorting', this.options.handle, function(event){
                if (event.button == 0) {
                    $this.placeholder = $(this).closest($this.item[0].children)[0];
                    $this.handle = $this.placeholder.cloneNode(true);
                    let rect = null,
                        helper = $($this.handle),
                        place = $($this.placeholder),
                        delta = {};
                    $this.prepareData($this);
                    delta.x = $this.css.left - event.clientX;
                    delta.y = $this.css.top - event.clientY;
                    $(document).off('mousemove.gridSorting').on('mousemove.gridSorting', function(event){
                        if (!document.body.classList.contains('grid-sorting-started')) {
                            $this.handle.classList.add('sorting-grid-handle-item');
                            if ($this.options.group) {
                                $this.handle.classList.add($this.options.group);
                            }
                            if ($this.options.hasDrop) {
                                $g($this.options.hasDrop).addClass('grid-sorting-droppable')
                                    .on('mouseenter', function(){
                                        $this.droppad = true;
                                    })
                                    .on('mouseleave', function(){
                                        $this.droppad = false;
                                    })
                                    .append('<span class="grid-sorting-droppable-text">'+$this.options.dropText+'</span>');
                            }
                            document.body.append($this.handle);
                            $this.placeholder.classList.add('sorting-grid-placeholder-item');
                            document.body.classList.add('grid-sorting-started');
                        }
                        let target = null,
                            top = event.clientY + delta.y,
                            left = event.clientX + delta.x,
                            bottom = top + $this.css.height,
                            elements = $this.elements,
                            right = left + $this.css.width;
                        if (elements.right && right > elements.right.rect.left + elements.right.rect.width / 2) {
                            target = elements.right.el;
                            place.insertAfter(target);
                        } else if (elements.bottom && bottom > elements.bottom.rect.top + elements.bottom.rect.height / 2) {
                            target = elements.bottom.el;
                            place.insertAfter(target);
                        } else if (elements.left && left < elements.left.rect.left + elements.left.rect.width / 2) {
                            target = elements.left.el;
                            place.insertBefore(target);
                        } else if (elements.top && top < elements.top.rect.top + elements.top.rect.height / 2) {
                            target = elements.top.el;
                            place.insertBefore(target);
                        }
                        if (target) {
                            $this.prepareData($this);
                        }
                        helper.css({
                            top : top,
                            left : left,
                        });
                        
                        return false;
                    }).off('mouseleave.gridSorting').on('mouseleave.gridSorting', function(){
                        $(document).trigger('mouseup.gridSorting');
                    }).off('mouseup.gridSorting').on('mouseup.gridSorting', function(){
                        $(document).off('mousemove.gridSorting mouseup.gridSorting mouseleave.gridSorting');
                        if (document.body.classList.contains('grid-sorting-started')) {
                            if ($this.options.hasDrop) {
                                $g($this.options.hasDrop).removeClass('grid-sorting-droppable')
                                    .off('mouseenter mouseleave')
                                    .find('.grid-sorting-droppable-text').addClass('droppable-text-out')
                            }
                            if ($this.droppad) {
                                $this.options.drop($this.placeholder);
                                $this.handle.classList.add('grid-sorting-drop-animation');
                            } else {
                                $this.handle.classList.add('grid-sorting-return-animation');
                                helper.css($this.css);
                            }
                            setTimeout(function(){
                                $this.placeholder.classList.remove('sorting-grid-placeholder-item');
                                $this.handle.remove();
                                $this.elements = {};
                                $this.placeholder = $this.handle = null;
                                if (!$this.droppad) {
                                    $this.options.change($this.item);
                                }
                                if ($this.options.hasDrop) {
                                    $g($this.options.hasDrop).find('.grid-sorting-droppable-text').remove();
                                }
                                $this.droppad = false;
                            }, 300);
                            document.body.classList.remove('grid-sorting-started');
                        }
                    });
                    if ($this.options.group == 'group-apps-list') {
                        return false;
                    }
                }
            });
        }
    }

    $.fn.gridSorting = function(option) {
        return this.each(function (){
            var $this = $(this),
                data = $this.data('gridSorting'),
                options = $.extend({}, $.fn.gridSorting.defaults, typeof option == 'object' && option);
            if (data) {
                $this.removeData();
            }
            $this.data('gridSorting', (data = new gridSorting(this, options)));
            data.init();
        });
    }
    
    $.fn.gridSorting.defaults = {
        selector : '> *',
        handle: '.grid-sorting-handle',
        group: '',
        hasDrop: null,
        drop : function(){},
        change : function(){},
        start : function(){}
    }
}

var app = {
        currentOrder: null,
        cart: {},
        objects: {
            productoptions: {
                title: 'Title',
                image: '',
                color: '#1da6f4',
                key: 0
            },
            statuses: {
                title: "Status",
                color: "#1da6f4",
                key: 0
            },
            taxes: {
                type: "tax",
                title: "Tax",
                rate: ''
            }
        },
        imageTypes: ['jpg', 'png', 'gif', 'svg', 'jpeg', 'ico', 'webp'],
        getExt: function(name){
            let array = name.split('.');
            
            return array[array.length - 1].toLowerCase();
        },
        isImage: function(name){
            return this.imageTypes.indexOf(this.getExt(name)) != -1;
        },
        modules:{},
        cke:{},
        _: function(key){
            if (gridboxLanguage && gridboxLanguage[key]) {
                return gridboxLanguage[key];
            } else {
                return key;
            }
        },
        showLoading: function(text){
            let str = app._(text)+'<img src="'+JUri;
            str += 'administrator/components/com_gridbox/assets/images/reload.svg"></img>';
            notification[0].className = 'notification-in';
            notification.find('p').html(str);
        },
        isExternal: function(link){
            return link.indexOf('https://') != -1 || link.indexOf('http://') != -1;
        },
        createChart: function(array){
            if (!app.chart) {
                app.chart = document.querySelector('.ba-statistics-chart');
            }
            app.chart.innerHTML = '';
            app.chart.classList.remove('ba-chart-loaded');
            if (array.length == 1) {
                app.chart.classList.add('ba-chart-single-point');
            } else {
                app.chart.classList.remove('ba-chart-single-point');
            }
            let chart = new liteChart(),
                labels = [],
                values = [];
            array.forEach(function(obj){
                labels.push(obj.label)
                values.push(obj.value);
            })
            chart.setLabels(labels);
            chart.addLegend({"values": values});
            chart.inject(app.chart);
            chart.draw();
        },
        statisticFilter: function(){
            makeFetchRequest('index.php?option=com_gridbox&task=orders.getStatistic', {
                type: app.statistic.type,
                date: app.statistic.value
            }).then(function(json){
                app.createChart(json.chart);
                $g('.ba-store-statistic-count-wrapper').each(function(){
                    this.querySelector('.ba-store-statistic-count').textContent = json.counts[this.dataset.type];
                });
                $g('.ba-store-statistic-total-price .ba-store-statistic-price').text(app.renderPrice(json.total));
                let parent = $g('.ba-store-statistic-products').empty();
                json.products.forEach(function(product) {
                    let div = document.querySelector('.ba-store-statistic-product-template').content.cloneNode(true);
                    if (product.image) {
                        div.querySelector('.ba-store-statistic-product-image')
                            .style.backgroundImage = 'url('+product.image.replace(/\s/g, '%20')+')';
                    } else {
                        div.querySelector('.ba-store-statistic-product-image').remove();
                    }
                    if (product.info) {
                        div.querySelector('.ba-store-statistic-product-info').innerHTML = product.info;
                    } else {
                        div.querySelector('.ba-store-statistic-product-info').remove();
                    }
                    if (product.link) {
                        div.querySelector('a').href = product.link;
                    } else {
                        div.querySelector('a').remove();
                    }
                    div.querySelector('.ba-store-statistic-product-title').textContent = product.title;
                    div.querySelector('.ba-store-statistic-price').textContent = app.renderPrice(product.price);
                    div.querySelector('.ba-store-statistic-product-sales-count').textContent = product.quantity;
                    parent.append(div);
                });
            })
        },
        prepareEmptyCart: function(modal){
            app.cart = {
                modal: modal,
                products:{},
                promo: null,
                shipping: null,
                subtotal: 0,
                tax: 0,
                discount: 0,
                country: '',
                region: '',
                total: 0
            };
            modal.find('.ba-options-group-toolbar label').not('.add-order-product').addClass('disabled');
        },
        getProductExtraOption: function(key, obj, quantity, symbol, position){
            let price = obj.price != '' ? app.renderPrice(obj.price * quantity, symbol, position) : '',
                str = '<div class="ba-product-extra-option" data-key="'+key+'">';
            str += '<div class="ba-product-delete-extra-option"><i class="zmdi zmdi-delete"></i></div>';
            str += '<div class="ba-product-extra-option-image"></div>';
            str += '<div class="ba-cart-product-extra-option-values">';
            str += '<span class="ba-cart-product-extra-option-value">'+obj.value+'</span>';
            str += '<span class="ba-cart-product-extra-option-price" data-price="'+obj.price+'">'+price+'</span></div>';
            str += '</div>';

            return str;
        },
        getProductExtraRow: function(ind, obj, quantity, symbol, position){
            let str = '<div class="ba-product-extra-option-row" data-ind="'+ind+'">';
            str += '<div class="ba-product-extra-option">';
            str += '<div class="ba-product-delete-extra-option"></div>';
            str += '<div class="ba-product-extra-option-image"></div>';
            str += '<div class="ba-cart-product-extra-option-title"><span>'+obj.title+'</span>';
            if (obj.attachments) {
                str += '<i class="zmdi zmdi-download download-attached-files"></i>';
            }
            str += '</div></div>';
            for (let key in obj.values) {
                str += app.getProductExtraOption(key, obj.values[key], quantity, symbol, position);
            }
            if (obj.attachments) {
                str += '<div class="ba-product-attachments">';
                obj.attachments.forEach((file) => {
                    let src = JUri+'components/com_gridbox/assets/uploads/attachments/'+file.filename;
                    str += '<div class="ba-product-attachment" data-id="'+file.id+'">';
                    str += '<i class="zmdi zmdi-delete remove-product-attachment"></i>';
                    if (app.isImage(file.name)) {
                        str += '<span class="attachment-image" data-img="'+src+'" ';
                        str += 'style="background-image: url('+src+')"></span>';
                    } else {
                        str += '<i class="zmdi zmdi-attachment-alt"></i>';
                    }
                    str += '<span class="attachment-title">'+file.name+'</span>';
                    str += '<a class="zmdi zmdi-download" download="'+file.name+'" href="'+src+'"></a>';
                    str += '</div>';
                });
                str += '</div>';
            }
            str += '</div>';

            return str;
        },
        getProductSortingHTML: function(obj, quantity, symbol, position){
            let key = obj.renew_id != '0' ? obj.db_id+'_'+obj.renew_id : obj.id,
                variation = obj.variation ? ' data-variation="'+obj.variation+'"' : '',
                str = '<div class="sorting-item" data-id="'+key+'"'+variation,
                extraPrice = obj.extra_options.price ? obj.extra_options.price * quantity : 0,
                price = app.renderPrice(obj.price * quantity + extraPrice, symbol, position),
                fileQuantity = '',
                extraHTML = '';
            if (obj.extra_options && obj.extra_options.items) {
                for (let ind in obj.extra_options.items) {
                    if (obj.extra_options.items[ind].quantity) {
                        fileQuantity = ' file-quantity-enabled';
                    }
                    extraHTML += app.getProductExtraRow(ind, obj.extra_options.items[ind], quantity, symbol, position);
                }
            }
            str += '><div class="ba-order-product-wrapper"><div class="sorting-checkbox">';
            str += '<label class="ba-checkbox ba-hide-checkbox"><input type="checkbox" name="product" value="';
            str += key+'"'+variation+'><span></span></label></div>';
            if (obj.image) {
                str += '<div class="sorting-image"><img src="'+(app.isExternal(obj.image) ? '' : JUri)+obj.image+'"></div>';
            }
            str += '<div class="sorting-title"><span class="product-title">'+obj.title+'</span>';
            str += (obj.info ? '<span class="product-info">'+obj.info+'</span>' : '');
            if (obj.product_type == 'booking' && obj.booking) {
                str += '<span class="product-booking-info">'+
                    (app._('DATE')+': '+obj.booking.formated.start_date+(obj.booking.end_date ? ' - '+obj.booking.formated.end_date : ''))+'</span>';
                str += obj.booking.start_time ? '<span class="product-booking-info">'+app._('TIME')+': '+obj.booking.start_time+'</span>' : '';
                str += obj.booking.guests ? '<span class="product-booking-info">'+app._('GUESTS')+': '+obj.booking.guests+'</span>' : '';
            }
            str += '</div>';
            if (obj.product_type != 'digital' && obj.product_type != 'subscription' && obj.product_type != 'booking') {
                str += '<div class="sorting-quantity'+fileQuantity+'"><input type="number" value="'+quantity+'" data-id="'+obj.id+'"></div>';
            }
            str += '<div class="ba-cart-product-price-cell">';
            if (obj.sale_price !== '') {
                str += '<span class="ba-cart-sale-price-wrapper"><span class="ba-cart-price-value">'+price+'</span></span>';
            }
            str += '<span class="ba-cart-price-wrapper "><span class="ba-cart-price-value">';
            if (obj.sale_price !== '') {
                price = app.renderPrice(obj.sale_price * quantity + extraPrice, symbol, position);
            }
            str += price+'</span></span></div></div>';
            str += '<div class="ba-product-extra-options">';
            str += extraHTML;
            str += '</div>';
            str += '</div>';

            return str;
        },
        checkPromoSales: function(promo, product){
            return promo.disable_sales == 0 || product.sale_price === '';
        },
        checkPromoCode: function(promo, product){
            let valid = false;
            if (promo.applies_to == '*') {
                valid = this.checkPromoSales(promo, product);
            } else if (promo.applies_to == 'product') {
                for (let i in promo.map) {
                    valid = promo.map[i].id == product.id && this.checkPromoSales(promo, product);
                    if (valid) {
                        break;
                    }
                }
            } else {
                for (let i in promo.map) {
                    valid = product.categories.indexOf(promo.map[i].id) != -1 && this.checkPromoSales(promo, product);
                    if (valid) {
                        break;
                    }
                }
            }

            return valid;
        },
        checkProductTaxMap: function(product, categories){
            let valid = false;
            for (let i = 0; i < categories.length; i++) {
                valid = product.categories.indexOf(categories[i]) != -1;
                if (valid) {
                    break;
                }
            }

            return valid;
        },
        getTaxRegion: function(regions){
            let result = null;
            for (let i = 0; i < regions.length; i++) {
                if (regions[i].state_id == app.cart.region) {
                    result = regions[i];
                    break;
                }
            }

            return result;
        },
        calculateProductTax: function(product, price, country, region, category){
            let obj = null,
                array = category ? app.taxRates.categories : app.taxRates.empty;
            for (let i = 0; i < array.length; i++) {
                let tax = array[i],
                    count = country ? tax.country_id == app.cart.country : true,
                    cat = category ? app.checkProductTaxMap(product, tax.categories) : true,
                    reg = region ? app.getTaxRegion(tax.regions) : true,
                    rate = 0;
                if (count && cat && reg) {
                    rate = reg.rate ? reg.rate : tax.rate;
                    obj = {
                        key: tax.key,
                        title: tax.title,
                        rate: rate,
                        amount: app.store.tax.mode == 'excl' ? price * (rate / 100) : price - price / (rate / 100 + 1)
                    };
                    break;
                }
            }
            if (!obj && country && region && category) {
                obj = app.calculateProductTax(product, price, true, false, true);
            } else if (!obj && country && !region && category) {
                obj = app.calculateProductTax(product, price, true, true, false);
            } else if (!obj && country && region && !category) {
                obj = app.calculateProductTax(product, price, true, false, false);
            } else if (!obj && country && !region && !category) {
                obj = app.calculateProductTax(product, price, false, false, true);
            } else if (!obj && !country && !region && category) {
                obj = app.calculateProductTax(product, price, false, false, false);
            }

            return obj;
        },
        getStoreShippingTax: function(country, region){
            let obj = null;
            for (let i = 0; i < app.store.tax.rates.length; i++) {
                let rate = app.store.tax.rates[i],
                    count = country ? rate.country_id == app.cart.country : true,
                    reg = region ? app.getTaxRegion(rate.regions) : true;
                if (rate.shipping && count && reg) {
                    obj = {};
                    obj.key = i;
                    obj.title = rate.title;
                    obj.rate = rate.rate;
                    obj.amount = rate.rate / 100;
                    break;
                }
            }

            if (!obj && country && region) {
                obj = app.getStoreShippingTax(true, false);
            } else if (!obj && country && !region) {
                obj = app.getStoreShippingTax(false, false);
            }

            return obj;
        },
        calculateOrder: function(){
            if (!app.taxRates) {
                app.taxRates = {
                    categories: [],
                    empty: []
                }
                for (let i = 0; i < app.store.tax.rates.length; i++) {
                    let rate = app.store.tax.rates[i];
                    rate.key = i;
                    if (rate.categories.length) {
                        app.taxRates.categories.push(rate);
                    } else {
                        app.taxRates.empty.push(rate);
                    }
                }
            }
            app.cart.total = app.cart.subtotal = app.cart.tax = app.cart.discount = 0;
            app.cart.validPromo = false;
            app.cart.country = app.cart.region = '';
            app.cart.taxes = {};
            app.cart.taxes.count = 0;
            app.cart.quantity = 0;
            app.cart.modal.find('.ba-options-group-element[data-type="country"] select').each(function(){
                app.cart[this.dataset.type] = this.value;
            });
            let mode = app.store.tax.mode,
                promoProducts = 0,
                hasShipping = false,
                products = app.cart.products;
            for (let ind in products) {
                let product = products[ind];
                product.promo = app.cart.promo && this.checkPromoCode(app.cart.promo, product);
                if (product.promo) {
                    promoProducts++;
                }
            }
            for (let ind in products) {
                let product = products[ind],
                    price = (product.sale_price !== '' ? product.sale_price : product.price) * product.quantity;
                app.cart.quantity += product.quantity;
                if (product.extra_options.price) {
                    price += product.extra_options.price * product.quantity;
                }
                if (product.product_type != 'digital' && product.product_type != 'subscription' && product.product_type != 'booking') {
                    hasShipping = true;
                }
                app.cart.subtotal += price;
                product.tax = app.calculateProductTax(product, price, true, true, true);
                if (product.promo) {
                    app.cart.validPromo = true;
                    let discount = app.cart.promo.discount;
                    discount = app.cart.promo.unit == '%' ? price * (discount / 100) : discount / promoProducts;
                    price -= discount;
                    app.cart.discount += discount;
                }
                product.net_price = price;
                if (product.tax) {
                    let amount = product.tax.amount,
                        rate = product.tax.rate;
                    if (product.promo) {
                        amount = mode == 'excl' ? price * (rate / 100) : price - price / (rate / 100 + 1);
                        product.tax.amount = amount;
                    }
                    app.cart.tax += amount;
                    product.net_price = mode == 'excl' ? price : price - amount;
                    if (!app.cart.taxes[product.tax.key]) {
                        app.cart.taxes[product.tax.key] = {};
                        app.cart.taxes[product.tax.key].title = product.tax.title;
                        app.cart.taxes[product.tax.key].rate = rate;
                        app.cart.taxes[product.tax.key].amount = amount;
                        app.cart.taxes[product.tax.key].net = product.net_price;
                        app.cart.taxes.count++;
                    } else {
                        app.cart.taxes[product.tax.key].amount += amount;
                        app.cart.taxes[product.tax.key].net += product.net_price;
                    }
                }
                app.cart.total += price;
            }
            for (let i = 0; i < app.store.sales.length; i++) {
                let sale = app.store.sales[i];
                if (app.cart.total < sale.cart_discount) {
                    continue;
                }
                let discount = sale.unit == '%' ? (app.cart.total - app.cart.discount) * (sale.discount / 100) : sale.discount;
                app.cart.discount += discount;
                app.cart.total -= discount;
                break;
            }
            let price = app.renderPrice(app.cart.subtotal);
            app.cart.modal.find('.order-subtotal-element .ba-cart-price-value').text(price);
            app.cart.modal.find('.order-shipping-method').each(function(){
                if (!this.classList.contains('empty-shipping-methods') && hasShipping) {
                    this.classList.remove('ba-hide-element');
                } else {
                    this.classList.add('ba-hide-element');
                }
            })
            if (app.cart.modal.find('.order-promo-code').hasClass('ba-hide-element')
                && app.cart.modal.find('.order-shipping-method').hasClass('ba-hide-element')) {
                app.cart.modal.find('.order-methods-wrapper').addClass('ba-hide-element');
            } else {
                app.cart.modal.find('.order-methods-wrapper').removeClass('ba-hide-element');
            }
            if (mode == 'incl') {
                let title = app._('INCLUDING_TAXES')+' ';
                price = app.renderPrice(app.cart.tax);
                if (app.cart.taxes.count == 1) {
                    for (let ind in app.cart.taxes) {
                        if (ind == 'count') {
                            continue;
                        }
                        title = app._('INCLUDES')+' '+app.cart.taxes[ind].rate+'%'+' '+app.cart.taxes[ind].title;
                    }
                }
                title += ' '+price;
                app.cart.modal.find('.order-tax-element label').text(title);
            } else if (mode == 'excl' && app.cart.taxes.count != 0) {
                app.cart.modal.find('.order-tax-element').remove();
                let html = '';
                for (let ind in app.cart.taxes) {
                    if (ind == 'count') {
                        continue;
                    }
                    app.cart.total += app.cart.taxes[ind].amount;
                    price = app.renderPrice(app.cart.taxes[ind].amount);
                    html += '<div class="ba-options-group-element order-tax-element" data-mode="excl">';
                    html += '<label class="ba-options-group-label">'+app.cart.taxes[ind].title;
                    html += '</label><span class="ba-cart-price-wrapper "><span class="ba-cart-price-value">';
                    html += price+'</span></span></div>';
                }
                app.cart.modal.find('.order-total-element').before(html);
            }
            price = app.renderPrice(app.cart.discount);
            app.cart.modal.find('.order-discount-element .ba-cart-price-value').text(price);
            if (app.cart.shipping) {
                let params = JSON.parse(app.cart.shipping.options),
                    shipping = app.getShippingPrice(params) * 1;
                app.cart.shipping.tax = null;
                if (params.type == 'free' || params.type == 'pickup') {
                    price = app._('FREE');
                } else {
                    app.cart.total += shipping;
                    price = app.renderPrice(shipping);
                }
                app.cart.shipping.price = shipping;
                app.cart.modal.find('.order-shipping-element .ba-cart-price-value').text(price);
                app.cart.modal.find('.order-shipping-tax-element').each(function(){
                    let shippingTax = app.getStoreShippingTax(true, true),
                        amount = 0;
                    if (shippingTax) {
                        amount = shippingTax.amount;
                        amount = mode == 'excl' ? shipping * amount : shipping - shipping / (amount + 1);
                        shippingTax.amount = amount;
                        app.cart.total += mode == 'excl' ? amount : 0;
                    }
                    app.cart.shipping.tax = shippingTax;
                    price = app.renderPrice(amount);
                    if (mode == 'excl') {
                        this.querySelector('.ba-cart-price-value').textContent = price;
                    } else {
                        let text = shippingTax ? app._('INCLUDES')+' '+shippingTax.title : app._('INCLUDING_TAXES');
                        this.querySelector('.ba-options-group-label').textContent = text+' '+price;
                    }
                });
            } else {
                price = app.renderPrice(0);
                app.cart.modal.find('.order-shipping-element, .order-shipping-tax-element').find('.ba-cart-price-value').text(price);
            }
            price = app.renderPrice(app.cart.total);
            app.cart.modal.find('.order-total-element .ba-cart-price-value').text(price);
        },
        getShippingPrice: function(params){
            let price = 0,
                object = params[params.type];
            if (params.type == 'flat') {
                price = object.price;
            } else if (params.type == 'weight-unit') {
                let weight = 0;
                for (let ind in app.cart.products) {
                    let product = app.cart.products[ind];
                    if (product.weight) {
                        weight += product.weight * product.quantity;
                    } else if (product.dimensions.weight) {
                        weight += product.dimensions.weight * product.quantity;
                    }
                    if (product.extra_options.items) {
                        for (let i in product.extra_options.items) {
                            let values = product.extra_options.items[i].values;
                            if (values) {
                                for (let j in values) {
                                    if (values[j].weight) {
                                        weight += values[j].weight * product.quantity;
                                    }
                                }
                            }
                        }
                    }
                }
                price = weight * object.price;
            } else if (params.type == 'product') {
                price = app.cart.quantity * params.product.price;
            } else if (params.type == 'prices' || params.type == 'weight') {
                let range = [],
                    obj = null,
                    unlimited = null;
                for (let ind in object.range) {
                    let value = object.range[ind];
                    if (value.rate === '') {
                        unlimited = value;
                    } else {
                        value.rate *= 1;
                        range.push(value);
                    }
                }
                range.sort(function(a, b){
                    if (a.rate == b.rate) {
                        return 0;
                    }

                    return (a.rate < b.rate) ? -1 : 1;
                });
                if (params.type == 'weight') {
                    netValue = 0;
                    for (let ind in app.cart.products) {
                        let product = app.cart.products[ind];
                        if (product.weight) {
                            netValue += product.weight * product.quantity;
                        } else if (product.dimensions.weight) {
                            netValue += product.dimensions.weight * product.quantity;
                        }
                        if (product.extra_options.items) {
                            for (let i in product.extra_options.items) {
                                let values = product.extra_options.items[i].values;
                                if (values) {
                                    for (let j in values) {
                                        if (values[j].weight) {
                                            netValue += values[j].weight * product.quantity;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    netValue = app.cart.total;
                }
                range.forEach(function(value){
                    if (netValue <= value.rate && obj === null) {
                        obj = value;
                    }
                });
                if (obj === null && unlimited) {
                    obj = unlimited;
                }
                if (obj) {
                    price = obj.price;
                }
            } else if (params.type == 'category') {
                for (let ind in app.cart.products) {
                    let product = app.cart.products[ind],
                        obj = null;
                    for (let ind in object.range) {
                        let value = object.range[ind];
                        for (let id in value.rate) {
                            if (product.categories.indexOf(value.rate[id]) != -1) {
                                obj = value.price;
                                break;
                            }
                        }
                        if (obj !== null) {
                            break;
                        }
                    }
                    if (obj !== null) {
                        price += obj * product.quantity;
                        continue;
                    }
                }
            }
            if (object && object.enabled && app.cart.total > object.free * 1) {
                price = 0;
            }

            return price;
        },
        decimalAdjust: function(type, value, exp){
            if (typeof exp === 'undefined') {
                exp = app.store.currency.decimals * -1;
            }
            if (typeof exp === 'undefined' || +exp === 0) {
                return Math[type](value);
            }
            value = +value;
            exp = +exp;
            if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
                return NaN;
            }
            value = value.toString().split('e');
            value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
            value = value.toString().split('e');

            return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
        },
        strrev: function(string){
            var ret = '', i = 0;
            for (i = string.length - 1; i >= 0; i--) {
                ret += string[i];
            }

            return ret;
        },
        renderPrice: function(value, symbol, position){
            value = String(app.decimalAdjust('round', value));
            let thousand = app.store.currency.thousand,
                separator = app.store.currency.separator,
                decimals = app.store.currency.decimals,
                priceArray = value.replace('-', '').trim().split('.'),
                priceThousand = priceArray[0],
                priceDecimal = priceArray[1] ? priceArray[1] : '',
                price = '';
            if (!symbol) {
                symbol = app.store.currency.symbol;
                position = app.store.currency.position;
            }
            if (priceThousand.length > 3 && thousand != '') {
                for (let i = 0; i < priceThousand.length; i++) {
                    if (i % 3 == 0 && i != 0) {
                        price += thousand;
                    }
                    price += priceThousand[priceThousand.length - 1 - i];
                }
                price = this.strrev(price);
            } else {
                price += priceThousand;
            }
            if (decimals != 0) {
                price += separator;
                for (let i = 0; i < decimals; i++) {
                    price += priceDecimal[i] ? priceDecimal[i] : '0';
                }
            }
            if (position == '') {
                price = symbol+' '+price;
            } else {
                price = price+' '+symbol;
            }

            return price;
        },
        setSubgroupChilds: function(div){
            let count = div.querySelectorAll('.ba-group-element:not([disabled]), .ba-options-group-element:not([disabled])').length;
            div.style.setProperty('--subgroup-childs', count);
        },
        toggleAlertTooltip: function(alert, $this, parent, key){
            if (alert && !$this.alertTooltip && !$this.closest('.hidden-condition-field')) {
                $this.alertTooltip = document.createElement('span');
                $this.alertTooltip.className = 'ba-alert-tooltip';
                $this.alertTooltip.textContent = app._(key);
                parent.classList.add('ba-alert');
                parent.appendChild($this.alertTooltip);
            } else if (alert && $this.alertTooltip) {
                $this.alertTooltip.textContent = app._(key);
            } else if (!alert && $this.alertTooltip) {
                this.removeAlertTooltip($this);
            }
        },
        removeAlertTooltip: function($this){
            if (!$this.alertTooltip && $this.closest('.ba-alert')) {
                $this = $this.closest('.ba-alert');
            }
            if ($this.alertTooltip) {
                $this.alertTooltip.remove();
                $this.alertTooltip = null;
                $this.closest('.ba-alert').classList.remove('ba-alert');
            }
        },
        loadMinicolors: function(){
            let script = document.createElement('script'),
                link = document.createElement('link');
            script.type = 'text/javascript';
            script.src = JUri+'components/com_gridbox/libraries/minicolors/js/minicolors.js';
            script.onload = function(){
                setTimeout(app.setMinicolors, 1000)
            }
            document.head.append(script);
            link.href = JUri+'components/com_gridbox/libraries/minicolors/css/minicolors.css';
            link.rel = 'stylesheet';
            link.type = 'text/css';
            document.head.append(link);
        },
        setMinicolors: function(){
            $g('body').on('click', 'input[data-type="color"], .edit-booking-calendar-services-color', function(){
                fontBtn = this;
                app.setMinicolorsColor(this.dataset.rgba);
                if (this.classList.contains('edit-booking-calendar-services-color')) {
                    $g('.editing-service-color').removeClass('editing-service-color');
                    this.closest('li').classList.add('editing-service-color');
                }
                let rect = this.getBoundingClientRect();
                $g('#color-variables-dialog').css({
                    left : rect.left - 285,
                    top : rect.bottom - ((rect.bottom - rect.top) / 2) - 174
                }).removeClass('ba-right-position ba-bottom-position ba-top-position').modal();
            }).on('click', '.minicolors-swatch.minicolors-trigger', function(){
                $g(this).prev().trigger('click');
            }).on('input', 'input[data-type="color"]', app.inputColor);
            $g('.edit-booking-calendar-services-color').on('minicolorsInput', function(){
                this.closest('.booking-calendar-services-color-wrapper').style.setProperty('--badge-color', this.dataset.rgba);
                let id = this.closest('li').dataset.id;
                document.querySelector('.booking-calendar-content').style.setProperty('--service-color-'+id, this.dataset.rgba);
                clearTimeout(this.delay)
                this.delay = setTimeout(function(){
                    app.fetch('index.php?option=com_gridbox&task=bookingcalendar.setColor', {
                        id: id,
                        color: this.dataset.rgba
                    })
                }.bind(this), 500)
            });
            $g('.variables-color-picker').minicolors({
                opacity: true,
                theme: 'bootstrap',
                change: function(hex, opacity) {
                    let rgba = $g(this).minicolors('rgbaString');
                    fontBtn.value = hex;
                    $g('.variables-color-picker').closest('#color-picker-cell')
                        .find('.minicolors-opacity').val(opacity * 1);
                    fontBtn.dataset.rgba = rgba;
                    $g(fontBtn).trigger('minicolorsInput').next().find('.minicolors-swatch-color')
                        .css('background-color', rgba).closest('.minicolors').next()
                        .find('.minicolors-opacity').val(opacity * 1).removeAttr('readonly');
                }
            });
            $g('#color-variables-dialog').on('hide', function(){
                $g('.editing-service-color').removeClass('editing-service-color');
                setTimeout(function(){
                    this.style.setProperty('--color-variables-arrow-right', '');
                }.bind(this), 300);
            });
            $g('#color-variables-dialog .minicolors-opacity').on('input', function(){
                let obj = {
                    color: $g('.variables-color-picker').val(),
                    opacity: this.value * 1,
                    update: false
                }
                $g('.variables-color-picker').minicolors('value', obj);
                fontBtn.dataset.rgba = $g('.variables-color-picker').minicolors('rgbaString');
                $g(fontBtn).trigger('minicolorsInput');
                if (fontBtn.localName == 'input') {
                    $g(fontBtn).next().find('.minicolors-swatch-color').css('background-color', fontBtn.dataset.rgba)
                        .closest('.minicolors').next().find('.minicolors-opacity').val(this.value);
                }
            });
            $g('.minicolors-opacity[data-callback]').on('input', function(){
                let input = $g(this).parent().prev().find('.minicolors-input')[0],
                    opacity = this.value * 1
                    value = input.dataset.rgba;
                if (this.value) {
                    let parts = value.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
                        rgba = 'rgba(';
                    if (parts) {
                        for (let i = 1; i < 4; i++) {
                            rgba += parts[i]+', ';
                        }
                    } else {
                        parts = value.match(/[^#]\w/g);
                        for (let i = 0; i < 3; i++) {
                            rgba += parseInt(parts[i], 16);
                            rgba += ', ';
                        }
                    }
                    rgba += this.value+')';
                    input.dataset.rgba = rgba;
                    $g(input).next().find('.minicolors-swatch-color').css('background-color', rgba);
                    $g(input).trigger('minicolorsInput');
                }
            });
        },
        setMinicolorsColor: function(value){
            var rgba = value ? value : 'rgba(255,255,255,0)',
                color = app.rgba2hex(rgba),
                obj = {
                    color : color[0],
                    opacity : color[1],
                    update: false
                }
            $g('.variables-color-picker').minicolors('value', obj).closest('#color-picker-cell')
                .find('.minicolors-opacity').val(color[1]);
            $g('#color-variables-dialog .active').removeClass('active');
            $g('#color-picker-cell, #color-variables-dialog .nav-tabs li:first-child').addClass('active');
        },
        inputColor: function(){
            var value = this.value.trim().toLowerCase(),
                parts = value.match(/[^#]\w/g),
                opacity = 1;
            if (parts && parts.length == 3) {
                var rgba = 'rgba(';
                for (var i = 0; i < 3; i++) {
                    rgba += parseInt(parts[i], 16);
                    rgba += ', ';
                }
                if (!this.dataset.rgba) {
                    rgba += '1)';
                } else {
                    parts = this.dataset.rgba.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
                    if (!parts) {
                        rgba += '1)';
                    } else {
                        opacity = parts[4];
                        rgba += parts[4]+')';
                    }
                }
                this.dataset.rgba = rgba;
                $g(this).next().find('.minicolors-swatch-color').css('background-color', rgba);
                $g(this).trigger('minicolorsInput');
                app.setMinicolorsColor(rgba);
            }
            $g(this).closest('.ba-settings-item').find('.minicolors-opacity').val(opacity).removeAttr('readonly');
        },
        updateInput: function(input, rgba){
            var color = app.rgba2hex(rgba);
            input.attr('data-rgba', rgba).val(color[0]).next().find('.minicolors-swatch-color').css('background-color', rgba);
            input.closest('.minicolors').next().find('.minicolors-opacity').val(color[1]);
        },
        rgba2hex: function(rgb){
            var parts = rgb.toLowerCase().match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
                hex = '#',
                part,
                color = [];
            if (parts) {
                for (var i = 1; i <= 3; i++) {
                    part = parseInt(parts[i]).toString(16);
                    if (part.length < 2) {
                        part = '0'+part;
                    }
                    hex += part;
                }
                if (!parts[4]) {
                    parts[4] = 1;
                }
                color.push(hex);
                color.push(parts[4] * 1);
                
                return color;
            } else {
                color.push(rgb.trim());
                color.push(1);
                
                return color;
            }
        },
        setTooltip: function(query){
            $g(query).find('.ba-tooltip').each(function(){
                setTooltip(this)
            });
        }
    },
    massage = '',
    sortableInd,
    pageId,
    item,
    CKE,
    themeTitle = '',
    flag = true,
    exportId = [],
    submitTask,
    deleteMode,
    uploadMode,
    currentContext,
    $g = null,
    gridboxCallback,
    fontBtn,
    oldTitle = '',
    moveTo = '',
    authorSocial = {
        "behance":{
            "title":"behance",
            "label":"Behance",
            "icon":"zmdi zmdi-behance"
        },
        "dribbble":{
            "title":"dribbble",
            "label":"Dribbble",
            "icon":"zmdi zmdi-dribbble"
        },
        "facebook":{
            "title":"facebook",
            "label":"Facebook",
            "icon":"zmdi zmdi-facebook"
        },
        "google+":{
            "title":"google+",
            "label":"Google+",
            "icon":"zmdi zmdi-google-plus"
        },
        "instagram":{
            "title":"instagram",
            "label":"Instagram",
            "icon":"zmdi zmdi-instagram"
        },
        "linkedin":{
            "title":"linkedin",
            "label":"Linkedin",
            "icon":"zmdi zmdi-linkedin"
        },
        "odnoklassniki":{
            "title":"odnoklassniki",
            "label":"Odnoklassniki",
            "icon":"zmdi zmdi-odnoklassniki"
        },
        "pinterest":{
            "title":"pinterest",
            "label":"Pinterest",
            "icon":"zmdi zmdi-pinterest"
        },
        "tumblr":{
            "title":"tumblr",
            "label":"Tumblr",
            "icon":"zmdi zmdi-tumblr"
        },
        "twitter":{
            "title":"twitter",
            "label":"Twitter",
            "icon":"zmdi zmdi-twitter"
        },
        "vimeo":{
            "title":"vimeo",
            "label":"Vimeo",
            "icon":"zmdi zmdi-vimeo"
        },
        "vkontakte":{
            "title":"vkontakte",
            "label":"Vkontakte",
            "icon":"zmdi zmdi-vk"
        },
        "youtube":{
            "title":"youtube",
            "label":"Youtube",
            "icon":"zmdi zmdi-youtube"
        }
    };

async function makeFetchRequest(url, data, isFile)
{
    let body = app.getFormData(data, isFile),
        options = {
            method: 'POST',
            body: body
        },
        request = await fetch(url, options),
        response = null;
    if (request.ok) {
        let text = await request.text();
        try {
            response = JSON.parse(text);
        } catch (err) {
            console.info(text);
            console.info(err);
        }
    } else {
        let utf8Decoder = new TextDecoder("utf-8"),
            reader = request.body.getReader(),
            textData = await reader.read(),
            text = utf8Decoder.decode(textData.value);
        console.info(text);
    }

    return response;
}

function getCSSrulesString()
{
    var str = 'body.cke_editable  {font-family: sans-serif, Arial, Verdana, "Trebuchet MS";}';
    str += '::-webkit-scrollbar {width: 6px;} ::-webkit-scrollbar-track {background-color: transparent; }'
    str += '::-webkit-scrollbar-thumb {background: #ddd;border-radius: 6px;}';
    
    return str;
}

function setCkeditor()
{
    if (typeof(CKEDITOR) != 'undefined') {
        if ($g('html').attr('dir') == 'rtl') {
            CKEDITOR.config.contentsLangDirection = 'rtl';
        }
        let toolbars = {
            basic: [
                {name: 'document', items: ['Source']},
                {name: 'styles', items: ['Format']},
                {name: 'colors', items: ['TextColor']},
                {name: 'basicstyles', items: ['Bold', 'Italic']},
                {name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'myJustifyLeft', 'JustifyCenter', 'JustifyRight']},
                {name: 'links', items: ['Link', 'Unlink']},
                {name: 'insert', items: ['myImage']},
                {name: 'data-tags', items: ['dataTags', 'resizeEditor']}
            ],
            simple: [
                {name: 'document', items: ['Source']},
                {name: 'basicstyles', items: ['Bold', 'Italic']},
                {name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'myJustifyLeft', 'JustifyCenter', 'JustifyRight']},
                {name: 'links', items: ['Link', 'Unlink']}
            ]
        };
        CKEDITOR.dtd.$removeEmpty.span = 0;
        CKEDITOR.dtd.$removeEmpty.i = 0;
        CKEDITOR.config.removePlugins = 'image,magicline';
        CKEDITOR.config.uiColor = '#fafafa';
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.contentsCss = [getCSSrulesString()];
        $g('.category-description, .ckeditor-options-wrapper textarea, #resized-ckeditor-dialog textarea').each(function(){
            let key = this.dataset.settings ? this.dataset.settings : this.dataset.key,
                toolbar = this.dataset.cke ? this.dataset.cke : 'basic';
            if (this.dataset.group) {
                key = this.dataset.group+'-'+key;
            }
            app.cke[key] = CKEDITOR.replace(this);
            app.cke[key].config.toolbar_Basic = toolbars[toolbar];
            app.cke[key].config.toolbar = 'Basic';
            app.cke[key].config.height = 150;
        });
    }
}

function loadPage(firstLoading)
{
    document.querySelectorAll('[task="reviews.readAll"] button').forEach((btn) => {
        if (document.querySelector('.unread-comments-count[data-type="reviews"]')) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'true');
        }
    });
    document.querySelectorAll('[task="comments.readAll"] button').forEach((btn) => {
        if (document.querySelector('.unread-comments-count[data-type="comments"]')) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'true');
        }
    });
    if (window.booking) {
        for (let day in window.booking.default) {
            let obj = window.booking.default[day];
            if (!obj.enable) {
                continue;
            }
            let row = document.querySelector('.ba-booking-calendar-row[data-time="'+obj.hours[0].start+'"]');
            if (!row) {
                continue;
            }
            let body = document.querySelector('.ba-booking-calendar-body'),
                rect = row.getBoundingClientRect(),
                targetRect = body.getBoundingClientRect();
            body.scrollTop = rect.top - targetRect.top;
            break;
        }
        let cells = document.querySelectorAll('.main-table[data-layout="monthly"] .ba-booking-calendar-body .ba-booking-calendar-cell');
        cells.forEach((cell, i) => {
            Array.from(cell.children).forEach((div, j) => {
                if (!div.classList.contains('multiple-appointment') || div.classList.contains('clone-multiple-appointment')) {
                    return;
                }
                let nights = (new Date(div.dataset.end) - new Date(cell.dataset.date)) / 60 / 60 / 1000 / 24,
                    clone = null;
                div.style.setProperty('--multiple-nights', nights);
                if (div.dataset.start < cell.dataset.date) {
                    div.classList.add('clone-multiple-appointment');
                }
                if (nights == 0) {
                    div.classList.add('last-multiple-clone')
                }
                for (let c = 1; c <= nights; c++) {
                    let k = i + c,
                        child = cells[k].children[j];
                    if (!cells[k].dataset.date) {
                        continue;
                    }
                    clone = div.cloneNode();
                    clone.classList.add('clone-multiple-appointment');
                    if (c == nights) {
                        clone.classList.add('last-multiple-clone')
                    }
                    if (child && child.classList.contains('empty-booking-appointement')) {
                        child.before(clone);
                        child.remove();
                    } else if (child) {
                        child.before(clone);
                    } else {
                        for (let n = 0; n < j; n++) {
                            if (cells[k].children[n]) {
                                continue;
                            }
                            let empty = document.createElement('div');
                            empty.className = 'empty-booking-appointement';
                            cells[k].append(empty);
                        }
                        cells[k].append(clone);
                    }
                }
            })
        })
    }

    $g('.multiple-appointment').on('mouseenter', function(){
        $g('.multiple-appointment[data-id="'+this.dataset.id+'"]').addClass('multiple-appointment-hovered')
    }).on('mouseleave', function(){
        $g('.multiple-appointment[data-id="'+this.dataset.id+'"]').removeClass('multiple-appointment-hovered')
    })

    if ($g('.general-tabs').length > 0) {
        setTabsUnderline();
        $g('.general-tabs ul.uploader-nav').off('show').on('show', function(event){
            event.stopPropagation();
            var ind = [],
                ul = $g(event.currentTarget),
                id = $g(event.relatedTarget).attr('href'),
                aId = $g(event.target).attr('href');
            ul.find('li a').each(function(i){
                if (this == event.target) {
                    ind[0] = i;
                }
                if (this == event.relatedTarget) {
                    ind[1] = i;
                }
            });
            if (ind[0] > ind[1]) {
                $g(id).addClass('out-left');
                $g(aId).addClass('right');
                setTimeout(function(){
                    $g(id).removeClass('out-left');
                    $g(aId).removeClass('right');
                }, 500);
            } else {
                $g(id).addClass('out-right');
                $g(aId).addClass('left');
                setTimeout(function(){
                    $g(id).removeClass('out-right');
                    $g(aId).removeClass('left');
                }, 500);
            }
            setTabUnderline(event.target, ul.next()[0])
        });
    }
    jQuery('#filter-bar .ba-custom-select input[type="text"]').each(function(){
        this.size = this.value.length;
    });
    app.appsList.setEvents();
    $g('.open-calendar-dialog').each(function(){
        if (!this.dataset.created) {
            createCalendar(this);
        }
    })
    jQuery('#filter-bar .ba-custom-select').on('customAction', function(){
        var input = this.querySelector('input[type="text"]');
        input.size = input.value.length;
    });
    $g('span[data-sorting]').on('click', function(){
        var order = $g('[name="filter_order"]'),
            direction = $g('[name="filter_order_Dir"]'),
            dir = direction.val();
        if (order.val() == this.dataset.sorting) {
            dir = dir == 'asc' ? 'desc' : 'asc';
        }
        order.val(this.dataset.sorting);
        direction.val(dir);
        createAjax();
    });
    if (document.querySelector('.payment-methods-table, .shipping-table') && !firstLoading) {
        setCkeditor()
    }
    $g('#theme-import-file').on('change', function(){
        if (this.files.length > 0) {
            var array = this.files[0].name.split('.'),
                n = array.length - 1,
                ext = array[n];
            $g('.theme-import-trigger').val(this.files[0].name);
            if (ext != 'xml') {
                showNotice(app._('UPLOAD_ERROR'), 'ba-alert');
                $g('.apply-import').removeClass('active-button');
            } else {
                $g('.apply-import').addClass('active-button');
            }
        }
    });

    $g('#theme-import-trigger').on('click', function(){
        document.getElementById('theme-import-file').click();
    });

    app.getAppLicense = function(){
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=pages.getAppLicense",
            data:{
                data: gridboxUser.data
            },
            success : function(msg){
                if ($g('#login-modal').hasClass('in')) {
                    $g('#login-modal').modal('hide');
                }
                if ('callback' in gridboxUser) {
                    gridboxUser.callback();
                }
                if (gridboxCallback == 'dashboard') {
                    showNotice(app._('YOUR_LICENSE_ACTIVE'));
                    $g('.ba-gridbox-dashboard-row.gridbox-activate-license').hide();
                    $g('.ba-gridbox-dashboard-row.gridbox-deactivate-license').css('display', '');
                    $g('.ba-dashboard-popover-trigger[data-target="ba-dashboard-about"]').each(function(){
                        let count = this.querySelector('.about-notifications-count');
                        count.textContent = count.textContent * 1 - 1;
                        if (count.textContent == 0) {
                            this.querySelector('i').className = 'zmdi zmdi-info';
                            count.style.display = 'none';
                        }
                    });
                }
            }
        });
    }

    $g('.login-button.active-button').on('click', function(event){
        event.preventDefault();
        if (!$g(this).attr('data-submit')) {
            $g(this).attr('data-submit', 'false');
            let script = document.createElement('script'),
                url = 'https://www.balbooa.com/demo/index.php?',
                domain = window.location.host.replace('www.', '');
            domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
            url += 'option=com_baupdater&task=gridbox.getGridboxUser';
            url += '&login='+window.btoa($g('.ba-username').val().trim());
            url += '&password='+window.btoa($g('.ba-password').val().trim());
            if (domain[domain.length - 1] != '/') {
                domain += '/';
            }
            url += '&domain='+window.btoa(domain);
            url += '&time='+(+(new Date()));
            script.src = url;
            script.onload = function(){
                $g('.login-button.active-button').removeAttr('data-submit');
            }
            document.head.appendChild(script);
        }
    });

    $g('.ba-username, .ba-password').on('keyup', function(event){
        if (event.keyCode == 13) {
            document.querySelector('.login-button.active-button').click();
        }
    });

    $g('#filter_search').on('keydown', function(event){
        if (event.keyCode == 13) {
            createAjax();
        }
    });

    $g('div[class$="-filter"] [type="hidden"], #limit').on('change', function(event){
        if (this.dataset.name) {
            $g('input[name="'+this.dataset.name+'"]').val(this.value);
        }
        createAjax();
    });

    $g('.ba-custom-author-select > i, div.ba-custom-author-select input').on('click', function(event){
        var $this = $g(this),
            parent = $this.parent();
        if (!parent.find('ul').hasClass('visible-select')) {
            event.stopPropagation();
            $g('.visible-select').removeClass('visible-select');
            parent.find('ul').addClass('visible-select');
            parent.find('li').off('click').one('click', function(){
                var text = this.textContent.trim(),
                    image = this.dataset.image,
                    authors = [],
                    author = '',
                    li = parent.find('li[data-value]'),
                    val = this.dataset.value,
                    str = '<span class="selected-author" data-id="'+val;
                str += '"><span class="ba-author-avatar" style="background-image: url(';
                str += image.replace(/\s/g, '%20')+')"></span><span class="ba-author-name">'+text+'</span>';
                str += '<i class="zmdi zmdi-close remove-selected-author"></i></span>';
                parent.before(str);
                parent.trigger('customAction');
                parent.parent().find('.selected-author').each(function(){
                    authors.push(this.dataset.id);
                });
                li.each(function(){
                    if (authors.indexOf(this.dataset.value) == -1) {
                        this.style.display = ''
                    } else {
                        this.style.display = 'none';
                    }
                });
                if (li.length == authors.length) {
                    $g('.select-post-author').hide();
                } else {
                    $g('.select-post-author').css('display', '');
                }
                author = authors.join(',');
                parent.find('input[type="hidden"]').val(author);
            });
            parent.trigger('show');
            setTimeout(function(){
                $g('body').one('click', function(){
                    $g('.visible-select').removeClass('visible-select');
                });
            }, 50);
        }
    });

    $g('div.ba-custom-select').on('show', function(){
        if (!this.classList.contains('orders-status-select')) {
            var $this = $g(this),
                ul = $this.find('ul'),
                value = $this.find('input[type="hidden"]').val();
            ul.find('i').remove();
            ul.find('.selected').removeClass('selected');
            ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
        }
    });

    $g('.reset-filtering').off('click').on('click', function(){
        $g('[name="filter_state"], [name$="_filter"], [name="publish_up"], [name="publish_down"]').val('');
        createAjax();
    });
    $g('.reset-calendar-filtering').off('click').on('click', function(){
        $g('[name="publish_up"], [name="publish_down"]').val('');
        createAjax();
    });
    $g('.enable-custom-pages-order').off('click').on('click', function(){
        let order = $g('[name="filter_order"]');
        if (order.val() != 'order_list') {
            $g('[name="filter_order"]').val('order_list');
        } else {
            $g('[name="filter_order"]').val('id');
        }
        createAjax();
    });
    $g('.create-categery').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        if (!('permitted' in this.dataset)) {
            var id = 0,
                $this = $g('.category-list > ul').find('li.active');
            if ($this.hasClass('ba-category')) {
                var obj = $this.find('> a input[type="hidden"]').val();
                obj = JSON.parse(obj);
                id = obj.id;
            }
            $g('.parent-id').val(id);
            $g('.category-name').val('');
            $g('#create-category-modal').modal();
        } else {
            showNotice(app._('CREATE_NOT_PERMITTED'));
        }
    });

    $g('.create-tags-folder').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        let modal = $g('#tags-folder-modal');
        modal.find('h3').text(app._('CREATE_FOLDER'));
        modal.find('#apply-tags-folder').attr('data-id', 0);
        modal.find('input').val('');
        modal.modal();
    });

    $g('.tags-folder-name').off('input').on('input', function(){
        document.querySelector('#apply-tags-folder').classList[this.value.trim() != '' ? 'add' : 'remove']('active-button');
    });

    $g('#apply-tags-folder').off('click').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return;
        }
        $g('#tags-folder-modal').modal('hide');
        app.fetch('index.php?option=com_gridbox&task=tags.createFolder', {
            title: $g('.tags-folder-name').val().trim(),
            id: this.dataset.id
        }).then((text) => {
            app.loadPageContent(window.location.href).then(function(){
                loadPage();
                showNotice(text, '');
            });
        })
    });

    $g('.tags-folders-list ul').each(function(ind){
        new sortable(this, {
            handle : '> .ba-tags-folder > span > .sorting-handle',
            selector : '> .ba-tags-folder',
            change: function(element){
                let ind = 1,
                    data = [];
                $g('.tags-folders-list ul .ba-tags-folder').each(function(){
                    data.push({
                        id: this.dataset.id,
                        order_list: ind++
                    });
                });
                app.fetch('index.php?option=com_gridbox&task=tags.orderFolders', {
                    data: JSON.stringify(data)
                })
            },
            group: 'categories-'+ind
        });
    });

    $g('.tags-folders-list ul li').on('contextmenu', function(event){
        currentContext = $g(this);
        $g('span.tags-folder-delete')[this.dataset.id == 1 ? 'addClass' : 'removeClass']('disabled');
        showContext(event, $g('.tags-folder-context-menu'));
    });

    $g('span.tags-folder-delete').off('mousedown').on('mousedown', function(){
        if (this.classList.contains('disabled')) {
            return;
        }
        let id = currentContext.attr('data-id');
        $g('#context-item').val(id);
        deleteMode = 'tags.deleteTagsFolder';
        $g('#delete-dialog').modal();
    });

    $g('span.tags-folder-rename').off('mousedown').on('mousedown', function(){
        let modal = $g('#rename-modal'),
            id = currentContext.attr('data-id'),
            title = currentContext.find('a span').text().trim();
        modal.find('input[type="text"]').val(title);
        modal.find('#apply-rename').attr('data-id', id).addClass('active-button');
        modal.modal();
    });

    $g('#rename-modal input[type="text"]').off('input').on('input', function(){
        $g('#apply-rename')[this.value.trim() ? 'addClass' : 'removeClass'];
    });

    $g('#apply-rename').off('click').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return;
        }
        let modal = $g('#rename-modal'),
            data = {
                id: this.dataset.id,
                title: modal.find('input[type="text"]').val().trim()
            };
        modal.modal('hide');
        $g('.tags-folders-list li[data-id="'+data.id+'"] a span').text(data.title);
        app.fetch('index.php?option=com_gridbox&task=tags.renameFolder', data);
    });

    $g('span.tags-move').on('mousedown', function(){
        let str = currentContext.find('.select-td input[type="hidden"]').val(),
            obj = JSON.parse(str);
        moveTo = 'tags.move';
        $g('#context-item').val(obj.id);
        showTagsMoveTo();
    });

    app.setTooltip('body div');

    $g('ul.root-list').off('click').on('click', 'i.zmdi-chevron-right', function(){
        var $this = $g(this).parent(),
            blog = $g('input[name="blog"]').val(),
            category = this.parentNode.dataset.id;
        getVisibleBranchClilds($this);
        if ($this.hasClass('visible-branch')) {
            $this.removeClass('visible-branch');
            deleteCookie('blog'+blog+'id'+category);
        } else {
            $this.addClass('visible-branch');
            setCookie('blog'+blog+'id'+category, 1);
        }
        getParentVisibleBranchClilds($this);
    });

    $g('.main-table tbody.order-list-sorting').each(function(){
        let handle = this.dataset.handle;
        new sortable(this, {
            handle : handle ? handle : '> tr > td',
            selector : '> tr',
            change: function(element){
                var cid = [],
                    order = [],
                    root_order = [],
                    type = 'pages',
                    category = $g('.order-list-sorting').attr('data-category');
                $g('.order-list-sorting tr').each(function(){
                    cid.push($g(this).find('.select-td input[type="checkbox"]').val() * 1);
                    order.push($g(this).find('.title-cell input[name="order[]"]').val() * 1)
                    root_order.push($g(this).find('.title-cell input[name="root_order[]"]').val() * 1)
                });
                order.sort(function(a, b){
                    return a * 1 > b * 1 ? 1 : -1;
                });
                root_order.sort(function(a, b){
                    return a * 1 > b * 1 ? 1 : -1;
                });
                if ($g('.main-table').hasClass('tags-table')) {
                    type = 'tags';
                } else if ($g('.main-table').hasClass('authors-table')) {
                    type = 'authors';
                } else if ($g('.main-table').hasClass('shipping-table')) {
                    type = 'store_shipping';
                } else if ($g('.main-table').hasClass('payment-methods-table')) {
                    type = 'store_payment_methods';
                }
                $g.ajax({
                    type : "POST",
                    dataType : 'text',
                    url : 'index.php?option=com_gridbox&task=pages.orderPages&tmpl=component',
                    data : {
                        cid : cid,
                        type: type,
                        category: category,
                        root_order: root_order,
                        order: order
                    }
                });
            },
            group: 'pages'
        });
    });

    $g('input[name="category_order_list"]').val(sortableInd);

    $g('.category-list ul.root-list .root ul').each(function(ind){
        new sortable(this, {
            handle : '> .ba-category > span > .sorting-handle',
            selector : '> .ba-category',
            change: function(element){
                sortableInd = 1;
                var data = [];
                $g('.category-list ul.root-list .ba-category').each(function(){
                    var obj = {
                        id : this.dataset.id,
                        order_list : sortableInd++
                    }
                    data.push(obj);
                });
                $g('input[name="category_order_list"]').val(sortableInd);
                $g.ajax({
                    type : "POST",
                    dataType : 'text',
                    url : 'index.php?option=com_gridbox&task=apps.orderCategories&tmpl=component',
                    data : {
                        data : JSON.stringify(data)
                    },
                    success: function(msg){
                        
                    }
                });
            },
            group : 'categories-'+ind
        });
    });

    $g('.sorting-container').each(function(){
        new sortable(this, {
            handle : '> .sorting-item .sortable-handle',
            selector : '> .sorting-item',
            group : 'sorting-container'
        });
    })

    $g('ul.root-list a, .tags-folders-list li a').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        let src = this.href;
        document.getElementById("adminForm").action = src;
        window.history.pushState(null, null, src);
        app.loadPageContent(src).then(function(){
            loadPage();
        });
    });

    $g('ul.root-list li.ba-category').on('contextmenu', function(event){
        currentContext = $g(this);
        showContext(event, $g('.category-context-menu'));
    });

    $g('ul.root-list i.open-category-settings').on('mousedown', function(event){
        event.stopPropagation();
        currentContext = $g(this).closest('li.ba-category');
        $g('span.category-settings').trigger('mousedown');
    });

    $g('.main-table:not(.dashboard-content):not(.trashed-list) tbody tr').on('contextmenu', function(event){
        if (document.querySelector('.page-context-menu')) {
            currentContext = $g(this);
            showContext(event, $g('.page-context-menu'));
        }
    });
    $g('.main-table.trashed-list tbody tr').on('contextmenu', function(event){
        currentContext = $g(this);
        if (this.dataset.type != 'system') {
            showContext(event, $g('.page-context-menu'));
        } else {
            showContext(event, $g('.system-page-context-menu'));
        }
    });
    $g('div[data-view="schedule"] .ba-booking-calendar-body > div').on('contextmenu', function(){
        currentContext = $g(this);
        let query = this.dataset.type == 'appointment' ? 'booking-appointment' : 'block-time';
        showContext(event, $g('.'+query+'-context-menu'));
    });
    $g('div[data-view="calendar"] .booking-appointment[data-id]').on('contextmenu', function(){
        currentContext = $g(this);
        showContext(event, $g('.booking-appointment-context-menu'));
    });
    $g('div[data-view="calendar"] .booking-appointment[data-product]').on('contextmenu', function(event){
        currentContext = $g(this);
        showContext(event, $g('.monthly-product-context-menu'));
    });
    $g('div[data-view="calendar"] .booking-appointment[data-product]').on('click', function(){
        app.booking.viewMonthlyItems(this);
    })
    $g('.booking-appointment[data-id], .ba-booking-calendar-row[data-type="appointment"]').on('click', function(){
        app.booking.getDetails(this.dataset.id);
    })
    $g('div[data-view="calendar"] .ba-booking-calendar-cell[data-date]').on('contextmenu', function(){
        currentContext = $g(this);
        showContext(event, $g('.calendar-cell-context-menu'));
    });
    $g('div[data-view="calendar"] .booking-appointment-time-block').on('contextmenu', function(){
        currentContext = $g('.booking-appointment-time-block[data-id="'+this.dataset.id+'"]');
        showContext(event, $g('.block-time-context-menu'));
    });
    $g('div[data-layout="daily"] .booking-appointment').each(function(){
        let height = 0;
        this.querySelectorAll('*').forEach((child) => {
            if (child.classList.contains('booking-appointment-count')) {
                return;
            }
            height += child.offsetHeight;
        })
        this.style.setProperty('--offset-height', height + 'px')
    });
    $g('.ba-title-click-trigger').on('click', function(){
        currentContext = $g(this).closest('tr');
        $g('span.tags-settings').trigger('mousedown');
    });
    $g('.toggle-sidebar').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        let body = $g('body');
        if (body.hasClass('visible-sidebar')) {
            $g('body').removeClass('visible-sidebar');
            deleteCookie('gridbox-sidebar');
        } else {
            $g('body').addClass('visible-sidebar');
            setCookie('gridbox-sidebar', 'visible', {
                expires: 60 * 60 * 24 * 30 * 365
            });
        }
    });

    $g('.ba-create-tags').on('mousedown', function(event){
        event.preventDefault();
        let modal = $g('#create-new-tag-modal');
        modal.find('.ba-btn-primary').removeClass('active-button');
        modal.find('input[type="text"]').val('');
        modal.modal();
    });

    $g('#tag-name').off('input').on('input', function(){
        let flag = true,
            modal = $g(this).closest('.modal');
        modal.find('input').each(function(){
            if (!this.value.trim()) {
                flag = false;
            }
        });
        if (flag) {
            modal.find('.ba-btn-primary').addClass('active-button');
        } else {
            modal.find('.ba-btn-primary').removeClass('active-button');
        }
    });

    $g('.select-user').on('click', function(){
        showUsersDialog(0, this);
    });
    $g('.blog-settings').on('mousedown', function(){
        var obj = $g('#blog-data').val(),
            modal = $g('#category-settings-dialog'),
            value;
        obj = JSON.parse(obj);
        $g('#category-settings-dialog input[data-key="core.edit.layouts"]').closest('.ba-group-element').removeAttr('disabled');
        app.setSubgroupChilds($g('#category-settings-dialog .permission-action-wrapper')[0]);
        app.associations.prepare(modal, obj.language, obj.id, 'app');
        $g('#category-settings-dialog .permissions-options').each(function(){
            getPermissions(obj.id, 'app', this);
        });
        $g('#category-settings-dialog').find('.select-data-tags, .seo-default-settings').css('display', 'none');
        $g('.ba-dashboard-apps-dialog.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        $g('.category-title').val(obj.title);
        $g('.category-id').val(obj.id);
        $g('.category-alias').val(obj.alias);
        $g('.apply-blog-settings').css('display', '');
        $g('.category-settings-apply').hide();
        $g('.blog-theme-select').closest('.ba-options-group').css('display', '');
        $g('.category-access-select input[type="hidden"]').val(obj.access);
        var access = $g('.category-access-select li[data-value="'+obj.access+'"]').text().trim();
        $g('.category-access-select input[type="text"]').val(access);
        value = $g('.blog-theme-select li[data-value="'+obj.theme+'"]').text().trim();
        $g('.blog-theme-select input[type="hidden"]').val(obj.theme);
        $g('.blog-theme-select input[type="text"]').val(value);
        value = $g('.category-robots-select li[data-value="'+obj.robots+'"]').text().trim();
        $g('.category-robots-select input[type="hidden"]').val(obj.robots);
        $g('.category-robots-select input[type="text"]').val(value);
        $g('.category-meta-title').val(obj.meta_title);
        $g('.category-meta-description').val(obj.meta_description);
        $g('.category-meta-keywords').val(obj.meta_keywords);
        app.cke.description.setData(obj.description);
        $g('.category-publish').prop('checked', obj.published == 1);
        let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
        $g('.category-intro-image').val(obj.image).parent().find('.image-field-tooltip').css({
            'background-image': obj.image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        if (obj.share_image == 'share_image') {
            obj.share_image = obj.image;
        }
        image = !app.isExternal(obj.share_image) ? JUri+obj.share_image : obj.share_image;
        $g('.category-share-image').val(obj.share_image).parent().find('.image-field-tooltip').css({
            'background-image': obj.share_image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        $g('.category-share-title').val(obj.share_title);
        $g('.category-share-description').val(obj.share_description);
        let clone = document.querySelector('.apps-sitemap-template').content.cloneNode(true);
        $g('#category-sitemap-options').html(clone);
        $g('#category-sitemap-options .ba-range-wrapper input[type="range"]').each(function(){
            rangeAction(this, inputCallback);
        });
        $g('#category-settings-dialog textarea[name="category_schema_markup"]').val(obj.schema_markup);
        $g('#category-settings-dialog .sitemap-include').prop('checked', Boolean(obj.sitemap_include * 1));
        var range = $g('#category-settings-dialog .priority').val(obj.priority).prev().val(obj.priority);
        setLinearWidth(range);
        $g('#category-settings-dialog .changefreq').val(obj.changefreq).prev().each(function(){
            this.value = $g(this).closest('.ba-custom-select').find('li[data-value="'+obj.changefreq+'"]').text().trim();
        });
        $g('#category-settings-dialog .set-group-display').each(function(){
            var action = this.checked ? 'addClass' : 'removeClass';
            $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
        });
        $g('i.zmdi-check.disabled-button').removeClass('disabled-button');
        $g('.ba-alert-container').hide();
        $g('#category-settings-dialog').modal();
    });
    $g('.single-settings').on('click', function(){
        var blog = $g('#blog-data').val();
        blog = JSON.parse(blog);
        oldTitle = blog.title;
        $g('.blog-title').val(blog.title);
        $g('.apply-single-settings').removeClass('active-button');
        $g('#single-settings-modal').modal();
    });
    $g('.booking-calendar-settings').on('click', function(){
        let modal = $g('#booking-calendar-settings-dialog');
        modal.modal();
    });
    $g('.ba-booking-calendar-action').on('click', function(){
        document.querySelector('input[name="calendar_date"]').value = this.dataset.value;
        createAjax();
    })
    $g('.ba-booking-calendar-today input').on('update', function(){
        document.querySelector('input[name="calendar_date"]').value = this.dataset.value;
        createAjax();
    });
    $g('.ba-booking-calendar-layout-action-wrapper .layout-action').on('click', function(){
        if (this.classList.contains('active')) {
            return;
        }
        document.querySelector('.ba-booking-calendar-layout-action-wrapper .active').classList.remove('active');
        this.classList.add('active');
        let input = document.querySelector('input[name="booking_view"]');
        input.value = this.dataset.layout;
        createAjax();
    })
    $g('.ba-booking-calendar-select').on('customAction', function(){
        document.querySelector('input[name="calendar_date"]').value = '';
        createAjax();
    })
    $g('.comments-settings').on('click', function(){
        let view = $g('input[name="ba_view"]').val();
        app.fetch('index.php?option=com_gridbox&task='+view+'.getSettings').then((text) => {
            let obj = JSON.parse(text),
                modal = $g('#comments-settings-dialog'),
                commentsModerators,
                str = '';
            modal.find('.website-comments-settings').each(function(){
                if (this.type == 'checkbox') {
                    this.checked = Boolean(obj.website[this.dataset.website] * 1);
                } else {
                    this.value = obj.website[this.dataset.website];
                }
            });
            if (obj.moderators == 'super_user') {
                commentsModerators = [];
                for (let i = 0; i < obj.users.length; i++) {
                    if (obj.users[i].level == 8) {
                        commentsModerators.push(obj.users[i].id);
                    }
                }
            } else {
                commentsModerators = obj.moderators.split(',');
            }
            commentsModerators.forEach((id, i) => {
                commentsModerators[i] *= 1;
            })
            for (let i = 0; i < obj.users.length; i++) {
                let index = commentsModerators.indexOf(obj.users[i].id)
                if (index != -1) {
                    delete (commentsModerators[index]);
                    str += '<li data-value="'+obj.users[i].id+'"><span>'+obj.users[i].name;
                    str += '</span><i class="zmdi zmdi-close"></i></li>';
                }
            }
            $g('.comments-moderators-list li:not(.add-comments-moderator)').remove();
            $g('.comments-moderators-list li.add-comments-moderator').before(str);
            $g('.comments-banned-list-wrapper li:not(.enter-comments-banned-item)').remove();
            str = '';
            for (let i = 0; i < obj.commentsBanList.emails.length; i++) {
                str += '<li><span>'+obj.commentsBanList.emails[i].email+'</span><i class="zmdi zmdi-close"></i></li>';
            }
            $g('.comments-banned-emails li.enter-comments-banned-item').before(str);
            str = '';
            for (let i = 0; i < obj.commentsBanList.words.length; i++) {
                str += '<li><span>'+obj.commentsBanList.words[i].word+'</span><i class="zmdi zmdi-close"></i></li>';
            }
            $g('.comments-banned-words li.enter-comments-banned-item').before(str);
            str = '';
            for (let i = 0; i < obj.commentsBanList.ip.length; i++) {
                str += '<li><span>'+obj.commentsBanList.ip[i].ip+'</span><i class="zmdi zmdi-close"></i></li>';
            }
            $g('.comments-banned-ip li.enter-comments-banned-item').before(str);
            modal.find('.set-group-display').each(function(){
                var action = this.checked ? 'addClass' : 'removeClass';
                $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
            });
            modal.find('.ba-custom-select').each(function(){
                let value = this.querySelector('input[type="hidden"]').value,
                    text = $g(this).find('li[data-value="'+value+'"]').text().trim();
                if (!text) {
                    text = app._('NONE_SELECTED');
                    this.querySelector('input[type="hidden"]').value = '';
                }
                $g(this).find('input[type="text"]').val(text);
            });
            modal.modal();
        })
    });
    $g('.app-duplicate').on('mousedown', function(){
        $g('.ba-dashboard-apps-dialog.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        $g(this).off('mousedown');
        app.showLoading('LOADING');
        Joomla.submitbutton('pages.duplicateApp');
    });
    $g('.set-featured-post').on('click', function(){
        let id = $g(this).closest('tr').find('td.select-td input[type="checkbox"]').val();
        app.fetch('index.php?option=com_gridbox&task=apps.setFeatured', {
            id: id,
            featured: this.dataset.featured
        }).then((text) => {
            $g('body > .ba-tooltip').remove();
            reloadPage(text);
        });
    });

    app.sidebar.contextEvents();

    $g('.pagination-toolbar li a').on('click', function(event){
        event.preventDefault();
    });
}

function showUsersDialog(id, $this)
{
    fontBtn = $this;
    $g('.user-sorting-select').each(function(){
        let value = $g(this).find('li[data-value="id"]').text().trim();
        $g(this).find('input[type="text"]').val(value);
        $g(this).find('input[type="hidden"]').val('id');
    });
    $g('.user-direction-select').each(function(){
        let value = $g(this).find('li[data-value="asc"]').text().trim();
        $g(this).find('input[type="text"]').val(value);
        $g(this).find('input[type="hidden"]').val('asc');
    });
    $g('.user-group-select').each(function(){
        let value = $g(this).find('li[data-value=""]').text().trim();
        $g(this).find('input[type="text"]').val(value);
        $g(this).find('input[type="hidden"]').val('');
    });
    $g('.user-sorting-select').trigger('customAction');
    $g('.search-ba-author-users').val('');
    $g('.modal[data-modal-type="users-dialog"] .ba-options-group').css('display', '');
    $g('.modal[data-modal-type="users-dialog"] .ba-group-wrapper').attr('data-id', id);
    $g('.modal[data-modal-type="users-dialog"]').modal();
}

function checkContext(context, deltaY, deltaX)
{
    context[deltaX - context.width() < 0 ? 'addClass' : 'removeClass']('ba-left');
    context[deltaY - context.height() < 0 ? 'addClass' : 'removeClass']('ba-top');
}

function showContext(event, context)
{
    event.stopPropagation();
    event.preventDefault();
    $g('.context-active').removeClass('context-active');
    $g('.ba-context-menu').not(context).hide();
    currentContext.addClass('context-active');
    let deltaX = document.documentElement.clientWidth - event.pageX,
        deltaY = document.documentElement.clientHeight - event.clientY;
    setTimeout(function(){
        context.css({
            top : event.pageY,
            left : event.pageX,
        }).show();
        checkContext(context, deltaY, deltaX);
    }, 50);
}

function setTooltip(item)
{
    $g(item).parent().off('mouseenter mouseleave').on('mouseenter', function(){
        if (this.closest('.ba-sidebar') && document.body.classList.contains('visible-sidebar')) {
            return false;
        }
        var coord = this.getBoundingClientRect(),
            top = coord.top,
            clone = this.querySelector('.ba-tooltip').cloneNode(true),
            center = (coord.right - coord.left) / 2;
        center = coord.left + center;
        if (clone.classList.contains('ba-bottom')) {
            top = coord.bottom;
        }
        $g('body').append(clone);
        var tooltip = $g(clone),
            width = tooltip.outerWidth(),
            height = tooltip.outerHeight();
        if (tooltip.hasClass('ba-top') || tooltip.hasClass('ba-help')) {
            top -= (15 + height);
            center -= (width / 2)
        } else if (tooltip.hasClass('ba-left') || tooltip.hasClass('ba-right')) {
            top += (coord.bottom - coord.top - height) / 2;
        }
        if (tooltip.hasClass('ba-left')) {
            center = coord.left - width - 15
        } else if (tooltip.hasClass('ba-right')) {
            center = coord.right + 15
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
        var tooltip = $g('body > .ba-tooltip').addClass('tooltip-hidden');
        setTimeout(function(){
            tooltip.remove();
        }, 500);
    });
}

function calculateNewPermissions(usergroup, key, value, div)
{
    if (!app.permissions.rules[key]) {
        app.permissions.rules[key] = {};
    }
    if (value === '' && app.permissions.rules[key][usergroup.group]) {
        delete app.permissions.rules[key][usergroup.group];
    } else {
        app.permissions.rules[key][usergroup.group] = value;
    }
    let ind = null,
        actions = getPermissionsActions(div);
    for (ind in app.permissions.rules[key]) {

    }
    if (ind == null) {
        delete app.permissions.rules[key]
    }
    $g.ajax({
        type:"POST",
        dataType:'text',
        data:{
            id: usergroup.id,
            type: usergroup.type,
            actions: actions,
            rules: JSON.stringify(app.permissions.rules)
        },
        url:"index.php?option=com_gridbox&task=gridbox.testNewPermissions",
        success: function(msg){
            app.permissions.groups = JSON.parse(msg);
            setGroupPermissions(usergroup.id, usergroup.type, usergroup.group, div);
        }
    });
}

function getPermissionsActions(div)
{
    let actions = [];
    div.querySelectorAll('.ba-group-element:not([disabled]) .select-permission-action input[data-key]').forEach(function(el){
        actions.push(el.dataset.key);
    });

    return actions.join(', ');
}

function setGroupPermissions(id, type, group, div)
{
    div.querySelectorAll('.permission-action-wrapper .ba-group-element:not([disabled])').forEach(function(el){
        let input = el.querySelector('input[type="hidden"][data-key]'),
            obj = app.permissions,
            key = input.dataset.key,
            value = obj.rules[key] ? ((group in obj.rules[key]) ? obj.rules[key][group] : '') : '',
            text = el.querySelector('li[data-value="'+value+'"]').textContent.trim();
        input.value = value;
        el.querySelector('input[type="text"]').value = text;
        el.querySelectorAll('.calculated-permission').forEach(function(calculated){
            calculated.dataset.status = obj.groups[group][key].status;
            calculated.querySelector('i').className = obj.groups[group][key].icon;
            calculated.querySelector('span.ba-tooltip').textContent = obj.groups[group][key].text;
        });
        input.usergroup = {
            id: id,
            type: type,
            group: group
        }
    });
}

function getPermissions(id, type, $this)
{
    let actions = getPermissionsActions($this);
    app.fetch('index.php?option=com_gridbox&task=gridbox.getPermissions', {
        id: id,
        type: type,
        actions: actions
    }).then(function(text){
        app.permissions = JSON.parse(text);
        app.permissions.id = id;
        app.permissions.type = type;
        let group = 0,
            div = $g($this);
        div.find('.select-permission-usergroup').each(function(){
            let li = this.querySelector('ul li');
            group = li.dataset.value;
            this.querySelector('input[type="hidden"]').value = group;
            this.querySelector('input[type="text"]').value = li.textContent.trim();
            this.usergroup = {
                id: id,
                type: type
            }
        });
        div.find('.permission-action-wrapper').each(function(){
            setGroupPermissions(id, type, group, this);
        });
    });
}

function updatePermissions()
{
    app.fetch("index.php?option=com_gridbox&task=gridbox.updatePermissions", {
        id: app.permissions.id,
        type: app.permissions.type,
        rules: JSON.stringify(app.permissions.rules)
    });
    app.associations.save();
}

function showTagsMoveTo()
{
    let str = '';
    $g('.tags-folders-list li:not([data-id="1"])').each(function(){
        str += '<li><label><i class="zmdi zmdi-folder"></i>';
        str += this.textContent.trim()+'<input type="radio" style="display:none;"';
        str += " name='category_id' value='"+this.dataset.id+"'></label>";
        str += '</li>';
    });
    $g('#move-to-modal .availible-folders ul.root-list').html(str).addClass('ba-move-category');
    $g('.apply-move').removeClass('active-button');
    $g('#move-to-modal').modal();
}

function getProductsHtml(modal, json, type)
{
    let ul = modal.querySelector('ul');
    json.forEach(function(el){
        let li = document.createElement('li'),
            html = '<span class="ba-item-thumbnail"';
        if (el.image) {
            let image = !app.isExternal(el.image) ? JUri+el.image : el.image;
            html += ' style="background-image: url('+image.replace(/\s/g, '%20')+');"';
        }
        html += '>';
        if (!el.image) {
            html += '<i class="zmdi zmdi-'+(type == 'category' ? 'folder' : 'label')+'"></i>';
        }
        html += '</span><span class="picker-item-title"><span class="ba-picker-item-title">'+el.title+'</span>';
        if (type == 'product' && el.info) {
            html += '<span class="ba-picker-item-info">'+el.info+'</span>';
        }
        html += '</span>';
        if (type == 'product') {
            html += '<span class="picker-item-price">'+('sale' in el.prices ? el.prices.sale : el.prices.price)+'</span>';
        }
        li.dataset.value = JSON.stringify(el);
        li.dataset.id = el.id;
        li.innerHTML = html;
        ul.append(li);
    });
}

function getSortingItem(obj)
{
    let item = document.createElement('div'),
        html = '';
    if (obj.type != 'tax') {
        html += '<div class="sorting-icon"><i class="zmdi zmdi-more-vert sortable-handle"></i></div>';
    }
    html += '<div class="sorting-checkbox"><label class="ba-checkbox ba-hide-checkbox">'+
        '<input type="checkbox"><span></span></label></div>';
    html += '<div class="sorting-title"><input type="text" placeholder="'+app._('TITLE')+'"></div>';
    if ('color' in obj) {
        html += '<div class="sorting-color-picker"><div class="minicolors minicolors-theme-bootstrap">'+
            '<input type="text" data-type="color" class="minicolors-input" data-rgba="'+obj.color+
            '"><span class="minicolors-swatch minicolors-trigger"><span class="minicolors-swatch-color" style="background-color: '+
            obj.color+';"></span></span></div></div>';
    }
    if ('image' in obj) {
        html += '<div class="sorting-image-picker" data-image="'+obj.image+
            '" style="--sorting-image: url('+JUri+obj.image.replace(/\s/g, '%20')+')"><i class="zmdi zmdi-camera"></i></div>';
    }
    if (obj.type == 'tax') {
        html += '<div class="sorting-tax-rate"><input type="text" value="'+obj.rate+'" placeholder="%"></div>';
        html += '<div class="sorting-tax-countries-wrapper">';
        html += '<div class="sorting-tax-country">';
        html += '<div class="tax-rates-items-wrapper"></div>';
        html += '<div class="select-items-wrapper add-tax-country-region" data-target="country">';
        html += '<span class="ba-tooltip ba-top ba-hide-element">'+app._('ADD_COUNTRY')+'</span>';
        html += '<i class="zmdi zmdi-globe"></i></div>';
        html += '</div>';
        html += '</div>';
        html += '<div class="sorting-tax-category-wrapper">';
        html += '<div class="tax-rates-items-wrapper"></div>';
        html += '<div class="select-items-wrapper"><span class="ba-tooltip ba-top ba-hide-element">';
        html += app._('ADD_CATEGORY')+'</span>';
        html += '<i class="zmdi zmdi-folder add-tax-category"></i></div>';
        html += '</div>';
        html += '<div class="sorting-more-options-wrapper">';
        html += '<i class="zmdi zmdi-more show-more-tax-options" data-shipping="0"></i>';
        html += '</div>';
    }
    item.className = 'sorting-item';
    item.innerHTML = html;
    item.querySelector('.sorting-title input').value = obj.title;
    item.querySelector('input[type="checkbox"]').dataset.ind = obj.key;
    if (obj.type == 'tax') {
        item.querySelector('.sorting-tax-category-wrapper').style.setProperty('--placeholder-text', "'"+app._('CATEGORY')+"'");
        item.querySelector('.sorting-tax-country').style.setProperty('--placeholder-text', "'"+app._('COUNTRY')+"'");
        app.setTooltip(item);
    }
    
    return item;
}

function checkIframe(modal, view, callback, reload)
{
    let iframe = modal.find('iframe')[0];
    if (iframe.src.indexOf('view='+view) == -1 || reload) {
        iframe.src = 'index.php?option=com_gridbox&view='+view+'&tmpl=component';
        iframe.onload = function(){
            modal.modal();
            if (callback) {
                callback();
            }
        }
    } else {
        modal.modal();
        if (callback) {
            callback();
        }
    }
}

function createSelectedApplies(obj, type)
{
    let html = '<span class="ba-item-thumbnail"',
        span = document.createElement('span');
    span.className = 'selected-applies selected-items';
    span.dataset.id = obj.id;
    if (obj.variation) {
        span.dataset.variation = obj.variation;
    }
    if (obj.image) {
        let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
        html += ' style="background-image: url('+image.replace(/\s/g, '%20')+');"';
    }
    html += '>';
    if (!obj.image) {
        html += '<i class="zmdi zmdi-'+(type == 'category' ? 'folder' : 'label')+'"></i>';
    }
    html += '</span><span class="selected-items-name"><span class="selected-items-title">'+obj.title+'</span>';
    if (obj.info) {
        html += '<span class="selected-items-info">'+obj.info+'</span>';
    }
    html += '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
    span.innerHTML = html;
    document.querySelector('.selected-applies-wrapper').append(span);
}

function prepareCouponApplies(value)
{
    document.querySelectorAll('.ba-options-applies-wrapper, .selected-applies-wrapper').forEach(function(el){
        el.style.display = value == '*' || value == 'cart' ? 'none' : '';
        if (el.classList.contains('ba-options-applies-wrapper')) {
            let btn = el.querySelector('i');
            btn.dataset.modal = value+'-applies-dialog';
            btn.dataset.type = value;
        } else {
            el.innerHTML = '';
        }
    });
    document.querySelectorAll('.discount-cart-total').forEach(function(el){
        el.style.display = value == 'cart' ? '' : 'none';
    });
}

function showAppliesModal(modal)
{
    modal.querySelectorAll('li').forEach(function(li){
        let obj = JSON.parse(li.dataset.value),
            str1 = '.selected-applies[data-id="'+obj.id+'"]'+(obj.variation ? '[data-variation="'+obj.variation+'"]' : ''),
            query = str1+', input[name="product"][value="'+obj.id+'"]'+(obj.variation ? '[data-variation="'+obj.variation+'"]' : ''),
            exist = document.querySelector(query);
        li.classList[exist ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog(modal);
}

function setTabUnderline(a, underline)
{
    let rect = a.getBoundingClientRect(),
        parent = a.closest('.general-tabs').getBoundingClientRect();
    underline.style.left = (Math.floor(rect.left) - Math.floor(parent.left))+'px';
    underline.style.right = (Math.ceil(parent.right) - Math.ceil(rect.right))+'px';
}

function setTabsUnderline()
{
    $g('.general-tabs div.tabs-underline').each(function(){
        let a = this.closest('.general-tabs').querySelector('ul.uploader-nav li.active a');
        a ? setTabUnderline(a, this) : '';
    });
}

function showDataTagsDialog(dialog, margin)
{
    var rect = fontBtn.getBoundingClientRect(),
        modal = $g((typeof dialog == 'object' ? dialog : '#'+dialog)),
        width = modal.innerWidth(),
        height = modal.innerHeight(),
        top = rect.bottom - height / 2 - rect.height / 2,
        offset = 15,
        bottom = '50%';
    if (!margin && margin !== 0) {
        margin = 10;
    }
    if (window.innerHeight - top < height) {
        top = window.innerHeight - height - offset;
        bottom = (window.innerHeight - rect.bottom + rect.height / 2 - offset)+'px';
    } else if (top < 0) {
        top = offset;
        bottom = (height - rect.bottom + rect.height / 2 + offset)+'px';
    }
    modal.css({
        left: rect.left - width - margin,
        top: top
    }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);
}

function getCookie(name)
{
    var matches = document.cookie.match(new RegExp("(?:^|; )"+name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1')+"=([^;]*)"));

    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};
    var expires = options.expires;
    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }
    value = encodeURIComponent(value);
    var updatedCookie = name + "=" + value;
    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }
    document.cookie = updatedCookie;
}

function deleteCookie(name)
{
    setCookie(name, "", {
        expires: -1
    });
}

function disableRangeDates(date, $this)
{
    if ($this.dataset.key == 'to' && $this.range.value != '') {
        return new Date($this.range.value) > new Date(date);
    } else if ($this.dataset.key == 'from' && $this.range.value != '') {
        return new Date($this.range.value) < new Date(date);
    }

    return false;
}

function createCalendar($this)
{
    if ($this.dataset.type == 'range-dates') {
        $this.range = document.querySelector('.open-calendar-dialog[data-type="range-dates"][data-name="'+$this.dataset.link+'"]');
        $this.disableFunc = function(date, cell){
            let flag = this.dataset.productId ? app.booking.calendar.disableMultiple(date, cell, this) : disableRangeDates(date, this);
            
            return flag;
        }
    } else {
        $this.disableFunc = function(date, cell){
            let flag = this.dataset.productId ? app.booking.calendar.disableSingle(date, cell, this) : false;
            
            return flag;
        }
    }
    let btn = $this.nextElementSibling || $this;
    $g(btn).on('click', function(){
        let product_id = this.dataset.productId ? this.dataset.productId : 0;
        gridboxCalendar.show($this, product_id);
    });
    $g($this).on('update', function(event, cell){
        if (this.dataset.type == 'range-dates' && this.dataset.key == 'to' && this.range.value == '') {
            this.range.value = this.value;
            this.value = '';
        }
        if (this.dataset.productId && app.booking.product.booking.type == 'single'
            && app.booking.product.booking.single.time == 'yes') {
            app.booking.calendar.getSingleSlots(this.dataset.value)
        } else if (this.dataset.productId && app.booking.product.booking.single.type == 'group-session') {
            $g('#new-booking-modal input[name="guests"]').each(function(){
                this.value = 1;
                this.max = cell.dataset.guests;
            })
        }  else if (this.dataset.type == 'range-dates' && this.range.value && this.value && this.dataset.action == 'filter') {
            createAjax();
        } else if (this.dataset.type == 'range-dates' && this.range.value && this.value) {
            let params = [];
            if (this.dataset.key == 'from') {
                params.push(this.value);
                params.push(this.range.value);
            } else {
                params.push(this.range.value);
                params.push(this.value);
            }
            $g(this).trigger('dateUpdated', params);
        }
    });
    $this.dataset.created = 'true';
}

function getVisibleBranchClilds(parent)
{
    let childs = parent.find('> ul > li').length;
    parent.find('> ul > li.visible-branch').each(function(){
        childs += getVisibleBranchClilds($g(this));
    });
    parent[0].style.setProperty('--category-childs', childs);

    return childs;
}

function getParentVisibleBranchClilds(el)
{
    let parents = el.parent().parents('li.visible-branch');
    if (parents.length) {
        let parent = parents[parents.length - 1];
        getVisibleBranchClilds($g(parent));
    }
}

function setGravatarDefault(item)
{
    item.previousElementSibling.style.backgroundImage = 'url('+JUri+'components/com_gridbox/assets/images/default-user.png'+')';
}

function showNotice(message, className)
{
    if (!className) {
        className = '';
    }
    if (notification.hasClass('notification-in')) {
        setTimeout(function(){
            notification.removeClass('notification-in').addClass('animation-out');
            setTimeout(function(){
                addNoticeText(message, className);
            }, 400);
        }, 2000);
    } else {
        addNoticeText(message, className);
    }
}

function reloadPage(message, type)
{
    if (submitTask == 'pages.deleteApp') {
        showNotice(message);
        window.location.href = 'index.php?option=com_gridbox'
    } else {
        app.loadPageContent(window.location.href).then(function(){
            loadPage();
            $g('body > .ba-tooltip').remove();
            if (message) {
                showNotice(message, type);
            }
        });
    }
}

app.showNotice = showNotice;

function addNoticeText(message, className)
{
    var time = 3000;
    if (className) {
        time = 6000;
    }
    notification.find('p').html(message);
    notification.addClass(className).removeClass('animation-out').addClass('notification-in');
    setTimeout(function(){
        notification.removeClass('notification-in').addClass('animation-out');
        setTimeout(function(){
            notification.removeClass(className);
        }, 400);
    }, time);
}

app.messageListener = function(){
    if (uploadMode == 'sortingImage') {
        fontBtn.dataset.image = app.messageData[0].path;
        fontBtn.style.setProperty('--sorting-image', 'url('+JUri+app.messageData[0].path.replace(/\s/g, '%20')+')');
        $g('#uploader-modal').modal('hide');
    } else if (uploadMode == 'introImage' || uploadMode == 'invoiceLogo') {
        $g(fontBtn).val(app.messageData[0].path).trigger('change');
        $g('#uploader-modal').modal('hide');
        showNotice(app._('SUCCESS_UPLOAD'));
    } else if (uploadMode == 'ckeImage') {
        $g('.cke-upload-image').val(JUri+app.messageData[0].path);
        $g('#add-cke-image').addClass('active-button');
        $g('#uploader-modal').modal('hide');
    } else if (uploadMode == 'themeImage') {
        $g('.theme-image').val(app.messageData[0].path);
        $g('.theme-apply').addClass('active-button');
        $g('#uploader-modal').modal('hide');
    } else if (uploadMode == 'association') {
        fontBtn.dataset.id = app.messageData.id;
        fontBtn.value = app.messageData.title;
        $g('#pages-list-modal').modal('hide');
    }
}

app.fetch = async function(url, data, isFile){
    let request = await fetch(url, {
            method: 'POST',
            body: app.getFormData(data, isFile)
        }),
        text = await request.text();

    return text;
}

app.getFormData =  function(data, isFile){
    let formData = new FormData();
    if (!data) {
        return formData;
    }

    for (let ind in data) {
        if (Array.isArray(data[ind])) {
            data[ind].forEach(function(v){
                formData.append(ind+'[]', v);
            })
        } else if (!isFile && typeof data[ind] == 'object') {
            for (let i in data[ind]) {
                formData.append(ind+'['+i+']', data[ind][i]);
            }
        } else {
            formData.append(ind, data[ind]);
        }
    }

    return formData;
}

app.checkModule = function(name){
    if (!(name in app)) {
        loadModule(name);
    } else {
        app[name]();
    }
}

function loadModule(name)
{
    var script = document.createElement('script');
    script.type = 'text/javascript';
    if (name == 'photoEditor') {
        script.src = 'components/com_gridbox/assets/js/'+name+'.js';
    } else {
        script.src = JUri+'components/com_gridbox/libraries/modules/'+name+'.js';
    }
    document.head.append(script);
}

function createAjax()
{
    let form = document.getElementById('adminForm'),
        view = $g('[name="ba_view"]').val(),
        src = form.action,
        obj = {
            filter_search: $g('[name="filter_search"]').val(),
            filter_state: $g('[name="filter_state"]').val(),
            filter_order: $g('[name="filter_order"]').val(),
            theme_filter: $g('[name="theme_filter"]').val(),
            author_filter: $g('[name="author_filter"]').val(),
            access_filter: $g('[name="access_filter"]').val(),
            calendar_date: $g('[name="calendar_date"]').val(),
            booking_layout: $g('[name="booking_layout"]').val(),
            booking_view: $g('[name="booking_view"]').val(),
            services: $g('[name="services"]').val(),
            filter_paid: Number($g('[name="filter_paid"]').prop('checked')),
            filter_not_paid: Number($g('[name="filter_not_paid"]').prop('checked')),
            language_filter: $g('[name="language_filter"]').val(),
            filter_order_Dir: $g('[name="filter_order_Dir"]').val(),
            limit: $g('[name="limit"]').val(),
            publish_up: $g('[name="publish_up"]').val(),
            publish_down: $g('[name="publish_down"]').val()
        };
    view = view.split('&');
    obj['view'] = view[0];
    view = '&task=pages.setFilters';
    $g('body > .ba-tooltip').remove();
    $g.ajax({
        type : "POST",
        dataType : 'text',
        url : src+view,
        data : obj,
        success: function(msg){
            src = window.location.href;
            if (window.location.hash) {
                src = src.replace(window.location.hash, '');
            }
            app.loadPageContent(src).then(function(){
                loadPage();
            });
        }
    });
}

function rangeAction(range, callback)
{
    var $this = $g(range),
        max = $this.attr('max') * 1,
        min = $this.attr('min') * 1,
        number = $this.next();
    number.on('input', function(){
        var value = this.value * 1;
        if (max && value > max) {
            this.value = value = max;
        }
        if (min && value < min) {
            value = min;
        }
        $this.val(value);
        setLinearWidth($this);
        callback(number);
    });
    $this.on('input', function(){
        var value = this.value * 1;
        number.val(value).trigger('input');
    });
}

function inputCallback(input)
{
    var callback = input.attr('data-callback');
    if (callback in app) {
        app[callback]();
    }
}

function setLinearWidth(range)
{
    var max = range.attr('max') * 1,
        value = range.val() * 1,
        sx = ((Math.abs(value) * 100) / max) * range.width() / 100,
        linear = range.prev();
    if (value < 0) {
        linear.addClass('ba-mirror-liner');
    } else {
        linear.removeClass('ba-mirror-liner');
    }
    if (linear.hasClass('letter-spacing')) {
        sx = sx / 2;
    }
    linear.width(sx);
}

app.states = {
    addState: function($this){
        if (!$this.clicked) {
            $this.clicked = true;
            makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.addState', {
                id: app.country.obj.id
            }).then(function(json){
                $this.clicked = false;
                app.country.obj.states[json.id] = json;
                app.states.add(json);
            });
        }
    },
    checkShippingResionsCount(){
        let c = Object.keys(app.country.obj.states).length;
        $g('.shipping-countries-list .selected-items[data-id="'+app.country.obj.id+'"] .selected-regions-count').each(function(){
            this.dataset.totalRegions = c;
        });
    },
    add: function(json){
        let content = this.content.cloneNode(true),
            li = content.querySelector('li');
        li.querySelector('.country-title').textContent = json.title;
        li.querySelector('input').value = json.title;
        li.dataset.title = json.title;
        li.dataset.value = json.id;
        this.ul.append(content);
        this.checkShippingResionsCount();
        app.setTooltip(li)
    },
    back: function(){
        app.country.modal.classList.add('visible-country');
    },
    load: function(){
        this.content = app.country.modal.querySelector('template.state-li').content;
        this.ul =  app.country.modal.querySelector('.states-modal-body ul');
        this.header = app.country.modal.querySelector('.states-modal-header');
    },
    show: function(obj){
        this.header.textContent = obj.title;
        this.ul.innerHTML = '';
        for (let ind in obj.states) {
            this.add(obj.states[ind]);
        }
    },
    edit: function(item){
        let li = item.closest('li'),
            input = li.querySelector('input');
        this.obj = app.country.obj.states[li.dataset.value];
        this.ul.classList.add('country-editing');
        input.value = this.obj.title;
        app.country.toggle(li, 'add', 'editing-country');
        input.setSelectionRange(this.obj.title.length, this.obj.title.length);
        input.focus();
    },
    close: function(){
        let adding = app.country.modal.classList.contains('add-region-to-tax');
        this.ul.querySelectorAll('li.editing-country').forEach(function(el){
            app.country.toggle(el, 'remove', 'editing-country'+(adding ? ' prevent-event' : ''));
        });
        this.ul.classList.remove('country-editing');
    },
    save: function(item){
        let li = item.closest('li'),
            adding = app.country.modal.classList.contains('add-region-to-tax');
        this.obj.title = li.querySelector('input').value.trim();
        li.querySelector('.country-title').textContent = this.obj.title;
        makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.updateState', this.obj).then(function(){
            app.country.toggle(li, 'remove', 'editing-country'+(adding ? ' prevent-event' : ''));
            app.states.ul.classList.remove('country-editing');
            $g('.tax-country-state .selected-items[data-id="'+app.states.obj.id+'"]').each(function(){
                this.querySelector('.selected-items-name').textContent = app.states.obj.title;
            });
        });
    },
    deleteState: function(){
        makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.deleteState', this.obj).then(function(){
            app.states.ul.querySelector('li[data-value="'+app.states.obj.id+'"]').remove();
            delete app.country.obj.states[app.states.obj.id];
            $g('.tax-country-state .selected-items[data-id="'+app.states.obj.id+'"] .delete-country-region').trigger('click');
            app.states.checkShippingResionsCount();
        });
    },
    delete: function(item){
        let li = item.closest('li');
        this.obj = app.country.obj.states[li.dataset.value];
        deleteMode = 'state.delete';
        $g('#delete-dialog').modal();
    }
}

app.setCurrentOrder = function(json){
    let price = shipping = promo = '',
        modal = $g('#create-new-order-dialog'),
        taxes = {
            count: 0
        },
        html = '',
        footer = document.querySelector('.view-order-footer-total-wrapper').content.cloneNode(true),
        container = modal.find('.sorting-container').empty(),
        info = modal.find('.customer-info-wrapper .ba-options-group-wrapper').empty();
    modal.find('.modal-header h3').text(app._('ORDER_DETAILS'));
    modal.find('.orders-details-number').text(json.order_number);
    modal.find('.download-exist-order, .edit-order-status').attr('data-id', json.id);
    modal.find('.order-footer-total-wrapper').html(footer);
    $g('.edit-order-status').each(function(){
        let $this = $g(this),
            status = app.statuses[json.status] ? app.statuses[json.status] : app.statuses.undefined;
        this.style.setProperty('--order-status-color', status.color)
        $this.find('.order-status-title').text(status.title);
    });
    app.prepareEmptyCart(modal);
    if (json.user) {
        let search = '.customer-info-fields-pattern[data-type="user"]',
            content = document.querySelector(search).content.cloneNode(true);
        content.querySelectorAll('input').forEach(function(input){
            input.value = json.user.username;
            input.dataset.value = json.user.id;
        });
        content.querySelector('.customer-info-data').textContent = json.user.username;
        content.querySelector('.set-order-user').dataset.value = JSON.stringify(json.user);
        info.append(content);
    }
    json.info.forEach(function(obj){
        if (obj.type != 'headline' && obj.type != 'acceptance') {
            let search = '.customer-info-fields-pattern[data-type="'+obj.type+'"]',
                settings = JSON.parse(obj.options),
                content = document.querySelector(search).content.cloneNode(true),
                infoData = content.querySelector('.customer-info-data'),
                value = obj.value;
            if (value == '') {
                content.querySelector('.ba-options-group-element').classList.add('ba-hide-element');
            }
            if (obj.type == 'checkbox') {
                value = value.replace(/; /g, '<br>');
            }
            content.querySelector('.customer-info-title').textContent = obj.title;
            if (obj.type == 'country' && value) {
                let object = JSON.parse(value);
                infoData.innerHTML = object.country;
                infoData = content.querySelector('.customer-info-data[data-type="region"]');
                object.region != '' ? infoData.innerHTML = object.region : infoData.remove();
            } else {
                infoData.innerHTML = value;
            }
            content.querySelectorAll('[data-type="checkbox"], [data-type="radio"]').forEach(function(div){
                let pattern = div.querySelector('.ba-checkbox-wrapper'),
                    clone = null;
                settings.options.forEach(function(option){
                    clone = pattern.cloneNode(true);
                    clone.querySelector('label + span').textContent = option;
                    clone.querySelector('input').value = option
                    pattern.parentNode.insertBefore(clone, pattern);
                });
                pattern.remove();
            })
            content.querySelectorAll('input, textarea, select').forEach(function(input){
                if (obj.type == 'email') {
                    input.required = true;
                } else if (obj.customer_id == 1) {
                    input.dataset.customer = 1;
                }
                if (obj.type == 'textarea' || obj.type == 'email' || obj.type == 'text') {
                    input.value = obj.value;
                    input.placeholder = settings.placeholder ? settings.placeholder : '';
                } else if (obj.type == 'country') {
                    let select = document.createElement('select'),
                        option = document.createElement('option'),
                        object = value ? JSON.parse(value) : {};
                    select.dataset.type = 'country';
                    input.parentNode.insertBefore(select, input);
                    option.value = '';
                    option.textContent = settings.placeholder;
                    select.append(option);
                    app.countries.forEach(function(country){
                        option = document.createElement('option')
                        option.value = country.id;
                        option.textContent = country.title;
                        select.append(option);
                        if (country.title == object.country) {
                            select.value = country.id;
                            if (country.states.length > 0) {
                                let select2 = document.createElement('select');
                                select2.dataset.type = 'region';
                                input.parentNode.insertBefore(select2, input);
                                country.states.forEach(function(region){
                                    option = document.createElement('option')
                                    option.value = region.id;
                                    option.textContent = region.title;
                                    select2.append(option);
                                    if (region.title == object.region) {
                                        select2.value = region.id;
                                    }
                                });
                            }
                        }
                    });
                } else if (obj.type == 'dropdown') {
                    let option = document.createElement('option');
                    option.value = '';
                    option.textContent = settings.placeholder;
                    input.append(option);
                    settings.options.forEach(function(title){
                        option = document.createElement('option');
                        option.value = title;
                        option.textContent = title;
                        input.append(option);
                    });
                    input.querySelectorAll('option').forEach(function(option){
                        if (option.value == obj.value) {
                            option.selected = true;
                        }
                    })
                } else if (obj.type == 'acceptance') {
                    input.value = obj.value;
                    input.checked = true;
                    content.querySelector('.ba-checkout-acceptance-html').innerHTML = settings.html;
                } else if (obj.type == 'radio' && obj.value == input.value) {
                    input.checked = true;
                } else if (obj.type == 'checkbox') {
                    let values = obj.value.split('; ');
                    values.forEach(function(val){
                        if (val == input.value) {
                            input.checked = true;
                        }
                    })
                }
                input.name = obj.id;
            });
            info.append(content);
        }
    });
    json.products.forEach(function(product){
        let obj = {
                id: product.product_id,
                image: product.image,
                info: product.info,
                price: product.price / product.quantity,
                quantity: product.quantity,
                sale_price: product.sale_price != '' ? product.sale_price / product.quantity : '',
                title: product.title,
                variation: product.variation,
                extra_options: product.extra_options,
                extra: product.extra,
                product_type: product.product_type,
                booking: product.booking,
                renew_id: product.renew_id,
                attachments: product.attachments,
                db_id: product.id
            }
        if (product.tax) {
            let exist = false,
                key = 0;
            price = product.sale_price ? product.sale_price : product.price;
            for (let ind in taxes) {
                if (ind == 'count') {
                    continue;
                }
                if (taxes[ind].title == product.tax_title && taxes[ind].rate == product.tax_rate) {
                    taxes[ind].amount += product.tax * 1;
                    exist = true;
                    break;
                }
                key++;
            }
            if (!exist) {
                taxes.count++;
                taxes[key] = {
                    amount: product.tax * 1,
                    title: product.tax_title,
                    rate: product.tax_rate
                }
            }
        }
        html = app.getProductSortingHTML(obj, obj.quantity, json.currency_symbol, json.currency_position);
        container.append(html);
    });
    if (json.payment && json.payment.type != 'admin') {
        modal.find('.order-payment-method').css('display', '').find('.customer-info-data').text(json.payment.title);
    } else if (json.payment) {
        modal.find('.order-payment-method').hide().find('.customer-info-data').text(json.payment.title);
    }
    if (json.shipping) {
        shipping = json.shipping.title+(json.shipping.carrier ? ' - '+json.shipping.carrier : '');
    }
    if (json.promo) {
        promo = json.promo.title;
    }
    modal.find('.order-shipping-carrier').css('display', 'none');
    modal.find('.order-promo-code').css('display', (promo ? '' : 'none')).find('input').val(promo);
    modal.find('.order-shipping-method .customer-info-data').text(shipping);
    modal.find('.order-coupon-code').removeAttr('data-value');
    price = app.renderPrice(json.subtotal, json.currency_symbol, json.currency_position);
    modal.find('.order-subtotal-element .ba-cart-price-value').text(price);
    modal.find('.order-total-element, .order-shipping-element').attr('data-mode', json.tax_mode);
    if (json.shipping) {
        modal.find('.order-shipping-carrier input').val(json.shipping.carrier);
        price = app.renderPrice(json.shipping.price, json.currency_symbol, json.currency_position);
        modal.find('.order-shipping-element .ba-cart-price-value').text(price);
        price = app.renderPrice(json.shipping.tax, json.currency_symbol, json.currency_position);
        if (json.tax_mode == 'incl') {
            price = app._('INCLUDES')+' '+json.shipping.tax_title+' '+price;
            json.tax = json.tax * 1 + json.shipping.tax * 1;
            if (taxes.count == 1) {
                for (let ind in taxes) {
                    if (ind == 'count') {
                        continue;
                    }
                    if (taxes[ind].title != json.shipping.tax_title || taxes[0].rate != json.shipping.tax_rate) {
                        taxes.count++;
                    }
                }
            }
        }
    } else {
        modal.find('.order-shipping-element, .order-shipping-tax-element').remove();
    }
    if (json.tax_mode == 'incl') {
        modal.find('.order-shipping-tax-element[data-mode="excl"]').remove();
        modal.find('.order-tax-element[data-mode="excl"]').remove();
        modal.find('.order-shipping-tax-element label').text(price);
    } else {
        modal.find('.order-shipping-tax-element[data-mode="incl"]').remove();
        modal.find('.order-tax-element[data-mode="incl"]').remove();
        modal.find('.order-shipping-tax-element .ba-cart-price-value').text(price);
    }
    if (!json.tax || json.tax == '0') {
        modal.find('.order-tax-element').remove();
        modal.find('.order-shipping-tax-element').remove();
    } else if (json.tax_mode == 'incl') {
        price = app.renderPrice(json.tax, json.currency_symbol, json.currency_position);
        modal.find('.order-total-element').each(function(){
            let title = taxes.count == 1 ? app._('INCLUDES')+' '+taxes[0].rate+'% '+taxes[0].title : app._('INCLUDING_TAXES');
            title += ' '+price;
            this.querySelector('.order-tax-element label').textContent = title;
        });
    } else if (taxes.count != 0) {
        let taxElement = modal.find('.order-tax-element').remove(),
            clone = null;
        for (let ind in taxes) {
            if (ind == 'count') {
                continue;
            }
            clone = taxElement.clone();
            clone.find('.ba-options-group-label').text(taxes[ind].title);
            price = app.renderPrice(taxes[ind].amount, json.currency_symbol, json.currency_position);
            clone.find('.ba-cart-price-value').text(price);
            modal.find('.order-total-element').before(clone);
        }
    } else {
        price = app.renderPrice(json.tax, json.currency_symbol, json.currency_position);
        modal.find('.order-tax-element .ba-cart-price-value').text(price)
    }
    price = app.renderPrice(json.total, json.currency_symbol, json.currency_position)
    modal.find('.order-total-element .ba-cart-price-value').text(price);
    if (json.promo) {
        price = app.renderPrice(json.promo.value, json.currency_symbol, json.currency_position);
        modal.find('.order-discount-element .ba-cart-price-value').text(price);
    } else {
        modal.find('.order-discount-element').remove();
    }
    modal.find('.order-shipping-method').each(function(){
        if (json.shipping) {
            this.classList.remove('ba-hide-element')
        } else if (!json.shipping) {
            this.classList.add('ba-hide-element')
        }
    });
    modal.find('.order-promo-code').each(function(){
        if (json.promo) {
            this.classList.remove('ba-hide-element')
        } else if (!json.promo) {
            this.classList.add('ba-hide-element')
        }
    });
    modal.find('.order-payment-method').each(function(){
        if (json.payment && json.payment.type != 'admin') {
            this.classList.remove('ba-hide-element')
        } else if (!json.payment) {
            this.classList.add('ba-hide-element')
        }
    });
    if (json.shipping || json.promo || (json.payment && json.payment.type != 'admin')) {
        modal.find('.order-methods-wrapper').removeClass('ba-hide-element');
    } else {
        modal.find('.order-methods-wrapper').addClass('ba-hide-element');
    }
    
    modal.addClass('view-created-order').modal();
    app.currentOrder = json;
}

app.country = {
    countries: {},
    addCountry: function($this){
        if (!$this.clicked) {
            $this.clicked = true;
            makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.addCountry').then(function(json){
                $this.clicked = false;
                app.country.add(json);
            });
        }
    },
    add: function(json){
        let content = this.content.cloneNode(true),
            li = content.querySelector('li');
        this.countries[json.id] = json;
        li.querySelector('.country-title').textContent = json.title;
        li.querySelector('input').value = json.title;
        li.dataset.title = json.title;
        li.dataset.value = json.id;
        this.ul.append(content);
        app.setTooltip(li);
    },
    toggle: function(el, action, classes){
        classes.split(' ').forEach(function(className){
            if (className) {
                el.classList[action](className);
            }
        })
    },
    getShippingEl:function(id, states){
        let obj = this.countries[id],
            c = Object.keys(obj.states).length,
            count = 0,
            div = document.createElement('div'),
            span = null;
        if (!states) {
            states = {};
            for (let ind in obj.states) {
                states[ind] = true;
            }
        }
        for (let ind in states) {
            if (states[ind]) {
                count++;
            }
        }
        div.innerHTML = '<span class="selected-items" data-id="'+obj.id+'"><span class="selected-items-name">'+obj.title+
            '</span><span data-count="'+count+'" data-total-regions="'+c+'" class="selected-regions-count">'+
            app._('REGIONS')+'</span><i class="zmdi zmdi-close remove-selected-items"></i></span>';
        span = div.querySelector('.selected-items');
        span.dataset.regions = JSON.stringify(states);
        
        return span
    },
    load: function(){
        this.modal = document.querySelector('#store-countries-dialog');
        if (this.modal) {
            this.content = this.modal.querySelector('template.country-li').content;
            this.ul =  this.modal.querySelector('.country-modal-body ul');
            app.states.load();
            makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.getCountries').then(function(json){
                json.forEach(function(obj){
                    app.country.add(obj);
                })
            });
        }
    },
    edit: function(item){
        let li = item.closest('li'),
            input = li.querySelector('input');
        this.obj = this.countries[li.dataset.value];
        this.ul.classList.add('country-editing');
        input.value = this.obj.title;
        this.toggle(li, 'add', 'editing-country prevent-event');
        input.setSelectionRange(this.obj.title.length, this.obj.title.length);
        input.focus();
    },
    close: function(){
        this.ul.querySelectorAll('li.editing-country').forEach(function(el){
            app.country.toggle(el, 'remove', 'editing-country prevent-event');
        });
        this.ul.classList.remove('country-editing');
    },
    save: function(item){
        let li = item.closest('li');
        this.obj.title = li.querySelector('input').value.trim();
        li.querySelector('.country-title').textContent = this.obj.title;
        makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.updateCountry', this.obj).then(function(){
            app.country.toggle(li, 'remove', 'editing-country prevent-event');
            app.country.ul.classList.remove('country-editing');
            $g('.sorting-tax-country, .shipping-countries-list')
                .find('.selected-items[data-id="'+app.country.obj.id+'"]').each(function(){
                this.querySelector('.selected-items-name').textContent = app.country.obj.title;
            });
        });
    },
    show: function(item){
        let li = item.closest('li');
        this.obj = this.countries[li.dataset.value];
        app.states.show(this.obj);
        this.modal.classList.remove('visible-country');
    },
    showModal: function(item){
        fontBtn = item;
        let action = app.country.modal.classList.contains('add-region-to-tax') ? 'removeClass' : 'addClass',
            modal = $g(app.country.modal)[action]('visible-country');
        modal.find('.editing-country span[data-action="close"]').trigger('click');
        modal.find('.picker-search').val('');
        modal.find('li').css('display', '');
        showDataTagsDialog('store-countries-dialog', 0);
    },
    deleteCountry: function(){
        makeFetchRequest('index.php?option=com_gridbox&task=storeSettings.deleteCountry', this.obj).then(function(){
            app.country.ul.querySelector('li[data-value="'+app.country.obj.id+'"]').remove();
            $g('.sorting-tax-country, .shipping-countries-list')
                .find(' .selected-items[data-id="'+app.country.obj.id+'"] .delete-tax-country').trigger('click');
        });
    },
    delete: function(item){
        let li = item.closest('li');
        this.obj = this.countries[li.dataset.value];
        deleteMode = 'country.delete';
        $g('#delete-dialog').modal();
    }
};

app.showSystemSettings = function(obj){
    obj.options = JSON.parse(obj.page_options);
    if (!obj.options.suffix) {
        obj.options.suffix = '';
    }
    let modal = $g('#system-settings-dialog');
    modal.find('.general-tabs > ul .active, .general-tabs > .tab-content > .active').removeClass('active');
    modal.find('li').first().addClass('active');
    modal.find('#general-options').addClass('active');
    modal.find('.page-title').val(obj.title);
    modal.find('.page-alias').val(obj.alias).closest('.ba-options-group').css('display', obj.alias == '' ? 'none' : '');
    modal.find('.theme-select').each(function(){
        this.querySelector('input[type="hidden"]').value = obj.theme;
        this.querySelector('input[type="text"]').value = obj.themeName;
    });
    modal.find('.error-page-settings, .submission-form-options').hide();
    modal.find('.languages-options').css('display', '');
    if (obj.type == '404') {
        modal.find('.page-enable-header').prop('checked', obj.options.enable_header).closest('.error-page-settings').css('display', '');
    } else if (obj.type == 'submission-form') {
        modal.find('.submission-form-options').css('display', '');
        modal.find('.languages-options').hide();
        modal.find('.submission-form-moderation').prop('checked', obj.options.premoderation);
        modal.find('.submission-form-author').prop('checked', obj.options.author);
        modal.find('.submission-form-access input[type="hidden"]').val(obj.options.access);
        $g('#access').val(obj.page_access);
        let value = $g('.submission-form-access li[data-value="'+obj.options.access+'"]').text().trim();
        $g('.submission-form-access input[type="text"]').val(value);
        modal.find('.submission-form-notifications').prop('checked', obj.options.emails);
        modal.find('.submission-form-submited').prop('checked', obj.options.submited_email);
        modal.find('.submission-form-publishing').prop('checked', obj.options.published_email);
        modal.find('.set-group-display').each(function(){
            let action = this.checked ? 'addClass' : 'removeClass';
            $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
        });
    }
    modal.find('.page-class-suffix').val(obj.options.suffix);
    app.associations.prepare(modal, obj.language, obj.id, 'system');
    app.associations.system = obj.type;
    $g('.apply-system-settings').attr('data-id', obj.id).attr('data-type', obj.type);
    modal.modal().attr('data-type', obj.type);
}

app.csv = {
    url: 'index.php?option=com_gridbox&task=',
    templates: {},
    wrappers: {},
    init: function(){
        this.form = document.getElementById("adminForm");
        this.app_id = this.form && this.form.blog ? this.form.blog.value : 0;
        this.modal = $g('#import-export-csv-modal');
        document.querySelectorAll('template.csv-template').forEach(function(template){
            app.csv.templates[template.dataset.key] = template;
        });
        document.querySelectorAll('.csv-content-wrapper').forEach(function(wrapper){
            app.csv.wrappers[wrapper.dataset.key] = wrapper;
        });
        this.setEvents();
    },
    getTemplate: function(key){
        return this.templates[key].content.cloneNode(true);
    },
    setEmptyWrapper: function(key){
        this.wrappers[key].innerHTML = '';
    },
    matchField: function(key, title, str){
        clone = this.getTemplate('csv-match-field');
        clone.querySelector('.ba-options-group-element-title').textContent = title;
        clone.querySelectorAll('select').forEach(function(select){
            select.innerHTML = str;
            select.dataset.key = key;
        });
        this.wrappers['csv-match-fields'].append(clone);
    },
    setStep: function(step){
        let text = step == 3 ? app._('IMPORT') : app._('NEXT');
        this.modal.find('[class*="csv-import-step-"]').css('display', 'none');
        this.modal.find('.csv-import-step-'+step).css('display', '');
        this.modal.find('.apply-csv-import').text(text);
    },
    importCheck: function(json){
        let query = '';
        key = 'csv-import-check'
        clone = app.csv.getTemplate(key);
        app.csv.setEmptyWrapper(key);
        clone.querySelector('[data-type="new"] .csv-import-status-color').textContent = json.new;
        clone.querySelector('[data-type="updated"] .csv-import-status-color').textContent = json.updated;
        clone.querySelector('[data-type="errors"] .csv-import-status-color').textContent = json.errors;
        if (json.errors == 0) {
            clone.querySelector('.csv-import-status-text').remove();
        }
        if (json.new == 0 && json.updated == 0) {
            this.modal.find('.apply-csv-import').removeClass('active-button');
        }
        app.csv.wrappers[key].append(clone);
        key = 'csv-error-log-row';
        app.csv.setEmptyWrapper(key);
        json.log.forEach(function(row){
            clone = app.csv.getTemplate(key);
            for (let ind in row) {
                query = '[data-key="'+ind+'"]'+(ind == 'code' ? ' span' : '');
                clone.querySelector(query).textContent = row[ind];
            }
            app.csv.wrappers[key].append(clone);
        });
    },
    getMatched: function(){
        let matched = {};
        this.wrappers['csv-match-fields'].querySelectorAll('select').forEach(function(select){
            let value = select.value;
            if (value) {
                matched[select.dataset.key] = value;
            }
        });

        return JSON.stringify(matched);
    },
    exportCSV: function(backup){
        return new Promise(function(resolve, reject){
            let array = [],
                pks = backup ? [] : app.csv.pks;
            app.csv.modal.find('.csv-export-field input').each(function(){
                if (this.checked || backup) {
                    array.push(this.value);
                }
            });
            makeFetchRequest(app.csv.url+'exportCSV', {
                id: app.csv.app_id,
                cells: array,
                pks: pks
            }).then(function(json){
                if (json.status) {
                    let iframe = document.createElement('iframe');
                    iframe.src = app.csv.url+'download&file='+json.file;
                    iframe.style.display = 'none';
                    document.body.append(iframe);
                    resolve();
                } else {
                    app.showNotice(json.message, 'ba-alert')
                }
            });
        });
    },
    setEvents: function(){
        $g('.apply-export-csv').on('click', function(event){
            event.preventDefault();
            app.csv.modal.modal('hide');
            app.csv.exportCSV();
        });
        $g('.csv-content-wrapper[data-key="csv-import-check"]').on('click', '.csv-import-status-text', function(){
            $g('#csv-import-error-log-modal').modal();
        });
        $g('.csv-import-options').on('click', '.trigger-csv-import', function(){
            this.closest('div').querySelector('input[type="file"]').click();
        }).on('change', 'input[type="file"]', function(){
            let value = '';
            if (this.files.length != 0) {
                value = this.files[0].name;
                $g('.apply-csv-import').addClass('active-button');
            } else {
                $g('.apply-csv-import').removeClass('active-button');
            }
            app.csv.file = this.files[0];
            $g('.trigger-csv-import').val(value);
        });
        $g('.csv-content-wrapper[data-key="csv-import-options"]').on('change', '.import-property', function(){
            app.csv[this.dataset.key] = this.checked;
        });
        $g('.csv-import-back').on('click', function(){
            event.preventDefault();
            if (this.classList.contains('disabled-button')) {
                return;
            }
            let step = this.steps.pop();
            app.csv.setStep(step);
            $g('.apply-csv-import').addClass('active-button').attr('data-step', step);
            if (step == 1) {
                this.classList.add('disabled-button');
            }
        })
        $g('.apply-csv-import').on('click', function(event){
            event.preventDefault();
            if (!this.classList.contains('active-button')) {
                return;
            }
            $g('.csv-import-back').removeClass('disabled-button')[0].steps.push(this.dataset.step);
            let $this = this;
            if (this.dataset.step == 1) {
                let type = app.csv.modal.find('.csv-file-type').val(),
                    step = type == 'gridbox' ? 3 : 2,
                    url = app.csv.url+(step == 2 ? 'getMatchFields' : 'checkGridboxCsv');
                makeFetchRequest(url, {
                    id: app.csv.app_id,
                    file: app.csv.file,
                    overwrite: Number(app.csv.overwrite)
                }, true).then(function(json){
                    if (step == 2) {
                        let str = '<option value="">'+app._('SELECT')+'</option>';
                        app.csv.setEmptyWrapper('csv-match-fields');
                        json.data.forEach(function(key, i){
                            if (key.trim()) {
                                str += '<option value="'+i+'">'+key+'</option>';
                            }
                        });
                        for (let ind in json.cells) {
                            app.csv.matchField(ind, json.cells[ind], str);
                        }
                        for (let ind in json.fields) {
                            app.csv.matchField(json.fields[ind].id, json.fields[ind].title, str);
                        }
                    } else {
                        app.csv.importCheck(json);
                    }
                    app.csv.setStep(step);
                    $this.dataset.step = step;
                });
            } else if (this.dataset.step == 2) {
                let matched = app.csv.getMatched();
                makeFetchRequest(app.csv.url+'checkMatchedCsv', {
                    id: app.csv.app_id,
                    file: app.csv.file,
                    matched: matched,
                    overwrite: Number(app.csv.overwrite)
                }, true).then(function(json){
                    app.csv.importCheck(json);
                    app.csv.setStep(3);
                    $this.dataset.step = 3;
                })
            } else if (this.dataset.step == 3) {
                if (app.csv.backup && app.csv.controller =='orders') {
                    app.csv.exportCSV(true).then(function(){
                        app.csv.importCSV();
                    });
                } else if (app.csv.backup) {
                    let obj = {
                        id: [app.csv.app_id],
                        type: 'app',
                        menu: false
                    }
                    app.exportXML(obj).then(function(){
                        app.csv.importCSV();
                    });
                } else {
                    app.csv.importCSV();
                }
            }
        })
    },
    importCSV: function(){
        let type = app.csv.modal.find('.csv-file-type').val(),
            matched = type == 'gridbox' ? '{}' : app.csv.getMatched(),
            data = {
                id: app.csv.app_id,
                file: app.csv.file,
                type: type,
                matched: matched,
                overwrite: Number(app.csv.overwrite)
            };
        app.showLoading('LOADING');
        app.csv.modal.modal('hide');
        makeFetchRequest(app.csv.url+'importCSV', data, true).then(function(json){
            setTimeout(function(){
                notification.removeClass('notification-in').addClass('animation-out');
                setTimeout(function(){
                    showNotice(app._('COM_GRIDBOX_N_ITEMS_IMPORTED'));
                    setTimeout(function(){
                        window.location.href = window.location.href;
                    }, 400);
                }, 400);
            }, 2000);
        });
    },
    getPages: function(){
        this.pks = [];
        this.form.querySelectorAll('.select-td input[type="checkbox"]').forEach(function(input){
            if (input.checked) {
                app.csv.pks.push(input.value);
            }
        });
    },
    getExportFields: function(ind, title, array){
        clone = this.getTemplate('csv-export-field');
        clone.querySelectorAll('input').forEach(function(input){
            input.value = ind
            if (array.indexOf(ind) != -1) {
                input.setAttribute('disabled', '');
            }
        });
        clone.querySelector('.csv-export-field-title').textContent = title;
        this.wrappers['csv-export-fields'].append(clone);
    },
    show: function(task){
        this.controller = task.replace('.exportcsv', '');
        this.url = 'index.php?option=com_gridbox&task='+this.controller+'.';
        this.getPages();
        makeFetchRequest(app.csv.url+'getAppCells', {
            id: app.csv.app_id,
            pks: app.csv.pks
        }).then(function(json){
            let clone = null,
                array = ['id', 'category', 'title', 'product_type'];
            app.csv.setEmptyWrapper('csv-export-fields');
            for (let ind in json.cells) {
                app.csv.getExportFields(ind, json.cells[ind], array);
            }
            for (let ind in json.fields) {
                app.csv.getExportFields(json.fields[ind].id, json.fields[ind].title, []);
            }
            if (json.export) {
                for (let ind in json.export) {
                    app.csv.getExportFields(ind, json.export[ind], []);
                }
            }
            app.csv.setEmptyWrapper('csv-import-options');
            clone = app.csv.getTemplate('csv-import-options');
            app.csv.wrappers['csv-import-options'].append(clone);
            $g('.apply-csv-import').removeClass('active-button').attr('data-step', 1);
            $g('.csv-import-back').addClass('disabled-button')[0].steps = [];
            app.csv.overwrite = app.csv.backup = false;
            app.csv.setStep(1);
            app.csv.modal.modal();
        });
    }
}

app.loadPageContent = function(src){
    return new Promise(function(resolve, reject){
        app.fetch(src).then(function(text){
            let boxchecked = document.adminForm.boxchecked,
                div = document.createElement('div');
            div.innerHTML = text;
            document.querySelector('#gridbox-container').innerHTML = div.querySelector('#gridbox-container').innerHTML;
            if (document.querySelector('#post-tags-dialog')) {
                document.querySelector('.post-tags-wrapper').innerHTML = div.querySelector('.post-tags-wrapper').innerHTML;
            }
            if (boxchecked) {
                document.adminForm.boxchecked.replaceWith(boxchecked);
            }
            resolve();
        });
    });
}

app.exportXML = function(obj){
    let data = {
        export_data: JSON.stringify(obj)
    }
    return new Promise(function(resolve, reject) {
        makeFetchRequest('index.php?option=com_gridbox&view=pages&task=pages.exportXML', data).then(function(json){
            if (!json.status) {
                return;
            }
            let iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = 'index.php?option=com_gridbox&view=pages&task=pages.download&file='+json.file;
            document.body.append(iframe);
            resolve();
        });
    });
}

app.appsList = {
    parent_id: 0,
    setDraggable: function(){
        $g('.apps-list-wrapper').find('.gridbox-app-item').on('click', 'a', function(event){
            let folder = this.closest('.gridbox-app-folder');
            if (folder) {
                event.preventDefault();
            }
            if (folder && this.classList.contains('footer-action-view')) {
                let modal = $g('#app-group-modal'),
                    title = folder.querySelector('.gridbox-app-item-header span').textContent.trim();
                app.appsList.parent_id = folder.dataset.id;
                app.fetch('index.php?option=com_gridbox&task=appslist.getGroupApps', {
                    id: folder.dataset.id
                }).then((text) => {
                    modal.find('.group-apps-list-wrapper').html(text);
                    modal.modal().find('.app-group-modal-title').text(title);
                });
            }
        }).find('.gridbox-app-item-body a').each(function(){
            let options = {
                group: 'apps-list',
                parentChange: function(parent, el){
                    let item = el.closest('.gridbox-app-item'),
                        action = item.dataset.type == 'group' ||  el.children.length > 1 ? 'add' : 'remove';
                    item.classList[action]('gridbox-app-folder');
                    item = parent.closest('.gridbox-app-item');
                    action = item.dataset.type == 'group' ||  parent.children.length > 1 ? 'add' : 'remove';
                    item.classList[action]('gridbox-app-folder');
                },
                change: function(item){
                    let parent = item.closest('.gridbox-app-item'),
                        data = {
                            ids: [],
                            parent: parent.dataset.id,
                            type: parent.dataset.type,
                        }
                    parent.querySelectorAll('.gridbox-app-item-icon-wrapper').forEach(function(span){
                        data.ids.push(span.dataset.id);
                    });
                    if (data.ids.length > 1) {
                        app.fetch('index.php?option=com_gridbox&task=appslist.setAppsGroup', data).then((text) => {
                            app.appsList.setApps(text);
                        });
                    }
                }
            }
            $g(this).gridDraggable(options);
        });
    },
    refresh: function(){
        app.fetch('index.php?option=com_gridbox&task=appslist.refreshApps').then((text) => {
            app.appsList.setApps(text);
        });
    },
    refreshSidebar: function(){
        $g('.apps-list-context-menu, .apps-group-childs').remove();
        app.fetch('index.php?option=com_gridbox&task=appslist.refreshSidebar').then((text) => {
            $g('.ba-sidebar').after(text);
            app.sidebar.contextEvents();
        });
    },
    setApps: function(text){
        document.querySelectorAll('.apps-list-wrapper').forEach(($this) => {
            $this.innerHTML = text;
            app.setTooltip($this);
            app.appsList.setDraggable();
        });
        this.refreshSidebar();
    },
    setEvents: function(){
        $g('.apps-list-wrapper').each(function(){
            let options = {
                group: 'apps-list',
                change: function(item){
                    let data = {
                        parent_id: 0,
                        types: [],
                        orders: [],
                        ids: []
                    };
                    item.find('.gridbox-app-item').each(function(){
                        data.orders.push(this.dataset.order * 1);
                        data.ids.push(this.dataset.id);
                        data.types.push(this.dataset.type);
                    });
                    data.orders.sort(function(a, b){
                        return a - b;
                    });
                    app.fetch('index.php?option=com_gridbox&task=appslist.orderApps', data).then((text) => {
                        app.appsList.refreshSidebar();
                    });
                }
            }
            $g(this).gridSorting(options);
        });
        $g('.group-apps-list-wrapper').each(function(){
            let options = {
                group: 'group-apps-list',
                hasDrop: '.app-group-modal-backdrop',
                dropText: app._('DRAG_IT_OUT_UNGROUP'),
                drop: function(item){
                    item.remove();
                    app.fetch('index.php?option=com_gridbox&task=appslist.ungroup', {
                        id: item.querySelector('.gridbox-app-item-icon-wrapper').dataset.id
                    }).then(() => {
                        app.appsList.refresh();
                    });
                },
                change: function(item){
                    let data = {
                        parent_id: app.appsList.parent_id,
                        orders: [],
                        ids: []
                    };
                    item.find('.gridbox-app-item-icon-wrapper').each(function(){
                        data.orders.push(this.dataset.order * 1);
                        data.ids.push(this.dataset.id);
                    });
                    data.orders.sort(function(a, b){
                        return a - b;
                    });
                    app.fetch('index.php?option=com_gridbox&task=appslist.orderApps', data).then(() => {
                        app.appsList.refresh();
                    });
                }
            }
            $g(this).gridSorting(options);
        })
        app.appsList.setDraggable();
    }
}

app.sidebar = {
    contextEvents: function(){
        $g('.sidebar-context-parent').off('click').on('click', function(event){
            if (!this.classList.contains('app-list') && !this.classList.contains('gridbox-store')) {
                event.preventDefault();
                event.stopPropagation();
            }
        }).on('mouseenter', function(event){
            let rect = this.getBoundingClientRect(),
                div = $g('div.'+this.dataset.context),
                h = div.height(),
                y = rect.top - (rect.top + h > window.innerHeight ? h - rect.height : 0) + window.pageYOffset,
                x = rect.right;
            if (div[0].parentNode != document.body) {
                document.body.append(div[0]);
            }
            if (div.hasClass('apps-group-childs')) {
                x = rect.left + this.closest('.ba-context-menu').offsetWidth;
            }
            div.css({
                left: x,
                display: ''
            })[0].style.setProperty('--context-top', y+'px');
        }).on('mouseleave', function(event){
            let target = event.relatedTarget;
            if (!(target && (target.classList.contains(this.dataset.context)
                    || target.closest('.'+this.dataset.context) || target.closest('.apps-group-childs')))) {
                $g('div.'+this.dataset.context).hide();
            }
        });
        $g('div.ba-context-menu[data-source]').on('mouseleave', function(event){
            let target = event.relatedTarget;
            if (!(target && (target.classList.contains(this.dataset.source)
                    || target.closest('.'+this.dataset.source) || target.closest('.apps-group-childs')))) {
                this.style.display = 'none';
                !target.closest('.ba-context-menu') ? $g('.ba-context-menu').hide() : '';
            }
        });
        $g('.default-action').off('mousedown').on('mousedown', function(event){
            if (event.button > 1) {
                return false;
            }
            event.stopPropagation();
            setTimeout(function(){
                $g(this).closest('div.ba-context-menu').hide();
            }, 150);
        });
        $g('.default-action').off('click').on('click', function(){
            if (this.classList.contains('single-post-layout') && this.parentNode.dataset.count == 0) {
                event.preventDefault();
            }
        });
    }
}

app.associations = {
    prepare: function(modal, lang, id, type){
        this.modal = modal;
        this.id = id;
        this.type = type;
        this.setLanguage(lang);
        app.fetch('index.php?option=com_gridbox&task=associations.getLinks', {
            id: this.id,
            type: this.type
        }).then(function(text){
            let data = JSON.parse(text);
            app.associations.set(lang, data);
        });
    },
    setLanguage: function(language){
        this.modal.find('.language-select').each(function(){
            let flag = 'url('+JUri+'components/com_gridbox/assets/images/flags/'+language+'.png)',
                value = this.querySelector('li[data-value="'+language+'"]').textContent.trim();
            this.querySelector('input[type="hidden"]').value = language;
            this.querySelector('input[type="text"]').value = value;
            this.style.setProperty('--flag-img', flag);
            this.dataset.lang = language;
        });
    },
    set: function(lang, data){
        this.modal.find('.language-associations-group').each(function(){
            this.querySelectorAll('.ba-group-element').forEach(function($this){
                $this.classList[$this.dataset.lang == lang ? 'add' : 'remove']('ba-hide-element');
                let input = $this.querySelector('.association-page'),
                    obj = data && data[$this.dataset.lang] ? data[$this.dataset.lang] : null;
                if (obj && app.associations.id != obj.id) {
                    input.value = obj.title;
                    input.dataset.id = obj.id;
                } else {
                    input.value = '';
                    input.removeAttribute('data-id');
                }
            });
            this.querySelectorAll('.ba-group-element:not(.ba-hide-element)').forEach(function($this, i){
                $this.querySelector('label').classList[i != 0 ? 'add' : 'remove']('ba-hide-element');
            });
            this.style.display = lang == '*' ? 'none' : '';
        });
    },
    save: function(){
        let data = {
                id: this.id,
                type: this.type,
                items: [this.id]
            },
            query = '.language-associations-group .association-page';
        if (this.modal.attr('id') == 'system-settings-dialog' && this.modal.attr('data-type') == 'submission-form') {
            query = '#publishing-basic-options '+query;
        }
        this.modal.find(query).each(function(){
            if (this.dataset.id) {
                data.items.push(this.dataset.id)
            }
        });
        app.fetch('index.php?option=com_gridbox&task=associations.saveLinks', data);
    }
}

app.multicategory = {
    start: () => {
        if (!document.querySelector('template.page-multicategory-list')) {
            return;
        }
        let $this = app.multicategory,
            clone = document.querySelector('template.page-multicategory-list').content.cloneNode(true);
        document.body.append(clone);
        $this.ul = document.querySelector('ul.page-multicategory-list');
        $this.li = $this.ul.querySelectorAll('li');
        app.setTooltip($this.ul);
        $g($this.ul).on('change', 'input[type="checkbox"]', function(){
            let li = this.closest('li')
            if (!this.checked && li.classList.contains('default-category')) {
                this.checked = true;
                return;
            }
        }).on('click', '.set-default-page-category', function(){
            let li = this.closest('li')
            if (li.classList.contains('default-category')) {
                return;
            }
            $this.ul.querySelectorAll('li.default-category').forEach((category) => {
                category.classList.remove('default-category');
                category.querySelector('input').checked = false;
            });
            li.classList.add('default-category');
            li.querySelector('input').checked = true;
        });
        $g('body').on('click', '.page-multicategory-select input, .page-multicategory-select > i', function(){
            let ul = $this.ul;
            if (ul.classList.contains('visible-select')) {
                return;
            }
            $this.backdrop = document.createElement('div');
            $this.backdrop.classList.add('page-multicategory-backdrop');
            $this.backdrop.addEventListener('click', $this.hide);
            document.body.append($this.backdrop);
            ul.classList.add('visible-select');
        })
    },
    hide: () => {
        let data = {
            category: {
                id: 0,
                title: ''
            },
            id : [],
            title: []
        };
        app.multicategory.ul.querySelectorAll('input[type="checkbox"]').forEach((input) => {
            let li = input.closest('li');
            if (li.classList.contains('default-category')) {
                data.category.id = li.dataset.value;
                data.category.title = li.querySelector('.multicategory-title').textContent.trim();
            } else if (input.checked) {
                data.id.push(li.dataset.value);
                data.title.push(li.querySelector('.multicategory-title').textContent.trim());
            }
        });
        data.title.unshift(data.category.title);
        $g('.page-multicategory-select input').each(function(){
            this.value = this.type == 'text' ? data.title.join(', ') : (this.name == 'page_category' ? data.category.id : data.id.join(','));
        });
        app.multicategory.ul.classList.remove('visible-select');
        app.multicategory.backdrop.remove();
    },
    setActive: (id, isDefault) => {
        let title = '';
        app.multicategory.li.forEach((li) => {
            if (li.dataset.value == id) {
                li.querySelector('input[type="checkbox"]').checked = true;
                li.classList[isDefault ? 'add' : 'remove']('default-category');
                title = li.querySelector('.multicategory-title').textContent.trim();
            }
        });

        return title;
    },
    set: (category, categories) => {
        app.multicategory.li.forEach((li) => {
            li.querySelector('input[type="checkbox"]').checked = false;
            li.classList.remove('default-category');
        });
        let array = [];
        array.push(app.multicategory.setActive(category, true));
        categories.forEach((obj) => {
            array.push(app.multicategory.setActive(obj.category_id, false));
        });

        return array.join(', ');
    }
}

app.booking = {
    getAttachmentHTML: (file) => {
        let str = '',
            attachment = document.createElement('div'),
            isImage = app.isImage(file.name);
        attachment.className = 'extra-attachment-file';
        if (isImage) {
            str += '<span class="post-intro-image"></span>';
        } else {
            str += '<i class="zmdi zmdi-attachment"></i>';
        }
        str += '<span class="attachment-title">'+file.name;
        str += '</span><span class="attachment-progress-bar-wrapper"><span class="attachment-progress-bar">';
        str += '</span></span><i class="zmdi zmdi-delete remove-attachment-file"></i>';
        attachment.innerHTML = str;
        if (isImage && !file.attachment_id) {
            let reader = new FileReader();
            reader.onloadend = function() {
                attachment.querySelectorAll('.post-intro-image').forEach((img) => {
                    img.style.backgroundImage = 'url('+reader.result+')';
                    img.dataset.image = reader.result;
                });
            }
            reader.readAsDataURL(file);
        } else if (isImage) {
            attachment.querySelectorAll('.post-intro-image').forEach((img) => {
                img.style.backgroundImage = 'url('+JUri+'components/com_gridbox/assets/uploads/attachments/'+file.filename+')';
                img.dataset.image = JUri+'components/com_gridbox/assets/uploads/attachments/'+file.filename;
            });
        }

        return attachment;
    },
    uploadAttachmentFile: function(files, $this, option_id, product_id){
        if (files.length == 0) {
            $this.dataset.uploading == '';
            $g($this).trigger('uploaded');
            return;
        }
        let file = files.shift(),
            container = $g($this).closest('.extra-options-details').find('.extra-attached-files'),
            attachment = app.booking.getAttachmentHTML(file),
            xhr = new XMLHttpRequest(),
            formData = new FormData();
        container.append(attachment);
        formData.append('file', file);
        formData.append('id', product_id);
        formData.append('option_id', option_id);
        formData.append('is_admin', 1);
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
            app.booking.uploadAttachmentFile(files, $this, option_id, product_id);
            setTimeout(function(){
                attachment.classList.add('attachment-file-uploaded');
            }, 300);
        };
        xhr.open("POST", JUri+"index.php?option=com_gridbox&task=store.uploadAttachmentFile", true);
        xhr.send(formData);
    },
    calendar: {
        blocked: {},
        addDays: (date, days) => {
            let result = new Date(date);
            result.setDate(result.getDate() + days * 1);

          return result;
        },
        getSingleSlots: (date) => {
            return new Promise((resolve, reject) => {
                app.fetch(JUri+'index.php?option=com_gridbox&task=calendar.getSingleSlots', {
                    id: app.booking.product.product_id,
                    date: date
                }).then((text) => {
                    app.booking.product.times = JSON.parse(text);
                    app.booking.calendar.drawSlots();
                    resolve();
                })
            });
        },
        drawSlots: () => {
            let select = $g('#new-booking-modal select[name="start_time"]');
            select.find('option').not('option[value=""]').remove();
            app.booking.product.times.forEach((obj, i) => {
                select.append('<option value="'+i+'">'+obj.start+'</option>');
            })
        },
        isBlocked: (first, date) => {
            flag = false;
            for (let blocked in app.booking.calendar.blocked) {
                if (first < date && date > blocked && first < blocked) {
                    flag = true;
                    break;
                }
            }

            return flag
        },
        disableMultiple: function(date, cell, $this){
            let flag = cell.dataset.blocked == 1,
                calendar = app.booking.calendar,
                multiple = app.booking.product.booking.multiple;

            if (cell.dataset.blocked == 1) {
                calendar.blocked[date] = true;
            }
            if (gridboxCalendar.multiple.length == 1) {
                let min = multiple.min ? multiple.min * 1 : 0,
                    first = gridboxCalendar.multiple[0].dataset.date,
                    max = multiple.max ? calendar.addDays(first, multiple.max * 1) < new Date(date) : false;
                flag = flag || calendar.isBlocked(first, date) || first > date || calendar.addDays(first, min) > new Date(date) || max;
            } else if (gridboxCalendar.multiple.length == 0) {
                flag = flag || ($this.dataset.now > date) || ($this.dataset.early ? $this.dataset.early < date : false);
            }

            return flag;
        },
        disableSingle: function(date, cell, $this){
            let single = app.booking.product.booking.single,
                slot = single.time == 'yes' ? cell.dataset.slots * 1 == 0 : cell.dataset.blocked == 1;

            return ($this.dataset.now > date) || ($this.dataset.early ? $this.dataset.early < date : false) || slot;
        }
    },
    setPaid: (id, status) => {
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.setPaid', {
            id: id,
            status: status
        }).then((text) => {
            app.booking.getDetails(id);
            $g('.ba-booking-item-image[data-id="'+id+'"] .ba-booking-item-badge[data-paid]').attr('data-paid', status);
            reloadPage();
        });
    },
    delete: (id) => {
        return new Promise(function(resolve, reject){
            app.fetch('index.php?option=com_gridbox&task=bookingcalendar.deleteBooking', {
                id: id
            }).then((text) => {
                reloadPage(app._('COM_GRIDBOX_N_ITEMS_DELETED'));
                $g('.ba-booking-item[data-id="'+id+'"]').remove();
                resolve();
            })
        });
    },
    setDetails: (template, wrapper, title, info, type, price) => {
        let clone = template.content.cloneNode(true);
        clone.querySelector('.booking-details').dataset.detail = type;
        clone.querySelector('.booking-details-label').textContent = title;
        if (type == 'extra_options') {
            let div = clone.querySelector('.booking-details-info');
            div.remove();
            for (let ind in info.values) {
                let obj = info.values[ind],
                    clone2 = div.cloneNode(true);
                clone2.innerHTML = '<span>'+obj.value+'</span><span>'+app.renderPrice(obj.price)+'</span>';
                clone.querySelector('.booking-details').append(clone2)
            }
            if (info.attachments) {
                let str = '<div class="ba-product-attachments">';
                info.attachments.forEach((file) => {
                    let src = JUri+'components/com_gridbox/assets/uploads/attachments/'+file.filename;
                    str += '<div class="ba-product-attachment" data-id="'+file.id+'">';
                    if (app.isImage(file.name)) {
                        str += '<span class="attachment-image" data-img="'+src+'" ';
                        str += 'style="background-image: url('+src+')"></span>';
                    } else {
                        str += '<i class="zmdi zmdi-attachment-alt"></i>';
                    }
                    str += '<span class="attachment-title">'+file.name+'</span>';
                    str += '<a class="zmdi zmdi-download" download="'+file.name+'" href="'+src+'"></a>';
                    str += '</div>';
                });
                str += '</div>';
                clone.querySelector('.booking-details').insertAdjacentHTML('beforeend', str)
            }
        } else if (price) {
            clone.querySelector('.booking-details-info').innerHTML = '<span>'+info+'</span><span>'+price+'</span>';
        } else {
            clone.querySelector('.booking-details-info').textContent = info;
        }
        wrapper.append(clone);
    },
    setStatus: (id) => {
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.setStatus', {
            id: id
        })
    },
    viewMonthlyItems: (div) => {
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getMonthlyItems', {
            id: div.dataset.product,
            date: div.dataset.date,
            time: div.dataset.time ? div.dataset.time : ''
        }).then((text) => {
            let obj = JSON.parse(text),
                str = title = '',
                modal = $g('#monthly-product-booking-details-modal');
            modal.find('.monthly-product-date').text(obj.date);
            modal.find('.monthly-product-year').text(obj.year);
            obj.items.forEach((item) => {
                str += '<div class="monthly-product-item" data-id="'+item.id+'">'+
                    (item.start_time ? item.start_time+' - '+item.end_time : obj.start_date)+'</div>';
                title = item.title;
            });
            modal.find('h3').text(title);
            modal.find('.monthly-products-wrapper').html(str);
            modal.modal();
        });
    },
    editBooking: (id) => {
        app.booking.getBookingDetails(id, 1).then((data) => {
            data.booking = JSON.parse(data.booking);
            let modal = $g('#new-booking-modal'),
                extra_options = JSON.parse(data.extra_options);
            modal.find('.modal-header h3').text(app._('EDIT'));
            app.booking.old_product = data.product_id;
            data.product_id = data.item_id;
            app.booking.product = data;
            modal.find('input, select').each(function(){
                if (this.type == 'checkbox' || this.type == 'radio') {
                    this.checked = false;
                } else {
                    this.value = '';
                }
                app.removeAlertTooltip(this);
            });
            modal.find('input[name="service"]').each(function(){
                this.value = data.title;
                this.dataset.value = JSON.stringify(data.item);
                this.dataset.id = data.product_id;
            }).trigger('change');
            modal.find('.new-booking-details.extra-options-details [name]').each(function(){
                let option = extra_options.items[this.name];
                if (!option) {
                    return;
                }
                if ((this.type == 'checkbox' || this.type == 'radio') && option.values[this.value]) {
                    this.checked = true;
                } else if (this.type == 'text' && this.closest('.extra-options-details').dataset.type != 'file') {
                    this.value = option.values[0].value;
                } else if (this.type == 'text') {
                    let container = this.closest('.extra-options-details').querySelector('.extra-attached-files');                    
                    option.attachments.forEach((file) => {
                        let attachment = app.booking.getAttachmentHTML(file);
                        attachment.dataset.id = file.id;
                        attachment.dataset.attachment = file.attachment_id;
                        attachment.classList.add('attachment-file-uploaded');
                        container.append(attachment);
                    });
                }
            });
            modal.find('.open-calendar-dialog').each(function(){
                this.dataset.productId = data.product_id;
                this.value = data[this.name];
                this.dataset.value = data.dates[this.name];
            });
            if (data.booking.type == 'single' && data.booking.single.time == 'yes') {
                app.booking.calendar.getSingleSlots(data.dates.start_date).then(() => {
                    let exists = false;
                    if (data.booking.single.type == 'group-session') {
                        app.booking.product.times.forEach((time) => {
                            if (time.start != data.start_time) {
                                return;
                            }
                            exists = true;
                            time.guests += data.guests * 1;
                            modal.find('input[name="guests"]')[0].max = time.guests;
                        })
                    }
                    if (!exists) {
                        let time = {
                            start: data.start_time,
                            end: data.end_time
                        }
                        if (data.booking.single.type == 'group-session') {
                            time.guests = data.guests
                            modal.find('input[name="guests"]')[0].max = time.guests;
                        }
                        app.booking.product.times.push(time);
                        app.booking.product.times.sort((slot1, slot2) => slot1.start > slot2.start ? 1 : -1);
                    }
                    app.booking.calendar.drawSlots();
                    app.booking.product.times.forEach((slot, i) => {
                        if (slot.start != data.start_time) {
                            return;
                        }
                        modal[0].querySelector('select[name="start_time"]').value = i;
                    });
                });
            } else if (data.booking.single.type == 'group-session') {
                app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getGroupSessionGuest', {
                    id: app.booking.product.product_id,
                    date: data.dates.start_date
                }).then((guests) => {
                    console.info(guests)
                    modal.find('input[name="guests"]')[0].max = guests * 1 + data.guests * 1;
                });
            }
            modal.find('input[name="guests"]').val(data.guests);
            modal.find('input[name="user"]').each(function(){
                this.value = data.user ? data.user.username : '';
                this.dataset.id = data.user ? data.user.id : 0;
            });
            data.info.forEach((info) => {
                modal.find('.new-booking-details:not(.extra-options-details) [name="'+info.customer_id+'"]').each(function(){
                    this.value = info.value;
                })
            })
            modal.modal();
        });
    },
    getBookingDetails: (id,  edit = 0) => {
        return new Promise((resolve, reject) => {
            app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getBookingDetails', {
                id: id,
                edit: edit
            }).then((text) => {
                let data = JSON.parse(text);
                resolve(data)
            });
        })
    },
    showNewBookingModal: () => {
        let modal = $g('#new-booking-modal');
        modal.find('.modal-header h3').text(app._('NEW_BOOKING'));
        modal.find('.service-price, .extra-options-details').remove();
        modal.find('input, select').each(function(){
            if (this.type == 'checkbox' || this.type == 'radio') {
                this.checked = false;
            } else if (this.name == 'guests') {
                this.value = 1;
            } else {
                this.value = '';
            }
            app.removeAlertTooltip(this);
        })
        modal.find('[data-type="multiple"], [data-type="single"]').addClass('ba-hide-element');
        modal.modal();
    },
    getDetails: (id) => {
        app.booking.getBookingDetails(id).then((data) => {
            let modal = $g('#booking-details-modal'),
                template = document.querySelector('template.booking-details'),
                extra_options = JSON.parse(data.extra_options),
                wrapper = document.querySelector('.booking-details-wrapper');
            app.booking.old_product = 0;
            wrapper.innerHTML = '';
            wrapper.dataset.type = data.end_date != '' ? 'multiple' : 'single';
            app.booking.setDetails(template, wrapper, app._('SERVICE'), data.title, 'service', app.renderPrice(data.price));
            if (extra_options.items) {
                for (let ind in extra_options.items) {
                    let item = extra_options.items[ind];
                    app.booking.setDetails(template, wrapper, item.title, item, 'extra_options');
                }
            }
            if (data.end_date != '') {
                let div = document.createElement('div');
                div.className = 'booking-multiple-details';
                wrapper.append(div);
                app.booking.setDetails(template, div, app._('CHECK_IN'), data.start_date, 'start_date');
                app.booking.setDetails(template, div, app._('CHECK_OUT'), data.end_date, 'end_date');
            } else {
                let div = document.createElement('div');
                div.className = 'booking-single-details';
                wrapper.append(div);
                app.booking.setDetails(template, div, app._('DATE'), data.start_date, 'start_date');
                if (data.start_time != '') {
                    app.booking.setDetails(template, div, app._('TIME'), data.start_time, 'time');
                }
                if (data.guests != '') {
                    app.booking.setDetails(template, div, app._('GUESTS'), data.guests, 'guests');
                }
            }
            if (data.user) {
                app.booking.setDetails(template, wrapper, app._('USER'), data.user.username, 'user');
            }
            data.info.forEach((info) => {
                if (info.type == 'headline' || info.value == '' || info.type == 'acceptance' || (info.type == 'country' && info.value == '')) {
                    return;
                }
                if (info.type == 'country') {
                    let object = JSON.parse(info.value);
                    info.value = object.country+(object.region ? (', '+object.region) : '')
                }
                if (info.value == '') {
                    return;
                }
                app.booking.setDetails(template, wrapper, info.title, info.value, 'info');
            });
            document.querySelectorAll('.booking-details-footer-row[data-type]').forEach((row) => {
                if (row.dataset.type == 'left' &&
                    (!data.payment || data.payment.type == 'offline' || data.payment.type == 'admin' || data.paid == 1)) {
                    row.style.display = 'none';
                } else if (row.dataset.type == 'left') {
                    row.style.display = '';
                    row.querySelector('.ba-booking-price').textContent = app.renderPrice(data.later);
                } else if (!data.payment || data.payment.type == 'offline' || data.payment.type == 'admin' || data.paid == 1) {
                    row.querySelector('.booking-details-payment').textContent = !data.payment || data.payment.type == 'admin' ? 'Manual Payment'
                        : data.payment.title;
                    row.querySelector('.ba-booking-price').textContent = app.renderPrice(data.price, data.symbol, data.position);
                } else {
                    row.querySelector('.booking-details-payment').textContent = app._('ONLINE_PREPAYMENT');
                    row.querySelector('.ba-booking-price').textContent = app.renderPrice(data.prepaid, data.symbol, data.position);
                }
            });
            document.querySelectorAll('#booking-details-modal .ba-booking-item-badge').forEach((badge) => {
                badge.dataset.paid = data.paid;
                badge.textContent = data.paid == 1 ? app._('PAID') : app._('NOT_PAID');
            })
            document.querySelector('.mark-booking-as-paid').style.display = data.paid == 1 ? 'none' : '';
            document.querySelector('.mark-booking-as-unpaid').style.display = data.paid == 0 ? 'none' : '';
            modal.attr('data-id', id).modal();
            if (!modal.hasClass('in')) {
                modal.modal();
            }
        })
    },
    block: {
        edit: (id) => {
            app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getBlockDetails', {
                id: id
            }).then((text) => {
                let obj = JSON.parse(text);
                app.booking.block.show(obj);
            })
        },
        delete: (id) => {
            app.fetch('index.php?option=com_gridbox&task=bookingcalendar.deleteBlock', {
                id: id
            }).then((text) => {
                reloadPage(app._('COM_GRIDBOX_N_ITEMS_DELETED'));
            })
        },
        show: (data) => {
            if (!app.booking.block.modal) {
                app.booking.block.modal = $g('#block-time-modal');
            }
            let modal = app.booking.block.modal;
            modal.find('input').val('').attr('data-value', '');
            modal.find('select, input').each(function(){
                let key = this.name.replace('_date', '_formated');
                this.value = data && data[key] ? data[key] : '';
                this.dataset.value = data && data[this.name] ? data[this.name] : '';
            });
            modal.find('#apply-block-time')[0].dataset.id = data ? data.id : 0;
            app.booking.block.check(data);
            setTimeout(() => {
                modal.modal();
            }, 300);
        },
        check: (data) => {
            let flag = false,
                modal = app.booking.block.modal;
            modal.find('input, select').each(function(){
                flag = !this.value || flag
            });
            modal.find('#apply-block-time')[flag ? 'removeClass' : 'addClass']('active-button');
        },
        send: (id) => {
            let data = {
                    id: id
                },
                modal = app.booking.block.modal;
            modal.find('input, select').each(function(){
                data[this.name] = this.localName == 'select' ? this.value : this.dataset.value;
            });
            app.fetch('index.php?option=com_gridbox&task=bookingcalendar.setBlockTime', data).then((text) => {
                reloadPage();
            });
            modal.modal('hide');
        }
    }
}

document.addEventListener('DOMContentLoaded', function(){
    window.$g = jQuery;
    window.notification = $g('#ba-notification');
    app.notification = window.notification;
    renderGridSorting($g);

    sortableInd = $g('.category-list ul.root-list .ba-category').length + 1;

    $g('.ba-checkbox.filter-services').on('change', () => {
        let data = []
        document.querySelectorAll('.ba-checkbox.filter-services input').forEach(function(input){
            if (!input.checked) {
                data.push(input.closest('li').dataset.id);
            }
        });
        document.querySelector('input[name="services"]').value = data.join(',');
        createAjax();
    });

    $g('#booking-calendar-new-bookings').on('mouseenter', '.ba-booking-item[data-unread="1"]', function(){
        app.booking.setStatus(this.dataset.id);
        this.dataset.unread = 0;
        this.querySelector('.ba-booking-item-badge[data-status]').remove();
        document.querySelectorAll('.about-notifications-count').forEach((div) => {
            div.dataset.count = div.dataset.count * 1 - 1;
            div.textContent = div.textContent * 1 - 1;
        });
        $g('.unread-comments-count[data-type="booking"], .gridbox-store[data-context="store-context-menu"] .unread-comments-count').each(function(){
            let count = this.textContent - 1;
            if (count) {
                this.textContent = count;
            } else {
                this.remove();
            }
        });
    })

    $g('.ba-checkbox.trigger-paid-filters').on('change', () => {
        createAjax();
    });

    $g('body').on('click', '.ba-booking-item', function(){
        app.booking.getDetails(this.dataset.id);
    });
    $g('body').on('click', '.ba-booking-pagination span', function(){
        if (this.loading) {
            return;
        }
        this.loading = true;
        const wrapper = this.closest('.tab-pane');
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getAppointments', {
            next: this.dataset.next,
            type: this.dataset.type
        }).then((text) => {
            wrapper.querySelector('.ba-booking-pagination').remove();
            wrapper.insertAdjacentHTML('beforeend', text);
        })
    });

    $g('body').on('click', '.trigger-context-menu', function(event){
        currentContext = $g(this);
        showContext(event, $g('.'+this.dataset.context));
    });

    $g('body').on('show-gridbox-modal', '.modal', function(){
        let body = document.body;
        if (!body.classList.contains('modal-wrapper-opened')) {
            let width = window.innerWidth - document.documentElement.clientWidth
            body.style.setProperty('--body-scroll-width', width+'px');
            body.classList.add('modal-wrapper-opened');
        }
    }).on('hidden-gridbox-modal', '.modal', function(){
        let body = document.body;
        clearTimeout(body.delay);
        body.delay = setTimeout(function(){
            if (!body.querySelector('.modal.in')) {
                body.classList.remove('modal-wrapper-opened');
                body.style.removeProperty('--body-scroll-width');
            }
        }, 500);
    });

    $g('.add-tracking-number').on('click', function(){
        let modal = $g('#tracking-number-modal');
        modal.find('.tracking-number-input').val(app.currentOrder.tracking.number);
        modal.find('.tracking-url-input').val(app.currentOrder.tracking.url);
        modal.find('.tracking-title-input').val(app.currentOrder.tracking.title);
        modal.modal();
    });

    $g('.apply-tracking-number').on('click', function(event){
        event.preventDefault();
        let data = app.currentOrder.tracking;
        data.number = document.querySelector('.tracking-number-input').value;
        data.url = document.querySelector('.tracking-url-input').value;
        data.title = document.querySelector('.tracking-title-input').value;
        makeFetchRequest('index.php?option=com_gridbox&task=orders.setTracking', data).then(function(json){
            app.currentOrder.tracking = json;
        });
        $g('#tracking-number-modal').modal('hide');
    });

    app.csv.init();

    if (!('Joomla' in window)) {
        window.Joomla = {};
    }
    Joomla.submitbutton = function(task) {
        let form = document.getElementById("adminForm");
        if (task == 'pages.export') {
            exportId = [];
            $g('.table-striped tbody tr').find('input[type="checkbox"]').each(function(){
                if (this.checked) {
                    var id = this.value;
                    exportId.push(id);
                }
            });
            $g('li.export-apps').hide();
            $g('#export-dialog').modal();
            $g('.apply-export').attr('data-export', 'pages');
        } else if (task == 'apps.export') {
            $g('li.export-apps').hide();
            $g('#export-dialog').modal();
            $g('.apply-export').attr('data-export', 'app');
        } else if (task == 'apps.exportcsv' || task == 'orders.exportcsv') {
            app.csv.show(task);
        } else if (task == 'apps.deleteApp') {
            deleteMode = 'pages.deleteApp';
            $g('#delete-dialog').modal();
        } else if (task == 'pages.settings') {
            var options = [],
                obj = tr = null;
            $g('.table-striped tbody input[type="checkbox"]').each(function(){
                if ($g(this).prop('checked')) {
                    tr = this.closest('tr');
                    obj = this.closest('td').querySelector('input[type="hidden"]').value;
                    options.push('option');
                }
            });
            if (options.length != 1) {
                alert($g('.jlib-selection').val());
                return false;
            }
            obj = JSON.parse(obj);
            showPageSettings(obj, tr);
        } else if (task == 'system.settings') {
            var options = [],
                obj = tr = null;
            $g('.table-striped tbody input[type="checkbox"]').each(function(){
                if ($g(this).prop('checked')) {
                    tr = this.closest('tr');
                    obj = this.closest('td').querySelector('input[type="hidden"]').value;
                    options.push('option');
                }
            });
            if (options.length != 1) {
                alert($g('.jlib-selection').val());
                return false;
            }
            obj = JSON.parse(obj);
            app.showSystemSettings(obj);
        } else if (task == 'themes.delete') {
            var def = 0;
            $g('#installed-themes-view label').each(function(){
                if ($g(this).find('input[type="checkbox"]').prop('checked')) {
                    def = $g(this).find('p').attr('data-default');
                    if (def == 1) {
                        return false;
                    }
                }
            });
            if (def == 1) {
                $g('#default-message-dialog').modal();
            } else {
                deleteMode = 'array';
                $g('#delete-dialog').modal();
            }
            return false;
        } else if (task == 'apps.addTrash' || task == 'pages.addTrash' || task == 'system.addTrash' || task == 'tags.delete' || task == 'orders.delete'
            || task == 'paymentmethods.delete' || task == 'shipping.delete' || task == 'promocodes.delete'
            || task == 'productoptions.delete' || task == 'subscriptions.delete' || task == 'system.delete') {
            deleteMode = task;
            $g('#delete-dialog').modal();
        } else if (task == 'tags.moveTo') {
            moveTo = task;
            showTagsMoveTo();
        } else if (task == 'apps.moveTo') {
            moveTo = task;
            showMoveTo();
        } else if (task == 'trashed.delete') {
            let types = [];
            $g('input[name="cid[]"]').each(function(){
                if (this.checked) {
                    types.push(this.closest('tr').dataset.type);
                }
            });
            $g('input[name="types"]').val(types.join(', '));
            Joomla.submitform(task);
        } else {
            Joomla.submitform(task);
        }
    }
    Joomla.submitform = function(task) {
        if (task == 'apps.duplicate') {
            app.showLoading('LOADING');
        }
        $g('.status-td i').trigger('mouseleave');
        var form = document.getElementById("adminForm"),
            obj = {
                cid: [],
                meta_tags: []
            },
            src = form.action;
        if (!task) {
            form.submit();
            return false;
        }
        $g(form).find('[name]').not('[name="cid[]"]').not('[name="meta_tags[]"]').each(function(){
            if (this.name == 'task') {
                obj['task'] = task;
            } else if ((this.type == 'radio' || this.type == 'checkbox') && this.checked) {
                obj[this.name] = this.value;
            } else if (this.type != 'radio' && this.type != 'checkbox') {
                obj[this.name] = this.value;
            }
            if (this.name == 'intro_image') {
                obj[this.name] = this.dataset.value ? this.dataset.value : this.value;
            }
        });
        obj.cid = [];
        $g('[name="cid[]"]').each(function(){
            if (this.checked) {
                obj.cid.push(this.value);
            }
        });
        if (task == 'tags.moveTo') {
            $g('#move-to-modal input[name="category_id"]').each(function(){
                if (this.checked) {
                    obj.category_id = this.value;
                }
            });
        }
        obj.meta_tags = [];
        $g('[name="meta_tags[]"] option').each(function(){
            obj.meta_tags.push(this.value);
        });
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : src,
            data : obj,
            error: function(msg){
                console.info(msg.responseText);
            },
            success: function(msg){
                if (task == 'apps.addCategory') {
                    var obj = JSON.parse(msg);
                    if ($g('li.root li.active').length > 0) {
                        var blog = $g('input[name="blog"]').val(),
                            category = $g('li.root  li.active')[0].dataset.id;
                            setCookie('blog'+blog+'id'+category, 1);
                    }
                    src = JUri+'administrator/index.php?option=com_gridbox&view=apps&id=';
                    src += form.blog.value+'&category='+obj.id;
                    form.action = src;
                    window.history.pushState(null, null, src);
                    app.loadPageContent(src).then(function(){
                        loadPage();
                        showNotice(obj.msg, '');
                    });
                } else if (task == 'tags.deleteTagsFolder') {
                    src = JUri+'administrator/index.php?option=com_gridbox&view=tags';
                    window.history.pushState(null, null, src);
                    app.loadPageContent(src).then(function(){
                        loadPage();
                        showNotice(msg, '');
                    });
                } else {
                    reloadPage(msg);
                }
            }
        });
    }
    setInterval(function(){
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task=gridbox.getSession&tmpl=component",
            success : function(msg){
            }
        });
    }, 600000);
    function getAuthorPatern(ind)
    {
        var label = authorSocial[app.authorsSocial[ind].title].label,
            str = '<span class="authors-link" data-key="'+ind+'"><span class="authors-link-title">'+
            label+'</span><i class="zmdi zmdi-close delete-author-social-link"></i></span>';

        return str;
    }
    function openAuthorSocialDialog(obj)
    {
        let str = '',
            link = obj ? obj.link : '',
            title = obj ? obj.title : 'facebook',
            modal = $g('#edit-author-social-modal');
        for (let ind in authorSocial) {
            if (ind != 'google+') {
                str += '<li data-value="'+authorSocial[ind].title+'">'+authorSocial[ind].label+'</li>';
            }
        }
        modal.find('ul').html(str);
        modal.find('.ba-custom-select input[type="hidden"]').val(title).prev().val(authorSocial[title].label);
        modal.find('.author-link-url').val(link);
        if (link.trim()) {
            $g('.apply-author-link').addClass('active-button');
        } else {
            $g('.apply-author-link').removeClass('active-button');
        }
        modal.modal();
    }

    function getCommentLikeStatus()
    {
        let str = app.currentComment.find('td.select-td  input[type="hidden"]').val(),
            obj = JSON.parse(str),
            div = $g('.comment-data-view-pattern').clone(),
            avatar = app.currentComment.find('.ba-author-avatar').clone(),
            view = $g('input[name="ba_view"]').val();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+".getCommentLikeStatus",
            data: {
                id: obj.id
            },
            complete:function(msg){
                let message = obj.message.replace(/\n/g, '<br>');
                if (!message) {
                    div.find('.comment-user-message-wrapper').hide();
                }
                $g('.comments-sidebar-header > span.disabled').removeClass('disabled');
                div.find('.comment-user-info-wrapper').prepend(avatar);
                div.find('.comment-user-name').text(obj.name);
                div.find('.comment-user-email').text(obj.email);
                div.find('.comment-user-ip').text(obj.ip);
                div.find('.comment-user-date').text(obj.time);
                div.find('.comment-page-title').text(obj.title);
                div.find('.comment-page-url').attr('href', obj.link);
                div.find('.comment-message').html(message);
                div.find('.comment-likes-action[data-action="likes"] .likes-count').text(obj.likes);
                div.find('.comment-likes-action[data-action="dislikes"] .likes-count').text(obj.dislikes);
                div.find('.comment-likes-action').removeClass('active');
                div.find('.comment-likes-action[data-action="'+msg.responseText+'"]').addClass('active');
                for (let i = 0; i < obj.attachments.length; i++) {
                    if (obj.attachments[i].type == 'file') {
                        let str = '<div class="comment-attachment-file">';
                        str += '<i class="zmdi zmdi-attachment-alt"></i>';
                        str += '<a target="_blank" href="'+obj.attachments[i].link+'">'+
                            obj.attachments[i].name+'</a><span class="comment-attachment-icons-wrapper"><a download href="'+
                            obj.attachments[i].link+'"><i class="zmdi zmdi-download"></i></a>';
                        if (obj.email == joomlaUser.email) {
                            str += '<i class="zmdi zmdi-delete delete-comment-attachment-file" data-id="'+obj.attachments[i].id+
                                '" data-filename="'+obj.attachments[i].filename+'" data-type="file"></i>';
                        }
                        str += '</span></div>';
                        div.find('.comment-attachments-wrapper').append(str);
                    } else {
                        let str = '<span class="comment-attachment-image-type-wrapper">'
                        str += '<span class="comment-attachment-image-type" style="background-image: url('+
                            obj.attachments[i].link.replace(/\s/g, '%20')+');" data-img="'+obj.attachments[i].link+'"></span>';
                        if (obj.email == joomlaUser.email) {
                            str += '<i class="zmdi zmdi-close delete-comment-attachment-file" data-id="'+obj.attachments[i].id+
                                '" data-filename="'+obj.attachments[i].filename+'"></i>';
                        }
                        str += '</span>'
                        div.find('.comment-attachments-image-wrapper').append(str);
                    }
                }
                if (obj.user_type == 'user' &&  obj.user_id == joomlaUser.id) {
                    div.find('.comment-user-message-wrapper .ba-comment-message-wrapper').hide();
                } else {
                    div.find('.edit-user-comment, .comment-user-message-wrapper .ba-comment-message-wrapper').remove();
                }
                if (view == 'reviews') {
                    div.find('.review-rating-wrapper').each(function(){
                        if (obj.parent != 0) {
                            this.remove();
                        } else {
                            let stars = this.querySelectorAll('i');
                            for (let i = 0; i < obj.rating; i++) {
                                stars[i].classList.add('active');
                            }
                        }
                    });
                    if (obj.parent != 0) {
                        div.find('> .ba-comment-message-wrapper').remove();
                    }
                }
                let html = div.html();
                $g('.comments-sidebar-body').html(html);
                app.setTooltip('.comments-sidebar-body');
            }
        });
    }

    function insertTextAtCursor(el, text)
    {
        var val = el.value, endIndex, range;
        if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined") {
            endIndex = el.selectionEnd;
            el.value = val.slice(0, el.selectionStart) + text + val.slice(endIndex);
            el.selectionStart = el.selectionEnd = endIndex + text.length;
        } else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined") {
            el.focus();
            range = document.selection.createRange();
            range.collapse(false);
            range.text = text;
            range.select();
        }
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
            modal.find('a.zmdi-download').attr('href', image.src);
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
        img.style.backgroundImage = 'url('+image.src.replace(/\s/g, '%20')+')';

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
        img.style.backgroundImage = 'url('+image.src.replace(/\s/g, '%20')+')';

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
        }, 500);
    }

    $g('.ba-store-statistic-select input[type="hidden"]').on('change', function(){
        app.statistic = {
            date: new Date(this.dataset.current),
            current: this.dataset.current,
            value: this.dataset.current,
            type: this.value
        }
        $g('.ba-store-statistic-action[data-action="+"]').addClass('ba-disabled');
        if (this.value == 'y') {
            $g('.ba-store-statistic-action[data-action="-"]').addClass('ba-disabled');
        } else {
            $g('.ba-store-statistic-action[data-action="-"]').removeClass('ba-disabled');
        }
        this.closest('.ba-store-statistic-header-filter-wrapper').classList.remove('ba-custom-store-statistic');
        if (this.value == 'c') {
            this.closest('.ba-store-statistic-header-filter-wrapper').classList.add('ba-custom-store-statistic');
            $g('.ba-store-statistic-custom-action input').val(app.statistic.value);
            app.statistic.value = app.statistic.value+' - '+app.statistic.value;
        } else if (this.value == 'm') {
            app.statistic.value = app.statistic.date.getFullYear();
        }
        app.statisticFilter();
    });

    $g('.ba-store-statistic-header-filter-wrapper').on('dateUpdated', function(event, d1, d2){
        app.statistic.value = d1+' - '+d2;
        let date = new Date(d1),
            month = date.getMonth() + 1,
            year = date.getFullYear(),
            day = date.getDate(),
            value = app._('SHORT_M'+month)+' '+day+', '+year;
        date = new Date(d2);
        month = date.getMonth() + 1;
        year = date.getFullYear();
        day = date.getDate();
        value += ' - '+app._('SHORT_M'+month)+' '+(day < 10 ? '0'+day : day)+', '+year;
        $g('.ba-store-statistic-select input[type="text"]').val(value);
        app.statisticFilter();
    })

    $g('.ba-store-statistic-action').on('click', function(){
        let value = '';
        if (app.statistic.type == 'd') {
            let day = app.statistic.date.getDate(),
                month = year = null;
            app.statistic.date.setDate(day + (this.dataset.action == '+' ? 1 : -1));
            month = app.statistic.date.getMonth() + 1;
            year = app.statistic.date.getFullYear();
            day = app.statistic.date.getDate();
            app.statistic.value = year+'-'+(month < 10 ? '0'+month : month)+'-'+(day < 10 ? '0'+day : day);
            value = app._('SHORT_M'+month)+' '+(day < 10 ? '0'+day : day)+', '+year;
        } else if (app.statistic.type == 'w') {
            let day = app.statistic.date.getDate(),
                month = year = null;
            app.statistic.date.setDate(day + (this.dataset.action == '+' ? 7 : -7));
            month = app.statistic.date.getMonth() + 1;
            year = app.statistic.date.getFullYear();
            day = app.statistic.date.getDate();
            app.statistic.value = year+'-'+(month < 10 ? '0'+month : month)+'-'+(day < 10 ? '0'+day : day);
            let date = new Date(+app.statistic.date);
            date.setDate(date.getDate() - 7);
            day = date.getDate();
            month = date.getMonth() + 1;
            year = date.getFullYear();
            value = app._('SHORT_M'+month)+' '+day+', '+year+' - ';
            date.setDate(day + 6);
            day = date.getDate();
            month = date.getMonth() + 1;
            year = date.getFullYear();
            value += app._('SHORT_M'+month)+' '+(day < 10 ? '0'+day : day)+', '+year;
        } else if (app.statistic.type == 'm') {
            let year = app.statistic.date.getFullYear();
            app.statistic.date.setFullYear(year + (this.dataset.action == '+' ? 1 : -1));
            year = app.statistic.date.getFullYear();
            app.statistic.value = String(year);
            value = app._('MONTHLY')+', '+year;
        } else if (app.statistic.type == 'c' || app.statistic.type == 'y') {
            return false;
        }
        if (this.dataset.action == '+' && app.statistic.value > app.statistic.current) {
            $g('.ba-store-statistic-action[data-action="+"]').addClass('ba-disabled');
            app.statistic.date = new Date(app.statistic.current);
            return false;
        } else {
            $g('.ba-store-statistic-action.ba-disabled').removeClass('ba-disabled');
        }
        $g('.ba-store-statistic-select input[type="text"]').val(value);
        app.statisticFilter();
    });

    $g('.ba-statistics-chart').each(function(){
        $g('.ba-store-statistic-select input[type="hidden"]').trigger('change');
    });

    $g('.orders-status-select').on('customAction', function(){
        $g('.apply-order-status').addClass('active-button');
    });

    $g('.apply-order-status').on('click', function(event){
        event.preventDefault();
        let modal = $g('#orders-status-modal'),
            id = modal.attr('data-id'),
            status = modal.find('input[type="hidden"]').val();
        if (status == 'completed') {
            app.showLoading('LOADING');
        }
        makeFetchRequest('index.php?option=com_gridbox&task=orders.updateStatus', {
            id : id,
            status: status,
            comment: modal.find('textarea').val()
        }).then(function(json){
            $g('.edit-order-status, tr[data-id="'+id+'"] .order-status-cell').each(function(){
                let $this = $g(this);
                this.style.setProperty('--order-status-color', app.statuses[status].color);
                $this.find('.order-status-title').text(app.statuses[status].title);
            });
            if (status == 'completed') {
                notification.removeClass('notification-in').addClass('animation-out');
            }
            modal.modal('hide');
        });
    });

    $g('#category-applies-dialog').each(function(){
        let url = 'index.php?option=com_gridbox&task=promocodes.getCategories',
            modal = this;
        makeFetchRequest(url).then(function(json){
            app.categories = {};
            json.forEach(function(obj){
                app.categories[obj.id] = obj;
            });
            getProductsHtml(modal, json, 'category');
            modal.dataset.loaded = 'loaded';
        });
    });

    $g('#store-tax-options .sorting-container').on('click', '.add-tax-category', function(){
        fontBtn = this;
        this.wrapper = this.closest('.sorting-tax-category-wrapper').querySelector('.tax-rates-items-wrapper');
        document.querySelectorAll('#category-applies-dialog li').forEach(function(li){
            let exist = fontBtn.wrapper.querySelector('span[data-id="'+li.dataset.id+'"]');
            li.classList[exist ? 'add' : 'remove']('selected');
        });
        showDataTagsDialog('category-applies-dialog', 15);
    }).on('change', '.add-tax-category', function(){
        let obj = JSON.parse(this.dataset.value),
            html = '<span class="selected-items" data-id="'+obj.id+'"><span class="selected-items-name">';
        html += obj.title+'</span><i class="zmdi zmdi-close remove-selected-items"></i></span>';
        $g(this.wrapper).append(html);
    }).on('click', '.add-tax-country-region', function(){
        let modal = $g('#store-countries-dialog');
        if (this.dataset.target == 'region') {
            let wrapper = this.closest('.sorting-tax-countries-wrapper'),
                id = wrapper.querySelector('.tax-rates-items-wrapper .selected-items').dataset.id,
                states = [];
            modal.addClass('add-region-to-tax');
            wrapper.querySelectorAll('.tax-country-state .selected-items').forEach(function(item){
                states.push(item.dataset.id);
            })
            modal.find('.country-modal-body li[data-value="'+id+'"] span[data-action="show"]').trigger('click');
            modal.find('.states-modal-body li').each(function(){
                this.classList.remove('prevent-event');
                if (states.indexOf(this.dataset.value) != -1) {
                    this.classList.add('selected');
                }
            });
        } else {
            modal.removeClass('add-region-to-tax');
        }
        app.country.showModal(this);
    }).on('change', '.add-tax-country-region', function(){
        let row = $g(this).closest('.sorting-item'),
            id = this.dataset.value,
            region = this.dataset.target == 'region',
            obj = region ? app.country.obj.states[id] : app.country.countries[id],
            html = '<span class="selected-items" data-id="'+obj.id+'"><span class="selected-items-name">'+obj.title+
                '</span><i class="zmdi zmdi-close '+(region ?'delete-country-region' : 'delete-tax-country')+'"></i></span>';
        if (region) {
            let state = '<div class="tax-country-state">'+html+'</div>';
            row.find('.sorting-tax-rate').append('<input type="text" placeholder="%">');
            row.find('.sorting-tax-countries-wrapper').append(state);
        } else {
            this.querySelector('i').className = 'zmdi zmdi-pin';
            this.querySelector('.ba-tooltip').textContent = app._('ADD_REGION');
            row.find('.tax-country-state').remove();
            row.find('.sorting-tax-rate input').each(function(i){
                if (i != 0) {
                    this.remove();
                }
            })
            row.find('.sorting-tax-country .tax-rates-items-wrapper').html(html);
            for (let ind in obj.states) {
                let state = '<div class="tax-country-state"><span class="selected-items" data-id="'+ind;
                state += '"><span class="selected-items-name">'+obj.states[ind].title;
                state += '</span><i class="zmdi zmdi-close delete-country-region"></i></span></div>';
                row.find('.sorting-tax-rate').append('<input type="text" placeholder="%">');
                row.find('.sorting-tax-countries-wrapper').append(state);
            }
            this.dataset.target = 'region';
        }
    }).on('click', '.delete-tax-country', function(){
        let row = $g(this).closest('.sorting-item');
        row.find('.tax-country-state, .sorting-tax-country .selected-items').remove();
        row.find('.add-tax-country-region').each(function(){
            this.dataset.target = 'country';
            this.querySelector('i').className = 'zmdi zmdi-globe';
            this.querySelector('.ba-tooltip').textContent = app._('ADD_COUNTRY');
        });
        row.find('.sorting-tax-rate input').each(function(i){
            if (i != 0) {
                this.remove();
            }
        })
    }).on('click', '.delete-country-region', function(){
        let parent = $g(this).closest('.tax-country-state'),
            ind = parent.index();
        this.closest('.sorting-item').querySelector('.sorting-tax-rate input:nth-child('+(ind + 1)+')').remove();
        parent.remove();
    }).on('click', '.show-more-tax-options', function(){
        fontBtn = this;
        let rect = this.getBoundingClientRect(),
            w = document.documentElement.offsetWidth,
            modal = $g('#more-tax-options-dialog'),
            width = modal.innerWidth(),
            height = modal.innerHeight(),
            top = rect.top - height - 10,
            left = rect.left - width / 2 + rect.width / 2,
            bottom = '50%';
        if (w < left + width) {
            left = w - width;
            bottom = (w - rect.right + rect.width / 2)+'px';
        }
        modal.find('input[type="checkbox"][data-option="shipping"]').prop('checked', Boolean(this.dataset.shipping * 1));
        modal.css({
            top: top+'px',
            left: left+'px'
        }).modal()[0].style.setProperty('--picker-arrow-bottom', bottom);

    });

    $g('#more-tax-options-dialog').on('change', 'input[data-option]', function(){
        fontBtn.dataset[this.dataset.option] = Number(this.checked);
    });

    $g('.seo-default-settings').on('click', function(){
        let data = {
                type: this.dataset.type,
                id: 0
            }
        document.querySelectorAll('input[name="blog"]').forEach((input) => {
            data.id = input.value;
        });
        app.fetch('index.php?option=com_gridbox&task=apps.getDefaultsSeo', data).then((text) => {
            let modal = $g('#seo-default-settings-modal'),
                json = JSON.parse(text);
            modal.find('.select-data-tags').attr('data-template', data.type+'-data-tags-template');
            modal.find('input[data-key], textarea[data-key]').each((i, input) => {
                let select = input.closest('.ba-custom-select'),
                    value = json[input.dataset.key];
                input.value = value;
                if (select) {
                    let li = select.querySelector('li[data-value="'+value+'"]');
                    select.querySelector('input[type="text"]').value = li ? li.textContent.trim() : '';
                }
            });
            modal.modal();
        });
    });

    $g('.apply-seo-default-settings').on('click', (event) => {
        event.preventDefault();
        let modal = $g('#seo-default-settings-modal'),
            data = {};
        modal.find('input[data-key], textarea[data-key]').each((i, input) => {
            data[input.dataset.key] = input.value;
        });
        app.fetch('index.php?option=com_gridbox&task=apps.setDefaultsSeo', data);
        modal.modal('hide');
    });

    $g('.select-data-tags').on('click', function(){
        fontBtn = this;
        document.querySelector('#data-tags-dialog').dataset.view = '';
        $g('.invoice-all-fields').css('display', 'none');
        if (this.dataset.template) {
            let template = document.querySelector('template.'+this.dataset.template).content.cloneNode(true);
            document.querySelectorAll('#data-tags-dialog .modal-body').forEach((div) => {
                div.innerHTML = '';
                div.append(template);
            })
        }
        showDataTagsDialog('data-tags-dialog');
    });

    $g('.select-post-tags').on('click', function(){
        fontBtn = this;
        $g('#post-tags-dialog .ba-settings-item[data-id]').each(function(){
            this.classList[document.querySelector('.meta-tags option[value="'+this.dataset.id+'"]') ? 'add' : 'remove']('selected');
        });
        showDataTagsDialog('post-tags-dialog');
    });

    $g('#post-tags-dialog .modal-body').on('change', '.select-data-tags-type', function(){
        let modal = $g('#post-tags-dialog');
        modal.find('.ba-settings-item[data-id]').hide();
        modal.find('.search-post-tags').value = '';
        modal.find('.ba-settings-item[data-id]'+(this.value != 1 ? '[data-folder="'+this.value+'"]' : '')).css('display', '');
    });
    $g('#post-tags-dialog .modal-body').on('input', '.search-post-tags', function(){
        let modal = $g('#post-tags-dialog'),
            search = this.value.trim().toLowerCase();
            folder = modal.find('.select-data-tags-type').val();
        modal.find('.ba-settings-item[data-id]').hide();
        modal.find('.ba-settings-item[data-id]').each(function(){
            if (folder == this.dataset.folder || folder == 1) {
                let title = this.querySelector('.ba-settings-item-title').textContent.trim().toLowerCase();
                this.style.display = (search === '' || title.indexOf(search) != -1 ? '' : 'none');
            }
        });
    });
    $g('#post-tags-dialog .modal-body').on('click', '.post-tags-wrapper .ba-settings-input-type', function(){
        let id = this.dataset.id,
            title = this.textContent.trim(),
            str = '';
        if (document.querySelector('.meta-tags option[value="'+id+'"]')) {
            return;
        }
        str = '<li class="tags-chosen"><span>'+title+'</span><i class="zmdi zmdi-close" data-remove="'+id+'"></i></li>';
        $g('.picked-tags .search-tag').before(str);
        str = '<option value="'+id+'" selected>'+title+'</option>';
        $g('select.meta_tags').append(str);
        $g('#post-tags-dialog').modal('hide');
    });

    $g('#data-tags-dialog .modal-body').on('change', '.select-data-tags-type', function(){
        let modal = $g('#data-tags-dialog');
        modal.find('div.ba-settings-group[class*="-data-tags"]').hide();
        modal.find('div.ba-settings-group'+(this.value ? '.'+this.value+'-data-tags' : '')).css('display', '');
    });
    $g('#data-tags-dialog .modal-body').on('click', '.ba-settings-input-type', function(){
        let value = this.querySelector('input[type="text"]').value;
        if ('ondataTagsInput' in fontBtn) {
            fontBtn.dataset.value = value;
            $g(fontBtn).trigger('dataTagsInput');
        } else {
            let input = fontBtn.closest('.ba-options-group-element, .ba-group-element').querySelector('input[type="text"], textarea');
            input.setRangeText(value);
            let start = input.selectionStart+value.length;
            input.setSelectionRange(start, start);
            input.focus();
            $g(input).trigger('input');
        }
        $g('#data-tags-dialog').modal('hide');
    });

    $g('body .modal').on('shown', function(){
        let backdrop = $g('.modal-backdrop').last().addClass(this.id+'-backdrop');
        if (this.classList.contains('ba-modal-picker')) {
            backdrop.addClass('modal-picker-backdrop');
        }
        app.modal.setHeight(this);
    });

    $g('body').on('click', '#gridbox-payment-methods-dialog .gridbox-app-element', function(){
        $g('#gridbox-payment-methods-dialog').modal('hide').trigger('hidden-gridbox-modal');
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task=paymentmethods.addMethod",
            data: {
                'type': this.dataset.type
            },
            complete:function(msg){
                reloadPage(app._('ITEM_CREATED'));
            }
        });
        this.dataset.installed = 1;
        this.querySelector('.default-theme').classList.remove('ba-hide-element');
    });

    $g('body').on('click', '.select-td label, .status-td a', function(event){
        event.stopPropagation();
    });

    $g('body.view-orders').on('change', 'input[name="cid[]"]', function(){
        let canImport = true;
        $g('.main-table tr input[name="cid[]"]').each(function(){
            if (!this.checked) {
                return;
            }
            if (this.closest('tr').classList.contains('order-with-booking')) {
                canImport = false;
            } else {
                canImport = true;
                return false;
            }
        });
        if (canImport) {
            $g('joomla-toolbar-button[task="orders.exportcsv"] button').removeAttr('disabled');
        } else {
            $g('joomla-toolbar-button[task="orders.exportcsv"] button').attr('disabled', 1)
        }
    })

    $g('body').on('click', '.edit-order-status', function(){
        makeFetchRequest('index.php?option=com_gridbox&task=orders.getStatus', {
            id: this.dataset.id
        }).then(function(json){
            let modal = $g('#orders-status-modal'),
                html = '';
            modal.find('.orders-status-select').each(function(){
                let status = app.statuses[json.status] ? app.statuses[json.status] : app.statuses.undefined;
                this.querySelector('input[type="hidden"]').value = json.status;
                this.querySelector('input[type="text"]').value = status.title;
                this.style.setProperty('--status-color', status.color);
            });
            modal.find('.ba-btn-primary').removeClass('active-button');
            modal.find('textarea').val('');
            json.history.forEach(function(record){
                let status = app.statuses[record.status] ? app.statuses[record.status] : app.statuses.undefined;
                html += '<div class="order-status-history-record"><div class="order-status-history-record-header">'+
                    '<div><span class="order-status-history-record-username">'+record.username+'</span>'+
                    '<span class="order-status-history-record-date">'+record.date+'</span></div>'+
                    '<div><span class="order-status-history-record-text">'+app._('CHANGED_STATUS_TO')+'</span>'+
                    '<span class="order-status-history-record-status" style="--status-color: '+status.color+
                    '">'+status.title+'</span></div></div><div class="order-status-history-record-comment">'+
                    record.comment+'</div></div>';
            });
            modal.find('#order-status-history').html(html);
            modal.modal().attr('data-id', json.id);
        });
    }).on('click', '.payment-methods-table tbody tr', function(){
        let $this = this,
            data = {
                id: this.dataset.id
            };
        makeFetchRequest('index.php?option=com_gridbox&task=paymentmethods.getOptions', data).then(function(json){
            if (json) {
                let title = json.title;
                document.querySelectorAll('#gridbox-payment-methods-dialog [data-type="'+json.type+'"]').forEach(function(el){
                    title = el.querySelector('.ba-title').textContent;
                });
                document.querySelector('.ba-options-group-header').textContent = title;
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.type;
                document.querySelector('.twin-view-right-sidebar').dataset.id = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
                $this.classList.add('active');
                let settings = JSON.parse(json.settings);
                document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function(el){
                    el.value = json[el.dataset.key];
                    app.removeAlertTooltip(el);
                });
                document.querySelectorAll('.'+json.type+'-payment-options [data-settings]').forEach(function(el){
                    let def = el.type == 'checkbox' ? false : '',
                        value = el.dataset.settings in settings ? settings[el.dataset.settings] : def;
                    el[el.type == 'checkbox' ? 'checked' : 'value'] = value;
                    if (el.dataset.cke) {
                        app.cke[el.dataset.settings].setData(value);
                    }
                    app.removeAlertTooltip(el);
                });
                document.querySelectorAll('.'+json.type+'-payment-options .set-group-display').forEach(function(el){
                    var action = el.checked ? 'addClass' : 'removeClass';
                    $g(el).closest('.ba-options-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
                });
            }
        });
    }).on('change', '.payment-methods-table .set-group-display', function(){
        $g(this).closest('.ba-options-group-element').nextAll().find('[data-settings]').each(function(){
            app.removeAlertTooltip(this);
        });
    }).on('click', '.apply-payment-methods', function(){
        if (!this.classList.contains('disabled')) {
            let alert = false,
                obj = {
                    id: this.closest('.twin-view-right-sidebar').dataset.id,
                    type: this.closest('.twin-view-right-sidebar').dataset.edit
                },
                settings = {};
            document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.value;
                if ($this.value == '') {
                    let parent = $this.closest('.ba-options-group-element');
                    alert = true;
                    app.toggleAlertTooltip(alert, $this, parent, 'THIS_FIELD_REQUIRED');
                }
            });
            document.querySelectorAll('.'+obj.type+'-payment-options [data-settings]').forEach(function($this){
                settings[$this.dataset.settings] = $this.type == 'checkbox' ? $this.checked : $this.value;
                if ($this.dataset.cke) {
                    settings[$this.dataset.settings] = app.cke[$this.dataset.settings].getData()
                }
                if ($this.dataset.settings != 'description' && $this.type != 'checkbox' && $this.value == '' &&
                    (!$this.closest('.ba-subgroup-element') || $this.closest('.ba-subgroup-element').classList.contains('visible-subgroup'))) {
                    let parent = $this.closest('.ba-options-group-element');
                    alert = true;
                    app.toggleAlertTooltip(alert, $this, parent, 'THIS_FIELD_REQUIRED');
                }
            });
            if (alert) {
                return false;
            }
            obj.settings = JSON.stringify(settings);
            makeFetchRequest('index.php?option=com_gridbox&task=paymentmethods.updateMethod', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    }).on('click', '.delete-payment-method', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.id;
            deleteMode = 'paymentmethods.contextDelete';
            $g('#delete-dialog').modal();
        }
    });

    $g('body').on('click', '.integrations-table tbody tr', function(){
        let $this = this,
            data = {
                id: this.dataset.id
            };
        makeFetchRequest('index.php?option=com_gridbox&task=integrations.getOptions', data).then(function(json){
            if (json) {
                document.querySelector('.ba-options-group-header').textContent = json.title;
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.service;
                document.querySelector('.twin-view-right-sidebar').dataset.id = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
                $this.classList.add('active');
                document.querySelectorAll('.integrations-options [data-key]').forEach(function(el){
                    let text = 'App id';
                    if (json.service == 'google_login') {
                        text = 'OAuth client';
                    } else if (json.service == 'disqus') {
                        text = 'Disqus Subdomain';
                    } else if (json.service == 'exchangerates') {
                        text = 'API Key';
                    } else if (json.service == 'inpost' || json.service == 'novaposhta') {
                        text = 'Token';
                    }
                    el.closest('div').querySelector('label').textContent = text;
                    el.value = json[el.dataset.key];
                });
            }
        });
    }).on('click', '.apply-integration', function(){
        if (!this.classList.contains('disabled')) {
            let sidebar = this.closest('.twin-view-right-sidebar'),
                obj = {
                    id: sidebar.dataset.id
                };
            sidebar.querySelectorAll('.integrations-options [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.value;
            });
            makeFetchRequest('index.php?option=com_gridbox&task=integrations.update', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    })

    $g('body').on('focus', '.ba-options-group-wrapper .ba-alert', function(){
        app.removeAlertTooltip(this.querySelector('input, select'));
    });

    $g('body').on('click', '.shipping-table tbody tr', function(){
        let $this = this,
            data = {
                id: this.dataset.id
            };
        makeFetchRequest('index.php?option=com_gridbox&task=shipping.getOptions', data).then(function(json){
            if (json) {
                let params = JSON.parse(json.options),
                    value = null;
                $g('.shipping-options [data-settings]').each(function(){
                    let shippingType = this.closest('.shipping-type-options');
                    if (shippingType && params.type != shippingType.dataset.type) {
                        value = this.type == 'checkbox' ? false : '';
                    } else if (this.dataset.group) {
                        value = params[this.dataset.group][this.dataset.settings];
                    } else {
                        value = params[this.dataset.settings];
                    }
                    if (this.type == 'checkbox') {
                        this.checked = value;
                        $g(this).trigger('change');
                    } else if (this.dataset.settings == 'type') {
                        this.value = value;
                        $g('.shipping-type-options').hide();
                        let length = $g('.'+value+'-shipping-type').css('display', '').length;
                        if (length > 0) {
                            $g('.shipping-type-options-label').css('display', '');
                        } else {
                            $g('.shipping-type-options-label').hide();
                        }
                    } else if (this.type == 'text') {
                        this.value = value;
                    } else if (this.dataset.cke) {
                        this.value = value;
                        app.cke[this.dataset.group+'-'+this.dataset.settings].setData(value);
                    } else if (this.classList.contains('shipping-countries-list')) {
                        this.innerHTML = '';
                        for (let id in value) {
                            let span = app.country.getShippingEl(id, value[id]);
                            this.append(span);
                        }
                    } else if (this.classList.contains('ba-rate-by-list')) {
                        this.innerHTML = '';
                        if (value) {
                            for (let ind in value) {
                                let clone = document.querySelector('template.rate-by-'+this.dataset.group).content.cloneNode(true);
                                clone.querySelectorAll('[data-ind]').forEach(function(el){
                                    if (el.localName == 'input') {
                                        el.value = value[ind][el.dataset.ind];
                                    } else {
                                        value[ind][el.dataset.ind].forEach(function(id){
                                            if (app.categories[id]) {
                                                let obj = app.categories[id],
                                                    html = '<span class="selected-items" data-id="'+id+'">';
                                                html += '<span class="ba-item-thumbnail"';
                                                if (obj.image) {
                                                    let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
                                                    html += ' style="background-image: url('+image.replace(/\s/g, '%20')+');"';
                                                }
                                                html += '>';
                                                if (!obj.image) {
                                                    html += '<i class="zmdi zmdi-folder"></i>';
                                                }
                                                html += '</span><span class="selected-items-name">'+obj.title;
                                                html += '</span><i class="zmdi zmdi-close remove-selected-items"></i></span>';
                                                el.insertAdjacentHTML('beforeend', html);
                                            }
                                        });
                                    }
                                });
                                app.setTooltip(clone);
                                this.append(clone);
                            }
                        }
                    }
                });
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                $this.classList.add('active');
                document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function(el){
                    el.value = json[el.dataset.key];
                    app.removeAlertTooltip(el);
                });
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
            }
        });
    }).on('change', '.shipping-options select[data-settings="type"]', function(){
        $g('.shipping-type-options').hide();
        let length = $g('.'+this.value+'-shipping-type').css('display', '').length;
        if (length > 0) {
            $g('.shipping-type-options-label').css('display', '');
        } else {
            $g('.shipping-type-options-label').hide();
        }
    }).on('click', '.shipping-add-countries', function(){
        let list = this.closest('.shipping-countries-wrapper').querySelector('.shipping-countries-list');
        app.country.modal.querySelectorAll('.country-modal-body li').forEach(function(li){
            if (list.querySelector('.selected-items[data-id="'+li.dataset.value+'"]')) {
                li.classList.add('disabled-country');
            } else {
                li.classList.remove('disabled-country');
            }
        });
        app.country.showModal(this);
    }).on('change', '.shipping-add-countries', function(){
        let span = app.country.getShippingEl(this.dataset.value);
        $g(this).closest('.shipping-countries-wrapper').find('.shipping-countries-list').append(span);
    }).on('click', '.selected-regions-count', function(){
        fontBtn = this;
        let item = this.closest('.selected-items'),
            states = JSON.parse(item.dataset.regions),
            id = item.dataset.id,
            content = document.querySelector('template.states-list-li').content,
            modal = document.querySelector('#store-states-list-dialog'),
            ul = modal.querySelector('ul'),
            country = app.country.countries[id],
            obj = country.states;
        ul.innerHTML = '';
        modal.querySelector('.states-modal-header').textContent = country.title;
        for (let ind in obj) {
            let clone = content.cloneNode(true);
            clone.querySelector('.picker-item-title').textContent = obj[ind].title;
            clone.querySelectorAll('input').forEach(function(input){
                input.dataset.id = ind;
                input.checked = (ind in states) ? states[ind] : false;
            });
            ul.append(clone);
        }
        showDataTagsDialog('store-states-list-dialog', 0);
    }).on('change', '.label-toggle-btn', function(){
        this.closest('.ba-options-group-element').classList[this.checked ? 'remove' : 'add']('hidden-element-content');
    }).on('change', '#store-states-list-dialog input[type="checkbox"]', function(){
        let item = fontBtn.closest('.selected-items'),
            c = 0,
            states = JSON.parse(item.dataset.regions);
        states[this.dataset.id] = this.checked;
        for (let ind in states) {
            if (states[ind]) {
                c++;
            }
        }
        item.querySelector('.selected-regions-count').dataset.count = c;
        item.dataset.regions = JSON.stringify(states);
    }).on('click', '.add-new-rate-by', function(){
        let clone = document.querySelector('template.rate-by-'+this.dataset.target).content.cloneNode(true);
        app.setTooltip(clone);
        this.closest('.ba-rate-by-wrapper').querySelector('.ba-rate-by-list').append(clone);
    }).on('click', '.delete-up-to-rate-line', function(){
        $g('body > .ba-tooltip').remove();
        this.closest('.ba-rate-by-line').remove();
    }).on('click', '.apply-shipping', function(){
        if (!this.classList.contains('disabled')) {
            let required = ['title', 'price'],
                alert = false,
                params = {},
                obj = {
                    id: this.closest('.twin-view-right-sidebar').dataset.edit
                };
            document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.value;
                if (required.indexOf($this.dataset.key) != -1 && $this.value == '') {
                    let parent = $this.closest('.ba-options-group-element');
                    alert = true;
                    app.toggleAlertTooltip(alert, $this, parent, 'THIS_FIELD_REQUIRED');
                }
            });
            if (alert) {
                return false;
            }
            $g('.shipping-options [data-settings]').each(function(){
                let shippingType = this.closest('.shipping-type-options');
                if (shippingType && params.type != shippingType.dataset.type) {
                    return true;
                }
                if (this.type == 'checkbox') {
                    value = this.checked;
                } else if (this.dataset.settings == 'type') {
                    value = this.value;
                } else if (this.type == 'text') {
                    value = this.value;
                } else if (this.dataset.cke) {
                    value = app.cke[this.dataset.group+'-'+this.dataset.settings].getData();
                } else if (this.classList.contains('shipping-countries-list')) {
                    value = {};
                    this.querySelectorAll('.selected-items').forEach(function(item){
                        value[item.dataset.id] = JSON.parse(item.dataset.regions);
                    });
                } else if (this.classList.contains('ba-rate-by-list')) {
                    value = {};
                    this.querySelectorAll('.ba-rate-by-line').forEach(function(line, i){
                        value[i] = {};
                        line.querySelectorAll('input').forEach(function(input){
                            value[i][input.dataset.ind] = input.value;
                        });
                        line.querySelectorAll('.selected-items-list').forEach(function(div){
                            let array = value[i][div.dataset.ind] = [];
                            div.querySelectorAll('.selected-items').forEach(function(span){
                                array.push(span.dataset.id);
                            });
                        });
                    });
                }
                if (this.dataset.group && !params[this.dataset.group]) {
                    params[this.dataset.group] = {};
                }
                if (this.dataset.group) {
                    params[this.dataset.group][this.dataset.settings] = value;
                } else {
                    params[this.dataset.settings] = value;
                }
            });
            obj.options = JSON.stringify(params);
            makeFetchRequest('index.php?option=com_gridbox&task=shipping.updateShipping', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    }).on('click', '.add-category-rate', function(){
        fontBtn = this;
        document.querySelectorAll('#category-applies-dialog li').forEach(function(li){
            let exist = document.querySelector('span[data-id="'+li.dataset.id+'"]');
            li.classList[exist ? 'add' : 'remove']('selected');
        });
        showDataTagsDialog('category-applies-dialog', 15);
    }).on('change', '.add-category-rate', function(){
        let obj = JSON.parse(this.dataset.value),
            wrapper = this.closest('.ba-rate-by-line').querySelector('.selected-items-list'),
            html = '<span class="selected-items" data-id="'+obj.id+'">';
        html += '<span class="ba-item-thumbnail"';
        if (obj.image) {
            let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
            html += ' style="background-image: url('+image.replace(/\s/g, '%20')+');"';
        }
        html += '>';
        if (!obj.image) {
            html += '<i class="zmdi zmdi-folder"></i>';
        }
        html += '</span><span class="selected-items-name">';
        html += obj.title+'</span><i class="zmdi zmdi-close remove-selected-items"></i></span>';
        $g(wrapper).append(html);
    }).on('click', '.delete-shipping', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            deleteMode = 'shipping.contextDelete';
            $g('#delete-dialog').modal();
        }
    });

    $g('body').on('click', '.ba-add-order', function(event){
        event.preventDefault();
        event.stopPropagation();
        let price = app.renderPrice(0),
            html = document.querySelector('.exist-customer-info-fields').content.cloneNode(true),
            footer = document.querySelector('.template-order-footer-total-wrapper').content.cloneNode(true),
            modal = $g('#create-new-order-dialog');
        app.prepareEmptyCart(modal);
        modal.find('.modal-header h3').text(app._('NEW_ORDER'));
        modal.find('.customer-info-wrapper .ba-options-group-wrapper').html(html);
        modal.find('.order-footer-total-wrapper').html(footer);
        modal.find('.order-coupon-code').removeAttr('data-value');
        modal.find('.sorting-container').empty();
        modal.find('.order-info-wrapper').find('input, textarea, select').val('');
        modal.find('.ba-cart-price-value').text(price);
        modal.find('.order-shipping-carrier').css('display', 'none');
        modal.removeClass('view-created-order').modal();
    }).on('mousedown', '.context-view-order', function(){
        currentContext.trigger('click');
    }).on('mousedown', '.context-download-order', function(){
        let id = currentContext.attr('data-id');
        $g('.download-exist-order[data-layout="pdf"]').attr('data-id', id).trigger('click');
    }).on('mousedown', '.context-delete-order', function(){
        document.querySelector('#context-item').value = currentContext.attr('data-id');
        deleteMode = 'orders.contextDelete';
        $g('#delete-dialog').modal();
    }).on('click', '.orders-list tbody tr', function(){
        let tr = this.closest('tr');
        makeFetchRequest('index.php?option=com_gridbox&task=orders.getOrder', {
            id: tr.dataset.id
        }).then(function(json){
            if (tr.classList.contains('unread-order')) {
                tr.classList.remove('unread-order');
                $g('.unread-comments-count[data-type="orders"]').each(function(){
                    let count = this.textContent - 1;
                    if (count) {
                        this.textContent = count;
                    } else {
                        this.remove();
                    }
                });
            }
            app.setCurrentOrder(json);
        });
    });

    app.renew = {
        discount: 0,
        subtotal: 0,
        total: 0,
        tax: null,
        validPromo: false,
        expires: {
            h: app._('HOURS'),
            d: app._('DAYS'),
            m: app._('MONTHS'),
            y: app._('YEARS')
        },
        checkPromo: function(){
            if (this.promo.applies_to == '*') {
                this.validPromo = true;
            } else if (this.promo.applies_to == 'product') {
                for (let i in this.promo.map) {
                    this.validPromo = this.promo.map[i].id == this.subscription.product_id;
                    if (this.validPromo) {
                        break;
                    }
                }
            } else {
                for (let i in this.promo.map) {
                    this.validPromo = this.subscription.categories.indexOf(this.promo.map[i].id) != -1;
                    if (this.validPromo) {
                        break;
                    }
                }
            }

            return this.validPromo;
        },
        checkProductTaxMap: function(categories){
            let valid = false;
            for (let i = 0; i < categories.length; i++) {
                valid = this.subscription.categories.indexOf(categories[i]) != -1;
                if (valid) {
                    break;
                }
            }

            return valid;
        },
        getTaxRegion: function(regions){
            let result = null;
            for (let i = 0; i < regions.length; i++) {
                if (regions[i].state_id == this.subscription.region) {
                    result = regions[i];
                    break;
                }
            }

            return result;
        },
        getProductTax: function(price, country, region, category){
            let obj = null,
                array = category ? app.taxRates.categories : app.taxRates.empty;
            for (let i = 0; i < array.length; i++) {
                let tax = array[i],
                    count = country ? tax.country_id == this.subscription.country : true,
                    cat = category ? this.checkProductTaxMap(tax.categories) : true,
                    reg = region ? this.getTaxRegion(tax.regions) : true,
                    rate = 0;
                if (count && cat && reg) {
                    rate = reg.rate ? reg.rate : tax.rate;
                    obj = {
                        key: tax.key,
                        title: tax.title,
                        rate: rate,
                        amount: app.store.tax.mode == 'excl' ? price * (rate / 100) : price - price / (rate / 100 + 1)
                    };
                    break;
                }
            }
            if (!obj && country && region && category) {
                obj = this.getProductTax(price, true, false, true);
            } else if (!obj && country && !region && category) {
                obj = this.getProductTax(price, true, true, false);
            } else if (!obj && country && region && !category) {
                obj = this.getProductTax(price, true, false, false);
            } else if (!obj && country && !region && !category) {
                obj = this.getProductTax(price, false, false, true);
            } else if (!obj && !country && !region && category) {
                obj = this.getProductTax(price, false, false, false);
            }

            return obj;
        },
        calculate: function(){
            if (!app.taxRates) {
                app.taxRates = {
                    categories: [],
                    empty: []
                }
                for (let i = 0; i < app.store.tax.rates.length; i++) {
                    let rate = app.store.tax.rates[i];
                    rate.key = i;
                    if (rate.categories.length) {
                        app.taxRates.categories.push(rate);
                    } else {
                        app.taxRates.empty.push(rate);
                    }
                }
            }
            this.validPromo = false;
            this.total = this.subtotal = this.plan ? this.plan.price * 1 : 0;
            let modal = $g('#subscription-renew-modal'),
                price = app.renderPrice(this.total);
            modal.find('.order-subtotal-element .ba-cart-price-value').text(price);
            if (this.promo && this.plan && this.checkPromo()) {
                this.discount = this.promo.discount * 1;
                this.discount = this.promo.unit == '%' ? this.total * (this.discount / 100) : this.discount;
                this.total -= this.discount;
            } else {
                this.discount = 0;
            }
            price = app.renderPrice(this.discount);
            modal.find('.order-discount-element .ba-cart-price-value').text(price);
            this.tax = this.getProductTax(this.total, true, true, true);
            if (app.store.tax.mode == 'incl' && this.tax) {
                let title = app._('INCLUDES')+' '+this.tax.rate+'%'+' '+this.tax.title+' '+app.renderPrice(this.tax.amount);
                modal.find('.order-tax-element label').text(title);
            } else if (app.store.tax.mode == 'incl' && !this.tax) {
                let title = app._('INCLUDING_TAXES')+' '+app.renderPrice(0);
                modal.find('.order-tax-element label').text(title);
            } else if (app.store.tax.mode == 'excl' && this.tax) {
                this.total += this.tax.amount;
                price = app.renderPrice(this.tax.amount);
                modal.find('.order-tax-element .ba-cart-price-value').text(price);
            } else if (app.store.tax.mode == 'excl' && !this.tax) {
                price = app.renderPrice(0);
                modal.find('.order-tax-element .ba-cart-price-value').text(price);
            }
            price = app.renderPrice(this.total);
            modal.find('.order-total-element .ba-cart-price-value').text(price);
            if (this.plan) {
                modal.find('.apply-subscription-renew').addClass('active-button')
            } else {
                modal.find('.apply-subscription-renew').removeClass('active-button')
            }
        },
        getSubscription: function(id){
            makeFetchRequest('index.php?option=com_gridbox&task=subscriptions.getSubscription', {
                id: id
            }).then(function(json){
                let modal = $g('#view-subscriptions-dialog'),
                    btn = modal.find('.renew-subscription-btn')[0],
                    template = document.querySelector('template[data-key="subscription-order"]').content,
                    content = action = null,
                    total = price = 0,
                    wrapper = modal.find('.subscription-orders-history-wrapper .ba-options-group-wrapper').empty();
                modal.find('.subscription-details-title').text(json.title);
                modal.find('.subscription-info-wrapper .customer-info-data').each(function(){
                    this.textContent = json[this.dataset.key];
                    action = json[this.dataset.key] ? 'remove' : 'add';
                    this.closest('.ba-options-group-element').classList[action]('ba-hide-element');
                });
                if (json.expires) {
                    modal.find('.open-calendar-dialog').each(function(){
                        let array = json.expires.split(' ')[0].split('-');
                        this.dataset.year = array[0];
                        this.dataset.month = array[1] - 1;
                        this.dataset.day = array[2];
                    });
                }
                json.orders.forEach(function(order){
                    content = template.cloneNode(true);
                    price = app.renderPrice(order.total, order.currency_symbol, order.currency_position);
                    total += order.total * 1;
                    content.querySelector('.ba-options-group-label').textContent = order.order_number;
                    content.querySelector('.subscription-order-date').textContent = order.date;
                    content.querySelector('.ba-cart-price-value').textContent = price;
                    wrapper.append(content);
                });
                template = document.querySelector('template[data-key="subscription-order-total"]').content;
                content = template.cloneNode(true);
                price = app.renderPrice(total, app.store.currency.symbol, app.store.currency.position);
                content.querySelector('.ba-cart-price-value').textContent = price;
                wrapper.append(content);
                app.renew.plans = json.renew;
                app.renew.subscription = json;
                btn.classList[json.renew.length == 0 ? 'add' : 'remove']('ba-hide-element');
                modal.attr('data-id', json.id);
                if (!modal.hasClass('in')) {
                    modal.modal();
                } else {
                    let expires = json.expires.split(' ');
                    document.querySelector('tr[data-id="'+json.id+'"] .expires-td').textContent = expires[0];
                }
            });
        }
    }

    $g('body').on('click', '.subscriptions-list tbody tr', function(){
        if (!app.store.promos) {
            makeFetchRequest('index.php?option=com_gridbox&task=promocodes.getPromoCodes').then(function(json){
                app.store.promos = json;
                getProductsHtml(document.querySelector('#order-coupon-code-dialog'), json, '');
            });
        }
        app.renew.getSubscription(this.dataset.id);
    }).on('click', '.renew-subscription-btn', function(){
        let modal = $g('#subscription-renew-modal'),
            wrapper = modal.find('.subscriptions-renew-plans-wrapper'),
            price = content = title = null,
            template = document.querySelector('template[data-key="subscriptions-renew-plan"]').content;
        modal.find('.subscriptions-renew-plan').remove();
        app.renew.plan = null;
        app.renew.plans.forEach(function(plan, i){
            content = template.cloneNode(true);
            price = app.renderPrice(plan.price, app.store.currency.symbol, app.store.currency.position);
            title = plan.length.value+' '+app.renew.expires[plan.length.format];
            plan.title = title;
            content.querySelector('.ba-cart-price-value').textContent = price;
            content.querySelector('.subscriptions-renew-plan-title').textContent = title;
            content.querySelector('input').value = i;
            wrapper.prepend(content);
        });
        modal.find('.reset-coupon-code').trigger('click');
        if (modal.find('.reset-coupon-code').length == 0) {
            app.renew.calculate();
        }
        modal.modal();
    }).on('click', '.subscription-renew-coupon-code', function(){
        fontBtn = this;
        showDataTagsDialog('order-coupon-code-dialog');
    }).on('change', '.subscription-renew-coupon-code', function(){
        let promo = this.dataset.value ? JSON.parse(this.dataset.value) : null;
        app.renew.promo = promo;
        app.renew.calculate();
        this.closest('.ba-options-input-action-wrapper').querySelector('input').value = promo ? promo.code : '';
    }).on('click', '.reset-coupon-code', function(){
        $g('.subscription-renew-coupon-code').removeAttr('data-value').trigger('change');
    }).on('update', '.edit-subscription-expires input', function(){
        let id = this.closest('#view-subscriptions-dialog').dataset.id,
            $this = this,
            expires = this.value,
            div = this.closest('.ba-options-input-action-wrapper').querySelector('.customer-info-data');
        makeFetchRequest('index.php?option=com_gridbox&task=subscriptions.setExpires', {
            id: id,
            expires: expires
        }).then(function(json){
            div.textContent = json.expires;
            document.querySelector('tr[data-id="'+id+'"] .expires-td').textContent = expires;
            let array = json.expires.split(' ')[0].split('-');
            $this.dataset.year = array[0];
            $this.dataset.month = array[1] - 1;
            $this.dataset.day = array[2];
            app.showNotice(json.message);
        });
    }).on('change', '.subscriptions-renew-plan input', function(){
        app.renew.plan = app.renew.plans[this.value];
        app.renew.calculate();
    }).on('click', '.apply-subscription-renew', function(event){
        event.preventDefault();
        if (app.renew.plan) {
            let obj = {
                    subtotal: app.renew.subtotal,
                    tax: app.renew.tax,
                    total: app.renew.total,
                    discount: app.renew.discount,
                    validPromo: app.renew.validPromo,
                    plan: app.renew.plan,
                    promo: app.renew.promo,
                    id: app.renew.subscription.id,
                    user_id: app.renew.subscription.user_id,
                    product_id: app.renew.subscription.product_id,
                    sku: app.renew.subscription.sku,
                    image: app.renew.subscription.image,
                    title: app.renew.subscription.title
                },
                data = JSON.stringify(obj);
            makeFetchRequest('index.php?option=com_gridbox&task=subscriptions.setRenew', {
                data: data
            }).then(function(json){
                app.renew.getSubscription(obj.id);
                app.showNotice(app._('SUBSCRIPTION_RENEWED_SUCCESSFULLY'));
                $g('#subscription-renew-modal').modal('hide');
            });
        }
    });

    $g('#create-new-order-dialog').on('show', function(){
        if (!app.store.promos) {
            makeFetchRequest('index.php?option=com_gridbox&task=promocodes.getPromoCodes').then(function(json){
                app.store.promos = json;
                getProductsHtml(document.querySelector('#order-coupon-code-dialog'), json, '');
            });
        }
        $g(this).find('[required], .ba-options-group-sorting-wrapper').each(function(){
            app.removeAlertTooltip(this);
        });
    }).on('hide', function(){
        $g(this).removeClass('edit-created-order').find('.ba-visible-element').removeClass('ba-visible-element');
    }).on('click', '.edit-exist-order', function(){
        let modal = $g(this).closest('.modal'),
            hasShipping = false,
            footer = document.querySelector('.template-order-footer-total-wrapper').content.cloneNode(true);
        app.prepareEmptyCart(modal);
        modal.find('.order-footer-total-wrapper').html(footer);
        app.cart.order_id = app.currentOrder.id;
        app.currentOrder.products.forEach(function(product){
            let key = product.renew_id != '0' ? product.id+'_'+product.renew_id : product.product_id,
                search = '.sorting-item[data-id="'+key+'"]',
                item = modal.find(search+(product.variation ? '[data-variation="'+product.variation+'"]' : ''));
            if (!product.data) {
                item.remove();
                return;
            }
            product.data.quantity = product.quantity;
            if (product.data.stock != '' && product.data.quantity * 1 > product.data.stock * 1) {
                product.data.quantity = product.data.stock * 1;
            }
            let data = $g.extend(true, {}, product.data),
                min = data.min ? data.min * 1 : 1;
            key = data.id+(data.variation ? '+'+data.variation : '');
            if (data.quantity * 1 < min) {
                data.quantity = min;
            }
            if (product.renew_id != '0') {
                key = product.id+'_'+product.renew_id;
                data.title = product.title;
                data.price = product.price;
                data.sale_price = product.sale_price;
            }
            data.renew_id = product.renew_id;
            data.db_id = product.id;
            app.cart.products[key] = $g.extend(true, {}, data);

            if (product.product_type != 'booking' && product.product_type != 'digital' && product.product_type != 'subscription') {
                hasShipping = true;
            }
        });
        if (app.currentOrder.shipping) {
            for (let i = 0; i < app.store.shipping.length; i++) {
                if (app.store.shipping[i].id == app.currentOrder.shipping.shipping_id) {
                    modal.find('.order-shipping-method select').val(i);
                    app.cart.shipping = $g.extend(true, {}, app.store.shipping[i]);
                    app.cart.shipping.db_id = app.currentOrder.shipping.id;
                    break;
                }
            }
        }
        if (app.currentOrder.promo) {
            for (let i = 0; i < app.store.promos.length; i++) {
                if (app.store.promos[i].id == app.currentOrder.promo.promo_id) {
                    modal.find('.order-promo-code input').val(app.store.promos[i].title);
                    app.cart.promo = $g.extend(true, {}, app.store.promos[i]);
                    app.cart.promo.db_id = app.currentOrder.promo.id;
                    break;
                }
            }
            if (!app.cart.promo) {
                modal.find('.order-promo-code input').val('');
            }
        }
        modal.find('.order-shipping-method').each(function(){
            if (!this.classList.contains('empty-shipping-methods') && hasShipping) {
                this.classList.remove('ba-hide-element');
            } else {
                this.classList.add('ba-hide-element');
            }
        });
        modal.find('.order-promo-code').each(function(){
            if (!this.classList.contains('empty-promo-methods')) {
                this.classList.remove('ba-hide-element');
            } else {
                this.classList.add('ba-hide-element');
            }
        });
        if (modal.find('.order-promo-code').hasClass('ba-hide-element') && modal.find('.order-shipping-method').hasClass('ba-hide-element')) {
            modal.find('.order-methods-wrapper').addClass('ba-hide-element');
        } else {
            modal.find('.order-methods-wrapper').removeClass('ba-hide-element');
        }
        app.setTooltip(modal);
        modal.find('.customer-info-wrapper .ba-hide-element').not('.ba-tooltip').removeClass('ba-hide-element');
        app.calculateOrder();
        app.currentOrder.products.forEach(function(product){
            let key = product.renew_id != '0' ? product.id+'_'+product.renew_id : product.product_id,
                search = '.sorting-item[data-id="'+key+'"]',
                item = modal.find(search+(product.variation ? '[data-variation="'+product.variation+'"]' : ''));
            if (product.data) {
                key = product.data.id+(product.data.variation ? '+'+product.data.variation : '');
                if (product.renew_id != '0') {
                    key = product.id+'_'+product.renew_id;
                }
                let html = app.getProductSortingHTML(app.cart.products[key], app.cart.products[key].quantity);
                item.replaceWith(html);
            }
        });
        modal.find('.order-shipping-carrier').css('display', (app.cart.shipping && app.cart.shipping.carrier != 0 ? '' : 'none'));
        modal.addClass('edit-created-order').find('.order-promo-code').css('display', '');
    }).on('change', '.ba-options-group-element[data-type="country"] select[data-type="country"]', function(){
        let parent = $g(this).closest('.ba-options-group-element'),
            value = this.value;
        parent.find('select[data-type="region"]').remove();
        app.countries.forEach(function(country){
            if (country.id == value) {
                let select = document.createElement('select');
                select.dataset.type = 'region';
                country.states.forEach(function(region){
                    let option = document.createElement('option');
                    option.value = region.id;
                    option.textContent = region.title;
                    select.append(option);
                });
                parent.append(select);
            }
        });
        app.calculateOrder();
    }).on('change', '.ba-options-group-element[data-type="country"] select[data-type="region"]', function(){
        app.calculateOrder();
    }).on('click', '.download-exist-order', function(){
        let iframe = document.createElement('iframe'),
            layout = this.dataset.layout;
        iframe.className = 'download-exist-order-iframe';
        document.body.appendChild(iframe);
        iframe.src = JUri+'administrator/index.php?option=com_gridbox&view=orders&layout='+layout+'&tmpl=component&id='+this.dataset.id;
        iframe.onload = function(){
            if (layout == 'print') {
                iframe.contentWindow.print();
            }
        }
    }).on('click', '.ba-options-group-toolbar label.add-order-product', function(){
        fontBtn = this;
        $g(this).closest('.ba-options-group-sorting-wrapper').each(function(){
            app.removeAlertTooltip(this);
        });
        let modal = document.getElementById('product-applies-dialog');
        if (!modal.dataset.loaded) {
            makeFetchRequest('index.php?option=com_gridbox&task=promocodes.getProducts', {
                category: 1,
                app_type: 'products'
            }).then(function(json){
                getProductsHtml(modal, json, 'product');
                modal.dataset.loaded = 'loaded';
                showAppliesModal(modal);
            });
        } else {
            showAppliesModal(modal);
        }
    }).on('click', 'label.delete-order-product', function(){
        if (!this.classList.contains('disabled')) {
            deleteMode = 'delete-order-cart-item';
            $g('#delete-dialog').modal();
        }
    }).on('change', '.ba-options-group-toolbar label.add-order-product', function(){
        let obj = JSON.parse(this.dataset.value),
            min = obj.min ? obj.min * 1 : 1,
            key = obj.id+(obj.variation ? '+'+obj.variation : ''),
            str = app.getProductSortingHTML(obj, min);
        obj.quantity = min;
        $g('.order-info-wrapper .sorting-container').append(str)
        app.cart.products[key] = obj;
        app.calculateOrder();
        app.setTooltip(app.cart.modal);
    }).on('click', '.ba-add-product-extra-option', function(){
        if (this.classList.contains('disabled')) {
            return false;
        }
        let item = this.checkbox.closest('.sorting-item'),
            modal = document.querySelector('#extra-options-dialog'),
            ul = modal.querySelector('ul'),
            ind = item.dataset.id+(item.dataset.variation ? '+'+item.dataset.variation : ''),
            obj = app.cart.products[ind];
        ul.innerHTML = '';
        for (let id in obj.extra) {
            let row = item.querySelector('.ba-product-extra-option-row[data-ind="'+id+'"]');
            if (obj.extra[id].type != 'checkbox' && row) {
                continue;
            }
            for (let key in obj.extra[id].items) {
                if (row && row.querySelector('.ba-product-extra-option[data-key="'+key+'"]')) {
                    continue;
                }
                let li = document.createElement('li'),
                    extra = obj.extra[id].items[key],
                    price = extra.price ? app.renderPrice(extra.price) : '',
                    data = {
                        id: id,
                        key: key
                    };
                li.dataset.value = JSON.stringify(data);
                li.innerHTML = '<span class="picker-item-title"><span class="ba-picker-item-title">'+
                    obj.extra[id].title+': '+extra.title+'</span></span><span class="picker-item-price">'+price+'</span>';
                ul.append(li);
            }
        }
        modal.querySelector('input.picker-search').value = '';
        fontBtn = this;
        showDataTagsDialog('extra-options-dialog');
    }).on('change', '.ba-add-product-extra-option', function(){
        let item = this.checkbox.closest('.sorting-item'),
            ind = item.dataset.id+(item.dataset.variation ? '+'+item.dataset.variation : ''),
            obj = app.cart.products[ind],
            str = '',
            div = document.createElement('div'),
            data = JSON.parse(this.dataset.value);
        if (!obj.extra_options.items) {
            obj.extra_options = {
                count: 0,
                price: 0,
                items: {}
            }
        }
        if (!obj.extra_options.items[data.id]) {
            obj.extra_options.items[data.id] = {
                title: obj.extra[data.id].title,
                required: obj.extra[data.id].required == '1',
                values: {}
            }
        }
        obj.extra_options.items[data.id].values[data.key] = {
            price: obj.extra[data.id].items[data.key].price,
            weight: obj.extra[data.id].items[data.key].weight,
            value: obj.extra[data.id].items[data.key].title
        }
        obj.extra_options.count++;
        if (obj.extra[data.id].items[data.key].price) {
            obj.extra_options.price += obj.extra[data.id].items[data.key].price * 1;
        }
        str = app.getProductSortingHTML(obj, obj.quantity);
        app.calculateOrder();
        div.innerHTML = str;
        this.checkbox = div.querySelector('input[type="checkbox"]');
        this.checkbox.checked = true;
        $g(item).replaceWith(div.querySelector('.sorting-item'));
    }).on('click', '.download-attached-files', function(){
        this.closest('.ba-product-extra-option-row').querySelectorAll('.ba-product-attachment a').forEach((a) => {
            a.click();
        });
    }).on('click', '.remove-product-attachment', function(){
        let item = this.closest('.sorting-item'),
            ind = item.dataset.id+(item.dataset.variation ? '+'+item.dataset.variation : ''),
            row = this.closest('.ba-product-extra-option-row'),
            key = row.dataset.ind,
            product = app.cart.products[ind],
            obj = product.extra_options.items[key],
            attachments = this.closest('.ba-product-attachments'),
            attachment = this.closest('.ba-product-attachment');
            files = []
            str = '';
        obj.attachments.forEach((file) => {
            if (file.id != attachment.dataset.id) {
                files.push(file);
            }
        });
        obj.attachments = files;
        attachment.remove();        
        if (obj.attachments.length == 0) {
            row.remove();
            product.extra_options.count--;
            product.extra_options.price -= obj.price ? obj.price : 0;
            delete product.extra_options.items[key];
        } else {
            product.quantity = obj.quantity ? obj.attachments.length : product.quantity;
            product.extra_options.price -= (obj.price && obj.charge) ? obj.price : 0;
        };
        str = app.getProductSortingHTML(product, product.quantity);
        app.calculateOrder();
        $g(item).replaceWith(str);
    }).on('click', '.ba-product-delete-extra-option i', function(){
        let option = this.closest('.ba-product-extra-option'),
            row = option.closest('.ba-product-extra-option-row'),
            item = row.closest('.sorting-item'),
            ind = item.dataset.id+(item.dataset.variation ? '+'+item.dataset.variation : ''),
            obj = app.cart.products[ind],
            object = obj.extra_options.items[row.dataset.ind].values[option.dataset.key],
            str = '';
        if (object.price) {
            obj.extra_options.price -= object.price;
        }
        obj.extra_options.count--;
        delete obj.extra_options.items[row.dataset.ind].values[option.dataset.key];
        option.remove();
        if (!row.querySelector('.ba-product-extra-option[data-key]')) {
            delete obj.extra_options.items[row.dataset.ind];
            row.remove();
            
        }
        str = app.getProductSortingHTML(obj, obj.quantity);
        app.calculateOrder();
        $g(item).replaceWith(str);
    }).on('blur', '.sorting-quantity input', function(){
        let item = this.closest('.sorting-item'),
            input = item.querySelector('input[type="checkbox"]'),
            key = input.value+(input.dataset.variation ? '+'+input.dataset.variation : ''),
            product = app.cart.products[key],
            min = product.min ? product.min * 1 : 1,
            quantity = this.value ? this.value * 1 : min,
            extraPrice = price = null;
        if (this.value && product.stock != '' && product.stock < quantity) {
            quantity = product.stock * 1;
        }
        if (this.value && quantity < min) {
            quantity = min;
        }
        if (this.value != quantity) {
            this.value = quantity;
        }
    }).on('input', '.sorting-quantity input', function(){
        let item = this.closest('.sorting-item'),
            input = item.querySelector('input[type="checkbox"]'),
            key = input.value+(input.dataset.variation ? '+'+input.dataset.variation : ''),
            product = app.cart.products[key],
            min = product.min ? product.min * 1 : 1,
            quantity = this.value ? this.value * 1 : min,
            extraPrice = price = null;

        if (this.value && product.stock != '' && product.stock < quantity) {
            quantity = product.stock * 1;
        }
        if (this.value && quantity < min) {
            quantity = min;
        }
        if (this.value) {
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                product.quantity = quantity;
                app.calculateOrder();
            }, 300);
            extraPrice = product.extra_options.price ? product.extra_options.price * quantity : 0;
            price = app.renderPrice(product.price * quantity + extraPrice);
            if (product.sale_price !== '') {
                item.querySelector('.ba-cart-sale-price-wrapper .ba-cart-price-value').textContent = price;
                price = app.renderPrice(product.sale_price * quantity + extraPrice);
            }
            item.querySelector('.ba-cart-price-wrapper .ba-cart-price-value').textContent = price;
            item.querySelectorAll('.ba-cart-product-extra-option-price').forEach(function(extra){
                if (extra.dataset.price) {
                    price = app.renderPrice(extra.dataset.price * quantity);
                    extra.textContent = price;
                }
            })
        }
    }).on('click', '.set-order-user', function(){
        showUsersDialog(0, this);
    }).on('change', '.set-order-user', function(){
        let info = this.userInfo,
            obj = this.dataset.value ? JSON.parse(this.dataset.value) : null;
        $g(this).closest('.customer-info-wrapper').find('.ba-options-group-element').each(function(){
            if (this.dataset.type == 'user') {
                this.querySelectorAll('input').forEach(function(input){
                    input.value = obj ? obj.username : '';
                    input.dataset.value = obj ? obj.id : '';
                });
            } else if (this.dataset.type == 'text' || this.dataset.type == 'email'
                || this.dataset.type == 'textarea' || this.dataset.type == 'dropdown') {
                this.querySelectorAll('input, textarea').forEach(function(input){
                    input.value = info[input.name] ? info[input.name].value : '';
                });
            } else if (this.dataset.type == 'checkbox' || this.dataset.type == 'radio') {
                this.querySelectorAll('input').forEach(function(input){
                    input.checked = info[input.name] ? info[input.name].value == input.value : false;
                });
            } else if (this.dataset.type == 'country') {
                let input = this.querySelector('input')
                    select = this.querySelector('select[data-type="country"]');
                if (info[input.name]) {
                    let object = JSON.parse(info[input.name].value);
                    $g(select).val(object.country).trigger('change');
                    $g(this).find('select[data-type="region"]').trigger('change');
                    input.value = info[input.name].value;
                } else {
                    $g(select).val('').trigger('change');
                    input.value = '';
                }
            }
        });
    }).on('click', '.reset-order-user', function(){
        let btn = $g('.set-order-user');
        btn[0].userInfo = {};
        btn.removeAttr('data-value').trigger('change');
    }).on('click', '.order-coupon-code', function(){
        fontBtn = this;
        showDataTagsDialog('order-coupon-code-dialog');
    }).on('change', '.order-coupon-code', function(){
        app.cart.promo = this.dataset.value ? JSON.parse(this.dataset.value) : null;
        this.closest('.ba-options-input-action-wrapper').querySelector('input').value = app.cart.promo ? app.cart.promo.code : '';
        app.calculateOrder();
    }).on('click', '.reset-coupon-code', function(){
        $g('.order-coupon-code').removeAttr('data-value').trigger('change');
    }).on('change', '.select-order-shipping', function(){
        app.cart.shipping = this.value ? app.store.shipping[this.value] : null;
        if (app.cart.shipping.carrier != 0) {
            $g('.order-shipping-carrier').css('display', '');
        } else {
            $g('.order-shipping-carrier').css('display', 'none');
        }
        app.calculateOrder();
    }).on('focus change', '[required]', function(){
        app.removeAlertTooltip(this);
    }).on('click', '.back-order-cart', function(){
        app.setCurrentOrder(app.currentOrder);
        $g('#create-new-order-dialog').removeClass('edit-created-order');
    }).on('click', '.save-order-cart', function(){
        if (this.clicked) {
            return false;
        }
        let modal = $g('#create-new-order-dialog'),
            not = '[type="checkbox"], [type="radio"]';
        modal.find('input[required], textarea[required], select[required]').not(not).each(function(){
            if (!this.closest('.ba-hide-element')) {
                let alert = !this.value.trim(),
                    key = 'THIS_FIELD_REQUIRED';
                if (this.value && this.type == 'email') {
                    alert = !(/@/g.test(this.value) && this.value.match(/@/g).length == 1);
                    key = 'ENTER_VALID_VALUE';
                }
                app.toggleAlertTooltip(alert, this, this.closest('.ba-options-group-element'), key);
            }
        });
        modal.find('[data-type="checkbox"], [data-type="radio"], [data-type="acceptance"]').each(function(){
            let alert = this.querySelector('input[required]') ? true : false;
            this.querySelectorAll('input[type="radio"][required], input[type="checkbox"][required]').forEach(function($this){
                if ($this.checked) {
                    alert = false;
                }
            });
            app.toggleAlertTooltip(alert, this, this, 'THIS_FIELD_REQUIRED');
        });
        modal.find('.ba-options-group-sorting-wrapper').each(function(){
            let alert = this.querySelectorAll('.sorting-item').length == 0,
                key = 'THIS_FIELD_REQUIRED';
            app.toggleAlertTooltip(alert, this, this, key);
        });
        let action = app.cart.order_id ? 'updateOrder' : 'createOrder',
            obj = $g.extend(true, {}, app.cart),
            alert = modal.find('.ba-alert'),
            $this = this,
            wrapper = $g('.customer-info-wrapper');
        if (alert.length) {
            alert[0].scrollIntoView(true);
            return false;
        }
        if (obj.shipping && obj.shipping.carrier != 0) {
            obj.carrier = $g('.enter-carrier-address').val().trim();
        }
        obj.info = {};
        wrapper.find('input[name], textarea[name], select[name]').not(not).each(function(){
            let parent = this.closest('.ba-options-group-element'),
                value = this.value.trim();
            if (this.type == 'hidden' && parent.dataset.type == 'country') {
                let country = parent.querySelector('select[data-type="country"]'),
                    region = parent.querySelector('select[data-type="region"]'),
                    object = {
                        country: country.value,
                        region: region ? region.value : ''
                    };
                value = JSON.stringify(object);
            } else if (parent.dataset.type == 'user') {
                value = this.dataset.value ? this.dataset.value : 0;
            }
            obj.info[this.name] = value;
        });
        wrapper.find('[data-type="checkbox"], [data-type="radio"], [data-type="acceptance"]').each(function(){
            let values = [],
                name = '';
            this.querySelectorAll('input').forEach(function(input){
                name = input.name;
                if (input.checked) {
                    values.push(input.value);
                }
            });
            obj.info[name] = values.join('; ');
        });
        this.clicked = true;
        delete(obj.modal);
        app.showLoading('SAVING')
        makeFetchRequest('index.php?option=com_gridbox&task=orders.'+action, {
            data: JSON.stringify(obj)
        }).then(function(json){
            $this.clicked = false;
            if (app.cart.order_id) {
                showNotice(app._('SAVE_SUCCESS'));
                let total = app.cart.modal.find('.order-total-element .ba-cart-price-value').text(),
                    email = name = '';
                app.cart.modal.find('.customer-info-wrapper .ba-options-group-element').each(function(){
                    let input = this.querySelector('input, textarea, select'),
                        text = input.value.trim();
                    this.querySelector('.customer-info-data').textContent = text;
                    if (input.type == 'email') {
                        email = text;
                    } else if (input.dataset.customer == 1) {
                        name = text;
                    }
                    if (!text) {
                        this.classList.add('ba-hide-element');
                    }
                });
                app.cart.modal.find('.order-shipping-method').each(function(){
                    let select = this.querySelector('select'),
                        text = select.value == '' ? '' : select.querySelector('option[value="'+select.value+'"]').textContent,
                        carrier = app.cart.shipping && app.cart.shipping.carrier != 0 ? $g('.enter-carrier-address').val().trim() : '';
                    this.querySelector('.customer-info-data').textContent = text+(carrier ? ' - '+carrier : '');
                });
                app.cart.modal.find('.order-shipping-carrier').css('display', 'none');
                app.cart.modal.removeClass('edit-created-order');
                $g('tr[data-id="'+app.currentOrder.id+'"]').each(function(){
                    this.querySelector('.customer-td').textContent = name;
                    this.querySelector('.email-td').textContent = email;
                    this.querySelector('.total-td').textContent = total;
                });
            } else {
                app.cart.modal.modal('hide');
                reloadPage(app._('ITEM_CREATED'));
            }
        });
    });
    $g('body').on('click', '.product-options-table tbody tr', function(){
        let $this = this;
        makeFetchRequest('index.php?option=com_gridbox&task=productoptions.getOptions', {
            id: this.dataset.id
        }).then(function(json){
            if (json) {
                let settings = JSON.parse(json.options),
                    container = document.querySelector('.sorting-container');
                if (!json.file_options) {
                    let file_options = {
                        multiple: false,
                        size: 2000,
                        count: '',
                        types: 'csv,doc,gif,ico,jpg,jpeg,pdf,png,txt,xls,svg,mp4,webp',
                        quantity: false,
                        charge: false,
                        droppable: true
                    };
                    json.file_options = JSON.stringify(file_options)
                }
                json.file = JSON.parse(json.file_options)
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
                document.querySelectorAll('.ba-options-group-toolbar label[data-action="delete"]').forEach(function(el){
                    el.classList.add('disabled');
                });
                $this.classList.add('active');
                document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function(el){
                    if (el.type == 'checkbox') {
                        el.checked = Boolean(json[el.dataset.key] * 1);
                    } else {
                        el.value = json[el.dataset.key];
                    }
                });
                document.querySelectorAll('.ba-options-group-element [data-file]').forEach(function(el){
                    if (el.type == 'checkbox') {
                        el.checked = json.file[el.dataset.file];
                    } else {
                        el.value = json.file[el.dataset.file];
                    }
                });
                document.querySelector('.product-options-files-count').style.display = json.file.multiple ? '' : 'none';
                container.innerHTML = '';
                container.classList.remove('color-picker-sorting-item');
                container.classList.remove('image-picker-sorting-item');
                if (json.field_type == 'image' || json.field_type == 'color') {
                    container.classList.add(json.field_type+'-picker-sorting-item');
                }
                if (json.field_type == 'file') {
                    document.querySelector('.product-options-values').style.display = 'none';
                    document.querySelector('.product-options-file-type').style.display = '';
                } else if (json.field_type == 'textinput' || json.field_type == 'textarea') {
                    document.querySelector('.product-options-values').style.display = 'none';
                    document.querySelector('.product-options-file-type').style.display = 'none';
                } else {
                    document.querySelector('.product-options-values').style.display = '';
                    document.querySelector('.product-options-file-type').style.display = 'none';
                }
                settings.forEach(function(obj){
                    container.append(getSortingItem(obj));
                });
            }
        });
    }).on('change', 'input[data-file="multiple"]', function(){
        document.querySelector('.product-options-files-count').style.display = this.checked ? '' : 'none';
    }).on('click', '.ba-options-group-toolbar label[data-action="add"][data-object]', function(){
        let obj = app.objects[this.dataset.object];
        obj.key = +(new Date());
        this.closest('.ba-options-group-element').querySelector('.sorting-container').append(getSortingItem(obj))
    }).on('change', 'select[data-key="field_type"]', function(){
        let container = document.querySelector('.sorting-container');
        container.classList.remove('color-picker-sorting-item');
        container.classList.remove('image-picker-sorting-item');
        if (this.value == 'image' || this.value == 'color') {
            container.classList.add(this.value+'-picker-sorting-item');
        }
        if (this.value == 'file') {
            document.querySelector('.product-options-values').style.display = 'none';
            document.querySelector('.product-options-file-type').style.display = '';
        } else if (this.value == 'textinput' || this.value == 'textarea') {
            document.querySelector('.product-options-values').style.display = 'none';
            document.querySelector('.product-options-file-type').style.display = 'none';
        } else {
            document.querySelector('.product-options-values').style.display = '';
            document.querySelector('.product-options-file-type').style.display = 'none';
        }
    }).on('click', '.sorting-image-picker', function(){
        fontBtn = this;
        uploadMode = 'sortingImage';
        checkIframe($g('#uploader-modal'), 'uploader');
    }).on('change', '.sorting-checkbox input', function(){
        let checked = {
                count: 0,
                checkbox: null,
                flag: false
            };
        if (this.dataset.ind == 'new' || this.dataset.ind == 'completed' || this.dataset.ind == 'refunded') {
            this.checked = false;
            return false;
        }
        this.closest('.sorting-container').querySelectorAll('.sorting-checkbox input').forEach(function($this){
            if ($this.checked) {
                checked.flag = $this.checked;
                checked.count++;
                checked.checkbox = $this;
            }
        });
        this.closest('.ba-options-group-element').querySelectorAll('label[data-action="delete"]').forEach(function($this){
            $this.classList[checked.flag ? 'remove' : 'add']('disabled');
        });
        if (this.name == 'product' && checked.count == 1) {
            let item = checked.checkbox.closest('.sorting-item'),
                btn = document.querySelector('.ba-add-product-extra-option'),
                ind = item.dataset.id+(item.dataset.variation ? '+'+item.dataset.variation : ''),
                obj = app.cart.products[ind];
            if (Object.keys(obj.extra).length != 0 && checked.flag) {
                btn.classList.remove('disabled');
                btn.checkbox = checked.checkbox;
            } else {
                btn.classList.add('disabled');
            }
        }
    }).on('click', '.ba-options-group-toolbar label[data-action="delete"]:not(.delete-order-product)', function(){
        if (!this.classList.contains('disabled')) {
            deleteMode = {
                type: 'delete-sorting-item',
                container: this.closest('.ba-options-group-element').querySelector('.sorting-container'),
                btn: this
            }
            $g('#delete-dialog').modal();
        }
    }).on('click', '.apply-product-options', function(){
        if (!this.classList.contains('disabled')) {
            let obj = {
                    id: this.closest('.twin-view-right-sidebar').dataset.edit
                },
                options = [],
                file = {};
            document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.type == 'checkbox' ? Number($this.checked) : $this.value;
            });
            document.querySelectorAll('.ba-options-group-sorting-wrapper .sorting-item').forEach(function($this){
                let item = {
                    title: $this.querySelector('.sorting-title input').value.trim(),
                    image: $this.querySelector('.sorting-image-picker').dataset.image,
                    color: $this.querySelector('.sorting-color-picker input').dataset.rgba,
                    key: $this.querySelector('input[type="checkbox"]').dataset.ind
                }
                options.push(item);
            });
            document.querySelectorAll('.ba-options-group-element [data-file]').forEach(function(el){
                if (el.type == 'checkbox') {
                    file[el.dataset.file] = el.checked;
                } else {
                    file[el.dataset.file] = el.value;
                }
            });
            obj.file_options = JSON.stringify(file);
            obj.options = JSON.stringify(options);
            makeFetchRequest('index.php?option=com_gridbox&task=productoptions.updateProductoptions', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    }).on('click', '.delete-product-options', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            deleteMode = 'productoptions.contextDelete';
            $g('#delete-dialog').modal();
        }
    });

    $g('body').on('click', '.sales-table tbody tr', function(){
        let $this = this,
            data = {
                id: this.dataset.id
            };
        makeFetchRequest('index.php?option=com_gridbox&task=sales.getOptions', data).then(function(json){
            if (json) {
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                $this.classList.add('active');
                let decimals = 0,
                    symbol = '%';
                document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function(el){
                    if (el.type == 'checkbox') {
                        el.checked = Boolean(json[el.dataset.key] * 1);
                    } else {
                        el.value = json[el.dataset.key];
                    }
                    app.removeAlertTooltip(el);
                    if (el.dataset.key == 'unit') {
                        decimals = json.unit == '%' ? 2 : el.dataset.decimals;
                        symbol = json.unit == '%' ? '%' : el.dataset.symbol;
                    }
                });
                document.querySelector('.ba-options-price-currency').textContent = symbol;
                document.querySelector('.coupon-type-select input').dataset.decimals = decimals;
                $g('.coupon-type-select input').trigger('input');
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
                prepareCouponApplies(json.applies_to);
                json.map.forEach(function(obj){
                    createSelectedApplies(obj, json.applies_to);
                });
            }
        });
    }).on('click', '.apply-sales', function(){
        if (!this.classList.contains('disabled')) {
            let required = ['title', 'code', 'discount'],
                alert = false,
                obj = {
                    id: this.closest('.twin-view-right-sidebar').dataset.edit
                },
                map = [];
            document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.type == 'checkbox' ? Number($this.checked) : $this.value;
                if (required.indexOf($this.dataset.key) != -1 && $this.value == '') {
                    let parent = $this.closest('.ba-options-price-wrapper, .ba-options-group-element');
                    alert = true;
                    app.toggleAlertTooltip(alert, $this, parent, 'THIS_FIELD_REQUIRED');
                }
            });
            if (alert) {
                return false;
            }
            document.querySelectorAll('.selected-applies').forEach(function($this){
                map.push({
                    id: $this.dataset.id,
                    variation: $this.dataset.variation ? $this.dataset.variation : ''
                });
            });
            obj.map = JSON.stringify(map);
            makeFetchRequest('index.php?option=com_gridbox&task=sales.updateSales', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    }).on('click', '.duplicate-sales', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            Joomla.submitbutton('sales.contextDuplicate');
        }
    }).on('click', '.delete-sales', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            deleteMode = 'sales.contextDelete';
            $g('#delete-dialog').modal();
        }
    });

    $g('body').on('click', '.promo-codes-table tbody tr', function(){
        let $this = this,
            data = {
                id: this.dataset.id
            };
        makeFetchRequest('index.php?option=com_gridbox&task=promocodes.getOptions', data).then(function(json){
            if (json) {
                document.querySelector('.twin-view-right-sidebar').dataset.edit = json.id;
                document.querySelectorAll('tr.active').forEach(function(tr){
                    tr.classList.remove('active');
                });
                $this.classList.add('active');
                let decimals = 0,
                    symbol = '%';
                document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function(el){
                    if (el.type == 'checkbox') {
                        el.checked = Boolean(json[el.dataset.key] * 1);
                    } else {
                        el.value = json[el.dataset.key];
                    }
                    app.removeAlertTooltip(el);
                    if (el.dataset.key == 'unit') {
                        decimals = json.unit == '%' ? 2 : el.dataset.decimals;
                        symbol = json.unit == '%' ? '%' : el.dataset.symbol;
                    }
                });
                document.querySelector('.ba-options-price-currency').textContent = symbol;
                document.querySelector('.coupon-type-select input').dataset.decimals = decimals;
                $g('.coupon-type-select input').trigger('input');
                document.querySelectorAll('.twin-view-sidebar-header > span.disabled').forEach(function(el){
                    el.classList.remove('disabled');
                });
                prepareCouponApplies(json.applies_to);
                json.map.forEach(function(obj){
                    createSelectedApplies(obj, json.applies_to);
                });
            }
        });
    }).on('change', '.coupon-type-select select', function(){
        let symbol = this.value == '%' ? '%' : this.dataset.symbol,
            decimals = this.value == '%' ? 2 : this.dataset.decimals;
        this.closest('.coupon-type-select').querySelector('.ba-options-price-currency').textContent = symbol;
        document.querySelector('.coupon-type-select input').dataset.decimals = decimals;
        $g('.coupon-type-select input').trigger('input');
    }).on('change', '.ba-options-group-applies-wrapper select', function(){
        prepareCouponApplies(this.value);
    }).on('click', '.ba-options-applies-wrapper i', function(){
        fontBtn = this;
        let modal = document.getElementById(this.dataset.modal);
        if (!modal.dataset.loaded) {
            let url = 'index.php?option=com_gridbox&task=promocodes.get';
            url += fontBtn.dataset.type == 'category' ? 'Categories' : 'Products';
            makeFetchRequest(url).then(function(json){
                getProductsHtml(modal, json, fontBtn.dataset.type);
                modal.dataset.loaded = 'loaded';
                showAppliesModal(modal)
            });
        } else {
            showAppliesModal(modal);
        }
    }).on('change', '.ba-options-applies-wrapper i', function(){
        let obj = JSON.parse(this.dataset.value);
        createSelectedApplies(obj, this.dataset.type);
    }).on('click', '.remove-selected-items', function(){
        this.closest('.selected-items').remove();
    }).on('click', '.apply-promo-code', function(){
        if (!this.classList.contains('disabled')) {
            let required = ['title', 'code', 'discount'],
                alert = false,
                obj = {
                    id: this.closest('.twin-view-right-sidebar').dataset.edit
                },
                map = [];
            document.querySelectorAll('.ba-options-group-element [data-key]').forEach(function($this){
                obj[$this.dataset.key] = $this.type == 'checkbox' ? Number($this.checked) : $this.value;
                if (required.indexOf($this.dataset.key) != -1 && $this.value == '') {
                    let parent = $this.closest('.ba-options-price-wrapper, .ba-options-group-element');
                    alert = true;
                    app.toggleAlertTooltip(alert, $this, parent, 'THIS_FIELD_REQUIRED');
                }
            });
            if (alert) {
                return false;
            }
            document.querySelectorAll('.selected-applies').forEach(function($this){
                map.push({
                    id: $this.dataset.id,
                    variation: $this.dataset.variation ? $this.dataset.variation : ''
                });
            });
            obj.map = JSON.stringify(map);
            makeFetchRequest('index.php?option=com_gridbox&task=promocodes.updatePromoCode', obj).then(function(json){
                if (json) {
                    reloadPage(json.message);
                }
            });
        }
    }).on('click', '.duplicate-promo-code', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            Joomla.submitbutton('promocodes.contextDuplicate');
        }
    }).on('click', '.delete-promo-code', function(){
        if (!this.classList.contains('disabled')) {
            document.querySelector('#context-item').value = this.closest('.twin-view-right-sidebar').dataset.edit;
            deleteMode = 'promocodes.contextDelete';
            $g('#delete-dialog').modal();
        }
    });
    
    $g('.store-email-options-filter').on('change', function(){
        $g(this).closest('.ba-options-group-wrapper').find('> div[class*="-email-options"]').hide();
        $g(this).closest('.ba-options-group-wrapper').find('.'+this.value+'-email-options').css('display', '');
    });

    $g('.set-resized-ckeditor-data').on('click', function(event){
        event.preventDefault();
        let data = app.cke.resized.getData();
        this.ckeditor.setData(data)
        $g('#resized-ckeditor-dialog').modal('hide');
    });

    $g('#store-general-options').each(() => {
        let = checkout = {};
        document.querySelectorAll('input[data-group="checkout"], select[data-group="checkout"]').forEach(function(input){
            checkout[input.dataset.key] = input;
            input.checkout = checkout;
        });
    });
    $g('input[data-group="checkout"][data-key="login"]').on('change', function(){
        if (!this.checkout.login.checked && !this.checkout.guest.checked) {
            this.checkout.guest.checked = true;
        }
        let flag = this.checkout.login.checked;
        for (let ind in this.checkout) {
            if (ind == 'login' || ind == 'guest') {
                continue;
            }
            flag = this.checkout.login.checked;
            if (ind == 'terms') {
                flag = this.checkout.login.checked && this.checkout.registration.checked;
            }
            this.checkout[ind].closest('.ba-options-group-element').style.display = flag || ind == 'minimum' ? '' : 'none';
        }
    });
    $g('input[data-group="checkout"][data-key="guest"]').on('change', function(){
        if (!this.checkout.login.checked && !this.checkout.guest.checked) {
            this.checkout.guest.checked = true;
        }
    });
    $g('input[data-group="checkout"][data-key="registration"]').on('change', function(){
        let flag = this.checkout.login.checked && this.checkout.registration.checked;
        this.checkout.terms.closest('.ba-options-group-element').style.display = flag ? '' : 'none';
    });

    $g('.apply-store-settings').on('click', function(){
        let data = {
                id: this.dataset.id,
                notifications: JSON.stringify(app.notifications),
                currencies: app.currencies,
                added: app.added
            };
        document.querySelectorAll('.store-settings-table [data-key]').forEach(function($this){
            let value = $this.value;
            if (!data[$this.dataset.group]) {
                data[$this.dataset.group] = {}
            }
            if ($this.type == 'checkbox') {
                value = $this.checked;
            } else if ($this.closest('.ckeditor-options-wrapper')) {
                value = app.cke[$this.dataset.group+'-'+$this.dataset.key].getData();
            }
            data[$this.dataset.group][$this.dataset.key] = value;
        });
        data.general = app.store.general;
        data.invoice = app.store.invoice;
        data.statuses = [];
        data.tax.rates = [];
        for (let ind in app.statuses) {
            if (ind != 'stock' && ind != 'reminder') {
                delete app.statuses[ind];
            }
        }
        $g('#store-order-statuses-options .sorting-item').each(function(){
            let obj = {
                "title": this.querySelector('.sorting-title input').value,
                "color": this.querySelector('.sorting-color-picker input').dataset.rgba,
                "key": this.querySelector('.sorting-checkbox input').dataset.ind
            }
            data.statuses.push(obj);
            app.statuses[obj.key] = obj;
        });
        $g('.email-notification-status-select ul').each(function(){
            let str = '';
            data.statuses.forEach((status) => {
                str += '<li data-value="'+status.key+'" data-color="'+status.color+'" style="--status-color: '+
                    status.color+';">'+status.title+'</li>';
            });
            this.innerHTML = str;
        });
        $g('.notification-status').each(function(){
            let status = this.dataset.status;
            if (status && app.statuses[status]) {
                this.style.setProperty('--status-color', app.statuses[status].color);
                this.textContent = app.statuses[status].title;
            } else {
                this.style.removeProperty('--status-color');
                this.textContent = '';
            }
        });
        $g('#store-tax-options .sorting-container .sorting-item').each(function(){
            let $this = this,
                country = this.querySelector('.sorting-tax-country .selected-items'),
                obj = {
                    title: this.querySelector('.sorting-title input').value.trim(),
                    rate: this.querySelector('.sorting-tax-rate input:nth-child(1)').value.trim(),
                    categories: [],
                    country_id: country ? country.dataset.id : '',
                    regions: [],
                    shipping: Boolean(this.querySelector('.show-more-tax-options').dataset.shipping * 1)
                }
            this.querySelectorAll('.sorting-tax-category-wrapper .selected-items').forEach(function(category){
                obj.categories.push(category.dataset.id);
            });
            this.querySelectorAll('.tax-country-state .selected-items').forEach(function(state){
                let ind = $g(state).index();
                obj.regions.push({
                    state_id: state.dataset.id,
                    rate: $this.querySelector('.sorting-tax-rate input:nth-child('+(ind + 1)+')').value
                });
            });
            data.tax.rates.push(obj);
        });
        for (let ind in data) {
            if (typeof data[ind] == 'object') {
                data[ind] = JSON.stringify(data[ind]);
            }
        }
        makeFetchRequest('index.php?option=com_gridbox&task=storesettings.updateSettings', data).then(function(json){
            if (json) {
                showNotice(json.message);
            }
        });
    });

    $g('.edit-login-acceptance').on('click', () => {
        let value = document.querySelector('textarea[data-key="terms_text"][data-group="checkout"]').value;
        app.cke.acceptance.setData(value);
        $g('#acceptance-html-modal').modal();
    });

    $g('.apply-acceptance-html').on('click', () => {
        document.querySelector('textarea[data-key="terms_text"][data-group="checkout"]').value = app.cke.acceptance.getData();
        $g('#acceptance-html-modal').modal('hide');
    })

    $g('#store-email-options .sorting-container').on('change', function(){
        let notifications = [];
        this.querySelectorAll('.notification-sorting-item').forEach((item, i) => {
            notifications.push(app.notifications[item.dataset.ind * 1]);
            item.dataset.ind = i;
        });
        app.notifications = notifications;
    });

    $g('#store-email-options .sorting-container').on('click', '.notification-sorting-item', function(event){
        if (event.target.closest('.sorting-icon, .sorting-checkbox')) {
            return;
        }
        let i = this.dataset.ind * 1,
            obj = app.notifications[i],
            str = status = '',
            modal = $g('#add-email-notification-modal');
        if (!obj.delay) {
            obj.delay = {
                enabled: false,
                value: "",
                format: "d"
            }
        }
        if (!obj.key) {
            obj.key = +new Date();
        }
        for (let ind in obj) {
            if (ind == 'body') {
                app.cke.body.setData(obj[ind]);
            } else if (ind == 'admins') {
                obj[ind].forEach((email) => {
                    str += '<span class="entered-emails selected-items" data-email="'+email+'">'+
                        '<span class="selected-items-name">'+email+'</span>'+
                        '<i class="zmdi zmdi-close remove-selected-items"></i></span>';
                });
                modal.find('[data-key="admins"]').html(str);
            } else if (ind == 'status') {
                if (app.systemStatuses.indexOf(obj.status) != -1) {
                    status = app.statuses[obj.status].title;
                }
                modal.find('.email-notification-status-select').each(function(){
                    this.querySelector('input[type="hidden"]').value = obj[ind];
                    let li = this.querySelector('li[data-value="'+obj[ind]+'"]');
                    if (li) {
                        this.querySelector('input[type="text"]').value = li.textContent.trim();
                        this.style.setProperty('--status-color', li.dataset.color);
                    } else if (status) {
                        this.querySelector('input[type="text"]').value = status;
                        this.style.setProperty('--status-color', '#ff4f49');
                    } else {
                        this.querySelector('input[type="text"]').value = '';
                        this.style.removeProperty('--status-color');
                    }
                });
            } else if (ind == 'delay') {
                modal.find('.email-sending-delay-checkbox input').prop('checked', obj.delay.enabled);
                modal.find('.email-sending-delay-options').css('display', obj.delay.enabled ? '' : 'none').find('input, select').each((i, el) => {
                    el.value = obj.delay[el.dataset.key];
                })
            } else if (ind == 'reminder') {
                modal.find('.notification-appointment-reminder-options').find('input, select').each((i, el) => {
                    el.value = obj.reminder[el.dataset.key];
                })
            } else {
                modal.find('[data-key="'+ind+'"]').val(obj[ind]);
            }
        }
        if (app.systemStatuses.indexOf(obj.status) != -1) {
            modal.find('.notification-status-option, .notification-recipient-option, .email-sending-delay-options-wrapper').addClass('disabled');
        } else {
            modal.find('.notification-status-option, .notification-recipient-option, .email-sending-delay-options-wrapper').removeClass('disabled');
        }
        $g('.notification-recipient-option select').trigger('change');
        modal.find('.notification-stock-options')['quantity' in obj ? 'removeClass' : 'addClass']('ba-hide-element');
        modal.find('.notification-appointment-reminder-options')['reminder' in obj ? 'removeClass' : 'addClass']('ba-hide-element');
        modal.find('.apply-email-notification').attr('data-ind', i);
        modal.modal();
    });

    $g('#add-email-notification-modal .email-sending-delay-checkbox input').on('change', function(){
        $g('#add-email-notification-modal .email-sending-delay-options').css('display', this.checked ? '' : 'none');
    });

    $g('.add-email-notification').on('click', function(){
        let modal = $g('#add-email-notification-modal'),
            content = document.querySelector('template.empty-notification').cloneNode(true).innerHTML;
        modal.find('.notification-stock-options').addClass('ba-hide-element');
        modal.find('input, select').val('');
        modal.find('input[data-key="enabled"]').prop('checked', false);
        modal.find('.email-sending-delay-options').css('display', 'none');
        modal.find('.entered-emails-wrapper').html('');
        $g('.notification-recipient-option select').val('admin').trigger('change');
        app.cke.body.setData(content);
        modal.find('.apply-email-notification').attr('data-ind', -1);
        modal.find('.disabled').removeClass('disabled');
        modal.find('.email-notification-status-select')[0].style.removeProperty('--status-color');
        modal.modal();
    });

    $g('.trigger-general-modals').on('click', function(){
        let modal = $g('#'+this.dataset.modal);
        modal.find('[data-key]').each(function(){
            this.value = app.store[this.dataset.group][this.dataset.key];
        });
        modal.modal();
    });

    $g('.apply-store-settings-modal').on('click', function(){
        let modal = $g(this).closest('.modal');
        modal.find('[data-key]').each(function(){
            app.store[this.dataset.group][this.dataset.key] = this.value;
        });
        modal.modal('hide');
    });

    $g('#invoice-modal .set-invoice-logo').on('click', function(){
        checkIframe($g('#uploader-modal'), 'uploader');
        fontBtn = document.querySelector('#invoice-modal input[data-key="logo"]');
        uploadMode = 'invoiceLogo';
    });

    $g('.select-invoice-data-tags').on('click', function(){
        fontBtn = this;
        $g('.invoice-all-fields').css('display', '');
        document.querySelector('#data-tags-dialog').dataset.view = this.dataset.target;
        showDataTagsDialog('data-tags-dialog');
    });

    $g('#store-currency-options .sorting-container').on('change', function(){
        let currencies = [];
        this.querySelectorAll('.currency-sorting-item').forEach((item, i) => {
            currencies.push(app.currencies.list[item.dataset.ind * 1]);
            item.dataset.ind = i;
        });
        app.currencies.list = currencies;
    });

    $g('#store-currency-options input[data-key="auto"]').on('change', function(){
        app.currencies.auto = this.checked;
    });

    $g('#store-currency-options .sorting-container').on('click', '.currency-sorting-item', function(event){
        if (event.target.closest('.sorting-icon, .sorting-checkbox, .sorting-currency-default')) {
            return;
        }
        let i = this.dataset.ind * 1,
            obj = app.currencies.list[i],
            disabled = obj.default || app.currencies.auto,
            value = '',
            str = '',
            modal = $g('#add-currency-modal');
        modal.find('[data-key]').each(function(){
            value = obj[this.dataset.key];
            this.value = this.dataset.key == 'rate' ? Number(value).toFixed(2) : value;
        });
        modal.find('.ba-alert').find('input, select').each(function(){
            app.removeAlertTooltip(this);
        });
        modal.find('.apply-currency').attr('data-ind', i);
        modal.attr('data-default', Number(disabled)).modal();
    });

    $g('#store-currency-options .sorting-container').on('click', '.sorting-currency-default[data-default="0"]', function(event){
        let item = this.closest('.currency-sorting-item'),
            i = item.dataset.ind * 1,
            obj = app.currencies.list[i];
        app.currencies.list.forEach((object, ind) => {
            if (object.default) {
                object.default = false;
                document.querySelectorAll('.currency-sorting-item[data-ind="'+ind+'"]').forEach((div) => {
                    div.querySelector('.sorting-currency-default').dataset.default = 0;
                    div.querySelector('input[type="checkbox"]').removeAttribute('disabled');
                });
            }
        });
        this.dataset.default = 1;
        obj.default = true;
        obj.rate = 1;
        item.querySelector('.sorting-currency-rate').textContent = Number(obj.rate).toFixed(2);
        item.querySelector('input[type="checkbox"]').setAttribute('disabled', true);
        app.exchangerates.check();
    });

    app.exchangerates = {
        check:() => {
            let data = app.exchangerates.getData(),
                flag = app.currencies.auto && document.querySelector('.auto-exchangerates').dataset.configured == 1 && app.currencies.list.length > 1;
            if (!flag) {
                return;
            }
            if (!app.exchangerates.isValid(data)) {
                app.exchangerates.request(data);
            }
        },
        getData: ()  => {
            let data = {
                    symbols: []
                }
            app.currencies.list.forEach((obj) => {
                if (obj.default) {
                    data.base = obj.code;
                } else {
                    data.symbols.push(obj.code);
                }
            });

            return data
        },
        isValid: (data) => {
            let flag = data.base == app.exchangerates_data.base;
            data.symbols.forEach((symbol) => {
                if (flag && !app.exchangerates_data.rates[symbol]) {
                    flag = false;
                }
            });

            return flag;
        },
        request: (data) => {
            app.fetch('index.php?option=com_gridbox&task=storesettings.getExchangerates', data).then((text) => {
                return JSON.parse(text)
            }).then((json) => {
                app.exchangerates_data = json;
                app.currencies.list.forEach((object, ind) =>{
                    if (!object.default) {
                        object.rate = json.rates[object.code] || 1;
                        document.querySelectorAll('.currency-sorting-item[data-ind="'+ind+'"]').forEach((div) => {
                            div.querySelector('.sorting-currency-rate').textContent = Number(object.rate).toFixed(2);
                        });
                    }
                });
            });
        }
    }

    $g('.add-currency').on('click', function(){
        let modal = $g('#add-currency-modal'),
            disabled = app.currencies.auto;
        modal.find('input, select').val('');
        modal.find('select[data-key="separator"]').val(',');
        modal.find('input[data-key="decimals"]').val(2);
        modal.find('select[data-key="language"]').val('*');
        modal.find('.ba-alert').find('input, select').each(function(){
            app.removeAlertTooltip(this);
        });
        modal.find('.apply-currency').attr('data-ind', -1);
        modal.find('.disabled').removeClass('disabled');
        modal.attr('data-default', Number(disabled)).modal();
    });

    $g('#add-currency-modal input[data-key="code"]').on('input', function(){
        if (!app.currencies.auto) {
            return;
        }
        let $this = this;
        clearTimeout(this.delay);
        this.delay = setTimeout(() => {
            let code = $this.value.trim();
            if (code.length !== 3) {
                return;
            }
            let headers = new Headers(),
                data = app.exchangerates.getData(),
                options = {
                    method: 'GET',
                    redirect: 'follow',
                    headers: headers
                };
            headers.append("apikey", app.exchangerates_key);
            fetch("https://api.apilayer.com/exchangerates_data/latest?symbols="+code+"&base="+data.base, options)
                .then(response => response.text())
                .then((text) => {
                    let json = JSON.parse(text),
                        value = json.rates && json.rates[code] ? json.rates[code] : 1;
                    $g('#add-currency-modal input[data-key="rate"]').val(Number(value).toFixed(2));
                });
        }, 300);
    })

    $g('.apply-currency').on('click', function(event){
        event.preventDefault();
        let modal = $g('#add-currency-modal'),
            ind = this.dataset.ind * 1,
            obj = ind == -1 ? {default: false} : app.currencies.list[ind],
            alert = false,
            html = key = item = null;
        modal.find('[data-key]').each(function(){
            if (this.value.trim() == '' && this.dataset.key != 'thousand' && this.dataset.key != 'position') {
                alert = true;
                app.toggleAlertTooltip(true, this, this.closest('.ba-options-group-element'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (alert) {
            return false;
        }
        modal.find('.ba-options-group-element [data-key]').each(function(){
            obj[this.dataset.key] = this.value;
        });
        if (ind == -1) {
            app.currencies.list.push(obj);
            ind = app.currencies.list.length - 1
        }
        html = document.createElement('div');
        html.className = 'sorting-item currency-sorting-item';
        html.dataset.ind = ind;
        html.innerHTML = '<div class="sorting-icon"><i class="zmdi zmdi-more-vert sortable-handle"></i></div>'+
            '<div class="sorting-checkbox"><label class="ba-checkbox ba-hide-checkbox">'+
            '<input type="checkbox" '+(obj.default ? 'disabled' : '')+'><span></span></label></div>'+
            '<div class="sorting-title">'+obj.title+'</div><div class="sorting-currency-symbol">'+obj.symbol+'</div>'+
            '<div class="sorting-currency-code">'+obj.code+'</div><div class="sorting-currency-rate">'+Number(obj.rate).toFixed(2)+'</div>'+
            '<div class="sorting-currency-default" data-default="'+Number(obj.default)+'"><i class="zmdi zmdi-star"></i>'+
            '<span class="ba-tooltip ba-top ba-hide-element">'+app._('DEFAULT')+'</span></div>';
        item = document.querySelector('#store-currency-options .currency-sorting-item[data-ind="'+ind+'"]');
        if (item) {
            item.replaceWith(html)
        } else {
            document.querySelector('#store-currency-options .sorting-container').append(html);
        }
        app.setTooltip('.currency-sorting-item[data-ind="'+ind+'"]');
        app.exchangerates.check();
        modal.modal('hide');
    });

    $g('.apply-email-notification').on('click', function(event){
        event.preventDefault();
        let modal = $g('#add-email-notification-modal'),
            ind = this.dataset.ind * 1,
            obj = {
                delay: {},
                key: +new Date()
            },
            alert = false,
            status = key = html = item = null;
        if (ind != -1) {
            obj.key = app.notifications[ind].key;
        }
        modal.find('[data-key="title"], .notification-status-option input[type="text"]').each(function(){
            if (this.value.trim() == '') {
                alert = true;
                app.toggleAlertTooltip(true, this, this.closest('.ba-options-group-element'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (alert) {
            return false;
        }
        modal.find('.ba-options-group-element:not(.ba-hide-element) [data-key]').each(function(){
            key = this.dataset.key;
            if (key == 'body') {
                obj[key] = app.cke.body.getData();
            } else if (key == 'admins') {
                obj[key] = [];
                this.querySelectorAll('.entered-emails').forEach((el) => {
                    obj[key].push(el.dataset.email);
                });
            } else if (this.dataset.group) {
                obj[this.dataset.group] = obj[this.dataset.group] || {};
                obj[this.dataset.group][key] =  this.type == 'checkbox' ? this.checked : this.value;
            } else {
                obj[key] = this.value.trim();
            }
        });
        status = (obj.status && app.statuses[obj.status]) ? app.statuses[obj.status] : null;
        if (ind == -1) {
            app.notifications.push(obj);
            ind = app.notifications.length - 1
        } else {
            app.notifications[ind] = obj;
        }
        html = document.createElement('div');
        html.className = 'sorting-item notification-sorting-item';
        html.dataset.ind = ind;
        html.innerHTML = '<div class="sorting-icon"><i class="zmdi zmdi-more-vert sortable-handle"></i></div>'+
            '<div class="sorting-checkbox"><label class="ba-checkbox ba-hide-checkbox">'+
            '<input type="checkbox"><span></span></label></div><div class="sorting-title">'+
            obj.title+'</div><div class="notification-sorting-type">'+app._(obj.type.toUpperCase())+
            '</div><div class="notification-sorting-status"><span class="notification-status"'+
            ' style="'+(status ? '--status-color:'+status.color : '')+
            '" data-status="'+(status ? status.key : '')+'">'+(status ? status.title : '')+'</span></div>';
        item = document.querySelector('#store-email-options .notification-sorting-item[data-ind="'+ind+'"]');
        if (item) {
            item.replaceWith(html)
        } else {
            document.querySelector('#store-email-options .sorting-container').append(html);
        }
        modal.modal('hide');
    });

    $g('.notification-recipient-option select').on('change', function(){
        let modal = $g('#add-email-notification-modal');
        modal.find('.customer-email-options, .admin-email-options').addClass('ba-hide-element');
        modal.find('.'+this.value+'-email-options').removeClass('ba-hide-element');
    });

    $g('.ba-add-email-action').on('keyup', function(event){
        let wrapper = this.closest('.ba-options-group-element').querySelector('.entered-emails-wrapper');
        if (event.keyCode == 13 && /@/g.test(this.value) && this.value.match(/@/g).length == 1
            && !wrapper.querySelector('.entered-emails[data-email="'+this.value+'"]')) {
            let html = '<span class="selected-items-name">'+this.value+'</span><i class="zmdi zmdi-close remove-selected-items"></i>',
                span = document.createElement('span');
            span.className = 'entered-emails selected-items';
            span.dataset.email = this.value;
            span.innerHTML = html;
            wrapper.append(span);
            this.value = '';
        }
    });

    $g('.picker-search').on('input', function(){
        let search = this.value.toLowerCase(),
            li = this.closest('div.modal-list-type-wrapper').querySelectorAll('li[data-value]');
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            li.forEach(function($this){
                let title = $this.textContent.toLowerCase();
                $this.style.display = search == '' || title.indexOf(search) != -1 ? '' : 'none';
            });
        }, 300);
    });

    app.country.load();

    $g('.ba-modal-list-picker').on('click', '.prevent-event', function(event){
        event.preventDefault();
        event.stopPropagation();
    }).on('click', '.picker-item-action-icon[data-action]', function(){
        app[this.dataset.wrapper][this.dataset.action](this);
    }).on('click', '.add-new-country', function(){
        app.country.addCountry(this);
    }).on('click', '.add-new-state', function(){
        app.states.addState(this);
    }).on('click', '.states-back-wrapper', function(){
        app.states.back();
    }).on('click', 'li', function(){
        if (!this.classList.contains('prevent-event') && !this.classList.contains('disabled-country')) {
            fontBtn.dataset.value = this.dataset.value;
            $g(fontBtn).trigger('change');
            $g(this).closest('.ba-modal-list-picker').modal('hide');
        }
    });

    $g('body').on('click', '.copy-to-clipboard', function(event){
        var textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        textarea.value = this.previousElementSibling.value;
        textarea.select()
        document.execCommand('copy');
        textarea.remove();
        showNotice(app._('SUCCESSFULLY_COPIED_TO_CLIPBOARD'));
    }).on('input', 'input.integer-validation', function(){
        let decimals = this.dataset.decimals * 1,
            max = decimals > 0 ? 1 : 0,
            match = this.value.match(new RegExp('\\d+\\.{0,'+max+'}\\d{0,'+decimals+'}'));
        if (!match) {
            this.value = '';
        } else if (match[0] != this.value) {
            this.value = match[0];
        }
    });

    $g('body').on('click', '.dashboard-view-media-manager', function(event){
        event.preventDefault();
        checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader', function(){
            var iframe = document.querySelector('#uploader-modal iframe').contentWindow;
            iframe.document.body.classList.add('media-manager-enabled');
        });
    });

    $g('body').on('click', '.add-new-app', function(event){
        event.preventDefault();
        event.stopPropagation();
        $g('#ba-gridbox-apps-dialog .search-gridbox-apps').val('');
        $g('#ba-gridbox-apps-dialog .gridbox-app-element').css('display', '');
        var modal = $g('#ba-gridbox-apps-dialog');
        modal.find('.gridbox-app-element[data-system][data-installed="1"]')
            .attr('data-installed', 0).find('.default-theme').remove();
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=appslist.getSystemApps",
            success: function(msg){
                var array = JSON.parse(msg)
                for (var i = 0; i < array.length; i++) {
                    modal.find('.gridbox-app-element.gridbox-app-item-'+array[i].title)
                        .attr('data-installed', 1).find('.gridbox-app-item-body')
                        .append('<span class="default-theme"><i class="zmdi zmdi-check-circle"></i></span>');
                }
                modal.modal();
            }
        });
    });

    $g('body').on('click', '.ba-add-payment-method', function(event){
        event.preventDefault();
        event.stopPropagation();
        $g('#gridbox-payment-methods-dialog .search-gridbox-apps').val('');
        $g('#gridbox-payment-methods-dialog .gridbox-app-element').css('display', '');
        $g('#gridbox-payment-methods-dialog').modal();
    }).on('click', '.ba-create-store-product', function(event){
        event.preventDefault();
        event.stopPropagation();
        document.querySelector('.ba-select-store-product-type').classList.add('visible-store-product-type');
    }).on('click', '.create-new-booking-appointment [data-type="block-time"]', function(event){
        app.booking.block.show();
    }).on('click', '.create-new-booking-appointment [data-type="new-booking"]', function(event){
        app.booking.showNewBookingModal();
    });

    $g('#block-time-modal .open-calendar-dialog').on('update', function(){
        app.booking.block.check()
    });
    $g('#block-time-modal select').on('change', function(){
        app.booking.block.check()
    });
    $g('#apply-block-time').on('click', function(event){
        event.preventDefault();
        if (!this.classList.contains('active-button')) {
            return;
        }
        app.booking.block.send(this.dataset.id);
    });
    $g('#new-booking-modal').on('click', '.extra-options-details[data-type="file"] input[type="text"]', function(){
        this.closest('.extra-options-details').querySelector('input[type="file"]').click();
    })

    $g('#new-booking-modal').on('change', '.extra-options-details[data-type="file"] input[type="file"]', function(){
        let uploaded = this.closest('.extra-options-details').querySelectorAll('.extra-attachment-file').length;
        if (this.dataset.count !== '' && this.files.length + uploaded > this.dataset.count * 1) {
            app.showNotice(app._('MAXIMUM_NUMBER_FILES_EXCEEDED'), 'ba-alert');
            this.value = '';
            return;
        }
        let files = [].slice.call(this.files),
            size = msg = ext = null,
            id = this.dataset.id * 1,
            product_id = this.closest('.modal-body').querySelector('input[name="service"]').dataset.id,
            types = this.dataset.types.replace(/ /g, '').split(','),
            flag = this.dataset.uploading != 'pending';
        for (let i = 0; i < files.length; i++) {
            size = this.dataset.size * 1000;
            ext = app.getExt(files[i].name);
            if (flag && (size < files[i].size || types.indexOf(ext) == -1)) {
                msg = size < files[i].size ? 'NOT_ALLOWED_FILE_SIZE' : 'NOT_SUPPORTED_FILE';
                flag = false;
                app.showNotice(app._(msg), 'ba-alert');
                break
            }
        }
        if (flag) {
            this.dataset.uploading == 'pending';
            app.booking.uploadAttachmentFile(files, this, id, product_id);
        }
        this.value = '';
    }).on('click', '.remove-attachment-file', function(){
        let attachment = this.closest('.extra-attachment-file');
        attachment.remove();
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.removeAttachment', {
            id: attachment.dataset.id,
            is_admin: 1
        });
    });

    $g('#new-booking-modal input[name="service"]').on('click', function(){
        fontBtn = this;
        let modal = document.getElementById('product-applies-dialog');
        if (modal.dataset.loaded == 'loaded') {
            showAppliesModal(modal);
            return;
        }
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.getProducts').then((text) => {
            let products = JSON.parse(text);
            getProductsHtml(modal, products, 'product');
            modal.dataset.loaded = 'loaded';
            showAppliesModal(modal);
        });
    }).on('change', function(){
        let obj = JSON.parse(this.dataset.value),
            parent = this.closest('.new-booking-details'),
            price = document.createElement('span'),
            modal = $g('#new-booking-modal');
        price.className = 'service-price';
        price.textContent = app.renderPrice(obj.sale_price != '' ? obj.sale_price : obj.price);
        obj.booking = JSON.parse(obj.booking);
        app.booking.product = obj;
        this.value = obj.title;
        this.dataset.id = obj.id;
        modal.find('.service-price, .extra-options-details').remove();
        parent.querySelector('.service-price');
        parent.append(price)
        modal.find('[data-type="multiple"], [data-type="single"]').addClass('ba-hide-element');
        modal.find('[data-type="'+obj.booking.type+'"]').removeClass('ba-hide-element');
        modal.find('select[name="start_time"] option').not('option[value=""]').remove();
        if (obj.booking.type == 'single' && obj.booking.single.time == "yes") {
            modal.find('select[name="start_time"]').closest('div').removeClass('ba-hide-element');
        } else if (obj.booking.type == 'single') {
            modal.find('select[name="start_time"]').closest('div').addClass('ba-hide-element');
        }
        if (obj.booking.type == 'single' && obj.booking.single.type == "private") {
            modal.find('input[name="guests"]').closest('div').addClass('ba-hide-element');
        } else if (obj.booking.type == 'single') {
            modal.find('input[name="guests"]').attr('max', obj.booking.single.participants)
                .closest('div').removeClass('ba-hide-element');
        }
        modal.find('.open-calendar-dialog').each(function(){
            this.dataset.productId = obj.id;
            this.value = '';
        })
        for (let ind in obj.extra) {
            let div = document.createElement('div'),
                html = '',
                option = obj.extra[ind];
            div.className = 'new-booking-details extra-options-details';
            div.innerHTML = '<label><span>'+option.title+'</span>'+
                (option.required == 1 ? '<span class="booking-required-star">*</span>' : '')+'</label>';
            div.dataset.type = option.type;
            if (option.type == 'textinput' || option.type == 'textarea') {
                let item = option.items[0];
                html += '<input type="text" name="'+ind+'"'+(option.required == 1 ? ' required' : '')+'><span class="focus-underline"></span>'+
                    (item.price ? '<span class="extra-option-price">'+app.renderPrice(item.price)+'</span>' : '')
            } else if (option.type == 'file') {
                let item = option.items[0];
                html += '<input type="text" name="'+ind+'"'+(option.required == 1 ? ' required' : '')+
                    ' readonly placeholder="'+app._('SELECT')+'"><input type="file"'+(option.file.multiple ? ' multiple' : '')+
                    'data-size="'+option.file.size+'" data-types="'+option.file.types+'" data-count="'+
                    (option.file.multiple ? option.file.count : 1)+'" data-id="'+ind+'" style="display: none;">'+
                    (item.price ? '<span class="extra-option-price">'+app.renderPrice(item.price)+'</span>' : '')+
                    '<div class="extra-attached-files" data-charge="'+(option.file.charge ? 1 : 0)+
                    '" data-quantity="'+(option.file.quantity ? 1 : 0)+'"></div>'
            } else {
                for (let key in option.items) {
                    let item = option.items[key];
                    html += '<div class="ba-checkbox-wrapper"><span>'+item.title+
                        (item.price ? '<span class="extra-option-price">'+app.renderPrice(item.price)+'</span>' : '')+
                        '</span><label class="ba-'+(option.type == 'checkbox' ? 'checkbox' : 'radio')+
                        '"><input type="'+(option.type == 'checkbox' ? 'checkbox' : 'radio')+'" name="'+ind+'" value="'+key+
                        '"'+(option.required == 1 ? ' required' : '')+'><span></span></label></div>';
                }
            }
            
            $g(div).append(html)
            modal.find('.service-booking-details').after(div);
        }
    });

    $g('#new-booking-modal input[name="guests"]').on('input', function(){
        if (this.value < 0) {
            this.value = 0;
        } else if (this.value * 1 > this.max * 1) {
            this.value = this.max;
        }
    })

    $g('#new-booking-modal select[name="start_time"]').on('change', function(){
        if (!('guests') in app.booking.product.times[this.value] ||
            app.booking.product.booking.single.type != 'group-session') {
            return;
        }
        let max = app.booking.product.times[this.value].guests;
        $g('#new-booking-modal input[name="guests"]').each(function(){
            this.value = 1;
            this.max = max;
        });
    })

    $g('#new-booking-modal input[name="user"]').on('click', function(){
        showUsersDialog(0, this);
    }).on('change', function(){
        let user = JSON.parse(this.dataset.value);
        this.value = user.username;
        this.dataset.id = user.id;
    })

    $g('#new-booking-modal select[data-type="country"]').on('change', function(){
        let parent = $g(this).closest('div'),
            value = this.value;
        parent.find('select[data-type="region"]').remove();
        app.countries.forEach(function(country){
            if (country.id != value) {
                return;
            }
            let select = document.createElement('select');
            select.dataset.type = 'region';
            country.states.forEach(function(region){
                let option = document.createElement('option');
                option.value = region.id;
                option.textContent = region.title;
                select.append(option);
            });
            if (select.querySelector('option')) {
                parent.append(select);
            }
        });
    });

    $g('#new-booking-modal').find('input[required], select[required]').on('focus', function(){
        app.removeAlertTooltip(this);
    })

    $g('#apply-new-booking').on('click', function(event){
        event.preventDefault();
        let modal = document.querySelector('#new-booking-modal')
        modal.querySelectorAll('input[required], select[required]').forEach(($this) => {
            if ($this.closest('.ba-hide-element') || $this.value != '') {
                return;
            }
            let parent = $this.closest('.new-booking-details');
            app.toggleAlertTooltip(true, $this, parent, 'THIS_FIELD_REQUIRED');
        });
        if (modal.querySelector('.ba-alert')) {
            return;
        }
        let data = {
            old_product: app.booking.old_product,
            info: {},
            extra:{}
        }
        modal.querySelectorAll('input[name], select[name]').forEach(($this) => {
            if ($this.closest('.ba-hide-element')) {
                return;
            }
            let isExtra = $this.closest('.extra-options-details'),
                details = $this.closest('.new-booking-details');
            if (isExtra && ($this.type == 'checkbox' || $this.type == 'radio') && $this.checked) {
                data.extra[$this.name] = data.extra[$this.name] || [];
                data.extra[$this.name].push($this.value)
            } else if (isExtra && ($this.type == 'checkbox' || $this.type == 'radio') && !$this.checked) {
                return;
            } else if (isExtra && details.dataset.type == 'file') {
                data.extra[$this.name] = [];
                details.querySelectorAll('.extra-attachment-file').forEach((file) => {
                    data.extra[$this.name].push(file.dataset.id);
                });
            } else if (isExtra) {
                data.extra[$this.name] = $this.value.trim();
            } else if ($this.name == 'service' || $this.name == 'user') {
                data[$this.name] = $this.dataset.id;
            } else if ($this.name == 'start_date' || $this.name == 'end_date') {
                data[$this.name] = $this.dataset.value;
            } else if ($this.name == 'start_time') {
                data.start_time = app.booking.product.times[$this.value].start;
                data.end_time = app.booking.product.times[$this.value].end;
            } else if ($this.name == 'guests') {
                data.guests = $this.value;
            } else if ($this.type == 'hidden' && details.dataset.type == 'country') {
                let object = {}
                details.querySelectorAll('select').forEach((select) => {
                    object[select.dataset.type] = select.value;
                })
                data.info[$this.name] = JSON.stringify(object)
            } else if (($this.type == 'checkbox' || $this.type == 'radio') && $this.checked) {
                data.info[$this.name] = data.info[$this.name] || [];
                data.info[$this.name].push($this.value)
            } else {
                data.info[$this.name] = $this.value
            }
        });
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.createAppointment', data).then((text) => {
            console.info(text)
            reloadPage();
        });
        $g('#new-booking-modal').modal('hide');
    });

    $g('.view-monthly-product-appointment').on('click', function(){
        app.booking.viewMonthlyItems(currentContext[0]);
    });
    $g('.monthly-products-wrapper').on('click', '.monthly-product-item', function(){
        app.booking.getDetails(this.dataset.id);
    })
    $g('.view-booking-appointment').on('click', function(){
        app.booking.getDetails(currentContext[0].dataset.id);
    });

    $g('.edit-booking-appointment').on('click', function(){
        app.booking.editBooking(currentContext[0].dataset.id);
    });

    $g('.delete-booking-appointment').on('click', function(){
        deleteMode = {
            action: 'delete-booking',
            id: currentContext[0].dataset.id
        }
        $g('#delete-dialog').modal();
    });
    $g('.delete-block-time').on('click', function(){
        deleteMode = {
            action: 'delete-block-time',
            id: currentContext[0].dataset.id
        }
        $g('#delete-dialog').modal();
    });
    $g('.edit-booking-details').on('click', function(){
        let id = document.querySelector('#booking-details-modal').dataset.id;
        app.booking.editBooking(id);
    });
    $g('.mark-booking-as-paid, .mark-booking-as-unpaid').on('click', function(){
        let id = document.querySelector('#booking-details-modal').dataset.id;
        app.booking.setPaid(id, this.dataset.action);
    });
    $g('.delete-booking').on('click', function(){
        let id = document.querySelector('#booking-details-modal').dataset.id;
        deleteMode = {
            action: 'delete-booking-viewed',
            id: id
        }
        $g('#delete-dialog').modal();
    })
    $g('.edit-block-time').on('click', function(){
        app.booking.block.edit(currentContext[0].dataset.id);
    });
    $g('.set-new-booking').on('click', function(){
        app.booking.showNewBookingModal();
    });
    $g('.set-block-time').on('click', function(){
        let data = {
                start_date: currentContext[0].dataset.date,
                start_formated: currentContext[0].dataset.formated,
                end_formated: currentContext[0].dataset.formated,
                end_date: currentContext[0].dataset.date,
                start_time: currentContext[0].closest('.ba-booking-calendar-row').dataset.time,
                id: 0
            },
            end_time = document.querySelector('select[name="end_time"] option[value="'+data.start_time+'"]').nextElementSibling;
        data.end_time = end_time ? end_time.value : '';
        app.booking.block.show(data);
    });

    $g('body').on('click', '.ba-add-sales', function(event){
        event.preventDefault();
        event.stopPropagation();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task=sales.addSales",
            complete:function(msg){
                reloadPage(app._('ITEM_CREATED'));
            }
        });
    }).on('click', '.ba-add-promocodes-method', function(event){
        event.preventDefault();
        event.stopPropagation();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task=promocodes.addPromoCode",
            complete:function(msg){
                reloadPage(app._('ITEM_CREATED'));
            }
        });
    }).on('click', '.ba-add-shipping', function(event){
        event.preventDefault();
        event.stopPropagation();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task=shipping.addShipping",
            complete:function(msg){
                reloadPage(app._('ITEM_CREATED'));
            }
        });
    }).on('click', '.ba-add-product-options', function(event){
        event.preventDefault();
        event.stopPropagation();
        makeFetchRequest('index.php?option=com_gridbox&task=productoptions.addProductOptions').then(function(json){
            reloadPage(app._('ITEM_CREATED'));
        });
    });

    $g('body').on('click', '.gridbox-app-item.add-new-theme', function(event){
        $g('#ba-gridbox-themes-dialog .search-gridbox-apps').val('');
        $g('#ba-gridbox-themes-dialog .gridbox-app-element').css('display', '');
        $g('#ba-gridbox-themes-dialog').modal();
    });

    $g('body').on('click', '.delete-gridbox-app-item, .remove-group-app', function(event){
        event.preventDefault();
        deleteMode = {
            action: 'pages.deleteGridboxAppItem',
            item : this.closest('.gridbox-app-item, .group-apps-list-item'),
            id: this.dataset.id
        };
        $g('#delete-dialog').modal();
    });

    $g('body').on('keyup', '.gridbox-app-item-header span', function(event){
        let $this = this,
            data = {}
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            let item = $this.closest('.gridbox-app-item');
            data.id = item.dataset.id;
            data.type = item.dataset.type;
            data.title = $this.textContent.trim();
            if (data.title) {
                app.fetch('index.php?option=com_gridbox&task=appslist.renameApp', data).then(() => {
                    app.appsList.refreshSidebar();
                });
            }
        }, 300);
    });

    $g('body').on('click', '.gridbox-app-item-footer-action.theme-duplicate', function(event){
        event.preventDefault();
        var id = this.closest('.gridbox-app-item').dataset.id;
        $g('#context-item').val(id);
        Joomla.submitbutton('themes.contextDuplicate');
    });

    $g('body').on('click', '.gridbox-app-item-footer-action.theme-delete', function(event){
        event.preventDefault();
        var item = this.closest('.gridbox-app-item')
            id = item.dataset.id,
            def = item.querySelector('p').dataset.default;
        if (def == 1) {
            $g('#default-message-dialog').modal();
            return false;
        }
        $g('#context-item').val(id);
        deleteMode = 'single';
        $g('#delete-dialog').modal();
    });

    $g('body').on('click', '.gridbox-app-item-footer-action.theme-settings', function(event){
        event.preventDefault();
        item = this.closest('.gridbox-app-item')
        var obj = {
                id : item.dataset.id,
                name : item.querySelector('p > span').textContent,
                default : item.querySelector('p').dataset.default,
                image : item.querySelector('.image-container').dataset.image
            };
        pageId = obj.id;
        setThemeSettings(obj);
    });

    $g('body').on('change', '.set-group-display', function(){
        let action = this.checked ? 'addClass' : 'removeClass',
            $this = $g(this).closest('.ba-group-element, .ba-options-group-element').nextAll();
        $this[action]('visible-subgroup').removeClass('subgroup-animation-ended');
        clearTimeout(this.subDelay);
        if (this.checked) {
            this.subDelay = setTimeout(function(){
                $this.addClass('subgroup-animation-ended');
            }, 750);
        }
    });

    $g('body').on('click.lightbox', '.comment-attachment-image-type, .attachment-image', function(){
        var wrapper = $g(this).closest('.comment-attachments-image-wrapper, .ba-product-attachments'),
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
        img.style.backgroundImage = 'url('+this.dataset.img.replace(/\s/g, '%20')+')';
        div.className = 'ba-image-modal instagram-modal ba-comments-image-modal';
        img.style.top = (offset.top - $g(window).scrollTop())+'px';
        img.style.left = offset.left+'px';
        img.style.width = width+'px';
        img.style.height = height+'px';
        div.appendChild(img);
        modal.on('click', function(){
            commentsImageModalClose(modal, images, index)
        });
        $g('body').append(div);
        image.onload = function(){
            setCommentsImage(this);
        }
        image.src = this.dataset.img;
        setTimeout(function(){
            var str = '';
            if (wrapper.find('.comment-attachment-image-type, .attachment-image').length > 1) {
                str += '<i class="zmdi zmdi-chevron-left"></i><i class="zmdi zmdi-chevron-right"></i>';
            }
            str += '<a class="zmdi zmdi-download" href="'+image.src+'" download></a>';
            str += '<i class="zmdi zmdi-close"></i>';
            modal.append(str);
            modal.find('.zmdi-chevron-left').on('click', function(event){
                event.stopPropagation();
                index = commentsImageGetPrev(img, images, index);
            });
            modal.find('.zmdi-chevron-right').on('click', function(event){
                event.stopPropagation();
                index = commentsImageGetNext(img, images, index);
            });
            modal.find('.zmdi-close').on('click', function(event){
                event.stopPropagation();
                commentsImageModalClose(modal, images, index)
            });
        }, 600);
        wrapper.find('.comment-attachment-image-type, .attachment-image').each(function(ind){
            images.push(this);
            if (this == $this) {
                index = ind;
            }
        });
        $g(window).on('keyup.instagram', function(event){
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

    $g('body').on('click', '.ban-user-comment', function(event){
        let str = app.currentComment.find('input[type="hidden"]').val(),
            obj = JSON.parse(str),
            view = $g('input[name="ba_view"]').val();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+".banUser",
            data: {
                'email': obj.email,
                'ip': obj.ip
            },
            complete:function(msg){
                showNotice(msg.responseText);
            }
        });
    });
    
    $g('body').on('click', '.approve-user-comment, .spam-user-comment', function(event){
        let str = app.currentComment.find('input[type="hidden"]').val(),
            status = this.dataset.status,
            task = this.dataset.task,
            obj = JSON.parse(str),
            view = $g('input[name="ba_view"]').val();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+"."+task,
            data: {
                'context-item': obj.id
            },
            complete:function(msg){
                let iconClassName = 'zmdi zmdi-eye ba-icon-md',
                    constName = 'COM_GRIDBOX_N_ITEMS_APPROVED';
                    iconTooltip = app._('APPROVED');
                if (status == 'spam') {
                    iconClassName = 'zmdi zmdi-alert-octagon ba-icon-md';
                    iconTooltip = app._('SPAM');
                    constName = 'COM_GRIDBOX_N_ITEMS_SPAMED';
                }
                obj.status = status;
                str = JSON.stringify(obj);
                app.currentComment.find('input[type="hidden"]').val(str);
                app.currentComment.find('.status-td i')[0].className = iconClassName;
                app.currentComment.find('.status-td .ba-tooltip').text(iconTooltip);
                showNotice(app._(constName));
            }
        });
    });

    $g('body').on('click', '.delete-user-comment', function(event){
        var id = app.currentComment.find('input[type="checkbox"]').val(),
            view = $g('input[name="ba_view"]').val();
        $g('#context-item').val(id);
        deleteMode = view+'.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('body').on('click', '.edit-user-comment', function(event){
        let parent = $g(this).closest('.comment-user-message-wrapper'),
            message = parent.find('> .comment-message').html().trim().replace(/<br>/g, '\n');
        parent.find('> .comment-message, .edit-user-comment').hide();
        parent.find('> .ba-comment-message-wrapper').css('display', '').find('.ba-comment-message').val(message);
    });

    $g('body').on('click', '.ba-dashboard-popover-trigger', function(event){
        event.stopPropagation();
        event.preventDefault();
        document.querySelectorAll('.visible-dashboard-dialog').forEach(function(element){
            element.classList.remove('visible-dashboard-dialog');
        });
        let div = document.querySelector('.'+this.dataset.target),
            rect = this.getBoundingClientRect();
        div.classList.add('visible-dashboard-dialog');
        let left = (rect.left - div.offsetWidth / 2 + rect.width / 2),
            top = rect.bottom + window.pageYOffset + 10,
            arrow = '50%';
        if (div.dataset.position == 'top') {
            top = rect.top - div.offsetHeight - 10;
        }
        if (this.dataset.target == 'blog-settings-context-menu' && left < 110) {
            left = 110;
            arrow = (rect.left - 110 + rect.width / 2)+'px'
        }
        if (left + div.offsetWidth > window.innerWidth) {
            left = rect.right - div.offsetWidth;
            arrow = (rect.left - left + rect.width / 2)+'px'
        }
        div.style.setProperty('--arrow-position', arrow);
        div.style.top = top+'px';
        div.style.left = left+'px';
        if (div.querySelector('.tabs-underline')) {
            setTimeout(() => {
                setTabsUnderline();
            }, 300);
        }
    });

    $g('body').on('click', '.ba-comment-smiles-picker', function(event){
        event.stopPropagation();
        let picker = $g('.ba-comment-smiles-picker-dialog').addClass('visible-smiles-picker'),
            rect = this.getBoundingClientRect(),
            div = picker[0];
        fontBtn = $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-message')[0];
        div.style.top = (rect.top + window.pageYOffset - div.offsetHeight / 2 + rect.height / 2)+'px';
        div.style.left = (rect.left - div.offsetWidth - 10)+'px';
    });

    $g('body').on('click', '.ba-submit-cancel', function(){
        let parent = $g(this).closest('.comment-user-message-wrapper');
        parent.find('.comment-message, .edit-user-comment').css('display', '');
        parent.find('> .ba-comment-message-wrapper').hide()
            .find('.ba-comment-message').next().find('.ba-comment-xhr-attachment .zmdi-delete').trigger('click');
    });

    $g('body').on('input', '.ba-comment-message', function(){
        checkCommentDisabledBtn(this);
    });

    $g('body').on('click', '.ba-submit-comment', function(event){
        let str = app.currentComment.find('input[type="hidden"]').val(),
            obj = JSON.parse(str),
            attachments = {},
            message = $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-message').val(),
            data = {
                message: message,
                type: this.dataset.type,
                parent: obj.id
            },
            view = $g('input[name="ba_view"]').val();
        $g(this).closest('.ba-comment-message-wrapper').find('.ba-comment-xhr-attachment').each(function(){
            attachments[this.dataset.id] = app.tmpAttachments[this.dataset.id];
        });
        data.attachments = JSON.stringify(attachments);
        var matches = data.message.match(/(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe23\u20d0-\u20f0]|\ud83c[\udffb-\udfff])?(?:\u200d(?:[^\ud800-\udfff]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe23\u20d0-\u20f0]|\ud83c[\udffb-\udfff])?)*/g);
        if (matches) {
            for (var i = 0; i < matches.length; i++) {
                let charCode = '&#'+matches[i].codePointAt(0)+';';
                data.message = data.message.replace(matches[i], charCode);
            }
        }
        if (data.message || data.attachments != '{}') {
            $g.ajax({
                type : "POST",
                dataType : 'text',
                url : "index.php?option=com_gridbox&task="+view+".sendCommentMesssage",
                data: data,
                complete:function(msg){
                    if (data.type == 'reply') {
                        reloadPage();
                    } else {
                        obj.message = data.message;
                        obj.attachments = JSON.parse(msg.responseText);
                        str = JSON.stringify(obj);
                        app.currentComment.find('input[type="hidden"]').val(str);
                        app.currentComment.find('span.comments-message').text(message);
                        app.currentComment.trigger('click');
                    }
                }
            });
        }
    });

    $g('body').on('click', '.delete-comment-attachment-file', function(){
        let $this = this,
            data = {
                id: this.dataset.id,
                filename: this.dataset.filename
            },
            view = $g('input[name="ba_view"]').val();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+".removeTmpAttachment",
            data: data,
            complete:function(msg){
                var str = app.currentComment.find('input[type="hidden"]').val(),
                    obj = JSON.parse(str),
                    list = [];
                for (var i = 0; i < obj.attachments.length; i++) {
                    if (obj.attachments[i].id != data.id) {
                        list.push(obj.attachments[i]);
                    }
                }
                obj.attachments = list;
                str = JSON.stringify(obj);
                app.currentComment.find('input[type="hidden"]').val(str);
                if ($this.dataset.type == 'file') {
                    $this.closest('.comment-attachment-file').remove();
                } else {
                    $this.closest('.comment-attachment-image-type-wrapper').remove();
                }
            }
        });
    });

    $g('.ba-comment-smiles-picker-dialog').on('click', 'span', function(event){
        event.stopPropagation();
        insertTextAtCursor(fontBtn, this.textContent);
    });

    $g('body').on('click', function(event){
        $g('.ba-comment-smiles-picker-dialog.visible-smiles-picker').removeClass('visible-smiles-picker');
        if (!event.target || !event.target.closest('.ba-dashboard-apps-dialog, .modal.in, .modal-backdrop, .ba-context-menu')) {
            $g('.ba-dashboard-apps-dialog.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        }
        $g('.ba-select-store-product-type.visible-store-product-type').each(function(i, el){
            el.classList.remove('visible-store-product-type');
            el.classList.add('store-product-type-out');
            setTimeout(function(){
                el.classList.remove('store-product-type-out');
            }, 300);
        });
    });

    $g('body').on('click', '.ba-comment-attachment-trigger', function(){
        let $this = $g(this).next();
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
            var size = this.dataset.size * 1000,
                name = files[i].name.split('.'),
                msg = '',
                ext = name[name.length - 1].toLowerCase(),
                types = this.dataset.types.replace(/ /g, '').split(',');
            if (size < files[i].size) {
                msg = 'NOT_ALLOWED_FILE_SIZE';
            } else if (types.indexOf(ext) == -1) {
                msg = 'NOT_SUPPORTED_FILE';
            }
            if (size < files[i].size || types.indexOf(ext) == -1) {
                flag = false;
                showNotice(app._(msg), 'ba-alert');
                this.dataset.uploading = '';
                break
            }
        }
        if (flag) {
            uploadCommentAttachmentFile(files, this.dataset.attach, container);
        }
    });

    app.tmpAttachments = {};

    function removeTmpAttachment($this)
    {
        if ($this.dataset.id) {
            let view = $g('input[name="ba_view"]').val();
            $g.ajax({
                type : "POST",
                dataType : 'text',
                url : "index.php?option=com_gridbox&task="+view+".removeTmpAttachment",
                data: {
                    id: $this.dataset.id,
                    filename: app.tmpAttachments[$this.dataset.id].filename
                },
                complete:function(msg){
                    let container = $this.closest('.ba-comment-xhr-attachment-wrapper');
                    $this.remove();
                    delete(app.tmpAttachments[$this.dataset.id]);
                    checkCommentDisabledBtn(container);
                }
            });
        }
    }

    function checkCommentDisabledBtn($this)
    {
        let btn = $g($this).closest('.ba-comment-message-wrapper').find('.ba-submit-comment'),
            message = $g($this).closest('.ba-comment-message-wrapper').find('.ba-comment-message').val(),
            attachments = {},
            str = '';
        $g($this).closest('.ba-comment-message-wrapper').find('.ba-comment-xhr-attachment').each(function(){
            attachments[this.dataset.id] = app.tmpAttachments[this.dataset.id];
        });
        str = JSON.stringify(attachments);
        if (message.trim() || str != '{}') {
            btn.removeClass('ba-disabled-submit');
        } else {
            btn.addClass('ba-disabled-submit');
        }
    }

    function uploadCommentAttachmentFile(files, type, container)
    {
        if (files.length) {
            var file = files.shift(),
                attachment = document.createElement('div'),
                str = '',
                xhr = new XMLHttpRequest(),
                formData = new FormData(),
                view = $g('input[name="ba_view"]').val();
            attachment.className = 'ba-comment-xhr-attachment';
            if (type == 'file') {
                str += '<i class="zmdi zmdi-attachment-alt"></i>';
            } else {
                str += '<span class="post-intro-image"></span>';
            }
            str += '<span class="attachment-title">'+file.name;
            str += '</span><span class="attachment-progress-bar-wrapper"><span class="attachment-progress-bar">';
            str += '</span></span><i class="zmdi zmdi-delete"></i>';
            attachment.innerHTML = str;
            if (type == 'image') {
                let reader = new FileReader();
                reader.onloadend = function() {
                    attachment.querySelector('.post-intro-image')
                        .style.backgroundImage = 'url('+reader.result.replace(/\s/g, '%20')+')';
                }
                reader.readAsDataURL(file);
            }
            $g(attachment).find('.zmdi-delete').on('click', function(){
                removeTmpAttachment(this.closest('.ba-comment-xhr-attachment'));
            });
            formData.append('file', file);
            formData.append('type', type);
            xhr.upload.onprogress = function(event) {
                attachment.querySelector('.attachment-progress-bar').style.width = Math.round(event.loaded / event.total * 100)+"%";
            }
            xhr.onload = xhr.onerror = function(){
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
                uploadCommentAttachmentFile(files, type, container);
            };
            container.append(attachment);
            xhr.open("POST", "index.php?option=com_gridbox&task="+view+".uploadAttachmentFile", true);
            xhr.send(formData);
        } else {
            checkCommentDisabledBtn(container);
            $g('body .ba-comment-attachment[data-uploading="uploading"]').removeAttr('data-uploading');
        }
    }

    $g('body').on('click', '.comments-table tbody tr', function(){
        $g('.active-comment').removeClass('active-comment');
        app.currentComment = $g(this).addClass('active-comment');
        app.tmpAttachments = {};
        getCommentLikeStatus();
    });

    $g('body').on('click', '.comment-likes-action', function(){
        if (this.dataset.disabled) {
            return false;
        }
        $g('.comments-right-sidebar .comment-likes-action').attr('data-disabled', 'disabled');
        let str = app.currentComment.find('td.select-td  input[type="hidden"]').val(),
            obj = JSON.parse(str),
            action = this.dataset.action,
            view = $g('input[name="ba_view"]').val();
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+".setLikes",
            data: {
                id: obj.id,
                action: this.dataset.action
            },
            complete:function(msg){
                let obj = JSON.parse(msg.responseText);
                setTimeout(function(){
                    $g('.comments-right-sidebar .comment-likes-action').removeAttr('data-disabled');
                }, 100);
                $g('.comments-right-sidebar .comment-likes-action[data-action="likes"] .likes-count').text(obj.likes);
                $g('.comments-right-sidebar .comment-likes-action[data-action="dislikes"] .likes-count').text(obj.dislikes);
                $g('.comments-right-sidebar .comment-likes-action').removeClass('active');
                $g('.comments-right-sidebar .comment-likes-action[data-action="'+obj.status+'"]').addClass('active');
            }
        });
    });

    $g('body').on('click', '.ba-comment-unread', function(){
        this.classList.remove('ba-comment-unread');
        let id = this.querySelector('input[type="checkbox"]').value,
            view = $g('input[name="ba_view"]').val();
        $g('.unread-comments-count[data-type="'+view+'"]').each(function(){
            let count = this.textContent - 1;
            if (count) {
                this.textContent = count;
            } else {
                this.remove();
            }
        });
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : "index.php?option=com_gridbox&task="+view+".setReadStatus",
            data: {
                id: id
            }
        });
    });

    $g('body').on('click', '.delete-author-social-link', function(event){
        event.stopPropagation();
        deleteMode = this;
        $g('#delete-dialog').modal();
    });

    $g('body').on('click', 'span.authors-link', function(){
        let key = this.dataset.key;
        $g('.apply-author-link').attr('data-key', key);
        openAuthorSocialDialog(app.authorsSocial[key]);
    });

    $g('.add-new-author-social-link i').on('click', function(){
        var key = -1;
        for (key in app.authorsSocial) {}
        $g('.apply-author-link').attr('data-key', key * 1 + 1);
        openAuthorSocialDialog();
    });

    $g('#edit-author-social-modal .author-link-url').on('input', function(){
        if (this.value.trim()) {
            $g('.apply-author-link').addClass('active-button');
        } else {
            $g('.apply-author-link').removeClass('active-button');
        }
    });

    $g('.apply-author-link').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button')) {
            let title = $g('#edit-author-social-modal .ba-custom-select input[type="hidden"]').val(),
                link = $g('#edit-author-social-modal .author-link-url').val().trim(),
                key = this.dataset.key;
            app.authorsSocial[key] = $g.extend(true, {}, authorSocial[title]);
            app.authorsSocial[key].link = link;
            let str = '';
            for (var ind in app.authorsSocial) {
                str += getAuthorPatern(ind);
            }
            $g('.authors-links-list').html(str);
            $g('#edit-author-social-modal').modal('hide');
        }
    });

    $g('.apply-system-settings').on('click', function(event){
        event.preventDefault();
        let options = {},
            query = '.page-language',
            modal = app.associations.modal;
        if (this.dataset.type == '404') {
            options.enable_header = modal.find('.page-enable-header').prop('checked');
        } else if (this.dataset.type == 'submission-form') {
            options.premoderation = modal.find('.submission-form-moderation').prop('checked');
            options.author = modal.find('.submission-form-author').prop('checked');
            options.access = modal.find('.submission-form-access input[type="hidden"]').val();
            options.emails = modal.find('.submission-form-notifications').prop('checked');
            options.submited_email = modal.find('.submission-form-submited').prop('checked');
            options.published_email = modal.find('.submission-form-publishing').prop('checked');
            query = '#publishing-basic-options '+query;
        }
        options.suffix = modal.find('.page-class-suffix').val().trim();
        app.associations.save();
        app.fetch('index.php?option=com_gridbox&task=system.applySettings', {
            title: modal.find('.page-title').val().trim(),
            alias: modal.find('.page-alias').val().trim(),
            theme: modal.find('.page-theme').val(),
            language: modal.find(query).val(),
            options: JSON.stringify(options),
            type: this.dataset.type,
            id: this.dataset.id
        }).then(function(text){
            reloadPage(text);
            modal.modal('hide');
        });
    });

    $g('.ba-range-wrapper input[type="range"]').each(function(){
        rangeAction(this, inputCallback);
    });

    $g('.ba-settings-toolbar input[type="number"]').on('input', function(){
        inputCallback($g(this));
    });

    notification.find('.zmdi.zmdi-close').on('click', function(){
        notification.removeClass('notification-in').addClass('animation-out');
    });

    function showPageSettings(obj, tr)
    {
        if (!tr.querySelector('.title-cell a')) {
            showNotice(app._('EDIT_NOT_PERMITTED'));
            return false;
        }
        var end = obj.end_publishing,
            modal = $g('#settings-dialog');
        if (end == '0000-00-00 00:00:00') {
            end = '';
        }
        $g('#settings-dialog .publish').prop('checked', obj.published == 1);
        $g('#published_on').val(obj.created);
        $g('.select-post-author').each(function(){
            $g('span.selected-author').remove();
            var author = [],
                li = $g(this).find('li[data-value]'),
                authorId = '';
            for (var i = 0; i < obj.author.length; i++) {
                if (!obj.author[i].avatar) {
                    obj.author[i].avatar = 'components/com_gridbox/assets/images/default-user.png';
                }
                var str = '<span class="selected-author" data-id="'+obj.author[i].id
                str += '"><span class="ba-author-avatar" style="background-image: url(';
                str += JUri+obj.author[i].avatar.replace(/\s/g, '%20')+')"></span><span class="ba-author-name">'+obj.author[i].title+'</span>';
                str += '<i class="zmdi zmdi-close remove-selected-author"></i></span>';
                $g(this).before(str);
                author.push(obj.author[i].id);
            }
            li.each(function(){
                this.style.display = author.indexOf(this.dataset.value) == -1 ? '' : this.style.display = 'none';
            });
            if (li.length == author.length) {
                $g('.select-post-author').hide();
            } else {
                $g('.select-post-author').css('display', '');
            }
            authorId = author.join(',');
            this.querySelector('input[type="hidden"]').value = authorId;
        });
        $g('#published_down').val(end);
        $g('#access').val(obj.page_access);
        var value = $g('.access-select li[data-value="'+obj.page_access+'"]').text().trim();
        $g('.access-select input[type="text"]').val(value);
        app.associations.prepare(modal, obj.language, obj.id, 'page');
        $g('#robots').val(obj.robots);
        value = $g('.robots-select li[data-value="'+obj.robots+'"]').text().trim();
        $g('.robots-select input[type="text"]').val(value);
        $g('.theme-list').val(obj.theme);
        var theme = $g('.theme-select li[data-value="'+obj.theme+'"]').text().trim(),
            modalFlag = true;
        $g('.theme-select input[type="text"]').val(theme);
        $g('#settings-dialog .ba-alert-container').hide();
        $g('#settings-dialog .permissions-options').each(function(){
            getPermissions(obj.id, 'page', this);
        });
        if ($g('.meta-tags').length > 0) {
            let categoriesTitle = app.multicategory.set(obj.page_category, obj.categories);
            $g('#page-category').val(obj.page_category).prev().val(categoriesTitle);
            $g.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_gridbox&task=gridbox.getPageTags",
                data : {
                    page_id : obj.id
                },
                success: function(msg){
                    msg = JSON.parse(msg);
                    $g('select.meta_tags').empty()
                    if (msg) {
                        $g('.picked-tags .tags-chosen').remove();
                        $g('select[name="meta_tags"]').empty();
                        for (var i = 0; i < msg.length; i++) {
                            var title = msg[i].title,
                                tagId = msg[i].id,
                                str = '<li class="tags-chosen"><span>';
                            str += title+'</span><i class="zmdi zmdi-close" data-remove="'+tagId+'"></i></li>';
                            $g('.picked-tags .search-tag').before(str);
                            str = '<option value="'+tagId+'" selected>'+title+'</option>';
                            $g('select.meta_tags').append(str);
                        }
                        $g('.meta-tags .picked-tags .search-tag input').val('');
                    }
                }
            });
        }
        $g('#settings-dialog .page-id').val(obj.id);
        $g('#settings-dialog .page-title').val(obj.title);
        $g('#settings-dialog .page-class-suffix').val(obj.class_suffix);
        $g('#settings-dialog .page-meta-title').val(obj.meta_title);
        $g('#settings-dialog .page-meta-description').val(obj.meta_description);
        $g('#settings-dialog .page-meta-keywords').val(obj.meta_keywords);
        $g('#settings-dialog .page-alias').val(obj.page_alias);
        $g('#settings-dialog .intro-text').val(obj.intro_text);
        let image = obj.img ? obj.img : obj.intro_image;
        $g('#settings-dialog .intro-image').each(function(){
            image = image != '' && !app.isExternal(image) ? JUri+image : image;
            let tooltip = this.closest('.ba-group-element').querySelector('.image-field-tooltip');
            this.dataset.value = obj.intro_image;
            this.value = obj.img ? obj.img : obj.intro_image;
            tooltip.style.backgroundImage = image != '' ? 'url('+image.replace(/\s/g, '%20')+')' : '';
        });
        if (obj.share_image == 'share_image') {
            obj.share_image = obj.intro_image;
        }
        image = !app.isExternal(obj.share_image) ? JUri+obj.share_image : obj.share_image;
        $g('#settings-dialog .share-image').val(obj.share_image).parent().find('.image-field-tooltip').css({
            'background-image': obj.share_image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        $g('#settings-dialog .share-title').val(obj.share_title);
        $g('#settings-dialog .share-description').val(obj.share_description);
        $g('#settings-dialog textarea[name="schema_markup"]').val(obj.schema_markup);
        $g('#settings-dialog .sitemap-override').prop('checked', Boolean(obj.sitemap_override * 1));
        $g('#settings-dialog .sitemap-include').prop('checked', Boolean(obj.sitemap_include * 1));
        var range = $g('#settings-dialog .priority').val(obj.priority).prev().val(obj.priority);
        setLinearWidth(range);
        $g('#settings-dialog .changefreq').val(obj.changefreq).prev().each(function(){
            this.value = $g(this).closest('.ba-custom-select').find('li[data-value="'+obj.changefreq+'"]').text().trim();
        });
        $g('#settings-dialog .set-group-display').each(function(){
            var action = this.checked ? 'addClass' : 'removeClass';
            $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
        });
        $g('.settings-apply').removeClass('disabled-button');
        $g('#settings-dialog').modal();
    }

    function drawBlogMoveTo(array)
    {
        var str = '',
            type = 'blog';
        if (moveTo != 'apps.moveTo' && !currentContext.hasClass('ba-category')) {
            var obj = currentContext.find('.select-td input[type="hidden"]').val();
            obj = JSON.parse(obj);
            type = obj.app_type;
        } else if (moveTo == 'apps.moveTo') {
            var obj = jQuery('td.select-td input[type="hidden"]').first().val();
            obj = JSON.parse(obj);
            type = obj.app_type;
        } else if (moveTo == 'apps.categoryMoveTo') {
            var obj = $g('#blog-data').val();
            obj = JSON.parse(obj);
            type = obj.type;
        }
        array.forEach(function(el, i){
            if (el.type == type) {
                var value = '{"id":0, "app_id":'+el.id+'}';
                str += '<li class="root '+el.type+'"><label><i class="zmdi zmdi-folder"></i>';
                str += el.title+'<input type="radio" style="display:none;"';
                str += " name='category_id' value='"+value+"'></label>";
                if (el.categories.length > 0) {
                    var catStr = drawRestoreBlog(el.categories, el.id);
                    if (catStr != '<ul></ul>') {
                        str += catStr;
                        str += '<i class="zmdi zmdi-chevron-right ba-icon-md"></i>';
                    }
                }
                str += '</li>';
            }
        });

        return str;
    }

    function drawRestoreBlog(array, app_id)
    {
        var str = '<ul>',
            id = 0;
        if (moveTo != 'apps.moveTo' && currentContext.hasClass('ba-category')) {
            id = currentContext.attr('data-id');
        }
        array.forEach(function(el, i){
            if (id != el.id) {
                var value = '{"id":'+el.id+', "app_id":'+app_id+'}';
                str += '<li><label><i class="zmdi zmdi-folder"></i>';
                str += el.title+'<input type="radio" style="display:none;"';
                str += " name='category_id' value='"+value+"'></label>";
                if (el.child.length > 0) {
                    var catStr = drawRestoreBlog(el.child, app_id);
                    if (catStr != '<ul></ul>') {
                        str += catStr;
                        str += '<i class="zmdi zmdi-chevron-right ba-icon-md"></i>';
                    }
                }
                str += '</li>';
            }
        });
        str += '</ul>';

        return str;
    }

    function showComemntsModeratorDialog()
    {
        $g('#ba-comments-users-dialog .user-sorting-select').each(function(){
            var value = $g(this).find('li[data-value="id"]').text().trim();
            $g(this).find('input[type="text"]').val(value);
            $g(this).find('input[type="hidden"]').val('id');
        });
        $g('#ba-comments-users-dialog .user-direction-select').each(function(){
            var value = $g(this).find('li[data-value="asc"]').text().trim();
            $g(this).find('input[type="text"]').val(value);
            $g(this).find('input[type="hidden"]').val('asc');
        });
        $g('#ba-comments-users-dialog .user-group-select').each(function(){
            var value = $g(this).find('li[data-value=""]').text().trim();
            $g(this).find('input[type="text"]').val(value);
            $g(this).find('input[type="hidden"]').val('');
        });
        $g('#ba-comments-users-dialog .user-sorting-select').trigger('customAction');
        $g('#ba-comments-users-dialog .search-ba-author-users').val('');
        $g('#ba-comments-users-dialog .ba-options-group').css('display', '');
        $g('#ba-comments-users-dialog').modal();
    }

    $g('#published_on, #published_down, input.open-calendar-dialog').each(function(){
        createCalendar(this);
    });
    $g('body').on('click', '.reset-date-field', function(){
        this.closest('.date-field-wrapper').querySelector('input').value = '';
    });

    $g('.ba-modal-lg').on('shown', function(){
        setTabsUnderline();
    });

    $g('.search-ba-author-users').off('input').on('input', function(){
        var search = this.value.trim(),
            modal = $g(this).closest('.ba-modal-lg');
        modal.find('.ba-options-group').each(function(){
            var name = this.querySelector('.ba-author-name').textContent.trim().toLowerCase(),
                username = this.querySelector('.ba-author-username').textContent.trim().toLowerCase();
            if (name.indexOf(search) != -1 || username.indexOf(search) != -1) {
                this.style.display = '';
            } else {
                this.style.display = 'none';
            }
        });
    });
    $g('.user-group-select').off('customAction').on('customAction', function(){
        let group = this.querySelector('input[type="hidden"]').value,
            modal = $g(this).closest('.ba-modal-lg');
        modal.find('.ba-options-group').each(function(){
            let display = 'none'
            this.querySelectorAll('.ba-author-usergroup span').forEach(function($this){
                if ($this.textContent.trim() == group || group == '') {
                    display = '';
                }
            });
            this.style.display = display;
        });
    });
    $g('.user-sorting-select, .user-direction-select').off('customAction').on('customAction', function(){
        var sort = $g('.user-sorting-select input[type="hidden"]').val(),
            dir = $g('.user-direction-select input[type="hidden"]').val(),
            modal = $g(this).closest('.ba-modal-lg'),
            items = Array.prototype.slice.call(modal[0].querySelectorAll('.ba-options-group'));
        items.sort(function(a, b){
            var text1 = a.querySelector('.ba-author-'+sort).textContent.trim(),
                text2 = b.querySelector('.ba-author-'+sort).textContent.trim()
            if (text1 > text2) return 1;
            if (text1 < text2) return -1;
        });
        if (dir == 'desc') {
            items.reverse();
        }
        for (var i = 0; i < items.length; i++) {
            modal.find('.ba-group-wrapper').append(items[i]);
        }
    });

    $g('.comments-settings-apply').on('click', function(){
        let modal = $g('#comments-settings-dialog'),
            view = $g('input[name="ba_view"]').val(),
            moderators = modal.find('.comments-moderators-list')[0],
            obj = {
                website: {},
                commentsBannedList:{
                    emails: [],
                    words: [],
                    ip: []
                }
            }
        modal.find('.website-comments-settings').each(function(){
            if (this.type == 'checkbox') {
                obj.website[this.dataset.website] = Number(this.checked);
            } else {
                obj.website[this.dataset.website] = this.value.trim();
            }
        });
        obj.website[moderators.dataset.website] = '';
        $g(moderators).find('li[data-value]').each(function(){
            if (obj.website[moderators.dataset.website]) {
                obj.website[moderators.dataset.website] += ',';
            }
            obj.website[moderators.dataset.website] += this.dataset.value;
        });
        $g('.comments-banned-list-wrapper ul').each(function(){
            var banned = this.dataset.type;
            $g(this).find('li:not(.enter-comments-banned-item)').each(function(){
                obj.commentsBannedList[banned].push(this.textContent.trim())
            });
        });
        $g.ajax({
            type:"POST",
            dataType:'text',
            url: 'index.php?option=com_gridbox&task='+view+'.saveCommentsOptions',
            data : {
                obj : JSON.stringify(obj)
            },
            complete: function(response){
                $g('#comments-settings-dialog').modal('hide');
                showNotice(response.responseText);
            }
        });
    });
    $g('#ba-comments-users-dialog .users-table-list .ba-author-username span').on('click', function(){
        var id = this.dataset.id,
            name = this.closest('.ba-group-element').querySelector('.ba-author-name').textContent.trim();
        if ($g('#comments-settings-dialog .comments-moderators-list li[data-value="'+id+'"]').length == 0) {
            var str = '<li data-value="'+id+'"><span>'+name+'</span><i class="zmdi zmdi-close"></i></li>';
            $g('#comments-settings-dialog .comments-moderators-list li.add-comments-moderator').before(str);
            $g('#ba-comments-users-dialog').modal('hide');
        }
    });
    $g('#comments-settings-dialog .comments-moderators-list').on('click', 'i.zmdi-close', function(){
        this.closest('li').remove();
    }).on('click', '.add-comments-moderator i', function(){
        showComemntsModeratorDialog();
    });
    $g('#comments-settings-dialog .comments-banned-list-wrapper').on('click', 'i.zmdi-close', function(){
        this.closest('li').remove();
    }).on('keyup', 'input[type="text"]', function(event){
        if (event.keyCode == 13 && this.value.trim()) {
            var str = '<li><span>'+this.value.trim()+'</span><i class="zmdi zmdi-close"></i></li>';
            $g(this).closest('li').before(str);
            this.value = '';
        }
    });
    $g('#comments_recaptcha option').each(function(){
        let value = this.value;
        if (value == 'recaptcha' || value == 'recaptcha_invisible') {
            let str = '<li data-value="'+value+'">'+this.textContent.trim()+'</li>';
            $g(this).closest('.ba-group-element').find('ul').append(str);
            str = '<option value="'+value+'" {selected}>'+this.textContent.trim()+'</option>';
            $g(this).closest('.ba-options-group-element ').find('select[data-key]').each(function(){
                str = str.replace('{selected}', this.dataset.value == value ? ' selected' : '');
            }).append(str);
        }
    });
    $g('.ba-subgroup-element').each(function(){
        app.setSubgroupChilds(this);
    });
    $g('#ba-author-users-dialog .users-table-list .ba-author-username span').off('click').on('click', function(){
        var currentUser = this.closest('.ba-group-wrapper').dataset.id,
            id = this.dataset.id,
            username = this.textContent.trim(),
            flag = true,
            modal = $g('#create-new-tag-modal');
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : 'index.php?option=com_gridbox&task=authors.checkUser',
            data : {
                currentUser: currentUser,
                id: id
            },
            success: function(msg){
                if (msg != 0) {
                    showNotice(msg, 'ba-alert');
                } else {
                    $g(fontBtn).val(username).prev().val(id);
                    modal.find('input').each(function(){
                        if (!this.value.trim()) {
                            flag = false;
                        }
                    });
                    if (!flag) {
                        modal.find('.ba-btn-primary').removeClass('active-button');
                    } else {
                        modal.find('.ba-btn-primary').addClass('active-button');
                    }
                    $g('#ba-author-users-dialog').modal('hide');
                }
            }
        });
    });
    $g('#ba-orders-users-dialog .users-table-list .ba-author-username span').off('click').on('click', function(){
        let obj = {
                id: this.dataset.id,
                username: this.textContent.trim(),
            },
            str = JSON.stringify(obj);
        makeFetchRequest('index.php?option=com_gridbox&task=orders.getUserInfo', {
            id: this.dataset.id
        }).then(function(json){
            fontBtn.userInfo = json;
            $g(fontBtn).attr('data-value', str).trigger('change');
            $g('#ba-orders-users-dialog').modal('hide');
        });
    });

    $g('body').on('click', '.ba-custom-select > i, .ba-custom-select input', function(event){
        let parent = $g(this).parent();
        if (!parent.find('ul').hasClass('visible-select')) {
            event.stopPropagation();
            $g('.visible-select').removeClass('visible-select');
            parent.find('ul').addClass('visible-select');
            parent.find('li').off('click').one('click', function(){
                let text = this.textContent.trim(),
                    val = this.dataset.value;
                if (parent.hasClass('orders-status-select') || parent.hasClass('email-notification-status-select')) {
                    parent[0].style.setProperty('--status-color', this.dataset.color);
                } else if (parent.hasClass('ba-store-statistic-select') || parent.hasClass('ba-booking-calendar-select')) {
                    text = this.dataset.text;
                }
                parent.find('input[type="text"]').val(text);
                parent.find('input[type="hidden"]').val(val).trigger('change');
                parent.trigger('customAction');
            });
            parent.trigger('show');
            setTimeout(function(){
                $g('body').one('click', function(){
                    $g('.visible-select').removeClass('visible-select');
                });
            }, 50);
        }
    });

    $g('.modal').on('hide', function(){
        $g(this).addClass('ba-modal-close');
        setTimeout(function(){
            $g('.ba-modal-close').removeClass('ba-modal-close');
        }, 500);
    });

    setTimeout(function(){
        $g('.alert.alert-success').addClass('animation-out');
    }, 2000);

    app.checkGridboxData = function(obj){
        var url = 'https://www.balbooa.com/demo/index.php?',
            domain = window.location.host.replace('www.', ''),
            script = document.createElement('script');
        domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
        url += 'option=com_baupdater&task=gridbox.checkGridboxUser';
        url += '&data='+obj.data;
        if (domain[domain.length - 1] != '/') {
            domain += '/';
        }
        url += '&domain='+window.btoa(domain);
        script.src = url;
        document.head.appendChild(script);
    }

    app.showGridboxLogin = function(){
        $g('.ba-username').val('');
        $g('.ba-password').val('');
        $g('#login-modal').modal();
    }

    app.checkGridboxState = function(){
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=pages.checkGridboxState",
            success: function(msg){
                var flag = true,
                    obj;
                if (msg) {
                    obj = JSON.parse(msg);
                    flag = !obj.data;
                }
                if (flag) {
                    app.showGridboxLogin();
                } else {
                    app.checkGridboxData(obj);
                }
            }
        });
    }

    $g('#ba-gridbox-apps-dialog div.gridbox-app-element').on('click', function(event){
        app.loginItem = this;
        gridboxCallback = 'appAction';
        app.checkGridboxState();
    });

    $g('.search-gridbox-apps').off('input').on('input', function(){
        let search = this.value.trim().toLowerCase(),
            modal = $g(this).closest('.ba-modal-lg');
        modal.find('.gridbox-app-element').each(function(){
            let name = this.querySelector('.ba-title').textContent.trim().toLowerCase();
            if (name.indexOf(search) != -1) {
                this.style.display = '';
            } else {
                this.style.display = 'none';
            }
        });
    });

    $g('.create-new-tag').on('click', function(event){
        event.preventDefault();
        if ($g(this).hasClass('active-button')) {
            $g('#create-new-tag-modal').modal('hide');
            Joomla.submitbutton('tags.addTag');
        }
    });

    $g('.create-new-author').on('click', function(event){
        event.preventDefault();
        if ($g(this).hasClass('active-button')) {
            $g('#create-new-tag-modal').modal('hide');
            Joomla.submitbutton('authors.addAuthor');
        }
    });

    $g('body').on('click', function(){
        $g('.context-active').removeClass('context-active');
        $g('.ba-context-menu').hide();
    });

    $g('.export-page').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        exportId = [id];
        $g('li.export-apps').hide();
        $g('#export-dialog').modal();
        $g('.apply-export').attr('data-export', 'pages');
    });

    $g('.export-gridbox').on('mousedown', function(){
        exportId = [];
        $g('li.export-apps').css('display', '');
        $g('#export-dialog').modal();
        $g('.apply-export').attr('data-export', 'gridbox');
    });

    $g('.import-gridbox').on('mousedown', function(){
        $g('#import-dialog').modal();
        $g('.theme-import-trigger').val('');
        $g('.apply-import').removeClass('active-button');
    });

    $g('.import-joomla-content').on('mousedown', function(){
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=trashed.getCategories",
            success: function(msg){
                var array = JSON.parse(msg)
                    str = '',
                    ul = $g('#import-joomla-content-modal .availible-folders ul.root-list');
                array.forEach(function(el, i){
                    var value = '{"id":'+el.id+', "type":"'+el.type+'"}';
                    str += '<li class="root single"><label><i class="zmdi zmdi-folder"></i>';
                    str += el.title+'<input type="radio" style="display:none;"';
                    str += " name='category_id' value='"+value+"'></label>";
                    str += '</li>';
                });
                ul.html(str);
                $g('.apply-import-joomla-content').removeClass('active-button');
                $g('#import-joomla-content-modal').modal();
            }
        });
    });

    $g('.apply-import-joomla-content').on('click', function(event){
        event.preventDefault();
        if (this.classList.contains('active-button')) {
            $g('#import-joomla-content-modal').modal('hide');
            var obj;
            $g('#import-joomla-content-modal [name="category_id"]').each(function(){
                if (this.checked) {
                    obj = JSON.parse(this.value);
                    return false;
                }
            });
            $g.ajax({
                type:"POST",
                dataType:'text',
                data: {
                    type: obj.type
                },
                url:"index.php?option=com_gridbox&task=gridbox.checkJoomlaContentCount",
                success: function(msg){
                    var data = JSON.parse(msg),
                        str = '<span>'+app._('INSTALLING');
                    str += ' <span class="installed-joomla-content">0</span> / '+data.count;
                    str +='</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
                    if (data.count > 0) {
                        notification.find('p').html(str);
                        notification.removeClass('animation-out').addClass('notification-in');
                        importObject.joomla(data, obj);
                    }
                    
                }
            });
        }
    });

    var importObject = {
        data: {},
        joomla: function(obj, application){
            this.data = {
                tags: [],
                categories: []
            }
            this.data.categories[1] = 0;
            this.joomlaCategories(obj, application);
        },
        joomlaArticles: function(obj, application){
            if (obj.articles && obj.articles.length > 0) {
                var article = obj.articles.shift();
                $g.ajax({
                    type:"POST",
                    dataType:'text',
                    data: {
                        tags: importObject.data.tags,
                        categories: importObject.data.categories,
                        app_id: application.id,
                        app_type: application.type,
                        id: article.id
                    },
                    url:"index.php?option=com_gridbox&task=gridbox.importJoomlaArticles",
                    success: function(msg){
                        setTimeout(function(){
                            $g('.installed-joomla-content').each(function(){
                                this.textContent = this.textContent * 1 + 1;
                            });
                            importObject.joomlaArticles(obj, application);
                            reloadPage();
                        }, 100);
                    }
                });
            } else {
                showNotice(app._('INSTALLED'));
            }
        },
        joomlaCategories: function(obj, application){
            if (obj.categories && obj.categories.length > 0) {
                var category = obj.categories.shift();
                $g.ajax({
                    type:"POST",
                    dataType:'text',
                    data: {
                        categories: importObject.data.categories,
                        app_id: application.id,
                        id: category.id
                    },
                    url:"index.php?option=com_gridbox&task=gridbox.importJoomlaCategories",
                    success: function(msg){
                        setTimeout(function(){
                            $g('.installed-joomla-content').each(function(){
                                this.textContent = this.textContent * 1 + 1;
                            });
                            importObject.data.categories[category.id] = msg;
                            importObject.joomlaCategories(obj, application);
                            reloadPage();
                        }, 100);
                    }
                });
            } else {
                this.joomlaTags(obj, application);
            }
        },
        joomlaTags: function(obj, application){
            if (obj.tags && obj.tags.length > 0) {
                var tag = obj.tags.shift();
                $g.ajax({
                    type:"POST",
                    dataType:'text',
                    data: {
                        id: tag.id
                    },
                    url:"index.php?option=com_gridbox&task=gridbox.importJoomlaTags",
                    success: function(msg){
                        setTimeout(function(){
                            $g('.installed-joomla-content').each(function(){
                                this.textContent = this.textContent * 1 + 1;
                            });
                            importObject.data.tags[tag.id] = msg;
                            importObject.joomlaTags(obj, application);
                            reloadPage();
                        }, 100);
                    }
                });
            } else {
                this.joomlaArticles(obj, application);
            }
        }
    }

    $g('#import-joomla-content-modal .availible-folders').on('change', '[name="category_id"]', function(event){
        event.stopPropagation();
        $g('#import-joomla-content-modal .availible-folders > ul .active').removeClass('active');
        $g(this).closest('li').addClass('active');
        $g('#import-joomla-content-modal .ba-btn-primary').addClass('active-button');
    });
    $g('span.gridbox-languages').on('mousedown', function(){
        $g('#languages-dialog').modal();
    });

    $g('#languages-dialog .languages-wrapper').on('click', 'span.language-title', function(){
        $g('#languages-dialog').modal('hide');
        app.showLoading('INSTALLING');
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=pages.addLanguage",
            data:{
                method: window.atob('YmFzZTY0X2RlY29kZQ=='),
                url: gridboxApi.languages[this.dataset.key].url,
                zip: gridboxApi.languages[this.dataset.key].zip,
            },
            success: function(msg){
                showNotice(msg);
            }
        });
    });

    $g('.share-image-wrapper input[type="text"]').on('click', function(){
        checkIframe($g('#uploader-modal'), 'uploader');
        fontBtn = this;
        uploadMode = 'introImage';
    }).on('change', function(){
        $g(this).parent().find('.image-field-tooltip').css({
            'background-image': 'url('+JUri+this.value.replace(/\s/g, '%20')+')'
        });
        this.dataset.value = this.value;
    });

    $g('.ba-custom-select.language-select').on('customAction', function(){
        let lang = this.querySelector('input[type="hidden"]').value;
        app.associations.setLanguage(lang);
        app.associations.set(lang);
    });

    $g('.association-wrapper').on('click', '.association-page', function(){
        let lang = this.closest('.ba-group-element').dataset.lang,
            link = 'associations&language='+lang+'&type='+app.associations.type;
        if (app.associations.type == 'system') {
            link += '&system='+app.associations.system;
        }
        uploadMode = 'association';
        fontBtn = this;
        checkIframe($g('#pages-list-modal'), link, null, true);
    }).on('click', '.reset-association', function(){
        let $this = this.closest('.association-wrapper').querySelector('.association-page');
        $this.removeAttribute('data-id');
        $this.value = '';
    });

    $g('.select-permission-usergroup').on('customAction', function(){
        let data = this.usergroup,
            group = this.querySelector('input[type="hidden"]').value;
        $g(this).closest('.permissions-options').find('.permission-action-wrapper').each(function(){
            setGroupPermissions(data.id, data.type, group, this);
        });
    });

    $g('.select-permission-action').on('customAction', function(){
        let input = this.querySelector('input[type="hidden"]');
        calculateNewPermissions(input.usergroup, input.dataset.key, input.value, input.closest('.permission-action-wrapper'));
    });

    $g('.reset-share-image').on('click', function(){
        $g(this).parent().find('input[type="text"]').val('').attr('data-value', '');
        $g(this).parent().find('.image-field-tooltip').css('background-image', '');
    });

    $g('.select-author-username').on('click', function(){
        showUsersDialog(this.dataset.user_id, this);
    });

    $g(document).on('click', '.remove-selected-author', function(){
        this.parentNode.remove();
        let authors = [],
            li = $g('.select-post-author li[data-value]'),
            author = '';
        $g('.selected-author').each(function(){
            authors.push(this.dataset.id);
        });
        li.each(function(){
            if (authors.indexOf(this.dataset.value) == -1) {
                this.style.display = '';
            } else {
                this.style.display = 'none';
            }
        });
        if (li.length == authors.length) {
            $g('.select-post-author').hide();
        } else {
            $g('.select-post-author').css('display', '');
        }
        author = authors.join(',');
        $g('.select-post-author input[type="hidden"]').val(author);
    });

    $g('.blog-title').on('input', function(){
        var val = this.value.trim();
        if (val && val != oldTitle) {
            $g(this).closest('.modal').find('.ba-btn-primary').addClass('active-button');
        } else {
            $g(this).closest('.modal').find('.ba-btn-primary').removeClass('active-button');
        }
    });

    $g('.apply-blog-settings').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        var description = app.cke.description.getData();
        $g('.category-description').val(description);
        $g('#category-settings-dialog').modal('hide');
        updatePermissions();
        Joomla.submitbutton('apps.applySettings');
    });

    $g('.apply-single-settings').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        if (!$g(this).hasClass('active-button')) {
            return false;
        }
        $g('#single-settings-modal').modal('hide');
        Joomla.submitbutton('pages.applySingle');
    });

    $g('.activate-link').on('click', function(event){
        event.preventDefault();
        $g('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        gridboxCallback = 'dashboard';
        app.showGridboxLogin();
    });

    $g('.deactivate-link').on('click', function(event){
        event.preventDefault();
        $g('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        $g('#deactivate-dialog').modal();
    });

    $g('#apply-deactivate').on('click', function(event){
        event.preventDefault();
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=pages.checkGridboxState",
            success: function(msg){
                var obj = JSON.parse(msg),
                    url = 'https://www.balbooa.com/demo/index.php?',
                    script = document.createElement('script');
                url += 'option=com_baupdater&task=gridbox.deactivateLicense';
                url += '&data='+obj.data+'&time='+(+(new Date()));
                script.onload = function(){
                    $g.ajax({
                        type : "POST",
                        dataType : 'text',
                        url : JUri+"index.php?option=com_gridbox&task=editor.setAppLicense",
                        success: function(msg){
                            app.showNotice(app._('SUCCESSFULY_DEACTIVATED'));
                            $g('.ba-dashboard-popover-trigger[data-target="ba-dashboard-about"]').each(function(){
                                this.querySelector('i').className = 'zmdi zmdi-notifications';
                                let count = this.querySelector('.about-notifications-count');
                                count.textContent = count.textContent * 1 + 1;
                                count.style.display = '';
                            });
                            $g('.gridbox-activate-license').css('display', '');
                            $g('.gridbox-deactivate-license').hide();
                        }
                    });
                }
                script.src = url;
                document.head.appendChild(script);
            }
        });
        $g('#deactivate-dialog').modal('hide');
    });
    
    $g('.gridbox-update-wrapper').off('click').on('click', '.update-link', function(event){
        event.preventDefault();
        $g('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        gridboxCallback = 'updateAction';
        app.checkGridboxState();
    });

    $g('.apply-import').on('click', function(event){
        event.preventDefault();
        var files = document.getElementById('theme-import-file').files;
        if (files.length > 0 && this.classList.contains('active-button')) {
            var data = new FormData(),
                url = document.getElementById("adminForm").action+"&task=themes.uploadTheme&file="+files[0].name;
            app.showLoading('INSTALLING');
            data.append('file', files[0]);
            $g.ajax({
                url: url,
                data: data,
                type: 'post',
                processData: false,
                cache: false,
                contentType: false,
                success: function(msg){
                    setTimeout(function(){
                        notification.removeClass('notification-in').addClass('animation-out');
                        setTimeout(function(){
                            showNotice(msg, '');
                            setTimeout(function(){
                                window.location.href = window.location.href;
                            }, 400);
                        }, 400);
                    }, 2000);
                }
            });
            $g('#import-dialog').modal('hide');
        }
    });

    $g('.apply-export').on('click', function(event){
        event.preventDefault();
        if (this.dataset.export == 'app') {
            exportId = [$g('input[name="blog"]').val()];
        }
        let obj = {
            id: exportId,
            type: this.dataset.export,
            menu: $g('.menu-export').prop('checked')
        }
        app.exportXML(obj);
        $g('#export-dialog').modal('hide');
    });

    setCkeditor();

    $g('span.category-settings').on('mousedown', function(){
        var obj = currentContext.find('> a input').val(),
            modal = $g('#category-settings-dialog');
        obj = JSON.parse(obj);
        $g('#category-settings-dialog input[data-key="core.edit.layouts"]').closest('.ba-group-element').attr('disabled', 'true');
        app.setSubgroupChilds($g('#category-settings-dialog .permission-action-wrapper')[0]);
        app.associations.prepare(modal, obj.language, obj.id, 'category');
        $g('#category-settings-dialog .permissions-options').each(function(){
            getPermissions(obj.id, 'category', this);
        });
        $g('.category-title').val(obj.title);
        $g('.category-id').val(obj.id);
        $g('.category-parent').val(obj.parent);
        $g('.category-alias').val(obj.alias);
        $g('.apply-blog-settings').hide();
        $g('.category-settings-apply').css('display', '');
        $g('#category-settings-dialog').find('.select-data-tags, .seo-default-settings').css('display', '');
        $g('.blog-theme-select').closest('.ba-options-group').hide();
        $g('#category-settings-dialog .cke-editor-container').closest('.ba-options-group')
            .css('display', '').prev().css('display', '');
        $g('.category-access-select input[type="hidden"]').val(obj.access);
        var access = $g('.category-access-select li[data-value="'+obj.access+'"]').text().trim();
        $g('.category-access-select input[type="text"]').val(access);
        var value = $g('.category-robots-select li[data-value="'+obj.robots+'"]').text().trim();
        $g('.category-robots-select input[type="hidden"]').val(obj.robots);
        $g('.category-robots-select input[type="text"]').val(value);
        app.cke.description.setData(obj.description);
        $g('.category-meta-title').val(obj.meta_title);
        $g('.category-meta-description').val(obj.meta_description);
        $g('.category-meta-keywords').val(obj.meta_keywords);
        $g('.category-publish').prop('checked', obj.published == 1);
        let image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
        $g('.category-intro-image').val(obj.image).parent().find('.image-field-tooltip').css({
            'background-image': obj.image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        if (obj.share_image == 'share_image') {
            obj.share_image = obj.image;
        }
        image = !app.isExternal(obj.share_image) ? JUri+obj.share_image : obj.share_image;
        $g('.category-share-image').val(obj.share_image).parent().find('.image-field-tooltip').css({
            'background-image': obj.share_image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        $g('.category-share-title').val(obj.share_title);
        $g('.category-share-description').val(obj.share_description);
        let clone = document.querySelector('.category-sitemap-template').content.cloneNode(true);
        $g('#category-sitemap-options').html(clone);
        $g('#category-settings-dialog textarea[name="category_schema_markup"]').val(obj.schema_markup);
        $g('#category-settings-dialog .sitemap-override').prop('checked', Boolean(obj.sitemap_override * 1));
        $g('#category-settings-dialog .sitemap-include').prop('checked', Boolean(obj.sitemap_include * 1));
        var range = $g('#category-settings-dialog .priority').val(obj.priority).prev().val(obj.priority);
        setLinearWidth(range);
        $g('#category-settings-dialog .changefreq').val(obj.changefreq).prev().each(function(){
            this.value = $g(this).closest('.ba-custom-select').find('li[data-value="'+obj.changefreq+'"]').text().trim();
        });
        $g('#category-settings-dialog .set-group-display').each(function(){
            var action = this.checked ? 'addClass' : 'removeClass';
            $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
        });
        $g('i.zmdi-check.disabled-button').removeClass('disabled-button');
        $g('.ba-alert-container').hide();
        $g('#category-settings-dialog').modal();
    });

    $g('span.tags-settings').on('mousedown', function(){
        var obj = currentContext.find('.select-td input[type="hidden"]').val(),
            modal = $g('#category-settings-dialog');
        obj = JSON.parse(obj);
        app.associations.prepare(modal, obj.language, obj.id, 'tag');
        $g('.category-title').val(obj.title);
        $g('.category-id').val(obj.id);
        $g('.category-alias').val(obj.alias);
        $g('#category-settings-dialog .cke-editor-container');
        $g('.category-access-select').each(function(){
            var value = $g(this).find('li[data-value="'+obj.access+'"]').text().trim();
            $g(this).find('input[type="hidden"]').val(obj.access);
            $g(this).find('input[type="text"]').val(value);
        });
        $g('.category-robots-select').each(function(){
            var value = $g(this).find('li[data-value="'+obj.robots+'"]').text().trim();
            $g(this).find('input[type="hidden"]').val(obj.robots);
            $g(this).find('input[type="text"]').val(value);
        });
        $g('.select-author-username').each(function(){
            this.value = obj.username;
            this.dataset.user_id = obj.user_id;
            this.previousElementSibling.value = obj.user_id;
        });
        $g('.select-author-avatar').each(function(){
            let image = !app.isExternal(obj.avatar) ? JUri+obj.avatar : obj.avatar;
            $g(this).val(obj.avatar).parent().find('.image-field-tooltip').css({
                'background-image': obj.avatar ? 'url('+image.replace(/\s/g, '%20')+')' : ''
            });
        });
        $g('.authors-links-wrapper').each(function(){
            app.authorsSocial = JSON.parse(obj.author_social);
            let str = '';
            for (var ind in app.authorsSocial) {
                str += getAuthorPatern(ind);
            }
            $g('.authors-links-list').html(str);
        });
        app.cke.description.setData(obj.description);
        $g('.category-meta-title').val(obj.meta_title);
        $g('.category-meta-description').val(obj.meta_description);
        $g('.category-meta-keywords').val(obj.meta_keywords);
        image = !app.isExternal(obj.image) ? JUri+obj.image : obj.image;
        $g('.category-intro-image').val(obj.image).parent().find('.image-field-tooltip').css({
            'background-image': obj.image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        if (obj.share_image == 'share_image') {
            obj.share_image = obj.image;
        }
        image = !app.isExternal(obj.share_image) ? JUri+obj.share_image : obj.share_image;
        $g('.category-share-image').val(obj.share_image).parent().find('.image-field-tooltip').css({
            'background-image': obj.share_image ? 'url('+image.replace(/\s/g, '%20')+')' : ''
        });
        $g('.category-share-title').val(obj.share_title);
        $g('.category-share-description').val(obj.share_description);
        $g('#category-settings-dialog textarea[name="category_schema_markup"]').val(obj.schema_markup);
        $g('#category-settings-dialog .sitemap-override').prop('checked', Boolean(obj.sitemap_override * 1));
        $g('#category-settings-dialog .sitemap-include').prop('checked', Boolean(obj.sitemap_include * 1));
        var range = $g('#category-settings-dialog .priority').val(obj.priority).prev().val(obj.priority);
        setLinearWidth(range);
        $g('#category-settings-dialog .changefreq').val(obj.changefreq).prev().each(function(){
            this.value = $g(this).closest('.ba-custom-select').find('li[data-value="'+obj.changefreq+'"]').text().trim();
        });
        $g('#category-settings-dialog .set-group-display').each(function(){
            var action = this.checked ? 'addClass' : 'removeClass';
            $g(this).closest('.ba-group-element').nextAll()[action]('visible-subgroup subgroup-animation-ended');
        });
        $g('i.zmdi-check.disabled-button').removeClass('disabled-button');
        $g('.ba-alert-container').hide();
        $g('#category-settings-dialog').modal();
    });

    $g('.tags-settings-apply').on('click', function(){
        if ($g(this).hasClass('disabled-button')) {
            return false;
        }
        var description = app.cke.description.getData();
        $g('.category-description').val(description);
        $g('#category-settings-dialog').modal('hide');
        app.associations.save();
        Joomla.submitbutton('tags.updateTags');
    });

    $g('.authors-settings-apply').on('click', function(){
        if ($g(this).hasClass('disabled-button')) {
            return false;
        }
        var description = app.cke.description.getData(),
            social = JSON.stringify(app.authorsSocial);
        $g('.category-description').val(description);
        $g('textarea[name="author_social"]').val(social);
        $g('#category-settings-dialog').modal('hide');
        Joomla.submitbutton('authors.updateAuthors');
    });

    $g('.category-settings-apply').on('click', function(){
        if ($g(this).hasClass('disabled-button')) {
            return false;
        }
        var description = app.cke.description.getData();
        $g('.category-description').val(description);
        $g('#category-settings-dialog').modal('hide');
        updatePermissions();
        Joomla.submitbutton('apps.updateCategory');
    });

    $g('span.category-delete').on('mousedown', function(){
        var obj = currentContext.find('> a input[type="hidden"]').val();
        $g('#context-item').val(obj);
        deleteMode = 'apps.deleteCategory';
        $g('#delete-dialog').modal();
    });

    $g('span.category-duplicate').on('mousedown', function(){
        var id = currentContext.attr('data-id');
        $g('#context-item').val(id);
        Joomla.submitbutton('apps.categoryDuplicate');
    });

    $g('span.category-move').on('mousedown', function(){
        var id = currentContext.attr('data-id');
        moveTo = 'apps.categoryMoveTo';
        $g('#context-item').val(id);
        showMoveTo();
    });

    function showMoveTo()
    {
        app.fetch('index.php?option=com_gridbox&task=trashed.getCategories').then((text) => {
            let array = JSON.parse(text),
                str = drawBlogMoveTo(array),
                ul = $g('#move-to-modal .availible-folders ul.root-list');
            if (moveTo != 'apps.moveTo' && currentContext.hasClass('ba-category')) {
                ul.addClass('ba-move-category');
            } else {
                ul.removeClass('ba-move-category');
            }
            ul.html(str);
            $g('.apply-move').removeClass('active-button');
            $g('#move-to-modal').modal();
        });
    }

    $g('span.page-move').on('mousedown', function(){
        var obj = currentContext.find('.select-td input[type="hidden"]').val();
        obj = JSON.parse(obj)
        moveTo = 'apps.pageMoveTo';
        $g('#context-item').val(obj.id);
        showMoveTo();
    });

    $g('span.page-move-single').on('mousedown', function(){
        var obj = currentContext.find('.select-td input[type="hidden"]').val();
        obj = JSON.parse(obj)
        moveTo = 'trashed.restoreSingle';
        $g('#context-item').val(obj.id);
        showMoveTo();
    });

    $g('span.page-duplicate').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton('pages.contextDuplicate');
    });

    $g('span.system-page-duplicate').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton('system.contextDuplicate');
    });

    $g('span.tags-duplicate').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton('tags.contextDuplicate');
    });

    $g('span.page-trash').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'pages.contextTrash';
        $g('#delete-dialog').modal();
    });

    $g('span.system-page-trash').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'system.contextTrash';
        $g('#delete-dialog').modal();
    });

    $g('span.tags-delete').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'tags.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('span.system-page-delete').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'system.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('span.comments-delete').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val(),
            view = $g('input[name="ba_view"]').val();
        $g('#context-item').val(id);
        deleteMode = view+'.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('span.comments-approve').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val(),
            view = $g('input[name="ba_view"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton(view+'.contextApprove');
    });

    $g('span.comments-spam').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val(),
            view = $g('input[name="ba_view"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton(view+'.contextSpam');
    });

    $g('span.authors-delete').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'authors.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('span.blog-duplicate').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        app.showLoading('LOADING');
        Joomla.submitbutton('apps.contextDuplicate');
    });

    $g('span.blog-trash').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'apps.contextTrash';
        $g('#delete-dialog').modal();
    });

    $g('input.category-name').on('input', function(){
        $g('#create-new-category')[this.value.trim() ? 'addClass' : 'removeClass']('active-button');
    });

    $g('#create-new-category').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        if (this.classList.contains('active-button')) {
            $g('#create-category-modal').modal('hide');
            Joomla.submitbutton('apps.addCategory');
        }
    })

    $g('#apply-delete').on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        if (deleteMode == 'state.delete') {
            app.states.deleteState();
        } else if (deleteMode == 'country.delete') {
            app.country.deleteCountry();
        } else if (typeof(deleteMode) == 'object' && deleteMode.action && deleteMode.action == 'pages.deleteGridboxAppItem') {
            app.fetch('index.php?option=com_gridbox&task=pages.deleteGridboxAppItem', {
                blog: deleteMode.id
            }).then(() => {
                if (deleteMode.item.dataset.type != 'system_apps') {
                    app.appsList.refresh();
                }
                deleteMode.item.remove();
                showNotice(app._('COM_GRIDBOX_N_ITEMS_DELETED'));
            });
        } else if (typeof(deleteMode) == 'object' && deleteMode.action == 'delete-booking') {
            app.booking.delete(deleteMode.id);
        } else if (typeof(deleteMode) == 'object' && deleteMode.action == 'delete-booking-viewed') {
            app.booking.delete(deleteMode.id).then(() => {
                $g('#booking-details-modal').modal('hide');
            });
        } else if (typeof(deleteMode) == 'object' && deleteMode.action == 'delete-block-time') {
            app.booking.block.delete(deleteMode.id);
        } else if (typeof(deleteMode) == 'object' && deleteMode.type == 'delete-sorting-item') {
            $g(deleteMode.container).find('.sorting-checkbox input').each(function(){
                if (this.checked) {
                    this.closest('.sorting-item').remove();
                }
            }).trigger('change');
            deleteMode.btn.classList.add('disabled');
        } else if (typeof(deleteMode) == 'object' && ('classList' in deleteMode)
            && $g(deleteMode).hasClass('delete-author-social-link')) {
            let key = deleteMode.closest('.authors-link').dataset.key,
                list = {},
                i = 0;
            for (let ind in app.authorsSocial) {
                if (ind != key) {
                    list[i++] = app.authorsSocial[ind];
                }
            }
            app.authorsSocial = list;
            let str = '';
            for (var ind in app.authorsSocial) {
                str += getAuthorPatern(ind);
            }
            $g('.authors-links-list').html(str);
        } else if (deleteMode == 'delete-order-cart-item') {
            $g('.sorting-container .sorting-checkbox input').each(function(){
                if (this.checked) {
                    let key = this.value+(this.dataset.variation ? '+'+this.dataset.variation : '');
                    delete(app.cart.products[key]);
                    this.closest('.sorting-item').remove();
                }
            });
            app.calculateOrder();
        } else if (deleteMode == 'single') {
            Joomla.submitbutton('themes.contextDelete');
        } else if (deleteMode == 'array') {
            Joomla.submitform('themes.delete');
        } else if (deleteMode == 'apps.addTrash' || deleteMode == 'pages.addTrash' || deleteMode == 'system.addTrash' || deleteMode == 'tags.delete'
            || deleteMode == 'orders.delete' || deleteMode == 'productoptions.delete' || deleteMode == 'subscriptions.delete'
            || deleteMode == 'paymentmethods.delete' || deleteMode == 'shipping.delete' || deleteMode == 'promocodes.delete'
            || deleteMode == 'sales.delete' || deleteMode == 'system.delete') {
            Joomla.submitform(deleteMode);
        } else {
            submitTask = deleteMode;
            Joomla.submitbutton(deleteMode);
        }
        $g('#delete-dialog').modal('hide');
    });

    $g('span.page-delete').on('mousedown', function(){
        var id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        Joomla.submitbutton('pages.contextDelete');
    });

    $g('span.trashed-delete').on('mousedown', function(){
        let id = currentContext.find('input[type="checkbox"]').val();
        $g('#context-item').val(id);
        deleteMode = 'trashed.contextDelete';
        $g('#delete-dialog').modal();
    });

    $g('span.trashed-restore').on('mousedown', function(){
        let obj = currentContext.find('.select-td input[type="hidden"]').val();
        obj = JSON.parse(obj);
        $g('#context-item').val(obj.id);
        if (obj.app_type == 'single') {
            moveTo = 'trashed.restoreSingle';
        } else if (obj.app_type) {
            moveTo = 'trashed.restoreBlog';
        }
        showMoveTo();
    });

    $g('span.system-restore').on('mousedown', function(){
        let obj = currentContext.find('.select-td input[type="hidden"]').val();
        obj = JSON.parse(obj);
        $g('#context-item').val(obj.id);
        Joomla.submitform('system.restore');
    });

    $g('#move-to-modal .availible-folders').on('change', '[name="category_id"]', function(event){
        event.stopPropagation();
        let li = $g(this).closest('li');
        if (li.hasClass('root') && !li.hasClass('single') && !currentContext.hasClass('ba-category')) {
            return false;
        }
        $g('#move-to-modal .availible-folders > ul .active').removeClass('active');
        li.addClass('active');
        $g('#move-to-modal .apply-move').addClass('active-button');
    });

    $g('#move-to-modal .apply-move').on('click', function(event){
        event.preventDefault();
        if (!$g(this).hasClass('active-button')) {
            return false;
        }
        $g('#move-to-modal').modal('hide');
        if (moveTo == 'tags.move') {
            let data = {
                id: $g('#context-item').val(),
                folder: 1
            }
            $g('#move-to-modal input[name="category_id"]').each(function(){
                if (this.checked) {
                    data.folder = this.value;
                }
            });
            app.fetch('index.php?option=com_gridbox&task=tags.move', data).then((text) => {
                app.loadPageContent(window.location.href).then(function(){
                    loadPage();
                    showNotice(app._('SUCCESS_MOVED'), '');
                });
            })
        } else {
            Joomla.submitform(moveTo);
        }
    });

    $g('span.page-settings').on('mousedown', function(){
        var obj = currentContext.find('.select-td input[type="hidden"]').val();
        pageId = currentContext.find('.select-td input[type="checkbox"]').val();
        obj = JSON.parse(obj);
        item = $g(this);
        if (!this.dataset.callback) {
            showPageSettings(obj, currentContext[0]);
        } else {
            app[this.dataset.callback](obj);
        }
    });

    $g('span.view-frontend-page').on('mousedown', function(){
        let val = currentContext.find('.select-td input[type="hidden"]').val(),
            obj = JSON.parse(val);
        window.open(JUri+'index.php/pageID-'+obj.id, '_blank');
    });

    $g('.meta-tags .picked-tags .search-tag input').on('keyup', function(event){
        let title = this.value.trim();
        if (event.keyCode == 13 && title) {
            let str = '<li class="tags-chosen"><span>',
                tagId = 'new$'+title;
            $g('#post-tags-dialog .ba-settings-item').each(function(){
                if (title == this.textContent.trim()) {
                    tagId = this.dataset.id;
                    return false;
                }
            });
            if (tagId != 'new$'+title || document.querySelector('.picked-tags .tags-chosen i[data-remove="'+tagId+'"]')) {
                return;
            }
            str += title+'</span><i class="zmdi zmdi-close" data-remove="'+tagId+'"></i></li>';
            $g('.picked-tags .search-tag').before(str);
            str = '<option value="'+tagId+'" selected>'+title+'</option>';
            $g('select.meta_tags').append(str);
            this.value = '';
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    });

    $g('.meta-tags .picked-tags').on('click', '.zmdi.zmdi-close', function(){
        let id = $g(this).attr('data-remove');
        $g('select.meta_tags option[value="'+id+'"]').remove();
        this.closest('li').remove();
    });

    app.checkThemeInstall = (text, status) => {
        let flag = ((status && status == 200) || !status) && text == app._('SUCCESS_UPLOAD');
        if (flag) {
            notification.removeClass('notification-in').addClass('animation-out');
            setTimeout(function(){
                showNotice(text);
                setTimeout(function(){
                    window.location.href = window.location.href;
                }, 400);
            }, 400);
        } else {
            console.error(text)
        }
        console.info(flag)

        return flag;
    }

    app.updateThemes = function(obj){
        app.showLoading('INSTALLING');
        if (window.gridboxApi.plugins) {
            $g.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_gridbox&task=pages.addPlugins&tmpl=component",
                data:{
                    'plugins' : JSON.stringify(window.gridboxApi.plugins)
                },
                async : false
            });
        }
        var data = window.atob(obj.data),
            XHR = new XMLHttpRequest(),
            url = "index.php?option=com_gridbox&task=themes.downloadTheme";
        XHR.onreadystatechange = function(e){
            if (XHR.readyState == 4 && !app.checkThemeInstall(XHR.responseText, XHR.status)) {
                app.fetch('index.php?option=com_gridbox&task=themes.downloadThemePost', {
                    data: data
                }).then((text) => {
                    if (!app.checkThemeInstall(text)) {
                        $g.ajax({
                            type:"POST",
                            dataType:'text',
                            url:"index.php?option=com_gridbox&task=themes.downloadThemeCurl",
                            data: {
                                url: obj.url
                            },
                            error: function(response){
                                console.error(response)
                            },
                            success: function(text){
                                app.checkThemeInstall(text);
                            }
                        });
                    }
                });
            }
        }
        XHR.open("POST", url, true);
        XHR.send(data);
    }

    app.updateApps = function(obj){
        if (obj.type) {
            app.fetch('index.php?option=com_gridbox&task=pages.addApp', {
                type: obj.type
            }).then(function(){
                $g('#ba-gridbox-apps-dialog').modal('hide');
                reloadPage(app._('SUCCESS_INSTALL'));
            });
        } else if (obj.system) {
            if (obj.installed == 1) {
                return false;
            }
            app.fetch('index.php?option=com_gridbox&task=appslist.addSystemApp', {
                type: obj.system
            }).then(function(){
                obj.installed = 1;
                $g('#ba-gridbox-apps-dialog').modal('hide');
                reloadPage(app._('SUCCESS_INSTALL'));
            });
        }
    }

    app.updateGridbox = function(package){
        $g('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        setTimeout(function(){
            app.showLoading('UPDATING');
        }, 400);
        var XHR = new XMLHttpRequest(),
            url = 'index.php?option=com_gridbox&task=pages.updateGridbox&tmpl=component',
            data = {
                method: window.atob('YmFzZTY0X2RlY29kZQ=='),
                package: package
            };
        XHR.onreadystatechange = function(e) {
            if (XHR.readyState == 4) {
                setTimeout(function(){
                    notification[0].className = 'animation-out';
                    setTimeout(function(){
                        notification.find('p').html(app._('UPDATED'));
                        notification[0].className = 'notification-in';
                        setTimeout(function(){
                            notification[0].className = 'animation-out';
                            setTimeout(function(){
                                window.location.href = window.location.href;
                            }, 400);
                        }, 3000);
                    }, 400);
                }, 2000);
            }
        };
        XHR.open("POST", url, true);
        XHR.send(JSON.stringify(data));
    }
    
    $g('.settings-apply').on('click', function(event){
        event.stopPropagation();
        event.preventDefault();
        let title = $g('#settings-dialog .page-title').val().replace(/;/g, '').trim();
        if (!title) {
            return false;
        }
        $g('#settings-dialog').modal('hide');
        updatePermissions();
        Joomla.submitbutton('gridbox.updateParams');
    });

    $g('.modal .page-title, .modal .category-title').on('input', function(event){
        event.stopPropagation();
        event.preventDefault();
        var $this = $g(this),
            title = $this.val().trim();
        if (!title) {
            $this.closest('.modal').find('.modal-header i.zmdi-check').addClass('disabled-button');
            $this.parent().find('.ba-alert-container').show();
        } else {
            $this.closest('.modal').find('.modal-header i.zmdi-check').removeClass('disabled-button');
            $this.parent().find('.ba-alert-container').hide();
        }
    });

    function setThemeSettings(obj)
    {
        $g('#theme-edit-dialog .theme-name').val(obj.name);
        $g('#theme-edit-dialog .theme-image').val(obj.image);
        $g('#theme-edit-dialog .theme-default').prop('checked', obj.default == 1);
        $g('#theme-edit-dialog .theme-default').prop('disabled', obj.default == 1);
        if (obj.image != 'components/com_gridbox/assets/images/default-theme.png') {
            $g('#theme-edit-dialog .theme-image + i')[0].className = 'zmdi zmdi-close';
        } else {
            $g('#theme-edit-dialog .theme-image + i')[0].className = 'zmdi zmdi-attachment-alt';
        }
        $g('.theme-apply').removeClass('active-button');
        $g('#theme-edit-dialog').modal();
    }

    $g('.theme-image + i').on('click', function(){
        if (this.classList.contains('zmdi-close')) {
            $g('#theme-edit-dialog .theme-image').val('components/com_gridbox/assets/images/default-theme.png');
            $g('.theme-apply').addClass('active-button');
        }
    });

    $g('.theme-image').on('click', function(){
        uploadMode = 'themeImage';
        checkIframe($g('#uploader-modal'), 'uploader');
    });

    $g('.theme-name').on('input', function(event){
        event.stopPropagation();
        event.preventDefault();
        var val = $g(this).val().trim();
        if (val && themeTitle != val) {
            $g('.theme-apply').addClass('active-button');
        } else {
            $g('.theme-apply').removeClass('active-button');
        }
    });

    $g('.theme-default').on('change', function(event){
        event.stopPropagation();
        event.preventDefault();
        var val = this.value.trim();
        if (val && themeTitle != val) {
            $g('.theme-apply').addClass('active-button');
        } else {
            $g('.theme-apply').removeClass('active-button');
        }
    });

    $g('.theme-apply').on('click', function(event){
        event.stopPropagation();
        event.preventDefault();
        if (!$g(this).hasClass('active-button')) {
            return false;
        }
        var name = $g('#theme-edit-dialog .theme-name').val(),
            image = $g('.theme-image').val();
            defaultTheme = Number($g('#theme-edit-dialog .theme-default').prop('checked')),
            oldDefault = Number($g('#theme-edit-dialog .theme-default').prop('disabled'));
        $g.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_gridbox&task=theme.updateParams",
            data:{
                ba_id: pageId,
                image : image,
                theme_title: name,
                default_theme: defaultTheme,
                old_default: oldDefault
            },
            success: function(msg){
                showNotice(msg)
                if (defaultTheme == 1 && oldDefault == 0) {
                    var i = $g('.installed-themes-view .gridbox-app-item span.default-theme');
                    $g('.installed-themes-view .gridbox-app-item p').attr('data-default', 0);
                    $g(item).find('p').attr('data-default', 1).before(i);
                }
                item.querySelector('.image-container').dataset.image = image;
                if (app.isExternal(image)) {
                    $g(item).find('.image-container').css('background-image', 'url('+image.replace(/\s/g, '%20')+')');
                } else {
                    $g(item).find('.image-container').css('background-image', 'url(../'+image.replace(/\s/g, '%20')+')');
                }
                item.querySelector('p span').textContent = name;
                $g('#theme-edit-dialog').modal('hide');
            }
        });
    });
    if ('minicolors' in $g.fn) {
        app.setMinicolors();
    }
    app.multicategory.start();
    loadPage(true);
    $g('#booking-calendar-default-hours-options').on('click', '.booking-calendar-add-hours', function(){
        let content = document.querySelector('template.booking-calendar-default-hours').content.cloneNode(true),
            subgroup = this.closest('.ba-options-group').querySelector('.ba-subgroup-element');
        subgroup.append(content);
        subgroup.style.setProperty('--subgroup-childs', subgroup.querySelectorAll('.ba-group-element').length)
    }).on('click', '.booking-calendar-delete-hours', function(){
        let subgroup = this.closest('.ba-subgroup-element');
        this.closest('.ba-group-element').remove();
        subgroup.style.setProperty('--subgroup-childs', subgroup.querySelectorAll('.ba-group-element').length);
    }).on('change', 'input[data-option="enable"]', function(){
        this.closest('.ba-group-element').classList[this.checked ? 'add' : 'remove']('booking-calendar-default-hours-enabled');
    });
    $g('.booking-calendar-settings-apply').on('click', function(){
        let modal = $g('#booking-calendar-settings-dialog'),
            obj = {
                limitation: {
                    enable: modal.find('input[data-group="limitation"][data-option="enable"]')[0].checked
                },
                default: {}
            },
            data = {};
        modal.find('.ba-group-element[data-limitation]').each(function(){
            obj.limitation[this.dataset.limitation] = {
                value: this.querySelector('input').value,
                format: this.querySelector('select').value
            }
        })
        modal.find('.ba-options-group[data-day]').each(function(){
            let hours = [];
            this.querySelectorAll('.ba-group-element').forEach((element) => {
                hours.push({
                    start: element.querySelector('select[data-option="start"]').value,
                    end: element.querySelector('select[data-option="end"]').value
                });
            });
            obj.default[this.dataset.day] = {
                enable: this.querySelector('input[data-option="enable"]').checked,
                hours: hours
            }
        });
        for (let ind in obj) {
            data[ind] = JSON.stringify(obj[ind]);
        }
        app.fetch('index.php?option=com_gridbox&task=bookingcalendar.updateSettings', data).then((text) => {
            app.showNotice(text);
        });
        modal.modal('hide');
    });
});

app.modal = {
    setHeights: () => {
        document.querySelectorAll('body .modal.in').forEach(function(modal){
            app.modal.setHeight(modal);
        });
    },
    setHeight: (modal) => {
        modal.style.setProperty('--modal-offset-height', modal.offsetHeight+'px')
    }
}

window.addEventListener('resize', function(){
    app.modal.setHeights();
});

document.addEventListener('DOMContentLoaded', function(){
    let script = document.createElement('script');
    script.onload = function(){
        if (window.installedPlugins) {
            for (let key in gridboxApi.plugins) {
                for (let ind in gridboxApi.plugins[key]) {
                    if (installedPlugins[ind]) {
                        delete(gridboxApi.plugins[key][ind]);
                    }
                }
            }
            let flag = true;
            for (let key in gridboxApi.plugins) {
                flag = true;
                for (let ind in gridboxApi.plugins[key]) {
                    flag = false;
                }
                if (flag) {
                    delete(gridboxApi.plugins[key])
                }
            }
            flag = true;
            for (let key in gridboxApi.plugins) {
                flag = false;
                break;
            }
            if (flag) {
                delete(gridboxApi.plugins)
            }
        }
        $g.ajax({
            type : "POST",
            dataType : 'text',
            url : 'index.php?option=com_gridbox&task=pages.versionCompare',
            data : {
                version: gridboxApi.version
            },
            success: function(msg){
                if (msg == -1) {
                    $g('.gridbox-update-wrapper').each(function(){
                        this.classList.add('gridbox-update-available');
                        this.querySelector('i').className = 'zmdi zmdi-alert-triangle';
                        this.querySelector('span').textContent = app._('UPDATE_AVAILABLE');
                        if (this.classList.contains('gridbox-update-wrapper')) {
                            let a = document.createElement('a');
                            a.className = 'update-link dashboard-link-action';
                            a.href = "#";
                            a.textContent = app._('UPDATE');
                            this.appendChild(a);
                        }
                    });
                    $g('.ba-dashboard-popover-trigger[data-target="ba-dashboard-about"]').each(function(){
                        this.querySelector('i').className = 'zmdi zmdi-notifications';
                        let count = this.querySelector('.about-notifications-count');
                        count.textContent = count.textContent * 1 + 1;
                        count.style.display = '';
                    });
                }
            }
        });
        gridboxApi.languages.forEach(function(el, ind){
            var str = '<div class="language-line"><span class="language-img"><img src="'+el.flag+'">';
            str += '</span><span class="language-title" data-key="'+ind+'">'+el.title;
            str += '</span><span class="language-code">'+el.code+'</span></div>';
            $g('#languages-dialog .languages-wrapper').append(str);
        });
        let div = document.querySelector('#ba-gridbox-themes-dialog .upload-theme');
        if (div) {
            let str = title = '',
                demo = 'https://www.balbooa.com/showcase-template/gridbox-themes/';
            gridboxApi.themes.forEach(function(el, ind){
                title = el.title.toLowerCase();
                let uri = demo+title.replace(/\s/g, '-');
                str += '<div class="gridbox-app-element" data-id="'+ind+'"><div class="gridbox-app-item-body">'+
                    '<div class="image-container" background-image="'+el.image+'"><img src="'+el.image+'"></div>'+
                    '<p data-default="0"><span class="ba-title">'+el.title+'</span></p></div>'+
                    '<div class="gridbox-app-item-footer">'+
                    '<a class="gridbox-app-item-footer-action" href="#"><i class="zmdi zmdi-download"></i>'+
                    '<span class="ba-tooltip ba-bottom ba-hide-element">'+app._('IMPORT')+'</span></a>'+
                    '<a class="gridbox-app-item-footer-action footer-action-view theme-demo-link" href="'+uri+'" target="_blank">'+
                    '<i class="zmdi zmdi-eye"></i>'+
                    '<span class="ba-tooltip ba-bottom ba-hide-element">'+app._('VIEW')+'</span></a>'+
                    '</div></div>';
            });
            div.innerHTML = str;
        }
        $g('.gridbox-apps-wrapper div.gridbox-app-element').on('click', function(event){
            if (!event.target || !(event.target.classList.contains('theme-demo-link') || event.target.closest('.theme-demo-link'))) {
                event.preventDefault();
            }
        });

        $g('#ba-gridbox-themes-dialog div.gridbox-app-element').on('click', function(event){
            if (!event.target || !(event.target.classList.contains('theme-demo-link') || event.target.closest('.theme-demo-link'))) {
                app.loginItem = this;
                gridboxCallback = 'themeAction';
                app.checkGridboxState();
            }
        });
    }
    let classList = document.body.classList;
    if (classList.contains('view-dashboard') || classList.contains('view-themes')) {
        script.type = 'text/javascript';
        script.src = 'https://www.balbooa.com/updates/gridbox/gridboxApi/admin/gridboxApi.js';
        document.head.appendChild(script);
    }
});