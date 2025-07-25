<?php

class SIPTrace
{
    public $enableThor  = false;
    public $trace_array = array();
    public $traced_ip   = array();
    public $SIPProxies  = array();
    public $mediaTrace  = false;
    public $thor_nodes  = array();
    public $hostnames   = array();
    public $proxyGroups = array();
    private $cdr_source;

    public function __construct($cdr_source)
    {
        global $DATASOURCES, $auth;
        require_once 'errors.php';

        $this->cdr_source = $cdr_source;
        $this->cdrtool    = new DB_CDRTool();

        if (!is_array($DATASOURCES[$this->cdr_source])) {
            $log = sprintf("Error: datasource '%s' is not defined\n", $this->cdr_source);
            print $log;
            throw new DataSourceUndefinedError($log);
            return 0;
        }

        if (strlen($DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->enableThor = $DATASOURCES[$this->cdr_source]['enableThor'];
        }

        if (strlen($DATASOURCES[$this->cdr_source]['mediaTrace'])) {
            $this->mediaTrace = $DATASOURCES[$this->cdr_source]['mediaTrace'];
        }

        if (is_array($DATASOURCES[$this->cdr_source]['proxyGroups'])) {
            $this->proxyGroups = $DATASOURCES[$this->cdr_source]['proxyGroups'];
        }

        if ($this->enableThor) {
            require '/etc/cdrtool/ngnpro_engines.inc';
            require_once 'ngnpro_soap_library.php';
            if ($DATASOURCES[$this->cdr_source]['soapEngineId'] && in_array($DATASOURCES[$this->cdr_source]['soapEngineId'], array_keys($soapEngines))) {
                $this->soapEngineId=$DATASOURCES[$this->cdr_source]['soapEngineId'];

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
                }
            } else {
                printf("<p><font color=red>Error: soapEngineID not defined in datasource %s</font>", $this->cdr_source);
                return false;
            }
        } else {
            $this->table             = $DATASOURCES[$this->cdr_source]['table'];
            $db_class                = $DATASOURCES[$this->cdr_source]['db_class'];
            $this->purgeRecordsAfter = $DATASOURCES[$this->cdr_source]['purgeRecordsAfter'];

            if (class_exists($db_class)) {
                $this->db                = new $db_class;
            } else {
                printf("<p><font color=red>Error: database class '%s' is not defined</font>", $db_class);
                return false;
            }
        }

        if (is_object($auth)) {
            $this->isAuthorized=1;
        }

        if (is_array($DATASOURCES[$this->cdr_source]['SIPProxies'])) {
            $this->SIPProxies = $DATASOURCES[$this->cdr_source]['SIPProxies'];
        }
    }

    private function isProxy($ip, $sip_proxy = '')
    {
        if (!$ip) {
            return false;
        }

        if (!$this->enableThor) {
            if (!is_array($this->SIPProxies)) {
                return false;
            }

            if (in_array($ip, array_keys($this->SIPProxies))) {
                return true;
            }
        } elseif ($sip_proxy) {
            if (isset($this->thor_nodes[$ip])) {
                return true;
            } else {
                if (isThorNode($ip, $sip_proxy) || isThorNode($ip, $sip_proxy, 'msteams_gateway')) {
                    $this->thor_nodes[$ip] = 1;
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    private function getTrace($proxyIP, $callid, $fromtag, $totag)
    {
        if ($this->enableThor) {
            // get trace using soap request
            if (!$proxyIP || !$callid || !$fromtag) {
                return false;
            }

            global $DATASOURCES;
            if (is_array($DATASOURCES[$this->cdr_source]['proxyTranslation_IP'])
                && isset($DATASOURCES[$this->cdr_source]['proxyTranslation_IP'][$proxyIP])
                && strlen($DATASOURCES[$this->cdr_source]['proxyTranslation_IP'][$proxyIP])
            ) {
                $proxyIP = $DATASOURCES[$this->cdr_source]['proxyTranslation_IP'][$proxyIP];
            }
            if (!is_object($this->soapclient)) {
                print "Error: soap client is not defined.";
                return false;
            }

            $filter = array(
                'nodeIp'  => $proxyIP,
                'callId'  => $callid,
                'fromTag' => $fromtag,
                'toTag'   => $totag
            );
            $this->soapclient->addHeader($this->SoapAuth);

            $result = $this->soapclient->getSipTrace($filter);

            if ((new PEAR)->isError($result)) {
                $error_msg   = $result->getMessage();
                $error_fault = $result->getFault();
                $error_code  = $result->getCode();

                printf(
                    "
                    <div style='display: flex; align-items: center; justify-content: center;'>
                        <div class='span10' style='padding-top:40px;'>
                            <div class='alert alert-danger'><h4>Error from %s</h4><br/>%s (%s)</div>
                        </div>
                    </div>
                    ",
                    $this->SOAPurl,
                    $error_fault->detail->exception->errorstring,
                    $error_fault->detail->exception->errorcode
                );
                return false;
            }

            $columns = 0;

            $traces = json_decode($result);

            $trace_array = array();

            foreach ($traces as $_trace) {
                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/", $_trace->to_ip, $m)) {
                    $toip      = $m[2];
                    $transport = $m[1];
                    $toport    = $m[3];
                } elseif (preg_match("/^(.*):(.*)$/", $_trace->to_ip, $m)) {
                    $toip      = $m[1];
                    $transport = 'udp';
                    $toport    = $m[2];
                } else {
                    $toip      = $_trace->to_ip;
                    $transport = $_trace->to_proto;
                    $toport    = $_trace->to_port;
                }

                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/", $_trace->from_ip, $m)) {
                    $fromip    = $m[2];
                    $fromport  = $m[3];
                } elseif (preg_match("/^(.*):(.*)$/", $_trace->from_ip, $m)) {
                    $fromip    = $m[1];
                    $fromport  = $m[2];
                } else {
                    $fromip    = $_trace->from_ip;
                    $fromport  = $_trace->from_port;
                }

                if (isset($this->proxyGroups[$fromip])) {
                    $fromip = $this->proxyGroups[$fromip];
                }

                if (isset($this->proxyGroups[$toip])) {
                    $toip = $this->proxyGroups[$toip];
                }

                if (!isset($this->column[$fromip])) {
                    $this->column[$fromip] = $columns + 1;
                    $this->column_port[$fromip] = $fromport;
                    $columns++;
                }

                if (!isset($this->column[$toip])) {
                    $this->column[$toip]   = $columns+1;
                    $this->column_port[$toip] = $toport;
                    $columns++;
                }

                preg_match("/^(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/", $_trace->time_stamp, $m);
                $timestamp = mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);

                $idx=$proxyIP.'_'.$_trace->id;

                $trace_array[$idx] = array (
                    'id'         => $idx,
                    'direction'  => $_trace->direction,
                    'fromip'     => $fromip,
                    'toip'       => $toip,
                    'fromport'   => $fromport,
                    'toport'     => $toport,
                    'method'     => $_trace->method,
                    'transport'  => $transport,
                    'date'       => $_trace->time_stamp,
                    'status'     => $_trace->status,
                    'timestamp'  => $timestamp,
                    'msg'        => $_trace->message,
                    'md5'        => md5($_trace->message)
                );
            }

            $this->trace_array=$trace_array;
            $this->rows = count($this->trace_array);
        } else {
            // get trace from SQL

            if (!is_object($this->db)) {
                print "<p><font color=red>Error: no database connection defined</font>";
                return false;
            }

            $query = sprintf(
                "
                select
                    *,
                    UNIX_TIMESTAMP(time_stamp) as timestamp
                from
                    %s
                where
                    callid = '%s'
                order by id asc
                ",
                addslashes($this->table),
                addslashes($callid)
            );

            if (!$this->db->query($query)) {
                printf("Database error for query %s: %s (%s)", $query, $this->db->Error, $this->db->Errno);
                return false;
            }

            $this->rows = $this->db->num_rows();

            $columns = 0;

            while ($this->db->next_record()) {
                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/", $this->db->f('toip'), $m)) {
                    $toip      = $m[2];
                    $transport = $m[1];
                    $toport    = $m[3];
                } elseif (preg_match("/^(.*):(.*)$/", $this->db->f('toip'), $m)) {
                    $toip      = $m[1];
                    $transport = 'udp';
                    $toport    = $m[2];
                } else {
                    $toip = $this->db->f('toip');
                    $toport    = '5060';
                }

                if (preg_match("/^(udp|tcp|tls):(.*):(.*)$/", $this->db->f('fromip'), $m)) {
                    $fromip   = $m[2];
                    $fromport = $m[3];
                } elseif (preg_match("/^(.*):(.*)$/", $this->db->f('fromip'), $m)) {
                    $fromip   = $m[1];
                    $fromport = $m[2];
                } else {
                    $fromip = $this->db->f('fromip');
                    $transport = 'udp';
                    $fromport = '5060';
                }

                if (!$this->column[$fromip]) {
                    $this->column[$fromip]=$columns+1;
                    $this->column_port[$fromip]=$fromport;
                    $columns++;
                }

                if (!$this->column[$toip]) {
                    $this->column[$toip] = $columns + 1;
                    $this->column_port[$toip]=$toport;
                    $columns++;
                }

                $this->trace_array[$this->db->f('id')] =
                    array (
                        'id'        => $this->db->f('id'),
                        'direction' => $this->db->f('direction'),
                        'fromip'    => $fromip,
                        'toip'      => $toip,
                        'method'    => $this->db->f('method'),
                        'fromport'  => $fromport,
                        'toport'    => $toport,
                        'transport' => $transport,
                        'date'      => $this->db->f('time_stamp'),
                        'status'    => $this->db->f('status'),
                        'timestamp' => $this->db->f('timestamp'),
                        'msg'       => $this->db->f('msg'),
                        'md5'       => md5($this->db->f('msg'))
                    );
            }
        }
    }

    private function printLabelProtocolPort($transport, $port)
    {
        echo '<span class="label">';
        if ($transport == 'tls') {
            echo "<span><i class='icon-lock'></i></span>&nbsp;";
        }
        printf('%s: %d', strtoupper($transport), $port);
        echo '</span>';
    }

    public function show($proxyIP, $callid, $fromtag, $totag)
    {
        $action           = $_REQUEST['action'];
        $toggleVisibility = $_REQUEST['toggleVisibility'];

        if ($action == 'toggleVisibility') {
            $this->togglePublicVisibility($callid, $fromtag, $toggleVisibility);
        }

        if ($_SERVER['HTTPS'] == "on") {
            $protocolURL = "https://";
        } else {
            $protocolURL = "http://";
        }

        $this->getTrace($proxyIP, $callid, $fromtag, $totag);

        /* No trace can be found */

        if (!count($this->trace_array)) {
            echo "
                <div style='display: flex; align-items: center; justify-content: center;'>
                    <div class='span10' style='padding-top:40px;'>
                        <p class='alert'>SIP trace for session id <strong>$callid</strong> is not available.</p>
                    </div>
                </div>
            ";
            return;
        }

        echo "
            <div class=container-fluid>
                <div id=trace class=main>
                    <h1 class='page-header'>CDRTool SIP trace<br /><small>Call ID: $callid $authorize</small></h1>
                        <div class=row-fluid>
                            <div class=span9>
        ";
        $basicURL = $protocolURL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $fullURL = $basicURL;
        print "URLs for this trace: <a href=$fullURL>HTML</a> | <a href=$fullURL&format=text>TEXT</a></td>";

        if ($this->mediaTrace) {
            $media_query = array(
                'cdr_source'    => $this->mediaTrace,
                'callid'        => $callid,
                'fromtag'       => $fromtag,
                'totag'         => $totag,
                'proxyIP'       => $proxyIP
            );
            $this->mediaTraceLink = sprintf(
                "<p class=pull-right>
                    <a href=\"javascript:void(null);\" onClick=\"return window.open('media_trace.phtml?%s', 'mediatrace','toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=730')\">
                    Click here for RTP media information
                    </a>
                </p>",
                http_build_query($media_query)
            );
        }

        print "
            </div>
            <div class='span3'>
                <p class='pull-right'>Click on each packet to expand its body content</p>
                $this->mediaTraceLink
            </div>
        </div>
        ";

        foreach (array_keys($this->trace_array) as $key) {
            $this->trace_array[$key]['isProxy'] = 0;

            if ($this->trace_array[$key]['direction'] == 'in') {
                if (is_array($this->SIPProxies)) {
                    $thisIP=explode(":", $this->trace_array[$key]['fromip']);
                    if ($this->isProxy($thisIP[0], $proxyIP)) {
                        $this->trace_array[$key]['isProxy'] = 1;
                    }
                }

                $this->trace_array[$key]['msg_possition']   = $this->column[$this->trace_array[$key]['toip']];
                $this->trace_array[$key]['arrow_possition'] = $this->column[$this->trace_array[$key]['fromip']];
                $this->trace_array[$key]['arrow_direction'] = $arrow_direction;

                // handle self-generated BYE
                if ($this->trace_array[$key]['fromip'] == $this->trace_array[$key]['toip']) {
                    if ($this->trace_array[$key]['method'] == 'BYE') {
                        $bye_ip = $this->trace_array[$key]['fromip'];
                        $bye_lines = preg_split('/\n|\r\n?/', $this->trace_array[$key]['msg']);

                        $bye_line = $bye_lines[0];
                        $fi=$this->trace_array[$key]['fromip'];
                        $fp=$this->trace_array[$key]['fromport'];
                        $ti=$this->trace_array[$key]['toip'];
                        $tp=$this->trace_array[$key]['toport'];

                        if (preg_match("/^BYE (sip:|sips:)(.*)\@(.*):(\d*)(.*)$/", $bye_line, $m)) {
                            $bye_ip = $m[3];
                            $bye_port = $m[4];
                            if ($this->column[$bye_ip]) {
                                $this->trace_array[$key]['fromip'] = $bye_ip;
                                $this->trace_array[$key]['fromport'] = $bye_port;
                                $this->trace_array[$key]['arrow_possition'] = $this->column[$bye_ip];
                            } else {
                                $found_bye_ip = false;
                                foreach ($bye_lines as $_line) {
                                    if (preg_match("/^Route:(.*)$/", $_line, $mr)) {
                                        $line = str_replace(array('<', '>'), "", $mr[1]);
                                        $routes = explode(",", $line);
                                        foreach ($routes as $r) {
                                            if (preg_match("/(.*sip:|sips:)(.*):(\d+)(.*)$/", $r, $mm)) {
                                                $bye_ip = $mm[2];
                                                $bye_port = $mm[3];
                                                if ($this->column[$bye_ip]){
                                                    $this->trace_array[$key]['fromip'] = $bye_ip;
                                                    $this->trace_array[$key]['fromport'] = $bye_port;
                                                    $this->trace_array[$key]['arrow_possition'] = $this->column[$bye_ip];
                                                    $found_bye_ip = true;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($found_bye_ip) {
                                            break;
                                        }
                                    }
                                }
                            }
                        } else {
                            $arrow_direction = "loop";
                        }

                        if ($this->column[$this->trace_array[$key]['fromip']] < $this->column[$proxyIP]) {
                            $arrow_direction = "left";
                        } else {
                            $arrow_direction = "right";
                        }
                    } else {
                        $arrow_direction = "loop";
                    }
                } elseif ($this->column[$this->trace_array[$key]['fromip']] < $this->column[$this->trace_array[$key]['toip']]) {
                    $arrow_direction = "right";
                } else {
                    $arrow_direction = "left";
                }

                $this->trace_array[$key]['arrow_direction'] = $arrow_direction;
            } else {
                if ($this->trace_array[$key]['fromip'] == $this->trace_array[$key]['toip']) {
                    $arrow_direction = "loop";
                } elseif ($this->column[$this->trace_array[$key]['fromip']] < $this->column[$this->trace_array[$key]['toip']]) {
                    $arrow_direction = "right";
                } else {
                    $arrow_direction = "left";
                }

                $this->trace_array[$key]['msg_possition']   = $this->column[$this->trace_array[$key]['fromip']];
                $this->trace_array[$key]['arrow_possition'] = $this->column[$this->trace_array[$key]['toip']];
                $this->trace_array[$key]['arrow_direction'] = $arrow_direction;
            }
        }
        echo "
            <table class='table siptrace'>
            <thead>
                <tr>
                    <th>Packet</th>
                    <th>Time</th>
        ";

        $_seen_timeline = array();
        foreach (array_keys($this->column) as $_key) {
            $IPels = explode(":", $_key);

            if (isset($this->hostnames[$IPels[0]])) {
                $_hostname = $this->hostnames[$IPels[0]];
            } else {
                $_hostname = $_key;
            }

            print "<th style='text-align: center' colspan=\"2\">";
            if ($proxyIP != $IPels[0] && $this->isProxy($IPels[0], $proxyIP)) {
                $trace_query = array(
                    'cdr_source'    => $this->cdr_source,
                    'callid'        => $callid,
                    'fromtag'       => $fromtag,
                    'totag'         => $totag,
                    'proxyIP'       => $IPels[0]
                );
                $trace_link = sprintf(
                    "<a href=\"javascript:void(null);\" onClick=\"return window.open('sip_trace.phtml?%s', '_self',
                    'toolbar=0,status=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600')\">%s:%s</a>",
                    http_build_query($trace_query),
                    $_hostname,
                    $this->column_port[$_key]
                );
                printf("%s", $trace_link);
            } else {
                printf("%s", $_hostname);
            }
            print "</th>";
        }

        print "</tr>
            </thead>";

        /* Rows */

        $i=0;
        foreach (array_keys($this->trace_array) as $key) {
            $i++;

            $id        = $this->trace_array[$key]['id'];
            $msg       = $this->trace_array[$key]['msg'];
            $fromip    = $this->trace_array[$key]['fromip'];
            $toip      = $this->trace_array[$key]['toip'];
            $date      = substr($this->trace_array[$key]['date'], 11);
            $status    = $this->trace_array[$key]['status'];
            $direction = $this->trace_array[$key]['direction'];
            $timestamp = $this->trace_array[$key]['timestamp'];
            $method    = $this->trace_array[$key]['method'];
            $isProxy   = $this->trace_array[$key]['isProxy'];
            $transport = $this->trace_array[$key]['transport'];

            $msg_possition   = $this->trace_array[$key]['msg_possition'];
            $arrow_possition = $this->trace_array[$key]['arrow_possition'];
            $arrow_direction = $this->trace_array[$key]['arrow_direction'];
            $md5             = $this->trace_array[$key]['md5'];

            if ($i == 1) {
                $begin_timestamp = $timestamp;
            }
            $timeline = $timestamp - $begin_timestamp;

            $sip_phone_img = getImageForUserAgent($msg);

            if ($seen_msg[$md5]) {
                continue;
            }

            $SIPclass = substr($status, 0, 1);
            switch ($SIPclass) {
                case 6:
                    $status_color = "red";
                    break;
                case 5:
                    $status_color = "red";
                    break;
                case 4:
                    $status_color = "red";
                    break;
                case 3:
                    $status_color = "green";
                    break;
                case 2:
                    $status_color = "green";
                    break;
                case 1:
                    $status_color = "orange";
                    break;
                default:
                    $status_color = "blue";
                    if ($method == "ACK") {
                        $status_color = 'cyan';
                    } elseif ($method == "CANCEL") {
                        $status_color = 'magenta';
                    }
                    break;
            }

            $_lines = explode("\n", $msg);

            if (preg_match("/^(.*) SIP/", $_lines[0], $m)) {
                $_lines[0] = $m[1];
            } elseif (preg_match("/^SIP\/2\.0 (.*)/", $_lines[0], $m)) {
                $_lines[0] = $m[1];
            }

            unset($media);
            unset($diversions);

            $media_index=0;
            $search_ice=0;
            $search_ip=0;
            $contact_header='';

            foreach ($_lines as $_line) {
                if (preg_match("/^(Diversion: ).*;(.*)$/", $_line, $m)) {
                    $diversions[]=$m[1].$m[2];
                }

                if (preg_match("/^Cseq:\s*\d+\s*(.*)$/i", $_line, $m)) {
                    $status_for_method=$m[1];
                }

                if (preg_match("/^c=IN \w+ ([\d|\w\.]+)/i", $_line, $m)) {
                    $media['ip']=$m[1];
                }

                if (preg_match("/^m=(\w+) (\d+) /i", $_line, $m)) {
                    $media_index++;
                    $search_ice=1;
                    $search_ip=1;
                    $media['streams'][$media_index] = array(
                        'type' => $m[1],
                        'ip'   => $media['ip'],
                        'port' => $m[2],
                        'ice'  => ''
                    );
                }

                if ($search_ip && preg_match("/^c=IN \w+ ([\d|\w\.]+)/i", $_line, $m)) {
                    $media['streams'][$media_index]['ip']=$m[1];
                    $search_ip=0;
                }

                if ($search_ice && preg_match("/^a=ice/i", $_line, $m)) {
                    $media['streams'][$media_index]['ice']="ICE";
                    $search_ice=0;
                }
            }


            $_els = explode(";", $_lines[0]);

            $cell_content = "<div id=\"packet$i\"><span>$_els[0]</span>";

            if ($status) {
                $cell_content .= " <font color=black>for ".$status_for_method."</font>";
            }

            if (is_array($diversions)) {
                foreach ($diversions as $_diversion) {
                    $cell_content.="<br><em class='gray'>$_diversion</em>";
                }
            }

            if (is_array($media['streams'])) {
                foreach (array_keys($media['streams']) as $_key) {
                    $_stream = sprintf(
                        "%s: %s:%s %s",
                        $media['streams'][$_key]['type'],
                        $media['streams'][$_key]['ip'],
                        $media['streams'][$_key]['port'],
                        $media['streams'][$_key]['ice']
                    );
                    if ($media['streams'][$_key]['port']) {
                        $cell_content.="<br><em class='gray'>$_stream</em>";
                    } else {
                        $cell_content.="<br><em style='text-decoration: line-through'>$_stream</em>";
                    }
                }
            }

            $cell_content.="
            </div>
            ";

            print "
                <tr onClick=\"return toggleVisibility('row$i')\">
            ";

            $packet_length = strlen($msg);

            print "
                <td><span>$i/$this->rows&nbsp;</span></td>
                <td><span><nobr>$date</nobr></span>";
            if ($timeline && !isset($_seen_timeline[$timeline])) {
                printf("&nbsp;&nbsp;<span class='badge badge-info'>+%ds</span>", $timeline);
                $_seen_timeline[$timeline] = 1;
            }

            print "<br /><nobr>$packet_length bytes</nobr></td>
            ";

            $column_current = 1;
            while ($column_current <= count($this->column)) {
                if ($arrow_possition == $column_current) {
                    /* First cell, first port, append extra cell */
                    if ($column_current < count($this->column) && $column_current < $msg_possition) {
                        print "<td style='text-align: right' class='span2'>";
                        if ($direction == 'in') {
                            $this->printLabelProtocolPort($transport, $this->trace_array[$key]['fromport']);
                        } else {
                            $this->printLabelProtocolPort($transport, $this->trace_array[$key]['toport']);
                        }
                        echo "</td>";
                    }

                    $arrowColor = $status_color;

                    if ($arrow_direction == 'loop') {
                        print "<td><i style='font-size: 2.4em' class=\"icon-refresh pull-right\"></i></td>";
                    }

                    if ($arrow_possition >= 2 * $msg_possition) {
                        $arrow_span = ($arrow_possition * 2) - 4;
                        echo "<td colspan='$arrow_span' style='border-left: 2px solid #95b3d0; border-right: 2px solid #95b3d0; width:66%'>";
                    } else {
                        echo "<td colspan='2' style='border-left: 2px solid #95b3d0; border-right: 2px solid #95b3d0; width:33%'>";
                    }


                    if ($arrow_direction != 'loop') {
                        print "<div class='sarrow $arrowColor $arrow_direction'></div>";
                    }

                    if ($arrow_direction == "left") {
                        print "<div style='text-align: right; padding-right:16px;'>$cell_content</div>";
                    } else {
                        print "<div style='float:left; padding-left:16px;'>$cell_content</div>";
                    }
                    echo "</td>";

                    if ($column_current < count($this->column) && $column_current > $msg_possition) {
                        print "<td class='span2'>";
                        if ($direction == 'in') {
                            $this->printLabelProtocolPort($transport, $this->trace_array[$key]['fromport']);
                        } else {
                            $this->printLabelProtocolPort($transport, $this->trace_array[$key]['toport']);
                        }
                        echo "</td>";
                    }
                } else {
                    if ($msg_possition == $column_current) {
                        if ($msg_possition < $arrow_possition) {
                            print "<td style='width: 17%;text-align: right'>";
                            if ($direction == 'out') {
                                $this->printLabelProtocolPort($transport, $this->trace_array[$key]['fromport']);
                            } else {
                                $this->printLabelProtocolPort($transport, $this->trace_array[$key]['toport']);
                            }
                        } else {
                            print "<td style='width: 17%'>";
                            if ($direction == 'out') {
                                $this->printLabelProtocolPort($transport, $this->trace_array[$key]['fromport']);
                            } else {
                                $this->printLabelProtocolPort($transport, $this->trace_array[$key]['toport']);
                            }
                        }
                    } elseif ($arrow_possition != $column_current
                        && ( $column_current == 1
                        || ( $arrow_possition < $column_current
                        && $arrow_possition != $msg_possition)
                    )) {
                        print "<td style='width: 17%; border-right: 2px solid #95b3d0'>";
                        print "</td><td>";
                        print "&nbsp;";
                    } elseif ($arrow_possition == $msg_possition) {
                        echo "<td></td>";
                    #} elseif ($column_current != $this->column[$fromip] && $column_current != $this->column[$toip]) {
                    #    echo "<td colspan=2 border-left: 2px solid #95b3d0'></td>";
                    }
                    echo "</td>";
                }

                if ($arrow_possition == $column_current && $column_current == count($this->column)) {
                    echo "<td style='width:17%'>";
                    if ($direction == 'in') {
                        $this->printLabelProtocolPort($transport, $this->trace_array[$key]['fromport']);
                    } else {
                        $this->printLabelProtocolPort($transport, $this->trace_array[$key]['toport']);
                    }
                    echo "</td>";
                }
                $column_current++;
                if ($arrow_direction == 'loop') {
                    $seen_msg[$md5]++;
                }
            }

            echo "</tr>";

            if (is_array($this->SIPProxies)) {
                $IPels = explode(":", $fromip);
                $justIP = $IPels[0];
                foreach (array_keys($this->SIPProxies) as $localProxy) {
                    if ($localProxy == $justIP) {
                        $direction="out";
                        break;
                    }
                }
            }

            /* Details */

            $trace_span = count($this->column) * 2 + 3;

            print "
            <tr class='extrainfo $status_color' id=row$i>
                <td colspan=$trace_span>
                    <div class='row-fluid'>
                        <div class='span2' style='max-width: 120px; padding-left: 15px;'>
            ";

            if ($direction == "out" or $isProxy) {
                print "<nobr><h1>SIP Proxy</h1></nobr>";
            } else {
                if ($sip_phone_img && $sip_phone_img!='unknown.png') {
                    print "<img style='max-width:none' src=images/$sip_phone_img>";
                } else {
                    print "<i style=\"font-size:28px\" class=\"icon-question\"></i>";
                }
            }
            print "<br />";
            if ($timeline > 0) {
                printf("<p>+%s s<br>(%s)</p>", $timeline, sec2hms($timeline));
            }
            print "</div><div class=span10 style='font-family: monospace; color: #333333;'>";

            $msg = nl2br(htmlentities($msg));

            print "<span>$msg</span>
                </div>
                </div>";

            echo "
                </td>
            </tr>
            ";
        }

        print "
        </table>
        ";
    }

    public function showText($proxyIP, $callid, $fromtag, $totag)
    {
        $this->getTrace($proxyIP, $callid, $fromtag, $totag);
        print "<pre>";

        if (!count($this->trace_array)) {
            print "SIP trace for session id $callid is not available.";
            return false;
        }

        printf("SIP trace on proxy %s for session %s\n--\n\n", $proxyIP, $callid);

        foreach (array_keys($this->trace_array) as $key) {
            $i++;
            printf(
                "Packet %d at %s from %s to %s (%s)\n",
                $i,
                $this->trace_array[$key]['date'],
                $this->trace_array[$key]['fromip'],
                $this->trace_array[$key]['toip'],
                $this->trace_array[$key]['direction']
            );
            printf(
                "\n%s\n",
                htmlspecialchars($this->trace_array[$key]['msg'])
            );
            print "---\n";
        }
        print "</pre>";
    }

    public function togglePublicVisibility($callid, $fromtag, $public = '0')
    {
        $key="callid-".trim($callid).trim($fromtag);

        if (!$public) {
            $query = sprintf("delete from memcache where `key` = '%s'", addslashes($key));
            $this->cdrtool->query($query);
        } else {
            $query = sprintf("delete from memcache where `key` = '%s'", addslashes($key));
            $this->cdrtool->query($query);
            $query = sprintf("insert into memcache values ('%s','public')", addslashes($key));
            $this->cdrtool->query($query);
        }
    }

    public function purgeRecords($days = '')
    {
        if ($this->enableThor) {
            return true;
        }

        $b=time();

        if ($days) {
            $this->purgeRecordsAfter = $days;
        } elseif (!$this->purgeRecordsAfter) {
            $this->purgeRecordsAfter = 15;
        }

        $beforeDate=Date("Y-m-d", time()-$this->purgeRecordsAfter*3600*24);

        $query = sprintf(
            "SELECT id as min, time_stamp FROM %s ORDER BY id ASC limit 1",
            addslashes($this->table)
        );

        if ($this->db->query($query)) {
            if ($this->db->num_rows()) {
                $this->db->next_record();
                $min=$this->db->f('min');
                $begindate=$this->db->f('date');
            } else {
                $log = sprintf("No records found in %s\n", $this->table);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
        } else {
            $log = sprintf("Error: %s (%s)\n", $this->db->Error, $query);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $query=sprintf(
            "select id as max from %s where time_stamp < '%s' order by id DESC limit 1",
            addslashes($this->table),
            addslashes($beforeDate)
        );

        if ($this->db->query($query) && $this->db->num_rows()) {
            $this->db->next_record();
            $max=$this->db->f('max');
        } else {
            $log=sprintf(
                "No records found in %s before %s, records start after %s\n",
                $this->table,
                $beforeDate,
                $begindate
            );
            syslog(LOG_NOTICE, $log);
            print $log;
            return false;
        }

        $deleted = 0;
        $i = $min;

        $interval = 1000;

        $rows2delete = $max - $min;
        $found = 0;

        print "$rows2delete traces to delete between $min and $max\n";

        while ($i<=$max) {
            $found=$found+$interval;

            if ($i + $interval < $max) {
                $top=$i;
            } else {
                $top=$max;
            }
            $query=sprintf(
                "delete low_priority from %s where id >= '%d' and id <='%d'",
                addslashes($this->table),
                addslashes($min),
                addslashes($top)
            );
            if ($this->db->query($query)) {
                $deleted = $deleted + $this->db->affected_rows();
            } else {
                $log = sprintf("Error: %s (%s)", $this->db->Error, $this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return false;
            }

            if ($found > $progress * $rows2delete / 100) {
                $progress++;
                if ($progress % 10 == 0) {
                    print "$progress% ";
                }
                flush();
            }

            $i = $i + $interval;
        }

        print "\n";

        $e = time();
        $d = $e - $b;
        $rps = 0;

        if ($deleted && $d) {
            $rps=$deleted/$d;
        }

        $log = sprintf(
            "%s records before %s from %s deleted in %d s @ %.0f rps\n",
            $deleted,
            $beforeDate,
            $this->table,
            $d,
            $rps
        );
        syslog(LOG_NOTICE, $log);
        print $log;

        return true;
    }
}
