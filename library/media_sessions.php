<?php
class MediaSessions {

    public $dispatcher_port   = 25061;
    public $sessions          = array();
    public $summary           = array();
    public $domain_statistics = array();
    public $timeout           = 3;

    public function __construct($dispatcher = '', $allowedDomains = array(), $filters = array())
    {
        $this->dispatcher     = $dispatcher;
        $this->filters        = $filters;
        $this->allowedDomains = $allowedDomains;
        $this->getUserAgentPictures();
    }

    public function getUserAgentPictures()
    {
        global $userAgentImages;
        global $userAgentImagesFile;

        if (!isset($userAgentImagesFile)) {
            $userAgentImagesFile = "phone_images.php";
        }

        require_once($userAgentImagesFile);

        $this->userAgentImages = $userAgentImages;
    }

    private function connectSocket()
    {
        if (!strlen($this->dispatcher)) {
            return false;
        }

        if (preg_match("/^(tls|tcp):(.*):(.*)$/", $this->dispatcher, $m)) {
            $hostname  = $m[1].'://'.$m[2];
            $port      = $m[3];
            $target= 'tcp://'.$m[2].':'.$m[3];
            $transport= $m[1];

            $this->mp_tls_cert_file  = '/etc/cdrtool/mediaproxy.'.$m[2].'.pem';

            if ($m[1] == 'tls') {
                if (!file_exists($this->mp_tls_cert_file)) {
                    printf(
                        "<p><font color=red>Error: mediaproxy certificate file %s does not exist. </font>\n",
                        $this->mp_tls_cert_file
                    );
                    return false;
                }

                $tls_options=array('ssl' => array('local_cert'        => $this->mp_tls_cert_file));

                $context = stream_context_create($tls_options);
            } else {
                $context = stream_context_create(array());
            }
        } else {
            printf(
                "<p><font color=red>Error: MediaProxy dispatcher '%s' must be in the form: tls:hostname:port or tcp:hostname:port</font>",
                $this->dispatcher
            );
            return false;
        }

        if ($fp = stream_socket_client($target, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT, $context)) {
            if ($transport == "tls") {
                if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_SSLv3_CLIENT)) {
                    printf("<p><font color=red>Error connecting to %s: (Could not enable crypto) </font>\n", $target);
                    return false;
                }
            }
            return $fp;
        } else {
            printf("<p><font color=red>Error connecting to %s: %s (%s) </font>\n", $target, $errstr, $errno);
            return false;
        }
    }

    public function fetchSessionFromNetwork()
    {
        // get sessions from MediaProxy2 dispatcher

        if (!$fp = $this->connectSocket()) {
            return array();
        }

        fputs($fp, "sessions\r\n");
        $line = fgets($fp);
        return json_decode($line);
    }

    public function fetchSummaryFromNetwork()
    {
        // get summary from MediaProxy2 dispatcher

        if (count($this->allowedDomains)) {
            return array();
        }
        if (!$fp = $this->connectSocket()) {
            return array();
        }

        fwrite($fp, "summary\r\n");

        $line = fgets($fp);
        fclose($fp);
        return json_decode($line, true);
    }

    public function getSessions()
    {
        $_sessions = $this->fetchSessionFromNetwork();

        if (count($this->allowedDomains)) {
            $this->domain_statistics['total'] = array(
                'sessions' => 0,
                'caller'   => 0,
                'callee'   => 0
            );

            foreach ($_sessions as $_session) {
                list($user1, $domain1) = explode("@", $_session->from_uri);
                list($user2, $domain2) = explode("@", $_session->to_uri);

                if (preg_match("/^(.*):/", $domain1, $m)) {
                    $domain1 = $m[1];
                }

                $may_display = false;
                foreach ($this->allowedDomains as $allow_domain) {
                    if ($this->endsWith($domain1, $allow_domain)) {
                        $may_display = true;
                        break;
                    }
                    if ($this->endsWith($domain2, $allow_domain)) {
                        $may_display = true;
                        break;
                    }
                }
                if (!$may_display) {
                    continue;
                }

                if (!array_key_exists($domain1, $this->domain_statistics)) {
                    $this->domain_statistics[$domain1] = array(
                        'sessions' => 0,
                        'caller'   => 0,
                        'callee'   => 0
                    );
                }
                $this->domain_statistics[$domain1]['sessions']++;
                $this->domain_statistics['total']['sessions']++;

                foreach ($_session->streams as $streamInfo) {
                    list($relay_ip, $relay_port) = explode(":", $streamInfo->caller_local);
                    $_relay_statistics[$relay_ip]['stream_count'][$streamInfo->media_type]++;

                    if ($_session->duration) {
                        $session_bps =($streamInfo->caller_bytes + $streamInfo->callee_bytes) / $_session->duration * 8;
                        $_relay_statistics[$relay_ip]['bps_relayed'] = $_relay_statistics[$relay_ip]['bps_relayed'] + $session_bps;
                    }

                    $this->domain_statistics[$domain1]['caller'] = $this->domain_statistics[$domain1]['caller'] + intval($streamInfo->caller_bytes / $_session->duration * 2);
                    $this->domain_statistics['total']['caller'] = $this->domain_statistics['total']['caller'] + intval($streamInfo->caller_bytes / $_session->duration * 2);
                    $this->domain_statistics[$domain1]['callee'] = $this->domain_statistics[$domain1]['callee'] + intval($streamInfo->callee_bytes / $_session->duration * 2);
                    $this->domain_statistics['total']['callee'] = $this->domain_statistics['total']['callee'] + intval($streamInfo->callee_bytes/$_session->duration * 2);
                }

                $_relay_statistics[$relay_ip]['session_count']++;

                $_sessions2[] = $_session;
            }
        } else {
            $this->domain_statistics['total'] = array(
                'sessions' => 0,
                'caller'   => 0,
                'callee'   => 0
            );
            foreach ($_sessions as $_session) {
                list($user1, $domain1) = explode("@", $_session->from_uri);
                list($user2, $domain2) = explode("@", $_session->to_uri);
                if (preg_match("/^(.*):/", $domain1, $m)) {
                    $domain1=$m[1];
                }

                if (!array_key_exists($domain1, $this->domain_statistics)) {
                    $this->domain_statistics[$domain1]= array(
                        'sessions' => 0,
                        'caller'   => 0,
                        'callee'   => 0
                    );
                }
                $this->domain_statistics[$domain1]['sessions']++;
                $this->domain_statistics['total']['sessions']++;

                foreach ($_session->streams as $streamInfo) {
                    if ($_session->duration) {
                        $this->domain_statistics[$domain1]['caller'] = $this->domain_statistics[$domain1]['caller']+intval($streamInfo->caller_bytes/$_session->duration*2);
                        $this->domain_statistics['total']['caller'] = $this->domain_statistics['total']['caller']+intval($streamInfo->caller_bytes/$_session->duration*2);
                        $this->domain_statistics[$domain1]['callee'] = $this->domain_statistics[$domain1]['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
                        $this->domain_statistics['total']['callee'] = $this->domain_statistics['total']['callee']+intval($streamInfo->callee_bytes/$_session->duration*2);
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

                if (preg_match("/$user/", $_session->from_uri) ||
                    preg_match("/$user/", $_session->to_uri)) {
                    $this->sessions[] = $_session;
                }
            }
        } else {
            $this->sessions = $_sessions2;
        }
    }

    public function getSummary()
    {
        if (count($this->allowedDomains)) {
            if (is_array($this->relay_statistics)) {
                $this->summary = $this->relay_statistics;
            }
        } else {
            $this->summary = $this->fetchSummaryFromNetwork();
        }
    }

    public function showSearch()
    {
        printf(
            "
            <div class='pull-right' id='session_search'><form method=post class='form-inline' action=%s>
            <div class='input-append'>
                <input class=span2 type=text name=user placeholder=\"Search for callers\" value='%s'>
                <button class='btn btn-primary' type=submit><i class='icon-search'></i></button>
            </div>
            </form></div>
            ",
            $_SERVER['PHP_SELF'],
            isset($_REQUEST['user']) ? $_REQUEST['user'] : ''
        );

        print "<script type=\"text/javascript\">
            $(document).ready(function() {
                console.log($('#session_search'));
                    $('#session_search').detach().appendTo('#sessions_title');
                });</script>";
    }

    public function showHeader()
    {
        print "
        <html>
        <head>
          <title>Media sessions</title>
        </head>

        <body marginwidth=20 leftmargin=20 link=#000066 vlink=#006666 bgcolor=white>
        ";
    }

    public function showFooter()
    {
    }

    public function showAll()
    {
        $this->showHeader();
        if (!$this->allowedDomains) {
            $this->showSummary();
        }
        $this->showSearch();
        $this->showSessions();
        $this->showFooter();
    }

    public function showSummary()
    {
        $this->getSummary();

        if (!count($this->summary)) {
            return;
        }

        if (count($this->allowedDomains)) {
            print "<div class=row>
            <table class='span10 table table-striped table-condesed'>
              <thead>
              <tr>
                <th width=10px></th>
                <th>
                Address</td>
                <th>
                Relayed traffic</th>
                <th>
                Sessions</th>
                <th>
                Streams</th>
                <th>
                Status</th>
              </tr></thead>";
        } else {
            print "<div class=row>
            <table class='span10 table table-striped table-condensed'>
            <thead>
              <tr>
                <th width=10px></th>
                <th>
                Address</td>
                <th>
                Version</td>
                <th>
                 Uptime</th>
                <th>
                Relayed traffic</th>
                <th>
                Sessions</th>
                <th>
                Streams</th>
                <th>
                Status</th>
              </tr></thead>";
        }
        $i = 1;

        foreach ($this->summary as $relay) {
            unset($media_types);
            unset($streams);

            $streams = '';
            $media_types=count($relay['stream_count']);

            if ($media_types > 1) {
                foreach (array_keys($relay['stream_count']) as $key) {
                    $streams .= sprintf("%s %s, ", $key, $relay['stream_count'][$key]);
                }
                $streams=chop($streams, ', ');
            } else {
                foreach (array_keys($relay['stream_count']) as $key) {
                    $streams=sprintf("%s %s", $key, $relay['stream_count'][$key]);
                }
            }
            $rClass= 'label-success';
            if ($relay['status'] == 'halting') {
                $rClass = 'label-warning';
            }
            $relayStatus = sprintf("<span class=\"label %s\">%s</span>", $rClass, ucfirst($relay['status']));

            if (count($this->allowedDomains)) {
                printf(
                    "
                    <tr>
                        <td>%d</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%d</td>
                        <td valign=top>%s</td>
                        <td>%s</td>
                    </tr>",
                    $i,
                    $this->ip2host($relay['ip']),
                    $this->normalizeTraffic($relay['bps_relayed']),
                    $relay['session_count'],
                    $streams,
                    $relayStatus
                );
            } else {
                printf(
                    "
                    <tr>
                        <td>%d</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%d</td>
                        <td valign=top>%s</td>
                        <td>%s</td>
                    </tr>",
                    $i,
                    $this->ip2host($relay['ip']),
                    $relay['version'],
                    $this->normalizeTime($relay['uptime']),
                    $this->normalizeTraffic($relay['bps_relayed']),
                    $relay['session_count'],
                    $streams,
                    $relayStatus
                );
            }
            $i++;
        }

        print "
        </table></div>
        ";
    }

    public function showSessions()
    {
        print "<h2 id='sessions_title'>Sessions</h2>";
        if (!count($this->sessions)) {
            return;
        }

        print "
            <table id='sessions' class='table-bordered table-condensed table'>
            <thead>
                <tr valign=bottom>
                    <th rowspan=2>Callers (".count($this->sessions).")</th>
                    <th rowspan=2 colspan=2>Phones</th>
                    <th colspan=10>Media Streams</th>
                </tr>
                <tr valign=bottom>
                    <th><nobr>Caller address</nobr></th>
                    <th>Relay caller</th>
                    <th>Relay callee</th>
                    <th><nobr>Callee address</nobr></th>
                    <th>Status</th>
                    <th>Type/Codec</th>
                    <th>Duration</th>
                    <th>Bytes<br>Caller</th>
                    <th>Bytes<br>Called</th>
                </tr>
            </thead>";

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
                <tr>
                 <td style='height: 39px' rowspan=$sc>
                   <nobr><b>From:</b> $from</nobr><br>
                   <nobr><b>To:</b> $to</nobr><br>
                 </td>
                 <td rowspan=$sc style='text-align:center; vertical-align:middle'>
                 ";
            if ($fromImage == 'unknown.png') {
                print "<i style=\"font-size:28px\" class=\"icon-question\"
                        title=\"$fromAgent\"
                        ONMOUSEOVER='window.status=\"$fromAgent\";'
                        ONMOUSEOUT='window.status=\"\";'></i>";
            } elseif ($fromImage == 'asterisk.png') {
                print "<i style=\"font-size:25px\" class=\"icon-asterisk\"
                        title=\"$fromAgent\"
                        ONMOUSEOVER='window.status=\"$fromAgent\";'
                        ONMOUSEOUT='window.status=\"\";'></i>";
            } else {
                print "
                   <img src=\"images/30/$fromImage\"
                        alt=\"$fromAgent\"
                        title=\"$fromAgent\"
                        ONMOUSEOVER='window.status=\"$fromAgent\";'
                        ONMOUSEOUT='window.status=\"\";'
                        border=0
                        style='max-height:30px; max-width:30px;'
                        />";
            }
            print "
                 </td>
                 <td rowspan=$sc style='text-align:center; vertical-align:middle'>";
            if ($toImage == 'unknown.png') {
                print "<i style=\"font-size:28px\" class=\"icon-question\"
                        title=\"$toAgent\"
                        ONMOUSEOVER='window.status=\"$toAgent\";'
                        ONMOUSEOUT='window.status=\"\";'></i>";
            } elseif ($toImage == 'asterisk.png') {
                print "<i style=\"font-size:25px\" class=\"icon-asterisk\"
                        title=\"$toAgent\"
                        ONMOUSEOVER='window.status=\"$toAgent\";'
                        ONMOUSEOUT='window.status=\"\";'></i>";
            } else {
                print "
                   <img src=\"images/30/$toImage\"
                        alt=\"$toAgent\"
                        title=\"$toAgent\"
                        ONMOUSEOVER='window.status=\"$toAgent\";'
                        ONMOUSEOUT='window.status=\"\";'
                        border=0
                        style='max-height:30px; max-width: 30px'
                        />";
            }
            print "</td>";
            $duration = $this->normalizeTime($session->duration);
            if (count($session->streams) > 0) {
                foreach ($session->streams as $streamInfo) {
                    $status   = $streamInfo->status;
                    $statusClass = "";

                    if ($status=="idle" || $status=='hold') {
                        $idletime = $this->normalizeTime($streamInfo->timeout_wait);
                        $status = sprintf("%s %s", $status, $idletime);
                    } else if ($status == "closed") {
                        $statusClass = "muted";
                    }

                    $caller = $streamInfo->caller_remote;
                    $callee = $streamInfo->callee_remote;
                    $relay_caller  = $streamInfo->caller_local;
                    $relay_callee  = $streamInfo->callee_local;

                    if (substr_count($relay_caller, ":") == 1) {
                        // Probaly ipv4
                        $relay_caller_data = explode(":", $relay_caller);
                        $relay_caller = $this->ip2host($relay_caller_data[0]).":".$relay_caller_data[1];
                    }

                    if (substr_count($relay_callee, ":") == 1) {
                        // Probaly ipv4
                        $relay_callee_data = explode(":", $relay_callee);
                        $relay_callee = $this->ip2host($relay_callee_data[0]).":".$relay_callee_data[1];
                    }
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
                    if ($codec == 'Unknown') {
                        $codec = '&#150;';   // a dash
                    } else {
                        $codec  = "<span class=\"label label-info\">$codec</span>";
                    }
                    if ($type == 'Unknown') {
                        $type = '&#150;';    // a dash
                    } elseif ($type == 'video') {
                        $type  = "<span class=\"badge badge-success\">$type</span>";
                    } elseif ($type == 'audio') {
                        $type  = "<span class=\"badge badge-info\">$type</span>";
                    } else {
                        $type  = "<span class=\"badge\">$type</span>";
                    }
                    $bytes_in1 = $this->normalizeBytes($streamInfo->caller_bytes);
                    $bytes_in2 = $this->normalizeBytes($streamInfo->callee_bytes);
                    print "
                        <td class=\"$statusClass\"   align=$align1>$caller</td>
                        <td class=\"$statusClass\">$relay_caller</td>
                        <td class=\"$statusClass\">$relay_callee</td>
                        <td class=\"$statusClass\"  align=$align2>$callee</td>

                        <td class=\"$statusClass\" style='text-align: center;'><nobr>$status</nobr></td>
                        <td class=\"$statusClass\"><nobr>$type $codec</nobr></td>
                        <td class=\"$statusClass\" style='text-align:right;'>$duration</td>
                        <td class=\"$statusClass\" style='text-align:right;'>$bytes_in1</td>
                        <td class=\"$statusClass\" style='text-align:right;'>$bytes_in2</td>
                        </tr>";
                }
            } else {
                print "<td colspan='9'>&nbsp;</td></tr>";
            }
            $i++;
        }
        print "
            </table>
            <br />";
    }

    private function normalizeBytes($bytes)
    {
        $mb = $bytes /1024 /1024.0;
        $kb = $bytes /1024.0;
        if ($mb >= 0.95) {
            return sprintf("%.2fM", $mb);
        } elseif ($kb >= 1) {
            return sprintf("%.2fk", $kb);
        } else {
            return sprintf("%d", $bytes);
        }
    }

    private function normalizeTime($period)
    {
        $sec = $period % 60;
        $min = floor($period / 60);
        $h   = floor($min / 60);
        $d   = floor($h / 24);
        $min = $min % 60;
        $h   = $h % 24;

        if ($d >= 1) {
            return sprintf('%dd %dh %02d\' %02d"', $d, $h, $min, $sec);
        } else if ($h >= 1) {
            return sprintf('%dh %02d\' %02d"', $h, $min, $sec);
        } else {
            return sprintf('%d\' %02d"', $min, $sec);
        }
    }

    private function normalizeTraffic($traffic)
    {
        // input is in bits/second
        $mb = $traffic / 1024 / 1024.0;
        $gb = $traffic/ 1024 / 1024 / 1024;
        $kb = $traffic / 1024.0;
        if ($gb >= 0.95) {
            return sprintf("%.2f Gbit/s", $gb);
        } elseif ($mb >= 0.95) {
            return sprintf("%.2f Mbit/s", $mb);
        } elseif ($kb >= 1) {
            return sprintf("%.2f Kbit/s", $kb);
        } elseif ($traffic == 0) {
            return $traffic;
        } else {
            return sprintf("%d bit/s", $traffic);
        }
    }

    public function getImageForUserAgent($agent)
    {
        foreach ($this->userAgentImages as $agentRegexp => $image) {
            if (preg_match("/$agentRegexp/i", $agent)) {
                return $image;
            }
        }

        return "unknown.png";
    }

    public function ip2host($ip)
    {
        return $ip;
    }
}

class MediaSessionsNGNPro extends MediaSessions {
    // get Media session from NGNPro

    public function __construct($engineId, $allowedDomains = array(), $filters = array())
    {

        if (!strlen($engineId)) {
            return false;
        }

        $this->soapEngineId   = $engineId;
        $this->filters        = $filters;
        $this->allowedDomains = $allowedDomains;

        $this->getUserAgentPictures();

        require("/etc/cdrtool/ngnpro_engines.inc");
        require_once("ngnpro_soap_library.php");

        if (!strlen($this->soapEngineId)) {
            return false;
        }
        if (!$soapEngines[$this->soapEngineId]) {
            return false;
        }

        $this->SOAPlogin = array(
                               "username"    => $soapEngines[$this->soapEngineId]['username'],
                               "password"    => $soapEngines[$this->soapEngineId]['password'],
                               "admin"       => true
                               );

        $this->SOAPurl=$soapEngines[$this->soapEngineId]['url'];

        $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

        // Instantiate the SOAP client
        $this->soapclient = new WebService_NGNPro_SipPort($this->SOAPurl);

        $this->soapclient->setOpt('curl', CURLOPT_TIMEOUT, 5);
        $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if (is_array($soapEngines[$this->soapEngineId]['hostnames'])) {
            $this->hostnames=$soapEngines[$this->soapEngineId]['hostnames'];
        } else {
            $this->hostnames=array();
        }
    }

    public function fetchSessionFromNetwork()
    {
        if (!is_object($this->soapclient)) {
            return false;
        }

        $this->soapclient->addHeader($this->SoapAuth);
        $result     = $this->soapclient->getMediaSessions();

        if ((new PEAR)->isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            printf(
                "<font color=red>Error from %s: %s: %s</font>",
                $this->SOAPurl,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }

        return json_decode($result);
    }

    public function fetchSummaryFromNetwork()
    {
        if (!is_object($this->soapclient)) {
            return array();
        }

        $this->soapclient->addHeader($this->SoapAuth);
        $result     = $this->soapclient->getMediaSummary();

        if ((new PEAR)->isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            printf(
                "<font color=red>Error from %s: %s: %s</font>",
                $this->SOAPurl,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return array();
        }
        return json_decode($result, true);
    }

    public function ip2host($ip)
    {
        if ($this->hostnames[$ip]) {
            return $this->hostnames[$ip];
        } else {
            return $ip;
        }
    }

    public function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}
?>
