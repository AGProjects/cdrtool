<?php

class MediaTrace
{
    public $enableThor  = false;
    public $table       = 'media_sessions';

    private $soapEngineId;
    private $cdr_source;
    private $SOAPlogin;
    private $SOAPurl;
    private $SoapAuth;
    private $soapclient;
    private $info;
    private $db;

    public function __construct($cdr_source)
    {
        global $DATASOURCES;

        require_once 'errors.php';

        $this->cdr_source = $cdr_source;

        if (!is_array($DATASOURCES[$this->cdr_source])) {
            $log = sprintf("Error: datasource '%s' is not defined", $this->cdr_source);
            print $log;
            throw new DataSourceUndefinedError($log);
            return 0;
        }

        if (strlen($DATASOURCES[$this->cdr_source]['enableThor'])) {
            $this->enableThor = $DATASOURCES[$this->cdr_source]['enableThor'];
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

                $this->SOAPurl = $soapEngines[$this->soapEngineId]['url'];

                $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

                // Instantiate the SOAP client
                $this->soapclient = new WebService_NGNPro_SipPort($this->SOAPurl);

                $this->soapclient->setOpt('curl', CURLOPT_TIMEOUT, 5);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
            } else {
                print "Error: soapEngineID not defined in datasource $this->cdr_source";
                return false;
            }
        } else {
            if ($DATASOURCES[$this->cdr_source]['table']) {
                $this->table = $DATASOURCES[$this->cdr_source]['table'];
            }

            $db_class = $DATASOURCES[$this->cdr_source]['db_class'];
            if (class_exists($db_class)) {
                $this->db = new $db_class;
            } else {
                printf("<p><font color=red>Error: database class %s is not defined in datasource %s</font>", $db_class, $this->cdr_source);
                return false;
            }
        }
    }

    private function getTrace($proxyIP, $callid, $fromtag, $totag)
    {
        if ($this->enableThor) {
            // get trace using soap request
            if (!$proxyIP || !$callid || !$fromtag) {
                echo "
                    <div style='display: flex; align-items: center; justify-content: center;'>
                        <div class='span10' style='padding-top:40px;'>
                            <p class='alert alert-danger'><strong>Error</strong>: proxyIP or callid or fromtag are not defined</p>
                        </div>
                    </div>
                ";
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
                print "<p><font color=red>Error: soap client is not defined</font>";
                return false;
            }

            $filter = array(
                'nodeIp'  => $proxyIP,
                'callId'  => $callid,
                'fromTag' => $fromtag,
                'toTag'   => $totag
            );

            $this->soapclient->addHeader($this->SoapAuth);

            $result = $this->soapclient->getMediaTrace($filter);

            if ((new PEAR)->isError($result)) {
                $error_msg   = $result->getMessage();
                $error_fault = $result->getFault();
                $error_code  = $result->getCode();

                if ($error_fault->detail->exception->errorcode != 1060) {
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
                }
                return false;
            }
            $this->info = json_decode($result);
        } else {
            if (!is_object($this->db)) {
                print "<p><font color=red>Error: no database connection defined</font>";
                return false;
            }

            // get trace from SQL
            $query = sprintf(
                "select info from %s where call_id = '%s' and from_tag = '%s' and to_tag= '%s'",
                addslashes($this->table),
                addslashes($callid),
                addslashes($fromtag),
                addslashes($totag)
            );

            if (!$this->db->query($query)) {
                printf(
                    "<p><font color=red>Database error for query %s: %s (%s)</font>",
                    $query,
                    $this->db->Error,
                    $this->db->Errno
                );
                return false;
            }

            if ($this->db->num_rows()) {
                $this->db->next_record();
                $this->info = json_decode($this->db->f('info'));
            }
        }
    }

    public function show($proxyIP, $callid, $fromtag, $totag)
    {
        if ($_SERVER['HTTPS'] == "on") {
            $protocolURL = "https://";
        } else {
            $protocolURL = "http://";
        }

        $this->getTrace($proxyIP, $callid, $fromtag, $totag);

        if (!is_object($this->info)) {
            echo "
                <div style='display: flex; align-items: center; justify-content: center;'>
                    <div class='span10' style='padding-top:40px;'>
                        <div class='alert'>No information available</div>
                    </div>
                </div>
            ";
            return false;
        }

        if (empty($this->info->streams)) {
            echo "
                <div style='display: flex; align-items: center; justify-content: center;'>
                    <div class='span10' style='padding-top:40px;'>
                        <div class='alert alert-info'>No RTP media streams have been established</div>
                    </div>
                </div>
            ";
            return;
        }

        print "<div class='container-fluid'><div id=trace class='main'>";
        $sessionId = rtrim(base64_encode(hash('md5', $callid, true)), "=");
        print "<h1 class=page-header>CDRTool Media Trace<br/><small>Call ID: $callid</small><br /><small>Media Session ID: $sessionId</small></h1>";
        
        $seen_stamp = [];
        foreach (array_values($this->info->streams) as $_val) {
            $_diff = $_val->end_time - $_val->timeout_wait;
            $seen_stamp[$_val->start_time]++;
            $seen_stamp[$_val->end_time]++;
            $seen_stamp[$_diff]++;
            $media_types[]=$_val->media_type;
        }

        print "<h2>Media Information</h2>";

        print "<table border=0>";
        printf("<tr><td class=border>Call duration</td><td class=border>%s</td></tr>", $this->info->duration);
        list($relay_ip, $relay_port)=explode(":", $this->info->streams[0]->caller_local);
        printf("<tr><td class=border>Media relay</td><td class=border>%s</td></tr>", $relay_ip);
        print "</table>";

        print "<h2>Media Streams</h2>";

        print "<table class='table table-condensed table-striped' style='width:600px' border=0>";
        print "<thead><tr><th></th>";

        foreach (array_values($media_types) as $_type) {
            printf("<th>%s</th>", ucfirst($_type));
        }

        print "</tr></thead>";

        foreach ($this->info->streams[0] as $_val => $_value) {
            printf("<tr><td class=border>%s</td>", ucfirst(preg_replace("/_/", " ", $_val)));
            $j=0;
            while ($j < count($media_types)) {
                printf("<td class=border>%s</td>", $this->info->streams[$j]->$_val);
                $j++;
            }

            printf("</tr>\n");
        }

        print "</table>";

        print "<br><h2>Stream Succession</h2>";

        $w_legend_bar = 500;
        $w_text = 30;
        $stamps = array_keys($seen_stamp);
        sort($stamps);

        $w_table = $w_legend_bar + $w_text;

        print "<table border=0 cellpadding=1 cellspacing=1 width=$w_table>";

        $j = 0;

        $_index = 0;
        foreach (array_values($this->info->streams) as $_val) {
            if ($_val->status == 'unselected ice candidate') {
                continue;
            }

            $_index = $_index+$_val->start_time;

            $_duration   = $_val->end_time-$_val->start_time;
            $_timeout    = $_val->timeout_wait;

            $duration_print = $_duration;

            if ($_val->status == 'conntrack timeout') {
                $w_duration   = intval(($_duration-$_timeout)*$w_legend_bar/$this->info->duration);
                $w_timeout    = intval($_timeout*$w_legend_bar/$this->info->duration);
                $duration_print = $_duration - $_timeout;
            } elseif ($_val->status == 'no-traffic timeout') {
                $w_duration   = intval($_duration*$w_legend_bar/$this->info->duration);
                $w_timeout    = intval($_timeout*$w_legend_bar/$this->info->duration);
            } elseif ($_val->status == 'closed') {
                $w_duration   = intval($_duration * $w_legend_bar / $this->info->duration);
                $w_timeout    = 0;
            }


            $w_start_time = intval($_index*$w_legend_bar/$this->info->duration);
            $w_rest       = $w_legend_bar-$w_duration-$w_timeout-$w_start_time;
            $w_duration_p = 0;
            if ($w_duration > 0) {
                $w_duration_p = ($w_legend_bar / $w_duration) * 100;
            }
            $w_timeout_p = 0;
            if ($w_timeout > 0) {
                $w_timeout_p  = ($w_legend_bar / $w_timeout) * 100;
            }
            $w_start_p = 0;
            if ($w_start_time > 0) {
                $w_start_p  = ($w_legend_bar / $w_start_time)* 100;
            }
            //printf ("%s, %s, %s, %s<br>\n",$w_start_p,$w_duration_p,$w_timeout_p,$w_rest);

            if ($_val->caller_packets != '0' && $_val->callee_packets != '0') {
                print "<tr><td width=$w_text class=border>$_val->media_type</td>";
                print "<td width=$w_legend_bar>\n";
                //print "<table width=100% border=0 cellpadding=0 cellspacing=0><tr>\n";
                print "<div class='progress progress-striped'>";
                print "<div class=bar style='width:$w_start_p%'></div>\n";
                print "<div class='bar bar-success'  style='width:$w_duration_p% ; text-align:center'><font color=white>$duration_print</font></div>\n";

                if ($_val->timeout_wait) {
                    print "<div class='bar bar-danger' style='width:$w_timeout_p%; text-align:center'><font color=white>$_timeout</font></div>\n";
                } else {
                    print "<div class='bar bar-warning' style='width:$w_timeout_p%; text-align:center'></div>\n";
                }
                //print "<td width=$w_rest bgcolor=white align=center></td>\n";

                //print "</table>\n";

                print "</td></tr>";
            } elseif ($_val->status == 'unselected ICE candidate') {
                print "<tr><td>$_val->media_type</td><td>ICE session</td></tr>";
            } else {
                print "<tr><td>$_val->media_type</td><td>No stream data found</td></tr>";
            }
        }

        print "</table>";

        print "<br><strong>Legend</strong>";
        print "<p><table border=0>
        <tr>
        <td width=50><div class='progress progress-striped progress-success'><div class='bar' style='width:100%'></div></div></td>
        <td>Session data</td>
        </tr>
        <tr>
        <td><div class='progress progress-striped progress-danger'><div class='bar' style='width:100%'></div></div></td>
        <td>Timeout period</td>
        </tr>
        </table></p></div></div>
        ";
    }
}
