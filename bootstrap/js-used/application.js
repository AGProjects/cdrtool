
// GRAPHS

function bytes(bytes, label) {
    if (bytes == 0) return '';
    var s = ['', 'K', 'M', 'G', 'T', 'P'];
    var e = Math.floor(Math.log(bytes)/Math.log(1000));
    var value = ((bytes/Math.pow(1000, Math.floor(e))).toFixed(1));
    e = (e<0) ? (-e) : e;
    if (label) value += ' ' + s[e];
    return value;
}

function between(x, min, max) {
    return x >= min && x <= max;
}

function formatDate(myDate){
    var day = [];
    var month = [];

    day[0]="Sunday";
    day[1]="Monday";
    day[2]="Tuesday";
    day[3]="Wednesday";
    day[4]="Thursday";
    day[5]="Friday";
    day[6]="Saturday";
    month[0]="Jan";
    month[1]="Feb";
    month[2]="Mar";
    month[3]="Apr";
    month[4]="May";
    month[5]="Jun";
    month[6]="Jul";
    month[7]="Aug";
    month[8]="Sep";
    month[9]="Oct";
    month[10]="Nov";
    month[11]="Dec";

    var hours = myDate.getUTCHours();
    var minutes = myDate.getMinutes();
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes;

    return(day[myDate.getDay()]+", "+month[myDate.getMonth()]+ ' '+ myDate.getDate()+", "+strTime);
}


function isBigEnough(value) {
    return function(element, index, array) {
        return (element[0] >= value);
    };
}
function isSmallEnough(value) {
    return function(element, index, array) {
        return (element[0] <= value);
    };
}


function MultiDonut(id, in_data) {
    var margin = {top: 150, right: 300, bottom: 150, left: 300},
        radius = Math.min(margin.top, margin.right, margin.bottom, margin.left) - 40;

    var hue = d3.scale.category10();

    var luminance = d3.scale.sqrt()
        .domain([0, 5000])
        .clamp(true)
        .range([90, 20]);

    var svg = d3.select(id).append("svg")
        // TODO: check if svg is now fully responsive
        // .attr("width", margin.left + margin.right)
        // .attr("height", margin.top + margin.bottom+50)
        // .attr("width","100%")
       .attr("viewBox","0 0 600 350")
       .attr("style","max-height: 350px")
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var partition = d3.layout.partition()
        .sort(function(a, b) { return d3.ascending(a.name, b.name); })
        .size([2 * Math.PI, radius]);

    var arc = d3.svg.arc()
        .startAngle(function(d) { return d.x; })
        .endAngle(function(d) { return d.x + d.dx - .01 / (d.depth + .5); })
        .innerRadius(function(d) { d.depth === 1 ? dist = radius / 3* d.depth : dist = radius / 3 * d.depth; return dist; })
        .outerRadius(function(d) { return radius / 3 * (d.depth + 1) - 1; });

    var root = in_data;

    partition
        .value(function(d) { return d.size; })
        .nodes(root)
        .forEach(function(d) {
            d._children = d.children;
            d.sum = d.value;
            d.key = key(d);
            d.fill = fill(d);
        });

    // Now redefine the value function to use the previously-computed sum.
    partition
        .children(function(d, depth) { return depth < 2 ? d._children : null; })
        .value(function(d) { return d.sum; });

    var center = svg.append("circle")
        .attr("r", radius / 3)
        .on("click", zoomOut);

    center
        .append("title")
        .text("zoom out");

    svg
        .append("g")
        .attr('class','dia');

    var path = svg.select(".dia").selectAll("path")
        .data(partition.nodes(root).slice(1))
        .enter().append("path")
        .attr("d", arc)
        .style("fill", function(d) { return d.fill; })
        .each(function(d) { this._current = updateArc(d); })
        .on("mouseover",update_legend)
        .on("mouseout",remove_legend)
        .on("click", zoomIn);

    svg
        .append("g")
        .attr("class", "labelst");

    svg
        .append("g")
        .attr("class", "labels");
    svg
        .append("g")
        .attr("class", "lines");
    svg
        .append("g")
        .attr("class", "center");
    svg
        .append("g")
        .attr("class", "innerlines");

    var lineFunction = d3.svg.line()
        .x(function(d) { return d.x; })
        .y(function(d) { return d.y; })
        .interpolate("basis");

    var addOuterLabelLines = function(data) {
        svg.select(".lines").selectAll("path")
        .remove();
        svg.select(".lines").selectAll("path")
        .data(data)
        .enter()
        .append("path")
        .filter(function(d) { return ((d.sum/d.parent.sum)*100) > 10 })
        .filter(function(d) { return ((d.sum/d.parent.parent.sum)*100) >=5 ;})
        .attr("d", function(d) {
            centr = arc.centroid(d);
            ocentr = arc.centroid(d);
            midAngle = Math.atan2(ocentr[1], ocentr[0]);
            ocentr[0] = Math.cos(midAngle) * radius*1.2;
            ocentr[1] = Math.sin(midAngle) * radius*1.2;
            centr[1] = ocentr[1];
            centr[0] < 0 ? centr[0] = ocentr[0]-20 : centr[0] = ocentr[0] + 20;
            return lineFunction([
                {
                    'x': arc.centroid(d)[0],
                    'y': arc.centroid(d)[1]
                },
                {
                    'x': ocentr[0],
                    'y': ocentr[1]
                },
                {
                    'x': centr[0],
                    'y': centr[1]
                }
            ]);
        })
    };

    var addInnerLabelLines = function(data) {
        svg.select(".innerlines").selectAll("path")
        .remove();
        svg.select(".innerlines").selectAll("path")
        .data(data)
        .enter()
        .append("path")
        .attr("d", function(d) {
            offset = arc.centroid(d);
            offset[1] = offset[1] + 15;
            centr = arc.centroid(d);
            ocentr = arc.centroid(d);
            midAngle = Math.atan2(offset[1], ocentr[0]);
            ocentr[0] = Math.cos(midAngle) * radius*1.2;
            ocentr[1] = Math.sin(midAngle+.28) * radius*1.2;
            centr[1] = ocentr[1];
            centr[0] < 0 ? centr[0] = ocentr[0]-25 : centr[0] = ocentr[0]+25 ;
            return lineFunction([
                {
                    'x': offset[0],
                    'y': offset[1]
                },
                {
                    'x': ocentr[0],
                    'y': ocentr[1]
                },
                {
                    'x': centr[0],
                    'y': centr[1]
                }
            ]);
        })
        .attr("id", function(d) {return(d.name)})
    };

    var addOuterLabel = function(data) {
        svg.select(".labelst").selectAll("text")
        .remove();
        points = [];
        svg.select(".labelst").selectAll("text")
        .data(data)
        .enter().append("text")
        .attr("text-anchor", "left")
        .attr("class", function(d){
            if (d.depth < 2) {
                return 'cat'
            }
            return 'sec'
        })
        .text(function(d) {
            if (((d.sum/d.parent.sum)*100) > 10 ) {
                if (d.depth>=2) {
                    if (((d.sum/d.parent.parent.sum)*100) >= 5) {
                        return d.name;
                    }
            } else {
                    return d.name;
            }
        }
        })
        .style("font-weight", function(d) {
            if (d.depth === 1) {
                return 'bold';
            }})
        .attr("transform", function(d) {
            centr = arc.centroid(d);
            ocentr = arc.centroid(d);
            if (d.depth >= 2) {
                midAngle = Math.atan2(ocentr[1], ocentr[0]);
                ocentr[1] = Math.sin(midAngle) * radius*1.2;
            } else {
                offset = arc.centroid(d);
                offset[1] = offset[1]+15;
                midAngle = Math.atan2(offset[1], ocentr[0]);
                ocentr[1] = Math.sin(midAngle+.28) * radius*1.2;
            }
            ocentr[0] = Math.cos(midAngle) * radius*1.2;
            centr[1] = ocentr[1]+4;
            textLength = this.getComputedTextLength();
            if (d.depth === 1) {
                centr[0] < 0 ? centr[0] = ocentr[0]-textLength-26 : centr[0] = ocentr[0] + 26;
            }
            else {
                centr[0] < 0 ? centr[0] = ocentr[0]-textLength-21 : centr[0] = ocentr[0] +21;
            }
            return "translate(" + centr + ")";
        })
        .on("mouseover",update_legend)
        .on("mouseout",remove_legend)
        .on("click", zoomIn);

    };

    var addPercentages = function (data) {
        svg.select(".labels").selectAll("text")
            .remove();
        svg.select(".labels").selectAll("text")
        .data(data)
        .enter().append("text")
        .attr("text-anchor", "left")
        .text(function(d) {
            if (((d.sum/d.parent.sum)*100) > 10 ) {
                if (d.depth>=2) {
                    if (((d.sum/d.parent.parent.sum)*100) > 5) {
                        return ((d.sum/d.parent.parent.sum)*100).toFixed(1)+'%';
                    }
                } else {
                    return ((d.sum/d.parent.sum)*100).toFixed(1) +'%';
                }
            }
        })
        .attr("transform", function(d) {
            centr = arc.centroid(d);
            textLength = this.getComputedTextLength();
            if (d.depth>=2) {
                ocentr = arc.centroid(d);
                midAngle = Math.atan2(ocentr[1], ocentr[0]);
                ocentr[0] = Math.cos(midAngle) * radius*1.2;
                ocentr[1] = Math.sin(midAngle) * radius*1.2;
                centr[1] = ocentr[1]+18;
                centr[0] < 0 ? centr[0] = ocentr[0]-21-textLength : centr[0] = ocentr[0]+21 ;
            } else {
                centr[0] = centr[0]-(textLength/2);
                centr[1] = centr[1];
            }
            return "translate(" + centr + ")";
        })
        .on("mouseover",update_legend)
        .on("mouseout",remove_legend)
        .on("click", zoomIn);
    };

    addOuterLabelLines(partition.nodes(root).slice(1)
            .filter(function(d){return d.depth >= 2}));
    addInnerLabelLines(partition.nodes(root).slice(1)
            .filter(function(d){return ((d.sum/d.parent.sum)*100) > 10})
            .filter(function(d){return d.depth === 1}));
    addOuterLabel(partition.nodes(root).slice(1));
    addPercentages(partition.nodes(root).slice(1));


    function zoomIn(p) {
        if (p.depth > 1) p = p.parent;
            if (!p.children) return;
            zoom(p, p);
    }

    function zoomOut(p) {
        if (!p.parent) return;
            zoom(p.parent, p);
    }

    // Zoom to the specified new root.
    function zoom(root, p) {
        if (document.documentElement.__transition__) return;


        // Rescale outside angles to match the new layout.
        var enterArc,
            exitArc,
            outsideAngle = d3.scale.linear().domain([0, 2 * Math.PI]);

        function insideArc(d) {
            return p.key > d.key
                ? {depth: d.depth - 1 , x: 0, dx: 0} : p.key < d.key
                ? {depth: d.depth - 1, x: 2 * Math.PI, dx: 0}
                : {depth: 0, x: 0, dx: 2 * Math.PI};
        }

        function outsideArc(d) {
            return {depth: d.depth + 1, x: outsideAngle(d.x), dx: outsideAngle(d.x + d.dx) - outsideAngle(d.x)};
        }

        center.datum(root);

        // When zooming in, arcs enter from the outside and exit to the inside.
        // Entering outside arcs start from the old layout.
        if (root === p) enterArc = outsideArc, exitArc = insideArc, outsideAngle.range([p.x, p.x + p.dx]);

        path = path.data(partition.nodes(root).slice(1), function(d) { return d.key; });

        // When zooming out, arcs enter from the inside and exit to the outside.
        // Exiting outside arcs transition to the new layout.
        if (root !== p) enterArc = insideArc, exitArc = outsideArc, outsideAngle.range([p.x, p.x + p.dx]);

        addOuterLabelLines(partition.nodes(root).slice(1)
                .filter(function(d){return d.depth >= 2}));
        addInnerLabelLines(partition.nodes(root).slice(1)
                .filter(function(d){return ((d.sum/d.parent.sum)*100) > 10})
                .filter(function(d){return d.depth === 1}));

        var textp = svg.select(".center").selectAll("text")
            .data([root]);

        textp.enter().append("text")
            .attr("text-anchor", "middle")
            .text(function(d) {
                    return d.name;
            });

        textp.exit().remove();
        textp.transition().style("fill-opacity", 1)
            .attr("text-anchor", "middle")
            .text(function(d) {
                    return d.name;
            });

        addOuterLabel(partition.nodes(root).slice(1));
        addPercentages(partition.nodes(root).slice(1));

        d3.transition().duration(d3.event.altKey ? 7500 : 750).each(function() {
            path.exit().transition()
                .style("fill-opacity", function(d) { return d.depth === 1 + (root === p) ? 1 : 0; })
                .attrTween("d", function(d) { return arcTween.call(this, exitArc(d)); })
                .remove();

            path.enter().append("path")
                .style("fill-opacity", function(d) { return d.depth === 2 - (root === p) ? 1 : 0; })
                .style("fill", function(d) { return d.fill; })
                .on("mouseover",update_legend)
                .on("mouseout",remove_legend)
                .on("click", zoomIn)
                .each(function(d) { this._current = enterArc(d); });

            path.transition()
                .style("fill-opacity", 1)
                .attrTween("d", function(d) { return arcTween.call(this, updateArc(d)); });
        });
        fixOverlap();
    }

    function fixOverlap() {
        console.log('Fixing overlapping labels');

        svg.select(".labelst").selectAll("text.sec").each(function(d1, i1) {

            var thisbb0 = this.getBoundingClientRect();
            var overlapper = this;

            if (this.textContent !== '') {
                svg.select(".labelst").selectAll("text.cat").each(function(d, i) {
                    if (overlapper !== this){
                var thisbb1 = this.getBoundingClientRect();
                var  p1x = thisbb0.left,
                     p1y = thisbb0.top,
                     p2x = thisbb0.right,
                     p2y = thisbb0.bottom,

                     p3x = thisbb1.left,
                     p3y = thisbb1.top,
                     p4x = thisbb1.right,
                     p4y = thisbb1.bottom;

                x_overlap = Math.max(0, Math.min(p2x,p4x) - Math.max(p1x,p3x));
                y_overlap = Math.max(0, Math.min(p2y,p4y) - Math.max(p1y,p3y));

                if ( this.textContent !== '' && x_overlap !== 0 && y_overlap !==0 ) {
                    var correction =0;
                    if (p2y > p3y && p4y >= p2y) {
                        correction  = thisbb0.height-y_overlap+thisbb1.height;
                    } else {
                        correction = y_overlap;
                    }
                    console.log("Overlap with minor label, moving: "+ this.textContent);
                    cords = d3.transform(d3.select(this).attr("transform")).translate;
                    console.log(cords);
                    if (cords[0] < 0) {
                        cords[0] = cords[0];
                    } else {
                        cords[0] = cords[0];
                    }
                    if (cords[1] < 0) {
                        cords[1] = cords[1]-correction;
                    } else {
                        cords[1] = cords[1]+correction;
                    }

                    d3.select(this).attr("transform",
                       "translate(" + (cords[0]) + "," +
                        (cords[1]) + ")");

                    var cw = d3.select(this)[0][0].clientWidth;

                    d3.select("path#"+ this.textContent)
                        .transition().attr("d", function(d){
                            centr = arc.centroid(d);
                            ocentr = arc.centroid(d);
                            midAngle = Math.atan2(ocentr[1], ocentr[0]);
                            ocentr[0] = Math.cos(midAngle) * radius*1.2;
                            ocentr[1] = Math.sin(midAngle) * radius*1.2;
                            ocentr[1] < 0 ? ocentr[1]=ocentr[1]+4: ocentr[1]= ocentr[1]-4;
                            cords[0] < 0 ? centr[0] = cords[0]+cw+1 : centr[0] = cords[0] ;
                            return lineFunction([
                                {
                                    'x': arc.centroid(d)[0],
                                    'y': arc.centroid(d)[1]
                                },
                                {
                                    'x': ocentr[0],
                                    'y': cords[1]-4
                                },
                                {
                                    'x': centr[0],
                                    'y': cords[1]-4
                                }
                            ]);
                    });
                }
              }
            });
            }
        });

        svg.select(".labels").selectAll("text").each(function(d1, i1) {

            var thisbb0 = this.getBoundingClientRect();
            var overlapper = this;

            if (this.textContent !== '') {
                svg.select(".labelst").selectAll("text.cat").each(function(d, i) {
                var thisbb1 = this.getBoundingClientRect();
                var  p1x = thisbb0.left,
                     p1y = thisbb0.top,
                     p2x = thisbb0.right,
                     p2y = thisbb0.bottom,
                     p3x = thisbb1.left,
                     p3y = thisbb1.top,
                     p4x = thisbb1.right,
                     p4y = thisbb1.bottom;

                x_overlap = Math.max(0, Math.min(p2x,p4x) - Math.max(p1x,p3x));
                y_overlap = Math.max(0, Math.min(p2y,p4y) - Math.max(p1y,p3y));

                if ( this.textContent !== '' && x_overlap !== 0 && y_overlap !==0 ) {

                    console.log("Overlap with a %, moving: "+ this.textContent);
                    cords = d3.transform(d3.select(this).attr("transform")).translate;

                    if (cords[0] < 0) {
                        cords[0] = cords[0]-x_overlap;
                    } else {
                        cords[0] = cords[0]+x_overlap;
                    }
                    if (cords[1] < 0) {
                        cords[1] = cords[1]+y_overlap;
                    } else {
                        cords[1] = cords[1]+y_overlap;
                    }
                    d3.select(this).attr("transform",
                       "translate(" + (cords[0]) + "," +
                        (cords[1]) + ")");

                    var cw = d3.select(this)[0][0].clientWidth;

                    d3.select("path#"+ this.textContent)
                        .transition().attr("d", function(d){
                            centr = arc.centroid(d);
                            ocentr = arc.centroid(d);
                            midAngle = Math.atan2(ocentr[1], ocentr[0]);
                            ocentr[0] = Math.cos(midAngle) * radius*1.2;
                            ocentr[1] = Math.sin(midAngle) * radius*1.2;
                            ocentr[1] < 0 ? ocentr[1]=ocentr[1]-4: ocentr[1]= ocentr[1]-4;
                            cords[0] < 0 ? centr[0] = cords[0]+cw+1 : centr[0] = cords[0] ;
                            return lineFunction([
                                {
                                    'x': arc.centroid(d)[0],
                                    'y': arc.centroid(d)[1]
                                },
                                {
                                    'x': ocentr[0],
                                    'y': cords[1]-4
                                },
                                {
                                    'x': centr[0],
                                    'y': cords[1]-4
                                }
                            ]);
                    });
                }
            });
            }
        });
    }

    function key(d) {
        var k = [], p = d;
        while (p.depth) k.push(p.name), p = p.parent;
        return k.reverse().join(".");
    }

    function fill(d) {
        var p = d;
        while (p.depth > 1) p = p.parent;
        var c = d3.lab(hue(p.name));
        c.l = luminance((d.sum/p.sum)*500*(1/d.depth));
        if ( p == d) {
            c.l = luminance((d.sum/p.sum)*1000);
        }
        return c;
    }

    function arcTween(b) {
        var i = d3.interpolate(this._current, b);
        this._current = i(0);
        return function(t) {
            return arc(i(t));
        };
    }

    function updateArc(d) {
        return {depth: d.depth, x: d.x, dx: d.dx};
    }

    var div = d3.select('body').insert("div")
        .attr("class", "tooltip1")
        .style("opacity", 0);

    function update_legend(d)
    {
        div.transition()
            .duration(100)
            .style("opacity", .9);
        perc =  d.depth >= 2 ? ((d.sum/d.parent.parent.sum)*100).toFixed(1) : ((d.sum/d.parent.sum)*100).toFixed(1);
        div.html(d.name + "<br/>"  + perc +"%" )
            .style("left", (d3.event.pageX) + "px")
            .style("top", (d3.event.pageY - 28) + "px");
    }

    function remove_legend(d) {
        div.transition()
            .duration(100)
            .style("opacity", 0);
    }

    fixOverlap();
    d3.select(self.frameElement).style("height", margin.top + margin.bottom + "px");
}

function basicTimeGraph(container,legend, flotr_data, extra_options) {
    var default_options = {
        title: '',
        ytitle: '',
        suffix: '',
        ticks1:
            function(y) {
                return Math.round(y);
        },
        scaling: 'linear',
        trackY: false,
        minY: 0,
        maxY: ''
    };

    if (typeof extra_options != "undefined") {
        $.extend(default_options, extra_options);
    }

    var
        data, graph, offset, i,
        options = {
            shadowSize: 0,
            xaxis : {
                mode : 'time',
            },
            selection : {
                mode : 'x',
            },
            title: default_options.title,
            yaxis: {
                min: default_options.minY,
                title : default_options.ytitle,
                tickFormatter: default_options.ticks1,
                autoscale: true,
                autoscaleMargin: 2,
                noTicks: 4,
                scaling: default_options.scaling
            },
            y2axis: {
                min: 0,
                autoscale: true,
                autoscaleMargin: 2,
                noTicks: 3,
            },
            HtmlText : false,
            grid: {
                outline: 'ws',
                minorHorizontalLines: true,
            },
            legend: {
                noColumns: 2,
                container: legend,
                labelBoxBorderColor: '#FFFFFF',
            },
            mouse : {
                track : true,
                trackAll: true,
                relative: true,
                trackY: default_options.trackY,
                trackFormatter: function(obj){
                    if (typeof obj.series !== undefined) {
                        return formatDate(new Date(Math.round(obj.x))) +
                            '<br>' +
                            obj.series.label +
                            ': '+
                            default_options.ticks1(obj.y) +
                            default_options.suffix;
                    }
                }
            },
            lines: {
                lineWidth: 1,
            },
            resolution: window.devicePixelRatio,
         };

    if ( default_options.scaling !== 'linear') {
        options.yaxis.ticks=  [10e-5, 10e-4, 10e-3, 10e-2, 10e-1, 10e0, 10e1, 10e2, 10e3, 10e4, 10e5];
        options.yaxis.min = 10e-2;
    }
    var g ='';

    // Draw graph with default options, overwriting with passed options
    function drawGraph (opts) {

        // Clone the options, so the 'options' variable always keeps intact.
        o = Flotr._.extend(Flotr._.clone(options), opts || {});

        // Return a new graph.
        g = Flotr.draw(
            container,
            flotr_data,
            o
        );

        return g;
    }

    graph = drawGraph();

    // Selection of an interval
    Flotr.EventAdapter.observe(container, 'flotr:select', function(area){

        // Prevent too small selection interval (20 mins)
        if (area.x2-area.x1< 1200000) {
            area.x1 = area.x2 - 1200000;
        }

        // Get maximum of max two series, only process selected interval
        function result_max() {
            result_a = flotr_data[0].data.filter(isBigEnough(area.x1)).filter(isSmallEnough(area.x2)).reduce(
                function(max, arr) {
                    return max >= arr[1] ? max : arr[1];
            }, -Infinity);

            if (typeof flotr_data[1] !== "undefined") {
                if ( typeof flotr_data[1].yaxis === "undefined") {
                    result_b = flotr_data[1].data.filter(isBigEnough(area.x1)).filter(isSmallEnough(area.x2)).reduce(function(max, arr) {
                    return max >= arr[1] ? max : arr[1];
                    }, -Infinity);
                    if (result_b > result_a ){
                        return result_b;
                    }
                }
            }
            return result_a;
        }

        op = {
            xaxis : {
                min : area.x1,
                max : area.x2,
                mode : 'time'
            },
            yaxis : {
                title : default_options.ytitle,
                min   : 0,
                max   : result_max()*1.2,
                tickFormatter: default_options.ticks1,
                scaling: default_options.scaling
            },
        };

        if ( default_options.scaling !== 'linear') {
            op.yaxis.min = 10e-2;
            //op.yaxis.max = op.yaxis.max*10;
            //op.yaxis.ticks = [10e-5, 10e-4, 10e-3, 10e-2, 10e-1, 10e0, 10e1, 10e2, 10e3, 10e4, 10e5];
        }
        if (typeof flotr_data[1] !== "undefined") {
            if ( typeof flotr_data[1].yaxis !== "undefined") {
                result_b = flotr_data[1]['data'].filter(isBigEnough(area.x1)).filter(isSmallEnough(area.x2)).reduce(function(max, arr) {
                return max >= arr[1] ? max : arr[1];
                }, -Infinity);
                op.y2axis = {min :0, max: result_b*1.2};
            }
        }
        graph = drawGraph(op);
    });

    // Reset graph
    Flotr.EventAdapter.observe(container, 'flotr:click', function () {

        new_opts ={
            yaxis : {
                min: 0,
                title:extra_options.ytitle,
                tickFormatter: default_options.ticks1,
                autoscaleMargin: 2,
                autoscale: true,
                noTicks: 3,
                scaling: default_options.scaling

            },
        };
        if ( default_options.scaling !== 'linear') {
            new_opts.yaxis.min = 10e-2;
            new_opts.yaxis.ticks = [10e-5, 10e-4, 10e-3, 10e-2, 10e-1, 10e0, 10e1, 10e2, 10e3, 10e4, 10e5];
        }
        graph = drawGraph(new_opts);
    });

    $(window).resize(function() {
        new_opts ={
            yaxis : {
                min: 0,
                title:extra_options.ytitle,
                tickFormatter: default_options.ticks1,
                autoscaleMargin: 2,
                autoscale: true,
                noTicks: 3,
                scaling: default_options.scaling

            },
        };
        if ( default_options.scaling !== 'linear') {
            new_opts.yaxis.min = 10e-2;
            new_opts.yaxis.ticks = [10e-5, 10e-4, 10e-3, 10e-2, 10e-1, 10e0, 10e1, 10e2, 10e3, 10e4, 10e5];
        }
        graph = drawGraph(new_opts);

    });
}

!function ($) {

  $(function(){

    jQuery.fn.exists = function(){
      return this.length>0;
    };

    if ( $('#timepicker1').exists()) {
      $('#timepicker1').timepicker();
    }

    if ( $('#timepicker2').exists()) {
      $('#timepicker2').timepicker();
    }

    if ( $('#reportrange').exists()) {
        picker = $('#reportrange').daterangepicker({
            ranges: {
                 'Today': [moment(), moment()],
                 'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                 'Last 7 Days': [moment().subtract('days', 6), moment().endOf('day')],
                 'Last 30 Days': [moment().subtract('days', 29), moment().endOf('day')],
                 'This Month': [moment().startOf('month'), moment().endOf('month')],
                 'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            timePicker: true, timePickerIncrement: 30,
            format: 'YYYY-MM-DD HH:mm '
        },
        function(start,end) {
            var url = window.location.href;
            if (url.indexOf('?') > -1){
                url = url.substring(0,url.indexOf('?'));
            }
            url += '?start_date='+ encodeURIComponent(start.format('YYYY-MM-DD HH:mm'))+"&stop_date="+ encodeURIComponent(end.format('YYYY-MM-DD HH:mm'));
            window.location.href = url;
        });
        if ( typeof stop_date_set !== "undefined") {
            picker.data('daterangepicker').setEndDate(stop_date_set);
        }
        if ( typeof start_date_set !== "undefined") {
            picker.data('daterangepicker').setStartDate(start_date_set);
        }
    }
    if ( $('#begin_date').exists()) {
      $('#begin_date').datepicker();
    }

    if ( $('#thorMap').exists()) {

      var draw = SVG('thorMap');

      draw.viewbox(0, 0, thorData.imgsize, thorData.imgsize);

      // draw.line(0,315,630,315).stroke({ width: 1, color:'grey'});
      // draw.line(315,0,315,630).stroke({ width: 1, color:'grey'});

      draw.circle()
        .radius(315*0.6)
        .fill('none')
        .stroke({
          width: 10,
          color:'#ebebeb'
        })
        .cy(315)
        .cx(315);

      cloud = draw.text("\uf0c2")
        .font({
          family: 'FontAwesome',
          size: '100'
        })
        .y(265)
        .x(265)
        .fill('#bbb');

      var dns_nodes;

      if (typeof thorData.dns_managers != "undefined") {
          dns_nodes = Object.keys(thorData.dns_managers).length;
          index = 0 ;
          //console.log(thorData);
          $.each(thorData.dns_managers, function( key, value ) {

                // draw.text("\ue9a4").font({
                //     family: 'picol',
                //     size: '40'
                // })
                // .cy(20)
                // .cx(610-(40*dns_nodes)+(index*45))
                // .fill('#88a2d2');


                // draw.text("DNS"+(index+1)).font({
                //     family: 'Verdana',
                //     size: '10'
                // })
                // .cy(50)
                // .cx(610-(40*dns_nodes)+(index*45));

            draw.circle()
                .radius(4)
                .fill('green')
                .cx(490)
                .cy(21+(index*12));

            var hostname = key;

            if (thorData.hostnames[key]){
              hostname = thorData.hostnames[key];
            }

            draw.text("DNS"+(index+1)+': '+hostname)
              .font({
                family: 'Verdana',
                size: '10'
              })
              .cy(20+(index*12))
              .x(500);
            index++;
          });
      }

      sip_proxies=0;

      if (typeof thorData.node_statistics != "undefined" && !$.isEmptyObject(thorData.node_statistics)) {
          sip_proxies = sip_proxies + Object.keys(thorData.node_statistics).length;
      }

      if (typeof thorData.conference_servers != "undefined" && !$.isEmptyObject(thorData.conference_servers)) {
          sip_proxies = sip_proxies+ Object.keys(thorData.conference_servers).length;
      }

      if (typeof thorData.voicemail_servers != "undefined" && !$.isEmptyObject(thorData.voicemail_servers)) {
          sip_proxies = sip_proxies+ Object.keys(thorData.voicemail_servers).length;
      }

      //var sip_proxies = Object.keys(thorData.node_statistics).length + Object.keys(thorData.conference_servers).length + Object.keys(thorData.voicemail_servers).length;

        //var sip_proxies = Object.keys(thorData.node_statistics).length
      var counter= 0;

    if (typeof thorData.voicemail_servers != "undefined" && !$.isEmptyObject(thorData.voicemail_servers)) {
      $.each(thorData.voicemail_servers, function( key, value ) {
        position = ((2/sip_proxies)*counter);
        counter++;

        //   draw.circle()
        //     .radius(10)
        //     .fill('white')
        //     .stroke({color:'green', width:2})
        //     .cx(315+(315*0.6)*Math.sin(position*Math.PI))
        //     .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        draw.rect(32,24)
          .fill('white')
          .stroke({
            color:'green',
            width:2
          })
          .radius(5)
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        draw.text("\uf0e0")
          .font({
            family: 'FontAwesome',
            size: '14',
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        var hostname = key;

        if (thorData.hostnames[key]){
          hostname = thorData.hostnames[key];
        }

        align='start';
        x=0;
        y=0;
        if (position  === 0.5 ) {
          x= -20;
          align='start';
        } else if (position === 1.5) {
          x= 20;
          align='end';
        } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
          x = 25;
          align='end';
        } else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
          x = -25;
        } else if (position === 1 ) {
          align='middle';
          y=20;
        } else if (position === 0 ) {
          align='middle';
          y=-25;
        }

        draw.text(hostname ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).x(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

        });
        }
        if (typeof thorData.conference_servers != "undefined" && !$.isEmptyObject(thorData.conference_servers)) {
        $.each(thorData.conference_servers, function( key, value ) {
          position = ((2/sip_proxies)*counter);
          counter++;

          // draw.circle()
          //   .radius(10)
          //   .fill('white')
          //   .stroke({color:'green', width:2})
          //   .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          //   .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          draw.rect(32,24).fill('white').stroke({color:'green', width:2}).radius(5)
          .x(315).y(315).animate(500, SVG.easing.quartIn).cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          draw.text("\uf0c0").font({
            family: 'FontAwesome',
            size: '15',
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          var hostname = key;

          if (thorData.hostnames[key]){
            hostname = thorData.hostnames[key];
          }

        align='start';
        x=0;
        y=0;
         if (position  === 0.5 ) {
            x= -20;
            align='start';
          } else if (position === 1.5) {
            x= 20;
            align='end';
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            x = 25;
            align='end';
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            x = -25;
          }
          else if (position === 1 ) {
            align='middle';
            y=20;
          } else if (position === 0 ) {
            align='middle';
            y=-20;
          }

          draw.text(hostname ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y)
          .x(315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

        });
        }
if (typeof thorData.node_statistics != "undefined" && !$.isEmptyObject(thorData.node_statistics)) {
        $.each(thorData.node_statistics, function( key, value ) {
          position = ((2/sip_proxies)*counter);
          counter++;

          // draw.text("\ue9a4").font({
          //   family: 'picol',
          //   size: '40'
          // }).y(290+(315*0.69)*Math.sin(position*Math.PI)).x(
          //     300+(315*0.69)*Math.cos(position*Math.PI)).fill('#5177bd');

          draw.rect(32,24).fill('white').stroke({color:'green', width:2}).radius(4)
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          // draw.circle()
          //   .radius(10)
          //   .fill('white')
          //   .stroke({color:'green', width:2})
          //   .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          //   .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        if (thorData.sip_proxies[key]){
            draw.text("\uf0b2").font({
            family: 'FontAwesome',
            size: '15',
          })
              .x(315).y(315).animate(500, SVG.easing.quartIn)
            .cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));
          }
        x =0 ;
        y = 0;
          // if (position > 0 && position < 0.5) {
          //   x = -45;
          //   y = -20;
          // } else if (position === 0.5) {
          //   x = 0;
          //   y = -55;
          // } else if (position > 0.5 && position < 1) {
          //   x = 120;
          //   y = -15;
          // } else if (position === 1) {
          //   x = 30;
          //   y = 15;
          // } else if (position > 1 && position < 1.5) {
          //   x = 120;
          //   y = -20;
          // } else if (position > 1.5 && position < 2) {
          //   x = -45;
          //   y = -20;
          // } else {
          //   x = 0;
          //   y = 15;
          // }

          var accounts = '';
          if (thorData.node_statistics[key].online_accounts ){
            accounts= thorData.node_statistics[key].online_accounts+' accounts';
          }

          var hostname = key;

          if (thorData.hostnames[key]){
            hostname = thorData.hostnames[key];
          }

        align='start';
         if (position  === 0.5 ) {
            x= -20;
            align='start';
          } else if (position === 1.5) {
            x= 20;
            align='end';
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            x = 25;
            align='end';
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            x = -25;
          }
          else if (position === 1 ) {
            align='middle';
            y=30;
          } else if (position === 0 ) {
            align='middle';
            y=-30;
          }

          draw.text(hostname+"\n"+accounts ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          }).x(315).y(315).animate(500, SVG.easing.quartIn).cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).x(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

        align='start';
        x =0 ;
        y = 0;
          if (position === 0.5) {
            align='end';
          }else if (position === 1.5 ) {
            align='start';
            x= -50;
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            align='start';
            x= -50;
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            align='end';
          }
          else if (position === 1 ) {
            //align='middle';
            y=-24;
          } else if (position === 0 ) {
            //align='middle';
            y=25;
          }

          if (thorData.node_statistics[key].sessions) {
            draw.text(thorData.node_statistics[key].sessions +' sessions').font({
            family: 'Verdana',
            size: '10',
            anchor: align,
          }).x(315).y(315).animate(400, SVG.easing.quartIn)
            .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).cx(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');
          }
        });
    }
    }

    if ( $('#end_date').exists()) {
        $('#end_date').datepicker();
    }
    $('tr[rel=tooltip]').tooltip();

    $('.tooltip-test').tooltip();
    $('.popover-test').popover();

    // popover demo
    $("select[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

    $("a[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });


   $("button[rel=popover]")
      .popover();

    $("input[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

    $("textarea[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

   if ($('fileupload').exists()){
        $('fileupload').fileupload();
   }

    // if ( $('#download_password').exists()) {
    //     $('#download_password').click(function(){
    //         var password = $('#password').val();
    //         var ha1 = $('#ha1').val();
    //         var ha1b = $('#ha1b').val();
    //         var username = $('#username').val();
    //         var domain = $('#domain').val();
    //         var str = username + ":"+
    //             domain + ":" +
    //             password;

    //         var ha_calc = MD5(str);

    //         if (ha1 === ha_calc) {
    //             $('#java_buttons').removeClass('hide');
    //             $('#password_download').addClass('hide');
    //             var content = decodeURIComponent($('[name=file_content]').val());
    //             var obj= $.parseJSON(content);
    //             obj.password=password;
    //             var new_content= JSON.stringify(obj);
    //             new_content= encodeURIComponent(new_content);
    //             $('[name=file_content]').val(new_content);
    //         } else {
    //             $('#pass_group').addClass('error');
    //             $('#help-text').remove();
    //             $('#controls_password').append('<span id="help-text" class="help-inline">Entered password does not match your account</span>');
    //         }

    //         return false;
    //     });
    // }

    // request built javascript
    $('.download-btn').on('click', function () {

      var css = $("#components.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , js = $("#plugins.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , vars = {}
        , img = ['glyphicons-halflings.png', 'glyphicons-halflings-white.png']

    $("#variables.download input")
      .each(function () {
        $(this).val() && (vars[ $(this).prev().text() ] = $(this).val())
      })

      $.ajax({
        type: 'POST'
      , url: /\?dev/.test(window.location) ? 'http://localhost:3000' : 'http://bootstrap.herokuapp.com'
      , dataType: 'jsonpi'
      , params: {
          js: js
        , css: css
        , vars: vars
        , img: img
      }
      })
    })
  });

// Modified from the original jsonpi https://github.com/benvinegar/jquery-jsonpi
$.ajaxTransport('jsonpi', function(opts, originalOptions, jqXHR) {
  var url = opts.url;

  return {
    send: function(_, completeCallback) {
      var name = 'jQuery_iframe_' + jQuery.now()
        , iframe, form

      iframe = $('<iframe>')
        .attr('name', name)
        .appendTo('head')

      form = $('<form>')
        .attr('method', opts.type) // GET or POST
        .attr('action', url)
        .attr('target', name)

      $.each(opts.params, function(k, v) {

        $('<input>')
          .attr('type', 'hidden')
          .attr('name', k)
          .attr('value', typeof v == 'string' ? v : JSON.stringify(v))
          .appendTo(form)
      })

      form.appendTo('body').submit()
    }
  }
});


}(window.jQuery)
