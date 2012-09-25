<?
class CDRS_opensips extends CDRS {
    var $table                 = "radacct";
    var $CDR_class             = "CDR_opensips";
    var $subscriber_table      = "subscriber";
    var $ENUMtld               = '';
    var $maxCDRsNormalizeWeb   = 500;
    var $sipTrace              = 'sip_trace';
    var $mediaTrace            = 'media_trace';
    var $missed_calls_group    = 'missed-calls';
    var $rate_on_net_group     = 'rate-on-net';
    var $callerid_cache        = array();

    var $CDRFields=array('id'              => 'RadAcctId',
                         'callId'          => 'AcctSessionId',
                         'duration'        => 'AcctSessionTime',
                         'startTime'       => 'AcctStartTime',
                         'stopTime'        => 'AcctStopTime',
                         'inputTraffic'    => 'AcctInputOctets',
                         'outputTraffic'   => 'AcctOutputOctets',
                         'flow'            => 'ServiceType',
                         'aNumber'         => 'CallingStationId',
                         'username'        => 'UserName',
                         'domain'          => 'Realm',
                         'cNumber'         => 'CalledStationId',
                         'timestamp'       => 'timestamp',
                         'SipMethod'       => 'SipMethod',
                         'disconnect'      => 'SipResponseCode',
                         'SipFromTag'      => 'SipFromTag',
                         'SipToTag'        => 'SipToTag',
                         'RemoteAddress'   => 'SipTranslatedRequestURI',
                         'SipCodec'        => 'SipCodecs',
                         'SipUserAgents'   => 'SipUserAgents',
                         'application'     => 'SipApplicationType',
                         'BillingPartyId'  => 'UserName',
                         'SipRPID'         => 'SipRPID',
                         'SipProxyServer'  => 'NASIPAddress',
                         'gateway'         => 'SourceIP',
                         'SourceIP'        => 'SourceIP',
                         'SourcePort'      => 'SourcePort',
                         'CanonicalURI'    => 'CanonicalURI',
                         'normalized'      => 'Normalized',
                         'rate'            => 'Rate',
                         'price'           => 'Price',
                         'DestinationId'   => 'DestinationId',
                         'ResellerId'      => 'BillingId',
                         'MediaInfo'       => 'MediaInfo',
                         'RTPStatistics'   => 'RTPStatistics',
                         'ENUMtld'         => 'ENUMtld',
                         'UserAgent'       => 'UserAgent',
                         'FromHeader'      => 'FromHeader'
                         );

    var $CDRNormalizationFields=array('id'              => 'RadAcctId',
                                      'callId'          => 'AcctSessionId',
                                      'username'        => 'UserName',
                                      'domain'          => 'Realm',
                                      'gateway'         => 'SourceIP',
                                      'duration'        => 'AcctSessionTime',
                                      'startTime'       => 'AcctStartTime',
                                      'stopTime'        => 'AcctStopTime',
                                      'inputTraffic'    => 'AcctInputOctets',
                                      'outputTraffic'   => 'AcctOutputOctets',
                                      'aNumber'         => 'CallingStationId',
                                      'cNumber'         => 'CalledStationId',
                                      'timestamp'       => 'timestamp',
                                      'disconnect'      => 'SipResponseCode',
                                      'RemoteAddress'   => 'SipTranslatedRequestURI',
                                      'CanonicalURI'    => 'CanonicalURI',
                                      'SipRPID'         => 'SipRPID',
                                      'SipMethod'       => 'SipMethod',
                                      'application'     => 'SipApplicationType',
                                      'flow'            => 'ServiceType',
                                      'BillingPartyId'  => 'UserName',
                                      'ResellerId'      => 'BillingId',
                                      'price'           => 'Price',
                                      'DestinationId'   => 'DestinationId',
                                      'ENUMtld'         => 'ENUMtld'
                                      );

    var $GROUPBY=array('UserName'             => 'SIP Billing Party',
                       'CallingStationId'     => 'SIP Caller Party',
                       'SipRPID'              => 'SIP Remote Party Id',
                       'CanonicalURI'         => 'SIP Canonical URI',
                       'DestinationId'        => 'SIP Destination Id',
                       'NASIPAddress'         => 'SIP Proxy',
                       'MediaInfo'            => 'Media Information',
                       'SourceIP'             => 'Source IP',
                       'Realm'                => 'SIP Billing domain',
                       'UserAgent'            => 'User Agent',
                       'SipCodecs'            => 'Codec type',
                       'SipApplicationType'   => 'Application',
                       'SipResponseCode'      => 'SIP status code',
                       'BillingId'            => 'Tech prefix',
                       'ServiceType'          => 'Call Flow',
                       ' '                    => '-------------',
                       'hour'                 => 'Hour of day',
                       'DAYOFWEEK'            => 'Day of Week',
                       'DAYOFMONTH'           => 'Day of Month',
                       'DAYOFYEAR'            => 'Day of Year',
                       'BYMONTH'              => 'Month',
                       'BYYEAR'               => 'Year'
                       );

    var $FormElements=array(
        "begin_hour","begin_min","begin_month","begin_day","begin_year","begin_datetime",
        "end_hour","end_min","end_month","end_day","end_year","end_datetime",
        "call_id","sip_proxy",
        "a_number","a_number_comp","UserName","UserName_comp","BillingId",
        "c_number","c_number_comp","DestinationId","ExcludeDestinations",
        "NASPortId","Realm","Realms",
        "SipMethod","SipCodec","SipRPID","UserAgent",
        "application","sip_status","sip_status_class","gateway",
        "duration","action","MONTHYEAR",
        "order_by","order_type","group_by",
        "cdr_source","trace",
        "unnormalize","media_info","cdr_table","maxrowsperpage", "flow"
        );

    var $createTableFile="/setup/radius/OpenSIPS/radacct.mysql";

    function LoadDisconnectCodes() {

        $query="select * from sip_status order by code";
        $this->disconnectCodesElements[]=array("label"=>"Any Status","value"=>"");
        $this->disconnectCodesElements[]=array("label"=>"Undefined (0)","value"=>"0");
        $this->disconnectCodesClassElements[]=array("label"=>"Any Status Class","value"=>"");

        if ($this->cdrtool->query($query)) {
            while($this->cdrtool->next_record()) {
                $key         = $this->cdrtool->f('code');
                $value       = $this->cdrtool->f('description');
                $value_print = $this->cdrtool->f('description')." (".$this->cdrtool->f('code').")";
    
                if (preg_match("/^[^2-6]/",$key)) {
                    continue;
                }
                $this->disconnectCodesElements[]=array("label"=>$value_print,"value"=>$key);
                $this->disconnectCodesDescription[$key]=$value;
    
                $class=substr($key,0,1);
                $class_text=substr($key,0,1)."XX (".$this->cdrtool->f('code_type').")";
                if (!$seen[$class]) {
                    $this->disconnectCodesClassElements[]=array("label"=>$class_text,"value"=>substr($key,0,1));
                    $this->disconnectCodesClassDescription[substr($key,0,1)]=$class_text;
                    $seen[$class]++;
                }
                $i++;
            }
        }
    }

    function showTableHeader($begin_datetime,$end_datetime) {

        if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$begin_datetime) && preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$end_datetime)) {
            print "<p>From $begin_datetime to $end_datetime";
        }

        print "
        <table class='table table-hover table-condensed' width=100%>
        <thead>
        <tr>
        <th>Id</th>
        <th>Start Time</th>
        <th>Flow</th>
        <th>SIP Caller</th>
        <th>Caller Location</th>
        <th>Sip Proxy</th>
        <th>Media</th>
        <th>SIP Destination</th>
        <th>Dur</th>
        <th>Price</th>
        <th align=right>KBIn</th>
        <th align=right>KBOut</th>
        <th align=right>Status</th>
        <th align=right>Codecs</th>
        </tr>
        </thead>
        ";
    }

    function showExportHeader() {
        print "id,StartTime,StopTime,BillingParty,BillingDomain,PSTNCallerId,CallerParty,CalledParty,DestinationId,DestinationName,RemoteAddress,CanonicalURI,Duration,Price,SIPProxy,Caller KBIn,Called KBIn,CallingUserAgent,CalledUserAgent,StatusCode,StatusName,Codec,Media\n";
    }

    function showTableHeaderSubscriber($begin_datetime,$end_datetime) {
        if (!$this->export) {
            if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$begin_datetime) && preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$end_datetime)) {
                print "<p>
                From $begin_datetime to $end_datetime
                ";
            }

            print  "
            <table border=1 cellspacing=2 width=100% align=center>
            <tr>
            <td>
            <table border=0 cellspacing=2 width=100%>
            <tr bgcolor=lightgrey>
            <td>Id</td>
            <td><b>Start Time</b>
            <td><b>SIP Caller</b></td>
            <td><b>Caller Location</b></td>
            <td><b>Sip Proxy</b></td>
            <td><b>SIP Destination</b></td>
            <td><b>Duration</b></td>
            <td><b>Price</b></td>
            <td align=right><b>KBIn</b></td>
            <td align=right><b>KBOut</b></td>
            </tr>
            ";
        } else {
            print "id,StartTime,StopTime,SIPBillingParty,SIPBillingDomain,RemotePartyId,CallerParty,CalledParty,DestinationId,DestinationName,RemoteAddress,CanonicalURI,Duration,Price,SIPProxy,Applications,Caller KBIn,Called KBIn,CallingUserAgent,CalledUserAgent,StatusCode,StatusName,Codec,Application\n";
        }
    }

    function showTableHeaderStatistics($begin_datetime,$end_datetime) {
        $group_byPrint=$this->GROUPBY[$this->group_byOrig];

        if (!$this->export) {
            if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$begin_datetime) && preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/",$end_datetime)) {
                print "<p>
                From $begin_datetime to $end_datetime
                ";
            }

            print "
            <table border=1 cellspacing=2 width=100% align=center>
            <tr>
            <td>
            <table border=0 cellspacing=2 width=100%>
            <tr bgcolor=lightgrey>
                <td></td>
                <td>            <b>Calls</b></td>
                <td align=right><b>Seconds</b></td>
                <td align=right><b>Minutes</b></td>
                <td align=right><b>Hours</b></td>
                <td align=right><b>Price</b></td>
                <td align=right><b>TrafficIn(MB)</b></td>
                <td align=right><b>TrafficOut(MB)</b></td>
                <td align=center colspan=2><b>Success</b></td>
                <td align=center colspan=2><b>Failure</b></td>
                <td>            <b>$group_byPrint</b></td>
                <td>            <b>Description</b></td>
                <td>            <b>Action</b></td>
            </tr>
            ";
        } else {
            print "id,Calls,Seconds,Minutes,Hours,Price,TrafficIn(MB),TrafficOut(MB),Success(%),Success(calls),Failure(%),Failure(calls),$group_byPrint,Description\n";
        }
    }


    function initForm() {

        // form els added below must have global vars
        foreach ($this->FormElements as $_el) {
            global ${$_el};
            ${$_el} = trim($_REQUEST[$_el]);
        }

        $action         = "search";
        
        if ($this->CDRTool['filter']['gateway']) {
            $gateway=$this->CDRTool["filter"]["gateway"];
        }

        if ($this->CDRTool['filter']['aNumber']) {
            $UserName=$this->CDRTool['filter']['aNumber'];
        }

        if ($this->CDRTool['filter']['domain']) {
            $Realm  = $this->CDRTool['filter']['domain'];
        }

        if (!$maxrowsperpage) $maxrowsperpage=15;

        $this->f = new form;

        if (isset($this->CDRTool['dataSourcesAllowed'])) {
            while (list($k,$v)=each($this->CDRTool['dataSourcesAllowed'])) {
                if ($this->DATASOURCES[$v]['invisible']) continue;
                $cdr_source_els[]=array("label"=>$this->DATASOURCES[$v]['name'],"value"=>$v);
            }
        }
        
        if (!$cdr_source) $cdr_source=$cdr_source_els[0]['value'];

        $this->f->add_element(array("name"=>"cdr_source",
                                    "type"=>"select",
                                    "options"=>$cdr_source_els,
                                    "size"=>"1",
                                    "extrahtml"=>"class=span2 onChange=\"document.datasource.submit.disabled = true; location.href = 'callsearch.phtml?cdr_source=' + this.options[this.selectedIndex].value\"",
                                    "value"=>"$cdr_source"
                              )
                       );

        $cdr_table_els=array();
        foreach ($this->tables as $_table) {
            if (preg_match("/^.*(\d{6})$/",$_table,$m)) {
                $cdr_table_els[]=array("label"=>$m[1],"value"=>$_table);
            } else {
                $cdr_table_els[]=array("label"=>$_table,"value"=>$_table);
            }
        }
    
        $this->f->add_element(array(  "name"=>"cdr_table",
                                "type"=>"select",
                                "options"=>$cdr_table_els,
                                "size"=>"1",
                                "class"=>"span2",
                                "value"=>$cdr_table,
                                "extrahtml"=>"class=span2"
                                ));

        if ($begin_datetime) {
            preg_match("/^(\d\d\d\d)-(\d+)-(\d+)\s+(\d\d):(\d\d)/", "$begin_datetime", $parts);
            $begin_year    =date(Y,$begin_datetime);
            $begin_month=date(m,$begin_datetime);
            $begin_day     =date(d,$begin_datetime);
            $begin_hour    =date(H,$begin_datetime);
            $begin_min     =date(i,$begin_datetime);
        } else {
            $begin_day      = $_REQUEST["begin_day"];
            $begin_month    = $_REQUEST["begin_month"];
            $begin_year     = $_REQUEST["begin_year"];
            $begin_hour     = $_REQUEST["begin_hour"];
            $begin_min      = $_REQUEST["begin_min"];
        }
        
        if ($end_datetime) {
            preg_match("/^(\d\d\d\d)-(\d+)-(\d+)\s+(\d\d):(\d\d)/", "$end_datetime", $parts);
            $end_year    =date(Y,$end_datetime);
            $end_month   =date(m,$end_datetime);
            $end_day     =date(d,$end_datetime);
            $end_hour    =date(H,$end_datetime);
            $end_min     =date(i,$end_datetime);
        }  else {
            $end_day        = $_REQUEST["end_day"];
            $end_month      = $_REQUEST["end_month"];
            $end_year       = $_REQUEST["end_year"];
            $end_hour       = $_REQUEST["end_hour"];
            $end_min        = $_REQUEST["end_min"];
        }

        // corect last day of the month to be valid day
        $begin_day = validDay($begin_month,$begin_day,$begin_year);
        $end_day   = validDay($end_month,$end_day,$end_year);

        $default_year  = Date("Y");
        $default_month = Date("m");
        $default_day   = Date("d");
        $default_hour  = Date(H,time());
        
        if ($default_hour > 1) $default_hour=$default_hour-1;
        
        $default_hour = preg_replace("/^(\d)$/","0$1",$default_hour);
        $default_min  = Date("i");
        
        if ($default_min > 10) {
            $default_min=$default_min-10;
            $default_min=preg_replace("/^(\d)$/","0$1",$default_min);
        }

        if (!$begin_hour)  $begin_hour  = $default_hour;
        if (!$begin_min)   $begin_min   = $default_min;
        if (!$begin_day)   $begin_day   = $default_day;
        if (!$begin_month) $begin_month = $default_month;
        if (!$begin_year)  $begin_year  = $default_year;

        if (!$end_hour)  $end_hour  = 23;
        if (!$end_min)   $end_min   = 55;
        if (!$end_day)   $end_day   = $default_day;
        if (!$end_month) $end_month = $default_month;
        if (!$end_year)  $end_year  = $default_year;

        $m=0;
        while ($m<24) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $hours_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }
        $this->f->add_element(array(
                    "name"=>"begin_hour",
                    "type"=>"select",
                    "options"=>$hours_els,
                    "size"=>"1",
                    "extrahtml"=>"class=span1"
                    ));
        $this->f->add_element(array(    "name"=>"end_hour",
                    "type"=>"select",
                    "options"=>$hours_els,
                    "size"=>"1",
                    "value"=>"23",
                    "extrahtml"=>"class=span1"
                    ));
        $m=0;
        while ($m<60) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $min_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }
        $this->f->add_element(array(    "name"=>"begin_min",
                    "type"=>"select",
                    "options"=>$min_els,
                    "size"=>"1",
                    "extrahtml"=>"class=span1"
                    ));
        $this->f->add_element(array(
                    "name"=>"end_min",
                    "type"=>"select",
                    "options"=>$min_els,
                    "size"=>"1",
                    "extrahtml"=>"class=span1"
                    ));
        $m=1;
        while ($m<32) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $days_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }
        $this->f->add_element(array(    "name"=>"begin_day",
                                "type"=>"select",
                                "options"=>$days_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span1"
        
                    ));
        $this->f->add_element(array(    "name"=>"end_day",
                                "type"=>"select",
                                "options"=>$days_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span1"
                    ));

        $m=1;
        while ($m<13) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $month_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }
        
        $this->f->add_element(array(    "name"=>"begin_month",
                                "type"=>"select",
                                "options"=>$month_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span1"
                    ));
        $this->f->add_element(array(    "name"=>"end_month",
                                "type"=>"select",
                                "options"=>$month_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span1"
                    ));
        $thisYear=date("Y",time());
        $y=$thisYear;
        while ($y>$thisYear-6) {
            $year_els[]=array("label"=>$y,"value"=>$y);
            $y--;
        }
        $this->f->add_element(array(    "name"=>"begin_year",
                                "type"=>"select",
                                "options"=>$year_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
        
                    ));
        $this->f->add_element(array(    "name"=>"end_year",
                                "type"=>"select",
                                "options"=>$year_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"call_id",
                                "type"=>"text",
                                "size"=>"50",
                                "maxlength"=>"100",
                                "extrahtml"=>"class=span4"
                    ));

        $this->f->add_element(array(    "name"=>"UserName",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"255",
                                "extrahtml"=>"class=span2"
                    ));

        $this->f->add_element(array(    "name"=>"a_number",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"255",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"BillingId",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"255",
                                "extra_html"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"c_number",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"255",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(  "name"=>"sip_status",
                                "type"=>"select",
                                "options"=>$this->disconnectCodesElements,
                                "size"=>"1",
                                "value"=>$sip_status,
                                "extrahtml"=>"class=span3"
                                ));
        $this->f->add_element(array(  "name"=>"sip_status_class",
                                "type"=>"select",
                                "options"=>$this->disconnectCodesClassElements,
                                "size"=>"1",
                                "extrahtml"=>"class=span3"
                                ));

        if (!$this->CDRTool['filter']['aNumber']) {
            $durations_els = array(
                array("label"=>"All calls","value"=>""),
                array("label"=>"0 seconds","value"=>"zero"),
                array("label"=>"non 0 seconds","value"=>"nonzero"),
                array("label"=>"non 0 seconds without price","value"=>"zeroprice"),
                array("label"=>"less than 5 seconds","value"=>"< 5"),
                array("label"=>"more than 5 seconds","value"=>"> 5"),
                array("label"=>"less than 60 seconds","value"=>"< 60"),
                array("label"=>"greater than 1 hour","value"=>"> 3600"),
                array("label"=>"one hour","value"=>"onehour"),
                array("label"=>"greater than 5 hours","value"=>"> 18000"),
                array("label"=>"Un-normalized calls","value"=>"unnormalized"),
                array("label"=>"Un-normalized calls > 0s","value"=>"unnormalized_duration"),
                array("label"=>"One way media","value"=>"onewaymedia"),
                array("label"=>"No media","value"=>"nomedia")
                );
        } else {
            $durations_els = array(
                array("label"=>"All calls","value"=>""),
                array("label"=>"0 seconds call","value"=>"zero"),
                array("label"=>"Succesfull calls","value"=>"nonzero"),
                array("label"=>"less than 60 seconds","value"=>"< 60"),
                array("label"=>"greater than 1 hour","value"=>"> 3600")
                );
            $this->GROUPBY=array(
                           'UserName'             => 'SIP Billing Party',
                           'CallingStationId'     => 'SIP Caller Party',
                           'DestinationId'        => 'SIP Destination Id',
                           'SipApplicationType'   => 'Application',
                               ' '                => '-------------',
                           'hour'                 => 'Hour of day',
                           'DAYOFWEEK'            => 'Day of Week',
                           'DAYOFMONTH'           => 'Day of Month',
                           'DAYOFYEAR'            => 'Day of Year',
                           'BYMONTH'              => 'Month',
                           'BYYEAR'               => 'Year'

                           );

        }
        
        $flow_els = array(
                array("label"=>"Any Call Flow","value"=>""),
                array("label"=>"On Net","value"=>"on-net"),
                array("label"=>"Incoming","value"=>"incoming"),
                array("label"=>"Outgoing","value"=>"outgoing"),
                array("label"=>"Transit","value"=>"transit"),
                array("label"=>"Diverted On Net","value"=>"diverted-on-net"),
                array("label"=>"Diverted Off Net","value"=>"diverted-off-net"),
                array("label"=>"On Net Diverted On Net","value"=>"on-net-diverted-on-net"),
                array("label"=>"On Net Diverted Off Net","value"=>"on-net-diverted-off-net"),
                array("label"=>"Unknown Flow","value"=>"Sip-Session")
                );

        $this->f->add_element(array(    "name"=>"flow",
                                "type"=>"select",
                                "options"=>$flow_els,
                                "value"=>"",
                                "size"=>"1",
                                "extrahtml"=>"class=span3"
                    ));

        $this->f->add_element(array(    "name"=>"duration",
                                "type"=>"select",
                                "options"=>$durations_els,
                                "value"=>"All",
                                "size"=>"1",
                                "extrahtml"=>"class=span3"
                    ));
        $comp_ops_els = array(
                array("label"=>"Begins with","value"=>"begin"),
                array("label"=>"Contains","value"=>"contain"),
                array("label"=>"Is empty","value"=>"empty"),
                array("label"=>"Equal","value"=>"equal")
                );
        $this->f->add_element(array(    "name"=>"a_number_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"c_number_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"UserName_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array(    "name"=>"Realm",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"25",
                                "extrahtml"=>"class=span2"
                    ));

        $media_info_els=array(
                array("label"=>"","value"=>""),
                        array("label"=>"Timeout","value"=>"timeout"),
                        array("label"=>"ICE session","value"=>"ICE session")
                );

        $this->f->add_element(array(    "name"=>"media_info",
                                "type"=>"select",
                                "options"=>$media_info_els,
                                "size"=>"1",
                                "value"=>"",
                                "extrahtml"=>"class=span2"
                    ));

        $this->f->add_element(array("type"=>"submit",
                              "name"=>"submit",
                              "value"=>"Search","extrahtml"=>"class=btn"
                    ));
        $max_els=array(
                array("label"=>"5","value"=>"5"),
                        array("label"=>"10","value"=>"10"),
                        array("label"=>"15","value"=>"15"),
                        array("label"=>"25","value"=>"25"),
                        array("label"=>"50","value"=>"50"),
                        array("label"=>"100","value"=>"100"),
                        array("label"=>"500","value"=>"500")
                );
        $this->f->add_element(array(    "name"=>"maxrowsperpage",
                                "type"=>"select",
                                "options"=>$max_els,
                                "size"=>"1",
                                "value"=>"25",
                                "extrahtml"=>"class=span2"
                    ));
        $order_type_els=array(
                        array("label"=>"Descending","value"=>"DESC"),
                        array("label"=>"Ascending","value"=>"ASC")
                        );
        $this->f->add_element(array(    "name"=>"order_type",
                                "type"=>"select",
                                "options"=>$order_type_els,
                                "size"=>"1",
                                "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array("type"=>"hidden",
                              "name"=>"action",
                              "value"=>$action
                ));

        $order_by_els=array(array("label"=>"Id","value"=>"RadAcctId"),
                            array("label"=>"Date","value"=>"AcctStopTime"),
                            array("label"=>"Billing Party","value"=>"UserName"),
                            array("label"=>"Remote Party Id","value"=>"SipRPID"),
                            array("label"=>"Caller Party","value"=>"CallingStationId"),
                            array("label"=>"Destination","value"=>"CalledStationId"),
                            array("label"=>"Duration","value"=>"AcctSessionTime"),
                            array("label"=>"Input traffic","value"=>"AcctInputOctets"),
                            array("label"=>"Output traffic","value"=>"AcctOutputInputOctets"),
                            array("label"=>"Price","value"=>"Price"),
                            array("label"=>"Failures(%)","value"=>"zeroP"),
                            array("label"=>"Success(%)","value"=>"nonzeroP"),
                            array("label"=>"Group by","value"=>"group_by")
        );

        $group_by_els[]=array("label"=>"","value"=>"");

        while (list($k,$v)=each($this->GROUPBY)) {
            $group_by_els[]=array("label"=>$v,"value"=>$k);
        }

        $this->f->add_element(array("name"=>"order_by",
                                    "type"=>"select",
                                    "options"=>$order_by_els,
                                    "value"=>$order_by,
                                    "size"=>"1",
                                    "extrahtml"=>"class=span3"
                                ));
        $this->f->add_element(array("name"=>"group_by",
                                    "type"=>"select",
                                    "options"=>$group_by_els,
                                    "value"=>$group_by,
                                    "size"=>"1",
                                    "extrahtml"=>"class=span3"
                                ));
        $application_els=array(
                               array("label"=>"Any Application",          "value"=>""),
                               array("label"=>"Audio",        "value"=>"audio"),
                               array("label"=>"Video",        "value"=>"video"),
                               array("label"=>"Message" ,         "value"=>"message"),
                               array("label"=>"IM Chat" ,     "value"=>"chat"),
                               array("label"=>"Audio + Chat" ,     "value"=>"audio=2C chat"),
                               array("label"=>"File Transfer","value"=>"file-transfer")
                               );

        $this->f->add_element(array("name"=>"application",
                                    "type"=>"select",
                                    "options"=>$application_els,
                                    "value"=>$application,
                                    "size"=>"1",
                                    "extrahtml"=>"class=span2"
                                ));
        $this->f->add_element(array("name"=>"UserAgent",
                                    "type"=>"text",
                                    "size"=>"25",
                                    "maxlength"=>"50",
                                    "value"=>$UserAgent,
                                    "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array("name"=>"SipCodec",
                                    "type"=>"text",
                                    "size"=>"10",
                                    "maxlength"=>"50",
                                    "value"=>$SipCodec,
                                    "extrahtml"=>"class=span2"
                    ));
        $this->f->add_element(array("name"=>"sip_proxy",
                                    "type"=>"text",
                                    "size"=>"25",
                                    "maxlength"=>"255",
                                    "value"=>$sip_proxy,
                                    "extrahtml"=>"class=span2"
                                    ));
        $this->f->add_element(array("name"=>"gateway",
                                    "type"=>"text",
                                    "size"=>"25",
                                    "maxlength"=>"255",
                                    "value"=>$gateway,
                                    "extrahtml"=>"class=span2"
                                    ));
        $this->f->add_element(array("name"=>"DestinationId",
                                    "type"=>"text",
                                    "size"=>"10",
                                    "extrahtml"=>"class=span3"
                                ));
        $this->f->add_element(array(    "name"=>"ExcludeDestinations",
                                "type"=>"text",
                                "size"=>"20",
                                "maxlength"=>"255",
                                "extrahtml"=>"class=span3"
                    ));
        $this->f->load_defaults();

    }

    function searchForm() {
        global $perm;

        $this->initForm();
        $this->f->start("","POST","","","datasource");

        print "<table id='search' class='table table-bordered table-condensed' cellpadding=5 width=100% align=center>";

        $this->showDataSources ($this->f);
        $this->showDateTimeElements ($this->f);

        // freeze some form els
        if ($this->CDRTool['filter']['aNumber']) {
            $ff[]="a_number";
            $ff[]="a_number_comp";
            $ff[]="UserName";
            $ff[]="UserName_comp";
        }

        if ($this->CDRTool['filter']['domain']) {
            $Realm=$this->CDRTool['filter']['domain'];
            $ff[]="Realm";
        }

        if ($this->CDRTool['filter']['gateway']) {
            $gateway=$this->CDRTool['filter']['gateway'];
            $ff[]="gateway";
        }

        if (count($ff)) {
            $this->f->freeze($ff);
        }

        print "
        <tr> 
            <td align=left>
            <b>SIP Call Id / Source IP</b>
            </td>
            <td>
            <div class=\"input-prepend\">
            <div class=\"input-append\">";
            $this->f->show_element("call_id","");
            print "<span class=\"add-on\">/</span></div>";
            $this->f->show_element("gateway","");
            print "</div>
            Sip Proxy ";

            $this->f->show_element("sip_proxy","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>User Agent / Media Codecs</b>
            </td>
            <td >
            ";
            $this->f->show_element("UserAgent","");
            print " Codec: ";
            $this->f->show_element("SipCodec","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>SIP Billing Party (Username)</b>
            </td>
            <td>
            ";
            $this->f->show_element("UserName_comp","");
            print "
                <div class=\"input-prepend\">
                <div class=\"input-append\">
            ";
            $this->f->show_element("UserName","");
            print "<span class=\"add-on\">@</span></div>";
            $this->f->show_element("Realm","");
            print "</div> Tech prefix: ";
            $this->f->show_element("BillingId","");
            print "</div>
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>
            SIP Caller Party (From URI)
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("a_number_comp","");
            print "&nbsp;";
            $this->f->show_element("a_number");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>SIP Destination (Canonical URI)
            </b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("c_number_comp","");
            print "&nbsp;";

            $this->f->show_element("c_number","");
            print " Exclude: ";
            $this->f->show_element("ExcludeDestinations_comp");
            $this->f->show_element("ExcludeDestinations","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>Application / Call Flow</b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("flow","");
            $this->f->show_element("application","");
            print " Media Info: ";
            $this->f->show_element("media_info","");
            print "
            </td>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>Duration / Status</b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("duration","");
            print "&nbsp;";
            $this->f->show_element("sip_status","");
            $this->f->show_element("sip_status_class","");
            print "
            </td>
        </tr>
        ";
        print "
        <tr> 
            <td align=left>
            <b>Order by / Group by</b>
            </td>
            <td valign=top>
             ";
            $this->f->show_element("order_by","");
            $this->f->show_element("order_type","");

            if ($perm->have_perm("statistics")) {
               print " Group by ";
               $this->f->show_element("group_by","");
            }

            print "<nobr> Max results per page ";
            $this->f->show_element("maxrowsperpage","");

            print "</nobr>&nbsp";

            if (!$perm->have_perm('readonly')) {
                print ";&nbsp;&nbsp; <nobr>ReNormalize ";
                print "<input type=checkbox name=ReNormalize value=1>
                </nobr>";
            }

            print "
            </td>
        </tr>
        ";

        print "
        </table>
        <p>
        <center>
        ";

        $this->f->show_element("submit","");
        $this->f->finish();

        print "
        </center>
        ";
    }

    function searchFormSubscriber() {
        global $perm;

        $this->initForm();
        $this->f->start("","POST","","","datasource");

        print "
        <table cellpadding=5 CELLSPACING=0 border=6 width=100% align=center>
        ";
        $this->showDataSources ($this->f);
        $this->showDateTimeElements ($this->f);

        // freeze some form els
        if ($this->CDRTool['filter']['aNumber']) {
            $ff[]="UserName";
        }

        if ($this->CDRTool['filter']['domain']) {
            $ff[]="Realm";
        }

        if ($this->CDRTool["filter"]["gateway"]) {
            $ff[]="gateway";
        }

        if (count($ff)) {
            $this->f->freeze($ff);
        }

        print "
        <tr> 
            <td align=left>
            <b>
            SIP Caller Party
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("a_number","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>
            SIP Billing Party
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("UserName","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>
            SIP Destination
            </b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("c_number_comp","");
            $this->f->show_element("c_number","");
            //$this->f->show_element("DestinationId","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";
        
        print "
        <tr> 
            <td align=left>
            <b>SIP Session duration</b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("duration","");
            print " Application ";
            $this->f->show_element("application","");
            print "
            </td>
        </tr>
        ";
        print "
        <tr> 
            <td align=left>
            <b>Order by</b>
            </td>
            <td valign=top>
             ";
             $this->f->show_element("order_by","");
             $this->f->show_element("order_type","");

             if ($perm->have_perm("statistics")) {
                print " Group by ";
                $this->f->show_element("group_by","");
             }

             print " Max results per page ";
             $this->f->show_element("maxrowsperpage","");
             print "
             </td>
        </tr>
        ";

        print "
        </table>
        <p>
        <center>
        ";

        $this->f->show_element("submit","");
        $this->f->finish();

        print "
        </center>
        ";

    }

    function show() {
        global $perm;

        if (!is_object($this->CDRdb)) {
            $log=sprintf("Error: CDR database is not initalized");
            print $log;
            return false;
        }

        foreach ($this->FormElements as $_el) {
            ${$_el} = trim($_REQUEST[$_el]);
        }

        // overwrite some elements based on user rights
        if ($this->CDRTool['filter']['gateway']) {
            $gateway =$this->CDRTool['filter']['gateway'];
        }

        if (!$this->export) {
            if (!$begin_datetime) {
                $begin_datetime="$begin_year-$begin_month-$begin_day $begin_hour:$begin_min";
                $begin_datetime_timestamp=mktime($begin_hour, $begin_min, 0, $begin_month,$begin_day,$begin_year);
            } else {
                $begin_datetime_timestamp=$begin_datetime;
                $begin_datetime=Date("Y-m-d H:i",$begin_datetime);
            }

            if (!$end_datetime) {
                $end_datetime_timestamp=mktime($end_hour, $end_min, 0, $end_month,$end_day,$end_year);
                $end_datetime="$end_year-$end_month-$end_day $end_hour:$end_min";
            } else {
                $end_datetime_timestamp=$end_datetime;
                $end_datetime=Date("Y-m-d H:i",$end_datetime);
            }
        } else {
            $begin_datetime=Date("Y-m-d H:i",$begin_datetime);
            $end_datetime=Date("Y-m-d H:i",$end_datetime);
        }

        if (!$order_by || (!$group_by && $order_by == "group_by")) {
            $order_by=$this->idField;
        }

        if (!$cdr_table) $cdr_table=$this->table;

        $this->url=sprintf("?cdr_source=%s&cdr_table=%s",$this->cdr_source,$cdr_table);

        if ($this->CDRTool['filter']['domain']) {
            $this->url  .= sprintf("&Realms=%s",urlencode($this->CDRTool['filter']['domain']));
            $Realms      = explode(" ",$this->CDRTool['filter']['domain']);
        } else if ($Realms) {
            $this->url   .= sprintf("&Realms=%s",urlencode($Realms));
            $Realms      = explode(" ",$Realms);
        }

        if ($this->CDRTool['filter']['aNumber']) {
            $this->url   .= sprintf("&UserName=%s",urlencode($this->CDRTool['filter']['aNumber']));
        }

        if ($this->CDRTool['filter']['after_date']) {
            $where .= sprintf(" and %s >= '%s' ",addslashes($this->startTimeField),addslashes($this->CDRTool['filter']['after_date']));
        }

        if ($order_by) {
            $this->url.=sprintf("&order_by=%s&order_type=%s",addslashes($order_by),addslashes($order_type));
        }

        $this->url.=sprintf("&begin_datetime=%s",urlencode($begin_datetime_timestamp));
        $this->url.=sprintf("&end_datetime=%s",urlencode($end_datetime_timestamp));

        if (!$call_id && $begin_datetime && $end_datetime) {
            $where .= sprintf(" (%s >= '%s' and %s < '%s') ",addslashes($this->startTimeField),addslashes($begin_datetime),addslashes($this->startTimeField), addslashes($end_datetime));
        } else {
            $where .= sprintf(" (%s >= '1970-01-01' ) ",addslashes($this->startTimeField));
        }

        if ($MONTHYEAR) {
            $where .=  sprintf(" and %s like '%s%s' ", addslashes($this->startTimeField), addslashes($MONTHYEAR), '%');
            $this->url.= sprintf("&MONTHYEAR=%s",urlencode($MONTHYEAR));
        }

        if ($flow) {
            $this->url.=sprintf("&flow=%s",urlencode($flow));
            $where .=  sprintf(" and %s = '%s' ", addslashes($this->flowField), addslashes($flow));
        }

        if ($this->CDRTool['filter']['aNumber']) {
            // force user to see only CDRS with his a_numbers
            $where .= sprintf(" and ( %s = '%s' or %s = '%s') ",
                                addslashes($this->usernameField),
                                addslashes($this->CDRTool['filter']['aNumber']),
                                addslashes($this->CanonicalURIField),
                                addslashes($this->CDRTool['filter']['aNumber'])
                             );
            $UserName_comp='equal';
            $UserName=$this->CDRTool['filter']['aNumber'];
        }

        if ($UserName_comp == "empty") {
            $where .= sprintf(" and %s = ''", addslashes($this->usernameField));
            $this->url.=sprintf("&UserName_comp=%s",urlencode($UserName_comp));
        } else if (strlen($UserName) && !$this->CDRTool['filter']['aNumber']) {
            if (!$UserName_comp) $UserName_comp='begin';

            if ($UserName_comp=="begin") {
                $where .= sprintf(" and %s like '%s%s'", addslashes($this->usernameField), addslashes($UserName), '%');
            } elseif ($UserName_comp=="contain") {
                $where .= sprintf(" and %s like '%s%s%s'", addslashes($this->usernameField), '%s', addslashes($UserName), '%');
            } elseif ($UserName_comp=="equal") {
                $where .= sprintf(" and %s = '%s'", addslashes($this->usernameField), addslashes($UserName));
            } else {
                $where .= sprintf(" and %s = ''", addslashes($this->usernameField));
            }

            $this->url.=sprintf("&UserName=%s&UserName_comp=%s",urlencode($UserName),$UserName_comp);
        }

        $a_number=trim($a_number);
        if ($a_number_comp == "empty") {
            $where .= sprintf(" and %s = ''", addslashes($this->aNumberField));
            $this->url.=sprintf("&a_number_comp=%s",urlencode($a_number_comp));
        } else if (strlen($a_number)) {
            $a_number=urldecode($a_number);
            if (!$a_number_comp) $a_number_comp="equal";

            $this->url.=sprintf("&a_number=%s",urlencode($a_number));

            if ($a_number_comp=="begin") {
                $where .= sprintf(" and %s like '%s%s'", addslashes($this->aNumberField), addslashes($a_number), '%s');
            } elseif ($a_number_comp=="contain") {
                $where .= sprintf(" and %s like '%s%s%s'", addslashes($this->aNumberField), '%s', addslashes($a_number), '%s');
            } elseif ($a_number_comp=="equal") {
                $where .= sprintf(" and %s = '%s'", addslashes($this->aNumberField), addslashes($a_number));
            }
            $this->url.=sprintf("&a_number_comp=%s",urlencode($a_number_comp));
        }

        $c_number=trim($c_number);
        if ($c_number_comp == "empty") {
            $where .= sprintf(" and %s = ''", addslashes($this->CanonicalURIField));
            $this->url.=sprintf("&c_number_comp=%s",urlencode($c_number_comp));
        } else if (strlen($c_number)) {
            $c_number=urldecode($c_number);
            if (!$c_number_comp) $c_number_comp="begin";

            if (!$c_number_comp || $c_number_comp=="begin") {
                $where .= sprintf(" and %s like '%s%s'", addslashes($this->CanonicalURIField), addslashes($c_number), '%s');
            } elseif ($c_number_comp=="contain") {
                $where .= sprintf(" and %s like '%s%s%s'", addslashes($this->CanonicalURIField), '%s', addslashes($c_number), '%s');
            } elseif ($c_number_comp=="equal") {
                $where .= sprintf(" and %s = '%s'", addslashes($this->CanonicalURIField), addslashes($c_number));
            }
            $this->url.=sprintf("&c_number=%s&c_number_comp=%s",urlencode($c_number),urlencode($c_number_comp));
        }

        $Realm=trim($Realm);

        if ($this->CDRTool['filter']['domain']) {
            $where .= "
            and (" ;
            $rr=0;
            foreach ($Realms as $realm) {
                if ($rr) $where .= " or ";
                $where .= " $this->domainField like '".addslashes($realm)."%' ";
                $rr++;
            }
            $where .= " ) ";

        } else if ($Realm) {
            $Realm=urldecode($Realm);
            $where .= " and $this->domainField like '".addslashes($Realm)."%' ";
            $this->url.=sprintf("&Realm=%s",urlencode($Realm));
        } else if ($Realms)  {
            $where .= "
            and (" ;
            $rr=0;
            foreach ($Realms as $realm) {
                if ($rr) {
                    $where .= " or ";
                }
                $where .= " $this->domainField like '".addslashes($realm)."%' ";

                $rr++;
            }
            $where .= " ) ";
        }

        $BillingId=trim($BillingId);
        if (preg_match("/^\d+$/",$BillingId) && $this->BillingIdField) {
            $where .= " and $this->BillingIdField = '".addslashes($BillingId)."'";
            $this->url.=sprintf("&BillingId=%s",urlencode($BillingId));
        }

        if ($application) {
            $where .= " and $this->applicationField like '%".addslashes($application)."%'";
            $this->url.=sprintf("&application=%s",urlencode($application));
        }

        if ($DestinationId) {
            if ($DestinationId=="empty") {
                $DestinationIdSQL="";
            } else {
                $DestinationIdSQL=$DestinationId;
            }
            $where .= " and $this->DestinationIdField = '".addslashes($DestinationIdSQL)."'";
            $this->url.=sprintf("&DestinationId=%s",urlencode($DestinationId));
        }

        if (strlen(trim($ExcludeDestinations))) {
            $ExcludeDestArray=explode(" ",trim($ExcludeDestinations));

            foreach ($ExcludeDestArray as $exclDst) {
                if (preg_match("/^0+(\d+)$/",$exclDst,$m)) {
                    $exclDest_id=$m[1];
                } else {
                    $exclDest_id=$exclDst;
                }

                $where .= " and ".
                $this->CanonicalURIField.
                " not like '".
                addslashes(trim($exclDst)).
                "'";
            }

            $this->url.=sprintf("&ExcludeDestinations=%s",urlencode($ExcludeDestinations));
        }

        $call_id=trim($call_id);

        if ($call_id) {
            $call_id=urldecode($call_id);
            $where .= " and $this->callIdField = '".addslashes($call_id)."'";
            $this->url.=sprintf("&call_id=%s",urlencode($call_id));
        }

        if ($sip_proxy) {
            $sip_proxy=urldecode($sip_proxy);
            $where .= " and $this->SipProxyServerField = '".addslashes($sip_proxy)."'";
            $this->url.=sprintf("&sip_proxy=%s",urlencode($sip_proxy));
        }

        if ($SipCodec) {
            $this->url.=sprintf("&SipCodec=%s",urlencode($SipCodec));
            if ($SipCodec != "empty") {
                $where .= " and $this->SipCodecField = '".addslashes($SipCodec)."'";
            } else {
                $where .= " and $this->SipCodecField = ''";
            }
        }

        if ($SipRPID) {
            $this->url.=sprintf("&SipRPID=%s",urlencode($SipRPID));
            if ($SipRPID != "empty") {
                $where .= " and $this->SipRPIDField = '".addslashes($SipRPID)."'";
            } else {
                $where .= " and $this->SipRPIDField = ''";
            }
        }

        if ($UserAgent) {
            $where .= " and $this->UserAgentField like '%".addslashes($UserAgent)."%'";
            $this->url.=sprintf("&UserAgent=%s",urlencode($UserAgent));
        }

        if (strlen($sip_status)) {
            $where .= " and $this->disconnectField ='".addslashes($sip_status)."'";
            $this->url.=sprintf("&sip_status=%s",urlencode($sip_status));
        }

        if ($sip_status_class) {
            $where .= " and $this->disconnectField like '$sip_status_class%'";
            $this->url.=sprintf("&sip_status_class=%s",urlencode($sip_status_class));
        }

        if ($this->CDRTool[filter]["gateway"]) {
            $gatewayFilter=$this->CDRTool[filter]["gateway"];
            $where .= " and ($this->gatewayField = '".addslashes($gatewayFilter)."')";
        } else if ($gateway) {
            $gateway=urldecode($gateway);
            $where .= " and $this->gatewayField = '".addslashes($gateway)."'";
            $this->url.=sprintf("&gateway=%s",urlencode($gateway));
        }

        if ($duration) {
            if (preg_match("/\d+/",$duration) ) {
                $where .= " and ($this->durationField > 0 and $this->durationField $duration) ";
            } elseif (preg_match("/onehour/",$duration) ) {
                $where .= " and ($this->durationField < 3610 and $this->durationField > 3530) ";
            } elseif ($duration == "zero") {
                $where .= " and $this->durationField = 0";
            } elseif ($duration == "zeroprice" && $this->priceField) {
                $where .= " and $this->durationField > 0 and ($this->priceField = '' or $this->priceField is NULL)";
            } elseif ($duration == "nonzero") {
                $where .= " and $this->durationField > 0";
            } elseif ($duration == "onewaymedia") {
                $where .= " and (($this->inputTrafficField > 0 && $this->outputTrafficField = 0) || ($this->inputTrafficField = 0 && $this->outputTrafficField > 0)) " ;
            } elseif ($duration == "nomedia") {
                $where .= " and ($this->inputTrafficField = 0 && $this->outputTrafficField = 0) " ;
            }
            $this->url.=sprintf("&duration=%s",urlencode($duration));
        }

        if ($media_info) {
            $this->url.=sprintf("&media_info=%s",urlencode($media_info));
            $where .= sprintf(" and %s = '%s' ",addslashes($this->MediaInfoField), addslashes($media_info));
        }

        $this->url.=sprintf("&maxrowsperpage=%s",addslashes($this->maxrowsperpage));
        $url_calls = $this->scriptFile.$this->url."&action=search";

        if ($group_by) {
            $this->url.=sprintf("&group_by=%s",urlencode($group_by));
        }

        $this->url_edit   = $this->scriptFile.$this->url."&action=edit";
        $this->url_run    = $this->scriptFile.$this->url."&action=search";
        $this->url_export = $_SERVER["PHP_SELF"].$this->url."&action=search&export=1";

        if ($duration == "unnormalized") {
            $where .= " and $this->normalizedField = '0' ";
        }

        if ($duration == "unnormalized_duration") {
            $where .= " and $this->normalizedField = '0' and $this->durationField > 0 ";
        }

        if ($group_by) {

            $this->group_byOrig=$group_by;

            if ($group_by=="hour") {
                $group_by="HOUR(AcctStartTime)";
            } else if (preg_match("/^DAY/",$group_by)) {
                $group_by="$group_by(AcctStartTime)";
            } else if (preg_match("/BYMONTH/",$group_by)) {
                $group_by="DATE_FORMAT(AcctStartTime,'%Y-%m')";
            } else if (preg_match("/BYYEAR/",$group_by)) {
                $group_by="DATE_FORMAT(AcctStartTime,'%Y')";
            } else if ($group_by=="UserAgentType") {
                $group_by="SUBSTRING_INDEX($this->SipUserAgentsField, ' ', '1')";
            }

            $this->group_by=$group_by;

            if ($group_by==$this->callIdField) {
                $having=sprintf(" having count(%s) > 1 ", addslashes($group_by));
            }

            $query= sprintf("select sum(%s) as duration, SEC_TO_TIME(sum(%s)) as duration_print, count(%s) as calls, %s from %s where %s group by %s %s ",
            addslashes($this->durationField),
            addslashes($this->durationField),
            addslashes($group_by),
            addslashes($group_by),
            addslashes($cdr_table),
            $where,
            addslashes($group_by),
            $having
            );
        } else {
            $query = sprintf("select count(*) as records from %s where ", addslashes($cdr_table)). $where;
        }

        dprint($query);

        if ($this->CDRdb->query($query)) {
             $this->CDRdb->next_record();
             if ($group_by) {
                $rows = $this->CDRdb->num_rows();
             } else {
                $rows = $this->CDRdb->f('records');
             }
        } else {
            printf ("%s",$this->CDRdb->Error);
            $rows = 0;
        }

        $this->rows=$rows;

        if ($this->CDRTool['filter']['aNumber']) {
            $this->showResultsMenuSubscriber();
        } else {
            $this->showResultsMenu();
        }

        if (!$this->next)   {
            $i=0;
            $this->next=0;
        } else {
            $i=$this->next;
        }
        $j=0;
        $z=0;

        if (!$this->export) print "<div class=\"alert alert-info\">";
        
        if ($rows>0)  {
            
            if ($call_id && $unnormalize) {
                $query=sprintf("update %s
                set %s = '0'
                where %s = '%s'",
                addslashes($cdr_table),
                addslashes($this->normalizedField),
                addslashes($this->callIdField),
                addslashes($call_id)
                );

                $this->CDRdb->query($query);
            }

            if ($UnNormalizedCalls=$this->getUnNormalized($where,$cdr_table)) {
                if (!$this->DATASOURCES[$this->cdr_source]['skipNormalizeOnPageLoad']) {
                    if ($UnNormalizedCalls < $this->maxCDRsNormalizeWeb) {
                        $this->NormalizeCDRS($where,$cdr_table);
                        if (!$this->export && $this->status['normalized'] ) {
                            printf ("<b><span class=\"alert-heading\">%d</span></b> CDRs normalized. ",$this->status['normalized']);
                            if ($this->status['cached_keys']['saved_keys']) {
                                printf ("Quota usage updated for <b><span class=\"alert-heading\">%d</span></b> accounts. ",$this->status['cached_keys']['saved_keys']);
                            } 
                        }
                    }
                }
            }

            if ($rows > $this->maxrowsperpage)  {
                $maxrows=$this->maxrowsperpage+$this->next;
                if ($maxrows > $rows) {
                    $maxrows=$rows;
                    $prev_rows=$maxrows;
                }
            } else {
                $maxrows=$rows;
            }

            if ($duration == "unnormalized") {
                // if display un normalized calls we must substract
                // the amount of calls normalized above
                $maxrows=$maxrows-$this->status['normalized'];
            }
        
            if ($group_by) {
                if ($order_by=="group_by") {
                    $order_by1=$group_by;
                } else {
                    if ($order_by == $this->inputTrafficField ||
                        $order_by == $this->outputTrafficField ||
                        $order_by == $this->durationField ||
                        $order_by == $this->priceField ||
                        $order_by == "zeroP" ||
                        $order_by == "nonzeroP" )  {
                        $order_by1=$order_by;
                    } else {
                        $order_by1="calls";
                    }
                }

                $this->SipMethodField=$this->CDRFields['SipMethod'];
                $query= "
                select sum($this->durationField) as $this->durationField,
                SEC_TO_TIME(sum($this->durationField)) as hours,
                count($group_by) as calls,
                $this->SipMethodField,
                2*sum($this->inputTrafficField)/1024/1024 as $this->inputTrafficField,
                2*sum($this->outputTrafficField)/1024/1024 as $this->outputTrafficField,
                SUM($this->durationField = '0') as zero,
                SUM($this->durationField > '0') as nonzero,
                ";

                if ($order_by=="zeroP" || $order_by=="nonzeroP") {
                    $query.="
                    SUM($this->durationField = '0')/count($group_by)*100 as zeroP,
                    SUM($this->durationField > '0')/count($group_by)*100 as nonzeroP,
                    ";
                }

                $query.="
                sum($this->inputTrafficField)*8*2/1024/sum($this->durationField) as netrate_in,
                sum($this->outputTrafficField)*8*2/1024/sum($this->durationField) as netrate_out
                ";

                if ($this->priceField) {
                    $query.= ", sum($this->priceField) as $this->priceField ";
                }

                $query.= "
                , $group_by as mygroup
                from $cdr_table
                where $where
                group by $group_by
                $having
                order by $order_by1 $order_type
                limit $i,$this->maxrowsperpage
                ";

                $this->CDRdb->query($query);

                $this->showTableHeaderStatistics($begin_datetime,$end_datetime);

                while ($i<$maxrows)  {
                
                    $found=$i+1;
                    $this->CDRdb->next_record();

                    $calls              = $this->CDRdb->f('calls');
                    $seconds            = $this->CDRdb->f($this->durationField);
                    $seconds            = $this->CDRdb->f($this->durationField);
                    $seconds_print      = number_format($this->CDRdb->f($this->durationField),0);
                    $minutes            = number_format($this->CDRdb->f($this->durationField)/60,0,"","");
                    $minutes_print      = number_format($this->CDRdb->f($this->durationField)/60,0);
                    $hours              = $this->CDRdb->f('hours');

                    $AcctInputOctets    = number_format($this->CDRdb->f($this->inputTrafficField),2,".","");
                    $AcctOutputOctets   = number_format($this->CDRdb->f($this->outputTrafficField),2,".","");
                    $NetRateIn          = $this->CDRdb->f('netrate_in');
                    $NetRateOut         = $this->CDRdb->f('netrate_out');
                    $SipMethod          = $this->CDRdb->f($this->callTypeField);
                    $AcctTerminateCause = $this->CDRdb->f($this->disconnectField);
                    $mygroup            = $this->CDRdb->f('mygroup');

                    $zero               = $this->CDRdb->f('zero');
                    $nonzero            = $this->CDRdb->f('nonzero');
                    $success            = number_format($nonzero/$calls*100,2,".","");
                    $failure            = number_format($zero/$calls*100,2,".","");

                    $NetworkRateIn      = number_format($NetRateIn,2);
                    $NetworkRateOut     = number_format($NetRateOut,2);
                    $NetworkRate        = max($NetworkRateIn,$NetworkRateOut);

                    if ($this->priceField) {
                        $price          = $this->CDRdb->f($this->priceField);
                    }

                    $rr=floor($found/2);
                    $mod=$found-$rr*2;
                
                    if ($mod ==0) {
                        $inout_color="lightgrey";
                    } else {
                        $inout_color="white";
                    }

                    $traceValue="";
                    $mygroup_print=quoted_printable_decode($mygroup);

                    if ($this->group_byOrig==$this->DestinationIdField) {
                        if ($this->CDRTool['filter']['domain'] && $this->destinations[$this->CDRTool['filter']['domain']]) {
                            list($_dst_id,$_dst_name)=$this->getPSTNDestinationId($mygroup,'',$this->CDRTool['filter']['domain']);
                            $description=$_dst_name;
                        } else {
                            $description=$this->destinations[0]["default"][$mygroup]["name"];
                            //list($_dst_id,$_dst_name)=$this->getPSTNDestinationId($mygroup);
                            //$description=$_dst_name;
                        }

                        if ($mygroup) {
                            $traceValue=$mygroup;
                        } else {
                            $traceValue="empty";
                        }

                    } else if ($this->group_byOrig==$this->aNumberField) {
                        # Normalize Called Station Id
                        $N=$this->NormalizeNumber($mygroup);
                        $mygroup_print=$N['username']."@".$N[domain];
                        $description="";
                        $traceField="a_number";
                        $traceValue=urlencode($mygroup);
                    } else if ($this->group_byOrig==$this->CanonicalURIField) {
                        $traceField="c_number";
                        $traceValue=urlencode($mygroup);
                    } else if ($this->group_byOrig==$this->SipProxyServerField) {
                        $traceField="sip_proxy";
                        $traceValue=urlencode($mygroup);
                    } else if ($this->group_byOrig==$this->SipCodecField) {
                        $traceField="SipCodec";
                    } else if (preg_match("/UserAgent/",$this->group_byOrig)) {
                        $traceField="UserAgent";
                    } else if (preg_match("/^BY/",$this->group_byOrig)) {
                        $traceField="MONTHYEAR";
                    } else if ($this->group_byOrig==$this->callIdField) {
                        $traceField="call_id";
                    } else if ($this->group_byOrig=="DAYOFWEEK") {
                        if ($mygroup == "1") {
                            $description="Sunday";
                        } else if ($mygroup == "2") {
                            $description="Monday";
                        } else if ($mygroup == "3") {
                            $description="Tuesday";
                        } else if ($mygroup == "4") {
                            $description="Wednesday";
                        } else if ($mygroup == "5") {
                            $description="Thursday";
                        } else if ($mygroup == "6") {
                            $description="Friday";
                        } else if ($mygroup == "7") {
                            $description="Saturday";
                        }
                    } else if ($this->group_byOrig=="DAYOFMONTH") {
                        $description    =$this->CDRdb->f('day');
                    } else if ($this->group_byOrig=="DAYOFYEAR") {
                        $description    =$this->CDRdb->f('day');
                    } else if ($this->group_byOrig=="SourceIP") {
                        $traceField="gateway";
                    } else if ($this->group_byOrig=="SipResponseCode") {
                        $description    =$this->disconnectCodesDescription[$mygroup];
                        $traceField="sip_status";
                    } else if ($this->group_byOrig=="SipApplicationType") {
                        $traceField="application";
                    } else if ($this->group_byOrig=="ServiceType") {
                        $traceField="flow";
                    } else {
                        $description="";
                    }

                    if (!$traceField) {
                        $traceField    = $group_by;
                    }

                    if (!$traceValue) {
                        $traceValue    = $mygroup;
                    }

                    if (!$traceValue) {
                        $traceValue="";
                        $comp_type="empty";
                    } else {
                        $comp_type="begin";
                    }

                    $traceValue_enc=urlencode($traceValue);

                    if (!$this->export) {
                        print "
                        <tr bgcolor=$inout_color>
                        <td><b>$found</b></td>
                        <td align=right>$calls</td>
                        <td align=right>$seconds_print</td>
                        <td align=right>$minutes_print</td>
                        <td align=right>$hours</td>
                        ";
                        if ($perm->have_perm("showPrice")) {
                            $pricePrint=number_format($price,4,".","");
                        } else {
                            $pricePrint='x.xxx';
                        }
                        print "
                        <td align=right>$pricePrint</td>
                        <td align=right>$AcctInputOctets</td>
                        <td align=right>$AcctOutputOctets</td>
                        <td align=right>$success%</td>
                        <td align=right>($nonzero calls)</td>
                        <td align=right>$failure%</td>
                        <td align=right>($zero calls)</td>
                        <td>$mygroup_print</td>
                        <td>$description</td>
                        <td>";
                        printf("<a href=%s&%s=%s&%s_comp=%s target=_new>Display calls</a></td>",$url_calls,$traceField,$traceValue_enc,$traceField,$comp_type);
                        print "
                        </tr>
                        ";
                    } else {
                         print "$found,";
                         print "$calls,";
                         print "$seconds,";
                         print "$minutes,";
                         print "$hours,";
                         if ($perm->have_perm("showPrice")) {
                             $pricePrint=$price;
                         } else {
                             $pricePrint='x.xxx';
                         }
                         print "$pricePrint,";
                         print "$AcctInputOctets,";
                         print "$AcctOutputOctets,";
                         print "$success,";
                         print "$nonzero,";
                         print "$failure,";
                         print "$zero,";
                         print "$mygroup_print,";
                         print "$description";
                         print "\n";
                    }
                    $i++;
                 }

                 if (!$this->export) {
                    print "
                    </table>
                    </td>
                    </tr>
                    </table>
                    ";
                 }
        
            } else {
                if (!$this->export) {
                    printf ("For more information about each call click on its Id column.</div> ");
                }

                if ($order_by=="zeroP" || $order_by=="nonzeroP") {
                    $order_by="timestamp";
                }
                $query=sprintf("select *, UNIX_TIMESTAMP($this->startTimeField) as timestamp
                from %s where %s order by %s %s limit %d, %d",
                addslashes($cdr_table),
                $where,
                addslashes($order_by),
                addslashes($order_type),
                $i,
                $this->maxrowsperpage
                );

                $this->CDRdb->query($query);

                if ($this->CDRTool['filter']['aNumber']) {
                    $this->showTableHeaderSubscriber($begin_datetime,$end_datetime);
                } else {
                    if (!$this->export) {
                        $this->showTableHeader($begin_datetime,$end_datetime);
                    } else {
                        $this->showExportHeader();
                    }
                }

                while ($i<$maxrows)  {
                    global $found;
                    $found=$i+1;
                    $this->CDRdb->next_record();                                            

                    $Structure=$this->_readCDRFieldsFromDB();
                    //dprint_r($Structure);
                    $CDR = new $this->CDR_class($this, $Structure);

                    if ($this->CDRTool['filter']['aNumber']) {
                        $CDR->showSubscriber();
                    } else {
                         if (!$this->export) {
                            $CDR->show();
                        } else {
                            $CDR->export();
                        }
                    }

                    $i++;
                 }

                 if (!$this->export) {
                    print "
                    </table>
                    </td>
                    </tr>
                    </table>
                    ";
                 }
        
            }

            $this->showPagination($this->next,$maxrows);
        }
    }

    function LoadDomains() {

        if (!$this->db_subscribers) {
            $log=printf("Error: Cannot load domains because db_subscribers is not defined in datasource %s",$this->cdr_source);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (!is_object($this->AccountsDB)) {
            $log=printf("Error: AccountsDB is not a valid database object");
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (strlen($this->DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->domain_table          = "sip_domains";
        } else {
            $this->domain_table          = "domain";
        }

        $query=sprintf("select * from %s",$this->domain_table);

        if ($this->CDRTool['filter']['aNumber']) {
            $els=explode("@",$this->CDRTool['filter']['aNumber']);
            $query.= sprintf(" where domain = '%s' ", addslashes($els[1]));
        } else if ($this->CDRTool['filter']['domain']) {
            $fdomain=$this->CDRTool['filter']['domain'];
            $query.=sprintf(" where domain = '%s' ", addslashes($fdomain));
        }

        if (!$this->AccountsDB->query($query)) {
            $log=sprintf ("Database %s error: %s (%d) %s\n",$this->db_subscribers,$this->AccountsDB->Error,$this->AccountsDB->Errno,$query);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        while($this->AccountsDB->next_record()) {
            if ($this->AccountsDB->f('domain')) {
                $this->localDomains[$this->AccountsDB->f('domain')]=array('name'     => $this->AccountsDB->f('domain'),
                                                                          'reseller' => intval($this->AccountsDB->f('reseller_id'))
                                                                          );
            }
        }

        return count($this->localDomains);
    }

    function LoadTrustedPeers() {

        if (!$this->db_subscribers) {
            $log=printf("Error: Cannot load trusted peers because db_subscribers is not defined in datasource %s",$this->cdr_source);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (!is_object($this->AccountsDB)) {
            $log=printf("Error: AccountsDB is not a valid database object");
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (strlen($this->DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->trusted_table          = "sip_trusted";
        } else {
            $this->trusted_table          = "trusted_peers";
        }

        $query=sprintf("select * from %s",addslashes($this->trusted_table));

        if (!$this->AccountsDB->query($query)) {
            $log=sprintf ("Database %s error: %s (%d) %s\n",$this->db_subscribers,$this->AccountsDB->Error,$this->AccountsDB->Errno,$query);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        while($this->AccountsDB->next_record()) {
            if ($this->AccountsDB->f('ip')) {
                $this->trustedPeers[$this->AccountsDB->f('ip')]=array('ip'     => $this->AccountsDB->f('ip'),
                                                                          'reseller' => intval($this->AccountsDB->f('reseller_id'))
                                                                          );
            }
        }

        return count($this->trustedPeers);
    }

    function getQuota($account) {

        if (!$this->quotaEnabled) return true;

        if (!$account) return;

        if (!is_object($this->AccountsDB)) {
            $log=printf("Error: AccountsDB is not a valid database object");
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {

            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query 1 %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                $this->AccountsDB->next_record();
                $_profile=json_decode(trim($this->AccountsDB->f('profile')));
                return $_profile->quota;

            } else {
                return 0;
            }
        } else {

            $query=sprintf("select quota from subscriber where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                $this->AccountsDB->next_record();
                return $this->AccountsDB->f('quota');
            } else {
                return 0;
            }
        }
    }

    function getBlockedByQuotaStatus($account) {

        if (!$this->quotaEnabled) return true;

        if (!$account) return 0;

        if (!is_object($this->AccountsDB)) {
            $log=printf("Error: AccountsDB is not a valid database object");
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));
    
            if (!$this->AccountsDB->query($query)) {

                $log=sprintf ("Database error for query2 %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                $this->AccountsDB->next_record();

                $_profile=json_decode(trim($this->AccountsDB->f('profile')));
                if (in_array('quota',$_profile->groups)) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            $query=sprintf("select CONCAT(username,'@',domain) as account from grp where grp = 'quota' and username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                return 1;
            } else {
                return 0;
            }

        }

        return 0;
    }

    function notifyLastSessions($count='200',$account='') {
        // send emails with last missed and received sessions to subscribers in group $this->missed_calls_group

        $lockName=sprintf("%s:notifySessions",$this->cdr_source);

        if (!$this->getNormalizeLock($lockName)) {
            return true;
        }

        if (strlen($account)) {
            list($username,$domain)=explode('@',$account);
            if (!strlen($username) || !strlen($domain)) return false;
        } else {
            $query=sprintf("select * from memcache where `key` = '%s'",'notifySessionsLastRun');
            $this->cdrtool->query($query);
            if ($this->cdrtool->num_rows()) {
                $this->cdrtool->next_record();
                $lastRun=$this->cdrtool->f('value');
                if (Date('Y-m-d') == $lastRun) {
                    $log=sprintf("Notify sessions script already run for date %s\n",$lastRun);
                    print $log;
                    syslog(LOG_NOTICE,$log);
                    return true;
                }
            }
        }

        $this->notifySubscribers=array();

        require_once('Mail.php');
        require_once('Mail/mime.php');

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts");
            if (strlen($account)) {
                $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));
            }
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                while ($this->AccountsDB->next_record()) {
                    $_profile=json_decode(trim($this->AccountsDB->f('profile')));
                    if (in_array($this->missed_calls_group,$_profile->groups)) {
                        $this->notifySubscribers[$this->AccountsDB->f('username').'@'.$this->AccountsDB->f('domain')]=array('email'=>$this->AccountsDB->f('email'),'timezone' => $_profile->timezone);
                    }
                }
            } else {
                return 0;
            }

        } else {
            $query=sprintf("select CONCAT(username,'@',domain) as account from grp where grp = '%s'",addslashes($this->missed_calls_group));
            if (strlen($account)) {
                $query.= sprintf (" and username = '%s' and domain = '%s' ",$username,$domain);
            }
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                while ($this->AccountsDB->next_record()) {
                    $this->notifySubscribers[$this->AccountsDB->f('account')]=array('email'=>$this->AccountsDB->f('email'),'timezone' => $this->AccountsDB->f('timezone'));
                }
            } else {
                return 0;
            }
        }

        if (!count($this->notifySubscribers)) return 0;

        $j=0;
        foreach (array_keys($this->notifySubscribers) as $_subscriber) {
            $j++;
            $_last_sessions=array();
            unset($textBody);
            unset($htmlBody);

            $query = sprintf("SELECT *, UNIX_TIMESTAMP(%s) as timestamp FROM %s where (%s = '%s' or %s = '%s') and %s > DATE_ADD(NOW(), INTERVAL -1 day) order by %s desc limit 200",
            addslashes($this->startTimeField),
            addslashes($this->table),
            addslashes($this->usernameField),
            addslashes($_subscriber),
            addslashes($this->CanonicalURIField),
            addslashes($_subscriber),
            addslashes($this->startTimeField),
            addslashes($this->startTimeField)
            );

            if (!$this->CDRdb->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->CDRdb->Error,$this->CDRdb->Errno);
                syslog(LOG_NOTICE,$log);
                print $log;
                return 0;
            }

            if (Date('d')== 1) {
                while ($this->CDRdb->next_record()) {
                    $_last_sessions[]=array('duration'  => $this->CDRdb->f($this->durationField),
                                            'from'      => $this->CDRdb->f($this->aNumberField),
                                            'to'        => $this->CDRdb->f($this->cNumberField),
                                            'username'  => $this->CDRdb->f($this->usernameField),
                                            'canonical' => $this->CDRdb->f($this->CanonicalURIField),
                                            'date'      => getlocaltime($this->notifySubscribers[$_subscriber]['timezone'],$this->CDRdb->f('timestamp'))
                                          );
                }

                if (preg_match("/^(\w+)(\d{4})(\d{2})$/",$this->table,$m))  {
                    $previousTable=$m[1].date('Ym', mktime(0, 0, 0, $m[3]-1, "01", $m[2]));
                    $query = sprintf("SELECT *, UNIX_TIMESTAMP(%s) as timestamp FROM %s where %s = '%s' and %s > DATE_ADD(NOW(), INTERVAL -1 day) order by %s desc limit 200",
                    addslashes($this->startTimeField),
                    addslashes($previousTable),
                    addslashes($this->CanonicalURIField),
                    addslashes($_subscriber),
                    addslashes($this->startTimeField),
                    addslashes($this->startTimeField)
                    );

                    if (!$this->CDRdb->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->CDRdb->Error,$this->CDRdb->Errno);
                        syslog(LOG_NOTICE,$log);
                        print $log;
                        return 0;
                    }
                    while ($this->CDRdb->next_record()) {
                        $_last_sessions[]=array('duration'   => $this->CDRdb->f($this->durationField),
                                                'from'       => $this->CDRdb->f($this->aNumberField),
                                                'to'         => $this->CDRdb->f($this->cNumberField),
                                                'username'   => $this->CDRdb->f($this->usernameField),
                                                'canonical'  => $this->CDRdb->f($this->CanonicalURIField),
                                                'date'       => getlocaltime($this->notifySubscribers[$_subscriber]['timezone'],$this->CDRdb->f('timestamp'))
                                              );
                    }
                }

            } else {
                while ($this->CDRdb->next_record()) {
                    $_last_sessions[]=array('duration'  => $this->CDRdb->f($this->durationField),
                                            'from'      => $this->CDRdb->f($this->aNumberField),
                                            'to'        => $this->CDRdb->f($this->cNumberField),
                                            'username'  => $this->CDRdb->f($this->usernameField),
                                            'canonical' => $this->CDRdb->f($this->CanonicalURIField),
                                            'date'      => getlocaltime($this->notifySubscribers[$_subscriber]['timezone'],$this->CDRdb->f('timestamp'))
                                          );
                }
            }

            if (!count($_last_sessions)) continue;

            $sessions=array('missed'   => array(),
                            'received' => array(),
                            'diverted' => array()
                            );

            $have_sessions=0;

            foreach ($_last_sessions as $_s) {
                if ($_s['duration'] == 0 && $_s['canonical'] == $_subscriber) {
                    $sessions['missed'][]=$_s;
                    $have_sessions++;
                    continue;
                }

                if ($_s['duration'] > 0 && $_s['canonical'] == $_subscriber) {
                    $sessions['received'][]=$_s;
                    $have_sessions++;
                    continue;
                }

                if ($_s['from'] != $_subscriber && $_s['canonical'] != $_subscriber) {
                    $sessions['diverted'][]=$_s;
                    $have_sessions++;
                    continue;
                }
            }

            if (!$have_sessions) continue;;

            if (count($sessions['missed'])) {
                // missed sessions
                $textBody .= sprintf ("Missed sessions\n\n
                Id,Date,From,Duration\n
                ");
    
                $htmlBody .= sprintf ("<h2>Missed Calls</h2>
                <p>
                <table border=0>
                <tr>
                <th>
                </th>
                <th>Date and Time
                </th>
                <th>Caller
                </th>
                </tr>
                ");
    
                $i=0;
                foreach ($sessions['missed'] as $_session) {
                    $i++;
                    if ($i >= $count) break;
    
                    $htmlBody.=sprintf ("<tr><td>%s</td><td>%s</td><td><a href=sip:%s>sip:%s</a></td></tr>",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['from']
                    );
    
    
                    $txtBody.=sprintf ("%s,%s,%s,%s,%s\n",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['to']
                    );
                }
    
                $htmlBody.="</table>";
            }

            if (count($sessions['diverted'])) {

                // diverted sessions
                $textBody .= sprintf ("Diverted Calls\n\n
                Id,Date,From,Diverted to\n
                ");
    
                $htmlBody .= sprintf ("<h2>Diverted Calls</h2>
                <p>
                <table border=0>
                <tr>
                <th>
                </th>
                <th>Date and Time
                </th>
                <th>Caller
                </th>
                <th>Diverted to
                </th>
                </tr>
                ");
    
                $i=0;
                foreach ($sessions['diverted'] as $_session) {
                    $i++;
                    if ($i >= $count) break;
    
                    $htmlBody.=sprintf ("<tr><td>%s</td><td>%s</td><td><a href=sip:%s>sip:%s</a></td><td>%s</td></tr>",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['from'],
                    $_session['canonical']
                    );
    
                    $txtBody.=sprintf ("%s,%s,%s,%s\n",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['canonical']
                    );
                }
    
                $htmlBody.="</table>";
            }

            if (count($sessions['received'])) {

                // received sessions
                $textBody .= sprintf ("Received Calls\n\n
                Id,Date,From,Duration\n");
    
                $htmlBody .= sprintf ("<h2>Received Calls</h2>
                <p>
                <table border=0>
                <tr>
                <th>
                </th>
                <th>Date and Time
                </th>
                <th>Caller
                </th>
                <th>Duration
                </th>
                </tr>
                ");
    
                $i=1;
                foreach ($sessions['received'] as $_session) {
    
                    if ($i >= $count) break;
    
                    $htmlBody.=sprintf ("<tr><td>%s</td><td>%s</td><td><a href=sip:%s>sip:%s</a></td><td>%s</td></tr>",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['from'],
                    $_session['duration']
                    );
    
    
                    $txtBody.=sprintf ("%s,%s,%s,%s\n",
                    $i,
                    $_session['date'],
                    $_session['from'],
                    $_session['duration']
                    );
    
                    $i++;
                }
    
                $htmlBody.="</table>";
            }

            $htmlBody.="<p>This is an automatically generated message, do not reply.";
            $txtBody.="\nThis is an automatically generated message, do not reply.\n";

            $crlf = "\n";
               $hdrs = array(
                     'From'=> $this->CDRTool['provider']['fromEmail'],
                     'Subject' => sprintf("Incoming Calls for %s on %s",$_subscriber,date('Y-m-d'))
                     );

            $mime = new Mail_mime($crlf);
            $mime->setTXTBody($textBody);
            $mime->setHTMLBody($htmlBody);

            $body = $mime->get();     
            $hdrs = $mime->headers($hdrs);

            $mail =& Mail::factory('mail');
            $mail->send($this->notifySubscribers[$_subscriber]['email'], $hdrs, $body);

            $log=sprintf("Notify %s at %s with last %d sessions\n",
            $_subscriber,
            $this->notifySubscribers[$_subscriber]['email'],
            count($_last_sessions));
            print $log;
            syslog(LOG_NOTICE,$log);
        }

        $query=sprintf("update memcache set `value` = '%s' where `key` = '%s'",Date('Y-m-d'),'notifySessionsLastRun');

        if (!$this->cdrtool->query($query)) {
            $log=sprintf("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (!$this->cdrtool->affected_rows()) {
            $query=sprintf("insert into memcache (`value`,`key`) values ('%s','%s')",Date('Y-m-d'),'notifySessionsLastRun');
            if (!$this->cdrtool->query($query)) {
                if ($this->cdrtool->Errno != 1062) {
                    $log=sprintf("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                    print $log;
                    syslog(LOG_NOTICE,$log);
                    return false;
                }
            }
        }
    }

    function getCallerId($account) {
        if (!$account) {
            return NULL;
        }

        if ($this->callerid_cache[$account]) {
            return $this->callerid_cache[$account];
        }

        list($username,$domain) = explode('@',$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return NULL;
            }
    
            if ($this->AccountsDB->num_rows()) {
                $this->AccountsDB->next_record();
                $_profile=json_decode(trim($this->AccountsDB->f('profile')));
                $this->callerid_cache[$account]=$_profile->rpid;
                return $_profile->rpid;
            }
        } else {
            $query=sprintf("select rpid from subscriber where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return NULL;
            }

            if ($this->AccountsDB->num_rows()) {
                $this->AccountsDB->next_record();
                $rpid = $this->AccountsDB->f('rpid');
                $this->callerid_cache[$account]=$rpid;
                return $rpid;
            }
        }
        return NULL;
    }

    function rate_on_net_enabled($username, $domain) {
        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return false;
            }

            if ($this->AccountsDB->num_rows()) {
                while ($this->AccountsDB->next_record()) {
                    $_profile=json_decode(trim($this->AccountsDB->f('profile')));
                    if (in_array($this->rate_on_net_group,$_profile->groups)) {
                       return true;
                    }
                }
            }
        }
        return false;
    }
}

class CDR_opensips extends CDR {

    var $show_in_icon  = 0;
    var $show_out_icon = 0;
    var $CalleeCallerId = NULL;

    function CDR_opensips($parent, $CDRfields) {

        $this->CDRS = $parent;

        $this->cdr_source  = $this->CDRS->cdr_source;

        //dprint_r($CDRfields);
        foreach (array_keys($this->CDRS->CDRFields) as $field) {
            $this->$field = $CDRfields[$this->CDRS->CDRFields[$field]];
        }

        if ($this->CanonicalURI) {
            $this->CanonicalURI   = quoted_printable_decode($this->CanonicalURI);
        }

        if ($this->RemoteAddress) {
            $this->RemoteAddress  = quoted_printable_decode($this->RemoteAddress);
        }

        if ($this->BillingPartyId) {
            $this->BillingPartyId = quoted_printable_decode($this->BillingPartyId);
        }

        if ($this->aNumber) {
            $this->aNumber        = quoted_printable_decode($this->aNumber);
        }

        if ($this->cNumber) {
            $this->cNumber        = quoted_printable_decode($this->cNumber);
        }

        if ($this->SipRPID) {
            $this->SipRPID        = quoted_printable_decode($this->SipRPID);
        }
        
        if (!$this->application && $this->SipMethod) {
            $_method=strtolower($this->SipMethod);
            if ($_method == 'message') {
                $this->application = 'message';
                $this->stopTimeNormalized=$this->startTime;
            } else {
                $this->application = 'audio';
            }
        }

        if ($this->application == 'message') {
            $this->stopTimeNormalized=$this->startTime;
        }

        $this->application=strtolower($this->application);

        $this->application_print=quoted_printable_decode($this->application);

        $this->FromHeaderPrint = quoted_printable_decode($this->FromHeader);
        if (strstr($this->FromHeaderPrint,';')) {
            $_els=explode(";",$this->FromHeaderPrint);
            $this->FromHeaderPrint=$_els[0];
        }

        $this->FromHeaderPrint = htmlentities($this->FromHeaderPrint);

        $this->UserAgentPrint  = quoted_printable_decode($this->UserAgent);

        if (strstr($this->application,'audio')) {
            $this->application='audio';
        }

        if (!in_array($this->application,$this->supportedApplicationTypes)) {
            $this->application = $this->defaultApplicationType;
        }

        //$this->applicationNormalized=$this->application;

        if ($this->aNumber) {
            $NormalizedNumber        = $this->CDRS->NormalizeNumber($this->aNumber,"source");
            $this->aNumberPrint      = $NormalizedNumber['NumberPrint'];
            $this->aNumberNormalized = $NormalizedNumber['Normalized'];
            $this->aNumberUsername   = $NormalizedNumber['username'];
            $this->aNumberDomain     = $NormalizedNumber['domain'];
        }

        if (!$this->BillingPartyId || $this->BillingPartyId == 'n/a') {
            $this->BillingPartyId=$this->aNumberPrint;
        }

        $this->ResellerId=0;
        // calculate reseller
        $_billing_party_els=explode("@",$this->BillingPartyId);
        if ($this->isBillingPartyLocal()) {
            $this->ResellerId = $this->CDRS->localDomains[$_billing_party_els[1]]['reseller'];
        } else {
            if (!strlen($_billing_party_els[0])) $this->BillingPartyId=$_billing_party_els[1];
            if (count($_billing_party_els)==2) {
                if (!$this->domain) $this->domain=$_billing_party_els[1];
                if ($this->CDRS->localDomains[$_billing_party_els[1]]['reseller']) {
                    $this->ResellerId = $this->CDRS->localDomains[$_billing_party_els[1]]['reseller'];
                } else if ($this->CDRS->trustedPeers[$_billing_party_els[1]]['reseller']) {
                    $this->ResellerId = $this->CDRS->trustedPeers[$_billing_party_els[1]]['reseller'];
                }
            } else if (count($_billing_party_els)==1) {
                $this->ResellerId=$this->CDRS->trustedPeers[$_billing_party_els[0]]['reseller'];
            }
        }

        if (!strlen($this->ResellerId)) {
            $this->ResellerId = 0;
        }

        $this->BillingPartyId=strtolower($this->BillingPartyId);

        $this->BillingPartyIdPrint=$this->BillingPartyId;

        $this->domainNormalized = $this->domain;

        if (is_array($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation_SourceIP']) &&
            in_array($this->SourceIP,array_keys($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation_SourceIP'])) &&
            strlen($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation_SourceIP'][$this->SourceIP])) {

            $this->domainNormalized=$this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation_SourceIP'][$this->SourceIP];
        } else if (is_array($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation']) &&
            in_array($this->domain,array_keys($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation'])) &&
            strlen($this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation'][$this->domain])) {

            $this->domainNormalized=$this->CDRS->DATASOURCES[$this->cdr_source]['domainTranslation'][$this->domain];
        }
     
        $this->domainNormalized=strtolower($this->domainNormalized);

        $this->RemoteAddressPrint=quoted_printable_decode($this->RemoteAddress);

        $_timestamp_stop=$this->timestamp+$this->duration;

        $this->dayofweek       = date("w",$this->timestamp);
        $this->hourofday       = date("G",$this->timestamp);
        $this->dayofyear       = date("Y-m-d",$this->timestamp);

        // Called Station ID or cNumber should not be used for rating purposes because
        // it is chosen by the subscriber but the Proxy rewrites it into a different
        // final destination (the Canonical URI)

        // Canonical URI is the final logical SIP destination after all
        //   lookups like aliases, usrloc , call forwarding, ENUM
        //   mappings or PSTN gateways but before the DNS lookup
        //   Canonical URI must be saved in the SIP Proxy and added as an extra
        //   Radius attribute in the Radius START packet

        if (!$this->CanonicalURI) {
            if ($this->RemoteAddress) {
                $this->CanonicalURI=$this->RemoteAddress;
            } else if ($this->cNumber) {
                $this->CanonicalURI=$this->cNumber;
            }
        }

        if ($this->CanonicalURI) {
            $this->CanonicalURIPrint            = $this->CanonicalURI;
            $NormalizedNumber                   = $this->CDRS->NormalizeNumber($this->CanonicalURI,"destination",$this->BillingPartyId,$this->domain,$this->gateway,'',$this->ENUMtld,$this->ResellerId);
            $this->CanonicalURINormalized       = $NormalizedNumber['Normalized'];
            $this->CanonicalURIUsername         = $NormalizedNumber['username'];
            $this->CanonicalURIDomain           = $NormalizedNumber['domain'];
            $this->CanonicalURIPrint            = $NormalizedNumber['NumberPrint'];
            $this->CanonicalURIDelimiter        = $NormalizedNumber['delimiter'];
            $this->CanonicalURIE164             = $NormalizedNumber['E164'];

            // Destination Id is used for rating purposes
            $this->DestinationId                = $NormalizedNumber['DestinationId'];
            $this->destinationName              = $NormalizedNumber['destinationName'];
            $this->region                       = $NormalizedNumber['region'];
        }

        if ($this->cNumber) {
            $NormalizedNumber                   = $this->CDRS->NormalizeNumber($this->cNumber,"destination",$this->BillingPartyId,$this->domain,$this->gateway,'',$this->ENUMtld,$this->ResellerId);
            $this->cNumberNormalized            = $NormalizedNumber['Normalized'];
            $this->cNumberUsername              = $NormalizedNumber['username'];
            $this->cNumberDomain                = $NormalizedNumber['domain'];
            $this->cNumberPrint                 = $NormalizedNumber['username'].$NormalizedNumber['delimiter'].$NormalizedNumber['domain'];
            $this->cNumberDelimiter             = $NormalizedNumber['delimiter'];
            $this->cNumberE164                  = $NormalizedNumber['E164'];
        }

        if ($this->RemoteAddress) {
            // Next hop is the real destination after all lookups including DNS
            $NormalizedNumber                   = $this->CDRS->NormalizeNumber($this->RemoteAddress,"destination",$this->BillingPartyId,$this->domain,$this->gateway,'',$this->ENUMtld,$this->ResellerId);
            $this->RemoteAddressPrint           = $NormalizedNumber['NumberPrint'];
            $this->RemoteAddressNormalized      = $NormalizedNumber['Normalized'];
            $this->RemoteAddressDestinationId   = $NormalizedNumber['DestinationId'];
            $this->RemoteAddressDestinationName = $NormalizedNumber['destinationName'];
            $this->RemoteAddressUsername        = $NormalizedNumber['username'];
            $this->RemoteAddressDelimiter       = $NormalizedNumber['delimiter'];
            $this->RemoteAddressE164            = $NormalizedNumber['E164'];
    
            $this->remoteGateway                = $NormalizedNumber['domain'];
            $this->remoteUsername               = $NormalizedNumber['username'];
    
        }

        $this->isCalleeLocal();
        $this->isCallerLocal();

        if ($this->CallerIsLocal) {
            if ($this->aNumberPrint == $this->BillingPartyId) {
                // call is not diverted

                if ($this->CalleeIsLocal) {
                    $this->flow = 'on-net';
                } else {
                    $this->flow = 'outgoing';
                }
            } else {
                // call is diverted
                if ($this->CalleeIsLocal) {
                    $this->flow = 'on-net-diverted-on-net';
                } else {
                    $this->flow = 'on-net-diverted-off-net';
                }
            }
        } else {
            if ($this->isBillingPartyLocal()) {
                // call is diverted by local user
                if ($this->CalleeIsLocal) {
                    $this->flow = 'diverted-on-net';
                } else {
                    $this->flow = 'diverted-off-net';
                }
            } else if ($this->CalleeIsLocal) {
                $this->flow = 'incoming';
            } else {
                // transit from trusted peer
                $this->flow = 'transit';
            }
        }

        if (($this->flow == 'on-net' || $this->flow == 'diverted-on-net' || $this->flow == 'on-net-diverted-on-net') &&
            $this->application == 'audio' &&
            $this->CDRS->rating_settings['rate_on_net_calls'] &&
            $this->CDRS->rate_on_net_enabled($_billing_party_els[0], $_billing_party_els[1]) &&
            !$this->DestinationId &&
            $this->CalleeCallerId) {
            $_dest = preg_replace("/^\+(\d+)$/","00$1", $this->CalleeCallerId);
            $NormalizedNumber = $this->CDRS->NormalizeNumber($_dest,"destination",$this->BillingPartyId,$this->domain,$this->gateway,'',$this->ENUMtld,$this->ResellerId);
            $this->DestinationId = $NormalizedNumber['DestinationId'];
            $this->destinationName = $NormalizedNumber['destinationName'];
        }

        if ($this->application == "presence") {
            $this->destinationPrint     = $this->cNumberUsername.$this->cNumberDelimiter.$this->cNumberDomain;
            $this->DestinationForRating = $this->cNumberNormalized;

        } else {
            if (!$this->DestinationId) {
                if ($this->CanonicalURIDomain) {
                    $this->destinationPrint = $this->CanonicalURIUsername.$this->CanonicalURIDelimiter.$this->CanonicalURIDomain;
                } else {
                    $this->destinationPrint = $this->cNumberUsername.$this->cNumberDelimiter.$this->cNumberDomain;
                }

                if (strstr($this->CanonicalURINormalized,'@')) {
                    $this->DestinationForRating = $this->CanonicalURINormalized;
                } else {
                    $this->DestinationForRating = $this->RemoteAddressNormalized;
                }

            } else {
                $this->DestinationForRating = $this->CanonicalURINormalized;
                $this->destinationPrint = $this->CanonicalURIPrint;
            }
        }

        if ($this->inputTraffic) {
            $this->inputTrafficPrint  = number_format($this->inputTraffic/1024,2);
        } else {
            $this->inputTrafficPrint=0;
        }

        if ($this->outputTraffic) {
            $this->outputTrafficPrint = number_format($this->outputTraffic/1024,2);
        } else {
            $this->outputTrafficPrint=0;
        }

        if (!$CDRfields['skip_fix_prepaid_duration']) {

            if (!$this->normalized && $this->callId) {
                // fix the duration of prepaid sessions if the prepaid duration is different than radius calculated duration
                $query=sprintf("select duration from prepaid_history
                where session = '%s'
                and destination = '%s'
                order by id desc limit 1",
                addslashes($this->callId),
                addslashes($this->destinationPrint)     // must be synced with maxsession time
                );
    
                if ($this->CDRS->cdrtool->query($query)) {
                    if ($this->CDRS->cdrtool->num_rows()) {
                        $this->CDRS->cdrtool->next_record();
                        $this->durationNormalized = $this->CDRS->cdrtool->f('duration');
                        $this->durationPrint      = sec2hms($this->durationNormalized);
                    } else {
                        $this->durationPrint      = sec2hms($this->duration);
    
                    }
                } else {
                    $log=sprintf("Database error for query %s: %s (%s)",$query,$this->CDRS->cdrtool->Error,$this->CDRS->cdrtool->Errno);
                    syslog(LOG_NOTICE,$log);
                }
            } else {
                $this->durationPrint      = sec2hms($this->duration);
            }
        } else {
            $this->durationPrint      = sec2hms($this->duration);
        }

        if ($this->disconnect) {
            $this->disconnectPrint    = $this->NormalizeDisconnect($this->disconnect);
        }

        $this->traceIn();
        $this->traceOut();

        $this->obfuscateCallerId();

        if ($this->CDRS->rating) {
            global $perm;
            if (is_object($perm) && $perm->have_perm("showPrice")) {
                $this->pricePrint=$this->price;
            } else {
                $this->pricePrint='x.xxx';
            }
        }
    }

    function buildCDRdetail() {
        global $perm;
        global $found;

        if (!is_object($perm)) return;

        $this->geo_location=$this->lookupGeoLocation($this->SourceIP);

        $this->cdr_details="
        <div class=\"alert alert-info\">
          <div class='row-fluid'>
            <div class='span4'>
                <h5>SIP Signalling</h5>

        ";

        $this->cdr_details.= sprintf("
                <a href=%s&call_id=%s><font color=orange>Click here to show only this call id</font></a>
        ",
        $this->CDRS->url_run,
        urlencode($this->callId)
        );

        if ($this->CDRS->sipTrace) {
            $trace_datasource = $this->CDRS->sipTrace;
            $callid_enc       = urlencode(quoted_printable_decode($this->callId));
            $fromtag_enc      = urlencode(quoted_printable_decode($this->SipFromTag));
            $totag_enc        = urlencode(quoted_printable_decode($this->SipToTag));

            $this->traceLink="<a href=\"javascript:void(null);\" onClick=\"return window.open('sip_trace.phtml?cdr_source=$trace_datasource&callid=$callid_enc&fromtag=$fromtag_enc&totag=$totag_enc&proxyIP=$this->SipProxyServer', 'Trace',
            'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=1300px,height=600')\"><font color=red>Click here for the SIP trace</font></a> &nbsp;";

            $this->cdr_details.= "
                <div class=\"row-fluid\">
                <div class=\"span3\">Call id:</div>
                <div class=\"span9\">$this->callId </div>
            </div>
            ";
        }

        $this->cdr_details.= sprintf("
            <div class=\"row-fluid\">
                <div class=\"span12\">%s</div>
            </div>
        ", $this->traceLink);

        $this->cdr_details.= "
        <div class=\"row-fluid\">
            <div class=\"span3\">From tag: </div>
            <div class=\"span9\">$this->SipFromTag</div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">To tag: </div>
            <div class=\"span9\">$this->SipToTag</div>
        </div>                 
        <div class=\"row-fluid\">
            <div class=\"span3\">Start Time:</div>
            <div class=\"span9\">$this->startTime $providerTimezone</div>
        </div>            
        <div class=\"row-fluid\">
            <div class=\"span3\">Stop Time:</div>
            <div class=\"span9\">$this->stopTime</div>
        </div>
        ";

        $this->cdr_details.= sprintf("
        <div class=\"row-fluid\">
            <div class=\"span3\">Country:</div>
            <div class=\"span9\">%s</div>
        </div>
        ",$this->geo_location);

        $this->cdr_details.= "
        <div class=\"row-fluid\">
            <div class=\"span3\">Method:</div>
            <div class=\"span9\">$this->SipMethod from <i>$this->SourceIP:$this->SourcePort</i></div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">From:</div>
            <div class=\"span9\">$this->aNumberPrint</div>
        </div>

        <div class=\"row-fluid\">
            <div class=\"span3\">From Header:</div>
            <div class=\"span9\">$this->FromHeaderPrint</div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">User Agent:</div>
            <div class=\"span9\">$this->UserAgentPrint</div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">Domain:</div>
            <div class=\"span9\">$this->domain</div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">To (dialed URI):</div>
            <div class=\"span9\">$this->cNumberPrint</div>
        </div>
        ";

        if ($this->CanonicalURI) {
        $this->cdr_details.= sprintf("
        <div class=\"row-fluid\">
            
            <div class=\"span3\">Canonical URI:   </div>
            <div class=\"span9\">%s</div>
        </div>
        ",htmlentities($this->CanonicalURI));
        }

        $this->cdr_details.= sprintf("
        <div class=\"row-fluid\">
            
            <div class=\"span3\">Next Hop URI:</div>
            <div class=\"span9\">%s</div>
        </div>
        ",htmlentities($this->RemoteAddress));

        if ($this->DestinationId) {
            $this->cdr_details.= "
            <div class=\"row-fluid\">
                
                <div class=\"span3\">Destination: </div>
                <div class=\"span9\">$this->destinationName ($this->DestinationId)</div>
            </div>
            ";
        }

        if ($this->ENUMtld && $this->ENUMtld != 'none' && $this->ENUMtld != 'N/A') {
            $this->cdr_details.= "
            <div class=\"row-fluid\">
                
                <div class=\"span3\">ENUM TLD: </div>
                <div class=\"span9\">$this->ENUMtld</div>
            </div>
            ";
        }

        if ($this->SipRPID) {
            $this->cdr_details .= "
            <div class=\"row-fluid\">
            
            <div class=\"span3\">Caller ID:  </div>
            <div class=\"span9\">$this->SipRPIDPrint</div>
            </div>
            ";
        }

        if ($this->CalleeCallerId) {
            $this->cdr_details .= "
            <div class=\"row-fluid\">
            
            <div class=\"span3\">Called ID:  </div>
            <div class=\"span9\">$this->CalleeCallerId</div>
            </div>
            ";
        }

        $this->cdr_details.= "
        <div class=\"row-fluid\">
            <div class=\"span3\">Billing Party:</div>
            <div class=\"span9\"><font color=brown>$this->BillingPartyIdPrint</font></div>
        </div>
        <div class=\"row-fluid\">
            <div class=\"span3\">Reseller:</div>
            <div class=\"span9\"><font color=brown>$this->ResellerId</font></div>
        </div>
        </div>
        ";

        $this->cdr_details.= "
        <div class='span3'>
        ";

        if ($this->application != 'message') {
            $this->cdr_details.= "
                <h5>Media Streams</h5>
            ";
    
            if ($this->CDRS->mediaTrace) {
                $media_trace_datasource = $this->CDRS->mediaTrace;
    
                $this->mediaTraceLink="<a href=\"javascript:void(null);\" onClick=\"return window.open('media_trace.phtml?cdr_source=$media_trace_datasource&callid=$callid_enc&fromtag=$fromtag_enc&totag=$totag_enc&proxyIP=$this->SipProxyServer', 'Trace',
                'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=730')\">Click here for media information</a> &nbsp;";
    
                $this->cdr_details.= sprintf("
                <div class=\"row-fluid\">
                    <div class='span12'>%s</div>
                </div>
                ", $this->mediaTraceLink);
    
            }

            $this->SipCodec   = quoted_printable_decode($this->SipCodec);
    
            if ($this->SipCodec) {
                $this->cdr_details.= "
                <div class=\"row-fluid\">
                    <div class=\"span5\">Codecs: </div>
                    <div class=\"span7\">$this->SipCodec</div>
                </div>
                ";
            }
    
            $this->cdr_details.= "
            <div class=\"row-fluid\">
                
                <div class=\"span5\">Caller RTP: </div>
                <div class=\"span7\">$this->inputTrafficPrint KB</div>
            </div>
            <div class=\"row-fluid\">
                
                <div class=\"span5\">Called RTP: </div>
                <div class=\"span7\">$this->outputTrafficPrint KB</div>
            </div>
            ";
    
            if ($this->MediaInfo) {
                $this->cdr_details.= "
                <div class=\"row-fluid\">
                <div class=\"span5\">Media Info:</div>
                <div class=\"span7\"><font color=red>$this->MediaInfo</font></div>
                </div>
                ";
            }

            $this->cdr_details.= "
            <div class=\"row-fluid\">
                <div class=\"span5\">Applications: </div>
                <div class=\"span7\">$this->application_print</div>
            </div>
            ";

        }


        if ($this->SipUserAgents) {
            $this->SipUserAgents   = quoted_printable_decode($this->SipUserAgents);

            $callerAgents=explode("+",$this->SipUserAgents);
            $callerUA=htmlentities($callerAgents[0]);
            $calledUA=htmlentities($callerAgents[1]);

            $this->cdr_details.= "
            <div class=\"row-fluid\">
                <div class=\"span5\">Caller SIP UA: </div>
                <div class=\"span7\">$callerUA</div>
            </div>
            <div class=\"row-fluid\">
                <div class=\"span5\">Called SIP UA: </div>
                <div class=\"span7\">$calledUA</div>
            </div>
            ";
        }

        $this->cdr_details.= "
        </div>";
 
 
        if ($perm->have_perm("showPrice") && $this->normalized) {
            $this->cdr_details.= "
            <div class=\"span3\">
                    <h5>Rating</h5>
            ";
 
            if ($this->price > 0 || $this->rate) {
                $this->ratePrint=nl2br($this->rate);
                $this->cdr_details.= "
                <div class=\"row-fluid\">
                $this->ratePrint
                </div>
                ";
 
            } else {
                $this->cdr_details.= "
                <div class=\"row-fluid\">
                Free call
                </div>
                ";
            }
 
            $this->cdr_details.= "
            </div>
            ";
        }

        $this->cdr_details.=  "
        </div>
        </div>
        ";
    }

    function traceIn () {
        $datasource=$this->CDRS->traceInURL[$this->SourceIP];
        global $DATASOURCES;

        if (!$datasource || !$DATASOURCES[$datasource]) {
            return;
        }

        $tplus     = $this->timestamp+$this->duration+300;
        $tmin      = $this->timestamp-300;
        $c_number  = $this->remoteUsername;
        $cdr_table = Date('Ym',time($this->timestamp));

        $this->traceIn=
                        "<a href=callsearch.phtml".
                        "?cdr_source=$datasource".
                        "&cdr_table=$cdr_table".
                        "&trace=1".
                        "&action=search".
                        "&c_number=$c_number".
                        "&c_number_comp=begins".
                        "&begin_datetime=$tmin".
                        "&end_datetime=$tplus".
                        " target=bottom>".
                        "In".
                        "</a>";
    }

    function traceOut () {
        $datasource=$this->CDRS->traceOutURL[$this->remoteGateway];
        global $DATASOURCES;

        if (!$datasource || !$DATASOURCES[$datasource]) {
            return;
        }

        $tplus     = $this->timestamp+$this->duration+300;
        $tmin      = $this->timestamp-300;
        $c_number  = preg_replace("/^(0+)/","",$this->remoteUsername);
        $cdr_table = Date('Ym',time($this->timestamp));

        $this->traceOut=
                        "<a href=callsearch.phtml".
                        "?cdr_source=$datasource".
                        "&cdr_table=$cdr_table".
                        "&trace=1".
                        "&action=search".
                        "&c_number=$c_number".
                        "&c_number_comp=contain".
                        "&begin_datetime=$tmin".
                        "&end_datetime=$tplus".
                        " target=bottom>".
                        "Out".
                        "</a>";
    }

    function show() {
        $this->buildCDRdetail();

        global $found;
        global $perm;

        $rr=floor($found/2);
        $mod=$found-$rr*2;

        if ($mod ==0) {
            $inout_color="#F9F9F9";
        } else {
            $inout_color="white";
        }

        $this->ratePrint=nl2br($this->rate);

        if ($this->CDRS->Accounts[$this->BillingPartyId]['timezone']) {
            $timezone_print=$this->CDRS->Accounts[$this->BillingPartyId]['timezone'];
        } else {
            $timezone_print=$this->CDRS->CDRTool['provider']['timezone'];
        }

        $found_print=$found;

        if ($this->normalized) $found_print.='N';

        $providerTimezone=$this->CDRS->CDRTool['provider']['timezone'];

        print "
        <tr bgcolor=$inout_color>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><a href=#>$found_print</a></td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->startTime</nobr></td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->flow</nobr></td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->aNumberPrint</td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->geo_location</td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\">$this->SipProxyServer</td>
        <td valign=top onClick=\"return toggleVisibility('row$found')\">$this->application</td>
        <td valign=top><nobr>$this->destinationPrint</nobr>
        ";

        if ($this->DestinationId) {
            if ($this->DestinationId != $this->CanonicalURI) {
                print " ($this->destinationName $this->DestinationId)";
            } else {
                print " ($this->destinationName)";
            }
        }

        print "</td>";

        if (!$this->normalized){
            if ($this->duration > 0 ) {
                 print "<td valign=top align=left colspan=4><font color=red>$this->duration(s)</a></td>";
            } else {
                print "<td valign=top align=left colspan=4><font color=red>in progress</a></td>";
            }
        } else {
            print "
            <td valign=top align=right>$this->durationPrint</td>
            <td valign=top align=right>$this->pricePrint</td>
            <td valign=top align=right>$this->inputTrafficPrint </td>
            <td valign=top align=right>$this->outputTrafficPrint</td>
            ";
        }

        $SIPclass=substr($this->disconnect,0,1);

        if ($SIPclass=="6") {
            $status_color="<span class=\"pull-right label label-important\">";
        } else if ($SIPclass=="5" ) {
            $status_color="<span class=\"pull-right label label-important\">";
        } else if ($SIPclass=="4" ) {
            $status_color="<span class=\"pull-right label label-info\">";
        } else if ($SIPclass=="3" ) {
            $status_color="<span class=\"pull-right label label-success\">";
        } else if ($SIPclass=="2" ) {
            $status_color="<span class=\"pull-right label label-success\">";
        } else  {
            $status_color="<span class=\"pull-right label\">";
        }

        print "
        <td valign=top align=right>$status_color $this->disconnectPrint</span></td>
        <td valign=top align=right>$this->SipCodec</td>
        </tr>
        <tr class=extrainfo id='row$found'>
        <td></td>
        <td colspan=13>$this->cdr_details</th>
        </tr>

        ";
    }

    function export() {
        global $found;

        $disconnectName   = $this->CDRS->disconnectCodesDescription[$this->disconnect];
        $UserAgents       = explode("+",$this->SipUserAgents);
        $CallingUserAgent = trim($UserAgents[0]);
        $CalledUserAgent  = trim($UserAgents[1]);

        print "$found";
        print ",$this->startTime";
        print ",$this->stopTime";
        print ",$this->BillingPartyIdPrint";
        print ",$this->domain";
        print ",$this->SipRPIDPrint";
        print ",$this->aNumberPrint";
        print ",$this->destinationPrint";
        print ",$this->DestinationId";
        print ",$this->destinationName";
        print ",$this->RemoteAddressPrint";
        print ",$this->CanonicalURIPrint";
        print ",$this->duration";
        print ",$this->price";
        print ",$this->SipProxyServer";
        print ",$this->inputTraffic";
        print ",$this->outputTraffic";
        printf(",%s",preg_replace("/,/","/",quoted_printable_decode($CallingUserAgent)));
        printf(",%s",preg_replace("/,/","/",quoted_printable_decode($CalledUserAgent)));
        print ",$this->disconnect";
        print ",$disconnectName";
        printf(",%s",preg_replace("/,/","/",quoted_printable_decode($this->SipCodec)));
        print ",$this->application";
        print "\n";
    }

    function showSubscriber() {
        $this->buildCDRdetail();
        global $found;

        $rr=floor($found/2);
        $mod=$found-$rr*2;

        if ($mod ==0) {
            $inout_color="lightgrey";
        } else {
            $inout_color="white";
        }

        if (!$this->CDRS->export) {
            $timezone_print=$this->CDRS->CDRTool['provider']['timezone'];

            $found_print=$found;
    
            if ($this->normalized) $found_print.='N';

            print "
            <tr bgcolor=$inout_color>
            <td valign=top onClick=\"return toggleVisibility('row$found')\"><a href=#>$found_print</a></td>
            <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->startTime $timezone_print</nobr></td>
            <td valign=top><nobr>$this->aNumberPrint</nobr></td>
            <td valign=top onClick=\"return toggleVisibility('row$found')\"><nobr>$this->geo_location</td>
            <td valign=top onClick=\"return toggleVisibility('row$found')\">$this->SipProxyServer</td>
            <td valign=top><nobr>$this->destinationPrint $this->destinationName</td>
            <td valign=top align=right>$this->durationPrint</td>
            ";

            if ($this->CDRS->rating) {
                print "<td valign=top align=right>$this->pricePrint</td>";
            }

            print "
            <td valign=top align=right>$this->inputTrafficPrint </td>
            <td valign=top align=right>$this->outputTrafficPrint</td>
            </tr>
            ";
            print "
            <tr>
            <td></td>
            <td colspan=11>$this->cdr_details</td>
            </tr>
            ";

        } else {
            $disconnectName=$this->CDRS->disconnectCodesDescription[$this->disconnect];
            $UserAgents=explode("+",$this->SipUserAgents);
            $CallingUserAgent=trim($UserAgents[0]);
            $CalledUserAgent=trim($UserAgents[1]);
            print "$found,$this->startTime,$this->stopTime,$this->BillingPartyId,$this->domain,$this->aNumberPrint,$this->cNumberPrint,$this->DestinationId,$this->destinationName,$this->RemoteAddressPrint,$this->duration,$this->price,$this->SipProxyServer,$this->inputTraffic,$this->outputTraffic,$CallingUserAgent,$CalledUserAgent,$this->disconnect,$disconnectName,$this->SipCodec,$this->application\n";
        }
    }

    function isBillingPartyLocal() {
        $els=explode("@",$this->BillingPartyId);

        if ($els[1] && in_array($els[1],array_keys($this->CDRS->localDomains))) {
            return true;
        }

        return false;
    }

    function isCallerLocal() {
        if (in_array($this->aNumberDomain,array_keys($this->CDRS->localDomains))) {
            $this->CallerIsLocal=true;
            $this->SipRPID = $this->CDRS->getCallerId($this->BillingPartyId);
            $this->SipRPIDPrint = $this->SipRPID;
            #$this->SipRPIDPrint = quoted_printable_decode($this->SipRPID);
        }
    }

    function isCalleeLocal() {
        if (in_array($this->CanonicalURIDomain,array_keys($this->CDRS->localDomains)) && !preg_match("/^0/",$this->CanonicalURIUsername)) {
            $this->CalleeIsLocal=true;
            $this->CalleeCallerId=$this->CDRS->getCallerId($this->CanonicalURI);
        }
    }

    function obfuscateCallerId() {
        global $obfuscateCallerId;
        if ($obfuscateCallerId) {

            //Caller party
            $caller_els=explode("@",$this->aNumberPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='caller';
            }

            if (count($caller_els)== 2) {
                $this->aNumberPrint=$_user.'@'.$caller_els[1];
            } else {
                $this->aNumberPrint=$_user;
            }

            //Billing party
            $caller_els=explode("@",$this->BillingPartyIdPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='party';
            }
    
            $this->BillingPartyIdPrint=$_user.'@'.$caller_els[1];

            // Destination
            $caller_els=explode("@",$this->destinationPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='destination';
            }
    
            if (count($caller_els)== 2) {
                $this->destinationPrint=$_user.'@'.$caller_els[1];
            } else {
                $this->destinationPrint=$_user;
            }

            $caller_els=explode("@",$this->cNumberPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='dialedNumber';
            }
    
            if (count($caller_els)== 2) {
                $this->cNumberPrint=$_user.'@'.$caller_els[1];
            } else {
                $this->cNumberPrint=$_user;
            }

            $caller_els=explode("@",$this->RemoteAddressPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='remoteAddress';
            }
    
            if (count($caller_els)== 2) {
                $this->RemoteAddressPrint=$_user.'@'.$caller_els[1];
            } else {
                $this->RemoteAddressPrint=$_user;
            }

            // Canonical URI
            $caller_els=explode("@",$this->CanonicalURIPrint);
    
            if (is_numeric($caller_els[0]) && strlen($caller_els[0]>3)) {
                $_user=substr($caller_els[0],0,strlen($caller_els[0])-3).'xxx';
            } else {
                $_user='canonicalURI';
            }

            if (count($caller_els)== 2) {
                $this->CanonicalURIPrint=$_user.'@'.$caller_els[1];
            } else {
                $this->CanonicalURIPrint=$_user;
            }

            if (is_numeric($this->SipRPIDPrint) && strlen($this->SipRPIDPrint) > 3) {
                $this->SipRPIDPrint=substr($this->SipRPID,0,strlen($this->SipRPID)-3).'xxx';
            } else {
                $_user='callerId';
            }

            // IP address
            $this->SourceIP='xxx.xxx.xxx.xxx';
        }
    }

}

class SIP_trace {
    var $enableThor  = false;
    var $trace_array = array();
    var $traced_ip   = array();
    var $SIPProxies  = array();
    var $mediaTrace  = false;
    var $thor_nodes  = array();
    var $hostnames   = array();

    function SIP_trace ($cdr_source) {
        global $DATASOURCES, $auth;

        $this->cdr_source = $cdr_source;
        $this->cdrtool    = new DB_CDRTool();

        if (!is_array($DATASOURCES[$this->cdr_source])) {
            $log=sprintf("Error: datasource '%s' is not defined",$this->cdr_source);
            print $log;
            return 0;
        }

        if (strlen($DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->enableThor = $DATASOURCES[$this->cdr_source]['enableThor'];
        }

        if (strlen($DATASOURCES[$this->cdr_source]['mediaTrace'])) {
            $this->mediaTrace = $DATASOURCES[$this->cdr_source]['mediaTrace'];
        }

        if ($this->enableThor) {
            require("/etc/cdrtool/ngnpro_engines.inc");
            require_once("ngnpro_soap_library.php");

            if ($DATASOURCES[$this->cdr_source]['soapEngineId'] && in_array($DATASOURCES[$this->cdr_source]['soapEngineId'],array_keys($soapEngines))) {
                $this->soapEngineId=$DATASOURCES[$this->cdr_source]['soapEngineId'];

                $this->SOAPlogin = array(
                                       "username"    => $soapEngines[$this->soapEngineId]['username'],
                                       "password"    => $soapEngines[$this->soapEngineId]['password'],
                                       "admin"       => true
                                       );
    
                $this->SOAPurl=$soapEngines[$this->soapEngineId]['url'];
        
                $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');
    
                // Instantiate the SOAP client
                $this->soapclient = new WebService_NGNPro_SipPort($this->SOAPurl);
    
                $this->soapclient->setOpt('curl', CURLOPT_TIMEOUT,        5);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
    
                if (is_array($soapEngines[$this->soapEngineId]['hostnames'])) {
                    $this->hostnames=$soapEngines[$this->soapEngineId]['hostnames'];
                }

            } else {
                printf ("<p><font color=red>Error: soapEngineID not defined in datasource %s</font>",$this->cdr_source);
                return false;
            }

        } else {
            $this->table             = $DATASOURCES[$this->cdr_source]['table'];
            $db_class                = $DATASOURCES[$this->cdr_source]['db_class'];
            $this->purgeRecordsAfter = $DATASOURCES[$this->cdr_source]['purgeRecordsAfter'];

            if (class_exists($db_class)) {
                $this->db                = new $db_class;
            } else {
                printf("<p><font color=red>Error: database class '%s' is not defined</font>",$db_class);
                return false;
            }
        }

        if (is_object($auth)) $this->isAuthorized=1;

        if (is_array($DATASOURCES[$this->cdr_source]['SIPProxies'])) {
            $this->SIPProxies=$DATASOURCES[$this->cdr_source]['SIPProxies'];
        }

    }

    function isProxy($ip,$sip_proxy='') {
        if (!$ip) return false;

        if (!$this->enableThor) {
            if (!is_array($this->SIPProxies)) {
                return false;
            }
    
            if (in_array($ip,array_keys($this->SIPProxies))) {
                return true;
            }
        } else if ($sip_proxy) {
            if ($this->thor_nodes[$ip]) {
                return true;
            } else {
                if (isThorNode($ip,$sip_proxy)) {
                    $this->thor_nodes[$ip]=1;
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    function getTrace ($proxyIP,$callid,$fromtag,$totag) {

        if ($this->enableThor) {
            // get trace using soap request
            if (!$proxyIP || !$callid || !$fromtag) return false;
    
            if (!is_object($this->soapclient)) {
                print "Error: soap client is not defined.";
                return false;
            }

            $this->seen_ip=array();

            $filter=array('nodeIp'  => $proxyIP,
                          'callId'  => $callid,
                          'fromTag' => $fromtag,
                          'toTag'   => $totag
                          );
            $this->soapclient->addHeader($this->SoapAuth);
    
            $result     = $this->soapclient->getSipTrace($filter);
    
            if (PEAR::isError($result)) {
                $error_msg   = $result->getMessage();
                $error_fault = $result->getFault();
                $error_code  = $result->getCode();
    
                printf("<font color=red>Error from %s: %s (%s)</font>",$this->SOAPurl,
                $error_fault->detail->exception->errorstring,
                $error_fault->detail->exception->errorcode
                );
                return false;
            }
    
            $columns=0;

            $traces=json_decode($result);

            $trace_array=array();
    
            foreach ($traces as $_trace) {
    
                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/",$_trace->to_ip,$m)) {
                    $toip      = $m[2];
                    $transport = $m[1];
                    $toport    = $m[3];
                } else if (preg_match("/^(.*):(.*)$/",$_trace->to_ip,$m)) {
                    $toip      = $m[1];
                    $transport = 'udp';
                    $toport    = $m[2];
                } else {
                    $toip      = $_trace->to_ip;
                    $transport = 'udp';
                    $toport    = '5060';
                }
    
                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/",$_trace->from_ip,$m)) {
                    $fromip    = $m[2];
                    $fromport  = $m[3];
                } else if (preg_match("/^(.*):(.*)$/",$_trace->from_ip,$m)) {
                    $fromip    = $m[1];
                    $fromport  = $m[2];
                } else {
                    $fromip    = $_trace->from_ip;
                }
    
                if (!$this->seen_ip[$toip] && $this->isProxy($toip)) {
                    $this->seen_ip[$toip]++;
                }
    
                if (!$this->seen_ip[$fromip] && $this->isProxy($fromip)) {
                    $this->seen_ip[$fromip]++;
                }
    
                if (!$this->column[$fromip]) {
                    $this->column[$fromip] = $columns+1;
                    $this->column_port[$fromip]=$fromport;
                    $columns++;
                }
    
                if (!$this->column[$toip]) {
                    $this->column[$toip]   = $columns+1;
                    $this->column_port[$toip]=$toport;
                    $columns++;
                }
    
                preg_match("/^(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/",$_trace->time_stamp,$m);
                $timestamp = mktime($m[4],$m[5],$m[6],$m[2],$m[3],$m[1]);
    
                $idx=$proxyIP.'_'.$_trace->id;
    
                $trace_array[$idx]=
                            array (
                                   'id'         => $idx,
                                   'direction'  => $_trace->direction,
                                   'fromip'     => $fromip,
                                   'toip'       => $toip,
                                   'fromport'   => $fromport,
                                   'toport'     => $toport,
                                   'method'     => $_trace->method,
                                   'transport'  => $transport,
                                   'date'       => $_trace->time_stamp,
                                   'status'     => $_trace->status,
                                   'timestamp'  => $timestamp,
                                   'msg'        => $_trace->message,
                                   'md5'        => md5($_trace->message)
                                   );
            }
    
            $this->trace_array=$trace_array;
            $this->rows = count($this->trace_array);

        } else {
            // get trace from SQL

            if (!is_object($this->db)) {
                print "<p><font color=red>Error: no database connection defined</font>";
                return false;
            }

            $query=sprintf("select *,UNIX_TIMESTAMP(time_stamp) as timestamp
            from %s where callid = '%s' order by id asc",
            addslashes($this->table),
            addslashes($callid));
    
            if (!$this->db->query($query)) {
                printf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                return false;
            }
    
            $this->rows = $this->db->num_rows();
    
            $columns = 0;
    
            while ($this->db->next_record()) {
    
                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/",$this->db->f('toip'),$m)) {
                    $toip      = $m[2];
                    $transport = $m[1];
                    $toport    = $m[3];
                } else if (preg_match("/^(.*):(.*)$/",$this->db->f('toip'),$m)) {
                    $toip      = $m[1];
                    $transport = 'udp';
                    $toport    = $m[2];
                } else {
                    $toip = $this->db->f('toip');
                    $toport    = '5060';
                }

                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/",$this->db->f('fromip'),$m)) {
                    $fromip   = $m[2];
                    $fromport = $m[3];
                } else if (preg_match("/^(.*):(.*)$/",$this->db->f('fromip'),$m)) {
                    $fromip   = $m[1];
                    $fromport = $m[2];
                } else {
                    $fromip = $this->db->f('fromip');
                    $transport = 'udp';
                    $fromport = '5060';
                }

                if (!$this->seen_ip[$toip] && $this->isProxy($toip)) {
                    $this->seen_ip[$toip]++;
                }
    
                if (!$this->seen_ip[$fromip] && $this->isProxy($fromip)) {
                    $this->seen_ip[$fromip]++;
                }

                if (!$this->column[$fromip]) {
                    $this->column[$fromip]=$columns+1;
                    $this->column_port[$fromip]=$fromport;
                    $columns++;
                }
    
                if (!$this->column[$toip]) {
                    $this->column[$toip]=$columns+1;
                    $this->column_port[$toip]=$toport;
                    $columns++;
                }
    
                $this->trace_array[$this->db->f('id')]=
                            array (
                                   'id'        => $this->db->f('id'),
                                   'direction' => $this->db->f('direction'),
                                   'fromip'    => $fromip,
                                   'toip'      => $toip,
                                   'method'    => $this->db->f('method'),
                                   'fromport'  => $fromport,
                                   'toport'    => $toport,
                                   'transport' => $transport,
                                   'date'      => $this->db->f('time_stamp'),
                                   'status'    => $this->db->f('status'),
                                   'timestamp' => $this->db->f('timestamp'),
                                   'msg'       => $this->db->f('msg'),
                                   'md5'       => md5($this->db->f('msg'))
                                   );
            }
        }

    }

    function show($proxyIP,$callid,$fromtag,$totag) {

        $action           = $_REQUEST['action'];
        $toggleVisibility = $_REQUEST['toggleVisibility'];

        if ($action=='toggleVisibility') {
            $this->togglePublicVisibility($callid,$fromtag,$toggleVisibility);
        }

        if ($_SERVER['HTTPS'] == "on") {
            $protocolURL = "https://";
        } else {
            $protocolURL = "http://";
        }

        $this->getTrace($proxyIP,$callid,$fromtag,$totag);

        if (!count($this->trace_array)) {
            print "<p>
            SIP trace for session id $callid is not available.";
            return;
        }

        print "<div class=container-fluid><div id=trace class=main>
        <h1 class='page-header'>CDRTool SIP trace
        <small>SIP session $callid $authorizei</small></h1>

        <div class=row-fluid>
        <div class=span9>
        <form class=form-inline method=post name=visibility>
        ";
        if ($this->isAuthorized) {
            $key="callid-".trim($callid).trim($fromtag);

            $query=sprintf("select * from memcache where `key` = '%s'",addslashes($key));
            $this->cdrtool->query($query);

            $basicURL = $protocolURL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

            if ($this->cdrtool->num_rows()) {
                $selected_toggleVisibility['public']="selected";
                $fullURL=$basicURL."&public=1";
                $color2="lightblue";
            } else {
                $selected_toggleVisibility['private']="selected";
                $fullURL=$basicURL;
            }

            print "

            This SIP trace is visible

            <select class=span3 name=toggleVisibility onChange=\"document.visibility.submit.disabled = true; location.href = '$basicURL&action=toggleVisibility&toggleVisibility=' + this.options[this.selectedIndex].value\">
            <option $selected_toggleVisibility[public] value=1>without authorization
            <option $selected_toggleVisibility[private] value=0>only to authorized users
            </select>
            <input type=hidden name=action value=toggleVisibility>
            ";
            
            print "URLs for this trace: <a href=$fullURL>HTML</a> | <a href=$fullURL&format=text>TEXT</a></td>";
        } else {
            print "";
        }

        if ($this->mediaTrace) {
            $this->mediaTraceLink=sprintf("<p class=pull-right><a href=\"javascript:void(null);\" onClick=\"return window.open('media_trace.phtml?cdr_source=%s&callid=%s&fromtag=%s&totag=%s&proxyIP=%s', 'mediatrace',
            'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=730')\">Click here for RTP media information</a></p>",
            $this->mediaTrace,
            urlencode($callid),
            urlencode($fromtag),
            urlencode($totag),
            $proxyIP
            );
        }

        print "
            </form>
            </div>
            <div class='span3'>
            <p class='pull-right'>
        Click on each packet to expand its body content</p>
        $this->mediaTraceLink
        </div>
        </div>
        ";

        foreach (array_keys($this->trace_array) as $key) {

            if ($this->trace_array[$key]['direction'] == 'in') {

                if (is_array($this->SIPProxies)) {
                    $thisIP=explode(":",$this->trace_array[$key]['fromip']);
                    if ($this->isProxy($thisIP[0],$proxyIP)) {
                        $this->trace_array[$key]['isProxy'] = 1;
                    }
                }

                if ($this->trace_array[$key]['fromip'] == $this->trace_array[$key]['toip']) {
                    $this->trace_array[$key]['arrow_align'] = "left";
                    $arrow_direction="loop";
                } else if ($this->column[$this->trace_array[$key]['fromip']] < $this->column[$this->trace_array[$key]['toip']]) {
                    $arrow_direction="right";
                    $this->trace_array[$key]['arrow_align'] = "right";
                } else {
                    $arrow_direction="left";
                    $this->trace_array[$key]['arrow_align'] = "left";
                }
                $this->trace_array[$key]['msg_possition']   = $this->column[$this->trace_array[$key]['toip']];
                $this->trace_array[$key]['arrow_possition'] = $this->column[$this->trace_array[$key]['fromip']];
                $this->trace_array[$key]['arrow_direction'] = $arrow_direction;

            } else {
                if ($this->trace_array[$key]['fromip'] == $this->trace_array[$key]['toip']) {
                    $this->trace_array[$key]['arrow_align'] = "left";
                    $arrow_direction="loop";
                } else if ($this->column[$this->trace_array[$key]['fromip']] < $this->column[$this->trace_array[$key]['toip']]) {
                    $this->trace_array[$key]['arrow_align'] = "left";
                    $arrow_direction="right";
                } else {
                    $this->trace_array[$key]['arrow_align'] = "right";
                    $arrow_direction="left";
                }

                $this->trace_array[$key]['msg_possition']   = $this->column[$this->trace_array[$key]['fromip']];
                $this->trace_array[$key]['arrow_possition'] = $this->column[$this->trace_array[$key]['toip']];
                $this->trace_array[$key]['arrow_direction'] = $arrow_direction;
            }
        }

        //print_r($this->hostnames);

        print "
        <p>
        <a name=#top>
        <table class='table table-condensed table-hover' cellpadding=2 cellspacing=0 border=0 width=100%>
        <thead>
        <tr>
        <th></th>
        <th align=center><b>Packet</b></th>
        <th align=center><b>Size</b></th>
        <th align=center><b>Time</b></th>
        ";

        foreach (array_keys($this->column) as $_key) {
            $IPels=explode(":",$_key);

            if ($this->hostnames[$IPels[0]]) {
                $_hostname=$this->hostnames[$IPels[0]];
            } else {
                $_hostname=$_key;
            }

            print "<th align=center class=border>";
            if ($proxyIP != $IPels[0] && $this->isProxy($IPels[0],$proxyIP)) {
                $trace_link=sprintf("<a href=\"javascript:void(null);\" onClick=\"return window.open('sip_trace.phtml?cdr_source=%s&callid=%s&fromtag=%s&totag=%s&proxyIP=%s', 'Trace',
                'toolbar=0,status=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600')\"><font color=red><b>%s:%s (Trace) </b></font></a>",
                urlencode($this->cdr_source),
                urlencode($callid),
                urlencode($fromtag),
                urlencode($totag),
                $IPels[0],
                $_hostname,
                $this->column_port[$_key]
                );
                printf ("<b><font color=blue>%s</b>",$trace_link);
            } else {
                printf ("<b>%s</b>",$_hostname);
            }
            print "</th>";
        }

        print "</tr>
            </thead>";

        $i=0;

        foreach (array_keys($this->trace_array) as $key) {

            $i++;

            $id        = $this->trace_array[$key]['id'];
            $msg       = $this->trace_array[$key]['msg'];
            $fromip    = $this->trace_array[$key]['fromip'];
            $toip      = $this->trace_array[$key]['toip'];
            $date      = substr($this->trace_array[$key]['date'],11);
            $status    = $this->trace_array[$key]['status'];
            $direction = $this->trace_array[$key]['direction'];
            $timestamp = $this->trace_array[$key]['timestamp'];
            $method    = $this->trace_array[$key]['method'];
            $isProxy   = $this->trace_array[$key]['isProxy'];
            $transport = $this->trace_array[$key]['transport'];

            $msg_possition   = $this->trace_array[$key]['msg_possition'];
            $arrow_possition = $this->trace_array[$key]['arrow_possition'];
            $arrow_direction = $this->trace_array[$key]['arrow_direction'];
            $arrow_align     = $this->trace_array[$key]['arrow_align'];
            $md5             = $this->trace_array[$key]['md5'];

            if ($i==1) $begin_timestamp = $timestamp;
            $timeline=$timestamp-$begin_timestamp;

            $sip_phone_img=getImageForUserAgent($msg);

            if ($seen_msg[$md5]) continue;

            $SIPclass=substr($status,0,1);
    
            if ($SIPclass=="6") {
                $status_color="<font color=red>";
            } else if ($SIPclass=="5" ) {
                $status_color="<font color=red>";
            } else if ($SIPclass=="4" ) {
                $status_color="<font color=blue>";
            } else if ($SIPclass=="3" ) {
                $status_color="<font color=green>";
            } else if ($SIPclass=="2" ) {
                $status_color="<font color=green>";
            } else if ($SIPclass=="1" ) {
                $status_color="<font color=\"#cc6600\">";
            } else  {
                $status_color="<font color=black>";
            }

            $_lines=explode("\n",$msg);

            if (preg_match("/^(.*) SIP/",$_lines[0],$m)) {
                $_lines[0]=$m[1];
            } else if (preg_match("/^SIP\/2\.0 (.*)/",$_lines[0],$m)) {
                $_lines[0]=$m[1];
            }

            unset($media);
            unset($diversions);

            $j=0;
            $media_index=0;
            $search_ice=0;
            $search_ip=0;
            $contact_header='';

            foreach ($_lines as $_line) {
                if (preg_match("/^(Diversion: ).*;(.*)$/",$_line,$m)) {
                    $diversions[]=$m[1].$m[2];
                }

                if (preg_match("/^Cseq:\s*\d+\s*(.*)$/i",$_line,$m)) {
                    $status_for_method=$m[1];
                }

                if (preg_match("/^Contact:\s*.*@(.*:\d+).*$/i",$_line,$m)) {
                    $contact_header=$m[1];
                }

                if (preg_match("/^c=IN \w+ ([\d|\w\.]+)/i",$_line,$m)) {
                    $media['ip']=$m[1];
                }

                if (preg_match("/^m=(\w+) (\d+) /i",$_line,$m)) {
                    $media_index++;
                    $search_ice=1;
                    $search_ip=1;
                    $media['streams'][$media_index]=array('type' => $m[1],
                                                          'ip'   => $media['ip'],
                                                          'port' => $m[2],
                                                          'ice'  => ''
                                                          );
                }

                if ($search_ip && preg_match("/^c=IN \w+ ([\d|\w\.]+)/i",$_line,$m)) {
                    $media['streams'][$media_index]['ip']=$m[1];
                    $search_ip=0;
                }

                if ($search_ice && preg_match("/^a=ice/i",$_line,$m)) {
                    $media['streams'][$media_index]['ice']="ICE";
                    $search_ice=0;
                }

                $j++;
            }


            $_els=explode(";",$_lines[0]);

            $cell_content = "<a name=\"packet$i\">
            $status_color $_els[0]</font>
            ";

            if ($status) $cell_content.=" <font color=black>for ".$status_for_method."</font>";

            if ($contact_header) {
                $cell_content.=sprintf("<br>Contact: %s",htmlspecialchars($contact_header));
            }

            if (is_array($diversions)) {
                foreach ($diversions as $_diversion) {
                    $cell_content.="<br><b>$_diversion</b>";
                }
            }

            if (is_array($media['streams'])) {
                foreach (array_keys($media['streams']) as $_key) {
                    $_stream=sprintf("%s->%s:%s %s",$media['streams'][$_key]['type'],
                                                 $media['streams'][$_key]['ip'],
                                                 $media['streams'][$_key]['port'],
                                                 $media['streams'][$_key]['ice']
                                                 );
                    if ($media['streams'][$_key]['port']) {
                        $cell_content.="<br><b>$_stream</b>";
                    } else {
                        $cell_content.="<br><b><font color=red>$_stream</font></b>";
                    }

                }
            }

            $cell_content.="
            </font>
            </a>
            ";

            print "
            <tr class=border onClick=\"return toggleVisibility('row$i')\">
            <td>
            ";

            if ($timeline && !$_seen_timeline[$timeline]) {
                printf ("%s+%ds </font>",$status_color,$timeline);
                $_seen_timeline[$timeline]++;
            }

            $len=strlen($msg);

            print "
            </td>
            <td class=bbot>$status_color$i/$this->rows&nbsp;</font></td>
            <td class=bbot>$len bytes</td>
            <td class=bbot><nobr>$status_color$date</nobr></font>
            ";

            $column_current=1;
            
            while ($column_current <= count($this->column)) {

                if ($arrow_possition==$column_current) {
                    if ($direction=='out') {
                        if ($arrow_direction == 'left') {
                            $arrow="green_arrow_left.png";
                        } else if ($arrow_direction == 'right') {
                            $arrow="green_arrow_right.png";
                        } else if ($arrow_direction == 'loop'){
                            $arrow="LoopArrow.png";
                        }
                    } else {
                        if ($arrow_direction == 'left') {
                            $arrow="blue_arrow_left.png";
                        } else if ($arrow_direction == 'right') {
                            $arrow="blue_arrow_right.png";
                        } else if ($arrow_direction == 'loop'){
                            $arrow="LoopArrow.png";
                        }
                    }

                }

                if ($arrow_possition==$column_current) {
                    print "<td class=bbot align=$arrow_align>";

                    if ($arrow_direction == 'left') {
                        print "<img src=images/$arrow border=0>";
                        if (!$isProxy && $direction=='in' && $sip_phone_img && $sip_phone_img!='unknown.png')
                        print "<img src=images/30/$sip_phone_img border=0>";
                    } else {
                        if (!$isProxy && $direction=='in' && $sip_phone_img && $sip_phone_img!='unknown.png')
                        print "<img src=images/30/$sip_phone_img border=0>";
                        print "<img src=images/$arrow border=0>";
                    }

                    print "<br>";
                    if ($transport == 'tls') print "<span style='font-size:16px'><i class='icon-lock'></i></span> ";
                    if ($direction == 'in') {
                        printf ("%s port %d",strtoupper($transport),$this->trace_array[$key]['fromport']);
                    } else {
                        printf ("%s port %d",strtoupper($transport),$this->trace_array[$key]['toport']);
                    }

                } else {
                    print "<td class=bbot>";
                }

                if ($msg_possition == $column_current) {
                    print $cell_content;
                    print "<br>";
                    if ($direction == 'in') {
                        printf ("%s port %d",strtoupper($transport),$this->trace_array[$key]['toport']);
                    } else {
                        printf ("%s port %d",strtoupper($transport),$this->trace_array[$key]['fromport']);
                    }
                } else {
                    print "&nbsp;";
                }

                print "
                </td>
                ";

                $column_current++;
                if ($arrow_direction=='loop') $seen_msg[$md5]++;
            }

            print "</tr>";

            if (is_array($this->SIPProxies)) {
                $IPels=explode(":",$fromip);
                $justIP=$IPels[0];
                foreach (array_keys($this->SIPProxies) as $localProxy) {
                    if ($localProxy==$justIP) {
                        $direction="out";
                        break;
                    }
                }
            }

            $trace_span=count($this->column)+2;

            print "
            <tr class=extrainfo id=row$i >
            <td></td>
            <td colspan=$trace_span>
            ";

            print "<div class='alert alert-info'>
                <div class=row-fluid><div class='span2'><div class='row-fluid'><div class='span12'>";

            if ($direction == "out") {
                print "
                <nobr>
                <h1>SIP Proxy</h1></nobr>
                ";
            } else {
                if ($sip_phone_img) print "<img style='max-width:none' src=images/$sip_phone_img>";
            }
            print "</div></div><div class=row-fluid><div class='span12'>";
            if ($timeline > 0) {
                printf ("<p>+%s s<br>(%s)",$timeline,sec2hms($timeline));
            }
            print "</div></div></div><div class=span10>";
            $msg=nl2br(htmlentities($msg));

            print "
                $status_color $msg 
            
            </div></div></div>";

            print "
                </td>
                <td></td>
            </tr>
            ";
        }

        print "
        </table>
        ";
    }

    function showText($proxyIP,$callid,$fromtag,$totag) {

        $this->getTrace($proxyIP,$callid,$fromtag,$totag);
        print "<pre>";

        if (!count($this->trace_array)) {
            print "SIP trace for session id $callid is not available.";
            return false;
        }

        printf ("SIP trace on proxy %s for session %s\n--\n\n",$proxyIP,$callid);

        foreach (array_keys($this->trace_array) as $key) {
            $i++;
            printf ("Packet %d at %s from %s to %s (%s)\n",
            $i,
            $this->trace_array[$key]['date'],
            $this->trace_array[$key]['fromip'],
            $this->trace_array[$key]['toip'],
            $this->trace_array[$key]['direction']);
            printf ("\n%s\n",htmlspecialchars($this->trace_array[$key]['msg']));
            print "---\n";
        }
        print "</pre>";
    }

    function togglePublicVisibility($callid,$fromtag,$public='0') {

        $key="callid-".trim($callid).trim($fromtag);

        if (!$public) {
            $query=sprintf("delete from memcache where `key` = '%s'",addslashes($key));
            $this->cdrtool->query($query);
        } else {
            $query=sprintf("delete from memcache where `key` = '%s'",addslashes($key));
            $this->cdrtool->query($query);
            $query=sprintf("insert into memcache values ('%s','public')",addslashes($key));
            $this->cdrtool->query($query);
        }
    }

    function purgeRecords($days='') {

        if ($this->enableThor) {
            return true;
        }

        $b=time();

        if ($days) {
            $this->purgeRecordsAfter=$days;
        } else if (!$this->purgeRecordsAfter) {
            $this->purgeRecordsAfter=15;
        }

        $beforeDate=Date("Y-m-d", time()-$this->purgeRecordsAfter*3600*24);

        $query=sprintf("select id as min, time_stamp from %s order by id ASC limit 1",
                       addslashes($this->table));
        
        if ($this->db->query($query)) {
            if ($this->db->num_rows()) {
                $this->db->next_record();
                $min=$this->db->f('min');
                $begindate=$this->db->f('date');
            } else {
                $log=sprintf("No records found in %s\n",$this->table);
                print $log;
                syslog(LOG_NOTICE,$log);
                return false;
            }

        } else {
            $log=sprintf("Error: %s (%s)\n",$this->db->Error,$query);
            print $log;
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $query=sprintf("select id as max from %s where time_stamp < '%s' order by id DESC limit 1",
                addslashes($this->table),addslashes($beforeDate));


        if ($this->db->query($query) && $this->db->num_rows()) {
            $this->db->next_record();
            $max=$this->db->f('max');
        } else {
            $log=sprintf("No records found in %s before %s, records start after %s\n",
            $this->table,$beforeDate,$begindate);
            syslog(LOG_NOTICE,$log);
            print $log;
            return false;
        }

        $deleted=0;
        $i=$min;

        $interval=1000;

        $rows2delete=$max-$min;
        $found = 0;

        print "$rows2delete traces to delete between $min and $max\n";

        while ($i<=$max) {
            $found=$found+$interval;

            if ($i + $interval < $max) {
                $top=$i;
            } else {
                $top=$max;
            }
            $query=sprintf("delete low_priority from %s
                            where id >= '%d' and id <='%d'",
                            addslashes($this->table),addslashes($min),addslashes($top));
            if ($this->db->query($query)) {
                $deleted=$deleted+$this->db->affected_rows();
            } else {
                $log=sprintf("Error: %s (%s)",$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE,$log);
                return false;
            }

            if ($found > $progress*$rows2delete/100) {
                $progress++;
                if ($progress%10==0) {
                    print "$progress% ";
                }
                flush();
            }

            $i=$i+$interval;
        }

        print "\n";

        $e    =time();
        $d    =$e-$b;
        $rps=0;

        if ($deleted && $d) $rps=$deleted/$d;

        $log=sprintf("%s records before %s from %s deleted in %d s @ %.0f rps\n",$deleted,$beforeDate,$this->table,$d,$rps);
        syslog(LOG_NOTICE,$log);
        print $log;

        return true;
    }
}

class Media_trace {
    var $enableThor  = false;
    var $table       = 'media_sessions';

    function Media_trace ($cdr_source) {
        global $DATASOURCES;

        $this->cdr_source = $cdr_source;
        $this->cdrtool  = new DB_CDRTool();

        if (!is_array($DATASOURCES[$this->cdr_source])) {
            $log=sprintf("Error: datasource '%s' is not defined",$this->cdr_source);
            print $log;
            return 0;
        }

        if (strlen($DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->enableThor = $DATASOURCES[$this->cdr_source]['enableThor'];
        }

        if ($this->enableThor) {
            require("/etc/cdrtool/ngnpro_engines.inc");
            require_once("ngnpro_soap_library.php");

            if ($DATASOURCES[$this->cdr_source]['soapEngineId'] && in_array($DATASOURCES[$this->cdr_source]['soapEngineId'],array_keys($soapEngines))) {
                $this->soapEngineId=$DATASOURCES[$this->cdr_source]['soapEngineId'];

                $this->SOAPlogin = array(
                                       "username"    => $soapEngines[$this->soapEngineId]['username'],
                                       "password"    => $soapEngines[$this->soapEngineId]['password'],
                                       "admin"       => true
                                       );
    
                $this->SOAPurl=$soapEngines[$this->soapEngineId]['url'];
        
                $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');
    
                // Instantiate the SOAP client
                $this->soapclient = new WebService_NGNPro_SipPort($this->SOAPurl);
    
                $this->soapclient->setOpt('curl', CURLOPT_TIMEOUT,        5);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
    
            } else {
                print "Error: soapEngineID not defined in datasource $this->cdr_source";
                return false;
            }

        } else {
            if ($DATASOURCES[$this->cdr_source]['table']) {
                $this->table = $DATASOURCES[$this->cdr_source]['table'];
            }

            $db_class = $DATASOURCES[$this->cdr_source]['db_class'];
            if (class_exists($db_class)) {
                $this->db                = new $db_class;
            } else {
                printf("<p><font color=red>Error: database class %s is not defined in datasource %s</font>",$db_class,$this->cdr_source);
                return false;
            }
        }
    }

    function getTrace ($proxyIP,$callid,$fromtag,$totag) {

        if ($this->enableThor) {
            // get trace using soap request
            if (!$proxyIP || !$callid || !$fromtag) {
                print "<p><font color=red>Error: proxyIP or callid or fromtag are not defined</font>";
                return false;
            }
    
            if (!is_object($this->soapclient)) {
                print "<p><font color=red>Error: soap client is not defined</font>";
                return false;
            }
    
            $filter=array('nodeIp'  => $proxyIP,
                          'callId'  => $callid,
                          'fromTag' => $fromtag,
                          'toTag'   => $totag
                          );
    
            $this->soapclient->addHeader($this->SoapAuth);
    
            $result     = $this->soapclient->getMediaTrace($filter);
    
            if (PEAR::isError($result)) {
                $error_msg   = $result->getMessage();
                $error_fault = $result->getFault();
                $error_code  = $result->getCode();
    
                if ($error_fault->detail->exception->errorcode != 1060) {
                    printf("<font color=red>Error from %s: %s (%s)</font>",
                    $this->SOAPurl,
                    $error_fault->detail->exception->errorstring,
                    $error_fault->detail->exception->errorcode
                    );
                }
                return false;
            }
    
            $this->info = json_decode($result);

        } else {
            if (!is_object($this->db)) {
                print "<p><font color=red>Error: no database connection defined</font>";
                return false;
            }

            // get trace from SQL
            $query=sprintf("select info from %s where call_id = '%s' and from_tag = '%s' and to_tag= '%s'",
            addslashes($this->table),
            addslashes($callid),
            addslashes($fromtag),
            addslashes($totag)
            );

            if (!$this->db->query($query)) {
                printf ("<p><font color=red>Database error for query %s: %s (%s)</font>",$query,$this->db->Error,$this->db->Errno);
                return false;
            }
    
            if ($this->db->num_rows()) {
                $this->db->next_record();
                $this->info = json_decode($this->db->f('info'));
            }
        }
    }

    function show($proxyIP,$callid,$fromtag,$totag) {

        if ($_SERVER['HTTPS'] == "on") {
            $protocolURL = "https://";
        } else {
            $protocolURL = "http://";
        }

        $this->getTrace($proxyIP,$callid,$fromtag,$totag);
        print "<div class=container-fluid'><div id=trace class='main'>";

        if (!is_object($this->info)) {
            print "<p>No information available.";
            return false;
        }

        if (!count($this->info->streams)) {
            print "<p>
            No RTP media streams have been established";
            return;
        }

        print "
        <h1 class=page-header>CDRTool Media Trace
        <small>Media Session $callid</small></h1>
        ";

        foreach (array_values($this->info->streams) as $_val) {
            $_diff=$_val->end_time-$_val->timeout_wait;
            $seen_stamp[$_val->start_time]++;
            $seen_stamp[$_val->end_time]++;
            $seen_stamp[$_diff]++;
            $media_types[]=$_val->media_type;
        }

        print "<h2>Media Information</h2>";

        print "<table border=0>";
        printf ("<tr><td class=border>Call duration</td><td class=border>%s</td></tr>",$this->info->duration);
        list($relay_ip,$relay_port)=explode(":",$this->info->streams[0]->caller_local);
        printf ("<tr><td class=border>Media relay</td><td class=border>%s</td></tr>",$relay_ip);
        print "</table>";

        print "<h2>Media Streams</h2>";

        print "<table class='table table-condensed table-striped' style='width:300px' border=0>";
        print "<thead><tr><th></th>";

        foreach (array_values($media_types) as $_type) {
            printf ("<th>%s</th>",ucfirst($_type));
        }

        print "</tr></thead>";

        foreach ($this->info->streams[0]  as $_val => $_value) {
            printf ("<tr><td class=border>%s</td>",ucfirst(preg_replace("/_/"," ",$_val)));
            $j=0;
            while ($j < count($media_types)) {
                printf ("<td class=border>%s</td>",$this->info->streams[$j]->$_val);
                $j++;
            }

            printf ("</tr>\n");
        }

        print "</table>";

        print "<br><h2>Stream Succession</h2>";

        $w_legend_bar=500;
        $w_text=30;
        $stamps=array_keys($seen_stamp);
        sort($stamps);

        $w_table=$w_legend_bar+$w_text;

        print "<table border=0 cellpadding=1 cellspacing=1 width=$w_table>";

        $j=0;

        $_index=0;
        foreach (array_values($this->info->streams) as $_val) {

            if ($_val->status == 'unselected ice candidate') continue;

            $_index=$_index+$_val->start_time;

            $_duration   = $_val->end_time-$_val->start_time;
            $_timeout    = $_val->timeout_wait;

            $duration_print= $_duration;

            if ($_val->status == 'conntrack timeout') {
                $w_duration   = intval(($_duration-$_timeout)*$w_legend_bar/$this->info->duration);
                $w_timeout    = intval($_timeout*$w_legend_bar/$this->info->duration);
                $duration_print = $_duration - $_timeout;

            } else if ($_val->status == 'no-traffic timeout') {
                $w_duration   = intval($_duration*$w_legend_bar/$this->info->duration);
                $w_timeout    = intval($_timeout*$w_legend_bar/$this->info->duration);

            } else if ($_val->status == 'closed' ) {
                $w_duration   = intval($_duration * $w_legend_bar / $this->info->duration);
                $w_timeout    = 0;
            }


            $w_start_time = intval($_index*$w_legend_bar/$this->info->duration);
            $w_rest       = $w_legend_bar-$w_duration-$w_timeout-$w_start_time;
            $w_duration_p = ($w_legend_bar/$w_duration) * 100;
            $w_timeout_p  = ($w_legend_bar/$w_timeout)* 100;
            $w_start_p  = ($w_legend_bar/$w_start)* 100;

            printf ("%s, %s, %s, %s<br>\n",$w_start_p,$w_duration_p,$w_timeout_p,$w_rest);
            
            if ($_val->caller_packets != '0' && $_val->callee_packets != '0'){

                print "<tr><td width=$w_text class=border>$_val->media_type</td>";

                print "<td width=$w_legend_bar>\n";
                //print "<table width=100% border=0 cellpadding=0 cellspacing=0><tr>\n";
                print "<div class='progress progress-striped'>";
                print "<div class=bar style='width:$w_start_p%'></div>\n";
                print "<div class='bar bar-success'  style='width:$w_duration_p% ; text-align:center'><font color=white>$duration_print</font></div>\n";

                if ($_val->timeout_wait) {
                    print "<div class='bar bar-danger' style='width:$w_timeout%; text-align:center'><font color=white>$_timeout</font></div>\n";
                } else {
                    print "<div class='bar bar-warning' style='width:$w_timeout%; text-align:center'></div>\n";
                }
                //print "<td width=$w_rest bgcolor=white align=center></td>\n";

                //print "</table>\n";

                print "</td></tr>";
            } elseif ( $_val->status == 'unselected ICE candidate')  {
                print "<tr><td>ICE session</td></tr>";
            } else {
                print "<tr><td>No stream data found</td></tr>";
            }
        }

        print "</table>";

        print "<br><strong>Legend</strong>";
        print "<p><table border=0>
        <tr><td width=50><div class='progress progress-striped progress-success'><div class='bar' style='width:100%'></div></div></td><td>Session data</td></tr>
        <tr><td><div class='progress progress-striped progress-danger'><div class='bar' style='width:100%'></div></div></td><td>Timeout period</td></tr>
        </table></p></div></div>
        ";
    }
}

include_once("phone_images.php");
function getImageForUserAgent($msg) {
    global $userAgentImages;

    $msg_lines=explode("\n",$msg);
    foreach($msg_lines as $line) {
        $els=explode(":",$line);
        if (strtolower($els[0]) == 'user-agent' || strtolower($els[0]) == 'server') {
            foreach ($userAgentImages as $agentRegexp => $image) {
                if (preg_match("/^(user-agent|server):.*$agentRegexp/i", $line)) {
                    return $image;
                }
            }
        }


    }
    return "unknown.png";
}

function isThorNode($ip,$sip_proxy) {

    if (!$ip || !$sip_proxy) return false;

    $socket = fsockopen($sip_proxy, 9500, $errno, $errstr, 1);

    if (!$socket) {
        return false;
    }
    
    $request=sprintf("is_online %s as sip_proxy",$ip);
    
    if (fputs($socket,"$request\r\n") !== false) {
        $ret = trim(fgets($socket,4096));
        fclose($socket);
    } else {
        fclose($socket);
        return false;
    }

    if ($ret == 'Yes') {
        return true;
    } else {
        return false;
    }
}

?>
