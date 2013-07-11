<?
/*
    Copyright (c) 2013 AG Projects
    http://ag-projects.com
    Author Tijmen de Mes
*/

class ProvisioningStatistics {
    // obtain statistics from Database for NGNPro

    function getTopRequestsProvisioning($class) {
        global $CDRTool;

        $number_of_requests=0;
        $requests=array();
        $requests_ip=array();
        $temp=array();

        if (!class_exists($class)) return array();

        $db = new $class();
        $start = (float) array_sum(explode(' ',microtime()));
        $query = "select substring_index(function,':',1) as port, total as number from ngnpro_logs_new_functions group by port order by number desc limit 0, 5";
        dprint($query);

        if (!$db->query($query))  {
            $log = sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return array();
        }

        if (!$db->num_rows()) return array();

        $requests['total'] = 0;
        while ($db->next_record()) {
            $temp[$db->f('port')] = intval($db->f('number'));
            $requests['total'] = intval($db->f('number')) + $requests['total'];
        }

        foreach($temp as $key=> $value) {
            $query = "select total as number, function, substring_index(function,':',1) as port,substring_index(function,':',-1) as method from ngnpro_logs_new_functions where function like '$key:%' group by function order by number desc limit 0,5 ";
            dprint("$query");

            if (!$db->query($query))  {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return array();
            }

            if (!$db->num_rows()) return array();
            while ($db->next_record()) {
                 $requests[$db->f('port')][$db->f('method')] = intval($db->f('number'));
                 $requests[$db->f('port')]['total'] = $requests[$db->f('port')]['total'] + intval($db->f('number'));
            }

        }
        $end = (float) array_sum(explode(' ',microtime()));
        dprint("Processing time: ". sprintf("%.4f", ($end-$start))." seconds<br>");

        $start = (float) array_sum(explode(' ',microtime()));
        $query ="select total as number, function, ip from ngnpro_logs_new_functions group by ip,function order by number desc";
        dprint("$query");

        if (!$db->query($query))  {
            $log = sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return array();
        }

        if (!$db->num_rows()) return array();
        while ($db->next_record()) {
            list($port,$method) = explode(":", $db->f('function'));
            $requests_ip[$port][$method][$db->f('ip')] = intval($db->f('number'));
        }

        $end = (float) array_sum(explode(' ',microtime()));
        dprint("Processing time for getTopRequestsProvisioningNew: ". sprintf("%.4f", ($end-$start))." seconds");
        return array($requests,$requests_ip);
    }

    function getPeriod($class) {
        global $CDRTool;

        $temp = array();

        if (!class_exists($class)) return array();

        $db = new $class();

        $query = "select MIN(date) as min_date,MAX(date) as max_date, sum(total) as total from ngnpro_logs_new";
        dprint($query);

        if (!$db->query($query))  {
            $log = sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return array();
        }

        if (!$db->num_rows()) return array();

        $requests['total']=0;
        while ($db->next_record()) {
            $temp = array($db->f('min_date'),$db->f('max_date'),$db->f('total'));
        }

        return $temp;
    }

    function getCategories($requests) {
        $total = $requests['total'];
        $port_data= array();
        $colors = array(
               '#2f7ed8', '#0d233a', '#8bbc21',
               '#910000', '#1aadce', '#492970','#f28f43',
               '#77a1e5', '#c42525', '#a6c96a'
           );

        $num=0;
        foreach($requests as $key => $value) {
            if ($key != 'total'){
                $port_data[] = array(
                    "name"  => $key,
                    "y"     => round(($requests[$key]['total']/$total)*100,2),
                    "color" => $colors[$num],
                    "id"    => "$key"
                );
                $num++;
            }
        }

        $return=json_encode($port_data);
        return $return;
    }


    function getSecondCategories($requests) {
        $total = $requests['total'];
        $method_data= array();

        foreach($requests as $key => $value) {
             if ($key != 'total'){
                foreach($value as $key1 => $value1) {
                    if ($key1 != 'total'){
                        $method_data[$key][] = array(
                            "name"     => $key1,
                            "y"        => round(($value[$key1]/$total)*100,2),
                            "parentId" => "$key",
                            "value"    => "$value[$key1]");
                    }
                 }
            }
        }

        dprint("<pre>");
        dprint_r($method_data);
        dprint("</pre>");

        $return = json_encode($method_data);
        return $return;
    }

    function printChartDonut($titlex,$titley,$num,$categories, $second_categories,$requests_ip) {
        // Create the chart

        $requests_ip=json_encode($requests_ip);

        $chart = "
            <script type=\"text/javascript\">
                $(function () {

                    var colors = Highcharts.getOptions().colors,

                    methods = [$second_categories];
                    ports   = $categories;
                    var new_methods = [];

                    total_data = $requests_ip;

                    // console.log(total_data['Sip']);

                    for (var j = 0; j < methods.length; j++) {
                        // console.log(methods[j]);

                        num=0;

                        $.each( methods[j], function( key, value ) {
                            // console.log( value[0] );
                            for (var i = 0; i < value.length; i++) {
                                var brightness = 0.2 - (i / value.length) / 5 ;

                                name = value[i].name;
                                new_methods.push ({
                                    name     : name,
                                    y        : value[i].y,
                                    color    : Highcharts.Color(ports[num].color).brighten(brightness).get(),
                                    parentId : value[i].parentId,
                                });

                            }
                            num++;
                        });

                    }

                    new_methods.sort();
                    $('#sub_container$num').fadeIn();
                    renderSubPie(new_methods[0].parentId+':'+new_methods[0].name+' by IP', total_data[new_methods[0].parentId][new_methods[0].name]);

                    // console.log(new_methods);

                    $('#container$num').highcharts({
                        chart : {
                            type   : 'pie',
                            height : 350,
                        },
                        title : {
                            text : '$titlex',
                        },
                        plotOptions : {
                            pie : {
                                allowPointSelect : true,
                                shadow           : false,
                                center           : ['50%', '50%']
                            },
                            series : {
                                cursor : 'pointer',
                                point  : {
                                    events : {
                                        click : function() {
                                            // console.log(this.selected);

                                            if (total_data[this.parentId] !== undefined) {
                                                if (!this.selected){
                                                    $('#sub_container$num').fadeIn();
                                                    // console.log(total_data[this.parentId][this.name]);
                                                    renderSubPie(this.parentId+':'+this.name+' by IP', total_data[this.parentId][this.name]);
                                                } else {
                                                    $('#sub_container$num').fadeOut();
                                                }
                                            } else {
                                                $('#sub_container$num').fadeOut();
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        tooltip : {
                            valueSuffix : '%'
                        },
                        credits : {
                          enabled : false
                        },
                        series: [{
                            name : 'Requests',
                            data : $categories,
                            size : '60%',
                            dataLabels : {
                                formatter : function() {
                                    return this.y > 12 ? this.point.name : null;
                                },
                                color    : 'white',
                                distance : -50
                            }
                        },{
                            name : 'Function',
                            data : new_methods,
                            size : '80%',
                            innerSize  : '60%',
                            dataLabels : {
                                formatter : function() {
                                    // display only if larger than 3
                                    return this.y > 3 ? '<b>'+ this.point.name +'</b><br />'+ this.y +'%' : null;
                                }
                            }
                        }],
                    });

                    function renderSubPie(title,data) {
                        var new_data = [];
                        var total    = 0;
                        $.each( data, function( key, value ) {
                            total = total + value;
                        });

                        $.each( data, function( key, value ) {
                            val = (value/total)*100;
                            new_data.push ({
                                name : key,
                                y    : val,
                            });
                        });

                        $('#sub_container$num').highcharts({
                            chart : {
                                type   : 'pie',
                                height : 350,
                            },
                            title : {
                                text : title,
                            },
                            subtitle : {
                                text : total + ' requests',
                            },
                            plotOptions : {
                                pie : {
                                    shadow : false,
                                    center : ['50%', '50%']
                                },
                            },
                            tooltip : {
                                formatter : function() {
                                    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                                 }
                            },
                            credits : {
                              enabled : false
                            },
                            series : [{
                                name : 'Requests',
                                data : new_data,
                                size : '80%',
                                dataLabels : {
                                    formatter : function() {
                                        return this.y > 12 ?  '<b>'+ this.point.name +'</b><br/>'+  Math.round(this.percentage) +'%'  : null;
                                    },
                                },
                            }],
                        });
                    }
                });
        </script>

        <div id='container$num' class='span5'></div>
        <div class='span1'></div>
        <div id='sub_container$num' class='span5 pull-right' style='display:none'></div>";

        print $chart;
    }

    function getRequestsProvisioning($class,$days) {
        global $CDRTool;
        $requests = array();
        $period = '300';
        if (!class_exists($class)) return array();

        $db = new $class();

        $query = "select sum(total) as number,date from ngnpro_logs_new GROUP BY UNIX_TIMESTAMP(date) DIV 300";
        dprint($query);

        if (!$db->query($query))  {
            $log = sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return array();
        }

        if (!$db->num_rows()) return array();

        while ($db->next_record()) {
            $requests[] = array($db->f('date'),(intval($db->f('number')))/5);
        }

        return json_encode($requests);
    }

   function getRequestsTime($class, $days) {
        global $CDRTool;
        $requests = array();
        $period = '300';

        if (!class_exists($class)) return array();
        $start = (float) array_sum(explode(' ',microtime()));
        $db = new $class();

        if ($days <= 10) {
            $period='600';
        } else if ($days <= 20) {
             $period='1200';
        } else if ($days > 20) {
             $period='2400';
        }

        #$query = "select sum(total) as number, date, concat('[',group_concat(data),']') as data from ngnpro_logs_new GROUP BY UNIX_TIMESTAMP(date) DIV 60 order by date";
        $query = "select sum(total) as number, date, sum(total_time) as data from ngnpro_logs_new GROUP BY UNIX_TIMESTAMP(date) DIV 300 order by date";
        dprint($query);

        if (!$db->query($query))  {
            $log = sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return array();
        }

        if (!$db->num_rows()) return array();

        while ($db->next_record()) {
            $total= $db->f('data');
            $requests[] = array($db->f('date'),($total/intval($db->f('number')))*1000);
        }
        $end = (float) array_sum(explode(' ',microtime()));
        dprint("<br>Processing time for getRequestsTime: ". sprintf("%.4f", ($end-$start))." seconds<br>");
        // echo "<pre>";
        // print_r($requests);
        // echo "</pre>";
        return json_encode($requests);
    }

    function printChartLine($num,$requests,$requests_time) {

        $num=$num+1;
        $chart = "
            <script type=\"text/javascript\">
            $(function () {
                var requests = $requests;

                for (var j = 0; j < requests.length; j++) {
                    var t = requests[j][0].split(/[- :]/);
                    requests[j][0] = Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    //requests[j][1] = requests[j][1]/5;
                }

                var request_time = $requests_time;

                for (var j = 0; j < request_time.length; j++) {
                    var t = request_time[j][0].split(/[- :]/);
                    request_time[j][0] = Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                }
                console.log(requests);

                $('#container_line$num').highcharts({
                    chart : {
                        type     : 'spline',
                        zoomType : 'x',
                        height   : 280,
                        marginRight: 80
                    },
                    credits : {
                        enabled : false
                    },
                    title : {
                        text : 'Provisioning requests - Average execution time',
                    },
                    xAxis : {
                        type  : 'datetime',
                        title : {
                            text : null
                        },
                        minRange : 3600000,
                        endOnTick: true,
                    },
                    plotOptions : {
                        spline  : {
                            marker : {
                                enabled:  false
                            },
                            lineWidth : 1,
                            shadow    : false,
                            states    : {
                                hover : {
                                    lineWidth : 1
                                }
                            },
                            threshold : null
                        }
                    },
                    yAxis : [{
                        title : {
                            text : 'Requests'
                        },
                    },{
                        title : {
                            text : 'Execution time (ms)'
                        },
                        opposite : true
                    }],
                    series: [{
                        name: 'Requests per minute',
                        data: requests
                    }
                    ,{
                        name: 'Average execution time',
                        data: request_time,
                        color: '#8A0808',
                        yAxis: 1,
                    }
                    ],
                });
            });
            </script>
            <div  style='float:left; width: 100%;'>
            <div id='container_line$num' class='span12' style='width: 75%; margin-left: auto; display:table ;margin-right: auto; text-align:center;float:none;'></div>";

        print $chart;
    }
}

?>
