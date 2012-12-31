<?
/*
    Copyright (c) 2008 AG Projects
    http://ag-projects.com
    Author Adrian Georgescu
*/

class NetworkStatistics {
    // obtain statistics from SIP Thor network

	var $statistics        = array();
    var $status            = array();
	var $sip_summary       = array();
    var $sip_proxies       = array();
    var $node_statistics   = array();
    var $domain_statistics = array();
    var $allowedRoles      = array('sip_proxy',
                                   'media_relay',
                                   'provisioning_server',
                                   'xcap_server',
                                   'thor_dnsmanager',
                                   'thor_database',
                                   'voicemail_server'
                                   );
	var $allowedSummary    = array('online_accounts',
                                   'total_accounts'
                                   );

    function NetworkStatistics($engineId,$allowedDomains=array()) {
    	if (!strlen($engineId)) return false;

        $this->allowedDomains  = $allowedDomains;
        $this->soapEngineId    = $engineId;

    	require("/etc/cdrtool/ngnpro_engines.inc");
        require_once("ngnpro_soap_library.php");

        $this->SOAPlogin = array(
                               "username"    => $soapEngines[$this->soapEngineId]['username'],
                               "password"    => $soapEngines[$this->soapEngineId]['password'],
                               "admin"       => true
                               );

        $this->SOAPurl=$soapEngines[$this->soapEngineId]['url'];

        $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

        $this->soapclient = new WebService_NGNPro_NetworkPort($this->SOAPurl);

        $this->soapclient->setOpt('curl', CURLOPT_TIMEOUT,        5);
        $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if (is_array($soapEngines[$this->soapEngineId]['hostnames'])) {
            $this->hostnames=$soapEngines[$this->soapEngineId]['hostnames'];
        } else {
            $this->hostnames=array();
        }

    }

    function getStatistics() {

        $this->soapclient->addHeader($this->SoapAuth);
        $result = $this->soapclient->getStatistics();

        if (PEAR::isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            $log=sprintf("Error from %s: %s: %s",$this->SOAPurl,$error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $this->statistics=json_decode($result,true);;

        foreach (array_keys($this->statistics) as $_ip) {
        	if ($_ip == 'summary') {
                foreach (array_keys($this->statistics[$_ip]) as $_role) {
                    if ($_role == 'sip_proxy') {
                        foreach (array_keys($this->statistics[$_ip][$_role]) as $_section) {
                            foreach (array_keys($this->statistics[$_ip][$_role][$_section]) as $_domain) {
                                if (count($this->allowedDomains) && !in_array($_domain,$this->allowedDomains)) continue;
                                if (count($this->allowedDomains) && !in_array($_section,$this->allowedSummary)) continue;
                                $this->sip_summary[$_section]=$this->sip_summary[$_section]+$this->statistics[$_ip][$_role][$_section][$_domain];
                            }
                        }
                    }
                }

                continue;

            }

            foreach (array_keys($this->statistics[$_ip]) as $_role) {
                if ($_role == 'sip_proxy') {
                  	$this->node_statistics[$_ip]['sip_proxy']=true;
					foreach (array_keys($this->statistics[$_ip][$_role]) as $_section) {
                        foreach (array_keys($this->statistics[$_ip][$_role][$_section]) as $_domain) {
                            if (count($this->allowedDomains) && !in_array($_domain,$this->allowedDomains)) continue;
                            $this->domain_statistics[$_domain][$_section] = $this->domain_statistics[$_domain][$_section] + $this->statistics[$_ip][$_role][$_section][$_domain];
                            $this->domain_statistics['total'][$_section]  = $this->domain_statistics['total'][$_section]  + $this->statistics[$_ip][$_role][$_section][$_domain];
                    		$this->node_statistics[$_ip][$_section]       = $this->node_statistics[$_ip][$_section]       + $this->statistics[$_ip][$_role][$_section][$_domain];
                        }
                    }
                }
            }
        }
    }

    function getStatus() {

        $this->soapclient->addHeader($this->SoapAuth);
        $result = $this->soapclient->getStatus();

        if (PEAR::isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            $log=sprintf("Error from %s: %s: %s",$this->SOAPurl,$error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $this->status=json_decode($result,true);

        foreach (array_keys($this->status) as $_id) {
            foreach ($this->status[$_id]['roles'] as $_role) {

                if ($_role=='sip_proxy') $this->sip_proxies[$this->status[$_id]['ip']]++;
                if ($_role=='thor_dnsmanager') $this->dns_managers[$this->status[$_id]['ip']]++;
                if ($_role=='thor_manager') $this->thor_managers[$this->status[$_id]['ip']]++;

				$ip=$this->status[$_id]['ip'];
                $this->roles[$_role][$ip]=array('ip'      => $ip,
                                                'version' => $this->status[$_id]['version']
                                                );
                foreach(array_keys($this->status[$_id]) as $_attr) {
                    if ($_attr == 'ip' || $_attr == 'version' || $_attr == 'roles') continue;
                	$this->roles[$_role][$ip]['attributes'][$_attr]=$this->status[$_id][$_attr];
                }
            }
        }
    }

    function showStatus() {
        $this->getStatus();

        print "<div class=row><table class=\"span12 table table-condensed table-striped\">";
        print "<thead><tr>";
        if (count($this->allowedDomains)) {
        	print "<th>Role</th><th>Address</th><th>Version</th></tr>";
        } else {
        	print "<th>Role</th><th>Address</th><th>Version</th><th>Attributes</th></tr></thead>";
        }

        foreach (array_keys($this->roles) as $_role) {
            if (count($this->allowedDomains)) {
                if (!in_array($_role,$this->allowedRoles)) continue;
            }
            foreach ($this->roles[$_role] as $_entity) {
                if (!$print_role[$_role]) {
                	$_role_print=preg_replace("/_/"," ",$_role);
                } else {
                	$_role_print='';
                }

            	if (count($this->allowedDomains)) {

            		printf ("<tr><td><b>%s</b></td><td class=span4>%s</td><td>%s</td></tr>",
            		ucfirst($_role_print),$this->ip2host($_entity['ip']),$_entity['version']);
				} else {
                	$a_print='';

                    if ( is_array($_entity['attributes'])) {
                        foreach (array_keys($_entity['attributes']) as $_a1) {
                            if ($_a1 == 'dburi') {
                                if (preg_match("/^(mysql:\/\/\w*):\w*(@.*)$/",$_entity['attributes'][$_a1],$m)) {
                                    $val=$m[1].':xxx'.$m[2];
                                } else {
                                    $val=$_entity['attributes'][$_a1];
                                }
                            } else {
                                $val=$_entity['attributes'][$_a1];
                            }
                            $a_print .= sprintf ("%s=%s ",$_a1,$val);
                        }
                    }

            		printf ("<tr><td class=span3><b>%s</b></td><td class=span2>%s</td><td class=span2>%s</td><td>%s</td></tr>",
            		ucfirst($_role_print),$this->ip2host($_entity['ip']),$_entity['version'],$a_print);
                }
                $print_role[$_role]++;
            }
        }

        print "</table>";

	}

    function showStatistics() {
        $this->getStatistics();

        print "<div class=row><table class='span12 table table-condensed table-striped'>";
        print "<thead><tr>";
        foreach (array_keys($this->sip_summary) as $_section) {
        	$_section_print=preg_replace("/_/"," ",$_section);
            printf ("<th>%s</th>",ucfirst($_section_print));
        }
        print "</tr></thead><tr>";
        foreach (array_keys($this->sip_summary) as $_section) {
            printf ("<td style='text-align:center'>%s</td>",$this->sip_summary[$_section]);
        }
        print "</tr>";
        print "</table></div>";

	}

    function ip2host($ip) {
		if ($this->hostnames[$ip]) {
            return $this->hostnames[$ip];
        } else {
        	return $ip;
        }
    }
}

class SipThorNetworkImage {
    // plot graphical SIP Thor network status

    var $imgsize      = 630;
    var $nodes        = array();
	var $node_statistics = array();
    var $display_options = array();
    var $accounts_item   = 'online_accounts';

    function SipThorNetworkImage($engineId,$allowedDomains=array(),$display_options=array()) {

        if (!strlen($engineId)) return false;

        if (is_array($display_options)) {
        	$this->display_options=$display_options;
        }

        if ($this->display_options['accounts_item']) {
        	$this->accounts_item=$this->display_options['accounts_item'];
        }

        $this->soapEngineId=$engineId;
        $NetworkStatistics=new NetworkStatistics($engineId,$allowedDomains);

        $NetworkStatistics->getStatus();
        $NetworkStatistics->getStatistics();

        $this->sip_proxies     = $NetworkStatistics->sip_proxies;
        $this->dns_managers    = $NetworkStatistics->dns_managers;
        $this->thor_mangers    = $NetworkStatistics->thor_managers;
        $this->node_statistics = $NetworkStatistics->node_statistics;
        $this->hostnames       = $NetworkStatistics->hostnames;

        /*
        if (!$this->display_options['hide_sessions']) {
            require_once("media_sessions.php");
            $MediaSessions = new MediaSessionsNGNPro($engineId);
            $MediaSessions->getSummary();
    
            foreach ($MediaSessions->summary as $_relay) {
                $this->node_statistics[$_relay['ip']]['sessions']=$_relay['session_count'];
            }
        }
        */
    }

    function buildImage() {
        $img   = imagecreatetruecolor($this->imgsize, $this->imgsize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        imagefill($img, 0, 0, $white);
    
        $c=count($this->sip_proxies);
        $cx=$this->imgsize/2;
        $cy=$cx;
     
        $radius=0.7*$cx;
     
        // Sip Thor node image
        $sip_thor_node_img = @imagecreatefrompng('SipThorNode.png');
        list($nw, $nh) = getimagesize('SipThorNode.png');
    
        // Internet cloud Image
        $cloud_img = @imagecreatefrompng('InternetCloud.png');
        list($cw, $ch) = getimagesize('InternetCloud.png');
    
        // Sip Thor title rectangle image
        $sip_thor_background_img = @imagecreatefrompng('SipThorNetworkBackground.png');
        list($tw, $th) = getimagesize('SipThorNetworkBackground.png');

        if (!$this->display_options['hide_frame']) {
        	imagecopy ($img,$sip_thor_background_img, $this->imgsize/2-$tw/2, $this->imgsize/2-$th/2, 0, 0, $tw, $th);
        }
        imagecopy ($img,$cloud_img, $this->imgsize/2-$cw/2, $this->imgsize/2-$ch/2, 0, 0, $cw, $ch);

        $dash=false;
        $dashsize=2;
        
        for ($angle=0; $angle<=(180+$dashsize); $angle+=$dashsize) { 
          $x = ($radius * cos(deg2rad($angle))); 
          $y = ($radius * sin(deg2rad($angle))); 
     
          if ($dash) { 
              imageline($img, $cx+$px, $cy+$py, $cx+$x, $cy+$y, $black);
              imageline($img, $cx-$px, $cx-$py, $cx-$x, $cy-$y, $black);
          } 
          
          $dash=!$dash; 
          $px=$x; 
          $py=$y;

          if ($dash) { 
              imageline($img, $cx+$px, $cy+$py, $cx+$x, $cy+$y, $black);
              imageline($img, $cx-$px, $cx-$py, $cx-$x, $cy-$y, $black);
          } 

        }

        if (count($this->dns_managers)) {
            $h1=0;
            $t=count($this->dns_managers);
            foreach (array_keys($this->dns_managers) as $_ip) {
    			imagecopy ($img,$sip_thor_node_img, $this->imgsize-120-$h1, 0, 0, 0, $nw-20, $nh-20);

                $text=sprintf("DNS%s",$t--);
            	imagestring ($img, 3, $this->imgsize-65-$h1, 80, $text, $black);
                $v1=$v1+10;
            	$h1=$h1+50;

            }

            $v1=100;
            foreach (array_keys($this->dns_managers) as $_ip) {
            	imagestring ($img, 3, $this->imgsize-125, $v1, $_ip, $black);
                $v1=$v1+10;
            }
        }

        if (count($this->node_statistics)) {
    
            $dashsize=360/count($this->node_statistics);
            $j=0;

            $node_names=array_keys($this->node_statistics);

            for ($angle=0; $angle<360; $angle+=$dashsize) {
         
              $x = ($radius * cos(deg2rad($angle)));
              $y = ($radius * sin(deg2rad($angle)));

              if ($this->hostnames[$node_names[$j]]) {
              	$text = $this->hostnames[$node_names[$j]];
              	//$text = $node_names[$j];
              } else {
              	$text = $node_names[$j];
              }
              $px=$x; 
              $py=$y;

              if (strlen($this->node_statistics[$node_names[$j]]['online_accounts']) && strlen($this->node_statistics[$node_names[$j]]['sessions'])) {
                if (!$this->display_options['hide_accounts']) {
              		$extra_text1=intval($this->node_statistics[$node_names[$j]][$this->accounts_item]). ' accounts';
                }
                if (!$this->display_options['hide_sessions']) {
               		$extra_text2=intval($this->node_statistics[$node_names[$j]]['sessions']). ' sessions';
                }
              } else if (strlen($this->node_statistics[$node_names[$j]]['online_accounts'])) {
                if (!$this->display_options['hide_accounts']) {
              		$extra_text1=intval($this->node_statistics[$node_names[$j]][$this->accounts_item]). ' accounts';
                } else {
                    $extra_text1=$node_names[$j];
                }
               	$extra_text2="";
              } else if (strlen($this->node_statistics[$node_names[$j]]['sessions'])) {
                if (!$this->display_options['hide_sessions']) {
               		$extra_text1=intval($this->node_statistics[$node_names[$j]]['sessions']). ' sessions';
                }
              	$extra_text2="";
              }
         
              if (($angle >= 120 && $angle < 240)) {
                imagestring ($img, 3, $cx+$px-70, $cy+$py-72, $text, $black);
                imagestring ($img, 3, $cx+$px-70, $cy+$py-62, $extra_text1, $black);
                imagestring ($img, 3, $cx+$px-70, $cy+$py-52, $extra_text2, $black);
              } else {
                imagestring ($img, 3, $cx+$px-110, $cy+$py-30, $text, $black);
                imagestring ($img, 3, $cx+$px-110, $cy+$py-20, $extra_text1, $black);
                imagestring ($img, 3, $cx+$px-110, $cy+$py-10, $extra_text2, $black);
              }
              imagecopy ($img,$sip_thor_node_img, $cx+$px-$nw/2+7, $cy+$py-$nh/2+5, 0, 0, $nw-20, $nh-20);
              $j++;
              
            }
        }
    
        return $img;
    }
}

class SIPstatistics {
    // build graphical statistics with sip registrar and media relay usage

    var $domains        = array('total'=>'total');

    function SIPstatistics () {
        global $CDRTool;

		$this->path=$CDRTool['Path'];

        $this->harvest_file       = "/tmp/CDRTool-sip-statistics.txt";
        $this->harvest_script     = $this->path."/scripts/harvestStatistics.php";

        $this->mrtgcfg_dir        = $this->path."/status/usage";
    	$this->mrtgcfg_file       = $this->path."/status/usage/sip_statistics.mrtg";
        $this->mrtg_data_script   = $this->path."/scripts/generateMrtgData.php";
        $this->mrtg_config_script = $this->path."/scripts/generateMrtgConfig.php";

        $this->getDomains();

	}

    function getDomains () {
        global $CDRTool;

        if (!is_array($CDRTool['statistics']['domains'])) return;

        foreach ($CDRTool['statistics']['domains'] as $_domain) {
        	$this->domains[$_domain]=$_domain;
        }
    }

    function generateMrtgConfigFile () {

        if (!$handle = fopen($this->mrtgcfg_file, 'w+')) {
        	echo "Error opening {$this->mrtgcfg_file}.\n";
            return 0;
        }
        
        // printing cfg header

        fwrite($handle,"
### Global Config Options
WorkDir: {$this->mrtgcfg_dir}
IconDir: {$this->mrtgcfg_dir}/images
Refresh: 300
#WriteExpires: Yes
        ");

        while(list($key,$value) = each($this->domains)) {
        	fwrite($handle,"\n\n
## {$key}

Target[{$key}_users]: `{$this->mrtg_data_script} {$key} users`
Options[{$key}_users]: growright, gauge, nobanner
BodyTag[{$key}_users]: <BODY LEFTMARGIN=\"1\" TOPMARGIN=\"1\">
#PNGTitle[{$key}_users]: <center>Online Users for {$key}</center>
MaxBytes[{$key}_users]: 5000000
Title[{$key}_users]: Online Users for {$key}
ShortLegend[{$key}_users]: U
XSize[{$key}_users]: 300
YSize[{$key}_users]: 75
Ylegend[{$key}_users]: Users
Legend1[{$key}_users]: Online Users
LegendI[{$key}_users]:   Online Users
LegendO[{$key}_users]: 
PageTop[{$key}_users]: <H1> Online Users for {$key} </H1>

Target[{$key}_sessions]: `{$this->mrtg_data_script} {$key} sessions`
Options[{$key}_sessions]: growright, nobanner, gauge
BodyTag[{$key}_sessions]: <BODY LEFTMARGIN=\"1\" TOPMARGIN=\"1\">
MaxBytes[{$key}_sessions]: 50000
Title[{$key}_sessions]: Sessions Statistics for {$key}
ShortLegend[{$key}_sessions]: Ses
XSize[{$key}_sessions]: 300
YSize[{$key}_sessions]: 75
Ylegend[{$key}_sessions]: Sessions
Legend1[{$key}_sessions]: Active Sessions
LegendI[{$key}_sessions]:   Active Sessions
LegendO[{$key}_sessions]:   
PageTop[{$key}_sessions]: <H1> Active Sessions for {$key} </H1>

Target[{$key}_traffic]: `{$this->mrtg_data_script} {$key} traffic`
Options[{$key}_traffic]: gauge, growright, bits, nobanner
BodyTag[{$key}_traffic]: <BODY LEFTMARGIN=\"1\" TOPMARGIN=\"1\">
#PNGTitle[{$key}_traffic]: {$key} traffic
MaxBytes[{$key}_traffic]: 1250000000
Title[{$key}_traffic]: IP traffic for {$key}
XSize[{$key}_traffic]: 300
YSize[{$key}_traffic]: 75
Legend1[{$key}_traffic]: Caller Traffic in Bits per Second
Legend2[{$key}_traffic]: Called Traffic in Bits per Second
LegendI[{$key}_traffic]:   caller
LegendO[{$key}_traffic]:   called
PageTop[{$key}_traffic]: <H1> IP Traffic for {$key} </H1>

        ");
        
        }
        
        fclose($handle);
	}

    function generateMrtgData($domain,$dataType) {
    	$value1=0;
        $value2=0;
	$domain= str_replace(".","\.",$domain);

    	$lines=explode("\n",file_get_contents($this->harvest_file));
        foreach ($lines as $line) {
            if (preg_match("/^$domain\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/",$line,$m)) {
                if ($dataType == 'sessions') {
                	$value1 = $m[2];
                    $value2 = $m[2];
                } else if ($dataType == 'traffic') {
                	$value1 = $m[3];
                    $value2 = $m[4];
                } else if ($dataType == 'users') {
                	$value1 = $m[1];
                    $value2 = $m[1];
                }
            }
        }

        printf ("%d\n%d\n0\n0\n\n",$value1,$value2);
    }

    function getOnlineAccountsFromMySQL($class) {

		$domains=array();
        $online_devices=0;
        $online_accounts=0;

        if (!class_exists($class)) return array();

        $db = new $class();

        $query="select count(*) as c, domain from location group by domain";
        dprint($query);

        if (!$db->query($query))  {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
        	return array();
        }
        if (!$db->num_rows()) return array();

        while ($db->next_record()) {
            $domains[$db->f('domain')]['online_devices'] = intval($db->f('c'));
            $online_devices = $online_devices + intval($db->f('c'));
        }

        $query="select count(distinct(concat(username,domain))) as c, domain from location group by domain";
        dprint($query);

        if (!$db->query($query))  {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$db->Error,$db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
        	return array();
        }

        if (!$db->num_rows()) return array();

        while ($db->next_record()) {
            $domains[$db->f('domain')]['online_accounts'] = intval($db->f('c'));
            $online_accounts=$online_accounts+intval($db->f('c'));
        }

		$domains['total']['online_devices']=$online_devices;
        $domains['total']['online_accounts']=$online_accounts;

        return $domains;
	}

    function writeHarvestFile($body) {

        if (!strlen($body)) return 0;

        if (!$handle = fopen($this->harvest_file, 'w+')) {
            $log=sprintf ("Error opening mrtg harvest file %s\n",$this->harvest_file);
            print $log;
            syslog(LOG_NOTICE, $log);
        	return false;
        }

        fwrite($handle,$body);
		fclose($handle);
    }

    function harvestStatistics() {
        global $DATASOURCES;

        $datasources=array_keys($DATASOURCES);

        $totals=array();

        foreach ($datasources as $datasource) {
            if (!$DATASOURCES[$datasource]['skipStatistics']) {
                if ($DATASOURCES[$datasource]['mediaSessions']) {
                    // MediaProxy 2 via NGNPro
                    require_once("media_sessions.php");
                    $MediaSessions = new MediaSessionsNGNPro($DATASOURCES[$datasource]['mediaSessions']);
                    $MediaSessions->getSessions();
                    $totals=array_merge_recursive($totals,$MediaSessions->domain_statistics);
                } else if ($DATASOURCES[$datasource]['mediaDispatcher']) {
                    // MediaProxy 2 via dispatcher tcp socket
                    require_once("media_sessions.php");
                    $MediaSessions = new MediaSessions($DATASOURCES[$datasource]['mediaDispatcher']);
                    $MediaSessions->getSessions();
                    $totals=array_merge_recursive($totals,$MediaSessions->domain_statistics);
                } else if ($DATASOURCES[$datasource]['mediaServers']){
                    // MediaProxy 1 via relay tcp socket
                    $MediaSessions = new MediaSessions1($DATASOURCES[$datasource]['mediaServers'],$allowedDomains);
                    $MediaSessions->getSessions();
                    $totals=array_merge_recursive($totals,$MediaSessions->domain_statistics);
                }
            
                if ($DATASOURCES[$datasource]['networkStatus']) {
                    // OpenSIPS via NGNPro
                    $NetworkStatistics = new NetworkStatistics($DATASOURCES[$datasource]['networkStatus']);
                    $NetworkStatistics->getStatistics();
                    $totals=array_merge_recursive($totals,$NetworkStatistics->domain_statistics);
                } else if ($DATASOURCES[$datasource]['db_registrar']) {
                    // OpenSIPS via MySQL query
                    $db_registrar_domains=$this->getOnlineAccountsFromMySQL($DATASOURCES[$datasource]['db_registrar']);
                    $totals=array_merge_recursive($totals,$db_registrar_domains);
                }
            }
        }

        $body="domains\t\t\tonline_accounts\tsessions\tcaller\tcallee\n\n";
        foreach (array_keys($totals) as $_domain) {
            if (!$totals[$_domain]['online_accounts'] && !$totals[$_domain]['sessions']) continue;
        
            $body.=sprintf("%s\t\t%d\t\t%d\t\t%s\t%s\n",
            $_domain,
            $totals[$_domain]['online_accounts'],
            $totals[$_domain]['sessions'],
            intval($totals[$_domain]['caller']),
            intval($totals[$_domain]['callee'])
            );
        }

		$this->writeHarvestFile($body);
    }

    function buildStatistics() {

    	system($this->mrtg_config_script);
        system($this->harvest_script);
        system("env LANG=C mrtg $this->mrtgcfg_file");

    }
}

?>
