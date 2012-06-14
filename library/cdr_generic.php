<?
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
    var $defaults            = array();
    var $whereUnnormalized   = '';
    var $skipNormalize       = false;
    var $reNormalize         = false;
    var $usageKeysForDeletionFromCache = array();
    var $localDomains        = array();
    var $trustedPeers        = array();
    var $maxCDRsNormalizeWeb = 500;
    var $E164_class          = 'E164_Europe';
    var $quotaEnabled        = false;
    var $csv_writter         = false;

    var $CallerIsLocal       = false;
    var $CalleeIsLocal       = false;

    var $CDRNormalizationFields=array('id'               => 'RadAcctId',
                                      'callId'          => 'AcctSessionId',
                                      'username'        => 'UserName',
                                      'domain'            => 'Realm',
                                      'gateway'         => 'NASIPAddress',
                                      'duration'        => 'AcctSessionTime',
                                      'startTime'       => 'AcctStartTime',
                                      'stopTime'        => 'AcctStopTime',
                                      'inputTraffic'    => 'AcctInputOctets',
                                      'outputTraffic'   => 'AcctOutputOctets',
                                      'aNumber'          => 'CallingStationId',
                                      'cNumber'           => 'CalledStationId',
                                      'timestamp'        => 'timestamp',
                                      'BillingPartyId'  => 'UserName',
                                      'sipRPID'         => 'SipRPID',
                                      'ResellerId'      => 'BillingId',
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

        if (!$cdr_source) {
            $log="Error: cdr_source not defined\n";
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        if (!$DATASOURCES[$cdr_source] || !$DATASOURCES[$cdr_source]['db_class']) {
            $log="Error: no such datasource defined ($cdr_source) \n";
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $this->initDefaults();

        $this->cdrtool         = new DB_CDRTool();
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

        if ($this->DATASOURCES[$this->cdr_source]['UserQuotaClass']) {
            $this->quotaEnabled     = 1;
            $this->quota_init_flag  = $this->cdr_source.':quotaCheckInit';
            $this->quota_reset_flag = $this->cdr_source.':reset_quota_for';
            if ($this->DATASOURCES[$this->cdr_source]['daily_quota']) {
                $this->daily_quota=$this->DATASOURCES[$this->cdr_source]['daily_quota'];
            }
        }

        // connect to the CDR database(s)
        if(!$this->DATASOURCES[$this->cdr_source]['db_class']) {
            $log=sprintf("Error: \$DATASOURCES['%s']['db_class'] is not defined",$this->cdr_source);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $_dbClass            = $this->DATASOURCES[$this->cdr_source]['db_class'];

        if (is_array($_dbClass)) {
            if ($_dbClass[0]) $this->primary_database   = $_dbClass[0];
            if ($_dbClass[1]) $this->secondary_database   = $_dbClass[1];
        } else {
            $this->primary_database = $_dbClass;
        }

        if(!class_exists($this->primary_database)) {
            $log=sprintf("Error: database class '%s' is not defined",$this->primary_database);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $this->CDRdb    = new $this->primary_database;

        // check db connectivity
        if (!$this->CDRdb->query('SELECT 1')) {
            $log=sprintf("Error: failed to connect to the primary CDR database %s\n",$this->primary_database);
            syslog(LOG_NOTICE, $log);

            if ($this->secondary_database) {
                $this->CDRdb    = new $this->secondary_database;
                if (!$this->CDRdb->query('SELECT 1')) {
                    $log=sprintf("Error: failed to connect to the secondary CDR database %s\n",$this->secondary_database);
                    syslog(LOG_NOTICE, $log);
                    return 0;
                } else {
                    $this->CDRdb1         = new $this->secondary_database;
                    $this->db_class       = $this->secondary_database;
                }
            } else {
                return 0;
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

        if ($this->DATASOURCES[$this->cdr_source]['db_subscribers']) {
            if (class_exists($this->DATASOURCES[$this->cdr_source]['db_subscribers'])) {
                $this->AccountsDB       = new $this->DATASOURCES[$this->cdr_source]['db_subscribers'];
                $this->db_subscribers  = $this->DATASOURCES[$this->cdr_source]['db_subscribers'];
            } else {
                $log=sprintf("Error: subscribers database class %s is not defined",$this->DATASOURCES[$this->cdr_source]['db_subscribers']);
                syslog(LOG_NOTICE, $log);
                return 0;
            }
        } else if (class_exists('DB_opensips')) {
            $this->AccountsDB      = new DB_opensips();
            $this->db_subscribers  = 'DB_opensips';
        } else {
            $log=sprintf("Error: subscribers database is not defined, please define 'db_subscribers' in datasource '%s'",$this->cdr_source);
            syslog(LOG_NOTICE, $log);
            return 0;
        }
    
        if ($this->DATASOURCES[$this->cdr_source]['BillingIdField']) {
            $this->BillingIdField  = $this->DATASOURCES[$this->cdr_source]['BillingIdField'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['E164_class']) {
            if (class_exists($this->DATASOURCES[$this->cdr_source]['E164_class'])) {
                $this->E164_class=$this->DATASOURCES[$this->cdr_source]['E164_class'];
            } else {
                printf ("Error: E164 class '%s' defined in datasource %s does not exist, using default '%s'",$this->DATASOURCES[$this->cdr_source]['E164_class'],$this->cdr_source,$this->E164_class);
            }
        }

        if ($this->DATASOURCES[$this->cdr_source]['sipTrace']) {
            $this->sipTrace=$this->DATASOURCES[$this->cdr_source]['sipTrace'];
        }

        if ($this->DATASOURCES[$this->cdr_source]['mediaTrace']) {
            $this->mediaTrace=$this->DATASOURCES[$this->cdr_source]['mediaTrace'];
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
        $this->traceInURL         = $this->DATASOURCES[$this->cdr_source]['traceInURL'];
        $this->traceOutURL         = $this->DATASOURCES[$this->cdr_source]['traceOutURL'];
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

        $this->LoadDisconnectCodes();

        $this->LoadDestinations();

        $this->LoadENUMtlds();

        $this->LoadDomains();

        $this->LoadTrustedPeers();

        $this->getCDRtables();

        if ($this->DATASOURCES[$this->cdr_source]['csv_writer_class']) {
            $csv_writter_class=$this->DATASOURCES[$this->cdr_source]['csv_writer_class'];
            if (class_exists($csv_writter_class)) {
                $this->csv_writter = new $csv_writter_class($this->cdr_source,$this->DATASOURCES[$this->cdr_source]['csv_directory'],$this->db_subscribers);
            }

        }

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

    function LoadTrustedPeers() {
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
            $b=time();

            $this->cdrtool->next_record();
            $_destinations=json_decode($this->cdrtool->f('value'),true);

            foreach (array_keys($_destinations) as $_key1) {
                foreach(array_keys($_destinations[$_key1]) as $_key2) {
                    $this->destinations_count=$this->destinations_count+count($_destinations[$_key1][$_key2]);
                }
            }

            if (!$this->destinations_count) {
                $log=sprintf("Error: cached destinations key contains no data");
                syslog(LOG_NOTICE,$log);
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
               $_destinations_sip=json_decode($this->cdrtool->f('value'),true);

               foreach (array_keys($_destinations_sip) as $_key1) {
                   foreach(array_keys($_destinations_sip[$_key1]) as $_key2) {
                       $this->destinations_sip_count=$this->destinations_count+count($_destinations_sip[$_key1][$_key2]);
                   }
               }
            }

            /*
            $e=time();
            $log=sprintf("Read %d PSTN destinations from cache in %d seconds",$this->destinations_count,$e-$b);
            syslog(LOG_NOTICE,$log);

            if ($this->destinations_sip_count) {
                $e=time();
                $log=sprintf("Read %d SIP destinations from cache in %d seconds",$this->destinations_sip_count,$e-$b);
                syslog(LOG_NOTICE,$log);
            }
            */
            $this->destinations     = $_destinations;
            $this->destinations_sip = $_destinations_sip;
            unset($_destinations);
            unset($_destinations_sip);

        } else {

            $this->CacheDestinations();

            $this->destinations     = $this->_destinations;
            $this->destinations_sip = $this->_destinations_sip;

            unset($this->_destinations);
            unset($this->_destinations_sip);
        }

        if (is_array($this->destinations)) {
            foreach (array_keys($this->destinations) as $_reseller) {
                foreach ($this->destinations[$_reseller] as $key => $val) {
                    $this->destinations_length[$_reseller][$key] = max(array_map(strlen, array_keys($val)));
                }
            }
        }

        $c=$this->destinations_count + $this->destinations_sip_count;
        return $c;
    }

    function CacheDestinations() {

        $this->_destinations     = array();
        $this->_destinations_sip = array();

        $this->destinations_count     = 0;
        $this->destinations_sip_count = 0;

        $b=time();

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

        if (!$this->cdrtool->num_rows()) {
            $log=sprintf("Error: could not find any entries in the destinations table");
            syslog(LOG_NOTICE,$log);
            return 0;
        }

        $destinations_cache = "\n";
        $destinations_sip_cache = "\n";

        $this->destinations_count=0;
        $this->destinations_default_count=0;
        $this->destinations_gateway_count=0;
        $this->destinations_domain_count=0;
        $this->destinations_subscriber_count=0;

        $j=0;
        while($this->cdrtool->next_record()) {
            $j++;

            $reseller_id = $this->cdrtool->Record['reseller_id'];
            $gateway     = trim($this->cdrtool->Record['gateway']);
            $domain      = trim($this->cdrtool->Record['domain']);
            $subscriber  = trim($this->cdrtool->Record['subscriber']);
            $dest_id     = trim($this->cdrtool->Record['dest_id']);
            $region      = $this->cdrtool->Record['region'];
            $name        = $this->cdrtool->Record['dest_name'];

            $name_print  = $this->cdrtool->Record['dest_name']." (".$dest_id.")";

            if (strstr($dest_id,'@')) {
                // SIP destination

                if ($subscriber) {
                    $this->_destinations_sip[$reseller_id][$subscriber][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_sip_count++;
                } else if ($domain) {
                    $this->_destinations_sip[$reseller_id][$domain][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_sip_count++;
                } else if ($gateway) {
                    $this->_destinations_sip[$reseller_id][$gateway][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_sip_count++;
                } else if ($dest_id) {
                    $this->_destinations_sip[$reseller_id]["default"][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_sip_count++;
                }

            } else {
                // PSTN destination
                if (!is_numeric($dest_id)) {
                    $log=sprintf("Error: cannot load non-numeric destination '%s' from row id %d",$dest_id,$this->cdrtool->Record['id']);
                    syslog(LOG_NOTICE,$log);
                    continue;
                }

                if ($subscriber) {
                    $this->destinations_subscriber_count++;
                    $this->_destinations[$reseller_id][$subscriber][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_count++;

                } else if ($domain) {
                    $this->destinations_domain_count++;
                    $this->_destinations[$reseller_id][$domain][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_count++;

                } else if ($gateway) {
                    $this->destinations_gateway_count++;
                    $this->_destinations[$reseller_id][$gateway][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_count++;

                } else if ($dest_id) {
                    $this->destinations_default_count++;
                    $this->_destinations[$reseller_id]["default"][$dest_id]=array('name'=>$name, 'region'=>$region);
                    $this->destinations_count++;
                }
            }
        }

        $destinations_cache     =json_encode($this->_destinations);
        $destinations_sip_cache =json_encode($this->_destinations_sip);

        $log=sprintf ("PSTN destinations cache size: %0.2f MB",strlen($destinations_cache)/1024/1024);
        syslog(LOG_NOTICE, $log);

        if ($destinations_sip_cache) {
            $log=sprintf ("SIP destinations cache size: %0.2f MB",strlen($destinations_sip_cache)/1024/1024);
            syslog(LOG_NOTICE, $log);
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

            $log=sprintf("Cached %d total, %d default, %d gateway, %d domain, %d subscriber destinations",
            $this->destinations_count,
            $this->destinations_default_count,
            $this->destinations_gateway_count,
            $this->destinations_domain_count,
            $this->destinations_subscriber_count
            );
            syslog(LOG_NOTICE, $log);

        } else {
            $query=sprintf("insert into memcache (`key`,`value`) values ('destinations','%s')",addslashes($destinations_cache));
            if (!$this->cdrtool->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }

            $log=sprintf("Cached %d total, %d default, %d gateway, %d domain, %d subscriber destinations",
            $this->destinations_count,
            $this->destinations_default_count,
            $this->destinations_gateway_count,
            $this->destinations_domain_count,
            $this->destinations_subscriber_count
            );
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
            $log=sprintf("Cached %d SIP destinations",$this->destinations_sip_count);
            syslog(LOG_NOTICE, $log);

        } else {
            $query=sprintf("insert into memcache (`key`,`value`) values ('destinations_sip','%s')",$destinations_sip_cache);

            if (!$this->cdrtool->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->cdrtool->Error,$this->cdrtool->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
            $log=sprintf("Updated cache for %d SIP destinations",$this->destinations_sip_count);
            syslog(LOG_NOTICE, $log);
        }

        return true;
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

        $this->ENUMtlds    = $_ENUMtlds;
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

        if (!$this->export) {
            print "
            <form action=log.phtml method=post>
            <table border=0 align=center>
            <tr>
            <td><a href=\"$this->url_edit\" >Refine search</a>
            | <a href=\"$this->url_run\">Refresh</a>
            </td>
            ";

            $log_query=sprintf("insert into log
            (date,login,ip,url,results,rerun,reedit,datasource,reseller_id)
            values
            (NOW(),'%s','%s','%s','%s','%s','%s','%s',%d)",
            addslashes($loginname),
            $_SERVER["REMOTE_ADDR"],
            $this->url,
            $this->rows,
            $this->url_run,
            $this->url_edit,
            $this->cdr_source,
            $this->CDRTool['filter']['reseller']
            );

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

            $log_query=sprintf("insert into log
            (date,login,ip,url,results,rerun,reedit,datasource,reseller_id)
            values
            (NOW(),'%s','%s','%s','%s','%s','%s','%s',%d)",
            addslashes($loginname),
            $_SERVER["REMOTE_ADDR"],
            $this->url,
            $this->rows,
            $this->url_run,
            $this->url_edit,
            $this->cdr_source,
            0
            );

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
            <b>Start Time</b>
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
            <b>Stop Time</b>
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
        <b>Data Source</b>
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

        if ($this->stopTimeField) $this->whereUnnormalized .= " and $this->stopTimeField not like '0000-00-00 00:00:00%' ";

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

            6. Mofified 5. for the case where the session received a broken BYE
               that did not generate a STOP while MediaProxy generated an UPDATE

            */

            $this->whereUnnormalized .= " and (ConnectInfo_stop is not NULL or MediaInfo is NULL or MediaInfo != '' or (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(AcctStopTime) > 20)) ";
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

        $this->missing_destinations=array();

        $b=time();

        if (!$where) $where=" (1=1) ";
        if (!$table) $table=$this->table;

        if ($this->skipNormalize) {
            return 1;
        }

        if (!$this->normalizedField) {
            return 1;
        }

        $lockName=sprintf("%s:%s",$this->cdr_source,$table);

        if (!$this->getNormalizeLock($lockName)) {
            //printf("Cannot get obtain lock %s",$lockName);
            return true;
        }

        $this->buildWhereForUnnormalizedSessions();

        $query=sprintf("select *, UNIX_TIMESTAMP($this->startTimeField) as timestamp
        from %s where %s and %s order by %s asc",
        $table,
        $where,
        $this->whereUnnormalized,
        $this->CDRFields['id']
        );

        $this->status['cdr_to_normalize']=0;
        $this->status['normalized']=0;
        $this->status['normalize_failures']=0;


        if (!$this->CDRdb->query($query)) {
            $log=sprintf ("Database error: %s (%s)\n",$this->CDRdb->Error,$this->CDRdb->Errno);
            syslog(LOG_NOTICE,$log);
            print $log;
            return false;
        }

        $this->status['cdr_to_normalize']=$this->CDRdb->num_rows();

        //print "<p>$query";

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

            if ($this->csv_writter) {
                if (!$this->csv_file_cannot_be_opened) {
                    if (!$this->csv_writter->ready) {
                        if (!$this->csv_writter->open_file($Structure[$this->CDRNormalizationFields['id']])) {
                            $this->csv_file_cannot_be_opened = true;
                        } else {
                            $this->csv_file_ready = true;
                        }
                    }
                }
            }

            $found++;

            $CDR = new $this->CDR_class($this, $Structure);

            if ($CDR->normalize("Save",$table)) {
                $this->status['normalized']++;
                if ($this->csv_file_ready) {
                    if (!$this->csv_writter->write_cdr($CDR)) {
                        // stop writing future records if we have a failure
                        $this->csv_file_cannot_be_opened = true;
                    }
                }

                if ($CDR->broken_rate) {
                    $this->brokenRates[$CDR->DestinationId]++;
                }

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
                            flush();
                    }
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

                    $log=sprintf("Mailing missing rates for %d destination(s) to %s",count($this->brokenRates),$to);
                    syslog(LOG_NOTICE,$log);

                    mail($to, "Missing CDRTool rates",$missingRatesBodytext, "From: $from");
                }
            }
        }

        if (count($this->missing_destinations)) {
            $to=$this->CDRTool['provider']['toEmail'];
            $from=$this->CDRTool['provider']['fromEmail'];

            $body='';
            foreach($this->missing_destinations as $_dest) {
                if (!$seen[$_dest]) {
                    $body.=sprintf("No destination for number %s\n",$_dest);
                }
                $seen[$_dest]++;
            }
            mail($to, "Missing CDRTool destinations",$body, "From: $from");
        }

        if ($this->status['cdr_to_normalize']>0) {
            $d=time()-$b;
            $log=sprintf("Normalization done in %d s, memory usage: %0.2f MB",$d,memory_get_usage()/1024/1024);
            syslog(LOG_NOTICE,$log );
    
        }

        if ($this->csv_file_ready) {
            $this->csv_writter->close_file();
            $this->csv_writter->ready = false;
        }

        if (count($this->usageKeysForDeletionFromCache)) {
            $this->resetQuota(array_keys($this->usageKeysForDeletionFromCache));
        }

        return 1;
    }

    function NormalizeNumber($Number,$type="destination",$subscriber="",$domain="",$gateway="",$CountryCode="",$ENUMtld="",$reseller_id=0) {

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
            // This is a SIP address without username
                $NumberStack['username']  = "";
                $NumberStack['domain']    = $m[2];
            } else {
            // This is a SIP address without domain
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

        // Translate the domain 
        if (is_array($this->DATASOURCES[$this->cdr_source]['domainTranslationDestination']) && 
            in_array($NumberStack['domain'],array_keys($this->DATASOURCES[$this->cdr_source]['domainTranslationDestination']))) {
            $NumberStack['domain'] = $this->DATASOURCES[$this->cdr_source]['domainTranslationDestination'][$NumberStack['domain']];
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

            if (!$CountryCode) $CountryCode=$this->CDRTool['normalize']['defaultCountryCode'];

            $e164class=$this->E164_class;
            $E164 =  new $e164class($this->intAccessCode, $this->natAccessCode,$CountryCode,$this->ENUMtlds[$ENUMtld]['e164_regexp']);

            $NumberStack['E164']=$E164->E164Format($NumberStack['username']);
        }

        if ($type=="destination" && $NumberStack['E164']) {
            // lookup destination id for the E164 number
            $dst_struct                     = $this->lookupDestination($NumberStack['E164'],$subscriber,$domain,$gateway,$reseller_id);
            $NumberStack['DestinationId']   = $dst_struct[0];
            $NumberStack['destinationName'] = $dst_struct[1];

            $NumberStack['NumberPrint']     = "+".$NumberStack['E164'];

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
            $dst_struct                     = $this->lookupDestination($Number,$subscriber,$domain,$gateway,$reseller_id);
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

    function lookupDestination($destination,$subscriber="",$domain="",$gateway="",$reseller_id=0) {

        if (!$destination) return;

        if (is_numeric($destination)){
            return $this->lookupPSTNDestination($destination,$subscriber,$domain,$gateway,$reseller_id);
        } else {
            return $this->lookupSipDestination($destination,$subscriber,$domain,$gateway,$reseller_id);
        }
    }

    function lookupSipDestination($destination='',$subscriber='',$domain='',$gateway='',$reseller_id=0) {
        if ($this->destinations_sip[$reseller_id][$subscriber]) {
            $destinations_sip = $this->destinations_sip[$reseller_id][$subscriber];
            $fCustomer="subscriber=$subscriber";
        } else if ($this->destinations_sip[$reseller_id][$domain]) {
            $destinations_sip = $this->destinations_sip[$reseller_id][$domain];
            $fCustomer="domain=$domain";
        } else if ($this->destinations_sip[$reseller_id][$gateway]) {
            $destinations_sip = $this->destinations_sip[$reseller_id][$gateway];
            $fCustomer="gateway=$gateway";
        } else if ($this->destinations_sip[$reseller_id]['default']) {
            $destinations_sip = $this->destinations_sip[$reseller_id]['default'];
            $fCustomer="default";
        } else if ($this->destinations_sip[0][$subscriber]) {
            $destinations_sip = $this->destinations_sip[0][$subscriber];
            $fCustomer="subscriber=$subscriber";
        } else if ($this->destinations_sip[0][$domain]) {
            $destinations_sip = $this->destinations_sip[0][$domain];
            $fCustomer="domain=$domain";
        } else if ($this->destinations_sip[0][$gateway]) {
            $destinations_sip = $this->destinations_sip[0][$gateway];
            $fCustomer="gateway=$gateway";
        } else if ($this->destinations_sip[0]['default']) {
            $destinations_sip = $this->destinations_sip[0]['default'];
            $fCustomer="default";
        }

        if ($destinations_sip[$destination]) {
            $ret=array($destination,$destinations_sip[$destination]['name']);
            return $ret;
        } else {
            return false;
        }
    }

    function lookupPSTNDestination($destination='',$subscriber='',$domain='',$gateway='',$reseller_id=0) {

        if ($this->destinations[$reseller_id][$subscriber]) {
            $_destinations = $this->destinations[$reseller_id][$subscriber];
            $maxLength = $this->destinations_length[$reseller_id][$subscriber];
            $fCustomer="subscriber=$subscriber";
        } else if ($this->destinations[$reseller_id][$domain]) {
            $_destinations = $this->destinations[$reseller_id][$domain];
            $maxLength = $this->destinations_length[$reseller_id][$domain];
            $fCustomer="domain=$domain";
        } else if ($this->destinations[$reseller_id][$gateway]) {
            $_destinations = $this->destinations[$reseller_id][$gateway];
            $maxLength = $this->destinations_length[$reseller_id][$gateway];
            $fCustomer="gateway=$gateway";
        } else if ($this->destinations[$reseller_id]['default']) {
            $_destinations = $this->destinations[$reseller_id]['default'];
            $maxLength = $this->destinations_length[$reseller_id]['default'];
            $fCustomer="default";
        } else if ($this->destinations[0][$subscriber]) {
            $_destinations = $this->destinations[0][$subscriber];
            $maxLength = $this->destinations_length[0][$subscriber];
            $fCustomer="subscriber=$subscriber";
        } else if ($this->destinations[0][$domain]) {
            $_destinations = $this->destinations[0][$domain];
            $maxLength = $this->destinations_length[0][$domain];
            $fCustomer="domain=$domain";
        } else if ($this->destinations[0][$gateway]) {
            $_destinations = $this->destinations[0][$gateway];
            $maxLength = $this->destinations_length[0][$gateway];
            $fCustomer="gateway=$gateway";
        } else if ($this->destinations[0]['default']){
            $_destinations = $this->destinations[0]['default'];
            $maxLength = $this->destinations_length[0]['default'];
            $fCustomer="default";
        } else {
            $log=sprintf("Error: cannot find destinations for subscriber='%s', domain ='%s', gateway='%s', reseller='%s'\n",$subscriber,$domain,$gateway,$reseller_id);
            syslog(LOG_NOTICE,$log);
        }
    
        if (count($_destinations)>0) {
            $length = min(strlen($destination), $maxLength);
            for ($i=$length; $i>0; $i--) {
                $buf = substr($destination, 0, $i);
                if ($_destinations[$buf]) {
                    return array($buf,$_destinations[$buf]['name']);
                }
            }
        }

        $log=sprintf("Error: cannot find destination id for %s of customer = '%s', total destinations = %d\n",$destination,$fCustomer,count($_destinations));
        syslog(LOG_NOTICE,$log);

        $this->missing_destinations[]=$destination;
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

            if (preg_match("/^(\w+)(\d{6})$/",$_table,$m)) {
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

        dprint($query);

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

        $end      = time();
        $duration = $end-$begin;
        $rps=0;

        if ($deleted && $duration) $rps=$deleted/$duration;

        $log=sprintf("%s CDRs of month %s deleted from %s in %d s @ %.0f rps\n",$deleted,$month,$sourceTable,$duration,$rps);
        syslog(LOG_NOTICE,$log);
        print $log;
        return 1;
    }

    function cacheQuotaUsage($accounts=array()) {

        if (!$this->quotaEnabled) return true;

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
                duration = duration + %d,
                cost = cost + '%s',
                cost_today = cost_today + '%s',
                traffic = traffic + '%s'
                where account = '%s'
                ",
                $accounts[$_key]['usage']['calls'],
                $accounts[$_key]['usage']['duration'],
                $accounts[$_key]['usage']['cost'],
                $accounts[$_key]['usage']['cost_today'],
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

                list($_u,$_d)=explode("@",$_key);

                $query=sprintf("insert into quota_usage
                (datasource,account,domain,quota,calls,duration,cost,cost_today,traffic,blocked,reseller_id)
                values
                ('%s','%s','%s',%d,%d,'%s','%s','%s','%s','%s',%d)
                ",
                $this->cdr_source,
                $_key,
                $_d,
                $quota,
                $accounts[$_key]['usage']['calls'],
                $accounts[$_key]['usage']['duration'],
                $accounts[$_key]['usage']['cost'],
                $accounts[$_key]['usage']['cost_today'],
                $accounts[$_key]['usage']['traffic'],
                intval($blocked),
                $this->localDomains[$_d]['reseller']
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
            print $log;
            syslog(LOG_NOTICE, $log);
            return 0;
        }
     
        if (!$lockname) {
            $log=sprintf("Error: no lockname provided. ");
            print $log;
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
                $log=sprintf("Lock %s already aquired by another process with id %s ",$lockname,$this->lock_connection_id);
                syslog(LOG_NOTICE, $log);
                print "$log\n";
                return 0;
            } else {
                $log=sprintf("Normalize lock id %s aquired for %s ",$this->lock_connection_id,$lockname);
                syslog(LOG_NOTICE, $log);
                //print "$log\n";
                return 1;
            }

        } else {
            $log=sprintf("Database error: failed to request mysql lock %s (%s)\n",$locker->Error,$locker->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return 0;
        }
    }

    function getQuota($account) {
    }

    function getBlockedByQuotaStatus($account) {
    }

    function resetQuota($accounts=array()) {

        if (!$this->quotaEnabled) return true;

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

class E164 {
    // Class that helps normalization of a telephone number in E164 format

    // Based on this normalization, CDRTool rating engine decides whether
    // to consider the session a PSTN destination and rate it according
    // to the PSTN rating plan

    function E164($intAccessCode='00', $natAccessCode='0',$CountryCode='',$ENUMtldRegexp="") {
        $this->regexp_international = "/^".$intAccessCode."([0-9]{5,})\$/";
        $this->regexp_national      = "/^".$natAccessCode."([0-9]{3,})\$/";
        $this->CountryCode          = trim($CountryCode);
        $this->ENUMtldRegexp        = trim($ENUMtldRegexp);
    }

    function E164Format($Number) {
        //dprint "E164Format($Number,ENUMtldRegexp=$this->ENUMtldRegexp)";
        // This function returns the full E164 format for a PSTN number without leading zero or +
        // E164 = Country Code + Network Code + Subscriber Number
        // Example: 31208015100 is an E164 number from Holland (country code 31)

        // If nothing is returned by this function the session is considered an Internet destination 

        if (preg_match($this->regexp_international,$Number,$m)) {
            return $m[1];
        } else if (preg_match($this->regexp_national,$Number,$m)) {
            // Add default country code
            return $this->CountryCode.$m[1];
        } else if (strlen($this->ENUMtldRegexp)) {
            $_regexp="/^".$this->ENUMtldRegexp."\$/";
            if (preg_match($_regexp,$Number,$m)) {
                return $m[1];
            }
        } 
        return false;
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
    var $defaultApplicationType    = "audio";
      var $supportedApplicationTypes = array('audio',
                                           'message',
                                           'video',
                                           'chat',
                                           'file-transfer'
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

    function normalize($save="",$table="") {

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
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s='1' ",$this->CDRS->normalizedField);
            }

            if ($this->CDRS->BillingPartyIdField && $this->BillingPartyId) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->BillingPartyIdField,addslashes($this->BillingPartyId));
            }

            if (strlen($this->durationNormalized) && $this->durationNormalized != $this->duration) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s ='%s' ",$this->CDRS->durationField,$this->durationNormalized);
                $this->duration=$this->durationNormalized;
            }

            if ($this->CDRS->DestinationIdField) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->DestinationIdField,$this->DestinationId);
            }

            if ($this->CDRS->ResellerIdField) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->ResellerIdField,$this->ResellerId);
            }

            if ($this->usernameNormalized && $this->usernameNormalized!=$this->username) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->usernameField,addslashes($this->usernameNormalized));
            }

            if ($this->aNumberNormalized && $this->aNumberNormalized!=$this->aNumber) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->aNumberField,addslashes($this->aNumberNormalized));
                $this->aNumber=$this->aNumberNormalized;
            }

            if ($this->CDRS->applicationField && $this->applicationNormalized) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->applicationField,addslashes($this->applicationNormalized));
                $this->application=$this->applicationNormalized;
            }

            if ($this->CDRS->flowField && $this->flow) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->flowField,addslashes($this->flow));
            }

            if ($this->domainNormalized && $this->domainNormalized != $this->domain) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->domainField,addslashes($this->domainNormalized));
                $this->domainNumber=$this->domainNormalized;
                $this->domain=$this->domainNormalized;
            }

            if ($this->cNumberNormalized && $this->cNumberNormalized!=$this->cNumber) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->cNumberField,addslashes($this->cNumberNormalized));
                $this->cNumber=$this->cNumberNormalized;
            }

            if ($this->CDRS->BillingIdField && $this->BillingId) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->BillingIdField,addslashes($this->BillingId));
            }

            if ($this->CDRS->RemoteAddressField && $this->RemoteAddressNormalized && $this->RemoteAddressNormalized!= $this->RemoteAddress) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->RemoteAddressField,addslashes($this->RemoteAddressNormalized));
            }

            if ($this->CDRS->CanonicalURIField && $this->CanonicalURINormalized && $this->CanonicalURINormalized!= $this->CanonicalURI) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->CanonicalURIField,addslashes($this->CanonicalURINormalized));
            }

            if ($this->stopTimeNormalized) {
                if ($updatedFields) $query .= ", ";
                $updatedFields++;
                $query.=sprintf(" %s = '%s' ",$this->CDRS->stopTimeField,addslashes($this->stopTimeNormalized));
            }

            if ($this->CDRS->ratingEnabled && ($this->duration || $this->application == 'message')) {

                if ($this->DestinationId) {
                    $Rate    = new Rate($this->CDRS->rating_settings, $this->CDRS->cdrtool);
    
                    if ($this->application == 'message') {
                        $RateDictionary=array(
                                              'callId'          => $this->callId,
                                              'timestamp'       => $this->timestamp,
                                              'duration'        => $this->duration,
                                              'DestinationId'   => $this->DestinationId,
                                              'BillingPartyId'  => $this->BillingPartyId,
                                              'ResellerId'      => $this->ResellerId,
                                              'domain'          => $this->domain,
                                              'gateway'         => $this->gateway,
                                              'RatingTables'    => $this->CDRS->RatingTables,
                                              'aNumber'         => $this->aNumber,
                                              'cNumber'         => $this->cNumber
                                              );
        
    
                        $Rate->calculateMessage($RateDictionary);
                    } else {
                        $RateDictionary=array(
                                              'callId'          => $this->callId,
                                              'timestamp'       => $this->timestamp,
                                              'duration'        => $this->duration,
                                              'DestinationId'   => $this->DestinationId,
                                              'inputTraffic'    => $this->inputTraffic,
                                              'outputTraffic'   => $this->outputTraffic,
                                              'BillingPartyId'  => $this->BillingPartyId,
                                              'ResellerId'      => $this->ResellerId,
                                              'domain'          => $this->domain,
                                              'gateway'         => $this->gateway,
                                              'RatingTables'    => $this->CDRS->RatingTables,
                                              'aNumber'         => $this->aNumber,
                                              'cNumber'         => $this->cNumber,
                                              'ENUMtld'         => $this->ENUMtld
                                              );
        
    
                        $Rate->calculateAudio($RateDictionary);
                    }
    
                    $this->pricePrint   = $Rate->pricePrint;
                    $this->price        = $Rate->price;
                    $this->rateInfo     = $Rate->rateInfo;
                    $this->rateDuration = $Rate->duration;
    
                    if ($Rate->broken_rate) {
                        $this->broken_rate=true;
                    }
                } else {
                    $this->rateInfo='';
                    $this->pricePrint='';
                    $this->price='';
                }

                if ($this->CDRS->priceField) {
                    if ($updatedFields) $query .= ", ";
                    $updatedFields++;
                    $query.=sprintf(" %s = '%s' ",$this->CDRS->priceField,$this->pricePrint);
    
                    if ($this->CDRS->rateField ) {
                        if ($updatedFields) $query .= ", ";
                        $updatedFields++;
                        $query.=sprintf(" %s = '%s' ",$this->CDRS->rateField,addslashes($this->rateInfo));
                    }
                }
            }

            $query1 = sprintf("update %s set %s where %s = '%s'",$table,$query,$this->idField,$this->id);
            dprint($query1);

            if ($updatedFields) {

                if ($this->CDRS->CDRdb1->query($query1)) {
                    if ($this->CDRS->CDRdb1->affected_rows()) {
                        if ($this->isBillingPartyLocal()) {
                            if ($table == $this->CDRS->table) {
                                // cache usage only if current month
        
                                $_traffic=($this->inputTraffic+$this->outputTraffic)/2;
                                $_usage=array('calls'    => 1,
                                              'duration' => $this->duration,
                                              'cost'     => $this->price,
                                              'cost_today' => $this->price,
                                              'traffic'  => $_traffic
                                             );
    
                                $this->cacheQuotaUsage($_usage);
                            }
                        }

                    } else {

                        if (preg_match("/^(\w+)(\d{4})(\d{2})$/",$table,$m)) {
                            $previousTable=$m[1].date('Ym', mktime(0, 0, 0, $m[3]-1, "01", $m[2]));
                            $query2 = sprintf("update %s set %s where %s = '%s'",$previousTable,$query,$this->idField,$this->id);

                            if ($this->CDRS->CDRdb1->query($query2)) {
                                if ($this->CDRS->CDRdb1->affected_rows()) {
                                    if ($this->isBillingPartyLocal()) {
                                        if ($previousTable == $this->CDRS->table) {
                                            // cache usage only if current month
                    
                                            $_traffic=($this->inputTraffic+$this->outputTraffic)/2;
                                            $_usage=array('calls'    => 1,
                                                          'duration' => $this->duration,
                                                          'cost'     => $this->price,
                                                          'cost_today' => $this->price,
                                                          'traffic'  => $_traffic
                                                          );
                                            $this->cacheQuotaUsage($_usage);
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
                    $log=sprintf ("Database error for query %s: %s (%s)",$query1,$this->CDRS->CDRdb1->Error,$this->CDRS->CDRdb1->Errno);
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

    function cacheQuotaUsage($usage) {

        if (!is_array($usage)) return ;

        $accounts[$this->BillingPartyId]['usage']=$usage;
        $this->CDRS->cacheQuotaUsage($accounts);
    }

    function isCallerLocal() {
        return false;
    }

    function isCalleeLocal() {
        return false;
    }

    function isBillingPartyLocal() {
        return false;
    }

    function obfuscateCallerId() {
        global $obfuscateCallerId;
        if ($obfuscateCallerId) {
        }
    }

    function lookupRateFromNetwork($RateDictionary,$fp) {
        $this->rateInfo='';
        $this->pricePrint='';
        $this->price='';

        $countEndofLines=0;
        $cmd="ShowPrice";
        foreach (array_keys($RateDictionary) as $key) {
            $cmd .=" ".$key."=".$RateDictionary[$key]." ";
        }

        $this->price       = 0;
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

    function lookupGeoLocation ($ip) {
        if ($_loc=geoip_record_by_name($ip)) {
            return $_loc['country_name'].'/'.$_loc['city'];
        } else if ($_loc=geoip_country_name_by_name($ip)) {
            return $_loc;
        } else {
            return '';
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
if (is_array($CDRToolModules)) {
    foreach ($CDRToolModules as $module) {
        $module_filename="cdr_".$module.".php";
        include($module_filename);
    }
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

class PrepaidHistory {
    function PrepaidHistory() {
        $this->db = new DB_cdrtool;
    }
        
    function purge($days=7) {
        $beforeDate=Date("Y-m-d", time()-$days*3600*24);
        $query=sprintf("delete from prepaid_history where date < '%s' and action like 'Debit balance%s'",$beforeDate,'%');
        
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)\n",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE,$log);
        } else {
            $log=sprintf ("Purged %d records from prepaid history before %s\n",$this->db->affected_rows(),$beforeDate);
            print $log;
            syslog(LOG_NOTICE,$log);
        }
    }
}

class CSVWritter {
    var $csv_directory      = '/var/spool/cdrtool/normalize';
    var $filename_extension = '.csv';
    var $fields             = array();
    var $ready              = false;
    var $cdr_type           = array();
    var $lines              = 0;

    function CSVWritter($cdr_source='',$csv_directory='') {
        if ($cdr_source) {
            $this->cdr_source = $cdr_source;
        } else {
            $this->cdr_source = 'unknown';
        }

        if ($csv_directory) {
            if (is_dir($csv_directory)) {
                $this->csv_directory = $csv_directory;
            } else {
                $log=sprintf ("CSV writter error: %s is not a directory\n",$csv_directory);
                syslog(LOG_NOTICE,$log);
                return false;
            }
        }

        $this->directory=$this->csv_directory."/".date("Ymd");

        if (!is_dir($this->directory)) {
            if (!mkdir($this->directory)) {
                $log=sprintf ("CSV writter error: cannot create directory %s\n",$this->directory);
                syslog(LOG_NOTICE,$log);
                return false;
            }
            chmod($this->directory, 0775);
        }

        $this->directory_ready = true;
    }

    function open_file ($filename_suffix='') {
        if ($this->ready) return true;

        if (!$this->directory_ready) return false;

        if (!$filename_suffix) {
            $log=sprintf ("CSV writter error: no filename suffix provided\n");
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $this->filename_prefix = strtolower($this->cdr_source).'-'.date('YmdHi');

        $this->full_path=rtrim($this->directory,'/').'/'.$this->filename_prefix.'-'.$filename_suffix.$this->filename_extension;

        $this->full_path_tmp=$this->full_path.'.tmp';

        if (!$this->fp = fopen($this->full_path_tmp, 'w')) {
            $log=sprintf ("CSV writter error: cannot open %s for writing\n",$this->full_path_tmp);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $this->ready = true;
        return true;
    }

    function close_file () {
        if (!$this->ready) return false;

        fclose($this->fp);

        if (!rename($this->full_path_tmp, $this->full_path)) {
            $log=sprintf ("CSV writter error: cannot rename %s to %s\n",$this->full_path_tmp,$this->full_path);
            syslog(LOG_NOTICE,$log);
        } else {
            $log=sprintf ("%d normalized CDRs written to %s\n",$this->lines, $this->full_path);
            syslog(LOG_NOTICE,$log);
        }
    }

    function write_cdr ($CDR) {
        if (!$this->ready) return false;

        $line = sprintf("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                        $CDR->id,
                        $CDR->callId,
                        $CDR->flow,
                        $CDR->application,
                        $CDR->username,
                        $CDR->CanonicalURI,
                        $CDR->startTime,
                        $CDR->stopTime,
                        $CDR->duration,
                        $CDR->DestinationId,
                        $CDR->BillingPartyId,
                        $CDR->ResellerId,
                        $CDR->price
                        );

        if (!fputs($this->fp,$line)) {
            $this->ready = false;
            return false;
        }

        $this->lines++;

        return true;
    }
}

class MaxRate extends CSVWritter {
    var $product         = '';
    var $cdr_types       = '';
    var $inbound_trunks  = array();
    var $outbound_trunks = array();
    var $skip_prefixes   = array();
    var $skip_numbers    = array();
    var $skip_domains    = array();
    var $rpid_cache      = array();
    var $translate_uris  = array();
 
    function MaxRate ($cdr_source='', $csv_directory='', $db_subscribers='') {
        global $MaxRateSettings;   // set in global.inc

        /*
        $MaxRateSettings= array('inbound_trunks'  => array('10.0.0.1' => 'KPNtrunk1',
                                                           '10.0.0.1' => 'KPNtrunk1'
                                                           ),

                                'outbound_trunks' => array('ss7a-caiw.net'=>'KPNout1',
                                                           'ss7b-caiw.net'=>'KPNout1'
                                                           ),

                                'cdr_types'       => array('on-net'                  => array('feature_set' => '(2)'),
                                                           'outgoing'                => array('feature_set' => '(1)'),
                                                           'incoming'                => array('feature_set' => '(1)'),
                                                           'diverted-off-net'        => array('feature_set' => '(1)'),
                                                           'on-net-diverted-on-net'  => array('feature_set' => '(2)'),
                                                           'on-net-diverted-off-net' => array('feature_set' => '(1)')
                                                           ),

                                'product'       => 7,
                                'translate_uris'=> array( '1233@10.0.0.2'=>'+1233',
                                                          '[1-9][0-9]{4}.*@10.0.0.2'=>'+1233'), 
                                'skip_domains'  => array('example.net','10.0.0.1'),
                                'skip_numbers'  => array('1233'), //  skip CDRs that has the username part in this array
                                'skip_prefixes' => array('0031901') // skip CDRs that begin with any of this prefixes
                               );
        */


        if (is_array($MaxRateSettings['inbound_trunks'])) {
            $this->inbound_trunks=$MaxRateSettings['inbound_trunks'];
        }

        if (is_array($MaxRateSettings['outbound_trunks'])) {
            $this->outbound_trunks=$MaxRateSettings['outbound_trunks'];
        }

        if (is_array($MaxRateSettings['cdr_types'])) {
            $this->cdr_types=$MaxRateSettings['cdr_types'];
        }

        if (is_array($MaxRateSettings['skip_domains'])) {
            $this->skip_domains=$MaxRateSettings['skip_domains'];
        }

        if (is_array($MaxRateSettings['skip_numbers'])) {
            $this->skip_numbers=$MaxRateSettings['skip_numbers'];
        }

        if (is_array($MaxRateSettings['skip_prefixes'])) {
            $this->skip_prefixes=$MaxRateSettings['skip_prefixes'];
        }

        if (strlen($MaxRateSettings['product'])) {
            $this->product=$MaxRateSettings['product'];
        }

        if (is_array($MaxRateSettings['translate_uris'])) {
            $this->translate_uris=$MaxRateSettings['translate_uris'];
        }

        $this->AccountsDB = new $db_subscribers();

        $this->CSVWritter($cdr_source, $csv_directory);

    }

    function write_cdr($CDR) {
        if (!$this->ready) return false;

        # skip if no audio
        if ($CDR->application != 'audio') return true;

        # skip if no duration
        if (!$CDR->duration && ($CDR->disconnect != 200)) return true;

        # normalize destination
        if ($CDR->CanonicalURIE164) {
            $cdr['destination'] = '+'.$CDR->CanonicalURIE164;
        } else {
            $cdr['destination'] = $CDR->CanonicalURI;
        }

        list($canonical_username, $canonical_domain)=explode("@",$cdr['destination']);

        # skip domains
        if ($canonical_domain && in_array($canonical_domain,$this->skip_domains)) return true;

        # skip numbers
        if ($canonical_username && in_array($canonical_username,$this->skip_numbers)) return true;

        # skip prefixes
        if ($canonical_username && count($this->skip_prefixes)) {
            foreach ($this->skip_prefixes as $prefix) {
                if (preg_match("/^$prefix/",$canonical_username)) return true;
            }
        }

        # get RPID if caller is local
        if ($CDR->flow != 'incoming') {
            $CallerRPID=$this->getRPIDforAccount($CDR->aNumberPrint);
        }

        if ($CallerRPID) {
            # normalize RPID
            $cdr['origin']      = '+31'.ltrim($CallerRPID,'0');
        } else {
            # normalize caller id numbers from PSTN gateway to +E.164
            if (preg_match("/^\+?0([1-9][0-9]+)@(.*)$/",$CDR->aNumberPrint,$m)) {
                $cdr['origin'] = "+31".$m[1];
            } else if (preg_match("/^\+?00([1-9][0-9]+)@(.*)$/",$CDR->aNumberPrint,$m)) {
                $cdr['origin'] = "+".$m[1];
            } else if (preg_match("/^([1-9][0-9]+)@(.*)$/",$CDR->aNumberPrint,$m)) {
                $cdr['origin'] = "+31".$m[1];
            } else if (preg_match("/^(\+[1-9][0-9]+)@(.*)$/",$CDR->aNumberPrint,$m)) {
                $cdr['origin'] = $m[1];
            } else if (preg_match("/^anonymous@(.*)$/",$CDR->aNumberPrint) && $CDR->SipRPID) {
                if (preg_match("/^\+?0([1-9][0-9]+)$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+31".$m[1];
                } else if (preg_match("/^\+?00([1-9][0-9]+)$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+".$m[1];
                } else if (preg_match("/^([1-9][0-9]+)@(.*)$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+31".$m[1];
                } else if (preg_match("/^(\+[1-9][0-9]+)@(.*)$/",$CDR->SipRPID,$m)) {
                   $cdr['origin'] = $m[1];
                } else if (preg_match("/^\+?0[0-9]?+@?(.*)?$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+31123456789";
                } else if (preg_match("/^.*[a-zA-Z].*$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+31123456789";
                } else if (preg_match("/^ims.imscore.net.*$/",$CDR->SipRPID,$m)) {
                    $cdr['origin'] = "+31123456789";
                } else {
                    $cdr['origin'] = $CDR->SipRPID;
                }
            } else {
                $cdr['origin'] = "+31123456789";
                //$cdr['origin'] = $CDR->aNumberPrint;
            }
        }

        # normalize short origins
        if (preg_match("/^\d{1,3}@.*$/",$cdr['origin'])) {
            $cdr['origin']='+31000000000';
        }

        # normalize anonymous origins
        if (preg_match("/^anonymous@.*$/",$cdr['origin'])) {
            $cdr['origin']='+31000000000';
        }

        #translate destination URIs to desired format
        if ($CDR->CanonicalURINormalized && count($this->translate_uris)) {
            foreach ($this->translate_uris as $key => $uri) {
                if ( preg_match("/^$key/", $CDR->CanonicalURINormalized)) {
                     $cdr['destination']=$uri;
                     break;
                }
            }
        }

        preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})$/",$CDR->startTime,$m);

        $cdr['start_date']  = sprintf ("%s/%s/%s %s",$m[3],$m[2],$m[1],$m[4]);

        $cdr['feature_set'] = $this->cdr_types[$CDR->flow]['feature_set'];

        $cdr['product']     = $this->product;

        # normalize duration based on billed duration
        if ($CDR->rateDuration) {
            $cdr['duration']    = $CDR->rateDuration;
        } else {
            $cdr['duration']    = $CDR->duration;
        }

        $cdr['extra']="$CDR->callId";

        if ($CDR->flow == 'on-net') {
            # RFP 4.2.1

            $cdr['charge_info'] = sprintf("(%s,1)",$cdr['origin']);

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['destination'] = '+31'.ltrim($CalleeRPID,'0');
            }
            
            $cdr['extra'] = $cdr['extra']." $CDR->flow";
        } else if ($CDR->flow == 'outgoing') {
            # RFP 4.2.2

            if ($this->outbound_trunks[$CDR->remoteGateway]) {
                $outbound_trunk = $this->outbound_trunks[$CDR->remoteGateway];
            } else {
                $outbound_trunk = 'unknown';
            }

            $cdr['charge_info'] = sprintf("(%s,1),(%s,2)",
                                          $cdr['origin'],
                                          $outbound_trunk
                                          );

            $cdr['extra'] = $cdr['extra']." $CDR->flow";

        } else if ($CDR->flow == 'incoming') {
            # RFP 4.2.3

            if ($this->inbound_trunks[$CDR->SourceIP]) {
                $inbound_trunk = $this->inbound_trunks[$CDR->SourceIP];
            } else {
                $inbound_trunk = 'unknown';
            }

            $cdr['charge_info']=sprintf("(%s,1)",$inbound_trunk);

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['destination'] = '+31'.ltrim($CalleeRPID,'0');
            }

            $cdr['extra'] = $cdr['extra']." $CDR->flow";

        } else if ($CDR->flow == 'diverted-on-net') {
            # RFP 4.2.4

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '+31'.ltrim($DiverterRPID,'0');
            } else {
                $diverter_origin = $CDR->username;
            }

            if ($CalleeRPID) {
                $cdr['c_num'] = '+31'.ltrim($CalleeRPID,'0');
            }

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;

            if ($this->inbound_trunks[$CDR->SourceIP]) {
                $inbound_trunk = $this->inbound_trunks[$CDR->SourceIP];
            } else {
                $inbound_trunk = 'unknown';
            }

            $cdr['charge_info'] = sprintf(  "(%s,1),(%s,2,%s,%s,%s,%s)",
                                            $inbound_trunk,
                                            $cdr['destination'],
                                            $cdr['destination'],
                                            $cdr['c_num'],
                                            $cdr['feature_set'],
                                            $cdr['product']
                                         );

            $cdr['extra'] = $cdr['extra']." incoming-diverted-on-net";

        } else if ($CDR->flow == 'diverted-off-net') {
            # RFP 4.2.5

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '+31'.ltrim($DiverterRPID,'0');
            } else {
                $diverter_origin = $CDR->username;
            }

            $cdr['c_num'] = $cdr['destination'];

            # Set destination to B-Number
            $cdr['destination']=$diverter_origin;

            if ($this->inbound_trunks[$CDR->SourceIP]) {
                $inbound_trunk = $this->inbound_trunks[$CDR->SourceIP];
            } else {
                $inbound_trunk = 'unknown';
            }

            if ($this->outbound_trunks[$CDR->remoteGateway]) {
                $outbound_trunk = $this->outbound_trunks[$CDR->remoteGateway];
            } else {
                $outbound_trunk = 'unknown';
            }

            $cdr['charge_info'] = sprintf(  "(%s,1),(%s,2,%s,%s,%s,%s),(%s,3)",
                                            $inbound_trunk,
                                            $diverter_origin,
                                            $diverter_origin,
                                            $cdr['c_num'],
                                            $cdr['feature_set'],
                                            $cdr['product'],
                                            $outbound_trunk
                                        );

            $cdr['extra'] = $cdr['extra']." incoming-diverted-off-net";

        } else if ($CDR->flow == 'on-net-diverted-on-net') {
            # RFP 4.2.6

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '+31'.ltrim($DiverterRPID,'0');
            } else {
                $diverter_origin = $CDR->username;
            }

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['c_num'] = '+31'.ltrim($CalleeRPID,'0');
            }
            
            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;

            $cdr['charge_info'] = sprintf(  "(%s,1),(%s,2,%s,%s,%s,%s)",
                                            $cdr['origin'],
                                            $diverter_origin,
                                            $diverter_origin,
                                            $cdr['c_num'],
                                            $cdr['feature_set'],
                                            $cdr['product']
                                            );

            $cdr['extra'] = $cdr['extra']." $CDR->flow";

        } else if ($CDR->flow == 'on-net-diverted-off-net') {
            # RFP 4.2.7

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '+31'.ltrim($DiverterRPID,'0');
            } else {
                $diverter_origin = $CDR->username;
            }
            
            $cdr['c_num']= $cdr['destination'];

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;

            if ($this->outbound_trunks[$CDR->remoteGateway]) {
                $outbound_trunk = $this->outbound_trunks[$CDR->remoteGateway];
            } else {
                $outbound_trunk = 'unknown';
            }

            $cdr['charge_info'] = sprintf("(%s,1),(%s,2,%s,%s,%s,%s),(%s,3)",
                                          $cdr['origin'],
                                          $diverter_origin,
                                          $diverter_origin,
                                          $cdr['c_num'],
                                          $cdr['feature_set'],
                                          $cdr['product'],
                                          $outbound_trunk
                                      );

            $cdr['extra'] = $cdr['extra']." $CDR->flow";
        }

        $line = sprintf("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                        $cdr['origin'],
                        '',
                        $cdr['destination'],
                        $cdr['start_date'],  
                        $cdr['feature_set'],
                        $cdr['product'],
                        $cdr['duration'],
                        '',
                        'Voice',
                        $cdr['charge_info'],
                        $cdr['extra']
                       );

        if (!fputs($this->fp,$line)) {
            $log=sprintf ("CSV writter error: cannot append to file %s\n",$this->full_path_tmp);
            syslog(LOG_NOTICE,$log);

            $this->close_file();
            $this->ready = false;
            return false;
        }

        $this->lines++;

        return true;
    }

    function getRPIDforAccount($account) {
        if (!$account) return false;

        if ($this->rpid_cache[$account]) {
            return $this->rpid_cache[$account];
        }

        list($username,$domain) = explode('@',$account);

        $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",addslashes($username),addslashes($domain));

        if (!$this->AccountsDB->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if ($this->AccountsDB->num_rows()) {
            $this->AccountsDB->next_record();
            $_profile=json_decode(trim($this->AccountsDB->f('profile')));
            $this->rpid_cache[$account]=$_profile->rpid;
            return $_profile->rpid;

        } else {
            return false;
        }
    }
}

?>
