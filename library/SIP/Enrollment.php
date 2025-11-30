<?php

class Enrollment
{
    protected $init                 = false;
    public $create_voicemail           = false;
    public $send_email_notification    = true;
    public $create_email_alias         = false;
    public $create_customer            = true;
    public $timezones                  = array();
    public $default_timezone           = 'Europe/Amsterdam';
    public $configuration_file         = '/etc/cdrtool/enrollment/config.ini';
    public $allow_pstn                 = 1;
    public $quota                      = 50;
    public $prepaid                    = 1;
    public $create_certificate         = 0;
    public $customer_belongs_to_reseller = false;

    protected $soapEngines;
    protected $enrollment;
    protected $sipDomain;
    protected $sipEngine;
    protected $customerEngine;
    protected $CustomerSoapEngine;
    protected $SipSoapEngine;
    protected $EmailSoapEngine;

    protected $customerRecords;
    protected $sipRecords;
    protected $emailRecords;

    protected $reseller;
    protected $outbound_proxy;
    protected $xcap_root;
    protected $msrp_relay;
    protected $settings_url;
    protected $ldap_hostname;
    protected $ldap_dn;
    protected $conference_server;
    protected $default_email;
    protected $sipClass;
    protected $groups;
    protected $emailEngine;
    protected $customerLoginCredentials;
    protected $sipLoginCredentials;

    protected $crt;
    protected $crt_out;
    protected $csr;
    protected $csr_out;
    protected $key;
    protected $key_out;
    protected $pk12_out;

    public function __construct()
    {
        require $this->configuration_file;
        require "/etc/cdrtool/ngnpro_engines.inc";
        changeloggerchannel('Enrollment');

        $this->soapEngines  = $soapEngines;
        $this->enrollment   = $enrollment;

        $this->loadTimezones();

        if (!is_array($this->soapEngines)) {
            $return = array(
                'success'       => false,
                'error_message' => 'Error: Missing soap engines configuration'
            );
            print (json_encode($return));
            return false;
        }

        if (!is_array($this->enrollment)) {
            $return = array(
                'success'       => false,
                'error_message' => 'Error: Missing enrollment configuration'
            );
            print (json_encode($return));
            return false;
        }

        $this->sipDomain      = $this->enrollment['sip_domain'];
        $this->sipEngine      = $this->enrollment['sip_engine'];

        if ($this->enrollment['timezone']) {
            $this->default_timezone = $this->enrollment['timezone'];
        }

        if ($this->enrollment['customer_engine']) {
            $this->customerEngine = $this->enrollment['customer_engine'];
        } else {
            $this->customerEngine = $this->enrollment['sip_engine'];
        }

        if ($this->enrollment['email_engine']) {
            $this->emailEngine = $this->enrollment['email_engine'];
        } else {
            $this->emailEngine = $this->enrollment['sip_engine'];
        }

        if (is_array($this->enrollment['groups'])) {
            $this->groups = $this->enrollment['groups'];
        } else {
            $this->groups = array();
        }

        $this->reseller          = $this->enrollment['reseller'];
        $this->outbound_proxy    = $this->enrollment['outbound_proxy'];
        $this->xcap_root         = $this->enrollment['xcap_root'];
        $this->msrp_relay        = $this->enrollment['msrp_relay'];
        $this->settings_url      = $this->enrollment['settings_url'];
        $this->ldap_hostname     = $this->enrollment['ldap_hostname'];
        $this->ldap_dn           = $this->enrollment['ldap_dn'];
        $this->conference_server = $this->enrollment['conference_server'];
        $this->default_email     = $this->enrollment['default_email'];

        if ($this->enrollment['sip_class']) {
            $this->sipClass = $this->enrollment['sip_class'];
        } else {
            $this->sipClass = 'SipSettings';
        }

        if (!$this->sipEngine) {
            $return = array(
                'success'       => false,
                'error_message' => 'Missing sip engine'
            );
            print (json_encode($return));
            return false;
        }

        if (!$this->sipDomain) {
            $return = array(
                'success'       => false,
                'error_message' => 'Missing sip domain'
            );
            print (json_encode($return));
            return false;
        }

        $this->sipLoginCredentials = array(
            'reseller'       => intval($this->reseller),
            'sip_engine'     => $this->sipEngine,
            'login_type'     => 'admin'
        );

        $this->init=true;
    }

    public function createAccount()
    {
        if (!$this->init) return false;


        if (!$_REQUEST['email']) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'Missing email address'
                          );
            print (json_encode($return));
            return false;
        }

        if (!$this->checkEmail($_REQUEST['email'])) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'Invalid email address'
                          );
            print (json_encode($return));
            return false;
        }

        if (!$_REQUEST['password']) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'Missing password'
                          );
            print (json_encode($return));
            return false;
        }

        if (!$_REQUEST['display_name']) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'Missing display name'
                          );
            print (json_encode($return));
            return false;
        }

        $username = strtolower(trim($_REQUEST['username']));

        if (!preg_match("/^[1-9a-z][0-9a-z_.-]{2,64}[0-9a-z]$/", $username)) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'The username must contain at least 4 lowercase alpha-numeric . _ or - characters and must start and end with a positive digit or letter'
                          );
            print (json_encode($return));
            return false;
        }

        $sip_address = $username.'@'.$this->sipDomain;

        if ($this->create_customer && !$_REQUEST['owner']) {
            // create owner id
            $customerEngine           = 'customers@'.$this->customerEngine;
            $this->CustomerSoapEngine = new SoapEngine($customerEngine, $this->soapEngines, $this->customerLoginCredentials);
            $_customer_class          = $this->CustomerSoapEngine->records_class;
            $this->customerRecords    = new $_customer_class($this->CustomerSoapEngine);
            $this->customerRecords->html=false;

            $properties = $this->customerRecords->setInitialCredits(
                array(
                    'sip_credit'         => 1,
                    'sip_alias_credit'   => 1,
                    'email_credit'       => 1
                )
            );
            if (preg_match("/^(\w+)\s+(\w+)$/", $_REQUEST['display_name'], $m)) {
                $firstName = $m[1];
                $lastName  = $m[2];
            } else {
                $firstName = $_REQUEST['display_name'];
                $lastName  = 'Blink';
            }

            $this->log_action("Create owner account ($firstName $lastName)");

            $timezone = $_REQUEST['tzinfo'];

            if (!in_array($timezone, $this->timezones)) {
                $timezone = $this->default_timezone;
            }

            $location = lookupGeoLocation($_SERVER['REMOTE_ADDR']);

            $customer = array(
                'firstName'  => $firstName,
                'lastName'   => $lastName,
                'timezone'   => $timezone,
                'password'   => trim($_REQUEST['password']),
                'email'      => trim($_REQUEST['email']),
                'country'    => $location['country_code'],
                'state'      => utf8_encode($location['region']),
                'city'       => utf8_encode($location['city']),
                'properties' => $properties
            );

            if ($this->customer_belongs_to_reseller) {
                $customer['reseller'] =intval($this->reseller);
            }

            // Normalize and validate phone number if provided
            if (!empty($_REQUEST['phoneNumber'])) {

                $raw = trim($_REQUEST['phoneNumber']);

                // keep only digits, +, -
                $filtered = preg_replace('/[^0-9+\-]/', '', $raw);

                // Convert leading 00 → +
                if (strpos($filtered, '00') === 0) {
                    $filtered = '+' . substr($filtered, 2);
                }

                // Remove dashes
                $filtered = str_replace('-', '', $filtered);

                // If it doesn’t start with + now, reject it
                if ($filtered !== '' && $filtered[0] !== '+') {
                    $filtered = '';  // invalid
                }

                // enforce max length 15
                if (strlen($filtered) > 15) {
                    $filtered = '';
                }

                // If valid, assign it
                if (!empty($filtered)) {
                    $customer['tel'] = $filtered;
                }
            }

            if (empty($customer['tel'])) {
                if ($location['country_code'] == 'NL') {
                    $customer['tel'] = '+31999999999';
                } elseif ($location['country_code'] == 'US') {
                    $customer['tel'] = sprintf("+1%s9999999", $location['area_code']);
                } else {
                    $customer['tel'] = '+19999999999';
                }
            }

            $_customer_created=false;

            $j=0;

            while ($j < 3) {
                $username .= RandomString(4);

                $customer['username'] = $username;

                if (!$result = $this->customerRecords->addRecord($customer)) {
                    if ($this->customerRecords->SoapEngine->exception->errorcode != "5001") {
                        $return = array(
                            'success'       => false,
                            'error'         => 'internal_error',
                            'error_message' => 'failed to create owner: ' . (string) $this->customerRecords->SoapEngine->exception->errorcode
                        );
                        print (json_encode($return));
                        return false;
                    }
                } else {
                    $_customer_created=true;
                    break;
                }

                $j++;
            }

            if (!$_customer_created) {
                if ($this->sipRecords->soap_error_description) {
                    $_msg=$this->sipRecords->soap_error_description;
                } else {
                    $_msg='failed to create customer account';
                }

                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => $_msg
                              );
                print (json_encode($return));
                return false;
            } else {
                $this->log_action("Owner account created (". $customer['username'].")");
            }

            $owner=$result->id;

            if (!$owner) {
                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => 'failed to obtain a new owner id'
                              );
                print (json_encode($return));
                return false;
            } else {
                $this->log_action("Owner id is $owner (". $customer['username'].")");
            }
        } elseif (is_numeric($_REQUEST['owner']) && $_REQUEST['owner'] != 0 ) {
            $owner=intval($_REQUEST['owner']);
        } else {
            $return = array(
                'success'       => false,
                'error'         => 'internal_error',
                'error_message' => 'no owner information provided'
            );
            print (json_encode($return));
            return false;
        }

        // create SIP Account
        $sipEngine           = 'sip_accounts@'.$this->sipEngine;

        $this->SipSoapEngine = new SoapEngine($sipEngine, $this->soapEngines, $this->sipLoginCredentials);
        $_sip_class          = $this->SipSoapEngine->records_class;
        $this->sipRecords    = new $_sip_class($this->SipSoapEngine);
        $this->sipRecords->html=false;


        $sip_properties[]=array('name'=> 'ip',                 'value' => $_SERVER['REMOTE_ADDR']);
        $sip_properties[]=array('name'=> 'registration_email', 'value' => $_REQUEST['email']);

        $languages = array("en","ro","nl","es","de");

        if (isset($_REQUEST['lang'])) {
            if (in_array($_REQUEST['lang'], $languages)) {
                $sip_properties[]=array('name'=> 'language',           'value' => $_REQUEST['lang']);
            }
        }

        if (strlen($timezone)) {
            $sip_properties[]=array('name'=> 'timezone', 'value' => $timezone);
        }

        if (strlen($user_agent)) {
            $sip_properties[]=array('name'=> 'user_agent', 'value' => trim(urldecode($user_agent)));
        }

        $sipAccount = array('account'   => $sip_address,
                            'fullname'  => $_REQUEST['display_name'],
                            'email'     => $_REQUEST['email'],
                            'password'  => $_REQUEST['password'],
                            'rpid'      => $customer['tel'],
                            'timezone'  => $timezone,
                            'prepaid'   => $this->prepaid,
                            'pstn'      => $this->allow_pstn,
                            'quota'     => $this->quota,
                            'owner'     => intval($owner),
                            'groups'    => $this->groups,
                            'properties'=> $sip_properties
                            );

        $this->log_action("Create SIP account ($sip_address)");

        if (!$result = $this->sipRecords->addRecord($sipAccount)) {
            if ($this->sipRecords->SoapEngine->exception->errorstring) {
                if ($this->sipRecords->SoapEngine->exception->errorcode == 1011) {
                    $return=array('success'       => false,
                                  'error'         => 'user_exists',
                                  'error_message' => $this->sipRecords->SoapEngine->exception->errorstring
                                  );
                } else {
                    $return=array('success'       => false,
                                  'error'         => 'internal_error',
                                  'error_message' => $this->sipRecords->SoapEngine->exception->errorstring
                                  );
                }
            } else {
                $_msg='failed to create sip account';
                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => $_msg
                              );
            }


            print (json_encode($return));

            $_dictionary=array('customer'=>intval($owner),
                               'error'   => 'internal_error',
                               'confirm' => true
                               );

            $this->customerRecords->deleteRecord($_dictionary);

            return false;
        } else {
            $sip_address=$result->id->username.'@'.$result->id->domain;
            $this->log_action("SIP account created ($sip_address)");

            if ($this->create_certificate) {
                if (!$passport = $this->generateCertificate($sip_address, $_REQUEST['email'], $_REQUEST['password'])) {
                    $return=array('success'       => false,
                                  'error'         => 'internal_error',
                                  'error_message' => 'failed to generate certificate'
                                  );
                    print (json_encode($return));
                    return false;
                }
            }

                // Generic code for all sip settings pages

            if ($this->create_voicemail || $this->send_email_notification) {
                if ($SipSettings = new $this->sipClass($sip_address, $this->sipLoginCredentials, $this->soapEngines)) {

                    if ($this->create_voicemail) {
                        // Add voicemail account
                        $this->log_action("Add voicemail account ($sip_address)");
                        $SipSettings->addVoicemail();
                        $SipSettings->setVoicemailDiversions();
                    }
                    if ($this->send_email_notification) {
                        if ($this->default_email) {
                            $SipSettings->support_email = $this->default_email;
                        }
                        // Sent account settings by email
                        $SipSettings->sendEmail('hideHtml');
                    }
                }
            }

            if ($this->create_email_alias) {
                $this->log_action("Add email alias ($sip_address)");
                $emailEngine           = 'email_aliases@'.$this->emailEngine;
                $this->EmailSoapEngine = new SoapEngine($emailEngine, $this->soapEngines, $this->sipLoginCredentials);
                $_email_class          = $this->EmailSoapEngine->records_class;
                $this->emailRecords    = new $_email_class($this->EmailSoapEngine);
                $this->emailRecords->html=false;

                $emailAlias = array('name'    => strtolower($sip_address),
                                    'type'    => 'MBOXFW',
                                    'owner'   => intval($owner),
                                    'value'   => $_REQUEST['email']
                                    );

                $this->emailRecords->addRecord($emailAlias);
            }

            $return=array('success'        => true,
                          'sip_address'    => $sip_address,
                          'email'          => $result->email,
                          'settings_url'   => $this->settings_url,
                          'outbound_proxy' => $this->outbound_proxy
                          );

            if ($this->create_certificate) {
                $return['passport'] = $passport;
            }

            if ($this->ldap_hostname) {
                $return['ldap_hostname']  = $this->ldap_hostname;
            }

            if ($this->ldap_dn) {
                $return['ldap_dn']        = $this->ldap_dn;
            }

            if ($this->msrp_relay) {
                $return['msrp_relay']        = $this->msrp_relay;
            }

            if ($this->xcap_root) {
                $return['xcap_root']        = $this->xcap_root;
            }

            if ($this->conference_server) {
                $return['conference_server']        = $this->conference_server;
            }

            print (json_encode($return));

            return true;
        }
    }

    public function generateCertificate($sip_address, $email, $password)
    {
        if (!$this->init) return false;

        if (!is_array($this->enrollment)) {
            print _("Error: missing enrollment settings");
            return false;
        }

        if (!$this->enrollment['ca_conf']) {
            //print _("Error: missing enrollment ca_conf settings");
            return false;
        }

        if (!$this->enrollment['ca_crt']) {
            //print _("Error: missing enrollment ca_crt settings");
            return false;
        }

        if (!$this->enrollment['ca_key']) {
            //print _("Error: missing enrollment ca_key settings");
            return false;
        }

        $config = array(
            'config'           => $this->enrollment['ca_conf'],
            'digest_alg'       => 'md5',
            'private_key_bits' => 1024,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'encrypt_key'      => false,
        );

        $dn = array(
            "countryName"            => $this->enrollment['countryName'],
            "stateOrProvinceName"    => $this->enrollment['stateOrProvinceName'],
            "localityName"           => $this->enrollment['localityName'],
            "organizationName"       => $this->enrollment['organizationName'],
            "organizationalUnitName" => $this->enrollment['organizationalUnitName'],
            "commonName"             => $sip_address,
            "emailAddress"           => $email
        );

        $this->key = openssl_pkey_new($config);
        $this->csr = openssl_csr_new($dn, $this->key);

        openssl_csr_export($this->csr, $this->csr_out);
        openssl_pkey_export($this->key, $this->key_out, $password, $config);

        $ca="file://".$this->enrollment['ca_crt'];

        $this->crt = openssl_csr_sign($this->csr, $ca, $this->enrollment['ca_key'], 3650, $config);

        if ($this->crt == false) {
            while (($e = openssl_error_string()) !== false) {
                echo $e . "\n";
                print "<br><br>";
            }
            return false;
        }

        openssl_x509_export($this->crt, $this->crt_out);
        openssl_pkcs12_export($this->crt, $this->pk12_out, $this->key, $password);

        return array(
            'crt'  => $this->crt_out,
            'key'  => $this->key_out,
            'pk12' => $this->pk12_out,
            'ca'   => file_get_contents($this->enrollment['ca_crt'])
        );
    }

    public function checkEmail($email)
    {
        dprint("checkEmail($email)");
        $regexp = "/^([a-z0-9][a-z0-9_.-]*)@([a-z0-9][a-z0-9-]*\.)+([a-z]{2,})$/i";
        if (stristr($email, "-.") ||
            !preg_match($regexp, $email)) {
            return false;
        }
        return true;
    }

    public function loadTimezones()
    {
        if (!$fp = fopen("timezones", "r")) {
            syslog(LOG_NOTICE, 'Error: Failed to open timezones file');
            return false;
        }
        while ($buffer = fgets($fp, 1024)) {
            $this->timezones[]=trim($buffer);
        }

        fclose($fp);
    }

    public function log_action($action)
    {
        global $auth;
        $location = "Unknown";
        $_loc = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        if ($_loc['country_name']) {
            $location = $_loc['country_name'];
        }
        $log = sprintf(
            "reseller=%s, engine=%s, location=%s, action=%s, script=%s",
            $this->sipLoginCredentials['reseller'],
            $this->sipLoginCredentials['sip_engine'],
            $location,
            $action,
            $_SERVER['PHP_SELF']
        );
        logger($log);
    }

}
