/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

var priceRange = function(element) {
    this.picker = $g(element);
    this.stylePos = 'left';
    this.mousePos = 'pageX';
    this.sizePos = 'offsetWidth';
    this.min = element.dataset.min;
    this.max = element.dataset.max;
    this.minInput = this.picker.closest('.ba-field-filter-value-wrapper').find('input[data-min]');
    this.maxInput = this.picker.closest('.ba-field-filter-value-wrapper').find('input[data-max]');
    this.step = 1;
    this.value = [element.dataset.minValue, element.dataset.maxValue];
    this.selectionEl = this.picker.find('.price-range-selection');
    this.selectionElStyle = this.selectionEl[0].style;
    this.handle1 = this.picker.find('.price-range-handle:first');
    this.handle1Stype = this.handle1[0].style;
    this.handle2 = this.picker.find('.price-range-handle:last');
    this.handle2Stype = this.handle2[0].style;
    this.diff = this.max - this.min;
    this.percentage = [
        (this.value[0] - this.min) * 100 / this.diff,
        (this.value[1] - this.min) * 100 / this.diff,
        this.step * 100 / this.diff
    ];
    this.offset = this.picker.offset();
    this.size = this.picker[0][this.sizePos];
    var $this = this;
    this.picker.on('mouseenter', function(){
        $this.size = $this.picker[0][$this.sizePos];
    })
    this.picker.on({
        mousedown: $g.proxy(this.mousedown, this),
        touchstart: $g.proxy(this.mousedown, this)
    });
};    
priceRange.prototype = {
    constructor: priceRange,
    layout: function(){
        this.handle1Stype[this.stylePos] = this.percentage[0]+'%';
        this.handle2Stype[this.stylePos] = this.percentage[1]+'%';
        this.selectionElStyle.left = Math.min(this.percentage[0], this.percentage[1]) +'%';
        this.selectionElStyle.width = Math.abs(this.percentage[0] - this.percentage[1]) +'%';
    },
    updateInput: function(){
        this.minInput.val(Math.round(this.value[0]));
        this.maxInput.val(Math.round(this.value[1]));
    },
    mousedown: function(ev) {
        if (ev.type === 'touchstart') {
            ev = ev.originalEvent;
        }
        if (app.itemsFilterBtn && app.itemsFilterBtn.classList.contains('ba-items-filter-show-button')) {
            app.itemsFilterBtn.style.display = 'none';
        }
        this.offset = this.picker.offset();
        this.size = this.picker[0][this.sizePos];
        var percentage = this.getPercentage(ev),
            diff1 = Math.abs(this.percentage[0] - percentage),
            diff2 = Math.abs(this.percentage[1] - percentage);
        this.dragged = (diff1 < diff2) ? 0 : 1;
        this.percentage[this.dragged] = percentage;
        this.layout();
        this.updateInput();
        $g(document).on({
            touchmove: $g.proxy(this.mousemove, this),
            touchend: $g.proxy(this.mouseup, this),
            mousemove: $g.proxy(this.mousemove, this),
            mouseup: $g.proxy(this.mouseup, this)
        });
        this.calculateValue();
        
        return false;
    },
    mousemove: function(ev) {
        if (ev.type === 'touchmove') {
            ev = ev.originalEvent;
        }
        var percentage = this.getPercentage(ev);
        if (this.dragged === 0 && this.percentage[1] < percentage) {
            this.percentage[0] = this.percentage[1];
            this.dragged = 1;
        } else if (this.dragged === 1 && this.percentage[0] > percentage) {
            this.percentage[1] = this.percentage[0];
            this.dragged = 0;
        }
        this.percentage[this.dragged] = percentage;
        this.layout();
        this.updateInput();
        this.calculateValue();
        
        return false;
    },
    mouseup: function() {
        $g(document).off({
            touchmove: this.mousemove,
            touchend: this.mouseup,
            mousemove: this.mousemove,
            mouseup: this.mouseup
        });
        this.layout();
        this.updateInput();
        this.calculateValue();
        let key = this.picker.closest('.ba-item').attr('id'),
            obj = app.items[key];
        showItemsFilterBtn(this.picker[0], obj, key);
        
        return false;
    },
    calculateValue: function() {
        this.value = [
            ((this.percentage[0]*1/this.percentage[2]*1)*this.step+this.min*1),
            ((this.percentage[1]*1/this.percentage[2]*1)*this.step+this.min*1)
        ];
    },
    getPercentage: function(ev) {
        if (ev.touches) {
            ev = ev.touches[0];
        }
        var percentage = (ev[this.mousePos] - this.offset[this.stylePos]) * 100 / this.size;
        percentage = Math.round(percentage / this.percentage[2]) * this.percentage[2];

        return Math.max(0, Math.min(100, percentage));
    }
};
$g.fn.priceRange = function(){
    return this.each(function(){
        var $this = $g(this),
            data = $this.data('priceRange');
        if (!data)  {
            $this.data('priceRange', (data = new priceRange(this)));
        }
    })
};
$g.fn.priceRange.Constructor = priceRange;

function showItemsFilterBtn($this, obj, key)
{
    let target = null,
        wrapper = $this.closest('.ba-fields-filter-wrapper');
    if ($this.type == 'checkbox' && ($this.closest('.ba-filter-color-value') || $this.closest('.ba-filter-image-value'))) {
        target = $this.closest('.ba-field-filter-value-wrapper').querySelector('.ba-field-filter-value-wrapper > div:last-child');
    } else if ($this.type == 'checkbox') {
        target = $this.closest('.ba-checkbox-wrapper').querySelector('.ba-checkbox-wrapper > span');
    } else {
        target = $this;
    }
    getItemsFilterCount(obj, wrapper, target);
}

function testitemsFilterQuery(queryURI)
{
    if (queryURI == '/?query=') {
        queryURI = JUri+'?query=';
    }
    let subLocation = window.location.href.replace('/index.php', ''),
        flag = false,
        subQuery = queryURI.replace('/index.php', ''),
        index = 0,
        queryIndexes = [subLocation.indexOf('query='), subQuery.indexOf('query=')];
    if (queryIndexes[0] != -1) {
        subLocation = subLocation.substr(0, queryIndexes[0] - 1);
    }
    subQuery = subQuery.substr(0, queryIndexes[1] - 1);
    index = subLocation.indexOf(subQuery);
    if (index > 0) {
        subLocation = subLocation.substr(index);
        index = 0;
    }
    if (index == 0 && subLocation[subLocation.length - 1] == '/' && subQuery[subQuery.length - 1] != '/') {
    	subLocation = subLocation.substr(0, subLocation.length - 1);
    }
    flag = subLocation == subQuery;

    return flag
}

function loadItemsFilterContent(query, wrapper)
{
    console.info(query, wrapper)
    let div = document.createElement('div'),
        xhr = new XMLHttpRequest();
    xhr.onload = xhr.onerror = function(){
        div.innerHTML = this.responseText;
        $g('.ba-item-fields-filter.ba-item').each(function(){
            let filterWrapper = this.querySelector('.ba-fields-filter-wrapper'),
                item = div.querySelector('#'+this.id+' .ba-selected-values-wrapper');
            if (item && filterWrapper == wrapper) {
                this.querySelector('.ba-selected-values-wrapper').innerHTML = item.innerHTML;
            } else if (item && filterWrapper != wrapper) {
                item = div.querySelector('#'+this.id);
                this.innerHTML = item.innerHTML;
                app.initItems(app.items[this.id], this.id);
                $g(this).find('.open-calendar-dialog').each(function(){
                    setupCalendar(this)
                });
            }
        });
        $g('.ba-item-blog-posts.ba-item').each(function(){
            let item = div.querySelector('#'+this.id);
            if (item) {
                $g(this).html(item.innerHTML);
                app.initItems(app.items[this.id], this.id);
            }
        });
        $g('style[data-id="adaptive-images"]').each(function(){
            let item = div.querySelector('style[data-id="adaptive-images"]');
            if (item) {
                this.innerHTML = item.innerHTML;
            }
        });
        $g('.ba-item-google-maps-places').each(function(){
            let pages = div.querySelector('#'+this.id+' .ba-map-wrapper').dataset.pages
            this.querySelector('.ba-map-wrapper').dataset.pages = pages
            gridboxMaps.init(app.items[this.id], this.id);
        });
        if (app.lazyLoad) {
            app.lazyLoad.check();
        }
        window.history.replaceState(null, null, query);
    }
    xhr.open("GET", query, true);
    xhr.send();
}

function showFilterResults($this, wrapper)
{
    let flag = testitemsFilterQuery($this.queryURI);
    document.querySelector('.ba-items-filter-show-button').style.display = 'none';
    if (flag) {
        loadItemsFilterContent($this.queryURI+$this.queryStr, wrapper);
    } else {
        window.location.href = $this.queryURI+$this.queryStr;
    }
}

function getItemsFilterCount(obj, wrapper, target)
{
    if (themeData.page.view == 'gridbox') {
        return false;
    }
    let query = {},
        subArray = [],
        data = '',
        span = document.querySelector('.ba-items-filter-show-button');
    if (!span) {
        span = document.createElement('span');
        span.className = 'ba-items-filter-show-button';
        span.addEventListener('click', function(){
            if (!this.classList.contains('horizontal-filter-tooltip')) {
                showFilterResults(this, wrapper);
            }
        });
        document.body.appendChild(span);
    }
    if (obj.auto || !wrapper.classList.contains('horizontal-filter-bar')) {
        app.itemsFilterBtn = span;
    } else if (wrapper.classList.contains('horizontal-filter-bar') && !app.itemsFilterBtn) {
        app.itemsFilterBtn = wrapper.querySelector('.ba-items-filter-search-button');
        app.itemsFilterBtn.addEventListener('click', function(){
            showFilterResults(this, wrapper);
        });
    }
    $g(wrapper).find('.ba-field-filter [name]').each(function(){
        if (!query[this.name]) {
            query[this.name] = [];
        }
        if (this.type == 'checkbox' && this.checked) {
            query[this.name].push(this.value);
        } else if (this.type == 'number') {
            let value = this.value;
            if (!value && 'min' in this.dataset) {
                value = this.dataset.min;
            } else if (!value && 'max' in this.dataset) {
                value = this.dataset.max;
            }
            query[this.name].push(value);
        } else if (this.classList.contains('open-calendar-dialog') && this.value.trim()) {
            query[this.name].push(this.dataset.value);
        }
    });
    $g(wrapper).find('.price-range-track').each(function(){
        let data = $g(this).data('priceRange'),
            value0 = data.value[0],
            value1 = data.value[1],
            name = this.closest('.ba-field-filter-value-wrapper').querySelector('input[name]').name;
        if (!value0) {
            value0 = data.min;
        }
        if (!value1) {
            value1 = data.max;
        }
        if (value0 == data.min && value1 == data.max) {
            delete(query[name]);
        }
    });
    data = JSON.stringify(query);
    for (let ind in query) {
        if (query[ind].length > 0) {
            subArray.push(encodeURIComponent(ind+'__'+query[ind].join('--')));
        }
    }
    app.itemsFilterBtn.queryURI = wrapper.dataset.query;
    app.itemsFilterBtn.queryStr = subArray.join('__');
    span.style.display = 'none';
    if (!obj.auto && target && wrapper.classList.contains('horizontal-filter-bar')) {
        target = app.itemsFilterBtn;
        span.classList.add('horizontal-filter-tooltip');
    } else {
        span.classList.remove('horizontal-filter-tooltip');
    }
    let showTooltip = (wrapper.classList.contains('horizontal-filter-bar') && window.innerWidth > menuBreakpoint)
        || !wrapper.classList.contains('horizontal-filter-bar')
    if (obj.auto) {
        showFilterResults(app.itemsFilterBtn, wrapper);
    } else if (target && showTooltip) {
        let searchParams = new URLSearchParams(window.location.search),
            search = searchParams.has('search') ? searchParams.get('search') : '';
        $g.ajax({
            type: "POST",
            dataType: 'text',
            url: JUri+"index.php?option=com_gridbox&task=page.getItemsFilterCount",
            data: {
                app: obj.app,
                id: wrapper.dataset.category,
                tag: wrapper.dataset.tag,
                author: wrapper.dataset.author,
                search: search,
                data: data
            },
            complete: function(msg){
                span.style.display = '';
                span.textContent = gridboxLanguage['SHOW']+' ('+msg.responseText+')';
                let rect = target.getBoundingClientRect(),
                    computed = getComputedStyle(document.body),
                    borderTopWidth = computed.borderTopWidth.replace(/px|%/, ''),
                    borderLeftWidth = computed.borderLeftWidth.replace(/px|%/, ''),
                    top = (rect.top - span.offsetHeight / 2 + rect.height / 2 - borderTopWidth + pageYOffset),
                    left = rect.right + 10 - borderLeftWidth;
                span.classList.remove('filter-top-button');
                if (left + span.offsetWidth > document.documentElement.clientWidth) {
                    left = rect.left + rect.width / 2 - span.offsetWidth / 2;
                    top = rect.top + document.documentElement.scrollTop - 10 - span.offsetHeight;
                    if (target.classList.contains('price-range-track')) {
                        top -= 20;
                    }
                    span.classList.add('filter-top-button');
                }
                span.style.left = left+'px';
                span.style.top = top+'px';
            }
        });
    } else if (!target) {
        app.itemsFilterBtn.click();
    }
}

app.initItemsFilter = function(obj, key){
    $g('#'+key).off('change click input').on('change', '.ba-field-filter-value-wrapper input[type="checkbox"][name]', function(event){
        showItemsFilterBtn(this, obj, key);
    });
    $g('#'+key).on('click', '.ba-field-date-tags span', function(){
        let formated = this.dataset.formated.split(' - '),
            wrapper = this.closest('.ba-field-filter-value').querySelector('.ba-field-filter-date-calendars'),
            inputs = wrapper.querySelectorAll('.open-calendar-dialog'),
            dates = this.dataset.date.split(' - ');
        inputs[0].value = formated[0];
        inputs[1].value = formated[1];
        inputs[0].dataset.value = dates[0];
        inputs[1].dataset.value = dates[1];
        showItemsFilterBtn(wrapper, obj, key);
    });
    $g('#'+key).on('input', '.open-calendar-dialog[name]', function(){
        if (this.value && ((this.range && this.range.value) || !this.range)) {
            showItemsFilterBtn(this.closest('.ba-field-filter-date-calendars'), obj, key);
        }
    }).find('.open-calendar-dialog[name]').each(function(){
        let $this = this;
        this.range = Array.from(this.closest('.ba-field-filter-date-calendars').querySelectorAll('input[name]')).find(element => element != $this);
    });
    $g('#'+key).on('click', '.ba-field-filter-value-wrapper .ba-checkbox-wrapper > span', function(event){
        this.closest('.ba-checkbox-wrapper').querySelector('label').click();
    });
    $g('#'+key).on('click', '.ba-field-filter-value-wrapper .ba-show-all-filters', function(){
        if (app.itemsFilterBtn) {
            app.itemsFilterBtn.style.display = 'none';
        }
        this.closest('.ba-field-filter-value-wrapper').classList.add('visible-filters-value');
    });
    $g('#'+key).on('click', '.ba-field-filter-value-wrapper .ba-hide-filters', function(){
        if (app.itemsFilterBtn) {
            app.itemsFilterBtn.style.display = 'none';
        }
        this.closest('.ba-field-filter-value-wrapper').classList.remove('visible-filters-value');
    });
    $g('#'+key).on('click', '.ba-field-filter-value-wrapper .ba-cancel-all-filters', function(){
        $g(this).closest('.ba-field-filter-value-wrapper').find('[name]').prop('checked', false);
        showItemsFilterBtn(this.previousElementSibling, obj, key);
    });
    $g('#'+key).on('click', '.ba-selected-filter-values i', function(){
        let span = this.closest('.ba-selected-filter-values'),
            wrapper = this.closest('.ba-fields-filter-wrapper'),
            input = wrapper.querySelector('input[name="'+span.dataset.name+'"]');
        if (input.type == 'checkbox') {
            wrapper.querySelector('input[name="'+span.dataset.name+'"][value="'+span.dataset.value+'"]').checked = false;
        } else if (input.type == 'number') {
            let min = wrapper.querySelector('input[name="'+span.dataset.name+'"][data-min]'),
                max = wrapper.querySelector('input[name="'+span.dataset.name+'"][data-max]'),
                data = $g(min).closest('.ba-field-filter-value-wrapper').find('.price-range-track').data('priceRange');
            min.value = min.dataset.min;
            max.value = max.dataset.max;
            data.value[0] = min.dataset.min;
            data.value[1] = max.dataset.max;
            data.percentage[0] = 0;
            data.percentage[1] = 100;
            data.layout();
        } else if (input.classList.contains('open-calendar-dialog')) {
            wrapper.querySelectorAll('input[name="'+span.dataset.name+'"]').forEach((input) => {
                input.value = input.dataset.value = '';
            })
        }
        span.remove();
        getItemsFilterCount(obj, wrapper, null);
    });
    $g('#'+key).on('click', '.ba-selected-filter-values-remove-all', function(){
        let wrapper = this.closest('.ba-fields-filter-wrapper');
        $g(wrapper).find('input[name].open-calendar-dialog').val('').attr('data-value', '');
        $g(wrapper).find('input[name][type="checkbox"]').prop('checked', false);
        $g(wrapper).find('input[name][type="number"][data-min]').each(function(){
            let max = wrapper.querySelector('input[name="'+this.name+'"][data-max]'),
                track = $g(this).closest('.ba-field-filter-value-wrapper').find('.price-range-track'),
                data = track.data('priceRange');
            this.value = this.dataset.min;
            max.value = max.dataset.max;
            data.value[0] = this.dataset.min;
            data.value[1] = max.dataset.max;
            data.percentage[0] = 0;
            data.percentage[1] = 100;
            data.layout();
        });
        getItemsFilterCount(obj, wrapper, null);
    });
    $g('#'+key).on('click', '.ba-field-filter-label', function(event){
        let parent = this.closest('.ba-field-filter'),
            wrapper = parent.closest('.ba-fields-filter-wrapper');
        if (wrapper.classList.contains('horizontal-filter-bar')) {
            if (!parent.classList.contains('visible-horizontal-filters-value')) {
                event.stopPropagation();
                $g('.visible-horizontal-filters-value').removeClass('visible-horizontal-filters-value');
                $g('.visible-horizontal-filters-row').removeClass('visible-horizontal-filters-row');
                parent.classList.add('visible-horizontal-filters-value');
                $g(this).parentsUntil('body').filter('.ba-row').addClass('visible-horizontal-filters-row');
                $g('body').addClass('visible-horizontal-filters');
            }
        } else if (wrapper.classList.contains('ba-collapsible-filter')) {
            let h = parent.querySelector('.ba-field-filter-value').scrollHeight;
            parent.style.setProperty('--filter-value-height', h+'px');
            parent.classList.remove('ba-filter-icon-rotated');
            $g('.ba-items-filter-show-button').hide();
            if (parent.classList.contains('ba-filter-collapsed')) {
                parent.delay = setTimeout(function(){
                    parent.classList.remove('ba-filter-collapsed');
                }, 300);
            } else {
                clearTimeout(parent.delay);
                parent.classList.add('ba-filter-collapsed');
                parent.classList.add('ba-filter-icon-rotated');
                setTimeout(function(){
                    parent.style.setProperty('--filter-value-height', 0);
                }, 50);
            }
        }
    });
    $g('#'+key).on('click', '.open-responsive-filters', function(event){
        let wrapper = this.parentNode.querySelector('.ba-fields-filter-wrapper'),
            height = wrapper.scrollHeight;
        if (app.itemsFilterBtn && app.itemsFilterBtn.classList.contains('ba-items-filter-show-button')) {
            app.itemsFilterBtn.style.display = 'none';
        }
        if (!wrapper.classList.contains('visible-responsive-filters')) {
            wrapper.style.setProperty('--responsive-filters-height', height+'px');
            wrapper.style.setProperty('--responsive-filters-overflow', 'hidden');
            wrapper.classList.add('visible-responsive-filters');
            setTimeout(function(){
                wrapper.style.setProperty('--responsive-filters-height', 'auto');
                wrapper.style.setProperty('--responsive-filters-overflow', 'visible');
            }, 500);
        } else {
            wrapper.style.setProperty('--responsive-filters-height', height+'px');
            wrapper.style.setProperty('--responsive-filters-overflow', 'hidden');
            setTimeout(function(){
                wrapper.classList.remove('visible-responsive-filters');
            }, 50);
        }
    });
    $g('#'+key).on('click', '.visible-horizontal-filters-value', function(event){
        event.stopPropagation();
    });
    $g('#'+key+' .price-range-track').each(function(){
        $g(this).priceRange();
    });
    $g('#'+key).on('input', '.ba-field-filter-input-wrapper input', function(){
        let value = this.value * 1,
            track = $g(this).closest('.ba-field-filter-value-wrapper').find('.price-range-track'),
            data = track.data('priceRange'),
            max = this.dataset.max,
            min = this.dataset.min,
            key = this.dataset.min ? 0 : 1;
        if (app.itemsFilterBtn && app.itemsFilterBtn.classList.contains('ba-items-filter-show-button')) {
            app.itemsFilterBtn.style.display = 'none';
        }
        if (!this.dataset.min) {
            min = this.closest('.ba-field-filter-input-wrapper').querySelector('input[data-min]').dataset.min * 1;
        }
        if (!this.dataset.max) {
            max = this.closest('.ba-field-filter-input-wrapper').querySelector('input[data-max]').dataset.max * 1;
        }
        if (value < min) {
            value = min;
        }
        if (value > max) {
            value = max;
        }
        data.value[key] = value;
        data.percentage = [
            (data.value[0] - data.min) * 100 / data.diff,
            (data.value[1] - data.min) * 100 / data.diff,
            data.step * 100 / data.diff
        ];
        data.size = data.picker[0][data.sizePos];
        data.layout();
        clearTimeout(this.delay);
        this.delay = setTimeout(function(){
            showItemsFilterBtn(track[0], obj, key);
        }, 1000);
    });
    initItems();
}

$g(document).on('click.fieldFilter', function(event){
    if (event.target.closest('.gridbox-calendar-wrapper') &&
        (gridboxCalendar.input.value == '' || (gridboxCalendar.input.range && gridboxCalendar.input.range.value == ''))) {
        return;
    }
    $g('.visible-horizontal-filters-value').removeClass('visible-horizontal-filters-value');
    $g('.visible-horizontal-filters-row').removeClass('visible-horizontal-filters-row');
    document.body.classList.remove('visible-horizontal-filters');
});

if (app.modules.initItemsFilter) {
    app.initItemsFilter(app.modules.initItemsFilter.data, app.modules.initItemsFilter.selector);
}