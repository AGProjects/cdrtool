<?php
/*
 * Copyright (c) 2015-2025 AG Projects
 * https://ag-projects.com
 * Author Tijmen de Mes
*/

class ProvisioningStatistics
{
    // Obtain statistics from database for NGNPro

    private function queryHasError($db, $query)
    {
        dprint_sql($query);
        if (!$db->query($query)) {
            $log = sprintf(
                "Database error for query %s: %s (%s)",
                $query,
                $db->Error,
                $db->Errno
            );
            loggerAndPrint($log);
            return true;
        }
        return false;
    }

    public function getTopRequestsProvisioning($class, $start_date, $stop_date)
    {
        global $CDRTool;

        $number_of_requests = 0;
        $requests = array();
        $requests_ip = array();
        $temp = array();

        if (!class_exists($class)) {
            return array();
        }

        $today = new DateTime(); // This object represents current date/time
        $today->setTime(23, 59, 00); // reset time part, to prevent partial comparison

        if ($stop_date == $today) {
             $new_stop_date = $stop_date->add(new DateInterval('P1D'));
        } else {
            $new_stop_date = $stop_date;
        }
        $db = new $class();
        $start = (float) array_sum(explode(' ', microtime()));

        $query = sprintf(
            "select port, sum(number) as number
            from (
                select substring_index(function,':',1) as port, sum(total) as number
                from ngnpro_logs_functions
                where
                    date between '%s' and '%s'
                group by port

                union all

                select substring_index(function,':',1) as port, sum(total) as number
                from ngnpro_logs_functions_history
                where
                    date between '%s' and '%s'
                group by port
            ) t
            group by port
            order by number desc limit 0,5",
            $start_date->format('Y-m-d 00:00:00'),
            $new_stop_date->format('Y-m-d 00:00:00'),
            $start_date->format('Y-m-d 00:00:00'),
            $new_stop_date->format('Y-m-d 00:00:00')
        );

        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if (!$db->num_rows()) {
            return array();
        }

        $requests['total'] = 0;
        while ($db->next_record()) {
            $temp[$db->f('port')] = intval($db->f('number'));
            $requests['total'] = intval($db->f('number')) + $requests['total'];
        }

        dprint_r($requests);

        foreach ($temp as $key => $value) {
            $query = sprintf(
                "select sum(number) as number, function, port, method
                from (
                    select sum(total) as number, function, substring_index(function,':',1) as port,substring_index(function,':',-1) as method
                    from ngnpro_logs_functions
                    where
                        function like '$key:%%'
                    and
                        date between '%s' and '%s'
                    group by function

                    union all

                    select sum(total) as number, function, substring_index(function,':',1) as port,substring_index(function,':',-1) as method
                    from ngnpro_logs_functions_history
                    where
                        function like '$key:%%'
                    and
                        date between '%s' and '%s'
                    group by function
                ) t
                group by function
                order by number desc limit 0,5",
                $start_date->format('Y-m-d 00:00:00'),
                $new_stop_date->format('Y-m-d 00:00:00'),
                $start_date->format('Y-m-d 00:00:00'),
                $new_stop_date->format('Y-m-d 00:00:00')
            );
            #$query = "select sum(total) as number, function, substring_index(function,':',1) as port,substring_index(function,':',-1) as method from ngnpro_logs_functions where function like '$key:%' group by function order by number desc limit 0,5 ";
            if ($this->queryHasError($db, $query)) {
                return [];
            }

            if (!$db->num_rows()) {
                return array();
            }
            $requests[$key]['total'] = 0;
            while ($db->next_record()) {
                 $requests[$db->f('port')][$db->f('method')] = intval($db->f('number'));
                 $requests[$db->f('port')]['total'] = $requests[$db->f('port')]['total'] + intval($db->f('number'));
            }
        }
        $end = (float) array_sum(explode(' ', microtime()));
        dprint("Processing time: ". sprintf("%.4f", ($end-$start))." seconds<br>");

        $start = (float) array_sum(explode(' ', microtime()));

        $query = sprintf(
            "select sum(number) as number,function, ip
            from
            (
                select total as number, function, ip
                from ngnpro_logs_functions
                where
                    date between '%s' and '%s'
                group by ip,function

                union all

                select total as number, function, ip
                from ngnpro_logs_functions_history
                where
                    date between '%s' and '%s'
                group by ip,function
            ) t
            group by ip,function",
            $start_date->format('Y-m-d 00:00:00'),
            $new_stop_date->format('Y-m-d 00:00:00'),
            $start_date->format('Y-m-d 00:00:00'),
            $new_stop_date->format('Y-m-d 00:00:00')
        );

        #$query ="select total as number, function, ip from ngnpro_logs_functions group by ip,function order by number desc";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if (!$db->num_rows()) {
            return array();
        }

        while ($db->next_record()) {
            list($port,$method) = explode(":", $db->f('function'));
            $requests_ip[$port][$method][$db->f('ip')] = intval($db->f('number'));
        }

        $end = (float) array_sum(explode(' ', microtime()));
        dprint("Processing time for getTopRequestsProvisioningNew: ". sprintf("%.4f", ($end-$start))." seconds");
        return array($requests,$requests_ip);
    }


    public function getNumber($class, $start_date, $stop_date)
    {
        global $CDRTool;

        $temp = array();

        if (!class_exists($class)) {
            return array();
        }
        $db = new $class();
        // Get total
        $query = "select sum(total) as total from ngnpro_logs where date between '". $start_date->format('Y-m-d H:i:s')."' and '".$stop_date->format('Y-m-d H:i:s')."'";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if (!$db->num_rows()) {
            return array();
        }

        while ($db->next_record()) {
            $temp = array($db->f('total'));
        }
        dprint_r($temp);

        // Also get from archived entries
        $query = "select sum(total) as total from ngnpro_logs_summary where date between '". $start_date->format('Y-m-d H:i:s')."' and '".$stop_date->format('Y-m-d H:i:s')."'";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if (!$db->num_rows()) {
            return array();
        }

        while ($db->next_record()) {
            $temp[0] = $temp[0]+$db->f('total');
        }
        return $temp;
    }

    public function getData($requests, $data1)
    {
        $port_data = array();
        $port_data['name'] = "";
        $port_data['children'] = array();
        foreach ($requests as $key => $value) {
            if ($key != 'total') {
                $children1 = array();
                foreach ($requests[$key] as $key1 => $value1) {
                    if ($key1 != 'total') {
                        $children2 = array();
                        foreach ($data1[$key][$key1] as $key2 => $value2) {
                            $children2[] = array('name'=> "$key2", 'size'=>$value2);
                        }
                        $children1[] = array(
                            "name"  => $key1,
                            "children" => $children2
                        );
                    }
                }
                $port_data['children'][]=array('name' =>$key, 'children' => $children1);
            }
        }

        $return=json_encode($port_data);
        return $return;
    }

    public function printChartDonut($titlex, $good_data)
    {
        // Create the chart
        print "<h2>$titlex</h2>";
        print "<div id='pie_provisioning'></div>";
        $chart = "
            <style>
            svg {
                display: block;
                margin: 0 auto;
            }

            circle,
            path {
                cursor: pointer;
            }

            circle {
                fill: none;
                pointer-events: all;
            }

            .lines path{
                opacity: 1;
                stroke: black;
                stroke-width: 1px;
                fill: none;
            }
            .innerlines path {
                opacity: .5;
                stroke: blue;
                stroke-width: 3px;
                fill: none;
            }

            div.tooltip1 {
                position: absolute;
                width: 100px;
                height: 28px;
                padding: 2px;
                font: 11px sans-serif;
                background-color:rgb(249, 249, 249) !important;
                color: #333333 !important;
                border: solid 1px #000000 !important;
                z-index: 200;
                border-radius: 4px;
                pointer-events: none;
            }

            </style>

            <script type=\"text/javascript\">
                $(function () {
                    all_data = $good_data
                    MultiDonut('#pie_provisioning',all_data);
                });
            </script>";

        print $chart;
    }

    private function purge($class)
    {
        global $CDRTool;
        $interval = 547;

        if (!class_exists($class)) {
            return array();
        }

        $db = new $class();

        $query = "insert into ngnpro_logs_summary (total, date,total_time) "
               . "select sum(total) as number, date, sum(total_time) as data from ngnpro_logs "
               . "where date < DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY UNIX_TIMESTAMP(date) DIV 3600 order by date";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        $query = "delete from ngnpro_logs "
                ."where date < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        $query = sprintf("delete from ngnpro_logs_summary where date < date_sub(now(), interval %u day)", $interval);
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        $query = sprintf("delete from ngnpro_logs_functions where date < date_sub(now(), interval %u day)", $interval);
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        $query = sprintf("delete from ngnpro_logs_functions_history where date < date_sub(now(), interval %u day)", $interval);
        if ($this->queryHasError($db, $query)) {
            return [];
        }
    }

    public function getRequestsProvisioning($class, $days, $start_date, $stop_date)
    {
        global $CDRTool;
        $requests = array();
        $period = '300';
        $this->purge($class);

        if (!class_exists($class)) {
            return array();
        }

        $db = new $class();

        if ($days >= 10) {
            $period='600';
        } else if ($days >= 20) {
             $period='900';
        }

        $query = "select total as number,date from ngnpro_logs_summary where date between '"
               . $start_date->format('Y-m-d H:i:s')
               . "' and '"
               . $stop_date->format('Y-m-d H:i:s')
               . "' order by date";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if ($db->num_rows()) {
            while ($db->next_record()) {
                $requests[] = array($db->f('date'),(intval($db->f('number')))/60);
            }
        }

        $query = "select sum(total) as number,date from ngnpro_logs where date between '"
               . $start_date->format('Y-m-d H:i:s')
               . "' and '"
               . $stop_date->format('Y-m-d H:i:s')
               . "' GROUP BY UNIX_TIMESTAMP(date) DIV $period";
        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if ($db->num_rows()) {
            while ($db->next_record()) {
                $requests[] = array($db->f('date'),(intval($db->f('number')))/($period/60));
            }
        }

        return json_encode($requests);
    }

    public function getRequestsTime($class, $days, $start_date, $stop_date)
    {
        global $CDRTool;
        $requests = array();
        $period = '300';

        if (!class_exists($class)) {
            return array();
        }
        $start = (float) array_sum(explode(' ', microtime()));
        $db = new $class();

        if ($days >= 10) {
            $period='600';
        } else if ($days >= 20) {
             $period='900';
        }
        //else if ($days > 20) {
          //   $period='2400';
       // }

        $query = "select total as number, date, total_time as data from ngnpro_logs_summary where date between '"
               . $start_date->format('Y-m-d H:i:s')
               . "' and '"
               . $stop_date->format('Y-m-d H:i:s')
            . "' order by date ";

        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if ($db->num_rows()) {
            while ($db->next_record()) {
                $total= $db->f('data');
                $requests[] = array($db->f('date'),($total/intval($db->f('number')))*1000);
            }
        }
        #$query = "select sum(total) as number, date, concat('[',group_concat(data),']') as data from ngnpro_logs_new GROUP BY UNIX_TIMESTAMP(date) DIV 60 order by date";
        $query = "select sum(total) as number, date, sum(total_time) as data from ngnpro_logs where date between '"
               . $start_date->format('Y-m-d H:i:s')
               . "' and '"
               . $stop_date->format('Y-m-d H:i:s')
               . "' GROUP BY UNIX_TIMESTAMP(date) DIV $period order by date";

        if ($this->queryHasError($db, $query)) {
            return [];
        }

        if ($db->num_rows()) {
            while ($db->next_record()) {
                $total= $db->f('data');
                 $requests[] = array($db->f('date'),($total/intval($db->f('number')))*1000);
            }
        }
        $end = (float) array_sum(explode(' ', microtime()));
        dprint("<br>Processing time for getRequestsTime: ". sprintf("%.4f", ($end-$start))." seconds<br>");
        // echo "<pre>";
        // print_r($requests);
        // echo "</pre>";
        return json_encode($requests);
    }

    public function printLineCharts($name, $requests, $requests_time)
    {
        $chart = "
        <style type=\"text/css\">
        .graph {
            height: 200px;
            margin: 8px auto;
        }

        .graphs table{
            margin-left: auto;
            margin-right: auto;
        }

        .flotr-mouse-value {
            background-color:rgb(249, 249, 249) !important;
            color: #333333 !important;
            opacity: 0.9 !important;
            border: solid 1px #000000 !important;
            z-index: 200;
        }

        .grid {
            padding: 10px 0;
        }

        hr + .grid {
            padding:0;
            padding-bottom: 10px;
        }

        .grid:nth-child(even) {
            background-color:#f9f9f9;
        }

        .flotr-axis-title-y1 {
            -ms-transform: rotate(-90deg); /* IE 9 */
            -webkit-transform: rotate(-90deg); /* Chrome, Safari, Opera */
            transform: rotate(-90deg);
        }
        </style>
        <script type=\"text/javascript\">
            $(function () {
                var requests = $requests;

                var temp_data = [];
                var temp_data1 = [];

                for (var j = 0; j < requests.length; j++) {
                    var t = requests[j][0].split(/[- :]/);
                    requests[j][0] = Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    temp_data[j] = [requests[j][0],requests[j][1]];
                    //requests[j][1] = requests[j][1]/5;
                }

                var request_time = $requests_time;

                for (var j = 0; j < request_time.length; j++) {
                    var t = request_time[j][0].split(/[- :]/);
                    request_time[j][0] = Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    temp_data1[j] = [request_time[j][0],request_time[j][1]];
                }

                good_data = [
                    {
                            idx   : 0,
                            name  : 'test',
                            data  : temp_data,
                            label : 'number'
                    },
                ];
                data1 = [
                    {
                        idx   : 0,
                        name  : 'test',
                        data  : temp_data1,
                        label : 'time',
                        color : '#d9534f'
                    }
                ];

                var extra_options = {
                 //   title: 'Provisioning requests',
                    ytitle: 'requests',
                    suffix: '#',
                };

                basicTimeGraph(document.getElementById('new_graph_$name'), document.getElementById('legend_$name'),good_data,extra_options)

                extra_options1 = {};
                //extra_options1.title='Average execution time';
                extra_options1.ytitle= 'ms';
                extra_options1.suffix= 'ms';
                extra_options1.scaling = 'logarithmic';
                basicTimeGraph(document.getElementById('new_graph1_$name'), document.getElementById('legend1_$name'),data1,extra_options1)
            });
            </script>
            <div class='row-fluid graphs'>
            <div class='span6'><h2>Number of provisioning Requests</h2><div id='new_graph_$name' class='graph'></div><div id='legend_$name'></div></div>
            <div class='span6'><h2>Average execution time per request</h2><div id='new_graph1_$name' class='graph'></div><div id='legend1_$name'></div></div></div>
        ";
        print $chart;
    }
}

?>
