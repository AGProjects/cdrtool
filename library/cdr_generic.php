<?
if (!$maxrowsperpage) $maxrowsperpage=15;

$tz=$CDRTool['provider']['timezone'];
putenv("TZ=$tz");

class CDRS {

    var $CDR_class           = 'CDR';
    var $intAccessCode       = '00';
    var $natAccessCode       = '0';
    var $maxrowsperpage      = 15;
    var $status              = array();
    var $normalizedField     = 'Normalized';
    var $DestinationIdField  = 'DestinationId';
    var $BillingIdField      = 'UserName';
    var $sipTraceDataSource  = 'sip_trace';
    var $defaults            = array();
	var $whereUnnormalized   = '';
	var $skipNormalize       = false;
	var $reNormalize         = false;
	var $usageKeysForDeletionFromCache = array();
	var $localDomains        = array();

    var $CDRNormalizationFields=array('id'   	        => 'RadAcctId',
                                      'callId'          => 'AcctSessionId',
                                      'username'        => 'UserName',
                                      'domain'	        => 'Realm',
                                      'gateway'         => 'NASIPAddress',
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

    function _readCDRNormalizationFieldsFromDB() {
        foreach (array_keys($this->CDRNormalizationFields) as $field) {
            $mysqlField=$this->CDRNormalizationFields[$field];
            $CDRStructure[$mysqlField] = $this->CDRdb->f($mysqlField);
        }

        return $CDRStructure;
    }

    function _readCDRFieldsFromDB() {
        foreach (array_keys($this->CDRFields) as $field) {
            $mysqlField=$this->CDRFields[$field];
            $CDRStructure[$mysqlField] = $this->CDRdb->f($mysqlField);
        }
        return $CDRStructure;
    }

    function CDRS($cdr_source) {

        global $CDRTool;
        global $DATASOURCES;
   		global $RatingEngine;

        if (!$DATASOURCES[$cdr_source] || !$DATASOURCES[$cdr_source]['db_class']) {
            $log="Error: cdr_source or database not defined\n";
            print $log;
            syslog(LOG_NOTICE, $log);
            return 0;
        }

		$this->initDefaults();

        $this->cdrtool  	   = new DB_CDRTool();
        $this->cdr_source      = $cdr_source;
        $this->CDRTool         = $CDRTool;
        $this->rating_settings = $RatingEngine;
        $this->DATASOURCES     = $DATASOURCES;

        $this->cdrtool->Halt_On_Error="no";

        $this->table           = $this->DATASOURCES[$this->cdr_source]['table'];

        // init names of CDR fields
        foreach (array_keys($this->CDRFields) as $field) {
            $mysqlField=$this->CDRFields[$field];
            $_field=$field."Field";
            $this->$_field=$mysqlField;
        }

        if ($this->DATASOURCES[$this->cdr_source]['rating']) {
        	$this->ratingEnabled   = 1;
            $this->rating  = $this->DATASOURCES[$this->cdr_source]['rating'];
            if ($this->DATASOURCES[$this->cdr_source]['showRate'])   $this->showRate    = $this->DATASOURCES[$this->cdr_source]['showRate'];
            if ($this->DATASOURCES[$this->cdr_source]['rateField'])  $this->rateField   = $this->DATASOURCES[$this->cdr_source]['rateField'];
            if ($this->DATASOURCES[$this->cdr_source]['priceField']) $this->priceField   = $this->DATASOURCES[$this->cdr_source]['priceField'];
        }

        // connect to the CDR database(s)
        $_dbClass            = $this->DATASOURCES[$this->cdr_source]['db_class'];

        if (is_array($_dbClass)) {
            if (class_exists($_dbClass[0])) $this->primary_database   = $_dbClass[0];
            if (class_exists($_dbClass[1])) $this->secondary_database = $_dbClass[1];
        } else {
            $this->primary_database = $_dbClass;
        }

		if(!class_exists($this->primary_database)) {
            $log="Error instantiating db class: $this->primary_database\n";
            print $log;
            syslog(LOG_NOTICE, $log);
            return 0;
        }

		$this->CDRdb    = new $this->primary_database;

        // check db connectivity
        if (!$this->CDRdb->query('SELECT 1')) {
            if ($this->secondary_database) {
				$this->CDRdb    = new $this->secondary_database;
		        if (!$this->CDRdb->query('SELECT 1')) {
                    $log="Error: failed to connect to the CDR database\n";
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return 0;
                } else {
        			$this->CDRdb1         = new $this->secondary_database;
		            $this->db_class       = $this->secondary_database;
                }
            }
        } else {
        	$this->CDRdb1         = new $this->primary_database;
            $this->db_class       = $this->primary_database;
        }

		if ($this->DATASOURCES[$this->cdr_source]['DestinationIdField']) {
        	$this->DestinationIdField = $this->DATASOURCES[$this->cdr_source]['DestinationIdField'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['normalizedField']) {
        	$this->normalizedField=$this->DATASOURCES[$this->cdr_source]['normalizedField'];
        }

        if (strlen($this->DATASOURCES[$this->cdr_source]['intAccessCode'])) {
        	$this->intAccessCode=$this->DATASOURCES[$this->cdr_source]['intAccessCode'];
        }
    
        if (strlen($this->DATASOURCES[$this->cdr_source]['natAccessCode'])) {
        	$this->natAccessCode  = $this->DATASOURCES[$this->cdr_source]['natAccessCode'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['AccountsDBClass'] && class_exists($this->DATASOURCES[$this->cdr_source]['AccountsDBClass'])) {
            $this->AccountsDB       = new $this->DATASOURCES[$this->cdr_source]['AccountsDBClass'];
            $this->AccountsDBClass  = $this->DATASOURCES[$this->cdr_source]['AccountsDBClass'];
        } else if (class_exists('DB_openser')) {
            $this->AccountsDB       = new DB_openser();
            $this->AccountsDBClass  = 'DB_openser';
        }
    
        if ($this->DATASOURCES[$this->cdr_source]['BillingIdField']) {
            $this->BillingIdField  = $this->DATASOURCES[$this->cdr_source]['BillingIdField'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['sipTraceDataSource']) {
            $this->sipTraceDataSource=$this->DATASOURCES[$this->cdr_source]['sipTraceDataSource'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['mediaTraceDataSource']) {
            $this->mediaTraceDataSource=$this->DATASOURCES[$this->cdr_source]['mediaTraceDataSource'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['domain_table']) {
            $this->domain_table = $this->DATASOURCES[$this->cdr_source]['domain_table'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['skipNormalize']) {
            $this->skipNormalize = $this->DATASOURCES[$this->cdr_source]['skipNormalize'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['enableThor']) {
            $this->enableThor      = $this->DATASOURCES[$this->cdr_source]['enableThor'];
        }

        if (is_array($this->CDRTool['normalize']['CS_CODES'])) $this->CS_CODES=array_keys($this->CDRTool['normalize']['CS_CODES']);

        $this->missed_calls      = $this->DATASOURCES[$this->cdr_source]['missed_calls'];
        $this->traceInURL	     = $this->DATASOURCES[$this->cdr_source]['traceInURL'];
        $this->traceOutURL	     = $this->DATASOURCES[$this->cdr_source]['traceOutURL'];
        $this->protocolTraceURL  = $this->DATASOURCES[$this->cdr_source]['protocolTraceURL'];

        $spath = explode("/",$_SERVER["PHP_SELF"]);
        $last  = count($spath)-1;
        $this->scriptFile=$spath[$last];

        $this->next           = $_REQUEST["next"];
        $this->export         = $_REQUEST["export"];
        $this->trace          = $_REQUEST["trace"];

        if ($this->export) {
            $this->maxrowsperpage=10000000;
        } else {
            if ($_REQUEST["maxrowsperpage"]) {
                $this->maxrowsperpage = $_REQUEST["maxrowsperpage"];
            } else {
                $this->maxrowsperpage = "25";
            }
        }

        $this->quota_init_flag   = $this->cdr_source.':quotaCheckInit';
        $this->quota_reset_flag  = $this->cdr_source.':reset_quota_for';

        $this->LoadDisconnectCodes();
        $this->LoadDestinations();
        $this->LoadENUMtlds();
        $this->LoadDomains();
        $this->getCDRtables();


        $this->initOK=1;
    }

    function initDefaults() {
        if (is_readable('/etc/default/cdrtool')) {
            $defaultContentLines=explode("\n",file_get_contents('/etc/default/cdrtool'));
        
            foreach ($defaultContentLines as $_line) {
                list($defaults_key, $defaults_value)=explode("=",$_line);
                if (strlen($defaults_value)) $this->defaults[trim($defaults_key)]=trim($defaults_value);
            }
        }

    }

    function LoadDomains() {
    }

    function LoadAccounts() {
    }

    function LoadDestinations() {

 		$_destinations     = array();
        $_destinations_sip = array();

        $this->destinations_count     = 0;
        $this->destinations_sip_count = 0;

        $query=sprintf("select `value` from memcache where `key` = 'destinations'");
        if (!$this->cdrtool->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        if ($this->cdrtool->num_rows()) {

            $this->cdrtool->next_record();
			$mc_destinations=$this->cdrtool->f('value');

            $_dests=explode("\n",$mc_destinations);
            foreach ($_dests as $_dest)  {
                if (!strlen($_dest)) continue;
                $this->destinations_count++;
                $_els=explode("=",$_dest) ;
                $_els_parts=explode(";",$_els[0]);
                $_destinations[$_els_parts[0]][$_els_parts[1]]=$_els[1];
            }

            $query=sprintf("select `value` from memcache where `key` = 'destinations_sip'");
            if (!$this->cdrtool->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }

        	if ($this->cdrtool->num_rows()) {

            	$this->cdrtool->next_record();
                $mc_destinations_sip = $this->cdrtool->f('value');

                $_SipDests=explode("\n",$mc_destinations_sip);
                foreach ($_SipDests as $_dest)  {
                    if (!strlen($_dest)) continue;
                    $this->destinations_sip_count++;
                    $_els=explode("=",$_dest) ;
                    $_els_parts=explode(";",$_els[0]);
                    $_destinations_sip[$_els_parts[0]][$_els_parts[1]]=$_els[1];
                }
            }
        } else {

            $query="select * from destinations";
            if ($this->CDRTool['filter']['aNumber']) {
                $faNumber=$this->CDRTool['filter']['aNumber'];
                $query .= " where subscriber = '$faNumber' or
                (subscriber = '' and domain = '' and gateway = '') ";
            } else if ($this->CDRTool['filter']['domain']) {
                $fdomain=$this->CDRTool['filter']['domain'];
                $query .= " where domain = '$fdomain' or
                (subscriber = '' and domain = '' and gateway = '') ";
            } else if ($this->CDRTool['filter']['gateway']) {
                $fgateway=$this->CDRTool['filter']['gateway'];
                $query .= " where gateway = '$fgateway' or
                (subscriber = '' and domain = '' and gateway = '') ";
            }
    
            $this->cdrtool->query($query);

            $destinations_cache = "\n";
            $destinations_sip_cache = "\n";

            while($this->cdrtool->next_record()) {

                $gateway    = trim($this->cdrtool->Record['gateway']);
                $domain     = trim($this->cdrtool->Record['domain']);
                $subscriber = trim($this->cdrtool->Record['subscriber']);
                $id         = trim($this->cdrtool->Record['dest_id']);
                $name       = trim($this->cdrtool->Record['dest_name']);
                $name_print = $this->cdrtool->Record['dest_name']." (".$id.")";

                if (strstr($id,'@')) {
                    // SIP destination
	                $this->destinations_sip_count++;

                    if ($subscriber) {
                        $_destinations_sip[$subscriber][$id]=$name;
                        $destinations_sip_cache.=$subscriber.";".$id."=".$name."\n";
                        continue;
                    }
        
                    if ($domain) {
                        $_destinations_sip[$domain][$id]=$name;
                        $destinations_sip_cache.=$domain.";".$id."=".$name."\n";
                        continue;
                    }
        
                    if ($gateway) {
                        $_destinations_sip[$gateway][$id]=$name;
                        $destinations_sip_cache.=$gateway.";".$id."=".$name."\n";
                        continue;
                    }
    
                    if ($id) {
                        $_destinations_sip["default"][$id]=$name;
                        $destinations_sip_cache.="default;".$id."=".$name."\n";
                    }
                } else {
                    // PSTN destination
	                $this->destinations_count++;

                    if ($subscriber) {
                        $_destinations[$subscriber][$id]=$name;
                        $destinations_cache.=$subscriber.";".$id."=".$name."\n";
                        continue;
                    }
        
                    if ($domain) {
                        $_destinations[$domain][$id]=$name;
                        $destinations_cache.=$domain.";".$id."=".$name."\n";
                        continue;
                    }
        
                    if ($gateway) {
                        $_destinations[$gateway][$id]=$name;
                        $destinations_cache.=$gateway.";".$id."=".$name."\n";
                        continue;
                    }
    
                    if ($id) {
                        $_destinations["default"][$id]=$name;
                        $destinations_cache.="default;".$id."=".$name."\n";
                    }
                }
            }

            $query=sprintf("select `value` from memcache where `key` = 'destinations'");
            if (!$this->cdrtool->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }

    		if ($this->cdrtool->num_rows()) {
                $query=sprintf("update memcache set value = '%s' where `key` = 'destinations'",addslashes($destinations_cache));
                if (!$this->cdrtool->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                $log=sprintf("Updated %d destinations in cache key destinations",$this->destinations_count);
            	syslog(LOG_NOTICE, $log);

            } else {
                $query=sprintf("insert into memcache (`key`,`value`) values ('destinations','%s')",addslashes($destinations_cache));
                if (!$this->cdrtool->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }
                $log=sprintf("Inserted %d destinations in cache key destinations",$this->destinations_count);
            	syslog(LOG_NOTICE, $log);
            }

            $query=sprintf("select `value` from memcache where `key` = 'destinations_sip'");
            if (!$this->cdrtool->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
    
    		if ($this->cdrtool->num_rows()) {
                $query=sprintf("update memcache set value = '%s' where `key` = 'destinations_sip'",addslashes($destinations_sip_cache));
                if (!$this->cdrtool->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }
                $log=sprintf("Updated %d SIP destinations in cache key destinations_sip",$this->destinations_sip_count);
                syslog(LOG_NOTICE, $log);

            } else {
                $query=sprintf("insert into memcache (`key`,`value`) values ('destinations_sip','%s')",$destinations_sip_cache);

 				if (!$this->cdrtool->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }
                $log=sprintf("Inserted %d SIP destinations in cache key destinations_sip",$this->destinations_sip_count);
                syslog(LOG_NOTICE, $log);
            }
        }

        $this->destinations     = &$_destinations;
        $this->destinations_sip = &$_destinations_sip;

        if (is_array($this->destinations)) {
			foreach ($this->destinations as $key => $val) {
	       		$this->destinations_length[$key] = max(array_map(strlen, array_keys($val)));
	        }
        }

		$c=$this->destinations_count + $this->destinations_sip_count;
        return $c;
    }

    function LoadENUMtlds() {

		$_ENUMtlds =array();

        $query="select * from billing_enum_tlds";

        $this->cdrtool->query($query);

        while($this->cdrtool->next_record()) {

            $_ENUMtlds[trim($this->cdrtool->Record['enum_tld'])]=
                    array('discount'    => trim($this->cdrtool->Record['discount']),
                          'e164_regexp' => trim($this->cdrtool->Record['e164_regexp'])
                         );
        }

        $this->ENUMtlds    = &$_ENUMtlds;
        $c=count($this->ENUMtlds);

        return count($this->ENUMtlds);
    }

    function LoadDisconnectCodes() {
    }

    function initForm() {
    }

    function searchForm() {
    }

    function showTableHeader($begin_datetime,$end_datetime) {
    }

    function showTableHeaderStatistics($begin_datetime,$end_datetime) {
    }

    function showResultsMenu($hide_rows="") {
        global $loginname;

        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

        if (!$this->export) {
            print "
            <form action=log.phtml method=post>
            <table border=0 align=center>
            <tr>
            <td><a href=\"$this->url_edit\" >Refine search</a>
            | <a href=\"$this->url_run\">Refresh</a>
            </td>
            ";

            $log_query="insert into log
            (date,login,ip,url,results,rerun,reedit,datasource)
            values
            (NOW(),'$loginname','$REMOTE_ADDR','$this->url','$this->rows','$this->url_run','$this->url_edit','$this->cdr_source')";
            
            if ($this->cdrtool->query($log_query)) {
                $this->cdrtool->query("select LAST_INSERT_ID() as lid");
                $this->cdrtool->next_record();
                $current_log=$this->cdrtool->f('lid');
            }

            if ($this->rows) {
                print "<td> | <a href=\"$this->url_export\" target=_new>Export results to file</a></td>";
            }
            print "
            <td valign=middle>
            | <font color=blue>Want to share the results with others? </font>Give this query a name:
            </td>
            <td valign=middle>
            <input type=text name=log_description value=\"$old_description\">
            <input type=hidden name=current_log value=$current_log>
            <input type=hidden name=task value=edit>
            <input type=submit value=Save>
            </td>
            </form>
            ";

            print "
            </tr>
            </table>
            ";
            if (!$hide_rows) {
                print "
                <table width=100%>
                <tr>
                <td align=center>
                ";
                if ($this->rows == 0) {
                    print "No records found.";
                } else {
                    print "$this->rows records found. ";
                }         
                print "</td>
                </tr>
                </table>
                ";
            }
        }
    }

    function showResultsMenuSubscriber($hide_rows="") {
        global $loginname;

        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

        if (!$this->export) {
            print "
            <form action=log.phtml method=post>
            <table border=0 align=center>
            <tr>
            <td>
            <td>
            <a href=\"$this->url_edit\">Refine search</a>
            | <a href=\"$this->url_run\">Refresh</a>
            </td>
            ";
            $log_query="insert into log
            (date,login,ip,url,results,rerun,reedit,datasource)
            values
            (NOW(),'$loginname','$REMOTE_ADDR','$this->url','$this->rows','$this->url_run','$this->url_edit','$this->cdr_source')";
            
            if ($this->cdrtool->query($log_query)) {
                $this->cdrtool->query("select LAST_INSERT_ID() as lid");
                $this->cdrtool->next_record();
                $current_log=$this->cdrtool->f('lid');
            }
    
            if (!$this->CDRTool['filter']['aNumber']) {

                if ($this->rows) {
                    print " | <a href=\"$this->url_export\" target=_new>Export results to file</a>";
                }
                print "
                </td>
                <td valign=middle>
                | Save a description for this query:
                </td>
                <td valign=middle>
                <input type=text name=log_description value=\"$old_description\">
                <input type=hidden name=current_log value=$current_log>
                <input type=hidden name=task value=edit>
                <input type=submit value=Save>
                </td>
                </form>
                ";
            }

            print "
            </tr>
            </table>
            ";
            if (!$hide_rows) {
                print "
                <table width=100%>
                <tr>
                <td align=center>
                ";
                if ($this->rows == 0) {
                    print "No records found.";
                } else {
                    print "$this->rows records found. ";
                }         
                print "</td>
                </tr>
                </table>
                ";
            }
        }
    }

    function showDateTimeElements($f) {
        print "
        <tr>
            <td valign=middle align=left>
            <b>Start time</b>
            </td>
            <td>
            ";
            print "Date: ";
            $f->show_element("begin_year","");
            print "-";
            $f->show_element("begin_month","");
            print "-";
            $f->show_element("begin_day","");
            print " Time: ";
            $f->show_element("begin_hour","");
            print ":";
            $f->show_element("begin_min","");
            print "
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td valign=middle align=left>
            <b>Stop time</b>
            </td>
            <td>
             ";
             print "Date: ";
             $f->show_element("end_year","");
             print "-";
             $f->show_element("end_month","");
             print "-";
             $f->show_element("end_day","");
             print " Time: ";
             $f->show_element("end_hour","");
             print ":";
             $f->show_element("end_min","");
             print "
             </td>
        </tr>
        <tr>
        </tr>
        ";
    }

    function showDataSources ($f) {
        global $perm;
        print "
        <tr>
        <td class=cdr valign=middle  align=left>
        <b>Data source</b>
        </td>
        <td valign=middle>";
        $f->show_element("cdr_source","");
        if (count($this->tables) > 0) {
            print " Table: ";
        	$this->f->show_element("cdr_table","");
        }
        print "
        </td>
        </tr>
        <tr>
        ";
    }

    function showPagination($next,$maxrows) {
        $PHP_SELF=$_SERVER["PHP_SELF"];


        if (!$this->export) {
            print "
            <p>
            <table border=0 align=center>
            <tr>
            <td>
            ";
            if  ($next!=0  ) {
                $show_next=$this->maxrowsperpage-$next;
                if  ($show_next < 0)  {
                    $mod_show_next  =  $show_next-2*$show_next;
                }
                $url_prev=$PHP_SELF.$this->url."&action=search&next=$mod_show_next";
                print "<a href=\"$url_prev\">Previous</a> ";
            }
            
            print "
            </td>
            <td>
            ";
            if ($this->rows>$this->maxrowsperpage && $this->rows!=$maxrows)  {
                $show_next = $this->maxrowsperpage + $this->next;
                $url_next  = $PHP_SELF.$this->url."&action=search&next=$show_next";
                print "<a href=\"$url_next\">Next</a>";
            }
            print "
            </td>
            </tr>
            </table>
            ";
        }
    }

    function show() {
    }

    function dump() {
    }

    function unNormalize($where="",$table) {

        // do not allow renormalization for readonly accounts
        global $perm;
        if (is_object($perm) && $perm->have_perm('readonly')) return false;

        if (!$where) $where=" (1=1) ";

        if (!$table) $table=$this->table;

        if ($this->skipNormalize) {
            return 0;
        }

        if (!$this->normalizedField) {
            return 0;
        }

        $query=sprintf("update %s set %s = '0' where %s ",
        $table,
        $this->normalizedField,
        $where
        );

        $c=0;

        if ($this->CDRdb->query($query)) {
        	$c=$this->CDRdb->affected_rows();
        	$this->reNormalize=true;
        }

        return $c;
    }

    function buildWhereForUnnormalizedSessions () {

    	$this->whereUnnormalized = sprintf(" %s = '0'",$this->normalizedField);

        if ($this->stopTimeField) $this->whereUnnormalized .= " and $this->stopTimeField != '0000-00-00 00:00:00' ";

        if ($this->CDRFields['MediaTimeout']) {
            /*
            If we use MediaProxy information then eliminate all possible raise conditions
    
            1. Session started and is in progress:
                                AcctStopTime = '0000-00-00 00:00:00'
                                AcctSessionTime = 0
                                MediaInfo is NULL
                                ConnectInfo_stop is NULL
    
            2. Session closed with a negative response code ([4-6]XX):
                                AcctSessionTime = 0
                                AcctStopTime != '0000-00-00 00:00:00'
                                MediaInfo is NULL
                                ConnectInfo_stop is NULL
    
            3. Session received a BYE:
                                ConnectInfo_stop is not NULL
                                AcctStopTime != '0000-00-00 00:00:00'
    
            4. Media has timed-out:
                                MediaInfo = 'timeout'
                                ConnectInfo_stop is NULL
                                AcctStopTime != '0000-00-00 00:00:00'
    
            5. MediaProxy update before BYE is received:
                                MediaInfo = ''
                                ConnectInfo_stop is NULL
                                AcctStopTime != '0000-00-00 00:00:00'
    
            */

            $this->whereUnnormalized .= " and (ConnectInfo_stop is not NULL or MediaInfo is NULL or MediaInfo != '') ";
        }

    }

    function getUnNormalized($where="",$table) {

        if ($this->skipNormalize) {
            return 1;
        }

        if (!$where) $where=" (1=1) ";
        if (!$table) $table=$this->table;

        $ReNormalize = $_REQUEST["ReNormalize"];

        if ($ReNormalize) $this->unNormalize($where,$table);

        if (!$this->normalizedField) {
            return 1;
        }

		$this->buildWhereForUnnormalizedSessions();

        $query=sprintf("select count(*) as c from %s where %s and %s",
        $table,
        $where,
        $this->whereUnnormalized
        );


        if ($this->CDRdb->query($query)) {
            $this->CDRdb->next_record();
            $c=$this->CDRdb->f('c');
        }

        return $c;

    }

    function NormalizeCDRS($where="",$table="") {

        $b=time();

        if (!$where) $where=" (1=1) ";
        if (!$table) $table=$this->table;

        if ($this->skipNormalize) {
            return 0;
        }

        if (!$this->normalizedField) {
            return 0;
        }

        $lockName=sprintf("%s:%s",$this->cdr_source,$table);

        if (!$this->getNormalizeLock($lockName)) {
            return 1;
        }

		$this->buildWhereForUnnormalizedSessions();

        $query=sprintf("select *, UNIX_TIMESTAMP($this->startTimeField) as timestamp
        from %s where %s and %s",
        $table,
        $where,
        $this->whereUnnormalized
        );

        $this->status['cdr_to_normalize']=0;
        $this->status['normalized']=0;
        $this->status['normalize_failures']=0;

        $this->CDRdb->query($query);
        $this->status['cdr_to_normalize']=$this->CDRdb->num_rows();

        if ($this->status['cdr_to_normalize'] > 0) {

        	if ($this->ratingEnabled) {
                // Load rating tables
                $this->RatingTables = new RatingTables();
                $this->RatingTables->LoadRatingTables();
            }

        } else {
            return 0;
        }

		$this->usageKeysForDeletionFromCache=array();

        while ($this->CDRdb->next_record()) {

			$Structure=$this->_readCDRNormalizationFieldsFromDB();
            $found++;

            $CDR = new $this->CDR_class(&$this, &$Structure);

            if ($CDR->normalize("Save",$table)) {
                $this->status['normalized']++;
            } else {
                $this->status['normalize_failures']++;
            }

			if ($this->reNormalize && !$this->usageKeysForDeletionFromCache[$CDR->BillingPartyId]) {
            	$this->usageKeysForDeletionFromCache[$CDR->BillingPartyId]++;
            }

            if ($this->status['cdr_to_normalize'] > 1000) {
                if ($found > $progress*$this->status['cdr_to_normalize']/100) {
                    $progress++;
                    if ($progress%10==0) {
                        print "$progress% ";
                    }
                    flush();
                }
            }
        }

        if ($this->ratingEnabled && count($this->brokenRates) >0 ) {
            if ($this->rating_settings['reportMissingRates']) {
                if (count($this->brokenRates)) {
                    foreach (array_keys($this->brokenRates) as $dest) {
                        $missingRatesBodytext=$missingRatesBodytext."\nDestination id $dest (".$this->brokenRates[$dest]." calls)";
                    }
                    $to=$this->CDRTool['provider']['toEmail'];
                    $from=$this->CDRTool['provider']['fromEmail'];
     
                    mail($to, "Missing CDRTool rates",$missingRatesBodytext, "From: $from");
                }
            }
        }

        if ($this->status['cdr_to_normalize']>0) {
            $d=time()-$b;
            $log=sprintf("Normalization done in %d s, memory usage: %0.2f MB",$d,memory_get_usage()/1024/1024);
            syslog(LOG_NOTICE,$log );
    
        }

        if (count($this->usageKeysForDeletionFromCache)) {
			$this->resetQuota(array_keys($this->usageKeysForDeletionFromCache));
        }

        return 1;
    }

    function NormalizeNumber($Number,$type="destination",$subscriber="",$domain="",$gateway="",$CountryCode="",$ENUMtld="") {

        $this->CSCODE="";

        $Number=strtolower(quoted_printable_decode($Number));

        if ($pos = strpos($Number, "@")) {
            // this is a SIP URI
            $NumberStack['username']  = substr($Number,0,$pos);
            if (strlen($NumberStack['username']) < 1) {
            	$NumberStack['username'] = "unknown";
            }
            $NumberStack['domain']    = substr($Number,$pos+1);
            $NumberStack['delimiter'] = "@";

            $pos = strpos($NumberStack['username'], ":");
            if ($pos) {
            	$NumberStack['protocol'] = substr($NumberStack['username'],0,$pos+1);
            	$NumberStack['username'] = substr($NumberStack['username'],$pos+1);
            }

            if (preg_match("/^(.*)[=:;]/U",$NumberStack['domain'],$p)){
                $NumberStack['domain']    = $p[1];
	    	}

        } else if (preg_match("/^([a-z0-9]+:)(.*)$/i",$Number,$m)) {
            $oct=preg_split("/\./",$m[2]);
    	    if(sizeof($oct)==4) {
	        // This is SIP address with no username
                $NumberStack['username']  = "";
                $NumberStack['domain']    = $m[2];
            } else {
	        // This is SIP address with no domain
                $NumberStack['username']  = $m[2];
                $NumberStack['domain']    = "";
            }
            $NumberStack['protocol']  = $m[1];
            $NumberStack['delimiter'] = "";
        } else {
            // This is a simple address like a phone number
            $NumberStack['protocol']  = "";
            $NumberStack['username']  = $Number;
            $NumberStack['delimiter'] = "";
            $NumberStack['domain']    = "";
        }

        if (preg_match("/^(.*)[=:;]/U",$NumberStack['domain'],$p)){
            $NumberStack['domain']    = $p[1];
        }

        if ($type=="destination" && is_numeric($NumberStack['username'])) {
            // strip custom prefix from destination
            $usernameLength=strlen($NumberStack['username']);
            if (is_array($this->CS_CODES)) {
                foreach ($this->CS_CODES as $strip_prefix) {
                    $prefixLength = strlen($strip_prefix);
                    $restLength   = $usernameLength-$prefixLength;
                    if ($restLength > 0 and preg_match("/^$strip_prefix(.*)$/",$NumberStack['username'],$m)) {
                        $NumberStack['username']=$m[1];
                        $this->CSCODE=$strip_prefix;
                        break;
                    }
                }
            }

			if ($this->CDRTool['normalize']['E164Class'] && class_exists($this->CDRTool['normalize']['E164Class'])) {
				$E164_class = $this->CDRTool['normalize']['E164Class'];
            } else {
				$E164_class = "E164_Europe";
            }

			if (!$CountryCode) $CountryCode=$this->CDRTool['normalize']['defaultCountryCode'];

            $E164 =  new $E164_class($this->intAccessCode, $this->natAccessCode,$CountryCode,$this->ENUMtlds[$ENUMtld]['e164_regexp']);

            $NumberStack['E164']=$E164->E164Format($NumberStack['username']);
        }

        if ($type=="destination" && $NumberStack['E164']) {
            // lookup destination id for the E164 number
            $dst_struct                     = $this->lookupDestination($NumberStack[E164],$subscriber,$domain,$gateway);
            $NumberStack['DestinationId']   = $dst_struct[0];
            $NumberStack['destinationName'] = $dst_struct[1];

            $NumberStack['NumberPrint']     = "+".$NumberStack[E164];

            if (!$ENUMtld) {
            $NumberStack['Normalized']      = $this->intAccessCode.
                                              $NumberStack['E164'].
                                              $NumberStack['delimiter'].
                                              $NumberStack['domain'];
            } else {
            $NumberStack['Normalized']      =
                                              $NumberStack['username'].
                                              $NumberStack['delimiter'].
                                              $NumberStack['domain'];
            }
        } else {
            $dst_struct                     = $this->lookupDestination($Number,$subscriber,$domain,$gateway);
            $NumberStack['DestinationId']   = $dst_struct[0];
            $NumberStack['destinationName'] = $dst_struct[1];

            $NumberStack['NumberPrint']     = $NumberStack['username'].
                                              $NumberStack['delimiter'].
                                              $NumberStack['domain'];

            $NumberStack['Normalized']      = $NumberStack['username'].
                                              $NumberStack['delimiter'].
                                              $NumberStack['domain'];

        }

        return $NumberStack;

    }

    function lookupDestination($destination,$subscriber="",$domain="",$gateway="") {

        if (!$destination) {
            return;
        }

        if (is_numeric($destination)){
            return $this->getPSTNDestinationId($destination,$subscriber,$domain,$gateway);
        } else {
            return $this->getSIPDestinationId($destination,$subscriber,$domain,$gateway);
        }
    }

    function getSIPDestinationId($destination,$subscriber,$domain,$gateway) {
        if ($this->destinations_sip[$subscriber]) {
            $destinations_sip = $this->destinations_sip[$subscriber];
            $fCustomer="subscriber=$subscriber";
        } elseif ($this->destinations_sip[$domain]) {
            $destinations_sip = $this->destinations_sip[$domain];
            $fCustomer="domain=$domain";
        } elseif ($this->destinations_sip[$gateway]) {
            $destinations_sip = $this->destinations_sip[$gateway];
            $fCustomer="gateway=$gateway";
        } else {
            $destinations_sip = $this->destinations_sip['default'];
            $fCustomer="default";
        }


        if ($destinations_sip[$destination]) {
        	$ret=array($destination,$destinations_sip[$destination]);
            return $ret;
        } else {
            return false;
        }

    }

    function getPSTNDestinationId($destination,$subscriber,$domain,$gateway) {
        if ($this->destinations[$subscriber]) {
            $codes = $this->destinations[$subscriber];
            $maxLength = $this->destinations_length[$subscriber];
            $fCustomer="subscriber=$subscriber";
        } elseif ($this->destinations[$domain]) {
            $codes = $this->destinations[$domain];
            $maxLength = $this->destinations_length[$domain];
            $fCustomer="domain=$domain";
        } elseif ($this->destinations[$gateway]) {
            $codes = $this->destinations[$gateway];
            $maxLength = $this->destinations_length[$gateway];
            $fCustomer="gateway=$gateway";
        } else {
            $codes = $this->destinations['default'];
            $maxLength = $this->destinations_length['default'];
            $fCustomer="default";
        }


        if (!$destination)
            return false;
    
        if (count($codes)>0) {
            $length = min(strlen($destination), $maxLength);
            for ($i=$length; $i>0; $i--) {
                $buf = substr($destination, 0, $i);
                if ($codes[$buf]) {
                    $dest_name=$codes[$buf];
                    $ret=array($buf,$dest_name);
                    return $ret;
                }
            }
        }
        return false;
    }

    function import($file) {
    }

    function RadiusRecordRead($fp) {
    	$keepreading=1;

        while ($keepreading) {
            $contents = fgets($fp, 8192);
            if (preg_match("/^$/",$contents)) {
                $keepreading=0;
            } else {
            	$record[]=$contents;
            }
        }
        return $record;
    }

    function RadiusRecordParse($record) {
    	unset($radiusParsed);
        if (!is_array($record)) {
            return 0;
        }

        foreach ($record as $line) {
            $line=trim($line);
            foreach (array_keys($this->radiusAttributes) as $attribute) {
                if (preg_match("/$attribute = (.*)$/",$line,$m)) {
                    $value=preg_replace("/\"/","",trim($m[1]));
                	$radiusParsed[$attribute]= $value;
                }
            }
        }
        return $radiusParsed;
    }

    function getCDRtables() {
        if (!is_object($this->CDRdb)) return 0;
        $_tables=$this->CDRdb->table_names();
        $t=count($_tables);

		if ($this->table) $this->tables[]=$this->table;

        while ($t <> 0 ) {
            $_table=$_tables[$t]["table_name"];
            if ($_table=='radacct') $this->tables[]='radacct';

            if (preg_match("/^(\w+)\d{6}$/",$_table,$m)) {
                if ($list_t >24) break;
                $this->tables[]=$_table;
                $list_t++;
            }
            $t--;
        }

        $this->tables=array_unique($this->tables);
    }

    function rotateTable($sourceTable,$month,$action) {
        // create a new table tableYYYYMM and copy data from the main table into it
        // if no month is supplied, the default is the previous month

        if (!$month) $month=date('Ym', mktime(0, 0, 0, date("m")-1, "01", date("Y")));

		if (!$sourceTable) $sourceTable=$this->table;

        if (preg_match("/^(\w+)\d{6}$/",$sourceTable,$m)) {
			$destinationTable=$m[1].$month;
        } else {
			$destinationTable=$sourceTable.$month;
        }

        print("rotateTable($sourceTable,$month,$destinationTable)\n");

		if ($sourceTable == $destinationTable) {
        	$log=sprintf("Error: cannot copy records to the same table %s.\n",$destinationTable);
        	syslog(LOG_NOTICE,$log);
            print $log;
        	return 0;;
        }

		$createTableFile=$this->CDRTool['Path'].$this->createTableFile;

		if (!$this->createTableFile || !is_readable($createTableFile)) {
        	$log=sprintf("Error: cannot locate mysql creation file\n");
        	syslog(LOG_NOTICE,$log);
            print $log;
        	return 0;;
        }

		$lockFile="/var/lock/CDRTool_".$this->cdr_source."_rotateTable.lock";

		$f=fopen($lockFile,"w");
		if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    		if ($w) {
        		$log=sprintf("Another CDRTool rotate table is in progress. Aborting.\n");
        		syslog(LOG_NOTICE,$log);
                print $log;
        		return 0;;
    		}
		} else {
        	$log=sprintf("Another CDRTool rotate table is in progress. Aborting.\n");
        	syslog(LOG_NOTICE,$log);
            print $log;
        	return 0;;
		}

		$b=time();

        if (!preg_match("/^(\d{4})(\d{2})$/",$month,$m)) {
            print "Error: Month $month must be in YYYYMM format\n";
            return 0;
        } else {
            if ($m[2] > 12) {
                print "Error: Month must be in YYYYMM format\n";
                return 0;
            }

            $lastMonth=$month;
            $startSQL=$m[1]."-".$m[2]."-01";
            $stopSQL =date('Y-m-01', mktime(0, 0, 0, $m[2]+1, "01", $m[1]));
        }

        $query=sprintf("select count(*) as c from %s
                    where %s >='%s'
                    and %s < '%s'\n",
                    $sourceTable,
                    $this->CDRFields['startTime'],$startSQL,
                    $this->CDRFields['startTime'],$stopSQL);


		if ($this->CDRdb->query($query)) {
            $this->CDRdb->next_record();
            $rowsSourceTable=$this->CDRdb->f('c');
		    $log=sprintf ("Source table %s has %d records in month %s\n",$sourceTable,$rowsSourceTable,$month);
        	syslog(LOG_NOTICE,$log);
            print $log;
            if (!$rowsSourceTable) return 1;

		} else {
    	    $log=sprintf ("Error: %s (%s)\n",$this->table,$this->CDRdb->Error);
        	syslog(LOG_NOTICE,$log);
            print $log;
            return 0;
		}

        $query=sprintf("select count(*) as c from %s\n", $destinationTable);

		if ($this->CDRdb->query($query)) {
            $this->CDRdb->next_record();
            $rowsDestinationTable = $this->CDRdb->f('c');
		    $log=sprintf ("Destination table %s has %d records\n",$destinationTable,$rowsDestinationTable);
        	syslog(LOG_NOTICE,$log);
            print $log;
    
            if ($rowsDestinationTable != $rowsSourceTable) {
                $log=sprintf ("Error: source table has %d records and destination table has %d records\n",$rowsSourceTable,$rowsDestinationTable);
                syslog(LOG_NOTICE,$log);
                print $log;
            } else {
		    	$log=sprintf ("Tables are in sync\n");
        		syslog(LOG_NOTICE,$log);
            	print $log;
            }

		} else {
    	    $log=sprintf ("%s (%s)\n",$this->CDRdb->Error,$this->CDRdb->Errno);
        	syslog(LOG_NOTICE,$log);
            print $log;

            if ($this->CDRdb->Errno==1146) {

                $destinationTableTmp=$destinationTable."_tmp";
                $query=sprintf("drop table if exists %s",$destinationTableTmp);
                print($query);
                $this->CDRdb->query($query);
    
                if ($query=file_get_contents($createTableFile)) {
                    $query=preg_replace("/CREATE TABLE.*/","CREATE TABLE $destinationTableTmp (",$query);
                    if (!$this->CDRdb->query($query)) {
                        $log=sprintf ("Error creating table %s: %s, %s\n",$destinationTableTmp,$this->CDRdb->Error,$query);
                        syslog(LOG_NOTICE,$log);
                        print $log;
                        return 0;
                    }
                } else {
                    $log=sprintf ("Cannot read file %s\n",$createTableFile);
                    syslog(LOG_NOTICE,$log);
                    print $log;
                    return 0;
                }

                // if we reached this point we start to copy records
                $query=sprintf("insert into %s
                select * from %s
                where %s >='%s'
                and %s < '%s'",
                $destinationTableTmp,
                $sourceTable,
                $this->CDRFields['startTime'],$startSQL,
                $this->CDRFields['startTime'],$stopSQL);
        
                print ($query);
                return ;
        
                if ($this->CDRdb->query($query)) {
                    $e=time();
                    $d=$e-$b;
                    $rps=0;
                    if ($this->CDRdb->affected_rows() && $d) $rps=$this->CDRdb->affected_rows()/$d;
        
                    $log=printf ("Copied %d CDRs into table %s in %d s @ %.0f rps\n",$this->CDRdb->affected_rows(),$destinationTableTmp,$d,$rps);
                    syslog(LOG_NOTICE,$log);
                    print $log;
        
                    $query="rename table $destinationTableTmp to $destinationTableTmp";
        
                    if (!$this->CDRdb->query($query)) {
                        printf ("Error renaming table %s to %s: %s\n",$destinationTableTmp,$destinationTable,$this->CDRdb->Error);
                        return 0;
                    }
                } else {
                    printf ("Error copying records in table %s: %s\n",$destinationTable,$this->CDRdb->Error);
                    return 0;
                }
            }
        }
	}

    function purgeTable($sourceTable,$month) {
        // delete records for a given month with minimal locking of database
        // this function is useful after archive of CDR data using rotate script
        $begin=time();

        if ($month) {
        	if (!preg_match("/^(\d{4})(\d{2})$/",$month,$m)) {
            	print "Error: Month must be in YYYYMM format\n";
            	return 0;
            } else {
        		$beginDate=$m[1]."-".$m[2]."-01";
        		$endDate=date('Y-m-d', mktime(0, 0, 0, $m[2]+1, '01', $m[1]));
            }
        } else if (is_int($this->DATASOURCES[$this->cdr_source]['purgeCDRsAfter'])) {
        	$beginDate="1970-01-01";
        	$endDate=date('Y-m-d', mktime(0, 0, 0, Date('m'), Date('d')-$this->DATASOURCES[$this->cdr_source]['purgeCDRsAfter'], Date('Y')));
        } else {
            return 0;
        }

        if (!$sourceTable) $sourceTable=$this->table;

        $query=sprintf("select min(%s) as min,max(%s) as max from %s where %s >= '%s' and %s < '%s' ",
        $this->CDRFields['id'],$this->CDRFields['id'],$sourceTable,$this->CDRFields['startTime'],$beginDate,$this->CDRFields['startTime'],$endDate);

        print($query);

        if (!$this->CDRdb->query($query)) {
            printf ("Error: %s",$this->CDRdb->Error);
            return 0;
        }

        $this->CDRdb->next_record();
        $min=$this->CDRdb->f('min');
        $max=$this->CDRdb->f('max');

        if (!$min || !$max) {
            $log=sprintf("No CDRs found in %s between %s and %s\n",$sourceTable,$beginDate,$endDate);
            print $log;
        	syslog(LOG_NOTICE,$log);
            return 0;
        }

        $deleted=0;
        $i=$min;
        $interval=100;

        $rows2delete=$max-$min;
        $found = 0;

        print "$rows2delete CDRs will be deleted between $min and $max, $interval at a time\n";

        while ($i <= $max) {
            $found=$found+$interval;
            
            if ($i + $interval < $max) {
                $top=$i;
            } else {
                $top=$max;
            }

            $query=sprintf("delete low_priority from %s
                            where %s <= '%d' and %s >= '%d'",
                            $sourceTable,
                            $this->CDRFields['id'],
                            $top,
                            $this->CDRFields['id'],
                            $min);


            if ($this->CDRdb->query($query)) {
                $deleted=$deleted+$this->CDRdb->affected_rows();
            } else {
        		$log=sprintf("Error: %s (%s)",$this->CDRdb->Error,$this->CDRdb->Errno);
        		syslog(LOG_NOTICE,$log);
                print $log;
                return 0;
            }

            if ($found > $progress*$rows2delete/100) {
                $progress++;
                if ($progress%10==0) {
                    print "$progress% ";
                    flush();
                }
            }

            print ".";
            flush();

            $i=$i+$interval;

        }

        print "\n";

        $end	  = time();
        $duration = $end-$begin;
        $rps=0;

        if ($deleted && $duration) $rps=$deleted/$duration;

        $log=sprintf("%s CDRs of month %s deleted from %s in %d s @ %.0f rps\n",$deleted,$month,$sourceTable,$duration,$rps);
        syslog(LOG_NOTICE,$log);
        print $log;
        return 1;
    }

    function cacheMonthlyUsage($accounts=array()) {
        $saved_keys=0;
        $failed_keys=0;

        foreach (array_keys($accounts) as $_key) {

            $query=sprintf("select id from quota_usage where datasource = '%s' and account = '%s'",$this->cdr_source,$_key);

            if (!$this->cdrtool->query($query)){
                $log=sprintf ("Database error: %s (%s)",$this->cdrtool->Error,$this->cdrtool->Errno);
                syslog(LOG_NOTICE, $log);
                print($log);
            	return false;
            }

            if ($this->cdrtool->num_rows()) {
                // sync with quota_usage table
                $query=sprintf("update quota_usage set
                calls = calls + %d,
                duration  = duration + %d,
                cost = cost + '%s',
                traffic = traffic + '%s'
                where account = '%s'
                ",
                $accounts[$_key]['usage']['calls'],
                $accounts[$_key]['usage']['duration'],
                $accounts[$_key]['usage']['cost'],
                $accounts[$_key]['usage']['traffic'],
                $_key
                );

                if (!$this->cdrtool->query($query)){
                    $log=sprintf ("Database error: %s (%s)",$this->cdrtool->Error,$this->cdrtool->Errno);
                    syslog(LOG_NOTICE, $log);
                    $failed_keys++;
            	} else {
                	$saved_keys++;
                }

            } else {

				$quota=$this->getQuota($_key);
				$blocked=$this->getBlockedByQuotaStatus($_key);

                $query=sprintf("insert into quota_usage
                (datasource,account,domain,quota,calls,duration,cost,traffic,blocked)
                values
                ('%s','%s',SUBSTRING_INDEX('%s', '@',-1),%d,%d,'%s','%s','%s','%s')
                ",
                $this->cdr_source,
                $_key,
                $_key,
                $quota,
                $accounts[$_key]['usage']['calls'],
                $accounts[$_key]['usage']['duration'],
                $accounts[$_key]['usage']['cost'],
                $accounts[$_key]['usage']['traffic'],
                intval($blocked)
                );

                if (!$this->cdrtool->query($query)){
                    $log=sprintf ("Database error: %s (%s)",$this->cdrtool->Error,$this->cdrtool->Errno);
                    syslog(LOG_NOTICE, $log);
                    $failed_keys++;
            	} else {
                	$saved_keys++;
                }
            }
        }

        $this->status['cached_keys']['saved_keys']  = $this->status['cached_keys']['saved_keys']  + $saved_keys;
        $this->status['cached_keys']['failed_keys'] = $this->status['cached_keys']['failed_keys'] + $failed_keys;

        return 1;
    }

    function getNormalizeLock($lockname='') {

        if (!$locker = new DB_Locker()) {
            $log=sprintf("Error: cannot init locker database. ");
            syslog(LOG_NOTICE, $log);
            return 0;
        }
     
        if (!$lockname) {
            $log=sprintf("Error: no lockname provided. ");
            syslog(LOG_NOTICE, $log);
            return 0;
        }
     
     	unset($this->lock_connection_id);

        register_shutdown_function("unLockNormalization",$locker,$lockname);
     
        $query=sprintf("SELECT GET_LOCK('%s',0)",$lockname);

        if ($locker->query($query)) {
            $locker->next_record();
            $return = $locker->Record[0];

            $query=sprintf("SELECT IS_USED_LOCK('%s')",$lockname);
            if ($locker->query($query)) {
                $locker->next_record();
                $this->lock_connection_id=$locker->Record[0];
            }

            if ($return == 0) {
            	$log=sprintf(" Warning: data source %s normalize in progress, lock id %s ",$lockname,$this->lock_connection_id);
            	syslog(LOG_NOTICE, $log);
                print "$log\n";
                return 0;
            } else {
            	$log=sprintf(" Normalize lock id %s aquired for %s ",$this->lock_connection_id,$lockname);
            	syslog(LOG_NOTICE, $log);
                //print "$log\n";
                return 1;
            }

        } else {
            syslog(LOG_NOTICE, "Error: failed to request mysql lock");
            return 0;
        }
    }

    function getQuota($account) {
        if (!$account) return;

        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",$username,$domain);
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
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
            $query=sprintf("select quota from subscriber where username = '%s' and domain = '%s'",$username,$domain);
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
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
        if (!$account) return 0;

        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",$username,$domain);
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
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
            $query=sprintf("select CONCAT(username,'@',domain) as account from grp where grp = 'quota' and username = '%s' and domain = '%s'",$username,$domain);
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
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

    function resetQuota($accounts=array()) {

        $_reset_array=array_unique($accounts);

        foreach ($_reset_array as $_el) {
            if (strlen($_el)) $_accounts[]=$_el;
        }

        $_reset_array=$_accounts;

        $log=sprintf("Next quota check will rebuild the counters for %s accounts",count($_reset_array));
        syslog(LOG_NOTICE,$log );

        $query=sprintf("delete from memcache where `key` in ('%s','%s')",$this->quota_init_flag,$this->quota_reset_flag);
        if (!$this->cdrtool->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $query=sprintf("insert into memcache (`key`,`value`) values ('%s','%s')",$this->quota_reset_flag,json_encode($_reset_array));
        if (!$this->cdrtool->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $query="delete from quota_usage where account in (";
        $t=0;
        foreach ($_reset_array as $_el) {
            if ($t) $query.=",";
            $query.= sprintf("'%s'",$_el);
            $t++;
        }

        $query.=")";

        if (!$this->cdrtool->query($query)) {
            $log=sprintf ("Database error: %s (%s)",$this->cdrtool->Error,$this->cdrtool->Errno);
            syslog(LOG_NOTICE,$log);
            print $log;
            return 0;
        } else {
            return 1;
        }
    }
}

class CDRS_unknown extends CDRS {
    function searchForm() {
        return;
    }
}

class E164_Europe extends E164 {
    function E164_Europe ($intAccessCode='00', $natAccessCode='0',$CountryCode='',$ENUMtldRegexp="([1-9][0-9]{7,})") {
        $this->regexp_international = "/^".$intAccessCode."([1-9][0-9]{5,})\$/";
        $this->regexp_national      = "/^".$natAccessCode."([1-9][0-9]{3,})\$/";
        $this->CountryCode          = trim($CountryCode);
        $this->ENUMtldRegexp        = trim($ENUMtldRegexp);
    }
}

class E164_US extends E164 {
    function E164_US($intAccessCode='011', $natAccessCode='[1-9][0-9]{2}',$CountryCode='',$ENUMtldRegexp="([1-9][0-9]{7,})") {
        $this->regexp_international = "/^".$intAccessCode."([1-9][0-9]{5,})\$/";
        $this->regexp_national      = "/^".$natAccessCode."([0-9]{3,})\$/";
        $this->CountryCode          = trim($CountryCode);
        $this->ENUMtldRegexp        = trim($ENUMtldRegexp);
    }
}

class CDR {

    // we need two db descriptors to update a CDR
    // within same result set
    var $idField              = "RadAcctId";
    var $callIdField          = "AcctSessionId";
    var $usernameField        = "UserName";
    var $domainField          = "Realm";
    var $gatewayField         = "NASIPAddress";
    var $gatewayPortField     = "CiscoNASPort";
    var $timestampField       = "timestamp";
    var $portIdField          = "NASPortId";
    var $portTypeField        = "NASPortType";
    var $startTimeField       = "AcctStartTime";
    var $stopTimeField        = "AcctStopTime";
    var $durationField        = "AcctSessionTime";
    var $inputTrafficField    = "AcctInputOctets";
    var $outputTrafficField   = "AcctOutputOctets";
    var $serviceTypeField     = "ServiceType";
    var $cNumberField         = "CalledStationId";
    var $aNumberField         = "CallingStationId";
    var $disconnectField      = "H323DisconnectCause";
    var $traceIn              = "";
    var $traceOut             = "";
    var $defaultApplicationType = "audio";
  	var $supportedApplicationTypes = array('audio',
                                           'presence',
                                           'message',
                                           'video'
                                           );

    function CDR() {
    }

    function NormalizeDisconnect() {
		$causePrint=$this->CDRS->disconnectCodesDescription[$this->disconnect]." (".$this->disconnect.")";
        return $causePrint;
    }

    function traceOut () {
    }

    function traceIn () {
    }

    function show() {
    }

    function normalize($save="",$table) {

        if (!$table) $table = $this->CDRS->table;

        if ($this->CDRS->CSCODE && $CarrierInfo = $this->CDRS->CDRTool['normalize']['CS_CODES'][$this->CDRS->CSCODE]) {
            // We found a carrier so we set the BillingId
            $this->BillingId          = $CarrierInfo[BillingPartyId];
        }

        if ($save) {
            if (!$this->id) {
                return 0;
            }

            $query  ="";
            $query1 ="";
            $query2 ="";

            if ($this->CDRS->normalizedField) {
                $Field=$this->CDRS->normalizedField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query .= " $Field = '1' ";
                $updatedFields++;
            }

            if ($this->CDRS->DestinationIdField) {
                $Field=$this->CDRS->DestinationIdField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field  = '".$this->DestinationId."' ";
            }

			if ($this->usernameNormalized && $this->usernameNormalized!=$this->username) {
                $Field=$this->CDRS->usernameField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->usernameNormalized)."' ";
            }

            if ($this->aNumberNormalized && $this->aNumberNormalized!=$this->aNumber) {
                $Field=$this->CDRS->aNumberField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->aNumberNormalized)."' ";
                $this->aNumber=$this->aNumberNormalized;
            }

            if ($this->CDRS->applicationTypeField && $this->applicationTypeNormalized) {
                $Field=$this->CDRS->applicationTypeField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->applicationTypeNormalized)."' ";
                $this->applicationType=$this->applicationTypeNormalized;
            }

            if ($this->domainNormalized && $this->domainNormalized != $this->domain) {
                $Field=$this->CDRS->domainField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->domainNormalized)."' ";
                $this->domainNumber=$this->domainNormalized;
                $this->domain=$this->domainNormalized;
            }

            if ($this->cNumberNormalized && $this->cNumberNormalized!=$this->cNumber) {
                $Field=$this->CDRS->cNumberField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->cNumberNormalized)."' ";
                $this->cNumber=$this->cNumberNormalized;
            }

            if ($this->CDRS->BillingIdField && $this->BillingId) {
                $Field=$this->CDRS->BillingIdField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->BillingId)."' ";
            }

            if ($this->CDRS->RemoteAddressField && $this->RemoteAddressNormalized && $this->RemoteAddressNormalized!= $this->RemoteAddress) {
                $Field=$this->CDRS->RemoteAddressField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->RemoteAddressNormalized)."' ";
            }

            if ($this->CDRS->CanonicalURIField && $this->CanonicalURINormalized && $this->CanonicalURINormalized!= $this->CanonicalURI) {
                $Field=$this->CDRS->CanonicalURIField;
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=" $Field = '".addslashes($this->CanonicalURINormalized)."' ";
            }

            if ($this->CDRS->ratingEnabled && $this->duration) {

  		        $Rate    = new Rate($this->CDRS->rating_settings, $this->CDRS->cdrtool);

                $RateDictionary=array(
                                      'callId'          => $this->callId,
                                      'timestamp'       => $this->timestamp,
                                      'duration'        => $this->duration,
                                      'DestinationId'   => $this->DestinationId,
                                      'inputTraffic'    => $this->inputTraffic,
                                      'outputTraffic'   => $this->outputTraffic,
                                      'BillingPartyId'  => $this->BillingPartyId,
                                      'domain'          => $this->domain,
                                      'gateway'         => $this->gateway,
                                      'RatingTables'    => &$this->CDRS->RatingTables,
                                      'applicationType' => $this->applicationType,
                                      'aNumber'         => $this->aNumber,
                                      'cNumber'         => $this->cNumber,
                                      'ENUMtld'         => $this->ENUMtld
                                      );

                $Rate->calculate(&$RateDictionary);

                $this->pricePrint = $Rate->pricePrint;
                $this->price      = $Rate->price;
                $this->rateInfo   = $Rate->rateInfo;

                if ($this->CDRS->priceField) {

                    $Field=$this->CDRS->priceField;
                    if ($updatedFields) $query .= ", ";
                    $updatedFields++;
                    $query.=" $Field = '".$this->pricePrint."' ";
    
                    if ($this->CDRS->rateField ) {
                        $Field=$this->CDRS->rateField;
                        if ($updatedFields) $query .= ", ";
                        $updatedFields++;
                        $query.=" $Field = '".$this->rateInfo."' ";
                	}
                }
            }

            $query1 = sprintf("update %s set %s where %s = '%s'",$table,$query,$this->idField,$this->id);

            if ($updatedFields) {

                if ($this->CDRS->CDRdb1->query($query1)) {
                    if ($this->CDRS->CDRdb1->affected_rows()) {
        				if ($this->CallerIsLocal) {
                        	if ($table == $this->CDRS->table) {
                                // cache usage only if current month
        
                                $_traffic=($this->inputTraffic+$this->outputTraffic)/2;
                                $_usage=array('calls'    => 1,
                                              'duration' => $this->duration,
                                              'cost'     => $Rate->price,
                                              'traffic'  => $_traffic
                                             );
    
                                $this->cacheMonthlyUsage($_usage);
                            }
                        }

                    } else {
                        if (preg_match("/^(\w+)(\d{4})(\d{2})$/",$table,$m)) {
                            $previousTable=$m[1].date('Ym', mktime(0, 0, 0, $m[3]-1, "01", $m[2]));
                            $query2 = sprintf("update %s set %s where %s = '%s'",$previousTable,$query,$this->idField,$this->id);

                            if ($this->CDRS->CDRdb1->query($query2)) {
                    			if ($this->CDRS->CDRdb1->affected_rows()) {
        							if ($this->CallerIsLocal) {
                                        if ($previousTable == $this->CDRS->table) {
                                            // cache usage only if current month
                    
                                            $_traffic=($this->inputTraffic+$this->outputTraffic)/2;
                                            $_usage=array('calls'    => 1,
                                                          'duration' => $this->duration,
                                                          'cost'     => $Rate->price,
                                                          'traffic'  => $_traffic
                                                          );
                                            $this->cacheMonthlyUsage($_usage);
                                        }
                                    }
                                }
                            } else {
                                $log=sprintf ("Database error: %s (%s)",$this->CDRS->CDRdb1->Error,$this->CDRS->CDRdb1->Errno);
                                syslog(LOG_NOTICE, $log);
                                print($log);
                                return 0;
                            }

                        }

                    }

                    return 1;
                } else {
                	$log=sprintf ("Database error: %s (%s)",$this->CDRS->CDRdb1->Error,$this->CDRS->CDRdb1->Errno);
                    syslog(LOG_NOTICE, $log);
                    print($log);
                    return 0;
                }
            }
        } else {
            if ($this->CDRS->BillingPartyIdField && $CarrierInfo['BillingPartyId']) {
            	$this->domain = $CarrierInfo['BillingDomain'];
            }

			if ($this->usernameNormalized && $this->usernameNormalized!=$this->username) {
            	$this->username=$this->usernameNormalized;
            }

            if ($this->aNumberNormalized && $this->aNumberNormalized!=$this->aNumber) {
            	$this->aNumber=$this->aNumberNormalized;
            }

            if ($this->domainNormalized && $this->domainNormalized != $this->domain) {
                $this->domainNumber=$this->domainNormalized;
            }

            if ($this->cNumberNormalized && $this->cNumberNormalized!=$this->cNumber) {
                $this->cNumber=$this->cNumberNormalized;
            }

            if ($this->CDRS->RemoteAddressField && $this->RemoteAddressNormalized && $this->RemoteAddressNormalized!= $this->RemoteAddress) {
                $this->RemoteAddress=$this->RemoteAddressNormalized;
            }
        }
        return 1;
    }

    function cacheMonthlyUsage($usage) {

        if (!is_array($usage)) return ;

		$accounts[$this->BillingPartyId]['usage']=$usage;
        $this->CDRS->cacheMonthlyUsage($accounts);
    }


    function isCallerLocal() {
        return 0;
    }

    function isCalleeLocal() {
        return 0;
    }

    function obfuscateCallerId() {
        global $obfuscateCallerId;
        if ($obfuscateCallerId) {
    	}
    }

    function lookupRateFromNetwork(&$RateDictionary,&$fp) {
    	$this->rateInfo='';
        $this->pricePrint='';
        $this->price='';

    	$countEndofLines=0;
        $cmd="ShowPrice";
        foreach (array_keys(&$RateDictionary) as $key) {
            $cmd .=" ".$key."=".$RateDictionary[$key]." ";
        }

        $this->price	   = 0;
        $this->pricePrint  = "";
		$this->rateInfo    = "";

        if (fputs($fp,"$cmd\n") !== false) {
    
            $i=0;
            while ($i < 100) {
                $i++;

                $line = fgets($fp,1024);

                if (!$line) {
                    syslog(LOG_NOTICE, "Error: lookupRateFromNetwork(): connection to network socket died");
                    break;
                }

                if (preg_match("/^\n/",$line) || preg_match("/^END/",$line)) {
                    break;
                }

                if ($i == 1) {
                	$this->price = trim($line);
                	$this->pricePrint = number_format($this->price,4);
                    continue;
                }
    
                $this->rateInfo.=$line;
            }
        }
    }
}

function getLocalTime($timezone,$timestamp) {
	global $CDRTool;

    if (!$timezone || $timezone == $CDRTool['provider']['timezone']) {
    	return date("Y-m-d H:i:s", $timestamp);
    }

    putenv("TZ=$timezone");
    $startTimeLocal=date("Y-m-d H:i:s", $timestamp);
    $timezone=$CDRTool['provider']['timezone'];
    putenv("TZ=$timezone");
    return $startTimeLocal;
}

function sec2hms ($duration) {
    // return seconds in HH:MM:SS format
    $sum1=$duration;
    $duration_print="";

    $duration_hour=floor($sum1/3600);
    
    if ($duration_hour > 0) {
        $sum1=$sum1-($duration_hour*3600);
        $duration_print="$duration_hour:";
    }
    
    $duration_min=floor($sum1/60);
    
    if ($duration_min > 0) {
        $sum1=$sum1-($duration_min*60);
        if ($duration_min < 10) {
            $duration_min="0"."$duration_min";
        }
        $duration_print="$duration_print"."$duration_min:";
    } else {
        $duration_print="$duration_print"."00:";
    }

    if ($sum1< 10 ) {
        $duration_sec="0"."$sum1";
    } else {
        $duration_sec=$sum1;
    } 
    
    $duration_print="$duration_print"."$duration_sec";		
    return $duration_print;
}

function validDay($month,$day,$year) {
    if (!$month || !$year) {
        return $day;
    }
    while (1) {
    	if (!checkdate($month,$day,$year) && $day) {
            $day--;
            next;
        } else {
            break;
        }
    }
    return $day;
}

// include CDRTool modules defined in global.inc
foreach ($CDRToolModules as $module) {
    $module_filename="cdr_".$module.".php";
    include($module_filename);
}

function unLockNormalization ($dbid,$lockname) {
	$query=sprintf("SELECT RELEASE_LOCK('%s')",$lockname);
    $log=sprintf("Unlock %s",$lockname);
    syslog(LOG_NOTICE, $log);

    if (!$dbid->query($query)) {
    	$log="Error in unLockNormalization()";
    	syslog(LOG_NOTICE, $log);
    }
}

class SIPonline {

    function SIPonline ($datasource='',$database='db',$table='location') {
		global $CDRTool;

        $expandAll  = $_REQUEST['expandAll'];
        $domain     = $_REQUEST['domain'];

        $this->expandAll  = $expandAll;
        $this->domain     = $domain;
        $this->datasource = $datasource;

		if (strlen($CDRTool['filter']['domain'])) {
        	$this->allowedDomains=explode(" ",$CDRTool['filter']['domain']);
            $allowed_domains_sql="";
            $j=0;
            foreach ($this->allowedDomains as $_domain) {
                if ($j>0) $allowed_domains_sql.=",";
                $allowed_domains_sql.="'".addslashes($_domain)."'";
                $j++;
            }
        }

        $this->locationDB = new $database;
        $this->locationTable = $table;

		$this->Registered=array();
        $this->countUA=array();

        $where = " where (1=1) " ;

        if ($allowed_domains_sql) {
        	$where.= sprintf("and domain in (%s)",$allowed_domains_sql) ;
        }

        $query=sprintf("select count(*) as c, domain
        from %s %s
        group by domain
        order by domain ASC",$this->locationTable,$where);


        $this->locationDB->query($query);
        $this->domains=$this->locationDB->num_rows();
        while ($this->locationDB->next_record()) {
            $this->Registered[$this->locationDB->f('domain')]=$this->locationDB->f('c');
            $this->total=$this->total+$this->locationDB->f('c');
        }

        $query=sprintf("select count(*) as c, user_agent
        from %s %s",$this->locationTable,$where);
        if ($this->domain) {
            $query.=sprintf(" and domain = '%s' ",$this->domain);
        }

        $query.="
        group by user_agent
        order by c DESC";

        $this->locationDB->query($query);
        while ($this->locationDB->next_record()) {
            $this->countUA[$this->locationDB->f('user_agent')]=$this->locationDB->f('c');
        }

    }

    function showHeader() {
    	print "<table border=0 cellspacing=1 class=border>";
        print "<tr bgcolor=lightgrey>
        ";

        if ($this->domain) {
            print "
            <th></th>
            <th width=120 align=right>User@</th>
            <th align=left>Domain</th>
            <th></th>
            <th>SIP UA contact</th>
            <th>NAT address</th>
            <th>User Agent</th>
            <th>Expires</th>
            <th>Remain</th>
            ";
        } else {
            print "
            <th></td>
            <th width=120 align=right>Users@</th>
            <th align=left>Domain</th>
            ";
        }
        print "
        </tr>
        ";
    }

    function showFooter() {
        print "
        <tr bgcolor=lightgrey>
        <th></td>
        <th align=right>$this->total users@</td>
        <th align=left>$this->domains domains</td>
        </tr>
        </table>
        ";
    }

    function showAll() {
        global $found;

        $this->showHeader();
        foreach (array_keys($this->Registered) as $ld) {

        	$onlines=$this->Registered[$ld];
            $rr  = floor($found/2);
            $mod = $found-$rr*2;
        
            if ($mod ==0) {
                $bgcolor="lightgrey";
            } else {
                $bgcolor="white";
            }

        	if ($this->expandAll || ($this->domain && $this->domain==$ld)) {
           		$this->show($ld);
            } else {
            	$found++;

                $url = sprintf("%s?datasource=%s&domain=%s",
                $_SERVER['PHP_SELF'],
                urlencode($this->datasource),
                urlencode($ld)
                );

                print "
                <tr bgcolor=white>
                <td valign=top align=right>$found</td>
                <td valign=top align=right>$onlines users@</td>
                <td valign=top><a href=$url>$ld</a></td>
                ";

                if ($this->domain) {
                     print "
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     ";
                }
                print "
                </tr>
                ";
            }
        }

        $this->showfooter();
        /*
        print "<p>";
        $this->showUAdistribution();
        */

    }

    function show() {
        global $found;

        $query="SELECT *, SEC_TO_TIME(UNIX_TIMESTAMP(expires)-UNIX_TIMESTAMP(NOW())) AS remain
                FROM location
        ";
        if ($this->domain) $query.=" where domain = '$this->domain'";

        $query.= " ORDER BY domain ASC, username ASC ";

        $this->locationDB->query($query);

 		while ($this->locationDB->next_record()) {

            $rr  = floor($found/2);
            $mod = $found-$rr*2;
        
            if ($mod ==0) {
                $bgcolor="lightgrey";
            } else {
                $bgcolor="white";
            }
        
            $found++;

            $username   = $this->locationDB->f('username');
            $domain     = $this->locationDB->f('domain');
            $contact    = $this->locationDB->f('contact');
            $received   = $this->locationDB->f('received');
            $user_agent = $this->locationDB->f('user_agent');
            $expires    = $this->locationDB->f('expires');
            $remain     = $this->locationDB->f('remain');
        
            $contact_print=substr($contact,4);

            $c_els=explode(";", $contact);
            $r_els=explode(";", $received);

            $transport="UDP";

            if ($c_els[1] && preg_match("/transport=(tcp|tls)/i",$c_els[1],$m)) {
                $transport=strtoupper($m[1]);
            }

			$sip_account=$username."@".$domain;

            print "
            <tr bgcolor=$bgcolor>
            <td valign=top align=right class=border>$found</td>
            <td valign=top align=right class=border>$username@</td>
        	<td valign=top bgcolor=lightyellow class=border>$domain</td>
            <td valign=top class=border>$transport</td>
            <td valign=top align=right class=border>$c_els[0]</td>
            <td valign=top align=right class=border>$r_els[0]</td>
            <td valign=top class=border>$user_agent</td>
            <td valign=top class=border>$expires</td>
            <td valign=top align=right class=border>$remain</td>
            </tr>
            ";

            $seen[$username]++;
            $seen[$domain]++;
        
        }
    }

    function showUAdistribution () {
        print "<table border=0 cellspacing=1 class=border>";
        print "<tr bgcolor=lightgrey> ";
        print "<td></td>";
        print "<th>User agent</th>";
        print "<th>Users</th>";
        print "</tr> ";

        while (list($k,$v) = each($this->countUA)) {
            $users=$users+$v;
            $count++;
            print "<tr> ";
            print "<td>$count</td>";
            print "<td>$k</td>";
            print "<td>$v</td>";
            print "</tr>";
        }

        print "<tr bgcolor=lightgrey> ";
        print "<td></td>";
        print "<td><b>$this->domain</b></td>";
        print "<td><b>$users</b></td>";
        print "</tr> ";

        print "</table>";

    }
}

?>
