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

            if ((isset($this->allowedPorts[$this->soapEngine]) ? count($this->allowedPorts[$this->soapEngine]) : 0) > 0) {
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

                $this->soapclientVoicemail->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclientVoicemail->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

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

require_once 'NGNPro/SoapEngine/RecordGenerator.php';

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
        global $logger;
        if ($logger->getName() == 'Enrollment') {
            $log = sprintf(
                "reseller=%s, engine=%s, location=%s, action=%s, script=%s",
                $this->login_credentials['reseller'],
                $this->login_credentials['sip_engine'],
                $location,
                $action,
                $_SERVER['PHP_SELF']
            );
        } else {
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
        }
        logger($log);
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
            logger($log);
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

    public function showEngineSelection()
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
                if ((isset($this->SoapEngine->allowedPorts[$_engine]) ? count($this->SoapEngine->allowedPorts[$_engine]) : 0 )> 0 && !in_array($_port, $this->SoapEngine->allowedPorts[$_engine])) continue;

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
        if (empty($this->sortElements)) {
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
        $result = $this->SoapEngine->soapclientCustomers->getCustomers($Query);

        if ($this->checkLogSoapError($result, true)) {
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
        $result = $this->SoapEngine->soapclientCustomers->getResellers($Query);

        if ($this->checkLogSoapError($result, true)) {
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

        if ($this->checkLogSoapError($result, true)) {
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

            if ($this->checkLogSoapError($result, true)) {
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
                printf("<select class=span2 name=%s>", $name);
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
        $j = 0;
        foreach (array_keys($this->filters) as $filter) {
            if (strlen(trim($this->filters[$filter]))) {
                if ($j) $url .='&';
                $url .= sprintf('%s_filter=%s', $filter,urlencode(trim($this->filters[$filter])));
            }
            $j++;
        }

        return $url;
    }

    function printFiltersToForm()
    {
        foreach (array_keys($this->filters) as $filter) {
            if (strlen(trim($this->filters[$filter]))) {
                printf("<input type=hidden name=%s_filter value='%s'>", $filter,trim($this->filters[$filter]));
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
        if (!empty($this->carriers)) {
            return true;
        }

        $Query=array('filter'  => array('name'=>''),
                     'orderBy' => array('attribute' => 'name',
                                        'direction' => 'ASC'
                                  ),
                     'range'   => array('start' => 0,
                                        'count' => 1000)
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getCarriers');
        $result = $this->SoapEngine->soapclient->getCarriers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->carriers as $_carrier) {
                $this->carriers[$_carrier->id]=$_carrier->name;
            }
        }
    }

    function getGateways()
    {
        if (!empty($this->gateways)) {
            return true;
        }

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

        if ($this->checkLogSoapError($result, true)) {
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

        $log=sprintf("getCustomerProperties(%s,engine=%s)", $customer, $this->SoapEngine->customer_engine);
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

        if ($this->checkLogSoapError($result, true)) {
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

        $log=sprintf("setCustomerProperties(%s,engine=%s)", $customer, $this->SoapEngine->customer_engine);
        dprint($log);

        if (!$this->SoapEngine->customer_engine) {
            dprint ("No customer_engine available");
            return true;
        }

        if (!is_array($properties) || !$customer) return true;

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $this->log_action('setProperties');
        $result = $this->SoapEngine->soapclientCustomers->setProperties(intval($customer), $properties);

        if ($this->checkLogSoapError($result, true)) {
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
        printf ("%s", $this->SoapEngine->welcome_message);
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


require_once 'NGNPro/Records/SipDomains.php';
require_once 'NGNPro/Records/SipAccounts.php';
require_once 'NGNPro/Records/SipAliases.php';
require_once 'NGNPro/Records/EnumRanges.php';
require_once 'NGNPro/Records/EnumMappings.php';
require_once 'NGNPro/Records/DnsZones.php';
require_once 'NGNPro/Records/DnsRecords.php';
require_once 'NGNPro/Records/DnsRecords/FancyRecords.php';
require_once 'NGNPro/Records/DnsRecords/FancyRecords/EmailAliases.php';
require_once 'NGNPro/Records/DnsRecords/FancyRecords/UrlRedirect.php';

require_once 'NGNPro/Records/TrustedPeers.php';
require_once 'NGNPro/Records/Carriers.php';
require_once 'NGNPro/Records/Gateways.php';
require_once 'NGNPro/Records/GatewayRules.php';
require_once 'NGNPro/Records/Routes.php';
require_once 'NGNPro/Records/Customers.php';

require_once 'NGNPro/Presence.php';

require_once 'NGNPro/Actions.php';
require_once 'NGNPro/Actions/SipAccounts.php';
require_once 'NGNPro/Actions/SipAliases.php';
require_once 'NGNPro/Actions/EnumMappings.php';
require_once 'NGNPro/Actions/DnsRecords.php';
require_once 'NGNPro/Actions/DnsZones.php';
require_once 'NGNPro/Actions/Customers.php';

function check_ip_access_list($acl_string, $check = false)
{
    $list = explode(" ", $acl_string);
    $ip_access_list = array();

    foreach ($list as $el) {
        $els = explode("/", $el);
        if (count($els) != 2) {
            if ($check) {
                return false;
            } else {
                continue;
            }
        }
        list($ip, $mask) = $els;
        if ($mask <0 or $mask > 32) {
            if ($check) {
                return false;
            } else {
                continue;
            }
        }
        if (!preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $ip)) {
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

function objectToArray($d)
{
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
