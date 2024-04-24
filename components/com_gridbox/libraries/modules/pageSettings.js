/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

!function($) {
    let sortGroups = {}, 
        gridSorting = function(element, options){
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
                    let helper = $($this.handle),
                        place = $($this.placeholder),
                        delta = {};
                    $this.prepareData($this);
                    $(document).on('mousemove.gridSorting', function(event){
                        if (!document.body.classList.contains('grid-sorting-started')) {
                            $this.handle.classList.add('sorting-grid-handle-item');
                            if ($this.options.group) {
                                $this.handle.classList.add($this.options.group);
                            }
                            document.body.append($this.handle);
                            $this.placeholder.classList.add('sorting-grid-placeholder-item');
                            delta.x = $this.css.left - event.clientX;
                            delta.y = $this.css.top - event.clientY;
                            helper.css($this.css);
                            document.body.classList.add('grid-sorting-started');
                            if ($this.options.className) {
                                document.body.classList.add($this.options.className);
                            }
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
                        
                        //return false;
                    }).off('mouseleave.gridSorting').on('mouseleave.gridSorting', function(){
                        $(document).trigger('mouseup.gridSorting');
                    }).off('mouseup.gridSorting').on('mouseup.gridSorting', function(){
                        $this.handle.classList.add('grid-sorting-return-animation');
                        helper.css($this.css);
                        setTimeout(function(){
                            $this.placeholder.classList.remove('sorting-grid-placeholder-item');
                            $this.handle.remove();
                            $this.placeholder = $this.handle = null;
                            $this.elements = {};
                            $this.options.change($this.item);
                        }, 300);
                        document.body.classList.remove('grid-sorting-started');
                        if ($this.options.className) {
                            document.body.classList.remove($this.options.className);
                        }
                        $(document).off('mousemove.gridSorting mouseup.gridSorting mouseleave.gridSorting');
                    });
                    
                    //return false;
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
        change : function(){},
        start : function(){}
    }
}($g);

app.associations = {
    prepare: function(lang){
        this.id = app.editor.themeData.id * 1;
        this.type = !app.editor.themeData.edit_type ? 'page' : 'system';
        this.setLanguage(lang);
        app.fetch(JUri+'index.php?option=com_gridbox&task=associations.getLinks', {
            id: this.id,
            type: this.type
        }).then(function(text){
            let data = JSON.parse(text);
            app.associations.set(lang, data);
        });
    },
    setLanguage: function(language){
        $g('.language-select').each(function(){
            let flag = 'url('+JUri+'components/com_gridbox/assets/images/flags/'+language+'.png)',
                value = this.querySelector('li[data-value="'+language+'"]').textContent.trim();
            this.querySelector('input[type="hidden"]').value = language;
            this.querySelector('input[type="text"]').value = value;
            this.style.setProperty('--flag-img', flag);
            this.dataset.lang = language;
        });
    },
    set: function(lang, data){
        $g('.language-associations-group').each(function(){
            this.querySelectorAll('.ba-group-element, .blog-post-editor-group-element').forEach(function($this){
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
        }
        $g('#settings-dialog .language-associations-group .association-page').each(function(){
            if (this.dataset.id) {
                data.items.push(this.dataset.id)
            }
        });
        app.fetch(JUri+'index.php?option=com_gridbox&task=associations.saveLinks', data);
    }
}

function getFormData(data)
{
    let formData = new FormData();
    if (data) {
        for (let ind in data) {
            formData.append(ind, data[ind]);
        }
    }

    return formData;
}

async function makeFetchRequest(url, data)
{
    let body = getFormData(data),
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
        console.info(app.getErrorText(text));
    }

    return response;
}

app.productVariations = {};
app.productOptions = {};
app.productImages = {};
document.querySelectorAll('.field-sorting-wrapper.product-options .selected-items').forEach(function($this){
    let thumb = $this.querySelector('.ba-item-thumbnail');
    app.productImages[$this.dataset.key] = [];
    for (let ind in thumb.dataset) {
        if (/image-\d+/.test(ind)) {
            app.productImages[$this.dataset.key].push(thumb.dataset[ind]);
            thumb.removeAttribute('data-'+ind);
        }
    }
});
document.querySelectorAll('.blog-post-editor-options-group[data-field-type="product-variations"]').forEach(function(div){
    let array = div.querySelectorAll('.variations-table-body .variations-table-row');
    array.forEach(function($this){
        app.productVariations[$this.dataset.key] = {
            price: $this.querySelector('.price-cell input').value,
            sale_price: $this.querySelector('.sale-price-cell input').value,
            sku: $this.querySelector('.sku-cell input').value,
            stock: $this.querySelector('.stock-cell input').value,
            weight: $this.querySelector('.weight-cell input').value,
            default: Boolean($this.querySelector('.default-cell').dataset.default * 1)
        }
    });
    div.style.display = array.length > 0 ? '' : 'none';
});
document.querySelectorAll('#product-options-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=editor.getProductOptions').then(function(json){
        let ul = modal.querySelector('ul');
        json.forEach(function(el){
            let li = document.createElement('li'),
                html = '<span class="ba-item-thumbnail">';
            html += '<i class="zmdi zmdi-invert-colors"></i>';
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            el.options = JSON.parse(el.options);
            app.productOptions[el.id] = el;
            li.dataset.value = el.id;
            li.dataset.type = el.field_type;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

document.querySelectorAll('#subscription-usergroups-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=editor.getUserGroups').then(function(json){
        let ul = modal.querySelector('ul');
        json.forEach(function(el){
            let li = document.createElement('li'),
                html = '<span class="ba-item-thumbnail">';
            html += '<i class="zmdi zmdi-account-circle"></i>';
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            li.dataset.value = JSON.stringify(el);
            li.dataset.id = el.id;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

document.querySelectorAll('#subscription-upgrade-plans-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.getProductsList', {
        id: app.editor.themeData.id,
        type: 'subscription'
    }).then(function(json){
        let ul = modal.querySelector('ul');
        json.list.forEach(function(el){
            let li = document.createElement('li'),
                price = json.currency.position ? el.price+' '+json.currency.symbol : json.currency.symbol+' '+el.price,
                html = '<span class="ba-item-thumbnail"';
            if (el.image) {
                html += ' style="background-image: url('+el.image+')"';
            }
            html += '>';
            if (!el.image) {
                html += '<i class="zmdi zmdi-label"></i>';
            }
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            html += '<span class="picker-item-price">'+price+'</span>'
            li.dataset.value = JSON.stringify(el);
            li.dataset.id = el.id;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

document.querySelectorAll('#subscription-product-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.getProductsList', {
        id: app.editor.themeData.id,
        type: 'digital'
    }).then(function(json){
        let ul = modal.querySelector('ul');
        json.list.forEach(function(el){
            let li = document.createElement('li'),
                price = json.currency.position ? el.price+' '+json.currency.symbol : json.currency.symbol+' '+el.price,
                html = '<span class="ba-item-thumbnail"';
            if (el.image) {
                html += ' style="background-image: url('+el.image+')"';
            }
            html += '>';
            if (!el.image) {
                html += '<i class="zmdi zmdi-label"></i>';
            }
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            html += '<span class="picker-item-price">'+price+'</span>'
            li.dataset.value = JSON.stringify(el);
            li.dataset.id = el.id;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

document.querySelectorAll('#related-product-dialog').forEach(function(modal){
    makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.getProductsList', {
        id: app.editor.themeData.id
    }).then(function(json){
        let ul = modal.querySelector('ul');
        json.list.forEach(function(el){
            let li = document.createElement('li'),
                price = json.currency.position ? el.price+' '+json.currency.symbol : json.currency.symbol+' '+el.price,
                html = '<span class="ba-item-thumbnail"';
            if (el.image) {
                html += ' style="background-image: url('+el.image+')"';
            }
            html += '>';
            if (!el.image) {
                html += '<i class="zmdi zmdi-label"></i>';
            }
            html += '</span><span class="picker-item-title">'+el.title+'</span>';
            html += '<span class="picker-item-price">'+price+'</span>'
            li.dataset.value = JSON.stringify(el);
            li.dataset.id = el.id;
            li.innerHTML = html;
            ul.append(li);
        });
    });
});

app.badges = {
    addBadgeItem: function($this){
        if (!$this.clicked) {
            $this.clicked = true;
            makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.addProductBadge').then(function(json){
                $this.clicked = false;
                app.badges.addBadge(json);
            });
        }
    },
    calculateSale:function(){
        let price = document.querySelector('.blog-post-editor-options-group[data-field-key="price"] input').value,
            sale = document.querySelector('.blog-post-editor-options-group[data-field-key="sale_price"] input').value,
            value = '- '+(price == 0 ? 0 : Math.round(100 - ((sale === '' ? price : sale) * 100 / price))) + '%';
        app.badges.ul.querySelectorAll('li:first-child').forEach(function(li){
            let obj = JSON.parse(li.dataset.value);
            obj.title = value;
            li.dataset.title = value;
            li.dataset.value = JSON.stringify(obj);
            li.querySelector('.product-badge-title').textContent = value;
            li.querySelector('input').value = value;
        });
        $g('.field-sorting-wrapper.product-badges .selected-items[data-id="1"] .selected-items-name').text(value);
    },
    addBadge: function(json){
        let content = app.badges.content.cloneNode(true),
            li = content.querySelector('li');
        li.querySelector('.product-badge-title').textContent = json.title;
        li.querySelector('.product-badge-color-wrapper').style.setProperty('--badge-color', json.color);
        li.querySelector('input').value = json.title;
        li.dataset.title = json.title;
        li.dataset.value = JSON.stringify(json);
        if (json.type == 'sale') {
            li.querySelector('.edit-badge-item').remove();
            li.querySelector('.save-badge-item').remove();
            li.querySelector('.delete-badge-item').remove();
        }
        app.badges.ul.append(content);
    },
    toggleClass: function(el, action, classes){
        classes.split(' ').forEach(function(className){
            if (className) {
                el.classList[action](className);
            }
        })
    },
    loadStoreBadges: function(){
        app.badges.content = document.querySelector('template.product-badge-li').content;
        app.badges.ul =  document.querySelector('#product-badges-dialog ul');
        makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.getStoreBadge').then(function(json){
            json.forEach(function(obj){
                app.badges.addBadge(obj);
                app.badges.calculateSale()
            })
        });
    },
    editColor: function(){
        setMinicolorsColor(fontBtn.parentNode.style.getPropertyValue('--badge-color').trim());
        let modal = $g('#color-variables-dialog');
        modal.find('.nav-tabs li:last').hide();
        openPickerModal(modal, fontBtn);
    },
    editBadge: function(li){
        let obj = JSON.parse(li.dataset.value),
            input = li.querySelector('input');
        app.badges.ul.classList.add('product-badge-editing');
        input.value = obj.title;
        app.badges.toggleClass(li, 'add', 'editing-product-badge prevent-event');
        input.setSelectionRange(obj.title.length, obj.title.length);
        input.focus();
    },
    closeEdit: function(){
        app.badges.ul.querySelectorAll('li.editing-product-badge').forEach(function(el){
            let obj = JSON.parse(el.dataset.value);
            app.badges.toggleClass(el, 'remove', 'editing-product-badge prevent-event');
            el.querySelector('.product-badge-color-wrapper').style.setProperty('--badge-color', obj.color);
        });
        app.badges.ul.classList.remove('product-badge-editing');
    },
    saveBadge: function(li){
        let obj = JSON.parse(li.dataset.value);
        obj.title = li.querySelector('input').value.trim();
        obj.color = li.querySelector('.product-badge-color-wrapper').style.getPropertyValue('--badge-color').trim();
        makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.updateStoreBadge', obj).then(function(){
            li.querySelector('.product-badge-title').textContent = obj.title;
            li.dataset.value = JSON.stringify(obj);
            li.dataset.title = obj.title;
            app.badges.toggleClass(li, 'remove', 'editing-product-badge prevent-event');
            app.badges.ul.classList.remove('product-badge-editing');
            $g('.field-sorting-wrapper.product-badges .selected-items[data-id="'+obj.id+'"]').each(function(){
                this.querySelector('.selected-items-color').style.setProperty('--badge-color', obj.color);
                this.querySelector('.selected-items-name').textContent = obj.title;
            });
        });
    },
    deleteBadge: function(li){
        let obj = JSON.parse(li.dataset.value);
        makeFetchRequest(JUri+'index.php?option=com_gridbox&task=store.deleteStoreBadge', obj).then(function(){
            li.remove();
            $g('.field-sorting-wrapper.product-badges .selected-items[data-id="'+obj.id+'"]').remove();
        });
    }
}

document.querySelectorAll('#product-badges-dialog').forEach(function(){
    app.badges.loadStoreBadges();
});

$g('#ba-group-product-pricing .field-sorting-wrapper.product-badges .product-badges-title-wrapper i').on('click', function(){
    fontBtn = this;
    app.badges.closeEdit();
    showDataTagsDialog('product-badges-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value),
        wrapper = $g('.field-sorting-wrapper.product-badges .selected-items-wrapper'),
        str = '';
    if (!wrapper.find('.selected-items[data-id="'+obj.id+'"]').length) {
        str += '<span class="selected-items" data-id="'+obj.id+'"><span class="selected-items-color" style="--badge-color: ';
        str += obj.color+';"></span><span class="selected-items-name">'+obj.title+'</span>';
        str += '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';
        wrapper.append(str);
    }
});

$g('[data-field-type="subscription-action"] select').each(function(){
    this.subscriptionAction = function(){
        let obj = {
                products: ['subscription-products', 'subscription-remove'],
                groups: ['subscription-groups']
            },
            display = key = null;
        for (key in obj) {
            display = (key == this.value || this.value == 'full') ? '' : 'none';
            obj[key].forEach(function(type){
                document.querySelector('[data-field-type="'+type+'"]').style.display = display;
            });
        }
    }
    this.subscriptionAction();
}).on('change', function(){
    this.subscriptionAction();
});

$g('.add-new-renewal-plan').on('click', function(){
    if (!this.renewal) {
        this.renewalPlan = this.closest('.sorting-container').querySelector('.renewal-plan-template').content;
        this.renewalPlans = this.closest('.sorting-container').querySelector('.renewal-plans');
    }
    let clone = this.renewalPlan.cloneNode(true);
    clone.querySelector('.renewal-plan').dataset.key = 'plan-'+(+new Date());
    this.renewalPlans.append(clone);
});

$g('.subscription-products-title-wrapper i').on('click', function(){
    fontBtn = this;
    document.querySelectorAll('#subscription-product-dialog li').forEach(function(li){
        let selected = document.querySelector('.subscription-products .selected-items[data-id="'+li.dataset.id+'"]');
        li.classList[selected ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog('subscription-product-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value),
        wrapper = $g('.field-sorting-wrapper.subscription-products .selected-items-wrapper'),
        str = '';
    if (!wrapper.find('.selected-items[data-id="'+obj.id+'"]').length) {
        str += '<span class="selected-items" data-id="'+obj.id+'">';
        str += '<span class="ba-item-thumbnail"'+(obj.image ? ' style="background-image:url('+obj.image+')"' : '')+'>';
        if (!obj.image) {
            str += '<i class="zmdi zmdi-label"></i>';
        }
        str += '</span>';
        str += '<span class="selected-items-name">'+obj.title+'</span>';
        str += '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';
        wrapper.append(str);
    }
});

$g('.upgrade-plans-title-wrapper i').on('click', function(){
    fontBtn = this;
    document.querySelectorAll('#subscription-upgrade-plans-dialog li').forEach(function(li){
        let selected = document.querySelector('.upgrade-plans .selected-items[data-id="'+li.dataset.id+'"]');
        li.classList[selected ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog('subscription-upgrade-plans-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value),
        wrapper = $g('.field-sorting-wrapper.upgrade-plans .selected-items-wrapper'),
        str = '';
    if (!wrapper.find('.selected-items[data-id="'+obj.id+'"]').length) {
        str += '<span class="selected-items" data-id="'+obj.id+'">';
        str += '<span class="ba-item-thumbnail"'+(obj.image ? ' style="background-image:url('+obj.image+')"' : '')+'>';
        if (!obj.image) {
            str += '<i class="zmdi zmdi-label"></i>';
        }
        str += '</span>';
        str += '<span class="selected-items-name">'+obj.title+'</span>';
        str += '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';
        wrapper.append(str);
    }
});

$g('.subscription-groups-title-wrapper i').on('click', function(){
    fontBtn = this;
    document.querySelectorAll('#subscription-usergroups-dialog li').forEach(function(li){
        let selected = document.querySelector('.subscription-groups .selected-items[data-id="'+li.dataset.id+'"]');
        li.classList[selected ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog('subscription-usergroups-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value),
        wrapper = $g('.field-sorting-wrapper.subscription-groups .selected-items-wrapper'),
        str = '';
    if (!wrapper.find('.selected-items[data-id="'+obj.id+'"]').length) {
        str += '<span class="selected-items" data-id="'+obj.id+'">';
        str += '<span class="ba-item-thumbnail">';
        str += '<i class="zmdi zmdi-account-circle"></i>';
        str += '</span>';
        str += '<span class="selected-items-name">'+obj.title+'</span>';
        str += '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';
        wrapper.append(str);
    }
});


$g('#ba-group-related-product .related-product-title-wrapper i').on('click', function(){
    fontBtn = this;
    document.querySelectorAll('#related-product-dialog li').forEach(function(li){
        let selected = document.querySelector('.related-product .selected-items[data-id="'+li.dataset.id+'"]');
        li.classList[selected ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog('related-product-dialog');
}).on('change', function(){
    let obj = JSON.parse(this.dataset.value),
        wrapper = $g('.field-sorting-wrapper.related-product .selected-items-wrapper'),
        str = '';
    if (!wrapper.find('.selected-items[data-id="'+obj.id+'"]').length) {
        str += '<span class="selected-items" data-id="'+obj.id+'">';
        str += '<span class="ba-item-thumbnail"'+(obj.image ? ' style="background-image:url('+obj.image+')"' : '')+'>';
        if (!obj.image) {
            str += '<i class="zmdi zmdi-label"></i>';
        }
        str += '</span>';
        str += '<span class="selected-items-name">'+obj.title+'</span>';
        str += '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';
        wrapper.append(str);
    }
});

$g('.picker-search').on('input', function(){
    let search = this.value.toLowerCase(),
        li = this.closest('div.modal-list-type-wrapper').querySelectorAll('li[data-value]');
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        li.forEach(function($this){
            let title = ($this.dataset.title ? $this.dataset.title : $this.textContent).toLowerCase();
            $this.style.display = search == '' || title.indexOf(search) != -1 ? '' : 'none';
        });
    }, 300);
});

$g('.ba-modal-list-picker').on('click', '.prevent-event', function(event){
    event.preventDefault();
    event.stopPropagation();
}).on('click', '.add-badge-item', function(){
    app.badges.addBadgeItem(this);
}).on('click', '.edit-product-badge-color', function(){
    fontBtn = this;
    app.badges.editColor();
}).on('minicolorsInput', '.edit-product-badge-color', function(){
    this.parentNode.style.setProperty('--badge-color', this.dataset.rgba);
}).on('click', '.edit-badge-item', function(){
    app.badges.editBadge(this.closest('li'));
}).on('click', '.close-badge-edit', function(){
    app.badges.closeEdit();
}).on('click', '.save-badge-item', function(){
    app.badges.saveBadge(this.closest('li'));
}).on('click', '.delete-badge-item', function(){
    app.itemDelete = {
        type: 'delete-badge-item',
        item: this.closest('li')
    };
    app.checkModule('deleteItem');
}).on('click', 'li', function(){
    if (!this.classList.contains('prevent-event')) {
        fontBtn.dataset.value = this.dataset.value;
        $g(fontBtn).trigger('change');
        $g(this).closest('.ba-modal-list-picker').modal('hide');
    }
});

$g('body').on('click', '.remove-selected-items', function(){
    let array = ['.product-badges', '.related-product', '.subscription-products',
            '.subscription-groups', '.upgrade-plans'],
        $this = this,
        flag = array.some(function(className){
            return $this.closest(className);
        });
    if (flag) {
        this.closest('.selected-items').remove();
    }
}).on('click', '.variations-table-body .default-cell[data-default]', function(){
    let bool = !Boolean(this.dataset.default * 1),
        key = this.closest('.variations-table-row').dataset.key;
    this.dataset.default = Number(bool);
    app.productVariations[key].default = bool;
    for (let ind in app.productVariations) {
        if (ind != key) {
            app.productVariations[ind].default = false;
            $g('.variations-table-row[data-key="'+ind+'"] .default-cell').attr('data-default', 0);
        }
    }
}).on('click', '#ba-group-product-variations .product-options .remove-selected-items', function(){
    let item = this.closest('.selected-items'),
        key = item.dataset.key;
    item.remove();
    $g('.variations-table-body .variations-table-row').each(function(){
        let keys = this.dataset.key.split('+');
        if (keys.indexOf(key) != -1) {
            delete app.productVariations[this.dataset.key];
            this.remove();
        }
    });
}).on('click', '.add-new-product-options-value i', function(){
    let ul = $g('#product-options-values-dialog ul').empty(),
        id = this.closest('.sorting-item').dataset.id,
        options = app.productOptions[id].options;
    fontBtn = this;
    for (let ind in options) {
        let html = '<span class="ba-item-thumbnail">',
            li = document.createElement('li'),
            selected = this.closest('.sorting-item').querySelector('.selected-items[data-key="'+options[ind].key+'"]');
        html += '<i class="zmdi zmdi-invert-colors"></i>';
        html += '</span><span class="picker-item-title">'+options[ind].title+'</span>';
        li.dataset.value = JSON.stringify(options[ind]);
        li.dataset.key = options[ind].key;
        li.innerHTML = html;
        ul.append(li);
        li.classList[selected ? 'add' : 'remove']('selected');
    }
    showDataTagsDialog('product-options-values-dialog')
}).on('change', '.add-new-product-options-value i', function(){
    let obj = JSON.parse(this.dataset.value),
        span = createProductOptionItem(obj);
    this.closest('.sorting-item').querySelector('.selected-items-wrapper').insertAdjacentHTML('beforeend', span);
    prepareProductVariations();
}).on('click', '.add-new-extra-product-options i', function(){
    let ul = $g('#product-options-values-dialog ul').empty(),
        id = this.closest('.sorting-item').dataset.id,
        options = app.productOptions[id].options;
    fontBtn = this;
    for (let ind in options) {
        let html = '<span class="ba-item-thumbnail">',
            li = document.createElement('li'),
            selected = this.closest('.sorting-item').querySelector('.extra-product-options-row[data-key="'+options[ind].key+'"]');
        html += '<i class="zmdi zmdi-invert-colors"></i>';
        html += '</span><span class="picker-item-title">'+options[ind].title+'</span>';
        li.dataset.value = JSON.stringify(options[ind]);
        li.dataset.key = options[ind].key;
        li.innerHTML = html;
        ul.append(li);
        li.classList[selected ? 'add' : 'remove']('selected');
    }
    showDataTagsDialog('product-options-values-dialog')
}).on('change', '.add-new-extra-product-options i', function(){
    let obj = JSON.parse(this.dataset.value),
        html = '<div class="extra-product-options-row" data-key="'+obj.key+'">'+
        '<div class="extra-product-option-title">'+obj.title+'</div>'+
        '<div class="extra-product-option-price" data-field-type="price"><input type="text" data-decimals="10"></div>'+
        '<div class="extra-product-option-weight" data-field-type="price"><input type="text" data-decimals="2"></div>'+
        '<div class="extra-product-option-default"><i class="zmdi zmdi-star" data-default="0"></i></div>'+
        '<div class="extra-product-option-icons">'+
        '<span class="delete-extra-product-option"><i class="zmdi zmdi-delete"></i></span>'+
        '</div>';
        '</div>';
    this.closest('.sorting-item').querySelector('.extra-product-options-tbody').insertAdjacentHTML('beforeend', html);
}).on('click', '.product-extra-options .extra-product-option-default i', function(){
    let bool = !Boolean(this.dataset.default * 1),
        key = this.closest('.extra-product-options-row').dataset.key;
    this.dataset.default = Number(bool);
    this.closest('.extra-product-options-tbody').querySelectorAll('.extra-product-options-row').forEach(function(option){
        if (key != option.dataset.key) {
            option.querySelector('.extra-product-option-default i').dataset.default = 0;
        }
    });
}).on('click', '#ba-group-product-variations .product-extra-options .delete-extra-product-option i', function(event){
    event.stopPropagation();
    this.closest('.extra-product-options-row').remove()
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
            str += image+')"></span><span class="ba-author-name">'+text+'</span>';
            str += '<i class="zmdi zmdi-close remove-selected-author"></i></span>';
            $g('.ba-custom-author-select').before(str);
            parent.trigger('customAction');
            parent.parent().find('.selected-author').each(function(){
                authors.push(this.dataset.id);
            });
            li.each(function(){
                if (authors.indexOf(this.dataset.value) == -1) {
                    $g('.ba-custom-author-select li[data-value="'+this.dataset.value+'"]').css('display', '');
                } else {
                    $g('.ba-custom-author-select li[data-value="'+this.dataset.value+'"]').hide();
                }
            });
            if (li.length == authors.length) {
                $g('.select-post-author').hide();
            } else {
                $g('.select-post-author').css('display', '');
            }
            author = authors.join(',');
            $g('.ba-custom-author-select input[type="hidden"]').val(author);
        });
        parent.trigger('show');
        setTimeout(function(){
            $g('body').one('click', function(){
                $g('.visible-select').parent().trigger('customHide');
                $g('.visible-select').removeClass('visible-select');
            });
        }, 50);
    }
});

$g(document).on('click', '.remove-selected-author', function(){
    var authors = new Array(),
        parent = $g(this).closest('.ba-custom-author-select-wrapper'),
        li = parent.find('li[data-value]'),
        author = '';
    $g('.selected-author[data-id="'+this.parentNode.dataset.id+'"]').remove();
    parent.find('.selected-author').each(function(){
        authors.push(this.dataset.id);
    });
    li.each(function(){
        if (authors.indexOf(this.dataset.value) == -1) {
            $g('.ba-custom-author-select li[data-value="'+this.dataset.value+'"]').css('display', '');
        } else {
            $g('.ba-custom-author-select li[data-value="'+this.dataset.value+'"]').hide();
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

$g('.page-class-suffix').on('input', function(){
    app.editor.$g('body').removeClass(this.dataset.value).addClass(this.value).addClass('gridbox com_gridbox');
    $g('.page-class-suffix').not(this).val(this.value).attr('data-value', this.value);
    this.dataset.value = this.value;
});

$g('.reset-intro-image, .intro-image-delete-icon-wrapper i').on('click', function(){
    $g('input.intro-image').val('').attr('data-value', '').prev().css('background-image', '');
    $g('.blog-post-editor-img-thumbnail').css({
        'background-image': ''
    }).addClass('empty-intro-image');
});

$g('.share-image-wrapper input[type="text"]:not(.intro-image)').on('mousedown', function(event){
    event.stopPropagation();
    fontBtn = this;
    uploadMode = 'shareImage';
    checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
});

$g('.reset-share-image').on('click', function(){
    $g(this).parent().find('input[type="text"]').val('').trigger('input');
    $g(this).parent().find('.image-field-tooltip').css('background-image', '');
});

$g('.page-settings-input-trigger').on('input', function(){
    let $this = this;
    if (this.name == 'page_title' && this.value.trim()) {
        $g('.page-title.page-settings-input-trigger').removeClass('ba-alert-input');
        $g('#settings-dialog i.zmdi-check').removeClass('disabled-button');
        $g('#settings-dialog .ba-alert-container').hide();
    } else if (this.name == 'page_title') {
        $g('#settings-dialog i.zmdi-check').addClass('disabled-button');
        $g('#settings-dialog .ba-alert-container').show();
    } else if (this.name == 'published') {
        $g('.page-settings-custom-select-trigger input[name="published"]').val(Number(this.checked)).closest('.ba-custom-select').each(function(){
            this.querySelector('input[type="text"]').value = app._($this.checked ? 'PUBLISHED' : 'UNPUBLISHED')
        });
    }
    $g('.page-settings-input-trigger[name="'+this.name+'"]').not(this).val(this.value);
    $g('.page-settings-input-trigger[name="'+this.name+'"]').parent().find('.image-field-tooltip').css({
        'background-image': this.value ? 'url('+JUri+this.value+')' : ''
    });
});

$g('.blog-post-editor-options-group[data-field-type="range"] input[type="range"]').each(function(){
    setLinearWidth($g(this));
})

function checkRequiredFields($this)
{
    var type = $this.dataset.fieldType,
        field = {};
    switch (type) {
        case 'text':
        case 'file':
        case 'date':
        case 'event-date':
        case 'number':
        case 'range':
        case 'price':
            var input = $g($this).find('input[name]')[0];
            field.value = input.value.trim();
            break;
        case 'textarea':
            var input = $g($this).find('textarea[name="'+$this.dataset.id+'"]')[0];
            if (input.dataset.texteditor && !input.dataset.jce) {
                field.value = app.fieldsCKE[input.name].getData();
            } else if (input.dataset.texteditor && input.dataset.jce) {
                field.value = WFEditor.getContent('editor'+input.dataset.jce);
            } else {
                field.value = input.value.trim();
            }
            break;
        case 'select':
            var input = $g($this).find('select[name]')[0];
            field.value = input.value;
            break;
        case 'radio':
            $g($this).find('input[type="radio"][name]').each(function(){
                if (this.checked) {
                    field.value = this.value;
                }
            });
            break;
        case 'checkbox':
            $g($this).find('input[type="checkbox"][name]').each(function(){
                if (this.checked) {
                    if (!field.value) {
                        field.value = new Array();
                    }
                    field.value.push(this.value);
                }
            });
            break;
        case 'url':
        case 'field-button':
            $g($this).find('input[type="text"][name]').each(function(){
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
            $g($this).find('input[type="text"][name]').each(function(){
                if (!field.value) {
                    field.value = {};
                }
                field.value[this.dataset.name] = this.value;
            });
            break;
        case 'field-video':
            field.field_id = $this.dataset.id;
            var value = {};
            $g($this).find('[name][data-name]').each(function(){
                value[this.dataset.name] = this.value;
            });
            if (value.id || value.file) {
                field.value = JSON.stringify(value);
            } else {
                field.value = '';
            }
            break;
        case 'time':
            field.field_id = $this.dataset.id;
            var value = {};
            $g($this).find('select[data-name]').each(function(){
                value[this.dataset.name] = this.value;
            });
            if ($this.hasAttribute('data-required') && (value.hours == '' || value.minuts == '')) {
                field.value = '';
            } else {
                field.value = JSON.stringify(value);
            }
            break;
    }
    if ((type == 'url' && field.value.link && field.value.label) || (type == 'field-button' && field.value.link)
        || (field.type == 'image-field' && !field.value.src) || (type != 'url' && field.value)) {
        $this.classList.remove('ba-alert-label');
    }
}

function updateOptionsImageCount(key)
{
    $g('.selected-items[data-key="'+key+'"] .ba-item-thumbnail').each(function(){
        this.dataset.imageCount = app.productImages[key].length;
        this.style.backgroundImage = app.productImages[key].length ? 'url('+JUri+app.productImages[key][0]+')' : '';
    });
}

function desktopSlideshowFieldCallback(obj)
{
    app.addFieldSortingItem.dataset.img = obj.id;
    app.addFieldSortingItem.dataset.path = obj.path;
    app.addFieldSortingItem.querySelector('img').src = JUri+obj.path;
    app.addFieldSortingItem.querySelector('.sorting-title').textContent = obj.name;
}

function prepareProductVariations()
{
    let rows = [];
    app.subVariations = {};
    $g('.product-options .sorting-item').each(function(){
        let items = this.querySelectorAll('.selected-items');
        if (items.length) {
            let obj = {
                items: items,
                i: 0
            }
            rows.push(obj);
        }
    });
    if (rows.length) {
        getProductVariations(rows, true);
    } else {
        $g('.variations-table-body').empty();
    }
    $g('.blog-post-editor-options-group[data-field-type="product-variations"]').css('display', rows.length ? '' : 'none');
    app.productVariations = app.subVariations;
}

function getProductVariation(key)
{
    let keys = key.split('+'),
        result = null;
    for (let ind in app.productVariations) {
        let inds = ind.split('+'),
            exist = true;
        keys.forEach(function($this){
            if (inds.indexOf($this) == -1) {
                exist = false;
            }
        });
        if (exist) {
            result = ind;
            break;
        }
    }

    return result;
}

function getProductVariations(rows, newFlag)
{
    let html = key = title = '',
        price = document.querySelector('[data-field-key="price"] input'),
        decimals = price.dataset.decimals
        keys = [];
    rows.forEach(function(obj, ind){
        title += '<span>'+obj.items[obj.i].querySelector('.selected-items-name').textContent+'</span>';
        keys.push(obj.items[obj.i].dataset.key);
    });
    key = keys.join('+');
    let exist = getProductVariation(key);
    if (exist) {
        app.subVariations[key] = app.productVariations[exist];
        html = document.querySelector('.variations-table-row[data-key="'+exist+'"]');
        html.dataset.key = key;
        html.querySelector('.variations-table-cell.variation-cell').innerHTML = title;
    } else {
        app.subVariations[key] = {
            price: price.value,
            sale_price: document.querySelector('[data-field-key="sale_price"] input').value,
            weight: document.querySelector('[data-field-key="dimensions"] input').value,
            sku: '',
            stock: '',
            default: newFlag ? true : false
        }
        html += '<div class="variations-table-row" data-key="'+key+'" data-id="0">';
        html += '<div class="variations-table-cell variation-cell">'+title+'</div>';
        html += '<div class="variations-table-cell price-cell" data-field-type="price">';
        html += '<input type="text" data-key="price" data-decimals="'+decimals+'" value="';
        html += app.subVariations[key].price+'"></div>';
        html += '<div class="variations-table-cell sale-price-cell" data-field-type="price">';
        html += '<input type="text" data-key="sale_price" data-decimals="'+decimals+'" value="';
        html += app.subVariations[key].sale_price+'"></div>';
        html += '<div class="variations-table-cell sku-cell"><input type="text" data-key="sku"></div>';
        html += '<div class="variations-table-cell stock-cell" data-field-type="price">';
        html += '<input type="text" data-key="stock"></div>';
        html += '<div class="variations-table-cell weight-cell" data-field-type="price">';
        html += '<input type="text" data-key="weight" data-decimals="2" value="';
        html += app.subVariations[key].weight+'"></div>';
        html += '<div class="variations-table-cell default-cell" data-default="'+Number(app.subVariations[key].default);
        html += '"><i class="zmdi zmdi-star"></i></div>';
        html += '</div>';
    }
    $g('.variations-table-body').append(html);
    incrementVariationsRowIndex(rows, rows.length - 1);
}

function incrementVariationsRowIndex(rows, ind)
{
    if (rows[ind].i < rows[ind].items.length - 1) {
        rows[ind].i++;
        getProductVariations(rows)
    } else if (rows[ind].i == rows[ind].items.length - 1 && rows[ind - 1]) {
        rows[ind].i = 0;
        incrementVariationsRowIndex(rows, ind - 1)
    }
}

function prepareVariationsPhotosDialog(key)
{
    let modal = document.querySelector('#product-variations-photos-dialog'),
        str = '';
    modal.querySelectorAll('.ba-options-group-toolbar label[data-action="delete"]').forEach(function($this){
        $this.classList.add('disabled');
    });
    app.productImages[key].forEach(function(image){
        let name = image.split('/'),
            src = image;
        if (!app.isExternal(image)) {
            src = JUri+image;
        }
        str += '<div class="sorting-item"><div class="sorting-icon"><i class="zmdi zmdi-more-vert sortable-handle"></i>'+
            '</div><div class="sorting-checkbox"><label class="ba-checkbox ba-hide-checkbox"><input type="checkbox">'+
            '<span></span></label></div><div class="sorting-image"><span class="ba-item-thumbnail" data-image="'+image+
            '" style="background-image: url('+src+');"></span></div><div class="sorting-title">'+
            name[name.length - 1]+'</div></div>';
    });
    modal.querySelector('.sorting-container').innerHTML = str;
}

function reorderVariationsPhotos()
{
    app.productImages[fontBtn] = [];
    $g('#product-variations-photos-dialog .sorting-item .ba-item-thumbnail').each(function(){
        app.productImages[fontBtn].push(this.dataset.image);
    });
    updateOptionsImageCount(fontBtn);
}

$g('.blog-post-editor-options-group[data-field-type][data-required]').on('input customAction', function(){
    checkRequiredFields(this);
});

$g('.page-settings-custom-select-trigger').on('customAction', function(){
    let input = this.querySelector('input[type="hidden"]'),
        text = this.querySelector('input[type="text"]').value;
    $g('.page-settings-custom-select-trigger input[type="hidden"][name="'+input.name+'"]').val(input.value).prev().val(text);
    if (input.name == 'published') {
        $g('.page-settings-input-trigger[name="published"]').prop('checked', Boolean(input.value * 1));
    }
});

$g('.blog-post-editor-img-thumbnail .camera-container').on('mousedown', function(){
    setTimeout(function(){
        $g('.share-image-wrapper .intro-image').trigger('mousedown');
    }, 100);
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper .add-new-item').on('click', function(){
    fontBtn = this.closest('.field-sorting-wrapper');
    app.addFieldSortingWrapper = $g(this).closest('.field-sorting-wrapper').find('.sorting-container');
    if (fontBtn.classList.contains('product-options')) {
        fontBtn = this.querySelector('i');
        let search = '.sorting-item[data-type="color"], .sorting-item[data-type="image"]',
            flag = document.querySelector('.product-options').querySelector(search);
        document.querySelectorAll('#product-options-dialog li').forEach(function(li){
            let selected = document.querySelector('.product-options .sorting-item[data-id="'+li.dataset.value+'"]');
            if ((li.dataset.type == 'color' || li.dataset.type == 'image') && flag) {
                selected = true;
            } else if (li.dataset.type == 'checkbox' || li.dataset.type == 'file' || li.dataset.type == 'textarea'  || li.dataset.type == 'textinput') {
                selected = true;
            }
            li.classList[selected ? 'add' : 'remove']('selected');
        });
        showDataTagsDialog('product-options-dialog');
    } else if (fontBtn.classList.contains('product-extra-options')) {
        fontBtn = this.querySelector('i');
        document.querySelectorAll('#product-options-dialog li').forEach(function(li){
            let selected = document.querySelector('.product-extra-options .sorting-item[data-id="'+li.dataset.value+'"]');
            li.classList[selected ? 'add' : 'remove']('selected');
        });
        showDataTagsDialog('product-options-dialog');
    }
});

$g('#blog-post-editor-fields-options .sorting-container').on('click', '.unpublish-sorting-item', function(){
    let item = this.closest('.sorting-item');
    item.dataset.unpublish = item.dataset.unpublish == '1' ? '0' : '1';
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-toolbar-action[data-action="add"]')
    .on('click', function(){
    fontBtn = this.closest('.field-sorting-wrapper');
    app.addFieldSortingWrapper = $g(this).closest('.field-sorting-wrapper').find('.sorting-container');
    if (fontBtn.dataset.source == 'desktop') {
        createDesktopFileInput(fontBtn, fontBtn.dataset.size, 'gif, jpg, jpeg, png, svg, webp', desktopSortingFieldCallback, true);
    } else {
        uploadMode = 'addFieldSortingItem';
        checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
    }
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-toolbar-action[data-action="check"]')
    .on('click', function(){
    let parent = this.closest('.field-sorting-wrapper'),
        checkbox = parent.querySelectorAll('input[type="checkbox"]'),
        checked = this.dataset.checked == 'true',
        flag = !checked && checkbox.length != 0,
        btns = {};
    checkbox.forEach(function($this){
        $this.checked = !checked;
    });
    parent.querySelectorAll('.sorting-toolbar-action').forEach(function(btn){
        btns[btn.dataset.action] = btn;
    });
    if (btns.copy) {
        btns.copy.classList[flag ? 'remove' : 'add']('disabled');
    }
    if (btns.delete) {
        btns.delete.classList[flag ? 'remove' : 'add']('disabled');
    }
    this.dataset.checked = flag;
    this.classList[checkbox.length != 0 ? 'remove' : 'add']('disabled');
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper').on('change', 'input[type="checkbox"]', function(){
    let checked = [],
        parent = this.closest('.field-sorting-wrapper'),
        checkbox = parent.querySelectorAll('input[type="checkbox"]'),
        btns = {};
    parent.querySelectorAll('.sorting-toolbar-action').forEach(function(btn){
        btns[btn.dataset.action] = btn;
    });
    checkbox.forEach(function($this){
        $this.checked ? checked.push($this.closest('.sorting-item')) : '';
    });
    if (btns.copy) {
        btns.copy.classList[checked.length ? 'remove' : 'add']('disabled');
    }
    if (btns.delete) {
        btns.delete.classList[checked.length ? 'remove' : 'add']('disabled');
    }
    btns.check.dataset.checked = checked.length && checked.length == checkbox.length ? 'true' : 'false';
});

function createProductOptionItem(obj) {
    app.productImages[obj.key] = [];
    let html = '<span class="selected-items" data-key="'+obj.key+'" data-id="0">'+
        '<span class="ba-item-thumbnail" data-image-count="0"><i class="zmdi zmdi-camera"></i></span>'+
        '<span class="selected-items-name">'+obj.title+'</span>'+
        '<i class="zmdi zmdi-close remove-selected-items"></i><span class="grid-sorting-handle"></span></span>';

    return html;
}

$g('#blog-post-editor-fields-options .product-options .add-new-item i').on('change', function(){
    let div = document.createElement('div'),
        obj = app.productOptions[this.dataset.value],
        tags = '',
        wrapper = null,
        html = '<div class="product-options-title-wrapper">'+obj.title+'</div><div class="selected-items-wrapper"></div>';
    html += '<div class="product-options-icons-wrapper"><span class="add-new-product-options-value"><i class="zmdi zmdi-plus"></i></span>';
    html += '<span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span><span><i class="zmdi zmdi-delete"></i></span></div>';
    div.className = 'sorting-item';
    div.innerHTML = html;
    div.dataset.id = obj.id;
    div.dataset.type = obj.field_type;
    for (let ind in obj.options) {
        tags += createProductOptionItem(obj.options[ind]);
    }
    wrapper = div.querySelector('.selected-items-wrapper');
    wrapper.innerHTML = tags;
    setGridSorting(wrapper, 'product-options', prepareProductVariations);
    this.closest('.product-options').querySelector('.sorting-container').append(div);
    $g('.variations-table-body').empty();
    prepareProductVariations();
});

$g('#blog-post-editor-fields-options .product-extra-options .add-new-item i').on('change', function(){
    let div = document.createElement('div'),
        obj = app.productOptions[this.dataset.value],
        isFile = obj.field_type == 'file' || obj.field_type == 'textarea' || obj.field_type == 'textinput',
        html = '<div class="extra-product-options-table"><div class="extra-product-options-thead">'+
            '<div class="extra-product-options-row"><div class="extra-product-option-title">'+obj.title+'</div>'+
            '<div class="extra-product-option-price">'+(isFile ? '' : app._('PRICE'))+'</div>'+
            '<div class="extra-product-option-weight">'+(isFile ? '' : app._('WEIGHT'))+'</div>'+
            '<div class="extra-product-option-default">'+(isFile ? '' : app._('DEFAULT'))+'</div>';
    html += '<div class="extra-product-option-icons">'
    if (!isFile) {
        html += '<span class="add-new-extra-product-options"><i class="zmdi zmdi-plus"></i></span>'
    }
    html += '<span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span><span><i class="zmdi zmdi-delete"></i></span>'+
        '</div></div></div><div class="extra-product-options-tbody"></div></div>';
    div.className = 'sorting-item';
    div.innerHTML = html;
    div.dataset.id = obj.id;
    div.dataset.optionType = obj.field_type;
    html = '';
    if (!isFile) {
        for (let ind in obj.options) {
            html += '<div class="extra-product-options-row" data-key="'+obj.options[ind].key+'">'+
                '<div class="extra-product-option-title">'+obj.options[ind].title+'</div>'+
                '<div class="extra-product-option-price" data-field-type="price"><input type="text" data-decimals="10"></div>'+
                '<div class="extra-product-option-weight" data-field-type="price"><input type="text" data-decimals="2"></div>'+
                '<div class="extra-product-option-default"><i class="zmdi zmdi-star" data-default="0"></i></div>'+
                '<div class="extra-product-option-icons">'+
                    '<span class="delete-extra-product-option"><i class="zmdi zmdi-delete"></i></span>'+
                '</div></div>';
        }
    } else {
        let template = document.querySelector('.file-row-template').content.querySelector('.extra-product-options-row').cloneNode(true);
        html += template.outerHTML;
    }
    div.querySelector('.extra-product-options-tbody').innerHTML = html;
    this.closest('.product-extra-options').querySelector('.sorting-container').append(div);
});

function copyBlogPostEditorFields(items)
{
    let container = clone = null;
    items.forEach(function(item){
        container = item.closest('.field-sorting-wrapper');
        item.find('input[type="checkbox"]').prop('checked', false);
        clone = item.clone();
        item.after(clone);
    });
    if (container) {
        container.find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
    }
}

$g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-container').on('click', 'i.zmdi-copy', function(){
    let item = $g(this).closest('.sorting-item');
    copyBlogPostEditorFields([item])
});

$g('#blog-post-editor-fields-options .renewal-plans').on('click', '.delete-renewal-plan', function(){
    app.itemDelete = {
        type: 'delete-field-item',
        items: [this.closest('.renewal-plan')]
    };
    app.checkModule('deleteItem');
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-container').on('click', '.zmdi.zmdi-delete', function(){
    if (!this.closest('.delete-extra-product-option') && !this.closest('.delete-renewal-plan')) {
        app.itemDelete = {
            type: 'delete-field-item',
            items: [this.closest('.sorting-item')]
        };
        app.checkModule('deleteItem');
    }
});

$g('#blog-post-editor-fields-options .sorting-toolbar-action[data-action="copy"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.field-sorting-wrapper'),
        item = null,
        array = [];
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            item = $g(checkbox).closest('.sorting-item');
            array.push(item);
        }
    });
    copyBlogPostEditorFields(array);
});

$g('#blog-post-editor-fields-options .sorting-toolbar-action[data-action="delete"]').on('click', function(){
    if (this.classList.contains('disabled')) {
        return false;
    }
    let parent = this.closest('.field-sorting-wrapper'),
        item = null;
    app.itemDelete = {
        type: 'delete-field-item',
        items: []
    };
    parent.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox){
        if (checkbox.checked) {
            item = checkbox.closest('.sorting-item');
            app.itemDelete.items.push(item);
        }
    });
    app.checkModule('deleteItem');
});

$g('#blog-post-editor-fields-options .field-sorting-wrapper .sorting-container').on('click', '.zmdi.zmdi-edit', function(){
    app.addFieldSortingItem = this.closest('.sorting-item');
    if (this.closest('.blog-post-editor-options-group').dataset.fieldType == 'field-simple-gallery'
        || this.closest('.blog-post-editor-options-group').dataset.fieldType == 'product-gallery') {
        let img = app.addFieldSortingItem.querySelector('.sorting-title').textContent.trim(),
            alt = app.addFieldSortingItem.dataset.alt;
        $g('.field-sorting-upload-image').val(img).attr('data-image', app.addFieldSortingItem.dataset.img)
            .attr('data-path', app.addFieldSortingItem.dataset.path);
        $g('.field-simple-gallery-alt').val(alt);
        $g('#field-sorting-item-edit-modal').modal();
    } else {
        let wrapper = app.addFieldSortingItem.closest('.field-sorting-wrapper');
        if (wrapper.dataset.source == 'desktop') {
            fontBtn = this;
            createDesktopFileInput(fontBtn, wrapper.dataset.size, 'gif, jpg, jpeg, png, svg, webp', desktopSlideshowFieldCallback, false);
        } else {
            uploadMode = 'reselctSlideshowFieldSortingImg';
            checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader'); 
        }
    }
});

$g('body').on('click', '.field-sorting-wrapper.product-options .ba-item-thumbnail i', function(event){
    event.stopPropagation();
    fontBtn = this.closest('.selected-items').dataset.key;
    uploadMode = 'uploadVariationsPhotos';
    checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
}).on('click', '.field-sorting-wrapper.product-options .ba-item-thumbnail', function(event){
    let key = this.closest('.selected-items').dataset.key;
    prepareVariationsPhotosDialog(key);
    fontBtn = this;
    showDataTagsDialog('product-variations-photos-dialog');
    fontBtn = key;
}).on('input', '.variations-table-cell input', function(){
    let $this = this;
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        updateProductVariation($this);
    }, 300);
})

function updateProductVariation(input)
{
    let key = input.closest('.variations-table-row').dataset.key;
    app.productVariations[key][input.dataset.key] = input.value.trim();
}

$g('#product-variations-photos-dialog .sorting-container').on('change', 'input[type="checkbox"]', function(){
    let action = 'add';
    $g('#product-variations-photos-dialog input[type="checkbox"]').each(function(){
        if (this.checked) {
            action = 'remove';
            return false;
        }
    });
    document.querySelector('#product-variations-photos-dialog label[data-action="delete"]').classList[action]('disabled');
});

$g('#product-variations-photos-dialog label[data-action="add"]').on('click', function(){
    uploadMode = 'uploadVariationsPhotos';
    checkIframe($g('#uploader-modal').attr('data-check', 'multiple'), 'uploader');
});

$g('#product-variations-photos-dialog label[data-action="delete"]').on('click', function(){
    if (!this.classList.contains('disabled')) {
        app.itemDelete = 'deleteVariationsPhotos';
        app.loadModule('deleteItem');
    }
});

$g('#field-sorting-item-edit-modal .field-sorting-upload-image').on('click', function(){
    fontBtn = this;
    let wrapper = app.addFieldSortingItem.closest('.field-sorting-wrapper');
    if (wrapper.dataset.source == 'desktop') {
        createDesktopFileInput(fontBtn, wrapper.dataset.size, 'gif, jpg, jpeg, png, svg, webp', desktopGalleryFieldCallback, false);
    } else {
        uploadMode = 'reselctFieldSortingImg';
        checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
    }
});

$g('#apply-field-sorting-item').on('click', function(){
    let input = document.querySelector('#field-sorting-item-edit-modal .field-sorting-upload-image'),
        alt = $g('.field-simple-gallery-alt').val().trim(),
        str = addFieldSortingListHTML(input.dataset.image, alt, input.dataset.path, input.value);
    $g(app.addFieldSortingItem).replaceWith(str);
    $g('#field-sorting-item-edit-modal').modal('hide');
});

function addFieldSortingList(img, alt)
{
    var src = img,
        array = src.split('/');
    let str = addFieldSortingListHTML(img, alt, src, array[array.length - 1]);

    return str;
}

function addFieldSortingListHTML(img, alt, path, name)
{
    let src = path;
    if (!app.isExternal(src)) {
        src = JUri+src;
    }
    var str = '<div class="sorting-item" data-img="'+img+'" data-path="'+path+'" data-alt="'+alt+'">';
    str += '<div class="sorting-checkbox"><label><input type="checkbox"><span></span></label></div>';
    str += '<div class="sorting-image sorting-handle">';
    str += '<img src="'+src+'">';
    str += '</div><div class="sorting-title sorting-handle">'+name;
    str += '</div><div class="sorting-icons">';
    str += '<span><i class="zmdi zmdi-edit edit-sorting-item"></i></span>';
    str += '<span><i class="zmdi zmdi-copy copy-sorting-item"></i></span>';
    str += '<span><i class="zmdi zmdi-eye-off unpublish-sorting-item"></i></span>';
    str += '<span><i class="zmdi zmdi-delete delete-sorting-item"></i></span></div></div>';

    return str;
}

function setFieldsCKE($this)
{
    if (!app.fieldsCKE) {
        app.fieldsCKE = {};
    }
    app.fieldsCKE[$this.name] = CKEDITOR.replace($this);
    app.fieldsCKE[$this.name].on('change', function(){
        $g(this.element.$).trigger('input');
    });
}

function setFieldsJCE($this)
{
    WFEditor.setContent('editor'+$this.dataset.jce, $this.value);
    if (WFEditor['getEditor' in WFEditor ? 'getEditor' : '_getEditor']('editor'+$this.dataset.jce).onKeyUp) {
        WFEditor['getEditor' in WFEditor ? 'getEditor' : '_getEditor']('editor'+$this.dataset.jce).onKeyUp.add(function(){
            checkRequiredFields($this.closest('.blog-post-editor-options-group'));
        });
    }
    if (WFEditor['getEditor' in WFEditor ? 'getEditor' : '_getEditor']('editor'+$this.dataset.jce).onChange) {
        WFEditor['getEditor' in WFEditor ? 'getEditor' : '_getEditor']('editor'+$this.dataset.jce).onChange.add(function(){
            checkRequiredFields($this.closest('.blog-post-editor-options-group'));
        });
    }
}

$g('#blog-post-editor-fields-options textarea[data-texteditor]').each(function(){
    if (!this.dataset.jce) {
        setFieldsCKE(this);
    } else {
        setFieldsJCE(this);
    }
});

$g('.trigger-attachment-file-field').on('click', function(){
    fontBtn = this;
    if (this.dataset.source == 'desktop') {
        createDesktopFileInput(this, this.dataset.size, this.dataset.types, desktopVideoFieldCallback);
    } else {
        uploadMode = 'attachmentFileField';
        checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
    }
}).closest('div').find('.reset-attachment-file-field').on('click', function(){
    $g(this).closest('.blog-post-editor-group-element').find('.trigger-attachment-file-field, .select-input').val('').attr('data-value', '');
});

$g('.blog-post-editor-options-group .reset').on('click', function(){
    $g(this).closest('.blog-post-editor-group-element').find('.select-input').val('').attr('data-value', '');
});

$g('.reset-date-field').on('click', function(){
    this.closest('.blog-post-editor-group-element').querySelector('input').value = '';
});

function uploadDesktopFieldFile(files)
{
    let xhr = new XMLHttpRequest(),
        file = files.shift(),
        formData = new FormData();
    formData.append('file', file);
    formData.append('id', app.editor.themeData.id);
    xhr.onload = xhr.onerror = function(){
        let obj = JSON.parse(xhr.responseText);
        if (!obj.error) {
            fontBtn.desktopFieldCallback(obj);
        } else if (obj.error && !files.length) {
            app.showNotice(obj.msg, 'ba-alert');
        }
        if (files.length) {
            uploadDesktopFieldFile(files)
        } else if (!obj.error) {
            setTimeout(function(){
                app.notification.addClass('animation-out').removeClass('notification-in');
            }, 2000);
        }
    };
    xhr.open("POST", JUri+"index.php?option=com_gridbox&task=editor.uploadDesktopFieldFile", true);
    xhr.send(formData);
}

function uploadDesktopField()
{
    let files = [].slice.call(this.files),
        flag = true;
    for (let i = 0; i < files.length; i++) {
        var size = this.dataset.size * 1000,
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
            app.showNotice(gridboxLanguage[msg], 'ba-alert');
            break
        }
    }
    if (flag) {
        var str = '<span>'+gridboxLanguage['UPLOADING_MEDIA'];
        str += '</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
        app.notification.find('p').html(str);
        app.notification.removeClass('animation-out').addClass('notification-in');
        uploadDesktopFieldFile(files);
    }
}

function desktopVideoFieldCallback(obj)
{
    $g(this).attr('data-value', obj.id).val(obj.name);
}

function desktopImageFieldCallback(obj)
{
    $g(this).attr('data-value', obj.id).attr('data-src', obj.path).val(obj.name).trigger('input');
}

function desktopSortingFieldCallback(obj)
{
    let str = addFieldSortingListHTML(obj.id, '', obj.path, obj.name);
    app.addFieldSortingWrapper.append(str);
    app.addFieldSortingWrapper.closest('.blog-post-editor-options-group').removeClass('ba-alert-label');
    app.addFieldSortingWrapper.closest('.field-sorting-wrapper')
        .find('.sorting-toolbar-action[data-action="check"]').attr('data-checked', true).trigger('click');
}

function desktopGalleryFieldCallback(obj)
{
    fontBtn.dataset.image = obj.id;
    fontBtn.dataset.path = obj.path;
    fontBtn.value = obj.name;
}

function createDesktopFileInput($this, size, types, callback, multiple)
{
    if (!$this.desktopFieldCallback) {
        $this.desktopFieldCallback = callback;
    }
    let input = document.createElement('input');
    input.type = 'file';
    input.onchange = uploadDesktopField;
    if (multiple) {
        input.setAttribute('multiple', 'multiple');
    }
    input.dataset.size = size;
    input.dataset.types = types;
    input.style.display = 'none';
    document.body.append(input);
    setTimeout(function(){
        input.click();
    }, 100);
}

function uploadDigitalFile()
{
    let str = '<span>'+gridboxLanguage['UPLOADING_MEDIA'],
        xhr = new XMLHttpRequest(),
        file = this.files[0],
        formData = new FormData();
    str += '</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
    app.notification.find('p').html(str);
    app.notification.removeClass('animation-out').addClass('notification-in');
    formData.append('file', file);
    formData.append('id', app.editor.themeData.id);
    xhr.onload = xhr.onerror = function(){
        let obj = JSON.parse(xhr.responseText);
        if (obj.error) {
            app.showNotice(obj.msg, 'ba-alert');
        } else {
            fontBtn.closest('.blog-post-editor-options-group').classList.remove('ba-alert-label')
            fontBtn.value = obj.name;
            fontBtn.dataset.value = obj.filename;
            fontBtn.setAttribute('readonly', '');
            fontBtn.onfocus = function(){
                this.blur();
            };
            fontBtn.dataset.type = 'upload';
        }
        setTimeout(function(){
            app.notification.addClass('animation-out').removeClass('notification-in');
        }, 2000);
    };
    xhr.open("POST", JUri+"index.php?option=com_gridbox&task=store.uploadDigitalFile");
    xhr.send(formData);
}

function createDigitalFileInput()
{
    let input = document.createElement('input');
    input.type = 'file';
    input.onchange = uploadDigitalFile;
    input.style.display = 'none';
    document.body.append(input);
    setTimeout(function(){
        input.click();
    }, 100);
}

$g('.select-image-field').on('click', function(){
    fontBtn = this;
    if (this.dataset.source == 'desktop') {
        createDesktopFileInput(this, this.dataset.size, 'gif, jpg, jpeg, png, svg, webp', desktopImageFieldCallback);
    } else {
        uploadMode = 'imageField';
        checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
    }
}).on('input', function(){
    var parent = $g(this).closest('.blog-post-editor-group-element'),
        img = this.dataset.source == 'desktop' ? this.dataset.src : this.dataset.value;
    parent.find('.image-field-tooltip').css('background-image', 'url('+JUri+img+')');
}).closest('div').find('.reset-image-field').on('click', function(){
    var parent = $g(this).closest('.blog-post-editor-group-element');
    parent.find('.select-image-field').val('').attr('data-value', '');
    parent.find('.image-field-tooltip').css('background-image', '');
});

$g('.select-field-video-type').on('change', function(){
    var type = this.value,
        parent = $g(this).closest('.field-sorting-wrapper');
    parent.find('input[data-name="file"], input[data-name="id"]').val('');
    parent.find('.field-video-id')[type == 'source' ? 'hide' : 'show']();
    parent.find('.field-video-file')[type != 'source' ? 'hide' : 'show']();
});

$g('.field-sorting-wrapper .field-video-file input[data-name="file"]').on('click', function(){
    fontBtn = this;
    if (this.dataset.source == 'desktop') {
        createDesktopFileInput(this, this.dataset.size, 'mp4', desktopVideoFieldCallback);
    } else {
        uploadMode = 'videoSource';
        checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
    }
}).on('change', function(){
    $g(this).trigger('input');
});

$g('i.add-fields-group').on('click', function(){
    $g('#apps-fields-group-dialog').modal().find('.apps-fields-group-title').val('');
});

$g('#apps-fields-group-dialog .apps-fields-group-title').on('input', function(){
    if (this.value.trim()) {
        $g('#apply-apps-fields-group').removeClass('disable-button').addClass('active-button');
    } else {
        $g('#apply-apps-fields-group').addClass('disable-button').removeClass('active-button');
    }
});

$g('#apply-apps-fields-group').on('click', function(event){
    event.preventDefault();
    if (this.classList.contains('active-button')) {
        $g('#apps-fields-group-dialog').modal('hide');
        let wrapper = document.createElement('div'),
            titleWrapper = document.createElement('div'),
            iconsWrapper = document.createElement('div'),
            title = document.createElement('input'),
            div = document.createElement('div');
        titleWrapper.className = 'ba-fields-group-title';
        iconsWrapper.className = 'ba-fields-group-icons';
        iconsWrapper.innerHTML = '<i class="zmdi zmdi-delete"></i><i class="zmdi zmdi-apps"></i>';
        title.value = $g('#apps-fields-group-dialog .apps-fields-group-title').val().trim();
        title.type = 'text';
        title.placeholder = gridboxLanguage['NEW_GROUP'];
        wrapper.className = 'ba-fields-group-wrapper';
        wrapper.id = 'ba-group-'+(+new Date());
        div.className = 'ba-fields-group';
        titleWrapper.appendChild(title);
        titleWrapper.appendChild(iconsWrapper);
        wrapper.appendChild(titleWrapper);
        wrapper.appendChild(div);
        $g('#blog-post-editor-fields-options .ba-app-fields-groups-wrapper').append(wrapper);
        app.setFieldsSortable(div);
    }
});

$g('.apps-fields-editor-toggle').on('change', function(){
    if (this.checked) {
        document.body.classList.add('enable-field-editor');
    } else {
        document.body.classList.remove('enable-field-editor');
    }
    localStorage.setItem('enable-field-editor', this.checked);
});

$g('#blog-post-editor-fields-options').on('click', '.ba-fields-group-icons i.zmdi-delete', function(){
    var wrapper = this.closest('.ba-fields-group-wrapper');
    if (wrapper.querySelector('.blog-post-editor-options-group')) {
        app.showNotice(gridboxLanguage['GROUP_DELETE_NOTICE'], 'ba-alert');
    } else {
        app.itemDelete = {
            type: 'delete-field-item',
            item: wrapper
        };
        app.checkModule('deleteItem');
    }
});

if (document.querySelector('.field-google-map-wrapper')) {
    app.mapScript = document.createElement('script');
    app.mapScript.onload = function(){
        app.fieldMaps = {};
        var options = {
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
            var string = this.previousElementSibling.textContent,
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
                var place = autocomplete.getPlace();
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
                var obj = {
                    position: event.latLng,
                    map: this
                }
                app.fieldMaps[this.name].marker = new google.maps.Marker(obj);
            });
        });
    }
    app.mapScript.src = 'https://maps.googleapis.com/maps/api/js?libraries=places&key='+integrations.google_maps.key;
    document.head.appendChild(app.mapScript);
}

$g('body').on('input', '[data-field-type="price"] input, [data-field-key="stock"] input', function(){
    let decimals = this.dataset.decimals ? this.dataset.decimals * 1 : 0,
        max = decimals > 0 ? 1 : 0,
        match = this.value.match(new RegExp('\\d+\\.{0,'+max+'}\\d{0,'+decimals+'}'));
    if (!match) {
        this.value = '';
    } else if (match[0] != this.value) {
        this.value = match[0];
    }
}).on('focus', '.trigger-variations-table input', function(){
    this.lastValue = this.value;
    this.fieldKey = this.closest('.blog-post-editor-options-group').dataset.fieldKey.replace('dimensions', 'weight');
}).on('input', '.trigger-variations-table input', function(){
    let $this = this,
        key = this.fieldKey;
    clearTimeout(this.delay);
    this.delay = setTimeout(function(){
        document.querySelectorAll('.variations-table-cell input[data-key="'+key+'"]').forEach(function(input){
            if ($this.lastValue == input.value) {
                input.value = $this.value;
                updateProductVariation(input);
            }
        });
        app.badges.calculateSale();
        $this.lastValue = $this.value;
    }, 300);
});

function setGridSorting(wrapper, group, change)
{
    let options = {
        group: group
    }
    if (group == 'product-options') {
        options.group += '-'+wrapper.closest('.sorting-item').dataset.type
    }
    if (change) {
        options.change = change;
    }
    $g(wrapper).gridSorting(options);
}

$g('#ba-group-product-variations .field-sorting-wrapper.product-options .selected-items-wrapper').each(function(){
    setGridSorting(this, 'product-options', prepareProductVariations);
});

$g('#ba-group-product-pricing .field-sorting-wrapper.product-badges .selected-items-wrapper').each(function(){
    setGridSorting(this, 'product-badges');
});

$g('#ba-group-related-product .field-sorting-wrapper.related-product .selected-items-wrapper').each(function(){
    setGridSorting(this, 'related-product');
});

$g('.field-sorting-wrapper.subscription-products .selected-items-wrapper').each(function(){
    setGridSorting(this, 'subscription-products');
});

$g('.field-sorting-wrapper.upgrade-plans .selected-items-wrapper').each(function(){
    setGridSorting(this, 'upgrade-plans');
});

$g('.field-sorting-wrapper.subscription-groups .selected-items-wrapper').each(function(){
    setGridSorting(this, 'subscription-groups');
});

$g('.trigger-upload-digital-file').on('click', function(){
    fontBtn = this.closest('.blog-post-editor-group-element').querySelector('input');
    createDigitalFileInput();
}).closest('div').find('.reset-digital-file').on('click', function(){
    let input = $g(this).closest('.blog-post-editor-group-element').find('input')
    input.val('').attr('data-value', '').removeAttr('readonly').attr('data-type', 'link');
    input[0].onfocus = null;
});

if (!app.editor.themeData.edit_type || app.editor.themeData.edit_type == 'system') {
    $g('#settings-dialog .language-select input[type="hidden"]').each(function(){
        app.associations.prepare(this.value)
    });
    $g('.ba-custom-select.language-select').on('customAction', function(){
        let lang = this.querySelector('input[type="hidden"]').value;
        app.associations.setLanguage(lang);
        app.associations.set(lang);
    });
    $g('.association-wrapper').on('click', '.association-page', function(){
        let lang = this.dataset.lang,
            link = 'associations&associate='+lang+'&type='+app.associations.type;
        if (app.associations.type == 'system') {
            link += '&system='+app.editor.systemType;
        }
        uploadMode = 'association';
        fontBtn = this;
        checkIframe($g('#association-pages-list-modal'), link, null, true);
    }).on('click', '.reset-association', function(){
        let $this = this.closest('.association-wrapper').querySelector('.association-page');
        $this.removeAttribute('data-id');
        $this.value = '';
        $g('.association-page[data-lang="'+$this.dataset.lang+'"]').not($this).val('').removeAttr('data-id');
    }).on('input', '.association-page', function(){
        $g('.association-page[data-lang="'+this.dataset.lang+'"]').not(this)
            .val(this.value).attr('data-id', this.dataset.id);
    });
}

$g('.select-data-tags').on('click', function(){
    fontBtn = this;
    if (this.dataset.template) {
        let template = document.querySelector('template.'+this.dataset.template).content.cloneNode(true);
        document.querySelectorAll('#data-tags-dialog .modal-body').forEach((div) => {
            div.innerHTML = '';
            div.append(template);
        })
    }
    showDataTagsDialog('data-tags-dialog');
});
$g('#data-tags-dialog .modal-body').on('change', '.select-data-tags-type', function(){
    let modal = $g('#data-tags-dialog');
    modal.find('div.ba-settings-group[class*="-data-tags"]').hide();
    modal.find('div.ba-settings-group'+(this.value ? '.'+this.value+'-data-tags' : '')).css('display', '');
});
$g('#data-tags-dialog .modal-body').on('click', '.ba-settings-input-type', function(){
    let value = this.querySelector('input[type="text"]').value,
        input = fontBtn.closest('.ba-options-group-element, .ba-group-element').querySelector('input[type="text"], textarea');
    input.setRangeText(value);
    let start = input.selectionStart+value.length;
    input.setSelectionRange(start, start);
    input.focus();
    $g(input).trigger('input');
    $g('#data-tags-dialog').modal('hide');
});

$g('.meta-tags .picked-tags .search-tag input').on('keyup', function(event){
    let title = this.value.trim();
    if (event.keyCode == 13) {
        if (!title) {
            this.value = '';
            return false;
        }
        let str = '<li class="tags-chosen"><span>',
            tagId = 'new$'+title;
        $g('#post-tags-dialog .ba-settings-item').each(function(){
            if (title == this.textContent.trim()) {
                tagId = this.dataset.id;
                return false;
            }
        });
        if (tagId != 'new$'+title || document.querySelector('.picked-tags .tags-chosen i[data-remove="'+tagId+'"]')) {
            return false;
        }
        str += title+'</span><i class="zmdi zmdi-close" data-remove="'+tagId+'"></i></li>';
        $g('.picked-tags .search-tag').before(str);
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
    $g('#post-tags-dialog .ba-settings-item[data-id]').each(function(){
        this.classList[document.querySelector('.meta-tags option[value="'+this.dataset.id+'"]') ? 'add' : 'remove']('selected');
    });
    showDataTagsDialog('post-tags-dialog');
}).on('change', function(){
    let id = this.postTag.id,
        title = this.postTag.title,
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
$g('#post-tags-dialog .modal-body').on('change', '.select-data-tags-type', function(){
    let modal = $g('#post-tags-dialog');
    modal.find('.ba-settings-item[data-id]').hide();
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
    fontBtn.postTag = {
        id: this.dataset.id,
        title: this.textContent.trim()
    }
    $g(fontBtn).trigger('change');
});
$g('.meta-tags .picked-tags').on('click', '.zmdi.zmdi-close', function(){
    let id = this.dataset.remove;
    $g('select.meta_tags option[value="'+id+'"]').remove();
    $g('.tags-chosen i[data-remove="'+id+'"]').parent().remove();
});

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
            ul.classList[this.closest('#blog-post-editor-general-options') ? 'add' : 'remove']('right-panel-categories');
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
        categories.forEach((category_id) => {
            array.push(app.multicategory.setActive(category_id, false));
        });

        return array.join(', ');
    }
}

if (!app.editor.themeData.edit_type) {
    if (!app.modules.calendar) {
        app.loadModule('calendar');
    }

    app.multicategory.start();
    $g('#settings-dialog .page-multicategory-select').each(function(){
        let category = this.querySelector('input[name="page_category"]').value,
            categories = this.querySelector('input[name="page_categories"]').value.split(', ');
        app.multicategory.set(category, categories);
    })
    

    $g('.seo-default-settings').on('click', function(){
        let data = {
                type: 'page',
                id: this.dataset.id
            }
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.getDefaultsSeo', data).then((text) => {
            let modal = $g('#seo-default-settings-modal'),
                json = JSON.parse(text);
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
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.setDefaultsSeo', data);
        modal.modal('hide');
    });

    $g('.share-image-wrapper .intro-image').on('mousedown', function(event){
        event.preventDefault();
        uploadMode = 'introImage';
        checkIframe($g('#uploader-modal').attr('data-check', 'single'), 'uploader');
    });

    $g('#ba-group-product-booking').on('change', 'div[data-field-key="booking_type"]', function(){
        let wrapper = this.closest('#ba-group-product-booking'),
            type = this.querySelector('select').value,
            single = time = availability =null;
        wrapper.querySelectorAll('.blog-post-editor-options-group:not([data-field-key="booking_type"])').forEach((div) => {
            if (div.dataset.fieldKey == 'min' || div.dataset.fieldKey == 'max') {
                div.classList[type == 'multiple' ? 'remove' : 'add']('ba-hide-element');
            } else if (div.dataset.fieldKey == 'participants') {
                div.classList[type != 'multiple' && single != 'private' ? 'remove' : 'add']('ba-hide-element');
            } else if (['duration', 'availability'].indexOf(div.dataset.fieldKey) != -1) {
                div.classList[type != 'multiple' && time == 'yes' ? 'remove' : 'add']('ba-hide-element');
            } else if (div.dataset.fieldKey == 'booking-hours') {
                div.classList[type != 'multiple' && time == 'yes' && availability == 'custom' ? 'remove' : 'add']('ba-hide-element');
            } else {
                div.classList[type != 'multiple' ? 'remove' : 'add']('ba-hide-element');
            }
            if (div.dataset.fieldKey == 'type') {
                single = div.querySelector('select').value;
            } else if (div.dataset.fieldKey == 'time') {
                time = div.querySelector('select').value;
            }if (div.dataset.fieldKey == 'availability') {
                availability = div.querySelector('select').value;
            }
        })
    }).on('change', 'div[data-field-key="type"]', function(){
        let wrapper = this.closest('#ba-group-product-booking'),
            type = this.querySelector('select').value;
        this.classList[type == 'private' ? 'remove' : 'add']('one-fifty-width');
        wrapper.querySelector('div[data-field-key="participants"]').classList[type == 'private' ? 'add' : 'remove']('ba-hide-element');
    }).on('change', 'div[data-field-key="time"]', function(){
        let wrapper = this.closest('#ba-group-product-booking'),
            availability = null
            time = this.querySelector('select').value;
        wrapper.querySelectorAll('[data-field-key="duration"], [data-field-key="availability"]').forEach((div) => {
            if (div.dataset.fieldKey == 'availability') {
                availability = div.querySelector('select').value;
            }
            div.classList[time == 'yes' ? 'remove' : 'add']('ba-hide-element');
        });
        wrapper.querySelector('div[data-field-key="booking-hours"]')
            .classList[time == 'no' || availability != 'custom' ? 'add' : 'remove']('ba-hide-element');
    }).on('change', 'div[data-field-key="availability"]', function(){
        let wrapper = this.closest('#ba-group-product-booking'),
            availability = this.querySelector('select').value;
        wrapper.querySelector('div[data-field-key="booking-hours"]').classList[availability != 'custom' ? 'add' : 'remove']('ba-hide-element');
    }).on('click', '.booking-calendar-delete-hours', function(){
        let subgroup = this.closest('.ba-subgroup-element');
        this.closest('.booking-working-hours-element').remove();
        subgroup.style.setProperty('--subgroup-childs', subgroup.querySelectorAll('.booking-working-hours-element').length);
    }).on('click', '.booking-calendar-add-hours', function(){
        let content = document.querySelector('template.booking-calendar-default-hours').content.cloneNode(true),
            subgroup = this.closest('.booking-working-hours-group').querySelector('.ba-subgroup-element');
        subgroup.append(content);
        subgroup.style.setProperty('--subgroup-childs', subgroup.querySelectorAll('.booking-working-hours-element').length)
    }).on('change', '.booking-working-hours-element input[type="checkbox"]', function(){
        this.closest('.booking-working-hours-element').classList[this.checked ? 'add' : 'remove']('booking-working-hours-enabled');
    });

    $g('.blog-post-editor-options-group[data-field-key="booking_payment"] select[name="unit"]').on('change', function(){
        this.closest('div').querySelector('.field-editor-price-currency').textContent = this.value == '%' ? '%' : this.dataset.symbol;
    });

    $g('.blog-post-editor-options-group[data-field-key="booking_payment"] select[name="type"]').on('change', function(){
        this.closest('.blog-post-editor-options-group').classList[this.value == 'complete' ? 'remove' : 'add']('booking-partial-payment');
    });
}