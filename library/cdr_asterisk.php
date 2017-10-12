<?php
class CDRS_asterisk extends CDRS {
    var $table               = "cdr";
    var $CDR_class           = "CDR_asterisk";

    // mapping between CDRTool fields and database fields

    var $CDRFields=array('id'   	       => 'id',
                         'callId'          => 'uniqueid',
                         'duration'        => 'billsec',
                         'startTime'       => 'calldate',
                         'aNumber'  	   => 'clid',
                         'username'        => 'src',
                         'UserName'        => 'src',
                         'domain'	       => 'dcontext',
                         'cNumber'   	   => 'dst',
                         'timestamp'	   => 'timestamp',
                         'applicationType' => 'lastapp',
                         'BillingPartyId'  => 'src',
                         'billingCode'     => 'accountcode',
                         'BillingPartyId'  => 'src',
                         'appDuration'     => 'duration',
                         'data'            => 'lastdata',
                         'channelIn'       => 'channel',
                         'channelOut'      => 'dstchannel',
                         'normalized'      => 'Normalized',
                         'rate'            => 'Rate',
                         'price'           => 'Price',
                         'BillingPartyId'  => 'UserName',
                         'DestinationId'   => 'DestinationId'
                         );

    var $CDRNormalizationFields=array('id'   	        => 'id',
                                      'callId'          => 'uniqueid',
                                      'username'        => 'src',
                                      'domain'	        => 'dcontext',
                                      'duration'        => 'billsec',
                                      'startTime'       => 'calldate',
                                      'aNumber'  	    => 'clid',
                                      'cNumber'   	    => 'dst',
                                      'timestamp'	    => 'timestamp',
                                      'BillingPartyId'  => 'src',
                                      'price'           => 'Price',
                                      'DestinationId'   => 'DestinationId'
                                      );

    var $GROUPBY   = array(
        "src"      => "Username",
        "clid"     => "Source",
        "dst"      => "Destination",
        "lastapp"  => "Application Type",
        "dcontext" => "Context",
        "DestinationId"       => "Destination Id",
        " "                    => "-------------",
        "hour"                 => "Hour of day",
        "DAYOFWEEK"            => "Day of Week",
        "DAYOFMONTH"           => "Day of Month",
        "DAYOFYEAR"            => "Day of Year"
        );

    var $FormElements=array(
        "begin_hour","begin_min","begin_month","begin_day","begin_year","begin_datetime","end_date","begin_date",
        "end_hour","end_min","end_month","end_day","end_year","end_datetime","end_date","begin_date",
        "call_id","a_number","a_number_comp","c_number","c_number_comp","DestinationId","ExcludeDestinations",
        "unnormalize",
        "UserName","UserName_comp","BillingId",
        "applicationType","context","channel_in","channel_out","data",
        "duration","action","redirect","MONTHYEAR",
        "order_by","order_type","group_by","cdr_table","maxrowsperpage");

    function LoadDisconnectCodes() {
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
                    print "<td align=right><b>Price</b></td>";
                }
                print "
                <td align=center colspan=2><b>Success</b></td>
                <td align=center colspan=2><b>Failure</b></td>
                <td><b>$group_byPrint</b></td>
                <td><b>Description</b></td>
                <td><b>Action</b></td>
            </tr>
            ";
        } else {
            print  "id,Calls,Seconds,Minutes,Hours,Price,Success(%),Success(calls),Failure(%),Failure(calls),$group_byPrint,Description\n";
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
                <td> <b>Date and time</td>
                <td> <b>Username</b></td>
                <td> <b>Source</b></td>
                <td> <b>In</b></td>
                <td colspan=2> <b>Destination</b></td>
                <td> <b>Out</b></td>
                <td> <b>Dur</b></td>
                <td> <b>Price</b></td>
                <td> <b>Context</b></td>
                <td> <b>Channel in</b></td>
                <td> <b>Channel out</b></td>
                <td> <b>Account</b></td>
                <td> <b>App (Duration)</b></td>
        </tr>
        ";
    }

    function showExportHeader() {
    	print "id,StartTime,Username,Source,Destination,DestinationId,DestinationName,Duration,Price,Context,ChannelIn,ChannelOut,Account,Application,ApplicationDuration\n";
    }

    function showSubscriber() {
        $this->show();
    }

    function show() {

        global $perm;

        foreach ($this->FormElements as $_el) {
            ${$_el} = $_REQUEST[$_el];
        }

        if ($begin_time) {
            list($begin_hour,$begin_min)=explode(":",$begin_time);
        }

        if ($end_time) {
            list($end_hour,$end_min)=explode(":",$end_time);
        }

        if ($begin_date) {
            list($begin_year,$begin_month,$begin_day)=explode("-",$begin_date);
        }

        if ($end_date) {
            list($end_year,$end_month,$end_day)=explode("-",$end_date);
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

        if (!$order_by || (!$group_by && $order_by == "group_by")) {
            $order_by=$this->idField;
        }

		if (!$cdr_table) $cdr_table=$this->table;
        // build an url to be able to log and refine the query
        $this->url="?cdr_source=$this->cdr_source&cdr_table=$cdr_table";
        $this->url=$this->url."&order_by=$order_by&order_type=$order_type";
        $this->url=$this->url."&begin_datetime=$begin_datetime_url";
        $this->url=$this->url."&end_datetime=$end_datetime_url";

        if ($this->CDRTool['filter']['aNumber']) {
            $this->url   = $this->url."&aNumbers=".urlencode($this->CDRTool['filter']['aNumber']);
            $aNumbers    = explode(" ",$this->CDRTool['filter']['aNumber']);
        } else if ($aNumbers) {
            $this->url   = $this->url."&aNumbers=".urlencode($aNumbers);
            $aNumbers    = explode(" ",$aNumbers);
        }

        if ($this->CDRTool['filter']['after_date']) {
            $where .= sprintf(" and calldate >= '%s' ",$this->CDRTool['filter']['after_date']);
        }

        $where = " calldate > '$begin_datetime' and calldate <= '$end_datetime' ";
        
        $a_number=trim($a_number);

        if ($this->CDRTool['filter']['aNumber']) {
 			$where .= "
            and $this->aNumberField in (" ;
            $rr=0;
            foreach ($aNumbers as $_aNumber) {
            	$_aNumber=trim($_aNumber);
                if (strlen($_aNumber)) {
	                if ($rr) $where .= ", ";
    	            $where .= " '$_aNumber'";
        	        $rr++;
                }
            }
            $where .= ") ";

        } else if ($aNumbers)  {
 			$where .= "
            and $this->aNumberField in (" ;
            $rr=0;
            foreach ($aNumbers as $_aNumber) {
            	$_aNumber=trim($_aNumber);
                if (strlen($_aNumber)) {
	                if ($rr) $where .= ", ";
    	            $where .= " '$_aNumber'";
        	        $rr++;
                }
            }
            $where .= ") ";
        } else if ($a_number_comp == "empty") {
            $where .= " and $this->aNumberField = ''";
            $this->url=$this->url."&a_number_comp=$a_number_comp";
        } else if (strlen($a_number)) {
            $a_number=urldecode($a_number);
            if (!$a_number_comp) $a_number_comp="equal";
            $a_number_encoded=urlencode($a_number);

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
            if ($s) {
                $where .= ")";
            }
            $this->url=$this->url."&a_number_comp=$a_number_comp";

        }

        if ($channel_in) {
            $channel_in_encoded=urlencode($channel_in);
            $where = "$where"." and $this->channelInField = '".addslashes($channel_in)."'";
            $this->url="$this->url"."&channel_in=$channel_in_encoded";
        }

        if ($applicationType) {
            $where = "$where"." and $this->applicationTypeField like '".addslashes($applicationType)."%'";
            $applicationType_encoded=urlencode($applicationType);
            $this->url="$this->url"."&applicationType=$applicationType_encoded";
        }

        if ($UserName_comp != "empty") {
        	$UserName=trim($UserName);
            if ($UserName) {
                $UserName=urldecode($UserName);
                if (!$UserName_comp) {
                    $UserName_comp="begin";
                }
                $UserName_encoded=urlencode($UserName);
                if ($UserName_comp=="begin") {
                    $where .= " and $this->usernameField like '".addslashes($UserName)."%'";
                } elseif ($UserName_comp=="contain") {
                    $where .= " and $this->usernameField like '%".addslashes($UserName)."%'";
                } elseif ($UserName_comp=="equal") {
                    $where .= " and $this->usernameField = '".addslashes($UserName)."'";
                }
                $this->url="$this->url"."&UserName=$UserName_encoded&UserName_comp=$UserName_comp";
            }

        } else {
            $where .= " and $this->usernameField = ''";
            $this->url="$this->url"."&UserName_comp=$UserName_comp";
        }

        if ($context) {
            $context_encoded=urlencode($context);
            $where = "$where"." and $this->domainField = '".addslashes($context)."'";
            $this->url="$this->url"."&context=$context_encoded";
        }
        
        if ($channel_out) {
            $channel_out_encoded=urlencode($channel_out);
            $where = "$where"." and $this->channelOutField = '".addslashes($channel_out)."'";
            $this->url="$this->url"."&channel_out=$channel_out_encoded";
        }
        
        if (strlen($c_number)) {
            # Trim content of dest_form - allow only digits
            if ($c_number_comp=="begin") {
                $where = "$where"." and $this->cNumberField like '".addslashes($c_number)."%'";
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

        if ($DestinationId) {
            if ($DestinationId=="empty") {
                $DestinationIdSQL="";
            } else {
                $DestinationIdSQL=$DestinationId;
            }
            $where = "$where"." and $this->DestinationIdField = '".addslashes($DestinationIdSQL)."'";
            $DestinationId_encoded=urlencode($DestinationId);
            $this->url="$this->url"."&DestinationId=$DestinationId_encoded";
        }
        
        if ($duration) {
            if (preg_match("/\d+/",$duration) ) {
                    $where .= " and ($this->durationField > 0 and $this->durationField $duration) ";
            } elseif ($duration == "zero") {
                    $where = "$where"." and $this->durationField = 0";
            } elseif ($duration == "nonzero") {
                    $where = "$where"." and $this->durationField > 0";
            } 
            $this->url="$this->url"."&duration=$duration";
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
            $this->group_byOrig=$group_by;

            if ($group_by=="hour") {
	            $group_by="HOUR($this->startTimeField)";
            } else if (preg_match("/^DAY/",$group_by)) {
                $group_by="$group_by($this->startTimeField)";
            }
            if (!$perm->have_perm("statistics")) {
                print "<p><font color=red>You do not have the right for statistics.</font>";
                return 0 ;
            }

            $this->group_by=$group_by;

            $query= "
            select sum($this->durationField) as duration, SEC_TO_TIME(sum($this->durationField)) as duration_print,
            count($group_by) as calls, $group_by
            from $cdr_table
            where $where
            group by $group_by
            ";
        } else {
            $query = "select count(*) as records from $cdr_table where ".$where;
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
            $rows = 0;
        }

        $this->rows=$rows;
        if ($this->CDRTool['filter']['aNumber']) {
            $this->showResultsMenuSubscriber('0',$begin_datetime,$end_datetime);
        } else {
            $this->showResultsMenu('0',$begin_datetime,$end_datetime);
        }

        if  (!$this->next)   {
            $i=0;
            $this->next=0;
        } else  {
            $i=$this->next;
        }
        $j=0;
        $z=0;
        
        if  ($rows>0) {
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
                if  ($maxrows > $rows)  {
                    $maxrows=$rows;
                    $prev_rows=$maxrows;
                }
            } else {
                $maxrows=$rows;
            }

            if ($group_by) {
                if ($order_by=="group_by") {
                    $order_by1=$group_by;
                } else {
                    if ($order_by == $this->priceField ||
                        $order_by == "zeroP"    ||
                        $order_by == "nonzeroP" ||
                        $order_by == $this->durationField ) 	{
                        $order_by1 = $order_by;
                    }  else {
                        $order_by1 = "calls";
                    }
                }

                $query= "
                select
                sum($this->durationField) as $this->durationField,
                SEC_TO_TIME(sum($this->durationField)) as hours,
                count($group_by) as calls, ";
                if ($this->priceField) {
                    $query.=" sum($this->priceField) as price, ";
                }
                $query.=" 
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
                $group_by as mygroup
                from $cdr_table
                where $where
                group by $group_by
                order by $order_by1 $order_type
                limit $i, $this->maxrowsperpage
                ";
                dprint($query);

                $this->CDRdb->query($query);
            
                $this->showTableHeaderStatistics();

                while ($i<$maxrows)  {
                
                    $found=$i+1;
                    $this->CDRdb->next_record();
                    $seconds    	   	     =$this->CDRdb->Record[$this->durationField];
                    $seconds_print 	   	     =number_format($this->CDRdb->Record[$this->durationField],0);
                    $minutes	   	         =number_format($this->CDRdb->Record[$this->durationField]/60,0,"","");
                    $minutes_print	   	     =number_format($this->CDRdb->Record[$this->durationField]/60,0);
                    $hours      	     	 =$this->CDRdb->Record['hours'];
                    $calls		     		 =$this->CDRdb->Record['calls'];
                    $mygroup		      	 =$this->CDRdb->Record['mygroup'];
                    if ($this->rating && $this->priceField) {
                    	$price      	     	 =$this->CDRdb->Record['price'];
                    }
                    $zero		             =$this->CDRdb->Record['zero'];
                    $nonzero	             =$this->CDRdb->Record['nonzero'];
                    $success		         =number_format($nonzero/$calls*100,2,".","");
                    $failure		         =number_format($zero/$calls*100,2,".","");
        
                       
                    $rr=floor($found/2);
                    $mod=$found-$rr*2;
                
                    if ($mod ==0) {
                        $inout_color="lightgrey";
                    } else {
                        $inout_color="white";
                    }
        
                    $mygroup_enc=urlencode($mygroup);

                    $traceValue="";

                    if ($group_by==$this->DestinationIdField) {
					    if ($this->CDRTool['filter']['aNumber']) {
                            $description=$this->destinations[$this->CDRTool['filter']['aNumber']][$mygroup];
                        } else if ($this->CDRTool['filter']['domain']) {
                            $description=$this->destinations[$this->CDRTool['filter']['domain']][$mygroup];
                        } else {
                        	$description=$this->destinations["default"][$mygroup];
                        }

                    } else if ($group_by==$this->aNumberField) {
                        $traceField="a_number";
                    } else if ($group_by==$this->usernameField) {
                        $traceField="username";
                    } else if ($group_by==$this->cNumberField) {
                        $traceField="c_number";
                    }

                    if ($mygroup) {
                        $traceValue=$mygroup;
                    } else {
                        $traceValue="empty";
                    }

                    if (!$traceField) {
                        $traceField    = $group_by;
                    }

                    if (!$traceValue) {
                        $traceValue    = $mygroup;
                    }

                    $mygroup_print = $mygroup;

                    $traceValue_enc=urlencode($traceValue);

                    if (!$this->export) {
                        $pricePrint=number_format($price,4);

                        print  "
                        <tr bgcolor=$inout_color>
                        <td><b>$found</b></td>
                        <td align=right>$calls</td>
                        <td align=right>$seconds_print</td>
                        <td align=right>$minutes_print</td>
                        <td align=right>$hours</td>
                        <td align=right>$pricePrint</td>
                        <td align=right>$success%</td>
                        <td align=right>($nonzero calls)</td>
                        <td align=right>$failure%</td>
                        <td align=right>($zero calls)</td>
                        <td>$mygroup_print</td>
                        <td>$description</td>
                        <td><a href=$url_calls&$traceField=$traceValue_enc target=_new>Display calls</a></td>
                        </tr>
                        ";
                    } else {
                         print "$found,";
                         print "$calls,";
                         print "$seconds,";
                         print "$minutes,";
                         print "$hours,";
                         print "$price,";
                         print "$success,";
                         print "$nonzero,";
                         print "$failure,";
                         print "$zero,";
                         print "$mygroup,";
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
                if (!$order_by) {
                    $order_by=calldate;
                }

                $query = "select *,unix_timestamp(calldate) as timestamp
                from $cdr_table
                where $where
                order by $order_by $order_type
                limit $i,$this->maxrowsperpage";

                dprint("$query");
        
                $this->CDRdb->query($query);

                if (!$this->export) {
                	$this->showTableHeader();
                } else {
                    $this->showExportHeader();
                }

                while  ($i<$maxrows)  {
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
        }

        $this->showPagination($this->next,$maxrows,$rows);

    }

    function initForm() {
        // form els added below must have global vars
        foreach ($this->FormElements as $_el) {
            global ${$_el};
            ${$_el} = $_REQUEST[$_el];
        }

        $action         = "search";

        if ($this->CDRTool['filter']['aNumber']) {
            $a_number=$this->CDRTool['filter']['aNumber'];
            $UserName=$this->CDRTool['filter']['aNumber'];
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

        $this->f->add_element(array(
                    "name"=>"begin_time",
                    "size"=>"1",
		            "type"=>"text",
                    "extrahtml"=>"id='timepicker1' class=\"input-small\" data-show-meridian='false' data-minute-step='1' data-default-time='$begin_hour:$begin_min'"
                    ));
        $this->f->add_element(array(
                    "name"=>"end_time",
                    "size"=>"1",
		            "type"=>"text",
                    "extrahtml"=>"id='timepicker2' class=\"input-small\" data-show-meridian='false' data-minute-step='1' data-default-time='$end_hour:$end_min'"
                    ));

        $this->f->add_element(array(
                    "name"=>"begin_date",
                    "size"=>"10",
                    "maxlength"=>"10",
                    "type"=>"text",
                    "value"=>"$begin_year-$begin_month-$begin_day",
                    "extrahtml"=>"id='begin_date' data-date-format=\"yyyy-mm-dd\" class=\"span2\""
                    ));

        $this->f->add_element(array(
                    "name"=>"end_date",
                    "size"=>"1",
                    "type"=>"text",
                    "value"=>"$end_year-$end_month-$end_day",
                    "extrahtml"=>"id='end_date' data-date-format=\"yyyy-mm-dd\" class=\"span2\""
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

        $order_by_els=array(array("label"=>"Date","value"=>"calldate"),
                            array("label"=>"CLID","value"=>"clid"),
                            array("label"=>"Source","value"=>"src"),
                            array("label"=>"Destination","value"=>"dst"),
                            array("label"=>"Call duration","value"=>"duration"),
                            array("label"=>"Application","value"=>"lastapp"),
                            array("label"=>"Data","value"=>"lastdata"),
                            array("label"=>"Price","value"=>"Price"),
                            array("label"=>"Failures(%)","value"=>"zeroP"),
                            array("label"=>"Success(%)","value"=>"nonzeroP"),
                            array("label"=>"Group by","value"=>"group_by")
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

        $applicationType_els=array(array("label"=>"Any",               "value"=>""),
                               array("label"=>"Dial",              "value"=>"Dial"),
                               array("label"=>"Voicemail",         "value"=>"Voicemail"),
                               array("label"=>"Playback",          "value"=>"Playback"),
                               array("label"=>"Conference",        "value"=>"MeetMe"),
                               array("label"=>"Enum Lookup",       "value"=>"ENUMLookup"),
                               array("label"=>"Record",            "value"=>"Record"),
                               array("label"=>"AGI scripting",     "value"=>"AGI"),
                               array("label"=>"Music On Hold",     "value"=>"MusicOnHold"),
                               array("label"=>"Back Ground Music", "value"=>"BackGround"),
                               array("label"=>"MP3 Player",        "value"=>"MP3Player")
                               );

        $this->f->add_element(array(	"name"=>"applicationType",
                                "type"=>"select",
                                "options"=>$applicationType_els,
                                "value"=>$applicationType,
                                "size"=>"1"
                                ));

        if ($this->DATASOURCES[$this->cdr_source][contexts]) {

            $contexts_els[]=array("label"=>"All contexts","value"=>"");
            while (list($k,$v)=each($this->DATASOURCES[$this->cdr_source][contexts])) {
                $contexts_els[]=array("label"=>$v[WEBName]." (".$k.")","value"=>$k);
            }

            $this->f->add_element(array(
                                "name"=>"context",
                                "type"=>"select",
                                "options"=>$contexts_els,
                                "size"=>"1",
                                "value"=>$context
                                ));

        } else {
            $this->f->add_element(array(	"name"=>"context",
                                    "type"=>"text",
                                    "size"=>"10",
                                    "maxlength"=>"45"
                        ));
        }

        $this->f->add_element(array(	"name"=>"channel_in",
                                "type"=>"text",
                                "size"=>"20",
                                "maxlength"=>"25"
                    ));

        $this->f->add_element(array(	"name"=>"channel_out",
                                "type"=>"text",
                                "size"=>"20",
                                "maxlength"=>"25"
                    ));

        $this->f->load_defaults();

    }

    function searchForm() {
        global $perm;

        // Start displaying form
        
        $this->initForm();
        $this->f->start("","POST","","","datasource");

        print "
        <table cellpadding=5 CELLSPACING=0 border=6 width=100% align=center>
        ";
        $this->showDataSources ($this->f);
        $this->showDateTimeElements ($this->f);

        if ($this->CDRTool['filter']['aNumber']) {
        	$ff[]="a_number";
            $ff[]="a_number_comp";
            $ff[]="UserName";
            $ff[]="UserName_comp";
        }

        if ($this->CDRTool['filter']['domain']) {
        	$ff[]="context";
        }

        if (count($ff)) {
        	$this->f->freeze($ff);
        }

        print "
        <tr> 
            <td align=left>
            <b>Context</b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("context","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";
        print "
        <tr> 
            <td align=left>
            <b>Channels</b>
            </td>
            <td valign=top> 
            Incomming
            ";
            $this->f->show_element("channel_in","");
            print "Outgoing";
            $this->f->show_element("channel_out","");
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
            Username
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("UserName_comp","");
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
            Source
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("a_number_comp","");
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
            Destination
            </b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("c_number_comp","");
            $this->f->show_element("c_number","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";
        
        print "
        <tr> 
            <td align=left>
            <b>Duration</b>
            </td>
            <td valign=top>   ";
             $this->f->show_element("duration","");
             print " Application ";
             $this->f->show_element("applicationType","");
             print "
             </td>
        </tr>
        <tr>
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
            print "</nobr>&nbsp;&nbsp;&nbsp; <nobr>ReNormalize";
            print "<input type=checkbox name=ReNormalize value=1>
            </nobr>";
            print "
            </td>
        </tr>
        </table>
        <p>
        <center>
        ";

        $this->f->show_element("submit","");

        $this->f->finish();

        print "</center>";

    }

    function searchFormSubscriber() {
        global $perm;

        // Start displaying form
        
        $this->initForm();
        $this->f->start("","POST","","","datasource");

        print "
        <table cellpadding=5 CELLSPACING=0 border=6 width=100% align=center>
        ";
        $this->showDataSources ($this->f);
        $this->showDateTimeElements ($this->f);

        if ($this->CDRTool['filter']['aNumber']) {
            $ff[]="a_number";
            $ff[]="a_number_comp";
            $ff[]="UserName";
            $ff[]="UserName_comp";
        }

        if ($this->CDRTool['filter']['domain']) {
        	$ff[]="context";
        }

        if (count($ff)) {
        	$this->f->freeze($ff);
        }

        print "
        <tr> 
            <td align=left>
            <b>Context</b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("context","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";
        print "
        <tr> 
            <td align=left>
            <b>Channels</b>
            </td>
            <td valign=top> 
            Incomming
            ";
            $this->f->show_element("channel_in","");
            print "Outgoing";
            $this->f->show_element("channel_out","");
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
            Source
            </b>
            </td>
            <td valign=top>
            ";
            $this->f->show_element("a_number_comp","");
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
            Destination
            </b>
            </td>
            <td valign=top>   ";
            $this->f->show_element("c_number_comp","");
            $this->f->show_element("c_number","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        ";
        
        print "
        <tr> 
            <td align=left>
            <b>Duration</b>
            </td>
            <td valign=top>   ";
             $this->f->show_element("duration","");
             print " Application ";
             $this->f->show_element("applicationType","");
             print "
             </td>
        </tr>
        <tr>
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
            print "</nobr>&nbsp;&nbsp;&nbsp; <nobr>ReNormalize";
            print "<input type=checkbox name=ReNormalize value=1>
            </nobr>";
            print "
            </td>
        </tr>
        </table>
        <p>
        <center>
        ";

        $this->f->show_element("submit","");

        $this->f->finish();

        print "</center>";

    }

}

class CDR_asterisk extends CDR {

    function CDR_asterisk($parent,$CDRfields) {

        dprint("<hr>Init CDR");
        dprint_r($CDRfields);

        $this->CDRS= $parent;

        foreach (array_keys($this->CDRS->CDRFields) as $field) {
        	$key=$this->CDRS->CDRFields[$field];
            $this->$field = $CDRfields[$key];
            $mysqlField=$this->CDRS->CDRFields[$field];
            $_field=$field."Field";
            $this->$_field=$mysqlField;
        }

        $this->dayofweek        = date("w",$this->timestamp);
        $this->hourofday        = date("G",$this->timestamp);
        $this->dayofyear        = date("Y-m-d",$this->timestamp);

        $this->aNumberPrint     = $this->aNumber;

        $NormalizedNumber       = $this->CDRS->NormalizeNumber($this->cNumber,"destination",$this->aNumberPrint,$this->domain,"");
        $this->cNumberNormalized= $NormalizedNumber[Normalized];
        $this->cNumberPrint     = $NormalizedNumber[NumberPrint];
        $this->DestinationId    = $NormalizedNumber[DestinationId];
        $this->destinationName  = $NormalizedNumber[destinationName];

        $this->durationPrint    = sec2hms($this->duration);
        $this->appDurationPrint = sec2hms($this->appDuration);

        if ($this->CDRS->rating) {
            $this->showRate           = $this->CDRS->showRate;
        }

		$chanIn_els  = explode("/",$this->channelIn);
        $chanOut_els = explode("/",$this->channelOut);

        $this->remoteGatewayIn  = $chanIn_els[0];
        $this->remoteGatewayOut = $chanOut_els[0];

        $this->traceIn();
        $this->traceOut();

		if ($this->price == "0.0000") {
        	$this->pricePrint="";
        } else {
        	$this->pricePrint=$this->price;
        }

		if (!strlen($this->username)) {
        	$this->username="unknown";
        }

        if ($this->domain) {
            $_from=$this->username."@".$this->domain;
        } else {
            $_from=$this->username;
        }

        $this->NetworkRateDictionary=array(
                                  'callId'          => $this->id,
                                  'Timestamp'       => $this->timestamp,
                                  'Duration'        => $this->duration,
                                  'Application'     => 'audio',
                                  'From'            => $_from,
                                  'To'              => $this->cNumberNormalized
                                  );
    }

    function traceIn () {
		$datasource=$this->CDRS->traceInURL[$this->remoteGatewayIn];
        global $DATASOURCES;

    	if (!$datasource || !$DATASOURCES[$datasource]) {
            return;
        }

        $tplus     = $this->timestamp+$this->duration+300;
        $tmin      = $this->timestamp-300;
        $cdr_table = $DATASOURCES[$datasource][table];
		$c_number  = preg_replace("/^(0+)/","",$this->cNumber);

        $this->traceIn=
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
                        "In".
                        "</a>";

    }

    function traceOut () {
		$datasource=$this->CDRS->traceOutURL[$this->remoteGatewayOut];
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

        $rr=floor($found/2);
        $mod=$found-$rr*2;
        
        if ($mod ==0) {
        	$inout_color="lightgrey";
        } else {
        	$inout_color="white";
        }

        if ($this->normalized) {
            $found_print=$found."N";
        } else {
            $found_print=$found;
        }

        $this->ratePrint=nl2br($this->rate);

        $CallInfoVerbose="
        <table border=0 bgcolor=#CCDDFF class=extrainfo id=row$found cellpadding=0 cellspacing=0>
        <tr>
        <td valign=top>";

        if ($this->price > 0) {
            $CallInfoVerbose.= "
        	<table border=0 cellpadding=0 cellspacing=0>

            <tr>
        		<td colspan=3><b>Rating information</b></td>

            </tr>
            <tr>
            <td></td>
            <td colspan=2>$this->ratePrint</td>
            </tr>
            </table>
            ";
        }

        $CallInfoVerbose.=  "
        </td>
        </tr>
        </table>";

        print  "
        <tr bgcolor=$inout_color>
        <td valign=top onClick=\"return toggleVisibility('row$found')\"><a href=#>$found_print</a></td>
        <td>$this->startTime</td>
        <td>$this->username</td>
        <td>$this->aNumberPrint</td>
        <td>$this->traceIn</td>
        <td>$this->cNumberPrint
        <td>$this->destinationName $this->DestinationId</td>
        <td>$this->traceOut</td>
        <td align=right>$this->durationPrint</td>
        <td valign=top align=right>$this->pricePrint</td>
        <td>$this->domain</td>
        <td>$this->channelIn</td>
        <td>$this->channelOut</td>
        <td align=right>$this->billingAccount</td>
        <td align=right>$this->applicationType ($this->appDurationPrint)</td>
        </tr>
        <tr>
        <td></td>
        <td colspan=11>$CallInfoVerbose</td>
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
        print ",$this->domain";
        print ",$this->channelIn";
        print ",$this->channelOut";
        print ",$this->billingAccount";
        print ",$this->applicationType";
        print ",$this->appDuration";
        print "\n";
    }
}

?>
