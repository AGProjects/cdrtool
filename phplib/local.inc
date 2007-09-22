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
    $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];

    $max_login_attempts=5;

    $sess->register("challenge");
	if (!$challenge) {
		$challenge = md5(uniqid($this->magic));
	}

	$query="select * from spam where ip = '$REMOTE_ADDR'";
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
        $query="delete from spam where ip = '$spam_login_ip'";
        if ($this->db->query("$query")) {
            unset($spam_login_tries);
        }
    }
           
	if ($spam_login_tries < $max_login_attempts) {
        $title="Login";
        if (is_readable("local/header.phtml")) {
            include("local/header.phtml");
        } else {
            include("header.phtml");
        }
        $layout = new pageLayoutLocal();
        //$layout->showHeader();
        $layout->showLoginForm($this);
        $layout->showFooter();

	} else {
        if ($spam_login_tries == $max_login_attempts) {
            $log_time=Date("Y-m-d H:i:s",time());
            $log_query=sprintf("insert into log (date,login,ip,description,results)
            values ('%s','%s','%s','%s attempts to wrong login', 'IP blocked until %s')",
            $log_time,addslashes($username),$REMOTE_ADDR,$spam_login_tries,$next_try);

            $this->db->query($log_query);

        }

        $new_stamp=time();		
        $query="update spam
        set tries = tries + 1
        where ip =  '$REMOTE_ADDR' ";
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

            $ret=$domainAuth->validate($a[0],$a[1],$password);

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
        	$username_sql=addslashes($username);
            $this->db->query("select * from auth_user where
            username = '$username_sql' and expire > NOW()");
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
        
                    $update="update auth_user
                    set otp_passwd='$random_otp',
                    otp_expire = '$expire_otp'
                    where username = '$username_sql'
                    ";
                    if ($this->db->query($update)) {
                        if ($otp_email) {
                            mail("$otp_email", "OTP for CDRTool", "$random_otp valid until $expire_otp CET (GMT+1) requested from $REMOTE_ADDR", "From: support@ag-projects.com");
                            dprint ("via email to $otp_email");
                        }
                        if ($otp_tel) {
                            $otp_tel=preg_replace("/[^0-9+]/","",$otp_tel);
                            dprint ("via SMS to $otp_tel");
                            otp_sms("$otp_tel", "Password is $random_otp valid until $expire_otp CET (GMT+1) from $REMOTE_ADDR","1");
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
            $this->database_table,
            addslashes($username)));
        
            $this->db->next_record();
            $uid   		        = $this->db->f("user_id");
            $perm  		        = $this->db->f("perms");
            $pass  		        = $this->db->f("password");
            $pass_md5  	        = md5($this->db->f("password"));
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
            $CDRTool['filter']['soap']       = $this->db->f('soapFilter');
            $CDRTool['reseller']             = $this->db->f('reseller');
            
            if ($this->db->f('only_after_date') && $this->db->f('only_after_date') != "0000-00-00") {
            	$CDRTool[filter][after_date]=$this->db->f('only_after_date');
            }
      
            $expected_response = md5("$username:$pass_md5:$challenge");
            $expect_otp=md5("$username:$otp_passwd_md5:$challenge");
        
            ## True when JS is disabled
            if ($response == "") {
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
            "provisioning"  => 256
            );

  function perm_invalid($does_have, $must_have) {
    global $perm, $auth, $sess;
    global $_PHPLIB;

    include($_PHPLIB["libdir"] . "perminvalid.phtml");
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

class DomainAuth {

    function DomainAuth () {
        $this->userDB   = new DB_ser;
        $this->allowedDataSourcesSubscriber = array('ser_radius');
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

class pageLayout {

    function showLoginForm(&$parentAuth) {

        global $simplewire_ID, $username, $otp_error, $CDRTool;
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
        <script language=JavaScript>
        <!--
          if (document.forms[0][0].value != '') {
              document.forms[0][1].focus();
          } else {
              document.forms[0][0].focus();
          }
        // -->
        </script>
        ";
        
        $url=$auth->url();
        print "
        <center>
        <br>
    	";
	$this->hasAGProjectslogo=1;
	$logo=$CDRTool['tld']."/images/CDRTool.gif";
        print "<a href=http://ag-projects.com target=agprojects><img src=$logo border=0></a>";
        print "
        <form action=\"$url\" method=post>
        <p>
        <table align=center cellspacing=0 cellpadding=2 width=300 border=5>
        <tr>
        <td colspan=2>
        <p>
        Please identify yourself with username and password.
        ";
        if ($CDRTool[provider][sampleLoginSubscriber]) {
            $sampleLoginSubscriber    = $CDRTool[provider][sampleLoginSubscriber];
        } else {
            $sampleLoginSubscriber="account@example.com";
        }
        if ($CDRTool[provider][sampleLoginDomain]) {
            $sampleLoginDomain    = $CDRTool[provider][sampleLoginDomain];
        } else {
            $sampleLoginDomain="client2.eurovoice.ro";
        }
        
        $web_username=$auth->auth["uname"];
        print "
        <p>
        <ul>
        <li>Subscriber account (e.g. $sampleLoginSubscriber)</li>
        <li>Domain account (e.g. $sampleLoginDomain)</li>
        <li>Administrator account
        </ul>
        </td>
        </tr>
        <tr valign=middle align=left>
        
          <td>Username:</td>
          <td>
            <input type=text name=username value=\"$web_username\" size=40 maxlength=255></td>
         </tr>
         <tr valign=middle align=left>
          <td>Password:</td>
          <td>
            <input type=password name=password size=40 maxlength=32></td>
         </tr>
        
        <tr>
          <td valign=center align=center>
            &nbsp;
          </td>
          <td align=left>
          <input onClick=\"doChallengeResponse(); return false;\" type=submit name=submitbtn value=\"Login now\">
        </td>
        
         </tr>
        ";
        
        if ($simplewire_ID) {
            print "
            <tr>
            <td colspan=2>
                <p>
            
            If you make use of <b>O</b>ne <b>T</b>ime <b>P</b>asswords:
        
            <ul class=s>
                <li>Fill in your username
                <li>Press the Send OTP button
                <li>Collect the password
                <li>Fill it in the password field
                <li>Press the Login Now button to login
            </ul>
            <input type=submit name=sendotp value=\"Send OTP\">
            </td>
            </tr>
            ";
        }
        
        print "
        </table>
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

		        $query="select * from spam where ip = '$_SERVER[REMOTE_ADDR]'";
    			$spam->query($query);

                if (!$spam->num_rows()) {
                    $query=sprintf("insert into spam (ip,tries,login,stamp)
                    values ('%s','1','%s','%s')
                    ",$_SERVER[REMOTE_ADDR],addslashes($username),time());
                } else {
                    $query=sprintf("update spam set
                    tries = tries +1 where ip = '%s'", $_SERVER[REMOTE_ADDR]);
                }
                $spam->query($query);
            
            } else {
                print "Please fill in your One Time Password!";
            }
        }
        
        print "
        </table>
        </form>
        ";
    }
 
    function showHeader($title='') {
    }
 
    function showTopMenu($title='') {
        global $DATASOURCES, $CDRTool, $cdr_source, $perm;
        $loginName=$CDRTool['loginName'];
    
        $version=trim(file_get_contents('version'));
        print "<table width=100% cellpadding=5 CELLSPACING=0 border=5 align=center>
        <tr>
        ";

        if (is_readable("local/images/logo.gif")) {
        	print "<td valign=middle><img src=\"local/images/logo.gif\"></td>";
        } else if (is_readable("local/images/logo.jpg")) {
        	print "<td valign=middle><img src=\"local/images/logo.jpg\"></td>";
        } else if (is_readable("local/images/logo.png")) {
        	print "<td valign=middle><img src=\"local/images/logo.png\"></td>";
        } else {
            $this->hasAGProjectslogo=1;
	    $logo=$CDRTool['tld']."/images/CDRTool.gif";
            print "<td>";
            print "<a href=http://ag-projects.com target=agprojects><img src=$logo border=0></a>";
            print "</td>";
        }

        print "
        <td width=100%>
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
            print " <a href=callsearch.phtml>CDRs</a> ";
        }
        if ($perm->have_perm("rates")) {
            print " | <a href=rating_tables.phtml>Rating tables</a>";
        }
        
        if ($perm->have_perm("provisioning")) {
            print " | <a href=provisioning.phtml>SIP and ENUM</a>";
        }
        
        if ($perm->have_perm("statistics")) {
            print " | <a href=status/sip_online.phtml target=siponline>SIP registrar</a>";
            print " | <a href=status/media_sessions.phtml target=mediasessions>Media sessions</a>";
            print " | <a href=status/usage/index.phtml target=usage>Statistics</a>";
        }

        print " | <a href=log.phtml>Logs</a>";
        print " | <a href=accounts.phtml>Accounts</a>";
        
        $now_print=Date("Y-m-d H:i:s",time());
        $tz=$CDRTool['provider']['timezone'];
        print " | $now_print | <a href=doc/changelog target=changelog>v. $version</a>";
        print "
        </td>
        <td align=right>
        ";
        printf ("<a href=logout.phtml target=_top><b>Logout %s</b></a>",$loginname);
        print "
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <p>
        ";
    }

    function showTopMenuSubscriber($title="") {
        global $DATASOURCES, $CDRTool, $cdr_source, $perm;
    
        $loginName=$CDRTool['loginName'];
    
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
        printf ("<a href=logout.phtml target=_top><b>Logout %s</b></a>",$loginname);
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
        <center>
        <a href=http://ag-projects.com target=agprojects><img src=images/CDRTool.gif border=0></a>
        <p>
        <h2>Terms and conditions</h2>
        <p>
        <form action=callsearch.phtml method=post>
        <textarea name=legal rows=20 cols=60 wrap=virtual readonly=yes>$legalNotice</textarea>
        <p>
        You are logged in as $loginname ";
        if ($loginName) print "($loginName). ";
        print "<p>
        If you agree with the Terms and Conditions, <br>
        press on <b>I agree</b> button to continue.
        <p>
        <input type=submit value=\"I agree\">
        <input type=hidden name=previous_page value=license_page>
        </form>
        ";
    }   
 
    function showFooter() {
        global $CDRTool;
        if (!$CDRTool['filter']['aNumber'] && !$this->hasAGProjectslogo) {
            $thisYear=date("Y",time());
            print "
            <p>
            <table width=100% border=0>
            <tr>
            <td align=right>
            <a href=http://ag-projects.com target=agprojects><img src=images/PoweredbyAGProjects.gif border=0>
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

function changeLanguage() {
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
            dprint("$Number is an international number");
            return $m[1];
        } else if (preg_match($this->regexp_national,$Number,$m)) {
            // Add default country code
            dprint("$Number is a national number");
            return $this->CountryCode.$m[1];
        } else if (strlen($this->ENUMtldRegexp)) {
            $_regexp="/^".$this->ENUMtldRegexp."\$/";
            if (preg_match($_regexp,$Number,$m)) {
            	dprint("$Number is an ENUM number that matched $_regexp");
                return $m[1];
            } else {
                dprint ("No match for $_regexp");
            }
        } 
        return false;
    }
}

?>
