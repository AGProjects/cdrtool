<?
class CDRTool_CT_Sql extends CT_Sql {
  var $database_class = "DB_CDRTool";          ## Which database to connect...
  var $database_table = "active_sessions";     ## and find our session data in this table.
}

class CDRTool_Session extends Session {
  var $classname      = "CDRTool_Session";
  var $auto_init      = "setup.inc";
  var $cookiename     = "CDRc";            ## defaults to classname
  var $magic          = "bzssdgaune";      ## ID seed
  var $mode           = "cookie";          ## We propagate session IDs with cookies
  var $fallback_mode  = "get";
  var $allowcache     = "no";
  var $allowcache_expires     = "5";
  var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
  var $that_class     = "CDRTool_CT_Sql"; ## name of data storage container
  var $gc_probability = 5;  
}

class CDRTool_User extends User {
    var $classname = "CDRTool_User";
    var $magic          = "Abraacdascadabra";     ## ID seed
    var $that_class     = "CDRTool_CT_Sql"; ## data storage container
}

class CDRTool_Auth extends Auth {
  var $classname      = "CDRTool_Auth";
  var $lifetime       =  240;
  var $database_class = "DB_CDRTool";
  var $database_table = "auth_user";
  
  function auth_loginform() {
    global $sess;
    global $_PHPLIB;
    global $max_login_attempts;
    global $CDRTool;

    $username    = $_POST["username"];
    $sendotp     = $_POST["sendotp"];
    $password    = $_POST["password"];
    $challenge   = $_POST["challenge"];
    $response    = $_POST["response"];

    $max_login_attempts=5;

    $sess->register("challenge");
	if (!$challenge) {
		$challenge = md5(uniqid($this->magic));
	}

	$query=sprintf("select * from spam where ip = '%s'",addslashes($_SERVER['REMOTE_ADDR']));
    $this->db->query($query);

    if ($this->db->num_rows()) {
        $this->db->next_record();
    
        $spam_login_ip    = $this->db->f('ip');
        $spam_login_tries = $this->db->f('tries');
        $spam_login_stamp = $this->db->f('stamp');
        $next_try	      = $spam_login_stamp+120;
        $remains          = $next_try-time();
        $next_try	      = Date("Y-m-d H:i:s",$next_try);
        $now	          = Date("Y-m-d H:i:s",time());

	}

    if ($remains < 0) {
        $query=sprintf("delete from spam where ip = '%s'", addslashes($spam_login_ip));
        if ($this->db->query($query)) {
            unset($spam_login_tries);
        }
    }

	if ($spam_login_tries < $max_login_attempts) {
        $title="Login";
        if (is_readable("/etc/cdrtool/local/header.phtml")) {
            include("/etc/cdrtool/local/header.phtml");
        } else {
            include("$CDRTool[Path]/header.phtml");
        }

        $layout = new pageLayoutLocal();
        $layout->showLoginForm($this);
        $layout->showFooter();

	} else {
        if ($spam_login_tries == $max_login_attempts) {
            $log_time=Date("Y-m-d H:i:s",time());
            $log_query=sprintf("insert into log (date,login,ip,description,results)
            values ('%s','%s','%s','%s attempts to wrong login', 'IP blocked until %s')",
            addslashes($log_time),
            addslashes($username),
            addslashes($_SERVER['REMOTE_ADDR']),
            addslashes($spam_login_tries),
            addslashes($next_try)
            );

            $this->db->query($log_query);

        }

        $new_stamp=time();		
        $query=sprintf("update spam
        set tries = tries + 1
        where ip =  '%s' ",addslashes($_SERVER['REMOTE_ADDR']));
        $this->db->query($query);
    
        print "
        <html>
        <body>
        <p>The current time on this system is $now.
        <p>Too many wrong attempts to login, wait until
        $next_try (over $remains seconds) and try again.
        <p>
        If you forgot your password please contact your system administrator for obtaining a new one.
        </body>
        </html>
        ";
        exit;

	}
  }

  function auth_validatelogin() {
    global $d_cli, $d_card, $prepaid_login, $cust_form, $codeFilter, $aNumberFilter,$login_for;
    global $CDRTool;
    global $otp_error, $otpasswd;
    global $verbose;
    global $DATASOURCES;

    $username    = $_POST["username"];
    $sendotp     = $_POST["sendotp"];
    $password    = $_POST["password"];
    $challenge   = $_POST["challenge"];
    $response    = $_POST["response"];
    $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

    if(isset($username)) {
      $this->auth["uname"]=$username;        ## This provides access for "loginform.ihtml"
    }
        
    $uid = false;

    if ($username) {
        $username=trim($username);
        if (preg_match ("/\@/",$username)) {
            $a=explode("@",$username);
        
            $domainAuth = new DomainAuthLocal();

            $ret=$domainAuth->validate($a[0],$a[1],$password,$response);

            if ($ret[0]) {
                foreach ($ret[2] as $allowedDS) {
                    $CDRTool[dataSourcesAllowed][]=$allowedDS;
                }

                if ($ret[1] == "subscriber") {
                    $CDRTool[filter][aNumber] = $username;
                	$this->auth["perm"] = "callsearch,statistics,showPrice,showCallerId";
                } else {
                    $CDRTool[filter][domain]  = $a[1];
                	$this->auth["perm"] = "callsearch,statistics,showPrice,showCallerId";
                }
            }

            return $ret[0];
            
        } else {
            $query = sprintf("select * from auth_user where username = '%s' and expire > NOW()", addslashes($username));
            $this->db->query($query);
            $this->db->next_record();
            $otp_enabled_db =$this->db->f('otp_enable');
            $otp_email      =$this->db->f('email');
            $otp_tel        =$this->db->f('tel');
            $otp_passwd	    =$this->db->f('otp_passwd');
            $otp_passwd_md5 =md5($this->db->f('otp_passwd'));

            if ($sendotp) {
                if ($otp_email || $otp_tel) {
                    $interval="15";
                    print "<p>Sending OneTimePassword ";
                    $random_otp=random_passwd_gen();
                    $expire_otp= date("Y-m-d H:i:s", mktime(date("H"),date("i")+$interval,0,date("m") ,date("d"),date("Y")));
        
                    $update=sprintf("update auth_user set otp_passwd='%s', otp_expire = '%s' where username = '%s'",
                    addslashes($random_otp),
                    addslashes($expire_otp),
                    addslashes($username)
                    );

                    if ($this->db->query($update)) {
                        if ($otp_email) {
							$body=sprintf("%s valid until %s CET (GMT+1) requested from %s",$random_otp,$expire_otp,$_SERVER['REMOTE_ADDR']);
                            mail($otp_email, "OTP for CDRTool", $body, "From: support@ag-projects.com");
                        }
                        if ($otp_tel) {
                        	$body=sprintf("Password is %s valid until %s CET (GMT+1) from %s",$random_otp,$expire_otp,$_SERVER['REMOTE_ADDR']);
                            $otp_tel=preg_replace("/[^0-9+]/","",$otp_tel);
                            otp_sms($otp_tel, $body,"1");
                        }
                        print "<p>
                        Password will expire at: $expire_otp (in $interval minutes)<br>";
                    }
                } else {
                    print "<p>No OTP recipient exists for this account. ";
                }
            }

            $this->db->query(sprintf("
            select *,UNIX_TIMESTAMP(otp_expire) as timestamp_otp ,
            UNIX_TIMESTAMP() as timestamp_now  ".
            "       from %s ".
            "       where username = '%s' " .
            "       and expire > NOW()    " ,
            addslashes($this->database_table),
            addslashes($username)));
        
            $this->db->next_record();
            $uid   		        = $this->db->f("user_id");
            $perm  		        = $this->db->f("perms");
            if ($CDRTool['provider']['clear_text_passwords']!=1) {

                // Update hashed pass if none set and we need hashed ones 
                if ( $this->db->f("password_hashed") == '' && $this->db->f("password") != '') {
                    $newpassmd5=md5($this->db->f("password"));
                    $this->db->query(sprintf(
                        "UPDATE %s".
                        " set password_hashed='%s', password=''".
                        " where username='%s'",
                        addslashes($this->database_table),
                        addslashes($newpassmd5),
                        addslashes($username)));
                    $pass     =  $newpassmd5;
                    $pass_md5 =  $newpassmd5;
                } else {
                    $pass  		        = $this->db->f("password_hashed");
                    $pass_md5  	        = $this->db->f("password_hashed");
                }
            } else {
                $pass  		        = $this->db->f("password");
                $pass_md5  	        = md5($this->db->f("password"));
            }
            $otp_passwd 	    = $this->db->f("otp_passwd");
            if (strlen($this->db->f('otp_passwd'))) {
            	$otp_passwd_md5	=md5($this->db->f('otp_passwd'));
            } else {
            	$otp_passwd_md5="garbage";
            }

            $timestamp_otp	    = $this->db->f("timestamp_otp");
            $timestamp_now      = $this->db->f("timestamp_now");

            $CDRTool['loginName']          = $this->db->f("name");
            $CDRTool['loginEmail']         = $this->db->f("email");

            $_dataSourcesAllowed = explode(",",$this->db->f("sources"));
		    $_datasourceDefined  = array_keys($DATASOURCES);

            $CDRTool['dataSourcesAllowed'] = array_intersect($_dataSourcesAllowed,$_datasourceDefined);

            // limits per CDRTool login account
            $CDRTool['filter']['user_id']    = $this->db->f("user_id");
            $CDRTool['filter']['aNumber']    = $this->db->f('aNumberFilter');
            $CDRTool['filter']['displayA']   = $this->db->f('display_cli');
            $CDRTool['filter']['domain']     = $this->db->f('domainFilter');
            $CDRTool['filter']['gateway']    = $this->db->f('gatewayFilter');
            $CDRTool['filter']['compid']     = $this->db->f('compidFilter');
            $CDRTool['filter']['cscode']     = $this->db->f('cscodeFilter');

            if (preg_match("/^(\d+)\.(\d+)$/",$this->db->f('impersonate'),$m)) {
            	$CDRTool['filter']['customer']  = $m[1];
                $CDRTool['filter']['reseller']  = $m[2];
            } else if (preg_match("/^(\d+)$/",$this->db->f('impersonate'),$m)) {
            	$CDRTool['filter']['customer']  = $m[1];
                $CDRTool['filter']['reseller']  = $m[1];
            } else {
            	$CDRTool['filter']['customer']  = '';
                $CDRTool['filter']['reseller']  = '';
            }

            $CDRTool['impersonate'] = $this->db->f('impersonate');
            
            if ($this->db->f('afterDateFilter') && $this->db->f('afterDateFilter') != "0000-00-00") {
            	$CDRTool['filter']['after_date']=$this->db->f('afterDateFilter');
            }

			if ($CDRTool['filter']['customer']) {
                // get soap credentials from NGNPro database
                global $soapEngines ;
    
                require_once('SOAP/Client.php');
                require("/etc/cdrtool/ngnpro_engines.inc");
                require_once("ngnpro_soap_library.php");
        
                $this->SOAPlogin=array(
                                       "username" => $soapEngines[$CDRTool['ngnpro_reseller_engine']]['username'],
                                       "password" => $soapEngines[$CDRTool['ngnpro_reseller_engine']]['password'],
                                       "admin"    => true
                );
        
                $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');
        
                $this->CustomerPort  = new WebService_NGNPro_CustomerPort($soapEngines[$CDRTool['ngnpro_reseller_engine']]['url']);
                $this->CustomerPort->setOpt('curl', CURLOPT_TIMEOUT,        5);
                $this->CustomerPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->CustomerPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
    
                $filter  = array('customer' => intval($CDRTool['filter']['customer']));
                $range   = array('start' => 0,'count' => 1);
                $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
                $Query=array('filter'     => $filter,'orderBy' => $orderBy,'range'   => $range);
        
                // Call function
                $this->CustomerPort->addHeader($this->SoapAuth);
                $result     = $this->CustomerPort->getCustomers($Query);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    $log = sprintf ("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    syslog(LOG_NOTICE, $log);
                } else {
                    if (count($result->accounts) == 1) {
                        if ($result->accounts[0]->impersonate) {
                            // get the credentials of the impersonate field
                            $filter  = array('customer' => intval($result->accounts[0]->impersonate), 'reseller' => intval($result->accounts[0]->reseller));
                            $range   = array('start' => 0,'count' => 1);
                            $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
                            $Query=array('filter'     => $filter,'orderBy' => $orderBy,'range'   => $range);
                    
                            // Call function
			                $this->CustomerPort->addHeader($this->SoapAuth);
                            $result     = $this->CustomerPort->getCustomers($Query);

                            if (PEAR::isError($result)) {
                                $error_msg  = $result->getMessage();
                                $error_fault= $result->getFault();
                                $error_code = $result->getCode();
                                $log = sprintf ("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                                syslog(LOG_NOTICE, $log);
                            } else {
                                if (count($result->accounts) == 1) {
                                    $CDRTool["soap_username"] = $result->accounts[0]->username;
                                    $CDRTool["soap_password"] = $result->accounts[0]->password;
                                } else {
                        	    print "<p>Error retrieving customer data from the provisioning server, there is no such impersonate id. ";
                                }
                            }
                        } else {
                            $CDRTool["soap_username"] = $result->accounts[0]->username;
                            $CDRTool["soap_password"] = $result->accounts[0]->password;
                        }
                    } else {
                        print "<p>Error retrieving customer data from the provisioning server, there is no such customer id. ";
                    }
                }
            }

            $expected_response = md5("$username:$pass_md5:$challenge");
            $expect_otp=md5("$username:$otp_passwd_md5:$challenge");
        
            ## True when JS is disabled
            if ($response == "") {
                if ($CDRTool['provider']['clear_text_passwords']!= 1 ) {
                    $password=md5($password);
                }
                if ($password == $pass || ($password == $otp_passwd && $timestamp_otp > $timestamp_now)) {
                    $this->auth["perm"] = $perm;
                    return $uid;
                } else {
                    return false;
                }
            } else {
                ## Response is set, JS is enabled
                // we check if either otp or normal password match
                //print "<p>$response == $expected_response <p>$response == $expect_otp";
                if ($expected_response == $response || ($response == $expect_otp && $timestamp_otp > $timestamp_now)) {
                    $this->auth["perm"] = $perm;
                    return $uid;
                } else {
                    return false;
                }
            }
    	}
  	}

  }

}

class CDRTool_Perm extends Perm {
  var $classname = "CDRTool_Perm";
  var $permissions = array(
            "admin"         => 1,
            "callsearch"    => 2,
            "statistics"    => 4,
            "sqlquery"      => 8,
            "soapclient"    => 16,
            "rates"         => 32,
            "showCallerId"  => 64,
            "showPrice"     => 128,
            "provisioning"  => 256,
            "readonly"      => 512
            );

  function perm_invalid($does_have, $must_have) {
    global $perm, $auth, $sess;
    global $_PHPLIB;

    include($_PHPLIB["libdir"] . "perminvalid.phtml");
  }
}

class SIP_Subscriber_Session extends Session {
  var $classname      = "SIP_Subscriber_Session";
  var $auto_init      = "SIP_setup.inc";
  var $cookiename     = "SIPCookie2";        ## defaults to classname
  var $magic          = "3333jhjjjss13";      ## ID seed
  var $mode           = "cookie";            ## We propagate session IDs with cookies
  var $fallback_mode  = "get";
  var $allowcache     = "public";
  var $lifetime       = 0;                   ## 0 = do session cookies, else minutes
  var $that_class     = "CDRTool_CT_Sql";     ## name of data storage container
  var $gc_probability = 5;
}

class SIP_Subscriber_Auth extends Auth {
  // use this auth for SIP accounts
  var $classname      = "SIP_Subscriber_Auth";
  var $lifetime       =  0;
  var $magic          = "d66mmmg111dsgzz";  ## Challenge seed

  function auth_loginform() {

    global $sess;
    global $max_login_attempts;

    $username    = $_POST["username"];
    $password    = $_POST["password"];
    $challenge   = $_POST["challenge"];
    $step        = $_POST["step"];
    $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

    $sess->register("challenge");

    if (!$challenge) {
		$challenge = md5(uniqid($this->magic));
	}

    include("sip_login.phtml");
  }
  
  function auth_validatelogin() {
        global $SIP;

        $username    = $_POST["username"];
        $password    = $_POST["password"];
        $challenge   = $_POST["challenge"];
        $response    = $_POST["response"];

        if(isset($username)) {
            $this->auth["uname"]=$username;
        }

        $a=explode("@",$username);
        $domain= $a[1];
        if (count($a) !=2 ) return false;

	    global $domainFilters, $resellerFilters, $soapEngines ;

        require_once('SOAP/Client.php');
      	require("/etc/cdrtool/ngnpro_engines.inc");
	    require_once("ngnpro_soap_library.php");

        $SIP['account']    = $username;

        if ($domainFilters[$domain]['sip_engine']) {
            $SIP['engine']   = $domainFilters[$domain]['sip_engine'];
        } else if ($domainFilters['default']['sip_engine']) {
            $SIP['engine']=$domainFilters['default']['sip_engine'];
        } else {
            print "Error: cannot authenticate SIP subscriber, no domainFilter defined in ngnpro_engines.inc";
            return false;
        }

        printf ("Engine=%s",$SIP['engine']);

        $this->SOAPlogin=array(
                               "username" => $soapEngines[$SIP['engine']]['username'],
                               "password" => $soapEngines[$SIP['engine']]['password'],
                               "admin"    => true
        );

        $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

        $this->SipPort  = new WebService_NGNPro_SipPort($soapEngines[$SIP['engine']]['url']);

        $this->SipPort->setOpt('curl', CURLOPT_TIMEOUT,        5);
        $this->SipPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->SipPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
        $this->SipPort->addHeader($this->SoapAuth);

        $result     = $this->SipPort->getAccount(array("username" =>$a[0],"domain"   =>$domain));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log = printf ("SOAP error from %s (SipPort): %s (%s): %s",$soapEngines[$SIP['engine']]['url'],$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        }


        //dprint_r($result->properties);

        $web_password='';
        foreach ($result->properties as $_property) {
            if ($_property->name == 'web_password') {
                $web_password=$_property->value;
                break;
            }
        }

        if (!$web_password) $web_password=$result->password;

        $pass_md5  	   = md5($web_password);
        $expected_response = md5("$username:$pass_md5:$challenge");

        $SIP['customer'] = $result->customer;
        $SIP['reseller'] = $result->reseller;

        if ($expected_response == $response) {
            return true;
       } else {
            return false;
       }
    }
}

function otp_sms($tel,$message,$hideoutput) {
    $tel=preg_replace("/[^0-9]/","",$tel);
    $tel="+".$tel;
    
    $message=substr($message,0,135);

    if (!$tel || !$message) return 0;

	$cmd="/usr/bin/sms --destination $tel --message \"$message\"";

    exec($cmd,$output,$returnCode);

	if ($returnCode == "0") {
		if (!$hideoutput) {
			print "<p>";
            printf (_("SMS sent succesfully to %s. "), $tel);
		}
	} else {
        print "<p>";
        print "<b>";
        print "OTP ";
        print _("Error");
	}
}

function random_passwd_gen() {
    # Calculating random password
    $alf=array("a","b","c","d","e","f",
               "h","i","j","k","l","m",
               "n","p","r","s","t","w",
               "x","y","1","2","3","4",
               "5","6","7","8","9");
    
    while($i < 5) {
        srand((double)microtime()*1000000);
        $randval = rand(0,28);
        $random_otp="$random_otp"."$alf[$randval]";
        $i++;
    }	      
    return $random_otp;
}

function dprint($msg="") {
    global $verbose;
    if ($verbose) {
        print "<br>$msg\n";
    }
}

function dprint_r($obj) {
    global $verbose;
    if ($verbose) {
        print "<pre>\n";
        print_r($obj);
        print "</pre>\n";
    }
}

function checkEmail($email) {
    global $verbose;
    dprint ("<b>checkEmail($email) </b>");
    if (stristr($email,"-.") ||
        !preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_.-]*@([a-zA-Z0-9][a-zA-Z0-9-]*\.)+[a-zA-Z]{2,}$/i",$email)) {
        return 0;
    }
    return 1;
}

class OpenSIPS_DomainAuth {

    function OpenSIPS_DomainAuth () {
        $this->userDB   = new DB_opensips;
        $this->allowedDataSourcesSubscriber = array('opensips_radius','sip_trace','media_trace');
    }

    function validate ($user, $domain, $password) {

      	$ha1 = md5($user. ':' . $domain . ':' . $password);

        $query = sprintf("SELECT * FROM subscriber
                  WHERE username = '%s'
                  AND     domain = '%s'
                  AND   ( password = '%s' or ha1 = '%s') ",
                  addslashes($user),
                  addslashes($domain),
                  addslashes($password),
                  addslashes($ha1)
                  );
    
        if ($this->userDB->query($query)) {
            $this->userDB->next_record();
            $uid = $this->userDB->f('username');
            if ($uid) {
                return array($uid, "subscriber", $this->allowedDataSourcesSubscriber);
            }
        }
    }
}

class SipThor_DomainAuth {

    function SipThor_DomainAuth () {
        $this->userDB   = new DB_sipthor;
        $this->allowedDataSourcesSubscriber = array('sipthor','sip_trace_thor','media_trace_thor');
    }

    function validate ($user, $domain, $password,$response) {
    	$query = sprintf("SELECT * FROM sip_accounts
                  WHERE username = '%s'
                  AND     domain = '%s'
                  ",
                  addslashes($user),
                  addslashes($domain)
                  );
        
        if ($this->userDB->query($query)) {
            $this->userDB->next_record();

            $profile=json_decode($this->userDB->f('profile'),'true');

            if ($profile['properties']['web_password']) {
                $check_password = $profile['properties']['web_password'];
            } else {
                $check_password = $profile['password'];
            }

            $check_password_md5 = md5("$check_password");
            $expected_response = md5("$user@$domain:$check_password_md5:$challenge");

            if ($expected_response == $response) {
                $uid = $this->userDB->f('username');
                if ($uid) {
                    return array($uid, "subscriber", $this->allowedDataSourcesSubscriber);
                }
            } else if ($check_password == $password) {
                $uid = $this->userDB->f('username');
                if ($uid) {
                    return array($uid, "subscriber", $this->allowedDataSourcesSubscriber);
                }
            }
        }
    }
}

class pageLayout {

    function showLoginForm(&$parentAuth) {

        global $username, $otp_error, $CDRTool;
        $auth=$parentAuth;
        
        print "
        <script language=javascript src=md5.js></script>
        <script language=javascript>
        
          function doChallengeResponse() {
            str = document.login.username.value + \":\" +
                  MD5(document.login.password.value) + \":\" +
                  document.login.challenge.value;
        
            document.login.response.value = MD5(str);
            document.login.password.value = \"\";
            document.login.submit();
          }
        
        </script>
        ";
        
        $url=$auth->url();
        print "
            <div id=wrapper2>
        <br>
    	";
	$this->hasAGProjectslogo=1;
	$logo=$CDRTool['tld']."/images/CDRTool.png";
        print "<center><a href=http://cdrtool.ag-projects.com target=agprojects><img src=$logo border=0></a></center><br>";
        print "
        <form  class=form-horizontal action=\"$url\" method=post name=login>
        <p>
        ";
        if ($CDRTool[provider][sampleLoginSubscriber]) {
            $sampleLoginSubscriber    = $CDRTool[provider][sampleLoginSubscriber];
        } else {
            $sampleLoginSubscriber="account@sip2sip.info";
        }
        if ($CDRTool[provider][sampleLoginDomain]) {
            $sampleLoginDomain    = $CDRTool[provider][sampleLoginDomain];
        } else {
            $sampleLoginDomain="sip2sip.info";
        }
        
        $web_username=$auth->auth["uname"];
        print "
        <div class=control-group>
            <label class=control-label>
            Username
            </label>
            <div class=controls>
            <input rel='popover' 
            placeholder='Please identify yourself'
            data-original-title='User name types supported' 
            data-content='        <ul>
        <li>Subscriber account<br>(e.g. $sampleLoginSubscriber)</li>
        <li>Domain account<br>(e.g. $sampleLoginDomain)</li>
        <li>Administrator account
        </ul>
            '
            type=text name=username value=\"$web_username\" size=40 maxlength=255>
            </div>
        </div>
        <div class=control-group>
            <label class=control-label>
            Password</label>
          <div class=controls>
            <input type=password name=password size=40 maxlength=32>
            </div>
         </div>
          <div class='controls'>
            <input onClick=\"doChallengeResponse(); return false;\" type=submit name=submitbtn class='btn btn-primary' value=\"Login\">
            </div>
            <br>
        ";
        
        print "
            <div class='well well-small'>
            <div class=row-fluid>
            <div class=pull-left>
        If you make use of <b>O</b>ne <b>T</b>ime <b>P</b>asswords:
        <ul class=s>
            <li>Fill in your username
            <li>Press the Send OTP button
            <li>Collect the password
            <li>Fill it in the password field
            <li>Press the Login Now button to login
            </ul></div><div class=pull-right style='height:100px; vertical-align:bottom'>
        <input class='btn' type=submit name=sendotp style='position:relative; top:60px;' value=\"Send OTP\">
        </div></div></div>
        </div>
        ";
        
       
        
        if ( isset($username)) {
            if (!$sendotp || $username) {
                print "
                 <p>
                 <table>
                  <tr>
                   <td colspan=2><font color=red><b>Invalid username/password combination. <br>
                   <br> $otp_error</b></font></td>
                  </tr>
                 </table>
                ";
                
                $spam=new DB_CDRTool;

		        $query=sprintf("select * from spam where ip = '%s'",addslashes($_SERVER[REMOTE_ADDR]));
    			$spam->query($query);

                if (!$spam->num_rows()) {
                    $query=sprintf("insert into spam (ip,tries,login,stamp)
                    values ('%s','1','%s','%s')
                    ",$_SERVER[REMOTE_ADDR],addslashes($username),time());
                } else {
                    $query=sprintf("update spam set
                    tries = tries +1 where ip = '%s'", addslashes($_SERVER[REMOTE_ADDR]));
                }
                $spam->query($query);
            
            } else {
                print "Please fill in your One Time Password!";
            }
        }
        print "<input type=\"hidden\" name=\"response\"  value=\"\">";
        print "<input type=\"hidden\" name=\"challenge\" value=\"$challenge\">";
        print "
        </table>
        </form>
        <script language=JavaScript>
        <!--
          if (document.login.username.value != '') {
              document.login.password.focus();
          } else {
              document.login.username.focus();
          }
        // -->
        </script>
        ";
    }
 
    function showHeader($title='') {
    }
 
    function showTopMenu($title='') {
        global $DATASOURCES, $CDRTool, $cdr_source, $perm;
        $version=trim(file_get_contents('version'));
        print '
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                   </a>
                        ';
        
        $now_print=Date("Y-m-d H:i:s",time());
        $tz=$CDRTool['provider']['timezone'];
        
        if (is_readable($CDRTool['Path']."/images/logo.gif")) {
            printf ("<span style='margin-left: -20px; padding: 0px 20px 0px; float:left'><img height=\"35px\" src=\"%s/images/logo.gif\"></span>\n",$CDRTool['tld']);
        } else if (is_readable($CDRTool['Path']."/images/logo.jpg")) {
            printf ("span style='margin-left: -20px; padding: 0px 20px 0px; float:left'><img height=\"35px\" src=\"%s/images/logo.jpg\"></span>\n",$CDRTool['tld']);
        } else if (is_readable($CDRTool['Path']."/images/logo.png")) {
            printf ("<span style='margin-left: -20px; padding: 0px 20px 0px; float:left'><img height=\"35px\"src=\"%s/images/logo.png\"></span>\n",$CDRTool['tld']);
        } else {
            $this->hasAGProjectslogo=1;
            print '<a class="brand" href=http://cdrtool.ag-projects.com target=agprojects>CDRTool</a>';
        }
        print'
            <div id="menu" class="btn-group pull-right">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon-user"></i> ';
        print $CDRTool['loginName'];
        print '
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                <li>
                    <a style="font-size: 11px" href="http://cdrtool.ag-projects.com" target=changelog>About v. ';
        print "$version";
        print '     </a></li>
                    <li class="divider"></li>
                    <li><a href=logout.phtml target=_top>Logout</a></li>
                </ul>
            </div>
            <div class="nav-collapse">
                <ul class="nav">';

        if ($perm->have_perm("callsearch")) {  
            print "
                    <li><a href=callsearch.phtml>CDR</a></li>";
        }

        if ($perm->have_perm("rates")) {
            print "
                    <li><a href=rating_tables.phtml>Rating</a></li>";
            print "
                    <li><a href=rating_tables.phtml?table=prepaid>Prepaid</a></li>";
            print "
                    <li><a href=rating_tables.phtml?table=quota_usage>Quota</a></li>";
        }

        if ($perm->have_perm("statistics")) {
            print "
                    <li><a href=network_status.phtml>Network</a></li>";
            print "
                    <li><a href=media_sessions.phtml>Sessions</a></li>";
            print "
                    <li><a href=sip_usage.phtml>Usage</a></li>";
        }
        
        if ($perm->have_perm("admin")) {
            print "<li><a href=mysql_replication_status.phtml>Replication</a></li>";
        }

        if ($perm->have_perm("provisioning")) {
            print "<li><a href=provisioning.phtml>Provisioning</a></li>";
        }
        
        print "<li><a href=accounts.phtml>Accounts</a></li>";
        print "<li><a href=log.phtml>Logs</a></li>";
        print '
                </ul>
              </div><!--/.nav-collapse -->
            </div>
          </div>
        </div>';


        print "<div class='container-fluid' ><div class='main'>";
        print "<div class=\"page-header\">";
        print "<h1>";
        print "$title";
        print $DATASOURCES[$cdr_source]['name'];
        print "</h1></div>";
        // print "<table width=100% cellpadding=5 CELLSPACING=0 border=5 align=center>
        // <tr>
        // ";

        // if (is_readable($CDRTool['Path']."/images/logo.gif")) {
        // 	printf ("<td valign=middle><img src=\"%s/images/logo.gif\"></td>",$CDRTool['tld']);
        // } else if (is_readable($CDRTool['Path']."/images/logo.jpg")) {
        // 	printf ("<td valign=middle><img src=\"%s/images/logo.jpg\"></td>",$CDRTool['tld']);
        // } else if (is_readable($CDRTool['Path']."/images/logo.png")) {
        // 	printf ("<td valign=middle><img src=\"%s/images/logo.png\"></td>",$CDRTool['tld']);
        // } else {
        //     $this->hasAGProjectslogo=1;
        //     print "<td>";
        //     printf ("<a href=http://cdrtool.ag-projects.com target=agprojects><img src='%s/images/CDRTool.png' border=0></a>",$CDRTool['tld']);
        //     print "</td>";
        // }

        // print "
        // <td width=100%>
        //     <table width=100%>
        //     </tr>
        //     <td>";
        //     print "<h1>$title";
        //     print " ";
        //     print $DATASOURCES[$cdr_source]['name'];
        //     print "</h1><p>";
        //     print "<td align=right>";
        //     print "</td></tr>
        //     </table>
        //     "; 
        
        // print "<table width=100%>
        // <tr>
        // <td align=left>
        // <table border=0 width=100%>
        // <tr>
        // "; 

        // if ($perm->have_perm("callsearch")) {  
        //     print " <td class=tab><a href=callsearch.phtml>CDRs</a></td> ";
        // }
        
        // if ($perm->have_perm("rates")) {
        //     print " <td class=tab><a href=rating_tables.phtml>Rating</a></td>";
        //     print " <td class=tab><a href=rating_tables.phtml?table=prepaid>Prepaid</a></td>";
        //     print " <td class=tab><a href=rating_tables.phtml?table=quota_usage>Quota</a></td>";
        // }

        // if ($perm->have_perm("statistics")) {
        //     print " <td class=tab><a href=network_status.phtml>Network</a></td>";
        //     print " <td class=tab><a href=media_sessions.phtml>Sessions</a></td>";
        //     print " <td class=tab><a href=status/usage/index.phtml target=usage>Usage</a></td>";
        // }
        
        // if ($perm->have_perm("admin")) {
        //     print " <td class=tab><a href=mysql_replication_status.phtml>Replication</a></td>";
        // }

        // if ($perm->have_perm("provisioning")) {
        //     print " <td class=tab><a href=provisioning.phtml>Provisioning</a></td>";
        // }
        
        // print " <td class=tab><a href=accounts.phtml>Accounts</a></td>";
        // print " <td class=tab><a href=log.phtml>Logs</a></td>";
        
        // $now_print=Date("Y-m-d H:i:s",time());
        // $tz=$CDRTool['provider']['timezone'];
        // //print " <td>$now_print | <a href=doc/changelog target=changelog>v. $version</a></td>";
        // print " <td><a href=doc/changelog target=changelog>v. $version</a></td>";
        // print "
        // </tr>
        // </table>
        // </td>
        // <td align=right>
        // ";
        // printf ("<a href=logout.phtml target=_top><b>Logout %s</b></a>",$CDRTool['loginName']);
        // print "
        // </tr>
        // </table>
        // </td>
        // </tr>
        // </table>
        // <p>
        //";
    }

    function showTopMenuSubscriber($title="") {
        global $DATASOURCES, $CDRTool, $cdr_source, $perm;
    
        $version=trim(file_get_contents(version));
        print "<table width=100% cellpadding=5 CELLSPACING=0 border=5 align=center>
        <tr>
        <td>
            <table width=100%>
            </tr>
            <td>";
            print "<h1>$title";
            print " ";
            print $DATASOURCES[$cdr_source]['name'];
            print "</h1><p>";
            print "<td align=right>";
            print "</td></tr>
            </table>
            "; 
        
        print "<table width=100%>
        <tr>
        <td align=left>
        "; 
        if ($perm->have_perm("callsearch")) {  
            print " <a href=callsearch.phtml>Call detail records</a>";
        } 
        
        $now_print=Date("Y-m-d H:i:s",time());
        $tz=getenv('TZ');
        print " | $now_print ($tz) | v. $version";
        print "
        </td>
        <td align=right>
        ";
        printf ("<a href=logout.phtml target=_top><b>Logout %s</b></a>",$CDRTool['loginName']);
        print "
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <p>
        ";
    }
 
    function showLegalNotice () {
        global $loginname, $CDRTool;
 
        $CDRTool_company=$CDRTool[provider][name];
 
        $legalNotice="Legal Notice".
        "\n\n".
        "This software is intended for the use of $CDRTool_company, ".
        "resellers of $CDRTool_company and the customers of $CDRTool_company. ".
        "The use of this software by any natural or legal person that does ".
        "not belong to $CDRTool_company, its Resellers or is a not a ".
        "customer of $CDRTool_company or its resellers is therefore ".
        "expressly prohibited.".
        "\n\n".
        "All the information stored on, and accessible through this software ".
        "are personal data protected as such by international and domestic ".
        "legislation relating to the processing of personal data and ".
        "the protection of the right to privacy. For these reasons: ".
        "1. This software shall exclusively be used to the extent that it ".
        "is necessary for the provision of services to $CDRTool_company ".
        "customers and its resellers; ".
        "2. No information displayed on, and accessible through this software ".
        "shall be communicated to any natural or legal person outside ".
        "$CDRTool_company and its resellers, without prejudice to the ".
        "possibility for competent authorities (namely government bodies, ".
        "courts, regulatory authorities) to be informed of billing or ".
        "traffic data in conformity with the applicable legislation. ".
        "\n\n";

        $loginName=$CDRTool[loginName];
		$this->hasAGProjectslogo=1;

        print "
            <div id=wrapper2>
            <center>
            <a href=http://cdrtool.ag-projects.com target=agprojects><img src=images/CDRTool.png border=0></a>
            </center>
        <h2 class=page-header>Terms and conditions</h2>
        <div class='row-fluid'>
        <form class='form-horizontal' action=callsearch.phtml method=post>
        <textarea class=span12 name=legal rows=20 cols=60 wrap=virtual readonly=yes>$legalNotice</textarea>
        <p>
        You are currently logged in as $loginname

        <center>
        <p>
        If you agree with the Terms and Conditions, <br>
        press on <b>I agree</b> button to continue.</p>
        <input type=submit class=btn value=\"I agree\">
        </center>
        <input type=hidden name=previous_page value=license_page>
        </div>
        </form>
        </div>
        ";
    }   
 
    function showFooter() {
        global $CDRTool;
        if (!$CDRTool['filter']['aNumber'] && !$this->hasAGProjectslogo) {
            $thisYear=date("Y",time());
            print "
            <p>
            <table width=100% border=0 align=center>
            <tr>
            <td align=right>
            <a href=http://cdrtool.ag-projects.com target=agprojects><img src=images/PoweredbyAGProjects.png border=0>
            </td>
            </tr>
            </table>
            ";
        }
    }

    function showLogout ($loginname) {
       print "
       <table width=70% align=center>
       <td>
       <br>
       <br>
         <h1>Logout</h1>
         <p>
         You have been logged in as $loginname.</b>
         <p> 
         You have been logged out.
       <br>
       <br>
       <p>
       <a href=index.phtml>Login again</a>
       </td>
       </table>
        ";
    }
}

function unLockTables ($dbid) {
	$dbid->query("unlock tables");
}

function changeLanguage($lang='en',$domain='cdrtool') {
    // run dpkg-reconfigure locales and select support languages .utf8

    $lang = languageCodeFor(isset($lang) ? $lang : 'en');
    $lang.='.utf8';
    setlocale(LC_ALL, $lang); 
    bindtextdomain($domain, '/var/www/CDRTool/po/locale');
    bind_textdomain_codeset($domain,'UTF-8');
    textdomain($domain);
}

// return full language code for given 2 letter language code
function languageCodeFor($lang='en') {
    $lang = isset($lang) ? strtolower($lang) : 'en';
    switch ($lang) {
        case 'en': return 'en_US'; // this can be C or en_US
        case 'ja': return 'ja_JP';
        default  : return ($lang . '_' . strtoupper($lang));
    }
    return 'C'; // this will never be reached
}

function RandomString($len=11) {
    $alf=array("a","b","c","d","e","f",
           "h","i","j","k","l","m",
           "n","p","r","s","t","w",
           "x","y","1","2","3","4",
           "5","6","7","8","9");
    $i=0;
    while($i < $len) {
        srand((double)microtime()*1000000);
        $randval = rand(0,28);
        $string="$string"."$alf[$randval]";
        $i++;
    }
    return $string;
}

function RandomNumber($len=5,$skipzero=false) {
	
    $alf=array("1","2","3","4","5",
               "9","8","7","6");
    if (!$skipzero) $alf[]="0";
    
    $i=0;
    while($i < $len) {
        srand((double)microtime()*1000000);
        $randval = rand(0,9);
        $string="$string"."$alf[$randval]";
        $i++;
    }          
    return $string;
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
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

function get_location($ip) {
    $geo_location=array();
    $geo_location['country'] = '';
    $geo_location['city'] = '' ;
    $geo_location['code'] = '';
    $geo_location['region'] = '';
    
    if ($_loc=geoip_record_by_name($ip)) {
        if ($_loc['city']) {
            $geo_location['city'] = $_loc['city']; 
        }    
        $geo_location['country'] = $_loc['country_name']; 
        $geo_location['code']    = $_loc['country_code'];
        $geo_location['region']  = $_loc['region'];
    }
    return json_encode($geo_location);
}

?>
