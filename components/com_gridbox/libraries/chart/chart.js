function liteChart(settings) {
    panel = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    var def = {
        labels: {
            fontSize: 12,
            list: null,
        },
        legends: {
            list: []
        },
        line: {
            style: "straight",
        },
        padding: {
            top: 55,
            right: 15,
            bottom: 40,
            left: 20,
        },
        panel: panel,
        point: {
            radius: 10
        }
    };
    Object.assign(this, def, settings);
}

function createElement(elem, attributes)
{
    let el = document.createElementNS("http://www.w3.org/2000/svg", elem);
    for (var key in attributes) {
        el.setAttribute(key, attributes[key]);
    }

    return el;
}

function createText(x, y, textAnchor, alignmentBaseline, contents)
{
    let text = createElement( "text", {"x":x.toFixed(),"y":y.toFixed(),"text-anchor":textAnchor} );
    text.setAttribute("alignment-baseline", alignmentBaseline); 
    text.textContent = contents;

    return text;
}

function getPath(chart, chords)
{
    let i = 0,
        path = "";
    if (chart.line.style == 'straight') {
        chords.forEach(function(elem){
            if (i == 0) {
                path += " M ";
            }
            path += elem.x1.toFixed() + " " + elem.y1.toFixed() + " ";
            ++i;
            if (chords.length === i) {
                path += elem.x2.toFixed() + " " + elem.y2.toFixed();
            }
        });
    } else {
        chords.forEach(function(elem){
            if (i === 0) {
                path += "M "+elem.x1.toFixed()+" "+elem.y1.toFixed();
            }
            path += " C "+elem.cx1.toFixed()+" "+elem.cy1.toFixed()+" "+elem.cx2.toFixed();
            path += " "+elem.cy2.toFixed()+" "+elem.x2.toFixed()+" "+elem.y2.toFixed();
            ++i;
        });
    }

    return path;
}

liteChart.prototype.getWidth = function(){
    return this.panel.getBoundingClientRect().width;
}

liteChart.prototype.getHeight = function(){
    return this.panel.getBoundingClientRect().height;
}

liteChart.prototype.setLabels = function(labels) {
    this.labels.list = labels;
}

liteChart.prototype.addLegend = function(legend){
    this.legends.list.push(legend);
}

liteChart.prototype.getMaxValue = function(){
    let max = value = 0;
    this.legends.list.forEach(function(legend){
        legend.values.forEach(function(value){
            if (max < value) {
                max = value;
            }
        });
    });

    return max;
}

liteChart.prototype.inject = function(obj){
    obj.appendChild(this.panel);
    let chart = this,
        panel = this.panel;
    panel.wrapper = obj;
    panel.style.width = obj.clientWidth;
    panel.style.height = obj.clientHeight;
    window.addEventListener("resize", function() {
        panel.style.width = obj.clientWidth;
        panel.style.height = obj.clientHeight;
        chart.draw();
    });
}


liteChart.prototype.draw = function(){    
    this.panel.innerHTML = '';
    let NS = "http://www.w3.org/2000/svg";
    let line = function(x1, y1, x2, y2){

        return createElement("line", {"x1":x1.toFixed(),"y1":y1.toFixed(),"x2":x2.toFixed(),"y2":y2.toFixed()});
    };
    let area = function(chart, chords){
        
        return createElement("path", {"d":getPath(chart, chords)});
    };
    let circle = function(cx, cy){

        return createElement("circle", {"cx":cx,"cy":cy});
    };
    let getGridXLabelWidth = function(chart){
        let width = maxWidth = 0,
            margin = chart.labels.fontSize / 3,
            temp =  tempText2 = null;
        temp = createText(0, 0, "end", "middle", chart.max);
        chart.panel.appendChild(temp);
        maxWidth = temp.getBoundingClientRect().width;
        chart.panel.removeChild(temp);
        width = margin + maxWidth + 10 + margin;

        return width;
    }
    let getLabelHeight = function(chart){
        
        return chart.labels.fontSize / 2 + chart.labels.fontSize + chart.labels.fontSize / 2;
    }
    let getAxisYMaxValue = function(chart, v){
        let max = Math.round(v ? v : chart.getMaxValue()),
            s = String(max),
            l = s.length,
            d = Math.pow(10, l - 1);
        max = Math.ceil(max / d) * d;
        if (max == 0) {
            max = 400;
        }
        s = String(max / 4);
        l = s.length;
        if (s[l -1] != 0) {
            max = getAxisYMaxValue(chart, max + 1);
        }

        return max;
    }
    let getRatio = function(chart){
        let valueRange = chart.max,
            graphHeight = chart.getHeight() - chart.padding.top - chart.padding.bottom - getLabelHeight(chart);

        return graphHeight / valueRange;
    }
    this.max = getAxisYMaxValue(this);
    let width = this.getWidth(),
        height = this.getHeight(),
        graphMarginTop = this.padding.top,
        graphMarginBottom = this.padding.bottom + getLabelHeight(this),
        graphMarginLeft = this.padding.left + getGridXLabelWidth(this),
        graphMarginRight = this.padding.right,
        graphWidth = width - graphMarginLeft - graphMarginRight,
        graphHeight = height - graphMarginTop - graphMarginBottom;
    let drawGridX = function(chart){
        let axisYMaxValue = chart.max,
            unit = axisYMaxValue / 4,
            currentLineValue = 0,
            currentLineHeight = gridIndex = 0,
            label = null,
            g = document.createElementNS(NS, "g");
        chart.panel.appendChild(g);
        while (currentLineValue <= axisYMaxValue) {
            currentLineHeight = chart.getHeight() - chart.padding.bottom - getLabelHeight(chart) - currentLineValue * getRatio(chart);
            val = currentLineValue;
            label = createText(graphMarginLeft - 10, currentLineHeight, "end", "middle", val);
            g.appendChild(label);
            currentLineValue += unit;
            ++gridIndex;
        }
    };
    let drawLabels = function(chart){
        let labelCount = chart.labels.list.length,
            legendCount = 1,
            groupUnit = 2 * legendCount + 1,
            unitCount = groupUnit * labelCount + 1,
            unitSize = graphWidth / unitCount,
            index = x = 0,
            label = null,
            labels = chart.labels.list,
            y = chart.padding.top + graphHeight + (chart.labels.fontSize / 2),
            g = document.createElementNS(NS, "g");
        chart.panel.appendChild(g);
        labels.forEach(function(label){
            x = graphMarginLeft + (groupUnit * index + legendCount + 1) * unitSize;
            label = createText(x, y + 10, 'middle', 'hanging', label);
            g.appendChild(label);
            ++index;
        });
    };
    let drawLines = function(chart) {
        let labelCount = chart.labels.list.length,
            groupUnit = 3,
            unitCount = groupUnit * labelCount + 1,
            unitWidth = graphWidth / unitCount,
            legendsIndex = 0;
        chart.legends.list.forEach(function(legend){
            let valuesIndex = x1 = x2 = y0 = y2 = 0,
                y1 = line1 = null,
                lineD0 = lineD1 = "",
                chords = [];
            legend.values.forEach(function(value){
                var i = 0;
                if (labelCount > valuesIndex) {
                    x2 = graphMarginLeft + unitWidth / 2 + ((groupUnit * unitWidth) / 2) + (groupUnit * unitWidth * valuesIndex);
                    y2 = chart.padding.top + graphHeight - (value * getRatio(chart));
                    if (y1 != null && y2 >= chart.padding.top - 0.1 && y2 <= chart.padding.top + graphHeight + 0.1) {
                        var part = {
                            x1: x1, y1: y1,
                            cx1: (x1 + x2) / 2, cy1: y1,
                            x2: x2, y2: y2,
                            cx2: (x1 + x2) / 2, cy2: y2,
                        }
                        chords.push(part);
                    }
                    x1 = x2;
                    y0 = y1;
                    y1 = y2;
                    ++valuesIndex;
                }
                i++;
            });
            line1 = area(chart, chords);
            chart.panel.appendChild(line1);
            ++legendsIndex;
        });
    };
    let drawPoints = function(chart) {
        let labelCount = chart.labels.list.length,
            groupUnit = 3,
            unitCount = groupUnit * labelCount + 1,
            unitWidth = graphWidth / unitCount,
            legendsIndex = 0;
        chart.legends.list.forEach(function(legend){
            let valuesIndex = x = y = pointValueY = 0,
                point = pointValue = null,
                minY = chart.padding.top + graphHeight - (chart.labels.fontSize / 3),
                g = document.createElementNS(NS, "g");
            g.classList.add('ba-chart-points-wrapper');
            chart.panel.appendChild(g);
            legend.values.forEach(function(value){
                if (labelCount > valuesIndex) {
                    x = graphMarginLeft + unitWidth / 2 + ((groupUnit * unitWidth) / 2) + (groupUnit * unitWidth * valuesIndex);
                    y = chart.padding.top + graphHeight - (value * getRatio(chart));
                    if (y >= chart.padding.top - 0.1 && y <= chart.padding.top + graphHeight + 0.1) {
                        point = circle(x, y);
                        g.appendChild(point);
                    }
                    pointValueY = y - chart.point.radius - (chart.labels.fontSize / 3);
                    pointValueY = minY < pointValueY ? minY : pointValueY;
                    pointValueY -= 10;
                    if (pointValueY > chart.labels.fontSize) {
                        let price = app.renderPrice(value);
                        pointValue = createText(x, pointValueY, "middle", "baseline", price);
                        g.appendChild(pointValue);
                        let r = pointValue.getBoundingClientRect(),
                            rect = createElement('rect', {
                                x: x - 10 - r.width / 2,
                                y: pointValueY - 8 - r.height,
                                width: r.width + 20,
                                height: r.height + 20,
                            });
                        g.appendChild(rect);
                        g.appendChild(pointValue);
                    }
                    ++valuesIndex;
                }
            });
            ++legendsIndex;
        });
    };
    drawGridX(this);
    drawLabels(this);
    drawLines(this);
    drawPoints(this);
    this.panel.wrapper.classList.add('ba-chart-loaded');
}