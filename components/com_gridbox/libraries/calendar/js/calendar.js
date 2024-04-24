window.gridboxCalendar = {
    multiple: [],
    create: function(){
        if (!document.querySelector('.zmdi')) {
            let link = document.createElement('link');
            link.href = JUri+"/templates/gridbox/library/icons/material/material.css"
            link.rel = "stylesheet";
            link.type = "text/css"
            document.head.append(link)
        }
        this.calendar = document.createElement('div');
        this.calendar.className = 'gridbox-calendar-wrapper';
        this.calendar.innerHTML = '<div class="ba-gridbox-calendar"></div><div class="ba-close-calendar"></div>';
        document.body.appendChild(this.calendar);
        if ('setTooltip' in app) {
            app.setTooltip(this.calendar)
        }
        this.date = new Date();
        this.setEvents();
    },
    setEvents: function(){
        let calendar = this.calendar;
        $g(calendar).on('mousedown', function(event){
            event.stopPropagation();
        }).on('click', 'i[data-action]', function(){
            let year = gridboxCalendar.year * 1,
                month = gridboxCalendar.month * 1;
            if (this.dataset.action == 'next') {
                year = (month === 11) ? year + 1 : year;
                month = (month + 1) % 12;
            } else if (this.dataset.action == 'prev') {
                year = (month === 0) ? year - 1 : year;
                month = (month === 0) ? 11 : month - 1;
            } else if (this.dataset.action == 'next-year') {
                year = year + 1;
            } else if (this.dataset.action == 'prev-year') {
                year = year - 1;
            }
            gridboxCalendar.year = year;
            gridboxCalendar.month = month;
            gridboxCalendar.render();
        }).on('mouseleave', '.ba-date-cell', function(){
            if (!gridboxCalendar.isMultiple || gridboxCalendar.multiple.length != 1) {
                return;
            }
            gridboxCalendar.calendar.querySelectorAll('.ba-date-cell').forEach((cell) => {
                cell.classList.remove('active');
            });
        }).on('mouseenter', '.ba-date-cell', function(){
            if (!gridboxCalendar.isMultiple || gridboxCalendar.multiple.length != 1 || this.classList.contains('disabled-date')) {
                return;
            }
            let date = gridboxCalendar.multiple[0].dataset.date,
                hovered = this;
            gridboxCalendar.calendar.querySelectorAll('.ba-date-cell').forEach((cell) => {
                cell.classList[date <= cell.dataset.date && cell.dataset.date <= hovered.dataset.date ? 'add' : 'remove']('active');
            });
        }).on('click', '.ba-date-cell, .ba-gridbox-today-btn', function(){
            if (this.classList.contains('disabled-date')) {
                return;
            }
            if (gridboxCalendar.isMultiple && !this.classList.contains('selected')) {
                gridboxCalendar.multiple.push(this);
                this.classList.add('selected');
                gridboxCalendar.checkCells();
            }
            if (gridboxCalendar.isMultiple && gridboxCalendar.multiple.length == 1) {
                return;
            }
            let value = input = null;
            if (gridboxCalendar.isMultiple) {
                gridboxCalendar.multiple.forEach((cell, i) => {
                    value = cell.dataset.formated+(!gridboxCalendar.format ? ' '+cell.dataset.time : '');
                    if ((gridboxCalendar.input.dataset.key == 'from' && i == 0)
                        || (gridboxCalendar.input.dataset.key == 'to' && i == 1)) {
                        input = gridboxCalendar.input;
                    } else {
                        input = gridboxCalendar.input.range;
                    }
                    input.dataset.value = cell.dataset.date;
                    input.value = value;
                });
            } else {
                value = this.dataset.formated+(!gridboxCalendar.format ? ' '+this.dataset.time : '');
                gridboxCalendar.input.dataset.value = this.dataset.date;
                gridboxCalendar.input.value = value;
            }
            gridboxCalendar.update(this);
            gridboxCalendar.hide();
        }).on('click', '.ba-close-calendar', function(){
            gridboxCalendar.hide();
        });
    },
    update: function(cell){
        $g(this.input).trigger('update', cell);
    },
    show: function(input, product_id){
        if (!this.calendar) {
            this.create();
        }
        if (input.dataset.year) {
            this.year = input.dataset.year * 1;
            this.month = input.dataset.month * 1;
        } else {
            this.year = this.date.getFullYear();
            this.month = this.date.getMonth();
        }
        this.product_id = product_id;
        this.format = input.dataset.format ? input.dataset.format : '';
        this.footer = input.dataset.footer ? input.dataset.footer : 0;
        this.multiple_calendar = input.dataset.multiple ? input.dataset.multiple : 0;
        this.input = input;
        this.render().then(function(){
            gridboxCalendar.showCalendar();
        });
    },
    hide: function(){
        gridboxCalendar.multiple = [];
        this.calendar.classList.remove('visible-gridbox-calendar');
    },
    showCalendar: function(){
        this.calendar.classList.add('visible-gridbox-calendar');
    },
    checkCells: function(){
        let $this = this;
        this.calendar.querySelectorAll('.ba-date-cell').forEach(function(cell){
            let flag = $this.input.disableFunc ? $this.input.disableFunc(cell.dataset.date, cell) : false;
            if ($this.input.dataset.day && cell.dataset.day == $this.input.dataset.day
                && $this.year == $this.input.dataset.year * 1 && $this.month == $this.input.dataset.month * 1) {
                cell.classList.add('ba-curent-date');
            } else if ($this.input.dataset.day) {
                cell.classList.remove('ba-curent-date');
            }

            if (gridboxCalendar.isMultiple && gridboxCalendar.multiple.length == 1) {
                cell.classList[cell.dataset.date == gridboxCalendar.multiple[0].dataset.date ? 'add' : 'remove']('selected');
            }

            cell.classList[flag ? 'add' : 'remove']('disabled-date');
        });
    },
    render: function(){
        return new Promise((resolve, reject) => {

            app.fetch(JUri+'index.php?option=com_gridbox&task=calendar.render', {
                start: 1,
                year: gridboxCalendar.year,
                month: gridboxCalendar.month * 1 + 1,
                date_format: gridboxCalendar.format,
                product_id: gridboxCalendar.product_id,
                footer: gridboxCalendar.footer,
                multiple: gridboxCalendar.multiple_calendar
            }).then(function(html){
                gridboxCalendar.calendar.querySelector('.ba-gridbox-calendar').innerHTML = html;
                gridboxCalendar.isMultiple = gridboxCalendar.calendar.querySelectorAll('.ba-gridbox-calendar-inner').length > 1;
                gridboxCalendar.calendar.classList[gridboxCalendar.isMultiple ? 'add' : 'remove']('ba-gridbox-multiple-date-calendar');
                gridboxCalendar.checkCells();
                resolve();
            });
        });

    }
}