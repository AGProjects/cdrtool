<?

class CDRS_cisco extends CDRS {
    var $table                    = "radacct";
    var $CDR_class                = "CDR_cisco";

    var $CDRFields=array('id'   	           => 'RadAcctId',
                         'callId'              => 'H323ConfID',
                         'duration'            => 'AcctSessionTime',
                         'startTime'           => 'AcctStartTime',
                         'stopTime'            => 'AcctStopTime',
                         'inputTraffic'        => 'AcctInputOctets',
                         'outputTraffic'       => 'AcctOutputOctets',
                         'aNumber'  	       => 'CallingStationId',
                         'username'            => 'UserName',
                         'domain'	           => 'Realm',
                         'cNumber'   	       => 'CalledStationId',
                         'timestamp'	       => 'timestamp',
                         'serviceType'         => 'ServiceType',
                         'disconnect'          => 'H323DisconnectCause',
                         'applicationType'     => 'SipApplicationType',
                         'BillingPartyId'      => 'UserName',
                         'localGateway'        => 'NASIPAddress',
                         'gateway'             => 'H323RemoteAddress',
                         'BillingPartyId'      => 'UserName',
                         'NASPortId'           => 'NASPortId',
                         'NASPortType'         => 'NASPortType',
                         'H323GWID'            => 'H323GWID',
                         'H323CallOrigin'      => 'H323CallOrigin',
                         'H323CallType'        => 'H323CallType',
                         'H323SetupTime'       => 'H323SetupTime',
                         'H323ConnectTime'     => 'H323ConnectTime',
                         'H323DisconnectTime'  => 'H323DisconnectTime',
                         'H323DisconnectCause' => 'H323DisconnectCause',
                         'RemoteAddress'       => 'H323RemoteAddress',
                         'H323VoiceQuality'    => 'H323VoiceQuality',
                         'H323ConfID'          => 'H323ConfID',
                         'normalized'          => 'Normalized',
                         'rate'                => 'Rate',
                         'price'               => 'Price',
                         'BillingPartyId'      => 'UserName',
                         'DestinationId'       => 'DestinationId'
                         );

    var $CDRNormalizationFields=array('id'   	        => 'RadAcctId',
                                      'callId'          => 'AcctSessionId',
                                      'username'        => 'UserName',
                                      'domain'	        => 'Realm',
                                      'gateway'         => 'H323RemoteAddress',
                                      'NASIPAddress'    => 'NASIPAddress',
                                      'NASPortId'       => 'NASPortId',
                                      'duration'        => 'AcctSessionTime',
                                      'startTime'       => 'AcctStartTime',
                                      'stopTime'        => 'AcctStopTime',
                                      'inputTraffic'    => 'AcctInputOctets',
                                      'outputTraffic'   => 'AcctOutputOctets',
                                      'aNumber'  	    => 'CallingStationId',
                                      'cNumber'   	    => 'CalledStationId',
                                      'timestamp'	    => 'timestamp',
                                      'BillingPartyId'  => 'UserName',
                                      'price'           => 'Price',
                                      'DestinationId'   => 'DestinationId'
                                      );

    var $GROUPBY=array("CallingStationId"    => "Caller Party",
                       "DestinationId"       => "Destination Id",
                       "NASPortId"           => "Port Id",
                       "H323RemoteAddress"   => "Gateway",
                       "H323RemoteAddress"   => "Remote address",
                       "H323DisconnectCause" => "Disconnection cause"
        );

     var $FormElements=array(
        "begin_hour","begin_min","begin_month","begin_day","begin_year","begin_datetime",
        "end_hour","end_min","end_month","end_day","end_year","end_datetime",
        "call_id","a_number","a_number_comp","c_number","c_number_comp","DestinationId","ExcludeDestinations",
        "unnormalize",
        "UserName","UserName_comp","BillingId",
        "NASIPAddress","NASPortId","RemoteAddress","H323CallType","H323CallOrigin","release_cause",
        "duration","action","MONTHYEAR","showRate",
        "order_by","order_type","group_by",
        "cdr_source","trace","cdr_table","maxrowsperpage");

    function LoadDisconnectCodes() {

        $query="select * from isdncause order by cause";
        dprint("$query");
        $this->cdrtool->query($query);
        $this->disconnectCodesElements[]=array("label"=>"Any disconnection cause","value"=>"");

        while($this->cdrtool->next_record()) {
            $key         = $this->cdrtool->f('cause');
            $value       = $this->cdrtool->f('description');
            $value_print = $value." (".$key.")";
    
            $this->disconnectCodesElements[]=array("label"=>$value_print,"value"=>$key);
            $this->disconnectCodesDescription[$key]=$value;
            $found++;
        }
        dprint("Loaded $found release codes");
        return 1;

    }

    function showTableHeaderStatistics() {
        $group_byPrint=$this->GROUPBY[$this->group_by];
        if (!$this->export) {
            print "
            <table border=1 cellspacing=2 width=100% align=center>
            <tr>
            <td>
            <table border=0 cellspacing=2 width=100%>
            <tr bgcolor=lightgrey>
                <td></td>
                <td>			<b>Calls</b></td>
                <td align=right><b>Seconds</b></td>
                <td align=right><b>Minutes</b></td>
                <td align=right><b>Hours</b></td>";
                if ($this->rating) {
                    print "<td align=right><b>Price</b></td>
                    ";
                }
                print "
                <td align=right><b>MB In</b></td>
                <td align=right><b>MB Out</b></td>
                <td align=center colspan=2><b>Success</b></td>
                <td align=center colspan=2><b>Failure</b></td>
                <td>			<b>$group_byPrint</b></td>
                <td>			<b>Description</b></td>
                <td>            <b>Type</b>
                <td>			<b>Action</b></td>
            </tr>
            ";
            print "</tr>";
        } else {
            print  "id,Calls,Seconds,Minutes,Hours,Price,TrafficIn(MB),TrafficOut(MB),Success(%),Success(calls),Failure(%),Failure(calls),$group_byPrint,Description\n";
        }

    }

    function showTableHeaderSubscriber() {
        $this->showTableHeader();
    }

    function showTableHeader() {

        print  "
        <table border=1 cellspacing=2 width=100% align=center>
        <tr>
        <td>
        <table border=0 cellspacing=2 width=100%>
        <tr bgcolor=lightgrey>
                <td>
                <td> <b>Date and time
                <td> <b>Caller Party</b>
                <td> <b>In</b>
                <td colspan=2> <b>Destination</b>
                <td> <b>Out</b>
                <td> <b>Dur</b>";
                if ($this->rating) {
                    if ($this->showRate) {
                        print "<td><b>Rate</b></td>";
                    }
                    print "
                    <td> <b>Price</b>
                    ";
                }
                print "
                <td> <b>Gateway</b>
                <td> <b>KBIn</b>
                <td> <b>KBOut</b>
                <td> <b>Disconnect</b>
                <td> <b>Channel</b>
                <td> <b>Direction</b>
                <td> <b>CallType</b>
        </tr>
        ";
    }

    function showExportHeader() {
        print "id,StartTime,Username,CallerParty,Destination,DestinationId,DestinationName,Duration,Price,Gateway,KBIn,KBOut,Disconnect,Channel,Direction,CallType\n";
    }

    function searchFormSubscriber() {
        $this->searchForm();
    }

    function initForm() {

        // form els added below must have global vars
        foreach ($this->FormElements as $_el) {
            global ${$_el};
            ${$_el} = $_REQUEST[$_el];
        }

        $action         = "search";

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
                                    "extrahtml"=>"onChange=\"document.datasource.submit.disabled = true; location.href = 'callsearch.phtml?cdr_source=' + this.options[this.selectedIndex].value\"",
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
                                "value"=>$cdr_table
                                ));

        if ($begin_datetime) {
            dprint("begin_datetime=$begin_datetime");
            preg_match("/^(\d\d\d\d)-(\d+)-(\d+)\s+(\d\d):(\d\d)/", "$begin_datetime", $parts);
            $begin_year	=date(Y,$begin_datetime);
            $begin_month=date(m,$begin_datetime);
            $begin_day 	=date(d,$begin_datetime);
            $begin_hour	=date(H,$begin_datetime);
            $begin_min 	=date(i,$begin_datetime);
        } else {
            $begin_day      = $_REQUEST["begin_day"];
            $begin_month    = $_REQUEST["begin_month"];
            $begin_year     = $_REQUEST["begin_year"];
            $begin_hour     = $_REQUEST["begin_hour"];
            $begin_min      = $_REQUEST["begin_min"];
        }
        
        if ($end_datetime) {
            dprint("end_datetime=$end_datetime");
            preg_match("/^(\d\d\d\d)-(\d+)-(\d+)\s+(\d\d):(\d\d)/", "$end_datetime", $parts);
            $end_year	=date(Y,$end_datetime);
            $end_month 	=date(m,$end_datetime);
            $end_day 	=date(d,$end_datetime);
            $end_hour	=date(H,$end_datetime);
            $end_min 	=date(i,$end_datetime);
        }  else {
            $end_day        = $_REQUEST["end_day"];
            $end_month      = $_REQUEST["end_month"];
            $end_year       = $_REQUEST["end_year"];
            $end_hour       = $_REQUEST["end_hour"];
            $end_min        = $_REQUEST["end_min"];
        }
        
        $default_year	=Date("Y");
        $default_month	=Date("m");
        $default_day	=Date("d");
        $default_hour	=Date(H,time());
        
        if ($default_hour > 1) {
            $default_hour=$default_hour-1;
        }
        
        $default_hour=preg_replace("/^(\d)$/","0$1",$default_hour);
        $default_min	=Date("i");
        
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
                    "size"=>"1"
                    ));
        $this->f->add_element(array(	"name"=>"end_hour",
                    "type"=>"select",
                    "options"=>$hours_els,
                    "size"=>"1",
                    "value"=>"23"
                    ));
        $m=0;
        while ($m<60) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $min_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }

        $this->f->add_element(array(	"name"=>"begin_min",
                    "type"=>"select",
                    "options"=>$min_els,
                    "size"=>"1"
                    ));
        $this->f->add_element(array(
                    "name"=>"end_min",
                    "type"=>"select",
                    "options"=>$min_els,
                    "size"=>"1"
                    ));

        $m=1;
        while ($m<32) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $days_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }

        $this->f->add_element(array(	"name"=>"begin_day",
                                "type"=>"select",
                                "options"=>$days_els,
                                "size"=>"1"
        
                    ));
        $this->f->add_element(array(	"name"=>"end_day",
                                "type"=>"select",
                                "options"=>$days_els,
                                "size"=>"1"
        
                    ));

        $m=1;
        while ($m<13) {
            if ($m<10) { $v="0".$m; } else { $v=$m; }
            $month_els[]=array("label"=>$v,"value"=>$v);
            $m++;
        }
        
        
        $this->f->add_element(array(	"name"=>"begin_month",
                                "type"=>"select",
                                "options"=>$month_els,
                                "size"=>"1"
                    ));
        
        $this->f->add_element(array(	"name"=>"end_month",
                                "type"=>"select",
                                "options"=>$month_els,
                                "size"=>"1"
                    ));
        
        $thisYear=date("Y",time());
        $y=$thisYear;
        while ($y>$thisYear-6) {
            $year_els[]=array("label"=>$y,"value"=>$y);
            $y--;
        }
        $this->f->add_element(array(	"name"=>"begin_year",
                                "type"=>"select",
                                "options"=>$year_els,
                                "size"=>"1"
        
                    ));
        $this->f->add_element(array(	"name"=>"end_year",
                                "type"=>"select",
                                "options"=>$year_els,
                                "size"=>"1"
                    ));

        $this->f->add_element(array(	"name"=>"call_id",
                                "type"=>"text",
                                "size"=>"50",
                                "maxlength"=>"100"
                    ));
        
        $this->f->add_element(array(	"name"=>"a_number",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"25"
                    ));
        $this->f->add_element(array(	"name"=>"UserName",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"25"
                    ));
        $this->f->add_element(array(	"name"=>"c_number",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"25"
                    ));
        
        if (!$this->CDRTool['filter']['aNumber']) {
            $durations_els = array(
                array("label"=>"All calls","value"=>""),
                array("label"=>"0 seconds call","value"=>"zero"),
                array("label"=>"non 0 seconds","value"=>"nonzero"),
                array("label"=>"non 0 seconds with 0 price","value"=>"zeroprice"),
                array("label"=>"less than 5 seconds","value"=>"< 5"),
                array("label"=>"more than 5 seconds","value"=>"> 5"),
                array("label"=>"less than 60 seconds","value"=>"< 60"),
                array("label"=>"greater than 1 hour","value"=>"> 3600"),
                array("label"=>"greater than 5 hours","value"=>"> 18000")
                );
        } else {
            $durations_els = array(
                array("label"=>"Succesfull calls","value"=>"nonzero")
                );
        }
        
        $this->f->add_element(array(	"name"=>"duration",
                                "type"=>"select",
                                "options"=>$durations_els,
                                "value"=>"All",
                                "size"=>"1"
                    ));
        
        $comp_ops_els = array(
                array("label"=>"Begins with","value"=>"begin"),
                array("label"=>"Contains","value"=>"contain"),
                array("label"=>"Is empty","value"=>"empty"),
                array("label"=>"Equal","value"=>"equal")
                );

        $this->f->add_element(array(	"name"=>"a_number_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1"
                    ));
        $this->f->add_element(array(	"name"=>"c_number_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1"
                    ));
        $this->f->add_element(array(	"name"=>"UserName_comp",
                                "type"=>"select",
                                "options"=>$comp_ops_els,
                                "value"=>"begin",
                                "size"=>"1"
                    ));
        $this->f->add_element(array(	"name"=>"Realm",
                                "type"=>"text",
                                "size"=>"25",
                                "maxlength"=>"25"
                    ));
        
        $this->f->add_element(array("type"=>"submit",
                              "name"=>"submit",
                              "value"=>"Search"
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
        
        
        $this->f->add_element(array(	"name"=>"maxrowsperpage",
                                "type"=>"select",
                                "options"=>$max_els,
                                "size"=>"1",
                                "value"=>"25"
                    ));
        
        $order_type_els=array(
                        array("label"=>"Descending","value"=>"DESC"),
                        array("label"=>"Ascending","value"=>"ASC")
                        );
        
        $this->f->add_element(array(	"name"=>"order_type",
                                "type"=>"select",
                                "options"=>$order_type_els,
                                "size"=>"1"
                    ));
        
        $this->f->add_element(array("type"=>"hidden",
                              "name"=>"action",
                              "value"=>$action,
                ));

        $order_by_els=array(array("label"=>"Id","value"=>"RadAcctId"),
                            array("label"=>"Date","value"=>"AcctStopTime"),
                            array("label"=>"Disconnection cause","value"=>"H323DisconnectCause"),
                            array("label"=>"Caller Party","value"=>"CallingStationId"),
                            array("label"=>"Destination","value"=>"CalledStationId"),
                            array("label"=>"Call duration","value"=>"AcctSessionTime"),
                            array("label"=>"Price","value"=>"Price"),
                            array("label"=>"Failures(%)","value"=>"zeroP"),
                            array("label"=>"Success(%)","value"=>"nonzeroP")
        );

        $group_by_els[]=array("label"=>"","value"=>"");
        while (list($k,$v)=each($this->GROUPBY)) {
            $group_by_els[]=array("label"=>$v,"value"=>$k);
        }

        $this->f->add_element(array(	"name"=>"order_by",
                                "type"=>"select",
                                "options"=>$order_by_els,
                                "value"=>$order_by,
                                "size"=>"1"
                                ));
        $this->f->add_element(array(	"name"=>"group_by",
                                "type"=>"select",
                                "options"=>$group_by_els,
                                "value"=>$group_by,
                                "size"=>"1"
                                ));
        // Cisco stuff
        $this->f->add_element(array(	"name"=>"NASIPAddress",
                    "type"=>"text",
                    "size"=>"25",
                    "maxlength"=>"50"
                    ));
        $this->f->add_element(array(	"name"=>"RemoteAddress",
                    "type"=>"text",
                    "size"=>"15",
                    "maxlength"=>"50"
                    ));
        $this->f->add_element(array(	"name"=>"NASPortId",
                    "type"=>"text",
                    "size"=>"15",
                    "maxlength"=>"25"
                    ));
        $H323CallType_els=array(
                        array("label"=>"Any technology","value"=>""),
                        array("label"=>"Telephony","value"=>"Telephony"),
                        array("label"=>"Voice over IP","value"=>"VoIP")
                        );
        
        $this->f->add_element(array(	"name"=>"H323CallType",
                                "type"=>"select",
                                "options"=>$H323CallType_els,
                                "size"=>"1"
                    ));
        $H323CallOrigin_els=array(
                        array("label"=>"Any origin","value"=>""),
                        array("label"=>"Incoming","value"=>"answer"),
                        array("label"=>"Outgoing","value"=>"originate")
                        );
        
        $this->f->add_element(array(	"name"=>"H323CallOrigin",
                                "type"=>"select",
                                "options"=>$H323CallOrigin_els,
                                "size"=>"1"
                    ));
        
        $this->f->add_element(array(	"name"=>"release_cause",
                            "type"=>"select",
                            "options"=>$this->disconnectCodesElements,
                            "size"=>"1"
                    ));
        

        $this->f->load_defaults();
        // Start displaying form

    }

    function searchForm() {
        global $perm;

        $this->initForm();

        $this->f->start("","POST","","","datasource");

        print "
        <table cellpadding=5 CELLSPACING=0  border=6  width=100% align=center>
        ";

        $this->showDataSources ($this->f);
        $this->showDateTimeElements ($this->f);

        if ($this->CDRTool['filter']['aNumber']) {
            $ff[]="a_number";
        }
        if ($this->CDRTool['filter']['domain']) {
            $ff[]="comp_id";
        }
        if (count($ff)) {
            $this->f->freeze($ff);
        }

        print "
        <tr> 
            <td align=left>
            <b>Gateway</b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("RemoteAddress","");
            print " Local gateway:";
            $this->f->show_element("NASIPAddress","");
            print " Port Id ";
            $this->f->show_element("NASPortId","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>Call Id</b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("call_id","");
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
            Caller Party
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("a_number_comp","");
            $this->f->show_element("a_number","");
            print " Direction ";
            $this->f->show_element("H323CallOrigin","");
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
            Destination
            </b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("c_number_comp","");
            $this->f->show_element("c_number","");
            print " Technology ";
            $this->f->show_element("H323CallType","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";

        print "
        <tr> 
            <td align=left>
            <b>Call duration</b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("duration","");
        print "
            Disconnection cause
            ";
            $this->f->show_element("release_cause","");
            print " ReNormalize";
            print "<input type=checkbox name=ReNormalize value=1>";
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

        print "</center>";
    }

    function showSubscriber() {
        $this->show();
    }

    function show() {
        global $perm;

        foreach ($this->FormElements as $_el) {
            ${$_el} = $_REQUEST[$_el];
        }

        if (!$this->export) {
            if (!$begin_datetime) {
                $begin_datetime="$begin_year-$begin_month-$begin_day $begin_hour:$begin_min";
                $begin_datetime_timestamp=mktime($begin_hour, $begin_min, 0, $begin_month,$begin_day,$begin_year);
            } else {
                $begin_datetime_timestamp=$begin_datetime;
                $begin_datetime=Date("Y-m-d H:i",$begin_datetime);
            }

            $begin_datetime_url=urlencode($begin_datetime_timestamp);

            if (!$end_datetime) {
                $end_datetime_timestamp=mktime($end_hour, $end_min, 0, $end_month,$end_day,$end_year);
                $end_datetime="$end_year-$end_month-$end_day $end_hour:$end_min";
            } else {
                $end_datetime_timestamp=$end_datetime;
                $end_datetime=Date("Y-m-d H:i",$end_datetime);
            }
            $end_datetime_url=urlencode($end_datetime_timestamp);
        } else {
            $begin_datetime=Date("Y-m-d H:i",$begin_datetime);
            $end_datetime=Date("Y-m-d H:i",$end_datetime);
        }

        if (!$order_by) {
            $order_by=$this->timestampField;
            $order_type="DESC";
        }

        // build an url to be able to log and refine the query
		if (!$cdr_table) $cdr_table=$this->table;

        $this->url="?cdr_source=$this->cdr_source&cdr_table=$cdr_table";
        $this->url=$this->url."&order_by=$order_by&order_type=$order_type";
        $this->url=$this->url."&begin_datetime=$begin_datetime_url";
        $this->url=$this->url."&end_datetime=$end_datetime_url";
        
        $where = " ($this->startTimeField >= '$begin_datetime' and $this->startTimeField < '$end_datetime')";
        
        if ($this->CDRTool[filter][aNumber]) {
            // force user to see only this a_number
            $a_number=$this->CDRTool[filter][aNumber];
        }

        $a_number=trim($a_number);

        if ($a_number_comp == "empty") {
            $where .= " and $this->aNumberField = ''";
        } else {
            if ($a_number) {
                if (!$a_number_comp) {
                    $a_number_comp="equal";
                }
                $a_number_encoded=urlencode($a_number);
                $this->url="$this->url"."&a_number=$a_number_encoded";
                if ($a_number_comp=="begin") {
                    $where .= " and ($this->aNumberField like '".addslashes($a_number)."%'";
                    $s=1;
                } elseif ($a_number_comp=="contain") {
                    $where .= " and ($this->aNumberField like '%".addslashes($a_number)."%'";
                    $s=1;
                } elseif ($a_number_comp=="equal") {
                    $where .= " and ($this->aNumberField = '".addslashes($a_number)."'";
                    $s=1;
                }
                if ($this->CDRTool[filter][aNumber]) {
                    $where .= " or $this->cNumberField like '".addslashes($a_number)."%') ";
                } else {
                    if ($s) {
                        $where .= ")";
                    }
                }
                $this->url=$this->url."&a_number_comp=$a_number_comp";
            }
        }

        if ($UserName_comp != "empty") {
            if ($UserName) {
                if (!$UserName_comp) {
                    $UserName_comp="begin";
                }
                $UserName_encoded=trim($UserName);
                $UserName_encoded=urlencode($UserName);
                $this->url="$this->url"."&UserName=$UserName_encoded";
                if ($UserName_comp=="begin") {
                    $where .= " and $this->usernameField like '".addslashes($UserName)."%'";
                } elseif ($UserName_comp=="contain") {
                    $where .= " and $this->usernameField like '%".addslashes($UserName)."%'";
                } elseif ($UserName_comp=="equal") {
                    $where .= " and $this->usernameField = '".addslashes($UserName)."'";
                }
            }
        } else {
            $where .= " and $this->usernameField = ''";
        }

        if ($NASPortId) {
            $NASPortId_encoded=urlencode($NASPortId);
            $where = "$where"." and $this->NASPortIdField = '".addslashes($NASPortId)."'";
            $this->url="$this->url"."&$this->NASPortIdField=$NASPortId_encoded";
        }

        $call_id_encoded=trim($call_id);
        if ($call_id) {
            $where = "$where"." and $this->callIdField = '".addslashes($call_id)."'";
            $call_id_encoded=urlencode($call_id);
            $this->url="$this->url"."&call_id=$call_id_encoded";
        }

        if ($release_cause) {
            $release_cause_hex=dechex($release_cause);
            $where = "$where"." and $this->disconnectField = '".addslashes($release_cause_hex)."'";
            $this->url="$this->url"."&release_cause=$release_cause";
        }

        if ($DestinationId) {
            $where = "$where"." and $this->DestinationIdField = '".addslashes($DestinationId)."'";
            $this->url="$this->url"."&$this->DestinationIdField=$DestinationId";
        }

        if ($H323CallType) {
            $where = "$where"." and $this->H323CallTypeField = '".addslashes($H323CallType)."'";
            $this->url="$this->url"."&$this->H323CallTypeField=$H323CallType";
        } else {
            if ($group_by) {
                if ($group_by=="RemoteAddress") {
                    $where = "$where"." and $this->H323CallTypeField = 'VoIP'";
                    $this->url="$this->url"."&$this->H323CallTypeField=VoIP";
                } else {
                    $where = "$where"." and $this->H323CallTypeField  = 'Telephony'";
                    $this->url="$this->url"."&$this->H323CallTypeField=Telephony";
                }
            } else {
                $where = "$where"." and ($this->H323CallTypeField = 'VoIP' or
                $this->H323CallTypeField = 'Telephony')";
            }
        }

        if ($NASIPAddress) {
            $NASIPAddress_encoded=urlencode($NASIPAddress);
            $where = "$where"." and $this->localGatewayField = '".addslashes($NASIPAddress)."'";
            $this->url="$this->url"."&$this->localGatewayField=$NASIPAddress_encoded";
        }

        if ($RemoteAddress) {
            $where = "$where"." and $this->RemoteAddressField = '".addslashes($RemoteAddress)."'";
            $this->url="$this->url"."&RemoteAddress=$RemoteAddress";
        }

        if ($H323CallOrigin) {
            $where = "$where"." and $this->H323CallOriginField = '".addslashes($H323CallOrigin)."'";
            $this->url="$this->url"."&$this->H323CallOriginField=$H323CallOrigin";
        }

        $c_number=trim($c_number);
        if (strlen($c_number)) {

            # Trim content of dest_form - allow only digits
            if ($c_number_comp=="begin") {
                $where = "$where"." and  $this->cNumberField like '".addslashes($c_number)."%'";
            } elseif ($c_number_comp=="equal") {
                $where = "$where"." and $this->cNumberField = '".addslashes($c_number)."'";
            } elseif ($c_number_comp=="contain") {
                $where .= " and $this->cNumberField like '%".addslashes($c_number)."%'";
            } else {
                $where = "$where"." and $this->cNumberField like '%".addslashes($c_number)."%'";
            }
            $c_number_encoded=urlencode($c_number);
            $this->url=$this->url."&c_number=$c_number_encoded&c_number_comp=$c_number_comp";
        }

        if ($duration) {
            if (preg_match("/\d+/",$duration) ) {
                $where .= " and ($this->durationField > 0 and $this->durationField $duration) ";
            } elseif ($duration == "zero") {
                $where = "$where"." and $this->durationField = 0";
            } elseif ($duration == "nonzero") {
                $where = "$where"." and $this->durationField > 0";
            }

            $duration_enc=urlencode($duration);
            $this->url="$this->url"."&duration=$duration_enc";
        }
        
        $this->url="$this->url"."&maxrowsperpage=$this->maxrowsperpage";
        $url_calls = $this->scriptFile.$this->url."&action=search";

        if ($group_by) {
            $this->url="$this->url"."&group_by=$group_by";
        }

        $this->url_edit	    = $this->scriptFile.$this->url."&action=edit";
        $this->url_run	    = $this->scriptFile.$this->url."&action=search";
        $this->url_export	= $_SERVER["PHP_SELF"].$this->url."&action=search&export=1";
        
        if ($group_by) {
            if (!$perm->have_perm("statistics")) {
                print "<p><font color=red>You do not have the right for statistics.</font>";
                return 0 ;
            }

            $this->group_by=$group_by;

            $query= "
            select sum($this->durationField) as duration,
            count($group_by) as calls, $group_by
            from $this->table
            where $where
            group by $group_by
            ";
        } else {
            $query = "select count(*) as records from $this->table where"."$where";
        }
        
        dprint("$query");
        
        if ($this->CDRdb->query($query)) {
             $this->CDRdb->next_record();
             if ($group_by) {
                $rows=$this->CDRdb->num_rows();
             } else {
                $rows = $this->CDRdb->f('records');
             }
        } else {
            print "$this->CDRdb->Error";
            $rows = 0;
        }

        $this->rows=$rows;

        if ($this->CDRTool[filter][aNumber]) {
            $this->showResultsMenuSubscriber('0',$begin_datetime,$end_datetime);
        } else {
            $this->showResultsMenu('0',$begin_datetime,$end_datetime);
        }

        if (!$this->next)   {
            $i=0;
            $this->next=0;
        } else {
            $i=$this->next;
        }
        $j=0;
        $z=0;
        
        if ($rows>0)  {
            if ($UnNormalizedCalls=$this->getUnNormalized($where,$cdr_table)) {
                dprint("Normalize calls");
                $this->NormalizeCDRS($where,$cdr_table);
                if (!$this->export && $this->status['normalized'] ) {
                    printf ("<p> Found %d CDRs for normalization. ",$this->status['normalized']);
                }
            }

            $this->$rows=$rows;

            if  ($rows > $this->maxrowsperpage)  {
                $maxrows=$this->maxrowsperpage+$this->next;
                if ($maxrows > $rows)  {
                    $maxrows=$rows;
                    $prev_rows=$maxrows;
                }
            } else {
                $maxrows=$rows;
            }
        
            if ($group_by) {
                if ($order_by == $this->inputTrafficField ||
                    $order_by == $this->outputTrafficField ||
                    $order_by == $this->durationField ||
                    $order_by == $this->priceField ||
                    $order_by == "zeroP" ||
                    $order_by == "nonzeroP"
                        ) 	{
                        $order_by1=$order_by;
                }  else {
                        $order_by1="calls";
                }

                $query= "
                select sum($this->durationField) as $this->durationField,
                SEC_TO_TIME(sum($this->durationField)) as hours,
                count($group_by) as calls, ";
                if ($this->priceField) {
                    $query.=" sum($this->priceField) as price, ";
                }
                $query.=" 
                $this->H323CallTypeField,
                sum($this->inputTrafficField) as $this->inputTrafficField,
                sum($this->outputTrafficField) as $this->outputTrafficField,
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
                $group_by as group_by
                from $this->table
                where $where
                group by $group_by
                order by $order_by1 $order_type
                limit $i,$this->maxrowsperpage
                ";

                dprint($query);
                $this->CDRdb->query($query);

                $this->showTableHeaderStatistics();

                while ($i<$maxrows)  {
                
                    $found=$i+1;
                    $this->CDRdb->next_record();											
                    $seconds    	   	     =$this->CDRdb->f($this->durationField);
                    $seconds_print 	   	     =number_format($this->CDRdb->f($this->durationField),0);
                    $minutes	   	         =number_format($this->CDRdb->f($this->durationField)/60,0,"","");
                    $minutes_print	   	     =number_format($this->CDRdb->f($this->durationField)/60,0);
                    $hours      	     	 =$this->CDRdb->f('hours');
                    $calls		     		 =$this->CDRdb->f('calls');
                    if ($this->priceField) {
                        $price      	     	 =$this->CDRdb->f('price');
                    }
                    $AcctInputOctets         =number_format($this->CDRdb->f($this->inputTrafficField)/1024,2,".",".");
                    $AcctOutputOctets        =number_format($this->CDRdb->f($this->outputTrafficField)/1024,2,".",".");
                    $H323CallType            =$this->CDRdb->f($this->H323CallTypeField);
                    $AcctTerminateCause      =$this->CDRdb->f($this->disconnectField);
                    $mygroup		      	 =$this->CDRdb->f('group_by');

                    $zero		             =$this->CDRdb->f('zero');
                    $nonzero	             =$this->CDRdb->f('nonzero');
                    $success		         =number_format($nonzero/$calls*100,2,".",".");
                    $failure		         =number_format($zero/$calls*100,2,".",".");
        
                    if ($group_by==$this->disconnectField) {
                        if ($mygroup=="AC") {
                            $mygroup="22";
                        }
                        $DisconnectCause=hexdec($mygroup);
                        $mygroup_print=hexdec($mygroup);
                        $mygroup_enc=urlencode($DisconnectCause);
                    } else {
                        $DisconnectCause     ="";
                        $mygroup_print=$mygroup;
                        $mygroup_enc=urlencode($mygroup);
                    }
        
                    $rr=floor($found/2);
                    $mod=$found-$rr*2;
                
                    if ($mod ==0) {
                        $inout_color="lightgrey";
                    } else {
                        $inout_color="white";
                    }

                    if ($group_by == $this->disconnectField) {
                        $description=$this->disconnectCodesDescription[$DisconnectCause];
                    } else if ($group_by==$this->DestinationIdField) {
                        if ($this->CDRTool['filter']['aNumber']) {
                            $description=$this->destinations[$this->CDRTool['filter']['aNumber']][$mygroup];
                        } else if ($this->CDRTool['filter']['domain']) {
                            $description=$this->destinations[$this->CDRTool['filter']['domain']][$mygroup];
                        } else {
                        	$description=$this->destinations["default"][$mygroup];
                        }
                    } else {
                        $description="";
                    }
                    if ($group_by==$this->aNumberField) {
                        $traceField="a_number";
                    } else if ($group_by==$this->cNumberField) {
                        $traceField="c_number";
                    } else if ($group_by==$this->disconnectField) {
                        $traceField="release_cause";
                    } else {
                        $traceField=$group_by;
                    }
                    if (!$this->export) {
                        $pricePrint=number_format($price,4);

                        print  "
                        <tr bgcolor=$inout_color>
                        <td><b>$found</b></td>
                        <td align=right>$calls</td>
                        <td align=right>$seconds_print</td>
                        <td align=right>$minutes_print</td>
                        <td align=right>$hours</td>";
                        if ($this->rating) {
                            print "<td align=right>$pricePrint</td>";
                        }
                        print "
                        <td align=right>$AcctInputOctets
                        <td align=right>$AcctOutputOctets
                        <td align=right>$success%</td>
                        <td align=right>($nonzero calls)</td>
                        <td align=right>$failure%</td>
                        <td align=right>($zero calls)</td>
                        <td>$mygroup_print
                        <td>$description
                        <td align=right>$H323CallType</td>
                        <td><a href=$url_calls&$traceField=$mygroup_enc target=_new>Display calls </a>
                        </tr>
                        ";
                    } else {
                         print "$found,$calls,$seconds,$minutes,$hours,$AcctInputOctets,$AcctOutputOctets,$success,$nonzero,$failure,$zero,$mygroup_print,$description\n";
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
                if ($order_by=="zeroP" || $order_by=="nonzeroP") {
                    $order_by="timestamp";
                }
                $query = "select *, UNIX_TIMESTAMP($this->startTimeField) as timestamp
                from $this->table where ".
                "$where ".
                "order  by  $order_by $order_type ".
                "limit $i,$this->maxrowsperpage";

                dprint($query);
                $this->CDRdb->query($query);

                if (!$this->export) {
                	$this->showTableHeader();
                } else {
                	$this->showExportHeader();
                }

                while ($i<$maxrows)  {
                    global $found;
                    $found=$i+1;
                    $this->CDRdb->next_record();											

					$Structure=$this->_readCDRFieldsFromDB();
                    $CDR = new $this->CDR_class($this, $Structure);

                 	if (!$this->export) {
                    	$CDR->show();
                    } else {
                    	$CDR->export();
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

            dprint("$query");
            $this->showPagination($this->next,$maxrows);
            
        }
        
    }

    function import($file,$timezone) {
        $b=time();
        $this->radiusAttributes=array(
            "Acct-Session-Id"            => "AcctSessionId",
            "Calling-Station-Id"         => "CallingStationId",
            "Called-Station-Id"          => "CalledStationId",
            "Acct-Status-Type"           => "",
            "Realm"                      => "",
            "Acct-Authentic"             => "AcctAuthentic",
            "Acct-Terminate-Cause"       => "AcctTerminateCause",
            "User-Name"                  => "UserName",
            "Service-Type"               => "ServiceType",
            "NAS-IP-Address"             => "NASIPAddress",
            "Acct-Delay-Time"            => "AcctDelayTime",
            "Client-IP-Address"          => "ClientIPAddress",
            "Acct-Unique-Session-Id"     => "AcctUniqueId",
            "Timestamp"                  => "Timestamp",
            "NAS-Port-Id"                => "NASPortId",
            "Cisco-NAS-Port"             => "CiscoNASPort",
            "NAS-Port-Type"              => "NASPortType",
            "NAS-Port"                   => "NASPort",
            "Framed-Protocol"            => "FramedProtocol",
            "Framed-IP-Address"          => "FramedIPAddress",
            "Connect-Info"               => "ConnectInfo_stop",
            "h323-setup-time"            => "H323SetupTime",
            "h323-connect-time"          => "H323ConnectTime",
            "h323-disconnect-time"       => "H323DisconnectTime",
            "h323-disconnect-cause"      => "H323DisconnectCause",
            "h323-remote-address"        => "H323RemoteAddress",
            "h323-voice-quality"         => "H323VoiceQuality",
            "h323-gw-id"                 => "H323GWID",
            "h323-conf-id"	             => "H323ConfID",
            "h323-call-origin"           => "H323CallOrigin",
            "h323-call-type"             => "H323CallType",
            "Acct-Input-Octets"          => "AcctInputOctets",
            "Acct-Output-Octets"         => "AcctOutputOctets",
            "Acct-Input-Packets"         => "AcctInputPackets",
            "Acct-Output-Packets"        => "AcctOutputPackets",
            "Acct-Session-Time"          => "AcctSessionTime"
            );

        if (!$fp=fopen("$file","r")) {
            print "Error: cannot open file $file\n";
            return 0;
        } else {
            $filesize=filesize($file);
            print "File $file (size=$filesize) succesfully opened.\n";
        }

        $success=0;
        $failed =0;

        if ($filesize > 100000) {
            print "Import progress: ";
            flush();
        }

        while (!feof($fp)) {
            $record       = $this->RadiusRecordRead($fp);
            $radiusParsed = $this->RadiusRecordParse($record);
            $currentFilePointer=ftell($fp);
            if ($filesize > 100000) {
                if ($currentFilePointer> $progress*$filesize/100) {
                    $progress++;
                    if ($progress%10==0) {
                        print "$progress% ";
                        if ($progress==100) {
                            print "\n";
                        }
                    }
                    flush();
                }
            }
            dprint_r($radiusParsed);
            $packetType   = $radiusParsed["Acct-Status-Type"];
            $radiusTable  = $this->table;
            unset($query);

            if ($packetType=="Start") {
                $results[$file][$packetType]++;
                $accounting_start_query= "INSERT into $this->table (RadAcctId, AcctSessionId, AcctUniqueId, UserName, Realm, NASIPAddress, NASPortId, NASPortType, AcctStartTime, AcctStopTime, AcctSessionTime, AcctAuthentic, ConnectInfo_start, ConnectInfo_stop, AcctInputOctets, AcctOutputOctets, CalledStationId, CallingStationId, AcctTerminateCause, ServiceType, FramedProtocol, FramedIPAddress, AcctStartDelay, AcctStopDelay) values('', '%{Acct-Session-Id}', '%{Acct-Unique-Session-Id}', '%{SQL-User-Name}', '%{Realm}', '%{NAS-IP-Address}', '%{NAS-Port-Id}', '%{NAS-Port-Type}', '%S', '0', '0', '%{Acct-Authentic}', '%{Connect-Info}', '', '0', '0', '%{Called-Station-Id}', '%{Calling-Station-Id}', '', '%{Service-Type}', '%{Framed-Protocol}', '%{Framed-IP-Address}', '%{Acct-Delay-Time}', '0')";
                $query=$accounting_start_query;
				$query=preg_replace("/%{/","",$query);
                $query=preg_replace("/}'/","'",$query);
                $query=preg_replace("/}\)/",")",$query);
                $Timestamp=getLocalTime($timezone,$radiusParsed[Timestamp]);

                $query=preg_replace("/'%S'/","'$Timestamp'",$query);
                $query=preg_replace("/SQL-User-Name/","User-Name",$query);
                foreach (array_keys($this->radiusAttributes) as $attribute) {
                	$value=$radiusParsed[$attribute];
                    $query=preg_replace("/'$attribute'/","'$value'",$query);
                }
            } else if ($packetType=="Stop") {
				$accounting_stop_query = "INSERT into $this->table (RadAcctId, AcctSessionId, AcctUniqueId, UserName, Realm, NASIPAddress, CiscoNASPort, NASPortId, NASPortType, AcctStartTime, AcctStopTime, AcctSessionTime, AcctAuthentic, ConnectInfo_start, ConnectInfo_stop, AcctInputOctets, AcctOutputOctets, CalledStationId, CallingStationId, AcctTerminateCause, ServiceType, FramedProtocol, FramedIPAddress, AcctStartDelay, AcctStopDelay, H323GWId, H323CallOrigin, H323CallType, H323Setuptime,H323ConnectTime, H323DisconnectTime,H323DisconnectCause,H323RemoteAddress,H323VoiceQuality, H323ConfId, Timestamp ) values  ('', '%{Acct-Session-Id}', '%{Acct-Unique-Session-Id}', '%{SQL-User-Name}', '%{Realm}', '%{NAS-IP-Address}', '%{Cisco-NAS-Port}','%{NAS-Port-Id}', '%{NAS-Port-Type}', from_unixtime(unix_timestamp('%S')-%{Acct-Session-Time}), '%S', '%{Acct-Session-Time}', '%{Acct-Authentic}', '', '%{Connect-Info}', '%{Acct-Input-Octets}', '%{Acct-Output-Octets}', '%{Called-Station-Id}', '%{Calling-Station-Id}', '%{Acct-Terminate-Cause}', '%{Service-Type}', '%{Framed-Protocol}', '%{Framed-IP-Address}', '0', '%{Acct-Delay-Time}', SUBSTRING('%{h323-remote-address}',21), SUBSTRING('%{h323-call-origin}',18), SUBSTRING('%{h323-call-type}',16), SUBSTRING('%{h323-setup-time}',18), SUBSTRING('%{h323-connect-time}',20), SUBSTRING('%{h323-disconnect-time}',23), SUBSTRING('%{h323-disconnect-cause}',23), SUBSTRING('%{h323-remote-address}',21),SUBSTRING('%{h323-voice-quality}',20), SUBSTRING('%{h323-conf-id}',14) , '%{Timestamp}')";
                $query=$accounting_stop_query;
				$query=preg_replace("/%{/","",$query);
                $query=preg_replace("/}'/","'",$query);
                $query=preg_replace("/}\)/",")",$query);
                $Timestamp=getLocalTime($timezone,$radiusParsed[Timestamp]);

                $query=preg_replace("/'%S'/","'$Timestamp'",$query);
                $query=preg_replace("/SQL-User-Name/","User-Name",$query);
                foreach (array_keys($this->radiusAttributes) as $attribute) {
                	$value=$radiusParsed[$attribute];
                    $query=preg_replace("/'$attribute'/","'$value'",$query);
                    $value=$radiusParsed['Acct-Session-Time'];
                    $query=preg_replace("/Acct-Session-Time/","$value",$query);
                }
            }

            if ($query) {
            	$results[$file][packetTypes][$packetType]++;
            	$AcctSessionId=$radiusParsed["Acct-Session-Id"];
            	dprint ("$packetType Radius packet $AcctSessionId\n");
                $this->CDRdb->Halt_On_Error="no";
                dprint("$query\n");

                if ($this->CDRdb->query($query)) {
                    if ($packetType=="Start" || $packetType=="Failed" ) {
                        if ($this->CDRdb->affected_rows()) {
                            $results[$file][database][insert]++;
                        } else {
                            $results[$file][database][insertExists]++;
                        }
                    } else if ($packetType=="Stop") {
                        if ($this->CDRdb->affected_rows()) {
                            $results[$file][database][update]++;
                        } else {
                            $results[$file][database][updateExists]++;
                        }

                    }
                } else {
                    $results[$file][database][errorType][$packetType]++;
                    $results[$file][database][error]++;
                    $results[$file][database][errors][$this->CDRdb->Errno]++;
                    $results[$file][database][errorDict][$this->CDRdb->Errno]=$this->CDRdb->Error;
                }
                $results[$file][database][queries]++;
            }
        }

        $e=time();
        $d=$e-$b;

        print "Import results (script runtime $d s)\n";
        foreach (array_keys($results[$file][packetTypes]) as $ptype) {
        	$howmany=$results[$file][packetTypes][$ptype];
            print "$ptype: $howmany packets ";
            $totalPackets=$totalPackets+$howmany;
        }

        print "\nDatabase import statistics: ";
        if ($results[$file][database][insert]) {
            $howmany=$results[$file][database][insert];
            print "$howmany records inserted ";
        }

        if ($results[$file][database][update]) {
            $howmany=$results[$file][database][update];
            print "$howmany records updated";
        }

        if ($results[$file][database][error]) {
            $howmany=$results[$file][database][error];
            $queries=$results[$file][database][queries];
            print "\n$queries queries from which $howmany queries failed\n";
        	foreach (array_keys($results[$file][database][errors]) as $error) {
        		$howmany=$results[$file][database][errors][$error];
                $errorDescription=$results[$file][database][errorDict][$error];
                print "MySQL Error $error ($errorDescription): $howmany errors\n";
            }
            print "Error were generated by the folowing RADIUS packet types:\n";
        	foreach (array_keys($results[$file][database][errorType]) as $errorType) {
        		$howmany=$results[$file][database][errorType][$errorType];
                print "$errorType packet: $howmany errors\n";
            }
        }

        if ($d) {
        	$importSpeed=number_format($totalPackets/$d,0,"","");
            print "Import speed: $importSpeed packets / second\n";
        }
    }

}

class CDR_cisco extends CDR {

    function CDR_cisco(&$parent, $CDRfields) {

        $this->CDRS = & $parent;
        dprint("<hr>Init CDR");
        dprint($this->timestampField);
        dprint_r($CDRfields);

        foreach (array_keys($this->CDRS->CDRFields) as $field) {
        	$key=$this->CDRS->CDRFields[$field];
            $this->$field = quoted_printable_decode($CDRfields[$key]);
            $mysqlField=$this->CDRS->CDRFields[$field];
            $_field=$field."Field";
            $this->$_field=$mysqlField;
        }

        if ($this->CDRS->rating) {
            $this->showRate           = $this->CDRS->showRate;
        }

        $this->dayofweek       = date("w",$this->timestamp);
        $this->hourofday       = date("G",$this->timestamp);
        $this->dayofyear       = date("Y-m-d",$this->timestamp);
        $this->traffic         = 2*($this->inputTraffic+$this->outputTraffic);

        if (!$this->NASPortId) {
            $this->NASPortIdPrint=$this->RemoteAddress;
        } else {
            $this->NASPortIdPrint=$this->NASPortId;
        }

        if (preg_match("/^(ISDN) ([0-9]+)/",$this->NASPortIdPrint,$m)) {
            $this->gateway=$this->localGateway."-".$m[1]."-".$m[2];
        } else if ($this->RemoteAddress) {
            $this->gateway=$this->RemoteAddress;
        }

        if ($this->H323GWID) {
            $this->aNumberPrint=$this->aNumber."@".$this->H323GWID;
        } else {
            $this->aNumberPrint=$this->aNumber;
        }

        dprint("A number");
        $NormalizedNumber         = $this->CDRS->NormalizeNumber($this->aNumber,"source");
	    $this->aNumberPrint       = $NormalizedNumber[NumberPrint];
        $this->aNumberNormalized  = $NormalizedNumber[Normalized];

        dprint("C number");
		$NormalizedNumber         = $this->CDRS->NormalizeNumber($this->cNumber);
	    $this->cNumberPrint       = $NormalizedNumber[NumberPrint];
        $this->cNumberNormalized  = $NormalizedNumber[Normalized];
        $this->DestinationId      = $NormalizedNumber[DestinationId];
        $this->destinationName    = $NormalizedNumber[destinationName];

        $this->inputTrafficPrint  = number_format(2*$this->inputTraffic/1024,2);
        $this->outputTrafficPrint = number_format(2*$this->outputTraffic/1024,2);

        $this->durationPrint=sec2hms($this->duration);
        $this->disconnectPrint=$this->NormalizeDisconnect($this->disconnect);

        if ($this->H323CallOrigin=="answer") {
            $this->H323CallOrigin="incoming";
        } elseif ($this->H323CallOrigin=="originate") {
            $this->H323CallOrigin="outgoing";
        }

        $this->traceIn();
        $this->traceOut();

		if ($this->price == "0.0000") {
        	$this->pricePrint="";
        } else {
        	$this->pricePrint=$this->price;
        }

        $this->NetworkRateDictionary=array(
                                  'callId'          => $this->callId,
                                  'Timestamp'       => $this->timestamp,
                                  'Duration'        => $this->duration,
                                  'inputTraffic'    => $this->inputTraffic,
                                  'outputTraffic'   => $this->outputTraffic,
                                  'Application'     => "audio",
                                  'Gateway'         => $this->gateway,
                                  'From'            => $this->aNumberNormalized,
                                  'To'              => $this->cNumberNormalized
                                  );
    }

    function NormalizeDisconnect() {
        $this->disconnect=hexdec($this->disconnect);
        $causePrint=$this->CDRS->disconnectCodesDescription[$this->disconnect];
        return $causePrint;
    }

	function traceIn() {
        if ($this->H323CallOrigin=="incoming" && $this->CDRS->traceInURL[$this->gateway]) {
            $tplus = $this->timestamp+$this->duration+300;
            $tmin  = $this->timestamp-300;
            $url_trace="&trace=1&action=search&c_number=$this->cNumber&c_number_comp=contain&begin_datetime=$tmin&end_datetime=$tplus";
            $this->traceIn="<a href=".$this->CDRS->traceInURL[$this->gateway].$url_trace." target=bottom>In</a>";
        }
    }

    function traceOut() {
    	if (!$this->H323CallOrigin=="outgoing" || !$this->CDRS->traceOutURL[$this->gateway]) {
            return;
        }

        $datasource=$this->CDRS->traceOutURL[$this->gateway];
        global $DATASOURCES;

    	if (!$datasource || !$DATASOURCES[$datasource]) {
            return;
        }

        $tplus     = $this->timestamp+$this->duration+300;
        $tmin      = $this->timestamp-300;
        $cdr_table = $DATASOURCES[$datasource][table];
		$c_number  = preg_replace("/^(0+)/","",$this->cNumber);

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

    function showSubscriber() {
        $this->show();
    }

    function show() {
        global $found;

        $rr=floor($found/2);
        $mod=$found-$rr*2;

        if ($mod ==0) {
            $inout_color="lightgrey";
        } else {
            $inout_color="white";
        }

        $check=date("Y-m-d H:i",$this->timestamp);
        print  "
        <tr bgcolor=$inout_color>
        <td><b>$found</b></td>
        <td>$this->startTime</td>
        <td>$this->aNumberPrint</td>
        <td>$this->traceIn</td>
        <td>$this->cNumberPrint</td>
        <td>$this->destinationName $this->DestinationId</td>
        <td>$this->traceOut</td>
        ";
        if ($this->duration) {
            print "<td align=right>$this->durationPrint</td>";
        } else {
            print "<td align=right><font color=red>$this->durationPrint</a></td>";
        }

        if ($this->CDRS->rating) {
            $this->ratePrint=nl2br($this->rate);
            if ($this->showRate) {
                print "<td>$this->ratePrint</td>";
            }
            if ($this->price) {
                $quotedRate = addcslashes(nl2br($this->rate), "\n\r");
                $jscode="onMouseOver=\"stm('Rating info', '$quotedRate', Style['right'])\" onMouseOut=\"htm()\"";
            } else {
                $jscode = "";
            }
            print "<td valign=top align=right $jscode>$this->pricePrint</td>
            ";
        }

        print "
        <td>$this->gateway</td>
        <td>$this->inputTrafficPrint</td>
        <td>$this->outputTrafficPrint</td>
        <td>$this->disconnectPrint ($this->disconnect)</td>
        <td>$this->NASPortIdPrint</td>
        <td>$this->H323CallOrigin</td>
        <td>$this->H323CallType</td>
        </tr>
        ";
    }

    function export() {
        global $found;

        print "$found";
        print ",$this->startTime";
        print ",$this->username";
        print ",$this->aNumberPrint";
        print ",$this->cNumberPrint";
        print ",$this->DestinationId";
        print ",$this->destinationName";
        print ",$this->duration";
        print ",$this->pricePrint";
        print ",$this->gateway";
        print ",$this->inputTraffic";
        print ",$this->outputTraffic";
        print ",$this->disconnect";
        print ",$this->disconnectPrint";
        print ",$this->NASPortIdPrint";
        print ",$this->H323CallOrigin";
        print ",$this->H323CallType";
        print "\n";
    }
}
?>
