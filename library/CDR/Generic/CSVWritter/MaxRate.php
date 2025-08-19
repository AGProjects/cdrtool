<?php

class MaxRate extends CSVWritter
{
    var $skip_prefixes   = array();
    var $skip_numbers    = array();
    var $skip_domains    = array();
    var $rpid_cache      = array();
    var $translate_uris  = array();

    public function __construct($cdr_source = '', $csv_directory = '', $db_subscribers = '')
    {
        global $MaxRateSettings;   // set in global.inc

        /*
            $MaxRateSettings= array(
                                'translate_uris'=> array( '1233@10.0.0.2'=>'+1233',
                                                          '[1-9][0-9]{4}.*@10.0.0.2'=>'+1233'),
                                'skip_domains'  => array('example.net','10.0.0.1'),
                                'skip_numbers'  => array('1233'), //  skip CDRs that has the username part in this array
                                'skip_prefixes' => array('0031901') // skip CDRs that begin with any of this prefixes
                               );
        */


        if (is_array($MaxRateSettings['skip_domains'])) {
            $this->skip_domains=$MaxRateSettings['skip_domains'];
        }

        if (is_array($MaxRateSettings['skip_numbers'])) {
            $this->skip_numbers=$MaxRateSettings['skip_numbers'];
        }

        if (is_array($MaxRateSettings['skip_prefixes'])) {
            $this->skip_prefixes=$MaxRateSettings['skip_prefixes'];
        }

        if (is_array($MaxRateSettings['translate_uris'])) {
            $this->translate_uris=$MaxRateSettings['translate_uris'];
        }

        $this->AccountsDB = new $db_subscribers();

        parent::__construct($cdr_source, $csv_directory);
    }

    function write_cdr($CDR)
    {
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

        list($canonical_username, $canonical_domain)=explode("@", $cdr['destination']);

        # skip domains
        if ($canonical_domain && in_array($canonical_domain, $this->skip_domains)) return true;

        # skip numbers
        if ($canonical_username && in_array($canonical_username, $this->skip_numbers)) return true;

        # skip prefixes
        if ($canonical_username && count($this->skip_prefixes)) {
            foreach ($this->skip_prefixes as $prefix) {
                if (preg_match("/^$prefix/", $canonical_username)) return true;
            }
        }

        # get RPID if caller is local
        if ($CDR->flow != 'incoming') {
            $CallerRPID=$this->getRPIDforAccount($CDR->aNumberPrint);
        }


        if ($CallerRPID) {
            # normalize RPID
            $cdr['origin']      = '0031'.ltrim($CallerRPID,'0');
        } else {
            # normalize caller id numbers from PSTN gateway to 00format
            if (preg_match("/^\+?0([1-9][0-9]+)@(.*)$/", $CDR->aNumberPrint, $m)) {
                $cdr['origin'] = "0031".$m[1];
            } elseif (preg_match("/^\+?00([1-9][0-9]+)@(.*)$/", $CDR->aNumberPrint, $m)) {
                $cdr['origin'] = "00".$m[1];
            } elseif (preg_match("/^([1-9][0-9]+)@(.*)$/", $CDR->aNumberPrint, $m)) {
                $cdr['origin'] = "0031".$m[1];
            } elseif (preg_match("/^\+([1-9][0-9]+)@(.*)$/", $CDR->aNumberPrint, $m)) {
                $cdr['origin'] = "00".$m[1];
            } elseif (preg_match("/^anonymous@(.*)$/", $CDR->aNumberPrint) && $CDR->SipRPID) {
                if (preg_match("/^\+?0([1-9][0-9]+)$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = "0031".$m[1];
                } elseif (preg_match("/^\+?00([1-9][0-9]+)$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = "00".$m[1];
                } elseif (preg_match("/^([1-9][0-9]+)@(.*)$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = $m[1];
                } elseif (preg_match("/^\+([1-9][0-9]+)@(.*)$/", $CDR->SipRPID, $m)) {
                   $cdr['origin'] = "00".$m[1];
                } elseif (preg_match("/^\+?0[0-9]?+@?(.*)?$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = "0031123456789";
                } elseif (preg_match("/^.*[a-zA-Z].*$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = "0031123456789";
                } elseif (preg_match("/^ims.imscore.net.*$/", $CDR->SipRPID, $m)) {
                    $cdr['origin'] = "0031123456789";
                } else {
                    $cdr['origin'] = $CDR->SipRPID;
                }
            } else {
                $cdr['origin'] = "0031123456789";
                //$cdr['origin'] = $CDR->aNumberPrint;
            }
        }

        # normalize short origins
        if (preg_match("/^\d{1,3}@.*$/", $cdr['origin'])) {
            $cdr['origin']='+31000000000';
        }

        # normalize anonymous origins
        if (preg_match("/^anonymous@.*$/", $cdr['origin'])) {
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

        preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})$/", $CDR->startTime, $m);

        $cdr['start_date']  = sprintf("%s/%s/%s %s", $m[3], $m[2], $m[1], $m[4]);
        $cdr['diversion'] = '';
        preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})$/", $CDR->stopTime, $m);
        $cdr['stop_date']  = sprintf("%s/%s/%s %s", $m[3], $m[2], $m[1], $m[4]);

        $cdr['product']     = $this->product;

        # normalize duration based on billed duration
        if ($CDR->rateDuration) {
            $cdr['duration']    = $CDR->rateDuration;
        } else {
            $cdr['duration']    = $CDR->duration;
        }

        $rate_info = explode("\n", $CDR->rateInfo);

        for ($i = 0; $i < sizeof($rate_info); ++$i) {
            //dprint_r($rate_info[$i]);
            if (strpos($rate_info[$i], "ProfileId:") !== false) {
                $cdr['profile'] = ltrim(str_replace("ProfileId: ", '', $rate_info[$i]));
            }
        }

        //$cdr['extra']="$CDR->callId";

        list($cdr['username'], $cdr['domain'])= explode('@', $CDR->username);

        $cdr['charge_info'] = sprintf('"%s","%s","%s"', $CDR->price, $cdr['profile'], $CDR->destinationName);

        if ($CDR->flow == 'on-net') {
            # RFP 4.2.1

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['destination'] = '0031'.ltrim($CalleeRPID,'0');
            }

            $cdr['extra'] = $cdr['extra']."$CDR->flow";
        } elseif ($CDR->flow == 'outgoing') {
            # RFP 4.2.2

            $cdr['extra'] = $cdr['extra']."$CDR->flow";
        } elseif ($CDR->flow == 'incoming') {
            # RFP 4.2.3

            if ($this->inbound_trunks[$CDR->SourceIP]) {
                $inbound_trunk = $this->inbound_trunks[$CDR->SourceIP];
            } else {
                $inbound_trunk = 'unknown';
            }

            $cdr['username'] =  $canonical_username;

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['destination'] = '0031'.ltrim($CalleeRPID,'0');
            }

            $cdr['extra'] = $cdr['extra']."$CDR->flow";
        } elseif ($CDR->flow == 'diverted-on-net') {
            # RFP 4.2.4

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '0031'.ltrim($DiverterRPID, '0');
            } else {
                $diverter_origin = $CDR->username;
            }

            if ($CalleeRPID) {
                $cdr['c_num'] = '0031'.ltrim($CalleeRPID, '0');
            }

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;

            $cdr['diversion'] = $cdr['c_num'];

            $cdr['extra'] = $cdr['extra']."incoming-diverted-on-net";
        } elseif ($CDR->flow == 'diverted-off-net') {
            # RFP 4.2.5

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '0031'.ltrim($DiverterRPID, '0');
            } else {
                $diverter_origin = $CDR->username;
            }

            $cdr['c_num'] = $cdr['destination'];

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;
            $cdr['diversion'] = $cdr['c_num'];

            $cdr['extra'] = $cdr['extra']."incoming-diverted-off-net";
        } elseif ($CDR->flow == 'on-net-diverted-on-net') {
            # RFP 4.2.6

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '0031'.ltrim($DiverterRPID, '0');
            } else {
                $diverter_origin = $CDR->username;
            }

            $CalleeRPID=$this->getRPIDforAccount($CDR->CanonicalURI);

            if ($CalleeRPID) {
                $cdr['c_num'] = '0031'.ltrim($CalleeRPID, '0');
            }

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;
            $cdr['diversion'] = $cdr['c_num'];

            $cdr['extra'] = $cdr['extra']."$CDR->flow";
        } elseif ($CDR->flow == 'on-net-diverted-off-net') {
            # RFP 4.2.7

            $DiverterRPID=$this->getRPIDforAccount($CDR->username);

            if ($DiverterRPID) {
                $diverter_origin = '0031'.ltrim($DiverterRPID, '0');
            } else {
                $diverter_origin = $CDR->username;
            }

            $cdr['c_num']= $cdr['destination'];

            # Set destination to B-Number
            $cdr['destination'] = $diverter_origin;

            $cdr['diversion'] = $cdr['c_num'];
            $cdr['extra'] = $cdr['extra']."$CDR->flow";
        }

        $cdr['username'] = preg_replace('/caiw0+|test0+/', "", $cdr['username']);
        $cdr['origin'] = str_replace('+', '00', $cdr['origin']);
        $cdr['destination'] = str_replace('+', '00', $cdr['destination']);
        $cdr['diversion'] = str_replace('+', '00', $cdr['diversion']);

        $line = sprintf(
            '"%s","%s","%s","%s","%s","%s","%s","%s","%s",%s'."\n",
            $CDR->callId,
            $cdr['origin'],
            $cdr['username'],
            $cdr['destination'],
            $cdr['diversion'],
            $cdr['start_date'],
            $cdr['stop_date'],
            $cdr['duration'],
            $cdr['extra'],
            $cdr['charge_info']
        );

        if (!fputs($this->fp, $line)) {
            $log = sprintf(
                "CSV writter error: cannot append to file %s\n",
                $this->full_path_tmp
            );
            syslog(LOG_NOTICE, $log);

            $this->close_file();
            $this->ready = false;
            return false;
        }

        $this->lines++;

        return true;
    }

    function getRPIDforAccount($account)
    {
        if (!$account) return false;

        if ($this->rpid_cache[$account]) {
            return $this->rpid_cache[$account];
        }

        list($username, $domain) = explode('@', $account);

        $query = sprintf(
            "select * from sip_accounts where username = '%s' and domain = '%s'",
            addslashes($username),
            addslashes($domain)
        );

        if (!$this->AccountsDB->query($query)) {
            $log = sprintf(
                "Database error for query %s: %s (%s)",
                $query,
                $this->AccountsDB->Error,
                $this->AccountsDB->Errno
            );
            syslog(LOG_NOTICE, $log);
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
