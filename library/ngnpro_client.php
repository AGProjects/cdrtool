<?php
require_once 'ngnpro_soap_library.php';

/*
    Copyright (c) 2007-2022 AG Projects
    https://ag-projects.com
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
    $this->SipSoapEngine = new SoapEngine($sip_engine, $soapEngines, $login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class($this->SipSoapEngine);

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
    $this->SipSoapEngine = new SoapEngine($sip_engine, $soapEngines, $login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class($this->SipSoapEngine);

    $sipDomain = array('domain'  => 'example.com',
                       'customer' => $customer,
                       'reseller' => $reseller
                      );

    $this->sipRecords->addRecord($sipDomain);

    ///////////////////////////////
    // How to create a SIP alias //
    ///////////////////////////////

    $sip_engine          = 'sip_aliases@engine';
    $this->SipSoapEngine = new SoapEngine($sip_engine, $soapEngines, $login_credentials);
    $_sip_class          = $this->SipSoapEngine->records_class;
    $this->sipRecords    = new $_sip_class($this->SipSoapEngine);

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
    $this->EnumSoapEngine = new SoapEngine($enum_engine, $soapEngines, $login_credentials);
    $_enum_class          = $this->EnumSoapEngine->records_class;
    $this->enumRecords    = new $_enum_class($this->EnumSoapEngine);

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

class SoapEngine
{
    public $version       = 1;
    public $adminonly     = 0;
    public $customer      = 0;
    public $reseller      = 0;
    public $login_type    = 'reseller';
    public $allowedPorts  = array();
    public $timeout       = 5;
    public $exception     = array();
    public $result        = false;
    public $extraFormElements  = array();
    public $default_enum_tld   =  'e164.arpa';
    public $default_timezone   =  'Europe/Amsterdam';
    public $default_sip_proxy  = "";
    public $default_msrp_relay = "";

    public $ports = array(
        'sip_accounts' => array(
            'records_class' => 'SipAccounts',
            'name'          => 'SIP accounts',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'sip',
            'description'   => 'Manage SIP accounts and their settings. Click on the SIP account to access the settings page. Use _ or % to match one or more characters. ',
        ),
        'customers' => array(
            'records_class' => 'Customers',
            'name'          => 'Owner Accounts',
            'soap_class'    => 'WebService_NGNPro_CustomerPort',
            'category'      => 'general',
            'description'   => 'Manage accounts with address information and other properties. SIP domains and ENUM ranges can be assigned to accounts. Use _ or % to match one or more characters. '
        ),
        'sip_domains' => array(
            'records_class' => 'SipDomains',
            'name'          => 'SIP domains',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'sip',
            'description'   => 'Manage SIP domains (e.g example.com) served by the SIP Proxy. Use _ or % to match one or more characters. '
        ),
        'trusted_peers' => array(
            'records_class' => 'TrustedPeers',
            'name'          => 'Trusted peers',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'sip',
            'description'   => 'Manage trusted parties that are allowed to route sessions through the SIP proxy without digest authentication. ',
            'resellers_only'=> true
        ),
        'enum_numbers' => array(
            'records_class' => 'EnumMappings',
            'name'          => 'ENUM numbers',
            'soap_class'    => 'WebService_NGNPro_EnumPort',
            'category'      => 'dns',
            'description'   => 'Manage E164 numbers used for incoming calls and their mappings (e.g. +31123456789 map to sip:user@example.com). Use _ or % to match one or more characters. '
        ),
        'enum_ranges' => array(
            'records_class' => 'EnumRanges',
            'name'          => 'ENUM ranges',
            'soap_class'    => 'WebService_NGNPro_EnumPort',
            'category'      => 'dns',
            'description'   => 'Manage E164 number ranges that hold individual phone numbers. Use _ or % to match one or more characters. '
        ),
        'dns_zones' => array(
            'records_class' => 'DnsZones',
            'name'          => 'DNS zones',
            'soap_class'    => 'WebService_NGNPro_DnsPort',
            'category'      => 'dns',
            'description'   => 'Manage DNS zones. Use _ or % to match one or more characters. '
        ),
        'dns_records' => array(
            'records_class' => 'DnsRecords',
            'name'          => 'DNS records',
            'soap_class'    => 'WebService_NGNPro_DnsPort',
            'category'      => 'dns',
            'description'   => 'Manage DNS records. Use _ or % to match one or more characters. '
        ),
        'pstn_carriers' => array(
            'records_class' => 'Carriers',
            'name'          => 'PSTN carriers',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'pstn',
            'description'   => 'Manage outbound carriers for PSTN traffic. Click on Carier to edit its attributes. ',
            'resellers_only'=> true
        ),
        'pstn_gateways' => array(
            'records_class' => 'Gateways',
            'name'          => 'PSTN gateways',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'pstn',
            'description'   => 'Manage outbound PSTN gateways. Click on Gateway to edit its attributes. ',
            'resellers_only'=> true
        ),
        'pstn_routes' => array(
            'records_class' => 'Routes',
            'name'          => 'PSTN routes',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'pstn',
            'description'   => 'Manage outbound PSTN routes. A prefix must be formated as 00+E164, an empty prefix matches all routes. ',
            'resellers_only'=> true
        ),
        'gateway_rules' => array(
            'records_class' => 'GatewayRules',
            'name'          => 'PSTN rules',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'pstn',
            'description'   => 'Manage translation rules for PSTN gateways. Rules are applied against 00+E164 prefix. Click on Rule to edit its attributes. ',
            'resellers_only'=> true
        ),
        'email_aliases' => array(
            'records_class' => 'EmailAliases',
            'name'          => 'Email aliases',
            'soap_class'    => 'WebService_NGNPro_DnsPort',
            'category'      => 'dns',
            'description'   => 'Manage email aliases. Use _ or % to match one or more characters. '
        ),
        'url_redirect' => array(
            'records_class' => 'UrlRedirect',
            'name'          => 'URL redirect',
            'soap_class'    => 'WebService_NGNPro_DnsPort',
            'category'      => 'dns',
            'description'   => 'Manage WEB URL redirections. Use _ or % to match one or more characters. '
        ),
        'sip_aliases' => array(
            'records_class' => 'SipAliases',
            'name'          => 'SIP aliases',
            'soap_class'    => 'WebService_NGNPro_SipPort',
            'category'      => 'sip',
            'description'   => 'Manage redirections for SIP addresses e.g. redirect user1@example1.com (alias) to user2@example2.com (target). Use _ or % to match one or more characters. '
        )
    );

    /**
     * service is port@engine where:
     *
     * - port is an available NGNPro service
     * - engine is a connection to an NGNPro server
     *
     * - soapEngines is an array of NGNPro connections and
     * settings belonging to them:
     *
     * $soapEngines = array(
     *     'mdns' => array(
     *         'name'        => 'Managed DNS',
     *         'username'    => 'soapadmin',
     *         'password'    => 'passwd',
     *         'url'         => 'http://example.com:9200/'
     *     )
     * );
     */
    public function __construct($service, $soapEngines, $login_credentials = array())
    {
        $this->login_credentials = &$login_credentials;

        if (is_array($this->login_credentials['ports'])) {
            $_ports = array();
            foreach (array_keys($this->ports) as $_key) {
                if (in_array($_key, array_keys($this->login_credentials['ports']))) {
                    if (strlen($this->login_credentials['ports'][$_key]['records_class'])) {
                        $_ports[$_key]['records_class'] = $this->login_credentials['ports'][$_key]['records_class'];
                    } else {
                        $_ports[$_key]['records_class'] = $this->ports[$_key]['records_class'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['soap_class'])) {
                        $_ports[$_key]['soap_class'] = $this->login_credentials['ports'][$_key]['soap_class'];
                    } else {
                        $_ports[$_key]['soap_class'] = $this->ports[$_key]['soap_class'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['name'])) {
                        $_ports[$_key]['name'] = $this->login_credentials['ports'][$_key]['name'];
                    } else {
                        $_ports[$_key]['name'] = $this->ports[$_key]['name'];
                    }
                    if (strlen($this->login_credentials['ports'][$_key]['description'])) {
                        $_ports[$_key]['description'] = $this->login_credentials['ports'][$_key]['description'];
                    } else {
                        $_ports[$_key]['description'] = $this->ports[$_key]['description'];
                    }
                } else {
                    $_ports[$_key] = $this->ports[$_key];
                }
            }
            $this->ports = $_ports;
        }

        //dprint_r($this->login_credentials);

        if ($this->login_credentials['login_type'] == 'admin') $this->adminonly = 1;

        if (strlen($this->login_credentials['soap_filter'])) {
            $this->soapEngines = $this->getSoapEngineAllowed($soapEngines, $this->login_credentials['soap_filter']);
        } else {
            $this->soapEngines = $soapEngines;
        }

        if (is_array($this->soapEngines)) {
            $_engines  = array_keys($this->soapEngines);

            if (!$service) {
                // use first engine available
                if (is_array($this->allowedPorts) && count($this->allowedPorts[$_engines[0]]) > 0) {
                    $_ports = $this->allowedPorts[$_engines[0]];
                } else {
                    $_ports = array_keys($this->ports);
                }
                // default service is:
                $service = $_ports[0].'@'.$_engines[0];
            }

            if (is_array($this->login_credentials['extra_form_elements'])) {
                $this->extraFormElements = $this->login_credentials['extra_form_elements'];
            }

            $this->service = $service;

            $_els = explode('@', $this->service);

            if (!$_els[1]) {
                $this->soapEngine = $_engines[0];
            } else {
                $this->soapEngine = $_els[1];
            }

            $this->sip_engine = $this->soapEngine;

            if (strlen($this->soapEngines[$this->soapEngine]['version'])) {
                $this->version = $this->soapEngines[$this->soapEngine]['version'];
            }

            $default_port = 'customers';

            if (isset($this->allowedPorts[$this->soapEngine]) ? count($this->allowedPorts[$this->soapEngine]) : 0 > 0) {
                if (in_array($_els[0], $this->allowedPorts[$this->soapEngine])) {
                    $this->port = $_els[0];
                } else if (in_array($default_port, $this->allowedPorts[$this->soapEngine])) {
                    $this->port = $default_port;
                } else {
                    // disable some version dependent ports
                    foreach (array_keys($this->ports) as $_p) {
                        if (in_array($_p, $this->allowedPorts[$this->soapEngine])) {
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

            $this->service = $this->port.'@'.$this->soapEngine;

            foreach (array_keys($this->soapEngines) as $_key) {
                $this->skip[$_key] = $this->soapEngines[$_key]['skip'];
                if ($this->soapEngines[$_key]['skip_ports']) {
                    $this->skip_ports[$_key] = $this->soapEngines[$_key]['skip_ports'];
                }
            }

            $this->impersonate = intval($this->soapEngines[$this->soapEngine]['impersonate']);

            if ($this->soapEngines[$this->soapEngine]['default_enum_tld']) {
                $this->default_enum_tld = $this->soapEngines[$this->soapEngine]['default_enum_tld'];
            }

            if ($this->soapEngines[$this->soapEngine]['default_timezone']) {
                $this->default_timezone = $this->soapEngines[$this->soapEngine]['default_timezone'];
            }

            if ($this->soapEngines[$this->soapEngine]['sip_proxy']) {
                $this->default_sip_proxy = $this->soapEngines[$this->soapEngine]['sip_proxy'];
            }

            if ($this->soapEngines[$this->soapEngine]['msrp_relay']) {
                $this->default_msrp_relay = $this->soapEngines[$this->soapEngine]['msrp_relay'];
            }

            if ($this->soapEngines[$this->soapEngine]['default_country']) {
                $this->default_country = $this->soapEngines[$this->soapEngine]['default_country'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['sip_engine'])) {
                $this->sip_engine = $this->soapEngines[$this->soapEngine]['sip_engine'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['voicemail_engine'])) {
                $this->voicemail_engine = $this->soapEngines[$this->soapEngine]['voicemail_engine'];
            }

            if (strlen($this->login_credentials['customer_engine'])) {
                $this->customer_engine = $this->login_credentials['customer_engine'];
            } elseif (strlen($this->soapEngines[$this->soapEngine]['customer_engine'])) {
                $this->customer_engine = $this->soapEngines[$this->soapEngine]['customer_engine'];
            } else {
                $this->customer_engine = $this->soapEngine;
            }

            if (strlen($this->soapEngines[$this->soapEngine]['sip_settings_page'])) {
                $this->sip_settings_page = $this->soapEngines[$this->soapEngine]['sip_settings_page'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['call_limit'])) {
                $this->call_limit = $this->soapEngines[$this->soapEngine]['call_limit'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['digest_settings_page'])) {
                $this->digest_settings_page = $this->soapEngines[$this->soapEngine]['digest_settings_page'];
            }

            if (is_array($this->soapEngines[$this->soapEngine]['customer_properties'])) {
                $this->customer_properties = $this->soapEngines[$this->soapEngine]['customer_properties'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['timeout'])) {
                $this->timeout = intval($this->soapEngines[$this->soapEngine]['timeout']);
            }

            if (strlen($this->soapEngines[$this->soapEngine]['store_clear_text_passwords'])) {
                $this->store_clear_text_passwords = $this->soapEngines[$this->soapEngine]['store_clear_text_passwords'];
            }

            if (strlen($this->soapEngines[$this->soapEngine]['allow_none_local_dns_zones'])) {
                $this->allow_none_local_dns_zones = $this->soapEngines[$this->soapEngine]['allow_none_local_dns_zones'];
            }
            if (strlen($this->login_credentials['record_generator'])) {
                $this->record_generator = $this->login_credentials['record_generator'];
            } elseif (strlen($this->soapEngines[$this->soapEngine]['record_generator'])) {
                $this->record_generator = $this->soapEngines[$this->soapEngine]['record_generator'];
            }

            if (strlen($this->login_credentials['name_servers'])) {
                $this->name_servers = $this->login_credentials['name_servers'];
            } elseif (strlen($this->soapEngines[$this->soapEngine]['name_servers'])) {
                $this->name_servers = $this->soapEngines[$this->soapEngine]['name_servers'];
            }

            if (strlen($login_credentials['reseller'])) {
                $this->reseller = $login_credentials['reseller'];
            } elseif ($this->adminonly && $_REQUEST['reseller_filter']) {
                $this->reseller = $_REQUEST['reseller_filter'];
            }

            if (strlen($login_credentials['customer'])) {
                $this->customer = $login_credentials['customer'];
            } elseif ($this->adminonly && $_REQUEST['customer_filter']) {
                $this->customer = $_REQUEST['customer_filter'];
            }

            if (strlen($login_credentials['soap_username'])) {
                $this->soapUsername=$login_credentials['soap_username'];
                $this->SOAPlogin = array(
                    "username"    => $this->soapUsername,
                    "password"    => $login_credentials['soap_password'],
                    "admin"       => false
                );
            } else {
                // use the credentials defined for the soap engine
                $this->soapUsername = $this->soapEngines[$this->soapEngine]['username'];
                if ($this->customer) {
                    $this->SOAPlogin = array(
                        "username"    => $this->soapUsername,
                        "password"    => $this->soapEngines[$this->soapEngine]['password'],
                        "admin"       => true,
                        "impersonate" => intval($this->customer)
                    );
                } else {
                    $this->SOAPlogin = array(
                        "username"    => $this->soapUsername,
                        "password"    => $this->soapEngines[$this->soapEngine]['password'],
                        "admin"       => true,
                        "impersonate" => intval($this->reseller)
                    );
                }

                $this->SOAPloginAdmin = array(
                    "username"    => $this->soapUsername,
                    "password"    => $this->soapEngines[$this->soapEngine]['password'],
                    "admin"       => true
                );
            }

            $this->SOAPurl = $this->soapEngines[$this->soapEngine]['url'];

            $log = sprintf(
                "<p>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",
                $this->soap_class,
                $this->SOAPurl,
                $this->SOAPurl,
                $this->soapUsername
            );
            dprint($log);

            $this->SoapAuth      = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');
            $this->SoapAuthAdmin = array('auth', $this->SOAPloginAdmin , 'urn:AGProjects:NGNPro', 0, '');

            // Instantiate the SOAP client
            if (!class_exists($this->soap_class)) return ;

            $this->soapclient = new $this->soap_class($this->SOAPurl);

            $this->soapclient->setOpt('curl', 'CURLOPT_SSL_VERIFYPEER', 0);
            $this->soapclient->setOpt('curl', 'CURLOPT_SSL_VERIFYHOST', 0);

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

                $this->soapclientCustomers->setOpt('curl', 'CURLOPT_SSL_VERIFYPEER', 0);
                $this->soapclientCustomers->setOpt('curl', 'CURLOPT_SSL_VERIFYHOST', 0);

                if (strlen($this->soapEngines[$this->customer_engine]['timeout'])) {
                    $this->soapclientCustomers->_options['timeout'] = intval($this->soapEngines[$this->customer_engine]['timeout']);
                } else {
                    $this->soapclientCustomers->_options['timeout'] = $this->timeout;
                }
            }

            if ($this->voicemail_engine) {
                $this->SOAPloginVoicemail = array(
                    "username"    => $this->soapEngines[$this->voicemail_engine]['username'],
                    "password"    => $this->soapEngines[$this->voicemail_engine]['password'],
                    "admin"       => true,
                    "impersonate" => intval($this->reseller)
                );

                $this->SoapAuthVoicemail = array('auth', $this->SOAPloginVoicemail , 'urn:AGProjects:NGNPro', 0, '');

                $this->SOAPurlVoicemail    = $this->soapEngines[$this->voicemail_engine]['url'];
                $this->soapclientVoicemail = new WebService_NGNPro_VoicemailPort($this->SOAPurlVoicemail);

                $this->soapclientVoicemail->setOpt('curl', 'CURLOPT_SSL_VERIFYPEER', 0);
                $this->soapclientVoicemail->setOpt('curl', 'CURLOPT_SSL_VERIFYHOST', 0);

                if (strlen($this->soapEngines[$this->voicemail_engine]['timeout'])) {
                    $this->soapclientVoicemail->_options['timeout'] = intval($this->soapEngines[$this->voicemail_engine]['timeout']);
                } else {
                    $this->soapclientVoicemail->_options['timeout'] = $this->timeout;
                }
            }
        } else {
            print "<font color=red>Error: No SOAP credentials defined.</font>";
        }

        $this->url = $_SERVER['PHP_SELF']."?1=1";

        foreach (array_keys($this->extraFormElements) as $element) {
            if (!strlen($this->extraFormElements[$element])) continue;
            $this->url  .= sprintf(
                '&%s=%s',
                $element,
                urlencode($this->extraFormElements[$element])
            );
        }

        $this->support_email   = $this->soapEngines[$this->soapEngine]['support_email'];
        $this->support_web     = $this->soapEngines[$this->soapEngine]['support_web'];
        $this->welcome_message = $this->soapEngines[$this->soapEngine]['welcome_message'];
    }

    /**
     * returns a list of allowed engines based on a filter
     * the filter format is:
     * engine1:port1,port2 engine2 engine3:port1
     */
    public function getSoapEngineAllowed($soapEngines, $filter)
    {
        if (!$filter) {
            $soapEngines_checked = $soapEngines;
        } else {
            $_filter_els = explode(" ", $filter);
            foreach (array_keys($soapEngines) as $_engine) {
                foreach ($_filter_els as $_filter) {
                    unset($_allowed_engine);
                    $_allowed_ports = array();

                    list($_allowed_engine, $_allowed_ports_els) = explode(":", $_filter);

                    if ($_allowed_ports_els) {
                        $_allowed_ports = explode(",", $_allowed_ports_els);
                    }

                    if (count($_allowed_ports) == 0) {
                        $_allowed_ports = array_keys($this->ports);
                    }

                    if ($_engine == $_allowed_engine) {
                        $soapEngines_checked[$_engine] = $soapEngines[$_engine];
                        $this->allowedPorts[$_engine] = $_allowed_ports;
                        continue;
                    }
                }
            }
        }
        return $soapEngines_checked;
    }

    /**
     * $function = array(
     *     'commit'   => array(
     *         'name'       => 'addAccount',
     *         'parameters' => array(
     *             $param1,
     *             $param2
     *          ),
     *          'logs'       => array(
     *              'success' => 'The function was a success',
     *              'failure' => 'The function has failed'
     *          )
     *      )
     *  );
     */
    public function execute($function, $html = true, $adminonly = false)
    {
        if (!$function['commit']['name']) {
            if ($html) {
                print "<font color=red>Error: no function name supplied</font>";
            } else {
                print "Error: no function name supplied\n";
            }
            return false;
        }

        if ($adminonly) {
            $this->soapclient->addHeader($this->SoapAuthAdmin);
        } else {
            $this->soapclient->addHeader($this->SoapAuth);
        }

        $result = call_user_func_array(
            array(
                $this->soapclient,
                $function['commit']['name']
            ),
            $function['commit']['parameters']
        );

        if ((new PEAR)->isError($result)) {
            $this->error_msg   = $result->getMessage();
            $this->error_fault = $result->getFault();
            $this->error_code  = $result->getCode();

            $this->exception   = $this->error_fault->detail->exception;

            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SOAPurl,
                $this->error_msg,
                $this->error_fault->detail->exception->errorcode,
                $this->error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);

            if ($html) {
                $log = sprintf(
                    "SOAP query failed: %s (%s): %s",
                    $this->error_msg,
                    $this->error_fault->detail->exception->errorcode,
                    $this->error_fault->detail->exception->errorstring
                );
                print "<font color=red>$log</font>";
            }
            return false;
        } else {
            $this->result = $result;

            if ($function['commit']['logs']['success']) {
                if ($html) {
                    printf(
                        "<p><font color=green>%s </font>\n",
                        htmlentities($function['commit']['logs']['success'])
                    );
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

class Records
{
    public $maxrowsperpage     = '20';
    public $sip_settings_page  = 'sip_settings.phtml';
    public $allowedDomains     = array();
    public $selectionActive    = false;
    public $selectionKeys      = array();
    public $resellers          = array();
    public $customers          = array();
    public $record_generator   = false;
    public $customer_properties = array();
    public $loginProperties    = array();
    public $errorMessage       = '';
    public $html               = true;
    public $filters            = array();
    public $selectionActiveExceptions    = array();

    public function log_action($action = 'Unknown')
    {
        global $CDRTool;
        $location = "Unknown";
        $_loc = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        if ($_loc['country_name']) {
            $location = $_loc['country_name'];
        }
        $log = sprintf(
            "CDRTool login username=%s, type=%s, impersonate=%s, IP=%s, location=%s, action=%s:%s, script=%s",
            $this->login_credentials['username'],
            $this->login_credentials['login_type'],
            $CDRTool['impersonate'],
            $_SERVER['REMOTE_ADDR'],
            $location,
            $this->SoapEngine->port,
            $action,
            $_SERVER['PHP_SELF']
        );
        syslog(LOG_NOTICE, $log);
    }

    public function soapHasError($result)
    {
        return (new PEAR)->isError($result);
    }


    public function checkLogSoapError($result, $syslog = false, $print = false)
    {
        if (!$this->soapHasError($result)) {
            return false;
        }

        $error_msg  = $result->getMessage();
        $error_fault= $result->getFault();
        $error_code = $result->getCode();

        if ($syslog) {
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);
            if ($print) {
                printf("<p><font color=red>Error: $log</font>");
            }
        } else {
            printf(
                "<font color=red>Error: %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
        }
        return true;
    }

    public function buildUrl($data)
    {
        return sprintf(
            "%s&%s",
            $this->url,
            http_build_query($data)
        );
    }

    public function __construct($SoapEngine)
    {
        $this->SoapEngine          = $SoapEngine;

        $this->version             = $this->SoapEngine->version;
        $this->login_credentials   = $this->SoapEngine->login_credentials;

        $this->sorting['sortBy']    = trim($_REQUEST['sortBy']);
        $this->sorting['sortOrder'] = trim($_REQUEST['sortOrder']);

        $this->next       = $_REQUEST['next'];

        $this->adminonly   = $this->SoapEngine->adminonly;
        $this->reseller    = $this->SoapEngine->reseller;
        $this->customer    = $this->SoapEngine->customer;
        $this->impersonate = $this->SoapEngine->impersonate;
        $this->url         = $this->SoapEngine->url;

        foreach (array_keys($this->filters) as $_filter) {
            if (strlen($this->filters[$_filter]) && !in_array($_filter, $this->selectionActiveExceptions)) {
                $this->selectionActive = true;
                break;
            }
        }

        if ($this->adminonly) {
            $this->url .= sprintf('&adminonly=%s', $this->adminonly);
            if ($this->login_credentials['reseller']) {
                $this->filters['reseller'] = $this->login_credentials['reseller'];
            } else {
                $this->filters['reseller'] = trim($_REQUEST['reseller_filter']);
            }
        }

        $this->filters['customer'] = trim($_REQUEST['customer_filter']);

        //$this->getResellers();

        $this->getCustomers();

        $this->getLoginAccount();

        if (strlen($this->SoapEngine->sip_settings_page)) {
            $this->sip_settings_page = $this->SoapEngine->sip_settings_page;
        }

        if (strlen($this->SoapEngine->digest_settings_page)) {
            $this->digest_settings_page = $this->SoapEngine->digest_settings_page;
        }

        $this->support_email   = $this->SoapEngine->support_email;
        $this->support_web     = $this->SoapEngine->support_web;
    }

    function showEngineSelection()
    {
        $selected_soapEngine[$this->SoapEngine->service] =' selected';

        $pstn_access = $this->getCustomerProperty('pstn_access');

        printf("<select class=span3 name='service' onChange=\"jumpMenu('this.form')\">\n");

	$j = 1;
	foreach (array_keys($this->SoapEngine->soapEngines) as $_engine) {
	    if ($this->SoapEngine->skip[$_engine]) continue;
	    if ($j > 1) printf ("<option value=''>--------\n");
	    foreach (array_keys($this->SoapEngine->ports) as $_port) {
		$idx = $_port.'@'.$_engine;
		if (is_array($this->SoapEngine->skip_ports[$_engine]) && in_array($_port, $this->SoapEngine->skip_ports[$_engine])) continue;

		if ($this->login_credentials['login_type'] !='admin') {
		    if (!$pstn_access && (preg_match("/^pstn_/", $_port))) continue;
		}
		if (isset($this->SoapEngine->allowedPorts[$_engine]) ? count($this->SoapEngine->allowedPorts[$_engine]) : 0 > 0 && !in_array($_port, $this->SoapEngine->allowedPorts[$_engine])) continue;

		// disable some version dependent ports

		if ($_port == 'customers' && $this->SoapEngine->soapEngines[$_engine]['version'] < 2) continue;

		if ($this->SoapEngine->ports[$_port]['resellers_only']) {
		    if ($this->login_credentials['login_type']=='admin' || $this->loginAccount->resellerActive) {
			printf(
			    "<option value=\"%s@%s\"%s>%s@%s\n",
			    $_port,
			    $_engine,
			    $selected_soapEngine[$idx],
			    $this->SoapEngine->ports[$_port]['name'],
			    $this->SoapEngine->soapEngines[$_engine]['name']
			);
		    }
		} else {
		    printf(
			"<option value=\"%s@%s\"%s>%s@%s\n",
			$_port,
			$_engine,
			$selected_soapEngine[$idx],
			$this->SoapEngine->ports[$_port]['name'],
			$this->SoapEngine->soapEngines[$_engine]['name']
		    );
		}
	    }

	    $j++;
	}
        print("</select>");
        printf(
            "<script type=\"text/JavaScript\">
            function jumpMenu() {
                location.href=\"%s&service=\" + document.engines.service.options[document.engines.service.selectedIndex].value;
            }
            </script>",
            $this->url
        );
    }

    function showAfterEngineSelection()
    {
    }

    function showCustomerSelection()
    {
        $this->showCustomerForm();
    }

    function showResellerSelection()
    {
        if ($this->adminonly) {
            $this->showResellerForm();
        } else {
            printf("%s", $this->reseller);
        }
    }

    function showPagination($maxrows)
    {
        $url .= $this->url.'&'.$this->addFiltersToURL().sprintf(
            "&service=%s&sortBy=%s&sortOrder=%s",
            urlencode($this->SoapEngine->service),
            urlencode($this->sorting['sortBy']),
            urlencode($this->sorting['sortOrder'])
        );

        print "
          <ul class='pager'>
        ";

        if ($this->next != 0) {
            $show_next = $this->maxrowsperpage-$this->next;
            if ($show_next < 0) {
                $mod_show_next  =  $show_next-2*$show_next;
            }
            if (!$mod_show_next) $mod_show_next=0;

            printf("<li><a href='%s&next=%s'>&larr;  Previous</a></li>", $url, $mod_show_next);
            if ($mod_show_next/$this->maxrowsperpage >= 1) {
                printf("<li><a href='%s&next=0'>Begin</a></li> ", $url);
            }
        }

        if ($this->next + $this->maxrowsperpage < $this->rows)  {
            $show_next = $this->maxrowsperpage + $this->next;
            printf("<li><a href='%s&next=%s'>Next &rarr; </a></li> ", $url, $show_next);
        }

        print("</ul>");
    }

    function showSeachFormCustom()
    {
    }

    function showSeachForm()
    {
        if ($this->hide_html()) {
            return;
        }

        printf(
            "<p><b>%s</b>",
            $this->SoapEngine->ports[$this->SoapEngine->port]['description']
        );

        printf(
            "<form class=\"form-inline\" method=post name=engines action=%s><div class='well well-small'>",
            $_SERVER['PHP_SELF']
        );
        //print "
        //<td align=left>
        //";
        print("<input class='btn btn-primary' type=submit name=action value=Search>");

        $this->showEngineSelection();
        $this->showAfterEngineSelection();

        print("<div class=pull-right>
          Order by");
        $this->showSortForm();
        print("</div>");
        $this->printHiddenFormElements('skipServiceElement');

        print("<div style=\"clear:both\"><br /></div>");

        print("<div class=input-prepend><span class=add-on>");
        $this->showTextBeforeCustomerSelection();
        print("</span>");

        $this->showCustomerSelection();
        $this->showResellerSelection();
        print("</div>");

        $this->showSeachFormCustom();
        print("</div>
        </form>
        ");

        if ($_REQUEST['action'] != 'Delete') $this->showAddForm();
    }

    function listRecords()
    {
    }

    function getRecordKeys()
    {
    }

    function addRecord($dictionary = array())
    {
    }

    function deleteRecord($dictionary = array())
    {
    }

    function showSortCaret($sortSearch)
    {
        if ($this->sorting['sortBy'] == $sortSearch && $this->sorting['sortOrder'] == 'DESC') {
            print('<i style="font-size:12px; color: #3a87ad" class="icon-caret-down"></i>');
        } else if ($this->sorting['sortBy'] == $sortSearch && $this->sorting['sortOrder'] == 'ASC') {
            print('<i style="font-size:12px; color: #3a87ad" class="icon-caret-up"></i>');
        }
    }

    function tel2enum($tel, $tld)
    {
        if (strlen($tld) == 0)  $tld="e164.arpa";

        // transform telephone number in FQDN Enum style domain name
        if (preg_match("/^[+]?(\d+)$/", $tel, $m)) {
            $l = strlen($m[1]);
            $rev_num = "";
            $z = 0;
            while ($z < $l) {
                $ss = substr($m[1], $z, 1);
                $enum = $ss.".".$enum;
                $z++;
            }
            preg_match("/^(.*)\.$/", $enum, $m);
            $enum = $m[1];
            $enum = $enum.".$tld.";
            return($enum);
        } else {
            return($tel);
        }
    }

    function showAddForm()
    {
        if ($this->selectionActive) return;
    }

    function showSortForm()
    {
        if (!count($this->sortElements)) {
            return;
        }

        $selected_sortBy[$this->sorting['sortBy']]='selected';

        print "<select class=span2 name=sortBy>";
        foreach (array_keys($this->sortElements) as $key) {
            printf(
                "<option value='%s' %s>%s",
                $key,
                $selected_sortBy[$key],
                $this->sortElements[$key]
            );
        }
        print("</select>");

        $selected_sortOrder[$this->sorting['sortOrder']]='selected';
        print("<select class=span1 name=sortOrder>");
        printf("<option value='DESC' %s>DESC", $selected_sortOrder['DESC']);
        printf("<option value='ASC' %s>ASC", $selected_sortOrder['ASC']);
        print("</select>");
    }

    function showTimezones($timezone)
    {
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }
        print "<select name=timezone>";
        print "<option>";
        while ($buffer = fgets($fp, 1024)) {
            $buffer = trim($buffer);
            if ($this->timezone == $buffer) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            printf("<option %s>%s>", $selected, $buffer);
        }
        print "</select>";
        fclose($fp);
    }

    function printHiddenFormElements($skipServiceElement = '')
    {
        if (!$skipServiceElement) {
            printf("<input type=hidden name=service value='%s'>", $this->SoapEngine->service);
        }

        if ($this->adminonly) {
            printf("<input type=hidden name=adminonly value='%s'>", $this->adminonly);
        }

        if (is_array($this->SoapEngine->extraFormElements)) {
	    foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
		if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
		printf(
		    "<input type=hidden name=%s value='%s'>\n",
		    $element,
		    $this->SoapEngine->extraFormElements[$element]
		);
	    }
	}
    }

    function getAllowedDomains()
    {
    }

    function showActionsForm()
    {
        if (!$this->selectionActive) {
            return;
        }

        $class_name = get_class($this).'Actions';

        if (class_exists($class_name)) {
            $actions = new $class_name($this->SoapEngine, $this->login_credentials);
            $actions->showActionsForm($this->filters, $this->sorting);
        }
    }

    function executeActions()
    {
        $this->showSeachForm();

        $this->getRecordKeys();

        dprint_r($this->selectionKeys);

        $class_name=get_class($this).'Actions';

        if (class_exists($class_name)) {
            $actions = new $class_name($this->SoapEngine, $this->login_credentials);
            $actions->execute(
                $this->selectionKeys,
                $_REQUEST['sub_action'],
                trim($_REQUEST['sub_action_parameter'])
            );
        }
    }

    function getCustomers()
    {
        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }
        if (!$this->filters['reseller']) return;

        // Filter
        $filter = array('reseller'=>intval($this->filters['reseller']));

        $range = array(
            'start' => 0,
            'count' => 100
        );

        // Order
        $orderBy = array(
            'attribute' => 'customer',
            'direction' => 'ASC'
        );

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $this->log_action('getCustomers');

        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getCustomers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            if ($result->total > $range['count']) return;

            if ($range['count'] <= $result->total) {
                $max = $range['count'];
            } else {
                $max = $result->total;
            }

            $i = 0;
            while ($i < $max) {
                $customer = $result->accounts[$i];
                $this->customers[$customer->id] = $customer->firstName.' '.$customer->lastName;
                $i++;
            }
            return true;
        }
    }

    function getResellers()
    {
        if (!$this->SoapEngine->customer_engine) {
            dprint("No customer_engine available");
            return true;
        }

        if (!$this->adminonly) return;
        // Filter

        $filter = array('reseller'=>intval($this->filters['reseller']));

        $range = array(
            'start' => 0,
            'count' => 200
        );

        // Order
        $orderBy = array(
            'attribute' => 'customer',
            'direction' => 'ASC'
        );

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $this->log_action('getResellers');
        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getResellers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            //if ($result->total > $range['count']) return;

            if ($range['count'] <= $result->total) {
                $max = $range['count'];
            } else {
                $max = $result->total;
            }

            $i = 0;
            while ($i < $max) {
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

    function getLoginAccount()
    {
        if (!$this->SoapEngine->customer_engine) {
            dprint("No customer_engine available");
            return true;
        }

        if (!$this->customer) {
            //print ("No customer available");
            return true;
        }

        $filter = array('customer'=>intval($this->customer));
        $range = array('start' => 0,'count' => 1);
        $orderBy = array('attribute' => 'customer','direction' => 'ASC');

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credetials
        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $this->log_action('getResellers');
        // Call function
        $result     = $this->SoapEngine->soapclientCustomers->getResellers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $this->loginAccount = $result->accounts[0];
            $this->loginImpersonate = $result->accounts[0]->impersonate;
            $this->loginProperties = $this->loginAccount->properties;
        }

        if ($this->loginAccount->reseller == $this->customer) {
            $this->resellerProperties = $this->loginProperties;
        } else {
            $filter = array('customer' => intval($this->loginAccount->reseller));
            $range = array('start' => 0, 'count' => 1);
            $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');

            // Compose query
            $Query = array(
                'filter'     => $filter,
                'orderBy' => $orderBy,
                'range'   => $range
            );


            // Insert credetials
            $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
            $this->log_action('getResellers');

            // Call function
            $result     = $this->SoapEngine->soapclientCustomers->getResellers($Query);

            if ((new PEAR)->isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                $log = sprintf(
                    "SOAP request error from %s: %s (%s): %s",
                    $this->SoapEngine->SOAPurl,
                    $error_msg,
                    $error_fault->detail->exception->errorcode,
                    $error_fault->detail->exception->errorstring
                );
                syslog(LOG_NOTICE, $log);
                return false;
            } else {
                $this->resellerProperties=$result->accounts[0]->properties;
            }
        }
        //dprint_r($this->resellerProperties);
    }

    function showCustomerForm($name = 'customer_filter')
    {
        if ($this->login_credentials['customer'] != $this->login_credentials['reseller']) {
            printf(" %s ", $this->login_credentials['customer']);
        } else {
            if (count($this->customers)) {
                $select_customer[$this->filters['customer']]='selected';
                printf("<select class=span2 name=%s>",$name);
                print("<option>");
                foreach (array_keys($this->customers) as $_res) {
                    printf(
                        "<option value='%s' %s>%s (%s)\n",
                        $_res,
                        $select_customer[$_res],
                        $_res,
                        $this->customers[$_res]
                    );
                }
                print("</select>");
            } else {
                printf(
                    "<input class=span1 type=text name=%s value='%s'>",
                    $name,
                    $this->filters['customer']
                );
            }
        }
    }

    function showResellerForm($name = 'reseller_filter')
    {
        if (!$this->adminonly) return;
        if ($this->login_credentials['reseller']) {
            printf(" %s ", $this->login_credentials['reseller']);
        } else {
            if (count($this->resellers)) {
                $select_reseller[$this->filters['reseller']] = 'selected';
                printf("<select class=span1 name=%s>", $name);
                print("<option>");
                foreach (array_keys($this->resellers) as $_res) {
                    printf(
                        "<option value='%s' %s>%s (%s)\n",
                        $_res,
                        $select_reseller[$_res],
                        $_res,
                        $this->resellers[$_res]
                    );
                }
                print("</select>");
            } else {
                printf(
                    "<input class=span1 type=text size=7 name=%s value='%s'>",
                    $name,
                    $this->filters['reseller']
                );
            }
        }
    }

    function showTextBeforeCustomerSelection()
    {
        print _("Owner");
    }

    function addFiltersToURL()
    {
        $url = '';
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

    function printFiltersToForm()
    {
        foreach(array_keys($this->filters) as $filter) {
            if (strlen(trim($this->filters[$filter]))) {
                printf("<input type=hidden name=%s_filter value='%s'>",$filter,trim($this->filters[$filter]));
            }
        }
    }

    function getRecord($domain)
    {
    }

    function updateRecord()
    {
    }

    function copyRecord()
    {
    }

    function showRecord($record) {
    }

    function RandomString($len = 11)
    {
        $alf = array("a","b","c","d","e","f",
               "h","i","j","k","l","m",
               "n","p","r","s","t","w",
               "x","y","1","2","3","4",
               "5","6","7","8","9");
        $i = 0;
        while ($i < $len) {
            srand((double)microtime()*1000000);
            $randval = rand(0, 28);
            $string = "$string"."$alf[$randval]";
            $i++;
        }
        return $string;
    }

    function RandomNumber($len = 5)
    {
        $alf = array("0","1","2","3","4","5",
                     "9","8","7","6");
        $i = 0;
        while ($i < $len) {
            srand((double)microtime() * 1000000);
            $randval = rand(0, 9);
            $string = "$string"."$alf[$randval]";
            $i++;
        }
        return $string;
    }

    function validDomain($domain)
    {
        if (!preg_match ("/^[A-Za-z0-9-.]{1,}\.[A-Za-z]{2,}$/", $domain)) {
            return false;
        }

        return true;
    }

    function getCarriers()
    {
        if (count($this->carriers)) return true;

        $Query=array('filter'  => array('name'=>''),
                     'orderBy' => array('attribute' => 'name',
                                        'direction' => 'ASC'
                                  ),
                     'range'   => array('start' => 0,
                                        'count' => 1000)
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getCarriers');
        $result     = $this->SoapEngine->soapclient->getCarriers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            foreach ($result->carriers as $_carrier) {
                $this->carriers[$_carrier->id]=$_carrier->name;
            }
        }
    }

    function getGateways()
    {
        if (count($this->gateways)) return true;

        $Query=array('filter'  => array('name'=>''),
                     'orderBy' => array('attribute' => 'name',
                                        'direction' => 'ASC'
                                  ),
                     'range'   => array('start' => 0,
                                        'count' => 1000)
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getGateways');
        $result = $this->SoapEngine->soapclient->getGateways($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            foreach ($result->gateways as $_gateway) {
                $this->gateways[$_gateway->id] = sprintf("%s, Carrier %s", $_gateway->name, $_gateway->carrier);
            }
        }
    }

    function updateBefore()
    {
        return true;
    }

    function updateAfter($customer, $customer_old)
    {
        return true;
    }

    function showCustomerTextBox()
    {
        print "<div class='input-prepend'><span class='add-on'>Owner</span>";
        if ($this->adminonly) {
            $this->showCustomerForm('customer');
            print "<div class='input-prepend'>";
            $this->showResellerForm('reseller');
            print "</div>";
        } else {
            $this->showCustomerForm('customer');
        }
    }

    function makebar($w)
    {
        $return = "<div style='width:150px' class=\"progress\">";
        if ($w < 0) $w = 0;
        if ($w > 100) $w = 100;
        $width = $w;
        $extra = 100 - $w;
        if ($width < 50) {
            $color = "black";
            $return .= "<div style='width:150px' class=\"progress progress-info progress-striped\"><div class=\"bar\" style=\"width: $width%\"></div>";
        } elseif ($width < 70) {
            $return .= "<div style='width:150px' class=\"progress progress-warning progress-striped\"><div class=\"bar\" style=\"width: $width%\"></div>";
        } else {
            $return .= "<div style='width:150px' class=\"progress progress-danger progress-striped\"><div class=\"bar\" style=\"width: $width%\"></div>";
        }
        $return .="</div>";
        return $return;
    }

    function customerFromLogin($dictionary = array())
    {
        if ($this->login_credentials['reseller']) {
            $reseller = $this->login_credentials['reseller'];

            if ($dictionary['customer']) {
                $customer = $dictionary['customer'];
            } elseif ($_REQUEST['customer']) {
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

    function getCustomerProperties($customer = '')
    {
        if (!$customer) $customer=$this->customer;

        $log=sprintf("getCustomerProperties(%s,engine=%s)",$customer, $this->SoapEngine->customer_engine);
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
        $this->log_action('getProperties');
        $result     = $this->SoapEngine->soapclientCustomers->getProperties(intval($customer));
        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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

    function setCustomerProperties($properties, $customer = '')
    {
        if (!$customer) $customer=$this->customer;

        $log=sprintf("setCustomerProperties(%s,engine=%s)",$customer, $this->SoapEngine->customer_engine);
        dprint($log);

        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!is_array($properties) || !$customer) return true;

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $this->log_action('setProperties');
        $result = $this->SoapEngine->soapclientCustomers->setProperties(intval($customer), $properties);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        return true;
    }

    function getCustomerProperty($name = '')
    {
	if (empty($this->loginProperties)) {
	    return false;
	}

        foreach ($this->loginProperties as $_property) {
            if ($_property->name == $name) {
                return $_property->value;
            }
        }

        return false;
    }

    function getResellerProperty($name = '')
    {
	if (empty($this->resellerProperties)) {
	    return false;
	}

        foreach ($this->resellerProperties as $_property) {
            if ($_property->name == $name) {
                return $_property->value;
            }
        }

        return false;
    }

    function checkRecord($dictionary)
    {
        return true;
    }

    function showWelcomeMessage()
    {
        if (!strlen($this->SoapEngine->welcome_message)) return ;
        printf ("%s",$this->SoapEngine->welcome_message);
    }

    function print_w($obj)
    {
        print "<pre>\n";
        print_r($obj);
        print "</pre>\n";
    }

    function hide_html()
    {
        return false;
    }
}

class SipDomains extends Records
{
    var $FieldsAdminOnly=array(
        'reseller' => array('type'=>'integer'),
    );

    var $Fields=array(
        'customer'    => array('type'=>'integer'),
        'certificate' =>  array('type'=>'text'),
        'private_key' =>  array('type'=>'text'),
        'match_ip_address' =>  array('type'=>'text', 'name'=> 'Match IP addresses'),
        'verify_cert' => array('type'=>'boolean'),
        'require_cert' => array('type'=>'boolean')
    );

    public function __construct($SoapEngine)
    {
        dprint("init Domains");

        $this->filters = array(
            'domain'       => strtolower(trim($_REQUEST['domain_filter']))
        );

        parent::__construct($SoapEngine);

        // keep default maxrowsperpage
        $this->sortElements = array(
            'changeDate' => 'Change date',
            'domain'     => 'Domain'
        );
    }

    function listRecords()
    {
        $this->showSeachForm();

        // Filter
        $filter = array(
            'domain'    => $this->filters['domain'],
            'customer'  => intval($this->filters['customer']),
            'reseller'  => intval($this->filters['reseller'])
        );

        // Range
        $range = array(
            'start' => intval($this->next),
            'count' => intval($this->maxrowsperpage)
        );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array(
            'attribute' => $this->sorting['sortBy'],
            'direction' => $this->sorting['sortOrder']
        );

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );
        dprint_r($Query);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');

        // Call function
        $result = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $this->rows = $result->total;

            if ($_REQUEST['action'] == 'Export' and $this->rows) {
                $this->exportDomain($result->domains[0]->domain);
                return;
            }


            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-striped table-condensed' width=100%>
            <thead>
            <tr>
                <th>Id</th>
                <th>Owner</th>
                <th colspan=3>SIP domain</th>
                <th>Change date</th>
                <th>Actions</th>
            </tr>
            </thead>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage) {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {
                    if (!$result->domains[$i]) break;
                    $domain = $result->domains[$i];

                    $index = $this->next+$i+1;

                    $delete_url = $this->url.sprintf(
                        "&service=%s&action=Delete&domain_filter=%s",
                        urlencode($this->SoapEngine->service),
                        urlencode($domain->domain)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['domain_filter'] == $domain->domain) {
                        $delete_url .= "&confirm=1";
                        $deleteText = "<font color=red>Confirm</font>";
                    } else {
                        $deleteText = "Delete";
                    }

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['domain_filter'] == $domain->domain) {
                        $delete_url .= "&confirm=1";
                        $deleteText = "<font color=red>Confirm</font>";
                    } else {
                        $deleteText = "Delete";
                    }

                    $_customer_url = $this->url.sprintf(
                        "&service=customers@%s&customer_filter=%s",
                        urlencode($this->SoapEngine->customer_engine),
                        urlencode($domain->customer)
                    );

                    $_sip_domains_url = $this->url.sprintf(
                        "&service=sip_domains@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                    );

                    $_sip_accounts_url = $this->url.sprintf(
                        "&service=sip_accounts@%s&domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                    );

                    $_sip_aliases_url = $this->url.sprintf(
                        "&service=sip_aliases@%s&alias_domain_filter=%s",
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($domain->domain)
                    );
                    if ($this->adminonly) {
                        $export_url = $this->url.sprintf(
                            "&service=%s&action=Export&domain_filter=%s",
                            urlencode($this->SoapEngine->service),
                            urlencode($domain->domain)
                        );

                        printf(
                            "
                            <tr>
                            <td>%s</td>
                            <td><a href=%s>%s.%s</a></td>
                            <td><a href=%s>%s</a></td>
                            <td><a href=%s>Sip accounts</a></td>
                            <td><a href=%s>Sip aliases</a></td>
                            <td>%s</td>
                            <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                            <td><a class='btn-small btn-danger' href=%s>Export</a></td>
                            </tr>",
                            $index,
                            $_customer_url,
                            $domain->customer,
                            $domain->reseller,
                            $_sip_domains_url,
                            $domain->domain,
                            $_sip_accounts_url,
                            $_sip_aliases_url,
                            $domain->changeDate,
                            $delete_url,
                            $deleteText,
                            $export_url
                        );
                    } else {
                        printf(
                            "
                            <tr>
                            <td>%s</td>
                            <td><a href=%s>%s.%s</a></td>
                            <td><a href=%s>%s</a></td>
                            <td><a href=%s>Sip accounts</a></td>
                            <td><a href=%s>Sip aliases</a></td>
                            <td>%s</td>
                            <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                            </tr>",
                            $index,
                            $_customer_url,
                            $domain->customer,
                            $domain->reseller,
                            $_sip_domains_url,
                            $domain->domain,
                            $_sip_accounts_url,
                            $_sip_aliases_url,
                            $domain->changeDate,
                            $delete_url,
                            $deleteText
                        );
                    }

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($domain);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showSeachFormCustom()
    {
        printf (" <div class='input-prepend'><span class=add-on>SIP domain</span><input class=span2 type=text size=20 name=domain_filter value='%s'></div>",$this->filters['domain']);
    }

    function exportRecord($dictionary = array())
    {
    }

    function deleteRecord($dictionary = array())
    {
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
        return $this->SoapEngine->execute($function, $this->html);
    }

    function showAddForm() {
        if ($this->selectionActive) return;
            printf ("<form class=form-inline method=post name=addform action=%s enctype='multipart/form-data'>",$_SERVER['PHP_SELF']);
            print "
            <div class='well well-small'>
            ";

            print "
            <input type=submit class='btn btn-warning' name=action value=Add>
            ";

            $this->showCustomerTextBox();

            printf (" </div><div class='input-prepend'><span class='add-on'>SIP domain</span><input type=text size=20 name=domain></div>");

            $this->printHiddenFormElements();
            printf (" Import SIP domain from file:
            <input type='hidden' name='MAX_FILE_SIZE' value=1024000>
            <div class='fileupload fileupload-new' style='display: inline-block; margin-bottom:0px' data-provides='fileupload'>
                <div class='input-append'>
                    <div class='uneditable-input input-small'>
                        <span class='fileupload-preview'></span>
                    </div>
                    <span class='btn btn-file'>
                    <span class='fileupload-new'>Select file</span>
                    <span class='fileupload-exists'>Change</span>
                    <input type='file' name='import_file'/></span>
                    <a href='#' class='btn fileupload-exists' data-dismiss='fileupload'>Remove</a>
                    <button type='submit' name=action class='btn fileupload-exists' value=\"Add\"><i class='icon-upload'></i> Import</button>
                </div>
            </div>
            "
            );
            print "</div>
            </form>
        ";
    }

    function addRecord($dictionary = array())
    {
        if ($this->adminonly && $_FILES['import_file']['tmp_name']) {
            $content=fread(fopen($_FILES['import_file']['tmp_name'], "r"), $_FILES['import_file']['size']);
            //print_r($content);

            if (!$imported_data=json_decode($content, true)) {
                printf ("<p><font color=red>Error: reading imported data. </font>");
                return false;
            }

            //print_r($imported_data);

            if (!in_array('sip_domains', array_keys($imported_data))) {
                printf ("<p><font color=red>Error: Missing SIP domains in imported data. </font>");
                return false;
            }

            if (!in_array('sip_accounts', array_keys($imported_data))) {
                return false;
                printf ("<p><font color=red>Error: Missing SIP accounts in imported data. </font>");
            }

            foreach($imported_data['customers'] as $customer) {
                // Insert credetials
                $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addAccount');

                $customer['credit'] = floatval($customer['credit']);
                $customer['balance'] = floatval($customer['balance']);
                // Call function
                $result     = $this->SoapEngine->soapclientCustomers->addAccount($customer);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 5001) {
                        $result     = $this->SoapEngine->soapclientCustomers->updateCustomer($customer);
                        if ((new PEAR)->isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                            printf ("<p><font color=red>Error: $log</font>");
                        } else {
                            printf('<p>Customer %s has been updated',$customer['id']);
                        }
                    } else {
                        $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                        printf ("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>Customer %s has been added',$customer['id']);
                }
            }

            foreach($imported_data['sip_domains'] as $domain) {
                flush();
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addDomain');
                $result = $this->SoapEngine->soapclient->addDomain($domain);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 1001) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $this->log_action('updateDomain');
                        $result = $this->SoapEngine->soapclient->updateDomain($domain);
                        if ((new PEAR)->isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                            printf ("<p><font color=red>Error: $log</font>");
                         } else {
                             printf('<p>SIP domain %s has been updated',$domain['domain']);
                         }
                    } else {
                        $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                        printf ("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>SIP domain %s has been added',$domain['domain']);
                }

            }
            $i = 0;
            $added = 0;
            $updated = 0;
            $failed = 0;
            foreach($imported_data['sip_accounts'] as $account) {
                $i+=1;
                flush();
                $account['callLimit'] = intval($account['callLimit']);
                $account['prepaid']   = intval($account['prepaid']);
                $account['quota']     = intval($account['quota']);
                $account['owner']     = intval($account['owner']);
                $account['timeout']   = intval($account['timeout']);

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addAccount');
                $result = $this->SoapEngine->soapclient->addAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 1011) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $result = $this->SoapEngine->soapclient->updateAccount($account);
                        if ((new PEAR)->isError($result)) {
                            $error_msg  = $result->getMessage();
                            $error_fault= $result->getFault();
                            $error_code = $result->getCode();
                            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                            printf ("<p><font color=red>Error: $log</font>");
                            $failed += 1;
                        } else {
                            printf('<p>%d SIP account %s@%s has been updated',$i, $account['id']['username'], $account['id']['domain']);
                            $updated += 1;
                        }
                    } else {
                        $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                        printf ("<p><font color=red>Error: $log</font>");
                        $failed += 1;
                    }
                } else {
                    printf('<p>%d SIP account %s@%s has been added',$i, $account['id']['username'], $account['id']['domain']);
                    $added += 1;
                }
            }
            if ($added) {
                printf('<p>%d SIP accounts added',$added);
            }
            if ($updated) {
                printf('<p>%d SIP accounts updated',$updated);
            }
            if ($failed) {
                printf('<p>%d SIP accounts failed',$failed);
            }

            $added = 0;
            foreach($imported_data['sip_aliases'] as $alias) {
                flush();

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addAlias');
                $result = $this->SoapEngine->soapclient->addAlias($alias);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                    printf ("<p><font color=red>Error: $log</font>");
                } else {
                   $added += 1;
                }
            }

            if ($added) {
                printf('<p>%d SIP aliases added',$added);
            }

            return true;
        } else {
            if ($dictionary['domain']) {
                $domain = $dictionary['domain'];
            } else {
                $domain = trim($_REQUEST['domain']);
            }

            list($customer, $reseller)=$this->customerFromLogin($dictionary);

            if (!$this->validDomain($domain)) {
                print "<font color=red>Error: invalid domain name</font>";
                return false;
            }

            $domainStructure = array('domain'   => strtolower($domain),
                                     'customer' => intval($customer),
                                     'reseller' => intval($reseller)
                                    );
            $function=array('commit'   => array('name'       => 'addDomain',
                                                'parameters' => array($domainStructure),
                                                'logs'       => array('success' => sprintf('SIP domain %s has been added',$domain)))
                                               );

            return $this->SoapEngine->execute($function, $this->html);
        }
    }

    function getRecordKeys()
    {
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
        $this->log_action('getDomains');
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error in getAllowedDomains from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            //return false;
        } else {
            foreach ($result->domains as $_domain) {
                $this->selectionKeys[]=$_domain->domain;
            }
        }
    }

    function getRecord($domain)
    {
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
        $this->log_action('getDomains');

        // Call function
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            if ($result->domains[0]) {
                return $result->domains[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($domain)
    {
        if ($domain->certificate and $domain->private_key) {
            $pemdata = sprintf("%s\n%s", $domain->certificate,  $domain->private_key);
            $cert = openssl_x509_read( $pemdata );
            if ($cert) {
                $cert_data = openssl_x509_parse( $cert );
                openssl_x509_free( $cert );
                $expire = mktime($cert_data['validTo_time_t']);
            } else {
                $cert_data = "";
            }
        }

        #print("<pre>");
        #print_r($cert_data);
        #print("</pre>");
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

        if ($cert_data) {
            // Parse the resource and print out the contents.
                $ts = $cert_data['validTo_time_t'];
                $expire = new DateTime("@$ts");
                printf("<tr><td>TLS CN</td><td>%s</td></tr>", $cert_data['subject']['CN']);
                printf("<tr><td>CA Issuer</td><td>%s %s %s</td></tr>", $cert_data['issuer']['C'], $cert_data['issuer']['O'], $cert_data['issuer']['CN']);;
                printf("<tr><td>Expire date</td><td>%s</td></tr>", $expire->format('Y-m-d'));
        }
        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name=$this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name=preg_replace("/_/"," ",ucfirst($item));
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
                } else if ($this->FieldsAdminOnly[$item]['type'] == 'boolean') {
                    if ($domain->$item == 1) {
                        $checked = "checked";   
                    } else {
                        $checked = "";
                    }
                
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input type=checkbox name=%s_form %s value=1></td>
                    </tr>",
                    $item_name,
                    $item,
                    $checked
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
                $item_name = preg_replace("/_/"," ",ucfirst($item));
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf(
                    "<tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                    </tr>",
                    $item_name,
                    $item,
                    $domain->$item
                );
            } elseif ($this->Fields[$item]['type'] == 'boolean') {
                if ($domain->$item == 1) {
                    $checked = "checked";
                } else {
                    $checked = "";
                }

                printf(
                    "<tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input type=checkbox name=%s_form %s value=1></td>
                    </tr>",
                    $item_name,
                    $item,
                    $checked
                );
            } else {
                printf(
                    "<tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    </tr>",
                    $item_name,
                    $item,
                    $domain->$item
                );
            }
        }

        printf("<input type=hidden name=domain_filter value='%s'>", $domain->domain);
        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print "</form>";
        print "</table>";
    }

    function updateRecord()
    {
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
                $domain->$item = intval($_REQUEST[$var_name] == 1);
            } else if ($this->Fields[$item]['type'] == 'boolean') {
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
                } else if ($this->Fields[$item]['type'] == 'boolean') {
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

        return $this->SoapEngine->execute($function, $this->html);
    }

    function hide_html() {
        if ($_REQUEST['action'] == 'Export') {
            return true;
        } else {
            return false;
        }
    }

    function exportDomain($domain) {
        $exported_data= array();
        // Filter
        $filter=array(
                      'domain'    => $domain,
                      'customer'  => intval($this->filters['customer']),
                      'reseller'  => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1000
                     );
        // Compose query
        $Query=array('filter'  => $filter,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $i = 0 ;

            while ($i < $result->total) {
                $domain = $result->domains[$i];
                if (!in_array($domain->customer, $export_customers)) {
                    $export_customers[]=$domain->customer;
                }
                if (!in_array($domain->reseller, $export_customers)) {
                    $export_customers[]=$domain->reseller;
                }
                $i+=1;
                $exported_data['sip_domains'][] = objectToArray($domain);
            }
        }

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccounts');
        // Call function
        $result = call_user_func_array(array($this->SoapEngine->soapclient,'getAccounts'),array($Query));

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $exported_data['sip_accounts'] = objectToArray($result->accounts);
            foreach ($result->accounts as $account) {
                if (!in_array($account->owner, $export_customers)) {
                    $export_customers[]=$account->owner;
                }

                $sipId=array("username" => $account->id->username,
                             "domain"   => $account->id->domain
                             );
                $this->SoapEngine->soapclientVoicemail->addHeader($this->SoapEngine->SoapAuthVoicemail);
                $result = $this->SoapEngine->soapclientVoicemail->getAccount($sipId);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode != "2000" && $error_fault->detail->exception->errorcode != "1010") {
                        printf ("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                    }
                } else {
                    $exported_data['voicemail_accounts'][] = $result;
                }

                // Filter
                $filter=array('targetUsername' => $account->id->username,
                              'targetDomain'   => $account->id->domain
                              );

                // Range
                $range=array('start' => 0,
                             'count' => 20
                             );

                // Compose query
                $Query=array('filter'  => $filter,
                             'range'   => $range
                             );

                // Call function
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAliases');
                $result = $this->SoapEngine->soapclient->getAliases($Query);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                } else {
                    foreach ($result->aliases as $alias) {
                        $exported_data['sip_aliases'][] = objectToArray($alias);
                    }
                }
            }
        }

        foreach ($export_customers as $customer) {
            if (!$customer) {
                continue;
            }
            $filter=array(
                          'customer'     => intval($customer),
                          );

            // Compose query
            $Query=array('filter'     => $filter
                            );

            // Insert credetials
            $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);
            $this->log_action('getCustomers');

            // Call function
            $result     = $this->SoapEngine->soapclientCustomers->getCustomers($Query);
            if ((new PEAR)->isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                syslog(LOG_NOTICE, $log);
                return false;
            } else {
                $exported_data['customers'] = objectToArray($result->accounts);
            }
        }
        //print_r($exported_data['customers']);

        print_r(json_encode($exported_data));
    }
}

require_once 'NGNPro/Records/SipAccounts.php';
require_once 'NGNPro/Records/SipAliases.php';
require_once 'NGNPro/Records/EnumRanges.php';
require_once 'NGNPro/Records/EnumMappings.php';
require_once 'NGNPro/Records/DnsZones.php';
require_once 'NGNPro/Records/DnsRecords.php';

class FancyRecords extends DnsRecords
{
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

class EmailAliases extends FancyRecords
{
    var $recordTypes=array('MBOXFW'  => 'Email alias');
    var $typeFilter='MBOXFW';
}

class UrlRedirect extends FancyRecords
{
    var $recordTypes=array('URL'   => 'URL forwarding');
    var $typeFilter='URL';
}

class TrustedPeers extends Records
{
    var $FieldsAdminOnly=array(

                              'msteams'     => array('type'=>'boolean', 'name' => 'MS Teams'),
                              'prepaid'    => array('type'=>'boolean'),
                              'tenant'      => array('type'=>'string'),
                              'callLimit'   => array('type'=>'integer', 'name' => 'Capacity'),
                              'blocked'    => array('type'=>'integer')
                              );
    var $Fields=array(
                              'description'    => array('type'=>'string'),
                              'authToken'    => array('type'=>'string', 'name' => 'Authentication token')
                              );

    public function __construct($SoapEngine)
    {

        $this->filters   = array('ip'     => trim($_REQUEST['ip_filter']),
                                 'tenant'   => trim($_REQUEST['tenant_filter']),                     
                                 'description'  => trim($_REQUEST['description_filter']),
                                 'msteams'  => trim($_REQUEST['msteams_filter'])
                                 );

        parent::__construct($SoapEngine);

        $this->sortElements=array(
                        'changeDate'  => 'Change date',
                        'description' => 'Description',
                        'ip'          => 'Address'
                        );
    }

    function listRecords() {

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('ip' => $this->filters['ip'],
                      'description'   => $this->filters['description'],
                      'tenant' => $this->filters['tenant'],
                      'msteams' => 1 == intval($this->filters['msteams'])
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
        $this->log_action('getTrustedPeers');

        $result     = $this->SoapEngine->soapclient->getTrustedPeers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>

            <table class='table table-striped table-condensed' width=100%>
            <thead>
            <tr>
                <td><b>Id</b></th>
                <td><b>Owner</b></td>
                <td><b>Trusted peer</b></td>
                <td><b>Prepaid</b></td>
                <td><b>Capacity</b></td>
                <td><b>MS Teams</b></td>
                <td><b>Tenant</b></td>
                <td><b>Description</b></td>
                <td><b>Blocked</b></td>
                <td><b>Change date</b></td>
                <td><b>Actions</b></td>
            </tr>
            </thead>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage) {
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

                    $delete_url = $this->url.sprintf("&service=%s&action=Delete&ip_filter=%s&msteams_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($peer->ip),
                    urlencode(intval($peer->msteams))
                    );

                    $update_url = $this->url.sprintf("&service=%s&ip_filter=%s&msteams_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($peer->ip),
                    urlencode($peer->msteams)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['ip_filter'] == $peer->ip) {
                        $delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }
                    if ($peer->msteams) {
                        $msteams = 'Yes';
                    } else {
                        $msteams = 'No';
                    }

                    if ($peer->prepaid) {
                        $prepaid = 'Yes';
                    } else {
                        $prepaid = 'No';
                    }

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($peer->reseller)
                    );

                    printf("
                    <tr>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    </tr>",
                    $index,
                    $_customer_url,
                    $peer->reseller,
                    $update_url,
                    $peer->ip,
                    $prepaid,
                    $peer->callLimit,
                    $msteams,
                    $peer->tenant,
                    $peer->description,
                    $peer->blocked,
                    $peer->changeDate,
                    $delete_url,
                    $actionText
                    );

                    $i++;
                }
            }

            print "</table>";

            if ($result->total == 1) {
                $this->showRecord($peer);
            }
            $this->showPagination($maxrows);

            return true;
        }
    }

    function showRecord($peer) {
        print "<table border=0 cellpadding=10>";
        print "
        <tr>
        <td valign=top>
        <table border=0>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

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
                    $peer->$item
                    );
                } else if ($this->FieldsAdminOnly[$item]['type'] == 'boolean') {
                    if ($peer->$item == 1) {
                        $checked = "checked";   
                    } else {
                        $checked = "";
                    }
                
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input type=checkbox name=%s_form %s value=1></td>
                    </tr>",
                    $item_name,
                    $item,
                    $checked
                    );
                } else {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    </tr>",
                    $item_name,
                    $item,
                    $peer->$item
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
                $peer->$item
                );
            } else if ($this->Fields[$item]['type'] == 'boolean') {
                if ($peer->$item == 1) {
                    $checked = "checked";   
                } else {
                    $checked = "";
                }
                
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input type=checkbox name=%s_form %s value=1></td>
                </tr>",
                $item_name,
                $item,
                $checked
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                </tr>",
                $item_name,
                $item,
                $peer->$item
                );
            }
        }

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        printf ("<input type=hidden name=ip_filter value='%s'>",$peer->ip);
        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        printf ("<form class=form-inline method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";

        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";
        $this->showCustomerTextBox();
        if ($this->filters['msteams']) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        printf (" <div class='input-prepend'><span class='add-on'>Address</span><input class=span2 type=text size=20 name=ipaddress></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Call limit</span><input class=span1 type=text size=4 name=callLimit value=30></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Description</span><input class=span2 type=text size=30 name=description></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Tenant</span><input class=span1 type=text size=20 name=tenant value=%s></div>", $this->filters['tenant']);
        printf (" <div class='input-prepend'><span class='add-on'>MS Teams<input type=checkbox class=span1 name=msteams value=1 %s></span></div>", $checked);

        $this->printHiddenFormElements();

        print "</div>
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

        if ($dictionary['msteams']) {
            $msteams   = $dictionary['msteams'];
        } else {
            $msteams   = trim($_REQUEST['msteams']);
        }
        
        $this->filters['msteams'] = $msteams;

        if ($dictionary['description']) {
            $description   = $dictionary['description'];
        } else {
            $description   = trim($_REQUEST['description']);
        }

        if ($dictionary['tenant']) {
            $tenant = $dictionary['tenant'];
        } else {
            $tenant = trim($_REQUEST['tenant']);
        }

        if ($dictionary['callLimit']) {
            $callLimit   = $dictionary['callLimit'];
        } else {
            $callLimit   = trim($_REQUEST['callLimit']);
        }

        if ($dictionary['owner']) {
            $owner   = $dictionary['owner'];
        } else {
            $owner   = trim($_REQUEST['owner']);
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        if (!strlen($ipaddress)) {
            printf ("<p><font color=red>Error: Missing IP or description. </font>");
            return false;
        }

        $peer=array(
                     'ip'          => $ipaddress,
                     'description' => $description,
                     'callLimit'  => intval($callLimit),
                     'msteams'    => 1 == $msteams,
                     'tenant'     => $tenant,
                     'blocked'    => 0,
                     'owner'       => intval($_REQUEST['owner']),
                     'customer'    => intval($customer),
                     'reseller'    => intval($reseller)
                    );

        $function=array('commit'   => array('name'       => 'addTrustedPeer',
                                            'parameters' => array($peer),
                                            'logs'       => array('success' => sprintf('Trusted peer %s has been added',$ipaddress)))
                        );

        return $this->SoapEngine->execute($function,$this->html);
    }

    function updateRecord() {
        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        if (!strlen($this->filters['ip'])) {
            print "<p><font color=red>Error: missing peer address. </font>";
            return false;
        }
        $peer=array(
                     'ip'          => $this->filters['ip'],
                     'description' => $_REQUEST['description_form'],
                     'authToken' => $_REQUEST['authToken_form'],
                     'tenant'      => $_REQUEST['tenant_form'],
                     'callLimit'   => intval($_REQUEST['callLimit_form']),
                     'prepaid'   => 1 == $_REQUEST['prepaid_form'],
                     'blocked'   => intval($_REQUEST['blocked_form']),
                     'msteams'     => 1 == $_REQUEST['msteams_form'],
                     'customer'    => intval($customer),
                     'reseller'    => intval($reseller)
                    );
    
        $function=array('commit'   => array('name'       => 'updateTrustedPeer',
                                            'parameters' => array($peer),
                                            'logs'       => array('success' => sprintf('Trusted peer %s has been updated',$this->filters['ip'])))
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
                                            'logs'       => array('success' => sprintf('Trusted peer %s has been deleted',$this->filters['ip'])))
                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {
        if (intval($this->filters['msteams'])  == 1) {
            $checked_msteams = 'checked';
        } else {
            $checked_msteams = '';
        }

        printf (" <div class='input-prepend'><span class='add-on'>Address</span><input class=span2 type=text size=20 name=ip_filter value='%s'></div>",$this->filters['ip']);
        printf (" <div class='input-prepend'><span class='add-on'>Description</span><input type=text class=span2 size=20 name=description_filter value='%s'></div>",$this->filters['description']);
        printf (" <div class='input-prepend'><span class='add-on'>Tenant</span><input type=text class=span1 size=10 name=tenant_filter value='%s'></div>",$this->filters['tenant']);
        printf (" <div class='input-prepend'><span class='add-on'>Blocked</span><input class=span1 type=text size=4 name=blocked_filter value='%s'></div>",$this->filters['blocked']);
        printf (" <div class='input-prepend'><span class='add-on'>MS Teams<input type=checkbox value=1 name=msteams_filter class=span1 %s></span></div>",$checked_msteams);

    }

    function showCustomerTextBox () {
        print "<div class='input-prepend'><span class='add-on'>Owner</span>";
        $this->showResellerForm('reseller');
        print "</div>";
    }

    function showTextBeforeCustomerSelection() {
        print "Owner";
    }

    function showCustomerForm($name='customer_filter') {
    }
}

require_once 'NGNPro/Records/Carriers.php';

class Gateways extends Records
{
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

    public function __construct($SoapEngine)
    {
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

        parent::__construct($SoapEngine);
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
        $this->log_action('getGateways');
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-striped table-condensed' width=100%>
            ";

            print "
            <thead>
            <tr>
                <th>Id</th>
                <th>Owner</th>
                <th>Gateway</th>
                <th>Carrier</th>
                <th>Name</th>
                <th>Address</th>
                <th>Rules</th>
                <th>Change date</th>
                <th>Actions</th>
                </tr>
               </thead>
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
                    <tr>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top>%s</td>
                    <td valign=top>%s:%s:%s</td>
                    <td valign=top><a href=%s>Rules</a></td>
                    <td valign=top>%s</td>
                    <td valign=top><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>",
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

        printf ("<form class='form-inline' method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
            <div class='well well-small'>
            ";

        print "
            <input class='btn btn-warning' type=submit name=action value=Add>
            ";

        printf (" Carrier ");

        print "<select name=carrier_id> ";
        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s'>%s",$_carrier,$this->carriers[$_carrier]);
        }

        printf (" </select>");

        printf ("  <div class=input-prepend><span class=\"add-on\">Name</span><input class=span2 type=text size=20 name=name></div>");

        printf ("  <div class=input-prepend><span class=\"add-on\">Transport</span>");

        print "<select class=span1 name=transport> ";

        foreach ($this->transports as $_transport) {
            printf ("<option value='%s'>%s",$_transport,$_transport);
        }

        printf (" </select></div>");

        printf ("  <div class=input-prepend><span class=\"add-on\">Address</span><input class=span2 type=text size=25 name=address></div>");

        $this->printHiddenFormElements();

        print "</div>
            </form>
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
        printf ("  <div class=input-prepend><span class=\"add-on\">Gateway</span><input class=2 type=text size=10 name=id_filter value='%s'></div>",$this->filters['id']);

        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        printf (" </select>");
        printf (" <div class=input-prepend><span class=\"add-on\">Name</span><input type=text size=20 name=name_filter value='%s'></div>",$this->filters['name']);

    }

    function showCustomerForm($name='customer_filter') {
    }

    function showTextBeforeCustomerSelection() {
        print "Owner";
    }

    function showRecord($gateway) {

        print "<h3>Gateway</h3>";

        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<div class=control-group>
            <label class=control-label>%s</label>
            ",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                $item,
                $gateway->$item,
                $gateway->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf ("<div class=controls><select class=span2 name=%s_form>",$item);
                    $selected_carrier[$gateway->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
                    }
                    printf (" </select></div>");

                } else if ($item == 'transport') {
                    printf ("<div class=controls><select class=span2 name=%s_form>",$item);
                    $selected_transport[$gateway->$item]='selected';
                    foreach ($this->transports as $_transport) {
                        printf ("<option value='%s' %s>%s",$_transport,$selected_transport[$_transport],$_transport);
                    }

                    print "</select></div>";

                } else {
                    printf ("<div class=controls><input class=span2 name=%s_form size=30 type=text value='%s'></div>",
                    $item,
                    $gateway->$item
                    );
                }
            }
            print "
            </div>";
        }

        printf ("<input type=hidden name=id_filter value='%s'>",$gateway->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class=form-actions>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";

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
        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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
        $this->log_action('getGateways');
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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

require_once 'NGNPro/Records/GatewayRules.php';

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

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'prefix'    => trim($_REQUEST['prefix_filter']),
            'priority'  => trim($_REQUEST['priority_filter']),
            'carrier_id'=> trim($_REQUEST['carrier_id_filter']),
            'reseller'  => trim($_REQUEST['reseller_filter']),
            'id'        => trim($_REQUEST['id_filter'])
        );
        parent::__construct($SoapEngine);
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
        $this->log_action('getRoutes');
        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-condensed table-striped'width=100%>
            ";

            print "<thead>
            <tr>
                <th><b>Id</b></th>
                <th><b>Owner</b></th>
                <th><b>Route</b></th>
                <th><b>Carrier</b></th>
                <th><b>Gateways</b></th>
                <th><b>Prefix</b></th>
                <th><b>Originator</b></th>
                <th><b>Priority</b></th>
                <th><b>Change date</b></th>
                <th><b>Actions</b></th>
                </tr>
            </thead>
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
                    <tr>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>Gateways</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>",
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

        printf ("<form class=form-inline method=post name=addform action=%s><div class='well well-small'>",$_SERVER['PHP_SELF']);

        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";

        print "<div class='input-prepend'><span class='add-on'>";
        printf (" Carrier ");
        print "</span>";

        print "<select class=span2 name=carrier_id> ";
        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s'>%s",$_carrier,$this->carriers[$_carrier]);
        }
        printf (" </select></div>");

        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input type=text size=20 name=prefix></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Originator</span><input type=text size=20 name=originator></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Priority</span><input type=text size=5 name=priority></div>");

        $this->printHiddenFormElements();

        print "</div>
        </form>
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

        printf (" <div class='input-prepend'><span class='add-on'>Route</span><input type=text size=10 name=id_filter value='%s'></div>",$this->filters['id']);
        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        print "</select>";
        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input type=text size=15 name=prefix_filter value='%s'></div>",$this->filters['prefix']);

    }

    function showCustomerTextBox () {
        print "Owner";
        $this->showResellerForm('reseller');
    }

    function showCustomerForm($name='customer_filter') {
    }

    function showTextBeforeCustomerSelection() {
        print "Owner";
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
        $this->log_action('getRoutes');
        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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

        print "<h3>Route</h3>";

        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<div class=control-group>
            <label class=control-label>%s</label>
            ",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                $item,
                $route->$item,
                $route->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf ("<div class=controls><select name=%s_form>",$item);
                    $selected_carrier[$route->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
                    }
                    printf (" </select></div>");

                } else {
                    printf ("<div class=controls><input name=%s_form type=text value='%s'></div>",
                    $item,
                    $route->$item
                    );
                }
            }
            print "</div>";
        }

        printf ("<input type=hidden name=id_filter value='%s'>",$carier->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class='form-actions'>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";
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

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            return true;
        }
    }
}

class Customers extends Records
{
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

    var $propertiesItems = array(
        'sip_credit'          => array(
            'name'      => 'Credit for SIP accounts',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'sip_alias_credit'    => array(
            'name'      => 'Credit for SIP aliases',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'enum_range_credit'   => array(
            'name'      => 'Credit for ENUM ranges',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'enum_number_credit'  => array(
            'name'      => 'Credit for ENUM numbers',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'dns_zone_credit'     => array(
            'name'      => 'Credit for DNS zones',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'email_credit'        => array(
            'name'      => 'Credit for E-mail aliases',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'pstn_access'         => array(
            'name'      => 'Access to PSTN',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'prepaid_changes'      => array(
            'name'      => 'Prepaid Changes',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'pstn_changes'      => array(
            'name'       => 'Pstn Changes',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'payment_processor_class'      => array(
            'name'       => 'Payment Processor Class',
            'category'   => 'sip',
            'permission' => 'admin'
        ),
        'voicemail_server'      => array(
            'name'       => 'Voicemail Server Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'voicemail_access_number'    => array(
            'name'       => 'Voicemail Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FUNC_access_number'    => array(
            'name'      => 'Forwarding Unconditional Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FNOL_access_number'    => array(
            'name'      => 'Forwarding Not-Online Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FNOA_access_number'    => array(
            'name'      => 'Forwarding Not-Available Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FBUS_access_number'    => array(
            'name'      => 'Forwarding On Busy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'change_privacy_access_number' => array(
            'name'      => 'Change privacy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'check_privacy_access_number' => array(
            'name'      => 'Check privacy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'reject_anonymous_access_number' => array(
            'name'      => 'Reject anonymous Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_proxy'           => array(
            'name'      => 'SIP Proxy Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_outbound_proxy'   => array(
            'name'      => 'SIP Client Outbound proxy',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'store_clear_text_passwords' => array(
            'name'      => 'Store clear text passwords',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'xcap_root'           => array(
            'name'      => 'XCAP Root URL',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'absolute_voicemail_uri'=> array(
            'name'    => 'Use Absolute Voicemail Uri',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'dns_admin_email'     => array('name'     => 'DNS zones Administrator Email',
            'category' => 'dns',
            'permission'  => 'customer'),
        'support_web'         => array('name'      => 'Support Web Site',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'support_email'       => array('name'      => 'Support Email Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'billing_email'       => array('name'      => 'Billing Email Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'support_company'     => array('name'      => 'Support Organization',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'cdrtool_address'     => array('name'      => 'CDRTool Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_settings_page'   => array('name'      => 'SIP Settings Page',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'digest_settings_page' => array('name'     => 'Settings Page (Digest Auth)',
            'category'   => 'sip',
            'permission' => 'reseller'
        ),
        'records_per_page'    => array('name'     => 'Records per page',
            'category'  => 'web',
            'permission'  => 'customer'
        ),
        'push_notifications_server' => array('name'=>'Push server public interface',
            'category' =>'sip',
            'permission' => 'customer'
        ),
        'push_notifications_server_private' => array('name'=>'Push server private interface',
            'category' =>'sip',
            'permission' => 'customer'
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
        'username'    => array('type'       =>'text', 'extra_html' => 'readonly autocomplete="off"'
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
        'vatNumber'   => array(
            'type'=>'text',
            'name'=>'VAT number'
        ),
        'bankAccount' => array(
            'type'=>'text',
            'name'=>'Bank account'
        ),
        'billingEmail' => array(
            'type'=>'text',
            'name'=>'Billing email'
        ),
        'billingAddress' => array(
            'type'=>'textarea',
            'name'=>'Billing address'
        ),
    );

    var $addFields=array(
        'username'    => array(
            'type'       =>'text'
        ),
        'password'    => array(
            'type'=>'text',
            'name'=>'Password'
        ),
        'firstName'   => array(
            'type'=>'text',
            'name'=>'First name'
        ),
        'lastName'    => array(
            'type'=>'text',
            'name'=>'Last name'
        ),
        'organization'=> array('type'=>'text'),
        'tel'         => array('type'=>'text'),
        'email'       => array('type'=>'text'),
        'address'     => array('type'=>'textarea'),
        'postcode'    => array('type'=>'text'),
        'city'        => array('type'=>'text'),
        'state'       => array('type'=>'text'),
        'country'     => array('type'=>'text'),
        'timezone'    => array('type'=>'text')
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

    var $hide_html = false;

    public function __construct($SoapEngine)
    {
        dprint("init Customers");

        $this->filters   = array(
            'username'       => trim($_REQUEST['username_filter']),
            'firstName'      => trim($_REQUEST['firstName_filter']),
            'lastName'       => trim($_REQUEST['lastName_filter']),
            'organization'   => trim($_REQUEST['organization_filter']),
            'tel'            => trim($_REQUEST['tel_filter']),
            'email'          => htmlspecialchars(trim($_REQUEST['email_filter'])),
            'web'            => trim($_REQUEST['web_filter']),
            'country'        => trim($_REQUEST['country_filter']),
            'city'           => trim($_REQUEST['city_filter']),
            'only_resellers' => trim($_REQUEST['only_resellers_filter'])
        );

        parent::__construct($SoapEngine);

        $this->showAddForm = $_REQUEST['showAddForm'];

        if (is_array($this->SoapEngine->customer_properties)) {
            $this->customer_properties = $this->SoapEngine->customer_properties;
        } else {
            $this->customer_properties = array();
        }

        $this->allProperties=array_merge($this->propertiesItems,$this->customer_properties);

    }

    function showSeachForm() {
         printf ("<p><b>%s</b>",
        $this->SoapEngine->ports[$this->SoapEngine->port]['description'],
        '%'
        );

         printf ("<form class=form-inline method=post name=engines action=%s>",$_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";
        print "
        ";
        print "
        <button class='btn btn-primary' type=submit name=action value=Search>Search</button>";

        $this->showEngineSelection();

        print "
        <div class='pull-right'>
        ";
        $this->showSortForm();

        print "
        </div><div style='clear:both' /><br/><div class=input-prepend><span class=\"add-on\">Id</span>";


        $this->showCustomerSelection();
        $this->showResellerSelection();
        print "</div>
        ";

        $this->showSeachFormCustom();

        $this->printHiddenFormElements('skipServiceElement');
        print "</div>
            </div>
        </form>
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
            $this->log_action('getResellers');
            $result     = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $this->log_action('getCustomers');
            $result     = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {

            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <div class='alert alert-success'><center>$this->rows records found. Click on the id to edit the account.</center></div>
            ";

            print "
                <div class=\"btn-group\">
            ";

            $_add_url = $this->url.sprintf("&service=%s&showAddForm=1",
            urlencode($this->SoapEngine->service)
            );

            printf ("<a class='btn btn-warning' href=%s>Add new account</a> ",$_add_url);


            if ($this->adminonly) {
                if ($this->adminonly && $this->filters['reseller']) {
                	$_add_url = $this->url.sprintf("&service=%s&showAddForm=1&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($this->filters['reseller'])
                    );
                    printf (" <a class='btn btn-warning' href=%s>Add a new account for reseller %s</a>",$_add_url,$this->filters['reseller']);
                }
            }
            print "</div>";
            if ($this->rows > 1) {

                print "
                <table class='table table-striped table-condensed' border=0 cellpadding=2 width=100%>
                <thead>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Impersonate</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Country</th>
                    <th>E-mail</th>
                    <th>Phone number</th>
                    <th>Change date</th>
                    <th>Actions</th>
                </tr>
                </thead>
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
                    <tr>
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
                    <td><a class='btn-small btn-danger' href=%s>%s</a>
                    ",
                    $index,
                    $_customer_url,
                    $customer->id,
                    $customer->reseller,
                    $customer->impersonate,
                    strip_tags($customer->username),
                    strip_tags($customer->firstName),
                    strip_tags($customer->lastName),
                    strip_tags($customer->organization),
                    strip_tags($customer->country),
                    strip_tags($customer->email),
                    strip_tags($customer->email),
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
        printf (" <div class=input-prepend><span class=\"add-on\">Username</span><input class='span1' type=text name=username_filter value='%s'></div>",$this->filters['username']);
        printf (" <div class=input-prepend><span class=\"add-on\">FN</span><input class='span2' type=text name=firstName_filter value='%s'></div>\n",$this->filters['firstName']);
        printf (" <div class=input-prepend><span class=\"add-on\">LN</span><input class='span2' type=text name=lastName_filter value='%s'></div>\n",$this->filters['lastName']);
        printf (" <div class=input-prepend><span class=\"add-on\">Organization</span><input class='span2' type=text name=organization_filter value='%s'></div>\n",$this->filters['organization']);
        printf (" <div class=input-prepend><span class=\"add-on\">Email</span><input class='span2' type=text name=email_filter value='%s'></div>\n",$this->filters['email']);

        if ($this->adminonly) {
            if ($this->filters['only_resellers']) $check_only_resellers_filter='checked';
            printf (" Resellers <input class=checkbox type=checkbox name=only_resellers_filter value=1 %s>",$check_only_resellers_filter);
        }
    }

    function deleteRecord($dictionary= Array()) {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['customer']) {
            $customer=$dictionary['customer'];
        } else {
            $customer=$this->filters['customer'];
        }

        if (!strlen($customer)) {
            print "<p><font color=red>Error: missing customer id. </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteAccount',
                                            'parameters' => array(intval($customer)),
                                            'logs'       => array('success' => sprintf('Customer id %s has been deleted',$this->filters['customer'])))
                        );
        if ($this->SoapEngine->execute($function,$this->html)) {
            unset($this->filters);
            return true;

        } else {
            return false;
        }

    }

    function getRecord($id) {

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccount');
        $result     = $this->SoapEngine->soapclient->getAccount(intval($id));

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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
            print "<input class='btn' type=submit name=action value=Update>";
            printf (" E-mail <input class=checkbox type=checkbox name=notify value='1'> account information");
        }

        print "</td>
        <td align=right>";

        printf ("<input type=hidden name=customer_filter value=%s>",$customer->id);

        if ($this->adminonly) {
            printf ("<input type=hidden name=reseller_filter value=%s>",$customer->reseller);
        }

          if ($this->adminonly || $this->reseller == $customer->reseller) {
            if ($_REQUEST['action'] != 'Delete') {
                print "<div class='btn-group'><input class='btn' type=submit name=action value=Copy>";
            }

            print "<input class='btn btn-danger'type=submit name=action value=Delete></div>";

            if ($_REQUEST['action'] == 'Delete' || $_REQUEST['action'] == 'Copy') {
                print "<input class='btn btn-warning' type=hidden name=confirm value=1>";
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
        <td class=border>Property</td>
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
                        <td class=border><input name=%s_form size=30 type=text value='%s' %s></td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item,
                        $this->Fields[$item]['extra_html']
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
            <td class=border>Level</td>
            <td class=border>Property</td>
            <td class=border>Value</td>
            <td class=border>Description</td>
            </tr>");
        } else if ($this->login_credentials['login_type'] == 'reseller') {
            printf ("<tr bgcolor=lightgrey>
            <td class=border>Level</td>
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
                <td class=border><input type=text size=45 name='%s_form' value='%s' autocomplete='no'></td>
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
                            <td class=border>%s</td>
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
                        if ($_key == 'yubikey' && $_REQUEST[$var_name] != '') {
                            $customer->properties[$_key]->value = substr($customer->properties[$_key]->value,0,12);
                        }
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
                    if ($item == 'yubikey' ) {
                        $var_value = substr($var_value,0,12);
                    }
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
                $customer->$item = strip_tags(trim($_REQUEST[$var_name]));
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

        //dprint_r($customer);

        if ($this->SoapEngine->execute($function,$this->html,$this->adminonly)) {
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
        $this->log_action('getCustomers');

        // Call function
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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

        print "<div class='row-fluid'>
        <h1 class=page-header>Add new account</h1>";
        print "<p>";
        print _("Accounts are used for login and to assign ownership to data created in the platform. ");
        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        print "
        <p>
        <input type=hidden name=showAddForm value=1>
        ";

        if ($this->adminonly && $this->filters['reseller']) {
            printf ("<tr><td class=border>Reseller</td>
            <td class=border>%s</td></tr>",$this->filters['reseller']);

            printf ("<input type=hidden name=reseller_filter value='%s'>",$this->filters['reseller']);

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
                if (!$_value) {
                    if ($this->SoapEngine->default_timezone) {
                        $_value=$this->SoapEngine->default_timezone;
                    } else {
                        $_value='Europe/Amsterdam';
                    }
                }

                printf ("
                  <div class=\"control-group\">
                    <label class=\"control-label\">%s",
                $item_name
                );
                print "</label>
                  <div class=\"controls\">";

                $this->showTimezones($_value);

                print "</div>
                </div>
                ";
            } else if ($item=='state') {
                printf ("
                  <div class=\"control-group\">
                    <label class=\"control-label\">
                    %s
                  </label>
                  <div class=\"controls\">",
                $item_name
                );
                print "
                <select name=state_form>";

                $selected_state[$_REQUEST[$item_form]]='selected';

                foreach ($this->states as $_state) {
                    printf ("<option value='%s' %s>%s",$_state['value'],$selected_state[$_state['value']],$_state['label']);
                }

                print "
                </select>
                </div>
                </div>
                ";
            } else if ($item=='country') {
                printf ("<div class=\"control-group\">
                    <label class=\"control-label\">
                  %s
                  </label>
                  <div class=\"controls\">",
                $item_name
                );
                print "
                <select name=country_form>";

		        if (!$_REQUEST[$item_form]) {
                    if ($this->SoapEngine->default_country) {
                        $_value=$this->SoapEngine->default_country;
                    } else {
                        $_value='NL';
                    }
                } else {
                    $_value=$_REQUEST[$item_form];
                }

                $selected_country[$_value]='selected';

                foreach ($this->countries as $_country) {
                    printf ("<option value='%s' %s>%s",$_country['value'],$selected_country[$_country['value']],$_country['label']);
                }

                print "
                </select>
                </div>
                </div>
                ";

            } else {
                if ($this->addFields[$item]['type'] == 'textarea') {
                    printf ("
                    <div class=\"control-group\">
                    <label class=\"control-label\">
                    %s
                    </label>
                    <div class=\"controls\">
                    <textarea cols=30 name=%s_form rows=4>%s</textarea>
                    </div>
                    </div>
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
                    <div class=\"control-group\">
                    <label class=\"control-label\">
                    %s
                    </label>
                    <div class=\"controls\">
                    <input name=%s_form size=30 type=%s value='%s'>
                    </div>
                    </div>
                    ",
                    $item_name,
                    $item,
                    $type,
                    $_REQUEST[$item_form]
                    );

                    if ($item=='password' && $confirmPassword) {
                        printf ("
                        <div class=\"control-group error\">
                        <label class=\"control-label\">
                        Confirm password
                        </label>
                        <div class=\"controls\">
                        <input name=confirm_password_form size=30 type=password value='%s'>
                        </div>
                        </div>
                        </tr>
                        ",
                        $_REQUEST[confirm_password_form]
                        );
                    }
                }
            }
        }
        if ($_REQUEST['notify']) $checked_notify='checked';
        printf ("
                            <div class=\"control-group\">
                    <label class=\"control-label\">Email notification</label>
                  <div class=\"controls\">
          <input class=checkbox type=checkbox name=notify value='1' %s></div></div>",$checked_notify);

        $this->printHiddenFormElements();
        print "<tr><td colspan=2><div class=form-actions><input class='btn' type=submit name=action value=Add></div></td></tr></form>";
        print "
        </div>
        ";
    }

    function addRecord($dictionary=array(),$confirmPassword=false) {

        if (!$this->checkRecord($dictionary)) {
            return false;
        }

        foreach (array_keys($this->addFields) as $item) {

        if ($dictionary[$item]) {
                $customer[$item] = strip_tags(trim($dictionary[$item]));
            } else {
                $item_form       = $item.'_form';
                $customer[$item] = strip_tags(trim($_REQUEST[$item_form]));
            }
        }

        if (!strlen($customer['username'])) $customer['username'] = trim($customer['firstName']).'.'.trim($customer['lastName'].$this->RandomNumber(5));
        if (!strlen($customer['state']))    $customer['state']    = 'N/A';
        if (!strlen($customer['country']))  $customer['country']  = 'N/A';
        if (!strlen($customer['city']))     $customer['city']     = 'Unknown';
        if (!strlen($customer['address']))  $customer['address']  = 'Unknown';
        if (!strlen($customer['postcode'])) $customer['postcode'] = 'Unknown';
        if (!strlen($customer['timezone'])) $customer['timezone'] = 'Europe/Amsterdam';

        if ($dictionary['reseller']) {
            $customer['reseller']=intval($dictionary['reseller']);
        } else if ($this->adminonly && $this->filters['reseller']) {
            $customer['reseller']=intval($this->filters['reseller']);
        }

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

        if ($customer['address'] != 'Unknown') {
            $customer['billingAddress'] = $customer['address']."\n".
                                          $customer['postcode']." ".$customer['city']."\n".
                                          $_state.$customer['country']."\n";
        }

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

        if ($this->hide_html) {
           $logs = array();
        } else {
           $logs = array('success' => sprintf('Customer entry %s %s has been created',$customer['firstName'],$customer['lastName']));
        }

        $function=array('commit'   => array('name'       => 'addAccount',
                                            'parameters' => array($customer),
                                            'logs'       => $logs
                                            )
                       );

        if ($result = $this->SoapEngine->execute($function,$this->html)) {

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
            $this->log_action('getResellers');
            $result     = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $this->log_action('getCustomers');
            $result     = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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
        $this->log_action('getCustomers');
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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
        $this->log_action('getCustomers');
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
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
    function __construct($SoapEngine) {
        $this->SoapEngine         = $SoapEngine;
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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
    var $default_ip_access_list = '';
    var $default_call_limit = '';

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
                $this->sipRecords    = new $_sip_class($this->SipSoapEngine);

                $this->sipRecords->getAllowedDomains();
                print_r($this->record_generators[$generatorId]['sip_engine']);
                if ($this->soapEngines[$this->record_generators[$generatorId]['sip_engine']]['ip_access_list']){
                    $this->default_ip_access_list = $this->soapEngines[$this->record_generators[$generatorId]['sip_engine']]['ip_access_list'];
                }
                if ($this->soapEngines[$this->record_generators[$generatorId]['sip_engine']]['call_limit']){
                    $this->default_call_limit = $this->soapEngines[$this->record_generators[$generatorId]['sip_engine']]['call_limit'];
                }
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
                $this->enumRecords    = new $_enum_class($this->EnumSoapEngine);
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
                $this->customerRecords    = new $_customer_class($this->CustomerSoapEngine);
            }
        } else {
            printf ("<font color=red>Error: customer_engine %s does not exist</font>",$this->record_generators[$generatorId]['customer_engine']);
        }

        if ($_REQUEST['reseller_filter']) $this->template['reseller']=intval($_REQUEST['reseller_filter']);
        if ($_REQUEST['customer_filter']) $this->template['customer']=intval($_REQUEST['customer_filter']);
    }

    function showGeneratorForm() {

        print "
        <form method=post>
        <table cellspacing=1 cellpadding=1 bgcolor=black>
        <tr>
        <td>
        <table cellspacing=3 cellpadding=4 width=100% bgcolor=#444444>
        <tr>
        <td>
        <font color=white>
        <b>";
        print _("ENUM number generator");
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
            <td align=right><input class=checkbox type=checkbox name=create_sip value=1 %s>
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
            print _("PSTN access");
            printf ("
            <td align=right><input class=checkbox type=checkbox name=pstn value=1 %s>
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

            if (isset($_REQUEST['call_limit'])) {
                $call_limit=$_REQUEST['call_limit'];
            } else {
                $call_limit = $this->sipRecords->getCustomerProperty('enum_generator_call_limit');
            }

            if (!strlen($call_limit) && strlen($this->default_call_limit)) {
                $call_limit = $this->default_call_limit;
            }

            print "
            <tr>
            <td>";
            print _("PSTN call limit");
            printf ("
            <td align=right><input type=text size=10 name=call_limit value='%s'>
            </td>
            </tr>
            ",$call_limit);

            if (isset($_REQUEST['ip_access_list'])) {
                $ip_access_list=$_REQUEST['ip_access_list'];
            } else {
                $ip_access_list = $this->sipRecords->getCustomerProperty('enum_generator_ip_access_list');
            }

            if (!$ip_access_list && $this->default_ip_access_list) {
                $ip_access_list = $this->default_ip_access_list;
            }
            print "
            <tr>
            <td>";
            print _("IP access list");
            printf ("
            <td align=right><input type=text size=40 name=ip_access_list value='%s'>
            </td>
            </tr>
            ",$ip_access_list);

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

        $ip_access_list = preg_replace("/\s+/"," ", $_REQUEST['ip_access_list']);
        if (strlen($ip_access_list) and !check_ip_access_list(trim($ip_access_list), true)) {
            printf ("<font color=red>Error: IP access lists must be a space separated list of IP network/mask, example: 10.0.20.40/24</font>");
            return false;
        }

        $this->template['ip_access_list'] = trim($ip_access_list);

        if (strlen($_REQUEST['call_limit']) && !is_numeric($_REQUEST['call_limit'])) {
            printf ("<font color=red>Error: PSTN call limit must be numeric</font>");
            return false;
        }

        $this->template['call_limit']=$_REQUEST['call_limit'];

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
                       ),
                  array('name'       => 'enum_generator_call_limit',
                        'category'   => 'web',
                        'value'      => strval($this->template['call_limit']),
                        'permission' => 'customer'
                       ),
                  array('name'       => 'enum_generator_ip_access_list',
                        'category'   => 'web',
                        'value'      => strval($this->template['ip_access_list']),
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

                printf ('and sip account %s@%s ',$username,$this->template['domain']);

                $ip_access_list = check_ip_access_list($this->template['ip_access_list']);

                $sipAccount = array('account'        => $username.'@'.$this->template['domain'],
                                    'quota'          => $this->template['quota'],
                                    'prepaid'        => $this->template['prepaid'],
                                    'password'       => $this->template['password'],
                                    'groups'         => $groups,
                                    'owner'          => $this->template['owner'],
                                    'pstn'           => $this->template['pstn'],
                                    'ip_access_list' => $ip_access_list,
                                    'call_limit'     => $this->template['call_limit']
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
    var $html = true;

    function __construct($SoapEngine, $login_credentials)
    {
        $this->SoapEngine = $SoapEngine;
        $this->login_credentials = $login_credentials;
        $this->version    = $this->SoapEngine->version;
        $this->adminonly  = $this->SoapEngine->adminonly;
    }

    function log_action($action='Unknown') {
       global $CDRTool;
       $location = "Unknown";
       $_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR']);
       if ($_loc['country_name']) {
           $location = $_loc['country_name'];
       }
       $log = sprintf("CDRTool login username=%s, type=%s, impersonate=%s, IP=%s, location=%s, action=%s:%s, script=%s",
       $this->login_credentials['username'], 
       $this->login_credentials['login_type'], 
       $CDRTool['impersonate'], 
       $_SERVER['REMOTE_ADDR'],
       $location,
       $this->SoapEngine->port,
       $action,
       $_SERVER['PHP_SELF']
       );
       syslog(LOG_NOTICE, $log);
    }

    function checkLogSoapError($result, $syslog = false)
    {
        if (!(new PEAR)->isError($result)) {
            return false;
        }
        $error_msg  = $result->getMessage();
        $error_fault= $result->getFault();
        $error_code = $result->getCode();
        if ($syslog) {
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            syslog(LOG_NOTICE, $log);
        } else {
            printf(
                "<font color=red>Error: %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
        }
        return true;
    }

    function execute($selectionKeys, $action, $sub_action_parameter) {
    }

    function showActionsForm($filters,$sorting,$hideParameter=false) {
        if (!count($this->actions)) return;

        printf ("<form class=form-inline method=post name=actionform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";

        print "
        <input class='btn btn-warning' type=submit value='Perform this action on the selection:'>
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
            <input type=text class=span2 size=%d name=sub_action_parameter>
            ",$this->sub_action_parameter_size);
        }
        print "
        <p class=pull-right>
        ";
        print " Maximum of 500 records
        </p>
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
            print "</div>
            </form>
        ";

    }
}

require_once 'NGNPro/Actions/SipAccounts.php';
require_once 'NGNPro/Actions/SipAliases.php';
require_once 'NGNPro/Actions/EnumMappings.php';
require_once 'NGNPro/Actions/DnsRecords.php';
require_once 'NGNPro/Actions/DnsZones.php';
require_once 'NGNPro/Actions/Customers.php';

function check_ip_access_list($acl_string, $check=false) {
    $list=explode(" ",$acl_string);
    $ip_access_list = array();

    foreach ($list as $el) {
        $els = explode("/",$el);
        if (count($els) != 2) {
            if ($check) {
                return false;
            } else {
                continue;
            }
        }
        list($ip,$mask) = $els;
        if ($mask <0 or $mask > 32) {
            if ($check) {
                return false;
            } else {
                continue;
            }
        }
        if (!preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/",$ip)) {
            if ($check) {
                return false;
            } else {
                continue;
            }
        }
        $ip_access_list[]=array('ip'=>$ip, 'mask'=>intval($mask));
    }

    if ($check) {
        return true;
    } else {
        return $ip_access_list;
    }
}

function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}

?>
