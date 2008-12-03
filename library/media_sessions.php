<?
class MediaSessions {

    /*
       connect to a mediaproxy dispatcher
       get media sessions and display them

    */

    var $dispatcher_port = 25061;
    var $sessions        = array();
    var $relays          = array();
    var $timeout         = 3;

    function MediaSessions ($dispatcher='',$allowedDomains=array(),$filters=array()) {

        if (!strlen($dispatcher)) return false;

        global $userAgentImages;
        require_once("phone_images.php");
        $this->userAgentImages = $userAgentImages;

        $this->filters = $filters;
        $this->allowedDomains  = $allowedDomains;

        list($ip,$port) = explode(":",$dispatcher);

        $this->dispatcher_ip = $ip;

        if ($port) $this->dispatcher_port = $port;

        return $this->getSessions();

    }

    function getSessions () {

        if (!$this->dispatcher_ip) return false;
        if (!$this->dispatcher_port) return false;

        if ($fp = fsockopen ($this->dispatcher_ip, $this->dispatcher_port, $errno, $errstr, $this->timeout)) {
            printf ("<p>Connected to MediaProxy2 dispatcher %s:%s",$this->dispatcher_ip, $this->dispatcher_port);

            if (!count($this->allowedDomains)) {
               fputs($fp, "summary\r\n");
   
               while (!feof($fp)) {
                   $line  = fgets($fp);
   
                   if (preg_match("/^\r\n/",$line)) {
                       break;
                   }
   
                   $this->relays[] = json_decode($line);
               }
            }

            fputs($fp, "sessions\r\n");

            while (!feof($fp)) {
                $line = fgets($fp);

                if (preg_match("/^\r\n/",$line)) {
                    break;
                }

                $line=json_decode($line);

                if (count($this->allowedDomains)) {
                    list($user1,$domain1)=explode("@",$line->from_uri);
                    list($user2,$domain2)=explode("@",$line->to_uri);
                    if (!in_array($domain1,$this->allowedDomains) && !in_array($domain2,$this->allowedDomains)) {
                        continue;
                    }
                }

                if (strlen($this->filters['user'])) {
                    $user=$this->filters['user'];
                    if (preg_match("/$user/",$line->from_uri) ||
                        preg_match("/$user/",$line->to_uri)
                        ) {
                        $this->sessions[] = $line;
                    }

                } else {
                    $this->sessions[] = $line;
                }

            }

            fclose($fp);
            return true;

        } else {
            printf ("<p><font color=red>Error connecting to %s:%s: %s (%s) </font>\n",$this->dispatcher_ip,$this->dispatcher_port,$errstr,$errno);
            return false;
        }
    }

    function showSearch() {
        if (!count($this->sessions)) return;
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

    function show() {

        $this->showHeader();

        print "<h3>Media sessions</h3>";

        $this->showSearch();

        if (!count($this->allowedDomains)) {
            $this->showRelays();
        }

        $this->showSessions();

        $this->showFooter();
    }

    function showRelays() {
        if (!count($this->sessions)) return;

        print "
        <table border=0 class=border cellpadding=2 cellspacing=0>
          <tr bgcolor=#c0c0c0 class=border align=right>
            <th class=bordertb width=10px></th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Address</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Version</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Uptime</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Relayed traffic</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Sessions</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Streams</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Status</th>
          </tr>";
    
        $i = 1;

        foreach ($this->relays as $relay) {

            unset($media_types);

            foreach ($relay->stream_count as $key => $value) {
                $media_types++;
            }

            if ($media_types > 1) {
                $streams = "<table border=0>";
    
                foreach ($relay->stream_count as $key => $value) {
                    $streams .= sprintf("<tr><td>%s</td><td>%s</td></tr>",$key,$value);
                }
    
                $streams .= "</table>";
            } else {
                foreach ($relay->stream_count as $key => $value) {
                    $streams=sprintf("%s %s",$key,$value);
                }
            }

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
              $relay->ip,
              $relay->version,
              $this->normalizeTime($relay->uptime),
              $this->normalizeTraffic($relay->bps_relayed),
              $relay->session_count,
              $streams,
              ucfirst($relay->status)
              );

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
}

class MediaSessions1 {
    function MediaSessions1 ($servers=array(),$allowedDomains=array()) {
        $this->servers = $servers;
        $this->allowedDomains  = $allowedDomains;
        global $userAgentImages;
        require_once("phone_images.php");
        $this->userAgentImages = $userAgentImages;
    }

    function isDomainAllowed($from,$to) {
        $els = explode("@",$from);
        $fromDomain = $els[1];
        $els = explode("@",$to);
        $toDomain = $els[1];
    
        if (count($this->allowedDomains)) {
            if (in_array($fromDomain,$this->allowedDomains) || in_array($toDomain,$this->allowedDomains)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
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
        $traffic = $traffic * 8;
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
    
    function getRTPSessions($ip, $port) {
        if ($fp = fsockopen ($ip, $port, $errno, $errstr, "3") ) {
            fputs($fp, "status\n");
            $proxy      = array('status' => 'Ok');
            $crtSession = 'None';
            while (!feof($fp)) {
                $line = fgets($fp, 2048);
                $elements = explode(" ", $line);
                if ($elements[0] == 'version' && count($elements)==2) {
                    $proxy['version'] = $elements[1];
                } else if ($elements[0] == 'proxy' && count($elements)==3) {
                    $proxy['sessionCount'] = $elements[1];
                    $traffic = explode("/", $elements[2]);
                    $proxy['traffic'] = array('caller'  => $traffic[0],
                                              'called'  => $traffic[1],
                                              'relayed' => $traffic[2]);
                    $proxy['sessions'] = array();
                } else if ($elements[0]=='session' && count($elements)==7) {
                    if ($this->isDomainAllowed($elements[2],$elements[3])) {
                        $crtSession = $elements[1];
                        $info = array('from' => $elements[2],
                                      'to'   => $elements[3],
                                      'fromAgent' => quoted_printable_decode($elements[4]),
                                      'toAgent'   => quoted_printable_decode($elements[5]),
                                      'duration'  => $elements[6],
                                      'streams'   => array());
                        $proxy['sessions'][$crtSession] = $info;
                        $allowed_session=1;
                    } else {
                        unset($allowed_session);
                    }
                } else if ($elements[0] == 'stream' && count($elements)==9) {
                    if (!$allowed_session) continue;
                    $stream = array('caller'   => $elements[1],
                                    'called'   => $elements[2],
                                    'via'      => $elements[3],
                                    'bytes'    => explode("/", $elements[4]),
                                    'status'   => $elements[5],
                                    'codec'    => $elements[6],
                                    'type'     => $elements[7],
                                    'idletime' => $elements[8]);
                    $proxy['sessions'][$crtSession]['streams'][] = $stream;
                } else {
                    //print "Invalid line: '$line'<br>\n";
                }
            }
            fclose($fp);
    
            if (!isset($proxy['version'])) {
                if ($fp = fsockopen ($ip, $port, $errno, $errstr, "2") ) {
                    fputs($fp, "version\n");
                    $line = fgets($fp, 2048);
                    $version = trim($line);
                    if (!$version)
                        $version = 'unknown';
                    $proxy['version'] = $version;
                    fclose($fp);
                }
            }
    
            return $proxy;
         } else {
            return array('status' => "<font color=red>$errstr</font>");
         }
    }
    
    function haveSessions() {
        foreach ($this->servers as $server) {
            if ($this->sessions[$server]['sessionCount'] > 0) {
                return True;
            }
        }
    
        return False;
    }
    
    function showSummary() {
        // IE seems to ignore border on <tr> elements
        // that's why we used bordertb on <th> and <td>
        print "
        <table border=0 class=border cellpadding=2 cellspacing=0>
          <tr bgcolor=#c0c0c0 class=border align=right>
            <th class=bordertb width=10px></th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Server</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Version</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Caller traffic</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Called traffic</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Relayed traffic</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Sessions</th>
            <th class=bordertb width=10px></th>
            <th class=bordertb>Status</th>
          </tr>";
    
        $i = 1;
        foreach ($this->servers as $server) {
            list($ip, $port) = explode(":", $server);
            $sessionInfo = $this->sessions[$server];
            $status = $sessionInfo['status'];
            if ($status=='Ok')
                $version = $sessionInfo['version'];
            else
                $version = "&nbsp;";
            if ($status!='Ok' || $sessionInfo['sessionCount'] == 0) {
                $caller  = "&nbsp;";
                $called  = "&nbsp;";
                $relayed = "&nbsp;";
                $sessionCount = "&nbsp;";
            } else {
                $caller  = $this->normalizeTraffic($sessionInfo['traffic']['caller']);
                $called  = $this->normalizeTraffic($sessionInfo['traffic']['called']);
                $relayed = $this->normalizeTraffic($sessionInfo['traffic']['relayed']);
                $sessionCount = $sessionInfo['sessionCount'];
            }
            print "
          <tr class=border align=right>
            <td class=border>$i</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$ip</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$version</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$caller</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$called</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$relayed</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb>$sessionCount</td>
            <td class=bordertb width=10px></td>
            <td class=bordertb><nobr>$status</nobr></td>
          </tr>";
            $i++;
        }
        
        print "
        </table>
        <br />
        ";
    }
    
    function showSessions() {
    
        if ($this->haveSessions($this->servers, $sessions)) {
            print "
        <table border=0 cellpadding=2 cellspacing=0 class=border>
         <tr valign=bottom bgcolor=black>
          <th rowspan=2>&nbsp;</th>
          <th rowspan=2><font color=white>Call</font></th>
          <th rowspan=2 colspan=2><font color=white>Phones</font></th>
          <th colspan=10 bgcolor=#393939><font color=white>Media Streams</font></th>
         </tr>
         <tr valign=bottom bgcolor=#afafaf>
          <th class=border><nobr>Caller address</nobr></th>
          <th class=border><nobr>Called address</nobr></th>
          <th class=border>Via address</th>
          <th class=border>Status</th>
          <th class=border>Codec</th>
          <th class=border>Type</th>
          <th class=border>Duration</th>
          <th class=border>Bytes<br>Caller</th>
          <th class=border>Bytes<br>Called</th>
          <th class=border>Bytes<br>Relayed</th>
         </tr>";
    
            $i = 1;
            foreach ($this->servers as $server) {
                $serverSessions = $this->sessions[$server]['sessions'];
                foreach ($serverSessions as $id => $sessionInfo) {
                    $sc = count($sessionInfo['streams']);
                    $from = $sessionInfo['from'];
                    $to   = $sessionInfo['to'];
                    $fromAgent = $sessionInfo['fromAgent'];
                    $toAgent = $sessionInfo['toAgent'];
                    $fromImage = $this->getImageForUserAgent($fromAgent);
                    $toImage = $this->getImageForUserAgent($toAgent);
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
                    $duration = $this->normalizeTime($sessionInfo['duration']);
                    foreach ($sessionInfo['streams'] as $streamInfo) {
                        $status   = $streamInfo['status'];
                        if ($status=="idle" || $status=='hold') {
                            $idletime = $this->normalizeTime($streamInfo['idletime']);
                            $status = sprintf("%s %s", $status, $idletime);
                        }
                        $caller = $streamInfo['caller'];
                        $called = $streamInfo['called'];
                        $via    = $streamInfo['via'];
                        $codec  = $streamInfo['codec'];
                        $type   = $streamInfo['type'];
                        if ($caller == '?.?.?.?:?') {
                            $caller = '&#150;';  // a dash
                            $align1 = 'center';
                        } else {
                            $align1 = 'left';
                        }
                        if ($called == '?.?.?.?:?') {
                            $called = '&#150;';  // a dash
                            $align2 = 'center';
                        } else {
                            $align2 = 'left';
                        }
                        if ($codec == 'Unknown')
                            $codec = '&#150;';   // a dash
                        if ($type == 'Unknown')
                            $type = '&#150;';    // a dash
                        $bytes_in1 = $this->normalizeBytes($streamInfo['bytes'][0]);
                        $bytes_in2 = $this->normalizeBytes($streamInfo['bytes'][1]);
                        $bytes_rel = $this->normalizeBytes($streamInfo['bytes'][2]);
                        print "
          <td class=border align=$align1>$caller</td>
          <td class=border align=$align2>$called</td>
          <td class=border align=left>$via</td>
          <td class=border align=center><nobr>$status</nobr></td>
          <td class=border align=center>$codec</td>
          <td class=border align=center>$type</td>
          <td class=border align=right>$duration</td>
          <td class=border align=right>$bytes_in1</td>
          <td class=border align=right>$bytes_in2</td>
          <td class=border align=right>$bytes_rel</td>
         </tr>";
                    }
                    $i++;
                }
            }
            print "
         </table>
         <br />";
        }
    
    }

    function showHeader() {
        print "
        <html>
        <head>
         <title>Media Sessions</title>
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
        
        <h2>Media Sessions</h2>

        ";
    }

    function show() {
        $this->sessions = array();
    
        foreach ($this->servers as $server) {
            list($ip, $port) = explode(":", $server);
            if (!$port)
                $port = "25060";
            $this->sessions[$server] = $this->getRTPSessions($ip, $port);
        }

        print "<h3>Media sessions</h3>";

        $this->showSessions();

        if (!count($this->allowedDomains)) {
            $this->showSummary();
        }
    }
}

?>
