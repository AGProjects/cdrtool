<?
require_once('ngnpro_soap_library.php');

/*
    Copyright (c) 2007 AG Projects
    http://ag-projects.com
    Author Adrian Georgescu

    This client library provide the functions for managing SIP accounts,
    ENUM ranges, ENUM numbers, Trusted Peers, LCR, Rating plans
    on a remote NGNPro server

    // Usage example

    // login using your favorite php session management and read data from the login function

    // login_credentials can overwrite many defaults, see SoapEngine->SoapEngine() function

    if ($adminonly) {
        $login_credentials=array(
                                'login_type'          => 'admin',
                                'reseller'            => $reseller,
                                'customer'            => $customer,
                                'extra_form_elements' => array()
                                );
    } else {
        $login_credentials=array(
                                'login_type'          => 'reseller',
                                'soap_username'        => $soapUsername,
                                'soap_password'        => $soapPassword,
                                'reseller'            => $reseller,
                                'customer'            => $customer,
                                'extra_form_elements' => array()
                                );
    
    }

    // login_credentials can overwite SoapEngine->ports
    $login_credentials['ports']['customers'] = array(
                                           'records_class' => 'Customers',
                                           'name'          => 'Login accounts',
                                           'soap_class'    => 'WebService_NGNPro_CustomerPort',
                                           'category'      => 'general',
                                           'description'   => 'Manage login accounts, customer information  and properties. Customer id can be assigned to entities like SIP domains and ENUM ranges. Use _ or % to match one or more characters. '
                                           );

    
    require_once("ngnpro_client.phtml");
    require("/etc/cdrtool/ngnpro_engines.inc");

    $extraFormElements=array();
    
    ////////////////////////////////
    // How to create a SIP record //
    ////////////////////////////////
    
    $sip_engine          = 'sip_accounts@engine';
    $this->SipSoapEngine = new SoapEngine($sip_engine,$soapEngines,$login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class(&$this->SipSoapEngine);
    
    $sipAccount = array('account'  => 'user@example.com',
                        'quota'    => $quota,
                        'prepaid'  => $prepaid,
                        'password' => $password,
                        'pstn'     => true,
                        'owner'    => $owner,
                        'customer' => $customer,
                        'reseller' => $reseller
                        );
    
    $this->sipRecords->addRecord($sipAccount);

    ////////////////////////////////
    // How to create a SIP domain //
    ////////////////////////////////
    
    $sip_engine          = 'sip_accounts@engine';
    $this->SipSoapEngine = new SoapEngine($sip_engine,$soapEngines,$login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class(&$this->SipSoapEngine);
    
    $sipDomain = array('domain'  => 'example.com',
                       'customer' => $customer,
                       'reseller' => $reseller
                      );
    
    $this->sipRecords->addRecord($sipDomain);

    ///////////////////////////////
    // How to create a SIP alias //
    ///////////////////////////////
    
    $sip_engine          = 'sip_aliases@engine';
    $this->SipSoapEngine = new SoapEngine($sip_engine,$soapEngines,$login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class(&$this->SipSoapEngine);
    
    $sipAlias = array('alias'    => 'user@example1.com',
                      'target'   => 'user@example2.com',
                      'owner'    => $owner,
                      'customer' => $customer,
                      'reseller' => $reseller
                     );
    
    $this->sipRecords->addRecord($sipAlias);
    
    ///////////////////////////////////
    // How to create an ENUM mapping //
    ///////////////////////////////////
    
    $enum_engine          = 'enum_numbers@engine';
    $this->EnumSoapEngine = new SoapEngine($enum_engine,$soapEngines,$login_credentials);
    $_enum_class          = $this->EnumSoapEngine->records_class;
    $this->enumRecords    = new $_enum_class(&$this->EnumSoapEngine);
    
    $enumMapping = array('tld'      => $tld,
                         'number'   => $number,
                         'type'     => 'sip',
                         'mapto'    => 'sip:user@example.com',
                         'owner'    => $owner,
                         'customer' => $customer,
                         'reseller' => $reseller
                        );
    
    $this->enumRecords->addRecord($enumMapping);
    
*/

class SoapEngine {

    var $version       = 1;
    var $adminonly     = 0;
    var $customer      = 0;
    var $reseller      = 0;
    var $login_type    = 'reseller';
    var $allowedPorts  = array();
    var $timeout       = 5;
    var $exception     = array();
    var $result        = false;
    var $extraFormElements = array();
    var $default_enum_tld  =  'e164.arpa';
    var $default_timezone  =  'Europe/Amsterdam';

    var $ports=array(
                         'sip_accounts'   => array(
                                           'records_class' => 'SipAccounts',
                                           'name'          => 'SIP accounts',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'sip',
                                           'description'   => 'Manage SIP accounts and their settings. Click on the SIP account to access the settings page. Use _ or % to match one or more characters. ',
                                           ),
                         'sip_domains'    => array(
                                           'records_class' => 'SipDomains',
                                           'name'          => 'SIP domains',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'sip',
                                           'description'   => 'Manage SIP domains (e.g example.com) served by the SIP Proxy. Use _ or % to match one or more characters. '
                                           ),
                         'sip_aliases'    => array(
                                           'records_class' => 'SipAliases',
                                           'name'          => 'SIP aliases',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'sip',
                                           'description'   => 'Manage aliases for SIP destinations (e.g. user1@example1.com alias to user2@example2.com). Use _ or % to match one or more characters. '
                                           ),
                         'customers'      => array(
                                           'records_class' => 'Customers',
                                           'name'          => 'Customers',
                                           'soap_class'    => 'WebService_NGNPro_CustomerPort',
                                           'category'      => 'general',
                                           'description'   => 'Manage customers, address information and properties. SIP domains and ENUM ranges can be assigned to customers. Use _ or % to match one or more characters. '
                                           ),
                         'enum_numbers'   => array(
                                           'records_class' => 'EnumMappings',
                                           'name'          => 'ENUM numbers',
                                           'soap_class'    => 'WebService_NGNPro_EnumPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage E164 numbers used for incoming calls and their mappings (e.g. +31123456789 map to sip:user@example.com). Use _ or % to match one or more characters. '
                                           ),
                         'enum_ranges'    => array(
                                           'records_class' => 'EnumRanges',
                                           'name'          => 'ENUM ranges',
                                           'soap_class'    => 'WebService_NGNPro_EnumPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage E164 number ranges that hold individual phone numbers. Use _ or % to match one or more characters. '
                                           ),
                         'dns_zones'      => array(
                                           'records_class' => 'DnsZones',
                                           'name'          => 'DNS zones',
                                           'soap_class'    => 'WebService_NGNPro_DnsPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage DNS zones. Use _ or % to match one or more characters. '
                                           ),
                         'dns_records'    => array(
                                           'records_class' => 'DnsRecords',
                                           'name'          => 'DNS records',
                                           'soap_class'    => 'WebService_NGNPro_DnsPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage DNS records. Use _ or % to match one or more characters. '
                                           ),
                         'email_aliases'    => array(
                                           'records_class' => 'EmailAliases',
                                           'name'          => 'Email aliases',
                                           'soap_class'    => 'WebService_NGNPro_DnsPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage email aliases. Use _ or % to match one or more characters. '
                                           ),
                         'url_redirect'    => array(
                                           'records_class' => 'UrlRedirect',
                                           'name'          => 'URL redirect',
                                           'soap_class'    => 'WebService_NGNPro_DnsPort',
                                           'category'      => 'dns',
                                           'description'   => 'Manage URL redirections. Use _ or % to match one or more characters. '
                                           ),
                         'trusted_peers'   => array(
                                           'records_class' => 'TrustedPeers',
                                           'name'          => 'Trusted peers',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'sip',
                                           'description'   => 'Manage trusted parties that are allowed to route sessions through the SIP proxy without digest authentication. ',
                                           'resellers_only'=> true
                                           ),

                         'pstn_carriers'  => array(
                                           'records_class' => 'Carriers',
                                           'name'          => 'PSTN carriers',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'pstn',
                                           'description'   => 'Manage outbound carriers for PSTN traffic. Click on Carier to edit its attributes. ',
                                           'resellers_only'=> true
                                           ),
                         'pstn_gateways'  => array(
                                           'records_class' => 'Gateways',
                                           'name'          => 'PSTN gateways',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'pstn',
                                           'description'   => 'Manage outbound PSTN gateways. Click on Gateway to edit its attributes. ',
                                           'resellers_only'=> true
                                           ),
                         'pstn_routes'    => array(
                                           'records_class' => 'Routes',
                                           'name'          => 'PSTN routes',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'pstn',
                                           'description'   => 'Manage outbound PSTN routes. A prefix must be formated as 00+E164, an empty prefix matches all routes. ',
                                           'resellers_only'=> true
                                           ),
                         'gateway_rules'  => array(
                                           'records_class' => 'GatewayRules',
                                           'name'          => 'PSTN rules',
                                           'soap_class'    => 'WebService_NGNPro_SipPort',
                                           'category'      => 'pstn',
                                           'description'   => 'Manage translation rules for PSTN gateways. Rules are applied against 00+E164 prefix. Click on Rule to edit its attributes. ',
                                           'resellers_only'=> true
                                           )

                        );

    function getSoapEngineAllowed($soapEngines,$filter) {
        // returns a list of allowed engines based on a filter
        // the filter format is:
        // engine1:port1,port2 engine2 engine3:port1

        if (!$filter){
            $soapEngines_checked=$soapEngines;
        } else {
    
            $_filter_els=explode(" ",$filter);
            foreach(array_keys($soapEngines) as $_engine) {
                foreach ($_filter_els as $_filter) {
                    unset($_allowed_engine);
                    $_allowed_ports=array();
    
                    list($_allowed_engine,$_allowed_ports_els) = explode(":",$_filter);
    
                    if ($_allowed_ports_els) {
                        $_allowed_ports = explode(",",$_allowed_ports_els);
                    }
    
                    if (count($_allowed_ports) == 0) {
                        $_allowed_ports=array_keys($this->ports);
                    }
    
                    if ($_engine == $_allowed_engine) {
                        $soapEngines_checked[$_engine]=$soapEngines[$_engine];
                        $this->allowedPorts[$_engine]=$_allowed_ports;
                        continue;
                    }
                }
            }
        }

        return $soapEngines_checked;
    }

    function SoapEngine($service,$soapEngines,$login_credentials=array()) {

        /*
            service is port@engine where:

            - port is an available NGNPro service
            - engine is a connection to an NGNPro server

            - soapEngines is an array of NGNPro connections and
            settings belonging to them:

            $soapEngines=array(
                                 'mdns' => array('name'        => 'Managed DNS',
                                                 'username'    => 'soapadmin',
                                                 'password'    => 'passwd',
                                                 'url'         => 'http://example.com:9200/'
                                                 )
                               );
        */

        $this->login_credentials = &$login_credentials;

        if (is_array($this->login_credentials['ports'])) {
            $_ports=array();
            foreach (array_keys($this->ports) as $_key) {
                if (in_array($_key,array_keys($this->login_credentials['ports']))) {
                    if (strlen($this->login_credentials['ports'][$_key]['records_class'])){
                        $_ports[$_key]['records_class']=$this->login_credentials['ports'][$_key]['records_class'];
                    } else {
                        $_ports[$_key]['records_class']=$this->ports[$_key]['records_class'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['soap_class'])){
                        $_ports[$_key]['soap_class']=$this->login_credentials['ports'][$_key]['soap_class'];
                    } else {
                        $_ports[$_key]['soap_class']=$this->ports[$_key]['soap_class'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['name'])){
                        $_ports[$_key]['name']=$this->login_credentials['ports'][$_key]['name'];
                    } else {
                        $_ports[$_key]['name']=$this->ports[$_key]['name'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['description'])){
                        $_ports[$_key]['description']=$this->login_credentials['ports'][$_key]['description'];
                    } else {
                        $_ports[$_key]['description']=$this->ports[$_key]['description'];
                    }
                } else {
                    $_ports[$_key]=$this->ports[$_key];
                }
            }

            $this->ports=$_ports;
        }

        //dprint_r($this->login_credentials);

        if ($this->login_credentials['login_type'] == 'admin') $this->adminonly = 1;

        if (strlen($this->login_credentials['soap_filter'])) {
            $this->soapEngines = $this->getSoapEngineAllowed($soapEngines,$this->login_credentials['soap_filter']);
        } else {
            $this->soapEngines = $soapEngines;
        }

        if (is_array($this->soapEngines)) {
            $_engines  = array_keys($this->soapEngines);

            if (!$service) {
                // use first engine available
                if (is_array($this->allowedPorts) && count($this->allowedPorts[$_engines[0]]) > 0) {
                    $_ports=$this->allowedPorts[$_engines[0]];
                } else {
                    $_ports    = array_keys($this->ports);
                }
                // default service is:
                $service   = $_ports[0].'@'.$_engines[0];
            }

            if (is_array($this->login_credentials['extra_form_elements'])) {
                $this->extraFormElements    = $this->login_credentials['extra_form_elements'];
            }

            $this->service = $service;

            $_els=explode('@',$this->service);

            if (!$_els[1]) {
                $this->soapEngine = $_engines[0];
            } else {
                $this->soapEngine = $_els[1];
            }

            $this->sip_engine = $this->soapEngine;

            if (strlen($this->soapEngines[$this->soapEngine]['version'])) {
                $this->version = $this->soapEngines[$this->soapEngine]['version'];
            }

            if ($this->version > 1) {
                $default_port='customers';
            } else {
                $default_port='sip_accounts';
            }

            if (count($this->allowedPorts[$this->soapEngine]) > 0 ) {
                if (in_array($_els[0],$this->allowedPorts[$this->soapEngine])) {
                    $this->port=$_els[0];
                } else if (in_array($default_port,$this->allowedPorts[$this->soapEngine])) {
                    $this->port = $default_port;
                } else {
                    // disable some version dependent ports
                    foreach (array_keys($this->ports) as $_p) {
                        if ($this->version < 2 && $_p == 'customers') continue;
                        if ($this->version < 4 && ($_p == 'gateway_rules' || $_p == 'pstn_gateways' || $_p == 'pstn_routes' || $p == 'pstn_carriers')) continue;

                        if (in_array($_p,$this->allowedPorts[$this->soapEngine])) {
                            $this->port = $_p;
                            break;
                        }
                    }
                }
            } else {
                if ($_els[0]) {
                    $this->port = $_els[0];
                } else {
                    $this->port = $default_port;
                }
            }

            $this->records_class   = $this->ports[$this->port]['records_class'];
            $this->soap_class      = $this->ports[$this->port]['soap_class'];

            $this->service    = $this->port.'@'.$this->soapEngine;

            foreach(array_keys($this->soapEngines) as $_key ) {
                $this->skip[$_key]=$this->soapEngines[$_key]['skip'];
                if ($this->soapEngines[$_key]['skip_ports']) {
                    $this->skip_ports[$_key]=$this->soapEngines[$_key]['skip_ports'];
                }
            }

            $this->impersonate  = intval($this->soapEngines[$this->soapEngine]['impersonate']);

			if ($this->soapEngines[$this->soapEngine]['default_enum_tld']) {
            	$this->default_enum_tld  = $this->soapEngines[$this->soapEngine]['default_enum_tld'];
            }

            if ($this->soapEngines[$this->soapEngine]['default_timezone']) {
            	$this->default_timezone  = $this->soapEngines[$this->soapEngine]['default_timezone'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['sip_engine'])) {
                $this->sip_engine=$this->soapEngines[$this->soapEngine]['sip_engine'];
            }

            if (strlen($this->login_credentials['customer_engine'])) {
                $this->customer_engine=$this->login_credentials['customer_engine'];
            } else if (strlen($this->soapEngines[$this->soapEngine]['customer_engine'])) {
                $this->customer_engine=$this->soapEngines[$this->soapEngine]['customer_engine'];
            } else if ($this->version > 1) {
                $this->customer_engine=$this->soapEngine;
            }

            if (strlen($this->soapEngines[$this->soapEngine]['sip_settings_page'])) {
                $this->sip_settings_page=$this->soapEngines[$this->soapEngine]['sip_settings_page'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['customer_properties'])) {
                $this->customer_properties=$this->soapEngines[$this->soapEngine]['customer_properties'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['timeout'])) {
                $this->timeout=intval($this->soapEngines[$this->soapEngine]['timeout']);
            }

            if (strlen($this->soapEngines[$this->soapEngine]['store_clear_text_passwords'])) {
                $this->store_clear_text_passwords=$this->soapEngines[$this->soapEngine]['store_clear_text_passwords'];
            }
			
            if (strlen($this->login_credentials['record_generator'])) {
                $this->record_generator=$this->login_credentials['record_generator'];
            } else if (strlen($this->soapEngines[$this->soapEngine]['record_generator'])) {
                $this->record_generator=$this->soapEngines[$this->soapEngine]['record_generator'];
            }

            if (strlen($this->login_credentials['name_servers'])) {
                $this->name_servers=$this->login_credentials['name_servers'];
            } else if (strlen($this->soapEngines[$this->soapEngine]['name_servers'])) {
                $this->name_servers=$this->soapEngines[$this->soapEngine]['name_servers'];
            }

            if (strlen($login_credentials['reseller'])) {
                $this->reseller = $login_credentials['reseller'];
            } else if ($this->adminonly && $_REQUEST['reseller_filter']){
                $this->reseller = $_REQUEST['reseller_filter'];
            }

            if (strlen($login_credentials['customer'])) {
                $this->customer = $login_credentials['customer'];
            } else if ($this->adminonly && $_REQUEST['customer_filter']){
                $this->customer = $_REQUEST['customer_filter'];
            }

            if (strlen($login_credentials['soap_username']) && $this->version > 1) {
                $this->soapUsername=$login_credentials['soap_username'];
                $this->SOAPlogin = array(
                                       "username"    => $this->soapUsername,
                                       "password"    => $login_credentials['soap_password'],
                                       "admin"       => false
                                       );
            } else {
                // use the credentials defined for the soap engine
                $this->soapUsername=$this->soapEngines[$this->soapEngine]['username'];
                $this->SOAPlogin = array(
                                       "username"    => $this->soapUsername,
                                       "password"    => $this->soapEngines[$this->soapEngine]['password'],
                                       "admin"       => true,
                                       "impersonate" => intval($this->reseller)
                                       );
            }

            $this->SOAPurl=$this->soapEngines[$this->soapEngine]['url'];

            $log=sprintf ("<p>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soap_class,$this->SOAPurl,$this->SOAPurl,$this->soapUsername);
            dprint($log);
            $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');


            // Instantiate the SOAP client
            if (!class_exists($this->soap_class)) return ;

            $this->soapclient = new $this->soap_class($this->SOAPurl);

            $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
            $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

            // set the timeout
            $this->soapclient->_options['timeout'] = $this->timeout;

            if ($this->customer_engine) {
                $this->SOAPloginCustomers = array(
                                       "username"    => $this->soapEngines[$this->customer_engine]['username'],
                                       "password"    => $this->soapEngines[$this->customer_engine]['password'],
                                       "admin"       => true,
                                       "impersonate" => intval($this->reseller)
                                       );

                $this->SoapAuthCustomers = array('auth', $this->SOAPloginCustomers , 'urn:AGProjects:NGNPro', 0, '');

                $this->SOAPurlCustomers    = $this->soapEngines[$this->customer_engine]['url'];
                $this->soapclientCustomers = new WebService_NGNPro_CustomerPort($this->SOAPurlCustomers);

                $this->soapclientCustomers->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclientCustomers->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

                if (strlen($this->soapEngines[$this->customer_engine]['timeout'])) {
                    $this->soapclientCustomers->_options['timeout'] = intval($this->soapEngines[$this->customer_engine]['timeout']);
                } else {
                    $this->soapclientCustomers->_options['timeout'] = $this->timeout;
                }
            }

        } else {
            print "<font color=red>Error: No SOAP credentials defined.</font>";
        }

        $this->url = $_SERVER['PHP_SELF']."?1=1";

        foreach (array_keys($this->extraFormElements) as $element) {
            if (!strlen($this->extraFormElements[$element])) continue;
            $this->url  .= sprintf('&%s=%s',$element,urlencode($this->extraFormElements[$element]));
        }

        $this->support_email   = $this->soapEngines[$this->soapEngine]['support_email'];
        $this->support_web     = $this->soapEngines[$this->soapEngine]['support_web'];
        $this->welcome_message = $this->soapEngines[$this->soapEngine]['welcome_message'];

    }

    function execute($function,$html=true) {

        /*
        $function=array('commit'   => array('name'       => 'addAccount',
                                            'parameters' => array($param1,$param2),
                                            'logs'       => array('success' => 'The function was a success',
                                                                  'failure' => 'The function has failed'
                                                                  )
                                           )

                        );
        */

        if (!$function['commit']['name']) {
            if ($html) {
                print "<font color=red>Error: no function name supplied</font>";
            } else {
                print "Error: no function name supplied\n";
            }
            return false;
        }

        $this->soapclient->addHeader($this->SoapAuth);
        $result = call_user_func_array(array(&$this->soapclient,$function['commit']['name']),$function['commit']['parameters']);

        if (PEAR::isError($result)) {
            $this->error_msg   = $result->getMessage();
            $this->error_fault = $result->getFault();
            $this->error_code  = $result->getCode();

            $this->exception   = $this->error_fault->detail->exception;

            if ($html) {
                printf ("<p><font color=red>Error from %s: %s (%s): %s</font>\n",$this->SOAPurl,$this->error_msg, $this->error_fault->detail->exception->errorcode,$this->error_fault->detail->exception->errorstring);                return false;
            }
            return false;

        } else {
        	$this->result=$result;

            if ($function['commit']['logs']['success']) {
                if ($html) {
                    printf ("<p><font color=green>%s </font>\n",htmlentities($function['commit']['logs']['success']));
                }
            }

            if (is_object($result) || strlen($result)) {
        		return $result;
            } else {
                return true;
            }
        }
    }
}

class Records {
    var $maxrowsperpage     = '20';
    var $sip_settings_page  = 'sip_settings.phtml';
    var $allowedDomains     = array();
    var $selectionActive    = false;
    var $selectionKeys      = array();
    var $resellers          = array();
    var $customers          = array();
    var $record_generator   = false;
    var $customer_properties = array();
    var $loginProperties    = array();
    var $errorMessage       = '';
    var $html               = true;
    var $filters            = array();
    var $selectionActiveExceptions    = array();

    function Records(&$SoapEngine) {

        $this->SoapEngine          = &$SoapEngine;

        $this->version             = &$this->SoapEngine->version;
        $this->login_credentials   = &$this->SoapEngine->login_credentials;

        $this->sorting['sortBy']    = trim($_REQUEST['sortBy']);
        $this->sorting['sortOrder'] = trim($_REQUEST['sortOrder']);

        $this->next       = $_REQUEST['next'];

        $this->adminonly   = $this->SoapEngine->adminonly;
        $this->reseller    = $this->SoapEngine->reseller;
        $this->customer    = $this->SoapEngine->customer;
        $this->impersonate = $this->SoapEngine->impersonate;
        $this->url         = $this->SoapEngine->url;

        foreach(array_keys($this->filters) as $_filter) {
            if (strlen($this->filters[$_filter]) && !in_array($_filter,$this->selectionActiveExceptions)) {
                $this->selectionActive=true;
                break;
            }
        }

        if ($this->adminonly) {
            $this->url .= sprintf('&adminonly=%s',$this->adminonly);
            if ($this->login_credentials['reseller']) {
                $this->filters['reseller']=$this->login_credentials['reseller'];
            } else {
                $this->filters['reseller']=trim($_REQUEST['reseller_filter']);
            }
        }

        $this->filters['customer'] = trim($_REQUEST['customer_filter']);

        //$this->getResellers();

        $this->getCustomers();

        $this->getLoginAccount();

        if (strlen($this->SoapEngine->sip_settings_page)) {
            $this->sip_settings_page=$this->SoapEngine->sip_settings_page;
        }

        $this->support_email   = $this->SoapEngine->support_email;
        $this->support_web     = $this->SoapEngine->support_web;

    }

    function showEngineSelection() {
        $selected_soapEngine[$this->SoapEngine->service]=' selected';

        printf("
        <script type=\"text/JavaScript\">
        function jumpMenu(){
            location.href=\"%s&service=\" + document.engines.service.options[document.engines.service.selectedIndex].value;
        }
        </script>",
        $this->url
        );

        $pstn_access=$this->getCustomerProperty('pstn_access');

        printf("<select name='service' onChange=\"jumpMenu('this.form')\">\n");

        $j=1;
        foreach (array_keys($this->SoapEngine->soapEngines) as $_engine) {
        	if ($this->SoapEngine->skip[$_engine]) continue;
            if ($j > 1) printf ("<option value=''>--------\n");
            foreach (array_keys($this->SoapEngine->ports) as $_port) {
                $idx=$_port.'@'.$_engine;
                if (is_array($this->SoapEngine->skip_ports[$_engine]) && in_array($_port,$this->SoapEngine->skip_ports[$_engine])) continue;

                if ($this->login_credentials['login_type'] !='admin') {
                    if (!$pstn_access && (preg_match("/^pstn_/",$_port))) continue;
                }
                if (count($this->SoapEngine->allowedPorts[$_engine]) > 0 && !in_array($_port,$this->SoapEngine->allowedPorts[$_engine])) continue;

                // disable some version dependent ports

                if ($_port == 'customers' && $this->SoapEngine->soapEngines[$_engine]['version'] < 2) continue;
                if ($this->version < 4 && ($_port == 'gateway_rules' || $_port == 'pstn_gateways' || $_port == 'pstn_routes' || $_port == 'pstn_carriers' || $_port == 'gateway_groups')) continue;

                if ($this->SoapEngine->ports[$_port]['resellers_only']) {
                    if ($this->login_credentials['login_type']=='admin' || $this->loginAccount->resellerActive) {
                        printf ("<option value=\"%s@%s\"%s>%s@%s\n",$_port,$_engine,$selected_soapEngine[$idx],$this->SoapEngine->ports[$_port]['name'],$this->SoapEngine->soapEngines[$_engine]['name']);
                    }
                } else {
            		printf ("<option value=\"%s@%s\"%s>%s@%s\n",$_port,$_engine,$selected_soapEngine[$idx],$this->SoapEngine->ports[$_port]['name'],$this->SoapEngine->soapEngines[$_engine]['name']);
        		}
            }

            $j++;
        }
        printf ("</select>");

    }

    function showAfterEngineSelection () {
    }

    function showCustomerSelection() {
        if ($this->version > 1) {
            $this->showCustomerForm();
        }
    }

    function showResellerSelection() {
        if ($this->version > 1) {
            if ($this->adminonly) {
                $this->showResellerForm();
            } else {
                printf ("%s",$this->reseller);
            }
        }
    }

    function showPagination($maxrows) {

        $url .= $this->url.'&'.$this->addFiltersToURL().
        sprintf("&service=%s&sortBy=%s&sortOrder=%s",
        urlencode($this->SoapEngine->service),
        urlencode($this->sorting['sortBy']),
        urlencode($this->sorting['sortOrder'])
        );

        print "
        <p>
        <table border=0 align=center>
        <tr>
        <td>
        ";

        if ($this->next != 0  ) {
            $show_next=$this->maxrowsperpage-$this->next;
            if  ($show_next < 0)  {
                $mod_show_next  =  $show_next-2*$show_next;
            }
            if (!$mod_show_next) $mod_show_next=0;

            if ($mod_show_next/$this->maxrowsperpage >= 1) {
                printf ("<a href='%s&next=0'>Begin</a> ",$url);
            }

            printf ("<a href='%s&next=%s'>Previous</a> ",$url,$mod_show_next);
        }
        
        print "
        </td>
        <td>
        ";

        if ($this->next + $this->maxrowsperpage < $this->rows)  {
            $show_next = $this->maxrowsperpage + $this->next;
            printf ("<a href='%s&next=%s'>Next</a> ",$url,$show_next);
        }

        print "
        </td>
        </tr>
        </table>
        ";
    }

    function showSeachFormCustom() {
    }

    function showSeachForm() {

    	printf ("<p><b>%s</b>",
        $this->SoapEngine->ports[$this->SoapEngine->port]['description']);

        print "
        <p>
        <table border=0 bgcolor=lightgreen class=border width=100%>
        <tr>

        ";
        printf ("<form method=post name=engines action=%s>",$_SERVER['PHP_SELF']);
        print "
        <td align=left>
        ";
        print "
        <input type=submit name=action value=Search>
        ";

        $this->showEngineSelection();

        $this->showAfterEngineSelection();

        print "
        </td>

        <td align=right>
		Order by";
        $this->showSortForm();

        $this->printHiddenFormElements('skipServiceElement');

        print "
        </td>
        </tr>
        <tr>
        <td colspan=2>
        ";

    	$this->showTextBeforeCustomerSelection();
        $this->showCustomerSelection();
        $this->showResellerSelection();

        $this->showSeachFormCustom();
        print "
        </td>
		</tr>
        </form>
        </table>
        ";

        if ($_REQUEST['action'] != 'Delete') $this->showAddForm();
    }

    function listRecords() {
    }

    function getRecordKeys() {
    }

    function addRecord($dictionary=array()) {
    }

    function deleteRecord() {
    }

    function tel2enum($tel,$tld) {

        if (strlen($tld) == 0)  $tld="e164.arpa";

        // transform telephone number in FQDN Enum style domain name
        if (preg_match("/^[+]?(\d+)$/",$tel,$m)) {
            $l=strlen($m[1]);
            $rev_num="";
            $z=0;
            while ($z < $l) {
                $ss=substr($m[1],$z,1);
                $enum=$ss.".".$enum;
                $z++;
            }
            preg_match("/^(.*)\.$/",$enum,$m);
            $enum=$m[1];
            $enum=$enum.".$tld.";
            return($enum);
         } else {
            return($tel);
         }
    }

    function showAddForm() {
        if ($this->selectionActive) return;
    }

    function showSortForm() {

        if (!count($this->sortElements)) {
            return;
        }

        $selected_sortBy[$this->sorting['sortBy']]='selected';

        print "<select name=sortBy>";
        foreach (array_keys($this->sortElements) as $key) {
            printf ("<option value='%s' %s>%s",$key,$selected_sortBy[$key],$this->sortElements[$key]);
        }
        print "</select>";

        $selected_sortOrder[$this->sorting['sortOrder']]='selected';
        print "<select name=sortOrder>";
        printf ("<option value='DESC' %s>DESC",$selected_sortOrder['DESC']);
        printf ("<option value='ASC' %s>ASC",$selected_sortOrder['ASC']);
        print "</select>";
    }

    function showTimezones() {
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }
        print "<select name=timezone>";
        print "<option>";
        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);
            if ($this->timezone==$buffer) {
                $selected="selected";
            } else {
                $selected="";
            }
            printf ("<option %s>%s>",$selected,$buffer);
        }
        print "</select>";
        fclose($fp);

    }

    function printHiddenFormElements ($skipServiceElement='') {
        if (!$skipServiceElement) {
            printf("<input type=hidden name=service value='%s'>",$this->SoapEngine->service);
        }

        if ($this->adminonly) {
            printf("<input type=hidden name=adminonly value='%s'>",$this->adminonly);
        }

        foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
            if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
            printf ("<input type=hidden name=%s value='%s'>\n",$element,$this->SoapEngine->extraFormElements[$element]);
        }

    }

    function getAllowedDomains() {
    }

    function showActionsForm() {
        if (!$this->selectionActive) {
            return;
        }

        $class_name=get_class($this).'Actions';

        if (class_exists($class_name)) {
            $actions=new $class_name(&$this->SoapEngine);
            $actions->showActionsForm($this->filters,$this->sorting);
        }
    }

    function executeActions() {
        $this->showSeachForm();

        $this->getRecordKeys();

        dprint_r($this->selectionKeys);

        $class_name=get_class($this).'Actions';

        if (class_exists($class_name)) {
            $actions=new $class_name(&$this->SoapEngine);
            $actions->execute(&$this->selectionKeys,$_REQUEST['sub_action'],trim($_REQUEST['sub_action_parameter']));
        }
    }

    function getCustomers() {
        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }
        if (!$this->filters['reseller']) return;

        // Filter
        $filter=array('reseller'=>intval($this->filters['reseller']));

        $range=array('start' => 0,
                     'count' => 100
                     );

        // Order
        $orderBy = array('attribute' => 'customer',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);

        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getCustomers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            if ($result->total > $range['count']) return;

            if ($range['count'] <= $result->total) {
                $max=$range['count'];
            } else {
                $max=$result->total;
            }

            $i=0;
            while ($i < $max)  {
                $customer=$result->accounts[$i];
                $this->customers[$customer->id] = $customer->firstName.' '.$customer->lastName;
                $i++;
            }
            return true;
        }
    }

    function getResellers() {
        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!$this->adminonly) return;
        // Filter

        $filter=array('reseller'=>intval($this->filters['reseller']));

        $range=array('start' => 0,
                     'count' => 200
                     );

        // Order
        $orderBy = array('attribute' => 'customer',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);

        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getResellers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            //if ($result->total > $range['count']) return;

            if ($range['count'] <= $result->total) {
                $max=$range['count'];
            } else {
                $max=$result->total;
            }

            $i=0;
            while ($i < $max)  {
                $reseller = $result->accounts[$i];

                if (strlen($reseller->organization) && $reseller->organization!= 'N/A') {
                    $this->resellers[$reseller->id] = $reseller->organization;
                } else {
                    $this->resellers[$reseller->id] = $reseller->firstName.' '.$reseller->lastName;
                }
                $i++;
            }

            dprint_r($this->resellers);
            return true;
        }
    }

    function getLoginAccount() {
        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!$this->customer) {
            //print ("No customer available");
            return true;
        }

        $filter=array('customer'=>intval($this->customer));

        $range=array('start' => 0,
                     'count' => 100
                     );

        // Order
        $orderBy = array('attribute' => 'customer',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );


        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);

        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getResellers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $this->loginAccount=$result->accounts[0];
            $this->loginProperties=$this->loginAccount->properties;
        }
    }

    function showCustomerForm($name='customer_filter') {
        if ($this->login_credentials['customer'] != $this->login_credentials['reseller']) {
            printf ("%s",$this->login_credentials['customer']);
        } else {
            if (count($this->customers)) {
                $select_customer[$this->filters['customer']]='selected';
                printf ("<select name=%s>",$name);
                print "<option>";
                foreach (array_keys($this->customers) as $_res) {
                    printf ("<option value='%s' %s>%s (%s)\n",$_res,$select_customer[$_res],$_res,$this->customers[$_res]);
                }
                print "</select>";
            } else {
                printf ("<input type=text size=7 name=%s value='%s'>",$name,$this->filters['customer']);
            }
        }
    }

    function showResellerForm($name='reseller_filter') {
        if (!$this->adminonly) return;
        if ($this->login_credentials['reseller']) {
            printf ("%s",$this->login_credentials['reseller']);
        } else {
            if (count($this->resellers)) {
                $select_reseller[$this->filters['reseller']]='selected';
                printf ("<select name=%s>",$name);
                print "<option>";
                foreach (array_keys($this->resellers) as $_res) {
                    printf ("<option value='%s' %s>%s (%s)\n",$_res,$select_reseller[$_res],$_res,$this->resellers[$_res]);
                }
                print "</select>";
            } else {
                printf ("<input type=text size=7 name=%s value='%s'>",$name,$this->filters['reseller']);
            }
        }
    }
    function showTextBeforeCustomerSelection() {
        print "Customer";
    }

    function addFiltersToURL() {

        $j=0;
        foreach(array_keys($this->filters) as $filter) {
            if (strlen(trim($this->filters[$filter]))) {
                if ($j) $url .='&';
                $url .= sprintf('%s_filter=%s',$filter,urlencode(trim($this->filters[$filter])));
            }
            $j++;
        }

        return $url;
    }

    function printFiltersToForm() {
        foreach(array_keys($this->filters) as $filter) {
            if (strlen(trim($this->filters[$filter]))) {
                printf("<input type=hidden name=%s_filter value='%s'>",$filter,trim($this->filters[$filter]));
            }
        }
    }

    function getRecord () {
    }

    function updateRecord () {
    }

    function copyRecord () {
    }

    function showRecord () {
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

    function RandomNumber($len=5) {
        $alf=array("0","1","2","3","4","5",
                   "9","8","7","6");
        $i=0;
        while($i < $len) {
            srand((double)microtime()*1000000);
            $randval = rand(0,9);
            $string="$string"."$alf[$randval]";
            $i++;
        }          
        return $string;
    }

    function validDomain($domain) {
           if (!preg_match ("/^[A-Za-z0-9-.]{1,}\.[A-Za-z]{2,}$/",$domain)) {
            return false;
        }

        return true;
    }

    function getCarriers () {
        if (count($this->carriers)) return true;

        $Query=array('filter'  => array('name'=>''),
                     'orderBy' => array('attribute' => 'name',
                                        'direction' => 'ASC'
                                  ),
                     'range'   => array('start' => 0,
                                        'count' => 1000)
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getCarriers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->carriers as $_carrier) {
                $this->carriers[$_carrier->id]=$_carrier->name;
            }
        }
    }

    function getGateways () {
        if (count($this->gateways)) return true;

        $Query=array('filter'  => array('name'=>''),
                     'orderBy' => array('attribute' => 'name',
                                        'direction' => 'ASC'
                                  ),
                     'range'   => array('start' => 0,
                                        'count' => 1000)
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->gateways as $_gateway) {
                $this->gateways[$_gateway->id]=sprintf("%s, Carrier %s",$_gateway->name,$_gateway->carrier);
            }
        }
    }

    function updateBefore () {
        return true;
    }

    function updateAfter () {
        return true;
    }

    function showCustomerTextBox () {
        if ($this->version < 2) return;
        print "Customer";
        if ($this->adminonly) {
            $this->showCustomerForm('customer');
            print ".";
            $this->showResellerForm('reseller');
        } else {
            $this->showCustomerForm('customer');
        }
    }

    function makebar($w) {
        if ($w < 0) $w = 0;
        if ($w > 100) $w = 100;
        $width = $w;
        $extra = 100 - $w;
        if ($width < 50)
            $color = "black";
        else if ($width < 70)
            $color = "darkred";
        else
            $color = "red";
        return "
        <table class=bar cellspacing=0>
        <tr>
        <td class=$color width=$width></td>
        <td class=white width=$extra>
        </td>
        </tr>
        </table>
        ";
    }

    function customerFromLogin($dictionary=array()) {

        if ($this->login_credentials['reseller']) {
            $reseller = $this->login_credentials['reseller'];

            if ($dictionary['customer']) {
                $customer = $dictionary['customer'];
            } else if ($_REQUEST['customer']) {
                $customer = $_REQUEST['customer'];
            } else {
                $customer = $this->login_credentials['customer'];
            }

        } else {
            if ($dictionary['reseller']) {
                $reseller = $dictionary['reseller'];
            } else {
                $reseller = trim($_REQUEST['reseller']);
            }

            if ($dictionary['customer']) {
                $customer = $dictionary['customer'];
            } else {
                $customer = trim($_REQUEST['customer']);
            }
        }

        if (!$customer) $customer = $reseller;

        return array(intval($customer),intval($reseller));

    }

    function getCustomerProperties($customer='') {
        if (!$customer) $customer=$this->customer;

        $log=sprintf("getCustomerProperties(%s,engine=%s)",$customer,$this->SoapEngine->customer_engine);
        dprint($log);

        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!$customer) {
            dprint ("No customer available");
            return true;
        }

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $result     = $this->SoapEngine->soapclientCustomers->getProperties(intval($customer));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $this->loginProperties=$result;
        }

        /*
        print "<pre>";
        print_r($this->loginProperties);
        print "</pre>";
        */

        return true;
    }

    function setCustomerProperties($properties,$customer='') {
        if (!$customer) $customer=$this->customer;

        $log=sprintf("setCustomerProperties(%s,engine=%s)",$customer,$this->SoapEngine->customer_engine);
        dprint($log);

        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!is_array($properties) || !$customer) return true;

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $result     = $this->SoapEngine->soapclientCustomers->setProperties(intval($customer),$properties);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }

    function getCustomerProperty($name='') {
        if (!count($this->loginProperties)) return false;

        foreach ($this->loginProperties as $_property) {
            if ($_property->name == $name) {
                return $_property->value;
            }
        }

        return false;
    }

    function checkRecord() {
        return true;
    }

    function showWelcomeMessage() {
        if (!strlen($this->SoapEngine->welcome_message)) return ;
        printf ("%s",$this->SoapEngine->welcome_message);
    }

    function print_w($obj) {
        print "<pre>\n";
        print_r($obj);
        print "</pre>\n";
    }

    function initRemoteReplicationEngine($reseller) {
        if (!$reseller) return false;
        if (!$this->remote_engine_name)  return false;

        dprint_r($this->SoapEngine->login_credentials['reseller_filters'][$reseller]);

        $remote_engine=$this->SoapEngine->login_credentials['reseller_filters'][$reseller][$this->remote_engine_name];

        if (!strlen($remote_engine)) {
        	return false;
        }

        if ($this->SoapEngine->soapEngine == $remote_engine) {
        	return false;
        }

        if (!in_array($remote_engine,array_keys($this->SoapEngine->soapEngines))) {
        	return false;
        }

        $this->SOAPloginRemote = array(
                               "username"    => $this->SoapEngine->soapEngines[$remote_engine]['username'],
                               "password"    => $this->SoapEngine->soapEngines[$remote_engine]['password'],
                               "admin"       => true,
                               "impersonate" => intval($reseller)
                           );

        $this->SOAPurlRemote=$this->SoapEngine->soapEngines[$remote_engine]['url'];

        $log=sprintf ("and syncronize changes to <a href=%swsdl target=wsdl>%s</a>",$this->SOAPurlRemote,$this->SOAPurlRemote);
        dprint($log);

        $this->SoapAuthRemote  = array('auth', $this->SOAPloginRemote , 'urn:AGProjects:NGNPro', 0, '');

        $this->SoapEngineRemote = new $this->SoapEngine->soap_class($this->SOAPurlRemote);

        if (strlen($this->soapEngines[$remote_engine]['timeout'])) {
            $this->SoapEngineRemote->_options['timeout'] = intval($this->soapEngines[$remote_engine]['timeout']);
        } else {
            $this->SoapEngineRemote->_options['timeout'] = $this->soapTimeout;
        }

        $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        return true;
    }
}

class SipDomains extends Records {
    var $FieldsAdminOnly=array(
                              'reseller' => array('type'=>'integer'),
                              );

    var $Fields=array(
                              'customer'    => array('type'=>'integer')
                              );

    function SipDomains(&$SoapEngine) {
        dprint("init Domains");

        $this->filters   = array(
                               'domain'       => strtolower(trim($_REQUEST['domain_filter']))
                               );

        $this->Records(&$SoapEngine);

        if ($this->version > 1) {
            // keep default maxrowsperpage
            $this->sortElements=array('changeDate' => 'Change date',
                                        'domain'     => 'Domain'
                                     );

        } else {
            $this->maxrowsperpage = 10000;
        }


    }

    function listRecords() {

        $this->showSeachForm();
    
        if ($this->version > 1) {

            // Filter
            $filter=array(
                          'domain'    => $this->filters['domain'],
                          'customer'  => intval($this->filters['customer']),
                          'reseller'  => intval($this->filters['reseller'])
                          );
    
            // Range
            $range=array('start' => intval($this->next),
                         'count' => intval($this->maxrowsperpage)
                         );
    
            // Order
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
    
            $orderBy = array('attribute' => $this->sorting['sortBy'],
                             'direction' => $this->sorting['sortOrder']
                             );
    
            // Compose query
            $Query=array('filter'     => $filter,
                            'orderBy' => $orderBy,
                            'range'   => $range
                            );
            dprint_r($Query);
    
            // Insert credetials
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
    
            // Call function
            $result     = $this->SoapEngine->soapclient->getDomains($Query);
        } else {
            // Insert credetials
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
    
            // Call function
            $result     = $this->SoapEngine->soapclient->getDomains();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            if ($this->version > 1) {
                $this->rows = $result->total;
            } else {
                $this->rows = count($result);
            }

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
            ";
            if ($this->version > 1) {
                print "
                <td><b>Id</b></th>
                <td><b>Customer</b></td>
                <td colspan=3><b>Domain</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
                ";
            } else {
                print "
                <td><b>Id</b></th>
                <td colspan=3><b>Domain</b></td>
                <td><b>Actions</b></td>
                ";
            }
                print "
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {

                    if ($this->version > 1) {
                        if (!$result->domains[$i]) break;
                        $domain = $result->domains[$i];
                    } else {
                        $domain = $result[$i];
                    }

                    $index = $this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    if ($this->version > 1) {
                        $_url = $this->url.sprintf("&service=%s&action=Delete&domain_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($domain->domain)
                        );

                        if ($_REQUEST['action'] == 'Delete' &&
                            $_REQUEST['domain_filter'] == $domain->domain) {
                            $_url .= "&confirm=1";
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }
    
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($domain->customer)
                        );

                        $_sip_domains_url = $this->url.sprintf("&service=sip_domains@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                        );

                        $_sip_accounts_url = $this->url.sprintf("&service=sip_accounts@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                        );

                        $_sip_aliases_url = $this->url.sprintf("&service=sip_aliases@%s&alias_domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                        );
    
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td><a href=%s>Sip accounts</a></td>
                        <td><a href=%s>Sip aliases</a></td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $_customer_url,
                        $domain->customer,
                        $domain->reseller,
                        $_sip_domains_url,
                        $domain->domain,
                        $_sip_accounts_url,
                        $_sip_aliases_url,
                        $domain->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        $_url = $this->url.sprintf("&service=%s&action=Delete&domain_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($domain)
                        );

                        $_sip_accounts_url = $this->url.sprintf("&service=sip_accounts@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain)
                        );

                        $_sip_aliases_url = $this->url.sprintf("&service=sip_aliases@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain)
                        );

    
                        if ($_REQUEST['action'] == 'Delete' &&
                            $_REQUEST['domain_filter'] == $domain) {
                            $_url .= "&confirm=1";
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>Sip accounts</a></td>
                        <td><a href=%s>Sip aliases</a></td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $domain,
                        $_sip_accounts_url,
                        $_sip_aliases_url,
                        $_url,
                        $actionText
                        );
                    }

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1 && $this->version > 1) {
                $this->showRecord($domain);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showSeachFormCustom() {

        if ($this->version > 1) {
            printf (" SIP domain<input type=text size=20 name=domain_filter value='%s'>",$this->filters['domain']);
        }
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['domain']) {
            $domain=$dictionary['domain'];
        } else {
            $domain=$this->filters['domain'];
        }

        if (!strlen($domain)) {
            print "<p><font color=red>Error: missing SIP domain. </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteDomain',
                                            'parameters' => array($domain),
                                            'logs'       => array('success' => sprintf('SIP domain %s has been deleted',$domain))
                                           )
                                           );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showAddForm() {
        if ($this->selectionActive) return;
        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";

            $this->showCustomerTextBox();

            printf (" SIP domain<input type=text size=20 name=domain>");

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";

            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {

        if ($dictionary['domain']) {
            $domain = $dictionary['domain'];
        } else {
            $domain = trim($_REQUEST['domain']);
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        if (!$this->validDomain($domain)) {
            print "<font color=red>Error: invalid domain name</font>";
            return false;
        }

        if ($this->version > 1) {
            $domainStructure = array('domain'   => strtolower($domain),
                                     'customer' => intval($customer),
                                     'reseller' => intval($reseller)
                                    );
        } else {
            $domainStructure = strtolower($domain);
        }

        $function=array('commit'   => array('name'       => 'addDomain',
                                            'parameters' => array($domainStructure),
                                            'logs'       => array('success' => sprintf('SIP domain %s has been added',$domain)))
                                           );

        return $this->SoapEngine->execute($function,$this->html);

    }

    function getRecordKeys() {

        if ($this->version > 1) {
            // Filter
            $filter=array(
                          'domain'    => $this->filters['domain'],
                          'customer'  => intval($this->filters['customer']),
                          'reseller'  => intval($this->filters['reseller'])
                          );
    
            // Range
            $range=array('start' => intval($this->next),
                         'count' => intval($this->maxrowsperpage)
                         );
    
            // Order
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
    
            $orderBy = array('attribute' => $this->sorting['sortBy'],
                             'direction' => $this->sorting['sortOrder']
                             );

            // Compose query
            $Query=array('filter'     => $filter,
                            'orderBy' => $orderBy,
                            'range'   => $range
                            );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains($Query);

        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error in getAllowedDomains from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            //return false;
        } else {
            if ($this->version > 1) {
                foreach ($result->domains as $_domain) {
                    $this->selectionKeys[]=$_domain->domain;
                }
            } else {
                $this->selectionKeys[]=$result;
            }
        }
    }

    function getRecord($domain) {
        if ($this->version < 2) return false;

        // Filter
        $filter=array(
                      'domain'    => $domain
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );
        dprint_r($Query);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->domains[0]){
                return $result->domains[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($domain) {

        print "<table border=0 cellpadding=10>";
        print "
        <tr>
        <td valign=top>
        <table border=0>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        if ($this->adminonly) {

            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name=$this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name=ucfirst($item);
                }
    
                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                    </tr>",
                    $item_name,
                    $item,
                    $domain->$item
                    );
                } else {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    </tr>",
                    $item_name,
                    $item,
                    $domain->$item
                    );
                }
            }
        }

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                </tr>",
                $item_name,
                $item,
                $domain->$item
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                </tr>",
                $item_name,
                $item,
                $domain->$item
                );
            }
        }

        printf ("<input type=hidden name=domain_filter value='%s'",$domain->domain);
        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function updateRecord () {
        //print "<p>Updating domain ...";

        if (!$_REQUEST['domain_filter']) return false;

        if (!$domain = $this->getRecord($_REQUEST['domain_filter'])) {
            return false;
        }

        $domain_old=$domain;

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $domain->$item = intval($_REQUEST[$var_name]);
            } else {
                $domain->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name=$item.'_form';
                //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $domain->$item = intval($_REQUEST[$var_name]);
                } else {
                    $domain->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function=array('commit'   => array('name'       => 'updateDomain',
                                            'parameters' => array($domain),
                                            'logs'       => array('success' => sprintf('Domain %s has been updated',$domain->domain)))
                        );

        return $this->SoapEngine->execute($function,$this->html);

    }

}

class SipAccounts extends Records {
    var $selectionActiveExceptions=array('domain');

    var $sortElements=array('changeDate' => 'Change date',
                            'username'   => 'Username',
                            'domain'     => 'Domain'
                            );

    var $store_clear_text_passwords=true;

    function SipAccounts(&$SoapEngine) {
        dprint("init SipAccounts");


        $this->filters = array('username' => strtolower(trim($_REQUEST['username_filter'])),
                               'domain'   => strtolower(trim($_REQUEST['domain_filter'])),
                               'firstname'=> trim($_REQUEST['firstname_filter']),
                               'lastname' => trim($_REQUEST['lastname_filter']),
                               'email'    => trim($_REQUEST['email_filter']),
                               'owner'    => trim($_REQUEST['owner_filter']),
                               'customer' => trim($_REQUEST['customer_filter']),
                               'reseller' => trim($_REQUEST['reseller_filter'])
                              );

        $this->Records(&$SoapEngine);

		if (strlen($this->SoapEngine->store_clear_text_passwords)) {
        	$this->store_clear_text_passwords=$this->SoapEngine->store_clear_text_passwords;
        }
    }

    function getRecordKeys() {

        if (preg_match("/^(.*)@(.*)$/",$this->filters['username'],$m)) {
            $this->filters['username'] = $m[1];
            $this->filters['domain']   = $m[2];
        }

        // Filter
        $filter=array('username' => $this->filters['username'],
                      'domain'   => $this->filters['domain'],
                      'firstName'=> $this->filters['firstname'],
                      'lastName' => $this->filters['lastname'],
                      'email'    => $this->filters['email'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getAccounts($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->accounts as $account) {
                $this->selectionKeys[]=array('username' => $account->id->username,
                                             'domain'   => $account->id->domain
                                             );
            }

            return true;
        }

        return false;
    }

    function listRecords() {
        $this->getAllowedDomains();

        if (preg_match("/^(.*)@(.*)$/",$this->filters['username'],$m)) {
            $this->filters['username'] = $m[1];
            $this->filters['domain']   = $m[2];
        }

        $this->showSeachForm();

        // Filter
        $filter=array('username' => $this->filters['username'],
                      'domain'   => $this->filters['domain'],
                      'firstName'=> $this->filters['firstname'],
                      'lastName' => $this->filters['lastname'],
                      'email'    => $this->filters['email'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getAccounts($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                <td><b>Account</b></td>
                <td><b>Full name</b></td>
                <td><b>Email</b></td>
                <td><b>Timezone</b></td>
                <td><b>Features</b></td>
                <td align=right><b>Quota</b></td>
                <td align=right><b>Balance</b></td>
                <td><b>Owner</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }


            if ($this->rows) {
            	$i=0;

                if ($this->version > 1) {

                    $_prepaid_accounts=array();
                    while ($i < $maxrows)  {
                        if (!$result->accounts[$i]) break;
                        $account = $result->accounts[$i];
                        if ($account->prepaid) {
                            $_prepaid_accounts[]=array("username" => $account->id->username,
                                                       "domain" => $account->id->domain
                                                      );
                        }
                        $i++;
                    }
    
                    if (count($_prepaid_accounts)) {
                        // Insert credetials
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                
                        // Call function
                        $result1     = $this->SoapEngine->soapclient->getPrepaidStatus($_prepaid_accounts);
                        if (!PEAR::isError($result1)) {
                            $j=0;
    
                            foreach ($result1 as $_account) {
                                $_sip_account=sprintf("%s@%s",$_prepaid_accounts[$j]['username'],$_prepaid_accounts[$j]['domain']);
                                $_prepaid_balance[$_sip_account]=$_account->balance;
                                $j++;
                            }
                        }
                    }
                }

            	$i=0;

                while ($i < $maxrows)  {
    
                    if (!$result->accounts[$i]) break;
    
                    $account = $result->accounts[$i];
    
                    $index=$this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

			        $_url = $this->url.'&'.$this->addFiltersToURL().sprintf("&service=%s&action=Delete",
                    urlencode($this->SoapEngine->service)
                    );

                    if (!$this->filters['domain']) {
			        	$_url .= sprintf("&domain_filter=%s",urlencode($account->id->domain));
                    }

                    if (!$this->filters['username']) {
                    	$_url .= sprintf("&username_filter=%s",urlencode($account->id->username));
                    }

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['username_filter'] == $account->id->username &&
                        $_REQUEST['domain_filter'] == $account->id->domain) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($account->reseller) {
                        $resellersip_settings_page=$account->reseller;
                    } else if ($this->SoapEngine->impersonate) {
                        // use the reseller from the soap engine
                        $resellersip_settings_page=$this->SoapEngine->impersonate;
                    } else {
                        // use the reseller from the login
                        $resellersip_settings_page=$this->reseller;
                    }

                    if ($this->sip_settings_page) {
                        $url=sprintf('%s?account=%s@%s&sip_engine=%s',
                        $this->sip_settings_page,$account->id->username,$account->id->domain,$this->SoapEngine->sip_engine);

                        if ($this->adminonly) {
                        	$url  .= sprintf('&reseller=%s',$resellersip_settings_page);
                        	$url  .= sprintf('&adminonly=%s',$this->adminonly);
                        } else {
                        	if ($account->reseller == $this->reseller) $url  .= sprintf('&reseller=%s',$resellersip_settings_page);
                        }

                        foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
                            if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
                            $url  .= sprintf('&%s=%s',$element,urlencode($this->SoapEngine->extraFormElements[$element]));
                        }

                        $sip_account=sprintf("
                        <a href=\"javascript:void(null);\" onClick=\"return window.open('%s', 'SIP_Settings',
                        'toolbar=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=720')\">
                        %s@%s</a>",$url,$account->id->username,$account->id->domain);
                    } else {
                        $sip_account=sprintf("%s@%s",$account->id->username,$account->id->domain);
                    }

                    unset($groups);
                    if (is_array($account->groups)) foreach ($account->groups as $_grp) {
                        if ($_grp == 'free-pstn') {
                            $groups.='pstn ';
                        } else {
                            $groups.=$_grp.' ';
                        }
                    }

                    if ($this->version > 1) {
                        /*
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($account->customer));
                        */

                        if ($account->owner) {
                            $_owner_url = sprintf
                            ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                            $this->url,
                            urlencode($this->SoapEngine->soapEngine),
                            urlencode($account->owner),
                            $account->owner
                            );
                        } else {
                            $_owner_url='';
                        }          
                        $prepaid_account=sprintf("%s@%s",$account->id->username,$account->id->domain);

                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s %s</td>
                        <td><a href=mailto:%s>%s</a></td>
                        <td align=right>%s</td>
                        <td>%s</td>
                        <td align=right>%s</td>
                        <td align=right>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>
                        ",
                        $bgcolor,
                        $index,
                        $sip_account,
                        $account->firstName,
                        $account->lastName,
                        $account->email,
                        $account->email,
                        $account->timezone,
                        $groups,
                        $account->quota,
                        $_prepaid_balance[$prepaid_account],
                        $_owner_url,
                        $account->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s </td>
                        <td>%s</td>
                        <td>%s %s</td>
                        <td><a href=mailto:%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>
                        ",
                        $bgcolor,
                        $index,
                        $sip_account,
                        $account->firstName,
                        $account->lastName,
                        $account->email,
                        $account->email,
                        $account->timezone,
                        $groups,
                        $account->quota,
                        $account->owner,
                        $account->changeDate,
                        $_url,
                        $actionText
                        );
                    }

                    $i++;
                }
    
            }

            print "</table>";

            $this->showPagination($maxrows);

            return true;
        }
    }

    function showSeachFormCustom() {
        printf (" Account<input type=text size=12 name=username_filter value='%s'>",$this->filters['username']);
        printf ("@");

        if (count($this->allowedDomains) > 0) {
            if ($this->filters['domain'] && !in_array($this->filters['domain'],$this->allowedDomains)) {
                printf ("<input type=text size=15 name=domain_filter value='%s'>",$this->filters['domain']);
            } else {
                $selected_domain[$this->filters['domain']]='selected';
                printf ("<select name=domain_filter>
                <option>");
                foreach ($this->allowedDomains as $_domain) {
                    printf ("<option value='$_domain' %s>$_domain",$selected_domain[$_domain]);
                }
                printf ("</select>");
            }
        } else {
            printf ("<input type=text size=15 name=domain_filter value='%s'>",$this->filters['domain']);
        }

        if ($this->version > 1) {
            printf (" FN<input type=text size=10 name=firstname_filter value='%s'>",$this->filters['firstname']);
            printf (" LN<input type=text size=10 name=lastname_filter value='%s'>",$this->filters['lastname']);
            printf (" Email<input type=text size=25 name=email_filter value='%s'>",$this->filters['email']);
            printf (" Owner<input type=text size=7 name=owner_filter value='%s'>",$this->filters['owner']);
        }
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['username']) {
            $username=$dictionary['username'];
        } else {
            $username=$this->filters['username'];
        }

        if ($dictionary['domain']) {
            $domain=$dictionary['domain'];
        } else {
            $domain=$this->filters['domain'];
        }

        if (!strlen($username) || !strlen($domain)) {
            print "<p><font color=red>Error: missing SIP account username or domain. </font>";
            return false;
        }

        $account=array('username' => $username,
                       'domain'   => $domain
                      );

        $function=array('commit'   => array('name'       => 'deleteAccount',
                                            'parameters' => array($account),
                                            'logs'       => array('success' => sprintf('SIP account %s@%s has been deleted',$this->filters['username'],$this->filters['domain'])
                                                                  )
                                           )

                        );

        foreach (array_keys($this->filters) as $_filter) {
            if ($_filter == 'username' || $_filter == 'domain') continue;
            $new_filters[$_filter]=$this->filters[$_filter];
        }

        $this->filters=$new_filters;

        return $this->SoapEngine->execute($function,$this->html);
    }

    function showAddForm() {
        if ($this->filters['username']) return;

        if (!count($this->allowedDomains)) {
            print "<p><font color=red>You must create at least one SIP domain before adding SIP accounts</font>";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
        ";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <td align=left>
        ";

        print "
        <input type=submit name=action value=Add>
        ";

        if ($_REQUEST['account']) {
            $_account=$_REQUEST['account'];
        } else {
            $_account=$this->getCustomerProperty('sip_accounts_last_username');
        }

        printf ("User<input type=text size=15 name=account value='%s'>",$_account);

        if ($_REQUEST['domain']) {
            $_domain=$_REQUEST['domain'];
            $selected_domain[$_REQUEST['domain']]='selected';
        } else if ($this->filters['domain']) {
            $_domain=$this->filters['domain'];
            $selected_domain[$this->filters['domain']]='selected';
        } else if ($_domain=$this->getCustomerProperty('sip_accounts_last_domain')) {
            $selected_domain[$_domain]='selected';
        }

        if (count($this->allowedDomains) > 0) {
            print "@<select name=domain>";
            foreach ($this->allowedDomains as $_domain) {
                printf ("<option value='%s' %s>%s\n",$_domain,$selected_domain[$_domain],$_domain);
            }
            print "</select>";

        } else {
            printf (" <input type=text name=domain size=15 value='%s'>",$_domain);
        }

        if ($_REQUEST['quota']) {
            $_quota=$_REQUEST['quota'];
        } else {
            $_quota=$this->getCustomerProperty('sip_accounts_last_quota');
        }

        if (!$_quota) $_quota='';

        if ($_prepaid=$this->getCustomerProperty('sip_accounts_last_prepaid')) {
            $checked_prepaid='checked';
        } else {
            $checked_prepaid='';
        }

        if ($_pstn=$this->getCustomerProperty('sip_accounts_last_pstn')) {
            $checked_pstn='checked';
        } else {
            $checked_pstn='';
        }

        printf (" Pass<input type=password size=10 name=password value='%s'>",$_REQUEST['password']);
        printf (" Name<input type=text size=15 name=fullname value='%s'>",$_REQUEST['fullname']);
        printf (" Email<input type=text size=20 name=email value='%s'>",$_REQUEST['email']);
        printf ("<nobr>Owner<input type=text size=7 name=owner value='%s'></nobr> ",$_REQUEST['owner']);
        printf ("<nobr>PSTN<input type=checkbox name=pstn value=1 %s></nobr> ",$checked_pstn);
        printf ("<nobr>Quota<input type=text size=5 name=quota value='%s'></nobr> ",$_quota);
        printf ("<nobr>Prepaid<input type=checkbox name=prepaid value=1 %s></nobr> ",$checked_prepaid);

        print "
        </td>
        <td align=right>
        ";
        print "
        </td>
        ";
        $this->printHiddenFormElements();

        print "
        </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {
        dprint_r($dictionary);

        if ($dictionary['account']) {
            $account_els  = explode("@", $dictionary['account']);
            $this->skipSaveProperties=true;
        } else {
            $account_els  = explode("@", trim($_REQUEST['account']));
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        $username=$account_els[0];

        if (strlen($account_els[1])) {
            $domain=$account_els[1];
        } else if ($dictionary['domain']) {
            $domain=$dictionary['domain'];
        } else if ($_REQUEST['domain']) {
            $domain=trim($_REQUEST['domain']);
        } else {
            printf ("<p><font color=red>Error: Missing SIP domain</font>");
            return false;
        }

        if (!$this->validDomain($domain)) {
            print "<font color=red>Error: invalid domain name</font>";
            return false;
        }

        if ($dictionary['fullname']) {
            $name_els  = explode(" ", $dictionary['fullname']);
        } else {
            $name_els  = explode(" ", trim($_REQUEST['fullname']));
        }

        if (strlen($name_els[0])) {
            $firstName=$name_els[0];
        } else {
            $firstName='Account';
        }

        if (strlen($name_els[1])) {
            $j=1;
            while ($j < count($name_els)) {
                $lastName .= $name_els[$j].' ';
                $j++;
            }
        } else {
            if ($username=="<autoincrement>") {
                $lastName="Unknown";
            } else {
            	$lastName=$username;
            }
        }

        $lastName=trim($lastName);

        if (strlen($dictionary['timezone'])) {
            $timezone=$dictionary['timezone'];
        } else if (strlen(trim($_REQUEST['timezone']))) {
            $timezone=trim($_REQUEST['timezone']);
        } else if ($this->SoapEngine->default_timezone) {
            $timezone=$this->SoapEngine->default_timezone;
        } else {
            $timezone='Europe/Amsterdam';
        }

        if (strlen($dictionary['password'])) {
            $password=$dictionary['password'];
        } else if (strlen(trim($_REQUEST['password']))) {
            $password=trim($_REQUEST['password']);
        } else {
            $password=$this->RandomString(10);
        }

        if (is_array($dictionary['groups'])) {
            $groups=$dictionary['groups'];
        } else {
        	$groups=array();
        }

        if($dictionary['pstn'] || $_REQUEST['pstn']) {
            $_pstn=1;
            $groups[]='free-pstn';
        } else {
            $_pstn=0;
        }

        if (strlen($dictionary['email'])) {
            $email=$dictionary['email'];
        } else {
            $email=trim($_REQUEST['email']);
        }

        if (strlen($dictionary['rpid'])) {
            $rpid=$dictionary['rpid'];
        } else {
            $rpid=trim($_REQUEST['rpid']);
        }

        if (strlen($dictionary['owner'])) {
            $owner=intval($dictionary['owner']);
        } else {
            $owner=intval($_REQUEST['owner']);
        }
        if (strlen($dictionary['quota'])) {
            $quota=intval($dictionary['quota']);
        } else {
            $quota=intval($_REQUEST['quota']);
        }

        if (strlen($dictionary['prepaid'])) {
            $prepaid=intval($dictionary['prepaid']);
        } else {
            $prepaid=intval($_REQUEST['prepaid']);
        }

        if (!$email) {
            if ($username=="<autoincrement>") {
        		$email='unknown@'.strtolower($domain);
            } else {
        		$email=strtolower($username).'@'.strtolower($domain);
            }
        }

        if (!$this->skipSaveProperties) {
            $_p=array(
                      array('name'       => 'sip_accounts_last_domain',
                            'category'   => 'web',
                            'value'      => "$domain",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'sip_accounts_last_username',
                            'category'   => 'web',
                            'value'      => "$username",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'sip_accounts_last_timezone',
                            'category'   => 'web',
                            'value'      => "$timezone",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'sip_accounts_last_quota',
                            'category'   => 'web',
                            'value'      => "$quota",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'sip_accounts_last_pstn',
                            'category'   => 'web',
                            'value'      => "$_pstn",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'sip_accounts_last_prepaid',
                            'category'   => 'web',
                            'value'      => "$prepaid",
                            'permission' => 'customer'
                           )
                      );
    
            $this->setCustomerProperties($_p);
        }

		if (is_array($dictionary['properties'])) {
        	$properties=$dictionary['properties'];
        } else {
        	$properties=array();
        }

        if ($this->SoapEngine->login_credentials['reseller']) {
            $reseller_properties=$this->getResellerProperties($this->SoapEngine->login_credentials['reseller'],'store_clear_text_passwords');

            if (strlen($reseller_properties['store_clear_text_passwords'])) {
                $this->store_clear_text_passwords=$reseller_properties['store_clear_text_passwords'];
            }

        } else {
            $_reseller=$this->getResellerForDomain(strtolower($domain));

            if ($_reseller) {
    			$reseller_properties=$this->getResellerProperties($_reseller,'store_clear_text_passwords');

            	if (strlen($reseller_properties['store_clear_text_passwords'])) {
                	$this->store_clear_text_passwords=$reseller_properties['store_clear_text_passwords'];
                }
            }
        }

        if ($this->store_clear_text_passwords || $username == '<autoincrement>') {
            $password_final=$password;
        } else {
            $md1=strtolower($username).':'.strtolower($domain).':'.$password;
            $md2=strtolower($username).'@'.strtolower($domain).':'.strtolower($domain).':'.$password;
            $password_final=md5($md1).':'.md5($md2);
        }

        $account=array(
                     'id'     => array('username' => strtolower($username),
                                       'domain'   => strtolower($domain)
                                       ),
                     'firstName'  => $firstName,
                     'lastName'   => $lastName,
                     'password'   => $password_final,
                     'timezone'   => $timezone,
                     'email'      => strtolower($email),
                     'owner'      => $owner,
                     'rpid'       => $rpid,
                     'groups'     => $groups,
                     'prepaid'    => $prepaid,
                     'quota'      => $quota,
                     'region'     => '',
                     'properties' => $properties
                    );

        //print_r($account);
        $deleteAccount=array('username' => $username,
                             'domain'   => $domain);


        if ($this->html) {
            if ($username == '<autoincrement>') {
                $success_log=sprintf('SIP account has been generated in domain %s',$domain);
            } else {
                $success_log=sprintf('SIP account %s@%s has been added',$username,$domain);
            }
        }

        $function=array('commit'   => array('name'       => 'addAccount',
                                            'parameters' => array($account),
                                            'logs'       => array('success' => $success_log))
                        );

        return $this->SoapEngine->execute($function,$this->html);
    }

    function getAllowedDomains() {

        if ($this->version > 1) {

            // Filter
            $filter=array(
                          'domain'    => ''
                          );
    
            // Range
            $range=array('start' => 0,
                         'count' => 1000
                         );
    
            $orderBy = array('attribute' => 'domain',
                             'direction' => 'ASC'
                             );
    
            // Compose query
            $Query=array('filter'     => $filter,
                            'orderBy' => $orderBy,
                            'range'   => $range
                            );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains($Query);
        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error in getAllowedDomains from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            //return false;
        } else {
            if ($this->version > 1) {
                foreach ($result->domains as $_domain) {
                    if ($this->validDomain($_domain->domain)) {
                        $this->allowedDomains[]=$_domain->domain;
                    }
                }
            } else {
                foreach ($result as $_domain) {
                    if ($this->validDomain($_domain)) {
                        $this->allowedDomains[]=$_domain;
                    }
                }
            }
        }
    }

    function showPasswordReminderForm($accounts=array()) {

        printf ("
        <form method=post>
    	<h2>");

        print _("Login account reminder");
        print "</h2>
        <p>";

        print _("Fill in the e-mail address used during the registration of the SIP account:");
        printf ("
        <p>
        <input type=text size=35 name='email_filter' value='%s'>
        ",
        $this->filters['email']);

        if (count($accounts) > 1 || $_REQUEST['sip_filter']) {
            printf ("
            <p>");
            print _("More than one accounts use this email address. If you wish to receive the password for a particular account fill in the SIP account below:");

            printf ("
            <p>
            <input type=text size=35 name='sip_filter' value='%s'>
            ",
            $_REQUEST['sip_filter']);
        }

        printf ("
        <input type=submit value='Submit'>
        </form>
        ");

    }

    function getAccountsForPasswordReminder($maximum_accounts=5) {

		$accounts=array();

    	$filter  = array('email' => $this->filters['email']);

		if ($_REQUEST['sip_filter']) {
            list($username,$domain)=explode('@',trim($_REQUEST['sip_filter']));
            if ($username && $domain) {
    			$filter  = array('username' => $username,
                                 'domain'   => $domain
                                 );
            }
        }

        $range   = array('start' => 0,
                         'count' => $maximum_accounts);

        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC');

        $Query   = array('filter'  => $filter,
                         'orderBy' => $orderBy,
                         'range'   => $range);
        
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result  = $this->SoapEngine->soapclient->getAccounts($Query);
        
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        } else {
            $i=0;

            while ($i < $result->total)  {
            	if (!$result->accounts[$i]) break;
                $account = $result->accounts[$i];
                $accounts[]=array('username'=> $account->id->username,
                                  'domain'  => $account->id->domain
                                  );
            	$i++;
            }
        }

        return $accounts;
    }

    function getResellerForDomain($domain='') {
        // Filter
        $filter=array(
                      'domain'    => $domain
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->domains[0]){
                return $result->domains[0]->reseller;
            } else {
                return false;
            }
        }
    }

    function getResellerProperties($reseller='',$property='') {

        $properties=array();

        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!$reseller) {
            dprint ("No customer provided");
            return true;
        }

        if (!$property) {
            dprint ("No property provided");
            return true;
        }

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $result     = $this->SoapEngine->soapclientCustomers->getProperties(intval($reseller));

        dprint_r($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach ($result as $_property) {
        	$properties[$_property->name]=$_property->value;
        }

        return $properties;

    }

}

class SipAliases extends Records {
    var $selectionActiveExceptions=array('alias_domain');

    function SipAliases(&$SoapEngine) {
        dprint("init SipAliases");

        $target_filters_els=explode("@",trim($_REQUEST['target_username_filter']));
        $target_username=$target_filters_els[0];

        if (count($target_filters_els) > 1) {
            $target_domain=$target_filters_els[1];
        }

        $this->filters   = array('alias_username'    => strtolower(trim($_REQUEST['alias_username_filter'])),
                                 'alias_domain'      => strtolower(trim($_REQUEST['alias_domain_filter'])),
                                 'target_username'   => strtolower($target_username),
                                 'target_domain'      => strtolower($target_domain)
                                 );


        $this->Records(&$SoapEngine);

        if ($this->version > 1) {
            $this->sortElements=array(
                            'changeDate'     => 'Change date',
                            'aliasUsername'  => 'Alias user',
                            'aliasDomain'    => 'Alias domain',
                            'targetUsername' => 'Target user',
                            'targetDomain'   => 'Target domain',
                            );
        } else {
            $this->sortElements=array(
                            'aliasUsername'  => 'Alias user',
                            'aliasDomain'    => 'Alias domain',
                            'targetUsername' => 'Target user',
                            'targetDomain'   => 'Target domain',
                            );
        }

    }

    function getRecordKeys() {

        // Filter
        $filter=array('aliasUsername'  => $this->filters['alias_username'],
                      'aliasDomain'    => $this->filters['alias_domain'],
                      'targetUsername' => $this->filters['target_username'],
                      'targetDomain'   => $this->filters['target_domain'],
                      'owner'          => intval($this->filters['owner']),
                      'customer'       => intval($this->filters['customer']),
                      'reseller'       => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'aliasUsername';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        //dprint_r($Query);
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getAliases($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->aliases as $alias) {
                $this->selectionKeys[]=array('username' => $alias->id->username,
                                             'domain'   => $alias->id->domain);
            }

            return true;
        }
    }


    function listRecords() {
        $this->getAllowedDomains();

        // Make sure we apply the domain filter from the login credetials

        $this->showSeachForm();

        // Filter
        $filter=array('aliasUsername'  => $this->filters['alias_username'],
                      'aliasDomain'    => $this->filters['alias_domain'],
                      'targetUsername' => $this->filters['target_username'],
                      'targetDomain'   => $this->filters['target_domain'],
                      'owner'          => intval($this->filters['owner']),
                      'customer'       => intval($this->filters['customer']),
                      'reseller'       => intval($this->filters['reseller'])

                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order

        if ($this->version > 1) {
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
        } else {
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'aliasUsername';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';
        }

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getAliases($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                <td><b>Alias</b></td>
                <td><b>Target</b></td>
                <td><b>Owner</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->aliases[$i]) break;
    
                    $alias = $result->aliases[$i];
    
                    $index=$this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&alias_username_filter=%s&alias_domain_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($alias->id->username),
                    urlencode($alias->id->domain)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['alias_username_filter'] == $alias->id->username &&
                        $_REQUEST['alias_domain_filter'] == $alias->id->domain) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($this->version > 1) {

                        /*
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($alias->customer)
                        );
                        */

                        $_sip_accounts_url = $this->url.sprintf("&service=sip_accounts@%s&username_filter=%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($alias->target->username),
                        urlencode($alias->target->domain)
                        );
    
                        if ($alias->owner) {
                            $_owner_url = sprintf
                            ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                            $this->url,
                            urlencode($this->SoapEngine->soapEngine),
                            urlencode($alias->owner),
                            $alias->owner
                            );
                        } else {
                            $_owner_url='';
                        }          

                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>%s@%s</td>
                        <td><a href=%s>%s@%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>
                        ",
                        $bgcolor,
                        $index,
                        $alias->id->username,
                        $alias->id->domain,
                        $_sip_accounts_url,
                        $alias->target->username,
                        $alias->target->domain,
                        $_owner_url,
                        $alias->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>%s@%s</td>
                        <td>%s@%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>
                        ",
                        $bgcolor,
                        $index,
                        $alias->id->username,
                        $alias->id->domain,
                        $alias->target->username,
                        $alias->target->domain,
                        $alias->owner,
                        $alias->changeDate,
                        $_url,
                        $actionText
                        );
                    }
                    $i++;
    
                }
    
            }

            print "</table>";

            $this->showPagination($maxrows);

            /*
            $_properties=array(
                               array('name'       => $this->SoapEngine->port.'_sortBy',
                                     'value'      => $this->sorting['sortBy'],
                                     'permission' => 'customer',
                                     'category'   => 'web'
                                     ),
                               array('name'       => $this->SoapEngine->port.'_sortOrder',
                                     'value'      => $this->sorting['sortOrder'],
                                     'permission' => 'customer',
                                     'category'   => 'web'
                                     )
                               );

            print_r($_properties);
            $this->setCustomerProperties($_properties);
            */

            return true;
        }
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['alias_username']) {
            $alias_username=$dictionary['alias_username'];
        } else {
            $alias_username=$this->filters['alias_username'];
        }

        if ($dictionary['alias_domain']) {
            $alias_domain=$dictionary['alias_domain'];
        } else {
            $alias_domain=$this->filters['alias_domain'];
        }

        if (!strlen($alias_username) || !strlen($alias_domain)) {
            print "<p><font color=red>Error: missing SIP alias username or domain. </font>";
            return false;
        }

        $alias=array('username' => $alias_username,
                     'domain'   => $alias_domain
                    );

        $function=array('commit'   => array('name'       => 'deleteAlias',
                                            'parameters' => array($alias),
                                            'logs'       => array('success' => sprintf('SIP alias %s@%s has been deleted',$this->filters['alias_username'],$this->filters['alias_domain'])
                                                                  )
                                           )

                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {
        printf (" Alias<input type=text size=12 name=alias_username_filter value='%s'>",$this->filters['alias_username']);
        printf ("@");

        if (count($this->allowedDomains) > 0) {
            if ($this->filters['alias_domain'] && !in_array($this->filters['alias_domain'],$this->allowedDomains)) {
                printf ("<input type=text size=15 name=alias_domain_filter value='%s'>",$this->filters['alias_domain']);
            } else {
                $selected_domain[$this->filters['alias_domain']]='selected';
                printf ("<select name=alias_domain_filter>
                <option>");
    
                foreach ($this->allowedDomains as $_domain) {
                    printf ("<option value='$_domain' %s>$_domain",$selected_domain[$_domain]);
                }
    
                printf ("</select>");
            }
        } else {
            printf ("<input type=text size=15 name=alias_domain_filter value='%s'>",$this->filters['alias_domain']);
        }

        printf (" Target<input type=text size=35 name=target_username_filter value='%s'>",trim($_REQUEST['target_username_filter']));

        if ($this->version > 1) {
            printf (" Owner<input type=text size=7 name=owner_filter value='%s'>",$this->filters['owner']);
        }

    }

    function showAddForm() {
        if ($this->selectionActive) return;

        if (!count($this->allowedDomains)) {
            print "<p><font color=red>You must create at least one SIP domain before adding SIP aliases</font>";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";

            printf (" Alias<input type=text size=15 name=alias>");

            if ($_REQUEST['domain']) {
                $_domain=$_REQUEST['domain'];
                $selected_domain[$_REQUEST['domain']]='selected';
            } else if ($_domain=$this->getCustomerProperty('sip_aliases_last_domain')) {
                $selected_domain[$_domain]='selected';
            }
    
            if (count($this->allowedDomains) > 0) {
                print "@<select name=domain>";
                foreach ($this->allowedDomains as $_domain) {
                    printf ("<option value='%s' %s>%s\n",$_domain,$selected_domain[$_domain],$_domain);
                }
                print "</select>";
    
            } else {
                printf (" <input type=text name=domain size=15 value='%s'>",$_domain);
            }

            printf (" Target<input type=text size=35 name=target>");

            printf (" Owner<input type=text size=7 name=owner>");

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";
            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {

        if ($dictionary['alias']) {
            $alias_els  = explode("@", $dictionary['alias']);
            $this->skipSaveProperties=true;
        } else {
            $alias_els  = explode("@", trim($_REQUEST['alias']));
        }

        if ($dictionary['target']) {
            $target_els = explode("@", $dictionary['target']);
        } else {
            $target_els = explode("@", trim($_REQUEST['target']));
        }

        if ($dictionary['owner']) {
            $owner = $dictionary['owner'];
        } else {
            $owner = $_REQUEST['owner'];
        }

        if (preg_match("/:(.*)$/",$target_els[0],$m)) {
            $target_username=$m[1];
        } else {
            $target_username=$target_els[0];
        }

        if (preg_match("/:(.*)$/",$alias_els[0],$m)) {
            $username=$m[1];
        } else {
            $username=$alias_els[0];
        }

        if (strlen($alias_els[1])) {
            $domain=$alias_els[1];

        } else if (trim($_REQUEST['domain'])) {
            $domain=trim($_REQUEST['domain']);

        } else {
            if ($this->html) {
            	printf ("<p><font color=red>Error: Missing SIP domain</font>");
            }
            return false;
        }

        if (!$this->validDomain($domain)) {
            if ($this->html) {
            	print "<font color=red>Error: invalid domain name</font>";
            }
            return false;
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        if (!$this->skipSaveProperties=true) {
            $_p=array(
                      array('name'       => 'sip_aliases_last_domain',
                            'category'   => 'web',
                            'value'      => strtolower($domain),
                            'permission' => 'customer'
                           )
                      );
    
            $this->setCustomerProperties($_p);
        }

        $alias=array(
                     'id'     => array('username' => strtolower($username),
                                       'domain'   => strtolower($domain)
                                       ),
                     'target' => array('username' => strtolower($target_username),
                                       'domain'   => strtolower($target_els[1])
                                       ),
                     'owner'      => intval($owner)
                    );

        $deleteAlias=array('username' => strtolower($username),
                           'domain'   => strtolower($domain)
                           );

        $function=array('commit'   => array('name'       => 'addAlias',
                                            'parameters' => array($alias),
                                            'logs'       => array('success' => sprintf('SIP alias %s@%s has been added',$username,$domain)))
                        );
     
        return $this->SoapEngine->execute($function,$this->html);
    }

    function getAllowedDomains() {
        if ($this->version > 1) {

            // Filter
            $filter=array(
                          'domain'    => ''
                          );
    
            // Range
            $range=array('start' => 0,
                         'count' => 1000
                         );
    
            $orderBy = array('attribute' => 'domain',
                             'direction' => 'ASC'
                             );
    
            // Compose query
            $Query=array('filter'     => $filter,
                            'orderBy' => $orderBy,
                            'range'   => $range
                            );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains($Query);
        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getDomains();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($this->version > 1) {
                foreach ($result->domains as $_domain) {
                    if ($this->validDomain($_domain->domain)) {
                        $this->allowedDomains[]=$_domain->domain;
                    }
                }
            } else {
                foreach ($result as $_domain) {
                    if ($this->validDomain($_domain)) {
                        $this->allowedDomains[]=$_domain;
                    }
                }
            }
        }
    }
}

class EnumRanges extends Records {
    var $selectionActiveExceptions=array('tld');
    var $remote_engine_name='enum_engine_remote';
	var $record_generator='';

    // only admin can add prefixes below
    var $deniedPrefixes=array('1','20','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','27','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299','30','31','32','33','34','350','351','352','353','354','355','356','357','358','359','36','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','39','40','41','420','421','422','423','424','425','426','427','428','429','43','44','45','46','47','48','49','500','501','502','503','504','505','506','507','508','509','51','52','53','54','55','56','57','58','590','591','592','593','594','595','596','597','598','599','60','61','62','63','64','65','66','670','671','672','673','674','675','676','677','678','679','680','681','682','683','684','685','686','687','688','689','690','691','692','693','694','695','696','697','698','699','7','800','801','802','803','804','805','806','807','808','809','81','82','830','831','832','833','834','835','836','837','838','839','84','850','851','852','853','854','855','856','857','858','859','86','870','871','872','873','874','875','876','877','878','879','880','881','882','883','884','885','886','887','888','889','890','891','892','893','894','895','896','897','898','899','90','91','92','93','94','95','960','961','962','963','964','965','966','967','968','969','970','971','972','973','974','975','976','977','978','979','98','990','991','992','993','994','995','996','997','998','999');

    var $FieldsAdminOnly=array(
                              'reseller' => array('type'=>'integer',
                                                   'help' => 'Range owner')
                              );

    var $Fields=array(
                              'customer'    => array('type'=>'integer',
                                                   'help' => 'Range owner'
                                                      ),
                              'serial'        => array('type'=>'integer',
                                                     'help'=>'DNS serial number',
                                                     'readonly' => 1
                                                     ),
                              'ttl'         => array('type'=>'integer',
                                                     'help'=>'Cache period in DNS clients'
                                                     ),
                              'info'        => array('type'=>'string',
                                                     'help' =>'Range description'
                                                     ),
                              'size'        => array('type'=>'integer',
                                                     'help'=>'Maximum number of telephone numbers'
                                                     ),
                              'minDigits'         => array('type'=>'integer',
                                                           'help'=>'Minimum number of digits for telephone numbers'
                                                           ),
                              'maxDigits'         => array('type'=>'integer',
                                                           'help'=>'Maximum number of digits for telephone numbers'
                                                              )
                              );

    function EnumRanges(&$SoapEngine) {
        dprint("init EnumRanges");

        $this->filters   = array('prefix'       => trim(ltrim($_REQUEST['prefix_filter']),'+'),
                                 'tld'          => trim($_REQUEST['tld_filter']),
                                 'info'         => trim($_REQUEST['info_filter'])
                                 );

        $this->Records(&$SoapEngine);

        if ($this->version > 1) {
            $this->sortElements=array('changeDate' => 'Change date',
                                        'prefix'     => 'Prefix',
                                      'tld'        => 'TLD'
                                     );
            $this->Fields['nameservers'] = array('type'=>'text',
                                                 'name'=>'Name servers',
                                                 'help'=>'Name servers authoritative for this DNS zone'
                                                 );

        }

        if ($this->login_credentials['reseller_filters'][$this->reseller]['record_generator']) {
            printf ("Engine: %s",$this->SoapEngine->soapEngine);
            if (is_array($this->login_credentials['reseller_filters'][$this->reseller]['record_generator'])) {
                $_rg=$this->login_credentials['reseller_filters'][$this->reseller]['record_generator'];
                if ($_rg[$this->SoapEngine->soapEngine]) {
					$this->record_generator=$_rg[$this->SoapEngine->soapEngine];
                }
            } else {
				$this->record_generator=$this->login_credentials['reseller_filters'][$this->reseller]['record_generator'];
            }
        } else  if (strlen($this->SoapEngine->record_generator)) {
            $this->record_generator=$this->SoapEngine->record_generator;
        }
    }

    function listRecords() {
        $this->getAllowedDomains();
        $this->showSeachForm();

        if ($this->version > 1) {
            // Filter
            $filter=array('prefix'   => $this->filters['prefix'],
                          'tld'      => $this->filters['tld'],
                          'info'     => $this->filters['info'],
                          'customer' => intval($this->filters['customer']),
                          'reseller' => intval($this->filters['reseller'])
                          );

            // Range
            $range=array('start' => intval($this->next),
                         'count' => intval($this->maxrowsperpage)
                         );

            // Order
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
    
            $orderBy = array('attribute' => $this->sorting['sortBy'],
                             'direction' => $this->sorting['sortOrder']
                             );
    
            // Compose query
            $Query=array('filter'  => $filter,
                         'orderBy' => $orderBy,
                         'range'   => $range
                         );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges($Query);
    
        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            if ($this->version > 1) {
                $this->rows = $result->total;
            } else {
                $this->rows = count($result);
            }

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
                ";
                if ($this->version > 1) {
                    print "
                    <tr bgcolor=lightgrey>
                    <td><b>Id</b></th>
                    <td><b>Customer</b></td>
                    <td><b>Prefix </b></td>
                    <td><b>TLD</b></td>
                    <td><b>Serial</b></td>
                    <td><b>TTL</b></td>
                    <td><b>Info</b></td>
                    <td><b>Min</b></td>
                    <td><b>Max</b></td>
                    <td><b>Size</b></td>
                    <td colspan=2><b>Used</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                    </tr>
                    ";
                } else {
                    print "
                    <tr bgcolor=lightgrey>
                    <td><b>Id</b></th>
                    <td><b>Prefix </b></td>
                    <td><b>TLD</b></td>
                    <td><b>TTL</b></td>
                    <td><b>Min digits</b></td>
                    <td><b>Max digits</b></td>
                    <td><b>Size</b></td>
                    <td colspan=2><b>Used</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                    </tr>
                    ";
                }

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if ($this->version > 1) {
                        if (!$result->ranges[$i]) break;
                        $range = $result->ranges[$i];

                    } else {
                        $range = $result[$i];
                    }

                    $index=$this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&prefix_filter=%s&tld_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($range->id->prefix),
                    urlencode($range->id->tld)
                    );

                    if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$range->reseller);

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['prefix_filter'] == $range->id->prefix &&
                        $_REQUEST['tld_filter'] == $range->id->tld) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($this->adminonly) {
                        $range_url=sprintf('<a href=%s&service=%s&reseller_filter=%s&prefix_filter=%s&tld_filter=%s>%s</a>',$this->url,$this->SoapEngine->service,$range->reseller,$range->id->prefix,$range->id->tld,$range->id->prefix);

                    } else {
                        $range_url=sprintf('<a href=%s&&service=%s&prefix_filter=%s&tld_filter=%s>%s</a>',$this->url,$this->SoapEngine->service,$range->id->prefix,$range->id->tld,$range->id->prefix);
                    }

                    if ($this->record_generator) {
                        $generator_url=sprintf('<a href=%s&generatorId=%s&range=%s@%s&number_length=%s&reseller_filter=%s target=generator>G</a>',$this->url,$this->record_generator,$range->id->prefix,$range->id->tld,$range->maxDigits,$range->reseller);
                    } else {
                        $generator_url='';
                    }

                    if ($range->size) {
                        $usage=intval(100*$range->used/$range->size);
                        $bar=$this->makebar($usage);
                    } else {
                        $bar="";
                    }

                    if ($this->version > 1) {
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($range->customer)
                        );

						$_nameservers='';
                        foreach ($range->nameservers as $_ns) {
                        	$_nameservers.= $_ns.' ';
                        }
                        printf("
                        <tr bgcolor=%s>
                        <td>%s %s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td>+%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $generator_url,
                        $_customer_url,
                        $range->customer,
                        $range->reseller,
                        $range_url,
                        $range->id->tld,
                        $range->serial,
                        $range->ttl,
                        $range->info,
                        $range->minDigits,
                        $range->maxDigits,
                        $range->size,
                        $range->used,
                        $bar,
                        $range->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>+%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $range_url,
                        $range->id->tld,
                        $range->ttl,
                        $range->minDigits,
                        $range->maxDigits,
                        $range->size,
                        $range->used,
                        $bar,
                        $range->changeDate,
                        $_url,
                        $actionText
                        );
                    }
                    printf("
                    </tr>
                    ");

                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1 && $this->version > 1) {
                $this->showRecord($range);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['prefix']) || !strlen($this->filters['tld'])) {
            print "<p><font color=red>Error: missing ENUM range id </font>";
            return false;
        }

        $rangeId=array('prefix'=>$this->filters['prefix'],
                       'tld'=>$this->filters['tld']);

        if ($this->adminonly) {
			$this->initRemoteReplicationEngine($this->filters['reseller']);
        } else {
			$this->initRemoteReplicationEngine($this->reseller);
        }

        $function=array('commit'   => array('name'       => 'deleteRange',
                                            'parameters' => array($rangeId),
                                            'logs'       => array('success' => sprintf('ENUM range +%s under %s has been deleted',$this->filters['prefix'],$this->filters['tld'])
                                                                  )
                                            )
                        );

  		unset($this->filters);

        $result = $this->SoapEngine->execute($function,$this->html);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
            // delete remote if remote engine is set
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->deleteRange($rangeId);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                } else {
					unset($this->selectionActive);
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    function showAddForm() {
        if ($this->selectionActive) return;

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";
            if ($this->version > 1) {
                $this->showCustomerTextBox();
            }

            printf ("Prefix +<input type=text size=15 name=prefix value='%s'> ",$_REQUEST['prefix']);
            printf (" TLD");

            if ($_REQUEST['tld']) {
                printf ("<input type=text size=15 name=tld value='%s'>",$_REQUEST['tld']);
            } else if ($this->filters['tld']) {
                printf ("<input type=text size=15 name=tld value='%s'>",$this->filters['tld']);
            } else if ($_tld=$this->getCustomerProperty('enum_ranges_last_tld')) {
                printf ("<input type=text size=15 name=tld value='%s'>",$_tld);
            } else {
                printf ("<input type=text size=15 name=tld>");
            }

            printf ("TTL<input type=text size=5 name=ttl value=3600> ");
            printf ("Min Digits<input type=text size=3 name=minDigits value=11> ");
            printf ("Max Digits<input type=text size=3 name=maxDigits value=11> ");
            if ($this->version > 1) {
                printf (" Info<input type=text size=15 name=info value='%s'> ",$_REQUEST['info']);
            }

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";

            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord() {
        $tld    = trim($_REQUEST['tld']);
        $prefix = trim($_REQUEST['prefix']);
        $size   = trim($_REQUEST['size']);
        $info   = trim($_REQUEST['info']);

        if (!strlen($tld)) {
        	$tld=$this->SoapEngine->default_enum_tld;
        }

        if (!strlen($tld) || !strlen($prefix) || !is_numeric($prefix)) {
            printf ("<p><font color=red>Error: Missing TLD or prefix. </font>");
            return false;
        }

        if (!$this->adminonly) {
            if (in_array($prefix,$this->deniedPrefixes)) {
                print "<p><font color=red>Error: Only an administrator account can create the prefix coresponding to a country code.</font>";
                return false;
            }
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

		$this->initRemoteReplicationEngine($reseller);

        if (!trim($_REQUEST['ttl'])) {
            $ttl=3600;
        } else {
            $ttl=intval(trim($_REQUEST['ttl']));
        }

        $range=array(
                     'id'         => array('prefix' => $prefix,
                                           'tld'    => $tld),
                     'ttl'        => $ttl,
                     'info'       => $info,
                     'minDigits'  => intval(trim($_REQUEST['minDigits'])),
                     'maxDigits'  => intval(trim($_REQUEST['maxDigits'])),
                     'size'       => intval($size),
                     'customer'   => intval($customer),
                     'reseller'   => intval($reseller)
                    );

        $deleteRange=array('prefix'=>$prefix,
                           'tld'=>$tld);

        $_p=array(
                  array('name'       => 'enum_ranges_last_tld',
                        'category'   => 'web',
                        'value'      => "$tld",
                        'permission' => 'customer'
                       )
                  );

        $this->setCustomerProperties($_p);

        $function=array('commit'   => array('name'       => 'addRange',
                                            'parameters' => array($range),
                                            'logs'       => array('success' => sprintf('ENUM range +%s under %s has been added',$prefix,$tld)))
                        );
     
        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result);
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {

            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->addRange($range);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        			unset($this->filters);

                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    function showSeachFormCustom() {
        if ($this->version > 1) {
            printf (" Prefix<input type=text size=15 name=prefix_filter value='%s'>",$this->filters['prefix']);
            printf (" TLD");

            if (count($this->allowedDomains) > 0) {
                $selected_tld[$this->filters['tld']]='selected';
                printf ("<select name=tld_filter>
                <option>");
    
                foreach ($this->allowedDomains as $_tld) {
                    printf ("<option value='%s' %s>%s",$_tld,$selected_tld[$_tld],$_tld);
                }
    
                printf ("</select>");
            } else {
                printf ("<input type=text size=20 name=tld_filter value='%s'>",$this->filters['tld']);
            }
            printf (" Info<input type=text size=10 name=info_filter value='%s'>",$this->filters['info']);
        }
    }

    function getAllowedDomains() {
        if ($this->version > 1) {
            // Filter
            $filter=array('prefix'   => '');
            // Range
            $range=array('start' => 0,
                         'count' => 200
                         );
    
            // Order
            if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
            if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
    
            $orderBy = array('attribute' => $this->sorting['sortBy'],
                             'direction' => $this->sorting['sortOrder']
                             );
    
            // Compose query
            $Query=array('filter'  => $filter,
                         'orderBy' => $orderBy,
                         'range'   => $range
                         );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges($Query);
    
        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($this->version > 1) {
                foreach($result->ranges as $range) {
                    $this->ranges[]=array('prefix'    => $range->id->prefix,
                                          'tld'       => $range->id->tld,
                                          'minDigits' => $range->minDigits,
                                          'maxDigits' => $range->maxDigits
                                          );
                    if (in_array($range->id->tld,$this->allowedDomains)) continue;
                    $this->allowedDomains[]=$range->id->tld;
                    $seen[$range->id->tld]++;
                }
            } else {
                foreach($result as $range) {
                    $this->ranges[]=array('prefix'    => $range->id->prefix,
                                          'tld'       => $range->id->tld,
                                          'minDigits' => $range->minDigits,
                                          'maxDigits' => $range->maxDigits
                                          );
                    if (in_array($range->id->tld,$this->allowedDomains)) continue;
                    $this->allowedDomains[]=$range->id->tld;
                    $seen[$range->id->tld]++;
                }
            }

            if (!$seen[$this->SoapEngine->default_enum_tld]) {
                $this->allowedDomains[]=$this->SoapEngine->default_enum_tld;
            }
        }
    }

    function showRecord($range) {

        print "<table border=0 cellpadding=10>";
        print "
        <tr>
        <td valign=top>
        <table border=0>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        printf ("<tr><td class=border>DNS zone</td><td class=border>%s</td></td>",
        $this->tel2enum($range->id->prefix,$range->id->tld));

        if ($this->adminonly) {

            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($item == 'nameservers') {
                    foreach ($range->$item as $_item) {
                        $nameservers.=$_item."\n";
                    }
                    $item_value=$nameservers;
                } else {
                    $item_value=$range->$item;
                }

                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name=$this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name=ucfirst($item);
                }
    
                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                    <td class=border valign=top>%s</td>
                    </tr>",
                    $item_name,
                    $item,
                    $item_value,
                    $this->FieldsAdminOnly[$item]['help']
                    );
                } else {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    <td class=border>%s</td>
                    </tr>",
                    $item_name,
                    $item,
                    $item_value,
                    $this->FieldsAdminOnly[$item]['help']
                    );
                }
            }
        }

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($item == 'nameservers') {
                foreach ($range->$item as $_item) {
                    $nameservers.=$_item."\n";
                }
                $item_value=$nameservers;
            } else {
                $item_value=$range->$item;
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            }else if ($this->Fields[$item]['readonly']) {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border>%s</td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item_value,
                $this->Fields[$item]['help']
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                <td class=border>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            }
        }

        printf ("<input type=hidden name=tld_filter value='%s'",$range->id->tld);
        printf ("<input type=hidden name=prefix_filter value='%s'",$range->id->prefix);
        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function updateRecord () {
        //print "<p>Updating range ...";

        if (!$_REQUEST['prefix_filter'] || !$_REQUEST['tld_filter']) return;

        $rangeid=array('prefix' => $_REQUEST['prefix_filter'],
                      'tld'    => $_REQUEST['tld_filter']
                     );

        if (!$range = $this->getRecord($rangeid)) {
            return false;
        }

		$this->initRemoteReplicationEngine($range->reseller);

        $range_old=$range;

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $range->$item = intval($_REQUEST[$var_name]);
            } else if ($item == 'nameservers') {
                $_txt=trim($_REQUEST[$var_name]);
                if (!strlen($_txt)) {
                    unset($range->$item);
                } else {
                    $_nameservers=array();
                    $_lines=explode("\n",$_txt);
                    foreach ($_lines as $_line) {
                        $_ns=trim($_line);
                        $_nameservers[]=$_ns;
                    }
                    $range->$item=$_nameservers;
                }
            } else {
                $range->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name=$item.'_form';
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $range->$item = intval($_REQUEST[$var_name]);
                } else {
                    $range->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function=array('commit'   => array('name'       => 'updateRange',
                                            'parameters' => array($range),
                                            'logs'       => array('success' => sprintf('ENUM range +%s under %s has been updated',$rangeid['prefix'],$rangeid['tld'])))
                        );
     
        $result = $this->SoapEngine->execute($function,$this->html);
        dprint_r($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {

            // update remote if remote engine is set
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->updateRange($range);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }

    }

    function getRecord($rangeid) {
        // Filter
        if (!$rangeid['prefix'] || !$rangeid['tld']) {
            print "Error in getRecord(): Missing prefix or tld";
            return false;
        }

        $filter=array('prefix'   => $rangeid['prefix'],
                      'tld'      => $rangeid['tld']
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getRanges($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->ranges[0]){
                return $result->ranges[0];
            } else {
                return false;
            }
        }
    }
}

class EnumMappings extends Records {
    var $remote_engine_name='enum_engine_remote';

    var $default_ttl = 3600;
    var $default_priority = 5;

    var $sortElements=array('changeDate' => 'Change date',
                            'number'     => 'Number',
                            'tld'        => 'TLD'
                            );

    var $ranges=array();

    var $FieldsReadOnly=array(
                              'customer',
                              'reseller'
                              );
    var $Fields=array(
                              'owner'    => array('type'=>'integer'),
                              'info'     => array('type'=>'string')
                              );

    var $mapping_fields=array('id'       => 'integer',
                              'type'     => 'string',
                              'mapto'    => 'string',
                              'priority' => 'integer',
                              'ttl'      => 'integer'
                              );

    var $NAPTR_services=array(
        "sip"    => array("service"=>"sip",
                              "webname"=>"SIP",
                              "schemas"=>array("sip:","sips:")),
        "mailto" => array("service"=>"mailto",
                              "webname"=>"Email",
                              "schemas"=>array("mailto:")),
        "web:http"   => array("service"=>"web:http",
                              "webname"=>"WEB (http)",
                              "schemas"=>array("http://")),
        "web:https"  => array("service"=>"web:https",
                              "webname"=>"WEB (https)",
                              "schemas"=>array("https://")),
        "x-skype:callto" => array("service"=>"x-skype:callto",
                              "webname"=>"Skype",
                              "schemas"=>array("callto:")),
        "h323"   => array("service"=>"h323",
                              "webname"=>"H323",
                              "schemas"=>array("h323:")),
        "iax"    => array("service"=>"iax",
                              "webname"=>"IAX",
                              "schemas"=>array("iax:")),
        "iax2"   => array("service"=>"iax2",
                              "webname"=>"IAX2",
                              "schemas"=>array("iax2:")),
        "mms"    => array("service"=>"mms",
                              "webname"=>"MMS",
                              "schemas"=>array("tel:","mailto:")),
        "sms"    => array("service"=>"sms",
                              "webname"=>"SMS",
                              "schemas"=>array("tel:","mailto:")),
        "ems"    => array("service"=>"ems",
                              "webname"=>"EMS",
                              "schemas"=>array("tel:","mailto:")),
        "im"     => array("service"=>"im",
                              "webname"=>"IM",
                              "schemas"=>array("im:")),
        "npd:tel"   => array("service"=>"npd+tel",
                              "webname"=>"Portability",
                              "schemas"=>array("tel:")),
        "void:mailto"  => array("service"=>"void:mailto",
                              "webname"=>"VOID(mail)",
                              "schemas"=>array("mailto:")),
        "void:http"  => array("service"=>"void:http",
                              "webname"=>"VOID(http)",
                              "schemas"=>array("http://")),
        "void:https" => array("service"=>"void:https",
                              "webname"=>"VOID(https)",
                              "schemas"=>array("https://")),
        "voice"  => array("service"=>"voice",
                              "webname"=>"Voice",
                              "schemas"=>array("voice:","tel:")),
        "tel"    => array("service"=>"tel",
                              "webname"=>"Tel",
                              "schemas"=>array("tel:")),
        "fax:tel"    => array("service"=>"fax:tel",
                              "webname"=>"Fax",
                              "schemas"=>array("tel:")),
        "ifax:mailto"   => array("service"=>"ifax:mailto",
                              "webname"=>"iFax",
                              "schemas"=>array("mailto:")),
        "pres"   => array("service"=>"pres",
                              "webname"=>"Presence",
                              "schemas"=>array("pres:")),
        "ft:ftp"    => array("service"=>"ft:ftp",
                              "webname"=>"FTP",
                              "schemas"=>array("ftp://")),
        "loc:http"  => array("service"=>"loc:http",
                              "webname"=>"GeoLocation",
                              "schemas"=>array("http://")),
        "key:http"  => array("service"=>"key:http",
                              "webname"=>"Public key",
                              "schemas"=>array("http://")),
        "key:https"  => array("service"=>"key:https",
                              "webname"=>"Public key (HTTPS)",
                              "schemas"=>array("https://"))
        );

    function EnumMappings(&$SoapEngine) {
        dprint("init EnumMappings");

        if ($_REQUEST['range_filter']) {
            list($_prefix,$_tld_filter)= explode("@",$_REQUEST['range_filter']);
            if ($_prefix && !$_REQUEST['number_filter']) {
                $_number_filter=$_prefix.'%';
            } else {
                $_number_filter=$_REQUEST['number_filter'];
            }
        } else {
            $_number_filter=$_REQUEST['number_filter'];
            $_tld_filter=trim($_REQUEST['tld_filter']);
        }

        $_number_filter=ltrim($_number_filter,'+');

        $this->filters   = array('number'       => ltrim($_number_filter,'+'),
                                 'tld'          => $_tld_filter,
                                 'range'        => trim($_REQUEST['range_filter']),
                                 'type'         => trim($_REQUEST['type_filter']),
                                 'mapto'        => trim($_REQUEST['mapto_filter']),
                                 'owner'        => trim($_REQUEST['owner_filter'])
                                );
        $this->Records(&$SoapEngine);
        $this->getAllowedDomains();

    }

    function listRecords() {
        $this->showSeachForm();

        $filter=array('number'   => $this->filters['number'],
                      'tld'      => $this->filters['tld'],
                      'type'     => $this->filters['type'],
                      'mapto'    => $this->filters['mapto'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );
        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
                <td></th>
                ";

                if ($this->version > 1) {
                    print "
                    <td><b>Customer</b></td>
                    <td><b>Phone number</b></td>
                    <td><b>TLD</b></td>
                    <td><b>Info</b></td>
                    <td><b>Owner</b></td>
                    <td><b>Type</b></td>
                    <td><b>Id</b></td>
                    <td><b>Map to</b></td>
                    <td><b>TTL</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                    ";
                } else {
                    print "
                    <td><b>Phone number</b></td>
                    <td><b>TLD</b></td>
                    <td><b>Info</b></td>
                    <td><b>Type</b></td>
                    <td><b>Map to</b></td>
                    <td><b>TTL</b></td>
                    <td><b>Owner</b></td>
                    <td><b>Actions</b></td>
                    ";
                }
                print "
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->numbers[$i]) break;
    
                    $number = $result->numbers[$i];
                    $index=$this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $j=1;

                    foreach ($number->mappings as $_mapping) {
                        unset($sip_engine);
                        foreach (array_keys($this->login_credentials['reseller_filters']) as $_res) {
                            if ($_res == $number->reseller) {
                                if ($this->login_credentials['reseller_filters'][$_res]['sip_engine']) {
                                    $sip_engine=$this->login_credentials['reseller_filters'][$_res]['sip_engine'];
                                    break;
                                }
                            }
                        }

                        if (!$sip_engine) {
                            if ($this->login_credentials['reseller_filters']['default']['sip_engine']) {
                                $sip_engine=$this->login_credentials['reseller_filters']['default']['sip_engine'];
                            } else {
                                $sip_engine=$this->SoapEngine->sip_engine;
                            }
                        }

                        if (preg_match("/^sip:(.*)$/",$_mapping->mapto,$m) && $this->sip_settings_page) {
                            $url=sprintf('%s?account=%s&reseller=%s&sip_engine=%s',
                            $this->sip_settings_page,$m[1], $number->reseller,$sip_engine);
    
                            if ($this->adminonly) $url  .= sprintf('&adminonly=%s',$this->adminonly);
    
                            foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
                                if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
                                $url  .= sprintf('&%s=%s',$element,urlencode($this->SoapEngine->extraFormElements[$element]));
                            }
    
                            $mapto=sprintf("
                            <a href=\"javascript:void(null);\" onClick=\"return window.open('%s', 'SIP_Settings',
                            'toolbar=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=720')\">
                            sip:%s</a>",$url,$m[1]);
                        } else {
                            $mapto=sprintf("%s",$_mapping->mapto);
                        }

                        $_url = $this->url.sprintf("&service=%s&action=Delete&number_filter=%s&tld_filter=%s&mapto_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($number->id->number),
                        urlencode($number->id->tld),
                        urlencode($_mapping->mapto)
                        );

                        if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$number->reseller);
    
                        if ($_REQUEST['action'] == 'Delete' &&
                            $_REQUEST['number_filter'] == $number->id->number &&
                            $_REQUEST['tld_filter'] == $number->id->tld &&
                            $_REQUEST['mapto_filter'] == $_mapping->mapto) {
                            $_url .= "&confirm=1";
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }

                        if ($j==1) {

                            $_number_url = $this->url.sprintf("&service=%s&number_filter=%s&tld_filter=%s",
                            urlencode($this->SoapEngine->service),
                            urlencode($number->id->number),
                            urlencode($number->id->tld)
                            );

 		                   	if ($this->adminonly) $_number_url.= sprintf ("&reseller_filter=%s",$number->reseller);

                            if ($this->version > 1) {
                                $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                                urlencode($this->SoapEngine->customer_engine),
                                urlencode($number->customer)
                                );
    
                                if ($number->owner) {
                                    $_owner_url = sprintf
                                    ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                                    $this->url,
                                    urlencode($this->SoapEngine->soapEngine),
                                    urlencode($number->owner),
                                    $number->owner
                                    );
                                } else {
                                    $_owner_url='';
                                }          
    
                                printf("
                                <tr bgcolor=%s>
                                <td>%s</td>
                                <td><a href=%s>%s.%s</a></td>
                                <td><a href=%s>+%s</a></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a href=%s>%s</a></td>
                                </tr>",
                                $bgcolor,
                                $index,
                                $_customer_url,
                                $number->customer,
                                $number->reseller,
                                $_number_url,
                                $number->id->number,
                                $number->id->tld,
                                $number->info,
                                $_owner_url,
                                ucfirst($_mapping->type),
                                $_mapping->id,
                                $mapto,
                                $_mapping->ttl,
                                $number->changeDate,
                                $_url,
                                $actionText
                                );
                            } else {
                                printf("
                                <tr bgcolor=%s>
                                <td>%s</td>
                                <td>+%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a href=%s>%s</a></td>
                                </tr>",
                                $bgcolor,
                                $index,
                                $number->id->number,
                                $number->id->tld,
                                $number->info,
                                ucfirst($_mapping->type),
                                $mapto,
                                $_mapping->ttl,
                                $number->owner,
                                $_url,
                                $actionText
                                );
                            }
                        } else {
                            if ($this->version > 1) {
                                printf("
                                <tr bgcolor=%s>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a href=%s>%s</a></td>
                                </tr>",
                                $bgcolor,
                                ucfirst($_mapping->type),
                                $_mapping->id,
                                $mapto,
                                $_mapping->ttl,
                                $number->changeDate,
                                $_url,
                                $actionText
                                );
                            } else {
                                printf("
                                <tr bgcolor=%s>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a href=%s>%s</a></td>
                                </tr>",
                                $bgcolor,
                                ucfirst($_mapping->type),
                                $mapto,
                                $_mapping->ttl,
                                $_url,
                                $actionText
                                );
                            }
                        }
                        $j++;
                    }

                    if (!is_array($number->mappings) || !count($number->mappings)) {
                        $_url = $this->url.sprintf("&service=%s&action=Delete&number_filter=%s&tld_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($number->id->number),
                        urlencode($number->id->tld),
                        urlencode($_mapping->mapto)
                        );

 		                if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$number->reseller);

                        if ($_REQUEST['action'] == 'Delete' &&
                            $_REQUEST['number_filter'] == $number->id->number &&
                            $_REQUEST['tld_filter'] == $number->id->tld &&
                            $_REQUEST['mapto_filter'] == $_mapping->mapto) {
                            $_url .= "&confirm=1";
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }

                        $_number_url = $this->url.sprintf("&service=%s&number_filter=%s&tld_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($number->id->number),
                        urlencode($number->id->tld)
                        );

 		                if ($this->adminonly) $_number_url.= sprintf ("&reseller_filter=%s",$number->reseller);

                        if ($this->version > 1) {
                            $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                            urlencode($this->SoapEngine->customer_engine),
                            urlencode($number->customer)
                            );

                            if ($number->owner) {
                                $_owner_url = sprintf
                                ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                                $this->url,
                                urlencode($this->SoapEngine->soapEngine),
                                urlencode($number->owner),
                                $number->owner
                                );
                            } else {
                                $_owner_url='';
                            }          

                            printf("
                            <tr bgcolor=%s>
                            <td>%s</td>
                            <td><a href=%s>%s.%s</a></td>
                            <td><a href=%s>+%s</a></td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>%s</td>
                            <td><a href=%s>%s</a></td>
                            </tr>",
                            $bgcolor,
                            $index,
                            $_customer_url,
                            $number->customer,
                            $number->reseller,
                            $_number_url,
                            $number->id->number,
                            $number->id->tld,
                            $number->info,
                            $_owner_url,
                            $number->changeDate,
                            $_url,
                            $actionText
                            );
                        } else {
                            printf("
                            <tr bgcolor=%s>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><a href=%s>%s</a></td>
                            </tr>",
                            $bgcolor,
                            $_url,
                            $actionText
                            );
                        }
                    }

                    printf("
                    </tr>
                    ");

                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1 ) {
                $this->showRecord($number);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function getLastNumber() {

        // Filter
        $filter=array('number' => ''
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            if ($result->total) {
                $number = array('number'   => $result->numbers[0]->id->number,
                                'tld'      => $result->numbers[0]->id->tld,
                                'mappings' => $result->numbers[0]->mappings
                                );

                return $number;
            }

        }

        return false;
    }

    function showSeachFormCustom() {

        /*
        print " <select name=range_filter><option>";
        $selected_range[$_REQUEST['range_filter']]='selected';
        foreach ($this->ranges as $_range) {
            $rangeId=$_range['prefix'].'@'.$_range['tld'];
            printf ("<option value='%s' %s>%s +%s",$rangeId,$selected_range[$rangeId],$_range['tld'],$_range['prefix']);
        }

        print "</select>";
        */

        printf (" Number <input type=text size=15 name=number_filter value='%s'>",$_REQUEST['number_filter']);

        printf (" <nobr>Map to");
        print "<select name=type_filter>
        <option>
        ";
        reset($this->NAPTR_services);
        $selected_naptr_service[$this->filters['type']]='selected';
        while (list($k,$v) = each($this->NAPTR_services)) {
            printf ("<option value='%s' %s>%s",$k,$selected_naptr_service[$k],$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        ";

        printf ("<input type=text size=20 name=mapto_filter value='%s'></nobr>",$this->filters['mapto']);

        if ($this->version > 1) {
            printf (" Owner<input type=text size=7 name=owner_filter value='%s'>",$this->filters['owner']);
        }

    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['number']) {
            $number=$dictionary['number'];
        } else {
            $number=$this->filters['number'];
        }

        if ($dictionary['tld']) {
            $tld=$dictionary['tld'];
        } else {
            $tld=$this->filters['tld'];
        }

        if ($dictionary['mapto']) {
            $mapto=$dictionary['mapto'];
        } else {
            $mapto=$this->filters['mapto'];
        }

        if (!strlen($number) || !strlen($tld)) {
            print "<p><font color=red>Error: missing ENUM number or TLD </font>";
            return false;
        }

        $enum_id=array('number' => $number,
                       'tld'    => $tld
                       );

        if ($this->adminonly) {
			$this->initRemoteReplicationEngine($this->filters['reseller']);
        } else {
			$this->initRemoteReplicationEngine($this->reseller);
        }

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getNumber($enum_id);

        if (!PEAR::isError($result)) {
            // the number exists and we make an update
            $result_new=$result;

            if (count($result->mappings) > 1) {
                foreach ($result->mappings as $_mapping) {
                    if ($_mapping->mapto != $mapto) {
                        $mappings_new[]=array('type'     => $_mapping->type,
                                              'mapto'    => $_mapping->mapto,
                                              'ttl'      => $_mapping->ttl,
                                              'priority' => $_mapping->priority,
                                              'id'       => $_mapping->id
                                          );
                    }
                }

                if (!is_array($mappings_new)) $mappings_new = array();

                $result_new->mappings=$mappings_new;
    
                $function=array('commit'   => array('name'       => 'updateNumber',
                                                    'parameters' => array($result_new),
                                                    'logs'       => array('success' => sprintf('ENUM mapping %s has been deleted',$mapto)))
                                );

                $result = $this->SoapEngine->execute($function,$this->html);
        
                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        
                    return false;
                } else {
                    // update remote if remote engine is set
                    if (is_object($this->SoapEngineRemote)) {
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                        $result = $this->SoapEngineRemote->updateNumber($result);
        
                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngineRemote->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                            return false;
                        } else {
							unset($this->selectionActive);
                            return true;
                        }
                    } else {
                        return true;
                    }
                }

            } else {
                $function=array('commit'   => array('name'       => 'deleteNumber',
                                                    'parameters' => array($enum_id),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been deleted',$number,$tld))),
                                );

                $result = $this->SoapEngine->execute($function,$this->html);
        
                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        
                    return false;
                } else {

                    // update remote if remote engine is set
                    if (is_object($this->SoapEngineRemote)) {
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                        $result = $this->SoapEngineRemote->deleteNumber($enum_id);
        
                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngineRemote->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                            return false;
                        } else {
							unset($this->selectionActive);
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            }

            unset($this->filters);


        } else {
            return false;
        }

    }

    function showAddForm() {
        if ($this->selectionActive) return;

        //if ($this->adminonly && !$this->filters['reseller']) return;

        if (!count($this->ranges)) {
            //print "<p><font color=red>You must create at least one ENUM range before adding ENUM numbers</font>";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
        ";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        if ($this->adminonly) {
        	printf (" <input type=hidden name=reseller_filter value='%s'>",$this->filters['reseller']);
        }

        print "
        <td align=left>
        ";

        print "
        <input type=submit name=action value=Add>
        ";
        printf (" Number");

        print "<select name=range>";

        if ($_REQUEST['range']) {
            $selected_range[$_REQUEST['range']]='selected';
        } else if ($_range=$this->getCustomerProperty('enum_numbers_last_range')) {
            $selected_range[$_range]='selected';
        }

        foreach ($this->ranges as $_range) {
            $rangeId=$_range['prefix'].'@'.$_range['tld'];
            printf ("<option value='%s' %s>+%s (%s)",$rangeId,$selected_range[$rangeId],$_range['prefix'],$_range['tld']);
        }

        print "</select>";

        if ($_REQUEST['number']) {
            printf ("<input type=text size=15 name=number value='%s'>",$_REQUEST['number']);
        } else if ($_number=$this->getCustomerProperty('enum_numbers_last_number')) {
            $_prefix=$_range['prefix'];
            preg_match("/^$_prefix(.*)/",$_number,$m);
            printf ("<input type=text size=15 name=number value='%s'>",$m[1]);
        } else {
            printf ("<input type=text size=15 name=number>");
        }

        printf (" Map to");
        print "<select name=type>";

        if ($_REQUEST['type']) {
            $selected_naptr_service[$_REQUEST['type']]='selected';
        } else if ($_type=$this->getCustomerProperty('enum_numbers_last_type')) {
            $selected_naptr_service[$_type]='selected';
        }

        reset($this->NAPTR_services);

        while (list($k,$v) = each($this->NAPTR_services)) {
            printf ("<option value='%s' %s>%s",$k,$selected_naptr_service[$k],$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        ";
        if ($_REQUEST['type']) {
            $selected_naptr_service[$_REQUEST['type']]='selected';
        } else if ($_type=$this->getCustomerProperty('enum_numbers_last_type')) {
            $selected_naptr_service[$_type]='selected';
        }

        printf (" <input type=text size=25 name=mapto value='%s'>",$_REQUEST['mapto']);

        print" TTL";
        if ($_REQUEST['ttl']) {
            printf ("<input type=text size=5 name=ttl value='%s'>",$_REQUEST['ttl']);
        } else if ($_ttl=$this->getCustomerProperty('enum_numbers_last_ttl')) {
            printf ("<input type=text size=5 name=ttl value='%s'>",$_ttl);
        } else {
            printf ("<input type=text size=5 name=ttl value='3600'>");
        }
        printf (" Owner<input type=text size=7 name=owner value='%s'>",$_REQUEST['owner']);
        printf (" Info<input type=text size=10 name=info value='%s'>",$_REQUEST['info']);

        print "
        </td>
        <td align=right>
        ";
        print "
        </td>
        ";

        $this->printHiddenFormElements();

        print "
        </form>
        </tr>
        </table>
        ";
    }

    function getAllowedDomains() {
        if ($this->version > 1) {
            // Filter
            $filter=array('prefix'   => '',
                          'customer' => intval($this->filters['customer']),
                          'reseller' => intval($this->filters['reseller'])
                          );
            // Range
            $range=array('start' => 0,
                         'count' => 200
                         );
    
            // Order
            $orderBy = array('attribute' => 'prefix',
                             'direction' => 'ASC'
                             );
    
            // Compose query
            $Query=array('filter'  => $filter,
                         'orderBy' => $orderBy,
                         'range'   => $range
                         );
    
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges($Query);
    
        } else {
            $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
            $result     = $this->SoapEngine->soapclient->getRanges();
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($this->version > 1) {
                foreach($result->ranges as $range) {
                    $this->ranges[]=array('prefix'    => $range->id->prefix,
                                          'tld'       => $range->id->tld,
                                          'minDigits' => $range->minDigits,
                                          'maxDigits' => $range->maxDigits
                                          );
                    if (in_array($range->id->tld,$this->allowedDomains)) continue;
                    $this->allowedDomains[]=$range->id->tld;
                    $seen[$range->id->tld]++;
                }
            } else {
                foreach($result as $range) {
                    $this->ranges[]=array('prefix'    => $range->id->prefix,
                                          'tld'       => $range->id->tld,
                                          'minDigits' => $range->minDigits,
                                          'maxDigits' => $range->maxDigits
                                          );
                    if (in_array($range->id->tld,$this->allowedDomains)) continue;
                    $this->allowedDomains[]=$range->id->tld;
                    $seen[$range->id->tld]++;
                }
            }
            if (!$seen[$this->SoapEngine->default_enum_tld]) {
                $this->allowedDomains[]=$this->SoapEngine->default_enum_tld;
            }
        }
    }

    function addRecord($dictionary=array()) {
        $prefix='';
        if ($dictionary['range']) {
            list($prefix,$tld)=explode('@',trim($dictionary['range']));
            $this->skipSaveProperties=true;
        } else if ($dictionary['tld']) {
            $tld = $dictionary['tld'];
        } else if ($_REQUEST['range']) {
            list($prefix,$tld)=explode('@',trim($_REQUEST['range']));
        } else {
            $tld = trim($_REQUEST['tld']);
        }

        if ($dictionary['number']) {
            $number = $dictionary['number'];
        } else {
            $number = trim($_REQUEST['number']);
        }

        $number=$prefix.$number;

        if (!strlen($tld)) {
        	$tld=$this->SoapEngine->default_enum_tld;
        }

        if (!strlen($tld) || !strlen($number) || !is_numeric($number)) {
            printf ("<p><font color=red>Error: Missing TLD or number. </font>");
            return false;
        }

		$this->initRemoteReplicationEngine($reseller);

        if ($dictionary['ttl']) {
            $ttl = intval($dictionary['ttl']);
        } else {
            $ttl = intval(trim($_REQUEST['ttl']));
        }

        if (!$ttl) $ttl=3600;

        if ($dictionary['priority']) {
            $priority = intval($dictionary['priority']);
        } else {
            $priority = intval(trim($_REQUEST['priority']));
        }

        if ($dictionary['owner']) {
            $owner = intval($dictionary['owner']);
        } else {
            $owner = intval(trim($_REQUEST['owner']));
        }

        if ($dictionary['info']) {
            $info = $dictionary['info'];
        } else {
            $info = trim($_REQUEST['info']);
        }

        if (!$priority) $priority=5;

        $enum_id=array('number' => $number,
                       'tld'    => $tld);

        if ($dictionary['mapto']) {
            $mapto = $dictionary['mapto'];
        } else {
            $mapto = trim($_REQUEST['mapto']);
        }

        if ($dictionary['type']) {
            $type = $dictionary['type'];
        } else {
            $type = trim($_REQUEST['type']);
        }

        if (preg_match("/^([a-z0-9]+:\/\/)(.*)$/i",$mapto,$m)) {
            $_scheme = $m[1];
            $_value  = $m[2];
        } else if (preg_match("/^([a-z0-9]+:)(.*)$/i",$mapto,$m)) {
            $_scheme = $m[1];
            $_value  = $m[2];
        } else {
            $_scheme = '';
            $_value  = $mapto;
        }

        if (!$_value) {
            $lastNumber=$this->getLastNumber();
            foreach($lastNumber['mappings'] as $_mapping) {
                if ($_mapping->type == trim($type)) {
                    if (preg_match("/^(.*)@(.*)$/",$_mapping->mapto,$m)) {
                        $_value = $number.'@'.$m[2];
                        break;
                    }
                }
            }
        }

        if (!$_scheme || !in_array($_scheme,$this->NAPTR_services[trim($type)]['schemas'])) {
            $_scheme=$this->NAPTR_services[trim($type)]['schemas'][0];
        }

        $mapto=$_scheme.$_value;

        $enum_number=array('id'       => $enum_id,
                           'owner'    => $owner,
                           'info'     => $info,
                           'mappings' => array(array('type'     => $type,
                                                     'mapto'    => $mapto,
                                                     'ttl'      => $ttl,
                                                     'priority' => $priority
                                                    )
                                               )
                           );

        if (!$this->skipSaveProperties=true) {
    
            $_p=array(
                      array('name'       => 'enum_numbers_last_range',
                            'category'   => 'web',
                            'value'      => $_REQUEST['range'],
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_type',
                            'category'   => 'web',
                            'value'      => "$type",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_number',
                            'category'   => 'web',
                            'value'      => "$number",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_ttl',
                            'category'   => 'web',
                            'value'      => "$ttl",
                            'permission' => 'customer'
                           )
                      );
    
            $this->setCustomerProperties($_p);
        }

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getNumber($enum_id);

        if (PEAR::isError($result)) {
            $error_msg=$result->getMessage();
            $error_fault=$result->getFault();
            $error_code=$result->getCode();

            if ($error_fault->detail->exception->errorcode == "3002") {

                $function=array('commit'   => array('name'       => 'addNumber',
                                                    'parameters' => array($enum_number),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been added',$number,$tld)))
                                );
             
                $result = $this->SoapEngine->execute($function,$this->html);
        
                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        
                    return false;
                } else {

                    // add remote if remote engine is set
                    if (is_object($this->SoapEngineRemote)) {
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                        $result = $this->SoapEngineRemote->addNumber($result);
        
                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngineRemote->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }

            } else {
                printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        } else {
            // the number exists and we make an update
            $result_new=$result;
            foreach ($result->mappings as $_mapping) {
                $mappings_new[]=array('type'     => $_mapping->type,
                                      'mapto'    => $_mapping->mapto,
                                      'ttl'      => $_mapping->ttl,
                                      'priority' => $_mapping->priority,
                                      'id'       => $_mapping->id
                                      );

                if ($_mapping->mapto == $mapto) {
                    printf ("<p><font color=blue>Info: ENUM mapping %s for number %s already exists</font>",$mapto,$number);
                    return $result;
                }
            }

            $mappings_new[]=array('type'    => trim($type),
                                  'mapto'   => $mapto,
                                  'ttl'     => intval(trim($_REQUEST['ttl'])),
                                  'priority'=> intval(trim($_REQUEST['priority'])),
                                 );
            // add mapping
            $result_new->mappings=$mappings_new;

            $function=array('commit'   => array('name'       => 'updateNumber',
                                                'parameters' => array($result_new),
                                                'logs'       => array('success' => sprintf('ENUM number +%s under %s has been updated',$number,$tld)))
                            );

            $result = $this->SoapEngine->execute($function,$this->html);
    
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
    
                return false;
            } else {

                // add remote if remote engine is set
                if (is_object($this->SoapEngineRemote)) {
                    $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                    $result = $this->SoapEngineRemote->updateNumber($result);
    
                    if (PEAR::isError($result)) {
                        $error_msg  = $result->getMessage();
                        $error_fault= $result->getFault();
                        $error_code = $result->getCode();
                        printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngineRemote->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }

        }
    }

    function getRecordKeys() {

        // Filter
        $filter=array('number'   => $this->filters['number'],
                      'tld'      => $this->filters['tld'],
                      'type'     => $this->filters['type'],
                      'mapto'    => $this->filters['mapto'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->numbers as $number) {
                $this->selectionKeys[]=array('number' => $number->id->number,
                                             'tld'    => $number->id->tld);
            }
            return true;
        }
    }

    function showRecord($number) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Number</h3>";
        print "</td><td>";
        print "<h3>Mappings</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        printf ("<tr><td class=border>DNS name</td><td class=border>%s</td></td>",
        $this->tel2enum($number->id->number,$number->id->tld));

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                </tr>",
                $item_name,
                $item,
                $number->$item
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                </tr>",
                $item_name,
                $item,
                $number->$item
                );
            }
        }

        printf ("<input type=hidden name=tld_filter value='%s'",$number->id->tld);
        printf ("<input type=hidden name=number_filter value='%s'",$number->id->number);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";

        print "</td><td valign=top>";

        print "<table border=0>";
        print "<tr>";
        print "<td></td>";
        print "<td class=border>Id</td>";
        print "<td class=border>Type</td>";
        print "<td class=border>Map to</td>";
        print "<td class=border>TTL</td>";
        print "</tr>";

        foreach ($number->mappings as $_mapping) {
            $j++;
            unset($selected_type);
            print "<tr>";
            print "<td>$j</td>";
            printf ("<td class=border>%d<input type=hidden name=mapping_id[] value='%d'></td>",$_mapping->id,$_mapping->id);
            $selected_type[$_mapping->type]='selected';
            printf ("
            <td class=border><select name=mapping_type[]>");
            reset($this->NAPTR_services);
            while (list($k,$v) = each($this->NAPTR_services)) {
                printf ("<option value='%s' %s>%s",$k,$selected_type[$k],$this->NAPTR_services[$k]['webname']);
            }

            print "
            </select>
            </td>";

            printf ("
            <td class=border><input name=mapping_mapto[] size=30 value='%s'></td>
            <td class=border><input name=mapping_ttl[] size=6 value='%s'></td>
            ",
            $_mapping->mapto,
            $_mapping->ttl
            );
            print "</tr>";
        }

        $j++;
        print "<tr>";
        print "<td></td>";
        print "<td></td>";

        printf ("
        <td class=border><select name=mapping_type[]>");
        reset($this->NAPTR_services);
        while (list($k,$v) = each($this->NAPTR_services)) {
            printf ("<option value='%s'>%s",$k,$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        </td>";

        printf ("
        <td class=border><input name=mapping_mapto[] size=30></td>
        <td class=border><input name=mapping_ttl[] size=6></td>
        "
        );

        print "</tr>";

        print "</table>";

        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function getRecord($enumid) {

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getNumber($enumid);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            return $result;
        }
    }

    function updateRecord () {
        //print "<p>Updating number ...";

        if (!$_REQUEST['number_filter'] || !$_REQUEST['tld_filter']) return;

        $enumid=array('number' => $_REQUEST['number_filter'],
                      'tld'    => $_REQUEST['tld_filter']
                     );

        if (!$number = $this->getRecord($enumid)) {
            return false;
        }

		$this->initRemoteReplicationEngine($number->reseller);

        $number_old=$number;

        $new_mappings=array();

        /*
        foreach ($number->mappings as $_mapping) {
            foreach (array_keys($this->mapping_fields) as $field) {
                if ($this->mapping_fields[$field] == 'integer') {
                    $new_mapping[$field]=intval($_mapping->$field);
                } else {
                    $new_mapping[$field]=$_mapping->$field;
                }
            }

            $new_mappings[]=$new_mapping;
        }
        */

        $j=0;
        while ($j< count($_REQUEST['mapping_type'])) {
            $mapto    = $_REQUEST['mapping_mapto'][$j];
            $type     = $_REQUEST['mapping_type'][$j];
            $id       = $_REQUEST['mapping_id'][$j];
            $ttl      = intval($_REQUEST['mapping_ttl'][$j]);
            $priority = intval($_REQUEST['mapping_priority'][$j]);

            if (!$ttl) $ttl = $this->default_ttl;
            if (!$priority) $priority = $this->default_priority;

            if (strlen($mapto)) {
                if (preg_match("/^([a-z0-9]+:\/\/)(.*)$/i",$mapto,$m)) {
                    $_scheme = $m[1];
                    $_value  = $m[2];
                } else if (preg_match("/^([a-z0-9]+:)(.*)$/i",$mapto,$m)) {
                    $_scheme = $m[1];
                    $_value  = $m[2];
                } else {
                    $_scheme = '';
                    $_value  = $mapto;
                }

                reset($this->NAPTR_services);
                if (!$_scheme || !in_array($_scheme,$this->NAPTR_services[trim($type)]['schemas'])) {
                    $_scheme=$this->NAPTR_services[trim($type)]['schemas'][0];
                }
        
                $mapto=$_scheme.$_value;
    
                $new_mappings[]=array( 'type'     => $type,
                                       'ttl'      => $ttl,
                                       'id'       => intval($id),
                                       'mapto'    => $mapto,
                                       'priority' => $priority
                                       );
            }

            $j++;
        }

        $number->mappings=$new_mappings;

        if (!is_array($number->mappings)) $number->mappings=array();

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $number->$item = intval($_REQUEST[$var_name]);
            } else {
                $number->$item = trim($_REQUEST[$var_name]);
            }
        }

        //print_r($number);
        $function=array('commit'   => array('name'       => 'updateNumber',
                                            'parameters' => array($number),
                                            'logs'       => array('success' => sprintf('ENUM number +%s under %s has been updated',$enumid['number'],$enumid['tld'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {

            // update remote if remote engine is set
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->updateNumber($result);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngineRemote->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}

class DnsZones extends Records {
    var $remote_engine_name='dns_engine_remote';

    var $FieldsAdminOnly=array(
                              'reseller'      => array('type'=>'integer',
                                                   'help' => 'Zone owner')
                              );

    var $Fields=array(
                              'customer'      => array('type'=>'integer',
                                                   'help' => 'Zone owner'
                                                      ),
                              'serial'        => array('type'=>'integer',
                                                     'help'=>'Serial number',
                                                     'readonly' => 1
                                                     ),
                              'email'         => array('type'=>'string',
                                                     'help'=>'Administrator address'
                                                     ),
                              'ttl'           => array('type'=>'integer',
                                                     'help'=>'Time to live of SOA record'
                                                     ),
                              'minimum'         => array('type'=>'integer',
                                                     'help'=>'Default time to live period'
                                                     ),
                              'retry'         => array('type'=>'integer',
                                                     'help'=>'Retry transfer period'
                                                     ),
                              'expire'        => array('type'=>'integer',
                                                     'help'=>'Expire period'
                                                     ),
                              'info'          => array('type'=>'string',
                                                     'help' =>'Zone description'
                                                     )
                              );

    function DnsZones(&$SoapEngine) {
        dprint("init DnsZones");

        $this->filters   = array(
                                 'name' => trim($_REQUEST['name_filter']),
                                 'info' => trim($_REQUEST['info_filter'])
                                 );

        $this->Records(&$SoapEngine);

        $this->sortElements=array('changeDate' => 'Change date',
                                   'name'     => 'Name'
                                 );
        $this->Fields['nameservers'] = array('type'=>'text',
                                             'name'=>'Name servers',
                                             'help'=>'Authoritative name servers'
                                             );

        if ($this->reseller) {
        	dprint_r($this->SoapEngine->login_credentials['reseller_filters'][$this->reseller]);

            $dns_engine_remote=$this->SoapEngine->login_credentials['reseller_filters'][$this->reseller]['dns_engine_remote'];
 
            if (strlen($dns_engine_remote) && $this->SoapEngine->soapEngine != $dns_engine_remote) {
                // replicate change
 
                if (in_array($dns_engine_remote,array_keys($this->SoapEngine->soapEngines))) {
                    $this->SOAPloginRemote = array(
                                           "username"    => $this->SoapEngine->soapEngines[$dns_engine_remote]['username'],
                                           "password"    => $this->SoapEngine->soapEngines[$dns_engine_remote]['password'],
                                           "admin"       => true,
                                           "impersonate" => intval($this->reseller)
                                       );
    
                    $this->SOAPurlRemote=$this->SoapEngine->soapEngines[$dns_engine_remote]['url'];
    
                    $log=sprintf ("and syncronize changes to <a href=%swsdl target=wsdl>%s</a>",$this->SOAPurlRemote,$this->SOAPurlRemote);
                    dprint($log);
 
                    $this->SoapAuthRemote  = array('auth', $this->SOAPloginRemote , 'urn:AGProjects:NGNPro', 0, '');
 
                    $this->SoapEngineRemote = new $this->SoapEngine->soap_class($this->SOAPurlRemote);
 
                    if (strlen($this->soapEngines[$dns_engine_remote]['timeout'])) {
                        $this->SoapEngineRemote->_options['timeout'] = intval($this->soapEngines[$dns_engine_remote]['timeout']);
                    } else {
                        $this->SoapEngineRemote->_options['timeout'] = $this->soapTimeout;
                    }
 
                    $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                    $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
                }
            }
        }

    }

    function showAfterEngineSelection () {
        if ($this->SoapEngine->name_servers) {
        //printf (" Available name servers: %s",$this->SoapEngine->name_servers);
        }
    }

    function listRecords() {
        $this->showSeachForm();

        // Filter
        $filter=array('name'     => $this->filters['name'],
                      'info'     => $this->filters['info'],
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
            <td><b>Id</b></th>
            <td><b>Customer</b></td>
            <td><b>Zone</b></td>
            <td><b>Administrator</b></td>
            <td><b>Info</b></td>
            <td><b></b></td>
            <td><b>Serial</b></td>
            <td><b>Default TTL</b></td>
            <td><b>Change date</b></td>
            <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->zones[$i]) break;
                    $zone = $result->zones[$i];

                    $index=$this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&name_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($zone->name)
                    );

                    if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$zone->reseller);

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['name_filter'] == $zone->name) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $zone_url=sprintf('%s&service=%s&name_filter=%s',
                    $this->url,
                    $this->SoapEngine->service,
                    $zone->name
                    );

                    $records_url = $this->url.sprintf("&service=dns_records@%s&zone_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($zone->name)
                    );

                    if ($this->adminonly) $zone_url    .= sprintf("&reseller_filter=%s",$zone->reseller);
                    if ($this->adminonly) $records_url .= sprintf("&reseller_filter=%s",$zone->reseller);

                    $customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($zone->customer)
                    );

                    sort($zone->nameservers);

					$ns_text='';
                    foreach ($zone->nameservers as $ns) {
                        $ns_text.= $ns." ";
                    }

                    printf("
                    <tr bgcolor=%s>
                    <td>%s</td>
                    <td><a href=%s>%s.%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=%s>Records</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    </tr>",
                    $bgcolor,
                    $index,
                    $customer_url,
                    $zone->customer,
                    $zone->reseller,
                    $zone_url,
                    $zone->name,
                    $zone->email,
                    $zone->info,
                    $records_url,
                    $zone->serial,
                    $zone->ttl,
                    $zone->changeDate,
                    $_url,
                    $actionText
                    );
                    printf("
                    </tr>
                    ");

                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1 && $this->version > 1) {
                $this->showRecord($zone);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['name'])) {
            print "<p><font color=red>Error: missing Dns zone name </font>";
            return false;
        }

		$name=$this->filters['name'];

        if ($this->adminonly) {
			$this->initRemoteReplicationEngine($this->filters['reseller']);
        } else {
			$this->initRemoteReplicationEngine($this->reseller);
        }

        $function=array('commit'   => array('name'       => 'deleteZone',
                                            'parameters' => array($name),
                                            'logs'       => array('success' => sprintf('Dns zone %s has been deleted',$this->filters['name'])
                                                                  )
                                            )
                        );

        unset($this->filters);

        $result = $this->SoapEngine->execute($function,$this->html);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->deleteZone($name);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }

    }

    function showAddForm() {
        if ($this->selectionActive) return;

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";
            $this->showCustomerTextBox();

            printf (" DNS zone <input type=text size=25 name=name value='%s'> ",$_REQUEST['name']);

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";

            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord() {
        $name         = trim($_REQUEST['name']);
        $info         = trim($_REQUEST['info']);
        $name_servers = trim($_REQUEST['name_servers']);

        if (!strlen($name)) {
            printf ("<p><font color=red>Error: Missing zone name. </font>");
            return false;
        }

        if (is_numeric($prefix)) {
            printf ("<p><font color=red>Error: Numeric zone names are not allowed. Use ENUM port instead. </font>");
            return false;
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

		$this->initRemoteReplicationEngine($reseller);

        if (!trim($_REQUEST['ttl'])) {
            $ttl=3600;
        } else {
            $ttl=intval(trim($_REQUEST['ttl']));
        }

        if ($name_servers)  {
            $ns_array=explode(" ",trim($name_servers));
        } else if ($this->login_credentials['login_type'] != 'admin' && $this->SoapEngine->name_servers){
            $ns_array=explode(" ",trim($this->SoapEngine->name_servers));
        } else {
            $ns_array=array();
        }

        $zone=array(
                     'name'        => $name,
                     'ttl'         => $ttl,
                     'info'        => $info,
                     'customer'    => intval($customer),
                     'reseller'    => intval($reseller),
                     'nameservers' => $ns_array
                    );

        $deleteZone=array('name'=>$name);

        $function=array('commit'   => array('name'       => 'addZone',
                                            'parameters' => array($zone),
                                            'logs'       => array('success' => sprintf('DNS zone %s has been added',$name)))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);
        dprint_r($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {

            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->addZone($zone);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
        			unset($this->filters);

                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }

    }

    function showSeachFormCustom() {
    	printf (" DNS zone<input type=text size=25 name=name_filter value='%s'>",$this->filters['name']);
    	printf (" Info<input type=text size=25 name=info_filter value='%s'>",$this->filters['info']);
    }

    function showRecord($zone) {

        print "<table border=0 cellpadding=10>";
        print "
        <tr>
        <td valign=top>
        <table border=0>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        printf ("<tr><td class=border>DNS zone</td><td class=border>%s</td></td>",$zone->name);

        if ($this->adminonly) {

            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($item == 'nameservers') {
                    foreach ($zone->$item as $_item) {
                        $nameservers.=$_item."\n";
                    }
                    $item_value=$nameservers;
                } else {
                    $item_value=$zone->$item;
                }

                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name=$this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name=ucfirst($item);
                }
    
                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                    <td class=border valign=top>%s</td>
                    </tr>",
                    $item_name,
                    $item,
                    $item_value,
                    $this->FieldsAdminOnly[$item]['help']
                    );
                } else {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    <td class=border>%s</td>
                    </tr>",
                    $item_name,
                    $item,
                    $item_value,
                    $this->FieldsAdminOnly[$item]['help']
                    );
                }
            }
        }

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($item == 'nameservers') {
                foreach ($zone->$item as $_item) {
                    $nameservers.=$_item."\n";
                }
                $item_value=$nameservers;
            } else {
                $item_value=$zone->$item;
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            } else if ($this->Fields[$item]['readonly']) {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border>%s</td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item_value,
                $this->Fields[$item]['help']
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                <td class=border>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            }
        }

        printf ("<input type=hidden name=tld_filter value='%s'",$zone->id->tld);
        printf ("<input type=hidden name=prefix_filter value='%s'",$zone->id->prefix);
        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function updateRecord () {

        if (!$_REQUEST['name_filter']) return;
        //dprintf ("<p>Updating zone %s...",$_REQUEST['name_filter']);

        $filter=array('name' => $_REQUEST['name_filter']);

        if (!$zone = $this->getRecord($filter)) {
            return false;
        }

		$this->initRemoteReplicationEngine($zone->reseller);

        $zone_old=$zone;

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $zone->$item = intval($_REQUEST[$var_name]);
            } else if ($item == 'nameservers') {
                $_txt=trim($_REQUEST[$var_name]);
                if (!strlen($_txt)) {
                    unset($zone->$item);
                } else {
                    $_nameservers=array();
                    $_lines=explode("\n",$_txt);
                    foreach ($_lines as $_line) {
                        $_ns=trim($_line);
                        $_nameservers[]=$_ns;
                    }
                    $zone->$item=$_nameservers;
                }
            } else {
                $zone->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name=$item.'_form';
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $zone->$item = intval($_REQUEST[$var_name]);
                } else {
                    $zone->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function=array('commit'   => array('name'       => 'updateZone',
                                            'parameters' => array($zone),
                                            'logs'       => array('success' => sprintf('DNS zone %s has been updated',$filter['name'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = $this->SoapEngineRemote->updateZone($zone);
    
                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    unset($this->filters);
    
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }

    }

    function getRecord($zone) {
        // Filter
        if (!$zone['name']) {
            print "Error in getRecord(): Missing zone name";
            return false;
        }

        $filter=array('name'   => $zone['name']);

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->zones[0]){
                return $result->zones[0];
            } else {
                return false;
            }
        }
    }

    function getRecordKeys() {

        // Filter
        $filter=array('name'     => $this->filters['name'],
                      'info'     => $this->filters['info'],
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 200
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->zones as $zone) {
                $this->selectionKeys[]=array('name' => $zone->name);
            }
            return true;
        }
    }
}

class DnsRecords extends Records {
    var $remote_engine_name  = 'dns_engine_remote';
	var $max_zones_selection = 50;
	var $typeFilter          = false;
    var $default_ttl         = 3600;
    var $fancy               = false;

    var $sortElements=array('changeDate' => 'Change date',
                            'type'       => 'Type',
                            'name'       => 'Name'
                            );

    var $FieldsReadOnly=array(
                              'customer',
                              'reseller'

                              );
    var $Fields=array(
                              'type'     => array('type'=>'string'),
                              'priority' => array('type'=>'integer'),
                              'value'    => array('type'=>'string'),
                              'ttl'      => array('type'=>'integer')
                              );

    var $recordTypes=array('A'     => 'IP address',
                           'AAAA'  => 'IP v6 address',
                           'CNAME' => 'Hostname alias',
                           'MX'    => 'Mail server address',
                           'SRV'   => 'Server resource',
                           'NS'    => 'Name server address',
                           'NAPTR' => 'Name authority',
                           'PTR'   => 'Reverse IP address',
                           'TXT'   => 'Text',
                           'LOC'   => 'Geo location'
                           );

    var $havePriority         = array('MX','SRV','NAPTR');

	var $addRecordFunction    = 'addRecord';
    var $deleteRecordFunction = 'deleteRecord';
    var $updateRecordFunction = 'updateRecord';
    var $getRecordsFunction   = 'getRecords';
    var $getRecordFunction    = 'getRecord';

    var $recordTypesTemplate=array(
                               'sipudp' =>  array('name'    => 'SIP - UDP transport',
                                                  'records' =>  array(
                                                                      'naptr' => array('name'  => '',
                                                                                       'type'   => 'NAPTR',
                                                                                       'priority'=> '30',
                                                                                       'value' => '10 30 "s" "SIP+D2U" "" _sip._udp'
                                                                                       ),
                                                                      'srv'   => array('name'   => '_sip._udp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '30',
                                                                                       'value'  => '10 5060 #VALUE#|10 5060 sip'
                                                                                       )
                                                                      ),
                                                 ),
                               'siptcp' =>  array('name'    => 'SIP - TCP transport',
                                                  'records' =>  array(
                                                                      'naptr' => array('name'    => '',
                                                                                       'type'    => 'NAPTR',
                                                                                       'priority'=> '20',
                                                                                       'value' => '10 20 "s" "SIP+D2T" "" _sip._tcp'
                                                                                       ),
                                                                      'srv'   => array('name'  => '_sip._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '20',
                                                                                       'value' => '10 5060 #VALUE#|10 5060 sip'
                                                                                       )
                                                                      ),
                                                  ),
                               'siptls' =>  array('name'    => 'SIP - TLS transport',
                                                  'records' =>  array('srv'   => array('name'  => '_sips._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '10 5061 #VALUE#|10 5061 sip'
                                                                                       ),
                                                                      'naptr' => array('name'  => '',
                                                                                       'type'   => 'NAPTR',
                                                                                       'priority'=> '10',
                                                                                       'value' => '10 10 "s" "SIPS+D2T" "" _sips._tcp'
                                                                                       )
                                                                      )


    											),
                               'stun' =>  array('name'    => 'STUN - NAT mirror',
                                                  'records' =>  array('srv'   => array('name'  => '_stun._udp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '0',
                                                                                       'value' => '10 3478 #VALUE#|10 3478 stun'
                                                                                       )
                                                                      ),
                                                  ),
                               'xmpp-server' =>  array('name'    => 'XMPP server',
                                                  'records' =>  array(
                                                                      'srv'   => array('name'  => '_xmpp-server._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '0',
                                                                                       'value' => '10 5269 #VALUE#|10 5269 xmpp'
                                                                                       ),
                                                                      'srv1'   => array('name'  => '_jabber._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '0',
                                                                                       'value' => '10 5269 #VALUE#|10 5269 xmpp'
                                                                                       )
                                                                      ),
                                                  ),
                               'xmpp-client' =>  array('name'    => 'XMPP client',
                                                  'records' =>  array('srv'   => array('name'  => '_xmpp-client._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '0',
                                                                                       'value' => '10 5222 #VALUE#|10 5222 xmpp'
                                                                                       )
                                                                      ),
                                                  ),
                               'mediaproxy' =>  array('name'    => 'MediaProxy - RTP relay',
                                                  'records' =>  array('srv'   => array('name'  => '_mediaproxy._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '0',
                                                                                       'value' => '10 25060 #VALUE#|10 25060 mediaproxy'
                                                                                       )
                                                                      ),
                                                  ),
                               'msrp' =>  array('name'    => 'MSRP - IM relay',
                                                  'records' =>  array('srv'   => array('name'  => '_msrps._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '0 2855 msrprelay'
                                                                                       )
                                                                      )
    											),
                               'sipthor' =>  array('name'    => 'SIP - Thor network',
                                                   'records' =>
                                                                array(
                                                                      'sipserver' => array('name'  => '_sip._udp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '30 5060 proxy'
                                                                                       ),
                                                                      'sipns1' => array('name'  => 'proxy',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns1'
                                                                                       ),
                                                                      'sipns2' => array('name'  => 'proxy',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns2'
                                                                                       ),
                                                                      'sipns3' => array('name'  => 'proxy',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns3'
                                                                                       ),
                                                                      'eventserver' => array('name'  => '_eventserver._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '0 8000 eventserver'
                                                                                       ),
                                                                      'evns1' => array('name'  => 'eventserver',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns1'
                                                                                       ),
                                                                      'evns2' => array('name'  => 'eventserver',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns2'
                                                                                       ),
                                                                      'evns3' => array('name'  => 'eventserver',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns3'
                                                                                       ),
                                                                      'ngnproserver' => array('name'  => '_ngnpro._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '0 9200 ngnpro'
                                                                                       ),
                                                                      'ngnns1' => array('name'  => 'ngnpro',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns1'
                                                                                       ),
                                                                      'ngnns2' => array('name'  => 'ngnpro',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns2'
                                                                                       ),
                                                                      'ngnns3' => array('name'  => 'ngnpro',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns3'
                                                                                       ),
                                                                      'xcapserver' => array('name'  => '_xcap._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '0 443 xcap'
                                                                                       ),
                                                                      'xcapns1' => array('name'  => 'xcap',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns1'
                                                                                       ),
                                                                      'xcapns2' => array('name'  => 'xcap',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns2'
                                                                                       ),
                                                                      'xcapns3' => array('name'  => 'xcap',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns3'
                                                                                       ),
                                                                      'voicemail' => array('name'  => '_voicemail._tcp',
                                                                                       'type'   => 'SRV',
                                                                                       'priority'=> '10',
                                                                                       'value' => '0 9200 voicemail'
                                                                                       ),
                                                                      'vmns1' => array('name'  => 'voicemail',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns1'
                                                                                       ),
                                                                      'vmns2' => array('name'  => 'voicemail',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns2'
                                                                                       ),
                                                                      'vmns3' => array('name'  => 'voicemail',
                                                                                       'type'   => 'NS',
                                                                                       'value' => 'ns3'
                                                                                       )
                                                                      )


    											)
                              );


    function DnsRecords(&$SoapEngine) {

        dprint("init DnsRecords");

		$_name=trim($_REQUEST['name_filter']);

        if (strlen($_name) && !strstr($_name,'.') && !strstr($_name,'%')) $_name.='%';

		if ($this->typeFilter) {
            $this->filters   = array(
                                     'id'           => trim($_REQUEST['id_filter']),
                                     'zone'         => trim($_REQUEST['zone_filter']),
                                     'name'         => $_name,
                                     'type'         => $this->typeFilter,
                                     'value'        => trim($_REQUEST['value_filter']),
                                     'owner'        => trim($_REQUEST['owner_filter'])
                                    );
        } else {
            $this->filters   = array(
                                     'id'           => trim($_REQUEST['id_filter']),
                                     'zone'         => trim($_REQUEST['zone_filter']),
                                     'name'         => $_name,
                                     'type'         => trim($_REQUEST['type_filter']),
                                     'value'        => trim($_REQUEST['value_filter']),
                                     'owner'        => trim($_REQUEST['owner_filter'])
                                    );
        }

        $this->Records(&$SoapEngine);
        $this->getAllowedDomains();

    }

    function listRecords() {
        $this->showSeachForm();

        if ($this->typeFilter) {
            $filter=array(
                          'id'       => intval($this->filters['id']),
                          'zone'     => $this->filters['zone'],
                          'name'     => $this->filters['name'],
                          'type'     => $this->typeFilter,
                          'value'    => $this->filters['value'],
                          'owner'    => intval($this->filters['owner']),
                          'customer' => intval($this->filters['customer']),
                          'reseller' => intval($this->filters['reseller'])
                          );
        } else {
            $filter=array(
                          'id'       => intval($this->filters['id']),
                          'zone'     => $this->filters['zone'],
                          'name'     => $this->filters['name'],
                          'type'     => $this->filters['type'],
                          'value'    => $this->filters['value'],
                          'owner'    => intval($this->filters['owner']),
                          'customer' => intval($this->filters['customer']),
                          'reseller' => intval($this->filters['reseller'])
                          );
         }
        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result = call_user_func_array(array(&$this->SoapEngine->soapclient,$this->getRecordsFunction),array($Query));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            if ($this->rows > 1 && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <table border=0 align=center>
            <tr><td>$this->rows records found. Click on record id to edit the values.</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            ";
            if ($this->fancy) {
                print "
                <tr bgcolor=lightgrey>
                    <td></th>
                    <td><b>Customer</b></td>
                    <td><b>Zone</b></td>
                    <td><b>Id</b></td>
                    <td><b>Name</b></td>
                    <td><b>Type</b></td>
                    <td><b>Value</b></td>
                    <td><b>Owner</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                </tr>
                ";
            } else {
                print "
                <tr bgcolor=lightgrey>
                    <td></th>
                    <td><b>Customer</b></td>
                    <td><b>Zone</b></td>
                    <td><b>Id</b></td>
                    <td><b>Name</b></td>
                    <td><b>Type</b></td>
                    <td align=right><b>Priority</b></td>
                    <td><b>Value</b></td>
                    <td><b>TTL</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                </tr>
                ";
            }

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->records[$i]) break;
    
                    $record = $result->records[$i];
                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&name_filter=%s&zone_filter=%s&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($record->name),
                    urlencode($record->zone),
                    urlencode($record->id)
                    );

                    if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$record->reseller);

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $record->id) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($record->customer)
                    );

                    $_zone_url = $this->url.sprintf("&service=dns_zones@%s&name_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($record->zone)
                    );

                    if ($this->adminonly) $_zone_url.= sprintf ("&reseller_filter=%s",$record->reseller);

                    $_record_url = $this->url.sprintf("&service=%s@%s&zone_filter=%s&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($record->zone),
                    urlencode($record->id)
                    );

                    if ($this->adminonly) $_record_url.= sprintf ("&reseller_filter=%s",$record->reseller);

                    if ($record->owner) {
                        $_owner_url = sprintf
                        ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                        $this->url,
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($record->owner),
                        $record->owner
                        );
                    } else {
                        $_owner_url='';
                    }          

		            if ($this->fancy) {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
    
                        $bgcolor,
                        $index,
                        $_customer_url,
                        $record->customer,
                        $record->reseller,
                        $_zone_url,
                        $record->zone,
                        $_record_url,
                        $record->id,
                        $record->name,
                        $record->type,
                        $record->value,
                        $record->owner,
                        $record->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td align=right>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
    
                        $bgcolor,
                        $index,
                        $_customer_url,
                        $record->customer,
                        $record->reseller,
                        $_zone_url,
                        $record->zone,
                        $_record_url,
                        $record->id,
                        $record->name,
                        $record->type,
                        $record->priority,
                        $record->value,
                        $record->ttl,
                        $record->changeDate,
                        $_url,
                        $actionText
                        );

                    }
                	$i++;

                }
            }


            print "</table>";

            if ($this->rows == 1 ) {
                $this->showRecord($record);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showSeachFormCustom() {

        printf (" Record Id<input type=text size=7 name=id_filter value='%s'>",$this->filters['id']);
        printf (" Name<input type=text size=20 name=name_filter value='%s'>",$this->filters['name']);

        if (count($this->allowedDomains) > 0) {
            $selected_zone[$this->filters['zone']]='selected';
            print "<select name=zone_filter><option value=''>Zone";
            foreach ($this->allowedDomains as $_zone) {
                printf ("<option value='%s' %s>%s",$_zone,$selected_zone[$_zone],$_zone);
            }
            print "</select>";
        } else {
        	printf (" Zone<input type=text size=20 name=zone_filter value='%s'>",$this->filters['zone']);
        }

        if ($this->typeFilter) {
            printf (" Type %s <input type=hidden name=%s_filter>",$this->typeFilter,$this->typeFilter);
        } else {
            $selected_type[$this->filters['type']]='selected';
            printf (" <select name=type_filter><option value=''>Type");
            foreach (array_keys($this->recordTypes) as $_type) {
                printf ("<option value='%s' %s>%s",$_type,$selected_type[$_type],$_type);
            }
            print "</select>";
        }
        printf (" Value <input type=text size=35 name=value_filter value='%s'>",$this->filters['value']);

    }

    function deleteRecord($dictionary=array()) {

        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id=$dictionary['id'];
        } else {
            $id=$this->filters['id'];
        }

        if (!$id) {
            print "<p><font color=red>Missing record id. </font>";
            return false;
        }

        if ($this->adminonly) {
			$this->initRemoteReplicationEngine($this->filters['reseller']);
        } else {
			$this->initRemoteReplicationEngine($this->reseller);
        }

        $function=array('commit'   => array('name'       => $this->deleteRecordFunction,
                                            'parameters' => array($id),
                                            'logs'       => array('success' => sprintf('Dns record %s has been deleted',$id)))
                        );

		$zone=$this->filters['zone'];
       	unset($this->filters);
        $this->filters['zone']=$zone;

        $result = $this->SoapEngine->execute($function,$this->html);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result = call_user_func_array(array(&$this->SoapEngineRemote,$this->deleteRecordFunction),array($id));

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    unset($this->filters);
                }
            } else {
                return true;
            }
        }

    }

    function showAddForm() {

        /*
        if ($this->adminonly) {
        	if (!$this->filters['reseller']) {
                print "<p>To add a new record you must search first for a customer";
            	return;
            }
        }
        */

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
        ";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        if ($this->adminonly) {
        	printf (" <input type=hidden name=reseller_filter value='%s'>",$this->filters['reseller']);
        }

        print "
        <td align=left>
        ";

        print "
        <input type=submit name=action value=Add>
        ";

        printf (" Name");

        printf (" <input type=text size=20 name=name value='%s'>",trim($_REQUEST['name']));

        if (count($this->allowedDomains) > 0) {

            if ($_REQUEST['zone']) {
                $selected_zone[$_REQUEST['zone']]='selected';
            } else if ($this->filters['zone']) {
                $selected_zone[$this->filters['zone']]='selected';
            } else if ($_zone=$this->getCustomerProperty('dns_records_last_zone')) {
                $selected_zone[$_zone]='selected';
            }
    
            print ".<select name=zone>";
            foreach ($this->allowedDomains as $_zone) {
                printf ("<option value='%s' %s>%s",$_zone,$selected_zone[$_zone],$_zone);
            }
            print "</select>";
        } else {
            if ($_REQUEST['zone']) {
                $_zone_selected=$_REQUEST['zone'];
            } else if ($this->filters['zone']) {
            	$_zone_selected=$this->filters['zone'];
            } else if ($_zone=$this->getCustomerProperty('dns_records_last_zone')) {
                $_zone_selected=$_zone;
            }
        	printf (" Zone <input type=text size=20 name=zone value='%s'>",$_zone_selected);
        }

        if ($this->typeFilter) {
            printf ("Type %s <input type=hidden name=%s>",$this->typeFilter,$this->typeFilter);
        } else {
            print "Type <select name=type>
            ";
    
            if ($_REQUEST['type']) {
                $selected_type[$_REQUEST['type']]='selected';
            } else if ($_type=$this->getCustomerProperty('dns_records_last_type')) {
                $selected_type[$_type]='selected';
            }
    
            foreach(array_keys($this->recordTypes) as $_type) {
                printf ("<option value='%s' %s>%s - %s",$_type,$selected_type[$_type],$_type,$this->recordTypes[$_type]);
            }

            foreach(array_keys($this->recordTypesTemplate) as $_type) {
                printf ("<option value='%s' %s>%s",$_type,$selected_type[$_type],$this->recordTypesTemplate[$_type]['name']);
            }
    
            print "
            </select>
            ";
        }

        printf (" Value<input type=text size=35 name=value value='%s'>",trim($_REQUEST['value']));

        if (!$this->fancy)  {
        	printf (" Priority<input type=text size=5 name=priority value='%s'>",trim($_REQUEST['priority']));
        }

        print "
        </td>
        <td align=right>
        ";
        print "
        </td>
        ";

        $this->printHiddenFormElements();

        print "
        </form>
        </tr>
        </table>
        ";
    }

    function getAllowedDomains() {
        // Filter
        $filter=array(
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );
        // Range
        $range=array('start' => 0,
                     'count' => $this->max_zones_selection
                     );

        // Order
        $orderBy = array('attribute' => 'name',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
        	if ($result->total  > $this->max_zones_selection) return false;
            foreach($result->zones as $zone) {
                if (in_array($zone->name,$this->allowedDomains)) continue;
                $this->allowedDomains[]=$zone->name;
                $seen[$zone->name]++;
            }
        }
    }

    function addRecord($dictionary=array()) {

    	if ($this->typeFilter) {
            $type = $this->typeFilter;
        } else if ($dictionary['type']) {
            $type = $dictionary['type'];
        } else {
            $type = trim($_REQUEST['type']);
        }

        if ($dictionary['name']) {
            $name = $dictionary['name'];
        } else {
            $name = trim($_REQUEST['name']);
        }

        $name=rtrim($name,".");

        if (preg_match("/^(.*)@(.*)$/",$name,$m)) {
            $zone=$m[2];
        } else {
            if ($dictionary['zone']) {
                $zone=$dictionary['zone'];
                $this->skipSaveProperties=true;
            } else if ($_REQUEST['zone']) {
                $zone=$_REQUEST['zone'];
            }

            if ($type=='MBOXFW') {
                $name.='@'.$zone;
            }
        }

        if (!strlen($zone)) {
        	if ($this->html) {
            	printf ("<p><font color=red>Error: Missing zone name. </font>");
            }
            return false;
        }

        $this->filters['zone']=$zone;

        if (!strlen($type)) {
        	if ($this->html) {
            	printf ("<p><font color=red>Error: Missing record type. </font>");
            }
            return false;
        }

        if ($dictionary['value']) {
            $value = $dictionary['value'];
        } else {
            $value = trim($_REQUEST['value']);
        }

        $value=rtrim($value,".");

        if ($this->adminonly) {
            if ($dictionary['reseller']) {
				$this->initRemoteReplicationEngine($dictionary['reseller']);
            } else if ($this->filters['reseller']) {
				$this->initRemoteReplicationEngine($this->filters['reseller']);
            } else {
                if ($this->html) {
                	printf ("<p><font color=red>Error: Missing reseller, please first search zones for a given reseller </font>");
                }
                return false;
            }
        } else {
			$this->initRemoteReplicationEngine($this->reseller);
        }

        if ($dictionary['ttl']) {
            $ttl = intval($dictionary['ttl']);
        } else {
            $ttl = intval(trim($_REQUEST['ttl']));
        }

        if (!$ttl) $ttl=3600;

        if ($dictionary['owner']) {
            $owner = intval($dictionary['owner']);
        } else {
            $owner = intval(trim($_REQUEST['owner']));
        }

        if ($dictionary['priority']) {
            $priority = $dictionary['priority'];
        } else {
            $priority = trim($_REQUEST['priority']);
        }

        if (in_array($type,array_keys($this->recordTypes))) {

            if (!strlen($value)) {
        		if ($this->html) {
                	printf ("<p><font color=red>Error: Missing record value. </font>");
                }
                return false;
            }

            $record=array('name'     => trim($name),
                          'zone'     => trim($zone),
                          'type'     => $type,
                          'value'    => trim($value),
                          'owner'    => intval($owner),
                          'ttl'      => intval($ttl),
                          'priority' => intval($priority)
                          );
     
            if (!$this->skipSaveProperties=true) {
        
                $_p=array(
                          array('name'       => 'dns_records_last_zone',
                                'category'   => 'web',
                                'value'      => $_REQUEST['zone'],
                                'permission' => 'customer'
                               ),
                          array('name'       => 'dns_records_last_type',
                                'category'   => 'web',
                                'value'      => "$type",
                                'permission' => 'customer'
                               )
                          );
        
                $this->setCustomerProperties($_p);
            }
     
            $function=array('commit'   => array('name'       => $this->addRecordFunction,
                                                'parameters' => array($record),
                                                'logs'       => array('success' => sprintf('Dns record %s under %s has been added',$name,$zone))),
                            );
         
            $result = $this->SoapEngine->execute($function,$this->html);
	        dprint_r($result);

            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
        		if ($this->html) {
                	printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                }
                return false;
            } else {
                if (is_object($this->SoapEngineRemote)) {
                    $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
	                $result = call_user_func_array(array(&$this->SoapEngineRemote,$this->addRecordFunction),array($result));

                    if (PEAR::isError($result)) {
                        $error_msg  = $result->getMessage();
                        $error_fault= $result->getFault();
                        $error_code = $result->getCode();
			        	if ($this->html) {
            	            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                        }
                        unset($this->filters);
    
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }

        } else if (in_array($type,array_keys($this->recordTypesTemplate))) {

            foreach (array_values($this->recordTypesTemplate[$type]['records']) as $_records) {
                $value_new='';

                if (strlen($_records['value'])) {
                	if (preg_match("/^_sip/",$_records['name'])) {
                        if (!$value) {
                            $value=$this->getCustomerProperty('dns_records_last_sip_server');
                            if (!$value)  {
                            	$value=$this->getCustomerProperty('sip_proxy');
                            }
                            $save_new_value=false;
                        } else {
                            $save_new_value=true;
                        }
                    }

                    $els=explode("|",$_records['value']);

                    foreach ($els as $el) {
                        if (preg_match("/#VALUE#/",$el)) {
                        	if ($value) {
                            	$value_new=preg_replace("/#VALUE#/",$value,$el);
                            } else {
                                continue;
                            }
                        } else {
                            $value_new=$el;
                        }
                        break;
                    }

                    // save value if type sip server
                    if ($save_new_value && $_records['name'] && preg_match("/^_sip/",$_records['name'])) {
                        $_p=array(
                                  array('name'       => 'dns_records_last_sip_server',
                                        'category'   => 'web',
                                        'value'      => $value,
                                        'permission' => 'customer'
                                       )
                                  );
                
                        $this->setCustomerProperties($_p);
                    }
                }

		        if (!in_array($_records['type'],array_keys($this->recordTypes))) {
                    continue;
                }

                $record=array('name'     => $_records['name'],
                              'zone'     => trim($zone),
                              'type'     => $_records['type'],
                              'value'    => $value_new,
                              'owner'    => intval($owner),
                              'ttl'      => intval($_records['value']),
                              'priority' => intval($_records['priority'])
                              );

                //print_r($record);
                $function=array('commit'   => array('name'       => $this->addRecordFunction,
                                                    'parameters' => array($record),
                                                    'logs'       => array('success' => sprintf('Dns %s record under %s has been added',$_records['type'],$zone))
                                                    )
                                );

                $result = $this->SoapEngine->execute($function,$this->html);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();

                    if ($this->html) {
                    	printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    }

                    return false;
                } else {
                    if (is_object($this->SoapEngineRemote)) {
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);

                        $result = call_user_func_array(array(&$this->SoapEngineRemote,$this->addRecordFunction),array($result));

                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
				        	if ($this->html) {
                            	printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                            }
                            unset($this->filters);
                        }
                    }
                }
            }


        } else {
        	if ($this->html) {
            	printf ("<p><font color=red>Error: Invalid or missing record type. </font>");
            }
            return false;
        }

    }

    function getRecordKeys() {

        // Filter
        $filter=array(
                      'id'       => intval($this->filters['id']),
                      'zone'     => $this->filters['zone'],
                      'name'     => $this->filters['name'],
                      'type'     => $this->filters['type'],
                      'value'    => $this->filters['value'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getRecords($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->records as $record) {
                $this->selectionKeys[]=array('id' => $record->id);
            }
            return true;
        }
    }

    function showRecord($record) {

        print "<h3>Record</h3>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        printf ("<tr><td class=border>Name</td><td class=border>%s</td></td>",
        $record->name);

        foreach (array_keys($this->Fields) as $item) {
            if (is_array($this->havePriority) && $item == 'priority' && !in_array($record->type,$this->havePriority)) continue;

            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($item == 'type') {
            	$selected_type[$record->$item]='selected';

				$select_box=sprintf("<select name=%s_form>",$item);
                foreach(array_keys($this->recordTypes) as $_type) {
                    $select_box.=sprintf ("<option value='%s' %s>%s - %s",$_type,$selected_type[$_type],$_type,$this->recordTypes[$_type]);
                }
    
                foreach(array_keys($this->recordTypesTemplate) as $_type) {
                    $select_box.=sprintf ("<option value='%s' %s>%s",$_type,$selected_type[$_type],$this->recordTypesTemplate[$_type]['name']);
                }

				$select_box.="</select>";

                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border>%s</td>
                </tr>",
                $item_name,
                $select_box
                );

            } else if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=0 name=%s_form rows=4>%s</textarea></td>
                </tr>",
                $item_name,
                $item,
                $record->$item
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=40 type=text value='%s'></td>
                </tr>",
                $item_name,
                $item,
                $record->$item
                );
            }
        }

        printf ("<input type=hidden name=id_filter value='%s'",$record->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function getRecord($id) {
        // Filter
        if (!$id) {
            print "Error in getRecord(): Missing record id";
            return false;
        }

        $filter=array('id'   => $id);

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result = call_user_func_array(array(&$this->SoapEngine->soapclient,$this->getRecordsFunction),array($Query));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->records[0]){
                return $result->records[0];
            } else {
                return false;
            }
        }
    }

    function updateRecord () {
        //print "<p>Updating record ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$record = $this->getRecord(intval($_REQUEST['id_filter']))) {
            return false;
        }

		$this->initRemoteReplicationEngine($record->reseller);

        $record_old=$record;

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $record->$item = intval($_REQUEST[$var_name]);
            } else {
                $record->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function=array('commit'   => array('name'       => $this->updateRecordFunction,
                                            'parameters' => array($record),
                                            'logs'       => array('success' => sprintf('Record %s has been updated',$_REQUEST['id_filter'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);
        dprint_r($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
            if (is_object($this->SoapEngineRemote)) {
                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);

                $result = call_user_func_array(array(&$this->SoapEngineRemote,$this->updateRecordFunction),array($result));

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    unset($this->filters);

                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}

class FancyRecords extends DnsRecords {
    var $fancy = true;

	var $addRecordFunction    = 'addFancyRecord';
    var $deleteRecordFunction = 'deleteFancyRecord';
    var $updateRecordFunction = 'updateFancyRecord';
    var $getRecordsFunction   = 'getFancyRecords';
    var $getRecordFunction    = 'getFancyRecord';

    var $recordTypesTemplate=array();

    var $Fields=array(
                              'type'     => array('type'=>'string'),
                              'value'    => array('type'=>'string')
                              );


}

class EmailAliases extends FancyRecords {
    var $recordTypes=array('MBOXFW'  => 'Email alias');
    var $typeFilter='MBOXFW';
}

class UrlRedirect extends FancyRecords {
    var $recordTypes=array('URL'   => 'URL forwarding');
    var $typeFilter='URL';
}

class TrustedPeers extends Records {

    function TrustedPeers(&$SoapEngine) {

        $this->filters   = array('ip'     => trim($_REQUEST['ip_filter']),
                                 'description'  => trim($_REQUEST['description_filter'])
                                 );

        $this->Records(&$SoapEngine);

        if ($this->version > 1) {

            $this->sortElements=array(
                            'changeDate'  => 'Change date',
                            'description' => 'Description',
                            'ip'          => 'IP address'
                            );
        } else {
            $this->sortElements=array(
                            'description' => 'Description',
                            'ip'          => 'IP address'
                            );
        }

    }

    function listRecords() {

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('ip' => $this->filters['ip'],
                      'description'   => $this->filters['description']
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'description';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $result     = $this->SoapEngine->soapclient->getTrustedPeers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                ";
                if ($this->version > 3) {
                	print "
                    <td><b>Reseller</b></td>
                    <td><b>IP address</b></td>
                    <td><b>Protocol</b></td>
                    <td><b>From pattern</b></td>
                    <td><b>Description</b></td>
                    <td><b>Change date</b></td>
                    ";

                } else if ($this->version > 1) {
                	print "
                    <td><b>Customer</b></td>
                    <td><b>IP address</b></td>
                    <td><b>Protocol</b></td>
                    <td><b>From pattern</b></td>
                    <td><b>Description</b></td>
                    <td><b>Change date</b></td>
                    ";
                } else {
                	print "
                    <td><b>IP address</b></td>
                    <td><b>Protocol</b></td>
                    <td><b>From pattern</b></td>
                    <td><b>Description</b></td>
                    ";
                }
                print "<td><b>Actions</b></td>


            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->peers[$i]) break;
    
                    $peer = $result->peers[$i];
    
                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&ip_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($peer->ip)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['ip_filter'] == $peer->ip) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($this->version > 3) {
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($peer->reseller)
                        );

                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $_customer_url,
                        $peer->reseller,
                        $peer->ip,
                        $peer->protocol,
                        $peer->fromPattern,
                        $peer->description,
                        $peer->changeDate,
                        $_url,
                        $actionText
                        );

                    } else if ($this->version > 1) {
                        $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($peer->customer)
                        );

                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $_customer_url,
                        $peer->reseller,
                        $peer->customer,
                        $peer->ip,
                        $peer->protocol,
                        $peer->fromPattern,
                        $peer->description,
                        $peer->changeDate,
                        $_url,
                        $actionText
                        );
                    } else {
                        printf("
                        <tr bgcolor=%s>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>",
                        $bgcolor,
                        $index,
                        $peer->ip,
                        $peer->protocol,
                        $peer->fromPattern,
                        $peer->description,
                        $_url,
                        $actionText
                        );
                    }

                    printf("
                    </tr>
                    ");
                    $i++;
                }
            }

            print "</table>";

            $this->showPagination($maxrows);

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";
            $this->showCustomerTextBox();

            printf (" IP address<input type=text size=20 name=ipaddress>");
            printf (" Description<input type=text size=30 name=description>");

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";

            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {

        if ($dictionary['ipaddress']) {
            $ipaddress   = $dictionary['ipaddress'];
        } else {
            $ipaddress   = trim($_REQUEST['ipaddress']);
        }

        if ($dictionary['description']) {
            $description   = $dictionary['description'];
        } else {
            $description   = trim($_REQUEST['description']);
        }

        if ($dictionary['owner']) {
            $owner   = $dictionary['owner'];
        } else {
            $owner   = trim($_REQUEST['owner']);
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        if (!strlen($ipaddress) || !strlen($description)) {
            printf ("<p><font color=red>Error: Missing IP or description. </font>");
            return false;
        }

        $peer=array(
                     'ip'          => $ipaddress,
                     'description' => $description,
                     'owner'       => intval($_REQUEST['owner']),
                     'customer'    => intval($customer),
                     'reseller'    => intval($reseller)
                    );

        $function=array('commit'   => array('name'       => 'addTrustedPeer',
                                            'parameters' => array($peer),
                                            'logs'       => array('success' => sprintf('Trusted peers %s has been added',$ipaddress)))
                        );
     
        return $this->SoapEngine->execute($function,$this->html);
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['ip'])) {
            print "<p><font color=red>Error: missing IP address. </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteTrustedPeer',
                                            'parameters' => array($this->filters['ip']),
                                            'logs'       => array('success' => sprintf('Trusted peers %s has been deleted',$this->filters['ip'])))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {

        printf (" IP address<input type=text size=20 name=ip_filter value='%s'>",$this->filters['ip']);
        printf (" Description<input type=text size=30 name=description_filter value='%s'>",$this->filters['description']);

    }

    function showCustomerTextBox () {
        print "Reseller";
        $this->showResellerForm('reseller');
    }

    function showTextBeforeCustomerSelection() {
        print "Reseller";
    }

    function showCustomerForm() {
    }

}
class Carriers extends Records {
    var $carriers=array();

    var $Fields=array(
                              'id'         => array('type'=>'integer',
                                                   'readonly' => true),
                              'name'       => array('type'=>'string')
                              );

    var $sortElements=array(
                            'changeDate' => 'Change date',
                            'name'       => 'Carrier'
                            );

    function Carriers(&$SoapEngine) {
        $this->filters   = array('id'   => trim($_REQUEST['id_filter']),
                                 'name' => trim($_REQUEST['name_filter'])
                                 );

        $this->Records(&$SoapEngine);
    }

    function showCustomerTextBox () {
        print "Reseller";
        $this->showResellerForm('reseller');
    }

    function listRecords() {

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array(
                      'id'       => intval($this->filters['id']),
                      'name'     => $this->filters['name'],
                      'reseller' => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Call function
        $result     = $this->SoapEngine->soapclient->getCarriers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            ";

            print "
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                <td><b>Reseller</b></td>
                <td><b>Carrier</b></th>
                <td><b>Name</b></th>
                <td><b>Gateways</b></th>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->carriers[$i]) break;
    
                    $carrier = $result->carriers[$i];

                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($carrier->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $carrier->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($carrier->id),
                    urlencode($carrier->reseller)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($carrier->reseller)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&carrier_id_filter=%d&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($carrier->id),
                    urlencode($carrier->reseller)
                    );

                    printf("
                    <tr bgcolor=%s>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td>%s</td>
                    <td><a href=%s>Gateways</a></td>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    </tr>",
                    $bgcolor,
                    $index,
                    $_customer_url,
                    $carrier->reseller,
                    $_url,
                    $carrier->id,
                    $carrier->name,
                    $_gateway_url,
                    $carrier->changeDate,
                    $_delete_url,
                    $actionText
                    );

                    printf("
                    </tr>
                    ");
                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($carrier);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;
        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";

            $this->showCustomerTextBox();

            printf (" Name <input type=text size=20 name=name>");

            print "
            </td>
            <td align=right>
            ";

            print "
            </td>
            ";
            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {

        if ($dictionary['name']) {
            $name=$dictionary['name'];
        } else {
            $name = trim($_REQUEST['name']);
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        $structure=array('name'     => $name,
                         'reseller' => intval($reseller)
                         );


        if (!strlen($name)) {
            printf ("<p><font color=red>Error: Missing name. </font>");
            return false;
        }

        $function=array('commit'   => array('name'       => 'addCarrier',
                                            'parameters' => array($structure),
                                            'logs'       => array('success' => sprintf('Carrier %s has been added',$name)))
                        );
     
        return $this->SoapEngine->execute($function,$this->html);
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id   = $dictionary['id'];
        } else {
            $id   = trim($this->filters['id']);
        }

        if (!strlen($id)) {
            print "<p><font color=red>Error: missing carrier id </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteCarrier',
                                            'parameters' => array(intval($id)),
                                            'logs'       => array('success' => sprintf('Carrier %d has been deleted',$id)))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {
        printf (" Carrier <input type=text size=10 name=id_filter value='%s'>",$this->filters['id']);
        printf (" Name <input type=text size=20 name=name_filter value='%s'>",$this->filters['name']);
    }

    function showCustomerForm() {
    }

    function showTextBeforeCustomerSelection() {
        print "Reseller";
    }

    function getRecord($id) {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'  => intval($id));

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'name';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function

        $result     = $this->SoapEngine->soapclient->getCarriers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->carriers[0]){
                return $result->carriers[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($carrier) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Carrier</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<tr>
            <td class=border valign=top>%s</td>
            <td class=border>",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<input name=%s_form type=hidden value='%s'>%s</td>",
                $item,
                $carrier->$item,
                $carrier->$item
                );
            } else {
                printf ("<input name=%s_form type=text value='%s'></td>",
                $item,
                $carrier->$item
                );
            }
            print "
            </tr>";
        }

        printf ("<input type=hidden name=id_filter value='%s'",$carier->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";

        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function updateRecord () {
        //print "<p>Updating carrier ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$carrier = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $carrier->$item = intval($_REQUEST[$var_name]);
            } else {
                $carrier->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function=array('commit'   => array('name'       => 'updateCarrier',
                                            'parameters' => array($carrier),
                                            'logs'       => array('success' => sprintf('Carrier %d has been updated',$_REQUEST['id_filter'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
        	return true;
        }
    }
}

class Gateways extends Records {
    var $carriers=array();
    var $FieldsReadOnly=array(
                              'reseller',
                              'changeDate'
                              );

    var $Fields=array(
                              'id'         => array('type'=>'integer',
                                                   'readonly' => true),
                              'name'       => array('type'=>'string'),
                              'carrier_id' => array('type'=>'integer'),
                              'transport'  => array('type'=>'string'),
                              'ip'         => array('name'=>'IP or hostname',
                                                   'type'=>'string'),
                              'port'       => array('type'=>'integer')
                              );

    //var $transports=array('udp','tcp','tls');
    var $transports=array('udp');

    function Gateways(&$SoapEngine) {
        $this->filters   = array(
                                 'id'         => trim($_REQUEST['id_filter']),
                                 'name'       => trim($_REQUEST['name_filter']),
                                 'carrier_id' => trim($_REQUEST['carrier_id_filter'])
                                 );

        $this->sortElements=array(
                            'changeDate'  => 'Change date',
                            'name'        => 'Gateway',
                            'carrier_id'  => 'Carrier',
                            'ip'          => 'Address'
                            );

        $this->Records(&$SoapEngine);
    }

    function listRecords() {
    	$this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'        => intval($this->filters['id']),
                      'name'      => $this->filters['name'],
                      'carrier_id'=> intval($this->filters['carrier_id']),
                      'customer'  => intval($this->filters['customer']),
                      'reseller'  => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            ";

            print "
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                <td><b>Reseller</b></td>
                <td><b>Gateway</b></th>
                <td><b>Carrier</b></td>
                <td><b>Name</b></th>
                <td><b>Address</b></td>
                <td><b>Rules</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->gateways[$i]) break;
    
                    $gateway = $result->gateways[$i];
    
                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $gateway->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway->id)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($gateway->reseller)
                    );

                    $_carrier_url = $this->url.sprintf("&service=pstn_carriers@%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway->carrier_id),
                    urlencode($gateway->reseller)
                    );

                    $_rules_url = $this->url.sprintf("&service=gateway_rules@%s&gateway_id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway->id),
                    urlencode($gateway->reseller)
                    );

                    $_r=0;

                    printf("
                    <tr bgcolor=%s>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top>%s</td>
                    <td valign=top>%s:%s:%s</td>
                    <td valign=top><a href=%s>Rules</a></td>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    </tr>",
                    $bgcolor,
                    $index,
                    $_customer_url,
                    $gateway->reseller,
                    $_url,
                    $gateway->id,
                    $_carrier_url,
                    $gateway->carrier,
                    $gateway->name,
                    $gateway->transport,
                    $gateway->ip,
                    $gateway->port,
                    $_rules_url,
                    $gateway->changeDate,
                    $_delete_url,
                    $actionText
                    );

                    printf("
                    </tr>
                    ");

                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($gateway);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        $this->getCarriers();

        if (!count($this->carriers)) {
            print "<p>Create a carrier first";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            ";

            printf (" Carrier ");

            print "<select name=carrier_id> ";
            foreach (array_keys($this->carriers) as $_carrier) {
                printf ("<option value='%s'>%s",$_carrier,$this->carriers[$_carrier]);
            }
            printf (" </select>");

            printf (" Name <input type=text size=20 name=name>");

            printf (" Transport ");

            print "<select name=transport> ";

            foreach ($this->transports as $_transport) {
                printf ("<option value='%s'>%s",$_transport,$_transport);
            }

            printf (" </select>");

            printf (" Address<input type=text size=25 name=address>");

            print "
            </td>
            <td align=right>
            ";
            print "
            </td>
            ";
            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {
        if ($dictionary['name']) {
            $name   = $dictionary['name'];
        } else {
            $name   = trim($_REQUEST['name']);
        }

        if ($dictionary['carrier_id']) {
            $carrier_id   = $dictionary['carrier_id'];
        } else {
            $carrier_id   = trim($_REQUEST['carrier_id']);
        }

        if ($dictionary['address']) {
            $address   = $dictionary['address'];
        } else {
            $address   = trim($_REQUEST['address']);
        }

        if ($dictionary['transport']) {
            $transport   = $dictionary['transport'];
        } else {
            $transport   = trim($_REQUEST['transport']);
        }

        if (!strlen($name) || !strlen($carrier_id) || !strlen($address)) {
            printf ("<p><font color=red>Error: Missing gateway name, carrier_id or address</font>");
            return false;
        }

        $address_els=explode(':',$address);

        if (count($address_els) == 1) {
            $ip   = $address_els[0];
            $port ='5060';
        } else if (count($address_els) == 2) {
            $ip   = $address_els[0];
            $port = $address_els[1];
        }

        if (!$port) $port = 5060;

        if (!in_array($transport,$this->transports)) {
        	$transport=$this->transports[0];
        }

        $gateway=array(
                     'name'       => $name,
                     'carrier_id' => intval($carrier_id),
                     'ip'         => $ip,
                     'port'       => intval($port),
                     'transport'  => $transport
                    );

        $function=array('commit'   => array('name'       => 'addGateway',
                                            'parameters' => array($gateway),
                                            'logs'       => array('success' => sprintf('Gateway %s has been added',$name)))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id   = $dictionary['id'];
        } else {
            $id   = trim($this->filters['id']);
        }

        if (!strlen($id)) {
            print "<p><font color=red>Error: missing gateway id. </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteGateway',
                                            'parameters' => array(intval($id)),
                                            'logs'       => array('success' => sprintf('Gateway %d has been deleted',$id)))
                        );
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);

    }

    function showSeachFormCustom() {
        printf (" Gateway <input type=text size=10 name=id_filter value='%s'>",$this->filters['id']);

        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        printf (" </select>");
        printf (" Name <input type=text size=20 name=name_filter value='%s'>",$this->filters['name']);

    }

    function showCustomerForm() {
    }

    function showTextBeforeCustomerSelection() {
        print "Reseller";
    }

    function showRecord($gateway) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Gateway</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<tr>
            <td class=border valign=top>%s</td>
            <td class=border>",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<input name=%s_form type=hidden value='%s'>%s</td>",
                $item,
                $gateway->$item,
                $gateway->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf ("<select name=%s_form>",$item);
                    $selected_carrier[$gateway->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
                    }
                    printf (" </select>");

                } else if ($item == 'transport') {
                    printf ("<select name=%s_form>",$item);
                    $selected_transport[$gateway->$item]='selected';
                    foreach ($this->transports as $_transport) {
                        printf ("<option value='%s' %s>%s",$_transport,$selected_transport[$_transport],$_transport);
                    }
    
                    print "</select>";
    
                } else {
                    printf ("<input name=%s_form size=30 type=text value='%s'></td>",
                    $item,
                    $gateway->$item
                    );
                }
            }
            print "
            </tr>";


        }

        printf ("<input type=hidden name=id_filter value='%s'",$gateway->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";

        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function updateRecord () {
        //print "<p>Updating gateway ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$gateway = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $gateway->$item = intval($_REQUEST[$var_name]);
            } else {
                $gateway->$item = trim($_REQUEST[$var_name]);
            }
        }

        if (!in_array($gateway->transport,$this->transports)) {
            printf ("<font color=red>Invalid transport '%s'</font>",$gateway->transport);
            return false;
        }

        $function=array('commit'   => array('name'       => 'updateGateway',
                                            'parameters' => array($gateway),
                                            'logs'       => array('success' => sprintf('Gateway %s has been updated',$_REQUEST['name_filter'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
        	return true;
        }
    }

    function getRecord($id) {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'  => intval($id));

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'name';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->gateways[0]){
                return $result->gateways[0];
            } else {
                return false;
            }
        }
    }
}

class GatewayRules extends Records {
    var $carriers=array();
    var $FieldsReadOnly=array(
                              'reseller',
                              'changeDate'
                              );

    var $Fields=array(
                              'id'         => array('type'=>'integer','readonly' => true),
                              'gateway_id' => array('type'=>'integer','name' => 'Gateway'),
                              'prefix'     => array('type'=>'string'),
                              'strip'      => array('type'=>'integer'),
                              'prepend'    => array('type'=>'string'),
                              'minLength'  => array('type'=>'integer'),
                              'maxLength'  => array('type'=>'integer')
                              );

    function GatewayRules(&$SoapEngine) {
        $this->filters   = array('id'         => trim($_REQUEST['id_filter']),
                                 'gateway_id' => trim($_REQUEST['gateway_id_filter']),
                                 'carrier_id' => trim($_REQUEST['carrier_id_filter']),
                                 'prefix'     => trim($_REQUEST['prefix_filter']),
                                 );

        $this->sortElements=array(
                            'changeDate' => 'Change date',
                            'gateway'    => 'Gateway',
                            'carrier'    => 'Carrier',
                            'prefix'     => 'Prefix'
                            );
        $this->Records(&$SoapEngine);
    }

    function listRecords() {
    	$this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'         => intval($this->filters['id']),
                      'gateway_id' => intval($this->filters['gateway_id']),
                      'carrier_id' => intval($this->filters['carrier_id']),
                      'prefix'     => $this->filters['prefix'],
                      'customer'   => intval($this->filters['customer']),
                      'reseller'   => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        $result     = $this->SoapEngine->soapclient->getGatewayRules($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            ";

            print "
            <tr bgcolor=lightgrey>
                <td><b></b></th>
                <td><b>Reseller</b></td>
                <td><b>Rule</b></th>
                <td><b>Carrier</b></td>
                <td><b>Gateway</b></td>
                <td><b>Prefix</b></td>
                <td><b>Strip</b></td>
                <td><b>Prepend</b></td>
                <td><b>MinLength</b></td>
                <td><b>MaxLength</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->gateway_rules[$i]) break;
    
                    $gateway_rule = $result->gateway_rules[$i];

                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway_rule->id),
                    urlencode($gateway_rule->reseller)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $gateway_rule->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway_rule->id),
                    urlencode($gateway_rule->reseller)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($gateway_rule->reseller)
                    );

                    $_carrier_url = $this->url.sprintf("&service=pstn_carriers@%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway_rule->carrier_id),
                    urlencode($gateway_rule->reseller)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway_rule->gateway_id),
                    urlencode($gateway_rule->reseller)
                    );

                    printf("
                    <tr bgcolor=%s>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a> (%d)</td>
                    <td valign=top><a href=%s>%s (%d)</a></td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    </tr>",
                    $bgcolor,
                    $index,
                    $_customer_url, $gateway_rule->reseller,
                    $_url,     $gateway_rule->id,
                    $_carrier_url, $gateway_rule->carrier,$gateway_rule->carrier_id,
                    $_gateway_url, $gateway_rule->gateway,$gateway_rule->gateway_id,
                    $gateway_rule->prefix,
                    $gateway_rule->strip,
                    $gateway_rule->prepend,
                    $gateway_rule->minLength,
                    $gateway_rule->maxLength,
                    $gateway_rule->changeDate,
                    $_delete_url,
                    $actionText
                    );

                    printf("
                    </tr>
                    ");

                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($gateway_rule);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        $this->getGateways();

        if (!count($this->gateways)) {
            print "<p>Create a gateway first";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
            ";
            printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
            print "
            <td align=left>
            ";

            print "
            <input type=submit name=action value=Add>
            <input type=hidden name=sortBy value=changeDate>
            ";

            print "Gateway <select name=gateway_id> ";
            foreach (array_keys($this->gateways) as $_gateway) {
                printf ("<option value='%s'>%s",$_gateway,$this->gateways[$_gateway]);
            }
            printf (" </select>");

            printf (" Prefix <input type=text size=15 name=prefix>");
            printf (" Strip <input type=text size=5 name=strip>");
            printf (" Prepend <input type=text size=15 name=prepend>");
            printf (" Min length <input type=text size=5 name=minLength>");
            printf (" Max length <input type=text size=5 name=maxLength>");

            print "
            </td>
            ";
            $this->printHiddenFormElements();

            print "
            </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {
        if ($dictionary['gateway_id']) {
            $gateway_id   = $dictionary['gateway_id'];
        } else {
            $gateway_id = trim($_REQUEST['gateway_id']);
        }

        if ($dictionary['prefix']) {
            $prefix   = $dictionary['prefix'];
        } else {
            $prefix   = trim($_REQUEST['prefix']);
        }

        if ($dictionary['strip']) {
            $strip   = $dictionary['strip'];
        } else {
            $strip   = trim($_REQUEST['strip']);
        }

        if ($dictionary['prepend']) {
            $prepend   = $dictionary['prepend'];
        } else {
            $prepend   = trim($_REQUEST['prepend']);
        }

        if ($dictionary['minLength']) {
            $minLength   = $dictionary['minLength'];
        } else {
            $minLength   = trim($_REQUEST['minLength']);
        }

        if ($dictionary['maxLength']) {
            $maxLength   = $dictionary['maxLength'];
        } else {
            $maxLength   = trim($_REQUEST['maxLength']);
        }

        if (!strlen($gateway_id)) {
            printf ("<p><font color=red>Error: Missing gateway id</font>");
            return false;
        }

        $rule=array(
                     'gateway_id' => intval($gateway_id),
                     'prefix'     => $prefix,
                     'prepend'    => $prepend,
                     'strip'      => intval($strip),
                     'minLength'  => intval($minLength),
                     'maxLength'  => intval($maxLength)
                    );

        $function=array('commit'   => array('name'       => 'addGatewayRule',
                                            'parameters' => array($rule),
                                            'logs'       => array('success' => sprintf('Gateway rule has been added')))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id   = $dictionary['id'];
        } else {
            $id   = trim($this->filters['id']);
        }

        if (!strlen($id)) {
            print "<p><font color=red>Error: missing rule id </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteGatewayRule',
                                            'parameters' => array(intval($id)),
                                            'logs'       => array('success' => sprintf('Gateway rule %d has been deleted',$id)))
                        );
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);

    }

    function showSeachFormCustom() {
        printf (" Rule <input type=text size=15 name=id_filter value='%s'>",$this->filters['id']);
        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        printf (" </select>");
        printf (" Gateway <input type=text size=15 name=gateway_id_filter value='%s'>",$this->filters['gateway_id']);
        printf (" Prefix <input type=text size=15 name=prefix_filter value='%s'>",$this->filters['prefix']);
    }

    function showCustomerForm() {
    }
    function showTextBeforeCustomerSelection() {
        print "Reseller";
    }

    function showRecord($rule) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Rule</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<tr>
            <td class=border valign=top>%s</td>
            <td class=border>",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<input name=%s_form type=hidden value='%s'>%s</td>",
                $item,
                $rule->$item,
                $rule->$item
                );
            } else {
                if ($item == 'gateway_id') {
                    printf ("<select name=%s_form>",$item);
                    $selected_gateway[$rule->$item]='selected';

                    foreach (array_keys($this->gateways) as $_gateway) {
                        printf ("<option value='%s' %s>%s",$_gateway,$selected_gateway[$_gateway],$this->gateways[$_gateway]);
                    }

                    print "</select>";
    
                } else {
                    printf ("<input name=%s_form size=30 type=text value='%s'></td>",
                    $item,
                    $rule->$item
                    );
                }
            }
            print "
            </tr>";


        }

        printf ("<input type=hidden name=reseller_filter value='%s'",$rule->reseller);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";


        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function updateRecord () {
        //print "<p>Updating rule ...";

        if (!$_REQUEST['id_form'] || !strlen($_REQUEST['reseller_filter'])) {
        	return;
        }

        if (!$rule = $this->getRecord($_REQUEST['id_form'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $rule->$item = intval($_REQUEST[$var_name]);
            } else {
                $rule->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function=array('commit'   => array('name'       => 'updateGatewayRule',
                                            'parameters' => array($rule),
                                            'logs'       => array('success' => sprintf('Rule %d has been updated',$_REQUEST['id_form'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
        	return true;
        }
    }

    function getRecord($id) {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'  => intval($id));

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $this->sorting['sortBy']    = 'gateway';
        $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $result     = $this->SoapEngine->soapclient->getGatewayRules($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->gateway_rules[0]){
                return $result->gateway_rules[0];
            } else {
                return false;
            }
        }
    }

}

class Routes extends Records {
    var $carriers=array();

    var $Fields=array(
                              'id'          => array('type'=>'integer',
                                                     'readonly' => true),
                              'carrier_id'  => array('type'=>'integer','name'=>'Carrier'),
                              'prefix'      => array('type'=>'string'),
                              'originator'  => array('type'=>'string'),
                              'priority'    => array('type'=>'integer')
                              );

    var $sortElements=array(
                            'prefix'       => 'Prefix',
                            'priority'     => 'Priority'
                            );

    function Routes(&$SoapEngine) {
        $this->filters   = array('prefix'    => trim($_REQUEST['prefix_filter']),
                                 'priority'  => trim($_REQUEST['priority_filter']),
                                 'carrier_id'=> trim($_REQUEST['carrier_id_filter']),
                                 'reseller'  => trim($_REQUEST['reseller_filter']),
                                 'id'        => trim($_REQUEST['id_filter'])
                                 );

        $this->Records(&$SoapEngine);
    }

    function listRecords() {
        $this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('prefix'       => $this->filters['prefix'],
                      'carrier_id'   => intval($this->filters['carrier_id']),
                      'reseller'     => intval($this->filters['reseller']),
                      'id'           => intval($this->filters['id'])
                      );


        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'prefix';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <table border=0 align=center>
            <tr><td>$this->rows records found</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            ";

            print "
            <tr bgcolor=lightgrey>
                <td><b>Id</b></th>
                <td><b>Reseller</b></td>
                <td><b>Route</b></th>
                <td><b>Carrier</b></td>
                <td><b>Gateways</b></td>
                <td><b>Prefix</b></th>
                <td><b>Originator</b></td>
                <td><b>Priority</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
    
                    if (!$result->routes[$i]) break;
    
                    $route = $result->routes[$i];
    
                    $index=$this->next+$i+1;

                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%d",
                    urlencode($this->SoapEngine->service),
                    urlencode($route->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $route->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%d",
                    urlencode($this->SoapEngine->service),
                    urlencode($route->id)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($route->reseller)
                    );

                    $_carrier_url = $this->url.sprintf("&service=pstn_carriers@%s&id_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($route->carrier_id)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&carrier_id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($route->carrier_id),
                    urlencode($route->reseller)
                    );

                    printf("
                    <tr bgcolor=%s>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>Gateways</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    </tr>",
                    $bgcolor,
                    $index,
                    $_customer_url,
                    $route->reseller,
                    $_url,
                    $route->id,
                    $_carrier_url,
                    $route->carrier,
                    $_gateway_url,
                    $route->prefix,
                    $route->originator,
                    $route->priority,
                    $route->changeDate,
                    $_delete_url,
                    $actionText
                    );

                    printf("
                    </tr>
                    ");
                    $i++;
    
                }
    
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($route);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        if (!count($this->carriers)) {
            print "<p>Create a carrier first";
            return false;
        }

        print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightblue>
        <tr>
        ";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <td align=left>
        ";

        print "
        <input type=submit name=action value=Add>
        ";

        printf (" Carrier: ");

        print "<select name=carrier_id> ";
        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s'>%s",$_carrier,$this->carriers[$_carrier]);
        }
        printf (" </select>");

        printf (" Prefix <input type=text size=20 name=prefix>");
        printf (" Originator <input type=text size=20 name=originator>");
        printf (" Priority <input type=text size=5 name=priority>");

        print "
        </td>
        <td align=right>
        ";
        print "
        </td>
        ";

        $this->printHiddenFormElements();

        print "
        </form>
        </tr>
        </table>
        ";
    }

    function addRecord($dictionary=array()) {
        if ($dictionary['prefix']) {
            $prefix   = $dictionary['prefix'];
        } else {
            $prefix   = trim($_REQUEST['prefix']);
        }

        if ($dictionary['carrier_id']) {
            $carrier_id   = $dictionary['carrier_id'];
        } else {
            $carrier_id   = trim($_REQUEST['carrier_id']);
        }

        if ($dictionary['originator']) {
            $originator   = $dictionary['originator'];
        } else {
            $originator   = trim($_REQUEST['originator']);
        }

        if ($dictionary['priority']) {
            $priority   = $dictionary['priority'];
        } else {
            $priority   = trim($_REQUEST['priority']);
        }

        if (!strlen($carrier_id)) {
            printf ("<p><font color=red>Error: Missing carrier id. </font>");
            return false;
        }
 
        $route=array(
                     'prefix'       => $prefix,
                     'originator'   => $originator,
                     'carrier_id'   => intval($carrier_id),
                     'priority'     => intval($priority)
                     );

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'addRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %s has been added',$prefix)))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);

    }

    function deleteRecord($dictionary=array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id   = $dictionary['id'];
        } else {
            $id   = trim($this->filters['id']);
        }

        if (!strlen($id)) {
            print "<p><font color=red>Error: missing route id. </font>";
            return false;
        }

        $route=array('id'=> intval($id));

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'deleteRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %s has been deleted',$prefix)))
                        );
     
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {

        printf (" Route <input type=text size=10 name=id_filter value='%s'>",$this->filters['id']);
        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        printf (" Prefix<input type=text size=15 name=prefix_filter value='%s'>",$this->filters['prefix']);

    }

    function showCustomerTextBox () {
        print "Reseller";
        $this->showResellerForm('reseller');
    }

    function showCustomerForm() {
    }

    function showTextBeforeCustomerSelection() {
        print "Reseller";
    }

    function getRecord($id) {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'  => intval($id));

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'prefix';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function

        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->routes[0]){
                return $result->routes[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($route) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Route</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<tr>
            <td class=border valign=top>%s</td>
            <td class=border>",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<input name=%s_form type=hidden value='%s'>%s</td>",
                $item,
                $route->$item,
                $route->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf ("<select name=%s_form>",$item);
                    $selected_carrier[$route->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
                    }
                    printf (" </select>");

                } else {
                    printf ("<input name=%s_form type=text value='%s'></td>",
                    $item,
                    $route->$item
                    );
                }
            }
            print "
            </tr>";
        }

        printf ("<input type=hidden name=id_filter value='%s'",$carier->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";

        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function updateRecord () {
        //print "<p>Updating route ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$route = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $route->$item = intval($_REQUEST[$var_name]);
            } else {
                $route->$item = trim($_REQUEST[$var_name]);
            }
        }

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'updateRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %d has been updated',$_REQUEST['id_filter'])))
                        );
        $result = $this->SoapEngine->execute($function,$this->html);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

            return false;
        } else {
        	return true;
        }
    }
}

class Customers extends Records {
    var $children     = array();
    var $showAddForm  = false;

    var $sortElements = array(
                            'changeDate'   => 'Change date',
                            'username'     => 'Username',
                            'firstName'    => 'First name',
                            'lastName'     => 'Last name',
                            'organization' => 'Organization',
                            'customer'     => 'Customer'
                            );

    var $propertiesItems = array('sip_credit'          => array('name'      => 'Credit for SIP accounts',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'sip_alias_credit'    => array('name'      => 'Credit for SIP aliases',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'enum_range_credit'   => array('name'      => 'Credit for ENUM ranges',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'enum_number_credit'  => array('name'      => 'Credit for ENUM numbers',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'dns_zone_credit'     => array('name'      => 'Credit for DNS zones',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
	                             'email_credit'        => array('name'      => 'Credit for E-mail aliases',
                                                               'category'   => 'credit',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'pstn_access'         => array('name'      => 'Access to PSTN',
                                                               'category'   => 'sip',
                                                               'permission' => 'admin',
                                                               'resellerMayManageForChildAccounts' => true
                                                               ),
                                 'pstn_changes'         => array('name'      => 'Change PSTN settings',
                                                               'category'   => 'sip',
                                                               'permission' => 'reseller'
                                                               ),
                                 'prepaid_changes'      => array('name'      => 'Change Prepaid',
                                                               'category'   => 'sip',
                                                               'permission' => 'reseller'
                                                               ),
                                 'voicemail_server'    => array('name'      => 'Voicemail server address',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'voicemail_access_number'    => array('name'      => 'Voicemail access number',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'FUNC_access_number'    => array('name'      => 'Call forwarding access number',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'sip_proxy'           => array('name'      => 'SIP Proxy server address',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'store_clear_text_passwords' => array('name'      => 'Store clear text passwords',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'xcap_root'           => array('name'      => 'XCAP Root URL',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'absolute_voicemail_uri'=> array('name'    => 'Use absolute voicemail uri',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
    		    			     'dns_admin_email'     => array('name'     => 'DNS zones administrator email',
                                                               'category' => 'dns',
                                                               'permission'  => 'customer'),
                                 'support_web'         => array('name'      => 'Support web site',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'support_email'       => array('name'      => 'Support email address',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'support_company'     => array('name'      => 'Support organization',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'cdrtool_address'     => array('name'      => 'CDRTool address',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'sip_settings_page'   => array('name'      => 'SIP settings page',
                                                               'category'   => 'sip',
                                                               'permission' => 'customer'
                                                               ),
                                 'records_per_page'    => array('name'     => 'Records per page',
                                                               'category'  => 'web',
                                                               'permission'  => 'customer'
                                                               )

                                 );

    var $FieldsReadOnly=array(
                              'id'          => array('type'=>'integer'),
                              'reseller'    => array('type'=>'integer')
                              );
    var $Fields=array(
                              'resellerActive' => array ('type'      => 'boolean',
                                                      'name'      => 'Reseller active',
                                                      'adminonly' => true
                                                     ),
                              'impersonate'     => array('type'       =>'integer',
                                                     'name'       =>'Impersonate'),
                              'companyCode' => array('type'       =>'text',
                                                     'name'       =>'Company code',
                                                     'adminonly'  => true
                                                     ),
                              'balance'     => array('type'       => 'float',
                                                     'adminonly'  => true
                                                     ),
                              'credit'      => array('type'       => 'float',
                                                     'adminonly'  => true
                                                     ),
                              'username'    => array('type'       =>'text'
                                                     ),
                              'password'    => array('type'=>'text',
                                                     'name'=>'Password'),
                              'firstName'   => array('type'=>'text',
                                                     'name'=>'First name'),
                              'lastName'    => array('type'=>'text',
                                                     'name'=>'Last name'),
                              'organization'=> array('type'=>'text'),
                              'tel'         => array('type'=>'text'),
                              'fax'         => array('type'=>'text'),
                              'sip'         => array('type'=>'text'),
                              'enum'        => array('type'=>'text'),
                              'mobile'      => array('type'=>'text'),
                              'email'       => array('type'=>'text'),
                              'web'         => array('type'=>'text'),
                              'address'     => array('type'=>'textarea'),
                              'postcode'    => array('type'=>'text'),
                              'city'        => array('type'=>'text'),
                              'state'       => array('type'=>'text'),
                              'country'     => array('type'=>'text'),
                              'timezone'    => array('type'=>'text'),
                              'language'    => array('type'=>'text'),
                              'vatNumber'   => array('type'=>'text',
                                                     'name'=>'VAT number'),
                              'bankAccount' => array('type'=>'text',
                                                     'name'=>'Bank account'
                                                     ),
                             'billingEmail' => array('type'=>'text',
                                                     'name'=>'Billing email'
                                                     ),
                           'billingAddress' => array('type'=>'textarea',
                                                     'name'=>'Billing address'
                                                     )
                              );

    var $addFields=array(
                              'username'    => array('type'       =>'text'
                                                     ),
                              'password'    => array('type'=>'text',
                                                     'name'=>'Password'),
                              'firstName'   => array('type'=>'text',
                                                     'name'=>'First name'),
                              'lastName'    => array('type'=>'text',
                                                     'name'=>'Last name'),
                              'organization'=> array('type'=>'text'),
                              'tel'         => array('type'=>'text'),
                              'email'       => array('type'=>'text'),
                              'address'     => array('type'=>'textarea'),
                              'postcode'    => array('type'=>'text'),
                              'city'        => array('type'=>'text'),
                              'state'       => array('type'=>'text'),
                              'country'     => array('type'=>'text'),
                              'timezone'    => array('type'=>'text'),
                              );

        var $states=array(
        array("label"=>"", "value"=>"N/A"),
        array("label"=>"-- CANADA --", "value"=>"-"),
        array("label"=>"Alberta", "value"=>"AB"),
        array("label"=>"British Columbia", "value"=>"BC"),
        array("label"=>"Manitoba", "value"=>"MB"),
        array("label"=>"New Brunswick", "value"=>"NB"),
        array("label"=>"Newfoundland/Labrador", "value"=>"NL"),
        array("label"=>"Northwest Territory", "value"=>"NT"),
        array("label"=>"Nova Scotia", "value"=>"NS"),
        array("label"=>"Nunavut", "value"=>"NU"),
        array("label"=>"Ontario", "value"=>"ON"),
        array("label"=>"Prince Edward Island", "value"=>"PE"),
        array("label"=>"Quebec", "value"=>"QC"),
        array("label"=>"Saskatchewan", "value"=>"SN"),
        array("label"=>"Yukon", "value"=>"YT"),
        array("label"=>"---- US -----", "value"=>"-"),
        array("label"=>"Alabama", "value"=>"AL"),
        array("label"=>"Alaska", "value"=>"AK"),
        array("label"=>"American Samoa", "value"=>"AS"),
        array("label"=>"Arizona", "value"=>"AZ"),
        array("label"=>"Arkansas", "value"=>"AR"),
        array("label"=>"California", "value"=>"CA"),
        array("label"=>"Canal Zone", "value"=>"CZ"),
        array("label"=>"Colorado", "value"=>"CO"),
        array("label"=>"Connecticut", "value"=>"CT"),
        array("label"=>"Delaware", "value"=>"DE"),
        array("label"=>"District of Columbia", "value"=>"DC"),
        array("label"=>"Florida", "value"=>"FL"),
        array("label"=>"Georgia", "value"=>"GA"),
        array("label"=>"Guam", "value"=>"GU"),
        array("label"=>"Hawaii", "value"=>"HI"),
        array("label"=>"Idaho", "value"=>"ID"),
        array("label"=>"Illinois", "value"=>"IL"),
        array("label"=>"Indiana", "value"=>"IN"),
        array("label"=>"Iowa", "value"=>"IA"),
        array("label"=>"Kansas", "value"=>"KS"),
        array("label"=>"Kentucky", "value"=>"KY"),
        array("label"=>"Louisiana", "value"=>"LA"),
        array("label"=>"Maine", "value"=>"ME"),
        array("label"=>"Mariana Islands", "value"=>"MP"),
        array("label"=>"Maryland", "value"=>"MD"),
        array("label"=>"Massachusetts", "value"=>"MA"),
        array("label"=>"Michigan", "value"=>"MI"),
        array("label"=>"Minnesota", "value"=>"MN"),
        array("label"=>"Mississippi", "value"=>"MS"),
        array("label"=>"Missouri", "value"=>"MO"),
        array("label"=>"Montana", "value"=>"MT"),
        array("label"=>"Nebraska", "value"=>"NE"),
        array("label"=>"Nevada", "value"=>"NV"),
        array("label"=>"New Hampshire", "value"=>"NH"),
        array("label"=>"New Jersey", "value"=>"NJ"),
        array("label"=>"New Mexico", "value"=>"NM"),
        array("label"=>"New York", "value"=>"NY"),
        array("label"=>"North Carolina", "value"=>"NC"),
        array("label"=>"North Dakota", "value"=>"ND"),
        array("label"=>"Ohio", "value"=>"OH"),
        array("label"=>"Oklahoma", "value"=>"OK"),
        array("label"=>"Oregon", "value"=>"OR"),
        array("label"=>"Pennsylvania", "value"=>"PA"),
        array("label"=>"Puerto Rico", "value"=>"PR"),
        array("label"=>"Rhode Island", "value"=>"RI"),
        array("label"=>"South Carolina", "value"=>"SC"),
        array("label"=>"South Dakota", "value"=>"SD"),
        array("label"=>"Tennessee", "value"=>"TN"),
        array("label"=>"Texas", "value"=>"TX"),
        array("label"=>"Utah", "value"=>"UT"),
        array("label"=>"Vermont", "value"=>"VT"),
        array("label"=>"Virgin Islands", "value"=>"VI"),
        array("label"=>"Virginia", "value"=>"VA"),
        array("label"=>"Washington", "value"=>"WA"),
        array("label"=>"West Virginia", "value"=>"WV"),
        array("label"=>"Wisconsin", "value"=>"WI"),
        array("label"=>"Wyoming", "value"=>"WY"),
        array("label"=>"APO", "value"=>"AP"),
        array("label"=>"AEO", "value"=>"AE"),
        array("label"=>"AAO", "value"=>"AA"),
        array("label"=>"FPO", "value"=>"FP")
        );
        
        var $countries=array(
        array("label"=>"Ascension Island",    "value"=>"AC"),
        array("label"=>"Afghanistan",        "value"=>"AF"),
        array("label"=>"Albania",        "value"=>"AL"),
        array("label"=>"Algeria",        "value"=>"DZ"),
        array("label"=>"American Samoa",    "value"=>"AS"),
        array("label"=>"Andorra",        "value"=>"AD"),
        array("label"=>"Angola",        "value"=>"AO"),
        array("label"=>"Anguilla",        "value"=>"AI"),
        array("label"=>"Antarctica",        "value"=>"AQ"),
        array("label"=>"Antigua And Barbuda",    "value"=>"AG"),
        array("label"=>"Argentina",        "value"=>"AR"),
        array("label"=>"Armenia",        "value"=>"AM"),
        array("label"=>"Aruba",            "value"=>"AW"),
        array("label"=>"Australia",        "value"=>"AU"),
        array("label"=>"Austria",        "value"=>"AT"),
        array("label"=>"Azerbaijan",        "value"=>"AZ"),
        array("label"=>"Bahamas",        "value"=>"BS"),
        array("label"=>"Bahrain",        "value"=>"BH"),
        array("label"=>"Bangladesh",            "value"=>"BD"),
        array("label"=>"Barbados",            "value"=>"BB"),
        array("label"=>"Belarus",            "value"=>"BY"),
        array("label"=>"Belgium",            "value"=>"BE"),
        array("label"=>"Belize",            "value"=>"BZ"),
        array("label"=>"Benin",                "value"=>"BJ"),
        array("label"=>"Bermuda",            "value"=>"BM"),
        array("label"=>"Bhutan",            "value"=>"BT"),
        array("label"=>"Bolivia",            "value"=>"BO"),
        array("label"=>"Bosnia And Herzegowina","value"=>"BA"),
        array("label"=>"Botswana",        "value"=>"BW"),
        array("label"=>"Bouvet Island",        "value"=>"BV"),
        array("label"=>"Brazil",        "value"=>"BR"),
        array("label"=>"British Indian Ocean Territory",    "value"=>"IO"),
        array("label"=>"Brunei Darussalam",    "value"=>"BN"),
        array("label"=>"Bulgaria",            "value"=>"BG"),
        array("label"=>"Burkina Faso",            "value"=>"BF"),
        array("label"=>"Burundi",            "value"=>"BI"),
        array("label"=>"Cambodia",            "value"=>"KH"),
        array("label"=>"Cameroon",            "value"=>"CM"),
        array("label"=>"Canada",            "value"=>"CA"),
        array("label"=>"Cape Verde",            "value"=>"CV"),
        array("label"=>"Cayman Islands",        "value"=>"KY"),
        array("label"=>"Central African Republic",    "value"=>"CF"),
        array("label"=>"Chad",            "value"=>"TD"),
        array("label"=>"Chile",            "value"=>"CL"),
        array("label"=>"China",            "value"=>"CN"),
        array("label"=>"Christmas Island",    "value"=>"CX"),
        array("label"=>"Cocos (Keeling) Islands",    "value"=>"CC"),
        array("label"=>"Colombia",        "value"=>"CO"),
        array("label"=>"Comoros",        "value"=>"KM"),
        array("label"=>"Congo",            "value"=>"CG"),
        array("label"=>"Congo, Democratic People's Republic",    "value"=>"CD"),
        array("label"=>"Cook Islands",         "value"=>"CK"),
        array("label"=>"Costa Rica",        "value"=>"CR"),
        array("label"=>"Cote d'Ivoire",        "value"=>"CI"),
        array("label"=>"Croatia (local name: Hrvatska)",    "value"=>"HR"),
        array("label"=>"Cuba",        "value"=>"CU"),
        array("label"=>"Cyprus",    "value"=>"CY"),
        array("label"=>"Czech Republic","value"=>"CZ"),
        array("label"=>"Denmark",    "value"=>"DK"),
        array("label"=>"Djibouti",    "value"=>"DJ"),
        array("label"=>"Dominica",    "value"=>"DM"),
        array("label"=>"Dominican Republic",    "value"=>"DO"),
        array("label"=>"East Timor",    "value"=>"TP"),
        array("label"=>"Ecuador",    "value"=>"EC"),
        array("label"=>"Egypt",        "value"=>"EG"),
        array("label"=>"El Salvador",    "value"=>"SV"),
        array("label"=>"Equatorial Guinea",    "value"=>"GQ"),
        array("label"=>"Eritrea",    "value"=>"ER"),
        array("label"=>"Estonia",    "value"=>"EE"),
        array("label"=>"Ethiopia",    "value"=>"ET"),
        array("label"=>"Falkland Islands (Malvinas)",    "value"=>"FK"),
        array("label"=>"Faroe Islands",    "value"=>"FO"),
        array("label"=>"Fiji",        "value"=>"FJ"),
        array("label"=>"Finland",    "value"=>"FI"),
        array("label"=>"France",    "value"=>"FR"),
        array("label"=>"French Guiana",    "value"=>"GF"),
        array("label"=>"French Polynesia",    "value"=>"PF"),
        array("label"=>"French Southern Territories",    "value"=>"TF"),
        array("label"=>"Gabon",        "value"=>"GA"),
        array("label"=>"Gambia",    "value"=>"GM"),
        array("label"=>"Georgia",    "value"=>"GE"),
        array("label"=>"Germany",    "value"=>"DE"),
        array("label"=>"Ghana",    "value"=>"GH"),
        array("label"=>"Gibraltar",    "value"=>"GI"),
        array("label"=>"Greece",    "value"=>"GR"),
        array("label"=>"Greenland",    "value"=>"GL"),
        array("label"=>"Grenada",    "value"=>"GD"),
        array("label"=>"Guadeloupe",    "value"=>"GP"),
        array("label"=>"Guam",    "value"=>"GU"),
        array("label"=>"Guatemala",    "value"=>"GT"),
        array("label"=>"Guernsey",    "value"=>"GG"),
        array("label"=>"Guinea",    "value"=>"GN"),
        array("label"=>"Guinea-Bissau",    "value"=>"GW"),
        array("label"=>"Guyana",    "value"=>"GY"),
        array("label"=>"Haiti",    "value"=>"HT"),
        array("label"=>"Heard And Mc Donald Islands",    "value"=>"HM"),
        array("label"=>"Honduras",    "value"=>"HN"),
        array("label"=>"Hong Kong",    "value"=>"HK"),
        array("label"=>"Hungary",    "value"=>"HU"),
        array("label"=>"Iceland",    "value"=>"IS"),
        array("label"=>"India",    "value"=>"IN"),
        array("label"=>"Indonesia",    "value"=>"ID"),
        array("label"=>"Iran (Islamic Republic Of)",    "value"=>"IR"),
        array("label"=>"Iraq",    "value"=>"IQ"),
        array("label"=>"Ireland",    "value"=>"IE"),
        array("label"=>"Isle of Man",    "value"=>"IM"),
        array("label"=>"Israel",    "value"=>"IL"),
        array("label"=>"Italy",    "value"=>"IT"),
        array("label"=>"Jamaica",    "value"=>"JM"),
        array("label"=>"Japan",    "value"=>"JP"),
        array("label"=>"Jersey",    "value"=>"JE"),
        array("label"=>"Jordan",    "value"=>"JO"),
        array("label"=>"Kazakhstan",    "value"=>"KZ"),
        array("label"=>"Kenya",    "value"=>"KE"),
        array("label"=>"Kiribati",    "value"=>"KI"),
        array("label"=>"Korea, Democratic People's Republic Of",    "value"=>"KP"),
        array("label"=>"Korea, Republic Of",    "value"=>"KR"),
        array("label"=>"Kuwait",    "value"=>"KW"),
        array("label"=>"Kyrgyzstan",    "value"=>"KG"),
        array("label"=>"Lao People's Democratic Republic",    "value"=>"LA"),
        array("label"=>"Latvia",    "value"=>"LV"),
        array("label"=>"Lebanon",    "value"=>"LB"),
        array("label"=>"Lesotho",    "value"=>"LS"),
        array("label"=>"Liberia",    "value"=>"LR"),
        array("label"=>"Libyan Arab Jamahiriya",    "value"=>"LY"),
        array("label"=>"Liechtenstein",    "value"=>"LI"),
        array("label"=>"Lithuania",    "value"=>"LT"),
        array("label"=>"Luxembourg",    "value"=>"LU"),
        array("label"=>"Macau",    "value"=>"MO"),
        array("label"=>"Macedonia, The Former Yugoslav",    "value"=>"MK"),
        array("label"=>"Of",    "value"=>"Republic"),
        array("label"=>"Madagascar",    "value"=>"MG"),
        array("label"=>"Malawi",    "value"=>"MW"),
        array("label"=>"Malaysia",    "value"=>"MY"),
        array("label"=>"Maldives",    "value"=>"MV"),
        array("label"=>"Mali",    "value"=>"ML"),
        array("label"=>"Malta",    "value"=>"MT"),
        array("label"=>"Marshall Islands",    "value"=>"MH"),
        array("label"=>"Martinique",    "value"=>"MQ"),
        array("label"=>"Mauritania",    "value"=>"MR"),
        array("label"=>"Mauritius",    "value"=>"MU"),
        array("label"=>"Mayotte",    "value"=>"YT"),
        array("label"=>"Mexico",    "value"=>"MX"),
        array("label"=>"Micronesia, Federated States Of",    "value"=>"FM"),
        array("label"=>"Moldova, Republic Of",    "value"=>"MD"),
        array("label"=>"Monaco",    "value"=>"MC"),
        array("label"=>"Mongolia",    "value"=>"MN"),
        array("label"=>"Montserrat",    "value"=>"MS"),
        array("label"=>"Morocco",    "value"=>"MA"),
        array("label"=>"Mozambique",    "value"=>"MZ"),
        array("label"=>"Myanmar",    "value"=>"MM"),
        array("label"=>"Namibia",    "value"=>"NA"),
        array("label"=>"Nauru",    "value"=>"NR"),
        array("label"=>"Nepal",    "value"=>"NP"),
        array("label"=>"Netherlands",    "value"=>"NL"),
        array("label"=>"Netherlands Antilles",    "value"=>"AN"),
        array("label"=>"New Caledonia",    "value"=>"NC"),
        array("label"=>"New Zealand",    "value"=>"NZ"),
        array("label"=>"Nicaragua",    "value"=>"NI"),
        array("label"=>"Niger",    "value"=>"NE"),
        array("label"=>"Nigeria",    "value"=>"NG"),
        array("label"=>"Niue",    "value"=>"NU"),
        array("label"=>"Norfolk Island",    "value"=>"NF"),
        array("label"=>"Northern Mariana Islands",    "value"=>"MP"),
        array("label"=>"Norway",    "value"=>"NO"),
        array("label"=>"Oman",    "value"=>"OM"),
        array("label"=>"Pakistan",    "value"=>"PK"),
        array("label"=>"Palau",    "value"=>"PW"),
        array("label"=>"Palestinian Territories",    "value"=>"PS"),
        array("label"=>"Panama",    "value"=>"PA"),
        array("label"=>"Papua New Guinea",    "value"=>"PG"),
        array("label"=>"Paraguay",    "value"=>"PY"),
        array("label"=>"Peru",    "value"=>"PE"),
        array("label"=>"Philippines",    "value"=>"PH"),
        array("label"=>"Pitcairn",    "value"=>"PN"),
        array("label"=>"Poland",    "value"=>"PL"),
        array("label"=>"Portugal",    "value"=>"PT"),
        array("label"=>"Puerto Rico",    "value"=>"PR"),
        array("label"=>"Qatar",    "value"=>"QA"),
        array("label"=>"Reunion",    "value"=>"RE"),
        array("label"=>"Romania",    "value"=>"RO"),
        array("label"=>"Russian Federation",    "value"=>"RU"),
        array("label"=>"Rwanda",    "value"=>"RW"),
        array("label"=>"Saint Kitts And Nevis",    "value"=>"KN"),
        array("label"=>"Saint Lucia",    "value"=>"LC"),
        array("label"=>"Saint Vincent And The Grenadines",    "value"=>"VC"),
        array("label"=>"Samoa",    "value"=>"WS"),
        array("label"=>"San Marino",    "value"=>"SM"),
        array("label"=>"Sao Tome And Principe",    "value"=>"ST"),
        array("label"=>"Saudi Arabia",    "value"=>"SA"),
        array("label"=>"Senegal",    "value"=>"SN"),
        array("label"=>"Seychelles",    "value"=>"SC"),
        array("label"=>"Sierra Leone",    "value"=>"SL"),
        array("label"=>"Singapore",    "value"=>"SG"),
        array("label"=>"Slovakia (Slovak Republic)",    "value"=>"SK"),
        array("label"=>"Slovenia",    "value"=>"SI"),
        array("label"=>"Solomon Islands",    "value"=>"SB"),
        array("label"=>"Somalia",    "value"=>"SO"),
        array("label"=>"South Africa",    "value"=>"ZA"),
        array("label"=>"South Georgia And South Sandwich",    "value"=>"GS"),
        array("label"=>"Spain",    "value"=>"ES"),
        array("label"=>"Sri Lanka",    "value"=>"LK"),
        array("label"=>"St. Helena",    "value"=>"SH"),
        array("label"=>"St. Pierre And Miquelon",    "value"=>"PM"),
        array("label"=>"Sudan",    "value"=>"SD"),
        array("label"=>"Suriname",    "value"=>"SR"),
        array("label"=>"Svalbard And Jan Mayen Islands",    "value"=>"SJ"),
        array("label"=>"Swaziland",    "value"=>"SZ"),
        array("label"=>"Sweden",    "value"=>"SE"),
        array("label"=>"Switzerland",    "value"=>"CH"),
        array("label"=>"Syrian Arab Republic",    "value"=>"SY"),
        array("label"=>"Taiwan, Province Of China",    "value"=>"TW"),
        array("label"=>"Tajikistan",    "value"=>"TJ"),
        array("label"=>"Tanzania, United Republic Of",    "value"=>"TZ"),
        array("label"=>"Thailand",    "value"=>"TH"),
        array("label"=>"Togo",    "value"=>"TG"),
        array("label"=>"Tokelau",    "value"=>"TK"),
        array("label"=>"Tonga",    "value"=>"TO"),
        array("label"=>"Trinidad And Tobago",    "value"=>"TT"),
        array("label"=>"Tunisia",    "value"=>"TN"),
        array("label"=>"Turkey",    "value"=>"TR"),
        array("label"=>"Turkmenistan",    "value"=>"TM"),
        array("label"=>"Turks And Caicos Islands",    "value"=>"TC"),
        array("label"=>"Tuvalu",    "value"=>"TV"),
        array("label"=>"Uganda",    "value"=>"UG"),
        array("label"=>"Ukraine",    "value"=>"UA"),
        array("label"=>"United Arab Emirates",    "value"=>"AE"),
        array("label"=>"United Kingdom",    "value"=>"UK"),
        array("label"=>"United States",    "value"=>"US"),
        array("label"=>"United States Minor Outlying Islands",    "value"=>"UM"),
        array("label"=>"Uruguay",    "value"=>"UY"),
        array("label"=>"Uzbekistan",    "value"=>"UZ"),
        array("label"=>"Vanuatu",    "value"=>"VU"),
        array("label"=>"Vatican City State (Holy See)",    "value"=>"VA"),
        array("label"=>"Venezuela",    "value"=>"VE"),
        array("label"=>"Viet Nam",    "value"=>"VN"),
        array("label"=>"Virgin Islands (British)",    "value"=>"VG"),
        array("label"=>"Virgin Islands (U.S.)",    "value"=>"VI"),
        array("label"=>"Wallis And Futuna Islands",    "value"=>"WF"),
        array("label"=>"Western Sahara",    "value"=>"EH"),
        array("label"=>"Yemen",    "value"=>"YE"),
        array("label"=>"Yugoslavia",    "value"=>"YU"),
        array("label"=>"Zaire",    "value"=>"ZR"),
        array("label"=>"Zambia",    "value"=>"ZM"),
        array("label"=>"Zimbabwe",    "value"=>"ZW"),
        array("label"=>"Undefined",    "value"=>"N/A")
        );

    function Customers(&$SoapEngine) {
        dprint("init Customers");

        $this->filters   = array(
                           'username'       => trim($_REQUEST['username_filter']),
                           'firstName'      => trim($_REQUEST['firstName_filter']),
                           'lastName'       => trim($_REQUEST['lastName_filter']),
                           'organization'   => trim($_REQUEST['organization_filter']),
                           'tel'            => trim($_REQUEST['tel_filter']),
                           'email'          => trim($_REQUEST['email_filter']),
                           'web'            => trim($_REQUEST['web_filter']),
                           'country'        => trim($_REQUEST['country_filter']),
                           'city'           => trim($_REQUEST['city_filter']),
                           'only_resellers' => trim($_REQUEST['only_resellers_filter'])
                           );

        $this->Records(&$SoapEngine);

        $this->showAddForm = $_REQUEST['showAddForm'];

        if (is_array($this->SoapEngine->customer_properties)) {
        	$this->customer_properties = &$this->SoapEngine->customer_properties;
        } else {
        	$this->customer_properties = array();
        }

        $this->allProperties=array_merge($this->propertiesItems,$this->customer_properties);

        if ($_REQUEST['action'] == 'Add' || $_REQUEST['action'] == 'Copy' ||
            $_REQUEST['action'] == 'Delete' || $_REQUEST['action'] == 'Update') {

            if ($this->reseller) {
                $customer_engine_remote=$this->SoapEngine->login_credentials['reseller_filters'][$this->reseller]['customer_engine_remote'];

                if (strlen($customer_engine_remote) && $this->SoapEngine->soapEngine != $customer_engine_remote) {
                    // replicate change

                    if (in_array($customer_engine_remote,array_keys($this->SoapEngine->soapEngines))) {
                        $this->SOAPloginRemote = array(
                                               "username"    => $this->SoapEngine->soapEngines[$customer_engine_remote]['username'],
                                               "password"    => $this->SoapEngine->soapEngines[$customer_engine_remote]['password'],
                                               "admin"       => true,
                                               "impersonate" => intval($this->reseller)
                                           );
        
                        //dprint_r($this->SOAPloginRemote);
                        $this->SOAPurlRemote=$this->SoapEngine->soapEngines[$customer_engine_remote]['url'];
        
                        $log=sprintf ("and syncronize changes to <a href=%swsdl target=wsdl>%s</a>",$this->SOAPurlRemote,$this->SOAPurlRemote);
                        dprint($log);
    
                        $this->SoapAuthRemote  = array('auth', $this->SOAPloginRemote , 'urn:AGProjects:NGNPro', 0, '');
    
                        $this->SoapEngineRemote = new $this->SoapEngine->soap_class($this->SOAPurlRemote);

                        if (strlen($this->soapEngines[$customer_engine_remote]['timeout'])) {
                            $this->SoapEngineRemote->_options['timeout'] = intval($this->soapEngines[$customer_engine_remote]['timeout']);
                        } else {
                            $this->SoapEngineRemote->_options['timeout'] = $this->soapTimeout;
                        }

                        $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                        $this->SoapEngineRemote->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
                    }
                }
            }
        }
    }

    function showSeachForm() {
         printf ("<p><b>%s</b>",
        $this->SoapEngine->ports[$this->SoapEngine->port]['description'],
        '%'
        );

         print "
        <p>
        <table border=0 class=border width=100% bgcolor=lightgreen>
        <tr>
        ";

        printf ("<form method=post name=engines action=%s>",$_SERVER['PHP_SELF']);
        print "
        <td align=left>
        ";
        print "
        <input type=submit name=action value=Search>
        ";

        $this->showEngineSelection();

        print "
        </td>
        <td align=right>
        ";
        $this->showSortForm();

        print "
        </td>
        </tr>
        <tr>
        <td colspan=2>Id";


        $this->showCustomerSelection();
        $this->showResellerSelection();

        $this->showSeachFormCustom();
        print "</td>
        </tr>
        ";
        $this->printHiddenFormElements('skipServiceElement');
        print "
        </form>
        </table>
        ";
    }

    function listRecords() {

        // Filter
        $filter=array('username'     => $this->filters['username'],
                      'firstName'    => $this->filters['firstName'],
                      'lastName'     => $this->filters['lastName'],
                      'organization' => $this->filters['organization'],
                      'tel'          => $this->filters['tel'],
                      'email'        => $this->filters['email'],
                      'web'          => $this->filters['web'],
                      'city'         => $this->filters['city'],
                      'country'      => $this->filters['country'],
                      'only_resellers' => $this->filters['only_resellers'],
                      'customer'     => intval($this->filters['customer']),
                      'reseller'     => intval($this->filters['reseller'])
                      );

        //print_r($filter);

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        $this->showSeachForm();

        if ($this->showAddForm) {
            $this->showAddForm();
            return true;
        }

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        if ($this->adminonly && $this->filters['only_resellers']) {
            $result     = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $result     = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {

            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <table border=0 align=center width=100%>
            <tr><td align=left width=33%>";

            $_add_url = $this->url.sprintf("&service=%s&showAddForm=1",
            urlencode($this->SoapEngine->service)
            );

            printf ("<a href=%s>New customer</a> ",$_add_url);


            if ($this->adminonly) {
                if ($this->adminonly && $this->filters['reseller']) {
                	$_add_url = $this->url.sprintf("&service=%s&showAddForm=1&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($this->filters['reseller'])
                    );
                    printf (" | <a href=%s>New customer under reseller %s</a> ",$_add_url,$this->filters['reseller']);
                }
            }

            print "
            <td align=center width=33%>$this->rows records found</td>
            <td>
            </td></tr>
            </table>
            ";

            if ($this->rows > 1) {

                print "
                <table border=0 cellpadding=2 width=100%>
                <tr bgcolor=lightgrey>
                    <td><b>Id</b></th>
                    <td><b>Customer</b></td>
                    <td><b>Alias of</b></td>
                    <td><b>Username</b></td>
                    <td><b>Name</b></td>
                    <td><b>Organization</b></td>
                    <td><b>Country</b></td>
                    <td><b>E-mail</b></td>
                    <td><b>Phone number</b></td>
                    <td><b>Change date</b></td>
                    <td><b>Actions</b></td>
                </tr>
                ";
            }

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows > 1) {
                while ($i < $maxrows)  {

                    if (!$result->accounts[$i]) break;
    
                    $customer = $result->accounts[$i];

                    $index = $this->next+$i+1;
    
                    $rr=floor($index/2);
                    $mod=$index-$rr*2;
            
                    if ($mod ==0) {
                        $bgcolor="lightgrey";
                    } else {
                        $bgcolor="white";
                    }

                    $_url = $this->url.sprintf("&service=%s&action=Delete&reseller_filter=%s&customer_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($customer->reseller),
                    urlencode($customer->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['customer_filter'] == $customer->id) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_customer_url = $this->url.sprintf("&service=%s&reseller_filter=%s&customer_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($customer->reseller),
                    urlencode($customer->id)
                    );

                    printf("
                    <tr bgcolor=%s>
                    <td>%s</td>
                    <td><a href=%s>%s.%s</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s %s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=mailto:%s>%s</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=%s>%s</a>
                    ",
                    $bgcolor,
                    $index,
                    $_customer_url,
                    $customer->id,
                    $customer->reseller,
                    $customer->impersonate,
                    $customer->username,
                    $customer->firstName,
                    $customer->lastName,
                    $customer->organization,
                    $customer->country,
                    $customer->email,
                    $customer->email,
                    $customer->tel,
                    $customer->changeDate,
                    $_url,
                    $actionText
                    );

                    $this->showExtraActions($customer);
                    print "</td>
                    </tr>
                    ";

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1 ) {
                $customer = $result->accounts[0];
                $this->showRecord($customer);
            }


            $this->showPagination($maxrows);

            return true;
        }
    }

    function showSeachFormCustom() {
        printf (" Username<input type=text size=10 name=username_filter value='%s'>",$this->filters['username']);
        printf (" FN<input type=text size=10 name=firstName_filter value='%s'>",$this->filters['firstName']);
        printf (" LN<input type=text size=15 name=lastName_filter value='%s'>",$this->filters['lastName']);
        printf (" Organization<input type=text size=15 name=organization_filter value='%s'>",$this->filters['organization']);
        printf (" Email<input type=text size=25 name=email_filter value='%s'>",$this->filters['email']);

        if ($this->adminonly) {
            if ($this->filters['only_resellers']) $check_only_resellers_filter='checked';
            printf (" Resellers<input type=checkbox name=only_resellers_filter value=1 %s>",$check_only_resellers_filter);
        }
    }

    function deleteRecord($confirm) {
        if (!$confirm && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Delete again to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['customer'])) {
            print "<p><font color=red>Error: missing customer id. </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteAccount',
                                            'parameters' => array(intval($this->filters['customer'])),
                                            'logs'       => array('success' => sprintf('Customer id %s has been deleted',$this->filters['customer'])))
                        );

        if ($this->SoapEngine->execute($function,$this->html)) {
            if (is_object($this->SoapEngineRemote)) {

                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result     = $this->SoapEngineRemote->deleteAccount(intval($this->filters['customer']));
                
                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode != 5000)  {
                        printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    }
                }
            }

            unset($this->filters);
            return true;

        } else {
            return false;
        }

    }

    function getRecord($id) {

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $result     = $this->SoapEngine->soapclient->getAccount(intval($id));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            return $result;
        }
    }

    function showRecordHeader($customer) {
    }

    function showRecordFooter($customer) {
    }

    function showExtraActions($customer) {
    }

    function showRecord($customer) {
        //dprint_r($customer);

        $this->showRecordHeader($customer);

        print "<table border=0 cellpadding=10>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "

        <tr>
        <td align=left>";
        if ($_REQUEST['action'] != 'Delete' && $_REQUEST['action'] != 'Copy') {
            print "<input type=submit name=action value=Update>";
            printf (" E-mail <input type=checkbox name=notify value='1'> account information");
        }

        print "</td>
        <td align=right>";

        printf ("<input type=hidden name=customer_filter value=%s>",$customer->id);

        if ($this->adminonly) {
            printf ("<input type=hidden name=reseller_filter value=%s>",$customer->reseller);
        }

          if ($this->adminonly || $this->reseller == $customer->reseller) {
            if ($_REQUEST['action'] != 'Delete') {
                print "<input type=submit name=action value=Copy>";
            }

            print "<input type=submit name=action value=Delete>";

            if ($_REQUEST['action'] == 'Delete' || $_REQUEST['action'] == 'Copy') {
                print "<input type=hidden name=confirm value=1>";
            }
        }

        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td valign=top>

        <table border=0>
        ";

        printf ("<tr bgcolor=lightgrey>
        <td class=border>Field</td>
        <td class=border>Value</td>
        </tr>");
        foreach (array_keys($this->FieldsReadOnly) as $item) {
            printf ("<tr>
            <td class=border valign=top>%s</td>
            <td class=border>%s</td>
            </tr>",
            ucfirst($item),
            $customer->$item
            );
        }

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($item=='timezone') {
                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>";

                $this->showTimezones($customer->$item);

                print "</td>
                </tr>
                ";
            } else if ($item=='state') {
                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>
                <select name=state_form>";

                $selected_state[$customer->state]='selected';

                foreach ($this->states as $_state) {
                    printf ("<option value='%s' %s>%s",$_state['value'],$selected_state[$_state['value']],$_state['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";
            } else if ($item=='country') {
                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>
                <select name=country_form>";

                $selected_country[$customer->country]='selected';

                foreach ($this->countries as $_country) {
                    printf ("<option value='%s' %s>%s",$_country['value'],$selected_country[$_country['value']],$_country['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";

            } else if ($item=='resellerActive' && ($customer->reseller != $customer->id)) {
                printf ("<input name=%s_form type=hidden value='%s'>",
                        $item,
                        $customer->$item);

            } else if ($item=='impersonate') {

                if ($customer->reseller != $customer->id) {
                    if ($this->adminonly || $this->customer == $customer->reseller) {
                        printf ("<tr>
                        <td class=border valign=top>%s</td>",
                        $item_name
                        );
                        print "<td class=border>      ";
                        $this->getChildren($customer->reseller);
                        if (count($this->children)> 0) {
                            print "
                            <select name=impersonate_form>
                            <option>";
                            $selected_impersonate[$customer->impersonate]='selected';
                            foreach (array_keys($this->children) as $_child) {
                                printf ("<option value='%s' %s>%s. %s %s",$_child,$selected_impersonate[$_child],$_child,$this->children[$_child]['firstName'],$this->children[$_child]['lastName']);
                            }
        
                            print "
                            </select>
                            ";
                        } else {
                            printf ("
                            <input name=%s_form size=30 type=text value='%s'>
                            ",
                            $item,
                            $customer->$item
                            );
                        }
                        print "
                        </td>
                        </tr>
                        ";
                    } else {
                        printf ("
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item,
                        $customer->$item
                        );
                    }
                    } else {
                    printf ("
                    <input name=%s_form type=hidden value='%s'>
                    ",
                    $item,
                    $customer->$item
                    );
                }

            } else {
                if ($this->Fields[$item]['type'] == 'textarea') {
                    printf ("
                    <tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $customer->$item
                    );
                } elseif ($this->Fields[$item]['type'] == 'boolean') {
                    if ($this->Fields[$item]['adminonly'] && !$this->adminonly) {
                        printf ("
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item,
                        $customer->$item
                        );
                    } else {
                        $_var='select_'.$item;
                        ${$_var}[$customer->$item]='selected';

                        printf ("
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border>
                        <select name=%s_form>
                        <option value='0' %s>False
                        <option value='1' %s>True
                        </select>
                        </td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        ${$_var}[0],
                        ${$_var}[1]
                        );
                    }
                } else {
                    if ($this->Fields[$item]['adminonly'] && !$this->adminonly) {
                        printf ("
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item,
                        $customer->$item
                        );
                    } else {
                        printf ("
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item
                        );
                    }
                }
            }
        }

        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        //print "</form>";
        print "
        </table>
        ";

        /*
        print "<pre>";
        print_r($customer);
        print "</pre>";
        */

        print "</td>
        <td valign=top>";
        /*
        print "<pre>";
        print_r($this->login_credentials);
        print "</pre>";
        */

        print "
        <table border=0>";

        if ($this->login_credentials['login_type'] == 'admin') {
            printf ("<tr bgcolor=lightgrey>
            <td class=border>Category</td>
            <td class=border>Permission</td>
            <td class=border>Property</td>
            <td class=border>Value</td>
            <td class=border>Description</td>
            </tr>");
        } else if ($this->login_credentials['login_type'] == 'reseller') {
            printf ("<tr bgcolor=lightgrey>
            <td class=border>Permission</td>
            <td class=border>Property</td>
            <td class=border>Value</td>
            </tr>"
            );
        } else {
            printf ("<tr bgcolor=lightgrey>
            <td class=border>Property</td>
            <td class=border>Value</td>
            </tr>"
            );
        }

        foreach ($customer->properties as $_property) {
            if (in_array($_property->name,array_keys($this->allProperties))) {
                $this->allProperties[$_property->name]['value']=$_property->value;
            }
        }

        foreach (array_keys($this->allProperties) as $item) {
            $item_print=preg_replace("/_/"," ",$item);

            $_permission=$this->allProperties[$item]['permission'];

            if ($this->login_credentials['login_type'] == 'admin') {
                if ($this->allProperties[$item]['permission'] == 'admin' &&
                        $customer->id != $customer->reseller &&
                        $this->allProperties[$item]['resellerMayManageForChildAccounts']) {

                        $_permission='reseller';
                }

                printf ("<tr>
                <td class=border>%s</td>
                <td class=border>%s</td>
                <td class=border>%s</td>
                <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                <td class=border>%s</td>
                </tr>",
                $this->allProperties[$item]['category'],
                ucfirst($_permission),
                $item_print,
                $item,
                $this->allProperties[$item]['value'],
                $this->allProperties[$item]['name']
                );
            } else if ($this->login_credentials['login_type'] == 'reseller') {
                // logged in as reseller

                if ($this->allProperties[$item]['permission'] == 'admin') {
                    if ($customer->id == $customer->reseller ) {
                        // reseller cannot modify himself for items with admin permission
                        if (!$this->allProperties[$item]['invisible']) {
                            printf ("<tr>
                            <td class=border>%s</td>
                            <td class=border>%s</td>
                            <td class=border>%s </td>
                            </tr>",
                            ucfirst($this->allProperties[$item]['permission']),
                            $this->allProperties[$item]['name'],
                            $this->allProperties[$item]['value']
                            );
                        }
                    } else {
                        if ($this->allProperties[$item]['resellerMayManageForChildAccounts']) {
                            // reseller can manage these properties for his customers
                            printf ("<tr>
                            <td class=border>%s</td>
                            <td class=border>%s</td>
                            <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                            </tr>",
                            'Reseller',
                            $this->allProperties[$item]['name'],
                            $item,
                            $this->allProperties[$item]['value']
                            );
                        } else {
                            if (!$this->allProperties[$item]['invisible']) {
                                // otherwise cannot modify them
                                printf ("<tr>
                                <td class=border>%s</td>
                                <td class=border>%s</td>
                                <td class=border>%s </td>
                                </tr>",
                                ucfirst($this->allProperties[$item]['permission']),
                                $this->allProperties[$item]['name'],
                                $this->allProperties[$item]['value']
                                );
                            }
                        }
                    }
                } else {
                    printf ("<tr>
                    <td class=border>%s</td>
                    <td class=border>%s</td>
                    <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                    </tr>",
                    ucfirst($this->allProperties[$item]['permission']),
                    $this->allProperties[$item]['name'],
                    $item,
                    $this->allProperties[$item]['value']
                    );
                }
            } else {
                // logged in as customer
                if ($this->allProperties[$item]['permission'] == 'admin' || $this->allProperties[$item]['permission'] == 'reseller' ) {
                    if (!$this->allProperties[$item]['invisible']) {
                        printf ("<tr>
                        <td class=border>%s</td>
                        <td class=border>%s </td>
                        </tr>",
                        $this->allProperties[$item]['name'],
                        $this->allProperties[$item]['value']
                        );
                    }
                } else {
                    printf ("<tr>
                    <td class=border>%s</td>
                    <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                    </tr>",
                    $this->allProperties[$item]['name'],
                    $item,
                    $this->allProperties[$item]['value']
                    );
                }

            }

        }

        print "
        </table>
        ";

        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print "</form>";
        print "
        </td>
        </tr>
        </table>
        ";

        $this->showRecordFooter($customer);

    }

    function updateRecord () {
        //print "<p>Updating customer ...";

        if (!strlen($this->filters['customer'])) {
            return false;
        }

        if (!$customer=$this->getRecord($this->filters['customer'])) {
            return false;
        }

		if ($_REQUEST['notify']) {

            $customer_notify=array('firstName'=> $customer->firstName,
                                   'lastName' => $customer->lastName,
                                   'email'    => $customer->email,
                                   'username' => $customer->username,
                                   'password' => $customer->password
                                   );

            if ($this->notify($customer_notify)) {
                print "<p>";
            	printf (_("The login account details have been sent to %s"), $customer->email);
            	return true;
            } else {
                print "<p>";
            	printf (_("Error sending e-mail notification"));
                return false;
            }
        }

        if (!$this->updateBefore($customer)) {
            return false;
        }

        $customer->credit      = floatval($customer->credit);
        $customer->balance     = floatval($customer->balance);

        foreach ($customer->properties as $_property) {
            $properties[]=$_property;
        }

        if (is_array($properties)) {
            $customer->properties=$properties;
        } else {
            $customer->properties=array();
        }

        $customer_old = $customer;

        // update properties

        foreach (array_keys($this->allProperties) as $item) {
            $var_name   = $item.'_form';

            $updated_property=array();

            foreach (array_keys($customer->properties) as $_key) {
                $_property=$customer->properties[$_key];

                if ($_property->name == $item) {
                    // update property

                    if ($_property->permission == 'admin') {
                        if ($this->login_credentials['login_type'] == 'admin') {
                            $customer->properties[$_key]->value=trim($_REQUEST[$var_name]);
                        } else if ($this->login_credentials['login_type'] == 'reseller' && $this->allProperties[$item]['resellerMayManageForChildAccounts']) {
                            if ($customer->id != $customer->reseller) {
                                $customer->properties[$_key]->value=trim($_REQUEST[$var_name]);
                            }
                        }

                    } else if ($_property->permission == 'reseller') {
                        if ($this->login_credentials['login_type'] == 'admin' || $this->login_credentials['login_type'] == 'reseller') {
                            $customer->properties[$_key]->value=trim($_REQUEST[$var_name]);
                        }
                    } else {
                        $customer->properties[$_key]->value=trim($_REQUEST[$var_name]);
                    }

                    $updated_property[$item]++;

                    break;
                }
            }

            if (!$updated_property[$item] && strlen($_REQUEST[$var_name])) {
                // add new property

                unset($var_value);
                unset($_permission);

                if ($this->allProperties[$item]['permission'] == 'admin') {
                    $_permission = 'admin';

                    if ($this->login_credentials['login_type'] == 'admin') {
                        $var_value   =  trim($_REQUEST[$var_name]);
                    } else if ($this->login_credentials['login_type'] == 'reseller' && $this->allProperties[$item]['resellerMayManageForChildAccounts']) {
                        if ($customer->id != $customer->reseller) {
                            $var_value   =  trim($_REQUEST[$var_name]);
                        }
                    }

                } else if ($this->allProperties[$item]['permission'] == 'reseller') {
                    $_permission = 'reseller';

                    if ($this->login_credentials['login_type'] == 'admin' || $this->login_credentials['login_type'] == 'reseller') {
                        $var_value   =  trim($_REQUEST[$var_name]);
                    }
                } else {
                    $_permission = 'customer';
                    $var_value   =  trim($_REQUEST[$var_name]);
                }

                if (strlen($var_value)) {
                    $customer->properties[] = array('name'       => $item,
                                                    'value'      => $var_value,
                                                    'category'   => $this->allProperties[$item]['category'],
                                                    'permission' => $this->allProperties[$item]['permission']
                                                     );
                }

            }

        }

        /*
        print "<pre>";
        print_r($customer->properties);
        print "</pre>";
        */

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer' || $this->Fields[$item]['type'] == 'boolean') {
                $customer->$item = intval($_REQUEST[$var_name]);
            } else if ($this->Fields[$item]['type'] == 'float') {
                $customer->$item = floatval($_REQUEST[$var_name]);
            } else {
                $customer->$item = trim($_REQUEST[$var_name]);
            }
        }

        $customer->tel  = preg_replace("/[^\+0-9]/","",$customer->tel);
        $customer->fax  = preg_replace("/[^\+0-9]/","",$customer->fax);
        $customer->enum = preg_replace("/[^\+0-9]/","",$customer->enum);

        if (!strlen($_REQUEST['password_form'])) $customer->password = $this->RandomString(6);

        if (!strlen($_REQUEST['state_form']))    $customer->state    = 'N/A';
        if (!strlen($_REQUEST['country_form']))  $customer->country  = 'N/A';
        if (!strlen($_REQUEST['city_form']))     $customer->city     = 'Unknown';
        if (!strlen($_REQUEST['address_form']))  $customer->address  = 'Unknown';
        if (!strlen($_REQUEST['postcode_form'])) $customer->postcode = 'Unknown';
        if (!strlen($_REQUEST['tel_form']))      $customer->tel      = '+19999999999';

        if ($customer->reseller != $customer->id) {
            // a subaccount cannot change his own impersonate field
            if (!$this->adminonly) {
                if ($this->customer != $customer->reseller) {
                    $customer->impersonate=$customer_old->impersonate;
                }
            }
        }

        $function=array('commit'   => array('name'       => 'updateAccount',
                                            'parameters' => array($customer),
                                            'logs'       => array('success' => sprintf('Customer id %s has been updated',$customer->id)))
                        );
     
        if ($this->SoapEngine->execute($function,$this->html)) {

            // update remote
            if (is_object($this->SoapEngineRemote)) {

                $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                $result     = $this->SoapEngineRemote->updateAccount($customer);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();

                    if ($error_fault->detail->exception->errorcode == 5000) {
                        // try add the missing customer
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                        $result     = $this->SoapEngineRemote->addAccount($customer);

                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

        		            // roll back local changes

                            $function=array('commit'   => array('name'       => 'updateAccount',
                                                                'parameters' => array($customer_old),
                                                                'logs'       => array('success' => sprintf('Customer id %s has been rolled back',$customer->id))
                                                                )
                                            );
                         
                            $this->SoapEngine->execute($function,$this->html);
                    		return false;

                        } else {
                    		return true;
                        }

                    } else {
                    	// roll back local changes
                        printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
    
                        $function=array('commit'   => array('name'       => 'updateAccount',
                                                            'parameters' => array($customer_old),
                                                            'logs'       => array('success' => sprintf('Customer id %s has been rolled back',$customer->id))
                                                            )
                                        );
                     
                        $this->SoapEngine->execute($function,$this->html);
                    	return false;
                    }

                } else {
                    return true;
                }
            }

            $this->updateAfter($customer,$customer_old);
            return true;
        } else {
            return false;
        }
    }

    function showTimezones($timezone) {
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }

        print "<select name=timezone_form>";
        print "\n<option>";
        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);
            if ($timezone==$buffer) {
                $selected="selected";
            } else {
                $selected="";
            }
            print "\n<option $selected>";
            print "$buffer";
        }
        fclose($fp);
        print "</select>";
    }

    function getChildren($reseller) {
        return;
        // Filter

        $filter=array('reseller'     => intval($reseller));

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        $orderBy = array('attribute' => 'firstName',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $i=0;
            if ($result->total > 100) return;
            while ($i < $result->total) {
                $customer = $result->accounts[$i];

                   $this->children[$customer->id]=array('firstName'    => $customer->firstName,
                                                     'lastName'     => $customer->lastName,
                                                     'organization' => $customer->organization
                                               );
                   $i++;
            }
        }
    }

    function copyRecord () {
        //print "<p>Copy customer ...";

        if (!strlen($this->filters['customer'])) {
            return false;
        }

        if (!$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Copy again to confirm the copy</font>";
            return true;
        }

        if (!$customer=$this->getRecord($this->filters['customer'])) {
            return false;
        }

        $customer->credit      = floatval($customer->credit);
        $customer->balance     = floatval($customer->balance);

        foreach ($customer->properties as $_property) {
            $properties[]=$_property;
        }

        if (is_array($properties)) {
            $customer->properties=$properties;
        } else {
            $customer->properties=array();
        }

        // change username
        $customer_new=$customer;
        unset($customer_new->id);

        $j=1;
        while ($j < 9) {

            $customer_new->username=$customer->username.$j;

            $function=array('commit'   => array('name'       => 'addAccount',
                                                'parameters' => array($customer_new),
                                                'logs'       => array('success' => sprintf('Customer id %s has been copied',$customer->id)))
                            );
    
            if ($this->SoapEngine->execute($function,$this->html)) {
                // update remote
                if (is_object($this->SoapEngineRemote)) {
                    if ($_id = $this->getCustomerId($customer_new->username)) {

                        $customerRemote=$customer_new;
                        $customerRemote->id = intval($_id);
    
                        $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                        $result     = $this->SoapEngineRemote->addAccount($customerRemote);

                        if (PEAR::isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
    
                            $function=array('commit'   => array('name'       => 'deleteAccount',
                                                                'parameters' => array(intval($_id)),
                                                                'logs'       => array('success' => sprintf('Customer id %s could not be copied ',$this->filters['customer'])))
                                            );
                    
                            $this->SoapEngine->execute($function,$this->html);
                            return false;
                        }
                    }
                }

                // Reset filters to find the copy
                $this->filters=array();
                $this->filters['username']=$customer_new->username;

                return true;
            } else {
                if ($this->SoapEngine->error_fault->detail->exception->errorcode != "5001") {
                    return false;
                }
            }

            $j++;
        }
    }

    function showAddForm($confirmPassword=false) {

        print "<h3>Register new customer</h3>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <p>
        <input type=submit name=action value=Add>
        <p>
        ";

        if ($_REQUEST['notify']) $checked_notify='checked';
        printf ("Send notification by email <input type=checkbox name=notify value='1' %s>",$checked_notify);
        print "
        <p>
        <input type=hidden name=showAddForm value=1>
        <table border=0>
        ";

        if ($this->adminonly && $this->filters['reseller']) {
            printf ("<tr><td class=border>Reseller</td>
            <td class=border>%s</td></tr>",$this->filters['reseller']);

            printf ("<input type=hidden name=reseller_filter value='%s'",$this->filters['reseller']);

        } else if ($this->reseller) {
            printf ("<tr><td class=border>Reseller</td>
            <td class=border>%s</td></tr>",$this->reseller);
        }

        foreach (array_keys($this->addFields) as $item) {
            if ($this->addFields[$item]['name']) {
                $item_name=$this->addFields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            $item_form=$item.'_form';

            if ($item=='timezone') {
                $_value=$_REQUEST['timezone_form'];
                if (!$_value) $_value='Europe/Amsterdam';

                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>";

                $this->showTimezones($_value);

                print "</td>
                </tr>
                ";
            } else if ($item=='state') {
                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>
                <select name=state_form>";

                $selected_state[$_REQUEST[$item_form]]='selected';

                foreach ($this->states as $_state) {
                    printf ("<option value='%s' %s>%s",$_state['value'],$selected_state[$_state['value']],$_state['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";
            } else if ($item=='country') {
                printf ("<tr>
                <td class=border valign=top>%s</td>",
                $item_name
                );
                print "<td class=border>
                <select name=country_form>";

                if (!$_REQUEST[$item_form]) {
                    $_value='NL';
                } else {
                    $_value=$_REQUEST[$item_form];
                }

                $selected_country[$_value]='selected';

                foreach ($this->countries as $_country) {
                    printf ("<option value='%s' %s>%s",$_country['value'],$selected_country[$_country['value']],$_country['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";

            } else {
                if ($this->addFields[$item]['type'] == 'textarea') {
                    printf ("
                    <tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $_REQUEST[$item_form]
                    );
                } elseif ($this->addFields[$item]['type'] == 'boolean') {
                    $_var='select_'.$item;
                    ${$_var}[$_REQUEST[$item_form]]='selected';

                    printf ("
                    <tr>
                    <td class=border valign=top>%s</td>
                    <td class=border>
                    <select name=%s_form>
                    <option value='0' %s>False
                    <option value='1' %s>True
                    </select>
                    </td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    ${$_var}[0],
                    ${$_var}[1]
                    );
                } else {
                    $type='text';
                    if (strstr($item,'password')) $type='password';

                    printf ("
                    <tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=%s value='%s'></td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $type,
                    $_REQUEST[$item_form]
                    );

                    if ($item=='password' && $confirmPassword) {
                        printf ("
                        <tr>
                        <td class=border valign=top><font color=red>Confirm password</font></td>
                        <td class=border><input name=confirm_password_form size=30 type=password value='%s'></td>
                        </tr>
                        ",
                        $_REQUEST[confirm_password_form]
                        );
                    }
                }
            }
        }

        $this->printHiddenFormElements();
        print "</form>";
        print "
        </table>
        ";
    }

    function addRecord($dictionary=array(),$confirmPassword=false) {

        if (!$this->checkRecord($dictionary)) {
            return false;
        }

        foreach (array_keys($this->addFields) as $item) {

            if ($dictionary[$item]) {
                $customer[$item] = trim($dictionary[$item]);
            } else {
                $item_form       = $item.'_form';
                $customer[$item] = trim($_REQUEST[$item_form]);
            }
        }

        if (!strlen($customer['username'])) $customer['username'] = trim($customer['firstName']).'.'.trim($customer['lastName'].$this->RandomNumber(5));
        if (!strlen($customer['state']))    $customer['state']    = 'N/A';
        if (!strlen($customer['country']))  $customer['country']  = 'N/A';
        if (!strlen($customer['city']))     $customer['city']     = 'Unknown';
        if (!strlen($customer['address']))  $customer['address']  = 'Unknown';
        if (!strlen($customer['postcode'])) $customer['postcode'] = 'Unknown';
        if (!strlen($customer['timezone'])) $customer['timezone'] = 'Europe/Amsterdam';

        $customer['username'] = strtolower(preg_replace ("/\s+/",".",trim($customer['username'])));
        $customer['username'] = preg_replace ("/\.{2,}/",".",$customer['username']);

        if ($customer['state'] != 'N/A') {
            $_state=$customer['state'].' ';
        } else {
            $_state='';
        }

        if (!strlen($customer['tel'])){
            $customer['tel'] = '+19999999999';
        } else {
            $customer['tel'] = preg_replace("/[^0-9\+]/","",$customer['tel']);
            if (preg_match("/^00(\d{1,20})$/",$customer['tel'],$m)) {
                $customer['tel'] = "+".$m[1];
            }
        }

        $customer['billingEmail']   = $customer['email'];
        $customer['billingAddress'] = $customer['address']."\n".
                                      $customer['postcode']." ".$customer['city']."\n".
                                      $_state.$customer['country']."\n";

        if ($confirmPassword) {
            if (!strlen($customer['password'])) {
                $this->errorMessage='Password cannot be empty';
                return false;
            } else if ($customer['password'] != $_REQUEST['confirm_password_form']) {
                $this->errorMessage='Password is not confirmed';
                return false;
            }
        }

        if (!strlen($customer['password'])) $customer['password'] = $this->RandomString(6);

        if (is_array($dictionary['properties'])) {
            $customer['properties']=$dictionary['properties'];
        } else {
            $customer['properties']=array();
        }

        $function=array('commit'   => array('name'       => 'addAccount',
                                            'parameters' => array($customer),
                                            'logs'       => array('success' => sprintf('Account for %s %s has been added',$customer['firstName'],$customer['lastName']))
                                            )
                       );

        if ($result = $this->SoapEngine->execute($function,$this->html)) {
            // update remote
            if (is_object($this->SoapEngineRemote)) {
                if ($_id = $this->getCustomerId($customer['username'])) {
                    $customerRemote=$customer;
                    $customerRemote['id'] = intval($_id);

                    $this->SoapEngineRemote->addHeader($this->SoapAuthRemote);
                    $result     = $this->SoapEngineRemote->addAccount($customerRemote);

                    if (PEAR::isError($result)) {
                        $error_msg  = $result->getMessage();
                        $error_fault= $result->getFault();
                        $error_code = $result->getCode();
                        printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SOAPurlRemote,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);

                        $function=array('commit'   => array('name'       => 'deleteAccount',
                                                            'parameters' => array(intval($_id)),
                                                            'logs'       => array('success' => sprintf('Customer id %s could not be created ',$this->filters['customer'])))
                                        );
                
                        $this->SoapEngine->execute($function,$this->html);
                        return false;
                    }
                }
            }

            // We have succesfully added customer entry
            $this->showAddForm=false;

            if ($dictionary['notify'] || $_REQUEST['notify']) $this->notify($customer);

            return $result;

        } else {
            return false;
        }
    }

    function notify($customer) {
        /*
        must be supplied with an array:
        $customer=array('firstName' => ''
                        'lastName'  => '',
                        'email'     => '',
                        'username'  => '',
                        'password'  => ''
                        );
        */

        if ($this->support_web) {
            $url=$this->support_web;

        } else {
            if ($_SERVER['HTTPS']=="on") {
                $protocolURL="https://";
            } else {
                $protocolURL="http://";
            }
    
            $url=sprintf("%s%s",$protocolURL,$_SERVER['HTTP_HOST']);
        }

        $body=
        sprintf("Dear %s,\n\n",$customer['firstName']).
        sprintf("This e-mail message is for your record. You have registered a login account at %s as follows:\n\n",$url).
        sprintf("Username: %s\n",$customer['username']).
        sprintf("Password: %s\n",$customer['password']).
        "\n".

        sprintf("The registration has been performed from the IP address %s.",$_SERVER['REMOTE_ADDR']).
        "\n".
        "\n".

        sprintf("This message was sent in clear text over the Internet and it is advisable, in order to protect your account, to login and change your password displayed in this message. ").
        "\n".

        "\n".
        "This is an automatic message, do not reply.\n";

        $from    = sprintf("From: %s",$this->support_email);
        $subject = sprintf("Your account at %s",$url);

        return mail($customer['email'], $subject, $body, $from);
    }

    function getRecordKeys() {
        // Filter
        $filter=array('username'       => $this->filters['username'],
                      'firstName'      => $this->filters['firstName'],
                      'lastName'       => $this->filters['lastName'],
                      'organization'   => $this->filters['organization'],
                      'tel'            => $this->filters['tel'],
                      'email'          => $this->filters['email'],
                      'web'            => $this->filters['web'],
                      'city'           => $this->filters['city'],
                      'country'        => $this->filters['country'],
                      'only_resellers' => $this->filters['only_resellers'],
                      'customer'       => intval($this->filters['customer']),
                      'reseller'       => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );

        // Order
        $orderBy = array('attribute' => 'customer',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                    );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        if ($this->adminonly && $this->filters['only_resellers']) {
            $result     = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $result     = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result->accounts as $customer) {
                $this->selectionKeys[]=$customer->id;
            }
        }
    }

    function getProperty($customer,$name) {
        foreach ($customer->properties as $_property) {
            if ($_property->name == $name) {
                return $_property->value;
            }
        }
        return false;
    }

    function getCustomerId($username) {
        if (!strlen($username)) return false;
        $filter  = array('username' => $username);
        $range   = array('start' => 0,'count' => 1);
        $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
        $Query=array('filter'     => $filter,'orderBy' => $orderBy,'range'   => $range);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if (count($result->accounts) == 1) {
                return $result->accounts[0]->id;
            } else {
                return false;
            }
        }
    }

    function getCustomer($username) {
        if (!strlen($username)) {
            return false;
        }
        $filter  = array('username' => $username);
        $range   = array('start' => 0,'count' => 1);
        $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
        $Query=array('filter'     => $filter,'orderBy' => $orderBy,'range'   => $range);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if (count($result->accounts) == 1) {
                return $result->accounts[0];
            } else {
                return false;
            }
        }
    }

    function setInitialCredits($credits=array()) {

		$properties=array();

        foreach (array_keys($credits) as $item) {
            if ($this->allProperties[$item]['category'] != 'credit') continue;
            $properties[] = array('name'       => $item,
                                  'value'      => "$credits[$item]",
                                  'category'   => $this->allProperties[$item]['category'],
                                  'permission' => $this->allProperties[$item]['permission']
                                  );
        }

        return $properties;
    }

    function showVcard($vcardDictionary) {
        #http://www.stab.nu/vcard/
        # This file will return an vCard Version 3.0 Compliant file to the user. Observe that you should set up     #
        # your web-server with the correct MIME-type. The reason to use the \r\n as breakes is because it should be #
        # more compatible with MS Outlook. All other, better coded, clients sholdnt have any problems with this.    #
        #                                                                                                           #
        # Version 1.0 (2003-08-29)                                                                                  #
        #                                                                                                           #
        # Author: Alf Lovbo <affe@stab.nu>                                                                          #
        #                                                                                                           #
        # This document is released under the GNU General Public License.                                           #
        #                                                                                                           #
        #############################################################################################################
        #                                                                                                           #
        # USAGE                                                                                                     #
        # -----                                                                                                     #
        # The following variables can be used togheter with this document for accessing the functions supplied. All #
        # of the functions listed below takes an value described by the comment after the |-symbol.                 #
        #                                                                                                           #
        # $vcard_birtda | Birthday YYYY-MM-DD                $vcard_f_name | Family name                            #
        # $vcard_cellul | Cellular Phone Number              $vcard_compan | Company Name                           #
        # $vcard_h_addr | Street Address (home)              $vcard_h_city | City (home)                            #
        # $vcard_h_coun | Country (home)                     $vcard_h_fax  | Fax (home)                             #
        # $vcard_h_mail | E-mail (home)                      $vcard_h_phon | Phone (home)                           #
        # $vcard_h_zip  | Zip-code (home)                    $vcard_nickna | Nickname                               #
        # $vcard_note   | Note                               $vcard_s_name | Given name                             #
        # $vcard_uri    | Homepage, URL                      $vcard_w_addr | Street Address (work)                  #
        # $vcard_w_city | City (work)                        $vcard_w_coun | Country (work)                         #
        # $vcard_w_fax  | Fax (work)                         $vcard_w_mail | E-mail (work)                          #
        # $vcard_w_phon | Phone (work)                       $vcard_w_role | Function (work)                        #
        # $vcard_w_titl | Title (work)                       $vcard_w_zip  | Zip-code (work)                        #
        #                                                                                                           #
        #############################################################################################################
        # You dont need to change anything below this comment.                                                      #
        #############################################################################################################

        /*
        $vcardDictionary=array(
                               "vcard_nickna"	=> $this->username,
                               "vcard_f_name"	=> $this->lastname,
                               "vcard_s_name"	=> $this->firstname,
                               "vcard_compan"	=> $this->organization,
                               "vcard_w_addr"	=> $this->address,
                               "vcard_w_zip"	=> $this->postcode,
                               "vcard_w_city"	=> $this->city,
                               "vcard_w_state"	=> $this->county,
                               "vcard_w_coun"	=> $this->country,
                               "vcard_w_mail"	=> $this->email,
                               "vcard_w_phon"	=> $this->tel,
                               "vcard_w_fax"	=> $this->fax,
                               "vcard_enum"	    => $this->enum,
                               "vcard_sip"	    => $this->sip,
                               "vcard_uri"		=> $this->web,
                               "vcard_cellul"	=> $this->mobile
                               );
        */

        foreach (array_keys($vcardDictionary) as $field) {
            $value=$vcardDictionary[$field];
            ${$field}=$value;
        }
    
        if ($vcard_w_state=="N/A") $vcard_w_state=" ";
        $vcard_w_addr = preg_replace("/[\n|\r]/"," ",$vcard_w_addr);
    
        $vcard_sortst = $vcard_f_name;
    
        $vcard_tz = date("O");
        $vcard_rev = date("Y-m-d");
    
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "CLASS:PUBLIC\r\n";
        $vcard .= "PRODID:-//PHP vCard Class//NONSGML Version 1//SE\r\n";
        $vcard .= "REV:" . $vcard_rev . "\r\n";
        $vcard .= "TZ:" . $vcard_tz . "\r\n";
        if ($vcard_f_name != ""){
            if ($vcard_s_name != ""){
                $vcard .= "FN:" . $vcard_s_name . " " . $vcard_f_name . "\r\n";
                $vcard .= "N:" . $vcard_f_name . ";" . $vcard_s_name . "\r\n";
            }
            else {
                $vcard .= "FN:" . $vcard_f_name . "\r\n";
                $vcard .= "N:" . $vcard_f_name . "\r\n";
            }
        }
        elseif ($vcard_s_name != ""){
            $vcard .= "FN:" . $vcard_s_name . "\r\n";
            $vcard .= "N:" . $vcard_s_name . "\r\n";
        }
        if ($vcard_nickna != ""){
            $vcard .= "NICKNAME:" . $vcard_nickna . "\r\n";
        }
        if ($vcard_compan != ""){
            $vcard .= "ORG:" . $vcard_compan . "\r\n";
            $vcard .= "SORTSTRING:" . $vcard_compan . "\r\n";
        }
        elseif ($vcard_f_name != ""){
            $vcard .= "SORTSTRING:" . $vcard_f_name . "\r\n";
        }
        if ($vcard_birtda != ""){
            $vcard .= "BDAY:" . $vcard_birtda . "\r\n";
        }
        if ($vcard_w_role != ""){
            $vcard .= "ROLE:" . $vcard_w_role . "\r\n";
        }
        if ($vcard_w_titl != ""){
            $vcard .= "TITLE:" . $vcard_w_titl . "\r\n";
        }
        if ($vcard_note != ""){
            $vcard .= "NOTE:" . $vcard_note . "\r\n";
        }
        if ($vcard_w_mail != ""){
            $item++;
            $vcard .= "item$item.EMAIL;TYPE=INTERNET;type=PREF:" . $vcard_w_mail . "\r\n";
            $vcard .= "item$item.X-ABLabel:email" . "\r\n";
        }
        if ($vcard_cellul != ""){
            $vcard .= "TEL;TYPE=VOICE,CELL:" . $vcard_cellul . "\r\n";
        }
        if ($vcard_enum != ""){
            $item++;
            $vcard .= "item$item.TEL:" . $vcard_enum . "\r\n";
            $vcard .= "item$item.X-ABLabel:ENUM" . "\r\n";
        }
        if ($vcard_sip != ""){
            $item++;
            $vcard .= "item$item.TEL;TYPE=INTERNET:" . $vcard_sip . "\r\n";
            $vcard .= "item$item.X-ABLabel:SIP" . "\r\n";
        }
        if ($vcard_w_fax != ""){
            $vcard .= "TEL;TYPE=FAX,WORK:" . $vcard_w_fax . "\r\n";
        }
        if ($vcard_w_phon != ""){          
                    $vcard .= "TEL;TYPE=VOICE,WORK:" . $vcard_w_phon . "\r\n";         
            }
        if ($vcard_uri != ""){
            $vcard .= "URL:" . $vcard_uri . "\r\n";
        }
        if ($vcard_addr != ""){
            $vcard .= "ADR;TYPE=HOME,POSTAL,PARCEL:" . $vcard_addr . "\r\n";
        }
        if ($vcard_labl != ""){
            $vcard .= "LABEL;TYPE=DOM,HOME,POSTAL,PARCEL:" . $vcard_labl . "\r\n";
        }
        $vcard_addr = "";
        $vcard_labl = "";
        if ($vcard_w_addr != ""){
                    $vcard_addr = ";;" . $vcard_w_addr;
                    $vcard_labl = $vcard_w_addr;
            }
        if ($vcard_w_city != ""){
                    if ($vcard_addr != ""){
                            $vcard_addr .= ";" . $vcard_w_city;  
                    }
                    else{ 
                            $vcard_addr .= ";;;" . $vcard_w_city;
                    }
                    if ($vcard_labl != ""){
                            $vcard_labl .= "\\r\\n" . $vcard_w_city;
                    }
                    else {
                            $vcard_labl = $vcard_w_city;
                    }
            }
        if ($vcard_w_state != ""){
                    if ($vcard_addr != ""){
                            $vcard_addr .= ";" . $vcard_w_state;
                    }
                    else{ 
                            $vcard_addr .= ";;;" . $vcard_w_state;
                    }
                    if ($vcard_labl != ""){
                            $vcard_labl .= "\\r\\n" . $vcard_w_state;
                    }
                    else {
                            $vcard_labl = $vcard_w_state;
                    }
            }
        if ($vcard_w_zip != ""){
                    if ($vcard_addr != ""){
                            $vcard_addr .= ";" . $vcard_w_zip;
                    }
                    else{
                            $vcard_addr .= ";;;;" . $vcard_w_zip;
                    }
                    if ($vcard_labl != ""){
                            $vcard_labl .= "\\r\\n" . $vcard_w_zip;
                    }
                    else {  
                            $vcard_labl = $vcard_w_zip;
                    }
            }
        if ($vcard_w_coun != ""){
                    if ($vcard_addr != ""){
                            $vcard_addr .= ";" . $vcard_w_coun;
                    }
                    else{
                            $vcard_addr .= ";;;;;" . $vcard_w_coun;
                    }
                    if ($vcard_labl != ""){
                            $vcard_labl .= "\\r\\n" . $vcard_w_coun;
                    }
                    else {
                            $vcard_labl = $vcard_w_coun;
                    }
            }
        if ($vcard_addr != ""){
                    $vcard .= "ADR;TYPE=WORK,POSTAL,PARCEL:" . $vcard_addr . "\r\n";
            }
            if ($vcard_labl != ""){
                    $vcard .= "LABEL;TYPE=DOM,WORK,POSTAL,PARCEL:" . $vcard_labl . "\r\n";
            }
        if ($vcard_categ != ""){
            $vcard .= "CATEGORY:" . $vcard_categ . "\r\n";
        }
    
        $vcard .= "END:VCARD\n";
        return $vcard;
    }
}

class Presence {
    function Presence(&$SoapEngine) {
        $this->SoapEngine         = &$SoapEngine;
    }

    function publishPresence ($soapEngine,$SIPaccount=array(),$note='None',$activity='idle') {

        if (!in_array($soapEngine,array_keys($this->SoapEngine->soapEngines))) {
            print "Error: soapEngine '$soapEngine' does not exist.\n";
            return false;
        }

        if (!$SIPaccount['username'] || !$SIPaccount['domain'] || !$SIPaccount['password'] ) {
            print "Error: SIP account not defined\n";
            return false;
        }

        $this->SOAPurl       = $this->SoapEngine->soapEngines[$soapEngine]['url'];
        $this->PresencePort  = new WebService_SoapSIMPLEProxy_PresencePort($this->SOAPurl);

        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        $allowed_activities=array('open',
                                  'idle',
                                  'busy',
                                  'available'
                                 );
    
        if (in_array($activity,$allowed_activities)) {
            $presentity['activity'] = $activity;
        } else {
            $presentity['activity'] = 'open';
        }
    
        $presentity['note']     = $note;

        $result = $this->PresencePort->setPresenceInformation(array("username" =>$SIPaccount['username'],"domain"   =>$SIPaccount['domain']),$SIPaccount['password'], $presentity);
    
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
    
        return true;
    }

    function getPresenceInformation ($soapEngine,$SIPaccount) {

        if (!in_array($soapEngine,array_keys($this->SoapEngine->soapEngines))) {
            print "Error: soapEngine '$soapEngine' does not exist.\n";
            return false;
        }

        if (!$SIPaccount['username'] || !$SIPaccount['domain'] || !$SIPaccount['password'] ) {
            print "Error: SIP account not defined";
            return false;
        }

        $this->SOAPurl       = $this->SoapEngine->soapEngines[$soapEngine]['url'];
        $this->PresencePort  = new WebService_SoapSIMPLEProxy_PresencePort($this->SOAPurl);

        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        $result = $this->PresencePort->getPresenceInformation(array("username" =>$SIPaccount['username'],"domain"   =>$SIPaccount['domain']),$SIPaccount['password']);
    
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            return $result;
        }
    }
}

class recordGenerator extends SoapEngine {

    //this class generates in bulk enum numbers and sip accounts

    var $template     = array();
    var $allowedPorts = array();
    var $maxRecords   = 500;
    var $minimum_number_length = 4;
    var $maximum_number_length = 15;

    function recordGenerator($generatorId,$record_generators,$soapEngines,$login_credentials=array()) {
        $this->record_generators = $record_generators;
        $this->generatorId       = $generatorId;
        $this->login_credentials = $login_credentials;

        //dprint_r($this->login_credentials);
        $keys = array_keys($this->record_generators);
        if (!$generatorId) $generatorId=$keys[0];

        if (!in_array($generatorId,array_keys($this->record_generators))) {
            return false;
        }

        if (strlen($this->login_credentials['soap_filter'])) {
            $this->soapEngines = $this->getSoapEngineAllowed($soapEngines,$this->login_credentials['soap_filter']);
        } else {
            $this->soapEngines = $soapEngines;
        }

        if (in_array($this->record_generators[$generatorId]['sip_engine'],array_keys($this->soapEngines))) {
            // sip zones
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['sip_engine']]) > 1 && !in_array('sip_accounts',$this->allowedPorts[$this->record_generators[$generatorId]['sip_engine']])) {
                // sip port not available
                dprint("sip port not avaliable");
            } else {
                $sip_engine           = 'sip_accounts@'.$this->record_generators[$generatorId]['sip_engine'];
                $this->SipSoapEngine = new SoapEngine($sip_engine,$soapEngines,$login_credentials);
                $_sip_class          = $this->SipSoapEngine->records_class;
                $this->sipRecords    = new $_sip_class(&$this->SipSoapEngine);

                $this->sipRecords->getAllowedDomains();
            }
        } else {
            printf ("<font color=red>Error: sip_engine %s does not exist</font>",$this->record_generators[$generatorId]['sip_engine']);
        }

        if (in_array($this->record_generators[$generatorId]['enum_engine'],array_keys($this->soapEngines))) {
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['enum_engine']]) > 1 && !in_array('enum_numbers',$this->allowedPorts[$this->record_generators[$generatorId]['enum_engine']])) {
                dprint("enum port not avaliable");
                // enum port not available
            } else {
                // enum mappings
                $enum_engine          = 'enum_numbers@'.$this->record_generators[$generatorId]['enum_engine'];
                $this->EnumSoapEngine = new SoapEngine($enum_engine,$soapEngines,$login_credentials);
                $_enum_class          = $this->EnumSoapEngine->records_class;
                $this->enumRecords    = new $_enum_class(&$this->EnumSoapEngine);
            }

        } else {
            printf ("<font color=red>Error: enum_engine %s does not exist</font>",$this->record_generators[$generatorId]['enum_engine']);
        }

        if (in_array($this->record_generators[$generatorId]['customer_engine'],array_keys($this->soapEngines))) {
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['customer_engine']]) > 1 && !in_array('customers',$this->allowedPorts[$this->record_generators[$generatorId]['customer_engine']])) {
                dprint("customer port not avaliable");
            } else {
                $customer_engine          = 'customers@'.$this->record_generators[$generatorId]['customer_engine'];
                $this->CustomerSoapEngine = new SoapEngine($customer_engine,$soapEngines,$login_credentials);
                $_customer_class          = $this->CustomerSoapEngine->records_class;
                $this->customerRecords    = new $_customer_class(&$this->CustomerSoapEngine);
            }
        } else {
            printf ("<font color=red>Error: customer_engine %s does not exist</font>",$this->record_generators[$generatorId]['customer_engine']);
        }

        if ($_REQUEST['reseller_filter']) $this->template['reseller']=intval($_REQUEST['reseller_filter']);
        if ($_REQUEST['customer_filter']) $this->template['customer']=intval($_REQUEST['customer_filter']);
    }

    function showGeneratorForm() {

        print "
        <form method=post target=_new>
        <table cellspacing=1 cellpadding=1 bgcolor=black>
        <tr>
        <td>
        <table cellspacing=3 cellpadding=4 width=100% bgcolor=#444444>
        <tr>
        <td>
        <font color=white>
        <b>";
        print _("Record generator");
        print "</b>
        </font>
        </td>
        </tr>
        </table>
        </tr>
        <tr>
        <td colspan=100%>

        <table cellpadding=2 bgcolor=white width=100%>
        <tr>
        <td colspan=3>
        </td>
        </tr>
        ";
    
        print "
        <tr>
        <td>";
        print _("ENUM range");
        print "
        <td align=right>";

        /*
        if ($_REQUEST['range']) {
            $selected_range[$_REQUEST['range']]='selected';
        } else if ($_last_range=$this->enumRecords->getCustomerProperty('enum_generator_range')) {
            $selected_range[$_last_range] = 'selected';
        }

        if (is_array($this->enumRecords->ranges)) {
            print "<select name=range>";
            foreach ($this->enumRecords->ranges as $_range) {
                $rangeId=$_range['prefix'].'@'.$_range['tld'];
                printf ("<option value='%s' %s>+%s under %s",$rangeId,$selected_range[$rangeId],$_range['prefix'],$_range['tld']);
            }
            print "</select>";
        }
        */

        list($_range['prefix'],$_range['tld'])=explode("@",$_REQUEST['range']);
        printf ("<input type=hidden name=range value='%s'>+%s under %s",$_REQUEST['range'],$_range['prefix'],$_range['tld']);


        print "<td>
        </tr>
        ";

        print "
        <tr>
        <td colspan=2>
        ";

        print "<b>";
        print _("ENUM mapping template");
        print "</b>";
        print "</td>
        </tr>
        ";

        if ($_REQUEST['add_prefix']) {
            $add_prefix=$_REQUEST['add_prefix'];
        } else {
            $add_prefix = $this->sipRecords->getCustomerProperty('enum_generator_add_prefix');
        }

        print "
        <tr>
        <td>";
        print _("Add prefix after range:");
        printf ("
        <td align=right>
        <input type=text name=add_prefix size=10 maxsize=15 value='%s'>
        </td>
        <td>
        </td>
        </tr>
        ",$add_prefix);

        if ($_REQUEST['number_length']) {
            $number_length=$_REQUEST['number_length'];
        } else {
            $number_length = $this->sipRecords->getCustomerProperty('enum_generator_number_length');
        }

        print "
        <tr>
        <td>";
        print _("Number length:");
        printf ("
        <td align=right>
        <input type=text name=number_length size=10 maxsize=15 value='%s'>
        <tr>
        <td>
        ",$number_length);
    
        print _("SIP domain:");
        print "
        <td align=right>
        ";

        if (count($this->sipRecords->allowedDomains) > 0) {
            if ($_REQUEST['domain']) {
                $selected_domain[$_REQUEST['domain']]='selected';
            } else if ($_last_domain=$this->sipRecords->getCustomerProperty('enum_generator_sip_domain')) {
                $selected_domain[$_last_domain] = 'selected';
            }

            print "
            <select name=domain>
            ";

            foreach ($this->sipRecords->allowedDomains as $domain) {
                printf ("<option value='%s' %s>%s",$domain,$selected_domain[$domain],$domain);
            }

            print "</select>  ";
        } else {
            print "<input type=text size=15 name=domain>";
        }

        print "
        </td>
        <td>";
        print "
        </td>
        </tr>
        ";

        if ($_REQUEST['strip_digits']) {
            $strip_digits=$_REQUEST['strip_digits'];
        } else if ($strip_digits = $this->sipRecords->getCustomerProperty('enum_generator_strip_digits')) {
        } else {
            $strip_digits=0;
        }

        print "
        <tr>
        <td>";
        print _("Strip digits:");
        printf ("
        <td align=right>
        <input type=text size=10 name=strip_digits value='%s'>
        </td>
        </tr>
        ",$strip_digits);
        print "
        <tr>
        <td>";
        print _("Owner:");
        printf ("
        <td align=right><input type=text size=7 name=owner value='%s'>
        <td>",$_REQUEST['owner']);
        print "
        </td>
        </tr>";

        print "
        <tr>
        <td>";
        print _("Info:");
        printf ("
        <td align=right><input type=text size=10 name=info value='%s'>
        <td>",$_REQUEST['info']);
        print "
        </td>
        </tr>";

        if (count($this->sipRecords->allowedDomains) > 0) {
            print "
            <tr>
            <td colspan=3><hr noshade size=1>
            </td>
            </tr>
            ";
    
            print "
            <tr>
            <td colspan=2>
            ";
            print "<b>";
            print _("SIP account template");
            print "</b>";
            print "</td>
            </tr>
            ";

            print "
            <tr>
            <td>";
            print _("Create SIP records");
            if ($_REQUEST['create_sip']) {
                $checked_create_sip='checked';
            } else {
                $checked_create_sip='';
            }
            printf ("
            <td align=right><input type=checkbox name=create_sip value=1 %s>
            </td>
            </tr>
            ",$checked_create_sip);

            if ($_REQUEST['pstn']) {
                $checked_pstn='checked';
            } else {
                $checked_pstn='';
            }

            print "
            <tr>
            <td>";
            print _("PSTN");
            printf ("
            <td align=right><input type=checkbox name=pstn value=1 %s>
            </td>
            </tr>
            ",$checked_pstn);

            if ($_REQUEST['prepaid']) {
                $checked_prepaid='checked';
            } else {
                $checked_prepaid='';
            }

            print "
            <tr>
            <td>";
            print _("Prepaid");
            printf ("
            <td align=right><input type=checkbox name=prepaid value=1 %s>
            </td>
            </tr>
            ",$checked_prepaid);

            if ($_REQUEST['rpid_strip_digits']) {
                $rpid_strip_digits=$_REQUEST['rpid_strip_digits'];
            } else if ($rpid_strip_digits = $this->sipRecords->getCustomerProperty('enum_generator_rpid_strip_digits')) {
            } else {
                $rpid_strip_digits=0;
            }

            print "
            <tr>
            <td>";
            print _("Strip digits from Caller-ID");
            printf ("
            <td align=right><input type=text size=10 name=rpid_strip_digits value='%s'>
            </td>
            </tr>
            ",$rpid_strip_digits);

            print "
            <tr>
            <td>";
            print _("Quota");
            printf ("
            <td align=right><input type=text size=10 name=quota value='%s'>
            </td>
            </tr>
            ",$_REQUEST['quota']);

            print "
            <tr>
            <td>";
            print _("Password");
            printf ("
            <td align=right><input type=text size=10 name=password value='%s'>
            </td>
            </tr>
            ",$_REQUEST['password']);

        }

        if ($_REQUEST['nr_records']) {
            $nr_records=$_REQUEST['nr_records'];
        } else {
            $nr_records=1;
        }

        print "
        <tr>
        <td colspan=3>
        <hr noshade size=1 with=100%>
        </td>
        </tr>
        ";
        print "
        <tr>
        <td>
        ";

        print "<input type=hidden value=Generate>";
        print "<input type=submit value=Generate>";
        printf ("<td align=right>
        Number of records:<input type=text size=10 name=nr_records value='%s'>
        ",$nr_records);
        print "<td>";
        print "
        </tr>
        ";

        print "
        <tr>
        <td colspan=2>
        <br>
        <input type=hidden name=action value=Generate>
        <p>";
        print _("Existing records will not be overwritten. ");
        print "</td>
        </tr>
        ";

        $this->printHiddenFormElements();

        print "
        </table>
        </form>
        </td>
        </tr>
        </table>
        ";
    }


    function checkGenerateRequest() {
        // check number of records
        $this->template['create_sip']=trim($_REQUEST['create_sip']);

        $this->template['rpid_strip_digits']=intval($_REQUEST['rpid_strip_digits']);

        $this->template['info']=trim($_REQUEST['info']);

        $nr_records=trim($_REQUEST['nr_records']);

        if (!is_numeric($nr_records) || $nr_records < 1 || $nr_records > $this->maxRecords) {
            printf ("<font color=red>Error: number of records must be a number between 1 and %d</font>",$this->maxRecords);
            return false;
        }

        $this->template['nr_records'] = $nr_records;

        $number_length=trim($_REQUEST['number_length']);

        if (!is_numeric($number_length) || $number_length < $this->minimum_number_length || $number_length > $this->maximum_number_length) {
            printf ("<font color=red>Error: number length must be a number between 4 and 15</font>",$this->minimum_number_length,$this->maximum_number_length);
            return false;
        }

        $this->template['number_length'] = $number_length;

        $strip_digits=trim($_REQUEST['strip_digits']);
        if (!is_numeric($strip_digits) || $strip_digits < 0 || $number_length < $strip_digits + 3) {
            printf ("<font color=red>Error: strip digits + 3 must be smaller then %d</font>",$number_length);
            return false;
        }

        $this->template['strip_digits'] = $strip_digits;

        // sip domain
        $domain=trim($_REQUEST['domain']);
        if (!strlen($domain)) {
            print "<font color=red>Error: SIP domain is missing</font>";
            return false;
        }
        $this->template['domain'] = $domain;

        $add_prefix=trim($_REQUEST['add_prefix']);
        if (strlen($add_prefix) && !is_numeric($add_prefix)) {
            print "<font color=red>Error: Add prefix must be numeric</font>";
            return false;
        }

        $this->template['add_prefix'] = $add_prefix;

        $owner=trim($_REQUEST['owner']);
        if (strlen($owner) && !is_numeric($owner)) {
            print "<font color=red>Error: Owner must be an integer</font>";
            return false;
        }

        // check ENUM TLD
        list($rangePrefix,$tld)=explode('@',trim($_REQUEST['range']));

        $this->template['range']    = trim($_REQUEST['range']);
        $this->template['rangePrefix'] = $rangePrefix;
        $this->template['tld']      = $tld;
        $this->template['quota']    = intval($_REQUEST['quota']);
        $this->template['owner']    = intval($owner);
        $this->template['pstn']     = intval($_REQUEST['pstn']);
        $this->template['prepaid']  = intval($_REQUEST['prepaid']);
        $this->template['password'] = trim($_REQUEST['password']);

        ///////////////////////////////////////
        // logical checks
        if (strlen($this->template['add_prefix'])) {
            $start = $this->template['add_prefix'];
        } else {
            $start = 0;
        }

        $this->template['digitsAfterRange']  = $this->template['number_length'] - strlen($this->template['rangePrefix']);

        if ($this->template['number_length'] == strlen($this->template['rangePrefix']) + strlen($this->template['add_prefix'])) {
            $this->template['firstNumber'] = $this->template['rangePrefix'].$this->template['add_prefix'];
            $this->template['lastNumber']  = substr($this->template['firstNumber'],0,-1).'9';
            $this->template['maxNumbers']  = $this->template['lastNumber'] - $this->template['firstNumber'] + 1;

        } else {
            $this->template['firstNumber'] = $this->template['rangePrefix'].str_pad($start,$this->template['digitsAfterRange'],'0');
            $this->template['lastNumber']  = sprintf("%.0f", $this->template['firstNumber'] + pow(10,$this->template['digitsAfterRange']-strlen($this->template['add_prefix'])) - 1);
            $this->template['maxNumbers']  = pow(10,$this->template['digitsAfterRange']-strlen($this->template['add_prefix']));
        }

        dprint_r($this->template);

        if ($this->template['maxNumbers'] < $this->template['nr_records']) {
            printf ("<font color=red>Error: Insufficient numbers in range, requested = %d, available = %d</font>",$this->template['nr_records'],$this->template['maxNumbers']);
            return false;
        }

        return true;
    }


    function generateRecords() {

        print "<p>";
        if (!$this->checkGenerateRequest()) {
            return false;
        }
        print "<p>Generating records
        <ol>";

        $_p=array(
                  array('name'       => 'enum_generator_sip_domain',
                        'category'   => 'web',
                        'value'      => strval($this->template['domain']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_range',
                        'category'   => 'web',
                        'value'      => strval($this->template['range']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_strip_digits',
                        'category'   => 'web',
                        'value'      => strval($this->template['strip_digits']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_number_length',
                        'category'   => 'web',
                        'value'      => strval($this->template['number_length']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_add_prefix',
                        'category'   => 'web',
                        'value'      => strval($this->template['add_prefix']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_rpid_strip_digits',
                        'category'   => 'web',
                        'value'      => strval($this->template['rpid_strip_digits']),
                        'permission' => 'customer'
                       )
                  );

        $this->enumRecords->setCustomerProperties($_p);

        if ($this->template['owner']) {
            if ($customer = $this->customerRecords->getRecord($this->template['owner'])) {
                $this->template['email']     = $customer->email;
                $this->template['firstName'] = $customer->firstName;
                $this->template['lastName']  = $customer->lastName;
                if (!strlen($this->template['info'])) {
                    $this->template['info'] = $customer->firstName.' '.$customer->lastName;
                }
            } else {
                printf ("<font color=red>Error: cannot retrieve customer information for owner %d</font>",$this->template['owner']);
            }
        }

        dprint_r($this->template);
        $i=0;

        while ($i < $this->template['nr_records']) {

            $number   = sprintf("%.0f", $this->template['firstNumber'] + $i);
            $username = substr($number,$this->template['strip_digits']);
            $mapto    = 'sip:'.$username.'@'.$this->template['domain'];

            print "<li>";
            printf ('Generating number +%s with mapping %s ',$number,$mapto);
            flush();

            $enumMapping = array('tld'      => $this->template['tld'],
                                 'number'   => $number,
                                 'type'     => 'sip',
                                 'mapto'    => $mapto,
                                 'info'     => $this->template['info'],
                                 'owner'    => $this->template['owner']
                                );

            if ($this->template['create_sip']) {
                if (preg_match("/^0/",$username)) {
                    printf ('SIP accounts starting with 0 are not generated (%s@%s)',$username,$this->template['domain']);
                    continue;
                }

                $groups=array();

                if ($this->template['pstn']) {
                    $groups[]='free-pstn';
                }

                printf ('and sip account %s@%s ',$username,$this->template['domain']);
    
                $sipAccount = array('account'  => $username.'@'.$this->template['domain'],
                                    'quota'    => $this->template['quota'],
                                    'prepaid'  => $this->template['prepaid'],
                                    'password' => $this->template['password'],
                                    'groups'   => $groups,
                                    'owner'    => $this->template['owner']
                                    );

                if ($this->template['firstName']) {
                    $sipAccount['fullname'] = $this->template['firstName'].' '.$this->template['lastName'];
                }

                if ($this->template['email'])     {
                    $sipAccount['email']     = $this->template['email'];
                }


                if ($this->template['pstn']) {

                    $strip_rpid=intval($this->template['rpid_strip_digits']);

                	if ($strip_rpid && strlen($number) > $strip_rpid) {
                    	$sipAccount['rpid']=substr($number,intval($this->template['rpid_strip_digits']));
                    } else {
                		$sipAccount['rpid']=$number;
                    }
                }

            } else {
                unset($sipAccount);
            }

            dprint_r($sipAccount);

            if (is_array($enumMapping)) $this->enumRecords->addRecord($enumMapping);
            if (is_array($sipAccount))  $this->sipRecords->addRecord($sipAccount);

            $i++;
        }

        print "</ol>";
        return true;
    }

    function printHiddenFormElements () {
        printf("<input type=hidden name=generatorId value='%s'>",$this->generatorId);

        if ($this->adminonly) {
            printf("<input type=hidden name=adminonly value='%s'>",$this->adminonly);
        }

        if ($this->template['customer']) {
            printf("<input type=hidden name=customer_filter value='%s'>",$this->template['customer']);
        }

        if ($this->template['reseller']) {
            printf("<input type=hidden name=reseller_filter value='%s'>",$this->template['reseller']);
        }

        foreach (array_keys($this->EnumSoapEngine->extraFormElements) as $element) {
            if (!strlen($this->EnumSoapEngine->extraFormElements[$element])) continue;
            printf ("<input type=hidden name=%s value='%s'>\n",$element,$this->EnumSoapEngine->extraFormElements[$element]);
        }
    }

    function getSoapEngineAllowed($soapEngines,$filter) {

        // filter syntax:
        // $filter="engine1:port1,port2,port3 engine2 engine3";
        // where engine is a connection from ngnpro_engines.inc and
        // port is valid port from that engine like sip_accounts or enum_numbers

        $_filter_els=explode(" ",trim($filter));
        foreach(array_keys($soapEngines) as $_engine) {
            foreach ($_filter_els as $_filter) {
                unset($_allowed_engine);
                $_allowed_ports=array();

                list($_allowed_engine,$_allowed_ports_els) = explode(":",$_filter);

                if ($_allowed_ports_els) {
                    $_allowed_ports = explode(",",$_allowed_ports_els);
                }

                if ($_engine == $_allowed_engine) {
                    $soapEngines_checked[$_engine]=$soapEngines[$_engine];
                    $this->allowedPorts[$_engine]=$_allowed_ports;
                    continue;
                }
            }
        }

        return $soapEngines_checked;
    }

}

class Actions {
    // this class perfom actions on an array of entities returned by selections

    var $actions = array();
    var $version = 1;
    var $sub_action_parameter_size = 35;

    function Actions(&$SoapEngine) {
        $this->SoapEngine = $SoapEngine;
        $this->version    = $this->SoapEngine->version;
        $this->adminonly  = $this->SoapEngine->adminonly;
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
    }

    function showActionsForm($filters,$sorting,$hideParameter=false) {
        if (!count($this->actions)) return;

        print "
        <p>
        <table border=0 class=border width=100%>
        <tr>
        ";

        printf ("<form method=post name=actionform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <td align=left>
        ";

        print "
        <input type=submit value='Perform this action on the selection:'>
        <input type=hidden name=action value=PerformActions>
        ";
        if ($this->adminonly) {
            print "
            <input type=hidden name=adminonly value=$this->adminonly>
            ";
        }


        print "<select name=sub_action>";
        $j=0;
        foreach (array_keys($this->actions) as $_action) {
            $j++;
            printf ("<option value='%s'>%d. %s",$_action,$j,$this->actions[$_action]);
        }
        print "</select>";

        if (!$hideParameter) {
            printf ("
            <input type=text size=%d name=sub_action_parameter>
            ",$this->sub_action_parameter_size);
        }
        print "
        </td>
        <td align=right>
        ";
        print "
        </td>
        ";

        foreach (array_keys($filters) as $_filter) {
            printf ("<input type=hidden name='%s_filter' value='%s'>\n", $_filter,$filters[$_filter]);
        }

        foreach (array_keys($sorting) as $_sorting) {
            printf ("<input type=hidden name='%s' value='%s'>\n", $_sorting,$sorting[$_sorting]);
        }

        printf("<input type=hidden name=service value='%s'>",$this->SoapEngine->service);

        foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
            if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
            printf ("<input type=hidden name=%s value='%s'>\n",$element,$this->SoapEngine->extraFormElements[$element]);
        }
            print "
            </form>
        </tr>
        </table>
        ";

    }
}

class SipAccountsActions extends Actions {
    var $actions=array('block'          => 'Block SIP accounts',
                       'deblock'        => 'Deblock SIP accounts',
                       'enable_pstn'    => 'Enable access to PSTN for the SIP accounts',
                       'disable_pstn'   => 'Disable access to PSTN for the SIP accounts',
                       'deblock_quota'  => 'Deblock SIP accounts blocked by quota',
                       'prepaid'        => 'Make SIP accounts prepaid',
                       'postpaid'       => 'Make SIP accounts postpaid',
                       'delete'         => 'Delete SIP accounts',
                       'setquota'       => 'Set quota of SIP account to:',
                       'rpidasusername' => 'Set PSTN caller ID as the username',
                       'prefixtorpid'   => 'Add to PSTN caller ID this prefix:',
                       'rmdsfromrpid'   => 'Remove from PSTN caller ID digits:',
                       'addtogroup'     => 'Add SIP accounts to group:',
                       'removefromgroup'=> 'Remove SIP accounts from group:',
                       'addbalance'     => 'Add to prepaid balance value:',
                       'changeowner'    => 'Change owner to:',
                       'changefirstname'=> 'Change first name to:',
                       'changelastname' => 'Change last name to:',
                       'changepassword' => 'Change password to:'
                       );

    function SipAccountsActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            print "<li>";

            flush();
            //printf ("Performing action=%s on key=%s",$action,$key);

            $account=array('username' => $key['username'],
                           'domain'   => $key['domain']
                          );

            if ($action=='block') {

                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,'blocked'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been blocked',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='deblock') {

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'blocked'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been de-blocked',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='removefromgroup') {
                if (!strlen($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a group name</font>");
                    break;
                }

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been removed from group %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='addtogroup') {
                if (!strlen($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a group name</font>");
                    break;
                }

                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s is now in group %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='deblock_quota') {

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'quota'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been deblocked from quota',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='disable_pstn') {

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'free-pstn'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has no access to the PSTN',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='enable_pstn') {

                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,'free-pstn'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has access to the PSTN',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='delete') {

                $function=array('commit'   => array('name'       => 'deleteAccount',
                                                    'parameters' => array($account),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been deleted',$key['username'],$key['domain'])
                                                                          )
                                                   )
        
                                );

                $this->SoapEngine->execute($function,$this->html);

            } else if ($action=='prepaid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $result->prepaid=1;

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s is now prepaid',$key['username'],$key['domain'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='postpaid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $result->prepaid=0;

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s is now postpaid',$key['username'],$key['domain'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='setquota') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($sub_action_parameter);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has quota set to %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
                
            } else if ($action=='rmdsfromrpid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties)) $result->properties=array();
                    if (!is_array($result->groups))     $result->groups=array();
                    if (is_numeric($sub_action_parameter) && strlen($result->rpid) > $sub_action_parameter) {
                        printf("%s %s",$result->rpid,$sub_action_parameter);
                        $result->rpid=substr($result->rpid,$sub_action_parameter);
                        printf("%s %s",$result->rpid,$sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric and less than caller if length</font>",$sub_action_parameter);
                        continue;
                    }

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s',$key['username'],$key['domain'],$result->rpid)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='rpidasusername') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties)) $result->properties=array();
                    if (!is_array($result->groups))     $result->groups=array();
                    if (is_numeric($key['username']))   $result->rpid=$key['username'];

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s',$key['username'],$key['domain'],$key['username'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='prefixtorpid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->rpid=$sub_action_parameter.$result->rpid;
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s ',$key['username'],$key['domain'],$result->rpid)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='changecustomer' && $this->version > 1) {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->customer=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has customer set to %s ',$key['username'],$key['domain'],$result->customer)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='changeowner') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->owner=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has owner set to %s ',$key['username'],$key['domain'],$result->owner)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='changefirstname') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->firstName=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s first name has been set to %s ',$key['username'],$key['domain'],$result->firstName)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='changelastname') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->lastName=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s last name has been set to %s ',$key['username'],$key['domain'],$result->lastName)
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='changepassword') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->password=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('Password for SIP account %s@%s has been changed',$key['username'],$key['domain'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);

                }
            } else if ($action=='addbalance') {
                if (!is_numeric($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a positive balance</font>");
                    break;
                }

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if (PEAR::isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                }

                if (!$result->prepaid) {
                    printf ("<font color=red>Info: SIP account %s@%s is not prepaid, no action performed</font>",$key['username'],$key['domain']);
                    continue;
                }

                $function=array('commit'   => array('name'       => 'addBalance',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s balance has been increased with %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )
        
                                );
                $this->SoapEngine->execute($function,$this->html);

            }
        }
        print "</ol>";

    }

}

class SipAliasesActions extends Actions {
    var $actions=array(
                       'delete'         => 'Delete SIP aliases'
                       );

    function SipAliasesActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            print "<li>";
            flush();

            //printf ("Performing action=%s on key=%s",$action,$key);
            $alias=array('username' => $key['username'],
                         'domain'   => $key['domain']
                        );

            if ($action=='delete') {
    
                $function=array('commit'   => array('name'       => 'deleteAlias',
                                                    'parameters' => array($alias),
                                                    'logs'       => array('success' => sprintf('SIP alias %s@%s has been deleted',$key['username'],$key['domain'])
                                                                          )
                                                   )
                                );
                $this->SoapEngine->execute($function,$this->html);
            }
        }
        print "</ol>";

    }

}

class EnumMappingsActions extends Actions {
    var $actions=array(
                       'changettl'      => 'Change TTL to:',
                       'changeowner'    => 'Change owner to:',
                       'changeinfo'     => 'Change info to:',
                       'delete'         => 'Delete ENUM mappings'
                       );

    var $mapping_fields=array('id'       => 'integer',
                              'type'     => 'text',
                              'mapto'    => 'text',
                              'priority' => 'integer',
                              'ttl'      => 'integer'
                              );

    function EnumMappingsActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            flush();
            print "<li>";

            $enum_id=array('number' => $key['number'],
                           'tld'    => $key['tld']
                          );
            if ($action=='delete') {

                //printf ("Performing action=%s on key=%s",$action,$key);
                $function=array('commit'   => array('name'       => 'deleteNumber',
                                                    'parameters' => array($enum_id),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been deleted',$key['number'],$key['tld'])
                                                                          )
                                                   )
        
                                );
    
                $this->SoapEngine->execute($function,$this->html);
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if (PEAR::isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        foreach (array_keys($this->mapping_fields) as $field) {
                            if ($field == 'ttl') {
                                $new_mapping[$field]=intval($sub_action_parameter);
                            } else {
                                if ($this->mapping_fields[$field] == 'integer') {
                                    $new_mapping[$field]=intval($_mapping->$field);
                                } else {
                                    $new_mapping[$field]=$_mapping->$field;
                                }
                            }

                        }

                        $new_mappings[]=$new_mapping;
                    }

                    $number->mappings=$new_mappings;

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s TTL has been set to %d',$key['number'],$key['tld'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeowner') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if (PEAR::isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        $new_mappings[]=$_mapping;
                    }

                    $number->mappings=$new_mappings;

                    if (is_numeric($sub_action_parameter)) {
                        $number->owner=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: Owner '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s owner has been set to  %d',$key['number'],$key['tld'],intval($sub_action_parameter))
                                                                              )
                                                       )
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeinfo') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if (PEAR::isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        $new_mappings[]=$_mapping;
                    }

                    $number->mappings=$new_mappings;

                    $number->info=trim($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s info has been set to %s',$key['number'],$key['tld'],trim($sub_action_parameter))
                                                                              )
                                                       )
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            }
        }

        print "</ol>";
    }
}

class DnsRecordsActions extends Actions {
	var $sub_action_parameter_size = 50;

    var $actions=array(
                       'changettl'      => 'Change TTL to:',
                       'changepriority' => 'Change Priority to:',
                       'changevalue'    => 'Change value to:',
                       'delete'         => 'Delete records'
                       );

    function DnsRecordsActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            flush();
            print "<li>";
            //printf ("Performing action=%s on key=%s",$action,$key['id']);

            if ($action=='delete') {

                $function=array('commit'   => array('name'       => 'deleteRecord',
                                                    'parameters' => array(intval($key['id'])),
                                                    'logs'       => array('success' => sprintf('Record %d has been deleted',$key['id'])
                                                                          )
                                                   )
        
                                );
    
                $this->SoapEngine->execute($function,$this->html);
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if (PEAR::isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $record->ttl=intval($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('TTL for record %d has been set to %d',$key['id'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changepriority') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if (PEAR::isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    if (is_numeric($sub_action_parameter)) {
                        $record->priority=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: Priority '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('Priority for record %d has been set to %d',$key['id'],intval($sub_action_parameter))
                                                                              )
                                                       )
                                    );

                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changevalue') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if (PEAR::isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                	$record->value=$sub_action_parameter;

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('Value of record %d has been set to %s',$key['id'],$sub_action_parameter)
                                                                              )
                                                       )
                                    );

                    $this->SoapEngine->execute($function,$this->html);
                }
            }
        }

        print "</ol>";
    }
}

class DnsZonesActions extends Actions {
	var $sub_action_parameter_size = 50;

    var $actions=array(
                       'changettl'      => 'Change TTL to:',
                       'changeexpire'   => 'Change Expire to:',
                       'changeminimum'  => 'Change Minimum to:',
                       'changeretry'    => 'Change Retry to:',
                       'changeinfo'     => 'Change Info to:',
                       'addnsrecord'    => 'Add name server:',
                       'removensrecord' => 'Remove name server:',
                       'delete'         => 'Delete zones'
                       );

    function DnsZonesActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            flush();
            print "<li>";

            if ($action=='delete') {

                $function=array('commit'   => array('name'       => 'deleteZone',
                                                    'parameters' => $key['name'],
                                                    'logs'       => array('success' => sprintf('Zone %s has been deleted',$key['name'])
                                                                          )
                                                   )
        
                                );
    
                $this->SoapEngine->execute($function,$this->html);
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->ttl=intval($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('TTL for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeexpire') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Expire '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->expire=intval($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Expire for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeminimum') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Minimum '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->minimum=intval($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Minimum for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'addnsrecord') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

					$zone->nameservers[]=$sub_action_parameter;
					$zone->nameservers=array_unique($zone->nameservers);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Added NS record %s for zone %s',$sub_action_parameter,$key['name'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'removensrecord') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
					$new_servers=array();
					foreach ($zone->nameservers as $_ns) {
                        if ($_ns == $sub_action_parameter) continue;
                        $new_servers[]=$_ns;
                    }

					$zone->nameservers=array_unique($new_servers);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('NS record %s removed from zone %s',$sub_action_parameter,$key['name'])
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeretry') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Retry '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->retry=intval($sub_action_parameter);

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Retry for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )
            
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeinfo') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if (PEAR::isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                	$zone->info=$sub_action_parameter;

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Info for zone %s has been set to %s',$key['name'],$sub_action_parameter)
                                                                              )
                                                       )
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            }
        }

        print "</ol>";
    }
}

class CustomersActions extends Actions {
    var $actions=array(
                       'delete'         => 'Delete customers'
                       );

    function CustomerActions(&$SoapEngine) {
        $this->Actions(&$SoapEngine);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {

        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";

        foreach($selectionKeys as $key) {
            flush();
            print "<li>";

            if ($action=='delete') {
                $function=array('commit'  => array('name'       => 'deleteAccount',
                                                    'parameters' => intval($key),
                                                   'logs'       => array('success' => sprintf('Customer id %s has been deleted',$key)))
                                );

                $this->SoapEngine->execute($function,$this->html);
            }
        }

        print "</ol>";
    }
}

?>
