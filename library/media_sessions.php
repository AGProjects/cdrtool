<?
class MediaSessions {

    var $dispatcher_port   = 25061;
    var $sessions          = array();
    var $summary           = array();
    var $domain_statistics = array();
    var $timeout           = 3;

    function MediaSessions ($dispatcher='',$allowedDomains=array(),$filters=array()) {

        $this->dispatcher     = $dispatcher;
        $this->filters        = $filters;
        $this->allowedDomains = $allowedDomains;
        $this->getUserAgentPictures();

    }

    function getUserAgentPictures (){
        global $userAgentImages;
        global $userAgentImagesFile;

        if (!isset($userAgentImagesFile)) {
            $userAgentImagesFile="phone_images.php";
        }

        require_once($userAgentImagesFile);

        $this->userAgentImages = $userAgentImages;
    }

    function connectSocket() {
        if (!strlen($this->dispatcher)) return false;

        if (preg_match("/^(tls|tcp):(.*):(.*)$/",$this->dispatcher,$m)) {
            $hostname  = $m[1].'://'.$m[2];
            $port      = $m[3];
            $target= $m[1].'://'.$m[2].':'.$m[3];

		    $this->mp_tls_cert_file  = '/etc/cdrtool/mediaproxy.'.$m[2].'.pem';

            if ($m[1] == 'tls') {
                if (!file_exists($this->mp_tls_cert_file)) {
                    printf ("<p><font color=red>Error: mediaproxy certificate file %s does not exist. </font>\n",$this->mp_tls_cert_file);
                    return false;
                }

                $tls_options=array('ssl' => array('local_cert'        => $this->mp_tls_cert_file));

                $context=stream_context_create($tls_options);
            } else {
                $context=stream_context_create(array());
            }

        } else {
            printf ("<p><font color=red>Error: MediaProxy dispatcher '%s' must be in the form: tls:hostname:port or tcp:hostname:port</font>",$this->dispatcher);
            return false;
        }

        if ($fp = stream_socket_client ($target, $errno, $errstr,$this->timeout,STREAM_CLIENT_CONNECT,$context)) {
            return $fp;
        } else {
            printf ("<p><font color=red>Error connecting to %s: %s (%s) </font>\n",$target,$errstr,$errno);
                                 return false;
        }
    }

    function fetchSessionFromNetwork() {
    	// get sessions from MediaProxy2 dispatcher

        if (!$fp = $this->connectSocket()) return array();

        fputs($fp, "sessions\r\n");
        $line = fgets($fp);
        return json_decode($line);
    }

    function fetchSummaryFromNetwork() {
    	// get summary from MediaProxy2 dispatcher

        if (count($this->allowedDomains)) return array();
        if (!$fp = $this->connectSocket()) return array();

        fwrite($fp, "summary\r\n");

        $line = fgets($fp);
        fclose($fp);
        return json_decode($line,true);
    }

    function getSessions () {
        $_sessions=$this->fetchSessionFromNetwork();

        if (count($this->allowedDomains)) {
            foreach ($_sessions as $_session) {

                list($user1,$domain1)=explode("@",$_session->from_uri);
                list($user2,$domain2)=explode("@",$_session->to_uri);

                if (preg_match("/^(.*):/",$domain1,$m)) $domain1=$m[1];

                if (!in_array($domain1,$this->allowedDomains) && !in_array($domain2,$this->allowedDomains)) {
                    continue;
                }

                $this->domain_statistics[$domain1]['sessions']++;
                $this->domain_statistics['total']['sessions']++;
                
                foreach ($_session->streams as $streamInfo) {

                    list($relay_ip,$relay_port)=explode(":",$streamInfo->caller_local);
                    $_relay_statistics[$relay_ip]['stream_count'][$streamInfo->media_type]++;;

                    if ($_session->duration) {
                    	$session_bps=($streamInfo->caller_bytes+$streamInfo->callee_bytes)/$_session->duration*8;
                    	$_relay_statistics[$relay_ip]['bps_relayed'] = $_relay_statistics[$relay_ip]['bps_relayed'] + $session_bps;
                    }

                    $this->domain_statistics[$domain1]['caller'] = $this->domain_statistics[$domain1]['caller'] + intval($streamInfo->caller_bytes/$_session->duration*2);
                    $this->domain_statistics['total']['caller']=$this->domain_statistics['total']['caller']+intval($streamInfo->caller_bytes/$_session->duration*2);
                    $this->domain_statistics[$domain1]['callee']=$this->domain_statistics[$domain1]['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
                    $this->domain_statistics['total']['callee']=$this->domain_statistics['total']['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
                }

                $_relay_statistics[$relay_ip]['session_count']++;

                $_sessions2[] = $_session;
            }
        } else {
            foreach ($_sessions as $_session) {
                list($user1,$domain1)=explode("@",$_session->from_uri);
                list($user2,$domain2)=explode("@",$_session->to_uri);
                if (preg_match("/^(.*):/",$domain1,$m)) $domain1=$m[1];

                $this->domain_statistics[$domain1]['sessions']++;
                $this->domain_statistics['total']['sessions']++;

                foreach ($_session->streams as $streamInfo) {
                    if ($_session->duration) {
                        $this->domain_statistics[$domain1]['caller']=$this->domain_statistics[$domain1]['caller']+intval($streamInfo->caller_bytes/$_session->duration*2);
                        $this->domain_statistics['total']['caller']=$this->domain_statistics['total']['caller']+intval($streamInfo->caller_bytes/$_session->duration*2);
                        $this->domain_statistics[$domain1]['callee']=$this->domain_statistics[$domain1]['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
                        $this->domain_statistics['total']['callee']=$this->domain_statistics['total']['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
                    }
                }

            }

            $_sessions2 = $_sessions;
        }

        if (count($this->allowedDomains)) {
            foreach (array_keys($_relay_statistics) as $_ip) {
                $this->relay_statistics[]=array('ip'            => $_ip,
                                                'bps_relayed'   => $_relay_statistics[$_ip]['bps_relayed'],
                                                'session_count' => $_relay_statistics[$_ip]['session_count'],
                                                'stream_count'  => $_relay_statistics[$_ip]['stream_count'],
                                                'status'        => 'ok',
                                                'uptime'        => 'unknown'
                                                );
            }
        }

        if (strlen($this->filters['user'])) {
            foreach ($_sessions2 as $_session) {
                $user=$this->filters['user'];

                if (preg_match("/$user/",$_session->from_uri) ||
                    preg_match("/$user/",$_session->to_uri)) {
                    $this->sessions[] = $_session;
                }
            }
        } else {
        	$this->sessions = $_sessions2;
        }

    }

    function getSummary () {
        if (count($this->allowedDomains)){
        	if (is_array($this->relay_statistics)) {
        	   $this->summary = $this->relay_statistics;
                }
        } else {
            $this->summary = $this->fetchSummaryFromNetwork();
        }
    }

    function showSearch() {
        printf ("<form method=post action=%s>
        <input type=text name=user value='%s'>
        <input type=submit value='Search callers'>
        <p>
        ",
        $_SERVER['PHP_SELF'],
        $_REQUEST['user']
        );
    }

    function showHeader() {
        print "
        <html>
        <head>
          <title>Media sessions</title>
        </head>
        
        <body marginwidth=20 leftmargin=20 link=#000066 vlink=#006666 bgcolor=white>
        <style type=\"text/css\">
        <!--
        
        .border {
            border: 1px solid #999999;
            border-collapse: collapse;
        }
        
        .bordertb {
            border-top: 1px solid #999999;
            border-bottom: 1px solid #999999;
            border-collapse: collapse;
        }
        
        body {
            font-family: Verdana, Sans, Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: gray;
        }
        
        p {
            font-family: Verdana, Sans, Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: gray;
        }
        
        pre {
            font-family: Lucida Console, Courier;
            font-size: 10pt;
            color: black;
        }
        
        td {
            font-family: Verdana, Sans, Arial, Helvetica, sans-serif;
            font-size: 8pt;
            vertical-align: top;
            color: #444444;
        }
        
        th {
            font-family: Verdana, Sans, Arial, Helvetica, sans-serif;
            font-size: 8pt;
            vertical-align: bottom;
            color: black;
        }
        
        -->
        </style>
        ";
    }

    function showFooter() {
    }

    function showAll() {

        $this->showHeader();

        $this->showSearch();

        $this->showSummary();

        $this->showSessions();

        $this->showFooter();
    }

    function showSummary() {

		$this->getSummary();

        if (!count($this->summary)) return;

        if (count($this->allowedDomains)) {

            print "
            <table border=0 class=border cellpadding=2 cellspacing=0>
              <tr bgcolor=#c0c0c0 class=border align=right>
                <th class=bordertb width=10px></th>
                <th class=bordertb width=10px></th>
                <td><b>Address</b></td>
                <th class=bordertb width=10px></b></td>
                <td><b>Relayed traffic</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Sessions</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Streams</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Status</b></td>
              </tr>";
        } else {
            print "
            <table border=0 class=border cellpadding=2 cellspacing=0>
              <tr bgcolor=#c0c0c0 class=border align=right>
                <th class=bordertb width=10px></th>
                <th class=bordertb width=10px></th>
                <td><b>Address</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Version</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Uptime</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Relayed traffic</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Sessions</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Streams</b></td>
                <th class=bordertb width=10px></th>
                <td><b>Status</b></td>
              </tr>";
        }
        $i = 1;

        foreach ($this->summary as $relay) {

            unset($media_types);
            unset($streams);

			$media_types=count($relay['stream_count']);

            if ($media_types > 1) {
                $streams = "<table border=0>";
    
                foreach (array_keys($relay['stream_count']) as $key) {
                    $streams .= sprintf("<tr><td>%s</td><td>%s</td></tr>",$key,$relay['stream_count'][$key]);
                }
    
                $streams .= "</table>";
            } else {
                foreach (array_keys($relay['stream_count']) as $key) {
                    $streams=sprintf("%s %s",$key,$relay['stream_count'][$key]);
                }
            }

        	if (count($this->allowedDomains)) {
                
                printf ("
                  <tr class=border align=right>
                    <td class=border>%d</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%d</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb valign=top>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                  </tr>",
                  $i,
                  $this->ip2host($relay['ip']),
                  $this->normalizeTraffic($relay['bps_relayed']),
                  $relay['session_count'],
                  $streams,
                  ucfirst($relay['status'])
                  );
            } else {
                printf ("
                  <tr class=border align=right>
                    <td class=border>%d</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%d</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb valign=top>%s</td>
                    <td class=bordertb width=10px></td>
                    <td class=bordertb>%s</td>
                  </tr>",
                  $i,
                  $this->ip2host($relay['ip']),
                  $relay['version'],
                  $this->normalizeTime($relay['uptime']),
                  $this->normalizeTraffic($relay['bps_relayed']),
                  $relay['session_count'],
                  $streams,
                  ucfirst($relay['status'])
                  );
            }
            $i++;
        }

        print "
        </table>
        <br />
        ";
    }

    function showSessions () {
        if (!count($this->sessions)) return;
           print "
           <table border=0 cellpadding=2 cellspacing=0 class=border>
            <tr valign=bottom bgcolor=black>
             <th rowspan=2>&nbsp;</th>
             <th rowspan=2><font color=white>Callers</font></th>
             <th rowspan=2 colspan=2><font color=white>Phones</font></th>
             <th colspan=10 bgcolor=#393939><font color=white>Media Streams</font></th>
            </tr>
            <tr valign=bottom bgcolor=#afafaf>
             <th class=border><nobr>Caller address</nobr></th>
             <th class=border>Relay caller</th>
             <th class=border>Relay callee</th>
             <th class=border><nobr>Callee address</nobr></th>
             <th class=border>Status</th>
             <th class=border>Codec</th>
             <th class=border>Type</th>
             <th class=border>Duration</th>
             <th class=border>Bytes<br>Caller</th>
             <th class=border>Bytes<br>Called</th>
            </tr>";
    
            $i = 1;
            foreach ($this->sessions as $session) {
                $from = $session->from_uri;
                $to   = $session->to_uri;
                $fromAgent = $session->caller_ua;
                $toAgent   = $session->callee_ua;
                $fromImage = $this->getImageForUserAgent($fromAgent);
                $toImage = $this->getImageForUserAgent($toAgent);
                $sc = count($session->streams);

                print "
                <tr valign=top class=border>
                 <td class=border rowspan=$sc>$i</td>
                 <td class=border rowspan=$sc>
                   <nobr><b>From:</b> $from</nobr><br>
                   <nobr><b>To:</b> $to</nobr><br>
                 </td>
                 <td class=border rowspan=$sc align=center>
                   <img src=\"images/30/$fromImage\"
                        alt=\"$fromAgent\"
                        title=\"$fromAgent\"
                        ONMOUSEOVER='window.status=\"$fromAgent\";'
                        ONMOUSEOUT='window.status=\"\";'
                        border=0
                   />
                 </td>
                 <td class=border rowspan=$sc align=center>
                   <img src=\"images/30/$toImage\"
                        alt=\"$toAgent\"
                        title=\"$toAgent\"
                        ONMOUSEOVER='window.status=\"$toAgent\";'
                        ONMOUSEOUT='window.status=\"\";'
                        border=0
                   />
                 </td>";

                    $duration = $this->normalizeTime($session->duration);

                    foreach ($session->streams as $streamInfo) {
                        $status   = $streamInfo->status;

                        if ($status=="idle" || $status=='hold') {
                            $idletime = $this->normalizeTime($streamInfo->timeout_wait);
                            $status = sprintf("%s %s", $status, $idletime);
                        }

                        $caller = $streamInfo->caller_remote;
                        $callee = $streamInfo->callee_remote;
                        $relay_caller  = $streamInfo->caller_local;
                        $relay_callee  = $streamInfo->callee_local;

                        $codec  = $streamInfo->caller_codec;
                        $type   = $streamInfo->media_type;

                        if ($caller == '?.?.?.?:?') {
                            $caller = '&#150;';  // a dash
                            $align1 = 'center';
                        } else {
                            $align1 = 'left';
                        }
                        if ($callee == '?.?.?.?:?') {
                            $callee = '&#150;';  // a dash
                            $align2 = 'center';
                        } else {
                            $align2 = 'left';
                        }
                        if ($codec == 'Unknown')
                            $codec = '&#150;';   // a dash
                        if ($type == 'Unknown')
                            $type = '&#150;';    // a dash
                        $bytes_in1 = $this->normalizeBytes($streamInfo->caller_bytes);
                        $bytes_in2 = $this->normalizeBytes($streamInfo->callee_bytes);
                        print "
          <td class=border align=$align1>$caller</td>
          <td class=border align=left>$relay_caller</td>
          <td class=border align=left>$relay_callee</td>
          <td class=border align=$align2>$callee</td>

          <td class=border align=center><nobr>$status</nobr></td>
          <td class=border align=center>$codec</td>
          <td class=border align=center>$type</td>
          <td class=border align=right>$duration</td>
          <td class=border align=right>$bytes_in1</td>
          <td class=border align=right>$bytes_in2</td>
         </tr>";
                    }
                    $i++;
            }
            print "
         </table>
         <br />";

    }

    function normalizeBytes($bytes) {
        $mb = $bytes/1024/1024.0;
        $kb = $bytes/1024.0;
        if ($mb >= 0.95) {
            return sprintf("%.2fM", $mb);
        } else if ($kb >= 1) {
            return sprintf("%.2fk", $kb);
        } else {
            return sprintf("%d", $bytes);
        }
    }
    
    function normalizeTime($period) {
        $sec = $period % 60;
        $min = floor($period/60);
        $h   = floor($min/60);
        $min = $min % 60;
    
        if ($h >= 1) {
            return sprintf('%dh%02d\'%02d"', $h, $min, $sec);
        } else {
            return sprintf('%d\'%02d"', $min, $sec);
        }
    }
    
    function normalizeTraffic($traffic) {
        // input is in bytes/second
        $mb = $traffic/1024/1024.0;
        $kb = $traffic/1024.0;
        if ($mb >= 0.95) {
            return sprintf("%.2fMbps", $mb);
        } else if ($kb >= 1) {
            return sprintf("%.2fkbps",$kb);
        } else {
            return sprintf("%dbps",$traffic);
        }
    }
    
    function getImageForUserAgent($agent) {
    
        foreach ($this->userAgentImages as $agentRegexp => $image) {
            if (preg_match("/$agentRegexp/i", $agent)) {
                return $image;
            }
        }
    
        return "unknown.png";
    }

    function ip2host($ip) {
        return $ip;
    }
}

class MediaSessionsNGNPro extends MediaSessions {
    // get Media session from NGNPro

    function MediaSessionsNGNPro($engineId,$allowedDomains=array(),$filters=array()) {

        if (!strlen($engineId)) return false;

        $this->soapEngineId   = $engineId;
        $this->dispatcher     = $dispatcher;
        $this->filters        = $filters;
        $this->allowedDomains = $allowedDomains;

        $this->getUserAgentPictures();

        require("/etc/cdrtool/ngnpro_engines.inc");
        require_once("ngnpro_soap_library.php");

        if (!strlen($this->soapEngineId)) return false;
        if (!$soapEngines[$this->soapEngineId]) return false;

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
        } else {
            $this->hostnames=array();
        }
    }

    function fetchSessionFromNetwork() {
    	if (!is_object($this->soapclient)) return false;

        $this->soapclient->addHeader($this->SoapAuth);
        $result     = $this->soapclient->getMediaSessions();

        if (PEAR::isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            printf("<font color=red>Error from %s: %s: %s</font>",$this->SOAPurl,$error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return json_decode($result);

    }

    function fetchSummaryFromNetwork() {
        if (!is_object($this->soapclient)) return array();
    
        $this->soapclient->addHeader($this->SoapAuth);
        $result     = $this->soapclient->getMediaSummary();
    
        if (PEAR::isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();
    
            printf("<font color=red>Error from %s: %s: %s</font>",$this->SOAPurl,$error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return array();
        }
    
        return json_decode($result,true);
	}

    function ip2host($ip) {
		if ($this->hostnames[$ip]) {
            return $this->hostnames[$ip];
        } else {
        	return $ip;
        }
    }

}
?>
