<?
class NGNProCDRToolServer {

	var $max_login_attempts = 25;
	var $retry_auth_after   = 60;
	var $CanonicalURIField  = "CanonicalURI";
	var $UserNameField      = "UserName";
    var $radiusTable        = "radacct";
    var $startTimeField     = "AcctStartTime";
    var $stopTimeField      = "AcctStopTime";
    var $durationField      = "AcctSessionTime";
    var $DestinationIdField = "DestinationId";

    function NGNProCDRToolServer() {

        global $DATASOURCES;
        global $SOAPServer;
        global $RatingEngine;

        $this->RatingEngine  = $RatingEngine;
        $this->SOAPServer    = $SOAPServer;
        $this->DATASOURCES   = $DATASOURCES;

        $cdr_source    = $this->SOAPServer['cdr_source'];

        $this->db      = new DB_CDRTool;
		$this->db->Halt_On_Error ="no";

        $this->radius  = new $this->DATASOURCES[$cdr_source]['db_class'];

        if ($this->DATASOURCES[$cdr_source]['table']) {
        	$this->radiusTable   = $this->DATASOURCES[$cdr_source]['table'];
        }

        if ($this->SOAPServer['UserNameField']) {
        	$this->UserNameField   = $this->SOAPServer['UserNameField'];
        }

        if ($this->SOAPServer['CanonicalURIField']) {
        	$this->UserNameField   = $this->SOAPServer['CanonicalURIField'];
        }

        if ($this->SOAPServer['DB_ser']) {
        	$this->ser= new $this->SOAPServer['DB_ser'];
        } else {
        	$this->ser     = new DB_ser;
        }
    }

    function checkSpam() {
    
        $REMOTE_ADDR=$_SERVER[REMOTE_ADDR];
    
        $query="select * from spam where ip = '$REMOTE_ADDR'";
        $this->db->query($query);
    
        if ($this->db->num_rows()) {
            $this->db->next_record();
        
            $spam_login_ip    = $this->db->f('ip');
            $spam_login_tries = $this->db->f('tries');
            $spam_login_stamp = $this->db->f('stamp');
            $next_try	      = $spam_login_stamp+$this->retry_auth_after;
            $this->remains    = $next_try-time();
            $this->next_try	  = Date("Y-m-d H:i:s",$next_try);
            $now	          = Date("Y-m-d H:i:s",time());
    
        }
    
        if ($this->remains < 0) {
            $query="delete from spam where ip = '$spam_login_ip'";
            $this->db->query($query);
            return 0;
        }
               
        if ($spam_login_tries < $this->max_login_attempts) {
            return 1;
        } else {
            return 0;
        }
    }

    function checkAuth($auth) {
		$auth->username=addslashes($auth->username);
        $auth->password=addslashes($auth->password);

        $query="select * from auth_user
        where username = '$auth->username'
        and password   = '$auth->password'
        and perms   like '%soapclient%'
        and expire > NOW()";

        if (!$this->db->query($query) || !$this->db->num_rows()) {
			syslog(LOG_NOTICE,"SOAP login failed as username=$auth->username password=$auth->password");

            // save wrong attempt for spam detection
            $query="select * from spam where ip = '$_SERVER[REMOTE_ADDR]'";
    		$this->db->query($query);

            if (!$this->db->num_rows()) {
            	$query=sprintf("insert into spam (ip,tries,login,stamp)
                values ('%s','1','%s','%s')
                ",$_SERVER[REMOTE_ADDR],$auth->username,time());
            } else {
            	$query=sprintf("update spam set
                tries = tries +1 where ip = '%s'", $_SERVER[REMOTE_ADDR]);
            }
            $this->db->query($query);

            return 0;
        }

        $this->db->next_record();

        $this->CDRTool['filter']['domain']  = $this->db->f('domainFilter');

        if ($this->CDRTool['filter']['domain']) {
            $this->Realms      = explode(" ",$this->CDRTool['filter']['domain']);
        }

        return 1;
    }

    function checkRatingEngineConnection () {
        if ($this->RatingEngine['socketIP'] &&
        	$this->RatingEngine['socketPort'] &&
            $this->fp = fsockopen ($this->RatingEngine['socketIP'], $this->RatingEngine['socketPort'], $errno, $errstr, 5) ){
            return 1;
        } else {
            return 0;
        }
    }

    function checkCDRFilter($filter) {
        if (!is_object($filter)) {
            $this->CDRFilterError="filter must be an object";
            return 0;
        }

        if ($filter->calls && !is_int($filter->calls)) {
            $this->CDRFilterError= "filter[calls] must be an integer";
            return 0;
        }

        $filterDates=array("beginDate","endDate");

        foreach ($filterDates as $_date) {
            if ($filter->$_date) {
                if (preg_match("/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/",$filter->beginDate,$m)) {
                    // check date
                    if (!checkdate($m[2],$m[3],$m[1])) {
                        $this->CDRFilterError="filter[$_date] contains an invalid date $m[1]-$m[2]-$m[3]";
                        return 0;
                    }
                    if ($m[4] > 23 || $m[4] < 0) {
                        $this->CDRFilterError="filter[$_date] contains invalid hours $m[4]";
                        return 0;
                    }
                    if ($m[5] > 59 || $m[5] < 0) {
                        $this->CDRFilterError="filter[$_date] contains invalid minutes $m[5]";
                        return 0;
                    }
                    if ($m[6] > 59 || $m[6] < 0) {
                        $this->CDRFilterError="filter[$_date] contains invalid seconds $m[5]";
                        return 0;
                    }
    
                } else if (preg_match("/^(\d{4})\-(\d{2})\-(\d{2})$/",$filter->beginDate,$m)) {
                    // check date
                    if (!checkdate($m[2],$m[3],$m[1])) {
                        $this->CDRFilterError="filter[$_date] contains an invalid date $m[1]-$m[2]-$m[3]";
                        return 0;
                    }
                } else {
                    $this->CDRFilterError="filter[$_date] format must be YYYY-MM-DD HH:MM:SS or YYYY-MM-DD";
                    return 0;
                }
            }
        }

        if (strlen($filter->duration)) {
            if (!preg_match("/^[<|>|=]\d{1,10}$/",$filter->duration)) {
            	$this->CDRFilterError="filter[duration] must be an integer prefixed with one equal, less than or more than sign =<>";
                return 0;
            }
        }

        return 1;
    }

    function Version() {
		syslog(LOG_NOTICE,"Version");
        $version = trim(file_get_contents("../version"));
        return $version;
    }

    function AddPrepaidAccount($auth, $account) {
		syslog(LOG_NOTICE,"AddPrepaidAccount($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }

            syslog(LOG_NOTICE,$fault->message);

            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 254) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->Realms) {
            unset($foundDomain);
            foreach ($this->Realms as $realm) {
                if (preg_match("/^.*@$realm$/",$account)) {
                    $foundDomain=1;
                	break;
                }
            }
            if (!$foundDomain) {
            	$fault = new SOAP_Fault();
	            $fault->message = "Error: Invalid domain for prepaid account";
            	syslog(LOG_NOTICE,$fault->message);
    	        $fault->code    = "902";
				unset($fault->backtrace);
        	    return $fault;
            }
        }

        $query=sprintf("insert into prepaid (account, change_date)
        values ('%s', NOW())",addslashes($account));

        if (!$this->db->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->db->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

		$this->reloadPrepaidAccounts();

 		return 1;

    }

    function DeletePrepaidAccount($auth, $account) {
		syslog(LOG_NOTICE,"DeletePrepaidAccount($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 254) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

		$account=addslashes($account);

        if ($this->Realms) {
            unset($foundDomain);
            foreach ($this->Realms as $realm) {
                if (preg_match("/^.*@$realm$/",$account)) {
                    $foundDomain=1;
                	break;
                }
            }
            if (!$foundDomain) {
            	$fault = new SOAP_Fault();
	            $fault->message = "Error: Invalid domain for prepaid account";
           	 	syslog(LOG_NOTICE,$fault->message);
    	        $fault->code    = "902";
				unset($fault->backtrace);
        	    return $fault;
            }
        }

        $query=sprintf("delete from prepaid
        where account = '%s'",addslashes($account));

        if (!$this->db->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->db->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

		$this->reloadPrepaidAccounts();

        return 1;
    }

    function GetPrepaidAccountInfo($auth, $account) {
		syslog(LOG_NOTICE,"GetPrepaidAccountInfo($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 254) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account or balance";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkRatingEngineConnection()) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Rating Engine Error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "4000";
			unset($fault->backtrace);
            return $fault;
        }

		$account=addslashes($account);

        if ($this->Realms) {
            unset($foundDomain);
            foreach ($this->Realms as $realm) {
                if (preg_match("/^.*@$realm$/",$account)) {
                    $foundDomain=1;
                	break;
                }
            }
            if (!$foundDomain) {
            	$fault = new SOAP_Fault();
	            $fault->message = "Error: Invalid domain for prepaid account";
            	syslog(LOG_NOTICE,$fault->message);
    	        $fault->code    = "902";
				unset($fault->backtrace);
        	    return $fault;
            }
        }

        $query="select *,UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(call_in_progress) as sessionTime
        from prepaid where account = '$account'";

        if (!$this->db->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->db->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->db->num_rows()){
            $this->db->next_record();

            if ($this->db->f('call_in_progress') != "0000-00-00 00:00:00") {
                $call_in_progress=1;
            } else {
                $call_in_progress=0;
            }

			$call_in_progress=intval($call_in_progress);

    		if ($call_in_progress) {
            	$sessionTime=intval($this->db->f('sessionTime'));
            } else {
            	$sessionTime=intval("0");
            }

            $structure=array(
            "balance"        => floatval(number_format($this->db->f('balance'),4,".","")),
            "lastCallPrice"  => floatval(number_format($this->db->f('last_call_price'),4,".","")),
            "callInProgress" => $call_in_progress,
            "sessionTime"    => $sessionTime,
            "maxSessionTime" => intval($this->db->f('maxsessiontime')),
            "destination"    => $this->db->f('destination')
            );
            return $structure;

        } else {
            $error_msg=$this->db->Error;
        	$fault = new SOAP_Fault();
	        $fault->message = "Error: non-existent prepaid account";
            syslog(LOG_NOTICE,$fault->message);
    	    $fault->code    = "201";
			unset($fault->backtrace);
        	return $fault;
        }
    }

    function AddBalance($auth, $account, $balance) {
		syslog(LOG_NOTICE,"AddBalance(account=$account,balance=$balance)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkRatingEngineConnection()) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Rating engine error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "4000";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 254) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        $query=sprintf("select * from prepaid
        where account = '%s'",addslashes($account));

        dprint($query);

        if (!$this->db->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->db->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        if (!$this->db->num_rows()) {
        	$fault = new SOAP_Fault();
	        $fault->message = "Error: Prepaid account $account does not exist";
            syslog(LOG_NOTICE,$fault->message);
    	    $fault->code    = "201";
			unset($fault->backtrace);
        	return $fault;
        }

		$balance=trim($balance);

        if (!is_numeric($balance)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Invalid balance";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->Realms) {
            unset($foundDomain);
            foreach ($this->Realms as $realm) {
                if (preg_match("/^.*@$realm$/",$account)) {
                    $foundDomain=1;
                	break;
                }
            }

            if (!$foundDomain) {
            	$fault = new SOAP_Fault();
	            $fault->message = "Error: Invalid domain for prepaid account";
            	syslog(LOG_NOTICE,$fault->message);
    	        $fault->code    = "902";
				unset($fault->backtrace);
        	    return $fault;
            }
        }

        fputs($this->fp,"AddBalance From=$account To=$account Value=$balance\n");
        $line=trim(fgets($this->fp,64));

        if ($line) {
			$this->reloadPrepaidAccounts();
            return 1;
        } else {
            $error_msg=$this->db->Error;
        	$fault = new SOAP_Fault();
	        $fault->message = "Error: Failed to add balance";
            syslog(LOG_NOTICE,$fault->message);
    	    $fault->code    = "201";
			unset($fault->backtrace);
        	return $fault;
        }
    }


    function GetCallStatistics ($auth, $account, $afterDate="1970-01-01") {
		syslog(LOG_NOTICE,"GetCallStatistics($account,$afterDate)");

        $account=trim($account);

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 64 || strlen($account) == 0) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }


        // summary
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->UserNameField like '%@".addslashes($realm)."'";
                $rr++;
            }
            $where = $where." ) ";
        }

        $query="select sum(AcctSessionTime) as duration,
        min(AcctStartTime) as firstCall,
        sum(AcctInputOctets+AcctOutputOctets)/1024/1024 as traffic,
        max(AcctStartTime) as lastCall,
        sum(Price) as price,
        count(*) as calls from $this->radiusTable
        where $this->UserNameField = '$account'
        and AcctStartTime > '$afterDate'
        and SipMethod = 'Invite'
        $where ";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->radius->num_rows()) {
        	$this->radius->next_record();

            $structure=array(
            "calls"         => $this->radius->f('calls'),
            "duration" 		=> $this->radius->f('duration'),
            "firstCall"     => $this->radius->f('firstCall'),
            "lastCall"      => $this->radius->f('lastCall'),
            "traffic"       => $this->radius->f('traffic'),
            "price"         => $this->radius->f('price')
            );
        } else {
            $structure=array(
            "calls"         => "0",
            "duration"      => "0",
            "firstCall"     => "0000-00-00 00:00:00",
            "lastCall"      => "0000-00-00 00:00:00",
            "traffic"       => "0.00",
            "price"         => "0.00"
            );
        }

        return $structure;

    }

    function GetCallHistory ($auth, $account, $filters) {
		syslog(LOG_NOTICE,"GetCallHistory($account)");

        $account=trim($account);

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

		if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 64 || strlen($account) == 0) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        if (is_object($filters)) {
            $filter_names=array("receivedCallsFilter","placedCallsFilter");
            foreach ($filter_names as $_filter) {
                $filter=$filters->$_filter;
                if (is_object($filter) && !$this->checkCDRFilter($filter)) {
                    $fault = new SOAP_Fault();
                    $fault->message = "Error: ".$this->CDRFilterError;
                    syslog(LOG_NOTICE,$fault->message);
                    $fault->code    = "101";
                    unset($fault->backtrace);
                    return $fault;
                }
            }
        }

        $j=0;

        $_els=explode("@",$account);
        if (is_array($this->Realms) && !in_array($_els[1],$this->Realms)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Invalid domain $_els[1] for account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "902";
            unset($fault->backtrace);
            return $fault;
        }

        if ($j) $CalledStationIds=$CalledStationIds.",";
        $CalledStationIds = $CalledStationIds."'".addslashes($_account)."'";
        $j++;

        // summary
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->UserNameField like '%@".addslashes($realm)."'";
                $rr++;
            }
            $where = $where." ) ";
        }

        $query="select sum(AcctSessionTime) as duration,
        min(AcctStartTime) as firstCall,
        sum(AcctInputOctets+AcctOutputOctets)/1024/1024 as traffic,
        max(AcctStartTime) as lastCall,
        sum(Price) as price,
        count(*) as calls from $this->radiusTable
        where $this->UserNameField = '$account'
        and SipMethod = 'Invite'
        $where ";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError(summary) $error_msg";
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->radius->num_rows()) {
        	$this->radius->next_record();

            $summary=array(
            "calls"         => $this->radius->f('calls'),
            "duration" 		=> $this->radius->f('duration'),
            "firstCall"     => $this->radius->f('firstCall'),
            "lastCall"      => $this->radius->f('lastCall'),
            "traffic"       => $this->radius->f('traffic'),
            "price"         => $this->radius->f('price')
            );
        } else {
            $summary=array(
            "calls"         => "0",
            "duration"      => "0",
            "firstCall"     => "0000-00-00 00:00:00",
            "lastCall"      => "0000-00-00 00:00:00",
            "traffic"       => "0.00",
            "price"         => "0.00"
            );
        }

        // received calls
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->CanonicalURIField like '%@".addslashes($realm)."' ";
                $rr++;
            }
            $where = $where." ) ";
        }

        if (is_object($filters->receivedCallsFilter)) {
        	$filter=$filters->receivedCallsFilter;
	        if ($filter->beginDate) $where = $where." and $this->startTimeField >= '$filter->beginDate' ";
    	    if ($filter->endDate)   $where = $where." and $this->startTimeField < '$filter->endDate' ";
        	if ($filter->duration)  $where = $where." and $this->durationField $filter->duration ";
			$maxrows=$filters->receivedCallsFilter->calls;
        }

        if (!$maxrows) $maxrows=5;

        $query="select * from $this->radiusTable
        where $this->CanonicalURIField like '$account%'
        and SipMethod = 'Invite'
        $where
        order by radacctid desc
        limit $maxrows";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError(receivedCalls) $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

		$receivedCalls=array();

        while ($this->radius->next_record()) {
            $receivedCalls[]=array(
                               "address"   => $this->normalizeURI($this->radius->f('CallingStationId')),
                               "duration"  => $this->radius->f($this->durationField),
                               "date"      => $this->radius->f($this->startTimeField)
                               );
        }

        // placed calls
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->UserNameField like '%@".addslashes($realm)."' ";
                $rr++;
            }
            $where = $where." ) ";
        }

		if (is_object($filters->placedCallsFilter)) {
        	$filter=$placedCallsFilter;
        	if ($filter->beginDate) $where = $where." and $this->startTimeField >= '$filter->beginDate' ";
        	if ($filter->endDate)   $where = $where." and $this->startTimeField < '$filter->endDate' ";
        	if ($filter->duration)  $where = $where." and $this->durationField $filter->duration ";
			$maxrows=$filters->placedCallsFilter->calls;
        }

        if (!$maxrows) $maxrows=5;

        $query="select * from $this->radiusTable
        where CallingStationId = '$account'
        and SipMethod = 'Invite'
        $where
        and SipResponseCode not like '3%'
        order by radacctid desc
        limit $maxrows";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError(placedCalls) $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

		$placedCalls=array();

        while ($this->radius->next_record()) {
            $placedCalls[]=array(
                               "address"       => $this->normalizeURI($this->radius->f('CalledStationId')),
                               "duration"      => $this->radius->f($this->durationField),
                               "date"          => $this->radius->f($this->startTimeField),
                               "destinationId" => $this->radius->f($this->DestinationIdField)
                               );
        }

        $structure=array( "summary"       => $summary,
                          "receivedCalls" => $receivedCalls,
                          "placedCalls"   => $placedCalls
        			     );

        return $structure;

    }

    function GetLastReceivedCalls ($auth, $account, $filter) {
		syslog(LOG_NOTICE,"GetLastReceivedCalls($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 64 || strlen($account) == 0) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        if (is_object($filter) && !$this->checkCDRFilter($filter)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: ".$this->CDRFilterError;
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "101";
			unset($fault->backtrace);
            return $fault;
        }

        // received calls
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->CanonicalURIField like '%@".addslashes($realm)."' ";
                $rr++;
            }
            $where = $where." ) ";
        }

        $j=0;

        $_els=explode("@",$account);
        if (is_array($this->Realms) && !in_array($_els[1],$this->Realms)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Invalid domain $_els[1] for account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "902";
            unset($fault->backtrace);
            return $fault;
        }

        if (is_object($filter)) {
        	if ($filter->beginDate) $where = $where." and $this->startTimeField >= '$filter->beginDate' ";
        	if ($filter->endDate)   $where = $where." and $this->startTimeField < '$filter->endDate' ";
        	if ($filter->duration)  $where = $where." and $this->durationField $filter->duration ";
           	$maxrows=$filter->calls;
        }

        if (!$maxrows) $maxrows=5;

        $query="select * from $this->radiusTable
        where $this->CanonicalURIField = '$account'
        and AcctSessionTime > 0
        and SipMethod = 'Invite'
        $where
        order by radacctid desc limit $maxrows";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        $structure=array();

        while ($this->radius->next_record()) {
            $structure[]=array(
                               "address"   => $this->normalizeURI($this->radius->f('CallingStationId')),
                               "duration"  => $this->radius->f($this->durationField),
                               "date"      => $this->radius->f($this->startTimeField)
                               );
        }

        return $structure;

    }

    function GetLastPlacedCalls ($auth, $account, $filter) {
		syslog(LOG_NOTICE,"GetLastPlacedCalls($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 64 || strlen($account) == 0) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

        if ($this->Realms) {
            unset($foundDomain);
            foreach ($this->Realms as $realm) {
                if (preg_match("/^.*@$realm$/",$account)) {
                    $foundDomain=1;
                	break;
                }
            }
            if (!$foundDomain) {
            	$fault = new SOAP_Fault();
	            $fault->message = "Error: Invalid domain for account";
            	syslog(LOG_NOTICE,$fault->message);
    	        $fault->code    = "902";
				unset($fault->backtrace);
        	    return $fault;
            }
        }

        if (is_object($filter) && !$this->checkCDRFilter($filter)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: ".$this->CDRFilterError;
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "101";
			unset($fault->backtrace);
            return $fault;
        }

		$account=addslashes($account);

        // placed calls
        if ($this->Realms) {
            $where = " and (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." or ";
                $where = $where." $this->UserNameField like '%@".addslashes($realm)."' ";
                $rr++;
            }
            $where = $where." ) ";
        }

        if (is_object($filter)) {
        	if ($filter->beginDate) $where = $where." and $this->startTimeField >= '$filter->beginDate' ";
        	if ($filter->endDate)   $where = $where." and $this->startTimeField < '$filter->endDate' ";
        	if ($filter->duration)  $where = $where." and $this->durationField $filter->duration ";
           	$maxrows=$filter->calls;
        }

        if (!$maxrows) $maxrows=5;

        $query="select * from $this->radiusTable
        where $this->UserNameField = '$account'
        and SipMethod = 'Invite'
        $where
        and SipResponseCode not like '3%'
        order by radacctid desc
        limit $maxrows";

        if (!$this->radius->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        $structure=array();

        while ($this->radius->next_record()) {
            $structure[]=array(
                               "address"       => $this->normalizeURI($this->radius->f('SipTranslatedRequestURI')),
                               "duration"      => $this->radius->f($this->durationField),
                               "date"          => $this->radius->f($this->startTimeField),
                               "destinationId" => $this->radius->f($this->DestinationIdField)
                               );
        }

        return $structure;

    }

    function GetSERLocations ($auth, $account) {
		syslog(LOG_NOTICE,"GetSERLocations($account)");

		if (!$this->checkSpam()) {
            $fault = new SOAP_Fault();
            if ($this->remains > 0) {
            	$fault->message = "Error: Too many wrong authentication attempts, try again in $this->remains seconds";
            } else {
            	$fault->message = "Error: Too many wrong authentication attempts";
            }
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!$this->checkAuth ($auth)) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Authentication error";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "901";
			unset($fault->backtrace);
            return $fault;
        }

        if (!is_string($account) || strlen($account) > 64 || strlen($account) == 0) {
            $fault = new SOAP_Fault();
            $fault->message = "Error: Missing account";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "102";
			unset($fault->backtrace);
            return $fault;
        }

    	$_els=explode("@",$account);
        $_username=addslashes($_els[0]);
        $_domain=addslashes($_els[1]);

        if ($this->Realms) {
            $where = " and domain in (" ;
            $rr=0;
            foreach ($this->Realms as $realm) {
                if ($rr) $where = $where." ,";
                $where = $where."'".addslashes($realm)."'";
                $rr++;
            }
            $where = $where." ) ";
        }

    	$query="select *,
        SEC_TO_TIME(UNIX_TIMESTAMP(expires)-UNIX_TIMESTAMP()) as remain
        from location
        where username = '$_username'
        and domain     = '$_domain'
        $where
        and SEC_TO_TIME(UNIX_TIMESTAMP(expires)-UNIX_TIMESTAMP()) >= 0
        order by remain desc
        limit 16";

        if (!$this->ser->query($query)) {
            $fault = new SOAP_Fault();
            $error_msg=$this->radius->Error;
            $fault->message = "Error: DatabaseError $error_msg";
            syslog(LOG_NOTICE,$fault->message);
            $fault->code    = "201";
			unset($fault->backtrace);
            return $fault;
        }

        while ($this->ser->next_record()) {
            $structure[]=   array("contact"   => $this->ser->f('contact'),
                                 "expires"    => $this->ser->f('expires'),
                                 "user_agent" => $this->ser->f('user_agent'),
                                 "remain"     => $this->ser->f('remain'),
                    			 "whdr"       => $this->ser->f('whdr')
                                 );
 
        }
        return $structure;
    }

    function normalizeURI($uri) {
        $uri=quoted_printable_decode($uri);
        if (preg_match("/^(.*<sips?:.*)@(.*>)/",$uri,$m)) {
            if (preg_match("/^(.*):/U",$m[2],$p)){
                $uri=$m[1]."@".$p[1].">";
            } else {
                $uri=$m[1]."@".$m[2];
            }
        } else if (preg_match("/^(sips?:.*)[=:;]/U",$uri,$p)) {
            $uri=$p[1];
        }
    
        return $uri;
    }

    function reloadPrepaidAccounts() {
        global $RatingEngine;
        if ($RatingEngine["socketIP"] && $RatingEngine["socketPort"] &&
            $fp = fsockopen ($RatingEngine["socketIP"], $RatingEngine["socketPort"], $errno, $errstr, 2)) {
            fputs($fp, "ReloadPrepaidAccounts\n");
            fclose($fp);
            return 1;
        }
        return 0;
    }
}
?>
